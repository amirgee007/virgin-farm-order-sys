<?php

namespace Vanguard\Support\Plugins;

use Vanguard\Plugins\Plugin;
use Vanguard\Support\Sidebar\Item;

class Users extends Plugin
{
    public function sidebar()
    {
        return Item::create(__('Client/Admin Users'))
            ->route('users.index')
            ->icon('fas fa-users')
            ->active("users*")
            ->permissions('users.manage');
    }
}
