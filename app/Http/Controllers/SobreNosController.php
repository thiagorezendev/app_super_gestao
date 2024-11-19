<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Middleware\LogAcessoMiddlaware;

class SobreNosController extends Controller
{
    public function sobreNos() {
        return view('site.sobre-nos');
    }
}
