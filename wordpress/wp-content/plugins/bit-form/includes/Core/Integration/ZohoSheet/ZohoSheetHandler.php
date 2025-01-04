<?php

/**
 * ZohoSheet Integration
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoSheet;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\HttpHelper;
use BitCode\BitForm\Core\Util\IpTool;
use WP_Error;

/**
 * Provide functionality for ZohoCrm integration
 */
class ZohoSheetHandler
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
    add_action('wp_ajax_bitforms_zsheet_generate_token', [__CLASS__, 'generateTokens']);
    add_action('wp_ajax_bitforms_zsheet_refresh_workbooks', [__CLASS__, 'refreshWorkbooksAjaxHelper']);
    add_action('wp_ajax_bitforms_zsheet_refresh_worksheets', [__CLASS__, 'refreshWorksheetsAjaxHelper']);
    add_action('wp_ajax_bitforms_zsheet_refresh_worksheet_headers', [__CLASS__, 'refreshWorksheetHeadersAjaxHelper']);
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
  public static function refreshWorkbooksAjaxHelper()
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
        $response['tokenDetails'] = ZohoSheetHandler::_refreshAccessToken($queryParams);
      }

      $workbooksMetaApiEndpoint = "https://sheet.zoho.{$queryParams->dataCenter}/api/v2/workbooks?method=workbook.list";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $workbooksMetaResponse = HttpHelper::get($workbooksMetaApiEndpoint, null, $authorizationHeader);

      $allWorkbooks = [];
      if (!is_wp_error($workbooksMetaResponse) && empty($workbooksMetaResponse->response->error)) {
        $workbooks = $workbooksMetaResponse->workbooks;
        foreach ($workbooks as $workbook) {
          $allWorkbooks[$workbook->workbook_name] = (object) [
            'workbookId'   => $workbook->resource_id,
            'workbookName' => $workbook->workbook_name
          ];
        }
        uksort($allWorkbooks, 'strnatcasecmp');
        $response['workbooks'] = $allWorkbooks;
      } else {
        wp_send_json_error(
          $workbooksMetaResponse->response->error->message,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoSheetHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['workbooks']);
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
  public static function refreshWorksheetsAjaxHelper()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $authorizationHeader = null;
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->workbook)
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
        $response['tokenDetails'] = ZohoSheetHandler::_refreshAccessToken($queryParams);
      }

      $worksheetsMetaApiEndpoint = "https://sheet.zoho.{$queryParams->dataCenter}/api/v2/{$queryParams->workbook}?method=worksheet.list";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $worksheetsMetaResponse = HttpHelper::get($worksheetsMetaApiEndpoint, null, $authorizationHeader);

      if (!is_wp_error($worksheetsMetaResponse)) {
        $allWorksheets = [];
        $worksheets = $worksheetsMetaResponse->worksheet_names;
        foreach ($worksheets as $worksheet) {
          $allWorksheets[] = $worksheet->worksheet_name;
        }
        usort($allWorksheets, 'strnatcasecmp');
        $response['worksheets'] = $allWorksheets;
      } else {
        wp_send_json_error(
          'error' === $fieldsMetaResponse->status ? $fieldsMetaResponse->message : 'Unknown',
          400
        );
      }
      if (!empty($response['tokenDetails']) && $response['tokenDetails'] && !empty($queryParams->id)) {
        $response['queryWorkbook'] = $queryParams->workbook;
        ZohoSheetHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response);
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
  public static function refreshWorksheetHeadersAjaxHelper()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $authorizationHeader = null;
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->workbook)
        || empty($queryParams->worksheet)
        || empty($queryParams->tokenDetails)
        || empty($queryParams->dataCenter)
        || empty($queryParams->clientId)
        || empty($queryParams->clientSecret)
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
        $response['tokenDetails'] = ZohoSheetHandler::_refreshAccessToken($queryParams);
      }

      if ((int) $queryParams->headerRow < 1) {
        $queryParams->headerRow = 1;
      }

      $worksheetHeadersMetaApiEndpoint = "https://sheet.zoho.{$queryParams->dataCenter}/api/v2/{$queryParams->workbook}?method=worksheet.records.fetch&worksheet_name={$queryParams->worksheet}&count=1&header_row={$queryParams->headerRow}";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $worksheetHeadersMetaResponse = HttpHelper::get($worksheetHeadersMetaApiEndpoint, null, $authorizationHeader);

      // wp_send_json_success($worksheetHeadersMetaResponse, 200);

      if (!is_wp_error($worksheetHeadersMetaResponse)) {
        $allHeaders = array_diff(array_keys((array) $worksheetHeadersMetaResponse->records[0]), ['row_index']);

        usort($allHeaders, 'strnatcasecmp');
        $response['worksheet_headers'] = $allHeaders;
      } else {
        wp_send_json_error(
          'error' === $fieldsMetaResponse->status ? $fieldsMetaResponse->message : 'Unknown',
          400
        );
      }
      if (!empty($response['tokenDetails']) && $response['tokenDetails'] && !empty($queryParams->id)) {
        $response['queryModule'] = $queryParams->module;
        ZohoSheetHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response);
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
   * @return string | boolean  $tokenDetails API token details
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
    $zsheetDetails = $integrationHandler->getAIntegration($integrationID);

    if (is_wp_error($zsheetDetails)) {
      return;
    }
    $newDetails = json_decode($zsheetDetails[0]->integration_details);

    $newDetails->tokenDetails = $tokenDetails;
    if (!empty($others['workbooks'])) {
      $newDetails->default->workbooks = $others['workbooks'];
    }
    if (!empty($others['worksheets'])) {
      $newDetails->default->worksheets = $others['worksheets'];
    }
    if (!empty($others['worksheet_headers'])) {
      $newDetails->default->worksheets->headers->{$others['worksheet']} = $others['worksheet_headers'];
    }

    $integrationHandler->updateIntegration($integrationID, $zsheetDetails[0]->integration_name, 'Zoho Sheet', wp_json_encode($newDetails), 'form');
  }

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = is_string($integrationData->integration_details) ? json_decode($integrationData->integration_details) : $integrationData->integration_details;

    $tokenDetails = $integrationDetails->tokenDetails;
    $workbook = $integrationDetails->workbook;
    $worksheet = $integrationDetails->worksheet;
    $headerRow = $integrationDetails->headerRow;
    $dataCenter = $integrationDetails->dataCenter;
    $fieldMap = $integrationDetails->field_map;
    $actions = $integrationDetails->actions;
    $defaultDataConf = $integrationDetails->default;
    if (
      empty($tokenDetails)
      || empty($workbook)
      || empty($worksheet)
      || empty($fieldMap)
    ) {
      return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for zoho sheet api', 'bit-form'));
    }

    $requiredParams = null;
    if ((intval($tokenDetails->generates_on) + (55 * 60)) < time()) {
      $requiredParams['clientId'] = $integrationDetails->clientId;
      $requiredParams['clientSecret'] = $integrationDetails->clientSecret;
      $requiredParams['dataCenter'] = $integrationDetails->dataCenter;
      $requiredParams['tokenDetails'] = $tokenDetails;
      $newTokenDetails = ZohoSheetHandler::_refreshAccessToken((object)$requiredParams);
      if ($newTokenDetails) {
        ZohoSheetHandler::_saveRefreshedToken($this->_formID, $this->_integrationID, $newTokenDetails);
        $tokenDetails = $newTokenDetails;
      }
    }

    // $actions = $integrationDetails->actions;
    $recordApiHelper = new RecordApiHelper($tokenDetails, $this->_integrationID, $logID);

    $zsheetApiResponse = $recordApiHelper->executeRecordApi(
      $workbook,
      $worksheet,
      $headerRow,
      $dataCenter,
      $actions,
      $defaultDataConf,
      $fieldValues,
      $fieldMap
    );

    if (is_wp_error($zsheetApiResponse)) {
      return $zsheetApiResponse;
    }
    return $zsheetApiResponse;
  }
}
