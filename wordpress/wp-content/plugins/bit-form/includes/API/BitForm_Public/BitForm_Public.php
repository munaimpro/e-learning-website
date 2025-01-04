<?php

namespace BitCode\BitForm\API\BitForm_Public;

use BitCode\BitForm\Core\Database\ApiModel;

class BitForm_Public
{
  public static function getForms()
  {
    $db = new ApiModel();
    $forms = $db->getForm();
    return $forms;
  }

  public static function getFields($formId)
  {
    $db = new ApiModel();
    $formData = $db->getField($formId);
    if (!empty($formData)) {
      $unset_types = ['paypal', 'razorpay', 'stripe', 'recaptcha'];
      $formContent = json_decode($formData[0]->form_content);
      foreach ($formContent->fields as $key => $field) {
        if (in_array($field->typ, $unset_types)) {
          unset($formContent->fields->{$key}, $fieldsKey[$key]);
        }
      }
      return $formContent->fields;
    }
    return (object) [];
  }
}
