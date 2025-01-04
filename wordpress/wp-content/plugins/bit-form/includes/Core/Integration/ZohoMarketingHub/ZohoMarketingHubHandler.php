<?php

/**
 * ZohoMarketingHub Integration
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoMarketingHub;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\HttpHelper;
use BitCode\BitForm\Core\Util\IpTool;

/**
 * Provide functionality for ZohoCrm integration
 */
class ZohoMarketingHubHandler
{
  private $_formID;
  private $_integrationID;

  public function __construct($integrationID, $fromID)
  {
    $this->_formID = $fromID;
    $this->_integrationID = $integrationID;
  }

  /**
   * Helps to register ajax function's with wp
   *
   * @return null
   */
  public static function registerAjax()
  {
    add_action('wp_ajax_bitforms_zmarketingHub_generate_token', [__CLASS__, 'generateTokens']);
    add_action('wp_ajax_bitforms_zmarketingHub_refresh_lists', [__CLASS__, 'refreshListsAjaxHelper']);
    add_action('wp_ajax_bitforms_zmarketingHub_refresh_contact_fields', [__CLASS__, 'refreshContactFieldsAjaxHelper']);
  }

  /**
   * Process ajax request for generate_token
   *
   * @return string zoho crm api response and status
   */
  public static function generateTokens()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $requestsParams = json_decode($inputJSON);
      if (
        empty($requestsParams->{'accounts-server'})
        || empty($requestsParams->dataCenter)
        || empty($requestsParams->clientId)
        || empty($requestsParams->clientSecret)
        || empty($requestsParams->redirectURI)
        || empty($requestsParams->code)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }

      $apiEndpoint = \urldecode($requestsParams->{'accounts-server'}) . '/oauth/v2/token';
      $requestParams = [
        'grant_type'    => 'authorization_code',
        'client_id'     => $requestsParams->clientId,
        'client_secret' => $requestsParams->clientSecret,
        'redirect_uri'  => \urldecode($requestsParams->redirectURI),
        'code'          => $requestsParams->code
      ];
      $apiResponse = HttpHelper::post($apiEndpoint, $requestParams);

