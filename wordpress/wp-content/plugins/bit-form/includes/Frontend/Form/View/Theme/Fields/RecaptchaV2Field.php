<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

use BitCode\BitForm\Core\Integration\IntegrationHandler;

class RecaptchaV2Field
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $integrationHandler = new IntegrationHandler(0);
    $allFormIntegrations = $integrationHandler->getAllIntegration('app', 'gReCaptcha');
    if (is_wp_error($allFormIntegrations)) {
      return '';
    }
    $siteKey = json_decode($allFormIntegrations[0]->integration_details)->siteKey;
    return self::field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $siteKey, $error = null, $value = null);
  }

  private static function field($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $siteKey, $error = null, $value = null)
  {
    $fieldHelpers = new ClassicFieldHelpers($field, $rowID, $form_atomic_Cls_map);
    $theme = $field->config->theme;
    $size = $field->config->size;

    return <<<RECAPTCHAV2FIELD
      <div
        {$fieldHelpers->getCustomAttributes('fld-wrp')}
        class="{$fieldHelpers->getAtomicCls('fld-wrp')} {$fieldHelpers->getCustomClasses('fld-wrp')}"
      >
        <div class="$rowID-recaptcha-wrp">
          <div class="g-recaptcha" data-theme="{$theme}" data-size="{$size}" data-sitekey="{$fieldHelpers->esc_attr($siteKey)}">
          </div>
        </div>
      </div>
RECAPTCHAV2FIELD;
  }
}
