<?php

/**
 * ZohoRecruit Record Api
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoSign;

use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\FieldValueHandler;
use BitCode\BitForm\Core\Util\HttpHelper;

/**
 * Provide functionality for Record insert,upsert
 */
class RecordApiHelper
{
  private $_defaultHeader;
  private $_tokenDetails;
  private $_logID;
  private $_integrationID;
  private $_logResponse;

  public function __construct($tokenDetails, $integId, $logID)
  {
    $this->_tokenDetails = $tokenDetails;
    $this->_defaultHeader['Authorization'] = "Zoho-oauthtoken {$tokenDetails->access_token}";
    $this->_defaultHeader['Content-Type'] = 'application/json';
    $this->_integrationID = $integId;
    $this->_logID = $logID;
    $this->_logResponse = new UtilApiResponse();
  }

  public function insertRecord($dataCenter, $data)
  {
    $insertRecordEndpoint = "https://sign.zoho.{$dataCenter}/api/accounts/{$this->_defaultHeader['accountId']}/messages";
    return HttpHelper::post($insertRecordEndpoint, $data, $this->_defaultHeader);
  }

  private function sendDocument($dataCenter, $template, $data)
  {
    $sendDocumentEndpoint = "https://sign.zoho.{$dataCenter}/api/v1/templates/{$template}/createdocument?data={$data}";
    return HttpHelper::post($sendDocumentEndpoint, null, $this->_defaultHeader);
  }

  public function executeRecordApi($dataCenter, $template, $templateActions, $notes, $fieldValues)
  {
    $notes = FieldValueHandler::replaceFieldWithValue($notes, $fieldValues);

    foreach ($templateActions as $action) {
      if (!empty($action->in_person_email)) {
        $action->in_person_email = FieldValueHandler::replaceFieldWithValue($action->in_person_email, $fieldValues);
      }
      if (!empty($action->in_person_name)) {
        $action->in_person_name = FieldValueHandler::replaceFieldWithValue($action->in_person_name, $fieldValues);
      }
      if (!empty($action->recipient_email)) {
        $action->recipient_email = FieldValueHandler::replaceFieldWithValue($action->recipient_email, $fieldValues);
      }
      if (!empty($action->recipient_name)) {
        $action->recipient_name = FieldValueHandler::replaceFieldWithValue($action->recipient_name, $fieldValues);
      }
    }

    $actions = wp_json_encode($templateActions);

    $data = '{"templates":{"notes":"' . $notes . '","field_data":{"field_text_data":{},"field_boolean_data":{},"field_date_data":{}},"actions":' . $actions . '}}';

    $recordApiResponse = $this->sendDocument($dataCenter, $template, $data);
    if (isset($recordApiResponse->error_param) || 'failure' === $recordApiResponse->status || 'error' === $recordApiResponse->status) {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'send', 'type_name' => 'template'], 'error', $recordApiResponse);
    } else {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'send', 'type_name' => 'template'], 'success', $recordApiResponse);
    }

    return $recordApiResponse;
  }
}
