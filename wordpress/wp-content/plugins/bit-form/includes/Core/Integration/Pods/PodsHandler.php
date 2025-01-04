<?php

/**
 * Pod Integration
 *
 */

namespace BitCode\BitForm\Core\Integration\Pods;

use BitCode\BitForm\Core\Form\FormManager;
use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\SmartTags;
use BitCode\BitForm\Core\Util\WpFileHandler;

/**
 * Provide functionality for POD integration
 */
class PodsHandler
{
  private $_formID;
  private $_wpdb;

  public const BIT_FORMS_FILE_TYPE = ['file-up', 'advanced-file-up', 'signature'];

  public function __construct($integrationID, $fromID)
  {
    $this->_formID = $fromID;

    global $wpdb;
    $this->_wpdb = $wpdb;
  }

  /**
   * Helps to register ajax function's with wp
   *
   * @return null
   */
  private function smartTagMappingValue($fieldMap)
  {
    $specialTagFieldValue = [];
    $data = SmartTags::getPostUserData(true);
    $specialTagFields = SmartTags::smartTagFieldKeys();

    foreach ($fieldMap as $value) {
      if (isset($value->formField)) {
        $triggerValue = $value->formField;
        if (in_array($triggerValue, $specialTagFields)) {
          $specialTagFieldValue[$value->formField] = SmartTags::getSmartTagValue($triggerValue, $data);
        }
      }
    }
    return $specialTagFieldValue;
  }

  private function podMappingField($integrationDetails, $podField, $fieldValues, $entryID, $postID)
  {
    $string_types = [
      'text',
      'textarea',
      'radio',
      'number',
      'oembed',
      'password',
      'email',
      'pick',
      'paragraph',
      'wysiwyg',
      'code',
      'check',
      'color',
      'website',
      'date',
      'time',
      'datetime',
      'datetime-local',
      'url',
      'currency',
    ];

    $fieldValues = IntegrationHandler::formattedRepeaterValue($fieldValues);

    $fileUploadHandle = new WpFileHandler($this->_formID);
    $podData = [];
    foreach ($integrationDetails->pod_map as $fieldPair) {
      if (!empty($fieldPair->podFormField) && !empty($fieldPair->formField)) {
        $pod = $podField[$fieldPair->podFormField];
        if (in_array($pod['type'], $string_types) && !empty($fieldValues[$fieldPair->formField])) {
          if (1 === $pod['is_repeatable'] && is_array($fieldValues[$fieldPair->formField])) {
            // $repeaterValues = call_user_func_array('array_merge', array_map('array_values', $fieldValues[$fieldPair->formField]));
            $podData[$fieldPair->podFormField] = $fieldValues[$fieldPair->formField];
          } else {
            $podData[$fieldPair->podFormField] = $fieldValues[$fieldPair->formField];
          }
        }
        // elseif (isset($field['mul'], $field) && in_array($pod['type'], $string_types) && 'file-up' !== $field['type']) {
        //   if (true === $field['mul'] && 'multi' === $pod['pick_format_type'] && !empty($fieldValues[$fieldPair->formField])) {
        //     $podData[$fieldPair->podFormField] = explode(',', $fieldValues[$fieldPair->formField]);
        //   } elseif (false === $field['mul'] && 'multi' === $pod['pick_format_type'] && !empty($fieldValues[$fieldPair->formField])) {
        //     $podData[$fieldPair->podFormField] = $fieldValues[$fieldPair->formField];
        //   }
        // }
        elseif ('file' === $pod['type']) {
          if (!empty($fieldValues[$fieldPair->formField])) {
            if ('multi' === $pod['file_format_type']) {
              $attachmentId = $fileUploadHandle->multiFileMoveWpMedia($entryID, $fieldValues[$fieldPair->formField], $postID);
              if (!empty($attachmentId)) {
                update_post_meta($postID, $fieldPair->podFormField, $attachmentId);
                update_post_meta($postID, '_pods_' . $fieldPair->podFormField, wp_json_encode($attachmentId));
              }
            } elseif ('single' === $pod['file_format_type']) {
              $attachmentData = [];
              if (!is_array($fieldValues[$fieldPair->formField])) {
                $fileArr = explode('/', $fieldValues[$fieldPair->formField]);
                $attachmentData[] = $fileArr[count($fileArr) - 1];
              } else {
                $attachmentData = $fieldValues[$fieldPair->formField];
              }
              $attachmentId = $fileUploadHandle->singleFileMoveWpMedia($entryID, $attachmentData, $postID);
              if (!empty($attachmentId)) {
                update_post_meta($postID, $fieldPair->podFormField, $attachmentId);
                update_post_meta($postID, '_pods_' . $fieldPair->podFormField, wp_json_encode($attachmentId));
              }
            }
          }
        }
      }
    }
    return $podData;
  }

  // formatted field values for repeater field

