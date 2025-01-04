<?php

namespace BitCode\BitForm\Admin\Form\ExtraFieldJS;

class SetSliderFieldValue
{
  public static function init($fk, $field, $contentId)
  {
    return <<<FUNCTIONDEF
        function setSliderFieldValue(formContentId = null) {
          const contentIds = formContentId ? [formContentId] : Object.keys(bf_globals);
          contentIds.forEach(contentId => {
            bfSelect("#form-"+contentId)?.querySelectorAll("input[type='range']").forEach(function(el){
              el.addEventListener("input",function(e){
                e.target.parentNode.style.setProperty('--bfv-fld-val', JSON.stringify(el.value));
                const bfFill = ((el.value - (el.min || 0)) / ((el.max || 100) - (el.min || 0))) * 100;
                e.target.style.setProperty('--bfv-fill-lower-track', `\${bfFill}%`, 'important');
              });
              const style = window.getComputedStyle(el);
              el.style.setProperty('--bfv-track-dir', style.direction === 'rtl'? 'to left' : 'to right');
            });
          })
        }
        
FUNCTIONDEF;
  }
}
