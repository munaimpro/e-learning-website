<?php

/**
 * Class For Database Migration
 *
 * @category Database
 * @author   BitCode Developer <developer@bitcode.pro>
 */

namespace BitCode\BitForm\Core\Database;

/**
 * Database Migration
 */
final class DB
{
  /**
   * Undocumented function
   *
   * @return void
   */
  public static function migrate()
  {
    global $wpdb;
    global $bitforms_db_version;
    $collate = '';

    if ($wpdb->has_cap('collation')) {
      if (!empty($wpdb->charset)) {
        $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
      }

      if (!empty($wpdb->collate)) {
        $collate .= " COLLATE $wpdb->collate";
      }
    }

    $max_index_length = 191;

    $table_schema = [
      "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}bitforms_reports` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `category` varchar(50)  NOT NULL,
                `type` varchar(20)  NOT NULL,
                `context` varchar(20)  NOT NULL,
                `details` longtext DEFAULT NULL,
                `isDefault` tinyint(1) DEFAULT 0,/* 0 not default, 1 default */
                `user_id` bigint(20) unsigned DEFAULT NULL,
                `user_ip` int(11) unsigned DEFAULT NULL,
                `user_device` varchar(50) DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) $collate;",

      "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}bitforms_form` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `form_name` varchar(255) DEFAULT NULL,
                `form_content` longtext DEFAULT NULL,
                `builder_helper_state` longtext DEFAULT NULL,
                `atomic_class_map` longtext DEFAULT NULL,
                `generated_script_page_ids` longtext DEFAULT NULL,
                `user_id` bigint(20) unsigned DEFAULT NULL,
                `user_ip` int(11) unsigned DEFAULT NULL,
                `views` int(11) unsigned DEFAULT 0,
                `entries` int(11) unsigned DEFAULT 0,
                `user_device` varchar(50) DEFAULT NULL,
                `status` tinyint(1) DEFAULT 1,/* 0 not published, 1 published,  2 trashed */
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) $collate;",

