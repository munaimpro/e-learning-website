<?php

/**
 * Get set Form,fields
 */

namespace BitCode\BitForm\Frontend\Form;

/**
 * FrontendFormManager class
 */

use BitCode\BitForm\Admin\Form\Helpers;
use BitCode\BitForm\Core\Database\FormEntryModel;
use BitCode\BitForm\Core\Form\FormManager;
use BitCode\BitForm\Core\Form\Validator\FormFieldValidator;
use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Messages\SuccessMessageHandler;
use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\DateTimeHelper;
use BitCode\BitForm\Core\Util\HttpHelper;
use BitCode\BitForm\Core\Util\IpTool;
use BitCode\BitForm\Core\WorkFlow\WorkFlow;
use BitCode\BitForm\Core\WorkFlow\WorkFlowHandler;
use BitCode\BitForm\Frontend\Form\View\FormViewer;
use BitCode\BitFormPro\Admin\FormSettings\FormAbandonment;
use WP_Error;

final class FrontendFormManager extends FormManager
{
  private $_form_identifier;
  private $_form_token;
  private $_form_id;
  private $_work_flows;
  private $_conf_messages;

  // private $_has_upload = false;
  public function __construct($form_id, $shortCodeCounter = null)
  {
    parent::__construct($form_id);
    $this->_form_identifier = 'bitforms_' . $form_id;
    $this->_form_identifier .= !empty(get_post()->ID) ? '_' . get_post()->ID : '';
    $this->_form_identifier .= !empty($shortCodeCounter) ? "_$shortCodeCounter" : '';
    $this->_form_token = wp_create_nonce('bitforms_' . $form_id);
    $this->_form_id = $form_id;
  }

  public function getFormIdentifier()
  {
    return $this->_form_identifier;
  }

  public function getFormID()
  {
    return $this->_form_id;
  }

  public function getFormToken()
  {
    return $this->_form_token;
  }

  public function isSubmitted()
  {
    // return isset($_POST[$this->_form_identifier]) ? true : false;
    return (isset($_POST['bitforms_id']) && $_POST['bitforms_id'] === $this->_form_identifier) ? true : false;
  }

  public function getSubmittedFields($submitted_data)
  {
    unset($submitted_data[$this->_form_identifier]);
    // unset($submitted_data['bit-form-submit-btn']);
    return array_keys($submitted_data);
  }

  public function formView($fields = null, $hasFile = false, $errorMessages = null, $previousValue = null)
  {
    $formContents = $this->getFormContent();
    $formAtomicClsMap = $this->getAtomicClsMap();
    if (!empty($fields)) {
      $formContents->fields = is_string($fields) ? json_decode($fields) : $fields;
    } else {
      $workFlowRunHelper = new WorkFlow($this->form_id);
      $workFlowreturnedOnLoad = $workFlowRunHelper->executeOnLoad(
        'create',
        $formContents->fields
      );
      $formContents->fields = empty($workFlowreturnedOnLoad['fields']) ? $formContents->fields : $workFlowreturnedOnLoad['fields'];
    }
    $formViewer = new FormViewer($this, $formContents, $formAtomicClsMap, $errorMessages, $previousValue);
    $isRestricted = $this->checkSubmissionRestriction(false);
    $msg = !empty($isRestricted) ? $isRestricted[0] : '';
    return $formViewer->getView($hasFile, $msg);
  }

  public function conversationalFormView($fields = null, $hasFile = false, $errorMessages = null, $previousValue = null)
  {
    $formContents = $this->getFormContent();
    $formAtomicClsMap = $this->getAtomicClsMap();
    if (!empty($fields)) {
      $formContents->fields = is_string($fields) ? json_decode($fields) : $fields;
    } else {
      $workFlowRunHelper = new WorkFlow($this->form_id);
      $workFlowreturnedOnLoad = $workFlowRunHelper->executeOnLoad(
        'create',
        $formContents->fields
      );
      $formContents->fields = empty($workFlowreturnedOnLoad['fields']) ? $formContents->fields : $workFlowreturnedOnLoad['fields'];
    }
    $formViewer = new FormViewer($this, $formContents, $formAtomicClsMap, $errorMessages, $previousValue);
    $isRestricted = $this->checkSubmissionRestriction(false);
    $msg = !empty($isRestricted) ? $isRestricted[0] : '';
    return $formViewer->getConversationalView($hasFile, $msg);
  }

