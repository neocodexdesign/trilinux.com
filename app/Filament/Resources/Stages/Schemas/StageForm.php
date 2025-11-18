<?php

namespace App\Filament\Resources\Stages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->relationship('project', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
            'planned' => 'Planned',
            'in_progress' => 'In progress',
            'paused' => 'Paused',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ])
                    ->default('planned')
                    ->required(),
                DateTimePicker::make('expected_start_at'),
                DateTimePicker::make('expected_end_at'),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('ended_at'),
                Select::make('dependent_stage_id')
                    ->relationship('dependentStage', 'name')
                    ->label('Etapa Dependente'),

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

                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
