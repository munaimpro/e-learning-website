<?php

/**
 * Rapidmail Integration
 *
 */

namespace BitCode\BitForm\Core\Integration\Rapidmail;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\HttpHelper;
use WP_Error;

class RapidmailHandler
{
  private $_integrationID;
  public static $apiBaseUri = 'https://apiv3.emailsys.net/v1';
  protected $_defaultHeader;

  public function __construct($integrationID)
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
    add_action('wp_ajax_bitforms_rapidmail_authorization', [__CLASS__, 'checkAuthorization']);
    add_action('wp_ajax_bitforms_rapidmail_get_all_recipients', [__CLASS__, 'getAllRecipients']);
    add_action('wp_ajax_bitforms_rapidmail_get_all_fields', [__CLASS__, 'getAllFields']);
  }

  public static function checkAuthorization()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $tokenRequestParams = json_decode($inputJSON);

      if (
        empty($tokenRequestParams->username)
        || empty($tokenRequestParams->password)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $header = [
        'Authorization' => 'Basic ' . base64_encode("$tokenRequestParams->username:$tokenRequestParams->password"),
        'Accept'        => '*/*',
        'verify'        => false
      ];
      $apiEndpoint = self::$apiBaseUri . '/apiusers';

      $apiResponse = HttpHelper::get($apiEndpoint, null, $header);
      if (!(property_exists($apiResponse, '_embedded') && property_exists($apiResponse->_embedded, 'apiusers'))) {
        wp_send_json_error(
          // empty($apiResponse->error) ? 'Unknown' : $apiResponse->error,
          'Unauthorize',
          400
        );
      } else {
        $apiResponse->generates_on = \time();
        wp_send_json_success($apiResponse, 200);
      }
    }
  }

  /**
   * Process request for getting recipientlists from rapidmail
   *
   * @param $queryParams Mandatory params to get recipients
   *
   * @return JSON rapidmailmail recipientlists data
   */
  public static function getAllRecipients()
  {
    $response = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);

      if (
        empty($queryParams->username)
        || empty($queryParams->password)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $header = [
        'Authorization' => 'Basic ' . base64_encode("$queryParams->username:$queryParams->password"),
        'Accept'        => '*/*',
        'verify'        => false
      ];
      $recipientApiEndpoint = self::$apiBaseUri . '/recipientlists';
      $apiResponse = HttpHelper::get($recipientApiEndpoint, null, $header);
      $tempRecipient = $apiResponse->_embedded->recipientlists;
      $data = [];

      foreach ($tempRecipient as $list) {
        $data[] = (object) [
          'id'   => $list->id,
          'name' => $list->name
        ];
      }
      $response['recipientlists'] = $data;
      wp_send_json_success($response, 200);
    }
  }

  public static function getAllFields($queryParams)
  {
    if (
      empty($queryParams->username)
      || empty($queryParams->password)
    ) {
      wp_send_json_error(
        __(
          'Requested parameter is empty',
          'bit-form'
        ),
        400
      );
    }
    $header = [
      'Authorization' => 'Basic ' . base64_encode("$queryParams->username:$queryParams->password"),
      'Accept'        => '*/*',
      'verify'        => false
    ];
    $recipientApiEndpoint = self::$apiBaseUri . '/recipientlists';
    $apiResponse = HttpHelper::get($recipientApiEndpoint, null, $header);
    $tempRecipient = $apiResponse->_embedded->recipientlists;
    $data = [];

    foreach ($tempRecipient as $list) {
      $data[] = (object) [
        'id'   => $list->id,
        'name' => $list->name
      ];
    }
    $response['recipientlists'] = $data;
    wp_send_json_success($response, 200);
  }

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = $integrationData->integration_details;
    if (is_string($integrationDetails)) {
      $integrationDetails = json_decode($integrationDetails);
    }
    $fieldMap = $integrationDetails->field_map;
    $defaultDataConf = $integrationDetails->default;
    $username = $integrationDetails->username;
    $password = $integrationDetails->password;
    $recipientLists = $defaultDataConf->recipientlists;

    if (
      empty($username)
      || empty($password)
      || empty($fieldMap)
    ) {
      $error = new WP_Error('REQ_FIELD_EMPTY', __('username, password, fields are required for rapidmail api', 'bit-form'));
      return $error;
    }
    if (empty($recipientLists)) {
      $error = new WP_Error('REQ_FIELD_EMPTY', __('Recipient List are required for rapidmail api', 'bit-form'));
      return $error;
    }
    $actions = $integrationDetails->actions;

    $recordApiHelper = new RecordApiHelper($integrationDetails, $username, $password, $logID);
    $rapidmailResponse = $recordApiHelper->executeRecordApi(
      $this->_integrationID,
      $defaultDataConf,
      $recipientLists,
      $fieldValues,
      $fieldMap,
      $actions
    );
    if (is_wp_error($rapidmailResponse)) {
      return $rapidmailResponse;
    }
    return $rapidmailResponse;
  }
}
