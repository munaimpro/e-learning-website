<?php

/**
 * ZohoAnalytics Integration
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoAnalytics;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\HttpHelper;
use BitCode\BitForm\Core\Util\IpTool;
use WP_Error;

/**
 * Provide functionality for ZohoCrm integration
 */
class ZohoAnalyticsHandler
{
  private $_formID;
  private $_integrationID;

  private $_logResponse;

  public function __construct($integrationID, $fromID)
  {
    $this->_formID = $fromID;
    $this->_integrationID = $integrationID;
    $this->_logResponse = new UtilApiResponse();
  }

  /**
   * Helps to register ajax function's with wp
   *
   * @return null
   */
  public static function registerAjax()
  {
    add_action('wp_ajax_bitforms_zanalytics_generate_token', [__CLASS__, 'generateTokens']);
    add_action('wp_ajax_bitforms_zanalytics_refresh_workspaces', [__CLASS__, 'refreshWorkspacesAjaxHelper']);
    add_action('wp_ajax_bitforms_zanalytics_refresh_users', [__CLASS__, 'refreshUsersAjaxHelper']);
    add_action('wp_ajax_bitforms_zanalytics_refresh_tables', [__CLASS__, 'refreshTablesAjaxHelper']);
    add_action('wp_ajax_bitforms_zanalytics_refresh_table_headers', [__CLASS__, 'refreshTableHeadersAjaxHelper']);
  }

  /**
   * Process ajax request for generate_token
   *
   * @return JSON zoho crm api response and status
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
  public static function refreshWorkspacesAjaxHelper()
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
        || empty($queryParams->ownerEmail)
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
        $response['tokenDetails'] = ZohoAnalyticsHandler::_refreshAccessToken($queryParams);
      }

      $workspacesMetaApiEndpoint = "https://analyticsapi.zoho.{$queryParams->dataCenter}/api/{$queryParams->ownerEmail}?ZOHO_ACTION=MYWORKSPACELIST&ZOHO_OUTPUT_FORMAT=JSON&ZOHO_ERROR_FORMAT=JSON&ZOHO_API_VERSION=1.0";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $workspacesMetaResponse = HttpHelper::get($workspacesMetaApiEndpoint, null, $authorizationHeader);

      $allWorkspaces = [];
      if (!is_wp_error($workspacesMetaResponse) && empty($workspacesMetaResponse->response->error)) {
        $workspaces = $workspacesMetaResponse->response->result;
        foreach ($workspaces as $workspace) {
          $allWorkspaces[] = $workspace->workspaceName;
        }
        usort($allWorkspaces, 'strnatcasecmp');
        $response['workspaces'] = $allWorkspaces;
      } else {
        wp_send_json_error(
          $workspacesMetaResponse->response->error->message,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoAnalyticsHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['workspaces']);
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

  public static function refreshUsersAjaxHelper()
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
        || empty($queryParams->ownerEmail)
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
        $response['tokenDetails'] = ZohoAnalyticsHandler::_refreshAccessToken($queryParams);
      }

      $usersMetaApiEndpoint = "https://analyticsapi.zoho.{$queryParams->dataCenter}/api/{$queryParams->ownerEmail}?ZOHO_ACTION=GETUSERS&ZOHO_OUTPUT_FORMAT=JSON&ZOHO_ERROR_FORMAT=JSON&ZOHO_API_VERSION=1.0";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $usersMetaResponse = HttpHelper::get($usersMetaApiEndpoint, null, $authorizationHeader);

      $allusers = [];
      if (!is_wp_error($usersMetaResponse) && empty($usersMetaResponse->response->error)) {
        $users = $usersMetaResponse->response->result;
        foreach ($users as $user) {
          $allusers[] = $user->emailId;
        }
        usort($allusers, 'strnatcasecmp');
        $response['users'] = $allusers;
      } else {
        wp_send_json_error(
          $usersMetaResponse->response->error->message,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoAnalyticsHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['users']);
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
  public static function refreshTablesAjaxHelper()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $authorizationHeader = null;
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->workspace)
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
        $response['tokenDetails'] = ZohoAnalyticsHandler::_refreshAccessToken($queryParams);
      }

      $tablesMetaApiEndpoint = "https://analyticsapi.zoho.{$queryParams->dataCenter}/api/{$queryParams->ownerEmail}/{$queryParams->workspace}?ZOHO_ACTION=VIEWLIST&ZOHO_OUTPUT_FORMAT=JSON&ZOHO_ERROR_FORMAT=JSON&ZOHO_API_VERSION=1.0";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $tablesMetaResponse = HttpHelper::get($tablesMetaApiEndpoint, null, $authorizationHeader);

      $allTables = [];
      if (!is_wp_error($tablesMetaResponse)) {
        $tables = $tablesMetaResponse->response->result;
        foreach ($tables as $table) {
          if ('Table' === $table->viewType) {
            $allTables[] = $table->viewName;
          }
        }
        usort($allTables, 'strnatcasecmp');
        $response['tables'] = $allTables;
      } else {
        wp_send_json_error(
          'error' === $fieldsMetaResponse->status ? $fieldsMetaResponse->message : 'Unknown',
          400
        );
      }
      if (!empty($response['tokenDetails']) && $response['tokenDetails'] && !empty($queryParams->id)) {
        $response['queryWorkspace'] = $queryParams->workspace;
        ZohoAnalyticsHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response);
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
  public static function refreshTableHeadersAjaxHelper()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $authorizationHeader = null;
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->workspace)
        || empty($queryParams->table)
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
        $response['tokenDetails'] = ZohoAnalyticsHandler::_refreshAccessToken($queryParams);
      }

      $tableHeadersMetaApiEndpoint = "https://analyticsapi.zoho.{$queryParams->dataCenter}/api/{$queryParams->ownerEmail}/{$queryParams->workspace}/{$queryParams->table}?ZOHO_ACTION=EXPORT&ZOHO_OUTPUT_FORMAT=JSON&ZOHO_ERROR_FORMAT=JSON&ZOHO_API_VERSION=1.0";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $tableHeadersMetaResponse = HttpHelper::get($tableHeadersMetaApiEndpoint, null, $authorizationHeader);

      if ('string' === gettype($tableHeadersMetaResponse)) {
        $tableHeadersMetaResponse = json_decode(preg_replace("/\\\'/", "'", $tableHeadersMetaResponse));
      }

      if (!is_wp_error($tableHeadersMetaResponse)) {
        $allHeaders = array_diff($tableHeadersMetaResponse->response->result->column_order, ['Auto Number']);
        usort($allHeaders, 'strnatcasecmp');
        $response['table_headers'] = $allHeaders;
      } else {
        wp_send_json_error(
          'error' === $tableHeadersMetaResponse->status ? $tableHeadersMetaResponse->message : 'Unknown',
          400
        );
      }
      if (!empty($response['tokenDetails']) && $response['tokenDetails'] && !empty($queryParams->id)) {
        $response['queryModule'] = $queryParams->module;
        ZohoAnalyticsHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response);
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
   * @param Integer $fromID        ID of Integration related form
   * @param Integer $integrationID ID of Zoho crm Integration
   * @param Obeject $tokenDetails  refreshed token info
   *
   * @return null
   */
  protected static function _saveRefreshedToken($formID, $integrationID, $tokenDetails, $others = null)
  {
    if (empty($formID) || empty($integrationID)) {
      return;
    }

    $integrationHandler = new IntegrationHandler($formID, IpTool::getUserDetail());
    $zanalyticsDetails = $integrationHandler->getAIntegration($integrationID);

    if (is_wp_error($zanalyticsDetails)) {
      return;
    }
    $newDetails = json_decode($zanalyticsDetails[0]->integration_details);

    $newDetails->tokenDetails = $tokenDetails;
    if (!empty($others['workspaces'])) {
      $newDetails->default->workspaces = $others['workspaces'];
    }
    if (!empty($others['tables'])) {
      $newDetails->default->tables = $others['tables'];
    }
    if (!empty($others['table_headers'])) {
      $newDetails->default->tables->headers->{$others['table']} = $others['table_headers'];
    }

    $integrationHandler->updateIntegration($integrationID, $zanalyticsDetails[0]->integration_name, 'Zoho Analytics', wp_json_encode($newDetails), 'form');
  }

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = is_string($integrationData->integration_details) ? json_decode($integrationData->integration_details) : $integrationData->integration_details;

