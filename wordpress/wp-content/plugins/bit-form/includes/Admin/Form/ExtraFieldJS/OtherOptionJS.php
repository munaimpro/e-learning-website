<?php

namespace BitCode\BitForm\Admin\Form\ExtraFieldJS;

class OtherOptionJS
{
  public static function init($fk, $field, $contentId)
  {
    return <<<FUNCTIONDEF
        function initAddOtherOpt(formContentId = null) {
          const contentIds = formContentId ? [formContentId] : Object.keys(bf_globals);
          contentIds.forEach(contentId => {
            bfSelect("#form-"+contentId).querySelectorAll("input[data-bf-other-inp]").forEach(function(el){
              el.addEventListener("keyup",function(e){
                e.target.parentNode.parentNode.querySelector("input").value = el.value;
                e.target.parentNode.parentNode.querySelector("input").dispatchEvent(new Event('input'));
              });
            });
          })
        }
        function resetOtherOpt(){
          document.querySelectorAll("input[data-oopt]").forEach(function(el){
            el.value = '';
          });
        }
FUNCTIONDEF;
  }
}
