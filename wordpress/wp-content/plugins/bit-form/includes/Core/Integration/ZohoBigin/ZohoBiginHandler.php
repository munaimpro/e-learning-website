<?php

/**
 * ZohoBigin Integration
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoBigin;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\HttpHelper;
use BitCode\BitForm\Core\Util\IpTool;
use WP_Error;

/**
 * Provide functionality for ZohoCrm integration
 */
class ZohoBiginHandler
{
  private $_formID;
  private $_integrationID;
  private $_logResponse;

  public function __construct($integrationID, $fromID)
  {
    $this->_formID = $fromID;
    $this->_integrationID = $integrationID;
    $this->_logResponse = new UtilApiResponse();
  }

  /**
   * Helps to register ajax function's with wp
   *
   * @return null
   */
  public static function registerAjax()
  {
    add_action('wp_ajax_bitforms_zbigin_generate_token', [__CLASS__, 'generateTokens']);
    add_action('wp_ajax_bitforms_zbigin_refresh_modules', [__CLASS__, 'refreshModulesAjaxHelper']);
    add_action('wp_ajax_bitforms_zbigin_refresh_notetypes', [__CLASS__, 'refreshNoteTypesAjaxHelper']);
    add_action('wp_ajax_bitforms_zbigin_refresh_related_lists', [__CLASS__, 'refreshRelatedModulesAjaxHelper']);
    add_action('wp_ajax_bitforms_zbigin_refresh_fields', [__CLASS__, 'getFields']);
    add_action('wp_ajax_bitforms_zbigin_refresh_tags', [__CLASS__, 'getTagList']);
    add_action('wp_ajax_bitforms_zbigin_refresh_users', [__CLASS__, 'getUsers']);
  }

  /**
   * Process ajax request for generate_token
   *
   * @return JSON zoho bigin api response and status
   */
  public static function generateTokens()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
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

      if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
        wp_send_json_error(
          empty($apiResponse->error) ? 'Unknown' : $apiResponse->error,
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

  /**
   * Process ajax request for refresh bigin modules
   *
   * @return JSON bigin module data
   */
  public static function refreshModulesAjaxHelper()
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
        $response['tokenDetails'] = ZohoBiginHandler::_refreshAccessToken($queryParams);
      }
      $modulesMetaApiEndpoint = "https://www.zohoapis.{$queryParams->dataCenter}/bigin/v1/settings/modules";
      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $modulesMetaResponse = HttpHelper::get($modulesMetaApiEndpoint, null, $authorizationHeader);
      // wp_send_json_success($modulesMetaResponse, 200);
      if (!is_wp_error($modulesMetaResponse) && (empty($modulesMetaResponse->status) || (!empty($modulesMetaResponse->status) && 'error' !== $modulesMetaResponse->status))) {
        $retriveModuleData = $modulesMetaResponse->modules;
        $allModules = [];
        foreach ($retriveModuleData as $module) {
          if (!in_array($module->api_name, ['Activities', 'Social', 'Associated_Products', 'Notes', 'Attachments'])) {
            $allModules[$module->plural_label] = (object) [
              'api_name'     => $module->api_name,
              'plural_label' => $module->plural_label
            ];
          }
        }
        uksort($allModules, 'strnatcasecmp');
        $response['modules'] = $allModules;
      } else {
        wp_send_json_error(
          empty($modulesMetaResponse->error) ? 'Unknown' : $modulesMetaResponse->error,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoBiginHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['modules']);
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
   * Process ajax request for refresh bigin modules
   *
   * @return JSON bigin module data
   */
  public static function refreshRelatedModulesAjaxHelper()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->tokenDetails)
        || empty($queryParams->dataCenter)
        || empty($queryParams->clientId)
        || empty($queryParams->clientSecret)
        || empty($queryParams->module)
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
      $relatedModules = [];

