<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a Armaria
|
*/

namespace App\Http\Controllers\Ocorrencia;

use App\Advogado;
use App\Classes\Constantes;
use App\Classes\Tdata;
use App\Http\Controllers\Advogado\AdvogadoController;
use App\Http\Controllers\Arquivo\ArquivoController;
use App\Http\Controllers\ControleAcesso\ControleAcessoController;
use App\Http\Controllers\Permissoes\PermissoesController;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Interno\InternoController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Http\Controllers\Visita\VisitaController;
use App\Visita;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OcorrenciaController extends Controller
{
    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->middleware('auth');


    }

    public function callAction($method, $parameters)
    {
        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::verifyPermission("OcorrenciaController", $method);
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

//        $metodosControlador = collect(['livroFechamento','livroFechamentoStore']);
//        $metodosAdm = collect(['livroDownload']);
//        $metodosComum = collect(['cadastro','store','editar','editarStore','deletarArquivo','buscar','visualizarOcorrencia','excluirOcorrencia','livro']);
//
//        if($metodosControlador->contains($method))
//        {
//            if (Auth::user()->tipo < 700 && Auth::user()->tipo != 500) {
//
//                return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//            }
//
//        }
//
//        if($metodosAdm->contains($method))
//        {
//            if (Auth::user()->tipo < 600 && Auth::user()->tipo != 500) {
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

    /*
    |--------------------------------------------------------------------------
    | Funcoes acessadas via ajax
    |--------------------------------------------------------------------------
    |
    */
    
   public function getNaturezaObjeto(Request $request) {
       
       $objetos = DB::table('ocorrencia_natureza_objeto')->where('fk_natureza', $request->get('tipo'))->get();
       return $objetos;
   }
    
    public function getOcorrenciaEnvolvidosAjax(Request $request)
    {

        $id = $request->get('idEnvolvido');
        $tipo = $request->get('tipo');
        $dataInicio = Tdata::TdataMysql($request->get('dataInicio'));
        $dataFinal = Tdata::TdataMysql($request->get('dataFinal'));

        $dadosOcorrenciasAjax = DB::table('ocorrencia_envolvidos');
        $dadosOcorrenciasAjax->leftJoin('ocorrencia','ocorrencia.id','=','ocorrencia_envolvidos.fk_ocorrencia');
        $dadosOcorrenciasAjax->leftJoin('ocorrencia_natureza','ocorrencia_natureza.id','=','ocorrencia.natureza');

        $dadosOcorrenciasAjax->where('ocorrencia_envolvidos.fk_tabela', $tipo);
        $dadosOcorrenciasAjax->where('ocorrencia.status','A');
        $dadosOcorrenciasAjax->where('ocorrencia_envolvidos.fk_id_envolvido', $id);

        if(!empty($dataInicio) && !empty($dataFinal) )
        {
            $dadosOcorrenciasAjax->where('data', '>=', $dataInicio);
            $dadosOcorrenciasAjax->where('data', '<=', $dataFinal);
        }

        if(!empty($dataInicio) && empty($dataFinal) )
        {
            $dadosOcorrenciasAjax->where('data', '>=', $dataInicio);
        }

        if(empty($dataInicio) && !empty($dataFinal) )
        {
            $dadosOcorrenciasAjax->where('data', '<=', $dataFinal);
        }

        $dadosOcorrencias = $dadosOcorrenciasAjax->select('ocorrencia.*', 'ocorrencia.id as idOcorrencia','ocorrencia_envolvidos.*','ocorrencia_natureza.natureza as naturezaOcorrencia')->orderBy('ocorrencia.data','DESC')->get();

        $ocorrencias = array();
        foreach($dadosOcorrencias as $dadosOcorrencia)
        {

            // ------------------------------------------------------

            $dadosServidores = DB::table('ocorrencia_envolvidos')->where('fk_tabela', 'servidor')->where('fk_ocorrencia', $dadosOcorrencia->idOcorrencia)->get();
            $dadosServidoresArray = array();
            if(count($dadosServidores) > 0)
            {
                foreach($dadosServidores as $dadosServidore) {

                    array_push($dadosServidoresArray, array(
                        'nomeAgente' => FacadeController::getNomeServidor($dadosServidore->fk_id_envolvido),
                        'matricula' => $dadosServidore->fk_id_envolvido

                    ));
                }

            }else{
                $textoServidores = null;
            }

            $textoServidores = "";
            if ($dadosServidoresArray != null) {
                foreach ($dadosServidoresArray as $servidor) {
                    $textoServidores .= $servidor['nomeAgente'] . " (" . $servidor['matricula'] . "), ";
                }
            }

            // -------------------------------------------------------

            $dadosVisitantes = DB::table('ocorrencia_envolvidos')->where('fk_tabela', 'visita')->where('fk_ocorrencia', $dadosOcorrencia->idOcorrencia)->get();
            $dadosVisitanteArray = array();
            if(count($dadosVisitantes) > 0)
            {
                foreach($dadosVisitantes as $dadosVisitante) {

                    array_push($dadosVisitanteArray, array(
                        'nomeVisita' => VisitaController::getNomeVisita($dadosVisitante->fk_id_envolvido),
                        'cpfVisita' => VisitaController::getCpfVisita($dadosVisitante->fk_id_envolvido),

                    ));
                }

            }else{
                $textoVisitantes = null;
            }

            $textoVisitantes = "";
            if ($dadosVisitanteArray != null) {
                foreach ($dadosVisitanteArray as $visita) {
                    $textoVisitantes .= $visita['nomeVisita'] . " (" . $visita['cpfVisita'] . "), ";
                }
            }

            // -------------------------------------------------------

            $dadosInternos = DB::table('ocorrencia_envolvidos')->where('fk_tabela', 'interno')->where('fk_ocorrencia', $dadosOcorrencia->idOcorrencia)->get();
            $dadosInternoArray = array();
            if(count($dadosInternos) > 0)
            {
                foreach($dadosInternos as $dadosInterno) {

                    array_push($dadosInternoArray, array(
                        'nomeInterno' => InternoController::getInternoNome($dadosInterno->fk_id_envolvido),
                        'prontuario' => $dadosInterno->fk_id_envolvido,

                    ));
                }

            }else{
                $textoInternos = null;
            }

            $textoInternos = "";
            if ($dadosInternoArray != null) {
                foreach ($dadosInternoArray as $interno) {
                    $textoInternos .= $interno['nomeInterno'] . " (" . $interno['prontuario'] . "), ";
                }
            }

            // -------------------------------------------------------


            $dadosAdvs = DB::table('ocorrencia_envolvidos')->where('fk_tabela', 'advogado')->where('fk_ocorrencia', $dadosOcorrencia->idOcorrencia)->get();
            $dadosAdvsArray = array();
            if(count($dadosAdvs) > 0)
            {
                foreach($dadosAdvs as $dadosAdv) {

                    array_push($dadosAdvsArray, array(
                        'nomeAdv' => AdvogadoController::getNomeAdv($dadosAdv->fk_id_envolvido),
                        'oab' => AdvogadoController::getOabAdv($dadosAdv->fk_id_envolvido),

                    ));
                }

            }else{
                $textoAdvs = null;;
            }

            $textoAdvs = "";
            if ($dadosAdvsArray != null) {
                foreach ($dadosAdvsArray as $adv) {
                    $textoAdvs .= $adv['nomeAdv'] . " (" . $adv['oab'] . "), ";
                }
            }

            //$textoAdvs = null;

            // -------------------------------------------------------

            $dadosLog = DB::table('log')->where('tabela_alterada', 'ocorrencia')->where('tipo_alteracao', 'add')->where('fk_id', $dadosOcorrencia->idOcorrencia)->get();
            $logArray = array();
            foreach ($dadosLog as $log) {
                $nomeServidor = FacadeController::getNomeServidorCpf($log->cpf_usuario);

                array_push($logArray, array(
                    'data' => Tdata::TdataBR(Tdata::getDataTimestamp($log->created_at)),
                    'hora' => Tdata::getTempoTimestamp($log->created_at),
                    'tabela' => str_replace("_", " ", $log->tabela_alterada),
                    'descricao' => $log->texto,
                    'realizadoPor' => $nomeServidor . " (" . $log->cpf_usuario . ")",
                ));
            }
            $textoLog = "";
            if ($logArray != null) {
                foreach ($logArray as $l) {
                    $textoLog .= "Registrado por: ".$l['realizadoPor']." em ".$l['data']." às ".$l['hora'];
                }
            }

            // -------------------------------------------------------

            array_push($ocorrencias, array(
                'unidade' => UnidadesController::getNomeUp($dadosOcorrencia->unidade),
                'naturezaOcorrencia' => $dadosOcorrencia->naturezaOcorrencia,
                'idOcorrencia' => $dadosOcorrencia->idOcorrencia,
                'data' => Tdata::TdataBR($dadosOcorrencia->data),
                'dia' => Tdata::getDiaData($dadosOcorrencia->data),
                'ano' => Tdata::getAnoData($dadosOcorrencia->data),
                'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($dadosOcorrencia->data)),
                'horario' => $dadosOcorrencia->horario,
                'servidoresEnvolvidos' => $textoServidores,
                'visitasEnvolvidas' => $textoVisitantes,
                'internosEnvolvidos' => $textoInternos,
                'advsEnvolvidos' => $textoAdvs,
                'dadosLog' => $textoLog,


            ));
        }

        return $ocorrencias;

    }

    /*
    |--------------------------------------------------------------------------
    | Getters e Setters
    |--------------------------------------------------------------------------
    |
    */
    public static function getNaturezaOcorrencia($id)
    {
        $dados = DB::table('ocorrencia_natureza')->where('id',$id)->get();

        return $dados[0]->natureza;
    }
    public function cadastro()
    {
        /**
         * define a data e hora de inicio e termino do plantao
         */
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dataInicioPlantao = self::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = self::getDataProxFechamentoLivroUnidade($lotacaoServidor);

        // calcula os horarios de inicio e termino do plantão
        $timestampHoraProximoFechamento = self::getDataUltimoFechamentoLivro($lotacaoServidor)." ".self::getHorarioReferenciaProxFechamentoLivroUnidade($lotacaoServidor);
        $date = new DateTime($timestampHoraProximoFechamento);
        $date->sub(new \DateInterval(('PT1S')));

        $timestampHoraUltimoFechamento = self::getDataUltimoFechamentoLivro($lotacaoServidor)." ".self::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);



        $ocorrenciaHoraInicioPlantao = Tdata::getTempoTimestamp($timestampHoraUltimoFechamento.":00");
        $ocorrenciaDataInicioPlantao = $dataInicioPlantao." ".$ocorrenciaHoraInicioPlantao;
        $ocorrenciaHoraTerminoPlantao = Tdata::getTempoTimestamp($date->format('Y-m-d H:i:s'));
        $ocorrenciaDataTerminoPlantao = self::getDataProxFechamentoLivroUnidade($lotacaoServidor)." ".$ocorrenciaHoraTerminoPlantao;

        $naturezaOcorrencia = DB::table('ocorrencia_natureza')->orderBy('natureza','ASC')->get();

        $ocorrencias = DB::table('ocorrencia')->where('status',"A")
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'>=', $ocorrenciaDataInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'<=', $ocorrenciaDataTerminoPlantao)
            ->where('unidade', $lotacaoServidor)
            ->orderBy('ocorrencia.data','DESC')->orderBy('ocorrencia.horario','DESC')->get();

        $dadosOcorrencia = array();

        $envol = "";

        foreach($ocorrencias as $ocorrencia)
        {
            $envolvidos = DB::table('ocorrencia_envolvidos')->where('fk_ocorrencia', $ocorrencia->id)->get();

            $arquivos = DB::table('ocorrencia_arquivos')->where('fk_ocorrencia', $ocorrencia->id)->get();

            $envolvidosArray = array();

            foreach(json_decode($envolvidos, true) as $envolvido)
            {
                if($envolvido['fk_tabela'] == "servidor")
                {
                    $envol = $envol."agnt,";
                   array_push($envolvidosArray, array(
                       'servidor' => FacadeController::getDadosServidorPorMat($envolvido['fk_id_envolvido'])
                   ));
                }

                if($envolvido['fk_tabela'] == "interno")
                {
                    $envol = $envol."inter,";
                   array_push($envolvidosArray, array(
                       'interno' => InternoController::getDadosPrincipaisInterno($envolvido['fk_id_envolvido'])
                   ));
                }

                if($envolvido['fk_tabela'] == "advogado")
                {
                    $envol = $envol."adv,";
                   array_push($envolvidosArray, array(
                       'advogado' => AdvogadoController::getDadosAdv($envolvido['fk_id_envolvido'])
                   ));
                }

                if($envolvido['fk_tabela'] == "visita")
                {
                   $envol = $envol."vis,";
                   array_push($envolvidosArray, array(
                       'visita' => VisitaController::getDadosVisita ($envolvido['fk_id_envolvido'])
                   ));
                }


            }

            // dados Log
            if(count(LogController::getDadosLog($ocorrencia->id, "ocorrencia")) != 0) {
                $dadosLog = LogController::getDadosLog($ocorrencia->id, "ocorrencia");
                $userLog = FacadeController::getDadosServidor($dadosLog[0]->cpf_usuario, "nome");
                $textoLog = "Registrado por: " . $userLog[0]->nome . " (" . $dadosLog[0]->cpf_usuario . ") em " . Tdata::TtimestampBR($dadosLog[0]->created_at);
            }
            else{
                $textoLog = "Sem log registrado.";
            }

            array_push($dadosOcorrencia, array(
                "id" => $ocorrencia->id,
                "natureza" => self::getNaturezaOcorrencia($ocorrencia->natureza),
                "data" => Tdata::TdataBR($ocorrencia->data),
                "atualizacao" => Tdata::TtimestampBR($ocorrencia->update_at),
                "horario" => $ocorrencia->horario,
                "descricao" => $ocorrencia->descricao,
                "arquivos" => $arquivos,
                "envol" => $envol,
                "registro" => $textoLog,
                "envolvidos" => $envolvidosArray

             ));

        }
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $rtLivro = explode("/",$url);
        if(isset($rtLivro[6]))
        {
            $returnLivro = $rtLivro[6];

        }else{
            $returnLivro = 0;
        }


        return view('ocorrencia.cadastro.cadastro', compact('naturezaOcorrencia','dadosOcorrencia','returnLivro'));
    }

    public function store(Request $request, $id, $returnLivro)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dataInicioPlantao = self::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = self::getDataProxFechamentoLivroUnidade($lotacaoServidor);
        $horaInicioPlantao = self::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
        $horaTerminoPlantao = self::getHorarioReferenciaProxFechamentoLivroUnidade($lotacaoServidor);

        $stringInfoPlantao = "O livro de ocorrência em aberto inicia no dia ". Tdata::TdataBR(self::getDataUltimoFechamentoLivro($lotacaoServidor)) ." às ".self::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor)." e vai até ". Tdata::TdataBR(self::getDataProxFechamentoLivroUnidade($lotacaoServidor))." às ".self::getHorarioReferenciaProxFechamentoLivroUnidade($lotacaoServidor);


        /**
         * validacoes
         */

        $dataValidar = $request->get('dt_ocorrencia');
        $horaValidar = $request->get('hr_ocorrencia');
        $naturezaValidar = $request->get('natureza');
        $descricaoValidar = $request->get('descricao');

        if(Tdata::TdataMysql($dataValidar)." ".$horaValidar < $dataInicioPlantao." ".$horaInicioPlantao || Tdata::TdataMysql($dataValidar)." ".$horaValidar > $dataTerminoPlantao." ".$horaTerminoPlantao)
        {
            return redirect()->back()->withInput()->with('alert-danger', '<strong>ATENÇÃO! </strong>Você não pode inserir uma ocorrência para datas passadas ou futuras! '.$stringInfoPlantao);
        }
        if($dataValidar == null ||$horaValidar == null ||$naturezaValidar == null ||$descricaoValidar == null)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Preencha os campos obrigatórios!');

        }

        //--------------------- !validacoes ----------------------------------

        // LOG - array que armazena os textos do log
        $diferencas = array();
        $alteracoes = "Criação de ocorrência<br>";
        array_push($diferencas, $alteracoes);


        $insertOcorrencia = DB::table('ocorrencia')->insert([
                'unidade' => $lotacaoServidor,
                'natureza' => $request->get('natureza'),
                'natureza_objeto' => $request->get('tipo_objeto'),
                'data' => Tdata::TdataMysql($request->get('dt_ocorrencia')),
                'horario' => $request->get('hr_ocorrencia'),
                'descricao' => $request->get('descricao'),
                'status' => 'A',
                'created_at' => date("Y-m-d H:i:s"),
            ]
        );

        $idOcorrencia = DB::getPdo()->lastInsertId();

        $servidores = $request->get('agentesHidden');
        if(isset($servidores))
        {
            foreach($servidores as $servidor)
            {
                $insertServidores = DB::table('ocorrencia_envolvidos')->insert([
                        'fk_ocorrencia' => $idOcorrencia,
                        'fk_tabela' => "servidor",
                        'fk_id_envolvido' => $servidor,
                    ]
                );

                // LOG
                $dadosServidor = FacadeController::getDadosServidorPorMat($servidor);
                $alteracoes = "Inserção de servidor na ocorrência: <i><strong>".$dadosServidor[0]->nome."(".$servidor.")</i></strong><br>";
                array_push($diferencas, $alteracoes);


            }

        }


        $internos = $request->get('internos');
        if(isset($internos)) {
            foreach ($internos as $interno) {
                $insertInternos = DB::table('ocorrencia_envolvidos')->insert([
                        'fk_ocorrencia' => $idOcorrencia,
                        'fk_tabela' => "interno",
                        'fk_id_envolvido' => $interno,
                    ]
                );

                //LOG
                $nomeInterno = InternoController::getInternoNome($interno);
                $alteracoes = "Inserção de interno na ocorrência: <i><strong>".$nomeInterno."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }
        }

        if($request->get('natureza') == 35)
        {
            $internosObito = $request->get('checkInternoObito');
            if(isset($internosObito)) {
                foreach ($internosObito as $internoObito) {
                    $insertInternos = DB::table('ocorrencia_envolvidos')->insert([
                            'fk_ocorrencia' => $idOcorrencia,
                            'fk_tabela' => "interno",
                            'fk_id_envolvido' => $internoObito,
                        ]
                    );

                    //LOG
                    $nomeInternObito = InternoController::getInternoNome($internoObito);
                        $alteracoes = "Inserção de interno que foi a óbito: <i><strong>".$nomeInternObito."</i></strong><br>";
                    array_push($diferencas, $alteracoes);

                }
            }


        }

        $advogados = $request->get('advogados');
        if(isset($advogados)) {
            foreach ($advogados as $advogado) {
                $insertServidores = DB::table('ocorrencia_envolvidos')->insert([
                        'fk_ocorrencia' => $idOcorrencia,
                        'fk_tabela' => "advogado",
                        'fk_id_envolvido' => $advogado,
                    ]
                );

                $nomeAdv = AdvogadoController::getDadosAdv($advogado);
                $alteracoes = "Inserção de advogado na ocorrência: <i><strong>".$nomeAdv[0]->nome."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }
        }

        $visitas = $request->get('visitas');
        if(isset($visitas)) {
            foreach ($visitas as $visita) {
                $insertServidores = DB::table('ocorrencia_envolvidos')->insert([
                        'fk_ocorrencia' => $idOcorrencia,
                        'fk_tabela' => "visita",
                        'fk_id_envolvido' => $visita,
                    ]
                );

                $nomeVisita = VisitaController::getDadosVisita($visita);
                $alteracoes = "Inserção de visita na ocorrência: <i><strong>".$nomeVisita[0]->nome."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }
        }

        // se enviar o arquivo
        if($request->file('inputFile')[0] != null) {

            $qtdArquivo = count($request->file('inputFile'));

            for ($x = 0; $x < $qtdArquivo; $x++) {

                $destinationPath = Constantes::ARQUIVOS_OCORRENCIA . "/" . $idOcorrencia;

                $name = "ocorrencia".$idOcorrencia . "_" . time() . "_" . $request->file('inputFile')[$x]->getClientOriginalName();

                $request->file('inputFile')[$x]->storeAs($destinationPath, $name);

                $insertArquivo = DB::table('ocorrencia_arquivos')->insert(
                    ['fk_ocorrencia' => $idOcorrencia,
                        'nome_arquivo' => $name,
                        'created_at' => date("Y-m-d H:i:s")
                    ]
                );

                $alteracoes = "Inserção de arquivo na ocorrência: <i><strong>".$name."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }
        }

        if ($idOcorrencia) {

            $textoLog = implode(" ", $diferencas);
            LogController::log($idOcorrencia,"ocorrencia","add", Auth::user()->cpf, $textoLog);

            if($returnLivro == 1)
            {
                return redirect()->route('ocorrencia.livro')->with('alert-success', 'Ocorrência inserida com sucesso!');

            }else {

                return redirect()->route('ocorrencia.buscar')->with('alert-success', 'Ocorrência inserida com sucesso!');
            }

        }else{

            return redirect()->route('ocorrencia.buscar')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }

    public function editar($id, $returnLivro=0)
    {

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $naturezaOcorrencia = DB::table('ocorrencia_natureza')->orderBy('natureza','ASC')->get();

        $ocorrencia = DB::table('ocorrencia')->select('ocorrencia.*', DB::raw("DATE_FORMAT(ocorrencia.data,'%d/%m/%Y') as data"))->where('id', $id)->get();

        if($ocorrencia[0]->unidade != $lotacaoServidor)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para alterar as ocorrências de outra unidade!');

        }

        $dadosLog = LogController::getDadosLogService($id, "ocorrencia");
        $permissaoFechamento = PermissoesController::verifyPermission("OcorrenciaController", "livroFechamentoStore");
        if(Auth::user()->cpf != $dadosLog->cpf_usuario)
        {
            if(!$permissaoFechamento){
                return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para alterar as ocorrências criadas por outros usuários');
            }

        }

        $relacaoInterno = DB::table('ocorrencia_envolvidos')->where('fk_ocorrencia', $id)->where('fk_tabela','interno')->get();
        $dadosInterno = array();
        foreach($relacaoInterno as $relacao)
        {
            $dadosInternos = (InternoController::getDadosPrincipaisInterno($relacao->fk_id_envolvido));


            array_push($dadosInterno, array(
                "nome" => $dadosInternos[0]['nome'],
                "id" => $dadosInternos[0]['id'],
                "faccao" => $dadosInternos[0]['faccao'],
                "lideranca" => $dadosInternos[0]['lideranca'],
                "foto" => $dadosInternos[0]['foto'],
                "sigla" => $dadosInternos[0]['sigla']
            ));

        }

        $relacaoAgentes = DB::table('ocorrencia_envolvidos')->where('fk_ocorrencia', $id)->where('fk_tabela','servidor')->get();
        $dadosAgentes = array();
        foreach($relacaoAgentes as $agentes)
        {
            $relAgentes = FacadeController::getDadosServidorPorMat($agentes->fk_id_envolvido);

            array_push($dadosAgentes, array(
                "nome" => utf8_encode($relAgentes[0]->nome),
                "matricula" => $relAgentes[0]->matricula,
                "lotacao" => FacadeController::getLotacaoServidor($relAgentes[0]->cpf),
                "foto" => config('app.rh')."/storage/app/arquivos/servidores/".$relAgentes[0]->id."/foto/".$relAgentes[0]->id.".jpeg",
            ));
        }

        $relacaoAgentes = DB::table('ocorrencia_envolvidos')->where('fk_ocorrencia', $id)->where('fk_tabela','servidor')->get();
        $dadosAgentes = array();
        foreach($relacaoAgentes as $agentes)
        {
            $relAgentes = FacadeController::getDadosServidorPorMat($agentes->fk_id_envolvido);

            array_push($dadosAgentes, array(
                "nome" => $relAgentes[0]->nome,
                "matricula" => $relAgentes[0]->matricula,
                "lotacao" => FacadeController::getLotacaoServidor($relAgentes[0]->cpf),
                "foto" => config('app.rh')."/storage/app/arquivos/servidores/".$relAgentes[0]->id."/foto/".$relAgentes[0]->id.".jpeg",
            ));
        }

        $relacaoVisitas = DB::table('ocorrencia_envolvidos')->where('fk_ocorrencia', $id)->where('fk_tabela','visita')->get();
        $dadosVisitas = array();
        foreach($relacaoVisitas as $visitas)
        {
            $relVisitas = VisitaController::getDadosVisita($visitas->fk_id_envolvido);

            array_push($dadosVisitas, array(
                "id" => $visitas->fk_id_envolvido,
                "nome" => $relVisitas[0]->nome,
                "cpf" => $relVisitas[0]->cpf,
                "foto" => VisitaController::getFotoVisita($visitas->fk_id_envolvido),
            ));
        }

        $relacaoAdvogados = DB::table('ocorrencia_envolvidos')->where('fk_ocorrencia', $id)->where('fk_tabela','advogado')->get();
        $dadosAdvogados = array();
        foreach($relacaoAdvogados as $advogados)
        {
            $relAdvogados = AdvogadoController::getDadosAdv($advogados->fk_id_envolvido);

            array_push($dadosAdvogados, array(
                "id" => $advogados->fk_id_envolvido,
                "nome" => $relAdvogados[0]->nome,
                "oab" => $relAdvogados[0]->oab_numero."-".$relAdvogados[0]->oab_uf,
                "foto" => AdvogadoController::getFotoAdv($advogados->fk_id_envolvido),
            ));
        }

        $dadosArquivos = DB::table('ocorrencia_arquivos')->select("*", DB::raw("DATE_FORMAT(created_at,'%d/%m/%Y') as data"))->where('fk_ocorrencia', $id)->get();

        return view('ocorrencia.cadastro.editar', compact('naturezaOcorrencia', 'ocorrencia','dadosInterno','dadosAgentes','dadosVisitas','dadosAdvogados','dadosArquivos','returnLivro'));
    }

    public function editarStore(Request $request, $id, $returnLivro)
    {
        $dadosLog = LogController::getDadosLogService($id, "ocorrencia");
        $permissaoFechamento = PermissoesController::verifyPermission("OcorrenciaController", "livroFechamentoStore");
        if(Auth::user()->cpf != $dadosLog->cpf_usuario)
        {
            if(!$permissaoFechamento){
                return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para alterar as ocorrências criadas por outros usuários');
            }

        }

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dataInicioPlantao = self::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = self::getDataProxFechamentoLivroUnidade($lotacaoServidor);
        $horaInicioPlantao = self::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
        $horaTerminoPlantao = self::getHorarioReferenciaProxFechamentoLivroUnidade($lotacaoServidor);

        $stringInfoPlantao = "O livro de ocorrência em aberto inicia no dia ". Tdata::TdataBR(self::getDataUltimoFechamentoLivro($lotacaoServidor)) ." às ".self::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor)." e vai até ". Tdata::TdataBR(self::getDataProxFechamentoLivroUnidade($lotacaoServidor))." às ".self::getHorarioReferenciaProxFechamentoLivroUnidade($lotacaoServidor);



        /**
         * validacoes
         */

        $dataValidar = $request->get('dt_ocorrencia');
        $horaValidar = $request->get('hr_ocorrencia');
        $naturezaValidar = $request->get('natureza');
        $descricaoValidar = $request->get('descricao');

        if(Tdata::TdataMysql($dataValidar)." ".$horaValidar < $dataInicioPlantao." ".$horaInicioPlantao || Tdata::TdataMysql($dataValidar)." ".$horaValidar > $dataTerminoPlantao." ".$horaTerminoPlantao)
        {
            return redirect()->back()->withInput()->with('alert-danger', '<strong>ATENÇÃO! </strong>Você não pode inserir uma ocorrência para datas passadas ou futuras! '.$stringInfoPlantao);
        }

        
        if($dataValidar == null ||$horaValidar == null ||$naturezaValidar == null ||$descricaoValidar == null)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Preencha os campos obrigatórios!');

        }

        //--------------------- !validacoes ----------------------------------

        //-------------------- LOG -------------------------------------

        // LOG - array que armazena os textos do log
        $diferencas = array();
        $alteracoes = "Alteração da ocorrência<br>";
        array_push($diferencas, $alteracoes);

        $dataForm = array('natureza'=> $request->get('natureza'), 'data' => $request->get('dt_ocorrencia'), 'horario' => $request->get('hr_ocorrencia'), 'descricao' => $request->get('descricao'));

        $dataForm['data'] = Tdata::TdataMysql($dataForm['data']);

        // LOG - array que armazena os textos do log
        $diferencas = array();

        $dadosBD = DB::table("ocorrencia")->where('id', $id)->get();
        json_decode(json_encode($dadosBD[0]), true);


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

                    $alteracoes = "Alteração de dados no campo " . ucfirst(str_replace("_", " ", $k)) . ": <i><strong>" . $dataForm[$k] . "</i></strong><br>";
                    array_push($diferencas, $alteracoes);
                }
            }

        }

        //-------------------- LOG -------------------------------------

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $updatetOcorrencia = DB::table('ocorrencia')->where('id',$id)->update([
                'unidade' => $lotacaoServidor,
                'natureza' => $request->get('natureza'),
                'natureza_objeto' => $request->get('tipo_objeto'),
                'data' => Tdata::TdataMysql($request->get('dt_ocorrencia')),
                'horario' => $request->get('hr_ocorrencia'),
                'descricao' => $request->get('descricao'),
                'status' => 'A',
                'update_at' => date("Y-m-d H:i:s"),
            ]
        );

        // -- gerencia os agentes
        $agentesRemover = $request->get('agentesRemover');
        if(isset($agentesRemover)){
            foreach ($agentesRemover as $matriculaAgente) {
                $deleteAgente = DB::table('ocorrencia_envolvidos')->where('fk_ocorrencia', $id)->where('fk_tabela', 'servidor')->where('fk_id_envolvido', $matriculaAgente)->delete();

                // LOG
                $alteracoes = "Exclusão de servidor da ocorrência: <i><strong>".FacadeController::getNomeServidor($matriculaAgente)."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }

        }
        $servidores = $request->get('agentesHidden');
        if(isset($servidores)) {
            foreach ($servidores as $servidor) {
                $verificaServidor = DB::table('ocorrencia_envolvidos')->where('fk_ocorrencia', $id)->where('fk_tabela', 'servidor')->where('fk_id_envolvido', $servidor)->get();

                if (empty(json_decode($verificaServidor, true))) {

                    $insertServidores = DB::table('ocorrencia_envolvidos')->insert([
                            'fk_ocorrencia' => $id,
                            'fk_tabela' => "servidor",
                            'fk_id_envolvido' => $servidor,
                        ]
                    );

                    // LOG
                    $alteracoes = "Inserção de servidor na ocorrência: <i><strong>".FacadeController::getNomeServidor($servidor)."</i></strong><br>";
                    array_push($diferencas, $alteracoes);

                }

            }
        }

        //-- gerencia os internos
        $internosRemover = $request->get('internosRemover');
        if(isset($internosRemover)){
            foreach ($internosRemover as $idInternoRemover) {
                $deleteAgente = DB::table('ocorrencia_envolvidos')->where('fk_ocorrencia', $id)->where('fk_tabela', 'interno')->where('fk_id_envolvido', $idInternoRemover)->delete();

                // LOG
                $alteracoes = "Exclusão de interno da ocorrencia: <i><strong>".InternoController::getInternoNome($idInternoRemover)."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }

        }
        $internos = $request->input('internos');
        if(isset($internos)) {
            foreach ($internos as $interno) {
                if (is_numeric($interno)) {
                    $verificaInterno = DB::table('ocorrencia_envolvidos')->where('fk_ocorrencia', $id)->where('fk_tabela', 'interno')->where('fk_id_envolvido', $interno)->get();

                    if (empty(json_decode($verificaInterno))) {
                        $insertInternos = DB::table('ocorrencia_envolvidos')->insert([
                                'fk_ocorrencia' => $id,
                                'fk_tabela' => "interno",
                                'fk_id_envolvido' => $interno,
                            ]
                        );

                        // LOG
                        $alteracoes = "Inserção de interno na ocorrência: <i><strong>".InternoController::getInternoNome($interno)."</i></strong><br>";
                        array_push($diferencas, $alteracoes);

                    }
                }
            }
        }

        //-- gerencia visita
        $visitasRemover = $request->get('visitasRemover');
        if(isset($visitasRemover)){
            foreach ($visitasRemover as $visitaRemover) {

                $deleteVisita = DB::table('ocorrencia_envolvidos')->where('fk_ocorrencia', $id)->where('fk_tabela', 'visita')->where('fk_id_envolvido', $visitaRemover)->delete();

                // LOG
                $visitaDados = VisitaController::getDadosVisita($visitaRemover);
                $alteracoes = "Exclusão de visita da ocorrência: <i><strong>".$visitaDados[0]->nome."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }
        }
        $visitas = $request->input('visitas');
        if(isset($visitas)) {
            foreach ($visitas as $visita) {
                if (is_numeric($visita)) {
                    $verificaVisita = DB::table('ocorrencia_envolvidos')->where('fk_ocorrencia', $id)->where('fk_tabela', 'visita')->where('fk_id_envolvido', $visita)->get();

                    if (empty(json_decode($verificaVisita))) {
                        $insertInternos = DB::table('ocorrencia_envolvidos')->insert([
                                'fk_ocorrencia' => $id,
                                'fk_tabela' => "visita",
                                'fk_id_envolvido' => $visita,
                            ]
                        );

                        $visitaDados = VisitaController::getDadosVisita($visitaRemover);
                        $alteracoes = "Inserção de visita na ocorrência: <i><strong>".$visitaDados[0]->nome."</i></strong><br>";
                        array_push($diferencas, $alteracoes);

                    }
                }
            }
        }

        //-- gerencia visita
        $advogadosRemover = $request->get('advogadosRemover');
        if(isset($advogadosRemover)){
            foreach ($advogadosRemover as $advogadoRemover) {
                $deleteAdvogado = DB::table('ocorrencia_envolvidos')->where('fk_ocorrencia', $id)->where('fk_tabela', 'advogado')->where('fk_id_envolvido', $advogadoRemover)->delete();

                // LOG
                $advDados = AdvogadoController::getDadosAdv($advogadoRemover);
                $alteracoes = "Exclusão do advogado da ocorrência: <i><strong>".$advDados[0]->nome."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }
        }
        $advogados = $request->input('advogados');
        if(isset($advogados)) {
            foreach ($advogados as $advogado) {
                if (is_numeric($advogado)) {
                    $verificaAdvogado = DB::table('ocorrencia_envolvidos')->where('fk_ocorrencia', $id)->where('fk_tabela', 'advogado')->where('fk_id_envolvido', $advogado)->get();

                    if (empty(json_decode($verificaAdvogado))) {
                        $insertInternos = DB::table('ocorrencia_envolvidos')->insert([
                                'fk_ocorrencia' => $id,
                                'fk_tabela' => "advogado",
                                'fk_id_envolvido' => $advogado,
                            ]
                        );

                        $advDados = AdvogadoController::getDadosAdv($advogado);
                        $alteracoes = "Inserção de visita na ocorrência: <i><strong>".$advDados[0]->nome."</i></strong><br>";
                        array_push($diferencas, $alteracoes);

                    }
                }
            }
        }


        // se enviar o arquivo
        if($request->file('inputFile')[0] != null) {

            $qtdArquivo = count($request->file('inputFile'));

            for ($x = 0; $x < $qtdArquivo; $x++) {

                $destinationPath = Constantes::ARQUIVOS_OCORRENCIA . "/" . $id;

                $name = "ocorrencia".$id . "_" . time() . "_" . $request->file('inputFile')[$x]->getClientOriginalName();

                $request->file('inputFile')[$x]->storeAs($destinationPath, $name);

                $insertArquivo = DB::table('ocorrencia_arquivos')->insert(
                    ['fk_ocorrencia' => $id,
                        'nome_arquivo' => $name,
                        'created_at' => date("Y-m-d H:i:s")
                    ]
                );

                $alteracoes = "Inserção de arquivo na ocorrência, arquivo: <i><strong>".$name."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }
        }

        if ($id) {

            $textoLog = implode(" ", $diferencas);
            LogController::log($id,"ocorrencia","update", Auth::user()->cpf, $textoLog);

            if($returnLivro == 1)
            {
                return redirect()->route('ocorrencia.livro')->with('alert-success', 'Ocorrência alterada com sucesso!');

            }else {

                return redirect()->route('ocorrencia.buscar')->with('alert-success', 'Ocorrência alterada com sucesso!');
            }

        }else{

            return redirect('ocorrencia.buscar')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }

    public function deletarArquivo($id)
    {
        $delete = ArquivoController::excluirArquivo("ocorrencia_arquivos",$id,Constantes::ARQUIVOS_OCORRENCIA);

        if($delete)
        {
            LogController::log($id,"ocorrencia_arquivos","delete",Auth::user()->cpf,"Exclusão de arquivo da sindicancia");

            return redirect()->route('ocorrencia.buscar')->with('alert-success', 'Arquivo apagado com sucesso!');
        }else{
            return redirect()->route('ocorrencia.buscar')->with('alert-danger', 'Ocorreu um problema ao apagar o arquivo, por favor, tente novamente!');
        }

    }

    public function buscar(Request $request)
    {
        $naturezaOcorrencia = DB::table('ocorrencia_natureza')->orderBy('natureza','ASC')->get();

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        /**
         * se não realizar uma busca
         * - Evita que mostre todos os resultados sem realizar uma busca
         */
        if(!strpos($url,"?"))
        {

            $dados = false;

            $livro = false;

        }else {

            //dd($request->all());

            $livro = false;

            $buscaTipo = $request->get('buscaTipo');

            if ($buscaTipo == "livro") {


                $livro = true;
                $unidade = $request->get('unidade');
                $horaInicioPlantao = $request->get('horaInicioPlantao');

                if ($unidade == null) {
                    $idUnidade = $lotacaoServidor;
                    $nomeUnidade = UnidadesController::getNomeUp($lotacaoServidor);
                } else {

                    $idUnidade = $unidade;
                    $nomeUnidade = UnidadesController::getNomeUp($idUnidade);

                }
                $dados = array('dataLivro' => $request->get('data_inicial'), 'dataLivroMysql' => Tdata::TdataMysql($request->get('data_inicial')), 'idUnidade' => $idUnidade, 'nomeUnidade' => $nomeUnidade, 'horario' => $horaInicioPlantao);

                $getFechamento = DB::table('ocorrencia_fechamento_livro')->where('unidade', $idUnidade)->orderBy('data', 'DESC')->first();

                $dataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor);
                $horarioTerminoPlantao = OcorrenciaController::getHorarioFechamento59Seg($lotacaoServidor);


        if(Tdata::TdataMysql($request->get('data_inicial'))." ".$horaInicioPlantao >= self::getDataUltimoFechamentoLivro($idUnidade)." ".self::getHorarioReferenciaUltimoFechamentoLivro($idUnidade))
                {
                    return redirect()->back()->withInput()->with('alert-danger', 'Livro em aberto! Último livro disponível para download: '.Tdata::TdataBR(self::getDataPenultimoFechamentoLivro($idUnidade))." às ".self::getHorarioReferenciaPenultimoFechamentoLivro($idUnidade));

                    $dados = false;

                }
//        if(Tdata::TdataInteiro(Tdata::TdataMysql($request->get('data_inicial'))) >= Tdata::TdataInteiro(Tdata::addDias($getFechamento->data, 1)))
//                {
//                    return redirect()->back()->withInput()->with('alert-danger', 'Livro em aberto! Último livro disponível para download: '.Tdata::TdataBR($getFechamento->data));
//
//                    $dados = false;
//
//                }


            } else {

                $ocorrencia = DB::table('ocorrencia');

                $ocorrencia->leftJoin('unidade', 'unidade.id', '=', 'ocorrencia.unidade');
                $ocorrencia->leftJoin('ocorrencia_natureza', 'ocorrencia_natureza.id', '=', 'ocorrencia.natureza');

                $ocorrencia->where('ocorrencia.status', 'A');

                $unidade = $request->get('unidade');
                $data_inicial = Tdata::TdataMysql($request->get('data_inicial'));
                $data_final = Tdata::TdataMysql($request->get('data_final'));
                $natureza = $request->get('natureza');

                if ($unidade != null) {
                    $ocorrencia->where('ocorrencia.unidade', $unidade);
                }

                if ($data_inicial != null) {
                    $ocorrencia->where('ocorrencia.data', '>=', $data_inicial);
                }

                if ($data_final != null) {
                    $ocorrencia->where('ocorrencia.data', '<=', $data_final);
                }

                if ($unidade == null) {
                    $ocorrencia->where('ocorrencia.unidade', $lotacaoServidor);
                }

                if ($natureza != null) {
                    $ocorrencia->where('ocorrencia.natureza', $natureza);
                }

                $internos = $request->get('internos');
                $agentes = $request->get('agentesHidden');
                $visitas = $request->get('visitas');
                $advogados = $request->get('advogados');

                if (isset($internos) || isset($agentes) || isset($visitas) || $advogados) {

                    $ocorrencia->leftJoin('ocorrencia_envolvidos', function ($query) {
                        $query->on('ocorrencia_envolvidos.fk_ocorrencia', '=', 'ocorrencia.id');
                    });

                }

                if (isset($internos)) {


                    foreach ($internos as $interno) {
                        $ocorrencia->where('ocorrencia_envolvidos.fk_id_envolvido', $interno)->where('ocorrencia_envolvidos.fk_tabela', 'interno');

                    }
                }

                if (isset($agentes)) {

                    foreach ($agentes as $agente) {
                        $ocorrencia->where('ocorrencia_envolvidos.fk_id_envolvido', $agente)->where('ocorrencia_envolvidos.fk_tabela', 'servidor');

                    }
                }

                if (isset($visitas)) {

                    foreach ($visitas as $visita) {
                        $ocorrencia->where('ocorrencia_envolvidos.fk_id_envolvido', $visita)->where('ocorrencia_envolvidos.fk_tabela', 'visita');

                    }
                }

                if (isset($advogados)) {

                    foreach ($advogados as $advogado) {
                        $ocorrencia->where('ocorrencia_envolvidos.fk_id_envolvido', $advogado)->where('ocorrencia_envolvidos.fk_tabela', 'advogado');

                    }
                }

                // passa a query para a paginacao "->appends..."
                $dados = $ocorrencia->select("unidade.sigla AS unidadePrisional", "ocorrencia.*", 'ocorrencia_natureza.natureza AS naturezaOcorrencia', DB::raw("DATE_FORMAT(ocorrencia.data,'%d/%m/%Y') as data"))->orderBy('ocorrencia.data', 'DESC')->orderBy('ocorrencia.horario', 'DESC')->paginate(50)->appends($request->input());

            }
        }

        $unidades = DB::table('unidade')->where('status','1')->orderBy('sigla', 'ASC')->get();

        return view('ocorrencia.buscar.buscar', compact('naturezaOcorrencia','unidades','dados','livro'));
    }

    public function visualizarOcorrencia($id)
    {

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(AutH::user()->cpf));

        $ocorrencias = DB::table('ocorrencia')->where('id',$id)
            ->orderBy('ocorrencia.data','DESC')->orderBy('ocorrencia.horario','DESC')->get();

        $dadosOcorrencia = array();

        $envol = "";

        foreach($ocorrencias as $ocorrencia)
        {
            $envolvidos = DB::table('ocorrencia_envolvidos')->where('fk_ocorrencia', $ocorrencia->id)->get();

            $arquivos = DB::table('ocorrencia_arquivos')->where('fk_ocorrencia', $ocorrencia->id)->get();

            $envolvidosArray = array();

            foreach(json_decode($envolvidos, true) as $envolvido)
            {
                if($envolvido['fk_tabela'] == "servidor")
                {
                    $envol = $envol."agnt,";
                    array_push($envolvidosArray, array(
                        'servidor' => FacadeController::getDadosServidorPorMat($envolvido['fk_id_envolvido'])
                    ));
                }

                if($envolvido['fk_tabela'] == "interno")
                {
                    $envol = $envol."inter,";
                    array_push($envolvidosArray, array(
                        'interno' => InternoController::getDadosPrincipaisInterno($envolvido['fk_id_envolvido'])
                    ));
                }

                if($envolvido['fk_tabela'] == "advogado")
                {
                    $envol = $envol."adv,";
                    array_push($envolvidosArray, array(
                        'advogado' => AdvogadoController::getDadosAdv($envolvido['fk_id_envolvido'])
                    ));
                }

                if($envolvido['fk_tabela'] == "visita")
                {
                    $envol = $envol."vis,";
                    array_push($envolvidosArray, array(
                        'visita' => VisitaController::getDadosVisita ($envolvido['fk_id_envolvido'])
                    ));
                }


            }

//            dd($envol);

            // dados Log
            if(count(LogController::getDadosLog($ocorrencia->id, "ocorrencia")) != 0) {
                $dadosLog = LogController::getDadosLog($ocorrencia->id, "ocorrencia");
                $userLog = FacadeController::getDadosServidor($dadosLog[0]->cpf_usuario, "nome");
                $textoLog = "Registrado por: " . $userLog[0]->nome . " (" . $dadosLog[0]->cpf_usuario . ") em " . Tdata::TtimestampBR($dadosLog[0]->created_at);
            }
            else{
                $textoLog = "Sem log registrado.";
            }

            array_push($dadosOcorrencia, array(
                "id" => $ocorrencia->id,
                "natureza" => self::getNaturezaOcorrencia($ocorrencia->natureza),
                "data" => Tdata::TdataBR($ocorrencia->data),
                "atualizacao" => Tdata::TtimestampBR($ocorrencia->update_at),
                "horario" => $ocorrencia->horario,
                "descricao" => $ocorrencia->descricao,
                "arquivos" => $arquivos,
                "envol" => $envol,
                "registro" => $textoLog,
                "envolvidos" => $envolvidosArray

            ));

        }
        
        $dataInicioPlantao = Tdata::TdataBR(OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor));
        $dataTerminoPlantao = Tdata::TdataBR(OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor));


        return view('ocorrencia.cadastro.visualizarOcorrencia', compact('dadosOcorrencia','dataTerminoPlantao','dataInicioPlantao'));

    }

    public function excluirOcorrencia($id)
    {
        $verificaOcorrencia = DB::table('log')
                ->where("tabela_alterada","ocorrencia")
                ->where('fk_id',$id)
                ->where("tipo_alteracao", "add")
                ->get()[0];
        
        if($verificaOcorrencia->cpf_usuario != Auth::user()->cpf)
        {
            return redirect()->back()->with('alert-danger', 'Erro ao excluir ocorrência! Apenas o usuário que criou a ocorrência pode excluí-la.');
        }
        
        $updatetOcorrencia = DB::table('ocorrencia')->where('id',$id)->update([
                'status' => 'I',
                'update_at' => date("Y-m-d H:i:s"),
            ]
        );

        if($updatetOcorrencia)
        {
            LogController::log($id,"ocorrencia","delete",Auth::user()->cpf,"Exclusão de ocorrência.");

            return redirect()->route('ocorrencia.buscar')->with('alert-success', 'Ocorrência excluída com sucesso!');
        }else{
            return redirect()->route('ocorrencia.buscar')->with('alert-danger', 'Ocorreu um problema ao apagar o arquivo, por favor, tente novamente!');
        }

    }

    public function livro()
    {

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(AutH::user()->cpf));
        if(!DB::table('ocorrencia_fechamento_livro')->where('unidade', $lotacaoServidor)->exists())
        {
            return redirect()->route('inicio')->with('alert-danger', 'Sua unidade não está apta ao preenchimento do livro de ocorrência, entre em contato com o setor de TI!');
        }
        
        $stringInfoPlantao = "Livro de ocorrência do plantão do dia ". Tdata::TdataBR(self::getDataUltimoFechamentoLivro($lotacaoServidor)) ." às ".self::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor)." até ". Tdata::TdataBR(self::getDataProxFechamentoLivroUnidade($lotacaoServidor))." às ".self::getHorarioReferenciaProxFechamentoLivroUnidade($lotacaoServidor);

        $getFechamento = self::getDataUltimoFechamentoLivro($lotacaoServidor);


       
        $dataInicioPlantao = self::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = self::getDataProxFechamentoLivroUnidade($lotacaoServidor);

        // calcula os horarios de inicio e termino do plantão
        $timestampHoraProximoFechamento = self::getDataUltimoFechamentoLivro($lotacaoServidor)." ".self::getHorarioReferenciaProxFechamentoLivroUnidade($lotacaoServidor);
        $date = new DateTime($timestampHoraProximoFechamento);
        $date->sub(new \DateInterval(('PT1S')));

        $timestampHoraUltimoFechamento = self::getDataUltimoFechamentoLivro($lotacaoServidor)." ".self::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);



        $ocorrenciaHoraInicioPlantao = Tdata::getTempoTimestamp($timestampHoraUltimoFechamento.":00");
        $ocorrenciaDataInicioPlantao = $dataInicioPlantao." ".$ocorrenciaHoraInicioPlantao;
        $ocorrenciaHoraTerminoPlantao = Tdata::getTempoTimestamp($date->format('Y-m-d H:i:s'));
        $ocorrenciaDataTerminoPlantao = self::getDataProxFechamentoLivroUnidade($lotacaoServidor)." ".$ocorrenciaHoraTerminoPlantao;
        
        
            $relacaoAgentes = DB::table('equipe_dia')
                ->where('data', $dataInicioPlantao)
                ->where('hora', $ocorrenciaHoraInicioPlantao)
                ->where('unidade',$lotacaoServidor)
                ->orderBy('escala','ASC')
                ->orderBy('status','ASC')
                ->orderBy('lotacaoUnidade','ASC')
                ->get();

        $dadosEquipe = array();

        //dd($relacaoAgentes);

        foreach($relacaoAgentes as $agentes)
        {

            if($agentes->matricula != "" && !is_null($agentes->matricula)) {
                $relAgentes = FacadeController::getServidorMatricula($agentes->matricula);

                if (is_numeric($agentes->lotacaoUnidade)) {
                    $lotacaoUnidade = UnidadesController::getNomePav($agentes->lotacaoUnidade);
                } else {
                    $lotacaoUnidade = $agentes->lotacaoUnidade;
                }
                array_push($dadosEquipe, array(
                    "nome" => $relAgentes[0]->nome,
                    "status" => $agentes->status,
                    "fone" => $relAgentes[0]->numero,
                    "lotacaoUnidade" => $lotacaoUnidade,
                    "escala" => $agentes->escala,
                    "funcao" => $agentes->funcao,
                    "permuta" => $agentes->permuta,
                    "permutaMat" => $agentes->permutaMat,
                    "matricula" => $relAgentes[0]->matricula,
                    "lotacao" => FacadeController::getLotacaoServidor($relAgentes[0]->cpf),
                    "foto" => config('app.rh') . "/storage/app/arquivos/servidores/" . $relAgentes[0]->id . "/foto/" . $relAgentes[0]->id . ".jpeg",

                ));
            }
        }


        $ocorrenciasGeral = DB::table('ocorrencia')
            ->where('unidade',$lotacaoServidor)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '>=', $ocorrenciaDataInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '<=', $ocorrenciaDataTerminoPlantao)
            ->where('status','A')
            ->orderBy('ocorrencia.data', 'DESC')->orderBy('ocorrencia.horario', 'DESC')
            ->get();

        $allOcorrencias =  json_decode($ocorrenciasGeral, true);

        $filtroOcorrencia = array(27);
        $escalaPostoDiurno = array_filter($allOcorrencias, function ($var) use ($filtroOcorrencia) {
            return in_array($var['natureza'], $filtroOcorrencia);
        });

        $filtroOcorrencia = array(28);
        $escalaPostoNoturno = array_filter($allOcorrencias, function ($var) use ($filtroOcorrencia) {
            return in_array($var['natureza'], $filtroOcorrencia);
        });

        $filtroOcorrencia = array(29);
        $rondas = array_filter($allOcorrencias, function ($var) use ($filtroOcorrencia) {
            return in_array($var['natureza'], $filtroOcorrencia);
        });

        $filtroOcorrencia = array(30);
        $confere = array_filter($allOcorrencias, function ($var) use ($filtroOcorrencia) {
            return in_array($var['natureza'], $filtroOcorrencia);
        });

        $filtroOcorrencia = array(31);
        $revistas = array_filter($allOcorrencias, function ($var) use ($filtroOcorrencia) {
            return in_array($var['natureza'], $filtroOcorrencia);
        });

        $filtroOcorrencia = array(32);
        $escalaBanhoSol = array_filter($allOcorrencias, function ($var) use ($filtroOcorrencia) {
            return in_array($var['natureza'], $filtroOcorrencia);
        });

        $filtroOcorrencia = array(33);
        $revistaPatios = array_filter($allOcorrencias, function ($var) use ($filtroOcorrencia) {
            return in_array($var['natureza'], $filtroOcorrencia);
        });

        $filtroOcorrencia = array(34);
        $materialBelico = array_filter($allOcorrencias, function ($var) use ($filtroOcorrencia) {
            return in_array($var['natureza'], $filtroOcorrencia);
        });

        $filtroOcorrencia = array(36);
        $alimentacao = array_filter($allOcorrencias, function ($var) use ($filtroOcorrencia) {
            return in_array($var['natureza'], $filtroOcorrencia);
        });

        $filtroOcorrencia = array(43);
        $videoconferencia = array_filter($allOcorrencias, function ($var) use ($filtroOcorrencia) {
            return in_array($var['natureza'], $filtroOcorrencia);
        });

        $filtroOcorrencia = array(27,28,29,30,31,32,33,34,36,43);
        $ocorrencias = array_filter($allOcorrencias, function ($var) use ($filtroOcorrencia) {
            return !in_array($var['natureza'], $filtroOcorrencia);
        });
        $ocorrencias = json_decode(json_encode($ocorrencias), FALSE);

        
        /*
        $escalaPostoDiurno = DB::table('ocorrencia')
            ->where('unidade',$lotacaoServidor)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '>=', $ocorrenciaDataInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '<=', $ocorrenciaDataTerminoPlantao)
            ->where('status','A')
            ->where('natureza', 27)
            ->get();



        $escalaPostoNoturno = DB::table('ocorrencia')
            ->where('unidade',$lotacaoServidor)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '>=', $ocorrenciaDataInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '<=', $ocorrenciaDataTerminoPlantao)
            ->where('status','A')
            ->where('natureza', 28)
            ->get();



        $rondas = DB::table('ocorrencia')
            ->where('unidade',$lotacaoServidor)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '>=', $ocorrenciaDataInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '<=', $ocorrenciaDataTerminoPlantao)
            ->where('status','A')
            ->where('natureza', 29)
            ->get();



        $confere = DB::table('ocorrencia')
            ->where('unidade',$lotacaoServidor)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '>=', $ocorrenciaDataInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '<=', $ocorrenciaDataTerminoPlantao)
            ->where('status','A')
            ->where('natureza', 30)
            ->get();


                $revistas = DB::table('ocorrencia')
                ->where('unidade',$lotacaoServidor)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '>=', $ocorrenciaDataInicioPlantao)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '<=', $ocorrenciaDataTerminoPlantao)
                ->where('status','A')
                ->where('natureza', 31)
                ->get();



        $escalaBanhoSol = DB::table('ocorrencia')
            ->where('unidade',$lotacaoServidor)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '>=', $ocorrenciaDataInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '<=', $ocorrenciaDataTerminoPlantao)
            ->where('status','A')
            ->where('natureza', 32)
            ->get();



        $revistaPatios = DB::table('ocorrencia')
            ->where('unidade',$lotacaoServidor)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '>=', $ocorrenciaDataInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '<=', $ocorrenciaDataTerminoPlantao)
            ->where('status','A')
            ->where('natureza', 33)
            ->get();



        $materialBelico = DB::table('ocorrencia')
            ->where('unidade',$lotacaoServidor)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '>=', $ocorrenciaDataInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '<=', $ocorrenciaDataTerminoPlantao)
            ->where('status','A')
            ->where('natureza', 34)
            ->get();

        $alimentacao = DB::table('ocorrencia')
            ->where('unidade',$lotacaoServidor)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '>=', $ocorrenciaDataInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '<=', $ocorrenciaDataTerminoPlantao)
            ->where('status','A')
            ->where('natureza', 36)
            ->get();

        $videoconferencia = DB::table('ocorrencia')
            ->where('unidade',$lotacaoServidor)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '>=', $ocorrenciaDataInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '<=', $ocorrenciaDataTerminoPlantao)
            ->where('status','A')
            ->where('natureza', 43)
            ->get();
         

        // ----------- ocorrencias diversas ------------------------------------

        $ocorrencias = DB::table('ocorrencia')->where('status', "A")
            ->whereNotIn('natureza', [27,28,29,30,31,32,33,34,36,43])
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '>=', $ocorrenciaDataInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '<=', $ocorrenciaDataTerminoPlantao)
            ->where('unidade',$lotacaoServidor)
            ->orderBy('ocorrencia.data', 'DESC')->orderBy('ocorrencia.horario', 'DESC')->get();

         * 
         */
                
        $dadosOcorrencia = array();

        $envol = "";

        foreach($ocorrencias as $ocorrencia)
        {
            $envolvidos = DB::table('ocorrencia_envolvidos')->where('fk_ocorrencia', $ocorrencia->id)->get();

            $arquivos = DB::table('ocorrencia_arquivos')->where('fk_ocorrencia', $ocorrencia->id)->get();

            $envolvidosArray = array();

            foreach(json_decode($envolvidos, true) as $envolvido)
            {
                if($envolvido['fk_tabela'] == "servidor")
                {
                    $envol = $envol."agnt,";
                    array_push($envolvidosArray, array(
                        'servidor' => FacadeController::getDadosServidorPorMat($envolvido['fk_id_envolvido'])
                    ));
                }

                if($envolvido['fk_tabela'] == "interno")
                {
                    $envol = $envol."inter,";
                    array_push($envolvidosArray, array(
                        'interno' => InternoController::getDadosPrincipaisInterno($envolvido['fk_id_envolvido'])
                    ));
                }

                if($envolvido['fk_tabela'] == "advogado")
                {
                    $envol = $envol."adv,";
                    array_push($envolvidosArray, array(
                        'advogado' => AdvogadoController::getDadosAdv($envolvido['fk_id_envolvido'])
                    ));
                }

                if($envolvido['fk_tabela'] == "visita")
                {
                    $envol = $envol."vis,";
                    array_push($envolvidosArray, array(
                        'visita' => VisitaController::getDadosVisita ($envolvido['fk_id_envolvido'])
                    ));
                }


            }

            // dados Log
            $dadosLog = LogController::getDadosLog($ocorrencia->id, "ocorrencia");
            if(count($dadosLog) == 0){
                $textoLog = "** Log não registrado **";
            }else {
                $userLog = FacadeController::getDadosServidor($dadosLog[0]->cpf_usuario, "nome");
                $textoLog = "Registrado por: ".$userLog[0]->nome." (".$dadosLog[0]->cpf_usuario.") em ".Tdata::TtimestampBR($dadosLog[0]->created_at);
            }


            array_push($dadosOcorrencia, array(
                "id" => $ocorrencia->id,
                "natureza" => self::getNaturezaOcorrencia($ocorrencia->natureza),
                "data" => Tdata::TdataBR($ocorrencia->data),
                "atualizacao" => Tdata::TtimestampBR($ocorrencia->update_at),
                "horario" => $ocorrencia->horario,
                "descricao" => $ocorrencia->descricao,
                "arquivos" => $arquivos,
                "envol" => $envol,
                "registro" => $textoLog,
                "envolvidos" => $envolvidosArray

            ));

        }


        $dadosEscolta = DB::table('escolta')
            ->where('unidade',$lotacaoServidor)
            ->where(DB::raw('CONCAT_WS(" ", data_saida, hora_saida)'),'>=', $ocorrenciaDataInicioPlantao)
            ->whereNotNull('data_retorno')
            //->where(DB::raw('CONCAT_WS(" ", data_retorno, hora_retorno)'),'<=', $ocorrenciaDataTerminoPlantao)
            ->where('status', '=', 'Finalizada')
            ->where('tipo', '!=', 'gep')
            ->get();


        $escolta = json_decode($dadosEscolta, true);

        foreach($escolta as $key => $value)
        {
            $agentesDB = DB::table('escolta_agentes')->where('fk_escolta', $escolta[$key]['id'])->get();

            $agentes = array();

            foreach($agentesDB as $agp) {

                if(!is_null($agp->matricula) && $agp->matricula != ""){
                    $dados = DB::connection('mysql2')->table('servidors')->where('matricula', $agp->matricula)
                        ->leftJoin('lotacaos', 'lotacaos.id', '=', 'servidors.lotacao')
                        ->select('servidors.*', 'lotacaos.descricao')->get();


                    array_push($agentes, array(
                        "nome" => $dados[0]->nome,
                        "matricula" => $dados[0]->matricula,
                        "cpf" => $dados[0]->cpf,
                        "lotacao" => utf8_encode($dados[0]->descricao),
                    ));
                }
            }
            
            $escolta[$key]['agentes'] = $agentes;
            
            $escolta[$key]['data_saida'] = Tdata::TdataBR($escolta[$key]['data_saida']);
            $escolta[$key]['data_retorno'] = Tdata::TdataBR($escolta[$key]['data_retorno']);



                $relacaoInterno = DB::table('escolta_internos')->where('fk_escolta', $escolta[$key]['id'])->get();

                $dadosInterno = array();
                foreach ($relacaoInterno as $relacao) {
                    $dadosInternos = (InternoController::getDadosPrincipaisInterno($relacao->fk_interno));


                    array_push($dadosInterno, array(
                        "nome" => $dadosInternos[0]['nome'],
                        "id" => $dadosInternos[0]['id'],
                        "faccao" => $dadosInternos[0]['faccao'],
                        "lideranca" => $dadosInternos[0]['lideranca'],
                        "foto" => $dadosInternos[0]['foto'],
                        "sigla" => $dadosInternos[0]['sigla']
                    ));

                }

            $escolta[$key]['internos'] = $dadosInterno;

        }


        $escoltaGep = DB::table('gep_internos')
            ->leftJoin('gep','gep.id','=','gep_internos.fk_gep')
            ->leftJoin('escolta','escolta.id','=','gep_internos.fk_escolta')
            ->leftJoin('interno','interno.id','=','gep_internos.fk_interno')
            ->where('interno.unidade_prisional',$lotacaoServidor)
            ->where(DB::raw('CONCAT_WS(" ", gep.data_saida, gep.hora_saida)'),'>=', $ocorrenciaDataInicioPlantao)
            ->whereNotNull('gep.data_retorno')
            ->where(DB::raw('CONCAT_WS(" ", gep.data_retorno, gep.hora_retorno)'),'<=', $ocorrenciaDataTerminoPlantao)
            ->where('gep.status', '=', 'Finalizada')
            ->select('interno.nome',DB::raw("DATE_FORMAT(gep.data_saida,'%d/%m/%Y') as data_saida"), DB::raw("DATE_FORMAT(gep.data_retorno,'%d/%m/%Y') as data_retorno"), 'gep.hora_saida','gep.hora_retorno', 'escolta.destino','gep.vtr_placa')
            ->get();



        $pavilhoesUnidade = UnidadesController::getPavilhoes($lotacaoServidor);

            $qtdPavilhao = array();
            $qtdAla = array();

            foreach ($pavilhoesUnidade as $pavilhao) {

                $dadospavilhao = DB::table('interno')->where('unidade_prisional', $lotacaoServidor)->where('status','A')->where('pavilhao', $pavilhao['id'])->count();

                array_push($qtdPavilhao, array(
                    'idPavilhao' => $pavilhao['id'],
                    'nomePavilhao' => UnidadesController::getNomePav($pavilhao['id']),
                    'qtdPavilhao' => $dadospavilhao,
                ));

                $alas = UnidadesController::getAlas($pavilhao['id']);

                foreach ($alas as $ala) {
                    $dadosAla = DB::table('interno')->where('unidade_prisional', $lotacaoServidor)->where('status','A')->where('pavilhao', $pavilhao['id'])->where('ala', $ala['id'])->count();

                    array_push($qtdAla, array(
                        'pv' => $pavilhao['id'],
                        'nomeAla' => UnidadesController::getNomeAla($ala['id']),
                        'qtdAla' => $dadosAla,
                    ));


                }

            }


        $internosUnidadeTotal = DB::table('interno')->where('unidade_prisional', $lotacaoServidor)->where('status','A')->count();


        $dadosVisitantes = DB::table('controle_acesso_visita_interno')
            ->leftJoin('visita', 'visita.id', 'controle_acesso_visita_interno.fk_visita')
            ->leftJoin('controle_acesso', 'controle_acesso.cpf', 'visita.cpf')
            ->where('controle_acesso_visita_interno.unidade', $lotacaoServidor)
            ->where('controle_acesso.tabela','visita')
            ->where(DB::raw('CONCAT_WS(" ", controle_acesso.data, controle_acesso.horario)'), '>=', $ocorrenciaDataInicioPlantao)
            ->Where(DB::raw('CONCAT_WS(" ", controle_acesso.data, controle_acesso.horario)'), '<=', $ocorrenciaDataTerminoPlantao)
            ->where(DB::raw('CONCAT_WS(" ", controle_acesso_visita_interno.data, controle_acesso_visita_interno.horario)'), '>=', $ocorrenciaDataInicioPlantao)
            ->Where(DB::raw('CONCAT_WS(" ", controle_acesso_visita_interno.data, controle_acesso_visita_interno.horario)'), '<=', $ocorrenciaDataTerminoPlantao)
            ->groupBy('visita.cpf')
            ->get();
        $visitantes = array();

        //dd($dadosVisitantes);
        foreach($dadosVisitantes as $x => $visitante)
        {


            //$dadosVisitante = VisitaController::getDadosVisita($visitante->fk_visita);

            //$dadaosSaida = DB::table('controle_acesso')->where('cpf', $dadosVisitante[0]->cpf)->where('data', $visitante->data)->where('horario', $visitante->horario)->get();
//            $iterador = 0;
//            $objetoHoarioSaida = array();
//            foreach($dadaosSaida as $dadoSaida){
//                if($iterador < 1){
//                    $objetoHoarioSaida = $dadoSaida->horario_saida;
//                    $iterador = $iterador + 1;
//                }
//            }
//
//
//            $horario_saida = implode("", (array) $objetoHoarioSaida);
            $horarioSaida = 'Não registrado';
            if($visitante->horario_saida != null){
                $horarioSaida = $visitante->horario_saida;
            }
//

            array_push($visitantes, array(
                'nomeVisitante' => $visitante->nome,
                'nomeInterno' => InternoController::getInternoNome($visitante->fk_interno)." ".InternoController::getLotacaoInterno($visitante->fk_interno),
                'horarioEntrada' => $visitante->horario,
                'horarioSaida' => $horarioSaida,
            ));


        }


        $visitantes = collect($visitantes)->sortBy('horarioEntrada');


        $dadosVisitantesExterno = DB::table('controle_acesso')->where('unidade', $lotacaoServidor)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '>=', $ocorrenciaDataInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '<=', $ocorrenciaDataTerminoPlantao)
            ->where('tabela','visitante externo')
            ->get();
        $visitantesExterno = array();
        foreach($dadosVisitantesExterno as $visitanteExt)
        {
            $dadosVisitanteExt = ControleAcessoController::getCadastroVisitanteExterno($visitanteExt->cpf);

            array_push($visitantesExterno, array(
                'nomeVisitante' => $dadosVisitanteExt[0]->nome,
                'horarioEntrada' => $visitanteExt->horario,
                'horarioSaida' => $visitanteExt->horario_saida,
                'motivo' => $visitanteExt->motivo,
            ));

        }
        $visitantesExterno = collect($visitantesExterno)->sortBy('horarioEntrada');

        $dadosVisitantesServidor = DB::table('controle_acesso')->where('unidade', $lotacaoServidor)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '>=', $ocorrenciaDataInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '<=', $ocorrenciaDataTerminoPlantao)
            ->where('tabela','servidor')
            ->get();

        $visitantesServidor = array();

        foreach($dadosVisitantesServidor as $visitanteSer)
        {

            array_push($visitantesServidor, array(
                'nomeVisitante' => FacadeController::getNomeServidorCpf($visitanteSer->cpf),
                'horarioEntrada' => $visitanteSer->horario,
                'horarioSaida' => $visitanteSer->horario_saida,
                'motivo' => $visitanteSer->motivo,
            ));

        }
        $visitantesServidor = collect($visitantesServidor)->sortBy('horarioEntrada');



