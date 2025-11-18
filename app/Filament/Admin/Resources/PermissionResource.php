<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PermissionResource\Pages;
use Spatie\Permission\Models\Permission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\EditAction;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;
    
    protected static string|BackedEnum|null $navigationIcon = null;
    
    protected static ?string $navigationLabel = 'Permissões';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Nome da Permissão')
                    ->helperText('Ex: project.create, task.edit, user.delete'),

                Select::make('category')
                    ->options([
                        'project' => 'Projetos',
                        'stage' => 'Etapas',
                        'task' => 'Tarefas',
                        'user' => 'Usuários',
                        'tenant' => 'Tenants',
                        'template' => 'Templates',
                        'report' => 'Relatórios',
                        'system' => 'Sistema',
                    ])
                    ->label('Categoria')
                    ->helperText('Categoria para organização'),

                Select::make('action')
                    ->options([
                        'view' => 'Visualizar',
                        'create' => 'Criar',
                        'edit' => 'Editar',
                        'delete' => 'Deletar',
                        'start' => 'Iniciar',
                        'pause' => 'Pausar',
                        'resume' => 'Retomar',
                        'complete' => 'Completar',
                        'review' => 'Revisar',
                        'export' => 'Exportar',
                        '*' => 'Todas',
                    ])
                    ->label('Ação'),
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

                BadgeColumn::make('category')
                    ->colors([
                        'primary' => 'project',
                        'success' => 'task',
                        'warning' => 'user',
                        'danger' => 'system',
                        'secondary' => fn ($state) => !in_array($state, ['project', 'task', 'user', 'system']),
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'project' => 'Projetos',
                        'stage' => 'Etapas', 
                        'task' => 'Tarefas',
                        'user' => 'Usuários',
                        'tenant' => 'Tenants',
                        'template' => 'Templates',
                        'report' => 'Relatórios',
                        'system' => 'Sistema',
                        default => $state,
                    })
                    ->label('Categoria'),

                BadgeColumn::make('action')
                    ->colors([
                        'success' => ['view', 'create'],
                        'warning' => ['edit', 'start', 'resume'],
                        'danger' => ['delete', 'pause'],
                        'primary' => ['complete', 'review', 'export'],
                        'secondary' => '*',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'view' => 'Visualizar',
                        'create' => 'Criar',
                        'edit' => 'Editar',
                        'delete' => 'Deletar',
                        'start' => 'Iniciar',
                        'pause' => 'Pausar',
                        'resume' => 'Retomar',
                        'complete' => 'Completar',
                        'review' => 'Revisar',
                        'export' => 'Exportar',
                        '*' => 'Todas',
                        default => $state,
                    })
                    ->label('Ação'),

                TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Usuários'),

                TextColumn::make('roles_count')
                    ->counts('roles')
                    ->label('Roles'),

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
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
}