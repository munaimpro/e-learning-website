<?php

namespace BitCode\BitForm\Frontend\Form\View\Conversational\Fields;

use BitCode\BitForm\Core\Util\FrontendHelpers;

class RadioBoxField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
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
    $value = '';
    $radioBoxOptions = '';
    $bfFrontendFormIds = FrontendHelpers::$bfFrontendFormIds;
    $contentCount = count($bfFrontendFormIds);
    if (property_exists($field, 'opt') && count($field->opt) > 0) {
      $defaultValue = isset($field->val) ? $field->val : '';
      foreach ($field->opt as $key => $opt) {
        $value = isset($opt->val) ? $opt->val : $opt->lbl;
        $check = '';
        $req = '';
        $lbl = $fieldHelpers->renderHTMR($opt->lbl);
        $disabled = '';
        $keyChar = chr(65 + $key);
        if ($fieldHelpers->property_exists_nested($opt, 'check', true)) {
          $check = "checked='{$opt->check}'";
        } else {
          if ($value === $defaultValue) {
            $check = "checked='checked'";
          }
        }
        if ($fieldHelpers->property_exists_nested($opt, 'req', true)) {
          $req = "required='{$opt->req}'";
        }
        if ($fieldHelpers->property_exists_nested($field, 'valid->disabled', true)
        || $fieldHelpers->property_exists_nested($opt, 'disabled', true)) {
          $disabled = "disabled='disabled'";
        }

        $radioBoxOptions .= <<<RADIOBOXOPTIONS
          <div 
            {$fieldHelpers->getCustomAttributes('cw')}
            class="{$fieldHelpers->getConversationalCls('cw')} {$fieldHelpers->getCustomClasses('cw')}"
          >
            <input
              {$fieldHelpers->getCustomAttributes('ci')}
              id="{$rowID}-{$contentCount}-chk-{$key}"
              type="radio"
              class="{$fieldHelpers->getConversationalCls('ci')} {$fieldHelpers->getCustomClasses('ci')}"
              value="{$fieldHelpers->esc_attr($value)}"
              {$check}
              {$name}
              {$req}
              {$disabled}
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
                {$fieldHelpers->kses_post($lbl)}
              </span>
            </label>
          </div>
RADIOBOXOPTIONS;
      }
    }

    //Other Option
    $optCount = property_exists($field, 'opt') ? count($field->opt) : 0;
    $inputPh = isset($field->otherInpPh) ? $field->otherInpPh : 'Other...';
    $inpReq = isset($field->valid->otherOptReq) ? ($field->valid->otherOptReq ? 'required' : '') : '';
    if (property_exists($field, 'addOtherOpt') && $field->addOtherOpt) {
      $keyChar = chr(65 + count($field->opt));
      $radioBoxOptions .= <<<RADIOBOXOPTIONS
      <div 
        {$fieldHelpers->getCustomAttributes('cw')}
        class="{$fieldHelpers->getConversationalCls('cw')} {$fieldHelpers->getCustomClasses('cw')}"
      >
      <input
        {$fieldHelpers->getCustomAttributes('ci')}
        id="{$rowID}-{$contentCount}-chk-{$optCount}"
        data-oopt="{$rowID}"
        type="radio"
        class="{$fieldHelpers->getConversationalCls('ci')} {$fieldHelpers->getCustomClasses('ci')}"
        value=""
        {$check}
        {$name}
      />
      <label
        {$fieldHelpers->getCustomAttributes('cl')}
        data-cl
        for="{$rowID}-{$contentCount}-chk-{$optCount}"
        class="{$fieldHelpers->getConversationalCls('cl')} {$fieldHelpers->getCustomClasses('cl')}"
      >
        <span
          {$fieldHelpers->getCustomAttributes('rdo')}
          data-bx
          class="{$fieldHelpers->getConversationalCls('bx')} {$fieldHelpers->getConversationalCls('rdo')} {$fieldHelpers->getCustomClasses('rdo')}"
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
          data-bf-other-inp="{$rowID}-chk-{$optCount}"
          type="text"
          class="{$fieldHelpers->getConversationalCls('other-inp')}"
          {$inpReq}
          placeholder="{$fieldHelpers->esc_attr($inputPh)}"
        >
      </div>
    </div>
RADIOBOXOPTIONS;
    }

    return <<<RADIOBOX
      <div
        {$fieldHelpers->getCustomAttributes('cc')}
        class="{$fieldHelpers->getConversationalCls('cc')} {$fieldHelpers->getCustomClasses('cc')}"
      >
        {$radioBoxOptions}
      </div>
RADIOBOX;
  }
}
