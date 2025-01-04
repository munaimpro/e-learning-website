<?php

/**
 * ZohoCrm Record Api
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoCRM;

use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\DateTimeHelper;
use BitCode\BitForm\Core\Util\HttpHelper;
use WP_Error;

/**
 * Provide functionality for Record insert,upsert
 */
class RecordApiHelper
{
  protected $_defaultHeader;
  protected $_apiDomain;
  protected $_tokenDetails;
  protected $_logResponse;

  public function __construct($tokenDetails)
  {
    $this->_defaultHeader['Authorization'] = "Zoho-oauthtoken {$tokenDetails->access_token}";
    $this->_apiDomain = \urldecode($tokenDetails->api_domain);
    $this->_tokenDetails = $tokenDetails;
    $this->_logResponse = new UtilApiResponse();
  }

  public function serachRecord($module, $searchCriteria)
  {
    $searchRecordEndpoint = "{$this->_apiDomain}/crm/v2/{$module}/search";
    return HttpHelper::get($searchRecordEndpoint, ['criteria' => "({$searchCriteria})"], $this->_defaultHeader);
  }

  public function upsertRecord($module, $data)
  {
    $insertRecordEndpoint = "{$this->_apiDomain}/crm/v2/{$module}/upsert";
    $data = \is_string($data) ? $data : wp_json_encode($data);
    return HttpHelper::post($insertRecordEndpoint, $data, $this->_defaultHeader);
  }

  public function insertRecord($module, $data)
  {
    $insertRecordEndpoint = "{$this->_apiDomain}/crm/v2/{$module}";
    $data = \is_string($data) ? $data : wp_json_encode($data);
    return HttpHelper::post($insertRecordEndpoint, $data, $this->_defaultHeader);
  }

