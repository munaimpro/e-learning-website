<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

use BitCode\BitForm\Core\Util\FrontendHelpers;

class DecisionBoxField
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

    $suggestions = '';
    $req = $fieldHelpers->required();
    $disabled = '';
    $readonly = '';
    $name = $fieldHelpers->name();
    $value = '';
    $bfFrontendFormIds = FrontendHelpers::$bfFrontendFormIds;
    $contentCount = count($bfFrontendFormIds);
    if ($fieldHelpers->property_exists_nested($field, 'msg->checked')) {
      $value = "value='{$field->msg->checked}'";
    }
    $checked = '';

    if ($fieldHelpers->property_exists_nested($field, 'valid->disabled', true)) {
      $disabled = 'disabled';
    }

    if ($fieldHelpers->property_exists_nested($field, 'valid->checked', true)) {
      $checked = 'checked';
    }

    if ($fieldHelpers->property_exists_nested($field, 'valid->readonly', true)) {
      $readonly = 'readonly';
    }

    $lbl = '';
    if (isset($field->lbl) && !empty($field->lbl)) {
      $lbl = $field->lbl;
    } elseif ($fieldHelpers->property_exists_nested($field, 'info->lbl') && !empty($field->info->lbl)) {
      $lbl = $field->info->lbl;
    }

    return <<<DECISIONBOXFIELD
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

        <div
          {$fieldHelpers->getCustomAttributes('cw')}
          class="{$fieldHelpers->getAtomicCls('cw')} {$fieldHelpers->getCustomClasses('cw')}"
        >
          <input
            id="{$rowID}-{$contentCount}-decision"
            type="checkbox"
            class="{$fieldHelpers->getAtomicCls('ci')}"
            {$disabled}
            {$readonly}
            {$req}
            {$name}
            {$checked}
            {$value}
          />
          <label
            {$fieldHelpers->getCustomAttributes('cl')}
            data-cl
            for="{$rowID}-{$contentCount}-decision"
            class="{$fieldHelpers->getAtomicCls('cl')}"
          >
            <span
              {$fieldHelpers->getCustomAttributes('bx')}
              data-bx
              class="{$fieldHelpers->getAtomicCls('bx')} {$fieldHelpers->getAtomicCls('bx')}"
            >
              <svg width="12" height="10" viewBox="0 0 12 10" class="{$fieldHelpers->getAtomicCls('svgwrp')}">
                <use data-ck-icn href="#{$rowID}-ck-svg" class="{$fieldHelpers->getAtomicCls('ck-icn')}" />
              </svg>
            </span>
            <span
              {$fieldHelpers->getCustomAttributes('ct')}
              class="{$fieldHelpers->getAtomicCls('ct')} {$fieldHelpers->getCustomClasses('ct')}"
            >
              {$fieldHelpers->kses_post($fieldHelpers->renderHTMR($lbl))}
            </span>
          </label>
        </div>
      </div>
DECISIONBOXFIELD;
  }
}