      if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
        wp_send_json_error(
          empty($apiResponse->error) ? 'Unknown' : $apiResponse->error,
          400
        );
      }
      $apiResponse->generates_on = \time();
      wp_send_json_success($apiResponse, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  /**
   * Process ajax request for refresh crm modules
   *
   * @return JSON crm module data
   */
  public static function refreshListsAjaxHelper()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $authorizationHeader = null;
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->tokenDetails)
        || empty($queryParams->dataCenter)
        || empty($queryParams->clientId)
        || empty($queryParams->clientSecret)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $response = [];
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = ZohoMarketingHubHandler::_refreshAccessToken($queryParams);
      }

      $listsMetaApiEndpoint = "https://marketinghub.zoho.{$queryParams->dataCenter}/api/v1/getmailinglists?resfmt=JSON&range=100";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $listsMetaResponse = HttpHelper::get($listsMetaApiEndpoint, null, $authorizationHeader);

      $allLists = [];
      if (!is_wp_error($listsMetaResponse)) {
        $lists = $listsMetaResponse->list_of_details;

        if (count($lists) > 0) {
          foreach ($lists as $list) {
            $allLists[$list->listname] = (object) [
              'listkey'  => $list->listkey,
              'listname' => $list->listname
            ];
          }
        }
        uksort($allLists, 'strnatcasecmp');
        $response['lists'] = $allLists;
      } else {
        wp_send_json_error(
          empty($listsMetaResponse->data) ? 'Unknown' : $listsMetaResponse->error,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoMarketingHubHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['lists']);
      }
      wp_send_json_success($response, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  /**
   * Process ajax request for refesh crm layouts
   *
   * @return JSON crm layout data
   */
  public static function refreshContactFieldsAjaxHelper()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $authorizationHeader = null;
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->list)
        || empty($queryParams->tokenDetails)
        || empty($queryParams->dataCenter)
        || empty($queryParams->clientId)
        || empty($queryParams->clientSecret)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $response = [];
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = ZohoMarketingHubHandler::_refreshAccessToken($queryParams);
      }

      $contactFieldsMetaApiEndpoint = "https://marketinghub.zoho.{$queryParams->dataCenter}/api/v1/lead/allfields?type=json";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $contactFieldsMetaResponse = HttpHelper::get($contactFieldsMetaApiEndpoint, null, $authorizationHeader);

      // wp_send_json_success($contactFieldsMetaResponse, 200);

      if (!is_wp_error($contactFieldsMetaResponse)) {
        $allFields = [];
        $fields = $contactFieldsMetaResponse->response->fieldnames->fieldname;

        if (count($fields) > 0) {
          foreach ($fields as $field) {
            $allFields[] = $field->DISPLAY_NAME;
          }
        }

        usort($allFields, 'strnatcasecmp');
        $response['fields'] = $allFields;

        $response['required'] = ['Contact Email'];
      } else {
        wp_send_json_error(
          'error' === $contactFieldsMetaResponse->status ? $contactFieldsMetaResponse->message : 'Unknown',
          400
        );
      }
      if (!empty($response['tokenDetails']) && $response['tokenDetails'] && !empty($queryParams->id)) {
        $response['queryModule'] = $queryParams->module;
        ZohoMarketingHubHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response);
      }
      wp_send_json_success($response, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  /**
   * Helps to refresh zoho crm access_token
   *
   * @param  object $apiData Contains required data for refresh access token
   * @return string|boolean  $tokenDetails API token details
   */
  protected static function _refreshAccessToken($apiData)
  {
    if (
      empty($apiData->dataCenter)
      || empty($apiData->clientId)
      || empty($apiData->clientSecret)
      || empty($apiData->tokenDetails)
    ) {
      return false;
    }
    $tokenDetails = $apiData->tokenDetails;

    $dataCenter = $apiData->dataCenter;
    $apiEndpoint = "https://accounts.zoho.{$dataCenter}/oauth/v2/token";
    $requestParams = [
      'grant_type'    => 'refresh_token',
      'client_id'     => $apiData->clientId,
      'client_secret' => $apiData->clientSecret,
      'refresh_token' => $tokenDetails->refresh_token,
    ];

    $apiResponse = HttpHelper::post($apiEndpoint, $requestParams);
    if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
      return false;
    }
    $tokenDetails->generates_on = \time();
    $tokenDetails->access_token = $apiResponse->access_token;
    return $tokenDetails;
  }

  /**
   * Save updated access_token to avoid unnecessary token generation
   *
   * @param integer $fromID        ID of Integration related form
   * @param integer $integrationID ID of Zoho crm Integration
   * @param object $tokenDetails  refreshed token info
   *
   * @return null
   */
  protected static function _saveRefreshedToken($formID, $integrationID, $tokenDetails, $others = null)
  {
    if (empty($formID) || empty($integrationID)) {
      return;
    }

    $integrationHandler = new IntegrationHandler($formID, IpTool::getUserDetail());
    $zmarketingHubDetails = $integrationHandler->getAIntegration($integrationID);

    if (is_wp_error($zmarketingHubDetails)) {
      return;
    }
    $newDetails = json_decode($zmarketingHubDetails[0]->integration_details);

    $newDetails->tokenDetails = $tokenDetails;
    if (!empty($others['lists'])) {
      $newDetails->default->lists = $others['lists'];
    }
    if (!empty($others['fieds'])) {
      $newDetails->default->fields = $others['fields'];
    }
    if (!empty($others['required'])) {
      $newDetails->default->required = $others['required'];
    }

    $integrationHandler->updateIntegration($integrationID, $zmarketingHubDetails[0]->integration_name, 'Zoho MarketingHub', wp_json_encode($newDetails), 'form');
  }

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = is_string($integrationData->integration_details) ? json_decode($integrationData->integration_details) : $integrationData->integration_details;

    $tokenDetails = $integrationDetails->tokenDetails;
    $list = $integrationDetails->list;
    $dataCenter = $integrationDetails->dataCenter;
    $fieldMap = $integrationDetails->field_map;
    $required = $integrationDetails->default->fields->{$list}->required;
    if (
      empty($tokenDetails)
      || empty($list)
      || empty($fieldMap)
    ) {
      return new WP_Error('REQ_FIELD_EMPTY', __('list are required for zoho marketingHub api', 'bit-form'));
    }

    $requiredParams = null;
    if ((intval($tokenDetails->generates_on) + (55 * 60)) < time()) {
      $requiredParams['clientId'] = $integrationDetails->clientId;
      $requiredParams['clientSecret'] = $integrationDetails->clientSecret;
      $requiredParams['dataCenter'] = $integrationDetails->dataCenter;
      $requiredParams['tokenDetails'] = $tokenDetails;
      $newTokenDetails = ZohoMarketingHubHandler::_refreshAccessToken((object)$requiredParams);
      if ($newTokenDetails) {
        ZohoMarketingHubHandler::_saveRefreshedToken($this->_formID, $this->_integrationID, $newTokenDetails);
        $tokenDetails = $newTokenDetails;
      }
    }

    // $actions = $integrationDetails->actions;
    $recordApiHelper = new RecordApiHelper($tokenDetails, $this->_integrationID, $logID);

    $zmarketingHubApiResponse = $recordApiHelper->executeRecordApi(
      $list,
      $dataCenter,
      $fieldValues,
      $fieldMap,
      $required
    );

    if (is_wp_error($zmarketingHubApiResponse)) {
      return $zmarketingHubApiResponse;
    }
    return $zmarketingHubApiResponse;
  }
}
