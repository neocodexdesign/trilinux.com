<?php

namespace App\Filament\Tenant\Resources\Stages\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use App\Models\Project;
use App\Models\Stage;
use App\Models\User;

class StageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('tenant_id')
                    ->default(auth()->user()?->tenant_id),
                
                Placeholder::make('project_info')
                    ->label('Projeto Selecionado')
                    ->content(fn ($record) => $record?->project?->name ?? 'Nenhum projeto selecionado')
                    ->visible(fn ($record) => $record !== null),
                
                Select::make('project_id')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Projeto')
                    ->reactive(),
                
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nome da Etapa'),
                
                Textarea::make('description')
                    ->rows(3)
                    ->label('Descrição'),
                
                Select::make('status')
                    ->options([
                        'planned' => 'Planejado',
                        'in_progress' => 'Em Progresso',
                        'paused' => 'Pausado',
                        'completed' => 'Concluído',
                        'cancelled' => 'Cancelado',
                    ])
                    ->default('planned')
                    ->required()
                    ->label('Status'),
                
                DateTimePicker::make('expected_start_at')
                    ->label('Data Prevista de Início'),
                
                DateTimePicker::make('expected_end_at')
                    ->label('Data Prevista de Término'),
                
                Select::make('dependent_stage_id')
                    ->relationship('dependentStage', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Depende da Etapa'),
                
                Select::make('responsible_id')
                    ->relationship('responsible', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Responsável'),
                
                TextInput::make('order')
                    ->numeric()
                    ->default(1)
                    ->label('Ordem'),
            ]);
    }
}
