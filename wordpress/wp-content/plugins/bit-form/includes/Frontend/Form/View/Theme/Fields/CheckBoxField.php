<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

use BitCode\BitForm\Core\Util\FrontendHelpers;

class CheckBoxField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value)
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
          $disabled = 'disabled';
        }

        if (isset($field->mx) && !empty($field->valid->disableOnMax) && !$check && ((int) $field->mx <= count($defaultValues))) {
          $disabled = 'disabled';
        }

        $checkBoxOptions .= <<<CHECKBOXOPTIONS
        <div
          {$fieldHelpers->getCustomAttributes('cw')}
          class="{$fieldHelpers->getAtomicCls('cw')} {$fieldHelpers->getCustomClasses('cw')}"
        >
          <input
            id="{$rowID}-{$contentCount}-chk-{$key}"
            type="checkbox"
            class="{$fieldHelpers->getAtomicCls('ci')} {$fieldHelpers->getCustomClasses('ci')}"
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
            class="{$fieldHelpers->getAtomicCls('cl')} {$fieldHelpers->getCustomClasses('cl')}"
          >
            <span
              {$fieldHelpers->getCustomAttributes('bx')}
              data-bx
              class="{$fieldHelpers->getAtomicCls('bx')} {$fieldHelpers->getCustomClasses('bx')}"
            >
              <svg width="12" height="10" viewBox="0 0 12 10" class="{$fieldHelpers->getAtomicCls('svgwrp')} {$fieldHelpers->getCustomClasses('svgwrp')}">
                <use data-ck-icn href="$href" class="{$fieldHelpers->getAtomicCls('ck-icn')} {$fieldHelpers->getCustomClasses('ck-icn')}" />
              </svg>
            </span>
            <span
              {$fieldHelpers->getCustomAttributes('ct')}
              class="{$fieldHelpers->getAtomicCls('ct')} {$fieldHelpers->getCustomClasses('ct')}"
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
    $inpReq = isset($field->valid->otherOptReq) ? ($field->valid->otherOptReq ? 'required' : '') : '';
    $other = __('Other...', 'bit-form');
    $inputPh = isset($field->otherInpPh) ? "placeholder='{$fieldHelpers->esc_attr($field->otherInpPh)}'" : "placeholder='{$other}'";
    if (property_exists($field, 'addOtherOpt') && $field->addOtherOpt) {
      $checkBoxOptions .= <<<CHECKBOXOPTIONS
      <div
          {$fieldHelpers->getCustomAttributes('cw')}
          class="{$fieldHelpers->getAtomicCls('cw')} {$fieldHelpers->getCustomClasses('cw')}"
        >
          <input
            id="{$rowID}-{$contentCount}-chk-{$optCount}"
            data-oopt="{$rowID}"
            type="checkbox"
            class="{$fieldHelpers->getAtomicCls('ci')} {$fieldHelpers->getCustomClasses('ci')}"
            {$disabled}
            value=""
            {$name}
          />
          <label
            {$fieldHelpers->getCustomAttributes('cl')}
            data-cl
            for="{$rowID}-{$contentCount}-chk-{$optCount}"
            class="{$fieldHelpers->getAtomicCls('cl')} {$fieldHelpers->getCustomClasses('cl')}"
          >
            <span
              {$fieldHelpers->getCustomAttributes('ck')}
              data-bx
              class="{$fieldHelpers->getAtomicCls('bx')} {$fieldHelpers->getAtomicCls('ck')} {$fieldHelpers->getCustomClasses('ck')}"
            >
              <svg width="12" height="10" viewBox="0 0 12 10" class="{$fieldHelpers->getAtomicCls('svgwrp')} {$fieldHelpers->getCustomClasses('svgwrp')}">
                <use data-ck-icn href="#$rowID-ck-svg"  class="{$fieldHelpers->getAtomicCls('ck-icn')} {$fieldHelpers->getCustomClasses('ck-icn')}" />
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
              data-bf-other-inp='{$rowID}-chk-{$optCount}'
              type="text"
              class="{$fieldHelpers->getAtomicCls('other-inp')} {$fieldHelpers->getCustomClasses('other-inp')}"
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
  class="{$fieldHelpers->getAtomicCls('cc')} {$fieldHelpers->getCustomClasses('cc')}"
>
  <svg class="{$fieldHelpers->getAtomicCls('cks')}">
    <symbol id="{$rowID}-ck-svg" viewBox="0 0 12 10">
      <polyline
        class="{$fieldHelpers->getAtomicCls('ck-svgline')}"
        points="1.5 6 4.5 9 10.5 1"
      ></polyline>
    </symbol>
  </svg>
  {$checkBoxOptions}
</div>
CHECKBOXFIELD;
  }
}
