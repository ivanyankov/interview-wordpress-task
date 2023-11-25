<?php
namespace Wpform;

class WpformActivator 
{
    public static function activate() 
    {
        global $wpdb;

        $table_name = $wpdb->prefix . FORM_SUBMISSIONS_TABLE_NAME;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_token varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            first_name varchar(255) NOT NULL,
            last_name varchar(255) NOT NULL,
            date_of_birth date NOT NULL,
            phone_number varchar(255) NOT NULL,
            downloaded_certificate BOOLEAN NOT NULL DEFAULT FALSE,
            created_at date NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
