<?php

namespace BitCode\BitForm\Core\Hooks;

class PostType
{
  public static function registerBitformsPostType()
  {
    $args = [
      'label'           => __('Bitform Pages', 'bit-form'),
      'public'          => true,
      'show_ui'         => true,
      'show_in_menu'    => false,
      'capability_type' => 'page',
      'hierarchical'    => false,
      'query_var'       => false,
      'supports'        => ['title'],
      'show_in_rest'    => false,
    ];
    register_post_type('bitforms', $args);
  }

  public static function registerCustomPostType()
  {
    flush_rewrite_rules(false);
    $cpts = get_option('bitform_custom_post_types');
    if (!empty($cpts)) {
      foreach ($cpts as $cpt) {
        $labels = [
          'name'          => _x($cpt->name, 'Post type general name', 'bit-form'),
          'singular_name' => _x($cpt->singular_label, 'Post type singular name', 'bit-form'),
          'menu_name'     => _x($cpt->menu_name, 'Admin Menu text', 'bit-form'),
        ];
        $args = [
          'labels'             => $labels,
          'public'             => 1 === $cpt->public ? true : false,
          'publicly_queryable' => 1 === $cpt->public_queryable ? true : false,
          'show_ui'            => 1 === $cpt->show_ui ? true : false,
          'show_in_menu'       => 1 === $cpt->show_in_menu ? true : false,
          'query_var'          => true,
          'rewrite'            => ['slug' => $cpt->name],
          'capability_type'    => 'post',
          'has_archive'        => true,
          'hierarchical'       => false,
          'show_in_rest'       => 1 === isset($cpt->show_in_rest) ? true : false,
          'menu_icon'          => $cpt->menu_icon,
          'supports'           => ['title', 'editor', 'author', 'excerpt', 'comments'],
        ];
        register_post_type($cpt->name, $args);
      }
    }
  }
}
