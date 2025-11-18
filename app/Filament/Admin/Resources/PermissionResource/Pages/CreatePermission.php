<?php

namespace App\Filament\Admin\Resources\PermissionResource\Pages;

use App\Filament\Admin\Resources\PermissionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate permission name based on category and action
        if (!empty($data['category']) && !empty($data['action'])) {
            $data['name'] = $data['category'] . '.' . $data['action'];
        }

        // Remove helper fields that aren't part of the Permission model
        unset($data['category'], $data['action']);

        return $data;
    }
}