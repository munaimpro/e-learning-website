<?php

/**
 * Autonami Record Api
 *
 */

namespace BitCode\BitForm\Core\Integration\Autonami;

use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BWF_Contacts;
use BWFCRM_Contact;

/**
 * Provide functionality for Record insert
 */
class RecordApiHelper
{
  private $_integrationID;
  private $_logID;
  private $_logResponse;

  public function __construct($integId, $logID, $entryID)
  {
    $this->_integrationID = $integId;
    $this->_logID = $logID;
    $this->_logResponse = new UtilApiResponse();
  }

  public function insertRecord($data, $actions)
  {
    $contact_obj = BWF_Contacts::get_instance();
    $contact = $contact_obj->get_contact_by('email', $data['email']);
    $userExist = (absint($contact->get_id()) > 0);

    if ($userExist && isset($actions->skip_if_exists) && $actions->skip_if_exists) {
      $response = ['success' => false, 'messages' => 'Contact already exists!'];
    } else {
      foreach ($data as $key => $item) {
        $obj = 'set_' . $key;
        $contact->$obj($item);
      }
      $contact->set_status(1);
      $contact->save();

      $customContact = new BWFCRM_Contact($data['email']);
      foreach ($data as $key => $item) {
        if ('address' === $key) {
          $key = 'address-1';
        }
        $customContact->set_field_by_slug($key, $item);
      }
      $customContact->save_fields();

      if (absint($contact->get_id()) > 0) {
        $response = ['success' => true, 'messages' => 'Insert successfully!'];
      } else {
        $response = ['success' => false, 'messages' => 'Something wrong!'];
      }
    }
    return $response;
  }

  public function executeRecordApi($fieldValues, $fieldMap, $actions, $lists, $tags)
  {
    $fieldData = [];
    foreach ($fieldMap as $fieldPair) {
      if (!empty($fieldPair->autonamiField)) {
        if ('custom' === $fieldPair->formField && isset($fieldPair->customValue)) {
          $fieldData[$fieldPair->autonamiField] = $fieldPair->customValue;
        } else {
          $fieldData[$fieldPair->autonamiField] = is_array($fieldValues[$fieldPair->formField]) ? wp_json_encode($fieldValues[$fieldPair->formField]) : $fieldValues[$fieldPair->formField];
        }
      }
    }
    $fieldData['lists'] = $lists;
    $fieldData['tags'] = $tags;

    $recordApiResponse = $this->insertRecord($fieldData, $actions);

    if ($recordApiResponse['success']) {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' =>  'record', 'type_name' => 'insert'], 'success', $recordApiResponse);
    } else {
      $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' =>  'record', 'type_name' => 'insert'], 'error', $recordApiResponse);
    }
    return $recordApiResponse;
  }
}
