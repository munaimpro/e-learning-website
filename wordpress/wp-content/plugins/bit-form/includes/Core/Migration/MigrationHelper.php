<?php

namespace BitCode\BitForm\Core\Migration;

class MigrationHelper
{
  public static function setNestedProperty(&$rootObj, $properties, $value)
  {
    $firstProperty = array_shift($properties);
    $current = &$rootObj;

    if (is_object($current)) {
      if (!property_exists($current, $firstProperty)) {
        $current->$firstProperty = (object)[];
      }
      if (count($properties) > 0) {
        self::setNestedProperty($current->$firstProperty, $properties, $value);
      } else {
        $current->$firstProperty = $value;
      }
    } elseif (is_array($current)) {
      if (!array_key_exists($firstProperty, $current)) {
        $current[$firstProperty] = [];
      }
      if (count($properties) > 0) {
        self::setNestedProperty($current[$firstProperty], $properties, $value);
      } else {
        $current[$firstProperty] = $value;
      }
    }
  }

  public static function getNestedPropertyValue($rootObj, $properties)
  {
    $firstProperty = array_shift($properties);
    $current = $rootObj;
    if (is_object($current) && count($properties) > 0) {
      return self::getNestedPropertyValue($current->$firstProperty, $properties);
    } elseif (is_object($current)) {
      return property_exists($current, $firstProperty) ? $current->$firstProperty : null;
    } elseif (is_array($current) && count($properties) > 0) {
      return self::getNestedPropertyValue($current[$firstProperty], $properties);
    } elseif (is_array($current)) {
      return array_key_exists($firstProperty, $current) ? $current[$firstProperty] : null;
    }
  }

  public static function optionListConvert($optionList)
  {
    $newOptionList = [];
    foreach ($optionList as $option) {
      $newOpt = (object)[];
      if (isset($option->label)) {
        $newOpt->lbl = $option->label;
      }
      if (isset($option->value)) {
        $newOpt->val = $option->value;
      }
      if (isset($option->code)) {
        $newOpt->i = $option->code;
      }
      if (isset($option->prefix_img)) {
        $newOpt->img = str_replace('/v1', '', BITFORMS_ASSET_URI . $option->prefix_img);
      }
      $newOptionList[] = $newOpt;
    }
    return $newOptionList;
  }

  public static function convertFields($fieldsArray)
  {
    $conversionFormate = new FieldsConversionFormate();
    $newFieldsArray = [];

    foreach ($fieldsArray as $fieldKey => $field) {
      // print_r(gettype($field));
      $convertFormate = $conversionFormate->getFormate($field->typ);
      $newFieldProperty = [];

      // convert unchange property
      foreach ($convertFormate['unchangeProperty'] as $property) {
        if (property_exists($field, $property)) {
          $newFieldProperty[$property] = $field->{$property};
        }
      }

      // add new property
      foreach ($convertFormate['newProperty'] as $property => $value) {
        $properties = explode('->', $property);
        self::setNestedProperty($newFieldProperty, $properties, $value);
      }

      // modify changed property
      foreach ($convertFormate['changedProperty'] as $oldProperty => $newPropertyArr) {
        $properties = explode('->', $oldProperty);
        $value = self::getNestedPropertyValue($field, $properties);
        foreach ($newPropertyArr as $newProperty) {
          if (is_null($value) && isset($newProperty['val'])) {
            $value = $newProperty['val'];
          }
          if (isset($newProperty['isEqualTo']) && $value === $newProperty['isEqualTo']) {
            $value = $newProperty['val'];
          } elseif (isset($newProperty['isEqualTo'])) {
            continue;
          }
          if (!is_null($value)) {
            $properties = explode('->', $newProperty['path']);
            self::setNestedProperty($newFieldProperty, $properties, $value);
          }
        }
      }
      $newFieldProperty['fieldName'] .= "-$fieldKey";

      if ('select' === $newFieldProperty['typ']) {
        $newFieldProperty['optionsList'][0]['List-1'] = self::optionListConvert($field->opt);
        if (isset($newFieldProperty['val']) && is_array($newFieldProperty['val'])) {
          $newFieldProperty['val'] = implode(BITFORMS_BF_SEPARATOR, $newFieldProperty['val']);
        }
        if (isset($field->opt[0]->prefix_img) && !is_null($field->opt[0]->prefix_img)) {
          $newFieldProperty['config']['optionIcon'] = true;
        }
      }
      $newFieldsArray[$fieldKey] = $newFieldProperty;
    }

    return $newFieldsArray;
  }