  public function checkEmptySubmission($data, $file)
  {
    $formFields = $this->getFields();
    foreach ($formFields as $key => $field) {
      $fieldType = $field['type'];
      if ('button' === $fieldType) {
        continue;
      }
      $fileUploadFieldTypes = ['file-up', 'advanced-file-up'];
      if ('decision-box' === $fieldType || 'gdpr' === $fieldType) {
        continue;
      }
      $isFileType = in_array($fieldType, $fileUploadFieldTypes);
      if ($this->isRepeatedField($key)) {
        $fileData = !empty($file[$key]) ? $file[$key] : [];
        $dataVal = !empty($data[$key]) ? $data[$key] : [];
        if (!$this->checkRepeatedFieldEmptySubmission($isFileType, $dataVal, $fileData)) {
          return false;
        }
        continue;
      }
      if (!$isFileType && (!empty($data[$key]) || (isset($data[$key]) && is_numeric($data[$key])))) {
        return false;
      }
      if ($isFileType && !empty($file[$key]['name']) && is_string($file[$key]['name'])) {
        return false;
      }
      if ($isFileType && !empty($file[$key]['name'][0])) {
        return false;
      }
    }
    return true;
  }

  private function checkRepeatedFieldEmptySubmission($isFileType, $data, $file = [])
  {
    if (!$isFileType) {
      foreach ($data as $value) {
        if (!empty($value)) {
          return false;
        }
      }
    }
    if ($isFileType) {
      foreach ($file['name'] as $value) {
        if (!empty($value) && is_string($value)) {
          return false;
        }
        if (is_array($value) && !empty($value[0])) {
          return false;
        }
      }
    }
    return true;
  }

  private function getParams()
  {
    $url = wp_parse_url(wp_get_referer());
    $parameter = [];
    if (isset($url['query'])) {
      $queries = explode('&', $url['query']);
      foreach ($queries as $query) {
        list($field, $value) = explode('=', $query);
        $parameter[$field] = $value;
      }
    }
    return $parameter;
  }

  public function handleSubmission()
  {
    $this->fieldNameReplaceOfPost();

    $validated = $this->beforeSubmittedValidate();
    $validated = apply_filters('bitform_filter_form_validation', $validated, $this->_form_id);

    if (true === $validated) {
      do_action('bitform_validation_success', $this->_form_id);
      unset($_POST['hidden_fields']);

      $redirectPage = '';
      $regSuccMsg = '';

      $existAuth = (new IntegrationHandler($this->_form_id))->getAllIntegration('wp_user_auth', 'wp_auth', 1);
      if (!is_wp_error($existAuth) && count($existAuth) > 0) {
        $parameter = $this->getParams();
        $existAuthFilter = has_filter('bf_wp_user_auth');

        if (true === $existAuthFilter) {
          $result = apply_filters('bf_wp_user_auth', $existAuth[0], $_POST, $parameter);

          $result = apply_filters('bitform_filter_wp_user_auth_response', $result, $this->_form_id, $_POST, $parameter);

          do_action('bitform_wp_user_auth_response', $result, $this->_form_id, $_POST, $parameter);

          if (isset($result['auth_type']) && 'register' === $result['auth_type']) {
            if (!$result['success']) {
              return new WP_Error('errors', __($result['message'], 'bit-form'));
            } elseif (isset($result['success'])) {
              $redirectPage = $result['redirectPage'];
              $regSuccMsg = $result['message'];
              $newNonce = wp_create_nonce('bitforms_' . $this->_form_id);
            }
          } else {
            if (!$result['success']) {
              return new WP_Error('errors', __($result['message'], 'bit-form'));
            } else {
              return $result;
            }
          }
        }
      }

      $saveResponse = $this->saveFormEntry($_POST);
      if (is_wp_error($saveResponse)) {
        return $saveResponse;
      }

      $entryID = $saveResponse['entry_id'];
      do_action('bitform_submit_success', $this->_form_id, $entryID, $_POST, $_FILES);

      // check and replace signature field value
      $formFields = $this->getFields();
      // $uploadPath = BITFORMS_UPLOAD_BASE_URL . "/uploads/{$this->_form_id}/{$entryID}"; // web root
      // $uploadPath = BITFORMS_UPLOAD_DIR . "/{$this->_form_id}/{$entryID}"; // server root
      $uploadPath = $this->_form_id . DIRECTORY_SEPARATOR . $entryID;

      foreach ($formFields as $key => $field) {
        if ('signature' === $field['type']) {
          $saveResponse['fields'][$key] = $uploadPath . DIRECTORY_SEPARATOR . $saveResponse['fields'][$key];
          break;
        }
      }

      $captchaV3Settings = $this->getCaptchaV3Settings();
      if ($captchaV3Settings) {
        $token = $_POST['g-recaptcha-response'];
        $integrationHandler = new IntegrationHandler(0);
        $allFormIntegrations = $integrationHandler->getAllIntegration('app', 'gReCaptchaV3');
        if (!is_wp_error($allFormIntegrations)) {
          foreach ($allFormIntegrations as $integration) {
            if (!is_null($integration->integration_type) && 'gReCaptchaV3' === $integration->integration_type) {
              $integrationDetails = json_decode($integration->integration_details);
              $integrationDetails->id = $integration->id;
              $reCAPTCHA = $integrationDetails;
            }
          }
        }
        if (!empty($reCAPTCHA->secretKey)) {
          $gRecaptchaResponse = HttpHelper::post(
            'https://www.google.com/recaptcha/api/siteverify',
            ['secret' => $reCAPTCHA->secretKey, 'response' => $token]
          );
          if ($captchaV3Settings && !empty($saveResponse['triggerData'])) {
            $logID = $saveResponse['triggerData']['logID'];
            $integId = $reCAPTCHA->id;
            $saveApiResponse = new UtilApiResponse();
            $saveApiResponse->apiResponse($logID, $integId, ['type_name' => 'ReCaptcha', 'type' => 'v3'], 'success', $gRecaptchaResponse);
          }
        }
        unset($_POST['g-recaptcha-response']);
      }
      if (!empty($redirectPage) && empty($saveResponse['redirectPage']) || null === $saveResponse['redirectPage']) {
        $saveResponse['redirectPage'] = $redirectPage;
      }
      if (!empty($regSuccMsg) && isset($saveResponse['dflt_message'])) {
        $saveResponse['message'] = $regSuccMsg;
      }

      $saveResponse = IntegrationHandler::maybeSetCronForIntegration($saveResponse, 'create');
      $entryId = $saveResponse['entry_id'];

      $responseMsg = is_array($saveResponse) && !empty($saveResponse) ? $saveResponse : __('Form Submitted Successfully', 'bit-form');
      if (isset($newNonce)) {
        $responseMsg['new_nonce'] = $newNonce;
      }
      $_POST = [];
      $responseMsg['entry_id'] = $entryId;
      return $responseMsg;
    }
    do_action('bitform_validation_error', $this->_form_id, $validated);
    return $validated;
  }

