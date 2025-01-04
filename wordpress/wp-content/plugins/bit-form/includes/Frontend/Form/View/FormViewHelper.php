<?php

namespace BitCode\BitForm\Frontend\Form\View;

use BitCode\BitForm\Frontend\Form\FrontendFormManager;

class FormViewHelper
{
  private $_formContents;
  private $_formInfo;
  private $_layout;
  private $_form;
  private $_fieldHelpers;

  public function __construct(FrontendFormManager $form, $form_contents)
  {
    $this->_form = $form;
    $this->_formContents = $form_contents;
    $this->_formInfo = isset($form_contents->formInfo) ? $form_contents->formInfo : '';
    $this->_layout = isset($form_contents->layout) ? $form_contents->layout : '';
  }

  private function getFormInfoProperty($propertyName)
  {
    return isset($this->_formInfo->{$propertyName}) ? $this->_formInfo->{$propertyName} : null;
  }

  private function getFormId()
  {
    return $this->_form->getFormID();
  }

  public function filterHtml($str)
  {
    return wp_kses_post(str_replace('$_bf_$', '\\', $str));
  }

  private function getIconMarkup($source, $classElement)
  {
    if (empty($source)) {
      return '';
    }
    return <<<ICON
<img
  class="{$this->_form->getAtomicCls($classElement)}"
  src="{$source}"
  alt=""
/>
ICON;
  }

  private function getHeaderLabelMarkup($lbl, $preIcn, $sufIcn, $uniqClass)
  {
    $formID = $this->getFormId();
    return <<<HEADERLABEL
            <span class='{$this->_form->getAtomicCls("_frm-b{$formID}-stp-hdr-{$uniqClass}")}'>
              {$this->getIconMarkup($preIcn, "_frm-b{$formID}-stp-{$uniqClass}-pre-i")}
              {$this->filterHtml($lbl)}
              {$this->getIconMarkup($sufIcn, "_frm-b{$formID}-stp-{$uniqClass}-suf-i")}
            </span>
HEADERLABEL;
  }

  private function getStepButton($btnSettings)
  {
    $key = $btnSettings->key;
    $txt = $btnSettings->txt;
    $btnTyp = $btnSettings->typ;
    $formID = $this->getFormId();
    $preIcn = isset($btnSettings->preIcn) ? $btnSettings->preIcn : null;
    $sufIcn = isset($btnSettings->sufIcn) ? $btnSettings->sufIcn : null;
    $preIcnMrkp = $this->getIconMarkup($preIcn, "_frm-b{$formID}-{$key}-pre-i");
    $sufIcnMrkp = $this->getIconMarkup($sufIcn, "_frm-b{$formID}-{$key}-suf-i");
    $btnSpinner = '<span class="bf-spinner d-none"></span>';
    return <<<BUTTON
        <button
          class="{$this->_form->getAtomicCls("_frm-b{$formID}-{$key}")} {$key}"
          type="{$btnTyp}"
        >
          {$preIcnMrkp}
          {$this->filterHtml($txt)}
          {$sufIcnMrkp}
          {$btnSpinner}
        </button>
BUTTON;
  }

