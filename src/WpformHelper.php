<?php

namespace Wpform;


class WpformHelper {
    /**
     * This ensures that the rate limit check is 
     * performed before any other processing takes place.
     */
    public static function check_rate_limit($attempts = 10) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = "rate_limit_{$ip}";
        $attempt = get_transient($key);
    
        if ($attempt !== false && $attempt > $attempts) {
            wp_send_json_error(["errors" => __('Too many requests. Please try again later.', 'wpform-textdomain')]);
            exit;
        }
    
        set_transient($key, ($attempt ? $attempt : 0) + 1, MINUTE_IN_SECONDS);
    }
    
}