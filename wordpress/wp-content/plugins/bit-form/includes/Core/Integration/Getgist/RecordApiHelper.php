<?php

namespace BitCode\BitForm\Core\Integration\Getgist;

use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\HttpHelper;

class RecordApiHelper
{
  private $_defaultHeader;

  private $_logResponse;

  private $logID;

  public function __construct($api_key, $integId, $logID)
  {
    $this->_defaultHeader['Content-Type'] = 'application/json';
    $this->_defaultHeader['Authorization'] = "Bearer $api_key";
    $this->_logResponse = new UtilApiResponse();
    $this->logID = $logID;
  }

  public function createContact($data)
  {
    $data = \is_string($data) ? $data : wp_json_encode((object)$data);
    $insertRecordEndpoint = 'https://api.getgist.com/contacts';
    return HttpHelper::post($insertRecordEndpoint, $data, $this->_defaultHeader);
  }

  public function generateReqDataFromFieldMap($data, $fieldMap, $integrationDetails)
  {
    $dataFinal = [];

    foreach ($fieldMap as $value) {
      $triggerValue = $value->formField;
      $actionValue = $value->getgistFormField;
      if ('custom' === $triggerValue) {
        $dataFinal[$actionValue] = $value->customValue;
      } elseif (!is_null($data[$triggerValue])) {
        $dataFinal[$actionValue] = $data[$triggerValue];
      }
    }

    if (property_exists($integrationDetails, 'user_type') && property_exists($integrationDetails, 'userId') && 'User' === $integrationDetails->user_type) {
      $dataFinal['user_id'] = $integrationDetails->userId;
    }

    if ($integrationDetails->actions) {
      if (property_exists($integrationDetails->actions, 'tags') && $integrationDetails->actions->tags) {
        $dataFinal['tags'] = explode(',', $integrationDetails->tags);
      }
    }
    return $dataFinal;
  }

  public function execute($integId, $fieldValues, $fieldMap, $integrationDetails)
  {
    $finalData = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap, $integrationDetails);
    $apiResponse = $this->createContact($finalData);

    if (!property_exists($apiResponse, 'contact') || property_exists($apiResponse, 'errors')) {
      $this->_logResponse->apiResponse($this->logID, $integId, ['type' => 'contact', 'type_name' => 'contact_add'], 'errors', $apiResponse);
    } else {
      $this->_logResponse->apiResponse($this->logID, $integId, ['type' => 'contact', 'type_name' => 'contact_add'], 'success', $apiResponse);
    }
    return $apiResponse;
  }
}
