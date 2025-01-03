<?php

namespace Plugins\LaravelSaas\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class SaasInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saas:install
        {--domain= : saas domain, add into config/tenancy.php, such as saas.test}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '初始化 stancl/tenancy 3.x: https://tenancyforlaravel.com/docs/v3/quickstart';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('tenancy:install');

        $this->initTenantMigrations();
        $this->initTenantPublicAssets();
        $this->setTenantPrefix();
        // $this->registerDomains($this->option('domain')); # 已经通过 setTenantPrefix 初始化了
        $this->addInitTenantAction();
        $this->resetRegisterPasswordLength();
        $this->createTenantMigrationsDir();
        $this->resetLoginLogic();
        $this->registerInteriaFlashMessage();
        $this->registerProvider();
        $this->registerRoutes();

        $this->call('migrate');
        $this->call('tenants:migrate');
    }

    /**
     * Replace a given string within a given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $path
     * @return void
     */
    protected function replaceInFile($search, $replace, $path)
    {
        if (!is_file($path)) {
            return;
        }

        $content = file_get_contents($path);
        if (! str_contains($content, $replace)) {
            file_put_contents($path, str_replace($search, $replace, $content));
        }
    }

    /**
     * Install the provider in the plugin.json file.
     *
     * @param  string  $after
     * @param  string  $name
     * @param  string  $group
     */
    protected function installPluginProviderAfter(string $after, string $name, string $appConfigPath): void
    {
        $appConfig = file_get_contents($appConfigPath);

        $providers = Str::before(Str::after($appConfig, '\'providers\' => ServiceProvider::defaultProviders()->merge(['), sprintf('])->toArray(),', PHP_EOL));

        if (! Str::contains($providers, $name)) {
            $modifiedProviders = str_replace(
                sprintf('%s,', $after),
                sprintf('%s,', $after).PHP_EOL.'        '.sprintf('%s', $name),
                $providers,
            );

            $this->replaceInFile(
                $providers,
                $modifiedProviders,
                $appConfigPath,
            );
        }
    }

    /**
     * Install the provider in the plugin.json file.
     *
     * @param  string  $after
     * @param  string  $name
     * @param  string  $group
     */
    protected function installBuildCommandAfter(string $after, string $name, string $packageJsonPath): void
    {
        $packageJson = file_get_contents($packageJsonPath);

        $scripts = Str::before(Str::after($packageJson, '"build": "'), sprintf('"', PHP_EOL));

        if (! Str::contains($scripts, $name)) {
            $modifiedScripts = preg_replace(
                sprintf('/"?%s/', $after),
                sprintf('%s', $after).' && '.sprintf('%s', $name),
                $scripts,
                1,
            );

            $this->replaceInFile(
                $scripts,
                $modifiedScripts,
                $packageJsonPath,
            );
        }
    }

    public function registerProvider()
    {
        $this->installPluginProviderAfter('App\Providers\RouteServiceProvider::class', 'App\Providers\TenancyServiceProvider::class, // <-- here', config_path('app.php'));
    }

    public function registerRoutes()
    {
        if (! is_file($tenantSessionMiddlewareFile = app_path('Http/Middleware/AuthenticateTenantSession.php'))) {
            copy(__DIR__.'/stubs/AuthenticateTenantSession.stub', $tenantSessionMiddlewareFile);
        }
        
        $this->replaceInFile(<<<'TXT'
        Route::middleware([
            'web',
            InitializeTenancyByDomain::class,
            PreventAccessFromCentralDomains::class,
        ])->group(function () {
            Route::get('/', function () {
                return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
            });
        });
        TXT,
        <<<'TXT'
        \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::$onFail = function ($exception, $request, $next) {
            return redirect(config('app.url'));
        };

        Route::middleware([
            'web',
            InitializeTenancyByDomain::class,
            PreventAccessFromCentralDomains::class,
        ])->group(function () {
            // Route::get('/', function () {
            //     return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
            // });

            require __DIR__.'/web.php';
        });
        TXT, base_path('routes/tenant.php'));

        $this->replaceInFile(<<<'TXT'
            public function boot(): void
            {
                RateLimiter::for('api', function (Request $request) {
                    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
                });
        
                $this->routes(function () {
                    Route::middleware('api')
                        ->prefix('api')
                        ->group(base_path('routes/api.php'));
        
                    Route::middleware('web')
                        ->group(base_path('routes/web.php'));
                });
            }
        TXT,
        <<<'TXT'
            public function boot(): void
            {
                RateLimiter::for('api', function (Request $request) {
                    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
                });
        
                $this->routes(function () {
                    foreach ($this->centralDomains() as $domain) {
                        Route::middleware(['api', \App\Http\Middleware\AuthenticateTenantSession::class])
                            ->domain($domain)
                            ->prefix('api')
                            ->group(base_path('routes/api.php'));
        
                        Route::middleware(['web', \App\Http\Middleware\AuthenticateTenantSession::class])
                            ->domain($domain)
                            ->group(base_path('routes/web.php'));
                    }
                });
            }

            protected function centralDomains(): array
            {
                return config('tenancy.central_domains');
            }
        TXT, app_path('Providers/RouteServiceProvider.php'));
    }

    public function registerDomains($domain = null)
    {
        if (! $domain) {
            $urlInfo = parse_url(config('app.url'));

            $domain = $urlInfo['host'];
        }

        if (! $domain) {
            return;
        }

        $this->replaceInFile(<<<'TXT'
            'central_domains' => [
                '127.0.0.1',
                'localhost',
            ],
        TXT,
        <<<'TXT'
            'central_domains' => [
                '$domain',
                '127.0.0.1',
                'localhost',
            ],
        TXT, config_path('tenancy.php'));
    }

    public function createTenantMigrationsDir()
    {
        $path = database_path('migrations/tenant');

        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
            @touch($path.'/.gitkeep');
        }
    }

    public function setTenantPrefix()
    {
        $content = file_get_contents($filePath = config_path('tenancy.php'));

        if (str_contains($content, "url_override")) {
            return;
        }

        $newContent = str_replace(
            [
                "use Stancl\Tenancy\Database\Models\Tenant;\n\nreturn [",
                "'tenant_model' => Tenant::class,",
                "'localhost',\n    ],",
                "'prefix' => 'tenant',",
                "'suffix_base' => 'tenant',",
                "_base' => 'tenant',",
                "// '--force' => true,",
                "'public' => '%storage_path%/app/public/',\n        ],",
            ],
            [
                "use Stancl\Tenancy\Database\Models\Tenant;\n\n\$prefix = env('DB_DATABASE') . '_';\n\nreturn [",
                "'tenant_model' => \App\Models\Tenant::class,",
                "'localhost',\n\t\tstr_replace(['http://', 'https://'], '', trim(env('APP_URL', ''), '/')),\n    ],",
                "'prefix' => \$prefix,",
                "'suffix_base' => \"tenants/\".\$prefix,",
                "_base' => \$prefix,",
                "'--force' => true,",
                "'public' => '%storage_path%/app/public/',
        ],

        /*
        * Use this to support Storage url method on local driver disks.
        * You should create a symbolic link which points to the public directory using command: artisan tenants:link
        * Then you can use tenant aware Storage url: Storage::disk('public')->url('file.jpg')
        *
        * See https://github.com/archtechx/tenancy/pull/689
        */
        'url_override' => [
            // The array key is local disk (must exist in root_override) and value is public directory (%tenant_id% will be replaced with actual tenant id).
            'public' => 'tenants/public-%tenant_id%',
        ],",
            ],
            $content
        );
        file_put_contents($filePath, $newContent);
    }

    public function addInitTenantAction()
    {
        $tenantModelFile = app_path('Models/Tenant.php');

        if (file_exists($tenantModelFile)) {
            return;
        }

        copy(__DIR__.'/stubs/tenant_model.stub', app_path('Models/Tenant.php'));
        
        $content = file_get_contents($filePath = base_path('app/Providers/TenancyServiceProvider.php'));

        $newContent = str_replace(
            [
                "// Jobs\SeedDatabase::class,",
                "send(function (Events\TenantCreated \$event) {
                    return \$event->tenant;
                })",
                "send(function (Events\TenantDeleted \$event) {
                    return \$event->tenant;
                })",
            ],
            [
                "Jobs\SeedDatabase::class,",
                "send(function (Events\TenantCreated \$event) {
                    \App\Models\Tenant::createStorageLink(\$event->tenant); // <-- here.
                    return \$event->tenant;
                })",
                "send(function (Events\TenantDeleted \$event) {
                    \App\Models\Tenant::removeStorageLink(\$event->tenant); // <-- here.
                    return \$event->tenant;
                })",
            ],
            $content
        );
        file_put_contents($filePath, $newContent);
    }

    public function resetRegisterPasswordLength()
    {
        if (! class_exists(\App\Http\Controllers\Auth\RegisteredUserController::class)) {
            return;
        }

        $this->replaceInFile('Rules\Password::defaults()', "'min:6'", app_path('Http/Controllers/Auth/RegisteredUserController.php'));
    }

    public function initTenantMigrations()
    {
        $path = database_path('migrations/tenant');

        $files = glob(database_path('migrations/*create_users_table*'));
        $createUsersTableMigration = $files ? $files[0] : null;
        if (! $createUsersTableMigration) {
            return;
        }

        $migrationFile = basename($createUsersTableMigration);
        $createUsersTableMigrationWithTenant = database_path("migrations/tenant/{$migrationFile}");

        if (! is_file($createUsersTableMigrationWithTenant)) {
            copy($createUsersTableMigration, $createUsersTableMigrationWithTenant);
        }

        if (file_exists($path.'/.gitkeep')) {
            unlink($path.'/.gitkeep');
        }
    }

    public function initTenantPublicAssets()
    {
        if (is_dir(base_path('public/build')) && is_dir(base_path('public/tenancy/assets'))) {
            $this->installBuildCommandAfter('vite build', 'cp -r public/build public/tenancy/assets/build', base_path('package.json'));
        }

        tap(new Filesystem, function ($files) {
            $path = public_path('tenancy/assets');
            $files->ensureDirectoryExists($path);

            // build assets
            $build = public_path('build');
            $buildWithTenancy = public_path('tenancy/assets/build');

            $files->copyDirectory($build, $buildWithTenancy);
        });


    }

    public function resetLoginLogic()
    {
        $this->replaceInFile(<<<'TXT'
            public function create(): Response
            {
                return Inertia::render('Auth/Login', [
                    'canResetPassword' => Route::has('password.request'),
                    'status' => session('status'),
                ]);
            }
        TXT,
        <<<'TXT'
            public function create(): Response|RedirectResponse
            {
                if ($encryptData = \request('encryptData')) {
                    $data = decrypt($encryptData);
                    $userArray = $data['user'] ?? null;
                    $user = \App\Models\User::where('name', $userArray['name'])->first();
                    auth()->login($user);
        
                    return redirect('/login');
                }
        
                return Inertia::render('Auth/Login', [
                    'canResetPassword' => Route::has('password.request'),
                    'status' => session('status'),
                ]);
            }
        TXT, app_path('Http/Controllers/Auth/AuthenticatedSessionController.php'));
    }

    public function registerInteriaFlashMessage()
    {
        $this->replaceInFile(<<<'TXT'
            public function share(Request $request): array
            {
                return array_merge(parent::share($request), [
                    'auth' => [
                        'user' => $request->user(),
                    ],
                    'ziggy' => function () use ($request) {
                        return array_merge((new Ziggy)->toArray(), [
                            'location' => $request->url(),
                        ]);
                    },
                ]);
            }
        TXT,
        <<<'TXT'
            public function share(Request $request): array
            {
                return array_merge(parent::share($request), [
                    'auth' => [
                        'user' => $request->user(),
                    ],
                    'ziggy' => function () use ($request) {
                        return array_merge((new Ziggy)->toArray(), [
                            'location' => $request->url(),
                        ]);
                    },
                    'flash' => session()->all(),
                ]);
            }
        TXT, app_path('Http/Middleware/HandleInertiaRequests.php'));
    }
}
