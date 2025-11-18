<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TenantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperuser();
    }

    public function view(User $user, Tenant $tenant): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Users can only view their own tenant
        return $user->tenant_id === $tenant->id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperuser();
    }

    public function update(User $user, Tenant $tenant): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Tenant admins can update their own tenant
        return $user->tenant_id === $tenant->id && $user->role === 'admin';
    }

    public function delete(User $user, Tenant $tenant): bool
    {
        return $user->isSuperuser();
    }

    public function restore(User $user, Tenant $tenant): bool
    {
        return $user->isSuperuser();
    }

    public function forceDelete(User $user, Tenant $tenant): bool
    {
        return $user->isSuperuser();
    }
}