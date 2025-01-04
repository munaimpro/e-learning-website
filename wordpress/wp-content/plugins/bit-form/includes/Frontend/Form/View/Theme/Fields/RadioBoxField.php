<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

use BitCode\BitForm\Core\Util\FrontendHelpers;

class RadioBoxField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $inputWrapper = new ClassicInputWrapper($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    $input = self::field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    return $inputWrapper->wrapper($input);
  }

  private static function field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value)
  {
    $fieldHelpers = new ClassicFieldHelpers($field, $rowID, $form_atomic_Cls_map);
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
            class="{$fieldHelpers->getAtomicCls('cw')} {$fieldHelpers->getCustomClasses('cw')}"
          >
            <input
              {$fieldHelpers->getCustomAttributes('ci')}
              id="{$rowID}-{$contentCount}-chk-{$key}"
              type="radio"
              class="{$fieldHelpers->getAtomicCls('ci')} {$fieldHelpers->getCustomClasses('ci')}"
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
              class="{$fieldHelpers->getAtomicCls('cl')} {$fieldHelpers->getCustomClasses('cl')}"
            >
              <span
                {$fieldHelpers->getCustomAttributes('bx')}
                data-bx
                class="{$fieldHelpers->getAtomicCls('bx')} {$fieldHelpers->getCustomClasses('bx')}"
              >
                <svg width="12" height="10" viewBox="0 0 12 10" class="{$fieldHelpers->getAtomicCls('svgwrp')} {$fieldHelpers->getCustomClasses('svgwrp')}">
                  <use data-ck-icn href=#{$rowID}-ck-svg class="{$fieldHelpers->getAtomicCls('ck-icn')} {$fieldHelpers->getCustomClasses('ck-icn')}" />
                </svg>
              </span>
              <span
                {$fieldHelpers->getCustomAttributes('ct')}
                class="{$fieldHelpers->getAtomicCls('ct')} {$fieldHelpers->getCustomClasses('ct')}"
              >
                {$fieldHelpers->kses_post($lbl)}
              </span>
            </label>
          </div>
RADIOBOXOPTIONS;
      }
    }

    //Other Option
    $other = __('Other...', 'bit-form');
    $optCount = property_exists($field, 'opt') ? count($field->opt) : 0;
    $inputPh = isset($field->otherInpPh) ? $field->otherInpPh : $other;
    $inpReq = isset($field->valid->otherOptReq) ? ($field->valid->otherOptReq ? 'required' : '') : '';
    if (property_exists($field, 'addOtherOpt') && $field->addOtherOpt) {
      $radioBoxOptions .= <<<RADIOBOXOPTIONS
      <div 
        {$fieldHelpers->getCustomAttributes('cw')}
        class="{$fieldHelpers->getAtomicCls('cw')} {$fieldHelpers->getCustomClasses('cw')}"
      >
      <input
        {$fieldHelpers->getCustomAttributes('ci')}
        id="{$rowID}-{$contentCount}-chk-{$optCount}"
        data-oopt="{$rowID}"
        type="radio"
        class="{$fieldHelpers->getAtomicCls('ci')} {$fieldHelpers->getCustomClasses('ci')}"
        value=""
        {$check}
        {$name}
      />
      <label
        {$fieldHelpers->getCustomAttributes('cl')}
        data-cl
        for="{$rowID}-{$contentCount}-chk-{$optCount}"
        class="{$fieldHelpers->getAtomicCls('cl')} {$fieldHelpers->getCustomClasses('cl')}"
      >
        <span
          {$fieldHelpers->getCustomAttributes('rdo')}
          data-bx
          class="{$fieldHelpers->getAtomicCls('bx')} {$fieldHelpers->getAtomicCls('rdo')} {$fieldHelpers->getCustomClasses('rdo')}"
        >
          <svg width="12" height="10" viewBox="0 0 12 10" class="{$fieldHelpers->getAtomicCls('svgwrp')} {$fieldHelpers->getCustomClasses('svgwrp')}">
            <use data-ck-icn href=#{$rowID}-ck-svg class="{$fieldHelpers->getAtomicCls('ck-icn')} {$fieldHelpers->getCustomClasses('ck-icn')}" />
          </svg>
        </span>
        <span
          {$fieldHelpers->getCustomAttributes('ct')}
          class="{$fieldHelpers->getAtomicCls('ct')} {$fieldHelpers->getCustomClasses('ct')}"
        >
    {$other}
        </span>
      </label>
      <div data-oinp-wrp class="{$fieldHelpers->getAtomicCls('other-inp-wrp')}">
        <input
          data-bf-other-inp="{$rowID}-chk-{$optCount}"
          type="text"
          class="{$fieldHelpers->getAtomicCls('other-inp')}"
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
        class="{$fieldHelpers->getAtomicCls('cc')} {$fieldHelpers->getCustomClasses('cc')}"
      >
        {$radioBoxOptions}
      </div>
RADIOBOX;
  }
}
