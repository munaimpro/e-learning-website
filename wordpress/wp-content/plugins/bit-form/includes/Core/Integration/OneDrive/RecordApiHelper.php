<?php

namespace BitCode\BitForm\Core\Integration\OneDrive;

use BitCode\BitForm\Core\Util\ApiResponse;
use BitCode\BitForm\Core\Util\FileHandler;
use WP_Error;

class RecordApiHelper
{
  protected $token;
  protected $formId;
  protected $entryId;
  protected $errorApiResponse = [];
  protected $successApiResponse = [];

  private $logResponse;

  public function __construct($token, $formId, $entryId)
  {
    $this->token = $token;
    $this->formId = $formId;
    $this->entryId = $entryId;
    $this->logResponse = new ApiResponse();
  }

  public function uploadFile($folder, $filePath, $folderId, $parentId)
  {
    if ('' === $filePath) {
      return;
    }

    $filePath = $this->makeFilePath($filePath);
    $filesize = filesize($filePath);
    $fp = fopen($filePath, 'rb');
    $body = fread($fp, $filesize);
    if (!$body) {
      return new WP_Error(423, 'Can\'t open file!');
    }
    if (is_null($parentId)) {
      $parentId = $folderId;
    }
    $ids = explode('!', $folderId);
    if ('' === $filePath) {
      return false;
    }
    $apiEndpoint = 'https://api.onedrive.com/v1.0/drives/' . $ids[0] . '/items/' . $parentId . ':/' . basename($filePath) . ':/content';
    $headers = [
      'Authorization: Bearer ' . $this->token,
      'Content-Type: application/octet-stream',
      'Content-Length: ' . filesize($filePath),
      'Prefer: respond-async',
      'X-HTTP-Method: PUT'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($filePath));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response);
    return $response;
  }

  public function handleAllFiles($folderWithFile, $actions, $folderId, $parentId)
  {
    foreach ($folderWithFile as $folder => $filePath) {
      if ('' === $filePath) {
        continue;
      }
      if (is_array($filePath)) {
        foreach ($filePath as $singleFilePath) {
          if ('' === $singleFilePath) {
            continue;
          }
          $response = $this->uploadFile($folder, $singleFilePath, $folderId, $parentId);
          $this->storeInState($response);
          $this->deleteFile($singleFilePath, $actions);
        }
      } else {
        $response = $this->uploadFile($folder, $filePath, $folderId, $parentId);
        $this->storeInState($response);
        $this->deleteFile($filePath, $actions);
      }
    }
  }

  protected function storeInState($response)
  {
    $response = is_string($response) ? json_decode($response) : $response;
    if (isset($response->id)) {
      $this->successApiResponse[] = $response;
    } else {
      $this->errorApiResponse[] = $response;
    }
  }

  public function deleteFile($filePath, $actions)
  {
    if (isset($actions->delete_from_wp) && $actions->delete_from_wp) {
      $filePath = $this->makeFilePath($filePath);
      FileHandler::deleteIsFileExists($filePath);
    }
  }

  public function makeFilePath($filePath)
  {
    $upDir = wp_upload_dir();
    return $upDir['basedir'] . '/bitforms/uploads/' . $this->formId . '/' . $this->entryId . '/' . $filePath;
  }

  public function executeRecordApi($integrationId, $logID,  $fieldValues, $fieldMap, $actions, $folderId, $parentId)
  {
    $folderWithFile = [];
    $actionsAttachments = explode(',', "$actions->attachments");

    if (is_array($actionsAttachments)) {
      foreach ($actionsAttachments as $actionAttachment) {
        foreach ($fieldValues[$actionAttachment] as $value) {
          $folderWithFile[] = ["$actionAttachment" => $value];
        }
        $this->handleAllFiles($folderWithFile, $actions, $folderId, $parentId);
      }
    }

    if (count($this->errorApiResponse) > 0) {
      $this->logResponse->apiResponse($logID, $integrationId, ['type' =>  'record', 'type_name' => 'insert'], 'error', 'Some Files Can\'t Upload For Some Reason. ' . wp_json_encode($this->errorApiResponse));
    }
    if (count($this->successApiResponse) > 0) {
      $this->logResponse->apiResponse($logID, $integrationId, ['type' =>  'record', 'type_name' => 'insert'], 'success', 'All Files Uploaded. ' . wp_json_encode($this->successApiResponse));
    }
  }
}
