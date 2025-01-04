<?php

namespace BitCode\BitForm\Core\Util;

/**
 * Class handling plugin deactivation.
 *
 * @since 1.0.0
 * @access private
 * @ignore
 */
final class Deactivation
{
  /**
   * Registers functionality through WordPress hooks.
   *
   * @since 1.0.0
   */
  public function register()
  {
    add_action('bitforms_deactivation', [$this, 'deactive']);
  }

  public function deactive()
  {
    $routes = get_option('bitforms_routes');
    if ($routes && isset($routes['root'])) {
      $root_page = ['ID' => $routes['root'], 'post_status' => 'draft'];
      wp_update_post($root_page);
    }
    if ($routes && isset($routes['file'])) {
      $file_page = ['ID' => $routes['file'], 'post_status' => 'draft'];
      wp_update_post($file_page);
    }
    flush_rewrite_rules();
  }
}
