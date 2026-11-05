<?php

namespace App\Http\Controllers;

use App\Models\LoginAttempt;
use Illuminate\Support\Facades\DB;

class SecurityDashboardController extends Controller
{
    public function index()
    {
        $since = now()->subHours(24);
        $stats = [];
        
        // Contar los estados de las últimas 24 horas
        foreach (['success', 'failed', 'blocked'] as $status) {
            $stats[$status] = LoginAttempt::where('status', $status)
                ->where('created_at', '>=', $since)
                ->count();
        }

        // Buscar IPs sospechosas (3 o más fallos en los últimos 5 minutos)
        $suspiciousIps = LoginAttempt::select(
            'ip_address',
            DB::raw('COUNT(*) as total')
        )
        ->where('status', 'failed')
        ->where('created_at', '>=', now()->subMinutes(5))
        ->groupBy('ip_address')
        ->havingRaw('COUNT(*) >= 3')
        ->orderByDesc('total')
        ->get();

        return view('security-dashboard', compact('stats', 'suspiciousIps'));
    }
}