<?php

/**
 * Handle Form Create,Update,delete Operation
 *
 */

namespace BitCode\BitForm\Admin\Form;

use BitCode\BitForm\Admin\Form\Template\TemplateProvider;
use BitCode\BitForm\Core\Database\EmailTemplateModel;
use BitCode\BitForm\Core\Database\FormEntryLogModel;
use BitCode\BitForm\Core\Database\FormEntryMetaModel;
use BitCode\BitForm\Core\Database\FormEntryModel;
use BitCode\BitForm\Core\Database\FormModel;
use BitCode\BitForm\Core\Database\IntegrationModel;
use BitCode\BitForm\Core\Database\PdfTemplateModel;
use BitCode\BitForm\Core\Database\ReportsModel;
use BitCode\BitForm\Core\Database\SuccessMessageModel;
use BitCode\BitForm\Core\Database\WorkFlowModel;
use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Messages\EmailTemplateHandler;
use BitCode\BitForm\Core\Messages\PdfTemplateHandler;
use BitCode\BitForm\Core\Messages\SuccessMessageHandler;
use BitCode\BitForm\Core\Migration\MigrationHelper;
use BitCode\BitForm\Core\Util\FileHandler;
use BitCode\BitForm\Core\Util\FormDuplicateHelper;
use BitCode\BitForm\Core\Util\HttpHelper;
use BitCode\BitForm\Core\Util\IpTool;
use BitCode\BitForm\Core\Util\Utilities;
use BitCode\BitForm\Core\WorkFlow\WorkFlow;
use BitCode\BitForm\Core\WorkFlow\WorkFlowHandler;
use WP_Error;

class AdminFormHandler
{
  private static $formModel;
  private static $ipTool;

  public function __construct()
  {
    static::$formModel = new FormModel();
    static::$ipTool = new IpTool();
  }

  public function getTemplate($Request, $post)
  {
    $templateName = null;
    $newFormId = null;
    if (isset($post->template)) {
      $templateName = $post->template;
      $newFormId = $post->newFormId;
    }

    $templateProvider = new TemplateProvider();
    $form_content_raw = $templateProvider->getTemplate($templateName, $newFormId);
    if (!$form_content_raw) {
      return new WP_Error('template_empty', __('Template not found, by this name: ', 'bit-form') . $templateName);
    } else {
      return wp_json_encode(
        [
          'id'           => 0,
          'form_content' => $form_content_raw,
        ]
      );
    }
  }

