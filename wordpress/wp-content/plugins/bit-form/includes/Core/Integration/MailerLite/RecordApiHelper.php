<?php

/**
 * MailerLite    Record Api
 */

namespace BitCode\BitForm\Core\Integration\MailerLite;

use BitCode\BitForm\Core\Database\FormEntryLogModel;
use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\HttpHelper;

/**
 * Provide functionality for Record insert, upsert
 */
class RecordApiHelper
{
  private $_integrationID;
  private $_baseUrl;

  private $_defaultHeader;

  private $_logID;

  private $_logResponse;

  private $_entryID;

  private $_actions;

  public function __construct($auth_token, $integId, $logID, $entryID, $actions, $version)
  {
    $this->_integrationID = $integId;
    if ('v2' === $version) {
      $this->_baseUrl = 'https://connect.mailerlite.com/api/';
      $this->_defaultHeader = [
        'Authorization' => "Bearer $auth_token"
      ];
    } else {
      $this->_baseUrl = 'https://api.mailerlite.com/api/v2/';
      $this->_defaultHeader = [
        'X-Mailerlite-Apikey' => $auth_token
      ];
    }
    $this->_logID = $logID;
    $this->_logResponse = new UtilApiResponse();
    $this->_entryID = $entryID;
    $this->_actions = $actions;
  }

  public function existSubscriber($auth_token, $email)
  {
    if (empty($auth_token)) {
      wp_send_json_error(
        __(
          'Requested parameter is empty',
          'bit-integrations'
        ),
        400
      );
    }

    $apiEndpoints = $this->_baseUrl . "subscribers/$email";

    $response = HttpHelper::get($apiEndpoints, null, $this->_defaultHeader);
    if (property_exists($response, 'error') || 'Resource not found.' === $response->message) {
      return false;
    } else {
      return true;
    }
  }

  public function enableDoubleOptIn($auth_token)
  {
    if (empty($auth_token)) {
      wp_send_json_error(
        __(
          'Requested parameter is empty',
          'bit-integrations'
        ),
        400
      );
    }

    $apiEndpoints = $this->_baseUrl . 'settings/double_optin';
    $requestParams = [
      'enable' => true
    ];

    HttpHelper::post($apiEndpoints, $requestParams, $this->_defaultHeader);
  }

  public function addSubscriber($auth_token, $groupIds, $type, $finalData)
  {
    $apiEndpoints = $this->_baseUrl . 'subscribers';
    $splitGroupIds = null;
    if (!empty($groupIds)) {
      $splitGroupIds = explode(',', $groupIds);
    }

    if (empty($finalData['email'])) {
      return ['success'=>false, 'message'=>'Required field Email is empty', 'code'=>400];
    }
    if ('https://connect.mailerlite.com/api/' === $this->_baseUrl) {
      $requestParams = [
        'email'   => $finalData['email'],
        'status'  => $type ? $type : 'active',
      ];
    } else {
      $requestParams = [
        'email' => $finalData['email'],
        'type'  => $type ? $type : 'active',
      ];
    }

    foreach ($finalData as $key => $value) {
      if ('email' !== $key) {
        if ('name' === $key) {
          $requestParams[$key] = $value;
        } else {
          $requestParams['fields'][$key] = $value;
        }
      }
    }
    $requestParams['fields'] = !empty($requestParams['fields']) ? (object) $requestParams['fields'] : [];
    $email = $finalData['email'];
    $isExist = $this->existSubscriber($auth_token, $email);
    $response = null;

    if ($isExist && !empty($this->_actions->update)) {
      if (!empty($this->_actions->double_opt_in)) {
        $this->enableDoubleOptIn($auth_token);
      }
      if (!empty($groupIds)) {
        if ('https://connect.mailerlite.com/api/' === $this->_baseUrl) {
          $requestParams['groups'] = $splitGroupIds;
          $response = HttpHelper::post($apiEndpoints, $requestParams, $this->_defaultHeader);
        } else {
          for ($i = 0; $i < count($splitGroupIds); $i++) {
            $apiEndpoints = $this->_baseUrl . 'groups/' . $splitGroupIds[$i] . '/subscribers';
            $response = HttpHelper::post($apiEndpoints, $requestParams, $this->_defaultHeader);
          };
        }
        return $response;
      }
      $response = HttpHelper::post($apiEndpoints, $requestParams, $this->_defaultHeader);
      $response->update = true;
    } elseif ($isExist && empty($this->_actions->update)) {
      return ['success'=>false, 'message'=>'Subscriber already exist', 'code'=>400];
    } else {
      if (!empty($this->_actions->double_opt_in)) {
        $this->enableDoubleOptIn($auth_token);
      }
      if (!empty($groupIds)) {
        if ('https://connect.mailerlite.com/api/' === $this->_baseUrl) {
          $requestParams['groups'] = $splitGroupIds;
          $response = HttpHelper::post($apiEndpoints, $requestParams, $this->_defaultHeader);
        } else {
          for ($i = 0; $i < count($splitGroupIds); $i++) {
            $apiEndpoints = $this->_baseUrl . 'groups/' . $splitGroupIds[$i] . '/subscribers';
            $response = HttpHelper::post($apiEndpoints, $requestParams, $this->_defaultHeader);
          };
        }
        return $response;
      }
      $response = HttpHelper::post($apiEndpoints, $requestParams, $this->_defaultHeader);
    }
    return $response;
  }

  public function generateReqDataFromFieldMap($data, $fieldMap)
  {
    $dataFinal = [];

    foreach ($fieldMap as $value) {
      $triggerValue = $value->formField;
      $actionValue = $value->mailerLiteFormField;
      if ('custom' === $triggerValue) {
        $dataFinal[$actionValue] = $value->customValue;
      } elseif (!is_null($data[$triggerValue])) {
        $dataFinal[$actionValue] = $data[$triggerValue];
      }
    }

    return $dataFinal;
  }

  public function executeRecordApi(
    $groupId,
    $type,
    $fieldValues,
    $fieldMap,
    $auth_token
  ) {
    $fieldData = [];
    $attributes = [];
    $model = new FormEntryLogModel();
    $fieldData['attributes'] = (object) $attributes;

    $recordApiResponse = null;
    if ($this->_entryID) {
      $result = $model->entryLogCheck($this->_entryID, $this->_integrationID);
      if (!count($result) || isset($result->errors['result_empty'])) {
        $finalData = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
        $recordApiResponse = $this->addSubscriber($auth_token, $groupId, $type, $finalData);

        if (empty($recordApiResponse)) {
          $recordApiResponse = ['success' => true, 'id' => $fieldData['email']];
        }
      }
    }

    if (isset($recordApiResponse->data->id) || isset($recordApiResponse->id)) {
      $res = ['success'=>true, 'message'=>isset($recordApiResponse->update) ? 'Subscriber has been updated successfully' : 'Subscriber has been created successfully', 'code'=>200];
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' =>  'record', 'type_name' =>isset($recordApiResponse->update) ? 'update' : 'insert'], 'success', $res);
    } else {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' =>  'record', 'type_name' => isset($recordApiResponse->update) ? 'update' : 'insert'], 'error', $recordApiResponse);
    }
    return $recordApiResponse;
  }
}
