<?php

/**
 * ZohoWorkDrive Integration
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoWorkDrive;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\HttpHelper;
use BitCode\BitForm\Core\Util\IpTool;

/**
 * Provide functionality for ZohoCrm integration
 */
class ZohoWorkDriveHandler
{
  private $_formID;
  private $_integrationID;

  public function __construct($integrationID, $formID)
  {
    $this->_formID = $formID;
    $this->_integrationID = $integrationID;
  }

  /**
   * Helps to register ajax function's with wp
   *
   * @return null
   */
  public static function registerAjax()
  {
    add_action('wp_ajax_bitforms_zworkdrive_generate_token', [__CLASS__, 'generateTokens']);
    add_action('wp_ajax_bitforms_zworkdrive_refresh_teams', [__CLASS__, 'refreshTeamsAjaxHelper']);
    add_action('wp_ajax_bitforms_zworkdrive_refresh_team_folders', [__CLASS__, 'refreshTeamFoldersAjaxHelper']);
    add_action('wp_ajax_bitforms_zworkdrive_refresh_sub_folders', [__CLASS__, 'refreshSubFoldersAjaxHelper']);
    add_action('wp_ajax_bitforms_zworkdrive_refresh_users', [__CLASS__, 'refreshUsersAjaxHelper']);
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
        empty($requestsParams->{'accounts-server'})
        || empty($requestsParams->dataCenter)
        || empty($requestsParams->clientId)
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

      $apiEndpoint = \urldecode($requestsParams->{'accounts-server'}) . '/oauth/v2/token';
      $requestParams = [
        'grant_type'    => 'authorization_code',
        'client_id'     => $requestsParams->clientId,
        'client_secret' => $requestsParams->clientSecret,
        'redirect_uri'  => \urldecode($requestsParams->redirectURI),
        'code'          => $requestsParams->code
      ];
      $apiResponse = HttpHelper::post($apiEndpoint, $requestParams);

      $zuidEndpoint = "https://workdrive.zoho.{$requestsParams->dataCenter}/api/v1/users/me";
      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$apiResponse->access_token}";
      $zuidResponse = HttpHelper::get($zuidEndpoint, null, $authorizationHeader);

      $apiResponse->zuid = $zuidResponse->data->id;

