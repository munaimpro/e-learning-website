<?php

/**
 * ZohoRecruit Record Api
 *
 */

namespace BitCode\BitForm\Core\Integration\MailPoet;

use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use MailPoet\API\MP\v1\APIException;

/**
 * Provide functionality for Record insert,upsert
 */
class RecordApiHelper
{
  private $_integrationID;

  private $_logID;

  private $_logResponse;

  public function __construct($integId, $logID, $entryID)
  {
    $this->_integrationID = $integId;
    $this->_logID = $logID;
    $this->_logResponse = new UtilApiResponse();
  }

  public function insertRecord($subscriber, $lists)
  {
    if (class_exists(\MailPoet\API\API::class)) {
      $mailpoet_api = \MailPoet\API\API::MP('v1');
      try {
        $response = $mailpoet_api->addSubscriber($subscriber, $lists);
        $response = [
          'success'=> true,
          'id'     => $response['id']
        ];
      } catch (APIException $e) {
        $response = [
          'success' => false,
          'code'    => $e->getCode(),
          'message' => $e->getMessage()
        ];
      }
      return $response;
    }
  }

  public function executeRecordApi($fieldValues, $fieldMap, $lists)
  {
    $fieldData = [];

    foreach ($fieldMap as $fieldKey => $fieldPair) {
      if (!empty($fieldPair->mailPoetField)) {
        if ('custom' === $fieldPair->formField && isset($fieldPair->customValue)) {
          $fieldData[$fieldPair->mailPoetField] = $fieldPair->customValue;
        } else {
          $fieldData[$fieldPair->mailPoetField] = $fieldValues[$fieldPair->formField];
        }
      }
    }

    $recordApiResponse = $this->insertRecord($fieldData, $lists);
    if ($recordApiResponse['success']) {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' =>  'record', 'type_name' => 'insert'], 'success', $recordApiResponse);
    } else {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' =>  'record', 'type_name' => 'insert'], 'error', $recordApiResponse);
    }

    return $recordApiResponse;
  }
}
