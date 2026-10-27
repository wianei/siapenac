<?php


/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a solicitacao de internos por unidade prisional diversa
|
*/

namespace App\Http\Controllers\Solicitacao;

use App\Http\Controllers\Advogado\AdvogadoController;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Interno\InternoController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Permissoes\PermissoesController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Interno;
use App\Visita;
use App\Contato;
use App\Endereco;
use App\Classes\Constantes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Classes\GeocodingMaps;
use App\Classes\Tdata;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SolicitacaoInternoController extends Controller
{
    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');

        $this->middleware('auth');

    }

    public function callAction($method, $parameters)
    {

        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::verifyPermission("SolicitacaoInternoController", $method);
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }
        
//        $metodosAdm = collect(['solicitar']);
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

    public function solicitar()
    {
        $usuarioLogadoUP = DB::table('unidade')->where('sigla', FacadeController::getLotacaoServidor(Auth::user()->cpf))->get();
        $usuarioLogadoUP = json_decode($usuarioLogadoUP, true)[0];

        return view('movimentacoes.solicitacaoInterno.solicitarInterno', compact('usuarioLogadoUP'));
    }

    public function solicitarStore(Request $request)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $pavilhaoSolicitante = $request->get('pavilhao');
        $alaSolicitante = $request->get('ala');
        $celaSolicitante = $request->get('cela');
        $internosId = $request->get('internos');
        $motivo = $request->get('motivo');
        $dataEntrada = $request->get('dataEntrada');
        $horaEntrada = $request->get('hora');

        if(count($internosId) <= 1)
        {
            $verificaInternoPendente = DB::table("solicitacao_transferencia_interno")->where('status',"Pendente")->where('fk_interno', $internosId[0])->count();
            if($verificaInternoPendente > 0)
            {
                return redirect()->back()->with('alert-danger', "O interno(a) ".InternoController::getInternoNome($internosId[0])." possui uma solicitação pendente, por isso não é possível solicitar sua transferência.");

            }
            if(InternoController::getIdUnidadeInterno($internosId[0]) == $lotacaoServidor) {
                return redirect()->back()->with('alert-danger', "O interno(a) ".InternoController::getInternoNome($internosId[0])." está localizado na unidade solicitante!");

            }

            $qtdTransferencia = DB::table('movimentacao')->join('movimentacao_interno','movimentacao.id','=','movimentacao_interno.fk_movimentacao')->where('movimentacao_interno.fk_interno',$internosId[0])->where('movimentacao_interno.status','Pendente')->count();
            if($qtdTransferencia > 0){
                return redirect()->back()->with('alert-danger', "O interno(a) ".InternoController::getInternoNome($internosId[0])." possui uma transferência em aberto para outra unidade prisional.");

            }


        }

        $insertSolicitacao = DB::table('solicitacao_transferencia')->insert([
            'unidade_solicitante' => $lotacaoServidor,
            'pavilhao_solicitante' => $pavilhaoSolicitante,
            'ala_solicitante' => $alaSolicitante,
            'cela_solicitante' => $celaSolicitante,
            'data_entrada_interno' => Tdata::TdataMysql($dataEntrada)." ".$horaEntrada,
            'created_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        $idSolicitacao = DB::getPdo()->lastInsertId();

        $solicitacoesDuplicadas = "";
        $transferenciaEmAberto = "";
        $internosMesmaUnidade = "";
        if($insertSolicitacao) {

            $internosLog = "Solicitação de Interno - Unidade: ".UnidadesController::getSiglaUp($lotacaoServidor)."<br>";
            $textoLog = "";

            foreach ($internosId as $id) {
                $nomeInterno = InternoController::getInternoNome($id);

                $verificaSolicitacaoPendente = DB::table("solicitacao_transferencia_interno")->where('status',"Pendente")->where('fk_interno', $id)->count();
                $qtdTransferencia = DB::table('movimentacao')->join('movimentacao_interno','movimentacao.id','=','movimentacao_interno.fk_movimentacao')->where('movimentacao_interno.fk_interno',$id)->where('movimentacao_interno.status','Pendente')->count();

                if($verificaSolicitacaoPendente > 0){
                    $solicitacoesDuplicadas = $solicitacoesDuplicadas.$nomeInterno.", ";
                }
                else {
                    if ($qtdTransferencia > 0) {
                        $transferenciaEmAberto = $transferenciaEmAberto . $nomeInterno . ", ";
                    } else {

                        if (InternoController::getIdUnidadeInterno($id) != $lotacaoServidor) {

                            DB::table('solicitacao_transferencia_interno')->insert([
                                    'status' => 'pendente',
                                    'fk_solicitacao' => $idSolicitacao,
                                    'fk_interno' => $id,
                                    'unidade_anterior' => UnidadesController::getIdUP($id),
                                    'pavilhao_anterior' => UnidadesController::getIdPavilhao($id),
                                    'ala_anterior' => UnidadesController::getIdAla($id),
                                    'cela_anterior' => UnidadesController::getIdCela($id),
                                    'obs' => $motivo,
                                ]
                            );
                            $internosLog = $internosLog . "Prontuário: " . $id . " - " . $nomeInterno . ", ";
                        } else {
                            $internosMesmaUnidade = $internosMesmaUnidade . $nomeInterno . ", ";
                        }
                    }
                }
            }

            $textoLog = $textoLog . substr($internosLog, 0, -2) . ".";
            LogController::log($idSolicitacao, "solicitacao_transferencia", "add", Auth::user()->cpf, $textoLog);


            if ($request->file('documentos')[0] != null) {

                $qtdArquivo = count($request->file('documentos'));

                for ($x = 0; $x < $qtdArquivo; $x++) {

                    $destinationPath = Constantes::ARQUIVOS_SOLICITACAO_INTERNO . "/" . $idSolicitacao;

                    $name = "solicitacao" . $idSolicitacao . "_" . time() . "_" . $request->file('documentos')[$x]->getClientOriginalName();

                    $request->file('documentos')[$x]->storeAs($destinationPath, $name);

                    $insertArquivo = DB::table('solicitacao_transferencia_arquivos')->insert(
                        [
                            'fk_solicitacao' => $idSolicitacao,
                            'nome_arquivo' => $name,
                            'created_at' => Tdata::TtimeStampHojeMysql(),
                        ]
                    );

                }
            }

            $textoSolicitacoesDuplicadas = "";
            $textoInternoMesmaUnidade = "";
            $textoInternoTransferenciaEmAberto = "";
            if(!empty($solicitacoesDuplicadas))
            {
                $textoSolicitacoesDuplicadas = "<br>Internos com solicitação Pendente:<br>".$solicitacoesDuplicadas;
            }
            if(!empty($internosMesmaUnidade))
            {
                $textoInternoMesmaUnidade = "<br>Internos que já estão na unidade solicitante:<br>".$internosMesmaUnidade;
            }
            if(!empty($transferenciaEmAberto))
            {
                $textoInternoTransferenciaEmAberto = "<br>Internos que estão com transferência em aberto:<br>".$transferenciaEmAberto;
            }
            return redirect()->route('solicitacaoInterno.historico')->with('alert-success', "Solicitação de Interno Realizada com Sucesso!".$textoSolicitacoesDuplicadas.$textoInternoMesmaUnidade.$textoInternoTransferenciaEmAberto);

        }else{

            return redirect()->back()->with('alert-danger', 'Ocorreu um erro e a solicitação não foi realizada, entre em contato com a '.config('app.ti').'.');

        }

    }

    public function historico()
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $solicitacoesDados = DB::table('solicitacao_transferencia_interno')
            ->leftJoin('solicitacao_transferencia', 'solicitacao_transferencia.id','solicitacao_transferencia_interno.fk_solicitacao')
            ->where('solicitacao_transferencia.unidade_solicitante', $lotacaoServidor)
            ->where('solicitacao_transferencia_interno.status','=', 'pendente')
            ->select('solicitacao_transferencia_interno.id AS solicitacaoId', 'solicitacao_transferencia_interno.*', 'solicitacao_transferencia.*')
            ->orderBy('solicitacao_transferencia.created_at', 'DESC')
            ->get();

        $solicitacoes = array();

        foreach($solicitacoesDados as $solicitacoesDado)
        {
            array_push($solicitacoes, array(
                'idSolicitacao' => $solicitacoesDado->fk_solicitacao,
                'idSolicitacaoInterno' => $solicitacoesDado->solicitacaoId,
                'status' => $solicitacoesDado->status,
                'idInterno' => $solicitacoesDado->fk_interno,
                'regime' => InternoController::getRegime($solicitacoesDado->fk_interno),
                'nomeInterno' => InternoController::getInternoNome($solicitacoesDado->fk_interno),
                'unidadeSolicitada' => UnidadesController::getSiglaUp($solicitacoesDado->unidade_anterior),
                'obs' => $solicitacoesDado->obs,
                'obsCancelamento' => $solicitacoesDado->obs_cancelamento,
                'dataSolicitacao' => Tdata::TtimestampBR($solicitacoesDado->created_at)
            ));
        }

        $solicitacoesCanceladasDados = DB::table('solicitacao_transferencia_interno')
            ->leftJoin('solicitacao_transferencia', 'solicitacao_transferencia.id','solicitacao_transferencia_interno.fk_solicitacao')
            ->where('solicitacao_transferencia.unidade_solicitante', $lotacaoServidor)
            ->where('solicitacao_transferencia_interno.status', 'cancelada')
            ->select('solicitacao_transferencia_interno.id AS solicitacaoId', 'solicitacao_transferencia_interno.*', 'solicitacao_transferencia.*')
            ->orderBy('solicitacao_transferencia.created_at', 'DESC')
            ->get();

        $solicitacoesCanceladas = array();

        foreach($solicitacoesCanceladasDados as $solicitacoesCanceladaDado)
        {
            array_push($solicitacoesCanceladas, array(
                'idSolicitacao' => $solicitacoesCanceladaDado->fk_solicitacao,
                'idSolicitacaoInterno' => $solicitacoesCanceladaDado->solicitacaoId,
                'status' => $solicitacoesCanceladaDado->status,
                'idInterno' => $solicitacoesCanceladaDado->fk_interno,
                'nomeInterno' => InternoController::getInternoNome($solicitacoesCanceladaDado->fk_interno),
                'unidadeSolicitada' => UnidadesController::getSiglaUp($solicitacoesCanceladaDado->unidade_anterior),
                'obs' => $solicitacoesCanceladaDado->obs,
                'obsCancelamento' => $solicitacoesCanceladaDado->obs_cancelamento,
                'dataSolicitacao' => Tdata::TtimestampBR($solicitacoesCanceladaDado->created_at)
            ));
        }

        $solicitacoesCanceladasUnidadeSolicitadaDados = DB::table('solicitacao_transferencia_interno')
            ->leftJoin('solicitacao_transferencia', 'solicitacao_transferencia.id','solicitacao_transferencia_interno.fk_solicitacao')
            ->where('solicitacao_transferencia.unidade_solicitante', $lotacaoServidor)
            ->where('solicitacao_transferencia_interno.status', 'cancelada')
            ->where('solicitacao_transferencia_interno.obs_cancelamento', '!=', null)
            ->select('solicitacao_transferencia_interno.id AS solicitacaoId', 'solicitacao_transferencia_interno.*', 'solicitacao_transferencia.*')
            ->orderBy('solicitacao_transferencia.created_at', 'DESC')
            ->get();

        $solicitacoesCanceladasUnidadeSolicitada = array();

        foreach($solicitacoesCanceladasUnidadeSolicitadaDados as $solicitacoesCanceladasUnidadeSolicitadaDado)
        {
            array_push($solicitacoesCanceladasUnidadeSolicitada, array(
                'idSolicitacao' => $solicitacoesCanceladasUnidadeSolicitadaDado->fk_solicitacao,
                'idSolicitacaoInterno' => $solicitacoesCanceladasUnidadeSolicitadaDado->solicitacaoId,
                'status' => $solicitacoesCanceladasUnidadeSolicitadaDado->status,
                'idInterno' => $solicitacoesCanceladasUnidadeSolicitadaDado->fk_interno,
                'nomeInterno' => InternoController::getInternoNome($solicitacoesCanceladasUnidadeSolicitadaDado->fk_interno),
                'unidadeSolicitada' => UnidadesController::getSiglaUp($solicitacoesCanceladasUnidadeSolicitadaDado->unidade_anterior),
                'obs' => $solicitacoesCanceladasUnidadeSolicitadaDado->obs,
                'obsCancelamento' => $solicitacoesCanceladasUnidadeSolicitadaDado->obs_cancelamento,
                'dataSolicitacao' => Tdata::TtimestampBR($solicitacoesCanceladasUnidadeSolicitadaDado->created_at)
            ));
        }

        return view('movimentacoes.solicitacaoInterno.historicoSolicitacoes', compact('solicitacoes','solicitacoesCanceladas','solicitacoesCanceladasUnidadeSolicitada'));
    }

    public function cancelarSolicitacaoStore(Request $request)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $internos = $request->get('cancelamentoSolicitacaoCheck');

        foreach ($internos as $interno) {

            $dadosSolicitacao = explode("/",$interno);
            $verificaAutoridade = DB::table('solicitacao_transferencia_interno')
                ->leftJoin('solicitacao_transferencia', 'solicitacao_transferencia.id','solicitacao_transferencia_interno.fk_solicitacao')
                ->where('solicitacao_transferencia.unidade_solicitante', $lotacaoServidor)
                ->where('solicitacao_transferencia_interno.fk_interno', $dadosSolicitacao[0])
                ->where('solicitacao_transferencia.id', $dadosSolicitacao[1])
                ->count();

            $servidorResponsavel = FacadeController::getNomeServidorCpf(Auth::user()->cpf)."(".Auth::user()->cpf.")";
            $motivacao = $request->get('motivo')."<br>Cancelado por: ".$servidorResponsavel." em ".Tdata::TtimeStampHojeBr();

            if($verificaAutoridade > 0)
            {
                DB::table('solicitacao_transferencia_interno')->where('id', $dadosSolicitacao[2])->update(['status'=>'cancelada', 'obs'=>$motivacao, ]);

                LogController::log($dadosSolicitacao[1], "solicitacao_transferencia_interno", "update", Auth::user()->cpf, "Cancelamento de solicitação de interno pela unidade solicitante.");

            }
        }

        if($internos)
        {
            return redirect()->route('solicitacaoInterno.historico')->with('alert-success', "Cancelamento de solicitação realizado com sucesso!");

        }
    }

    public function pedidos()
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $solicitacoesDados = DB::table('solicitacao_transferencia_interno')
            ->leftJoin('solicitacao_transferencia', 'solicitacao_transferencia.id','solicitacao_transferencia_interno.fk_solicitacao')
            ->where('solicitacao_transferencia_interno.unidade_anterior', $lotacaoServidor)
            ->where('solicitacao_transferencia_interno.status', 'pendente')
            ->select('solicitacao_transferencia_interno.id AS solicitacaoId', 'solicitacao_transferencia_interno.*', 'solicitacao_transferencia.*')
            ->orderBy('solicitacao_transferencia.created_at', 'DESC')
            ->get();

        $solicitacoes = array();

        foreach($solicitacoesDados as $solicitacoesDado)
        {
            array_push($solicitacoes, array(
                'idSolicitacao' => $solicitacoesDado->fk_solicitacao,
                'idSolicitacaoInterno' => $solicitacoesDado->solicitacaoId,
                'status' => $solicitacoesDado->status,
                'idInterno' => $solicitacoesDado->fk_interno,
                'nomeInterno' => InternoController::getInternoNome($solicitacoesDado->fk_interno),
                'regime' => InternoController::getRegime($solicitacoesDado->fk_interno),
                'unidadeSolicitante' => UnidadesController::getSiglaUp($solicitacoesDado->unidade_solicitante),
                'unidadeSolicitada' => UnidadesController::getSiglaUp($solicitacoesDado->unidade_anterior),
                'pavilhaoSolicitada' => UnidadesController::getNomePav($solicitacoesDado->pavilhao_anterior),
                'alaSolicitada' => UnidadesController::getNomeAla($solicitacoesDado->ala_anterior),
                'celaSolicitada' => UnidadesController::getNomeCela($solicitacoesDado->cela_anterior),
                'obs' => $solicitacoesDado->obs,
                'dataSolicitacao' => Tdata::TtimestampBR($solicitacoesDado->created_at),
                'chaveAutorizacao' => $solicitacoesDado->fk_interno."_".$solicitacoesDado->solicitacaoId,
            ));
        }

        $solicitacoesCanceladasDados = DB::table('solicitacao_transferencia_interno')
            ->leftJoin('solicitacao_transferencia', 'solicitacao_transferencia.id','solicitacao_transferencia_interno.fk_solicitacao')
            ->where('solicitacao_transferencia_interno.unidade_anterior', $lotacaoServidor)
            ->where('solicitacao_transferencia_interno.status', 'cancelada')
            ->where('solicitacao_transferencia_interno.obs_cancelamento', '!=', null)
            ->select('solicitacao_transferencia_interno.id AS solicitacaoId', 'solicitacao_transferencia_interno.*', 'solicitacao_transferencia.*')
            ->orderBy('solicitacao_transferencia.created_at', 'DESC')
            ->get();

        $solicitacoesCanceladas = array();

        foreach($solicitacoesCanceladasDados as $solicitacoesCanceladasDado)
        {
            array_push($solicitacoesCanceladas, array(
                'idSolicitacao' => $solicitacoesCanceladasDado->fk_solicitacao,
                'idSolicitacaoInterno' => $solicitacoesCanceladasDado->solicitacaoId,
                'status' => $solicitacoesCanceladasDado->status,
                'idInterno' => $solicitacoesCanceladasDado->fk_interno,
                'nomeInterno' => InternoController::getInternoNome($solicitacoesCanceladasDado->fk_interno),
                'unidadeSolicitante' => UnidadesController::getSiglaUp($solicitacoesCanceladasDado->unidade_solicitante),
                'unidadeSolicitada' => UnidadesController::getSiglaUp($solicitacoesCanceladasDado->unidade_anterior),
                'pavilhaoSolicitada' => UnidadesController::getNomePav($solicitacoesCanceladasDado->pavilhao_anterior),
                'alaSolicitada' => UnidadesController::getNomeAla($solicitacoesCanceladasDado->ala_anterior),
                'celaSolicitada' => UnidadesController::getNomeCela($solicitacoesCanceladasDado->cela_anterior),
                'obs' => $solicitacoesCanceladasDado->obs,
                'regime' => InternoController::getRegime($solicitacoesCanceladasDado->fk_interno),
                'obsCancelamento' => $solicitacoesCanceladasDado->obs_cancelamento,
                'dataSolicitacao' => Tdata::TtimestampBR($solicitacoesCanceladasDado->created_at),
                'chaveAutorizacao' => $solicitacoesCanceladasDado->fk_interno."_".$solicitacoesCanceladasDado->solicitacaoId,
            ));
        }

        return view('movimentacoes.solicitacaoInterno.pedidos', compact('solicitacoes','solicitacoesCanceladas'));
    }

    public function confirmarSolicitacaoStore($chaveAutorizacao)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $ids = explode("_", $chaveAutorizacao);
        $idInterno = $ids[0];
        $idSolicitacao = $ids[1];

        $solicitacaoDados = DB::table('solicitacao_transferencia_interno')
            ->leftJoin('solicitacao_transferencia', 'solicitacao_transferencia.id','solicitacao_transferencia_interno.fk_solicitacao')
            ->where('solicitacao_transferencia_interno.unidade_anterior', $lotacaoServidor)
            ->where('solicitacao_transferencia_interno.id', $idSolicitacao)
            ->where('solicitacao_transferencia_interno.fk_interno', $idInterno)
            ->where('solicitacao_transferencia_interno.status', 'pendente')
            ->select('solicitacao_transferencia_interno.id AS solicitacaoId', 'solicitacao_transferencia_interno.*', 'solicitacao_transferencia.*')
            ->orderBy('solicitacao_transferencia.created_at', 'DESC')
            ->get()[0];

        $insertTransferencia = DB::table('movimentacao')->insert([
            'tipo' => 'transferencia',
            'unidade_destino' => $solicitacaoDados->unidade_solicitante,
            'motivo' => "Transferência realizada por solicitação do destino. Motivação: ".$solicitacaoDados->obs,
            'data' => $solicitacaoDados->data_entrada_interno,
            'created_at' => Tdata::TtimeStampHojeMysql(),
        ]);
        $idTransferencia = DB::getPdo()->lastInsertId();

        DB::table('movimentacao_interno')->insert([
                'status' => 'Finalizada',
                'fk_movimentacao' => $idTransferencia,
                'fk_interno' => $idInterno,
                'up_anterior' => UnidadesController::getIdUP($idInterno),
                'pv_anterior' => UnidadesController::getIdPavilhao($idInterno),
                'ala_anterior' => UnidadesController::getIdAla($idInterno),
                'cela_anterior' => UnidadesController::getIdCela($idInterno)
            ]
        );
        LogController::log($idTransferencia, "movimentacao", "add", Auth::user()->cpf, "Tranferência de unidade prisional através de solicitação. Interno(a): ".InternoController::getInternoNome($idInterno)." prontuário: ".$idInterno);


        $arquivosSolicitacao = DB::table('solicitacao_transferencia_arquivos')->where('fk_solicitacao', $solicitacaoDados->fk_solicitacao)->get();
        foreach($arquivosSolicitacao as $arquivo)
        {
                if (file_exists(storage_path('app/arquivos/solicitacao_interno/' . $solicitacaoDados->fk_solicitacao . "/" . $arquivo->nome_arquivo))) {

                $move = Storage::copy(Constantes::ARQUIVOS_SOLICITACAO_INTERNO . "/" . $solicitacaoDados->fk_solicitacao . "/" . $arquivo->nome_arquivo,  Constantes::ARQUIVOS_MOVIMENTACAO . "/transferencia/" . $idTransferencia."/". $arquivo->nome_arquivo);


                    $insertArquivo = DB::table('movimentacao_arquivos')->insert(
                        [
                            'fk_movimentacao' => $idTransferencia,
                            'nome_arquivo' => $arquivo->nome_arquivo,
                        ]
                    );


                }
        }

        $insertTransferenciaEntrada = DB::table('movimentacao')->insert([
            'tipo' => 'transferenciaentrada',
            'unidade_destino' => $solicitacaoDados->unidade_solicitante,
            'motivo' => "Entrada na Unidade Prisional por solicitação de transferência",
            'data' => $solicitacaoDados->data_entrada_interno,
            'created_at' => Tdata::TtimeStampHojeMysql(),
        ]);
        $idTransferenciaEntrada = DB::getPdo()->lastInsertId();
        LogController::log($idTransferenciaEntrada, "movimentacao", "add", Auth::user()->cpf, "Entrada na unidade prisional gerada através de transferência por solicitação. Interno(a): ".InternoController::getInternoNome($idInterno)." prontuário: ".$idInterno);


        DB::table('movimentacao_interno')->insert([
                'status' => 'Finalizada',
                'fk_movimentacao' => $idTransferenciaEntrada,
                'fk_interno' => $idInterno,
                'up_anterior' => UnidadesController::getIdUP($idInterno),
                'pv_anterior' => UnidadesController::getIdPavilhao($idInterno),
                'ala_anterior' => UnidadesController::getIdAla($idInterno),
                'cela_anterior' => UnidadesController::getIdCela($idInterno)
            ]
        );
        if($idTransferenciaEntrada)
        {
            DB::table('interno')->where('id',$idInterno)->update(
                [
                    'unidade_prisional'=> $solicitacaoDados->unidade_solicitante,
                    'pavilhao' => $solicitacaoDados->pavilhao_solicitante,
                    'ala' => $solicitacaoDados->ala_solicitante,
                    'cela' => $solicitacaoDados->cela_solicitante
                ]);

            LogController::log($idInterno, "interno", "update", Auth::user()->cpf, "Alteração de unidade, pavilhão, ala e cela gerada através de transferência por solicitação. Interno(a): ".InternoController::getInternoNome($idInterno)." prontuário: ".$idInterno);

            DB::table('solicitacao_transferencia_interno')->where('id', $idSolicitacao)->update(['status'=> "finalizada"]);

            $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula')[0]->matricula;
            $nomeServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'nome')[0]->nome;
            $unidadeServidorLogado = FacadeController::getLotacaoServidor(Auth::user()->cpf);
            $nomeInterno = InternoController::getInternoNome($idInterno);


            $assunto = "Solicitação de Interno Aceita pela Unidade ".UnidadesController::getSiglaUp($solicitacaoDados->unidade_anterior);
            $descricao = "O(A) servidor(a) ".$nomeServidorLogado." (matrícula ".$matriculaServidorLogado."), lotado na unidade ".$unidadeServidorLogado.", Transferiu o interno(a) ".$nomeInterno." (prontuário ".$idInterno.") através da solicitação de transferência.";

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
                    'destinatario' => $solicitacaoDados->unidade_solicitante,
                    'created_at' => date("Y-m-d"),
                    'hora' => date("H:i"),
                ]
            );

                return redirect()->route('solicitacaoInterno.pedidos')->with('alert-success', "Transferência de interno realizada com sucesso!");


        }else{

            return redirect()->back()->with('alert-danger', "Operação não realizada, entre em contato com o ".config('app.ti').'.');

        }


    }

    public function CancelarSolicitacao($chaveCancelamento)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $ids = explode("_", $chaveCancelamento);
        $idInterno = $ids[0];
        $idSolicitacao = $ids[1];

        $solicitacaoDados = DB::table('solicitacao_transferencia_interno')
            ->leftJoin('solicitacao_transferencia', 'solicitacao_transferencia.id','solicitacao_transferencia_interno.fk_solicitacao')
            ->leftJoin('interno', 'interno.id','solicitacao_transferencia_interno.fk_interno')
            ->leftJoin('unidade', 'unidade.id','solicitacao_transferencia.unidade_solicitante')
            ->where('solicitacao_transferencia_interno.unidade_anterior', $lotacaoServidor)
            ->where('solicitacao_transferencia_interno.id', $idSolicitacao)
            ->where('solicitacao_transferencia_interno.fk_interno', $idInterno)
            ->where('solicitacao_transferencia_interno.status', 'pendente')
            ->select('solicitacao_transferencia_interno.id AS solicitacaoId', 'solicitacao_transferencia_interno.*', 'interno.nome', 'solicitacao_transferencia.*','unidade.nome AS nome_unidade_solicitante')
            ->orderBy('solicitacao_transferencia.created_at', 'DESC')
            ->get()[0];

        return view('movimentacoes.solicitacaoInterno.cancelarSolicitacao', compact('chaveCancelamento','solicitacaoDados'));
    }

    public function CancelarSolicitacaoUnidadeSolicitadaStore(Request $request, $chaveCancelamento)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $ids = explode("_", $chaveCancelamento);
        $idInterno = $ids[0];
        $idSolicitacao = $ids[1];


            $verificaAutoridade = DB::table('solicitacao_transferencia_interno')
                ->leftJoin('solicitacao_transferencia', 'solicitacao_transferencia.id','solicitacao_transferencia_interno.fk_solicitacao')
                ->where('solicitacao_transferencia_interno.fk_interno', $idInterno)
                ->where('solicitacao_transferencia_interno.id', $idSolicitacao)
                ->where('solicitacao_transferencia_interno.unidade_anterior', $lotacaoServidor)
                ->select('solicitacao_transferencia_interno.id AS solicitacaoId', 'solicitacao_transferencia_interno.*', 'solicitacao_transferencia.*')
                ->get()[0];

        $servidorResponsavel = FacadeController::getNomeServidorCpf(Auth::user()->cpf)."(".Auth::user()->cpf.")";
        $motivacao = $request->get('motivo')."<br>Cancelado por: ".$servidorResponsavel." em ".Tdata::TtimeStampHojeBr();
            if($verificaAutoridade != [])
            {
               $cancelarUpdate = DB::table('solicitacao_transferencia_interno')->where('id', $idSolicitacao)->update(['status'=>'cancelada', 'obs_cancelamento'=> $motivacao, 'data_cancelamento'=>Tdata::TtimeStampHojeMysql() ]);

                LogController::log($idSolicitacao, "solicitacao_transferencia_interno", "update", Auth::user()->cpf, "Cancelamento de solicitação de interno, realizada pela unidade solicitada. Interno(a): ".InternoController::getInternoNome($idInterno)." prontuário: ".$idInterno);

            }


        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula')[0]->matricula;
        $nomeServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'nome')[0]->nome;
        $unidadeServidorLogado = FacadeController::getLotacaoServidor(Auth::user()->cpf);
        $nomeInterno = InternoController::getInternoNome($idInterno);


        $assunto = "Solicitação de Interno Cancelada pela Unidade ".UnidadesController::getSiglaUp($verificaAutoridade->unidade_anterior);
        $descricao = "O(A) servidor(a) ".$nomeServidorLogado." (matrícula ".$matriculaServidorLogado."), lotado na unidade ".$unidadeServidorLogado.", cancelou a solicitação do(a) interno(a) ".$nomeInterno." (prontuário ".$idInterno."). Motivação: ".$request->get('motivo');

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
                'destinatario' => $verificaAutoridade->unidade_solicitante,
                'created_at' => date("Y-m-d"),
                'hora' => date("H:i"),
            ]
        );


        if($cancelarUpdate)
        {
            return redirect()->route('solicitacaoInterno.historico')->with('alert-success', "Cancelamento de solicitação realizado com sucesso!");

        }else{
            return redirect()->route('solicitacaoInterno.historico')->with('alert-danger', "Erro ao cancelar a solicitação! Entre em contato com a ".config('app.ti').".");

        }

    }
    public static function getQtdInternosSolicitados()
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $internosSolicitados = DB::table('solicitacao_transferencia_interno')
            ->leftJoin('solicitacao_transferencia', 'solicitacao_transferencia.id','solicitacao_transferencia_interno.fk_solicitacao')
            ->where('solicitacao_transferencia_interno.unidade_anterior', $lotacaoServidor)
            ->where('solicitacao_transferencia_interno.status', 'pendente')
            ->select('solicitacao_transferencia_interno.id AS solicitacaoId', 'solicitacao_transferencia_interno.*', 'solicitacao_transferencia.*')
            ->orderBy('solicitacao_transferencia.created_at', 'DESC')
            ->count();

        return $internosSolicitados;
    }
}