//        if(date("H:i") >= "00:00" && date("H:i") < "07:59") {
//
//            $dadosAdvs = DB::table('advogado_atendimento')->where('unidade', $lotacaoServidor)->where('data_atendimento', $dataPlantaoOntem)->get();
//        }else{
//            $dadosAdvs = DB::table('advogado_atendimento')->where('unidade', $lotacaoServidor)->where('data_atendimento', $dataInicioPlantao)->get();
//
//        }

        $dadosAdvs = DB::table('advogado_atendimento')->where('unidade', $lotacaoServidor)
            ->where(DB::raw('CONCAT_WS(" ", data_atendimento, horaInicio)'), '>=', $ocorrenciaDataInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data_atendimento, horaInicio)'), '<=', $ocorrenciaDataTerminoPlantao)
            ->get();
        

        $advogados = array();
        foreach($dadosAdvs as $adv)
        {
            $dadosAdv = AdvogadoController::getDadosAdv($adv->fk_advogado);
            array_push($advogados, array(
                'nomeAdvogado' => $dadosAdv[0]->nome,
                'nomeInterno' => InternoController::getInternoNome($adv->fk_interno)." ".InternoController::getLotacaoInterno($adv->fk_interno),
                'inicioAtendimento' => $adv->horaInicio,
                'terminoAtendimento' => $adv->horaTermino,
                'duracao' => $adv->duracao,
            ));

        }

        $advogados = collect($advogados)->sortBy('inicioAtendimento');


        $mudancaDeCela = DB::table('movimentacao_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao.data', '>=', $ocorrenciaDataInicioPlantao)
            ->where('movimentacao.data', '<=', $ocorrenciaDataTerminoPlantao)
            ->where('movimentacao.unidade_destino', $lotacaoServidor)
            ->where('movimentacao.tipo','mudancacela')
            ->get();


        $dadosMudancaCela = array();

        foreach($mudancaDeCela as $mudanca)
        {
            $dadosInter = InternoController::getDadosPrincipaisInterno($mudanca->fk_interno);

            array_push($dadosMudancaCela, array(
                'nome' => $dadosInter[0]['nome'],
                'prontuario' => $mudanca->fk_interno,
                'horario' => Tdata::getTempoTimestamp($mudanca->data),
                'pvAnteior' => UnidadesController::getNomePav($mudanca->pv_anterior),
                'alaAnteior' => UnidadesController::getNomeAla($mudanca->ala_anterior),
                'celaAnteior' => UnidadesController::getNomeCela($mudanca->cela_anterior),
                'pvDestino' => UnidadesController::getNomePav($mudanca->pavilhao_destino),
                'alaDestino' => UnidadesController::getNomeAla($mudanca->ala_destino),
                'celaDestino' => UnidadesController::getNomeCela($mudanca->cela_destino),
            ));
        }

        //dd('aqui');
        $dadosEntradaEntradaUnidade = DB::table('movimentacao_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao.data', '>=', $ocorrenciaDataInicioPlantao)
            ->where('movimentacao.data', '<=', $ocorrenciaDataTerminoPlantao)
            ->where('movimentacao.unidade_destino', $lotacaoServidor)
            ->where('movimentacao.tipo', '=', 'entradaunidadeprisional')
            ->where('movimentacao_interno.status', 'Finalizada')
            ->get();

        $dadosEntradaTransferencia = DB::table('movimentacao_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao.data', '>=', $ocorrenciaDataInicioPlantao)
            ->where('movimentacao.data', '<=', $ocorrenciaDataTerminoPlantao)
            ->where('movimentacao.unidade_destino', $lotacaoServidor)
            ->where('movimentacao.tipo', '=', 'transferenciaentrada')
            ->where('movimentacao_interno.status', 'Finalizada')
            ->get();

        $dadosEntradaInterno = array_merge(json_decode($dadosEntradaEntradaUnidade, true), json_decode($dadosEntradaTransferencia, true));

        $entradaInterno = array();

        foreach($dadosEntradaInterno as $entrada)
        {
            $dadosInter = InternoController::getDadosPrincipaisInterno($entrada['fk_interno']);

            array_push($entradaInterno, array(
                'nome' => $dadosInter[0]['nome'],
                'prontuario' => $entrada['fk_interno'],
                'tipo' => $entrada['tipo'],
                'horario' => Tdata::getTempoTimestamp($entrada['data']),
                'pvAnteior' => UnidadesController::getNomePav($entrada['pv_anterior']),
                'alaAnteior' => UnidadesController::getNomeAla($entrada['ala_anterior']),
                'celaAnteior' => UnidadesController::getNomeCela($entrada['cela_anterior']),
                'pvDestino' => UnidadesController::getNomePav($entrada['pavilhao_destino']),
                'alaDestino' => UnidadesController::getNomeAla($entrada['ala_destino']),
                'celaDestino' => UnidadesController::getNomeCela($entrada['cela_destino']),
            ));
        }


        $dadosSaidaInternoGeral = DB::table('movimentacao_interno')
            ->leftJoin('interno', 'interno.id', '=', 'movimentacao_interno.fk_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao.data', '>=', $ocorrenciaDataInicioPlantao)
            ->where('movimentacao.data', '<=', $ocorrenciaDataTerminoPlantao)
            ->where('movimentacao_interno.up_anterior', $lotacaoServidor)
            ->select('movimentacao_interno.*','movimentacao.*','interno.condicao_interno')
            ->where('movimentacao_interno.status', 'Finalizada')
            ->orderBy('movimentacao.data', 'ASC')
            ->get();
                
        $allMovInterno =  json_decode($dadosSaidaInternoGeral, true);

        $filtro = array("progressaoaberto");
        $dadosSaidaInternoAberto = array_filter($allMovInterno, function ($var) use ($filtro) {
            return in_array($var['tipo'], $filtro) && $var['condicao_interno'] == 646;
        });

        $filtro = array("transferencia");
        $dadosSaidaInternoTransferencia = array_filter($allMovInterno, function ($var) use ($filtro) {
            return in_array($var['tipo'], $filtro);
        });

        $filtro = array("alvara");
        $dadosSaidaInternoAlvara = array_filter($allMovInterno, function ($var) use ($filtro) {
            return in_array($var['tipo'], $filtro);
        });

        $filtro = array("livramentocondicional");
        $dadosSaidaInternoLivramentoCondicional = array_filter($allMovInterno, function ($var) use ($filtro) {
            return in_array($var['tipo'], $filtro);
        });

        $filtro = array("recambiamento");
        $dadosSaidaInternoRecambiamento = array_filter($allMovInterno, function ($var) use ($filtro) {
            return in_array($var['tipo'], $filtro);
        });

        $filtro = array("obito");
        $dadosSaidaInternoObito = array_filter($allMovInterno, function ($var) use ($filtro) {
            return in_array($var['tipo'], $filtro);
        });

        $filtro = array("extincaopunibilidade");
        $dadosSaidaInternoExtincaopunibilidade = array_filter($allMovInterno, function ($var) use ($filtro) {
            return in_array($var['tipo'], $filtro);
        });

        $filtro = array("prisaodomiciliar");
        $dadosSaidaInternoPrisaoDomiciliar = array_filter($allMovInterno, function ($var) use ($filtro) {
            return in_array($var['tipo'], $filtro);
        });

        $filtro = array("saidajudicial");
        $dadosSaidaInternoSaidaJudicial = array_filter($allMovInterno, function ($var) use ($filtro) {
            return in_array($var['tipo'], $filtro);
        });

        $dadosSaidaInterno = array_merge($dadosSaidaInternoAberto, $dadosSaidaInternoTransferencia, $dadosSaidaInternoAlvara, $dadosSaidaInternoLivramentoCondicional, $dadosSaidaInternoRecambiamento, $dadosSaidaInternoObito, $dadosSaidaInternoExtincaopunibilidade, $dadosSaidaInternoPrisaoDomiciliar, $dadosSaidaInternoSaidaJudicial);

        $saidaInterno = array();

        foreach($dadosSaidaInterno as $entrada)
        {
            $dadosInter = InternoController::getDadosPrincipaisInterno($entrada['fk_interno']);

            array_push($saidaInterno, array(
                'nome' => $dadosInter[0]['nome'],
                'prontuario' => $entrada['fk_interno'],
                'tipo' => $entrada['tipo'],
                'horario' => Tdata::getTempoTimestamp($entrada['data']),
                'pvAnteior' => UnidadesController::getNomePav($entrada['pv_anterior']),
                'alaAnteior' => UnidadesController::getNomeAla($entrada['ala_anterior']),
                'celaAnteior' => UnidadesController::getNomeCela($entrada['cela_anterior']),
                'pvDestino' => UnidadesController::getNomePav($entrada['pavilhao_destino']),
                'alaDestino' => UnidadesController::getNomeAla($entrada['ala_destino']),
                'celaDestino' => UnidadesController::getNomeCela($entrada['cela_destino']),
            ));
        }

        //dd($saidaInterno);

        // --------------------------- SAUDE E ASSISTENCIA ------------------------------------------------------------
        
        $atendimentoSaudeAssistenciaDados = DB::table("saude_assistencia")
                ->where("unidade", $lotacaoServidor)
                ->where(DB::raw('CONCAT_WS(" ", data_atendimento, hora_atendimento)'), '>=', $ocorrenciaDataInicioPlantao)
                ->where(DB::raw('CONCAT_WS(" ", data_atendimento, hora_atendimento)'), '<=', $ocorrenciaDataTerminoPlantao)
                ->where('status', 'A')
                ->orderBy('hora_atendimento', 'ASC')
                ->get();
       
        $atendimentosSaudeAssistencia = array();
        foreach($atendimentoSaudeAssistenciaDados as $atendimentos)
        {
            array_push($atendimentosSaudeAssistencia, array(
               'setor' => $atendimentos->setor,
                'internoNome' => InternoController::getInternoNome($atendimentos->fk_interno),
                'internoLocalizacao' => InternoController::getLotacaoInterno($atendimentos->fk_interno),
                'internoProntuario' => $atendimentos->fk_interno,
                'horarioAtendimento' => $atendimentos->hora_atendimento,
            ));
        }
        
        // --------------------------- FIM SAUDE E ASSISTENCIA ------------------------------------------------------------

        $dtInicioPlantao = Tdata::TdataBR($dataInicioPlantao);

        return view('ocorrencia.livro.livro', compact('stringInfoPlantao','dtInicioPlantao','dataTerminoPlantao','dadosEquipe','escalaPostoDiurno','escalaPostoNoturno','rondas','confere','revistas','escalaBanhoSol','revistaPatios','alimentacao','videoconferencia','escolta','escoltaGep','qtdPavilhao','qtdAla','internosUnidadeTotal','visitantes','visitantesExterno', 'visitantesServidor','advogados','dadosOcorrencia','materialBelico','dadosMudancaCela','entradaInterno','saidaInterno','atendimentosSaudeAssistencia'));
    }

    public function livroDownload($dataLivro, $unidade, $horario)
    {
        

        //date_default_timezone_set('America/Fortaleza');

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $qtdPavilhao = array();
        $qtdAla = array();
        $qtdCela = array();
        $internosUnidadeTotal = null;


        $pavilhoesUnidade = DB::table('unidade_quantitativo')->where('fk_referencia', $lotacaoServidor)->where('tipo','pavilhao')->where('data', $dataLivro)->get();

        foreach ($pavilhoesUnidade as $pavilhao) {

            array_push($qtdPavilhao, array(
                'idPavilhao' => $pavilhao->fk_id,
                'nomePavilhao' => UnidadesController::getNomePav($pavilhao->fk_id),
                'qtdPavilhao' => $pavilhao->quantidade,
            ));

            $alas = DB::table('unidade_quantitativo')->where('fk_referencia', $pavilhao->fk_id)->where('tipo','ala')->where('data', $dataLivro)->get();

            foreach ($alas as $ala) {

                array_push($qtdAla, array(
                    'pv' => $pavilhao->fk_id,
                    'alaId' => $ala->fk_id,
                    'nomeAla' => UnidadesController::getNomeAla($ala->fk_id),
                    'qtdAla' => $ala->quantidade,
                ));

                $celas = DB::table('unidade_quantitativo')->where('fk_referencia', $ala->fk_id)->where('tipo','cela')->where('data', $dataLivro)->get();

                foreach($celas as $cela)
                {
                    array_push($qtdCela, array(
                        'ala' => $cela->fk_referencia,
                        'idCela' => $cela->fk_id,
                        'nomeCela' => UnidadesController::getNomeCela($cela->fk_id),
                        'qtdCela' => $cela->quantidade,

                    ));
                }


            }

        }

        $qtdPavilhao = collect($qtdPavilhao);
        $qtdAla = collect($qtdAla);
        $qtdCela = collect($qtdCela);

        $qtdPavilhao = $qtdPavilhao->unique('idPavilhao');
        $qtdAla = $qtdAla->unique('alaId');
        $qtdCela = $qtdCela->unique('idCela');


        $timestampTerminoPlantao = self::getTimestampFechamentoLivroUnidadeData($unidade, $dataLivro, $horario);
        $date = new DateTime($timestampTerminoPlantao);
        $date->sub(new \DateInterval(('PT1S')));
        $timestampTerminoPlantao = $date->format('Y-m-d H:i:s');

       
          
        $relacaoAgentes = DB::table('equipe_dia')
                ->where(DB::raw('CONCAT_WS(" ", data, hora)'), $dataLivro." ".$horario)
                ->where('unidade',$unidade)
                ->orderBy('escala','ASC')
                ->orderBy('status','ASC')
                ->orderBy('lotacaoUnidade','ASC')
                ->get();

        $dadosEquipe = array();

        foreach($relacaoAgentes as $agentes)
        {
            $relAgentes = FacadeController::getServidorMatricula($agentes->matricula);

            if(is_numeric($agentes->lotacaoUnidade))
            {
                $lotacaoUnidade = UnidadesController::getNomePav($agentes->lotacaoUnidade);
            }else{
                $lotacaoUnidade = $agentes->lotacaoUnidade;
            }
            array_push($dadosEquipe, array(
                "nome" =>  utf8_encode($relAgentes[0]->nome),
                "status" => $agentes->status,
                "fone" => $relAgentes[0]->numero,
                "lotacaoUnidade" => $lotacaoUnidade,
                "escala" => $agentes->escala,
                "funcao" => $agentes->funcao,
                "permuta" => $agentes->permuta,
                "permutaMat" => $agentes->permutaMat,
                "matricula" => $relAgentes[0]->matricula,
                "lotacao" => FacadeController::getLotacaoServidor($relAgentes[0]->cpf),
                "foto" => config('app.rh')."/storage/app/arquivos/servidores/".$relAgentes[0]->id."/foto/".$relAgentes[0]->id.".jpeg",

            ));
        }

            $escalaPostoDiurno = DB::table('ocorrencia')
                ->where('unidade', $unidade)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'>=', $dataLivro." ".$horario)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'<', $timestampTerminoPlantao)
                ->where('status','A')
                ->where('natureza', 27)
                ->get();



            $escalaPostoNoturno = DB::table('ocorrencia')
                ->where('unidade', $unidade)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'>=', $dataLivro." ".$horario)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'<', $timestampTerminoPlantao)
                ->where('status','A')
                ->where('natureza', 28)
                ->get();


            $rondas = DB::table('ocorrencia')
                ->where('unidade', $unidade)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'>=', $dataLivro." ".$horario)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'<', $timestampTerminoPlantao)
                ->where('status','A')
                ->where('natureza', 29)
                ->get();


            $confere = DB::table('ocorrencia')
                ->where('unidade', $unidade)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'>=', $dataLivro." ".$horario)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'<', $timestampTerminoPlantao)
                ->where('status','A')
                ->where('natureza', 30)
                ->get();

            $revistas = DB::table('ocorrencia')
                ->where('unidade', $unidade)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'>=', $dataLivro." ".$horario)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'<', $timestampTerminoPlantao)
                ->where('status','A')
                ->where('natureza', 31)
                ->get();


            $escalaBanhoSol = DB::table('ocorrencia')
                ->where('unidade', $unidade)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'>=', $dataLivro." ".$horario)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'<', $timestampTerminoPlantao)
                ->where('status','A')
                ->where('natureza', 32)
                ->get();

            $revistaPatios = DB::table('ocorrencia')
                ->where('unidade', $unidade)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'>=', $dataLivro." ".$horario)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'<', $timestampTerminoPlantao)
                ->where('status','A')
                ->where('natureza', 33)
                ->get();

            $materialBelico = DB::table('ocorrencia')
                ->where('unidade', $unidade)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'>=', $dataLivro." ".$horario)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'<', $timestampTerminoPlantao)
                ->where('status','A')
                ->where('natureza', 34)
                ->get();

            $alimentacao = DB::table('ocorrencia')
                ->where('unidade', $unidade)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'>=', $dataLivro." ".$horario)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'<', $timestampTerminoPlantao)
                ->where('status','A')
                ->where('natureza', 36)
                ->get();

            $videoconferencia = DB::table('ocorrencia')
                ->where('unidade', $unidade)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'>=', $dataLivro." ".$horario)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'<', $timestampTerminoPlantao)
                ->where('status','A')
                ->where('natureza', 43)
                ->get();


        $dadosEscolta = DB::table('escolta')
            ->where('unidade',$unidade)
            ->where(DB::raw('CONCAT_WS(" ", data_saida, hora_saida)'),'>=', $dataLivro." ".$horario)
            ->where(DB::raw('CONCAT_WS(" ", data_retorno, hora_retorno)'),'<=', $timestampTerminoPlantao)
            ->where('status', '=', 'Finalizada')
            //->orWhere('status', 'Parcialmente Cancelada')
            ->get();

        $escolta = json_decode($dadosEscolta, true);

        foreach($escolta as $key => $value)
        {
            $agentesDB = DB::table('escolta_agentes')->where('fk_escolta', $escolta[$key]['id'])->get();

            $agentes = array();

            foreach($agentesDB as $agp) {

                $dados = DB::connection('mysql2')->table('servidors')->where('matricula', $agp->matricula)
                    ->leftJoin('lotacaos', 'lotacaos.id', '=', 'servidors.lotacao')
                    ->select('servidors.*', 'lotacaos.descricao')->get();


                array_push($agentes, array(
                    "nome" => $dados[0]->nome,
                    "matricula" => $dados[0]->matricula,
                    "cpf" => $dados[0]->cpf,
                    "lotacao" => utf8_encode($dados[0]->descricao),
                ));
            }

            $escolta[$key]['agentes'] = $agentes;

            $escolta[$key]['data_saida'] = Tdata::TdataBR($escolta[$key]['data_saida']);
            $escolta[$key]['data_retorno'] = Tdata::TdataBR($escolta[$key]['data_retorno']);

            if($escolta[$key]['tipo'] == "gep") {

                $relacaoInterno = DB::table('gep_internos')
                    ->leftJoin('gep','gep.id','=','gep_internos.fk_gep')
                    ->where('gep_internos.fk_escolta', $escolta[$key]['id'])
                    ->where('gep.status','Finalizada')
                    ->get();

                $dadosInterno = array();
                foreach ($relacaoInterno as $relacao) {
                    $dadosInternos = (InternoController::getDadosPrincipaisInterno($relacao->fk_interno));


                    array_push($dadosInterno, array(
                        "nome" => $dadosInternos[0]['nome'],
                        "id" => $dadosInternos[0]['id'],
                        "faccao" => $dadosInternos[0]['faccao'],
                        "lideranca" => $dadosInternos[0]['lideranca'],
                        "foto" => $dadosInternos[0]['foto'],
                        "sigla" => $dadosInternos[0]['sigla']
                    ));

                }
            }
            else {
                $relacaoInterno = DB::table('escolta_internos')->where('fk_escolta', $escolta[$key]['id'])->get();

                $dadosInterno = array();
                foreach ($relacaoInterno as $relacao) {
                    $dadosInternos = (InternoController::getDadosPrincipaisInterno($relacao->fk_interno));


                    array_push($dadosInterno, array(
                        "nome" => $dadosInternos[0]['nome'],
                        "id" => $dadosInternos[0]['id'],
                        "faccao" => $dadosInternos[0]['faccao'],
                        "lideranca" => $dadosInternos[0]['lideranca'],
                        "foto" => $dadosInternos[0]['foto'],
                        "sigla" => $dadosInternos[0]['sigla']
                    ));

                }
            }
            $escolta[$key]['internos'] = $dadosInterno;

        }

        $escoltaGep = DB::table('gep_internos')
            ->leftJoin('gep','gep.id','=','gep_internos.fk_gep')
            ->leftJoin('escolta','escolta.id','=','gep_internos.fk_escolta')
            ->leftJoin('interno','interno.id','=','gep_internos.fk_interno')
            ->where('interno.unidade_prisional',$unidade)
            ->where(DB::raw('CONCAT_WS(" ", gep.data_saida, gep.hora_saida)'),'>=', $dataLivro." 07:59")
            ->whereNotNull('gep.data_retorno')
            ->where(DB::raw('CONCAT_WS(" ", gep.data_retorno, gep.hora_retorno)'),'<=', Tdata::addDias($dataLivro,1)." 07:58")
            ->where('gep.status', '=', 'Finalizada')
            ->select('interno.nome',DB::raw("DATE_FORMAT(gep.data_saida,'%d/%m/%Y') as data_saida"), DB::raw("DATE_FORMAT(gep.data_retorno,'%d/%m/%Y') as data_retorno"), 'gep.hora_saida','gep.hora_retorno', 'escolta.destino','gep.vtr_placa')
            ->get();



        $dadosVisitantes = DB::table('controle_acesso_visita_interno')
                ->where('unidade', $lotacaoServidor)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'>=', $dataLivro." ".$horario)
                ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'<=', $timestampTerminoPlantao)
                ->get();
            $visitantes = array();
            foreach($dadosVisitantes as $visitante)
            {
                $dadosVisitante = VisitaController::getDadosVisita($visitante->fk_visita);
                $dadaosSaida = DB::table('controle_acesso')->where('cpf', $dadosVisitante[0]->cpf)->where('horario', $visitante->horario)->get();
                $horario_saida = 'Não Informado';
                if(isset($dadaosSaida[0]->horario_saida)){
                    $horario_saida = $dadaosSaida[0]->horario_saida;
                }
                array_push($visitantes, array(
                    'nomeVisitante' => $dadosVisitante[0]->nome,
                    'nomeInterno' => InternoController::getInternoNome($visitante->fk_interno)." ".InternoController::getLotacaoInterno($visitante->fk_interno),
                    'horarioEntrada' => $visitante->horario,
                    'horarioSaida' => $horario_saida,
                ));

            }

        $visitantes = collect($visitantes)->sortBy('horarioEntrada');



        $dadosVisitantesExterno = DB::table('controle_acesso')->where('unidade', $lotacaoServidor)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'>=', $dataLivro." ".$horario)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'<=', $timestampTerminoPlantao)
            ->where('tabela','visitante externo')
            ->get();
        $visitantesExterno = array();
        foreach($dadosVisitantesExterno as $visitanteExt)
        {
            $dadosVisitanteExt = ControleAcessoController::getCadastroVisitanteExterno($visitanteExt->cpf);

            array_push($visitantesExterno, array(
                'nomeVisitante' => $dadosVisitanteExt[0]->nome,
                'horarioEntrada' => $visitanteExt->horario,
                'horarioSaida' => $visitanteExt->horario_saida,
                'motivo' => $visitanteExt->motivo,
            ));

        }
        $visitantesExterno = collect($visitantesExterno)->sortBy('horarioEntrada');


        $dadosVisitantesServidor = DB::table('controle_acesso')->where('unidade', $lotacaoServidor)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'>=', $dataLivro." ".$horario)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'<=', $timestampTerminoPlantao)
            ->where('tabela','servidor')
            ->get();

        $visitantesServidor = array();

        foreach($dadosVisitantesServidor as $visitanteSer)
        {

            array_push($visitantesServidor, array(
                'nomeVisitante' => FacadeController::getNomeServidorCpf($visitanteSer->cpf),
                'horarioEntrada' => $visitanteSer->horario,
                'horarioSaida' => $visitanteSer->horario_saida,
                'motivo' => $visitanteSer->motivo,
            ));

        }
        $visitantesServidor = collect($visitantesServidor)->sortBy('horarioEntrada');


        $dadosAdvs = DB::table('advogado_atendimento')
            ->where('unidade', $unidade)
            ->where(DB::raw('CONCAT_WS(" ", data_atendimento, horaInicio)'),'>=', $dataLivro." ".$horario)
            ->where(DB::raw('CONCAT_WS(" ", data_atendimento, horaInicio)'),'<=', $timestampTerminoPlantao)

            ->get();
        $advogados = array();

        foreach($dadosAdvs as $adv)
        {
            $dadosAdv = AdvogadoController::getDadosAdv($adv->fk_advogado);
            array_push($advogados, array(
                'nomeAdvogado' => $dadosAdv[0]->nome,
                'nomeInterno' => InternoController::getInternoNome($adv->fk_interno)." ".InternoController::getLotacaoInterno($adv->fk_interno),
                'inicioAtendimento' => $adv->horaInicio,
                'terminoAtendimento' => $adv->horaTermino,
                'duracao' => $adv->duracao,
            ));

        }

        $advogados = collect($advogados)->sortBy('inicioAtendimento');


        // ----------- ocorrencias diversas ------------------------------------

        $ocorrenciaHoraInicioPlantao = $horario;
        $ocorrenciaDataInicioPlantao = $dataLivro." ".$ocorrenciaHoraInicioPlantao;
        $ocorrenciaHoraTerminoPlantao = date("07:58:59");
        $ocorrenciaDataTerminoPlantao = $timestampTerminoPlantao;


        $ocorrencias = DB::table('ocorrencia')->where('status', "A")
            ->whereNotIn('natureza', [27,28,29,30,31,32,33,34,36,43])
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '>=', $ocorrenciaDataInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '<=', $ocorrenciaDataTerminoPlantao)
            ->where('unidade', $unidade)
            ->orderBy('ocorrencia.data', 'DESC')->orderBy('ocorrencia.horario', 'DESC')->get();

        $dadosOcorrencia = array();

        $envol = "";

        foreach($ocorrencias as $ocorrencia)
        {
            $envolvidos = DB::table('ocorrencia_envolvidos')->where('fk_ocorrencia', $ocorrencia->id)->get();

            $arquivos = DB::table('ocorrencia_arquivos')->where('fk_ocorrencia', $ocorrencia->id)->get();

            $envolvidosArray = array();

            foreach(json_decode($envolvidos, true) as $envolvido)
            {
                if($envolvido['fk_tabela'] == "servidor")
                {
                    $envol = $envol."agnt,";
                    array_push($envolvidosArray, array(
                        'servidor' => FacadeController::getDadosServidorPorMat($envolvido['fk_id_envolvido'])
                    ));
                }

                if($envolvido['fk_tabela'] == "interno")
                {
                    $envol = $envol."inter,";
                    array_push($envolvidosArray, array(
                        'interno' => InternoController::getDadosPrincipaisInterno($envolvido['fk_id_envolvido'])
                    ));
                }

                if($envolvido['fk_tabela'] == "advogado")
                {
                    $envol = $envol."adv,";
                    array_push($envolvidosArray, array(
                        'advogado' => AdvogadoController::getDadosAdv($envolvido['fk_id_envolvido'])
                    ));
                }

                if($envolvido['fk_tabela'] == "visita")
                {
                    $envol = $envol."vis,";
                    array_push($envolvidosArray, array(
                        'visita' => VisitaController::getDadosVisita ($envolvido['fk_id_envolvido'])
                    ));
                }


            }

            // dados Log
            $dadosLog = LogController::getDadosLog($ocorrencia->id, "ocorrencia");
            if(count($dadosLog) == 0){
                $textoLog = "** Log não registrado **";
            }else {
                $userLog = FacadeController::getDadosServidor($dadosLog[0]->cpf_usuario, "nome");
                $textoLog = "Registrado por: ".$userLog[0]->nome." (".$dadosLog[0]->cpf_usuario.") em ".Tdata::TtimestampBR($dadosLog[0]->created_at);
            }

            array_push($dadosOcorrencia, array(
                "id" => $ocorrencia->id,
                "natureza" => self::getNaturezaOcorrencia($ocorrencia->natureza),
                "data" => Tdata::TdataBR($ocorrencia->data),
                "atualizacao" => Tdata::TtimestampBR($ocorrencia->update_at),
                "horario" => $ocorrencia->horario,
                "descricao" => $ocorrencia->descricao,
                "arquivos" => $arquivos,
                "envol" => $envol,
                "registro" => $textoLog,
                "envolvidos" => $envolvidosArray

            ));

        }


        $mudancaDeCela = DB::table('movimentacao_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao.data', '>=', $ocorrenciaDataInicioPlantao)
            ->where('movimentacao.data', '<=', $ocorrenciaDataTerminoPlantao)
            ->where('movimentacao.unidade_destino', $unidade)
            ->where('movimentacao.tipo','mudancacela')
            ->get();

        $dadosMudancaCela = array();

        foreach($mudancaDeCela as $mudanca)
        {
            $dadosInter = InternoController::getDadosPrincipaisInterno($mudanca->fk_interno);

            array_push($dadosMudancaCela, array(
                'nome' => $dadosInter[0]['nome'],
                'prontuario' => $mudanca->fk_interno,
                'horario' => Tdata::getTempoTimestamp($mudanca->data),
                'pvAnteior' => UnidadesController::getNomePav($mudanca->pv_anterior),
                'alaAnteior' => UnidadesController::getNomeAla($mudanca->ala_anterior),
                'celaAnteior' => UnidadesController::getNomeCela($mudanca->cela_anterior),
                'pvDestino' => UnidadesController::getNomePav($mudanca->pavilhao_destino),
                'alaDestino' => UnidadesController::getNomeAla($mudanca->ala_destino),
                'celaDestino' => UnidadesController::getNomeCela($mudanca->cela_destino),
            ));
        }


        $dadosEntradaEntradaUnidade = DB::table('movimentacao_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao.data', '>=', $ocorrenciaDataInicioPlantao)
            ->where('movimentacao.data', '<=', $ocorrenciaDataTerminoPlantao)
            ->where('movimentacao.unidade_destino', $unidade)
            ->where('movimentacao.tipo', '=', 'entradaunidadeprisional')
            ->where('movimentacao_interno.status', 'Finalizada')
            ->get();

        $dadosEntradaTransferencia = DB::table('movimentacao_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao.data', '>=', $ocorrenciaDataInicioPlantao)
            ->where('movimentacao.data', '<=', $ocorrenciaDataTerminoPlantao)
            ->where('movimentacao.unidade_destino', $unidade)
            ->where('movimentacao.tipo', '=', 'transferenciaentrada')
            ->where('movimentacao_interno.status', 'Finalizada')
            ->get();

        $dadosEntradaInterno = array_merge(json_decode($dadosEntradaEntradaUnidade, true), json_decode($dadosEntradaTransferencia, true));

        $entradaInterno = array();

        foreach($dadosEntradaInterno as $entrada)
        {
            $dadosInter = InternoController::getDadosPrincipaisInterno($entrada['fk_interno']);

            array_push($entradaInterno, array(
                'nome' => $dadosInter[0]['nome'],
                'prontuario' => $entrada['fk_interno'],
                'tipo' => $entrada['tipo'],
                'horario' => Tdata::getTempoTimestamp($entrada['data']),
                'pvAnteior' => UnidadesController::getNomePav($entrada['pv_anterior']),
                'alaAnteior' => UnidadesController::getNomeAla($entrada['ala_anterior']),
                'celaAnteior' => UnidadesController::getNomeCela($entrada['cela_anterior']),
                'pvDestino' => UnidadesController::getNomePav($entrada['pavilhao_destino']),
                'alaDestino' => UnidadesController::getNomeAla($entrada['ala_destino']),
                'celaDestino' => UnidadesController::getNomeCela($entrada['cela_destino']),
            ));
        }

        $dadosSaidaInternoAberto = DB::table('movimentacao_interno')
            ->leftJoin('interno', 'interno.id', '=', 'movimentacao_interno.fk_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao.data', '>=', $ocorrenciaDataInicioPlantao)
            ->where('movimentacao.data', '<=', $ocorrenciaDataTerminoPlantao)
            ->where('movimentacao_interno.up_anterior', $unidade)
            ->where('movimentacao.tipo', '=', 'progressaoaberto')
            ->where('interno.condicao_interno', 646)
            ->select('movimentacao_interno.*','movimentacao.*')
            ->where('movimentacao_interno.status', 'Finalizada')
            ->get();

        $dadosSaidaInternoTransferencia = DB::table('movimentacao_interno')
            ->leftJoin('interno', 'interno.id', '=', 'movimentacao_interno.fk_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao.data', '>=', $ocorrenciaDataInicioPlantao)
            ->where('movimentacao.data', '<=', $ocorrenciaDataTerminoPlantao)
            ->where('movimentacao_interno.up_anterior', $unidade)
            ->where('movimentacao.tipo', '=', 'transferencia')
            ->select('movimentacao_interno.*','movimentacao.*')
            ->where('movimentacao_interno.status', 'Finalizada')
            ->get();

        $dadosSaidaInternoAlvara = DB::table('movimentacao_interno')
            ->leftJoin('interno', 'interno.id', '=', 'movimentacao_interno.fk_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao.data', '>=', $ocorrenciaDataInicioPlantao)
            ->where('movimentacao.data', '<=', $ocorrenciaDataTerminoPlantao)
            ->where('movimentacao_interno.up_anterior', $unidade)
            ->where('movimentacao.tipo', '=', 'alvara')
            ->select('movimentacao_interno.*','movimentacao.*')
            ->where('movimentacao_interno.status', 'Finalizada')
            ->get();

        $dadosSaidaInternoLivramentoCondicional = DB::table('movimentacao_interno')
            ->leftJoin('interno', 'interno.id', '=', 'movimentacao_interno.fk_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao.data', '>=', $ocorrenciaDataInicioPlantao)
            ->where('movimentacao.data', '<=', $ocorrenciaDataTerminoPlantao)
            ->where('movimentacao_interno.up_anterior', $unidade)
            ->where('movimentacao.tipo', '=', 'livramentocondicional')
            ->select('movimentacao_interno.*','movimentacao.*')
            ->where('movimentacao_interno.status', 'Finalizada')
            ->get();

        $dadosSaidaInternoRecambiamento = DB::table('movimentacao_interno')
            ->leftJoin('interno', 'interno.id', '=', 'movimentacao_interno.fk_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao.data', '>=', $ocorrenciaDataInicioPlantao)
            ->where('movimentacao.data', '<=', $ocorrenciaDataTerminoPlantao)
            ->where('movimentacao_interno.up_anterior', $unidade)
            ->where('movimentacao.tipo', '=', 'recambiamento')
            ->select('movimentacao_interno.*','movimentacao.*')
            ->where('movimentacao_interno.status', 'Finalizada')
            ->get();

        $dadosSaidaInternoObito = DB::table('movimentacao_interno')
            ->leftJoin('interno', 'interno.id', '=', 'movimentacao_interno.fk_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao.data', '>=', $ocorrenciaDataInicioPlantao)
            ->where('movimentacao.data', '<=', $ocorrenciaDataTerminoPlantao)
            ->where('movimentacao_interno.up_anterior', $unidade)
            ->where('movimentacao.tipo', '=', 'obito')
            ->select('movimentacao_interno.*','movimentacao.*')
            ->where('movimentacao_interno.status', 'Finalizada')
            ->get();

        $dadosSaidaInternoExtincaopunibilidade = DB::table('movimentacao_interno')
            ->leftJoin('interno', 'interno.id', '=', 'movimentacao_interno.fk_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao.data', '>=', $ocorrenciaDataInicioPlantao)
            ->where('movimentacao.data', '<=', $ocorrenciaDataTerminoPlantao)
            ->where('movimentacao_interno.up_anterior', $unidade)
            ->where('movimentacao.tipo', '=', 'extincaopunibilidade')
            ->select('movimentacao_interno.*','movimentacao.*')
            ->where('movimentacao_interno.status', 'Finalizada')
            ->get();

        $dadosSaidaInternoPrisaoDomiciliar = DB::table('movimentacao_interno')
            ->leftJoin('interno', 'interno.id', '=', 'movimentacao_interno.fk_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao.data', '>=', $ocorrenciaDataInicioPlantao)
            ->where('movimentacao.data', '<=', $ocorrenciaDataTerminoPlantao)
            ->where('movimentacao_interno.up_anterior', $unidade)
            ->where('movimentacao.tipo', '=', 'prisaodomiciliar')
            ->select('movimentacao_interno.*','movimentacao.*')
            ->where('movimentacao_interno.status', 'Finalizada')
            ->get();

        $dadosSaidaInternoSaidaJudicial = DB::table('movimentacao_interno')
            ->leftJoin('interno', 'interno.id', '=', 'movimentacao_interno.fk_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao.data', '>=', $ocorrenciaDataInicioPlantao)
            ->where('movimentacao.data', '<=', $ocorrenciaDataTerminoPlantao)
            ->where('movimentacao_interno.up_anterior', $unidade)
            ->where('movimentacao.tipo', '=', 'saidajudicial')
            ->select('movimentacao_interno.*','movimentacao.*')
            ->where('movimentacao_interno.status', 'Finalizada')
            ->get();

        $dadosSaidaInternoFuga = DB::table('movimentacao_interno')
            ->leftJoin('interno', 'interno.id', '=', 'movimentacao_interno.fk_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao.data', '>=', $ocorrenciaDataInicioPlantao)
            ->where('movimentacao.data', '<=', $ocorrenciaDataTerminoPlantao)
            ->where('movimentacao_interno.up_anterior', $unidade)
            ->where('movimentacao.tipo', '=', 'fuga')
            ->select('movimentacao_interno.*','movimentacao.*')
            ->where('movimentacao_interno.status', 'Finalizada')
            ->get();

        $dadosSaidaInterno = array_merge(json_decode($dadosSaidaInternoAberto, true), json_decode($dadosSaidaInternoTransferencia, true), json_decode($dadosSaidaInternoAlvara, true), json_decode($dadosSaidaInternoLivramentoCondicional, true), json_decode($dadosSaidaInternoRecambiamento, true), json_decode($dadosSaidaInternoObito, true), json_decode($dadosSaidaInternoExtincaopunibilidade, true), json_decode($dadosSaidaInternoPrisaoDomiciliar, true), json_decode($dadosSaidaInternoSaidaJudicial, true), json_decode($dadosSaidaInternoFuga, true));

        $saidaInterno = array();

        foreach($dadosSaidaInterno as $entrada)
        {
            $dadosInter = InternoController::getDadosPrincipaisInterno($entrada['fk_interno']);

            array_push($saidaInterno, array(
                'nome' => $dadosInter[0]['nome'],
                'prontuario' => $entrada['fk_interno'],
                'tipo' => $entrada['tipo'],
                'horario' => Tdata::getTempoTimestamp($entrada['data']),
                'pvAnteior' => UnidadesController::getNomePav($entrada['pv_anterior']),
                'alaAnteior' => UnidadesController::getNomeAla($entrada['ala_anterior']),
                'celaAnteior' => UnidadesController::getNomeCela($entrada['cela_anterior']),
                'pvDestino' => UnidadesController::getNomePav($entrada['pavilhao_destino']),
                'alaDestino' => UnidadesController::getNomeAla($entrada['ala_destino']),
                'celaDestino' => UnidadesController::getNomeCela($entrada['cela_destino']),
            ));
        }

        // --------------------------- SAUDE E ASSISTENCIA ------------------------------------------------------------
        
        $atendimentoSaudeAssistenciaDados = DB::table("saude_assistencia")
                ->where("unidade", $unidade)
                ->where(DB::raw('CONCAT_WS(" ", data_atendimento, hora_atendimento)'), '>=', $ocorrenciaDataInicioPlantao)
                ->where(DB::raw('CONCAT_WS(" ", data_atendimento, hora_atendimento)'), '<=', $ocorrenciaDataTerminoPlantao)
                ->where('status', 'A')
                ->orderBy('hora_atendimento', 'ASC')
                ->get();
       
        $atendimentosSaudeAssistencia = array();
        foreach($atendimentoSaudeAssistenciaDados as $atendimentos)
        {
            array_push($atendimentosSaudeAssistencia, array(
               'setor' => $atendimentos->setor,
                'internoNome' => InternoController::getInternoNome($atendimentos->fk_interno),
                'internoLocalizacao' => InternoController::getLotacaoInterno($atendimentos->fk_interno),
                'internoProntuario' => $atendimentos->fk_interno,
                'horarioAtendimento' => $atendimentos->hora_atendimento,
            ));
        }
        
        // --------------------------- FIM SAUDE E ASSISTENCIA ------------------------------------------------------------
        
        $dataBusca = Tdata::TdataBR($dataLivro);
        $nomeUnidade = UnidadesController::getNomeUp($unidade);
        $nomeServidor = FacadeController::getDadosServidor(Auth::user()->cpf, "nome");
        $nomeServidorFooter = FacadeController::getNomeServidorCpf(Auth::user()->cpf)." (".Auth::user()->cpf.")";

        $dadosRodape = $nomeUnidade." - Livro de Ocorrência do Plantão ".$dataBusca." <br> Gerador pelo Servidor ".$nomeServidor[0]->nome ."(".Auth::user()->cpf.")";

        //dd($dadosOcorrencia);

        $dataInicioPlantao = Tdata::TdataBR($dataLivro);
        $dataTerminoPlantao = Tdata::TdataBR(Tdata::getDataTimestamp($timestampTerminoPlantao));
        
        $inicioPlantao =                 Tdata::TtimestampBR($ocorrenciaDataInicioPlantao);
        $terminoPlantao =                Tdata::TtimestampBR($ocorrenciaDataTerminoPlantao);
        
        
        $pdf = \PDF::loadView('ocorrencia.livro.pdfLivro', compact('dataInicioPlantao','dataTerminoPlantao','dadosEquipe','escalaPostoDiurno','escalaPostoNoturno','rondas','confere','revistas','escalaBanhoSol','revistaPatios','escolta','escoltaGep','qtdPavilhao','qtdAla','qtdCela','internosUnidadeTotal','visitantes','visitantesExterno','visitantesServidor','advogados','dadosOcorrencia','materialBelico','alimentacao','videoconferencia','dataBusca','nomeUnidade','dadosRodape','dadosMudancaCela','entradaInterno','saidaInterno','atendimentosSaudeAssistencia'))
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 30)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('header-html', view('template.header.headerLivroOcorrenciaPdf',compact('dataBusca','nomeUnidade','inicioPlantao','terminoPlantao')))
            ->setOption ('footer-center', utf8_encode('Gerado por '.$nomeServidorFooter.' em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');
        return $pdf->inline();

    }

    public function livroFechamento()
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dataFechamento = Tdata::TdataBR(self::getDataProxFechamentoLivroUnidade($lotacaoServidor));
        
        $horarioFechamento = self::getHorarioReferenciaProxFechamentoLivroUnidade($lotacaoServidor);

        return view('ocorrencia.livro.fechamentoLivro', compact('dataFechamento','horarioFechamento'));

    }

    /**
     * Geters do livro dinamico
     */
    
    // retorna a quantodade de livros fechados da unidade
    public static function getQtdLivrosFechadosUnidade($idUnidade, $data) {
        
        $dados = DB::table('ocorrencia_fechamento_livro')
                ->where('unidade', $idUnidade)
                ->where('data', $data)
                ->count();
        
        return $dados;
        
    }
    
    // retorna a data(yyyy-mm-dd) de registro do ultimo fechamento do livro de ocorrencia
    public static function getDataUltimoFechamentoLivro($idUnidade) {
        
        $getFechamento = DB::table('ocorrencia_fechamento_livro')->where('unidade', $idUnidade)->orderBy('data', 'DESC')->first();
        
        return $getFechamento->data;

    }
    
    public static function getDataPenultimoFechamentoLivro($idUnidade) {
        
        $getFechamento = DB::table('ocorrencia_fechamento_livro')->where('unidade', $idUnidade)->orderBy('data', 'DESC')->get();
        
        if(count($getFechamento) > 1)
        {
          
            return $getFechamento[1]->data;
        }else{
            
            return $getFechamento[0]->data;
        }


    }
    
    // retorna a data(yyyy-mm-dd) de registro do ultimo fechamento do livro de ocorrencia
    public static function getHorarioReferenciaUltimoFechamentoLivro($idUnidade) {
        
        $getFechamento = DB::table('ocorrencia_fechamento_livro')->where('unidade', $idUnidade)->orderBy('data', 'DESC')->orderBy('horarioReferencia', 'DESC')->first();
        
        return $getFechamento->horarioReferencia;

    }
    
    public static function getHorarioReferenciaPenultimoFechamentoLivro($idUnidade) {
        
        $getFechamento = DB::table('ocorrencia_fechamento_livro')->where('unidade', $idUnidade)->orderBy('data', 'DESC')->orderBy('horarioReferencia', 'DESC')->get();
        
        if(count($getFechamento) > 1)
        {
          
            return $getFechamento[1]->horarioReferencia;
        }else{
            
            return $getFechamento[0]->horarioReferencia;
        }
        

    }
    
    // calcula a data do proximo fechamento do livro de ocorrencia
    public static function getDataProxFechamentoLivroUnidade($idUnidade) {
        
        $getFechamento = DB::table('ocorrencia_fechamento_livro')->where('unidade', $idUnidade)->orderBy('data', 'DESC')->orderBy('horarioReferencia','DESC')->first();
        
        $horasPlantaoUnidade = UnidadesController::getHorasPlantaoUnidade($idUnidade);
        
        $dataFechamento = Tdata::getDataTimestamp(Tdata::addHourToDateTime($getFechamento->data." ".$getFechamento->horarioReferencia, $horasPlantaoUnidade));
        
        return $dataFechamento;
               
    }
    
    // retorna o proximo livro de ocorrencia baseado em uma data
    public static function getTimestampFechamentoLivroUnidadeData($idUnidade, $data, $hora) {
        
        $getFechamento = DB::table('ocorrencia_fechamento_livro')->where('unidade', $idUnidade)->orderBy('data', 'DESC')->orderBy('horarioReferencia','DESC')->first();
        
        $horasPlantaoUnidade = UnidadesController::getHorasPlantaoUnidade($idUnidade);
        
        $dataFechamento = Tdata::addHourToDateTime($data." ".$hora, $horasPlantaoUnidade);
        
        return $dataFechamento;
               
    }
    
    public static function getHorarioFechamento59Seg($idUnidade) {
        
        $timestampHoraProximoFechamento = self::getDataUltimoFechamentoLivro($idUnidade)." ".self::getHorarioReferenciaProxFechamentoLivroUnidade($idUnidade);
        $date = new DateTime($timestampHoraProximoFechamento);
        $date->sub(new \DateInterval(('PT1S')));

        $timestampHoraUltimoFechamento = self::getDataUltimoFechamentoLivro($idUnidade)." ".self::getHorarioReferenciaUltimoFechamentoLivro($idUnidade);


        $ocorrenciaHoraTerminoPlantao = Tdata::getTempoTimestamp($date->format('Y-m-d H:i:s'));
        
        return $ocorrenciaHoraTerminoPlantao;

    }
    
    // calcula o horario do proximo fechamento do livro de ocorrencia
    public static function getHorarioReferenciaProxFechamentoLivroUnidade($idUnidade) {
        
        $getFechamento = DB::table('ocorrencia_fechamento_livro')->where('unidade', $idUnidade)->orderBy('data', 'DESC')->orderBy('horarioReferencia','DESC')->first();
        
        $horasPlantaoUnidade =        UnidadesController::getHorasPlantaoUnidade($idUnidade);
        
        $dataFechamento =        Tdata::getTempoTimestamp(Tdata::addHourToDateTime($getFechamento->data." ".$getFechamento->horarioReferencia, $horasPlantaoUnidade));
        
        return $dataFechamento;
                    

    }
    
    // calcula o horario do proximo fechamento do livro de ocorrencia
    public static function getTimestampReferenciaProxFechamentoLivroUnidade($idUnidade) {
        
        $getFechamento = DB::table('ocorrencia_fechamento_livro')->where('unidade', $idUnidade)->orderBy('data', 'DESC')->orderBy('horarioReferencia','DESC')->first();
        
        $horasPlantaoUnidade =        UnidadesController::getHorasPlantaoUnidade($idUnidade);
        
        $dataFechamento = Tdata::addHourToDateTime($getFechamento->data." ".$getFechamento->horarioReferencia, $horasPlantaoUnidade);
        
        return $dataFechamento;        
                      
    }
    
    
    # !Geters dinamicos
    
    
    
    
    public function livroFechamentoStore(Request $request)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $getFechamento = DB::table('ocorrencia_fechamento_livro')->where('unidade', $lotacaoServidor)->orderBy('data', 'DESC')->orderBy('horarioReferencia','DESC')->first();
     
        
        $dataFechamento = Tdata::addDias($getFechamento->data, 1);
        
        // verifica se pode fechar o livro de ocorrência        
        if(Tdata::timestampMysqlInteiro(Tdata::TtimeStampHojeMysql()) < Tdata::timestampMysqlInteiro(self::getTimestampReferenciaProxFechamentoLivroUnidade($lotacaoServidor))){
            
            return redirect()->back()->with('alert-danger', 'Você só pode fechar o livro do plantão a partir de '. self::getHorarioReferenciaProxFechamentoLivroUnidade($lotacaoServidor).' do dia '.Tdata::TdataBR(self::getDataProxFechamentoLivroUnidade($lotacaoServidor)).".");
            
            
        }
             
        else {

            UnidadesController::addQuantitativo(Tdata::addDias($getFechamento->data, 1));

            $userData = DB::table("users")->where('cpf', Auth::user()->cpf)->get();


            if (Hash::check($request->get('password'), $userData[0]->password)) {

                $startDate = new DateTime($getFechamento->created_at);
                $diff = $startDate->diff(new DateTime(Tdata::TtimeStampHojeMysql()));
                if($diff->i < 2)
                {
                    return redirect()->back()->withInput()->with('alert-danger', "<strong>ATENÇÃO! </strong>Aguarde ". (2 - $diff->i) ." minutos para fechar o plantão");


                }else {


                    /**
                     * verificacoes antes do fechamento do livro
                     */
                    $verificaSaidaControleAcesso = DB::table('controle_acesso')
                            ->where('unidade', $lotacaoServidor)
                            ->where('tabela', "!=", "servidor")
                            ->where('data', self::getDataUltimoFechamentoLivro($lotacaoServidor))
                            ->whereNull('data_saida')->count();

                    if($verificaSaidaControleAcesso > 0)
                    {
                        return redirect()->back()->with('alert-danger', 'Existem registros de saída no controle de acesso em aberto, é nessário informar data e horário da saída dos visitantes para liberação do fechamento do livro!');

                    }


                    /**
                     * verificacao de agendamentos de atendimento de adv que nao compareceram
                     */

//                    $verifyAgendamentoHorario = DB::table('advogado_agendamento')->where('unidade', $lotacaoServidor)->where('data', $dataFechamento)->where('status','Agendado')->get();
//
//                    $updateAgendamento = DB::table('advogado_agendamento')->where('unidade', $lotacaoServidor)->where('data', $dataFechamento)->where('status','Agendado')->update(['status'=>'Falta']);
                    
                    // !verificacao de agendamentos de atendimento de adv que nao compareceram


                    $insertFechamento = DB::table('ocorrencia_fechamento_livro')->insert([
                        'unidade' => $lotacaoServidor,
                        'data' => self::getDataProxFechamentoLivroUnidade($lotacaoServidor),
                        'horarioReferencia' => self::getHorarioReferenciaProxFechamentoLivroUnidade($lotacaoServidor),
                        'cpf' => Auth::user()->cpf,
                        'created_at' => Tdata::TtimeStampHojeMysql(),
                    ]);

                    $idFechamento = DB::getPdo()->lastInsertId();

                    if ($insertFechamento) {
                        return redirect()->route('ocorrencia.livro')->with('alert-success', 'Fechamento do Livro Realizado com Sucesso!');

                        LogController::log($idFechamento, "ocorrencia_fechamento_livro", "add", Auth::user()->cpf, "Fechamento de Livro");

                    }
                }
            }

            else {

                return redirect()->back()->withInput()->with('alert-danger', 'A senha não confere! <br> Utilize a senha de acesso ao '.config('app.name'));

            }
        }
    }

    public function livroFechamentoGetAjax()
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $getFechamento = DB::table('ocorrencia_fechamento_livro')->where('unidade', $lotacaoServidor)->orderBy('data', 'DESC')->first();

        $dataInicioPlantao = self::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = self::getDataProxFechamentoLivroUnidade($lotacaoServidor);

        $configDatePicker = ['inicioPlantao'=> Tdata::TdataBR($dataInicioPlantao), 'terminoPlantao'=> Tdata::TdataBR($dataTerminoPlantao)];


        return ($configDatePicker);
    }
}