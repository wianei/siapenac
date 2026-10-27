<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a Agenda
|
*/

namespace App\Http\Controllers\Municipios;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MunicipiosController extends Controller
{
    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');

    }

    public function getMunicipio(Request $request)
    {
        $municipio = DB::table('municipio')->where('Uf',$request->get('uf'))->orderBy('Nome','ASC')->get();

        $cidades = array();
        foreach(json_decode($municipio) as $m)
        {
            array_push($cidades, $m->Nome);
        }

        return json_encode($cidades);
    }

}