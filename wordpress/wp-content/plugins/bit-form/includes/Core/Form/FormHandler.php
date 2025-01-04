<?php

namespace BitCode\BitForm\Core\Form;

use BitCode\BitForm\Admin\Form\AdminFormHandler;
use BitCode\BitForm\Core\Capability\Request;
use  BitCode\BitForm\Frontend\Form\FrontendFormHandler;

final class FormHandler
{
  private $_container = [];
  private static $_instance = null;

  /**
   * Undocumented function
   */
  public static function getInstance()
  {
    if (null === self::$_instance) {
      self::$_instance = new FormHandler();
    }
    return self::$_instance;
  }

  public function __construct()
  {
    if (Request::Check('frontend')) {
      $this->load_public_form_handler();
    }
    if (Request::Check('admin')) {
      $this->load_admin_form_handler();
    }
  }

  /**
   * Magic getter function
   *
   * @param $object
   *
   * @return mixed
   */
  public function __get($object)
  {
    if (array_key_exists($object, $this->_container)) {
      return $this->_container[$object];
    }

    return $this->{$object};
  }

  /**
   * Magic isset function
   *
   * @param $object
   *
   * @return mixed
   */
  public function __isset($object)
  {
    return isset($this->{$object}) || isset($this->container[$object]);
  }

  public function load_admin_form_handler()
  {
    $this->_container['admin'] = new AdminFormHandler();
  }

  protected function load_public_form_handler()
  {
    $this->_container['frontend'] = new FrontendFormHandler();
    if (class_exists('BitCode\BitFormPro\Frontend\PaymentHandler')) {
      $this->_container['frontend'] = new \BitCode\BitFormPro\Frontend\PaymentHandler();
    }
  }
}
