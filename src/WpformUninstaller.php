<?php
namespace Wpform;

final class WpformUninstaller
{   
    /**
     * Executes upon plugin deletion and
     * deletes the custom database table
     */
    public static function uninstall() 
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . FORM_SUBMISSIONS_TABLE_NAME;
        
        $sql = "DROP TABLE IF EXISTS $table_name;";
        
        $wpdb->query($sql);
    }
}
