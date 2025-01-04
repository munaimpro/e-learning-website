<?php

namespace BitCode\BitForm\Core\Integration\Dropbox;

use BitCode\BitForm\Core\Integration\Dropbox\RecordApiHelper as DropboxRecordApiHelper;
use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\ApiResponse;
use BitCode\BitForm\Core\Util\HttpHelper;
use BitCode\BitForm\Core\Util\IpTool;
use WP_Error;

class DropboxHandler
{
  private $formID;
  private $integrationID;
  protected static $apiBaseUri = 'https://api.dropboxapi.com';
  protected static $contentBaseUri = 'https://content.dropboxapi.com';

  public function __construct($integrationID, $fromID)
  {
    $this->formID = $fromID;
    $this->integrationID = $integrationID;
  }

  /**
   * Helps to register ajax function's with wp
   *
   * @return null
   */
  public static function registerAjax()
  {
    add_action('wp_ajax_bitforms_dropbox_authorization', [__CLASS__, 'checkAuthorization']);
    add_action('wp_ajax_bitforms_dropbox_get_all_folders', [__CLASS__, 'getAllFolders']);
  }

  /**
   * authorize dropbox
   *
   * @return JSON
   */
  public static function checkAuthorization()
  {
    if (!isset($_REQUEST['_ajax_nonce']) && !wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      wp_send_json_error(__('Token expired', 'bit-form'), 401);
    }

    $inputJSON = file_get_contents('php://input');
    $queryParams = json_decode($inputJSON);

    if (empty($queryParams->accessCode) || empty($queryParams->apiKey) || empty($queryParams->apiSecret)) {
      wp_send_json_error(__('Requested parameter is empty', 'bit-form'), 400);
    }

    $body = [
      'code'          => $queryParams->accessCode,
      'grant_type'    => 'authorization_code',
      'client_id'     => $queryParams->apiKey,
      'client_secret' => $queryParams->apiSecret,
    ];

    $apiEndpoint = self::$apiBaseUri . '/oauth2/token';
    $apiResponse = HttpHelper::post($apiEndpoint, $body);

    if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
      wp_send_json_error(empty($apiResponse->error_description) ? 'Unknown' : $apiResponse->error_description, 400);
    }
    $apiResponse->generates_on = \time();
    wp_send_json_success($apiResponse, 200);
  }

  /**
   * get dropbox folders List
   *
   * @return JSON
   */
  public static function getAllFolders()
  {
    if (!isset($_REQUEST['_ajax_nonce']) && !wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      wp_send_json_error(__('Token expired', 'bit-form'), 401);
    }

    $inputJSON = file_get_contents('php://input');
    $queryParams = json_decode($inputJSON);

    if (empty($queryParams->tokenDetails) || empty($queryParams->apiKey) || empty($queryParams->apiSecret)) {
      wp_send_json_error(__('Requested parameter is empty', 'bit-form'), 400);
    }

    $token = self::tokenExpiryCheck($queryParams->tokenDetails, $queryParams->apiKey, $queryParams->apiSecret);
    if ($token->access_token !== $queryParams->tokenDetails->access_token) {
      self::saveRefreshedToken($queryParams->formID, $queryParams->id, $token);
    }

    $folders = self::getDropboxFoldersList($token->access_token);
    $data = [];
    if ($folders->entries) {
      foreach ($folders->entries as $folder) {
        $folder = (array)$folder;
        if ('folder' === $folder['.tag']) {
          $data[] = (object) [
            'name'       => $folder['name'],
            'lower_path' => $folder['path_lower'],
          ];
        }
      }
    }

    $response['dropboxFoldersList'] = $data;
    $response['tokenDetails'] = $token;
    wp_send_json_success($response, 200);
  }

  public static function getDropboxFoldersList($token)
  {
    $headers = [
      'Content-Type'  => 'application/json; charset=utf-8',
      'Authorization' => 'Bearer ' . $token,
    ];
    $options = [
      'path'                           => '',
      'recursive'                      => true,
      'include_deleted'                => false,
      'include_mounted_folders'        => true,
      'include_non_downloadable_files' => true
    ];
    $options = wp_json_encode($options);

    $recipientApiEndpoint = self::$apiBaseUri . '/2/files/list_folder';
    $apiResponse = HttpHelper::post($recipientApiEndpoint, $options, $headers);
    if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
      return false;
    }
    return $apiResponse;
  }

  private static function tokenExpiryCheck($token, $apiKey, $apiSecret)
  {
    if (!$token) {
      return false;
    }

    if (($token->generates_on + $token->expires_in - 30) < time()) {
      $refreshToken = self::refreshToken($token->refresh_token, $apiKey, $apiSecret);
      if (is_wp_error($refreshToken) || !empty($refreshToken->error)) {
        return false;
      }

      $token->access_token = $refreshToken->access_token;
      $token->expires_in = $refreshToken->expires_in;
      $token->generates_on = $refreshToken->generates_on;
    }
    return $token;
  }

  private static function refreshToken($refresh_token, $apiKey, $apiSecret)
  {
    $body = [
      'grant_type'    => 'refresh_token',
      'client_id'     => $apiKey,
      'client_secret' => $apiSecret,
      'refresh_token' => $refresh_token,
    ];

    $apiEndpoint = self::$apiBaseUri . '/oauth2/token';
    $apiResponse = HttpHelper::post($apiEndpoint, $body);
    if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
      return false;
    }
    $token = $apiResponse;
    $token->generates_on = \time();
    return $token;
  }

  private static function saveRefreshedToken($formID, $integrationID, $tokenDetails)
  {
    if (empty($formID) || empty($integrationID)) {
      return;
    }

    $integrationHandler = new IntegrationHandler($formID, IpTool::getUserDetail());
    $dropboxDetails = $integrationHandler->getAIntegration($integrationID);
    if (is_wp_error($dropboxDetails)) {
      return;
    }

    $newDetails = json_decode($dropboxDetails[0]->integration_details);
    $newDetails->tokenDetails = $tokenDetails;
    $integrationHandler->updateIntegration($integrationID, $dropboxDetails[0]->integration_name, 'Dropbox', wp_json_encode($newDetails), 'form');
  }

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = json_decode($integrationData->integration_details);

    if (empty($integrationDetails->tokenDetails->access_token)) {
      (new ApiResponse())->apiResponse($logID, $this->integrationID, ['type' =>  'record', 'type_name' => 'insert'], 'error', 'Not Authorization By Dropbox.');
      return;
    }

    $actions = $integrationDetails->actions;
    $fieldMap = $integrationDetails->field_map;
    $tokenDetails = self::tokenExpiryCheck($integrationDetails->tokenDetails, $integrationDetails->apiKey, $integrationDetails->apiSecret);
    if ($tokenDetails->access_token !== $integrationDetails->tokenDetails->access_token) {
      self::saveRefreshedToken($this->formID, $this->integrationID, $tokenDetails);
    }

    if (empty($fieldMap)) {
      return new WP_Error('REQ_FIELD_EMPTY', __('Required data not found.', 'bit-form'));
    }

    $dropboxResponse = (new DropboxRecordApiHelper($tokenDetails->access_token, $this->formID, $entryID));
    $apiResponse = $dropboxResponse->executeRecordApi($this->integrationID, $logID, $fieldValues, $fieldMap, $actions);
    if (is_wp_error($dropboxResponse)) {
      return $dropboxResponse;
    }
    return $apiResponse;
  }
}