  public function handleUpdateEntry()
  {
    $this->fieldNameReplaceOfPost();
    $validated = $this->beforeSubmittedValidate();
    $validated = apply_filters('bitform_filter_form_validation', $validated, $this->_form_id);

    $entryID = $_REQUEST['entryID'];
    $GLOBALS['bf_entry_id'] = $entryID;
    if (is_null($entryID)) {
      return new WP_Error('empty_form', __('Entries id is invalid', 'bit-form'));
    }
    if (true === $validated) {
      do_action('bitform_validation_success', $this->_form_id);
      unset($_POST['hidden_fields'], $_POST['entryID']);

      $redirectPage = '';
      $regSuccMsg = '';

      $existAuth = (new IntegrationHandler($this->_form_id))->getAllIntegration('wp_user_auth', 'wp_auth', 1);
      if (!is_wp_error($existAuth) && count($existAuth) > 0) {
        $parameter = $this->getParams();
        $existAuthFilter = has_filter('bf_wp_user_auth');

        if (true === $existAuthFilter) {
          $result = apply_filters('bf_wp_user_auth', $existAuth[0], $_POST, $parameter);

          if (isset($result['auth_type']) && 'register' === $result['auth_type']) {
            if (!$result['success']) {
              return new WP_Error('errors', __($result['message'], 'bit-form'));
            } elseif (isset($result['success'])) {
              $redirectPage = $result['redirectPage'];
              $regSuccMsg = $result['message'];
              $newNonce = wp_create_nonce('bitforms_' . $this->_form_id);
            }
          } else {
            if (!$result['success']) {
              return new WP_Error('errors', __($result['message'], 'bit-form'));
            } else {
              return $result;
            }
          }
        }
      }

      $updateResponse = $this->updateFormEntry($_POST, $this->getFormID(), $entryID);
      if (is_wp_error($updateResponse)) {
        return $updateResponse;
      }

      do_action('bitform_update_success', $this->_form_id, $entryID, $_POST);

      $captchaV3Settings = $this->getCaptchaV3Settings();
      if ($captchaV3Settings) {
        $token = $_POST['g-recaptcha-response'];
        $integrationHandler = new IntegrationHandler(0);
        $allFormIntegrations = $integrationHandler->getAllIntegration('app', 'gReCaptchaV3');
        if (!is_wp_error($allFormIntegrations)) {
          foreach ($allFormIntegrations as $integration) {
            if (!is_null($integration->integration_type) && 'gReCaptchaV3' === $integration->integration_type) {
              $integrationDetails = json_decode($integration->integration_details);
              $integrationDetails->id = $integration->id;
              $reCAPTCHA = $integrationDetails;
            }
          }
        }
        if (!empty($reCAPTCHA->secretKey)) {
          $gRecaptchaResponse = HttpHelper::post(
            'https://www.google.com/recaptcha/api/siteverify',
            ['secret' => $reCAPTCHA->secretKey, 'response' => $token]
          );
          if ($captchaV3Settings && !empty($updateResponse['triggerData'])) {
            $logID = $updateResponse['triggerData']['logID'];
            $integId = $reCAPTCHA->id;
            $saveApiResponse = new UtilApiResponse();
            $saveApiResponse->apiResponse($logID, $integId, ['type_name' => 'ReCaptcha', 'type' => 'v3'], 'success', $gRecaptchaResponse);
          }
        }
        unset($_POST['g-recaptcha-response']);
      }
      if (!empty($redirectPage) && empty($updateResponse['redirectPage']) || null === $updateResponse['redirectPage']) {
        $updateResponse['redirectPage'] = $redirectPage;
      }
      if (!empty($regSuccMsg) && isset($updateResponse['dflt_message'])) {
        $updateResponse['message'] = $regSuccMsg;
      }
      $updateResponse = IntegrationHandler::maybeSetCronForIntegration($updateResponse, 'create');
      $entryId = $updateResponse['entry_id'];

      $responseMsg = is_array($updateResponse) && !empty($updateResponse) ? $updateResponse : __('Entry Update Successfully', 'bit-form');
      if (isset($newNonce)) {
        $responseMsg['new_nonce'] = $newNonce;
      }
      $_POST = [];
      $responseMsg['entry_id'] = $entryId;
      return $responseMsg;
    }
    do_action('bitform_validation_error', $this->_form_id, $validated);
    return $validated;
  }

