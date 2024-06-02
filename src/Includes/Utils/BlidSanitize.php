<?php
namespace Bluefield\Includes\Utils;

/**
 * 
 * We are using sanitize_textarea_field for the most part because we don't want
 * to strip unneccessarily (such as commas)
 * @see https://developer.wordpress.org/reference/functions/sanitize_textarea_field/
 */
class BlidSanitize {
    public static function validate_remote_addr($ip) {
        //We still want to send the IP address to our service to track
        return filter_var($ip, FILTER_VALIDATE_IP) ?: $ip; 
    }

    public static function sanitize_user_agent($user_agent) {
        return sanitize_textarea_field($user_agent);
    }

    public static function sanitize_query_string($query) {
        return sanitize_textarea_field($query);
    }

    public static function sanitize_http_host($host_name) {
        return sanitize_textarea_field($host_name);
    }

    public static function sanitize_http_accept($accept) {
        return sanitize_textarea_field($accept);
    }

    public static function sanitize_request_uri($uri) {
        return sanitize_textarea_field($uri);
    }

    public static function sanitize_http_accept_encoding($accept_encoding) {
        return sanitize_textarea_field($accept_encoding);
    }

    public static function sanitize_http_accept_language($accept_language) {
        return sanitize_textarea_field($accept_language);
    }

    public static function sanitize_request_method($method) {
        return sanitize_textarea_field($method);
    }

    public static function sanitize_http_referer($referer) {
        return sanitize_textarea_field($referer);
    }

    public static function sanitize_cookie($cookie) {
        return sanitize_textarea_field($cookie);
    }
}