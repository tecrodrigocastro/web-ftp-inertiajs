<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;

// Rotas de Login (sem middleware)
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/login-env', [LoginController::class, 'loginEnv'])->name('login.env');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rotas protegidas por FTP auth
Route::middleware(['web'])->group(function () {
    // Rota principal
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Operações FTP
    Route::post('/upload', [HomeController::class, 'upload'])->name('upload');
    Route::get('/download/{path}', [HomeController::class, 'download'])->name('download')->where('path', '.*');
    Route::delete('/delete/{path}', [HomeController::class, 'delete'])->name('delete')->where('path', '.*');
    Route::post('/create-folder', [HomeController::class, 'createFolder'])->name('create-folder');
    Route::post('/rename', [HomeController::class, 'rename'])->name('rename');
    Route::post('/unzip/{path}', [HomeController::class, 'unzip'])->name('unzip')->where('path', '.*');
    Route::post('/unzip-async/{path}', [HomeController::class, 'unzipAsync'])->name('unzip.async')->where('path', '.*');

    // Edição de arquivos
    Route::get('/edit/{path}', [HomeController::class, 'edit'])->name('edit')->where('path', '.*');
    Route::post('/edit/{path}', [HomeController::class, 'update'])->name('update')->where('path', '.*');

    // Visualização de arquivos
    Route::get('/raw/{path}', [HomeController::class, 'viewRaw'])->name('raw')->where('path', '.*');

    // Novas rotas para copiar e mover itens
    Route::post('/copy-items', [HomeController::class, 'copyItems'])->name('copy-items');
    Route::post('/move-items', [HomeController::class, 'moveItems'])->name('move-items');
});
