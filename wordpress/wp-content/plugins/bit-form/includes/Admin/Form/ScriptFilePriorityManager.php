<?php

namespace BitCode\BitForm\Admin\Form;

class ScriptFilePriorityManager
{
  public static function jsFile()
  {
    /**
     * file load priority
     * first priority status code 100
     *  sub status code link 101,102
     * second priority status code 200
     *  sub status code link 201,202, 203
     * continue..........
     * priority = 100 range for helper file
     * priority = 200 range for field file
     * priority = 300 range for script file
     * priority = 400 range for custom script file
     * priority = 500 range for js init for custom field file
     * priority = 600 range for js init for CDN field file
     * Priority = 700 range for validation js file
     * priority = 800 range for conditional js file
     * @array [fieldType] => [
     *  priority => 'choose one of the priority status code',
     *  filename => 'fileName',
     *  scriptTyp => 'script' or 'init' or 'custom' by default script
     *  source => 'filePath' or 'url' or 'cdn' or className or customClassName by scriptTyp && by default it will be 'filePath'=> 'assets/js/fileName.js'',
     *  fk => 'field key'
     *  field => 'field object'
     * ]

     * final priority
        helper js
        common js
        custom field script js
        init for custom field js
        inti for CDN field js
        validation js
        confirmation js
        conditional js
        submit js
        custom js
     */
    $initSource = '\\BitCode\\BitForm\\Admin\\Form\\InitJs\\';
    $customSource = '\\BitCode\\BitForm\\Admin\\Form\\ExtraFieldJS\\';
    return [
      'helperScript' => [
        ['priority' => 101, 'filename' => 'bfSelect.min.js'],
        ['priority' => 101, 'filename' => 'bfReset.min.js'],
        ['priority' => 101, 'filename' => 'setBFMsg.min.js'],
        ['priority' => 101, 'filename' => 'scrollToElm.min.js'],
        ['priority' => 101, 'filename' => 'getFldKeyAndRowIndx.min.js'],
        ['priority' => 101, 'filename' => 'moveToFirstErrFld.min.js'],
        ['priority' => 101, 'filename' => 'bfValidationErrMsg.min.js'],
        ['priority' => 101, 'filename' => 'setHiddenFld.min.js'],
        ['priority' => 101, 'filename' => 'submit-form.min.js'],
        ['priority' => 101, 'filename' => 'setStyleProperty.min.js']
      ],
      'range' => [
        ['priority' => 301, 'filename' => 'SetSliderFieldValue', 'scriptTyp' => 'custom', 'source' => $customSource, 'path' => 'showValue'],
      ],
      'country' => [
        ['priority' => 101, 'filename' => 'observeElm.min.js'],
        ['priority' => 301, 'filename' => 'bit-country-field.min.js'],
        ['priority' => 201, 'filename' => 'customFieldsReset.min.js'],
        ['priority' => 101, 'filename' => 'observeElm.min.js'],
      ],
      'currency' => [
        ['priority' => 101, 'filename' => 'observeElm.min.js'],
        ['priority' => 301, 'filename' => 'bit-currency-field.min.js'],
        ['priority' => 201, 'filename' => 'customFieldsReset.min.js'],
        ['priority' => 101, 'filename' => 'observeElm.min.js'],
      ],
      'select' => [
        ['priority' => 101, 'filename' => 'observeElm.min.js'],
        ['priority' => 301, 'filename' => 'bit-select-field.min.js'],
        ['priority' => 201, 'filename' => 'customFieldsReset.min.js'],
        ['priority' => 101, 'filename' => 'observeElm.min.js'],
      ],
      'advanced-file-up' => [
        ['priority' => 101, 'filename' => 'observeElm.min.js'],
        ['priority' => 202, 'filename' => 'bit-filepond.min.js'],
        ['priority' => 301, 'filename' => 'bit-advanced-file-up-field.min.js'],
        ['priority' => 101, 'filename' => 'advancedFileHandle.min.js'],
        ['priority' => 201, 'filename' => 'customFieldsReset.min.js'],
      ],
      'file-up' => [
        ['priority' => 301, 'filename' => 'bit-file-up-field.min.js'],
        ['priority' => 201, 'filename' => 'customFieldsReset.min.js'],
      ],
      'phone-number' => [
        ['priority' => 101, 'filename' => 'observeElm.min.js'],
        ['priority' => 301, 'filename' => 'bit-phone-number-field.min.js'],
        ['priority' => 201, 'filename' => 'customFieldsReset.min.js'],
        ['priority' => 101, 'filename' => 'observeElm.min.js'],
      ],
      'paypal' => [
        ['priority' => 201, 'filename' => 'isFormValidatedWithoutError.min.js'],
        ['priority' => 202, 'filename' => 'saveFormProgress.min.js'],
        ['priority' => 201, 'filename' => 'customFieldsReset.min.js'],
        ['priority' => 301, 'filename' => 'bit-paypal-field.min.js'],
      ],
      'stripe' => [
        ['priority' => 201, 'filename' => 'bitsFetchFront.min.js'],
        ['priority' => 201, 'filename' => 'isFormValidatedWithoutError.min.js'],
        ['priority' => 201, 'filename' => 'customFieldsReset.min.js'],
        ['priority' => 202, 'filename' => 'saveFormProgress.min.js'],
        ['priority' => 301, 'filename' => 'bit-stripe-field.min.js'],
      ],
      'mollie' => [
        ['priority' => 201, 'filename' => 'bitsFetchFront.min.js'],
        ['priority' => 201, 'filename' => 'isFormValidatedWithoutError.min.js'],
        ['priority' => 201, 'filename' => 'customFieldsReset.min.js'],
        ['priority' => 202, 'filename' => 'saveFormProgress.min.js'],
        ['priority' => 301, 'filename' => 'bit-mollie-field.min.js'],
      ],
      'repeater' => [
        ['priority' => 102, 'filename' => 'bit-repeater-field.min.js'],
        ['priority' => 701, 'filename' => 'checkRepeatedField.min.js'],
        ['priority' => 701, 'filename' => 'isRepeaterField.min.js'],
        ['priority' => 701, 'filename' => 'getRepeatedIndexes.min.js'],
        ['priority' => 701, 'filename' => 'getIndexesBaseOnConditions.min.js'],
        ['priority' => 201, 'filename' => 'customFieldsReset.min.js'],
      ],
      'razorpay' => [
        ['priority' => 201, 'filename' => 'replaceFieldAndSmartValues.min.js'],
        ['priority' => 201, 'filename' => 'isFormValidatedWithoutError.min.js'],
        ['priority' => 201, 'filename' => 'customFieldsReset.min.js'],
        ['priority' => 202, 'filename' => 'saveFormProgress.min.js'],
        ['priority' => 301, 'filename' => 'bit-razorpay-field.min.js'],
      ],
      'radio' => [
        ['priority' => 301, 'filename' => 'OtherOptionJS', 'scriptTyp' => 'custom', 'source' => $customSource, 'path' => 'addOtherOpt'],
      ],
      'check' => [
        ['priority' => 301, 'filename' => 'OtherOptionJS', 'scriptTyp' => 'custom', 'source' => $customSource, 'path' => 'addOtherOpt'],
        ['priority' => 301, 'filename' => 'CheckDisableOnMax', 'scriptTyp' => 'custom', 'source' => $customSource, 'path' => 'valid->disableOnMax'],
      ],
      'image-select' => [
        ['priority' => 301, 'filename' => 'CheckDisableOnMax', 'scriptTyp' => 'custom', 'source' => $customSource, 'path' => 'valid->disableOnMax'],
      ],
      'recaptcha' => [
        ['priority' => 301, 'filename' => 'bit-recaptcha-field.min.js'],
      ],
      'decision-box' => [
        ['priority' => 101, 'filename' => 'decisionFldHandle.min.js'],
      ],
      'gdpr' => [
        ['priority' => 101, 'filename' => 'decisionFldHandle.min.js'],
      ],
      'signature' => [
        ['priority' => 201, 'filename' => 'customFieldsReset.min.js'],
        ['priority' => 301, 'filename' => 'bit-signature-field.min.js'],
      ],
      'rating' => [
        ['priority' => 201, 'filename' => 'customFieldsReset.min.js'],
        ['priority' => 301, 'filename' => 'bit-rating-field.min.js'],
      ],
    ];
  }

