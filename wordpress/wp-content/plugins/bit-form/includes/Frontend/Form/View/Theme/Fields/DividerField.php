<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

class DividerField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $fieldHelpers = new ClassicFieldHelpers($field, $rowID, $form_atomic_Cls_map);

    return <<<DIVIDERFIELD
    <div
      {$fieldHelpers->getCustomAttributes('fld-wrp')}
      class="{$fieldHelpers->getAtomicCls('fld-wrp')} {$fieldHelpers->getCustomClasses('fld-wrp')}"
    >
      <div
        {$fieldHelpers->getCustomAttributes('divider')}
        class="{$fieldHelpers->getAtomicCls('divider')} {$fieldHelpers->getCustomClasses('divider')}"
      ></div>
    </div>
DIVIDERFIELD;
  }
}
