<?php

namespace BitCode\BitForm\Core\Integration\Rapidmail;

use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\HttpHelper;

/**
 * Provide functionality for Record insert,upsert
 */
class RecordApiHelper
{
  protected $_defaultHeader;
  public static $apiBaseUri = 'https://apiv3.emailsys.net/v1';

  private $integrationDetails;

  private $logID;

  private $_logResponse;

  public function __construct($integrationDetails, $username, $password, $logID)
  {
    $this->integrationDetails = $integrationDetails;
    $this->_defaultHeader = [
      'Authorization' => 'Basic ' . base64_encode("$username:$password"),
      'Accept'        => '*/*',
      'Content-Type'  => 'application/json',
      'verify'        => false
    ];
    $this->_logResponse = new UtilApiResponse();
    $this->logID = $logID;
  }

  public function insertRecipientRecord($data)
  {
    $insertRecordEndpoint = self::$apiBaseUri . '/recipients';
    $data = \is_string($data) ? $data : wp_json_encode((object)$data);
    $response = HttpHelper::post($insertRecordEndpoint, $data, $this->_defaultHeader);
    return $response;
  }

  public function generateReqDataFromFieldMap($data, $fieldMap)
  {
    $dataFinal = [];

    foreach ($fieldMap as $value) {
      $triggerValue = $value->formField;
      $actionValue = $value->rapidmailFormField;
      if ('custom' === $triggerValue) {
        $dataFinal[$actionValue] = $value->customValue;
      } elseif (!is_null($data[$triggerValue])) {
        if (strtotime($data[$triggerValue])) {
          $dataFinal[$actionValue] = date('Y-m-d', strtotime($data[$triggerValue]));
        } else {
          $dataFinal[$actionValue] = $data[$triggerValue];
        }
      }
    }

    $selectedRecipientList = $this->integrationDetails->recipient_id;
    if ($this->integrationDetails->actions) {
      if (property_exists($this->integrationDetails->actions, 'send_activationmail') && $this->integrationDetails->actions->send_activationmail) {
        $dataFinal['send_activationmail'] = 'yes';
      }
    }
    $dataFinal['recipientlist_id'] = (int)$selectedRecipientList;
    return $dataFinal;
  }

  public function executeRecordApi($integId, $defaultConf, $recipientLists, $fieldValues, $fieldMap, $actions)
  {
    $finalData = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
    $apiResponse = $this->insertRecipientRecord($finalData);

    if (!property_exists($apiResponse, 'recipientlist_id')) {
      $this->_logResponse->apiResponse($this->logID, $integId, ['type' => 'recipient', 'type_name' => 'add'], 'errors', $apiResponse);
    } else {
      $this->_logResponse->apiResponse($this->logID, $integId, ['type' => 'recipient', 'type_name' => 'add'], 'success', $apiResponse);
    }
    return $apiResponse;
  }
}
