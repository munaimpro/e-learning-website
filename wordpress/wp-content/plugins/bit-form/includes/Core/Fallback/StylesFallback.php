<?php

namespace BitCode\BitForm\Core\Fallback;

use BitCode\BitForm\Core\Database\FormModel;
use BitCode\BitForm\Core\Util\Utilities;
use BitCode\BitForm\Jcof\Jcof;

class StylesFallback
{
  public function signatureFldIframeStyle()
  {
    $formModel = new FormModel();
    $forms = $formModel->get(
      ['id', 'form_content', 'builder_helper_state']
    );

    if (is_wp_error($forms)) {
      return;
    }

    $signatureIframeStyle = [
      'position'      => 'absolute',
      'top'           => 0,
      'left'          => 0,
      'width'         => '100%',
      'height'        => '100%',
      'opacity'       => 0,
      'pointer-events'=> 'none',
      'border'        => 'none',
    ];

    foreach ($forms as $form) {
      $hasStyleChanged = false;
      $formId = $form->id;
      $builderHelperState = json_decode($form->builder_helper_state);
      if (empty($builderHelperState->staticStyles)) {
        continue;
      }
      $staticStyleState = $builderHelperState->staticStyles;
      $staticStyleState = Jcof::parse($staticStyleState);

      $formContent = json_decode($form->form_content);
      $fields = $formContent->fields;

      foreach ($fields as $fldKey => $fldData) {
        if ('signature' !== $fldData->typ) {
          continue;
        }
        if (!isset($staticStyleState['staticStyles'])) {
          $staticStyleState['staticStyles'] = [];
        }
        $iframeKey = ".{$fldKey}-signature-iframe";
        $staticStyleState['staticStyles'][$iframeKey] = $signatureIframeStyle;
        $generatedStyleCSS = Utilities::styleGenerator([$iframeKey => $signatureIframeStyle]);
        Utilities::appendCSS($formId, $generatedStyleCSS);
        $hasStyleChanged = true;
      }

      if (!$hasStyleChanged) {
        continue;
      }

      $staticStyleState = Jcof::stringify($staticStyleState);
      $builderHelperState->staticStyles = $staticStyleState;
      $formModel->update(
        [
          'builder_helper_state' => wp_json_encode($builderHelperState),
        ],
        [
          'id' => $formId,
        ]
      );
    }
  }

  public function stripeFldButtonDisabled()
  {
    $stylesArray = [
      '.__fk__-stripe-btn:disabled' => [
        'background' => '#7ca5e7',
        'transform'  => 'none',
      ],
      '.__fk__-stripe-wrp .stripe-pay-btn:disabled'=> [
        'background' => '#7ca5e7',
        'transform'  => 'none',
      ]
    ];

    $this->addStyles('stripe', $stylesArray);
  }

  public function stripeFldErrorMessage()
  {
    $stylesArray = [
      '.__fk__-err-inner' => [
        'overflow'=> 'hidden',
      ],
    ];

    $this->addStyles('stripe', $stylesArray);
  }

  /**
   *  for adding fallback styles to field in form staticStyles object
   *
   * @param string $fieldType
   * @param array $stylesArray
   * @return void
   */
  private function addStyles(string $fieldType, array $stylesArray): void
  {
    $formModel = new FormModel();
    $forms = $formModel->get(
      ['id', 'form_content', 'builder_helper_state']
    );

    if (is_wp_error($forms)) {
      return;
    }
    foreach ($forms as $form) {
      $hasStyleChanged = false;
      $formId = $form->id;
      $builderHelperState = json_decode($form->builder_helper_state);
      if (empty($builderHelperState->staticStyles)) {
        continue;
      }
      $staticStyleState = $builderHelperState->staticStyles;
      $staticStyleState = Jcof::parse($staticStyleState);

      $formContent = json_decode($form->form_content);
      $fields = $formContent->fields;

      foreach ($fields as $fldKey => $fldData) {
        if ($fieldType !== $fldData->typ) {
          continue;
        }
        if (!isset($staticStyleState['staticStyles'])) {
          $staticStyleState['staticStyles'] = [];
        }

        $newStyles = $this->replaceFldKey($fldKey, $stylesArray);
        foreach ($newStyles as $newStyleKey => $newStyleValue) {
          $staticStyleState['staticStyles'][$newStyleKey] = $newStyleValue;
        }

        $generatedStyleCSS = Utilities::styleGenerator($newStyles);
        Utilities::appendCSS($formId, $generatedStyleCSS);
        $hasStyleChanged = true;
      }

      if (!$hasStyleChanged) {
        continue;
      }
      $staticStyleState = Jcof::stringify($staticStyleState);
      $builderHelperState->staticStyles = $staticStyleState;
      $formModel->update(
        [
          'builder_helper_state' => wp_json_encode($builderHelperState),
        ],
        [
          'id' => $formId,
        ]
      );
    }
  }

