<?php

namespace BitCode\BitForm\Admin\Form\InitJs;

class Stripe
{
  public static function init()
  {
    return <<<STRIPE_INIT_JS
    const src = 'https://js.stripe.com/v3/';
    const attrs = {};
    const id = 'bit_stripe_field-' + contentId;
    const initFunc = function(){
      bf_globals[contentId].inits[fldKey] = getFldInstance(contentId, fldKey, fldType)
    };

    scriptLoader(src, '', attrs, id, initFunc);

STRIPE_INIT_JS;
  }
}
