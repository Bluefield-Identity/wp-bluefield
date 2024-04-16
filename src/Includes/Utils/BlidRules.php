<?php
namespace Bluefield\Includes\Utils;

class BlidRules {
    public function pass() {
        $is_admin = $this->is_admin();
        $is_login = $this->is_login();
        $is_wp_cli = $this->is_wp_cli();
        $is_rest = $this->is_rest();

        $allowed = (
            !$is_admin &&
            !$is_login &&
            !$is_rest &&
            !$is_wp_cli
        );

        return $allowed;
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

    public function is_rest() {
        return !empty($GLOBALS['wp']->query_vars['rest_route']);
    }
}

