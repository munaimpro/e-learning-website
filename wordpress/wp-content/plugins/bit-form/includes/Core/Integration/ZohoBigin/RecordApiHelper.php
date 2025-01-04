<?php

/**
 * ZohoBigin Record Api
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoBigin;

use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\DateTimeHelper;
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

  private $_integID;

  private $_logID;

  private $_logResponse;

  public function __construct($tokenDetails, $integID, $logID)
  {
    $this->_defaultHeader['Authorization'] = "Zoho-oauthtoken {$tokenDetails->access_token}";
    $this->_apiDomain = \urldecode($tokenDetails->api_domain);
    $this->_tokenDetails = $tokenDetails;
    $this->_integID = $integID;
    $this->_logID = $logID;
    $this->_logResponse = new UtilApiResponse();
  }

  public function insertRecord($module, $data)
  {
    $insertRecordEndpoint = "https://www.zohoapis.com/bigin/v1/{$module}";
    return HttpHelper::post($insertRecordEndpoint, $data, $this->_defaultHeader);
  }

  private function insertNote($module, $recordId, $data)
  {
    $insertRecordEndpoint = "https://www.zohoapis.com/bigin/v1/{$module}/{$recordId}/Notes";
    return HttpHelper::post($insertRecordEndpoint, $data, $this->_defaultHeader);
  }

  public function executeRecordApi($formID, $entryID, $defaultConf, $module, $fieldValues, $fieldMap, $actions, $required)
  {
    $fieldData = [];
    foreach ($fieldMap as $fieldKey => $fieldPair) {
      if (!empty($fieldPair->zohoFormField)) {
        if ('custom' === $fieldPair->formField && isset($fieldPair->customValue)) {
          $fieldData[$fieldPair->zohoFormField] = $this->formatFieldValue($fieldPair->customValue, $defaultConf->moduleData->{$module}->fields->{$fieldPair->zohoFormField});
        } else {
          $fieldData[$fieldPair->zohoFormField] = $this->formatFieldValue($fieldValues[$fieldPair->formField], $defaultConf->moduleData->{$module}->fields->{$fieldPair->zohoFormField});
        }

        if (empty($fieldData[$fieldPair->zohoFormField]) && \in_array($fieldPair->zohoFormField, $required)) {
          $error = new WP_Error('REQ_FIELD_EMPTY', wp_sprintf(__('%s is required for zoho bigin, %s module', 'bit-form'), $fieldPair->zohoFormField, $module));
          $this->_logResponse->apiResponse($this->_logID, $this->_integID, ['type' => 'record', 'type_name' => 'field'], 'validation', $error);
          return $error;
        }
      }
    }

    $requestParams['data'][] = (object) $fieldData;
    $requestParams['trigger'] = [];

    if (!empty($actions->workflow)) {
      $requestParams['trigger'][] = 'workflow';
    }
    if (!empty($actions->approval)) {
      $requestParams['trigger'][] = 'approval';
    }

    $recordApiResponse = $this->insertRecord($module, wp_json_encode($requestParams));
    if ((isset($recordApiResponse->status) && 'error' === $recordApiResponse->status) || 'error' === $recordApiResponse->data[0]->status) {
      return $this->_logResponse->apiResponse($this->_logID, $this->_integID, ['type' => 'record', 'type_name' => $module], 'error', $recordApiResponse);
    } else {
      $this->_logResponse->apiResponse($this->_logID, $this->_integID, ['type' => 'record', 'type_name' => $module], 'success', $recordApiResponse);
    }
    $recordID = 0;
    if (!empty($recordApiResponse->data[0]->details->id)) {
      $recordID = $recordApiResponse->data[0]->details->id;
      if (isset($actions->note)) {
        $note_title = $actions->note->title ? $actions->note->title : '';
        $note_content = $actions->note->content ? $actions->note->content : '';
        $note = (object) [
          'Note_Title'   => $note_title,
          'Note_Content' => $note_content,
          'Parent_Id'    => $recordID,
          'se_module'    => $module
        ];
        $requestParams['data'][] = $note;

        $noteApiResponse = $this->insertNote($module, $recordID, wp_json_encode($requestParams));
        if (isset($noteApiResponse->status) && 'error' === $noteApiResponse->status) {
          $this->_logResponse->apiResponse($this->_logID, $this->_integID, ['type' => 'note', 'type_name' => $module], 'error', $noteApiResponse);
        } else {
          $this->_logResponse->apiResponse($this->_logID, $this->_integID, ['type' => 'note', 'type_name' => $module], 'success', $noteApiResponse);
        }
      }
    }

    // Attachments
    if (isset($actions->attachments)) {
      $filesApiHelper = new FilesApiHelper($this->_tokenDetails, $formID, $entryID);
      $attachments = explode(',', $actions->attachments);
      $fileFound = 0;
      $responseType = 'success';
      $attachmentApiResponses = [];
      foreach ($attachments as $fileField) {
        if (isset($fieldValues[$fileField]) && !empty($fieldValues[$fileField])) {
          $fileFound = 1;
          if (is_array($fieldValues[$fileField])) {
            foreach ($fieldValues[$fileField] as $singleFile) {
              $attachmentApiResponse = $filesApiHelper->uploadFiles($singleFile, $module, $recordID);
              if (isset($attachmentApiResponse->status) && 'error' === $attachmentApiResponse->status) {
                $responseType = 'error';
              }
              $attachmentApiResponses[] = $attachmentApiResponse;
            }
          } else {
            $attachmentApiResponse = $filesApiHelper->uploadFiles($fieldValues[$fileField], $module, $recordID);
            if (isset($attachmentApiResponse->status) && 'error' === $attachmentApiResponse->status) {
              $responseType = 'error';
            }
            $attachmentApiResponses[] = $attachmentApiResponse;
          }
        }
      }
      if ($fileFound) {
        $this->_logResponse->apiResponse($this->_logID, $this->_integID, ['type' => 'file', 'type_name' => $module], $responseType, $attachmentApiResponses);
      }
    }

    if (isset($actions->photo)) {
      $filesApiHelper = new FilesApiHelper($this->_tokenDetails, $formID, $entryID);
      if (isset($fieldValues[$actions->photo])) {
        $attachmentApiResponse = $filesApiHelper->uploadFiles($fieldValues[$actions->photo], $module, $recordID, true);
        if (isset($attachmentApiResponse->status) && 'error' === $attachmentApiResponse->status) {
          $this->_logResponse->apiResponse($this->_logID, $this->_integID, ['type' => 'photo', 'type_name' => $module], 'error', $attachmentApiResponse);
        } else {
          $this->_logResponse->apiResponse($this->_logID, $this->_integID, ['type' => 'photo', 'type_name' => $module], 'success', $attachmentApiResponse);
        }
      }
    }

    return $recordApiResponse;
  }

  public function formatFieldValue($value, $formatSpecs)
  {
    if (empty($value)) {
      return '';
    }

    switch ($formatSpecs->data_type) {
      case 'AutoNumber':
        $apiFormat = 'integer';
        break;

      case 'Text':
      case 'Pick list':
      case 'Lookup':
      case 'Email':
      case 'Website':
      case 'Currency':
      case 'TextArea':
        $apiFormat = 'string';
        break;

      case 'Date':
        $apiFormat = 'date';
        break;

      case 'DateTime':
        $apiFormat = 'datetime';
        break;

      case 'Double':
        $apiFormat = 'double';
        break;

      case 'Boolean':
        $apiFormat = 'boolean';
        break;

      default:
        $apiFormat = $formatSpecs->data_type;
        break;
    }

    $formatedValue = '';
    $fieldFormat = gettype($value);
    if ($fieldFormat === $apiFormat && 'datetime' !== $formatSpecs->data_type) {
      $formatedValue = $value;
    } else {
      if ('string' === $apiFormat && 'datetime' !== $formatSpecs->data_type) {
        $formatedValue = !is_string($value) ? wp_json_encode($value) : $value;
      } elseif ('datetime' === $apiFormat) {
        $dateTimeHelper = new DateTimeHelper();
        $formatedValue = $dateTimeHelper->getFormated($value, 'Y-m-d\TH:i', DateTimeHelper::wp_timezone(), 'Y-m-d H:i:s', null);
      } elseif ('date' === $apiFormat) {
        $dateTimeHelper = new DateTimeHelper();
        $formatedValue = $dateTimeHelper->getFormated($value, 'Y-m-d', DateTimeHelper::wp_timezone(), 'm/d/Y', null);
      } else {
        $stringyfiedValue = !is_string($value) ? wp_json_encode($value) : $value;

        switch ($apiFormat) {
          case 'double':
            $formatedValue = (float) $stringyfiedValue;
            break;

          case 'boolean':
            $formatedValue = (bool) $stringyfiedValue;
            break;

          case 'integer':
            $formatedValue = (int) $stringyfiedValue;
            break;

          default:
            $formatedValue = $stringyfiedValue;
            break;
        }
      }
    }
    $formatedValueLenght = 'array' === $apiFormat || 'object' === $apiFormat ? (is_countable($formatedValue) ? \count($formatedValue) : @count($formatedValue)) : \strlen($formatedValue);
    if ($formatedValueLenght > $formatSpecs->length) {
      $formatedValue = 'array' === $apiFormat || 'object' === $apiFormat ? array_slice($formatedValue, 0, $formatSpecs->length) : substr($formatedValue, 0, $formatSpecs->length);
    }

    return $formatedValue;
  }
}
