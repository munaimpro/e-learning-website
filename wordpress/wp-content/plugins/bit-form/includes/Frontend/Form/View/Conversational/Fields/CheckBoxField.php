<?php

namespace BitCode\BitForm\Frontend\Form\View\Conversational\Fields;

use BitCode\BitForm\Core\Util\FrontendHelpers;

class CheckBoxField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value)
  {
    $inputWrapper = new ConversationalInputWrapper($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    $input = self::field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    return $inputWrapper->wrapper($input);
  }

  private static function field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value)
  {
    $fieldHelpers = new ConversationalFieldHelpers($formID, $field, $rowID, $form_atomic_Cls_map);

    $req = $fieldHelpers->required();
    $name = $fieldHelpers->name();
    $checkBoxOptions = '';
    $bfFrontendFormIds = FrontendHelpers::$bfFrontendFormIds;
    $contentCount = count($bfFrontendFormIds);

    if (property_exists($field, 'opt') && count($field->opt) > 0) {
      if (isset($field->val) && false !== strpos($field->val, BITFORMS_BF_SEPARATOR)) {
        $defaultValues = explode(BITFORMS_BF_SEPARATOR, $field->val);
      } else {
        $defaultValues = isset($field->val) ? explode(',', $field->val) : [];
      }
      foreach ($field->opt as $key => $opt) {
        $value = isset($opt->val) ? $opt->val : $opt->lbl;
        $check = '';
        $req = '';
        $disabled = '';
        $keyChar = chr(65 + $key);
        $href = htmlentities("#$rowID-ck-svg");
        if ($fieldHelpers->property_exists_nested($opt, 'check', true)) {
          $check = 'checked';
        } else {
          if (in_array($value, $defaultValues)) {
            $check = 'checked';
          }
        }
        if ($fieldHelpers->property_exists_nested($opt, 'req', true)) {
          $req = 'required';
        }
        if ($fieldHelpers->property_exists_nested($field, 'valid->disabled', true)
        || $fieldHelpers->property_exists_nested($opt, 'disabled', true)) {
          $disabled = "disabled='disabled'";
        }

        $checkBoxOptions .= <<<CHECKBOXOPTIONS
        <div
          {$fieldHelpers->getCustomAttributes('cw')}
          class="{$fieldHelpers->getConversationalCls('cw')} {$fieldHelpers->getCustomClasses('cw')}"
        >
          <input
            id="{$rowID}-{$contentCount}-chk-{$key}"
            type="checkbox"
            class="{$fieldHelpers->getConversationalCls('ci')} {$fieldHelpers->getCustomClasses('ci')}"
            {$disabled}
            value="{$value}"
            {$check}
            {$req}
            {$name}
          />

          <label
            {$fieldHelpers->getCustomAttributes('cl')}
            data-cl
            for="{$rowID}-{$contentCount}-chk-{$key}"
            class="{$fieldHelpers->getConversationalCls('cl')} {$fieldHelpers->getCustomClasses('cl')}"
          >
            
            <span
            {$fieldHelpers->getCustomAttributes('bx')}
            data-bx
            class="{$fieldHelpers->getConversationalCls('bx')} {$fieldHelpers->getCustomClasses('bx')}"
            >
              <span class="{$fieldHelpers->getConversationalCls('opt-key-lbl')}">Key</span>
              <span class="{$fieldHelpers->getConversationalCls('opt-key')}">{$keyChar}</span>
            </span>
            <span
              {$fieldHelpers->getCustomAttributes('ct')}
              class="{$fieldHelpers->getConversationalCls('ct')} {$fieldHelpers->getCustomClasses('ct')}"
            >
              {$fieldHelpers->kses_post($fieldHelpers->renderHTMR($opt->lbl))}
            </span>
          </label>
        </div>
CHECKBOXOPTIONS;
      }
    }

    //Other Option
    $optCount = property_exists($field, 'opt') ? count($field->opt) : 0;
    $inputPh = isset($field->otherInpPh) ? "placeholder='{$fieldHelpers->esc_attr($field->otherInpPh)}'" : "placeholder='Other...'";
    $inpReq = isset($field->valid->otherOptReq) ? ($field->valid->otherOptReq ? 'required' : '') : '';
    if (property_exists($field, 'addOtherOpt') && $field->addOtherOpt) {
      $keyChar = chr(65 + count($field->opt));
      $checkBoxOptions .= <<<CHECKBOXOPTIONS
      <div
          {$fieldHelpers->getCustomAttributes('cw')}
          class="{$fieldHelpers->getConversationalCls('cw')} {$fieldHelpers->getCustomClasses('cw')}"
        >
          <input
            id="{$rowID}-{$contentCount}-chk-{$optCount}"
            data-oopt="{$rowID}"
            type="checkbox"
            class="{$fieldHelpers->getConversationalCls('ci')} {$fieldHelpers->getCustomClasses('ci')}"
            {$disabled}
            value=""
            {$name}
          />
          <label
            {$fieldHelpers->getCustomAttributes('cl')}
            data-cl
            for="{$rowID}-{$contentCount}-chk-{$optCount}"
            class="{$fieldHelpers->getConversationalCls('cl')} {$fieldHelpers->getCustomClasses('cl')}"
          >
            <span
              {$fieldHelpers->getCustomAttributes('ck')}
              data-bx
              class="{$fieldHelpers->getConversationalCls('bx')} {$fieldHelpers->getConversationalCls('ck')} {$fieldHelpers->getCustomClasses('ck')}"
            >
              <span class="{$fieldHelpers->getConversationalCls('opt-key')}">{$keyChar}</span>
            </span>
            <span
              {$fieldHelpers->getCustomAttributes('ct')}
              class="{$fieldHelpers->getConversationalCls('ct')} {$fieldHelpers->getCustomClasses('ct')}"
            >
              Other..
            </span>
          </label>
          <div data-oinp-wrp class="{$fieldHelpers->getConversationalCls('other-inp-wrp')}">
            <input
              data-bf-other-inp='{$rowID}-chk-{$optCount}'
              type="text"
              class="{$fieldHelpers->getConversationalCls('other-inp')} {$fieldHelpers->getCustomClasses('other-inp')}"
              {$inpReq}
              {$inputPh}
            />
          </div>
        </div>
CHECKBOXOPTIONS;
    }

    return <<<CHECKBOXFIELD
<div
  {$fieldHelpers->getCustomAttributes('cc')}
  class="{$fieldHelpers->getConversationalCls('cc')} {$fieldHelpers->getCustomClasses('cc')}"
>
  <svg class="{$fieldHelpers->getConversationalCls('cks')}">
    <symbol id="{$rowID}-ck-svg" viewBox="0 0 12 10">
      <polyline
        class="{$fieldHelpers->getConversationalCls('ck-svgline')}"
        points="1.5 6 4.5 9 10.5 1"
      ></polyline>
    </symbol>
  </svg>
  {$checkBoxOptions}
</div>
CHECKBOXFIELD;
  }
}
