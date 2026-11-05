<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();
            
            // Login exitoso
            LoginAttempt::create([
                'identifier_hash' => hash('sha256', strtolower($request->email)),
                'ip_address' => $request->ip(),
                'status' => 'success',
                'user_agent' => substr($request->userAgent() ?? '', 0, 255),
                'created_at' => now(),
            ]);
            Log::channel('security')->info('Login exitoso', ['ip' => $request->ip()]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Login fallido
            LoginAttempt::create([
                'identifier_hash' => hash('sha256', strtolower($request->email)),
                'ip_address' => $request->ip(),
                'status' => 'failed',
                'user_agent' => substr($request->userAgent() ?? '', 0, 255),
                'created_at' => now(),
            ]);
            Log::channel('security')->warning('Login fallido', ['ip' => $request->ip()]);
            
            throw $e; // Volvemos a lanzar el error para que la vista muestre "Credenciales incorrectas"
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