  public static function filePondPlugins($plugin)
  {
    $plugins = [
      'allowFileSizeValidation' => ['priority' => 201, 'filename' => 'bit-filepond-plugin-file-validate-size.min.js'],
      'allowFileTypeValidation' => ['priority' => 201, 'filename' => 'bit-filepond-plugin-file-validate-type.min.js'],
      'allowImageCrop'          => ['priority' => 201, 'filename' => 'bit-filepond-plugin-image-crop.min.js'],
      'allowImagePreview'       => ['priority' => 201, 'filename' => 'bit-filepond-plugin-image-preview.min.js'],
      'allowImageResize'        => ['priority' => 201, 'filename' => 'bit-filepond-plugin-image-resize.min.js'],
      'allowImageTransform'     => ['priority' => 201, 'filename' => 'bit-filepond-plugin-image-transform.min.js'],
      'allowImageValidateSize'  => ['priority' => 201, 'filename' => 'bit-filepond-plugin-image-validate-size.min.js'],
      'allowPreview'            => ['priority' => 201, 'filename' => 'bit-filepond-plugin-media-preview.min.js'],
    ];
    if (array_key_exists($plugin, $plugins)) {
      return $plugins[$plugin];
    }

    return false;
  }

  public static function getAllFldConfs()
  {
    // $contentId = $fieldData['contentId'];
    // $fieldKey = $fieldData['fk'];
    // $field = $fieldData['field'];
    // key is config path, value is field path
    // var is for variable value
    // path is for field path [ for multiple paths]
    // val is for static value
    $configs = [
      'select' => [
        'fieldKey'     => ['var' => 'fieldKey'],
        'options'      => ['path' => 'optionsList'],
        'placeholder'  => ['path' => 'ph'],
        'classNames'   => ['path' => 'customClasses'],
        'attributes'   => ['path' => 'customAttributes'],
        'defaultValue' => ['path' => ['val', 'config->defaultValue'], 'val'=> ''],
        'separator'    => ['val' => BITFORMS_BF_SEPARATOR],
        'mn'           => ['path' => 'mn', 'val' => 0],
        'mx'           => ['path' => 'mx', 'val' => 0],
        'disableOnMax' => ['path' => 'valid->disableOnMax', 'val' => false],
      ],
      'country' => [
        'fieldKey'       => ['var' => 'fieldKey'],
        'options'        => ['path' => 'options'],
        'placeholder'    => ['path' => 'ph'],
        'assetsURL'      => ['val' => BITFORMS_ROOT_URI . '/static/countries/'],
        'classNames'     => ['path' => 'customClasses'],
        'attributes'     => ['path' => 'customAttributes'],
        'defaultValue'   => ['path' => ['val', 'config->defaultValue'], 'val'=> ''],
      ],
      'currency' => [
        'fieldKey'       => ['var' => 'fieldKey'],
        'options'        => ['path' => 'options'],
        'assetsURL'      => ['val' => BITFORMS_ROOT_URI . '/static/currencies/'],
        'classNames'     => ['path' => 'customClasses'],
        'attributes'     => ['path' => 'customAttributes'],
        'defaultValue'   => ['path' => ['val', 'config->defaultValue'], 'val'=> ''],
      ],
      'phone-number' => [
        'fieldKey'   => ['var' => 'fieldKey'],
        'options'    => ['path' => 'options'],
        'assetsURL'  => ['val' => BITFORMS_ROOT_URI . '/static/countries/'],
        'classNames' => ['path' => 'customClasses'],
        'attributes' => ['path' => 'customAttributes']
      ],
      'file-up' => [
        'fieldKey'      => ['var' => 'fieldKey'],
        'formID'        => ['var' => 'formId'],
        'maxSizeErrMsg' => ['path' => ['err->maxSize->msg', 'err->maxSize->dflt']],
        'minFileErrMsg' => ['path' => ['err->minFile->msg', 'err->minFile->dflt']],
        'maxFileErrMsg' => ['path' => ['err->maxFile->msg', 'err->maxFile->dflt']],
        'assetsURL'     => ['val' => BITFORMS_ROOT_URI . '/static/file-upload/'],
        'classNames'    => ['path' => 'customClasses'],
        'attributes'    => ['path' => 'customAttributes'],
        'fieldName'     => ['path' => 'fieldName'],
      ],
      'paypal' => [
        'payIntegID'         => ['path' => 'payIntegID'],
        'style'              => ['path' => 'style'],
        'currency'           => ['path' => 'currency', 'val' => 'USD'],
        'amount'             => ['path' => 'amount', 'val' => 0],
        'amountFld'          => ['path' => 'amountFld', 'val' => ''],
        'shipping'           => ['path' => 'shipping', 'val' => 0],
        'shippingVal'        => ['path' => 'shippingFld', 'val' => ''],
        'tax'                => ['path' => 'tax', 'val' => 0],
        'taxVal'             => ['path' => 'taxFld', 'val' => ''],
        'description'        => ['path' => 'description', 'val' => ''],
        'descFld'            => ['path' => 'descFld', 'val' => ''],
        'payType'            => ['path' => 'payType', 'val' => 'payment'],
        'planId'             => ['path' => 'planId', 'val' => ''],
        'locale'             => ['path' => 'locale', 'val' => 'en_US'],
        'disableFunding'     => ['path' => 'disableFunding', 'val' => ''],
        'fieldKey'           => ['var' => 'fieldKey'],
        'clientId'           => ['path' => 'clientId'],
        'contentId'          => ['var' => 'contentId'],
        'namespace'          => ['val' => 'bit-paypal-__$contentId__'],
      ],
      'stripe' => [
        'payIntegID'          => ['path' => 'payIntegID'],
        'locale'              => ['path' => 'config->options->locale', 'val' => 'en'],
        'amountType'          => ['path' => 'config->amountType', 'val'=> 'fixed'],
        'fieldKey'            => ['var' => 'fieldKey'],
        'contentId'           => ['var' => 'contentId'],
        'publishableKey'      => ['path' => 'publishableKey'],
      ],
      'mollie' => [
        'payIntegID'          => ['path' => 'payIntegID'],
        'locale'              => ['path' => 'config->locale', 'val' => 'en'],
        'amountType'          => ['path' => 'config->amountType', 'val'=> 'fixed'],
        'fieldKey'            => ['var' => 'fieldKey'],
        'contentId'           => ['var' => 'contentId'],
        'apiKey'              => ['path' => 'apiKey'],
        'payment_method'      => ['path' => 'config->payment_method', 'val' => ['creditcard']],
        'description'         => ['path' => 'config->description', 'val' => ''],
        'amount'              => ['path' => 'config->amount', 'val' => 0],
        'currency'            => ['path' => 'config->currency', 'val' => 'EUR'],
      ],
      'advanced-file-up' => [
        'fieldKey'           => ['var' => 'fieldKey'],
        'formID'             => ['var' => 'formId'],
        'configSetting'      => ['path' => 'config'],
        'ajaxURL'            => ['val' => admin_url('admin-ajax.php')],
        'nonce'              => ['val' => wp_create_nonce('bitforms_save')],
        'uploadFileToServer' => ['val' => 1],
        'fieldName'          => ['path' => 'fieldName'],
        'contentId'          => ['var' => 'contentId'],
      ],
      'razorpay' => [
        'fieldKey'           => ['var' => 'fieldKey'],
        'options'            => ['path' => 'options'],
        'payIntegID'         => ['path' => 'payIntegID'],
        'payType'            => ['path' =>'payType', 'val' => ''],
        'clientId'           => ['path' => 'clientId'],
        'contentId'          => ['var' => 'contentId'],
        'includeOrderId'     => ['path' => 'includeOrderId', 'val' => false],
        'newOrderId'         => ['path' => 'newOrderId', 'val' => false],
        'orderIdFld'         => ['path' => 'orderIdFld', 'val' => ''],
      ],
      'repeater' => [
        'fieldKey'           => ['var' => 'fieldKey'],
        'fieldName'          => ['path' => 'fieldName'],
        'contentId'          => ['var' => 'contentId'],
        'defaultRow'         => ['path' => 'defaultRow', 'val' => 1],
        'minimumRow'         => ['path' => 'minRow', 'val' => 1],
        'maximumRow'         => ['path' => 'maxRow', 'val' => 0],
        'showAddBtn'         => ['path' => 'addBtn->show', 'val' => false],
        'showAddToEndBtn'    => ['path' => 'addToEndBtn->show', 'val' => false],
        'defaultValue'       => ['path' => 'val', 'val' => ''],
      ],
      'signature' => [
        'fieldKey'                     => ['var' => 'fieldKey'],
        'contentId'                    => ['var' => 'contentId'],
        'maxWidth'                     => ['path' => 'config->maxWidth', 'val' => '1'],
        'penColor'                     => ['path' => 'config->penColor', 'val'=> '#000000'],
        'backgroundColor'              => ['path' => 'config->backgroundColor', 'val' => '#ffffff'],
        'imgTyp'                       => ['path' => 'config->imgTyp', 'val' => 'image/png'],
        'assetsURL'                    => ['val' => BITFORMS_ROOT_URI . '/static/signature/']
      ],
      'rating' => [
        'fieldKey'                     => ['var' => 'fieldKey'],
        'contentId'                    => ['var' => 'contentId'],
        'options'                      => ['path' => 'opt'],
        'showReviewLblOnHover'         => ['path' => 'showReviewLblOnHover'],
        'showReviewLblOnSelect'        => ['path' => 'showReviewLblOnSelect'],
        'selectedRating'               => ['path' => 'selectedRating'],
        'defaultValue'                 => ['path' => ['val', 'config->defaultValue'], 'val'=> ''],
      ]
    ];

    return $configs;
  }