  public static function convertLayout($layoutObj)
  {
    $newLayoutArray = [];

    foreach ($layoutObj as $breakdownSize => $layoutArr) {
      // v1 width variation
      $v1WidthVariation = [
        'lg' => 6,
        'md' => 4,
        'sm' => 2,
      ];
      $v2Width = 60;
      $v2RowHeight = 43;

      $newLayoutArray[$breakdownSize] = [];
      foreach ($layoutArr as $index => $fieldLayout) {
        $fieldLayout->w = $fieldLayout->w * ($v2Width / $v1WidthVariation[$breakdownSize]);
        $fieldLayout->x = $fieldLayout->x * ($v2Width / $v1WidthVariation[$breakdownSize]);
        $fieldLayout->h = $fieldLayout->h * $v2RowHeight;
        $fieldLayout->y = $fieldLayout->y * $v2RowHeight;
        unset($fieldLayout->minH, $fieldLayout->maxH);
        $newLayoutArray[$breakdownSize][] = $fieldLayout;
      }
    }
    return $newLayoutArray;
  }

  public static function convertSuccessMessage($successMessageArr)
  {
    $newSuccessMessageArr = [];

    // this config is set as snackbar in bottom right corner
    $defaultMessageConfig = [
      'msgType'  => 'snackbar',
      'position' => 'bottom-right',
      'animation'=> 'slide-up',
      'autoHide' => true,
      'duration' => 3,
      'styles'   => [
        'width'       => '300px',
        'padding'     => '10px 35px 10px 20px',
        'background'  => '#383838',
        'color'       => '#fff',
        'borderWidth' => '1px',
        'borderType'  => 'none',
        'borderColor' => 'var(--bg-5)',
        'borderRadius'=> 'var(--g-bdr-rad)',
        'boxShadow'   => [
          [
            'x'     => '0px',
            'y'     => '27px',
            'blur'  => '30px',
            'spread'=> '',
            'color' => 'rgb(0 0 0 / 18%)',
            'inset' => ''
          ]
        ],
        'closeBackground'=> '#666',
        'closeHover'     => 'var(--bg-10)',
        'closeIconColor' => '#fff',
        'closeIconHover' => 'var(--bg-50)'
      ]
    ];
    foreach ($successMessageArr as $message) {
      $message->config = $defaultMessageConfig;
      $newSuccessMessageArr[] = $message;
    }
    return $newSuccessMessageArr;
  }

  public static function convertWebHooksToIntegrations($formSettingsV1)
  {
    $webHooks = $formSettingsV1->confirmation->type->webHooks;
    $integrations = $formSettingsV1->integrations;
    foreach ($webHooks as $webHook) {
      $webHook->type = 'Web Hooks';
      $webHook->name = $webHook->title;
      $integrations[] = $webHook;
    }
    return $integrations;
  }

  public static function duplicateV1StyleFiles()
  {
    $source = BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-styles';
    $destination = BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-styles-v1';
    if (!file_exists(BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-styles-v1')) {
      wp_mkdir_p(BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-styles-v1');
    }
    self::recursiveCopyFiles($source, $destination);
    self::recursiveDeleteFiles($source);
  }

  public static function getBitformDefaultStyleClass()
  {
    return self::arrayToCssString(BitformDefaultStyle::commonStyleClasses());
  }

  public static function recursiveDeleteFiles($src)
  {
    if (is_file($src)) {
      return wp_delete_file($src);
    } elseif (is_dir($src)) {
      $scan = glob(rtrim($src, '/') . '/*');
      foreach ($scan as $path) {
        self::recursiveDeleteFiles($path);
      }
    }
  }

  public static function recursiveCopyFiles($src, $dst)
  {
    if (is_file($src) && !is_dir($dst)) {
      return copy($src, $dst);
    } elseif (is_dir($src)) {
      $scan = glob(rtrim($src, '/') . '/*');
      foreach ($scan as $path) {
        self::recursiveCopyFiles($path, $dst . '/' . basename($path));
      }
    }
  }

  public static function arrayToCssString($array)
  {
    $cssString = '';

    foreach ($array as $selector => $properties) {
      $cssString .= $selector . ' {';

      foreach ($properties as $property => $value) {
        $cssString .= $property . ': ' . $value . '; ';
      }

      // Remove the trailing space and add closing brace
      $cssString = rtrim($cssString, ' ') . '}';
    }

    return $cssString;
  }

  public static function formatFormData($formData)
  {
    $newFormData = json_decode(wp_json_encode($formData));

    $formContent = json_decode(wp_json_encode($formData->form_content));

    $newFormData->fields = $formContent->fields;
    $newFormData->layout = $formContent->layout;
    $newFormData->nestedLayouts = $formContent->nestedLayout;
    $newFormData->formInfo = $formContent->formInfo;

    if (is_array($formData->reports) && count($formData->reports) > 0) {
      $newFormData->currentReport = $formData->reports[0];
      $newFormData->report_id = $formData->reports[0]->id;
    }

    return $newFormData;
  }
}