      "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}bitforms_workflows` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `workflow_name` varchar(255) NOT NULL,
                `workflow_type` varchar(50) NOT NULL,
                `workflow_run` varchar(50) NOT NULL,
                `workflow_behaviour` varchar(25) NOT NULL,
                `workflow_condition` longtext DEFAULT NULL,
                `workflow_order` int(11) unsigned DEFAULT NULL,
                `form_id` bigint(20) unsigned DEFAULT NULL,
                `user_id` bigint(20) unsigned DEFAULT NULL,
                `user_ip` int(11) unsigned DEFAULT NULL,
                `user_location` text DEFAULT NULL,
                `user_device` varchar(50) DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `form_id` (`form_id`)
            ) $collate;",

      "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}bitforms_success_messages` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `message_title` varchar(255) DEFAULT NULL,
                `message_content` longtext DEFAULT NULL,
                `message_config` longtext DEFAULT NULL,
                `form_id` bigint(20) unsigned DEFAULT NULL,
                `user_id` bigint(20) unsigned DEFAULT NULL,
                `user_ip` int(11) unsigned DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) $collate;",

      "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}bitforms_email_template` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `title` text DEFAULT NULL,
                `sub` text DEFAULT NULL,
                `body` longtext DEFAULT NULL,
                `form_id` bigint(20) unsigned DEFAULT NULL,
                `user_id` bigint(20) unsigned DEFAULT NULL,
                `user_ip` int(11) unsigned DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) $collate;",

      "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}bitforms_integration` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `category` varchar(50)  NOT NULL,
                `integration_name` varchar(255) DEFAULT NULL,
                `integration_type` varchar(50)  NOT NULL,
                `integration_details` longtext DEFAULT NULL,
                `form_id` bigint(20) unsigned DEFAULT NULL, /* form_id = 0 means all/app */
                `user_id` bigint(20) unsigned DEFAULT NULL,
                `user_ip` int(11) unsigned DEFAULT NULL,
                `status` tinyint(1) DEFAULT 1,/* 0 disabled, 1 published,  2 trashed */
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) $collate;",

      "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}bitforms_form_entries` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `form_id` bigint(20) unsigned DEFAULT NULL,
                `user_id` bigint(20) unsigned DEFAULT NULL,
                `user_ip` varchar(255) DEFAULT NULL,
                `user_location` text DEFAULT NULL,
                `user_device` varchar(50) DEFAULT NULL,
                `referer` TEXT DEFAULT NULL,
                `status` tinyint(1) DEFAULT 0,/* 0 not viewed, 1 viewed,  2 trashed */
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `form_id` (`form_id`)
            ) $collate;",

      "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}bitforms_form_entrymeta` (
                `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `bitforms_form_entry_id` bigint(20) unsigned DEFAULT NULL,
                `meta_key` varchar(255) DEFAULT NULL,
                `meta_value` longtext,
                PRIMARY KEY (`meta_id`),
                KEY `meta_key` (`meta_key`({$max_index_length})),
                KEY `entry_id` (`bitforms_form_entry_id`)
            ) $collate;",

      "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}bitforms_form_entry_log` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `log_type` varchar(50) DEFAULT NULL,
                `ip` int(11) DEFAULT NULL,
                `action_type` varchar(50) DEFAULT NULL,
                `form_entry_id` bigint(20) DEFAULT NULL,
                `form_id` bigint(20) DEFAULT NULL,
                `content` longtext,
                `created_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                KEY `form_id` (`form_id`),
                KEY `form_entry_id` (`form_entry_id`)
            ) $collate;",

      "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}bitforms_form_log_details` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `log_id` bigint(20) NOT NULL,
                `integration_id` bigint(20) DEFAULT NULL,
                `api_type` varchar(255) DEFAULT NULL,
                `response_type` varchar(50) DEFAULT NULL,
                `response_obj` LONGTEXT DEFAULT NULL,
                `created_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                KEY `log_id` (`log_id`),
                KEY `integration_id` (`integration_id`)
            ) $collate;",
      "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}bitforms_form_entry_relatedinfo` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `info_type` VARCHAR(50) NOT NULL,
                `info_details` LONGTEXT NULL,
                `form_id` BIGINT(20) UNSIGNED NOT NULL,
                `entry_id` BIGINT(20) UNSIGNED NOT NULL,
                `user_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
                `user_ip` INT(11) UNSIGNED NULL DEFAULT NULL,
                `status` INT(1) UNSIGNED NOT NULL DEFAULT '1',
                `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `form_id` (`form_id`),
                KEY `entry_id` (`entry_id`)
            ) $collate;"
    ];

    $table_schema_update = [
      "ALTER TABLE `{$wpdb->prefix}bitforms_form_entry_log`
                CHANGE COLUMN `log_type` `log_type` VARCHAR(50) NULL DEFAULT NULL,
                ADD COLUMN `action_type` VARCHAR(50) NULL DEFAULT NULL,
                CHANGE COLUMN `meta_key` `content` LONGTEXT NULL,
                CHANGE COLUMN `created_at` `created_at` DATETIME NOT NULL",
      "ALTER TABLE `{$wpdb->prefix}bitforms_form_log_details`
                CHANGE COLUMN `api_type` `api_type` VARCHAR(255) NULL DEFAULT NULL,
                CHANGE COLUMN `response_obj` `response_obj` LONGTEXT NULL,
                CHANGE COLUMN `created_at` `created_at` DATETIME NOT NULL",
    ];

    include_once ABSPATH . 'wp-admin/includes/upgrade.php';
    foreach ($table_schema as $table) {
      dbDelta($table);
    }
    $installed_db_version = get_site_option('bitforms_db_version');
    if ($installed_db_version && version_compare('1.1', $installed_db_version, '>=')) {
      // if new db version is equal or higher than 1.1
      foreach ($table_schema_update as $table) {
        $wpdb->query($table);
      }
    }
    if ($installed_db_version && version_compare('1.3', $installed_db_version, '>=')) {
      // if new db version is equal or higher than 1.3
      $wpdb->query("ALTER TABLE `{$wpdb->prefix}bitforms_form_entries` CHANGE `user_ip` `user_ip` VARCHAR(255) NULL DEFAULT NULL");
    }

    if ($installed_db_version && version_compare('2.0', $installed_db_version, '>=')) {
      $wpdb->query(
        "ALTER TABLE `{$wpdb->prefix}bitforms_success_messages`
                ADD `message_config` LONGTEXT NULL AFTER `message_content`"
      );
      $wpdb->query(
        "ALTER TABLE `{$wpdb->prefix}bitforms_form`
                ADD `builder_helper_state` LONGTEXT NULL AFTER `form_content`,
                ADD `atomic_class_map` LONGTEXT NULL AFTER `builder_helper_state`,
                ADD `generated_script_page_ids` LONGTEXT NULL AFTER `atomic_class_map`"
      );
      $wpdb->query(
        "ALTER TABLE `{$wpdb->prefix}bitforms_workflows` 
                ADD `workflow_order` INT(11) DEFAULT NULL AFTER `workflow_condition`"
      );
    }

    if ($installed_db_version && version_compare('2.2', $installed_db_version, '>=')) {
      $wpdb->query(
        "ALTER TABLE `{$wpdb->prefix}bitforms_form_entries` 
        MODIFY `referer` TEXT DEFAULT NULL"
      );
    }

    update_site_option(
      'bitforms_db_version',
      $bitforms_db_version
    );
  }
}
