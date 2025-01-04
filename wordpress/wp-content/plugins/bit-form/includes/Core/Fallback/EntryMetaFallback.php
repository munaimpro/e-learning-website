<?php

namespace BitCode\BitForm\Core\Fallback;

class EntryMetaFallback
{
  public function removeAllDuplicateMetaKeysExceptLast()
  {
    global $wpdb;
    $tablename = $wpdb->prefix . 'bitforms_form_entrymeta';
    $sql = $wpdb->query("DELETE a FROM $tablename AS a
    JOIN (
        SELECT bitforms_form_entry_id, meta_key, MAX(meta_id) AS max_id
        FROM $tablename
        GROUP BY bitforms_form_entry_id, meta_key
    ) AS b 
    ON a.bitforms_form_entry_id = b.bitforms_form_entry_id AND a.meta_key = b.meta_key
    WHERE a.meta_id != b.max_id;
    ");
  }
}
