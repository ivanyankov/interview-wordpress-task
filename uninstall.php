<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

if (!current_user_can('activate_plugins')) {
    return;
}

global $wpdb;

$table_name = $wpdb->prefix . FORM_SUBMISSIONS_TABLE_NAME;

$sql = "DROP TABLE IF EXISTS $table_name;";

$wpdb->query($sql);
