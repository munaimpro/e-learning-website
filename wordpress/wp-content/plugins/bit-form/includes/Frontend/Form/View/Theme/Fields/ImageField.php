<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

use BitCode\BitForm\Admin\Form\Helpers;

class ImageField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $fieldHelpers = new ClassicFieldHelpers($field, $rowID, $form_atomic_Cls_map);
    $imgHeight = intval(Helpers::property_exists_nested($field, 'height', '', 1) ? $field->height : 100);
    $imgWidth = intval(Helpers::property_exists_nested($field, 'width', '', 1) ? $field->width : 40);

    $imgSrc = Helpers::property_exists_nested($field, 'bg_img', '', 1) ? $field->bg_img : "https://via.placeholder.com/{$imgWidth}x{$imgHeight}";
    $alt = Helpers::property_exists_nested($field, 'alt', '', 1) ? $field->alt : '';
    $img = <<<IMG
      <img
        {$fieldHelpers->getCustomAttributes('img')}
        class="{$fieldHelpers->getAtomicCls('img')} {$fieldHelpers->getCustomClasses('img')}"
        src="{$fieldHelpers->esc_url($imgSrc)}"
        alt="{$fieldHelpers->esc_attr($alt)}"
        width="{$fieldHelpers->esc_attr($imgWidth)}"
        height="{$fieldHelpers->esc_attr($imgHeight)}"
      />
IMG;
    return <<<IMAGEFIELD
      <div
        {$fieldHelpers->getCustomAttributes('fld-wrp')}
        class="{$fieldHelpers->getAtomicCls('fld-wrp')} {$fieldHelpers->getCustomClasses('fld-wrp')}"
      >
      {$img}
      </div>
IMAGEFIELD;
  }
}
