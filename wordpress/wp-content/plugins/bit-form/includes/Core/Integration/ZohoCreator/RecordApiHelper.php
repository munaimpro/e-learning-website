<?php

/**
 * ZohoCreator Record Api
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoCreator;

use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\DateTimeHelper;
use BitCode\BitForm\Core\Util\HttpHelper;
use WP_Error;

/**
 * Provide functionality for Record insert,upsert
 */
class RecordApiHelper
{
  private $_defaultHeader;
  private $_apiDomain;
  private $_tokenDetails;

  private $_integrationID;
  private $_logID;
  private $_logResponse;

  public function __construct($tokenDetails, $integId, $logID)
  {
    $this->_defaultHeader['Authorization'] = "Zoho-oauthtoken {$tokenDetails->access_token}";
    $this->_defaultHeader['Content-Type'] = 'application/json';
    $this->_apiDomain = \urldecode($tokenDetails->api_domain);
    $this->_tokenDetails = $tokenDetails;
    $this->_integrationID = $integId;
    $this->_logID = $logID;
    $this->_logResponse = new UtilApiResponse();
  }

  public function insertRecord($dataCenter, $accountOwner, $applicationId, $formId, $data)
  {
    $insertRecordEndpoint = "https://creator.zoho.{$dataCenter}/api/v2/{$accountOwner}/{$applicationId}/form/{$formId}";

    return HttpHelper::post($insertRecordEndpoint, $data, $this->_defaultHeader);
  }

  public function updateRecord($dataCenter, $accountOwner, $applicationId, $reportId, $data)
  {
    $insertRecordEndpoint = "https://creator.zoho.{$dataCenter}/api/v2/{$accountOwner}/{$applicationId}/report/{$reportId}";

    return HttpHelper::request($insertRecordEndpoint, 'PATCH', $data, $this->_defaultHeader);
  }

  private function getAllReports($dataCenter, $accountOwner, $applicationId)
  {
    $getReportsEndpoint = "https://creator.zoho.{$dataCenter}/api/v2/{$accountOwner}/{$applicationId}/reports";

    return HttpHelper::get($getReportsEndpoint, null, $this->_defaultHeader);
  }

  private function testDate($date)
  {
    if ($date && date('Y-m-d', strtotime($date)) === $date) {
      return true;
    }
    return false;
  }

