<?php
namespace Bluefield\Includes\Utils;

class BlidLogger {
    public function log_exception(\Exception $exception, $with_trace = true) {
       $error_message = $this->get_exception_message($exception);
       if ($with_trace) {
           $error_trace = $this->get_exception_trace($exception);
       }

       $full_error_log = $error_message . "\n" . $error_trace;

       $this->write($full_error_log);
    }

    public function get_exception_message(\Exception $exception) {
        $message = $exception->getMessage();
        if(null !== $message) {
            return $message;
        }

        return 'Uknown Exception.';
    }

    public function get_exception_trace($exception) {
        $trace = $exception->getTraceAsString();
        if(null !== $trace) {
            return $trace;
        }

        return 'Uknown Trace.';
    }

    public function write(string $error_log = null) {
        if($error_log) {
            error_log($error_log);
        }
    }

    public function print_error($error_log = null) {
        if($error_log) {
            error_log(print_r($error_log, true));
        }
    }
}
