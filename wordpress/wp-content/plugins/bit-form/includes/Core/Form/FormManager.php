<?php

/**
 * Get set Form,fields
 */

namespace BitCode\BitForm\Core\Form;

/**
 * FrontendFormManager class
 */

use BitCode\BitForm\Admin\Form\CustomFieldHandler;
use BitCode\BitForm\Core\Database\FormEntryLogModel;
use BitCode\BitForm\Core\Database\FormEntryMetaModel;
use BitCode\BitForm\Core\Database\FormEntryModel;
use BitCode\BitForm\Core\Database\FormModel;
use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Messages\SuccessMessageHandler;
use BitCode\BitForm\Core\Util\FieldValueHandler;
use BitCode\BitForm\Core\Util\FileHandler;
use BitCode\BitForm\Core\Util\FrontendHelpers;
use BitCode\BitForm\Core\Util\IpTool;
use BitCode\BitForm\Core\WorkFlow\WorkFlow;
use BitCode\BitForm\Core\WorkFlow\WorkFlowHandler;
use WP_Error;

class FormManager
{
  protected static $form;
  protected $formModel;
  protected $form_id;
  private $_has_upload;
  private $_field_label;
  private $_fields;
  private $_repeaterFields;
  private $_work_flows;
  private $_conf_messages;
  private $_atomic_class_map;
  private $_saveFormAsDraft;

  public function __construct($form_id)
  {
    $this->form_id = $form_id;
    $this->formModel = new FormModel();

    static::$form = $this->formModel->get(
      [
        'id',
        'form_content',
        'form_name',
        'created_at',
        'views',
        'entries',
        'status',
        'builder_helper_state',
        'atomic_class_map',
        'generated_script_page_ids',
      ],
      [
        'id' => $form_id,
      ]
    );
    if (!is_wp_error(static::$form)) {
      $this->_atomic_class_map = json_decode(static::$form[0]->atomic_class_map);
      $bfMultipleFormsExists = FrontendHelpers::hasMultipleForms();
      if ($bfMultipleFormsExists && isset($this->_atomic_class_map->atomic_class_map_with_form_id)) {
        $this->_atomic_class_map = $this->_atomic_class_map->atomic_class_map_with_form_id;
      } elseif (isset($this->_atomic_class_map->atomic_class_map)) {
        $this->_atomic_class_map = $this->_atomic_class_map->atomic_class_map;
      }
    }
  }

  public function isExist()
  {
    return (!static::$form || is_wp_error(static::$form)) ? false : true;
  }

  public function checkStatus()
  {
    return '1' === static::$form[0]->status ? true : false;
  }

  public function getFieldsContent()
  {
    return self::$form[0]->form_content;
  }

  public function getFont()
  {
    $atomicClassMap = $this->_atomic_class_map;
    $font = isset($atomicClassMap->font) ? $atomicClassMap->font : '';
    return $font;
  }

  public function getStyle()
  {
    $builerState = \json_decode(static::$form[0]->builder_helper_state);
    $style = '';
    $themeVars = $builerState->themeVars;
    $themeColors = $builerState->themeColors;

    if (!empty($themeVars)) {
      $style .= ':root {';
      foreach ($themeVars->lgLightThemeVars as $key => $value) {
        $style .= "$key: $value; ";
      }
      $style .= '} ';
    }
    if (!empty($themeColors)) {
      $style .= ' :root {';
      foreach ($themeColors->lightThemeColors as $k => $v) {
        $style .= "$k:$v; ";
      }
      $style .= '} ';
    }

    $field = $builerState->style->lgLightStyles->fields;
    foreach ($field as $value) {
      $classes = $value->classes;
      foreach ($classes as $key => $value) {
        $style .= "{$key} {";
        foreach ($value as $k => $v) {
          $style .= "$k:$v; ";
        }
        $style .= '} ';
      }
    }
    return $style;
  }

  public function getCustomStyle()
  {
    $customCssCodes = '';
    $customCSSPath = BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-styles' . DIRECTORY_SEPARATOR . "bitform-custom-{$this->form_id}.css";

    if (file_exists($customCSSPath)) {
      $file = fopen($customCSSPath, 'r');
      $customCssCodes = fread($file, filesize($customCSSPath));
      fclose($file);
    }

    return $customCssCodes;
  }

  public function getCustomJS()
  {
    $customJSCodes = '';
    $customJsPath = BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-scripts' . DIRECTORY_SEPARATOR . "bitform-custom-{$this->form_id}.js";
    if (file_exists($customJsPath)) {
      $file = fopen($customJsPath, 'r');
      $customJSCodes = fread($file, filesize($customJsPath));
      fclose($file);
    }

    return $customJSCodes;
  }