  public function executeRecordApi($formID, $entryID, $fieldValues, $integrationDetails)
  {
    $dataCenter = $integrationDetails->dataCenter;
    $accountOwner = $integrationDetails->accountOwner;
    $applicationId = $integrationDetails->applicationId;
    $formId = $integrationDetails->formId;
    $fieldMap = $integrationDetails->field_map;
    $uploadFieldMap = $integrationDetails->upload_field_map;
    $actions = $integrationDetails->actions;
    $defaultFields = $integrationDetails->default->fields->{$applicationId}->{$formId}->fields;
    $required = $integrationDetails->default->fields->{$applicationId}->{$formId}->required;
    $dateFormat = '';
    foreach ($integrationDetails->default->applications as $defaultApplication) {
      if ($defaultApplication->applicationId === $applicationId) {
        $dateFormat = $defaultApplication->date_format;
        break;
      }
    }

    $fieldData = [];
    $dateTimeHelper = new DateTimeHelper();
    $convertedDateFormat = $dateTimeHelper->getUnicodeToPhpFormat('date', $dateFormat);

    $fieldPair = null;
    foreach ($defaultFields as $defaultField) {
      foreach ($fieldMap as $fieldPair) {
        if (!empty($fieldPair->zohoFormField) && $fieldPair->zohoFormField === $defaultField->apiName) {
          if (isset($defaultField->parent)) {
            if ('custom' === $fieldPair->formField && isset($fieldPair->customValue)) {
              $fieldData['data'][$defaultField->parent][$fieldPair->zohoFormField] = $this->testDate($fieldPair->customValue) ? date_format(date_create($fieldPair->customValue), $convertedDateFormat) : $fieldPair->customValue;
            } else {
              $fieldData['data'][$defaultField->parent][$fieldPair->zohoFormField] = $this->testDate($fieldValues[$fieldPair->formField]) ? date_format(date_create($fieldValues[$fieldPair->formField]), $convertedDateFormat) : $fieldValues[$fieldPair->formField];
            }
          } elseif ('custom' === $fieldPair->formField && isset($fieldPair->customValue)) {
            if ('Url' === $defaultField->apiName) {
              $fieldData['data']['Url']['url'] = $fieldPair->customValue;
            } elseif (isset($defaultField->type)) {
              $fieldData['data'][$fieldPair->zohoFormField] = 'string' === gettype($fieldPair->customValue) ? explode(',', $fieldPair->customValue) : $fieldPair->customValue;
            } else {
              $fieldData['data'][$fieldPair->zohoFormField] = $this->testDate($fieldPair->customValue) ? date_format(date_create($fieldPair->customValue), $convertedDateFormat) : $fieldPair->customValue;
            }
          } else {
            if ('Url' === $defaultField->apiName) {
              $fieldData['data']['Url']['url'] = $fieldValues[$fieldPair->formField];
            } elseif (isset($defaultField->type)) {
              $fieldData['data'][$fieldPair->zohoFormField] = 'string' === gettype($fieldValues[$fieldPair->formField]) ? explode(',', $fieldValues[$fieldPair->formField]) : $fieldValues[$fieldPair->formField];
            } else {
              $fieldData['data'][$fieldPair->zohoFormField] = $this->testDate($fieldValues[$fieldPair->formField]) ? date_format(date_create($fieldValues[$fieldPair->formField]), $convertedDateFormat) : $fieldValues[$fieldPair->formField];
            }
          }

          break;
        }
      }
      if (empty($fieldData['data'][$fieldPair->zohoFormField]) && \in_array($fieldPair->zohoFormField, $required)) {
        $error = new WP_Error('REQ_FIELD_EMPTY', wp_sprintf(__('%s is required for zoho creator', 'bit-form'), $fieldPair->zohoFormField));
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'field'], 'validation', $error);
        return $error;
      }
    }

    $recordApiResponse = '';

    $allReports = $this->getAllReports($dataCenter, $accountOwner, $applicationId);

    $reportId = $allReports->reports[0]->link_name;

    if (isset($actions->update->criteria)) {
      $fieldData['criteria'] = $actions->update->criteria;
      $recordApiResponse = $this->updateRecord($dataCenter, $accountOwner, $applicationId, $reportId, wp_json_encode($fieldData));
      if (isset($recordApiResponse->code) && 3000 === $recordApiResponse->code) {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'update'], 'success', $recordApiResponse);
      } else {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'update'], 'error', $recordApiResponse);
      }

      unset($fieldData['criteria']);

      if ($actions->update->insert && isset($recordApiResponse->message) && 'No Data Available' === $recordApiResponse->message) {
        $recordApiResponse = $this->insertRecord($dataCenter, $accountOwner, $applicationId, $formId, wp_json_encode($fieldData));
        if (isset($recordApiResponse->code) && 3000 === $recordApiResponse->code) {
          $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'insert'], 'success', $recordApiResponse);
        } else {
          $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'insert'], 'error', $recordApiResponse);
        }
      }
    } else {
      $recordApiResponse = $this->insertRecord($dataCenter, $accountOwner, $applicationId, $formId, wp_json_encode($fieldData));
      if (isset($recordApiResponse->code) && 3000 === $recordApiResponse->code) {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'insert'], 'success', $recordApiResponse);
      } else {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'insert'], 'error', $recordApiResponse);
      }
    }

    if (isset($recordApiResponse->result)) {
      foreach ($recordApiResponse->result as $record) {
        $recordId = $record->data->ID;
        $this->uploadFileToRecord($uploadFieldMap, $fieldValues, $dataCenter, $formID, $entryID, $accountOwner, $applicationId, $reportId, $recordId);
      }
    } else {
      $recordId = $recordApiResponse->data->ID;

      $this->uploadFileToRecord($uploadFieldMap, $fieldValues, $dataCenter, $formID, $entryID, $accountOwner, $applicationId, $reportId, $recordId);
    }

    return $recordApiResponse;
  }

  private function uploadFileToRecord($uploadFieldMap, $fieldValues, $dataCenter, $formID, $entryID,  $accountOwner, $applicationId, $reportId, $recordId)
  {
    $fileFound = 0;
    $fileApiResponses = [];
    $responseType = 'success';
    foreach ($uploadFieldMap as $uploadField) {
      if (!empty($uploadField->formField) && !empty($uploadField->zohoFormField)) {
        $filesApiHelper = new FilesApiHelper($this->_tokenDetails, $formID, $entryID);
        if (isset($fieldValues[$uploadField->formField]) && !empty($fieldValues[$uploadField->formField])) {
          $fileFound = 1;
          if (is_array($fieldValues[$uploadField->formField])) {
            foreach ($fieldValues[$uploadField->formField] as $singleFile) {
              $fileApiResponse = $filesApiHelper->uploadFiles($dataCenter, $singleFile, $accountOwner, $applicationId, $reportId, $recordId, $uploadField->zohoFormField);
              if (isset($fileApiResponse->code) && 3000 !== $fileApiResponse->code) {
                $responseType = 'error';
              }
              $fileApiResponses[] = $fileApiResponse;
            }
          } else {
            $fileApiResponse = $filesApiHelper->uploadFiles($dataCenter, $fieldValues[$uploadField->formField], $accountOwner, $applicationId, $reportId, $recordId, $uploadField->zohoFormField);
            if (isset($fileApiResponse->code) && 3000 !== $fileApiResponse->code) {
              $responseType = 'error';
            }
            $fileApiResponses[] = $fileApiResponse;
          }
        }
      }
    }

    if ($fileFound) {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'file', 'type_name' => 'form'], $responseType, $fileApiResponses);
    }

    return $fileApiResponses;
  }
}
