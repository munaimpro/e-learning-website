<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

use BitCode\BitForm\Frontend\Form\View\Theme\ThemeBase;

class SectionField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $formViewerInstance,  $nestedLayout, $error = null, $value = null)
  {
    $inputWrapper = new ClassicInputWrapper($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    $input = self::field($field, $rowID, $formID, $formViewerInstance, $nestedLayout, $form_atomic_Cls_map, $value);
    return $inputWrapper->wrapper($input);
  }

  private static function field($field, $rowID, $formID, $formViewerInstance, $nestedLayout, $form_atomic_Cls_map, $value)
  {
    $fieldHelpers = new ClassicFieldHelpers($field, $rowID, $form_atomic_Cls_map);

    $themeBase = new ThemeBase();
    $fieldHtml = '';
    if (isset($nestedLayout->lg)) {
      foreach ($nestedLayout->lg as $row) {
        $fieldHtml .= $themeBase->inputWrapper($formViewerInstance, $row->i);
      }
    }

    return <<<SECTIONFIELD
    <div 
      {$fieldHelpers->getCustomAttributes('inp-fld-wrp')}
      class="{$fieldHelpers->getAtomicCls('inp-fld-wrp')} {$fieldHelpers->getCustomClasses('inp-fld-wrp')}"
    >
        <div class="_frm-b{$formID} section-grid">
          {$fieldHtml}
        </div>
    </div>
SECTIONFIELD;
  }
}
