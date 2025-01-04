<?php

/**
 * Active Campaign Integration
 *
 */

namespace BitCode\BitForm\Core\Integration\ActiveCampaign;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\HttpHelper;
use WP_Error;

/**
 * Provide functionality for ZohoCrm integration
 */
class ActiveCampaignHandler
{
  private $_formID;
  private $_integrationID;

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
    add_action('wp_ajax_bitforms_aCampaign_authorize', [__CLASS__, 'activeCampaignAuthorize']);
    add_action('wp_ajax_bitforms_aCampaign_headers', [__CLASS__, 'activeCampaignHeaders']);
    add_action('wp_ajax_bitforms_aCampaign_lists', [__CLASS__, 'activeCampaignLists']);
    add_action('wp_ajax_bitforms_aCampaign_tags', [__CLASS__, 'activeCampaignTags']);
  }

  public static function _apiEndpoint($api_url, $method)
  {
    return "{$api_url}/api/3/{$method}/";
  }

  /**
   * Process ajax request
   *
   * @return string Active Campaign api response and status
   */
  public static function activeCampaignAuthorize()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $requestsParams = json_decode($inputJSON);
      if (
        empty($requestsParams->api_key)
        || empty($requestsParams->api_url)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }

      $apiEndpoint = self::_apiEndpoint($requestsParams->api_url, 'accounts');
      $authorizationHeader = [];
      $authorizationHeader['Api-Token'] = $requestsParams->api_key;
      $apiResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);

      if (is_wp_error($apiResponse) || empty($apiResponse)) {
        wp_send_json_error(
          empty($apiResponse) ? 'Unknown' : $apiResponse,
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
   * Process ajax request for refresh lists
   *
   * @return JSON active campaign list data
   */
  public static function activeCampaignLists()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);

      if (
        empty($queryParams->api_key)
        || empty($queryParams->api_url)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }

      $apiEndpoint = self::_apiEndpoint($queryParams->api_url, 'lists');
      $authorizationHeader = [];
      $authorizationHeader['Api-Token'] = $queryParams->api_key;
      $aCampaignResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);

      $lists = [];
      if (!is_wp_error($aCampaignResponse)) {
        $allLists = $aCampaignResponse->lists;

        foreach ($allLists as $list) {
          $lists[$list->name] = (object) [
            'listId'   => $list->id,
            'listName' => $list->name,
          ];
        }
        $response = [];
        $response['activeCampaignLists'] = $lists;
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

  /**
   * Process ajax request for refresh Tags
   *
   * @return JSON active campaign tags data
   */
  public static function activeCampaignTags()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);

      if (
        empty($queryParams->api_key)
        || empty($queryParams->api_url)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }

      $apiEndpoint = self::_apiEndpoint($queryParams->api_url, 'tags?limit=500');
      $authorizationHeader = [];
      $authorizationHeader['Api-Token'] = $queryParams->api_key;
      $aCampaignResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);

      $tags = [];
      if (!is_wp_error($aCampaignResponse)) {
        $allTags = $aCampaignResponse->tags;

        foreach ($allTags as $tag) {
          $tags[$tag->tag] = (object) [
            'tagId'   => $tag->id,
            'tagName' => $tag->tag,
          ];
        }
        $response = [];
        $response['activeCampaignTags'] = $tags;
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

  /**
   * Process ajax request for refresh crm modules
   *
   * @return JSON crm module data
   */
  public static function activeCampaignHeaders()
  {
    $authorizationHeader = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);

      if (
        empty($queryParams->api_key)
        || empty($queryParams->api_url)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }

      // $apiEndpoint = "{$queryParams->api_url}/api/3/fields";
      $apiEndpoint = self::_apiEndpoint($queryParams->api_url, 'fields');
      $$authorizationHeader = [];
      $authorizationHeader['Api-Token'] = $queryParams->api_key;
      $aCampaignResponse = HttpHelper::get($apiEndpoint, null, $authorizationHeader);

      $fields = [];
      if (!is_wp_error($aCampaignResponse)) {
        $allFields = $aCampaignResponse->fields;
        foreach ($allFields as $field) {
          $fields[$field->title] = (object) [
            'fieldId'   => $field->id,
            'fieldName' => $field->title,
            'required'  => '0' === $field->isrequired ? false : true
          ];
        }
        $fields['FirstName'] = (object) ['fieldId' => 'firstName', 'fieldName' => 'First Name', 'required' => false];
        $fields['LastName'] = (object) ['fieldId' => 'lastName', 'fieldName' => 'Last Name', 'required' => false];
        $fields['Email'] = (object) ['fieldId' => 'email', 'fieldName' => 'Email', 'required' => true];
        $fields['Phone'] = (object) ['fieldId' => 'phone', 'fieldName' => 'Phone', 'required' => false];
        $response = [];
        $response['activeCampaignField'] = $fields;
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
    $api_url = $integrationDetails->api_url;
    $fieldMap = $integrationDetails->field_map;
    $actions = $integrationDetails->actions;
    $listId = $integrationDetails->listId;
    $tags = $integrationDetails->tagIds;

    if (
      empty($api_key)
      || empty($api_url)
      || empty($fieldMap)
    ) {
      return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Sendinblue api', 'bit-form'));
    }
    $recordApiHelper = new RecordApiHelper($api_key, $api_url, $this->_integrationID, $logID, $entryID);
    $activeCampaignApiResponse = $recordApiHelper->executeRecordApi(
      $fieldValues,
      $fieldMap,
      $actions,
      $listId,
      $tags
    );

    if (is_wp_error($activeCampaignApiResponse)) {
      return $activeCampaignApiResponse;
    }
    return $activeCampaignApiResponse;
  }
}
