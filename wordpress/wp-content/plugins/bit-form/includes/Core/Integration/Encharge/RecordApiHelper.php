<?php

/**
 * Encharge Record Api
 */

namespace BitCode\BitForm\Core\Integration\Encharge;

use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\HttpHelper;

/**
 * Provide functionality for Record insert
 */
class RecordApiHelper
{
  private $_defaultHeader;
  private $_integrationID;
  private $_logID;
  private $_logResponse;

  public function __construct($api_key, $integId, $logID, $entryID)
  {
    // wp_send_json_success($tokenDetails);
    $this->_defaultHeader['Content-Type'] = 'application/json';
    $this->_defaultHeader['X-Encharge-Token'] = $api_key;
    $this->_integrationID = $integId;
    $this->_logID = $logID;
    $this->_logResponse = new UtilApiResponse();
  }

  /**
   * serd data to api
   * @return json response
   */
  public function insertRecord($data)
  {
    $insertRecordEndpoint = 'https://api.encharge.io/v1/people';
    return HttpHelper::post($insertRecordEndpoint, $data, $this->_defaultHeader);
  }

  public function executeRecordApi($fieldValues, $fieldMap, $tags)
  {
    $fieldData = [];

    foreach ($fieldMap as $fieldKey => $fieldPair) {
      if (!empty($fieldPair->enChargeFields)) {
        if ('custom' === $fieldPair->formField && isset($fieldPair->customValue)) {
          $fieldData[$fieldPair->enChargeFields] = $fieldPair->customValue;
        } else {
          $fieldData[$fieldPair->enChargeFields] = $fieldValues[$fieldPair->formField];
        }
      }
    }
    if (null !== $tags) {
      $fieldData['tags'] = $tags;
    }
    // wp_send_json_success($fieldData);
    $recordApiResponse = $this->insertRecord(wp_json_encode($fieldData));
    $type = 'insert';

    if ($recordApiResponse && isset($recordApiResponse->user)) {
      $recordApiResponse = [
        'status' => 'success',
        'email'  => $recordApiResponse->user->email
      ];
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' =>  'record', 'type_name' => $type], 'success', $recordApiResponse);
    } else {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' =>  'record', 'type_name' => $type], 'error', $recordApiResponse);
    }
    return $recordApiResponse;
  }
}
