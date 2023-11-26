<?php
namespace Wpform;

final class WpformShortcodeHandler {
    public function __construct() {
        add_shortcode('get_user_form', [$this, 'get_user_form_html']);
        // Actions
        add_action('wp_enqueue_scripts', [$this, 'wpform_enqueue_scripts']);
    }

    public function wpform_enqueue_scripts() {
        global $post;

        // Check if the current post contains the 'get_user_form' shortcode
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'get_user_form')) {
            wp_enqueue_script('wpform-shortcodes-scripts', PLUGIN_ROOT_URL . 'js/wpform-shortcodes-scripts.min.js', array('jquery'), null, true);
            wp_enqueue_style('wpform-shortcodes-styles', PLUGIN_ROOT_URL . 'css/wpform-shortcodes-styles.min.css');
        }
    }

    public function get_user_form_html() {
        ob_start();
        ?>
        <div class="wpform--holder">
            <form action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-action="wpform_submission_action" method="post">
                <input placeholder="<?php echo __('First Name', 'wpform'); ?>" type="text" name="wpform_first_name">
                <input placeholder="<?php echo __('Last Name', 'wpform'); ?>" type="text" name="wpform_last_name">
                <input placeholder="<?php echo __('Email', 'wpform'); ?>" type="email" name="wpform_email">
                <input type="date" name="wpform_dob" />
                <input placeholder="<?php echo __('Phone', 'wpform'); ?>" type="tel" name="wpform_phone">

                <?php wp_nonce_field('wpform_submission_nonce', 'wpform_submission_nonce'); ?>
                
                <button type="button"><?php echo __('Submit', 'wpform'); ?></button>
            </form>
            <span id="form-messages--holder"></span>
        </div>
        <?php
        return ob_get_clean();
    }
}