  public function validateFormSubmission($submitted_data)
  {
    $hidden_fields = isset($submitted_data['hidden_fields']) ? $submitted_data['hidden_fields'] : '';
    $submitted_fields = $this->getSubmittedFields($submitted_data);
    $form_fields = $this->getFields();
    $form_fields_names = array_keys($form_fields);
    if ($this->isGCLIDEnabled()) {
      array_push($form_fields_names, 'GCLID');
    }
    foreach ($submitted_fields as $field) {
      if ('hidden_fields' !== $field && !in_array($field, $form_fields_names) || false !== strpos($hidden_fields, $field)) {
        unset($submitted_data[$field]);
      }
    }
    return $submitted_data;
  }

  public function beforeSubmittedValidate()
  {
    if ($this->verifySubmissionNonce()) {
      if ($this->isExist()) {
        $isRestricted = $this->checkSubmissionRestriction();
        if ($isRestricted && !empty($isRestricted)) {
          return new WP_Error('spam_detection', $isRestricted[0]);
        }
        if ($this->isTrappedInHoneypot()) {
          return new WP_Error('spam_detection', __('Token verification failed', 'bit-form'));
        }
        $formCurrentStep = isset($_POST['form-current-step']) ? $_POST['form-current-step'] : null;

        if (is_wp_error($this->verifyGRecaptcha())) {
          return $this->verifyGRecaptcha();
        }

        /* Implement Turnstile Captcha start */
        if (is_wp_error($this->verifyTurnstileCaptcha())) {
          return $this->verifyTurnstileCaptcha();
        }
        /* Implement Turnstile Captcha end */

        $existAuth = (new IntegrationHandler($this->_form_id))->getAllIntegration('wp_user_auth', 'wp_auth', 1);

        // check if user is already logged in and form has auth integration
        do_action('bitform_checked_exist_auth', $this->_form_id, $existAuth);
        if (!is_wp_error($existAuth) && count($existAuth) > 0 && is_user_logged_in()) {
          return new WP_Error('auth_error', __('You are already logged in', 'bit-form'));
        }
        $validateForm = $this->validateFormSubmission($_POST);
        $validateFormFiles = $this->validateFormSubmission($_FILES);
        $validateForm = array_merge($validateForm, $validateFormFiles);
        $form_fields = $this->getFields();
        // check if form-current-step is set and form is multi-step
        $formCurrentStep = isset($_POST['form-current-step']) ? $_POST['form-current-step'] : null;
        if (!is_null($formCurrentStep)) {
          $formContents = $this->getFormContent();
          $layout = $formContents->layout;
          $stepIndex = (int) $formCurrentStep - 1;
          $stepLayout = $layout[$stepIndex]->layout->lg;
          $nestedLayout = $formContents->nestedLayout;
          $step_fields = [];
          foreach ($stepLayout as $lay) {
            $fk = $lay->i;
            if (isset($nestedLayout->{$fk})) {
              $nestedLg = $nestedLayout->{$fk}->lg;
              foreach ($nestedLg as $nestedLay) {
                $nestedFk = $nestedLay->i;
                $step_fields[$nestedFk] = $form_fields[$nestedFk];
              }
            }
            $step_fields[$fk] = $form_fields[$fk];
          }
          $form_fields = $step_fields;
        }
        $formFieldValidator = new FormFieldValidator($form_fields, $_POST, $_FILES);
        $validUniuqFields = [];
        $existFilter = has_filter('bf_check_duplicate_entry');
        if (true === $existFilter) {
          $validUniuqFields = apply_filters('bf_check_duplicate_entry', $form_fields, $_POST);

          $fieldKeys = array_keys($validUniuqFields);
          $form_fields_keys = array_keys($form_fields);
          $uniqueFields = [];
          foreach ($fieldKeys as $key) {
            if (in_array($key, $form_fields_keys)) {
              $uniqueFields[] = $form_fields[$key];
            }
          }
          do_action('bitform_Unique_entry', $uniqueFields, $validUniuqFields, $this->_form_id, $_POST);
        }
        $validateField = $formFieldValidator->validate('create', $this->_form_id);

        if ($validateForm && $validateField && 0 === count($validUniuqFields)) {
          return true;
        } else {
          $error = __('Please submit form with valid fields', 'bit-form');
          if (!$validateForm) {
            $errorMessages = $error;
          } elseif (count($formFieldValidator->getMessage()) > 0) {
            $errorMessages = $formFieldValidator->getMessage();
          } else {
            $errorMessages = 0 === count($validUniuqFields) ? $error : $validUniuqFields;
          }
          return new WP_Error('validation_error', $errorMessages);
        }
      }
      return new WP_Error('unknown_form', __('Form does not exist', 'bit-form'));
    } else {
      return new WP_Error('token_expired', __('Token expired', 'bit-form'));
    }
  }

