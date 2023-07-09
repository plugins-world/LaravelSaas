<?php

namespace Plugins\LaravelSaas\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use MouYong\LaravelConfig\Models\Config;

class LaravelSaasController extends Controller
{
    public function index()
    {
        $configs = Config::getValueByKeys([
            // 'item_key1',
            // 'item_key2',
        ]);

        $where = [];
        if (\request()->has('is_enabled')) {
            $where['is_enabled'] = \request('is_enabled');
        }

        // $data = Tenant::query()->where($where)->get();

        return view('LaravelSaas::index', [
            'configs' => $configs,
        ]);
    }

    public function showSettingView()
    {
        $configs = Config::getValueByKeys([
            // 'item_key1',
            // 'item_key2',
        ]);

        return view('LaravelSaas::setting', [
            'configs' => $configs,
        ]);
    }

    public function saveSetting()
    {
        \request()->validate([
            // 'item_key1' => 'required|url',
            // 'item_key2' => 'nullable|url',
        ]);

        $itemKeys = [
            // 'item_key1',
            // 'item_key2',
        ];

        // Config::updateConfigs($itemKeys, 'laravel-saas');

        return redirect(route('laravel-saas.setting'));
    }
}
