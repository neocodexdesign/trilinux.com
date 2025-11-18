<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        return $user->hasPermissionTo('task.view') || 
               $user->hasPermissionTo('task.*');
    }

    public function view(User $user, Task $task): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $task->tenant_id) {
            return false;
        }

        // Check role-based permissions
        if ($user->role === 'client') {
            // Clients can only view tasks assigned to them
            return $task->responsible_id === $user->id;
        }

        // Responsible users can always view their own tasks
        if ($task->responsible_id === $user->id) {
            return true;
        }

        return $user->hasPermissionTo('task.view') || 
               $user->hasPermissionTo('task.*');
    }

    public function create(User $user): bool
    {
        Log::info('TaskPolicy@create check', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'tenant_id' => $user->tenant_id,
            'is_superuser' => method_exists($user, 'isSuperuser') ? $user->isSuperuser() : null,
            'has_perm_task_create' => $user->hasPermissionTo('task.create'),
            'has_perm_task_all' => $user->hasPermissionTo('task.*'),
        ]);

        if ($user->isSuperuser()) {
            return true;
        }

        return in_array($user->role, ['admin']) && (
            $user->hasPermissionTo('task.create') || 
            $user->hasPermissionTo('task.*')
        );
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $task->tenant_id) {
            return false;
        }

        // Responsible users can update their own tasks
        if ($task->responsible_id === $user->id && in_array($user->role, ['operator', 'client'])) {
            return $user->hasPermissionTo('task.edit') || 
                   $user->hasPermissionTo('task.*');
        }

        return in_array($user->role, ['admin']) && (
            $user->hasPermissionTo('task.edit') || 
            $user->hasPermissionTo('task.*')
        );
    }

    public function delete(User $user, Task $task): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $task->tenant_id) {
            return false;
        }

        return in_array($user->role, ['admin']) && (
            $user->hasPermissionTo('task.delete') || 
            $user->hasPermissionTo('task.*')
        );
    }

    public function start(User $user, Task $task): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $task->tenant_id) {
            return false;
        }

        // Responsible users can start their own tasks
        if ($task->responsible_id === $user->id) {
            return $user->hasPermissionTo('task.start') || 
                   $user->hasPermissionTo('task.*');
        }

        return in_array($user->role, ['admin', 'operator']) && (
            $user->hasPermissionTo('task.start') || 
            $user->hasPermissionTo('task.*')
        );
    }

    public function pause(User $user, Task $task): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $task->tenant_id) {
            return false;
        }

        // Responsible users can pause their own tasks
        if ($task->responsible_id === $user->id) {
            return $user->hasPermissionTo('task.pause') || 
                   $user->hasPermissionTo('task.*');
        }

        return in_array($user->role, ['admin', 'operator']) && (
            $user->hasPermissionTo('task.pause') || 
            $user->hasPermissionTo('task.*')
        );
    }

    public function resume(User $user, Task $task): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $task->tenant_id) {
            return false;
        }

        // Responsible users can resume their own tasks
        if ($task->responsible_id === $user->id) {
            return $user->hasPermissionTo('task.resume') || 
                   $user->hasPermissionTo('task.*');
        }

        return in_array($user->role, ['admin', 'operator']) && (
            $user->hasPermissionTo('task.resume') || 
            $user->hasPermissionTo('task.*')
        );
    }

    public function complete(User $user, Task $task): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $task->tenant_id) {
            return false;
        }

        // Responsible users can complete their own tasks
        if ($task->responsible_id === $user->id) {
            return $user->hasPermissionTo('task.complete') || 
                   $user->hasPermissionTo('task.*');
        }

        return in_array($user->role, ['admin', 'operator']) && (
            $user->hasPermissionTo('task.complete') || 
            $user->hasPermissionTo('task.*')
        );
    }

    public function review(User $user, Task $task): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $task->tenant_id) {
            return false;
        }

        return in_array($user->role, ['admin']) && (
            $user->hasPermissionTo('task.review') || 
            $user->hasPermissionTo('task.*')
        );
    }

    public function reopen(User $user, Task $task): bool
    {
        if ($user->isSuperuser()) {
            return true;
        }

        // Check tenant access
        if ($user->tenant_id !== $task->tenant_id) {
            return false;
        }

        return in_array($user->role, ['admin']) && (
            $user->hasPermissionTo('task.reopen') || 
            $user->hasPermissionTo('task.*')
        );
    }

    public function restore(User $user, Task $task): bool
    {
        return $this->update($user, $task);
    }

    public function forceDelete(User $user, Task $task): bool
    {
        return $user->isSuperuser();
    }
}
