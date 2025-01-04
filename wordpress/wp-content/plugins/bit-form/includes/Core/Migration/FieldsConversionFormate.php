<?php

namespace BitCode\BitForm\Core\Migration;

class FieldsConversionFormate
{
  public static function getFormate($fieldType)
  {
    $formate = [
      'button' => [
        'unchangeProperty'=> ['typ', 'valid', 'btnSiz', 'btnTyp', 'txt', 'fulW'],
        'changedProperty' => [],
        'newProperty'     => [
          'icn'              => ['pos' => '', 'url' => ''],
          'align'            => 'end',
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'button',
        ],
        'removeProperty' => []
      ],
      'text' => [
        'unchangeProperty'=> ['typ', 'lbl', 'valid', 'err', 'adminLbl', 'ac', 'ph'],
        'changedProperty' => [],
        'newProperty'     => [
          'valid->reqShow'   => true,
          'valid->reqPos'    => 'after',
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'text',
          'phHide'           => true,
          'adminLblHide'     => true,
        ],
        'removeProperty' => []
      ],
      'username' => [
        'unchangeProperty'=> ['typ', 'lbl', 'valid', 'err', 'adminLbl', 'ph'],
        'changedProperty' => [],
        'newProperty'     => [
          'valid->reqShow'   => true,
          'valid->reqPos'    => 'after',
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'username',
          'phHide'           => true,
          'adminLblHide'     => true,
        ],
        'removeProperty' => []
      ],
      'textarea' => [
        'unchangeProperty'=> ['typ', 'lbl', 'valid', 'err', 'adminLbl', 'ph'],
        'changedProperty' => [],
        'newProperty'     => [
          'valid->reqShow'   => true,
          'valid->reqPos'    => 'after',
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'textarea',
          'phHide'           => true,
          'adminLblHide'     => true,
        ],
        'removeProperty' => []
      ],
      'check' => [
        'unchangeProperty'=> ['typ', 'lbl', 'opt', 'valid', 'err', 'adminLbl', 'mn', 'mx', 'customType'],
        'changedProperty' => [],
        'newProperty'     => [
          'valid->reqShow'   => true,
          'valid->reqPos'    => 'after',
          'optionCol'        => 1,
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'check',
          'phHide'           => true,
          'adminLblHide'     => true,
        ],
        'removeProperty' => ['round']
      ],
      'radio' => [
        'unchangeProperty'=> ['typ', 'lbl', 'opt', 'valid', 'err', 'adminLbl', 'customType'],
        'changedProperty' => [],
        'newProperty'     => [
          'valid->reqShow'   => true,
          'valid->reqPos'    => 'after',
          'optionCol'        => 1,
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'radio',
          'phHide'           => true,
          'adminLblHide'     => true,
          'round'            => true,
        ],
        'removeProperty' => []
      ],
      'email' => [
        'unchangeProperty'=> ['typ', 'lbl', 'valid', 'err', 'adminLbl', 'pattern', 'ph', 'ac'],
        'changedProperty' => [],
        'newProperty'     => [
          'valid->reqShow'   => true,
          'valid->reqPos'    => 'after',
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'email',
          'phHide'           => true,
          'adminLblHide'     => true,
          'acHide'           => true,
        ],
        'removeProperty' => []
      ],
      'password' => [
        'unchangeProperty'=> ['typ', 'lbl', 'valid', 'err', 'adminLbl', 'ph'],
        'changedProperty' => [],
        'newProperty'     => [
          'valid->reqShow'   => true,
          'valid->reqPos'    => 'after',
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'password',
          'phHide'           => true,
          'adminLblHide'     => true,
        ],
        'removeProperty' => ['ac']
      ],
      'number' => [
        'unchangeProperty'=> ['typ', 'lbl', 'valid', 'err', 'adminLbl', 'ph', 'ac', 'mn', 'mx'],
        'changedProperty' => [],
        'newProperty'     => [
          'valid->reqShow'   => true,
          'valid->reqPos'    => 'after',
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'number',
          'phHide'           => true,
          'adminLblHide'     => true,
        ],
        'removeProperty' => []
      ],
      'url' => [
        'unchangeProperty'=> ['typ', 'attr', 'lbl', 'valid', 'err', 'adminLbl', 'ph', 'ac'],
        'changedProperty' => [],
        'newProperty'     => [
          'valid->reqShow'   => true,
          'valid->reqPos'    => 'after',
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'url',
          'phHide'           => true,
          'adminLblHide'     => true,
        ],
        'removeProperty' => []
      ],
      'file-up' => [
        'unchangeProperty'=> ['typ', 'lbl', 'valid', 'err', 'adminLbl'],
        'changedProperty' => [
          'upBtnTxt' => [['path' => 'btnTxt']],
          'mul'      => [['path' => 'config->multiple']],
          'mxUp'     => [['path' => 'config->maxSize'],
            ['path' => 'config->allowMaxSize', 'isEqualTo' => null, 'val' => false],
            ['path' => 'config->showMaxSize', 'isEqualTo' => null, 'val' => false]],
          'unit'     => [['path' => 'config->sizeUnit']],
          'exts'     => [['path' => 'config->allowedFileType']],
        ],
        'newProperty' => [
          'valid->reqShow'   => true,
          'valid->reqPos'    => 'after',
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'file-up',
          'phHide'           => true,
          'adminLblHide'     => true,
          'prefixIcn'        => str_replace('/v1', '', BITFORMS_ASSET_URI) . '/../static/file-upload/paperclip.svg',
          'btnTxt'           => 'Attach File',
          'config'           => [
            'multiple'        => false,
            'allowMaxSize'    => true,
            'showMaxSize'     => true,
            'maxSize'         => 2,
            'sizeUnit'        => 'MB',
            'isItTotalMax'    => false,
            'showSelectStatus'=> true,
            'fileSelectStatus'=> 'No File Selected',
            'allowedFileType' => '',
            'showFileList'    => true,
            'fileExistMsg'    => 'A file allready exist',
            'showFilePreview' => true,
            'showFileSize'    => true,
            'duplicateAllow'  => false,
            'accept'          => '',
            'minFile'         => 0,
            'maxFile'         => 0
          ]
        ],
        'removeProperty' => ['mul', 'mxUp', 'unit', 'exts', 'upBtnTxt']
      ],
      'date' => [
        'unchangeProperty'=> ['typ', 'lbl', 'valid', 'err', 'adminLbl'],
        'changedProperty' => [],
        'newProperty'     => [
          'valid->reqShow'   => true,
          'valid->reqPos'    => 'after',
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'date',
          'adminLblHide'     => true,
        ],
        'removeProperty' => []
      ],
      'time' => [
        'unchangeProperty'=> ['typ', 'lbl', 'valid', 'err', 'adminLbl'],
        'changedProperty' => [],
        'newProperty'     => [
          'valid->reqShow'   => true,
          'valid->reqPos'    => 'after',
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'time',
          'adminLblHide'     => true,
        ],
        'removeProperty' => []
      ],
      'month' => [
        'unchangeProperty'=> ['typ', 'lbl', 'valid', 'err', 'adminLbl'],
        'changedProperty' => [],
        'newProperty'     => [
          'valid->reqShow'   => true,
          'valid->reqPos'    => 'after',
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'month',
          'adminLblHide'     => true,
        ],
        'removeProperty' => []
      ],
      'week' => [
        'unchangeProperty'=> ['typ', 'lbl', 'valid', 'err', 'adminLbl'],
        'changedProperty' => [],
        'newProperty'     => [
          'valid->reqShow'   => true,
          'valid->reqPos'    => 'after',
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'week',
          'adminLblHide'     => true,
        ],
        'removeProperty' => []
      ],
      'datetime-local' => [
        'unchangeProperty'=> ['typ', 'lbl', 'valid', 'err', 'adminLbl'],
        'changedProperty' => [],
        'newProperty'     => [
          'valid->reqShow'   => true,
          'valid->reqPos'    => 'after',
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'datetime-local',
          'adminLblHide'     => true,
        ],
        'removeProperty' => []
      ],
      'color' => [
        'unchangeProperty'=> ['typ', 'lbl', 'valid', 'err', 'adminLbl'],
        'changedProperty' => [],
        'newProperty'     => [
          'valid->reqShow'   => true,
          'valid->reqPos'    => 'after',
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'color',
          'adminLblHide'     => true,
        ],
        'removeProperty' => []
      ],
      'decision-box' => [
        'unchangeProperty'=> ['typ', 'lbl', 'msg', 'valid', 'err', 'adminLbl'],
        'changedProperty' => [],
        'newProperty'     => [
          'valid->reqShow'   => true,
          'valid->reqPos'    => 'after',
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'decision-box',
          'adminLblHide'     => true,
        ],
        'removeProperty' => []
      ],
      'select' => [
        'unchangeProperty'=> ['typ', 'lbl', 'ph', 'mul', 'valid', 'err', 'adminLbl', 'val', 'mn', 'mx'],
        'changedProperty' => [
          'mul'       => [
            ['path' => 'config->multipleSelect'],
            ['path' => 'config->showChip', 'isEqualTo' => false, 'val' => false],
            ['path' => 'config->closeOnSelect', 'isEqualTo' => false, 'val' => true]
          ],
          'opt'       => [['path' => 'optionsList->0->List-1']],
          'customOpt' => [['path' => 'config->allowCustomOption']],
          'ph'        => [['path' => 'ph', 'val' => 'Select...']],
          'customType'=> [['path' => 'customTypeList->0']],
        ],
        'newProperty' => [
          'valid->reqShow'   => true,
          'valid->reqPos'    => 'after',
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'select',
          'phHide'           => true,
          'adminLblHide'     => true,
          'optionsList'      => [
            ['List-1' => []]
          ],
          'config' => [
            'selectedOptImage'    => false,
            'selectedOptClearable'=> true,
            'searchClearable'     => true,
            'optionIcon'          => false,
            'placeholder'         => 'Select an option',
            'showSearchPh'        => true,
            'searchPlaceholder'   => 'Search options..',
            'maxHeight'           => 400,
            'multipleSelect'      => true,
            'showChip'            => true,
            'selectedOptImgSrc'   => 'test.png',
            'closeOnSelect'       => false,
            'activeList'          => 0,
            'allowCustomOption'   => false
          ]
        ],
        'removeProperty' => ['mul']
      ],
      'country' => [
        'unchangeProperty'=> ['lbl', 'ph', 'mul', 'valid', 'err', 'adminLbl', 'val', 'customOpt', 'mn', 'mx'],
        'changedProperty' => [
          'opt' => [['path' => 'options']]
        ],
        'newProperty' => [
          'typ'              => 'country',
          'valid->reqShow'   => true,
          'valid->reqPos'    => 'after',
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'country',
          'phHide'           => true,
          'adminLblHide'     => true,
          'config'           => [
            'selectedFlagImage'       => true,
            'selectedCountryClearable'=> true,
            'searchClearable'         => true,
            'optionFlagImage'         => true,
            'detectCountryByIp'       => false,
            'detectCountryByGeo'      => false,
            'defaultValue'            => '',
            'showSearchPh'            => true,
            'searchPlaceholder'       => 'Search for countries',
            'noCountryFoundText'      => 'No Country Found'
          ]
        ],
        'removeProperty' => ['mul']
      ],
      'recaptcha' => [
        'unchangeProperty'=> ['typ', 'lbl', 'valid'],
        'changedProperty' => [
          'theme' => [['path' => 'config->theme']]
        ],
        'newProperty' => [
          'fieldName' => 'recaptcha',
          'config'    => [
            'theme' => 'light',
            'size'  => 'normal',
          ],
        ],
        'removeProperty' => ['theme']
      ],
      'html' => [
        'unchangeProperty'=> ['typ', 'valid', 'content'],
        'changedProperty' => [],
        'newProperty'     => [
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'html',
          'adminLbl'         => 'HTML',
          'adminLblHide'     => true,
        ],
        'removeProperty' => ['lbl']
      ],
      'paypal' => [
        'unchangeProperty'=> [
          'typ',
          'valid',
          'payIntegID',
          'currency',
          'locale',
          'disableFunding',
          'amount',
          'amountType',
          'amountFld',
          'shipping',
          'shippingType',
          'shippingFld',
          'tax',
          'taxType',
          'taxFld',
          'payType',
          'planId'],
        'changedProperty' => [],
        'newProperty'     => [
          'fieldName'   => 'paypal',
          'adminLbl'    => 'Paypal',
          'adminLblHide'=> true,
          'style'       => [
            'layout'=> 'vertical',
            'color' => 'gold',
            'shape' => 'rect',
            'label' => 'paypal',
            'height'=> '55'
          ]
        ],
        'removeProperty' => []
      ],
      'razorpay' => [
        'unchangeProperty'=> [
          'typ',
          'lbl',
          'valid',
          'btnTxt',
          'btnSiz',
          'align',
          'fulW',
          'options'],
        'changedProperty' => [
          'options->payIntegID' => [['path' => 'payIntegID']]
        ],
        'newProperty' => [
          'customClasses'    => (object)[],
          'customAttributes' => (object)[],
          'fieldName'        => 'razorpay',
        ],
        'removeProperty' => ['options->payIntegID']
      ],
    ];

    return $formate[$fieldType];
  }
}
