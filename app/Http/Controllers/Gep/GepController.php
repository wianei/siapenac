<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados ao controle de acesso
|
*/

namespace App\Http\Controllers\Gep;

use App\Classes\Constantes;
use App\Classes\Tdata;
use App\Http\Controllers\Escolta\EscoltaController;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Foto\FotoController;
use App\Http\Controllers\Interno\InternoController;
use App\Http\Controllers\Judiciario\JudiciarioController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Movimentacoes\MovimentacoesController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Interno;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GepController extends Controller
{
    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');

        $this->middleware('auth');

    }

    public function callAction($method, $parameters)
    {

        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::verifyPermission("GepController", $method);
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }


//        $metodosGep = collect(['gerenciamentoEscolta','viaturas','viaturasInserir','novaSolicitacao','documentoEscolta','storeNovaSolicitacao','painelSolicitacoes','confirmarSolicitacao','repassarSolicitacao','cancelarSolicitacao','painelEscoltas','cancelarEscolta','finalizarEscolta','novaEscolta','internosPendentesEscoltaPdf','storeNovaEscolta','dadosJudiciario','dadosJudiciarioInserir','dadosJudiciarioEditar','storeDadosJudiciario','updateDadosJudiciario','editar','editarStore','show','showEscolta','arquivosEscolta','pautaDiaPdf','equipeDiaEditarStore','equipeDiaEditar','pautaDiaStore','pautaDia','documentos','docMissoes','docMissoesCapaMissao']);
//
//        if($metodosGep->contains($method))
//        {
//            $siglaUpLogado = FacadeController::getLotacaoServidor(Auth::user()->cpf, "LOTAÇÃO NO RH");
//
//            if (Auth::user()->tipo != 500 && Auth::user()->tipo != 1000 && $siglaUpLogado != "COPEN") {
//                return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
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
    public static function getDadosEscolta($idGep, $interno)
    {
        $dados = DB::table("gep_internos")
            ->leftJoin('gep','gep.id','=','gep_internos.fk_gep')
            ->leftJoin('escolta','escolta.id','=','gep_internos.fk_escolta')
            ->leftJoin('gep_equipe','gep_equipe.placa_vtr','=','gep.vtr_placa')
            ->where('gep.id', $idGep)
            ->where('gep_internos.fk_interno', $interno)
            ->select('escolta.*','escolta.hora_saida as horario_saida','gep.*','gep.id AS gepId','gep_equipe.id AS regiao')
            ->get();

        //dd($dados);

        return $dados;

    }

    public static function getRegiaoEquipeInterno($idGep, $interno, $data)
    {
        $dados = DB::table("gep_internos")
            ->leftJoin('gep','gep.id','=','gep_internos.fk_gep')
            ->leftJoin('gep_equipe','gep_equipe.placa_vtr','=','gep.vtr_placa')
            ->where('gep.id', $idGep)
            ->where('gep_equipe.data', $data)
            ->where('gep.data_saida', $data)
            ->where('gep_internos.fk_interno', $interno)
            ->select('gep_equipe.regiao AS regiao')
            ->get();

        //dd($dados);

        return $dados[0]->regiao;

    }
    public static function getEscolta($idGep, $idInterno)
    {
        $dados = DB::table('gep_internos')->where('fk_gep', $idGep)->where('fk_interno', $idInterno)->get();

        return $dados[0]->fk_escolta;
    }

    public static function getInternosEscolta($idGep, $not=null)
    {
        if($not != null)
        {
            $dados = DB::table('gep_internos')->where('fk_gep', $idGep)->where('fk_interno','!=',$not)->get();

        }else {
            $dados = DB::table('gep_internos')->where('fk_gep', $idGep)->get();
        }
        return $dados;
    }

    public static function getIdGepResponsavel($idUP)
    {
        $siglaUP = UnidadesController::getSiglaUp($idUP);
        $responsavel = 6405; // FTPEN-JOÃO PESSOA
        // if($siglaUP == "PES"|| $siglaUP == "CDP-JUCURUTU"){
        //     $responsavel = 130; // GEP CAICÓ
        // }
        // else if($siglaUP == "CDP-APODI" || $siglaUP == "CP CARAUBAS"){
        //     $responsavel = 140; // GEP CARAÚBAS
        // }
        // else if($siglaUP == "CPEAMN" || $siglaUP == "CP MOSSORO"){
        //     $responsavel = 150; // GEP MOSSORÓ
        // }
        // else if($siglaUP == "CPRPF"){
        //     $responsavel = 165; // GEP PAU DOS FERROS
        // }
        return $responsavel;
    }

    public function gerenciamentoEscolta()
    {
        return view('gep.escolta.gerenciamentoEscolta');
    }

    public function viaturas()
    {
        return view('gep.escolta.viaturas');
    }

    public function viaturasInserir()
    {
        return view('gep.escolta.viaturasInserir');
    }

    public function novaSolicitacao()
    {
        $varas = DB::table('judiciario_dados')->orderBy('vara', 'ASC')->orderBy('comarca', 'ASC')->get();

        return view('gep.escolta.novaSolicitacao', compact('varas'));
    }

    public function documentoEscolta($id){


        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $escolta = DB::table('gep')->where('id', $id)->get();

        $agps = DB::table('gep_agentes')->where('fk_gep', $id)->get();

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


        $relacaoInterno = DB::table('gep_internos')
            ->leftJoin('interno','interno.id','=','gep_internos.fk_interno')
            ->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional')
            ->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao')
            ->where('fk_gep',$id)
            ->orderBy('unidade.nome','ASC')
            ->orderBy('pavilhao.nome','ASC')
            ->get();

        $dadosInterno = array();

        $unidadesPavilhao = collect();

        foreach($relacaoInterno as $relacao)
        {
            $dadosInternos = (InternoController::getDadosPrincipaisInterno($relacao->fk_interno));
            $unidadeInterno = InternoController::getIdUnidadeInterno($relacao->fk_interno);
            $pavilhaoInterno = InternoController::getIdpavilhaoInterno($relacao->fk_interno);
            $fotosInterno = DB::table('foto')->where('fk_id', $relacao->fk_interno)->where('fk_tabela', 'interno')->orderBy('tipo','ASC')->get();

            if(count($fotosInterno) > 0)
            {
                $fotos = $fotosInterno;
            }else{
                $fotos = null;
            }

//            array_push($unidadesPavilhao, array(
//                'unidade' => $unidadeInterno,
//                'pavilhaoInterno' => $pavilhaoInterno
//            ));

            $unidadesPavilhao->push([
                'unidade' => $unidadeInterno,
                'pavilhaoInterno' => $pavilhaoInterno
            ]);

            $destino = EscoltaController::getDestinoEscolta($relacao->fk_escolta);
            if(is_numeric($destino))
            {
                $destino = JudiciarioController::getNomeVaraComarca($destino);
            }

            array_push($dadosInterno, array(
                "nome" => $dadosInternos[0]['nome'],
                "faccao" => $dadosInternos[0]['facNome'],
                "mae" => $dadosInternos[0]['mae'],
                "nascimento" => $dadosInternos[0]['data_nascimento'],
                "id" => $dadosInternos[0]['id'],
                "lideranca" => $dadosInternos[0]['lideranca'],
                "periculosidade" => $dadosInternos[0]['periculosidade'],
                "foto" => $fotos,
                // "foto" => $dadosInternos[0]['foto'],
                "sigla" => $dadosInternos[0]['sigla'],
                "idUnidade" => $unidadeInterno,
                "idPavilhao" => $pavilhaoInterno,
                "destinoInterno" => $destino,
            ));

        }

        $unidadesPavilhao = $unidadesPavilhao->unique('pavilhaoInterno');


        $nomeServidor = FacadeController::getNomeServidorCpf(Auth::user()->cpf)." (".Auth::user()->cpf.")";
        $unidade = UnidadesController::getNomeUp($lotacaoServidor);

        $comandanteDados = FacadeController::getDadosServidorPorMat($escolta[0]->vtr_comandante);
        $comandante = utf8_encode($comandanteDados[0]->nome)." (".$comandanteDados[0]->matricula.")";

        $motoristaDados = FacadeController::getDadosServidorPorMat($escolta[0]->vtr_motorista);
        $motorista = utf8_encode($motoristaDados[0]->nome)." (".$motoristaDados[0]->matricula.")";

        $vtr = $escolta[0]->vtr_placa;

        //dd($dadosInterno);

        //$dadosRodape = $escolta[0]->unidade." - Documentação de Escolta  <br> Gerador pelo Servidor ".$nomeServidor[0]->nome ."(".Auth::user()->cpf.")";


        $pdf = \PDF::loadView('gep.escolta.documento.pdfEscolta', compact('unidade','escolta','dadosInterno','arquivos','unidadesPavilhao'))
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 50)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('header-html', view('template.header.headerEscoltaGepPdf',compact('unidade','comandante','motorista','vtr','agentes')))
            //->setOption('footer-html', view('gep.escolta.documento.assinaturasPdfEscolta',compact('comandante','nomeServidor')))
            ->setOption ('footer-center', utf8_decode(date('\ d/m/Y\ ').' | Página [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');

        return $pdf->inline();

        //return view('escolta.show.show', compact('escolta','agentes','dadosInterno','arquivos'));


    }

    public function storeNovaSolicitacao(Request $request)
    {

        $created_AtEscolta = date("Y-m-d");
        if($request->get('tipoEscolta') == "audiencia")
        {
            $destino = $request->get('varaDestino');
        }
        if($request->get('tipoEscolta') == "diversas")
        {
            $destino = $request->get('destino');
        }

        $insertEscolta = DB::table('escolta')->insert([
                'unidade' => UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf)), // unidade que solicitou via siapen
                'tipo' => 'gep',
                'motivo' => $request->get('motivo'),
                'destino' => $destino,
                'documento' => $request->get('documento'),
                'responsavel' => $request->get('responsavel'),
                'data_saida' => Tdata::TdataMysql($request->get('data_saida')),
                'hora_saida' => $request->get('horarioEscolta'),
                'status' => 'Solicitada',
                'vtr_utilizacao' => 'S',
                'created_at' => $created_AtEscolta
            ]
        );

        if($insertEscolta) {
            $idEscolta = DB::getPdo()->lastInsertId();

            $internosEscolta = "";
            $idUpEscolta = "";
            // escolta_interno
            foreach ($request->get('internos') as $idInterno) {
                $insertInterno = DB::table('escolta_internos')->insert([
                        'fk_escolta' => $idEscolta,
                        'fk_interno' => $idInterno,
                        'up_anterior' => UnidadesController::getIdUP($idInterno),
                        'pav_anterior' => UnidadesController::getIdPavilhao($idInterno),
                        'ala_anterior' => UnidadesController::getIdAla($idInterno),
                        'cela_anterior' => UnidadesController::getIdCela($idInterno)
                    ]
                );
                if($insertInterno == false) {
                    return redirect()->route('gep.painelSolicitacoes')->with('alert-danger', 'Falha na operação, entre em contato com a '.config('app.ti'));
                }
                $internosEscolta = $internosEscolta."Prontuário: ".$idInterno." - ".InternoController::getInternoNome($idInterno).", ";
                $idUpEscolta = UnidadesController::getIdUP($idInterno);
            }
            $textoLog = "Solicitação de Escolta registrada em ".Tdata::TtimestampBR($created_AtEscolta).". Origem: ".UnidadesController::getNomeUp($idUpEscolta).". Destino: ".$request->get('destino').". Unidade que solicitou:".UnidadesController::getNomeUp(UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf))).". Responsável solicitado: ".UnidadesController::getNomeUp($request->get('responsavel')).". Internos: ";
            $textoLog = $textoLog.substr($internosEscolta,0,-2).".";
            LogController::log($idEscolta,"escolta","add", Auth::user()->cpf, $textoLog);

            // anexos
            if ($request->file('documentos')[0] != null) {

                $qtdArquivo = count($request->file('documentos'));

                for ($x = 0; $x < $qtdArquivo; $x++) {

                    $destinationPath = Constantes::ARQUIVOS_ESCOLTA . "/" . $idEscolta;

                    $name = "escolta" . $idEscolta . "_" . time() . "_" . $request->file('documentos')[$x]->getClientOriginalName();

                    $request->file('documentos')[$x]->storeAs($destinationPath, $name);
                    $insertArquivo = DB::table('escolta_arquivos')->insert(
                        [
                            'fk_escolta' => $idEscolta,
                            'nome_arquivo' => $name,
                            'created_at' => date("Y-m-d")
                        ]
                    );
                    if($insertArquivo == false) {
                        return redirect()->route('gep.painelSolicitacoes')->with('alert-danger', 'Falha na operação, entre em contato com a '.config('app.ti'));
                    }
                }
            }
            if($insertInterno){
                return redirect()->route('gep.painelSolicitacoes')->with('alert-success', 'Operação realizada com sucesso.');
            }
        }
        else{
            return redirect()->route('gep.painelSolicitacoes')->with('alert-danger', 'Falha na operação, entre em contato com a '.config('app.ti'));
        }
    }

    public function painelSolicitacoes(Request $request)
    {

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        /**
         * se não realizar uma busca
         * - Evita que mostre todos os resultados sem realizar uma busca
         */
        $escoltas = [];
        if(!strpos($url,"?"))
        {
//            $responsavel = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
//
//            $escoltas = DB::table('escolta')
//                ->where('responsavel', $responsavel)
//                ->orderBy('created_at', 'DESC')
//                ->get();
//
//            $escoltas = json_decode($escoltas, true);
//
//            foreach ($escoltas as $key => $escolta) {
//                $escoltas[$key]['created_at'] = Tdata::TdataBR($escoltas[$key]['created_at']);
//                $escoltas[$key]['responsavel'] = UnidadesController::getSiglaUp($escoltas[$key]['responsavel']);
//
//                if(!is_null($escolta['solicitante'])){
//                    $escoltas[$key]['solicitante'] = "Judiciário";
//                } else {
//                    $escoltas[$key]['solicitante'] = UnidadesController::getSiglaUp($escolta['unidade']);
//                }
//
//                $escoltas[$key]['qtd_internos'] = DB::table('escolta_internos')
//                    ->where('fk_escolta', $escolta['id'])
//                    ->count();
//            }
        }
        else {
            $escoltas = DB::table('escolta');
            if($request->get('responsavel') != ""){
                $escoltas = $escoltas->where('responsavel', $request->get('responsavel'));
            }
            if($request->get('status') != ""){
                $escoltas = $escoltas->where('status', $request->get('status'));
            }
            if($request->get('created_at_inicio') != ""){
                $escoltas = $escoltas->where('created_at', '>=', Tdata::TdataMysql($request->get('created_at_inicio')));
            }
            if($request->get('created_at_fim') != ""){
                $escoltas = $escoltas->where('created_at', '<=', Tdata::TdataMysql($request->get('created_at_fim')));
            }
            if($request->get('data_escolta_inicio') != ""){
                $escoltas = $escoltas->where('data_saida', '>=', Tdata::TdataMysql($request->get('data_escolta_inicio')));
            }
            if($request->get('data_escolta_fim') != ""){
                $escoltas = $escoltas->where('data_saida', '<=', Tdata::TdataMysql($request->get('data_escolta_fim')));
            }
            if($request->get('solicitante') == "poderJudiciario"){
                $escoltas = $escoltas->whereNotNull('solicitante');
            }
            elseif($request->get('solicitante') != ""){
                $escoltas = $escoltas->where('unidade', $request->get('solicitante'));
            }
            $escoltas = $escoltas->orderBy('id', 'DESC')->get();
            $escoltas = json_decode($escoltas, true);

            foreach ($escoltas as $key => $escolta) {
                $escoltas[$key]['created_at'] = Tdata::TdataBR($escoltas[$key]['created_at']);
                if(is_null($escoltas[$key]['data_saida'])){
                    $escoltas[$key]['data_saida'] = "Não Informado";
                }else{
                    $escoltas[$key]['data_saida'] = Tdata::TdataBR($escoltas[$key]['data_saida']);
                }

                if(is_numeric($escoltas[$key]['destino']))
                {
                    $escoltas[$key]['destino'] = JudiciarioController::getNomeVaraComarca($escoltas[$key]['destino']);
                }else{
                    $escoltas[$key]['destino'] = $escoltas[$key]['destino'];
                }
                $escoltas[$key]['responsavel'] = UnidadesController::getSiglaUp($escoltas[$key]['responsavel']);

                if(is_null($escoltas[$key]['solicitante'])){
                    $escoltas[$key]['solicitante'] = UnidadesController::getSiglaUp($escoltas[$key]['unidade']);
                }
                else{
                    $escoltas[$key]['solicitante'] = "Judiciário";
                }

                $escoltas[$key]['qtd_internos'] = DB::table('escolta_internos')
                    ->where('fk_escolta', $escolta['id'])
                    ->count();
            }
        }

        $lotacaoId = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $unidades = DB::table('unidade')->where('status', 1)->orderBy('sigla', 'ASC')->get();
        // passa a query para a paginacao "->appends..."
        $escoltas = collect($escoltas)->paginate(50)->appends($request->input());

        return view('gep.escolta.painelSolicitacoes', compact('escoltas','lotacaoId','unidades','url'));
    }

    public function confirmarSolicitacao(Request $request)
    {

        $idSolicitacao = $request->get('idSolicitacao');
        $status = DB::table('escolta')
            ->where('id', $idSolicitacao)
            ->update([
                'status' => 'Confirmada'
            ]);
        if($status) {
            $textoLog = "Escolta confirmada em ".Tdata::TtimestampBR(date("Y-m-d H:i:s")).".";
            LogController::log($idSolicitacao,"escolta","update", Auth::user()->cpf, $textoLog);
            return "true";
        }
        else {
            return "false";
        }
    }

    public function repassarSolicitacao(Request $request)
    {

        $idSolicitacao = $request->get('idSolicitacao');
        $responsavel = $request->get('responsavel');
        $ok = DB::table('escolta')
            ->where('id',$idSolicitacao)
            ->update([
                'responsavel' => $responsavel
            ]);
        if($ok){
            $textoLog = "Escolta repassada para ".UnidadesController::getNomeUp($responsavel)." em ".Tdata::TtimestampBR(date("Y-m-d H:i:s")).".";
            LogController::log($idSolicitacao,"escolta","update", Auth::user()->cpf, $textoLog);
            return "true";
        }
        else{
            return "false";
        }
    }

    public function cancelarSolicitacao(Request $request)
    {

        $idEscolta = $request->get('idSolicitacao');
        $obs = $request->get('obs');

        $gepFinalizadas = DB::table('gep')
            ->join('gep_internos','gep_internos.fk_gep','=','gep.id')
            ->where('gep_internos.fk_escolta',$idEscolta)
            ->where('gep.status', 'Finalizada')
            ->count();

        if($gepFinalizadas != 0){
            DB::table('escolta')
                ->where('id', $idEscolta)
                ->update([
                    'status' => 'Parcialmente Cancelada'
                ]);
            $textoLog = "Escolta parcialmente cancelada em ".Tdata::TtimestampBR(date("Y-m-d H:i:s")).".";
            LogController::log($idEscolta,"escolta","update", Auth::user()->cpf, $textoLog);
        }
        else{
            DB::table('escolta')
                ->where('id', $idEscolta)
                ->update([
                    'status' => 'Cancelada'
                ]);
            $textoLog = "Escolta cancelada em ".Tdata::TtimestampBR(date("Y-m-d H:i:s")).".";
            LogController::log($idEscolta,"escolta","update", Auth::user()->cpf, $textoLog);
        }

        $arquivosEscolta = DB::table('escolta_arquivos')
            ->where('fk_escolta',$idEscolta)
            ->get();

        foreach($arquivosEscolta as $arquivoEscolta){
            Storage::move(Constantes::ARQUIVOS_ESCOLTA . "/" . $arquivoEscolta->fk_escolta . "/" . $arquivoEscolta->nome_arquivo, Constantes::ARQUIVO_MORTO . "/" . $arquivoEscolta->nome_arquivo);
        }

        $idsGEP = DB::table('gep')
            ->join('gep_internos','gep_internos.fk_gep','=','gep.id')
            ->select('gep_internos.fk_escolta')
            ->where('gep.status','Pendente')
            ->where('gep_internos.fk_escolta',$idEscolta)
            ->get();
        $idsGEP = json_decode($idsGEP, true);

        foreach($idsGEP as $idGEP){
            DB::table('gep_agentes')
                ->where('fk_gep', $idGEP)
                ->delete();
            DB::table('gep_equipamentos')
                ->where('fk_gep', $idGEP)
                ->delete();
            DB::table('gep_internos')
                ->where('fk_gep', $idGEP)
                ->delete();
            DB::table('gep')
                ->where('id', $idGEP)
                ->delete();
        }

        DB::table('escolta')
            ->where('id', $idEscolta)
            ->update([
                'obs' => $obs
            ]);

        return "true";
    }

    public function painelEscoltas(Request $request)
    {

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        /**
         * se não realizar uma busca
         * - Evita que mostre todos os resultados sem realizar uma busca
         */
        $result = [];
        if(!strpos($url,"?")) {
//            $responsavel = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
//            $geps = DB::table('gep')
//                ->join('gep_internos', 'gep_internos.fk_gep', '=', 'gep.id')
//                ->join('escolta', 'escolta.id', '=', 'gep_internos.fk_escolta')
//                ->select('gep.id', 'gep.data_saida')
//                ->where('escolta.responsavel', $responsavel)
//                ->orderBy('gep.data_saida','DESC')
//                ->get();
//            $geps = json_decode($geps, true);
//            $result = [];
//            $idsGeps = [];
//            foreach ($geps as $key => $gep) {
//                array_push($idsGeps, $gep['id']);
//            }
//            $idsGeps = array_unique($idsGeps);
//            foreach ($idsGeps as $id) {
//                $gepTemp = DB::table('gep')
//                    ->where('gep.id', $id)
//                    ->get();
//                $gepTemp = json_decode($gepTemp, true)[0];
//                $result[$id]['id'] = $id;
//                $result[$id]['data_saida'] = Tdata::TdataBR($gepTemp['data_saida']);
//                $result[$id]['data_retorno'] = Tdata::TdataBR($gepTemp['data_retorno']);
//                $result[$id]['status'] = $gepTemp['status'];
//
//                $result[$id]['qtd_internos'] = DB::table('gep_internos')->where('fk_gep', $id)->count();
//
//                $fk_escolta = DB::table('gep_internos')->select('gep_internos.fk_escolta')->where('fk_gep', $id)->get();
//                $fk_escolta = json_decode($fk_escolta, true);
//
//                $result[$id]['unidade'] = "";
//                $result[$id]['responsavel'] = UnidadesController::getSiglaUp($responsavel);
//                $result[$id]['solicitante'] = "";
//                $result[$id]['destino'] = "";
//                $result[$id]['unidade_array'] = [];
//                $result[$id]['destino_array'] = [];
//                $result[$id]['solicitante_array'] = [];
//                foreach ($fk_escolta as $fkEscolta) {
//                    $escoltaTemp = DB::table('escolta')
//                        ->where('escolta.id', $fkEscolta)
//                        ->get();
//                    $escoltaTemp = json_decode($escoltaTemp, true)[0];
//                    array_push($result[$id]['unidade_array'], $escoltaTemp['unidade']);
//                    array_push($result[$id]['destino_array'], $escoltaTemp['destino']);
//                    array_push($result[$id]['solicitante_array'], $escoltaTemp['solicitante']);
//                }
//                $result[$id]['unidade_array'] = array_unique($result[$id]['unidade_array']);
//                foreach ($result[$id]['unidade_array'] as $unidade) {
//                    if ($result[$id]['unidade'] == "") $result[$id]['unidade'] = UnidadesController::getSiglaUp($unidade);
//                    else $result[$id]['unidade'] = $result[$id]['unidade'] . ", " . UnidadesController::getSiglaUp($unidade);
//                }
//                $result[$id]['destino_array'] = array_unique($result[$id]['destino_array']);
//                foreach ($result[$id]['destino_array'] as $destino) {
//                    if ($result[$id]['destino'] == "") $result[$id]['destino'] = $destino;
//                    else $result[$id]['destino'] = $result[$id]['destino'] . ", " . $destino;
//                }
//                $result[$id]['solicitante_array'] = array_unique($result[$id]['solicitante_array']);
//                foreach ($result[$id]['solicitante_array'] as $solicitante) {
//                    if ($result[$id]['solicitante'] == "") $result[$id]['solicitante'] = $solicitante;
//                    else $result[$id]['solicitante'] = $result[$id]['solicitante'] . ", " . $solicitante;
//                }
//            }
        }
        else {
//           // dd($request->get('seminternos'));
            if ($request->get('seminternos') == "S") {

                $geps = DB::table('gep')
                    ->select('gep.id', 'gep.data_saida');

                if ($request->get('responsavel') != "") {
                    $geps = $geps->where('escolta.responsavel', $request->get('responsavel'));
                }

                if ($request->get('data_saida') != "") {
                    $geps = $geps->where('gep.data_saida', '>=', Tdata::TdataMysql($request->get('data_saida')));
                }

                if ($request->get('data_retorno') != "") {
                    $geps = $geps->where('gep.data_retorno', '<=', Tdata::TdataMysql($request->get('data_retorno')));
                }

                if ($request->get('status') != "") {
                    $geps = $geps->where('gep.status', $request->get('status'));
                }

                if ($request->get('solicitante') == "poderJudiciario") {
                    $geps = $geps->whereNotNull('escolta.solicitante');
                }


                $geps = $geps->where('gep.unidade_gep', $lotacaoServidor);

                $geps = $geps->orderBy('gep.data_saida', 'DESC')->get();
                $geps = json_decode($geps, true);

                //dd($geps);

                $result = [];
                $idsGeps = [];
                foreach ($geps as $key => $gep) {
                    array_push($idsGeps, $gep['id']);
                }
                $idsGeps = array_unique($idsGeps);
                foreach ($idsGeps as $k => $id) {
                    $gepTemp = DB::table('gep')
                        ->where('gep.id', $id)
                        ->get();
                    $gepTemp = json_decode($gepTemp, true)[0];
                    $result[$id]['id'] = $id;
                    $result[$id]['data_saida'] = Tdata::TdataBR($gepTemp['data_saida']);
                    $result[$id]['data_retorno'] = Tdata::TdataBR($gepTemp['data_retorno']);
                    $result[$id]['status'] = $gepTemp['status'];

                    $result[$id]['qtd_internos'] = DB::table('gep_internos')->where('fk_gep', $id)->count();

                    $fk_escolta = DB::table('gep_internos')->select('gep_internos.fk_escolta')->where('fk_gep', $id)->get();
                    $fk_escolta = json_decode($fk_escolta, true);

                    $result[$id]['unidade'] = "";
                    $result[$id]['solicitante'] = "";
                    $result[$id]['responsavel'] = "";
                    $result[$id]['destino'] = "";
                    $result[$id]['unidade_array'] = [];
                    $result[$id]['destino_array'] = [];
                    $result[$id]['solicitante_array'] = [];
                    $result[$id]['responsavel_array'] = [];
                    foreach ($fk_escolta as $fkEscolta) {
                        $escoltaTemp = DB::table('escolta')
                            ->where('escolta.id', $fkEscolta)
                            ->get();
                        $escoltaTemp = json_decode($escoltaTemp, true)[0];

                        if (is_numeric($escoltaTemp['destino'])) {
                            $escoltaTemp['destino'] = JudiciarioController::getNomeVaraComarca($escoltaTemp['destino']);
                        }
                        array_push($result[$id]['unidade_array'], $escoltaTemp['unidade']);
                        array_push($result[$id]['destino_array'], $escoltaTemp['destino']);
                        array_push($result[$id]['solicitante_array'], $escoltaTemp['solicitante']);
                        array_push($result[$id]['responsavel_array'], $escoltaTemp['responsavel']);
                    }
                    $result[$id]['unidade_array'] = array_unique($result[$id]['unidade_array']);
                    foreach ($result[$id]['unidade_array'] as $unidade) {
                        if ($result[$id]['unidade'] == "") $result[$id]['unidade'] = UnidadesController::getSiglaUp($unidade);
                        else $result[$id]['unidade'] = $result[$id]['unidade'] . ", " . UnidadesController::getSiglaUp($unidade);
                    }
                    $result[$id]['destino_array'] = array_unique($result[$id]['destino_array']);
                    foreach ($result[$id]['destino_array'] as $destino) {
                        if ($result[$id]['destino'] == "") $result[$id]['destino'] = $destino;
                        else $result[$id]['destino'] = $result[$id]['destino'] . ", " . $destino;
                    }
                    $result[$id]['solicitante_array'] = array_unique($result[$id]['solicitante_array']);
                    foreach ($result[$id]['solicitante_array'] as $solicitante) {
                        if ($result[$id]['solicitante'] == "") $result[$id]['solicitante'] = $solicitante;
                        else $result[$id]['solicitante'] = $result[$id]['solicitante'] . ", " . $solicitante;
                    }
                    $result[$id]['responsavel_array'] = array_unique($result[$id]['responsavel_array']);
                    foreach ($result[$id]['responsavel_array'] as $responsavel) {
                        if ($result[$id]['responsavel'] == "") $result[$id]['responsavel'] = UnidadesController::getSiglaUp($responsavel);
                        else $result[$id]['responsavel'] = $result[$id]['responsavel'] . ", " . UnidadesController::getSiglaUp($responsavel);
                    }

                    $result[$id]['solicitante'] = null;
                    if($result[$id]['qtd_internos'] > 0)
                    {
                        //dd(key($result[$id]));
                        unset($result[$id]);
                    }


                }

                //dd($geps);
            }

        else{
                $geps = DB::table('gep')
                    ->join('gep_internos', 'gep_internos.fk_gep', '=', 'gep.id')
                    ->join('escolta', 'escolta.id', '=', 'gep_internos.fk_escolta')
                    ->select('gep.id', 'gep.data_saida');

                if ($request->get('responsavel') != "") {
                    $geps = $geps->where('escolta.responsavel', $request->get('responsavel'));
                }

                if ($request->get('data_saida') != "") {
                    $geps = $geps->where('gep.data_saida', '>=', Tdata::TdataMysql($request->get('data_saida')));
                }

                if ($request->get('data_retorno') != "") {
                    $geps = $geps->where('gep.data_retorno', '<=', Tdata::TdataMysql($request->get('data_retorno')));
                }

                if ($request->get('status') != "") {
                    $geps = $geps->where('gep.status', $request->get('status'));
                }

                if ($request->get('solicitante') == "poderJudiciario") {
                    $geps = $geps->whereNotNull('escolta.solicitante');
                } elseif ($request->get('solicitante') != "") {
                    $geps = $geps->where('escolta.unidade', $request->get('solicitante'));
                }

                $geps = $geps->orderBy('gep.data_saida', 'DESC')->get();
                $geps = json_decode($geps, true);

                $result = [];
                $idsGeps = [];
                foreach ($geps as $key => $gep) {
                    array_push($idsGeps, $gep['id']);
                }
                $idsGeps = array_unique($idsGeps);
                foreach ($idsGeps as $id) {
                    $gepTemp = DB::table('gep')
                        ->where('gep.id', $id)
                        ->get();
                    $gepTemp = json_decode($gepTemp, true)[0];
                    $result[$id]['id'] = $id;
                    $result[$id]['data_saida'] = Tdata::TdataBR($gepTemp['data_saida']);
                    $result[$id]['data_retorno'] = Tdata::TdataBR($gepTemp['data_retorno']);
                    $result[$id]['status'] = $gepTemp['status'];

                    $result[$id]['qtd_internos'] = DB::table('gep_internos')->where('fk_gep', $id)->count();

                    $fk_escolta = DB::table('gep_internos')->select('gep_internos.fk_escolta')->where('fk_gep', $id)->get();
                    $fk_escolta = json_decode($fk_escolta, true);

                    $result[$id]['unidade'] = "";
                    $result[$id]['solicitante'] = "";
                    $result[$id]['responsavel'] = "";
                    $result[$id]['destino'] = "";
                    $result[$id]['unidade_array'] = [];
                    $result[$id]['destino_array'] = [];
                    $result[$id]['solicitante_array'] = [];
                    $result[$id]['responsavel_array'] = [];
                    foreach ($fk_escolta as $fkEscolta) {
                        $escoltaTemp = DB::table('escolta')
                            ->where('escolta.id', $fkEscolta)
                            ->get();
                        $escoltaTemp = json_decode($escoltaTemp, true)[0];

                        if (is_numeric($escoltaTemp['destino'])) {
                            $escoltaTemp['destino'] = JudiciarioController::getNomeVaraComarca($escoltaTemp['destino']);
                        }
                        array_push($result[$id]['unidade_array'], $escoltaTemp['unidade']);
                        array_push($result[$id]['destino_array'], $escoltaTemp['destino']);
                        array_push($result[$id]['solicitante_array'], $escoltaTemp['solicitante']);
                        array_push($result[$id]['responsavel_array'], $escoltaTemp['responsavel']);
                    }
                    $result[$id]['unidade_array'] = array_unique($result[$id]['unidade_array']);
                    foreach ($result[$id]['unidade_array'] as $unidade) {
                        if ($result[$id]['unidade'] == "") $result[$id]['unidade'] = UnidadesController::getSiglaUp($unidade);
                        else $result[$id]['unidade'] = $result[$id]['unidade'] . ", " . UnidadesController::getSiglaUp($unidade);
                    }
                    $result[$id]['destino_array'] = array_unique($result[$id]['destino_array']);
                    foreach ($result[$id]['destino_array'] as $destino) {
                        if ($result[$id]['destino'] == "") $result[$id]['destino'] = $destino;
                        else $result[$id]['destino'] = $result[$id]['destino'] . ", " . $destino;
                    }
                    $result[$id]['solicitante_array'] = array_unique($result[$id]['solicitante_array']);
                    foreach ($result[$id]['solicitante_array'] as $solicitante) {
                        if ($result[$id]['solicitante'] == "") $result[$id]['solicitante'] = $solicitante;
                        else $result[$id]['solicitante'] = $result[$id]['solicitante'] . ", " . $solicitante;
                    }
                    $result[$id]['responsavel_array'] = array_unique($result[$id]['responsavel_array']);
                    foreach ($result[$id]['responsavel_array'] as $responsavel) {
                        if ($result[$id]['responsavel'] == "") $result[$id]['responsavel'] = UnidadesController::getSiglaUp($responsavel);
                        else $result[$id]['responsavel'] = $result[$id]['responsavel'] . ", " . UnidadesController::getSiglaUp($responsavel);
                    }
                }
            }
        }
        //dd($result);

        $geps = $result;
        $geps = collect($geps)->paginate(50)->appends($request->input());
        $unidades = DB::table('unidade')->where('status',1)->orderBy('sigla', 'ASC')->get();
        return view('gep.escolta.painelEscoltas', compact('geps','unidades','url'));
    }

    public function cancelarEscolta(Request $request)
    {

        $idGep = $request->get('idGep');
        DB::table('gep_agentes')
            ->where('fk_gep', $idGep)
            ->delete();
        DB::table('gep_equipamentos')
            ->where('fk_gep', $idGep)
            ->delete();
        DB::table('gep_internos')
            ->where('fk_gep', $idGep)
            ->delete();
        DB::table('gep')
            ->where('id', $idGep)
            ->delete();
        $textoLog = "Escolta (gep) cancelada em ".Tdata::TtimestampBR(date("Y-m-d H:i:s")).".";
        LogController::log($idGep,"gep","update", Auth::user()->cpf, $textoLog);
        return "true";
    }

    public function finalizarEscolta(Request $request)
    {

        $idGep = $request->get('idGep');

        $verificaData = DB::table('gep')->where('id', $idGep)->get();
        if($verificaData[0]->data_retorno == null || $verificaData[0]->hora_retorno == null)
        {
            return "false";

        }else {

            DB::table('gep')
                ->where('id', $idGep)
                ->update([
                    'status' => 'Finalizada'
                ]);


            // se todas os gep da escolta estiverem finalizada e não houver internos da escolta sem gep, finalizar a escolta


            // ids das solicitações que tem presos nesse carro
            $idsEscolta = DB::table('gep_internos')
                ->select('fk_escolta')
                ->where('fk_gep', $idGep)
                ->get();
            $idsEscolta = json_decode($idsEscolta, true);

            //percorre todas as solicitações e verifica se pode finalizá-las
            foreach($idsEscolta as $id){
                // conta a quantidade de presos finalizados em cada carro da solicitação do foreach
                $qtdGepFinalizados = DB::table('gep_internos')
                    ->join('gep', 'gep.id', '=', 'gep_internos.fk_gep')
                    ->where('gep_internos.fk_escolta', $id['fk_escolta'])
                    ->where('gep.status', 'Finalizada')
                    ->count();

                // conta a quantidade de internos na escolta
                $qtdEscoltaSolicitados = DB::table('escolta_internos')
                    ->join('escolta', 'escolta.id', '=', 'escolta_internos.fk_escolta')
                    ->where('escolta.id', $id['fk_escolta'])
                    ->count();

                if ($qtdGepFinalizados == $qtdEscoltaSolicitados) {

                    DB::table('escolta')
                        ->where('id', $id['fk_escolta'])
                        ->update([
                            'status' => 'Finalizada'
                        ]);

                    $textoLog = "Escolta (solicitação gep) finalizada em " . Tdata::TtimestampBR(date("Y-m-d H:i:s")) . ".";
                    LogController::log($id['fk_escolta'], "escolta", "update", Auth::user()->cpf, $textoLog);
                }
            }

            $textoLog = "Escolta (gep) finalizada em " . Tdata::TtimestampBR(date("Y-m-d H:i:s")) . ".";
            LogController::log($idGep, "gep", "update", Auth::user()->cpf, $textoLog);
            return "true";
        }
    }

    public function novaEscolta()
    {


        $responsavel = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));


        $internosPendentes = DB::table('escolta_internos')
            ->join('escolta', 'escolta.id','=','escolta_internos.fk_escolta')
            ->leftJoin('gep_internos', function ($query) {
                $query->on('gep_internos.fk_escolta', '=', 'escolta_internos.fk_escolta')
                    ->whereNull('gep_internos.fk_gep');
            })
            ->leftJoin('gep', function ($query) {
                $query->on('gep.id','=','gep_internos.fk_gep')
                    ->whereNull('gep.status');
            })
            ->select('escolta_internos.fk_escolta','escolta_internos.fk_interno','escolta.destino','escolta.created_at','escolta.data_saida','escolta.hora_saida')
            ->where('escolta.status','Confirmada')
            ->where('escolta.responsavel',$responsavel)
            ->get();
        $internosPendentes = json_decode($internosPendentes, true);

        //dd($internosPendentes);

        $escoltasPendentes = [];
        foreach($internosPendentes as $key=>$interno){
            $dadosFoto = DB::table('foto')
                ->where('fk_id', $interno['fk_interno'])
                ->where('fk_tabela', 'interno')
                ->where('tipo','principal')->get();
            $dadosFoto = json_decode($dadosFoto,true);

            if($dadosFoto == []){
                $dadosFoto[0]['tipo'] = "";
                $dadosFoto[0]['legenda'] = "";
                $dadosFoto[0]['created_at'] = "";
                $dadosFoto[0]['updated_at'] = "";
            }

            $fk_escoltaTemp = $internosPendentes[$key]['fk_escolta'];
            $hora_solicitado = Tdata::TdataBR($internosPendentes[$key]['data_saida'])." - ".$internosPendentes[$key]['hora_saida'];
            $destino = $internosPendentes[$key]['destino'];
            if(is_numeric($destino))
            {
                $destino = JudiciarioController::getNomeVaraComarca($destino);
            }


            $internosPendentes[$key] = InternoController::getDadosPrincipaisInterno($internosPendentes[$key]['fk_interno'])[0];
            $internosPendentes[$key]['destino'] = $destino;
            $internosPendentes[$key]['hora_solicitado'] = $hora_solicitado;
            $internosPendentes[$key]['fk_escolta'] = $fk_escoltaTemp;
            $internosPendentes[$key]['faccao_nome'] = InternoController::getNomeFaccao($internosPendentes[$key]['faccao']);
            $internosPendentes[$key]['localizacao'] = InternoController::getLotacaoInterno($internosPendentes[$key]['id']);
            $internosPendentes[$key]['fototipo'] = $dadosFoto[0]['tipo'];
            $internosPendentes[$key]['fotolegenda'] = $dadosFoto[0]['legenda'];
            $internosPendentes[$key]['fotodatainserida'] = $dadosFoto[0]['created_at'];
            $internosPendentes[$key]['fotodataalterada'] = $dadosFoto[0]['updated_at'];
            array_push($escoltasPendentes, $fk_escoltaTemp);
        };
        $escoltasPendentes = array_unique($escoltasPendentes);
