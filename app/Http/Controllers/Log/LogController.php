<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a Escolta
|
*/

namespace App\Http\Controllers\Log;

use App\Classes\Tdata;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Unidades\UnidadesController;
use \App\Http\Controllers\Permissoes\PermissoesController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LogController extends Controller
{
    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');

    }

    public static function log($fk_id, $tabelaAlterada,$tipoAlteracao,$cpfUsuario,$texto)
        {
            if(PermissoesController::getTipoPermissao() == "E")
            {
                $lotacaoServidor = 0;

            }else {

                $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
            }


            $insertLog = DB::table('log')->insert(
                [
                    'unidade' => $lotacaoServidor,
                    'fk_id' => $fk_id,
                    'tabela_alterada' => $tabelaAlterada,
                    'tipo_alteracao' => $tipoAlteracao,
                    'cpf_usuario' => $cpfUsuario,
                    'created_at' => Tdata::TtimeStampHojeMysql(),
                    'texto' => mb_strtolower($texto)
                ]
            );

            if($insertLog){
                return true;
            }


    }

    public static function logAcessoUser()
    {
        $dtHoje = Tdata::TdataHojeMysql();

        if (!file_exists(storage_path('app/arquivos/loguser/' . Auth::user()->cpf . "/" .$dtHoje.".txt"))) {

            Storage::put('arquivos/loguser/' . Auth::user()->cpf . "/" .$dtHoje.".txt", "");
            $txt = date("H:i:s")." - ".url()->full()."\n\r";
            Storage::prepend('arquivos/loguser/' . Auth::user()->cpf . "/" .$dtHoje.".txt", $txt);

        }else{

            $txt = date("H:i:s")." - ".url()->full()."\n\r";
            Storage::prepend('arquivos/loguser/' . Auth::user()->cpf . "/" .$dtHoje.".txt", $txt);

        }

    }

    public static function getUltimoLoginNavegacao()
    {
        if(file_exists(storage_path('app/arquivos/loguser/' . Auth::user()->cpf . "/")))
        {
            $files = scandir(storage_path('app/arquivos/loguser/' . Auth::user()->cpf . "/"));

            $teste = collect($files);
            $teste = $teste->reverse();
            return substr($teste[count($teste)-1],0,10);

        }else{
            return "";
        }



    }

    public static function getDadosLog($fk_id, $tabela_alterada, $tipo_alteracao='add')
    {
        $dados = DB::table('log')->where('fk_id', $fk_id)->where('tipo_alteracao', $tipo_alteracao)->where('tabela_alterada', $tabela_alterada)->get();

        return $dados;
    }
    
    public static function getDadosLogService($fk_id, $tabela_alterada, $tipo_alteracao='add')
    {
        $dados = DB::table('log')
                ->leftJoin('users','users.cpf','=','log.cpf_usuario')
                ->where('log.fk_id', $fk_id)
                ->where('log.tipo_alteracao', $tipo_alteracao)
                ->where('log.tabela_alterada', $tabela_alterada)
                ->select('log.*', DB::raw("DATE_FORMAT(log.created_at,'%d/%m/%Y %H:%i:%s') as created_at"),'users.name')
                ->orderBy('created_at', 'DESC')
                ->get();

        return $dados[0];
    }
}