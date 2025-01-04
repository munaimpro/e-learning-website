<?php

/**
 * ZohoCrm Record Api
 *
 */

namespace BitCode\BitForm\Core\Integration\Hubspot;

use BitCode\BitForm\Core\Util\ApiResponse;
use BitCode\BitForm\Core\Util\HttpHelper;

/**
 * Provide functionality for Record insert,upsert
 */
class HubspotRecordApiHelper
{
  public $_header = [];
  public $_apiBaseUrl = 'https://api.hubapi.com/crm/v3/objects';

  private $_logResponse;
  private $logID;

  public function __construct($logID, $apiKey)
  {
    $this->_logResponse = new ApiResponse();
    $this->logID = $logID;

    $this->_header = [
      'Content-Type'  => 'application/json',
      'authorization' => "Bearer {$apiKey}",
    ];
  }

  public function insertContact($data)
  {
    $organizedData = wp_json_encode([
      'properties' => $data,
    ]);
    $contactsEndPoint = "{$this->_apiBaseUrl}/contacts";
    $response = HttpHelper::post($contactsEndPoint, $organizedData, $this->_header);
    return $response;
  }

  public function insertDeal($finalData)
  {
    $data = wp_json_encode($finalData);
    $dealsEndPoint = "{$this->_apiBaseUrl}/deals";
    $response = HttpHelper::post($dealsEndPoint, $data, $this->_header);
    return $response;
  }

  public function insertTicket($finalData)
  {
    $organizedData = wp_json_encode([
      'properties' => $finalData,
    ]);
    $ticketsEndPoint = "{$this->_apiBaseUrl}/tickets";
    $response = HttpHelper::post($ticketsEndPoint, $organizedData, $this->_header);
    return $response;
  }

  public function generateReqDataFromField($data, $fieldMap, $integrationDetails)
  {
    $dataFinal = [];

    $dataFinal = Common::formFldMapping($dataFinal, $fieldMap, $data);

    $action = $integrationDetails->actions;
    $dataFinal = Common::customFldMapping('lead', $action, $integrationDetails, $dataFinal);

    return $dataFinal;
  }

  public function formatDealField($data, $fieldMap, $integrationDetails)
  {
    $dataFinal = [];
    $dataFinal = Common::formFldMapping($dataFinal, $fieldMap, $data, 'deal');

    $pipeline = $integrationDetails->pipeline;
    $stage = $integrationDetails->stage;
    $dataFinal['pipeline'] = $pipeline;
    $dataFinal['dealstage'] = $stage;
    $action = $integrationDetails->actions;
    $dataForAssosciations = [];
    if (!empty($action->action)) {
      $dataFinal = Common::customFldMapping('deal', $action, $integrationDetails, $dataFinal);
      $dataForAssosciations = Common::customFldMapping('company', $action, $integrationDetails, $dataForAssosciations);
    }

    $finalData = [];
    $finalData['properties'] = $dataFinal;
    $finalData['associations'] = (object) $dataForAssosciations;
    return $finalData;
  }

  public function formatTicketField($data, $fieldMap, $integrationDetails)
  {
    $dataFinal = [];
    $dataFinal = Common::formFldMapping($dataFinal, $fieldMap, $data);
    $pipeline = $integrationDetails->pipeline;
    $stage = $integrationDetails->stage;
    $dataFinal['hs_pipeline'] = $pipeline;
    $dataFinal['hs_pipeline_stage'] = $stage;

    $action = $integrationDetails->actions;

    if (property_exists($action, 'contact_owner')) {
      $owner = $integrationDetails->contact_owner;
      $dataFinal['hubspot_owner_id'] = $owner;
    }
    if (property_exists($action, 'priority')) {
      $priority = $integrationDetails->priority;
      $dataFinal['hs_ticket_priority'] = $priority ? strtoupper($priority) : 'HIGH';
    }

    return $dataFinal;
  }

  public function executeRecordApi($integId, $integrationDetails, $fieldValues, $fieldMap)
  {
    $actionName = $integrationDetails->actionName;
    $type = '';
    $typeName = '';
    if ('contact-create' === $actionName) {
      $finalData = $this->generateReqDataFromField($fieldValues, $fieldMap, $integrationDetails);
      $apiResponse = $this->insertContact($finalData);
      $type = 'contact';
      $typeName = 'contact-add';
    } elseif ('deal-create' === $actionName) {
      $finalData = $this->formatDealField($fieldValues, $fieldMap, $integrationDetails);
      $apiResponse = $this->insertDeal($finalData);
      $type = 'deal';
      $typeName = 'deal-add';
    } elseif ('ticket-create' === $actionName) {
      $finalData = $this->formatTicketField($fieldValues, $fieldMap, $integrationDetails);
      $apiResponse = $this->insertTicket($finalData);
      $type = 'ticket';
      $typeName = 'ticket-add';
    }

    if (!isset($apiResponse->properties)) {
      $this->_logResponse->apiResponse($this->logID, $integId, ['type' => $type, 'type_name' => $typeName], 'errors', $apiResponse);
    } else {
      $this->_logResponse->apiResponse($this->logID, $integId, ['type' => $type, 'type_name' => $typeName], 'success', wp_json_encode($apiResponse));
    }
    return $apiResponse;
  }
}
