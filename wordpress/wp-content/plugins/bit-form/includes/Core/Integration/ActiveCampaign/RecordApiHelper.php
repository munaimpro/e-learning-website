<?php

/**
 * Active Campaign Record Api
 *
 */

namespace BitCode\BitForm\Core\Integration\ActiveCampaign;

use BitCode\BitForm\Core\Database\FormEntryLogModel;
use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\HttpHelper;

/**
 * Provide functionality for Record insert,update, exist
 */
class RecordApiHelper
{
  private $_defaultHeader;
  private $_integrationID;
  private $_logID;
  private $_logResponse;
  private $_entryID;
  private $_apiEndpoint;

  public function __construct($api_key, $api_url, $integId, $logID, $entryID)
  {
    // wp_send_json_success($tokenDetails);
    $this->_defaultHeader['Api-Token'] = $api_key;
    $this->_apiEndpoint = $api_url . '/api/3';
    $this->_integrationID = $integId;
    $this->_logID = $logID;
    $this->_logResponse = new UtilApiResponse();
    $this->_entryID = $entryID;
  }

  // for insert data
  public function storeOrModifyRecord($method, $data)
  {
    $insertRecordEndpoint = "{$this->_apiEndpoint}/{$method}";
    return HttpHelper::post($insertRecordEndpoint, $data, $this->_defaultHeader);
  }

  public function updateRecord($id, $data)
  {
    $updateRecordEndpoint = "{$this->_apiEndpoint}/contacts/{$id}";
    return HttpHelper::request($updateRecordEndpoint, 'PUT', $data, $this->_defaultHeader);
  }

  private function existContact($email)
  {
    $searchEndPoint = "{$this->_apiEndpoint}/contacts?email={$email}";
    return HttpHelper::get($searchEndPoint, null, $this->_defaultHeader);
  }

  public function executeRecordApi($fieldValues, $fieldMap, $actions, $listId, $tags)
  {
    $fieldData = [];
    $customFields = [];

    foreach ($fieldMap as $fieldKey => $fieldPair) {
      if (!empty($fieldPair->activeCampaignField)) {
        if ('custom' === $fieldPair->formField && isset($fieldPair->customValue) && !is_numeric($fieldPair->activeCampaignField)) {
          $fieldData[$fieldPair->activeCampaignField] = $fieldPair->customValue;
        } elseif (is_numeric($fieldPair->activeCampaignField) && 'custom' === $fieldPair->formField && isset($fieldPair->customValue)) {
          array_push($customFields, ['field' => (int) $fieldPair->activeCampaignField, 'value' => $fieldPair->customValue]);
        } elseif (is_numeric($fieldPair->activeCampaignField)) {
          array_push($customFields, ['field' => (int) $fieldPair->activeCampaignField, 'value' => $fieldValues[$fieldPair->formField]]);
        } else {
          $fieldData[$fieldPair->activeCampaignField] = $fieldValues[$fieldPair->formField];
        }
      }
    }

    if (!empty($customFields)) {
      $fieldData['fieldValues'] = $customFields;
    }
    $activeCampaign['contact'] = (object) $fieldData;

    $model = new FormEntryLogModel();

    $data = null;
    $recordApiResponse = null;
    $type = null;
    if ($this->_entryID) {
      $result = $model->entryLogCheck($this->_entryID, $this->_integrationID);
      if (!count($result) || isset($result->errors['result_empty'])) {
        $recordApiResponse = $this->storeOrModifyRecord('contacts', wp_json_encode($activeCampaign));
        $type = 'insert';

        if (isset($recordApiResponse->contact)) {
          $recordApiResponse = ['success' => true, 'id' => $recordApiResponse->contact->id];
          if (isset($listId) && !empty($listId)) {
            $data['contactList'] = (object) [
              'list'    => $listId,
              'contact' => $recordApiResponse['id'],
              'status'  => 1
            ];
            $this->storeOrModifyRecord('contactLists', wp_json_encode($data));
          }
          if (isset($tags) && !empty($tags)) {
            foreach ($tags as $tag) {
              $data['contactTag'] = (object) [
                'contact' => $recordApiResponse['id'],
                'tag'     => $tag
              ];
              $this->storeOrModifyRecord('contactTags', wp_json_encode($data));
            }
          }
        }

        if (
          !empty($actions->update)
          && !empty($recordApiResponse->errors)
          && 'duplicate' === $recordApiResponse->errors[0]->code
        ) {
          $existContact = $this->existContact($activeCampaign['contact']->email);
          $recordApiResponse = $this->updateRecord($existContact->contacts[0]->id, wp_json_encode($activeCampaign));
          if (isset($recordApiResponse->contact)) {
            $recordApiResponse = ['success' => true, 'id' => $recordApiResponse->contact->id];

            if (isset($listId) && !empty($listId)) {
              $data['contactList'] = (object) [
                'list'    => $listId,
                'contact' => $recordApiResponse['id'],
                'status'  => 1
              ];
              $this->storeOrModifyRecord('contactLists', wp_json_encode($data));
            }
            if (isset($tags) && !empty($tags)) {
              foreach ($tags as $tag) {
                $data['contactTag'] = (object) [
                  'contact' => $recordApiResponse['id'],
                  'tag'     => $tag
                ];
                $this->storeOrModifyRecord('contactTags', wp_json_encode($data));
              }
            }
          }
          $type = 'update';
        }
      } else {
        $contactId = json_decode($result[0]->response_obj);
        $recordApiResponse = $this->updateRecord($contactId->id, wp_json_encode($activeCampaign));
        if (isset($recordApiResponse->contact)) {
          $recordApiResponse = ['success' => true, 'id' => $recordApiResponse->contact->id];

          if (isset($listId) && !empty($listId)) {
            $data['contactList'] = (object) [
              'list'    => $listId,
              'contact' => $recordApiResponse['id'],
              'status'  => 1
            ];
            $this->storeOrModifyRecord('contactLists', wp_json_encode($data));
          }
          if (isset($tags) && !empty($tags)) {
            foreach ($tags as $tag) {
              $data['contactTag'] = (object) [
                'contact' => $recordApiResponse['id'],
                'tag'     => $tag
              ];
              $this->storeOrModifyRecord('contactTags', wp_json_encode($data));
            }
          }
        }
        $type = 'update';
      }
    }

    if ($recordApiResponse && isset($recordApiResponse->errors)) {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' =>  'record', 'type_name' => $type], 'error', $recordApiResponse->errors);
    } else {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' =>  'record', 'type_name' => $type], 'success', $recordApiResponse);
    }
    return $recordApiResponse;
  }
}
