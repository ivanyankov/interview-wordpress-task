<?php

namespace Wpform;

use Wpform\WpformShortcodeHandler;
use Wpform\WpformValidator;
use Wpform\WpformHelper;
use FPDF;

class WpformMain {
    private static $instance = null;
    private $shortcodeHandler;

    private function __construct() {
        // Dependencies
        $this->shortcodeHandler = new WpformShortcodeHandler();
        // Actions
        add_action('init', [$this, 'wpform_load_textdomain']);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function init() {
        // Actions
        add_action('wp_ajax_wpform_submission_action', [$this, 'wpform_submission_ajax_handler']);
        add_action('wp_ajax_nopriv_wpform_submission_action', [$this, 'wpform_submission_ajax_handler']);
        add_action('admin_post_download_certificate', [$this, 'wpform_handle_download_certificate']);
        add_action('admin_post_nopriv_download_certificate', [$this, 'wpform_handle_download_certificate']);
    }

    /**
     * Submit the user form and sends email
     * with a link to download their certificate
     */
    public function wpform_submission_ajax_handler() {        
        WpformHelper::check_rate_limit(10);

        $nonce = isset($_POST['wpform_submission_nonce']) ? $_POST['wpform_submission_nonce'] : '';

        if (!wp_verify_nonce($nonce, 'wpform_submission_nonce')) {
            wp_send_json_error(['errors' => __('Nonce verification failed', 'wpform-textdomain')], 200);
        }
        
        $fieldsNameMapping = [
            'wpform_first_name' => __('First Name', 'wpform-textdomain'),
            'wpform_last_name' => __('Last Name', 'wpform-textdomain'),
            'wpform_email' => __('Email', 'wpform-textdomain'),
            'wpform_phone' => __('Phone', 'wpform-textdomain'),
            'wpform_dob' => __('Date of Birth', 'wpform-textdomain'),
        ];
    
        $fieldsRules = [
            'wpform_first_name' => ['required'],
            'wpform_last_name' => ['required'],
            'wpform_email' => ['required', 'email'],
            'wpform_phone' => ['required', 'phone'],
            'wpform_dob' => ['required', 'date'],
        ];

        // Validate the fields
        $validationErrors = WpformValidator::validate_fields($fieldsRules, $_POST, $fieldsNameMapping);

        if (!empty($validationErrors)) {
            wp_send_json_error([
                'validationErrors' => true,
                'errors' => $validationErrors
            ], 200);
        }

        global $wpdb;
        
        $first_name = sanitize_text_field($_POST['wpform_first_name']);
        $last_name = sanitize_text_field($_POST['wpform_last_name']);
        $email = sanitize_email($_POST['wpform_email']);
        $dob = sanitize_text_field($_POST['wpform_dob']);
        $phone = sanitize_text_field($_POST['wpform_phone']);
        $user_token = bin2hex(random_bytes(16));

        // save the user data to the database
        $table_name = $wpdb->prefix . FORM_SUBMISSIONS_TABLE_NAME;

        $wpdb->insert(
            $table_name,
            array(
                'user_token' => $user_token,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'date_of_birth' => $dob,
                'phone_number' => $phone,
                'created_at' => date("Y-m-d"),
                'downloaded_certificate' => false,
            ),
            array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
            )
        );

        // send email with a link to download the certificate to the user
        if (!mail(
            $email, 
            __("Congratulations on your certificate!", 'wpform-textdomain'), 
            sprintf(
                __("Congratulations for filling up our form! Please find below a link where you can download your certificate: <a href=\"%s\">%s</a>"),
                admin_url("admin-post.php?action=download_certificate&token=$user_token"),
                __("Download certificate!", 'wpform-textdomain')
            ),
            "Content-type: text/html; charset=iso-8859-1 \r\n"
        )) {
            wp_send_json_error(['errors' => __("The email with a link to download your certificate was not sent. Please contact the website administrators.", 'wpform-textdomain')], 200);
        }
        
        wp_send_json_success(__("You have successfully submitted the form! You will receive a link to download your certificate by an email."), 200);
    }

    /**
     * Generate the certificate and download it
     */
    public function wpform_handle_download_certificate() {
        global $wpdb;

        $table_name = $wpdb->prefix . FORM_SUBMISSIONS_TABLE_NAME;
        $token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';

        if(!$token) {
            die;
        }

        $userData = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE user_token = %s", $token));

        if(!$userData || $userData->downloaded_certificate) {
            wp_die('Unauthorized access!', 'Error', array('response' => 403));
        }

        $pdf = new FPDF('P','mm','A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 22);
        $pdf->Cell(0, 40, "", 0, 1, 'C');
        $pdf->Cell(0, 20, __("This is to certify that", 'wpform-textdomain'), 0, 1, 'C');
        $pdf->Cell(0, 20, sprintf("%s %s", $userData->first_name, $userData->last_name), 0, 1, 'C');
        $pdf->Cell(0, 20, __("filled the", 'wpform-textdomain'), 0, 1, 'C');
        $pdf->Cell(0, 20, __("2023 BeeCoded Form", 'wpform-textdomain'), 0, 1, 'C');
        $pdf->Cell(0, 30, \DateTime::createFromFormat('Y-m-d', $userData->created_at)->format('l j F'), 0, 1, 'C');
        $pdf->Image(PLUGIN_ROOT_PATH .'images/signature.png', 70, 160, 70);
        $pdf->Cell(0, 60, __("BeeCoded SRL Bucuresti", 'wpform-textdomain'), 0, 1, 'C');
        $pdf->Cell(0, 40, "", 0, 1, 'C');
        $pdf->Output('D', 'certificate.pdf');

        $wpdb->update(
            $table_name,
            array('downloaded_certificate' => 1),
            array('user_token' => $token),
            array('%d'),
            array('%s')
        );

        exit;
    }

    /**
     * Load the plugin textdomain
     */
    public function wpform_load_textdomain() {
        load_plugin_textdomain('wpform-textdomain', false, PLUGIN_ROOT_PATH . '/languages/');
    }
}
