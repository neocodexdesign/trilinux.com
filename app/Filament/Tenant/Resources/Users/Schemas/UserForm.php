<?php

namespace App\Filament\Tenant\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('tenant_id')
                    ->default(auth()->user()?->tenant_id),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nome'),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Email'),

                TextInput::make('username')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Username'),

                TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->label('Senha'),

                Select::make('role')
                    ->options([
                        'admin' => 'Admin - Controle total do tenant',
                        'operator' => 'Operator - Operações limitadas',
                        'client' => 'Client - Acesso muito limitado',
                    ])
                    ->required()
                    ->label('Tipo de Usuário')
                    ->helperText('Admin: controle total no tenant. Operator/Client: limitado.')
                    ->default('operator'),

                CheckboxList::make('permissions')
                    ->relationship('permissions', 'name')
                    ->options(function () {
                        return \Spatie\Permission\Models\Permission::all()
                            ->pluck('name', 'name')
                            ->map(function ($name) {
                                return ucfirst(str_replace(['.', '_'], [' → ', ' '], $name));
                            });
                    })
                    ->columns(3)
                    ->searchable()
                    ->bulkToggleable()
                    ->label('Permissões Específicas')
                    ->helperText('Permissões extras além do role padrão'),
            ]);
    }
}