<?php

namespace App\Filament\Resources\Teams\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Schema;
use App\Models\User;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nome da Equipe'),

                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3)
                    ->label('Descrição'),

                Select::make('color')
                    ->required()
                    ->default('blue')
                    ->options([
                        'blue' => 'Azul',
                        'green' => 'Verde',
                        'purple' => 'Roxo',
                        'orange' => 'Laranja',
                        'pink' => 'Rosa',
                        'yellow' => 'Amarelo',
                    ])
                    ->label('Cor'),

                Toggle::make('is_active')
                    ->required()
                    ->default(true)
                    ->label('Ativo'),

                CheckboxList::make('members')
                    ->relationship('users', 'name')
                    ->label('Membros da Equipe')
                    ->bulkToggleable()
                    ->searchable()
                    ->columnSpanFull(),
            ]);
    }
}
