<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

class MollieField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $inputWrapper = new ClassicInputWrapper($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    $input = self::field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    return $inputWrapper->wrapper($input);
  }

  private static function field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $fieldHelpers = new ClassicFieldHelpers($field, $rowID, $form_atomic_Cls_map);

    return <<<MOLLIEFIELD
    <div class="{$fieldHelpers->getAtomicCls('mollie-wrp')}">
      <button type="button" class="{$fieldHelpers->getAtomicCls('mollie-btn')}">
        <svg
          version="1.1"
          id="Layer_1"
          xmlns="http://www.w3.org/2000/svg"
          xmlns:xlink="http://www.w3.org/1999/xlink"
          x="0px"
          y="0px"
          viewBox="0 0 80 80"
          xmlSpace="preserve"
          class="{$fieldHelpers->getAtomicCls('mollie-icn')}"
        >
          <path
            stroke-width="6"
            fill="currentColor"
            d="M74.79,39.15v21.4c0,0.36-0.07,0.46-0.44,0.46c-3.59-0.02-7.21-0.02-10.8,0c-0.41,0-0.51-0.1-0.51-0.51  c0.02-6.94,0-13.86,0.02-20.79c0-2.89-0.97-5.31-3.25-7.09c-2.62-2.06-5.56-2.5-8.64-1.21c-3.03,1.26-4.76,3.62-5.24,6.89  c-0.05,0.49-0.07,0.99-0.07,1.48c0,6.89,0,13.78,0.02,20.7c0,0.41-0.1,0.53-0.53,0.53c-3.57-0.02-7.16-0.02-10.75,0  c-0.36,0-0.46-0.1-0.46-0.49V39.73c0.02-2.89-0.97-5.34-3.28-7.13c-2.62-2.04-5.56-2.48-8.61-1.19c-3.03,1.26-4.76,3.62-5.22,6.87  c-0.07,0.51-0.07,1.02-0.07,1.53c0,6.89-0.02,13.81,0,20.7c0,0.41-0.1,0.51-0.51,0.51c-3.59-0.02-7.18-0.02-10.77,0  c-0.36,0-0.46-0.1-0.46-0.46c0.02-7.3-0.05-14.61,0.05-21.94c0.05-4.97,1.89-9.37,5.29-13.01c4.54-4.9,10.19-7.09,16.86-6.53  c4.76,0.39,8.83,2.35,12.28,5.63c0.24,0.24,0.36,0.24,0.61,0c4.78-4.46,10.41-6.38,16.91-5.53c8.83,1.14,15.8,7.93,17.28,16.72  C74.69,36.99,74.79,38.06,74.79,39.15z"
          />
        </svg>
        {$fieldHelpers->kses_post($field->txt)}
        <svg width="20" height="20" class="mollie-btn-spinner d-none" version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="20 20 60 60" enable-background="new 0 0 0 0"
                xml:space="preserve">
                <path fill="#fff"
                  d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">
                  <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s" from="0 50 50"
                    to="360 50 50" repeatCount="indefinite" />
                </path>
              </svg>
      </button>
  </div>
MOLLIEFIELD;
  }
}
