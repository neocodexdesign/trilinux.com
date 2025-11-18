<?php

namespace App\Filament\Tenant\Resources\Tasks\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Carbon\Carbon;
use App\Models\Stage;
use App\Models\Task;
use App\Models\User;
use App\Models\TaskNote;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('tenant_id')
                    ->default(auth()->user()?->tenant_id),
                
                Placeholder::make('project_info')
                    ->label('Projeto')
                    ->content(fn ($record) => $record?->stage?->project?->name ?? 'Selecione uma etapa para ver o projeto')
                    ->visible(fn ($record) => $record !== null),
                
                Select::make('stage_id')
                    ->relationship('stage', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Etapa')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->project->name . ' → ' . $record->name)
                    ->reactive(),
                
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nome da Tarefa'),
                
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
                
                Select::make('dependent_task_id')
                    ->relationship('dependentTask', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Depende da Tarefa'),
                
                Select::make('responsible_id')
                    ->relationship('responsible', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Responsável'),
                
                TextInput::make('order')
                    ->numeric()
                    ->default(1)
                    ->label('Ordem'),
                
                TextInput::make('estimated_hours')
                    ->numeric()
                    ->step(0.25)
                    ->label('Horas Estimadas'),
                
                TextInput::make('actual_hours')
                    ->numeric()
                    ->step(0.25)
                    ->label('Horas Reais')
                    ->disabled()
                    ->dehydrated(false),

                Repeater::make('notes')
                    ->relationship('notes')
                    ->schema([
                        Textarea::make('content')
                            ->label('Nota Administrativa')
                            ->required()
                            ->rows(3),
                        
                        Placeholder::make('note_info')
                            ->label('Informações')
                            ->content(fn ($record) => $record ? 
                                'Criada por: ' . $record->user->name . ' em ' . $record->created_at->format('d/m/Y H:i') : 
                                'Nova nota será criada por: ' . auth()->user()->name
                            ),
                        
                        Hidden::make('user_id')
                            ->default(auth()->id()),
                            
                        Hidden::make('tenant_id')
                            ->default(auth()->user()?->tenant_id),
                    ])
                    ->orderColumn(false)
                    ->reorderable(false)
                    ->deletable(true)
                    ->addActionLabel('Adicionar Nota')
                    ->itemLabel(fn ($state, $record) => 
                        'Nota de ' . 
                        ($record?->created_at ? $record->created_at->format('d/m/Y H:i') : 'agora') .
                        ' - ' . 
                        ($record?->user?->name ?? (User::find($state['user_id'] ?? null)?->name ?? 'Usuário'))
                    )
                    ->collapsed()
                    ->cloneable(false)
                    ->visible(fn ($record) => $record !== null)
                    ->label('Notas Administrativas')
                    ->helperText('Notas privadas para controle interno'),
            ]);
    }
}
