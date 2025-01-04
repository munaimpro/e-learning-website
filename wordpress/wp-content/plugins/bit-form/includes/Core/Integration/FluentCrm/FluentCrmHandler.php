<?php

/**
 * Fluent CRM Integration
 *
 */

namespace BitCode\BitForm\Core\Integration\FluentCrm;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use FluentCrm\App\Models\CustomContactField;
use FluentCrm\App\Models\Lists;
use FluentCrm\App\Models\Subscriber;
use FluentCrm\App\Models\Tag;
use WP_Error;

/**
 * Provide functionality for ZohoCrm integration
 */
class FluentCrmHandler
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
    add_action('wp_ajax_bitforms_fluent_crm_authorize', [__CLASS__, 'fluentCrmAuthorize']);
    add_action('wp_ajax_bitforms_refresh_fluent_crm_lists', [__CLASS__, 'fluentCrmLists']);
    add_action('wp_ajax_bitforms_fluent_crm_headers', [__CLASS__, 'fluentCrmFields']);
  }

  /**
   * for chen fluent crm plugins are exists
   */
  public static function checkedExistsFluentCRM()
  {
    return is_plugin_active('fluent-crm/fluent-crm.php') ? true : false;
  }

  /**
   * @return Fluent CRM lists
   */
  public static function fluentCrmLists()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $response = null;
      if (self::checkedExistsFluentCRM()) {
        $lists = Lists::get();
        $fluentCrmList = [];
        foreach ($lists as $list) {
          $fluentCrmList[$list->title] = (object) [
            'id'    => $list->id,
            'title' => $list->title
          ];
        }
        $tags = Tag::get();
        $fluentCrmTags = [];
        foreach ($tags as $tag) {
          $fluentCrmTags[$tag->title] = (object) [
            'id'    => $tag->id,
            'title' => $tag->title
          ];
        }
        $response['fluentCrmList'] = $fluentCrmList;
        $response['fluentCrmTags'] = $fluentCrmTags;
      } else {
        wp_send_json_error(
          __(
            'Fluent CRM Plugins not found',
            'bit-form'
          ),
          400
        );
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

  public static function fluentCrmFields()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $response = null;
      if (self::checkedExistsFluentCRM()) {
        $fieldOptions = [];
        $primaryField = ['first_name', 'last_name', 'full_name', 'email'];

        foreach (Subscriber::mappables() as $key => $column) {
          if (in_array($key, $primaryField)) {
            if ('email' === $key) {
              $fieldOptions[$column] = (object) [
                'key'      => $key,
                'label'    => $column,
                'type'     => 'primary',
                'required' => true
              ];
            } else {
              $fieldOptions[$column] = (object) [
                'key'     => $key,
                'label'   => $column,
                'type'    => 'primary'
              ];
            }
          } else {
            $fieldOptions[$column] = (object) [
              'key'       => $key,
              'label'     => $column,
              'type'      => 'custom'
            ];
          }
        }
        foreach ((new CustomContactField())->getGlobalFields()['fields'] as $field) {
          $fieldOptions[$field['label']] = (object) [
            'key'         => $field['slug'],
            'label'       => $field['label'],
            'type'        => 'custom'
          ];
        }
        $response['fluentCrmFlelds'] = $fieldOptions;
      } else {
        wp_send_json_error(
          __(
            'Fluent CRM Plugins not found',
            'bit-form'
          ),
          400
        );
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
   * @return True Fluent crm are exists
   */
  public static function fluentCrmAuthorize()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      if (self::checkedExistsFluentCRM()) {
        wp_send_json_success(true);
      } else {
        wp_send_json_error(
          __(
            'Please! Insatall Fluent CRM',
            'bit-form'
          ),
          400
        );
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

    // wp_send_json_success($integrationDetails);

    $fieldMap = $integrationDetails->field_map;
    $defaultDataConf = $integrationDetails->default;
    $list_id = $integrationDetails->list_id;
    $tags = $integrationDetails->tags;
    $actions = $integrationDetails->actions;
    // wp_send_json_success($fieldMap);
    if (empty($fieldMap)) {
      return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Fluent CRM api', 'bit-form'));
    }

    $recordApiHelper = new RecordApiHelper($this->_integrationID, $logID, $entryID);

    $fluentCrmApiResponse = $recordApiHelper->executeRecordApi(
      $fieldValues,
      $fieldMap,
      $actions,
      $list_id,
      $tags
    );

    if (is_wp_error($fluentCrmApiResponse)) {
      return $fluentCrmApiResponse;
    }
    return $fluentCrmApiResponse;
  }
}
