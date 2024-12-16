<?php

namespace Plugins\LaravelSaas\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use MouYong\LaravelConfig\Models\Config;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddTenant
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        // 
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $configs = Config::getValueByKeys([
            'tenant_user_register_service',
        ]);

        $plugin = $configs['tenant_user_register_service'] ?? 'LaravelSaas';

        \FresnsCmdWord::plugin($plugin)->addTenant([
            'user' => $event->user?->toArray(),
        ]);
    }
}
