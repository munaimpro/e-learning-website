<?php

/**
 * ZohoSheet Integration
 *
 */

namespace BitCode\BitForm\Core\Integration\MailPoet;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use WP_Error;

/**
 * Provide functionality for ZohoCrm integration
 */
class MailPoetHandler
{
  private $_integrationID;

  public function __construct($integrationID, $fromID)
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
    add_action('wp_ajax_bitforms_mail_poet_authorize', [__CLASS__, 'mailPoetAuthorize']);
    add_action('wp_ajax_bitforms_refresh_news_letter', [__CLASS__, 'refreshNeswLetter']);
    add_action('wp_ajax_bitforms_mail_poet_list_headers', [__CLASS__, 'mailPoetListHeaders']);
  }

  /**
   * Process ajax request for generate_token
   *
   * @return string zoho crm api response and status
   */
  public static function mailPoetAuthorize()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      if (class_exists(\MailPoet\API\API::class)) {
        wp_send_json_success(true);
      } else {
        wp_send_json_error(
          __(
            'Please! Insatall MailPoet',
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

  /**
   * Process ajax request for refresh crm modules
   *
   * @return string JSON crm module data
   */
  public static function refreshNeswLetter()
  {
    $response = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      if (class_exists(\MailPoet\API\API::class)) {
        $mailpoet_api = \MailPoet\API\API::MP('v1');
        $newsletterList = $mailpoet_api->getLists();

        $allList = [];

        foreach ($newsletterList as $newsletter) {
          $allList[$newsletter['name']] = (object) [
            'newsletterId'   => $newsletter['id'],
            'newsletterName' => $newsletter['name']
          ];
        }
        $response['newsletterList'] = $allList;
        wp_send_json_success($response, 200);
      } else {
        wp_send_json_error(
          __(
            'Please! Install MailPoet',
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

  public static function mailPoetListHeaders()
  {
    $response = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      if (class_exists(\MailPoet\API\API::class)) {
        $mailpoet_api = \MailPoet\API\API::MP('v1');
        $subscriber_form_fields = $mailpoet_api->getSubscriberFields();

        $allList = [];

        foreach ($subscriber_form_fields as $fields) {
          $allList[$fields['name']] = (object) [
            'id'       => $fields['id'],
            'name'     => $fields['name'],
            'required' => $fields['params']['required']
          ];
        }
        $response['mailPoetFields'] = $allList;
        wp_send_json_success($response, 200);
      } else {
        wp_send_json_error(
          __(
            'Please! Insatall MailPoet',
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

    $fieldMap = $integrationDetails->field_map;
    $defaultDataConf = $integrationDetails->default;
    $lists = $integrationDetails->lists;
    if (empty($fieldMap)) {
      return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Mail Poet api', 'bit-form'));
    }

    $recordApiHelper = new RecordApiHelper($this->_integrationID, $logID, $entryID);

    $maiPoetApiResponse = $recordApiHelper->executeRecordApi(
      $fieldValues,
      $fieldMap,
      $lists
    );

    if (is_wp_error($maiPoetApiResponse)) {
      return $maiPoetApiResponse;
    }
    return $maiPoetApiResponse;
  }
}
