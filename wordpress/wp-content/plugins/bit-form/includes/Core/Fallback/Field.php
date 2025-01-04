<?php

namespace BitCode\BitForm\Core\Fallback;

use BitCode\BitForm\Admin\Form\AdminFormHandler;
use BitCode\BitForm\Core\Database\FormModel;

class Field
{
  public function buttonValidsObject()
  {
    $formModel = new FormModel();
    $forms = $formModel->get(
      ['id', 'form_content']
    );

    if (!is_wp_error($forms)) {
      foreach ($forms as $form) {
        $formID = $form->id;
        $formContent = json_decode($form->form_content);
        $fields = $formContent->fields;

        foreach ($fields as $fldKey => $fldData) {
          if ('button' === $fldData->typ && !isset($fldData->valid)) {
            $fldData->valid = (object) [];
          }
          $fields->{$fldKey} = $fldData;
        }

        $formContent->fields = $fields;

        $formModel->update(
          [
            'form_content' => wp_json_encode($formContent),
          ],
          [
            'id' => $formID,
          ]
        );
      }
    }
  }

  public function addButton()
  {
    // if version is lower or equal than 1.3.13
    $formModel = new FormModel();
    $forms = $formModel->get(
      ['id', 'form_content'],
      [
        'form_content' => ['operator' => 'LIKE', 'value' => '%\"buttons\":%'],
      ]
    );
    if (!is_wp_error($forms)) {
      $adminFormHandler = new AdminFormHandler();
      foreach ($forms as $form) {
        $formID = $form->id;
        $formContent = json_decode($form->form_content);
        $fields = wp_json_encode($formContent->fields);
        if (isset($formContent->buttons)) {
          if (false === strpos($fields, '"btnTyp":"submit"')) {
            $align = isset($formContent->buttons->align) ? $formContent->buttons->align : 'right';
            $btnSiz = isset($formContent->buttons->btnSiz) ? $formContent->buttons->btnSiz : 'md';
            $fulW = isset($formContent->buttons->fulW) ? $formContent->buttons->fulW : false;
            $btnData = [
              'typ'    => 'button',
              'btnSiz' => $btnSiz,
              'fulW'   => $fulW,
            ];
            $newID = 0;
            foreach ($formContent->fields as $key => $value) {
              $oldID = \preg_replace('/bf?\d+-(\d+)-?/', '$1', $key);
              if ($oldID > $newID) {
                $newID = $oldID;
              }
            }
            $newID = $newID + 1;
            $sBtnID = "bf${formID}-${newID}";
            $lY = 0;
            $mY = 0;
            $sY = 0;
            foreach ($formContent->layout->lg as $lg) {
              if ($lg->y > $lY) {
                $lY = $lg->y;
              }
            }
            $lY = $lY + $lg->h + 1;
            foreach ($formContent->layout->md as $md) {
              if ($md->y > $mY) {
                $mY = $md->y;
              }
            }
            $mY = $mY + $md->h + 1;
            foreach ($formContent->layout->sm as $sm) {
              if ($sm->y > $sY) {
                $sY = $sm->y;
              }
            }
            $sY = $sY + $sm->h + 1;
            if (isset($formContent->buttons->rstBtnTxt)) {
              $newID += 1;
              $rBtnID = "bf${formID}-${newID}";

              if ($fulW) {
                $btnData['btnTyp'] = 'submit';
                $btnData['align'] = $align;
                $btnData['txt'] = $formContent->buttons->subBtnTxt;
                $formContent->fields->{$sBtnID} = (object) $btnData;
                $rBtnData = $btnData;
                $rBtnData['btnTyp'] = 'reset';
                $rBtnData['align'] = $align;
                $rBtnData['txt'] = $formContent->buttons->rstBtnTxt;
                $formContent->fields->{$rBtnID} = (object) $rBtnData;
              } else {
                $btnData['btnTyp'] = 'submit';
                $btnData['align'] = 'right';
                $btnData['txt'] = $formContent->buttons->subBtnTxt;
                $formContent->fields->{$sBtnID} = (object) $btnData;
                $rBtnData = $btnData;
                $rBtnData['btnTyp'] = 'reset';
                $rBtnData['align'] = 'left';
                $rBtnData['txt'] = $formContent->buttons->rstBtnTxt;
                $formContent->fields->{$rBtnID} = (object) $rBtnData;
              }
              $subBtnLay = [
                'h'    => 2,
                'i'    => $sBtnID,
                'minH' => 2,
                'x'    => 0,
                'y'    => 1000,
              ];

              $subBtnLay['w'] = 3;
              $subBtnLay['y'] = $lY;

              $formContent->layout->lg[] = (object) $subBtnLay;

              $subBtnLay['w'] = 2;
              $subBtnLay['y'] = $mY;

              $formContent->layout->md[] = (object) $subBtnLay;

              $subBtnLay['w'] = 1;
              $subBtnLay['y'] = $sY;

              $formContent->layout->sm[] = (object) $subBtnLay;

              $subBtnLay['i'] = $rBtnID;
              $subBtnLay['x'] = 3;
              $subBtnLay['w'] = 3;
              $subBtnLay['y'] = $lY;

              $formContent->layout->lg[] = (object) $subBtnLay;

              $subBtnLay['x'] = 2;
              $subBtnLay['w'] = 2;
              $subBtnLay['y'] = $mY;

              $formContent->layout->md[] = (object) $subBtnLay;

              $subBtnLay['w'] = 1;
              $subBtnLay['x'] = 1;
              $subBtnLay['y'] = $sY;

              $formContent->layout->sm[] = (object) $subBtnLay;
            } else {
              $btnData['btnTyp'] = 'submit';
              $btnData['txt'] = $formContent->buttons->subBtnTxt;
              $btnData['align'] = $align;
              $formContent->fields->{$sBtnID} = (object) $btnData;

              $subBtnLay = [
                'h'    => 2,
                'i'    => $sBtnID,
                'minH' => 2,
                'x'    => 0,
                'w'    => 6,
              ];

              $subBtnLay['y'] = $lY;

              $formContent->layout->lg[] = (object) $subBtnLay;

              $subBtnLay['y'] = $mY;

              $formContent->layout->md[] = (object) $subBtnLay;

              $subBtnLay['y'] = $sY;

              $formContent->layout->sm[] = (object) $subBtnLay;
            }
          }
          $adminFormHandler->saveLayoutStyleSheet($formContent->layout, 'bitform-layout-' . $formID . '.css');
          unset($formContent->buttons);
          $formModel->update(
            [
              'form_content' => wp_json_encode($formContent),
            ],
            [
              'id' => $formID,
            ]
          );
        }
      }
    }
  }