    $tokenDetails = $integrationDetails->tokenDetails;
    $workspace = $integrationDetails->workspace;
    $table = $integrationDetails->table;
    $ownerEmail = $integrationDetails->ownerEmail;
    $dataCenter = $integrationDetails->dataCenter;
    $fieldMap = $integrationDetails->field_map;
    $actions = $integrationDetails->actions;
    $defaultDataConf = $integrationDetails->default;
    if (
      empty($tokenDetails)
      || empty($workspace)
      || empty($table)
      || empty($fieldMap)
    ) {
      $error = new WP_Error('REQ_FIELD_EMPTY', __('workspace, table, fields are required for zoho analytics api', 'bit-form'));
      $this->_logResponse->apiResponse($logID, $this->_integrationID, 'record', 'validation', $error);
      return $error;
    }

    $requiredParams = null;
    if ((intval($tokenDetails->generates_on) + (55 * 60)) < time()) {
      $requiredParams['clientId'] = $integrationDetails->clientId;
      $requiredParams['clientSecret'] = $integrationDetails->clientSecret;
      $requiredParams['dataCenter'] = $integrationDetails->dataCenter;
      $requiredParams['tokenDetails'] = $tokenDetails;
      $newTokenDetails = ZohoAnalyticsHandler::_refreshAccessToken((object)$requiredParams);
      if ($newTokenDetails) {
        ZohoAnalyticsHandler::_saveRefreshedToken($this->_formID, $this->_integrationID, $newTokenDetails);
        $tokenDetails = $newTokenDetails;
      }
    }

    // $actions = $integrationDetails->actions;
    $recordApiHelper = new RecordApiHelper($tokenDetails, $this->_integrationID, $logID);

    $zanalyticsApiResponse = $recordApiHelper->executeRecordApi(
      $workspace,
      $table,
      $ownerEmail,
      $dataCenter,
      $actions,
      $defaultDataConf,
      $fieldValues,
      $fieldMap
    );

    if (is_wp_error($zanalyticsApiResponse)) {
      return $zanalyticsApiResponse;
    }
    return $zanalyticsApiResponse;
  }
}
