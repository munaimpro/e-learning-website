<?php

/**
 *POST CREATEION WITH Metabox
 *
 */

namespace BitCode\BitForm\Core\Integration\MetaBox;

use BitCode\BitForm\Core\Form\FormManager;
use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\PostHelpers;
use BitCode\BitForm\Core\Util\SmartTags;
use BitCode\BitForm\Core\Util\WpFileHandler;

class MetaBoxHandler
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

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = is_string($integrationData->integration_details) ? json_decode($integrationData->integration_details) : $integrationData->integration_details;

    $formManger = new FormManager($integrationData->form_id);

    $formFields = $formManger->getFields();

    $postData = $this->setPostData($integrationDetails);

    $id = $postData['post_type'] . '_' . $entryID;

    $existPostId = $this->_wpdb->get_results("SELECT * FROM `{$this->_wpdb->prefix}bitforms_form_entrymeta` WHERE `meta_key`='$id' ");

    $metaBoxFields = rwmb_get_object_fields($postData['post_type']);

    $taxonomies = (new WpFileHandler($integrationData->form_id))->taxonomyData($formFields, $fieldValues);

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

      $smartTagValue = $this->smartTagMappingValue($integrationDetails->metabox_map);

      $updatedMbValues = $fieldValues + $smartTagValue;

      $metaBoxMappedData = MetaBoxHelper::metaBoxFieldMapping($integrationDetails->metabox_map, $updatedMbValues, $metaBoxFields, $postId, 'create');

      MetaBoxHelper::fileUploadMapping($integrationDetails->metabox_file_map, $fieldValues, $metaBoxFields, $this->_formID, $entryID, $postId);

      foreach ($metaBoxMappedData as $key => $value) {
        add_post_meta($postId, $key, $value);
      }
    } else {
      if (!empty($taxonomies)) {
        foreach ($taxonomies as $taxonomy) {
          wp_set_post_terms($existPostId[0]->meta_value, $taxonomy['value'], $taxonomy['term'], false);
        }
      }

      $metaBoxMappedData = MetaBoxHelper::metaBoxFieldMapping($integrationDetails->metabox_map, $fieldValues, $metaBoxFields, $existPostId[0]->meta_value, 'update');

      foreach ($metaBoxMappedData as $key => $value) {
        update_post_meta($existPostId[0]->meta_value, $key, $value);
      }

      $updatedData = $this->postFieldMapping($postData, $integrationDetails->post_map, $fieldValues, $existPostId[0]->meta_value, $entryID);

      $updatedData['ID'] = $existPostId[0]->meta_value;

      wp_update_post($updatedData, true);
    }
  }
}
