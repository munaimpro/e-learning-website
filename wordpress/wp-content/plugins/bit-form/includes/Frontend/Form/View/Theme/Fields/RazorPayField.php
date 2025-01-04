<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

class RazorPayField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $inputWrapper = new ClassicInputWrapper($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    $input = self::field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    return $inputWrapper->wrapper($input, true);
  }

  private static function field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $fieldHelpers = new ClassicFieldHelpers($field, $rowID, $form_atomic_Cls_map);
    $subTitl = '';

    if ($fieldHelpers->property_exists_nested($field, 'subTitl', true)) {
      $subTitl = <<<SUBTITLE
<span
  {$fieldHelpers->getCustomAttributes('razorpay-btn-sub-title')}
  class="{$fieldHelpers->getAtomicCls('razorpay-btn-sub-title')} {$fieldHelpers->getCustomClasses('razorpay-btn-sub-title')}"
>
  Secured by Razorpay
</span>
SUBTITLE;
    }

    return <<<RAZORPAYFIELD
<div class="bf-form">
  <div class="{$fieldHelpers->getAtomicCls('razorpay-wrp')}">
    <button
      {$fieldHelpers->getCustomAttributes('razorpay-btn')}
      type="button"
      class="{$fieldHelpers->getAtomicCls('razorpay-btn')} {$fieldHelpers->getCustomClasses('razorpay-btn')}"
    >
      <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M7.077 6.476l-.988 3.569 5.65-3.589-3.695 13.54 3.752.004 5.457-20L7.077 6.476z" fill="#fff" />
        <path d="M1.455 14.308L0 20h7.202L10.149 8.42l-8.694 5.887z" fill="#fff" />
      </svg>
      <div
        {$fieldHelpers->getCustomAttributes('razorpay-btn-text')}
        class="{$fieldHelpers->getAtomicCls('razorpay-btn-text')} {$fieldHelpers->getCustomClasses('razorpay-btn-text')}"
      >
        <span
          {$fieldHelpers->getCustomAttributes('razorpay-btn-title')}
          class="{$fieldHelpers->getAtomicCls('razorpay-btn-title')} {$fieldHelpers->getCustomClasses('razorpay-btn-title')}"
        >
          {$fieldHelpers->esc_html($fieldHelpers->renderHTMR($field->btnTxt))}
        </span>
        {$subTitl}
      </div>
    </button>
  </div>
</div>
RAZORPAYFIELD;
  }
}
