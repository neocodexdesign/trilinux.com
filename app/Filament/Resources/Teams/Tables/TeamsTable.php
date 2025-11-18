<?php

namespace App\Filament\Resources\Teams\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nome'),

                TextColumn::make('description')
                    ->limit(50)
                    ->label('Descrição'),

                TextColumn::make('color')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'blue' => 'Azul',
                        'green' => 'Verde',
                        'purple' => 'Roxo',
                        'orange' => 'Laranja',
                        'pink' => 'Rosa',
                        'yellow' => 'Amarelo',
                        default => $state,
                    })
                    ->color(fn (string $state): string => $state)
                    ->label('Cor'),

                TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Membros')
                    ->badge()
                    ->color('info'),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Ativo'),

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
