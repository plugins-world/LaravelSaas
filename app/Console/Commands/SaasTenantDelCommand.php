<?php

namespace Plugins\LaravelSaas\Console\Commands;

use Illuminate\Console\Command;

class SaasTenantDelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saas:tenant-del {tenant=foo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '删除租户';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant');

        $tenant = \App\Models\Tenant::find($tenantId);
        $tenant?->delete();

        $this->info("{$tenantId} 删除成功");
    }
}
