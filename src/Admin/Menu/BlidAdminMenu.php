<?php
/*
 * BlidAdminMenu Class
 */

namespace Bluefield\Admin\Menu;

use Bluefield\Options\BlidOptions;

class BlidAdminMenu
{
    const PAGE_IDENTIFIER       = 'bluefield_identity';
    const PAGE_TEMPLATE         = 'dashboard';
    const COMMON_SETTINGS_PAGE  = 'bluefield_identity';
    const SETTINGS_PAGE         = 'bluefield_identity-api-settings';

    private $option_group = 'api_settings';

    private $option_name = 'api_settings';

    private $prefix;

    private $settings = [];

    /**
     * Schema, such as max character count, for our options
     *
     * @var array[]
     */
    private $fields = [
        'remote_key' => [
            "max" => 24,
            'title' => "Client Remote Key",
            "placeholder" => "Client Remote Key",

        ],
        'account_password' => [
            "max" => 32,
            'title' => "Client Account Password",
            "placeholder" => "Client Account Password",
        ],
    ];

    public function __construct() {
        global $bluefield_api_settings;

        $this->prefix = BlidOptions::PREFIX;
        $this->settings = $bluefield_api_settings ?? [];

        $this->option_group = $this->prefix . $this->option_group;
        $this->option_name  = $this->prefix . $this->option_name;
    }

    public function register_hooks()
    {
        add_action('admin_menu', [$this, 'register_pages']);
        add_action('admin_init', [$this, 'register_common_settings']);
    }

    public function register_pages()
    {
        $manage_capability = $this->get_manage_capability();
        $page_identifier = $this->get_page_identifier();

        $menu = add_menu_page(
            'Bluefield Identity: ' . __('Dashboard', 'bluefield-identity'),
            'Bluefield Identity',
            $manage_capability,
            $page_identifier,
            [$this, 'show_page'],
            'dashicons-shield',
            98
        );

        $submenu = add_submenu_page(
            $page_identifier,
            'Bluefield Identity: ' . __('API Settings', 'bluefield-identity'),
            __('API Settings', 'bluefield-identity'),
            $manage_capability,
            self::SETTINGS_PAGE,
            [$this, 'show_page'],
            1
        );

        add_action( 'load-' . $menu, [$this, 'do_admin_enqueue'] );
        add_action( 'load-' . $submenu, [$this, 'do_admin_enqueue'] );
    }

    public function do_admin_enqueue() {
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_css'] );
    }

    public function enqueue_admin_css() {
        wp_enqueue_style( 'bluefield-css', BLUEFIELD_PLUGIN_URL . 'resources/css/bluefield-admin.css', [], '1.3', 'all' );
    }

    public function register_common_settings() {
        add_settings_section(
            'blid-common-settings-section',
            __('Bluefield Identity API Settings', 'bluefield-identity'),
            [$this, 'blid_common_settings_output'],
            self::SETTINGS_PAGE,
            []
        );

        add_settings_field(
            'blid-remote-key',
            '<span class="required">*</span> '. __('Client Remote Key', 'bluefield-identity'),
            [$this, 'blid_remote_key_callback'],
            self::SETTINGS_PAGE,
            'blid-common-settings-section',
            ['max' => 24]
        );

        add_settings_field(
            'blid-account-password',
            '<span class="required">*</span> '. __('Client Account Password', 'bluefield-identity'),
            [$this, 'blid_account_password_callback'],
            self::SETTINGS_PAGE,
            'blid-common-settings-section',
            ['max' => 32]
        );

        add_settings_section(
            'blid-common-settings-section-footer',
            '',
            [$this, 'blid_common_settings_output_footer'],
            self::SETTINGS_PAGE,
            []
        );

        register_setting('blid-common-settings-options', $this->option_name, [$this, 'validate_options_char_count']);
    }

    /**
     * Validate the client vars (make sure they don't exceed 32 characters)
     *
     * @param $input
     * @return mixed
     */
    function validate_options_char_count(array $input) {
        $schema = $this->fields;
        $valid_input = $this->settings;

        foreach ($input as $key => $value) {
            if(isset($schema[$key]) && isset($schema[$key]['max'])) {
                $max = $schema[$key]['max'];
                $title = $key;
                if (isset($schema[$key]['title'])) {
                    $title = $this->fields[$key]['title'];
                }
                if (is_string($value) && strlen($value) > $max) {
                    add_settings_error('bluefield_char_count', $key . '_error', $title . ' cannot be greater than ' . $max . ' characters!', 'error');
                    return $valid_input;
                }
            }
        }

        return $input;
    }

    public function get_page_identifier()
    {
        return self::PAGE_IDENTIFIER;
    }

    public function get_manage_capability()
    {
        return 'manage_options';
    }

    public function show_page()
    {
        require_once BLUEFIELD_PATH . 'pages/' . self::PAGE_TEMPLATE . '.php';
    }

    /*
     * Common settings
     *
     *
     *
     */

    public function blid_common_settings_output() {
        echo '
        <p><a target="_blank" href="https://www.bluefieldidentity.com/">Bluefield Identity</a> is a subscription web service that works on <strong>YOUR SITE</strong> to automatically block invalid traffic.</p>
        <p>Partner with us to address web scrapers, form stuffers, inventory hoarders and a variety of other bad behaviors.</p>
        <p>Bluefield Identity allows you to deny invalid traffic from running up your paid search bill for ALL advertisers.  Set your own rate limits, define geography fences, reduce proxied traffic and more.</p>
        <p>Click <a target="_blank" href="https://www.bluefieldidentity.com">here</a> to learn more! (We currently offer partnerships to US based websites only.)</p>
        ';
    }

    public function blid_account_password_callback($args) {
        $options = BlidOptions::get_option($this->option_name);
        $value = isset($options['account_password']) ? $options['account_password'] : '';
        $class = !empty($value) ? 'has-value' : 'required-value-missing-notice';
        $max = isset($args['max']) ? 'max="' . intval($args['max']) .'"': '';

        echo "<input style='width: 275px' class='".esc_attr($class)."' id='account-password' name='".esc_attr($this->option_name)."[account_password]' type='text' value='" . esc_attr($value) . "' placeholder='Client Account Password' ".esc_attr($max)." />";
    }

    public function blid_remote_key_callback($args) {
        $options = BlidOptions::get_option($this->option_name);
        $value = isset($options['remote_key']) ? $options['remote_key'] : '';
        $class = !empty($value) ? 'has-value' : 'required-value-missing-notice';
        $max = isset($args['max']) ? 'max="' . intval($args['max']) .'"': '';

        echo "<input style='width: 275px' class='".esc_attr($class)."' id='remote-key' name='".esc_attr($this->option_name)."[remote_key]' type='text' value='" . esc_attr($value) . "' placeholder='Client Remote Key' ".esc_attr($max)." />";
    }

    public function blid_common_settings_output_footer() {
        echo '
        <p><span class="required"><strong>*</strong></span> = required field</p>
        <p>Don\'t have a Client Remote Key and Password? <a target="_blank" href="https://www.bluefieldidentity.com/join/">Sign up for your 30 day free trial here</a>. No contract, no obligation and no credit card needed.</p>
        <p>Already signed up? Your Bluefield Identity Client Dashboard gives you access to your account settings, contacts for Bluefield Identity support, account reporting, and billing status and history.</p>
        <p>Access the Bluefield Identity Client Dashboard <a target="_blank" href="https://clients.bluefieldidentity.com/">here</a>.</p>
        ';
    }
}
