<?php

namespace BitCode\BitForm\Frontend\Form\View\Theme\Fields;

use BitCode\BitForm\Core\Integration\IntegrationHandler;

class TurnstileField
{
  public static function init($field, $rowID, $field_name, $form_atomic_Cls_map, $formID, $error = null, $value = null)
  {
    $integrationHandler = new IntegrationHandler(0);
    $allFormIntegrations = $integrationHandler->getAllIntegration('app', 'turnstileCaptcha');
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
    $language = $field->config->language;
    $appearance = $field->config->appearance;

    $interactiveCallback = '';
    $dNone = '';
    $script = '';

    $fldKeyWithContentId = $fieldHelpers->getFieldKeyWithContentCount();
    $fldKeyExceptHyphen = str_replace('-', '', $fldKeyWithContentId);

    $beforeInteractiveCallback = '';
    $afterInteractiveCallback = '';

    if ('interaction-only' === $appearance) {
      $beforeExcFuncName = 'bfBeforeInteractiveCallback' . $fldKeyExceptHyphen;
      $afterExcFuncName = 'bfAfterInteractiveCallback' . $fldKeyExceptHyphen;
      $dNone = 'd-none';
      $beforeInteractiveCallback = 'data-before-interactive-callback="' . $beforeExcFuncName . '"';
      $afterInteractiveCallback = 'data-after-interactive-callback="' . $afterExcFuncName . '"';
      $script = <<<TURNSTILESCRIPT
      <script>
        function {$beforeExcFuncName}() {
          const bfTurnstileFldSelector = document.querySelector('.{$fldKeyWithContentId}-turnstile-wrp');
          if(bfTurnstileFldSelector) bfTurnstileFldSelector.parentElement.classList.remove('d-none');
        }
        function {$afterExcFuncName}() {
          const bfTurnstileFldSelector = document.querySelector('.{$fldKeyWithContentId}-turnstile-wrp');
          if(bfTurnstileFldSelector) bfTurnstileFldSelector.parentElement.classList.add('d-none');
        }
      </script>
TURNSTILESCRIPT;
    }

    return <<<TURNSTILEFIELD
      {$script}
      <div
        {$fieldHelpers->getCustomAttributes('fld-wrp')}
        class="{$fieldHelpers->getAtomicCls('fld-wrp')} {$fieldHelpers->getCustomClasses('fld-wrp')} {$dNone}"
      >
        <div class="$fldKeyWithContentId-turnstile-wrp">
          <div class="cf-turnstile" {$beforeInteractiveCallback} {$afterInteractiveCallback} data-appearance="{$appearance}" data-language="{$language}" data-theme="{$theme}" data-size="{$size}" data-sitekey="{$fieldHelpers->esc_attr($siteKey)}">
          </div>
        </div>
      </div>
TURNSTILEFIELD;
  }
}