      if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
        wp_send_json_error(
          empty($apiResponse->error) ? 'Unknown' : $apiResponse->error,
          400
        );
      }
      if (is_wp_error($zuidResponse) || !empty($zuidResponse->error)) {
        wp_send_json_error(
          empty($zuidResponse->error) ? 'Unknown' : $zuidResponse->error,
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

  public static function refreshTeamsAjaxHelper()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $authorizationHeader = null;
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->tokenDetails)
        || empty($queryParams->dataCenter)
        || empty($queryParams->clientId)
        || empty($queryParams->clientSecret)
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
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = ZohoWorkDriveHandler::_refreshAccessToken($queryParams);
      }

      $teamsMetaApiEndpoint = "https://workdrive.zoho.{$queryParams->dataCenter}/api/v1/users/{$queryParams->tokenDetails->zuid}/teams";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $teamsMetaResponse = HttpHelper::get($teamsMetaApiEndpoint, null, $authorizationHeader);

      if (!is_wp_error($teamsMetaResponse)) {
        $allTeams = [];
        $teams = $teamsMetaResponse->data;

        if (count($teams) > 0) {
          foreach ($teams as $team) {
            $allTeams[$team->attributes->name] = (object) [
              'teamId'   => $team->id,
              'teamName' => $team->attributes->name
            ];
          }
        }
        uksort($allTeams, 'strnatcasecmp');
        $response['teams'] = $allTeams;
      } else {
        wp_send_json_error(
          empty($teamsMetaResponse->data) ? 'Unknown' : $teamsMetaResponse->error,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoWorkDriveHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['teams']);
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
   * Process ajax request for refresh crm modules
   *
   * @return JSON crm module data
   */
  public static function refreshTeamFoldersAjaxHelper()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $authorizationHeader = null;
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->tokenDetails)
        || empty($queryParams->dataCenter)
        || empty($queryParams->clientId)
        || empty($queryParams->clientSecret)
        || empty($queryParams->team)
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
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = ZohoWorkDriveHandler::_refreshAccessToken($queryParams);
      }

      // My Folders (Private)
      $currentUserMetaApiEndpoint = "https://workdrive.zoho.{$queryParams->dataCenter}/api/v1/teams/{$queryParams->team}/currentuser";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $currentUserMetaResponse = HttpHelper::get($currentUserMetaApiEndpoint, null, $authorizationHeader);
      $currentUserId = $currentUserMetaResponse->data->id;

      $getMyFolderIdMetaApiEndpoint = "https://workdrive.zoho.{$queryParams->dataCenter}/api/v1/users/{$currentUserId}/privatespace";
      $getMyFolderIdMetaResponse = HttpHelper::get($getMyFolderIdMetaApiEndpoint, null, $authorizationHeader);

      if (!is_wp_error($getMyFolderIdMetaResponse)) {
        if (count($getMyFolderIdMetaResponse) > 0) {
          $response['teamFolders'][$getMyFolderIdMetaResponse->data[0]->id] = (object) [
            'teamFolderId'   => $getMyFolderIdMetaResponse->data[0]->id,
            'teamFolderName' => 'My Folders',
            'type'           => 'private'
          ];
        }
      }

      // Team Folders
      $getTeamFoldersMetaApiEndpoint = "https://workdrive.zoho.{$queryParams->dataCenter}/api/v1/teams/{$queryParams->team}/workspaces";
      $getTeamFoldersMetaResponse = HttpHelper::get($getTeamFoldersMetaApiEndpoint, null, $authorizationHeader);

      if (!is_wp_error($getTeamFoldersMetaResponse)) {
        $teamFolders = $getTeamFoldersMetaResponse->data;

        if (count($teamFolders) > 0) {
          foreach ($teamFolders as $teamFolder) {
            $response['teamFolders'][$teamFolder->id] = (object) [
              'teamFolderId'   => $teamFolder->id,
              'teamFolderName' => $teamFolder->attributes->display_html_name,
              'type'           => 'team'
            ];
          }
        }
      } else {
        wp_send_json_error(
          empty($getTeamFoldersMetaResponse->data) ? 'Unknown' : $getTeamFoldersMetaResponse->error,
          400
        );
      }

      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoWorkDriveHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['teamFolders']);
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
   * Process ajax request for refesh crm layouts
   *
   * @return JSON crm layout data
   */
  public static function refreshSubFoldersAjaxHelper()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $authorizationHeader = null;
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->tokenDetails)
        || empty($queryParams->dataCenter)
        || empty($queryParams->clientId)
        || empty($queryParams->clientSecret)
        || empty($queryParams->folder)
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
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = ZohoWorkDriveHandler::_refreshAccessToken($queryParams);
      }

      $foldersMetaApiEndpoint = '';

      if ('team' === $queryParams->teamType) {
        $foldersMetaApiEndpoint = "https://workdrive.zoho.{$queryParams->dataCenter}/api/v1/files/{$queryParams->folder}/files";
      } else {
        $foldersMetaApiEndpoint = "https://workdrive.zoho.{$queryParams->dataCenter}/api/v1/privatespace/{$queryParams->folder}/files";
      }

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $foldersMetaResponse = HttpHelper::get($foldersMetaApiEndpoint, null, $authorizationHeader);

      if (!is_wp_error($foldersMetaResponse)) {
        $folders = $foldersMetaResponse->data;

        if (count($folders) > 0) {
          foreach ($folders as $folder) {
            if ($folder->attributes->is_folder) {
              $response['folders'][] = (object) [
                'folderId'   => $folder->id,
                'folderName' => $folder->attributes->display_html_name,
                'hasFolder'  => $folder->attributes->has_folders,
              ];
            }
          }
        }
      } else {
        wp_send_json_error(
          'error' === $foldersMetaResponse->status ? $foldersMetaResponse->message : 'Unknown',
          400
        );
      }
      if (!empty($response['tokenDetails']) && $response['tokenDetails'] && !empty($queryParams->id)) {
        $response['queryModule'] = $queryParams->module;
        ZohoWorkDriveHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['folders']);
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

  public static function refreshUsersAjaxHelper()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $authorizationHeader = null;
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->tokenDetails)
        || empty($queryParams->dataCenter)
        || empty($queryParams->clientId)
        || empty($queryParams->clientSecret)
        || empty($queryParams->team)
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
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = ZohoWorkDriveHandler::_refreshAccessToken($queryParams);
      }

      $usersMetaApiEndpoint = "https://workdrive.zoho.{$queryParams->dataCenter}/api/v1/teams/{$queryParams->team}/users";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $usersMetaResponse = HttpHelper::get($usersMetaApiEndpoint, null, $authorizationHeader);

      if (!is_wp_error($usersMetaResponse)) {
        $allUsers = [];
        $users = $usersMetaResponse->data;

        if (count($users) > 0) {
          foreach ($users as $user) {
            if ('users' === $user->type) {
              $allUsers[$user->attributes->display_name] = (object) [
                'userId'   => $user->attributes->email_id,
                'userName' => $user->attributes->display_name
              ];
            }
          }
        }
        uksort($allUsers, 'strnatcasecmp');
        $response['users'] = $allUsers;
      } else {
        wp_send_json_error(
          empty($usersMetaResponse->data) ? 'Unknown' : $usersMetaResponse->error,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoWorkDriveHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['users']);
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
   * Helps to refresh zoho crm access_token
   *
   * @param  object $apiData Contains required data for refresh access token
   * @return string|boolean  $tokenDetails API token details
   */
  protected static function _refreshAccessToken($apiData)
  {
    if (
      empty($apiData->dataCenter)
      || empty($apiData->clientId)
      || empty($apiData->clientSecret)
      || empty($apiData->tokenDetails)
    ) {
      return false;
    }
    $tokenDetails = $apiData->tokenDetails;

    $dataCenter = $apiData->dataCenter;
    $apiEndpoint = "https://accounts.zoho.{$dataCenter}/oauth/v2/token";
    $requestParams = [
      'grant_type'    => 'refresh_token',
      'client_id'     => $apiData->clientId,
      'client_secret' => $apiData->clientSecret,
      'refresh_token' => $tokenDetails->refresh_token,
    ];

    $apiResponse = HttpHelper::post($apiEndpoint, $requestParams);
    if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
      return false;
    }
    $tokenDetails->generates_on = \time();
    $tokenDetails->access_token = $apiResponse->access_token;
    return $tokenDetails;
  }

  /**
   * Save updated access_token to avoid unnecessary token generation
   *
   * @param integer $formID        ID of Integration related form
   * @param integer $integrationID ID of Zoho crm Integration
   * @param object $tokenDetails  refreshed token info
   *
   * @return null
   */
  protected static function _saveRefreshedToken($formID, $integrationID, $tokenDetails, $others = null)
  {
    if (empty($formID) || empty($integrationID)) {
      return;
    }

    $integrationHandler = new IntegrationHandler($formID, IpTool::getUserDetail());
    $zworkdriveDetails = $integrationHandler->getAIntegration($integrationID);

    if (is_wp_error($zworkdriveDetails)) {
      return;
    }
    $newDetails = json_decode($zworkdriveDetails[0]->integration_details);

    $newDetails->tokenDetails = $tokenDetails;
    if (!empty($others['teams'])) {
      $newDetails->default->teams = $others['teams'];
    }
    if (!empty($others['teamFolders'])) {
      $newDetails->default->teamFolders = $others['teamFolders'];
    }

    $integrationHandler->updateIntegration($integrationID, $zworkdriveDetails[0]->integration_name, 'Zoho WorkDrive', wp_json_encode($newDetails), 'form');
  }

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = is_string($integrationData->integration_details) ? json_decode($integrationData->integration_details) : $integrationData->integration_details;

    $tokenDetails = $integrationDetails->tokenDetails;
    $team = $integrationDetails->team;
    $folder = $integrationDetails->folder;
    $dataCenter = $integrationDetails->dataCenter;
    $actions = $integrationDetails->actions;
    $default = $integrationDetails->default;
    if (
      empty($tokenDetails)
      || empty($folder)
    ) {
      return new WP_Error('REQ_FIELD_EMPTY', __('parent folder are required for zoho workdrive api', 'bit-form'));
    }

    $requiredParams = null;
    if ((intval($tokenDetails->generates_on) + (55 * 60)) < time()) {
      $requiredParams['clientId'] = $integrationDetails->clientId;
      $requiredParams['clientSecret'] = $integrationDetails->clientSecret;
      $requiredParams['dataCenter'] = $integrationDetails->dataCenter;
      $requiredParams['tokenDetails'] = $tokenDetails;
      $newTokenDetails = ZohoWorkDriveHandler::_refreshAccessToken((object)$requiredParams);
      if ($newTokenDetails) {
        ZohoWorkDriveHandler::_saveRefreshedToken($this->_formID, $this->_integrationID, $newTokenDetails);
        $tokenDetails = $newTokenDetails;
      }
    }

    // $actions = $integrationDetails->actions;
    $recordApiHelper = new RecordApiHelper($tokenDetails, $this->_integrationID, $logID);

    $zworkdriveApiResponse = $recordApiHelper->executeRecordApi(
      $team,
      $folder,
      $dataCenter,
      $fieldValues,
      $default,
      $actions,
      $entryID,
      $this->_formID
    );

    if (is_wp_error($zworkdriveApiResponse)) {
      return $zworkdriveApiResponse;
    }
    return $zworkdriveApiResponse;
  }
}
