<?php
namespace Bluefield\Includes\API;

class BlidBaseApi {
    const METHODS = ['GET'];

    const BASE_API_URL = 'https://webservices.bluefieldidentity.com';

    private $url = null;

    private $api_settings = [];

    private $remote_key = null;

    private $password = null;

    public $request= null;

    public $body = null;

    public $response = null;

    public function __construct() {
        global $bluefield_api_settings;
        $this->api_settings = $bluefield_api_settings ?? [];

        $this->set_remote_key();
        $this->set_password();
        $this->set_url();
    }

    private function set_remote_key() {
        if (is_array($this->api_settings) && array_key_exists('remote_key', $this->api_settings)) {
            $this->remote_key = $this->api_settings['remote_key'] ?? null;
        }
    }

    private function set_password() {
        if (is_array($this->api_settings) && array_key_exists('account_password', $this->api_settings)) {
            $this->password = $this->api_settings['account_password'] ?? null;
        }
    }

    public function set_url() {
        if(self::BASE_API_URL) {
            $this->url = self::BASE_API_URL;
        } else {
            throw new \Exception("Missing the Base API Url");
        }
    }

    /**
     * Base API request
     *
     * @param array $data
     * @return mixed|\WP_Error
     */
    public function send_get_request(array $user_data = [], $content_type = null) {
        if(self::METHODS && !in_array('GET', self::METHODS)) {
            throw new \Exception("GET is not a valid type of request.");
        }
        
        $remote_key = $this->get_remote_key();
        $account_password = $this->get_password();
        $url = $this->get_base_api_url();

        //A little redundant i know
        if(!$remote_key) {
            throw new \Exception("Missing remote key.");
        }

        if(!$account_password) {
            throw new \Exception("Missing account password.");
        }

        $this->body = array_merge([
            'clientRemoteKey' => $remote_key,
            'clientRemotePassword' => $account_password,
            'clientRequestDateTime' => '',
            'visitorRemoteAddr' => '',
            'visitorUserAgent' => '',
            'visitorQueryString' => '',
            'visitorRequestAsset' => '',
            'visitorAccept' => '',
            'visitorAcceptEncoding' => '',
            'visitorAcceptLanguage' => '',
            'visitorReqMethod' => '',
            'visitorReferer' => '',
            'clientVar1' => '',
            'clientVar2' => '',
            'clientVar3' => '',
            'clientVar4' => '',
            'clientVar5' => '',
            'clientVar6' => '',
            'platform' => '',
            'major_version' => '',
            'minor_version' => '',
        ], $user_data);

        $url = "{$url}?" . http_build_query($this->body);

        $response = $this->wp_remote_get($url);
        
        if (is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code( $response );
        if($response_code !== 200) {
            throw new \Exception("Unexpected response code: {$response_code}");
        }

        $response_body = json_decode(wp_remote_retrieve_body($response), true);

        set_transient('bluefield-filter-cache', [
            'response' => $response_body,
        ], 10 * MINUTE_IN_SECONDS);

        if (in_array("MESSAGE", $response_body)) {
            throw new \Exception("MESSAGE " . $response_body[1]);
        }
        if (!array_key_exists('DATA', $response_body) || !count($response_body['DATA'])) {
            throw new \Exception("The DATA does not exist or is empty.");
        }

        return $response_body;
    }

    public function get_remote_key() {
        if(!$this->remote_key) {
            throw new \Exception("Missing Bluefield Remote Key.");
        }

        return $this->remote_key;
    }

    public function get_password() {
        if(!$this->password) {
            throw new \Exception("Missing Bluefield Account Password.");
        }

        return $this->password;
    }

    public function get_base_api_url() {
        $url = $this->url ?? self::BASE_API_URL;
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    public function wp_remote_get($url) {
        if(!$url) {
            throw new \Exception("Missing request URL.");
        }

        return wp_remote_get($url);
    }
}
