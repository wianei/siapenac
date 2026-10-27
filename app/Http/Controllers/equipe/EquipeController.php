<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a Agenda
|
*/

namespace App\Http\Controllers\Equipe;

use App\Classes\Constantes;
use App\Classes\Tdata;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Http\Controllers\Ocorrencia\OcorrenciaController;
use App\Http\Controllers\Permissoes\PermissoesController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EquipeController extends Controller
{

    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->middleware('auth');


    }

    public function callAction($method, $parameters)
    {
        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::verifyPermission("EquipeController", $method);
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

//        $metodosControlador = collect(['gerenciarEquipes','gerenciarEquipesStore']);
//        $metodosAdm = collect(['trocas','trocaCancelar','trocasStore']);
//        $metodosComum = collect(['dia']);
//
//        if($metodosControlador->contains($method))
//        {
//            if (Auth::user()->tipo < 700) {
//
//                return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//            }
//
//        }
//
//        if($metodosAdm->contains($method))
//        {
//            if (Auth::user()->tipo < 600) {
//
//                return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//            }
//
//        }
//
//        if($metodosComum->contains($method))
//        {
//            if (Auth::user()->tipo < 300) {
//
//                return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//            }
//
//        }


        return parent::callAction($method, $parameters);
    }


    public static function getDocumentoPermuta($id)
    {
        $dados = DB::table('equipe_permuta_arquivo')->where('fk_permuta', $id)->get();

        if(count($dados) > 0) {

            return $dados[0]->nome_arquivo;
        }else{
            return null;
        }
    }

    public function dia(Request $request)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $dataInicioPlantao = OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
        $horaInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);


        /**
         * se não realizar uma busca
         * - Evita que mostre todos os resultados sem realizar uma busca
         */
        if(!strpos($url,"?"))
        {

            $relacaoAgentes = DB::table('equipe_dia')
                ->where('data', $dataInicioPlantao)
                ->where('hora', $horaInicioPlantao)
                ->where('unidade',$lotacaoServidor)->where('permuta',null)
                ->orderBy('escala','ASC')->orderBy('status','ASC')
                ->orderBy('lotacaoUnidade','ASC')->get();

            $dadosAgentes = array();

            foreach($relacaoAgentes as $agentes)
            {
                if($agentes->matricula != "" && !is_null($agentes->matricula)){
                    $relAgentes = FacadeController::getServidorMatricula($agentes->matricula);

                    if(is_numeric($agentes->lotacaoUnidade))
                    {
                        $lotacaoUnidade = UnidadesController::getNomePav($agentes->lotacaoUnidade);
                    }else{
                        $lotacaoUnidade = $agentes->lotacaoUnidade;
                    }
                    array_push($dadosAgentes, array(
                        "nome" => ($relAgentes[0]->nome),
                        "status" => $agentes->status,
                        "fone" => $relAgentes[0]->numero,
                        "lotacaoUnidade" => $lotacaoUnidade,
                        "escala" => $agentes->escala,
                        "funcao" => $agentes->funcao,
                        "matricula" => $relAgentes[0]->matricula,
                        "lotacao" => FacadeController::getLotacaoServidor($relAgentes[0]->cpf),
                        "foto" => config('app.rh')."/storage/app/arquivos/servidores/".$relAgentes[0]->idServidor."/foto/".$relAgentes[0]->idServidor.".jpeg",

                    ));
                }
            }


            $relacaoAgentesPermuta = DB::table('equipe_dia')
                ->where('data', $dataInicioPlantao)
                ->where('hora', $horaInicioPlantao)
                ->where('permuta', "S")
                ->where('unidade',$lotacaoServidor)
                ->orderBy('data','ASC')->get();

            $dadosAgentesPermuta = array();

            foreach($relacaoAgentesPermuta as $agentesPermuta)
            {
                $relAgentesA = FacadeController::getServidorMatricula($agentesPermuta->matricula);
                $relAgenteB = FacadeController::getServidorMatricula($agentesPermuta->permutaMat);

                array_push($dadosAgentesPermuta, array(
                    "id" => $agentesPermuta->id,
                    "nome" => utf8_encode($relAgentesA[0]->nome),
                    "fone" => $relAgentesA[0]->numero,
                    "matricula" => $relAgentesA[0]->matricula,
                    "data" => Tdata::TdataBR($agentesPermuta->data),
                    "status" => $agentesPermuta->status,
                    "permutaMat" => $agentesPermuta->permutaMat,
                    "permutaNome" => utf8_encode($relAgenteB[0]->nome),
                    "permutaData" => Tdata::TdataBR($agentesPermuta->permutaData),
                    "foto" => config('app.rh')."/storage/app/arquivos/servidores/".$relAgentesA[0]->idServidor."/foto/".$relAgentesA[0]->idServidor.".jpeg",
                    "fotoB" => config('app.rh')."/storage/app/arquivos/servidores/".$relAgenteB[0]->idServidor."/foto/".$relAgenteB[0]->idServidor.".jpeg",

                ));
            }



        }
        
        else {
            
        $horaInicioPlantaoBusca = $request->get('horaInicioPlantao');


            $unidadeBusca = $request->get('unidade');
            $dataBusca = $request->get('data');

            $relacaoAgentesPermuta = DB::table('equipe_dia');
            $relacaoAgentes = DB::table('equipe_dia');

                if($dataBusca != null) {
                    $relacaoAgentes->where('data', Tdata::TdataMysql($dataBusca));
                    $relacaoAgentes->where('hora', $horaInicioPlantaoBusca);
                    $relacaoAgentesPermuta->where('data', Tdata::TdataMysql($dataBusca));
                    $relacaoAgentesPermuta->where('hora', $horaInicioPlantaoBusca);

                }else{
                    $relacaoAgentes->where('data', $dataInicioPlantao);
                    $relacaoAgentes->where('hora', $horaInicioPlantaoBusca);
                    $relacaoAgentesPermuta->where('data', $dataInicioPlantao);
                    $relacaoAgentesPermuta->where('hora', $horaInicioPlantaoBusca);
                }
                if($unidadeBusca){
                    $relacaoAgentes->where('unidade', $unidadeBusca);
                    $relacaoAgentesPermuta->where('unidade', $unidadeBusca);
                }else{
                    $relacaoAgentes->where('unidade', $lotacaoServidor);
                    $relacaoAgentesPermuta->where('unidade', $lotacaoServidor);
                }
                $relacaoAgentes->orderBy('escala','ASC');
                $relacaoAgentes->orderBy('status','ASC');
                $relacaoAgentes->where('permuta', null);
                $relacaoAgentes->orderBy('lotacaoUnidade','ASC');
                $equipe = $relacaoAgentes->get();

            $dadosAgentes = array();

            foreach($equipe as $agentes)
            {
                $relAgentes = FacadeController::getServidorMatricula($agentes->matricula);

                if(is_numeric($agentes->lotacaoUnidade))
                {
                    $lotacaoUnidade = UnidadesController::getNomePav($agentes->lotacaoUnidade);
                }else{
                    $lotacaoUnidade = $agentes->lotacaoUnidade;
                }
                array_push($dadosAgentes, array(
                    "nome" => utf8_encode($relAgentes[0]->nome),
                    "status" => $agentes->status,
                    "fone" => $relAgentes[0]->numero,
                    "lotacaoUnidade" => $lotacaoUnidade,
                    "escala" => $agentes->escala,
                    "funcao" => $agentes->funcao,
                    "matricula" => $relAgentes[0]->matricula,
                    "lotacao" => FacadeController::getLotacaoServidor($relAgentes[0]->cpf),
                    "foto" => config('app.rh')."/storage/app/arquivos/servidores/".$relAgentes[0]->id."/foto/".$relAgentes[0]->id.".jpeg",

                ));
            }


                $relacaoAgentesPermuta->where('permuta', "S");
                $relacaoAgentesPermuta->orderBy('data','ASC');
                $equipePermuta = $relacaoAgentesPermuta->get();

            $dadosAgentesPermuta = array();

            foreach($equipePermuta as $agentesPermuta)
            {
                $relAgentesA = FacadeController::getServidorMatricula($agentesPermuta->matricula);
                $relAgenteB = FacadeController::getServidorMatricula($agentesPermuta->permutaMat);

                array_push($dadosAgentesPermuta, array(
                    "id" => $agentesPermuta->id,
                    "nome" => utf8_encode($relAgentesA[0]->nome),
                    "fone" => $relAgentesA[0]->numero,
                    "matricula" => $relAgentesA[0]->matricula,
                    "data" => Tdata::TdataBR($agentesPermuta->data),
                    "status" => $agentesPermuta->status,
                    "permutaMat" => $agentesPermuta->permutaMat,
                    "permutaNome" => utf8_encode($relAgenteB[0]->nome),
                    "permutaData" => Tdata::TdataBR($agentesPermuta->permutaData),
                    "foto" => config('app.rh')."/storage/app/arquivos/servidores/".$relAgentesA[0]->idServidor."/foto/".$relAgentesA[0]->idServidor.".jpeg",
                    "fotoB" => config('app.rh')."/storage/app/arquivos/servidores/".$relAgenteB[0]->idServidor."/foto/".$relAgenteB[0]->idServidor.".jpeg",

                ));
            }



        }

        $unidades = DB::table('unidade')->where('status','1')->orderBy('sigla', 'ASC')->get();

        return view('equipe.dia', compact('dadosAgentes','dadosAgentesPermuta','unidades'));
    }


    public function status()
    {
        return view('equipe.status');
    }


    public function trocas()
    {
        $lotacaoServidor = FacadeController::getLotacaoServidor(Auth::user()->cpf);

        $servidoresLotacao = FacadeController::getServidoresDaLotacao($lotacaoServidor);

        $lotacaoServidorLogado = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $relacaoAgentes = DB::table('equipe_dia')->where('permuta', "S")->where('unidade',$lotacaoServidorLogado)
            ->where("data",'>=', Tdata::TdataHojeMysql())
            ->orderBy('data','ASC')->get();

        $dadosAgentes = array();

        foreach($relacaoAgentes as $agentes)
        {
            $relAgentes = FacadeController::getServidorMatricula($agentes->matricula);
            $relAgenteB = FacadeController::getServidorMatricula($agentes->permutaMat);

            array_push($dadosAgentes, array(
                "id" => $agentes->id,
                "nome" => utf8_encode($relAgentes[0]->nome),
                "fone" => $relAgentes[0]->numero,
                "matricula" => $relAgentes[0]->matricula,
                "data" => Tdata::TdataBR($agentes->data),
                "permutaMat" => $agentes->permutaMat,
                "permutaNome" => $relAgenteB[0]->nome,
                "permutaData" => Tdata::TdataBR($agentes->permutaData),
                "foto" => config('app.rh')."/storage/app/arquivos/servidores/".$relAgentes[0]->id."/foto/".$relAgentes[0]->id.".jpeg",
                "fotoB" => config('app.rh')."/storage/app/arquivos/servidores/".$relAgenteB[0]->id."/foto/".$relAgenteB[0]->id.".jpeg",
                "arquivo" => self::getDocumentoPermuta($agentes->id),

            ));
        }

        //dd($relacaoAgentes);

        return view('equipe.trocas', compact('servidoresLotacao','dadosAgentes'));
    }

    public function trocaCancelar($id)
    {
        $lotacaoServidorLogado = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $relacaoAgentes = DB::table('equipe_dia')->where('id', $id)->get();

        foreach($relacaoAgentes as $agentes)
        {
            $dataPlantao = $agentes->data;
            $dataPermuta = $agentes->permutaData;

            $matricula = $agentes->matricula;
            $matriculaPermuta = $agentes->permutaMat;

            if($dataPermuta <= Tdata::TdataHojeMysql() || $dataPlantao <= Tdata::TdataHojeMysql())
            {
                return redirect()->route('equipe.trocas')->with('alert-danger', 'Não foi possível cancelar a permuta, um dos plantões já foi realizado!');

            }else{

                $deleteA = DB::table('equipe_dia')
                    ->where('matricula', $matricula)
                    ->where('data', $dataPlantao)
                    ->where('unidade', $lotacaoServidorLogado)
                    ->where('permuta', 'S')
                    ->where('permutaMat', $matriculaPermuta)
                    ->where('permutaData', $dataPermuta)
                    ->delete();

                // LOG
                $nomeServidor = FacadeController::getNomeServidor($matricula);
                $nomeServidorB = FacadeController::getNomeServidor($matriculaPermuta);
                $alteracoesA = "Cancelamento de permuta de plantão do servidor: <i><strong>".$nomeServidor."(".$matricula.") - Trocou o dia: ".Tdata::TdataBR($dataPlantao)."<br>Com o servidor: ".$nomeServidorB."(".$matriculaPermuta.")</i></strong><br>";
                LogController::log($id,"equipe_dia","delete", Auth::user()->cpf, $alteracoesA);


                $deleteB = DB::table('equipe_dia')
                    ->where('matricula', $matriculaPermuta)
                    ->where('data', $dataPermuta)
                    ->where('unidade', $lotacaoServidorLogado)
                    ->where('permuta', 'S')
                    ->where('permutaMat', $matricula)
                    ->where('permutaData', $dataPlantao)
                    ->delete();

                $alteracoesB = "Cancelamento de permuta de plantão do servidor: <i><strong>".$nomeServidorB."(".$matriculaPermuta.") - Trocou o dia: ".Tdata::TdataBR($dataPermuta)."<br>Com o servidor: ".$nomeServidor."(".$matricula.")</i></strong><br>";
                LogController::log($id,"equipe_dia","delete", Auth::user()->cpf, $alteracoesB);


                if ($id) {

                    return redirect()->route('equipe.trocas')->with('alert-success', 'Permuta cancelada com sucesso!');

                }else{

                    return redirect('equipe.trocas')->with('alert-danger', 'Ocorreu um problema ao processar sua requisição, por favor, tente novamente!');

                }


            }

        }

    }

    public function trocasStore(Request $request)
    {
        //dd($request->all());

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dataA = $request->get('dataA');
        $dataB = $request->get('dataB');

        $agenteA = $request->get('agenteA');
        $agenteB = $request->get('agenteB');

        if($agenteA == $agenteB || $dataA == $dataB)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Servidores e datas não podem ser iguais!');

        }

        $insertServidorA = DB::table('equipe_dia')->insert([
                'matricula' => $agenteA,
                'data' => Tdata::TdataMysql($dataA),
                'unidade' => $lotacaoServidor,
                'permuta' => "S",
                'permutaMat' => $agenteB,
                'permutaData' => Tdata::TdataMysql($dataB),
            ]

        );

        $idA = DB::getPdo()->lastInsertId();


        // LOG
        $nomeServidor = FacadeController::getNomeServidor($agenteA);
        $nomeServidorB = FacadeController::getNomeServidor($agenteB);
        $alteracoesA = "Permuta de plantão do servidor: <i><strong>".$nomeServidor."(".$agenteA.") - Trocou o dia: ".$dataB."<br>Com o servidor: ".$nomeServidorB."(".$agenteB.")</i></strong><br>";
        LogController::log($idA,"equipe_dia","add", Auth::user()->cpf, $alteracoesA);


        $insertServidorB = DB::table('equipe_dia')->insert([
                'matricula' => $agenteB,
                'data' => Tdata::TdataMysql($dataB),
                'unidade' => $lotacaoServidor,
                'permuta' => "S",
                'permutaMat' => $agenteA,
                'permutaData' => Tdata::TdataMysql($dataA),
            ]

        );

        $idB = DB::getPdo()->lastInsertId();


        // se enviar o arquivo
        if($request->file('inputFile')[0] != null) {

            $randomName = str_random(8);

            $qtdArquivo = count($request->file('inputFile'));

            for ($x = 0; $x < $qtdArquivo; $x++) {

                $destinationPath = Constantes::ARQUIVOS_EQUIPE . "/permuta/";

                $name = "permuta_".$randomName."_" . time() . "_" . $request->file('inputFile')[$x]->getClientOriginalName();

                $request->file('inputFile')[$x]->storeAs($destinationPath, $name);

                $insertArquivo = DB::table('equipe_permuta_arquivo')->insert(
                    ['nome_arquivo' => $name,
                    'fk_permuta' => $idA,
                        'created_at' => Tdata::TtimeStampHojeMysql()
                    ]
                );

            }

            $insertArquivo = DB::table('equipe_permuta_arquivo')->insert(
                ['nome_arquivo' => $name,
                    'fk_permuta' => $idB,
                    'created_at' => Tdata::TtimeStampHojeMysql()
                ]
            );


        }


        // LOG
        $alteracoesB = "Permuta de plantão do servidor: <i><strong>".$nomeServidorB."(".$agenteB.") - Trocou o dia: ".$dataA."<br>Com o servidor: ".$nomeServidor."(".$agenteA.")</i></strong><br>";
        LogController::log($idB,"equipe_dia","add", Auth::user()->cpf, $alteracoesB);


        return redirect()->route('equipe.trocas')->with('alert-success', 'Permuta realizada com sucesso!');
    }
    public function diariaoperacional()
    {
        return view('equipe.do');
    }


    public function gerenciarEquipes($returnLivro=0)
    {

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dataInicioPlantao = OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
        $horaInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);

        $relacaoAgentes = DB::table('equipe_dia')->where('data', $dataInicioPlantao)->where('hora', $horaInicioPlantao)->where('unidade',$lotacaoServidor)->get();

        $lotacaoUnidade = DB::table('pavilhao')->where('fk_up',$lotacaoServidor)->where('status','A')->orderBy('nome','ASC')->get();


        $dadosAgentes = array();

        foreach($relacaoAgentes as $agentes)
        {
            $relAgentes = FacadeController::getDadosServidorPorMat($agentes->matricula);

            array_push($dadosAgentes, array(
                "nome" => utf8_encode($relAgentes[0]->nome),
                "status" => $agentes->status,
                "lotacaoUnidade" => $agentes->lotacaoUnidade,
                "escala" => $agentes->escala,
                "funcao" => $agentes->funcao,
                "permuta" => $agentes->permuta,
                "matricula" => $relAgentes[0]->matricula,
                "lotacao" => FacadeController::getLotacaoServidor($relAgentes[0]->cpf),
                "foto" => config('app.rh')."/storage/app/arquivos/servidores/".$relAgentes[0]->id."/foto/".$relAgentes[0]->id.".jpeg",

            ));
        }

        //dd($dadosAgentes);

        return view('equipe.gerenciarequipes', compact('dadosAgentes','lotacaoServidor','lotacaoUnidade','returnLivro'));
    }

    public function gerenciarEquipesStore(Request $request, $returnLivro)
    {
        //dd($request->all());

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dataInicioPlantao = OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
        $horaInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);

        $diferencas = array();

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $agentesRemover = $request->get('agentesRemover');

        if(isset($agentesRemover)){
            foreach ($agentesRemover as $matriculaAgente) {

                $dadosEquipe = DB::table('equipe_dia')->where('data', $dataInicioPlantao)->where('hora',$horaInicioPlantao)->where('matricula', $matriculaAgente)->get();

                $nomeServidor = FacadeController::getNomeServidor($matriculaAgente);
                $nomeUnidade = UnidadesController::getNomeUp($lotacaoServidor);
                $alteracoes = "Exclusão de servidor na equipe do dia: <i><strong>".$nomeServidor."(".$matriculaAgente.")<br>Data: ".Tdata::TdataHoje()."<br>Unidade: ".$nomeUnidade."</i></strong><br>**********<br>";

                LogController::log($dadosEquipe[0]->id,"equipe_dia","delete", Auth::user()->cpf, $alteracoes);

                $deleteAgente = DB::table('equipe_dia')->where('data', $dataInicioPlantao)->where('hora',$horaInicioPlantao)->where('matricula', $matriculaAgente)->delete();

            }

        }

        $servidores = $request->get('agentesHidden');
        if(isset($servidores))
        {

            $qtdAgentes = count($request->get('agentesHidden'));
            $status = $request->get('status');
            $escala = $request->get('escala');
            $funcao = $request->get('funcao');
            $lotacaoUnidade = $request->get('lotacaoUnidade');


            for ($x = 0; $x < $qtdAgentes; $x++) {

                $verificaServidor = DB::table('equipe_dia')->where('data', $dataInicioPlantao)->where('hora',$horaInicioPlantao)->where('matricula', $servidores[$x])->where('unidade', $lotacaoServidor)->get();

                if (!empty(json_decode($verificaServidor, true))) {
                    $updateEquipe = DB::table('equipe_dia')->where('data', $dataInicioPlantao)->where('hora',$horaInicioPlantao)->where('matricula', $servidores[$x])->where('unidade', $lotacaoServidor)->update([
                        'lotacaoUnidade' => $lotacaoUnidade[$x],
                        'status' => $status[$x],
                        'escala' => $escala[$x],
                        'funcao' => $funcao[$x],

                    ]);
                }

                if (empty(json_decode($verificaServidor, true))) {
                    $insertServidores = DB::table('equipe_dia')->insert([
                            'matricula' => $servidores[$x],
                            'data' => Tdata::TdataMysql($dataInicioPlantao),
                            'hora' => $horaInicioPlantao,
                            'unidade' => $lotacaoServidor,
                            'lotacaoUnidade' => $lotacaoUnidade[$x],
                            'status' => $status[$x],
                            'escala' => $escala[$x],
                            'funcao' => $funcao[$x],
                        ]

                    );

                    $idInsertServidor = DB::getPdo()->lastInsertId();

                    $nomeServidor = FacadeController::getNomeServidor($servidores[$x]);
                    $nomeUnidade = UnidadesController::getNomeUp($lotacaoServidor);
                    $alteracoes = "Inserção de servidor na equipe do dia: <i><strong>".$nomeServidor."(".$servidores[$x].")<br>Data: ".$request->get('data')." - Horário: ".$horaInicioPlantao."<br>Unidade: ".$nomeUnidade."<br>Lotação na unidade: ".$lotacaoUnidade[$x]."<br>Status: ".$status[$x]."<br>Escala: ".$escala[$x]."<br>Função: ".$funcao[$x]."</i></strong><br>**********<br>";
                    array_push($diferencas, $alteracoes);

                }
            }

        }

        if ($lotacaoServidor) {


            $textoLog = implode(" ", $diferencas);
            if(!empty($textoLog))
            {
                LogController::log($idInsertServidor,"equipe_dia","add", Auth::user()->cpf, $textoLog);
            }

            if($returnLivro == 1)
            {
                return redirect()->route('ocorrencia.livro')->with('alert-success', 'Servidor(es) inseridos com sucesso!');

            }else{
                return redirect()->route('equipe.dia')->with('alert-success', 'Servidor(es) inseridos com sucesso!');
            }

        }else{

            return redirect('equipe.dia')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }

}