<?php

namespace BitCode\BitForm\Core\Util;

class MetaBoxService
{
  public static function getMetaBoxFields($postType)
  {
    $unsupportedTypes = [
      'file_input',
      'tab',
      'osm',
      'heading',
      'key_value',
      'map',
      'custom_html',
      'background',
      'fieldset_text',
      'taxonomy',
      'taxonomy_advanced',
    ];

    $fileTypes = [
      'image',
      'image_upload',
      'file_advanced',
      'file_upload',
      'single_image',
      'file',
      'image_advanced',
      'video',
    ];

    $textFields = [];

    $fileFields = [];

    $metaBoxFields = rwmb_get_object_fields($postType);

    foreach ($metaBoxFields as $index => $field) {
      if (in_array($field['type'], $unsupportedTypes)) {
        continue;
      }

      if ('group' === $field['type'] && isset($field['fields'])) {
        $getFields = self::setMetaBoxSubFields($field, $textFields, $fileFields, $fileTypes);
        $textFields = $getFields['text_fields'];
        $fileFields = $getFields['file_fields'];
        continue;
      }

      if (in_array($field['type'], $fileTypes)) {
        $fileFields[$index]['name'] = $field['name'];
        $fileFields[$index]['key'] = $field['id'];
        $fileFields[$index]['required'] = $field['required'];
      } else {
        $textFields[$index]['name'] = $field['name'];
        $textFields[$index]['key'] = $field['id'];
        $textFields[$index]['required'] = $field['required'];
      }
    }

    return ['text_fields'=> $textFields, 'file_fields'=> $fileFields];
  }

  private static function setMetaBoxSubFields($field, $textFields, $fileFields, $fileTypes)
  {
    foreach ($field['fields'] as $childIndex => $subField) {
      if (in_array($subField['type'], $fileTypes)) {
        $fileFields[$childIndex]['name'] = $subField['name'];
        $fileFields[$childIndex]['type'] = $subField['type'];
        $fileFields[$childIndex]['key'] = $field['id'] . '.' . $subField['id'];
        $fileFields[$childIndex]['required'] = $subField['required'];
      } else {
        $textFields[$childIndex]['name'] = $field['name'] . '-' . $subField['name'];
        $textFields[$childIndex]['key'] = $field['id'] . '.' . $subField['id'];
        $textFields[$childIndex]['type'] = $subField['type'];
        $textFields[$childIndex]['required'] = $subField['required'];
      }
    }
    return ['text_fields'=>$textFields, 'file_fields'=> $fileFields];
  }
}
