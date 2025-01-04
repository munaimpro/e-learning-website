<?php

namespace BitCode\BitForm;

if (!\defined('ABSPATH')) {
  exit;
}

final class Config
{
  public const SLUG = 'bit-form';

  public const TITLE = 'Bit Form';

  public const VAR_PREFIX = BITFORMS_PREFIX;

  public const VERSION = BITFORMS_VERSION;

  public const DB_VERSION = BITFORMS_DB_VERSION;

  public const REQUIRED_PHP_VERSION = BITFORMS_REQUIRED_PHP_VERSION;

  public const REQUIRED_WP_VERSION = BITFORMS_REQUIRED_WP_VERSION;

  public const API_VERSION = BITFORMS_API_VERSION;

  public const APP_BASE = BITFORMS_PLUGIN_BASENAME;

  public const DEV_URL = 'http://localhost:3000';

  /**
   * Provides configuration for plugin.
   *
   * @param string $type    Type of conf
   * @param string $default Default value
   *
   * @return array|string|null
   */
  public static function get($type, $default = null)
  {
    switch ($type) {
      case 'MAIN_FILE':
        return realpath(__DIR__ . DIRECTORY_SEPARATOR . self::APP_BASE);

      case 'BASENAME':
        return plugin_basename(trim(self::get('MAIN_FILE')));

      case 'SITE_URL':
        $parsedUrl = wp_parse_url(get_admin_url());
        $siteUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        $siteUrl .= empty($parsedUrl['port']) ? null : ':' . $parsedUrl['port'];

        return $siteUrl;

      case 'ADMIN_URL':
        return str_replace(self::get('SITE_URL'), '', get_admin_url());

      case 'API_URL':
        global $wp_rewrite;

        return [
          'base'      => get_rest_url() . self::SLUG . '/v1',
          'separator' => $wp_rewrite->permalink_structure ? '?' : '&',
        ];

      case 'ROOT_URI':
        return BITFORMS_ROOT_URI;

      case 'ASSET_URI':
        return BITFORMS_ASSET_URI;

      case 'ASSET_JS_URI':
        return BITFORMS_ASSET_JS_URI;

      case 'ASSET_CSS_URI':
        return self::get('ASSET_URI') . '/css';

      case 'PLUGIN_PAGE_LINKS':
        return self::pluginPageLinks();

      case 'SIDE_BAR_MENU':
        // return self::sideBarMenu();

      case 'WP_DB_PREFIX':
        global $wpdb;

        return $wpdb->prefix;

      default:
        return $default;
    }
  }

  /**
   * Prefixed variable name with prefix.
   *
   * @param string $option Variable name
   *
   * @return array
   */
  public static function withPrefix($option)
  {
    return self::VAR_PREFIX . $option;
  }

  /**
   * Retrieves options from option table.
   *
   * @param string $option  Option name
   * @param bool   $default default value
   * @param bool   $wp      Whether option is default wp option
   *
   * @return mixed
   */
  public static function getOption($option, $default = false, $wp = false)
  {
    if ($wp) {
      return get_option($option, $default);
    }

    return get_option(self::withPrefix($option), $default);
  }

  /**
   * Saves option to option table.
   *
   * @param string $option   Option name
   * @param bool   $autoload Whether option will autoload
   * @param mixed  $value
   *
   * @return bool
   */
  public static function addOption($option, $value, $autoload = false)
  {
    return add_option(self::withPrefix($option), $value, '', $autoload ? 'yes' : 'no');
  }

  /**
   * Save or update option to option table.
   *
   * @param string $option   Option name
   * @param mixed  $value    Option value
   * @param bool   $autoload Whether option will autoload
   *
   * @return bool
   */
  public static function updateOption(string $option, mixed $value, bool $autoload = null): bool
  {
    return update_option(self::withPrefix($option), $value, !\is_null($autoload) ? 'yes' : null);
  }

  public static function isDev()
  {
    return \defined('BITAPPS_DEV') && BITAPPS_DEV;
  }

  /**
   * Provides links for plugin pages. Those links will bi displayed in
   * all plugin pages under the plugin name.
   *
   * @return array
   */
  private static function pluginPageLinks()
  {
    return [
      'app-setting' => [
        'title' => __('App Setting', self::SLUG),
        'url'   => self::get('ADMIN_URL') . 'admin.php?page=bitform#/app-settings/recaptcha',
      ],
      'doc-support' => [
        'title' => __('Doc & Support', self::SLUG),
        'url'   => self::get('ADMIN_URL') . 'admin.php?page=bitform#/doc-support',
      ],
    ];
  }
}
