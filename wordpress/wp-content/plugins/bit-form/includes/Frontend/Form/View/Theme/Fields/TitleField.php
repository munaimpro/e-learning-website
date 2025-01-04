<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

use BitCode\BitForm\Core\Util\FieldValueHandler;

class TitleField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    return self::field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
  }

  private static function titleGenerator($tag, $text, $cls, $preIcn, $sufIcn, $fk, $field, $form_atomic_Cls_map)
  {
    $text = FieldValueHandler::replaceSmartTagWithValue($text);
    $fieldHelpers = new ClassicFieldHelpers($field, $fk, $form_atomic_Cls_map);

    return <<<TITLEGENERATOR
      <{$tag} class="{$fieldHelpers->getAtomicCls($cls)} {$fieldHelpers->getCustomClasses($cls)}">
      {$preIcn}
      {$fieldHelpers->kses_post($text)}
      {$sufIcn}
      </{$tag}>
TITLEGENERATOR;
  }

  private static function iconImg($field, $object, $element, $fieldHelpers, $alt, $fk)
  {
    return <<<ICONIMG
      <img 
        {$fieldHelpers->getCustomAttributes($element)}
        class="{$fieldHelpers->getAtomicCls($element)} {$fieldHelpers->getCustomClasses($element)}"
        src="{$fieldHelpers->esc_url($field->$object)}" 
        alt="{$fieldHelpers->esc_attr($alt)}" 
      />
ICONIMG;
  }

  private static function field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $fieldHelpers = new ClassicFieldHelpers($field, $rowID, $form_atomic_Cls_map);

    $logo = $fieldHelpers->icon('logo', 'logo');
    $titleHide = '';
    $subtitleHide = '';

    $titlePreIcn = '';
    $titleSufIcn = '';
    $subTitlPreIcn = '';
    $subTitlSufIcn = '';

    if (property_exists($field, 'titlePreIcn')) {
      $titlePreIcn = self::iconImg($field, 'titlePreIcn', 'title-pre-i', $fieldHelpers, 'Title Prefix Icon', $rowID);
    }

    if (property_exists($field, 'titleSufIcn')) {
      $titleSufIcn = self::iconImg($field, 'titleSufIcn', 'title-suf-i', $fieldHelpers, 'Title Suffix Icon', $rowID);
    }

    if (property_exists($field, 'subTitlPreIcn')) {
      $subTitlPreIcn = self::iconImg($field, 'subTitlPreIcn', 'sub-titl-pre-i', $fieldHelpers, 'Subtitle Prefix Icon', $rowID);
    }

    if (property_exists($field, 'subTitlSufIcn')) {
      $subTitlSufIcn = self::iconImg($field, 'subTitlSufIcn', 'sub-titl-suf-i', $fieldHelpers, 'Subtitle Suffix Icon', $rowID);
    }

    if (property_exists($field, 'titleHide') && !$field->titleHide) {
      $titleHide = self::titleGenerator($field->titleTag, $field->title, 'title', $titlePreIcn, $titleSufIcn, $rowID, $field, $form_atomic_Cls_map);
    }

    if (property_exists($field, 'subtitleHide') && !$field->subtitleHide) {
      $subtitleHide = self::titleGenerator($field->subTitleTag, $field->subtitle, 'sub-titl', $subTitlPreIcn, $subTitlSufIcn, $rowID, $field, $form_atomic_Cls_map);
    }

    return <<<TITLEFIELD
    <div 
      {$fieldHelpers->getCustomAttributes('fld-wrp')}
      class="{$fieldHelpers->getAtomicCls('fld-wrp')} {$fieldHelpers->getCustomClasses('fld-wrp')}"
    >
     {$logo}
      <div
        {$fieldHelpers->getCustomAttributes('titl-wrp')}
        class="{$fieldHelpers->getAtomicCls('titl-wrp')} {$fieldHelpers->getCustomClasses('titl-wrp')}"
      >
        {$titleHide}
        {$subtitleHide}
      </div>
    </div>
TITLEFIELD;
  }
}
