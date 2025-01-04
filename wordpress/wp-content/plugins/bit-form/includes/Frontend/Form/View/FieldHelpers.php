<?php

namespace BitCode\BitForm\Frontend\Form\View;

use BitCode\BitForm\Core\Util\FieldValueHandler;
use BitCode\BitForm\Core\Util\FrontendHelpers;

class FieldHelpers
{
  private $_fld;
  private $_fk;
  private $_form_atomic_Cls_map;

  public function __construct($field, $fk, $form_atomic_Cls_map = null)
  {
    $this->_fld = $field;
    $this->_fk = $fk;
    $this->_form_atomic_Cls_map = $form_atomic_Cls_map;
  }

  public function getCustomAttributes($element)
  {
    if ($this->property_exists_nested($this->_fld, "customAttributes->{$element}")) {
      $attrString = '';
      $customAttributs = $this->_fld->customAttributes->{$element};

      foreach ($customAttributs as $attr) {
        $attrString .= " {$this->esc_attr($attr->key)}='{$this->esc_attr($attr->value)}'";
      }
      return $attrString;
    }
    return '';
  }

  public function getCustomClasses($element)
  {
    if ($this->property_exists_nested($this->_fld, "customClasses->{$element}")) {
      return $this->esc_attr($this->_fld->customClasses->{$element});
    }
    return '';
  }

  public function renderHTMR($title)
  {
    $allowed_html = wp_kses_allowed_html('post');

    $allowed_html['iframe'] = [
      'src'             => true,
      'width'           => true,
      'height'          => true,
      'frameborder'     => true,
      'allow'           => true,
      'allowfullscreen' => true,
      'title'           => true,
      'style'           => true,
    ];

    $allowed_html['style'] = true;
    return wp_kses($title, $allowed_html);
  }

  public function getAtomicCls($element)
  {
    if (empty($this->_fld) && empty($this->_fk)) {
      if (property_exists($this->_form_atomic_Cls_map, ".$element")) {
        $getAtomicCls = $this->_form_atomic_Cls_map->{".$element"};
        return implode(' ', $getAtomicCls) . " $element";
      }
      return $element;
    }
    if ('advanced-file-up' === $this->_fld->typ && isset($this->_form_atomic_Cls_map->{".$element"})) {
      $getAtomicCls = $this->_form_atomic_Cls_map->{".$element"};
      return implode(' ', $getAtomicCls) . " {$this->_fk}-{$element}";
    }
    $cls = ".$this->_fk-$element";
    if (isset($this->_form_atomic_Cls_map->{$cls})) {
      $getAtomicCls = $this->_form_atomic_Cls_map->{$cls};
      return implode(' ', $getAtomicCls) . " {$this->_fk}-{$element} bf-{$element}";
    }
    return "{$this->_fk}-{$element} bf-{$element}";
  }

  public function replaceToBackSlash($str)
  {
    $phReplaceToBackslash = str_replace('$_bf_$', '\\', $str); // Replace React inputs value "$_bf_$" to '\\'
    return FieldValueHandler::replaceSmartTagWithValue($phReplaceToBackslash);
  }

  /**
   * @param $element @exmp $element = 'fld-wrp'
   */
  public function icon($icnPropName, $element)
  {
    if ($this->property_exists_nested($this->_fld, $icnPropName, '', 1)) {
      return <<<ICON
<img
  {$this->getCustomAttributes($element)}
  class="{$this->getAtomicCls($element)} {$this->getCustomClasses($element)}"
  src="{$this->esc_url($this->_fld->{$icnPropName})}"
  alt=""
/>
ICON;
    }
    return '';
  }

  public function property_exists_nested($obj, $path = '', $valToCheck = null, $checkNegativeVal = 0)
  {
    $path = explode('->', $path);
    $current = $obj;
    foreach ($path as $key) {
      if (is_object($current)) {
        if (property_exists($current, $key)) {
          $current = $current->{$key};
        } else {
          return false;
        }
      } else {
        break;
      }
    }
    if (isset($valToCheck)) {
      if ($checkNegativeVal) {
        return $current !== $valToCheck;
      }
      return $current === $valToCheck;
    }
    return true;
  }

