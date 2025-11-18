<?php

namespace App\Filament\Resources\Projects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nome'),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'planned' => 'Planejado',
                        'in_progress' => 'Em Progresso',
                        'paused' => 'Pausado',
                        'completed' => 'Completado',
                        'cancelled' => 'Cancelado',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'planned' => 'gray',
                        'in_progress' => 'info',
                        'paused' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->label('Status'),

                TextColumn::make('responsible.name')
                    ->badge()
                    ->color('info')
                    ->label('Responsável')
                    ->default('—')
                    ->searchable(),

                TextColumn::make('team.name')
                    ->badge()
                    ->color(fn ($record) => $record->team?->color ?? 'gray')
                    ->label('Equipe')
                    ->default('—')
                    ->searchable(),

                TextColumn::make('expected_start_at')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->label('Início Esperado')
                    ->toggleable(),

                TextColumn::make('expected_end_at')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->label('Fim Esperado')
                    ->toggleable(),

                TextColumn::make('started_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Iniciado em')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ended_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Finalizado em')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('creator.name')
                    ->label('Criado por')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Criado em')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Atualizado em')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}
