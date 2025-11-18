<?php

namespace App\Filament\Resources\Stages\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant_id')
                    ->searchable(),
                TextColumn::make('project.name')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('expected_start_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expected_end_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ended_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('dependentStage.name')
                    ->searchable(),
                TextColumn::make('responsible.name')
                    ->searchable(),
                TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('manage')
                    ->label('Gerenciar Completo')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->url(fn ($record) => route('stages.manage', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
