<?php

namespace BitCode\BitForm\Admin;

use BitCode\BitForm\Admin\Form\FrontEndScriptGenerator;
use BitCode\BitForm\Admin\Form\Helpers;
use BitCode\BitForm\Admin\Form\Template\TemplateProvider;
use BitCode\BitForm\BfAnalytics;
use BitCode\BitForm\Core\Database\FormEntryModel;
use BitCode\BitForm\Core\Database\FormModel;
use BitCode\BitForm\Core\Form\FormHandler;
use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Integration\Integrations;
use BitCode\BitForm\Core\Util\IpTool;
use BitCode\BitForm\Core\Util\MailConfig;
use BitCode\BitForm\Core\Util\MetaBoxService;
use WP_Error;

class AdminAjax
{
  public function register()
  {
    add_action('wp_ajax_bitforms_integrations', [$this, 'integrations']);
    add_action('wp_ajax_integration', [$this, 'integration']);
    add_action('wp_ajax_bitforms_update_form', [$this, 'updateForm']);
    add_action('wp_ajax_bitforms_templates', [$this, 'templates']);
    add_action('wp_ajax_bitforms_create_new_form', [$this, 'createNewForm']);
    add_action('wp_ajax_bitforms_save_css', [$this, 'saveCss']);
    add_action('wp_ajax_bitforms_get_template', [$this, 'getTemplate']);
    add_action('wp_ajax_bitforms_change_status', [$this, 'changeFormStatus']);
    add_action('wp_ajax_bitforms_bulk_status_change', [$this, 'changeBulkFormStatus']);
    add_action('wp_ajax_bitforms_get_a_form', [$this, 'getAForm']);
    add_action('wp_ajax_bitforms_bulk_delete_form', [$this, 'deleteBlukForm']);
    add_action('wp_ajax_bitforms_bulk_delete_form_entries', [$this, 'deleteBlukFormEntries']);
    add_action('wp_ajax_bitforms_delete_aform', [$this, 'deleteAForm']);
    add_action('wp_ajax_bitforms_duplicate_aform', [$this, 'duplicateAForm']);
    add_action('wp_ajax_bitforms_export_aform', [$this, 'exportAForm']);
    add_action('wp_ajax_bitforms_import_aform', [$this, 'importAForm']);
    add_action('wp_ajax_bitforms_get_form_entries', [$this, 'getFormEntry']);
    add_action('wp_ajax_bitforms_get_entries_for_report', [$this, 'getEntriesForReport']);
    add_action('wp_ajax_bitforms_duplicate_form_entries', [$this, 'duplicateFormEntry']);
    add_action('wp_ajax_bitforms_edit_form_entry', [$this, 'editFormEntry']);
    add_action('wp_ajax_bitforms_update_form_entry', [$this, 'updateFormEntry']);
    add_action('wp_ajax_bitforms_get_all_form', [$this, 'getAllForms']);
    add_action('wp_ajax_bitforms_get_all_wp_pages', [$this, 'getAllWPPages']);
    add_action('wp_ajax_bitforms_delete_success_messsage', [$this, 'deleteSuccessMessage']);
    add_action('wp_ajax_bitforms_delete_integration', [$this, 'deleteAIntegration']);
    add_action('wp_ajax_bitforms_delete_workflow', [$this, 'deleteAWorkflow']);
    add_action('wp_ajax_bitforms_delete_mailtemplate', [$this, 'deleteAMailTemplate']);
    add_action('wp_ajax_bitforms_duplicate_mailtemplate', [$this, 'duplicateAMailTemplate']);
    add_action('wp_ajax_bitforms_save_allForm_report_prefs', [$this, 'setAllFormsReport']);
    add_action('wp_ajax_bitforms_save_grecaptcha', [$this, 'savegReCaptcha']);
    add_action('wp_ajax_bitforms_form_log_history', [$this, 'getLogHistory']);
    add_action('wp_ajax_bitforms_import_file_data', [$this, 'importFileData']);
    add_action('wp_ajax_bitforms_filter_export_data', [$this, 'filterExportEntry']);
    add_action('wp_ajax_bitforms_api_key', [$this, 'saveApiKey']);
    add_action('wp_ajax_bitforms_form_helpers_state', [$this, 'builerHelperState']);
    add_action('wp_ajax_bitforms_icn_save_setting', [$this, 'iconUpload']);
    add_action('wp_ajax_bitforms_get_download_icn', [$this, 'getDownlodedIcons']);
    add_action('wp_ajax_bitforms_icon_remove', [$this, 'iconRemove']);
    add_action('wp_ajax_bitforms_add_custom_code', [$this, 'addCustomCode']);
    add_action('wp_ajax_bitforms_get_custom_code', [$this, 'getCustomCode']);
    add_action('wp_ajax_bitforms_entry_status_update', [$this, 'updateEntryStatus']);
    add_action('wp_ajax_bitforms_get_generel_settings', [$this, 'getGenerelSettings']);
    add_action('wp_ajax_bitforms_save_generel_settings', [$this, 'saveGenerelSettings']);
    add_action('wp_ajax_bitforms_get_form_entry_count', [$this, 'getFormEntryLabelAndCount']);
    add_action('wp_ajax_bitforms_save_payment_setting', [$this, 'savePaymentSettings']);

    // form migrate code
    add_action('wp_ajax_bitforms_get_migrated_form_contents', [$this, 'migrateFormContents']);
    add_action('wp_ajax_bitforms_migrate_to_v2_complete', [$this, 'migrationComplete']);
    // add_action('wp_ajax_bitforms_migrate_back_to_v1', [$this, 'migrationBackToV1']);

    // PRO TO FREE (SMTP)
    add_action('wp_ajax_bitforms_get_mail_config', [$this, 'getEmailConfig']);
    add_action('wp_ajax_bitforms_mail_config', [$this, 'saveEmailConfig']);
    add_action('wp_ajax_bitforms_test_email', [$this, 'testEmail']);

    // PODS INTEGRATION
    add_action('wp_ajax_bitforms_get_pod_field', [$this, 'getPodsField']);
    add_action('wp_ajax_bitforms_get_pod_type', [$this, 'getPodsType']);

    // ACF INTEGRATION
    add_action('wp_ajax_bitforms_get_acf_group_fields', [$this, 'getAcfGroupFields']);
    add_action('wp_ajax_bitforms_get_custom_field', [$this, 'getCustomField']);

    // common (get post type) for integration
    add_action('wp_ajax_bitforms_get_post_type', [$this, 'postTypeByUser']);

    // Meta Box INTEGRATION
    add_action('wp_ajax_bitforms_get_metabox_fields', [$this, 'getMetaBoxFields']);

    // CHANGELOG VERSION OPTIONS
    add_action('wp_ajax_bitforms_changelog_version', [$this, 'setChangelogVersion']);

    // Notice Options
    add_action('wp_ajax_bitforms_handle_notice', [$this, 'handleNotice']);

    // conversational
    add_action('wp_ajax_bitforms_save_conversational_css', [$this, 'saveConversationalCSS']);

    // telematry
    add_action('wp_ajax_bitforms_analytics_permission', [$this, 'telemetry']);
  }

