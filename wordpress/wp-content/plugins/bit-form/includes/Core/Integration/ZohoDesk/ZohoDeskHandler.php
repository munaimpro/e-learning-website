<?php

/**
 * ZohoDesk Integration
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoDesk;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\HttpHelper;
use BitCode\BitForm\Core\Util\IpTool;

/**
 * Provide functionality for ZohoCrm integration
 */
class ZohoDeskHandler
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
    add_action('wp_ajax_bitforms_zdesk_generate_token', [__CLASS__, 'generateTokens']);
    add_action('wp_ajax_bitforms_zdesk_refresh_organizations', [__CLASS__, 'refreshOrganizationsAjaxHelper']);
    add_action('wp_ajax_bitforms_zdesk_refresh_departments', [__CLASS__, 'refreshDepartmentsAjaxHelper']);
    add_action('wp_ajax_bitforms_zdesk_refresh_fields', [__CLASS__, 'refreshFieldsAjaxHelper']);
    add_action('wp_ajax_bitforms_zdesk_refresh_owners', [__CLASS__, 'refreshTicketOwnersAjaxHelper']);
    add_action('wp_ajax_bitforms_zdesk_refresh_products', [__CLASS__, 'refreshProductsAjaxHelper']);
  }

  /**
   * Process ajax request for generate_token
   *
   * @return string | boolean zoho crm api response and status
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

  public static function refreshOrganizationsAjaxHelper()
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
        $response['tokenDetails'] = ZohoDeskHandler::_refreshAccessToken($queryParams);
      }

      $organizationsMetaApiEndpoint = "https://desk.zoho.{$queryParams->dataCenter}/api/v1/organizations";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $organizationsMetaResponse = HttpHelper::get($organizationsMetaApiEndpoint, null, $authorizationHeader);

      if (!is_wp_error($organizationsMetaResponse)) {
        $allOrganizations = [];
        $organizations = $organizationsMetaResponse->data;

        if (count($organizations) > 0) {
          foreach ($organizations as $organization) {
            $allOrganizations[$organization->companyName] = (object) [
              'orgId'      => $organization->id,
              'portalName' => $organization->companyName
            ];
          }
        }
        uksort($allOrganizations, 'strnatcasecmp');
        $response['organizations'] = $allOrganizations;
      } else {
        wp_send_json_error(
          empty($organizationsMetaResponse->data) ? 'Unknown' : $organizationsMetaResponse->error,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoDeskHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['lists']);
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
  public static function refreshDepartmentsAjaxHelper()
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
        || empty($queryParams->orgId)
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
        $response['tokenDetails'] = ZohoDeskHandler::_refreshAccessToken($queryParams);
      }

      $departmentsMetaApiEndpoint = "https://desk.zoho.{$queryParams->dataCenter}/api/v1/departments?isEnabled=true&limit=100";

      $authorizationHeader['orgId'] = "{$queryParams->orgId}";
      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $departmentsMetaResponse = HttpHelper::get($departmentsMetaApiEndpoint, null, $authorizationHeader);

      if (!is_wp_error($departmentsMetaResponse)) {
        $allDepartments = [];
        $departments = $departmentsMetaResponse->data;

        if (count($departments) > 0) {
          foreach ($departments as $department) {
            $allDepartments[$department->name] = (object) [
              'departmentId'   => $department->id,
              'departmentName' => $department->name
            ];
          }
        }
        uksort($allDepartments, 'strnatcasecmp');
        $response['departments'] = $allDepartments;
      } else {
        wp_send_json_error(
          empty($departmentsMetaResponse->data) ? 'Unknown' : $departmentsMetaResponse->error,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoDeskHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['lists']);
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
  public static function refreshFieldsAjaxHelper()
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
        || empty($queryParams->orgId)
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
        $response['tokenDetails'] = ZohoDeskHandler::_refreshAccessToken($queryParams);
      }

      $fieldsMetaApiEndpoint = "https://desk.zoho.{$queryParams->dataCenter}/api/v1/organizationFields?module=tickets";

      $authorizationHeader['orgId'] = "{$queryParams->orgId}";
      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $fieldsMetaResponse = HttpHelper::get($fieldsMetaApiEndpoint, null, $authorizationHeader);

      if (!is_wp_error($fieldsMetaResponse)) {
        $fields = $fieldsMetaResponse->data;

        if (count($fields) > 0) {
          $response['fields']['Contact Name - Last Name'] = (object) [
            'apiName'       => 'lastName',
            'displayLabel'  => 'Contact Name - Last Name',
            'isCustomField' => false,
            'required'      => true
          ];
          $response['fields']['Contact Name - First Name'] = (object) [
            'apiName'       => 'firstName',
            'displayLabel'  => 'Contact Name - First Name',
            'isCustomField' => false,
            'required'      => false
          ];
          $response['required'][] = 'lastName';
          foreach ($fields as $field) {
            if ('contactId' === $field->apiName || 'assigneeId' === $field->apiName) {
              continue;
            }
            $response['fields'][$field->displayLabel] = (object) [
              'apiName'       => $field->apiName,
              'displayLabel'  => $field->displayLabel,
              'isCustomField' => $field->isCustomField,
              'required'      => $field->isMandatory
            ];

            if ($field->isMandatory) {
              $response['required'][] = $field->apiName;
            }
          }
        }
        uksort($response['fields'], 'strnatcasecmp');
        usort($response['required'], 'strnatcasecmp');
      } else {
        wp_send_json_error(
          'error' === $fieldsMetaResponse->status ? $fieldsMetaResponse->message : 'Unknown',
          400
        );
      }
      if (!empty($response['tokenDetails']) && $response['tokenDetails'] && !empty($queryParams->id)) {
        $response['queryModule'] = $queryParams->module;
        ZohoDeskHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response);
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
  public static function refreshTicketOwnersAjaxHelper()
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
        || empty($queryParams->orgId)
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
        $response['tokenDetails'] = ZohoDeskHandler::_refreshAccessToken($queryParams);
      }

      $ownersMetaApiEndpoint = "https://desk.zoho.{$queryParams->dataCenter}/api/v1/agents?status=ACTIVE&limit=100";

      $authorizationHeader['orgId'] = "{$queryParams->orgId}";
      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $ownersMetaResponse = HttpHelper::get($ownersMetaApiEndpoint, null, $authorizationHeader);

      if (!is_wp_error($ownersMetaResponse)) {
        $owners = $ownersMetaResponse->data;

        if (count($owners) > 0) {
          foreach ($owners as $owner) {
            $response['owners'][] = (object) [
              'ownerId'   => $owner->id,
              'ownerName' => $owner->name
            ];
          }
        }
      } else {
        wp_send_json_error(
          empty($ownersMetaResponse->data) ? 'Unknown' : $ownersMetaResponse->error,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoDeskHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['lists']);
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
  public static function refreshProductsAjaxHelper()
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
        || empty($queryParams->orgId)
        || empty($queryParams->departmentId)
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
        $response['tokenDetails'] = ZohoDeskHandler::_refreshAccessToken($queryParams);
      }

      $productsMetaApiEndpoint = "https://desk.zoho.{$queryParams->dataCenter}/api/v1/products?departmentId={$queryParams->departmentId}&limit=100";

      $authorizationHeader['orgId'] = "{$queryParams->orgId}";
      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $productsMetaResponse = HttpHelper::get($productsMetaApiEndpoint, null, $authorizationHeader);

      if (!is_wp_error($productsMetaResponse)) {
        $products = $productsMetaResponse->data;

        if (count($products) > 0) {
          foreach ($products as $product) {
            $response['products'][] = (object) [
              'productId'   => $product->id,
              'productName' => $product->productName
            ];
          }
        }
      } else {
        wp_send_json_error(
          empty($productsMetaResponse->data) ? 'Unknown' : $productsMetaResponse->error,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoDeskHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['lists']);
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
   * @return string  $tokenDetails API token details
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
    $zdeskDetails = $integrationHandler->getAIntegration($integrationID);

    if (is_wp_error($zdeskDetails)) {
      return;
    }
    $newDetails = json_decode($zdeskDetails[0]->integration_details);

    $newDetails->tokenDetails = $tokenDetails;
    if (!empty($others['organizations'])) {
      $newDetails->default->organizations = $others['organizations'];
    }

    $integrationHandler->updateIntegration($integrationID, $zdeskDetails[0]->integration_name, 'Zoho Desk', wp_json_encode($newDetails), 'form');
  }

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = is_string($integrationData->integration_details) ? json_decode($integrationData->integration_details) : $integrationData->integration_details;

    $tokenDetails = $integrationDetails->tokenDetails;
    $orgId = $integrationDetails->orgId;
    $department = $integrationDetails->department;
    $dataCenter = $integrationDetails->dataCenter;
    $fieldMap = $integrationDetails->field_map;
    $required = $integrationDetails->default->fields->{$orgId}->required;
    $actions = $integrationDetails->actions;
    if (
      empty($tokenDetails)
      || empty($orgId)
      || empty($department)
      || empty($fieldMap)
    ) {
      return new WP_Error('REQ_FIELD_EMPTY', __('list are required for zoho desk api', 'bit-form'));
    }

    $requiredParams = null;
    if ((intval($tokenDetails->generates_on) + (55 * 60)) < time()) {
      $requiredParams['clientId'] = $integrationDetails->clientId;
      $requiredParams['clientSecret'] = $integrationDetails->clientSecret;
      $requiredParams['dataCenter'] = $integrationDetails->dataCenter;
      $requiredParams['tokenDetails'] = $tokenDetails;
      $newTokenDetails = ZohoDeskHandler::_refreshAccessToken((object)$requiredParams);
      if ($newTokenDetails) {
        ZohoDeskHandler::_saveRefreshedToken($this->_formID, $this->_integrationID, $newTokenDetails);
        $tokenDetails = $newTokenDetails;
      }
    }

    // $actions = $integrationDetails->actions;
    $recordApiHelper = new RecordApiHelper($tokenDetails, $orgId, $this->_integrationID, $logID);

    $zdeskApiResponse = $recordApiHelper->executeRecordApi(
      $department,
      $dataCenter,
      $fieldValues,
      $fieldMap,
      $required,
      $actions,
      $entryID,
      $this->_formID
    );

    if (is_wp_error($zdeskApiResponse)) {
      return $zdeskApiResponse;
    }
    return $zdeskApiResponse;
  }
}
