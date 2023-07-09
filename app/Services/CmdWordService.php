<?php

namespace Plugins\LaravelSaas\Services;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

class CmdWordService
{
    public static function addTenant($wordBody)
    {
        \info(__CLASS__ . '::' . __FUNCTION__, $wordBody);

        $user = $wordBody['user'];
        $tenantId = uniqid();

        Artisan::call('saas:tenant-add', [
            'tenant' => $tenantId,
        ]);

        $tenant = Tenant::find($tenantId);
        $tenant?->update([
            'name' => $user['name'],
            'email' => $user['email'],
        ]);

        $userModel = User::where('name', $user['name'])->firstOrFail();

        $tenant?->run(function () use ($userModel) {
            User::create([
                'name' => $userModel->name,
                'password' => $userModel->password,
                'email' => $userModel->email,
            ]);
        });

        return redirect('http://'.$tenantId.'.translate-merit-based.hecs.iwnweb.com');
    }
}
