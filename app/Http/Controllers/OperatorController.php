<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\TaskTime;

class OperatorController extends Controller
{
    public function dashboard()
    {
        if (!in_array(Auth::user()->role, ['operator', 'manager', 'admin', 'superuser'])) {
            abort(403, 'Unauthorized');
        }

        return view('operator.dashboard');
    }

    public function fecharExpediente(Request $request)
    {
        $user = Auth::user();
        
        TaskTime::where('user_id', $user->id)
            ->whereNull('ended_at')
            ->update([
                'ended_at' => now(),
                'paused_at' => now()
            ]);

        Task::whereHas('activeTimes', function ($query) use ($user) {
                $query->where('user_id', $user->id)->whereNull('ended_at');
            })
            ->update(['status' => 'paused']);

        Auth::logout();

        return redirect()->route('login')->with('message', 'Expediente fechado com sucesso!');
    }
}