  public function phoneNumberMissingPattern()
  {
    $formModel = new FormModel();
    $forms = $formModel->get(
      ['id', 'form_content']
    );

    if (!is_wp_error($forms)) {
      $missingPatterns = [
        'CA' => '(?:2(?:04|[23]6|[48]9|50|63)|3(?:06|43|54|6[578]|82)|4(?:03|1[68]|[26]8|3[178]|50|74)|5(?:06|1[49]|48|79|8[147])|6(?:04|[18]3|39|47|72)|7(?:0[59]|42|53|78|8[02])|8(?:[06]7|19|25|73)|90[25])[2-9]$_bf_$d{6}',
        'CL' => '2(?:1982[0-6]|3314[05-9])$_bf_$d{3}|(?:2(?:1(?:160|962)|3(?:2$_bf_$d$_bf_$d|3(?:[03467]$_bf_$d|1[0-35-9]|2[1-9]|5[0-24-9]|8[0-3])|600)|646[59])|80[1-9]$_bf_$d$_bf_$d|9(?:3(?:[0-57-9]$_bf_$d$_bf_$d|6(?:0[02-9]|[1-9]$_bf_$d))|6(?:[0-8]$_bf_$d$_bf_$d|9(?:[02-79]$_bf_$d|1[05-9]))|7[1-9]$_bf_$d$_bf_$d|9(?:[03-9]$_bf_$d$_bf_$d|1(?:[0235-9]$_bf_$d|4[0-24-9])|2(?:[0-79]$_bf_$d|8[0-46-9]))))$_bf_$d{4}|(?:22|3[2-5]|[47][1-35]|5[1-3578]|6[13-57]|8[1-9]|9[2458])$_bf_$d{7}',
        'DK' => '(?:[2-7]$_bf_$d|8[126-9]|9[1-46-9])$_bf_$d{6}',
        'GU' => '671(?:3(?:00|3[39]|4[349]|55|6[26])|4(?:00|56|7[1-9]|8[02-46-9])|5(?:55|6[2-5]|88)|6(?:3[2-578]|4[24-9]|5[34]|78|8[235-9])|7(?:[0479]7|2[0167]|3[45]|8[7-9])|8(?:[2-57-9]8|6[48])|9(?:2[29]|6[79]|7[1279]|8[7-9]|9[78]))$_bf_$d{4}',
        'MP' => '670(?:2(?:3[3-7]|56|8[4-8])|32[1-38]|4(?:33|8[348])|5(?:32|55|88)|6(?:64|70|82)|78[3589]|8[3-9]8|989)$_bf_$d{4}',
        'PR' => '(?:787|939)[2-9]$_bf_$d{6}',
        'US' => '5056(?:[0-35-9]$_bf_$d|4[46])$_bf_$d{4}|(?:4722|505[2-57-9])$_bf_$d{6}|(?:2(?:0[1-35-9]|1[02-9]|2[03-589]|3[149]|4[08]|5[1-46]|6[0279]|7[0269]|8[13])|3(?:0[1-57-9]|1[02-9]|2[01356]|3[0-24679]|4[167]|5[0-2]|6[014]|8[056])|4(?:0[124-9]|1[02-579]|2[3-5]|3[0245]|4[023578]|58|6[349]|7[0589]|8[04])|5(?:0[1-47-9]|1[0235-8]|20|3[0149]|4[01]|5[179]|6[1-47]|7[0-5]|8[0256])|6(?:0[1-35-9]|1[024-9]|2[03689]|[34][016]|5[01679]|6[0-279]|78|8[0-29])|7(?:0[1-46-8]|1[2-9]|2[04-7]|3[1247]|4[037]|5[47]|6[02359]|7[0-59]|8[156])|8(?:0[1-68]|1[02-8]|2[068]|3[0-2589]|4[03578]|5[046-9]|6[02-5]|7[028])|9(?:0[1346-9]|1[02-9]|2[0589]|3[0146-8]|4[01357-9]|5[12469]|7[0-389]|8[04-69]))[2-9]$_bf_$d{6}',
        'VI' => '340(?:2(?:0[0-368]|2[06-8]|4[49]|77)|3(?:32|44)|4(?:2[23]|44|7[34]|89)|5(?:1[34]|55)|6(?:2[56]|4[23]|77|9[023])|7(?:1[2-57-9]|2[57]|7$_bf_$d)|884|998)$_bf_$d{4}'
      ];

      foreach ($forms as $form) {
        $formID = $form->id;
        $formContent = json_decode($form->form_content);
        $fields = $formContent->fields;

        foreach ($fields as $fldKey => $fldData) {
          if ('phone-number' === $fldData->typ) {
            $options = $fldData->options;
            $optCount = count($options);
            for ($i = 0; $i < $optCount; $i++) {
              $opt = $options[$i];
              if (!isset($opt->ptrn) && isset($missingPatterns[$opt->i])) {
                $options[$i]->ptrn = $missingPatterns[$opt->i];
              }
            }
            $fldData->options = $options;
            $fields->{$fldKey} = $fldData;
          }
        }

        $formContent->fields = $fields;

        $formModel->update(
          [
            'form_content' => wp_json_encode($formContent),
          ],
          [
            'id' => $formID,
          ]
        );
      }
    }
  }
}