  public function getFormContentWithValue($defaultValues)
  {
    $form_content = \json_decode(static::$form[0]->form_content);
    // this filter just use private purpose
    $form_content->fields = apply_filters('bitform_dynamic_field_filter', $form_content->fields);
    if (!is_array($defaultValues) || 0 === count($defaultValues)) {
      return $form_content;
    }
    foreach ($form_content->fields as $fieldKey => $fieldDetails) {
      // $field_name = empty($fieldDetails->lbl) ? null : \preg_replace('/[\`\~\!\@\#\$\'\.\s\?\+\-\*\&\|\/\\!]/', '_', $fieldDetails->lbl);
      $fieldName = $fieldDetails->fieldName;
      $defaultValue = isset($defaultValues[$fieldName]) ? $defaultValues[$fieldName] : null;
      $defaultValue = isset($defaultValues[$fieldKey]) ? $defaultValues[$fieldKey] : $defaultValue;
      if ((isset($fieldDetails->mul) || 'check' === $fieldDetails->typ) && isset($defaultValue)) {
        // if (is_array($defaultValue)) {
        //     $fieldDetails->val =
        //         wp_json_encode(
        //             array_map('sanitize_text_field', $defaultValue)
        //         );
        // } else {
        //     $fieldDetails->val = sanitize_text_field($defaultValue);
        // }
        if ((isset($fieldDetails->mul) && true === $fieldDetails->mul) || is_array($defaultValue)) {
          $fieldDetails->val = wp_json_encode(array_map('sanitize_text_field', $defaultValue));
        } elseif (!is_array($defaultValue)) {
          $fieldDetails->val = sanitize_text_field($defaultValue);
        }
      } elseif (!is_null($defaultValue)) {
        $fieldDetails->val = is_string($defaultValue) ?
            sanitize_text_field($defaultValue) :
            sanitize_text_field($defaultValue[count($defaultValue) - 1]);
      }
    }
    return $form_content;
  }

  public function getFormContent()
  {
    $formContent = json_decode(static::$form[0]->form_content);
    $types = ['check', 'radio', 'select'];
    $filter = false;
    foreach ($formContent->fields as $field) {
      if (in_array($field->typ, $types) && property_exists($field, 'customType')) {
        $filter = true;
        break; // reduce unnecessary loop
      }
    }
    if (true === $filter) {
      $updateFields = apply_filters('bitform_dynamic_field_filter', $formContent->fields);
      $formContent->fields = $updateFields;
    }
    return $formContent;
  }

  public function getFormInfo()
  {
    $formContent = json_decode(static::$form[0]->form_content);
    $formInfo = isset($formContent->formInfo) ? $formContent->formInfo : null;
    return $formInfo;
  }

  public function getFormHelperStates()
  {
    $formHelperStates = json_decode(static::$form[0]->builder_helper_state);
    return $formHelperStates;
  }

  public function getAtomicClsMap()
  {
    return $this->_atomic_class_map;
  }

  private function is_json($str)
  {
    $json = json_decode($str);
    return $json && $str !== $json;
  }

  public function getFormData($columnName = '')
  {
    if (empty($columnName)) {
      return null;
    }

    $form = static::$form[0];
    if (!isset($form->{$columnName})) {
      return null;
    }

    $data = $form->{$columnName};
    if ($this->is_json($data)) {
      return json_decode($data);
    }

    return $data;
  }

  public function getFormName()
  {
    return static::$form[0]->form_name;
  }

  public function getFields()
  {
    if (!is_null($this->_fields)) {
      return $this->_fields;
    }
    $form_content = \json_decode(static::$form[0]->form_content);
    $layout = $form_content->layout;
    $fields = $form_content->fields;
    $field_details = [];
    foreach ($fields as $key => $field) {
      if ('recaptcha' === $field->typ) {
        continue;
      }
      // $field_name = empty($field->lbl) ? null : \preg_replace('/[\`\~\!\@\#\$\'\.\s\?\+\-\*\&\|\/\\\!]/', '_', $field->lbl);
      $field_type = $field->typ;
      $field_details[$key]['label'] = empty($field->lbl) ? null : $field->lbl;
      $field_details[$key]['type'] = $field_type;
      $field_details[$key]['key'] = $key;
      $field_details[$key]['name'] = isset($field->fieldName) ? $field->fieldName : '';
      if (isset($field->customType)) {
        $field_details[$key]['customType'] = $field->customType;
      }

      if (isset($field->err)) {
        if (isset($field->err->entryUnique)) {
          $field_details[$key]['entryUnique'] = $field->err->entryUnique;
        }
        if (isset($field->err->userUnique)) {
          $field_details[$key]['userUnique'] = $field->err->userUnique;
        }
      }

      if (isset($field->mul)) {
        $field_details[$key]['mul'] = $field->mul;
      }
      if ('file-up' === $field_type && isset($field->exts)) {
        $field_details[$key]['valid']['type'] = $field->exts;
      }
      if ('file-up' === $field_type && isset($field->mxUp)) {
        $field_details[$key]['valid']['upload_size'] = (int) $field->mxUp;
      }
      if (isset($field->valid) && !is_null($field->valid)) {
        if (isset($field->valid->req)) {
          $field_details[$key]['valid']['req'] = $field->valid->req;
        }
        if (isset($field->valid->reqMsg)) {
          $field_details[$key]['valid']['reqMsg'] = $field->valid->reqMsg;
        }
        if (isset($field->valid->typMsg)) {
          $field_details[$key]['valid']['typMsg'] = $field->valid->typMsg;
        }
      }
      if ($this->isRepeatedField($key)) {
        $field_details[$key]['repeated'] = true;
      }
    }
    if ($this->isGCLIDEnabled()) {
      $field_details['GCLID']['name'] = 'GCLID';
      $field_details['GCLID']['adminLbl'] = 'GCLID';
      $field_details['GCLID']['key'] = 'GCLID';
      $field_details['GCLID']['type'] = 'hidden';
    }
    $this->_fields = $field_details;
    return $field_details;
  }

