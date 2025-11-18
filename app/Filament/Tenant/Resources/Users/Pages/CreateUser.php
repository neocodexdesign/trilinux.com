<?php

namespace App\Filament\Tenant\Resources\Users\Pages;

use App\Filament\Tenant\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Força o tenant_id para o tenant do usuário logado
        $data['tenant_id'] = auth()->user()->tenant_id;

        // Hash da senha
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $user = $this->record;
        
        // Assign role based on user role field
        if ($user->role && !$user->hasRole($user->role)) {
            $roleName = $this->mapRoleToSpatie($user->role);
            if ($roleName) {
                $user->assignRole($roleName);
            }
        }
    }

    private function mapRoleToSpatie(string $role): ?string
    {
        return match($role) {
            'admin' => 'admin',
            'operator' => 'operator',
            'client' => 'client',
            default => null,
        };
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}