  public static function multiStepFiles()
  {
    return [
      ['priority' => 201, 'filename' => 'customFieldsReset.min.js'],
      ['priority' => 201, 'filename' => 'isFormValidatedWithoutError.min.js'],
      ['priority' => 202, 'filename' => 'saveFormProgress.min.js'],
      ['priority' => 700, 'filename' => 'bit-multi-step-form.min.js'],
    ];
  }

  public static function conversationalFormFiles()
  {
    return [
      ['priority' => 202, 'filename' => 'saveFormProgress.min.js'],
      ['priority' => 700, 'filename' => 'bit-conversational-form.min.js'],
      ['priority' => 700, 'filename' => 'handleConversationalFormMsg.min.js'],
    ];
  }

  public static function frontendScriptFile()
  {
    return [
      'observeElm'            => ['priority' => 101, 'filename' => 'observeElm.min.js'],
      'hidden-token-field'    => ['priority' => 300, 'filename' => 'hidden-token-field.min.js'],
    ];
  }

  public static function validationAndOtherScriptFile()
  {
    return [
      'checkFldValidation'             => ['priority' => 701, 'filename' => 'checkFldValidation.min.js'],
      'checkMinMaxOptions'             => ['priority' => 702, 'filename' => 'checkMinMaxOptions.min.js'],
      'checkMinMaxValue'               => ['priority' => 702, 'filename' => 'checkMinMaxValue.min.js'],
      'customOptionValidation'         => ['priority' => 702, 'filename' => 'customOptionValidation.min.js'],
      'dcsnbxFldValidation'            => ['priority' => 702, 'filename' => 'dcsnbxFldValidation.min.js'],
      'emailFldValidation'             => ['priority' => 702, 'filename' => 'emailFldValidation.min.js'],
      'fileupFldValidation'            => ['priority' => 702, 'filename' => 'fileupFldValidation.min.js'],
      'advanceFileupFldValidation'     => ['priority' => 702, 'filename' => 'advanceFileUpFldValidation.min.js'],
      'phoneNumberFldValidation'       => ['priority' => 702, 'filename' => 'phoneNumberFldValidation.min.js'],
      'generateBackslashPattern'       => ['priority' => 700, 'filename' => 'generateBackslashPattern.min.js'],
      'nmbrFldValidation'              => ['priority' => 702, 'filename' => 'nmbrFldValidation.min.js'],
      'regexPatternValidation'         => ['priority' => 701, 'filename' => 'regexPatternValidation.min.js'], // load before generateBackslashPattern file, then load  regexPatternValidation
      'requiredFldValidation'          => ['priority' => 700, 'filename' => 'requiredFldValidation.min.js'],
      'urlFldValidation'               => ['priority' => 702, 'filename' => 'urlFldValidation.min.js'],
      'validation'                     => ['priority' => 705, 'filename' => 'validateForm.min.js'], // last priority for validation script
      'conditionalLogic'               => ['priority' => 705, 'filename' => 'bit-conditionals.min.js'], // last priority for validation script
      'resetPlaceholders'              => ['priority' => 201, 'filename' => 'resetPlaceholders.min.js'],
      'validateFocusLost'              => ['priority' => 706, 'filename' => 'validate-focus.min.js'], // last priority for validation script
    ];
  }

