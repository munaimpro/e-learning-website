<?php
/**
 * Get set Form,fields
 */

namespace BitCode\BitForm\Admin\Form;

/**
 * FrontendFormManager class
 */
use BitCode\BitForm\Core\Form\FormManager;

final class AdminFormManager extends FormManager
{
  public function __construct($form_id)
  {
    parent::__construct($form_id);
  }

  public function getFormMetaData()
  {
    return [
      'created_at' => static::$form[0]->created_at,
      'views'      => static::$form[0]->views,
      'entries'    => static::$form[0]->entries,
      'status'     => static::$form[0]->status
    ];
  }
}