  public function getFieldsKey()
  {
    $form_content = \json_decode(static::$form[0]->form_content);
    $fields = $form_content->fields;
    $field_details = [];
    foreach ($fields as $key => $field) {
      if ('recaptcha' === $field->typ) {
        continue;
      }
      // $field_name = empty($field->lbl) ? null : \preg_replace('/[\`\~\!\@\#\$\'\.\s\?\+\-\*\&\|\/\\\!]/', '_', $field->lbl);
      $field_details[$key] = $key;
    }
    if ($this->isGCLIDEnabled()) {
      $field_details['GCLID'] = 'GCLID';
    }
    return $field_details;
  }

  public function getFieldLabel($forQuery = false)
  {
    if (!is_null($this->_field_label)) {
      return $this->_field_label;
    }
    $form_content = \json_decode(static::$form[0]->form_content);
    $fields = $form_content->fields;
    $field_details = [];
    $fieldCounter = 0;
    foreach ($fields as $key => $field) {
      if ('recaptcha' === $field->typ || 'turnstile' === $field->typ || 'html' === $field->typ || 'button' === $field->typ) {
        continue;
      }
      $field_details[$fieldCounter]['name'] = empty($field->lbl) ? null : $field->lbl;
      $field_details[$fieldCounter]['adminLbl'] = empty($field->adminLbl) ? $field_details[$fieldCounter]['name'] : $field->adminLbl;
      $field_details[$fieldCounter]['key'] = $key;
      $field_details[$fieldCounter]['type'] = $field->typ;
      $fieldCounter += 1;
    }
    if ($this->isGCLIDEnabled()) {
      $field_details[$fieldCounter]['name'] = 'GCLID';
      $field_details[$fieldCounter]['adminLbl'] = 'GCLID';
      $field_details[$fieldCounter]['key'] = 'GCLID';
      $field_details[$fieldCounter]['type'] = 'hidden';
      $fieldCounter += 1;
    }
    if (!$forQuery) {
      $field_details = (array) $this->addEntryInfo($field_details, $fieldCounter);
    }
    $this->_field_label = $field_details;
    return $field_details;
  }

  public function getUploadFields()
  {
    if (!is_null($this->_has_upload)) {
      return $this->_has_upload;
    }
    $upload_fields = [];
    $form_field_details = $this->getFields();
    foreach ($form_field_details as $field_name => $__field_detail) {
      if (isset($__field_detail['type']) && ('file-up' === $__field_detail['type'] || 'advanced-file-up' === $__field_detail['type'])) {
        $upload_fields[] = $field_name;
      }
    }
    $this->_has_upload = $upload_fields;
    return $upload_fields;
  }

  public function getSignatureFilePath($blobLink, $form_id, $entry_id, $imgType)
  {
    $imgTypes = [
      'image/png'     => 'png',
      'image/jpeg'    => 'jpg',
      'image/svg+xml' => 'svg',
    ];
    $data_uri = $blobLink;
    $encoded_image = explode(',', $data_uri)[1];
    $decoded_image = base64_decode($encoded_image);
    $_upload_dir = BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $form_id . DIRECTORY_SEPARATOR . $entry_id;

    wp_mkdir_p($_upload_dir);

    $filename = "{$entry_id}-" . time() . ".{$imgTypes[$imgType]}";
    $fullPath = $_upload_dir . DIRECTORY_SEPARATOR . $filename;
    file_put_contents($fullPath, $decoded_image);

    return $filename;
  }

  private function entryInsert($user_details)
  {
    $formEntryModel = new FormEntryModel();
    $entryId = $formEntryModel->insert(
      [
        'form_id'     => $this->form_id,
        'user_id'     => $user_details['id'],
        'user_ip'     => $user_details['ip'],
        'user_device' => $user_details['device'],
        'referer'     => $user_details['page'],
        'status'      => $this->_saveFormAsDraft ? 9 : 1,
        'created_at'  => $user_details['time'],
      ]
    );
    return $entryId;
  }

  private function submisionLog($user_details, $entry_id, $type)
  {
    $formEntryLogModel = new FormEntryLogModel();
    $submissionLogData = [
      'user_id'       => $user_details['id'],
      'action_type'   => $type, // create, update
      'log_type'      => 'entry',
      'ip'            => $user_details['ip'],
      'form_entry_id' => $entry_id,
      'content'       => null,
      'form_id'       => $this->form_id,
      'created_at'    => $user_details['time'],
    ];
    $submissionLogData = apply_filters('bitform_filter_submission_log_data', $submissionLogData, $this->form_id, $type);
    $logId = $formEntryLogModel->form_log_insert(
      $submissionLogData
    );
    return $logId;
  }

  private function isArrayAllKeyInt($InputArray)
  {
    if (!is_array($InputArray)) {
      return false;
    }

    if (count($InputArray) <= 0) {
      return true;
    }

    return array_unique(array_map('is_int', array_keys($InputArray))) === [true];
  }

  public function formatSubmittedData($submitted_data)
  {
    $form_content = $this->getFormContent();
    $form_fields = $form_content->fields;

    foreach ($submitted_data as $key => $value) {
      if (!isset($form_fields->{$key})) {
        continue;
      }
      $field_data = $form_fields->{$key};
      $field_type = $field_data->typ;
      if ('select' === $field_type && !empty($field_data->config->multipleSelect)) {
        $valueArr = [];
        if ($this->isRepeatedField($key)) {
          foreach ($value as $index => $v) {
            $valueArr[$index] = explode(BITFORMS_BF_SEPARATOR, $v);
          }
        } else {
          $valueArr = explode(BITFORMS_BF_SEPARATOR, $value);
        }
        $submitted_data[$key] = $valueArr;
      }
    }
    $submitted_data = apply_filters('bitform_filter_format_submitted_data', $submitted_data, $this->form_id);
    return $submitted_data;
  }

