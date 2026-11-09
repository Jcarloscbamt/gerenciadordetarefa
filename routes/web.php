<?php

use App\Http\Controllers\AprovacaoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\BoardListController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EmpresaSwitchController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\TemaController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// Raiz: manda para o dashboard (ou login, se não autenticado)
Route::get('/', fn () => redirect()->route('dashboard'));

/* ---------- Autenticação ---------- */
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'mostrar'])->name('login');
    Route::post('/login', [LoginController::class, 'entrar']);

    // Autocadastro (usuário nasce inativo, aguardando aprovação)
    Route::get('/cadastro', [RegisterController::class, 'mostrar'])->name('register');
    Route::post('/cadastro', [RegisterController::class, 'cadastrar']);

    // Recuperação de senha
    Route::get('/esqueci-senha', [PasswordResetController::class, 'mostrarSolicitar'])->name('password.request');
    Route::post('/esqueci-senha', [PasswordResetController::class, 'enviarLink'])->name('password.email');
    Route::get('/redefinir-senha/{token}', [PasswordResetController::class, 'mostrarRedefinir'])->name('password.reset');
    Route::post('/redefinir-senha', [PasswordResetController::class, 'redefinir'])->name('password.update');
});
Route::post('/logout', [LoginController::class, 'sair'])->middleware('auth')->name('logout');

/* ---------- Seleção de empresa (autenticado, sem exigir empresa) ---------- */
Route::middleware('auth')->group(function () {
    Route::get('/empresas/selecionar', [EmpresaSwitchController::class, 'selecionar'])->name('empresas.selecionar');
    Route::post('/empresas/selecionar/{empresa}', [EmpresaSwitchController::class, 'definir'])->name('empresas.definir');

    // Tema visual do usuário
    Route::post('/tema', [TemaController::class, 'salvar'])->name('tema.salvar');
});

/* ---------- Área interna (exige empresa selecionada) ---------- */
Route::middleware(['auth', 'empresa'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Quadros
    Route::get('/quadros', [BoardController::class, 'index'])->name('boards.index');
    Route::post('/quadros', [BoardController::class, 'store'])->name('boards.store');
    Route::get('/quadros/{board}', [BoardController::class, 'show'])->name('boards.show');
    Route::put('/quadros/{board}', [BoardController::class, 'update'])->name('boards.update');
    Route::delete('/quadros/{board}', [BoardController::class, 'destroy'])->name('boards.destroy');

    // Colunas
    Route::post('/quadros/{board}/colunas', [BoardListController::class, 'store'])->name('listas.store');
    Route::put('/colunas/{lista}', [BoardListController::class, 'update'])->name('listas.update');
    Route::delete('/colunas/{lista}', [BoardListController::class, 'destroy'])->name('listas.destroy');

    // Cartões
    Route::post('/colunas/{lista}/cartoes', [CardController::class, 'store'])->name('cards.store');
    Route::put('/cartoes/{card}', [CardController::class, 'update'])->name('cards.update');
    Route::delete('/cartoes/{card}', [CardController::class, 'destroy'])->name('cards.destroy');
    Route::post('/cartoes/mover', [CardController::class, 'mover'])->name('cards.mover');
    Route::post('/cartoes/{card}/concluir', [CardController::class, 'concluir'])->name('cards.concluir');

    /* ---------- Administração (admin da empresa) ---------- */
    Route::middleware('admin')->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::post('/usuarios/vincular', [UsuarioController::class, 'vincular'])->name('usuarios.vincular');
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
        Route::delete('/usuarios/{usuario}/excluir', [UsuarioController::class, 'excluir'])->name('usuarios.excluir');

        Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    });

    /* ---------- Super administrador ---------- */
    Route::middleware('super')->group(function () {
        Route::get('/aprovacoes', [AprovacaoController::class, 'index'])->name('aprovacoes.index');
        Route::post('/aprovacoes/{usuario}/aprovar', [AprovacaoController::class, 'aprovar'])->name('aprovacoes.aprovar');
        Route::delete('/aprovacoes/{usuario}', [AprovacaoController::class, 'rejeitar'])->name('aprovacoes.rejeitar');

        Route::get('/empresas', [EmpresaController::class, 'index'])->name('empresas.index');
        Route::post('/empresas', [EmpresaController::class, 'store'])->name('empresas.store');
        Route::put('/empresas/{empresa}', [EmpresaController::class, 'update'])->name('empresas.update');
        Route::delete('/empresas/{empresa}', [EmpresaController::class, 'destroy'])->name('empresas.destroy');
    });
});
