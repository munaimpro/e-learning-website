<?php

namespace BitCode\BitForm\Core\Util;

/**
 * Class handling SmartTags
 *
 * @since 1.0.0
 */

final class SmartTags
{
  public static function smartTagFieldKeys()
  {
    return [
      '_bf_current_time',
      '_bf_custom_date_format',
      '_bf_admin_email',
      '_bf_date_default',
      '_bf_date.m/d/y',
      '_bf_date.d/m/y',
      '_bf_date.y/m/d',
      '_bf_date.Y-m-d',
      '_bf_date.d-M, Y',
      '_bf_time',
      '_bf_user_email',
      '_bf_weekday',
      '_bf_http_referer_url',
      '_bf_ip_address',
      '_bf_operating_system',
      '_bf_browser_name',
      '_bf_random_digit',
      '_bf_user_id',
      '_bf_user_first_name',
      '_bf_user_last_name',
      '_bf_user_display_name',
      '_bf_user_nice_name',
      '_bf_user_login_name',
      '_bf_user_email',
      '_bf_user_url',
      '_bf_current_user_role',
      '_bf_author_id',
      '_bf_author_display',
      '_bf_author_email',
      '_bf_site_title',
      '_bf_site_description',
      '_bf_site_url',
      '_bf_wp_local_codes',
      '_bf_separator',
      '_bf_post_id',
      '_bf_post_name',
      '_bf_post_title',
      '_bf_post_date',
      '_bf_post_modified_date',
      '_bf_post_url',
      '_bf_query_param',
      '_bf_user_meta_key',
      '_bf_is_user_logged_in',
      '_bf_entry_id',
      '_bf_back_view',
    ];
  }

  public static function getPostUserData($referer = false)
  {
    $post = [];
    if ($referer && isset($_SERVER['HTTP_REFERER'])) {
      $postId = url_to_postid($_SERVER['HTTP_REFERER']);
    } else {
      $postId = url_to_postid($_SERVER['REQUEST_URI']);
    }

    if ($postId) {
      $post = get_post($postId, 'OBJECT');
    }

    $user = wp_get_current_user();

    $user_roles = $user->roles;

    if (!is_wp_error($user_roles) && count($user_roles) > 0) {
      $user->current_user_role = $user_roles[0];
    }

    $postAuthorInfo = [];
    if (isset($post->post_author)) {
      $postAuthorInfo = get_user_by('ID', $post->post_author);
    }

    return ['user' => $user, 'post' => $post, 'post_author_info' => $postAuthorInfo];
  }

  public static function setCustomSmartKeyValue($smartTags, $key, $customValue)
  {
    if (empty($customValue)) {
      return $smartTags;
    }
    switch ($key) {
      case '_bf_custom_date_format':
        $smartTags['_bf_custom_date_format'] = date($customValue);
        return $smartTags;
        break;
      case '_bf_query_param':
        $smartTags['_bf_query_param'] = isset($_GET[$customValue]) ? urldecode(stripslashes($_GET[$customValue])) : '';
        return $smartTags;
        break;
      case '_bf_user_meta_key':
        $user = wp_get_current_user();
        $metaValue = get_user_meta($user->ID, $customValue, true);
        if ($metaValue && is_string($metaValue)) {
          $smartTags['_bf_user_meta_key'] = $metaValue;
        }
        return $smartTags;
        break;
      case '_bf_count':
        return $smartTags;
        break;
      case '_bf_length':
        return $smartTags;
        break;
      case '_bf_calc':
        return $smartTags;
        break;
      case '_bf_math':
        return $smartTags;
        break;
      case '_bf_concat':
        return $smartTags;
        break;
      case '_bf_number':
          return $smartTags;
          break;
      default:
        return $smartTags;
    }
  }

