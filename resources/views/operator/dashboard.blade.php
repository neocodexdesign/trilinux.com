<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Operador</title>
    @livewireStyles
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none !important; }
        html, body { height: 100%; margin: 0; padding: 0; }
    </style>
</head>
<body class="bg-gray-50 h-screen overflow-hidden">
    <!-- Header fixo -->
    <div class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800">Dashboard do Operador</h1>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-gray-500 text-white px-3 py-2 rounded hover:bg-gray-600 transition text-sm">
                    Logout
                </button>
            </form>
            <form action="{{ route('operator.fechar-expediente') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                    Fechar Expediente
                </button>
            </form>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div style="height: calc(100vh - 80px);">
        @livewire('operator-dashboard')
    </div>

    @livewireScripts
</body>
</html>