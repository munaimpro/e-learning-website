<?php

namespace BitCode\BitForm\Admin\Form\InitJs;

class ScriptLoader
{
  public static function init()
  {
    return <<<SCRIPT_LOADER
    function scriptLoader(src, integrity, attrs, id, initFunc) {
      const script =  document.createElement('script');
      script.src = src;
      script.id = id;
      if(integrity){
        script.integrity = integrity;
        script.crossOrigin = 'anonymous';
      }
      if(attrs){
        Object.entries(attrs).forEach(function([key, val]){
          script.setAttribute(key,val);
        })
      }
      script.onload = function () {
          initFunc && initFunc();
      };
      var alreadyExistScriptElm = document.querySelector('script#'+id);
      if(alreadyExistScriptElm){
        initFunc && initFunc();
      } else {
        document.body.appendChild(script);
      }
    }
SCRIPT_LOADER;
  }
}
