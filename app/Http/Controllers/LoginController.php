<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function show()
    {
        return Inertia::render('Login', [
            'success' => session('success'),
            'error' => session('error')
        ]);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'host' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
            'port' => 'required|integer',
            'root' => 'required|string',
        ]);

        // Salvar dados FTP na sessão
        session([
            'ftp_host' => $data['host'],
            'ftp_username' => $data['username'],
            'ftp_password' => $data['password'],
            'ftp_port' => $data['port'],
            'ftp_root' => $data['root'],
        ]);

        return redirect()->route('home')->with('success', 'Login FTP realizado com sucesso!');
    }

    public function loginEnv()
    {
        // Usar dados do arquivo de configuração
        session([
            'ftp_host' => config('ftp.host'),
            'ftp_username' => config('ftp.username'),
            'ftp_password' => config('ftp.password'),
            'ftp_port' => config('ftp.port', 21),
            'ftp_root' => config('ftp.root', '/'),
        ]);

        return redirect()->route('home')->with('success', 'Login com dados do sistema realizado com sucesso!');
    }

    public function logout()
    {
        // Remover dados FTP da sessão
        session()->forget([
            'ftp_host',
            'ftp_username',
            'ftp_password',
            'ftp_port',
            'ftp_root'
        ]);

        return redirect()->route('login')->with('success', 'Logout realizado com sucesso!');
    }
}
