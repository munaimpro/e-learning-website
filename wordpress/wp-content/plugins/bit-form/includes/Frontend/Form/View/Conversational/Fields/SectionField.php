<?php

namespace BitCode\BitForm\Frontend\Form\View\Conversational\Fields;

use BitCode\BitForm\Frontend\Form\View\Conversational\DefaultConversationalTheme;

class SectionField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $formViewerInstance,  $nestedLayout, $error = null, $value = null)
  {
    $inputWrapper = new ConversationalInputWrapper($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    $input = self::field($field, $rowID, $formID, $formViewerInstance, $nestedLayout, $form_atomic_Cls_map, $value);
    return $inputWrapper->wrapper($input);
  }

  private static function field($field, $rowID, $formID, $formViewerInstance, $nestedLayout, $form_atomic_Cls_map, $value)
  {
    $fieldHelpers = new ConversationalFieldHelpers($formID, $field, $rowID, $form_atomic_Cls_map);
    $conversationalTheme = new DefaultConversationalTheme($formViewerInstance->getFormInfo()->conversationalSettings);
    $fieldHtml = '';
    if (isset($nestedLayout->lg)) {
      foreach ($nestedLayout->lg as $row) {
        $fieldHtml .= $conversationalTheme->inputWrapper($formViewerInstance, $row->i, true);
      }
    }

    return <<<SECTIONFIELD
    <div 
      {$fieldHelpers->getCustomAttributes('inp-fld-wrp')}
      class="{$fieldHelpers->getConversationalMultiCls('inp-fld-wrp')} {$fieldHelpers->getConversationalCls('inner-grid-fld-wrp')} {$fieldHelpers->getCustomClasses('inp-fld-wrp')}"
    >
        <div class="_frm-b{$formID} section-grid">
          {$fieldHtml}
        </div>
    </div>
SECTIONFIELD;
  }
}
