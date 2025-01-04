<?php

namespace BitCode\BitForm\Admin\Form\InitJs;

class Razorpay
{
  public static function init()
  {
    return <<<RAZORPAY_INIT_JS
    const src = 'https://checkout.razorpay.com/v1/checkout.js';
    const attrs = {};
    const id = 'bit_razorpay_script-' + contentId;
    const initFunc = function(){
      bf_globals[contentId].inits[fldKey] = getFldInstance(contentId, fldKey, fldType)
    };

    scriptLoader(src, '', attrs, id, initFunc);

RAZORPAY_INIT_JS;
  }
}