  private function formatRepeateFieldData($submitted_data, $form_fields)
  {
    $repeaterFields = $this->getRepeaterFields();
    foreach ($repeaterFields as $repeaterFldKey => $repeatedFields) {
      $repeatIndexes = $submitted_data["{$form_fields[$repeaterFldKey]['name']}-repeat-index"];
      $repeatIndexes = explode(',', $repeatIndexes);
      foreach ($repeatIndexes as $slNo => $repeatIndex) {
        foreach ($repeatedFields as $repeatedField) {
          if (!isset($submitted_data[$repeaterFldKey][$slNo])) {
            $submitted_data[$repeaterFldKey][$slNo] = [];
          }
          $submitted_data[$repeaterFldKey][$slNo][$repeatedField] = $submitted_data[$repeatedField][$repeatIndex];
        }
      }
      foreach ($repeatedFields as $repeatedField) {
        unset($submitted_data[$repeatedField]);
      }
      unset($submitted_data["{$form_fields[$repeaterFldKey]['name']}-repeat-index"]);
    }

    return $submitted_data;
  }

  private function saveEntryMeta($submitted_data, $entry_id)
  {
    $errorInEntryMetaInsert = false;
    $entryMeta = new FormEntryMetaModel();
    foreach ($submitted_data as $key => $value) {
      $value = $submitted_data[$key];
      if (is_string($value)) {
        $value = wp_unslash($value);
      } elseif ($this->isArrayAllKeyInt($value)) {
        $value = wp_json_encode(array_values($value));
      } else {
        $value = wp_json_encode($value);
      }
      $status = $entryMeta->insert(
        [
          'bitforms_form_entry_id' => $entry_id,
          'meta_key'               => $key,
          'meta_value'             => $value,
        ]
      );
      if (is_wp_error($status)) {
        $errorInEntryMetaInsert = true;
        break;
      }
    }
    return $errorInEntryMetaInsert;
  }

  public function setSaveFormAsDraft()
  {
    $this->_saveFormAsDraft = true;
  }

  public function saveFormEntry($submitted_data)
  {
    $submitted_data = $this->formatSubmittedData($submitted_data);
    $submitted_data = apply_filters('bitform_filter_save_form_entry', $submitted_data, $this->form_id);
    $form_content = \json_decode(static::$form[0]->form_content);
    do_action('bitform_save_entry', $this, $submitted_data, $this->form_id);
    $key = null;
    $ipTool = new IpTool();
    $fileHandler = new FileHandler();
    $form_fields = $this->getFields();
    $file_fields = $this->getUploadFields();

    foreach ($_FILES as $file_name => $file_details) {
      if ($file_fields && in_array($file_name, $file_fields)) {
        $validation = $fileHandler->validation($file_name, $file_details, $this->form_id);
        if (!empty($validation['error_type']) && !empty($validation['message'])) {
          return new WP_Error($validation['error_type'], __($validation['message'], 'bit-form'));
        }
      }
    }
    $user_details = $ipTool->getUserDetail();
    $user_details = apply_filters('bitform_filter_user_details', $user_details, $this->form_id);
    $user_details = apply_filters('bitform_filter_save_entry_user_details', $user_details, $this->form_id);

    $form_fields = $this->getFields();
    $submitted_data = $this->passwordEncrypted($submitted_data, $form_fields);
    $submitted_data = $this->formatRepeateFieldData($submitted_data, $form_fields);
    global $wpdb;
    $wpdb->query('START TRANSACTION');
    $entry_id = $this->entryInsert($user_details);
    $log_id = null;

    $GLOBALS['bf_entry_id'] = $entry_id;

    if (is_wp_error($entry_id)) {
      return new WP_Error('insert_error', __('Sorry, Error occurred in saving form entry', 'bit-form'));
    }
    if ($entry_id) {
      $log_id = $this->submisionLog($user_details, $entry_id, 'create', $key);
      if (is_wp_error($log_id)) {
        $wpdb->query('ROLLBACK');
        return new WP_Error('error_entry_log', __('Sorry, error occurred in logging form entry', 'bit-form'));
      }
    }
    if ($entry_id) {
      $submitted_fields = $this->getFormContentWithValue($submitted_data)->fields;
      $workFlowRunHelper = new WorkFlow($this->form_id);

      $workFlowreturnedOnSubmit = $workFlowRunHelper->executeOnSubmit(
        'create',
        $submitted_fields,
        $submitted_data,
        $entry_id,
        $log_id
      );

      if (!empty($workFlowreturnedOnSubmit['fields'])) {
        $submitted_data = $workFlowreturnedOnSubmit['fields'];
      }

      $file_fields = $this->getUploadFields();
      $formFields = $this->getFields();
      $submitted_data = FileHandler::tempDirToUploadDir($submitted_data, $formFields, $this->form_id, $entry_id);
      $fileHandler = new FileHandler();
      foreach ($_FILES as $file_name => $file_details) {
        if ($file_fields && in_array($file_name, $file_fields)) {
          $filePath = [];
          $repeaterFldKey = $this->isRepeatedField($file_name);
          if ($repeaterFldKey) {
            foreach ($file_details['name'] as $slNo => $fileName) {
              $repeateFileDetails = [
                'name'     => $file_details['name'][$slNo],
                'type'     => $file_details['type'][$slNo],
                'tmp_name' => $file_details['tmp_name'][$slNo],
                'error'    => $file_details['error'][$slNo],
                'size'     => $file_details['size'][$slNo],
              ];
              $filePath = $fileHandler->moveUploadedFiles($repeateFileDetails, $this->form_id, $entry_id);
              if (!empty($filePath)) {
                $submitted_data[$repeaterFldKey][$slNo - 1][$file_name] = $filePath;
              }
            }
          } else {
            $filePath = $fileHandler->moveUploadedFiles($file_details, $this->form_id, $entry_id);
            if (!empty($filePath)) {
              $submitted_data[$file_name] = $filePath;
            }
          }
        }
      }

      /* ======== for Signature field ===========*/
      foreach ($form_content->fields as $key => $field) {
        if ('signature' === $field->typ) {
          $fld_data = $submitted_data[$key];
          $img_type = $field->config->imgTyp;
          $submitted_data[$key] = $this->getSignatureFilePath($fld_data, $this->form_id, $entry_id, $img_type);
          break;
        }
      }

      if (!isset($form_content->additional->enabled->submission)) {
        $errorInEntryMetaInsert = $this->saveEntryMeta($submitted_data, $entry_id);
        if ($errorInEntryMetaInsert) {
          do_action('bitform_save_entry_error', $this, $submitted_data, $this->form_id);
          $wpdb->query('ROLLBACK');
          return new WP_Error('insert_error', __('Sorry, Error occured in saving form entry data', 'bit-form'));
        }
        do_action('bitform_after_save_entry_success', $this, $submitted_data, $this->form_id, $entry_id);
      } else {
        $wpdb->query('ROLLBACK');
      }
      $wpdb->query('COMMIT');
      $this->setSubmissionCount();
      $workFlowreturnedOnSubmit['entry_id'] = $entry_id;
      $workFlowreturnedOnSubmit['fields'] = $submitted_data;
      $workFlowreturnedOnSubmit = apply_filters('bitform_filter_return_submit_success', $workFlowreturnedOnSubmit, $this->form_id);

      return $workFlowreturnedOnSubmit;
    }
  }

