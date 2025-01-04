<?php

namespace BitCode\BitForm\Frontend\Form\View;

use BitCode\BitForm\Core\Util\FrontendHelpers;

class InputWrapper
{
  private $_fieldData;
  private $_fieldKey;
  private $_fieldName;
  private $_formID;
  private $_error;
  private $_value;
  private $_fieldHelpers;

  public function __construct($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $this->_fieldData = $field;
    $this->_fieldKey = $rowID;
    $this->_fieldName = $field_name;
    $this->_formID = $formID;
    $this->_error = $error;
    $this->_value = $value;
    $this->_fieldHelpers = new FieldHelpers($field, $rowID, $form_atomic_Cls_map);
  }

  public function wrapper($field, $noLabel = false, $noErrMsg = false)
  {
    $labelWrapper = !$noLabel ? $this->labelWrapper() : '';
    $helperText = $this->helperText();
    $error = !$noErrMsg ? $this->errorMessages() : '';
    $inputWrapper = <<<INPUTWRAPPER
    <div 
      {$this->_fieldHelpers->getCustomAttributes('fld-wrp')}
      class="{$this->_fieldHelpers->getAtomicCls('fld-wrp')} {$this->_fieldHelpers->getCustomClasses('fld-wrp')}"
    >
      {$labelWrapper}
      <div 
        {$this->_fieldHelpers->getCustomAttributes('inp-wrp')}
        class="{$this->_fieldHelpers->getAtomicCls('inp-wrp')} {$this->_fieldHelpers->getCustomClasses('inp-wrp')}"
      > 
        {$field}
        {$helperText}
        {$error}
      </div>
    </div>
INPUTWRAPPER;

    return $inputWrapper;
  }

  private function labelWrapper()
  {
    $_label = '';
    $_lblPreIcn = $this->_fieldHelpers->icon('lblPreIcn', 'lbl-pre-i');
    $_lblSufIcn = $this->_fieldHelpers->icon('lblSufIcn', 'lbl-suf-i');
    $_reqPre = '';
    $_reqPost = '';
    $_lbl = '';
    $_subtitle = '';
    $_subtitleText = '';
    $_subtitlePreIcn = $this->_fieldHelpers->icon('subTlePreIcn', 'sub-titl-pre-i');
    $_subtitlePostIcn = $this->_fieldHelpers->icon('subTleSufIcn', 'sub-titl-suf-i');
    if (property_exists($this->_fieldData, 'lbl')) {
      $replaceToBackSlash = $this->_fieldHelpers->replaceToBackSlash($this->_fieldData->lbl);
      $_lbl = wp_kses_post($this->_fieldHelpers->renderHTMR($replaceToBackSlash));
    }

    if (property_exists($this->_fieldData, 'subtitle')) {
      $replaceToBackSlash = $this->_fieldHelpers->replaceToBackSlash($this->_fieldData->subtitle);
      $_subtitleText = wp_kses_post($this->_fieldHelpers->renderHTMR($replaceToBackSlash));
    }

    // for pre required icon
    if (
      $this->_fieldHelpers->property_exists_nested($this->_fieldData, 'valid->req')
      && $this->_fieldHelpers->property_exists_nested($this->_fieldData, 'valid->reqShow', true)
      && $this->_fieldHelpers->property_exists_nested($this->_fieldData, 'valid->reqPos', 'before')
    ) {
      $_reqPre = <<<REQPRE
      <span 
        {$this->_fieldHelpers->getCustomAttributes('req-smbl')}
        class="{$this->_fieldHelpers->getAtomicCls('req-smbl')} {$this->_fieldHelpers->getCustomClasses('req-smbl')}" 
      >
        *
      </span>
REQPRE;
    }
    // for post required icon
    if (
      $this->_fieldHelpers->property_exists_nested($this->_fieldData, 'valid->req')
      && $this->_fieldHelpers->property_exists_nested($this->_fieldData, 'valid->reqShow', true)
      && $this->_fieldHelpers->property_exists_nested($this->_fieldData, 'valid->reqPos', 'before', 1)
    ) {
      $_reqPost = <<<REQPOST
      <span 
        {$this->_fieldHelpers->getCustomAttributes('req-smbl')}
        class="{$this->_fieldHelpers->getAtomicCls('req-smbl')} {$this->_fieldHelpers->getCustomClasses('req-smbl')}" 
      >
        *
      </span>
REQPOST;
    }

    // for Label
    if (
      property_exists($this->_fieldData, 'lbl')
      // && isset($this->_fieldData->valid->hideLbl)
      // && $this->_fieldData->valid->hideLbl !== true
    ) {
      $bfFrontendFormIds = FrontendHelpers::$bfFrontendFormIds;
      $contentCount = count($bfFrontendFormIds);
      $_label = <<<LABEL
      <label 
        {$this->_fieldHelpers->getCustomAttributes('lbl')} 
        class="{$this->_fieldHelpers->getAtomicCls('lbl')} {$this->_fieldHelpers->getCustomClasses('lbl')}"
        for="{$this->_fieldKey}-{$contentCount}">
        {$_reqPre}
        {$_lblPreIcn}
        {$_lbl}
        {$_lblSufIcn}
        {$_reqPost}
      </label>
LABEL;
    }

    // for subtitle
    if (property_exists($this->_fieldData, 'subtitle') && $this->_fieldData->subtitleHide) {
      $_subtitle = <<<SUBTITLE
      <div 
        {$this->_fieldHelpers->getCustomAttributes('sub-titl')}
        class="{$this->_fieldHelpers->getAtomicCls('sub-titl')} {$this->_fieldHelpers->getCustomClasses('sub-titl')}" 
      >
        {$_subtitlePreIcn}
        {$_subtitleText}
        {$_subtitlePostIcn}
      </div>
SUBTITLE;
    }
    return <<<LABELWRAPPER
      <div 
        {$this->_fieldHelpers->getCustomAttributes('lbl-wrp')}
        class="{$this->_fieldHelpers->getAtomicCls('lbl-wrp')} {$this->_fieldHelpers->getCustomClasses('lbl-wrp')}" 
      >
        {$_label}
        {$_subtitle}
      </div>
LABELWRAPPER;
  }

