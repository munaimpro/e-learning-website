<?php

/**
 * ZohoRecruit Record Api
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoDesk;

use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\HttpHelper;
use WP_Error;

/**
 * Provide functionality for Record insert,upsert
 */
class RecordApiHelper
{
  private $_defaultHeader;
  private $_apiDomain;
  private $_tokenDetails;

  private $_logID;

  private $_integrationID;

  private $_logResponse;

  public function __construct($tokenDetails, $orgId, $integId, $logID)
  {
    $this->_defaultHeader['Authorization'] = "Zoho-oauthtoken {$tokenDetails->access_token}";
    $this->_defaultHeader['orgId'] = $orgId;
    $this->_apiDomain = \urldecode($tokenDetails->api_domain);
    $this->_tokenDetails = $tokenDetails;
    $this->_integrationID = $integId;
    $this->_logID = $logID;
    $this->_logResponse = new UtilApiResponse();
  }

  public function insertRecord($dataCenter, $data)
  {
    $insertRecordEndpoint = "https://desk.zoho.{$dataCenter}/api/v1/tickets";

    return HttpHelper::post($insertRecordEndpoint, $data, $this->_defaultHeader);
  }

  public function createContact($dataCenter, $data)
  {
    $getContactEndpoint = "https://desk.zoho.{$dataCenter}/api/v1/contacts";

    return HttpHelper::post($getContactEndpoint, $data, $this->_defaultHeader);
  }

  public function searchContact($dataCenter, $email)
  {
    $searchContactEndpoint = "https://desk.zoho.{$dataCenter}/api/v1/contacts/search?limit=1&email={$email}";

    return HttpHelper::get($searchContactEndpoint, null, $this->_defaultHeader);
  }

  public function executeRecordApi($department, $dataCenter, $fieldValues, $fieldMap, $required, $actions, $entryID, $formID)
  {
    $fieldData = [];
    $customFieldData = [];
    foreach ($fieldMap as $fieldKey => $fieldPair) {
      if (!empty($fieldPair->zohoFormField)) {
        if ('custom' === $fieldPair->formField && isset($fieldPair->customValue)) {
          $fieldData[$fieldPair->zohoFormField] = $fieldPair->customValue;
        } else {
          if ('cf' === strtok($fieldPair->zohoFormField, '_')) {
            $customFieldData[$fieldPair->zohoFormField] = $fieldValues[$fieldPair->formField];
          } else {
            $fieldData[$fieldPair->zohoFormField] = $fieldValues[$fieldPair->formField];
          }
        }
      }

      if (empty($fieldData[$fieldPair->zohoFormField]) && \in_array($fieldPair->zohoFormField, $required)) {
        $error = new WP_Error('REQ_FIELD_EMPTY', wp_sprintf(__('%s is required for zoho bigin', 'bit-form'), $fieldPair->zohoFormField));
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'field'], 'validation', $error);
        return $error;
      }
    }

    $contactData = [
      'lastName' => $fieldData['lastName']
    ];

    if (array_key_exists('firstName', $fieldData)) {
      $contactData['firstName'] = $fieldData['firstName'];
    }

    $contactId = '';

    if (array_key_exists('email', $fieldData)) {
      $contactData['email'] = $fieldData['email'];

      $contactApiResponse = $this->searchContact($dataCenter, $contactData['email']);

      if ($contactApiResponse) {
        $contactId = $contactApiResponse->data[0]->id;
      }
    }

    if ('' === $contactId) {
      $contactApiResponse = $this->createContact($dataCenter, wp_json_encode($contactData));

      if (isset($contactApiResponse->errorCode)) {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'contact'], 'error', $contactApiResponse);
      } else {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'contact'], 'success', $contactApiResponse);
      }

      $contactId = $contactApiResponse->id;
    }

    $ticketData = $fieldData;

    unset($ticketData['firstName'], $ticketData['lastName']);

    $ticketData['contactId'] = $contactId;
    $ticketData['departmentId'] = $department;
    $ticketData['assigneeId'] = $actions->ticket_owner;

    if (!empty($actions->product)) {
      $ticketData['productId'] = $actions->product;
    }

    if ($customFieldData) {
      $ticketData['cf'] = $customFieldData;
    }

    $ticketApiResponse = $this->insertRecord($dataCenter, wp_json_encode($ticketData));

    if (isset($ticketApiResponse->errorCode)) {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'ticket'], 'error', $ticketApiResponse);
    } else {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'ticket'], 'success', $ticketApiResponse);
    }

    if (!empty($actions->attachments)) {
      $filesApiHelper = new FilesApiHelper($this->_tokenDetails, $this->_defaultHeader['orgId'], $formID, $entryID);
      $fileFound = 0;
      $responseType = 'success';
      $attachmentApiResponses = [];
      $attachments = explode(',', $actions->attachments);
      foreach ($attachments as $fileField) {
        if (isset($fieldValues[$fileField]) && !empty($fieldValues[$fileField])) {
          $fileFound = 1;
          if (is_array($fieldValues[$fileField])) {
            foreach ($fieldValues[$fileField] as $singleFile) {
              $attachmentApiResponse = $filesApiHelper->uploadFiles($singleFile, $ticketApiResponse->id, $dataCenter);
              if (isset($attachmentApiResponse->errorCode)) {
                $responseType = 'error';
              }
              $attachmentApiResponses[] = $attachmentApiResponse;
            }
          } else {
            $attachmentApiResponse = $filesApiHelper->uploadFiles($fieldValues[$fileField], $ticketApiResponse->id, $dataCenter);
            if (isset($attachmentApiResponse->errorCode)) {
              $responseType = 'error';
            }
            $attachmentApiResponses[] = $attachmentApiResponse;
          }
        }
      }

      if ($fileFound) {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'file', 'type_name' => 'ticket'], $responseType, $attachmentApiResponses);
      }
    }

    return $ticketApiResponse;
  }
}
