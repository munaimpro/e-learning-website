<?php

/**
 * Provides Base Model Class
 */

namespace BitCode\BitForm\Core\Database;

/**
 * Undocumented class
 */

use WP_Error;

class FormEntryModel extends Model
{
  protected static $table = 'bitforms_form_entries';

  public function bulkDelete(array $condition = null)
  {
    if (
      !\is_null($condition)
      && \is_array($condition)
      && array_keys($condition) !== range(0, count($condition) - 1)
    ) {
      $delete_condition = $condition;
    } else {
      return new WP_Error(
        'deletion_error',
        __('At least 1 condition needed', 'bit-form')
      );
    }
    $formatted_conditions = $this->getFormatedCondition($delete_condition);
    if ($formatted_conditions) {
      $condition_to_check = $formatted_conditions['conditions'];
      $all_values = $formatted_conditions['values'];
    } else {
      $condition_to_check = null;
      return new WP_Error(
        'deletion_error',
        __('At least 1 condition needed', 'bit-form')
      );
    }
    $prefix = $this->app_db->prefix;
    $sql = "DELETE `{$prefix}bitforms_form_entries`,`{$prefix}bitforms_form_entrymeta` FROM "
        . "`{$prefix}bitforms_form_entries` "
        . " INNER JOIN `{$prefix}bitforms_form_entrymeta` ON "
        . " `{$prefix}bitforms_form_entries`.`id` "
        . "= `{$prefix}bitforms_form_entrymeta`.`bitforms_form_entry_id` "
        . " $condition_to_check";
    return $this->execute($sql, $all_values)->getResult();
  }
}