  public function required()
  {
    if ($this->property_exists_nested($this->_fld, 'valid->req', true) && $this->property_exists_nested($this->_fld, 'err->req->show', true)) {
      return 'required';
    }

    return '';
  }

  public function disabled()
  {
    if ($this->property_exists_nested($this->_fld, 'valid->disabled', true)) {
      return 'disabled';
    }
    return '';
  }

  public function readonly()
  {
    if ($this->property_exists_nested($this->_fld, 'readonly', true)) {
      return 'readonly';
    }
    if ($this->property_exists_nested($this->_fld, 'valid->readonly', true)) {
      return 'readonly';
    }
    return '';
  }

  public function placeholder()
  {
    if ($this->property_exists_nested($this->_fld, 'ph', '', 1)) {
      return "placeholder='{$this->esc_attr($this->replaceToBackSlash($this->_fld->ph))}'";
    }
    return '';
  }

  /**
   * @param $str
   * for attribute value
   * @return string
   */
  public function esc_attr($str)
  {
    return esc_attr($str);
  }

  /**
   * @param $str
   * for html content that don't need any html markup
   * @return string
   */
  public function esc_html($str)
  {
    return esc_html($str);
  }

  /**
   * @param $str
   * for textarea content
   * @return string
   */
  public function esc_textarea($str)
  {
    return esc_textarea($str);
  }

  /**
   * @param $str
   * for url
   * @return string
   */
  public function esc_url($str)
  {
    return esc_url($str);
  }

  /**
   * @param $str
   * for content that needs html markup but escapes all scripts
   * @return string
   */
  public function kses_post($str)
  {
    return wp_kses_post($str);
  }

  public function name()
  {
    if ($this->property_exists_nested($this->_fld, 'fieldName', '', 1)) {
      if ($this->property_exists_nested($this->_fld, 'typ', 'check')) {
        return "name='{$this->esc_attr($this->_fld->fieldName)}[]'";
      }
      if ($this->property_exists_nested($this->_fld, 'typ', 'file-up') && $this->property_exists_nested($this->_fld, 'config->multiple', true)) {
        return "name='{$this->esc_attr($this->_fld->fieldName)}[]'";
      }
      if ($this->property_exists_nested($this->_fld, 'typ', 'image-select')
      && $this->property_exists_nested($this->_fld, 'inpType', 'checkbox')) {
        return "name='{$this->esc_attr($this->_fld->fieldName)}[]'";
      }
      return "name='{$this->esc_attr($this->_fld->fieldName)}'";
    }
    return '';
  }

  public function value()
  {
    $val = '';
    if ($this->property_exists_nested($this->_fld, 'val', '', 1)) {
      $val = $this->_fld->val;
    } elseif ($this->property_exists_nested($this->_fld, 'defaultValue', '', 1)) {
      $val = $this->_fld->defaultValue;
    }
    if (isset($val) && '' !== $val && 'textarea' !== $this->_fld->typ) {
      return "value='{$this->esc_attr($val)}'";
    }
    return $val;
  }

  public function autoComplete()
  {
    if ($this->property_exists_nested($this->_fld, 'ac', '', 1)) {
      return "autocomplete='{$this->esc_attr($this->_fld->ac)}'";
    }
    return '';
  }

  // getter & setter function for $_fk
  public function getFk()
  {
    return $this->_fk;
  }

  public function setFk($fk)
  {
    $this->_fk = $fk;
  }

  public function getFieldKeyWithContentCount()
  {
    $bfFrontendFormIds = FrontendHelpers::$bfFrontendFormIds;
    $contentCount = count($bfFrontendFormIds);
    return "{$this->_fk}-{$contentCount}";
  }
}
