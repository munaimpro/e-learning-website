<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

use BitCode\BitForm\Frontend\Form\View\Theme\ThemeBase;

class RepeaterField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $formViewerInstance,  $nestedLayout, $error = null, $value = null)
  {
    $inputWrapper = new ClassicInputWrapper($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    $input = self::field($field, $rowID, $field_name, $formID, $formViewerInstance, $nestedLayout, $form_atomic_Cls_map, $value);
    return $inputWrapper->wrapper($input);
  }

  private static function field($field, $rowID, $field_name, $formID, $formViewerInstance, $nestedLayout, $form_atomic_Cls_map, $value)
  {
    $fieldHelpers = new ClassicFieldHelpers($field, $rowID, $form_atomic_Cls_map);

    $addBtnMarkup = '';
    $addToEndBtnMarkup = '';

    if (isset($field->addBtn->show) && $field->addBtn->show) {
      $addBtnPreIcn = $fieldHelpers->icon('addBtnPreIcn', 'rpt-add-btn-pre-i');
      $addBtnSufIcn = $fieldHelpers->icon('addBtnSufIcn', 'rpt-add-btn-suf-i');
      $addBtnMarkup = <<<ADDBTN
        <button
          {$fieldHelpers->getCustomAttributes('rpt-add-btn')}
          class="{$fieldHelpers->getAtomicCls('rpt-add-btn')} {$fieldHelpers->getCustomClasses('rpt-add-btn')}"
          type="{$field->addBtn->btnTyp}"
          data-parent-field-name="{$field->fieldName}"
        >
          {$addBtnPreIcn}
          {$fieldHelpers->kses_post($fieldHelpers->renderHTMR($field->addBtn->txt))}
          {$addBtnSufIcn}
        </button>
ADDBTN;
    };

    if (isset($field->addToEndBtn->show) && $field->addToEndBtn->show) {
      $addToEndBtnPreIcn = $fieldHelpers->icon('addToEndBtnPreIcn', 'add-to-end-btn-pre-i');
      $addToEndBtnSufIcn = $fieldHelpers->icon('addToEndBtnSufIcn', 'add-to-end-btn-suf-i');
      $addToEndBtnMarkup = <<<ADDTOENDBTN
        <div 
          {$fieldHelpers->getCustomAttributes('add-to-end-btn-wrp')}
          class="{$fieldHelpers->getAtomicCls('add-to-end-btn-wrp')} {$fieldHelpers->getCustomClasses('add-to-end-btn-wrp')}"
        >       
          <button
            {$fieldHelpers->getCustomAttributes('add-to-end-btn')}
            class="{$fieldHelpers->getAtomicCls('add-to-end-btn')} {$fieldHelpers->getCustomClasses('add-to-end-btn')}"
            type="{$field->addToEndBtn->btnTyp}"
            data-parent-field-name="{$field->fieldName}"
          >
            {$addToEndBtnPreIcn}
            {$fieldHelpers->kses_post($fieldHelpers->renderHTMR($field->addToEndBtn->txt))}
            {$addToEndBtnSufIcn}
          </button>
        </div>
ADDTOENDBTN;
    };

    $removeBtnPreIcn = $fieldHelpers->icon('removeBtnPreIcn', 'rpt-rmv-btn-pre-i');
    $removeBtnSufIcn = $fieldHelpers->icon('removeBtnSufIcn', 'rpt-rmv-btn-suf-i');

    $themeBase = new ThemeBase();
    $fieldHtml = '';
    if (isset($nestedLayout->lg)) {
      foreach ($nestedLayout->lg as $row) {
        $fieldHtml .= $themeBase->inputWrapper($formViewerInstance, $row->i);
      }
    }

    $repeatableWrap = <<<RPTABLEWRAP
      <div 
        {$fieldHelpers->getCustomAttributes('rpt-wrp')}
        class="{$fieldHelpers->getAtomicCls('rpt-wrp')} {$fieldHelpers->getCustomClasses('rpt-wrp')}"
      >
        <div 
          {$fieldHelpers->getCustomAttributes('rpt-grid-wrp')}
          class="{$fieldHelpers->getAtomicCls('rpt-grid-wrp')} {$fieldHelpers->getCustomClasses('rpt-grid-wrp')}"
        >   
          <div class="_frm-b{$formID} repeater-grid">
            {$fieldHtml}
          </div>
        </div>
        <div 
          {$fieldHelpers->getCustomAttributes('pair-btn-wrp')}
          class="{$fieldHelpers->getAtomicCls('pair-btn-wrp')} {$fieldHelpers->getCustomClasses('pair-btn-wrp')}"
        >
          {$addBtnMarkup}
          <button
            {$fieldHelpers->getCustomAttributes('rpt-rmv-btn')}
            class="{$fieldHelpers->getAtomicCls('rpt-rmv-btn')} {$fieldHelpers->getCustomClasses('rpt-rmv-btn')}"
            type="{$field->removeBtn->btnTyp}"
            data-parent-field-name="{$field->fieldName}"
          >
            {$removeBtnPreIcn}
            {$fieldHelpers->renderHTMR($field->removeBtn->txt)}
            {$removeBtnSufIcn}
          </button>
        </div>
      </div>
RPTABLEWRAP;

    $defaultRow = isset($field->defaultRow) ? intval($field->defaultRow) : 1;
    if (isset($field->maxRow) && $defaultRow > intval($field->maxRow)) {
      $defaultRow = intval($field->maxRow);
    }

    if (isset($field->minRow) && $defaultRow < intval($field->minRow)) {
      $defaultRow = intval($field->minRow);
    }
    $repeatedRow = $repeatableWrap;
    while ($defaultRow > 1) {
      $repeatedRow .= $repeatableWrap;
      $defaultRow--;
    }

    return <<<SECTIONFIELD
    <div 
      {$fieldHelpers->getCustomAttributes('inp-fld-wrp')}
      class="{$fieldHelpers->getAtomicCls('inp-fld-wrp')} {$fieldHelpers->getCustomClasses('inp-fld-wrp')}"
    >
      <div 
        {$fieldHelpers->getCustomAttributes('rpt-fld-wrp')}
        class="{$fieldHelpers->getAtomicCls('rpt-fld-wrp')} {$fieldHelpers->getCustomClasses('rpt-fld-wrp')}"
      >
        {$repeatedRow}
        {$addToEndBtnMarkup}
        <input
          type="text"
          class="d-none"
          title="Rpeater Index Hidden Input"
          name="{$fieldHelpers->esc_attr($field->fieldName . '-repeat-index')}"
          value=""
        />
      </div>
    </div>
SECTIONFIELD;
  }
}
