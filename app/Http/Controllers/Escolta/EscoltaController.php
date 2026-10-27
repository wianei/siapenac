<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a Escolta
|
*/

namespace App\Http\Controllers\Escolta;

use App\Classes\Constantes;
use App\Classes\Tdata;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Gep\GepController;
use App\Http\Controllers\Interno\InternoController;
use App\Http\Controllers\Judiciario\JudiciarioController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Http\Controllers\Ocorrencia\OcorrenciaController;
use App\Http\Controllers\Permissoes\PermissoesController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class EscoltaController extends Controller
{

    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');

        $this->middleware('auth');

    }

    public function callAction($method, $parameters)
    {

        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::verifyPermission("EscoltaController", $method);
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

//        $metodosAdm = collect(['cadastro','store','buscar','editar','editarStore','arquivos','show','documento']);
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


        return parent::callAction($method, $parameters);
    }


    /*
    |--------------------------------------------------------------------------
    | Getters
    |--------------------------------------------------------------------------
    |
    | Getters de dados do interno
    |
    */
    public static function getUnidadeEscolta($idEscolta)
    {
        $dados = DB::table('escolta')->where('id', $idEscolta)->get();

        return $dados[0]->unidade;
    }

    public static function getInternosEscolta($idEscolta, $not=null)
    {
        if($not != null)
        {
            $dados = DB::table('escolta_internos')->where('fk_escolta', $idEscolta)->where('fk_interno','!=',$not)->get();

        }else{
            $dados = DB::table('escolta_internos')->where('fk_escolta', $idEscolta)->get();

        }

        return $dados;
    }

    public static function getDestinoEscolta($idEscolta)
    {
        $dados = DB::table('escolta')->where('id', $idEscolta)->get();

        return $dados[0]->destino;
    }