  public function getStepHeaderHtml()
  {
    $multiStepSettings = $this->getFormInfoProperty('multiStepSettings');

    if (isset($multiStepSettings->showStepHeader) && !$multiStepSettings->showStepHeader) {
      return '';
    }

    $formID = $this->_form->getFormID();
    $layout = $this->_layout;
    $stepHeaderHtml = <<<HEADERHTML
    <div class='{$this->_form->getAtomicCls("_frm-b{$formID}-stp-hdr-cntnr")}'>
      <div class='{$this->_form->getAtomicCls("_frm-b{$formID}-stp-hdr-wrpr")}'>
HEADERHTML;

    $showHeadarIcon = isset($multiStepSettings->headerIcon->show) ? $multiStepSettings->headerIcon->show : false;
    $showLbl = isset($multiStepSettings->showLbl) ? $multiStepSettings->showLbl : false;
    $showSubtitle = isset($multiStepSettings->showSubtitle) ? $multiStepSettings->showSubtitle : false;

    $iconType = isset($multiStepSettings->headerIcon->iconType) ? $multiStepSettings->headerIcon->iconType : 'number';

    $stepValidation = isset($multiStepSettings->validateOnStepChange) ? $multiStepSettings->validateOnStepChange : false;
    foreach ($layout as $key => $lay) {
      $step = $key + 1;
      $settings = isset($lay->settings) ? $lay->settings : null;

      if (empty($settings)) {
        continue;
      }
      $iconWrapper = '';
      $stepIcon = '';

      if ('icon' === $iconType) {
        $stepIcon = "<img src='{$settings->icon}' class={$this->_form->getAtomicCls("_frm-b{$formID}-stp-icn")} alt='Step Icon' />";
      } else {
        $stepIcon = "<span class='{$this->_form->getAtomicCls("_frm-b{$formID}-stp-num")}'>{$step}</span>";
      }
      if ($showHeadarIcon) {
        $iconWrapper = <<<ICONWRPR
        <div class='{$this->_form->getAtomicCls("_frm-b{$formID}-stp-hdr-icn-wrp")}'>
          <div class='{$this->_form->getAtomicCls("_frm-b{$formID}-stp-icn-cntn")}'>
            {$stepIcon}
          </div>
        </div>
ICONWRPR;
      }
      $showStepLbl = $showLbl && isset($settings->showLbl) ? $settings->showLbl : false;
      $showStepSubtitle = $showSubtitle && isset($settings->showSubtitle) ? $settings->showSubtitle : false;
      $lblPreIcn = isset($settings->lblPreIcn) ? $settings->lblPreIcn : null;
      $lblSufIcn = isset($settings->lblSufIcn) ? $settings->lblSufIcn : null;
      $subTlePreIcn = isset($settings->subTlePreIcn) ? $settings->subTlePreIcn : null;
      $subTleSufIcn = isset($settings->subTleSufIcn) ? $settings->subTleSufIcn : null;
      $stepLabelMarkup = $showStepLbl ? $this->getHeaderLabelMarkup($settings->lbl, $lblPreIcn, $lblSufIcn, 'lbl') : '';
      $stepSubtitleMarkup = $showStepSubtitle ? $this->getHeaderLabelMarkup($settings->subtitle, $subTlePreIcn, $subTleSufIcn, 'sub-titl') : '';

      $activeClass = 0 === $key ? 'active' : '';
      $disableClass = (0 !== $key && $stepValidation) ? 'disabled' : '';
      $stepHeaderHtml .= <<<HEADERHTML
      <div class='{$this->_form->getAtomicCls("_frm-b{$formID}-stp-hdr")} {$activeClass} {$disableClass}' data-step='{$step}'>
        <div class='{$this->_form->getAtomicCls("_frm-b{$formID}-stp-hdr-cntnt")}'>
          {$iconWrapper}
          <span class='{$this->_form->getAtomicCls("_frm-b{$formID}-stp-hdr-titl-wrpr")}'>
            {$stepLabelMarkup}
            {$stepSubtitleMarkup}
          </span>
        </div>
      </div>
HEADERHTML;
    }
    $stepHeaderHtml .= <<<HEADERHTML
      </div>
    </div>
HEADERHTML;
    return $stepHeaderHtml;
  }

  public function getProgressBarMarkup()
  {
    $multiStepSettings = $this->getFormInfoProperty('multiStepSettings');
    $progressBarSettings = isset($multiStepSettings->progressSettings) ? $multiStepSettings->progressSettings : null;
    if (isset($progressBarSettings->show) && !$progressBarSettings->show) {
      return '';
    }
    $precentage = (isset($progressBarSettings->showPercentage) && $progressBarSettings->showPercentage) ? '0%' : '';
    $formID = $this->getFormId();
    return <<<PROGRESSHTML
    <div class='{$this->_form->getAtomicCls("_frm-b{$formID}-stp-progress-wrpr")}'>
      <div class='{$this->_form->getAtomicCls("_frm-b{$formID}-stp-progress")}'>
        <div class='{$this->_form->getAtomicCls("_frm-b{$formID}-stp-progress-bar")}'>
          <div class='{$this->_form->getAtomicCls("_frm-b{$formID}-progress-fill")}' style="width: 0%;">
            {$precentage}
          </div>
        </div>
      </div>
    </div>
    PROGRESSHTML;
  }

  public function getStepButtonMarkup()
  {
    $multiStepSettings = $this->getFormInfoProperty('multiStepSettings');
    $btnSettings = isset($multiStepSettings->btnSettings) ? $multiStepSettings->btnSettings : null;
    if (is_null($btnSettings) || (empty($btnSettings->show))) {
      return '';
    }
    $formID = $this->getFormId();
    return <<<BUTTONWRPR
            <div class='{$this->_form->getAtomicCls("_frm-b{$formID}-stp-btn-wrpr")}'>
              <div class='{$this->_form->getAtomicCls("_frm-b{$formID}-stp-btn-cntnt")}'>
                {$this->getStepButton($btnSettings->prevBtn)}
                {$this->getStepButton($btnSettings->nextBtn)}
              </div>
            </div>
BUTTONWRPR;
  }
}
