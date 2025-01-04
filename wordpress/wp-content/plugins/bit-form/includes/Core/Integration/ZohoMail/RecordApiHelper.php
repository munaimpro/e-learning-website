<?php

/**
 * ZohoRecruit Record Api
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoMail;

use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\FieldValueHandler;
use BitCode\BitForm\Core\Util\HttpHelper;

/**
 * Provide functionality for Record insert,upsert
 */
class RecordApiHelper
{
  private $_defaultHeader;
  private $_tokenDetails;

  private $_logID;

  private $_integrationID;

  private $_logResponse;

  public function __construct($tokenDetails, $integId, $logID)
  {
    $this->_tokenDetails = $tokenDetails;
    $this->_integrationID = $integId;
    $this->_logID = $logID;
    $this->_logResponse = new UtilApiResponse();
    $this->_defaultHeader['Authorization'] = "Zoho-oauthtoken {$tokenDetails->access_token}";
    $this->_defaultHeader['accountId'] = $tokenDetails->accountId;
    $this->_defaultHeader['Content-Type'] = 'application/json';
  }

  public function insertRecord($dataCenter, $data)
  {
    $insertRecordEndpoint = "https://mail.zoho.{$dataCenter}/api/accounts/{$this->_defaultHeader['accountId']}/messages";
    return HttpHelper::post($insertRecordEndpoint, $data, $this->_defaultHeader);
  }

  public function executeRecordApi($integrationDetails, $fieldValues, $formID, $entryID)
  {
    $mailInfo = [
      'fromAddress' => $integrationDetails->tokenDetails->accountEmail,
      'toAddress'   => implode(',', FieldValueHandler::validateMailArry($integrationDetails->to, $fieldValues)),
      'ccAddress'   => implode(',', FieldValueHandler::validateMailArry($integrationDetails->cc, $fieldValues)),
      'bccAddress'  => implode(',', FieldValueHandler::validateMailArry($integrationDetails->bcc, $fieldValues)),
      'subject'     => FieldValueHandler::replaceFieldWithValue($integrationDetails->subject, $fieldValues),
      'content'     => FieldValueHandler::replaceFieldWithValue($integrationDetails->body, $fieldValues)
    ];

    if ('draft' === $integrationDetails->mailType) {
      $mailInfo['mode'] = 'draft';
    }

    if (!empty($integrationDetails->actions->attachments)) {
      $mailAttachments = [];
      $fileFound = 0;
      $responseType = 'success';
      $attachmentApiResponses = [];
      $filesApiHelper = new FilesApiHelper($this->_tokenDetails, $formID, $entryID);
      $attachments = explode(',', $integrationDetails->actions->attachments);
      foreach ($attachments as $fileField) {
        if (isset($fieldValues[$fileField]) && !empty($fieldValues[$fileField])) {
          $fileFound = 1;
          if (is_array($fieldValues[$fileField])) {
            foreach ($fieldValues[$fileField] as $singleFile) {
              $attachmentApiResponse = $filesApiHelper->uploadFiles($singleFile,  $integrationDetails->tokenDetails->accountId, $integrationDetails->dataCenter);
              if (isset($attachmentApiResponse->data)) {
                array_push($mailAttachments, $attachmentApiResponse->data);
              }
              if (!isset($attachmentApiResponse->status) && !isset($attachmentApiResponse->status->code) && 200 !== $attachmentApiResponse->status->code) {
                $responseType = 'error';
              }
              $attachmentApiResponses[] = $attachmentApiResponse;
            }
          } else {
            $attachmentApiResponse = $filesApiHelper->uploadFiles($fieldValues[$fileField],  $integrationDetails->tokenDetails->accountId, $integrationDetails->dataCenter);
            if (isset($attachmentApiResponse->data)) {
              array_push($mailAttachments, $attachmentApiResponse->data);
            }
            if (!isset($attachmentApiResponse->status) && !isset($attachmentApiResponse->status->code) && 200 !== $attachmentApiResponse->status->code) {
              $responseType = 'error';
            }

            $attachmentApiResponses[] = $attachmentApiResponse;
          }
        }
      }

      $mailInfo['attachments'] = $mailAttachments;
      if ($fileFound) {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'file', 'type_name' => 'mail'], $responseType, $attachmentApiResponses);
      }
    }

    $recordApiResponse = $this->insertRecord($integrationDetails->dataCenter, wp_json_encode($mailInfo));
    if (200 === $recordApiResponse->status->code) {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' =>  $integrationDetails->mailType, 'type_name' => 'mail'], 'success', $recordApiResponse);
    } else {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => $integrationDetails->mailType, 'type_name' => 'mail'], 'error', $recordApiResponse);
    }

    return $recordApiResponse;
  }
}
