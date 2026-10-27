<?php


/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados ao interno
|
*/

namespace App\Http\Controllers\Interno;

use App\Classes\Constantes;
use App\Classes\GeocodingMaps;
use App\Classes\Tdata;
use App\Contato;
use App\Http\Controllers\Advogado\AdvogadoController;
use App\Http\Controllers\ArtigosProcessos\ArtigosProcessosController;
use App\Http\Controllers\Escolta\EscoltaController;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Foto\FotoController;
use App\Http\Controllers\Gep\GepController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Movimentacoes\MovimentacoesController;
use App\Http\Controllers\Usuario\UsuarioController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Http\Controllers\Visita\VisitaController;
use App\Interno;
use App\Outrosnomes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Jobs\Confere;
use App\Http\Controllers\Permissoes\PermissoesController;


class InternoController extends Controller
{
    private $interno;
    public function __construct(Interno $interno)
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->$interno = $interno;
        $this->middleware('auth');

    }

    public function callAction($method, $parameters)
    {
        LogController::logAcessoUser();
        
        $except = collect(["validarCadastro"]);
        
        if(!$except->contains($method))
        {
            $retornoPermissao = PermissoesController::verifyPermission("InternoController", $method);
            
            if($retornoPermissao == false)
            {
                return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
            }
        }

//        $metodosAdm = collect(['atestadosCertidoes','atestadosCertidoesStore','cadastro','store','editar','update','reativar','reativarFunction','documentoDelete']);
//        $metodosComum = collect(['separacao','saidaSancaoTriagem','saidaSancaoTriagemStore','confereGeral','confereGeralStore']);
//        $metodosExterno = collect(['buscar','show']);
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
//
//        if($metodosExterno->contains($method))
//        {
//            if (Auth::user()->tipo < 100) {
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

    public static function getInternosUnidadeData($unidade, $data){
                
        $internosHj = DB::table("interno")->where('status','A')->where('unidade_prisional', $unidade)->pluck('id')->toArray();
        
        $datas = array_reverse(Tdata::getIntervaloDatas($data, date("Y-m-d")));
               
        
        $internosData = $internosHj;
        $keyUltimoDia = count($datas)-1;
        foreach($datas as $key => $data)
        {
            //dd($key, $keyUltimoDia);
            
            // retira as entradas
            $internosEntrada = self::getInternosEntradaUnidadeData($unidade, $data)->toArray();
            foreach($internosEntrada as $interno)
            {
                if (($key = array_search($interno, $internosData)) !== false) {
                    unset($internosData[$key]);
                }            

            }
            //$internosData = array_diff($internosData, $internosEntrada);
            
            // insere as saidas se nao for o ultimo dia
            if($key != $keyUltimoDia){
                $internosSaida = self::getInternosSaidaUnidadeData($unidade, Tdata::subDias($data, 1))->toArray();
                if(count($internosSaida) > 0)
                {
                    foreach($internosSaida as $internoSaida)
                    {
                        if(!in_array($internoSaida, $internosData))
                        {
                            
                            array_push($internosData, $internoSaida);
                        }
                    }
                }
            }
            
            
        }
        
        return $internosData;
        
        // array_diff($internosData, $internosHj) -> array com internos que estavam na data mas nao estao hoje
        //dd($internosHj, $internosData, array_diff($internosData, $internosHj));
        
        
        
    }
    
    public static function getInternosSaidaUnidadeData($unidade, $data)
    {
        $internos = DB::table("movimentacao_interno")
                ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                ->whereDate('movimentacao.data', $data)
                ->where('movimentacao_interno.up_anterior', $unidade)
                ->where('movimentacao_interno.status', 'Finalizada')
                ->whereIn('tipo', ['alvara','evasao','extincaopunibilidade','extradicao','fuga','livramentocondicional','obito','prisaodomiciliar','progressaoaberto','recambiamento','saidajudicial','transferencia'])
                ->pluck('movimentacao_interno.fk_interno');
        
        
        return $internos;
    }
    
    public static function getInternosEntradaUnidadeData($unidade, $data)
    {
        $internos = DB::table("movimentacao_interno")
                ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                ->whereDate('movimentacao.data', $data)
                ->where('movimentacao.unidade_destino', $unidade)
                ->where('movimentacao_interno.status', 'Finalizada')
                ->whereIn('tipo', ['entradaunidadeprisional','transferenciaentrada'])
                ->pluck('movimentacao_interno.fk_interno');
        
        
        return $internos;
    }
    
    ################################## !calculo de internos em determinada data ########################################################
    
    public static function getRegime($id)
    {
        $dado = DB::table('interno')->where('id', $id)->select('regime')->get();
        if(isset($dado[0])){
            if(is_null($dado[0]->regime)){
                return '';
            }
            else {
                return $dado[0]->regime;
            }
        } else{
            return '';
        }
    }
    public static function getUnidadeInterno($id, $nomeSigla=false)
    {
        $dado = DB::table('interno')->where('interno.id',$id)
            ->leftJoin('unidade','unidade.id','=','interno.unidade_prisional')
            ->select('unidade.nome','unidade.sigla')->get();

        if($nomeSigla == true)
        {
            return $dado[0]->nome." (".$dado[0]->sigla.")";

        }else{

            return $dado[0]->nome;
        }

    }
    public static function getFotoInterno($id)
    {
        $foto = DB::table('foto')->where('fk_id', $id)->where('fk_tabela','interno')->where('tipo','principal')->get();

        if(json_decode($foto, true) == [])
        {
            return "imagens/usuario/avatarInterno.png";
        }else{
            return "storage/app/arquivos/internos/fotos/".$id."/".$foto[0]->nome_arquivo;
        }

    }

    public static function getFotoPrincipalInternoBase64($id)
    {
        $foto = DB::table('foto')->where('fk_id', $id)->where('fk_tabela','interno')->where('tipo','principal')->get();

        if(count($foto) == 0)
        {
            $imagem = 'imagens/usuario/avatarInterno.png';
            $imgBase64 = self::imagensInternoMergeCpfUser($id, public_path($imagem));

            return $imgBase64;
            
        }else{
            
            $imagem = 'app/arquivos/internos/fotos/' . $id . '/' . $foto[0]->nome_arquivo;
            $imgBase64 = self::imagensInternoMergeCpfUser($id, storage_path($imagem));
            return $imgBase64;
        }

    }

    public static function getFotoInternoCPF($cpf)
    {
        $dados = self::getDadosInternoCpf($cpf);

        $foto = self::getFotoInterno($dados[0]->id);

        return asset($foto);
    }


    public static function getInternoNome($id)
    {
        if(is_null($id)) return "";
        $dado = DB::table('interno')->where('id',$id)->select('nome')->get();
        if(!isset($dado[0])) return "";
        return $dado[0]->nome;
    }

    public static function getFaccaoInterno($id)
    {
        if(is_null($id)) return "";
        $dado = DB::table('interno')->where('id',$id)->select('faccao')->get();
        if(!isset($dado[0])) return "";
        return self::getNomeFaccao($dado[0]->faccao);
    }

    public static function getIsLideranca($id)
    {
        if(is_null($id)) return "";
        $dado = DB::table('interno')->where('id',$id)->select('lideranca')->get();
        if(!isset($dado[0])) return "";
        $lideranca = "";
        if($dado[0]->lideranca == 'S')
        {
            $lideranca = "Sim";
        }else{
            $lideranca = "Não";
        }
        return $lideranca;
    }

    public static function getDadosInternoCpf($cpf)
    {
        $dado = DB::table('interno')->where('cpf',$cpf)->get();
        return $dado;
    }

    public static function getDadosInterno($id)
    {
        $dado = DB::table('interno')->where('id',$id)->get();
        return $dado;
    }

    public static function getDadosPrincipaisInterno($id)
    {
        if(is_null($id)) return "";
        $dado = DB::table('interno')
            ->leftJoin('foto', function ($query) {
                $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('tipo', 'principal');
            })
            ->leftJoin('faccao', function ($query) {
                $query->on('faccao.id', '=', 'interno.faccao');
            })
            ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
            ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
            ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
            ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
            ->where('interno.id', $id)
            ->select('interno.nome','interno.mae','interno.regime', 'interno.id', 'interno.periculosidade', DB::raw("DATE_FORMAT(interno.data_nascimento,'%d/%m/%Y') AS data_nascimento"), 'interno.unidade_prisional', 'interno.pavilhao', 'interno.ala', 'interno.cela', 'interno.faccao','faccao.nome AS facNome', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'foto.nome_arquivo AS foto',DB::raw('IFNULL(foto.legenda,"Não informado") AS fotolegenda'),DB::raw('IFNULL(foto.tipo,"Não informado") AS fototipo'),DB::raw('IFNULL(foto.created_at,"Não informado") AS fotodatainserida'),DB::raw('IFNULL(foto.updated_at,"Não informado") AS fotodataalterada'), DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'))
            ->limit(30)
            ->orderBy('interno.nome', 'ASC')->get();

        return json_decode($dado, true);

    }

    public static function getLotacaoInterno($idInterno)
    {
        if(is_null($idInterno)) return "";
        $lotacao = DB::table('interno')->select('unidade_prisional','pavilhao','ala','cela')->where('id',$idInterno)->get();
        $lotacao = json_decode($lotacao, true)[0];
        return UnidadesController::getSiglaUp($lotacao['unidade_prisional']) . " - " . UnidadesController::getNomePav($lotacao['pavilhao']) . " - " . UnidadesController::getNomeAla($lotacao['ala']) . " - " . UnidadesController::getNomeCela($lotacao['cela']);
    }

    public static function getIdUnidadeInterno($idInterno)
    {
        $lotacao = DB::table('interno')->select('unidade_prisional')->where('id',$idInterno)->get();

        return $lotacao[0]->unidade_prisional;
    }

    public static function getIdpavilhaoInterno($idInterno)
    {
        if(is_null($idInterno)) return "";
        $lotacao = DB::table('interno')->select('pavilhao')->where('id',$idInterno)->get();
        if(isset($lotacao[0]->pavilhao) && !is_null($lotacao[0]->pavilhao)){
            return $lotacao[0]->pavilhao;
        } else {
            return "";
        }
    }

    public static function getIdCelaInterno($idInterno)
    {
        $lotacao = DB::table('interno')->select('cela')->where('id',$idInterno)->get();

        return $lotacao[0]->cela;
    }

    public static function getOutrosNomesTxt($idInterno){
        $outrosNomes = DB::table('outrosnomes')->where('pk_tabela','interno')->where('pk_id',$idInterno)->get();
        $return = "";
        $i = 0;
        $outrosNomesQtd = count($outrosNomes);
        foreach($outrosNomes as $outroNome){
            if($i != $outrosNomesQtd - 1){
                $return = $return.$outroNome->nome.", ";
            } else {
                $return = $return.$outroNome->nome.".";
            }
            $i++;
        }
        return $return;
    }

    public static function getRelacaoVisita($idInterno, $idVisita)
    {
        $dado = DB::table('visita_interno')->where('fk_interno',$idInterno)->where('fk_visita',$idVisita)->select('grau_parentesco')->get();
        if($dado == collect([])) return 'GRAU DE PARENTESCO NÃO INFORMADO';
        return $dado[0]->grau_parentesco;
    }

    public static function getRelacaoVisitaUnidade($idVisita, $idUnidade)
    {
        $dado = DB::table('visita_interno')

            ->leftJoin('interno', function ($query) {
                $query->on('interno.id', '=', 'visita_interno.fk_interno');
            })
            ->leftJoin('controle_acesso_visita_interno_suspenso', function ($query) {
                $query->on('controle_acesso_visita_interno_suspenso.fk_interno', '=', 'interno.id');
            })
            ->leftJoin('sancao_triagem', function ($query) {
                $query->on('sancao_triagem.fk_interno', '=', 'interno.id');
            })
            ->where('visita_interno.fk_visita',$idVisita)
            ->where('visita_interno.status','A')
            ->where('interno.unidade_prisional', $idUnidade)
            ->where('interno.status', 'A')
            ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
            ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
            ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
            ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
            ->select('visita_interno.*','interno.cela','interno.nome AS nomeInterno', DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'),DB::raw("DATE_FORMAT(sancao_triagem.data_fim,'%d/%m/%Y') AS fimSancaoTriagem"),'sancao_triagem.tipo AS tipoSancaoTriagem','sancao_triagem.status as statusSancaoTriagem','controle_acesso_visita_interno_suspenso.status_suspensao AS vinculoSuspenso')->get();

        return $dado;
    }

//    public static function showPdfOcorrencias()
//    {
//
//        return view('ocorrencias');
//    }


    /*
    |--------------------------------------------------------------------------
    | Funcoes acessadas via ajax
    |--------------------------------------------------------------------------
    |
    */

    public function getSancaoOuTriagemAtiva(Request $request)
    {
        parse_str($request->get('internos'), $idInternos);

        $internosPendencia = array();

        //var_dump($idInternos);
        foreach($idInternos['internos'] as $idInterno)
        {

            $sancaoTriagem = DB::table('sancao_triagem')
                ->where('fk_interno', $idInterno)
                ->where('status', 'A')
                ->select('sancao_triagem.*',DB::raw('(CASE WHEN sancao_triagem.tipo = "sancao" THEN "Sanção Disciplinar" WHEN sancao_triagem.tipo = "triagem" THEN "Triagem" ELSE "Tipo não informado" END) AS tipo'))
                ->get();

            if(count($sancaoTriagem) > 0) {

                array_push($internosPendencia, array(
                    'id' => $idInterno,
                    'nome' => self::getInternoNome($idInterno),
                    'tipo' => $sancaoTriagem[0]->tipo,
                    'dataFim' => Tdata::TdataBR($sancaoTriagem[0]->data_fim)
                ));

            }
        }

        return $internosPendencia;
    }

    public function getSaudeAssistenciaAjax(Request $request)
    {
        $idInterno = $request->get('idInterno');
        $dataInicio = Tdata::TdataMysql($request->get('dataInicio'));
        $dataFinal = Tdata::TdataMysql($request->get('dataFinal'));

        $saudeAssistDados = DB::table('saude_assistencia');
        $saudeAssistDados->leftJoin('unidade', 'unidade.id', '=', 'saude_assistencia.unidade');
        $saudeAssistDados->leftJoin('interno', 'interno.id', '=', 'saude_assistencia.fk_interno');
        $saudeAssistDados->where('saude_assistencia.fk_interno', $idInterno);

        if(!empty($dataInicio) && !empty($dataFinal) )
        {
            $saudeAssistDados->where('data_atendimento', '>=', $dataInicio);
            $saudeAssistDados->where('data_atendimento', '<=', $dataFinal);
        }

        if(!empty($dataInicio) && empty($dataFinal) )
        {
            $saudeAssistDados->where('data_atendimento', '>=', $dataInicio);
        }

        if(empty($dataInicio) && !empty($dataFinal) )
        {
            $saudeAssistDados->where('data_atendimento', '<=', $dataFinal);
        }

        $saudeAssistDados->where('saude_assistencia.status', 'A');
        $saude = $saudeAssistDados->select('saude_assistencia.*', DB::raw("DATE_FORMAT(saude_assistencia.data_atendimento,'%d/%m/%Y') as data_atendimento"),'unidade.sigla AS unidadeNome','interno.nome AS nomeInterno','interno.id AS idInterno')->orderBy('saude_assistencia.data_atendimento', 'DESC')->orderBy('saude_assistencia.hora_atendimento', 'DESC')->get();


        $saudeAssist = array();

        foreach($saude as $item)
        {

            if(count(LogController::getDadosLog($item->id,"saude_assistencia","add")) != 0) {
                $dadosLog = LogController::getDadosLog($item->id, "saude_assistencia", "add");
                $userLog = UsuarioController::getNomeUser($dadosLog[0]->cpf_usuario);
                $textoLog = "Registrado por: " . $userLog . " (" . $dadosLog[0]->cpf_usuario . ") em " . Tdata::TtimestampBR($dadosLog[0]->created_at);
            }
            else{
                $textoLog = "Sem log registrado.";
            }

            array_push($saudeAssist, array(
                'setor' => $item->setor,
                'dataAtendimento' => $item->data_atendimento,
                'diaAtendimento' => substr(Tdata::TdataBR(Tdata::TdataMysql($item->data_atendimento)),0,2),
                'anoAtendimento' => substr(Tdata::TdataBR(Tdata::TdataMysql($item->data_atendimento)),6,4),
                'horaAtendimento' => $item->hora_atendimento,
                'descricao' => $item->descricao,
                'unidadeNome' => $item->unidadeNome,
                'crm' => $item->crm_medico,
                'nomeMedico' => $item->nome_medico,
                'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData(Tdata::TdataMysql($item->data_atendimento))),
                'log' => $textoLog,

            ));
        }

        return $saudeAssist;


    }
    public function getObito(Request $request)
    {
        $data = $request->get('data');

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        //$dadosObito = DB::table("obito")->where('obito_sispern','S')->where('data_obito', Tdata::TdataMysql($data))->get();
        $dadosObito = DB::table("obito")->where('obito_sispern','S')->where('data_obito', Tdata::TdataMysql($data))->get();

        $dadosInterno = array();

        foreach($dadosObito as $relacao)
        {
            $dadosInternos = (self::getDadosPrincipaisInterno($relacao->interno));
            if($dadosInternos[0]['unidade_prisional'] == $lotacaoServidor) {

                array_push($dadosInterno, array(
                    "nome" => $dadosInternos[0]['nome'],
                    "id" => $dadosInternos[0]['id'],
                    "sigla" => $dadosInternos[0]['sigla']
                ));

            }

        }

        return $dadosInterno;

    }
    public function getAtendimentoAdvogadoInternoAjx(Request $request)
    {
        $idInterno = $request->get('idInterno');
        $dataInicio = Tdata::TdataMysql($request->get('dataInicio'));
        $dataFinal = Tdata::TdataMysql($request->get('dataFinal'));

        $advogadoHistoricoDados = DB::table('advogado_atendimento')->where('fk_interno', $idInterno);

        if(!empty($dataInicio) && !empty($dataFinal) )
        {
            $advogadoHistoricoDados->where('data_atendimento', '>=', $dataInicio);
            $advogadoHistoricoDados->where('data_atendimento', '<=', $dataFinal);
        }

        if(!empty($dataInicio) && empty($dataFinal) )
        {
            $advogadoHistoricoDados->where('data_atendimento', '>=', $dataInicio);
        }

        if(empty($dataInicio) && !empty($dataFinal) )
        {
            $advogadoHistoricoDados->where('data_atendimento', '<=', $dataFinal);
        }

        $dadosAtendimentoAdv = $advogadoHistoricoDados->orderBy('data_atendimento', 'DESC')->orderBy('horaInicio', 'DESC')->limit(50)->get();

        $advogadoHistorico = array();

        foreach($dadosAtendimentoAdv as $advogadoHistoricoDado)
        {
            $dadosAdv = AdvogadoController::getDadosAdv($advogadoHistoricoDado->fk_advogado);
            array_push($advogadoHistorico, array(
                'nomeAdv' => $dadosAdv[0]->nome,
                'fotoAdv' => asset(AdvogadoController::getFotoAdv($advogadoHistoricoDado->fk_advogado)),
                'data' => Tdata::TdataBR($advogadoHistoricoDado->data_atendimento),
                'diaAtendimento' => substr(Tdata::TdataBR($advogadoHistoricoDado->data_atendimento),0,2),
                'anoAtendimento' => substr(Tdata::TdataBR($advogadoHistoricoDado->data_atendimento),6,4),
                'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($advogadoHistoricoDado->data_atendimento)),
                'oab' => $dadosAdv[0]->oab_numero."/".$dadosAdv[0]->oab_uf,
                'horaInicio' => $advogadoHistoricoDado->horaInicio,
                'horaTermino' => $advogadoHistoricoDado->horaTermino,
                'duracao' => $advogadoHistoricoDado->duracao."min.",
            ));

        }

        return $advogadoHistorico;


    }
    public function getVisitasInternoAjax(Request $request)
    {
        $idInterno = $request->get('idInterno');
        $dataInicio = Tdata::TdataMysql($request->get('dataInicio'));
        $dataFinal = Tdata::TdataMysql($request->get('dataFinal'));

        $visitanteHistoricoDados = DB::table('controle_acesso_visita_interno')->where('fk_interno', $idInterno);

        if(!empty($dataInicio) && !empty($dataFinal) )
        {
            $visitanteHistoricoDados->where('data', '>=', $dataInicio);
            $visitanteHistoricoDados->where('data', '<=', $dataFinal);
        }

        if(!empty($dataInicio) && empty($dataFinal) )
        {
            $visitanteHistoricoDados->where('data', '>=', $dataInicio);
        }

        if(empty($dataInicio) && !empty($dataFinal) )
        {
            $visitanteHistoricoDados->where('data', '<=', $dataFinal);
        }

        $dadosVisitasInterno = $visitanteHistoricoDados->orderBy('data', 'DESC')->orderBy('horario', 'DESC')->limit(50)->get();

        $visitanteHistorico = array();

        foreach($dadosVisitasInterno as $visitanteHistoricoDado)
        {
            $cpfVisita = VisitaController::getCpfVisita($visitanteHistoricoDado->fk_visita);

            $dadosSaida = DB::table('controle_acesso')->where('tabela', 'visita')->where('cpf', $cpfVisita)->where('data', $visitanteHistoricoDado->data)->where('horario', $visitanteHistoricoDado->horario)->get();

            $duracao = ( strtotime($dadosSaida[0]->horario_saida) - strtotime($visitanteHistoricoDado->horario) ) / 60;

            array_push($visitanteHistorico, array(
                'nomeVisita' => VisitaController::getNomeVisita($visitanteHistoricoDado->fk_visita),
                'fotoVisita' => asset(VisitaController::getFotoVisita($visitanteHistoricoDado->fk_visita)),
                'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($visitanteHistoricoDado->data)),
                'relacao' => InternoController::getRelacaoVisita($visitanteHistoricoDado->fk_interno, $visitanteHistoricoDado->fk_visita),
                'diaEntrada' => substr(Tdata::TdataBR($visitanteHistoricoDado->data),0,2),
                'anoEntrada' => substr(Tdata::TdataBR($visitanteHistoricoDado->data),6,4),
                'horaEntrada' => $visitanteHistoricoDado->horario,
                'dataEntrada' => Tdata::TdataBR($visitanteHistoricoDado->data),
                'horaSaida' => $dadosSaida[0]->horario_saida,
                'dataSaida' => Tdata::TdataBR($dadosSaida[0]->data_saida),
                'duracao' => $duracao."min"
            ));
        }

        return $visitanteHistorico;

    }

    public function buscaNomeAjax(Request $request)
    {
        $lotacaoServidor = FacadeController::getLotacaoServidor(Auth::user()->cpf);

        // restricao para consultar apenas internos da unidade do servidor
        if($request->get('tipo') == 'restrito'){


            if (is_numeric($request->get('nome'))) {
                $dado = DB::table('interno')
//                    ->leftJoin('foto', function ($query) {
//                        $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('tipo', 'principal');
//                    })
                    ->leftJoin('faccao',function($query){
                        $query->on('faccao.id','=','interno.faccao');
                    })
                    ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
                    ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
                    ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
                    ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
                    ->where('interno.id', $request->get('nome'))
                    ->where('unidade.sigla', $lotacaoServidor)
                    ->where('interno.status','A')
                    ->whereNotNull('interno.pavilhao')
                    ->whereNotNull('interno.ala')
                    ->whereNotNull('interno.cela')
                    ->select(DB::raw('UPPER(interno.nome) AS nome'),DB::raw('IFNULL(interno.regime,"Não informado") AS regime'), 'interno.id', DB::raw('IFNULL(interno.sentenciado,"Não informado") AS sentenciado'), DB::raw('IFNULL(interno.periculosidade,"Não informado") AS periculosidade'), 'interno.faccao AS facId',DB::raw('IFNULL(faccao.nome,"Não informado") AS faccao'), DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'))
                    ->limit(30)
                    ->orderBy('interno.nome', 'ASC')->get();
            } else {
                $dado = DB::table('interno')
//                    ->leftJoin('foto', function ($query) {
//                        $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('tipo', 'principal');
//                    })
                    ->leftJoin('faccao',function($query){
                        $query->on('faccao.id','=','interno.faccao');
                    })
                    ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
                    ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
                    ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
                    ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
                    ->where('interno.nome', 'LIKE', '%' . $request->get('nome') . '%')
                    ->where('unidade.sigla', $lotacaoServidor)
                    ->where('interno.status','A')
                    ->whereNotNull('interno.pavilhao')
                    ->whereNotNull('interno.ala')
                    ->whereNotNull('interno.cela')
                    ->select(DB::raw('UPPER(interno.nome) AS nome'),DB::raw('IFNULL(interno.regime,"Não informado") AS regime'), 'interno.id', DB::raw('IFNULL(interno.sentenciado,"Não informado") AS sentenciado'), DB::raw('IFNULL(interno.periculosidade,"Não informado") AS periculosidade'), 'interno.faccao AS facId',DB::raw('IFNULL(faccao.nome,"Não informado") AS faccao'), DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'))
                    ->limit(30)
                    ->orderBy('interno.nome', 'ASC')->get();
            }
            return $dado;
        }

        // consulta o interno de qualquer unidade
        if($request->get('tipo') == 'geral'){


            if (is_numeric($request->get('nome'))) {

                $dado = DB::table('interno')
//                    ->leftJoin('foto', function ($query) {
//                        $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('tipo', 'principal');
//                    })

                    ->leftJoin('faccao',function($query){
                        $query->on('faccao.id','=','interno.faccao');
                    })

                    ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
                    ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
                    ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
                    ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
                    ->where('interno.id', $request->get('nome'))
                    ->where('interno.status','A')
                    ->whereNotNull('interno.pavilhao')
                    ->whereNotNull('interno.ala')
                    ->whereNotNull('interno.cela')
                    ->select(DB::raw('UPPER(interno.nome) AS nome'),DB::raw('IFNULL(interno.regime,"Não informado") AS regime'), 'interno.id', DB::raw('IFNULL(interno.periculosidade,"Não informado") AS periculosidade'), 'interno.regime', 'interno.faccao AS facId',DB::raw('IFNULL(faccao.nome,"Não informado") AS faccao'), DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'))
                    ->limit(30)
                    ->orderBy('interno.nome', 'ASC')->get();
            } else {
                $dado = DB::table('interno')
//                    ->leftJoin('foto', function ($query) {
//                        $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('tipo', 'principal');
//                    })
                    ->leftJoin('faccao',function($query){
                        $query->on('faccao.id','=','interno.faccao');
                    })
                    ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
                    ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
                    ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
                    ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
                    ->where('interno.nome', 'LIKE', '%' . $request->get('nome') . '%')
                    ->where('interno.status','A')
                    ->whereNotNull('interno.pavilhao')
                    ->whereNotNull('interno.ala')
                    ->whereNotNull('interno.cela')
                    ->select(DB::raw('UPPER(interno.nome) AS nome'),DB::raw('IFNULL(interno.regime,"Não informado") AS regime'), 'interno.id', DB::raw('IFNULL(interno.periculosidade,"Não informado") AS periculosidade'), 'interno.faccao AS facId',DB::raw('IFNULL(faccao.nome,"Não informado") AS faccao'), DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'))
                    ->limit(30)
                    ->orderBy('interno.nome', 'ASC')->get();
            }
            return $dado;
        }

        // consulta o interno de qualquer unidade ativo e inativo
        if($request->get('tipo') == 'ativInat'){


            if (is_numeric($request->get('nome'))) {

                $dado = DB::table('interno')
//                    ->leftJoin('foto', function ($query) {
//                        $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('tipo', 'principal');
//                    })

                    ->leftJoin('faccao',function($query){
                        $query->on('faccao.id','=','interno.faccao');
                    })

                    ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
                    ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
                    ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
                    ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
                    ->where('interno.id', $request->get('nome'))
                    ->whereNotNull('interno.pavilhao')
                    ->whereNotNull('interno.ala')
                    ->whereNotNull('interno.cela')
                    ->select(DB::raw('UPPER(interno.nome) AS nome'),DB::raw('IFNULL(interno.regime,"Não informado") AS regime'), 'interno.id', DB::raw('IFNULL(interno.periculosidade,"Não informado") AS periculosidade'), 'interno.faccao AS facId',DB::raw('IFNULL(faccao.nome,"Não informado") AS faccao'), DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'))
                    ->limit(30)
                    ->orderBy('interno.nome', 'ASC')->get();
            } else {
                $dado = DB::table('interno')
//                    ->leftJoin('foto', function ($query) {
//                        $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('tipo', 'principal');
//                    })
                    ->leftJoin('faccao',function($query){
                        $query->on('faccao.id','=','interno.faccao');
                    })
                    ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
                    ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
                    ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
                    ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
                    ->where('interno.nome', 'LIKE', '%' . $request->get('nome') . '%')
                    ->whereNotNull('interno.pavilhao')
                    ->whereNotNull('interno.ala')
                    ->whereNotNull('interno.cela')
                    ->select(DB::raw('UPPER(interno.nome) AS nome'),DB::raw('IFNULL(interno.regime,"Não informado") AS regime'), 'interno.id', DB::raw('IFNULL(interno.periculosidade,"Não informado") AS periculosidade'), 'interno.faccao AS facId',DB::raw('IFNULL(faccao.nome,"Não informado") AS faccao'), DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'))
                    ->limit(30)
                    ->orderBy('interno.nome', 'ASC')->get();
            }
            return $dado;
        }

        // consulta o interno da unidade ativo e inativo
        if($request->get('tipo') == 'restrAtivInat'){


            if (is_numeric($request->get('nome'))) {

                $dado = DB::table('interno')
//                    ->leftJoin('foto', function ($query) {
//                        $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('tipo', 'principal');
//                    })

                    ->leftJoin('faccao',function($query){
                        $query->on('faccao.id','=','interno.faccao');
                    })

                    ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
                    ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
                    ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
                    ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
                    ->where('interno.id', $request->get('nome'))
                    ->where('unidade.sigla', $lotacaoServidor)
                    ->whereNotNull('interno.pavilhao')
                    ->whereNotNull('interno.ala')
                    ->whereNotNull('interno.cela')
                    ->select(DB::raw('UPPER(interno.nome) AS nome'),DB::raw('IFNULL(interno.regime,"Não informado") AS regime'), 'interno.id', DB::raw('IFNULL(interno.periculosidade,"Não informado") AS periculosidade'), 'interno.faccao AS facId',DB::raw('IFNULL(faccao.nome,"Não informado") AS faccao'), DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'))
                    ->limit(30)
                    ->orderBy('interno.nome', 'ASC')->get();
            } else {
                $dado = DB::table('interno')
//                    ->leftJoin('foto', function ($query) {
//                        $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('tipo', 'principal');
//                    })
                    ->leftJoin('faccao',function($query){
                        $query->on('faccao.id','=','interno.faccao');
                    })
                    ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
                    ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
                    ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
                    ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
                    ->where('interno.nome', 'LIKE', '%' . $request->get('nome') . '%')
                    ->where('unidade.sigla', $lotacaoServidor)
                    ->whereNotNull('interno.pavilhao')
                    ->whereNotNull('interno.ala')
                    ->whereNotNull('interno.cela')
                    ->select(DB::raw('UPPER(interno.nome) AS nome'),DB::raw('IFNULL(interno.regime,"Não informado") AS regime'), 'interno.id', DB::raw('IFNULL(interno.periculosidade,"Não informado") AS periculosidade'), 'interno.faccao AS facId',DB::raw('IFNULL(faccao.nome,"Não informado") AS faccao'), DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'))
                    ->limit(30)
                    ->orderBy('interno.nome', 'ASC')->get();
            }
            return $dado;
        }

        // restricao para consultar apenas internos ativos, do regime fechado, semiaberto ou aberto e da unidade do servidor
        if($request->get('tipo') == 'saidajudicial'){

            $lotacaoServidor = FacadeController::getLotacaoServidor(Auth::user()->cpf);

            if (is_numeric($request->get('nome'))) {
                $dado = DB::table('interno')
//                    ->leftJoin('foto', function ($query) {
//                        $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('tipo', 'principal');
//                    })
                    ->leftJoin('faccao',function($query){
                        $query->on('faccao.id','=','interno.faccao');
                    })
                    ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
                    ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
                    ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
                    ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
                    ->where('interno.id', $request->get('nome'))
                    ->where('unidade.sigla', $lotacaoServidor)
                    ->where('interno.status','A')
                   /* ->where(function($query){
                        $query->where('interno.regime','<>','FECHADO')
                            ->orWhereNull('interno.regime');
                    })*/
                    ->whereNotNull('interno.pavilhao')
                    ->whereNotNull('interno.ala')
                    ->whereNotNull('interno.cela')
                    ->select(DB::raw('UPPER(interno.nome) AS nome'),DB::raw('IFNULL(interno.regime,"Não informado") AS regime'), 'interno.id', DB::raw('IFNULL(interno.sentenciado,"Não informado") AS sentenciado'), DB::raw('IFNULL(interno.periculosidade,"Não informado") AS periculosidade'), 'interno.faccao AS facId',DB::raw('IFNULL(faccao.nome,"Não informado") AS faccao'), DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'))
                    ->limit(30)
                    ->orderBy('interno.nome', 'ASC')->get();
            } else {
                $dado = DB::table('interno')
//                    ->leftJoin('foto', function ($query) {
//                        $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('tipo', 'principal');
//                    })
                    ->leftJoin('faccao',function($query){
                        $query->on('faccao.id','=','interno.faccao');
                    })
                    ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
                    ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
                    ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
                    ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
                    ->where('interno.nome', 'LIKE', '%' . $request->get('nome') . '%')
                    ->where('unidade.sigla', $lotacaoServidor)
                    ->where('interno.status','A')
                   /* ->where(function($query){
                        $query->where('interno.regime','<>','FECHADO')
                            ->orWhereNull('interno.regime');
                    })*/
                    ->whereNotNull('interno.pavilhao')
                    ->whereNotNull('interno.ala')
                    ->whereNotNull('interno.cela')
                    ->select(DB::raw('UPPER(interno.nome) AS nome'),DB::raw('IFNULL(interno.regime,"Não informado") AS regime'), 'interno.id', DB::raw('IFNULL(interno.sentenciado,"Não informado") AS sentenciado'), DB::raw('IFNULL(interno.periculosidade,"Não informado") AS periculosidade'), 'interno.faccao AS facId',DB::raw('IFNULL(faccao.nome,"Não informado") AS faccao'), DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'))
                    ->limit(30)
                    ->orderBy('interno.nome', 'ASC')->get();
            }
            return $dado;
        }

        // consulta internos de qualquer unidade ativos e inativos
        if($request->get('tipo') == 'geralAtIn'){
            if (is_numeric($request->get('nome'))) {
                $dado = DB::table('interno')
                    ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
                    ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
                    ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
                    ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
                    ->where('interno.id', $request->get('nome'))
                    ->select('interno.nome',DB::raw('IFNULL(interno.regime,"Não informado") AS regime'), 'interno.id', 'interno.status', 'interno.mae', DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'))
                    ->limit(30)
                    ->orderBy('interno.nome', 'ASC')->get();
            } else {
                $dado = DB::table('interno')
                    ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
                    ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
                    ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
                    ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
                    ->where('interno.nome', 'LIKE', '%' . $request->get('nome') . '%')
                    ->select('interno.nome',DB::raw('IFNULL(interno.regime,"Não informado") AS regime'), 'interno.id', 'interno.status', 'interno.mae', DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'))
                    ->limit(30)
                    ->orderBy('interno.nome', 'ASC')->get();
            }
            return $dado;
        }

        if($request->get('tipo') == 'restritoIn'){
            $lotacaoServidor = FacadeController::getLotacaoServidor(Auth::user()->cpf);

            if (is_numeric($request->get('nome'))) {
                $dado = DB::table('interno')
//                    ->leftJoin('foto', function ($query) {
//                        $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('tipo', 'principal');
//                    })
                    ->leftJoin('faccao',function($query){
                        $query->on('faccao.id','=','interno.faccao');
                    })
                    ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
                    ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
                    ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
                    ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
                    ->where('interno.id', $request->get('nome'))
                    ->where('unidade.sigla', $lotacaoServidor)
                    ->where('interno.status','I')
                    ->where('interno.condicao_interno', 638)
                    ->whereNotNull('interno.pavilhao')
                    ->whereNotNull('interno.ala')
                    ->whereNotNull('interno.cela')
                    ->select(DB::raw('UPPER(interno.nome) AS nome'),DB::raw('IFNULL(interno.regime,"Não informado") AS regime'), 'interno.id', DB::raw('IFNULL(interno.sentenciado,"Não informado") AS sentenciado'), DB::raw('IFNULL(interno.periculosidade,"Não informado") AS periculosidade'), 'interno.faccao AS facId',DB::raw('IFNULL(faccao.nome,"Não informado") AS faccao'), DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'))
                    ->limit(30)
                    ->orderBy('interno.nome', 'ASC')->get();
            } else {
                $dado = DB::table('interno')
//                    ->leftJoin('foto', function ($query) {
//                        $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('tipo', 'principal');
//                    })
                    ->leftJoin('faccao',function($query){
                        $query->on('faccao.id','=','interno.faccao');
                    })
                    ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
                    ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
                    ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
                    ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
                    ->where('interno.nome', 'LIKE', '%' . $request->get('nome') . '%')
                    ->where('unidade.sigla', $lotacaoServidor)
                    ->where('interno.status','I')
                    ->where('interno.condicao_interno', 638)
                    ->whereNotNull('interno.pavilhao')
                    ->whereNotNull('interno.ala')
                    ->whereNotNull('interno.cela')
                    ->select(DB::raw('UPPER(interno.nome) AS nome'),DB::raw('IFNULL(interno.regime,"Não informado") AS regime'), 'interno.id', DB::raw('IFNULL(interno.sentenciado,"Não informado") AS sentenciado'), DB::raw('IFNULL(interno.periculosidade,"Não informado") AS periculosidade'), 'interno.faccao AS facId',DB::raw('IFNULL(faccao.nome,"Não informado") AS faccao'), DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'))
                    ->limit(30)
                    ->orderBy('interno.nome', 'ASC')->get();
            }
            return $dado;
        }

    }
    

public static function getImage($res) {
		$img = "";
		$type = "";
	
		if (intval(@imagesx($res)) > 0) {
			$img = $res;
                } else {
			$imginfo = getimagesize($res);

			switch($imginfo[2]) { 
				case 1:
					$type = "GIF";
					if (function_exists("imagecreatefromgif")) {
						$img = imagecreatefromgif($res);
					} else {
						die("Unsupported image type: $type");
					}
					break;
				case 2:
					$type = "JPG";
					if (function_exists("imagecreatefromjpeg")) {
						$img = imagecreatefromjpeg($res);
					} else {
						die("Unsupported image type: $type");
					}
					break;
				case 3:
					$type = "PNG";
					if (function_exists("imagecreatefrompng")) {
						$img = imagecreatefrompng($res);
					} else {
						die("Unsupported image type: $type");
					}
					break;
				case 6:
					$type = "BMP";
					if (function_exists("imagecreatefrombmp")) {
						$img = imagecreatefrombmp($res);
					} else {
						die("Unsupported image type: $type");
					}
					break;
			}
		}

		return array($type, $img);
    }

    public static function imagensInternoMergeCpfUser($id, $imagem)
    {
        

        /**
         * 
         */
        
        
        $kep = $imagem;
        list($img_type, $Image) = self::getImage($kep);
        $Text = Auth::user()->cpf;
       // dd($Image);
        $sx = imagesx($Image) ;
	$sy = imagesy($Image) ;
        
	    $Font= '/var/www/html/siapenac/public/font/arial.ttf';
	    $FontColor = ImageColorAllocate ($Image,0,102,255) ;
	    $FontShadow = ImageColorAllocate ($Image,255,255,255) ;
	    $Rotation = 0 ;
	    /* Make a copy image */
	    $OriginalImage = ImageCreateTrueColor($sx,$sy) ;
	    ImageCopy ($OriginalImage,$Image,0,0,0,0,$sx,$sy) ;
            
            $FontSize=1 ;
	    do
	        {
	        $FontSize *= 1.1 ;
	        $Box = @ImageTTFBBox($FontSize,0,$Font,$Text);
	        $TextWidth = abs($Box[4] - $Box[0]) ;
	        $TextHeight = abs($Box[5] - $Box[1]) ;
	        }
	    while ($TextWidth < $sx*0.9 && $FontSize < 30) ;
	    /*  Awkward maths to get the origin of the text in the right place */
	    $x = $sx/2 - cos(deg2rad($Rotation))*$TextWidth/2 ;
	    $y = $sy/2 + sin(deg2rad($Rotation))*$TextWidth/2 + cos(deg2rad($Rotation))*$TextHeight/2 ;
	    /* Make shadow text first followed by solid text */

	    imagettftext($Image,$FontSize,$Rotation,$x+1,$y+1,$FontShadow,$Font,$Text);
	    imagettftext($Image,$FontSize,$Rotation,$x,$y,$FontColor,$Font,$Text);

	    /* merge original image into version with text to show image through text */
	    imagecopymerge($Image,$OriginalImage,0,0,0,0,$sx,$sy,70) ;
	    //imagejpeg($Image, $kep, 100);
	    
        ob_start();
        // Save the image to file and free memory
        imagepng($Image);
        $imgString=ob_get_clean();
        $imgBase64=base64_encode($imgString);
        
        return $imgBase64;
        
        //dd($imgBase64);
        imagedestroy($im);


        
        ##############################################


    }

    public function bd(){
        
        $listaErrada = DB::table('_lista')->where('base', 'AC')->get();
        $listaParaEnviar = [];
        $pessoasNaoEncontradas = [];
        $movsQueMudamRegime = ["progressaosemiaberto", "mudancaaberto", "mudancasemiaberto", "progressaoaberto", "regressaofechado", "mudancafechado", "mudancaprovisorio", "regressaosemiaberto"];
        
        $status = '';
        $regime = '';
        $sentenciado = '';

        foreach($listaErrada as $itemCSV){

            $interno = DB::table('interno')
                ->where('nome', $itemCSV->nome)
                ->where('mae', $itemCSV->mae)
                ->where('data_nascimento', $itemCSV->data_nascimento)
                ->first();

            if($interno == null){
                array_push($pessoasNaoEncontradas, $itemCSV);
                
                if (!file_exists(storage_path("app/listaNaoEncontrados.txt"))) {
                    Storage::put('listaNaoEncontrados.txt', "");
                }
                Storage::append('listaNaoEncontrados.txt', $itemCSV->nome.", ".$itemCSV->mae.", ".$itemCSV->pai.", ".$itemCSV->data_nascimento.", ".$itemCSV->cpf.", ".$itemCSV->rg_numero.", ".$itemCSV->rg_expedidor);

                continue;
            }

            $movimentacoes = DB::table('movimentacao_interno')
                ->join('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                ->where('movimentacao_interno.fk_interno', $interno->id)
                ->where('movimentacao_interno.status', 'Finalizada')
                ->orderBy('movimentacao.data', 'DESC')
                ->get();

            // TODO: retornar regime, status e sentenciado a 15/03/2021 através das movimentações
                $regime = $interno->regime;
                $sentenciado = $interno->sentenciado;
                $status = $interno->status;

                /**
                 * --- MOVS --- :
                 * entradaunidadeprisional
                 * alvara
                 * transferencia
                 * transferenciaentrada
                 * prisaodomiciliar
                 * saidajudicial
                 * fuga
                 * recambiamento
                 * obito
                 * mudancacela
                 * livramentocondicional
                 * progressaosemiaberto
                 * mudancaaberto
                 * mudancasemiaberto
                 * progressaoaberto
                 * extincaopunibilidade
                 * regressaofechado
                 * mudancafechado
                 * mudancaprovisorio
                 * regressaosemiaberto
                 * 
                 * --- REGIMES --- :
                 * ABERTO
                 * FECHADO
                 * 
                 * NULL
                 * SEMIABERTO
                 * 
                 * --- SENTENCIADO --- :
                 * S
                 * 
                 * N
                 * NULL
                 * 
                 */
                
            foreach($movimentacoes as $key => $mov) {

                switch($mov->tipo){
                    case "entradaunidadeprisional":
                        $status = "A";
                        break;
                    case "alvara":
                        $status = "I";
                        break;
                    case "transferencia":
                        $status = "A";
                        break;
                    case "transferenciaentrada":
                        $status = "A";
                        break;
                    case "prisaodomiciliar":
                        $status = "I";
                        break;
                    case "saidajudicial":
                        $status = "I";
                        break;
                    case "fuga":
                        $status = "I";
                        break;
                    case "recambiamento":
                        $status = "I";
                        break;
                    case "obito":
                        $status = "I";
                        break;
                    case "mudancacela":
                        $status = "A";
                        break;
                    case "livramentocondicional":
                        $status = "I";
                        break;
                    case "progressaosemiaberto":
                        $status = "A";
                        $regime = "SEMIABERTO";
                        $sentenciado = "S";
                        break;
                    case "mudancaaberto":
                        // verifica se tem movimentação mais recente, caso sim: "é diferente de entradaunidadeprisional? se for, status = A", caso não: "status I"
                        // se não houver movimentaçao mais recente, pega os status de hoje
                        if($key > 0) {
                            $movAnterior = $movimentacoes[$key-1];
                            if($movAnterior->tipo != "entradaunidadeprisional"){
                                $status = "A";
                            } else {
                                $status = "I";
                            }
                        } else {
                            $status = $interno->status;
                        }
                        $regime = "ABERTO";
                        $sentenciado = "S";
                        break;
                    case "mudancasemiaberto":
                        $status = "A";
                        $regime = "SEMIABERTO";
                        $sentenciado = "S";
                        break;
                    case "progressaoaberto":
                        // verifica se tem movimentação mais recente, caso sim: "é diferente de entradaunidadeprisional? se for, status = A", caso não: "status I"
                        // se não houver movimentaçao mais recente, pega os status de hoje
                        if($key > 0) {
                            $movAnterior = $movimentacoes[$key-1];
                            if($movAnterior->tipo != "entradaunidadeprisional"){
                                $status = "A";
                            } else {
                                $status = "I";
                            }
                        } else {
                            $status = $interno->status;
                        }
                        $regime = "ABERTO";
                        $sentenciado = "S";
                        break;
                    case "extincaopunibilidade":
                        $status = "I";
                        break;
                    case "regressaofechado":
                        $status = "A";
                        $regime = "FECHADO";
                        $sentenciado = "S";
                        break;
                    case "mudancafechado":
                        $status = "A";
                        $regime = "FECHADO";
                        $sentenciado = "S";
                        break;
                    case "mudancaprovisorio":
                        $status = "A";
                        $regime = "PROVISORIO";
                        $sentenciado = "N";
                        break;
                    case "regressaosemiaberto":
                        $status = "A";
                        $regime = "SEMIABERTO";
                        $sentenciado = "S";
                        break;
                }

                if(in_array($mov->tipo, $movsQueMudamRegime) && $mov->data < "2021-03-15 00:00:00") {
                    break;
                }
                
            }

            $unidade = $interno->unidade_prisional;
            if($status == "A" && ($regime == "SEMIABERTO" || $regime == "ABERTO")) {
                // verifica unidade da última movimentação de entrada
                foreach($movimentacoes as $key => $mov) {

                    if(($mov->tipo == "entradaunidadeprisional" || $mov->tipo == "transferenciaentrada") && $mov->data < "2021-03-15 00:00:00"){
                        $unidade = $mov->unidade_destino; 
                        break;
                    }

                }  
            }

            if (!file_exists(storage_path("app/listaParaEnviar.txt"))) {
                Storage::put('listaParaEnviar.txt', "");
            }

            if($status == "I"){
                Storage::append('listaParaEnviar.txt', $itemCSV->nome.", ".$itemCSV->mae.", ".$itemCSV->pai.", ".$itemCSV->data_nascimento.", ".$itemCSV->cpf.", ".$itemCSV->rg_numero.", ".$itemCSV->rg_expedidor);
                // array_push($listaParaEnviar, $itemCSV);
            } else if ($status == "A") {
                if($unidade == 1841 || $unidade == 6442 || $unidade == 3581 || $unidade == 3579 || $unidade == 4410 || $unidade == 7000 || $unidade == 3580){
                    Storage::append('listaParaEnviar.txt', $itemCSV->nome.", ".$itemCSV->mae.", ".$itemCSV->pai.", ".$itemCSV->data_nascimento.", ".$itemCSV->cpf.", ".$itemCSV->rg_numero.", ".$itemCSV->rg_expedidor);
                } else if ($regime == "ABERTO"){
                    Storage::append('listaParaEnviar.txt', $itemCSV->nome.", ".$itemCSV->mae.", ".$itemCSV->pai.", ".$itemCSV->data_nascimento.", ".$itemCSV->cpf.", ".$itemCSV->rg_numero.", ".$itemCSV->rg_expedidor);
                }
            }

            $status = '';
            $regime = '';
            $sentenciado = '';

        }

        dd($pessoasNaoEncontradas);
        
    }

    public function atestadosCertidoes()
    {
        return view('interno.atestadosCertidoes.formAtestadosCertidoes');

    }

    public function atestadosCertidoesStore(Request $request)
    {

        //dd($request->input());
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));


        $internosPost = $request->get('internos');
        $tipo = $request->get('tipo');
        $conduta = $request->get('conduta');
        $numero = $request->get('numero');
        $processo = $request->get('processo');
        $vara = $request->get('vara');
        $justificativa = $request->get('justificativa');

        $internos = array();

        foreach($internosPost as $interno)
        {
            $dadosInterno = self::getDadosInterno($interno);

            array_push($internos, array(
                'nome' => $dadosInterno[0]->nome,
                'prontuario' => $interno,
                'sexo' => $dadosInterno[0]->sexo,
                'nacionalidade' => $dadosInterno[0]->naturalidade_pais,
                'dataNascimento' => Tdata::TdataBR($dadosInterno[0]->data_nascimento),
                'pai' => $dadosInterno[0]->pai,
                'mae' => $dadosInterno[0]->mae,
                'regime' => trim(strtoupper($dadosInterno[0]->regime)),
                'dataIngressoUnidade' => MovimentacoesController::getDataEntradaUnidadePrisional($interno, $dadosInterno[0]->unidade_prisional),
                'conduta' => strtoupper($conduta),
                'sentenciado' => $dadosInterno[0]->sentenciado,
                'naturalidade_cidade' => $dadosInterno[0]->naturalidade_cidade,
                'naturalidade_estado' => $dadosInterno[0]->naturalidade_estado,
                'naturalidade_pais' => $dadosInterno[0]->naturalidade_pais,
            ));
        }

        $data_alvara = null;
        if($tipo == "certidaocumprimento"){
            $data_alvara = DB::table('movimentacao')
                ->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao','movimentacao.id')
                ->where('movimentacao_interno.fk_interno',$internos[0]['prontuario'])
                ->where('movimentacao.tipo','alvara')
                ->select('movimentacao.id as id', 'movimentacao.data')
                ->orderBy('movimentacao.data','DESC')
                ->first();
        }

        if(isset($data_alvara->data)){
            $data_alvara = substr($data_alvara->data, 8, 2). '/' . substr($data_alvara->data, 5, 2). '/' . substr($data_alvara->data, 0, 4);
        } else {
            $data_alvara = null;
        }

        $assinaturaDoc = $request->get('assinatura');
        if($assinaturaDoc == "diretor")
        {
            $diretor = FacadeController::getDadosDiretorPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
            if($diretor != null) {

                $assinatura = array('nome' => utf8_encode($diretor[0]->nome),'matricula'=>($diretor[0]->matricula),'sexo'=>json_decode($diretor[0]->sexo));

            }else{

                $viceDiretor = FacadeController::getDadosViceDiretorPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

                if($viceDiretor == null)
                {
                    $assinatura = null;

                }else{
                    $assinatura = array('nome' => utf8_encode($viceDiretor[0]->nome),'matricula'=>json_decode($viceDiretor[0]->matricula),'sexo'=>json_decode($viceDiretor[0]->sexo));
                }

            }

        }
        if($assinaturaDoc == "viceDiretor") {

            $viceDiretor = FacadeController::getDadosViceDiretorPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

            if($viceDiretor != null)
            {
                $assinatura = array('nome' => utf8_encode($viceDiretor[0]->nome),'matricula'=>json_decode($viceDiretor[0]->matricula),'sexo'=>json_decode($viceDiretor[0]->sexo));


            }else{

                $assinatura = null;
            }

        }

//        $diretor = FacadeController::getDadosDiretorPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
//
//        if($diretor == null)
//        {
//            $viceDiretor = FacadeController::getDadosViceDiretorPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
//
//            if($viceDiretor == null)
//            {
//                $assinatura = null;
//
//            }else{
//                $assinatura = array('nome' => utf8_encode($viceDiretor[0]->nome),'matricula'=>json_decode($viceDiretor[0]->matricula),'sexo'=>json_decode($viceDiretor[0]->sexo));
//            }
//
//        }else{
//
//
//            $assinatura = array('nome' => utf8_encode($diretor[0]->nome),'matricula'=>($diretor[0]->matricula),'sexo'=>json_decode($diretor[0]->sexo));
//
//        }

        //dd($internos, $assinatura);

        $nomeUnidade = UnidadesController::getNomeUp($lotacaoServidor);
        $cidadeUnidade = UnidadesController::getCidadeUp($lotacaoServidor);
        $nomeServidor = FacadeController::getNomeServidorCpf(Auth::user()->cpf)." (".Auth::user()->cpf.")";

        if($tipo == "acc") {

            $pdf = \PDF::loadView('interno.atestadosCertidoes.accPdf', compact('internos', 'assinatura', 'nomeUnidade', 'cidadeUnidade', 'assinaturaDoc'))
                ->setOption('encoding', 'UTF-8')
                ->setOption('margin-bottom', 10)
                ->setOption('margin-top', 35)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('margin-bottom', 10)
                ->setOption('header-html', view('template.header.headerAccPdf', compact('nomeUnidade')))
                ->setOption('footer-center', utf8_decode('Emitido em' . date('\ d/m/Y\ ') . ''))
                ->setOption('footer-font-size', 7)
                ->setPaper('a4');
            return $pdf->inline();

        }if($tipo == "apc"){

        $pdf = \PDF::loadView('interno.atestadosCertidoes.apcPdf', compact('internos', 'assinatura', 'nomeUnidade', 'cidadeUnidade','numero', 'assinaturaDoc'))
            ->setOption('encoding', 'UTF-8')
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 35)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('header-html', view('template.header.headerAccPdf', compact('nomeUnidade')))
            ->setOption('footer-center', utf8_decode('Emitido em' . date('\ d/m/Y\ ') . ''))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');
        return $pdf->inline();

        }if($tipo == "certidaocumprimento") {

        $pdf = \PDF::loadView('interno.atestadosCertidoes.cumprimentoAlvaraPdf', compact('internos', 'assinatura', 'nomeUnidade', 'cidadeUnidade', 'assinaturaDoc','vara', 'processo', 'data_alvara'))
            ->setOption('encoding', 'UTF-8')
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 35)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('header-html', view('template.header.headerAccPdf', compact('nomeUnidade')))
            ->setOption('footer-center', utf8_decode('Emitido em' . date('\ d/m/Y\ ') . ''))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');
        LogController::log($internos[0]['prontuario'],"interno","emissao_certidao",Auth::user()->cpf,"EMISSÃO DE CERTIDÃO DE CUMPRIMENTO DE ALVARÁ");
        return $pdf->inline();

        }if($tipo == "certidaonaocumprimento") {

        $pdf = \PDF::loadView('interno.atestadosCertidoes.naoCumprimentoAlvara', compact('internos','assinatura', 'nomeUnidade', 'cidadeUnidade', 'assinaturaDoc', 'vara', 'processo', 'justificativa'))
            ->setOption('encoding', 'UTF-8')
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 35)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('header-html', view('template.header.headerAccPdf', compact('nomeUnidade')))
            ->setOption('footer-center', utf8_decode('Emitido em' . date('\ d/m/Y\ ') . ''))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');
        LogController::log($internos[0]['prontuario'],"interno","emissao_certidao",Auth::user()->cpf,"EMISSÃO DE CERTIDÃO DE NÃO CUMPRIMENTO DE ALVARÁ");
        return $pdf->inline();

        }

//        $pdf = \PDF::loadView('interno.atestadosCertidoes.accPdf', compact('internos','assinatura'))
//            ->setOption('encoding', 'UTF-8')
//            ->setOption('margin-bottom', 10)
//            ->setOption('margin-top', 35)
//            ->setOption('margin-left', 10)
//            ->setOption('margin-right', 10)
//            ->setOption('margin-bottom', 10)
//            ->setOption('header-html', view('template.header.headerAccPdf',compact('nomeUnidade')))
//            ->setOption ('footer-center', utf8_decode('Emitido pelo servidor '.$nomeServidor.' em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
//            ->setOption('footer-font-size', 7)
//            ->setPaper('a4');
//        return $pdf->inline();

    }

    /**
     * view de cadastro de interno
     */
    public function cadastro()
    {

        $lotacaoServidor = FacadeController::getLotacaoServidor(Auth::user()->cpf);

        $profissao = DB::table('profissao')->orderBy('nome', 'ASC')->get();

        $faccao = DB::table('faccao')->orderBy('nome','ASC')->get();

        $unidades = DB::table('unidade')->where('sigla',$lotacaoServidor)->orderBy('sigla', 'ASC')->get();

        return view('interno.cadastro', compact('profissao','unidades','faccao'));
    }

    public static function removeSignals($string)
    {
        $string = str_replace(array("Ç","ç"),'C',$string);
        return strtoupper(str_replace(array("´", "`", "^", "~", "'"), "", iconv('UTF-8', 'ASCII//TRANSLIT',$string)));
    }

    public function validarCadastro(Request $request, Interno $interno){

        if($request->get('cpf') != null){

            $unique = DB::table('interno')->where('cpf', $request->get('cpf'))->get();

            //dd(count($unique));

            if (count($unique) >= 1) {

                return redirect()->back()->withInput()->with('alert-danger', 'CPF já cadastrado no sistema');
            }

            if(strlen($request->get('cpf')) < 11){
                return redirect()->back()->withInput()->with('alert-danger', 'CPF invalido! O CPF deve possuir 11 digitos');
            }

        }

        if($request->get('data_nascimento') != null){
            $data = explode('/', $request->get('data_nascimento'));
            $ano = (int)$data[2];
            if(!checkdate($data[1], $data[0], $data[2]) || $ano >= date("Y")){
                return redirect()->back()->withInput()->with('alert-danger', 'Data de nascimento inválida!');
            }
        }

        if(strlen($request->get('data_entrada')) != 10){
            return redirect()->back()->withInput()->with('alert-danger', 'Data de entrada inválida!');
        }
        
        if(strlen($request->get('hora_entrada')) != 5){
            return redirect()->back()->withInput()->with('alert-danger', 'Hora de entrada inválida!');
        }

        $all = DB::table('interno')->get();

        $comparison = new \Atomescrochus\StringSimilarities\Compare();

        $internosSemelhantes = [];

        $nomeInterno = InternoController::removeSignals(iconv('UTF-8', 'ASCII//TRANSLIT', $request->get('nome')));
        $nomeMae = InternoController::removeSignals(iconv('UTF-8', 'ASCII//TRANSLIT', $request->get('mae')));
        $sexo = $request->get('sexo');
        $lotacao = array();
        $lotacaoaux = array();

        //dd($nomeInterno);

        foreach($all as $one){
            if($one->sexo == $sexo){
                $jaroWinklerNome = $comparison->jaroWinkler( InternoController::removeSignals( $one->nome), $nomeInterno);
                if($nomeMae != null && $nomeMae != ""){
                    $jaroWinklerMae = $comparison->jaroWinkler( InternoController::removeSignals($one->mae), $nomeMae);
                    if($jaroWinklerNome > 0.95 || ($jaroWinklerNome + $jaroWinklerMae)/2 > 0.95){
                        $lotacaoaux['unidade'] = DB::table('unidade')->select('unidade.nome')->where('id', $one->unidade_prisional)->get();
                        $lotacaoaux['sigla'] = DB::table('unidade')->select('unidade.sigla')->where('id', $one->unidade_prisional)->get();
                        $lotacaoaux['pavilhao'] = DB::table('pavilhao')->select('pavilhao.nome')->where('id', $one->pavilhao)->get();
                        $lotacaoaux['ala'] = DB::table('ala')->select('ala.nome')->where('id', $one->ala)->get();
                        $lotacaoaux['cela'] = DB::table('cela')->select('cela.nome')->where('id', $one->cela)->get();
                        array_push($lotacao, $lotacaoaux);
                        array_push($internosSemelhantes, $one);
                    }
                } elseif($jaroWinklerNome > 0.95){
                    $lotacaoaux['unidade'] = UnidadesController::getNomeUp($one->unidade_prisional);
                    $lotacaoaux['sigla'] = UnidadesController::getSiglaUp($one->unidade_prisional);
                    $lotacaoaux['pavilhao'] = UnidadesController::getNomePav($one->pavilhao);
                    $lotacaoaux['ala'] = UnidadesController::getNomeAla($one->ala);
                    $lotacaoaux['cela'] = UnidadesController::getNomeCela($one->cela);
                    array_push($lotacao, $lotacaoaux);
                    array_push($internosSemelhantes, $one);
                }
            }
        }

        // -------------------- Inserir os Fotos --------------------------- //

        $fotos = array();
        if($request->file('inputFile')[0] != null) {
            $qtdFoto = count($request->file('inputFile'));
            $tipoFoto = $request->get('tipoFoto');
            $legenda = $request->get('legenda');


            for ($x = 0; $x < $qtdFoto; $x++) {
                $destinationPath = storage_path('app/'.Constantes::ARVQUIVOS_INTERNO . "/fotos/upload/" . Auth::user()->id) ;
                $fotosaux['tipo'] = "";


                if($tipoFoto[$x] == null)
                {
                    $tipofoto = "outros";
                }else{
                    $tipofoto = $tipoFoto[$x];
                }


                $name =  uniqid() . '.jpg';

                $request->file('inputFile')[$x]->storeAs($destinationPath, $name);

                $fotosaux['destino'] = $destinationPath;
                $fotosaux['nome'] = $name;
                $fotosaux['tipo'] = $tipofoto;
                $fotosaux['legenda'] = $legenda;
                array_push($fotos, $fotosaux);

            }
        }

        // --------------------     Inserir os Fotos        --------------------------- //

        // --------------------     inserir os Documentos   --------------------------- //

        $documentos = array();
        if($request->file('inputFileDoc')[0] != null) {

            $qtdDoc = count($request->file('inputFileDoc'));
            $legendaDoc = $request->get('nomeDoc');

            for ($x = 0; $x < $qtdDoc; $x++) {

                $destinationPath = storage_path('app/'.Constantes::ARVQUIVOS_INTERNO . "/documentos/upload/"  . Auth::user()->id);

                $name = $request->file('inputFileDoc')[$x]->getClientOriginalName();

                $request->file('inputFileDoc')[$x]->storeAs($destinationPath, $name);

                $documentoaux['destino'] = $destinationPath;
                $documentoaux['nome'] = $name;
                $documentoaux['legenda'] = $legendaDoc;
                array_push($documentos, $documentoaux);

            }
        }

        // -------------------- !! inserir os Documentos --------------------------- //


        $request->session()->put('request',$request->request );
        $request->session()->put('documentos', $documentos);
        $request->session()->put('fotos', $fotos);
        $request->session()->all();

        if($internosSemelhantes != []){
            return view('interno.buscar.listaDuplicacoes', compact('internosSemelhantes', 'interno', 'request', 'lotacao'));
        }


        return InternoController::store($request, $interno);
    }

    /**
     * Faz o cadastro do interno no banco de dados
     */
    public function store(Request $request, Interno $interno)
    {

        
        $request->request = $request->session()->get('request');
        $fotos = $request->session()->get('fotos');
        $documentos = $request->session()->get('documentos');

        $diferencas = array();
        
        $dataEntrada = $request['data_entrada'];
        $hora = $request['hora_entrada'];

        // retira do array (por conta do create)
        $dataForm = $request->except('_token', 'data_entrada', 'hora_entrada', 'outro_nome','endereco_cep', 'endereco_logradouro', 'endereco_numero', 'endereco_bairro', 'endereco_complemento','endereco_cidade','endereco_estado','telefone','inputFile','tipoFoto','legenda','inputFileDoc','nomeDoc','diasTriagem','filhoNome', 'filhoDataNascimento', 'filhoNumeroCartaoSus', 'filhoNaUnidadePrisional', 'filhoIncapaz','possuiDeficiencia','tipoDeficiencia','cadeirante');

        $dataForm['data_nascimento'] = Tdata::TdataMysql($dataForm['data_nascimento']);
        $dataForm['rg_data'] = Tdata::TdataMysql($dataForm['rg_data']);

        // se os campos de datas estiverem vazios, para não retorna erro no bd, assumem o valor null
        if ($dataForm['data_nascimento'] == "") {
            $dataForm['data_nascimento'] = (NULL);
        }

        if ($dataForm['rg_data'] == "") {
            $dataForm['rg_data'] = (NULL);
        }

        $dataForm['status'] = "A";
        $dataForm['condicao_interno'] = 644;

        $idInterno = $interno->create($dataForm)->id;


        //IMPEDE QUE OUTRAS INFORMAÇÕES SEJAM ARMAZENADAS EM CASO DE ERRO
        if ($idInterno == "" || $idInterno == "null") {
            $request->session()->forget('request');
            $request->session()->forget('documentos');
            $request->session()->forget('fotos');
            if($fotos != null) {
                $qtdFoto = count($fotos);

                for ($x = 0; $x < $qtdFoto; $x++) {

                    unlink(storage_path('app/'. $fotos[$x]['destino']. '/'. $fotos[$x]['nome']));

                }
            }
            if($documentos != null) {

                $qtdDoc = count($documentos);

                for ($x = 0; $x < $qtdDoc; $x++) {

                    unlink(storage_path('app/'. $documentos[$x]['destino']. '/'. $documentos[$x]['nome']));
                }
            }

            return redirect()->route('interno.cadastro')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');
        }


        // -------------------- Insert movimentacao --------------------------- //

        $insertMovimentacao = DB::table('movimentacao')->insert([
            'tipo' => 'entradaunidadeprisional',
            'unidade_destino' => $dataForm['unidade_prisional'],
            'pavilhao_destino' => $dataForm['pavilhao'],
            'ala_destino' => $dataForm['ala'],
            'cela_destino' => $dataForm['cela'],
            'data' => Tdata::TdataMysql($dataEntrada)." ".$hora.":00",
            'created_at' => Tdata::TtimeStampHojeMysql()
        ]);


        $idMovimentacao = DB::getPdo()->lastInsertId();

        $insertMovimentacaoInterno = DB::table('movimentacao_interno')->insert([
            'status' => 'Finalizada',
            'fk_interno' => $idInterno,
            'fk_movimentacao' => $idMovimentacao,
        ]);

        // -------------------- !Insert movimentacao  --------------------------- //

        // -------------------- Log --------------------------- //

        if($idInterno)
        {
            LogController::log($idInterno,"interno","add",Auth::user()->cpf,"Criação do cadastro");
        }
        // -------------------- !Log --------------------------- //


        // -------------------- inserir outros nomes --------------------------- //

        $outrosNomes = $request->input('outro_nome');

        if ($outrosNomes[0] != null) {

            foreach ($outrosNomes as $outros) {

                $insertOutroNome = DB::table('outrosnomes')->insert([
                        'pk_id' => $idInterno,
                        'pk_tabela' => "interno",
                        'nome' => $outros
                    ]
                );

            }
        }

        // -------------------- !! inserir outros nomes

        
        /**
         * INSERIR FILHOS
         */
        $filhosDefault = $request->get('filhoNome');
        if(!is_null($filhosDefault[0]))
        {
            $filhosNome = $request->get('filhoNome');
            $filhoDataNascimento = $request->get('filhoDataNascimento');
            $filhoNumeroCartaoSus = $request->get('filhoNumeroCartaoSus');
            $filhoNaUnidadePrisional = $request->get('filhoNaUnidadePrisional');
            $filhoIncapaz = $request->get('filhoIncapaz');
            
            for($f = 0; $f < count($filhosDefault); $f++)
            {
                $dataNascFilho = null;
                
                if(!is_null($filhoDataNascimento[$f]))
                {
                    $dataNascFilho = Tdata::TdataMysql($filhoDataNascimento[$f]);
                }
                $insertFilhos = DB::table("filhos")->insert([
                    'fk_interno' => $idInterno,
                    'nome' => $filhosNome[$f],
                    'data_nascimento' => $dataNascFilho,
                    'numero_cartao_sus' => $filhoNumeroCartaoSus[$f],
                    'na_unidade_prisional' => $filhoNaUnidadePrisional[$f],
                    'incapaz' => $filhoIncapaz[$f],
                ]);
                
            $alteracoes = "Inclusão de dados do campo Filhos: <i><strong>Nome: ".$filhosNome[$f].", Data de Nascimento: ". $filhoDataNascimento[$f].", Número do Cartão SUS: ".$filhoNumeroCartaoSus[$f].", Está na Unidade Prisional: ".$filhoNaUnidadePrisional[$f].", Incapaz: ".$filhoIncapaz[$f]."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }
        }
        
        //FIM FILHOS
        
        /**
         * Deficiencia
         * insere a informacao se o interno e deficiente ou nao
         */
        if(DB::table('interno_deficiencia')->where('fk_interno', $idInterno)->count() == 0){
            DB::table('interno_deficiencia')->insert([
                'fk_interno' => $idInterno,
                'deficiente' => $request->get('possuiDeficiencia'),
                'tipo_deficiencia' => $request->get('tipoDeficiencia'),
                'cadeirante' => $request->get('cadeirante'),
                'created_at' => Tdata::TtimeStampHojeMysql(),
            ]);
        }
        
        // Fim deficiencia
        
        // -------------------- inserir os enderecos --------------------------- //

        $logradouroDefault = $request->get('endereco_logradouro');

        if($logradouroDefault[0] != null) {
            $qtdEndereco = count($request->get('endereco_cep'));
            $endereco_cep = $request->get('endereco_cep');
            $endereco_logradouro = $request->get('endereco_logradouro');
            $endereco_numero = $request->get('endereco_numero');
            $endereco_bairro = $request->get('endereco_bairro');
            $endereco_complemento = $request->get('endereco_complemento');
            $endereco_cidade = $request->get('endereco_cidade');
            $endereco_estado = $request->get('endereco_estado');

            // key api maps: AIzaSyBy6UMyq2qpaN2I5womEm0oQJbjNXKwzPU

            for ($x = 0; $x < $qtdEndereco; $x++) {

                $address = $endereco_logradouro[$x] . " " . $endereco_numero[$x] . " " . $endereco_bairro[$x] . " " . $endereco_cidade[$x] . " " . $endereco_estado[$x];
                $coordenadas = GeocodingMaps::getGeocodeData($address);


                $insertEndereco = DB::table('endereco')->insert([
                    'fk_id' => $idInterno,
                    'fk_tabela' => "interno",
                    'pais' => "null",
                    'estado' => $endereco_estado[$x],
                    'cidade' => $endereco_cidade[$x],
                    'bairro' => $endereco_bairro[$x],
                    'cep' => $endereco_cep[$x],
                    'logradouro' => $endereco_logradouro[$x],
                    'numero' => $endereco_numero[$x],
                    'complemento' => $endereco_complemento[$x],
                    'latitude' => $coordenadas[0],
                    'longitude' => $coordenadas[1],
                    'created_at' => date("Y-m-d H:i:s"),
                ]);
            }
        } // se o cepDefault for preenchido

        // -------------------- !! inserir os enderecos


        // -------------------- inserir os telefones --------------------------- //

        $fone = $request->input('telefone');

        if ($fone[0] != null) {

            foreach ($fone as $f) {

                $insertFone = DB::table('contato')->insert([
                        'fk_id' => $idInterno,
                        'fk_tabela' => "interno",
                        'numero' => $f,
                        'created_at' => date("Y-m-d H:i:s"),
                    ]
                );

            }
        }

        // -------------------- !! inserir os telefones --------------------------- //


        // -------------------- inserir os Fotos --------------------------- //


        if($fotos != null) {
            $qtdFoto = count($fotos);
            $tipoFoto = $request->get('tipoFoto');
            $legenda = $request->get('legenda');


            for ($x = 0; $x < $qtdFoto; $x++) {

                $destinationPath = Constantes::ARVQUIVOS_INTERNO . "/fotos/" . $idInterno;


                if($tipoFoto[$x] == null)
                {
                    $tipofoto = "outros";
                }else{
                    $tipofoto = $tipoFoto[$x];
                }

                if($tipoFoto[$x] == "principal")
                {
                    $name = $idInterno .".jpg";

                }else {

                    $name = $idInterno . "_" . time() . "_" . uniqid() . '.jpg';;
                }


                if(!file_exists(storage_path('app/'. $destinationPath. '/'))) {
                    mkdir(storage_path('app/' . $destinationPath . '/'));
                    
                }
                copy(storage_path('app/'. $fotos[$x]['destino']. '/'. $fotos[$x]['nome']), storage_path('app/'. $destinationPath. '/'. $name));
                unlink(storage_path('app/'. $fotos[$x]['destino']. '/'. $fotos[$x]['nome']));


                //$request->$fotos[$x]->storeAs($destinationPath, $name);
                //Storage::put($name, $destinationPath);


                $insertFoto = DB::table('foto')->insert(
                    ['fk_id' => $idInterno,
                        'fk_tabela' => "interno",
                        'nome_arquivo' => $name,
                        'legenda' => $legenda[$x],
                        'tipo' => $tipofoto,
                        'created_at' => date("Y-m-d H:i:s")
                    ]
                );

                $idFoto = DB::getPdo()->lastInsertId();

                LogController::log($idFoto,"foto","add", Auth::user()->cpf, "Inclusão de foto do interno<br>Arquivo: <i><strong>". $name ."</strong></i> <br>Legenda: <strong><i>".$legenda[$x]."</i></strong><br>Tipo: <strong><i>".$tipoFoto[$x]."</i></strong>");


            }
        }

        // -------------------- !! inserir os Fotos --------------------------- //

        // -------------------- inserir os Documentos --------------------------- //

        if($documentos != null) {

            $qtdDoc = count($documentos);
            $legendaDoc = $request->get('nomeDoc');

            for ($x = 0; $x < $qtdDoc; $x++) {

                $destinationPath = Constantes::ARVQUIVOS_INTERNO . "/documentos/" . $idInterno;

                $name = $idInterno . "_" . time() . "_" . $documentos[$x]['nome'];

                if(!file_exists(storage_path('app/'. $destinationPath. '/'))){
                mkdir(storage_path('app/'. $destinationPath. '/'));
                }
                copy(storage_path('app/'. $documentos[$x]['destino']. '/'. $documentos[$x]['nome']), storage_path('app/'. $destinationPath. '/'. $name));
                unlink(storage_path('app/'. $documentos[$x]['destino']. '/'. $documentos[$x]['nome']));

                //$request->file('inputFileDoc')[$x]->storeAs($destinationPath, $name);
                //Storage::put($name, $destinationPath);

                $insertDoc = DB::table('documento')->insert(
                    ['fk_id' => $idInterno,
                        'fk_tabela' => "interno",
                        'nome_arquivo' => $name,
                        'descricao' => $legendaDoc[$x],
                        'created_at' => date("Y-m-d H:i:s")
                    ]
                );

                $idDoc = DB::getPdo()->lastInsertId();

                LogController::log($idDoc,"documento","add", Auth::user()->cpf, "Inclusão de documento do interno<br>Arquivo: <i><strong>". $name ."</strong></i>");


            }
        }

        // -------------------- !! inserir os Documentos --------------------------- //

        // -------------------- cadastro na sancao --------------------------- //
        $diasTriagem = $request->get('diasTriagem');

        if($diasTriagem != 0) {
            //$dataHj = Tdata::TdataHojeMysql();
            $dataInicio = Tdata::TdataMysql($dataEntrada);
            $dataFimTriagem = Tdata::addDias($dataInicio, $request->get('diasTriagem'));

            $inserSancaoTriagem = DB::table('sancao_triagem')->insert([
                'tipo' => 'triagem',
                'unidade' => UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf)),
                'fk_movimentacao' => $idMovimentacao,
                'fk_interno' => $idInterno,
                'data_fim' => $dataFimTriagem,
                'status' => 'A',
                'motivo' => 'Entrada na unidade prisional',
                'created_at' => Tdata::TtimeStampHojeMysql(),
            ]);
        }

        // -------------------- !cadastro na sancao --------------------------- //


//        DB::table('interno')->where('id',$idInterno)->update([
//            'condicao_interno' => 644
//        ]);
//

        if ($idInterno != "" || $idInterno != "null") {
            $request->session()->forget('request');
            $request->session()->forget('documentos');
            $request->session()->forget('fotos');
            $request = null;

            return redirect()->route('interno.buscar')->with('alert-success', 'O cadastro do interno foi realizado com sucesso!');

        }else{

            return redirect()->route('interno.cadastro')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }

    }

    public function buscar(Request $request)
    {
        $request->session()->put('lista',null);

        $profissao = DB::table('profissao')->join('interno','interno.profissao','=','profissao.id')->select('profissao.id','profissao.nome')->distinct('profissao.nome')->where('interno.profissao','!=',null)->orderBy('profissao.nome', 'ASC')->get();

        $unidades = DB::table('unidade')->where('status','1')->orderBy('sigla', 'ASC')->get();

        $faccaoLista = DB::table('faccao')->orderBy('nome','ASC')->get();

        $condicaoLista = DB::table('condicao')->where('id','!=',644)->where('id','!=',647)->orderBy('nome','ASC')->get();

        $religioes = DB::table('interno')->select('religiao')->distinct('religiao')->where('religiao','!=',null)->orderBy('religiao','ASC')->get();

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $filtros = "";
        
        $nomeMaeBusca = "";

        /**
         * se não realizar uma busca
         * - Evita que mostre todos os resultados sem realizar uma busca
         */
        if(!strpos($url,"?"))
        {
            $dados = false;

        }else {
            

            $interno = DB::table('interno');

            $interno->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional');
            $interno->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao');
            $interno->leftJoin('ala', 'ala.id', '=', 'interno.ala');
            $interno->leftJoin('cela', 'cela.id', '=', 'interno.cela');


            $nome = $request->get('nome');
            $nome_social = $request->get('nome_social');
            $outrosNomes = $request->get('outrosNomes');
            $mae = $request->get('mae');
            $prontuario = $request->get('prontuario');
            $sexo = $request->get('sexo');
            $regime = $request->get('regime');
            $sentenciado = $request->get('sentenciado');
            $faccao = $request->get('faccao');
            $unidade_prisional = $request->get('unidade_prisional');
            $pavilhao = $request->get('pavilhao');
            $ala = $request->get('ala');
            $cela = $request->get('cela');
            $status = $request->get('status');
            $endereco_pais = $request->get('endereco_pais');
            $endereco_logradouro = $request->get('endereco_logradouro');
            $endereco_bairro = $request->get('endereco_bairro');
            $endereco_estado = $request->get('endereco_estado');
            $endereco_cidade = $request->get('endereco_cidade');
            $endereco_cep = $request->get('endereco_cep');
            $naturalidade_pais = $request->get('naturalidade_pais');
            $naturalidade_estado = $request->get('naturalidade_estado');
            $naturalidade_cidade = $request->get('naturalidade_cidade');
            $profissaoSearch = $request->get('profissao');
            $escolaridadeSearch = $request->get('escolaridade');
            $lideranca = $request->get('lideranca');
            $periculosidade = $request->get('periculosidade');
            $pai = $request->get('pai');
            $condicao = $request->get('condicao');
            $religiao = $request->get('religiao');
            $etnia = $request->get('etnia');
            $opcao_sexual = $request->get('opcao_sexual');
            $medida_seguranca = $request->get('medida_seguranca');
            $nascimento_de =$request->get('nascimento_de');
            $nascimento_ate =$request->get('nascimento_ate');
            $cpf = $request->get('cpf');
            $descricao_foto = $request->get('descricao_foto');
            $ultima_visita = $request->get('ultima_visita');
            $ultimo_advogado = $request->get('ultimo_advogado');
            $apenas_internos_fotos = $request->get('apenas_internos_fotos');
            $apenas_internos_dataNascimento = $request->get('apenas_internos_dataNascimento');
            $estado_civil = $request->get('estado_civil');
            $checkNomeMaeResultado = $request->get('checkNomeMaeResultado');
            $nomeMaeBusca = "";
            if(isset($checkNomeMaeResultado))
            {
                $nomeMaeBusca = "S";
            }


            if ($outrosNomes != null) {
                $interno->leftJoin('outrosnomes', 'outrosnomes.pk_id', '=', 'interno.id');
                $interno->where('outrosnomes.nome', 'LIKE', '%' . $outrosNomes . '%');
                $filtros = $filtros . 'Outros Nomes: ' . $outrosNomes . '; ';
            }

            if ($apenas_internos_fotos != null) {
                if($apenas_internos_fotos == "comFotos"){
                    $interno->join('foto', function ($query) {
                        $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('tipo', 'principal');
                    });
                    $filtros = $filtros . 'Apenas internos com fotos: Sim; ';
                } elseif($apenas_internos_fotos == "semFotos"){
                    $recordsSemFotos = DB::table('foto')->where('fk_tabela', 'interno')->where('tipo','principal')->pluck('fk_id');
                    $interno->whereNotIn('interno.id',$recordsSemFotos);
                    $filtros = $filtros . 'Apenas internos sem fotos: Sim; ';
                } 
            }

            if ($apenas_internos_dataNascimento != null) {
                if($apenas_internos_dataNascimento == "comDataNascimento"){
                    $interno->whereNotNull('interno.data_nascimento');
                    $filtros = $filtros . 'Apenas internos com data de nascimento: Sim; ';
                } elseif($apenas_internos_dataNascimento == "semDataNascimento"){
                    $interno->where(function ($query) {
                        $query->where('interno.data_nascimento','')->orWhereNull('interno.data_nascimento');
                    });
                    $filtros = $filtros . 'Apenas internos com data de nascimento: Não; ';
                } 
            }

            if ($estado_civil != null) {
                if($estado_civil == "naoPreenchido"){
                    $interno->where(function ($query) {
                        $query->where('interno.estado_civil','')->orWhereNull('interno.estado_civil');
                    });
                    $filtros = $filtros . 'Apenas internos com estado civil não preenchido; ';
                } else {
                    $interno->where('interno.estado_civil', $estado_civil);
                    $filtros = $filtros . 'Estado Civil: ' . $estado_civil . '; ';
                } 
            }

            if ($ultima_visita != null){

                $controle_acesso = DB::table('interno')
                    ->leftJoin('visita_interno', 'visita_interno.fk_interno', '=', 'interno.id')
                    ->leftJoin('visita', 'visita.id', '=', 'visita_interno.fk_visita')
                    ->leftJoin('controle_acesso', 'controle_acesso.cpf', '=', 'visita.cpf')
                    ->where('controle_acesso.data' , '>=' , Tdata::TdataMysql($ultima_visita))
                    ->where('controle_acesso.tabela', 'visita')
                    ->where('controle_acesso.tipo', 'S')
                    ->select('interno.id')->get();
                $valoresString = "";
                foreach($controle_acesso as $x => $item){
                    if($x == 0){
                        $valoresString = $valoresString . $item->id;
                    } else {
                        $valoresString = $valoresString . ',' . $item->id;
                    }
                }
//                /dd( explode(",", $valoresString));
                $interno->whereNotIn('interno.id',  explode(",", $valoresString) );

                $filtros = $filtros . 'Última vista em: ' . $ultima_visita . '; ';
            }

            if($ultimo_advogado != null){

                $atendimento_adv = DB::table('interno')
                    ->leftJoin('advogado_atendimento', 'advogado_atendimento.fk_interno', '=', 'interno.id')
                    ->where('advogado_atendimento.data_atendimento' , '>=' , Tdata::TdataMysql($ultimo_advogado))
                    ->select('interno.id')->distinct()->get();
                $valoresString = "";
                foreach($atendimento_adv as $x => $item){
                    if($x == 0){
                        $valoresString = $valoresString . $item->id;
                    } else {
                        $valoresString = $valoresString . ',' . $item->id;
                    }
                }
//                /dd( explode(",", $valoresString));
                $interno->whereNotIn('interno.id',  explode(",", $valoresString) );


                $filtros = $filtros . 'Última atendimento de advogado: ' . $ultimo_advogado . '; ';
            }

            if ($mae != null) {
                $interno->where('interno.mae', 'LIKE', '%' . $mae . '%');
                $filtros = $filtros . 'Nomes da Mãe: ' . $mae . '; ';
            }

            if ($prontuario != null) {
                $interno->where('interno.id', $prontuario);
                $filtros = $filtros . 'Prontuário: ' . $prontuario . '; ';
            }

            if ($descricao_foto != null){
                $interno->leftJoin('foto', 'foto.fk_id', '=', 'interno.id');
                $interno->where('fk_tabela', 'interno');
                $interno->where('foto.legenda', 'LIKE', '%'. $descricao_foto . '%');
                $filtros = $filtros . 'Legenda da foto: ' . $descricao_foto . '; ';
            }

            if($sexo == "naoPreenchido"){
                $interno->where(function ($query) {
                    $query->where('interno.sexo','')->orWhereNull('interno.sexo');
                });
                $filtros = $filtros . 'Sexo: Não Preenchida; ';
            } elseif ($sexo != null) {
                $interno->where('interno.sexo', $sexo);
                $filtros = $filtros . 'Sexo: ' . $sexo . '; ';
            }

            if ($regime != null) {
                $interno->where('interno.regime', $regime);
                $filtros = $filtros . 'Regime: ' . $regime . '; ';
            }

            if($sentenciado == "naoPreenchido"){
                $interno->where(function ($query) {
                    $query->where('interno.sentenciado','')->orWhereNull('interno.sentenciado');
                });
                $filtros = $filtros . 'Provisório: Não Preenchido; ';
            } elseif ($sentenciado != null) {
                $interno->where('interno.sentenciado', $sentenciado);
                $filtros = $filtros . 'Sentenciado: ' . $sentenciado . '; ';
            }

            if ($faccao != null) {
                if($faccao == "SemFaccao")
                {
                    $interno->where('interno.faccao', null);
                    $filtros = $filtros . 'Facção: Não faccionado; ';
                }else{
                    $interno->where('interno.faccao', $faccao);
                    $filtros = $filtros . 'Facção: ' . self::getNomeFaccao($faccao) . '; ';
                }

            }

            if ($unidade_prisional != null) {
                $interno->where('interno.unidade_prisional', $unidade_prisional);
                $filtros = $filtros . 'Unidade Prisional: ' . UnidadesController::getNomeUp($unidade_prisional) . '; ';
            }

            if ($pavilhao != null) {
                $interno->where('interno.pavilhao', $pavilhao);
                $filtros = $filtros . 'Pavilhão: ' . UnidadesController::getNomePav($pavilhao). '; ';
            }

            if ($ala != null) {
                $interno->where('interno.ala', $ala);
                $filtros = $filtros . 'Ala: ' . UnidadesController::getNomeAla($ala) . '; ';
            }

            if ($cela != null) {
                $interno->where('interno.cela', $cela);
                $filtros = $filtros . 'Cela: ' . UnidadesController::getNomeCela($cela) . '; ';
            }

            if ($status != null) {
                $interno->where('interno.status', $status);
                $filtros = $filtros . 'Status: ' . $status . '; ';
            }
            if ($naturalidade_pais != null) {
                if($naturalidade_pais == "naoPreenchida"){
                    $interno->where(function ($query) {
                        $query->where('interno.naturalidade_pais','')->orWhereNull('interno.naturalidade_pais');
                    });
                    $filtros = $filtros . 'Apenas internos com Naturalidade (País) não preenchida; ';
                } else {
                    $interno->where('interno.naturalidade_pais', $naturalidade_pais);
                    $filtros = $filtros . 'Naturalidade - País: ' . $naturalidade_pais . '; ';
                }
            }

            if ($naturalidade_estado != null) {
                if($naturalidade_estado == "naoPreenchida"){
                    $interno->where(function ($query) {
                        $query->where('interno.naturalidade_estado','')->orWhereNull('interno.naturalidade_estado');
                    });
                    $filtros = $filtros . 'Apenas internos com Naturalidade (Estado) não preenchida; ';
                } else {
                    $interno->where('interno.naturalidade_estado', $naturalidade_estado);
                    $filtros = $filtros . 'Naturalidade - Estado: ' . $naturalidade_estado . '; ';
                }
            }

            if ($naturalidade_cidade != null) {
                $interno->where('interno.naturalidade_cidade', $naturalidade_cidade);
                $filtros = $filtros . 'Naturalidade - Cidade: ' . $naturalidade_cidade . '; ';
            }

            if ($profissaoSearch != null) {
                $interno->where('interno.profissao', $profissaoSearch);
                $filtros = $filtros . 'Profissão: ' . $profissaoSearch . '; ';
            }

            if($escolaridadeSearch == "naoPreenchida"){
                $interno->where(function ($query) {
                    $query->where('interno.escolaridade','')->orWhereNull('interno.escolaridade');
                });
                $filtros = $filtros . 'Escolaridade: Não Preenchida; ';
            } elseif ($escolaridadeSearch != null) {
                $interno->where('interno.escolaridade', $escolaridadeSearch);
                $filtros = $filtros . 'Escolaridade: ' . $escolaridadeSearch . '; ';
            }

            if ($periculosidade != null) {
                if($periculosidade == "naoPreenchida"){
                    $interno->where(function ($query) {
                        $query->where('interno.periculosidade','')->orWhereNull('interno.periculosidade');
                    });
                    $filtros = $filtros . 'Periculosidade: Não Preenchida; ';
                } else {
                    $interno->where('interno.periculosidade', $periculosidade);
                    $filtros = $filtros . 'Periculosidade: ' . $periculosidade . '; ';
                } 
            }

            if ($lideranca != null) {
                $interno->where('interno.lideranca', $lideranca);
                $filtros = $filtros . 'Liderança: ' . $lideranca . '; ';
            }

            if ($pai != null) {
                $interno->where('interno.pai', $pai);
                $filtros = $filtros . 'Nome do Pai: ' . $pai . '; ';
            }

            if ($medida_seguranca != null) {
                $interno->where('interno.medida_seguranca', $medida_seguranca);
                $filtros = $filtros . 'Medidade de Segurança: ' . $medida_seguranca . '; ';
            }

            if ($condicao != null) {
                $interno->where('interno.condicao_interno', $condicao);
                $filtros = $filtros . 'Condição: ' . $condicao . '; ';
            }

            if($religiao == "naoPreenchida"){
                $interno->where(function ($query) {
                    $query->where('interno.religiao','')->orWhereNull('interno.religiao');
                });
                $filtros = $filtros . 'Religião: Não Preenchida; ';
            } elseif ($religiao != null) {
                $interno->where('interno.religiao', $religiao);
                $filtros = $filtros . 'Religião: ' . $religiao . '; ';
            }

            if($etnia == "naoPreenchida"){
                $interno->where(function ($query) {
                    $query->where('interno.etnia','')->orWhereNull('interno.etnia');
                });
                $filtros = $filtros . 'Cor da Pele: Não Preenchida; ';
            } elseif ($etnia != null) {
                $interno->where('interno.etnia', $etnia);
                $filtros = $filtros . 'Etnia: ' . $etnia . '; ';
            }

            if ($opcao_sexual != null) {
                if($opcao_sexual == "naoPreenchida"){
                    $interno->where(function ($query) {
                        $query->where('interno.opcao_sexual','')->orWhereNull('interno.opcao_sexual');
                    });
                    $filtros = $filtros . 'Orientação Sexual: Não Preenchida; ';
                } else {
                    $interno->where('interno.opcao_sexual', $opcao_sexual);
                    $filtros = $filtros . 'Orientação Sexual: ' . $opcao_sexual . '; ';
                } 
            }

            if($endereco_logradouro != null || $endereco_bairro != null || $endereco_estado != null || $endereco_cidade != null || $endereco_pais != null)
            {
                $interno->join('endereco', 'endereco.fk_id', '=', 'interno.id')->where('fk_tabela','interno');
            }

            if ($endereco_pais != null) {
                $interno->where('endereco.logradouro', $endereco_pais);
                $filtros = $filtros . 'Endereço - País: ' . $endereco_pais . '; ';
            }

            if ($endereco_logradouro != null) {
                $interno->where('endereco.logradouro', 'LIKE', '%' .$endereco_logradouro. '%');
                $filtros = $filtros . 'Endereço - Logradouro: ' . $endereco_logradouro . '; ';
            }

            if ($endereco_bairro != null) {
                $interno->where('endereco.bairro', 'LIKE', '%' .$endereco_bairro. '%');
                $filtros = $filtros . 'Endereço - Bairro: ' . $endereco_bairro . '; ';
            }

            if ($endereco_estado != null) {
                $interno->where('endereco.estado', $endereco_estado);
                $filtros = $filtros . 'Endereço - Estado: ' . $endereco_estado . '; ';
            }

            if ($endereco_cidade != null) {
                $interno->where('endereco.cidade', 'LIKE', '%' .$endereco_cidade. '%');
                $filtros = $filtros . 'Endereço - Cidade: ' . $endereco_cidade . '; ';
            }

            if ($endereco_cep != null){
                $interno->where('endereco.cep', 'LIKE', '%' .$endereco_cep. '%');
                $filtros = $filtros . 'Endereço - CEP: ' . $endereco_cep . '; ';
            }

            if($nascimento_de != null && $nascimento_ate != null){
                if(Tdata::TdataMysql($nascimento_de) > Tdata::TdataMysql($nascimento_ate)){
                    return redirect()->route('interno.buscar')->with('alert-danger', 'A data inicial do nascimento não deve ser maior que a data final!');
                }
            }

            if ($nascimento_de != null){
                $interno->where('interno.data_nascimento', '>=', Tdata::TdataMysql($nascimento_de));
                $filtros = $filtros . 'Nascimento de: ' . $nascimento_de . '; ';
            }

            if($nascimento_ate != null){
                $interno->where('interno.data_nascimento', '<=', Tdata::TdataMysql($nascimento_ate));
                $filtros = $filtros . 'Nascimento até: ' . $nascimento_ate . '; ';
            }

            if($cpf != null){
                $interno->where('interno.cpf', $cpf);
                $filtros = $filtros . 'CPF: ' . $cpf . '; ';
            }

            if($nome_social != null){
                $interno->where('interno.nome_social','like' , '%'.$nome_social . '%');
                $filtros = $filtros . 'Nome Social (Alterado Oficialmente): ' . $nome_social . '; ';
            }

            // passa a query para a paginacao "->appends..."

            $dados = $interno->select('interno.nome',"interno.id","interno.mae",
                DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca')
                ,"interno.status","unidade.sigla","pavilhao.nome AS pvNome","ala.nome AS ala","cela.nome AS cela","interno.cpf", "interno.nome_social")->orderBy('interno.nome','ASC')->get();
            $dados = $dados->unique('id');

            if ($nome != null) {
                $interno->where('interno.nome', 'LIKE', '%' . $nome . '%');
                $filtros = $filtros . 'Nome: ' . $nome . '; ';
            }

            $dadosNomes = $interno->select('interno.nome',"interno.id","interno.mae",
                DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'),"interno.status","unidade.sigla",
                "pavilhao.nome AS pvNome","ala.nome AS ala","cela.nome AS cela", "interno.cpf", "interno.nome_social")->orderBy('interno.nome','ASC')->get()->unique('id');

            $novosDados = [];


            if ($nome != null) {

                $comparison = new \Atomescrochus\StringSimilarities\Compare();
                $arrayComparador = array();

                foreach ( $dadosNomes as $dado) {
                    array_push($arrayComparador, array(2, $dado));
                }


                $nomeInterno = InternoController::removeSignals($nome);

                foreach($dados as $x => $dado){
                    $nomeComparado = InternoController::removeSignals($dados[$x]->nome);
                    $jaroWinklerNome = $comparison->jaroWinkler( $nomeInterno, $nomeComparado);
                    $valorInteiro = similar_text($nomeInterno, substr($nomeComparado, 0, strlen($nomeInterno)));
                    $percentual = $valorInteiro/strlen($nomeInterno);


                    if(strlen(explode(" ", $nomeInterno)[0]) < strlen( explode(" ", $nomeComparado)[0])){
                        $subtrator = (strlen(explode(" ", $nomeInterno)[0]))/(strlen( explode(" ", $nomeComparado)[0]));
                        /*if($dados[$x]->id == 2510645){
                            dd("AQUIX  " . $jaroWinklerNome. "  " .  $percentual .  "  "  . similar_text($nomeInterno, $nomeComparado) . "  " . similar_text($nomeInterno, substr($nomeComparado, 0, strlen($nomeInterno))) . "  " . strlen( explode(" ", $nomeComparado)[0])/(strlen(explode(" ", $nomeInterno)[0])));
                        }*/
                        if($subtrator >= 0.50){
                            $jaroWinklerNome = $jaroWinklerNome - $subtrator/2;
                            $percentual = $percentual - $subtrator/2;
                        }
                    } elseif (strlen(explode(" ", $nomeInterno)[0]) > strlen( explode(" ", $nomeComparado)[0])) {
                        $subtrator = (strlen( explode(" ", $nomeComparado)[0])/(strlen(explode(" ", $nomeInterno)[0])));
                        /*if($dados[$x]->id == 2510645){
                            dd($subtrator . "  AQUI  " . $jaroWinklerNome. "  " .  $percentual .  "  "  . similar_text($nomeInterno, $nomeComparado) . "  " . similar_text($nomeInterno, substr($nomeComparado, 0, strlen($nomeInterno))) . "  " . strlen( explode(" ", $nomeComparado)[0])/(strlen(explode(" ", $nomeInterno)[0])));
                        }*/
                        if($subtrator >= 0.50){
                            $jaroWinklerNome = $jaroWinklerNome - $subtrator/2;
                            $percentual = $percentual - $subtrator/2;
                        }
                    }

                    /*if($dados[$x]->id == 255393){
                        dd($jaroWinklerNome. "  " .  $percentual .  "  "  . similar_text($nomeInterno, $nomeComparado) . "  " . similar_text($nomeInterno, substr($nomeComparado, 0, strlen($nomeInterno))) . "  " . (strlen(explode(" ", $nomeInterno)[0]))/(strlen( explode(" ", $nomeComparado)[0])));
                    }*/
                    if(strlen($nomeInterno) <= 5 && !InternoController::contem($dados[$x]->id, $arrayComparador)
                        && ($percentual > 0.6 && $jaroWinklerNome > 0.6)
                        && ($percentual + $jaroWinklerNome) >= 1.40){
                        array_push($arrayComparador, array ( ($jaroWinklerNome + $percentual), $dados[$x]));
                    } else
                    if(strlen($nomeInterno) > 5 && !InternoController::contem($dados[$x]->id, $arrayComparador)
                        && ($percentual > 0.7 && $jaroWinklerNome > 0.7)
                        && ($percentual + $jaroWinklerNome) >= 1.70){
                        array_push($arrayComparador, array (($jaroWinklerNome + $percentual), $dados[$x]));
                    }
                }

                $tamanho = count($arrayComparador);

                for($x = 0; $x < $tamanho; $x++){
                    for($y = $x; $y < $tamanho; $y++){
                        if ($arrayComparador[$x][0] < $arrayComparador[$y][0]) {
                            $trocador = $arrayComparador[$y];
                            $arrayComparador[$y] = $arrayComparador[$x];
                            $arrayComparador[$x] = $trocador;
                            $y = $x+1;
                        }
                    }
                }


                for($x = 0; $x < $tamanho; $x++){
                    array_push($novosDados, $arrayComparador[$x][1]);
                }

                $dados = $novosDados;

            }

        }

        if($dados != false) {
            $request->session()->put('lista',$dados);
            $request->session()->put('filtros', $filtros);
            $dados = collect($dados)->paginate(50)->appends($request->input());
        }

        $nomeFiltro = $request->get('nome');

        return view('interno.buscar.buscar', compact('nomeFiltro', 'nomeMaeBusca','profissao', 'unidades','dados','faccaoLista','condicaoLista','religioes'));
    }

    public function contem($id, Array $arrayComparador){
        foreach ($arrayComparador as $item) {
            if($item[1]->id == $id){
                return true;
            }
        }
        return false;
    }

    /**
     * gerar Lista / gera a lista em pdf da busca realizada
     *
     */
    public function gerarListaBusca(Request $request){

        ini_set('max_execution_time', 30000);
        ini_set('memory_limit','256M');

        $dados = $request->session()->get('lista');
        $filtros = $request->session()->get('filtros');

        $pdf = \PDF::loadView('interno.buscar.buscaInternoListaPdf', compact('dados', 'filtros'))
            ->setOption('encoding', 'UTF-8')
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 30)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('header-html', view('template.header.headerBuscaPdf'))
            ->setOption ('footer-center', utf8_decode('Gerado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');
        return $pdf->inline();
    }

    /**
     * Gera uma lista em pdf com as informações que deseja imprimir
     * @param Request $request
     * @return pdf file
     */
    public function gerarPdfProntuario(Request $request, $id){
        
        ini_set('max_execution_time', 3000);
        ini_set('memory_limit','256M');

        
        // ------------- RECUPERA OS FILTROS ---------------
        $fotos = $request->get('fotos');
        $permissaoFotos = PermissoesController::verifyPermission("InternoController", "showFotos");
        
        $dados =  "dadospessoais";
        
        $advogados = $request->get('advogados');
        $permissaoAdvogados = PermissoesController::verifyPermission("InternoController", "showAdvogados");

        $visita = $request->get('visita');
        $permissaoVisita = PermissoesController::verifyPermission("InternoController", "showVisitantes");

        $historico = $request->get('historico');
        $permissaoHistorico = PermissoesController::verifyPermission("InternoController", "showHistorico");
        
        $companheiros = $request->get('companheiros');
        $permissaoCompanheiros = PermissoesController::verifyPermission("InternoController", "companheirosCela");
        
        $escoltas = $request->get('escoltas');
        $permissaoEscoltas = PermissoesController::verifyPermission("InternoController", "showEscoltas");
        
        $saude = $request->get('saude');
        $permissaoSaude = PermissoesController::verifyPermission("InternoController", "showSaudeAssistencia");
        
        $falta = $request->get('falta');
        $permissaoFalta = PermissoesController::verifyPermission("InternoController", "showDisciplina");

        $ocorrencia = $request->get('ocorrencia');
        $permissaoOcorrencia = PermissoesController::verifyPermission("InternoController", "showOcorrencia");

        $processosCrimes = $request->get('processosCrimes');
        $permissaoProcessosCrimes = PermissoesController::verifyPermission("InternoController", "showProcessos");

        // ---------------------- EXIBIÇÃO PADRÃO DOS FILTROS
        $exibirFotos = 'block';
        $exibirDados = 'block';
        $exibirAdvogado = 'block';
        $exibirVisita = 'block';
        $exibirHistorico = 'block';
        $exibirCompanheiros = 'block';
        $exibirEscoltas = 'block';
        $exibirSaude = 'block';
        $exibirFalta = 'block';
        $exibirProcessos = 'block';

        
        /*
         * 
         */
        
                $interno = DB::table('interno')->where('id', $id)->select('interno.*',DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'interno.medida_seguranca')->get();

        if(count($interno) == 0)
        {
            return redirect()->route('inicio')->withInput()->with('alert-danger', 'Interno não encontrado!');

        }

        $interno = json_decode($interno, true)[0];

        if(!is_null($dados)){
        $outrosnomes = DB::table('outrosnomes')->where('pk_tabela', 'interno')->where('pk_id', $id)->get();
        $interno['outrosnomes'] = [];
        if(!is_null($outrosnomes)){
            foreach(json_decode($outrosnomes,true) as $i){
                array_push($interno['outrosnomes'], $i['nome']);
            }
        }
        $enderecos = DB::table('endereco')->where('fk_tabela', 'interno')->where('fk_id', $id)->get();
        $interno['enderecos'] = [];
        if(!is_null($enderecos)){
            foreach(json_decode($enderecos,true) as $i){
                array_push($interno['enderecos'], $i);
            }
        }
        if(!is_null($interno['rg_data'])) $interno['rg_data'] = Tdata::TdataBR($interno['rg_data']);
        if(!is_null($interno['data_nascimento'])) $interno['data_nascimento'] = Tdata::TdataBR($interno['data_nascimento']);
        if(!is_null($interno['data_nascimento'])) $interno['idade'] = Tdata::getIdade($interno['data_nascimento']);
        else $interno['idade'] = "";
        if(!is_null($interno['created_at'])) $interno['created_at'] = "às ".Tdata::getTempoTimestamp($interno['created_at'])." em ".Tdata::TdataBR(Tdata::getDataTimestamp($interno['created_at']));
        else $interno['created_at'] = "";
        if(!is_null($interno['updated_at'])) $interno['updated_at'] = "às ".Tdata::getTempoTimestamp($interno['updated_at'])." em ".Tdata::TdataBR(Tdata::getDataTimestamp($interno['updated_at']));
        else $interno['updated_at'] = "";

        if(!is_null($interno['profissao'])) $interno['profissao_nome'] = InternoController::getNomeProfissao($interno['profissao']);
        else $interno['profissao_nome'] = "";

        if(!is_null($interno['unidade_prisional'])) $interno['unidade_prisional_nome'] = UnidadesController::getNomeUp($interno['unidade_prisional']);
        else $interno['unidade_prisional_nome'] = "";
        if(!is_null($interno['pavilhao'])) $interno['pavilhao_nome'] = UnidadesController::getNomePav($interno['pavilhao']);
        else $interno['pavilhao_nome'] = "";
        if(!is_null($interno['ala'])) $interno['ala_nome'] = UnidadesController::getNomeAla($interno['ala']);
        else $interno['ala_nome'] = "";
        if(!is_null($interno['cela'])) $interno['cela_nome'] = UnidadesController::getNomeCela($interno['cela']);
        else $interno['cela_nome'] = "";
        if(!is_null($interno['condicao_interno'])) $interno['condicao_nome'] = InternoController::getNomeCondicao($interno['condicao_interno']);
        else $interno['condicao_nome'] = "";
        if(!is_null($interno['faccao'])) $interno['faccao_nome'] = InternoController::getNomeFaccao($interno['faccao']);
        else $interno['faccao_nome'] = "";
        }

        // -------------------------- FOTOS -----------------------------------------------------------------------------------

        //if(!is_null($fotos)){
            $interno['fotos'] = DB::table('foto')->where('fk_tabela', 'interno')->where('fk_id', $id)->select('foto.*',DB::raw("DATE_FORMAT(created_at,'%d/%m/%Y %H:%i:%s') as created_at"),DB::raw("DATE_FORMAT(updated_at,'%d/%m/%Y %H:%i:%s') as updated_at"))->get();
            if($interno['fotos'] != collect([])) $interno['fotos'] = json_decode($interno['fotos'], true);

            $fotoPerfilDados = DB::table('foto')->where('fk_tabela', 'interno')->where('fk_id', $id)->where('tipo','principal')->get();
            if($fotoPerfilDados != collect([])){
                $fotoPerfilDados = json_decode($fotoPerfilDados, true)[0];
                $fotoPerfilDados['nome_arquivo'] =  'storage/app/arquivos/internos/fotos/'.$interno['id'].'/'.$fotoPerfilDados['nome_arquivo'];
                $fotoPerfilDados['created_at'] = Tdata::TdataBR(Tdata::getDataTimestamp($fotoPerfilDados['created_at']));
                $fotoPerfilDados['updated_at'] = Tdata::TdataBR(Tdata::getDataTimestamp($fotoPerfilDados['updated_at']));
            }
            else{
                $fotoPerfilDados['nome_arquivo'] = 'storage/app/semfoto.jpg';
            }
        //}
        // ------------------------- ADVOGADOS -----------------------------------------------------------------------------------

        if(!is_null($advogados)){
            if($permissaoAdvogados){
                $interno['advogados'] = DB::table('advogado_interno')->where('fk_interno', $id)->get();
                $interno['advogados'] = json_decode($interno['advogados'], true);
                foreach($interno['advogados'] as $key => $advogado){
                    $adv = DB::table('advogado')->where('id', $advogado['fk_advogado'])->get();
                    $adv = json_decode($adv, true)[0];
                    $interno['advogados'][$key]['dadosAdvogado'] = $adv;
                    $fotoAdv = DB::table('foto')->where('fk_tabela', 'advogado')->where('fk_id', $advogado['fk_advogado'])->where('tipo','principal')->get();
                    if($fotoAdv != collect([])){
                        $fotoAdv = json_decode($fotoAdv, true)[0];
                        $interno['advogados'][$key]['dadosAdvogado']['fotoPrincipal'] = $fotoAdv;
                        $interno['advogados'][$key]['dadosAdvogado']['fotoPrincipal']['nome_arquivo'] = 'storage/app/arquivos/advogados/fotos/'.$advogado['fk_advogado'].'/'.$fotoAdv['nome_arquivo'];

                    }
                    else {
                        $interno['advogados'][$key]['dadosAdvogado']['fotoPrincipal']['nome_arquivo'] = 'storage/app/semfoto.jpg';
                    }
                    }
            }else{
                
                $exibirAdvogado = 'none';
                $interno['advogados'] = [];
                $advogadoHistorico = [];

            }
        }

        
            // ------------------------- PROCESSOS -----------------------------------------------------------------------------------
        
        if(!is_null($processosCrimes)){
            if($permissaoProcessosCrimes){
                
                $processosDados = DB::table("interno_processos")->where("fk_interno", $id)->where("status", "A")->get();
                $processos = array();
                $skip = 0;
                foreach($processosDados as $processo)
                {

                    if($processo->tipo == "inqueritoPolicial")
                    {

                        $dispositivoLegal = array();

                        $dadosInquerito = DB::table("interno_processos_inquerito")
                            ->where("fk_processo", $processo->id)
                            ->select("interno_processos_inquerito.*",DB::raw("DATE_FORMAT(data_inquerito,'%d/%m/%Y') as dataInquerito"), DB::raw("DATE_FORMAT(data_fato,'%d/%m/%Y') as dataFato"), DB::raw("DATE_FORMAT(data_prisao,'%d/%m/%Y') as dataPrisao"))
                            ->get()[0];

                        $dispositivoLegalDados1 = DB::table("interno_processos_dispositivo_legal")->where("id_processo", $processo->id)->get();

                        foreach($dispositivoLegalDados1 as $lei)
                        {
                            $skip = $skip + 1;
                            if(!is_null($lei->seq_um))
                            {
                                $seqUm = ArtigosProcessosController::getSequencia($lei->seq_um_tipo, $lei->seq_um);
                            }else{
                                $seqUm = null;
                            }
                            if(!is_null($lei->seq_dois))
                            {
                                $seqDois = ArtigosProcessosController::getSequencia($lei->seq_dois_tipo, $lei->seq_dois);
                            }else{
                                $seqDois = null;
                            }
                            array_push($dispositivoLegal, array(
                                "lei" => ArtigosProcessosController::getNomeLei($lei->dispositivo_legal),
                                "artigo" => ArtigosProcessosController::getNomeArtigo($lei->artigo),
                                "nome" => ArtigosProcessosController::getNomeNatureza($lei->artigo),
                                "glossario" => ArtigosProcessosController::getGlossarioNatureza($lei->artigo),
                                "sequenciaUm" => $seqUm,
                                "sequenciaDois" => $seqDois,
                            ));
                        }


                        array_push($processos, array(
                            "tipo" => "Inquérito Policial",
                            "id" => $processo->id,
                            "dadosProcesso" => $dadosInquerito,
                            "dispositivoLegal" => $dispositivoLegal,
                        ));


                    }

                    if($processo->tipo == "processo")
                    {

                        $dispositivoLegal = array();
                        $dadosProcesso = DB::table("interno_processos_processo")
                            ->where("fk_processo", $processo->id)
                            ->select("interno_processos_processo.*",DB::raw("DATE_FORMAT(data_processo,'%d/%m/%Y') as dataProcesso"))
                            ->get()[0];

                        $dispositivoLegalDados2 = DB::table("interno_processos_dispositivo_legal")->where("id_processo", $processo->id)->orderBy("id", "ASC")->get();

                        foreach($dispositivoLegalDados2 as $lei)
                        {
                            $skip = $skip + 1;
                            if(!is_null($lei->seq_um))
                            {
                                $seqUm = ArtigosProcessosController::getSequencia($lei->seq_um_tipo, $lei->seq_um);
                            }else{
                                $seqUm = null;
                            }
                            if(!is_null($lei->seq_dois))
                            {
                                $seqDois = ArtigosProcessosController::getSequencia($lei->seq_dois_tipo, $lei->seq_dois);
                            }else{
                                $seqDois = null;
                            }
                            array_push($dispositivoLegal, array(
                                "lei" => ArtigosProcessosController::getNomeLei($lei->dispositivo_legal),
                                "artigo" => ArtigosProcessosController::getNomeArtigo($lei->artigo),
                                "nome" => ArtigosProcessosController::getNomeNatureza($lei->artigo),
                                "glossario" => ArtigosProcessosController::getGlossarioNatureza($lei->artigo),
                                "sequenciaUm" => $seqUm,
                                "sequenciaDois" => $seqDois,
                            ));
                        }


                        array_push($processos, array(
                            "tipo" => "Processo",
                            "id" => $processo->id,
                            "dadosProcesso" => $dadosProcesso,
                            "dispositivoLegal" => $dispositivoLegal,
                        ));


                    }

                    if($processo->tipo == "condenacao")
                    {
                        $dispositivoLegal = array();
                        $dadosCondenacao = DB::table("interno_processos_condenacao")
                            ->where("fk_processo", $processo->id)
                            ->select("interno_processos_condenacao.*",DB::raw("DATE_FORMAT(data_transito_julgado,'%d/%m/%Y') as dataTransitoJulgado"),DB::raw("DATE_FORMAT(data_sentenca,'%d/%m/%Y') as dataSentenca"))
                            ->get()[0];

                        $dispositivoLegalDados = DB::table("interno_processos_dispositivo_legal")->where("id_processo", $processo->id)->get();

                        foreach($dispositivoLegalDados as $lei)
                        {
                            if(!is_null($lei->seq_um))
                            {
                                $seqUm = ArtigosProcessosController::getSequencia($lei->seq_um_tipo, $lei->seq_um);
                            }else{
                                $seqUm = null;
                            }
                            if(!is_null($lei->seq_dois))
                            {
                                $seqDois = ArtigosProcessosController::getSequencia($lei->seq_dois_tipo, $lei->seq_dois);
                            }else{
                                $seqDois = null;
                            }
                            array_push($dispositivoLegal, array(
                                "lei" => ArtigosProcessosController::getNomeLei($lei->dispositivo_legal),
                                "artigo" => ArtigosProcessosController::getNomeArtigo($lei->artigo),
                                "nome" => ArtigosProcessosController::getNomeNatureza($lei->artigo),
                                "glossario" => ArtigosProcessosController::getGlossarioNatureza($lei->artigo),
                                "sequenciaUm" => $seqUm,
                                "sequenciaDois" => $seqDois,
                            ));
                        }


                        array_push($processos, array(
                            "tipo" => "Condenação",
                            "id" => $processo->id,
                            "dadosProcesso" => $dadosCondenacao,
                            "dispositivoLegal" => $dispositivoLegal,
                        ));


                    }
                }
                
            }
        }
            // ------------------------- VISITANTES -----------------------------------------------------------------------------------

        if(!is_null($visita)){
            if($permissaoVisita){
                $interno['visitas'] = DB::table('visita_interno')->where('fk_interno', $id)->get();
                $interno['visitas'] = json_decode($interno['visitas'], true);
                foreach($interno['visitas'] as $key => $visitanteInterno){
                    $vis = DB::table('visita')->where('id', $visitanteInterno['fk_visita'])->get();
                    $vis = json_decode($vis, true)[0];
                    $interno['visitas'][$key]['dadosVisita'] = $vis;
                    $fotoVis = DB::table('foto')->where('fk_tabela', 'visita')->where('fk_id', $visitanteInterno['fk_visita'])->where('tipo','principal')->get();
                    if($fotoVis != collect([])){
                        $fotoVis = json_decode($fotoVis, true)[0];
                        $interno['visitas'][$key]['dadosVisita']['fotoPrincipal'] = $fotoVis;
                        $interno['visitas'][$key]['dadosVisita']['fotoPrincipal']['nome_arquivo'] = 'storage/app/arquivos/visitas/fotos/'.$visitanteInterno['fk_visita'].'/'.$fotoVis['nome_arquivo'];
                    }
                    else {
                        $interno['visitas'][$key]['dadosVisita']['fotoPrincipal']['nome_arquivo'] = 'storage/app/semfoto.jpg';
                    }

                    $interno['visitas'][$key]['statusSuspensao'] = DB::table('controle_acesso_visita_interno_suspenso')->where('fk_visita', $visitanteInterno['fk_visita'])->where('fk_interno',$interno['id'])->get();
                    if($interno['visitas'][$key]['statusSuspensao'] != collect([])){
                        $interno['visitas'][$key]['statusSuspensao'] = json_decode($interno['visitas'][$key]['statusSuspensao'], true)[0]['status_suspensao'];
                        if($interno['visitas'][$key]['statusSuspensao'] == 'A'){
                            $interno['visitas'][$key]['statusSuspensao'] = "Visita Suspensa";
                        } else if($interno['visitas'][$key]['statusSuspensao'] == 'I'){
                            $interno['visitas'][$key]['statusSuspensao'] = "Visita Autorizada";
                        }
                    } else{
                        $interno['visitas'][$key]['statusSuspensao'] = "Visita Autorizada";
                    }
                    if($interno['visitas'][$key]['dadosVisita']['tipo'] == 'P'){
                        $interno['visitas'][$key]['statusSuspensao'] = "Visita Proibida";
                    }
                }


                $i = 0;
                $keys = array_keys($interno);
                foreach($interno as $item){
                    if(is_null($item) || $item == ""){
                        $interno[$keys[$i]] = "Não Informado";
                    }
                    $i++;
                }
            }else{
                $exibirVisita = 'none';
                $interno['visitas'] = [];
                $visitanteHistorico = [];
            }
        }

        // ------------------------- Historico -----------------------------------------------------------------------------------

        if($historico){
                if($permissaoHistorico){
                $dados = DB::table('movimentacao_interno')
                    ->where('fk_interno', $id)
                    ->where('status', 'Finalizada')
                    ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
                    ->select('movimentacao.*','movimentacao_interno.*')
                    ->orderBy('movimentacao.data', 'DESC');
                $dados = $dados->get();




                if(!empty(json_decode($dados)))
                {
                    $historicos = json_decode($dados, true);
        //            dd($historicos);


                    foreach($historicos as $key=>$historico){

                        $dadosLog = DB::table('log')->where('tabela_alterada', 'movimentacao')->where('tipo_alteracao', 'add')->where('fk_id', $historico['fk_movimentacao'])->get();
                        if($dadosLog == collect([])){
                            $dadosLog = DB::table('log')->where('tabela_alterada', 'interno')->where('tipo_alteracao', 'add')->where('fk_id', $historico['fk_interno'])->get();
                        }
                        if($historico['motivo'] == 'Registro da última data de entrada em unidade prisional que constava no banco de dados do '.config('app.name').' em 16/02/2019 para este interno, com a finalidade de possibilitar a emissão de atestados e certidões pós migração de banco de dados entre a antiga e nova versão do '.config('app.name').'. O horário da entrada (08h01) é meramente exemplificativo, porém a data está em conformidade com os registros do banco de dados anterior.'){
                            $dadosLog = collect([]);
                        }
                        $logHistoricoCancelamento = array();
                        if($historico['tipo'] == 'transferencia' && $historico['status'] == 'Cancelada'){
                            $dadosLogC = DB::table('log')->where('tabela_alterada', 'movimentacao_interno')->where('tipo_alteracao', 'update')->where('fk_id', $historico['id'])->get();

                            foreach ($dadosLogC as $log) {
                                $nomeServidor = FacadeController::getNomeServidorCpf($log->cpf_usuario);

                                array_push($logHistoricoCancelamento, array(
                                    'data' => Tdata::TdataBR(Tdata::getDataTimestamp($log->created_at)),
                                    'hora' => Tdata::getTempoTimestamp($log->created_at),
                                    'tabela' => str_replace("_", " ", $log->tabela_alterada),
                                    'descricao' => $log->texto,
                                    'realizadoPor' => utf8_encode($nomeServidor) . " (" . $log->cpf_usuario . ")",
                                ));
                            }
                        }

                        $logHistorico = array();
                        foreach ($dadosLog as $log) {
                            $nomeServidor = FacadeController::getNomeServidorCpf($log->cpf_usuario);

                            array_push($logHistorico, array(
                                'data' => Tdata::TdataBR(Tdata::getDataTimestamp($log->created_at)),
                                'hora' => Tdata::getTempoTimestamp($log->created_at),
                                'tabela' => str_replace("_", " ", $log->tabela_alterada),
                                'descricao' => $log->texto,
                                'realizadoPor' => utf8_encode($nomeServidor) . " (" . $log->cpf_usuario . ")",
                            ));
                        }


                        if($historicos[$key]['data'] != null){
                            $historicos[$key]['mesData'] = Tdata::getMesExtAbrev(Tdata::getMesData(Tdata::getDataTimestamp($historicos[$key]['data'])));
                        } else {
                            $historicos[$key]['mesData'] =  "Sem informação";
                        }


                        $historicos[$key]['nomeUnidade_destino'] = UnidadesController::getNomeUp($historicos[$key]['unidade_destino']);
                        $historicos[$key]['nomePavilhao_destino'] = UnidadesController::getNomePav($historicos[$key]['pavilhao_destino']);
                        $historicos[$key]['nomeAla_destino'] = UnidadesController::getNomeAla($historicos[$key]['ala_destino']);
                        $historicos[$key]['nomeCela_destino'] = UnidadesController::getNomeCela($historicos[$key]['cela_destino']);
                        $historicos[$key]['nomeUp_anterior'] = UnidadesController::getNomeUp($historicos[$key]['up_anterior']);
                        $historicos[$key]['nomePv_anterior'] = UnidadesController::getNomePav($historicos[$key]['pv_anterior']);
                        $historicos[$key]['nomeAla_anterior'] = UnidadesController::getNomeAla($historicos[$key]['ala_anterior']);
                        $historicos[$key]['nomeCela_anterior'] = UnidadesController::getNomeCela($historicos[$key]['cela_anterior']);
                        $historicos[$key]['log'] = $logHistorico;
                        $historicos[$key]['logCancelamento'] = $logHistoricoCancelamento;

                    }


                }else{
                    $historicos = null;
                }

            }else{
                $exibirHistorico = 'none';
                $historicos = [];

            }
        }

        // ------------------------- !Historico ----------------------------------------------------------------------------------


        // ------------------------- Visita historico -----------------------------------------------------------------------------------

        if(!is_null($visita)){
        $visitanteHistoricoDados = DB::table('controle_acesso_visita_interno')->where('fk_interno', $id)->orderBy('data', 'DESC')->orderBy('horario', 'DESC')->limit(50)->get();

        //dd($visitanteHistoricoDados);

        $visitanteHistorico = array();

        foreach($visitanteHistoricoDados as $visitanteHistoricoDado)
        {
            $cpfVisita = VisitaController::getCpfVisita($visitanteHistoricoDado->fk_visita);

            $dadosSaida = DB::table('controle_acesso')->where('tabela', 'visita')->where('cpf', $cpfVisita)->where('data', $visitanteHistoricoDado->data)->where('horario', $visitanteHistoricoDado->horario)->get();

            //dd($dadosSaida[0]->horario_saida == null || $dadosSaida[0]->data_saida == null);
            $iterador = 0;
            $dadoSaidaHorario = array();
            $dadoSaidaData = array();
            foreach($dadosSaida as $dadoSaidaIt){
                if($iterador < 1){
                    $dadoSaidaHorario = $dadoSaidaIt->horario_saida;
                    $dadoSaidaData = $dadoSaidaIt->data_saida;
                    $iterador = $iterador + 1;
                }
            }


            if($dadoSaidaHorario == null ||$dadoSaidaData == null)
            {
                $duracao = "";
                $horaSaida = "";
                $dataSaida = "";
            }else{
                $duracao = ( strtotime($dadoSaidaHorario) - strtotime($visitanteHistoricoDado->horario) ) / 60 ."min";
                $horaSaida = $dadoSaidaHorario;
                $dataSaida =  $dadoSaidaData;
            }

            array_push($visitanteHistorico, array(
                'nomeVisita' => VisitaController::getNomeVisita($visitanteHistoricoDado->fk_visita),
                'fotoVisita' => VisitaController::getFotoVisita($visitanteHistoricoDado->fk_visita),
                'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($visitanteHistoricoDado->data)),
                'relacao' => InternoController::getRelacaoVisita($visitanteHistoricoDado->fk_interno, $visitanteHistoricoDado->fk_visita),
                'horaEntrada' => $visitanteHistoricoDado->horario,
                'dataEntrada' => Tdata::TdataBR($visitanteHistoricoDado->data),
                'horaSaida' => $horaSaida,
                'dataSaida' => Tdata::TdataBR($dataSaida),
                'duracao' => $duracao
            ));
        }
        }
        
        //dd($visitanteHistorico);

        // ------------------------- !Visita historico ----------------------------------------------------------------------------------


        // ------------------------- Atendimento advogado historico ---------------------------------------------------------------------

        if(!is_null($advogados)){
            $advogadoHistoricoDados = DB::table('advogado_atendimento')->where('fk_interno', $id)->orderBy('data_atendimento', 'DESC')->orderBy('horaInicio', 'DESC')->limit(50)->get();

            $advogadoHistorico = array();

            foreach($advogadoHistoricoDados as $advogadoHistoricoDado)
            {
                $dadosAdv = AdvogadoController::getDadosAdv($advogadoHistoricoDado->fk_advogado);
                array_push($advogadoHistorico, array(
                    'nomeAdv' => $dadosAdv[0]->nome,
                    'fotoAdv' => asset(AdvogadoController::getFotoAdv($advogadoHistoricoDado->fk_advogado)),
                    'data' => Tdata::TdataBR($advogadoHistoricoDado->data_atendimento),
                    'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($advogadoHistoricoDado->data_atendimento)),
                    'oab' => $dadosAdv[0]->oab_numero."/".$dadosAdv[0]->oab_uf,
                    'horaInicio' => $advogadoHistoricoDado->horaInicio,
                    'horaTermino' => $advogadoHistoricoDado->horaTermino,
                    'duracao' => $advogadoHistoricoDado->duracao."min.",
                ));

            }
        }

//        dd($advogadoHistorico);

        // ------------------------- !Atendimento advogado historico ----------------------------------------------------------------------------------

        // ------------------------- Disciplina -----------------------------------------------------------------------------------

        if(!is_null($falta)){
            if($permissaoFalta){
                $dadosDisciplina = DB::table('sindicancia_interno')->leftJoin('sindicancia', 'sindicancia.id','=','sindicancia_interno.fk_sindicancia')->where("fk_interno", $id)->orderBy('sindicancia.data_incidente', 'DESC')->orderBy('fk_sindicancia', 'DESC')->get();
                $faltas = array();
                foreach ($dadosDisciplina as $dadosDiscip) {

                    $sindicanciaDados = array();
                    $dadosSindicancia = DB::table('sindicancia')->where('id', $dadosDiscip->fk_sindicancia)->get();
                    foreach($dadosSindicancia as $dadosSind)
                    {
                        array_push($sindicanciaDados, array(
                           'id' => $dadosSind->id,
                           'unidade' => UnidadesController::getNomeUp($dadosSind->unidade),
                           'titulo' => $dadosSind->titulo,
                           'status' => $dadosSind->status,
                           'gravidadeFalta' => $dadosSind->gravidade_falta,
                           'dataIncidente' => $dadosSind->data_incidente,
                           'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($dadosSind->data_incidente)),
                        ));
                    }

                    $agentesSindicancia = array();
                    $dadosSindicanciaAgentes = DB::table('sindicancia_agentes')->where('fk_sindicancia', $dadosDiscip->fk_sindicancia)->get();
                    if(count($dadosSindicanciaAgentes) > 0) {
                        foreach ($dadosSindicanciaAgentes as $dadosSindicanciaAgente) {
                            array_push($agentesSindicancia, array(
                                'nomeAgente' => FacadeController::getNomeServidor($dadosSindicanciaAgente->matricula),
                                'matricula' => $dadosSindicanciaAgente->matricula
                            ));
                        }
                    }else{
                        $agentesSindicancia = null;
                    }

                    $dadosSindicanciaArquivos = DB::table('sindicancia_arquivos')->where('fk_sindicancia', $dadosDiscip->fk_sindicancia)->get();
                    $arquivosSindicancia = array();
                    if(count($dadosSindicanciaArquivos) > 0)
                    {
                        foreach ($dadosSindicanciaArquivos as $dadosSindicanciaArquivo) {
                            array_push($arquivosSindicancia, array(
                                'idArquivo' => $dadosSindicanciaArquivo->id,
                                'nomeArquivo' => $dadosSindicanciaArquivo->nome_arquivo,
                            ));
                        }
                    }else{
                        $arquivosSindicancia = null;
                    }
                    array_push($faltas, array(
                        'dadosSindicancia' => $sindicanciaDados,
                        'dadosSindicanciaAgentes' => $agentesSindicancia,
                        'dadosSindicanciaArquivos' => $arquivosSindicancia,
                    ));
                }
            }else{
                $exibirFalta = 'none';
                $faltas = [];

            }
        }

        //dd($faltas);

        // ------------------------- !Disciplina ----------------------------------------------------------------------------------

        // ------------------------- Escoltas -----------------------------------------------------------------------------------
        
        if(!is_null($escoltas)){
            if($permissaoEscoltas){
                    $escoltaDados = DB::table('escolta_internos')
                        ->leftJoin('escolta', 'escolta.id','=', 'escolta_internos.fk_escolta')
                        ->where('escolta_internos.fk_interno', $id)
                        ->where('escolta.status', 'Finalizada')
                        ->orderBy('escolta.data_saida', 'DESC')
                        ->get();

                    $escolta = array();
                    foreach($escoltaDados as $escoltaDado)
                    {
                        $dadosEscoltas = DB::table('escolta')->where('id', $escoltaDado->fk_escolta)->get();
                        $dadosEscoltaArray = array();
                        if(count($dadosEscoltas) > 0){
                            foreach($dadosEscoltas as $dadosEscolta)
                            {
                                if($dadosEscolta->vtr_comandante != null)
                                {
                                    $comandante = FacadeController::getNomeServidor($dadosEscolta->vtr_comandante)." (".$dadosEscolta->vtr_comandante.")";
                                }else{
                                    $comandante = null;
                                }

                                if($dadosEscolta->vtr_motorista != null)
                                {
                                    $motorista = FacadeController::getNomeServidor($dadosEscolta->vtr_motorista)." (".$dadosEscolta->vtr_motorista.")";
                                }else{
                                    $motorista = null;
                                }

                                $internosEscoltaArray = array();

                                    $internosEscolta = EscoltaController::getInternosEscolta($dadosEscolta->id, $id);

                                    foreach($internosEscolta as $internoEsc)
                                    {
                                        array_push($internosEscoltaArray, array(
                                            'nome' => self::getInternoNome($internoEsc->fk_interno),
                                            'prontuario' => $internoEsc->fk_interno,
                                        ));
                                    }

                                array_push($dadosEscoltaArray, array(
                                   'id' => $dadosEscolta->id,
                                   'unidade' => UnidadesController::getNomeUp($dadosEscolta->unidade),
                                   'tipo' => $dadosEscolta->tipo,
            //                       'fkMovimentacao' => $dadosEscolta->fk_movimentacao,
                                   'motivo' => $dadosEscolta->motivo,
                                   'destino' => $dadosEscolta->destino,
                                   'dataSaida' => Tdata::TdataBR($dadosEscolta->data_saida),
                                   'horaSaida' => $dadosEscolta->hora_saida,
                                   'dataRetorno' => Tdata::TdataBR($dadosEscolta->data_retorno),
                                    'horaRetorno' => $dadosEscolta->hora_retorno,
                                    'vtrPlaca' => $dadosEscolta->vtr_placa,
                                   'vtrComandante' =>  $comandante,
                                   'matComandante' =>  $dadosEscolta->vtr_comandante,
                                   'vtrMotorista' => $motorista,
                                   'internosEscolta' => $internosEscoltaArray,
                                   'status' => $dadosEscolta->status,
                                   'obs' => $dadosEscolta->obs,
                                ));
                            }

                        }else{

                            $dadosEscoltaArray = null;

                        }


                        $dadosEscoltaAgentes = DB::table('escolta_agentes')->where('fk_escolta', $escoltaDado->fk_escolta)->get();
                        $dadosEscoltaAgentesArray = array();
                        if(count($dadosEscoltaAgentes) > 0)
                        {
                            foreach($dadosEscoltaAgentes as $dadosEscoltaAgente)
                            {
                                if($dadosEscoltaArray[0]['matComandante'] != null && $dadosEscoltaAgente->matricula == $dadosEscoltaArray[0]['matComandante'])
                                {
                                    array_unshift($dadosEscoltaAgentesArray, array(
                                        'nomeAgente' => "[COMANDANTE DA ESCOLTA] ".FacadeController::getNomeServidor($dadosEscoltaAgente->matricula),
                                        'matricula' => $dadosEscoltaAgente->matricula

                                    ));

                                }else{

                                    array_push($dadosEscoltaAgentesArray, array(
                                        'nomeAgente' => FacadeController::getNomeServidor($dadosEscoltaAgente->matricula),
                                        'matricula' => $dadosEscoltaAgente->matricula

                                    ));

                                }
                            }
                        }else{
                            $dadosEscoltaAgentesArray = null;
                        }

                        $dadosEscoltaArquivos = DB::table('escolta_arquivos')->where('fk_escolta', $dadosEscoltaArray[0]['id'])->get();
                        $dadosEscoltaArquivosArray = array();
                        if(count($dadosEscoltaArquivos) > 0)
                        {
                            foreach($dadosEscoltaArquivos as $arquivos)
                            {
                                array_push($dadosEscoltaArquivosArray, array(
                                    'idArquivo' => $arquivos->id,
                                    'nomeArquivo' => $arquivos->nome_arquivo,
                                ));

                            }
                        }else{

                            $dadosEscoltaArquivosArray = null;
                        }

                        $dadosLogEscolta = DB::table('log')->where('tabela_alterada', 'escolta')->where('tipo_alteracao', 'add')->where('fk_id', $dadosEscoltaArray[0]['id'])->get();
                        $logEscolta = array();
                            foreach ($dadosLogEscolta as $log) {
                                $nomeServidor = FacadeController::getNomeServidorCpf($log->cpf_usuario);

                                array_push($logEscolta, array(
                                    'data' => Tdata::TdataBR(Tdata::getDataTimestamp($log->created_at)),
                                    'hora' => Tdata::getTempoTimestamp($log->created_at),
                                    'tabela' => str_replace("_", " ", $log->tabela_alterada),
                                    'descricao' => $log->texto,
                                    'realizadoPor' => $nomeServidor . " (" . $log->cpf_usuario . ")",
                                ));
                            }


                        array_push($escolta, array(
                            'dadosEscolta' => $dadosEscoltaArray,
                            'dadosAgentes' => $dadosEscoltaAgentesArray,
                            'dadosArquivos' => $dadosEscoltaArquivosArray,
                            'dadosLog' => $logEscolta,
                            'dataEscolta' => Tdata::TdataMysql($dadosEscoltaArray[0]['dataSaida']),
                        ));
                    }

                    $escoltaDadosGEP = DB::table('gep_internos')
                        ->leftJoin('gep', 'gep.id','=', 'gep_internos.fk_gep')
                        ->where('gep_internos.fk_interno', $id)
                        ->where('gep.status', 'Finalizada')
                        ->orderBy('gep.data_saida', 'DESC')
                        ->get();

                    foreach($escoltaDadosGEP as $escoltaDadoGEP)
                    {
                        $dadosEscoltasGEP = DB::table('gep')->where('id', $escoltaDadoGEP->fk_gep)->get();
                        $dadosEscoltaArrayGEP = array();
                        if(count($dadosEscoltasGEP) > 0){

                            foreach($dadosEscoltasGEP as $dadosEscoltaGEP)
                            {
                                if($dadosEscoltaGEP->vtr_comandante != null)
                                {
                                    $comandanteGEP = FacadeController::getNomeServidor($dadosEscoltaGEP->vtr_comandante)." (".$dadosEscoltaGEP->vtr_comandante.")";
                                }else{
                                    $comandanteGEP = null;
                                }

                                if($dadosEscoltaGEP->vtr_motorista != null)
                                {
                                    $motoristaGEP = FacadeController::getNomeServidor($dadosEscoltaGEP->vtr_motorista)." (".$dadosEscoltaGEP->vtr_motorista.")";
                                }else{
                                    $motoristaGEP = null;
                                }

                                $internosEscoltaArray = array();

                                $internosEscolta = GepController::getInternosEscolta($dadosEscoltaGEP->id,$id);

                                foreach($internosEscolta as $internoEsc)
                                {
                                    array_push($internosEscoltaArray, array(
                                        'nome' => self::getInternoNome($internoEsc->fk_interno),
                                        'prontuario' => $internoEsc->fk_interno,
                                    ));
                                }

                                $idEscolta = GepController::getEscolta($dadosEscoltaGEP->id, $id);

                                array_push($dadosEscoltaArrayGEP, array(
                                   'id' => $dadosEscoltaGEP->id,
                                   'unidade' => UnidadesController::getNomeUp(EscoltaController::getUnidadeEscolta($idEscolta)),
                                   'tipo' => 'GEP',
            //                       'fkMovimentacao' => EscoltaController::getMovimentacaoEscolta($idEscolta),
                                   'motivo' => null,
                                   'destino' => EscoltaController::getDestinoEscolta($idEscolta),
                                   'dataSaida' => Tdata::TdataBR($dadosEscoltaGEP->data_saida),
                                   'horaSaida' => $dadosEscoltaGEP->hora_saida,
                                   'dataRetorno' => Tdata::TdataBR($dadosEscoltaGEP->data_retorno),
                                    'horaRetorno' => $dadosEscoltaGEP->hora_retorno,
                                    'vtrPlaca' => $dadosEscoltaGEP->vtr_placa,
                                   'vtrComandante' =>  $comandanteGEP,
                                   'matComandante' =>  $dadosEscoltaGEP->vtr_comandante,
                                   'vtrMotorista' => $motoristaGEP,
                                    'internosEscolta' => $internosEscoltaArray,
                                    'status' => $dadosEscoltaGEP->status,
                                   'obs' => null,
                                ));
                            }

                        }else{

                            $dadosEscoltaArrayGEP = null;

                        }

                        $dadosEscoltaAgentesGEP = DB::table('gep_agentes')->where('fk_gep', $escoltaDadoGEP->fk_gep)->get();
                        $dadosEscoltaAgentesArrayGEP = array();
                        if(count($dadosEscoltaAgentesGEP) > 0)
                        {
                            foreach($dadosEscoltaAgentesGEP as $dadosEscoltaAgenteGEP)
                            {
                                if($dadosEscoltaArrayGEP[0]['matComandante'] != null && $dadosEscoltaAgenteGEP->matricula == $dadosEscoltaArrayGEP[0]['matComandante'])
                                {
                                    array_unshift($dadosEscoltaAgentesArrayGEP, array(
                                        'nomeAgente' => "[COMANDANTE DA ESCOLTA] ".FacadeController::getNomeServidor($dadosEscoltaAgenteGEP->matricula),
                                        'matricula' => $dadosEscoltaAgenteGEP->matricula

                                    ));

                                }else{

                                    array_push($dadosEscoltaAgentesArrayGEP, array(
                                        'nomeAgente' => FacadeController::getNomeServidor($dadosEscoltaAgenteGEP->matricula),
                                        'matricula' => $dadosEscoltaAgenteGEP->matricula

                                    ));

                                }
                            }
                        }else{
                            $dadosEscoltaAgentesArrayGEP = null;
                        }


                            $dadosEscoltaArquivosArrayGEP = null;

                        array_push($escolta, array(
                            'dadosEscolta' => $dadosEscoltaArrayGEP,
                            'dadosAgentes' => $dadosEscoltaAgentesArrayGEP,
                            'dadosArquivos' => $dadosEscoltaArquivosArrayGEP,
                            'dadosLog' => null,
                            'dataEscolta' => Tdata::TdataMysql($dadosEscoltaArrayGEP[0]['dataSaida']),
                        ));
                    }

                    $escolta = collect($escolta)->sortBy('dataEscolta')->reverse()->toArray();
            }else{
                $exibirEscoltas = 'none';
                $escolta = [];
            }
        } // FIM de escolta diferente de null
        
        // ------------------------- !Escoltas ----------------------------------------------------------------------------------

        // ------------------------- Saude e Assistencia ----------------------------------------------------------------------------------

        if(!is_null($saude)){
            if($permissaoSaude){
                $saudeAssistDados = DB::table('saude_assistencia');
                $saudeAssistDados->leftJoin('unidade', 'unidade.id', '=', 'saude_assistencia.unidade');
                $saudeAssistDados->leftJoin('interno', 'interno.id', '=', 'saude_assistencia.fk_interno');
                $saudeAssistDados->where('saude_assistencia.fk_interno', $id);
                $saudeAssistDados->where('saude_assistencia.status', 'A');
                $saude = $saudeAssistDados->select('saude_assistencia.*', DB::raw("DATE_FORMAT(saude_assistencia.data_atendimento,'%d/%m/%Y') as data_atendimento"),'unidade.sigla AS unidadeNome','interno.nome AS nomeInterno','interno.id AS idInterno')->orderBy('saude_assistencia.data_atendimento', 'DESC')->orderBy('saude_assistencia.hora_atendimento', 'DESC')->get();


                $saudeAssist = array();

                foreach($saude as $item)
                {

                    if(count(LogController::getDadosLog($item->id,"saude_assistencia","add")) != 0) {
                        $dadosLog = LogController::getDadosLog($item->id, "saude_assistencia", "add");
                        $userLog = UsuarioController::getNomeUser($dadosLog[0]->cpf_usuario);
                        $textoLog = "Registrado por: " . $userLog . " (" . $dadosLog[0]->cpf_usuario . ") em " . Tdata::TtimestampBR($dadosLog[0]->created_at);
                    }
                    else{
                        $textoLog = "Sem log registrado";
                    }

                    array_push($saudeAssist, array(
                        'setor' => $item->setor,
                        'dataAtendimento' => $item->data_atendimento,
                        'horaAtendimento' => $item->hora_atendimento,
                        'descricao' => $item->descricao,
                        'unidadeNome' => $item->unidadeNome,
                        'crm' => $item->crm_medico,
                        'nomeMedico' => $item->nome_medico,
                        'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData(Tdata::TdataMysql($item->data_atendimento))),
                        'log' => $textoLog,

                    ));
                }
            }else{
                $exibirSaude = 'none';
                $saudeAssist = [];
            }
        }

        // ------------------------- !Saude e Assistencia ---------------------------------------------------------------------------------



        if(!is_null($ocorrencia)){
            if($permissaoOcorrencia){
                $ocorrencias = DB::table('ocorrencia_envolvidos')
                    ->join('ocorrencia','ocorrencia.id','=','ocorrencia_envolvidos.fk_ocorrencia')
                    ->where('ocorrencia_envolvidos.fk_tabela','interno')
                    ->where('ocorrencia_envolvidos.fk_id_envolvido',$id)
                    ->select('ocorrencia.id','ocorrencia.natureza','ocorrencia.data','ocorrencia.horario','ocorrencia.descricao')
                    ->orderBy('ocorrencia.data','DESC')
                    ->get();
                $ocorrencias = json_decode($ocorrencias, true);
                foreach($ocorrencias as $key => $ocorrencia){
                    $ocorrencias[$key]['mesData'] = Tdata::getMesExtAbrev(Tdata::getMesData($ocorrencias[$key]['data']));
                    $ocorrencias[$key]['arquivos'] = DB::table('ocorrencia_arquivos')->where('fk_ocorrencia', $ocorrencia['id'])->get();
                    $ocorrencias[$key]['arquivos'] = json_decode($ocorrencias[$key]['arquivos'], true);
                    $ocorrencias[$key]['log'] = DB::table('log')->where('tabela_alterada','ocorrencia')->where('tipo_alteracao','add')->where('fk_id',$ocorrencia['id'])->select('log.cpf_usuario','log.created_at')->get();
                    $ocorrencias[$key]['log'] = json_decode($ocorrencias[$key]['log'], true);
                    if($ocorrencias[$key]['log'] != []){
                        $ocorrencias[$key]['log'][0]['realizadoPor'] = utf8_encode(FacadeController::getNomeServidorCpf($ocorrencias[$key]['log'][0]['cpf_usuario'])) . " (" . $ocorrencias[$key]['log'][0]['cpf_usuario'] . ")";
                        $ocorrencias[$key]['log'][0]['data'] = Tdata::getDataBrTimestamp($ocorrencias[$key]['log'][0]['created_at']);
                        $ocorrencias[$key]['log'][0]['hora'] = Tdata::getTempoTimestamp($ocorrencias[$key]['log'][0]['created_at']);
                    }

                    $ocorrencias[$key]['natureza'] = DB::table('ocorrencia_natureza')->where('ocorrencia_natureza.id',$ocorrencias[$key]['natureza'])->select('ocorrencia_natureza.natureza')->get();
                    $ocorrencias[$key]['natureza'] = json_decode($ocorrencias[$key]['natureza'], true)[0]['natureza'];

        //            // internos envolvidos
                    $ocorrencias[$key]['internosEnvolvidos'] = DB::table('ocorrencia_envolvidos')->where('fk_tabela','interno')->where('fk_ocorrencia',$ocorrencias[$key]['id'])->select('fk_id_envolvido')->get();
                    $ocorrencias[$key]['internosEnvolvidos'] = json_decode($ocorrencias[$key]['internosEnvolvidos'], true);

        //            // agentes envolvidos
                    $ocorrencias[$key]['servidoresEnvolvidos'] = DB::table('ocorrencia_envolvidos')->where('fk_tabela','servidor')->where('fk_ocorrencia',$ocorrencias[$key]['id'])->select('fk_id_envolvido')->get();
                    $ocorrencias[$key]['servidoresEnvolvidos'] = json_decode($ocorrencias[$key]['servidoresEnvolvidos'],true);

        //            // advogados envolvidos
                    $ocorrencias[$key]['advogadosEnvolvidos'] = DB::table('ocorrencia_envolvidos')->where('fk_tabela','advogado')->where('fk_ocorrencia',$ocorrencias[$key]['id'])->select('fk_id_envolvido')->get();
                    $ocorrencias[$key]['advogadosEnvolvidos'] = json_decode($ocorrencias[$key]['advogadosEnvolvidos'], true);

        //            // visitas envolvidas
                    $ocorrencias[$key]['visitasEnvolvidas'] = DB::table('ocorrencia_envolvidos')->where('fk_tabela','visita')->where('fk_ocorrencia',$ocorrencias[$key]['id'])->select('fk_id_envolvido')->get();
                    $ocorrencias[$key]['visitasEnvolvidas'] = json_decode($ocorrencias[$key]['visitasEnvolvidas'], true);
                }
            }else{
                $exibirOcorrencia = 'none';
                $ocorrencias = [];
            }
        }

      
        /*
         * 
         * 
         */
        
        if(!is_null($companheiros)){
            if($permissaoCompanheiros){
                $companheirosCela = InternoController::getCompanheirosCela($id);
            }else{
                $exibirCompanheiros = 'none';
                $companheirosCela = [];

            }
        }

        // ------------------- CHECAGEM DOS FILTROS
        if($fotos == null){
            $exibirFotos = 'none';
            $interno['fotos'] = [];
        }
        if($dados == null){
            $exibirDados = 'none';
        }
        if($advogados == null){
            $exibirAdvogado = 'none';
            $interno['advogados'] = [];
            $advogadoHistorico = [];
        }
        if($visita == null){
            $exibirVisita = 'none';
            $interno['visitas'] = [];
            $visitanteHistorico = [];
        }
        if($historico == null){
            $exibirHistorico = 'none';
            $historicos = [];
        }
        if($companheiros == null){
            $exibirCompanheiros = 'none';
            $companheirosCela = [];
        }
        if($escoltas == null){
            $exibirEscoltas = 'none';
            $escolta = [];
        }
        if($saude == null){
            $exibirSaude = 'none';
            $saudeAssist = [];
        }
        if($falta == null){
            $exibirFalta = 'none';
            $faltas = [];
        }
        if($ocorrencia == null){
            $exibirOcorrencia = 'none';
            $ocorrencias = [];
        }
        if($processosCrimes == null){
            $exibirProcessos = 'none';
            $processos = [];
        }


        $pdf = \PDF::loadView('interno.buscar.imprimir', compact('interno', 'fotoPerfilDados', 'historicos', 'companheirosCela', 'escolta', 'saudeAssist', 'faltas', 'exibirFotos', 'exibirDados', 'exibirAdvogado', 'exibirVisita', 'exibirHistorico', 'exibirCompanheiros', 'exibirEscoltas', 'exibirSaude', 'exibirFalta', 'advogadoHistorico', 'visitanteHistorico','ocorrencias','processos','exibirProcessos'))
            ->setOption('encoding', 'UTF-8')
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 30)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('header-html', view('template.header.headerProntuarioPdf'))
            ->setOption ('footer-center', utf8_decode('Em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');
        return $pdf->inline();
    }

    public function enderecoInterno($id) {
        
        $dados = BD::table('endereco')->where('fk_id', $id)->where('fk_tabela', 'interno')->get();
        
    }
    /**
     * editar / faz a edição dos dados do interno
     *
     */
    public function editar(Interno $interno, $id)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dadosInterno = self::getDadosInterno($id);

        if($dadosInterno[0]->unidade_prisional != $lotacaoServidor)
        {
            return redirect()->back()->with('alert-danger', 'Você não tem permissão para alterar o interno de outra unidade');

        }

        $faccao = DB::table('faccao')->orderBy('nome','ASC')->get();

        $interno = $interno->find($id);

        $interno->data_nascimento = Tdata::TdataBR($interno->data_nascimento);
        $interno->rg_data = Tdata::TdataBR($interno->rg_data);

        $outrosNomes = DB::table('outrosnomes')->where('pk_id',$id)->where('pk_tabela','interno')->get();

        $fotos = DB::table('foto')->where('fk_id',$id)->where('fk_tabela','interno')->get();

        $documentos = DB::table('documento')->where('fk_id',$id)->where('fk_tabela','interno')->get();
        foreach($documentos as $documento)
        {
            $documento->created_at = Tdata::TtimestampBR($documento->created_at);
        }


        $endereco = DB::table('endereco')->where('fk_id',$id)->where('fk_tabela','interno')->get();
        
        $filhos = DB::table('filhos')->where('fk_interno', $id)->select('filhos.*', DB::raw("DATE_FORMAT(filhos.data_nascimento,'%d/%m/%Y') as data_nascimento"))->get();

        $telefone = DB::table('contato')->where('fk_id',$id)->where('fk_tabela','interno')->get();

        $profissao = DB::table('profissao')->orderBy('nome', 'ASC')->get();

        $unidades = DB::table('unidade')->where('status','1')->orderBy('sigla', 'ASC')->get();

        $pavilhao = DB::table('pavilhao')->where('fk_up',$interno->unidade_prisional)->where('status','A')->orderBy('nome', 'ASC')->get();

        $ala = DB::table('ala')->where('fk_pavilhao',$interno->pavilhao)->where('status','A')->orderBy('nome', 'ASC')->get();

        $cela = DB::table('cela')->where('fk_ala',$interno->ala)->where('status','A')->orderBy('nome', 'ASC')->get();

        $naturalidadeMunicipio = DB::table('municipio')->where('Uf',$interno->naturalidade_estado)->orderBy('nome', 'ASC')->get();

        $deficiencia = DB::table('interno_deficiencia')->where('fk_interno', $id)->first();

        //dd($interno->condicao_interno);


        return view('interno.cadastro', compact('profissao','unidades','interno','outrosNomes','endereco', 'telefone','naturalidadeMunicipio','pavilhao','ala','cela','fotos','faccao','documentos','filhos','deficiencia'));

    }

    /**
     * grava as alteracoes no BD
     * @param Request $request
     * @param Interno $interno
     * @param $id
     */
    public function update(Request $request, Interno $interno, $id)
    {

        //dd($request->input());

        $diferencas = array();


        // retira do array (por conta do create)
        $dataForm = $request->except('_token','idPessoa', 'outro_nome','enderecosRemovidos','idEnderecoCadastrado','endereco_cep', 'endereco_logradouro', 'endereco_numero', 'endereco_bairro', 'endereco_complemento','endereco_cidade','endereco_estado','TelefoneContato','telefone','inputFile','outrosNomesRemovidos','tipoFoto','tipoFotoRegistrada','legenda','tabelaFoto','inputFileDoc','nomeDoc','filhoNome', 'filhoDataNascimento', 'filhoNumeroCartaoSus', 'filhoNaUnidadePrisional', 'filhoIncapaz','idFilhoCadastrado','filhosRemovidos','possuiDeficiencia','tipoDeficiencia','cadeirante');


        $dataForm['data_nascimento'] = Tdata::TdataMysql($dataForm['data_nascimento']);
        $dataForm['rg_data'] = Tdata::TdataMysql($dataForm['rg_data']);

        // se os campos de datas estivivem vazios, para não retorna erro no bd, assumem o valor null
        if ($dataForm['data_nascimento'] == "") {
            $dataForm['data_nascimento'] = (NULL);
        }

        if ($dataForm['rg_data'] == ""  || $dataForm['rg_data'] == "0000-00-00"){
            $dataForm['rg_data'] = (NULL);
        }


        $cpf = $request->get('cpf');
        if(!is_null($cpf)){
            $cpfInterno = array_first(self::getDadosInternoCpf($request->get('cpf')));
            if(!is_null($cpfInterno)){
                if($cpfInterno->id != $id){
                    return redirect()->back()->with('alert-danger', 'Já existe um interno com este CPF cadastrado!');
                }
            }
        }



        //-------------- outros nomes ----------------------------//

        $outrosNomesRemovidos = $request->get('outrosNomesRemovidos');

        if($outrosNomesRemovidos != null || !empty($outrosNomesRemovidos))
        {
            foreach($outrosNomesRemovidos as $outroNome)
            {
                $outroNomeDell = DB::table('outrosnomes')->where('id', $outroNome)->get();

                // LOG
                $alteracoes = "Exclusão de dados do campo  outros nomes: <i><strong>".$outroNomeDell[0]->nome."</i></strong><br>";
                array_push($diferencas, $alteracoes);

                $deleteOutroNome = DB::table('outrosnomes')->where('id', $outroNome)->delete();


            }

        }

        $outrosNomesPost = $request->get('outro_nome');

        foreach($outrosNomesPost as $outrosNomes) {

            $verificaOutrosNomes = Outrosnomes::where('nome',$outrosNomes)->select('nome')->where('pk_id',$id)->get();

            if(empty(json_decode($verificaOutrosNomes, true)))
            {
                $key = array_search($outrosNomes,$outrosNomesPost);

                $insertOutroNome = DB::table('outrosnomes')->insert([
                        'pk_id' => $id,
                        'pk_tabela' => "interno",
                        'nome' => $outrosNomes
                    ]
                );

                if($insertOutroNome)
                {
                    $alteracoes = "Inserção de dados do campo  outros nomes: <i><strong>".$outrosNomes."</i></strong><br>";
                    array_push($diferencas, $alteracoes);

                }

            }

        }


        //-------------- !! outros nomes -------------------------//

        /**
         * INSERIR FILHOS
         */

        $filhosRemovidos = $request->get('filhosRemovidos');
        if($filhosRemovidos != null || !empty($filhosRemovidos))
        {
            foreach($filhosRemovidos as $fRemover)
            {
                $verificaFilhoDel = DB::table('filhos')->where('id', $fRemover)->get()[0];
                
                $alteracoes = "Exclusão de dados do campo Filhos: <i><strong>Nome: ".$verificaFilhoDel->nome.", Data de Nascimento: ". Tdata::TtimestampBR($verificaFilhoDel->data_nascimento).", Número do Cartão SUS: ".$verificaFilhoDel->numero_cartao_sus.", Está na Unidade Prisional: ".$verificaFilhoDel->na_unidade_prisional.", Incapaz: ".$verificaFilhoDel->incapaz."</i></strong><br>";
                array_push($diferencas, $alteracoes);

                $deleteEndereco = DB::table('filhos')->where('id', $fRemover)->delete();

            }
        }
        
        $filhosDefault = $request->get('filhoNome');
        if(!is_null($filhosDefault[0]))
        {
            $filhosNome = $request->get('filhoNome');
            $filhoDataNascimento = $request->get('filhoDataNascimento');
            $filhoNumeroCartaoSus = $request->get('filhoNumeroCartaoSus');
            $filhoNaUnidadePrisional = $request->get('filhoNaUnidadePrisional');
            $filhoIncapaz = $request->get('filhoIncapaz');
            
            for($f = 0; $f < count($filhosDefault); $f++)
            {

                $dataNascFilho = null;
                
                if(!is_null($filhoDataNascimento[$f]))
                {
                    $dataNascFilho = Tdata::TdataMysql($filhoDataNascimento[$f]);
                }
                
                $verificaFilhos = DB::table('filhos')->where('fk_interno', $id)->where('nome', $filhosNome[$f])->where('data_nascimento', $dataNascFilho)->count();
                
                if($verificaFilhos == 0){

                $insertFilhos = DB::table("filhos")->insert([
                    'fk_interno' => $id,
                    'nome' => $filhosNome[$f],
                    'data_nascimento' => $dataNascFilho,
                    'numero_cartao_sus' => $filhoNumeroCartaoSus[$f],
                    'na_unidade_prisional' => $filhoNaUnidadePrisional[$f],
                    'incapaz' => $filhoIncapaz[$f],
                ]);
                
                
                $alteracoes = "Inclusão de dados do campo Filhos: <i><strong>Nome: ".$filhosNome[$f].", Data de Nascimento: ". $filhoDataNascimento[$f].", Número do Cartão SUS: ".$filhoNumeroCartaoSus[$f].", Está na Unidade Prisional: ".$filhoNaUnidadePrisional[$f].", Incapaz: ".$filhoIncapaz[$f]."</i></strong><br>";
                array_push($diferencas, $alteracoes);

                }
                
            }
        }
        
        //FIM FILHOS


        //-------------- Endereco ----------------------------//

        // remove os enderecos selecionados
        $enderecoRemovido = $request->get('enderecosRemovidos');
        if($enderecoRemovido != null || !empty($enderecoRemovido))
        {
            foreach($enderecoRemovido as $endId){

                $verificaEnderecoDel = DB::table('endereco')->where('id', $endId)->get();

                // LOG
                $alteracoes = "Exclusão de dados do campo endereço: <i><strong>".$verificaEnderecoDel[0]->logradouro.", ".$verificaEnderecoDel[0]->numero.", ".$verificaEnderecoDel[0]->complemento.", ".$verificaEnderecoDel[0]->bairro.", ".$verificaEnderecoDel[0]->cidade."-".$verificaEnderecoDel[0]->estado."</i></strong><br>";
                array_push($diferencas, $alteracoes);

                $deleteEndereco = DB::table('endereco')->where('id', $endId)->delete();

            }

        }
        // faz a busca do endereco, o que nao constar no banco faz a insercao
        $enderecoPost = $request->get('endereco_logradouro');
        $idEnderecoCadastrado = $request->get('idEnderecoCadastrado');

            if($idEnderecoCadastrado != null) {

                $qtdEndCadastrado = count($idEnderecoCadastrado);

                for ($x = 0; $x < $qtdEndCadastrado; $x++) {

                    if ($idEnderecoCadastrado[$x] != 0) {

                        // pega latitude e longitude
                        $address = $request->get('endereco_logradouro')[$x] . " " . $request->get('endereco_numero')[$x] . " " . $request->get('endereco_bairro')[$x] . " " . $request->get('endereco_cidade')[$x] . " " . $request->get('endereco_estado')[$x];
                        $coordenadas = GeocodingMaps::getGeocodeData($address);

                        $insertEndereco = DB::table('endereco')->where('id', $idEnderecoCadastrado[$x])->update([
                            'estado' => $request->get('endereco_estado')[$x],
                            'cidade' => $request->get('endereco_cidade')[$x],
                            'bairro' => $request->get('endereco_bairro')[$x],
                            'cep' => $request->get('endereco_cep')[$x],
                            'logradouro' => $request->get('endereco_logradouro')[$x],
                            'numero' => $request->get('endereco_numero')[$x],
                            'complemento' => $request->get('endereco_complemento')[$x],
                            'latitude' => $coordenadas[0],
                            'longitude' => $coordenadas[1],
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);

                        if ($insertEndereco) {
                            $alteracoes = "Alteração de dados de endereço: <i><strong>" . $address . "</i></strong><br>";
                            array_push($diferencas, $alteracoes);

                        }


                    }
                    if ($idEnderecoCadastrado[$x] == 0) {

                        $address = $request->get('endereco_logradouro')[$x] . " " . $request->get('endereco_numero')[$x] . " " . $request->get('endereco_bairro')[$x] . " " . $request->get('endereco_cidade')[$x] . " " . $request->get('endereco_estado')[$x];
                        $coordenadas = GeocodingMaps::getGeocodeData($address);

                        // faz a insercao do endereco
                        $insertEndereco = DB::table('endereco')->insert([
                            'fk_id' => $id,
                            'fk_tabela' => "interno",
                            'pais' => "null",
                            'estado' => $request->get('endereco_estado')[$x],
                            'cidade' => $request->get('endereco_cidade')[$x],
                            'bairro' => $request->get('endereco_bairro')[$x],
                            'cep' => $request->get('endereco_cep')[$x],
                            'logradouro' => $request->get('endereco_logradouro')[$x],
                            'numero' => $request->get('endereco_numero')[$x],
                            'complemento' => $request->get('endereco_complemento')[$x],
                            'latitude' => $coordenadas[0],
                            'longitude' => $coordenadas[1],
                            'created_at' => date("Y-m-d H:i:s"),
                        ]);

                        if ($insertEndereco) {
                            $alteracoes = "Inclusão de dados de endereço: <i><strong>" . $address . "</i></strong><br>";
                            array_push($diferencas, $alteracoes);

                        }


                    }
                }
            }


        //-------------- !! Endereco -------------------------//



        //-------------- Fone ----------------------------//

        $fonesRemovidos = $request->get('TelefoneContato');

        if($fonesRemovidos != null || !empty($fonesRemovidos))
        {
            foreach($fonesRemovidos as $fone)
            {
                $verificaFoneDel = DB::table('contato')->where('id', $fone)->get();

                // LOG
                $alteracoes = "Exclusão de dados do campo telefone: <i><strong>".$verificaFoneDel[0]->numero."</i></strong><br>";
                array_push($diferencas, $alteracoes);

                $deleteFone = DB::table('contato')->where('id', $fone)->delete();

            }

        }

        $fonePost = $request->get('telefone');

        if($fonePost[0] != null) {
            foreach ($fonePost as $fp) {

                $verificaFone = Contato::where('numero', $fp)->select('numero')->where('fk_id', $id)->get();

                if (empty(json_decode($verificaFone, true))) {
                    // pega o indice do array do endereco
                    $key = array_search($fp, $fonePost);

                    $insertFone = DB::table('contato')->insert([
                            'fk_id' => $id,
                            'fk_tabela' => "interno",
                            'numero' => $fp,
                            'created_at' => date("Y-m-d H:i:s"),
                        ]
                    );

                    if($insertFone)
                    {
                        // LOG
                        $alteracoes = "Inserção de dados do campo telefone: <i><strong>".$fp."</i></strong><br>";
                        array_push($diferencas, $alteracoes);

                    }

                }

            }
        }

        //-------------- !! Fone -------------------------//

        // -------------------- inserir os Fotos --------------------------- //

        if($request->file('inputFile')[0] != null) {

            $qtdFoto = count($request->file('inputFile'));
            $tipoFoto = $request->get('tipoFoto');
            $legenda = $request->get('legenda');

            for ($x = 0; $x < $qtdFoto; $x++) {


                if($tipoFoto[$x] == null)
                {
                    $tipofoto = "outros";
                }else{
                    $tipofoto = $tipoFoto[$x];
                }

                if($tipofoto == "principal")
                {
                    $name = $id .".jpg";

                    $selectFoto = DB::table('foto')->where('fk_id', $id)->where('fk_tabela', 'interno')->where('tipo','principal')->get();

                    if(count(json_decode($selectFoto,true)) > 0) {

                        $renameOld = time() .$selectFoto[0]->nome_arquivo;
                        Storage::move("arquivos/internos/fotos/" . $id . "/" . $selectFoto[0]->nome_arquivo, "arquivos/internos/fotos/" . $id . "/" . $renameOld);

                        $updateTipo = DB::table('foto')->where('id', $selectFoto[0]->id)->update([
                            'tipo' => 'outros',
                            'nome_arquivo' => $renameOld,
                        ]);

                    }

                }else{

                    $name = $id . "__".$x."__" . time() . "_" . $request->file('inputFile')[$x]->getClientOriginalName();

                }

                $destinationPath = Constantes::ARVQUIVOS_INTERNO . "/fotos/" . $id;


                $request->file('inputFile')[$x]->storeAs($destinationPath, $name);
                //Storage::put($name, $destinationPath);


                $insertFoto = DB::table('foto')->insert(
                    ['fk_id' => $id,
                        'fk_tabela' => "interno",
                        'nome_arquivo' => $name,
                        'legenda' => $legenda[$x],
                        'tipo' => $tipofoto,
                        'created_at' => date("Y-m-d H:i:s")
                    ]
                );

                $idFoto = DB::getPdo()->lastInsertId();

                LogController::log($idFoto,"foto","add", Auth::user()->cpf, "Inclusão de foto do interno<br>Arquivo: <i><strong>". $name ."</strong></i> <br>Legenda: <strong><i>".$legenda[$x]."</i></strong><br>Tipo: <strong><i>".$tipoFoto[$x]."</i></strong>");

                if($insertFoto)
                {

                    // LOG
                    $alteracoes = "Inserção de foto <i><strong> arquivo: ".$name." / legenda: ".$legenda[$x]." / tipo: ".$tipoFoto[$x]."</i></strong><br>";
                    array_push($diferencas, $alteracoes);

                }

            }
        }


        // -------------------- !! inserir os Fotos --------------------------- //


        // -------------------- inserir os Documentos --------------------------- //

        if($request->file('inputFileDoc')[0] != null) {

            $qtdDoc = count($request->file('inputFileDoc'));
            $legendaDoc = $request->get('nomeDoc');

            for ($x = 0; $x < $qtdDoc; $x++) {

                if($legendaDoc[$x] == null)
                {
                    return redirect()->back()->with('alert-danger', 'Informe o nome do documento');

                }

                $destinationPath = Constantes::ARVQUIVOS_INTERNO . "/documentos/" . $id;

                $name = $id . "_" . time() . "_" . $request->file('inputFileDoc')[$x]->getClientOriginalName();

                $request->file('inputFileDoc')[$x]->storeAs($destinationPath, $name);
                //Storage::put($name, $destinationPath);

                $insertDoc = DB::table('documento')->insert(
                    ['fk_id' => $id,
                        'fk_tabela' => "interno",
                        'nome_arquivo' => $name,
                        'descricao' => $legendaDoc[$x],
                        'created_at' => date("Y-m-d H:i:s")
                    ]
                );

                $idDoc = DB::getPdo()->lastInsertId();

                LogController::log($idDoc,"documento","add", Auth::user()->cpf, "Inclusão de documento do interno<br>Arquivo: <i><strong>". $name ."</strong></i>");

                if($insertDoc)
                {
                    // LOG
                    $alteracoes = "Inserção de documento <i><strong> arquivo: ".$name." / legenda: ".$legendaDoc[$x]."</i></strong><br>";
                    array_push($diferencas, $alteracoes);

                }

            }
        }


        // -------------------- !! inserir os Documentos --------------------------- //

        // ----------- Deficiencia
        if(DB::table('interno_deficiencia')->where('fk_interno', $id)->count() == 0){
            DB::table('interno_deficiencia')->insert([
                'fk_interno' => $id,
                'deficiente' => $request->get('possuiDeficiencia'),
                'tipo_deficiencia' => $request->get('tipoDeficiencia'),
                'cadeirante' => $request->get('cadeirante'),
                'created_at' => Tdata::TtimeStampHojeMysql(),
            ]);
        }else{
            DB::table('interno_deficiencia')->where('fk_interno', $id)->update([
                'fk_interno' => $id,
                'deficiente' => $request->get('possuiDeficiencia'),
                'tipo_deficiencia' => $request->get('tipoDeficiencia'),
                'cadeirante' => $request->get('cadeirante'),
                'updated_at' => Tdata::TtimeStampHojeMysql(),
            ]);
        }
        // ----------- ! Deficiencia

        //-------------------- LOG -------------------------------------

        $dadosBD = $interno->where('id', $id)->get();
        json_decode($dadosBD[0]);


        foreach ($dataForm as $k => $v) {
            if ($dataForm[$k] != json_decode($dadosBD[0], true)[$k]) {

                // se apagar um dado no form
                if (empty($dataForm[$k])) {

                    $alteracoes = "Exclusão de dados do campo " . ucfirst(str_replace("_", " ", $k)) . ": <i><strong>" . $dataForm[$k] . "</i></strong><br>";
                    array_push($diferencas, $alteracoes);

                }

                // se inserir um dado no form
                if(empty(json_decode($dadosBD[0], true)[$k]))
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


        //-------------- Faz o update no BD ----------------------------//

        $update = $interno->find($id)->update($dataForm);


        //-------------- !! Faz o update no BD -------------------------//


        if ($update) {

            if(!empty($diferencas))
            {
                $textoLog = implode(" ", $diferencas);
                LogController::log($id, "interno","update", Auth::user()->cpf, $textoLog);
            }

            return redirect()->route('interno.buscar')->with('alert-success', 'O cadastro do interno foi alterado com sucesso!');

        }else{

            return redirect()->back()->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }

    }

    public function reativar($id)
    {
        $lotacaoServidor = FacadeController::getLotacaoServidor(Auth::user()->cpf);

        $unidades = DB::table('unidade')->where('sigla',$lotacaoServidor)->orderBy('sigla', 'ASC')->get();

        $nomeInterno = self::getInternoNome($id);

        return view('interno.reativar.reativar', compact('unidades','nomeInterno','id'));
    }

    public function reativarFunction(Request $request, $id)
    {
        if(strlen($request->get('data_entrada')) != 10){
            return redirect()->back()->withInput()->with('alert-danger', 'Data de entrada inválida!');
        }
        
        if(strlen($request->get('hora_entrada')) != 5){
            return redirect()->back()->withInput()->with('alert-danger', 'Hora de entrada inválida!');
        }

        /*
         * faz o envio de mensagem avisando a unidade anterior da reativacao do interno
         */
        $unidadeAnterior = DB::table('interno')->where('id', $id)->select('unidade_prisional')->get()[0]->unidade_prisional;
        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula')[0]->matricula;
        $nomeServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'nome')[0]->nome;
        $unidadeServidorLogado = FacadeController::getLotacaoServidor(Auth::user()->cpf);
        $nomeInterno = InternoController::getInternoNome($id);

        $assunto = "Reativação de Interno pela Unidade ".$unidadeServidorLogado;
        $descricao = "O(A) servidor(a) ".$nomeServidorLogado." (matrícula ".$matriculaServidorLogado."), lotado na unidade ".$unidadeServidorLogado.", reativou o(a) interno(a) ".$nomeInterno." (prontuário ".$id.") em ".Tdata::TtimeStampHojeBr().".";

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
                'destinatario' => $unidadeAnterior,
                'created_at' => date("Y-m-d"),
                'hora' => date("H:i"),
            ]
        );
        // fim envio mensagem

        DB::table('interno')->where('id',$id)->update([
            'condicao_interno' => 644
        ]);

        if($request->get('unidade_prisional') != null)
        {
            $reativar = DB::table('interno')->where('id', $id)->update([
                'status' => 'A',
                'unidade_prisional' => $request->get('unidade_prisional'),
                'pavilhao' => $request->get('pavilhao'),
                'ala' => $request->get('ala'),
                'cela' => $request->get('cela')
            ]);


            if ($reativar) {



                // -------------------- Insert movimentacao --------------------------- //

                $insertMovimentacao = DB::table('movimentacao')->insert([
                    'tipo' => 'entradaunidadeprisional',
                    'unidade_destino' => $request->get('unidade_prisional'),
                    'pavilhao_destino' => $request->get('pavilhao'),
                    'ala_destino' => $request->get('ala'),
                    'cela_destino' => $request->get('cela'),
                    'data' => Tdata::TdataMysql($request->get('data_entrada'))." ".$request->get('hora_entrada').":00",
                    'created_at' => Tdata::TtimeStampHojeMysql(),
                    'motivo' => $request->get('motivo_da_reativacao')
                ]);

                $idMovimentacao = DB::getPdo()->lastInsertId();

                $insertMovimentacaoInterno = DB::table('movimentacao_interno')->insert([
                    'status' => 'Finalizada',
                    'fk_interno' => $id,
                    'fk_movimentacao' => $idMovimentacao,
                ]);

                LogController::log($idMovimentacao,"movimentacao","add", Auth::user()->cpf, "Reativação de interno - Entrada na Unidade Prisional");

                // -------------------- cadastro na sancao --------------------------- //
                $diasTriagem = $request->get('diasTriagem');

                if($diasTriagem != 0) {
                    // $dataHj = Tdata::TdataHojeMysql();
                    $dataInicio = Tdata::TdataMysql($request->get('data_entrada'));
                    $dataFimTriagem = Tdata::addDias($dataInicio, $request->get('diasTriagem'));

                    $inserSancaoTriagem = DB::table('sancao_triagem')->insert([
                        'tipo' => 'triagem',
                        'unidade' => UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf)),
                        'fk_movimentacao' => $idMovimentacao,
                        'fk_interno' => $id,
                        'data_fim' => $dataFimTriagem,
                        'status' => 'A',
                        'motivo' => 'Entrada na unidade prisional',
                        'created_at' => Tdata::TtimeStampHojeMysql(),
                    ]);
                }

                // -------------------- !cadastro na sancao --------------------------- //


                // -------------------- !Insert movimentacao  --------------------------- //


                LogController::log($id,"interno","update", Auth::user()->cpf, "Reativação de interno");


                return redirect()->route('interno.editar',$id)->with('alert-success', 'Interno(a) reativado com sucesso, agora atualize os dados cadastrais dele(a).');

            }else{

                return redirect('interno.buscar')->with('alert-danger', 'Ocorreu um problema ao reativar o interno(a), tente novamente.');

            }

        }
    }

    public function documentoDelete($id, $filename)
    {

        if (file_exists(storage_path('app/arquivos/internos/documentos/' . $id . "/" . $filename))) {

                $move = Storage::move(Constantes::ARVQUIVOS_INTERNO . "/documentos/" . $id . "/" . $filename, Constantes::ARQUIVO_MORTO . "/" . $filename);

                $delete = DB::table('documento')->where('fk_id', $id)->where('fk_tabela', 'interno')->where('nome_arquivo', $filename)->delete();

            }else{

                $delete = DB::table('documento')->where('fk_id', $id)->where('fk_tabela', 'interno')->where('nome_arquivo', $filename)->delete();

                return "errorMove";
            }

        if ($delete) {

            LogController::log($id,"documento","delete", Auth::user()->cpf, "Exclusão de documento do interno<br>Arquivo: <i><strong>". $filename ."</strong></i>");

            return redirect()->back()->with('alert-success', 'Documento deletado com sucesso!');

        }else{

            return redirect()->back()->with('alert-danger', 'Ocorreu um problema ao deletar o documento, por favor, tente novamente!');

        }


    }

    public function separacao($dia)
    {

        if($dia == 0)
        {
            $diaBusca = Tdata::TdataHojeMysql();
            $dia = 1;
            $dataDisplay = Tdata::TdataHoje();

        }else{

            $diaBusca = Tdata::addDias(Tdata::TdataHojeMysql(), $dia);
            $dataDisplay = Tdata::TdataBR(Tdata::addDias(Tdata::TdataHojeMysql(), $dia));
            $dia += 1;
        }

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        /**
         * Agendamento de visitas
         */
//        $agendamentoTeleatendimento = DB::table('visita_agendamento')
//            ->leftJoin('interno', 'interno.id', '=', 'visita_agendamento.fk_interno')
//            ->leftJoin('visita', 'visita.id', '=', 'visita_agendamento.fk_visita')
//            ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
//            ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
//            ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
//            ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
//            ->leftJoin('faccao', 'faccao.id', '=', 'interno.faccao')
//            ->leftJoin('foto', function ($query) {
//                $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('foto.tipo', 'principal');
//            })
//            ->where('visita_agendamento.data', $diaBusca)
//            ->where('visita_agendamento.unidade', $lotacaoServidor)
//            ->where('visita_agendamento.status', 'Agendado')
//            ->select('interno.nome', 'interno.id', 'interno.periculosidade', 'interno.unidade_prisional', 'interno.pavilhao', 'interno.ala', 'interno.cela', 'interno.faccao', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'foto.nome_arquivo AS foto', DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'),'pavilhao.nome as pvNome', 'ala.nome as alNome', 'cela.nome as celNome','visita_agendamento.horario as atendHorario','faccao.nome as facNome','visita.nome AS visNome', 'visita.cpf AS visCpf','visita.email AS visEmail','visita_agendamento.tipo AS tipoTelevisita','visita_agendamento.id AS idTelevisita')
//            ->orderBy('visita_agendamento.horario', 'ASC')
//            ->orderBy('pavilhao.nome', 'ASC')
//            ->orderBy('ala.nome', 'ASC')
//            ->orderBy('cela.nome', 'ASC')
//            ->orderBy('interno.nome', 'ASC')
//            ->get();

        /*dd($agendamentoTeleatendimento);*/

        $atendimentoAdv = DB::table('advogado_agendamento')
            ->leftJoin('interno', 'interno.id', '=', 'advogado_agendamento.fk_interno')
            ->leftJoin('advogado', 'advogado.id', '=', 'advogado_agendamento.fk_advogado')
            ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
            ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
            ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
            ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
            ->leftJoin('faccao', 'faccao.id', '=', 'interno.faccao')
            ->leftJoin('foto', function ($query) {
                $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('tipo', 'principal');
            })
            ->where('advogado_agendamento.data', $diaBusca)
            ->where('advogado_agendamento.unidade', $lotacaoServidor)
            ->where('advogado_agendamento.status', 'Agendado')
            ->select('interno.nome', 'interno.id', 'interno.periculosidade', 'interno.unidade_prisional', 'interno.pavilhao', 'interno.ala', 'interno.cela', 'interno.faccao', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'foto.nome_arquivo AS foto', DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'),'pavilhao.nome as pvNome', 'ala.nome as alNome', 'cela.nome as celNome','advogado_agendamento.horario as atendHorario','faccao.nome as facNome','advogado.nome AS advNome','advogado.cpf AS advCpf','advogado.email AS advEmail','advogado_agendamento.id AS idAgendamento')
            ->orderBy('advogado_agendamento.horario', 'ASC')
            ->orderBy('pavilhao.nome', 'ASC')
            ->orderBy('ala.nome', 'ASC')
            ->orderBy('cela.nome', 'ASC')
            ->orderBy('interno.nome', 'ASC')
            ->get();

        $escoltaGep = DB::table('escolta_internos')
            ->leftJoin('escolta', 'escolta.id', '=', 'escolta_internos.fk_escolta')
            ->leftJoin('interno', 'interno.id', '=', 'escolta_internos.fk_interno')
            ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
            ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
            ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
            ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
            ->leftJoin('faccao', 'faccao.id', '=', 'interno.faccao')
            ->leftJoin('foto', function ($query) {
                $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('foto.tipo', 'principal');
            })
            ->where('escolta_internos.up_anterior', $lotacaoServidor)
            ->where('escolta.status', 'Confirmada')
            ->where('escolta.tipo', 'gep')
            ->where('escolta.data_saida', $diaBusca)
            ->select('interno.nome', 'interno.id', 'interno.periculosidade', 'interno.unidade_prisional', 'interno.pavilhao', 'interno.ala', 'interno.cela', 'interno.faccao', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'foto.nome_arquivo AS foto', DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'),'pavilhao.nome as pvNome', 'ala.nome as alNome', 'cela.nome as celNome','faccao.nome as facNome')
            ->orderBy('pavilhao.nome', 'ASC')
            ->orderBy('ala.nome', 'ASC')
            ->orderBy('cela.nome', 'ASC')
            ->orderBy('interno.nome', 'ASC')
            ->get();

        //dd($escoltaGep);

        $pavilhao = UnidadesController::getPavilhoes($lotacaoServidor);

        return view('interno.separacao.separacao', compact('atendimentoAdv','escoltaGep','pavilhao','dia','dataDisplay'));
    }

    public function saidaSancaoTriagem()
    {

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $saidaSancaoTDados = DB::table('sancao_triagem')
            ->leftJoin('interno', 'interno.id', '=', 'sancao_triagem.fk_interno')
            ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
            ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
            ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
            ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
            ->leftJoin('faccao', 'faccao.id', '=', 'interno.faccao')
            ->leftJoin('foto', function ($query) {
                $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('foto.tipo', 'principal');
            })
            ->where('interno.status', 'A')
            ->where('sancao_triagem.status', 'A')
            ->where('sancao_triagem.tipo', 'sancao')
            ->where('sancao_triagem.regressao_temporaria', 'S')
            ->where('sancao_triagem.unidade', $lotacaoServidor)
            ->select('interno.nome', 'interno.regime', 'interno.sentenciado', 'interno.id', 'interno.periculosidade', 'interno.unidade_prisional', 'interno.pavilhao', 'interno.ala', 'interno.cela', 'interno.faccao', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'foto.nome_arquivo AS foto', DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'),'pavilhao.nome as pvNome', 'ala.nome as alNome', 'cela.nome as celNome',DB::raw("DATE_FORMAT(sancao_triagem.data_fim,'%d/%m/%Y') as dataFim"), 'sancao_triagem.id as idTriagem', 'sancao_triagem.fk_movimentacao as fkMov','faccao.nome as facNome')
            ->orderBy('sancao_triagem.data_fim', 'ASC')
            ->orderBy('pavilhao.nome', 'ASC')
            ->orderBy('ala.nome', 'ASC')
            ->orderBy('cela.nome', 'ASC')
            ->orderBy('interno.nome', 'ASC')
            ->get();

        $saidaSancaoTDados = $saidaSancaoTDados->unique('id');

        $saidaSancaoT = json_decode($saidaSancaoTDados, true);

        //dd($saidaSancao);

        foreach($saidaSancaoT as $key => $saida)
        {

            $saidaSancaoT[$key]['lotacaoAnterior'] = MovimentacoesController::getLotacaoAnteriorMovimentacao($saida['id'], $saida['fkMov']);
            $saidaSancaoT[$key]['dataInteiro'] = Tdata::TdataInteiro(Tdata::TdataMysql($saida['dataFim']));
        }



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
            ->where('interno.status', 'A')
            ->where('sancao_triagem.status', 'A')
            ->where('sancao_triagem.tipo', 'sancao')
            ->where('sancao_triagem.regressao_temporaria', 'N')
            ->where('sancao_triagem.unidade', $lotacaoServidor)
            ->select('interno.nome', 'interno.id', 'interno.periculosidade', 'interno.unidade_prisional', 'interno.pavilhao', 'interno.ala', 'interno.cela', 'interno.faccao', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'foto.nome_arquivo AS foto', DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'),'pavilhao.nome as pvNome', 'ala.nome as alNome', 'cela.nome as celNome',DB::raw("DATE_FORMAT(sancao_triagem.data_fim,'%d/%m/%Y') as dataFim"), 'sancao_triagem.id as idTriagem', 'sancao_triagem.fk_movimentacao as fkMov','faccao.nome as facNome')
            ->orderBy('sancao_triagem.data_fim', 'ASC')
            ->orderBy('pavilhao.nome', 'ASC')
            ->orderBy('ala.nome', 'ASC')
            ->orderBy('cela.nome', 'ASC')
            ->orderBy('interno.nome', 'ASC')
            ->get();

        $saidaSancaoDados = $saidaSancaoDados->unique('id');

        $saidaSancao = json_decode($saidaSancaoDados, true);

        //dd($saidaSancao);

        foreach($saidaSancao as $key => $saida)
        {

            $saidaSancao[$key]['lotacaoAnterior'] = MovimentacoesController::getLotacaoAnteriorMovimentacao($saida['id'], $saida['fkMov']);
            $saidaSancao[$key]['dataInteiro'] = Tdata::TdataInteiro(Tdata::TdataMysql($saida['dataFim']));

        }



        $saidaTriagemDados = DB::table('sancao_triagem')
            ->leftJoin('interno', 'interno.id', '=', 'sancao_triagem.fk_interno')
            ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
            ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
            ->leftJoin('ala', 'ala.id', '=', 'interno.ala')
            ->leftJoin('cela', 'cela.id', '=', 'interno.cela')
            ->leftJoin('faccao', 'faccao.id', '=', 'interno.faccao')
            ->leftJoin('foto', function ($query) {
                $query->on('foto.fk_id', '=', 'interno.id')->where('foto.fk_tabela', '=', 'interno')->where('foto.tipo', 'principal');
            })
            ->where('interno.status', 'A')
            ->where('sancao_triagem.status', 'A')
            ->where('sancao_triagem.tipo', 'triagem')
            ->where('sancao_triagem.unidade', $lotacaoServidor)
            ->select('interno.nome', 'interno.id', 'interno.periculosidade', 'interno.unidade_prisional', 'interno.pavilhao', 'interno.ala', 'interno.cela', 'interno.faccao', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'foto.nome_arquivo AS foto', DB::raw('CONCAT_WS(" - ", unidade.sigla, pavilhao.nome, ala.nome, cela.nome) AS sigla'),'pavilhao.nome as pvNome', 'ala.nome as alNome', 'cela.nome as celNome',DB::raw("DATE_FORMAT(sancao_triagem.data_fim,'%d/%m/%Y') as dataFim"), 'sancao_triagem.id as idTriagem', 'sancao_triagem.fk_movimentacao as fkMov','faccao.nome as facNome')
            ->orderBy('sancao_triagem.data_fim', 'ASC')
            ->orderBy('pavilhao.nome', 'ASC')
            ->orderBy('ala.nome', 'ASC')
            ->orderBy('cela.nome', 'ASC')
            ->orderBy('interno.nome', 'ASC')
            ->get();

        $saidaTriagemDados = $saidaTriagemDados->unique('id');


        $saidaTriagem = json_decode($saidaTriagemDados, true);

//        dd($saidaTriagem);

        foreach($saidaTriagem as $key => $triagem)
        {

            $saidaTriagem[$key]['lotacaoAnterior'] = MovimentacoesController::getLotacaoAnteriorMovimentacao($triagem['id'], $triagem['fkMov']);
            $saidaTriagem[$key]['dataInteiro'] = Tdata::TdataInteiro(Tdata::TdataMysql($triagem['dataFim']));

        }


        $pavilhao = UnidadesController::getPavilhoes($lotacaoServidor);
        $unidadeServidor = DB::table('unidade')->where('id', $lotacaoServidor)->get();

//        dd($saidaSancao);

        return view('interno.sancaoTriagem.sancaoTriagem', compact('saidaSancao', 'saidaSancaoT', 'saidaTriagem', 'unidadeServidor', 'pavilhao'));


    }

    public function saidaSancaoTriagemStore(Request $request, $tipo)
    {
        $idInternos = $request->get('internosCelaCheck');

        //dd($tipo, $request->input());

        if($tipo == "sancao")
        {
            $insertMudancaCela = DB::table('movimentacao')->insert([
                    'tipo' => 'mudancacela',
                    'motivo' => "Saída da Sanção Disciplinar.",
                    'unidade_destino' => $request->get('unidade_prisional'),
                    'pavilhao_destino' => $request->get('pavilhao'),
                    'ala_destino' => $request->get('ala'),
                    'cela_destino' => $request->get('cela'),
                    'data' => Tdata::TdataMysql($request->get('dataSancao'))." ".$request->get('horaSancao').":00",
                    'created_at' => Tdata::TtimeStampHojeMysql()
                ]
            );
            $idMudancaCela = DB::getPdo()->lastInsertId();


            $internosLogMudCel = "";
            $idUpMC = "";

            foreach ($idInternos as $id) {

                $idInterno = explode("-", $id);

                DB::table('movimentacao_interno')->insert([
                        'status' => 'Finalizada',
                        'fk_movimentacao' => $idMudancaCela,
                        'fk_interno' => $idInterno[0],
                        'up_anterior' => UnidadesController::getIdUP($idInterno[0]),
                        'pv_anterior' => UnidadesController::getIdPavilhao($idInterno[0]),
                        'ala_anterior' => UnidadesController::getIdAla($idInterno[0]),
                        'cela_anterior' => UnidadesController::getIdCela($idInterno[0])
                    ]
                );

                DB::table('sancao_triagem')->where("id", $idInterno[1])->update(['status'=>'I']);

                $teste = DB::table('interno')->where('id', $idInterno[0])->update(
                    [
                        'pavilhao' => $request->get('pavilhao'),
                        'ala' => $request->get('ala'),
                        'cela' => $request->get('cela'),
                    ]
                );

                //dd($teste, $request->input());

                $internosLogMudCel = $internosLogMudCel."Prontuário: ".$idInterno[0]." - ".InternoController::getInternoNome($idInterno[0]).", ";
                $idUpMC = UnidadesController::getIdUP($idInterno[0]);
            }

            $textoLog = "Mudança de Cela realizada (retirada de sanção disciplinar). Registrada em ".Tdata::TtimeStampHojeBr().". Origem: ".UnidadesController::getNomeUp($idUpMC).", Destino: ".UnidadesController::getNomeUp(UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf))).", Internos: ";
            $textoLog = $textoLog.substr($internosLogMudCel,0,-2).".";
            LogController::log($idMudancaCela,"movimentacao","add", Auth::user()->cpf, $textoLog);

            if($insertMudancaCela)
            {
                return redirect()->back()->with('alert-success', 'Internos Retirados da sanção com sucesso!');

            }else{

                return redirect()->back()->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');
            }
        }

        if($tipo == "triagem")
        {
            $insertMudancaCela = DB::table('movimentacao')->insert([
                    'tipo' => 'mudancacela',
                    'motivo' => "Saída da Triagem.",
                    'unidade_destino' => $request->get('unidade_prisionalt'),
                    'pavilhao_destino' => $request->get('pavilhaot'),
                    'ala_destino' => $request->get('alat'),
                    'cela_destino' => $request->get('celat'),
                    'data' => Tdata::TdataMysql($request->get('dataTriagem'))." ".$request->get('horaTriagem').":00",
                    'created_at' => Tdata::TtimeStampHojeMysql()
                ]
            );
            $idMudancaCela = DB::getPdo()->lastInsertId();


            $internosLogMudCel = "";
            $idUpMC = "";

            foreach ($idInternos as $id) {

                $idInterno = explode("-", $id);

                DB::table('movimentacao_interno')->insert([
                        'status' => 'Finalizada',
                        'fk_movimentacao' => $idMudancaCela,
                        'fk_interno' => $idInterno[0],
                        'up_anterior' => UnidadesController::getIdUP($idInterno[0]),
                        'pv_anterior' => UnidadesController::getIdPavilhao($idInterno[0]),
                        'ala_anterior' => UnidadesController::getIdAla($idInterno[0]),
                        'cela_anterior' => UnidadesController::getIdCela($idInterno[0])
                    ]
                );

                DB::table('sancao_triagem')->where("id", $idInterno[1])->update(['status'=>'I']);

                $teste = DB::table('interno')->where('id', $idInterno[0])->update(
                    [
                        'pavilhao' => $request->get('pavilhaot'),
                        'ala' => $request->get('alat'),
                        'cela' => $request->get('celat'),
                    ]
                );

                //dd($teste, $request->input());

                $internosLogMudCel = $internosLogMudCel."Prontuário: ".$idInterno[0]." - ".InternoController::getInternoNome($idInterno[0]).", ";
                $idUpMC = UnidadesController::getIdUP($idInterno[0]);
            }

            $textoLog = "Mudança de Cela realizada (retirada da triagem). Registrada em ".Tdata::TtimeStampHojeBr().". Origem: ".UnidadesController::getNomeUp($idUpMC).", Destino: ".UnidadesController::getNomeUp(UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf))).", Internos: ";
            $textoLog = $textoLog.substr($internosLogMudCel,0,-2).".";
            LogController::log($idMudancaCela,"movimentacao","add", Auth::user()->cpf, $textoLog);

            if($insertMudancaCela)
            {
                return redirect()->back()->with('alert-success', 'Internos Retirados da triagem com sucesso!');

            }else{

                return redirect()->back()->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');
            }
        }



    }

    public static function getNomeCondicao($idCondicao)
    {
        if(is_null($idCondicao)) return "";
        $nome = DB::table('condicao')->select('nome')->where('id', $idCondicao)->get();
        return $nome[0]->nome;
    }

    public static function getNomeFaccao($idFaccao)
    {
        if(is_null($idFaccao)) return "Não Informada";
        $nome = DB::table('faccao')->select('nome')->where('id', $idFaccao)->get();
        return $nome[0]->nome;
    }

    public static function getNomeProfissao($idProfissao)
    {
        if(is_null($idProfissao)) return "Não Informada";
        $nome = DB::table('profissao')->select('nome')->where('id', $idProfissao)->get();
        return $nome[0]->nome;
    }

    public static function getStatusCondicao($idCondicao)
    {
        if(is_null($idCondicao)) return "";
        $nome = DB::table('condicao')->select('status')->where('id', $idCondicao)->get();
        return $nome[0]->status;
    }

    // dado o id do interno, retorna seu status "A" ou "I"
    public static function getStatus($id){
        $status = DB::table('interno')->where('id',$id)->first();
        if(isset($status)){
            return $status->status;
        }
        else{
            return "";
        }
    }

    public function show($id, Request $request)
    {

        $interno = DB::table('interno')->where('id', $id)->select('interno.*',DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'interno.medida_seguranca')->get();

        if(count($interno) == 0)
        {
            return redirect()->route('inicio')->withInput()->with('alert-danger', 'Interno não encontrado!');

        }

        $interno = json_decode($interno, true)[0];

        $outrosnomes = DB::table('outrosnomes')->where('pk_tabela', 'interno')->where('pk_id', $id)->get();
        $interno['outrosnomes'] = [];
        if(!is_null($outrosnomes)){
            foreach(json_decode($outrosnomes,true) as $i){
                array_push($interno['outrosnomes'], $i['nome']);
            }
        }
        $enderecos = DB::table('endereco')->where('fk_tabela', 'interno')->where('fk_id', $id)->get();
        $interno['enderecos'] = [];
        if(!is_null($enderecos)){
            foreach(json_decode($enderecos,true) as $i){
                array_push($interno['enderecos'], $i);
            }
        }
        if(!is_null($interno['rg_data'])) $interno['rg_data'] = Tdata::TdataBR($interno['rg_data']);
        if(!is_null($interno['data_nascimento'])) $interno['data_nascimento'] = Tdata::TdataBR($interno['data_nascimento']);
        if(!is_null($interno['data_nascimento'])) $interno['idade'] = Tdata::getIdade($interno['data_nascimento']);
        else $interno['idade'] = "";
        if(!is_null($interno['created_at'])) $interno['created_at'] = "às ".Tdata::getTempoTimestamp($interno['created_at'])." em ".Tdata::TdataBR(Tdata::getDataTimestamp($interno['created_at']));
        else $interno['created_at'] = "";
        if(!is_null($interno['updated_at'])) $interno['updated_at'] = "às ".Tdata::getTempoTimestamp($interno['updated_at'])." em ".Tdata::TdataBR(Tdata::getDataTimestamp($interno['updated_at']));
        else $interno['updated_at'] = "";

        if(!is_null($interno['profissao'])) $interno['profissao_nome'] = InternoController::getNomeProfissao($interno['profissao']);
        else $interno['profissao_nome'] = "";

        if(!is_null($interno['unidade_prisional'])) $interno['unidade_prisional_nome'] = UnidadesController::getNomeUp($interno['unidade_prisional']);
        else $interno['unidade_prisional_nome'] = "";
        if(!is_null($interno['pavilhao'])) $interno['pavilhao_nome'] = UnidadesController::getNomePav($interno['pavilhao']);
        else $interno['pavilhao_nome'] = "";
        if(!is_null($interno['ala'])) $interno['ala_nome'] = UnidadesController::getNomeAla($interno['ala']);
        else $interno['ala_nome'] = "";
        if(!is_null($interno['cela'])) $interno['cela_nome'] = UnidadesController::getNomeCela($interno['cela']);
        else $interno['cela_nome'] = "";
        if(!is_null($interno['condicao_interno'])) $interno['condicao_nome'] = InternoController::getNomeCondicao($interno['condicao_interno']);
        else $interno['condicao_nome'] = "";
        if(!is_null($interno['faccao'])) $interno['faccao_nome'] = InternoController::getNomeFaccao($interno['faccao']);
        else $interno['faccao_nome'] = "";

//      $interno['biometria_sejuc'] = ;
//      $interno['identificacao_criminal_biometrica_sesed'] = ;

        // -------------------------- FOTOS -----------------------------------------------------------------------------------

        $interno['fotos'] = DB::table('foto')->where('fk_tabela', 'interno')->where('fk_id', $id)->select('foto.*',DB::raw("DATE_FORMAT(created_at,'%d/%m/%Y %H:%i:%s') as created_at"),DB::raw("DATE_FORMAT(updated_at,'%d/%m/%Y %H:%i:%s') as updated_at"))->get();
        if($interno['fotos'] != collect([])) $interno['fotos'] = json_decode($interno['fotos'], true);

        $fotoPerfilDados = DB::table('foto')->where('fk_tabela', 'interno')->where('fk_id', $id)->where('tipo','principal')->get();
        if($fotoPerfilDados != collect([])){
            $fotoPerfilDados = json_decode($fotoPerfilDados, true)[0];
            $fotoPerfilDados['nome_arquivo'] =  'storage/app/arquivos/internos/fotos/'.$interno['id'].'/'.$fotoPerfilDados['nome_arquivo'];
            $fotoPerfilDados['created_at'] = Tdata::TdataBR(Tdata::getDataTimestamp($fotoPerfilDados['created_at']));
            $fotoPerfilDados['updated_at'] = Tdata::TdataBR(Tdata::getDataTimestamp($fotoPerfilDados['updated_at']));
        }
        else{
            $fotoPerfilDados['nome_arquivo'] = 'storage/app/semfoto.jpg';
        }

        // ------------------------- ADVOGADOS -----------------------------------------------------------------------------------

        $interno['advogados'] = DB::table('advogado_interno')->where('fk_interno', $id)->get();
        $interno['advogados'] = json_decode($interno['advogados'], true);
        foreach($interno['advogados'] as $key => $advogado){
            $adv = DB::table('advogado')->where('id', $advogado['fk_advogado'])->get();
            $adv = json_decode($adv, true)[0];
            $interno['advogados'][$key]['dadosAdvogado'] = $adv;
            $fotoAdv = DB::table('foto')->where('fk_tabela', 'advogado')->where('fk_id', $advogado['fk_advogado'])->where('tipo','principal')->get();
            if($fotoAdv != collect([])){
                $fotoAdv = json_decode($fotoAdv, true)[0];
                $interno['advogados'][$key]['dadosAdvogado']['fotoPrincipal'] = $fotoAdv;
                $interno['advogados'][$key]['dadosAdvogado']['fotoPrincipal']['nome_arquivo'] = 'storage/app/arquivos/advogados/fotos/'.$advogado['fk_advogado'].'/'.$fotoAdv['nome_arquivo'];

            }
            else {
                $interno['advogados'][$key]['dadosAdvogado']['fotoPrincipal']['nome_arquivo'] = 'storage/app/semfoto.jpg';
            }
        }

            // ------------------------- VISITANTES -----------------------------------------------------------------------------------

        $interno['visitas'] = DB::table('visita_interno')->where('fk_interno', $id)->get();
        $interno['visitas'] = json_decode($interno['visitas'], true);
        foreach($interno['visitas'] as $key => $visitanteInterno){
            $vis = DB::table('visita')->where('id', $visitanteInterno['fk_visita'])->get();
            $vis = json_decode($vis, true)[0];
            $interno['visitas'][$key]['dadosVisita'] = $vis;
            $fotoVis = DB::table('foto')->where('fk_tabela', 'visita')->where('fk_id', $visitanteInterno['fk_visita'])->where('tipo','principal')->get();
            if($fotoVis != collect([])){
                $fotoVis = json_decode($fotoVis, true)[0];
                $interno['visitas'][$key]['dadosVisita']['fotoPrincipal'] = $fotoVis;
                $interno['visitas'][$key]['dadosVisita']['fotoPrincipal']['nome_arquivo'] = 'storage/app/arquivos/visitas/fotos/'.$visitanteInterno['fk_visita'].'/'.$fotoVis['nome_arquivo'];
            }
            else {
                $interno['visitas'][$key]['dadosVisita']['fotoPrincipal']['nome_arquivo'] = 'storage/app/semfoto.jpg';
            }

            $interno['visitas'][$key]['statusSuspensao'] = DB::table('controle_acesso_visita_interno_suspenso')->where('fk_visita', $visitanteInterno['fk_visita'])->where('fk_interno',$interno['id'])->get();
            if($interno['visitas'][$key]['statusSuspensao'] != collect([])){
                $interno['visitas'][$key]['statusSuspensao'] = json_decode($interno['visitas'][$key]['statusSuspensao'], true)[0]['status_suspensao'];
                if($interno['visitas'][$key]['statusSuspensao'] == 'A'){
                    $interno['visitas'][$key]['statusSuspensao'] = "Visita Suspensa";
                } else if($interno['visitas'][$key]['statusSuspensao'] == 'I'){
                    $interno['visitas'][$key]['statusSuspensao'] = "Visita Autorizada";
                }
            } else{
                $interno['visitas'][$key]['statusSuspensao'] = "Visita Autorizada";
            }
            if($interno['visitas'][$key]['dadosVisita']['tipo'] == 'P'){
                $interno['visitas'][$key]['statusSuspensao'] = "Visita Proibida";
            }
        }

        // ------------------------- DOCUMENTOS -----------------------------------------------------------------------------------

        $documentos = DB::table('documento')
            ->leftJoin('log', function ($query) {
                $query->on('log.fk_id', '=', 'documento.id')
                    ->where('log.tipo_alteracao',"add")
                    ->where('log.tabela_alterada',"documento");
            })
            ->where('documento.fk_id',$id)
            ->where('documento.fk_tabela','interno')
            ->select("log.*", "log.created_at as logData","documento.*")
            ->get();

        foreach($documentos as $documento)
        {
            $documento->created_at = Tdata::TtimestampBR($documento->created_at);
            $documento->logData = Tdata::TtimestampBR($documento->logData);
            $documento->cpf_usuario = FacadeController::getNomeServidorCpf($documento->cpf_usuario)." (".$documento->cpf_usuario.")";
        }

        // ------------------------------------------------------------------------------------------------------------------------

        $i = 0;
        $keys = array_keys($interno);
        foreach($interno as $item){
            if(is_null($item) || $item == ""){
                $interno[$keys[$i]] = "Não Informado";
            }
            $i++;
        }


        // ------------------------- Historico -----------------------------------------------------------------------------------

        $dados = DB::table('movimentacao_interno')
            ->where('fk_interno', $id)
            ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.*','movimentacao_interno.*')
            ->orderBy('movimentacao.data', 'DESC');


        // ----------------------- ARQUIVOS DE HISTÓRICO DE TRANSFERÊNCIAS -----------------------------------
        $dadosTransferencias = DB::table('movimentacao_interno')
            ->where('fk_interno', $id)
            ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.*','movimentacao_interno.*')
            ->orderBy('movimentacao.data', 'DESC');

        $transferenciasHist = $dadosTransferencias->orderBy('movimentacao.data', 'DESC')->where('movimentacao.tipo', 'transferencia')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->get();

        $transferenciasHist = $transferenciasHist->unique('idmov');

        $transferenciasHist = json_decode($transferenciasHist, true);
        foreach ($transferenciasHist as $key => $transferenciaHist) {
            $transferenciasHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $transferenciaHist['idmov'])->get();
            $transferenciasHist[$key]['arquivos'] = json_decode($transferenciasHist[$key]['arquivos'], true);
        }

        // ----------------------- ARQUIVOS DE HISTÓRICO DE MUDANÇA DE CELA -----------------------------------
        $dadosMudanca = DB::table('movimentacao_interno')
            ->where('fk_interno', $id)
            ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.*','movimentacao_interno.*')
            ->orderBy('movimentacao.data', 'DESC');
        $mudancacelasHist = $dadosMudanca->orderBy('movimentacao.data', 'DESC')->where('movimentacao.tipo', 'mudancacela')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->get();

        $mudancacelasHist = $mudancacelasHist->unique('idmov');

        $mudancacelasHist = json_decode($mudancacelasHist, true);
        foreach ($mudancacelasHist as $key => $mudancacelaHist) {
            $mudancacelasHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $mudancacelaHist['idmov'])->get();
            $mudancacelasHist[$key]['arquivos'] = json_decode($mudancacelasHist[$key]['arquivos'], true);
        }


        // ----------------------- ARQUIVOS DE HISTÓRICO DE MUDANÇA DE SENTENCIADO OU PROVISÓRIO -----------------------------------
        $dadosMudancaSentenca = DB::table('movimentacao_interno')
            ->where('fk_interno', $id)
            ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.*','movimentacao_interno.*')
            ->orderBy('movimentacao.data', 'DESC');
        $mudancasentencaHist = $dadosMudancaSentenca->orderBy('movimentacao.data', 'DESC')
            ->where('movimentacao.tipo', 'mudancafechado')
            ->orWhere('movimentacao.tipo' , 'mudancaaberto')
            ->orWhere('movimentacao.tipo' , 'mudancasemiaberto')
            ->orWhere('movimentacao.tipo' , 'mudancaprovisorio')
            ->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->get();

        $mudancasentencaHist = $mudancasentencaHist->unique('idmov');

        $mudancasentencaHist = json_decode($mudancasentencaHist, true);
        foreach ($mudancasentencaHist as $key => $sentencaHist) {
            $mudancasentencaHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $sentencaHist['idmov'])->get();
            $mudancasentencaHist[$key]['arquivos'] = json_decode($mudancasentencaHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO ALVARÁ  -----
        $dadosAlvara = DB::table('movimentacao_interno')
            ->where('fk_interno', $id)
            ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.*','movimentacao_interno.*')
            ->orderBy('movimentacao.data', 'DESC');
        $alvarasHist = $dadosAlvara->orderBy('movimentacao.data', 'DESC')->where('movimentacao.tipo', 'alvara')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->get();

        $alvarasHist = $alvarasHist->unique('idmov');

        $alvarasHist = json_decode($alvarasHist, true);
        foreach ($alvarasHist as $key => $alvaraHist) {
            $alvarasHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $alvaraHist['idmov'])->get();
            $alvarasHist[$key]['arquivos'] = json_decode($alvarasHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO PROGRESSÃO ABERTO  -----
        $dadosProgressaoAberto = DB::table('movimentacao_interno')
            ->where('fk_interno', $id)
            ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.*','movimentacao_interno.*')
            ->orderBy('movimentacao.data', 'DESC');
        $abertosHist = $dadosProgressaoAberto->orderBy('movimentacao.data', 'DESC')->where('movimentacao.tipo', 'progressaoaberto')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->get();

        $abertosHist = $abertosHist->unique('idmov');

        $abertosHist = json_decode($abertosHist, true);
        foreach ($abertosHist as $key => $abertoHist) {
            $abertosHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $abertoHist['idmov'])->get();
            $abertosHist[$key]['arquivos'] = json_decode($abertosHist[$key]['arquivos'], true);
        }


        // ----- ARQUIVOS HISTÓRICO PROGRESSÃO ABERTO  -----
        $dadosRecambiamento = DB::table('movimentacao_interno')
            ->where('fk_interno', $id)
            ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.*','movimentacao_interno.*')
            ->orderBy('movimentacao.data', 'DESC');
        $recambiamentosHist = $dadosRecambiamento->orderBy('movimentacao.data', 'DESC')->where('movimentacao.tipo', 'recambiamento')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->get();

        $recambiamentosHist = $recambiamentosHist->unique('idmov');

        $recambiamentosHist = json_decode($recambiamentosHist, true);
        foreach ($recambiamentosHist as $key => $recambiamentoHist) {
            $recambiamentosHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $recambiamentoHist['idmov'])->get();
            $recambiamentosHist[$key]['arquivos'] = json_decode($recambiamentosHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO REGRESSÕES SEMIABERTO  -----
        $dadosRegressoesSemi = DB::table('movimentacao_interno')
            ->where('fk_interno', $id)
            ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.*','movimentacao_interno.*')
            ->orderBy('movimentacao.data', 'DESC');
        $regressaoesSemiHist = $dadosRegressoesSemi->orderBy('movimentacao.data', 'DESC')->where('movimentacao.tipo', 'regressaosemiaberto')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->get();

        $regressaoesSemiHist = $regressaoesSemiHist->unique('idmov');

        $regressaoesSemiHist = json_decode($regressaoesSemiHist, true);
        foreach ($regressaoesSemiHist as $key => $regressaoHist) {
            $regressaoesSemiHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $regressaoHist['idmov'])->get();
            $regressaoesSemiHist[$key]['arquivos'] = json_decode($regressaoesSemiHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO PROGRESSÃO SEMIABERTO  -----
        $dadosProgressoesSemi = DB::table('movimentacao_interno')
            ->where('fk_interno', $id)
            ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.*','movimentacao_interno.*')
            ->orderBy('movimentacao.data', 'DESC');
        $progressoesSemiHist = $dadosProgressoesSemi->orderBy('movimentacao.data', 'DESC')->where('movimentacao.tipo', 'progressaosemiaberto')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->get();

        $progressoesSemiHist = $progressoesSemiHist->unique('idmov');

        $progressoesSemiHist = json_decode($progressoesSemiHist, true);
        foreach ($progressoesSemiHist as $key => $progressaoHist) {
            $progressoesSemiHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $progressaoHist['idmov'])->get();
            $progressoesSemiHist[$key]['arquivos'] = json_decode($progressoesSemiHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS DA SAÍDA JUDICIAL ------
        $dadosSaidaJudicial = DB::table('movimentacao_interno')
            ->where('fk_interno', $id)
            ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.*','movimentacao_interno.*')
            ->orderBy('movimentacao.data', 'DESC');
        $saidaJudicialHist = $dadosSaidaJudicial->orderBy('movimentacao.data', 'DESC')->where('movimentacao.tipo', 'saidajudicial')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->get();

        $saidaJudicialHist = $saidaJudicialHist->unique('idmov');

        $saidaJudicialHist = json_decode($saidaJudicialHist, true);
        foreach ($saidaJudicialHist as $key => $judicialHist) {
            $saidaJudicialHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $judicialHist['idmov'])->get();
            $saidaJudicialHist[$key]['arquivos'] = json_decode($saidaJudicialHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO REGRESSÃO FECHADO  -----
        $dadosRegressao = DB::table('movimentacao_interno')
            ->where('fk_interno', $id)
            ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.*','movimentacao_interno.*')
            ->orderBy('movimentacao.data', 'DESC');
        $regressoesFechadoHist = $dadosRegressao->orderBy('movimentacao.data', 'DESC')->where('movimentacao.tipo', 'regressaofechado')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->get();

        $regressoesFechadoHist = $regressoesFechadoHist->unique('idmov');

        $regressoesFechadoHist = json_decode($regressoesFechadoHist, true);
        foreach ($regressoesFechadoHist as $key => $regressaoHist) {
            $regressoesFechadoHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $regressaoHist['idmov'])->get();
            $regressoesFechadoHist[$key]['arquivos'] = json_decode($regressoesFechadoHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO FUGAS  -----
        $dadosFugas = DB::table('movimentacao_interno')
            ->where('fk_interno', $id)
            ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.*','movimentacao_interno.*')
            ->orderBy('movimentacao.data', 'DESC');
        $fugasHist = $dadosFugas->orderBy('movimentacao.data', 'DESC')->where('movimentacao.tipo', 'fuga')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->get();

        $fugasHist = $fugasHist->unique('idmov');

        $fugasHist = json_decode($fugasHist, true);
        foreach ($fugasHist as $key => $fugaHist) {
            $fugasHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $fugaHist['idmov'])->get();
            $fugasHist[$key]['arquivos'] = json_decode($fugasHist[$key]['arquivos'], true);
        }


        // ----- ARQUIVOS HISTÓRICO ENTRADA NA UNIDADE  -----
        $dadosEntradaUnidade = DB::table('movimentacao_interno')
            ->where('fk_interno', $id)
            ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.*','movimentacao_interno.*')
            ->orderBy('movimentacao.data', 'DESC');
        $entradasUnidadeHist = $dadosEntradaUnidade->orderBy('movimentacao.data', 'DESC')
            ->where(function ($query) {
                $query->where('movimentacao.tipo', '=', 'entradaunidadeprisional')
                    ->orWhere('movimentacao.tipo', 'transferenciaentrada');
            })
            ->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->get();

        $entradasUnidadeHist = $entradasUnidadeHist->unique('idmov');

        $entradasUnidadeHist = json_decode($entradasUnidadeHist, true);
        foreach ($entradasUnidadeHist as $key => $entradaHist) {
            $entradasUnidadeHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $entradaHist['idmov'])->get();
            $entradasUnidadeHist[$key]['arquivos'] = json_decode($entradasUnidadeHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO PRISÕES DOMICILIARES  -----
        $dadosPrisaoDomiciliar = DB::table('movimentacao_interno')
            ->where('fk_interno', $id)
            ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.*','movimentacao_interno.*')
            ->orderBy('movimentacao.data', 'DESC');
        $prisoesdomiciliaresHist = $dadosPrisaoDomiciliar->orderBy('movimentacao.data', 'DESC')->where('movimentacao.tipo', 'prisaodomiciliar')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->get();

        $prisoesdomiciliaresHist = $prisoesdomiciliaresHist->unique('idmov');

        $prisoesdomiciliaresHist = json_decode($prisoesdomiciliaresHist, true);
        foreach ($prisoesdomiciliaresHist as $key => $prisaodomiciliarHist) {
            $prisoesdomiciliaresHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $prisaodomiciliarHist['idmov'])->get();
            $prisoesdomiciliaresHist[$key]['arquivos'] = json_decode($prisoesdomiciliaresHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO ÓBITOS  -----
        $dadosObito = DB::table('movimentacao_interno')
            ->where('fk_interno', $id)
            ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.*','movimentacao_interno.*')
            ->orderBy('movimentacao.data', 'DESC');
        $obitosHist = $dadosObito->orderBy('movimentacao.data', 'DESC')->where('movimentacao.tipo', 'obito')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->get();

        $obitosHist = $obitosHist->unique('idmov');

        $obitosHist = json_decode($obitosHist, true);
        foreach ($obitosHist as $key => $obitoHist) {
            $obitosHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $obitoHist['idmov'])->get();
            $obitosHist[$key]['arquivos'] = json_decode($obitosHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO LIVRAMENTOS CONDICIONAIS  -----
        $dadosLivramentoCondicional = DB::table('movimentacao_interno')
            ->where('fk_interno', $id)
            ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.*','movimentacao_interno.*')
            ->orderBy('movimentacao.data', 'DESC');
        $livramentoscondicionaisHist = $dadosLivramentoCondicional->orderBy('movimentacao.data', 'DESC')->where('movimentacao.tipo', 'livramentocondicional')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->get();

        $livramentoscondicionaisHist = $livramentoscondicionaisHist->unique('idmov');

        $livramentoscondicionaisHist = json_decode($livramentoscondicionaisHist, true);
        foreach ($livramentoscondicionaisHist as $key => $livramentocondicionalHist) {
            $livramentoscondicionaisHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $livramentocondicionalHist['idmov'])->get();
            $livramentoscondicionaisHist[$key]['arquivos'] = json_decode($livramentoscondicionaisHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO EXTINÇÕES DE PUNIBILIDADES  -----
        $dadosExtincaoPunibilidade = DB::table('movimentacao_interno')
            ->where('fk_interno', $id)
            ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.*','movimentacao_interno.*')
            ->orderBy('movimentacao.data', 'DESC');
        $extincoespunibilidadesHist = $dadosExtincaoPunibilidade->orderBy('movimentacao.data', 'DESC')->where('movimentacao.tipo', 'extincaopunibilidade')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->get();

        $extincoespunibilidadesHist = $extincoespunibilidadesHist->unique('idmov');

        $extincoespunibilidadesHist = json_decode($extincoespunibilidadesHist, true);
        foreach ($extincoespunibilidadesHist as $key => $extincaopunibilidadeHist) {
            $extincoespunibilidadesHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $extincaopunibilidadeHist['idmov'])->get();
            $extincoespunibilidadesHist[$key]['arquivos'] = json_decode($extincoespunibilidadesHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO EXTRADIÇÕES  -----
        $dadosExtradicao = DB::table('movimentacao_interno')
            ->where('fk_interno', $id)
            ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.*','movimentacao_interno.*')
            ->orderBy('movimentacao.data', 'DESC');
        $extradicoesHist = $dadosExtradicao->orderBy('movimentacao.data', 'DESC')->where('movimentacao.tipo', 'extradicao')->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')->get();

        $extradicoesHist = $extradicoesHist->unique('idmov');

        $extradicoesHist = json_decode($extradicoesHist, true);
        foreach ($extradicoesHist as $key => $extradicaoHist) {
            $extradicoesHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $extradicaoHist['idmov'])->get();
            $extradicoesHist[$key]['arquivos'] = json_decode($extradicoesHist[$key]['arquivos'], true);
        }


        $dados = $dados->get();

        //dd($transferenciasHist);

        if(!empty(json_decode($dados)))
        {
            $historicos = json_decode($dados, true);
//            dd($historicos);


            foreach($historicos as $key=>$historico){

                $dadosLog = DB::table('log')->where('tabela_alterada', 'movimentacao')->where('tipo_alteracao', 'add')->where('fk_id', $historico['fk_movimentacao'])->get();
                if($dadosLog == collect([])){
                    $dadosLog = DB::table('log')->where('tabela_alterada', 'interno')->where('tipo_alteracao', 'add')->where('fk_id', $historico['fk_interno'])->get();
                }
                if($historico['motivo'] == 'Registro da última data de entrada em unidade prisional que constava no banco de dados do '.config('app.name').' em 16/02/2019 para este interno, com a finalidade de possibilitar a emissão de atestados e certidões pós migração de banco de dados entre a antiga e nova versão do '.config('app.name').'. O horário da entrada (08h01) é meramente exemplificativo, porém a data está em conformidade com os registros do banco de dados anterior.'){
                    $dadosLog = collect([]);
                }
                $logHistoricoCancelamento = array();
                if($historico['tipo'] == 'transferencia' && $historico['status'] == 'Cancelada'){
                    $dadosLogC = DB::table('log')->where('tabela_alterada', 'movimentacao_interno')->where('tipo_alteracao', 'update')->where('fk_id', $historico['id'])->get();

                    foreach ($dadosLogC as $log) {
                        $nomeServidor = FacadeController::getNomeServidorCpf($log->cpf_usuario);

                        array_push($logHistoricoCancelamento, array(
                            'data' => Tdata::TdataBR(Tdata::getDataTimestamp($log->created_at)),
                            'hora' => Tdata::getTempoTimestamp($log->created_at),
                            'tabela' => str_replace("_", " ", $log->tabela_alterada),
                            'descricao' => $log->texto,
                            'realizadoPor' => utf8_encode($nomeServidor) . " (" . $log->cpf_usuario . ")",
                        ));
                    }
                }

                $logHistorico = array();
                foreach ($dadosLog as $log) {
                    $nomeServidor = FacadeController::getNomeServidorCpf($log->cpf_usuario);

                    array_push($logHistorico, array(
                        'data' => Tdata::TdataBR(Tdata::getDataTimestamp($log->created_at)),
                        'hora' => Tdata::getTempoTimestamp($log->created_at),
                        'tabela' => str_replace("_", " ", $log->tabela_alterada),
                        'descricao' => $log->texto,
                        'realizadoPor' => utf8_encode($nomeServidor) . " (" . $log->cpf_usuario . ")",
                    ));
                }


                if($historicos[$key]['data'] != null){
                    $historicos[$key]['mesData'] = Tdata::getMesExtAbrev(Tdata::getMesData(Tdata::getDataTimestamp($historicos[$key]['data'])));
                } else {
                    $historicos[$key]['mesData'] =  "Sem informação";
                }


                $historicos[$key]['nomeUnidade_destino'] = UnidadesController::getNomeUp($historicos[$key]['unidade_destino']);
                $historicos[$key]['nomePavilhao_destino'] = UnidadesController::getNomePav($historicos[$key]['pavilhao_destino']);
                $historicos[$key]['nomeAla_destino'] = UnidadesController::getNomeAla($historicos[$key]['ala_destino']);
                $historicos[$key]['nomeCela_destino'] = UnidadesController::getNomeCela($historicos[$key]['cela_destino']);
                $historicos[$key]['nomeUp_anterior'] = UnidadesController::getNomeUp($historicos[$key]['up_anterior']);
                $historicos[$key]['nomePv_anterior'] = UnidadesController::getNomePav($historicos[$key]['pv_anterior']);
                $historicos[$key]['nomeAla_anterior'] = UnidadesController::getNomeAla($historicos[$key]['ala_anterior']);
                $historicos[$key]['nomeCela_anterior'] = UnidadesController::getNomeCela($historicos[$key]['cela_anterior']);
                $historicos[$key]['log'] = $logHistorico;
                $historicos[$key]['logCancelamento'] = $logHistoricoCancelamento;

            }


        }else{
            $historicos = null;
        }


        $historicoAntigo = array();
        $historicoAntigoDados = DB::table('historico')->where('idinterno', $id)->orderBy('data_hora','DESC')->get();
        if(!empty(json_decode($historicoAntigoDados)))
        {

            foreach ($historicoAntigoDados as $historicoAntigoDado) {
                array_push($historicoAntigo, array(
                   'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($historicoAntigoDado->data_hora)),
                   'data' => Tdata::TdataBR($historicoAntigoDado->data_hora),
                   'descricao' => $historicoAntigoDado->descricao,
                   'motivoSaida' => $historicoAntigoDado->motivo_saida,
                   'unidadePrisional' => UnidadesController::getNomeUp($historicoAntigoDado->idup),
                ));
            }
        }

        //dd($historicoAntigo);

        // ------------------------- !Historico ----------------------------------------------------------------------------------


        // ------------------------- Visita historico -----------------------------------------------------------------------------------

        $visitanteHistoricoDados = DB::table('controle_acesso_visita_interno')->where('fk_interno', $id)->orderBy('data', 'DESC')->orderBy('horario', 'DESC')->limit(50)->get();

        //dd($visitanteHistoricoDados);

        $visitanteHistorico = array();

        foreach($visitanteHistoricoDados as $visitanteHistoricoDado)
        {
            $cpfVisita = VisitaController::getCpfVisita($visitanteHistoricoDado->fk_visita);

            $dadosSaida = DB::table('controle_acesso')->where('tabela', 'visita')->where('cpf', $cpfVisita)->where('data', $visitanteHistoricoDado->data)->where('horario', $visitanteHistoricoDado->horario)->get();

            //dd($dadosSaida[0]->horario_saida == null || $dadosSaida[0]->data_saida == null);
            $iterador = 0;
            $dadoSaidaHorario = array();
            $dadoSaidaData = array();
            foreach($dadosSaida as $dadoSaidaIt){
                if($iterador < 1){
                    $dadoSaidaHorario = $dadoSaidaIt->horario_saida;
                    $dadoSaidaData = $dadoSaidaIt->data_saida;
                    $iterador = $iterador + 1;
                }
            }


            if($dadoSaidaHorario == null ||$dadoSaidaData == null)
            {
                $duracao = "";
                $horaSaida = "";
                $dataSaida = "";
            }else{
                $duracao = ( strtotime($dadoSaidaHorario) - strtotime($visitanteHistoricoDado->horario) ) / 60 ."min";
                $horaSaida = $dadoSaidaHorario;
                $dataSaida =  $dadoSaidaData;
            }

            array_push($visitanteHistorico, array(
                'nomeVisita' => VisitaController::getNomeVisita($visitanteHistoricoDado->fk_visita),
                'fotoVisita' => VisitaController::getFotoVisita($visitanteHistoricoDado->fk_visita),
                'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($visitanteHistoricoDado->data)),
                'relacao' => InternoController::getRelacaoVisita($visitanteHistoricoDado->fk_interno, $visitanteHistoricoDado->fk_visita),
                'horaEntrada' => $visitanteHistoricoDado->horario,
                'dataEntrada' => Tdata::TdataBR($visitanteHistoricoDado->data),
                'horaSaida' => $horaSaida,
                'dataSaida' => Tdata::TdataBR($dataSaida),
                'duracao' => $duracao
            ));
        }
        
        //dd($visitanteHistorico);

        // ------------------------- !Visita historico ----------------------------------------------------------------------------------


        // ------------------------- Atendimento advogado historico ---------------------------------------------------------------------

        $advogadoHistoricoDados = DB::table('advogado_atendimento')->where('fk_interno', $id)->orderBy('data_atendimento', 'DESC')->orderBy('horaInicio', 'DESC')->limit(50)->get();

        $advogadoHistorico = array();

        foreach($advogadoHistoricoDados as $advogadoHistoricoDado)
        {
            $dadosAdv = AdvogadoController::getDadosAdv($advogadoHistoricoDado->fk_advogado);
            array_push($advogadoHistorico, array(
                'nomeAdv' => $dadosAdv[0]->nome,
                'fotoAdv' => asset(AdvogadoController::getFotoAdv($advogadoHistoricoDado->fk_advogado)),
                'data' => Tdata::TdataBR($advogadoHistoricoDado->data_atendimento),
                'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($advogadoHistoricoDado->data_atendimento)),
                'oab' => $dadosAdv[0]->oab_numero."/".$dadosAdv[0]->oab_uf,
                'horaInicio' => $advogadoHistoricoDado->horaInicio,
                'horaTermino' => $advogadoHistoricoDado->horaTermino,
                'duracao' => $advogadoHistoricoDado->duracao."min.",
            ));

        }

//        dd($advogadoHistorico);

        // ------------------------- !Atendimento advogado historico ----------------------------------------------------------------------------------

        // ------------------------- Disciplina -----------------------------------------------------------------------------------

        $dadosDisciplina = DB::table('sindicancia_interno')->leftJoin('sindicancia', 'sindicancia.id','=','sindicancia_interno.fk_sindicancia')->where("fk_interno", $id)->orderBy('sindicancia.data_incidente', 'DESC')->orderBy('fk_sindicancia', 'DESC')->get();
        $faltas = array();
        foreach ($dadosDisciplina as $dadosDiscip) {

            $sindicanciaDados = array();
            $dadosSindicancia = DB::table('sindicancia')->where('id', $dadosDiscip->fk_sindicancia)->get();
            foreach($dadosSindicancia as $dadosSind)
            {
                array_push($sindicanciaDados, array(
                   'id' => $dadosSind->id,
                   'unidade' => UnidadesController::getNomeUp($dadosSind->unidade),
                   'titulo' => $dadosSind->titulo,
                   'status' => $dadosSind->status,
                   'gravidadeFalta' => $dadosSind->gravidade_falta,
                   'dataIncidente' => $dadosSind->data_incidente,
                   'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($dadosSind->data_incidente)),
                ));
            }

            $agentesSindicancia = array();
            $dadosSindicanciaAgentes = DB::table('sindicancia_agentes')->where('fk_sindicancia', $dadosDiscip->fk_sindicancia)->get();
            if(count($dadosSindicanciaAgentes) > 0) {
                foreach ($dadosSindicanciaAgentes as $dadosSindicanciaAgente) {
                    array_push($agentesSindicancia, array(
                        'nomeAgente' => FacadeController::getNomeServidor($dadosSindicanciaAgente->matricula),
                        'matricula' => $dadosSindicanciaAgente->matricula
                    ));
                }
            }else{
                $agentesSindicancia = null;
            }

            $dadosSindicanciaArquivos = DB::table('sindicancia_arquivos')->where('fk_sindicancia', $dadosDiscip->fk_sindicancia)->get();
            $arquivosSindicancia = array();
            if(count($dadosSindicanciaArquivos) > 0)
            {
                foreach ($dadosSindicanciaArquivos as $dadosSindicanciaArquivo) {
                    array_push($arquivosSindicancia, array(
                        'idArquivo' => $dadosSindicanciaArquivo->id,
                        'nomeArquivo' => $dadosSindicanciaArquivo->nome_arquivo,
                    ));
                }
            }else{
                $arquivosSindicancia = null;
            }
            array_push($faltas, array(
                'dadosSindicancia' => $sindicanciaDados,
                'dadosSindicanciaAgentes' => $agentesSindicancia,
                'dadosSindicanciaArquivos' => $arquivosSindicancia,
            ));
        }

        //dd($faltas);

        // ------------------------- !Disciplina ----------------------------------------------------------------------------------

        // ------------------------- Escoltas -----------------------------------------------------------------------------------
        $escoltaDados = DB::table('escolta_internos')
            ->leftJoin('escolta', 'escolta.id','=', 'escolta_internos.fk_escolta')
            ->where('escolta_internos.fk_interno', $id)
            ->where('escolta.status', 'Finalizada')
            ->orderBy('escolta.data_saida', 'DESC')
            ->get();

        $escolta = array();
        foreach($escoltaDados as $escoltaDado)
        {
            $dadosEscoltas = DB::table('escolta')->where('id', $escoltaDado->fk_escolta)->get();
            $dadosEscoltaArray = array();
            if(count($dadosEscoltas) > 0){
                foreach($dadosEscoltas as $dadosEscolta)
                {
                    if($dadosEscolta->vtr_comandante != null)
                    {
                        $comandante = FacadeController::getNomeServidor($dadosEscolta->vtr_comandante)." (".$dadosEscolta->vtr_comandante.")";
                    }else{
                        $comandante = null;
                    }

                    if($dadosEscolta->vtr_motorista != null)
                    {
                        $motorista = FacadeController::getNomeServidor($dadosEscolta->vtr_motorista)." (".$dadosEscolta->vtr_motorista.")";
                    }else{
                        $motorista = null;
                    }

                    $internosEscoltaArray = array();

                        $internosEscolta = EscoltaController::getInternosEscolta($dadosEscolta->id, $id);

                        foreach($internosEscolta as $internoEsc)
                        {
                            array_push($internosEscoltaArray, array(
                                'nome' => self::getInternoNome($internoEsc->fk_interno),
                                'prontuario' => $internoEsc->fk_interno,
                            ));
                        }

                    array_push($dadosEscoltaArray, array(
                       'id' => $dadosEscolta->id,
                       'unidade' => UnidadesController::getNomeUp($dadosEscolta->unidade),
                       'tipo' => $dadosEscolta->tipo,
//                       'fkMovimentacao' => $dadosEscolta->fk_movimentacao,
                       'motivo' => $dadosEscolta->motivo,
                       'destino' => $dadosEscolta->destino,
                       'dataSaida' => Tdata::TdataBR($dadosEscolta->data_saida),
                       'horaSaida' => $dadosEscolta->hora_saida,
                       'dataRetorno' => Tdata::TdataBR($dadosEscolta->data_retorno),
                        'horaRetorno' => $dadosEscolta->hora_retorno,
                        'vtrPlaca' => $dadosEscolta->vtr_placa,
                       'vtrComandante' =>  $comandante,
                       'matComandante' =>  $dadosEscolta->vtr_comandante,
                       'vtrMotorista' => $motorista,
                       'internosEscolta' => $internosEscoltaArray,
                       'status' => $dadosEscolta->status,
                       'obs' => $dadosEscolta->obs,
                    ));
                }

            }else{

                $dadosEscoltaArray = null;

            }


            $dadosEscoltaAgentes = DB::table('escolta_agentes')->where('fk_escolta', $escoltaDado->fk_escolta)->get();
            $dadosEscoltaAgentesArray = array();
            if(count($dadosEscoltaAgentes) > 0)
            {
                foreach($dadosEscoltaAgentes as $dadosEscoltaAgente)
                {
                    if($dadosEscoltaArray[0]['matComandante'] != null && $dadosEscoltaAgente->matricula == $dadosEscoltaArray[0]['matComandante'])
                    {
                        array_unshift($dadosEscoltaAgentesArray, array(
                            'nomeAgente' => "[COMANDANTE DA ESCOLTA] ".FacadeController::getNomeServidor($dadosEscoltaAgente->matricula),
                            'matricula' => $dadosEscoltaAgente->matricula

                        ));

                    }else{

                        array_push($dadosEscoltaAgentesArray, array(
                            'nomeAgente' => FacadeController::getNomeServidor($dadosEscoltaAgente->matricula),
                            'matricula' => $dadosEscoltaAgente->matricula

                        ));

                    }
                }
            }else{
                $dadosEscoltaAgentesArray = null;
            }

            $dadosEscoltaArquivos = DB::table('escolta_arquivos')->where('fk_escolta', $dadosEscoltaArray[0]['id'])->get();
            $dadosEscoltaArquivosArray = array();
            if(count($dadosEscoltaArquivos) > 0)
            {
                foreach($dadosEscoltaArquivos as $arquivos)
                {
                    array_push($dadosEscoltaArquivosArray, array(
                        'idArquivo' => $arquivos->id,
                        'nomeArquivo' => $arquivos->nome_arquivo,
                    ));

                }
            }else{

                $dadosEscoltaArquivosArray = null;
            }

            $dadosLogEscolta = DB::table('log')->where('tabela_alterada', 'escolta')->where('tipo_alteracao', 'add')->where('fk_id', $dadosEscoltaArray[0]['id'])->get();
            $logEscolta = array();
                foreach ($dadosLogEscolta as $log) {
                    $nomeServidor = FacadeController::getNomeServidorCpf($log->cpf_usuario);

                    array_push($logEscolta, array(
                        'data' => Tdata::TdataBR(Tdata::getDataTimestamp($log->created_at)),
                        'hora' => Tdata::getTempoTimestamp($log->created_at),
                        'tabela' => str_replace("_", " ", $log->tabela_alterada),
                        'descricao' => $log->texto,
                        'realizadoPor' => $nomeServidor . " (" . $log->cpf_usuario . ")",
                    ));
                }


            array_push($escolta, array(
                'dadosEscolta' => $dadosEscoltaArray,
                'dadosAgentes' => $dadosEscoltaAgentesArray,
                'dadosArquivos' => $dadosEscoltaArquivosArray,
                'dadosLog' => $logEscolta,
                'dataEscolta' => Tdata::TdataMysql($dadosEscoltaArray[0]['dataSaida']),
            ));
        }

        $escoltaDadosGEP = DB::table('gep_internos')
            ->leftJoin('gep', 'gep.id','=', 'gep_internos.fk_gep')
            ->where('gep_internos.fk_interno', $id)
            ->where('gep.status', 'Finalizada')
            ->orderBy('gep.data_saida', 'DESC')
            ->get();

        foreach($escoltaDadosGEP as $escoltaDadoGEP)
        {
            $dadosEscoltasGEP = DB::table('gep')->where('id', $escoltaDadoGEP->fk_gep)->get();
            $dadosEscoltaArrayGEP = array();
            if(count($dadosEscoltasGEP) > 0){

                foreach($dadosEscoltasGEP as $dadosEscoltaGEP)
                {
                    if($dadosEscoltaGEP->vtr_comandante != null)
                    {
                        $comandanteGEP = FacadeController::getNomeServidor($dadosEscoltaGEP->vtr_comandante)." (".$dadosEscoltaGEP->vtr_comandante.")";
                    }else{
                        $comandanteGEP = null;
                    }

                    if($dadosEscoltaGEP->vtr_motorista != null)
                    {
                        $motoristaGEP = FacadeController::getNomeServidor($dadosEscoltaGEP->vtr_motorista)." (".$dadosEscoltaGEP->vtr_motorista.")";
                    }else{
                        $motoristaGEP = null;
                    }

                    $internosEscoltaArray = array();

                    $internosEscolta = GepController::getInternosEscolta($dadosEscoltaGEP->id,$id);

                    foreach($internosEscolta as $internoEsc)
                    {
                        array_push($internosEscoltaArray, array(
                            'nome' => self::getInternoNome($internoEsc->fk_interno),
                            'prontuario' => $internoEsc->fk_interno,
                        ));
                    }

                    $idEscolta = GepController::getEscolta($dadosEscoltaGEP->id, $id);

                    array_push($dadosEscoltaArrayGEP, array(
                       'id' => $dadosEscoltaGEP->id,
                       'unidade' => UnidadesController::getNomeUp(EscoltaController::getUnidadeEscolta($idEscolta)),
                       'tipo' => 'GEP',
//                       'fkMovimentacao' => EscoltaController::getMovimentacaoEscolta($idEscolta),
                       'motivo' => null,
                       'destino' => EscoltaController::getDestinoEscolta($idEscolta),
                       'dataSaida' => Tdata::TdataBR($dadosEscoltaGEP->data_saida),
                       'horaSaida' => $dadosEscoltaGEP->hora_saida,
                       'dataRetorno' => Tdata::TdataBR($dadosEscoltaGEP->data_retorno),
                        'horaRetorno' => $dadosEscoltaGEP->hora_retorno,
                        'vtrPlaca' => $dadosEscoltaGEP->vtr_placa,
                       'vtrComandante' =>  $comandanteGEP,
                       'matComandante' =>  $dadosEscoltaGEP->vtr_comandante,
                       'vtrMotorista' => $motoristaGEP,
                        'internosEscolta' => $internosEscoltaArray,
                        'status' => $dadosEscoltaGEP->status,
                       'obs' => null,
                    ));
                }

            }else{

                $dadosEscoltaArrayGEP = null;

            }

            $dadosEscoltaAgentesGEP = DB::table('gep_agentes')->where('fk_gep', $escoltaDadoGEP->fk_gep)->get();
            $dadosEscoltaAgentesArrayGEP = array();
            if(count($dadosEscoltaAgentesGEP) > 0)
            {
                foreach($dadosEscoltaAgentesGEP as $dadosEscoltaAgenteGEP)
                {
                    if($dadosEscoltaArrayGEP[0]['matComandante'] != null && $dadosEscoltaAgenteGEP->matricula == $dadosEscoltaArrayGEP[0]['matComandante'])
                    {
                        array_unshift($dadosEscoltaAgentesArrayGEP, array(
                            'nomeAgente' => "[COMANDANTE DA ESCOLTA] ".FacadeController::getNomeServidor($dadosEscoltaAgenteGEP->matricula),
                            'matricula' => $dadosEscoltaAgenteGEP->matricula

                        ));

                    }else{

                        array_push($dadosEscoltaAgentesArrayGEP, array(
                            'nomeAgente' => FacadeController::getNomeServidor($dadosEscoltaAgenteGEP->matricula),
                            'matricula' => $dadosEscoltaAgenteGEP->matricula

                        ));

                    }
                }
            }else{
                $dadosEscoltaAgentesArrayGEP = null;
            }


                $dadosEscoltaArquivosArrayGEP = null;

//            $dadosLogEscolta = DB::table('log')->where('tabela_alterada', 'escolta')->where('tipo_alteracao', 'add')->where('fk_id', $dadosEscoltaArray[0]['id'])->get();
//            $logEscolta = array();
//                foreach ($dadosLogEscolta as $log) {
//                    $nomeServidor = FacadeController::getNomeServidorCpf($log->cpf_usuario);
//
//                    array_push($logEscolta, array(
//                        'data' => Tdata::TdataBR(Tdata::getDataTimestamp($log->created_at)),
//                        'hora' => Tdata::getTempoTimestamp($log->created_at),
//                        'tabela' => str_replace("_", " ", $log->tabela_alterada),
//                        'descricao' => $log->texto,
//                        'realizadoPor' => $nomeServidor . " (" . $log->cpf_usuario . ")",
//                    ));
//                }


            array_push($escolta, array(
                'dadosEscolta' => $dadosEscoltaArrayGEP,
                'dadosAgentes' => $dadosEscoltaAgentesArrayGEP,
                'dadosArquivos' => $dadosEscoltaArquivosArrayGEP,
                'dadosLog' => null,
                'dataEscolta' => Tdata::TdataMysql($dadosEscoltaArrayGEP[0]['dataSaida']),
            ));
        }

        $escolta = collect($escolta)->sortBy('dataEscolta')->reverse()->toArray();
        //dd($escolta);
        // ------------------------- !Escoltas ----------------------------------------------------------------------------------

        // ------------------------- Saude e Assistencia ----------------------------------------------------------------------------------

        $saudeAssistDados = DB::table('saude_assistencia');
        $saudeAssistDados->leftJoin('unidade', 'unidade.id', '=', 'saude_assistencia.unidade');
        $saudeAssistDados->leftJoin('interno', 'interno.id', '=', 'saude_assistencia.fk_interno');
        $saudeAssistDados->where('saude_assistencia.fk_interno', $id);
        $saudeAssistDados->where('saude_assistencia.status', 'A');
        $saude = $saudeAssistDados->select('saude_assistencia.*', DB::raw("DATE_FORMAT(saude_assistencia.data_atendimento,'%d/%m/%Y') as data_atendimento"),'unidade.sigla AS unidadeNome','interno.nome AS nomeInterno','interno.id AS idInterno')->orderBy('saude_assistencia.data_atendimento', 'DESC')->orderBy('saude_assistencia.hora_atendimento', 'DESC')->get();


        $saudeAssist = array();

        foreach($saude as $item)
        {

            if(count(LogController::getDadosLog($item->id,"saude_assistencia","add")) != 0) {
                $dadosLog = LogController::getDadosLog($item->id, "saude_assistencia", "add");
                $userLog = UsuarioController::getNomeUser($dadosLog[0]->cpf_usuario);
                $textoLog = "Registrado por: " . $userLog . " (" . $dadosLog[0]->cpf_usuario . ") em " . Tdata::TtimestampBR($dadosLog[0]->created_at);
            }
            else{
                $textoLog = "Sem log registrado";
            }

            array_push($saudeAssist, array(
                'setor' => $item->setor,
                'dataAtendimento' => $item->data_atendimento,
                'horaAtendimento' => $item->hora_atendimento,
                'descricao' => $item->descricao,
                'unidadeNome' => $item->unidadeNome,
                'crm' => $item->crm_medico,
                'nomeMedico' => $item->nome_medico,
                'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData(Tdata::TdataMysql($item->data_atendimento))),
                'log' => $textoLog,

            ));
        }


        // ------------------------- !Saude e Assistencia ---------------------------------------------------------------------------------

        // ------------------------- Companheiros de cela do interno -----------------------------------------------------------------------

        /*$dadosInternos = self::getDadosInterno($id);*/

        /*if($dadosInternos[0]->status == "A") {

            $companheirosCelaDados = DB::table('interno')->where('cela', $dadosInternos[0]->cela)->where('id', '!=', $id)->where('status', 'A')->get();

            $companheirosCela = array();

            foreach ($companheirosCelaDados as $companheirosCelaDado) {
                $diasCela = self::getDiasCela($companheirosCelaDado->id, $companheirosCelaDado->cela);

                if ($companheirosCelaDado->lideranca == 'N') {
                    $lideranca = "Não";
                } else {
                    $lideranca = "Sim";
                }

                $outrosNomes = DB::table('outrosnomes')->where('pk_id',$companheirosCelaDado->id)->where('pk_tabela','interno')->get();
                $outrosNomes = json_decode($outrosNomes,true);

                array_push($companheirosCela, array(
                    'id' => $companheirosCelaDado->id,
                    'nome' => $companheirosCelaDado->nome,
                    'outrosnomes' => $outrosNomes,
                    'faccao' => self::getNomeFaccao($companheirosCelaDado->faccao),
                    'lideranca' => $lideranca,
                    'periculosidade' => $companheirosCelaDado->periculosidade,
                    'diasCela' => $diasCela,
                    'cela' => $companheirosCelaDado->cela,
                ));

            }
        }else{
            $companheirosCela = null;
        }*/

        //dd($companheirosCela);


        $ocorrencias = DB::table('ocorrencia_envolvidos')
            ->join('ocorrencia','ocorrencia.id','=','ocorrencia_envolvidos.fk_ocorrencia')
            ->where('ocorrencia_envolvidos.fk_tabela','interno')
            ->where('ocorrencia_envolvidos.fk_id_envolvido',$id)
            ->select('ocorrencia.id','ocorrencia.natureza','ocorrencia.data','ocorrencia.horario','ocorrencia.descricao')
            ->orderBy('ocorrencia.data','DESC')
            ->get();
        $ocorrencias = json_decode($ocorrencias, true);
        foreach($ocorrencias as $key => $ocorrencia){
            $ocorrencias[$key]['mesData'] = Tdata::getMesExtAbrev(Tdata::getMesData($ocorrencias[$key]['data']));
            $ocorrencias[$key]['arquivos'] = DB::table('ocorrencia_arquivos')->where('fk_ocorrencia', $ocorrencia['id'])->get();
            $ocorrencias[$key]['arquivos'] = json_decode($ocorrencias[$key]['arquivos'], true);
            $ocorrencias[$key]['log'] = DB::table('log')->where('tabela_alterada','ocorrencia')->where('tipo_alteracao','add')->where('fk_id',$ocorrencia['id'])->select('log.cpf_usuario','log.created_at')->get();
            $ocorrencias[$key]['log'] = json_decode($ocorrencias[$key]['log'], true);
            if($ocorrencias[$key]['log'] != []){
                $ocorrencias[$key]['log'][0]['realizadoPor'] = utf8_encode(FacadeController::getNomeServidorCpf($ocorrencias[$key]['log'][0]['cpf_usuario'])) . " (" . $ocorrencias[$key]['log'][0]['cpf_usuario'] . ")";
                $ocorrencias[$key]['log'][0]['data'] = Tdata::getDataBrTimestamp($ocorrencias[$key]['log'][0]['created_at']);
                $ocorrencias[$key]['log'][0]['hora'] = Tdata::getTempoTimestamp($ocorrencias[$key]['log'][0]['created_at']);
            }

            $ocorrencias[$key]['natureza'] = DB::table('ocorrencia_natureza')->where('ocorrencia_natureza.id',$ocorrencias[$key]['natureza'])->select('ocorrencia_natureza.natureza')->get();
            $ocorrencias[$key]['natureza'] = json_decode($ocorrencias[$key]['natureza'], true)[0]['natureza'];

//            // internos envolvidos
            $ocorrencias[$key]['internosEnvolvidos'] = DB::table('ocorrencia_envolvidos')->where('fk_tabela','interno')->where('fk_ocorrencia',$ocorrencias[$key]['id'])->select('fk_id_envolvido')->get();
            $ocorrencias[$key]['internosEnvolvidos'] = json_decode($ocorrencias[$key]['internosEnvolvidos'], true);

//            // agentes envolvidos
            $ocorrencias[$key]['servidoresEnvolvidos'] = DB::table('ocorrencia_envolvidos')->where('fk_tabela','servidor')->where('fk_ocorrencia',$ocorrencias[$key]['id'])->select('fk_id_envolvido')->get();
            $ocorrencias[$key]['servidoresEnvolvidos'] = json_decode($ocorrencias[$key]['servidoresEnvolvidos'],true);

//            // advogados envolvidos
            $ocorrencias[$key]['advogadosEnvolvidos'] = DB::table('ocorrencia_envolvidos')->where('fk_tabela','advogado')->where('fk_ocorrencia',$ocorrencias[$key]['id'])->select('fk_id_envolvido')->get();
            $ocorrencias[$key]['advogadosEnvolvidos'] = json_decode($ocorrencias[$key]['advogadosEnvolvidos'], true);

//            // visitas envolvidas
            $ocorrencias[$key]['visitasEnvolvidas'] = DB::table('ocorrencia_envolvidos')->where('fk_tabela','visita')->where('fk_ocorrencia',$ocorrencias[$key]['id'])->select('fk_id_envolvido')->get();
            $ocorrencias[$key]['visitasEnvolvidas'] = json_decode($ocorrencias[$key]['visitasEnvolvidas'], true);
        }


        // ------------------------- !Companheiros de cela do interno ----------------------------------------------------------------------

        $request->session()->remove('lista'); //Limpa esses request para não ficar pesada a requisição

        $request->session()->put('interno',$interno);
        $request->session()->put('fotoPerfilDados', $fotoPerfilDados);
        $request->session()->put('historicos', $historicos);
        $request->session()->put('historicoAntigo', $historicoAntigo);
        $request->session()->put('documentos', $documentos);
        $request->session()->put('advogadoHistorico', $advogadoHistorico);
        $request->session()->put('faltas', $faltas);
        //$request->session()->put('companheirosCela', $companheirosCela);
        $request->session()->put('escoltas', $escolta);
        $request->session()->put('saudeAssist', $saudeAssist);
        $request->session()->put('visitanteHistorico', $visitanteHistorico);



        // --- mandado de prisão em aberto (aguardando a chave de acesso e verificar se os parâmetros estão corretos) ---
       /*
       $xmlstring = file_get_contents('https://wwwh.cnj.jus.br/MPWS/mandadoPrisaoService?wsdl',
            [
                'headers' => [
                    'Content-Type'=>'text/xml; charset=ISO-8859-1'
                ],
                'body' => [
                    '<?xml version="1.0" encoding="ISO-8859-1"?>
                        <serviceRequest serviceName="mandadoPrisaoService.pesquisarMandado">
                            <requestBody>
                                <chaveAcesso></chaveAcesso>
                                <tipoSituacaoMandado>AGUARDANDO_CUMPRIMENTO</tipoSituacaoMandado>
                            </requestBody>
                        </serviceRequest>'
                ]
            ]
        );
        $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        dd($json,$array);
       */

       
        return view('interno.show.show', compact('interno','fotoPerfilDados','historicos','historicoAntigo','documentos','visitanteHistorico','id','advogadoHistorico','faltas',
            'escolta','saudeAssist','transferenciasHist','mudancacelasHist', 'alvarasHist', 'abertosHist', 'recambiamentosHist', 'regressaoesSemiHist', 'mudancasentencaHist' , 'progressoesSemiHist',
            'regressoesFechadoHist', 'fugasHist', 'obitosHist', 'prisoesdomiciliaresHist', 'extincoespunibilidadesHist', 'livramentoscondicionaisHist', 'entradasUnidadeHist', 'saidaJudicialHist', 'extradicoesHist',
            'ocorrencias'));
    }

    public function showDadosPessoais($id)
    {
        
        $interno = DB::table('interno')->where('id', $id)->select('interno.*',DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'interno.medida_seguranca')->get();
        
        if(count($interno) == 0)
        {
            return redirect()->route('inicio')->withInput()->with('alert-danger', 'Interno não encontrado!');

        }

        $interno = json_decode($interno, true)[0];

        // dados pessoais
        $outrosnomes = DB::table('outrosnomes')->where('pk_tabela', 'interno')->where('pk_id', $id)->get();
        $interno['outrosnomes'] = [];
        if(!is_null($outrosnomes)){
            foreach(json_decode($outrosnomes,true) as $i){
                array_push($interno['outrosnomes'], $i['nome']);
            }
        }
        $enderecos = DB::table('endereco')->where('fk_tabela', 'interno')->where('fk_id', $id)->get();
        $interno['enderecos'] = [];
        if(!is_null($enderecos)){
            foreach(json_decode($enderecos,true) as $i){
                array_push($interno['enderecos'], $i);
            }
        }
        
        if(!is_null($interno['rg_data'])) $interno['rg_data'] = Tdata::TdataBR($interno['rg_data']);
        if(!is_null($interno['data_nascimento'])) $interno['data_nascimento'] = Tdata::TdataBR($interno['data_nascimento']);
        if(!is_null($interno['data_nascimento'])) $interno['idade'] = Tdata::getIdade($interno['data_nascimento']);
        else $interno['idade'] = "";
        if(!is_null($interno['created_at'])) $interno['created_at'] = "às ".Tdata::getTempoTimestamp($interno['created_at'])." em ".Tdata::TdataBR(Tdata::getDataTimestamp($interno['created_at']));
        else $interno['created_at'] = "";
        if(!is_null($interno['updated_at'])) $interno['updated_at'] = "às ".Tdata::getTempoTimestamp($interno['updated_at'])." em ".Tdata::TdataBR(Tdata::getDataTimestamp($interno['updated_at']));
        else $interno['updated_at'] = "";

        if(!is_null($interno['profissao'])) $interno['profissao_nome'] = InternoController::getNomeProfissao($interno['profissao']);
        else $interno['profissao_nome'] = "";

        if(!is_null($interno['unidade_prisional'])) $interno['unidade_prisional_nome'] = UnidadesController::getNomeUp($interno['unidade_prisional']);
        else $interno['unidade_prisional_nome'] = "";
        if(!is_null($interno['pavilhao'])) $interno['pavilhao_nome'] = UnidadesController::getNomePav($interno['pavilhao']);
        else $interno['pavilhao_nome'] = "";
        if(!is_null($interno['ala'])) $interno['ala_nome'] = UnidadesController::getNomeAla($interno['ala']);
        else $interno['ala_nome'] = "";
        if(!is_null($interno['cela'])) $interno['cela_nome'] = UnidadesController::getNomeCela($interno['cela']);
        else $interno['cela_nome'] = "";
        if(!is_null($interno['condicao_interno'])) $interno['condicao_nome'] = InternoController::getNomeCondicao($interno['condicao_interno']);
        else $interno['condicao_nome'] = "";
        if(!is_null($interno['faccao'])) $interno['faccao_nome'] = InternoController::getNomeFaccao($interno['faccao']);
        else $interno['faccao_nome'] = "";

                // -------------------------- FOTOS -----------------------------------------------------------------------------------

        $fotoPerfilDados = self::getFotoPrincipalInternoBase64($id);
        
        return view('interno.show.dadosSeparados.dadosPessoais', compact('interno','id','fotoPerfilDados'));
    }
    
    public function showFotos($id) {
        
        
        $interno = DB::table('interno')->where('id', $id)->select('interno.*', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'interno.medida_seguranca')->get();

        if (count($interno) == 0) {
            return redirect()->route('inicio')->withInput()->with('alert-danger', 'Interno não encontrado!');
        }

        $interno = json_decode($interno, true)[0];


        if (!is_null($interno['rg_data']))
            $interno['rg_data'] = Tdata::TdataBR($interno['rg_data']);
        if (!is_null($interno['data_nascimento']))
            $interno['data_nascimento'] = Tdata::TdataBR($interno['data_nascimento']);
        if (!is_null($interno['data_nascimento']))
            $interno['idade'] = Tdata::getIdade($interno['data_nascimento']);
        else
            $interno['idade'] = "";
        if (!is_null($interno['created_at']))
            $interno['created_at'] = "às " . Tdata::getTempoTimestamp($interno['created_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['created_at']));
        else
            $interno['created_at'] = "";
        if (!is_null($interno['updated_at']))
            $interno['updated_at'] = "às " . Tdata::getTempoTimestamp($interno['updated_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['updated_at']));
        else
            $interno['updated_at'] = "";

        if (!is_null($interno['profissao']))
            $interno['profissao_nome'] = InternoController::getNomeProfissao($interno['profissao']);
        else
            $interno['profissao_nome'] = "";

        if (!is_null($interno['unidade_prisional']))
            $interno['unidade_prisional_nome'] = UnidadesController::getNomeUp($interno['unidade_prisional']);
        else
            $interno['unidade_prisional_nome'] = "";
        if (!is_null($interno['pavilhao']))
            $interno['pavilhao_nome'] = UnidadesController::getNomePav($interno['pavilhao']);
        else
            $interno['pavilhao_nome'] = "";
        if (!is_null($interno['ala']))
            $interno['ala_nome'] = UnidadesController::getNomeAla($interno['ala']);
        else
            $interno['ala_nome'] = "";
        if (!is_null($interno['cela']))
            $interno['cela_nome'] = UnidadesController::getNomeCela($interno['cela']);
        else
            $interno['cela_nome'] = "";
        if (!is_null($interno['condicao_interno']))
            $interno['condicao_nome'] = InternoController::getNomeCondicao($interno['condicao_interno']);
        else
            $interno['condicao_nome'] = "";
        if (!is_null($interno['faccao']))
            $interno['faccao_nome'] = InternoController::getNomeFaccao($interno['faccao']);
        else
            $interno['faccao_nome'] = "";

        // -------------------------- FOTOS -----------------------------------------------------------------------------------

        $fotos = array();
        $interno['fotos'] = DB::table('foto')->where('fk_tabela', 'interno')->where('fk_id', $id)->select('foto.*', DB::raw("DATE_FORMAT(created_at,'%d/%m/%Y %H:%i:%s') as created_at"), DB::raw("DATE_FORMAT(updated_at,'%d/%m/%Y %H:%i:%s') as updated_at"))->get();
        if ($interno['fotos'] != collect([]))
        {
            //
            
            foreach($interno['fotos'] as $k => $foto)
            {
                $imagem = 'app/arquivos/internos/fotos/' . $id . '/' . $foto->nome_arquivo;
                $imgBase64 = self::imagensInternoMergeCpfUser($id, storage_path($imagem));
                array_push($fotos, array(
                    "id" => $foto->id,
                    "fk_id" => $foto->fk_id,
                    "fk_tabela" => $foto->fk_tabela,
                    "nome_arquivo" => $foto->nome_arquivo,
                    "imagem" => $imgBase64,
                    "legenda" => $foto->legenda,
                    "tipo" => $foto->tipo,
                    "created_at" => $foto->created_at,
                    "updated_at" => $foto->updated_at,
                ));
            }
            
            $interno['fotos'] = json_decode($interno['fotos'], true);
        }
            
        
        
        
        $fotoPerfilDados = self::getFotoPrincipalInternoBase64($id);

        return view('interno.show.dadosSeparados.fotos', compact('interno', 'id', 'fotoPerfilDados','fotos'));
    }
    
    public function showHistorico($id) {
        
        $interno = DB::table('interno')->where('id', $id)->select('interno.*', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'interno.medida_seguranca')->get();

        if (count($interno) == 0) {
            return redirect()->route('inicio')->withInput()->with('alert-danger', 'Interno não encontrado!');
        }

        $interno = json_decode($interno, true)[0];


        if (!is_null($interno['rg_data']))
            $interno['rg_data'] = Tdata::TdataBR($interno['rg_data']);
        if (!is_null($interno['data_nascimento']))
            $interno['data_nascimento'] = Tdata::TdataBR($interno['data_nascimento']);
        if (!is_null($interno['data_nascimento']))
            $interno['idade'] = Tdata::getIdade($interno['data_nascimento']);
        else
            $interno['idade'] = "";
        if (!is_null($interno['created_at']))
            $interno['created_at'] = "às " . Tdata::getTempoTimestamp($interno['created_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['created_at']));
        else
            $interno['created_at'] = "";
        if (!is_null($interno['updated_at']))
            $interno['updated_at'] = "às " . Tdata::getTempoTimestamp($interno['updated_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['updated_at']));
        else
            $interno['updated_at'] = "";

        if (!is_null($interno['profissao']))
            $interno['profissao_nome'] = InternoController::getNomeProfissao($interno['profissao']);
        else
            $interno['profissao_nome'] = "";

        if (!is_null($interno['unidade_prisional']))
            $interno['unidade_prisional_nome'] = UnidadesController::getNomeUp($interno['unidade_prisional']);
        else
            $interno['unidade_prisional_nome'] = "";
        if (!is_null($interno['pavilhao']))
            $interno['pavilhao_nome'] = UnidadesController::getNomePav($interno['pavilhao']);
        else
            $interno['pavilhao_nome'] = "";
        if (!is_null($interno['ala']))
            $interno['ala_nome'] = UnidadesController::getNomeAla($interno['ala']);
        else
            $interno['ala_nome'] = "";
        if (!is_null($interno['cela']))
            $interno['cela_nome'] = UnidadesController::getNomeCela($interno['cela']);
        else
            $interno['cela_nome'] = "";
        if (!is_null($interno['condicao_interno']))
            $interno['condicao_nome'] = InternoController::getNomeCondicao($interno['condicao_interno']);
        else
            $interno['condicao_nome'] = "";
        if (!is_null($interno['faccao']))
            $interno['faccao_nome'] = InternoController::getNomeFaccao($interno['faccao']);
        else
            $interno['faccao_nome'] = "";

        // -------------------------- FOTOS -----------------------------------------------------------------------------------

        $fotoPerfilDados = self::getFotoPrincipalInternoBase64($id);

        
                // ------------------------- Historico -----------------------------------------------------------------------------------

        $dados = DB::table('movimentacao_interno')
            ->where('fk_interno', $id)
            ->where('status', 'Finalizada')
            ->leftJoin('movimentacao', 'movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.*','movimentacao_interno.*','movimentacao.id as idmov')
            ->orderBy('movimentacao.data', 'DESC')
            ->orderBy('movimentacao.id', 'DESC');
        
        $dados = $dados->get();
        $allMovInterno =  json_decode($dados, true);



        // ----------------------- ARQUIVOS DE HISTÓRICO DE TRANSFERÊNCIAS -----------------------------------
        
        /**
         * refatoracao
         */
        $filtroMovimentacao = array("transferencia");
        $transferenciasHist = array_filter($allMovInterno, function ($var) use ($filtroMovimentacao) {
            return in_array($var['tipo'], $filtroMovimentacao);
        });
       
        foreach ($transferenciasHist as $key => $transferenciaHist) {
            $transferenciasHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $transferenciaHist['idmov'])->get();
            $transferenciasHist[$key]['arquivos'] = json_decode($transferenciasHist[$key]['arquivos'], true);
        }

        // ----------------------- ARQUIVOS DE HISTÓRICO DE MUDANÇA DE CELA -----------------------------------
        
        $filtroMovimentacao = array("mudancacela");
        $mudancacelasHist = array_filter($allMovInterno, function ($var) use ($filtroMovimentacao) {
            return in_array($var['tipo'], $filtroMovimentacao);
        });

        foreach ($mudancacelasHist as $key => $mudancacelaHist) {
            $mudancacelasHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $mudancacelaHist['idmov'])->get();
            $mudancacelasHist[$key]['arquivos'] = json_decode($mudancacelasHist[$key]['arquivos'], true);
        }


        // ----------------------- ARQUIVOS DE HISTÓRICO DE MUDANÇA DE SENTENCIADO OU PROVISÓRIO -----------------------------------
        
        $filtroMovimentacao = array("mudancafechado","mudancaaberto","mudancasemiaberto","mudancaprovisorio");
        $mudancasentencaHist = array_filter($allMovInterno, function ($var) use ($filtroMovimentacao) {
            return in_array($var['tipo'], $filtroMovimentacao);
        });

       
        foreach ($mudancasentencaHist as $key => $sentencaHist) {
            $mudancasentencaHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $sentencaHist['idmov'])->get();
            $mudancasentencaHist[$key]['arquivos'] = json_decode($mudancasentencaHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO ALVARÁ  -----
        
        $filtroMovimentacao = array("alvara");
        $alvarasHist = array_filter($allMovInterno, function ($var) use ($filtroMovimentacao) {
            return in_array($var['tipo'], $filtroMovimentacao);
        });
        
        
        foreach ($alvarasHist as $key => $alvaraHist) {
            $alvarasHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $alvaraHist['idmov'])->get();
            $alvarasHist[$key]['arquivos'] = json_decode($alvarasHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO PROGRESSÃO ABERTO  -----
        
        $filtroMovimentacao = array("progressaoaberto");
        $abertosHist = array_filter($allMovInterno, function ($var) use ($filtroMovimentacao) {
            return in_array($var['tipo'], $filtroMovimentacao);
        });
                
        foreach ($abertosHist as $key => $abertoHist) {
            $abertosHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $abertoHist['idmov'])->get();
            $abertosHist[$key]['arquivos'] = json_decode($abertosHist[$key]['arquivos'], true);
        }


        // ----- ARQUIVOS HISTÓRICO PROGRESSÃO ABERTO  -----
        
        $filtroMovimentacao = array("recambiamento");
        $recambiamentosHist = array_filter($allMovInterno, function ($var) use ($filtroMovimentacao) {
            return in_array($var['tipo'], $filtroMovimentacao);
        });
               
        foreach ($recambiamentosHist as $key => $recambiamentoHist) {
            $recambiamentosHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $recambiamentoHist['idmov'])->get();
            $recambiamentosHist[$key]['arquivos'] = json_decode($recambiamentosHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO REGRESSÕES SEMIABERTO  -----
        
        $filtroMovimentacao = array("regressaosemiaberto");
        $regressaoesSemiHist = array_filter($allMovInterno, function ($var) use ($filtroMovimentacao) {
            return in_array($var['tipo'], $filtroMovimentacao);
        });

        foreach ($regressaoesSemiHist as $key => $regressaoHist) {
            $regressaoesSemiHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $regressaoHist['idmov'])->get();
            $regressaoesSemiHist[$key]['arquivos'] = json_decode($regressaoesSemiHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO PROGRESSÃO SEMIABERTO  -----
        
        $filtroMovimentacao = array("progressaosemiaberto");
        $progressoesSemiHist = array_filter($allMovInterno, function ($var) use ($filtroMovimentacao) {
            return in_array($var['tipo'], $filtroMovimentacao);
        });
        
        foreach ($progressoesSemiHist as $key => $progressaoHist) {
            $progressoesSemiHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $progressaoHist['idmov'])->get();
            $progressoesSemiHist[$key]['arquivos'] = json_decode($progressoesSemiHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS DA SAÍDA JUDICIAL ------
        
        $filtroMovimentacao = array("saidajudicial");
        $saidaJudicialHist = array_filter($allMovInterno, function ($var) use ($filtroMovimentacao) {
            return in_array($var['tipo'], $filtroMovimentacao);
        });
        
        foreach ($saidaJudicialHist as $key => $judicialHist) {
            $saidaJudicialHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $judicialHist['idmov'])->get();
            $saidaJudicialHist[$key]['arquivos'] = json_decode($saidaJudicialHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO REGRESSÃO FECHADO  -----
        
        $filtroMovimentacao = array("regressaofechado");
        $regressoesFechadoHist = array_filter($allMovInterno, function ($var) use ($filtroMovimentacao) {
            return in_array($var['tipo'], $filtroMovimentacao);
        });
        
        foreach ($regressoesFechadoHist as $key => $regressaoHist) {
            $regressoesFechadoHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $regressaoHist['idmov'])->get();
            $regressoesFechadoHist[$key]['arquivos'] = json_decode($regressoesFechadoHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO FUGAS  -----
        
        $filtroMovimentacao = array("fuga");
        $fugasHist = array_filter($allMovInterno, function ($var) use ($filtroMovimentacao) {
            return in_array($var['tipo'], $filtroMovimentacao);
        });
        
        foreach ($fugasHist as $key => $fugaHist) {
            $fugasHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $fugaHist['idmov'])->get();
            $fugasHist[$key]['arquivos'] = json_decode($fugasHist[$key]['arquivos'], true);
        }


        // ----- ARQUIVOS HISTÓRICO ENTRADA NA UNIDADE  -----
        
        $filtroMovimentacao = array("entradaunidadeprisional","transferenciaentrada");
        $entradasUnidadeHist = array_filter($allMovInterno, function ($var) use ($filtroMovimentacao) {
            return in_array($var['tipo'], $filtroMovimentacao);
        });
        
        foreach ($entradasUnidadeHist as $key => $entradaHist) {
            $entradasUnidadeHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $entradaHist['idmov'])->get();
            $entradasUnidadeHist[$key]['arquivos'] = json_decode($entradasUnidadeHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO PRISÕES DOMICILIARES  -----
        
        $filtroMovimentacao = array("prisaodomiciliar");
        $prisoesdomiciliaresHist = array_filter($allMovInterno, function ($var) use ($filtroMovimentacao) {
            return in_array($var['tipo'], $filtroMovimentacao);
        });
        
        foreach ($prisoesdomiciliaresHist as $key => $prisaodomiciliarHist) {
            $prisoesdomiciliaresHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $prisaodomiciliarHist['idmov'])->get();
            $prisoesdomiciliaresHist[$key]['arquivos'] = json_decode($prisoesdomiciliaresHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO ÓBITOS  -----
        
        $filtroMovimentacao = array("obito");
        $obitosHist = array_filter($allMovInterno, function ($var) use ($filtroMovimentacao) {
            return in_array($var['tipo'], $filtroMovimentacao);
        });
        
        foreach ($obitosHist as $key => $obitoHist) {
            $obitosHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $obitoHist['idmov'])->get();
            $obitosHist[$key]['arquivos'] = json_decode($obitosHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO LIVRAMENTOS CONDICIONAIS  -----
        
        $filtroMovimentacao = array("livramentocondicional");
        $livramentoscondicionaisHist = array_filter($allMovInterno, function ($var) use ($filtroMovimentacao) {
            return in_array($var['tipo'], $filtroMovimentacao);
        });
        
        foreach ($livramentoscondicionaisHist as $key => $livramentocondicionalHist) {
            $livramentoscondicionaisHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $livramentocondicionalHist['idmov'])->get();
            $livramentoscondicionaisHist[$key]['arquivos'] = json_decode($livramentoscondicionaisHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO EXTINÇÕES DE PUNIBILIDADES  -----
        
        $filtroMovimentacao = array("extincaopunibilidade");
        $extincoespunibilidadesHist = array_filter($allMovInterno, function ($var) use ($filtroMovimentacao) {
            return in_array($var['tipo'], $filtroMovimentacao);
        });

        foreach ($extincoespunibilidadesHist as $key => $extincaopunibilidadeHist) {
            $extincoespunibilidadesHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $extincaopunibilidadeHist['idmov'])->get();
            $extincoespunibilidadesHist[$key]['arquivos'] = json_decode($extincoespunibilidadesHist[$key]['arquivos'], true);
        }

        // ----- ARQUIVOS HISTÓRICO EXTRADIÇÕES  -----
        
        $filtroMovimentacao = array("extradicao");
        $extradicoesHist = array_filter($allMovInterno, function ($var) use ($filtroMovimentacao) {
            return in_array($var['tipo'], $filtroMovimentacao);
        });

        foreach ($extradicoesHist as $key => $extradicaoHist) {
            $extradicoesHist[$key]['arquivos'] = DB::table('movimentacao_arquivos')->where('fk_movimentacao', $extradicaoHist['idmov'])->get();
            $extradicoesHist[$key]['arquivos'] = json_decode($extradicoesHist[$key]['arquivos'], true);
        }


        //dd($transferenciasHist);

        if(!empty(json_decode($dados)))
        {
            $historicos = json_decode($dados, true);
//            dd($historicos);


            foreach($historicos as $key=>$historico){

                $dadosLog = DB::table('log')->where('tabela_alterada', 'movimentacao')->where('tipo_alteracao', 'add')->where('fk_id', $historico['fk_movimentacao'])->get();
                if($dadosLog == collect([])){
                    $dadosLog = DB::table('log')->where('tabela_alterada', 'interno')->where('tipo_alteracao', 'add')->where('fk_id', $historico['fk_interno'])->get();
                }
                if($historico['motivo'] == 'Registro da última data de entrada em unidade prisional que constava no banco de dados do '.config('app.name').' em 16/02/2019 para este interno, com a finalidade de possibilitar a emissão de atestados e certidões pós migração de banco de dados entre a antiga e nova versão do '.config('app.name').'. O horário da entrada (08h01) é meramente exemplificativo, porém a data está em conformidade com os registros do banco de dados anterior.'){
                    $dadosLog = collect([]);
                }
                $logHistoricoCancelamento = array();
                if($historico['tipo'] == 'transferencia' && $historico['status'] == 'Cancelada'){
                    $dadosLogC = DB::table('log')->where('tabela_alterada', 'movimentacao_interno')->where('tipo_alteracao', 'update')->where('fk_id', $historico['id'])->get();

                    foreach ($dadosLogC as $log) {
                        $nomeServidor = FacadeController::getNomeServidorCpf($log->cpf_usuario);

                        array_push($logHistoricoCancelamento, array(
                            'data' => Tdata::TdataBR(Tdata::getDataTimestamp($log->created_at)),
                            'hora' => Tdata::getTempoTimestamp($log->created_at),
                            'tabela' => str_replace("_", " ", $log->tabela_alterada),
                            'descricao' => $log->texto,
                            'realizadoPor' => utf8_encode($nomeServidor) . " (" . $log->cpf_usuario . ")",
                        ));
                    }
                }

                $logHistorico = array();
                foreach ($dadosLog as $log) {
                    $nomeServidor = FacadeController::getNomeServidorCpf($log->cpf_usuario);

                    array_push($logHistorico, array(
                        'data' => Tdata::TdataBR(Tdata::getDataTimestamp($log->created_at)),
                        'hora' => Tdata::getTempoTimestamp($log->created_at),
                        'tabela' => str_replace("_", " ", $log->tabela_alterada),
                        'descricao' => $log->texto,
                        'realizadoPor' => utf8_encode($nomeServidor) . " (" . $log->cpf_usuario . ")",
                    ));
                }


                if($historicos[$key]['data'] != null){
                    $historicos[$key]['mesData'] = Tdata::getMesExtAbrev(Tdata::getMesData(Tdata::getDataTimestamp($historicos[$key]['data'])));
                } else {
                    $historicos[$key]['mesData'] =  "Sem informação";
                }


                $historicos[$key]['nomeUnidade_destino'] = UnidadesController::getNomeUp($historicos[$key]['unidade_destino']);
                $historicos[$key]['nomePavilhao_destino'] = UnidadesController::getNomePav($historicos[$key]['pavilhao_destino']);
                $historicos[$key]['nomeAla_destino'] = UnidadesController::getNomeAla($historicos[$key]['ala_destino']);
                $historicos[$key]['nomeCela_destino'] = UnidadesController::getNomeCela($historicos[$key]['cela_destino']);
                $historicos[$key]['nomeUp_anterior'] = UnidadesController::getNomeUp($historicos[$key]['up_anterior']);
                $historicos[$key]['nomePv_anterior'] = UnidadesController::getNomePav($historicos[$key]['pv_anterior']);
                $historicos[$key]['nomeAla_anterior'] = UnidadesController::getNomeAla($historicos[$key]['ala_anterior']);
                $historicos[$key]['nomeCela_anterior'] = UnidadesController::getNomeCela($historicos[$key]['cela_anterior']);
                $historicos[$key]['log'] = $logHistorico;
                $historicos[$key]['logCancelamento'] = $logHistoricoCancelamento;

            }


        }else{
            $historicos = null;
        }


        $historicoAntigo = array();
        $historicoAntigoDados = DB::table('historico')->where('idinterno', $id)->orderBy('data_hora','DESC')->get();
        if(!empty(json_decode($historicoAntigoDados)))
        {

            foreach ($historicoAntigoDados as $historicoAntigoDado) {
                array_push($historicoAntigo, array(
                   'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($historicoAntigoDado->data_hora)),
                   'data' => Tdata::TdataBR($historicoAntigoDado->data_hora),
                   'descricao' => $historicoAntigoDado->descricao,
                   'motivoSaida' => $historicoAntigoDado->motivo_saida,
                   'unidadePrisional' => UnidadesController::getNomeUp($historicoAntigoDado->idup),
                ));
            }
        }


        // ------------------------- !Historico ----------------------------------------------------------------------------------

        return view('interno.show.dadosSeparados.historico', compact('interno', 'id', 'fotoPerfilDados', 'historicos','historicoAntigo','transferenciasHist','mudancacelasHist','alvarasHist','abertosHist','recambiamentosHist','regressaoesSemiHist','mudancasentencaHist','progressoesSemiHist','regressoesFechadoHist','fugasHist','obitosHist','prisoesdomiciliaresHist','extincoespunibilidadesHist','livramentoscondicionaisHist','entradasUnidadeHist','saidaJudicialHist', 'extradicoesHist'));
    }

    public function showVisitantes($id) {
                $interno = DB::table('interno')->where('id', $id)->select('interno.*', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'interno.medida_seguranca')->get();

        if (count($interno) == 0) {
            return redirect()->route('inicio')->withInput()->with('alert-danger', 'Interno não encontrado!');
        }

        $interno = json_decode($interno, true)[0];


        if (!is_null($interno['rg_data']))
            $interno['rg_data'] = Tdata::TdataBR($interno['rg_data']);
        if (!is_null($interno['data_nascimento']))
            $interno['data_nascimento'] = Tdata::TdataBR($interno['data_nascimento']);
        if (!is_null($interno['data_nascimento']))
            $interno['idade'] = Tdata::getIdade($interno['data_nascimento']);
        else
            $interno['idade'] = "";
        if (!is_null($interno['created_at']))
            $interno['created_at'] = "às " . Tdata::getTempoTimestamp($interno['created_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['created_at']));
        else
            $interno['created_at'] = "";
        if (!is_null($interno['updated_at']))
            $interno['updated_at'] = "às " . Tdata::getTempoTimestamp($interno['updated_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['updated_at']));
        else
            $interno['updated_at'] = "";

        if (!is_null($interno['profissao']))
            $interno['profissao_nome'] = InternoController::getNomeProfissao($interno['profissao']);
        else
            $interno['profissao_nome'] = "";

        if (!is_null($interno['unidade_prisional']))
            $interno['unidade_prisional_nome'] = UnidadesController::getNomeUp($interno['unidade_prisional']);
        else
            $interno['unidade_prisional_nome'] = "";
        if (!is_null($interno['pavilhao']))
            $interno['pavilhao_nome'] = UnidadesController::getNomePav($interno['pavilhao']);
        else
            $interno['pavilhao_nome'] = "";
        if (!is_null($interno['ala']))
            $interno['ala_nome'] = UnidadesController::getNomeAla($interno['ala']);
        else
            $interno['ala_nome'] = "";
        if (!is_null($interno['cela']))
            $interno['cela_nome'] = UnidadesController::getNomeCela($interno['cela']);
        else
            $interno['cela_nome'] = "";
        if (!is_null($interno['condicao_interno']))
            $interno['condicao_nome'] = InternoController::getNomeCondicao($interno['condicao_interno']);
        else
            $interno['condicao_nome'] = "";
        if (!is_null($interno['faccao']))
            $interno['faccao_nome'] = InternoController::getNomeFaccao($interno['faccao']);
        else
            $interno['faccao_nome'] = "";

        // -------------------------- FOTOS -----------------------------------------------------------------------------------

        $fotoPerfilDados = self::getFotoPrincipalInternoBase64($id);

                    // ------------------------- VISITANTES -----------------------------------------------------------------------------------

        $interno['visitas'] = DB::table('visita_interno')->where('fk_interno', $id)->get();
        $interno['visitas'] = json_decode($interno['visitas'], true);
        foreach($interno['visitas'] as $key => $visitanteInterno){
            $vis = DB::table('visita')->where('id', $visitanteInterno['fk_visita'])->get();
            $vis = json_decode($vis, true)[0];
            $interno['visitas'][$key]['dadosVisita'] = $vis;
            $fotoVis = DB::table('foto')->where('fk_tabela', 'visita')->where('fk_id', $visitanteInterno['fk_visita'])->where('tipo','principal')->get();
            if($fotoVis != collect([])){
                $fotoVis = json_decode($fotoVis, true)[0];
                $interno['visitas'][$key]['dadosVisita']['fotoPrincipal'] = $fotoVis;
                $interno['visitas'][$key]['dadosVisita']['fotoPrincipal']['nome_arquivo'] = 'storage/app/arquivos/visitas/fotos/'.$visitanteInterno['fk_visita'].'/'.$fotoVis['nome_arquivo'];
            }
            else {
                $interno['visitas'][$key]['dadosVisita']['fotoPrincipal']['nome_arquivo'] = 'storage/app/semfoto.jpg';
            }

            $interno['visitas'][$key]['statusSuspensao'] = DB::table('controle_acesso_visita_interno_suspenso')->where('fk_visita', $visitanteInterno['fk_visita'])->where('fk_interno',$interno['id'])->get();
            if($interno['visitas'][$key]['statusSuspensao'] != collect([])){
                $interno['visitas'][$key]['statusSuspensao'] = json_decode($interno['visitas'][$key]['statusSuspensao'], true)[0]['status_suspensao'];
                if($interno['visitas'][$key]['statusSuspensao'] == 'A'){
                    $interno['visitas'][$key]['statusSuspensao'] = "Visita Suspensa";
                } else if($interno['visitas'][$key]['statusSuspensao'] == 'I'){
                    $interno['visitas'][$key]['statusSuspensao'] = "Visita Autorizada";
                }
            } else{
                $interno['visitas'][$key]['statusSuspensao'] = "Visita Autorizada";
            }
            if($interno['visitas'][$key]['dadosVisita']['tipo'] == 'P'){
                $interno['visitas'][$key]['statusSuspensao'] = "Visita Proibida";
            }
        }
        
                // ------------------------- Visita historico -----------------------------------------------------------------------------------

        $visitanteHistoricoDados = DB::table('controle_acesso_visita_interno')->where('fk_interno', $id)->orderBy('data', 'DESC')->orderBy('horario', 'DESC')->limit(50)->get();


        $visitanteHistorico = array();

        foreach($visitanteHistoricoDados as $visitanteHistoricoDado)
        {
            $cpfVisita = VisitaController::getCpfVisita($visitanteHistoricoDado->fk_visita);

            $dadosSaida = DB::table('controle_acesso')->where('tabela', 'visita')->where('cpf', $cpfVisita)->where('data', $visitanteHistoricoDado->data)->where('horario', $visitanteHistoricoDado->horario)->get();

            $iterador = 0;
            $dadoSaidaHorario = array();
            $dadoSaidaData = array();
            foreach($dadosSaida as $dadoSaidaIt){
                if($iterador < 1){
                    $dadoSaidaHorario = $dadoSaidaIt->horario_saida;
                    $dadoSaidaData = $dadoSaidaIt->data_saida;
                    $iterador = $iterador + 1;
                }
            }


            if($dadoSaidaHorario == null ||$dadoSaidaData == null)
            {
                $duracao = "";
                $horaSaida = "";
                $dataSaida = "";
            }else{
                $duracao = ( strtotime($dadoSaidaHorario) - strtotime($visitanteHistoricoDado->horario) ) / 60 ."min";
                $horaSaida = $dadoSaidaHorario;
                $dataSaida =  $dadoSaidaData;
            }

            array_push($visitanteHistorico, array(
                'nomeVisita' => VisitaController::getNomeVisita($visitanteHistoricoDado->fk_visita),
                'fotoVisita' => VisitaController::getFotoVisita($visitanteHistoricoDado->fk_visita),
                'unidade' => UnidadesController::getNomeUp($visitanteHistoricoDado->unidade),
                'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($visitanteHistoricoDado->data)),
                'relacao' => InternoController::getRelacaoVisita($visitanteHistoricoDado->fk_interno, $visitanteHistoricoDado->fk_visita),
                'horaEntrada' => $visitanteHistoricoDado->horario,
                'dataEntrada' => Tdata::TdataBR($visitanteHistoricoDado->data),
                'horaSaida' => $horaSaida,
                'dataSaida' => Tdata::TdataBR($dataSaida),
                'duracao' => $duracao
            ));
        }
        

        // ------------------------- !Visita historico ----------------------------------------------------------------------------------


        return view('interno.show.dadosSeparados.visitantes', compact('interno', 'id', 'fotoPerfilDados','visitanteHistorico'));

    }
    
    public function showAdvogados($id) {
        
        $interno = DB::table('interno')->where('id', $id)->select('interno.*', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'interno.medida_seguranca')->get();

        if (count($interno) == 0) {
            return redirect()->route('inicio')->withInput()->with('alert-danger', 'Interno não encontrado!');
        }

        $interno = json_decode($interno, true)[0];


        if (!is_null($interno['rg_data']))
            $interno['rg_data'] = Tdata::TdataBR($interno['rg_data']);
        if (!is_null($interno['data_nascimento']))
            $interno['data_nascimento'] = Tdata::TdataBR($interno['data_nascimento']);
        if (!is_null($interno['data_nascimento']))
            $interno['idade'] = Tdata::getIdade($interno['data_nascimento']);
        else
            $interno['idade'] = "";
        if (!is_null($interno['created_at']))
            $interno['created_at'] = "às " . Tdata::getTempoTimestamp($interno['created_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['created_at']));
        else
            $interno['created_at'] = "";
        if (!is_null($interno['updated_at']))
            $interno['updated_at'] = "às " . Tdata::getTempoTimestamp($interno['updated_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['updated_at']));
        else
            $interno['updated_at'] = "";

        if (!is_null($interno['profissao']))
            $interno['profissao_nome'] = InternoController::getNomeProfissao($interno['profissao']);
        else
            $interno['profissao_nome'] = "";

        if (!is_null($interno['unidade_prisional']))
            $interno['unidade_prisional_nome'] = UnidadesController::getNomeUp($interno['unidade_prisional']);
        else
            $interno['unidade_prisional_nome'] = "";
        if (!is_null($interno['pavilhao']))
            $interno['pavilhao_nome'] = UnidadesController::getNomePav($interno['pavilhao']);
        else
            $interno['pavilhao_nome'] = "";
        if (!is_null($interno['ala']))
            $interno['ala_nome'] = UnidadesController::getNomeAla($interno['ala']);
        else
            $interno['ala_nome'] = "";
        if (!is_null($interno['cela']))
            $interno['cela_nome'] = UnidadesController::getNomeCela($interno['cela']);
        else
            $interno['cela_nome'] = "";
        if (!is_null($interno['condicao_interno']))
            $interno['condicao_nome'] = InternoController::getNomeCondicao($interno['condicao_interno']);
        else
            $interno['condicao_nome'] = "";
        if (!is_null($interno['faccao']))
            $interno['faccao_nome'] = InternoController::getNomeFaccao($interno['faccao']);
        else
            $interno['faccao_nome'] = "";

        // -------------------------- FOTOS -----------------------------------------------------------------------------------

        $fotoPerfilDados = self::getFotoPrincipalInternoBase64($id);


        // ------------------------- ADVOGADOS -----------------------------------------------------------------------------------

        $interno['advogados'] = DB::table('advogado_interno')->where('fk_interno', $id)->get();
        $interno['advogados'] = json_decode($interno['advogados'], true);
        foreach($interno['advogados'] as $key => $advogado){
            $adv = DB::table('advogado')->where('id', $advogado['fk_advogado'])->get();
            $adv = json_decode($adv, true)[0];
            $interno['advogados'][$key]['dadosAdvogado'] = $adv;
            $fotoAdv = DB::table('foto')->where('fk_tabela', 'advogado')->where('fk_id', $advogado['fk_advogado'])->where('tipo','principal')->get();
            if($fotoAdv != collect([])){
                $fotoAdv = json_decode($fotoAdv, true)[0];
                $interno['advogados'][$key]['dadosAdvogado']['fotoPrincipal'] = $fotoAdv;
                $interno['advogados'][$key]['dadosAdvogado']['fotoPrincipal']['nome_arquivo'] = 'storage/app/arquivos/advogados/fotos/'.$advogado['fk_advogado'].'/'.$fotoAdv['nome_arquivo'];

            }
            else {
                $interno['advogados'][$key]['dadosAdvogado']['fotoPrincipal']['nome_arquivo'] = 'storage/app/semfoto.jpg';
            }
        }

        
        // ------------------------- Atendimento advogado historico ---------------------------------------------------------------------

        $advogadoHistoricoDados = DB::table('advogado_atendimento')->where('fk_interno', $id)->orderBy('data_atendimento', 'DESC')->orderBy('horaInicio', 'DESC')->limit(50)->get();

        $advogadoHistorico = array();

        foreach($advogadoHistoricoDados as $advogadoHistoricoDado)
        {
            $dadosAdv = AdvogadoController::getDadosAdv($advogadoHistoricoDado->fk_advogado);
            array_push($advogadoHistorico, array(
                'nomeAdv' => $dadosAdv[0]->nome,
                'fotoAdv' => asset(AdvogadoController::getFotoAdv($advogadoHistoricoDado->fk_advogado)),
                'data' => Tdata::TdataBR($advogadoHistoricoDado->data_atendimento),
                'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($advogadoHistoricoDado->data_atendimento)),
                'oab' => $dadosAdv[0]->oab_numero."/".$dadosAdv[0]->oab_uf,
                'horaInicio' => $advogadoHistoricoDado->horaInicio,
                'horaTermino' => $advogadoHistoricoDado->horaTermino,
                'duracao' => $advogadoHistoricoDado->duracao."min.",
            ));

        }


        // ------------------------- !Atendimento advogado historico ----------------------------------------------------------------------------------

        return view('interno.show.dadosSeparados.advogados', compact('interno', 'id', 'fotoPerfilDados','advogadoHistorico'));

    }

    public function showDisciplina($id) {
        
                $interno = DB::table('interno')->where('id', $id)->select('interno.*', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'interno.medida_seguranca')->get();

        if (count($interno) == 0) {
            return redirect()->route('inicio')->withInput()->with('alert-danger', 'Interno não encontrado!');
        }

        $interno = json_decode($interno, true)[0];


        if (!is_null($interno['rg_data']))
            $interno['rg_data'] = Tdata::TdataBR($interno['rg_data']);
        if (!is_null($interno['data_nascimento']))
            $interno['data_nascimento'] = Tdata::TdataBR($interno['data_nascimento']);
        if (!is_null($interno['data_nascimento']))
            $interno['idade'] = Tdata::getIdade($interno['data_nascimento']);
        else
            $interno['idade'] = "";
        if (!is_null($interno['created_at']))
            $interno['created_at'] = "às " . Tdata::getTempoTimestamp($interno['created_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['created_at']));
        else
            $interno['created_at'] = "";
        if (!is_null($interno['updated_at']))
            $interno['updated_at'] = "às " . Tdata::getTempoTimestamp($interno['updated_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['updated_at']));
        else
            $interno['updated_at'] = "";

        if (!is_null($interno['profissao']))
            $interno['profissao_nome'] = InternoController::getNomeProfissao($interno['profissao']);
        else
            $interno['profissao_nome'] = "";

        if (!is_null($interno['unidade_prisional']))
            $interno['unidade_prisional_nome'] = UnidadesController::getNomeUp($interno['unidade_prisional']);
        else
            $interno['unidade_prisional_nome'] = "";
        if (!is_null($interno['pavilhao']))
            $interno['pavilhao_nome'] = UnidadesController::getNomePav($interno['pavilhao']);
        else
            $interno['pavilhao_nome'] = "";
        if (!is_null($interno['ala']))
            $interno['ala_nome'] = UnidadesController::getNomeAla($interno['ala']);
        else
            $interno['ala_nome'] = "";
        if (!is_null($interno['cela']))
            $interno['cela_nome'] = UnidadesController::getNomeCela($interno['cela']);
        else
            $interno['cela_nome'] = "";
        if (!is_null($interno['condicao_interno']))
            $interno['condicao_nome'] = InternoController::getNomeCondicao($interno['condicao_interno']);
        else
            $interno['condicao_nome'] = "";
        if (!is_null($interno['faccao']))
            $interno['faccao_nome'] = InternoController::getNomeFaccao($interno['faccao']);
        else
            $interno['faccao_nome'] = "";

        // -------------------------- FOTOS -----------------------------------------------------------------------------------

        $fotoPerfilDados = self::getFotoPrincipalInternoBase64($id);

        
                // ------------------------- Disciplina -----------------------------------------------------------------------------------

        $dadosDisciplina = DB::table('sindicancia_interno')->leftJoin('sindicancia', 'sindicancia.id','=','sindicancia_interno.fk_sindicancia')->where("fk_interno", $id)->orderBy('sindicancia.data_incidente', 'DESC')->orderBy('fk_sindicancia', 'DESC')->get();
        $faltas = array();
        foreach ($dadosDisciplina as $dadosDiscip) {

            $sindicanciaDados = array();
            $dadosSindicancia = DB::table('sindicancia')->where('id', $dadosDiscip->fk_sindicancia)->get();
            foreach($dadosSindicancia as $dadosSind)
            {
                array_push($sindicanciaDados, array(
                   'id' => $dadosSind->id,
                   'unidade' => UnidadesController::getNomeUp($dadosSind->unidade),
                   'titulo' => $dadosSind->titulo,
                   'status' => $dadosSind->status,
                   'gravidadeFalta' => $dadosSind->gravidade_falta,
                   'dataIncidente' => $dadosSind->data_incidente,
                   'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($dadosSind->data_incidente)),
                ));
            }

            $agentesSindicancia = array();
            $dadosSindicanciaAgentes = DB::table('sindicancia_agentes')->where('fk_sindicancia', $dadosDiscip->fk_sindicancia)->get();
            if(count($dadosSindicanciaAgentes) > 0) {
                foreach ($dadosSindicanciaAgentes as $dadosSindicanciaAgente) {
                    array_push($agentesSindicancia, array(
                        'nomeAgente' => FacadeController::getNomeServidor($dadosSindicanciaAgente->matricula),
                        'matricula' => $dadosSindicanciaAgente->matricula
                    ));
                }
            }else{
                $agentesSindicancia = null;
            }

            $dadosSindicanciaArquivos = DB::table('sindicancia_arquivos')->where('fk_sindicancia', $dadosDiscip->fk_sindicancia)->get();
            $arquivosSindicancia = array();
            if(count($dadosSindicanciaArquivos) > 0)
            {
                foreach ($dadosSindicanciaArquivos as $dadosSindicanciaArquivo) {
                    array_push($arquivosSindicancia, array(
                        'idArquivo' => $dadosSindicanciaArquivo->id,
                        'nomeArquivo' => $dadosSindicanciaArquivo->nome_arquivo,
                    ));
                }
            }else{
                $arquivosSindicancia = null;
            }
            array_push($faltas, array(
                'dadosSindicancia' => $sindicanciaDados,
                'dadosSindicanciaAgentes' => $agentesSindicancia,
                'dadosSindicanciaArquivos' => $arquivosSindicancia,
            ));
        }


        // ------------------------- !Disciplina ----------------------------------------------------------------------------------

        return view('interno.show.dadosSeparados.disciplina', compact('interno', 'id', 'fotoPerfilDados','faltas'));

    }
    
    public function showOcorrencia($id) {
        
        $interno = DB::table('interno')->where('id', $id)->select('interno.*', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'interno.medida_seguranca')->get();

        if (count($interno) == 0) {
            return redirect()->route('inicio')->withInput()->with('alert-danger', 'Interno não encontrado!');
        }

        $interno = json_decode($interno, true)[0];


        if (!is_null($interno['rg_data']))
            $interno['rg_data'] = Tdata::TdataBR($interno['rg_data']);
        if (!is_null($interno['data_nascimento']))
            $interno['data_nascimento'] = Tdata::TdataBR($interno['data_nascimento']);
        if (!is_null($interno['data_nascimento']))
            $interno['idade'] = Tdata::getIdade($interno['data_nascimento']);
        else
            $interno['idade'] = "";
        if (!is_null($interno['created_at']))
            $interno['created_at'] = "às " . Tdata::getTempoTimestamp($interno['created_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['created_at']));
        else
            $interno['created_at'] = "";
        if (!is_null($interno['updated_at']))
            $interno['updated_at'] = "às " . Tdata::getTempoTimestamp($interno['updated_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['updated_at']));
        else
            $interno['updated_at'] = "";

        if (!is_null($interno['profissao']))
            $interno['profissao_nome'] = InternoController::getNomeProfissao($interno['profissao']);
        else
            $interno['profissao_nome'] = "";

        if (!is_null($interno['unidade_prisional']))
            $interno['unidade_prisional_nome'] = UnidadesController::getNomeUp($interno['unidade_prisional']);
        else
            $interno['unidade_prisional_nome'] = "";
        if (!is_null($interno['pavilhao']))
            $interno['pavilhao_nome'] = UnidadesController::getNomePav($interno['pavilhao']);
        else
            $interno['pavilhao_nome'] = "";
        if (!is_null($interno['ala']))
            $interno['ala_nome'] = UnidadesController::getNomeAla($interno['ala']);
        else
            $interno['ala_nome'] = "";
        if (!is_null($interno['cela']))
            $interno['cela_nome'] = UnidadesController::getNomeCela($interno['cela']);
        else
            $interno['cela_nome'] = "";
        if (!is_null($interno['condicao_interno']))
            $interno['condicao_nome'] = InternoController::getNomeCondicao($interno['condicao_interno']);
        else
            $interno['condicao_nome'] = "";
        if (!is_null($interno['faccao']))
            $interno['faccao_nome'] = InternoController::getNomeFaccao($interno['faccao']);
        else
            $interno['faccao_nome'] = "";

        // -------------------------- FOTOS -----------------------------------------------------------------------------------

        $fotoPerfilDados = self::getFotoPrincipalInternoBase64($id);

                $ocorrencias = DB::table('ocorrencia_envolvidos')
            ->join('ocorrencia','ocorrencia.id','=','ocorrencia_envolvidos.fk_ocorrencia')
            ->where('ocorrencia_envolvidos.fk_tabela','interno')
            ->where('ocorrencia_envolvidos.fk_id_envolvido',$id)
            ->select('ocorrencia.id','ocorrencia.natureza','ocorrencia.data','ocorrencia.horario','ocorrencia.descricao')
            ->orderBy('ocorrencia.data','DESC')
            ->get();
        $ocorrencias = json_decode($ocorrencias, true);
        foreach($ocorrencias as $key => $ocorrencia){
            $ocorrencias[$key]['mesData'] = Tdata::getMesExtAbrev(Tdata::getMesData($ocorrencias[$key]['data']));
            $ocorrencias[$key]['arquivos'] = DB::table('ocorrencia_arquivos')->where('fk_ocorrencia', $ocorrencia['id'])->get();
            $ocorrencias[$key]['arquivos'] = json_decode($ocorrencias[$key]['arquivos'], true);
            $ocorrencias[$key]['log'] = DB::table('log')->where('tabela_alterada','ocorrencia')->where('tipo_alteracao','add')->where('fk_id',$ocorrencia['id'])->select('log.cpf_usuario','log.created_at')->get();
            $ocorrencias[$key]['log'] = json_decode($ocorrencias[$key]['log'], true);
            if($ocorrencias[$key]['log'] != []){
                $ocorrencias[$key]['log'][0]['realizadoPor'] = utf8_encode(FacadeController::getNomeServidorCpf($ocorrencias[$key]['log'][0]['cpf_usuario'])) . " (" . $ocorrencias[$key]['log'][0]['cpf_usuario'] . ")";
                $ocorrencias[$key]['log'][0]['data'] = Tdata::getDataBrTimestamp($ocorrencias[$key]['log'][0]['created_at']);
                $ocorrencias[$key]['log'][0]['hora'] = Tdata::getTempoTimestamp($ocorrencias[$key]['log'][0]['created_at']);
            }

            $ocorrencias[$key]['natureza'] = DB::table('ocorrencia_natureza')->where('ocorrencia_natureza.id',$ocorrencias[$key]['natureza'])->select('ocorrencia_natureza.natureza')->get();
            $ocorrencias[$key]['natureza'] = json_decode($ocorrencias[$key]['natureza'], true)[0]['natureza'];

//            // internos envolvidos
            $ocorrencias[$key]['internosEnvolvidos'] = DB::table('ocorrencia_envolvidos')->where('fk_tabela','interno')->where('fk_ocorrencia',$ocorrencias[$key]['id'])->select('fk_id_envolvido')->get();
            $ocorrencias[$key]['internosEnvolvidos'] = json_decode($ocorrencias[$key]['internosEnvolvidos'], true);

//            // agentes envolvidos
            $ocorrencias[$key]['servidoresEnvolvidos'] = DB::table('ocorrencia_envolvidos')->where('fk_tabela','servidor')->where('fk_ocorrencia',$ocorrencias[$key]['id'])->select('fk_id_envolvido')->get();
            $ocorrencias[$key]['servidoresEnvolvidos'] = json_decode($ocorrencias[$key]['servidoresEnvolvidos'],true);

//            // advogados envolvidos
            $ocorrencias[$key]['advogadosEnvolvidos'] = DB::table('ocorrencia_envolvidos')->where('fk_tabela','advogado')->where('fk_ocorrencia',$ocorrencias[$key]['id'])->select('fk_id_envolvido')->get();
            $ocorrencias[$key]['advogadosEnvolvidos'] = json_decode($ocorrencias[$key]['advogadosEnvolvidos'], true);

//            // visitas envolvidas
            $ocorrencias[$key]['visitasEnvolvidas'] = DB::table('ocorrencia_envolvidos')->where('fk_tabela','visita')->where('fk_ocorrencia',$ocorrencias[$key]['id'])->select('fk_id_envolvido')->get();
            $ocorrencias[$key]['visitasEnvolvidas'] = json_decode($ocorrencias[$key]['visitasEnvolvidas'], true);
        }

        
        return view('interno.show.dadosSeparados.ocorrencias', compact('interno', 'id', 'fotoPerfilDados','ocorrencias'));

    }

    public function showEscoltas($id) {
        
        $interno = DB::table('interno')->where('id', $id)->select('interno.*', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'interno.medida_seguranca')->get();

        if (count($interno) == 0) {
            return redirect()->route('inicio')->withInput()->with('alert-danger', 'Interno não encontrado!');
        }

        $interno = json_decode($interno, true)[0];


        if (!is_null($interno['rg_data']))
            $interno['rg_data'] = Tdata::TdataBR($interno['rg_data']);
        if (!is_null($interno['data_nascimento']))
            $interno['data_nascimento'] = Tdata::TdataBR($interno['data_nascimento']);
        if (!is_null($interno['data_nascimento']))
            $interno['idade'] = Tdata::getIdade($interno['data_nascimento']);
        else
            $interno['idade'] = "";
        if (!is_null($interno['created_at']))
            $interno['created_at'] = "às " . Tdata::getTempoTimestamp($interno['created_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['created_at']));
        else
            $interno['created_at'] = "";
        if (!is_null($interno['updated_at']))
            $interno['updated_at'] = "às " . Tdata::getTempoTimestamp($interno['updated_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['updated_at']));
        else
            $interno['updated_at'] = "";

        if (!is_null($interno['profissao']))
            $interno['profissao_nome'] = InternoController::getNomeProfissao($interno['profissao']);
        else
            $interno['profissao_nome'] = "";

        if (!is_null($interno['unidade_prisional']))
            $interno['unidade_prisional_nome'] = UnidadesController::getNomeUp($interno['unidade_prisional']);
        else
            $interno['unidade_prisional_nome'] = "";
        if (!is_null($interno['pavilhao']))
            $interno['pavilhao_nome'] = UnidadesController::getNomePav($interno['pavilhao']);
        else
            $interno['pavilhao_nome'] = "";
        if (!is_null($interno['ala']))
            $interno['ala_nome'] = UnidadesController::getNomeAla($interno['ala']);
        else
            $interno['ala_nome'] = "";
        if (!is_null($interno['cela']))
            $interno['cela_nome'] = UnidadesController::getNomeCela($interno['cela']);
        else
            $interno['cela_nome'] = "";
        if (!is_null($interno['condicao_interno']))
            $interno['condicao_nome'] = InternoController::getNomeCondicao($interno['condicao_interno']);
        else
            $interno['condicao_nome'] = "";
        if (!is_null($interno['faccao']))
            $interno['faccao_nome'] = InternoController::getNomeFaccao($interno['faccao']);
        else
            $interno['faccao_nome'] = "";

        // -------------------------- FOTOS -----------------------------------------------------------------------------------

        $fotoPerfilDados = self::getFotoPrincipalInternoBase64($id);

        
        // ------------------------- Escoltas -----------------------------------------------------------------------------------
        $escoltaDados = DB::table('escolta_internos')
            ->leftJoin('escolta', 'escolta.id','=', 'escolta_internos.fk_escolta')
            ->where('escolta_internos.fk_interno', $id)
            ->where('escolta.status', 'Finalizada')
            ->orderBy('escolta.data_saida', 'DESC')
            ->get();

        $escolta = array();
        foreach($escoltaDados as $escoltaDado)
        {
            $dadosEscoltas = DB::table('escolta')->where('id', $escoltaDado->fk_escolta)->get();
            $dadosEscoltaArray = array();
            if(count($dadosEscoltas) > 0){
                foreach($dadosEscoltas as $dadosEscolta)
                {
                    if($dadosEscolta->vtr_comandante != null)
                    {
                        $comandante = FacadeController::getNomeServidor($dadosEscolta->vtr_comandante)." (".$dadosEscolta->vtr_comandante.")";
                    }else{
                        $comandante = null;
                    }

                    if($dadosEscolta->vtr_motorista != null)
                    {
                        $motorista = FacadeController::getNomeServidor($dadosEscolta->vtr_motorista)." (".$dadosEscolta->vtr_motorista.")";
                    }else{
                        $motorista = null;
                    }

                    $internosEscoltaArray = array();

                        $internosEscolta = EscoltaController::getInternosEscolta($dadosEscolta->id, $id);

                        foreach($internosEscolta as $internoEsc)
                        {
                            array_push($internosEscoltaArray, array(
                                'nome' => self::getInternoNome($internoEsc->fk_interno),
                                'prontuario' => $internoEsc->fk_interno,
                            ));
                        }

                    array_push($dadosEscoltaArray, array(
                       'id' => $dadosEscolta->id,
                       'unidade' => UnidadesController::getNomeUp($dadosEscolta->unidade),
                       'tipo' => $dadosEscolta->tipo,
                       'motivo' => $dadosEscolta->motivo,
                       'destino' => $dadosEscolta->destino,
                       'dataSaida' => Tdata::TdataBR($dadosEscolta->data_saida),
                       'horaSaida' => $dadosEscolta->hora_saida,
                       'dataRetorno' => Tdata::TdataBR($dadosEscolta->data_retorno),
                        'horaRetorno' => $dadosEscolta->hora_retorno,
                        'vtrPlaca' => $dadosEscolta->vtr_placa,
                       'vtrComandante' =>  $comandante,
                       'matComandante' =>  $dadosEscolta->vtr_comandante,
                       'vtrMotorista' => $motorista,
                       'internosEscolta' => $internosEscoltaArray,
                       'status' => $dadosEscolta->status,
                       'obs' => $dadosEscolta->obs,
                    ));
                }

            }else{

                $dadosEscoltaArray = null;

            }


            $dadosEscoltaAgentes = DB::table('escolta_agentes')->where('fk_escolta', $escoltaDado->fk_escolta)->get();
            $dadosEscoltaAgentesArray = array();
            if(count($dadosEscoltaAgentes) > 0)
            {
                foreach($dadosEscoltaAgentes as $dadosEscoltaAgente)
                {
                    if($dadosEscoltaArray[0]['matComandante'] != null && $dadosEscoltaAgente->matricula == $dadosEscoltaArray[0]['matComandante'])
                    {
                        array_unshift($dadosEscoltaAgentesArray, array(
                            'nomeAgente' => "[COMANDANTE DA ESCOLTA] ".FacadeController::getNomeServidor($dadosEscoltaAgente->matricula),
                            'matricula' => $dadosEscoltaAgente->matricula

                        ));

                    }else{

                        array_push($dadosEscoltaAgentesArray, array(
                            'nomeAgente' => FacadeController::getNomeServidor($dadosEscoltaAgente->matricula),
                            'matricula' => $dadosEscoltaAgente->matricula

                        ));

                    }
                }
            }else{
                $dadosEscoltaAgentesArray = null;
            }

            $dadosEscoltaArquivos = DB::table('escolta_arquivos')->where('fk_escolta', $dadosEscoltaArray[0]['id'])->get();
            $dadosEscoltaArquivosArray = array();
            if(count($dadosEscoltaArquivos) > 0)
            {
                foreach($dadosEscoltaArquivos as $arquivos)
                {
                    array_push($dadosEscoltaArquivosArray, array(
                        'idArquivo' => $arquivos->id,
                        'nomeArquivo' => $arquivos->nome_arquivo,
                    ));

                }
            }else{

                $dadosEscoltaArquivosArray = null;
            }

            $dadosLogEscolta = DB::table('log')->where('tabela_alterada', 'escolta')->where('tipo_alteracao', 'add')->where('fk_id', $dadosEscoltaArray[0]['id'])->get();
            $logEscolta = array();
                foreach ($dadosLogEscolta as $log) {
                    $nomeServidor = FacadeController::getNomeServidorCpf($log->cpf_usuario);

                    array_push($logEscolta, array(
                        'data' => Tdata::TdataBR(Tdata::getDataTimestamp($log->created_at)),
                        'hora' => Tdata::getTempoTimestamp($log->created_at),
                        'tabela' => str_replace("_", " ", $log->tabela_alterada),
                        'descricao' => $log->texto,
                        'realizadoPor' => $nomeServidor . " (" . $log->cpf_usuario . ")",
                    ));
                }


            array_push($escolta, array(
                'dadosEscolta' => $dadosEscoltaArray,
                'dadosAgentes' => $dadosEscoltaAgentesArray,
                'dadosArquivos' => $dadosEscoltaArquivosArray,
                'dadosLog' => $logEscolta,
                'dataEscolta' => Tdata::TdataMysql($dadosEscoltaArray[0]['dataSaida']),
            ));
        }

        $escoltaDadosGEP = DB::table('gep_internos')
            ->leftJoin('gep', 'gep.id','=', 'gep_internos.fk_gep')
            ->where('gep_internos.fk_interno', $id)
            ->where('gep.status', 'Finalizada')
            ->orderBy('gep.data_saida', 'DESC')
            ->get();

        foreach($escoltaDadosGEP as $escoltaDadoGEP)
        {
            $dadosEscoltasGEP = DB::table('gep')->where('id', $escoltaDadoGEP->fk_gep)->get();
            $dadosEscoltaArrayGEP = array();
            if(count($dadosEscoltasGEP) > 0){

                foreach($dadosEscoltasGEP as $dadosEscoltaGEP)
                {
                    if($dadosEscoltaGEP->vtr_comandante != null)
                    {
                        $comandanteGEP = FacadeController::getNomeServidor($dadosEscoltaGEP->vtr_comandante)." (".$dadosEscoltaGEP->vtr_comandante.")";
                    }else{
                        $comandanteGEP = null;
                    }

                    if($dadosEscoltaGEP->vtr_motorista != null)
                    {
                        $motoristaGEP = FacadeController::getNomeServidor($dadosEscoltaGEP->vtr_motorista)." (".$dadosEscoltaGEP->vtr_motorista.")";
                    }else{
                        $motoristaGEP = null;
                    }

                    $internosEscoltaArray = array();

                    $internosEscolta = GepController::getInternosEscolta($dadosEscoltaGEP->id,$id);

                    foreach($internosEscolta as $internoEsc)
                    {
                        array_push($internosEscoltaArray, array(
                            'nome' => self::getInternoNome($internoEsc->fk_interno),
                            'prontuario' => $internoEsc->fk_interno,
                        ));
                    }

                    $idEscolta = GepController::getEscolta($dadosEscoltaGEP->id, $id);

                    array_push($dadosEscoltaArrayGEP, array(
                       'id' => $dadosEscoltaGEP->id,
                       'unidade' => UnidadesController::getNomeUp(EscoltaController::getUnidadeEscolta($idEscolta)),
                       'tipo' => 'GEP',
                       'motivo' => null,
                       'destino' => EscoltaController::getDestinoEscolta($idEscolta),
                       'dataSaida' => Tdata::TdataBR($dadosEscoltaGEP->data_saida),
                       'horaSaida' => $dadosEscoltaGEP->hora_saida,
                       'dataRetorno' => Tdata::TdataBR($dadosEscoltaGEP->data_retorno),
                        'horaRetorno' => $dadosEscoltaGEP->hora_retorno,
                        'vtrPlaca' => $dadosEscoltaGEP->vtr_placa,
                       'vtrComandante' =>  $comandanteGEP,
                       'matComandante' =>  $dadosEscoltaGEP->vtr_comandante,
                       'vtrMotorista' => $motoristaGEP,
                        'internosEscolta' => $internosEscoltaArray,
                        'status' => $dadosEscoltaGEP->status,
                       'obs' => null,
                    ));
                }

            }else{

                $dadosEscoltaArrayGEP = null;

            }

            $dadosEscoltaAgentesGEP = DB::table('gep_agentes')->where('fk_gep', $escoltaDadoGEP->fk_gep)->get();
            $dadosEscoltaAgentesArrayGEP = array();
            if(count($dadosEscoltaAgentesGEP) > 0)
            {
                foreach($dadosEscoltaAgentesGEP as $dadosEscoltaAgenteGEP)
                {
                    if($dadosEscoltaArrayGEP[0]['matComandante'] != null && $dadosEscoltaAgenteGEP->matricula == $dadosEscoltaArrayGEP[0]['matComandante'])
                    {
                        array_unshift($dadosEscoltaAgentesArrayGEP, array(
                            'nomeAgente' => "[COMANDANTE DA ESCOLTA] ".FacadeController::getNomeServidor($dadosEscoltaAgenteGEP->matricula),
                            'matricula' => $dadosEscoltaAgenteGEP->matricula

                        ));

                    }else{

                        array_push($dadosEscoltaAgentesArrayGEP, array(
                            'nomeAgente' => FacadeController::getNomeServidor($dadosEscoltaAgenteGEP->matricula),
                            'matricula' => $dadosEscoltaAgenteGEP->matricula

                        ));

                    }
                }
            }else{
                $dadosEscoltaAgentesArrayGEP = null;
            }

                $dadosEscoltaArquivosArrayGEP = null;

            array_push($escolta, array(
                'dadosEscolta' => $dadosEscoltaArrayGEP,
                'dadosAgentes' => $dadosEscoltaAgentesArrayGEP,
                'dadosArquivos' => $dadosEscoltaArquivosArrayGEP,
                'dadosLog' => null,
                'dataEscolta' => Tdata::TdataMysql($dadosEscoltaArrayGEP[0]['dataSaida']),
            ));
        }

        $escolta = collect($escolta)->sortBy('dataEscolta')->reverse()->toArray();
        // ------------------------- !Escoltas ----------------------------------------------------------------------------------

        return view('interno.show.dadosSeparados.escoltas', compact('interno', 'id', 'fotoPerfilDados','escolta'));

    }
    
    public function showSaudeAssistencia($id) {
        
        $interno = DB::table('interno')->where('id', $id)->select('interno.*', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'interno.medida_seguranca')->get();

        if (count($interno) == 0) {
            return redirect()->route('inicio')->withInput()->with('alert-danger', 'Interno não encontrado!');
        }

        $interno = json_decode($interno, true)[0];


        if (!is_null($interno['rg_data']))
            $interno['rg_data'] = Tdata::TdataBR($interno['rg_data']);
        if (!is_null($interno['data_nascimento']))
            $interno['data_nascimento'] = Tdata::TdataBR($interno['data_nascimento']);
        if (!is_null($interno['data_nascimento']))
            $interno['idade'] = Tdata::getIdade($interno['data_nascimento']);
        else
            $interno['idade'] = "";
        if (!is_null($interno['created_at']))
            $interno['created_at'] = "às " . Tdata::getTempoTimestamp($interno['created_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['created_at']));
        else
            $interno['created_at'] = "";
        if (!is_null($interno['updated_at']))
            $interno['updated_at'] = "às " . Tdata::getTempoTimestamp($interno['updated_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['updated_at']));
        else
            $interno['updated_at'] = "";

        if (!is_null($interno['profissao']))
            $interno['profissao_nome'] = InternoController::getNomeProfissao($interno['profissao']);
        else
            $interno['profissao_nome'] = "";

        if (!is_null($interno['unidade_prisional']))
            $interno['unidade_prisional_nome'] = UnidadesController::getNomeUp($interno['unidade_prisional']);
        else
            $interno['unidade_prisional_nome'] = "";
        if (!is_null($interno['pavilhao']))
            $interno['pavilhao_nome'] = UnidadesController::getNomePav($interno['pavilhao']);
        else
            $interno['pavilhao_nome'] = "";
        if (!is_null($interno['ala']))
            $interno['ala_nome'] = UnidadesController::getNomeAla($interno['ala']);
        else
            $interno['ala_nome'] = "";
        if (!is_null($interno['cela']))
            $interno['cela_nome'] = UnidadesController::getNomeCela($interno['cela']);
        else
            $interno['cela_nome'] = "";
        if (!is_null($interno['condicao_interno']))
            $interno['condicao_nome'] = InternoController::getNomeCondicao($interno['condicao_interno']);
        else
            $interno['condicao_nome'] = "";
        if (!is_null($interno['faccao']))
            $interno['faccao_nome'] = InternoController::getNomeFaccao($interno['faccao']);
        else
            $interno['faccao_nome'] = "";

        // -------------------------- FOTOS -----------------------------------------------------------------------------------

        $fotoPerfilDados = self::getFotoPrincipalInternoBase64($id);

                // ------------------------- Saude e Assistencia ----------------------------------------------------------------------------------

        $saudeAssistDados = DB::table('saude_assistencia');
        $saudeAssistDados->leftJoin('unidade', 'unidade.id', '=', 'saude_assistencia.unidade');
        $saudeAssistDados->leftJoin('interno', 'interno.id', '=', 'saude_assistencia.fk_interno');
        $saudeAssistDados->where('saude_assistencia.fk_interno', $id);
        $saudeAssistDados->where('saude_assistencia.status', 'A');
        $saude = $saudeAssistDados->select('saude_assistencia.*', DB::raw("DATE_FORMAT(saude_assistencia.data_atendimento,'%d/%m/%Y') as data_atendimento"),'unidade.sigla AS unidadeNome','interno.nome AS nomeInterno','interno.id AS idInterno')->orderBy('saude_assistencia.data_atendimento', 'DESC')->orderBy('saude_assistencia.hora_atendimento', 'DESC')->get();


        $saudeAssist = array();

        foreach($saude as $item)
        {

            if(count(LogController::getDadosLog($item->id,"saude_assistencia","add")) != 0) {
                $dadosLog = LogController::getDadosLog($item->id, "saude_assistencia", "add");
                $userLog = UsuarioController::getNomeUser($dadosLog[0]->cpf_usuario);
                $textoLog = "Registrado por: " . $userLog . " (" . $dadosLog[0]->cpf_usuario . ") em " . Tdata::TtimestampBR($dadosLog[0]->created_at);
            }
            else{
                $textoLog = "Sem log registrado";
            }

            array_push($saudeAssist, array(
                'setor' => $item->setor,
                'dataAtendimento' => $item->data_atendimento,
                'horaAtendimento' => $item->hora_atendimento,
                'descricao' => $item->descricao,
                'unidadeNome' => $item->unidadeNome,
                'crm' => $item->crm_medico,
                'nomeMedico' => $item->nome_medico,
                'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData(Tdata::TdataMysql($item->data_atendimento))),
                'log' => $textoLog,

            ));
        }

        // ------------------------- !Saude e Assistencia ---------------------------------------------------------------------------------

        return view('interno.show.dadosSeparados.saudeAssistencia', compact('interno', 'id', 'fotoPerfilDados','saudeAssist'));

    }

        public function showProcessos($id) {
        
                $interno = DB::table('interno')->where('id', $id)->select('interno.*', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'interno.medida_seguranca')->get();

        if (count($interno) == 0) {
            return redirect()->route('inicio')->withInput()->with('alert-danger', 'Interno não encontrado!');
        }

        $interno = json_decode($interno, true)[0];


        if (!is_null($interno['rg_data']))
            $interno['rg_data'] = Tdata::TdataBR($interno['rg_data']);
        if (!is_null($interno['data_nascimento']))
            $interno['data_nascimento'] = Tdata::TdataBR($interno['data_nascimento']);
        if (!is_null($interno['data_nascimento']))
            $interno['idade'] = Tdata::getIdade($interno['data_nascimento']);
        else
            $interno['idade'] = "";
        if (!is_null($interno['created_at']))
            $interno['created_at'] = "às " . Tdata::getTempoTimestamp($interno['created_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['created_at']));
        else
            $interno['created_at'] = "";
        if (!is_null($interno['updated_at']))
            $interno['updated_at'] = "às " . Tdata::getTempoTimestamp($interno['updated_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['updated_at']));
        else
            $interno['updated_at'] = "";

        if (!is_null($interno['profissao']))
            $interno['profissao_nome'] = InternoController::getNomeProfissao($interno['profissao']);
        else
            $interno['profissao_nome'] = "";

        if (!is_null($interno['unidade_prisional']))
            $interno['unidade_prisional_nome'] = UnidadesController::getNomeUp($interno['unidade_prisional']);
        else
            $interno['unidade_prisional_nome'] = "";
        if (!is_null($interno['pavilhao']))
            $interno['pavilhao_nome'] = UnidadesController::getNomePav($interno['pavilhao']);
        else
            $interno['pavilhao_nome'] = "";
        if (!is_null($interno['ala']))
            $interno['ala_nome'] = UnidadesController::getNomeAla($interno['ala']);
        else
            $interno['ala_nome'] = "";
        if (!is_null($interno['cela']))
            $interno['cela_nome'] = UnidadesController::getNomeCela($interno['cela']);
        else
            $interno['cela_nome'] = "";
        if (!is_null($interno['condicao_interno']))
            $interno['condicao_nome'] = InternoController::getNomeCondicao($interno['condicao_interno']);
        else
            $interno['condicao_nome'] = "";
        if (!is_null($interno['faccao']))
            $interno['faccao_nome'] = InternoController::getNomeFaccao($interno['faccao']);
        else
            $interno['faccao_nome'] = "";

        // -------------------------- FOTOS -----------------------------------------------------------------------------------

        $fotoPerfilDados = self::getFotoPrincipalInternoBase64($id);

        
        $processosDados = DB::table("interno_processos")->where("fk_interno", $id)->where("status", "A")->get();
        $processos = array();
        $skip = 0;
        foreach($processosDados as $processo)
        {

            if($processo->tipo == "inqueritoPolicial")
            {

                $dispositivoLegal = array();

                $dadosInquerito = DB::table("interno_processos_inquerito")
                    ->where("fk_processo", $processo->id)
                    ->select("interno_processos_inquerito.*",DB::raw("DATE_FORMAT(data_inquerito,'%d/%m/%Y') as dataInquerito"), DB::raw("DATE_FORMAT(data_fato,'%d/%m/%Y') as dataFato"), DB::raw("DATE_FORMAT(data_prisao,'%d/%m/%Y') as dataPrisao"))
                    ->get()[0];

                $dispositivoLegalDados1 = DB::table("interno_processos_dispositivo_legal")->where("id_processo", $processo->id)->get();

                foreach($dispositivoLegalDados1 as $lei)
                {
                    $skip = $skip + 1;
                    if(!is_null($lei->seq_um))
                    {
                        $seqUm = ArtigosProcessosController::getSequencia($lei->seq_um_tipo, $lei->seq_um);
                    }else{
                        $seqUm = null;
                    }
                    if(!is_null($lei->seq_dois))
                    {
                        $seqDois = ArtigosProcessosController::getSequencia($lei->seq_dois_tipo, $lei->seq_dois);
                    }else{
                        $seqDois = null;
                    }
                    array_push($dispositivoLegal, array(
                        "lei" => ArtigosProcessosController::getNomeLei($lei->dispositivo_legal),
                        "artigo" => ArtigosProcessosController::getNomeArtigo($lei->artigo),
                        "nome" => ArtigosProcessosController::getNomeNatureza($lei->artigo),
                        "glossario" => ArtigosProcessosController::getGlossarioNatureza($lei->artigo),
                        "sequenciaUm" => $seqUm,
                        "sequenciaDois" => $seqDois,
                    ));
                }


                array_push($processos, array(
                    "tipo" => "Inquérito Policial",
                    "id" => $processo->id,
                    "dadosProcesso" => $dadosInquerito,
                    "dispositivoLegal" => $dispositivoLegal,
                ));


            }

            if($processo->tipo == "processo")
            {

                $dispositivoLegal = array();
                $dadosProcesso = DB::table("interno_processos_processo")
                    ->where("fk_processo", $processo->id)
                    ->select("interno_processos_processo.*",DB::raw("DATE_FORMAT(data_processo,'%d/%m/%Y') as dataProcesso"))
                    ->get()[0];

                $dispositivoLegalDados2 = DB::table("interno_processos_dispositivo_legal")->where("id_processo", $processo->id)->orderBy("id", "ASC")->get();

                foreach($dispositivoLegalDados2 as $lei)
                {
                    $skip = $skip + 1;
                    if(!is_null($lei->seq_um))
                    {
                        $seqUm = ArtigosProcessosController::getSequencia($lei->seq_um_tipo, $lei->seq_um);
                    }else{
                        $seqUm = null;
                    }
                    if(!is_null($lei->seq_dois))
                    {
                        $seqDois = ArtigosProcessosController::getSequencia($lei->seq_dois_tipo, $lei->seq_dois);
                    }else{
                        $seqDois = null;
                    }
                    array_push($dispositivoLegal, array(
                        "lei" => ArtigosProcessosController::getNomeLei($lei->dispositivo_legal),
                        "artigo" => ArtigosProcessosController::getNomeArtigo($lei->artigo),
                        "nome" => ArtigosProcessosController::getNomeNatureza($lei->artigo),
                        "glossario" => ArtigosProcessosController::getGlossarioNatureza($lei->artigo),
                        "sequenciaUm" => $seqUm,
                        "sequenciaDois" => $seqDois,
                    ));
                }


                array_push($processos, array(
                    "tipo" => "Processo",
                    "id" => $processo->id,
                    "dadosProcesso" => $dadosProcesso,
                    "dispositivoLegal" => $dispositivoLegal,
                ));


            }

            if($processo->tipo == "condenacao")
            {
                $dispositivoLegal = array();
                $dadosCondenacao = DB::table("interno_processos_condenacao")
                    ->where("fk_processo", $processo->id)
                    ->select("interno_processos_condenacao.*",DB::raw("DATE_FORMAT(data_transito_julgado,'%d/%m/%Y') as dataTransitoJulgado"),DB::raw("DATE_FORMAT(data_sentenca,'%d/%m/%Y') as dataSentenca"))
                    ->get()[0];

                $dispositivoLegalDados = DB::table("interno_processos_dispositivo_legal")->where("id_processo", $processo->id)->get();

                foreach($dispositivoLegalDados as $lei)
                {
                    if(!is_null($lei->seq_um))
                    {
                        $seqUm = ArtigosProcessosController::getSequencia($lei->seq_um_tipo, $lei->seq_um);
                    }else{
                        $seqUm = null;
                    }
                    if(!is_null($lei->seq_dois))
                    {
                        $seqDois = ArtigosProcessosController::getSequencia($lei->seq_dois_tipo, $lei->seq_dois);
                    }else{
                        $seqDois = null;
                    }
                    array_push($dispositivoLegal, array(
                        "lei" => ArtigosProcessosController::getNomeLei($lei->dispositivo_legal),
                        "artigo" => ArtigosProcessosController::getNomeArtigo($lei->artigo),
                        "nome" => ArtigosProcessosController::getNomeNatureza($lei->artigo),
                        "glossario" => ArtigosProcessosController::getGlossarioNatureza($lei->artigo),
                        "sequenciaUm" => $seqUm,
                        "sequenciaDois" => $seqDois,
                    ));
                }


                array_push($processos, array(
                    "tipo" => "Condenação",
                    "id" => $processo->id,
                    "dadosProcesso" => $dadosCondenacao,
                    "dispositivoLegal" => $dispositivoLegal,
                ));


            }
        }
        
        return view('interno.show.dadosSeparados.processos', compact('interno', 'id', 'fotoPerfilDados','processos'));

    }
    

    public function showDocumentos($id) {
        
        $interno = DB::table('interno')->where('id', $id)->select('interno.*', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'interno.medida_seguranca')->get();

        if (count($interno) == 0) {
            return redirect()->route('inicio')->withInput()->with('alert-danger', 'Interno não encontrado!');
        }

        $interno = json_decode($interno, true)[0];


        if (!is_null($interno['rg_data']))
            $interno['rg_data'] = Tdata::TdataBR($interno['rg_data']);
        if (!is_null($interno['data_nascimento']))
            $interno['data_nascimento'] = Tdata::TdataBR($interno['data_nascimento']);
        if (!is_null($interno['data_nascimento']))
            $interno['idade'] = Tdata::getIdade($interno['data_nascimento']);
        else
            $interno['idade'] = "";
        if (!is_null($interno['created_at']))
            $interno['created_at'] = "às " . Tdata::getTempoTimestamp($interno['created_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['created_at']));
        else
            $interno['created_at'] = "";
        if (!is_null($interno['updated_at']))
            $interno['updated_at'] = "às " . Tdata::getTempoTimestamp($interno['updated_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['updated_at']));
        else
            $interno['updated_at'] = "";

        if (!is_null($interno['profissao']))
            $interno['profissao_nome'] = InternoController::getNomeProfissao($interno['profissao']);
        else
            $interno['profissao_nome'] = "";

        if (!is_null($interno['unidade_prisional']))
            $interno['unidade_prisional_nome'] = UnidadesController::getNomeUp($interno['unidade_prisional']);
        else
            $interno['unidade_prisional_nome'] = "";
        if (!is_null($interno['pavilhao']))
            $interno['pavilhao_nome'] = UnidadesController::getNomePav($interno['pavilhao']);
        else
            $interno['pavilhao_nome'] = "";
        if (!is_null($interno['ala']))
            $interno['ala_nome'] = UnidadesController::getNomeAla($interno['ala']);
        else
            $interno['ala_nome'] = "";
        if (!is_null($interno['cela']))
            $interno['cela_nome'] = UnidadesController::getNomeCela($interno['cela']);
        else
            $interno['cela_nome'] = "";
        if (!is_null($interno['condicao_interno']))
            $interno['condicao_nome'] = InternoController::getNomeCondicao($interno['condicao_interno']);
        else
            $interno['condicao_nome'] = "";
        if (!is_null($interno['faccao']))
            $interno['faccao_nome'] = InternoController::getNomeFaccao($interno['faccao']);
        else
            $interno['faccao_nome'] = "";

        // -------------------------- FOTOS -----------------------------------------------------------------------------------

        $fotoPerfilDados = self::getFotoPrincipalInternoBase64($id);

                // ------------------------- DOCUMENTOS -----------------------------------------------------------------------------------

        $documentos = DB::table('documento')
            ->leftJoin('log', function ($query) {
                $query->on('log.fk_id', '=', 'documento.id')
                    ->where('log.tipo_alteracao',"add")
                    ->where('log.tabela_alterada',"documento");
            })
            ->where('documento.fk_id',$id)
            ->where('documento.fk_tabela','interno')
            ->select("log.*", "log.created_at as logData","documento.*")
            ->get();

        foreach($documentos as $documento)
        {
            $documento->created_at = Tdata::TtimestampBR($documento->created_at);
            $documento->logData = Tdata::TtimestampBR($documento->logData);
            $documento->cpf_usuario = FacadeController::getNomeServidorCpf($documento->cpf_usuario)." (".$documento->cpf_usuario.")";
        }

        // ------------------------------------------------------------------------------------------------------------------------

        $i = 0;
        $keys = array_keys($interno);
        foreach($interno as $item){
            if(is_null($item) || $item == ""){
                $interno[$keys[$i]] = "Não Informado";
            }
            $i++;
        }

        return view('interno.show.dadosSeparados.documentos', compact('interno', 'id', 'fotoPerfilDados','documentos'));

    }
    
    public function showImprimir($id) {
        
                $interno = DB::table('interno')->where('id', $id)->select('interno.*', DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'interno.medida_seguranca')->get();

        if (count($interno) == 0) {
            return redirect()->route('inicio')->withInput()->with('alert-danger', 'Interno não encontrado!');
        }

        $interno = json_decode($interno, true)[0];


        if (!is_null($interno['rg_data']))
            $interno['rg_data'] = Tdata::TdataBR($interno['rg_data']);
        if (!is_null($interno['data_nascimento']))
            $interno['data_nascimento'] = Tdata::TdataBR($interno['data_nascimento']);
        if (!is_null($interno['data_nascimento']))
            $interno['idade'] = Tdata::getIdade($interno['data_nascimento']);
        else
            $interno['idade'] = "";
        if (!is_null($interno['created_at']))
            $interno['created_at'] = "às " . Tdata::getTempoTimestamp($interno['created_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['created_at']));
        else
            $interno['created_at'] = "";
        if (!is_null($interno['updated_at']))
            $interno['updated_at'] = "às " . Tdata::getTempoTimestamp($interno['updated_at']) . " em " . Tdata::TdataBR(Tdata::getDataTimestamp($interno['updated_at']));
        else
            $interno['updated_at'] = "";

        if (!is_null($interno['profissao']))
            $interno['profissao_nome'] = InternoController::getNomeProfissao($interno['profissao']);
        else
            $interno['profissao_nome'] = "";

        if (!is_null($interno['unidade_prisional']))
            $interno['unidade_prisional_nome'] = UnidadesController::getNomeUp($interno['unidade_prisional']);
        else
            $interno['unidade_prisional_nome'] = "";
        if (!is_null($interno['pavilhao']))
            $interno['pavilhao_nome'] = UnidadesController::getNomePav($interno['pavilhao']);
        else
            $interno['pavilhao_nome'] = "";
        if (!is_null($interno['ala']))
            $interno['ala_nome'] = UnidadesController::getNomeAla($interno['ala']);
        else
            $interno['ala_nome'] = "";
        if (!is_null($interno['cela']))
            $interno['cela_nome'] = UnidadesController::getNomeCela($interno['cela']);
        else
            $interno['cela_nome'] = "";
        if (!is_null($interno['condicao_interno']))
            $interno['condicao_nome'] = InternoController::getNomeCondicao($interno['condicao_interno']);
        else
            $interno['condicao_nome'] = "";
        if (!is_null($interno['faccao']))
            $interno['faccao_nome'] = InternoController::getNomeFaccao($interno['faccao']);
        else
            $interno['faccao_nome'] = "";

        // -------------------------- FOTOS -----------------------------------------------------------------------------------

        $fotoPerfilDados = self::getFotoPrincipalInternoBase64($id);

        return view('interno.show.dadosSeparados.imprimir', compact('interno', 'id', 'fotoPerfilDados'));

    }


    public function companheirosCela($id){

        $interno = DB::table('interno')->where('id', $id)->select('interno.*',DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" WHEN interno.lideranca = "N" THEN "Não" ELSE "Não Informado" END) AS lideranca'), 'interno.medida_seguranca')->get();
        $interno = json_decode($interno, true)[0];

        $outrosnomes = DB::table('outrosnomes')->where('pk_tabela', 'interno')->where('pk_id', $id)->get();
        $interno['outrosnomes'] = [];
        if(!is_null($outrosnomes)){
            foreach(json_decode($outrosnomes,true) as $i){
                array_push($interno['outrosnomes'], $i['nome']);
            }
        }
        $enderecos = DB::table('endereco')->where('fk_tabela', 'interno')->where('fk_id', $id)->get();
        $interno['enderecos'] = [];
        if(!is_null($enderecos)){
            foreach(json_decode($enderecos,true) as $i){
                array_push($interno['enderecos'], $i);
            }
        }
        if(!is_null($interno['rg_data'])) $interno['rg_data'] = Tdata::TdataBR($interno['rg_data']);
        if(!is_null($interno['data_nascimento'])) $interno['data_nascimento'] = Tdata::TdataBR($interno['data_nascimento']);
        if(!is_null($interno['data_nascimento'])) $interno['idade'] = Tdata::getIdade($interno['data_nascimento']);
        else $interno['idade'] = "";
        if(!is_null($interno['created_at'])) $interno['created_at'] = "às ".Tdata::getTempoTimestamp($interno['created_at'])." em ".Tdata::TdataBR(Tdata::getDataTimestamp($interno['created_at']));
        else $interno['created_at'] = "";
        if(!is_null($interno['updated_at'])) $interno['updated_at'] = "às ".Tdata::getTempoTimestamp($interno['updated_at'])." em ".Tdata::TdataBR(Tdata::getDataTimestamp($interno['updated_at']));
        else $interno['updated_at'] = "";

        if(!is_null($interno['profissao'])) $interno['profissao_nome'] = InternoController::getNomeProfissao($interno['profissao']);
        else $interno['profissao_nome'] = "";

        if(!is_null($interno['unidade_prisional'])) $interno['unidade_prisional_nome'] = UnidadesController::getNomeUp($interno['unidade_prisional']);
        else $interno['unidade_prisional_nome'] = "";
        if(!is_null($interno['pavilhao'])) $interno['pavilhao_nome'] = UnidadesController::getNomePav($interno['pavilhao']);
        else $interno['pavilhao_nome'] = "";
        if(!is_null($interno['ala'])) $interno['ala_nome'] = UnidadesController::getNomeAla($interno['ala']);
        else $interno['ala_nome'] = "";
        if(!is_null($interno['cela'])) $interno['cela_nome'] = UnidadesController::getNomeCela($interno['cela']);
        else $interno['cela_nome'] = "";
        if(!is_null($interno['condicao_interno'])) $interno['condicao_nome'] = InternoController::getNomeCondicao($interno['condicao_interno']);
        else $interno['condicao_nome'] = "";
        if(!is_null($interno['faccao'])) $interno['faccao_nome'] = InternoController::getNomeFaccao($interno['faccao']);
        else $interno['faccao_nome'] = "";

        $companheirosCela = InternoController::getCompanheirosCela($interno['id']);

        $fotoPerfilDados = self::getFotoPrincipalInternoBase64($id);

        return view('interno.show.companheirosCela', compact('interno','fotoPerfilDados','companheirosCela','id'));
    }

    public function getCompanheirosCela($id){
        $dadosInternos = self::getDadosInterno($id);

        if($dadosInternos[0]->status == "A") {

            $companheirosCelaDados = DB::table('interno')->where('cela', $dadosInternos[0]->cela)->where('id', '!=', $id)->where('status', 'A')->get();

            $companheirosCela = array();

            foreach ($companheirosCelaDados as $companheirosCelaDado) {
                $diasCela = self::getDiasCela($companheirosCelaDado->id, $companheirosCelaDado->cela);

                if ($companheirosCelaDado->lideranca == 'N') {
                    $lideranca = "Não";
                } else if($companheirosCelaDado->lideranca == 'S') {
                    $lideranca = "Sim";
                } else{
                    $lideranca = "Não Informado";
                }

                $outrosNomes = DB::table('outrosnomes')->where('pk_id',$companheirosCelaDado->id)->where('pk_tabela','interno')->get();
                $outrosNomes = json_decode($outrosNomes,true);

                array_push($companheirosCela, array(
                    'id' => $companheirosCelaDado->id,
                    'nome' => $companheirosCelaDado->nome,
                    'outrosnomes' => $outrosNomes,
                    'faccao' => self::getNomeFaccao($companheirosCelaDado->faccao),
                    'lideranca' => $lideranca,
                    'periculosidade' => $companheirosCelaDado->periculosidade,
                    'diasCela' => $diasCela,
                    'cela' => $companheirosCelaDado->cela,
                ));

            }
        }else{
            $companheirosCela = null;
        }

        return $companheirosCela;
    }

    public static function getDiasCela($idInterno, $celaAtual)
    {
        $dados = DB::table('movimentacao_interno')
            ->leftJoin('movimentacao','movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao_interno.fk_interno', $idInterno)
            ->where('movimentacao.cela_destino', $celaAtual)
            ->where('movimentacao.tipo', 'mudancacela')
            ->orderBy('movimentacao.data','DESC')
            ->first();

        //dd((count(collect($dados)) > 0));
        if(count(collect($dados)) > 0)
        {

            $data = Tdata::getDataTimestamp($dados->data);
            if($data == "") {
                return "* Não Calculado *";
            }
            $tempo = Tdata::diffDate($data, date('Y-m-d'), 'D');
            return intval($tempo)." Dias";

        }else{

            return "* Não Calculado *";
        }
    }

    public function confereGeral()
    {

        $lotacaoServidor = FacadeController::getLotacaoServidor(Auth::user()->cpf);

        $unidades = DB::table('unidade')->where('sigla',$lotacaoServidor)->orderBy('sigla', 'ASC')->get();

        //dd($unidades);

        return view('interno.confere.confere', compact('unidades'));

    }

    public function confereGeralStatusAjax() {
        
        $status = DB::table('confere_pdf_status')->get()[0]->status;
        
        return $status;
        
    }

    public function confereGeralStore(Request $request)
    {

        //dd($request->input());

        ini_set('max_execution_time', 3000);
        ini_set('memory_limit','256M');

        //dd($request->input());
        $unidade = $request->get('unidade_prisional');
        $pavilhao = $request->get('pavilhao');
        $ala = $request->get('ala');
        $cela = $request->get('cela');
        $fotos = $request->get('fotos');
        
        if($fotos == null){
            $exibirFoto = false;
        } else {
            $exibirFoto = true;
        }
        $nomeUnidade = UnidadesController::getNomeUp($unidade);
        $nomeServidor = FacadeController::getNomeServidorCpf(Auth::user()->cpf)." (".Auth::user()->cpf.")";
        $dataAtualizacao = DB::table('confere_pdf_status')->get()[0]->data_atualizacao;
        $dataAtualizacao = Tdata::TtimestampBR($dataAtualizacao);


        $internosDados = DB::table('confere_pdf');
        $internosDados->where('interno_unidade', $unidade);
        if(!is_null($pavilhao))
        {
            $internosDados->where('interno_pavilhao', $pavilhao);
        }
        if(!is_null($ala))
        {
            $internosDados->where('interno_ala', $ala);
        }
        if(!is_null($cela))
        {
            $internosDados->where('interno_cela', $cela);
        }

        
         $internos = $internosDados->select('confere_pdf.*', DB::raw("DATE_FORMAT(sancao_triagem_data_final,'%d/%m/%Y') as sancao_triagem_data_final"))->orderBy('interno_nome','ASC')->get();


        if(!is_null($pavilhao))
        {
            $pavilhaoDados = DB::table('pavilhao')->where('fk_up', $unidade)->where('id', $pavilhao)->where('status', 'A')->orderBy('nome','ASC')->get();
        }else{
            $pavilhaoDados = DB::table('pavilhao')->where('fk_up', $unidade)->where('status', 'A')->orderBy('nome','ASC')->get();

        }
        $dadosPavilhao = array();
        $pvArray = array();
        foreach ($pavilhaoDados as $dadosPv) {
            array_push($pvArray, $dadosPv->id);
        }

        if(!is_null($ala))
        {
            $AlaDados = DB::table('ala')->whereIn('fk_pavilhao', $pvArray)->where('id', $ala)->where('status','A')->orderBy('nome','ASC')->get();

        }else{
            $AlaDados = DB::table('ala')->whereIn('fk_pavilhao', $pvArray)->where('status','A')->orderBy('nome','ASC')->get();

        }
        $dadosAla = array();
        $alaArray = array();
        foreach($AlaDados as $dAla)
        {
            array_push($alaArray, $dAla->id);
        }

        foreach($pavilhaoDados as $dPv)
        {
            array_push($dadosPavilhao, array(
                'nome' => $dPv->nome,
                'id' => $dPv->id,
                'qtdInternos' => DB::table('interno')->where('unidade_prisional', $unidade)->where('pavilhao', $dPv->id)->where('status','A')->count()
            ));
        }

        if(!is_null($cela))
        {
            $celaDados = DB::table('cela')->whereIn('fk_ala', $alaArray)->where('id', $cela)->where('status','A')->orderBy('nome','ASC')->get();

        }else{
            $celaDados = DB::table('cela')->whereIn('fk_ala', $alaArray)->where('status','A')->orderBy('nome','ASC')->get();

        }
        $dadosCela = array();
        foreach($AlaDados as $dAl)
        {
            array_push($dadosAla, array(
                'nome' => $dAl->nome,
                'id' => $dAl->id,
                'fk_pavilhao' => $dAl->fk_pavilhao,
                'qtdCelas' => DB::table('cela')->where('fk_ala', $dAl->id)->where('status','A')->orderBy('nome','ASC')->count(),
                'qtdInternos' => DB::table('confere_pdf')->where('interno_unidade', $unidade)->where('interno_ala', $dAl->id)->count()

            ));
        }
        foreach($celaDados as $celaDado)
        {
            array_push($dadosCela, array(
                'nome' => $celaDado->nome,
                'id' => $celaDado->id,
                'fk_ala' => $celaDado->fk_ala,
                'qtdInternos' => DB::table('confere_pdf')->where('interno_unidade', $unidade)->where('interno_cela', $celaDado->id)->count(),
            ));
        }

               
        if($request->get('confere') == "simples"){

            $pdf = \PDF::loadView('interno.confere.confereSimplesPdf', compact('internos','dadosPavilhao','dadosAla','dadosCela'))
                ->setOption('encoding', 'utf-8')
                ->setOption('margin-bottom', 10)
                ->setOption('margin-top', 30)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('margin-bottom', 10)
                ->setOption('header-html', view('template.header.headerConferePdf',compact('nomeUnidade','dataAtualizacao')))
                ->setOption ('footer-center', 'Gerado em'.date('\ d/m/Y\ - H:i:s').' | Página [page] de [topage]')
                ->setOption('footer-font-size', 7)
                ->setPaper('a4');
            return $pdf->inline();

        } else if($request->get('confere') == "resumido"){

            $pdf = \PDF::loadView('interno.confere.confereResumidoPdf', compact('internos','dadosPavilhao','dadosAla','dadosCela'))
                ->setOption('encoding', 'utf-8')
                ->setOption('margin-bottom', 10)
                ->setOption('margin-top', 30)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('margin-bottom', 10)
                ->setOption('header-html', view('template.header.headerConfereResumidoPdf',compact('nomeUnidade','dataAtualizacao')))
                ->setOption ('footer-center', 'Gerado em'.date('\ d/m/Y\ - H:i:s').' | Página [page] de [topage]')
                ->setOption('footer-font-size', 7)
                ->setPaper('a4');
            return $pdf->inline();

        } else if($request->get('confere') == "completo"){


            $pdf = \PDF::loadView('interno.confere.conferePdf', compact('internos','dadosPavilhao','dadosAla','dadosCela', 'exibirFoto'))
                ->setOption('encoding', 'utf-8')
                ->setOption('margin-bottom', 10)
                ->setOption('margin-top', 30)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('margin-bottom', 10)
                ->setOption('header-html', view('template.header.headerConferePdf',compact('nomeUnidade','dataAtualizacao')))
                ->setOption ('footer-center', 'Gerado em'.date('\ d/m/Y\ - H:i:s').' | Página [page] de [topage]')
                ->setOption('footer-font-size', 7)
                ->setPaper('a4');
            return $pdf->inline();
        }
    }

    public static function getAtendimentoAdvHoje($idInterno){
        return DB::table('advogado_agendamento')->where('fk_interno', $idInterno)->where('data', '=', Tdata::TdataHojeMysql())->count() > 0;
    }

    public static function multiPropertySort($collection, array $sorting_instructions){

        return $collection->sort(function ($a, $b) use ($sorting_instructions){

            //stuff starts here to answer question...

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

    public function getFotoPrincipalInternoAjax(Request $request){

        $idInterno = $request->get('idInterno');
        
                $fotoDados = DB::table('foto')
                ->where('fk_tabela','interno')
                ->where('tipo', 'principal')
                ->where('fk_id', $idInterno)
                ->get();
        
        if(count($fotoDados) > 0)
        {
            if (is_dir(storage_path('app/arquivos/internos/fotos/' . $idInterno . "/"))) {

                $dir = storage_path('app/arquivos/internos/fotos/' . $idInterno . "/");

                $b = scandir($dir, 1);

                // se houver foto cadastrada
                if (file_exists(storage_path('app/arquivos/internos/fotos/' . $idInterno . "/" . $fotoDados[0]->nome_arquivo))) {

                    $path = storage_path('app/arquivos/internos/fotos/' . $idInterno . "/" . $fotoDados[0]->nome_arquivo);
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $foto = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
                
                return $foto;

                } else {

                    $foto = null;
                }
        }
        else{
            $foto = null;
        }


    }

    public static function getSancaoTriagem($idInterno){
        return DB::table('sancao_triagem')->where('fk_interno', $idInterno)->where('status','A')->orderBy('data_fim','DESC')->first();
    }

    public static function getDataSancaoTriagem($idInterno){
        return Tdata::TdataBR(DB::table('sancao_triagem')->where('fk_interno', $idInterno)->where('status','A')->orderBy('data_fim','DESC')->first()->data_fim);
    }

    public static function temEscoltaConfirmada($idInterno){
        return DB::table('escolta_internos')
            ->join('escolta','escolta.id','=','escolta_internos.fk_escolta')
            ->where('escolta_internos.fk_interno',$idInterno)
            ->where('escolta.status','Confirmada')
            ->where('escolta.data_saida', Tdata::TdataHojeMysql())
            ->count() > 0;
    }

}
