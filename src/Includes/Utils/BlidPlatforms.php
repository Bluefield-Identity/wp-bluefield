<?php
namespace Bluefield\Includes\Utils;

use Bluefield\Includes\Utils\BlidLogger;

class BlidPlatforms {
    const PLATFORM = 'WordPress';

    public function get_platform() {
        return self::PLATFORM;
    }

    public function get_plugin_version() {
        return $this->plugin_file_version(BLUEFIELD);
    }

    public function get_plugin_major_version() {
        return $this->get_major_version($this->get_plugin_version());
    }

    public function get_plugin_minor_version() {
        return $this->get_minor_version($this->get_plugin_version());
    }

    public function get_major_version(string $full_version = null) {
        $major_verson = null;

        if($full_version) {
            $last_dot = strrpos($full_version, '.');

            if(false !== $last_dot) {
                $major_verson = substr($full_version, 0, $last_dot);
            }
        }

        return $major_verson;
    }

    public function get_minor_version(string $full_version = null) {
        $minor_verson = null;

        if($full_version) {
            $last_dot = strrpos($full_version, '.') + 1;

            if(false !== $last_dot) {
                $minor_verson = substr($full_version, $last_dot);
            }
        }

        return $minor_verson;
    }

    public function plugin_file_version($plugin_file = null) {
        if(plugin_dir_path($plugin_file)) {
            $version = get_file_data( $plugin_file, ['Version' => 'Version'], 'plugin' );

            return $version['Version'];
        }

        return null;
    }
}
