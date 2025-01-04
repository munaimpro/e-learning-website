<?php

/**
 * Provides Base Model Class
 */

namespace BitCode\BitForm\Core\Database;

/**
 * Undocumented class
 */

class FormModel extends Model
{
  protected static $table = 'bitforms_form';

  /*  public function getNewFormId()
  {
      $table = $this->app_db->prefix . 'bitforms_form';
      $this->app_db->query("SELECT id FROM $table ORDER BY id DESC LIMIT 1");
      return $this->app_db->last_result;
  } */
}
