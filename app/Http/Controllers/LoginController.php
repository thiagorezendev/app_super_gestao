<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index() {
        return view('site.login', ['titulo' => 'Login']);
    }

    public function autenticar(Request $request) {
        //validação
        $regras = [
            'usuario' => 'email|required',
            'senha' => 'required'
        ];

        //mensagens de feedback da validação
        $feedback = [
            'usuario.email' => 'Insira um e-mail válido.',
            'usuario.required' => 'O campo usuário (e-mail) é obrigatório.',
            'senha.required' => 'O campo senha é obrigatório.'
        ];

        //se não passar pelo validate
        $request->validate($regras, $feedback);

        print_r($request->all());
    }
}
