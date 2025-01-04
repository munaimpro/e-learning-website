<?php

/**
 * Autonami Integration
 *
 */

namespace BitCode\BitForm\Core\Integration\Autonami;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BWFCRM_Fields;
use BWFCRM_Lists;
use BWFCRM_Tag;
use WP_Error;

class AutonamiHandler
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
    add_action('wp_ajax_bitforms_autonami_authorize', [__CLASS__, 'autonamiAuthorize']);
    add_action('wp_ajax_bitforms_autonami_lists_and_tags', [__CLASS__, 'autonamiListsAndTags']);
    add_action('wp_ajax_bitforms_autonami_fields', [__CLASS__, 'autonamiFields']);
  }

  /**
   * for chen autonami pro plugins are exists
   */
  public static function checkedExistsAutonami()
  {
    if (!class_exists('BWFCRM_Contact')) {
      wp_send_json_error(__('Autonami Pro Plugins not found', 'bit-form'), 400);
    } else {
      return true;
    }
  }

  /**
   * @return  Autonami lists
   */
  public static function autonamiListsAndTags()
  {
    if (!isset($_REQUEST['_ajax_nonce']) && !wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      wp_send_json_error(__('Token expired', 'bit-form'), 401);
    }

    self::checkedExistsAutonami();

    $lists = BWFCRM_Lists::get_lists();
    $autonamiList = [];
    foreach ($lists as $list) {
      $autonamiList[$list['name']] = (object) [
        'id'    => $list['ID'],
        'title' => $list['name']
      ];
    }

    $tags = BWFCRM_Tag::get_tags();
    $autonamiTags = [];
    foreach ($tags as $tag) {
      $autonamiTags[$tag['name']] = (object) [
        'id'    => $tag['ID'],
        'title' => $tag['name']
      ];
    }

    $response['autonamiList'] = $autonamiList;
    $response['autonamiTags'] = $autonamiTags;
    wp_send_json_success($response, 200);
  }

  public static function autonamiFields()
  {
    if (!isset($_REQUEST['_ajax_nonce']) && !wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      wp_send_json_error(__('Token expired', 'bit-form'), 401);
    }

    self::checkedExistsAutonami();

    $fieldOptions = [];
    $fieldOptions['Email'] = (object) [
      'key'      => 'email',
      'label'    => 'Email',
      'type'     => 'primary',
      'required' => true
    ];
    foreach (BWFCRM_Fields::get_default_fields() as $key => $column) {
      $fieldOptions[$column] = (object) [
        'key'   => $key,
        'label' => $column,
        'type'  => 'primary'
      ];
    }
    foreach (BWFCRM_Fields::get_custom_fields(1, 1) as $field) {
      $fieldOptions[$field['slug']] = (object) [
        'key'   => $field['slug'],
        'label' => $field['name'],
        'type'  => 'custom'
      ];
    }
    $response['autonamiFields'] = $fieldOptions;
    wp_send_json_success($response, 200);
  }

  /**
   * @return True Autonami are exists
   */
  public static function autonamiAuthorize()
  {
    if (!isset($_REQUEST['_ajax_nonce']) && !wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      wp_send_json_error(__('Token expired', 'bit-form'), 401);
    }

    if (self::checkedExistsAutonami()) {
      wp_send_json_success(true);
    } else {
      wp_send_json_error(__('Autonami Pro Plugins not found', 'bit-form'), 400);
    }
  }

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    if (!class_exists('BWFCRM_Contact')) {
      (new UtilApiResponse())->apiResponse($logID, $this->_integrationID, ['type' =>  'record', 'type_name' => 'insert'], 'error', 'Autonami Pro Plugins not found');
      return;
    }

    $integrationDetails = is_string($integrationData->integration_details) ? json_decode($integrationData->integration_details) : $integrationData->integration_details;

    $fieldMap = $integrationDetails->field_map;
    $lists = $integrationDetails->lists;
    $tags = $integrationDetails->tags;
    $actions = $integrationDetails->actions;

    if (empty($fieldMap)) {
      return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Autonami api', 'bit-form'));
    }

    $recordApiHelper = new RecordApiHelper($this->_integrationID, $logID, $entryID);
    $autonamiApiResponse = $recordApiHelper->executeRecordApi($fieldValues, $fieldMap, $actions, $lists, $tags);

    if (is_wp_error($autonamiApiResponse)) {
      return $autonamiApiResponse;
    }
    return $autonamiApiResponse;
  }
}
