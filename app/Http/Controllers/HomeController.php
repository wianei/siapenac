<?php

namespace App\Http\Controllers;

use App\Classes\Tdata;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Movimentacoes\MovimentacoesController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Http\Controllers\Permissoes\PermissoesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     *
     */
    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->middleware('auth');
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        PermissoesController::deletePermissionUser();
        PermissoesController::loadPermissionUser();
        
        LogController::logAcessoUser();

        if (PermissoesController::getTipoPermissao() == "I") {


            $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
           

            $internosSispern = DB::table('interno')->where('status', 'A')->count();

            $internosUnidade = DB::table('interno')->where('status', 'A')->where('unidade_prisional', $lotacaoServidor)->count();

            $servidoresUnidade = FacadeController::getTotalServidoresLotacao(FacadeController::getLotacaoServidor(Auth::user()->cpf));

            $atendimentoAdv = DB::table('advogado_agendamento')->where('data', Tdata::TdataHojeMysql())->where('unidade', $lotacaoServidor)->where('status','Agendado')->count();

            $internosEscolta = DB::table('escolta_internos')
                ->leftJoin('escolta', 'escolta.id', '=', 'escolta_internos.fk_escolta')
                ->where('escolta.unidade', $lotacaoServidor)
                ->where('escolta.tipo', 'gep')
                ->where('escolta.data_saida', Tdata::TdataHojeMysql())
                ->where('escolta.status', 'Confirmada')
                ->count();

            $internosSeparacao = $atendimentoAdv + $internosEscolta;

            $dadosLogUnidade = DB::table('log')->where('unidade', $lotacaoServidor)->whereNotIn('tabela_alterada', ['mensagem_destinatario_lido','user','mensagem','users_recuperacao','helpdesk','helpdesk_arquivos','helpdesk_notification'])->orderBy('created_at', 'DESC')->limit(5)->get();

            $logUnidade = array();
            foreach ($dadosLogUnidade as $log) {
                $nomeServidor = FacadeController::getNomeServidorCpf($log->cpf_usuario);

                array_push($logUnidade, array(
                    'data' => Tdata::TdataBR(Tdata::getDataTimestamp($log->created_at)),
                    'hora' => Tdata::getTempoTimestamp($log->created_at),
                    'tabela' => str_replace("_", " ", $log->tabela_alterada),
                    'descricao' => $log->texto,
                    'realizadoPor' => $nomeServidor . " (" . $log->cpf_usuario . ")",
                ));
            }

            $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf, 'matricula');
            
            $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

            $mensagensServidor = DB::table('mensagem_destinatarios')
                ->join('mensagem', 'mensagem.id', '=', 'mensagem_destinatarios.fk_mensagem')
                ->where('mensagem_destinatarios.destinatario', $matriculaServidorLogado[0]->matricula)
                ->where('mensagem.status', 'A')->where('mensagem_destinatarios.status', 'A')
                ->orWhere('mensagem_destinatarios.destinatario', $lotacaoServidor)
                ->orderBy('mensagem.created_at', 'DESC')->orderBy('mensagem.hora', 'DESC')->limit(5)->get();

