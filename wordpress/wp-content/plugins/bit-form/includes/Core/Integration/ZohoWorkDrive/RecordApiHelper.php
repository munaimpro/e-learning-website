<?php

/**
 * ZohoRecruit Record Api
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoWorkDrive;

use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\FieldValueHandler;
use BitCode\BitForm\Core\Util\HttpHelper;

/**
 * Provide functionality for Record insert,upsert
 */
class RecordApiHelper
{
  private $_defaultHeader;
  private $_apiDomain;
  private $_tokenDetails;
  private $_integrationID;
  private $_logID;
  private $_logResponse;

  public function __construct($tokenDetails, $integId, $logID)
  {
    $this->_defaultHeader['Authorization'] = "Zoho-oauthtoken {$tokenDetails->access_token}";
    $this->_apiDomain = \urldecode($tokenDetails->api_domain);
    $this->_tokenDetails = $tokenDetails;
    $this->_integrationID = $integId;
    $this->_logID = $logID;
    $this->_logResponse = new UtilApiResponse();
  }

  public function createFolder($dataCenter, $data)
  {
    $createFolderEndpoint = "https://workdrive.zoho.{$dataCenter}/api/v1/files";

    return HttpHelper::post($createFolderEndpoint, $data, $this->_defaultHeader);
  }

  private function shareAccess($resourceId, $email, $access, $notify, $dataCenter)
  {
    $createFolderEndpoint = "https://workdrive.zoho.{$dataCenter}/api/v1/permissions";

    $shareAccessData['data'] = [
      'attributes' => [
        'resource_id'            => $resourceId,
        'shared_type'            => 'personal',
        'email_id'               => $email,
        'role_id'                => $access,
        'send_notification_mail' => $notify
      ],
      'type' => 'permissions'
    ];

    return HttpHelper::post($createFolderEndpoint, wp_json_encode($shareAccessData), $this->_defaultHeader);
  }

  public function executeRecordApi($team, $folder, $dataCenter, $fieldValues, $default, $actions, $entryID, $formID)
  {
    $uploadFolder = $folder;
    $createFolderData = null;
    if (!empty($actions->create_folder)) {
      $foldername = $actions->create_folder->name;

      if ($actions->create_folder->suffix) {
        $foldername = $foldername . ' ' . uniqid();
      }

      if ('' === $foldername) {
        $foldername = 'New Folder ' . ' ' . uniqid();
      }

      $createFolderData['data'] = [
        'attributes' => [
          'name'      => FieldValueHandler::replaceFieldWithValue($foldername, $fieldValues),
          'parent_id' => $folder
        ],
        'type' => 'files'
      ];

      $createFolderResponse = $this->createFolder($dataCenter, wp_json_encode($createFolderData));
      if (isset($createFolderResponse->errors)) {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'folder', 'type_name' => 'create'], 'error', $createFolderResponse);
      } else {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'folder', 'type_name' => 'create'], 'success', $createFolderResponse);
      }

      $uploadFolder = $createFolderResponse->data->id;

      $this->handleShareAccess('folder', $actions, $default->users->{$team}, $uploadFolder, $dataCenter, $fieldValues);
    }

    if (!empty($actions->attachments)) {
      $filesApiHelper = new FilesApiHelper($this->_tokenDetails, $formID, $entryID);
      $attachments = explode(',', $actions->attachments);
      $attachmentApiResponses = [];
      $responseType = 'success';
      $fileFound = 0;
      foreach ($attachments as $fileField) {
        if (isset($fieldValues[$fileField]) && !empty($fieldValues[$fileField])) {
          $fileFound = 1;
          if (is_array($fieldValues[$fileField])) {
            foreach ($fieldValues[$fileField] as $singleFile) {
              $attachmentResponse = $filesApiHelper->uploadFiles($singleFile, $uploadFolder, $dataCenter);
              $attachmentId = $attachmentResponse->data[0]->attributes->resource_id;
              if (isset($attachmentResponse->errors)) {
                $responseType = 'error';
              }
              $attachmentApiResponses[] = $attachmentResponse;
              $this->handleShareAccess('file', $actions, $default->users->{$team}, $attachmentId, $dataCenter, $fieldValues);
            }
          } else {
            $attachmentResponse = $filesApiHelper->uploadFiles($fieldValues[$fileField], $uploadFolder, $dataCenter);
            $attachmentId = $attachmentResponse->data[0]->attributes->resource_id;
            if (isset($attachmentResponse->errors)) {
              $responseType = 'error';
            }
            $attachmentApiResponses[] = $attachmentResponse;
            $this->handleShareAccess('file', $actions, $default->users->{$team}, $attachmentId, $dataCenter, $fieldValues);
          }
        }
      }
      if ($fileFound) {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'file', 'type_name' => 'upload'], $responseType, $attachmentApiResponses);
      }
    }

    return $uploadFolder;
  }

  private function handleShareAccess($type, $actions, $users, $resourceId, $dataCenter, $fieldValues)
  {
    $shareAccessResponses = [];
    $responseType = 'success';
    $emailFound = 0;
    if (isset($actions->share->{$type}->permissions)) {
      foreach ($actions->share->{$type}->permissions as $user_share) {
        if (!empty($user_share->email)) {
          $emailFound = 1;
          $emails = explode(',', $user_share->email);
          if (in_array('all_users', $emails)) {
            foreach ($users as $user) {
              if (is_email($user->userId)) {
                $shareAccessResponse = $this->shareAccess($resourceId, $user->userId, $user_share->access, $actions->share->{$type}->mail, $dataCenter);
                if (isset($shareAccessResponse->errors)) {
                  $responseType = 'error';
                }
                $shareAccessResponses[] = $shareAccessResponse;
              }
            }
          } else {
            foreach ($emails as $email) {
              $email = FieldValueHandler::replaceFieldWithValue($email, $fieldValues);
              if (is_email($email)) {
                $shareAccessResponse = $this->shareAccess($resourceId, $email, $user_share->access, $actions->share->{$type}->mail, $dataCenter);
                if (isset($shareAccessResponse->errors)) {
                  $responseType = 'error';
                }
                $shareAccessResponses[] = $shareAccessResponse;
              }
            }
          }
        }
      }

      if ($emailFound) {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'share', 'type_name' => $type], $responseType, $shareAccessResponse);
      }
    }
    return $shareAccessResponses;
  }
}
