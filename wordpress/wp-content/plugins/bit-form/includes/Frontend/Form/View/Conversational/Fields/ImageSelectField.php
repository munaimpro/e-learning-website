<?php

namespace BitCode\BitForm\Frontend\Form\View\Conversational\Fields;

use BitCode\BitForm\Core\Util\FrontendHelpers;

class ImageSelectField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $inputWrapper = new ConversationalInputWrapper($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    $input = self::field($field, $rowID, $form_atomic_Cls_map, $formID, $value);
    return $inputWrapper->wrapper($input);
  }

  private static function field($field, $rowID, $form_atomic_Cls_map, $formID, $value)
  {
    $fieldHelpers = new ConversationalFieldHelpers($formID, $field, $rowID, $form_atomic_Cls_map);

    $name = $fieldHelpers->name();
    $req = $fieldHelpers->required();

    $bfFrontendFormIds = FrontendHelpers::$bfFrontendFormIds;
    $contentCount = count($bfFrontendFormIds);

    $imageOption = '';
    if (!empty($field->val)) {
      $value = $field->val;
    }
    if (!empty($value) && false !== strpos($value, BITFORMS_BF_SEPARATOR)) {
      $defaultValues = explode(BITFORMS_BF_SEPARATOR, $value);
    } else {
      $defaultValues = isset($value) ? explode(',', $value) : [];
    }

    if (isset($field->opt)) {
      $checkedImg = $field->tickImgSrc;
      foreach ($field->opt as $key => $opt) {
        $val = isset($opt->val) ? $opt->val : $opt->lbl;
        $img = $opt->img;
        $lbl = $fieldHelpers->renderHTMR($opt->lbl);
        $checked = '';
        $inpType = $field->inpType;
        $imgAlt = $fieldHelpers->esc_html($lbl);
        $disabled = '';

        if ($fieldHelpers->property_exists_nested($opt, 'check', true)) {
          $checked = 'checked';
        } elseif (in_array($val, $defaultValues)) {
          $checked = 'checked';
        }

        if (isset($field->mx) && !empty($field->valid->disableOnMax) && !$checked && ((int) $field->mx <= count($defaultValues))) {
          $disabled = 'disabled';
        }

        $optLblHide = null;

        if (!$field->optLblHide) {
          $optLblHide = <<<OPTLBLHIDE
          <div
            class="{$fieldHelpers->getConversationalCls('tc')} {$fieldHelpers->getCustomClasses('tc')}"
            {$fieldHelpers->getCustomAttributes('tc')}
          >
            <span
              class="{$fieldHelpers->getConversationalCls('img-title')} {$fieldHelpers->getCustomClasses('img-title')}"
              {$fieldHelpers->getCustomAttributes('img-title')}
            >
              {$fieldHelpers->kses_post($lbl)}
            </span>
        </div>
OPTLBLHIDE;
        } else {
          $optLblHide = '';
        }

        $imageOption .= <<<IMAGESELECT
          <div
            class="{$fieldHelpers->getConversationalCls('inp-opt')} {$fieldHelpers->getCustomClasses('inp-opt')}"
            {$fieldHelpers->getCustomAttributes('inp-opt')}
          >
            <input
              class="{$fieldHelpers->getConversationalCls('img-inp')} {$fieldHelpers->getCustomClasses('img-inp')}"
              type="{$inpType}"
              id="{$rowID}-{$contentCount}-img-wrp-{$key}"
              {$name}
              value="{$fieldHelpers->esc_attr($val)}"
              {$checked}
              {$req}
              {$disabled}
              {$fieldHelpers->getCustomAttributes('img-inp')}
            />
            <label
              for="{$rowID}-{$contentCount}-img-wrp-{$key}"
              class="{$fieldHelpers->getConversationalCls('img-wrp')} {$fieldHelpers->getCustomClasses('img-wrp')}"
              {$fieldHelpers->getCustomAttributes('img-wrp')}
            >
              <span
                class="{$fieldHelpers->getConversationalCls('check-box')} {$fieldHelpers->getCustomClasses('check-box')}"
              {$fieldHelpers->getCustomAttributes('check-box')}
              >
                <img
                  src="{$checkedImg}"
                  alt=""
                  class="{$fieldHelpers->getConversationalCls('check-img')} {$fieldHelpers->getCustomClasses('check-img')}"
                  {$fieldHelpers->getCustomAttributes('check-img')}
                />
              </span>

              <span
                class="{$fieldHelpers->getConversationalCls('img-card-wrp')} {$fieldHelpers->getCustomClasses('img-card-wrp')}"
                {$fieldHelpers->getCustomAttributes('img-card-wrp')}
              >
                <img
                  src="{$img}"
                  alt="{$imgAlt}"
                  aria-label="{$imgAlt}"
                  class="{$fieldHelpers->getConversationalCls('select-img')} {$fieldHelpers->getCustomClasses('select-img')}"
                  {$fieldHelpers->getCustomAttributes('select-img')}
                />
               {$optLblHide}
              </span>
            </label>
          </div>
IMAGESELECT;
      }
    }

    return <<<IMAGESELECTFIELD
    <div
      class="{$fieldHelpers->getConversationalCls('inp-fld-wrp')} {$fieldHelpers->getCustomClasses('inp-fld-wrp')}"
      {$fieldHelpers->getCustomAttributes('inp-fld-wrp')}
    >
      <div
        class="{$fieldHelpers->getConversationalCls('ic')} {$fieldHelpers->getCustomClasses('ic')}"
        {$fieldHelpers->getCustomAttributes('ic')}
        >
        {$imageOption}
      </div>
    </div>
IMAGESELECTFIELD;
  }
}