  public function passwordEncrypted($updatedValue, $form_fields)
  {
    $integrationHandler = new IntegrationHandler($this->form_id);
    $formIntegrations = $integrationHandler->getAllIntegration('wp_user_auth', 'wp_auth', 1);
    if (!isset($formIntegrations->errors['result_empty'])) {
      foreach ($form_fields as $field) {
        if (array_key_exists($field['key'], $updatedValue) && 'password' === $field['type']) {
          $updatedValue[$field['key']] = '**** (encrypted)';
        }
      }
    }
    return $updatedValue;
  }

  public function updateFormEntry($updatedValue, $formID, $entryID)
  {
    $updatedValue = $this->formatSubmittedData($updatedValue);
    $updatedValue = apply_filters('bitform_filter_update_form_entry', $updatedValue, $this->form_id);
    do_action('bitform_update_entry', $this, $updatedValue, $formID, $entryID);
    $formEntryModel = new FormEntryModel();
    $formEntryLogModel = new FormEntryLogModel();
    $formOldData = $formEntryLogModel->get_form_value($entryID);
    $key = null;
    $entryMeta = new FormEntryMetaModel();
    $ipTool = new IpTool();
    $user_details = $ipTool->getUserDetail();
    $user_details = apply_filters('bitform_filter_user_details', $user_details, $this->form_id);
    $user_details = apply_filters('bitform_filter_update_entry_user_details', $user_details, $this->form_id);

    $form_fields = $this->getFields();

    $updatedValue = $this->passwordEncrypted($updatedValue, $form_fields);
    $updatedValue = $this->formatRepeateFieldData($updatedValue, $form_fields);
    $field_map = [];
    foreach ($formOldData as $index => $data) {
      foreach ($form_fields as $field_key => $field) {
        if ($data->meta_key === $field['key']) {
          $field_map[$field_key] = $field['key'];
        }
      }
    }
    $oldEntry = $formEntryModel->get('status', ['id' => $entryID])[0];
    $formEntry = $formEntryModel->update(
      [
        'user_id'     => $user_details['id'],
        'user_ip'     => $user_details['ip'],
        'user_device' => $user_details['device'],
        'status'      => ('9' === $oldEntry->status && !$this->_saveFormAsDraft) ? 1 : $oldEntry->status,
        'updated_at'  => $user_details['time'],
      ],
      [
        'form_id' => $formID,
        'id'      => $entryID,
      ]
    );
    $log_id = null;
    if ($formEntry) {
      $log_id = $this->submisionLog($user_details, $entryID, 'update');
    }

    if (is_wp_error($formEntry) || !$formEntry) {
      return new WP_Error('empty_form', __('provided form entries does not exists', 'bit-form'));
    }
    $formFields = $this->getFields();
    $updatedValue = FileHandler::tempDirToUploadDir($updatedValue, $formFields, $this->form_id, $entryID);
    $file_fields = $this->getUploadFields();
    if (count($file_fields) > 0) {
      $fileHandler = new FileHandler();
      foreach ($_FILES as $file_name => $file_details) {
        if ($file_fields && in_array($file_name, $file_fields)) {
          $validation = $fileHandler->validation($file_name, $file_details, $this->form_id);
          if (!empty($validation['error_type']) && !empty($validation['message'])) {
            return new WP_Error($validation['error_type'], __($validation['message'], 'bit-form'));
          }
        }
      }
      foreach ($file_fields as $field_name) {
        if (isset($updatedValue[$field_name . '_old'])) {
          $file_exists = $entryMeta->get(
            'meta_value',
            [
              'bitforms_form_entry_id' => $entryID,
              'meta_key'               => $field_name,
            ]
          );
          if (!is_wp_error($file_exists) && count($file_exists) > 0) {
            $files_in_db = json_decode($file_exists[0]->meta_value);
            $files_old = empty($updatedValue[$field_name . '_old']) ? [] : explode(',', $updatedValue[$field_name . '_old']);
            $deleted_file = array_diff($files_in_db, $files_old);
            if (count($deleted_file) > 0) {
              $fileHandler->deleteFiles($formID, $entryID, $deleted_file);
            }
            $updatedValue[$field_name] = wp_json_encode($files_old);
          }
        }
        if (!empty($_FILES[$field_name]['name'])) {
          $repeaterFldKey = $this->isRepeatedField($field_name);
          if ($repeaterFldKey) {
            $file_details = $_FILES[$field_name];
            foreach ($file_details['name'] as $index => $file) {
              $repeateFileDetails = [
                'name'     => $file_details['name'][$index],
                'type'     => $file_details['type'][$index],
                'tmp_name' => $file_details['tmp_name'][$index],
                'error'    => $file_details['error'][$index],
                'size'     => $file_details['size'][$index],
              ];
              $meta_value = $fileHandler->moveUploadedFiles($repeateFileDetails, $formID, $entryID, $index);
              if (!empty($meta_value)) {
                $updatedValue[$repeaterFldKey][$index - 1][$field_name] = wp_json_encode($meta_value);
              }
            }
          } else {
            $meta_value = $fileHandler->moveUploadedFiles($_FILES[$field_name], $formID, $entryID);
            if (!empty($meta_value)) {
              if (isset($updatedValue[$field_name . '_old']) && !is_wp_error($file_exists) && count($file_exists) > 0) {
                $meta_value = empty($files_old) ? $meta_value : array_merge($meta_value, $files_old);
                $updatedValue[$field_name] = $meta_value;
              } else {
                $updatedValue[$field_name] = $meta_value;
              }
            }
          }
        }
      }
    }

    unset($updatedValue['_ajax_nonce'], $_REQUEST['g-recaptcha-response']);

    $workFlowRunHelper = new WorkFlow($formID);
    $workFlowreturnedOnSubmit = $workFlowRunHelper->executeOnSubmit(
      'edit',
      $this->getFormContentWithValue($updatedValue)->fields,
      $updatedValue,
      $entryID,
      $log_id
    );
    if (!empty($workFlowreturnedOnSubmit['fields'])) {
      $updatedValue = $workFlowreturnedOnSubmit['fields'];
    }

    $toUpdateValues = [];
    foreach ($form_fields as $field) {
      if (isset($updatedValue[$field['key']])) {
        $toUpdateValues[$field['key']] = $updatedValue[$field['key']];
      }
    }
    $form_content = \json_decode(static::$form[0]->form_content);

    foreach ($form_content->fields as $key => $field) {
      if ('signature' === $field->typ) {
        $fld_data = $updatedValue[$key];
        $img_type = $field->config->imgTyp;
        $toUpdateValues[$key] = $this->getSignatureFilePath($fld_data, $this->form_id, $entryID, $img_type);
        break;
      }
    }

    $formEntryMetaUpdateStatus = $entryMeta->update(
      $toUpdateValues,
      [
        'bitforms_form_entry_id' => $entryID,
      ]
    );
    if (is_wp_error($formEntryMetaUpdateStatus) || isset($newFileInsertStatus) && is_wp_error($newFileInsertStatus)) {
      do_action('bitform_update_entry_error', $this, $toUpdateValues, $formEntryMetaUpdateStatus, $this->form_id);
      return $formEntryMetaUpdateStatus;
    }
    $toUpdateValues = array_merge($formEntryMetaUpdateStatus, ['entry_id' => $entryID]);
    do_action('bitform_after_update_entry_success', $this, $toUpdateValues, $formID, $entryID);
    if (empty($workFlowreturnedOnSubmit['message'])) {
      $workFlowreturnedOnSubmit['message'] = __('Entry Updated Successfully', 'bit-form');
    }
    $customFieldHandler = new CustomFieldHandler();
    $toUpdateValues = $customFieldHandler->updatedData($form_fields, $toUpdateValues);

    $workFlowreturnedOnSubmit['updatedData'] = $toUpdateValues;
    $counter = 0;
    for ($i = 0; $i < count($formOldData); $i++) {
      if (array_key_exists($formOldData[$i]->meta_key . '_old', $toUpdateValues)) {
        unset($toUpdateValues[$formOldData[$i]->meta_key . '_old']);
      }
      if (in_array($formOldData[$i]->meta_key, $file_fields)) {
        if (
          empty($_FILES[$formOldData[$i]->meta_key]['name'])
          || (is_array($_FILES[$formOldData[$i]->meta_key]['name'])
              && 1 === count($_FILES[$formOldData[$i]->meta_key]['name'])
              && empty($_FILES[$formOldData[$i]->meta_key]['name'][0]))
        ) {
          unset($toUpdateValues[$formOldData[$i]->meta_key]);
          continue;
        }
        if (is_array($_FILES[$formOldData[$i]->meta_key]['name']) && !in_array($_FILES[$formOldData[$i]->meta_key]['name'], json_decode($formOldData[$i]->meta_value))) {
          $key[$i] = '${' . $formOldData[$i]->meta_key . '} file was Updated  To ' . wp_json_encode($_FILES[$formOldData[$i]->meta_key]['name']);
        } elseif (!is_array($_FILES[$formOldData[$i]->meta_key]['name']) && !in_array($_FILES[$formOldData[$i]->meta_key]['name'], json_decode($formOldData[$i]->meta_value))) {
          $key[$i] = '${' . $formOldData[$i]->meta_key . '} file was Updated  To ' . $_FILES[$formOldData[$i]->meta_key]['name'];
        }
        unset($toUpdateValues[$formOldData[$i]->meta_key]);
      } elseif (isset($toUpdateValues[$formOldData[$i]->meta_key])) {
        if (is_array($toUpdateValues[$formOldData[$i]->meta_key])) {
          if (json_decode($formOldData[$i]->meta_value) !== $toUpdateValues[$formOldData[$i]->meta_key]) {
            $key[$i] = '${' . $formOldData[$i]->meta_key . '} was Updated From ' . implode(',', json_decode($formOldData[$i]->meta_value)) . ' To ' . implode(',', $toUpdateValues[$formOldData[$i]->meta_key]);
          }
        } elseif (is_string($toUpdateValues[$formOldData[$i]->meta_key]) && !FieldValueHandler::isEmpty($toUpdateValues[$formOldData[$i]->meta_key])) {
          if ($formOldData[$i]->meta_value !== $toUpdateValues[$formOldData[$i]->meta_key]) {
            $key[$i] = '${' . $formOldData[$i]->meta_key . '} was Updated' . ($formOldData[$i]->meta_value ? ' From ' . $formOldData[$i]->meta_value : '') . ' To ' . $toUpdateValues[$formOldData[$i]->meta_key];
          }
        }
      }
      $counter++;
    }

    $newField = array_keys(array_diff_key($formEntryMetaUpdateStatus, $field_map));
    for ($i = 0; $i < count($newField); $i++) {
      if (is_array($toUpdateValues[$newField[$i]]) && !empty($toUpdateValues[$newField[$i]])) {
        $key[$counter + $i] = '${' . $newField[$i] . '} Updated To ' . implode(',', $toUpdateValues[$newField[$i]]);
      } elseif (is_string($newField[$i]) && !FieldValueHandler::isEmpty($toUpdateValues[$newField[$i]])) {
        $key[$counter + $i] = '${' . $newField[$i] . '} Updated To ' . $toUpdateValues[$newField[$i]];
      }
    }
    if (null !== $key) {
      $logUpdate = implode('b::f', (array) $key);
      $formEntryLogUpdate = $formEntryLogModel->logUpdate($logUpdate, $log_id);
    }
    $workFlowreturnedOnSubmit['entry_id'] = $entryID;
    $workFlowreturnedOnSubmit = apply_filters('bitform_filter_return_edit_success', $workFlowreturnedOnSubmit, $this->form_id);

    return $workFlowreturnedOnSubmit;
  }

