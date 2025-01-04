<?php

namespace BitCode\BitForm\Core\Integration\Getgist;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\HttpHelper;
use WP_Error;

class GetgistHandler
{
  public static $api_endpoint = 'https://api.getgist.com';

  public static function registerAjax()
  {
    add_action('wp_ajax_bitforms_getgist_authorize', [__CLASS__, 'getgistAuthorize']);
    add_action('wp_ajax_bitforms_getgist_tags', [__CLASS__, 'getAllTags']);
  }

  public static function getgistAuthorize()
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

      $apiEndpoint = self::$api_endpoint . '/contacts';
      $authorizationHeader['Authorization'] = "Bearer {$requestsParams->api_key}";
      $apiResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);

      if (is_wp_error($apiResponse) || 'authentication_failed' === $apiResponse->code) {
        wp_send_json_error(
          empty($apiResponse->code) ? 'Unknown' : $apiResponse->message,
          400
        );
      }

      wp_send_json_success(true);
    }
  }

  public static function getAllTags()
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

      $apiEndpoint = self::$api_endpoint . '/tags';
      $authorizationHeader['Authorization'] = "Bearer {$requestsParams->apiKey}";
      $apiResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);

      if (is_wp_error($apiResponse) || 'error' === $apiResponse->status) {
        wp_send_json_error(
          empty($apiResponse->code) ? 'Unknown' : $apiResponse->message,
          400
        );
      }
      $response = [];

      foreach ($apiResponse->tags as $tag) {
        $response[] = [
          'tagId'   => $tag->id,
          'tagName' => $tag->name,
        ];
      }
      wp_send_json_success($response, 200);
    }
  }

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    //$integrationDetails = $integrationData->flow_details;

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
      return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Gist api', 'bit-form'));
    }
    $recordApiHelper = new RecordApiHelper($api_key, $integId, $logID);
    $getgistApiResponse = $recordApiHelper->execute(
      $integId,
      $fieldValues,
      $fieldMap,
      $integrationDetails
    );

    if (is_wp_error($getgistApiResponse)) {
      return $getgistApiResponse;
    }
    return $getgistApiResponse;
  }
}