  public static function smartTags($data, $key = '', $customValue = '')
  {
    $userDetail = IpTool::getUserDetail();
    $device = explode('|', $userDetail['device']);

    global $bf_entry_id;
    global $wp;

    if (is_array($device)) {
      $browser = $device[0];
      $operating = $device[1];
    }
    $customSmartKeys = [
      '_bf_query_param',
      '_bf_user_meta_key',
      '_bf_custom_date_format',
      '_bf_count',
      '_bf_length',
      '_bf_calc',
      '_bf_math',
      '_bf_concat',
      '_bf_number',
    ];

    $smartTags = [
      '_bf_current_time'       => date('Y-m-d H:i:s'),
      '_bf_admin_email'        => get_bloginfo('admin_email'),
      '_bf_date_default'       => wp_date(get_option('date_format')),
      '_bf_date.m/d/y'         => wp_date('m/d/y'),
      '_bf_date.d/m/y'         => wp_date('d/m/y'),
      '_bf_date.y/m/d'         => wp_date('y/m/d'),
      '_bf_date.Y-m-d'         => wp_date('Y-m-d'),
      '_bf_date.d-M, Y'        => wp_date('d-M, Y'),
      '_bf_time'               => wp_date(get_option('time_format')),
      '_bf_weekday'            => wp_date('l'),
      '_bf_http_referer_url'   => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
      '_bf_ip_address'         => IpTool::getIP(),
      '_bf_browser_name'       => isset($browser) ? $browser : '',
      '_bf_operating_system'   => isset($operating) ? $operating : '',
      '_bf_random_digit'       => uniqid(),
      '_bf_user_id'            => (isset($data['user']->ID) ? strval($data['user']->ID) : ' '),
      '_bf_user_first_name'    => (isset($data['user']->first_name) ? $data['user']->first_name : ' '),
      '_bf_user_last_name'     => (isset($data['user']->last_name) ? $data['user']->last_name : ' '),
      '_bf_user_display_name'  => (isset($data['user']->display_name) ? $data['user']->display_name : ' '),
      '_bf_user_nice_name'     => (isset($data['user']->user_nicename) ? $data['user']->user_nicename : ' '),
      '_bf_user_login_name'    => (isset($data['user']->user_login) ? $data['user']->user_login : ' '),
      '_bf_user_email'         => (isset($data['user']->user_email) ? $data['user']->user_email : ' '),
      '_bf_user_url'           => (isset($data['user']->user_url) ? $data['user']->user_url : ' '),
      '_bf_current_user_role'  => (isset($data['user']->current_user_role) ? $data['user']->current_user_role : ' '),
      '_bf_author_id'          => (isset($data['post_author_info']->ID) ? strval($data['post_author_info']->ID) : ' '),
      '_bf_author_display'     => (isset($data['post_author_info']->display_name) ? $data['post_author_info']->display_name : ' '),
      '_bf_author_email'       => (isset($data['post_author_info']->user_email) ? $data['post_author_info']->user_email : ' '),
      '_bf_site_title'         => get_bloginfo('name'),
      '_bf_site_description'   => get_bloginfo('description'),
      '_bf_site_url'           => get_bloginfo('url'),
      '_bf_wp_local_codes'     => get_bloginfo('language'),
      '_bf_separator'          => BITFORMS_BF_SEPARATOR,
      '_bf_post_id'            => (is_object($data['post']) ? strval($data['post']->ID) : ''),
      '_bf_post_name'          => (is_object($data['post']) ? $data['post']->post_name : ''),
      '_bf_post_title'         => (is_object($data['post']) ? $data['post']->post_title : ''),
      '_bf_post_date'          => (is_object($data['post']) ? $data['post']->post_date : ''),
      '_bf_post_modified_date' => (is_object($data['post']) ? $data['post']->post_modified : ''),
      '_bf_post_url'           => (is_object($data['post']) ? strval(get_permalink($data['post']->ID)) : ''),
      '_bf_is_user_logged_in'  => is_user_logged_in() ? 'logged_in' : 'logged_out',
      '_bf_entry_id'           => $bf_entry_id ? $bf_entry_id : '',
      '_bf_back_view'          => home_url($wp->request),
    ];

    if (in_array($key, $customSmartKeys)) {
      $smartTags = static::setCustomSmartKeyValue($smartTags, $key, $customValue);
    }
    return $smartTags;
  }

  public static function getSmartTagValue($key, $isReferer = false, $customValue = '')
  {
    $smartKeys = self::smartTagFieldKeys();
    if (!in_array($key, $smartKeys)) {
      return;
    }
    $data = self::getPostUserData($isReferer);

    $smartTags = self::smartTags($data, $key, $customValue);

    if (isset($smartTags[$key])) {
      return $smartTags[$key];
    } else {
      return '';
    }

    // $userMeta = "";
    // if ( !empty( $customValue ) ) {
    //     $existMeta = get_user_meta( $data['user']->ID, $customValue, true );
    //     if ( $existMeta && is_string( $existMeta ) ) {
    //         $userMeta = $existMeta;
    //     }
    // }
  }
}
