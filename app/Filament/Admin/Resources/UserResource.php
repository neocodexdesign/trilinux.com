<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\EditAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    
    protected static string|BackedEnum|null $navigationIcon = null;
    
    protected static ?string $navigationLabel = 'Usuários';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                        'superuser' => 'Superuser - Acesso total',
                        'admin' => 'Admin - Total no tenant',
                        'operator' => 'Operator - Limitado',
                        'client' => 'Client - Muito limitado',
                    ])
                    ->required()
                    ->label('Tipo de Usuário')
                    ->helperText('Superuser: acesso global. Admin: controle total no tenant. Operator/Client: limitado.'),

                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Tenant')
                    ->nullable()
                    ->helperText('Deixe vazio para usuários superuser'),

                CheckboxList::make('permissions')
                    ->relationship('permissions', 'name')
                    ->options(function () {
                        return \Spatie\Permission\Models\Permission::all()
                            ->pluck('name', 'id')
                            ->map(function ($name) {
                                return ucfirst(str_replace(['.', '_'], [' → ', ' '], $name));
                            });
                    })
                    ->columns(3)
                    ->searchable()
                    ->bulkToggleable()
                    ->label('Permissões Extras')
                    ->helperText('Estas permissões são adicionadas às permissões do role'),
            ]);
    }

    public static function table(Table $table): Table
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
                        'danger' => 'superuser',
                        'warning' => 'admin',
                        'primary' => 'operator',
                        'success' => 'client',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'superuser' => 'Superuser',
                        'admin' => 'Admin',
                        'operator' => 'Operator',
                        'client' => 'Client',
                        default => $state,
                    })
                    ->label('Tipo'),

                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->default('N/A'),
                    
                TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Permissões Extras'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Criado em'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
}