<?php

/**
 * WebHooks Integration
 *
 */

namespace BitCode\BitForm\Core\Integration\WebHooks;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\HttpHelper;

/**
 * Provide functionality for webhooks
 */
class WebHooksHandler
{
  private $formID;
  private $webhookID;

  private $_logResponse;

  public function __construct($webhookID, $formID)
  {
    $this->formID = $formID;
    $this->webhookID = $webhookID;
    $this->_logResponse = new UtilApiResponse();
  }

  /**
   * Helps to register ajax function's with wp
   *
   * @return null
   */
  public static function registerAjax()
  {
    add_action('wp_ajax_bitforms_test_webhook', [__CLASS__, 'testWebhook']);
  }

  public static function testWebhook()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce(sanitize_text_field($_REQUEST['_ajax_nonce']), 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $webhookDetails = json_decode($inputJSON);
      $details = is_string($webhookDetails) ? json_decode($webhookDetails)->hookDetails : $webhookDetails->hookDetails;
      $method = isset($details->method) ? $details->method : 'get';
      $data = isset($details->url) ? WebHooksHandler::urlParserWrapper($details->url) : false;
      $response = null;
      if ($data) {
        $url = $data['url'];
        $params = $data['params'];
        $params['entry_id'] = 'test';
        switch (strtoupper($method)) {
          case 'GET':
            $response = HttpHelper::get($url, $params);
            break;

          case 'POST':
            $response = HttpHelper::post($url, $params);
            break;

          default:
            $response = HttpHelper::request($url, $method, $params);
            break;
        }
      }
      if (is_wp_error($response)) {
        wp_send_json_error(
          empty($response) ? 'Unknown Error Occured' : $response->get_error_message(),
          400
        );
      }
      if (empty($data['url'])) {
        wp_send_json_error(__('webhook url is empty', 'bit-form'), 400);
      }
      wp_send_json_success(['msg' => 'webhook executed succcessfully', 'response' => $response], 200);
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

  public function execute(IntegrationHandler $integrationHandler, $integrationDetails, $fieldValues, $entryID, $logID)
  {
    $details = is_string($integrationDetails->integration_details) ? json_decode($integrationDetails->integration_details) : $integrationDetails->integration_details;
    $method = isset($details->method) ? $details->method : 'get';
    $data = isset($details->url) ? $this->urlParserWrapper($details->url) : false;

    if ($data) {
      $url = $data['url'];
      $params = $data['params'];
      $params = IntegrationHandler::replaceFieldWithValue($params, $fieldValues);
      $params['entry_id'] = $entryID;
      switch (strtoupper($method)) {
        case 'GET':
          $response = HttpHelper::get($url, $params);
          break;

        case 'POST':
          $response = HttpHelper::post($url, $params);
          break;

        default:
          $response = HttpHelper::request($url, $method, $params);
          break;
      }

      // if bitform pro is not active then return the response without response log
      // if (!class_exists('BitCode\\BitFormPro\\Plugin')) {
      //   return $response;
      // }

      // if bitform pro is active then return the response with response log
      if (is_wp_error($response)) {
        $this->_logResponse->apiResponse(
          $logID,
          $this->webhookID,
          ['type' => 'record', 'type_name' => 'web hooks'],
          'errors',
          $response
        );
      } else {
        $this->_logResponse->apiResponse(
          $logID,
          $this->webhookID,
          ['type' => 'record', 'type_name' => 'web hooks'],
          'success',
          $response
        );
      }
      return $response;
    } else {
      // if (!class_exists('BitCode\\BitFormPro\\Plugin')) {
      //   return false;
      // }

      $this->_logResponse->apiResponse(
        $logID,
        $this->webhookID,
        ['type' => 'record', 'type_name' => 'web hooks'],
        'errors',
        'There is something wrong in the webhook url'
      );
    }
  }

  private static function urlParserWrapper($url)
  {
    if (empty($url)) {
      return false;
    }
    $parsedURL = wp_parse_url($url);

    $Scheme = isset($parsedURL['scheme']) ? $parsedURL['scheme'] . '://' : null;
    $Usr = isset($parsedURL['usr']) ? $parsedURL['usr'] : null;
    $Pass = isset($parsedURL['pass']) ? ':' . $parsedURL['pass'] : null;
    $Host = isset($parsedURL['host']) ? $parsedURL['host'] : null;
    $Port = isset($parsedURL['port']) ? ':' . $parsedURL['port'] : null;
    $Path = isset($parsedURL['path']) ? $parsedURL['path'] : null;
    $Query = isset($parsedURL['query']) ? $parsedURL['query'] : null;
    $Pass = ($Pass || $Usr) ? "$Pass@" : null;

    $cleanURL = "$Scheme$Usr$Pass$Host$Port$Path";
    $params = [];
    foreach (explode('&', $Query) as $keyValue) {
      if (empty($keyValue)) {
        continue;
      }
      list($field, $value) = explode('=', $keyValue);
      if ('' === trim($value)) {
        continue;
      }
      if (isset($params[$field])) {
        if (\is_array($params[$field])) {
          $params[$field][] = sanitize_text_field(urldecode($value));
        } else {
          $params[$field] = [$params[$field], sanitize_text_field(urldecode($value))];
        }
      } else {
        $params[$field] = sanitize_text_field(urldecode($value));
      }
    }

    return ['url' => $cleanURL, 'params' => $params];
  }

  private function iterate($array)
  {
    $ar = [];
    if (is_array($array)) {
      foreach ($array as $k => $v) {
        $ar[$k] = str_replace("\'", "'", $v);
      }
    }
    return $ar;
  }
}