  private function verifyGRecaptcha()
  {
    $captchaSettings = $this->getCaptchaSettings();
    $captchaV3Settings = $this->getCaptchaV3Settings();
    if ($captchaSettings || $captchaV3Settings) {
      $token = $_POST['g-recaptcha-response'];
      if (!isset($_POST['g-recaptcha-response'])) {
        return new WP_Error('spam_detection', __('Please verify reCAPTCHA', 'bit-form'));
      }
      $integrationHandler = new IntegrationHandler(0);
      $allFormIntegrations = $integrationHandler->getAllIntegration('app', $captchaSettings ? 'gReCaptcha' : 'gReCaptchaV3');
      if (!is_wp_error($allFormIntegrations)) {
        foreach ($allFormIntegrations as $integration) {
          if (!is_null($integration->integration_type) && $integration->integration_type === ($captchaSettings ? 'gReCaptcha' : 'gReCaptchaV3')) {
            $integrationDetails = json_decode($integration->integration_details);
            $integrationDetails->id = $integration->id;
            $reCAPTCHA = $integrationDetails;
          }
        }
      }
      if (!empty($reCAPTCHA->secretKey)) {
        $gRecaptchaResponse = HttpHelper::post(
          'https://www.google.com/recaptcha/api/siteverify',
          ['secret' => $reCAPTCHA->secretKey, 'response' => $token]
        );
        $isgReCaptchaVerified = false;
        if (!is_wp_error($gRecaptchaResponse)) {
          if (
            $captchaV3Settings
            && !empty($gRecaptchaResponse->score)
            && ((float) $gRecaptchaResponse->score < (float) $captchaV3Settings->score)
          ) {
            wp_send_json_error(
              __(
                $captchaV3Settings->message,
                'bit-form'
              )
            );
          }

          $isgReCaptchaVerified = $gRecaptchaResponse->success;
        }
        if (!$isgReCaptchaVerified) {
          return new WP_Error('spam_detection', __('Please verify reCAPTCHA', 'bit-form'));
        }
      }
    }
  }

  private function verifyTurnstileCaptcha()
  {
    $turnstileSetting = $this->getTurnstileSettings();
    if ($turnstileSetting) {
      if (!isset($_POST['cf-turnstile-response'])) {
        return new WP_Error('spam_detection', __('Please verify Cloudflare Turnstile Captcha', 'bit-form'));
      }
      $token = $_POST['cf-turnstile-response'];
      $turnstileCaptcha = null;
      $integrationHandler = new IntegrationHandler(0);
      $turnstileIntegration = $integrationHandler->getAllIntegration('app', 'turnstileCaptcha')[0];
      if (!is_wp_error($turnstileIntegration && !is_null($turnstileIntegration->integration_type))) {
        $turnstileCaptcha = json_decode($turnstileIntegration->integration_details);
        // $integrationDetails->id = $turnstileIntegration->id;
        // $turnstileCaptcha = $integrationDetails;
      }
      if (!is_null($turnstileCaptcha)) {
        $isTurnstileCaptchaVerified = false;
        $turnstileRecaptchaResponse = HttpHelper::post(
          'https://challenges.cloudflare.com/turnstile/v0/siteverify',
          ['secret' => $turnstileCaptcha->secretKey, 'response' => $token]
        );
        if (!is_wp_error($turnstileRecaptchaResponse)) {
          if (!$turnstileRecaptchaResponse->success) {
            wp_send_json_error(
              __(
                'Cloudflare Turnstile Validation Error: ' . implode(', ', $turnstileRecaptchaResponse->{'error-codes'}),
                'bit-form'
              )
            );
          }

          $isTurnstileCaptchaVerified = $turnstileRecaptchaResponse->success;
        }
        if (!$isTurnstileCaptchaVerified) {
          return new WP_Error('spam_detection', __('Please verify Cloudflare Turnstile Captcha', 'bit-form'));
        }
      }
    }
  }

