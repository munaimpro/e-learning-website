<?php

namespace BitCode\BitForm\Core\Integration\MetaBox;

use BitCode\BitForm\Core\Util\WpFileHandler;

class MetaBoxHelper
{
  public static function metaBoxFieldMapping($metaBoxMapField, $fieldValues, $metaBoxFieldInfo, $postId, $action)
  {
    $selectedTypes = ['select', 'checkbox_list', 'select_advanced', 'user', 'post'];

    $metaBoxMappedData = [];

    foreach ($metaBoxMapField as $fieldPair) {
      $metaBoxFieldKeys = explode('.', $fieldPair->metaboxField);

      $fieldName = $fieldPair->formField;

      if ('custom' === $fieldName && isset($fieldPair->customValue)) {
        $fieldValue = $fieldPair->customValue;
      } elseif (isset($fieldValues[$fieldName])) {
        $fieldValue = $fieldValues[$fieldName];
      }

      if (count($metaBoxFieldKeys) > 1) {
        $metaBoxFieldId = $metaBoxFieldKeys[0];
        $subFieldId = $metaBoxFieldKeys[1];
      } else {
        $metaBoxFieldId = $metaBoxFieldKeys[0];
      }

      $metaFieldInfo = $metaBoxFieldInfo[$metaBoxFieldId];

      if ('group' === $metaFieldInfo['type'] && isset($subFieldId)) {
        $metaBoxMappedData = self::setGroupSubFields($fieldName, $metaFieldInfo, $fieldValues, $subFieldId, $metaBoxMappedData);
      } elseif (in_array($metaFieldInfo['type'], $selectedTypes)) {
        $values = !is_array($fieldValue) ? explode(',',  $fieldValue) : $fieldValue;

        if (true === $metaFieldInfo['multiple'] && is_array($values)) {
          self::setMultipleTypeFields($values, $postId, $metaBoxFieldId, $action);
        } else {
          $metaBoxMappedData[$metaBoxFieldId] = $fieldValue;
        }
      } else {
        $metaBoxMappedData[$metaBoxFieldId] = $fieldValue;
      }
    }

    return $metaBoxMappedData;
  }

  public static function fileUploadMapping($metaboxMapField, $fieldValues, $metaboxFields, $formId, $entryID, $postId)
  {
    $fileUploadHandle = new WpFileHandler($formId);
    foreach ($metaboxMapField as $fieldPair) {
      if (!property_exists($fieldPair, 'formField')) {
        continue;
      }

      if (isset($fieldValues[$fieldPair->formField], $metaboxFields[$fieldPair->metaboxFileUpload])) {
        $metaInfo = $metaboxFields[$fieldPair->metaboxFileUpload];

        if (false === $metaInfo['multiple']) {
          $attachMentId = $fileUploadHandle->singleFileMoveWpMedia($entryID, $fieldValues[$fieldPair->formField], $postId);

          if (!empty($attachMentId)) {
            add_post_meta($postId, $metaInfo['id'], $attachMentId);
          }
        } elseif (true === $metaInfo['multiple']) {
          $attachMentIds = $fileUploadHandle->multiFileMoveWpMedia($entryID, $fieldValues[$fieldPair->formField], $postId);
          if (!empty($attachMentIds) && is_array($attachMentIds)) {
            foreach ($attachMentIds as $attachmentId) {
              add_post_meta($postId, $metaInfo['id'], $attachmentId);
            }
          }
        }
      }
    }
  }

  private static function setGroupSubFields($fieldName, $metaFieldInfo, $fieldValues, $subFieldId, $metaBoxMappedData)
  {
    $formFieldKeys = explode('.', $fieldName);

    if (2 !== count($formFieldKeys)) {
      return $metaBoxMappedData;
    }

    $formParentFieldKey = $formFieldKeys[0];
    $formChildFieldKey = $formFieldKeys[1];

    $metaBoxParentFieldKey = $metaFieldInfo['id'];

    $formRepeaterValues = $fieldValues[$formParentFieldKey];

    if (!isset($metaBoxMappedData[$metaBoxParentFieldKey])) {
      $metaBoxMappedData[$metaBoxParentFieldKey] = [];
    }

    foreach ($formRepeaterValues as $repeaterIndex => $values) {
      $fieldValue = $values[$formChildFieldKey];

      if (!isset($metaBoxMappedData[$metaBoxParentFieldKey][$repeaterIndex])) {
        $metaBoxMappedData[$metaBoxParentFieldKey][$repeaterIndex] = [];
      }

      $metaBoxMappedData[$metaBoxParentFieldKey][$repeaterIndex][$subFieldId] = $fieldValue;
    }

    return $metaBoxMappedData;
  }

  private static function setMultipleTypeFields($values, $postId, $metaBoxFieldId, $action)
  {
    $getMetaValues = get_post_meta($postId, $metaBoxFieldId, false);
    if ('update' === $action && count($getMetaValues) > 0) {
      foreach ($getMetaValues as $value) {
        delete_post_meta($postId, $metaBoxFieldId);
      }
    }
    foreach ($values as $value) {
      add_post_meta($postId, $metaBoxFieldId, $value);
    }
  }
}
