<?php

/**
 * Plugin Name: Bit Form
 * Plugin URI:  https://www.bitapps.pro/bit-form
 * Description: Contact Form Builder Plugin: Multi Step Contact Form, Payment Form, Custom Contact Form Plugin by Bit Form
 * Version:     2.17.4
 * Author:      Contact Form Builder - Bit Form
 * Author URI:  https://www.bitapps.pro
 * Text Domain: bit-form
 * Requires PHP: 7.4
 * Domain Path: /languages
 * License: GPLv2 or later
 */

/***
 * If try to direct access  plugin folder it will Exit
 **/
if (!defined('ABSPATH')) {
  exit;
}

// Define most essential constants.
define('BITFORMS_VERSION', '2.17.4');
define('BITFORMS_PLUGIN_MAIN_FILE', __FILE__);

global $bitforms_db_version;
$bitforms_db_version = '2.3';
define('BITFORMS_DB_VERSION', $bitforms_db_version);
define('BITFORMS_REQUIRED_WP_VERSION', '5.1');
define('BITFORMS_REQUIRED_PHP_VERSION', '7.4');
define('BITFORMS_API_VERSION', '1.0');

if (version_compare(PHP_VERSION, '5.6.0', '>=')) {
  require_once plugin_dir_path(__FILE__) . 'includes/loader.php';
}

/**
 * Handles plugin activation.
 *
 * Throws an error if the plugin is activated on an older version than PHP 5.6.
 */
function bitforms_activate_plugin($network_wide)
{
  if (version_compare(PHP_VERSION, '5.6.0', '<')) {
    wp_die(
      esc_html_e('bitforms requires PHP version 5.6.', 'bit-form'),
      esc_html_e('Error Activating', 'bit-form')
    );
  }

  do_action('bitforms_activation', $network_wide);
}

register_activation_hook(__FILE__, 'bitforms_activate_plugin');

/**
 * Handles plugin deactivation.
 */
function bitforms_deactivate_plugin($network_wide)
{
  if (version_compare(PHP_VERSION, '5.6.0', '<')) {
    return;
  }

  do_action('bitforms_deactivation', $network_wide);
}

register_deactivation_hook(__FILE__, 'bitforms_deactivate_plugin');

/**
 * Handles plugin uninstall.
 *
 * @access private
 */
function bitforms_uninstall_plugin()
{
  if (version_compare(PHP_VERSION, '5.6.0', '<')) {
    return;
  }

  do_action('bitforms_uninstall');
}
register_uninstall_hook(__FILE__, 'bitforms_uninstall_plugin');
