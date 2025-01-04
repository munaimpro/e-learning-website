<?php

namespace BitCode\BitForm\Frontend\Form\View;

use BitCode\BitForm\Admin\Form\Helpers;
use BitCode\BitForm\Frontend\Form\FrontendFormManager;
use BitCode\BitForm\Frontend\Form\View\Conversational\ConversationalHelpers;
use BitCode\BitForm\Frontend\Form\View\Conversational\DefaultConversationalTheme;

class FormViewer
{
  private $_theme = 'Default';
  private $_themeDetails;
  public $_fields;
  private $_layout;
  public $_nestedLayout;
  private $_buttons;
  public $_form;
  public $_error;
  private $_previousValue;
  public $_formAtomicClsMap;
  public $_tokens;
  public $_formContents;

  public function __construct(FrontendFormManager $formManager, $form_contents, $formAtomicClsMap, $errorMessages = null, $previousValue = null)
  {
    $this->_tokens = Helpers::csrfEecrypted();
    $this->_fields = $form_contents->fields;
    $this->_layout = $form_contents->layout;
    $this->_nestedLayout = isset($form_contents->nestedLayout) ? $form_contents->nestedLayout : '';
    $this->_buttons = isset($form_contents->buttons) ? $form_contents->buttons : '';
    $this->_form = $formManager;
    $this->_error = $errorMessages;
    $this->_formContents = $form_contents;
    $this->_previousValue = $previousValue;
    $this->_formAtomicClsMap = $formAtomicClsMap;
  }

  public function getView($hasFile, $msg = null)
  {
    $name = str_replace(' ', '', $this->_theme);
    $file = false !== strpos($name, 'Theme') ? $name . '.php' : $name . 'Theme.php';
    if (file_exists(__DIR__ . '/' . $file)) {
      $className = __NAMESPACE__ . '\\Theme\\' . $name . 'Theme';
      $this->_themeDetails = new $className();
    } else {
      $className = __NAMESPACE__ . '\\Theme\\' . 'DefaultTheme';
      $this->_themeDetails = new $className();
    }
    return $this->setView($hasFile, $msg);
  }

  public function honeypotField()
  {
    $time = time();
    $honeypodFldName = Helpers::honeypotEncryptedToken("_bitforms_{$this->getFormID()}_{$time}_");
    $honeypotInput = '';
    if (Helpers::property_exists_nested($this->_formContents, 'additional->enabled->honeypot', true)) {
      $honeypotInput =
      <<<HTMLa
        <input type="text" class="d-none" name="b_h_t" value="{$honeypodFldName}">
        <input type="text" class="d-none" name="{$honeypodFldName}" required>
HTMLa;
      return $honeypotInput;
    }
    return $honeypotInput;
  }

  public function setTheme($theme = 'Default')
  {
    $this->_theme = $theme;
  }

  public function getError($field_name)
  {
    return isset($this->_error[$field_name]) ? $this->_error[$field_name] : null;
  }

  public function getFieldName($rowId)
  {
    $formIdentifier = $this->_form->getFormIdentifier();
    $field_name = $rowId;
    if ('button' === $this->_fields->{$rowId}->typ && 'submit' === $this->_fields->{$rowId}->btnTyp) {
      $field_name = $formIdentifier;
    }
    $field_name .= isset($this->_fields->{$rowId}->lbl) ? preg_replace('/[\`\~\!\@\#\$\'\.\s\?\+\-\*\&\|\/\\\!]/', '_', $this->_fields->{$rowId}->lbl) : '';
    return $field_name;
  }

  public function getAtomicClaMap()
  {
    return $this->_formAtomicClsMap;
  }

