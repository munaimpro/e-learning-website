<?php

/**
 * ZohoRecruit Record Api
 *
 */

namespace BitCode\BitForm\Core\Integration\SendinBlue;

use BitCode\BitForm\Core\Database\FormEntryLogModel;
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

  private const BREVO_API_ENDPOINT = 'https://api.brevo.com/v3';

  public function __construct($api_key, $integId, $logID, $entryID)
  {
    // wp_send_json_success($tokenDetails);
    $this->_defaultHeader['Content-Type'] = 'application/json';
    $this->_defaultHeader['api-key'] = $api_key;
    $this->_integrationID = $integId;
    $this->_logID = $logID;
    $this->_logResponse = new UtilApiResponse();
    $this->_entryID = $entryID;
  }

  public function insertRecord($data)
  {
    $insertRecordEndpoint = self::BREVO_API_ENDPOINT . '/contacts';
    return HttpHelper::post($insertRecordEndpoint, $data, $this->_defaultHeader);
  }

  public function updateRecord($id, $data)
  {
    $updateRecordEndpoint = self::BREVO_API_ENDPOINT . "/contacts/{$id}";
    return HttpHelper::request($updateRecordEndpoint, 'PUT', $data, $this->_defaultHeader);
  }

  public function executeRecordApi($lists, $defaultDataConf, $fieldValues, $fieldMap, $actions)
  {
    $fieldData = [];
    $attributes = [];
    foreach ($fieldMap as $fieldPair) {
      if (!empty($fieldPair->sendinBlueField)) {
        if ('email' === $fieldPair->sendinBlueField) {
          $fieldData['email'] = $fieldValues[$fieldPair->formField];
        } elseif ('custom' === $fieldPair->formField && isset($fieldPair->customValue)) {
          $attributes[$fieldPair->sendinBlueField] = $fieldPair->customValue;
        } else {
          $attributes[$fieldPair->sendinBlueField] = $fieldValues[$fieldPair->formField];
        }
      }
    }
    $arrLists = array_map(function ($val) {
      return (int) $val;
    }, $lists);

    $fieldData['attributes'] = (object) $attributes;
    $fieldData['listIds'] = $arrLists;
    $model = new FormEntryLogModel();

    $recordApiResponse = null;
    $type = null;
    if ($this->_entryID) {
      $result = $model->entryLogCheck($this->_entryID, $this->_integrationID);
      if (!count($result) || isset($result->errors['result_empty'])) {
        $recordApiResponse = $this->insertRecord(wp_json_encode($fieldData));
        $type = 'insert';

        if (!empty($actions->update) && !empty($recordApiResponse->message) && 'Contact already exist' === $recordApiResponse->message) {
          $contactEmail = $fieldData['email'];
          $recordApiResponse = $this->updateRecord($contactEmail, wp_json_encode($fieldData));
          if (empty($recordApiResponse)) {
            $recordApiResponse = ['success' => true, 'id' => $fieldData['email']];
          }
          $type = 'update';
        }
      } else {
        $contactId = json_decode($result[0]->response_obj);
        $recordApiResponse = $this->updateRecord($contactId->id, wp_json_encode($fieldData));
        if (empty($recordApiResponse)) {
          $recordApiResponse = ['success' => true, 'id' => $contactId->id];
        }
        $type = 'update';
      }
    }

    if ($recordApiResponse && isset($recordApiResponse->code)) {
      $this->_logResponse->apiResponse(
        $this->_logID,
        $this->_integrationID,
        ['type' => 'record', 'type_name' => $type],
        'error',
        $recordApiResponse
      );
    } else {
      $this->_logResponse->apiResponse(
        $this->_logID,
        $this->_integrationID,
        ['type' => 'record', 'type_name' => $type],
        'success',
        $recordApiResponse
      );
    }
    return $recordApiResponse;
  }
}