  public function saveStyleSheet($styles, $sheetName)
  {
    $this->createFormStylesDirIfNotExists();

    if (
      file_exists(BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-styles')
      && is_writable(BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-styles')
    ) {
      $createStyleFile = fopen(BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-styles' . DIRECTORY_SEPARATOR . $sheetName, 'w');
      fwrite($createStyleFile, $styles);
      fclose($createStyleFile);
      return true;
    }
    return false;
  }

  public function saveLayoutStyleSheet($layout, $sheetName, $lay_row_height = 43)
  {
    $md_width = 600;
    $sm_width = 400;
    $lg_styles = '';
    $md_styles = '';
    $sm_styles = '';
    for ($i = 0; $i < count($layout->lg); $i++) {
      $fld = $layout->lg[$i];

      if ($fld->w < 10) {
        $lay_row_height = 43;
      } else {
        $lay_row_height = 2;
      }

      $class = $fld->i;
      $lg_g_r_s = $fld->y + 1;
      $lg_g_c_s = $fld->x + 1;
      $lg_g_r_e = 1 !== $fld->y ? $fld->h + ($fld->y + 1) : 1;
      $lg_g_c_e = ($fld->x + 1) + $fld->w;
      $lg_g_r_span = $lg_g_r_e - $lg_g_r_s;
      $lg_g_c_span = $lg_g_c_e - $lg_g_c_s;
      $lg_min_height = $fld->h * $lay_row_height . 'px;';

      $lg_styles .= ".$class { grid-area: $lg_g_r_s / $lg_g_c_s / $lg_g_r_e / $lg_g_c_e ; -ms-grid-row: $lg_g_r_s; -ms-grid-row-span: $lg_g_r_span; -ms-grid-column: $lg_g_c_s; -ms-grid-column-span: $lg_g_c_span;  min-height: $lg_min_height; }";

      $fld = $layout->md[$i];

      $class = $fld->i;
      $md_g_r_s = $fld->y + 1;
      $md_g_c_s = $fld->x + 1;
      $md_g_r_e = 1 !== $fld->y ? $fld->h + ($fld->y + 1) : 1;
      $md_g_c_e = ($fld->x + 1) + $fld->w;
      $md_g_r_span = $lg_g_r_e - $md_g_r_s;
      $md_g_c_span = $lg_g_c_e - $md_g_c_s;
      $md_min_height = $fld->h * $lay_row_height . 'px;';

      $md_styles .= ".$class { grid-area: $md_g_r_s / $md_g_c_s / $md_g_r_e / $md_g_c_e ; -ms-grid-row: $md_g_r_s; -ms-grid-row-span: $md_g_r_span; -ms-grid-column: $md_g_c_s; -ms-grid-column-span: $md_g_c_span;  min-height: $md_min_height; }";

      $fld = $layout->sm[$i];

      $class = $fld->i;
      $sm_g_r_s = $fld->y + 1;
      $sm_g_c_s = $fld->x + 1;
      $sm_g_r_e = 1 !== $fld->y ? $fld->h + ($fld->y + 1) : 1;
      $sm_g_c_e = ($fld->x + 1) + $fld->w;
      $sm_g_r_span = $sm_g_r_e - $sm_g_r_s;
      $sm_g_c_span = $sm_g_c_e - $sm_g_c_s;
      $sm_min_height = $fld->h * $lay_row_height . 'px;';

      $sm_styles .= ".$class { grid-area: $sm_g_r_s / $sm_g_c_s / $sm_g_r_e / $sm_g_c_e ; -ms-grid-row: $sm_g_r_s; -ms-grid-row-span: $sm_g_r_span; -ms-grid-column: $sm_g_c_s; -ms-grid-column-span: $sm_g_c_span;  min-height: $sm_min_height; }";
    }
    $formStyle = "._frm-g{
display: grid;
display: -ms-grid;
grid-template-columns: repeat( 6 , minmax( 30px , 1fr ));
-ms-grid-columns: minmax( 30px , 1fr ) minmax( 30px , 1fr ) minmax( 30px , 1fr ) minmax( 30px , 1fr ) minmax( 30px , 1fr ) minmax( 30px , 1fr );
}
{$lg_styles}
@media only screen and (max-width:{$md_width}px) {
._frm-g {grid-template-columns: repeat( 4 , minmax( 30px , 1fr ));-ms-grid-columns: minmax( 30px , 1fr ) minmax( 30px , 1fr ) minmax( 30px , 1fr ) minmax( 30px , 1fr );}
{$md_styles}
}
@media only screen and (max-width:{$sm_width}px) {
._frm-g {grid-template-columns: repeat( 2 , minmax( 30px , 1fr ));-ms-grid-columns: minmax( 30px , 1fr ) minmax( 30px , 1fr );}
{$sm_styles}
}";

    $this->createFormStylesDirIfNotExists();

    if (
      file_exists(BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-styles')
      && is_writable(BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-styles')
    ) {
      $createStyleFile = fopen(BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-styles' . DIRECTORY_SEPARATOR . $sheetName, 'w');
      fwrite($createStyleFile, $formStyle);
      fclose($createStyleFile);
      return true;
    }
    return false;
  }

  public function createFormStylesDirIfNotExists()
  {
    if (!file_exists(BITFORMS_UPLOAD_DIR)) {
      wp_mkdir_p(BITFORMS_UPLOAD_DIR);
    }
    if (!file_exists(BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-styles')) {
      wp_mkdir_p(BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-styles');
    }
  }

  private function pdfFontDownload($fontName)
  {
    if (class_exists('\BitCode\BitFormPro\Admin\AppSetting\Pdf')) {
      \BitCode\BitFormPro\Admin\AppSetting\Pdf::getInstance()->fontsDownload($fontName);
    }
    return false;
  }

  public function createNewForm($Request, $post)
  {
    // wp_send_json_success($post);
    // exit;
    if (!isset($post->form_id) || '' === $post->form_id) {
      return new WP_Error('empty_form', __('Error Occurred, Please Reload', 'bit-form'));
    }

    $formManager = new AdminFormManager($post->form_id);
    if ($formManager->isExist()) {
      return new WP_Error('empty_form', __('Error Occurred, Please Reload', 'bit-form'));
    }

    if (!class_exists('BitCode\\BitFormPro\\Plugin')) {
      if (!empty($workFlows) && count($workFlows) > 2) {
        return new WP_Error(
          'free_limit',
          __('You are allowed to add maximum 2 workflows ', 'bit-form')
        );
      }
    }

    if (isset($post->formStyle) && $post->formStyle) {
      $sheetName = 'bitform-' . $post->form_id . '.css';
      $this->saveStyleSheet($post->formStyle, $sheetName);
    }

    if (isset($post->layoutChanged) && $post->layoutChanged) {
      $sheetName = 'bitform-layout-' . $post->form_id . '.css';
      $rowHeight = (int)$post->rowHeight;
      $this->saveLayoutStyleSheet($post->layout, $sheetName, $rowHeight);
    }

    $fields = wp_unslash($post->fields);
    // $fields = $post->fields;
    $layout = wp_unslash($post->layout);
    $nestedLayout = isset($post->nestedLayouts) ? wp_unslash($post->nestedLayouts) : (object) [];
    $formInfo = wp_unslash($post->formInfo);
    $form_name = wp_unslash($post->form_name);
    $form_name = strlen($form_name) > 50 ? substr($form_name, 0, 50) : $form_name;
    $formSettings = (object) wp_unslash($post->formSettings);
    $atomic_class_map = isset($post->atomicClassMap) ? wp_unslash($post->atomicClassMap) : [];
    $breakpointSize = wp_unslash($post->breakpointSize);
    $style = $post->style;
    $themeVars = $post->themeVars;
    $themeColors = $post->themeColors;
    $staticStyles = $post->staticStyles;
    $workFlows = wp_unslash($post->workFlows);
    $additional = wp_unslash($post->additional);
    $reports = !empty($post->reports) ? $post->reports : (object) [];
    $builderSettings = (object) isset($post->builderSettings) ? wp_unslash($post->builderSettings) : [];

    $mailTem = $formSettings->mailTem;
    $pdfTem = $formSettings->pdfTem;
    $integrations = $formSettings->integrations;
    $formPermissions = $formSettings->formPermissions;

    $user_details = static::$ipTool->getUserDetail();
    if (empty($fields) || empty($layout)) {
      return new WP_Error(
        'empty_form',
        __('Can not save empty form.', 'bit-form')
      );
    }
    if (strlen($form_name) > 50) {
      return new WP_Error(
        'empty_form',
        __('Form Name Should Be Within 50 Characters', 'bit-form')
      );
    }
    $form_content = [
      'fields'       => $fields,
      'layout'       => $layout,
      'nestedLayout' => $nestedLayout,
      'formInfo'     => $formInfo,
    ];

    /* if (!empty($formSettings->submitBtn)) {
        $form_content = array_merge($form_content, array('buttons' => $formSettings->submitBtn));
    } */
    if (!empty($formSettings->theme)) {
      $form_content = array_merge($form_content, ['theme' => $formSettings->theme]);
    }

    if (!empty($formSettings->formPermissions)) {
      $form_content = array_merge($form_content, ['formPermissions' => $formSettings->formPermissions]);
    }

    if (!empty($additional)) {
      $form_content = array_merge($form_content, ['additional' => $additional]);
    }
    if (!empty($workFlows)) {
      $workflows = is_string($workFlows) ? json_decode($workFlows) : $workFlows;
      $workFlowExist = [];
      foreach ($workflows as $index => $workFlow) {
        if (in_array($workFlow->action_type, ['always', 'onload'])) {
          $workFlowExist['onload'] = true;
        }
        if ('oninput' === $workFlow->action_type || ('always' === $workFlow->action_type && 'cond' === $workFlow->action_behaviour)) {
          $workFlowExist['oninput'] = true;
        }
      }
      $form_content = array_merge($form_content, ['workFlowExist' => $workFlowExist]);
    }
    $form_content = \wp_json_encode($form_content);

    $builder_helper_state = (object) [
      'style'           => $style,
      'staticStyles'    => $staticStyles,
      'breakpointSize'  => $breakpointSize,
      'themeVars'       => $themeVars,
      'themeColors'     => $themeColors,
      'builderSettings' => $builderSettings
    ];
    $atomic_class_map = \wp_json_encode($atomic_class_map);
    $fdata = [
      'id'                        => $post->form_id,
      'form_content'              => $form_content,
      'builder_helper_state'      => \wp_json_encode($builder_helper_state),
      'atomic_class_map'          => $atomic_class_map,
      'generated_script_page_ids' => \wp_json_encode((object) []),
      'user_id'                   => $user_details['id'],
      'user_ip'                   => $user_details['ip'],
      'user_device'               => $user_details['device'],
      'status'                    => 1,
      'form_name'                 => $form_name,
      'created_at'                => $user_details['time'],
      'updated_at'                => $user_details['time'],
    ];

    $save_status = static::$formModel->insert($fdata);

    if (is_wp_error($save_status)) {
      return $save_status;
    } elseif (!$save_status) {
      return false;
    } else {
      wp_mkdir_p(BITFORMS_UPLOAD_DIR . "/$save_status");
      $formID = $save_status;
      $integartionIDForWorkflow = [];
      // Confiramtion Message [start]
      if (!empty($formSettings->confirmation->type->successMsg)) {
        $successMessages = $formSettings->confirmation->type->successMsg;
        $successMessageHandler = new SuccessMessageHandler($formID, $user_details);
        foreach ($successMessages as $messageKey => $messageDetail) {
          $savedID = $successMessageHandler->saveMessage($messageDetail);
          if ($savedID) {
            $msgIdx = empty($messageDetail->id) ? $messageKey : wp_json_encode(['id' => $messageDetail->id]);
            $integartionIDForWorkflow['successMsg'][$msgIdx] = $savedID;
            $messageDetail->id = $savedID;
            $style = $this->updateSuccessMessageClassName($style, $messageKey, $savedID);
            $atomic_class_map = $this->updateSuccessMessageClassName($atomic_class_map, $messageKey, $savedID);
          }
        }
        $builder_helper_state = (object) [
          'style'           => $style,
          'breakpointSize'  => $breakpointSize,
          'themeVars'       => $themeVars,
          'themeColors'     => $themeColors,
          'builderSettings' => $builderSettings
        ];
        $update_status = static::$formModel->update(
          [
            'builder_helper_state'   => \wp_json_encode($builder_helper_state),
            'atomic_class_map'       => $atomic_class_map,
          ],
          [
            'id' => $formID,
          ]
        );
        if (is_wp_error($update_status)) {
          return $update_status;
        }
        unset($formSettings->confirmation->type->successMsg);
      }
      // return $formSettings;
      // Confiramtion Message [end] */
      // Email Template [start]
      if (!empty($mailTem)) {
        $emailTemplateHandler
          = new EmailTemplateHandler($formID, $user_details);
        foreach ($mailTem as $templateKey => $templateDetail) {
          $savedID = $emailTemplateHandler->saveTemplate($templateDetail);
          if ($savedID) {
            $tempIdx = empty($templateDetail->id) ? $templateKey : wp_json_encode(['id' => $templateDetail->id]);
            $integartionIDForWorkflow['mailNotify'][$tempIdx] = $savedID;
          }
        }
      }
      // Email Template [end] */

      // PDF Template [start]
      if (!empty($pdfTem)) {
        $pdfTemplateHandler = new PdfTemplateHandler($formID);
        $workFlowString = '';
        if (!empty($workFlows)) {
          $workFlowString = wp_json_encode($workFlows);
        }
        foreach ($pdfTem as $templateDetail) {
          $savedID = $pdfTemplateHandler->save($templateDetail);

          $pdfSetting = $templateDetail->setting;

          if (isset($pdfSetting->font->name) && !empty($pdfSetting->font->name)) {
            $this->pdfFontDownload($pdfSetting->font->name);
          }
          if (!empty($workFlowString)) {
            $workFlowString = str_replace('"pdfId":"{\"id\":\"' . $templateDetail->id . '\"}"', '"pdfId":"{\"id\":\"' . $savedID . '\"}"', $workFlowString);
          }
        }
        if (!empty($workFlowString) && !empty($workFlows)) {
          $workFlows = json_decode($workFlowString);
        }
      }
      // PDF Template [end] */
      //Integration [start] */
      $integrationHandler = new IntegrationHandler($formID, $user_details);
      if (!empty($formSettings->confirmation->type)) {
        $allIntegrations = $formSettings->confirmation->type;
        foreach ($allIntegrations as $integrationType => $integrationGroup) {
          foreach ($integrationGroup as $singleIntegrationKey => $singleIntegrationDetail) {
            if (empty($singleIntegrationDetail->details)) {
              $integrationName = $singleIntegrationDetail->title;
              unset($singleIntegrationDetail->title, $singleIntegrationDetail->id);

              $integrationDetails = !is_string($singleIntegrationDetail) ?
                wp_json_encode($singleIntegrationDetail) : $singleIntegrationDetail;
            } else {
              $integrationName = $singleIntegrationDetail->title;
              $integrationDetails = !is_string($singleIntegrationDetail->details) ?
                wp_json_encode($singleIntegrationDetail->details) : $singleIntegrationDetail->details;
            }
            $savedID = $integrationHandler->saveIntegration($integrationName, $integrationType, $integrationDetails, 'form');
            $integIdx = empty($singleIntegrationDetail->id) ? $savedID : wp_json_encode(['id' => $singleIntegrationDetail->id]);
            $integartionIDForWorkflow[$integrationType][$integIdx] = $savedID;
          }
        }
      }
      if (!empty($integrations)) {
        foreach ($integrations as $singleIntegrationKey => $singleIntegrationDetail) {
          $integrationName = $singleIntegrationDetail->name;
          unset($singleIntegrationDetail->name);
          $integrationType = $singleIntegrationDetail->type;
          unset($singleIntegrationDetail->type);
          $singleIntegrationDetailID = empty($singleIntegrationDetail->id) ? null : $singleIntegrationDetail->id;
          unset($singleIntegrationDetail->id);
          if (empty($singleIntegrationDetail->details)) {
            $integrationDetails = !is_string($singleIntegrationDetail) ?
              wp_json_encode($singleIntegrationDetail) : $singleIntegrationDetail;
          } else {
            $integrationDetails = !is_string($singleIntegrationDetail->details) ?
              wp_json_encode($singleIntegrationDetail->details) : $singleIntegrationDetail->details;
          }
          $savedID = $integrationHandler->saveIntegration($integrationName, $integrationType, $integrationDetails, 'form');
          $integIdx = empty($singleIntegrationDetailID) ? $singleIntegrationKey : wp_json_encode(['id' => $singleIntegrationDetailID]);
          $integartionIDForWorkflow['integ'][$integIdx] = $savedID;
        }
      }
      //Integrations [end]
      //wrokFlows [start]
      if (!empty($workFlows)) {
        $workFlowHandler = new WorkFlowHandler($formID, $user_details);

        foreach ($workFlows as $index => $workFlow) {
          $savedID = $workFlowHandler->saveworkFlow($workFlow, $integartionIDForWorkflow, $index);
          $newData['workflow'] = 1;
        }

        // $workflowCount = count($workFlows);
        // while ($workflowCount) {
        //     $workFlow = $workFlows[--$workflowCount];
        //     $savedID = $workFlowHandler->saveworkFlow($workFlow, $integartionIDForWorkflow);
        //     $newData['workflow'] = 1;
        // }
      }
      //wrokFlows [end]
      $details = [
        'report_name'   => 'All Entries',
        'hiddenColumns' => ['__user_id', '__user_ip', '__referer', '__user_device', '__created_at', '__updated_at'],
        'pageSize'      => 10,
        'sortBy'        => [],
        'filters'       => [],
        'globalFilter'  => '',
        'order'         => []
      ];
      $defaultReport = [
        'type'        => 'table',
        'category'    => 'form',
        'context'     => $formID,
        'details'     => wp_json_encode($details),
        'isDefault'   => (bool) 1,
        'user_id'     => $user_details['id'],
        'user_ip'     => $user_details['ip'],
        'user_device' => $user_details['device'],
        'created_at'  => $user_details['time'],
        'updated_at'  => $user_details['time'],
      ];
      $reportsModel = new ReportsModel();
      $saveReportId = $reportsModel->insert($defaultReport);

      // }
      $updated_data = $this->getAForm(['id' => $save_status], null);
      if (!is_wp_error($saveReportId) && isset($updated_data['form_content'])) {
        $updated_data['form_content']['report_id'] = $saveReportId;
        $updated_data['form_content']['is_default'] = 1;
      }
      $updated_data['message'] = __('Form Saved successfully', 'bit-form');

      return $updated_data;
    }
  }

  public function updateForm($Request, $post)
  {
    // return ['checked dta'=>$post];
    $formId = $post->id;
    // get the builder_helper_state from the form
    $builder_helper_state = self::$formModel->get(['builder_helper_state'], ['id' => $formId]);
    $builder_helper_state = (!is_wp_error($builder_helper_state) && 1 === count($builder_helper_state) && isset($builder_helper_state[0]->builder_helper_state)) ? $builder_helper_state[0]->builder_helper_state : '{}';
    $builder_helper_state = json_decode($builder_helper_state);
    if (property_exists($post, 'breakpointSize')) {
      $builder_helper_state->breakpointSize = $post->breakpointSize;
    }
    if (property_exists($post, 'style')) {
      $builder_helper_state->style = $post->style;
    }
    if (property_exists($post, 'staticStyles')) {
      $builder_helper_state->staticStyles = $post->staticStyles;
    }
    if (property_exists($post, 'themeVars')) {
      $builder_helper_state->themeVars = $post->themeVars;
    }
    if (property_exists($post, 'themeColors')) {
      $builder_helper_state->themeColors = $post->themeColors;
    }
    if (property_exists($post, 'builderSettings')) {
      $builder_helper_state->builderSettings = $post->builderSettings;
    }
    if (property_exists($post, 'atomicClassMap')) {
      $atomic_class_map = \wp_json_encode((object)$post->atomicClassMap);
    }

    if (property_exists($post, 'fields') && null !== $post->fields && property_exists($post, 'layout') && $post->layout) {
      $fields = wp_unslash($post->fields);
      $layout = wp_unslash($post->layout);
      $nestedLayout = wp_unslash($post->nestedLayouts);
      $formInfo = wp_unslash($post->formInfo);
      $formID = wp_unslash($post->id);
      $form_name = wp_unslash($post->form_name);
      $formSettings = wp_unslash($post->formSettings);
      $workFlows = wp_unslash($post->workFlows);
      $additional = wp_unslash($post->additional);
      $reports = wp_unslash($post->currentReport);
    }

    $mailTem = $formSettings->mailTem;
    $pdfTem = isset($formSettings->pdfTem) ? $formSettings->pdfTem : [];
    $integrations = $formSettings->integrations;

    $user_details = static::$ipTool->getUserDetail();

    if (empty($fields) || empty($layout) || is_null($formID)) {
      return new WP_Error('empty_form', 'Can not update empty form.');
    }

    if (!class_exists('BitCode\\BitFormPro\\Plugin')) {
      if (count($workFlows) > 2) {
        return new WP_Error(
          'free_limit',
          __('You are allowed to add maximum 2 workflows ', 'bit-form')
        );
      }
    }

    $form_content = [
      'fields'       => $fields,
      'layout'       => $layout,
      'nestedLayout' => $nestedLayout,
      'formInfo'     => $formInfo,
    ];

    if (isset($post->report_id)) {
      $form_content['report_id'] = wp_unslash($post->report_id);
    }

    /* if (!empty($formSettings->submitBtn)) {
        $form_content = array_merge($form_content, array('buttons' => $formSettings->submitBtn));
    } */
    if (!empty($formSettings->theme)) {
      $form_content = array_merge($form_content, ['theme' => $formSettings->theme]);
    }

    if (!empty($formSettings->formPermissions)) {
      $form_content = array_merge($form_content, ['formPermissions' => $formSettings->formPermissions]);
    }
    if (!empty($additional)) {
      $form_content = array_merge($form_content, ['additional' => $additional]);
    }
    $integartionIDForWorkflow = [];
    $newData['settingConfiramation'] = 0;
    $newData['integation'] = 0;
    $newData['mailTemplate'] = 0;
    $newData['workflow'] = 0;
    $newData['reports'] = 0;
    $newData['pdfTemplate'] = 0;
    // Confiramtion Message [start]
    if (!empty($formSettings->confirmation->type->successMsg)) {
      $successMessages = $formSettings->confirmation->type->successMsg;
      $successMessageHandler
        = new SuccessMessageHandler($formID, $user_details);
      foreach ($successMessages as $messageKey => $messageDetail) {
        if (empty($messageDetail->id)) {
          $savedID = $successMessageHandler->saveMessage($messageDetail);
          if ($savedID) {
            $newData['settingConfiramation'] = 1;
            $integartionIDForWorkflow['successMsg'][$messageKey] = $savedID;
            $messageDetail->id = $savedID;
            $builder_helper_state->style = $this->updateSuccessMessageClassName($builder_helper_state->style, $messageKey, $savedID);
            if (isset($atomic_class_map)) {
              $atomic_class_map = $this->updateSuccessMessageClassName($atomic_class_map, $messageKey, $savedID);
            }
          }
        } else {
          $successMessageHandler->updateMessage($messageDetail);
        }
      }
      unset($formSettings->confirmation->type->successMsg);
    }
    // return $formSettings;
    // Confirmation Message [end] */
    // Email Template [start]
    if (!empty($mailTem)) {
      $emailTemplateHandler
        = new EmailTemplateHandler($formID, $user_details);
      foreach ($mailTem as $templateKey => $templateDetail) {
        if (empty($templateDetail->id)) {
          $savedID = $emailTemplateHandler->saveTemplate($templateDetail);
          if (is_wp_error($savedID) && 'result_empty' === $savedID->get_error_code()) {
            $newData['mailTemplate'] = 2;
          } else {
            $newData['mailTemplate'] = 1;
            $integartionIDForWorkflow['mailTem'][$templateKey] = $savedID;
          }
        } else {
          $emailTemplateHandler->updateTemplate($templateDetail);
        }
      }
    }
    // return $formSettings;
    // Email Template [end] */
    if (!empty($pdfTem)) {
      $pdfTemplateHandler = new PdfTemplateHandler($formID);

      foreach ($pdfTem as $templateKey => $templateDetail) {
        if (isset($templateDetail->setting->font->name)) {
          $this->pdfFontDownload($templateDetail->setting->font->name);
        }

        $savedID = $pdfTemplateHandler->saveOrUpdate($templateDetail);
        if (is_wp_error($savedID) && 'result_empty' === $savedID->get_error_code()) {
          $newData['pdfTemplate'] = 2;
        } else {
          $newData['pdfTemplate'] = 1;
          $integartionIDForWorkflow['pdfTem'][$templateKey] = $savedID;
        }
      }
    }
    //Integration [start] */
    $integrationHandler = new IntegrationHandler($formID, $user_details);
    if (!empty($formSettings->confirmation->type)) {
      $allIntegrations = $formSettings->confirmation->type;
      foreach ($allIntegrations as $integrationType => $integrationGroup) {
        foreach ($integrationGroup as $singleIntegrationKey => $singleIntegrationDetail) {
          if (empty($singleIntegrationDetail->details)) {
            $integrationName = $singleIntegrationDetail->title;
            unset($singleIntegrationDetail->title);
            $singleIntegrationDetailID = empty($singleIntegrationDetail->id) ? null : $singleIntegrationDetail->id;
            unset($singleIntegrationDetail->id);
            $integrationDetails = !is_string($singleIntegrationDetail) ?
              wp_json_encode($singleIntegrationDetail) : $singleIntegrationDetail;
          } else {
            $singleIntegrationDetailID = empty($singleIntegrationDetail->id) ? null : $singleIntegrationDetail->id;
            unset($singleIntegrationDetail->id);
            $integrationName = $singleIntegrationDetail->title;
            $integrationDetails = !is_string($singleIntegrationDetail->details) ?
              wp_json_encode($singleIntegrationDetail->details) : $singleIntegrationDetail->details;
          }
          if (empty($singleIntegrationDetailID)) {
            $savedID = $integrationHandler->saveIntegration($integrationName, $integrationType, $integrationDetails, 'form');
            $newData['settingConfiramation'] = 1;
            $integartionIDForWorkflow[$integrationType][$singleIntegrationKey] = $savedID;
          } else {
            $integrationHandler->updateIntegration($singleIntegrationDetailID, $integrationName, $integrationType, $integrationDetails, 'form');
          }
        }
      }
    }
    if (!empty($integrations)) {
      foreach ($integrations as $singleIntegrationKey => $singleIntegrationDetail) {
        $integrationName = $singleIntegrationDetail->name;
        unset($singleIntegrationDetail->name);
        $integrationType = $singleIntegrationDetail->type;
        unset($singleIntegrationDetail->type);
        $singleIntegrationDetailID = empty($singleIntegrationDetail->id) ? null : $singleIntegrationDetail->id;
        unset($singleIntegrationDetail->id);
        if (empty($singleIntegrationDetail->details)) {
          $integrationDetails = !is_string($singleIntegrationDetail) ?
            wp_json_encode($singleIntegrationDetail) : $singleIntegrationDetail;
        } else {
          $integrationDetails = !is_string($singleIntegrationDetail->details) ?
            wp_json_encode($singleIntegrationDetail->details) : $singleIntegrationDetail->details;
        }
        if (empty($singleIntegrationDetailID)) {
          $savedID = $integrationHandler->saveIntegration($integrationName, $integrationType, $integrationDetails, 'form');
          $integartionIDForWorkflow[$integrationType][$singleIntegrationKey] = $savedID;
          if (is_wp_error($savedID) && 'result_empty' !== $savedID->get_error_code()) {
            $newData['integation'] = 2;
          } else {
            $newData['integation'] = 1;
          }
        } else {
          $integrationHandler->updateIntegration($singleIntegrationDetailID, $integrationName, $integrationType, $integrationDetails, 'form');
        }
      }
    }
    //Integrations [end]
    //workFlows [start] */
    $workFlowExist = [];
    if (!empty($workFlows)) {
      $workFlowHandler = new WorkFlowHandler($formID, $user_details);
      // foreach ($workFlows as $workFlowIndex => $workFlow) {
      $workflows = is_string($workFlows) ? json_decode($workFlows) : $workFlows;

      foreach ($workflows as $index => $workFlow) {
        if (in_array($workFlow->action_type, ['always', 'onload'])) {
          $workFlowExist['onload'] = true;
        }
        if ('oninput' === $workFlow->action_type || ('always' === $workFlow->action_type && 'cond' === $workFlow->action_behaviour)) {
          $workFlowExist['oninput'] = true;
        }
        // $integartionIDForWorkflow = apply_filters('bitform_filter_integration_id', $workFlow, $formID, $index);
        if (empty($workFlow->id)) {
          $savedID = $workFlowHandler->saveworkFlow($workFlow, $integartionIDForWorkflow, $index);
          if (is_wp_error($savedID) && 'result_empty' !== $savedID->get_error_code()) {
            $newData['workflow'] = 2;
          } else {
            $newData['workflow'] = 1;
          }
        } else {
          $newData = $workFlowHandler->updateworkFlow($workFlow->id, $workFlow, $integartionIDForWorkflow, $index, $newData);
        }
      }
    }
    $form_content = array_merge($form_content, ['workFlowExist' => $workFlowExist]);
    //wrokFlows [end]
    //reports [start] */
    if (!empty($reports)) {
      $reportsModel = new ReportsModel();
      $fieldNames = [];
      foreach ($fields as $key => $field) {
        if (!empty($field->lbl)) {
          // $name = preg_replace('/[\`\~\!\@\#\$\'\.\s\?\+\-\*\&\|\/\\\!]/', '_', $field->lbl);
          $fieldNames["$key"] = $field->lbl;
        }
      }

      $fieldNames['__user_id'] = __('User', 'bit-form');
      $fieldNames['__user_ip'] = __('IP address', 'bit-form');
      // $fieldNames['__user_location'] = __('');
      $fieldNames['__referer'] = __('Refer URL');
      $fieldNames['__user_device'] = __('Device');
      $fieldNames['__created_at'] = __('Created Time');
      $fieldNames['__updated_at'] = __('Modified Time');

      $reportIsDefault = null;
      if (isset($form_content['report_id'])) {
        $validDateReport = $reportsModel->validateReportFields($reports, $fieldNames);
        if (isset($reports->isDefault)) {
          $reportIsDefault = $reports->isDefault;
        } else {
          $reportIsDefault = 0;
        }

        $reportsModel->update(
          [
            'type'        => 'table',
            'category'    => 'form',
            'context'     => $formID,
            'details'     => is_string($validDateReport) ? $validDateReport : wp_json_encode($validDateReport),
            'isDefault'   => (int) $reportIsDefault,
            'user_id'     => $user_details['id'],
            'user_ip'     => $user_details['ip'],
            'user_device' => $user_details['device'],
            'updated_at'  => $user_details['time'],
          ],
          [
            'id' => $form_content['report_id'],
          ]
        );
      }
    }
    //reports [end]
    //form abandonment [start]
    if (!empty($formSettings->formAbandonment)) {
      $formAbandonment = $formSettings->formAbandonment;
      // search for existing form abandonment in integration table
      $abandonmentInteg = $integrationHandler->getAllIntegration('formAbandonment', 'formAbandonment');
      if (!is_wp_error($abandonmentInteg) && !empty($abandonmentInteg)) {
        $abandonmentInteg = $abandonmentInteg[0];
      }
      if (isset($abandonmentInteg->id)) {
        $abandonmentIntegID = $abandonmentInteg->id;
        $abandonmentIntegDetails = wp_json_encode($formAbandonment);
        $integrationHandler->updateIntegration($abandonmentIntegID, 'formAbandonment', 'formAbandonment', $abandonmentIntegDetails, 'formAbandonment');
      } else {
        $abandonmentIntegDetails = wp_json_encode($formAbandonment);
        $integrationHandler->saveIntegration('formAbandonment', 'formAbandonment', $abandonmentIntegDetails, 'formAbandonment');
      }
    }
    //form abandonment [end]
    $form_content = wp_json_encode($form_content);
    $updateData = [
      'form_name'                 => $form_name,
      'form_content'              => $form_content,
      'user_id'                   => $user_details['id'],
      'user_ip'                   => $user_details['ip'],
      'builder_helper_state'      => \wp_json_encode((object)$builder_helper_state),
      'generated_script_page_ids' => \wp_json_encode((object) []),
      'user_device'               => $user_details['device'],
      'updated_at'                => $user_details['time'],
    ];
    if (isset($atomic_class_map)) {
      $updateData['atomic_class_map'] = $atomic_class_map;
    }
    $formUpdateVersion = get_option('bit-form_form_update_version');
    if (!$formUpdateVersion) {
      $formUpdateVersion = 1;
    } else {
      $formUpdateVersion = (int) $formUpdateVersion + 1;
    }
    update_option('bit-form_form_update_version', $formUpdateVersion);

    $update_status = static::$formModel->update(
      $updateData,
      [
        'id' => $formID,
      ]
    );
    if (is_wp_error($update_status)) {
      return $update_status;
    } elseif (!$update_status) {
      return new WP_Error('empty_form', __('Form update failed.', 'bit-form'));
    } else {
      if (isset($post->deletedFldKey) && is_array($post->deletedFldKey)) {
        $this->setEmptyMetaValue($post->deletedFldKey);
        unset($post->deletedFldKey);
      }

      $updated_data = $this->getAForm(['id' => $formID], null);

      if (0 === $newData['settingConfiramation'] && 0 === $newData['mailTemplate'] && 0 === $newData['integation'] && 0 === $newData['pdfTemplate']) {
        unset($updated_data['formSettings']);
      }
      if (0 === $newData['integation']) {
        unset($updated_data['integrations']);
      } elseif (2 === $newData['integation']) {
        $errorIN = '';
        $errorIN .= empty($errorIN) ? 'integation' : ', integation';
      }
      if (0 === $newData['mailTemplate']) {
        unset($updated_data['mailTem']);
      } elseif (2 === $newData['mailTemplate']) {
        $errorIN .= empty($errorIN) ? 'mailTemplate' : ', mailTemplate';
      }
      if (0 === $newData['pdfTemplate']) {
        unset($updated_data['pdfTem']);
      } elseif (2 === $newData['pdfTemplate']) {
        $errorIN .= empty($errorIN) ? 'pdfTemplate' : ', pdfTemplate';
      }
      if (0 === $newData['workflow']) {
        unset($updated_data['workFlows']);
      } elseif (2 === $newData['workflow']) {
        $errorIN .= empty($errorIN) ? 'workflow' : ', workflow';
      }
      if (2 === $newData['reports']) {
        $errorIN .= empty($errorIN) ? 'reports' : ', reports';
      }
      if (!empty($errorIN)) {
        $updated_data['message'] = sprintf(__('Error Occured in saving %s', 'bit-form'), $errorIN);
        return new WP_Error('Form update Error.', $updated_data);
      }
      if (null !== $reportIsDefault) {
        $updated_data['form_content']['is_default'] = $reportIsDefault;
      }
      $updated_data['message'] = __('Form updated successfully.', 'bitform');
      return $updated_data;
    }
  }

  public function setEmptyMetaValue($fieldkeys)
  {
    $sqlEscaped = array_map(function ($fld) {
      return "'" . esc_sql($fld) . "'";
    }, $fieldkeys);
    $deletedFldKey = implode(',', $sqlEscaped);
    global $wpdb;
    $tablename = $wpdb->prefix . 'bitforms_form_entrymeta';
    $sql = $wpdb->prepare("UPDATE $tablename SET meta_value = %s WHERE meta_key IN ( " . $deletedFldKey . ')', '');
    $wpdb->query($sql);
  }

  public function changeFormStatus($Request, $post)
  {
    if (isset($Request['status']) && $Request['id']) {
      $status = wp_unslash($Request['status']);
      $id = wp_unslash($Request['id']);
    } else {
      $status = wp_unslash($post->status);
      $id = wp_unslash($post->id);
    }
    $user_details = static::$ipTool->getUserDetail();
    // return $post->fields;
    $status = 'true' === $status || true === $status ? true : false;
    if (!is_bool($status) || is_null($id)) {
      // echo $status;
      return new WP_Error('status_change', __('Form status change failed.', 'bit-form'));
    }
    $update_status = static::$formModel->update(
      [
        'user_id'     => $user_details['id'],
        'user_ip'     => $user_details['ip'],
        'user_device' => $user_details['device'],
        'status'      => $status ? 1 : 0,
        'updated_at'  => $user_details['time'],
      ],
      [
        'id' => $id,
      ]
    );
    if (is_wp_error($update_status)) {
      return $update_status;
    } elseif (!$update_status) {
      return false;
    } else {
      return __('Form status changed successfully', 'bit-form');
    }
  }

  public function changeBulkFormStatus($Request, $post)
  {
    if (isset($Request['status']) && $Request['formID']) {
      $status = wp_unslash($Request['status']);
      $formID = wp_unslash($Request['formID']);
    } else {
      $status = wp_unslash($post->status);
      $formID = wp_unslash($post->formID);
    }
    $user_details = static::$ipTool->getUserDetail();
    // return $post->fields;
    $status = 'true' === $status || true === $status ? true : false;
    if (!is_bool($status) || is_null($formID) || !is_array($formID)) {
      // echo $status;
      return new WP_Error('status_change', __('Form status change failed.', 'bit-form'));
    }
    $update_status = static::$formModel->bulkUpdate(
      [
        'user_id'     => $user_details['id'],
        'user_ip'     => $user_details['ip'],
        'user_device' => $user_details['device'],
        'status'      => $status ? 1 : 0,
        'updated_at'  => $user_details['time'],
      ],
      [
        'id' => $formID,
      ]
    );
    if (is_wp_error($update_status)) {
      return $update_status;
    } elseif (!$update_status) {
      return false;
    } else {
      return __('Form status changed successfully', 'bit-form');
    }
  }

  public function getAllForm()
  {
    global $wpdb;

    $allForms = $wpdb->get_results("SELECT forms.id,forms.entries as fm_entries,forms.form_name,forms.status,forms.views,forms.created_at,COUNT(entries.id) as entries FROM `{$wpdb->prefix}bitforms_form` as forms LEFT JOIN `{$wpdb->prefix}bitforms_form_entries` as entries ON forms.id = entries.form_id GROUP BY forms.id");

    if (is_wp_error($allForms)) {
      return $allForms;
    } elseif (!$allForms) {
      return [];
    } else {
      return $allForms;
    }
  }

  public function getAForm($Request, $post)
  {
    if (isset($Request['id'])) {
      $formID = wp_unslash($Request['id']);
    } else {
      $formID = wp_unslash($post->id);
    }
    // return $post->fields;
    if (is_null($formID)) {
      return new WP_Error('empty_form', __('Form id is empty.', 'bit-form'));
    }
    $formManager = new AdminFormManager($formID);
    if (!$formManager->isExist()) {
      return new WP_Error('not_exists', __('Form is not exists.', 'bit-form'));
    }
    $form_content = $formManager->getFormContent();
    $helper_states = $formManager->getFormHelperStates();
    if ($formManager->isExist()) {
      $form_content_arr = [
        'layout'        => $form_content->layout,
        'nestedLayout'  => !empty($form_content->nestedLayout) ? $form_content->nestedLayout : (object) [],
        'formInfo'      => !empty($form_content->formInfo) ? $form_content->formInfo : (object) ['formName' => $formManager->getFormName()],
        'fields'        => $form_content->fields,
        'form_name'     => $formManager->getFormName(),
        'workFlowExist' => $form_content->workFlowExist,
        'report_id'     => isset($form_content->report_id) ? $form_content->report_id : null
      ];
      $successMessageHandler
        = new SuccessMessageHandler($formID);
      $successMessages = $successMessageHandler->getAllMessage();
      if (!is_wp_error($successMessages)) {
        foreach ($successMessages as $sucessMessagekey => $sucessMessagevalue) {
          $sucessMessagevalue->message_config = json_decode($sucessMessagevalue->message_config);
          $allConfirmation['type']['successMsg'][$sucessMessagekey] =
            [
              'id'     => $sucessMessagevalue->id,
              'title'  => $sucessMessagevalue->message_title,
              'msg'    => $sucessMessagevalue->message_content,
              'config' => $sucessMessagevalue->message_config,
            ];
        }
      }
      $emailTemplateHandler
        = new EmailTemplateHandler($formID);
      $emailTemplates = $emailTemplateHandler->getAllTemplate();
      if (!is_wp_error($emailTemplates)) {
        foreach ($emailTemplates as $emailTemplatekey => $emailTemplatevalue) {
          $mailTem[] =
            [
              'id'    => $emailTemplatevalue->id,
              'title' => $emailTemplatevalue->title,
              'sub'   => $emailTemplatevalue->sub,
              'body'  => $emailTemplatevalue->body,
            ];
        }
      }
      // get all pdf template
      $pdfTemplateHandler = new PdfTemplateHandler($formID);
      $pdfTemplates = $pdfTemplateHandler->getAll();

      if (!is_wp_error($pdfTemplates)) {
        foreach ($pdfTemplates as $value) {
          $pdfTem[] =
            [
              'id'      => $value->id,
              'title'   => $value->title,
              'setting' => json_decode($value->setting),
              'body'    => $value->body,
            ];
        }
      }

      $integrationHandler = new IntegrationHandler($formID);
      $formIntegrations = $integrationHandler->getAllIntegration('form');
      if (!is_wp_error($formIntegrations)) {
        foreach ($formIntegrations as $integrationkey => $integrationValue) {
          if ('redirectPage' === $integrationValue->integration_type || 'webHooks' === $integrationValue->integration_type) {
            $integrationData = [
              'id'    => $integrationValue->id,
              'title' => $integrationValue->integration_name,
            ];
            if (!empty($integrationValue->integration_details)) {
              $integration_details = (array) json_decode($integrationValue->integration_details);
              $integrationData = array_merge($integration_details, $integrationData);
            } else {
              $integration_details = [
                'url' => '',
              ];
              $integrationData = array_merge($integrationData, $integration_details);
            }
            $allConfirmation['type'][$integrationValue->integration_type][]
              = $integrationData;
          } else {
            $integrationData = [
              'id'   => $integrationValue->id,
              'name' => $integrationValue->integration_name,
              'type' => $integrationValue->integration_type,
            ];
            $integrations[] = array_merge(
              $integrationData,
              is_string($integrationValue->integration_details) ?
              (array) json_decode($integrationValue->integration_details) :
              $integrationValue->integration_details
            );
          }
        }
      }
      $settingsContent = [
        'formName'        => $formManager->getFormName(),
        'theme'           => isset($form_content->theme) ? $form_content->theme : 'default',
        'formPermissions' => isset($form_content->formPermissions) ? $form_content->formPermissions : (object) [],
        // 'submitBtn' => $form_content->buttons,
      ];

      if (!empty($allConfirmation)) {
        $confirmation = [
          'confirmation' => $allConfirmation,
        ];
      } else {
        $confirmation = [
          'confirmation' => ['type' => []],
        ];
      }
      $settingsContent = array_merge($settingsContent, $confirmation);
      if (!empty($integrations)) {
        $settingsContent = array_merge(
          $settingsContent,
          [
            'integrations' => $integrations,
          ]
        );
      } else {
        $settingsContent = array_merge(
          $settingsContent,
          [
            'integrations' => []
          ]
        );
      }

      if (!empty($mailTem)) {
        $settingsContent = array_merge(
          $settingsContent,
          [
            'mailTem' => $mailTem,
          ]
        );
      } else {
        $settingsContent = array_merge(
          $settingsContent,
          [
            'mailTem' => []
          ]
        );
      }

      if (!empty($pdfTem)) {
        $settingsContent = array_merge(
          $settingsContent,
          [
            'pdfTem' => $pdfTem,
          ]
        );
      } else {
        $settingsContent = array_merge(
          $settingsContent,
          [
            'pdfTem' => [],
          ]
        );
      }
      $formSettings = [
        'formSettings' => $settingsContent,
      ];
      $data = [
        'id'              => $formID,
        'form_name'       => $formManager->getFormName(),
        'form_content'    => $form_content_arr,
        'breakpointSize'  => $helper_states->breakpointSize,
        'style'           => $helper_states->style,
        'themeVars'       => $helper_states->themeVars,
        'themeColors'     => $helper_states->themeColors,
        'builderSettings' => $helper_states->builderSettings,
        'additional'      => empty($form_content->additional) ? [
          'enabled' => [
            'blocked_ip'    => false,
            'restrict_form' => false,
          ],
          'settings' => [
            'restrict_form' => [
              'day'  => [],
              'date' => [
                'from' => null,
                'to'   => null,
              ],
              'time' => [
                'from' => null,
                'to'   => null,
              ],
            ],
            'entry_limit' => null,
            'blocked_ip'  => [],
          ],
          'onePerIp' => false,
        ] : $form_content->additional,
        'created_at' => $formManager->getFormMetaData()['created_at'],
        'views'      => $formManager->getFormMetaData()['views'],
        'entries'    => $formManager->getFormMetaData()['entries'],
        'status'     => $formManager->getFormMetaData()['status'],
      ];
      $data = array_merge($data, $formSettings);
      $workFlowHandler = new WorkFlowHandler($formID);
      $workFlows = ['workFlows' => $workFlowHandler->getAllworkFlow()];
      $data = array_merge($data, $workFlows);
      $reportsModel = new ReportsModel();
      $returnedReportData = $reportsModel->get(
        [
          'id',
          'details',
          'isDefault',
          'type',
        ],
        [
          'category' => 'form',
          'context'  => $formID,
        ]
      );

      if (!is_wp_error($returnedReportData)) {
        $fieldNames = [];
        foreach ($formManager->getFieldLabel() as $key => $field) {
          $fieldNames[$field['key']] = isset($field->lbl) ? $field->lbl : '';
        }
        if ($formManager->isGCLIDEnabled()) {
          $fieldNames['GCLID'] = 'GCLID';
        }
        foreach ($returnedReportData as $reportKey => $reportData) {
          $reportDetails = $reportsModel->validateReportFields($reportData, $fieldNames);
          if (!empty($reportDetails) && is_string($reportDetails)) {
            $returnedReportData[$reportKey]->details = json_decode($reportDetails);
          } elseif (!empty($reportDetails)) {
            $returnedReportData[$reportKey]->details = $reportDetails;
          } else {
            $returnedReportData[$reportKey]->details = [];
          }
          if ('1' === (string) $reportData->isDefault) {
            $returnedReportData[$reportKey]->details->report_name = 'All Entries';
          }
        }
        $reports = ['reports' => $returnedReportData];
        $data = array_merge($data, $reports);
      }

      $formFields = $formManager->getFieldLabel();
      $data = array_merge($data, ['Labels' => $formFields]);

      return $data;
    }
  }

  public function getAFormV1($Request, $post)
  {
    if (isset($Request['id'])) {
      $formID = wp_unslash($Request['id']);
    } else {
      $formID = wp_unslash($post->id);
    }
    // return $post->fields;
    if (is_null($formID)) {
      return new WP_Error('empty_form', __('Form id is empty.', 'bit-form'));
    }
    $formManager = new AdminFormManager($formID);
    if (!$formManager->isExist()) {
      return new WP_Error('not_exists', __('Form is not exists.', 'bit-form'));
    }
    $form_content = $formManager->getFormContent();
    if ($formManager->isExist()) {
      $form_content_arr = [
        'layout'        => $form_content->layout,
        'fields'        => $form_content->fields,
        'form_name'     => $formManager->getFormName(),
        'workFlowExist' => $form_content->workFlowExist,
        'report_id'     => isset($form_content->report_id) ? $form_content->report_id : null
      ];
      $successMessageHandler
          = new SuccessMessageHandler($formID);
      $successMessages = $successMessageHandler->getAllMessage();
      if (!is_wp_error($successMessages)) {
        foreach ($successMessages as $sucessMessagekey => $sucessMessagevalue) {
          $allConfirmation['type']['successMsg'][$sucessMessagekey] =
              [
                'id'    => $sucessMessagevalue->id,
                'title' => $sucessMessagevalue->message_title,
                'msg'   => $sucessMessagevalue->message_content,
              ];
        }
      }
      $emailTemplateHandler
          = new EmailTemplateHandler($formID);
      $emailTemplates = $emailTemplateHandler->getAllTemplate();
      if (!is_wp_error($emailTemplates)) {
        foreach ($emailTemplates as $emailTemplatekey => $emailTemplatevalue) {
          $mailTem[] =
              [
                'id'    => $emailTemplatevalue->id,
                'title' => $emailTemplatevalue->title,
                'sub'   => $emailTemplatevalue->sub,
                'body'  => $emailTemplatevalue->body,
              ];
        }
      }
      $integrationHandler = new IntegrationHandler($formID);
      $formIntegrations = $integrationHandler->getAllIntegration('form');
      if (!is_wp_error($formIntegrations)) {
        foreach ($formIntegrations as $integrationkey => $integrationValue) {
          if ('redirectPage' === $integrationValue->integration_type || 'webHooks' === $integrationValue->integration_type) {
            $integrationData = [
              'id'    => $integrationValue->id,
              'title' => $integrationValue->integration_name,
            ];
            if (!empty($integrationValue->integration_details)) {
              // var_dump(json_decode($integrationValue->integration_details));
              $integration_details = (array) json_decode($integrationValue->integration_details);
              $integrationData = array_merge($integration_details, $integrationData);
            } else {
              $integration_details = [
                'url' => '',
              ];
              $integrationData = array_merge($integrationData, $integration_details);
            }
            $allConfirmation['type'][$integrationValue->integration_type][]
                = $integrationData;
          } else {
            $integrationData = [
              'id'   => $integrationValue->id,
              'name' => $integrationValue->integration_name,
              'type' => $integrationValue->integration_type,
            ];
            $integrations[] = array_merge(
              $integrationData,
              is_string($integrationValue->integration_details) ?
                  (array) json_decode($integrationValue->integration_details) :
                  $integrationValue->integration_details
            );
          }
        }
      }
      $settingsContent = [
        'formName' => $formManager->getFormName(),
        'theme'    => $form_content->theme,
        // 'submitBtn' => $form_content->buttons,
      ];

      if (!empty($allConfirmation)) {
        $confirmation = [
          'confirmation' => $allConfirmation,
        ];
      } else {
        $confirmation = [
          'confirmation' => ['type' => []],
        ];
      }
      $settingsContent = array_merge($settingsContent, $confirmation);
      if (!empty($integrations)) {
        $settingsContent = array_merge(
          $settingsContent,
          [
            'integrations' => $integrations,
          ]
        );
      } else {
        $settingsContent = array_merge(
          $settingsContent,
          [
            'integrations' => []
          ]
        );
      }
      if (!empty($mailTem)) {
        $settingsContent = array_merge(
          $settingsContent,
          [
            'mailTem' => $mailTem,
          ]
        );
      } else {
        $settingsContent = array_merge(
          $settingsContent,
          [
            'mailTem' => []
          ]
        );
      }
      $formSettings = [
        'formSettings' => $settingsContent,
      ];
      $data = [
        'id'           => $formID,
        'form_name'    => $formManager->getFormName(),
        'form_content' => $form_content_arr,
        'additional'   => empty($form_content->additional) ? [
          'enabled' => [
            'blocked_ip'    => false,
            'restrict_form' => false,
          ],
          'settings' => [
            'restrict_form' => [
              'day'  => [],
              'date' => [
                'from' => null,
                'to'   => null,
              ],
              'time' => [
                'from' => null,
                'to'   => null,
              ],
            ],
            'entry_limit' => null,
            'blocked_ip'  => [],
          ],
          'onePerIp' => false,
        ] : $form_content->additional,
        'created_at' => $formManager->getFormMetaData()['created_at'],
        'views'      => $formManager->getFormMetaData()['views'],
        'entries'    => $formManager->getFormMetaData()['entries'],
        'status'     => $formManager->getFormMetaData()['status'],
      ];
      $data = array_merge($data, $formSettings);
      $workFlowHandler = new WorkFlowHandler($formID);
      $workFlows = ['workFlows' => $workFlowHandler->getAllworkFlowV1()];
      $data = array_merge($data, $workFlows);
      $reportsModel = new ReportsModel();
      $returnedReportData = $reportsModel->get(
        [
          'id',
          'details',
          'isDefault',
          'type',
        ],
        [
          'category' => 'form',
          'context'  => $formID,
        ]
      );

      if (!is_wp_error($returnedReportData)) {
        $fieldNames = [];
        foreach ($formManager->getFieldLabel() as $key => $field) {
          $fieldNames[$field['key']] = isset($field->lbl) ? $field->lbl : '';
        }
        if ($formManager->isGCLIDEnabled()) {
          $fieldNames['GCLID'] = 'GCLID';
        }
        foreach ($returnedReportData as $reportKey => $reportData) {
          $reportDetails = $reportsModel->validateReportFields($reportData, $fieldNames);
          if (!empty($reportDetails) && is_string($reportDetails)) {
            $returnedReportData[$reportKey]->details = json_decode($reportDetails);
          } elseif (!empty($reportDetails)) {
            $returnedReportData[$reportKey]->details = $reportDetails;
          } else {
            $returnedReportData[$reportKey]->details = [];
          }
          if ('1' === (string) $reportData->isDefault) {
            $returnedReportData[$reportKey]->details->report_name = 'All Entries';
          }
        }
        $reports = ['reports' => $returnedReportData];
        $data = array_merge($data, $reports);
      }

      $formFields = $formManager->getFieldLabel();
      $data = array_merge($data, ['Labels' => $formFields]);

      return $data;
    }
  }

  public function deleteAForm($Request, $post)
  {
    if (isset($Request['id'])) {
      $formID = wp_unslash($Request['id']);
    } else {
      $formID = wp_unslash($post->id);
    }
    if (is_null($formID)) {
      return new WP_Error('empty_form', __('Form id is empty.', 'bit-form'));
    }

    if (!filter_var($formID, FILTER_VALIDATE_INT)) {
      return new WP_Error('invalid_form', __('Form id is invalid.', 'bit-form'));
    }

    $delete_status = static::$formModel->delete(
      [
        'id' => $formID,
      ]
    );

    $successMessageModel = new SuccessMessageModel();
    $deleteMsgStatus = $successMessageModel->delete(['form_id' => $formID]);

    $emailTemplateModel = new EmailTemplateModel();
    $deleteTemplateStatus = $emailTemplateModel->delete(['form_id' => $formID]);

    $pdfTemplateModel = new PdfTemplateModel();
    $deletePdfTemplateStatus = $pdfTemplateModel->delete(['form_id' => $formID]);

    $integrationModel = new IntegrationModel();
    $deleteIntegrationStatus = $integrationModel->delete(['form_id' => $formID]);

    $workFlowModel = new WorkFlowModel();
    $deleteworkFlowStatus = $workFlowModel->delete(['form_id' => $formID]);

    $reportsModel = new ReportsModel();
    $deleteReportStatus = $reportsModel->delete(['context' => $formID]);

    $formEntryModel = new FormEntryModel();
    $returnedEntries = $formEntryModel->get('id', ['form_id' => $formID]);
    $entries = [];

    // checked namespace exist
    if (class_exists('\BitCode\BitFormPro\Admin\BfTable\Table')) {
      \BitCode\BitFormPro\Admin\BfTable\Table::deleteTableByFormId($formID);
    }

    $fileHandler = new FileHandler();
    if (file_exists(BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $formID)) {
      $fileHandler->rmrf(BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $formID);
    }
    if (file_exists(BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $formID)) {
      $fileHandler->rmrf(BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $formID);
    }
    $formStylePath = BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-styles';

    $formCssPath = $formStylePath . DIRECTORY_SEPARATOR . 'bitform-' . $formID . '.css';
    FileHandler::deleteIsFileExists($formCssPath);

    $layoutCssPath = $formStylePath . DIRECTORY_SEPARATOR . 'bitform-layout-' . $formID . '.css';
    FileHandler::deleteIsFileExists($layoutCssPath);

    $customCssPath = $formStylePath . DIRECTORY_SEPARATOR . 'bitform-custom-' . $formID . '.css';
    FileHandler::deleteIsFileExists($customCssPath);

    $customJSPath = BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-scripts' . DIRECTORY_SEPARATOR . 'bitform-custom-' . $formID . '.js';
    FileHandler::deleteIsFileExists($customJSPath);

    $conversationCssPath = $formStylePath . DIRECTORY_SEPARATOR . 'bitform-conversational-' . $formID . '.css';
    FileHandler::deleteIsFileExists($conversationCssPath);

    return is_wp_error($delete_status) ? $delete_status : __('Form deleted successfully.', 'bit-form');
  }

  public function deleteBlukForm($Request, $post)
  {
    if (isset($Request['formID'])) {
      $formID = wp_unslash($Request['formID']);
    } else {
      $formID = wp_unslash($post->formID);
    }

    if (is_null($formID) || !is_array($formID)) {
      return new WP_Error('empty_form', __('Form id is empty.', 'bit-form'));
    }

    if (!Helpers::checkIsIntArr($formID)) {
      return new WP_Error('invalid_form', __('Form id is invalid', 'bit-form'));
    }

    $cssPath = BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-styles' . DIRECTORY_SEPARATOR;
    foreach ($formID as $id) {
      $formCssPath = $cssPath . 'bitform-' . $id . '.css';
      FileHandler::deleteIsFileExists($formCssPath);

      $layoutPath = $cssPath . 'bitform-layout-' . $id . '.css';
      FileHandler::deleteIsFileExists($layoutPath);

      $bitformCustomPath = $cssPath . 'bitform-' . $id . '-formid' . '.css';
      FileHandler::deleteIsFileExists($bitformCustomPath);
    }
    $delete_status = static::$formModel->bulkDelete(
      [
        'id' => $formID,
      ]
    );

    if (is_wp_error($delete_status)) {
      wp_send_json_error($delete_status->get_error_message(), 400);
    }

    $successMessageModel
      = new SuccessMessageModel();
    $deleteMsgStatus = $successMessageModel->bulkDelete(['form_id' => $formID]);

    $emailTemplateModel = new EmailTemplateModel();
    $deleteTemplateStatus = $emailTemplateModel->bulkDelete(['form_id' => $formID]);

    $pdfTemplateModel = new PdfTemplateModel();
    $deletePdfTemplateStatus = $pdfTemplateModel->bulkDelete(['form_id' => $formID]);

    $integrationModel = new IntegrationModel();
    $deleteIntegrationStatus = $integrationModel->bulkDelete(['form_id' => $formID]);

    $workFlowModel = new WorkFlowModel();
    $deleteworkFlowStatus = $workFlowModel->bulkDelete(['form_id' => $formID]);

    $reportsModel = new ReportsModel();
    $deleteReportStatus = $reportsModel->bulkDelete(['context' => $formID]);

    $formEntryModel = new FormEntryModel();
    $returnedEntries = $formEntryModel->get('id', ['form_id' => $formID]);

    if (class_exists('\BitCode\BitFormPro\Admin\BfTable\Table')) {
      \BitCode\BitFormPro\Admin\BfTable\Table::deleteTableByBulkFormId($formID);
    }

    $entries = [];
    foreach ($returnedEntries as $entryKey => $entryInfo) {
      if (!empty($entryInfo->id)) {
        $entries[] = $entryInfo->id;
      }
    }
    global $wpdb;
    $prefix = $wpdb->prefix;
    if (count($entries) > 0) {
      $deleteEntriesStatus = $formEntryModel->bulkDelete(
        [
          "`{$prefix}bitforms_form_entries`.`id`"      => $entries,
          "`{$prefix}bitforms_form_entries`.`form_id`" => $formID,
        ]
      );
    }

    $fileHandler = new FileHandler();
    foreach ($formID as $fId) {
      if (file_exists(BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $fId)) {
        $fileHandler->rmrf(BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $fId);
      }
    }

    return is_wp_error($delete_status) ? $delete_status : __('Forms deleted successfully.', 'bit-form');
  }

  public function genarateNewLayoutNField($layout, $fields, $oldId, $newId)
  {
    $newField = (object) [];
    foreach ($layout->lg as $ind => $itm) {
      $fld_tmp = $fields->{$layout->lg[$ind]->i};
      $layout->lg[$ind]->i = str_replace("bf$oldId-", "bf$newId-", $layout->lg[$ind]->i);
      $layout->md[$ind]->i = str_replace("bf$oldId-", "bf$newId-", $layout->md[$ind]->i);
      $layout->sm[$ind]->i = str_replace("bf$oldId-", "bf$newId-", $layout->sm[$ind]->i);
      $newField->{$layout->lg[$ind]->i} = $fld_tmp;
    }
    return ['layout' => $layout, 'fields' => $newField];
  }

  public function duplicateAForm($Request, $post)
  {
    $oldFormId = intval($post->id);
    $newFormId = intval($post->newFormId);
    if ($oldFormId < 1) {
      return new WP_Error('empty_form', __('Form id is empty.', 'bit-form'));
    }

    $formManager = new AdminFormManager($oldFormId);
    if (!$formManager->isExist()) {
      return new WP_Error('empty_form', __('Form does not exists.', 'bit-form'));
    }

    $duplicatedForm = $this->getAForm($Request, $post);
    $duplicatedForm['form_name'] = 'Duplicate of ' . $duplicatedForm['form_name'];
    $formData = FormDuplicateHelper::createReplica($duplicatedForm, $oldFormId, $newFormId);

    /* echo wp_json_encode($formData);
    \wp_die(); */
    $duplicateResponse = $this->createNewForm(null, $formData);

    if (!is_wp_error($duplicateResponse) || (is_wp_error($duplicateResponse) && 'result_empty' === $duplicateResponse->get_error_code())) {
      // duplicate stylesheet
      $style_dir = BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-styles' . DIRECTORY_SEPARATOR;
      // layout style copy
      $mainFormLayStyle = fopen($style_dir . 'bitform-layout-' . $oldFormId . '.css', 'r');
      $styleStr = fread($mainFormLayStyle, filesize($style_dir . 'bitform-layout-' . $oldFormId . '.css'));
      $newStyle = str_replace(".bf$oldFormId-", ".bf$newFormId-", $styleStr);
      $createStyleFile = fopen($style_dir . "bitform-layout-$newFormId.css", 'w');
      fwrite($createStyleFile, $newStyle);
      fclose($createStyleFile);
      // style copy
      $mainFormStyle = fopen($style_dir . 'bitform-' . $oldFormId . '.css', 'r');
      $styleStr = fread($mainFormStyle, filesize($style_dir . 'bitform-' . $oldFormId . '.css'));
      $newStyle = str_replace("-$oldFormId", "-$newFormId", $styleStr);
      $createStyleFile = fopen($style_dir . "bitform-$newFormId.css", 'w');
      fwrite($createStyleFile, $newStyle);
      fclose($createStyleFile);
      $duplicatedForm = $this->getAForm(null, (object) ['id' => $newFormId]);
      $duplicatedForm['message'] = __('Form duplicated successfully', 'bit-form');
      return $duplicatedForm;
    }
    return $duplicateResponse;
  }

  public function importAForm($post)
  {
    $oldFormId = !empty($post->formDetail->form_id) ? $post->formDetail->form_id : null;
    $newFormId = intval($post->newFormId);
    if (!$oldFormId || empty($post->formDetail->fields) || empty($post->formDetail->layout)) {
      return new WP_Error('invalid_form', __('Please import a valid json.', 'bit-form'));
    }
    $importtedForm = (array)$post->formDetail;
    $importtedForm['form_content']['fields'] = $importtedForm['fields'];
    $importtedForm['form_content']['layout'] = $importtedForm['layout'];
    $importtedForm['form_content']['nestedLayout'] = $importtedForm['nestedLayouts'];
    $importtedForm['form_content']['formInfo'] = $importtedForm['formInfo'];
    unset($importtedForm['fields'], $importtedForm['layout'], $importtedForm['nestedLayout'], $importtedForm['formInfo']);
    $formData = FormDuplicateHelper::createReplica((array)$importtedForm, $oldFormId, $newFormId);

    if (!empty($importtedForm['rowHeight'])) {
      $formData->rowHeight = $importtedForm['rowHeight'];
    }

    if (!empty($importtedForm['formStyle'])) {
      $formData->formStyle = str_replace("-$oldFormId", "-$newFormId", $importtedForm['formStyle']);
      $formData->layoutChanged = '-';
    }
    $importResponse = $this->createNewForm(null, $formData);

    $customCode = [];
    // add custom js
    if (!empty($formData->customCode->customJs)) {
      $customJsPath = 'form-scripts';
      $fileName = 'bitform-custom-' . $newFormId . '.js';
      $customCode['JavaScript'] = $formData->customCode->customJs;
      Helpers::saveFile($customJsPath, $fileName, $formData->customCode->customJs, 'w');
    }
    // add custom css
    if (!empty($formData->customCode->customCss)) {
      $customCssPath = 'form-styles';
      $fileName = 'bitform-custom-' . $newFormId . '.css';
      $customCode['CSS'] = $formData->customCode->customCss;
      Helpers::saveFile($customCssPath, $fileName, $formData->customCode->customCss, 'w');
    }
    if (!is_wp_error($importResponse)) {
      $importResponse['customCode'] = $customCode;
    }
    if (!is_wp_error($importResponse) || (is_wp_error($importResponse) && 'result_empty' === $importResponse->get_error_code())) {
      $responseData = $this->getAForm(null, (object) ['id' => $newFormId]);
      $responseData['customCode'] = $customCode;
      $responseData['message'] = __('Form imported successfully.', 'bit-form');
      return $responseData;
    }
    return $importResponse;
  }

  // public function exportAForm($Request) {
  //   $oldFormId = intval($Request['id']);
  //   if ($oldFormId < 1) {
  //     return new WP_Error('empty_form', __('Form id is empty.', 'bit-form'));
  //   }
  //   $formManager = new AdminFormManager($oldFormId);
  //   if (!$formManager->isExist()) {
  //     return new WP_Error('empty_form', __('Form does not exists.', 'bit-form'));
  //   }

  //   $newFormId = $oldFormId;
  //   $duplicatedForm = $this->getAForm($Request, (object)['id' => $oldFormId]);
  //   $formData = FormDuplicateHelper::createReplica($duplicatedForm, $oldFormId, $newFormId);

  //   $style_dir = BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-styles' . DIRECTORY_SEPARATOR;
  //   $mainFormStyle = fopen($style_dir . 'bitform-' . $oldFormId . '.css', 'r');
  //   $styleStr = fread($mainFormStyle, filesize($style_dir . 'bitform-' . $oldFormId . '.css'));

  //   $formData->formStyle = str_replace("-$oldFormId", "-$newFormId", $styleStr);
  //   $formData->layoutChanged = '-';
  //   $formData->rowHeight = FormDuplicateHelper::calcRowHeight($styleStr, $oldFormId);

  //   $formJson = wp_json_encode($formData);
  //   header('Content-Type: application/force-download');
  //   header('Content-Type: application/octet-stream');
  //   header('Content-Type: application/download');
  //   header('Content-Disposition: attachment; filename="bitform_export_' . $oldFormId . '.json"');
  //   header('Content-Description: File Transfer');
  //   header('Expires: 0');
  //   header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  //   header('Pragma: public');
  //   header('Content-Length: ' . strlen($formJson));
  //   header('Content-Transfer-Encoding: binary ');
  //   flush();
  //   echo $formJson;
  //   die();
  // }

  public function exportAForm($Request, $post)
  {
    $oldFormId = intval($post->id);
    if ($oldFormId < 1) {
      return new WP_Error('empty_form', __('Form id is empty.', 'bit-form'));
    }
    $formManager = new AdminFormManager($oldFormId);
    if (!$formManager->isExist()) {
      return new WP_Error('empty_form', __('Form does not exists.', 'bit-form'));
    }

    $newFormId = $oldFormId;
    $duplicatedForm = $this->getAForm($Request, (object)['id' => $oldFormId]);
    $formData = FormDuplicateHelper::createReplica($duplicatedForm, $oldFormId, $newFormId);
    // for custom css
    $customCssPath = 'form-styles/bitform-custom-' . $newFormId . '.css';
    $cssPath = Helpers::generatePathDirOrFile($customCssPath);
    $customCssContain = Helpers::fileRead($cssPath);
    $formData->customCode = [];
    if ('' !== $customCssContain) {
      $formData->customCode['customCss'] = $customCssContain;
    }
    // for custom js
    $customJsPath = 'form-scripts/bitform-custom-' . $newFormId . '.js';
    $jsPath = Helpers::generatePathDirOrFile($customJsPath);
    $customJsContain = Helpers::fileRead($jsPath);
    if ('' !== $customJsContain) {
      $formData->customCode['customJs'] = $customJsContain;
    }
    return $formData;
  }

  public function getFormEntryLabelAndCount($Request, $post)
  {
    if (isset($Request['id'])) {
      $id = wp_unslash($Request['id']);
    } else {
      $id = wp_unslash($post->id);
    }
    if (is_null($id)) {
      return new WP_Error('empty_form', __('Form id is empty.', 'bit-form'));
    }
    $formManager = new AdminFormManager($id);
    if (!$formManager->isExist()) {
      return new WP_Error('empty_form', __('Form does not exists.', 'bit-form'));
    }
    $formFields = $formManager->getFieldLabel();
    $formEntry = new FormEntryModel();
    $count = $formEntry->count(
      [
        'form_id' => $id,
      ]
    );
    $reportsModel = new ReportsModel();
    $returnedReportData = $reportsModel->get(
      [
        'id',
        'details',
        'isDefault',
        'type',
      ],
      [
        'category' => 'form',
        'context'  => $id,
      ]
    );
    $labels = [];
    if (!is_wp_error($returnedReportData)) {
      $fieldNames = [];
      foreach ($formFields as $field) {
        $fieldNames[$field['key']] = $field['name'];
        $labels[$field['key']] = $field;
      }
      foreach ($returnedReportData as $reportKey => $reportData) {
        $reportDetails = $reportsModel->validateReportFields($reportData, $fieldNames);
        if (!empty($reportDetails) && is_string($reportDetails)) {
          $returnedReportData[$reportKey]->details = json_decode($reportDetails);
        } elseif (!empty($reportDetails)) {
          $returnedReportData[$reportKey]->details = $reportDetails;
        }
      }
      $response['reports'] = empty($returnedReportData) ? [] : $returnedReportData;
    }

    $response['count'] = intval($count[0]->count);
    $response['Labels'] = $formFields;
    $response['fieldDetails'] = $labels;
    return $response;
  }

  public function getFormEntry($Request, $post)
  {
    if (isset($Request['id'])) {
      $id = wp_unslash($Request['id']);
      $offset = isset($Request['offset']) ?
        wp_unslash($Request['offset']) : 0;
      $pageSize = isset($Request['pageSize']) ?
        wp_unslash($Request['pageSize']) : 10;
    } else {
      $id = wp_unslash($post->id);
      $conditions = isset($post->conditions) ? wp_unslash($post->conditions) : [];
      $dateBetweenFilter = isset($post->entriesFilterByDate) ? wp_unslash($post->entriesFilterByDate) : [];
      $offset = isset($post->offset) ? wp_unslash($post->offset) : 0;
      $sortBy = isset($post->sortBy) ? wp_unslash($post->sortBy) : null;
      $filters = isset($post->filters) ? wp_unslash($post->filters) : null;
      $globalFilter = isset($post->globalFilter) ? wp_unslash($post->globalFilter) : null;
      $pageSize = isset($post->pageSize) ?
        wp_unslash($post->pageSize) : 10;
    }
    if (is_null($id)) {
      return new WP_Error('empty_form', __('Form id is empty.', 'bit-form'));
    }
    $formManager = new AdminFormManager($id);
    if (!$formManager->isExist()) {
      return new WP_Error('empty_form', __('Form does not exists', 'bit-form'));
    }

    $formEntry = new FormEntryModel();
    $entries = $formEntry->get(
      'id',
      [
        'form_id' => $id,
      ],
      null,
      null,
      'created_at',
      'DESC'
    );
    if (is_wp_error($entries)) {
      if ('result_empty' === $entries->get_error_code()) {
        return [
          'count'   => 0,
          'entries' => [],
        ];
      }
      return $entries;
    }
    $filter['field'] = $filters;
    $filter['global'] = $globalFilter;

    $formFields = $formManager->getFieldLabel(true);
    $fieldDetails = $formManager->getFields();

    $entryMeta = new FormEntryMetaModel();
    $formEntries = $entryMeta->getEntryMeta($formFields, $entries, $pageSize, $offset, $filter, $sortBy, $conditions, $dateBetweenFilter);
    $customFieldHandler = new CustomFieldHandler();
    $formEntries = $customFieldHandler->updatedEntries($formEntries, $fieldDetails);
    return $formEntries;
  }

  public function getSingleEntry($formId, $entryId)
  {
    $formManager = new AdminFormManager($formId);
    if (!$formManager->isExist()) {
      return new WP_Error('empty_form', __('Form does not exists', 'bit-form'));
    }

    $formFields = $formManager->getFieldLabel(true);
    $fieldDetails = $formManager->getFields();
    $entryMeta = new FormEntryMetaModel();
    // $formEntry = new FormEntryModel();

    $entries = $entryMeta->getSingleEntryMeta($formFields, $entryId);
    return $entries[0];

    // $customFieldHandler = new CustomFieldHandler();
    // $formEntries = $customFieldHandler->updatedEntries($formEntries, $fieldDetails);
    // return $formEntries;
  }

  public function getEntriesForReport($Request, $post)
  {
    if (isset($Request['id'])) {
      $id = wp_unslash($Request['id']);
      $offset = isset($Request['offset']) ?
        wp_unslash($Request['offset']) : null;
      $pageSize = isset($Request['pageSize']) ?
        wp_unslash($Request['pageSize']) : null;
    } else {
      $id = wp_unslash($post->id);
      $conditions = isset($post->conditions) ? wp_unslash($post->conditions) : [];
      $dateBetweenFilter = isset($post->entriesFilterByDate) ? wp_unslash($post->entriesFilterByDate) : [];
      $offset = isset($post->offset) ? wp_unslash($post->offset) : null;
      $sortBy = isset($post->sortBy) ? wp_unslash($post->sortBy) : null;
      $filters = isset($post->filters) ? wp_unslash($post->filters) : null;
      $globalFilter = isset($post->globalFilter) ? wp_unslash($post->globalFilter) : null;
      $pageSize = isset($post->pageSize) ?
        wp_unslash($post->pageSize) : null;
    }
    if (is_null($id)) {
      return new WP_Error('empty_form', __('Form id is empty.', 'bit-form'));
    }
    $formManager = new AdminFormManager($id);
    if (!$formManager->isExist()) {
      return new WP_Error('empty_form', __('Form does not exists', 'bit-form'));
    }

    $formEntry = new FormEntryModel();
    $entries = $formEntry->get(
      'id',
      [
        'form_id' => $id,
      ],
      null,
      null,
      'created_at',
      'DESC'
    );
    if (is_wp_error($entries)) {
      if ('result_empty' === $entries->get_error_code()) {
        return [
          'count'   => 0,
          'entries' => [],
        ];
      }
      return $entries;
    }
    $filter['field'] = $filters;
    $filter['global'] = $globalFilter;

    $formFields = $formManager->getFieldLabel(true);
    $fieldDetails = $formManager->getFields();

    $entryMeta = new FormEntryMetaModel();
    $formEntries = $entryMeta->getEntryMeta($formFields, $entries, $pageSize, $offset, $filter, $sortBy, $conditions, $dateBetweenFilter);
    $customFieldHandler = new CustomFieldHandler();
    $formEntries = $customFieldHandler->updatedEntries($formEntries, $fieldDetails);
    $formEntries['entries'] = Helpers::filterNullEntries($formEntries['entries']);
    return $formEntries;
  }

  public function getExportEntry($post)
  {
    $formId = wp_unslash($post->formId);
    $fileFormate = isset($post->fileFormate) ? wp_unslash($post->fileFormate) : null;
    $limit = isset($post->limit) ? wp_unslash($post->limit) : null;
    $sortBy = isset($post->sort) ? wp_unslash($post->sort) : 'ASC';
    $sortByField = isset($post->sortField) ? wp_unslash($post->sortField) : 'bitforms_form_entry_id';
    $formFields = json_decode($post->selectedField);
    if (is_null($formId)) {
      return new WP_Error('empty_form', __('Form id is empty.', 'bit-form'));
    }
    $formManager = new AdminFormManager($formId);
    $fieldLabels = $formManager->getFieldLabel(true);
    if (!$formManager->isExist()) {
      return new WP_Error('empty_form', __('Form does not exists', 'bit-form'));
    }

    $formEntry = new FormEntryModel();
    $entries = $formEntry->get(
      'id',
      [
        'form_id' => $formId,
      ],
      null,
      null,
      'created_at',
      'DESC'
    );
    if (is_wp_error($entries)) {
      if ('result_empty' === $entries->get_error_code()) {
        return [
          'count'   => 0,
          'entries' => [],
        ];
      }
      return $entries;
    }
    $entryMeta = new FormEntryMetaModel();
    $filter = [];
    $formEntries = $entryMeta->getExportEntry($formFields, $entries, $formId, $fieldLabels, $limit, $sortBy, $sortByField);
    return $formEntries;
  }

  public function deleteBlukFormEntries($Request, $post)
  {
    if (isset($Request['formID'])) {
      $formID = wp_unslash($Request['formID']);
      $entries = wp_unslash($Request['entries']);
    } else {
      $formID = wp_unslash($post->formID);
      $entries = wp_unslash($post->entries);
    }
    if (is_null($formID) || !is_array($entries) || 0 === count($entries)) {
      return new WP_Error('empty_form', __('Invalid Form ID or Entries ID.', 'bit-form'));
    }

    if (!filter_var($formID, FILTER_VALIDATE_INT)) {
      return new WP_Error('invalid_form', __('Form id is invalid.', 'bit-form'));
    }

    if (!Helpers::checkIsIntArr($entries)) {
      return new WP_Error('invalid_entries', __('Entries id is invalid', 'bit-form'));
    }

    $formManager = new AdminFormManager($formID);
    if (!$formManager->isExist()) {
      return new WP_Error('empty_form', __('Form does not exist.', 'bit-form'));
    }
    $workFlowRunHelper = new WorkFlow($formID);
    $workFlowreturnedOnDelete = $workFlowRunHelper->executeOnDelete(
      $formManager,
      $formID,
      $entries
    );
    if (isset($workFlowreturnedOnDelete['entries'])) {
      if (0 === count($workFlowreturnedOnDelete['entries'])) {
        return ['message' => __('Entry Deletetion prevented by  workflow', 'bit-form')];
      } elseif (count($workFlowreturnedOnDelete['entries']) === count($entries)) {
        $message = __('Entry Deleted successfully', 'bit-form');
      } else {
        $result['prevented'] = array_diff($entries, $workFlowreturnedOnDelete['entries']);
        $entries = $workFlowreturnedOnDelete['entries'];
        $message = __('Entry Deleted successfully, Some prevented by workflow', 'bit-form');
      }
    } else {
      $message = __('Entry Deleted successfully', 'bit-form');
    }
    global $wpdb;
    $prefix = $wpdb->prefix;
    $formEntryModel = new FormEntryModel();
    $delete_status = $formEntryModel->bulkDelete(
      [
        "`{$prefix}bitforms_form_entries`.`id`"      => $entries,
        "`{$prefix}bitforms_form_entries`.`form_id`" => $formID,
      ]
    );
    if (file_exists(BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $formID)) {
      $fileHandler = new FileHandler();
      foreach ($entries as $enrtyKey => $entryID) {
        $fileEntries = BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $formID . DIRECTORY_SEPARATOR . $entryID;
        if (file_exists($fileEntries)) {
          $fileHandler->rmrf($fileEntries);
        }
      }
    }
    $count = $formEntryModel->count(
      [
        'form_id' => $formID,
      ]
    );
    $formManager->resetSubmissionCount(intval($count[0]->count));
    if (is_wp_error($delete_status)) {
      return new WP_Error('entry_not_exists', __('Form entry deletion failed.', 'bit-form'));
    }
    $result['message'] = $message;
    return $result;
  }

  public function duplicateFormEntry($Request, $post)
  {
    if (isset($Request['formID'])) {
      $formID = wp_unslash($Request['formID']);
      $entries = wp_unslash($Request['entries']);
    } else {
      $formID = wp_unslash($post->formID);
      $entries = wp_unslash($post->entries);
    }
    if (is_null($formID) || !is_array($entries) || empty($entries)) {
      return new WP_Error('empty_form', __('Form id or entries id is invalid', 'bit-form'));
    }

    if (!filter_var($formID, FILTER_VALIDATE_INT)) {
      return new WP_Error('invalid_form', __('Form id is invalid', 'bit-form'));
    }

    if (!Helpers::checkIsIntArr($entries)) {
      return new WP_Error('invalid_entries', __('Entries id is invalid', 'bit-form'));
    }

    $formManager = new AdminFormManager($formID);
    if (!$formManager->isExist()) {
      return new WP_Error('empty_form', __('Form does not exist', 'bit-form'));
    }
    $formEntryModel = new FormEntryModel();
    $entryMeta = new FormEntryMetaModel();
    $user_details = static::$ipTool->getUserDetail();
    $total_entries = count($entries);
    $duplicate_count = 0;
    $test = '';
    $fileHandler = new FileHandler();
    $result = [];
    foreach ($entries as $entryIndex => $entryID) {
      $duplicatedEntryId = $formEntryModel->insert(
        [
          'form_id'     => $formID,
          'user_id'     => $user_details['id'],
          'user_ip'     => $user_details['ip'],
          'user_device' => $user_details['device'],
          'referer'     => 'duplicate of #' . $entryID,
          'status'      => 1,
          'created_at'  => $user_details['time'],
        ]
      );
      if ($duplicatedEntryId) {
        $duplicate_status = $entryMeta->duplicateEntryMeta(
          [
            'duplicateID' => $duplicatedEntryId,
            'entryID'     => $entryID,
          ]
        );
        if ($duplicate_status) {
          $result['details'][$entryID] = $duplicatedEntryId;
          $duplicate_count = $duplicate_count + 1;
          if (file_exists(BITFORMS_UPLOAD_DIR . "/$formID/$entryID")) {
            $fileHandler->cpyr(
              BITFORMS_UPLOAD_DIR . "/$formID/$entryID",
              BITFORMS_UPLOAD_DIR . "/$formID/$duplicatedEntryId"
            );
          }
        }
      }
    }

    $count = $formEntryModel->count(
      [
        'form_id' => $formID,
      ]
    );
    $formManager->resetSubmissionCount(intval($count[0]->count));
    $result['message'] = 1 === count($entries) ? __('Entry Duplicated successfully', 'bit-form') : __('Entries Duplicated successfully', 'bit-form');
    return ($total_entries === $duplicate_count) ? $result : false;
  }

  public function editFormEntry($Request, $post)
  {
    if (isset($Request['formID'])) {
      $formID = wp_unslash($Request['formID']);
      $entryID = wp_unslash($Request['entryID']);
    } else {
      $formID = wp_unslash($post->formID);
      $entryID = wp_unslash($post->entryID);
    }
    if (is_null($formID) || is_null($entryID)) {
      return new WP_Error('empty_form', __('Form id or entries id is invalid', 'bit-form'));
    }

    $formManager = new AdminFormManager($formID);
    if (!$formManager->isExist()) {
      return new WP_Error('empty_form', __('Form does not exist', 'bit-form'));
    }
    $formEntryModel = new FormEntryModel();
    $entryMeta = new FormEntryMetaModel();

    $formEntry = $formEntryModel->get(
      '*',
      [
        'form_id' => $formID,
        'id'      => $entryID,
      ]
    );

    if (!$formEntry) {
      return new WP_Error('empty_form', __('Form entries does not exist.', 'bit-form'));
    }
    $formEntryMeta = $entryMeta->get(
      [
        'meta_key',
        'meta_value',
      ],
      [
        'bitforms_form_entry_id' => $entryID,
      ]
    );
    $entries = [];
    foreach ($formEntryMeta as $key => $value) {
      $entries[$value->meta_key] = $value->meta_value;
    }
    $formContent = $formManager->getFormContent();
    $fieldsKey = $formManager->getFieldsKey();
    $form_fields = $formContent->fields;
    $layout = $formContent->layout;
    foreach ($form_fields as $key => $value) {
      // $field_name = preg_replace('/[\`\~\!\@\#\$\'\.\s\?\+\-\*\&\|\/\\!]/', '_', $value->lbl);
      if (isset($entries[$key])) {
        $form_fields->{$key}->val = $entries[$key];
        $form_fields->{$key}->name = $key;
      }
    }
    $workFlowRunHelper = new WorkFlow($formID);
    $workFlowreturnedOnLoad = $workFlowRunHelper->executeOnLoad(
      'edit',
      $form_fields
    );
    $workFlowreturnedOnUserInput = $workFlowRunHelper->executeOnUserInput(
      'edit',
      $form_fields
    );
    if (!empty($workFlowreturnedOnLoad['fields'])) {
      $form_fields = $workFlowreturnedOnLoad['fields'];
    }
    $formData = [
      'layout'        => $layout,
      'fields'        => $form_fields,
      'conditional'   => !empty($workFlowreturnedOnUserInput['conditional']) ? $workFlowreturnedOnUserInput['conditional'] : false,
      'fieldToCheck'  => !empty($workFlowreturnedOnUserInput['fieldToCheck']) ? $workFlowreturnedOnUserInput['fieldToCheck'] : false,
      'fieldToChange' => !empty($workFlowreturnedOnUserInput['fieldToChange']) ? $workFlowreturnedOnUserInput['fieldToChange'] : false,
      'fieldsKey'     => $fieldsKey,
    ];
    return $formData;
  }

  public function updateFormEntry($Request, $post)
  {
    if (isset($Request['formID'], $Request['entryID'])) {
      $formID = wp_unslash($Request['formID']);
      $entryID = wp_unslash($Request['entryID']);
      unset($Request['action'], $Request['formID'], $Request['entryID']);
    }
    $formManager = new AdminFormManager($formID);
    $formManager->fieldNameReplaceOfPost();
    $updatedValue = wp_unslash($_POST);

    if (is_null($updatedValue) || !is_array($updatedValue)) {
      return new WP_Error('empty_data', __('Failed to update, Data is empty.', 'bit-form'));
    }

    if (is_null($formID) || is_null($entryID)) {
      return new WP_Error('empty_data', __('Invalid Form ID or entries ID.', 'bit-form'));
    }

    if (!$formManager->isExist()) {
      return new WP_Error('empty_data', __('Form does not exist.', 'bit-form'));
    }
    return $formManager->updateFormEntry($updatedValue, $formID, $entryID);
  }

  public function getLogHistory($Request, $post)
  {
    if (isset($Request['formID'])) {
      $formID = wp_unslash($Request['formID']);
      $entryID = wp_unslash($Request['entryID']);
    } else {
      $formID = wp_unslash($post->formID);
      $entryID = wp_unslash($post->entryID);
    }

    if (is_null($formID) || is_null($entryID)) {
      return new WP_Error('empty_form', __('Invalid Form ID or Entries ID.', 'bit-form'));
    }

    $formManager = new AdminFormManager($formID);
    if (!$formManager->isExist()) {
      return new WP_Error('empty_form', __('Form does not exist.', 'bit-form'));
    }
    $formLogModel = new FormEntryLogModel();

    $log_history = $formLogModel->geLogHistory($formID, $entryID);
    return $log_history;
  }

  public function importDataStore($Request, $post)
  {
    if (isset($Request['formID'])) {
      $formID = wp_unslash($Request['formID']);
    } else {
      $formID = wp_unslash($post->formID);
    }
    if (is_null($formID)) {
      return new WP_Error('empty_form', __('Invalid Form id or entries id.', 'bit-form'));
    }
  }

  public function getAllWPPages($Request, $post)
  {
    $pages = get_pages(['post_status' => 'publish', 'sort_column' => 'post_date', 'sort_order' => 'desc']);
    $allPages = [];
    foreach ($pages as $pageKey => $pageDetails) {
      $allPages[$pageKey]['title'] = $pageDetails->post_title;
      $allPages[$pageKey]['url'] = get_page_link($pageDetails->ID);
    }
    return $allPages;
  }

  public function deleteAIntegration($Request, $post)
  {
    if (isset($Request['formID']) && $Request['id']) {
      $formID = json_decode(wp_unslash($Request['formID']));
      $id = wp_unslash($Request['id']);
    } else {
      $formID = wp_unslash($post->formID);
      $id = wp_unslash($post->id);
    }
    if ($formID < 0 || empty($id)) {
      return new WP_Error('empty_form', 'Invalid Form ID or Integration ID.');
    }

    $integrationHandler = new IntegrationHandler($formID);
    $delete_status = $integrationHandler->deleteIntegration($id);
    if (is_wp_error($delete_status)) {
      return $delete_status;
    }

    return [
      'message' => __('Integration deleted', 'bit-form'),
    ];
  }

  public function deleteSuccessMessage($Request, $post)
  {
    if (isset($Request['formID']) && $Request['id']) {
      $formID = json_decode(wp_unslash($Request['formID']));
      $id = wp_unslash($Request['id']);
    } else {
      $formID = wp_unslash($post->formID);
      $id = wp_unslash($post->id);
    }
    if (empty($formID) || empty($id)) {
      return new WP_Error('empty_form', 'Invalid Form ID or Message ID.');
    }
    $successMessageHandler
      = new SuccessMessageHandler($formID);
    $delete_status = $successMessageHandler->deleteMessage($id);
    if (is_wp_error($delete_status)) {
      return $delete_status;
    }
    return [
      'message' => __('Message deleted', 'bit-form'),
    ];
  }

  public function deleteAWorkflow($Request, $post)
  {
    if (isset($Request['formID']) && $Request['id']) {
      $formID = json_decode(wp_unslash($Request['formID']));
      $id = wp_unslash($Request['id']);
    } else {
      $formID = wp_unslash($post->formID);
      $id = wp_unslash($post->id);
    }
    if (empty($formID) || empty($id)) {
      return new WP_Error('empty_form', 'Invalid Form ID or Workflow ID.');
    }
    $workFlowHandler = new WorkFlowHandler($formID);
    $delete_status = $workFlowHandler->deleteworkFlow($id);
    if (is_wp_error($delete_status)) {
      return $delete_status;
    }
    return [
      'message' => __('workflow deleted', 'bit-form'),
    ];
  }

  public function deleteAMailTemplate($Request, $post)
  {
    if (isset($Request['formID']) && $Request['id']) {
      $formID = json_decode(wp_unslash($Request['formID']));
      $id = wp_unslash($Request['id']);
    } else {
      $formID = wp_unslash($post->formID);
      $id = wp_unslash($post->id);
    }
    if (empty($formID) || empty($id)) {
      return new WP_Error('empty_form', 'Invalid Form ID or  Email Template ID.');
    }
    $emailTemplateHandler = new EmailTemplateHandler($formID);
    $delete_status = $emailTemplateHandler->deleteTemplate($id);
    if (is_wp_error($delete_status)) {
      return $delete_status;
    }
    return [
      'message' => __('Email Template deleted', 'bit-form'),
    ];
  }

  public function duplicateAMailTemplate($Request, $post)
  {
    if (isset($Request['formID']) && $Request['id']) {
      $formID = json_decode(wp_unslash($Request['formID']));
      $id = wp_unslash($Request['id']);
    } else {
      $formID = wp_unslash($post->formID);
      $id = wp_unslash($post->id);
    }
    if (empty($formID) || empty($id)) {
      return new WP_Error('empty_form', 'Invalid Form ID or Email Template ID.');
    }

    if (false === filter_var($id, FILTER_VALIDATE_INT)) {
      return new WP_Error('empty_form', 'Invalid Email Template ID.');
    }

    if (false === filter_var($formID, FILTER_VALIDATE_INT)) {
      return new WP_Error('empty_form', 'Invalid Form ID.');
    }

    $emailTemplateHandler = new EmailTemplateHandler($formID);
    $duplicate_status = $emailTemplateHandler->duplicateTemplate($id);
    if (is_wp_error($duplicate_status)) {
      return $duplicate_status;
    }
    return [
      'message' => __('Email Template duplicated', 'bit-form'),
    ];
  }

  public function setAllFormsReport($Request, $post)
  {
    $reports = empty($post->reports) ? null : wp_unslash($post->reports);
    if (empty($reports)) {
      return new WP_Error('empty_report_prefs', 'Report data is Empty, nothing to save or update.');
    }
    if (!empty($reports)) {
      $reportsModel = new ReportsModel();
      $user_details = static::$ipTool->getUserDetail();
      foreach ($reports as $reportIndex => $report) {
        if (empty($report->id)) {
          $reportSaveSatus = $reportsModel->insert(
            [
              'type'        => 'table',
              'category'    => 'app',
              'context'     => 'allForm',
              'details'     => is_string($report->details) ? $report->details : wp_json_encode($report->details),
              'isDefault'   => 1,
              'user_id'     => $user_details['id'],
              'user_ip'     => $user_details['ip'],
              'user_device' => $user_details['device'],
              'created_at'  => $user_details['time'],
              'updated_at'  => $user_details['time'],
            ]
          );
          $newData['reports'] = 1;
        } else {
          $reportSaveSatus = $reportsModel->update(
            [
              'type'        => 'table',
              'category'    => 'app',
              'context'     => 'allForm',
              'details'     => is_string($report->details) ? $report->details : wp_json_encode($report->details),
              'isDefault'   => 1,
              'user_id'     => $user_details['id'],
              'user_ip'     => $user_details['ip'],
              'user_device' => $user_details['device'],
              'updated_at'  => $user_details['time'],
            ],
            [
              'id' => $report->id,
            ]
          );
        }
      }
    }
    if (is_wp_error($reportSaveSatus)) {
      if ('result_empty' === $reportSaveSatus->get_error_code()) {
        return new WP_Error('empty_report_prefs', 'Nothing to save or update');
      }
      return new WP_Error('error_report_prefs', 'Error occured in saving reports');
    }
    $returnedReportData = $reportsModel->get(
      [
        'id',
        'details',
        'isDefault',
        'category',
        'context',
      ],
      [
        'category' => 'app',
        'context'  => 'allForm',
      ]
    );
    if (!is_wp_error($returnedReportData)) {
      foreach ($returnedReportData as $reportKey => $reportData) {
        if (isset($reportData->details) && is_string($reportData->details)) {
          $returnedReportData[$reportKey]->details = json_decode($reportData->details);
        }
      }
      $reports = $returnedReportData;
    }
    return [
      'message' => __('Report preferrences saved successfully', 'bit-form'),
      'reports' => empty($reports) ? [] : $reports,
    ];
  }

  public function savegReCaptcha($Request, $post)
  {
    $reCaptcha = $post->reCaptcha;

    if (is_null($reCaptcha)) {
      return new WP_Error('empty_gcaptchdetails', __('g-ReCAPTCHA details is empty', 'bit-form'));
    }
    if (is_string($reCaptcha) && !empty(json_decode($reCaptcha)->id)) {
      $reCaptcha = json_decode($reCaptcha);
      $integrationID = $reCaptcha->id;
      unset($reCaptcha->id);
    } else {
      $integrationID = $reCaptcha->id;
      unset($reCaptcha->id);
    }
    $reCaptcha = wp_json_encode($reCaptcha);

    // $integrationName = 'google reCaptcha';
    $integrationName = $post->integrationName;

    // $integrationType = 'v2' === $post->version ? 'gReCaptcha' : 'gReCaptchaV3';
    $integrationType = $post->integrationType;

    $integrationDetails = $reCaptcha;
    $user_details = static::$ipTool->getUserDetail();
    $integrationHandler = new IntegrationHandler(0, $user_details);
    $response = [];
    if (empty($integrationID)) {
      $captchaSaveStatus = $integrationHandler->saveIntegration($integrationName, $integrationType, $integrationDetails, 'app');
      $response['id'] = $captchaSaveStatus;
      $response['message'] = __('reCAPTCHA saved successfully', 'bit-form');
    } else {
      $captchaSaveStatus = $integrationHandler->updateIntegration($integrationID, $integrationName, $integrationType, $integrationDetails, 'app');
      $response['message'] = __('reCAPTCHA updated successfully', 'bit-form');
    }

    if (is_wp_error($captchaSaveStatus)) {
      return $captchaSaveStatus;
    }
    return $response;
  }

  public function savePaymentSetting($Request, $post)
  {
    if (isset($post->paySetting)) {
      $paySetting = $post->paySetting;
    }
    if (is_null($paySetting)) {
      return new WP_Error('empty_gcaptchdetails', __('Setting details is empty', 'bit-form'));
    }
    if (is_string($paySetting)) {
      $paySetting = json_decode($paySetting);
    }

    $canSave = null;
    switch($paySetting->type) {
      case 'PayPal':
        $canSave = $this->addWebhookToPaypal($paySetting);
        break;
      case 'Razorpay':
        $canSave = true;
        break;
      case 'Stripe':
        $canSave = $this->addWebhookToStripe($paySetting);
        break;
      case 'Mollie':
        $canSave = true;
        break;
      default:
        $canSave = null;
    }
    if (is_null($canSave)) {
      return new WP_Error('stripe', __('Please re-check your Client ID & Secret', 'bit-form'));
    }
    $integrationID = null;
    if (isset($paySetting->id)) {
      $integrationID = $paySetting->id;
      unset($paySetting->id);
    }
    $integrationName = $paySetting->name;
    $integrationType = 'payments';
    $paySetting = wp_json_encode($paySetting);
    $integrationDetails = $paySetting;
    $user_details = static::$ipTool->getUserDetail();
    $integrationHandler = new IntegrationHandler(0, $user_details);
    $response = [];
    if (empty($integrationID)) {
      $paymentSaveStatus = $integrationHandler->saveIntegration($integrationName, $integrationType, $integrationDetails, 'app');
      $response['id'] = $paymentSaveStatus;
      $response['message'] = __('Payment setting saved successfully', 'bit-form');
    } else {
      $paymentSaveStatus = $integrationHandler->updateIntegration($integrationID, $integrationName, $integrationType, $integrationDetails, 'app');
      $response['message'] = __('Payment setting updated successfully', 'bit-form');
    }

    if (is_wp_error($paymentSaveStatus)) {
      return $paymentSaveStatus;
    }
    return $response;
  }

  private function addWebhookToPaypal($paySetting)
  {
    $transaction_mode = $paySetting->mode;
    $clientId = $paySetting->clientID;
    $clientSecret = $paySetting->clientSecret;
    $base_url = 'sandbox' === $transaction_mode ? 'https://api-m.sandbox.paypal.com' : 'https://api.paypal.com';
    // get all webhooks
    $webhooks_url = $base_url . '/v1/notifications/webhooks';
    $webhooks_headers = [
      'Content-Type'  => 'application/json',
      'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret)
    ];
    $webhook_url = get_rest_url() . 'bitform/v1/payments/paypal';
    // adding webhook
    $data = [
      'url'         => $webhook_url,
      'event_types' => [
        [
          'name' => 'CHECKOUT.ORDER.APPROVED'
        ]
      ]
    ];
    $webhook_response = HttpHelper::post($webhooks_url, wp_json_encode($data), $webhooks_headers);
    if (is_wp_error($webhook_response) || $webhook_response->error) {
      return null;
    }
    return true;
  }

  private function addWebhookToStripe($paySetting)
  {
    $clientSecret = $paySetting->clientSecret;

    $create_webhook_url = 'https://api.stripe.com/v1/webhook_endpoints';
    $headers = [
      'Content-Type'  => 'application/x-www-form-urlencoded',
      'Authorization' => "Bearer $clientSecret",
    ];

    $webhook_url = get_rest_url() . 'bitform/v1/payments/stripe';

    $apiResponse = HttpHelper::get($create_webhook_url, '', $headers);

    if (isset($apiResponse->error)) {
      return null;
    }
    $webhookList = $apiResponse->data;
    $webhook_already_exist = false;

    foreach ($webhookList as $webhook) {
      if ($webhook->url === $webhook_url) {
        $webhook_already_exist = true;
        break;
      }
    }

    $data = [
      'enabled_events[]'  => 'payment_intent.succeeded',
      'url'               => $webhook_url,
    ];

    if (!$webhook_already_exist) {
      $webhook_response = HttpHelper::post($create_webhook_url, $data, $headers);
      if (is_wp_error($webhook_response) || (isset($webhook_response->error) && $webhook_response->error)) {
        return null;
      }
    }
    return true;
  }

  public function builerHelperState($formId)
  {
    $data = static::$formModel->get(
      ['id', 'builder_helper_state'],
      ['id' => $formId]
    );

    if (!is_wp_error($data)) {
      foreach ($data as $key => $value) {
        if (isset($value->builder_helper_state) && is_string($value->builder_helper_state)) {
          $data[$key]->builder_helper_state = json_decode($value->builder_helper_state);
        }
      }
    }

    return $data;
  }

  public function updateGeneratedScriptPageIds()
  {
    $updateData = [
      'generated_script_page_ids' => \wp_json_encode((object) []),
    ];
    $update_status = static::$formModel->update(
      $updateData,
      [
        'status' => 1
      ]
    );
    if (is_wp_error($update_status)) {
      return $update_status;
    } elseif (!$update_status) {
      return new WP_Error('empty_form', __('Form update failed.', 'bit-form'));
    } else {
      return true;
    }
  }

  public function updateSuccessMessageClassName($str, $msgIdx, $msgId)
  {
    $TEMP_CONF_ID = '_tmp_' . $msgIdx . '_conf_id';
    return str_replace($TEMP_CONF_ID, $msgId, $str);
  }

  public function startMigrationProcess()
  {
    Utilities::duplicateDbTable('form', 'form_v1');
    Utilities::duplicateDbTable('workflows', 'workflows_v1');
    Utilities::duplicateDbTable('success_messages', 'success_messages_v1');
    MigrationHelper::duplicateV1StyleFiles();
    $all_forms = $this->getAllForm();
    $v1FormContents = [];
    $defaultStyleClass = MigrationHelper::getBitformDefaultStyleClass();
    foreach ($all_forms as $form) {
      $formContents = $this->getAFormV1(['id' => $form->id], ['id' => $form->id]);
      $v1FormContents[] = $formContents;
      FrontEndScriptGenerator::saveCssFile("{$form->id}", $defaultStyleClass);
      FrontEndScriptGenerator::saveCssFile("{$form->id}-formid", $defaultStyleClass);
    }
    set_transient('bitforms_v1_form_contents', $v1FormContents);
    update_option('bitforms_migrating_to_v2', true);
  }
}