  /**
   *  for adding fallback styles in form staticStyles object
   *
   * @return void
   */
  public function addStaticStyleForMultiStepForm(): void
  {
    $stylesArray = [
      '._frm-__fk__-stp-hdr-wrpr' => [
        'flex-wrap' => 'wrap',
      ],
      '@media (max-width: 576px)' => [
        '._frm-__fk__-stp-hdr-lbl' => [
          'font-size' => '12px',
        ],
        '._frm-__fk__-stp-hdr-sub-titl' => [
          'display' => 'none',
        ],
        '._frm-__fk__-stp-progress-bar' => [
          'font-size'=> '.65rem',
          'height'   => '.8rem'
        ],
      ],
    ];
    $formModel = new FormModel();
    $forms = $formModel->get(
      ['id', 'form_content', 'builder_helper_state']
    );

    if (is_wp_error($forms)) {
      return;
    }
    foreach ($forms as $form) {
      $hasStyleChanged = false;
      $formId = $form->id;
      $builderHelperState = json_decode($form->builder_helper_state);
      if (empty($builderHelperState->staticStyles)) {
        continue;
      }
      $staticStyleState = $builderHelperState->staticStyles;
      $staticStyleState = Jcof::parse($staticStyleState);

      $formContent = json_decode($form->form_content);
      $layouts = $formContent->layout;
      if (!is_array($layouts)) {
        continue;
      }

      foreach ($layouts as $layout) {
        if (!isset($staticStyleState['staticStyles'])) {
          $staticStyleState['staticStyles'] = [];
        }

        $newStyles = self::replaceKeys($stylesArray, '__fk__', "b{$formId}");

        $staticStyleState['staticStyles'] = array_merge($staticStyleState['staticStyles'], $newStyles);

        $generatedStyleCSS = Utilities::convertToCSS($newStyles);
        Utilities::appendCSS($formId, $generatedStyleCSS);
        $hasStyleChanged = true;
      }

      if (!$hasStyleChanged) {
        continue;
      }
      $staticStyleState = Jcof::stringify($staticStyleState);
      $builderHelperState->staticStyles = $staticStyleState;
      $formModel->update(
        [
          'builder_helper_state' => wp_json_encode($builderHelperState),
        ],
        [
          'id' => $formId,
        ]
      );
    }
  }

  /**
   * @method for replacing __fk__ with field key in styles array keys
   * @example: .__fk__-stripe-btn:disabled to .bk-1-stripe-btn:disabled
   *
   * @param string $fldKey
   * @param array $stylesArray
   * @return array $newStyleArray
   */
  private function replaceFldKey(string $fldKey, array $stylesArray): array
  {
    $newStyleArray = [];
    foreach ($stylesArray as $key => $value) {
      $key = str_replace('__fk__', $fldKey, $key);
      $newStyleArray[$key] = $value;
    }
    return $newStyleArray;
  }

  // public function signatureFldIframeStyle()
  // {
  //   $signatureIframeStyle = [
  //     '.__fk__-signature-iframe' => [
  //       'position'      => 'absolute',
  //       'top'           => 0,
  //       'left'          => 0,
  //       'width'         => '100%',
  //       'height'        => '100%',
  //       'opacity'       => 0,
  //       'pointer-events'=> 'none',
  //       'border'        => 'none',
  //     ]
  //   ];

  //   $this->addStyles('signature', $signatureIframeStyle);
  // }

  public static function replaceKeys(array $styles, string $search, string $replace): array
  {
    $updatedStyles = [];
    foreach ($styles as $key => $value) {
      $newKey = str_replace($search, $replace, $key);

      if (is_array($value)) {
        $value = self::replaceKeys($value, $search, $replace);
      }
      $updatedStyles[$newKey] = $value;
    }
    return $updatedStyles;
  }
}