  public function executeRecordApi($formID, $entryID, $integId, $logID, $defaultConf, $module, $layout, $fieldValues, $fieldMap, $actions, $required, $fileMap = [], $isRelated = false)
  {
    $fieldData = [];
    $filesApiHelper = new FilesApiHelper($this->_tokenDetails, $formID, $entryID, $integId, $logID);
    foreach ($fieldMap as $fieldKey => $fieldPair) {
      if (!empty($fieldPair->zohoFormField)) {
        if (empty($defaultConf->layouts->{$module}->{$layout}->fields->{$fieldPair->zohoFormField})) {
          continue;
        }
        if ('custom' === $fieldPair->formField && isset($fieldPair->customValue)) {
          $fieldData[$fieldPair->zohoFormField] = $this->formatFieldValue($fieldPair->customValue, $defaultConf->layouts->{$module}->{$layout}->fields->{$fieldPair->zohoFormField});
        } else {
          $fieldData[$fieldPair->zohoFormField] = $this->formatFieldValue($fieldValues[$fieldPair->formField], $defaultConf->layouts->{$module}->{$layout}->fields->{$fieldPair->zohoFormField});
        }
        if (empty($fieldData[$fieldPair->zohoFormField]) && \in_array($fieldPair->zohoFormField, $required)) {
          $error = new WP_Error('REQ_FIELD_EMPTY', wp_sprintf(__('%s is required for zoho crm, %s module', 'bit-form'), $fieldPair->zohoFormField, $module));
          $this->_logResponse->apiResponse($logID, $integId, ['type' => 'record', 'type_name' => 'field'], 'validation', $error);
          return $error;
        }

        if (!empty($fieldData[$fieldPair->zohoFormField])) {
          $requiredLength = $defaultConf->layouts->{$module}->{$layout}->fields->{$fieldPair->zohoFormField}->length;
          $currentLength = is_array($fieldData[$fieldPair->zohoFormField]) || is_object($fieldData[$fieldPair->zohoFormField]) ?
              @count($fieldData[$fieldPair->zohoFormField])
              : strlen($fieldData[$fieldPair->zohoFormField]);
          if ($currentLength > $requiredLength) {
            $error = new WP_Error('REQ_FIELD_LENGTH_EXCEEDED', wp_sprintf(__('zoho crm field %s\'s maximum length is %s, Given %s', 'bit-form'), $fieldPair->zohoFormField, $module));
            $this->_logResponse->apiResponse($logID, $integId, ['type' => 'record', 'type_name' => 'field'], 'validation', $error);
            return $error;
          }
        }
      }
    }

    if (count($fileMap)) {
      foreach ($fileMap as $fileKey => $filePair) {
        if (!empty($filePair->zohoFormField)) {
          if ('fileupload' === $defaultConf->layouts->{$module}->{$layout}->fileUploadFields->{$filePair->zohoFormField}->data_type && !empty($fieldValues[$filePair->formField])) {
            $files = $fieldValues[$filePair->formField];
            $fileLength = $defaultConf->layouts->{$module}->{$layout}->fileUploadFields->{$filePair->zohoFormField}->length;
            if (\is_array($files) && count($files) !== $fileLength) {
              $files = array_slice($fieldValues[$filePair->formField], 0, $fileLength);
            }
            $uploadsIDs = $filesApiHelper->uploadFiles($files);
            if ($uploadsIDs) {
              $fieldData[$filePair->zohoFormField] = $uploadsIDs;
            }
          }
        }
      }
    }
    if (!empty($defaultConf->layouts->{$module}->{$layout}->id)) {
      $fieldData['Layout']['id'] = $defaultConf->layouts->{$module}->{$layout}->id;
    }
    if (!empty($actions->gclid) && isset($fieldValues['GCLID'])) {
      $fieldData['$gclid'] = $fieldValues['GCLID'];
    }
    if (!empty($actions->rec_owner)) {
      $fieldData['Owner']['id'] = $actions->rec_owner;
    }
    $requestParams['data'][] = (object) $fieldData;
    $requestParams['trigger'] = [];
    if (!empty($actions->workflow)) {
      $requestParams['trigger'][] = 'workflow';
    }
    if (!empty($actions->approval)) {
      $requestParams['trigger'][] = 'approval';
    }
    if (!empty($actions->blueprint)) {
      $requestParams['trigger'][] = 'blueprint';
    }
    if (!empty($actions->assignment_rules)) {
      $requestParams['lar_id'] = $actions->assignment_rules;
    }
    $recordApiResponse = '';
    if (!empty($actions->upsert) && !empty($actions->upsert->crmField)) {
      $requestParams['duplicate_check_fields'] = [];
      if (!empty($actions->upsert)) {
        $duplicateCheckFields = [];
        $searchCriteria = '';
        foreach ($actions->upsert->crmField as $fieldInfo) {
          if (!empty($fieldInfo->name) && $fieldData[$fieldInfo->name]) {
            $duplicateCheckFields[] = $fieldInfo->name;
            if (empty($searchCriteria)) {
              $searchCriteria .= "({$fieldInfo->name}:equals:{$fieldData[$fieldInfo->name]})";
            } else {
              $searchCriteria .= "and({$fieldInfo->name}:equals:{$fieldData[$fieldInfo->name]})";
            }
          }
        }
        if (isset($actions->upsert->overwrite) && !$actions->upsert->overwrite && !empty($searchCriteria)) {
          $searchRecordApiResponse = $this->serachRecord($module, $searchCriteria);
          if (!empty($searchRecordApiResponse) && !empty($searchRecordApiResponse->data)) {
            $previousData = $searchRecordApiResponse->data[0];
            foreach ($fieldData as $apiName => $currentValue) {
              if (!empty($previousData->{$apiName})) {
                $fieldData[$apiName] = $previousData->{$apiName};
              }
            }
            $requestParams['data'][] = (object) $fieldData;
          }
        }
        $requestParams['duplicate_check_fields'] = $duplicateCheckFields;
      }
      $recordApiResponse = $this->upsertRecord($module, (object) $requestParams);
    } elseif ($isRelated) {
      $recordApiResponse = $this->insertRecord($module, (object) $requestParams);
    } else {
      $recordApiResponse = $this->upsertRecord($module, (object) $requestParams);
    }
    if (isset($recordApiResponse->status) && 'error' === $recordApiResponse->status) {
      $this->_logResponse->apiResponse($logID, $integId, ['type' => 'record', 'type_name' => $module], 'error', $recordApiResponse);
    } else {
      $this->_logResponse->apiResponse($logID, $integId, ['type' => 'record', 'type_name' => $module], 'success', $recordApiResponse);
    }
    if (
      !empty($recordApiResponse->data)
      && !empty($recordApiResponse->data[0]->code)
      && 'SUCCESS' === $recordApiResponse->data[0]->code
      && !empty($recordApiResponse->data[0]->details->id)
    ) {
      if (!empty($actions->tag_rec)) {
        $tags = '';
        $tag_rec = \explode(',', $actions->tag_rec);
        foreach ($tag_rec as $tag) {
          if (is_string($tag) && '${' === substr($tag, 0, 2) && '}' === $tag[strlen($tag) - 1]) {
            $tags .= (!empty($tags) ? ',' : '') . $fieldValues[substr($tag, 2, strlen($tag) - 3)];
          } else {
            $tags .= (!empty($tags) ? ',' : '') . $tag;
          }
        }
        $tagApiHelper = new TagApiHelper($this->_tokenDetails, $module);
        $addTagResponse = $tagApiHelper->addTagsSingleRecord($recordApiResponse->data[0]->details->id, $tags);
        if (isset($addTagResponse->status) && 'error' === $addTagResponse->status) {
          $this->_logResponse->apiResponse($logID, $integId, ['type' => 'tag', 'type_name' => $module], 'error', $addTagResponse);
        } else {
          $this->_logResponse->apiResponse($logID, $integId, ['type' => 'tag', 'type_name' => $module], 'success', $addTagResponse);
        }
      }
      if (!empty($actions->attachment)) {
        $validAttachments = [];
        $fileFound = 0;
        $responseType = 'success';
        $attachmentApiResponses = [];
        $attachment = explode(',', $actions->attachment);
        foreach ($attachment as $fileField) {
          if (isset($fieldValues[$fileField]) && !empty($fieldValues[$fileField])) {
            $fileFound = 1;
            if (is_array($fieldValues[$fileField])) {
              foreach ($fieldValues[$fileField] as $singleFile) {
                $attachmentApiResponse = $filesApiHelper->uploadFiles($singleFile, true, $module, $recordApiResponse->data[0]->details->id);
                if (isset($attachmentApiResponse->status) && 'error' === $attachmentApiResponse->status) {
                  $responseType = 'error';
                }
              }
            } else {
              $attachmentApiResponse = $filesApiHelper->uploadFiles($fieldValues[$fileField], true, $module, $recordApiResponse->data[0]->details->id);
              if (isset($attachmentApiResponse->status) && 'error' === $attachmentApiResponse->status) {
                $responseType = 'error';
              }
            }
          }
        }
        if ($fileFound) {
          $this->_logResponse->apiResponse($logID, $integId, ['type' => 'attachment', 'type_name' => $module], $responseType, $attachmentApiResponses);
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

    switch ($formatSpecs->json_type) {
      case 'jsonarray':
        $apiFormat = 'array';
        break;
      case 'jsonobject':
        $apiFormat = 'object';
        break;

      default:
        $apiFormat = $formatSpecs->json_type;
        break;
    }

    $formatedValue = '';
    $fieldFormat = gettype($value);
    if ($fieldFormat === $apiFormat && 'datetime' !== $formatSpecs->data_type) {
      $formatedValue = $value;
    } else {
      if ('array' === $apiFormat || 'object' === $apiFormat) {
        if ('string' === $fieldFormat) {
          if (-1 === strpos($value, ',')) {
            $formatedValue = json_decode($value);
          } else {
            $formatedValue = explode(',', $value);
          }
          $formatedValue = is_null($formatedValue) && !is_null($value) ? [$value] : $formatedValue;
        } else {
          $formatedValue = $value;
        }

        if ('object' === $apiFormat) {
          $formatedValue = (object) $formatedValue;
        }
      } elseif ('string' === $apiFormat && 'datetime' !== $formatSpecs->data_type) {
        $formatedValue = !is_string($value) ? wp_json_encode($value) : $value;
      } elseif ('datetime' === $formatSpecs->data_type) {
        $dateTimeHelper = new DateTimeHelper();
        $formatedValue = $dateTimeHelper->getFormated($value, 'Y-m-d\TH:i', wp_timezone(), 'Y-m-d\TH:i:sP', null);
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
