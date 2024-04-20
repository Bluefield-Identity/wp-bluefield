<?php
namespace Bluefield\Includes\API;

use Bluefield\Includes\Utils\BlidPlatforms;
use Bluefield\Includes\Utils\BlidUser;
use Bluefield\Includes\Utils\BlidClientVars;
use Bluefield\Includes\Utils\BlidLogger;

class BlidFilter extends BlidBaseApi {
    const FAIL = 'Fail';
    const PASS = 'Pass';

    private $user = null;

    private $client_vars = null;

    private $logger = null;

    private $platform = null;

    private $filter_recommend = null;

    public function __construct(BlidUser $user, BlidClientVars $client_vars, BlidLogger $logger, BlidPlatforms $platform)
    {
        parent::__construct();

        $this->user = $user;
        $this->client_vars = $client_vars;
        $this->logger = $logger;
        $this->platform = $platform;
    }

    public static function fromUser(User $user) {
        return new self($user, new BlidClientVars($GLOBALS), new BlidLogger());
    }

    /**
     * Try for a filter recommendation
     *
     * @throws \Exception
     */
    public function create_recommendation() {
        $args = [
            'clientRequestDateTime' => $this->user->get_client_request_date_time(),
            'visitorRemoteAddr' => $this->user->get_remote_addr(),
            'visitorUserAgent' => $this->user->get_http_user_agent(),
            'visitorQueryString' => $this->user->get_query_string(),
            'visitorRequestAsset' => $this->user->get_visitor_request_asset(),
            'visitorAccept' => $this->user->get_http_accept(),
            'visitorAcceptEncoding' => $this->user->get_http_accept_encoding(),
            'visitorAcceptLanguage' => $this->user->get_http_accept_language(),
            'visitorReqMethod' => $this->user->get_request_method(),
            'visitorReferer' => $this->user->get_http_referrer(),
            'clientVar1' => $this->client_vars->get_client_var_as_value('clientVar1'),
            'clientVar2' => $this->client_vars->get_client_var_as_value('clientVar2'),
            'clientVar3' => $this->client_vars->get_client_var_as_value('clientVar3'),
            'clientVar4' => $this->client_vars->get_client_var_as_value('clientVar4'),
            'clientVar5' => $this->client_vars->get_client_var_as_value('clientVar5'),
            'clientVar6' => $this->client_vars->get_client_var_as_value('clientVar6'),
            'platform' => $this->platform->get_platform(),
            'major_version' => $this->platform->get_plugin_major_version(),
            'minor_version' => $this->platform->get_plugin_minor_version(),
        ];

        $response_body = null;

        try {
            $response_body = $this->send_get_request($args);
        } catch (\Exception $exception) {
            $this->handle_exception($exception);
        }

        if($response_body && isset($response_body['COLUMNS']) && isset($response_body["DATA"])) {
            $data = $response_body["DATA"];
            $cols = $response_body['COLUMNS'];

            $filter_id = $this->retrieve_filter_id($cols, $data);
            $filter_reason = $this->retrieve_filter_reason($cols, $data);
            $filter_recommend = apply_filters('blid__filter_recommend', $this->retrieve_filter_recommend($cols, $data), $filter_id, $filter_reason, $args);

            $this->filter_recommend = [
                'filter_id' => $filter_id,
                'filter_recommend' => $filter_recommend,
                'filter_reason' => $filter_reason,
                'args' => $args,
            ];
        }
    }

    public function process_recommendation() {
        $this->create_recommendation();

        if(null === $this->filter_recommend) {
            return null;
        }

        if($this->filter_recommend['filter_recommend'] && !is_wp_error($this->filter_recommend['filter_recommend'])) {
            if($this->filter_recommend['filter_recommend'] === self::FAIL) {
                $post = get_post(get_the_ID());

                /**
                 * The following adds the ability to short circuit the
                 * failure to take place. A valid return value will be a boolean.
                 */
                $pre = apply_filters('blid__pre_do_failure', false, $post, $this->filter_recommend);

                if(false !== $pre) {
                    return $pre;
                }

                $this->do_failure();
            } elseif ($this->filter_recommend['filter_recommend'] !== self::PASS) {
                $this->logger->write('Unknown filter recommendation: ' . $this->filter_recommend['filter_recommend']);
            }
        }
    }

    public function retrieve_filter_id(array $columns = null, array $data = null) {
        if($columns && $data && (($index = array_search('filterid', $columns)) !== false)) {
            return $data[0][$index];
        }

        return null;
    }

    public function retrieve_filter_recommend(array $columns = null, array $data = null) {
        if($columns && $data && (($index = array_search('filterRecommend', $columns)) !== false)) {
            return $data[0][$index];
        }

        return null;
    }

    public function retrieve_filter_reason(array $columns = null, array $data = null) {
        if($columns && $data && (($index = array_search('filterReason', $columns)) !== false)) {
            return $data[0][$index];
        }

        return null;
    }

    /**
     * Kill the page by default
     *
     * @return void
     */
    public function do_failure() {
        $string = $this->generate_failure_string();

        $this->wp_die($string, 204);
    }

    public function generate_failure_string() {
        if(!$this->filter_recommend) {
            return '';
        }

        $filter = $this->filter_recommend;
        
        return $filter['filter_id'] . ' ' . $filter['filter_recommend'] . 'Args: ' . json_encode($filter['args']);
    }

    public function wp_die(string $string = '', int $status = 204) {
        wp_die($string, $status);
    }

    public function handle_exception(\Exception $exception) {
        $this->logger->log_exception($exception);
    }
}
