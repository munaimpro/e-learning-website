<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

use BitCode\BitForm\Core\Util\FrontendHelpers;

class FileUploadField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $inputWrapper = new ClassicInputWrapper($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    $input = self::field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    return $inputWrapper->wrapper($input);
  }

  private static function field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $fh = new ClassicFieldHelpers($field, $rowID, $form_atomic_Cls_map);
    $prefixIcn = $fh->icon('prefixIcn', 'pre-i');
    $suffixIcn = $fh->icon('suffixIcn', 'suf-i');
    $name = $fh->name();
    $req = $fh->required();
    $readonlyCls = isset($field->readonly) ? 'readonly' : '';
    $disabledCls = isset($field->disabled) ? 'disabled' : '';
    $btnTxt = isset($field->btnTxt) ? $field->btnTxt : '';
    $showSelectStatus = '';
    $maxSizeSection = '';
    $maxSize = isset($field->config->maxSize) ? $field->config->maxSize : '';
    $sizeUnit = isset($field->config->sizeUnit) ? $field->config->sizeUnit : '';
    $bfFrontendFormIds = FrontendHelpers::$bfFrontendFormIds;
    $contentCount = count($bfFrontendFormIds);

    if ($fh->property_exists_nested($field, 'config->showSelectStatus', true)) {
      $showSelectStatus = <<<SHOWSELSTATUS
      <div
        {$fh->getCustomAttributes('file-select-status')}
        class="{$fh->getAtomicCls('file-select-status')} {$fh->getCustomClasses('file-select-status')}"
      >
        No Choosen File
      </div>
SHOWSELSTATUS;
    }

    if (
      $fh->property_exists_nested($field, 'config->allowMaxSize', true)
      && $fh->property_exists_nested($field, 'config->showMaxSize', true)
      && $fh->property_exists_nested($field, 'config->maxSize')
      && 0 !== $field->config->maxSize
    ) {
      $maxSizeLbl = $fh->property_exists_nested($field, 'config->maxSizeLabel', '', 1) ? $field->config->maxSizeLabel : "(Max {$maxSize} {$sizeUnit})";
      $maxSizeSection = <<<MAXSIZE
      <small
        {$fh->getCustomAttributes('max-size-lbl')}
        class="{$fh->getAtomicCls('max-size-lbl')} {$fh->getCustomClasses('max-size-lbl')}"
      >
      {$fh->esc_html($maxSizeLbl)}
      </small>
MAXSIZE;
    }

    return <<<FILEUPLOADFIELD
    <div 
      {$fh->getCustomAttributes('file-up-container')}
      class="{$fh->getAtomicCls('file-up-container')} {$fh->getCustomClasses('file-up-container')}"
    >
      <div
        {$fh->getCustomAttributes('file-up-wrpr')}
        class="{$fh->getAtomicCls('file-up-wrpr')} {$fh->getCustomClasses('file-up-wrpr')} {$readonlyCls} {$disabledCls}"
      >
        <div
          {$fh->getCustomAttributes('file-input-wrpr')}
          class="{$fh->getAtomicCls('file-input-wrpr')} {$fh->getCustomClasses('file-input-wrpr')}"
        >
          <div
            {$fh->getCustomAttributes('btn-wrpr')}
            class="{$fh->getAtomicCls('btn-wrpr')} {$fh->getCustomClasses('btn-wrpr')}"
          >
            <button
              {$fh->getCustomAttributes('inp-btn')}
              type="button"
              class="{$fh->getAtomicCls('inp-btn')} {$fh->getCustomClasses('inp-btn')}"
            >
             {$prefixIcn}
              <span
                {$fh->getCustomAttributes('btn-txt')}
                class="{$fh->getAtomicCls('btn-txt')} {$fh->getCustomClasses('btn-txt')}"
              >
                {$fh->kses_post($btnTxt)}
              </span>
              {$suffixIcn}
            </button>
            {$showSelectStatus}
            {$maxSizeSection}
            <input
              {$fh->getCustomAttributes('file-upload-input')}
              type="file"
              class="{$fh->getAtomicCls('file-upload-input')} {$fh->getCustomClasses('file-upload-input')}"
              id="{$rowID}-{$contentCount}"
              {$name}
              {$req}
              {$fh->disabled()}
              {$fh->readonly()}
              aria-disabled="true"
              tabindex="-1"
            />
          </div>
          <div 
            {$fh->getCustomAttributes('err-wrp')}
            class="err-wrp {$fh->getCustomClasses('err-wrp')}"
          ></div>
        </div>
      </div>
    </div>
FILEUPLOADFIELD;
  }
}
