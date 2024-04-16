<?php
namespace Bluefield\Includes\Utils;

class BlidUser {
    private $client_request_date_time = null;

    private $server_data = [];

    public function __construct($server_data = []) {
        $this->client_request_date_time = gmdate('Y-m-d H:i:s.u');
        $this->server_data = $server_data;
    }

    /**
     * Get the time at which the request is made
     * 
     * current format: Y-m-d H:i:s.u
     */
    public function get_client_request_date_time() {
        return $this->client_request_date_time;
    }

    /**
     * Get server data with a given key
     *
     * @param string $key
     * @param $default
     * @return mixed|null
     */
    public function get_server_value(string $key, $default = '') {
        return $this->server_data[$key] ?? $default;
    }

    /**
     * Get the visitors IP Address
     *
     * @return mixed
     */
    public function get_remote_addr($default = '') {
        return $this->get_server_value('REMOTE_ADDR', $default);
    }

    /**
     * Get the users browser
     *
     * @return mixed
     */
    public function get_http_user_agent($default = '') {
        return $this->get_server_value('HTTP_USER_AGENT', $default);
    }

    /**
     * Get the query string
     *
     * @return mixed
     */
    public function get_query_string($default = '') {
        return $this->get_server_value('QUERY_STRING', $default);
    }

    /**
     * refers to the URL as it is given in the actual HTTP request
     *
     * @return string|mixed
     */
    public function get_visitor_request_asset($default = '') {
        return $this->get_server_value('REQUEST_URI', $default);
    }

    /**
     * Returns the contents of the host header i.e. example.com
     *
     * @return mixed
     */
    public function get_http_host($default = '') {
        return $this->get_server_value('HTTP_HOST', $default);
    }

    /**
     * Represents the Accept Header
     *
     * @return mixed
     */
    public function get_http_accept($default = '') {
        return $this->get_server_value('HTTP_ACCEPT', $default);
    }

    /**
     * Typically compression methods that the client can process i.e. gzip
     *
     * @return mixed
     */
    public function get_http_accept_encoding($default = '') {
        return $this->get_server_value('HTTP_ACCEPT_ENCODING', $default);
    }

    /**
     * Indicates what language preferences i.e. en-US
     *
     * @return mixed
     */
    public function get_http_accept_language($default = '') {
        return $this->get_server_value('HTTP_ACCEPT_LANGUAGE', $default);
    }

    /**
     * Represents the request method used to access the page, i.e. 'POST', 'GET' etc..
     *
     * @return mixed
     */
    public function get_request_method($default = '') {
        return $this->get_server_value('REQUEST_METHOD', $default);
    }

    /**
     * Represents the Accept-Encoding header
     *
     * @return mixed
     */
    public function get_http_referrer($default = '') {
        return $this->get_server_value('HTTP_REFERER', $default);
    }

    /**
     * @return array|mixed
     */
    public function get_server_data() {
        return $this->server_data;
    }
}
