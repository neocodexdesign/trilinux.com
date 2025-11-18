<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        // Superusers podem ver todos os usuários
        if ($user->isSuperuser()) {
            return true;
        }

        // Admins podem ver usuários do seu tenant
        if ($user->role === 'admin' && $user->tenant_id) {
            return true;
        }

        return false;
    }

    public function view(User $user, User $model): bool
    {
        // Superusers podem ver qualquer usuário
        if ($user->isSuperuser()) {
            return true;
        }

        // Usuários podem ver a si mesmos
        if ($user->id === $model->id) {
            return true;
        }

        // Admins podem ver usuários do mesmo tenant
        if ($user->role === 'admin' && $user->tenant_id === $model->tenant_id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        // Superusers podem criar qualquer usuário
        if ($user->isSuperuser()) {
            return true;
        }

        // Admins podem criar usuários no seu tenant
        if ($user->role === 'admin' && $user->tenant_id) {
            return true;
        }

        return false;
    }

    public function update(User $user, User $model): bool
    {
        // Superusers podem editar qualquer usuário
        if ($user->isSuperuser()) {
            return true;
        }

        // Usuários podem editar a si mesmos (informações básicas)
        if ($user->id === $model->id) {
            return true;
        }

        // Admins podem editar usuários do mesmo tenant, mas não outros admins ou superusers
        if ($user->role === 'admin' && 
            $user->tenant_id === $model->tenant_id && 
            !in_array($model->role, ['superuser'])) {
            return true;
        }

        return false;
    }

    public function delete(User $user, User $model): bool
    {
        // Superusers podem deletar qualquer usuário (exceto eles mesmos)
        if ($user->isSuperuser() && $user->id !== $model->id) {
            return true;
        }

        // Admins podem deletar usuários do mesmo tenant, mas não eles mesmos nem superusers
        if ($user->role === 'admin' && 
            $user->tenant_id === $model->tenant_id && 
            $user->id !== $model->id &&
            $model->role !== 'superuser') {
            return true;
        }

        return false;
    }

    public function restore(User $user, User $model): bool
    {
        return $this->update($user, $model);
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->isSuperuser() && $user->id !== $model->id;
    }

    public function assignRole(User $user, User $model): bool
    {
        // Superusers podem atribuir qualquer role
        if ($user->isSuperuser()) {
            return true;
        }

        // Admins podem atribuir roles, mas não podem criar outros admins ou superusers
        if ($user->role === 'admin' && 
            $user->tenant_id === $model->tenant_id &&
            !in_array($model->role, ['superuser'])) {
            return true;
        }

        return false;
    }

    public function assignPermissions(User $user, User $model): bool
    {
        // Superusers podem atribuir qualquer permissão
        if ($user->isSuperuser()) {
            return true;
        }

        // Admins podem atribuir permissões a usuários do seu tenant
        if ($user->role === 'admin' && 
            $user->tenant_id === $model->tenant_id &&
            $model->role !== 'superuser') {
            return true;
        }

        return false;
    }
}