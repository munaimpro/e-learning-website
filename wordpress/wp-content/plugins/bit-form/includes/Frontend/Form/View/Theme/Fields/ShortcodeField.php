<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

use BitCode\BitForm\Core\Util\FieldValueHandler;

class ShortcodeField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $fieldHelpers = new ClassicFieldHelpers($field, $rowID, $form_atomic_Cls_map);

    $content = '';
    if (isset($field->content) && !empty($field->content)) {
      $content = $field->content;
    } elseif ($fieldHelpers->property_exists_nested($field, 'info->content') && !empty($field->info->content)) {
      $content = $field->info->content;
    }

    $content = wp_kses_post(FieldValueHandler::replaceSmartTagWithValue($content));
    $content = do_shortcode($content);

    $content = self::filterShortCodeContent($content, $formID, $rowID);

    return <<<SHORTCODEFIELD
    <div
      {$fieldHelpers->getCustomAttributes('fld-wrp')}
      class="{$fieldHelpers->getAtomicCls('fld-wrp')} {$fieldHelpers->getCustomClasses('fld-wrp')}"
    >
      {$content}
    </div>
SHORTCODEFIELD;
  }

  private static function filterShortCodeContent($content, $formID, $fieldKey)
  {
    if (has_filter('bitform_filter_sortcode_content')) {
      $content = apply_filters('bitform_filter_sortcode_content', $content, $formID, $fieldKey);
    }
    return $content;
  }
}
