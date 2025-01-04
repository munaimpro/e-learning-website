<?php

/**
 * ZohoRecruit Record Api
 *
 */

namespace BitCode\BitForm\Core\Integration\GoogleSheet;

use BitCode\BitForm\Core\Database\FormEntryLogModel;
use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\HttpHelper;

/**
 * Provide functionality for Record insert,upsert
 */
class RecordApiHelper
{
  private $_defaultHeader;

  private $_integrationID;

  private $_logID;

  private $_logResponse;

  private $_entryID;

  public function __construct($tokenDetails, $integId, $logID, $entryID)
  {
    $this->_defaultHeader['Authorization'] = "Bearer {$tokenDetails->access_token}";
    $this->_defaultHeader['Content-Type'] = 'application/json';
    $this->_integrationID = $integId;
    $this->_logID = $logID;
    $this->_logResponse = new UtilApiResponse();
    $this->_entryID = $entryID;
  }

  public function insertRecord($spreadsheetsId, $worksheetName, $header, $headerRow, $data)
  {
    $insertRecordEndpoint = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetsId}/values/{$worksheetName}!{$headerRow}:append?valueInputOption=USER_ENTERED";
    return HttpHelper::post($insertRecordEndpoint, $data, $this->_defaultHeader);
  }

  public function updateRecord($spreadsheetId, $worksheetInfo, $data)
  {
    $updateRecordEndpoint = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$worksheetInfo}?valueInputOption=USER_ENTERED";
    return HttpHelper::request($updateRecordEndpoint, 'put', $data, $this->_defaultHeader);
  }

  public function executeRecordApi($spreadsheetId, $worksheetName, $headerRow, $header, $actions, $defaultConf, $fieldValues, $fieldMap)
  {
    $fieldData = [];
    $allHeaders = $defaultConf->headers->{$spreadsheetId}->{$worksheetName}->{$headerRow};
    $formattedFieldValues = IntegrationHandler::formattedRepeaterValue($fieldValues, 'string');

    foreach ($fieldMap as $fieldPair) {
      if (!empty($fieldPair->googleSheetField)) {
        if ('custom' === $fieldPair->formField && isset($fieldPair->customValue)) {
          $fieldData[$fieldPair->googleSheetField] = $fieldPair->customValue;
        } else {
          $fieldData[$fieldPair->googleSheetField] = $formattedFieldValues[$fieldPair->formField];
        }
      }
    }

    $values = [];

    foreach ($allHeaders as $googleSheetHeader) {
      if (!empty($fieldData[$googleSheetHeader])) {
        if ('array' === gettype($fieldData[$googleSheetHeader])) {
          $values[] = implode(', ', $fieldData[$googleSheetHeader]);
        } else {
          $values[] = $fieldData[$googleSheetHeader];
        }
      } else {
        $values[] = '';
      }
    }

    $data = [];
    $data['range'] = "{$worksheetName}!$headerRow";
    $data['majorDimension'] = "{$header}";
    $data['values'][] = $values;

    $model = new FormEntryLogModel();
    $recordApiResponse = null;
    if ($this->_entryID) {
      $result = $model->entryLogCheck($this->_entryID, $this->_integrationID);
      if (!count($result) || isset($result->errors['result_empty'])) {
        $recordApiResponse = $this->insertRecord($spreadsheetId, $worksheetName, $header, $headerRow, wp_json_encode($data));
        $type = 'insert';
      } else {
        $result = json_decode($result[0]->response_obj);

        if (isset($result->updates)) {
          $worksheetInfo = $result->updates->updatedRange;
        } else {
          $worksheetInfo = $result->updatedRange;
        }
        $spreadsheetId = $result->spreadsheetId;
        unset($data['range']);
        $recordApiResponse = $this->updateRecord($spreadsheetId, $worksheetInfo, wp_json_encode($data));
        $type = 'update';
      }
      if (isset($recordApiResponse->error)) {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' =>  'record', 'type_name' => $type], 'error', $recordApiResponse);
      } else {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' =>  'record', 'type_name' => $type], 'success', $recordApiResponse);
      }
    }

    return $recordApiResponse;
  }
}
