<?php

/**
 * Groundhogg Integration
 */

namespace BitCode\BitForm\Core\Integration\Groundhogg;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\HttpHelper;
use WP_Error;

/**
 * Provide functionality for Groundhogg integration
 */
class GroundhoggHandler
{
  private $integrationID;

  public function __construct($integrationID, $fromID)
  {
    $this->integrationID = $integrationID;
  }

  public static function registerAjax()
  {
    add_action('wp_ajax_bitforms_groundhogg_authorization_and_fetch_contacts', [__CLASS__, 'fetchAllContacts']);
    add_action('wp_ajax_bitforms_groundhogg_fetch_all_tags', [__CLASS__, 'groundhoggFetchAllTags']);
  }

  public static function groundhoggFetchAllTags()
  {
    if (!isset($_REQUEST['_ajax_nonce']) && !wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      wp_send_json_error(__('Token expired', 'bit-form'), 401);
    }

    $inputJSON = file_get_contents('php://input');
    $requestParams = json_decode($inputJSON);

    if (
      empty($requestParams->public_key) || empty($requestParams->token)
      || empty($requestParams->domainName)
    ) {
      wp_send_json_error(
        __(
          'Requested parameter is empty',
          'bit-form'
        ),
        400
      );
    }

    $authorizationHeader = [
      'gh-token'      => $requestParams->token,
      'gh-public-key' => $requestParams->public_key
    ];

    $apiEndpoint = $requestParams->domainName . '/wp-json/gh/v3/tags';
    $apiResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);

    if ('success' === $apiResponse->status) {
      $apiResponse;
      wp_send_json_success($apiResponse, 200);
    } else {
      wp_send_json_error(
        'There is an error .',
        400
      );
    }
  }

  public static function fetchAllContacts()
  {
    if (!isset($_REQUEST['_ajax_nonce']) && !wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      wp_send_json_error(__('Token expired', 'bit-form'), 401);
    }

    $inputJSON = file_get_contents('php://input');
    $requestParams = json_decode($inputJSON);

    if (
      empty($requestParams->public_key) || empty($requestParams->token)
      || empty($requestParams->domainName)
    ) {
      wp_send_json_error(
        __(
          'Requested parameter is empty',
          'bit-form'
        ),
        400
      );
    }

    $authorizationHeader = [
      'gh-token'      => $requestParams->token,
      'gh-public-key' => $requestParams->public_key
    ];

    $apiEndpoint = $requestParams->domainName . '/wp-json/gh/v3/contacts';
    $apiResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);

    if (!empty($apiResponse->status) && 'success' === $apiResponse->status) {
      $apiResponse;
      wp_send_json_success($apiResponse, 200);
    } else {
      wp_send_json_error(
        'There is an error .',
        400
      );
    }
  }

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = json_decode($integrationData->integration_details);
    $token = $integrationDetails->token;
    $public_key = $integrationDetails->public_key;
    $domainName = $integrationDetails->domainName;
    $mainAction = $integrationDetails->mainAction;
    $fieldMap = $integrationDetails->field_map;
    $actions = $integrationDetails->actions;
    $defaultDataConf = $integrationDetails->default;

    if (
      empty($token)
      || empty($public_key)
      || empty($domainName)
      || empty($fieldMap)
    ) {
      return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Trello api', 'bit-form'));
    }
    $recordApiHelper = new RecordApiHelper($this->integrationID, $logID, $entryID);
    $acumbamailApiResponse = $recordApiHelper->execute(
      $mainAction,
      $defaultDataConf,
      $fieldValues,
      $fieldMap,
      $public_key,
      $token,
      $actions,
      $integrationDetails
    );

    if (is_wp_error($acumbamailApiResponse)) {
      return $acumbamailApiResponse;
    }
    return $acumbamailApiResponse;
  }
}
