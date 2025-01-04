<?php

namespace BitCode\BitForm\Core\Util;

/**
 * Class handling plugin uninstallation.
 *
 * @since 1.0.0
 * @access private
 * @ignore
 */
final class Uninstallation
{
  /**
   * Registers functionality through WordPress hooks.
   *
   * @since 1.0.0
   */
  public function register()
  {
    add_action('bitforms_uninstall', [$this, 'uninstall']);
  }

  public function uninstall()
  {
    flush_rewrite_rules();
    $routes = get_option('bitforms_routes');
    if ($routes && isset($routes['root'])) {
      $this->deletePosts($routes['root']);
      $this->deletePosts($routes['file']);
      $this->deleteOptions('bitforms_routes');
    }
    global $wpdb;
    $data = get_option('bitform_app_config');
    if ($data && isset($data->delete_table) && $data->delete_table) {
      $tableArray = [
        $wpdb->prefix . 'bitforms_email_template',
        $wpdb->prefix . 'bitforms_form',
        $wpdb->prefix . 'bitforms_form_entries',
        $wpdb->prefix . 'bitforms_form_entrymeta',
        $wpdb->prefix . 'bitforms_form_entry_log',
        $wpdb->prefix . 'bitforms_form_log_details',
        $wpdb->prefix . 'bitforms_integration',
        $wpdb->prefix . 'bitforms_reports',
        $wpdb->prefix . 'bitforms_success_messages',
        $wpdb->prefix . 'bitforms_workflows',
        $wpdb->prefix . 'bitforms_form_entry_relatedinfo',
        $wpdb->prefix . 'bitforms_form_v1',
        $wpdb->prefix . 'bitforms_success_messages_v1',
        $wpdb->prefix . 'bitforms_workflows_v1',
      ];

      foreach ($tableArray as $tablename) {
        // $wpdb->query("DROP TABLE IF EXISTS $tablename");
        $wpdb->query(
          $wpdb->prepare(
            "DROP TABLE IF EXISTS $tablename"
          )
        );
      }

      $this->deleteOptions('bitform_app_config');
    }
    $this->deleteOptions('bitforms_routes');
    $this->deleteOptions('bitforms_db_version');
    $this->deleteOptions('bitforms_installed');
    $this->deleteOptions('bitforms_version');
    $this->deleteOptions('bitforms_routes');
    $this->deleteOptions('bitform_secret_api_key');
    $this->deleteOptions('bitforms_migrated_to_v2');
    $this->deleteOptions('bitforms_migrating_to_v2');
    $this->deleteOptions('bitforms_changelog_version');
    $this->deleteOptions('bitforms_hide_announcement');
    $this->deleteOptions('bitforms_hide_cashback');
  }

  private function deletePosts($id)
  {
    wp_delete_post($id);
  }

  private function deleteOptions($option)
  {
    delete_option($option);
  }
}
