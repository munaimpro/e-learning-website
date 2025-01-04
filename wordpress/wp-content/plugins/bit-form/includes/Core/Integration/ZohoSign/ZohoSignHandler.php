<?php

/**
 * ZohoSign Integration
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoSign;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\HttpHelper;
use BitCode\BitForm\Core\Util\IpTool;
use WP_Error;

/**
 * Provide functionality for ZohoCrm integration
 */
class ZohoSignHandler
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
    add_action('wp_ajax_bitforms_zsign_generate_token', [__CLASS__, 'generateTokens']);
    add_action('wp_ajax_bitforms_zsign_refresh_templates', [__CLASS__, 'refreshTemplatesAjaxHelper']);
    add_action('wp_ajax_bitforms_zsign_refresh_template_details', [__CLASS__, 'refreshTemplateDetailsAjaxHelper']);
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
    $zsignDetails = $integrationHandler->getAIntegration($integrationID);

    if (is_wp_error($zsignDetails)) {
      return;
    }
    $newDetails = json_decode($zsignDetails[0]->integration_details);

    $newDetails->tokenDetails = $tokenDetails;

    $integrationHandler->updateIntegration($integrationID, $zsignDetails[0]->integration_name, 'Zoho Sign', wp_json_encode($newDetails), 'form');
  }

  public static function refreshTemplatesAjaxHelper()
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
        $response['tokenDetails'] = ZohoSignHandler::_refreshAccessToken($queryParams);
      }
      $templatesMetaApiEndpoint = "https://sign.zoho.{$queryParams->dataCenter}/api/v1/templates";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $templatesMetaResponse = HttpHelper::get($templatesMetaApiEndpoint, null, $authorizationHeader);

      $allTemplates = [];
      if (!is_wp_error($templatesMetaResponse) && empty($templatesMetaResponse->response->error)) {
        $templates = $templatesMetaResponse->templates;
        foreach ($templates as $template) {
          $allTemplates[$template->template_name] = (object) [
            'templateId'   => $template->template_id,
            'templateName' => $template->template_name
          ];
        }
        uksort($allTemplates, 'strnatcasecmp');
        $response['templates'] = $allTemplates;
      } else {
        wp_send_json_error(
          $templatesMetaResponse->response->error->message,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoSignHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['templates']);
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

  public static function refreshTemplateDetailsAjaxHelper()
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
        || empty($queryParams->template)
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
        $response['tokenDetails'] = ZohoSignHandler::_refreshAccessToken($queryParams);
      }

      $templateDetailsMetaApiEndpoint = "https://sign.zoho.{$queryParams->dataCenter}/api/v1/templates/{$queryParams->template}";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $templateDetailsMetaResponse = HttpHelper::get($templateDetailsMetaApiEndpoint, null, $authorizationHeader);

      // wp_send_json_success($templateDetailsMetaResponse, 200);

      $details = [];
      if (!is_wp_error($templateDetailsMetaResponse) && empty($templateDetailsMetaResponse->response->error)) {
        $templateDetails = $templateDetailsMetaResponse->templates;
        if (!$templateDetails->is_deleted) {
          $details['actions'] = $templateDetails->actions;
          $details['notes'] = $templateDetails->notes;
        }
        $response['templateDetails'] = $details;
      } else {
        wp_send_json_error(
          $templateDetailsMetaResponse->response->error->message,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoSignHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['templateDetails']);
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

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = is_string($integrationData->integration_details) ? json_decode($integrationData->integration_details) : $integrationData->integration_details;
    $templateActions = $integrationDetails->templateActions;
    $notes = $integrationDetails->notes;
    $dataCenter = $integrationDetails->dataCenter;
    $template = $integrationDetails->template;
    $tokenDetails = $integrationDetails->tokenDetails;
    if (empty($tokenDetails)) {
      return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for zoho sign api', 'bit-form'));
    }

    $requiredParams = null;
    if ((intval($tokenDetails->generates_on) + (55 * 60)) < time()) {
      $requiredParams['clientId'] = $integrationDetails->clientId;
      $requiredParams['clientSecret'] = $integrationDetails->clientSecret;
      $requiredParams['dataCenter'] = $integrationDetails->dataCenter;
      $requiredParams['tokenDetails'] = $tokenDetails;
      $newTokenDetails = ZohoSignHandler::_refreshAccessToken((object)$requiredParams);
      if ($newTokenDetails) {
        ZohoSignHandler::_saveRefreshedToken($this->_formID, $this->_integrationID, $newTokenDetails);
        $tokenDetails = $newTokenDetails;
      }
    }

    // $actions = $integrationDetails->actions;
    $recordApiHelper = new RecordApiHelper($tokenDetails, $this->_integrationID, $logID);

    $zsignApiResponse = $recordApiHelper->executeRecordApi(
      $dataCenter,
      $template,
      $templateActions,
      $notes,
      $fieldValues
    );

    return $zsignApiResponse;
  }
}
