<?php

namespace BitCode\BitForm\Admin\Form\ExtraFieldJS;

class CheckDisableOnMax
{
  public static function init($fk, $field, $contentId)
  {
    return <<<FUNCTIONDEF
      function initCheckDisableOnMax(formContentId = null, parentSelector = '') {
        const contentIds = formContentId ? [formContentId] : Object.keys(bf_globals);
        contentIds.forEach(contentId => {
          const frm = bfSelect("#form-"+contentId);
          const {fields} = bf_globals[contentId];
          const checkFlds = Object.keys(fields).filter(fld => ['check', 'image-select'].includes(fields[fld].typ));
          checkFlds.forEach(fk => {
            if(typeof checkRepeatedField !== 'undefined' && checkRepeatedField(fk, bf_globals[contentId]) && !parentSelector){
              return;
            }
            const classSelector = parentSelector + "."+fk+"-inp-wrp input[type='checkbox']"; 
            frm.querySelectorAll(classSelector).forEach(function(el){
              el.addEventListener("change",function(e){
                  const max = Number(fields[fk].mx);
                  const checked = frm.querySelectorAll(classSelector + ":checked").length;
                  if(checked >= max){
                      frm.querySelectorAll(classSelector).forEach(function(el){
                        if(!el.checked){
                            el.setAttribute("disabled",true);
                        }
                      });
                  }else{
                      const options = fields[fk].opt;
                      frm.querySelectorAll(classSelector).forEach(function(el, index){
                        if(!options[index]?.disabled){
                          el.removeAttribute("disabled");
                        }
                      });
                  }
              });
            });
          })
        })
      }
FUNCTIONDEF;
  }
}
