<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user() || $request->user()->role !== $role) {
            if ($role === 'admin') {
                return redirect()->route('login')->with('error', 'Akses ditolak. Halaman ini hanya untuk Admin.');
            }
            return redirect()->route('login')->with('error', 'Akses ditolak. Silakan login sebagai Mahasiswa.');
        }

        return $next($request);
    }
}
