<?php

/**
 * SendFox Record Api
 */

namespace BitCode\BitForm\Core\Integration\SendFox;

use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\HttpHelper;

/**
 * Provide functionality for Record insert, upsert
 */
class RecordApiHelper
{
  private $_integrationID;
  private $logID;

  private $_logResponse;

  public function __construct($auth_token, $integId, $logID, $entryID)
  {
    $this->_integrationID = $integId;
    $this->_logResponse = new UtilApiResponse();
    $this->logID = $logID;
  }

  public function addContact($access_token, $listId, $finalData)
  {
    $apiEndpoints = 'https://api.sendfox.com/contacts';
    $listId = explode(',', $listId);
    $header = [
      'Authorization' => "Bearer {$access_token}",
      'Accept'        => 'application/json',
    ];

    $data = [
      'email'      => $finalData['email'],
      'first_name' => $finalData['first_name'],
      'last_name'  => $finalData['last_name'],
      'lists'      => $listId,
    ];

    return HttpHelper::post($apiEndpoints, $data, $header);
  }

  public function createContactList($access_token, $finalData)
  {
    $apiEndpoints = 'https://api.sendfox.com/lists';

    $header = [
      'Authorization' => "Bearer {$access_token}",
      'Accept'        => 'application/json',
    ];

    $data = [
      'name' => $finalData['name'],
    ];

    return HttpHelper::post($apiEndpoints, $data, $header);
  }

  public function generateReqDataFromFieldMap($data, $fieldMap)
  {
    $dataFinal = [];

    foreach ($fieldMap as $key => $value) {
      $triggerValue = $value->formField;
      $actionValue = $value->sendFoxFormField;
      if ('custom' === $triggerValue) {
        $dataFinal[$actionValue] = $value->customValue;
      } elseif (!is_null($data[$triggerValue])) {
        $dataFinal[$actionValue] = $data[$triggerValue];
      }
    }
    return $dataFinal;
  }

  public function generateListReqDataFromFieldMap($data, $fieldMap)
  {
    $dataFinal = [];

    foreach ($fieldMap as $key => $value) {
      $triggerValue = $value->formField;
      $actionValue = $value->sendFoxListFormField;
      if ('custom' === $triggerValue) {
        $dataFinal[$actionValue] = $value->customValue;
      } elseif (!is_null($data[$triggerValue])) {
        $dataFinal[$actionValue] = $data[$triggerValue];
      }
    }
    return $dataFinal;
  }

  public function generateReqUnsubscribeDataFromFieldMap($data, $fieldMap)
  {
    $dataFinal = [];

    foreach ($fieldMap as $key => $value) {
      $triggerValue = $value->formField;
      $actionValue = $value->sendFoxUnsubscribeFormField;
      if ('custom' === $triggerValue) {
        $dataFinal[$actionValue] = $value->customValue;
      } elseif (!is_null($data[$triggerValue])) {
        $dataFinal[$actionValue] = $data[$triggerValue];
      }
    }
    return $dataFinal;
  }

  public function unsubscribeContact($access_token, $finalData)
  {
    $apiEndpoints = 'https://api.sendfox.com/unsubscribe';

    $header = [
      'Authorization' => "Bearer {$access_token}",
      'Accept'        => 'application/json',
    ];

    $data = [
      'email' => $finalData['email'],
    ];
    return HttpHelper::request($apiEndpoints, 'PATCH', $data, $header);
  }

  public function execute(
    $listId,
    $mainAction,
    $fieldValues,
    $fieldMap,
    $access_token,
    $integrationDetails
  ) {
    $fieldData = [];
    $apiResponse = null;
    if ('1' === $integrationDetails->mainAction) {
      $type_name = 'Create List';
      $finalData = $this->generateListReqDataFromFieldMap($fieldValues, $integrationDetails->field_map_list);
      $apiResponse = $this->createContactList($access_token, $finalData);

      if (property_exists($apiResponse, 'id')) {
        $this->_logResponse->apiResponse($this->logID, $this->_integrationID, ['type' => 'record', 'type_name' => $type_name], 'success', $apiResponse);
      } else {
        $this->_logResponse->apiResponse($this->logID, $this->_integrationID, ['type' => 'record', 'type_name' => $type_name], 'error', $apiResponse);
      }
    }
    if ('2' === $integrationDetails->mainAction) {
      $type_name = 'Create Contact';
      $finalData = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
      $apiResponse = $this->addContact($access_token, $listId, $finalData);
      if (property_exists($apiResponse, 'errors')) {
        $this->_logResponse->apiResponse($this->logID, $this->_integrationID, ['type' => 'record', 'type_name' => $type_name], 'error', $apiResponse);
      } else {
        $this->_logResponse->apiResponse($this->logID, $this->_integrationID, ['type' => 'record', 'type_name' => $type_name], 'success', $apiResponse);
      }
    }

    if ('3' === $integrationDetails->mainAction) {
      $type_name = 'Unsubscribe';
      $finalData = $this->generateReqUnsubscribeDataFromFieldMap($fieldValues, $integrationDetails->field_map_unsubscribe);
      $apiResponse = $this->unsubscribeContact($access_token, $finalData);
      if (property_exists($apiResponse, 'id')) {
        $this->_logResponse->apiResponse($this->logID, $this->_integrationID, ['type' => 'record', 'type_name' => $type_name], 'success', $apiResponse);
      } else {
        $this->_logResponse->apiResponse($this->logID, $this->_integrationID, ['type' => 'record', 'type_name' => $type_name], 'error', $apiResponse);
      }
    }

    return $apiResponse;
  }
}
