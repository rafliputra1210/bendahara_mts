<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // 1. Tambahkan import Facade Auth ini

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) { // 2. Ubah auth()->check() menjadi Auth::check()
            return redirect()->route('login');
        }

        // Cek apakah role user ada di dalam daftar role yang diizinkan
        if (!in_array(Auth::user()->role, $roles)) { // 3. Ubah auth()->user() menjadi Auth::user()
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        return $next($request);
    }
}