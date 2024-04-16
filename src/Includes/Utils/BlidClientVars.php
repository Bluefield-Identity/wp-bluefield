<?php
/**
 * ClientVars
 */
namespace Bluefield\Includes\Utils;

use Bluefield\Options\BlidOptions;

class BlidClientVars {
    private $allowed_superglobals = [
        'GET',
        'POST',
        'REQUEST',
        'SERVER',
// Listed here just for documentation on options, can delete if want.
//        'COOKIE',
//        'FILE',
//        'ENV'
    ];

    /**
     * Schema is set in Bluefield\IncludesMenu\BlidClientGroup
     *
     * @var array - client vars come from the database
     */
    public  $client_vars_from_options = [];

    public $superglobal = [];

    public function __construct(array $superglobal = null) {
        $this->superglobal = $superglobal;

        $this->set_client_vars();
    }

    /**
     * From a specific global
     *
     * @param array $parmas
     * @return ClientVars
     */
    public static function fromGlobal(array $superglobal) {
        return new self($superglobal);
    }

    /**
     * Getting the data for each dynamic variable set in the admin settings
     *
     * @param $value
     * @param $default
     * @return mixed|string
     */
    public function get_variable($key, $default = "") {
        if($this->superglobal) {
            return isset($this->superglobal[$key]) ? sanitize_text_field($this->superglobal[$key]) : $default;
        } elseif(null === $this->superglobal) {
            foreach($this->allowed_superglobals as $superglobal) {
                $glob_key = '_' . $superglobal;
                if(is_array($GLOBALS[$glob_key]) && isset($GLOBALS[$glob_key][$key])) {
                    return sanitize_text_field($GLOBALS[$glob_key][$key]);
                }
            }
        }

        return $default;
    }

    /**
     * Set the client vars by looping through the client_vars option in the WP database
     *
     * @return void
     */
    public function set_client_vars() {
        $client_vars_options = $this->get_client_var_keys_from_options();

        if (isset($client_vars_options) && is_array($client_vars_options)) {
            if (isset($client_vars_options)) {
                foreach ($client_vars_options as $option => $key) {
                    if (!empty($key)) {
                        $api_settings['clientVars'][$option] = [
                            $key => $this->get_variable($key)
                        ];
                    }
                }

                if (isset($api_settings['clientVars'])) {
                    $this->client_vars_from_options = $api_settings['clientVars'];
                }
            }
        }
    }

    /**
     * Get the client vars as a value
     *
     * @param string|null $key - ClientVar1, ClientVar2, etc.
     * @return mixed|array
     */
    public function get_client_vars_as_value(?string $key = null, $default = '') {
        if($clientVar = $this->client_vars_from_options) {
            if (null !== $key && isset($clientVar[$key]) && is_array($clientVar[$key])) {
                return array_values($clientVar[$key])[0];
            }
        }

        return $default;
    }

    public function get_client_var_keys_from_options() {
        $api_settings = BlidOptions::get_option('api_settings', []);

        return $api_settings['clientVars'] ?? null;
    }

    public function get_client_var_values() {
        return $this->client_vars_from_options;
    }
}