  public function getRepeaterFields()
  {
    if (!is_null($this->_repeaterFields)) {
      return $this->_repeaterFields;
    }
    $repeaterFields = [];
    $form_content = \json_decode(static::$form[0]->form_content);
    $fields = $form_content->fields;
    $nestedLayouts = !empty($form_content->nestedLayout) ? $form_content->nestedLayout : [];
    foreach ($nestedLayouts as $fieldKey => $repeatLayout) {
      if ('repeater' !== $fields->{$fieldKey}->typ) {
        continue;
      }
      $repeaterFields[$fieldKey] = [];
      foreach ($repeatLayout->lg as $fieldLayoutData) {
        $repeaterFields[$fieldKey][] = $fieldLayoutData->i;
      }
    }
    $this->_repeaterFields = $repeaterFields;
    return $repeaterFields;
  }

  public function isRepeatedField($fieldKey)
  {
    $repeatedFields = $this->getRepeaterFields();
    foreach ($repeatedFields as $repeaterKey => $repeaterFields) {
      if (in_array($fieldKey, $repeaterFields)) {
        return $repeaterKey;
      }
    }
    return false;
  }

  public function fieldNameReplaceOfPost()
  {
    $fields = $this->getFields();
    foreach ($fields as $fieldKey => $fieldData) {
      if (array_key_exists('name', $fieldData)) {
        $fldName = $fieldData['name'];
        $fldName = str_replace(['.', ' '], '_', $fldName);
        if (array_key_exists($fldName, $_POST)) {
          $temp = $_POST[$fldName];
          unset($_POST[$fldName]);
          $_POST[$fieldKey] = $temp;
        } elseif (array_key_exists($fldName, $_FILES)) {
          $temp = $_FILES[$fldName];
          unset($_FILES[$fldName]);
          $_FILES[$fieldKey] = $temp;
        }
      }
    }
  }

