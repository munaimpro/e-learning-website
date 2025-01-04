<?php

namespace BitCode\BitForm\Frontend\Form\View\Conversational\Fields;

use BitCode\BitForm\Core\Util\FrontendHelpers;
use BitCode\BitForm\Frontend\Form\View\InputWrapper;

class ConversationalInputWrapper extends InputWrapper
{
  private $_fieldData;
  private $_fieldKey;
  private $_error;
  private $_fieldHelpers;

  public function __construct($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    parent::__construct($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
    $this->_fieldData = $field;
    $this->_fieldKey = $rowID;
    $this->_error = $error;
    $this->_fieldHelpers = new ConversationalFieldHelpers($formID, $field, $rowID, $form_atomic_Cls_map);
  }

  public function wrapper($field, $noLabel = false, $noErrMsg = false)
  {
    $labelWrapper = !$noLabel ? $this->labelWrapper() : '';
    $helperText = $this->helperText();
    $error = !$noErrMsg ? $this->errorMessages() : '';
    $inputWrapper = <<<INPUTWRAPPER
    <div 
      {$this->_fieldHelpers->getCustomAttributes('fld-wrp')}
      class="{$this->_fieldHelpers->getConversationalCls('fld-wrp')} {$this->_fieldHelpers->getCustomClasses('fld-wrp')}"
    > 
      
      {$labelWrapper}
      <div 
        {$this->_fieldHelpers->getCustomAttributes('inp-wrp')}
        class="{$this->_fieldHelpers->getConversationalCls('inp-wrp')} {$this->_fieldHelpers->getCustomClasses('inp-wrp')}"
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
    $_lblPreIcn = $this->conversationalIcon('lblPreIcn', 'lbl-pre-i');
    $_lblSufIcn = $this->conversationalIcon('lblSufIcn', 'lbl-suf-i');
    $_reqPre = '';
    $_reqPost = '';
    $_lbl = '';
    $_subtitle = '';
    $_subtitleText = '';
    $_subtitlePreIcn = $this->conversationalIcon('subTlePreIcn', 'sub-titl-pre-i');
    $_subtitlePostIcn = $this->conversationalIcon('subTleSufIcn', 'sub-titl-suf-i');
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
        class="{$this->_fieldHelpers->getConversationalCls('req-smbl')} {$this->_fieldHelpers->getCustomClasses('req-smbl')}" 
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
        class="{$this->_fieldHelpers->getConversationalCls('req-smbl')} {$this->_fieldHelpers->getCustomClasses('req-smbl')}" 
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
        class="{$this->_fieldHelpers->getConversationalMultiCls('lbl')} {$this->_fieldHelpers->getCustomClasses('lbl')}"
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
        class="{$this->_fieldHelpers->getConversationalMultiCls('sub-titl')} {$this->_fieldHelpers->getCustomClasses('sub-titl')}" 
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
        class="{$this->_fieldHelpers->getConversationalCls('lbl-wrp')} {$this->_fieldHelpers->getCustomClasses('lbl-wrp')}" 
      >
        {$_label}
        {$_subtitle}
      </div>
LABELWRAPPER;
  }

  private function helperText()
  {
    $_helperTxtPreIcn = $this->conversationalIcon('hlpPreIcn', 'hlp-txt-pre-i');
    $_helperTxtSufIcn = $this->conversationalIcon('hlpSufIcn', 'hlp-txt-suf-i');
    $_helperTxt = '';

    if (isset($this->_fieldData->helperTxt)) {
      $_helperTxt = wp_kses_post($this->_fieldHelpers->renderHTMR($this->_fieldData->helperTxt));
    }

    if (property_exists($this->_fieldData, 'helperTxt') && $this->_fieldData->helperTxt) {
      $_helperTxt = <<<HELPERTEXT
      <div 
        {$this->_fieldHelpers->getCustomAttributes('hlp-txt')}
        class="{$this->_fieldHelpers->getConversationalMultiCls('hlp-txt')} {$this->_fieldHelpers->getCustomClasses('hlp-txt')}" 
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
    $_errorPreIcn = $this->conversationalIcon('errPreIcn', 'err-txt-pre-i');
    $_errorSufIcn = $this->conversationalIcon('errSufIcn', 'err-txt-suf-i');
    $_error = '';
    $_style = 'opacity: 0 !important; height: 0px !important;';

    if ($this->_error) {
      $_error = wp_kses_post($this->_error);
      $_style = 'margin-top: 5px !important; height: 9px !important;';
    }

    $errMsgStart = '';
    $errMsgEnd = '';
    $msgStyle = "style='display: none !important;'";
    $errMsgAtomicCls = $this->_fieldHelpers->getConversationalMultiCls('err-msg');
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
    <div class='{$this->_fieldHelpers->getConversationalMultiCls('err-wrp')}' style="{$_style}">
    <div class='{$this->_fieldHelpers->getConversationalCls('err-inner')}'>
      {$errMsgStart}
        {$_errorPreIcn}
        <div {$this->_fieldHelpers->getCustomAttributes('err-txt')} {$errMsgCustomAttr} class="{$errMsgAtomicCls} {$errMsgCustomCls} {$this->_fieldHelpers->getConversationalMultiCls('err-txt')} {$this->_fieldHelpers->getCustomClasses('err-txt')}" {$msgStyle}>{$_error}</div>
        {$_errorSufIcn}
      {$errMsgEnd}
      </div>
    </div>
ERRORMESSAGES;
  }

  public function conversationalIcon($icnPropName, $element)
  {
    if ($this->_fieldHelpers->property_exists_nested($this->_fieldData, $icnPropName, '', 1)) {
      return <<<ICON
<img
  {$this->_fieldHelpers->getCustomAttributes($element)}
  class="{$this->_fieldHelpers->getConversationalCls($element)} {$this->_fieldHelpers->getCustomClasses($element)}"
  src="{$this->_fieldHelpers->esc_url($this->_fieldData->{$icnPropName})}"
  alt=""
/>
ICON;
    }
    return '';
  }
}
