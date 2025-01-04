<?php

/**
 *
 * @package BitForms
 */

namespace BitCode\BitForm\Core\Integration;

use BitCode\BitForm\Core\Database\FormEntryModel;
use BitCode\BitForm\Core\Database\FormModel;
use BitCode\BitForm\Core\Util\ApiResponse;
use BitCode\BitForm\Core\Util\SmartTags;
use BitCode\BitForm\Core\Util\Utilities;
/**
 * Provides details of available integration and helps to
 * execute available integrations
 */

use FilesystemIterator;

final class Integrations
{
  public $integrations = [];

  /**
   * Undocumented function
   *
   * @return array
   */
  public function getAllIntegrations()
  {
    return $this->allIntegrations();
  }

  /**
   * Undocumented function
   *
   * @return array
   */
  protected function allIntegrations()
  {
    $dirs = new FilesystemIterator(__DIR__);
    foreach ($dirs as $dirInfo) {
      if ($dirInfo->isDir()) {
        $integrationsBaseName = basename($dirInfo);
        if (
          file_exists(__DIR__ . '/' . $integrationsBaseName)
          && file_exists(__DIR__ . '/' . $integrationsBaseName . '/' . $integrationsBaseName . 'Handler.php')
        ) {
          $integrations[] = $integrationsBaseName;
        }
      }
    }
    return $integrations;
  }

  /**
   * Checks a Integration Exists or not
   *
   * @param  string $name Name of Integration
   * @return boolean | string
   */
  protected static function isExists($name)
  {
    $path = Utilities::getRealPath(__DIR__, $name);
    if (!empty($path) && $fileName = Utilities::getRealPath(__DIR__ . "/{$path}", "{$name}Handler.php")) {
      $className = Utilities::getFileNameExceptExtension($fileName);
      return __NAMESPACE__ . "\\{$path}\\{$className}";
    }

    if (!defined('BITFORMPRO_PLUGIN_DIR_PATH')) {
      return false;
    }

    $proPath = BITFORMPRO_PLUGIN_DIR_PATH . '/includes/Integration';
    $proNamespace = 'BitCode\\BitFormPro\\Integration';
    $path = Utilities::getRealPath($proPath, $name);
    if (!empty($path) && $fileName = Utilities::getRealPath("{$proPath}/{$path}", "{$name}Handler.php")) {
      $className = Utilities::getFileNameExceptExtension($fileName);
      return "{$proNamespace}\\{$path}\\{$className}";
    }

    return false;
  }

  /**
   * This function helps to get single integration information
   *
   * @return bool setIntegration()
   */
  public function getIntegration($integrationBaseName)
  {
    return $this->setIntegration($integrationBaseName);
  }

  public function registerAjax()
  {
    $dirs = new FilesystemIterator(__DIR__);
    foreach ($dirs as $dirInfo) {
      if ($dirInfo->isDir()) {
        $integrationBaseName = basename($dirInfo);
        if (
          file_exists(__DIR__ . '/' . $integrationBaseName)
          && file_exists(__DIR__ . '/' . $integrationBaseName . '/' . $integrationBaseName . 'Handler.php')
        ) {
          $integration = __NAMESPACE__ . "\\{$integrationBaseName}\\{$integrationBaseName}Handler";
          if (method_exists($integration, 'registerAjax')) {
            $integration::registerAjax();
            // (new $integration(0, 0))->registerAjax();
          }
          /* $handler = new $integration($integrationID, $formID);
                $handler->execute($integrationHandler, $integrationDetails, $fieldValues); */
        }
      }
    }
  }

  protected static function entryDeleted($formId, $entryId)
  {
    $formModel = new FormModel();
    $formContent = $formModel->get(
      [
        'id',
        'form_content',
      ],
      [
        'id' => $formId,
      ]
    );
    if (!is_wp_error($formContent)) {
      $content = \json_decode($formContent[0]->form_content);
      if (isset($content->additional->enabled->submission)) {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $formEntryModel = new FormEntryModel();
        $formEntryModel->bulkDelete(
          [
            "`{$prefix}bitforms_form_entries`.`id`"      => $entryId,
            "`{$prefix}bitforms_form_entries`.`form_id`" => $formId,
          ]
        );
      }
    }
  }

  public static function specialTagFields($fieldMap)
  {
    $specialTagFieldValue = [];

    foreach ($fieldMap as $value) {
      $triggerValue = $value->formField;
      $specialTagFieldValue[$value->formField] = SmartTags::getSmartTagValue($triggerValue);
    }
    return $specialTagFieldValue;
  }

  /**
   * This function helps to execute Integration
   *
   * @param array   $integrations List  of integration to execute.
   *                              Element   will be json string like {"id":1}
   * @param array   $fieldValues  Values of submitted fields
   * @param integer $formID       ID of current form
   *
   * @return void                  Nothing to return
   */
  public static function executeIntegrations($integrations, $fieldValues, $formID, $logID = null, $entryID = 0)
  {
    $integrationHandler = new IntegrationHandler($formID);
    $logResponse = new ApiResponse();
    if (is_array($integrations)) {
      foreach ($integrations as $integrationIDStr) {
        if (!is_string($integrationIDStr)) {
          return;
        }
        $integrationID = intval(json_decode($integrationIDStr)->id, 10);
        if (!empty($integrationID) && is_int($integrationID)) {
          $integrationResult
            = $integrationHandler->getAIntegration($integrationID);
          $integrationDetails = is_wp_error($integrationResult) ? null : $integrationResult[0];
          $integrationName = is_null($integrationDetails) ? null : ucfirst(str_replace(' ', '', $integrationDetails->integration_type));
          if ('Brevo(SendinBlue)' === $integrationName) {
            $integrationName = 'SendinBlue';
          }
          if (!is_null($integrationName) && static::isExists($integrationName)) {
            $integration = static::isExists($integrationName);
            $handler = new $integration($integrationID, $formID);
            $integDetails = is_string($integrationDetails->integration_details) ? json_decode($integrationDetails->integration_details) : $integrationDetails->integration_details;
            if (isset($integDetails->field_map)) {
              $sptagData = self::specialTagFields($integDetails->field_map);
              $fieldValues = $fieldValues + $sptagData;
            }
            $handler->execute($integrationHandler, $integrationDetails, $fieldValues, $entryID, $logID);
          } else {
            do_action('bitform_integration_run_failure', $integrationID, $formID);

            $errorMsg = apply_filters('bitform_filter_integration_run_failure_message', __('Integration execution failed', 'bit-form'), $integrationID, $formID);

            error_log('[+] Integration execution failed: ' . print_r($errorMsg, true));

            $logResponse->apiResponse(
              $logID,
              $integrationID,
              ['type' => 'record', 'type_name' => $integrationName || 'Integration'],
              'errors',
              $errorMsg
            );
          }
        }
      }
    }
  }

  public static function integrationExecutionHelper($integrations, $fieldValue, $formID, $entryID, $logID)
  {
    if (empty($integrations) || empty($formID)) {
      return;
    }
    $trnasientData = get_transient("bitform_trigger_transient_{$entryID}");
    $trnasientData = is_string($trnasientData) ? json_decode($trnasientData) : $trnasientData;
    if (!empty($trnasientData['fields'])) {
      $fieldValue = array_merge($fieldValue, $trnasientData['fields']);
    }
    foreach ($integrations as $key => $value) {
      self::executeIntegrations($value, $fieldValue, $formID, $logID, $entryID);
    }
    self::entryDeleted($formID, $entryID);
  }
}
