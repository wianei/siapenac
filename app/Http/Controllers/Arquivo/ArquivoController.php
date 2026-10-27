<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a Escolta
|
*/

namespace App\Http\Controllers\Arquivo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Classes\Constantes;

class ArquivoController extends Controller
{
    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->middleware('auth');

    }

    public static function excluirArquivo($tabela,$id,$caminho)
    {
        $selectArquivo = DB::table($tabela)->where('id', $id)->get();


            if (file_exists(storage_path("app/".$caminho.'/' . $id . "/" . $selectArquivo[0]->nome_arquivo))) {

                $move = Storage::move($caminho."/" . $id . "/" . $selectArquivo[0]->nome_arquivo, Constantes::ARQUIVO_MORTO . "/" . $selectArquivo[0]->nome_arquivo);

            }else{

                $delete = DB::table($tabela)->where('id', $id)->delete();

                return "errorMove";
            }


        $delete = DB::table($tabela)->where('id', $id)->delete();


        if($delete == 1)
        {
            return true;

        }else{

            return false;
        }


    }

}