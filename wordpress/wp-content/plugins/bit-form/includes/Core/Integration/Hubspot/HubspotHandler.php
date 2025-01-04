<?php

namespace BitCode\BitForm\Core\Integration\Hubspot;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\HttpHelper;
use WP_Error;

final class HubspotHandler
{
  private $_integrationID;
  public static $apiBaseUri = 'https://apiv3.emailsys.net/v1';
  protected $_defaultHeader;

  public function __construct($integrationID)
  {
    $this->_integrationID = $integrationID;
  }

  public static function registerAjax()
  {
    add_action('wp_ajax_bitforms_hubspot_authorize', [__CLASS__, 'hubspotAuthorize']);
    add_action('wp_ajax_bitforms_hubspot_pipeline', [__CLASS__, 'getAllPipelines']);
    add_action('wp_ajax_bitforms_hubspot_pipeline_tickets', [__CLASS__, 'getAllPipelinesTickets']);
    add_action('wp_ajax_bitforms_hubspot_owners', [__CLASS__, 'getAllOwners']);
    add_action('wp_ajax_bitforms_hubspot_contacts', [__CLASS__, 'getAllContacts']);
    add_action('wp_ajax_bitforms_hubspot_company', [__CLASS__, 'getAllCompany']);
  }

  public static function hubspotAuthorize()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $authorizationHeader = null;
      $inputJSON = file_get_contents('php://input');
      $requestsParams = json_decode($inputJSON);

      if (empty($requestsParams->api_key)) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $apiKey = $requestsParams->api_key;
      $apiEndpoint = 'https://api.hubapi.com/crm/v3/objects/contacts?limit=10&archived=false';
      $authorizationHeader['Accept'] = 'application/json';
      $authorizationHeader['authorization'] = "Bearer $apiKey";

