<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Schema;
use MouYong\LaravelConfig\Models\Config;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    protected $fresnsWordBody = [
        // 'type' => SubscribeUtility::TYPE_USER_ACTIVITY,
        // 'fskey' => 'laravel-saas',
        // 'cmdWord' => 'stats',
    ];

    protected $fresnsConfigItems = [
        [
            'item_tag' => 'laravel-saas',
            'item_key' => 'tenant_user_register_service',
            'item_type' => 'string',
            'item_value' => 'LaravelSaas',
        ],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // addSubscribeItem
        // \FresnsCmdWord::plugin()->addSubscribeItem($this->fresnsWordBody);

        // addKeyValues to Config table
        Config::addKeyValues($this->fresnsConfigItems);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // removeSubscribeItem
        // \FresnsCmdWord::plugin()->removeSubscribeItem($this->fresnsWordBody);

        // removeKeyValues from Config table
        Config::removeKeyValues($this->fresnsConfigItems);
    }
};
