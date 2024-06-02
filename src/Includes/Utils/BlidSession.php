<?php
namespace Bluefield\Includes\Utils;

use Bluefield\Includes\Utils\BlidSanitize;

class BlidSession {
    /**
     * The name of the cookie
     *
     * @var mixed|void
     */
    protected $_cookie;


    public function __construct() {
        $this->_cookie = apply_filters('bluefield_cookie', 'wp_bluefield_session');

        $this->init_session_cookie();
    }

    public function generate_random_string($string_len = 10) {
        $characters = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'));
        $characters_length = count($characters);
        $random_string = '';

        for ($i = 0; $i < $string_len; $i++) {
            $index = random_int(0, $characters_length - 1);
            $random_string .= $characters[$index];
        }

        return $random_string;
    }

    public function init_session_cookie() {
        if(!$this->get_session_cookie()) {
            $this->set_cookie($this->_cookie, $this->generate_random_string());
        }
    }

    public function get_session_cookie() {
        $cookie_value = isset( $_COOKIE[ $this->_cookie ] ) ? wp_unslash( BlidSanitize::sanitize_cookie($_COOKIE[ $this->_cookie ]) ) : false;

        if(empty($cookie_value) || ! is_string($cookie_value)) {
            return false;
        }

        return $cookie_value;
    }

    /**
     * Set the Bluefield Cookie
     *
     * @see https://www.php.net/manual/en/function.setcookie.php
     *
     * @param $name - string
     * @param $value - string
     * @return void
     */
    public function set_cookie($name = '', $value = '', $expire = 0, $secure = false, $httponly = false) {
        setcookie($name, $value, array(
            'expires'  => $expire,
            'secure'   => $secure,
            'path'     => COOKIEPATH ? COOKIEPATH : '/',
            'domain'   => COOKIE_DOMAIN,
            'httponly' => $httponly
        ));
    }
}
