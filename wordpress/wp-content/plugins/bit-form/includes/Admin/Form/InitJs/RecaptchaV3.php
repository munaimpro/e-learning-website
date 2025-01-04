<?php

namespace BitCode\BitForm\Admin\Form\InitJs;

class RecaptchaV3
{
  public static function init()
  {
    $bitforms_prefix = BITFORMS_PREFIX;
    // DANGER: no matter what, DONT CHANGE THE SCRIPT ID OF THIS SCRIPT
    $script_id = $bitforms_prefix . 'recaptcha-js';
    return <<<RECAPTCHA_V3_INIT_JS
    const src = 'https://www.google.com/recaptcha/api.js?render=' + contentData.gRecaptchaSiteKey;
    const attrs = {};
    const id = '$script_id';

    const recaptchaScript = document.querySelector('script#{$script_id}');
    if(!window.recaptcha && recaptchaScript && recaptchaScript.getAttribute('data-borlabs-script-blocker-js-handle') && recaptchaScript.getAttribute('data-borlabs-script-blocker-id')){
      return;
    }
    scriptLoader(src, '', attrs, id);
RECAPTCHA_V3_INIT_JS;
  }
}
