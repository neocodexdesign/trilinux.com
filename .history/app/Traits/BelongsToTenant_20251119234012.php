<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Stancl\Tenancy\Facades\Tenancy;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        // Automatically set tenant_id when creating
        static::creating(function ($model) {
            if ($model->tenant_id) {
                return;
            }

            $tenant = Tenancy::getTenant();
            if ($tenant) {
                $model->tenant_id = $tenant->getTenantKey();
                return;
            }

            if (auth()->check() && auth()->user()->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });

        // Apply global scope to filter by tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenant = tenant();
            if ($tenant) {
                $builder->where('tenant_id', $tenant->getTenantKey());
                return;
            }

            if (auth()->check() && auth()->user()->tenant_id && !auth()->user()->isSuperuser()) {
                $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }
}