<?php

/**
 * ZohoRecruit Record Api
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoMarketingHub;

use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
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

  private $_logID;
  private $_logResponse;

  private $_integrationID;

  public function __construct($tokenDetails, $integId, $logID)
  {
    $this->_defaultHeader['Authorization'] = "Zoho-oauthtoken {$tokenDetails->access_token}";
    $this->_apiDomain = \urldecode($tokenDetails->api_domain);
    $this->_tokenDetails = $tokenDetails;
    $this->_integrationID = $integId;
    $this->_logID = $logID;
    $this->_logResponse = new UtilApiResponse();
  }

  public function insertRecord($list, $dataCenter, $data)
  {
    $insertRecordEndpoint = "https://marketinghub.zoho.{$dataCenter}/api/v1/json/listsubscribe?resfmt=JSON&listkey={$list}&leadinfo=" . urlencode($data);
    return HttpHelper::post($insertRecordEndpoint, null, $this->_defaultHeader);
  }

  public function executeRecordApi($list, $dataCenter, $fieldValues, $fieldMap, $required)
  {
    $fieldData = [];
    foreach ($fieldMap as $fieldPair) {
      if (!empty($fieldPair->zohoFormField)) {
        if ('custom' === $fieldPair->formField && isset($fieldPair->customValue)) {
          $fieldData[$fieldPair->zohoFormField] = $fieldPair->customValue;
        } else {
          $fieldData[$fieldPair->zohoFormField] = $fieldValues[$fieldPair->formField];
        }
      }

      if (empty($fieldData[$fieldPair->zohoFormField]) && \in_array($fieldPair->zohoFormField, $required)) {
        $error = new WP_Error('REQ_FIELD_EMPTY', wp_sprintf('%s ', __('is required for zoho marketing hub', 'bit-form'), $fieldPair->zohoFormField));
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'field'], 'validation', $error);
        return $error;
      }
    }

    $recordApiResponse = $this->insertRecord($list, $dataCenter, wp_json_encode($fieldData));
    if (isset($recordApiResponse->status) && 'success' === $recordApiResponse->status) {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'list'], 'success', $recordApiResponse);
    } else {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'list'], 'error', $recordApiResponse);
    }
    return $recordApiResponse;
  }
}
