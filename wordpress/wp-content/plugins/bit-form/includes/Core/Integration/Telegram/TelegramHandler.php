<?php

/**
 * Telegrom Integration
 *
 */

namespace BitCode\BitForm\Core\Integration\Telegram;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\HttpHelper;
use WP_Error;

/**
 * Provide functionality for Telegram integration
 */
class TelegramHandler
{
  private $_formID;
  private $_integrationID;
  public static $api_endpoint = 'https://api.telegram.org/bot';

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
    add_action('wp_ajax_bitforms_telegram_authorize', [__CLASS__, 'telegramAuthorize']);
    add_action('wp_ajax_bitforms_refresh_get_updates', [__CLASS__, 'refreshGetUpdates']);
  }

  /**
   * Process ajax request for generate_token
   *
   * @return JSON zoho crm api response and status
   */
  public static function telegramAuthorize()
  {
    $authorizationHeader = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $requestsParams = json_decode($inputJSON);
      if (
        empty($requestsParams->bot_api_key)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }

      $apiEndpoint = self::$api_endpoint . $requestsParams->bot_api_key . '/getMe';
      $authorizationHeader['Accept'] = 'application/x-www-form-urlencoded';
      $apiResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);

      if (is_wp_error($apiResponse) || !$apiResponse->ok) {
        wp_send_json_error(
          empty($apiResponse->error_code) ? 'Unknown' : $apiResponse,
          400
        );
      }
      $apiEndpoint = self::$api_endpoint . $requestsParams->bot_api_key . '/getUpdates';
      $authorizationHeader['Accept'] = 'application/x-www-form-urlencoded';
      $apiResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);

      if (is_wp_error($apiResponse) || !$apiResponse->ok) {
        wp_send_json_error(
          empty($apiResponse->error_code) ? 'Unknown' : $apiResponse,
          400
        );
      }

      wp_send_json_success(true);
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
   * Process ajax request for refresh telegram get Updates
   *
   * @return JSON telegram get Updates data
   */
  public static function refreshGetUpdates()
  {
    $authorizationHeader = null;
    $response = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $requestsParams = json_decode($inputJSON);
      if (
        empty($requestsParams->bot_api_key)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $apiEndpoint = self::$api_endpoint . $requestsParams->bot_api_key . '/getUpdates';
      $authorizationHeader['Accept'] = 'application/json';
      $telegramResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);

      $allList = [];
      if (!is_wp_error($telegramResponse) && $telegramResponse->ok) {
        $telegramChatLists = $telegramResponse->result;

        foreach ($telegramChatLists as $list) {
          if (!empty($list->my_chat_member->chat->title) && !empty($list->my_chat_member->chat->id)) {
            $allList[$list->my_chat_member->chat->title] = (object) [
              'id'   => $list->my_chat_member->chat->id,
              'name' => $list->my_chat_member->chat->title,
            ];
          }
        }
        uksort($allList, 'strnatcasecmp');

        $response['telegramChatLists'] = $allList;
      } else {
        wp_send_json_error(
          $telegramResponse->description,
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

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = is_string($integrationData->integration_details) ? json_decode($integrationData->integration_details) : $integrationData->integration_details;

    $bot_api_key = $integrationDetails->bot_api_key;
    $parse_mode = $integrationDetails->parse_mode;
    $chat_id = $integrationDetails->chat_id;
    $body = $integrationDetails->body;

    if (
      empty($bot_api_key)
      || empty($parse_mode)
      || empty($chat_id)
      || empty($body)
    ) {
      return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Telegram api', 'bit-form'));
    }
    $recordApiHelper = new RecordApiHelper(self::$api_endpoint . $bot_api_key, $this->_integrationID, $logID, $entryID);
    $telegramApiResponse = $recordApiHelper->executeRecordApi(
      $integrationDetails,
      $fieldValues,
      $this->_formID,
      $entryID
    );

    if (is_wp_error($telegramApiResponse)) {
      return $telegramApiResponse;
    }
    return $telegramApiResponse;
  }
}
