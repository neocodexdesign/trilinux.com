<?php

namespace App\Filament\Tenant\Resources\Projects\Pages;

use App\Filament\Tenant\Resources\Projects\ProjectResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('markdownPlan')
                ->label('Markdown Plan')
                ->icon('heroicon-o-check-circle')
                ->url(fn () => route('projects.markdown-plan', $this->record))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
