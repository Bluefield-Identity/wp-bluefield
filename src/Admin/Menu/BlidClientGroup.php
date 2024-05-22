<?php
/**
 *BlidClientGroup class
 */
namespace Bluefield\Admin\Menu;

use Bluefield\Admin\BlidBaseAdmin;
use Bluefield\Options\BlidOptions;

class BlidClientGroup {
    const PAGE = 'bluefield_identity-client-vars';
    const PAGE_TEMPLATE = 'dashboard';

    private $option_group = 'client_vars';

    private $option_name = 'client_vars';

    private $prefix;

    private $settings = [];

    /**
     * @var \string[]
     */
    private $fields = [
        'clientVar1' => [
            'type' => 'text',
            'title' => 'Additional Variable 1 Name',
            'max' => '32',
        ],
        'clientVar2' => [
            'type' => 'text',
            'title' => 'Additional Variable 2 Name',
            'max' => '32',
        ],
        'clientVar3' => [
            'type' => 'text',
            'title' => 'Additional Variable 3 Name',
            'max' => '32',
        ],
        'clientVar4' => [
            'type' => 'text',
            'title' => 'Additional Variable 4 Name',
            'max' => '32',
        ],
        'clientVar5' => [
            'type' => 'text',
            'title' => 'Additional Variable 5 Name',
            'max' => '32',
        ],
        'clientVar6' => [
            'type' => 'text',
            'title' => 'Additional Variable 6 Name',
            'max' => '32',
        ]
    ];

    public function __construct() {
        global $bluefield_api_settings;

        $this->prefix = BlidOptions::PREFIX;

        $this->option_group = $this->prefix . $this->option_group;
        $this->option_name = $this->prefix . $this->option_name;
        $this->settings= isset($bluefield_api_settings['clientVars']) ? $bluefield_api_settings['clientVars'] : [];
    }

    public function register_hooks() {
        add_action('admin_menu', [$this, 'register_pages']);
        add_action('admin_init', [$this, 'register_client_var_fields']);
    }

    public function register_pages() {
        $page_identifier = BlidBaseAdmin::PAGE_IDENTIFIER;
        $manage_capability = $this->get_manage_capability();

        $submenu = add_submenu_page(
            $page_identifier,
            'Bluefield Identity: ' . __('Additional Variables', 'bluefield-identity'),
            __('Additional Variables', 'bluefield-identity'),
            $manage_capability,
            self::PAGE,
            [$this, 'show_page'],
            2
        );

        add_action( 'load-' . $submenu, [$this, 'do_admin_enqueue'] );
    }

    public function register_client_var_fields() {
        /* Front Page Options Section */
        add_settings_section(
            'blid-client-settings-section',
            __('Bluefield Identity Additional Variables (Advanced)', 'bluefield-identity'),
            [$this, 'blid_render_client_vars_description'],
            self::PAGE,
            []
        );

        add_settings_field(
            'blid-client-vars',
            null,
            [$this, 'blid_add_options'],
            self::PAGE,
            'blid-client-settings-section',
            []
        );

        register_setting('blid-client-variables-options', $this->option_name, [$this, 'validate_options_char_count']);
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

    public function get_manage_capability()
    {
        return 'manage_options';
    }

    public function show_page()
    {
        require_once BLUEFIELD_PATH . 'pages/' . self::PAGE_TEMPLATE . '.php';
    }

    public function do_admin_enqueue() {
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_css'] );
    }

    public function enqueue_admin_css() {
        wp_enqueue_style( 'bluefield-css', BLUEFIELD_PLUGIN_URL . 'resources/css/bluefield-admin.css', [], '1.0', 'all' );
    }

    /**
     * Client variable functions
     *
     *
     *
     */

    public function blid_render_client_vars_description() {
        echo '
        <p class="client-vars-descripton">Bluefield Identity allows you to pass up to 6 additional variables for your tracking and reporting purposes. You pass them to us, we record them and we pass them back in the response object.</p>
        <p>The variables can be anything present in the host website\'s Global variable scope and the maximum length of each variable value (not variable name) is 32 characters.</p>
        <p>Please note: Bluefield Identity does NOT store the name of the variable you pass us, just its value.  Our return data object will refer to them as "clientVar1", "clientVar2" and so on.  If you change a value you choose to send us from one variable to another, and you are using our return data object\'s "clientVar" values in analytics or other reporting, be advised that you will need to adjust your use of the returned data to match your change.  We do not track variable changes and are not responsible for such changes made.</p>
        <p>If you have any questions about this feature, please use the Contact Support option in your <a target="_blank" href="https://clients.bluefieldidentity.com/">Bluefield Identity Client Dashboard</a>.</p>
        ';
    }

    public function blid_add_options() {
        array_walk($this->fields, [$this, 'add_text_inputs'], $this->option_name);
    }

    /**
     * Output all the input fields for the client variables
     *
     * @param $item
     * @param $key
     * @param $option_name
     * @return void
     */
    public function add_text_inputs($item, $key, $option_name) {
        $options = BlidOptions::get_option($option_name, []);
        $value = '';
        if(isset($options)) {
            $value = isset($options[$key]) ? $options[$key] : '';
        }
        echo '
            <tr>
                <th width="75" style="width: 100px;" class="cell-width-600" scope="row">' . esc_html($item['title']) . '</th>
                <td class="cell-width-600">
                    <table class="client-vars-table">
                        <tr>
                           <td style="padding: 0; min-width: 275px;"><input style="min-width: 275px;" id="clientVars--' . esc_attr($key) . '" max="' . esc_attr($item['max']) . '" type="' . esc_attr($item['type']) . '" name="' . esc_attr($this->option_name) . '[' . esc_attr($key) . ']" placeholder="' . esc_attr($item['title']) . '" value="' . esc_attr($value) . '"></td>
                        </tr>
                    </table>
                </td>
            </tr>
    ';
    }
}
