<?php

namespace BitCode\BitForm\Admin\Form\InitJs;

class Paypal
{
  public static function init()
  {
    return <<<PAYPAL_INIT_JS
      const paypalUrlParams = {'client-id': fldData.clientId};
      if('locale' in fldData){
        paypalUrlParams.locale = fldData.locale;
      }
      if('disableFunding' in fldData && fldData.disableFunding.length){
        paypalUrlParams['disable-funding'] = fldData.disableFunding;
      }
      if(fldData?.payType === 'subscription'){
          paypalUrlParams.vault = 'true';
          paypalUrlParams.intent = 'subscription';
      } else if('currency' in fldData){
        paypalUrlParams.currency = fldData.currency;
      }

      const paypalUrl = 'https://www.paypal.com/sdk/js?' + Object.entries(paypalUrlParams).map(([key, val]) => key+'='+val).join('&');
      const attrs = {'data-namespace': 'bit-paypal-'+ contentId};
      const id = 'bit_paypal_script-' + contentId;
      const initFunc = function(){
        bf_globals[contentId].inits[fldKey] = getFldInstance(contentId, fldKey, fldType)
      };

      scriptLoader(paypalUrl, '', attrs, id, initFunc);
PAYPAL_INIT_JS;
  }
}
