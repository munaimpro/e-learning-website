<?php

namespace BitCode\BitForm\Frontend\Form\View\Conversational;

use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\AdvanceFileUpField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\CheckBoxField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\CountryField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\CurrencyField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\DecisionBoxField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\DropdownField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\FileUploadField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\GDPRAgreementField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\HtmlSelectField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\ImageSelectField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\PayPalField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\PhoneNumberField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\RadioBoxField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\RatingField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\RazorPayField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\RecaptchaV2Field;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\RepeaterField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\SectionField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\SignatureField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\StripeField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\TextAreaField;
use BitCode\BitForm\Frontend\Form\View\Conversational\Fields\TextField;

class ConversationalTheme
{
  private $_conversationalSettings;
  private $_nestedLayout;
  private $_formViewerInstance;
  private $_conversationalHelpers;

  public function __construct($conversationalSettings)
  {
    $this->_conversationalSettings = $conversationalSettings;
  }

  public function inputWrapper($formViewInstance, $rowID, $isNestedField = false)
  {
    $formID = $formViewInstance->getFormID();
    $this->_conversationalHelpers = new ConversationalHelpers($formID, $this->_conversationalSettings);
    $stepSettings = $this->_conversationalHelpers->getMergedStepSettings($rowID);
    if (isset($stepSettings->enable) && !$stepSettings->enable) {
      return '';
    }

    $field = $formViewInstance->fields($rowID);
    $field_name = $formViewInstance->getFieldName($rowID);
    $form_atomic_Cls_map = $formViewInstance->getAtomicClaMap();
    $error = $formViewInstance->getError($rowID);
    $value = $formViewInstance->getValue($field_name, $rowID);
    $this->_nestedLayout = $formViewInstance->getNestedLayout($rowID);
    $this->_formViewerInstance = $formViewInstance;
    $isHidden = !empty($field->valid->hide) && $field->valid->hide ? 'fld-hide' : null;

    $fieldHTML = $this->getField($field, $rowID, $field_name, $form_atomic_Cls_map, $error, $value, $formID);
    $stepBtnHTML = $isNestedField ? '' : $this->_conversationalHelpers->getStepButtonsView($field, $stepSettings);
    $minHeightCls = $isNestedField ? "bc{$formID}-min-height" : '';
    $fieldContent = <<<FIELDCONTENT
            <div class="btcd-fld-itm $rowID $minHeightCls $isHidden">
                $fieldHTML
                $stepBtnHTML
            </div>
FIELDCONTENT;

    $imageContent = $this->_conversationalHelpers->conversationalSettingsIcon($stepSettings, 'layoutImage', 'step-img', 'Field Image');
    $convStepLayout = empty($stepSettings->layout) ? 'normal-layout' : $stepSettings->layout;
    $layoutClsNames = "bc{$formID}-{$rowID} $convStepLayout";
    $stepLayoutMarkup = $isNestedField ? $fieldContent : $this->_conversationalHelpers->getStepLayout($formID, $imageContent, $fieldContent, $convStepLayout, $layoutClsNames);
    return $stepLayoutMarkup;
  }

  public function getField($field, $rowID, $field_name, $form_atomic_Cls_map, $error, $value, $formID)
  {
    switch($field->typ) {
      case 'text':
      case 'username':
      case 'number':
      case 'password':
      case 'email':
      case 'url':
      case 'date':
      case 'datetime-local':
      case 'time':
      case 'month':
      case 'week':
      case 'color':
        return TextField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
      case 'textarea':
        return TextAreaField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
      case 'check':
        // return $this->checkBox($field, $rowID, $field_name, $formID, $error, $value);
        return CheckBoxField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
      case 'radio':
        // return $this->radioBox($field, $rowID, $field_name, $formID, $error, $value);
        return RadioBoxField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
      case 'html-select':
        return HtmlSelectField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
      case 'select':
        // return $this->dropDown($field, $rowID, $field_name, $formID, $error, $value);
        return DropdownField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
      case 'file-up':
        // return $this->fileUp($field, $rowID, $field_name, $formID, $error, $value);
        return FileUploadField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
      case 'recaptcha':
        return RecaptchaV2Field::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
        // return $this->recaptcha($field, $rowID, $field_name, $formID, $error, $value);
      case 'decision-box':
        // return $this->decisionBox($field, $rowID, $field_name, $formID, $error, $value);
        return DecisionBoxField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
      case 'gdpr':
        return GDPRAgreementField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
      case 'paypal':
        return  PayPalField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
      case 'stripe':
        return  StripeField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
      case 'razorpay':
        return RazorPayField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
      case 'country':
        return CountryField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
      case 'currency':
        return CurrencyField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
      case 'phone-number':
        return PhoneNumberField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
      case 'advanced-file-up':
        return AdvanceFileUpField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
      case 'section':
        return SectionField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $this->_formViewerInstance, $this->_nestedLayout, $error, $value);
      case 'repeater':
        return RepeaterField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $this->_formViewerInstance, $this->_nestedLayout, $error, $value);
      case 'signature':
        return SignatureField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
      case 'rating':
        return RatingField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
      case 'image-select':
        return ImageSelectField::init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error, $value);
      default:
        break;
    }
  }
}
