<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

use BitCode\BitForm\Frontend\Form\View\InputWrapper;

class ClassicInputWrapper extends InputWrapper
{
  public function __construct($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    parent::__construct($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
  }
}
