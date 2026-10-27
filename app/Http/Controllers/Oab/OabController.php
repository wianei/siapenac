<?php


/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados ao interno
|
*/

namespace App\Http\Controllers\Oab;

use App\Classes\Tdata;
use App\Http\Controllers\Interno\InternoController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Permissoes\PermissoesController;
use App\Http\Controllers\Unidades\UnidadesController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OabController extends Controller
{

    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->middleware('auth');

    }

    public function callAction($method, $parameters)
    {
        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::getVerifyPermissionController("OabController");
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

        return parent::callAction($method, $parameters);
    }

    public static function getVerificaAtendimento($idAdv, $idInterno, $dataAtendimento, $unidade)
    {
        $verifica = DB::table('advogado_agendamento')->where('fk_advogado', $idAdv)->where('fk_interno', $idInterno)->where('data', Tdata::TdataMysql($dataAtendimento))->where('unidade', $unidade)->where('status','Agendado')->get();

        if(count($verifica) >= 1)
        {
            $update = DB::table('advogado_agendamento')->where('fk_advogado', $idAdv)->where('fk_interno', $idInterno)->where('data', Tdata::TdataMysql($dataAtendimento))->where('unidade', $unidade)->update(['status'=>'Atendido']);
        }

        //$endTime = strtotime("+30 minutes", strtotime("08:45"));

        //dd(date('h:i:s', $endTime));


    }

    public function verificaUnidadeInternoAjax(Request $request)
    {
        $idInterno = $request->get('idInterno');

        $dadosInterno = InternoController::getDadosInterno($idInterno);

        $diasAtendimento = UnidadesController::getDiasAtendimentoAdv($dadosInterno[0]->unidade_prisional);

        return $diasAtendimento;


    }

    public function horario(Request $request){

        /**
         * config
         */

        $configAgendamento = DB::table('_configagendamentoadv')->get();

        $tempoIntervalo = $configAgendamento[0]->tempoIntervalo;

        $tempoAtendimento =  $configAgendamento[0]->tempoAtendimento;

        // fim config

        $data = $request->get('data');

        $interno = $request->get('interno');

        $dadosInterno = InternoController::getDadosInterno($interno);

        $qtdParlatorio = UnidadesController::getQtdParlatorioPavilhao($dadosInterno[0]->pavilhao);
        $tipo = "pavilhao";

        if(is_null($qtdParlatorio))
        {
            $qtdParlatorio = UnidadesController::getQtdParlatorioUnidade($dadosInterno[0]->unidade_prisional);
            $tipo = "unidade";
        }

        $horarioIncioAtendimento = UnidadesController::getHorarioInicioAtendimentoAdv($dadosInterno[0]->unidade_prisional);
        $horarioTerminoAtendimento = UnidadesController::getHorarioTerminoAtendimentoAdv($dadosInterno[0]->unidade_prisional);


        $verificaCancelamento = DB::table('advogado_agendamento_data_cancelada')->where('data', Tdata::TdataMysql($data))->where('unidade', $dadosInterno[0]->unidade_prisional)->count();

        if($verificaCancelamento >= 1)
        {
            $horarios = array(array('value' => '', 'nome'=>'Horários Cancelados pela Unidade Prisional'));

        }else {
            if($tipo == "unidade")
            {
                $dado = DB::table('advogado_agendamento')
                    ->select('horario', DB::raw('COUNT(*) as qtd'))
                    ->where('data', Tdata::TdataMysql($data))
                    ->where('unidade', UnidadesController::getIdUP($interno))
                    ->where('status', 'Agendado')
                    ->groupBy('horario')->get();

            }else{

                $dado = DB::table('advogado_agendamento')
                    ->leftJoin('interno','interno.id','=','advogado_agendamento.fk_interno')
                    ->select('advogado_agendamento.horario', DB::raw('COUNT(*) as qtd'))
                    ->where('advogado_agendamento.data', Tdata::TdataMysql($data))
                    ->where('interno.pavilhao', UnidadesController::getIdPavilhao($interno))
                    ->where('advogado_agendamento.status', 'Agendado')
                    ->groupBy('advogado_agendamento.horario')->get();

            }


            $starttime = $horarioIncioAtendimento;
            $endtime = $horarioTerminoAtendimento;
            $duration = $tempoAtendimento;

            $horarios = array();
            $start_time = strtotime($starttime);
            $end_time = strtotime($endtime);
            $i = 1;
            $add_mins = ($duration + $tempoIntervalo) * 60;

            while ($start_time <= $end_time)
            {
                $array_of_time[] = date("H:i", $start_time);
                $flagTempo = strtotime("+".$tempoAtendimento." minutes", strtotime(date('H:i', $start_time)));
                array_push($horarios, array(
                    'value' => date('H:i', $start_time),
                    'nome' => "Horário - " . date('H:i', $start_time) . " - " . date('H:i', $flagTempo)
                ));

                $start_time += $add_mins;
                $i++;
            }

            //dd($horarios);
            foreach ($dado as $d) {
                if ($d->qtd >= $qtdParlatorio) {
                    foreach ($horarios as $i => $v) {
                        if ($v['value'] == $d->horario) {
                            unset($horarios[$i]);
                        }
                    }
                }
//                if ($d->qtd >= $qtdParlatorio) {
//                    $key = array_search($d->horario, $horarios);
//                    unset($horarios[$key]);
//
//                }

            }

            $intervaloInicio = UnidadesController::getHorarioInicioIntervaloAlmoco(UnidadesController::getIdUP($interno));
            $intervaloTermino = UnidadesController::getHorarioTerminoIntervaloAlmoco(UnidadesController::getIdUP($interno));
            $keys = array();
            foreach($horarios as $horario)
            {
                if($horario['value'] >= $intervaloInicio && $horario['value'] < $intervaloTermino)
                {

                    $horarioVal = $horario['value'];
                    array_push($keys, array_keys(
                                array_filter(
                                    $horarios,
                                    function ($value) use ($horarioVal) {
                                        return (strpos($value['value'], $horarioVal) !== false);
                                    }
                                )
                    ));


                }
            }
            foreach($keys as $key)
            {
                unset($horarios[$key[0]]);
            }


        }


       return $horarios;

    }
    public function home()
    {
        LogController::logAcessoUser();

        //PermissoesController::verifyPermission(10, Auth::user()->tipo, '!=');

        if (Auth::user()->orgao != "DPE-RN") {

            if (Auth::user()->tipo != 10) {


                return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');

            }
        }


        /**
         * config
         */
        $configAgendamento = DB::table('_configagendamentoadv')->get();

        $qtdFaltasMes = $configAgendamento[0]->quantidadeFaltasMes;
        // !config

        $dadosAdv = DB::table('advogado')->select('id','cpf')->where('cpf',Auth::user()->cpf)->get();

        if(count($dadosAdv) < 1)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Sem custodiádos vinculados ao seu cadastro!');
        }

        // Quantidade de agendamento mes
        $verificaFaltaMes = DB::table('advogado_agendamento')->where('fk_advogado', $dadosAdv[0]->id)->whereMonth('data', Tdata::getMesData(Tdata::TdataHojeMysql()))->where('status','Falta')->count();

        if($verificaFaltaMes >= $qtdFaltasMes)
        {
            $penalidadeFalta = "A";

        }else{

            $penalidadeFalta = "I";

        }


