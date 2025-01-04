<?php

/**
 * Encharge Integration
 *
 */

namespace BitCode\BitForm\Core\Integration\Encharge;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\HttpHelper;
use WP_Error;

/**
 * Provide functionality for Encharge integration
 */
class EnchargeHandler
{
  private $_integrationID;
  public static $api_endpoint = 'https://api.encharge.io/v1/';

  public function __construct($integrationID, $fromID)
  {
    $this->_integrationID = $integrationID;
  }

  /**
   * Helps to register ajax function's with wp
   *
   * @return null
   */
  public static function registerAjax()
  {
    add_action('wp_ajax_bitforms_encharge_authorize', [__CLASS__, 'enChargeAuthorize']);
    add_action('wp_ajax_bitforms_encharge_headers', [__CLASS__, 'enchargeHeaders']);
  }

  /**
   * Process ajax request for generate_token
   *
   * @return JSON enchagre user Authorization
   */
  public static function enChargeAuthorize()
  {
    $authorizationHeader = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $requestsParams = json_decode($inputJSON);
      if (
        empty($requestsParams->api_key)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }

      $apiEndpoint = self::$api_endpoint . 'accounts/info';
      $authorizationHeader['Accept'] = 'application/json';
      $authorizationHeader['X-Encharge-Token'] = $requestsParams->api_key;
      $apiResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);

      if (is_wp_error($apiResponse) || $apiResponse->error) {
        wp_send_json_error(
          empty($apiResponse->code) ? 'Unknown' : $apiResponse->error->message,
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
   * Process ajax request for refresh crm modules
   *
   * @return JSON Encharge field
   */
  public static function enchargeHeaders()
  {
    $authorizationHeader = null;
    $response = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->api_key)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $apiEndpoint = self::$api_endpoint . 'fields';
      $authorizationHeader['Accept'] = 'application/json';
      $authorizationHeader['X-Encharge-Token'] = $queryParams->api_key;
      $enChargeResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);
      $fields = [];
      if (!is_wp_error($enChargeResponse)) {
        $allFields = $enChargeResponse->items;
        // wp_send_json_success($allFields);
        foreach ($allFields as $field) {
          $required = 'email' === $field->name ? true : false;
          $fields[$field->name] = (object) [
            'fieldId'   => $field->name,
            'fieldName' => ucfirst($field->name),
            'required'  => $required
          ];
        }
        $response['enChargeFields'] = $fields;
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

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = is_string($integrationData->integration_details) ? json_decode($integrationData->integration_details) : $integrationData->integration_details;

    $api_key = $integrationDetails->api_key;
    $fieldMap = $integrationDetails->field_map;
    $tags = property_exists($integrationDetails, 'tags') ? $integrationDetails->tags : null;

    if (
      empty($api_key)
      || empty($fieldMap)
    ) {
      return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Encharge api', 'bit-form'));
    }
    $recordApiHelper = new RecordApiHelper($api_key, $this->_integrationID, $logID, $entryID);
    $enchagreApiResponse = $recordApiHelper->executeRecordApi(
      $fieldValues,
      $fieldMap,
      $tags
    );

    if (is_wp_error($enchagreApiResponse)) {
      return $enchagreApiResponse;
    }
    return $enchagreApiResponse;
  }
}
