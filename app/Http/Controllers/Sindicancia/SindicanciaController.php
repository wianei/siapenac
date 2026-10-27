<?php


/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a sindicancia
|
*/

namespace App\Http\Controllers\Sindicancia;

use App\Classes\Constantes;
use App\Classes\Tdata;
use App\Http\Controllers\Arquivo\ArquivoController;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Interno\InternoController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Http\Controllers\Permissoes\PermissoesController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class SindicanciaController extends Controller
{

    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->middleware('auth');


    }

    public function callAction($method, $parameters)
    {

        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::verifyPermission("SindicanciaController", $method);
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

//        $metodosComum = collect(['cadastro','store','buscar','editar','editarStore','arquivoDelete','visualizar','pdf']);
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

    /**
     * view de cadastro de interno
     */
    public function cadastro()
    {
        $lotacaoServidor = FacadeController::getLotacaoServidor(Auth::user()->cpf);

        $unidades = DB::table('unidade')->where('sigla',$lotacaoServidor)->orderBy('sigla', 'ASC')->get();

        return view('sindicancia.cadastro', compact('unidades'));
    }

    public function store(Request $request)
    {
        //dd($request->all());

        /**
         * validacoes
         */

        $internosValidar = $request->get('internos');
        if(!isset($internosValidar))
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Selecione os internos que foram para escolta!');

        }

        $tituloValidar = $request->get('titulo');
        $statusValidar = $request->get('status');
        $gravidadeFaltaValidar = $request->get('gravidade_falta');
        $dataIncidenteValidar = $request->get('data_incidente');
        $descricaoValidar = $request->get('descricao');

        if($tituloValidar == null ||$statusValidar == null ||$gravidadeFaltaValidar == null ||$dataIncidenteValidar == null ||$descricaoValidar == null)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Preencha os campos obrigatórios!');

        }

        //--------------------- !validacoes ----------------------------------

        // LOG - array que armazena os textos do log
        $diferencas = array();

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $insertSindicancia = DB::table('sindicancia')->insert([
                'unidade' => $lotacaoServidor,
                'titulo' => $request->get('titulo'),
                'status' => $request->get('status'),
                'gravidade_falta' => $request->get('gravidade_falta'),
                'data_incidente' => Tdata::TdataMysql($request->get('data_incidente')),
                'descricao' => $request->get('descricao'),
                'created_at' => date("Y-m-d"),
            ]
        );

        $idSindicancia = DB::getPdo()->lastInsertId();

        $alteracoes = "Criação de sindicância<br><i><strong>Unidade: ".UnidadesController::getNomeUp($lotacaoServidor)."<br>Título: ".$request->get('titulo')."<br>Status: ".$request->get('status')."<br>Gravidade da falta: ".$request->get('gravidade_falta')."<br>Data do incidente: ".$request->get('data_incidente')."</i></strong><br>";
        array_push($diferencas, $alteracoes);


        $servidores = $request->get('agentesHidden');
        if(isset($servidores)) {
            foreach ($servidores as $servidor) {
                $insertServidores = DB::table('sindicancia_agentes')->insert([
                        'fk_sindicancia' => $idSindicancia,
                        'matricula' => $servidor,
                    ]
                );

                // LOG
                $dadosServidor = FacadeController::getDadosServidorPorMat($servidor);
                $alteracoes = "Inserção de servidor na sindicância: <i><strong>" . $dadosServidor[0]->nome . "(" . $servidor . ")</i></strong><br>";
                array_push($diferencas, $alteracoes);


            }
        }

        $internos = $request->get('internos');
        foreach($internos as $interno)
        {
            $insertServidores = DB::table('sindicancia_interno')->insert([
                    'fk_sindicancia' => $idSindicancia,
                    'fk_interno' => $interno,
                ]
            );

            //LOG
            $nomeInterno = InternoController::getInternoNome($interno);
            $alteracoes = "Inserção de interno na sindicância: <i><strong>".$nomeInterno."</i></strong><br>";
            array_push($diferencas, $alteracoes);

        }


        // se enviar o arquivo
        if($request->file('inputFile')[0] != null) {

            $qtdArquivo = count($request->file('inputFile'));

            for ($x = 0; $x < $qtdArquivo; $x++) {

                $destinationPath = Constantes::ARQUIVOS_SINDICANCIA . "/" . $idSindicancia;

                $name = "sindicancia".$idSindicancia . "_" . time() . "_" . $request->file('inputFile')[$x]->getClientOriginalName();

                $request->file('inputFile')[$x]->storeAs($destinationPath, $name);

                $insertArquivo = DB::table('sindicancia_arquivos')->insert(
                    ['fk_sindicancia' => $idSindicancia,
                        'nome_arquivo' => $name,
                        'created_at' => date("Y-m-d")
                    ]
                );

                $alteracoes = "Inserção de arquivo na escolta, arquivo: <i><strong>".$name."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }
        }


        if ($idSindicancia) {

            $textoLog = implode(" ", $diferencas);
            LogController::log($idSindicancia,"sindicancia","add", Auth::user()->cpf, $textoLog);

            return redirect()->route('sindicancia.buscar')->with('alert-success', 'Sindicância inserida com sucesso!');

        }else{

            return redirect()->route('sindicancia.buscar')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }

    public function buscar(Request $request)
    {

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        /**
         * se não realizar uma busca
         * - Evita que mostre todos os resultados sem realizar uma busca
         */
        if(!strpos($url,"?"))
        {

            $dados = false;

        }else {

            //dd($request->all());

            $sindicancia = DB::table('sindicancia');

            $sindicancia->leftJoin('unidade', 'unidade.id', '=', 'sindicancia.unidade');

            $unidade = $request->get('unidade');
            $data_inicial = Tdata::TdataMysql($request->get('data_inicial'));
            $data_final = Tdata::TdataMysql($request->get('data_final'));
            $status = $request->get('status');
            $gravidade_falta = $request->get('gravidade_falta');

            if ($unidade != null) {
                $sindicancia->where('sindicancia.unidade', $unidade);
            }

            if ($data_inicial != null) {
                $sindicancia->where('sindicancia.data_incidente', '>=', $data_inicial);
            }

            if ($data_final != null) {
                $sindicancia->where('sindicancia.data_incidente', '<=', $data_final);
            }

            if ($status != null) {
                $sindicancia->where('sindicancia.status', $status);
            }

            if ($gravidade_falta != null) {
                $sindicancia->where('sindicancia.gravidade_falta', $gravidade_falta);
            }

            $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
            if ($unidade == null) {
                $sindicancia->where('sindicancia.unidade', $lotacaoServidor);
            }


            $internos = $request->get('internos');
            if(isset($internos)){

                $sindicancia->leftJoin('sindicancia_interno',function($query){
                    $query->on('sindicancia_interno.fk_sindicancia', '=' ,'sindicancia.id');
                });

                foreach($internos as $interno)
                {
                    $sindicancia->where('sindicancia_interno.fk_interno', $interno);

                }
            }

            $agentes = $request->get('agentesHidden');
            if(isset($agentes)){

                $sindicancia->leftJoin('sindicancia_agentes',function($query){
                    $query->on('sindicancia_agentes.fk_sindicancia', '=' ,'sindicancia.id');
                });

                foreach($agentes as $agente)
                {
                    $sindicancia->where('sindicancia_agentes.matricula', $agente);

                }
            }

            // passa a query para a paginacao "->appends..."
            $dados = $sindicancia->select( "unidade.sigla AS unidadePrisional","sindicancia.*", DB::raw("DATE_FORMAT(sindicancia.data_incidente,'%d/%m/%Y') as data"))->orderBy('sindicancia.data_incidente','DESC')->paginate(50)->appends($request->input());

        }

        $unidades = DB::table('unidade')->where('status','1')->orderBy('sigla', 'ASC')->get();

        return view('sindicancia.buscar',compact('unidades','dados'));
    }

    public function editar($id)
    {
        $lotacaoServidor = FacadeController::getLotacaoServidor(Auth::user()->cpf);

        $unidades = DB::table('unidade')->where('sigla',$lotacaoServidor)->orderBy('sigla', 'ASC')->get();

        $dados = DB::table('sindicancia')->select('sindicancia.*', DB::raw("DATE_FORMAT(sindicancia.data_incidente,'%d/%m/%Y') as data"))->where('id', $id)->get();

        if($dados[0]->unidade != UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf)))
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para alterar as sindicâncias de outra unidade!');

        }


        $relacaoInterno = DB::table('sindicancia_interno')->where('fk_sindicancia', $id)->get();

        $dadosInterno = array();

        foreach($relacaoInterno as $relacao)
        {
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

        $relacaoAgentes = DB::table('sindicancia_agentes')->where('fk_sindicancia', $id)->get();

        $dadosAgentes = array();

        foreach($relacaoAgentes as $agentes)
        {
            $relAgentes = FacadeController::getDadosServidorPorMat($agentes->matricula);

            array_push($dadosAgentes, array(
                "nome" => $relAgentes[0]->nome,
                "matricula" => $relAgentes[0]->matricula,
                "lotacao" => FacadeController::getLotacaoServidor($relAgentes[0]->cpf),
                "foto" => config('app.rh')."/storage/app/arquivos/servidores/".$relAgentes[0]->id."/foto/".$relAgentes[0]->id.".jpeg",
            ));
        }

        $dadosArquivos = DB::table('sindicancia_arquivos')->select("*", DB::raw("DATE_FORMAT(created_at,'%d/%m/%Y') as data"))->where('fk_sindicancia', $id)->get();
//dd($dadosAgentes);
        return view('sindicancia.cadastro', compact('unidades','dados','dadosInterno','dadosAgentes','dadosArquivos'));
    }

    public function editarStore(Request $request, $id)
    {
        //dd($request->input());

        /**
         * validacoes
         */

        $internosValidar = $request->get('internos');
        if(!isset($internosValidar))
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Selecione pelo menos um interno!');

        }

        $tituloValidar = $request->get('titulo');
        $statusValidar = $request->get('status');
        $gravidadeFaltaValidar = $request->get('gravidade_falta');
        $dataIncidenteValidar = $request->get('data_incidente');
        $descricaoValidar = $request->get('descricao');

        if($tituloValidar == null ||$statusValidar == null ||$gravidadeFaltaValidar == null ||$dataIncidenteValidar == null ||$descricaoValidar == null)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Preencha os campos obrigatórios!');

        }

        //--------------------- !validacoes ----------------------------------


        //-------------------- LOG -------------------------------------

        $dataForm = $request->except('_token','interno','unidade_prisional','pavilhao','ala','cela','internos','agente','agentesHidden','internosRemover','agentesRemover','inputFile');
        $dataForm['data_incidente'] = Tdata::TdataMysql($dataForm['data_incidente']);

        // LOG - array que armazena os textos do log
        $diferencas = array();


        $dadosBD = DB::table("sindicancia")->where('id', $id)->get();
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


        $updateSindicancia = DB::table('sindicancia')->where('id',$id)->update([
                'titulo' => $request->get('titulo'),
                'status' => $request->get('status'),
                'gravidade_falta' => $request->get('gravidade_falta'),
                'data_incidente' => Tdata::TdataMysql($request->get('data_incidente')),
                'descricao' => $request->get('descricao'),
                'update_at' => date("Y-m-d"),
            ]
        );

        // -- gerencia os agentes
        $agentesRemover = $request->get('agentesRemover');
        if(isset($agentesRemover)){
            foreach ($agentesRemover as $matriculaAgente) {
                $deleteAgente = DB::table('sindicancia_agentes')->where('fk_sindicancia', $id)->where('matricula', $matriculaAgente)->delete();

                // LOG
                $alteracoes = "Exclusão de servidor da sindicância: <i><strong>".FacadeController::getNomeServidor($matriculaAgente)."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }

        }

        $servidores = $request->get('agentesHidden');
        if(isset($servidores)) {
            foreach ($servidores as $servidor) {
                $verificaServidor = DB::table('sindicancia_agentes')->where('fk_sindicancia', $id)->where('matricula', $servidor)->get();

                if (empty(json_decode($verificaServidor, true))) {
                    $insertServidores = DB::table('sindicancia_agentes')->insert([
                        'fk_sindicancia' => $id,
                        'matricula' => $servidor,
                    ]);

                    // LOG
                    $alteracoes = "Inserção de servidor na sindicância: <i><strong>" . FacadeController::getNomeServidor($servidor) . "</i></strong><br>";
                    array_push($diferencas, $alteracoes);

                }

            }
        }

        //-- gerencia os internos
        $internosRemover = $request->get('internosRemover');
        if(isset($internosRemover)){
            foreach ($internosRemover as $idInternoRemover) {

                $deleteInterno = DB::table('sindicancia_interno')->where('fk_sindicancia', $id)->where('fk_interno', $idInternoRemover)->delete();

                // LOG
                $alteracoes = "Exclusão de interno da sindicância: <i><strong>".InternoController::getInternoNome($idInternoRemover)."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }

        }
        $internos = $request->input('internos');
        foreach($internos as $interno)
        {
            if(is_numeric($interno))
            {

                $verificaInterno = DB::table('sindicancia_interno')->where('fk_sindicancia',$id)->where('fk_interno', $interno)->get();

                if(empty(json_decode($verificaInterno)))
                {
                    $insertServidores = DB::table('sindicancia_interno')->insert([
                            'fk_sindicancia' => $id,
                            'fk_interno' => $interno,
                        ]
                    );

                    // LOG
                    $alteracoes = "Inserção de interno na sindicância: <i><strong>".InternoController::getInternoNome($interno)."</i></strong><br>";
                    array_push($diferencas, $alteracoes);


                }
            }
        }


        // -- gerencia os arquivos
        // se enviar o arquivo
        if($request->file('inputFile')[0] != null) {

            $qtdArquivo = count($request->file('inputFile'));

            for ($x = 0; $x < $qtdArquivo; $x++) {

                $destinationPath = Constantes::ARQUIVOS_SINDICANCIA . "/" . $id;

                $name = "sindicancia".$id . "_" . time() . "_" . $request->file('inputFile')[$x]->getClientOriginalName();

                $request->file('inputFile')[$x]->storeAs($destinationPath, $name);

                $insertArquivo = DB::table('sindicancia_arquivos')->insert(
                    ['fk_sindicancia' => $id,
                        'nome_arquivo' => $name,
                        'created_at' => date("Y-m-d")
                    ]
                );

                $alteracoes = "Inserção de arquivo na sindicância, arquivo: <i><strong>".$name."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }
        }


        if ($id) {

            $textoLog = implode(" ", $diferencas);
            LogController::log($id,"sindicancia","update", Auth::user()->cpf, $textoLog);

            return redirect()->route('sindicancia.buscar')->with('alert-success', 'Sindicância alterada com sucesso!');

        }else{

            return redirect()->route('sindicancia.buscar')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }

    public function arquivoDelete($id)
    {
        $delete = ArquivoController::excluirArquivo("sindicancia_arquivos",$id,Constantes::ARQUIVOS_SINDICANCIA);

        LogController::log($id,"sindicancia_arquivos","delete",Auth::user()->cpf,"Exclusão de arquivo da sindicancia");

        if($delete)
        {
            return redirect()->route('sindicancia.buscar')->with('alert-success', 'Arquivo apagado com sucesso!');
        }else{
            return redirect()->route('sindicancia.buscar')->with('alert-danger', 'Ocorreu um problema ao apagar o arquivo, por favor, tente novamente!');
        }
    }

    public function visualizar($id)
    {
        $lotacaoServidor = FacadeController::getLotacaoServidor(Auth::user()->cpf);

        $dados = DB::table('sindicancia')->select('sindicancia.*', DB::raw("DATE_FORMAT(sindicancia.data_incidente,'%d/%m/%Y') as data"), DB::raw("DATE_FORMAT(update_at,'%d/%m/%Y') as dataAtt"))->where('id', $id)->get();

        $relacaoInterno = DB::table('sindicancia_interno')->where('fk_sindicancia', $id)->get();

        $dadosInterno = array();

        foreach($relacaoInterno as $relacao)
        {
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

        $relacaoAgentes = DB::table('sindicancia_agentes')->where('fk_sindicancia', $id)->get();

        $dadosAgentes = array();

        foreach($relacaoAgentes as $agentes)
        {
            $relAgentes = FacadeController::getDadosServidorPorMat($agentes->matricula);

            array_push($dadosAgentes, array(
                "nome" => $relAgentes[0]->nome,
                "matricula" => $relAgentes[0]->matricula,
                "lotacao" => FacadeController::getLotacaoServidor($relAgentes[0]->cpf),
                "foto" => config('app.rh')."/storage/app/arquivos/servidores/".$relAgentes[0]->id."/foto/".$relAgentes[0]->id.".jpeg",
            ));
        }

        // dados Log
        if(count(LogController::getDadosLog($id, "sindicancia")) != 0){
            $dadosLog = LogController::getDadosLog($id, "sindicancia");
            $userLog = FacadeController::getDadosServidor($dadosLog[0]->cpf_usuario,"nome");
            $textoLog = "Registrado por: ".$userLog[0]->nome." (".$dadosLog[0]->cpf_usuario.") em ".Tdata::TtimestampBR($dadosLog[0]->created_at);
        }
        else{
            $textoLog = "Sem log registrado.";
        }

        $arquivos = DB::table('sindicancia_arquivos')->select("*", DB::raw("DATE_FORMAT(created_at,'%d/%m/%Y') as data"))->where('fk_sindicancia', $id)->get();


        if(count($arquivos) > 0){
            $dadosArquivos = $arquivos;
        }else{
            $dadosArquivos = false;
        }

        return view('sindicancia.visualizar', compact('dados','dadosInterno','dadosAgentes','dadosArquivos','textoLog'));
    }

    public function pdf($id)
    {

        $dados = DB::table('sindicancia')->select('sindicancia.*', DB::raw("DATE_FORMAT(sindicancia.data_incidente,'%d/%m/%Y') as data"), DB::raw("DATE_FORMAT(update_at,'%d/%m/%Y') as dataAtt"))->where('id', $id)->get();

        $relacaoInterno = DB::table('sindicancia_interno')->where('fk_sindicancia', $id)->get();

        $dadosInterno = array();

        foreach($relacaoInterno as $relacao)
        {
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

        $relacaoAgentes = DB::table('sindicancia_agentes')->where('fk_sindicancia', $id)->get();

        $dadosAgentes = array();

        foreach($relacaoAgentes as $agentes)
        {
            $relAgentes = FacadeController::getDadosServidorPorMat($agentes->matricula);

            array_push($dadosAgentes, array(
                "nome" => $relAgentes[0]->nome,
                "matricula" => $relAgentes[0]->matricula,
                "lotacao" => FacadeController::getLotacaoServidor($relAgentes[0]->cpf),
                "foto" => config('app.rh')."/storage/app/arquivos/servidores/".$relAgentes[0]->id."/foto/".$relAgentes[0]->id.".jpeg",
            ));
        }

        return \PDF::loadView('sindicancia.pdf', compact('dados','dadosInterno','dadosAgentes'))
            // Se quiser que fique no formato a4 retrato: ->setPaper('a4', 'landscape')
            //->download('nome-arquivo-pdf-gerado.pdf');
            // mostra na tela
            ->stream();
            //->save(storage_path("app/arquivos/cautela/cautela_".$numeroCautela.".pdf"))->stream();

    }
}
