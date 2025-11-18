<?php

namespace App\Filament\Admin\Resources\PermissionResource\Pages;

use App\Filament\Admin\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermission extends EditRecord
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Split permission name to populate category and action fields
        if (!empty($data['name']) && str_contains($data['name'], '.')) {
            [$category, $action] = explode('.', $data['name'], 2);
            $data['category'] = $category;
            $data['action'] = $action;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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