  public function setSubmissionCount($countStep = 1)
  {
    $update_status = $this->formModel->update(
      [
        'entries' => intval(static::$form[0]->entries) + $countStep,
      ],
      [
        'id' => $this->form_id,
      ]
    );
  }

  public function resetSubmissionCount($countStep)
  {
    $update_status = $this->formModel->update(
      [
        'entries' => intval($countStep),
      ],
      [
        'id' => $this->form_id,
      ]
    );
  }

  public function getCaptchaSettings()
  {
    $formContents = $this->getFormContent();
    $fieldStr = wp_json_encode($formContents->fields);
    if (false !== strpos($fieldStr, '"typ":"recaptcha"')) {
      return true;
    }
  }

  public function getTurnstileSettings()
  {
    $formContents = $this->getFormContent();
    $fieldStr = wp_json_encode($formContents->fields);
    if (false !== strpos($fieldStr, '"typ":"turnstile"')) {
      return true;
    }
  }

  public function getCaptchaV3Settings()
  {
    $formContents = $this->getFormContent();
    if (!empty($formContents->additional->enabled) && !empty($formContents->additional->enabled->recaptchav3)) {
      return $formContents->additional->settings->recaptchav3;
    }
    return false;
  }

  // public function getSuccessMessageMarkups() {
  //   if (is_null($this->_work_flows)) {
  //     $workFlowManager = new WorkFlowHandler($this->form_id);
  //     $this->_work_flows = $workFlowManager->getAllworkFlow();
  //   }

