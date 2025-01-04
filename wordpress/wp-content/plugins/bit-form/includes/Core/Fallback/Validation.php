<?php

namespace BitCode\BitForm\Core\Fallback;

use BitCode\BitForm\Core\Database\FormModel;

class Validation
{
  public function errorMessage()
  {
    $formModel = new FormModel();
    $forms = $formModel->get(
      ['id', 'form_content']
    );

    if (isset($forms) && !is_wp_error($forms)) {
      foreach ($forms as $form) {
        $formID = $form->id;
        $formContent = json_decode($form->form_content);
        $fields = $formContent->fields;

        foreach ($fields as $fldKey => $fldData) {
          if (isset($fldData->valid->req)) {
            if (!isset($fldData->err)) {
              $fldData->err = (object) [];
            }
            $fldData->err->req = (object) [
              'show' => true,
              'dflt' => 'This field is required',
            ];
          }

          if ('check' === $fldData->typ) {
            $reqOpts = array_map(function ($op) {
              return $op->lbl;
            }, array_filter($fldData->opt, function ($ot) {
              return isset($ot->req);
            }));
            if (!empty($reqOpts)) {
              if (!isset($fldData->err)) {
                $fldData->err = (object) [];
              }
              $fldData->err->req = (object) [
                'show' => true,
                'dflt' => implode(', ', $reqOpts) . ' is required',
              ];
            }
          }

          if ('number' === $fldData->typ) {
            if (!isset($fldData->err)) {
              $fldData->err = (object) [];
            }
            $fldData->err->invalid = (object) [
              'show' => true,
              'dflt' => 'Number is invalid',
            ];

            if (isset($fldData->mn)) {
              $fldData->err->mn = (object) [
                'show' => true,
                'dflt' => 'Minimum number is ' . $fldData->mn,
              ];
            }

            if (isset($fldData->mx)) {
              $fldData->err->mx = (object) [
                'show' => true,
                'dflt' => 'Maximum number is ' . $fldData->mx,
              ];
            }
          }

          if ('email' === $fldData->typ) {
            $fldData->pattern = '^$_bf_$w+([.-]?$_bf_$w+)*@$_bf_$w+([.-]?$_bf_$w+)*($_bf_$.$_bf_$w{2,3})+$';
            if (!isset($fldData->err)) {
              $fldData->err = (object) [];
            }
            $fldData->err->invalid = (object) [
              'show' => true,
              'dflt' => 'Email is invalid',
            ];
          }

          if ('url' === $fldData->typ) {
            $fldData->attr->pattern = '^(?:(?:https?|ftp):$_bf_$/$_bf_$/)?(?:(?!(?:10|127)(?:$_bf_$.$_bf_$d{1,3}){3})(?!(?:169$_bf_$.254|192$_bf_$.168)(?:$_bf_$.$_bf_$d{1,3}){2})(?!172$_bf_$.(?:1[6-9]|2$_bf_$d|3[0-1])(?:$_bf_$.$_bf_$d{1,3}){2})(?:[1-9]$_bf_$d?|1$_bf_$d$_bf_$d|2[01]$_bf_$d|22[0-3])(?:$_bf_$.(?:1?$_bf_$d{1,2}|2[0-4]$_bf_$d|25[0-5])){2}(?:$_bf_$.(?:[1-9]$_bf_$d?|1$_bf_$d$_bf_$d|2[0-4]$_bf_$d|25[0-4]))|(?:(?:[a-z$_bf_$u00a1-$_bf_$uffff0-9]-*)*[a-z$_bf_$u00a1-$_bf_$uffff0-9]+)(?:$_bf_$.(?:[a-z$_bf_$u00a1-$_bf_$uffff0-9]-*)*[a-z$_bf_$u00a1-$_bf_$uffff0-9]+)*(?:$_bf_$.(?:[a-z$_bf_$u00a1-$_bf_$uffff]{2,})))(?::$_bf_$d{2,5})?(?:$_bf_$/$_bf_$S*)?$';
            if (!isset($fldData->err)) {
              $fldData->err = (object) [];
            }
            $fldData->err->invalid = [
              'show' => true,
              'dflt' => 'URL is invalid',
            ];
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

  public function email()
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
          if ('email' === $fldData->typ) {
            $fldData->pattern = '^$_bf_$w+([.-]?$_bf_$w+)*@$_bf_$w+([.-]?$_bf_$w+)*($_bf_$.$_bf_$w{1,24})+$';
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
}
