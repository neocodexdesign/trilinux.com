<?php

namespace App\Filament\Tenant\Resources\Users;

use App\Filament\Tenant\Resources\Users\Pages\CreateUser;
use App\Filament\Tenant\Resources\Users\Pages\EditUser;
use App\Filament\Tenant\Resources\Users\Pages\ListUsers;
use App\Filament\Tenant\Resources\Users\Schemas\UserForm;
use App\Filament\Tenant\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = null;
    
    protected static ?string $navigationLabel = 'Usuários do Tenant';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        // Apenas usuários do mesmo tenant do usuário logado
        return parent::getEloquentQuery()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('role', '!=', 'superuser'); // Admins não podem ver/editar superusers
    }

    public static function canCreate(): bool
    {
        // Apenas admins podem criar usuários
        return auth()->user()->role === 'admin';
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        // Admin pode editar qualquer usuário do seu tenant, exceto superusers
        return auth()->user()->role === 'admin' && $record->role !== 'superuser';
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        // Admin pode deletar usuários do seu tenant, exceto ele mesmo e superusers
        return auth()->user()->role === 'admin' && 
               $record->id !== auth()->id() && 
               $record->role !== 'superuser';
    }
}