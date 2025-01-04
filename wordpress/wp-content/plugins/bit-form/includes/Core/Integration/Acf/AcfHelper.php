<?php

namespace BitCode\BitForm\Core\Integration\Acf;

use BitCode\BitForm\Core\Util\WpFileHandler;

class AcfHelper
{
  public static function acfFieldMapping($acfMapField, $fieldValues)
  {
    $selectedTypes = ['checkbox', 'select', 'user', 'post_object'];
    $acfFieldMappedData = [];

    foreach ($acfMapField as $fieldPair) {
      $fieldName = $fieldPair->formField;

      $acfFieldKeys = explode('.', $fieldPair->acfField);

      $fieldValue = '';

      if ('custom' === $fieldName && isset($fieldPair->customValue)) {
        $fieldValue = $fieldPair->customValue;
      } elseif (isset($fieldValues[$fieldName])) {
        $fieldValue = $fieldValues[$fieldName];
      }

      if (count($acfFieldKeys) > 1) {
        $acfFieldId = $acfFieldKeys[0];
        $subFieldId = $acfFieldKeys[1];
      } else {
        $acfFieldId = $acfFieldKeys[0];
      }

      $acfFieldInfo = get_field_object($acfFieldId);

      if (!$acfFieldInfo) {
        continue;
      }

      if ('repeater' === $acfFieldInfo['type'] && isset($subFieldId)) {
        $acfFieldMappedData = self::setRepeaterFields($fieldName, $acfFieldInfo, $fieldValues, $subFieldId, $acfFieldMappedData);
      } elseif (in_array($acfFieldInfo['type'], $selectedTypes)) {
        $acfFieldMappedData[$acfFieldId] = !is_array($fieldValue) ? explode(',',  $fieldValue) : $fieldValue;
      } else {
        $acfFieldMappedData[$acfFieldId] = $fieldValue;
      }
    }

    return $acfFieldMappedData;
  }

  private static function setRepeaterFields($fieldName, $acfFieldInfo, $fieldValues, $subFieldId, $acfFieldMappedData)
  {
    $formFieldKeys = explode('.', $fieldName);

    if (2 !== count($formFieldKeys)) {
      return $acfFieldMappedData;
    }

    $formParentFieldKey = $formFieldKeys[0];
    $formChildFieldKey = $formFieldKeys[1];

    $acfParentFieldKey = $acfFieldInfo['key'];

    $formRepeaterValues = $fieldValues[$formParentFieldKey];

    if (!isset($acfFieldMappedData[$acfParentFieldKey])) {
      $acfFieldMappedData[$acfParentFieldKey] = [];
    }

    foreach ($formRepeaterValues as $repeaterIndex => $values) {
      $fieldValue = $values[$formChildFieldKey];

      if (!isset($acfFieldMappedData[$acfParentFieldKey][$repeaterIndex])) {
        $acfFieldMappedData[$acfParentFieldKey][$repeaterIndex] = [];
      }

      $acfFieldMappedData[$acfParentFieldKey][$repeaterIndex][$subFieldId] = $fieldValue;
    }

    return $acfFieldMappedData;
  }

  public static function acfFileMapping($acfMapField, $fieldValues, $entryID, $postId, $formId)
  {
    $fileTypes = ['file', 'image'];

    $fileUploadHandle = new WpFileHandler($formId);

    foreach ($acfMapField as $fieldPair) {
      if (property_exists($fieldPair, 'acfFileUpload') && property_exists($fieldPair, 'formField')) {
        $acfFieldId = $fieldPair->acfFileUpload;

        $acfFieldInfo = get_field_object($acfFieldId);

        $formField = $fieldPair->formField;

        if (!$acfFieldInfo || empty($fieldValues[$formField])) {
          continue;
        }

        if (in_array($acfFieldInfo['type'], $fileTypes)) {
          $attachMentId = $fileUploadHandle->singleFileMoveWpMedia($entryID,  $fieldValues[$formField], $postId);

          if (!empty($attachMentId)) {
            $exists = metadata_exists('post', $postId, '_' . $acfFieldInfo['name']);

            if (false === $exists) {
              update_post_meta($postId, '_' . $acfFieldInfo['name'], $acfFieldInfo['key']);
              update_post_meta($postId, $acfFieldInfo['name'], wp_json_encode($attachMentId));
            } else {
              update_post_meta($postId, $acfFieldInfo['name'], wp_json_encode($attachMentId));
            }
          }
        } elseif ('gallery' === $acfFieldInfo['type']) {
          $attachMentId = $fileUploadHandle->multiFileMoveWpMedia($entryID, $fieldValues[$formField], $postId);

          $exists = metadata_exists('post', $postId, '_' . $acfFieldInfo['name']);

          if (!empty($attachMentId)) {
            if (false === $exists) {
              update_post_meta($postId, '_' . $acfFieldInfo['name'], $acfFieldInfo['key']);
              update_post_meta($postId, $acfFieldInfo['name'], $attachMentId);
            } else {
              update_post_meta($postId, $acfFieldInfo['name'], $attachMentId);
            }
          }
        }
      }
    }
  }
}