//        dd($internosPendentes);

        $gepInternos = DB::table('gep_internos')
            ->whereIn('fk_escolta',$escoltasPendentes)
            ->get();
        $gepInternos = json_decode($gepInternos, true);
//        dd($gepInternos);

        foreach($internosPendentes as $key=>$internoPendente) {
            foreach($gepInternos as $gepInterno) {
                if ($internoPendente['fk_escolta'] == $gepInterno['fk_escolta'] && $internoPendente['id'] == $gepInterno['fk_interno']) {
                    unset($internosPendentes[$key]);
                }
            }
        }

        foreach($internosPendentes as $key=>$item){
            $keys = array_keys($internosPendentes[$key]);
            foreach($keys as $k) {
                if (is_null($internosPendentes[$key][$k]) || $internosPendentes[$key][$k] == "") {
                    $internosPendentes[$key][$k] = "Não Informado";
                }
            }
        }

        return view('gep.escolta.novaEscolta', compact('internosPendentes','destino'));
    }

    public function internosPendentesEscoltaPdf(){

        $responsavel = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $unidade = FacadeController::getLotacaoServidor(Auth::user()->cpf);

        $internosPendentes = DB::table('escolta_internos')
            ->join('escolta', 'escolta.id','=','escolta_internos.fk_escolta')
            ->leftJoin('gep_internos', function ($query) {
                $query->on('gep_internos.fk_escolta', '=', 'escolta_internos.fk_escolta')
                    ->whereNull('gep_internos.fk_gep');
            })
            ->leftJoin('gep', function ($query) {
                $query->on('gep.id','=','gep_internos.fk_gep')
                    ->whereNull('gep.status');
            })
            ->select('escolta_internos.fk_escolta','escolta_internos.fk_interno','escolta.destino','escolta.created_at','escolta.data_saida','escolta.hora_saida')
            ->where('escolta.status','Confirmada')
            ->where('escolta.responsavel',$responsavel)
            ->get();
        $internosPendentes = json_decode($internosPendentes, true);

        //dd($internosPendentes);
        $escoltasPendentes = [];
        foreach($internosPendentes as $key=>$interno){

            $fk_escoltaTemp = $internosPendentes[$key]['fk_escolta'];
            $hora_solicitado = $internosPendentes[$key]['hora_saida'];
//            $hora_solicitado = Tdata::TdataBR($internosPendentes[$key]['data_saida'])." - ".$internosPendentes[$key]['hora_saida'];
            $destino = $internosPendentes[$key]['destino'];
            $internosPendentes[$key] = InternoController::getDadosPrincipaisInterno($internosPendentes[$key]['fk_interno'])[0];
            if(is_numeric($destino))
            {
                $destino = JudiciarioController::getNomeVaraComarca($destino);
            }
            $internosPendentes[$key]['destino'] = $destino;
            $internosPendentes[$key]['hora_solicitado'] = $hora_solicitado;
            $internosPendentes[$key]['fk_escolta'] = $fk_escoltaTemp;
            $internosPendentes[$key]['faccao_nome'] = InternoController::getNomeFaccao($internosPendentes[$key]['faccao']);
            $internosPendentes[$key]['localizacao'] = InternoController::getLotacaoInterno($internosPendentes[$key]['id']);
            array_push($escoltasPendentes, $fk_escoltaTemp);
        };
        $escoltasPendentes = array_unique($escoltasPendentes);

        $gepInternos = DB::table('gep_internos')
            ->whereIn('fk_escolta',$escoltasPendentes)
            ->get();
        $gepInternos = json_decode($gepInternos, true);

        foreach($internosPendentes as $key=>$internoPendente) {
            foreach($gepInternos as $gepInterno) {
                if ($internoPendente['fk_escolta'] == $gepInterno['fk_escolta'] && $internoPendente['id'] == $gepInterno['fk_interno']) {
                    unset($internosPendentes[$key]);
                }
            }
        }

        foreach($internosPendentes as $key=>$item){
            $keys = array_keys($internosPendentes[$key]);
            foreach($keys as $k) {
                if (is_null($internosPendentes[$key][$k]) || $internosPendentes[$key][$k] == "") {
                    $internosPendentes[$key][$k] = "Não Informado";
                }
            }
        }

        $sort = [
            ['column' => 'destino', 'order' => 'asc'],
            ['column' => 'hora_solicitado', 'order' => 'asc'],
            ['column' => 'localizacao', 'order' => 'asc'],
        ];

        $internosPendentes = collect($internosPendentes);
        $internosPendentes = self::multiPropertySort($internosPendentes, $sort);
        $internosPendentes = json_decode(json_encode($internosPendentes), FALSE);

        // dd($internosPendentes);

        $pdf = \PDF::loadView('gep.escolta.documento.pdfInternosPendentesDeEscolta', compact('internosPendentes'))
            ->setOption('orientation', 'landscape')
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 40)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('header-html', view('template.header.headerGepInternosPendentesEscoltaPdf',compact('unidade')))
            ->setOption ('footer-center', utf8_decode('Gerado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');

        return $pdf->inline();

    }

    public function storeNovaEscolta(Request $request)
    {
        $lotacao = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

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
        $created_AtEscolta = date("Y-m-d");
        $insertGep = DB::table('gep')->insert([
                'status' => 'Pendente',
                'data_saida' => Tdata::TdataMysql($request->get('data_saida')),
                'hora_saida' => $request->get('hora_saida'),
                'data_retorno' => $dataRetorno,
                'hora_retorno' => $horaRetorno,
                'vtr_placa' => $request->get('vtr_placa'),
                'vtr_comandante' => $request->get('vtrComandanteHidden'),
                'vtr_motorista' => $request->get('vtrMotoristaHidden'),
                'unidade_gep' => $lotacao,
                'created_at' => $created_AtEscolta
            ]
        );

        $idGep = DB::getPdo()->lastInsertId();

        if($request->get('agentesHidden')) {
            foreach ($request->get('agentesHidden') as $agente) {
                $insertGepAgentes = DB::table('gep_agentes')->insert([
                        'fk_gep' => $idGep,
                        'matricula' => $agente
                    ]
                );
            }
        }
        if($request->get('hiddenEquipamentoCautela')) {
            foreach ($request->get('hiddenEquipamentoCautela') as $equipamento) {
                $equip = explode('/', $equipamento);
                $qtd = $equip[1];
                if ($qtd == 0) $qtd = 1;
                $insertGepEquipamentos = DB::table('gep_equipamentos')->insert([
                        'fk_gep' => $idGep,
                        'fk_equipamento' => $equip[0],
                        'quantidade' => $qtd
                    ]
                );
            }
        }

        $internosEscolta = "";
        if($request->get('internosCelaCheck')) {
            foreach ($request->get('internosCelaCheck') as $internoId_fkescolta) {
                $internoId_fkescolta = explode('/', $internoId_fkescolta);
                $internoId = $internoId_fkescolta[0];
                $idEscolta = $internoId_fkescolta[1];

                $insertGepInternos = DB::table('gep_internos')->insert([
                        'fk_gep' => $idGep,
                        'fk_interno' => $internoId,
                        'fk_escolta' => $idEscolta
                    ]
                );

                $internosEscolta = $internosEscolta."Prontuário: ".$internoId." - ".InternoController::getInternoNome($internoId).", ";
            }
        }

        $textoLog = "Registro de Escolta (gep) em ".Tdata::TtimestampBR($created_AtEscolta).". Saída: ".Tdata::TdataBR($request->get('data_saida'))." ".$request->get('hora_saida').", Retorno: ".$dataRetorno." ".$horaRetorno.". Responsável pela escolta: ".UnidadesController::getNomeUp($request->get('responsavel')).". Internos: ";
        $textoLog = $textoLog.substr($internosEscolta,0,-2).".";
        LogController::log($idGep,"gep","add", Auth::user()->cpf, $textoLog);

        return redirect()->route('gep.painelEscoltas')->with('alert-success', 'Operação realizada com sucesso.');
    }

    public function dadosJudiciario()
    {

        $dados = DB::table('judiciario_dados')->get();
        return view('gep.judiciario.gerenciamento', compact('dados'));
    }

    public function dadosJudiciarioInserir()
    {
        return view('gep.judiciario.dadosJudiciarioInserir');
    }

    public function dadosJudiciarioEditar($id)
    {

        $dados = DB::table('judiciario_dados')->where('id',$id)->get();
        $dados = json_decode($dados,true)[0];
        return view('gep.judiciario.dadosJudiciarioInserir',compact('dados'));
    }

    public function storeDadosJudiciario(Request $request)
    {

        $insertDadosJudiciario = DB::table('judiciario_dados')->insert([
                'vara' => $request->get('vara'),
                'juiz' => $request->get('magistrado'),
                'qualificacao' => $request->get('qualificacao'),
                'comarca' => $request->get('comarca')
            ]
        );
        if($insertDadosJudiciario){
            return redirect()->route('gep.dadosJudiciario')->with('alert-success', 'Operação realizada com sucesso.');
        }
        else{
            return redirect()->route('gep.dadosJudiciario')->with('alert-danger', 'Falha na operação, entre em contato com a '.config('app.ti'));
        }
    }

    public function updateDadosJudiciario(Request $request)
    {

        $updateDadosJudiciario = DB::table('judiciario_dados')->where('id',$request->get('id'))->update([
                'vara' => $request->get('vara'),
                'juiz' => $request->get('magistrado'),
                'qualificacao' => $request->get('qualificacao'),
                'comarca' => $request->get('comarca')
            ]
        );
        if($updateDadosJudiciario){
            return redirect()->route('gep.dadosJudiciario')->with('alert-success', 'Operação realizada com sucesso.');
        }
        else{
            return redirect()->route('gep.dadosJudiciario')->with('alert-danger', 'Falha na operação, entre em contato com a '.config('app.ti'));
        }
    }

    public function editar($id)
    {
        $responsavel = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dados = DB::table('gep')->where('id',$id)->get();

        if($dados[0]->status != "Pendente")
        {
            return redirect()->back()->with('alert-danger', 'Apenas escoltas com o status pendente são liberadas para alteração');

        }


        if($dados[0]->unidade_gep != $responsavel)
        {
            return redirect()->back()->with('alert-danger', 'Você não faz parte do GEP que fez o cadastro da escolta, por isso não consegue editá-la');

        }



        /**
         * lista de internos pendentes para escolta
         */
        $internosPendentes = DB::table('escolta_internos')
            ->join('escolta', 'escolta.id','=','escolta_internos.fk_escolta')
            ->leftJoin('gep_internos', function ($query) {
                $query->on('gep_internos.fk_escolta', '=', 'escolta_internos.fk_escolta')
                    ->whereNull('gep_internos.fk_gep');
            })
            ->leftJoin('gep', function ($query) {
                $query->on('gep.id','=','gep_internos.fk_gep')
                    ->whereNull('gep.status');
            })
            ->select('escolta_internos.fk_escolta','escolta_internos.fk_interno','escolta.destino','escolta.created_at','escolta.data_saida','escolta.hora_saida')
            ->where('escolta.status','Confirmada')
            ->where('escolta.responsavel',$responsavel)
            ->get();
        $internosPendentes = json_decode($internosPendentes, true);

        //dd($internosPendentes);

        $escoltasPendentes = [];
        foreach($internosPendentes as $key=>$interno){

            $fk_escoltaTemp = $internosPendentes[$key]['fk_escolta'];
            $hora_solicitado = Tdata::TdataBR($internosPendentes[$key]['data_saida'])." - ".$internosPendentes[$key]['hora_saida'];
            $destino = $internosPendentes[$key]['destino'];
            if(is_numeric($destino))
            {
                $destino = JudiciarioController::getNomeVaraComarca($destino);
            }


            $internosPendentes[$key] = InternoController::getDadosPrincipaisInterno($internosPendentes[$key]['fk_interno'])[0];
            $internosPendentes[$key]['destino'] = $destino;
            $internosPendentes[$key]['hora_solicitado'] = $hora_solicitado;
            $internosPendentes[$key]['fk_escolta'] = $fk_escoltaTemp;
            $internosPendentes[$key]['faccao_nome'] = InternoController::getNomeFaccao($internosPendentes[$key]['faccao']);
            $internosPendentes[$key]['localizacao'] = InternoController::getLotacaoInterno($internosPendentes[$key]['id']);
            array_push($escoltasPendentes, $fk_escoltaTemp);
        };

        $escoltasPendentes = array_unique($escoltasPendentes);
//        dd($internosPendentes);

        $gepInternos = DB::table('gep_internos')
            ->whereIn('fk_escolta',$escoltasPendentes)
            ->get();
        $gepInternos = json_decode($gepInternos, true);
//        dd($gepInternos);

        foreach($internosPendentes as $key=>$internoPendente) {
            foreach($gepInternos as $gepInterno) {
                if ($internoPendente['fk_escolta'] == $gepInterno['fk_escolta'] && $internoPendente['id'] == $gepInterno['fk_interno']) {
                    unset($internosPendentes[$key]);
                }
            }
        }

        foreach($internosPendentes as $key=>$item){
            $keys = array_keys($internosPendentes[$key]);
            foreach($keys as $k) {
                if (is_null($internosPendentes[$key][$k]) || $internosPendentes[$key][$k] == "") {
                    $internosPendentes[$key][$k] = "Não Informado";
                }
            }
        }
        // fim da lista de internos pendentes para escolta

        /**
         * internos na escolta
         */
        $internosEscolta = DB::table('gep_internos')
            ->join('escolta', 'escolta.id','=','gep_internos.fk_escolta')
            ->join('gep', 'gep.id','=','gep_internos.fk_gep')
            ->select('gep_internos.fk_escolta','gep_internos.fk_interno','escolta.destino','escolta.created_at','escolta.data_saida','escolta.hora_saida')
            ->where('escolta.status','Confirmada')
            ->where('gep_internos.fk_gep',$id)
            ->where('escolta.responsavel',$responsavel)
            ->get();

//        dd($internosEscolta);

        $internosEscolta = $internosEscolta->unique('fk_interno');
        $internosEscolta = json_decode($internosEscolta, true);


        //$internosEscolta = [];
        foreach($internosEscolta as $key=>$interno){

            $fk_escoltaTemp = $internosEscolta[$key]['fk_escolta'];
            $hora_solicitado = Tdata::TdataBR($internosEscolta[$key]['data_saida'])." - ".$internosEscolta[$key]['hora_saida'];
            $destino = $internosEscolta[$key]['destino'];
            if(is_numeric($destino))
            {
                $destino = JudiciarioController::getNomeVaraComarca($destino);
            }

            $internosEscolta[$key] = InternoController::getDadosPrincipaisInterno($internosEscolta[$key]['fk_interno'])[0];
            $internosEscolta[$key]['destino'] = $destino;
            $internosEscolta[$key]['hora_solicitado'] = $hora_solicitado;
            $internosEscolta[$key]['fk_escolta'] = $fk_escoltaTemp;
            $internosEscolta[$key]['faccao_nome'] = InternoController::getNomeFaccao($internosEscolta[$key]['faccao']);
            $internosEscolta[$key]['localizacao'] = InternoController::getLotacaoInterno($internosEscolta[$key]['id']);
            array_push($escoltasPendentes, $fk_escoltaTemp);
        };

        $escoltasPendentes = array_unique($escoltasPendentes);


        $gepInternos = DB::table('gep_internos')
            ->whereIn('fk_escolta',$escoltasPendentes)
            ->get();
        $gepInternos = json_decode($gepInternos, true);

        foreach($internosEscolta as $key=>$item){
            $keys = array_keys($internosEscolta[$key]);
            foreach($keys as $k) {
                if (is_null($internosEscolta[$key][$k]) || $internosEscolta[$key][$k] == "") {
                    $internosEscolta[$key][$k] = "Não Informado";
                }
            }
        }

        //dd($internosEscolta);

        $sort = [
            ['column' => 'nome', 'order' => 'asc'],
            ['column' => 'destino', 'order' => 'asc'],

        ];


        $internosEscolta = collect($internosEscolta);
        $internosEscolta = self::multiPropertySort($internosEscolta, $sort);


        $agentes = DB::table('gep_agentes')
            ->where('fk_gep',$id)
            ->get();
        $agentes = json_decode($agentes,true);

        foreach($agentes as $key=>$agente){
            $idGep = $agente['fk_gep'];
            $matricula = $agente['matricula'];
            $i = FacadeController::getDadosServidorPorMat($matricula)[0];
            $agentes[$key]['nome'] = utf8_encode($i->nome);
            $agentes[$key]['lotacao'] = utf8_encode(FacadeController::getLotacaoServidor($i->cpf));
        }


        $dados = DB::table('gep')->where('id',$id)->get();

        $dados[0]->data_saida = Tdata::TdataBR($dados[0]->data_saida);
        $dados[0]->data_retorno = Tdata::TdataBR($dados[0]->data_retorno);

        $dataRetorno = $dados[0]->data_retorno;
        $comandanteVtr = FacadeController::getNomeServidor($dados[0]->vtr_comandante);
        $motoristaVtr = FacadeController::getNomeServidor($dados[0]->vtr_motorista);

        // dd($internosEscolta);


        return view('gep.escolta.editarEscoltas.editar', compact('id','dados','dataRetorno','internosPendentes','internosEscolta','agentes','comandanteVtr','motoristaVtr'));

    }

    public function editarStore(Request $request, $id)
    {


//        dd($request->input());

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $diferencas = array();

        $dataRetorno = $request->get('data_retorno');

        $dados = DB::table('gep')->where('id',$id)->get();

        if($dados[0]->status != "Pendente")
        {
            return redirect()->back()->with('alert-danger', 'Apenas escoltas com o status pendente são liberadas para alteração');

        }

        if($dados[0]->unidade_gep != $lotacaoServidor)
        {
            return redirect()->back()->with('alert-danger', 'Você não faz parte do GEP que cadastrou a escolta, por isso não consegue editá-la');

        }

        // *******************  Incluir internos da Escolta *********************************

        $internosEscolta = "";
        if($request->get('internosCelaCheck')) {
            foreach ($request->get('internosCelaCheck') as $internoId_fkescolta) {
                $internoId_fkescolta = explode('/', $internoId_fkescolta);
                $internoId = $internoId_fkescolta[0];
                $idEscolta = $internoId_fkescolta[1];

                $insertGepInternos = DB::table('gep_internos')->insert([
                        'fk_gep' => $id,
                        'fk_interno' => $internoId,
                        'fk_escolta' => $idEscolta
                    ]
                );

                $internosEscolta = $internosEscolta."Prontuário: ".$internoId." - ".InternoController::getInternoNome($internoId).", ";
            }
        }

        $textoLog = "Interno incluído na Escolta da FTPEN (".$id.") em ".Tdata::TtimeStampHojeBr().".";
        $textoLog = $textoLog.substr($internosEscolta,0,-2).".";
        array_push($diferencas, $textoLog);
        LogController::log($id,"gep_internos","add", Auth::user()->cpf, $textoLog);


        // *******************  Remover internos da Escolta *********************************

        $internosRemover = $request->get('internosRemover');

        if(isset($internosRemover)){
            foreach ($internosRemover as $idInternoRemover) {

                $nomeInterno = InternoController::getInternoNome($idInternoRemover);
                $alteracoes = "Exclusão de interno da Escolta (".$id.") : <i><strong>".$nomeInterno."(".$idInternoRemover.")<br>Data: ".Tdata::TdataHoje()."<br>**********<br>";

                LogController::log($id,"gep_internos","delete", Auth::user()->cpf, $alteracoes);
                array_push($diferencas, $alteracoes);

                $deleteInterno = DB::table('gep_internos')->where('fk_gep', $id)->where('fk_interno', $idInternoRemover)->delete();

            }

        }


        // *******************  Remover agentes da escolta  *********************************

        $agentesRemover = $request->get('agentesRemover');

        if(isset($agentesRemover)){
            foreach ($agentesRemover as $matriculaAgente) {

                $dadosAgenteGep = DB::table('gep_agentes')->where('fk_gep', $id)->where('matricula', $matriculaAgente)->get();

                $nomeServidor = FacadeController::getNomeServidor($matriculaAgente);
                $alteracoes = "Exclusão de servidor da Escolta (".$id.") : <i><strong>".$nomeServidor."(".$matriculaAgente.")<br>Data: ".Tdata::TdataHoje()."<br>**********<br>";

                LogController::log($dadosAgenteGep[0]->id,"gep_agentes","delete", Auth::user()->cpf, $alteracoes);
                array_push($diferencas, $alteracoes);

                $deleteAgente = DB::table('gep_agentes')->where('fk_gep', $id)->where('matricula', $matriculaAgente)->delete();

            }

        }

        // *******************  Incluir agentes na escolta  *********************************

        $servidores = $request->get('agentesHidden');
        if(isset($servidores))
        {

            $qtdAgentes = count($request->get('agentesHidden'));
            $status = $request->get('status');

            for ($x = 0; $x < $qtdAgentes; $x++) {

                $nomeServidor = FacadeController::getNomeServidor($servidores[$x]);

                $verificaServidor = DB::table('gep_agentes')
                    ->where('fk_gep', $id)
                    ->where('matricula', $servidores[$x])
                    ->get();

                if (empty(json_decode($verificaServidor, true))) {
                    $insertServidores = DB::table('gep_agentes')->insert([
                            'fk_gep' => $id,
                            'matricula' => $servidores[$x],
                        ]

                    );

                    $alteracoes = "Inserção de servidor na Escolta(".$id."), Nome: <i><strong>".$nomeServidor."(".$servidores[$x].")<br>**********<br>";
                    array_push($diferencas, $alteracoes);


                }
            }

        }


        //-------------------- LOG -------------------------------------

        $dadosBD = $dados;
        //json_decode($dadosBD[0]);

        $dataForm = array(
            'vtr_placa' => $request->get('vtr_placa'),
            'data_saida' => Tdata::TdataMysql($request->get('data_saida')),
            'hora_saida' => $request->get('hora_saida'),
            'data_retorno' => Tdata::TdataMysql($request->get('data_retorno')),
            'hora_retorno' => $request->get('hora_retorno'),
            'vtr_comandante' => $request->get('vtrComandanteHidden'),
            'vtr_motorista' => $request->get('vtrMotoristaHidden'),
        );


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


        // *******************  Update GEP  ********************************
        $updateGep = DB::table('gep')->where('id', $id)->update([
                'data_saida' => Tdata::TdataMysql($request->get('data_saida')),
                'hora_saida' => $request->get('hora_saida'),
                'data_retorno' => Tdata::TdataMysql($request->get('data_retorno')),
                'hora_retorno' => $request->get('hora_saida'),
                'vtr_placa' => $request->get('vtr_placa'),
                'vtr_comandante' => $request->get('vtrComandanteHidden'),
                'vtr_motorista' => $request->get('vtrMotoristaHidden'),
                'obs' => $request->get('obs')." em ".Tdata::TtimeStampHojeBr()." **** ".$dados[0]->obs,
                'updated_at' => Tdata::TtimeStampHojeMysql()
            ]
        );




//        $updateEscolta = DB::table('gep')->where('id',$id)->update([
//                'data_retorno' => $dtRetorno,
//                'hora_retorno' => $request->get('hora_retorno'),
//                'updated_at' => date("Y-m-d"),
//            ]
//        );

        if ($updateGep) {

            $textoLog = implode(" ", $diferencas);
            LogController::log($id,"gep","update", Auth::user()->cpf, $textoLog);

            return redirect()->route('gep.painelEscoltas')->with('alert-success', 'Escolta alterada com sucesso!');

        }else{

            return redirect('gep.painelEscoltas')->with('alert-danger', 'Ocorreu um problema, entre em contato com a '.config('app.ti'));

        }


    }

    public function show($id)
    {
        $gep = DB::table('gep')
            ->where('id',$id)
            ->get();
        $gep[0]->data_saida = Tdata::TdataBR($gep[0]->data_saida);
        $gep[0]->data_retorno = Tdata::TdataBR($gep[0]->data_retorno);
        $gep[0]->data_retorno = Tdata::TdataBR($gep[0]->data_retorno);
        $gep[0]->vtr_comandante = FacadeController::getNomeServidor($gep[0]->vtr_comandante);
        $gep[0]->vtr_motorista = FacadeController::getNomeServidor($gep[0]->vtr_motorista);
        $internosCountEscolta =  DB::table('gep_internos')->where('fk_gep',$id)->count();

        if($internosCountEscolta > 0) {
            $idEscolta = DB::table('gep_internos')
                ->where('fk_gep', $id)
                ->get()[0]->fk_escolta;
            $gep[0]->responsavel = UnidadesController::getSiglaUp(DB::table('escolta')
                ->where('id', $idEscolta)
                ->get()[0]->responsavel);
        }else{
            $gep[0]->responsavel = UnidadesController::getSiglaUp($gep[0]->unidade_gep);
        }

        $agentes = DB::table('gep_agentes')
            ->where('fk_gep',$id)
            ->get();
        $agentes = json_decode($agentes,true);

        foreach($agentes as $key=>$agente){
            $idGep = $agente['fk_gep'];
            $matricula = $agente['matricula'];
            $i = FacadeController::getDadosServidorPorMat($matricula)[0];
            $agentes[$key]['nome'] = utf8_encode($i->nome);
            $agentes[$key]['lotacao'] = utf8_encode(FacadeController::getLotacaoServidor($i->cpf));
        }

        $eqps = DB::table('gep_equipamentos')->where('fk_gep', $id)->get();
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


        if($internosCountEscolta > 0) {

            $internos = DB::table('gep_internos')
                ->where('fk_gep', $id)
                ->get();
            $internos = json_decode($internos, true);
            foreach ($internos as $key => $interno) {
                $dadosFoto = DB::table('foto')
                    ->where('fk_id', $interno['fk_interno'])
                    ->where('fk_tabela', 'interno')
                    ->where('tipo', 'principal')->get();
                $dadosFoto = json_decode($dadosFoto, true);


                if ($dadosFoto == []) {
                    $dadosFoto[0]['tipo'] = "";
                    $dadosFoto[0]['legenda'] = "";
                    $dadosFoto[0]['created_at'] = "";
                    $dadosFoto[0]['updated_at'] = "";
                }

                $idGep = $interno['fk_gep'];
                $idEscolta = $interno['fk_escolta'];
                $i = InternoController::getDadosPrincipaisInterno($interno['fk_interno']);
                $internos[$key]['nome'] = $i[0]['nome'];
                $internos[$key]['periculosidade'] = $i[0]['periculosidade'];
                $internos[$key]['unidade_prisional'] = $i[0]['unidade_prisional'];
                $internos[$key]['pavilhao'] = $i[0]['pavilhao'];
                $internos[$key]['ala'] = $i[0]['ala'];
                $internos[$key]['cela'] = $i[0]['cela'];
                $internos[$key]['faccao'] = InternoController::getNomeFaccao($i[0]['faccao']);
                $internos[$key]['lideranca'] = $i[0]['lideranca'];
                $internos[$key]['foto'] = $i[0]['foto'];
                $internos[$key]['fototipo'] = $dadosFoto[0]['tipo'];
                $internos[$key]['fotolegenda'] = $dadosFoto[0]['legenda'];
                $internos[$key]['fotodatainserida'] = $dadosFoto[0]['created_at'];
                $internos[$key]['fotodataalterada'] = $dadosFoto[0]['updated_at'];

                $internos[$key]['lotacao'] = InternoController::getLotacaoInterno($interno['fk_interno']);
            }
        }else{
            $internos = [];
        }

        // dados Log
        if(count(LogController::getDadosLog($id, "gep")) != 0){
            $dadosLog = LogController::getDadosLog($id, "gep");
            $userLog = FacadeController::getDadosServidor($dadosLog[0]->cpf_usuario,"nome");
            $textoLog = "Registrado por: ".$userLog[0]->nome." (".$dadosLog[0]->cpf_usuario.") em ".Tdata::TtimestampBR($dadosLog[0]->created_at);
        }
        else{
            $textoLog = "Sem log registrado.";
        }


        return view('gep.show', compact('gep','agentes','equipamentos','internos','textoLog'));
    }

    public function showEscolta($id)
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

            if(is_numeric($esc->destino))
            {
                $esc->destino = JudiciarioController::getNomeVaraComarca($esc->destino);
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

        return view('gep.escolta.showEscolta', compact('escolta','viaturas','agentes','equipamentos','VTR','dadosInterno','arquivos','textoLog'));

    }

    public function arquivosEscolta($id)
    {

        $dados = DB::table('escolta_arquivos')
            ->leftJoin('escolta','escolta.id','=','escolta_arquivos.fk_escolta')
            ->where('fk_escolta', $id)->get();

        foreach($dados as $arquivo)
        {
            if(is_numeric($arquivo->destino))
            {
                $arquivo->destino = JudiciarioController::getNomeVaraComarca($arquivo->destino);
            }
            $arquivo->data_saida = Tdata::TdataBR($arquivo->data_saida);
            $arquivo->data_retorno = Tdata::TdataBR($arquivo->data_retorno);
            $arquivo->created_at = Tdata::TdataBR($arquivo->created_at);
            $arquivo->unidade = UnidadesController::getNomeUp($arquivo->unidade);
        }

        return view('gep.escolta.escoltaArquivos', compact('dados'));

    }

    public static function pautaDia(Request $request)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        /**
         * se não realizar uma busca
         * - Evita que mostre todos os resultados sem realizar uma busca
         */
        $result = [];
        if(!strpos($url,"?")) {

            $dados = DB::table("gep_equipe")->where('unidade', $lotacaoServidor)->where('data', Tdata::TdataHojeMysql())->orderBy('placa_vtr', 'ASC')->get();

            $equipeDados = array();

            foreach ($dados as $equipe) {
                $agentes = array();

                $agentesEquipe = DB::table("gep_equipe_servidor")->where('fk_equipe', $equipe->id)->get();

                foreach ($agentesEquipe as $agnts) {
                    $dadosServidor = FacadeController::getDadosServidorPorMat($agnts->matricula);

                    array_push($agentes, array(
                        "id" => $dadosServidor[0]->id,
                        "nome" => utf8_encode($dadosServidor[0]->nome),
                        "lotacao" => FacadeController::getLotacaoServidor($dadosServidor[0]->cpf, "S"),
                        "foto" => config('app.rh')."/storage/app/arquivos/servidores/" . $dadosServidor[0]->id . "/foto/" . $dadosServidor[0]->id . ".jpeg",
                        "matricula" => $dadosServidor[0]->matricula,
                        "cpf" => $dadosServidor[0]->cpf,
                        "status" => $agnts->status
                    ));
                }

                $qtdTotal = 0;

                $unidades = DB::table('unidade')->where('status', 1)->get();
                $qtdInternoUnidade = array();
                foreach ($unidades as $unidade) {


                    $internos = DB::table('gep_internos')
                        ->leftJoin('gep', 'gep.id', '=', 'gep_internos.fk_gep')
                        ->leftJoin('interno', 'interno.id', '=', 'gep_internos.fk_interno')
                        ->where('gep.vtr_placa', $equipe->placa_vtr)
                        ->where('interno.unidade_prisional', $unidade->id)
                        ->where('gep.data_saida', Tdata::TdataHojeMysql())
                        ->count();


                    if ($internos > 0) {
                        array_push($qtdInternoUnidade, array(
                            'unidade' => $unidade->sigla,
                            'qtd' => $internos,
                        ));

                        $qtdTotal = $qtdTotal + $internos;
                    }
                }
                array_push($equipeDados, array(
                    "regiao" => $equipe->regiao,
                    "placa_vtr" => $equipe->placa_vtr,
                    "nome_equipe" => $equipe->nome_equipe,
                    "vtr_comandante" => FacadeController::getNomeServidor($equipe->vtr_comandante),
                    "vtr_motorista" => FacadeController::getNomeServidor($equipe->vtr_motorista),
                    "obs" => $equipe->obs,
                    "agentes" => $agentes,
                    "internos" => $qtdInternoUnidade,
                    "total" => $qtdTotal,
                    "id" => $equipe->id,

                ));
            }


            $sort = [
                ['column' => 'regiao', 'order' => 'asc'],
            ];

            $equipeDados = collect($equipeDados);
            $equipeDados = self::multiPropertySort($equipeDados, $sort);

            $dataBusca = Tdata::TdataHoje();

            return view('gep.escolta.pautaDoDia', compact('equipeDados', 'dataBusca'));

        }else{


            $dados = DB::table("gep_equipe")->where('unidade', $lotacaoServidor)->where('data', Tdata::TdataMysql($request->get('dataBuscar')))->orderBy('placa_vtr', 'ASC')->get();

            $equipeDados = array();

            foreach ($dados as $equipe) {
                $agentes = array();

                $agentesEquipe = DB::table("gep_equipe_servidor")->where('fk_equipe', $equipe->id)->get();

                foreach ($agentesEquipe as $agnts) {
                    $dadosServidor = FacadeController::getDadosServidorPorMat($agnts->matricula);

                    array_push($agentes, array(
                        "id" => $dadosServidor[0]->id,
                        "nome" => utf8_encode($dadosServidor[0]->nome),
                        "lotacao" => FacadeController::getLotacaoServidor($dadosServidor[0]->cpf, "S"),
                        "foto" => config('app.rh')."/storage/app/arquivos/servidores/" . $dadosServidor[0]->id . "/foto/" . $dadosServidor[0]->id . ".jpeg",
                        "matricula" => $dadosServidor[0]->matricula,
                        "cpf" => $dadosServidor[0]->cpf,
                        "status" => $agnts->status
                    ));
                }

                $qtdTotal = 0;

                $unidades = DB::table('unidade')->where('status', 1)->get();
                $qtdInternoUnidade = array();
                foreach ($unidades as $unidade) {


                    $internos = DB::table('gep_internos')
                        ->leftJoin('gep', 'gep.id', '=', 'gep_internos.fk_gep')
                        ->leftJoin('interno', 'interno.id', '=', 'gep_internos.fk_interno')
                        ->where('gep.vtr_placa', $equipe->placa_vtr)
                        ->where('interno.unidade_prisional', $unidade->id)
                        ->where('gep.data_saida', Tdata::TdataMysql($request->get('dataBuscar')))
                        ->count();


                    if ($internos > 0) {
                        array_push($qtdInternoUnidade, array(
                            'unidade' => $unidade->sigla,
                            'qtd' => $internos,
                        ));

                        $qtdTotal = $qtdTotal + $internos;
                    }
                }
                array_push($equipeDados, array(
                    "regiao" => $equipe->regiao,
                    "placa_vtr" => $equipe->placa_vtr,
                    "nome_equipe" => $equipe->nome_equipe,
                    "vtr_comandante" => FacadeController::getNomeServidor($equipe->vtr_comandante),
                    "vtr_motorista" => FacadeController::getNomeServidor($equipe->vtr_motorista),
                    "obs" => $equipe->obs,
                    "agentes" => $agentes,
                    "internos" => $qtdInternoUnidade,
                    "total" => $qtdTotal,
                    "id" => $equipe->id,


                ));
            }


            $sort = [
                ['column' => 'regiao', 'order' => 'asc'],
            ];

            $equipeDados = collect($equipeDados);
            $equipeDados = self::multiPropertySort($equipeDados, $sort);

            $dataBusca = $request->get('dataBuscar');

            return view('gep.escolta.pautaDoDia', compact('equipeDados','dataBusca'));

        }
    }

    public function pautaDiaStore(Request $request)
    {
        $verificaPlacaVTR = DB::table('gep_equipe')->where('placa_vtr', $request->get('vtr'))->where('data',  Tdata::TdataMysql($request->get('dataEquipe')))->get();

        if(count($verificaPlacaVTR) > 0)
        {
            return redirect()->back()->with('alert-danger', 'VTR cadastrada anteriormente para outra equipe!');

        }

        //dd($request->input());

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $diferencas = array();

        $insertEquipe = DB::table('gep_equipe')->insert([
            'data' => Tdata::TdataMysql($request->get('dataEquipe')),
            'unidade' => $lotacaoServidor,
            'regiao' => $request->get('regiao'),
            'placa_vtr' => $request->get('vtr'),
            'nome_equipe' => $request->get('equipeNome'),
            'vtr_comandante' => $request->get('vtrComandanteHidden'),
            'vtr_motorista' => $request->get('vtrMotoristaHidden'),
            'obs' => $request->get('obs'),
        ]);
        $idInsertEquipe = DB::getPdo()->lastInsertId();


        $servidores = $request->get('agentesHidden');
        if(isset($servidores))
        {

            $qtdAgentes = count($request->get('agentesHidden'));
            $status = $request->get('status');

            for ($x = 0; $x < $qtdAgentes; $x++) {

//                $verificaServidor = DB::table('gep_equipe_servidor')->where('data', $dataInicioPlantao)->where('matricula', $servidores[$x])->where('unidade', $lotacaoServidor)->get();
//
//                if (!empty(json_decode($verificaServidor, true))) {
//                    $updateEquipe = DB::table('equipe_dia')->where('data', $dataInicioPlantao)->where('matricula', $servidores[$x])->where('unidade', $lotacaoServidor)->update([
//                        'lotacaoUnidade' => $lotacaoUnidade[$x],
//                        'status' => $status[$x],
//                        'escala' => $escala[$x],
//                        'funcao' => $funcao[$x],
//
//                    ]);
//                }

                //if (empty(json_decode($verificaServidor, true))) {
                $insertServidores = DB::table('gep_equipe_servidor')->insert([
                        'fk_equipe' => $idInsertEquipe,
                        'matricula' => $servidores[$x],
                        'status' => $status[$x],
                        'created_at' => Tdata::TtimeStampHojeMysql(),
                    ]

                );

                $nomeServidor = FacadeController::getNomeServidor($servidores[$x]);
                $alteracoes = "Inserção de servidor na equipe do dia do GEP: <i><strong>".$nomeServidor."(".$servidores[$x].")<br>Status: ".$status[$x]."</i></strong><br>**********<br>";
                array_push($diferencas, $alteracoes);

                //}
            }

        }

        if ($idInsertEquipe) {


            $textoLog = implode(" ", $diferencas);
            if(!empty($textoLog))
            {
                LogController::log($idInsertEquipe,"gep_equipe","add", Auth::user()->cpf, $textoLog);
            }

            return redirect()->route('gep.pautaDia')->with('alert-success', 'Servidor(es) inseridos com sucesso!');

        }else{

            return redirect('gep.pautaDia')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }

    public function equipeDiaEditar($id)
    {

        $dados = DB::table("gep_equipe")->where('id', $id)->get();

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        if($dados[0]->unidade != $lotacaoServidor)
        {
            return redirect()->route('gep.pautaDia')->with('alert-danger', 'Equipe de outra unidade, você não tem permissão para editá-la!');

        }

        if($dados[0]->data != date("Y-m-d"))
        {
            return redirect()->route('gep.pautaDia')->with('alert-danger', 'Só é possível editar a equipe do dia!');

        }

        $equipeDados = array();

        foreach ($dados as $equipe) {
            $agentes = array();

            $agentesEquipe = DB::table("gep_equipe_servidor")->where('fk_equipe', $equipe->id)->get();

            foreach ($agentesEquipe as $agnts) {
                $dadosServidor = FacadeController::getDadosServidorPorMat($agnts->matricula);

                array_push($agentes, array(
                    "id" => $dadosServidor[0]->id,
                    "nome" => utf8_encode($dadosServidor[0]->nome),
                    "lotacao" => FacadeController::getLotacaoServidor($dadosServidor[0]->cpf, "S"),
                    "foto" => file_get_contents(config('app.rh').'/webservice/fotos-servidores/1wBd794bG5BFkHU71xltg6Gbo2Tx6a4y/'.$dadosServidor[0]->cpf),
                    "matricula" => $dadosServidor[0]->matricula,
                    "cpf" => $dadosServidor[0]->cpf,
                    "status" => $agnts->status
                ));
            }


            array_push($equipeDados, array(
                "regiao" => $equipe->regiao,
                "placa_vtr" => $equipe->placa_vtr,
                "nome_equipe" => $equipe->nome_equipe,
                "vtr_comandante" => utf8_encode(FacadeController::getNomeServidor($equipe->vtr_comandante)),
                "vtr_comandante_matricula" => $equipe->vtr_comandante,
                "vtr_motorista" => utf8_encode(FacadeController::getNomeServidor($equipe->vtr_motorista)),
                "vtr_motorista_matricula" => $equipe->vtr_motorista,
                "obs" => $equipe->obs,
                "agentes" => $agentes,
                "id" => $equipe->id,
                "data" => Tdata::TdataBR($equipe->data),


            ));
        }


//dd($equipeDados);

        return view('gep.escolta.equipeDiaForm', compact('equipeDados'));

    }

    public function equipeDiaEditarStore($id, Request $request)
    {
        //dd($id, $request->input());


        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $diferencas = array();

        $insertEquipe = DB::table('gep_equipe')->where('id', $id)->update([
            'data' => Tdata::TdataMysql($request->get('dataEquipe')),
            'unidade' => $lotacaoServidor,
            'regiao' => $request->get('regiao'),
            'placa_vtr' => $request->get('vtr'),
            'nome_equipe' => $request->get('equipeNome'),
            'vtr_comandante' => $request->get('vtrComandanteHidden'),
            'vtr_motorista' => $request->get('vtrMotoristaHidden'),
            'obs' => $request->get('obs'),
        ]);

        $agentesRemover = $request->get('agentesRemover');

        if(isset($agentesRemover)){
            foreach ($agentesRemover as $matriculaAgente) {

                $dadosEquipe = DB::table('gep_equipe_servidor')->where('fk_equipe', $id)->where('matricula', $matriculaAgente)->get();

                $nomeServidor = FacadeController::getNomeServidor($matriculaAgente);
                $nomeUnidade = UnidadesController::getNomeUp($lotacaoServidor);
                $alteracoes = "Exclusão de servidor na equipe do dia ". $request->get('dataEquipe').": <i><strong>".$nomeServidor."(".$matriculaAgente.")<br>Data: ".Tdata::TdataHoje()."<br>Unidade: ".$nomeUnidade."</i></strong><br>**********<br>";

                LogController::log($dadosEquipe[0]->id,"gep_equipe_servidor","delete", Auth::user()->cpf, $alteracoes);

                $deleteAgente = DB::table('gep_equipe_servidor')->where('fk_equipe', $id)->where('matricula', $matriculaAgente)->delete();

            }

        }



        $servidores = $request->get('agentesHidden');
        if(isset($servidores))
        {


            $qtdAgentes = count($request->get('agentesHidden'));
            $status = $request->get('status');

            for ($x = 0; $x < $qtdAgentes; $x++) {

                $nomeServidor = FacadeController::getNomeServidor($servidores[$x]);

                $verificaServidor = DB::table('gep_equipe_servidor')
                    ->leftJoin('gep_equipe','gep_equipe.id','=','gep_equipe_servidor.fk_equipe')
                    ->where('gep_equipe.data', Tdata::TdataMysql($request->get('dataEquipe')))
                    ->where('gep_equipe_servidor.matricula', $servidores[$x])
                    ->where('unidade', $lotacaoServidor)
                    ->get();

                if (!empty(json_decode($verificaServidor, true))) {
                    $updateEquipe = DB::table('gep_equipe_servidor')->where('fk_equipe', $id)->where('matricula', $servidores[$x])->update([
                        'fk_equipe' => $id,
                        'matricula' => $servidores[$x],
                        'status' => $status[$x],

                    ]);
                }

                if (empty(json_decode($verificaServidor, true))) {
                    $insertServidores = DB::table('gep_equipe_servidor')->insert([
                            'fk_equipe' => $id,
                            'matricula' => $servidores[$x],
                            'status' => $status[$x],
                            'created_at' => Tdata::TtimeStampHojeMysql(),
                        ]

                    );
                    $alteracoes = "Inserção de servidor na equipe do dia ".$request->get('dataEquipe')." do GEP: <i><strong>".$nomeServidor."(".$servidores[$x].")<br>Status: ".$status[$x]."</i></strong><br>**********<br>";
                    array_push($diferencas, $alteracoes);


                }
            }

        }

        if ($id) {


            $textoLog = implode(" ", $diferencas);
            if(!empty($textoLog))
            {
                LogController::log($id,"gep_equipe","update", Auth::user()->cpf, $textoLog);
            }

            return redirect()->route('gep.pautaDia')->with('alert-success', 'Equipe Alterada com sucesso!');

        }else{

            return redirect('gep.pautaDia')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }



    }

    public function pautaDiaPdf($data)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dados = DB::table("gep_equipe")->where('unidade', $lotacaoServidor)->where('data', Tdata::TdataMysql(str_replace("-","/",$data)))->orderBy('placa_vtr', 'ASC')->get();

        $equipeDados = array();

        foreach ($dados as $equipe) {
            $agentes = array();

            $agentesEquipe = DB::table("gep_equipe_servidor")->where('fk_equipe', $equipe->id)->get();

            foreach($agentesEquipe as $agnts)
            {
                $dadosServidor = FacadeController::getDadosServidorPorMat($agnts->matricula);

                array_push($agentes, array(
                    "id" => $dadosServidor[0]->id,
                    "nome" => utf8_encode($dadosServidor[0]->nome),
                    "lotacao" => FacadeController::getLotacaoServidor($dadosServidor[0]->cpf, "S"),
                    "foto" => config('app.rh')."/storage/app/arquivos/servidores/" . $dadosServidor[0]->id . "/foto/" . $dadosServidor[0]->id . ".jpeg",
                    "matricula" => $dadosServidor[0]->matricula,
                    "cpf" => $dadosServidor[0]->cpf,
                    "status" => $agnts->status
                ));
            }

            $qtdTotal = 0;

            $unidades = DB::table('unidade')->where('status', 1)->get();
            $qtdInternoUnidade = array();
            foreach($unidades as $unidade)
            {


                $internos = DB::table('gep_internos')
                    ->leftJoin('gep', 'gep.id','=','gep_internos.fk_gep')
                    ->leftJoin('interno','interno.id','=','gep_internos.fk_interno')
                    ->where('gep.vtr_placa', $equipe->placa_vtr)
                    ->where('interno.unidade_prisional', $unidade->id)
                    ->where('gep.data_saida',  Tdata::TdataMysql(str_replace("-","/",$data)))
                    ->count();


                if($internos > 0) {
                    array_push($qtdInternoUnidade, array(
                        'unidade' => $unidade->sigla,
                        'qtd' => $internos,
                    ));

                    $qtdTotal = $qtdTotal + $internos;
                }
            }
            array_push($equipeDados, array(
                "regiao" => $equipe->regiao,
                "placa_vtr" => $equipe->placa_vtr,
                "nome_equipe" => $equipe->nome_equipe,
                "vtr_comandante" => FacadeController::getNomeServidor($equipe->vtr_comandante),
                "vtr_motorista" => FacadeController::getNomeServidor($equipe->vtr_motorista),
                "obs" => $equipe->obs,
                "agentes" => $agentes,
                "internos" => $qtdInternoUnidade,
                "total" => $qtdTotal,

            ));
        }


        $sort = [
            ['column' => 'regiao', 'order' => 'asc'],
        ];

        $equipeDados = collect($equipeDados);
        $equipeDados = self::multiPropertySort($equipeDados, $sort);

        $unidade = FacadeController::getLotacaoServidor(Auth::user()->cpf);
        $data = Tdata::TdataHoje();

        $pdf = \PDF::loadView('gep.escolta.documento.pdfPautaDoDia', compact('equipeDados'))
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 40)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('header-html', view('template.header.headerGepPautaDoDia',compact('unidade','data')))
            ->setOption ('footer-center', utf8_decode('Gerado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');

        return $pdf->inline();

    }

    public function documentos(Request $request)
    {
        $data = $request->get('dataDocumento');

        if(is_null($data))
        {
            $dataBusca = Tdata::TdataHoje();
        }else{
            $dataBusca = $data;
        }

        return view('gep.escolta.documentos', compact('dataBusca'));
    }
    public function docMissoes($data)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dataDefinida = Tdata::TdataMysql(str_replace("-","/",$data));

        $escoltas = DB::table('gep')
            ->where('data_saida', $dataDefinida)->select('id')->get();

        $idEscolta = json_decode($escoltas, true);
        $idEscolta = array_column($idEscolta, 'id');

        $internos = array();
        //$missoes = array();
        $doc = array();

        //dd( self::getDadosEscolta(1112));

        $internos_escolta = DB::table('gep_internos')
            ->leftJoin('escolta','escolta.id','=','gep_internos.fk_escolta')
            ->where('escolta.responsavel', $lotacaoServidor)
            ->whereIn('gep_internos.fk_gep', $idEscolta)
            ->select('gep_internos.*')
            ->get();

        foreach($internos_escolta as $interno)
        {
            $dadosEscolta = self::getDadosEscolta($interno->fk_gep, $interno->fk_interno);

            if(is_numeric($dadosEscolta[0]->destino))
            {
                $destinoNome = JudiciarioController::getNomeVaraComarca($dadosEscolta[0]->destino);
                $qualificacao = JudiciarioController::getQualificacaoVara($dadosEscolta[0]->destino);
                $juiz = JudiciarioController::getJuizVara($dadosEscolta[0]->destino);
                $comarca = JudiciarioController::getComarcaVara($dadosEscolta[0]->destino);
            }else{
                $destinoNome = $dadosEscolta[0]->destino;
                $qualificacao = "";
                $juiz = "";
                $comarca = "";
            }

            $dataExtenso = substr($dadosEscolta[0]->data_saida, 8,2)." de ".Tdata::getMesExt(substr($dadosEscolta[0]->data_saida, 5,2))." de ".substr($dadosEscolta[0]->data_saida, 0,4);

            array_push($internos, array(
                'fk_gep' => $interno->fk_gep,
                'fk_escolta' => $interno->fk_escolta,
                'destino' => $destinoNome,
                'destinoCode' => $dadosEscolta[0]->destino,
                'qualificacao' => $qualificacao,
                'juiz' => $juiz,
                'comarca' => $comarca,
                'data_saida' => $dadosEscolta[0]->data_saida,
                'data_extenso' => $dataExtenso,
                'horario_saida' => $dadosEscolta[0]->horario_saida,
                'interno' => $interno->fk_interno,
                'documento' => $dadosEscolta[0]->documento,
                'vtr' => $dadosEscolta[0]->vtr_placa,
                'nomeInterno' => InternoController::getInternoNome($interno->fk_interno),
                'unidadeInterno' => InternoController::getIdUnidadeInterno($interno->fk_interno),
                'localizacao' => InternoController::getLotacaoInterno($interno->fk_interno)
            ));



        }

        //dd(count($internos));

        $missoes = array();

        foreach($internos as $array){
            $missoes[$array["destinoCode"]][$array["unidadeInterno"]][$array["horario_saida"]][] = $array;
        }




        $pdf = \PDF::loadView('gep.escolta.documento.oficioMissaoPdf', compact('missoes'))
            ->setOption('margin-bottom', 30)
            ->setOption('margin-top', 40)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('header-html', view('template.header.headerGepOficioPdf'))
            ->setOption ('footer-html', view('template.footer.footerGepOficioPdf'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');

        return $pdf->inline();


        // dd($missoes);
    }

    public function docMissoesCapaMissao($data)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        //dd(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dataDefinida = Tdata::TdataMysql(str_replace("-","/",$data));

        $equipeDiaDados = DB::table('gep_equipe')
            ->where('gep_equipe.unidade', $lotacaoServidor)
            ->where('gep_equipe.data', $dataDefinida)
            ->get();

        $equipes = array();
        $equipeDia = json_decode($equipeDiaDados, true);
        foreach($equipeDia as $array){
            $equipes[$array["regiao"]][] = $array;
        }

        //dd($equipes);

        $escoltas = DB::table('gep')
            ->where('data_saida', $dataDefinida)->select('id')->get();

        $idEscolta = json_decode($escoltas, true);
        $idEscolta = array_column($idEscolta, 'id');

        //dd($idEscolta);
        $internos = array();

        $internos_escolta = DB::table('gep_internos')
            ->leftJoin('escolta','escolta.id','=','gep_internos.fk_escolta')
            ->where('escolta.responsavel', $lotacaoServidor)
            ->whereIn('gep_internos.fk_gep', $idEscolta)
            ->select('gep_internos.*')
            ->get();

        //dd($internos_escolta);

        foreach($internos_escolta as $interno)
        {
            $dadosEscolta = self::getDadosEscolta($interno->fk_gep, $interno->fk_interno);

            if(is_numeric($dadosEscolta[0]->destino))
            {
                $destinoNome = JudiciarioController::getNomeVaraComarca($dadosEscolta[0]->destino);
                $qualificacao = JudiciarioController::getQualificacaoVara($dadosEscolta[0]->destino);
                $juiz = JudiciarioController::getJuizVara($dadosEscolta[0]->destino);
                $comarca = JudiciarioController::getComarcaVara($dadosEscolta[0]->destino);
            }else{
                $destinoNome = $dadosEscolta[0]->destino;
                $qualificacao = "";
                $juiz = "";
                $comarca = "";
            }

            $dataExtenso = substr($dadosEscolta[0]->data_saida, 8,2)." de ".Tdata::getMesExt(substr($dadosEscolta[0]->data_saida, 5,2))." de ".substr($dadosEscolta[0]->data_saida, 0,4);

            array_push($internos, array(
                'fk_gep' => $interno->fk_gep,
                'fk_escolta' => $interno->fk_escolta,
                'destino' => $destinoNome,
                'destinoCode' => $dadosEscolta[0]->destino,
                'qualificacao' => $qualificacao,
                'juiz' => $juiz,
                'comarca' => $comarca,
                'data_saida' => $dadosEscolta[0]->data_saida,
                'data_extenso' => $dataExtenso,
                'horario_saida' => $dadosEscolta[0]->horario_saida,
                'interno' => $interno->fk_interno,
                'documento' => $dadosEscolta[0]->documento,
                'vtr' => $dadosEscolta[0]->vtr_placa,
                'regiao' => self::getRegiaoEquipeInterno($interno->fk_gep, $interno->fk_interno, $dataDefinida),
                'nomeInterno' => InternoController::getInternoNome($interno->fk_interno),
                'unidadeInterno' => InternoController::getIdUnidadeInterno($interno->fk_interno),
                'localizacao' => InternoController::getLotacaoInterno($interno->fk_interno),
            ));



        }

        //dd($internos);

        $sort = [
            ['column' => 'destino', 'order' => 'asc'],
            ['column' => 'horario_saida', 'order' => 'asc'],
            ['column' => 'unidadeInterno', 'order' => 'asc'],
            ['column' => 'nomeInterno', 'order' => 'asc'],
        ];

        // dd($internos);

        $internos = collect($internos);
        $internos = self::multiPropertySort($internos, $sort);
        $internos = json_decode($internos, true);

        $internosMissao = array();

        foreach($internos as $array){
            $internosMissao[$array["regiao"]][] = $array;
        }

        $missoes = array();

        foreach($equipeDiaDados as $equipe)
        {
            $servidores = array();

            $servidoresDados = DB::table('gep_equipe_servidor')->where('fk_equipe', $equipe->id)->get();

            foreach($servidoresDados as $servidoresDado)
            {
                if($equipe->vtr_comandante != $servidoresDado->matricula && $equipe->vtr_motorista != $servidoresDado->matricula) {

                    array_push($servidores, array(
                        'nome' => FacadeController::getNomeServidor($servidoresDado->matricula) . " (" . $servidoresDado->matricula . ")"
                    ));
                }
            }

            array_push($missoes, array(
                'idEquipe' => $equipe->id,
                'vtr' => $equipe->placa_vtr,
                'vtr_comandante' => FacadeController::getNomeServidor($equipe->vtr_comandante)." (".$equipe->vtr_comandante.")",
                'vtr_motorista' => FacadeController::getNomeServidor($equipe->vtr_motorista)." (".$equipe->vtr_motorista.")",
                'dataEquipe' => $equipe->data,
                'regiao' => $equipe->regiao,
                'nome_equipe' => $equipe->nome_equipe,
                'obs' => $equipe->obs,
                'servidores' => $servidores,
            ));
        }

        $capaMissao = array();

        foreach($missoes as $array){
            $capaMissao[$array["regiao"]][$array["nome_equipe"]][] = $array;
        }

        //dd($capaMissao, $internosMissao);


        $pdf = \PDF::loadView('gep.escolta.documento.capaMissaoPdf', compact('capaMissao','internosMissao'))
            ->setOption('margin-bottom', 30)
            ->setOption('margin-top', 40)
            ->setOption('margin-left', 5)
            ->setOption('margin-right', 5)
            ->setOption('header-html', view('template.header.headerGepOficioPdf'))
            //->setOption ('footer-html', view('template.footer.footerGepOficioPdf'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');

        return $pdf->inline();


    }

    public function separacaoInternosUnidadePdf($data){

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dataDefinida = Tdata::TdataMysql(str_replace("-","/",$data));

        $escoltas = DB::table('gep')
            ->where('data_saida', $dataDefinida)->select('id')->get();

        $idEscolta = json_decode($escoltas, true);
        $idEscolta = array_column($idEscolta, 'id');

        $internos = array();
        //$missoes = array();
        $doc = array();

        //dd( self::getDadosEscolta(1112));

        $internos_escolta = DB::table('gep_internos')
            ->leftJoin('escolta','escolta.id','=','gep_internos.fk_escolta')
            ->where('escolta.responsavel', $lotacaoServidor)
            ->whereIn('gep_internos.fk_gep', $idEscolta)
            ->select('gep_internos.*')
            ->get();

        foreach($internos_escolta as $interno)
        {
            $dadosEscolta = self::getDadosEscolta($interno->fk_gep, $interno->fk_interno);

            if(is_numeric($dadosEscolta[0]->destino))
            {
                $destinoNome = JudiciarioController::getNomeVaraComarca($dadosEscolta[0]->destino);
                $qualificacao = JudiciarioController::getQualificacaoVara($dadosEscolta[0]->destino);
                $juiz = JudiciarioController::getJuizVara($dadosEscolta[0]->destino);
                $comarca = JudiciarioController::getComarcaVara($dadosEscolta[0]->destino);
            }else{
                $destinoNome = $dadosEscolta[0]->destino;
                $qualificacao = "";
                $juiz = "";
                $comarca = "";
            }

            $dataExtenso = substr($dadosEscolta[0]->data_saida, 8,2)." de ".Tdata::getMesExt(substr($dadosEscolta[0]->data_saida, 5,2))." de ".substr($dadosEscolta[0]->data_saida, 0,4);

            array_push($internos, array(
                'fk_gep' => $interno->fk_gep,
                'fk_escolta' => $interno->fk_escolta,
                'destino' => $destinoNome,
                'destinoCode' => $dadosEscolta[0]->destino,
                'qualificacao' => $qualificacao,
                'juiz' => $juiz,
                'comarca' => $comarca,
                'data_saida' => $dadosEscolta[0]->data_saida,
                'data_extenso' => $dataExtenso,
                'horario_saida' => $dadosEscolta[0]->horario_saida,
                'interno' => $interno->fk_interno,
                'documento' => $dadosEscolta[0]->documento,
                'vtr' => $dadosEscolta[0]->vtr_placa,
                'nomeInterno' => InternoController::getInternoNome($interno->fk_interno),
                'unidadeInterno' => InternoController::getIdUnidadeInterno($interno->fk_interno),
                'nomeUnidadeInterno' => UnidadesController::getNomeUp(InternoController::getIdUnidadeInterno($interno->fk_interno)),
                'localizacao' => InternoController::getLotacaoInterno($interno->fk_interno),
            ));

        }

        $sort = [
            ['column' => 'nomeInterno', 'order' => 'asc'],
            ['column' => 'localizacao', 'order' => 'asc'],

        ];

        // dd($internos);

        $internos = collect($internos);
        $internos = self::multiPropertySort($internos, $sort);
        $internos = json_decode($internos, true);


        //dd($internos);

        //dd(count($internos));

        $missoes = array();

        foreach($internos as $array){
            $missoes[$array["nomeUnidadeInterno"]][] = $array;
        }

        //dd($missoes);

        $pdf = \PDF::loadView('gep.escolta.documento.separacaoInternosUnidade', compact('missoes'))
            ->setOption('margin-bottom', 30)
            ->setOption('margin-top', 40)
            ->setOption('margin-left', 5)
            ->setOption('margin-right', 5)
            ->setOption('header-html', view('template.header.headerGepOficioPdf'))
            //->setOption ('footer-html', view('template.footer.footerGepOficioPdf'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');

        return $pdf->inline();


    }

    public function justificativaNaoRealizacao()
    {
        $varas = DB::table('judiciario_dados')->orderBy('vara', 'ASC')->orderBy('comarca', 'ASC')->get();

        return view('gep.escolta.justificativas.formJustificativas', compact('varas'));


    }

    public function justificativaNaoRealizacaoPdf(Request $request)
    {
        //dd($request->input());

        $tipo = $request->get('tipo');
        $nOficio = $request->get('nOficio');
        $dataDesignada = $request->get('dataDesignada');
        $horaDesignada = $request->get('horaDesignada');
        $varaDestino = $request->get('varaDestino');
        $internos = $request->get('internos');
        $dataEvento = $request->get('dataEvento');
        $dataEvento2 = $request->get('dataEvento2');
        $horaEvento = $request->get('horaEvento');
        $textoEvento = $request->get('textoEvento');
        $textoEvento2 = $request->get('textoEvento2');
        $textoEvento3 = $request->get('textoEvento3');
        $opreracaoNaCela = $request->get('opreracaoNaCela');

        if(!is_null($varaDestino)) {
            $judiciarioDados = JudiciarioController::getdadosJudiciario($varaDestino);
            $dadosJudiciario = array(
                'vara' => $judiciarioDados->vara,
                'juiz' => $judiciarioDados->juiz,
                'comarca' => $judiciarioDados->comarca,
                'qualificacao' => $judiciarioDados->qualificacao,
            );
        }else{
            $dadosJudiciario = null;
        }



        $internosDados = array();
        if(!is_null($internos)) {
            foreach ($internos as $interno) {
                $dadosInterno = InternoController::getDadosInterno($interno);
                array_push($internosDados, array(
                    'nome' => $dadosInterno[0]->nome,
                    'prontuario' => $interno,
                    'oficio' => $nOficio,
                    'unidadeInterno' => InternoController::getUnidadeInterno($interno),
                    'dataAlvara' => MovimentacoesController::getDataUltimoAlvara($interno),
                    'dataCondicional' => MovimentacoesController::getDataUltimaCondicional($interno),
                    'dataFuga' => MovimentacoesController::getDataUltimaFuga($interno),
                    'dataObito' => MovimentacoesController::getDataObito($interno),
                    'dataPrisaoDomiciliar' => MovimentacoesController::getDataUltimaPrisaoDomiciliar($interno),
                    'dataEntradaMonitoramento' => MovimentacoesController::getDataUltimaEntradaMonitoramento($interno),
                    'dataUltimaProgressaoSemiAberto' => MovimentacoesController::getDataUltimaProgressaoSemiAberto($interno),
                    'dataUltimaProgressaoAberto' => MovimentacoesController::getDataUltimaProgressaoAberto($interno),
                    'dataEvento' => $dataEvento,
                    'dataEvento2' => $dataEvento2,
                    'horaEvento' => $horaEvento,
                    'textoEvento' => $textoEvento,
                    'textoEvento2' => $textoEvento2,
                    'opreracaoNaCela' => $opreracaoNaCela,
                ));
                $nOficio = $nOficio +1;
            }
        }else{
            array_push($internosDados, array(
                'nome' => $textoEvento3,
            ));
        }

//        dd($internosDados);

        $textoData = Tdata::diaNome().", ". Tdata::dia()." de ".Tdata::getMesExt(date("m"))." de ".date("Y");

        $pdf = \PDF::loadView("gep.escolta.documento.justificativas.".$tipo."", compact('internosDados','dadosJudiciario','textoData','dataDesignada','horaDesignada'))
            ->setOption('margin-bottom', 55)
            ->setOption('margin-top', 40)
            ->setOption('margin-left', 5)
            ->setOption('margin-right', 5)
           ->setOption('header-html', view('gep.escolta.documento.justificativas.headerJustificativas'))
            ->setOption ('footer-html', view('gep.escolta.documento.justificativas.footerJustificativas'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');

        return $pdf->inline();



    }
    function group_by($key, $data) {
        $result = array();

        foreach($data as $val) {
            if(array_key_exists($key, $val)){
                $result[$val[$key]][] = $val;
            }else{
                $result[""][] = $val;
            }
        }

        return $result;
    }


    function multi_unique($src){
        $output = array_map("unserialize",
            array_unique(array_map("serialize", $src)));
        return $output;
    }
    public function buscaEquipeVtrAjax(Request $request)
    {
        $vtr = $request->get('vtr');

        $dados = DB::table("gep_equipe")->where('placa_vtr', $vtr)->orderBy('id', 'DESC')->first();

        if(is_null($dados))
        {
            return "";
        }else {

            $equipeDados = array();

            $agentesEquipe = DB::table("gep_equipe_servidor")->where('fk_equipe', $dados->id)->get();

            foreach ($agentesEquipe as $equipe) {
                $dadosServidor = FacadeController::getDadosServidorPorMat($equipe->matricula);

                array_push($equipeDados, array(
                    "id" => $dadosServidor[0]->id,
                    "nome" => utf8_encode($dadosServidor[0]->nome),
                    "lotacao" => FacadeController::getLotacaoServidor($dadosServidor[0]->cpf, "S"),
                    "foto" => config('app.rh')."/storage/app/arquivos/servidores/" . $dadosServidor[0]->id . "/foto/" . $dadosServidor[0]->id . ".jpeg",
                    "matricula" => $dadosServidor[0]->matricula,
                    "cpf" => $dadosServidor[0]->cpf
                ));
            }

            return $equipeDados = json_decode(json_encode($equipeDados), FALSE);
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