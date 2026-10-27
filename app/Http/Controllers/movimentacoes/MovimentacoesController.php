<?php


/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados ao interno
|
*/

namespace App\Http\Controllers\Movimentacoes;

use App\Classes\Constantes;
use App\Classes\Tdata;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Gep\GepController;
use App\Http\Controllers\Interno\InternoController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Http\Controllers\Ocorrencia\OcorrenciaController;
use App\Http\Controllers\Permissoes\PermissoesController;
use App\Interno;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Scalar\String_;

class MovimentacoesController extends Controller
{

    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        //$this->middleware('auth');
        ini_set('max_execution_time', 90000);
    }

    public function callAction($method, $parameters)
    {
        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::verifyPermission("MovimentacoesController", $method);
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

//        $metodosAdm = collect(['buscar','mudancacelaShow','storeMudarCela','progressaoregressaoShow','storeProgressaoRegressao','alvaraShow','storeAlvara','livramentocondicionalShow','storeLivramentoCondicional','fugaShow','storeFuga','recambiamentoShow','storeRecambiamento','obitoShow','storeObito','extincaopunibilidadeShow','storeExtincaoPunibilidade','prisaodomiciliarShow','storePrisaoDomiciliar','mudancasentenca','transferenciaShow','storeTransferir','receberInterno','alocarInterno']);
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

    public static function getQtdTempoTransferenciaNaoConfirmada($dias)
    {
        $dataInicial = Tdata::subDias(date("Y-m-d"), $dias);
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        return DB::table('movimentacao_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao_interno.up_anterior', $lotacaoServidor)
            ->where('movimentacao_interno.status','Pendente')
            ->where('movimentacao.data', '<=', $dataInicial." 23:59:59")
            ->count();
    }

    public static function getQtdTempoTransferenciaRecebidaNaoConfirmada($dias)
    {
        $dataInicial = Tdata::subDias(date("Y-m-d"), $dias);
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        return DB::table('movimentacao_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao.unidade_destino', $lotacaoServidor)
            ->where('movimentacao_interno.status','Pendente')
            ->where('movimentacao.data', '<=', $dataInicial." 23:59:59")
            ->count();
    }

    public static function getTempoTransferenciaNaoConfirmada($dias)
    {
        $dataInicial = Tdata::subDias(date("Y-m-d"), $dias);
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dadosTransferencia = DB::table('movimentacao_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao_interno.up_anterior', $lotacaoServidor)
            ->where('movimentacao_interno.status','Pendente')
            ->where('movimentacao.data', '<=', $dataInicial." 23:59:59")
            ->get();

        $transferenciasNaoAceitas = array();

        foreach($dadosTransferencia as $item)
        {
            array_push($transferenciasNaoAceitas, array(
                'prontuario' => $item->fk_interno,
                'nome' => InternoController::getInternoNome($item->fk_interno),
                'unidadeDestino' => UnidadesController::getSiglaUp($item->unidade_destino),
                'data' => Tdata::TdataBR(Tdata::getDataTimestamp($item->data))

            ));
        }

        return $transferenciasNaoAceitas;
    }

    public static function getTempoTransferenciaRecebidaNaoConfirmada($dias)
    {
        $dataInicial = Tdata::subDias(date("Y-m-d"), $dias);
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dadosTransferencia = DB::table('movimentacao_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao.unidade_destino', $lotacaoServidor)
            ->where('movimentacao_interno.status','Pendente')
            ->where('movimentacao.data', '<=', $dataInicial." 23:59:59")
            ->get();

        $transferenciasNaoAceitas = array();

        foreach($dadosTransferencia as $item)
        {
            array_push($transferenciasNaoAceitas, array(
                'prontuario' => $item->fk_interno,
                'nome' => InternoController::getInternoNome($item->fk_interno),
                'unidadeDestino' => UnidadesController::getSiglaUp($item->unidade_destino),
                'data' => Tdata::TdataBR(Tdata::getDataTimestamp($item->data))

            ));
        }

        return $transferenciasNaoAceitas;
    }

    public static function getLotacaoAnteriorMovimentacao($idInterno, $idMovimentacao)
    {
        $dados = DB::table('movimentacao_interno')->where('fk_interno', $idInterno)->where('fk_movimentacao', $idMovimentacao)->get();

        return $lotAnterior =  UnidadesController::getSiglaUp($dados[0]->up_anterior)." - ".UnidadesController::getNomePav($dados[0]->pv_anterior)." - ".UnidadesController::getNomeAla($dados[0]->ala_anterior)." - ".UnidadesController::getNomeCela($dados[0]->cela_anterior);
    }


    public static function getDataUltimoAlvara($interno)
    {
        //$lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dados = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
                ->where(function ($query) {
                    $query->where('movimentacao.tipo', '=', 'alvara');
                })
                ->where('movimentacao_interno.fk_interno', $interno)
                ->orderBy('movimentacao.data', 'DESC')->first();
        if(count($dados) > 0) {

            $data = Tdata::getDataTimestamp($dados->data);

            return Tdata::TdataBR($data);

        }else{

            return " *** ";
        }
    }

    public static function getDataUltimaCondicional($interno)
    {
        //$lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dados = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
                ->where(function ($query) {
                    $query->where('movimentacao.tipo', '=', 'livramentocondicional');
                })
                ->where('movimentacao_interno.fk_interno', $interno)
                ->orderBy('movimentacao.data', 'DESC')->first();
        if(count($dados) > 0) {

            $data = Tdata::getDataTimestamp($dados->data);

            return Tdata::TdataBR($data);

        }else{

            return null;
        }
    }

    public static function getDataUltimaFuga($interno)
    {
        //$lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dados = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
                ->where(function ($query) {
                    $query->where('movimentacao.tipo', '=', 'fuga');
                })
                ->where('movimentacao_interno.fk_interno', $interno)
                ->orderBy('movimentacao.data', 'DESC')->first();
        if(count($dados) > 0) {

            $data = Tdata::getDataTimestamp($dados->data);

            return Tdata::TdataBR($data);

        }else{

            return null;
        }
    }

    public static function getDataObito($interno)
    {
        //$lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dados = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
                ->where(function ($query) {
                    $query->where('movimentacao.tipo', '=', 'obito');
                })
                ->where('movimentacao_interno.fk_interno', $interno)
                ->orderBy('movimentacao.data', 'DESC')->first();
        if(count($dados) > 0) {

            $data = Tdata::getDataTimestamp($dados->data);

            return Tdata::TdataBR($data);

        }else{

            return null;
        }
    }

    public static function getDataUltimaEntradaMonitoramento($interno)
    {
        //$lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dados = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
                ->where(function ($query) {
                    $query->where('movimentacao.tipo', '=', 'transferenciaentrada')->where('unidade_destino', 8);
                })
                ->where('movimentacao_interno.fk_interno', $interno)
                ->orderBy('movimentacao.data', 'DESC')->first();
        if(count($dados) > 0) {

            $data = Tdata::getDataTimestamp($dados->data);

            return Tdata::TdataBR($data);

        }else{

            return null;
        }
    }

    public static function getDataUltimaPrisaoDomiciliar($interno)
    {
        //$lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dados = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
                ->where(function ($query) {
                    $query->where('movimentacao.tipo', '=', 'prisaodomiciliar');
                })
                ->where('movimentacao_interno.fk_interno', $interno)
                ->orderBy('movimentacao.data', 'DESC')->first();
        if(count($dados) > 0) {

            $data = Tdata::getDataTimestamp($dados->data);

            return Tdata::TdataBR($data);

        }else{

            return null;
        }
    }

    public static function getDataUltimaProgressaoSemiAberto($interno)
    {
        //$lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dados = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
                ->where(function ($query) {
                    $query->where('movimentacao.tipo', '=', 'progressaosemiaberto');
                })
                ->where('movimentacao_interno.fk_interno', $interno)
                ->orderBy('movimentacao.data', 'DESC')->first();
        if(count($dados) > 0) {

            $data = Tdata::getDataTimestamp($dados->data);

            return Tdata::TdataBR($data);

        }else{

            return null;
        }
    }

    public static function getDataUltimaProgressaoAberto($interno)
    {
        //$lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dados = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
                ->where(function ($query) {
                    $query->where('movimentacao.tipo', '=', 'progressaoaberto');
                })
                ->where('movimentacao_interno.fk_interno', $interno)
                ->orderBy('movimentacao.data', 'DESC')->first();
        if(count($dados) > 0) {

            $data = Tdata::getDataTimestamp($dados->data);

            return Tdata::TdataBR($data);

        }else{

            return null;
        }
    }

    public static function getDataEntradaUnidadePrisional($interno, $unidadePrisional)
    {
        //$lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dados = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
                ->where('movimentacao.unidade_destino', $unidadePrisional)
                ->where(function ($query) {
                    $query->where('movimentacao.tipo', '=', 'entradaunidadeprisional')
                        ->orWhere('movimentacao.tipo', 'transferenciaentrada');
                })
                ->where('movimentacao_interno.fk_interno', $interno)
                ->select('movimentacao.tipo','movimentacao.data')
                ->orderBy('movimentacao.data', 'DESC')->get();
        if(count(json_decode($dados, true)) > 0) {

            $data = Tdata::getDataTimestamp($dados[0]->data);

            return Tdata::TdataBR($data);
        }else{

            return " *** ";
        }
    }

    public function buscar()
    {
        return view('movimentacoes.buscar');
    }

    public static function internosMov($idMov)
    {
        $temp = DB::table('movimentacao_interno')
                    ->where('fk_movimentacao',$idMov)
                    ->select('fk_interno','status','up_anterior','pv_anterior','ala_anterior','cela_anterior')
                    ->get();
        $temp = json_decode($temp,true);

        foreach($temp as $key => $interno){
            $temp[$key]['id'] = $interno['fk_interno'];
            $temp[$key]['nome'] = InternoController::getInternoNome($interno['fk_interno']);
            $temp[$key]['status'] = self::statusMov($interno['status']);
            $temp[$key]['lotacao_anterior'] = UnidadesController::getSiglaUp($interno['up_anterior'])." - ".UnidadesController::getNomePav($interno['pv_anterior'])." - ".UnidadesController::getNomeAla($interno['ala_anterior'])." - ".UnidadesController::getNomeCela($interno['cela_anterior']);
        }
        return $temp;
    }

    public static function arquivosMov($idMov)
    {
        $temp = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $idMov)->get();
        $temp = json_decode($temp, true);
        return $temp;
    }

    public static function logMov($idMov)
    {
        $dadosLog = LogController::getDadosLog($idMov, 'movimentacao', 'add');
        if(count($dadosLog) != 0){
            $temp = FacadeController::getNomeServidorCpf($dadosLog[0]->cpf_usuario)." em ".Tdata::TtimestampBR($dadosLog[0]->created_at);
        }
        else{
            $temp = "Sem log registrado.";
        }
        return $temp;
    }

    public static function destinoMov($unidade_destino)
    {
        return UnidadesController::getSiglaUp($unidade_destino);
    }

    public static function statusMov($status)
    {
        if($status == "Pendente"){
            return "Aguardando Confirmação de Recebimento";
        }else if($status == "Finalizada"){
            return "Recebimento Confirmado";
        }else if($status == "Cancelada"){
            return "Transferência Cancelada";
        }
        return "";
    }

    public static function temTransfPendente($idInterno){
        return (DB::table('movimentacao_interno')
        ->where('movimentacao_interno.status','Pendente')
        ->where('movimentacao_interno.fk_interno',$idInterno)
        ->count() >= 1);
    }

    public function mudancacelaShow(Request $request)
    {

        $usuarioLogadoUP = DB::table('unidade')->where('sigla', FacadeController::getLotacaoServidor(Auth::user()->cpf))->get();
        $usuarioLogadoUP = json_decode($usuarioLogadoUP, true)[0];
        $unidadeLogado = FacadeController::getLotacaoServidor(Auth::user()->cpf);
        $unidadeLogado = UnidadesController::getIdUpPorSigla($unidadeLogado);
        $usuarioLogadoPVs = UnidadesController::getPavilhoes($unidadeLogado);
        $unidades = DB::table('unidade')->where('status', 1)->orderBy('sigla', 'ASC')->get();
        $unidades = json_decode($unidades,true);

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $mudancasCelas = null;
    
        if(strpos($url,"?")){


            $mudancasCelas = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                ->where('movimentacao.tipo', 'mudancacela');

            if ($request->get('unidade_prisionalh'))
                $mudancasCelas = $mudancasCelas->where('movimentacao.unidade_destino', $request->get('unidade_prisionalh'));

            if ($request->get('pavilhaoh'))
                $mudancasCelas = $mudancasCelas->where('movimentacao.pavilhao_destino', $request->get('pavilhaoh'));

            if ($request->get('alah'))
                $mudancasCelas = $mudancasCelas->where('movimentacao.ala_destino', $request->get('alah'));

            if ($request->get('celah'))
                $mudancasCelas = $mudancasCelas->where('movimentacao.cela_destino', $request->get('celah'));

            if ($request->get('unidade_prisionalv'))
                $mudancasCelas = $mudancasCelas->where('movimentacao_interno.up_anterior', $request->get('unidade_prisionalv'));

            if ($request->get('pavilhaov'))
                $mudancasCelas = $mudancasCelas->where('movimentacao_interno.pv_anterior', $request->get('pavilhaov'));

            if ($request->get('alav'))
                $mudancasCelas = $mudancasCelas->where('movimentacao_interno.ala_anterior', $request->get('alav'));

            if ($request->get('celav'))
                $mudancasCelas = $mudancasCelas->where('movimentacao_interno.cela_anterior', $request->get('celav'));

            if ($request->get('datainicial'))
                $mudancasCelas = $mudancasCelas->where('movimentacao.data', '>=', Tdata::TdataMysql($request->get('datainicial')));

            if ($request->get('datafinal'))
                $mudancasCelas = $mudancasCelas->where('movimentacao.data', '<=', Tdata::TdataMysql($request->get('datafinal')));

            $mudancasCelas = $mudancasCelas->orderBy('movimentacao.data', 'DESC')->paginate(20)->appends($request->input());


        }

        return view('movimentacoes.mudancacela.show', compact('usuarioLogadoPVs','usuarioLogadoUP','unidades','mudancasCelas','url'));
    }

    public function storeMudarCela(Request $request)
    {
//        if(Auth::user()->tipo < 600)
//        {
//            return redirect()->back()->withInput()->with('alert-danger', 'Você não tem permissão para realizar essa operação!');
//        }

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dataInicioPlantao =  OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor);
        $horarioInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
        $horarioTerminoPlantao = OcorrenciaController::getHorarioFechamento59Seg($lotacaoServidor);
        

        /**
         * validacoes
         */

        $dataValidar = $request->get('data');
        $horaValidar = $request->get('hora');

         if(Tdata::TdataMysql($dataValidar)." ".$horaValidar < $dataInicioPlantao." ".$horarioInicioPlantao || Tdata::TdataMysql($dataValidar)." ".$horaValidar > $dataTerminoPlantao." ".$horarioTerminoPlantao)
         {
             return redirect()->back()->withInput()->with('alert-danger', 'Você não pode inserir ocorrências em horários ou datas fora do plantão em aberto!');
         }
        
        
        if($dataValidar == null ||$horaValidar == null)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Preencha os campos obrigatórios!');
        }


        $internosId = $request->get('internos');

        $created_AtMudCel = date("Y-m-d H:i:s");
        $insertMudancaCela = DB::table('movimentacao')->insert([
                'tipo' => 'mudancacela',
                'motivo' => $request->get('motivo'),
                'unidade_destino' => UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf)),
                'pavilhao_destino' => $request->get('pavilhaor'),
                'ala_destino' => $request->get('alar'),
                'cela_destino' => $request->get('celar'),
                'data' => Tdata::TdataMysql($dataValidar)." ".$horaValidar.":00",
                'created_at' => $created_AtMudCel
            ]
        );
        $idMudancaCela = DB::getPdo()->lastInsertId();

        if($insertMudancaCela) {

            $internosLogMudCel = "";
            $idUpMC = "";
            foreach ($internosId as $id) {
                DB::table('movimentacao_interno')->insert([
                        'status' => 'Finalizada',
                        'fk_movimentacao' => $idMudancaCela,
                        'fk_interno' => $id,
                        'up_anterior' => UnidadesController::getIdUP($id),
                        'pv_anterior' => UnidadesController::getIdPavilhao($id),
                        'ala_anterior' => UnidadesController::getIdAla($id),
                        'cela_anterior' => UnidadesController::getIdCela($id)
                    ]
                );
                $internosLogMudCel = $internosLogMudCel."Prontuário: ".$id." - ".InternoController::getInternoNome($id).", ";
                $idUpMC = UnidadesController::getIdUP($id);
            }
            $textoLog = "Mudança de Cela realizada. Registrada em ".Tdata::TtimestampBR($created_AtMudCel).". Origem: ".UnidadesController::getNomeUp($idUpMC).", Destino: ".UnidadesController::getNomeUp(UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf))).", Internos: ";
            $textoLog = $textoLog.substr($internosLogMudCel,0,-2).".";
            LogController::log($idMudancaCela,"movimentacao","add", Auth::user()->cpf, $textoLog);

            // anexos
            if ($request->file('documentos')[0] != null) {

                $qtdArquivo = count($request->file('documentos'));

                for ($x = 0; $x < $qtdArquivo; $x++) {

                    $destinationPath = Constantes::ARQUIVOS_MOVIMENTACAO . "/mudancacela/" . $idMudancaCela;

                    $name = "movimentacao" . $idMudancaCela . "_" . time() . "_" . $request->file('documentos')[$x]->getClientOriginalName();

                    $request->file('documentos')[$x]->storeAs($destinationPath, $name);

                    $insertArquivo = DB::table('movimentacao_arquivos')->insert(
                        [
                            'fk_movimentacao' => $idMudancaCela,
                            'nome_arquivo' => $name
                        ]
                    );

                }
            }

            $errorST = 0;
            foreach ($internosId as $id) {
                DB::table('interno')->where('id', $id)->update(
                    [
                        'pavilhao' => $request->get('pavilhaor'),
                        'ala' => $request->get('alar'),
                        'cela' => $request->get('celar'),
                        'updated_at' => Tdata::TtimeStampHojeMysql(),
                    ]
                );

                // ------------------------------------ registra a sancao ou triagem -------------------------------------------------
                $tipoMudanca = $request->get('tipoMudanca');
                $dataHj = Tdata::TdataMysql($request->get('data'))." ".$request->get('hora').":00";
                $dataFim = Tdata::addDias($dataHj, $request->get('diasAdd'));
                if($tipoMudanca == "sancao" || $tipoMudanca == "triagem")
                {
                    $comparaDatas = Tdata::compDatas($dataFim, Tdata::TdataHojeMysql());
                    
                    //dd($dataHj, $dataFim, $comparaDatas);
                                       
                    if($comparaDatas == "maior")
                    {
                        $inserSancaoTriagem = DB::table('sancao_triagem')->insert([
                        'tipo' => $tipoMudanca,
                        'unidade' => UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf)),
                        'fk_movimentacao' => $idMudancaCela,
                        'fk_interno' => $id,
                        'data_fim' => $dataFim,
                        'status' => 'A',
                        'motivo' => $request->get('motivo'),
                        'created_at' => Tdata::TtimeStampHojeMysql(),
                        ]);
                        

                    }else{
                        $errorST = 1;
                    }
                    
                }

                // ------------------------------------ !registra a sancao ou triagem ------------------------------------------------

            }

            

            if($errorST == 0)
            {
                return redirect()->route('movimentacoes.mudancacelaShow')->with('alert-success', 'Mudança de cela realizada com sucesso.');

            }else{
                return redirect()->route('movimentacoes.mudancacelaShow')->with('alert-warning', 'Mudança de cela realizada com sucesso, porém um ou mais internos não foram registrados na sanção disciplinar ou triagem');

            }
            
        }
        else{
            return redirect()->route('movimentacoes.mudancacelaShow')->with('alert-danger', 'Ocorreu um erro e a mudança de cela não foi realizada, entre em contato com a '.config('app.ti').'.');
        }
    }

    public function progressaoregressaoShow(Request $request)
    {
        $unidades = DB::table('unidade')->where('status', 1)->orderBy('sigla', 'ASC')->get();
        $unidades = json_decode($unidades,true);

        $progressaoregressaoHist = null;

        // ----- HISTÓRICO PROGRESSAOREGRESSAO INÍCIO -----
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if(strpos($url,"?")){

            $progressaoregressaoHist = DB::table('movimentacao_interno')
            ->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao');

            if ($request->get('tipoB'))
                $progressaoregressaoHist->where('movimentacao.tipo', $request->get('tipoB'));

            if ($request->get('unidade_prisionalv'))
                $progressaoregressaoHist->where('movimentacao_interno.up_anterior', $request->get('unidade_prisionalv'));

            if ($request->get('pavilhaov'))
                $progressaoregressaoHist->where('movimentacao_interno.pv_anterior', $request->get('pavilhaov'));

            if ($request->get('alav'))
                $progressaoregressaoHist->where('movimentacao_interno.ala_anterior', $request->get('alav'));

            if ($request->get('celav'))
                $progressaoregressaoHist->where('movimentacao_interno.cela_anterior', $request->get('celav'));

            if ($request->get('datainicial'))
                $progressaoregressaoHist->where('movimentacao.data', '>=', Tdata::TdataMysql($request->get('datainicial')));

            if ($request->get('datafinal'))
                $progressaoregressaoHist->where('movimentacao.data', '<=', Tdata::TdataMysql($request->get('datafinal')));

            $progressaoregressaoHist = $progressaoregressaoHist->orderBy('movimentacao.data', 'DESC')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->paginate(20)->appends($request->input());
            
        }
        // ----- HISTORICO PROGRESSAOREGRESSAO FIM -----

        $usuarioLogadoUP = DB::table('unidade')->where('sigla', FacadeController::getLotacaoServidor(Auth::user()->cpf))->get();
        $usuarioLogadoUP = json_decode($usuarioLogadoUP, true)[0];

        
        return view('movimentacoes.progressaoregressao.show',compact('unidades','progressaoregressaoHist','url','usuarioLogadoUP'));
    }

    public function storeProgressaoRegressao(Request $request)
    {
//        if(Auth::user()->tipo < 600)
//        {
//            return redirect()->back()->withInput()->with('alert-danger', 'Você não tem permissão para realizar essa operação!');
//        }

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

//        $getFechamento = DB::table('ocorrencia_fechamento_livro')->where('unidade', $lotacaoServidor)->orderBy('data', 'DESC')->first();


        /**
         * validacoes
         */

        $dataValidar = $request->get('data');
        $horaValidar = $request->get('hora');

        if(Tdata::TdataMysql($dataValidar)." ".$horaValidar > Tdata::TtimeStampHojeMysql())
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Você não pode registrar em datas futuras.');
        }
        if($dataValidar == null ||$horaValidar == null)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Preencha os campos obrigatórios!');
        }

        $internosId = $request->get('internos');

        foreach ($internosId as $id) {
            $dadosInterno = InternoController::getDadosInterno($id);
            if(isset($dadosInterno[0])){
                if($dadosInterno[0]->sentenciado == 'N'){
                    return redirect()->back()->withInput()->with('alert-danger', 'Existem internos que não são sentenciados! Para isto use o menu Sentenciado ou Provisório em Movimentações.');
                }
            }
        }


        $created_AtProgRegr = date("Y-m-d H:i:s");
        $insertProgressaoRegressao = DB::table('movimentacao')->insert([
                'tipo' => $request->get('tipo'),
                'unidade_destino' => $request->get('unidade_prisionalAlocacao'),
                'pavilhao_destino' => $request->get('pavilhaoAlocacao'),
                'ala_destino' => $request->get('alaAlocacao'),
                'cela_destino' => $request->get('celaAlocacao'),
                'motivo' => $request->get('motivo'),
                'data' => Tdata::TdataMysql($request->get('data'))." ".$request->get('hora').":00",
                'created_at' => $created_AtProgRegr
            ]
        );
        if($insertProgressaoRegressao){
            $idProgressaoRegressao = DB::getPdo()->lastInsertId();

            if($request->get('fimsancao') == "Sim"){
                DB::table('movimentacao')->where('id',$idProgressaoRegressao)->update([
                    'motivo' => "Registro, por meio deste, o fim da sanção disciplinar com cumprimento de pena em regime mais gravoso.".$request->get('motivo'),
                ]);
            }

            $internosLogProgRegr = "";
            foreach ($internosId as $id) {
                DB::table('movimentacao_interno')->insert([
                        'status' => 'Finalizada',
                        'fk_movimentacao' => $idProgressaoRegressao,
                        'fk_interno' => $id,
                        'up_anterior' => UnidadesController::getIdUP($id),
                        'pv_anterior' => UnidadesController::getIdPavilhao($id),
                        'ala_anterior' => UnidadesController::getIdAla($id),
                        'cela_anterior' => UnidadesController::getIdCela($id)
                    ]
                );
                $internosLogProgRegr = $internosLogProgRegr."Prontuário: ".$id." - ".InternoController::getInternoNome($id).", ";
                $idUpProgRegr = UnidadesController::getIdUP($id);
            }
            $textoLog = "";
            if($request->get('tipo') == "progressaosemiaberto"){
                $textoLog = "Progressão para o semiaberto realizada. Registrada em ".Tdata::TtimestampBR($created_AtProgRegr).". Origem: ".UnidadesController::getNomeUp($idUpProgRegr).". Destino :".UnidadesController::getNomeUp($request->get('unidade_prisionalAlocacao'))." - ".UnidadesController::getNomePav($request->get('pavilhaoAlocacao'))." - ".UnidadesController::getNomeAla($request->get('alaAlocacao'))." - ".UnidadesController::getNomeCela($request->get('celaAlocacao'))." Internos: ";
            }
            else if ($request->get('tipo') == "progressaoaberto"){
                $textoLog = "Progressão para o aberto realizada. Registrada em ".Tdata::TtimestampBR($created_AtProgRegr).". Origem: ".UnidadesController::getNomeUp(UnidadesController::getIdUP($id)).". Destino :".UnidadesController::getNomeUp($request->get('unidade_prisionalAlocacao'))." - ".UnidadesController::getNomePav($request->get('pavilhaoAlocacao'))." - ".UnidadesController::getNomeAla($request->get('alaAlocacao'))." - ".UnidadesController::getNomeCela($request->get('celaAlocacao'))." Internos: ";
            }
            else if ($request->get('tipo') == "regressaosemiaberto"){
                $textoLog = "Regressão para o semiaberto realizada. Registrada em ".Tdata::TtimestampBR($created_AtProgRegr).". Origem: ".UnidadesController::getNomeUp(UnidadesController::getIdUP($id)).". Destino :".UnidadesController::getNomeUp($request->get('unidade_prisionalAlocacao'))." - ".UnidadesController::getNomePav($request->get('pavilhaoAlocacao'))." - ".UnidadesController::getNomeAla($request->get('alaAlocacao'))." - ".UnidadesController::getNomeCela($request->get('celaAlocacao'))." Internos: ";
            }
            else if ($request->get('tipo') == "regressaofechado"){
                $textoLog = "Regressão para o fechado realizada. Registrada em ".Tdata::TtimestampBR($created_AtProgRegr).". Origem: ".UnidadesController::getNomeUp(UnidadesController::getIdUP($id)).". Destino :".UnidadesController::getNomeUp($request->get('unidade_prisionalAlocacao'))." - ".UnidadesController::getNomePav($request->get('pavilhaoAlocacao'))." - ".UnidadesController::getNomeAla($request->get('alaAlocacao'))." - ".UnidadesController::getNomeCela($request->get('celaAlocacao'))." Internos: ";
            }
            $textoLog = $textoLog.substr($internosLogProgRegr,0,-2).".";
            LogController::log($idProgressaoRegressao,"movimentacao","add", Auth::user()->cpf, $textoLog);

            // anexos
            if ($request->file('documentos')[0] != null) {

                $qtdArquivo = count($request->file('documentos'));

                for ($x = 0; $x < $qtdArquivo; $x++) {

                    $destinationPath = Constantes::ARQUIVOS_MOVIMENTACAO . "/progressaoregressao/" . $idProgressaoRegressao;

                    $name = "movimentacao" . $idProgressaoRegressao . "_" . time() . "_" . $request->file('documentos')[$x]->getClientOriginalName();

                    $request->file('documentos')[$x]->storeAs($destinationPath, $name);

                    $insertArquivo = DB::table('movimentacao_arquivos')->insert(
                        [
                            'fk_movimentacao' => $idProgressaoRegressao,
                            'nome_arquivo' => $name,
                        ]
                    );

                }
            }

            // REGISTRA SAÍDA JUDICIAL SE O PRESO FOR ASSINAR EXTERNAMENTE
                $created_SaidaJudicial = date("Y-m-d H:i:s");
                if($request->get('assinaunidade') == "Não") {
                    DB::table('movimentacao')->insert([
                            'tipo' => 'saidajudicial',
                            'motivo' => 'Saída judicial registrada automaticamente através do registro da progressão de regime em que o interno não assinará na unidade em que ele estava lotado.',
                            'data' => Tdata::TdataMysql($request->get('data')) . " " . $request->get('hora') . ":59",
                            'created_at' => $created_SaidaJudicial
                        ]
                    );
                    $idSaidaJudicial = DB::getPdo()->lastInsertId();
                }

            foreach ($internosId as $id) {
                $regime = "";
                if($request->get('tipo') == "progressaosemiaberto"){
                    $regime = "SEMIABERTO";
                } else if($request->get('tipo') == "progressaoaberto"){
                    $regime = "ABERTO";
                } else if($request->get('tipo') == "regressaofechado"){
                    $regime = "FECHADO";
                    // -------------------- cadastro na sancao --------------------------- //
                    if($request->get('dias') != 0){
                        $dataHj = Tdata::TdataHojeMysql();
                        $dataFimTriagem = Tdata::addDias($dataHj, $request->get('dias'));

                        DB::table('sancao_triagem')->insert([
                            'tipo' => 'sancao',
                            'unidade' => UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf)),
                            'fk_movimentacao' => $idProgressaoRegressao,
                            'fk_interno' => $id,
                            'data_fim' => $dataFimTriagem,
                            'status' => 'A',
                            'motivo' => 'Sanção disciplinar com cumprimento em regime mais gravoso.',
                            'regressao_temporaria' => 'S',
                            'created_at' => Tdata::TtimeStampHojeMysql(),
                        ]);
                    }
                    // -------------------- !cadastro na sancao --------------------------- //
                } else if($request->get('tipo') == "regressaosemiaberto"){
                    $regime = "SEMIABERTO";
                    // -------------------- cadastro na sancao --------------------------- //
                    if($request->get('dias') != 0){
                            $dataHj = Tdata::TdataHojeMysql();
                            $dataFimTriagem = Tdata::addDias($dataHj, $request->get('dias'));

                            DB::table('sancao_triagem')->insert([
                                'tipo' => 'sancao',
                                'unidade' => UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf)),
                                'fk_movimentacao' => $idProgressaoRegressao,
                                'fk_interno' => $id,
                                'data_fim' => $dataFimTriagem,
                                'status' => 'A',
                                'motivo' => 'Sanção disciplinar com cumprimento em regime mais gravoso.',
                                'regressao_temporaria' => 'S',
                                'created_at' => Tdata::TtimeStampHojeMysql(),
                            ]);
                    }
                    // -------------------- !cadastro na sancao --------------------------- //
                }
                $status = "";
                if($request->get('assinaunidade') == "Sim"){
                    $status = "A";
                } else if($request->get('assinaunidade') == "Não") {
                    $status = "I";

                    // REGISTRA INTERNO NA SAIDAJUDICIAL
                        DB::table('movimentacao_interno')->insert([
                                'status' => 'Finalizada',
                                'fk_movimentacao' => $idSaidaJudicial,
                                'fk_interno' => $id,
                                'up_anterior' => UnidadesController::getIdUP($id),
                                'pv_anterior' => UnidadesController::getIdPavilhao($id),
                                'ala_anterior' => UnidadesController::getIdAla($id),
                                'cela_anterior' => UnidadesController::getIdCela($id)
                            ]
                        );
                }


                DB::table('interno')->where('id', $id)->update(
                    [
                        'saidasexternas' => $request->get('saidasexternas'),
                        'status' => $status,
                        'updated_at' => Tdata::TtimeStampHojeMysql(),
                    ]
                );

                if($request->get('assinaunidade') == "Sim"){
                    DB::table('interno')->where('id', $id)->update(
                        [
                            'pavilhao' => $request->get('pavilhaoAlocacao'),
                            'ala' => $request->get('alaAlocacao'),
                            'cela' => $request->get('celaAlocacao'),
                            'updated_at' => Tdata::TtimeStampHojeMysql(),
                        ]
                    );
                }

                if($request->get('temporaria') == "Não"){
                    DB::table('interno')->where('id', $id)->update(
                        [
                            'regime' => $regime,
                            'updated_at' => Tdata::TtimeStampHojeMysql(),
                        ]
                    );
                }

                if($status == "I"){
                    DB::table('interno')->where('id', $id)->update(
                        [
                            'condicao_interno' => 646,
                            'updated_at' => Tdata::TtimeStampHojeMysql(),
                        ]
                    );
                }
                else if($status == "A"){
                    DB::table('interno')->where('id', $id)->update(
                        [
                            'condicao_interno' => 644,
                            'updated_at' => Tdata::TtimeStampHojeMysql(),
                        ]
                    );
                }

            }
            return redirect()->route('movimentacoes.progressaoregressaoShow')->with('alert-success', 'Operação realizada com sucesso.');
        }
        else{
            return redirect()->route('movimentacoes.progressaoregressaoShow')->with('alert-danger', 'Ocorreu um erro e a operação não foi realizada, entre em contato com a '.config('app.ti').'.');
        }
    }

    public function storeMudancaSentenca(Request $request){
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

//        $getFechamento = DB::table('ocorrencia_fechamento_livro')->where('unidade', $lotacaoServidor)->orderBy('data', 'DESC')->first();


        /**
         * validacoes
         */


        $dataValidar = $request->get('data');
        $horaValidar = $request->get('hora');

        if(Tdata::TdataMysql($dataValidar)." ".$horaValidar > Tdata::TtimeStampHojeMysql())
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Você não pode registrar em datas futuras.');
        }
        if($dataValidar == null ||$horaValidar == null)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Preencha os campos obrigatórios!');
        }
        $internoId = $request->get('internos');
        $internoSelecionado = InternoController::getDadosInterno($internoId);
        $sentencaInterno = '';

        if(isset($internoSelecionado[0])){
            $sentencaInterno = $internoSelecionado[0]->sentenciado;
        } else {
            $sentencaInterno = 'N';
        }
        if ($request->get('tipo') == "mudancaprovisorio" && $sentencaInterno == 'N'){
            return redirect()->back()->withInput()->with('alert-danger', 'Não é permitido alterar a situação para provisório, pois o interno já consta como provisório.');
        }
        if ($request->get('tipo') != "mudancaprovisorio" && $sentencaInterno == 'S'){
            return redirect()->back()->withInput()->with('alert-danger', 'Para alterações de regime utilize o menu Movimentações > Progressão e Regressão.');
        }


        $created_AtMudancaSentenca = date("Y-m-d H:i:s");
        $insertMudancaSentenca = DB::table('movimentacao')->insert([
                'tipo' => $request->get('tipo'),
                'motivo' => $request->get('motivo'),
                'data' => Tdata::TdataMysql($request->get('data'))." ".$request->get('hora').":00",
                'created_at' => $created_AtMudancaSentenca
            ]
        );




        if($insertMudancaSentenca){
            $idMudancaSentenca = DB::getPdo()->lastInsertId();


            DB::table('movimentacao_interno')->insert([
                    'status' => 'Finalizada',
                    'fk_movimentacao' => $idMudancaSentenca,
                    'fk_interno' => $internoId[0],
                    'up_anterior' => null,
                    'pv_anterior' => null,
                    'ala_anterior' => null,
                    'cela_anterior' => null
                ]
            );
            $internosLogProgRegr = "Prontuário: " . $internoSelecionado[0]->id . " - " . $internoSelecionado[0]->nome . ", ";
            $textoLog = "";


            if($request->get('tipo') == "mudancaaberto"){
                $textoLog = "Mudança de provisório para o regime aberto realizada. Registrada em ". Tdata::TtimestampBR($created_AtMudancaSentenca) . ".  Interno: " . $internoSelecionado[0]->id;
            }
            else if ($request->get('tipo') == "mudancasemiaberto"){
                $textoLog = "Mudança de provisório para o regime Semiaberto realizada. Registrada em ". Tdata::TtimestampBR($created_AtMudancaSentenca). " Interno: " . $internoSelecionado[0]->id;
            }
            else if ($request->get('tipo') == "mudancafechado"){
                $textoLog = "Mudança de provisório para o regime fechado realizada. Registrada em ".Tdata::TtimestampBR($created_AtMudancaSentenca)." Interno: " . $internoSelecionado[0]->id;
            }
            else if ($request->get('tipo') == "mudancaprovisorio"){
                $textoLog = "Mudança de sentença para Provisório. Registrada em ".Tdata::TtimestampBR($created_AtMudancaSentenca)." Interno: " . $internoSelecionado[0]->id;
            }
            $textoLog = $textoLog.substr($internosLogProgRegr,0,-2).".";
            LogController::log($idMudancaSentenca,"movimentacao","add", Auth::user()->cpf, $textoLog);

            // anexos
            if ($request->file('documentos')[0] != null) {

                $qtdArquivo = count($request->file('documentos'));

                for ($x = 0; $x < $qtdArquivo; $x++) {

                    $destinationPath = Constantes::ARQUIVOS_MOVIMENTACAO . "/mudancasentenca/" . $idMudancaSentenca;

                    $name = "movimentacao" . $idMudancaSentenca . "_" . time() . "_" . $request->file('documentos')[$x]->getClientOriginalName();

                    $request->file('documentos')[$x]->storeAs($destinationPath, $name);

                    $insertArquivo = DB::table('movimentacao_arquivos')->insert(
                        [
                            'fk_movimentacao' => $idMudancaSentenca,
                            'nome_arquivo' => $name,
                        ]
                    );

                }
            }


            $regime = "";
            $sentenciado = "N";
            if($request->get('tipo') == "mudancaaberto"){
                $regime = "ABERTO";
                $sentenciado = 'S';
            } else if($request->get('tipo') == "mudancasemiaberto"){
                $regime = "SEMIABERTO";
                $sentenciado = 'S';
            } else if($request->get('tipo') == "mudancafechado"){
                $regime = "FECHADO";
                $sentenciado = 'S';
            } else if($request->get('tipo') == "mudancaprovisorio"){
                $regime = null;
                $sentenciado = 'N';
            }


            DB::table('interno')->where('id', $internoId)->update(
                [
                    'regime' => $regime,
                    'sentenciado' => $sentenciado,
                    'updated_at' => Tdata::TtimeStampHojeMysql(),
                ]
            );



            return redirect()->route('movimentacoes.mudancasentencaStoreShow')->with('alert-success', 'Operação realizada com sucesso.');
        }
        else{
            return redirect()->route('movimentacoes.mudancasentencaStoreShow')->with('alert-danger', 'Ocorreu um erro e a operação não foi realizada, entre em contato com a '.config('app.ti').'.');
        }
    }

    public function mudancaSentencaShow(Request $request)
    {

        $mudancasentencaHist = null;

        // ----- HISTÓRICO PROGRESSAOREGRESSAO INÍCIO -----
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if(strpos($url,"?")){

            $mudancasentencaHist = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao');

            if ($request->get('tipoB'))
                $mudancasentencaHist->where('movimentacao.tipo', $request->get('tipoB'));

            if ($request->get('datainicial'))
                $mudancasentencaHist->where('movimentacao.data', '>=', Tdata::TdataMysql($request->get('datainicial')));

            if ($request->get('datafinal'))
                $mudancasentencaHist->where('movimentacao.data', '<=', Tdata::TdataMysql($request->get('datafinal')));

            $mudancasentencaHist = $mudancasentencaHist->orderBy('movimentacao.data', 'DESC')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->paginate(20)->appends($request->input());

            // $mudancasentencaHist = $mudancasentencaHist->unique('idmov');

            // $mudancasentencaHist = json_decode($mudancasentencaHist, true);

            // foreach ($mudancasentencaHist as $key => $prHist) {
            //     $mudancasentencaHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $prHist['idmov'])->get();
            //     $mudancasentencaHist[$key]['arquivos'] = json_decode($mudancasentencaHist[$key]['arquivos'], true);
            //     if(count(LogController::getDadosLog($prHist['idmov'], 'movimentacao', 'add')) != 0){
            //         $mudancasentencaHist[$key]['log'] = LogController::getDadosLog($prHist['idmov'], 'movimentacao', 'add');
            //         $mudancasentencaHist[$key]['log'] = FacadeController::getNomeServidorCpf($mudancasentencaHist[$key]['log'][0]->cpf_usuario)." em ".Tdata::TtimestampBR($mudancasentencaHist[$key]['log'][0]->created_at);
            //     }
            //     else{
            //         $mudancasentencaHist[$key]['log'] = "Sem log registrado.";
            //     }

            //     if($prHist['data'] == null || $prHist['data'] == ""){
            //         $mudancasentencaHist[$key]['dia'] = "";
            //         $mudancasentencaHist[$key]['mes'] = "";
            //         $mudancasentencaHist[$key]['ano'] = "";
            //     }
            //     else{
            //         $mudancasentencaHist[$key]['dia'] = Tdata::getDiaData(Tdata::getDataTimestamp($prHist['data']));
            //         $mudancasentencaHist[$key]['mes'] = Tdata::getMesExtAbrev(Tdata::getMesData(Tdata::getDataTimestamp($prHist['data'])));
            //         $mudancasentencaHist[$key]['ano'] = Tdata::getAnoData(Tdata::getDataTimestamp($prHist['data']));
            //     }
            //     $mudancasentencaHist[$key]['hora'] = Tdata::getTempoTimestamp($prHist['data']);
            //     $mudancasentencaHist[$key]['internos'] = DB::table('movimentacao_interno')
            //         ->where('fk_movimentacao',$prHist['fk_movimentacao'])
            //         ->get();
            //     $mudancasentencaHist[$key]['internos'] = json_decode($mudancasentencaHist[$key]['internos'],true);
            //     foreach($mudancasentencaHist[$key]['internos'] as $keyy => $interno){
            //         $mudancasentencaHist[$key]['internos'][$keyy]['id'] = $interno['fk_interno'];
            //         $mudancasentencaHist[$key]['internos'][$keyy]['nome'] = InternoController::getInternoNome($interno['fk_interno']);
            //     }
            // }

            // $mudancasentencaHist = collect($mudancasentencaHist)->paginate(20)->appends($request->input());
        }
        // ----- HISTORICO PROGRESSAOREGRESSAO FIM -----

        $usuarioLogadoUP = DB::table('unidade')->where('sigla', FacadeController::getLotacaoServidor(Auth::user()->cpf))->get();
        $usuarioLogadoUP = json_decode($usuarioLogadoUP, true)[0];

        return view('movimentacoes.mudancasentenca.show',compact('mudancasentencaHist','url','usuarioLogadoUP'));
    }

    public function alvaraShow(Request $request)
    {
        $unidades = DB::table('unidade')->where('status', 1)->orderBy('sigla', 'ASC')->get();
        $unidades = json_decode($unidades,true);

        $alvarasHist = null;

        // ----- HISTÓRICO ALVARÁ INÍCIO -----
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if(strpos($url,"?")){


            $alvarasHist = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao', 'movimentacao_interno.fk_movimentacao','=','movimentacao.id')
                ->where('movimentacao.tipo', 'alvara');

            if ($request->get('unidade_prisionalv'))
                $alvarasHist = $alvarasHist->where('movimentacao_interno.up_anterior', $request->get('unidade_prisionalv'));

            if ($request->get('pavilhaov'))
                $alvarasHist = $alvarasHist->where('movimentacao_interno.pv_anterior', $request->get('pavilhaov'));

            if ($request->get('alav'))
                $alvarasHist = $alvarasHist->where('movimentacao_interno.ala_anterior', $request->get('alav'));

            if ($request->get('celav'))
                $alvarasHist = $alvarasHist->where('movimentacao_interno.cela_anterior', $request->get('celav'));

            if ($request->get('datainicial'))
                $alvarasHist = $alvarasHist->where('movimentacao.data', '>=', Tdata::TdataMysql($request->get('datainicial')));

            if ($request->get('datafinal'))
                $alvarasHist = $alvarasHist->where('movimentacao.data', '<=', Tdata::TdataMysql($request->get('datafinal')));

            $alvarasHist = $alvarasHist->orderBy('movimentacao.data', 'DESC')->paginate(20)->appends($request->input());
            
        }
        // ----- HISTORICO ALVARÁ FIM -----


        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        return view('movimentacoes.alvara.show',compact('unidades','alvarasHist','url','lotacaoServidor'));
    }

    public function storeAlvara(Request $request)
    {

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));


        $dataInicioPlantao =  OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor);
        $horarioInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
        $horarioTerminoPlantao = OcorrenciaController::getHorarioFechamento59Seg($lotacaoServidor);


        /**
         * validacoes
         */

        $dataValidar = $request->get('data');
        $horaValidar = $request->get('hora');

         if(Tdata::TdataMysql($dataValidar)." ".$horaValidar < $dataInicioPlantao." ".$horarioInicioPlantao || Tdata::TdataMysql($dataValidar)." ".$horaValidar > $dataTerminoPlantao." ".$horarioTerminoPlantao)
         {
             return redirect()->back()->withInput()->with('alert-danger', 'Você não pode inserir ocorrências em horários ou datas fora do plantão em aberto!');
         }
         
         
        if($dataValidar == null ||$horaValidar == null)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Preencha os campos obrigatórios!');
        }

        $internosId = $request->get('internos');

        $created_AtAlvara = date("Y-m-d H:i:s");
        $insertAlvara = DB::table('movimentacao')->insert([
                'tipo' => 'alvara',
                'motivo' => $request->get('motivo'),
                'data' => Tdata::TdataMysql($request->get('data'))." ".$request->get('hora').":00",
                'created_at' => $created_AtAlvara
            ]
        );
        if($insertAlvara){
            $idAlvara = DB::getPdo()->lastInsertId();

            $internosAlvara = "";
            $idUpAlv = "";
            foreach ($internosId as $id) {
                // -------------------- update na sancao --------------------------- //
                DB::table('sancao_triagem')->where('fk_interno',$id)->update([
                    'status' => 'I',
                    'update_at' => Tdata::TtimeStampHojeMysql(),
                ]);
                // -------------------- !update na sancao --------------------------- //
                DB::table('movimentacao_interno')->insert([
                        'status' => 'Finalizada',
                        'fk_movimentacao' => $idAlvara,
                        'fk_interno' => $id,
                        'up_anterior' => UnidadesController::getIdUP($id),
                        'pv_anterior' => UnidadesController::getIdPavilhao($id),
                        'ala_anterior' => UnidadesController::getIdAla($id),
                        'cela_anterior' => UnidadesController::getIdCela($id)
                    ]
                );
                $internosAlvara = $internosAlvara." Prontuário: ".$id." - ".InternoController::getInternoNome($id).", ";
                $idUpAlv = UnidadesController::getIdUP($id);

                DB::table('interno')->where('id', $id)->update(
                    [
                        'condicao_interno' => 638,
                        'status' => 'I',
                        'updated_at' => Tdata::TtimeStampHojeMysql(),
                    ]
                );
                if($request->get('tornozeleira') == "S"){
                    DB::table('interno')->where('id', $id)->update(
                        [
                            'status' => 'A',
                            'updated_at' => Tdata::TtimeStampHojeMysql(),
                        ]
                    );
                }

            }
            $textoLog = "Alvará registrado em ".Tdata::TtimestampBR($created_AtAlvara).". Origem: ".UnidadesController::getNomeUp($idUpAlv).". Internos: ";
            $textoLog = $textoLog.substr($internosAlvara,0,-2).".";
            LogController::log($idAlvara,"movimentacao","add", Auth::user()->cpf, $textoLog);

            // anexos
            if ($request->file('documentos')[0] != null) {

                $qtdArquivo = count($request->file('documentos'));

                for ($x = 0; $x < $qtdArquivo; $x++) {

                    $destinationPath = Constantes::ARQUIVOS_MOVIMENTACAO . "/alvara/" . $idAlvara;

                    $name = "movimentacao" . $idAlvara . "_" . time() . "_" . $request->file('documentos')[$x]->getClientOriginalName();

                    $request->file('documentos')[$x]->storeAs($destinationPath, $name);

                    $insertArquivo = DB::table('movimentacao_arquivos')->insert(
                        [
                            'fk_movimentacao' => $idAlvara,
                            'nome_arquivo' => $name,
                        ]
                    );

                }
            }
            return redirect()->route('movimentacoes.alvaraShow')->with('alert-success', 'Operação realizada com sucesso.');
        }
        else{
            return redirect()->route('movimentacoes.alvaraShow')->with('alert-danger', 'Ocorreu um erro e a operação não foi realizada, entre em contato com a '.config('app.ti').'.');
        }
    }

    public function livramentocondicionalShow(Request $request)
    {
        $unidades = DB::table('unidade')->where('status', 1)->orderBy('sigla', 'ASC')->get();
        $unidades = json_decode($unidades,true);

        $livramentocondicionalHist = null;
        
        // ----- HISTÓRICO LIVRAMENTOCONDICIONAL INÍCIO -----
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if(strpos($url,"?")){


            $livramentocondicionalHist = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao', function ($query) {
                    $query->on('movimentacao_interno.fk_movimentacao','=','movimentacao.id')->where('movimentacao.tipo', 'livramentocondicional');
                })->where('movimentacao.tipo', 'livramentocondicional');

            if ($request->get('unidade_prisionalh'))
                $livramentocondicionalHist = $livramentocondicionalHist->where('movimentacao.unidade_destino', $request->get('unidade_prisionalh'));

            if ($request->get('pavilhaoh'))
                $livramentocondicionalHist = $livramentocondicionalHist->where('movimentacao.pavilhao_destino', $request->get('pavilhaoh'));

            if ($request->get('alah'))
                $livramentocondicionalHist = $livramentocondicionalHist->where('movimentacao.ala_destino', $request->get('alah'));

            if ($request->get('celah'))
                $livramentocondicionalHist = $livramentocondicionalHist->where('movimentacao.cela_destino', $request->get('celah'));

            if ($request->get('unidade_prisionalv'))
                $livramentocondicionalHist = $livramentocondicionalHist->where('movimentacao_interno.up_anterior', $request->get('unidade_prisionalv'));

            if ($request->get('pavilhaov'))
                $livramentocondicionalHist = $livramentocondicionalHist->where('movimentacao_interno.pv_anterior', $request->get('pavilhaov'));

            if ($request->get('alav'))
                $livramentocondicionalHist = $livramentocondicionalHist->where('movimentacao_interno.ala_anterior', $request->get('alav'));

            if ($request->get('celav'))
                $livramentocondicionalHist = $livramentocondicionalHist->where('movimentacao_interno.cela_anterior', $request->get('celav'));

            if ($request->get('datainicial'))
                $livramentocondicionalHist = $livramentocondicionalHist->where('movimentacao.data', '>=', Tdata::TdataMysql($request->get('datainicial')));

            if ($request->get('datafinal'))
                $livramentocondicionalHist = $livramentocondicionalHist->where('movimentacao.data', '<=', Tdata::TdataMysql($request->get('datafinal')));

            $livramentocondicionalHist = $livramentocondicionalHist->orderBy('movimentacao.data', 'DESC')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->paginate(20)->appends($request->input());

        }
        // ----- HISTORICO LIVRAMENTOCONDICIONAL FIM -----

        return view('movimentacoes.livramentocondicional.show',compact('unidades','livramentocondicionalHist','url'));
    }

    public function storeLivramentoCondicional(Request $request)
    {

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dataInicioPlantao =  OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor);
        $horarioInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
        $horarioTerminoPlantao = OcorrenciaController::getHorarioFechamento59Seg($lotacaoServidor);


        /**
         * validacoes
         */

        $dataValidar = $request->get('data');
        $horaValidar = $request->get('hora');

         if(Tdata::TdataMysql($dataValidar)." ".$horaValidar < $dataInicioPlantao." ".$horarioInicioPlantao || Tdata::TdataMysql($dataValidar)." ".$horaValidar > $dataTerminoPlantao." ".$horarioTerminoPlantao)
         {
             return redirect()->back()->withInput()->with('alert-danger', 'Você não pode inserir ocorrências em horários ou datas fora do plantão em aberto!');
         }
         
         
        if($dataValidar == null ||$horaValidar == null)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Preencha os campos obrigatórios!');
        }

        $internosId = $request->get('internos');

        $created_AtLivramentoCond = date("Y-m-d H:i:s");
        $insertLivramentoCondicional = DB::table('movimentacao')->insert([
                'tipo' => 'livramentocondicional',
                'motivo' => $request->get('motivo'),
                'data' => Tdata::TdataMysql($request->get('data'))." ".$request->get('hora').":00",
                'created_at' => $created_AtLivramentoCond
            ]
        );
        if($insertLivramentoCondicional){
            $idLivramentoCondicional = DB::getPdo()->lastInsertId();

            $internosLivramentoCond = "";
            $idUpLivCond = "";
            foreach ($internosId as $id) {
                // -------------------- update na sancao --------------------------- //
                DB::table('sancao_triagem')->where('fk_interno',$id)->where('tipo','sancao')->update([
                    'status' => 'I',
                    'update_at' => Tdata::TtimeStampHojeMysql(),
                ]);
                // -------------------- !update na sancao --------------------------- //
                DB::table('movimentacao_interno')->insert([
                        'status' => 'Finalizada',
                        'fk_movimentacao' => $idLivramentoCondicional,
                        'fk_interno' => $id,
                        'up_anterior' => UnidadesController::getIdUP($id),
                        'pv_anterior' => UnidadesController::getIdPavilhao($id),
                        'ala_anterior' => UnidadesController::getIdAla($id),
                        'cela_anterior' => UnidadesController::getIdCela($id)
                    ]
                );
                $internosLivramentoCond = $internosLivramentoCond."Prontuário: ".$id." - ".InternoController::getInternoNome($id).", ";
                $idUpLivCond = UnidadesController::getIdUP($id);
            }
            $textoLog = "Livramento Condicional registrado em ".Tdata::TtimestampBR($created_AtLivramentoCond).". Origem: ".UnidadesController::getNomeUp($idUpLivCond).". Internos: ";
            $textoLog = $textoLog.substr($internosLivramentoCond,0,-2).".";
            LogController::log($idLivramentoCondicional,"movimentacao","add", Auth::user()->cpf, $textoLog);

            // anexos
            if ($request->file('documentos')[0] != null) {

                $qtdArquivo = count($request->file('documentos'));

                for ($x = 0; $x < $qtdArquivo; $x++) {

                    $destinationPath = Constantes::ARQUIVOS_MOVIMENTACAO . "/livramentocondicional/" . $idLivramentoCondicional;

                    $name = "movimentacao" . $idLivramentoCondicional . "_" . time() . "_" . $request->file('documentos')[$x]->getClientOriginalName();

                    $request->file('documentos')[$x]->storeAs($destinationPath, $name);

                    $insertArquivo = DB::table('movimentacao_arquivos')->insert(
                        [
                            'fk_movimentacao' => $idLivramentoCondicional,
                            'nome_arquivo' => $name,
                        ]
                    );

                }
            }
            foreach ($internosId as $id) {
                DB::table('interno')->where('id', $id)->update(
                    [
                        'sentenciado' => 'S',
                        'condicao_interno' => 251,
                        'status' => 'I',
                        'updated_at' => Tdata::TtimeStampHojeMysql(),
                    ]
                );
            }
            return redirect()->route('movimentacoes.livramentocondicionalShow')->with('alert-success', 'Operação realizada com sucesso.');
        }
        else{
            return redirect()->route('movimentacoes.livramentocondicionalShow')->with('alert-danger', 'Ocorreu um erro e a operação não foi realizada, entre em contato com a '.config('app.ti').'.');
        }
    }

    public function fugaShow(Request $request)
    {
        $unidades = DB::table('unidade')->where('status', 1)->orderBy('sigla', 'ASC')->get();
        $unidades = json_decode($unidades,true);

        $fugasHist = null;

        // ----- HISTÓRICO FUGA INÍCIO -----
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if(strpos($url,"?")){


            $fugasHist = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao', 'movimentacao_interno.fk_movimentacao','=','movimentacao.id')
                ->where('movimentacao.tipo', 'fuga');


            if ($request->get('unidade_prisionalh'))
                $fugasHist = $fugasHist->where('movimentacao.unidade_destino', $request->get('unidade_prisionalh'));

            if ($request->get('pavilhaoh'))
                $fugasHist = $fugasHist->where('movimentacao.pavilhao_destino', $request->get('pavilhaoh'));

            if ($request->get('alah'))
                $fugasHist = $fugasHist->where('movimentacao.ala_destino', $request->get('alah'));

            if ($request->get('celah'))
                $fugasHist = $fugasHist->where('movimentacao.cela_destino', $request->get('celah'));

            if ($request->get('unidade_prisionalv'))
                $fugasHist = $fugasHist->where('movimentacao_interno.up_anterior', $request->get('unidade_prisionalv'));

            if ($request->get('pavilhaov'))
                $fugasHist = $fugasHist->where('movimentacao_interno.pv_anterior', $request->get('pavilhaov'));

            if ($request->get('alav'))
                $fugasHist = $fugasHist->where('movimentacao_interno.ala_anterior', $request->get('alav'));

            if ($request->get('celav'))
                $fugasHist = $fugasHist->where('movimentacao_interno.cela_anterior', $request->get('celav'));

            if ($request->get('datainicial'))
                $fugasHist = $fugasHist->where('movimentacao.data', '>=', Tdata::TdataMysql($request->get('datainicial')));

            if ($request->get('datafinal'))
                $fugasHist = $fugasHist->where('movimentacao.data', '<=', Tdata::TdataMysql($request->get('datafinal')));
            
            $fugasHist = $fugasHist->orderBy('movimentacao.data', 'DESC')->paginate(20)->appends($request->input());
        }
        // ----- HISTÓRICO FUGA FIM -----

        return view('movimentacoes.fuga.show',compact('unidades','fugasHist','url'));
    }

    public function storeFuga(Request $request)
    {

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dataInicioPlantao =  OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor);
        $horarioInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
        $horarioTerminoPlantao = OcorrenciaController::getHorarioFechamento59Seg($lotacaoServidor);


        /**
         * validacoes
         */

        $dataValidar = $request->get('data');
        $horaValidar = $request->get('hora');

         if(Tdata::TdataMysql($dataValidar)." ".$horaValidar < $dataInicioPlantao." ".$horarioInicioPlantao || Tdata::TdataMysql($dataValidar)." ".$horaValidar > $dataTerminoPlantao." ".$horarioTerminoPlantao)
         {
             return redirect()->back()->withInput()->with('alert-danger', 'Você não pode inserir ocorrências em horários ou datas fora do plantão em aberto!');
         }
         
         
        if($dataValidar == null ||$horaValidar == null)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Preencha os campos obrigatórios!');
        }

        $internosId = $request->get('internos');

        $created_AtFuga = date("Y-m-d H:i:s");
        $insertFuga = DB::table('movimentacao')->insert([
                'tipo' => 'fuga',
                'motivo' => $request->get('motivo'),
                'data' => Tdata::TdataMysql($request->get('data'))." ".$request->get('hora').":00",
                'created_at' => $created_AtFuga
            ]
        );
        if($insertFuga){
            $idFuga = DB::getPdo()->lastInsertId();

            $internosFuga = "";
            $idUpFuga = "";
            foreach ($internosId as $id) {
                // -------------------- update na sancao --------------------------- //
                DB::table('sancao_triagem')->where('fk_interno',$id)->where('tipo','sancao')->update([
                    'status' => 'I',
                    'update_at' => Tdata::TtimeStampHojeMysql(),
                ]);
                // -------------------- !update na sancao --------------------------- //
                DB::table('movimentacao_interno')->insert([
                        'status' => 'Finalizada',
                        'fk_movimentacao' => $idFuga,
                        'fk_interno' => $id,
                        'up_anterior' => UnidadesController::getIdUP($id),
                        'pv_anterior' => UnidadesController::getIdPavilhao($id),
                        'ala_anterior' => UnidadesController::getIdAla($id),
                        'cela_anterior' => UnidadesController::getIdCela($id)
                    ]
                );
                $internosFuga = $internosFuga."Prontuário: ".$id." - ".InternoController::getInternoNome($id).", ";
                $idUpFuga = UnidadesController::getIdUP($id);
            }
            $textoLog = "Fuga registrada em ".Tdata::TtimestampBR($created_AtFuga).". Origem: ".UnidadesController::getNomeUp($idUpFuga).". Internos: ";
            $textoLog = $textoLog.substr($internosFuga,0,-2).".";
            LogController::log($idFuga,"movimentacao","add", Auth::user()->cpf, $textoLog);

            // anexos
            if ($request->file('documentos')[0] != null) {

                $qtdArquivo = count($request->file('documentos'));

                for ($x = 0; $x < $qtdArquivo; $x++) {

                    $destinationPath = Constantes::ARQUIVOS_MOVIMENTACAO . "/fuga/" . $idFuga;

                    $name = "movimentacao" . $idFuga . "_" . time() . "_" . $request->file('documentos')[$x]->getClientOriginalName();

                    $request->file('documentos')[$x]->storeAs($destinationPath, $name);

                    $insertArquivo = DB::table('movimentacao_arquivos')->insert(
                        [
                            'fk_movimentacao' => $idFuga,
                            'nome_arquivo' => $name,
                        ]
                    );

                }
            }
            foreach ($internosId as $id) {
                DB::table('interno')->where('id', $id)->update(
                    [
                        'condicao_interno' => 631,
                        'status' => 'I',
                        'updated_at' => Tdata::TtimeStampHojeMysql(),
                    ]
                );
            }
            return redirect()->route('movimentacoes.fugaShow')->with('alert-success', 'Operação realizada com sucesso.');
        }
        else{
            return redirect()->route('movimentacoes.fugaShow')->with('alert-danger', 'Ocorreu um erro e a operação não foi realizada, entre em contato com a '.config('app.ti').'.');
        }
    }

    public function recambiamentoShow(Request $request)
    {
        $unidades = DB::table('unidade')->where('status', 1)->orderBy('sigla', 'ASC')->get();
        $unidades = json_decode($unidades,true);

        $recambiamentosHist = null;

        // ----- HISTÓRICO TRANSFERÊNCIA INÍCIO -----
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if(strpos($url,"?")){

            $recambiamentosHist = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao', function ($query) {
                    $query->on('movimentacao_interno.fk_movimentacao','=','movimentacao.id')->where('movimentacao.tipo', 'recambiamento');
                })->where('movimentacao.tipo', 'recambiamento');

            if ($request->get('unidade_prisionalh'))
                $recambiamentosHist = $recambiamentosHist->where('movimentacao.unidade_destino', $request->get('unidade_prisionalh'));

            if ($request->get('pavilhaoh'))
                $recambiamentosHist = $recambiamentosHist->where('movimentacao.pavilhao_destino', $request->get('pavilhaoh'));

            if ($request->get('alah'))
                $recambiamentosHist = $recambiamentosHist->where('movimentacao.ala_destino', $request->get('alah'));

            if ($request->get('celah'))
                $recambiamentosHist = $recambiamentosHist->where('movimentacao.cela_destino', $request->get('celah'));

            if ($request->get('unidade_prisionalv'))
                $recambiamentosHist = $recambiamentosHist->where('movimentacao_interno.up_anterior', $request->get('unidade_prisionalv'));

            if ($request->get('pavilhaov'))
                $recambiamentosHist = $recambiamentosHist->where('movimentacao_interno.pv_anterior', $request->get('pavilhaov'));

            if ($request->get('alav'))
                $recambiamentosHist = $recambiamentosHist->where('movimentacao_interno.ala_anterior', $request->get('alav'));

            if ($request->get('celav'))
                $recambiamentosHist = $recambiamentosHist->where('movimentacao_interno.cela_anterior', $request->get('celav'));

            if ($request->get('unidade_destino'))
                $recambiamentosHist = $recambiamentosHist->where('movimentacao.unidade_destino', '=', $request->get('unidade_destino'));

            if ($request->get('datainicial'))
                $recambiamentosHist = $recambiamentosHist->where('movimentacao.data', '>=', Tdata::TdataMysql($request->get('datainicial')));

            if ($request->get('datafinal'))
                $recambiamentosHist = $recambiamentosHist->where('movimentacao.data', '<=', Tdata::TdataMysql($request->get('datafinal')));

            $recambiamentosHist = $recambiamentosHist->orderBy('movimentacao.data', 'DESC')->paginate(20)->appends($request->input());
        }
        // ----- HISTÓRICO RECAMBIAMENTO FIM -----

        return view('movimentacoes.recambiamento.show',compact('unidades','recambiamentosHist','url'));
    }

    public function storeRecambiamento(Request $request)
    {

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dataInicioPlantao =  OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor);
        $horarioInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
        $horarioTerminoPlantao = OcorrenciaController::getHorarioFechamento59Seg($lotacaoServidor);


        /**
         * validacoes
         */

        $dataValidar = $request->get('data');
        $horaValidar = $request->get('hora');

         if(Tdata::TdataMysql($dataValidar)." ".$horaValidar < $dataInicioPlantao." ".$horarioInicioPlantao || Tdata::TdataMysql($dataValidar)." ".$horaValidar > $dataTerminoPlantao." ".$horarioTerminoPlantao)
         {
             return redirect()->back()->withInput()->with('alert-danger', 'Você não pode inserir ocorrências em horários ou datas fora do plantão em aberto!');
         }
         
         
        if($dataValidar == null ||$horaValidar == null)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Preencha os campos obrigatórios!');
        }

        $internosId = $request->get('internos');

        $created_AtRecambiamento = date("Y-m-d H:s");
        $insertRecambiamento = DB::table('movimentacao')->insert([
                'tipo' => 'recambiamento',
                'unidade_destino' => $request->get('unidade_destino_store'),
                'motivo' => $request->get('motivo'),
                'data' => Tdata::TdataMysql($request->get('data'))." ".$request->get('hora').":00",
                'created_at' => $created_AtRecambiamento
            ]
        );
        if($insertRecambiamento){
            $idRecambiamento = DB::getPdo()->lastInsertId();

            $internosRecambiamento = "";
            $idUpRecambiamento = "";
            foreach ($internosId as $id) {
                // -------------------- update na sancao --------------------------- //
                DB::table('sancao_triagem')->where('fk_interno',$id)->where('tipo','sancao')->update([
                    'status' => 'I',
                    'update_at' => Tdata::TtimeStampHojeMysql(),
                ]);
                // -------------------- !update na sancao --------------------------- //
                DB::table('movimentacao_interno')->insert([
                        'status' => 'Finalizada',
                        'fk_movimentacao' => $idRecambiamento,
                        'fk_interno' => $id,
                        'up_anterior' => UnidadesController::getIdUP($id),
                        'pv_anterior' => UnidadesController::getIdPavilhao($id),
                        'ala_anterior' => UnidadesController::getIdAla($id),
                        'cela_anterior' => UnidadesController::getIdCela($id)
                    ]
                );
                $internosRecambiamento = $internosRecambiamento."Prontuário: ".$id." - ".InternoController::getInternoNome($id).", ";
                $idUpRecambiamento = UnidadesController::getIdUP($id);
            }
            $textoLog = "Recambiamento registrado em ".Tdata::TtimestampBR($created_AtRecambiamento).". Origem: ".UnidadesController::getNomeUp($idUpRecambiamento).". Internos: ";
            $textoLog = $textoLog.substr($internosRecambiamento,0,-2).".";
            LogController::log($idRecambiamento,"movimentacao","add", Auth::user()->cpf, $textoLog);

            // anexos
            if ($request->file('documentos')[0] != null) {

                $qtdArquivo = count($request->file('documentos'));

                for ($x = 0; $x < $qtdArquivo; $x++) {

                    $destinationPath = Constantes::ARQUIVOS_MOVIMENTACAO . "/recambiamento/" . $idRecambiamento;

                    $name = "movimentacao" . $idRecambiamento . "_" . time() . "_" . $request->file('documentos')[$x]->getClientOriginalName();

                    $request->file('documentos')[$x]->storeAs($destinationPath, $name);

                    $insertArquivo = DB::table('movimentacao_arquivos')->insert(
                        [
                            'fk_movimentacao' => $idRecambiamento,
                            'nome_arquivo' => $name,
                        ]
                    );

                }
            }
            foreach ($internosId as $id) {
                DB::table('interno')->where('id', $id)->update(
                    [
                        'condicao_interno' => 636,
                        'status' => 'I',
                        'updated_at' => Tdata::TtimeStampHojeMysql(),
                    ]
                );
            }
            return redirect()->route('movimentacoes.recambiamentoShow')->with('alert-success', 'Operação realizada com sucesso.');
        }
        else{
            return redirect()->route('movimentacoes.recambiamentoShow')->with('alert-danger', 'Ocorreu um erro e a operação não foi realizada, entre em contato com a '.config('app.ti').'.');
        }
    }

    public function obitoShow(Request $request)
    {
        $unidades = DB::table('unidade')->where('status', 1)->orderBy('sigla', 'ASC')->get();
        $unidades = json_decode($unidades,true);

        $obitosHist = null;

        // ----- HISTÓRICO ÓBITO INÍCIO -----
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if(strpos($url,"?")){


            $obitosHist = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao', 'movimentacao_interno.fk_movimentacao','=','movimentacao.id')
                ->leftJoin('obito','obito.fk_movimentacao','=','movimentacao.id')
                ->where('movimentacao.tipo', 'obito');

            if ($request->get('unidade_prisionalh'))
                $obitosHist = $obitosHist->where('movimentacao.unidade_destino', $request->get('unidade_prisionalh'));

            if ($request->get('pavilhaoh'))
                $obitosHist = $obitosHist->where('movimentacao.pavilhao_destino', $request->get('pavilhaoh'));

            if ($request->get('alah'))
                $obitosHist = $obitosHist->where('movimentacao.ala_destino', $request->get('alah'));

            if ($request->get('celah'))
                $obitosHist = $obitosHist->where('movimentacao.cela_destino', $request->get('celah'));

            if ($request->get('unidade_prisionalv'))
                $obitosHist = $obitosHist->where('movimentacao_interno.up_anterior', $request->get('unidade_prisionalv'));

            if ($request->get('pavilhaov'))
                $obitosHist = $obitosHist->where('movimentacao_interno.pv_anterior', $request->get('pavilhaov'));

            if ($request->get('alav'))
                $obitosHist = $obitosHist->where('movimentacao_interno.ala_anterior', $request->get('alav'));

            if ($request->get('celav'))
                $obitosHist = $obitosHist->where('movimentacao_interno.cela_anterior', $request->get('celav'));

            if ($request->get('obitoSispernB'))
                $obitosHist = $obitosHist->where('obito.obito_sispern', $request->get('obitoSispernB'));

            if ($request->get('datainicial'))
                $obitosHist = $obitosHist->where('movimentacao.data', '>=', Tdata::TdataMysql($request->get('datainicial')));

            if ($request->get('datafinal'))
                $obitosHist = $obitosHist->where('movimentacao.data', '<=', Tdata::TdataMysql($request->get('datafinal')));

            $obitosHist = $obitosHist->orderBy('movimentacao.data', 'DESC')->paginate(20)->appends($request->input());
        }
        // ----- HISTÓRICO ÓBITO FIM -----

        return view('movimentacoes.obito.show',compact('unidades', 'obitosHist','url'));
    }

    public function storeObito(Request $request, $registroOcorrencia=0)
    {

        /**
         * validacoes
         */
        if($request->get('obitoSispern') == 'S'){
            $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
            
            $dataInicioPlantao =  OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
            $dataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor);
            $horarioInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
            $horarioTerminoPlantao = OcorrenciaController::getHorarioFechamento59Seg($lotacaoServidor);

            $dataValidar = $request->get('data');
            $horaValidar = $request->get('hora');


            if(Tdata::TdataMysql($dataValidar)." ".$horaValidar < $dataInicioPlantao." ".$horarioInicioPlantao || Tdata::TdataMysql($dataValidar)." ".$horaValidar > $dataTerminoPlantao." ".$horarioTerminoPlantao)
            {
                return redirect()->back()->withInput()->with('alert-danger', 'Você não pode inserir ocorrências em horários ou datas fora do plantão em aberto!');
            }
            
            if($dataValidar == null ||$horaValidar == null)
            {
                return redirect()->back()->withInput()->with('alert-danger', 'Preencha os campos obrigatórios!');
            }
        }

        $internosId = $request->get('internos');

        $created_AtObito = date("Y-m-d H:i:s");

        $insertObito = [];
        if($request->get('obitoSispern') == "S"){
            $insertObito = DB::table('movimentacao')->insert([
                    'tipo' => 'obito',
                    'motivo' => $request->get('motivo'),
                    'data' => Tdata::TdataMysql($request->get('data'))." ".$request->get('hora').":00",
                    'created_at' => $created_AtObito
                ]
            );
        }
        else if($request->get('obitoSispern') == "N"){
            $insertObito = DB::table('movimentacao')->insert([
                    'tipo' => 'obito',
                    'motivo' => $request->get('motivo'),
                    'data' => Tdata::TdataMysql($request->get('data')),
                    'created_at' => $created_AtObito
                ]
            );
        }

        if($insertObito){
            $idObito = DB::getPdo()->lastInsertId();

            $internosObito = "";
            $idUpObito = "";
            foreach ($internosId as $id) {
                // -------------------- update na sancao --------------------------- //
                DB::table('sancao_triagem')->where('fk_interno',$id)->where('tipo','sancao')->update([
                    'status' => 'I',
                    'update_at' => Tdata::TtimeStampHojeMysql(),
                ]);
                // -------------------- !update na sancao --------------------------- //
                DB::table('movimentacao_interno')->insert([
                        'status' => 'Finalizada',
                        'fk_movimentacao' => $idObito,
                        'fk_interno' => $id,
                        'up_anterior' => UnidadesController::getIdUP($id),
                        'pv_anterior' => UnidadesController::getIdPavilhao($id),
                        'ala_anterior' => UnidadesController::getIdAla($id),
                        'cela_anterior' => UnidadesController::getIdCela($id)
                    ]
                );
                $internosObito = $internosObito."Prontuário: ".$id." - ".InternoController::getInternoNome($id).", ";
                $idUpObito = UnidadesController::getIdUP($id);
            }
            $textoLog = "Óbito registrado em ".Tdata::TtimestampBR($created_AtObito).". Origem: ".UnidadesController::getNomeUp($idUpObito).". Internos: ";
            $textoLog = $textoLog.substr($internosObito,0,-2).".";
            LogController::log($idObito,"movimentacao","add", Auth::user()->cpf, $textoLog);

            // anexos
            if ($request->file('documentos')[0] != null) {

                $qtdArquivo = count($request->file('documentos'));

                for ($x = 0; $x < $qtdArquivo; $x++) {

                    $destinationPath = Constantes::ARQUIVOS_MOVIMENTACAO . "/obito/" . $idObito;

                    $name = "movimentacao" . $idObito . "_" . time() . "_" . $request->file('documentos')[$x]->getClientOriginalName();

                    $request->file('documentos')[$x]->storeAs($destinationPath, $name);

                    $insertArquivo = DB::table('movimentacao_arquivos')->insert(
                        [
                            'fk_movimentacao' => $idObito,
                            'nome_arquivo' => $name,
                        ]
                    );

                }
            }
            foreach ($internosId as $id) {
                DB::table('interno')->where('id', $id)->update(
                    [
                        'condicao_interno' => 637,
                        'status' => 'I',
                        'updated_at' => Tdata::TtimeStampHojeMysql(),
                    ]
                );

                DB::table('obito')->insert([
                    'fk_movimentacao' => $idObito,
                    'interno' => $id,
                    'data_obito' => Tdata::TdataMysql($request->get('data')),
                    'obito_sispern' => $request->get('obitoSispern'),
                    'created_at' => Tdata::TtimeStampHojeMysql(),
                ]);

                LogController::log($idObito, "obito","add", Auth::user()->cpf, "Inserção de óbito<br> Interno: ".InternoController::getInternoNome($id)."(".$id.") <br>Data do Óbito: ".$request->get('data'));

            }


            if($registroOcorrencia == 1)
            {

                return redirect()->route('ocorrencia.cadastro', ['id'=>35, 'returnLivro' => 0])->with('alert-success', 'Óbito cadastrado com sucesso!');

            }else {
                return redirect()->route('movimentacoes.obitoShow')->with('alert-success', 'Operação realizada com sucesso.');
            }
        }
        else{
            return redirect()->route('movimentacoes.obitoShow')->with('alert-danger', 'Ocorreu um erro e a operação não foi realizada, entre em contato com a '.config('app.ti').'.');
        }
    }

    public function saidajudicialShow(Request $request){
       $unidades = DB::table('unidade')->where('status', 1)->orderBy('sigla', 'ASC')->get();
       $unidades = json_decode($unidades,true);

        $saidajudicialHist = null;
        
       // ----- HISTÓRICO SAIDA JUDICIAL INÍCIO -----
       $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

       if(strpos($url,"?")){

           $saidajudicialHist = DB::table('movimentacao_interno')
               ->leftJoin('movimentacao', 'movimentacao_interno.fk_movimentacao','=','movimentacao.id')
               ->where('movimentacao.tipo', 'saidajudicial');

           
           
           if ($request->get('unidade_prisionalv'))
               $saidajudicialHist = $saidajudicialHist->where('movimentacao_interno.up_anterior', $request->get('unidade_prisionalv'));

           if ($request->get('pavilhaov'))
               $saidajudicialHist = $saidajudicialHist->where('movimentacao_interno.pv_anterior', $request->get('pavilhaov'));

           if ($request->get('alav'))
               $saidajudicialHist = $saidajudicialHist->where('movimentacao_interno.ala_anterior', $request->get('alav'));

           if ($request->get('celav'))
               $saidajudicialHist = $saidajudicialHist->where('movimentacao_interno.cela_anterior', $request->get('celav'));

           if ($request->get('datainicial'))
               $saidajudicialHist = $saidajudicialHist->where('movimentacao.data', '>=', Tdata::TdataMysql($request->get('datainicial')));

           if ($request->get('datafinal')){
               $saidajudicialHist = $saidajudicialHist->where('movimentacao.data', '<=', Tdata::TdataMysql($request->get('datafinal')));
           }
           
           
            $saidajudicialHist = $saidajudicialHist->orderBy('movimentacao.data', 'DESC')->paginate(20)->appends($request->input());
              

       }
       
       // ----- HISTORICO SAIDA JUDICIAL FIM -----


       $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));



        return view('movimentacoes.saidajudicial.show',compact('unidades','saidajudicialHist','url','lotacaoServidor'));
    }

    public function saidajudicialStore(Request $request){

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dataInicioPlantao =  OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor);
        $horarioInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
        $horarioTerminoPlantao = OcorrenciaController::getHorarioFechamento59Seg($lotacaoServidor);


        /**
         * validacoes
         */

        $dataValidar = $request->get('data');
        $horaValidar = $request->get('hora');

         if(Tdata::TdataMysql($dataValidar)." ".$horaValidar < $dataInicioPlantao." ".$horarioInicioPlantao || Tdata::TdataMysql($dataValidar)." ".$horaValidar > $dataTerminoPlantao." ".$horarioTerminoPlantao)
         {
             return redirect()->back()->withInput()->with('alert-danger', 'Você não pode inserir ocorrências em horários ou datas fora do plantão em aberto!');
         }

        if($dataValidar == null ||$horaValidar == null)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Preencha os campos obrigatórios!');
        }

        $internoId = $request->get('internos');

        if(isset($internoId[0])){
            $internoId = $internoId[0];
        } else {
            return redirect()->back()->withInput()->with('alert-danger', 'Ocorreu um erro, tente novamente ou entre em contato com a '.config('app.ti').'.');
        }

        $created_AtSaida = date("Y-m-d H:i:s");
        $insertSaida = DB::table('movimentacao')->insert([
                'tipo' => 'saidajudicial',
                'motivo' => $request->get('motivo'),
                'data' => Tdata::TdataMysql($request->get('data'))." ".$request->get('hora').":00",
                'created_at' => $created_AtSaida
            ]
        );
        if($insertSaida){
            $idSaida = DB::getPdo()->lastInsertId();

            $internoSaida = "";
            $idUpSaida = "";

                // -------------------- update na sancao --------------------------- //
            DB::table('sancao_triagem')->where('fk_interno',$internoId)->update([
                'status' => 'I',
                'update_at' => Tdata::TtimeStampHojeMysql(),
            ]);
            // -------------------- !update na sancao --------------------------- //
            DB::table('movimentacao_interno')->insert([
                    'status' => 'Finalizada',
                    'fk_movimentacao' => $idSaida,
                    'fk_interno' => $internoId,
                    'up_anterior' => UnidadesController::getIdUP($internoId),
                    'pv_anterior' => UnidadesController::getIdPavilhao($internoId),
                    'ala_anterior' => UnidadesController::getIdAla($internoId),
                    'cela_anterior' => UnidadesController::getIdCela($internoId)
                ]
            );
            $internoSaida = $internoSaida." Prontuário: ".$internoId." - ".InternoController::getInternoNome($internoId).", ";
            $idUpSaida = UnidadesController::getIdUP($internoId);

            DB::table('interno')->where('id', $internoId)->update(
                [
                    'condicao_interno' => 646,
                    'status' => 'I',
                    'updated_at' => Tdata::TtimeStampHojeMysql(),
                ]
            );


            $textoLog = "Saída judicial registrado em ".Tdata::TtimestampBR($created_AtSaida).". Origem: ".UnidadesController::getNomeUp($idUpSaida).". Interno: ";
            $textoLog = $textoLog.substr($internoSaida,0,-2).".";
            LogController::log($idSaida,"movimentacao","add", Auth::user()->cpf, $textoLog);

            // anexos
            if ($request->file('documentos')[0] != null) {

                $qtdArquivo = count($request->file('documentos'));

                for ($x = 0; $x < $qtdArquivo; $x++) {

                    $destinationPath = Constantes::ARQUIVOS_MOVIMENTACAO . "/saidajudicial/" . $idSaida;

                    $name = "movimentacao" . $idSaida . "_" . time() . "_" . $request->file('documentos')[$x]->getClientOriginalName();

                    $request->file('documentos')[$x]->storeAs($destinationPath, $name);

                    $insertArquivo = DB::table('movimentacao_arquivos')->insert(
                        [
                            'fk_movimentacao' => $idSaida,
                            'nome_arquivo' => $name,
                        ]
                    );

                }
            }
            return redirect()->route('movimentacoes.saidajudicialShow')->with('alert-success', 'Operação realizada com sucesso.');
        }
        else{
            return redirect()->route('movimentacoes.saidajudicialShow')->with('alert-danger', 'Ocorreu um erro e a operação não foi realizada, entre em contato com a '.config('app.ti').'.');
        }
    }

    public function obitoNoSispern($idObito)
    {
        return DB::table('movimentacao')->join('obito','movimentacao.id','=','obito.fk_movimentacao')->where('movimentacao.id',$idObito)->where('obito.obito_sispern','S')->count() > 0;
    }

    public function extincaopunibilidadeShow(Request $request)
    {
        $unidades = DB::table('unidade')->where('status', 1)->orderBy('sigla', 'ASC')->get();
        $unidades = json_decode($unidades,true);

        $extincoespunibilidadesHist = null;

        // ----- HISTÓRICO EXTINÇÃOPUNIBILIDADE INÍCIO -----
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if(strpos($url,"?")){


            $extincoespunibilidadesHist = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao', function ($query) {
                    $query->on('movimentacao_interno.fk_movimentacao','=','movimentacao.id')->where('movimentacao.tipo', 'extincaopunibilidade');
                })->where('movimentacao.tipo', 'extincaopunibilidade');

            if ($request->get('unidade_prisionalh'))
                $extincoespunibilidadesHist = $extincoespunibilidadesHist->where('movimentacao.unidade_destino', $request->get('unidade_prisionalh'));

            if ($request->get('pavilhaoh'))
                $extincoespunibilidadesHist = $extincoespunibilidadesHist->where('movimentacao.pavilhao_destino', $request->get('pavilhaoh'));

            if ($request->get('alah'))
                $extincoespunibilidadesHist = $extincoespunibilidadesHist->where('movimentacao.ala_destino', $request->get('alah'));

            if ($request->get('celah'))
                $extincoespunibilidadesHist = $extincoespunibilidadesHist->where('movimentacao.cela_destino', $request->get('celah'));

            if ($request->get('unidade_prisionalv'))
                $extincoespunibilidadesHist = $extincoespunibilidadesHist->where('movimentacao_interno.up_anterior', $request->get('unidade_prisionalv'));

            if ($request->get('pavilhaov'))
                $extincoespunibilidadesHist = $extincoespunibilidadesHist->where('movimentacao_interno.pv_anterior', $request->get('pavilhaov'));

            if ($request->get('alav'))
                $extincoespunibilidadesHist = $extincoespunibilidadesHist->where('movimentacao_interno.ala_anterior', $request->get('alav'));

            if ($request->get('celav'))
                $extincoespunibilidadesHist = $extincoespunibilidadesHist->where('movimentacao_interno.cela_anterior', $request->get('celav'));

            if ($request->get('datainicial'))
                $extincoespunibilidadesHist = $extincoespunibilidadesHist->where('movimentacao.data', '>=', Tdata::TdataMysql($request->get('datainicial')));

            if ($request->get('datafinal'))
                $extincoespunibilidadesHist = $extincoespunibilidadesHist->where('movimentacao.data', '<=', Tdata::TdataMysql($request->get('datafinal')));

            $extincoespunibilidadesHist = $extincoespunibilidadesHist->orderBy('movimentacao.data', 'DESC')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->paginate(20)->appends($request->input());

            // $extincoespunibilidadesHist = $extincoespunibilidadesHist->unique('idmov');

            // $extincoespunibilidadesHist = json_decode($extincoespunibilidadesHist, true);
            // foreach ($extincoespunibilidadesHist as $key => $extincaopunibilidadeHist) {
            //     $extincoespunibilidadesHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $extincaopunibilidadeHist['idmov'])->get();
            //     $extincoespunibilidadesHist[$key]['arquivos'] = json_decode($extincoespunibilidadesHist[$key]['arquivos'], true);

            //     if(count(LogController::getDadosLog($extincaopunibilidadeHist['idmov'], 'movimentacao', 'add')) != 0){
            //         $extincoespunibilidadesHist[$key]['log'] = LogController::getDadosLog($extincaopunibilidadeHist['idmov'], 'movimentacao', 'add');
            //         $extincoespunibilidadesHist[$key]['log'] = FacadeController::getNomeServidorCpf($extincoespunibilidadesHist[$key]['log'][0]->cpf_usuario)." em ".Tdata::TtimestampBR($extincoespunibilidadesHist[$key]['log'][0]->created_at);
            //     }
            //     else{
            //         $extincoespunibilidadesHist[$key]['log'] = "Sem log registrado.";
            //     }

            //     if($extincaopunibilidadeHist['data'] == null || $extincaopunibilidadeHist['data'] == ""){
            //         $extincoespunibilidadesHist[$key]['dia'] = "";
            //         $extincoespunibilidadesHist[$key]['mes'] = "";
            //         $extincoespunibilidadesHist[$key]['ano'] = "";
            //     }
            //     else{
            //         $extincoespunibilidadesHist[$key]['dia'] = Tdata::getDiaData(Tdata::getDataTimestamp($extincaopunibilidadeHist['data']));
            //         $extincoespunibilidadesHist[$key]['mes'] = Tdata::getMesExtAbrev(Tdata::getMesData(Tdata::getDataTimestamp($extincaopunibilidadeHist['data'])));
            //         $extincoespunibilidadesHist[$key]['ano'] = Tdata::getAnoData(Tdata::getDataTimestamp($extincaopunibilidadeHist['data']));
            //     }
            //     $extincoespunibilidadesHist[$key]['hora'] = Tdata::getTempoTimestamp($extincaopunibilidadeHist['data']);
            //     $extincoespunibilidadesHist[$key]['destino'] = UnidadesController::getSiglaUp($extincaopunibilidadeHist['unidade_destino'])." - ".UnidadesController::getNomePav($extincaopunibilidadeHist['pavilhao_destino'])." - ".UnidadesController::getNomeAla($extincaopunibilidadeHist['ala_destino'])." - ".UnidadesController::getNomeCela($extincaopunibilidadeHist['cela_destino']);
            //     $extincoespunibilidadesHist[$key]['internos'] = DB::table('movimentacao_interno')
            //         ->where('fk_movimentacao',$extincaopunibilidadeHist['fk_movimentacao'])
            //         ->get();
            //     $extincoespunibilidadesHist[$key]['internos'] = json_decode($extincoespunibilidadesHist[$key]['internos'],true);
            //     foreach($extincoespunibilidadesHist[$key]['internos'] as $keyy => $interno){
            //         $extincoespunibilidadesHist[$key]['internos'][$keyy]['id'] = $interno['fk_interno'];
            //         $extincoespunibilidadesHist[$key]['internos'][$keyy]['nome'] = InternoController::getInternoNome($interno['fk_interno']);
            //         $extincoespunibilidadesHist[$key]['internos'][$keyy]['lotacao_anterior'] = UnidadesController::getSiglaUp($interno['up_anterior'])." - ".UnidadesController::getNomePav($interno['pv_anterior'])." - ".UnidadesController::getNomeAla($interno['ala_anterior'])." - ".UnidadesController::getNomeCela($interno['cela_anterior']);;
            //     }
            // }

            // $extincoespunibilidadesHist = collect($extincoespunibilidadesHist)->paginate(20)->appends($request->input());
        }
        // ----- HISTÓRICO EXTINÇÃOPUNIBILIDADE FIM -----

        return view('movimentacoes.extincaopunibilidade.show',compact('unidades','extincoespunibilidadesHist','url'));
    }

    public function storeExtincaoPunibilidade(Request $request)
    {

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dataInicioPlantao =  OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor);
        $horarioInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
        $horarioTerminoPlantao = OcorrenciaController::getHorarioFechamento59Seg($lotacaoServidor);


        /**
         * validacoes
         */

        $dataValidar = $request->get('data');
        $horaValidar = $request->get('hora');

         if(Tdata::TdataMysql($dataValidar)." ".$horaValidar < $dataInicioPlantao." ".$horarioInicioPlantao || Tdata::TdataMysql($dataValidar)." ".$horaValidar > $dataTerminoPlantao." ".$horarioTerminoPlantao)
         {
             return redirect()->back()->withInput()->with('alert-danger', 'Você não pode inserir ocorrências em horários ou datas fora do plantão em aberto!');
         }
         
         
        if($dataValidar == null ||$horaValidar == null)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Preencha os campos obrigatórios!');
        }

        $internosId = $request->get('internos');

        $created_AtExtincao = date("Y-m-d H:i:s");
        $insertExtincaoPunibilidade = DB::table('movimentacao')->insert([
                'tipo' => 'extincaopunibilidade',
                'motivo' => $request->get('motivo'),
                'data' => Tdata::TdataMysql($request->get('data'))." ".$request->get('hora').":00",
                'created_at' => $created_AtExtincao
            ]
        );
        if($insertExtincaoPunibilidade){
            $idExtincaoPunibilidade = DB::getPdo()->lastInsertId();

            $internosExtincao = "";
            $idUpExtincao = "";
            foreach ($internosId as $id) {
                // -------------------- update na sancao --------------------------- //
                DB::table('sancao_triagem')->where('fk_interno',$id)->where('tipo','sancao')->update([
                    'status' => 'I',
                    'update_at' => Tdata::TtimeStampHojeMysql(),
                ]);
                // -------------------- !update na sancao --------------------------- //
                DB::table('movimentacao_interno')->insert([
                        'status' => 'Finalizada',
                        'fk_movimentacao' => $idExtincaoPunibilidade,
                        'fk_interno' => $id,
                        'up_anterior' => UnidadesController::getIdUP($id),
                        'pv_anterior' => UnidadesController::getIdPavilhao($id),
                        'ala_anterior' => UnidadesController::getIdAla($id),
                        'cela_anterior' => UnidadesController::getIdCela($id)
                    ]
                );
                $internosExtincao = $internosExtincao."Prontuário: ".$id." - ".InternoController::getInternoNome($id).", ";
                $idUpExtincao = UnidadesController::getIdUP($id);
            }
            $textoLog = "Extinção de Punibilidade registrada em ".Tdata::TtimestampBR($created_AtExtincao).". Origem: ".UnidadesController::getNomeUp($idUpExtincao).". Internos: ";
            $textoLog = $textoLog.substr($internosExtincao,0,-2).".";
            LogController::log($idExtincaoPunibilidade,"movimentacao","add", Auth::user()->cpf, $textoLog);

            // anexos
            if ($request->file('documentos')[0] != null) {

                $qtdArquivo = count($request->file('documentos'));

                for ($x = 0; $x < $qtdArquivo; $x++) {

                    $destinationPath = Constantes::ARQUIVOS_MOVIMENTACAO . "/extincaopunibilidade/" . $idExtincaoPunibilidade;

                    $name = "movimentacao" . $idExtincaoPunibilidade . "_" . time() . "_" . $request->file('documentos')[$x]->getClientOriginalName();

                    $request->file('documentos')[$x]->storeAs($destinationPath, $name);

                    $insertArquivo = DB::table('movimentacao_arquivos')->insert(
                        [
                            'fk_movimentacao' => $idExtincaoPunibilidade,
                            'nome_arquivo' => $name,
                        ]
                    );

                }
            }
            foreach ($internosId as $id) {
                DB::table('interno')->where('id', $id)->update(
                    [
                        'sentenciado' => 'S',
                        'condicao_interno' => 639,
                        'status' => 'I',
                        'updated_at' => Tdata::TtimeStampHojeMysql(),
                    ]
                );
            }
            return redirect()->route('movimentacoes.extincaopunibilidadeShow')->with('alert-success', 'Operação realizada com sucesso.');
        }
        else{
            return redirect()->route('movimentacoes.extincaopunibilidadeShow')->with('alert-danger', 'Ocorreu um erro e a operação não foi realizada, entre em contato com a '.config('app.ti').'.');
        }
    }

    public function extradicaoShow(Request $request)
    {
        $unidades = DB::table('unidade')->where('status', 1)->orderBy('sigla', 'ASC')->get();
        $unidades = json_decode($unidades,true);

        $extradicoesHist = null;

        // ----- HISTÓRICO EXTINÇÃOPUNIBILIDADE INÍCIO -----
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if(strpos($url,"?")){


            $extradicoesHist = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao', function ($query) {
                    $query->on('movimentacao_interno.fk_movimentacao','=','movimentacao.id')->where('movimentacao.tipo', 'extradicao');
                })->where('movimentacao.tipo', 'extradicao');

            if ($request->get('unidade_prisionalh'))
                $extradicoesHist = $extradicoesHist->where('movimentacao.unidade_destino', $request->get('unidade_prisionalh'));

            if ($request->get('pavilhaoh'))
                $extradicoesHist = $extradicoesHist->where('movimentacao.pavilhao_destino', $request->get('pavilhaoh'));

            if ($request->get('alah'))
                $extradicoesHist = $extradicoesHist->where('movimentacao.ala_destino', $request->get('alah'));

            if ($request->get('celah'))
                $extradicoesHist = $extradicoesHist->where('movimentacao.cela_destino', $request->get('celah'));

            if ($request->get('unidade_prisionalv'))
                $extradicoesHist = $extradicoesHist->where('movimentacao_interno.up_anterior', $request->get('unidade_prisionalv'));

            if ($request->get('pavilhaov'))
                $extradicoesHist = $extradicoesHist->where('movimentacao_interno.pv_anterior', $request->get('pavilhaov'));

            if ($request->get('alav'))
                $extradicoesHist = $extradicoesHist->where('movimentacao_interno.ala_anterior', $request->get('alav'));

            if ($request->get('celav'))
                $extradicoesHist = $extradicoesHist->where('movimentacao_interno.cela_anterior', $request->get('celav'));

            if ($request->get('datainicial'))
                $extradicoesHist = $extradicoesHist->where('movimentacao.data', '>=', Tdata::TdataMysql($request->get('datainicial')));

            if ($request->get('datafinal'))
                $extradicoesHist = $extradicoesHist->where('movimentacao.data', '<=', Tdata::TdataMysql($request->get('datafinal')));

            $extradicoesHist = $extradicoesHist->orderBy('movimentacao.data', 'DESC')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->paginate(20)->appends($request->input());

        }
        // ----- HISTÓRICO EXTINÇÃOPUNIBILIDADE FIM -----

        return view('movimentacoes.extradicao.show',compact('unidades','extradicoesHist','url'));
    }

    public function storeExtradicao(Request $request)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dataInicioPlantao =  OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor);
        $horarioInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
        $horarioTerminoPlantao = OcorrenciaController::getHorarioFechamento59Seg($lotacaoServidor);


        /**
         * validacoes
         */

        $dataValidar = $request->get('data');
        $horaValidar = $request->get('hora');

         if(Tdata::TdataMysql($dataValidar)." ".$horaValidar < $dataInicioPlantao." ".$horarioInicioPlantao || Tdata::TdataMysql($dataValidar)." ".$horaValidar > $dataTerminoPlantao." ".$horarioTerminoPlantao)
         {
             return redirect()->back()->withInput()->with('alert-danger', 'Você não pode inserir ocorrências em horários ou datas fora do plantão em aberto!');
         }
         
        if($dataValidar == null ||$horaValidar == null)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Preencha os campos obrigatórios!');
        }

        $internosId = $request->get('internos');

        $created_AtLivramentoCond = date("Y-m-d H:i:s");
        $insertExtradicao = DB::table('movimentacao')->insert([
                'tipo' => 'extradicao',
                'motivo' => $request->get('motivo'),
                'data' => Tdata::TdataMysql($request->get('data'))." ".$request->get('hora').":00",
                'created_at' => $created_AtLivramentoCond
            ]
        );
        if($insertExtradicao){
            $idExtradicao = DB::getPdo()->lastInsertId();

            $internosExtrad = "";
            $idUpExt = "";
            foreach ($internosId as $id) {
                // -------------------- update na sancao --------------------------- //
                DB::table('sancao_triagem')->where('fk_interno',$id)->where('tipo','sancao')->update([
                    'status' => 'I',
                    'update_at' => Tdata::TtimeStampHojeMysql(),
                ]);
                // -------------------- !update na sancao --------------------------- //
                DB::table('movimentacao_interno')->insert([
                        'status' => 'Finalizada',
                        'fk_movimentacao' => $idExtradicao,
                        'fk_interno' => $id,
                        'up_anterior' => UnidadesController::getIdUP($id),
                        'pv_anterior' => UnidadesController::getIdPavilhao($id),
                        'ala_anterior' => UnidadesController::getIdAla($id),
                        'cela_anterior' => UnidadesController::getIdCela($id)
                    ]
                );
                $internosExtrad = $internosExtrad."Prontuário: ".$id." - ".InternoController::getInternoNome($id).", ";
                $idUpExt = UnidadesController::getIdUP($id);
            }
            $textoLog = "Extradição registrada em ".Tdata::TtimestampBR($created_AtLivramentoCond).". Origem: ".UnidadesController::getNomeUp($idUpExt).". Internos: ";
            $textoLog = $textoLog.substr($internosExtrad,0,-2).".";
            LogController::log($idExtradicao,"movimentacao","add", Auth::user()->cpf, $textoLog);

            // anexos
            if ($request->file('documentos')[0] != null) {

                $qtdArquivo = count($request->file('documentos'));

                for ($x = 0; $x < $qtdArquivo; $x++) {

                    $destinationPath = Constantes::ARQUIVOS_MOVIMENTACAO . "/extradicao/" . $idExtradicao;

                    $name = "movimentacao" . $idExtradicao . "_" . time() . "_" . $request->file('documentos')[$x]->getClientOriginalName();

                    $request->file('documentos')[$x]->storeAs($destinationPath, $name);

                    $insertArquivo = DB::table('movimentacao_arquivos')->insert(
                        [
                            'fk_movimentacao' => $idExtradicao,
                            'nome_arquivo' => $name,
                        ]
                    );

                }
            }
            foreach ($internosId as $id) {
                DB::table('interno')->where('id', $id)->update(
                    [
                        'sentenciado' => 'S',
                        'condicao_interno' => 650,
                        'status' => 'I',
                        'updated_at' => Tdata::TtimeStampHojeMysql(),
                    ]
                );
            }
            return redirect()->route('movimentacoes.extradicaoShow')->with('alert-success', 'Operação realizada com sucesso.');
        }
        else{
            return redirect()->route('movimentacoes.extradicaoShow')->with('alert-danger', 'Ocorreu um erro e a operação não foi realizada, entre em contato com a '.config('app.ti').'.');
        }
    }

    public function prisaodomiciliarShow(Request $request)
    {
        $unidades = DB::table('unidade')->where('status', 1)->orderBy('sigla', 'ASC')->get();
        $unidades = json_decode($unidades,true);

        $prisoesdomiciliaresHist = null;

        // ----- HISTÓRICO PRISÕESDOMICILIARES INÍCIO -----
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if(strpos($url,"?")){


            $prisoesdomiciliaresHist = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao', 'movimentacao_interno.fk_movimentacao','=','movimentacao.id')
                ->where('movimentacao.tipo', 'prisaodomiciliar');

            if ($request->get('unidade_prisionalh'))
                $prisoesdomiciliaresHist = $prisoesdomiciliaresHist->where('movimentacao.unidade_destino', $request->get('unidade_prisionalh'));

            if ($request->get('pavilhaoh'))
                $prisoesdomiciliaresHist = $prisoesdomiciliaresHist->where('movimentacao.pavilhao_destino', $request->get('pavilhaoh'));

            if ($request->get('alah'))
                $prisoesdomiciliaresHist = $prisoesdomiciliaresHist->where('movimentacao.ala_destino', $request->get('alah'));

            if ($request->get('celah'))
                $prisoesdomiciliaresHist = $prisoesdomiciliaresHist->where('movimentacao.cela_destino', $request->get('celah'));

            if ($request->get('unidade_prisionalv'))
                $prisoesdomiciliaresHist = $prisoesdomiciliaresHist->where('movimentacao_interno.up_anterior', $request->get('unidade_prisionalv'));

            if ($request->get('pavilhaov'))
                $prisoesdomiciliaresHist = $prisoesdomiciliaresHist->where('movimentacao_interno.pv_anterior', $request->get('pavilhaov'));

            if ($request->get('alav'))
                $prisoesdomiciliaresHist = $prisoesdomiciliaresHist->where('movimentacao_interno.ala_anterior', $request->get('alav'));

            if ($request->get('celav'))
                $prisoesdomiciliaresHist = $prisoesdomiciliaresHist->where('movimentacao_interno.cela_anterior', $request->get('celav'));

            if ($request->get('datainicial'))
                $prisoesdomiciliaresHist = $prisoesdomiciliaresHist->where('movimentacao.data', '>=', Tdata::TdataMysql($request->get('datainicial')));

            if ($request->get('datafinal'))
                $prisoesdomiciliaresHist = $prisoesdomiciliaresHist->where('movimentacao.data', '<=', Tdata::TdataMysql($request->get('datafinal')));
           
            $prisoesdomiciliaresHist = $prisoesdomiciliaresHist->orderBy('movimentacao.data', 'DESC')->paginate(20)->appends($request->input());
        }
        // ----- HISTÓRICO PRISÕESDOMICILIARES FIM -----

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        return view('movimentacoes.prisaodomiciliar.show',compact('unidades','prisoesdomiciliaresHist','url','lotacaoServidor'));
    }

    public function storePrisaoDomiciliar(Request $request)
    {

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dataInicioPlantao =  OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor);
        $horarioInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
        $horarioTerminoPlantao = OcorrenciaController::getHorarioFechamento59Seg($lotacaoServidor);



        /**
         * validacoes
         */

        $dataValidar = $request->get('data');
        $horaValidar = $request->get('hora');

         if(Tdata::TdataMysql($dataValidar)." ".$horaValidar < $dataInicioPlantao." ".$horarioInicioPlantao || Tdata::TdataMysql($dataValidar)." ".$horaValidar > $dataTerminoPlantao." ".$horarioTerminoPlantao)
         {
             return redirect()->back()->withInput()->with('alert-danger', 'Você não pode inserir ocorrências em horários ou datas fora do plantão em aberto!');
         }
         
        if($dataValidar == null ||$horaValidar == null)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Preencha os campos obrigatórios!');
        }

        $internosId = $request->get('internos');

        $created_AtPrDom = date("Y-m-d H:i:s");
        $insertPrisaoDomiciliar = DB::table('movimentacao')->insert([
                'tipo' => 'prisaodomiciliar',
                'motivo' => $request->get('motivo'),
                'data' => Tdata::TdataMysql($request->get('data'))." ".$request->get('hora').":00",
                'created_at' => $created_AtPrDom
            ]
        );
        if($insertPrisaoDomiciliar){
            $idPrisaoDomiciliar = DB::getPdo()->lastInsertId();

            $internosPrDom = "";
            $idUpPrDom = "";
            foreach ($internosId as $id) {
                // -------------------- update na sancao --------------------------- //
                DB::table('sancao_triagem')->where('fk_interno',$id)->where('tipo','sancao')->update([
                    'status' => 'I',
                    'update_at' => Tdata::TtimeStampHojeMysql(),
                ]);
                // -------------------- !update na sancao --------------------------- //
                DB::table('movimentacao_interno')->insert([
                        'status' => 'Finalizada',
                        'fk_movimentacao' => $idPrisaoDomiciliar,
                        'fk_interno' => $id,
                        'up_anterior' => UnidadesController::getIdUP($id),
                        'pv_anterior' => UnidadesController::getIdPavilhao($id),
                        'ala_anterior' => UnidadesController::getIdAla($id),
                        'cela_anterior' => UnidadesController::getIdCela($id)
                    ]
                );
                $internosPrDom = $internosPrDom."Prontuário: ".$id." - ".InternoController::getInternoNome($id).", ";
                $idUpPrDom = UnidadesController::getIdUP($id);
            }
            $textoLog = "Prisão Domiciliar registrada em ".Tdata::TtimestampBR($created_AtPrDom).". Origem: ".UnidadesController::getNomeUp($idUpPrDom).". Internos: ";
            $textoLog = $textoLog.substr($internosPrDom,0,-2).".";
            LogController::log($idPrisaoDomiciliar,"movimentacao","add", Auth::user()->cpf, $textoLog);

            // anexos
            if ($request->file('documentos')[0] != null) {

                $qtdArquivo = count($request->file('documentos'));

                for ($x = 0; $x < $qtdArquivo; $x++) {

                    $destinationPath = Constantes::ARQUIVOS_MOVIMENTACAO . "/prisaodomiciliar/" . $idPrisaoDomiciliar;

                    $name = "movimentacao" . $idPrisaoDomiciliar . "_" . time() . "_" . $request->file('documentos')[$x]->getClientOriginalName();

                    $request->file('documentos')[$x]->storeAs($destinationPath, $name);

                    $insertArquivo = DB::table('movimentacao_arquivos')->insert(
                        [
                            'fk_movimentacao' => $idPrisaoDomiciliar,
                            'nome_arquivo' => $name,
                        ]
                    );

                }
            }
            foreach ($internosId as $id) {

                if(FacadeController::getLotacaoServidor(Auth::user()->cpf) != config('app.tz')){
                    DB::table('interno')->where('id', $id)->update(
                        [
                            'status' => 'I',
                            'condicao_interno' => 635,
                            'updated_at' => Tdata::TtimeStampHojeMysql(),
                        ]
                    );
                }else {

                    if ($request->get('tornozeleira') == "N") {
                        DB::table('interno')->where('id', $id)->update(
                            [
                                'status' => 'I',
                                'condicao_interno' => 635,
                                'updated_at' => Tdata::TtimeStampHojeMysql(),
                            ]
                        );
                    } else {
                        DB::table('interno')->where('id', $id)->update(
                            [
                                'status' => 'A',
                                'condicao_interno' => 635,
                                'updated_at' => Tdata::TtimeStampHojeMysql(),
                            ]
                        );
                    }
                }
            }
            return redirect()->route('movimentacoes.prisaodomiciliarShow')->with('alert-success', 'Operação realizada com sucesso.');
        }
        else{
            return redirect()->route('movimentacoes.prisaodomiciliarShow')->with('alert-danger', 'Ocorreu um erro e a operação não foi realizada, entre em contato com a '.config('app.ti').'.');
        }
    }

    public function transferenciaShow(Request $request)
    {
//       dd($request->all());

        $btnPdf = $request->get('btnGerarPdf');

        // notificacao
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula');
        $insertNotificacaoLida = DB::table('notificacao_lida')->insert([
            'tipo' => 'alocacaoCela',
            'fk_servidor' => $matriculaServidorLogado[0]->matricula,
            'created_at' => Tdata::TdataHojeMysql(),
        ]);
        // --------------------------------

        $usuarioLogadoUP = DB::table('unidade')->where('sigla', utf8_encode(FacadeController::getLotacaoServidor(Auth::user()->cpf)))->get();
        $usuarioLogadoUP = json_decode($usuarioLogadoUP, true)[0];

        $unidades = DB::table('unidade')->where('status', 1)->orderBy('sigla', 'ASC')->get();

        $unidades = json_decode($unidades,true);
        $unidadesTransferir = $unidades;

       foreach($unidadesTransferir as $key => $unidade)
       {
           if($unidade['id'] == $lotacaoServidor)
           {
               unset($unidadesTransferir[$key]);
           }
       }

        //dd($unidades);

        // ----- HISTÓRICO TRANSFERÊNCIA INÍCIO -----
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $transferenciasHist = null;

        if(strpos($url,"?")){

            $transferenciasHist = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao', 'movimentacao_interno.fk_movimentacao','=','movimentacao.id')
                ->where('movimentacao.tipo', 'transferencia');

            if ($request->get('unidade_prisionalh'))
                $transferenciasHist = $transferenciasHist->where('movimentacao.unidade_destino', $request->get('unidade_prisionalh'));

            if ($request->get('pavilhaoh'))
                $transferenciasHist = $transferenciasHist->where('movimentacao.pavilhao_destino', $request->get('pavilhaoh'));

            if ($request->get('alah'))
                $transferenciasHist = $transferenciasHist->where('movimentacao.ala_destino', $request->get('alah'));

            if ($request->get('celah'))
                $transferenciasHist = $transferenciasHist->where('movimentacao.cela_destino', $request->get('celah'));

            if ($request->get('unidade_prisionalv'))
                $transferenciasHist = $transferenciasHist->where('movimentacao_interno.up_anterior', $request->get('unidade_prisionalv'));

            if ($request->get('pavilhaov'))
                $transferenciasHist = $transferenciasHist->where('movimentacao_interno.pv_anterior', $request->get('pavilhaov'));

            if ($request->get('alav'))
                $transferenciasHist = $transferenciasHist->where('movimentacao_interno.ala_anterior', $request->get('alav'));

            if ($request->get('celav'))
                $transferenciasHist = $transferenciasHist->where('movimentacao_interno.cela_anterior', $request->get('celav'));

            if ($request->get('datainicial'))
                $transferenciasHist = $transferenciasHist->where('movimentacao.data', '>=', Tdata::TdataMysql($request->get('datainicial')));

            if ($request->get('datafinal'))
                $transferenciasHist = $transferenciasHist->where('movimentacao.data', '<=', Tdata::TdataMysql($request->get('datafinal')));

            $transferenciasHist = $transferenciasHist->orderBy('movimentacao.data', 'DESC')->paginate(20)->appends($request->input());

            if(isset($btnPdf))
            {
                $transferenciaDados = $transferenciasHist;
            }

                       
        }
        // ----- HISTORICO TRANSFERÊNCIA FIM -----


        // $internosPendentes = [];

        // $movimentacoesInternosDados = DB::table('movimentacao_interno')
        //     ->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
        //     ->where('movimentacao_interno.status','Pendente')
        //     ->where('movimentacao.unidade_destino', $lotacaoServidor)
        //     ->where('movimentacao.tipo', 'transferencia')
        //     ->get();
        // foreach($movimentacoesInternosDados as $movimentacoesInternosDado)
        // {
        //     $dadosInternos = InternoController::getDadosPrincipaisInterno($movimentacoesInternosDado->fk_interno);
        //     array_push($internosPendentes, array(
        //         'id' => $movimentacoesInternosDado->fk_interno,
        //         'nome' => $dadosInternos[0]['nome'],
        //         'regime' => $dadosInternos[0]['regime'],
        //         'faccao_nome' => InternoController::getNomeFaccao($dadosInternos[0]['faccao']),
        //         'lideranca' => $dadosInternos[0]['lideranca'],
        //         'lotacao' => InternoController::getLotacaoInterno($movimentacoesInternosDado->fk_interno)
        //     ));

        // }

        // $internosPendentesEnv = [];

        // $movimentacoesInternosDados = DB::table('movimentacao_interno')
        //     ->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
        //     ->where('movimentacao_interno.status','Pendente')
        //     ->where('movimentacao_interno.up_anterior', $lotacaoServidor)
        //     ->where('movimentacao.tipo', 'transferencia')
        //     ->get();
        // foreach($movimentacoesInternosDados as $movimentacoesInternosDado)
        // {
        //     $dadosInternos = InternoController::getDadosPrincipaisInterno($movimentacoesInternosDado->fk_interno);
        //     array_push($internosPendentesEnv, array(
        //         'id' => $movimentacoesInternosDado->fk_interno,
        //         'nome' => $dadosInternos[0]['nome'],
        //         'destino' =>UnidadesController::getSiglaUp($movimentacoesInternosDado->unidade_destino),
        //         'data' =>Tdata::TdataBR(Tdata::getDataTimestamp($movimentacoesInternosDado->data)),
        //         'lotacao' => InternoController::getLotacaoInterno($movimentacoesInternosDado->fk_interno)
        //     ));

        // }

        // $internosAlocacao = DB::table('interno')
        //     ->select('id','nome','mae','faccao','lideranca')
        //     ->where('status','A')
        //     ->where('unidade_prisional', $lotacaoServidor)
        //     ->whereNull('cela')
        //     ->get();

        // $internosAlocacao = json_decode($internosAlocacao, true);
        // foreach($internosAlocacao as $key=>$iA){
        //     $internosAlocacao[$key]['faccao'] = InternoController::getNomeFaccao($iA['faccao']);
        //     if($iA['lideranca'] == 'S' || $iA['lideranca'] == 'Sim' || $iA['lideranca'] == 'SIM'){
        //         $internosAlocacao[$key]['lideranca'] = 'Sim';
        //     } else {
        //         $internosAlocacao[$key]['lideranca'] = 'Não';
        //     }
        // }

        if(isset($btnPdf))
        {
            //dd($transferenciaDados);
            $dadosTransferencia = $transferenciaDados;
            $nomeUnidadeOrigem = UnidadesController::getSiglaUp($request->get('unidade_prisionalv'));
            $nomeUnidadeDestino = UnidadesController::getSiglaUp($request->get('unidade_prisionalh'));
            $unidadeGeradora = UnidadesController::getNomeUp($lotacaoServidor);
            //dd($dadosTransferencia);

            $pdf = \PDF::loadView('movimentacoes.transferencia.pdfTransferencias', compact('dadosTransferencia','nomeUnidadeOrigem','nomeUnidadeDestino'))
                ->setOption('margin-bottom', 10)
                ->setOption('margin-top', 30)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('header-html', view('movimentacoes.transferencia.pdfHeaderTransferencias', compact('unidadeGeradora','nomeUnidadeOrigem','nomeUnidadeDestino')))
                ->setOption('footer-center', utf8_decode('Gerado em' . date('\ d/m/Y\ ') . ' | Pagina [page] de [topage]'))
                ->setOption('footer-font-size', 7)
                ->setPaper('a4');

            return $pdf->inline();

        }


        return view('movimentacoes.transferencia.show', compact('usuarioLogadoUP', 'unidades','unidadesTransferir', 'transferenciasHist','url'));
    }

    public function transferenciaTransferir()
    {
        // notificacao
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula');
        $insertNotificacaoLida = DB::table('notificacao_lida')->insert([
            'tipo' => 'alocacaoCela',
            'fk_servidor' => $matriculaServidorLogado[0]->matricula,
            'created_at' => Tdata::TdataHojeMysql(),
        ]);
        // --------------------------------
        
        
        $usuarioLogadoUP = DB::table('unidade')->where('sigla', utf8_encode(FacadeController::getLotacaoServidor(Auth::user()->cpf)))->get();
        $usuarioLogadoUP = json_decode($usuarioLogadoUP, true)[0];

        $unidades = DB::table('unidade')->where('status', 1)->orderBy('sigla', 'ASC')->get();

        $unidades = json_decode($unidades,true);
        $unidadesTransferir = $unidades;

       foreach($unidadesTransferir as $key => $unidade)
       {
           if($unidade['id'] == $lotacaoServidor)
           {
               unset($unidadesTransferir[$key]);
           }
       }

       $internosPendentesEnv = [];

       $internosPendentesCount = DB::table('movimentacao_interno')
       ->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
       ->where('movimentacao_interno.status','Pendente')
       ->where('movimentacao_interno.up_anterior', $lotacaoServidor)
       ->where('movimentacao.tipo', 'transferencia')
       ->count();

       $movimentacoesInternosDados = DB::table('movimentacao_interno')
           ->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
           ->where('movimentacao_interno.status','Pendente')
           ->where('movimentacao_interno.up_anterior', $lotacaoServidor)
           ->where('movimentacao.tipo', 'transferencia')
           ->orderBy('movimentacao.data', 'DESC')
           ->limit(5)
           ->get();
       foreach($movimentacoesInternosDados as $movimentacoesInternosDado)
       {
           $dadosInternos = InternoController::getDadosPrincipaisInterno($movimentacoesInternosDado->fk_interno);
           array_push($internosPendentesEnv, array(
               'id' => $movimentacoesInternosDado->fk_interno,
               'nome' => $dadosInternos[0]['nome'],
               'destino' =>UnidadesController::getSiglaUp($movimentacoesInternosDado->unidade_destino),
               'data' =>Tdata::TdataBR(Tdata::getDataTimestamp($movimentacoesInternosDado->data)),
               'lotacao' => InternoController::getLotacaoInterno($movimentacoesInternosDado->fk_interno)
           ));

       }


       if(isset($btnPdf))
       {
           //dd($transferenciaDados);
           $dadosTransferencia = $transferenciaDados;
           $nomeUnidadeOrigem = UnidadesController::getSiglaUp($request->get('unidade_prisionalv'));
           $nomeUnidadeDestino = UnidadesController::getSiglaUp($request->get('unidade_prisionalh'));
           $unidadeGeradora = UnidadesController::getNomeUp($lotacaoServidor);
           //dd($dadosTransferencia);

           $pdf = \PDF::loadView('movimentacoes.transferencia.pdfTransferencias', compact('dadosTransferencia','nomeUnidadeOrigem','nomeUnidadeDestino'))
               ->setOption('margin-bottom', 10)
               ->setOption('margin-top', 30)
               ->setOption('margin-left', 10)
               ->setOption('margin-right', 10)
               ->setOption('header-html', view('movimentacoes.transferencia.pdfHeaderTransferencias', compact('unidadeGeradora','nomeUnidadeOrigem','nomeUnidadeDestino')))
               ->setOption('footer-center', utf8_decode('Gerado em' . date('\ d/m/Y\ ') . ' | Pagina [page] de [topage]'))
               ->setOption('footer-font-size', 7)
               ->setPaper('a4');

           return $pdf->inline();

       }



        return view('movimentacoes.transferencia.includes.transferirInterno', compact('usuarioLogadoUP','unidadesTransferir','internosPendentesEnv','internosPendentesCount'));
    }

    public function transferenciaReceberInterno()
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula');
        $insertNotificacaoLida = DB::table('notificacao_lida')->insert([
            'tipo' => 'alocacaoCela',
            'fk_servidor' => $matriculaServidorLogado[0]->matricula,
            'created_at' => Tdata::TdataHojeMysql(),
        ]);

        $usuarioLogadoUP = DB::table('unidade')->where('sigla', utf8_encode(FacadeController::getLotacaoServidor(Auth::user()->cpf)))->get();
        $usuarioLogadoUP = json_decode($usuarioLogadoUP, true)[0];

        $internosPendentes = [];

            $movimentacoesInternosCount = DB::table('movimentacao_interno')
            ->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->where('movimentacao_interno.status','Pendente')
            ->where('movimentacao.unidade_destino', $lotacaoServidor)
            ->where('movimentacao.tipo', 'transferencia')
            ->orderBy('movimentacao.data', 'DESC')
            ->count();
            
            $movimentacoesInternosDados = DB::table('movimentacao_interno')
            ->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->where('movimentacao_interno.status','Pendente')
            ->where('movimentacao.unidade_destino', $lotacaoServidor)
            ->where('movimentacao.tipo', 'transferencia')
            ->orderBy('movimentacao.data', 'DESC')
            ->limit(5)
            ->get();
            
        foreach($movimentacoesInternosDados as $movimentacoesInternosDado)
        {
            $dadosInternos = InternoController::getDadosPrincipaisInterno($movimentacoesInternosDado->fk_interno);
            array_push($internosPendentes, array(
                'id' => $movimentacoesInternosDado->fk_interno,
                'nome' => $dadosInternos[0]['nome'],
                'regime' => $dadosInternos[0]['regime'],
                'faccao_nome' => InternoController::getNomeFaccao($dadosInternos[0]['faccao']),
                'lideranca' => $dadosInternos[0]['lideranca'],
                'lotacao' => InternoController::getLotacaoInterno($movimentacoesInternosDado->fk_interno)
            ));

        }

        $internosAlocacao = DB::table('interno')
        ->select('id','nome','mae','faccao','lideranca')
        ->where('status','A')
        ->where('unidade_prisional', $lotacaoServidor)
        ->whereNull('cela')
        ->orderBy('interno.nome', 'ASC')
        ->limit(5)
        ->get();

        $internosAlocacaoCount = DB::table('interno')
        ->select('id','nome','mae','faccao','lideranca')
        ->where('status','A')
        ->where('unidade_prisional', $lotacaoServidor)
        ->whereNull('cela')
        ->count();

        $internosAlocacao = json_decode($internosAlocacao, true);
        foreach($internosAlocacao as $key=>$iA){
            $internosAlocacao[$key]['faccao'] = InternoController::getNomeFaccao($iA['faccao']);
            if($iA['lideranca'] == 'S' || $iA['lideranca'] == 'Sim' || $iA['lideranca'] == 'SIM'){
                $internosAlocacao[$key]['lideranca'] = 'Sim';
            } else {
                $internosAlocacao[$key]['lideranca'] = 'Não';
            }
        }


        return view('movimentacoes.transferencia.includes.receberInterno', compact('internosPendentes','internosAlocacao','usuarioLogadoUP','movimentacoesInternosCount','internosAlocacaoCount'));

    }

    public function internosPendentesAlocacaoAjax(Request $request)
    {
        $skip = $request->get('skip');

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $internosAlocacao = DB::table('interno')
        ->select('id','nome','mae','faccao','lideranca')
        ->where('status','A')
        ->where('unidade_prisional', $lotacaoServidor)
        ->whereNull('cela')
        ->orderBy('interno.nome', 'ASC')
        ->skip($skip)
        ->take(5)    
        ->get();

        $internosAlocacao = json_decode($internosAlocacao, true);
        foreach($internosAlocacao as $key=>$iA){
            $internosAlocacao[$key]['faccao'] = InternoController::getNomeFaccao($iA['faccao']);
            if($iA['lideranca'] == 'S' || $iA['lideranca'] == 'Sim' || $iA['lideranca'] == 'SIM'){
                $internosAlocacao[$key]['lideranca'] = 'Sim';
            } else {
                $internosAlocacao[$key]['lideranca'] = 'Não';
            }
            $internosAlocacao[$key]['foto'] = InternoController::getFotoInterno($iA['id']);
        }


        return $internosAlocacao;

    }

    public function internosPendentesRecebimentoConfirmacaoAjax(Request $request)
    {
        $skip = $request->get('skip');

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        
        $internosPendentes = [];

        $movimentacoesInternosDados = DB::table('movimentacao_interno')
            ->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->where('movimentacao_interno.status','Pendente')
            ->where('movimentacao.unidade_destino', $lotacaoServidor)
            ->where('movimentacao.tipo', 'transferencia')
            ->orderBy('movimentacao.data', 'DESC')
            ->skip($skip)
            ->take(5)    
            ->get();

        foreach($movimentacoesInternosDados as $movimentacoesInternosDado)
        {
            $dadosInternos = InternoController::getDadosPrincipaisInterno($movimentacoesInternosDado->fk_interno);
            array_push($internosPendentes, array(
                'id' => $movimentacoesInternosDado->fk_interno,
                'nome' => $dadosInternos[0]['nome'],
                'regime' => $dadosInternos[0]['regime'],
                'faccao_nome' => InternoController::getNomeFaccao($dadosInternos[0]['faccao']),
                'lideranca' => $dadosInternos[0]['lideranca'],
                'foto' => InternoController::getFotoInterno($movimentacoesInternosDado->fk_interno),
                'lotacao' => InternoController::getLotacaoInterno($movimentacoesInternosDado->fk_interno)
            ));

        }

        return $internosPendentes;
        
    }
    public function internosPendentesConfirmacaoAjax(Request $request)
    {
        $skip = $request->get('skip');

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $internosPendentesEnv = [];

        $movimentacoesInternosDados = DB::table('movimentacao_interno')
        ->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
        ->where('movimentacao_interno.status','Pendente')
        ->where('movimentacao_interno.up_anterior', $lotacaoServidor)
        ->where('movimentacao.tipo', 'transferencia')
        ->orderBy('movimentacao.data', 'DESC')
        ->skip($skip)
        ->take(5)
        ->get();

        foreach($movimentacoesInternosDados as $movimentacoesInternosDado)
        {
            $dadosInternos = InternoController::getDadosPrincipaisInterno($movimentacoesInternosDado->fk_interno);
            array_push($internosPendentesEnv, array(
                'id' => $movimentacoesInternosDado->fk_interno,
                'nome' => $dadosInternos[0]['nome'],
                'destino' =>UnidadesController::getSiglaUp($movimentacoesInternosDado->unidade_destino),
                'data' =>Tdata::TdataBR(Tdata::getDataTimestamp($movimentacoesInternosDado->data)),
                'foto' => InternoController::getFotoInterno($movimentacoesInternosDado->fk_interno),
                'lotacao' => InternoController::getLotacaoInterno($movimentacoesInternosDado->fk_interno)
            ));
 
        }

        return $internosPendentesEnv;
 
 

    }

    public function storeTransferir(Request $request)
    {

        ini_set('max_execution_time', 20000);
        ini_set('memory_limit',-1);

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        
        
        $dataInicioPlantao =  OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor);
        $horarioInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
        $horarioTerminoPlantao = OcorrenciaController::getHorarioFechamento59Seg($lotacaoServidor);

        /**
         * validacoes
         */

        $dataValidar = $request->get('data');
        $horaValidar = $request->get('hora');

         if(Tdata::TdataMysql($dataValidar)." ".$horaValidar < $dataInicioPlantao." ".$horarioInicioPlantao || Tdata::TdataMysql($dataValidar)." ".$horaValidar > $dataTerminoPlantao." ".$horarioTerminoPlantao)
         {
             return redirect()->back()->withInput()->with('alert-danger', 'Você não pode inserir ocorrências em horários ou datas fora do plantão em aberto!');
         }
         
        if($dataValidar == null || $horaValidar == null)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Preencha os campos obrigatórios!');
        }


        $internos = "";
        foreach($request->get('internos') as $idInterno) {
            $qtdTransferencia = DB::table('movimentacao')->join('movimentacao_interno','movimentacao.id','=','movimentacao_interno.fk_movimentacao')->where('movimentacao_interno.fk_interno',$idInterno)->where('movimentacao_interno.status','Pendente')->count();
            if($qtdTransferencia > 0){
                $internos = $internos.InternoController::getInternoNome($idInterno)." (Prontuário: ".$idInterno."), ";
            }
        }

        if($internos != "") {
            return redirect()->back()->with('alert-danger', 'Operação não realizada, os internos a seguir possuem transferências em aberto, cancele as transferências em aberto antes de registrar outra. '.$internos);
        }

        // verifica se possui solicitacao de transferencia pendente
        $internosSolicitacaoPendente = "";
        foreach($request->get('internos') as $idInterno) {
            $qtdSolicitacao = DB::table('solicitacao_transferencia_interno')->where('fk_interno',$idInterno)->where('status','pendente')->count();
            if($qtdSolicitacao > 0){
                $internosSolicitacaoPendente = $internosSolicitacaoPendente."<br>".InternoController::getInternoNome($idInterno)." (Prontuário: ".$idInterno."), ";
            }
        }

        if($internosSolicitacaoPendente != "") {
            return redirect()->back()->with('alert-danger', 'Operação não realizada, os internos a seguir possuem solicitação de transferência em aberto, cancele as solicitações pendentes antes de realizar a transferência. '.$internosSolicitacaoPendente);
        }
        // // // // fim

        $created_atTransf = date("Y-m-d H:i:s");
    // insere os dados da Transferência
        $insertTransfencia = DB::table('movimentacao')->insert([
                'tipo' => 'transferencia',
                'motivo' => $request->get('motivo'),
                'unidade_destino' => $request->get('destino'),
                'data' => Tdata::TdataMysql($request->get('data'))." ".$request->get('hora').":00",
                'created_at' => $created_atTransf
            ]
        );

        $idTransferencia = DB::getPdo()->lastInsertId();

        $unidadeOrigem = "";
        $internosLog = "";
    // insere os presos da transferência na tabela correspondente
        foreach($request->get('internos') as $idInterno) {

            // -------------------- update na sancao --------------------------- //
            DB::table('sancao_triagem')->where('fk_interno',$idInterno)->update([
                'status' => 'I',
                'update_at' => Tdata::TtimeStampHojeMysql(),
            ]);
            // -------------------- !update na sancao --------------------------- //
            DB::table('movimentacao_interno')->insert([
                    'status' => 'Pendente',
                    'fk_movimentacao' => $idTransferencia,
                    'fk_interno' => $idInterno,
                    'up_anterior' => UnidadesController::getIdUP($idInterno),
                    'pv_anterior' => UnidadesController::getIdPavilhao($idInterno),
                    'ala_anterior' => UnidadesController::getIdAla($idInterno),
                    'cela_anterior' => UnidadesController::getIdCela($idInterno)
                ]
            );
            $unidadeOrigem = UnidadesController::getNomeUp(UnidadesController::getIdUP($idInterno));

            $internosLog = $internosLog."Prontuário: ".$idInterno.", ";
            //$internosLog = $internosLog."Prontuário: ".$idInterno." - ".$internosLog.InternoController::getInternoNome($idInterno).", ";
        }
        $internosLog = substr($internosLog,0,-2);

        $textoLog = "Registro de transferência. Registrada em ".Tdata::TtimestampBR($created_atTransf).". Origem: ".$unidadeOrigem.", Destino: ".UnidadesController::getNomeUp($request->get('destino')).". ";
        $textoLog = $textoLog."Internos: ".$internosLog.".";
        LogController::log($idTransferencia,"movimentacao","add", Auth::user()->cpf, $textoLog);

        // anexos
        if($request->file('documentos')[0] != null) {

            $qtdArquivo = count($request->file('documentos'));

            for ($x = 0; $x < $qtdArquivo; $x++) {

                $destinationPath = Constantes::ARQUIVOS_MOVIMENTACAO . "/transferencia/" . $idTransferencia;

                $name = "movimentacao".$idTransferencia . "_" . time() . "_" . $request->file('documentos')[$x]->getClientOriginalName();


                $request->file('documentos')[$x]->storeAs($destinationPath, $name);

                $insertArquivo = DB::table('movimentacao_arquivos')->insert(
                    [
                        'fk_movimentacao' => $idTransferencia,
                        'nome_arquivo' => $name,
                    ]
                );

            }
        }

        return redirect()->route('movimentacoes.transferenciaShow')->with('alert-success', 'Transferência registrada com sucesso.');
    }

    // Recebimento e cancelamento de recebimento de interno
    public function receberInterno(Request $request){



        $errorTriagem = "";

        if ($request->buttonRec == 'Confirmar') {
            $idsInternos = $request->get('internosReceberCheck');
            $internosRecebidosLog = "";
            $insertTransfencia = DB::table('movimentacao')->insert([
                    'tipo' => 'transferenciaentrada',
                    'motivo' => "Recebimento de interno após transferência de unidade.",
                    'unidade_destino' => UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf)),
                    'data' => Tdata::TdataMysql($request->get('data_entrada_r'))." ".$request->get('hora_entrada_r').":00",
                    'created_at' => Tdata::TtimeStampHojeMysql()
                ]
            );
            $idRecebimento = DB::getPdo()->lastInsertId();

            if($idsInternos){
                foreach($idsInternos as $id){

                    DB::table('movimentacao_interno')->insert([
                            'status' => 'Finalizada',
                            'fk_movimentacao' => $idRecebimento,
                            'fk_interno' => $id,
                            'up_anterior' => UnidadesController::getIdUP($id),
                            'pv_anterior' => UnidadesController::getIdPavilhao($id),
                            'ala_anterior' => UnidadesController::getIdAla($id),
                            'cela_anterior' => UnidadesController::getIdCela($id),
                            'obs' => $request->obs,
                        ]
                    );

                    // ------------------------------------ registra a triagem -------------------------------------------------
                    // $dataHj = Tdata::TdataHojeMysql();
                    $dataInicio = Tdata::TdataMysql($request->get('data_entrada_r'));
                    $diasAdd = $request->get('diasAdd');
                    if($diasAdd > 0){
                        $dataFim = Tdata::addDias($dataInicio, $diasAdd);
                        $anoDataFim = Tdata::getAnoData($dataFim);
                        if($anoDataFim >= date('Y')){
                            $idTransferencia = DB::table('movimentacao')->join('movimentacao_interno','movimentacao.id','=','movimentacao_interno.fk_movimentacao')->where('movimentacao_interno.fk_interno',$id)->where('movimentacao.tipo','transferencia')->where('movimentacao_interno.status','Pendente')->get();
                            $idTransferencia = json_decode($idTransferencia,true)[0]['fk_movimentacao'];
                            DB::table('sancao_triagem')->insert([
                                'tipo' => "triagem",
                                'unidade' => UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf)),
                                'fk_movimentacao' => $idTransferencia,
                                'fk_interno' => $id,
                                'data_fim' => $dataFim,
                                'status' => 'A',
                                'motivo' => "Triagem (recebimento de interno por transferência de unidade prisional).",
                                'created_at' => Tdata::TtimeStampHojeMysql(),
                            ]);
                        }   
                    }   

                    // ------------------------------------ !registra a sancao ou triagem ------------------------------------------------


                    $internosRecebidosLog = $internosRecebidosLog."Prontuário: ".$id." - ".InternoController::getInternoNome($id)."(Lotação anterior: ".InternoController::getLotacaoInterno($id)."), ";
                    DB::table('interno')->where('id',$id)->update(
                        [
                            'unidade_prisional'=>UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf)),
                            'pavilhao' => null,
                            'ala' => null,
                            'cela' => null,
                            'updated_at' => Tdata::TtimeStampHojeMysql(),
                        ]);

                    DB::table('movimentacao_interno')->where('fk_interno',$id)->update(
                        [
                            'status'=>'Finalizada'
                        ]);

                }
            }

            $textoLog = "Recebimento de interno por transferência de unidade. Registrado em ".Tdata::TtimeStampHojeBr().", Unidade Recebedora: ".FacadeController::getLotacaoServidor(Auth::user()->cpf).", Internos Recebidos: ".$internosRecebidosLog;
            LogController::log($idRecebimento,"movimentacao","add", Auth::user()->cpf, $textoLog);

            return redirect()->route('movimentacoes.transferenciaShow')->with('alert-success', 'Internos selecionados recebidos com sucesso.');
        }
        else if ($request->buttonRec == 'Cancelar') {
            $idsInternos = $request->get('internosReceberCheck');

            if($idsInternos) {
                foreach ($idsInternos as $id) {
                    $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula')[0]->matricula;
                    $nomeServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'nome')[0]->nome;
                    $unidadeServidorLogado = FacadeController::getLotacaoServidor(Auth::user()->cpf);
                    $nomeInterno = InternoController::getInternoNome($id);
                    $idLotacaoInterno = InternoController::getIdUnidadeInterno($id);
                    $siglaLotacaoInterno = UnidadesController::getSiglaUp($idLotacaoInterno);
                    // Altera o status para Cancelada
                        $idMovInt = DB::table('movimentacao_interno')->join('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
                            ->select('movimentacao_interno.id')
                            ->where('movimentacao_interno.fk_interno',$id)
                            ->where('movimentacao_interno.status','Pendente')
                            ->where('movimentacao.tipo','transferencia')
                            ->get()[0]->id;
                        DB::table('movimentacao_interno')
                            ->where('id',$idMovInt)
                            ->update([
                                'status' => 'Cancelada',
                                'obs' => $request->obs,

                                ]
                        );

                    // LOG Alteração
                    $textoLog = "Cancelamento de recebimento de interno pós solicitação de transferência de unidade. Registrado em ".Tdata::TtimeStampHojeBr().", cancelado por ".$nomeServidorLogado." (Matrícula ".$matriculaServidorLogado.", CPF ".Auth::user()->cpf."). O cancelamento foi realizado pela unidade de Destino: ".$unidadeServidorLogado.", a unidade de Origem era: ".$siglaLotacaoInterno.", o(a) interno(a) cuja transferência foi cancelada é o(a) ".$nomeInterno." (Prontuário ".$id.")<br>Observação: ".$request->obs.".";
                    LogController::log($idMovInt,"movimentacao_interno","update", Auth::user()->cpf, $textoLog);

                    // Envia mensagem para a unidade que enviou o interno
                        $assunto = "Cancelamento de Transferência de Interno";
                        $descricao = "O(A) servidor(a) ".$nomeServidorLogado." (matrícula ".$matriculaServidorLogado."), lotado na unidade ".$unidadeServidorLogado.", cancelou o recebimento do interno ".$nomeInterno." (prontuário ".$id.")<br>Observação: ".$request->obs.".<br> Este interno permanece lotado na unidade ".$siglaLotacaoInterno." no {{ config('app.name') }}. Você já está liberado para realizar uma nova transferência envolvendo este interno.";

                        $insertMensagem = DB::table('mensagem')->insert([
                                'remetente' => $matriculaServidorLogado,
                                'assunto' => $assunto,
                                'mensagem' => $descricao,
                                'created_at' => date("Y-m-d"),
                                'hora' => date("H:i"),
                            ]
                        );
                        $idMensagem = DB::getPdo()->lastInsertId();
                        $insertDestinatario = DB::table('mensagem_destinatarios')->insert([
                                'fk_mensagem' => $idMensagem,
                                'tipo' => 'unidade',
                                'destinatario' => $idLotacaoInterno,
                                'created_at' => date("Y-m-d"),
                                'hora' => date("H:i"),
                            ]
                        );

                    // LOG Mensagem
                        $textoLog = "Log de envio da mensagem de cancelamento de recebimento de interno pós solicitação de transferência de unidade, o id da movimentacao_interno foi ".$idMovInt;
                        LogController::log($idMensagem,"mensagem","add", Auth::user()->cpf, $textoLog);
                }
            }

            return redirect()->route('movimentacoes.transferenciaShow')->with('alert-success', 'Os recebimentos dos internos selecionados foram cancelados e as unidades que enviaram os internos foram informadas via mensagens do '.config('app.name').'.');
        }
    }

    public function cancelarTransfEnv(Request $request){
        $idsInternos = $request->get('internosCancelarTransfEnvCheck');

        if($idsInternos) {
            foreach ($idsInternos as $id) {
                $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula')[0]->matricula;
                $nomeServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'nome')[0]->nome;
                $unidadeServidorLogado = FacadeController::getLotacaoServidor(Auth::user()->cpf);
                $nomeInterno = InternoController::getInternoNome($id);
                $idLotacaoInterno = InternoController::getIdUnidadeInterno($id);
                $siglaLotacaoInterno = UnidadesController::getSiglaUp($idLotacaoInterno);

                // Altera o status para Cancelada
                $temp = DB::table('movimentacao_interno')->join('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
                    ->select('movimentacao_interno.id','movimentacao.unidade_destino')
                    ->where('movimentacao_interno.fk_interno',$id)
                    ->where('movimentacao_interno.status','Pendente')
                    ->where('movimentacao.tipo','transferencia')
                    ->get()[0];
                $idMovInt = $temp->id;
                $idUpDestino = $temp->unidade_destino;
                $siglaUpDestino = UnidadesController::getSiglaUp($idUpDestino);
                DB::table('movimentacao_interno')
                    ->where('id',$idMovInt)
                    ->update([
                            'status' => 'Cancelada',
                            'obs' => $request->obs
                        ]
                    );

                // LOG Alteração
                $textoLog = "Cancelamento de transferência de interno. Registrado em ".Tdata::TtimeStampHojeBr().", cancelado por ".$nomeServidorLogado." (Matrícula ".$matriculaServidorLogado.", CPF ".Auth::user()->cpf."). O cancelamento foi realizado pela unidade de Origem: ".$unidadeServidorLogado.", a unidade de Destino era: ".$siglaUpDestino.", o(a) interno(a) cuja transferência foi cancelada é o(a) ".$nomeInterno." (Prontuário ".$id.").<br>Observação".$request->obs."";
                LogController::log($idMovInt,"movimentacao_interno","update", Auth::user()->cpf, $textoLog);

                // Envia mensagem para a unidade que enviou o interno
                $assunto = "Cancelamento de Transferência de Interno";
                $descricao = "O(A) servidor(a) ".$nomeServidorLogado." (matrícula ".$matriculaServidorLogado."), lotado na unidade ".$unidadeServidorLogado.", cancelou a transferência do(a) interno(a) ".$nomeInterno." (prontuário ".$id.").<br>Observação".$request->obs."<br>Este(a) interno(a) permanece lotado(a) na unidade ".$siglaLotacaoInterno." no ".config('app.name').".";

                $insertMensagem = DB::table('mensagem')->insert([
                        'remetente' => $matriculaServidorLogado,
                        'assunto' => $assunto,
                        'mensagem' => $descricao,
                        'created_at' => date("Y-m-d"),
                        'hora' => date("H:i"),
                    ]
                );
                $idMensagem = DB::getPdo()->lastInsertId();
                $insertDestinatario = DB::table('mensagem_destinatarios')->insert([
                        'fk_mensagem' => $idMensagem,
                        'tipo' => 'unidade',
                        'destinatario' => $idUpDestino,
                        'created_at' => date("Y-m-d"),
                        'hora' => date("H:i"),
                    ]
                );

                // LOG Mensagem
                $textoLog = "Log de envio da mensagem de cancelamento de transferência de interno, o id da movimentacao_interno foi ".$idMovInt;
                LogController::log($idMensagem,"mensagem","add", Auth::user()->cpf, $textoLog);
            }
        }

        return redirect()->route('movimentacoes.transferenciaShow')->with('alert-success', 'As transferências dos internos selecionados foram canceladas e as unidades de destino foram informadas via mensagens do '.config('app.name').'.');
    }

    public function alocarInterno(Request $request){
        $idsInternos = $request->get('internosCelaCheck');
        $pavilhao = $request->get('pavilhaoAlocacao');
        $ala = $request->get('alaAlocacao');
        $cela = $request->get('celaAlocacao');
        $internosAlocadosLog = "";
        if(!is_null($idsInternos)){
            foreach($idsInternos as $id){
                $internosAlocadosLog = $internosAlocadosLog."Prontuário: ".$id." - ".InternoController::getInternoNome($id).", ";
                DB::table('interno')->where('id',$id)->update(
                    [
                        'pavilhao' => $pavilhao,
                        'ala' => $ala,
                        'cela' => $cela,
                        'updated_at' => Tdata::TtimeStampHojeMysql(),
                    ]);
            }
        }

        $textoLog = "Alocação de cela registrada em ".Tdata::TtimeStampHojeBr().". Alocado em: ".FacadeController::getLotacaoServidor(Auth::user()->cpf)." - ".UnidadesController::getNomePav($pavilhao)." - ".UnidadesController::getNomeAla($ala)." - ".UnidadesController::getNomeCela($cela).", Internos Recebidos: ".$internosAlocadosLog;
        LogController::log(0,"movimentacao","add", Auth::user()->cpf, $textoLog);

        return redirect()->route('movimentacoes.transferenciaShow')->with('alert-success', 'Internos alocados nas celas com sucesso.');
    }

    public function transferenciaProgressao()
    {

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula');

        $unidades = DB::table('unidade')->where('status', 1)->orderBy('sigla', 'ASC')->get();

        $unidades = json_decode($unidades,true);
        $unidadesTransferir = $unidades;

        foreach($unidadesTransferir as $key => $unidade)
        {
            if($unidade['id'] == $lotacaoServidor)
            {
                unset($unidadesTransferir[$key]);
            }
        }

        $usuarioLogadoUP = DB::table('unidade')->where('sigla', FacadeController::getLotacaoServidor(Auth::user()->cpf))->get();
        $usuarioLogadoUP = json_decode($usuarioLogadoUP, true)[0];


        return view('movimentacoes.transferencia.includes.transferenciaProgressao', compact('unidadesTransferir','lotacaoServidor','usuarioLogadoUP'));

    }

    public function transferenciaProgressaoStore(Request $request)
    {
        //dd($request->all());

        ini_set('max_execution_time', 20000);
        ini_set('memory_limit',-1);


        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

//        $getFechamento = DB::table('ocorrencia_fechamento_livro')->where('unidade', $lotacaoServidor)->orderBy('data', 'DESC')->first();


        /**
         * validacoes
         */

        $dataValidar = $request->get('data');
        $horaValidar = $request->get('hora');

        if(Tdata::TdataMysql($dataValidar)." ".$horaValidar > Tdata::TtimeStampHojeMysql())
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Você não pode registrar em datas futuras.');
        }
        if($dataValidar == null ||$horaValidar == null)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Preencha os campos obrigatórios!');
        }

        $internosId = $request->get('internos');
        foreach ($internosId as $id) {
            $dadosInterno = InternoController::getDadosInterno($id);
            if(isset($dadosInterno[0])){
                if($dadosInterno[0]->sentenciado == 'N'){
                    return redirect()->back()->withInput()->with('alert-danger', 'Existem internos que não são sentenciados! Para isto use o menu Sentenciado ou Provisório em Movimentações.');
                }
            }
        }

        $internos = "";
        foreach($request->get('internos') as $idInterno) {
            $qtdTransferencia = DB::table('movimentacao')->join('movimentacao_interno','movimentacao.id','=','movimentacao_interno.fk_movimentacao')->where('movimentacao_interno.fk_interno',$idInterno)->where('movimentacao_interno.status','Pendente')->count();
            if($qtdTransferencia > 0){
                $internos = $internos.InternoController::getInternoNome($idInterno)." (Prontuário: ".$idInterno."), ";
            }
        }

        if($internos != "") {
            return redirect()->back()->with('alert-danger', 'Operação não realizada, os internos a seguir possuem transferências em aberto, cancele as transferências em aberto antes de registrar outra. '.$internos);
        }

        // verifica se possui solicitacao de transferencia pendente
        $internosSolicitacaoPendente = "";
        foreach($request->get('internos') as $idInterno) {
            $qtdSolicitacao = DB::table('solicitacao_transferencia_interno')->where('fk_interno',$idInterno)->where('status','pendente')->count();
            if($qtdSolicitacao > 0){
                $internosSolicitacaoPendente = $internosSolicitacaoPendente."<br>".InternoController::getInternoNome($idInterno)." (Prontuário: ".$idInterno."), ";
            }
        }

        if($internosSolicitacaoPendente != "") {
            return redirect()->back()->with('alert-danger', 'Operação não realizada, os internos a seguir possuem solicitação de transferência em aberto, cancele as solicitações pendentes antes de realizar a transferência. '.$internosSolicitacaoPendente);
        }
        // // // // fim



        $created_AtProgRegr = date("Y-m-d H:i:s");
        $insertProgressaoRegressao = DB::table('movimentacao')->insert([
                'tipo' => $request->get('tipo'),
                'unidade_destino' => $request->get('unidade_prisionalAlocacao'),
                'pavilhao_destino' => $request->get('pavilhaoAlocacao'),
                'ala_destino' => $request->get('alaAlocacao'),
                'cela_destino' => $request->get('celaAlocacao'),
                'motivo' => $request->get('motivoPr'),
                'data' => Tdata::TdataMysql($request->get('dataPr'))." ".$request->get('hora').":00",
                'created_at' => $created_AtProgRegr
            ]
        );
        if($insertProgressaoRegressao){
            $idProgressaoRegressao = DB::getPdo()->lastInsertId();

            if($request->get('fimsancao') == "Sim"){
                DB::table('movimentacao')->where('id',$idProgressaoRegressao)->update([
                    'motivo' => "Registro, por meio deste, o fim da sanção disciplinar com cumprimento de pena em regime mais gravoso.".$request->get('motivo'),
                ]);
            }

            $internosLogProgRegr = "";
            foreach ($internosId as $id) {
                DB::table('movimentacao_interno')->insert([
                        'status' => 'Finalizada',
                        'fk_movimentacao' => $idProgressaoRegressao,
                        'fk_interno' => $id,
                        'up_anterior' => UnidadesController::getIdUP($id),
                        'pv_anterior' => UnidadesController::getIdPavilhao($id),
                        'ala_anterior' => UnidadesController::getIdAla($id),
                        'cela_anterior' => UnidadesController::getIdCela($id)
                    ]
                );
                $internosLogProgRegr = $internosLogProgRegr."Prontuário: ".$id." - ".InternoController::getInternoNome($id).", ";
                $idUpProgRegr = UnidadesController::getIdUP($id);
            }
            $textoLog = "";
            if($request->get('tipo') == "progressaosemiaberto"){
                $textoLog = "Progressão para o semiaberto realizada. Registrada em ".Tdata::TtimestampBR($created_AtProgRegr).". Origem: ".UnidadesController::getNomeUp($idUpProgRegr).". Destino :".UnidadesController::getNomeUp($request->get('unidade_prisionalAlocacao'))." - ".UnidadesController::getNomePav($request->get('pavilhaoAlocacao'))." - ".UnidadesController::getNomeAla($request->get('alaAlocacao'))." - ".UnidadesController::getNomeCela($request->get('celaAlocacao'))." Internos: ";
            }
            else if ($request->get('tipo') == "progressaoaberto"){
                $textoLog = "Progressão para o aberto realizada. Registrada em ".Tdata::TtimestampBR($created_AtProgRegr).". Origem: ".UnidadesController::getNomeUp(UnidadesController::getIdUP($id)).". Destino :".UnidadesController::getNomeUp($request->get('unidade_prisionalAlocacao'))." - ".UnidadesController::getNomePav($request->get('pavilhaoAlocacao'))." - ".UnidadesController::getNomeAla($request->get('alaAlocacao'))." - ".UnidadesController::getNomeCela($request->get('celaAlocacao'))." Internos: ";
            }
            else if ($request->get('tipo') == "regressaosemiaberto"){
                $textoLog = "Regressão para o semiaberto realizada. Registrada em ".Tdata::TtimestampBR($created_AtProgRegr).". Origem: ".UnidadesController::getNomeUp(UnidadesController::getIdUP($id)).". Destino :".UnidadesController::getNomeUp($request->get('unidade_prisionalAlocacao'))." - ".UnidadesController::getNomePav($request->get('pavilhaoAlocacao'))." - ".UnidadesController::getNomeAla($request->get('alaAlocacao'))." - ".UnidadesController::getNomeCela($request->get('celaAlocacao'))." Internos: ";
            }
            else if ($request->get('tipo') == "regressaofechado"){
                $textoLog = "Regressão para o fechado realizada. Registrada em ".Tdata::TtimestampBR($created_AtProgRegr).". Origem: ".UnidadesController::getNomeUp(UnidadesController::getIdUP($id)).". Destino :".UnidadesController::getNomeUp($request->get('unidade_prisionalAlocacao'))." - ".UnidadesController::getNomePav($request->get('pavilhaoAlocacao'))." - ".UnidadesController::getNomeAla($request->get('alaAlocacao'))." - ".UnidadesController::getNomeCela($request->get('celaAlocacao'))." Internos: ";
            }
            $textoLog = $textoLog.substr($internosLogProgRegr,0,-2).".";
            LogController::log($idProgressaoRegressao,"movimentacao","add", Auth::user()->cpf, $textoLog);

            // anexos
            if ($request->file('documentosPr')[0] != null) {

                $qtdArquivo = count($request->file('documentosPr'));

                for ($x = 0; $x < $qtdArquivo; $x++) {

                    $destinationPath = Constantes::ARQUIVOS_MOVIMENTACAO . "/progressaoregressao/" . $idProgressaoRegressao;

                    $name = "movimentacao" . $idProgressaoRegressao . "_" . time() . "_" . $request->file('documentosPr')[$x]->getClientOriginalName();

                    $request->file('documentosPr')[$x]->storeAs($destinationPath, $name);

                    $insertArquivo = DB::table('movimentacao_arquivos')->insert(
                        [
                            'fk_movimentacao' => $idProgressaoRegressao,
                            'nome_arquivo' => $name,
                        ]
                    );

                }
            }

            // REGISTRA SAÍDA JUDICIAL SE O PRESO FOR ASSINAR EXTERNAMENTE
            $created_SaidaJudicial = date("Y-m-d H:i:s");
            if($request->get('assinaunidade') == "Não") {
                DB::table('movimentacao')->insert([
                        'tipo' => 'saidajudicial',
                        'motivo' => 'Saída judicial registrada automaticamente através do registro da progressão de regime em que o interno não assinará na unidade em que ele estava lotado.',
                        'data' => Tdata::TdataMysql($request->get('data')) . " " . $request->get('hora') . ":59",
                        'created_at' => $created_SaidaJudicial
                    ]
                );
                $idSaidaJudicial = DB::getPdo()->lastInsertId();
            }

            foreach ($internosId as $id) {
                $regime = "";
                if($request->get('tipo') == "progressaosemiaberto"){
                    $regime = "SEMIABERTO";
                } else if($request->get('tipo') == "progressaoaberto"){
                    $regime = "ABERTO";
                } else if($request->get('tipo') == "regressaofechado"){
                    $regime = "FECHADO";
                    // -------------------- cadastro na sancao --------------------------- //
                    if($request->get('dias') != 0){
                        $dataHj = Tdata::TdataHojeMysql();
                        $dataFimTriagem = Tdata::addDias($dataHj, $request->get('dias'));

                        DB::table('sancao_triagem')->insert([
                            'tipo' => 'sancao',
                            'unidade' => UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf)),
                            'fk_movimentacao' => $idProgressaoRegressao,
                            'fk_interno' => $id,
                            'data_fim' => $dataFimTriagem,
                            'status' => 'A',
                            'motivo' => 'Sanção disciplinar com cumprimento em regime mais gravoso.',
                            'regressao_temporaria' => 'S',
                            'created_at' => Tdata::TtimeStampHojeMysql(),
                        ]);
                    }
                    // -------------------- !cadastro na sancao --------------------------- //
                } else if($request->get('tipo') == "regressaosemiaberto"){
                    $regime = "SEMIABERTO";
                    // -------------------- cadastro na sancao --------------------------- //
                    if($request->get('dias') != 0){
                        $dataHj = Tdata::TdataHojeMysql();
                        $dataFimTriagem = Tdata::addDias($dataHj, $request->get('dias'));

                        DB::table('sancao_triagem')->insert([
                            'tipo' => 'sancao',
                            'unidade' => UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf)),
                            'fk_movimentacao' => $idProgressaoRegressao,
                            'fk_interno' => $id,
                            'data_fim' => $dataFimTriagem,
                            'status' => 'A',
                            'motivo' => 'Sanção disciplinar com cumprimento em regime mais gravoso.',
                            'regressao_temporaria' => 'S',
                            'created_at' => Tdata::TtimeStampHojeMysql(),
                        ]);
                    }
                    // -------------------- !cadastro na sancao --------------------------- //
                }
                $status = "";
                if($request->get('assinaunidade') == "Sim"){
                    $status = "A";
                } else if($request->get('assinaunidade') == "Não") {
                    $status = "I";

                    // REGISTRA INTERNO NA SAIDAJUDICIAL
                    DB::table('movimentacao_interno')->insert([
                            'status' => 'Finalizada',
                            'fk_movimentacao' => $idSaidaJudicial,
                            'fk_interno' => $id,
                            'up_anterior' => UnidadesController::getIdUP($id),
                            'pv_anterior' => UnidadesController::getIdPavilhao($id),
                            'ala_anterior' => UnidadesController::getIdAla($id),
                            'cela_anterior' => UnidadesController::getIdCela($id)
                        ]
                    );
                }


                DB::table('interno')->where('id', $id)->update(
                    [
                        'saidasexternas' => $request->get('saidasexternas'),
                        'status' => $status,
                        'updated_at' => Tdata::TtimeStampHojeMysql(),
                    ]
                );

                if($request->get('assinaunidade') == "Sim"){
                    DB::table('interno')->where('id', $id)->update(
                        [
                            'pavilhao' => $request->get('pavilhaoAlocacao'),
                            'ala' => $request->get('alaAlocacao'),
                            'cela' => $request->get('celaAlocacao'),
                            'updated_at' => Tdata::TtimeStampHojeMysql(),
                        ]
                    );
                }

                if($request->get('temporaria') == "Não"){
                    DB::table('interno')->where('id', $id)->update(
                        [
                            'regime' => $regime,
                            'updated_at' => Tdata::TtimeStampHojeMysql(),
                        ]
                    );
                }

                if($status == "I"){
                    DB::table('interno')->where('id', $id)->update(
                        [
                            'condicao_interno' => 646,
                            'updated_at' => Tdata::TtimeStampHojeMysql(),
                        ]
                    );
                }
                else if($status == "A"){
                    DB::table('interno')->where('id', $id)->update(
                        [
                            'condicao_interno' => 644,
                            'updated_at' => Tdata::TtimeStampHojeMysql(),
                        ]
                    );
                }

            }


            /**
             * inicio da transferencia
             */
            $created_atTransf = date("Y-m-d H:i:s");
            // insere os dados da Transferência
            $insertTransfencia = DB::table('movimentacao')->insert([
                    'tipo' => 'transferencia',
                    'motivo' => $request->get('motivo'),
                    'unidade_destino' => $request->get('destino'),
                    'data' => Tdata::TdataMysql($request->get('data'))." ".$request->get('hora').":00",
                    'created_at' => $created_atTransf
                ]
            );

            $idTransferencia = DB::getPdo()->lastInsertId();

            $unidadeOrigem = "";
            $internosLog = "";
            $textoLog = "";
            // insere os presos da transferência na tabela correspondente
            foreach($request->get('internos') as $idInterno) {

                // -------------------- update na sancao --------------------------- //
                DB::table('sancao_triagem')->where('fk_interno',$idInterno)->update([
                    'status' => 'I',
                    'update_at' => Tdata::TtimeStampHojeMysql(),
                ]);
                // -------------------- !update na sancao --------------------------- //
                DB::table('movimentacao_interno')->insert([
                        'status' => 'Pendente',
                        'fk_movimentacao' => $idTransferencia,
                        'fk_interno' => $idInterno,
                        'up_anterior' => UnidadesController::getIdUP($idInterno),
                        'pv_anterior' => UnidadesController::getIdPavilhao($idInterno),
                        'ala_anterior' => UnidadesController::getIdAla($idInterno),
                        'cela_anterior' => UnidadesController::getIdCela($idInterno)
                    ]
                );
                $unidadeOrigem = UnidadesController::getNomeUp(UnidadesController::getIdUP($idInterno));

                $internosLog = $internosLog."Prontuário: ".$idInterno.", ";
                //$internosLog = $internosLog."Prontuário: ".$idInterno." - ".$internosLog.InternoController::getInternoNome($idInterno).", ";
            }
            $internosLog = substr($internosLog,0,-2);

            $textoLog = "Registro de transferência. Registrada em ".Tdata::TtimestampBR($created_atTransf).". Origem: ".$unidadeOrigem.", Destino: ".UnidadesController::getNomeUp($request->get('destino')).". ";
            $textoLog = $textoLog."Internos: ".$internosLog.".";
            LogController::log($idTransferencia,"movimentacao","add", Auth::user()->cpf, $textoLog);

            // anexos
            if($request->file('documentos')[0] != null) {

                $qtdArquivo = count($request->file('documentos'));

                for ($x = 0; $x < $qtdArquivo; $x++) {

                    $destinationPath = Constantes::ARQUIVOS_MOVIMENTACAO . "/transferencia/" . $idTransferencia;

                    $name = "movimentacao".$idTransferencia . "_" . time() . "_" . $request->file('documentos')[$x]->getClientOriginalName();


                    $request->file('documentos')[$x]->storeAs($destinationPath, $name);

                    $insertArquivo = DB::table('movimentacao_arquivos')->insert(
                        [
                            'fk_movimentacao' => $idTransferencia,
                            'nome_arquivo' => $name,
                        ]
                    );

                }
            }

            return redirect()->route('movimentacoes.progressaoregressaoShow')->with('alert-success', 'Operação realizada com sucesso.');
        }
        else{
            return redirect()->route('movimentacoes.progressaoregressaoShow')->with('alert-danger', 'Ocorreu um erro e a operação não foi realizada, entre em contato com a '.config('app.ti').'.');
        }

    }

}