//        $finalizarAtendimento = DB::table('advogado_agendamento')
//            ->where('fk_advogado', $dadosAdv[0]->id)
//            ->where('status','!=', 'Cancelada')
//            ->where(DB::raw('CONCAT_WS(" ", data, horario)'),'<', Tdata::TtimeStampHojeMysql())
//            ->update([
//                'status' => 'Finalizado'
//            ]);


        $dado = DB::table('advogado')->select('id','cpf')->where('cpf',Auth::user()->cpf)->get();
        //dd($dado[0]->id);
        $relacao = DB::table('advogado_interno');

        $relacao->leftJoin('interno',function($query){
            $query->on('interno.id','=','advogado_interno.fk_interno');
        });

        $relacao->leftJoin('unidade',function($query){
            $query->on('unidade.id','=','interno.unidade_prisional');
        });

        # ************************************************************************************* #
        #   IMPORTANTE ** se for só internos do PV5, inicialmente, descomentar a linha abaixo.  #
        #                                                                                       #
        #                   $relacao->where('interno.unidade_prisional',20);                    #
        #                                                                                       #
        # ************************************************************************************* #

        $relacao->where('interno.status','A');

        $relacao->where('advogado_interno.fk_advogado',$dado[0]->id)->where('advogado_interno.status','A');

        $dadosDosInternos = $relacao->select('interno.nome','interno.id','unidade.nome as nomeUnidade')->get();
        $dadosDosInternos = json_decode($dadosDosInternos, true);

        $sort = [
            ['column' => 'nome', 'order' => 'asc'],

        ];

        // dd($internos);

        $dadosDosInternos = collect($dadosDosInternos);
        $dadosDosInternos = self::multiPropertySort($dadosDosInternos, $sort);
        $dadosDosInternos = json_decode($dadosDosInternos, true);

        $dadosInternos = array();

        foreach($dadosDosInternos as $array){
            $dadosInternos[$array["nomeUnidade"]][] = $array;
        }

       //dd($dadosInternos);

        return view('oab.agendamentoAtendimento.agendamento', compact('dadosInternos','penalidadeFalta'));
    }

    public function store(Request $request)
    {
        LogController::logAcessoUser();

        /**
         * config
         */
            $configAgendamento = DB::table('_configagendamentoadv')->get();

            # define a quantidade de faltas ao atendimentos
            $qtdFaltasMes = $configAgendamento[0]->quantidadeFaltasMes;

            # define a quantidade de atendimentos por mes que o adv pode fazer a um interno especifico.
            $qtdAgendamentoMesAdv = $configAgendamento[0]->quantidadeAgendamentoInternoEspecificoMes;

            # define a quantidade de atendimentos por dia que o adv pode fazer a um interno especifico.
            $qtdAgendamentodiaInternoEspecifico = $configAgendamento[0]->quantidadeAgendamentoDiaInternoEspecifico;

            # define a quantidade de atendimentos por dia que o adv pode fazer a qualquer interno.
            $qtdAgendamentoDiaAdv = $configAgendamento[0]->quantidadeAgendamentoDiaAdv;
        // !config

        // validacoes

        $dataAtendimento = $request->get('dataAtendimento');
        $diaNumAtendimento = Tdata::diaNumero(Tdata::TdataMysql($dataAtendimento));
        $dadosInterno = InternoController::getDadosInterno($request->get('interno'));
        $diasAtendimento = UnidadesController::getDiasAtendimentoAdv($dadosInterno[0]->unidade_prisional);
        $diasAtendimentoArray = str_split(str_replace(",","",str_replace('"',"", $diasAtendimento)));

        if(in_array($diaNumAtendimento, $diasAtendimentoArray))
        {
            return redirect()->back()->with('alert-danger', 'A Unidade Prisional não realiza agendamento de teleatendimento na data informada!');

        }

        $dataHj = date("Y-m-d");

        $dado = DB::table('advogado')->select('id','cpf')->where('cpf',Auth::user()->cpf)->get();

        if(Tdata::TdataMysql($dataAtendimento) == "0000-00-00")
        {
            return redirect()->back()->with('alert-danger', 'Data incorreta. Favor voltar ao agendamento e selecionar uma data do formulário!');

        }

        if(Tdata::TdataInteiro(Tdata::TdataMysql($dataAtendimento)) < Tdata::TdataInteiro($dataHj))
        {
            return redirect()->back()->with('alert-danger', 'A data do agendamento não pode ser menor que a data de hoje');

        }

        $verificaFaltaMes = DB::table('advogado_agendamento')->where('fk_advogado', $dado[0]->id)->whereMonth('data', Tdata::getMesData(Tdata::TdataHojeMysql()))->where('status','Falta')->count();

        if($verificaFaltaMes >= $qtdFaltasMes)
        {
            return redirect()->back()->with('alert-danger', 'Você não pode mais agendar atendimentos neste mês, o número de faltas  foi atingido.');
        }

        $verificaAgendamentoMesmoHorarioMesmodia = DB::table('advogado_agendamento')->where('fk_advogado', $dado[0]->id)->where('data', Tdata::TdataMysql($request->get('dataAtendimento')))->where('horario', $request->get('horario'))->where('status','!=','Cancelada')->count();

        if($verificaAgendamentoMesmoHorarioMesmodia >= 1)
        {
            return redirect()->back()->with('alert-danger', 'Você já possui um agendamento para esse dia e horário!');
        }

        // verificacao de agendamento no mesmo horario para interno
        $verifyAgendamentoHorarioInterno = DB::table('advogado_agendamento')->where('fk_interno', $request->get('interno'))->where('unidade', UnidadesController::getIdUP($request->get('interno')))->where('data', Tdata::TdataMysql($request->get('dataAtendimento')))->where('horario', $request->get('horario'))->where('status','!=','Cancelada')->count();

        if($verifyAgendamentoHorarioInterno > 0)
        {
            return redirect()->back()->with('alert-danger', 'Já houve um agendamento para este interno no horário selecionado!');
        }

        // Quantidade de agendamento mes
        $verificaAtendimentoMes = DB::table('advogado_agendamento')->where('fk_interno', $request->get('interno'))->where('fk_advogado', $dado[0]->id)->whereMonth('data', Tdata::getMesData(Tdata::TdataMysql($request->get('dataAtendimento'))))->where('status','!=','Cancelada')->count();

        if($verificaAtendimentoMes >= $qtdAgendamentoMesAdv)
        {
            return redirect()->back()->with('alert-danger', 'A quantidade de agendamentos para o mês foi excedido!');
        }


        // quantidade de parlatorio
        $qtdParlatorio = UnidadesController::getQtdParlatorioPavilhao(UnidadesController::getIdPavilhao($request->get('interno')));
        $tipo = "pavilhao";

        if(is_null($qtdParlatorio))
        {
            $qtdParlatorio = UnidadesController::getQtdParlatorioUnidade(UnidadesController::getIdUP($request->get('interno')));
            $tipo = "unidade";
        }

        if($tipo == "unidade")
        {
            $verificaQtdAgendamentoHorario = DB::table('advogado_agendamento')
                ->select('horario', DB::raw('COUNT(*) as qtd'))
                ->where('data', Tdata::TdataMysql($request->get('dataAtendimento')))
                ->where('advogado_agendamento.horario', Tdata::TdataMysql($request->get('horario')))
                ->where('unidade', UnidadesController::getIdUP($request->get('interno')))
                ->where('status', 'Agendado')
                ->groupBy('horario')->get();

        }else{

            $verificaQtdAgendamentoHorario = DB::table('advogado_agendamento')
                ->leftJoin('interno','interno.id','=','advogado_agendamento.fk_interno')
                ->select('advogado_agendamento.horario', DB::raw('COUNT(*) as qtd'))
                ->where('advogado_agendamento.data', Tdata::TdataMysql($request->get('dataAtendimento')))
                ->where('advogado_agendamento.horario', Tdata::TdataMysql($request->get('horario')))
                ->where('interno.pavilhao', UnidadesController::getIdPavilhao($request->get('interno')))
                ->where('advogado_agendamento.status', 'Agendado')
                ->groupBy('advogado_agendamento.horario')->get();

        }


        if(count($verificaQtdAgendamentoHorario) >= $qtdParlatorio)
        {
            return redirect()->back()->with('alert-danger', 'Quantidade de agendamentos para o horário escolhido excedido!');
        }
        // fim quantidade de parlatorio

         //quantidade de agendamento por dia para um interno
        $verify = DB::table('advogado_agendamento')
            ->where('fk_interno', $request->get('interno'))
            ->where('fk_advogado', $dado[0]->id)
            ->where('unidade', UnidadesController::getIdUP($request->get('interno')))
            ->where('data', Tdata::TdataMysql($request->get('dataAtendimento')))
            ->where('status','!=','Cancelada')->count();

        if($verify >= $qtdAgendamentodiaInternoEspecifico)
        {
            return redirect()->back()->with('alert-danger', 'Atingido o número de agendamentos para este interno na dia de hoje!');
        }

        // verificacao de agendamento no mesmo horario
        $verifyAgendamentoHorario = DB::table('advogado_agendamento')->where('fk_advogado', $dado[0]->id)->where('unidade', UnidadesController::getIdUP($request->get('interno')))->where('data', Tdata::TdataMysql($request->get('dataAtendimento')))->where('horario', $request->get('horario'))->where('status','!=','Cancelada')->count();

        if($verifyAgendamentoHorario > 0)
        {
            return redirect()->back()->with('alert-danger', 'Já houve um agendamento para este horário!');
        }


        // verificacao de quantidade de agendamentos no dia
        $verifyAgendamentoDia = DB::table('advogado_agendamento')->where('fk_advogado', $dado[0]->id)->where('unidade', UnidadesController::getIdUP($request->get('interno')))->where('data', Tdata::TdataMysql($request->get('dataAtendimento')))->where('status','!=','Cancelada')->count();

        if($verifyAgendamentoDia >= $qtdAgendamentoDiaAdv)
        {
            return redirect()->back()->with('alert-danger', 'Número máximo de agendamento para o dia Atingido!');
        }


        // fim das validacoes

        $insert = DB::table('advogado_agendamento')->insert([
            'fk_interno' => $request->get('interno'),
            'fk_advogado' => $dado[0]->id,
            'unidade' => UnidadesController::getIdUP($request->get('interno')),
            'data' => Tdata::TdataMysql($request->get('dataAtendimento')),
            'horario' => $request->get('horario'),
            'status' => "Agendado",
            'created_at' => date("Y-m-d"),
        ]);

        $idInsert = DB::getPdo()->lastInsertId();
        if ($insert) {

            LogController::log($idInsert, "advogado_agendamento","add", Auth::user()->cpf, "Agendamento de Atendimento pelo Advogado - Unidade: ".UnidadesController::getIdUP($request->get('interno'))." - Data: ".$request->get('dataAtendimento')." - Horario: ".$request->get('horario')." - Status: Agendado");

            return redirect()->route('oab.historicoAgendamento')->with('alert-success', 'O agendamento foi realizado com sucesso!');

        }else{

            return redirect('oab.historicoAgendamento')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }

    public function cancelarAgendamento()
    {
        LogController::logAcessoUser();

        if (Auth::user()->orgao != "DPE-RN") {

            if (Auth::user()->tipo != 10) {

            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');

            }

        }


        $idAdv = DB::table('advogado')->select('id','cpf')->where('cpf',Auth::user()->cpf)->get();

        $dados = DB::table('advogado_agendamento')
            ->leftJoin('interno','interno.id','=','advogado_agendamento.fk_interno')
            ->select('advogado_agendamento.*', DB::raw("DATE_FORMAT(advogado_agendamento.data,'%d/%m/%Y') as data"), 'interno.nome')
            ->where('advogado_agendamento.fk_advogado', $idAdv[0]->id)
            ->where('advogado_agendamento.status','!=','Cancelada')
            ->where('advogado_agendamento.status','!=','Atendido')
            ->whereDate('advogado_agendamento.data', '>=', DB::raw('CURDATE()'))
            ->orderBy('advogado_agendamento.data','ASC')->get();

        return view('oab.agendamentoAtendimento.cancelamento', compact('dados'));
    }

    public function cancelarAgendamentoUpdate($id)
    {
        LogController::logAcessoUser();

        if (Auth::user()->orgao != "DPE-RN") {

            if (Auth::user()->tipo != 10) {

            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');

            }

        }


        $idAdv = DB::table('advogado')->select('id','cpf')->where('cpf',Auth::user()->cpf)->get();

        $updateAgendamento= DB::table('advogado_agendamento')->where('id',$id)->where('fk_advogado',$idAdv[0]->id)->update([
                'status' => "Cancelada",
                'update_at' => date("Y-m-d"),
            ]
        );

        if ($updateAgendamento) {

            LogController::log($id, "advogado_agendamento", "update", Auth::user()->cpf, "cancelamento de Agendamento");

            return redirect()->route('oab.historicoAgendamento')->with('alert-success', 'O agendamento foi cancelado com sucesso!');

        }else{

            return redirect('oab.historicoAgendamento')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }

    public function historicoAgendamento()
    {
        LogController::logAcessoUser();

        if (Auth::user()->orgao != "DPE-RN") {

            if (Auth::user()->tipo != 10) {

            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');

            }
        }



        $idAdv = DB::table('advogado')->select('id','cpf')->where('cpf',Auth::user()->cpf)->get();

        $dados = DB::table('advogado_agendamento')
            ->leftJoin('interno','interno.id','=','advogado_agendamento.fk_interno')
            ->select('advogado_agendamento.*', DB::raw("DATE_FORMAT(advogado_agendamento.data,'%d/%m/%Y') as data"), 'interno.nome')
            ->where('advogado_agendamento.fk_advogado', $idAdv[0]->id)
            ->orderBy('advogado_agendamento.data','ASC')
            ->orderBy('advogado_agendamento.horario','ASC')->get();

        return view('oab.agendamentoAtendimento.historicoAgendamento', compact('dados'));
    }

    public function alterarSenha()
    {
        LogController::logAcessoUser();

        return view('oab.alterarSenha.alterarSenha');
    }

    public function alterarSenhaStore(Request $request)
    {
        LogController::logAcessoUser();

        $userData = DB::table("users")->where('cpf', Auth::user()->cpf)->get();

        $nome = explode(" ",$userData[0]->name);

        $senhaAtual = $request->get('senha');
        $novaSenha = $request->get('novaSenha');
        $novaSenhaR = $request->get('novaSenhaR');

        $senhasProibidas = collect(['123456', '1234567','12345678','123456789','321321','654321','456789','987654','147258','963852','abc123','123abc','senhasiapen','siapen','111111','1234567890','password','666666','222222','333333','444444','555555','777777','888888','999999','1q2w3e4r', strtoupper($nome[0]), strtolower($nome[0])]);

        if($novaSenha != $novaSenhaR)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'As senhas não coincidem!');
        }

        if($senhasProibidas->contains($novaSenha) )
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Senha muito fácil! Não é permitido senha com sequencias numericas, Ex.: 123456, 321123 ou seu nome.');
        }


        if (Hash::check($senhaAtual, $userData[0]->password)) {

            $updateSenha = DB::table("users")->where('cpf', Auth::user()->cpf)->update(['password'=> Hash::make($novaSenha)]);

            if($updateSenha)
            {
                LogController::log($userData[0]->id,'user','update', Auth::user()->cpf,"Alteração de senha de acesso ao SIAPEN realizada pelo usuario");
                return redirect()->route('oab.home')->with('alert-success', 'Senha alterada com sucesso!');
            }

        }else{

            return redirect()->back()->withInput()->with('alert-danger', 'A senha atual não confere!');
        }

    }

    public static function multiPropertySort($collection, array $sorting_instructions){

        return $collection->sort(function ($a, $b) use ($sorting_instructions){

            foreach($sorting_instructions as $sorting_instruction){

                $a[$sorting_instruction['column']] = (isset($a[$sorting_instruction['column']])) ? $a[$sorting_instruction['column']] : '';
                $b[$sorting_instruction['column']] = (isset($b[$sorting_instruction['column']])) ? $b[$sorting_instruction['column']] : '';

                if(empty($sorting_instruction['order']) or strtolower($sorting_instruction['order']) == 'asc'){
                    $x = ($a[$sorting_instruction['column']] <=> $b[$sorting_instruction['column']]);
                }else{
                    $x = ($b[$sorting_instruction['column']] <=> $a[$sorting_instruction['column']]);

                }

                if($x != 0){
                    return $x;
                }

            }

            return 0;

        });
    }

}