  public function getValue($field_name, $rowId)
  {
    $valueToEsc = isset($this->_previousValue[$field_name]) ?
      $this->_previousValue[$field_name] : (isset($this->_fields->{$rowId}->val) ? $this->_fields->{$rowId}->val : null);
    if (empty($valueToEsc)) {
      $value = null;
    } else {
      if ((isset($this->_fields->{$rowId}->mul) || 'check' === $this->_fields->{$rowId}->typ || 'select' === $this->_fields->{$rowId}->typ)) {
        if ('select' === $this->_fields->{$rowId}->typ && is_string($valueToEsc)) {
          $valueToEsc = explode(',', $valueToEsc);
        }
        if (is_array($valueToEsc)) {
          $value = array_map('esc_attr', $valueToEsc);
        } else {
          $value = esc_attr($valueToEsc);
        }
      } else {
        $value = !is_array($valueToEsc) ? esc_attr($valueToEsc) :
          esc_attr($valueToEsc[count($valueToEsc) - 1]);
      }
    }
    return $value;
  }

  public function getFormID()
  {
    return $this->_form->getFormID();
  }

  public function fields($rowID)
  {
    return $this->_fields->{$rowID};
  }

  public function getNestedLayout($rowID)
  {
    // return null;
    return isset($this->_nestedLayout->{$rowID}) ? $this->_nestedLayout->{$rowID} : null;
  }

  public function getFormInfo()
  {
    return $this->_formContents->formInfo;
  }

  private function getLayoutHtml($lay)
  {
    $html = '';
    foreach ($lay->lg as $row) {
      $html .= $this->_themeDetails->inputWrapper($this, $row->i);
    }
    return $html;
  }

  public function getConversationalView($hasFile, $msg = null)
  {
    $conversationSettings = $this->getFormInfo()->conversationalSettings;
    $this->_themeDetails = new DefaultConversationalTheme($conversationSettings);
    $file_upload_tag = null;
    $restrictionMsg = '';
    $formID = $this->_form->getFormID();
    if ($hasFile) {
      $file_upload_tag = 'enctype="multipart/form-data"';
    }
    $formIdentifier = $this->_form->getFormIdentifier();
    $fieldHtml = '';
    $layouts = $this->_layout;
    $isMultiStep = is_array($layouts) && count($layouts) > 1;
    $layoutIsArray = !$isMultiStep && is_array($layouts);
    if ($layoutIsArray) {
      $layout = $layouts[0];
      $layouts = isset($layout->layout) ? $layout->layout : $layout;
      $this->_layout = $layouts;
    }
    $conversationalHelper = new ConversationalHelpers($this->getFormID(), $conversationSettings);
    $welcomePageHtml = $conversationalHelper->getWelcomePageView();
    if (!$isMultiStep) {
      $tempLayout = $conversationalHelper->filterConversationalAllowedFields($layouts, $this->_fields);
      $fieldHtml .= $this->getLayoutHtml($tempLayout);
    } else {
      foreach ($layouts as $lay) {
        $tempLayout = $conversationalHelper->filterConversationalAllowedFields($lay->layout, $this->_fields);
        $fieldHtml .= $this->getLayoutHtml($tempLayout);
      }
    }
    $conversationNavigationHtml = $conversationalHelper->getNavigationView();

    $confMsg = $this->_form->getSuccessMessageMarkups();
    $abandonmentMsg = $this->_form->getFormAbandonmentMessage();

    // if (!empty($this->_buttons)) {
    //   $this->_buttons->name = $formIdentifier;
    //   $buttonClass = "button-{$formIdentifier}";
    //   $subBtn = $this->_themeDetails->getField($this->_buttons, $buttonClass, '');
    // }

    if (!empty($msg)) {
      $restrictionMsg = <<<MSG
      <div class="{$this->_form->getAtomicCls("_frm-ovrly-b{$formID}")}">
      <p class="{$this->_form->getAtomicCls("_frm-ovrly-msg-b{$formID}")}">
        $msg
      </p>
      </div>
MSG;
    }
    $formHTML =
      <<<HTMLa
      <div id="{$formIdentifier}" class="bit-form {$this->_form->getAtomicCls("_frm-bg-b{$formID}")}">
      $restrictionMsg
          <form novalidate id="form-{$formIdentifier}" class="_frm-bc{$formID}" $file_upload_tag method='post'>
              <input type="text" class="d-none" name="csrf" value="{$this->_tokens['csrf']}">
              <input type="text" class="d-none" name="t_identity" value="{$this->_tokens['t_identity']}">
              {$this->honeypotField()}
              <input type="text" class="d-none" name="bitforms_id" value="bitforms_{$this->_form->getFormID()}">
              <div class="bc{$formID}-steps-container">
                $welcomePageHtml    
                $fieldHtml
              </div>
          </form>
          <div id='bc-form-msg-wrp-{$formIdentifier}' class="bc-form-msg-wrp"></div>
          $abandonmentMsg
          $conversationNavigationHtml
      </div>
HTMLa;

    return $formHTML;
  }

