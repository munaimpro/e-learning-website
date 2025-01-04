<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

class ButtonField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $inputWrapper = new ClassicInputWrapper($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    $button = self::field($field, $rowID, $form_atomic_Cls_map, $formID);
    return $inputWrapper->wrapper($button, true, true);
  }

  private static function getBtnClass($btnTyp)
  {
    switch($btnTyp) {
      case 'save-draft':
        return 'bf-trigger-form-abandonment';
      case 'next-step':
        return 'next-step-btn';
      case 'previous-step':
        return 'prev-step-btn';
    }
    return '';
  }

  private static function getBtnTyp($btnTyp)
  {
    switch($btnTyp) {
      case 'submit':
      case 'reset':
        return $btnTyp;
      default:
        return 'button';
    }
  }

  private static function field($field, $rowID, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $fieldHelpers = new ClassicFieldHelpers($field, $rowID, $form_atomic_Cls_map);
    // $helperTxt = self::helperTxt($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null);

    $btnPreIcn = $fieldHelpers->icon('btnPreIcn', 'btn-pre-i');
    $btnSufIcn = $fieldHelpers->icon('btnSufIcn', 'btn-suf-i');
    $disabled = $fieldHelpers->disabled();
    // $name = $field->btnTyp === 'submit' ? 'name="bit-form-submit-btn"' : '';
    $name = $fieldHelpers->name();
    $btnSpinner = '<span class="bf-spinner d-none"></span>';
    $btnClass = self::getBtnClass($field->btnTyp);
    $btnTyp = self::getBtnTyp($field->btnTyp);

    return <<<BUTTONFIELD
      <div 
        {$fieldHelpers->getCustomAttributes('inp-fld-wrp')}
        class="{$fieldHelpers->getAtomicCls('inp-fld-wrp')} {$fieldHelpers->getCustomClasses('inp-fld-wrp')}"
      >
        <button
          {$fieldHelpers->getCustomAttributes('btn')}
          class="{$fieldHelpers->getAtomicCls('btn')} {$fieldHelpers->getCustomClasses('btn')} {$btnClass}"
          type="{$btnTyp}"
          {$name}
          {$disabled}
        >
          {$btnPreIcn}
          {$fieldHelpers->kses_post($fieldHelpers->renderHTMR($field->txt))}
          {$btnSufIcn}
          {$btnSpinner}
        </button>
      </div>
BUTTONFIELD;
  }

  private static function helperTxt($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $fieldHelpers = new ClassicFieldHelpers($field, $rowID, $form_atomic_Cls_map);
    $hlpPreIcn = $fieldHelpers->icon('hlpPreIcn', 'hlp-txt-pre-i');
    $hlpSufIcn = $fieldHelpers->icon('hlpSufIcn', 'hlp-txt-suf-i');
    $hlpTxt = isset($field->helperTxt) ? $fieldHelpers->renderHTMR($field->helperTxt) : '';

    $helperTxt = <<<HELPERTXT
      <div
        {$fieldHelpers->getCustomAttributes('hlp-txt')}
        class="{$fieldHelpers->getAtomicCls('hlp-txt')} {$fieldHelpers->getCustomClasses('hlp-txt')}"
      >
        {$hlpPreIcn}
        {$fieldHelpers->kses_post($hlpTxt)}
        {$hlpSufIcn}
      </div>

HELPERTXT;

    return  property_exists($field, 'helperTxt') ? $helperTxt : '';
  }
}
