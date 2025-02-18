<?php

namespace Plugins\LaravelSaas\Console\Commands;

use Illuminate\Console\Command;

class SaasTenantAddCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saas:tenant-add {tenant=foo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建租户';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant');

        $tenant = \App\Models\Tenant::create(['id' => $tenantId]);
        $tenant->domains()->create(['domain' => "{$tenantId}.".str_replace(['http://', 'https://'], '', config('app.url'))]);

        $this->info("{$tenantId} 创建成功");
    }
}
