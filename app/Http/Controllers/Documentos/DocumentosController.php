<?php


/*
|--------------------------------------------------------------------------
| Desenvolvimento: geovanni_ti@hotmail.com / saulo.ricardo_@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com métodos relacionados aos documentos
|
*/

namespace App\Http\Controllers\Documentos;

use App\Classes\Constantes;
use App\Classes\GeocodingMaps;
use App\Classes\Tdata;
use App\Contato;
use App\Endereco;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Usuario\UsuarioController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Interno;
use App\Outrosnomes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\In;


class DocumentosController extends Controller
{
    public function documentosView($id)
    {
        $documento = DB::table('anexo')->where('id', $id)->get()[0];
        // se o arquivo existir no servidor
        return response()->file(storage_path($documento->caminho));
    }
}
