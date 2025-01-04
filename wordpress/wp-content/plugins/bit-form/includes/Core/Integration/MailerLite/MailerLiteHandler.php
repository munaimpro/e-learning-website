<?php

/**
 * MailerLite Integration
 */

namespace BitCode\BitForm\Core\Integration\MailerLite;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\HttpHelper;
use WP_Error;

/**
 * Provide functionality for MailerLite integration
 */
class MailerLiteHandler
{
  private $_integrationID;
  private static $_baseUrlV1 = 'https://api.mailerlite.com/api/v2/';
  private static $_baseUrlV2 = 'https://connect.mailerlite.com/api/';
  protected $_defaultHeader;

  public function __construct($integrationID, $fromID)
  {
    $this->_integrationID = $integrationID;
  }

  public static function registerAjax()
  {
    add_action('wp_ajax_bitforms_mailerlite_fetch_all_groups', [__CLASS__, 'fetchAllGroups']);
    add_action('wp_ajax_bitforms_mailerlite_refresh_fields', [__CLASS__, 'mailerliteRefreshFields']);
  }

  public static function fetchAllGroups()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $requestParams = json_decode($inputJSON);
      if (
        empty($requestParams->auth_token)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bitform'
          ),
          400
        );
      }

      if ('v2' === $requestParams->version) {
        $apiEndpoints = self::$_baseUrlV2 . 'groups/';
        $apiKey = $requestParams->auth_token;
        $header = [
          'Authorization: Bearer ' . $apiKey
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
          CURLOPT_URL            => $apiEndpoints,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING       => '',
          CURLOPT_MAXREDIRS      => 10,
          CURLOPT_TIMEOUT        => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST  => 'GET',
          CURLOPT_HTTPHEADER     => $header,
        ]);
        $data = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($data);

        $formattedResponse = [];

        foreach ($response->data as $value) {
          $formattedResponse[] =
              [
                'group_id' => $value->id,
                'name'     => $value->name,
              ];
        }
      } else {
        $apiEndpoints = self::$_baseUrlV1 . 'groups/';

        $header = [
          'X-Mailerlite-Apikey' => $requestParams->auth_token,
        ];

        $response = HttpHelper::get($apiEndpoints, null, $header);
        $formattedResponse = [];

        foreach ($response as $value) {
          $formattedResponse[] =
              [
                'group_id' => $value->id,
                'name'     => $value->name,
              ];
        }
      }

      wp_send_json_success($formattedResponse, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bitform'
        ),
        401
      );
    }
  }

  public static function mailerliteRefreshFields()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $requestParams = json_decode($inputJSON);

      if (
        empty($requestParams->auth_token)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bitform'
          ),
          400
        );
      }

      if ('v2' === $requestParams->version) {
        $apiEndpoints = self::$_baseUrlV2 . 'fields';

        $apiKey = $requestParams->auth_token;
        $header = [
          'Authorization' => "Bearer $apiKey",
        ];

        $response = HttpHelper::get($apiEndpoints, null, $header);

        $newResponse = [];
        foreach ($response->data as $value) {
          if ('email' !== $value->key) {
            $newResponse[] = [
              'key'      => $value->key,
              'label'    => $value->name,
              'required' => 'email' === $value->key ? true : false,
            ];
          }
        }

        $email[] = [
          'key'      => 'email',
          'label'    => 'Email',
          'required' => true,
        ];

        $formattedResponse = array_merge($email, $newResponse);

        if (isset($response->data)) {
          wp_send_json_success($formattedResponse, 200);
        } elseif (isset($response->message) && 'Unauthenticated.' === $response->message) {
          wp_send_json_error(
            __(
              'Invalid API Token',
              'bitform'
            ),
            401
          );
        }
      } else {
        $apiEndpoints = self::$_baseUrlV1 . 'fields';

        $apiKey = $requestParams->auth_token;
        $header = [
          'X-Mailerlite-Apikey' => $apiKey,
        ];

        $response = HttpHelper::get($apiEndpoints, null, $header);

        $formattedResponse = [];
        foreach ($response as $value) {
          $formattedResponse[] = [
            'key'      => $value->key,
            'label'    => $value->title,
            'required' => 'email' === $value->key ? true : false,
          ];
        }

        if (count($response) > 0) {
          wp_send_json_success($formattedResponse, 200);
        } elseif (isset($response->error->message) && 'Unauthorized' === $response->error->message) {
          wp_send_json_error(
            __(
              'Invalid API Token',
              'bitform'
            ),
            401
          );
        }
      }
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bitform'
        ),
        401
      );
    }
  }

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = is_string($integrationData->integration_details) ? json_decode($integrationData->integration_details) : $integrationData->integration_details;
    $auth_token = $integrationDetails->auth_token;
    $version = $integrationDetails->version;
    $groupIds = $integrationDetails->group_ids;
    $fieldMap = $integrationDetails->field_map;
    $type = $integrationDetails->mailer_lite_type;
    $actions = $integrationDetails->actions;

    if (
      empty($fieldMap)
       || empty($auth_token)
    ) {
      return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for MailerLite api', 'bit-integrations'));
    }
    $recordApiHelper = new RecordApiHelper($auth_token, $this->_integrationID, $logID, $entryID, $actions, $version);
    $mailerliteApiResponse = $recordApiHelper->executeRecordApi(
      $groupIds,
      $type,
      $fieldValues,
      $fieldMap,
      $auth_token
    );

    if (is_wp_error($mailerliteApiResponse)) {
      return $mailerliteApiResponse;
    }
    return $mailerliteApiResponse;
  }
}
