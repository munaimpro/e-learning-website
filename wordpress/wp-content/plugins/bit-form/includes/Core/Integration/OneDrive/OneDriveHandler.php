<?php

namespace BitCode\BitForm\Core\Integration\OneDrive;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Integration\OneDrive\RecordApiHelper as OneDriveRecordApiHelper;
use BitCode\BitForm\Core\Util\ApiResponse;
use BitCode\BitForm\Core\Util\HttpHelper;
use BitCode\BitForm\Core\Util\IpTool;

class OneDriveHandler
{
  private $formID;
  private $integrationID;

  public function __construct($integrationID, $fromID)
  {
    $this->formID = $fromID;
    $this->integrationID = $integrationID;
  }

  public static function registerAjax()
  {
    add_action('wp_ajax_bitforms_oneDrive_authorization', [__CLASS__, 'authorization']);
    add_action('wp_ajax_bitforms_oneDrive_get_all_folders', [__CLASS__, 'getAllFolders']);
    add_action('wp_ajax_bitforms_oneDrive_get_single_folder', [__CLASS__, 'singleOneDriveFolderList']);
  }

  public static function authorization()
  {
    if (!isset($_REQUEST['_ajax_nonce']) && !wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      wp_send_json_error(__('Token expired', 'bit-form'), 401);
    }

    $inputJSON = file_get_contents('php://input');
    $queryParams = json_decode($inputJSON);

    if (empty($queryParams->clientId) || empty($queryParams->clientSecret) || empty($queryParams->code) || empty($queryParams->redirectURI)) {
      wp_send_json_error(__('Requested parameter is empty', 'bit-form'), 400);
    }

    $body = [
      'client_id'     => $queryParams->clientId,
      'redirect_uri'  => urldecode($queryParams->redirectURI),
      'client_secret' => $queryParams->clientSecret,
      'grant_type'    => 'authorization_code',
      'code'          => urldecode($queryParams->code)
    ];

    $apiEndpoint = 'https://login.live.com/oauth20_token.srf';
    $header['Content-Type'] = 'application/x-www-form-urlencoded';
    $apiResponse = HttpHelper::post($apiEndpoint, $body, $header);
    if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
      wp_send_json_error(empty($apiResponse->error_description) ? 'Unknown' : $apiResponse->error_description, 400);
    }
    $apiResponse->generates_on = \time();
    wp_send_json_success($apiResponse, 200);
  }

  public static function getAllFolders()
  {
    if (!isset($_REQUEST['_ajax_nonce']) && !wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      wp_send_json_error(__('Token expired', 'bit-form'), 401);
    }

    $inputJSON = file_get_contents('php://input');
    $queryParams = json_decode($inputJSON);
    if (empty($queryParams->tokenDetails) || empty($queryParams->clientId) || empty($queryParams->clientSecret)) {
      wp_send_json_error(__('Requested parameter is empty', 'bit-form'), 400);
    }

    $token = self::tokenExpiryCheck($queryParams->tokenDetails, $queryParams->clientId, $queryParams->clientSecret);
    if ($token->access_token !== $queryParams->tokenDetails->access_token) {
      self::saveRefreshedToken($queryParams->formID, $queryParams->id, $token);
    }

    $folders = self::getOneDriveFoldersList($token->access_token);
    $foldersOnly = $folders->value;

    $data = [];
    if (is_array($foldersOnly)) {
      foreach ($foldersOnly as $folder) {
        if (property_exists($folder, 'folder')) {
          $data[] = $folder;
        }
      }
    }
    $response['oneDriveFoldersList'] = $data;
    $response['tokenDetails'] = $token;
    wp_send_json_success($response, 200);
  }

  public static function getOneDriveFoldersList($token)
  {
    $headers = [
      'Accept'        => 'application/json',
      'Content-Type'  => 'application/json;',
      'Authorization' => 'bearer ' . $token,
    ];
    $apiEndpoint = 'https://api.onedrive.com/v1.0/drive/root/children';
    $apiResponse = HttpHelper::get($apiEndpoint, [], $headers);
    if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
      return false;
    }
    return $apiResponse;
  }

  public static function singleOneDriveFolderList()
  {
    if (!isset($_REQUEST['_ajax_nonce']) && !wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      wp_send_json_error(__('Token expired', 'bit-form'), 401);
    }

    $inputJSON = file_get_contents('php://input');
    $queryParams = json_decode($inputJSON);

    if (empty($queryParams->tokenDetails) || empty($queryParams->clientId) || empty($queryParams->clientSecret)) {
      wp_send_json_error(__('Requested parameter is empty', 'bit-form'), 400);
    }

    $ids = explode('!', $queryParams->folder);
    $token = self::tokenExpiryCheck($queryParams->tokenDetails, $queryParams->clientId, $queryParams->clientSecret);
    if ($token->access_token !== $queryParams->tokenDetails->access_token) {
      self::saveRefreshedToken($queryParams->formID, $queryParams->id, $token);
    }

    $headers = [
      'Accept'        => 'application/json',
      'Content-Type'  => 'application/json;',
      'Authorization' => 'bearer ' . $queryParams->tokenDetails->access_token,
    ];
    $apiEndpoint = 'https://api.onedrive.com/v1.0/drives/' . $ids[0] . '/items/' . $queryParams->folder . '/children';
    $apiResponse = HttpHelper::get($apiEndpoint, [], $headers);
    $foldersOnly = $apiResponse->value;
    $data = [];
    if (is_array($foldersOnly)) {
      foreach ($foldersOnly as $folder) {
        if (property_exists($folder, 'folder')) {
          $data[] = $folder;
        }
      }
    }
    $response['folders'] = $data;
    $response['tokenDetails'] = $token;
    wp_send_json_success($response, 200);
  }

  private static function tokenExpiryCheck($token, $clientId, $clientSecret)
  {
    if (!$token) {
      return false;
    }

    if ((intval($token->generates_on) + (55 * 60)) < time()) {
      $refreshToken = self::refreshToken($token->refresh_token, $clientId, $clientSecret);
      if (is_wp_error($refreshToken) || !empty($refreshToken->error)) {
        return false;
      }
      $token->access_token = $refreshToken->access_token;
      $token->expires_in = $refreshToken->expires_in;
      $token->generates_on = $refreshToken->generates_on;
    }
    return $token;
  }

  private static function refreshToken($refresh_token, $clientId, $clientSecret)
  {
    $body = [
      'client_id'     => $clientId,
      'client_secret' => $clientSecret,
      'grant_type'    => 'refresh_token',
      'refresh_token' => $refresh_token,
    ];

    $apiEndpoint = 'https://login.live.com/oauth20_token.srf';
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
    $oneDriveDetails = $integrationHandler->getAIntegration($integrationID);
    if (is_wp_error($oneDriveDetails)) {
      return;
    }

    $newDetails = json_decode($oneDriveDetails[0]->integration_details);
    $newDetails->tokenDetails = $tokenDetails;
    $integrationHandler->updateIntegration($integrationID, $oneDriveDetails[0]->integration_name, 'OneDrive', wp_json_encode($newDetails), 'form');
  }

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = json_decode($integrationData->integration_details);

    if (empty($integrationDetails->tokenDetails->access_token)) {
      (new ApiResponse())->apiResponse($logID, $this->integrationID, ['type' =>  'record', 'type_name' => 'insert'], 'error', 'Not Authorization By OneDrive.');
      return;
    }

    $actions = $integrationDetails->actions;
    $folderId = $integrationDetails->folder;
    $fieldMap = $integrationDetails->field_map;
    $tokenDetails = self::tokenExpiryCheck($integrationDetails->tokenDetails, $integrationDetails->clientId, $integrationDetails->clientSecret);
    $parentId = $integrationDetails->folderMap[0];
    if ($tokenDetails->access_token !== $integrationDetails->tokenDetails->access_token) {
      self::saveRefreshedToken($this->formID, $this->integrationID, $tokenDetails);
    }

    (new OneDriveRecordApiHelper($tokenDetails->access_token, $this->formID, $entryID))->executeRecordApi($this->integrationID, $logID, $fieldValues, $fieldMap, $actions, $folderId, $parentId);
    return true;
  }
}
