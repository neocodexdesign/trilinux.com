<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use App\Models\Team;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label('Nome'),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->label('Descrição'),
                Select::make('status')
                    ->options([
                        'planned' => 'Planejado',
                        'in_progress' => 'Em Progresso',
                        'paused' => 'Pausado',
                        'completed' => 'Completado',
                        'cancelled' => 'Cancelado',
                    ])
                    ->default('planned')
                    ->required()
                    ->label('Status'),

                Select::make('responsible_id')
                    ->relationship('responsible', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Usuário Responsável')
                    ->helperText('Atribua a um usuário OU a uma equipe, nunca ambos')
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $state ? $set('team_id', null) : null),

                Select::make('team_id')
                    ->relationship('team', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Equipe Responsável')
                    ->helperText('Atribua a uma equipe OU a um usuário, nunca ambos')
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $state ? $set('responsible_id', null) : null),

                DateTimePicker::make('expected_start_at')
                    ->label('Início Esperado'),
                DateTimePicker::make('expected_end_at')
                    ->label('Fim Esperado'),
                DateTimePicker::make('started_at')
                    ->label('Iniciado em'),
                DateTimePicker::make('ended_at')
                    ->label('Finalizado em'),
                TextInput::make('created_by')
                    ->numeric()
                    ->hidden(),
            ]);
    }
}