  //   $ids = [];
  //   foreach ($this->_work_flows as $msgItem) {
  //     foreach ($msgItem['conditions'] as $condition) {
  //       if (isset($condition->actions->success)) {
  //         foreach ($condition->actions->success as $msg) {
  //           if ('successMsg' === $msg->type && isset($msg->details->id)) {
  //             $msgDetailsId = $msg->details->id;
  //             $idObj = json_decode(stripslashes($msgDetailsId));
  //             if (is_object($idObj) && !empty($idObj->id)) {
  //               array_push($ids, $idObj->id);
  //             }
  //           }
  //         }
  //       }
  //       if (isset($condition->actions->failure)) {
  //         $idObj = json_decode(stripslashes($condition->actions->failure));
  //         if (is_object($idObj) && !empty($idObj->id)) {
  //           array_push($ids, $idObj->id);
  //         }
  //       }
  //     }
  //   }
  //   $ids = array_unique($ids);
  //   if (is_null($this->_conf_messages)) {
  //     $successMsgHandler = new SuccessMessageHandler($this->form_id);
  //     $this->_conf_messages = $successMsgHandler->getMessages($ids);
  //   }

  //   $messageMarkups = '';
  //   if (is_wp_error($this->_conf_messages)) {
  //     return $messageMarkups;
  //   }

  //   foreach ($this->_conf_messages as $key => $msgItem) {
  //     $messageMarkups .= $this->messageMarkup($msgItem->id);
  //   }

  //   return $messageMarkups;
  // }

  //   private function messageMarkup($msgId) {
  //     return <<<SUCCESSMSG
  //             <div role="dialog" aria-hidden="true" data-modal-backdrop="true" class="{$this->getAtomicCls("msg-container-{$msgId}")} deactive2 test">
  //               <div role="button" class="{$this->getAtomicCls("msg-background-{$msgId}")} msg-backdrop">
  //                 <div class="bf-notification-message {$this->getAtomicCls("msg-content-{$msgId}")}">
  //                   <button class="{$this->getAtomicCls("close-{$msgId}")} bf-msg-close" type="button">
  //                     <svg class="{$this->getAtomicCls("close-icn-{$msgId}")}" viewBox="0 0 30 30">
  //                       <line fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" x1="4" y1="3.88" x2="26" y2="26.12"></line>
  //                       <line fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" x1="26" y1="3.88" x2="4" y2="26.12"></line>
  //                     </svg>
  //                   </button>
  //                   <div class="msg-content"></div>
  //                 </div>
  //               </div>
  //             </div>
  // SUCCESSMSG;
  //   }

  public function getAtomicCls($element)
  {
    $atomicClassMap = $this->getAtomicClsMap();
    if (is_object($atomicClassMap) && property_exists($atomicClassMap, ".$element")) {
      $getAtomicCls = $atomicClassMap->{".$element"};
      return implode(' ', $getAtomicCls) . " $element";
    }
    return $element;
  }

  public function isGCLIDEnabled()
  {
    $formContents = $this->getFormContent();
    if (isset($formContents->additional->enabled->captureGCLID) && $formContents->additional->enabled->captureGCLID) {
      return true;
    }
    return false;
  }

  protected function addEntryInfo($field_details, $counter)
  {
    $infos = [
      '__user_id'      => __('User'),
      '__entry_status' => __('Status'),
      //'__user_location' => __(''),
      '__referer'     => __('Refer URL'),
      '__user_device' => __('Device'),
      '__user_ip'     => __('IP address'),
      '__created_at'  => __('Created Time'),
      '__updated_at'  => __('Modified Time'),
    ];
    foreach ($infos as $key => $value) {
      $field_details[$counter]['name'] = $value;
      $field_details[$counter]['key'] = $key;
      $field_details[$counter]['type'] = 'sys';
      $counter = $counter + 1;
    }

    return $field_details;
  }
}