      $apiResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);

      if (is_wp_error($apiResponse) || 'error' === $apiResponse->status) {
        wp_send_json_error(
          empty($apiResponse->code) ? 'Unknown' : $apiResponse->message,
          400
        );
      }
      wp_send_json_success(true);
    }
  }

  public static function getAllPipelines()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $authorizationHeader = null;
      $inputJSON = file_get_contents('php://input');
      $requestsParams = json_decode($inputJSON);

      if (empty($requestsParams->apiKey)) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $apiKey = $requestsParams->apiKey;
      $type = $requestsParams->actionName;
      $apiEndpoint = "https://api.hubapi.com/crm/v3/pipelines/$type";
      $authorizationHeader['Accept'] = 'application/json';
      $authorizationHeader['authorization'] = "Bearer {$apiKey}";

      $apiResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);

      if (is_wp_error($apiResponse) || 'error' === $apiResponse->status) {
        wp_send_json_error(
          empty($apiResponse->code) ? 'Unknown' : $apiResponse->message,
          400
        );
      }
      $pipelines = $apiResponse->results;
      $response = [];

      foreach ($pipelines as $pipeline) {
        $tempStage = [];
        foreach ($pipeline->stages as $stage) {
          $tempStage[] = (object) [
            'stageId'   => $stage->id,
            'stageName' => $stage->label,
          ];
        }
        $response[] = (object) [
          'pipelineId'   => $pipeline->id,
          'pipelineName' => $pipeline->label,
          'stages'       => $tempStage,
        ];
      }
      wp_send_json_success($response, 200);
    }
  }

  public static function getAllOwners()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $authorizationHeader = null;
      $inputJSON = file_get_contents('php://input');
      $requestsParams = json_decode($inputJSON);

      if (empty($requestsParams->apiKey)) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $apiKey = $requestsParams->apiKey;
      // $apiEndpoint = "https://api.hubapi.com/owners/v2/owners?hapikey={$apiKey}";
      $apiEndpoint = 'https://api.hubapi.com/crm/v3/owners/?limit=100&archived=false';

      $authorizationHeader['Accept'] = 'application/json';
      $authorizationHeader['authorization'] = "Bearer {$apiKey}";

      $apiResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);
      if (is_wp_error($apiResponse) || (isset($apiResponse->status) && 'error' === $apiResponse->status)) {
        wp_send_json_error(
          empty($apiResponse->code) ? 'Unknown' : $apiResponse->message,
          400
        );
      }
      $response = [];
      foreach ($apiResponse->results as $owner) {
        $response[] = (object) [
          'ownerId'   => $owner->id,
          'ownerName' => $owner->firstName . ' ' . $owner->lastName,
        ];
      }
      wp_send_json_success($response, 200);
    }
  }

  public static function getAllContacts()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $authorizationHeader = null;
      $inputJSON = file_get_contents('php://input');
      $requestsParams = json_decode($inputJSON);

      if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
        $inputJSON = file_get_contents('php://input');
        $requestsParams = json_decode($inputJSON);

        if (empty($requestsParams->apiKey)) {
          wp_send_json_error(
            __(
              'Requested parameter is empty',
              'bit-form'
            ),
            400
          );
        }
        $apiKey = $requestsParams->apiKey;
        $apiEndpoint = 'https://api.hubapi.com/crm/v3/objects/contacts';
        $authorizationHeader['Accept'] = 'application/json';
        $authorizationHeader['authorization'] = "Bearer {$apiKey}";

        $apiResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);
        if (is_wp_error($apiResponse) || 'error' === $apiResponse->status) {
          wp_send_json_error(
            empty($apiResponse->code) ? 'Unknown' : $apiResponse->message,
            400
          );
        }
        $response = [];
        $contacts = $apiResponse->results;

        foreach ($contacts as $contact) {
          $name = $contact->properties->firstname;
          $response[] = (object) [
            'contactId'   => $contact->id,
            'contactName' => $name,
          ];
        }
        wp_send_json_success($response, 200);
      }
    }
  }

  public static function getAllCompany()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $authorizationHeader = null;
      $inputJSON = file_get_contents('php://input');
      $requestsParams = json_decode($inputJSON);
      if (empty($requestsParams->apiKey)) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $apiKey = $requestsParams->apiKey;

      $apiEndpoint = 'https://api.hubapi.com/companies/v2/companies/';
      $authorizationHeader['Accept'] = 'application/json';
      $authorizationHeader['authorization'] = "Bearer {$apiKey}";

      $apiResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);
      if (is_wp_error($apiResponse) || 'error' === $apiResponse->status) {
        wp_send_json_error(
          empty($apiResponse->code) ? 'Unknown' : $apiResponse->message,
          400
        );
      }
      $companies = $apiResponse->companies;
      $response = [];

      foreach ($companies as $company) {
        if (property_exists($company->properties, 'name')) {
          $name = $company->properties->name->value;
          $response[] = (object) [
            'companyId'   => $company->companyId,
            'companyName' => $name,
          ];
        }
      }
      wp_send_json_success($response, 200);
    }
  }

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = $integrationData->integration_details;
    if (is_string($integrationDetails)) {
      $integrationDetails = json_decode($integrationDetails);
    }

    $fieldMap = $integrationDetails->field_map;
    $apiKey = $integrationDetails->api_key;
    $integId = $integrationData->id;

    if (
      empty($apiKey)
      || empty($fieldMap)
    ) {
      $error = new WP_Error('REQ_FIELD_EMPTY', __('api key field is required for hubspot api', 'bit-form'));
      return $error;
    }

    // $actions = $integrationDetails->actions;
    $recordApiHelper = new HubspotRecordApiHelper($logID, $apiKey);
    $hubspotResponse = $recordApiHelper->executeRecordApi(
      $integId,
      $integrationDetails,
      $fieldValues,
      $fieldMap
    );
    if (is_wp_error($hubspotResponse)) {
      return $hubspotResponse;
    }
    return $hubspotResponse;
  }
}
