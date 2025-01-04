<?php

/**
 * ZohoRecruit Record Api
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoRecruit;

use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\DateTimeHelper;
use BitCode\BitForm\Core\Util\FieldValueHandler;
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
  private $_dataCenter;
  private $_integrationID;
  private $_logID;
  private $_logResponse;

  public function __construct($dataCenter, $tokenDetails, $integId, $logID)
  {
    $this->_defaultHeader['Authorization'] = "Zoho-oauthtoken {$tokenDetails->access_token}";
    $this->_apiDomain = \urldecode($tokenDetails->api_domain);
    $this->_dataCenter = $dataCenter;
    $this->_tokenDetails = $tokenDetails;
    $this->_integrationID = $integId;
    $this->_logID = $logID;
    $this->_logResponse = new UtilApiResponse();
  }

  public function insertRecord($module, $data)
  {
    $insertRecordEndpoint = "https://recruit.zoho.{$this->_dataCenter}/recruit/private/json/{$module}/addRecords";
    return HttpHelper::post($insertRecordEndpoint, $data, $this->_defaultHeader);
  }

  public function executeRecordApi($formID, $entryID, $defaultConf, $module, $fieldValues, $fieldMap, $actions, $required, $fileMap)
  {
    $fieldData = [];
    foreach ($fieldMap as $fieldKey => $fieldPair) {
      if (!empty($fieldPair->zohoFormField)) {
        if ('custom' === $fieldPair->formField && isset($fieldPair->customValue)) {
          $fieldData[$fieldPair->zohoFormField] = $this->formatFieldValue($fieldPair->customValue, $defaultConf->moduleData->{$module}->fields->{$fieldPair->zohoFormField});
        } else {
          $fieldData[$fieldPair->zohoFormField] = $this->formatFieldValue($fieldValues[$fieldPair->formField], $defaultConf->moduleData->{$module}->fields->{$fieldPair->zohoFormField});
          if ('Zip/Postal Code' === $fieldPair->zohoFormField) {
            $fieldData['Zip Code'] = $fieldData[$fieldPair->zohoFormField];
            unset($fieldData[$fieldPair->zohoFormField]);
          }
        }

        if (empty($fieldData[$fieldPair->zohoFormField]) && \in_array($fieldPair->zohoFormField, $required)) {
          $error = new WP_Error('REQ_FIELD_EMPTY', wp_sprintf(__('%s is required for zoho recruit, %s module', 'bit-form'), $fieldPair->zohoFormField, $module));
          $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'field'], 'validation', $error);
          return $error;
        }
      }
    }

    $xmlData = "<$module><row no='1'>";

    foreach ($fieldData as $field => $value) {
      $xmlData .= "<FL val='$field'>$value</FL>";
    }

    if (!empty($actions->recordOwner)) {
      $xmlData .= "<FL val='SMOWNERID'>$actions->recordOwner</FL>";
    }
    $xmlData .= "</row></$module>";
    $requestParams['scope'] = 'ZohoRecruit.modules.all';
    $requestParams['version'] = 4;

    $requestParams['xmlData'] = $xmlData;
    if (!empty($actions->workflow)) {
      $requestParams['wfTrigger'] = 'true'; //api accept string true | false
    }
    if (!empty($actions->upsert)) {
      $requestParams['duplicateCheck'] = 2;
    }
    if (!empty($actions->approval)) {
      $requestParams['isApproval'] = 'true'; //api accept string true | false
    }

    $recordApiResponse = $this->insertRecord($module, $requestParams);
    if (isset($recordApiResponse->response->error)) {
      return $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => $module], 'error', $recordApiResponse);
    } else {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => $module], 'success', $recordApiResponse);
    }

    if (isset($recordApiResponse->response->error)) {
      return new WP_Error('INSERT_ERROR', $recordApiResponse->response->error->message);
    }
    if (!empty($recordApiResponse->response->result->row->success->details->FL[0]) && 'Id' === $recordApiResponse->response->result->row->success->details->FL[0]->val) {
      $recordID = $recordApiResponse->response->result->row->success->details->FL[0]->content;
      if (isset($actions->note) && !empty($actions->note->type)) {
        $noteDetails = $actions->note;
        $typeDetails = explode('__', $noteDetails->type);
        $content = FieldValueHandler::replaceFieldWithValue($noteDetails->content, $fieldValues);
        $xmlData = "<Notes><row no='1'>";
        $xmlData .= "<FL val='entityId'>$recordID</FL>";
        $xmlData .= "<FL val='Note Type'>$typeDetails[1]</FL>";
        $xmlData .= "<FL val='Type Id'>$typeDetails[0]</FL>";
        $xmlData .= "<FL val='Note Content'>$content</FL>";
        $xmlData .= "<FL val='Parent Module'>$module</FL>";
        $xmlData .= '</row></Notes>';
        $requestParams['version'] = 2;
        $requestParams['xmlData'] = $xmlData;

        $noteApiResponse = $this->insertRecord('Notes', $requestParams);
        if (isset($noteApiResponse->response->error)) {
          $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'note', 'type_name' => $module], 'error', $noteApiResponse);
        } else {
          $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'note', 'type_name' => $module], 'success', $noteApiResponse);
        }
      }

      $filesApiHelper = new FilesApiHelper($module, $this->_dataCenter, $this->_tokenDetails, $formID, $entryID);
      if (count($fileMap)) {
        $fileFound = 0;
        $responseType = 'success';
        $fileUpResponses = [];
        foreach ($fileMap as $fileKey => $filePair) {
          if (!empty($filePair->zohoFormField) && !empty($fieldValues[$filePair->formField])) {
            $fileFound = 1;
            if (@property_exists($defaultConf->moduleData->{$module}->fileUploadFields, $filePair->zohoFormField) && 'UploadText' === $defaultConf->moduleData->{$module}->fileUploadFields->{$filePair->zohoFormField}->data_type) {
              $fileUpResponse = $filesApiHelper->uploadFiles($fieldValues[$filePair->formField], $recordID, $filePair->zohoFormField);
              if (isset($fileUpResponse->response->error)) {
                $responseType = 'error';
              }
              $fileUpResponses[] = $fileUpResponse;
            }
          }
        }
        if ($fileFound) {
          $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'file', 'type_name' => $module], $responseType, $fileUpResponses);
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
      case 'Picklist':
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
