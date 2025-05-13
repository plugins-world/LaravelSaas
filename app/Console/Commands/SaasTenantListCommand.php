<?php

namespace Plugins\LaravelSaas\Console\Commands;

use Illuminate\Console\Command;

class SaasTenantListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saas:tenant-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List tenants.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('tenants:list');
    }
}
