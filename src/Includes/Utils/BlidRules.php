<?php
namespace Bluefield\Includes\Utils;

class BlidRules {
    public function pass() {
        $is_admin  = $this->is_admin();
        $is_login  = $this->is_login();
        $is_wp_cli = $this->is_wp_cli();
        $is_rest   = $this->is_rest();
        $is_cron   = $this->is_cron();
        $is_asset   = $this->is_asset();

        return !(
            $is_admin ||
            $is_login ||
            $is_rest ||
            $is_wp_cli ||
            $is_cron ||
            $is_asset
        );
    }

    public function is_admin() {
        return is_admin();
    }

    public function is_login() {
        return is_login();
    }

    public function is_wp_cli() {
        return defined( 'WP_CLI' ) && WP_CLI;
    }

    // public function is_rest() {
    //     return !empty($GLOBALS['wp']->query_vars['rest_route']);
    // }


    public function is_rest() {

        if (defined('REST_REQUEST') && REST_REQUEST) {
            return true;
        }

        if (
            isset($_SERVER['REQUEST_URI']) &&
            str_contains($_SERVER['REQUEST_URI'], '/wp-json/')
        ) {
            return true;
        }

        return false;
    }

    public function is_cron() {

        if (defined('DOING_CRON') && DOING_CRON) {
            return true;
        }

        if (
            isset($_SERVER['SCRIPT_NAME']) &&
            str_contains($_SERVER['SCRIPT_NAME'], 'wp-cron.php')
        ) {
            return true;
        }

        return false;
    }

    public function is_asset() {
        $uri = $_SERVER['REQUEST_URI'] ?? '';

        if ($uri === '' || $uri === '/' || $uri === '/index.php') {
            return false;
        }

        return (
            $uri === '/favicon.ico' ||
            preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|webp)$/i', $uri)
        );
    }
}