  public function verifySubmissionNonce()
  {
    if (!isset($_POST['t_identity']) && !isset($_POST['csrf'])) {
      return false;
    }
    $tIdenty = sanitize_text_field($_POST['t_identity']);
    $csrf = sanitize_text_field($_POST['csrf']);
    unset($_POST['t_identity'], $_POST['action'], $_POST['bitforms_id'], $_POST['csrf']);
    return Helpers::csrfDecrypted($tIdenty, $csrf);
  }

  public function setViewCount()
  {
    if (!current_user_can('manage_options')) {
      $update_status = $this->formModel->update(
        [
          'views' => intval(static::$form[0]->views) + 1
        ],
        [
          'id' => $this->form_id
        ]
      );
    }
  }

  public function checkSubmissionRestriction($checkedEmptySubmitted = true)
  {
    $formContents = $this->getFormContent();
    $fromRestrictionSetitingsEnabled = empty($formContents->additional->enabled) ? [] : $formContents->additional->enabled;
    $fromRestrictionSetitings = empty($formContents->additional->settings) ? null : $formContents->additional->settings;
    if (is_null($formContents->additional->enabled) || is_null($formContents->additional->settings)) {
      return false;
    }
    $restrictionMessage = [];
    $ipTool = new IpTool();
    $ipAddress = $ipTool->getIP();
    foreach ($fromRestrictionSetitingsEnabled as $restrictionKey => $isEnabled) {
      if ($isEnabled) {
        if ('entry_limit' === $restrictionKey && isset($fromRestrictionSetitings->{$restrictionKey})) {
          $formEntry = new FormEntryModel();
          $countResult = $formEntry->count(
            [
              'form_id' => $this->form_id
            ]
          );
          $count = !empty($countResult[0]) && !empty($countResult[0]->count) ? $countResult[0]->count : false;
          if ($count && $count >= intval($fromRestrictionSetitings->{$restrictionKey})) {
            $restrictionMessage[] = __('Sorry!! Entry limit exceeded', 'bit-form');
          }
        }
        if ('onePerIp' === $restrictionKey) {
          $formEntry = new FormEntryModel();
          $countResult = $formEntry->count(
            [
              'form_id' => $this->form_id,
              'user_ip' => ip2long($ipAddress)
            ]
          );
          $count = !empty($countResult[0]) && !empty($countResult[0]->count) ? $countResult[0]->count : false;

          if ($count && $count > 0) {
            $restrictionMessage[] = __('Sorry!! You have already submitted', 'bit-form');
          }
        }
        if ('is_login' === $restrictionKey && 0 === get_current_user_id()) {
          $restrictionMessage[] = __($fromRestrictionSetitings->is_login->message, 'bit-form');
        }
        if ($checkedEmptySubmitted && 'empty_submission' === $restrictionKey) {
          $isEmpty = $this->checkEmptySubmission($_POST, $_FILES);
          if ($isEmpty) {
            $restrictionMessage[] = __($fromRestrictionSetitings->empty_submission->message, 'bit-form');
          }
        }
        if ('restrict_form' === $restrictionKey && isset($fromRestrictionSetitings->{$restrictionKey})) {
          $day = empty($fromRestrictionSetitings->{$restrictionKey}->day) ? null : $fromRestrictionSetitings->{$restrictionKey}->day;
          $date = empty($fromRestrictionSetitings->{$restrictionKey}->date) ? null : $fromRestrictionSetitings->{$restrictionKey}->date;
          $time = empty($fromRestrictionSetitings->{$restrictionKey}->time) ? null : $fromRestrictionSetitings->{$restrictionKey}->time;

          $isdayOk = $isdateOk = $istimeOk = true;
          $dayNotOkMsg = $dateNotOkMsg = $timeNotOkMsg = '';
          $dateTimeHelper = new DateTimeHelper();
          if (
            !empty($day)
            && is_array($day)
            && (in_array('Friday', $day)
                || in_array('Saturday', $day)
                || in_array('Sunday', $day)
                || in_array('Monday', $day)
                || in_array('Tuesday', $day)
                || in_array('Wednesday', $day)
                || in_array('Thursday', $day))
            && (!in_array($dateTimeHelper->getDay('full-name'), $day))
          ) {
            $isdayOk = false;
            $dayMsgVarsFormat = '';
            foreach ($day as $dayIndex => $dayValue) {
              if ($dayIndex > 0) {
                $dayMsgVarsFormat .= ', ';
              }
              $dayMsgVarsFormat .= '%s';
            }
            $dayNotOkMsg = vsprintf(__("in $dayMsgVarsFormat", 'bit-form'), $day);
          }
          if (
            !empty($day)
            && is_array($day)
            && (in_array('Custom', $day))
          ) {
            $startDate = empty($date->from) ? '00-00-0000' : $date->from;
            $endDate = empty($date->to) ? '00-00-0000' : $date->to;
            $dateFormat = preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $startDate) ? 'Y-m-d' : 'm-d-Y';
            if (!empty($date->from) && false !== strpos($startDate, 'T')) {
              $startDate = $dateTimeHelper->getDate($startDate, false, null, $dateFormat);
            }
            if (!empty($date->to) && false !== strpos($endDate, 'T')) {
              $endDate = $dateTimeHelper->getDate($endDate, false, null, $dateFormat);
            }
            $currentDate = $dateTimeHelper->getDate(null, null, null, $dateFormat);
            if (!($currentDate >= $startDate && $currentDate <= $endDate)) {
              $isdateOk = false;
              $dateNotOkMsg = sprintf(__('within %s to %s', 'bit-form'), $startDate, $endDate);
            }
          }

          if (!empty($time)) {
            $startTime = empty($time->from) ? '00:00' : $time->from;
            $endTime = empty($time->to) ? '23:59.999' : $time->to;
            $currentTime = $dateTimeHelper->getTime(null, null, null, 'H:i');
            if (!($currentTime >= $startTime && $currentTime <= $endTime)) {
              $istimeOk = false;
              $startTime = $dateTimeHelper->getTime($startTime, 'H:i', null);
              $endTime = $dateTimeHelper->getTime($endTime, 'H:i', null);
              $isTimeOk = false;
              $timeNotOkMsg = sprintf(__('%s to %s', 'bit-form'), $startTime, $endTime);
            }
          }

          if (!($isdateOk && $isdayOk && $istimeOk)) {
            if (!$isdayOk) {
              $restrictionMessage[] = !empty($timeNotOkMsg) ? sprintf(__('Form is available %s From %s', 'bit-form'), $dayNotOkMsg, $timeNotOkMsg) :
                  sprintf(__('Form is available %s', 'bit-form'), $dayNotOkMsg, $timeNotOkMsg);
            } elseif (!$isdateOk) {
              $restrictionMessage[] = !empty($timeNotOkMsg) ? sprintf(__('Form is available %s From %s', 'bit-form'), $dateNotOkMsg, $timeNotOkMsg) :
                  sprintf(__('Form is available %s', 'bit-form'), $dateNotOkMsg, $timeNotOkMsg);
            } elseif (!$istimeOk) {
              $restrictionMessage[] = sprintf(__('Form is available on %s', 'bit-form'), $timeNotOkMsg);
            }
          }
        }
        if ('blocked_ip' === $restrictionKey && isset($fromRestrictionSetitings->{$restrictionKey})) {
          $isIpBlocked = false;
          foreach ($fromRestrictionSetitings->{$restrictionKey} as $ipIndex => $ipDetails) {
            if (!empty($ipDetails->status) && $ipDetails->status && !empty($ipDetails->ip) && $ipDetails->ip === $ipAddress) {
              $isIpBlocked = true;
              break;
            }
          }
          if ($isIpBlocked) {
            $restrictionMessage[] = sprintf(__('Sorry!! Your IP address is %s, Blocked from submitting the form', 'bit-form'), $ipAddress);
          }
        }
        if ('private_ip' === $restrictionKey && isset($fromRestrictionSetitings->{$restrictionKey})) {
          $isIpWhiteListed = false;
          foreach ($fromRestrictionSetitings->{$restrictionKey} as $ipIndex => $ipDetails) {
            if (!empty($ipDetails->status) && $ipDetails->status && !empty($ipDetails->ip) && $ipDetails->ip === $ipAddress) {
              $isIpWhiteListed = true;
              break;
            }
          }
          if (!$isIpWhiteListed) {
            $restrictionMessage[] = sprintf(__('Sorry!! Your IP address is %s, Blocked from submitting the form', 'bit-form'), $ipAddress);
          }
        }
      }
    }
    return $restrictionMessage;
  }

  /**
     * Will check if form is submitted by a bot
     *
     * @return Boolean true - if submitted by bot else false
     */
  public function isTrappedInHoneypot()
  {
    $isHoneyPot = false;

    if (!$this->isHoneypotActive()) {
      return false;
    }

    $token = $_POST['b_h_t'];
    $pattern = '/^([a-zA-Z0-9]*_[a-zA-Z0-9]*){4}$/';
    $decryptedToken = base64_decode(base64_decode($token));

    preg_match($pattern, $decryptedToken, $validToken);

    if ($validToken) {
      if (isset($_POST[$token]) && empty($_POST[$token])) {
        $isHoneyPot = false;
      } else {
        $isHoneyPot = true;
      }
    } else {
      $isHoneyPot = true;
    }

    if (isset($_POST[$token])) {
      unset($_POST[$token]);
    }
    unset($_POST['b_h_t']);
    return $isHoneyPot;
  }

  public function isHoneypotActive()
  {
    $formContents = $this->getFormContent();
    $enabled = empty($formContents->additional->enabled) ? null : $formContents->additional->enabled;
    if (!empty($enabled->honeypot) && $enabled->honeypot) {
      return true;
    }
    return false;
  }

  public function checkPaymentFields()
  {
    $formContents = $this->getFormContent();
    $fields = $formContents->fields;

    $payments = [];
    foreach ($fields as $fldData) {
      if ('paypal' === $fldData->typ && property_exists($fldData, 'payIntegID')) {
        $payments['paypalKey'] = $this->getClientKey($fldData->payIntegID, 'clientID');
      } elseif ('razorpay' === $fldData->typ && property_exists($fldData->options, 'payIntegID')) {
        $payments['razorpayKey'] = $this->getClientKey($fldData->options->payIntegID, 'apiKey');
      }
    }

    return $payments;
  }

  private function getClientKey($integID, $keyName)
  {
    $client = '';
    if (!empty($integID)) {
      $integrationHandler = new IntegrationHandler(0);
      $integration = $integrationHandler->getAIntegration($integID, 'app', 'payments');
      if (!is_wp_error($integration)) {
        $integration_details = json_decode($integration[0]->integration_details);
        $client = base64_encode($integration_details->{$keyName});
      }
    }
    return $client;
  }

  public function getSuccessMessageMarkups()
  {
    if (is_null($this->_work_flows)) {
      $workFlowManager = new WorkFlowHandler($this->form_id);
      $this->_work_flows = $workFlowManager->getAllworkFlow();
    }

    $ids = [];
    foreach ($this->_work_flows as $msgItem) {
      foreach ($msgItem['conditions'] as $condition) {
        if (isset($condition->actions->success)) {
          foreach ($condition->actions->success as $msg) {
            if ('successMsg' === $msg->type && isset($msg->details->id)) {
              $idObj = json_decode(stripslashes($msg->details->id));
              if (is_object($idObj) && !empty($idObj->id)) {
                array_push($ids, $idObj->id);
              }
            }
          }
        }
        if (isset($condition->actions->failure)) {
          $idObj = json_decode(stripslashes($condition->actions->failure));
          if (is_object($idObj) && !empty($idObj->id)) {
            array_push($ids, $idObj->id);
          }
        }
      }
    }
    $ids = array_unique($ids);
    if (is_null($this->_conf_messages)) {
      $successMsgHandler = new SuccessMessageHandler($this->form_id);
      $this->_conf_messages = $successMsgHandler->getMessages($ids);
    }

    $messageMarkups = '';
    if (is_wp_error($this->_conf_messages)) {
      return $messageMarkups;
    }

    foreach ($this->_conf_messages as $msgItem) {
      $messageMarkups .= $this->messageMarkup($msgItem);
    }

    return $messageMarkups;
  }

  public function getFormAbandonmentMessage()
  {
    if (class_exists('\BitCode\BitFormPro\Admin\FormSettings\FormAbandonment')) {
      $formAbandonmentSettings = FormAbandonment::getFormAbandonmentSettings($this->form_id);
      $msg = '';
      if (isset($formAbandonmentSettings->showWarningMsg) && $formAbandonmentSettings->showWarningMsg && !empty($formAbandonmentSettings->warningMsg)) {
        $msg = $formAbandonmentSettings->warningMsg;
        $msg = '<div class="bf-form-msg active warning">' . wp_kses_post($msg) . '</div>';
      }
      return $msg;
    }
  }

  public function getFormAbandonmentSettings()
  {
    if (class_exists('\BitCode\BitFormPro\Admin\FormSettings\FormAbandonment')) {
      $formAbandonmentSettings = FormAbandonment::getFormAbandonmentSettings($this->form_id);
      return $formAbandonmentSettings;
    }
    return null;
  }

  private function messageMarkup($msg)
  {
    $msgId = $msg->id;
    $msgConfig = json_decode($msg->message_config);
    $scrollClass = 'below' === $msgConfig->msgType ? 'scroll' : '';

    return <<<SUCCESSMSG
            <div role="dialog" aria-hidden="true" data-modal-backdrop="true" class="{$this->getAtomicCls("msg-container-{$msgId}")} deactive {$scrollClass}">
              <div data-contentid="{$this->getFormIdentifier()}" data-msgid="{$msgId}" role="button" class="{$this->getAtomicCls("msg-background-{$msgId}")} msg-backdrop">
                <div class="bf-msg-content {$this->getAtomicCls("msg-content-{$msgId}")}">
                  <button data-contentid="{$this->getFormIdentifier()}" data-msgid="{$msgId}" class="{$this->getAtomicCls("close-{$msgId}")} bf-msg-close" type="button">
                    <svg class="{$this->getAtomicCls("close-icn-{$msgId}")}" viewBox="0 0 30 30">
                      <line fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" x1="4" y1="3.88" x2="26" y2="26.12"></line>
                      <line fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" x1="26" y1="3.88" x2="4" y2="26.12"></line>
                    </svg>
                  </button>
                  <div class="msg-content"></div>
                </div>
              </div>
            </div>
SUCCESSMSG;
  }
}
