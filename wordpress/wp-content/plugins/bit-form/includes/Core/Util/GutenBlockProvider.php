<?php

namespace BitCode\BitForm\Core\Util;

use BitCode\BitForm\Core\Form\FormHandler;

final class GutenBlockProvider
{
  public function register()
  {
    if (!function_exists('register_block_type')) {
      return;
    }

    add_action('enqueue_block_editor_assets', [$this, 'shortcodeBlock']);
  }

  public function shortcodeBlock()
  {
    wp_enqueue_script(
      'bitforms-runtime',
      BITFORMS_ROOT_URI . '/static/bitform-block/runtime.js',
      null,
      BITFORMS_VERSION,
      true
    );
    wp_enqueue_script(
      'bitforms-gutenberg-block',
      BITFORMS_ROOT_URI . '/static/bitform-block/bitforms-shortcode-block.js',
      ['bitforms-runtime', 'wp-blocks', 'wp-i18n', 'wp-components'],
      BITFORMS_VERSION,
      true
    );

    /* wp_register_style(
        'bitforms-gutenberg-block',
        plugins_url('style.css', __FILE__),
        array( ),
        filemtime(plugin_dir_path(__FILE__) . 'style.css')
    ); */
    $formHandler = FormHandler::getInstance();
    $all_forms = $formHandler->admin->getAllForm();
    $bitformsForms = apply_filters(
      'bitforms_localize_block_script',
      [
        'forms' => $all_forms
      ]
    );

    wp_localize_script('bitforms-gutenberg-block', 'bitformsBlock', $bitformsForms);
  }
}
