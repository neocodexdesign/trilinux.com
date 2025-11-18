<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Filtros</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label for="start-date" class="block text-sm font-medium text-gray-700 mb-1">Data Início</label>
                <input type="date" 
                       id="start-date"
                       wire:model.live="startDate" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
            </div>
            
            <div>
                <label for="end-date" class="block text-sm font-medium text-gray-700 mb-1">Data Fim</label>
                <input type="date" 
                       id="end-date"
                       wire:model.live="endDate" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
            </div>
            
            <div>
                <label for="project" class="block text-sm font-medium text-gray-700 mb-1">Projeto</label>
                <select wire:model.live="selectedProject" 
                        id="project"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="">Todos os Projetos</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="user" class="block text-sm font-medium text-gray-700 mb-1">Usuário</label>
                <select wire:model.live="selectedUser" 
                        id="user"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="">Todos os Usuários</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="report-type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Relatório</label>
                <select wire:model.live="reportType" 
                        id="report-type"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="daily_summary">Resumo Diário</option>
                    <option value="productivity">Produtividade</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Daily Summary Report -->
    @if($reportType === 'daily_summary' && $dailySummary)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Resumo Diário de Atividades</h2>
            </div>
            
            <div class="p-6">
                @forelse($dailySummary as $date => $dayData)
                    <div class="mb-8 last:mb-0">
                        <h3 class="text-md font-medium text-gray-800 mb-4 border-b pb-2">
                            {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($date)->locale('pt_BR')->isoFormat('dddd') }}
                        </h3>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach($dayData as $userData)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-blue-600 font-semibold text-sm">
                                                {{ substr($userData['user']->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $userData['user']->name }}</h4>
                                            <p class="text-xs text-gray-500">{{ ucfirst($userData['user']->role) }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-2 mb-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Tempo Trabalhado:</span>
                                            <span class="font-medium text-green-600">
                                                {{ floor($userData['total_work_minutes'] / 60) }}h {{ $userData['total_work_minutes'] % 60 }}m
                                            </span>
                                        </div>
                                        
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Tempo Pausado:</span>
                                            <span class="font-medium text-yellow-600">
                                                {{ floor($userData['total_pause_minutes'] / 60) }}h {{ $userData['total_pause_minutes'] % 60 }}m
                                            </span>
                                        </div>
                                        
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Tarefas:</span>
                                            <span class="font-medium text-blue-600">{{ $userData['tasks_count'] }}</span>
                                        </div>
                                    </div>
                                    
                                    @if($userData['projects']->count())
                                        <div class="border-t pt-3">
                                            <h5 class="text-xs font-medium text-gray-700 mb-2">Projetos:</h5>
                                            @foreach($userData['projects'] as $project)
                                                <div class="flex justify-between text-xs mb-1">
                                                    <span class="text-gray-600 truncate mr-2">{{ $project['name'] }}</span>
                                                    <span class="text-gray-800 font-medium whitespace-nowrap">
                                                        {{ floor($project['work_minutes'] / 60) }}h {{ $project['work_minutes'] % 60 }}m
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-12">Nenhum dado encontrado para o período selecionado.</p>
                @endforelse
            </div>
        </div>
    @endif

    <!-- Productivity Report -->
    @if($reportType === 'productivity' && $productivitySummary)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Relatório de Produtividade</h2>
            </div>
            
            <div class="p-6">
                @forelse($productivitySummary as $userData)
                    <div class="border border-gray-200 rounded-lg p-6 mb-4 last:mb-0">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                    <span class="text-blue-600 font-semibold">
                                        {{ substr($userData['user']->name, 0, 2) }}
                                    </span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $userData['user']->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ ucfirst($userData['user']->role) }}</p>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <div class="text-2xl font-bold text-green-600">{{ $userData['productivity_rate'] }}%</div>
                                <div class="text-xs text-gray-500">Taxa de Produtividade</div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                <div class="text-lg font-semibold text-green-600">
                                    {{ floor($userData['total_work_minutes'] / 60) }}h {{ $userData['total_work_minutes'] % 60 }}m
                                </div>
                                <div class="text-xs text-gray-600">Tempo Trabalhado</div>
                            </div>
                            
                            <div class="text-center p-3 bg-yellow-50 rounded-lg">
                                <div class="text-lg font-semibold text-yellow-600">
                                    {{ floor($userData['total_pause_minutes'] / 60) }}h {{ $userData['total_pause_minutes'] % 60 }}m
                                </div>
                                <div class="text-xs text-gray-600">Tempo Pausado</div>
                            </div>
                            
                            <div class="text-center p-3 bg-blue-50 rounded-lg">
                                <div class="text-lg font-semibold text-blue-600">{{ $userData['tasks_completed'] }}</div>
                                <div class="text-xs text-gray-600">Tarefas Concluídas</div>
                            </div>
                            
                            <div class="text-center p-3 bg-purple-50 rounded-lg">
                                <div class="text-lg font-semibold text-purple-600">
                                    {{ $userData['average_task_time'] }}m
                                </div>
                                <div class="text-xs text-gray-600">Tempo Médio/Tarefa</div>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mt-4">
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span>Produtividade</span>
                                <span>{{ $userData['productivity_rate'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $userData['productivity_rate'] }}%"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-12">Nenhum dado encontrado para o período selecionado.</p>
                @endforelse
            </div>
        </div>
    @endif
</div>
