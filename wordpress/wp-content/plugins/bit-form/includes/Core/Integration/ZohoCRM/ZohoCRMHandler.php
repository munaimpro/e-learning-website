<?php

/**
 * ZohoCrm Integration
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoCRM;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\HttpHelper;
use BitCode\BitForm\Core\Util\IpTool;
use WP_Error;

/**
 * Provide functionality for ZohoCrm integration
 */
class ZohoCRMHandler
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
    add_action('wp_ajax_bitforms_zcrm_generate_token', [__CLASS__, 'generateTokens']);
    add_action('wp_ajax_bitforms_zcrm_refresh_modules', [__CLASS__, 'refreshModulesAjaxHelper']);
    add_action('wp_ajax_bitforms_zcrm_refresh_layouts', [__CLASS__, 'refreshLayoutsAjaxHelper']);
    add_action('wp_ajax_bitforms_zcrm_get_users', [__CLASS__, 'refreshUsersAjaxHelper']);
    add_action('wp_ajax_bitforms_zcrm_get_tags', [__CLASS__, 'refreshTagListAjaxHelper']);
    add_action('wp_ajax_bitforms_zcrm_get_assignment_rules', [__CLASS__, 'getAssignmentRulesAjaxHelper']);
    add_action('wp_ajax_nopriv_bitforms_zcrm_get_assignment_rules', [__CLASS__, 'getAssignmentRulesAjaxHelper']);
    add_action('wp_ajax_bitforms_zcrm_get_related_lists', [__CLASS__, 'getRelatedListsAjaxHelper']);
  }

  /**
   * Process ajax request for generate_token
   *
   * @return JSON zoho crm api response and status
   */
  public static function generateTokens()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
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
   * Process ajax request for refresh crm modules
   *
   * @return JSON crm module data
   */
  public static function refreshModulesAjaxHelper()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
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
        $response['tokenDetails'] = ZohoCRMHandler::_refreshAccessToken($queryParams);
      }
      $zohosIntegratedModules = [
        'zohosign__ZohoSign_Document_Events',
        'zohoshowtime__ShowTime_Sessions',
        'zohoshowtime__Zoho_ShowTime',
        'zohosign__ZohoSign_Documents',
        'zohosign__ZohoSign_Recipients'
      ];
      $modulesMetaApiEndpoint = "{$queryParams->tokenDetails->api_domain}/crm/v2/settings/modules";
      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $modulesMetaResponse = HttpHelper::get($modulesMetaApiEndpoint, null, $authorizationHeader);
      if (!is_wp_error($modulesMetaResponse) && (empty($modulesMetaResponse->status) || (!empty($modulesMetaResponse->status) && 'error' !== $modulesMetaResponse->status))) {
        $retriveModuleData = $modulesMetaResponse->modules;

        $allModules = [];
        foreach ($retriveModuleData as $key => $value) {
          if ((!empty($value->inventory_template_supported) && $value->inventory_template_supported) || \in_array($value->api_name, $zohosIntegratedModules)) {
            continue;
          }
          if (!empty($value->api_supported) && $value->api_supported && !empty($value->editable) && $value->editable) {
            $allModules[$value->api_name] = (object) [
              'plural_label'       => $value->plural_label,
              'triggers_supported' => $value->triggers_supported,
              'quick_create'       => $value->quick_create,
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
        ZohoCRMHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['modules']);
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
  public static function refreshLayoutsAjaxHelper()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
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
        $response['tokenDetails'] = ZohoCRMHandler::_refreshAccessToken($queryParams);
      }
      $layoutsMetaApiEndpoint = "{$queryParams->tokenDetails->api_domain}/crm/v2/settings/layouts";
      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $requiredParams['module'] = $queryParams->module;
      $layoutsMetaResponse = HttpHelper::get($layoutsMetaApiEndpoint, $requiredParams, $authorizationHeader);
      if (!is_wp_error($layoutsMetaResponse) && (empty($layoutsMetaResponse->status) || (!empty($layoutsMetaResponse->status) && 'error' !== $layoutsMetaResponse->status))) {
        $retriveLayoutsData = $layoutsMetaResponse->layouts;
        $layouts = [];
        foreach ($retriveLayoutsData as $layoutKey => $layoutValue) {
          $fields = [];
          $fileUploadFields = [];
          $requiredFields = [];
          $requiredFileUploadFiles = [];
          $uniqueFields = [];
          foreach ($layoutValue->sections as $sectionKey => $sectionValue) {
            foreach ($sectionValue->fields as $fieldKey => $fieldDetails) {
              if (empty($fieldDetails->subform) && !empty($fieldDetails->api_name) && !empty($fieldDetails->view_type->create) && $fieldDetails->view_type->create && 'ownerlookup' !== $fieldDetails->data_type) {
                if ('fileupload' === $fieldDetails->data_type) {
                  $fileUploadFields[$fieldDetails->api_name] = (object) [
                    'display_label' => $fieldDetails->display_label,
                    'length'        => $fieldDetails->length,
                    'visible'       => $fieldDetails->visible,
                    'json_type'     => $fieldDetails->json_type,
                    'data_type'     => $fieldDetails->data_type,
                    'required'      => $fieldDetails->required
                  ];
                } else {
                  $fields[$fieldDetails->api_name] = (object) [
                    'display_label' => $fieldDetails->display_label,
                    'length'        => $fieldDetails->length,
                    'visible'       => $fieldDetails->visible,
                    'json_type'     => $fieldDetails->json_type,
                    'data_type'     => $fieldDetails->data_type,
                    'required'      => $fieldDetails->required
                  ];
                }

                if (!empty($fieldDetails->required) && $fieldDetails->required) {
                  if ('fileupload' === $fieldDetails->data_type) {
                    $requiredFileUploadFiles[] = $fieldDetails->api_name;
                  } elseif ('Parent_Id' !== $fieldDetails->api_name) {
                    $requiredFields[] = $fieldDetails->api_name;
                  }
                }
                if (!empty($fieldDetails->unique) && count((array)$fieldDetails->unique)) {
                  $uniqueFields[] = $fieldDetails->api_name;
                }
              }
            }
          }
          uksort($fields, 'strnatcasecmp');
          uksort($fileUploadFields, 'strnatcasecmp');
          usort($requiredFields, 'strnatcasecmp');
          usort($requiredFileUploadFiles, 'strnatcasecmp');

          $layouts[$layoutValue->name] = (object) [
            'visible'                  => $layoutValue->visible,
            'fields'                   => $fields,
            'required'                 => $requiredFields,
            'unique'                   => $uniqueFields,
            'id'                       => $layoutValue->id,
            'fileUploadFields'         => $fileUploadFields,
            'requiredFileUploadFields' => $requiredFileUploadFiles
          ];
        }
        uksort($layouts, 'strnatcasecmp');
        $response['layouts'] = $layouts;
      } else {
        wp_send_json_error(
          'error' === $layoutsMetaResponse->status ? $layoutsMetaResponse->message : 'Unknown',
          400
        );
      }
      if (!empty($response['tokenDetails']) && $response['tokenDetails'] && !empty($queryParams->id)) {
        $response['queryModule'] = $queryParams->module;
        ZohoCRMHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response);
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
   * @return string json | boolean  $tokenDetails API token details
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
    $zcrmDetails = $integrationHandler->getAIntegration($integrationID);

    if (is_wp_error($zcrmDetails)) {
      return;
    }
    $newDetails = json_decode($zcrmDetails[0]->integration_details);

    $newDetails->tokenDetails = $tokenDetails;
    if (!empty($others['modules'])) {
      $newDetails->default->modules = $others['modules'];
    }
    if (!empty($others['layouts']) && !empty($others['queryModule'])) {
      $newDetails->default->layouts->{$others['queryModule']} = $others['layouts'];
    }

    $integrationHandler->updateIntegration($integrationID, $zcrmDetails[0]->integration_name, 'Zoho CRM', wp_json_encode($newDetails), 'form');
  }

  /**
   * Process ajax request for refresh crm users
   *
   * @return JSON crm users data
   */
  public static function refreshUsersAjaxHelper()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
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
            'bitformpro'
          ),
          400
        );
      }
      $response = [];
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = self::_refreshAccessToken($queryParams);
      }
      $usersApiEndpoint = "{$queryParams->tokenDetails->api_domain}/crm/v2/users?type=ActiveConfirmedUsers";
      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $retrivedUsersData = [];
      $usersResponse = (object) [];
      do {
        $requiredParams = [];
        if (!empty($usersResponse->users)) {
          if (!empty($retrivedUsersData)) {
            $retrivedUsersData = array_merge($retrivedUsersData, $usersResponse->users);
          } else {
            $retrivedUsersData = $usersResponse->users;
          }
        }
        if (!empty($usersResponse->info->more_records) && $usersResponse->info->more_records) {
          $requiredParams['page'] = intval($usersResponse->info->page) + 1;
        }
        $usersResponse = HttpHelper::get($usersApiEndpoint, $requiredParams, $authorizationHeader);
      } while (null === $usersResponse || (!empty($usersResponse->info->more_records) && $usersResponse->info->more_records));
      if (empty($requiredParams) && !is_wp_error($usersResponse)) {
        $retrivedUsersData = $usersResponse->users;
      }
      if (!is_wp_error($usersResponse) && !empty($retrivedUsersData)) {
        $users = [];
        foreach ($retrivedUsersData as $userKey => $userValue) {
          $users[$userValue->full_name] = (object) [
            'full_name' => $userValue->full_name,
            'id'        => $userValue->id,
          ];
        }
        uksort($users, 'strnatcasecmp');
        $response['users'] = $users;
      } else {
        wp_send_json_error(
          'error' === $usersResponse->status ? $usersResponse->message : 'Unknown',
          400
        );
      }
      if (!empty($response['tokenDetails']) && $response['tokenDetails'] && !empty($queryParams->id)) {
        self::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response);
      }
      wp_send_json_success($response, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bitformpro'
        ),
        401
      );
    }
  }

  /**
   * Process ajax request for refresh tags of a module
   *
   * @return JSON crm Tags  for a module
   */
  public static function refreshTagListAjaxHelper()
  {
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
            'bitformpro'
          ),
          400
        );
      }
      $response = [];
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = self::_refreshAccessToken($queryParams);
      }
      $tokenDetails = empty($response['tokenDetails']) ? $queryParams->tokenDetails : $response['tokenDetails'];
      $tagApiHelper = new TagApiHelper($tokenDetails, $queryParams->module);
      $tagListApiResponse = $tagApiHelper->getTagList();
      if (!is_wp_error($tagListApiResponse)) {
        usort($tagListApiResponse, 'strnatcasecmp');
        $response['tags'] = $tagListApiResponse;
      } else {
        wp_send_json_error(
          is_wp_error($tagListApiResponse) ? $tagListApiResponse->get_error_message() : (empty($tagListApiResponse) ? __('Tag is empty', 'bitformpro') : 'Unknown'),
          empty($tagListApiResponse) ? 204 : 400
        );
      }
      if (!empty($response['tokenDetails']) && $response['tokenDetails'] && !empty($queryParams->id)) {
        self::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response);
      }
      wp_send_json_success($response, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bitformpro'
        ),
        401
      );
    }
  }

  /**
   * Process ajax request to get assignment rules of a Zoho CRM module
   *
   * @return JSON crm assignment rules data
   */
  public static function getAssignmentRulesAjaxHelper()
  {
    if (true || isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
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
            'bitformpro'
          ),
          400
        );
      }
      $response = [];
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = self::_refreshAccessToken($queryParams);
      }
      $metaDataApiHelper = new MetaDataApiHelper($queryParams->tokenDetails, true);
      $assignmentRulesResponse = $metaDataApiHelper->getAssignmentRules($queryParams->module);
      if (
        !is_wp_error($assignmentRulesResponse)
        && !empty($assignmentRulesResponse)
        && empty($assignmentRulesResponse->status)
      ) {
        uksort($assignmentRulesResponse, 'strnatcasecmp');
        $response['assignmentRules'] = $assignmentRulesResponse;
      } else {
        wp_send_json_error(
          !empty($assignmentRulesResponse->status)
              && 'error' === $assignmentRulesResponse->status ?
              $assignmentRulesResponse->message : (empty($assignmentRulesResponse) ? __('Assignment rules is empty', 'bitformpro') : 'Unknown'),
          empty($assignmentRulesResponse) ? 204 : 400
        );
      }
      if (!empty($response['tokenDetails']) && $response['tokenDetails'] && !empty($queryParams->id)) {
        // $response["queryModule"] = $queryParams->module;
        self::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response);
      }
      wp_send_json_success($response, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bitformpro'
        ),
        401
      );
    }
  }

  /**
   * Process ajax request to get realted lists of a Zoho CRM module
   *
   * @return JSON crm layout data
   */
  public static function getRelatedListsAjaxHelper()
  {
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
            'bitformpro'
          ),
          400
        );
      }
      $response = [];
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = self::_refreshAccessToken($queryParams);
      }
      $metaDataApiHelper = new MetaDataApiHelper($queryParams->tokenDetails);
      $relatedListResponse = $metaDataApiHelper->getRelatedLists($queryParams->module);
      if (
        !is_wp_error($relatedListResponse)
        && !empty($relatedListResponse)
        && empty($relatedListResponse->status)
      ) {
        uksort($relatedListResponse, 'strnatcasecmp');
        $response['relatedLists'] = $relatedListResponse;
      } else {
        wp_send_json_error(
          !empty($relatedListResponse->status)
              && 'error' === $relatedListResponse->status ?
              $relatedListResponse->message : (empty($relatedListResponse) ? __('RelatedList is empty', 'bitformpro') : 'Unknown'),
          empty($relatedListResponse) ? 204 : 400
        );
      }
      if (!empty($response['tokenDetails']) && $response['tokenDetails'] && !empty($queryParams->id)) {
        // $response["queryModule"] = $queryParams->module;
        self::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response);
      }
      wp_send_json_success($response, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bitformpro'
        ),
        401
      );
    }
  }

  public static function addRelatedListV2($zcrmApiResponse, $formID, $entryID, $integID, $logID, $fieldValues, $integrationDetails, $recordApiHelper)
  {
    foreach ($integrationDetails->relatedlists as $relatedlist) {
      // Related List apis..
      $relatedListModule = !empty($relatedlist->module) ? $relatedlist->module : '';
      $relatedListLayout = !empty($relatedlist->layout) ? $relatedlist->layout : '';
      $defaultDataConf = $integrationDetails->default;
      if (empty($relatedListModule) || empty($relatedListLayout)) {
        return new WP_Error('REQ_FIELD_EMPTY', __('module, layout are required for zoho crm relatedlist', 'bitformpro'));
      }
      $module = $integrationDetails->module;
      $moduleSingular = \substr($module, 0, \strlen($module) - 1);
      if (isset($defaultDataConf->layouts->{$relatedListModule}->{$relatedListLayout}->fields->{$module})) {
        $moduleSingular = $module;
      } elseif (!isset($defaultDataConf->layouts->{$relatedListModule}->{$relatedListLayout}->fields->{$moduleSingular})) {
        $moduleSingular = '';
      }
      $relatedListRequired = !empty($defaultDataConf->layouts->{$relatedListModule}->{$relatedListLayout}->required) ?
          $defaultDataConf->layouts->{$relatedListModule}->{$relatedListLayout}->required : [];
      $recordID = $zcrmApiResponse->data[0]->details->id;
      $defaultDataConf->layouts->{$relatedListModule}->{$relatedListLayout}->fields->{'$se_module'} = (object) [
        'length'    => 200,
        'visible'   => true,
        'json_type' => 'string',
        'data_type' => 'string',
      ];
      $fieldValues['$se_module'] = $module;
      $relatedlist->field_map[] = (object)
      [
        'formField'     => '$se_module',
        'zohoFormField' => '$se_module'
      ];
      if (isset($defaultDataConf->layouts->{$relatedListModule}->{$relatedListLayout}->fields->Parent_Id)) {
        $fieldValues['Parent_Id'] = (object) ['id' => $recordID];
        $relatedlist->field_map[] = (object)
        [
          'formField'     => 'Parent_Id',
          'zohoFormField' => 'Parent_Id'
        ];
      } elseif (!empty($moduleSingular)) {
        $fieldValues[$moduleSingular] = ['id' => $recordID];
        $relatedlist->field_map[] = (object)
        [
          'formField'     => $moduleSingular,
          'zohoFormField' => $moduleSingular
        ];
      } elseif ('Contacts' === $module) {
        $fieldValues['Who_Id'] = (object) ['id' => $recordID];
        $relatedlist->field_map[] = (object)
        [
          'formField'     => 'Who_Id',
          'zohoFormField' => 'Who_Id'
        ];
      } else {
        $fieldValues['What_Id'] = (object) ['id' => $recordID];
        $relatedlist->field_map[] = (object)
        [
          'formField'     => 'What_Id',
          'zohoFormField' => 'What_Id'
        ];
      }

      $zcrmRelatedlistApiResponse = $recordApiHelper->executeRecordApi(
        $formID,
        $entryID,
        $integID,
        $logID,
        $defaultDataConf,
        $relatedListModule,
        $relatedListLayout,
        $fieldValues,
        $relatedlist->field_map,
        $relatedlist->actions,
        $relatedListRequired,
        $relatedlist->upload_field_map,
        true
      );
    }
  }

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = is_string($integrationData->integration_details) ? json_decode($integrationData->integration_details) : $integrationData->integration_details;

    $tokenDetails = $integrationDetails->tokenDetails;
    $module = $integrationDetails->module;
    $layout = $integrationDetails->layout;
    $fieldMap = $integrationDetails->field_map;
    $fileMap = $integrationDetails->upload_field_map;
    $actions = $integrationDetails->actions;
    $defaultDataConf = $integrationDetails->default;

    if (
      empty($tokenDetails)
      || empty($module)
      || empty($layout)
      || empty($fieldMap)
    ) {
      $error = new WP_Error('REQ_FIELD_EMPTY', __('module, layout, fields are required for zoho crm api', 'bit-form'));
      $this->_logResponse->apiResponse($logID, $this->_integrationID, 'record', 'validation', $error);
      return $error;
    }
    if (empty($defaultDataConf->layouts->{$module}->{$layout}->fields) || empty($defaultDataConf->modules->{$module})) {
      $error = new WP_Error('REQ_FIELD_EMPTY', __('module, layout, fields are required for zoho crm api', 'bit-form'));
      $this->_logResponse->apiResponse($logID, $this->_integrationID, 'record', 'validation', $error);
      return $error;
    }
    if ((intval($tokenDetails->generates_on) + (55 * 60)) < time()) {
      $requiredParams['clientId'] = $integrationDetails->clientId;
      $requiredParams['clientSecret'] = $integrationDetails->clientSecret;
      $requiredParams['dataCenter'] = $integrationDetails->dataCenter;
      $requiredParams['tokenDetails'] = $tokenDetails;
      $newTokenDetails = ZohoCRMHandler::_refreshAccessToken((object)$requiredParams);
      if ($newTokenDetails) {
        ZohoCRMHandler::_saveRefreshedToken($this->_formID, $this->_integrationID, $newTokenDetails);
        $tokenDetails = $newTokenDetails;
      }
    }

    $required = !empty($defaultDataConf->layouts->{$module}->{$layout}->required) ?
        $defaultDataConf->layouts->{$module}->{$layout}->required : [];
    $actions = $integrationDetails->actions;
    $recordApiHelper = new RecordApiHelper($tokenDetails);
    $zcrmApiResponse = $recordApiHelper->executeRecordApi(
      $this->_formID,
      $entryID,
      $this->_integrationID,
      $logID,
      $defaultDataConf,
      $module,
      $layout,
      $fieldValues,
      $fieldMap,
      $actions,
      $required,
      $fileMap
    );
    if (is_wp_error($zcrmApiResponse)) {
      return $zcrmApiResponse;
    }
    if (
      !empty($zcrmApiResponse->data)
      && !empty($zcrmApiResponse->data[0]->code)
      && 'SUCCESS' === $zcrmApiResponse->data[0]->code
      && count($integrationDetails->relatedlists)
    ) {
      $zcrmApiResponse = self::addRelatedListV2($zcrmApiResponse, $this->_formID, $entryID, $this->_integrationID, $logID, $fieldValues, $integrationDetails, $recordApiHelper);
    }
    return $zcrmApiResponse;
  }
}
