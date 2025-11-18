<?php

// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller // <-- O nome da classe deve ser exato
{
    // O método deve existir e ser público
    public function showLogin()
    {
        return view('auth.login');
    }
}