<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

use BitCode\BitForm\Core\Util\FrontendHelpers;

class RatingField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $inputWrapper = new ClassicInputWrapper($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    $input = self::field($field, $rowID, $form_atomic_Cls_map, $value);
    return $inputWrapper->wrapper($input);
  }

  private static function field($field, $rowID, $form_atomic_Cls_map, $value)
  {
    $fieldHelpers = new ClassicFieldHelpers($field, $rowID, $form_atomic_Cls_map);

    $name = $fieldHelpers->name();
    $req = $fieldHelpers->required();

    $bfFrontendFormIds = FrontendHelpers::$bfFrontendFormIds;
    $contentCount = count($bfFrontendFormIds);

    $ratingOption = '';

    $checkedIndex = '';

    if (isset($field->opt)) {
      $defaultValue = isset($field->val) ? $field->val : '';
      foreach ($field->opt as $key => $opt) {
        if ($defaultValue && $defaultValue === $opt->val) {
          $checkedIndex = $key;
        } elseif (isset($opt->check) && true === $opt->check) {
          $checkedIndex = $key;
        }
      }

      $checkedCls = null;

      foreach ($field->opt as $key => $opt) {
        $val = $opt->val;
        $img = $opt->img;
        $lbl = $opt->lbl;
        $checked = '';

        // if ($defaultValue && $opt->val === $defaultValue) {
        //   $checked = "checked='checked'";
        // } elseif (isset($opt->check) && true === $opt->check) {
        //   $checked = "checked='{$opt->check}'";
        // } else {
        //   $checked = '';
        // }

        if ($defaultValue) {
          if ($opt->val === $defaultValue) {
            $checked = "checked='1'";
          }
        } else {
          if (isset($opt->check) && true === $opt->check) {
            $checked = "checked='{$opt->check}'";
          } else {
            $checked = '';
          }
        }

        if (!empty($checkedIndex) && $key <= $checkedIndex) {
          $checkedCls = $rowID . '-rating-selected';
        } else {
          $checkedCls = null;
        }
        $ratingOption .= <<<RATINGOPTION
        <label
          class="{$fieldHelpers->getAtomicCls('rating-lbl')} {$fieldHelpers->getCustomClasses('rating-lbl')}"
          for="{$rowID}-{$contentCount}-rating-{$key}"
          {$fieldHelpers->getCustomAttributes('rating-lbl')}
          data-indx="{$key}"
        >
          <input
            type="radio"
            class="{$fieldHelpers->getAtomicCls('rating-input')} {$fieldHelpers->getCustomClasses('rating-input')}"
            {$name}
            value={$val}
            aria-label="{$lbl}"
            id="{$rowID}-{$contentCount}-rating-{$key}"
            {$checked}
            {$req}
            {$fieldHelpers->getCustomAttributes('rating-input')}
          />
          <img
            class="{$fieldHelpers->getAtomicCls('rating-img')} {$fieldHelpers->getCustomClasses('rating-img')} {$checkedCls}"
            src="{$img}"
            alt="{$lbl}"
            aria-label="{$lbl}"
            {$fieldHelpers->getCustomAttributes('rating-img')}
          />
        </label>
RATINGOPTION;
      }
    }

    return <<<RATINGFIELD
    <div 
      {$fieldHelpers->getCustomAttributes('inp-fld-wrp')}
      class="{$fieldHelpers->getAtomicCls('inp-fld-wrp')} {$fieldHelpers->getCustomClasses('inp-fld-wrp')}"
    >
      <div 
        {$fieldHelpers->getCustomAttributes('rating-wrp')}
        class="{$fieldHelpers->getAtomicCls('rating-wrp')} {$fieldHelpers->getCustomClasses('rating-wrp')}"
        tabindex="0"
      >
        {$ratingOption}
      </div>
      <span 
        class="{$fieldHelpers->getAtomicCls('rating-msg')} {$fieldHelpers->getCustomClasses('rating-msg')}"
        {$fieldHelpers->getCustomAttributes('rating-msg')}
      >
      </span>
    </div>
RATINGFIELD;
  }
}
