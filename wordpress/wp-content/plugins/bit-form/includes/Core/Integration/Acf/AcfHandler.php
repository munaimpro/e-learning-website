<?php

/**
 *POST CREATEION WITH ACF
 *
 */

namespace BitCode\BitForm\Core\Integration\Acf;

use BitCode\BitForm\Core\Form\FormManager;
use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\PostHelpers;
use BitCode\BitForm\Core\Util\SmartTags;
use BitCode\BitForm\Core\Util\WpFileHandler;

class AcfHandler
{
  use PostHelpers;

  private $_formID;

  private $_wpdb;

  private const UPLOAD_FIELD = ['file-up', 'advanced-file-up'];

  public function __construct($integrationID, $fromID)
  {
    $this->_formID = $fromID;
    global $wpdb;
    $this->_wpdb = $wpdb;
  }

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

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID)
  {
    $integrationDetails = is_string($integrationData->integration_details) ? json_decode($integrationData->integration_details) : $integrationData->integration_details;

    $taxonomy = new WpFileHandler($integrationData->form_id);

    $formManger = new FormManager($integrationData->form_id);

    $formFields = $formManger->getFields();

    $postData = $this->setPostData($integrationDetails);

    $existId = $postData['post_type'] . '_' . $entryID;

    $existPostId = $this->_wpdb->get_results("SELECT * FROM `{$this->_wpdb->prefix}bitforms_form_entrymeta` WHERE `meta_key`='$existId' ");

    $taxonomies = $taxonomy->taxonomyData($formFields, $fieldValues);

    if ([] === $existPostId) {
      $postId = wp_insert_post(['post_title' => '(no title)', 'post_content' => '']);

      $smartTagValue = $this->smartTagMappingValue($integrationDetails->post_map);

      $updatedValues = $fieldValues + $smartTagValue;

      $postMappedData = $this->postFieldMapping($integrationDetails->post_map, $updatedValues, $postId, $entryID);

      $updatedData = $postData + $postMappedData;

      $updatedData['ID'] = $postId;

      unset($updatedData['_thumbnail_id']);

      wp_update_post($updatedData, true);

      if (!empty($taxonomies)) {
        foreach ($taxonomies as $taxonomy) {
          wp_set_post_terms($postId, $taxonomy['value'], $taxonomy['term'], false);
        }
      }

      $this->_wpdb->insert(
        "{$this->_wpdb->prefix}bitforms_form_entrymeta",
        [
          'meta_key'               => $postData['post_type'] . '_' . $entryID,
          'meta_value'             => $postId,
          'bitforms_form_entry_id' => $entryID,
        ]
      );

      $smartTagValue = $this->smartTagMappingValue($integrationDetails->acf_map);

      $updatedAcfValues = $fieldValues + $smartTagValue;

      $acfFieldMappedData = AcfHelper::acfFieldMapping($integrationDetails->acf_map, $updatedAcfValues);

      AcfHelper::acfFileMapping($integrationDetails->acf_file_map, $fieldValues, $entryID, $postId, $this->_formID);

      foreach ($acfFieldMappedData as $key => $metaValue) {
        update_field($key, $metaValue, $postId);
      }
    } else {
      if (!empty($taxonomies)) {
        foreach ($taxonomies as $taxonomy) {
          wp_set_post_terms($existPostId[0]->meta_value, $taxonomy['value'], $taxonomy['term'], false);
        }
      }

      $acfFieldMapping = AcfHelper::acfFieldMapping($integrationDetails->acf_map, $fieldValues);

      AcfHelper::acfFileMapping($integrationDetails->acf_file_map, $fieldValues, $entryID, $existPostId[0]->meta_value, $this->_formID);

      foreach ($acfFieldMapping as $data) {
        if (isset($data['key'], $data['value'])) {
          update_post_meta($existPostId[0]->meta_value, $data['name'], $data['value']);
        }
      }

      $postMappedData = $this->postFieldMapping($integrationDetails->post_map, $fieldValues, $existPostId[0]->meta_value, $entryID);

      $updatedData = $postMappedData + $postData;

      $updatedData['ID'] = $existPostId[0]->meta_value;

      wp_update_post($updatedData, true);
    }
  }
}
