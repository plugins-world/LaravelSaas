<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\LaravelSaas\Http\Controllers as WebController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('laravel-saas')->group(function() {
    Route::get('/', [WebController\LaravelSaasController::class, 'index'])->name('laravel-saas.index');
    Route::get('setting', [WebController\LaravelSaasController::class, 'showSettingView'])->name('laravel-saas.setting');
    Route::post('setting', [WebController\LaravelSaasController::class, 'saveSetting']);
});

// without VerifyCsrfToken
// Route::prefix('laravel-saas')->withoutMiddleware([
//     \App\Http\Middleware\EncryptCookies::class,
//     \App\Http\Middleware\VerifyCsrfToken::class,
// ])->group(function() {
//     Route::get('/', [WebController\LaravelSaasController::class, 'index']);
// });
