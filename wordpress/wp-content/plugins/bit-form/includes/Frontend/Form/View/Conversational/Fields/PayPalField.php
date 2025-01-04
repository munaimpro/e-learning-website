<?php

namespace BitCode\BitForm\Frontend\Form\View\Conversational\Fields;

class PayPalField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $inputWrapper = new ConversationalInputWrapper($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    $input = self::field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    return $inputWrapper->wrapper($input, true);
  }

  private static function field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $fieldHelpers = new ConversationalFieldHelpers($formID, $field, $rowID, $form_atomic_Cls_map);

    return <<<PAYPALFIELD
      <div class="{$fieldHelpers->getConversationalCls('paypal-wrp')}"></div>
PAYPALFIELD;
  }
}
