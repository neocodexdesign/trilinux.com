<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        // Superusers can see everything
        if ($user->isSuperuser()) {
            return true;
        }

        // Tenant admins can see everything in their tenant
        if ($user->role === 'admin' && $user->tenant_id) {
            return true;
        }

        // Check specific permissions
        return $user->hasPermissionTo('project.view') || 
               $user->hasPermissionTo('project.*');
    }

    public function view(User $user, Project $project): bool
    {
        // Superusers can see everything
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access - users can only see projects from their tenant
        if ($user->tenant_id !== $project->tenant_id) {
            return false;
        }

        // Tenant admins can see everything in their tenant
        if ($user->role === 'admin') {
            return true;
        }

        // Check role-based permissions
        if ($user->role === 'client') {
            // Clients can only view projects they are involved in
            return $project->stages()
                ->whereHas('tasks', function ($query) use ($user) {
                    $query->where('responsible_id', $user->id);
                })
                ->exists();
        }

        return $user->hasPermissionTo('project.view') || 
               $user->hasPermissionTo('project.*');
    }

    public function create(User $user): bool
    {
        // Superusers can create anything
        if ($user->isSuperuser()) {
            return true;
        }

        // Tenant admins can create projects in their tenant
        if ($user->role === 'admin' && $user->tenant_id) {
            return true;
        }

        // Check specific permissions
        return $user->hasPermissionTo('project.create') || 
               $user->hasPermissionTo('project.*');
    }

    public function update(User $user, Project $project): bool
    {
        // Superusers can update anything
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $project->tenant_id) {
            return false;
        }

        // Tenant admins can update everything in their tenant
        if ($user->role === 'admin') {
            return true;
        }

        // Check specific permissions
        return $user->hasPermissionTo('project.edit') || 
               $user->hasPermissionTo('project.*');
    }

    public function delete(User $user, Project $project): bool
    {
        // Superusers can delete anything
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $project->tenant_id) {
            return false;
        }

        // Tenant admins can delete everything in their tenant
        if ($user->role === 'admin') {
            return true;
        }

        // Check specific permissions
        return $user->hasPermissionTo('project.delete') || 
               $user->hasPermissionTo('project.*');
    }

    public function start(User $user, Project $project): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $project->tenant_id) {
            return false;
        }

        return in_array($user->role, ['admin', 'operator']) && (
            $user->hasPermissionTo('project.start') || 
            $user->hasPermissionTo('project.*')
        );
    }

    public function pause(User $user, Project $project): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $project->tenant_id) {
            return false;
        }

        return in_array($user->role, ['admin', 'operator']) && (
            $user->hasPermissionTo('project.pause') || 
            $user->hasPermissionTo('project.*')
        );
    }

    public function resume(User $user, Project $project): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $project->tenant_id) {
            return false;
        }

        return in_array($user->role, ['admin', 'operator']) && (
            $user->hasPermissionTo('project.resume') || 
            $user->hasPermissionTo('project.*')
        );
    }

    public function complete(User $user, Project $project): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $project->tenant_id) {
            return false;
        }

        return in_array($user->role, ['admin']) && (
            $user->hasPermissionTo('project.complete') || 
            $user->hasPermissionTo('project.*')
        );
    }

    public function review(User $user, Project $project): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $project->tenant_id) {
            return false;
        }

        return in_array($user->role, ['admin']) && (
            $user->hasPermissionTo('project.review') || 
            $user->hasPermissionTo('project.*')
        );
    }

    public function restore(User $user, Project $project): bool
    {
        return $this->update($user, $project);
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return $user->isSuperuser();
    }
}