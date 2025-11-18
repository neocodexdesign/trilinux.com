<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Events\TenancyInitialized;

class LogTenancyInitialized
{
    public function handle(TenancyInitialized $event): void
    {
        Log::info('✅ Tenancy initialized', [
            'tenant_id' => $event->tenancy->tenant->id,
            'url' => request()->fullUrl(),
        ]);
    }
}