  private function helperText()
  {
    $_helperTxtPreIcn = $this->_fieldHelpers->icon('hlpPreIcn', 'hlp-txt-pre-i');
    $_helperTxtSufIcn = $this->_fieldHelpers->icon('hlpSufIcn', 'hlp-txt-suf-i');
    $_helperTxt = '';

    if (isset($this->_fieldData->helperTxt)) {
      $_helperTxt = wp_kses_post($this->_fieldHelpers->renderHTMR($this->_fieldData->helperTxt));
    }

    if (property_exists($this->_fieldData, 'helperTxt') && $this->_fieldData->helperTxt) {
      $_helperTxt = <<<HELPERTEXT
      <div 
        {$this->_fieldHelpers->getCustomAttributes('hlp-txt')}
        class="{$this->_fieldHelpers->getAtomicCls('hlp-txt')} {$this->_fieldHelpers->getCustomClasses('hlp-txt')}" 
      >
        {$_helperTxtPreIcn}
        {$_helperTxt}
        {$_helperTxtSufIcn}
      </div>
HELPERTEXT;
    }

    // for helper text
    return $_helperTxt;
  }

  public function errorMessages()
  {
    $_errorPreIcn = $this->_fieldHelpers->icon('errPreIcn', 'err-txt-pre-i');
    $_errorSufIcn = $this->_fieldHelpers->icon('errSufIcn', 'err-txt-suf-i');
    $_error = '';
    $_style = 'opacity: 0 !important; height: 0px !important;';

    if ($this->_error) {
      $_error = wp_kses_post($this->_error);
      $_style = 'margin-top: 5px !important; height: 9px !important;';
    }

    $errMsgStart = '';
    $errMsgEnd = '';
    $msgStyle = "style='display: none !important;'";
    $errMsgAtomicCls = $this->_fieldHelpers->getAtomicCls('err-msg');
    $errMsgCustomCls = $this->_fieldHelpers->getCustomClasses('err-msg');
    $errMsgCustomAttr = $this->_fieldHelpers->getCustomAttributes('err-msg');
    if (!(empty($_errorPreIcn) && empty($_errorSufIcn))) {
      $errMsgStart = "<div {$errMsgCustomAttr} class='{$errMsgAtomicCls} {$errMsgCustomCls}' {$msgStyle}>";
      $errMsgEnd = '</div>';
      $msgStyle = '';
      $errMsgAtomicCls = '';
      $errMsgCustomCls = '';
      $errMsgCustomAttr = '';
    }

    return <<<ERRORMESSAGES
    <div class='{$this->_fieldHelpers->getAtomicCls('err-wrp')}' style="{$_style}">
    <div class='{$this->_fieldHelpers->getAtomicCls('err-inner')}'>
      {$errMsgStart}
        {$_errorPreIcn}
        <div {$this->_fieldHelpers->getCustomAttributes('err-txt')} {$errMsgCustomAttr} class="{$errMsgAtomicCls} {$errMsgCustomCls} {$this->_fieldHelpers->getAtomicCls('err-txt')} {$this->_fieldHelpers->getCustomClasses('err-txt')}" {$msgStyle}>{$_error}</div>
        {$_errorSufIcn}
      {$errMsgEnd}
      </div>
    </div>
ERRORMESSAGES;
  }
}