  private function postFieldMapping($fieldData, $post_map, $formFields, $fieldValues, $postID, $entryID)
  {
    $uploadFeatureImg = new WpFileHandler($this->_formID);
    foreach ($post_map as $fieldPair) {
      foreach ($formFields as $field) {
        if (!empty($fieldPair->postFormField) && !empty($fieldPair->formField)) {
          if ($fieldPair->formField === $field['key'] && '_thumbnail_id' !== $fieldPair->postFormField) {
            $fieldData[$fieldPair->postFormField] = $fieldValues[$fieldPair->formField];
          } elseif ($fieldPair->formField === $field['key'] && in_array($field['type'], self::BIT_FORMS_FILE_TYPE) && '_thumbnail_id' === $fieldPair->postFormField && !empty($fieldValues[$field['key']])) {
            if (!empty($fieldValues[$field['key']])) {
              $uploadFeatureImg->uploadFeatureImg($fieldValues[$field['key']], $entryID, $postID);
            }
          }
        }
      }
    }
    return $fieldData;
  }

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = is_string($integrationData->integration_details) ? json_decode($integrationData->integration_details) : $integrationData->integration_details;
    $fieldData = [];
    $taxonomy = new WpFileHandler($integrationData->form_id);

    $formManger = new FormManager($integrationData->form_id);
    $formFields = $formManger->getFields();
    $allFields = pods($integrationDetails->post_type);
    $podField = [];

    foreach ($allFields->fields as $key => $field) {
      $podField[$key]['type'] = $field['type'];
      // $podField[$key]['pick_format_type'] = $field['options']['pick_format_type'];
      // $podField[$key]['file_format_type'] = $field['options']['file_format_type'];
      $podField[$key]['pick_format_type'] = $field['pick_format_type'];
      $podField[$key]['file_format_type'] = $field['file_format_type'];
      $podField[$key]['is_repeatable'] = (int) $field['repeatable'];
    }

    $fieldData['comment_status'] = isset($integrationDetails->comment_status) ? $integrationDetails->comment_status : '';
    $fieldData['post_status'] = isset($integrationDetails->post_status) ? $integrationDetails->post_status : '';
    $fieldData['post_type'] = isset($integrationDetails->post_type) ? $integrationDetails->post_type : '';

    if (isset($integrationDetails->post_author) && 'logged_in_user' !== $integrationDetails->post_author) {
      $fieldData['post_author'] = $integrationDetails->post_author;
    } else {
      $fieldData['post_author'] = get_current_user_id();
    }

    $exist_id = $fieldData['post_type'] . '_' . $entryID;
    $sql = "SELECT * FROM `{$this->_wpdb->prefix}bitforms_form_entrymeta` WHERE `meta_key`='$exist_id' ";
    $exist_post_id = $this->_wpdb->get_results($sql);
    $taxanomyData = $taxonomy->taxonomyData($formFields, $fieldValues);

    if ([] === $exist_post_id) {
      $post_id = wp_insert_post(['post_title' => 'null', 'post_content' => 'null']);
      $smartTagValue = $this->smartTagMappingValue($integrationDetails->post_map);
      $updatedValues = $fieldValues + $smartTagValue;
      $updateData = $this->postFieldMapping($fieldData, $integrationDetails->post_map, $formFields, $updatedValues, $post_id, $entryID);
      $updateData['ID'] = $post_id;
      unset($updateData['_thumbnail_id']);
      wp_update_post($updateData, true);

      if (!empty($taxanomyData)) {
        foreach ($taxanomyData as $taxanomy) {
          wp_set_post_terms($post_id, $taxanomy['value'], $taxanomy['term'], false);
        }
      }

      $this->_wpdb->insert(
        "{$this->_wpdb->prefix}bitforms_form_entrymeta",
        [
          'meta_key'               => $fieldData['post_type'] . '_' . $entryID,
          'meta_value'             => $post_id,
          'bitforms_form_entry_id' => $entryID,
        ]
      );

      $smartTagValue = $this->smartTagMappingValue($integrationDetails->pod_map);
      $updatedPodValues = $fieldValues + $smartTagValue;
      $podData = $this->podMappingField($integrationDetails, $podField, $updatedPodValues, $entryID, $post_id);
      foreach ($podData as $key => $data) {
        add_post_meta($post_id, $key, $data);
      }
    } else {
      if (!empty($taxanomyData)) {
        foreach ($taxanomyData as $taxanomy) {
          wp_set_post_terms($exist_post_id[0]->meta_value, $taxanomy['value'], $taxanomy['term'], false);
        }
      }

      $podData = $this->podMappingField($integrationDetails, $podField, $fieldValues, $entryID, $exist_post_id[0]->meta_value);

      foreach ($podData as $key => $data) {
        if (is_array($data)) {
          $count = count($data);
          for ($i = 0; $i < $count; $i++) {
            delete_post_meta($exist_post_id[0]->meta_value, $key);
          }
          add_post_meta($exist_post_id[0]->meta_value, $key, $data);
        } else {
          update_post_meta($exist_post_id[0]->meta_value, $key, $data);
        }
      }

      $updateData = $this->postFieldMapping($fieldData, $integrationDetails->post_map, $formFields, $fieldValues, $exist_post_id[0]->meta_value, $entryID);
      $updateData['ID'] = $exist_post_id[0]->meta_value;
      wp_update_post($updateData, true);
    }
    return $exist_post_id;
  }
}
