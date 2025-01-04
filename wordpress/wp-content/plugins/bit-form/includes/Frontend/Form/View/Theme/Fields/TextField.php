<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

use BitCode\BitForm\Core\Util\FrontendHelpers;

class TextField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $inputWrapper = new ClassicInputWrapper($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    $input = self::field($field, $rowID, $form_atomic_Cls_map, $value);
    return $inputWrapper->wrapper($input);
  }

  private static function field($field, $rowID, $form_atomic_Cls_map, $value)
  {
    $fieldHelpers = new ClassicFieldHelpers($field, $rowID, $form_atomic_Cls_map);

    $prefixIcn = $fieldHelpers->icon('prefixIcn', 'pre-i');
    $suffixIcn = $fieldHelpers->icon('suffixIcn', 'suf-i');
    $sugg = '';
    $req = $fieldHelpers->required();
    $disabled = $fieldHelpers->disabled();
    $readonly = $fieldHelpers->readonly();
    $inputMode = '';
    $name = $fieldHelpers->name();
    $ac = $fieldHelpers->autocomplete();
    $mx = '';
    $mn = '';
    $ph = $fieldHelpers->placeholder();
    $value = $fieldHelpers->value();
    $list = '';
    $bfFrontendFormIds = FrontendHelpers::$bfFrontendFormIds;
    $contentCount = count($bfFrontendFormIds);

    $onClickAttr = self::onClickAttr($field, $rowID);

    if (property_exists($field, 'suggestions') && count($field->suggestions) > 0) {
      $list = "list='{$rowID}-{$contentCount}-datalist'";
      $sugg .= "<datalist id='{$rowID}-{$contentCount}-datalist'>";
      foreach ($field->suggestions as $suggestion) {
        $val = (isset($suggestion->val) && !empty($suggestion->val)) ? $suggestion->val : $suggestion->lbl;
        $sugg .= "<option value='{$fieldHelpers->esc_attr($val)}'>{$fieldHelpers->esc_html($suggestion->lbl)}</option>";
      }
      $sugg .= '</datalist>';
    }

    if (property_exists($field, 'mn') && '' !== $field->mn) {
      $mn = "min='{$fieldHelpers->esc_attr($field->mn)}'";
    }

    if (property_exists($field, 'mx') && '' !== $field->mx) {
      $mx = "max='{$fieldHelpers->esc_attr($field->mx)}'";
    }

    if (property_exists($field, 'inputMode') && '' !== $field->inputMode) {
      $inputMode = "inputMode='{$fieldHelpers->esc_attr($field->inputMode)}'";
    }

    return <<<TEXTFIELD
    <div 
      {$fieldHelpers->getCustomAttributes('inp-fld-wrp')}
      class="{$fieldHelpers->getAtomicCls('inp-fld-wrp')} {$fieldHelpers->getCustomClasses('inp-fld-wrp')}"
    >
      <input
        {$fieldHelpers->getCustomAttributes('fld')}
        id="{$rowID}-{$contentCount}"
        {$list}
        class="{$fieldHelpers->getAtomicCls('fld')} {$fieldHelpers->getCustomClasses('fld')}"
        type="{$field->typ}"
        {$req}
        {$disabled}
        {$readonly}
        {$ph}
        {$mn}
        {$mx}
        {$ac}
        {$inputMode}
        {$name}
        {$value}
        {$onClickAttr}
      />
      {$prefixIcn}
      {$suffixIcn}
    </div>
    {$sugg}
TEXTFIELD;
  }

  private static function onClickAttr($field, $rowID)
  {
    $onClickAttr = '';
    $dateType = ['date', 'datetime-local', 'month', 'time', 'week'];
    //field type check
    if (in_array($field->typ, $dateType)) {
      $onClickAttr = "onclick='this.showPicker();'";
    }
    return $onClickAttr;
  }
}
