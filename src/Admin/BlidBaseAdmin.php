<?php
namespace Bluefield\Admin;

use Bluefield\Admin\Menu\BlidAdminMenu;
use Bluefield\Admin\Menu\BlidClientGroup;

class BlidBaseAdmin {
    const PAGE_IDENTIFIER = 'bluefield_identity';

    public function __construct() {
        $admin_menu = new BlidAdminMenu();
        $admin_menu->register_hooks();

        $client_group = new BlidClientGroup();
        $client_group->register_hooks();
    }
}
