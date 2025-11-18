<?php

namespace App\Filament\Tenant\Resources\Users\Pages;

use App\Filament\Tenant\Resources\Users\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => 
                    auth()->user()->role === 'admin' && 
                    $this->record->id !== auth()->id() &&
                    $this->record->role !== 'superuser'
                ),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Don't show password in edit form
        unset($data['password']);
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Força o tenant_id para o tenant do usuário logado
        $data['tenant_id'] = auth()->user()->tenant_id;

        // Only hash password if it's being changed
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $user = $this->record;
        
        // Update role assignment
        if ($user->role) {
            $roleName = $this->mapRoleToSpatie($user->role);
            if ($roleName) {
                // Remove all roles and assign the new one
                $user->syncRoles([$roleName]);
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
}