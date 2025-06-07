<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckFtpAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar se há dados FTP na sessão
        if (!session('ftp_host') || !session('ftp_username') || !session('ftp_password')) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'FTP não autenticado'], 401);
            }

            return redirect()->route('login')->with('error', 'Faça login no FTP para acessar esta página.');
        }

        return $next($request);
    }
}
