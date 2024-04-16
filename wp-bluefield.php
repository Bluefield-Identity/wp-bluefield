<?php
/**
 * Plugin Name: Bluefield Identity
 * Plugin URI: https://www.bluefieldidentity.com/
 * Description: Enables sites to get more conversions by eliminating click fraud, bots, and scrapers
 * Version: 1.0.0
 * Requires at least: 5.0
 * Requires PHP: 5.6.20
 * Author: Bluefield Identity Inc.
 * Author URI: https://www.bluefieldidentity.com/
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: bluefield-identity
 * Domain Path: /languages/
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('BLUEFIELD', __FILE__);

if ( ! defined( 'BLUEFIELD_PATH' ) ) {
    define( 'BLUEFIELD_PATH', plugin_dir_path( BLUEFIELD ) );
}

if ( ! defined( 'BLUEFIELD_PLUGIN_URL' ) ) {
    define( 'BLUEFIELD_PLUGIN_URL', plugin_dir_url( BLUEFIELD ) );
}

require_once 'vendor/autoload.php';

use Bluefield\Options\BlidOptions;

$api_settings   = BlidOptions::get_option('api_settings', []);
$client_vars    = BlidOptions::get_option('client_vars', []);

global $bluefield_api_settings;
$bluefield_api_settings = array_merge(
    $api_settings,
    ['clientVars' => $client_vars]
);

global $bluefield_server_data;
$bluefield_server_data = [
    'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? '',
    'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'] ?? '',
    'QUERY_STRING' => $_SERVER['QUERY_STRING'] ?? '',
    'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? '',
    'HTTP_ACCEPT' => $_SERVER['HTTP_ACCEPT'] ?? '',
    'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? '',
    'HTTP_ACCEPT_ENCODING' => $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '',
    'HTTP_ACCEPT_LANGUAGE' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? '',
    'HTTP_REFERER' => $_SERVER['HTTP_REFERER'] ?? '',
];

use Bluefield\Includes\API\BlidFilter;
use Bluefield\Includes\Utils\BlidUser;
use Bluefield\Includes\Utils\BlidClientVars;
use Bluefield\Includes\Utils\BlidLogger;
use Bluefield\Includes\Utils\BlidPlatforms;

class Bluefield {
    public function __construct() {
        $this->boot();
    }

    public function boot() {
        register_activation_hook(BLUEFIELD, [$this, 'activate']);
        register_deactivation_hook(BLUEFIELD, [$this, 'deactivate']);

        $this->init_actions();
    }

    public function activate() {
        flush_rewrite_rules();
    }

    public function deactivate() {

    }

    public function init_actions() {
        add_action('blid__filter', [$this, 'create_filter']);
    }

    public function create_filter() {
        global $bluefield_server_data;
        global $bluefield_api_settings;

        $remote_key = isset($bluefield_api_settings['remote_key']) ? $bluefield_api_settings['remote_key'] : null;
        $account_password = isset($bluefield_api_settings['account_password']) ? $bluefield_api_settings['account_password'] : null;

        if(
            !empty($bluefield_server_data) &&
            !empty($remote_key) &&
            !empty($account_password)
        ) {
            $user = new BlidUser($bluefield_server_data);
            $clientVars = new BlidClientVars();
            $bfLogger = new BlidLogger();
            $platforms = new BlidPlatforms();

            $filter = new BlidFilter(
                $user,
                $clientVars,
                $bfLogger,
                $platforms
            );

            $filter->process_recommendation();
        }
    }
}

$bluefield = new Bluefield();

use Bluefield\Admin\BlidBaseAdmin;

$admin = new BlidBaseAdmin();

use Bluefield\Includes\Utils\BlidSession;

function blid_set_visitor() {
    $bf_session = new BlidSession();
}
add_action('init', 'blid_set_visitor');

use \Bluefield\Includes\Utils\BlidRules;

function do_blid_filter() {
    $run_filter = apply_filters('blid__pre_run_filter', true);

    $rules = new BlidRules();

    if($run_filter && $rules->pass()) {
        do_action('blid__filter');
    }
}
add_action('template_redirect', 'do_blid_filter');


