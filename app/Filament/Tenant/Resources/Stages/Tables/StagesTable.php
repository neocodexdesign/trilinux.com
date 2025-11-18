<?php

namespace App\Filament\Tenant\Resources\Stages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;

class StagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.name')
                    ->searchable()
                    ->sortable()
                    ->label('Projeto'),
                
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nome da Etapa'),
                
                TextColumn::make('description')
                    ->limit(50)
                    ->label('Descrição'),
                
                BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'planned',
                        'primary' => 'in_progress',
                        'warning' => 'paused',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'planned' => 'Planejado',
                        'in_progress' => 'Em Progresso',
                        'paused' => 'Pausado',
                        'completed' => 'Concluído',
                        'cancelled' => 'Cancelado',
                        default => $state,
                    })
                    ->label('Status'),
                
                TextColumn::make('responsible.name')
                    ->label('Responsável'),
                
                TextColumn::make('order')
                    ->sortable()
                    ->label('Ordem'),
                
                TextColumn::make('expected_start_at')
                    ->date()
                    ->label('Início Previsto'),
                
                TextColumn::make('expected_end_at')
                    ->date()
                    ->label('Fim Previsto'),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Criado em'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'planned' => 'Planejado',
                        'in_progress' => 'Em Progresso',
                        'paused' => 'Pausado',
                        'completed' => 'Concluído',
                        'cancelled' => 'Cancelado',
                    ])
                    ->label('Status'),
                
                SelectFilter::make('project_id')
                    ->relationship('project', 'name')
                    ->label('Projeto'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order', 'asc');
    }
}
