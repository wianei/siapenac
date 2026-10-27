<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a Notificacao
|
*/

namespace App\Http\Controllers\SaudeAssistencia;

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

class SaudeAssistenciaController extends Controller
{
    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->middleware('auth');


    }

    public function callAction($method, $parameters)
    {
        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::verifyPermission("SaudeAssistenciaController", $method);
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

//        $metodosComum = collect(['cadastroAtendimento','cadastroAtendimentoStore','showAtendimento','editarAtendimento','editarAtendimentoStore','excluirAtendimento','internosAtendimento','internosAtendimentoStore','listaInternosAtendimento','atendimentoRealizado']);
//
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


    public function cadastroAtendimento()
    {
        $lotacaoServidor = FacadeController::getLotacaoServidor(Auth::user()->cpf);

        $unidades = DB::table('unidade')->where('sigla',$lotacaoServidor)->orderBy('sigla', 'ASC')->get();

        return view('saudeAssistencia.registroAtendimento.registroAtendimento', compact('unidades'));
    }

    public function cadastroAtendimentoStore(Request $request)
    {
        //dd($request->input());

        $internos = $request->get('internos');
        $x = 0;
        foreach($internos as $interno)
        {
            $insertSaudeAssist = DB::table('saude_assistencia')->insert([
               'unidade' => $request->get('unidade_prisional'),
               'setor' => $request->get('setor'),
               'fk_interno' => $interno,
               'data_atendimento' => Tdata::TdataMysql($request->get('dataAtendimento')),
               'hora_atendimento' => $request->get('horaAtendimento'),
               'crm_medico' => $request->get('crmMedico'),
               'nome_medico' => $request->get('nomeMedico'),
               'descricao' => $request->get('descricao'),
               'created_at' => Tdata::TtimeStampHojeMysql(),
            ]);

            $idInsert = DB::getPdo()->lastInsertId();

            $dadosInterno = InternoController::getDadosInterno($interno);

            LogController::log($idInsert,"saude_assistencia","add",Auth::user()->cpf,"Inserção de registro de atendimento médico.<br>Interno: ".$dadosInterno[0]->nome." (".$interno.")");

            $x ++;

        }

        if($x >= 1)
        {
            return redirect()->route('saudeAssistencia.historicoAtendimento')->with('alert-success', 'Registro Realizado com sucesso!');

        }
    }

    public static function historicoAtendimento(Request $request)
    {

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $unidades = DB::table('unidade')->where('status', 1)->orderBy('sigla', 'ASC')->get();

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if(!strpos($url,"?")) {

            $dados = false;

        }else{

            $saudeAssist = DB::table('saude_assistencia');
            $saudeAssist->leftJoin('unidade', 'unidade.id', '=', 'saude_assistencia.unidade');
            $saudeAssist->leftJoin('interno', 'interno.id', '=', 'saude_assistencia.fk_interno');

            $unidade = $request->get('unidade');
            $setor = $request->get('setor');
            $dataInicioAtendimento = $request->get('dataInicioAtendimento');
            $dataTerminoAtendimento = $request->get('dataTerminoAtendimento');
            $internos = $request->get('internos');

            if($unidade != null)
            {
                $saudeAssist->where('unidade', $unidade);
            }else{
                $saudeAssist->where('unidade', $lotacaoServidor);
            }

            if($setor != null)
            {
                $saudeAssist->where('setor', $setor);
            }

            if($dataInicioAtendimento != null)
            {
                $saudeAssist->where('data_atendimento', '>=', Tdata::TdataMysql($dataInicioAtendimento));
            }

            if($dataTerminoAtendimento != null)
            {
                $saudeAssist->where('data_atendimento', '<=', Tdata::TdataMysql($dataTerminoAtendimento));
            }
            if(!is_null($internos)){
                if(count($internos) > 0)
                {
                    $saudeAssist->whereIn('saude_assistencia.fk_interno', $internos);
                }
            }

            $saudeAssist->where('saude_assistencia.status', 'A');

            // passa a query para a paginacao "->appends..."
            $dados = $saudeAssist->select('saude_assistencia.*', DB::raw("DATE_FORMAT(saude_assistencia.data_atendimento,'%d/%m/%Y') as data_atendimento"),'unidade.sigla AS unidadeNome','interno.nome AS nomeInterno','interno.id AS idInterno')->orderBy('saude_assistencia.data_atendimento', 'DESC')->orderBy('saude_assistencia.hora_atendimento', 'DESC')->paginate(50)->appends($request->input());


        }

        //dd($dados);

            return view('saudeAssistencia.buscarAtendimentos.buscarAtendimentos', compact('unidades','dados'));

    }

    public function showAtendimento($id)
    {

        $dadosAtendimento = DB::table("saude_assistencia")->where('id', $id)->get();

        $dados = array();

        foreach($dadosAtendimento as $dadosAtend)
        {
            $dadosInterno = InternoController::getDadosPrincipaisInterno($dadosAtend->fk_interno);
//            dd($dadosInterno);

            if(count(LogController::getDadosLog($id, "saude_assistencia", "add")) != 0) {
                $dadosLog = LogController::getDadosLog($id, "saude_assistencia", "add");
                $dadosUser = DB::table('users')->where('cpf', $dadosLog[0]->cpf_usuario)->select('name', 'cpf')->get();
                $dadosLog[0]->cpf_usuario = $dadosUser[0]->name;
                $log = "Resgistrado por: " . $dadosLog[0]->cpf_usuario . " (" . $dadosUser[0]->cpf . ") em " . Tdata::TtimestampBR($dadosLog[0]->created_at);
            }
            else{
                $log = "Sem log registrado.";
            }

            $unidade = UnidadesController::getNomeUp($dadosAtend->unidade);
            $fotoInterno = InternoController::getFotoInterno($dadosAtend->fk_interno);

            array_push($dados, array(
                'unidade' => $unidade,
                'setor' => $dadosAtend->setor,
                'interno' => $dadosInterno[0]['nome'],
                'localizacao' => $dadosInterno[0]['sigla'],
                'prontuario' => $dadosAtend->fk_interno,
                'foto' => asset($fotoInterno),
                'dataAtendimento' => Tdata::TdataBR($dadosAtend->data_atendimento),
                'horaAtendimento' => $dadosAtend->hora_atendimento,
                'descricao' => $dadosAtend->descricao,
                "log" => $log,
            ));
        }

        return view('saudeAssistencia.showAtendimento.showAtendimento', compact('dados'));

    }

    public function editarAtendimento($id)
    {
        $dados = DB::table("saude_assistencia")->where('id', $id)->get();
        $dadosLog = LogController::getDadosLogService($id, "saude_assistencia");
        
        if($dados[0]->unidade != UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf)))
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para alterar os atendimentos de outra unidade!');

        }

        if(Auth::user()->cpf != $dadosLog->cpf_usuario)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para alterar os atendimentos criados por outro usuário');

        }

        $dados[0]->data_atendimento = Tdata::TdataBR($dados[0]->data_atendimento);

        $dadosInterno = InternoController::getDadosPrincipaisInterno($dados[0]->fk_interno);

        $nomeInterno = $dadosInterno[0]['nome'];

        return view('saudeAssistencia.registroAtendimento.registroAtendimento', compact('dados','nomeInterno','id'));

    }

    public function editarAtendimentoStore(Request $request, $id)
    {
        //dd($request->input(),$id);
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $diferencas = array();


        $dadosBD = DB::table("saude_assistencia")->where('id', $id)->get();

        $dataForm = array('fk_interno'  => $dadosBD[0]->fk_interno, 'setor' => $request->get('setor'), 'data_atendimento' => Tdata::TdataMysql($request->get('dataAtendimento')), 'hora_atendimento' => $request->get('horaAtendimento'), 'descricao' => $request->get('descricao'), 'unidade' => $lotacaoServidor, 'crm_medico' => $request->get('crmMedico'), 'nome_medico' => $request->get('nomeMedico'));

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


        $update = DB::table('saude_assistencia')->where('id', $id)->update([
            'setor' => $request->get('setor'),
            'data_atendimento' => Tdata::TdataMysql($request->get('dataAtendimento')),
            'hora_atendimento' => $request->get('horaAtendimento'),
            'crm_medico' => $request->get('crmMedico'),
            'nome_medico' => $request->get('nomeMedico'),
            'descricao' => $request->get('descricao'),
            'update_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        if($id)
        {
            $textoLog = implode(" ", $diferencas);
            LogController::log($id,"saude_assistencia","update", Auth::user()->cpf, $textoLog);

            return redirect()->route('saudeAssistencia.historicoAtendimento')->with('alert-success', 'Alteração Realizada com sucesso!');
        }
    }

    public function excluirAtendimento($id)
    {
        $dados = DB::table("saude_assistencia")->where('id', $id)->get();
        $dadosLog = LogController::getDadosLogService($id, "saude_assistencia");
        
        if($dados[0]->unidade != UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf)))
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para alterar os atendimentos de outra unidade!');

        }

        if(Auth::user()->cpf != $dadosLog->cpf_usuario)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para alterar os atendimentos criados por outro usuário');

        }

        $update = DB::table('saude_assistencia')->where('id', $id)->update([
            'status' => 'I',
            'update_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        if($id)
        {
            LogController::log($id,"saude_assistencia","delete", Auth::user()->cpf, "Exclusão de atendimento");

            return redirect()->route('saudeAssistencia.historicoAtendimento')->with('alert-success', 'Exclusão Realizada com sucesso!');
        }

    }

    public function internosAtendimento()
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $lista = DB::table('saude_assistencia_interno_atendimento')
            ->leftJoin('interno', 'interno.id', '=', 'saude_assistencia_interno_atendimento.fk_interno')
            ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
            ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
            ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
            ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
            ->leftJoin('foto', function ($query) {
                $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('tipo', 'principal');
            })
            ->where('interno.unidade_prisional', $lotacaoServidor)
            ->select('interno.nome', 'interno.id', 'interno.periculosidade', 'interno.unidade_prisional', 'interno.pavilhao', 'interno.ala', 'interno.cela', 'interno.faccao', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" ELSE "Não" END) AS lideranca'), 'foto.nome_arquivo AS foto', DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'),'pavilhao.nome as pvNome', 'ala.nome as alNome', 'cela.nome as celNome', 'saude_assistencia_interno_atendimento.tipo_atendimento','saude_assistencia_interno_atendimento.prioridade','saude_assistencia_interno_atendimento.descricao', 'saude_assistencia_interno_atendimento.fk_interno')
            ->orderBy('saude_assistencia_interno_atendimento.tipo_atendimento', 'ASC')
            ->orderBy('saude_assistencia_interno_atendimento.prioridade', 'ASC')
            ->orderBy('pavilhao.nome', 'ASC')
            ->orderBy('ala.nome', 'ASC')
            ->orderBy('cela.nome', 'ASC')
            ->orderBy('interno.nome', 'ASC')
            ->where('saude_assistencia_interno_atendimento.status', 'A')
            ->get();

        return view('saudeAssistencia.internosParaAtendimento.internosParaAtendimento', compact('lista'));

    }

    public function internosAtendimentoStore(Request $request)
    {
        //dd($request->input());

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $diferencas = array();

        $internosRemover = $request->get('internosRemover');

        if(isset($internosRemover)){
            foreach ($internosRemover as $idInterno) {

                $dadosRemover = explode("/",$idInterno);
                $idInternoRemover = $dadosRemover[0];
                $tipoAtendimentoRemover = $dadosRemover[1];

                $getLista = DB::table('saude_assistencia_interno_atendimento')->where('unidade', $lotacaoServidor)->where('fk_interno', $idInternoRemover)->where('tipo_atendimento', $tipoAtendimentoRemover)->where('status', 'A')->get();


                $nomeInterno = InternoController::getInternoNome($idInternoRemover);
                $alteracoes = "Remoção de interno na Lista para atendimento ".$tipoAtendimentoRemover.". ".$nomeInterno."(".$idInternoRemover.")<br>Data: ".Tdata::TdataHoje()."<br>Unidade: ".$lotacaoServidor."";

                LogController::log($getLista[0]->id,"saude_assistencia_interno_atendimento","delete", Auth::user()->cpf, $alteracoes);

                $deleteInterno = DB::table('saude_assistencia_interno_atendimento')->where('unidade', $lotacaoServidor)->where('fk_interno', $idInternoRemover)->where('tipo_atendimento', $tipoAtendimentoRemover)->where('status', 'A')->update(['status'=>'I']);

            }

        }

        $internos = $request->get('internos');
        if(isset($internos))
        {

            $qtdInternos = count($request->get('internos'));
            $tipoAtendimento = $request->get('tipoAtendimento');
            $prioridade = $request->get('prioridade');
            $descricao = $request->get('descricao');


            for ($x = 0; $x < $qtdInternos; $x++) {

                $verificaInterno = DB::table('saude_assistencia_interno_atendimento')->where('fk_interno', $internos[$x])->where('unidade', $lotacaoServidor)->where('status', 'A')->where('tipo_atendimento', $tipoAtendimento[$x])->get();


                if (empty(json_decode($verificaInterno, true))) {
                    $insertInterno = DB::table('saude_assistencia_interno_atendimento')->insert([
                            'unidade' => $lotacaoServidor,
                            'fk_interno' => $internos[$x],
                            'tipo_atendimento' => $tipoAtendimento[$x],
                            'prioridade' => $prioridade[$x],
                            'descricao' => $descricao[$x],
                            'status' => 'A',
                            'created_at' => Tdata::TtimeStampHojeMysql(),
                        ]

                    );

                    $idInsertInterno = DB::getPdo()->lastInsertId();

                    $nomeInterno = InternoController::getInternoNome($internos[$x]);
                    $alteracoes = "Inserção de interno na Lista para atendimento ".$tipoAtendimento[$x]." com prioridade ".$prioridade[$x].". ".$nomeInterno."(".$internos[$x].")<br>Data: ".Tdata::TdataHoje()."<br>Unidade: ".$lotacaoServidor."";

                    LogController::log($idInsertInterno,"saude_assistencia_interno_atendimento","add", Auth::user()->cpf, $alteracoes);

                }
            }

        }

        if ($lotacaoServidor) {

             return redirect()->route('saudeAssistencia.listaInternosAtendimento')->with('alert-success', 'Interno(s) inseridos na lista com sucesso!');


        }else{

            return redirect()->back()->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }

    }

    public function listaInternosAtendimento()
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $lista = DB::table('saude_assistencia_interno_atendimento')
            ->leftJoin('interno', 'interno.id', '=', 'saude_assistencia_interno_atendimento.fk_interno')
            ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
            ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
            ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
            ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
            ->leftJoin('foto', function ($query) {
                $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('tipo', 'principal');
            })
            ->where('interno.unidade_prisional', $lotacaoServidor)
            ->select('interno.nome', 'interno.id', 'interno.periculosidade', 'interno.unidade_prisional', 'interno.pavilhao', 'interno.ala', 'interno.cela', 'interno.faccao', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" ELSE "Não" END) AS lideranca'), 'foto.nome_arquivo AS foto', DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'),'pavilhao.nome as pvNome', 'ala.nome as alNome', 'cela.nome as celNome', 'saude_assistencia_interno_atendimento.tipo_atendimento','saude_assistencia_interno_atendimento.prioridade','saude_assistencia_interno_atendimento.descricao', 'saude_assistencia_interno_atendimento.fk_interno', 'saude_assistencia_interno_atendimento.id AS idSaudeAssit')
            ->where('saude_assistencia_interno_atendimento.status', 'A')
            ->orderBy('saude_assistencia_interno_atendimento.tipo_atendimento', 'ASC')
            ->orderBy('saude_assistencia_interno_atendimento.prioridade', 'ASC')
            ->orderBy('saude_assistencia_interno_atendimento.created_at', 'ASC')
            ->orderBy('interno.nome', 'ASC')
            ->get();

        $pavilhao = UnidadesController::getPavilhoes($lotacaoServidor);


        return view('saudeAssistencia.internosParaAtendimento.listaParaAtendimento', compact('lista','pavilhao'));

    }

    public function atendimentoRealizado(Request $request)
    {
        //dd($request->input());

        $atendimentos = $request->get('internosCelaCheck');
        if(is_null($atendimentos)){
            return redirect()->route('saudeAssistencia.listaInternosAtendimento')->with('alert-danger', 'Nenhum atedimento foi selecionado!');
        }
        foreach($atendimentos as $atendimento)
        {
            $updateAtendimento = DB::table('saude_assistencia_interno_atendimento')->where('id', $atendimento)->update([
                'status' => 'I',
                'atendido' => 'S',
                'data_atendimento' => Tdata::TdataMysql($request->get('dataAtendimento'))

            ]);

        }

        return redirect()->route('saudeAssistencia.listaInternosAtendimento')->with('alert-success', 'Atendimentos realizados com sucesso!');

    }
}