  public static function validationScriptFileMapping($fieldType)
  {
    $textTypeField = ['text', 'password', 'username', 'date', 'datetime-local', 'time', 'month', 'week', 'color'];
    if (in_array($fieldType, $textTypeField)) {
      $fieldType = 'text';
    }
    $fields =
      [
        'text' => [
          'regexPatternValidation' => [
            'paths'        => ['valid->regexr'],
            'dependencies' => [
              'generateBackslashPattern'
            ]
          ]
        ],
        'email' => [
          'emailFldValidation' => [
            'paths'        => ['err->invalid->show'],
            'dependencies' => [
              'generateBackslashPattern'
            ]
          ],
        ],
        'number' => [
          'nmbrFldValidation' => [
            'paths' => ['mn', 'mx']
          ]
        ],
        'radio' => [
          'customOptionValidation' => [
            'paths'        => ['valid->otherOptReq'],
          ]
        ],
        'check' => [
          'checkMinMaxOptions' => [
            'paths'        => ['mn', 'mx'],
            'dependencies' => [
              'checkFldValidation'
            ]
          ],
          'customOptionValidation' => [
            'paths'        => ['valid->otherOptReq'],
          ]
        ],
        'select' => [
          'checkMinMaxOptions' => [
            'paths'        => ['mn', 'mx'],
            'dependencies' => [
              'checkFldValidation'
            ]
          ],
        ],
        'image-select' => [
          'checkMinMaxOptions' => [
            'paths'        => ['mn', 'mx'],
            'dependencies' => [
              'checkFldValidation'
            ]
          ],
        ],
        'range' => [
          'checkMinMaxValue' => [
            'paths' => ['mn', 'mx']
          ]
        ],
        'url' => [
          'regexPatternValidation' => [
            'paths'        => ['valid->regexr'],
            'dependencies' => [
              'generateBackslashPattern'
            ]
          ],
          'urlFldValidation' => [
            'paths'        => ['err->invalid->show'],
            'dependencies' => [
              'generateBackslashPattern'
            ]
          ]
        ],
        'decision-box' => [
          'dcsnbxFldValidation' => [
            'paths' => ['valid->req']
          ]
        ],
        'gdpr' => [
          'dcsnbxFldValidation' => [
            'paths' => ['valid->req']
          ]
        ],
        'file-up' => [
          'fileupFldValidation' => [
            'paths' => ['valid->req']
          ]
        ],
        'advanced-file-up' => [
          'advanceFileupFldValidation' => [
            'paths' => ['valid->req']
          ]
        ],
        'phone-number' => [
          'phoneNumberFldValidation' => [
            'paths'        => ['err->invalid->show'],
          ]
        ],
      ];
    return isset($fields[$fieldType]) ? $fields[$fieldType] : [];
  }

  public static function formAbandonmentNeededFiles($abandonType = null)
  {
    $files = [
      'autoSave' => [
        ['priority' => 201, 'filename' => 'bit-page-lifecycle.min.js'],
        ['priority' => 402, 'filename' => 'autoSavePartial.min.js'],
      ]
    ];

    $required = [
      ['priority' => 402, 'filename' => 'bit-form-abandonment.min.js'],
      ['priority' => 401, 'filename' => 'saveFormProgress.min.js'],
      ['priority' => 701, 'filename' => 'setFieldValues.min.js'],
    ];
    return isset($files[$abandonType]) ? array_merge($files[$abandonType], $required) : $required;
  }
}
