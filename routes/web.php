<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\LogAcessoMiddlaware;

Route::get('/', [App\Http\Controllers\PrincipalController::class, 'principal'])->name('site.index')->middleware('log.acesso');
Route::get('/sobre-nos', [App\Http\Controllers\SobreNosController::class, 'sobreNos'])->name('site.sobrenos');
Route::get('/contato', [App\Http\Controllers\ContatoController::class, 'contato'])->name('site.contato');// middleware(LogAcessoMiddlaware::class)->
Route::post('/contato', [App\Http\Controllers\ContatoController::class, 'salvar'])->name('site.contato');
Route::get('/login', function(){ return 'Login'; })->name('site.login');

Route::prefix('/app')->group(function(){
    Route::middleware('log.acesso', 'autenticacao')->get('/clientes', function(){ return 'Clientes'; })->name('app.clientes');
    Route::middleware('log.acesso', 'autenticacao')->get('/fornecedores', [App\Http\Controllers\FornecedorController::class, 'index'])->name('app.fornecedores');
    Route::middleware('log.acesso', 'autenticacao')->get('/produtos', function(){ return 'Produtos'; })->name('app.produtos');
});

Route::get('/teste/{p1}/{p2}', [App\Http\Controllers\TesteController::class, 'teste'])->name('teste');

Route::fallback(function() {
    echo 'A rota acessada não existe! <a href="'.route('site.index').'">Clique aqui</a> para voltar para a página principal.';
});