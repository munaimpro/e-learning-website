<?php

/**
 * SendFox Integration
 */

namespace BitCode\BitForm\Core\Integration\SendFox;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\HttpHelper;
use WP_Error;

class SendFoxHandler
{
  private $integrationID;
  public static $baseUrl = 'https://api.sendfox.com/';

  public function __construct($integrationID, $fromID)
  {
    $this->integrationID = $integrationID;
  }

  public static function registerAjax()
  {
    add_action('wp_ajax_bitforms_sendFox_authorize', [__CLASS__, 'sendFoxAuthorize']);
    add_action('wp_ajax_bitforms_sendfox_fetch_all_list', [__CLASS__, 'fetchContactLists']);
  }

  public static function sendFoxAuthorize($requestParams)
  {
    if (!isset($_REQUEST['_ajax_nonce']) && !wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      wp_send_json_error(__('Token expired', 'bit-form'), 401);
    }

    $inputJSON = file_get_contents('php://input');
    $requestParams = json_decode($inputJSON);
    if (empty($requestParams->access_token)) {
      wp_send_json_error(
        __(
          'Requested parameter is empty',
          'bit-form'
        ),
        400
      );
    }
    $apiEndpoints = self::$baseUrl . 'me';
    $header = [
      'Authorization' => "Bearer {$requestParams->access_token}",
      'Accept'        => 'application/json',
    ];

    $response = HttpHelper::get($apiEndpoints, null, $header);
    if ('Unauthenticated.' !== $response->message) {
      wp_send_json_success($response, 200);
    } else {
      wp_send_json_error(
        'The token is invalid',
        400
      );
    }
  }

  public static function fetchContactLists($requestParams)
  {
    if (!isset($_REQUEST['_ajax_nonce']) && !wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      wp_send_json_error(__('Token expired', 'bit-form'), 401);
    }

    $inputJSON = file_get_contents('php://input');
    $requestParams = json_decode($inputJSON);

    if (empty($requestParams->access_token)) {
      wp_send_json_error(
        __(
          'Requested parameter is empty',
          'bit-form'
        ),
        400
      );
    }
    $apiEndpoints = self::$baseUrl . 'lists';

    $requestParams = [
      'Authorization' => "Bearer {$requestParams->access_token}",
      'Accept'        => 'application/json',
    ];

    $response = HttpHelper::get($apiEndpoints, null, $requestParams);

    if ('Unauthenticated.' !== $response->message) {
      wp_send_json_success($response, 200);
    } else {
      wp_send_json_error(
        'The token is invalid',
        400
      );
    }
  }

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = json_decode($integrationData->integration_details);
    $access_token = $integrationDetails->access_token;
    $listId = $integrationDetails->listId;
    $mainAction = $integrationDetails->mainAction;
    $fieldMap = $integrationDetails->field_map;

    if (
      empty($mainAction)
      || empty($access_token)
    ) {
      return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for SendFox api', 'bit-form'));
    }
    $recordApiHelper = new RecordApiHelper($access_token, $this->integrationID, $logID, $entryID);
    $sendFoxApiResponse = $recordApiHelper->execute(
      $listId,
      $mainAction,
      $fieldValues,
      $fieldMap,
      $access_token,
      $integrationDetails
    );

    if (is_wp_error($sendFoxApiResponse)) {
      return $sendFoxApiResponse;
    }
    return $sendFoxApiResponse;
  }
}