      $allModules = [
        'Tasks' => (object) [
          'api_name'     => 'Tasks',
          'plural_label' => 'Tasks'
        ],
        'Events' => (object) [
          'api_name'     => 'Events',
          'plural_label' => 'Events'
        ],
        'Calls' => (object) [
          'api_name'     => 'Calls',
          'plural_label' => 'Calls'
        ],
      ];
      // $modulesMetaApiEndpoint = "https://www.zohoapis.{$queryParams->dataCenter}/bigin/v1/settings/related_lists";
      // $authorizationHeader["Authorization"] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      // $requiredParams['module'] = $queryParams->module;
      // $modulesMetaResponse = HttpHelper::get($modulesMetaApiEndpoint, $queryParams, $authorizationHeader);
      // wp_send_json_success($modulesMetaResponse, 200);
      foreach ($allModules as $module) {
        if ($module->api_name !== $queryParams->module) {
          $relatedModules[$module->plural_label] = (object) [
            'api_name'     => $module->api_name,
            'plural_label' => $module->plural_label
          ];
        }
      }
      uksort($relatedModules, 'strnatcasecmp');
      $response['related_modules'] = $relatedModules;

      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoBiginHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['related_modules']);
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
   * Process ajax request for refesh bigin layouts
   *
   * @return JSON bigin layout data
   */
  public static function getFields()
  {
    $authorizationHeader = null;
    $requiredParams = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->module)
        || empty($queryParams->tokenDetails)
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
        $response['tokenDetails'] = ZohoBiginHandler::_refreshAccessToken($queryParams);
      }
      $fieldsMetaApiEndpoint = "https://www.zohoapis.{$queryParams->dataCenter}/bigin/v1/settings/fields";
      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $requiredParams['module'] = $queryParams->module;
      $fieldsMetaResponse = HttpHelper::get($fieldsMetaApiEndpoint, $requiredParams, $authorizationHeader);

      if (!is_wp_error($fieldsMetaResponse) && (empty($fieldsMetaResponse->status) || (!empty($fieldsMetaResponse->status) && 'error' !== $fieldsMetaResponse->status))) {
        $retriveFieldsData = $fieldsMetaResponse->fields;
        $fields = [];
        $fileUploadFields = [];
        $requiredFields = [];
        $requiredFileUploadFiles = [];
        foreach ($retriveFieldsData as $field) {
          $fields[$field->api_name] = (object) [
            'api_name'      => $field->api_name,
            'display_label' => $field->display_label,
            'data_type'     => $field->data_type,
            'length'        => $field->length,
            'required'      => $field->system_mandatory
          ];
          if ($field->system_mandatory) {
            $requiredFields[] = $field->api_name;
          }
        }

        $fields['Pipeline'] = (object) [
          'api_name'      => 'Pipeline',
          'display_label' => 'Pipeline',
          'data_type'     => 'text',
          'length'        => 120,
          'required'      => true
        ];
        $requiredFields[] = 'Pipeline';

        uksort($fields, 'strnatcasecmp');
        uksort($fileUploadFields, 'strnatcasecmp');
        usort($requiredFields, 'strnatcasecmp');
        usort($requiredFileUploadFiles, 'strnatcasecmp');

        $fieldDetails = (object) [
          'fields'                   => $fields,
          'fileUploadFields'         => $fileUploadFields,
          'required'                 => $requiredFields,
          'requiredFileUploadFields' => $requiredFileUploadFiles
        ];
        $response['fieldDetails'] = $fieldDetails;
      } else {
        wp_send_json_error(
          'error' === $fieldsMetaResponse->status ? $fieldsMetaResponse->message : 'Unknown',
          400
        );
      }
      if (!empty($response['tokenDetails']) && $response['tokenDetails'] && !empty($queryParams->id)) {
        $response['queryModule'] = $queryParams->module;
        ZohoBiginHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response);
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

  public function getTagList()
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
        || empty($queryParams->module)
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
        $response['tokenDetails'] = ZohoBiginHandler::_refreshAccessToken($queryParams);
      }

      $tagsMetaApiEndpoint = "http://www.zohoapis.{$queryParams->dataCenter}/bigin/v1/settings/tags?module={$queryParams->module}";
      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $tagsMetaResponse = HttpHelper::get($tagsMetaApiEndpoint, null, $authorizationHeader);

      if (!is_wp_error($tagsMetaResponse)) {
        $tags = $tagsMetaResponse->tags;

        if (count($tags) > 0) {
          $allTags = [];
          foreach ($tags as $tag) {
            $allTags[$tag->name] = (object) [
              'tagId'   => $tag->id,
              'tagName' => $tag->name
            ];
          }
          uksort($allTags, 'strnatcasecmp');
          $response['tags'] = $allTags;
        }
      } else {
        wp_send_json_error(
          empty($tagsMetaResponse->data) ? 'Unknown' : $tagsMetaResponse->error,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoBiginHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['lists']);
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

  public function getUsers()
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
        $response['tokenDetails'] = ZohoBiginHandler::_refreshAccessToken($queryParams);
      }

      $usersMetaApiEndpoint = "https://www.zohoapis.{$queryParams->dataCenter}/bigin/v1/users";
      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $usersMetaResponse = HttpHelper::get($usersMetaApiEndpoint, null, $authorizationHeader);

      // wp_send_json_success($usersMetaResponse, 200);

      if (!is_wp_error($usersMetaResponse)) {
        $users = $usersMetaResponse->users;

        if (count($users) > 0) {
          $allUsers = [];
          foreach ($users as $user) {
            $allUsers[$user->full_name] = (object) [
              'userId'   => $user->id,
              'userName' => $user->full_name
            ];
          }
          uksort($allUsers, 'strnatcasecmp');
          $response['users'] = $allUsers;
        }
      } else {
        wp_send_json_error(
          empty($usersMetaResponse->data) ? 'Unknown' : $usersMetaResponse->error,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoBiginHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['lists']);
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
   * Helps to refresh zoho bigin access_token
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
   * @param integer $fromID        ID of Integration related form
   * @param integer $integrationID ID of Zoho bigin Integration
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
    $zbiginDetails = $integrationHandler->getAIntegration($integrationID);

    if (is_wp_error($zbiginDetails)) {
      return;
    }
    $newDetails = json_decode($zbiginDetails[0]->integration_details);

    $newDetails->tokenDetails = $tokenDetails;
    if (!empty($others['modules'])) {
      $newDetails->default->modules = $others['modules'];
    }
    if (!empty($others['related_modules'])) {
      $newDetails->default->relatedlist['modules'] = $others['related_modules'];
    }

    $integrationHandler->updateIntegration($integrationID, $zbiginDetails[0]->integration_name, 'Zoho Bigin', wp_json_encode($newDetails), 'form');
  }

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = is_string($integrationData->integration_details) ? json_decode($integrationData->integration_details) : $integrationData->integration_details;
    $integID = $integrationData->id;
    $tokenDetails = $integrationDetails->tokenDetails;
    $module = $integrationDetails->module;
    $fieldMap = $integrationDetails->field_map;
    $actions = $integrationDetails->actions;
    $defaultDataConf = $integrationDetails->default;
    if (
      empty($tokenDetails)
      || empty($module)
      || empty($fieldMap)
    ) {
      $error = new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for zoho bigin api', 'bit-form'));
      $this->_logResponse->apiResponse($logID, $this->_integrationID, 'record', 'validation', $error);
      return $error;
    }
    $requiredParams = null;
    if ((intval($tokenDetails->generates_on) + (55 * 60)) < time()) {
      $requiredParams['clientId'] = $integrationDetails->clientId;
      $requiredParams['clientSecret'] = $integrationDetails->clientSecret;
      $requiredParams['dataCenter'] = $integrationDetails->dataCenter;
      $requiredParams['tokenDetails'] = $tokenDetails;
      $newTokenDetails = ZohoBiginHandler::_refreshAccessToken((object)$requiredParams);
      if ($newTokenDetails) {
        ZohoBiginHandler::_saveRefreshedToken($this->_formID, $this->_integrationID, $newTokenDetails);
        $tokenDetails = $newTokenDetails;
      }
    }

    $required = !empty($defaultDataConf->moduleData->{$module}->required) ?
        $defaultDataConf->moduleData->{$module}->required : [];

    $actions = $integrationDetails->actions;
    $fileMap = $integrationDetails->upload_field_map;
    $recordApiHelper = new RecordApiHelper($tokenDetails, $integID, $logID);
    $zBiginApiResponse = $recordApiHelper->executeRecordApi(
      $this->_formID,
      $entryID,
      $defaultDataConf,
      $module,
      $fieldValues,
      $fieldMap,
      $actions,
      $required,
      $fileMap
    );
    if (is_wp_error($zBiginApiResponse)) {
      return $zBiginApiResponse;
    }

    if (
      count($integrationDetails->relatedlists)
      && !empty($zBiginApiResponse->response->result->row->success->details->FL[0])
      && 'Id' === $zBiginApiResponse->response->result->row->success->details->FL[0]->val
    ) {
      foreach ($integrationDetails->relatedlists as $relatedlist) {
        if (!empty($relatedlist->module)) {
          $recordID = $zBiginApiResponse->response->result->row->success->details->FL[0]->content;
          $relatedListModule = $relatedlist->module;
          $defaultDataConf->moduleData->{$relatedListModule}->fields->{'SEMODULE'} = (object) [
            'length'    => \strlen($relatedListModule),
            'required'  => true,
            'data_type' => 'string',
          ];
          $fieldValues['SEMODULE'] = $relatedListModule;
          $relatedlist->field_map[] = (object)
          [
            'formField'     => 'SEMODULE',
            'zohoFormField' => 'SEMODULE'
          ];

          $defaultDataConf->moduleData->{$relatedListModule}->fields->{'SEID'} = (object) [
            'length'    => \strlen($recordID),
            'required'  => true,
            'data_type' => 'string',
          ];
          $fieldValues['SEID'] = $recordID;
          $relatedlist->field_map[] = (object)
          [
            'formField'     => 'SEID',
            'zohoFormField' => 'SEID'
          ];

          $zBiginRelatedRecResp = $recordApiHelper->executeRecordApi(
            $this->_formID,
            $entryID,
            $defaultDataConf,
            $relatedListModule,
            $fieldValues,
            $relatedlist->field_map,
            $relatedlist->actions,
            !empty($defaultDataConf->moduleData->{$relatedListModule}->required) ?
                $defaultDataConf->moduleData->{$relatedListModule}->required : [],
            $relatedlist->upload_field_map
          );
        }
      }
    }
    return $zBiginApiResponse;
  }
}
