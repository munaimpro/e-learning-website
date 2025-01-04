<?php

namespace BitCode\BitForm\Frontend\Form\View\Conversational;

use BitCode\BitForm\Admin\Form\Helpers;

class ConversationalHelpers
{
  private $_formId;
  private $_conversationalSettings;
  private $_stepListObject;

  public function __construct($formId, $conversationalSettings)
  {
    $this->_formId = $formId;
    $this->_conversationalSettings = $conversationalSettings;
    $this->_stepListObject = $this->_conversationalSettings->stepListObject;
  }

  public function getWelcomePageView()
  {
    $welcomePageSettings = $this->getMergedStepSettings('welcomePage');
    $welcomeTitle = '';
    $welcomePageMarkup = '';
    $buttonMarkup = '';

    if (!$welcomePageSettings->enable) {
      return '';
    }
    if (!empty($welcomePageSettings->title)) {
      $welcomeTitle = <<<WELCOMETITLE
      <div class="bc{$this->_formId}-welcome-title">
        <h2>{$welcomePageSettings->title}</h2>
      </div>
WELCOMETITLE;
    }

    $startBtnPreIcn = $this->conversationalSettingsIcon($welcomePageSettings, 'startBtnPreIcn', 'btn-pre-icon', 'Start Button Pre Icon');
    $startBtnSufIcn = $this->conversationalSettingsIcon($welcomePageSettings, 'startBtnSufIcn', 'btn-suf-icon', 'Start Button Suf Icon');

    $startBtnText = !empty($welcomePageSettings->btnTxt) ? $welcomePageSettings->btnTxt : 'Start';

    $stepHints = !empty($welcomePageSettings->stepHints) ? $welcomePageSettings->stepHints : '';

    $buttonMarkup = <<<BUTTONMARKUP
    <div class="bc{$this->_formId}-step-btn-wrpr">
      <div class="bc{$this->_formId}-step-btn-inner-wrpr">
          <div class="bc{$this->_formId}-step-btn-cntnt">
            <button class="bc{$this->_formId}-start-btn bc{$this->_formId}-btn" type="button">
              {$startBtnPreIcn}
              {$startBtnText}
              {$startBtnSufIcn}
            </button>
            <span class="bc{$this->_formId}-step-hints">
              {$stepHints}
            </span>
          </div>
      </div>
    </div>
BUTTONMARKUP;

    $welcomePageMarkup = <<<WELCOMEPAGECONTENT
    <div class="bc{$this->_formId}-welcome-content">
      {$welcomeTitle}
      {$welcomePageSettings->content}
      {$buttonMarkup}
    </div>
WELCOMEPAGECONTENT;
    $imageContent = $this->conversationalSettingsIcon($welcomePageSettings, 'layoutImage', 'step-img', 'Background Image');
    $welcomeLayout = $welcomePageSettings->layout;
    $welcomeLayoutClsNames = "bc{$this->_formId}-welcome $welcomeLayout";
    return $this->getStepLayout($this->_formId, $imageContent, $welcomePageMarkup, $welcomeLayout, $welcomeLayoutClsNames);
  }

  public function getNavigationView()
  {
    $navigationSettings = $this->_conversationalSettings->navigationSettings;
    $formId = $this->_formId;
    $progressLabelMarkup = '';
    $progressBarMarkup = '';
    $progressMarkup = '';
    $brandingMarkup = '';
    $navBtnMarkup = '';

    if ($navigationSettings->showProgressLabel) {
      $progressLabelMarkup = <<<PERCENTAGEMARKUP
      <div class="bc{$formId}-progress-lbl-wrpr">
        <span class="bc{$formId}-progress-lbl">
          {$navigationSettings->progressLabel}
        </span>
      </div>
PERCENTAGEMARKUP;
    }

    if ($navigationSettings->showProgressBar) {
      $progressBarMarkup = <<<PROGRESSBARMARKUP
      <div class="bc{$formId}-progress-bar-wrpr">
        <div class="bc{$formId}-progress-bar">
          <div class="bc{$formId}-progress-fill" style="width: 40%"></div>
        </div>
      </div>
PROGRESSBARMARKUP;
    }

    if ($navigationSettings->showProgressBar || $navigationSettings->showProgressLabel) {
      $progressMarkup = <<<PROGRESSMARKUP
        <div class="bc{$formId}-progress-wrpr">
          <div class="bc{$formId}-progress-cntnt" >
          {$progressLabelMarkup}
          {$progressBarMarkup}
          </div>
        </div>
PROGRESSMARKUP;
    }

    if ($navigationSettings->showBranding) {
      $brandingMarkup = <<<BRANDINGMARKUP
          <div class="bc{$formId}-branding-wrpr">
            <div class="bc{$formId}-branding-cntnt">
              <div class="bc{$formId}-branding-lbl">
                <span class="bc{$formId}-prowered-by-lbl">Powered by</span>
                <span class="bc{$formId}-bit-form-lbl">Bit Form</span>
              </div>
            </div> 
          </div>
BRANDINGMARKUP;
    }

    if ($navigationSettings->showNavigateBtn) {
      $navBtnMarkup = <<<NAVBTNMARKUP
      <div class="bc{$formId}-nav-btn-wrpr">
        <div class="bc{$formId}-nav-btn-cntnt">
          <button class="bc{$formId}-nav-btn bc{$formId}-nav-btn-up" type="button">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
          </button>
          <button class="bc{$formId}-nav-btn bc{$formId}-nav-btn-down" type="button">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
          </button>
        </div>
      </div>
NAVBTNMARKUP;
    }
    $navBtnContainer = '';
    if ($navigationSettings->showNavigateBtn || $navigationSettings->showBranding) {
      $navBtnContainer = <<<NAVBTNCNTNR
      <div class="bc{$formId}-nav-btn-container">
        {$brandingMarkup}
        {$navBtnMarkup}
      </div>
NAVBTNCNTNR;
    }

    if ($navigationSettings->show) {
      return <<<NAVVIEW
      <div class="bc{$formId}-nav-wrpr">
        <div class="bc{$formId}-nav-wrpr-cntnt">
          {$progressMarkup}
          {$navBtnContainer}
        </div>
      </div>
NAVVIEW;
    }
    return '';
  }

