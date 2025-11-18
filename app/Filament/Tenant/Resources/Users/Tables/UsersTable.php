<?php

namespace App\Filament\Tenant\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nome'),
                    
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label('Email'),
                    
                TextColumn::make('username')
                    ->searchable()
                    ->label('Username'),

                BadgeColumn::make('role')
                    ->colors([
                        'warning' => 'admin',
                        'primary' => 'operator',
                        'success' => 'client',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Admin',
                        'operator' => 'Operator',
                        'client' => 'Client',
                        default => $state,
                    })
                    ->label('Tipo'),
                    
                TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Permissões Extras'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Criado em'),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'operator' => 'Operator',
                        'client' => 'Client',
                    ])
                    ->label('Tipo de Usuário'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}