//dd($mensagensServidor);

            $mensagens = array();

            foreach ($mensagensServidor as $mensagem) {

                $dadosRemetente = FacadeController::getDadosServidorPorMat($mensagem->remetente);

                $leitura = DB::table('mensagem_destinatario_lido')->where('fk_mensagem', $mensagem->id)->where('fk_destinatario', $matriculaServidorLogado[0]->matricula)->get();

                if (empty(json_decode($leitura, true))) {
                    $lido = "N";
                } else {
                    $lido = "S";
                }

                $arquivo = DB::table('mensagem_arquivo')->where('fk_mensagem', $mensagem->id)->get();
                if (empty(json_decode($arquivo, true))) {
                    $anexo = "N";
                } else {
                    $anexo = "S";
                }

                if ($mensagem->tipo == "unidade") {
                    $nomeDestinatario = UnidadesController::getNomeUp($mensagem->destinatario);
                }
                if ($mensagem->tipo == "servidor") {
                    $servidorDestinatario = FacadeController::getDadosServidorPorMat($mensagem->destinatario);
                    //$nomeDestinatario = $servidorDestinatario[0]->nome;
                    $nomeDestinatario = "Você";
                }

                array_push($mensagens, array(
                    'id' => $mensagem->id,
                    'remetente' => $dadosRemetente[0]->nome,
                    'destinatario' => $nomeDestinatario,
                    'hora' => $mensagem->hora,
                    'data' => Tdata::TdataBR($mensagem->created_at),
                    'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($mensagem->created_at)),
                    'assunto' => $mensagem->assunto,
                    'mensagem' => $mensagem->mensagem,
                    'lido' => $lido,
                    'anexo' => $anexo,
                    'tipo' => $mensagem->tipo,
                ));
            }


            $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

            $saidaSancaoDados = DB::table('sancao_triagem')
                ->leftJoin('interno', 'interno.id', '=', 'sancao_triagem.fk_interno')
                ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
                ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
                ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
                ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
                ->leftJoin('faccao', 'faccao.id', '=', 'interno.faccao')
                ->leftJoin('foto', function ($query) {
                    $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('foto.tipo', 'principal');
                })
                ->where('sancao_triagem.unidade', $lotacaoServidor)
                ->where('interno.status', 'A')
                ->where('sancao_triagem.status', 'A')
                ->where('sancao_triagem.data_fim', '<=', Tdata::TdataHojeMysql())
                ->select('interno.nome', 'interno.id', 'interno.periculosidade', 'interno.unidade_prisional', 'interno.pavilhao', 'interno.ala', 'interno.cela', 'interno.faccao', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" ELSE "Não" END) AS lideranca'), 'foto.nome_arquivo AS foto', DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'), 'pavilhao.nome as pvNome', 'ala.nome as alNome', 'cela.nome as celNome', DB::raw("DATE_FORMAT(sancao_triagem.data_fim,'%d/%m/%Y') as dataFim"), 'sancao_triagem.id as idTriagem', 'sancao_triagem.fk_movimentacao as fkMov', 'sancao_triagem.tipo as tipoSt', 'faccao.nome as facNome')
                ->orderBy('sancao_triagem.data_fim', 'ASC')
                ->orderBy('pavilhao.nome', 'ASC')
                ->orderBy('ala.nome', 'ASC')
                ->orderBy('cela.nome', 'ASC')
                ->orderBy('interno.nome', 'ASC')
                ->take(5)
                ->get();

            $saidaSancao = json_decode($saidaSancaoDados, true);

            //dd($saidaSancao);

            foreach ($saidaSancao as $key => $saida) {

                $saidaSancao[$key]['lotacaoAnterior'] = MovimentacoesController::getLotacaoAnteriorMovimentacao($saida['id'], $saida['fkMov']);
                $saidaSancao[$key]['mesData'] = Tdata::getMesExtAbrev(Tdata::getMesData(Tdata::TdataMysql($saida['dataFim'])));
            }


            $unidades = DB::table("unidade")->where('status', 1)->get();

            $qtdPresosUnidade = array();

            foreach($unidades as $unidade)
            {
                $presoUnidade = DB::table('interno')->where('status', 'A')->where('unidade_prisional', $unidade->id)->count();

                array_push($qtdPresosUnidade,
                    array("label"=> $unidade->sigla, "y"=> $presoUnidade)
                );
            }

//            $qtdPresosUnidade = collect($qtdPresosUnidade);
//
//            $qtdPresosUnidade->sortBy('y');

            usort($qtdPresosUnidade, function($a, $b) {
                return $b['y'] - $a['y'];
            });

            $qtdTransferenciasNaoAceitas = MovimentacoesController::getQtdTempoTransferenciaNaoConfirmada(0);
            $qtdTransferenciasRecebidasNaoAceitas = MovimentacoesController::getQtdTempoTransferenciaRecebidaNaoConfirmada(0);
            $qtdAlocacaoCelaPendente = DB::table('interno')->where('unidade_prisional', $lotacaoServidor)->where('status', 'A')->whereNull('cela')->count();

            return view('welcome', compact('internosSispern', 'internosUnidade', 'servidoresUnidade', 'internosSeparacao', 'logUnidade', 'mensagens', 'saidaSancao','qtdPresosUnidade','qtdTransferenciasNaoAceitas','qtdTransferenciasRecebidasNaoAceitas','qtdAlocacaoCelaPendente'));
        }
        $retornoPermissao = PermissoesController::getVerifyPermissionController("OabController");
        if($retornoPermissao == true)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }
        else{

            $unidades = DB::table("unidade")->where('status', 1)->get();

            $qtdPresosUnidade = array();


            foreach($unidades as $unidade)
            {
                $presoUnidade = DB::table('interno')->where('status', 'A')->where('unidade_prisional', $unidade->id)->count();

                array_push($qtdPresosUnidade,
                    array("label"=> $unidade->sigla, "y"=> $presoUnidade)
                );
            }

//            $qtdPresosUnidade = collect($qtdPresosUnidade);
//
//            $qtdPresosUnidade->sortBy('y');

            usort($qtdPresosUnidade, function($a, $b) {
                return $b['y'] - $a['y'];
            });

          //dd($qtdPresosUnidade);

            return view('welcome', compact('qtdPresosUnidade'));

        }
    }

    public function exibirMapa(){
        return view('mapa_unidades');
    }
}