  public function getStepButtonsView($fieldData, $stepSettings)
  {
    $formId = $this->_formId;
    $isFieldRequired = !empty($fieldData->valid->req) && $fieldData->valid->req;
    $btnTxt = $isFieldRequired ? $stepSettings->nextBtnTxt : $stepSettings->btnTxt;
    $btnPreIcn = $this->conversationalSettingsIcon($stepSettings, 'btnPreIcn', 'btn-pre-icon', 'Button Leading Icon');
    $btnSufIcn = $this->conversationalSettingsIcon($stepSettings, 'btnSufIcn', 'btn-suf-icon', 'Button Trailing Icon');
    $hiddenClass = $isFieldRequired ? 'bc-grid-hide' : '';
    $stepHints = !empty($stepSettings->stepHints) ? $stepSettings->stepHints : '';

    $btnWrapperMarkup = <<<BTNWRAPPERMARKUP
    <div class="{$hiddenClass} bc{$formId}-step-btn-wrpr">
      <div class="bc{$formId}-step-btn-inner-wrpr">
        <div class="bc{$formId}-step-btn-cntnt">
          <button class="bc{$formId}-btn bc{$formId}-btn-ok" type="button">
            {$btnPreIcn}
            {$btnTxt}
            {$btnSufIcn}
          </button>
          <span class="bc{$formId}-step-hints">
            {$stepHints}
          </span>
        </div>
      </div>
    </div>
BTNWRAPPERMARKUP;

    return $btnWrapperMarkup;
  }

  public static function getStepLayout($formID, $imageContent, $fieldContent, $layoutName, $layoutClassNames = '')
  {
    $imageContentWrapper = <<<IMAGECONTENTWRAPPER
    <div class="bc{$formID}-step-img-cntnr">
      <picture class="bc{$formID}-step-img-wrpr">
        {$imageContent}
      </picture>
    </div>
IMAGECONTENTWRAPPER;
    $imageContentWrapper = 'normal-layout' === $layoutName ? '' : $imageContentWrapper;

    return <<<STEPLAYOUT
    <div class="bc{$formID}-step-wrapper bc{$formID}-step bc-step-deactive {$layoutClassNames}">
      <div class="bc{$formID}-step-content">
        {$imageContentWrapper}
        <div class="bc{$formID}-step-fld-wrpr">
          {$fieldContent}
        </div>
      </div>
    </div>
STEPLAYOUT;
  }

  public function conversationalSettingsIcon($data, $icnPropName, $element, $alt = 'Image')
  {
    if (Helpers::property_exists_nested($data, $icnPropName, '', 1)) {
      $url = esc_url($data->$icnPropName);
      return <<<ICON
              <img
                class="bc{$this->_formId}-{$element}"
                src="{$url}"
                alt={$alt}
              />
ICON;
    }
    return '';
  }

  public function filterConversationalAllowedFields($layout, $fields)
  {
    $ignoreFields = $this::getConversationalIgnoredFields();
    $newLayouts = (object) ['lg' => []];
    foreach ($layout->lg as $row) {
      if (!in_array($fields->{$row->i}->typ, $ignoreFields)) {
        $newLayouts->lg[] = $row;
      }
    }
    return $newLayouts;
  }

  public static function getConversationalIgnoredFields()
  {
    return [
      'button',
      'divider',
      'title',
      'html',
      'shortcode',
      'recaptcha',
      'turnstile',
      'image',
      'stripe',
      'paypal',
      'razorpay',
    ];
  }

  public function getMergedStepSettings($stepPropertyName)
  {
    $allStepsSettings = $this->_stepListObject->allSteps;
    $stepSettings = !empty($this->_stepListObject->{$stepPropertyName}) ? $this->_stepListObject->{$stepPropertyName} : (object) [];
    $mergedSettings = (object) array_merge((array) $allStepsSettings, (array) $stepSettings);

    return $mergedSettings;
  }
}
