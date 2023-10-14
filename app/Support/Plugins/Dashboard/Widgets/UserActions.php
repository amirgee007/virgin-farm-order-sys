<?php

namespace Vanguard\Support\Plugins\Dashboard\Widgets;

use Vanguard\Plugins\Widget;
use Vanguard\User;

class UserActions extends Widget
{
    /**
     * UserActions constructor.
     */
    public function __construct()
    {
        $this->permissions(function (User $user) {
            return $user->hasRole('Client');
        });
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return view('plugins.dashboard.widgets.user-actions');
    }
}
