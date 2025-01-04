<?php

/**
 * MailChimp Integration
 *
 */

namespace BitCode\BitForm\Core\Integration\MailChimp;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\HttpHelper;
use WP_Error;

/**
 * Provide functionality for MailChimp integration
 */
class MailChimpHandler
{
  private $_integrationID;

  public function __construct($integrationID, $fromID)
  {
    $this->_integrationID = $integrationID;
  }

  /**
   * MailChimp API Endpoint
   */
  public static function apiEndPoint($dc)
  {
    return "https://$dc.api.mailchimp.com/3.0";
  }

  /**
   * Helps to register ajax function's with wp
   *
   * @return null
   */
  public static function registerAjax()
  {
    add_action('wp_ajax_bitforms_mChimp_generate_token', [__CLASS__, 'generateTokens']);
    add_action('wp_ajax_bitforms_mChimp_refresh_audience', [__CLASS__, 'refreshAudienceAjaxHelper']);
    add_action('wp_ajax_bitforms_mChimp_refresh_fields', [__CLASS__, 'refreshAudienceFields']);
    add_action('wp_ajax_bitforms_mChimp_refresh_tags', [__CLASS__, 'refreshTagsAjaxHelper']);
  }

  /**
   * Process ajax request for generate_token
   *
   * @return JSON zoho crm api response and status
   */
  public static function generateTokens()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $authorizationHeader = null;
      $inputJSON = file_get_contents('php://input');
      $requestsParams = json_decode($inputJSON);
      if (
        empty($requestsParams->clientId)
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

      $apiEndpoint = 'https://login.mailchimp.com/oauth2/token';
      $authorizationHeader['Content-Type'] = 'application/x-www-form-urlencoded';
      $requestParams = [
        'code'          => $requestsParams->code,
        'client_id'     => $requestsParams->clientId,
        'client_secret' => $requestsParams->clientSecret,
        'redirect_uri'  => $requestsParams->redirectURI,
        'grant_type'    => 'authorization_code'
      ];
      $apiResponse = HttpHelper::post($apiEndpoint, $requestParams, $authorizationHeader);

      $metaDataEndPoint = 'https://login.mailchimp.com/oauth2/metadata';

      $authorizationHeader['Authorization'] = "Bearer {$apiResponse->access_token}";
      $metaData = HttpHelper::post($metaDataEndPoint, null, $authorizationHeader);

      $apiResponse->dc = $metaData->dc;

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
   * Process ajax request for refresh MailChimp Audience list
   *
   * @return JSON MailChimp data
   */
  public static function refreshAudienceAjaxHelper()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $authorizationHeader = null;
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);

      if (
        empty($queryParams->tokenDetails)
        || empty($queryParams->clientId)
        || empty($queryParams->clientSecret)
        || empty($queryParams->tokenDetails->dc)
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
      $apiEndpoint = self::apiEndPoint($queryParams->tokenDetails->dc) . '/lists';

      $authorizationHeader['Authorization'] = "Bearer {$queryParams->tokenDetails->access_token}";
      $audienceResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);

      $allList = [];
      if (!is_wp_error($audienceResponse) && empty($audienceResponse->response->error)) {
        $audienceLists = $audienceResponse->lists;
        // wp_send_json_success($audienceLists);
        foreach ($audienceLists as $audienceList) {
          $allList[$audienceList->name] = (object) [
            'listId'   => $audienceList->id,
            'listName' => $audienceList->name
          ];
        }
        uksort($allList, 'strnatcasecmp');

        $response['audiencelist'] = $allList;
        // wp_send_json_success($response, 200);
      } else {
        wp_send_json_error(
          $audienceResponse->response->error->message,
          400
        );
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
   * Process ajax request for refresh MailChimp Audince Fields
   * @return JSON MailChimp Audience fields
   */
  public static function refreshAudienceFields()
  {
    $authorizationHeader = null;
    $response = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      // wp_send_json_success($queryParams);
      if (
        empty($queryParams->tokenDetails)
        || empty($queryParams->listId)
        || empty($queryParams->tokenDetails->dc)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $apiEndpoint = self::apiEndPoint($queryParams->tokenDetails->dc) . "/lists/$queryParams->listId/merge-fields";
      $authorizationHeader['Authorization'] = "Bearer {$queryParams->tokenDetails->access_token}";
      $mergeFieldResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);
      // wp_send_json_success($mergeFieldResponse);
      $fields = [];
      if (!is_wp_error($mergeFieldResponse)) {
        $allFields = $mergeFieldResponse->merge_fields;
        foreach ($allFields as $field) {
          if ('Address' === $field->name) {
            continue;
          }
          $fields[$field->name] = (object) [
            'tag'  => $field->tag,
            'name' => $field->name
          ];
        }
        $fields['Email'] = (object) ['tag' => 'email_address', 'name' => 'Email'];
        $response['audienceField'] = $fields;
        wp_send_json_success($response);
      }
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
   * Process ajax request for refresh MailChimp Tags
   * @return JSON MailChimp Tags
   */
  public static function refreshTagsAjaxHelper()
  {
    $authorizationHeader = null;
    $response = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      // wp_send_json_success($queryParams);
      if (
        empty($queryParams->tokenDetails)
        || empty($queryParams->listId)
        || empty($queryParams->tokenDetails->dc)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $apiEndpoint = self::apiEndPoint($queryParams->tokenDetails->dc) . "/lists/$queryParams->listId/segments?count=1000";
      $authorizationHeader['Authorization'] = "Bearer {$queryParams->tokenDetails->access_token}";
      $tagsList = HttpHelper::get($apiEndpoint, null, $authorizationHeader);
      // wp_send_json_success($tagsList->segments);
      $allList = [];
      foreach ($tagsList->segments as $tag) {
        $allList[$tag->name] = (object) [
          'tagId'   => $tag->id,
          'tagName' => $tag->name
        ];
      }
      uksort($allList, 'strnatcasecmp');
      $response['audienceTags'] = $allList;
      wp_send_json_success($response);
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
   * Save updated access_token to avoid unnecessary token generation
   *
   * @param Integer $fromID        ID of Integration related form
   * @param Integer $integrationID ID of Mail Chimp Integration
   * @param Obeject $tokenDetails  refreshed token info
   *
   * @return null
   */
  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = is_string($integrationData->integration_details) ? json_decode($integrationData->integration_details) : $integrationData->integration_details;

    $tokenDetails = $integrationDetails->tokenDetails;
    $listId = $integrationDetails->listId;
    $tags = $integrationDetails->tags;
    $fieldMap = $integrationDetails->field_map;
    $actions = $integrationDetails->actions;
    $defaultDataConf = $integrationDetails->default;
    $addressFields = $integrationDetails->address_field;

    if (
      empty($tokenDetails)
      || empty($listId)
      || empty($fieldMap)
      || empty($defaultDataConf)
    ) {
      return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Mail Chimp api', 'bit-form'));
    }
    $recordApiHelper = new RecordApiHelper($tokenDetails, $this->_integrationID, $logID, $entryID);
    $mChimpApiResponse = $recordApiHelper->executeRecordApi(
      $listId,
      $tags,
      $defaultDataConf,
      $fieldValues,
      $fieldMap,
      $actions,
      $addressFields
    );

    if (is_wp_error($mChimpApiResponse)) {
      return $mChimpApiResponse;
    }
    return $mChimpApiResponse;
  }
}
