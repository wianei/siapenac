<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a Notificacao
|
*/

namespace App\Http\Controllers\Notificacao;

use App\Classes\Tdata;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Http\Controllers\Permissoes\PermissoesController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificacaoController extends Controller
{
    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->middleware('auth');


    }

    public static function getNotificacoes()
    {
        if (PermissoesController::getTipoPermissao() == "I") {

            $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

            $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf, 'matricula');

            $notificacoes = array();
            $notificacoesGep = array();

            $dadosNotificacoesAlocacaoCela = DB::table('notificacao_lida')->where('tipo', 'alocacaoCela')->where('created_at', Tdata::TdataHojeMysql())->where('fk_servidor', $matriculaServidorLogado[0]->matricula)->orderBy('created_at', 'DESC')->first();

            //if($dadosNotificacoesAlocacaoCela ==  null) {

            $alocacaoDeCela = DB::table('interno')->where('unidade_prisional', $lotacaoServidor)->where('status', 'A')->whereNull('cela')->get();
            if (count($alocacaoDeCela) > 0) {
                $notificacoes['alocacaoCela'] = $alocacaoDeCela;
            }
            //}

            $dadosNotificacoesGep = DB::table('notificacao_lida')->where('tipo', 'gep')->select('fk_notificacao')->where('fk_servidor', $matriculaServidorLogado[0]->matricula)->get();

            foreach ($dadosNotificacoesGep as $dados) {
                array_push($notificacoesGep, $dados->fk_notificacao);
            }

            $gepCancelada = DB::table('escolta')->where('unidade', $lotacaoServidor)->where('tipo', 'gep')->where('status', 'Cancelada')->whereNotIn('id', $notificacoesGep)->where('data_saida', '>=', Tdata::TdataHojeMysql())->get();
            if (count($gepCancelada) > 0) {
                $notificacoes['gepCancelamento'] = $gepCancelada;
            }

            $sancaoTriagem = DB::table('sancao_triagem')
                    ->leftJoin('interno', 'interno.id', '=', 'sancao_triagem.fk_interno')
                    ->where('sancao_triagem.unidade', $lotacaoServidor)
                    ->where('interno.status', "A")
                    ->where('sancao_triagem.data_fim', '<=', Tdata::TdataHojeMysql())
                    ->where('sancao_triagem.status', 'A')
                    ->get();
            if (count($sancaoTriagem) > 0) {
                $notificacoes['sancaoTriagem'] = $sancaoTriagem;
            }

            $escoltaGepConfirmada = DB::table('escolta')->where('unidade', $lotacaoServidor)->where("tipo", "gep")->where('data_saida', '>=', Tdata::TdataHojeMysql())->where('status', 'Confirmada')->get();
            if (count($escoltaGepConfirmada) > 0) {
                $notificacoes['escoltaGepConfirmada'] = $escoltaGepConfirmada;
            }

            $internoPedenteTransferencia = DB::table('movimentacao_interno')
                ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                ->where('movimentacao.unidade_destino', $lotacaoServidor)
                ->where('movimentacao.tipo', "transferencia")
                ->where("movimentacao_interno.status", "Pendente")
                ->get();
            if (count($internoPedenteTransferencia) > 0) {
                $notificacoes['internoPedenteTransferencia'] = $internoPedenteTransferencia;
            }

            $internoPedenteSaidaTransferencia = DB::table('movimentacao_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao_interno.up_anterior', $lotacaoServidor)
            ->where('movimentacao.tipo', "transferencia")
            ->where("movimentacao_interno.status", "Pendente")
            ->get();
            if (count($internoPedenteSaidaTransferencia) > 0) {
                $notificacoes['internoPedenteSaidaTransferencia'] = $internoPedenteSaidaTransferencia;
            }

            //dd($notificacoes);

            $internosSolicitados = DB::table('solicitacao_transferencia_interno')
                ->leftJoin('solicitacao_transferencia', 'solicitacao_transferencia.id','solicitacao_transferencia_interno.fk_solicitacao')
                ->where('solicitacao_transferencia_interno.unidade_anterior', $lotacaoServidor)
                ->where('solicitacao_transferencia_interno.status', 'pendente')
                ->select('solicitacao_transferencia_interno.id AS solicitacaoId', 'solicitacao_transferencia_interno.*', 'solicitacao_transferencia.*')
                ->orderBy('solicitacao_transferencia.created_at', 'DESC')
                ->count();
            if($internosSolicitados > 0)
            {
                $notificacoes['solicitacoesInterno'] = $internosSolicitados;
            }

            return ($notificacoes);

        }

    }

    public static function getNotificacoesSetic()
    {
        $notificacaoDados = DB::table('helpdesk_notification')
            ->leftJoin('helpdesk', 'helpdesk.id', '=', 'helpdesk_notification.fk_chamado')
            ->where('helpdesk_notification.user', Auth::user()->cpf)
            ->where('helpdesk_notification.status', 'A')
            ->get();

//        dd(count($notificacaoDados));

        if(count($notificacaoDados) > 0)
        {
            foreach($notificacaoDados as $notificacaoDado)
            {
                $notificacaoDado->update_at = Tdata::TtimestampBR($notificacaoDado->update_at);
            }
            return $notificacaoDados;

        }else{

            return "false";
        }

    }

    public function notificacoes()
    {
//        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
//
//        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula');
//
//        $alocacaoDeCela = DB::table('interno')
//            ->leftJoin('foto', function ($query) {
//                $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('tipo', 'principal');
//            })
//            ->where('unidade_prisional', $lotacaoServidor)
//            ->where('regime', 'Fechado')
//            ->where('status', 'A')
//            ->where('cela', null)
//            ->select('interno.*','foto.nome_arquivo','foto.legenda')
//            ->limit(50)->orderBy('nome', 'ASC')->get();
//        if(count($alocacaoDeCela) > 0)
//        {
//            $internosPendentesDeAlocacao = $alocacaoDeCela;
//        }else{
//
//            $internosPendentesDeAlocacao = null;
//        }
//
//
//
//        $notificacoesGep = array();
//
//        $dadosNotificacoesGep = DB::table('notificacao_lida')->where('tipo','gep')->select('fk_notificacao')->where('fk_servidor', $matriculaServidorLogado[0]->matricula)->get();
//
//        foreach ($dadosNotificacoesGep as $dados) {
//            array_push($notificacoesGep, $dados->fk_notificacao);
//        }
//
//        $gepCancelada = DB::table('escolta')->where('unidade', $lotacaoServidor)->where('tipo', 'gep')->where('status', 'Cancelada')->whereNotIn('id', $notificacoesGep)->get();
//        if(count($gepCancelada) > 0) {
//
//            $escoltasCanceladas = $gepCancelada;
//        }else{
//
//            $escoltasCanceladas = null;
//        }
//
//        return view('notificacao.notificacao', compact('internosPendentesDeAlocacao','escoltasCanceladas'));
    }

}