//    public static function getMovimentacaoEscolta($idEscolta)
//    {
//        $dados = DB::table('escolta')->where('id', $idEscolta)->get();
//
//        $dadosMovimentacao = DB::table('movimentacao')->where('id', $dados[0]->fk_movimentacao)->get();
//
//        $nomeTipo = "";
//
//        if($dadosMovimentacao[0]->tipo == 'transferencia')
//        {
//            $nomeTipo = "Transferência";
//        }
//
//        return $nomeTipo;
//    }

    public function cadastro()
    {
        $lotacaoServidor = FacadeController::getLotacaoServidor(Auth::user()->cpf);

        $unidades = DB::table('unidade')->where('sigla',$lotacaoServidor)->orderBy('sigla', 'ASC')->get();

        return view('escolta.cadastro.cadastro', compact('unidades'));
    }

    public function store(Request $request)
    {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));



        /**
         * validacoes
         */
        
        $dataInicioPlantao = OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor);
        $horaInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
        $horaTerminoPlantao = OcorrenciaController::getHorarioReferenciaProxFechamentoLivroUnidade($lotacaoServidor);

        $stringInfoPlantao = "O livro de ocorrência em aberto inicia no dia ". Tdata::TdataBR(OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor)) ." às ".OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor)." e vai até ". Tdata::TdataBR(OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor))." às ".OcorrenciaController::getHorarioReferenciaProxFechamentoLivroUnidade($lotacaoServidor);


        /**
         * validacoes
         */

        $dataValidar = $request->get('data_saida');
        $horaValidar = $request->get('hora_saida');

        if(Tdata::TdataMysql($dataValidar)." ".$horaValidar < $dataInicioPlantao." ".$horaInicioPlantao || Tdata::TdataMysql($dataValidar)." ".$horaValidar > $dataTerminoPlantao." ".$horaTerminoPlantao)
        {
            return redirect()->back()->withInput()->with('alert-danger', '<strong>ATENÇÃO! </strong>Você não pode inserir uma ocorrência para datas passadas ou futuras! '.$stringInfoPlantao);
        }

        $internosValidar = $request->get('internos');
        if(!isset($internosValidar))
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Selecione os internos que foram para escolta!');

        }
        if($request->get('tipo_escolta') != "gep"){
            $agentesValidar = $request->get('agentesHidden');
            if (!isset($agentesValidar)) {
                return redirect()->back()->withInput()->with('alert-danger', 'Informe os agentes que participaram da escolta!');

            }

            $dataSaidaValidar = $request->get('data_saida');
            $horaSaidaValidar = $request->get('hora_saida');
            if ($dataSaidaValidar == null || $horaSaidaValidar == null) {
                return redirect()->back()->withInput()->with('alert-danger', 'Informe a data e horário de saída');

            }
        }
        //--------------------- !validacoes ----------------------------------

        //dd($request->all());

        // LOG - array que armazena os textos do log
        $diferencas = array();

        //dd($request->input());

        if($request->get('data_retorno') != null)
        {
            $status = "Finalizada";
        }
        elseif($request->get('data_retorno') == null && $request->get('tipo_escolta') == "gep")
        {
            $status = "Solicitada";
        }
        else{
                $status = "Em Andamento";
        }

        $dataRetorno = null;
        if($request->get('data_retorno') != null)
        {
            $dataRetorno = Tdata::TdataMysql($request->get('data_retorno'));
        }
        $horaRetorno = null;
        if($request->get('hora_retorno') != null)
        {
            $horaRetorno = $request->get('hora_retorno');
        }

        // insere os dados gerais da escolta
        $insertEscolta = DB::table('escolta')->insert([
                'unidade' => $lotacaoServidor,
                'tipo' => $request->get('tipo_escolta'),
                'motivo' => $request->get('obs'),
                'destino' => $request->get('destino'),
                'data_saida' => Tdata::TdataMysql($request->get('data_saida')),
                'hora_saida' => $request->get('hora_saida'),
                'data_retorno' => $dataRetorno,
                'hora_retorno' => $horaRetorno,
                'vtr_utilizacao' => $request->get('vtr'),
                'vtr_placa' => $request->get('vtr_placa'),
                'vtr_comandante' => $request->get('vtrComandanteHidden'),
                'vtr_motorista' => $request->get('vtrMotoristaHidden'),
                'status' =>$status,
                'responsavel' => GepController::getIdGepResponsavel($lotacaoServidor),
                'created_at' => date("Y-m-d"),
            ]
        );

        $idEscolta = DB::getPdo()->lastInsertId();

        // LOG
        $alteracoes = "Criação de escolta<br><i><strong>Unidade: ".UnidadesController::getNomeUp($lotacaoServidor)."<br>Tipo: ".$request->get('tipo_escolta')."<br>Destino: ".$request->get('destino')."<br>Data e hora da saída: ".$request->get('data_saida')."-".$request->get('hora_saida')."Data e hora de retorno: ".$request->get('data_retorno')."-".$request->get('hora_retorno')."<br>Status: ".$status."</i></strong><br>";
        array_push($diferencas, $alteracoes);



        if($request->get('tipo_escolta') != "gep") {
            // insere os agentes
            $servidores = $request->get('agentesHidden');
            foreach ($servidores as $servidor) {
                $insertServidores = DB::table('escolta_agentes')->insert([
                        'fk_escolta' => $idEscolta,
                        'matricula' => $servidor,
                    ]
                );

                // LOG
                $dadosServidor = FacadeController::getDadosServidorPorMat($servidor);
                $alteracoes = "Inserção de servidor na escolta: <i><strong>".$dadosServidor[0]->nome."(".$servidor.")</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }
        }

        $internos = $request->get('internos');
        foreach($internos as $interno)
        {
            $dadosInterno = InternoController::getDadosInterno($interno);
            $insertServidores = DB::table('escolta_internos')->insert([
                    'fk_escolta' => $idEscolta,
                    'fk_interno' => $interno,
                    'up_anterior' => $dadosInterno[0]->unidade_prisional,
                    'pav_anterior' => $dadosInterno[0]->pavilhao,
                    'ala_anterior' => $dadosInterno[0]->ala,
                    'cela_anterior' => $dadosInterno[0]->cela,
                ]
            );

            //LOG
            $nomeInterno = InternoController::getInternoNome($interno);
            $alteracoes = "Inserção de interno na escolta: <i><strong>".$nomeInterno."</i></strong><br>";
            array_push($diferencas, $alteracoes);

        }

        if($request->get('tipo_escolta') != "gep") {
            $equipamentosEscolta = $request->get('hiddenEquipamentoCautela');
            if (isset($equipamentosEscolta)) {
                // insere os equipamentos utilizados
                $equipamentos = $request->get('hiddenEquipamentoCautela');
                foreach ($equipamentos as $equipamento) {
                    $equip = explode('/', $equipamento);

                    $insertEquipamento = DB::table('escolta_equipamentos')->insert([
                            'fk_escolta' => $idEscolta,
                            'fk_equipamento' => $equip[0],
                            'quantidade' => $equip[1],
                        ]
                    );

                }
            }
        }

        // se enviar o arquivo
        if($request->file('inputFile')[0] != null) {

            $qtdArquivo = count($request->file('inputFile'));

            for ($x = 0; $x < $qtdArquivo; $x++) {

                $destinationPath = Constantes::ARQUIVOS_ESCOLTA . "/" . $idEscolta;

                $name = "escolta".$idEscolta . "_" . time() . "_" . $request->file('inputFile')[$x]->getClientOriginalName();

                $request->file('inputFile')[$x]->storeAs($destinationPath, $name);

                $insertArquivo = DB::table('escolta_arquivos')->insert(
                    ['fk_escolta' => $idEscolta,
                        'nome_arquivo' => $name,
                        'created_at' => date("Y-m-d")
                    ]
                );

                //LOG
                $alteracoes = "Inserção de arquivo na escolta, arquivo: <i><strong>".$name."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }
        }


        if ($insertEscolta) {

            $textoLog = implode(" ", $diferencas);
            LogController::log($idEscolta,"escolta","add", Auth::user()->cpf, $textoLog);

            return redirect()->route('escolta.buscar')->with('alert-success', 'Escolta inserida com sucesso!');

        }else{

            return redirect()->route('escolta.buscar')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }



    }

    public function buscar(Request $request)
    {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula');

        /**
         * se não realizar uma busca
         * - Evita que mostre todos os resultados sem realizar uma busca
         */
        if(!strpos($url,"?"))
        {

            $dados = false;

        }else {

            // retira a notificação
            if(strpos($url,"Cancelada"))
            {
                $gepCancelada = DB::table('escolta')->where('unidade', $lotacaoServidor)->where('tipo', 'gep')->where('status', 'Cancelada')->limit(10)->get();
                foreach($gepCancelada as $gep)
                {
                    $dadosNotificacoesGep = DB::table('notificacao_lida')->where('tipo','gep')->where('fk_servidor', $matriculaServidorLogado[0]->matricula)->where('fk_notificacao', $gep->id)->get();

                    if(count($dadosNotificacoesGep) == 0)
                    {
                        $insertNotificacaoLida = DB::table('notificacao_lida')->insert([
                           'tipo' => 'gep',
                           'fk_notificacao' => $gep->id,
                           'fk_servidor' => $matriculaServidorLogado[0]->matricula,
                           'fk_lido' => 'S',
                           'hora' => Tdata::hora(),
                           'created_at' => Tdata::TdataHojeMysql()
                        ]);
                    }

                }

            }


            $escolta = DB::table('escolta');

            $destino = $request->get('destino');
            $unidade_prisional = $request->get('unidade_prisional');
            $data_saida = Tdata::TdataMysql($request->get('data_saida'));
            $status = $request->get('status');
            $vtr = $request->get('vtr');
            $tipo_escolta = $request->get('tipo_escolta');

            if ($destino != null) {
                $escolta->where('escolta.destino', 'LIKE', '%' . $destino . '%');
            }

            if ($unidade_prisional != null) {
                $escolta->where('escolta.unidade', $unidade_prisional);
            }

            if ($data_saida != null) {
                $escolta->where('escolta.data_saida', $data_saida);
            }

            if ($status != null) {
                $escolta->where('escolta.status', $status);
            }

            if ($vtr != null) {
                $escolta->where('escolta.vtr_utilizacao', $vtr);
            }

            if ($tipo_escolta != null) {
                $escolta->where('escolta.tipo', $tipo_escolta);
            }

            if ($tipo_escolta == null) {
                $escolta->where('escolta.unidade', $lotacaoServidor);
            }


            $agentes = $request->get('agentesHidden');
            if(isset($agentes)){

                $escolta->leftJoin('escolta_agentes',function($query){
                    $query->on('escolta_agentes.fk_escolta', '=' ,'escolta.id');
                });

                $searchAgentes = "'escolta.destino' ''";

                foreach($agentes as $agente)
                {
                    $escolta->where('escolta_agentes.matricula', $agente);

                }

                // passa a query para a paginacao "->appends..."
                $dados = $escolta->select("escolta.id", "escolta.destino","escolta.tipo","escolta.id","escolta.status","escolta.vtr_utilizacao","escolta.vtr_placa","escolta.hora_saida", "escolta.hora_retorno", DB::raw("DATE_FORMAT(escolta.data_saida,'%d/%m/%Y') as dataSaida"), DB::raw("DATE_FORMAT(escolta.data_retorno,'%d/%m/%Y') as dataRetorno"),"escolta_agentes.*")->orderBy('escolta.data_saida','DESC')->paginate(50)->appends($request->input());


            }

            // passa a query para a paginacao "->appends..."
            $dados = $escolta->select("escolta.id", "escolta.destino","escolta.tipo","escolta.id","escolta.status","escolta.vtr_utilizacao","escolta.vtr_placa","escolta.hora_saida", "escolta.hora_retorno", DB::raw("DATE_FORMAT(escolta.data_saida,'%d/%m/%Y') as dataSaida"), DB::raw("DATE_FORMAT(escolta.data_retorno,'%d/%m/%Y') as dataRetorno"))->orderBy('escolta.data_saida','DESC')->paginate(50)->appends($request->input());


        }

        $unidades = DB::table('unidade')->where('status',1)->orderBy('sigla', 'ASC')->get();

        return view('escolta.buscar.buscar', compact('dados','unidades'));


    }

    public function editar($id)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dados = DB::table('escolta')->where('id',$id)->get();

        if($dados[0]->unidade != $lotacaoServidor)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para alterar as escoltas de outra unidade!');

        }

        $dataRetorno = $dados[0]->data_retorno;

        return view('escolta.editar.editar', compact('id','dataRetorno'));

    }

    public function editarStore(Request $request, $id)
    {
        //dd($request->all());

        $diferencas = array();

        $dataRetorno = $request->get('data_retorno');

        $dados = DB::table('escolta')->where('id',$id)->get();

        if($dataRetorno == null)
        {
            $dtRetorno =  $dados[0]->data_retorno;
            $status = $dados[0]->status;

        }else{

            $dtRetorno = Tdata::TdataMysql($request->get('data_retorno'));
            $status = "Finalizada";

            $alteracoes = "Alteração da escolta <br> Inserção da data de retorno: <i><strong>".$request->get('data_retorno')."</i></strong><br>Alteração de status para: <i><strong>".$status."</i></strong><br>";
            array_push($diferencas, $alteracoes);


        }

            $updateEscolta = DB::table('escolta')->where('id',$id)->update([
                    'data_retorno' => $dtRetorno,
                    'hora_retorno' => $request->get('hora_retorno'),
                    'status' => $status,
                    'update_at' => date("Y-m-d"),
                ]
            );




        // se enviar o arquivo
        if($request->file('inputFile')[0] != null) {

            $qtdArquivo = count($request->file('inputFile'));

            for ($x = 0; $x < $qtdArquivo; $x++) {

                $destinationPath = Constantes::ARQUIVOS_ESCOLTA . "/" . $id;

                $name = "escolta" . $id . "_" . time() . "_" . $request->file('inputFile')[$x]->getClientOriginalName();

                $request->file('inputFile')[$x]->storeAs($destinationPath, $name);

                $insertArquivo = DB::table('escolta_arquivos')->insert(
                    ['fk_escolta' => $id,
                        'nome_arquivo' => $name,
                        'created_at' => date("Y-m-d")
                    ]
                );

                $alteracoes = "Inserção de arquivo na escolta, arquivo: <i><strong>".$name."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }
        }



        if ($updateEscolta) {

            $textoLog = implode(" ", $diferencas);
            LogController::log($id,"escolta","update", Auth::user()->cpf, $textoLog);

            return redirect()->route('escolta.buscar')->with('alert-success', 'Escolta alterada com sucesso!');

        }else{

            return redirect('escolta.buscar')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }

    public function arquivos($id)
    {
        $dados = DB::table('escolta_arquivos')
            ->leftJoin('escolta','escolta.id','=','escolta_arquivos.fk_escolta')
            ->where('fk_escolta', $id)->get();

        foreach($dados as $arquivo)
        {
            $arquivo->data_saida = Tdata::TdataBR($arquivo->data_saida);
            $arquivo->data_retorno = Tdata::TdataBR($arquivo->data_retorno);
            $arquivo->created_at = Tdata::TdataBR($arquivo->created_at);
            $arquivo->unidade = UnidadesController::getNomeUp($arquivo->unidade);
        }
        

        return view('escolta.arquivos.arquivos', compact('dados'));
    }

    public function show($id)
    {
        $VTR['placa'] = "";
        $VTR['comandante'] = "";
        $VTR['motorista'] = "";

        $escolta = DB::table('escolta')->where('id',$id)->get();

        foreach($escolta as $esc)
        {
            if(is_numeric($esc->destino))
            {
                $esc->destino = JudiciarioController::getNomeVaraComarca($esc->destino);
            }

            $esc->responsavel = UnidadesController::getNomeUp($esc->responsavel);
            if($esc->unidade != "judiciario"){
                $esc->unidade = UnidadesController::getNomeUp($esc->unidade);
            }
            else{
                $esc->unidade = "Judiciário";
            }
            $esc->data_saida = Tdata::TdataBR($esc->data_saida);
            if($esc->data_retorno != null){
                $esc->data_retorno = Tdata::TdataBR($esc->data_retorno);
            }else{
                $esc->data_retorno = "";
            }
            $VTR['placa'] = $esc->vtr_placa;
            $VTR['comandante'] = FacadeController::getNomeServidor($esc->vtr_comandante);
            $VTR['motorista'] = FacadeController::getNomeServidor($esc->vtr_motorista);
        }

        $agps = DB::table('escolta_agentes')->where('fk_escolta', $id)->get();

        $agentes = array();

        foreach($agps as $agp) {

            $dados = DB::connection('mysql2')->table('servidors')->where('matricula', $agp->matricula)
                ->leftJoin('lotacaos', 'lotacaos.id', '=', 'servidors.lotacao')
                ->select('servidors.*', 'lotacaos.descricao')->get();


            array_push($agentes, array(
                "id" => $dados[0]->id,
                "nome" => $dados[0]->nome,
                "matricula" => $dados[0]->matricula,
                "cpf" => $dados[0]->cpf,
                "lotacao" => utf8_encode($dados[0]->descricao),
            ));
        }

        $geps = DB::table('gep')
            ->join('gep_internos','gep_internos.fk_gep','=','gep.id')
            ->where('gep_internos.fk_escolta',$id)
            ->select('gep.id','gep.vtr_comandante','gep.vtr_motorista','gep.vtr_placa','gep.data_saida','gep.hora_saida','gep.data_retorno','gep.hora_retorno')
            ->get();
        $geps = json_decode($geps,true);

        $viaturas = collect([]);

        foreach($geps as $gep) {

            $qtd = DB::table('gep_internos')
                ->where('fk_gep',$gep['id'])
                ->count();

            $viaturas->push(array(
                "id" => $gep['id'],
                "placa" => $gep['vtr_placa'],
                "comandante" => FacadeController::getNomeServidor($gep['vtr_comandante']),
                "motorista" => FacadeController::getNomeServidor($gep['vtr_motorista']),
                "qtd_internos" => $qtd,
                "data_hora_saida" => Tdata::TdataBR($gep['data_saida'])." ".$gep['hora_saida'],
                "data_hora_retorno" => Tdata::TdataBR($gep['data_retorno'])." ".$gep['hora_retorno']
            ));
            $viaturas = $viaturas->unique('id');
//
//            array_push($viaturas, array(
//                "id" => $gep['id'],
//                "placa" => $gep['vtr_placa'],
//                "comandante" => FacadeController::getNomeServidor($gep['vtr_comandante']),
//                "motorista" => FacadeController::getNomeServidor($gep['vtr_motorista']),
//                "qtd_internos" => $qtd,
//                "data_hora_saida" => Tdata::TdataBR($gep['data_saida'])." ".$gep['hora_saida'],
//                "data_hora_retorno" => Tdata::TdataBR($gep['data_retorno'])." ".$gep['hora_retorno']
//            ));
        }



        $eqps = DB::table('escolta_equipamentos')->where('fk_escolta', $id)->get();
        $eqps = json_decode($eqps,true);

        $equipamentos = array();

        foreach($eqps as $eqp) {

            $dados = DB::table('armaria_equipamento')
                ->where('id',$eqp['fk_equipamento'])
                ->get();

            $dados[0]->validade = Tdata::TdataBR($dados[0]->validade);

            $dados[0]->fk_fabricante = DB::table('armaria_equipamento_fabricante')
                ->select('nome')
                ->where('id',$dados[0]->fk_fabricante)
                ->get();
            $dados[0]->fk_fabricante = json_decode($dados[0]->fk_fabricante,true)[0]['nome'];

            $nome = "";
            if($dados[0]->equipamento_tipo == "armaFogo"){
                $nome = "Arma de fogo ".$dados[0]->equipamento_especie." (".$dados[0]->equipamento_especificacao." - Calibre ".$dados[0]->calibre.") - Fabricante ".$dados[0]->fk_fabricante." (".$dados[0]->modelo.") - Nº de Série: ".$dados[0]->n_serie;
            }
            elseif($dados[0]->equipamento_tipo == "municao"){
                $nome = "Munição ".$dados[0]->equipamento_especie." - Calibre ".$dados[0]->calibre." - Munição ".$dados[0]->modelo." - Fabricante ".$dados[0]->fk_fabricante." - Validade ".$dados[0]->validade." - Lote ".$dados[0]->lote;
            }
            elseif($dados[0]->equipamento_tipo == "carregador"){
                $nome = "Carregador ".$dados[0]->equipamento_especie.", Calibre ".$dados[0]->calibre.", Capacidade: ".$dados[0]->volume_peso.", Fabricante ".$dados[0]->fk_fabricante.", Modelo ".$dados[0]->modelo;
            }
            elseif($dados[0]->equipamento_tipo == "algema"){
                $nome = "Algema ".$dados[0]->equipamento_especie.", Tipo: ".$dados[0]->equipamento_especificacao.", Fabricante ".$dados[0]->fk_fabricante.", Nº de Série ".$dados[0]->n_serie;
            }
            elseif($dados[0]->equipamento_tipo == "granada"){
                $nome = "Granada ".$dados[0]->fk_fabricante." ".$dados[0]->equipamento_especie." ".$dados[0]->equipamento_especificacao." ".$dados[0]->modelo.", Validade ".$dados[0]->validade.", Lote ".strtoupper($dados[0]->lote);
            }
            elseif($dados[0]->equipamento_tipo == "epi"){
                if($dados[0]->equipamento_especie == "Capacete" || $dados[0]->equipamento_especie == "Escudo"){
                    $nome = "EPI ".$dados[0]->equipamento_especie.", Fabricante ".$dados[0]->fk_fabricante.", Validade ".$dados[0]->validade.", Lote ".strtoupper($dados[0]->lote).", Nº de Série ".$dados[0]->n_serie;
                }
                else{
                    $nome = "EPI ".$dados[0]->equipamento_especie;
                }
            }
            elseif($dados[0]->equipamento_tipo == "colete"){
                $nome = "Colete ".$dados[0]->fk_fabricante.", Tamanho ".$dados[0]->tamanho.", Validade ".$dados[0]->validade.", Nº de Série ".$dados[0]->n_serie;
            }
            elseif($dados[0]->equipamento_tipo == "espargidor"){
                $nome = "Espargidor ".$dados[0]->fk_fabricante.", Modelo ".$dados[0]->modelo.", Validade ".$dados[0]->validade.", Volume/Peso ".$dados[0]->volume_peso;
            }
            array_push($equipamentos, array(
                "nome" => $nome,
                "quantidade" => $eqp['quantidade'],
            ));
        }

        $relacaoInterno = DB::table('escolta_internos')->where('fk_escolta',$id)->get();

        $dadosInterno = array();
        foreach($relacaoInterno as $relacao)
        {
            $dadosInternos = (InternoController::getDadosPrincipaisInterno($relacao->fk_interno));

            $dadosFoto = DB::table('foto')
                ->where('fk_id', $dadosInternos[0]['id'])
                ->where('fk_tabela', 'interno')
                ->where('tipo','principal')->get();
            $dadosFoto = json_decode($dadosFoto,true);

            if($dadosFoto == []){
                $dadosFoto[0]['tipo'] = "";
                $dadosFoto[0]['legenda'] = "";
                $dadosFoto[0]['created_at'] = "";
                $dadosFoto[0]['updated_at'] = "";
            }

            array_push($dadosInterno, array(
                "nome" => $dadosInternos[0]['nome'],
                "id" => $dadosInternos[0]['id'],
                "faccao" => InternoController::getNomeFaccao($dadosInternos[0]['faccao']),
                "periculosidade" => $dadosInternos[0]['periculosidade'],
                "lideranca" => $dadosInternos[0]['lideranca'],
                "foto" => $dadosInternos[0]['foto'],
                "fototipo" => $dadosFoto[0]['tipo'],
                "fotolegenda" => $dadosFoto[0]['legenda'],
                "fotodatainserida" => $dadosFoto[0]['created_at'],
                "fotodataalterada" => $dadosFoto[0]['updated_at'],
                "sigla" => $dadosInternos[0]['sigla']
            ));

        }

        $arquivos = DB::table('escolta_arquivos')->where('fk_escolta', $id)->get();

        foreach($arquivos as $arquivo)
        {
            $arquivo->created_at = Tdata::TdataBR($arquivo->created_at);
        }

        // dados Log
        if(count(LogController::getDadosLog($id, "escolta")) != 0) {
            $dadosLog = LogController::getDadosLog($id, "escolta");
            $userLog = FacadeController::getDadosServidor($dadosLog[0]->cpf_usuario, "nome");
            $textoLog = "Registrado por: " . $userLog[0]->nome . " (" . $dadosLog[0]->cpf_usuario . ") em " . Tdata::TtimestampBR($dadosLog[0]->created_at);
        }
        else{
            $textoLog = "Sem log registrado.";
        }

        return view('escolta.show.show', compact('escolta','viaturas','agentes','equipamentos','VTR','dadosInterno','arquivos','textoLog'));
    }

    public function documento($id)
    {
        $VTR['placa'] = "";
        $VTR['comandante'] = "";
        $VTR['motorista'] = "";

        $escolta = DB::table('escolta')->where('id',$id)->get();
        foreach($escolta as $esc)
        {
            $esc->unidade = UnidadesController::getNomeUp($esc->unidade);
            $esc->data_saida = Tdata::TdataBR($esc->data_saida);
            if($esc->data_retorno != null){
                $esc->data_retorno = Tdata::TdataBR($esc->data_retorno);
            }else{
                $esc->data_retorno = "";
            }
            $VTR['placa'] = $esc->vtr_placa;
            $VTR['comandante'] = $esc->vtr_comandante;
            $VTR['motorista'] = $esc->vtr_motorista;
        }

        $agps = DB::table('escolta_agentes')->where('fk_escolta', $id)->get();

        $agentes = array();

        foreach($agps as $agp) {

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

        $eqps = DB::table('escolta_equipamentos')->where('fk_escolta', $id)->get();
        $eqps = json_decode($eqps,true);

        $equipamentos = array();

        foreach($eqps as $eqp) {

            $dados = DB::table('armaria_equipamento')
                ->where('id',$eqp['fk_equipamento'])
                ->get();

            $dados[0]->validade = Tdata::TdataBR($dados[0]->validade);

            $nome = "";
            if($dados[0]->equipamento_tipo == "armaFogo"){
                $nome = "Arma de fogo ".$dados[0]->equipamento_especie." (".$dados[0]->equipamento_especificacao." - Calibre ".$dados[0]->calibre.") - Fabricante ".$dados[0]->fk_fabricante." (".$dados[0]->modelo.") - Nº de Série: ".$dados[0]->n_serie;
            }
            elseif($dados[0]->equipamento_tipo == "municao"){
                $nome = "Munição ".$dados[0]->equipamento_especie." - Calibre ".$dados[0]->calibre." - Munição ".$dados[0]->modelo." - Fabricante ".$dados[0]->fk_fabricante." - Validade ".$dados[0]->validade." - Lote ".$dados[0]->lote;
            }
            elseif($dados[0]->equipamento_tipo == "carregador"){
                $nome = "Carregador ".$dados[0]->equipamento_especie.", Calibre ".$dados[0]->calibre.", Capacidade: ".$dados[0]->volume_peso.", Fabricante ".$dados[0]->fk_fabricante.", Modelo ".$dados[0]->modelo;
            }
            elseif($dados[0]->equipamento_tipo == "algema"){
                $nome = "Algema ".$dados[0]->equipamento_especie.", Tipo: ".$dados[0]->equipamento_especificacao.", Fabricante ".$dados[0]->fk_fabricante.", Nº de Série ".$dados[0]->n_serie;
            }
            elseif($dados[0]->equipamento_tipo == "granada"){
                $nome = "Granada ".$dados[0]->fk_fabricante." ".$dados[0]->equipamento_especie." ".$dados[0]->equipamento_especificacao." ".$dados[0]->modelo.", Validade ".$dados[0]->validade.", Lote ".strtoupper($dados[0]->lote);
            }
            elseif($dados[0]->equipamento_tipo == "epi"){
                if($dados[0]->equipamento_especie == "Capacete" || $dados[0]->equipamento_especie == "Escudo"){
                    $nome = "EPI ".$dados[0]->equipamento_especie.", Fabricante ".$dados[0]->fk_fabricante.", Validade ".$dados[0]->validade.", Lote ".strtoupper($dados[0]->lote).", Nº de Série ".$dados[0]->n_serie;
                }
                else{
                    $nome = "EPI ".$dados[0]->equipamento_especie;
                }
            }
            elseif($dados[0]->equipamento_tipo == "colete"){
                $nome = "Colete ".$dados[0]->fk_fabricante.", Tamanho ".$dados[0]->tamanho.", Validade ".$dados[0]->validade.", Nº de Série ".$dados[0]->n_serie;
            }
            elseif($dados[0]->equipamento_tipo == "espargidor"){
                $nome = "Espargidor ".$dados[0]->fk_fabricante.", Modelo ".$dados[0]->modelo.", Validade ".$dados[0]->validade.", Volume/Peso ".$dados[0]->volume_peso;
            }
            array_push($equipamentos, array(
                "nome" => $nome,
                "quantidade" => $eqp['quantidade'],
            ));
        }

        $relacaoInterno = DB::table('escolta_internos')->where('fk_escolta',$id)->get();

        $dadosInterno = array();
        foreach($relacaoInterno as $relacao)
        {
            $dadosInternos = (InternoController::getDadosPrincipaisInterno($relacao->fk_interno));

            array_push($dadosInterno, array(
                "nome" => $dadosInternos[0]['nome'],
                "faccao" => $dadosInternos[0]['facNome'],
                "mae" => $dadosInternos[0]['mae'],
                "nascimento" => $dadosInternos[0]['data_nascimento'],
                "id" => $dadosInternos[0]['id'],
                "lideranca" => $dadosInternos[0]['lideranca'],
                "periculosidade" => $dadosInternos[0]['periculosidade'],
                "foto" => $dadosInternos[0]['foto'],
                "sigla" => $dadosInternos[0]['sigla']
            ));

        }

        $arquivos = DB::table('escolta_arquivos')->where('fk_escolta', $id)->get();

        foreach($arquivos as $arquivo)
        {
            $arquivo->created_at = Tdata::TdataBR($arquivo->created_at);
        }

        $nomeServidor = FacadeController::getNomeServidorCpf(Auth::user()->cpf)." (".Auth::user()->cpf.")";

        $dadosRodape = $escolta[0]->unidade." - Documentação de Escolta  <br> Gerador pelo Servidor ".Auth::user()->name ."(".Auth::user()->cpf.")";


        $pdf = \PDF::loadView('escolta.documento.pdf', compact('escolta','agentes','equipamentos','dadosInterno','arquivos','dadosRodape'))
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 30)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('header-html', view('template.header.headerEscoltaPdf',compact('escolta')))
            ->setOption ('footer-center', 'Gerado por '.$nomeServidor.' em'.date('\ d/m/Y\ ').' | Página [page] de [topage]')
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');

        return $pdf->inline();

        //return view('escolta.show.show', compact('escolta','agentes','dadosInterno','arquivos'));

    }

    public function escoltaStatus_idMovimentacao($idMovimentacao){
        $qtdEscoltas = DB::table('movimentacao_interno')->where('fk_escolta',$idMovimentacao)->where('status','!=','Finalizada')->count();
        if($qtdEscoltas == 0){
            DB::table('escolta')->where('fk_movimentacao',$idMovimentacao)->update(
                [
                    'status'=>'Finalizada'
                ]);
        }
    }

}