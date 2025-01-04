<?php

namespace BitCode\BitForm\Core\Migration;

// create class for migration
final class Migration
{
  private $_formData;

  public function __construct($data)
  {
    $this->_formData = $data;
  }

  // create function for migration
  public function migrate()
  {
    $formData = $this->_formData;

    $formData->form_content = self::convertFormContent($formData->form_content);
    $formData->workFlows = WorkFlows::migrate($formData->workFlows);
    $formData->formSettings = self::convertFormSettings($formData->formSettings);
    return $formData;
  }

  public function convertFormContent($formContentV1)
  {
    $formContenV2 = $formContentV1;

    if (isset($formContentV1->fields)) {
      $formContenV2->fields = MigrationHelper::convertFields($formContentV1->fields);
    }
    if (isset($formContentV1->layout)) {
      $formContenV2->layout = MigrationHelper::convertLayout($formContentV1->layout);
    }

    return $formContenV2;
  }

  public function convertFormSettings($formSettingsV1)
  {
    $formSettingsV2 = $formSettingsV1;
    if (isset($formSettingsV1->confirmation->type->successMsg)) {
      $formSettingsV2->confirmation->type->successMsg = MigrationHelper::convertSuccessMessage($formSettingsV1->confirmation->type->successMsg);
    }
    if (!empty($formSettingsV1->confirmation->type->webHooks)) {
      $formSettingsV2->integrations = MigrationHelper::convertWebHooksToIntegrations($formSettingsV1);
    }
    return $formSettingsV2;
  }
}
