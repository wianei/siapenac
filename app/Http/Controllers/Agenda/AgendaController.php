<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a Agenda
|
*/

namespace App\Http\Controllers\Agenda;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AgendaController extends Controller
{

    public function consulta()
    {
        return view('agenda.consulta.consulta');
    }

    public function gerenciamento()
    {
        return view('agenda.gerenciamento.home');
    }

    public function cadastro()
    {
        return view('agenda.gerenciamento.cadastroEvento');
    }

}