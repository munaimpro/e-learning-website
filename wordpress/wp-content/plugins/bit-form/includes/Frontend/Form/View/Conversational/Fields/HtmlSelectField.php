<?php

namespace BitCode\BitForm\Frontend\Form\View\Conversational\Fields;

use BitCode\BitForm\Core\Util\FrontendHelpers;

class HtmlSelectField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $inputWrapper = new ConversationalInputWrapper($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    $input = self::field($field, $rowID, $form_atomic_Cls_map, $formID, $value);
    return $inputWrapper->wrapper($input);
  }

  private static function checkSelected($opt, $val)
  {
    $selected = '';
    if (isset($opt->val) && $opt->val === $val) {
      $selected = 'selected';
    } elseif (isset($opt->lbl) && $opt->lbl === $val) {
      $selected = 'selected';
    } elseif (property_exists($opt, 'check')) {
      $selected = 'selected';
    }
    return $selected;
  }

  private static function field($field, $rowID, $form_atomic_Cls_map, $formID, $val = null)
  {
    $fieldHelpers = new ConversationalFieldHelpers($formID, $field, $rowID, $form_atomic_Cls_map);
    $disabled = $fieldHelpers->disabled();
    $readonly = $fieldHelpers->readonly();
    $name = $fieldHelpers->name();
    $value = '';
    if ($val) {
      $value = $val;
    } elseif (isset($field->val)) {
      $value = $field->val;
    }

    $readonlyCls = $readonly ? 'readonly' : '';
    $phHide = '';
    $optionsHTML = '';
    $bfFrontendFormIds = FrontendHelpers::$bfFrontendFormIds;
    $contentCount = count($bfFrontendFormIds);
    if ($fieldHelpers->property_exists_nested($field, 'phHide', true)) {
      $phHide = <<<PHHIDE
        <option
          {$fieldHelpers->getCustomAttributes('slct-optn')}
          class="{$fieldHelpers->getConversationalCls('slct-optn')} {$fieldHelpers->getCustomClasses('slct-optn')}"
          value
        >
          {$fieldHelpers->kses_post($field->ph)}
        </option>
PHHIDE;
    }

    if (property_exists($field, 'opt')) {
      foreach ($field->opt as $opt) {
        $disabled = property_exists($opt, 'disabled') ? 'disabled' : '';
        if (property_exists($opt, 'type')) {
          $optionsHTML .= <<<OPTGRP
            <optgroup
              {$fieldHelpers->getCustomAttributes('slct-opt-grp')}
              class="{$fieldHelpers->getConversationalCls('slct-opt-grp')} {$fieldHelpers->getCustomClasses('slct-opt-grp')}"
              label="{$fieldHelpers->esc_attr($opt->title)}"
              {$disabled}
            >
OPTGRP;
          foreach ($opt->childs as $child) {
            $val = isset($child->val) ? $child->val : $child->lbl;
            $selected = self::checkSelected($child, $value);
            $disabled = property_exists($child, 'disabled') ? 'disabled' : '';
            $optionsHTML .= <<<OPT
              <option
                {$fieldHelpers->getCustomAttributes('slct-optn')}
                class="{$fieldHelpers->getConversationalCls('slct-optn')} {$fieldHelpers->getCustomClasses('slct-optn')}"
                value="{$fieldHelpers->esc_attr($val)}"
                {$selected}
                {$disabled}
              >
                {$fieldHelpers->kses_post($child->lbl)}
              </option>
OPT;
          }
          $optionsHTML .= '</optgroup>';
        } else {
          $selected = self::checkSelected($opt, $value);
          $val = isset($opt->val) ? $opt->val : $opt->lbl;
          $optionsHTML .= <<<OPT
            <option
              {$fieldHelpers->getCustomAttributes('slct-optn')}
              class="{$fieldHelpers->getConversationalCls('slct-optn')} {$fieldHelpers->getCustomClasses('slct-optn')}"
              value="{$fieldHelpers->esc_attr($val)}"
              {$selected}
              {$disabled}
            >
              {$fieldHelpers->kses_post($opt->lbl)}
            </option>
OPT;
        }
      }
    }

    return <<<HTMLSELECTFIELD
    <div 
      {$fieldHelpers->getCustomAttributes('inp-fld-wrp')}
      class="{$fieldHelpers->getConversationalCls('inp-fld-wrp')} {$fieldHelpers->getCustomClasses('inp-fld-wrp')}"
    >
      <select
        {$fieldHelpers->getCustomAttributes('fld')}
        id="{$rowID}-{$contentCount}"
        class="{$fieldHelpers->getConversationalCls('fld')} {$fieldHelpers->getCustomClasses('fld')} {$readonlyCls}"
        {$readonly}
        {$disabled}
        {$name}
        value="{$fieldHelpers->esc_attr($value)}"
      >
        {$phHide}
        {$optionsHTML}
      </select>
    </div>
HTMLSELECTFIELD;
  }
}
