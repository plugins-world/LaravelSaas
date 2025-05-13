<?php

namespace Plugins\LaravelSaas\Console\Commands;

use Illuminate\Console\Command;

class SaasTenantStorageLinkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saas:tenant-storage-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '修复租户软链接目录错误问题';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach (glob(public_path('public-*')) as $link) {
            \Illuminate\Support\Facades\File::delete($link);
        }
        
        \App\Models\Tenant::all()->runForEach(function ($tenant) {
            $target = base_path(sprintf("storage/%s%s/app/public",
                        config('tenancy.filesystem.suffix_base'),
                        $tenant->id));
            $link = str_replace('%tenant_id%', $tenant->id, config('tenancy.filesystem.url_override.public', 'public-%tenant_id%'));

            $message = "fix storage:link $link ========> $target";
            dump($message);
            
            chdir(public_path());
            \Illuminate\Support\Facades\File::ensureDirectoryExists(dirname($target));
            \Illuminate\Support\Facades\File::link($target, $link);
        });
        
        return 0;
    }
}
