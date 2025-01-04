<?php

/**
 * Elastic Email Integration
 */

namespace BitCode\BitForm\Core\Integration\ElasticEmail;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\HttpHelper;
use WP_Error;

/**
 * Provide functionality for ZohoCrm integration
 */
class ElasticEmailHandler
{
  public static function registerAjax()
  {
    add_action('wp_ajax_bitforms_elasticemail_authorize', [__CLASS__, 'elasticEmailAuthorize']);
    add_action('wp_ajax_bitforms_get_all_lists', [__CLASS__, 'getAllLists']);
  }

  public static function elasticEmailAuthorize()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
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

      $apiEndpoint = 'https://api.elasticemail.com/v4/lists';
      $apiKey = $requestsParams->api_key;
      $header = [
        'X-ElasticEmail-ApiKey' => $apiKey,
        'Accept'                => '*/*',
      ];
      $apiResponse = HttpHelper::get($apiEndpoint, null, $header);

      if (is_wp_error($apiResponse) || isset($apiResponse->Error)) {
        wp_send_json_error(
          $apiResponse->Error,
          400
        );
      }
      wp_send_json_success(true);
    }
  }

  public static function getAllLists()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $response = null;
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

      $apiEndpoint = 'https://api.elasticemail.com/v4/lists';
      $apiKey = $requestsParams->apiKey;
      $header = [
        'X-ElasticEmail-ApiKey' => $apiKey,
        'Accept'                => '*/*',
      ];
      $apiResponse = HttpHelper::get($apiEndpoint, null, $header);
      $data = [];
      foreach ($apiResponse as $list) {
        $data[] = (object) [
          'listId'   => $list->PublicListID,
          'listName' => $list->ListName
        ];
      }
      $response['lists'] = $data;
      wp_send_json_success($response, 200);
    }

    // wp_send_json_success(true);
  }

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = $integrationData->integration_details;
    if (is_string($integrationDetails)) {
      $integrationDetails = json_decode($integrationDetails);
    }
    $integId = $integrationData->id;

    $api_key = $integrationDetails->api_key;
    $fieldMap = $integrationDetails->field_map;
    $actions = $integrationDetails->actions;
    if (
      empty($api_key)
      || empty($fieldMap)
    ) {
      return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Elastic Email api', 'bit-form'));
    }
    $recordApiHelper = new RecordApiHelper($api_key, $integId, $logID, $integrationDetails);
    $elasticEmailApiResponse = $recordApiHelper->execute(
      $integId,
      $fieldValues,
      $fieldMap,
      $integrationDetails
      // $actions
    );

    if (is_wp_error($elasticEmailApiResponse)) {
      return $elasticEmailApiResponse;
    }
    return $elasticEmailApiResponse;
  }
}
