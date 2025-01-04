<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

use BitCode\BitForm\Core\Util\FrontendHelpers;

class SliderField
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
    $step = '';
    $ph = $fieldHelpers->placeholder();
    $value = $fieldHelpers->value();
    $list = '';
    $bfFrontendFormIds = FrontendHelpers::$bfFrontendFormIds;
    $contentCount = count($bfFrontendFormIds);

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

    if (property_exists($field, 'step') && '' !== $field->step) {
      $step = "step='{$fieldHelpers->esc_attr($field->step)}'";
    }

    if (property_exists($field, 'inputMode') && '' !== $field->inputMode) {
      $inputMode = "inputMode='{$fieldHelpers->esc_attr($field->inputMode)}'";
    }

    $minValue = property_exists($field, 'mn') ? $field->mn : 0;
    $maxValue = property_exists($field, 'mx') ? $field->mx : 100;

    $defaultVal = (($maxValue - $minValue) / 2) + $minValue;
    if ($fieldHelpers->property_exists_nested($field, 'val', '', 1)) {
      $defaultVal = $field->val;
    } elseif ($fieldHelpers->property_exists_nested($field, 'defaultValue', '', 1)) {
      $defaultVal = $field->defaultValue;
    }

    $lowerTrackPercentage = ($defaultVal - $minValue) / ($maxValue - $minValue) * 100;

    $wrpperStyle = "style='--bfv-fld-val: \"{$defaultVal}\";'";
    $inputStyle = "style='--bfv-fill-lower-track: {$lowerTrackPercentage}% !important;'";

    return <<<TEXTFIELD
    <div 
      {$fieldHelpers->getCustomAttributes('inp-fld-wrp')}
      class="{$fieldHelpers->getAtomicCls('inp-fld-wrp')} {$fieldHelpers->getCustomClasses('inp-fld-wrp')}"
      {$wrpperStyle}
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
        {$step}
        {$ac}
        {$inputMode}
        {$name}
        {$value}
        {$inputStyle}
      />
      {$prefixIcn}
      {$suffixIcn}
      <span class="{$fieldHelpers->getAtomicCls('slider-val')}">Value : </span>
    </div>
    {$sugg}
TEXTFIELD;
  }
}
