<?php

namespace BitCode\BitForm\Frontend\Form\View\Conversational\Fields;

use BitCode\BitForm\Frontend\Form\View\FieldHelpers;

class ConversationalFieldHelpers extends FieldHelpers
{
  private $_formId;

  public function __construct($formId, $field, $fk, $form_atomic_Cls_map = null)
  {
    parent::__construct($field, $fk, $form_atomic_Cls_map);
    $this->_formId = $formId;
  }

  public function getClassWithFieldKey($element)
  {
    $formId = $this->getFormId();
    $formId = !empty($formId) ? $formId : '';
    return "{$this->getFk()}-{$element}";
  }

  public function getConversationalCls($element)
  {
    $formId = $this->getFormId();
    $formId = !empty($formId) ? $formId : '';
    return "bc{$formId}-{$element}";
  }

  public function getConversationalMultiCls($element)
  {
    $conversationalCls = $this->getConversationalCls($element);
    $clsWithFieldKey = $this->getClassWithFieldKey($element);
    return "$conversationalCls $clsWithFieldKey";
  }

  // getter & setter function for $_formId
  public function getFormId()
  {
    return $this->_formId;
  }

  public function setFormId($formId)
  {
    $this->_formId = $formId;
  }
}
