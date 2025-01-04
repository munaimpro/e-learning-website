<?php

namespace BitCode\BitForm\Admin\Form\InitJs;

class Turnstile
{
  public static function init()
  {
    return <<<TURNSTILE_INIT_JS
    const src = 'https://challenges.cloudflare.com/turnstile/v0/api.js';
    const attrs = {
      async: true,
      defer: true,
    };
    const id = 'bit_turnstile_script-' +contentId;

    scriptLoader(src, '', attrs, id);
TURNSTILE_INIT_JS;
  }
}
