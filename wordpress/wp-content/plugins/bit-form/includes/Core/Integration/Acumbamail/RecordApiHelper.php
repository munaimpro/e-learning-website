<?php

namespace BitCode\BitForm\Core\Integration\Acumbamail;

use BitCode\BitForm\Core\Util\ApiResponse;
use BitCode\BitForm\Core\Util\HttpHelper;

/**
 * Provide functionality for Record insert, upsert
 */
class RecordApiHelper
{
  private $_integrationID;
  private $_logID;

  private $_logResponse;

  public function __construct($auth_token, $integId, $logID, $entryID)
  {
    $this->_integrationID = $integId;
    $this->_logResponse = new ApiResponse();
    $this->_logID = $logID;
  }

  public function addSubscriber($auth_token, $listId, $finalData)
  {
    $apiEndpoints = 'https://acumbamail.com/api/1/addSubscriber/';
    $header = [
      'Content-Type' => 'application/x-www-form-urlencoded'
    ];

    $requestParams = [
      'auth_token'          => $auth_token,
      'list_id'             => $listId,
      'welcome_email'       => 1,
      'update_subscriber'   => 1,
      'merge_fields[EMAIL]' => $finalData['email'],

    ];
    foreach ($finalData as $key => $value) {
      if ('email' !== $key) {
        $requestParams['merge_fields[' . $key . ']'] = $value;
      }
    }
    return HttpHelper::post($apiEndpoints, $requestParams, $header);
  }

  public function deleteSubscriber($auth_token, $listId, $finalData)
  {
    $apiEndpoints = 'https://acumbamail.com/api/1/deleteSubscriber/';

    $header = [
      'Content-Type' => 'application/x-www-form-urlencoded'
    ];

    $requestParams = [
      'auth_token' => $auth_token,
      'list_id'    => $listId,
      'email'      => $finalData['email'],
    ];

    return HttpHelper::post($apiEndpoints, $requestParams, $header);
  }

  public function generateReqDataFromFieldMap($data, $fieldMap)
  {
    $dataFinal = [];

    foreach ($fieldMap as $value) {
      $triggerValue = $value->formField;
      $actionValue = $value->acumbamailFormField;
      if ('custom' === $triggerValue) {
        $dataFinal[$actionValue] = $value->customValue;
      } elseif (!is_null($data[$triggerValue])) {
        $dataFinal[$actionValue] = $data[$triggerValue];
      }
    }
    return $dataFinal;
  }

  public function execute(
    $listId,
    $mainAction,
    $defaultDataConf,
    $fieldValues,
    $fieldMap,
    $auth_token
  ) {
    $finalData = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
    $apiResponse = null;
    $type = null;
    if ('1' === $mainAction) {
      $apiResponse = $this->addSubscriber($auth_token, $listId, $finalData);
      $type = 'add subscriber';
    } elseif ('2' === $mainAction) {
      $apiResponse = $this->deleteSubscriber($auth_token, $listId, $finalData);
      $type = 'delete subscriber';
    }

    if (property_exists($apiResponse, 'errors')) {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => $type], 'errors', $apiResponse);
    } else {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => $type], 'success', $apiResponse);
    }
    return $apiResponse;
  }
}
