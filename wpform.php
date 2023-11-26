<?php
/**
 * Plugin Name: Wordpress Form
 * Description: WordPress plugin which provides a form that will generate a certificate and emailed once it is filled and submitted.
 * Version: 1.0
 * Author: Ivan Yankov
 * Text Domain: wpform-textdomain
 */

define('FORM_SUBMISSIONS_TABLE_NAME', 'form_submissions');
define('PLUGIN_ROOT_URL', plugin_dir_url(__FILE__));
define('PLUGIN_ROOT_PATH', plugin_dir_path(__FILE__));

require_once __DIR__ . '/vendor/autoload.php';

$plugin = Wpform\WpformMain::getInstance();
$plugin->init();

register_activation_hook(__FILE__, ['Wpform\WpformActivator', 'activate']);