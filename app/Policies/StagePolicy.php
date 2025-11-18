<?php

namespace App\Policies;

use App\Models\Stage;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StagePolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        return $user->hasPermissionTo('stage.view') || 
               $user->hasPermissionTo('stage.*');
    }

    public function view(User $user, Stage $stage): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $stage->tenant_id) {
            return false;
        }

        // Check role-based permissions
        if ($user->role === 'client') {
            // Clients can only view stages with tasks assigned to them
            return $stage->tasks()
                ->where('responsible_id', $user->id)
                ->exists();
        }

        return $user->hasPermissionTo('stage.view') || 
               $user->hasPermissionTo('stage.*');
    }

    public function create(User $user): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        return in_array($user->role, ['admin']) && (
            $user->hasPermissionTo('stage.create') || 
            $user->hasPermissionTo('stage.*')
        );
    }

    public function update(User $user, Stage $stage): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $stage->tenant_id) {
            return false;
        }

        // Responsible users can update their own stages
        if ($stage->responsible_id === $user->id && $user->role === 'operator') {
            return $user->hasPermissionTo('stage.edit') || 
                   $user->hasPermissionTo('stage.*');
        }

        return in_array($user->role, ['admin']) && (
            $user->hasPermissionTo('stage.edit') || 
            $user->hasPermissionTo('stage.*')
        );
    }

    public function delete(User $user, Stage $stage): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $stage->tenant_id) {
            return false;
        }

        return in_array($user->role, ['admin']) && (
            $user->hasPermissionTo('stage.delete') || 
            $user->hasPermissionTo('stage.*')
        );
    }

    public function start(User $user, Stage $stage): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $stage->tenant_id) {
            return false;
        }

        // Responsible users can start their own stages
        if ($stage->responsible_id === $user->id) {
            return $user->hasPermissionTo('stage.start') || 
                   $user->hasPermissionTo('stage.*');
        }

        return in_array($user->role, ['admin', 'operator']) && (
            $user->hasPermissionTo('stage.start') || 
            $user->hasPermissionTo('stage.*')
        );
    }

    public function pause(User $user, Stage $stage): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $stage->tenant_id) {
            return false;
        }

        // Responsible users can pause their own stages
        if ($stage->responsible_id === $user->id) {
            return $user->hasPermissionTo('stage.pause') || 
                   $user->hasPermissionTo('stage.*');
        }

        return in_array($user->role, ['admin', 'operator']) && (
            $user->hasPermissionTo('stage.pause') || 
            $user->hasPermissionTo('stage.*')
        );
    }

    public function resume(User $user, Stage $stage): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $stage->tenant_id) {
            return false;
        }

        // Responsible users can resume their own stages
        if ($stage->responsible_id === $user->id) {
            return $user->hasPermissionTo('stage.resume') || 
                   $user->hasPermissionTo('stage.*');
        }

        return in_array($user->role, ['admin', 'operator']) && (
            $user->hasPermissionTo('stage.resume') || 
            $user->hasPermissionTo('stage.*')
        );
    }

    public function complete(User $user, Stage $stage): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $stage->tenant_id) {
            return false;
        }

        // Responsible users can complete their own stages
        if ($stage->responsible_id === $user->id) {
            return $user->hasPermissionTo('stage.complete') || 
                   $user->hasPermissionTo('stage.*');
        }

        return in_array($user->role, ['admin']) && (
            $user->hasPermissionTo('stage.complete') || 
            $user->hasPermissionTo('stage.*')
        );
    }

    public function review(User $user, Stage $stage): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $stage->tenant_id) {
            return false;
        }

        return in_array($user->role, ['admin']) && (
            $user->hasPermissionTo('stage.review') || 
            $user->hasPermissionTo('stage.*')
        );
    }

    public function restore(User $user, Stage $stage): bool
    {
        return $this->update($user, $stage);
    }

    public function forceDelete(User $user, Stage $stage): bool
    {
        return $user->isSuperuser();
    }
}