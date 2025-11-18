<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\OperatorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Rotas de Tenant
|--------------------------------------------------------------------------
|
| Estas rotas são carregadas APENAS para domínios de tenants.
| O middleware InitializeTenancyByDomain e PreventAccessFromCentralDomains
| são aplicados automaticamente pelo bootstrap/app.php
|
| A proteção contra carregamento no domínio central é feita no bootstrap/app.php
|
*/

Route::post('/__try-login', function (Request $r) {
    $ok = Auth::guard('web')->attempt([
        'email' => $r->input('email'),
        'password' => $r->input('password'),
    ]);

    return [
        'db' => DB::connection()->getDatabaseName(),
        'attempt' => $ok,
        'user_id' => $ok ? Auth::id() : null,
    ];
});

// Rotas do Operador
Route::middleware(['auth'])->group(function () {
    Route::get('/operator/dashboard', [OperatorController::class, 'dashboard'])->name('operator.dashboard');
    Route::post('/fechar-expediente', [OperatorController::class, 'fecharExpediente'])->name('operator.fechar-expediente');
});