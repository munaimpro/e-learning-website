<?php

/**
 * Telegram Record Api
 *
 */

namespace BitCode\BitForm\Core\Integration\Telegram;

use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\FieldValueHandler;
use BitCode\BitForm\Core\Util\HttpHelper;

/**
 * Provide functionality for Record insert,upsert
 */
class RecordApiHelper
{
  private $_defaultHeader;
  private $_integrationID;
  private $_logID;
  private $_logResponse;
  private $_entryID;
  private $_apiEndPoint;

  public function __construct($apiEndPoint, $integId, $logID, $entryID)
  {
    // wp_send_json_success($tokenDetails);
    $this->_defaultHeader['Content-Type'] = 'multipart/form-data';
    $this->_integrationID = $integId;
    $this->_logID = $logID;
    $this->_logResponse = new UtilApiResponse();
    $this->_entryID = $entryID;
    $this->_apiEndPoint = $apiEndPoint;
  }

  public function sendMessages($data)
  {
    $insertRecordEndpoint = $this->_apiEndPoint . '/sendMessage';
    return HttpHelper::get($insertRecordEndpoint, $data, $this->_defaultHeader);
  }

  public function executeRecordApi($integrationDetails, $fieldValues, $formID, $entryID)
  {
    $msg = FieldValueHandler::replaceFieldWithValue($integrationDetails->body, $fieldValues);
    $messagesBody = str_replace(['<p>', '</p>'], ' ', $msg);

    if (!empty($integrationDetails->actions->attachments)) {
      $attachment = $integrationDetails->actions->attachments;
      $file = [];
      foreach ($fieldValues as $fieldKey => $fieldValue) {
        if (in_array($fieldKey, $attachment)) {
          if (is_array($fieldValue)) {
            $file = array_merge($file, $fieldValue);
          } else {
            $file[] = $fieldValue;
          }
        }
      }

      if (!empty($file)) {
        if (is_array($file) && count($file) > 1) {
          $data = [
            'chat_id' => $integrationDetails->chat_id,
            'caption' => $messagesBody,
            'media'   => $file
          ];

          $sendPhotoApiHelper = new FilesApiHelper($formID, $entryID);
          $recordApiResponse = $sendPhotoApiHelper->uploadMultipleFiles($this->_apiEndPoint, $data);
        } else {
          $data = [
            'chat_id'    => $integrationDetails->chat_id,
            'caption'    => $messagesBody,
            'parse_mode' => $integrationDetails->parse_mode,
            'photo'      => is_array($file) ? $file[0] : $file
          ];

          $sendPhotoApiHelper = new FilesApiHelper($formID, $entryID);
          $recordApiResponse = $sendPhotoApiHelper->uploadFiles($this->_apiEndPoint, $data);
        }
      } else {
        $data = [
          'chat_id'    => $integrationDetails->chat_id,
          'text'       => $messagesBody,
          'parse_mode' => $integrationDetails->parse_mode
        ];
        $recordApiResponse = $this->sendMessages($data);
      }

      $type = 'insert';
    } else {
      $data = [
        'chat_id'    => $integrationDetails->chat_id,
        'text'       => $messagesBody,
        'parse_mode' => $integrationDetails->parse_mode
      ];
      $recordApiResponse = $this->sendMessages($data);
      $type = 'insert';
    }
    if (is_string($recordApiResponse)) {
      $recordApiResponse = json_decode($recordApiResponse);
    }

    if ($recordApiResponse && $recordApiResponse->ok) {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' =>  'record', 'type_name' => $type], 'success', $recordApiResponse);
    } else {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' =>  'record', 'type_name' => $type], 'error', $recordApiResponse);
    }
    return $recordApiResponse;
  }
}
