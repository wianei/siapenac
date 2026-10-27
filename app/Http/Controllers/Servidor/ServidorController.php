<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a Notificacao
|
*/

namespace App\Http\Controllers\Servidor;

use App\Classes\Tdata;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Interno\InternoController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Http\Controllers\Permissoes\PermissoesController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServidorController extends Controller
{
    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->middleware('auth');


    }

    public function callAction($method, $parameters)
    {
        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::verifyPermission("ServidorController", $method);
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }


        return parent::callAction($method, $parameters);
    }

    public function listarServidores()
    {
        ini_set('memory_limit', '2048M');

        $siglaLotacaoServidor = FacadeController::getLotacaoServidor(Auth::user()->cpf);

        $dadosUser = FacadeController::getServidoresDaLotacao($siglaLotacaoServidor);

        $totalServidores = FacadeController::getTotalServidoresLotacao(FacadeController::getLotacaoServidor(Auth::user()->cpf));


        $dados = array();

        foreach($dadosUser as $dado)
        {
            $existe = false;
            $dadosConta = DB::table('users')->where('cpf', $dado->cpf)->select('tipo','status')->get();
            if(count($dadosConta) == 0)
            {
                $permissao = "Não possui Conta";
                $tipo = 0;
            }else{
                
                $permissao = PermissoesController::getNomePerfil($dadosConta[0]->tipo);
                $tipo = PermissoesController::getNomePerfil($dadosConta[0]->tipo);

//                $permissao = (($dadosConta[0]->tipo == "300") ? "Padrão" :
//                    (($dadosConta[0]->tipo == "600") ? "Administrativo" :
//                        (($dadosConta[0]->tipo == "700") ? "Controlador" :
//                            (($dadosConta[0]->tipo == "800") ? "Gerência" :null))));
//                $tipo = $dadosConta[0]->tipo;

            }

            if(!is_null($dado->cpf) && $dado->cpf != ""){
                
                $foto = file_get_contents(config('app.rh').'/webservice/fotos-servidores/1wBd794bG5BFkHU71xltg6Gbo2Tx6a4y/'.trim($dado->cpf));

                foreach ($dados as $key => $dadoInserido) {
                    if($dadoInserido['cpf'] == $dado->cpf){
                        $dados[$key]['fone'] = $dadoInserido['fone'] . ', ' . $dado->fone;
                        $existe = true;
                    }
                }
                if(!$existe){
                    array_push($dados, array(
                        'nome' => $dado->nome,
                        'fone' => $dado->fone,
                        'cpf' => $dado->cpf,
                        'permissao' => $permissao,
                        'tipo' => $tipo,
                        'matricula' => $dado->matricula,
                        'foto' => $foto
                    ));
                }
            }

        }

        //dd(json_decode(json_encode($dados, false)));

        $dados = json_decode(json_encode($dados), FALSE);

        //dd($dados);

        return view('servidor.listaServidores.listaServidores', compact('dados','totalServidores'));

    }

    public function alterarPermissao($cpf)
    {
        $siglaLotacaoServidor = FacadeController::getLotacaoServidor(Auth::user()->cpf);

        $dados = DB::table('users')->where('cpf', $cpf)->where('status','A')->select('tipo','cpf','status')->get();

        $nomeServidor = FacadeController::getNomeServidorCpf($cpf);

        if(count($dados) == 0)
        {
            return redirect()->back()->with('alert-danger', "O servidor <strong>". $nomeServidor."</strong> não possui conta no " .config('app.name')." ou a conta está inativa.");
        }


        return view('servidor.listaServidores.alterarPermissaoServidor', compact('dados','nomeServidor'));


    }

    public function alterarPermissaoStore(Request $request, $cpf)
    {
        $siglaLotacaoServidor = FacadeController::getLotacaoServidor(Auth::user()->cpf);

        $lotacaoServidorAlterado = FacadeController::getLotacaoServidor($cpf);

        if($siglaLotacaoServidor != $lotacaoServidorAlterado)
        {
            return redirect()->back()->with('alert-danger', "Você não tem permissão para alterar a permissao do servidor de outra unidade!");

        }

        $diferencas = array();
        $alteracoes = "Alteração de permissão do usuario - realizado por diretor de unidade.<br>";

        $dadosBD = DB::table("users")->where('cpf', $cpf)->get();
        json_decode(json_encode($dadosBD[0]), true);


        $dataForm = array('tipo' => $request->tipo);

        foreach ($dataForm as $k => $v) {
            if ($dataForm[$k] != json_decode(json_encode($dadosBD[0]), true)[$k]) {

                // se apagar um dado no form
                if (empty($dataForm[$k])) {

                    $alteracoes = "Exclusão de dados do campo " . ucfirst(str_replace("_", " ", $k)) . ": <i><strong>" . $dataForm[$k] . "</i></strong><br>";
                    array_push($diferencas, $alteracoes);

                }

                // se inserir um dado no form
                if(empty(json_decode(json_encode($dadosBD[0]), true)[$k]))
                {
                    $alteracoes = "Inclusão de dados do campo " . ucfirst(str_replace("_", " ", $k)) . ": <i><strong>" . $dataForm[$k] . "</i></strong><br>";
                    array_push($diferencas, $alteracoes);

                }
                // se alterar um dado no form
                else {

                    $alteracoes = "Alteração de dados no campo " . ucfirst(str_replace("_", " ", $k)) . ": De ".json_decode(json_encode($dadosBD[0]), true)[$k] ." Para <i><strong>" . $dataForm[$k] . "</i></strong><br>";
                    array_push($diferencas, $alteracoes);
                }
            }

        }

        //-------------------- LOG -------------------------------------

        $updateUsuario = DB::table('users')->where('cpf', $cpf)->update([
            'tipo' => $request->tipo,
            'updated_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        if ($updateUsuario) {

            if(!empty($diferencas))
            {
                $textoLog = implode(" ", $diferencas);
                LogController::log($dadosBD[0]->id,"users","update", Auth::user()->cpf, $textoLog);
            }

            return redirect()->route('servidor.listarServidores')->with('alert-success', 'O cadastro da usuário foi alterado com sucesso!');

        }else{

            return redirect()->route('servidor.listarServidores')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }


}