  private function setView($hasFile, $msg)
  {
    $file_upload_tag = null;
    $restrictionMsg = '';
    $formID = $this->_form->getFormID();
    if ($hasFile) {
      $file_upload_tag = 'enctype="multipart/form-data"';
    }
    $formIdentifier = $this->_form->getFormIdentifier();
    $fieldHtml = '';
    $layouts = $this->_layout;
    $isMultiStep = is_array($layouts) && count($layouts) > 1;
    $layoutIsArray = !$isMultiStep && is_array($layouts);
    if ($layoutIsArray) {
      $layout = $layouts[0];
      $layouts = isset($layout->layout) ? $layout->layout : $layout;
      $this->_layout = $layouts;
    }
    if (!$isMultiStep) {
      $fieldHtml .= $this->getLayoutHtml($layouts);
    } else {
      $formViewHelper = new FormViewHelper($this->_form, $this->_formContents);

      $fieldHtml .= <<<STEPWRPR
      <div class='{$this->_form->getAtomicCls("_frm-b{$formID}-stp-cntnr")}'>
        {$formViewHelper->getStepHeaderHtml()}
        <div class='{$this->_form->getAtomicCls("_frm-b{$formID}-stp-wrpr")}'>
        {$formViewHelper->getProgressBarMarkup()}
          <div class='{$this->_form->getAtomicCls("_frm-b{$formID}-stp-cntnt-wrpr")}'>
STEPWRPR;
      foreach ($layouts as $key => $lay) {
        $hideOtherSteps = $key > 0 ? 'deactive' : '';
        $step = $key + 1;
        $fieldHtml .= <<<HTMLa
          <div class="{$this->_form->getAtomicCls("_frm-b{$formID}-stp-cntnt")} $hideOtherSteps" data-step="{$step}">
            <div class="_frm-b{$formID}">
              {$this->getLayoutHtml($lay->layout)}
            </div>
            {$formViewHelper->getStepButtonMarkup()}
          </div>
HTMLa;
      }
      $fieldHtml .= <<<CLOSINGTAG
          </div>
        </div>
      </div>
CLOSINGTAG;
    }

    $confMsg = $this->_form->getSuccessMessageMarkups();
    $abandonmentMsg = $this->_form->getFormAbandonmentMessage();

    if (!empty($this->_buttons)) {
      $this->_buttons->name = $formIdentifier;
      $buttonClass = "button-{$formIdentifier}";
      $subBtn = $this->_themeDetails->getField($this->_buttons, $buttonClass, '');
    }

    if (!empty($msg)) {
      $restrictionMsg = <<<MSG
      <div class="{$this->_form->getAtomicCls("_frm-ovrly-b{$formID}")}">
      <p class="{$this->_form->getAtomicCls("_frm-ovrly-msg-b{$formID}")}">
        $msg
      </p>
      </div>
MSG;
    }
    $formHTML =
      <<<HTMLa
      <div id="{$formIdentifier}" class="bit-form {$this->_form->getAtomicCls("_frm-bg-b{$formID}")}">
      $restrictionMsg
          <form novalidate id="form-{$formIdentifier}" class="{$this->_form->getAtomicCls("_frm-b{$formID}")}" $file_upload_tag method='post'>
              <input type="text" class="d-none" name="csrf" value="{$this->_tokens['csrf']}">
              <input type="text" class="d-none" name="t_identity" value="{$this->_tokens['t_identity']}">
              {$this->honeypotField()}
              <input type="text" class="d-none" name="bitforms_id" value="bitforms_{$this->_form->getFormID()}">
                  $fieldHtml
          </form>
          $abandonmentMsg
          <div id='bf-form-msg-wrp-{$formIdentifier}'></div>
          $confMsg
      </div>
HTMLa;
    return $formHTML;
  }
}