  /**
   * Telemetry data collection
   */
  public function telemetry()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      // error_log('telemetry data: ' . json_encode($input));
      $bfAnalytics = new BfAnalytics();
      $results = $bfAnalytics->analyticsOptIn($input->permission);
      if (is_wp_error($results)) {
        wp_send_json_error($results, 411);
      } else {
        wp_send_json_success($results, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  /**
   * Undocumented function
   *
   * @return void
   */
  public function integrations()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $testIntegration = new Integrations();
      $allIntegrations = $testIntegration->getAllintegrations();
      if ($allIntegrations) {
        wp_send_json_success($allIntegrations, 200);
      } else {
        wp_send_json_error(
          __('No Integration Found', 'bit-form'),
          404
        );
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function templates()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $templateProvider = new TemplateProvider();
      $status = $templateProvider->getAllTemplates();
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function builerHelperState()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);

      $formID = $input->formID;
      $formHandler = FormHandler::getInstance();
      $results = $formHandler->admin->builerHelperState($formID);
      if (is_wp_error($results)) {
        wp_send_json_error($results, 411);
      } else {
        wp_send_json_success($results, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function getTemplate()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->getTemplate($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function getAllForms()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $formHandler = FormHandler::getInstance();
      $all_forms = $formHandler->admin->getAllForm();
      if (is_wp_error($all_forms)) {
        wp_send_json_error($all_forms->get_error_message(), 411);
      } else {
        wp_send_json_success($all_forms, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function migrateFormContents()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $all_forms = get_transient('bitforms_v1_form_contents');
      wp_send_json_success($all_forms, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function setChangelogVersion()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $version = isset($input->version) ? $input->version : '';
      update_option('bitforms_changelog_version', $version);
      wp_send_json_success($version, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function handleNotice()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      try {
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON);
        $optionName = isset($input->optionName) ? $input->optionName : '';
        $optionValue = isset($input->optionValue) ? $input->optionValue : '';
        update_option($optionName, $optionValue);
        wp_send_json_success([$optionName, $optionValue], 200);
      } catch (\Exception $e) {
        wp_send_json_error($e->getMessage(), 400);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  private function formatFormContentForUpdate($formContents)
  {
    $updatedFormContents = (object) [];
    $updatedPaths = [
      'id'              => 'id',
      'form_name'       => 'form_name',
      'layout'          => 'form_content->layout',
      'fields'          => 'form_content->fields',
      'additional'      => 'additional',
      'workFlows'       => 'workFlows',
      'formStyle'       => null,
      'style'           => null,
      'staticStyles'    => null,
      'themeColors'     => null,
      'themeVars'       => null,
      'breakpointSize'  => null,
      'customCodes'     => null,
      'builderSettings' => null,
      'layoutChanged'   => null,
      'rowHeight'       => null,
      'formSettings'    => 'formSettings',
    ];
    foreach ($updatedPaths as $key => $value) {
      if (!is_null($value)) {
        $value = Helpers::getDataFromNestedPath($formContents, $value);
      }
      $updatedFormContents->$key = $value;
    }

    foreach ($formContents['reports'] as $report) {
      if ('1' === $report['isDefault']) {
        $updatedFormContents->currentReport = $report;
        $updatedFormContents->report_id = $report['id'];
      }
    }

    $updatedFormContents = json_decode(wp_json_encode($updatedFormContents));

    $updatedFormContents->formSettings->theme = 'default';

    return $updatedFormContents;
  }

  public function getEmailConfig()
  {
    \ignore_user_abort();

    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      unset($_REQUEST['_ajax_nonce'], $_REQUEST['action']);
      $ipTool = new IpTool();
      $user_details = $ipTool->getUserDetail();
      $integrationHandler = new IntegrationHandler(0, $user_details);
      $user_details = $ipTool->getUserDetail();
      $formIntegrations = $integrationHandler->getAllIntegration('mail', 'smtp');
      wp_send_json_success($formIntegrations, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function saveEmailConfig()
  {
    \ignore_user_abort();
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $ipTool = new IpTool();
      $status = $_REQUEST['status'];
      $user_details = $ipTool->getUserDetail();
      $integrationHandler = new IntegrationHandler(0, $user_details);
      unset($_REQUEST['_ajax_nonce'], $_REQUEST['action'], $_REQUEST['status']);
      $integrationDetails = wp_json_encode($_REQUEST);
      $user_details = $ipTool->getUserDetail();
      $integrationName = 'smtp';
      $integrationType = 'smtp';
      $formIntegrations = $integrationHandler->getAllIntegration('mail', 'smtp');
      if (isset($formIntegrations->errors['result_empty'])) {
        $integrationHandler->saveIntegration($integrationName, $integrationType, $integrationDetails, 'mail', $status);
      } else {
        $integrationHandler->updateIntegration($formIntegrations[0]->id, $integrationName, $integrationType, $integrationDetails, 'mail', $status);
      }
      wp_send_json_success($formIntegrations, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function testEmail()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $to = wp_unslash($_REQUEST['to']);
      $subject = wp_unslash($_REQUEST['subject']);
      $message = wp_unslash($_REQUEST['message']);
      unset($_REQUEST['_ajax_nonce'], $_REQUEST['action']);
      if (!empty($to) && !empty($subject) && !empty($message)) {
        try {
          (new MailConfig())->sendMail();
          add_action('wp_mail_failed', function ($error) {
            $data = [];
            $data['errors'] = $error->errors['wp_mail_failed'];
            wp_send_json_error($data, 400);
          });
          $result = wp_mail($to, $subject, $message);
          wp_send_json_success($result, 200);
        } catch (\Exception $e) {
          wp_send_json_error($e->getMessage(), 400);
        }
      } else {
        wp_send_json_error(
          __(
            'Some of the test fields are empty or an invalid email supplied',
            'bit-form'
          ),
          401
        );
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function migrationComplete()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      delete_transient('bitforms_v1_form_contents');
      delete_option('bitforms_migrating_to_v2');
      update_option('bitforms_migrated_to_v2', true);
      wp_send_json_success(__('Migration Complete', 'bit-form'), 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function importDataStore()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      echo wp_json_encode($input);
      die;
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function getFormEntryLabelAndCount()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->getFormEntryLabelAndCount($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function getFormEntry()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->getFormEntry($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(__('Token expired', 'bit-form'), 401);
    }
  }

  public function getEntriesForReport()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->getEntriesForReport($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(__('Token expired', 'bit-form'), 401);
    }
  }

  public function filterExportEntry()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->getExportEntry($input->data);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(__('Token expired', 'bit-form'), 401);
    }
  }

  /**
   * Undocumented function
   *
   * @return void
   */
  public function updateForm()
  {
    \ignore_user_abort();
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      // wp_send_json_success($input, 200);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->updateForm($_REQUEST, $input);
      if (isset($input->customCodes)) {
        FrontEndScriptGenerator::customCodeFile($input->id, $input->customCodes);
      }
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function saveCss()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formId = sanitize_text_field($input->form_id);
      if (isset($input->atomicCssText)) {
        $status = FrontEndScriptGenerator::saveCssFile($formId, $input->atomicCssText);
      }
      if (isset($input->atomicCssWithFormIdText)) {
        $status = FrontEndScriptGenerator::saveCssFile("{$formId}-formid", $input->atomicCssWithFormIdText);
      }
      if (isset($input->atomicClassMap) || isset($input->atomicClassMap)) {
        $formModel = new FormModel();
        $atomicClsMap = [
          'atomic_class_map'              => isset($input->atomicClassMap) ? $input->atomicClassMap : (object) [],
          'atomic_class_map_with_form_id' => isset($input->atomicClassMapWithFormId) ? $input->atomicClassMapWithFormId : (object) [],
        ];
        $updateData['atomic_class_map'] = wp_json_encode($atomicClsMap);
        $formModel->update(
          $updateData,
          [
            'id' => $formId,
          ]
        );
      }
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function createNewForm()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      // wp_send_json_success($input, 200);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->createNewForm($_REQUEST, $input);
      $formId = sanitize_text_field($input->form_id);
      if (isset($input->customCodes)) {
        FrontEndScriptGenerator::customCodeFile($formId, $input->customCodes);
      }
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function updateEntryStatus()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formId = sanitize_text_field($input->formId);
      $entryId = sanitize_text_field($input->entryId);
      $formEntryModel = new FormEntryModel();
      $updatedTime = current_time('mysql');
      $status = $formEntryModel->update(
        [
          'status'     => 0,
          'updated_at' => $updatedTime,
        ],
        [
          'form_id' => $formId,
          'id'      => $entryId,
        ]
      );
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success(['update_at_time' => $updatedTime], 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function changeFormStatus()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->changeFormStatus($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function changeBulkFormStatus()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->changeBulkFormStatus($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function getAForm()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->getAForm($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function duplicateAForm()
  {
    \ignore_user_abort();
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->duplicateAForm($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function importAForm()
  {
    \ignore_user_abort();
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->importAForm($input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  // public function exportAForm() {
  //   if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
  //     $formHandler = FormHandler::getInstance();
  //     $status = $formHandler->admin->exportAForm($_REQUEST);
  //     if (is_wp_error($status)) {
  //       wp_send_json_error($status->get_error_message(), 411);
  //     }
  //   } else {
  //     wp_send_json_error(
  //       __(
  //         'Token expired',
  //         'bit-form'
  //       ),
  //       401
  //     );
  //   }
  // }
  public function exportAForm()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->exportAForm($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function deleteAForm()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->deleteAForm($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function deleteBlukForm()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->deleteBlukForm($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function deleteBlukFormEntries()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->deleteBlukFormEntries($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function duplicateFormEntry()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->duplicateFormEntry($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function editFormEntry()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->editFormEntry($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function getLogHistory()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->getLogHistory($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function updateFormEntry()
  {
    \ignore_user_abort();
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->updateFormEntry($_REQUEST, $_POST);

      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        $status = IntegrationHandler::maybeSetCronForIntegration($status, 'update');
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function getAllWPPages()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->getAllWPPages($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function deleteSuccessMessage()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->deleteSuccessMessage($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function deleteAIntegration()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->deleteAIntegration($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function deleteAWorkflow()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->deleteAWorkflow($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function deleteAMailTemplate()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->deleteAMailTemplate($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function duplicateAMailTemplate()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->duplicateAMailTemplate($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function setAllFormsReport()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->setAllFormsReport($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function savegReCaptcha()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->savegReCaptcha($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function saveApiKey()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      if (empty($input->api_key)) {
        $api_key = get_option('bitform_secret_api_key');
      } elseif (!empty($input->api_key)) {
        update_option('bitform_secret_api_key', sanitize_text_field($input->api_key));
        $api_key = $input->api_key;
      }
      if (!$api_key) {
        $api_key = hash('sha1', base64_encode(12345));
        update_option('bitform_secret_api_key', $api_key);
      }
      wp_send_json_success($api_key, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  private function checkExtensionWithURL($urlStr)
  {
    $pattern = '/^https?:\/\/.*(\.(svg|png|jpg|jpeg|gif))?$/i';

    return preg_match($pattern, $urlStr);
  }

  public function iconUpload()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);

      $sanitize_url = sanitize_url($input->src);

      if (!$this->checkExtensionWithURL($sanitize_url)) {
        return new WP_Error(
          'type_error',
          __('Invalid file type', 'bit-form')
        );
      }

      $uploadDirInfo = wp_upload_dir();
      $wpUploadbaseDir = $uploadDirInfo['basedir'];
      $icnDir = $wpUploadbaseDir . DIRECTORY_SEPARATOR . 'bitforms' . DIRECTORY_SEPARATOR . 'icons';

      if (!is_dir($icnDir)) {
        mkdir($icnDir);
      }

      $imageUrlData = file_get_contents($sanitize_url);

      $filename = sanitize_file_name($input->id . '-' . basename($sanitize_url));

      $validation = wp_check_filetype($filename);
      $type = $validation['type'];

      if ($type && 0 === strpos($type, 'image/')) {
        $uploaded = file_put_contents($icnDir . '/' . $filename, $imageUrlData);

        if ($uploaded) {
          $uploadedFile = BITFORMS_UPLOAD_BASE_URL . '/' . 'icons' . '/' . $filename;
          wp_send_json_success($uploadedFile, 200);
        }
      } else {
        wp_send_json_error(
          __(
            'Invalid file type',
            'bit-form'
          ),
          401
        );
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  private function getFiles()
  {
    $uploadDirInfo = wp_upload_dir();
    $wpUploadbaseDir = $uploadDirInfo['basedir'];
    $icnDir = $wpUploadbaseDir . DIRECTORY_SEPARATOR . 'bitforms' . DIRECTORY_SEPARATOR . 'icons';
    $files = [];
    if (file_exists($icnDir)) {
      $openDir = opendir($icnDir . DIRECTORY_SEPARATOR);

      while (false !== ($fileName = readdir($openDir))) {
        $ext = substr($fileName, strrpos($fileName, '.') + 1);
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {
          $files[] = $fileName;
        }
      }

      closedir($openDir);
    }
    return $files;
  }

  public function getDownlodedIcons()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $files = $this->getFiles();
      wp_send_json_success($files, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function iconRemove()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);

      $uploadDirInfo = wp_upload_dir();

      $wpUploadbaseDir = $uploadDirInfo['basedir'];
      $icnDir = $wpUploadbaseDir . DIRECTORY_SEPARATOR . 'bitforms' . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR;
      $sanitizeFileName = sanitize_file_name($input->file);
      $filePath = $icnDir . $sanitizeFileName;
      if (file_exists($filePath)) {
        wp_delete_file($filePath);
        wp_send_json_success($this->getFiles(), 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function addCustomCode()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formId = sanitize_text_field($input->form_id);
      if (filter_var($formId, FILTER_VALIDATE_INT)) {
        FrontEndScriptGenerator::customCodeFile($formId, $input->customCodes);
        $status = ['message' => 'File Update Successfully..'];
        if (is_wp_error($status)) {
          wp_send_json_error($status->get_error_message(), 411);
        } else {
          wp_send_json_success($status, 200);
        }
      } else {
        wp_send_json_error(
          __(
            'Invalid form id',
            'bit-form'
          ),
          401
        );
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function getCustomCode()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formId = sanitize_text_field($input->form_id);

      if (filter_var($formId, FILTER_VALIDATE_INT)) {
        $status = FrontEndScriptGenerator::getCustomCodes($formId);
        if (is_wp_error($status)) {
          wp_send_json_error($status->get_error_message(), 411);
        } else {
          wp_send_json_success($status, 200);
        }
      } else {
        wp_send_json_error(
          __(
            'Invalid form id',
            'bit-form'
          ),
          401
        );
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function getGenerelSettings()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $data = get_option('bitform_app_config');

      if (is_wp_error($data)) {
        wp_send_json_error($data->get_error_message(), 411);
      } else {
        if (empty($data)) {
          $data = (object)[];
        }
        wp_send_json_success($data, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired'
        ),
        401
      );
    }
  }

  public function saveGenerelSettings()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $status = update_option('bitform_app_config', $input->config);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        if (Helpers::property_exists_nested($input, 'config->cache_plugin', true)) {
          $formHandler = FormHandler::getInstance();
          $formHandler->admin->updateGeneratedScriptPageIds();
        }
        wp_send_json_success(__('Save successfully done'));
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired'
        ),
        401
      );
    }
  }

  public function savePaymentSettings()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $formHandler = FormHandler::getInstance();
      $status = $formHandler->admin->savePaymentSetting($_REQUEST, $input);
      if (is_wp_error($status)) {
        wp_send_json_error($status->get_error_message(), 411);
      } else {
        wp_send_json_success($status, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function getPodsField()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $podsAdminExists = is_plugin_active('pods/init.php');

      $podField = [];
      if ($podsAdminExists) {
        $pods = pods($input->pod_type);
        $i = 0;
        foreach ($pods->fields as $field) {
          $i++;
          $podField[$i]['key'] = $field['name'];
          $podField[$i]['name'] = $field['label'];
          $podField[$i]['is-repeatable'] = $field['repeatable'] ?? 0;
          $podField[$i]['required'] = 1 === $field['options']['required'] ? true : false;
        }
      }

      if (is_wp_error($podField)) {
        wp_send_json_error($podField, 411);
      } else {
        wp_send_json_success($podField, 200);
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function getPodsType()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $users = get_users(['fields' => ['ID', 'display_name']]);
      $pods = [];
      $podsAdminExists = is_plugin_active('pods/init.php');
      if ($podsAdminExists) {
        $allPods = pods_api()->load_pods();
        foreach ($allPods as $key => $pod) {
          $pods[$key]['name'] = $pod['name'];
          $pods[$key]['label'] = $pod['label'];
        }
      }
      $data = ['users' => $users, 'post_types' => $pods];
      wp_send_json_success($data, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function postTypeByUser()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $users = get_users(
        [
          'fields' => ['ID', 'display_name', 'user_login', 'user_email', 'user_nicename'],
        ]
      );

      $postTypes = $this->getPostTypes();

      $data = ['post_types' => $postTypes, 'users' => $users];
      wp_send_json_success($data, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bitformpro'
        ),
        401
      );
    }
  }

  private function getPostTypes()
  {
    $all_cpt = get_post_types([
      'public'              => true,
      'exclude_from_search' => false,
      '_builtin'            => false,
      'capability_type'     => 'post',

    ], 'objects');
    $cpt = [];

    foreach ($all_cpt as $key => $post_type) {
      $cpt[$key]['name'] = $post_type->name;
      $cpt[$key]['label'] = $post_type->label;
    }
    $wp_post_types = get_post_types([
      'public'   => true,
      '_builtin' => true,
    ]);

    $wp_all_post_types = [];

    foreach ($wp_post_types as $key => $post_type) {
      if ('attachment' !== $post_type) {
        $wp_all_post_types[$key]['name'] = $post_type;
        $wp_all_post_types[$key]['label'] = ucwords($post_type);
      }
    }
    return array_merge($wp_all_post_types, $cpt);
  }

  public function getAcfGroupFields()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $acfFields = [];
      $types = ['select', 'checkbox', 'radio'];

      $field_groups = get_posts(['post_type' => 'acf-field-group']);

      if ($field_groups) {
        $groups = acf_get_field_groups();
        foreach ($groups as $group) {
          foreach (acf_get_fields($group['key']) as $acfField) {
            if (in_array($acfField['type'], $types)) {
              array_push($acfFields, [
                'key'         => $acfField['key'],
                'name'        => $acfField['label'],
                'choices'     => $acfField['choices'],
                'group_title' => $group['title'],
                'location'    => $group['location'],
              ]);
            }
          }
        }
      }

      wp_send_json_success($acfFields, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function getCustomField()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $input = json_decode($inputJSON);
      $acfFields = [];
      $acfFiles = [];

      $allowedFields = [
        'repeater',
        'text',
        'textarea',
        'password',
        'wysiwyg',
        'number',
        'radio',
        'color_picker',
        'oembed',
        'email',
        'url',
        'date_picker',
        'true_false',
        'date_time_picker',
        'time_picker',
        'message',
        'checkbox',
        'select',
        'post_object',
        'user',
        'file',
        'image',
        'gallery'
      ];

      $field_groups = get_posts(['post_type' => 'acf-field-group']);

      if ($field_groups) {
        $groups = acf_get_field_groups(['post_type' => $input->post_type]);

        foreach ($groups as $group) {
          foreach (acf_get_fields($group['key']) as $acfField) {
            if (in_array($acfField['type'], $allowedFields)) {
              if ('repeater' === $acfField['type']) {
                foreach ($acfField['sub_fields'] as $subField) {
                  if (in_array($subField['type'], $allowedFields)) {
                    array_push($acfFields, [
                      'key'      => $acfField['key'] . '.' . $subField['key'],
                      'name'     => $acfField['label'] . '-' . $subField['label'],
                      'required' => $subField['required'],
                    ]);
                  }
                }
              } elseif (in_array($acfField['type'], ['file', 'image', 'gallery'])) {
                array_push($acfFiles, [
                  'key'      => $acfField['key'],
                  'name'     => $acfField['label'],
                  'required' => $acfField['required'],
                ]);
              } else {
                array_push($acfFields, [
                  'key'      => $acfField['key'],
                  'name'     => $acfField['label'],
                  'required' => $acfField['required'],
                ]);
              }
            }
          }
        }
      }

      wp_send_json_success(['acfFields' => $acfFields, 'acfFile' => $acfFiles], 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public function getMetaBoxFields()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      if (!function_exists('rwmb_meta')) {
        wp_send_json_error(__('Meta Box must be activated!', 'bit-integrations'));
      }

      $input = json_decode(file_get_contents('php://input'));

      $metaBoxFields = rwmb_get_object_fields($input->post_type);

      $metaBoxFields = MetaBoxService::getMetaBoxFields($input->post_type);

      wp_send_json_success(
        [
          'metaboxFields' => array_values($metaBoxFields['text_fields']),
          'metaboxFile'   => array_values($metaBoxFields['file_fields']),
        ],
        200
      );
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bitformpro'
        ),
        401
      );
    }
  }

  public function saveConversationalCSS()
  {
    if (wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $requestsParams = json_decode($inputJSON);
      $formId = sanitize_text_field($requestsParams->formID);
      $css = $requestsParams->css;

      if (filter_var($formId, FILTER_VALIDATE_INT)) {
        $path = 'form-styles';
        $fileName = "bitform-conversational-$formId.css";
        FrontEndScriptGenerator::customCodeFileSaveOrDelete($css, $path, $fileName);
        wp_send_json_success(
          __(
            'Conversational CSS Saved Successfully!',
            'bitform'
          ),
          200
        );
      } else {
        wp_send_json_error(
          __(
            'Invalid form id',
            'bitform'
          ),
          401
        );
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bitform'
        ),
        401
      );
    }
  }
}
