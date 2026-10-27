<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados ao controle de acesso
|
*/

namespace App\Http\Controllers\ConcursoRemocao;

use App\Classes\Constantes;
use App\Classes\Tdata;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Support\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Helper\Table;

class ConcursoRemocaoController extends Controller
{

    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->middleware('auth');


    }

    public function callAction($method, $parameters)
    {

        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::getVerifyPermissionController("ConcursoRemocaoController");
        
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }




        return parent::callAction($method, $parameters);
    }


    public function escolhaLotacao(){

        $autorizadosArray = array("Pedro Florencio" => "11180625234", "Capistrano" => "00960485490", "Damasio" => "20198930453", "Saulo" => "08351578495", "Syvio" => "50361988400", "Andrezza" => "00960844481",
            "Ramon" => "09723287420", "Rocha" => "07063752464");

        //$unidades = DB::table('unidade')->where('status','1')->orderBy('nome', 'ASC')->get();

        $dadosServidor = FacadeController::getDadosServidor(Auth::user()->cpf,"*");


        if($dadosServidor[0]->cargo != 1)
        {
            return redirect()->back()->with('alert-danger', 'Opção liberada apenas para o cargo de Agente Penitenciário. Em caso de divergência, entre em contato com o Setor de RH da '.config('app.secretaria'));

        }

        $turmaSerivdor = FacadeController::getTurmaServidor($dadosServidor[0]->matricula);

        $unidadesDados = DB::table('concursoremocao_vagas_unidade');
        $unidadesDados->where('ano', 2019);

        if($turmaSerivdor == 1)
        {
            $unidadesDados->where('vagas_primeira_turma','!=', 0);
        }
        if($turmaSerivdor == 2)
        {
            $unidadesDados->where('vagas_segunda_turma','!=', 0);
        }
        if($turmaSerivdor == 3)
        {
            $unidadesDados->where('vagas_terceira_turma','!=', 0);
        }

//        if($dadosServidor[0]->sexo == "Masculino")
//        {
//            $unidadesDados->where('unidade','!=', 60);
//        }

        $unidadesDados = $unidadesDados->get();

        $unidades = array();
        foreach($unidadesDados as $unidadesDado)
        {
            array_push($unidades, array(
                'id' => $unidadesDado->unidade,
                'nome' => UnidadesController::getNomeUp($unidadesDado->unidade),
                'sigla' => UnidadesController::getSiglaUp($unidadesDado->unidade)
            ));

        }

        $unidades = collect($unidades)->sortBy('nome');
        $unidades = json_decode(json_encode($unidades), FALSE);

        $dadosServidor = FacadeController::getDadosServidor(Auth::user()->cpf,"*");

        $escolha = DB::table("concursoremocao_escolha_unidade_servidor")->where('matricula',$dadosServidor[0]->matricula)->select('concursoremocao_escolha_unidade_servidor.*', DB::raw("DATE_FORMAT(concursoremocao_escolha_unidade_servidor.created_at,'%d/%m/%Y às %H:%i') as created_at"))->get();

        return view('concursoRemocao.escolhaLotacao', compact('unidades','escolha'));
    }

    public function escolhaLotacaoStore(Request $request)
    {
        //dd($request->input());

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf, "RH"));

       // dd($lotacaoServidor);

        $values = array($request->get('lotacao1'), $request->get('lotacao2'), $request->get('lotacao3'));
        if(count(array_unique($values)) === 1) {
           // dd("elementos iguais", $request->get('lotacao1'), $request->get('lotacao2'), $request->get('lotacao3'));
            return redirect()->back()->with('alert-danger', 'As escolhas devem ser diferentes!');
        }

        $dadosServidor = FacadeController::getDadosServidor(Auth::user()->cpf,"*");

        $insertLotacao1 = DB::table('concursoremocao_escolha_unidade_servidor')->insert([
           'matricula' => $dadosServidor[0]->matricula,
           'unidade_atual' => $lotacaoServidor,
           'unidade_destino' => $request->get('lotacao1'),
           'ordem_escolha' => 1,
           'ano' => date('Y'),
           'created_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        $insertLotacao2 = DB::table('concursoremocao_escolha_unidade_servidor')->insert([
           'matricula' => $dadosServidor[0]->matricula,
           'unidade_atual' => $lotacaoServidor,
           'unidade_destino' => $request->get('lotacao2'),
           'ordem_escolha' => 2,
           'ano' => date('Y'),
           'created_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        $insertLotacao3 = DB::table('concursoremocao_escolha_unidade_servidor')->insert([
           'matricula' => $dadosServidor[0]->matricula,
           'unidade_atual' => $lotacaoServidor,
           'unidade_destino' => $request->get('lotacao3'),
           'ordem_escolha' => 3,
           'ano' => date('Y'),
           'created_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        if($insertLotacao3)
        {
            return redirect()->route('concursoRemocao.mapaVagasServidor')->with('alert-success', 'Opções de Lotação escolhidas com sucesso!');
        }

    }

    public function mapaVagasServidor()
    {

        $autorizadosArray = array("Pedro Florencio" => "11180625234", "Capistrano" => "00960485490", "Damasio" => "20198930453", "Saulo" => "08351578495", "Syvio" => "50361988400", "Andrezza" => "00960844481",
            "Ramon" => "09723287420", "Rocha" => "07063752464");

        if(!in_array(Auth::user()->cpf, $autorizadosArray)){
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

        $vagas = DB::table('concursoremocao_vagas_unidade')
            ->leftJoin('unidade','unidade.id','=','concursoremocao_vagas_unidade.unidade')
            ->where('concursoremocao_vagas_unidade.ano', 2019)
            ->orderBy('unidade.nome','ASC')
            ->get();


        $dados = array();
        foreach($vagas as $vaga)
        {

            $candidatos = DB::table('concursoremocao_escolha_unidade_servidor')
                ->where('unidade_destino', $vaga->unidade)
                ->where('ano', 2019)
                ->get();

            $remocoesT1 = DB::table('concursoremocao_lotacao_definida')->where('fk_lotacao_nova',$vaga->unidade)->where('turma',1)->count();

            $remocoesT2 = DB::table('concursoremocao_lotacao_definida')->where('fk_lotacao_nova',$vaga->unidade)->where('turma',2)->count();

            $remocoesT3 = DB::table('concursoremocao_lotacao_definida')->where('fk_lotacao_nova',$vaga->unidade)->where('turma',3)->count();

            $qtdCandidatos1Turma = 0;
            $qtdCandidatos2Turma = 0;
            $qtdCandidatos3Turma = 0;
            foreach($candidatos as $candidato)
            {
                $turmaCandidato = FacadeController::getTurmaServidor($candidato->matricula);
                if($turmaCandidato == 1)
                {
                    $qtdCandidatos1Turma = $qtdCandidatos1Turma + 1;
                }
                if($turmaCandidato == 2)
                {
                    $qtdCandidatos2Turma = $qtdCandidatos2Turma + 1;
                }
                if($turmaCandidato == 3)
                {
                    $qtdCandidatos3Turma = $qtdCandidatos3Turma + 1;
                }
            }


            array_push($dados, array(
                'nome' => $vaga->nome,
                'sigla' => $vaga->sigla,
                'vagas_primeira_turma' => $vaga->vagas_primeira_turma - $remocoesT1,
                'vagas_segunda_turma' => $vaga->vagas_segunda_turma - $remocoesT2,
                'vagas_terceira_turma' => $vaga->vagas_terceira_turma - $remocoesT3,
                'qtdTurma1' => $qtdCandidatos1Turma,
                'qtdTurma2' => $qtdCandidatos2Turma,
                'qtdTurma3' => $qtdCandidatos3Turma,
            ));
        }

        $dados = json_decode(json_encode($dados), FALSE);

        return view('concursoRemocao.mapaVagas', compact('dados'));
    }

    public function listaEscolhas(Request $request)
    {

        $autorizadosArray = array("Pedro Florencio" => "11180625234", "Capistrano" => "00960485490", "Damasio" => "20198930453", "Saulo" => "08351578495", "Syvio" => "50361988400", "Andrezza" => "00960844481",
            "Ramon" => "09723287420", "Rocha" => "07063752464");

        if(!in_array(Auth::user()->cpf, $autorizadosArray)){
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }
        //dd($autorizadosArray);

        $unidadeSelecionada = $request->get('lotacao');

        $unidadesDados = DB::table('concursoremocao_vagas_unidade')->where('ano', 2019)->get();

        $unidades = array();
        foreach($unidadesDados as $unidadesDado)
        {
            array_push($unidades, array(
                'id' => $unidadesDado->unidade,
                'nome' => UnidadesController::getNomeUp($unidadesDado->unidade),
                'sigla' => UnidadesController::getSiglaUp($unidadesDado->unidade)
            ));

        }

        $unidades = collect($unidades)->sortBy('nome');
        $unidades = json_decode(json_encode($unidades), FALSE);

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if(!strpos($url,"?"))
        {

            $dados = false;


        }else {

            $dados = 1;

            $dadosVagasUnidade = DB::table('concursoremocao_vagas_unidade')->where('unidade', $unidadeSelecionada)->get();

            $remocoesT1 = DB::table('concursoremocao_lotacao_definida')->where('fk_lotacao_nova', $unidadeSelecionada)->where('turma',1)->count();

            $remocoesT2 = DB::table('concursoremocao_lotacao_definida')->where('fk_lotacao_nova', $unidadeSelecionada)->where('turma',2)->count();

            $remocoesT3 = DB::table('concursoremocao_lotacao_definida')->where('fk_lotacao_nova', $unidadeSelecionada)->where('turma',3)->count();


            $vagasT1 = $dadosVagasUnidade[0]->vagas_primeira_turma - $remocoesT1;
            $vagasT2 = $dadosVagasUnidade[0]->vagas_segunda_turma - $remocoesT2;
            $vagasT3 = $dadosVagasUnidade[0]->vagas_terceira_turma - $remocoesT3;


            $servidores = DB::table('concursoremocao_escolha_unidade_servidor')->where('unidade_destino', $unidadeSelecionada)->get();

            // separacao de servidores por turma
            $dadosT1 = array();
            $dadosT2 = array();
            $dadosT3 = array();
            foreach ($servidores as $servidor) {
                if (FacadeController::getTurmaServidor($servidor->matricula) == 1) {

                    $remocao = DB::table('concursoremocao_lotacao_definida')->where('servidor', $servidor->matricula)->count();

                    if($remocao == 0) {

                        $dadosServidor = FacadeController::getDadosServidorPorMat($servidor->matricula);

                        if(self::getUnidadeOpcaoEscolha(1, $servidor->matricula) == $unidadeSelecionada)
                        {
                            $primeiraOpcao = 1;
                        }else{
                            $primeiraOpcao = 0;
                        }

                        if(self::getUnidadeOpcaoEscolha(2, $servidor->matricula) == $unidadeSelecionada)
                        {
                            $segundaOpcao = 1;
                        }else{
                            $segundaOpcao = 0;
                        }


                        array_push($dadosT1, array(
                            'id' => $servidor->id,
                            'nome' => utf8_encode($dadosServidor[0]->nome),
                            'data_nascimento' => $dadosServidor[0]->data_nascimento,
                            'data_exercicio' => $dadosServidor[0]->data_exercicio,
                            'data_exercicioBr' => Tdata::TdataBR($dadosServidor[0]->data_exercicio),
                            'lotacaoAtual' => UnidadesController::getSiglaUp($servidor->unidade_atual),
                            'opcao1' => UnidadesController::getSiglaUp(self::getUnidadeOpcaoEscolha(1, $servidor->matricula)),
                            'opcao1Id' => self::getUnidadeOpcaoEscolha(1, $servidor->matricula),
                            'primeiraOpcao' => self::getUnidadeOpcaoEscolha(1, $servidor->matricula),
                            'primeiraOpcaoOrdem' => $primeiraOpcao,
                            'segundaOpcaoOrdem' => $segundaOpcao,
                            'opcao2' => UnidadesController::getSiglaUp(self::getUnidadeOpcaoEscolha(2, $servidor->matricula)),
                            'opcao2Id' => self::getUnidadeOpcaoEscolha(2, $servidor->matricula),
                            'opcao3' => UnidadesController::getSiglaUp(self::getUnidadeOpcaoEscolha(3, $servidor->matricula)),
                            'opcao3Id' => self::getUnidadeOpcaoEscolha(3, $servidor->matricula),

                        ));
                    }
                }

                if (FacadeController::getTurmaServidor($servidor->matricula) == 2) {
                    $remocao = DB::table('concursoremocao_lotacao_definida')->where('servidor', $servidor->matricula)->count();

                    if($remocao == 0) {

                        $dadosServidor = FacadeController::getDadosServidorPorMat($servidor->matricula);

                        if(self::getUnidadeOpcaoEscolha(1, $servidor->matricula) == $unidadeSelecionada)
                        {
                            $primeiraOpcao = 1;
                        }else{
                            $primeiraOpcao = 0;
                        }

                        if(self::getUnidadeOpcaoEscolha(2, $servidor->matricula) == $unidadeSelecionada)
                        {
                            $segundaOpcao = 1;
                        }else{
                            $segundaOpcao = 0;
                        }

                        array_push($dadosT2, array(
                            'id' => $servidor->id,
                            'nome' => utf8_encode($dadosServidor[0]->nome),
                            'data_nascimento' => $dadosServidor[0]->data_nascimento,
                            'data_exercicio' => $dadosServidor[0]->data_exercicio,
                            'data_exercicioBr' => Tdata::TdataBR($dadosServidor[0]->data_exercicio),
                            'lotacaoAtual' => UnidadesController::getSiglaUp($servidor->unidade_atual),
                            'opcao1' => UnidadesController::getSiglaUp(self::getUnidadeOpcaoEscolha(1, $servidor->matricula)),
                            'opcao1Id' => self::getUnidadeOpcaoEscolha(1, $servidor->matricula),
                            'primeiraOpcao' => self::getUnidadeOpcaoEscolha(1, $servidor->matricula),
                            'primeiraOpcaoOrdem' => $primeiraOpcao,
                            'segundaOpcaoOrdem' => $segundaOpcao,
                            'opcao2' => UnidadesController::getSiglaUp(self::getUnidadeOpcaoEscolha(2, $servidor->matricula)),
                            'opcao2Id' => self::getUnidadeOpcaoEscolha(2, $servidor->matricula),
                            'opcao3' => UnidadesController::getSiglaUp(self::getUnidadeOpcaoEscolha(3, $servidor->matricula)),
                            'opcao3Id' => self::getUnidadeOpcaoEscolha(3, $servidor->matricula),

                        ));
                    }
                }

                if (FacadeController::getTurmaServidor($servidor->matricula) == 3) {
                    $remocao = DB::table('concursoremocao_lotacao_definida')->where('servidor', $servidor->matricula)->count();

                    if($remocao == 0) {

                        $dadosServidor = FacadeController::getDadosServidorPorMat($servidor->matricula);

                        if(self::getUnidadeOpcaoEscolha(1, $servidor->matricula) == $unidadeSelecionada)
                        {
                            $primeiraOpcao = 1;
                        }else{
                            $primeiraOpcao = 0;
                        }

                        if(self::getUnidadeOpcaoEscolha(2, $servidor->matricula) == $unidadeSelecionada)
                        {
                            $segundaOpcao = 1;
                        }else{
                            $segundaOpcao = 0;
                        }

                        array_push($dadosT3, array(
                            'id' => $servidor->id,
                            'nome' => utf8_encode($dadosServidor[0]->nome),
                            'data_nascimento' => $dadosServidor[0]->data_nascimento,
                            'data_exercicio' => $dadosServidor[0]->data_exercicio,
                            'data_exercicioBr' => Tdata::TdataBR($dadosServidor[0]->data_exercicio),
                            'lotacaoAtual' => UnidadesController::getSiglaUp($servidor->unidade_atual),
                            'opcao1' => UnidadesController::getSiglaUp(self::getUnidadeOpcaoEscolha(1, $servidor->matricula)),
                            'opcao1Id' => self::getUnidadeOpcaoEscolha(1, $servidor->matricula),
                            'primeiraOpcao' => self::getUnidadeOpcaoEscolha(1, $servidor->matricula),
                            'primeiraOpcaoOrdem' => $primeiraOpcao,
                            'segundaOpcaoOrdem' => $segundaOpcao,
                            'opcao2' => UnidadesController::getSiglaUp(self::getUnidadeOpcaoEscolha(2, $servidor->matricula)),
                            'opcao2Id' => self::getUnidadeOpcaoEscolha(2, $servidor->matricula),
                            'opcao3' => UnidadesController::getSiglaUp(self::getUnidadeOpcaoEscolha(3, $servidor->matricula)),
                            'opcao3Id' => self::getUnidadeOpcaoEscolha(3, $servidor->matricula),

                        ));
                    }
                }


            }


            $sort = [
                ['column' => 'primeiraOpcaoOrdem', 'order' => 'desc'],
                ['column' => 'segundaOpcaoOrdem', 'order' => 'desc'],
                ['column' => 'data_exercicio', 'order' => 'asc'],
                ['column' => 'data_nascimento', 'order' => 'asc'],
                //['column' => 'nome', 'order' => 'asc'],
            ];

            $dadosT1 = collect($dadosT1);
            $dadosT1 = self::multiPropertySort($dadosT1, $sort);
            $dadosT1 = json_decode(json_encode($dadosT1), FALSE);

            $dadosT2 = collect($dadosT2);
            $dadosT2 = self::multiPropertySort($dadosT2, $sort);
            $dadosT2 = json_decode(json_encode($dadosT2), FALSE);

            $dadosT3 = collect($dadosT3);
            $dadosT3 = self::multiPropertySort($dadosT3, $sort);
            $dadosT3 = json_decode(json_encode($dadosT3), FALSE);

            //dd($dadosT3);



            $unidadeNome = UnidadesController::getNomeUp($unidadeSelecionada);

        }

        return view('concursoRemocao.listaEscolhas', compact('dados','unidades','unidadeSelecionada','unidadeNome','dadosT1','vagasT1','dadosT2','vagasT2','dadosT3','vagasT3'));

    }

    public function listaEscolhasPdf($lotacao)
    {
        $unidadeSelecionada = $lotacao;

        $dadosVagasUnidade = DB::table('concursoremocao_vagas_unidade')->where('unidade', $unidadeSelecionada)->get();

        $remocoesT1 = DB::table('concursoremocao_lotacao_definida')->where('fk_lotacao_nova', $unidadeSelecionada)->where('turma',1)->count();

        $remocoesT2 = DB::table('concursoremocao_lotacao_definida')->where('fk_lotacao_nova', $unidadeSelecionada)->where('turma',2)->count();

        $remocoesT3 = DB::table('concursoremocao_lotacao_definida')->where('fk_lotacao_nova', $unidadeSelecionada)->where('turma',3)->count();


        $vagasT1 = $dadosVagasUnidade[0]->vagas_primeira_turma - $remocoesT1;
        $vagasT2 = $dadosVagasUnidade[0]->vagas_segunda_turma - $remocoesT2;
        $vagasT3 = $dadosVagasUnidade[0]->vagas_terceira_turma - $remocoesT3;


        $servidores = DB::table('concursoremocao_escolha_unidade_servidor')->where('unidade_destino', $unidadeSelecionada)->get();

        // separacao de servidores por turma
        $dadosT1 = array();
        $dadosT2 = array();
        $dadosT3 = array();
        foreach ($servidores as $servidor) {
            if (FacadeController::getTurmaServidor($servidor->matricula) == 1) {

                $remocao = DB::table('concursoremocao_lotacao_definida')->where('servidor', $servidor->matricula)->count();

                if($remocao == 0) {

                    $dadosServidor = FacadeController::getDadosServidorPorMat($servidor->matricula);

                    if(self::getUnidadeOpcaoEscolha(1, $servidor->matricula) == $unidadeSelecionada)
                    {
                        $primeiraOpcao = 1;
                    }else{
                        $primeiraOpcao = 0;
                    }

                    if(self::getUnidadeOpcaoEscolha(2, $servidor->matricula) == $unidadeSelecionada)
                    {
                        $segundaOpcao = 1;
                    }else{
                        $segundaOpcao = 0;
                    }


                    array_push($dadosT1, array(
                        'id' => $servidor->id,
                        'nome' => utf8_encode($dadosServidor[0]->nome),
                        'data_nascimento' => $dadosServidor[0]->data_nascimento,
                        'data_exercicio' => $dadosServidor[0]->data_exercicio,
                        'data_exercicioBr' => Tdata::TdataBR($dadosServidor[0]->data_exercicio),
                        'lotacaoAtual' => UnidadesController::getSiglaUp($servidor->unidade_atual),
                        'opcao1' => UnidadesController::getSiglaUp(self::getUnidadeOpcaoEscolha(1, $servidor->matricula)),
                        'opcao1Id' => self::getUnidadeOpcaoEscolha(1, $servidor->matricula),
                        'primeiraOpcao' => self::getUnidadeOpcaoEscolha(1, $servidor->matricula),
                        'primeiraOpcaoOrdem' => $primeiraOpcao,
                        'segundaOpcaoOrdem' => $segundaOpcao,
                        'opcao2' => UnidadesController::getSiglaUp(self::getUnidadeOpcaoEscolha(2, $servidor->matricula)),
                        'opcao2Id' => self::getUnidadeOpcaoEscolha(2, $servidor->matricula),
                        'opcao3' => UnidadesController::getSiglaUp(self::getUnidadeOpcaoEscolha(3, $servidor->matricula)),
                        'opcao3Id' => self::getUnidadeOpcaoEscolha(3, $servidor->matricula),

                    ));
                }
            }

            if (FacadeController::getTurmaServidor($servidor->matricula) == 2) {
                $remocao = DB::table('concursoremocao_lotacao_definida')->where('servidor', $servidor->matricula)->count();

                if($remocao == 0) {

                    $dadosServidor = FacadeController::getDadosServidorPorMat($servidor->matricula);

                    if(self::getUnidadeOpcaoEscolha(1, $servidor->matricula) == $unidadeSelecionada)
                    {
                        $primeiraOpcao = 1;
                    }else{
                        $primeiraOpcao = 0;
                    }

                    if(self::getUnidadeOpcaoEscolha(2, $servidor->matricula) == $unidadeSelecionada)
                    {
                        $segundaOpcao = 1;
                    }else{
                        $segundaOpcao = 0;
                    }

                    array_push($dadosT2, array(
                        'id' => $servidor->id,
                        'nome' => utf8_encode($dadosServidor[0]->nome),
                        'data_nascimento' => $dadosServidor[0]->data_nascimento,
                        'data_exercicio' => $dadosServidor[0]->data_exercicio,
                        'data_exercicioBr' => Tdata::TdataBR($dadosServidor[0]->data_exercicio),
                        'lotacaoAtual' => UnidadesController::getSiglaUp($servidor->unidade_atual),
                        'opcao1' => UnidadesController::getSiglaUp(self::getUnidadeOpcaoEscolha(1, $servidor->matricula)),
                        'opcao1Id' => self::getUnidadeOpcaoEscolha(1, $servidor->matricula),
                        'primeiraOpcao' => self::getUnidadeOpcaoEscolha(1, $servidor->matricula),
                        'primeiraOpcaoOrdem' => $primeiraOpcao,
                        'segundaOpcaoOrdem' => $segundaOpcao,
                        'opcao2' => UnidadesController::getSiglaUp(self::getUnidadeOpcaoEscolha(2, $servidor->matricula)),
                        'opcao2Id' => self::getUnidadeOpcaoEscolha(2, $servidor->matricula),
                        'opcao3' => UnidadesController::getSiglaUp(self::getUnidadeOpcaoEscolha(3, $servidor->matricula)),
                        'opcao3Id' => self::getUnidadeOpcaoEscolha(3, $servidor->matricula),

                    ));
                }
            }

            if (FacadeController::getTurmaServidor($servidor->matricula) == 3) {
                $remocao = DB::table('concursoremocao_lotacao_definida')->where('servidor', $servidor->matricula)->count();

                if($remocao == 0) {

                    $dadosServidor = FacadeController::getDadosServidorPorMat($servidor->matricula);

                    if(self::getUnidadeOpcaoEscolha(1, $servidor->matricula) == $unidadeSelecionada)
                    {
                        $primeiraOpcao = 1;
                    }else{
                        $primeiraOpcao = 0;
                    }

                    if(self::getUnidadeOpcaoEscolha(2, $servidor->matricula) == $unidadeSelecionada)
                    {
                        $segundaOpcao = 1;
                    }else{
                        $segundaOpcao = 0;
                    }

                    array_push($dadosT3, array(
                        'id' => $servidor->id,
                        'nome' => utf8_encode($dadosServidor[0]->nome),
                        'data_nascimento' => $dadosServidor[0]->data_nascimento,
                        'data_exercicio' => $dadosServidor[0]->data_exercicio,
                        'data_exercicioBr' => Tdata::TdataBR($dadosServidor[0]->data_exercicio),
                        'lotacaoAtual' => UnidadesController::getSiglaUp($servidor->unidade_atual),
                        'opcao1' => UnidadesController::getSiglaUp(self::getUnidadeOpcaoEscolha(1, $servidor->matricula)),
                        'opcao1Id' => self::getUnidadeOpcaoEscolha(1, $servidor->matricula),
                        'primeiraOpcao' => self::getUnidadeOpcaoEscolha(1, $servidor->matricula),
                        'primeiraOpcaoOrdem' => $primeiraOpcao,
                        'segundaOpcaoOrdem' => $segundaOpcao,
                        'opcao2' => UnidadesController::getSiglaUp(self::getUnidadeOpcaoEscolha(2, $servidor->matricula)),
                        'opcao2Id' => self::getUnidadeOpcaoEscolha(2, $servidor->matricula),
                        'opcao3' => UnidadesController::getSiglaUp(self::getUnidadeOpcaoEscolha(3, $servidor->matricula)),
                        'opcao3Id' => self::getUnidadeOpcaoEscolha(3, $servidor->matricula),

                    ));
                }
            }


        }

        $sort = [
            ['column' => 'primeiraOpcaoOrdem', 'order' => 'desc'],
            ['column' => 'segundaOpcaoOrdem', 'order' => 'desc'],
            ['column' => 'data_exercicio', 'order' => 'asc'],
            ['column' => 'data_nascimento', 'order' => 'asc'],
            //['column' => 'nome', 'order' => 'asc'],
        ];

        $dadosT1 = collect($dadosT1);
        $dadosT1 = self::multiPropertySort($dadosT1, $sort);
        $dadosT1 = json_decode(json_encode($dadosT1), FALSE);

        $dadosT2 = collect($dadosT2);
        $dadosT2 = self::multiPropertySort($dadosT2, $sort);
        $dadosT2 = json_decode(json_encode($dadosT2), FALSE);

        $dadosT3 = collect($dadosT3);
        $dadosT3 = self::multiPropertySort($dadosT3, $sort);
        $dadosT3 = json_decode(json_encode($dadosT3), FALSE);

        $unidadeNome = UnidadesController::getNomeUp($unidadeSelecionada);

        $pdf = \PDF::loadView('concursoRemocao.pdfListaEscolhas', compact('unidadeSelecionada','unidadeNome','dadosT1','vagasT1','dadosT2','vagasT2','dadosT3','vagasT3'))
            ->setOption('orientation', 'Landscape')
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 10)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption ('footer-center', utf8_decode('Gerado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');

        return $pdf->inline();

    }

    public static function getUnidadeOpcaoEscolha($ordem, $matricula)
    {
        $dados = DB::table('concursoremocao_escolha_unidade_servidor')->where('matricula', $matricula)->where('ordem_escolha', $ordem)->get();

        return $dados[0]->unidade_destino;

    }

    public function definirLotacaoServidor($id)
    {
        $autorizadosArray = array("Pedro Florencio" => "11180625234", "Capistrano" => "00960485490", "Damasio" => "20198930453", "Saulo" => "08351578495", "Syvio" => "50361988400", "Andrezza" => "00960844481",
            "Ramon" => "09723287420", "Rocha" => "07063752464");

        if(!in_array(Auth::user()->cpf, $autorizadosArray)){
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

        $escolhaServidor = DB::table('concursoremocao_escolha_unidade_servidor')->where('id', $id)->get();
        $escolhasServidor = DB::table('concursoremocao_escolha_unidade_servidor')->where('matricula', $escolhaServidor[0]->matricula)->get();
        $dadosServidor = FacadeController::getDadosServidorPorMat($escolhaServidor[0]->matricula);
        $nomeServidor = $dadosServidor[0]->nome;
        $matriculaServidor = $escolhaServidor[0]->matricula;
        $turma = FacadeController::getTurmaServidor($escolhaServidor[0]->matricula);

        $escolhas = array();
        foreach($escolhasServidor as $item)
        {
            array_push($escolhas, array(
                'nome'=> UnidadesController::getNomeUp($item->unidade_destino),
                'id' => $item->unidade_destino,
                'opcao' => $item->ordem_escolha,
            ));
        }
        $unidadeDestinoNome = UnidadesController::getNomeUp($escolhaServidor[0]->unidade_destino);
        $unidadeDestinoId = $escolhaServidor[0]->unidade_destino;
        $unidadeAtual = UnidadesController::getNomeUp($escolhaServidor[0]->unidade_atual);
        $unidadeAtualId = $escolhaServidor[0]->unidade_atual;
        $ordemOpcao = $escolhaServidor[0]->ordem_escolha;

        return view('concursoRemocao.definirLotacao', compact('escolhas','unidadeDestinoNome','unidadeDestinoId','unidadeAtual','unidadeAtualId','ordemOpcao','nomeServidor','matriculaServidor','turma'));


    }

    public function definirLotacaoServidorStore(Request $request)
    {
        $autorizadosArray = array("Pedro Florencio" => "11180625234", "Capistrano" => "00960485490", "Damasio" => "20198930453", "Saulo" => "08351578495", "Syvio" => "50361988400", "Andrezza" => "00960844481",
            "Ramon" => "09723287420", "Rocha" => "07063752464");

        if(!in_array(Auth::user()->cpf, $autorizadosArray)){
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

        // dd($request->input());

        $totalEscolha = DB::table('concursoremocao_lotacao_definida')->where('fk_lotacao_nova', $request->get('lotacao'))->where('turma', $request->get('turma'))->count();

        if($request->get('turma') == 1) {

            $totalVagas = DB::table('concursoremocao_vagas_unidade')->where('unidade', $request->get('lotacao'))->get();

            if($totalEscolha >= $totalVagas[0]->vagas_primeira_turma)
            {
                return redirect()->back()->with('alert-danger', 'Não há mais vagas na unidade selecionada');

            }

        }

        if($request->get('turma') == 2) {

            $totalVagas = DB::table('concursoremocao_vagas_unidade')->where('unidade', $request->get('lotacao'))->get();

            if($totalEscolha >= $totalVagas[0]->vagas_segunda_turma)
            {
                return redirect()->back()->with('alert-danger', 'Não há mais vagas na unidade selecionada');

            }

        }

        if($request->get('turma') == 3) {

            $totalVagas = DB::table('concursoremocao_vagas_unidade')->where('unidade', $request->get('lotacao'))->get();

            if($totalEscolha >= $totalVagas[0]->vagas_terceira_turma)
            {
                return redirect()->back()->with('alert-danger', 'Não há mais vagas na unidade selecionada');

            }

        }

        $verificaLista = DB::table('concursoremocao_lotacao_definida')->whereServidor($request->get('matriculaServidor'))->count();
        if($verificaLista > 0)
        {
            return redirect()->back()->with('alert-danger', 'O servidor já foi removido!');
        }

        $insert = DB::table('concursoremocao_lotacao_definida')->insert([
            'servidor' => $request->get('matriculaServidor'),
            'fk_lotacao_anterior' => $request->get('unidadeAtual'),
            'fk_lotacao_nova' => $request->get('lotacao'),
            'turma' => $request->get('turma'),
            'ordem_escolha' => $request->get('ordemOpcao'),
            'ano' => date("Y"),
            'created_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        if($insert)
        {
            return redirect()->route('concursoRemocao.listaRemocoes')->with('alert-success', 'Remoção realizada com sucesso!');
        }

    }

    public function listaRemocoes()
    {
        $autorizadosArray = array("Pedro Florencio" => "11180625234", "Capistrano" => "00960485490", "Damasio" => "20198930453", "Saulo" => "08351578495", "Syvio" => "50361988400", "Andrezza" => "00960844481",
            "Ramon" => "09723287420", "Rocha" => "07063752464");

        if(!in_array(Auth::user()->cpf, $autorizadosArray)){
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

        $unidadesDados = DB::table('concursoremocao_vagas_unidade')->get();


        $unidades = array();
        foreach($unidadesDados as $unidadesDado)
        {
            $qtdServidores = DB::table('concursoremocao_lotacao_definida')->where('fk_lotacao_nova', $unidadesDado->unidade)->count();

            array_push($unidades, array(
                "idUnidade" => $unidadesDado->unidade,
                "nomeUnidade" => UnidadesController::getNomeUp($unidadesDado->unidade),
                "vagasT1" => $unidadesDado->vagas_primeira_turma,
                "vagasT2" => $unidadesDado->vagas_segunda_turma,
                "vagasT3" => $unidadesDado->vagas_terceira_turma,
                "qtdServidores" => $qtdServidores,
            ));
        }

        $sort = [
            ['column' => 'nomeUnidade', 'order' => 'asc'],
        ];

        $unidades = collect($unidades);
        $unidades = self::multiPropertySort($unidades, $sort);
        $unidades = json_decode(json_encode($unidades), FALSE);


        $dadosServidores = DB::table('concursoremocao_lotacao_definida')->get();

        $servidores = array();
        foreach($dadosServidores as $dadosServidore)
        {
            $dadosServidor = FacadeController::getDadosServidorPorMat($dadosServidore->servidor);

            array_push($servidores, array(
               'nomeServidor' => $dadosServidor[0]->nome,
               'matriculaServidor' => $dadosServidore->servidor,
               'turma' => $dadosServidore->turma,
               'ordemEscolha' => $dadosServidore->ordem_escolha,
               'lotacaoAnterior' => UnidadesController::getNomeUp($dadosServidore->fk_lotacao_anterior),
               'lotacaoRemocao' => UnidadesController::getNomeUp($dadosServidore->fk_lotacao_nova),
               'idLotacaoRemocao' => $dadosServidore->fk_lotacao_nova,
            ));
        }

        $sortServidor = [
            ['column' => 'nomeServidor', 'order' => 'asc'],
        ];

        $servidores = collect($servidores);
        $servidores = self::multiPropertySort($servidores, $sortServidor);
        $servidores = json_decode(json_encode($servidores), FALSE);

        return view('concursoRemocao.listaRemocoes', compact('unidades','servidores'));
    }

    public function listaRemocoesPdf($tipo)
    {
        $unidadesDados = DB::table('concursoremocao_vagas_unidade')->get();


        $unidades = array();
        foreach($unidadesDados as $unidadesDado)
        {
            $qtdServidores = DB::table('concursoremocao_lotacao_definida')->where('fk_lotacao_nova', $unidadesDado->unidade)->count();

            array_push($unidades, array(
                "idUnidade" => $unidadesDado->unidade,
                "nomeUnidade" => UnidadesController::getNomeUp($unidadesDado->unidade),
                "vagasT1" => $unidadesDado->vagas_primeira_turma,
                "vagasT2" => $unidadesDado->vagas_segunda_turma,
                "vagasT3" => $unidadesDado->vagas_terceira_turma,
                "qtdServidores" => $qtdServidores,
            ));
        }

        $sort = [
            ['column' => 'nomeUnidade', 'order' => 'asc'],
        ];

        $unidades = collect($unidades);
        $unidades = self::multiPropertySort($unidades, $sort);
        $unidades = json_decode(json_encode($unidades), FALSE);


        $dadosServidores = DB::table('concursoremocao_lotacao_definida')->get();

        $servidores = array();
        foreach($dadosServidores as $dadosServidore)
        {
            $dadosServidor = FacadeController::getDadosServidorPorMat($dadosServidore->servidor);

            array_push($servidores, array(
                'nomeServidor' => $dadosServidor[0]->nome,
                'matriculaServidor' => $dadosServidore->servidor,
                'turma' => $dadosServidore->turma,
                'ordemEscolha' => $dadosServidore->ordem_escolha,
                'lotacaoAnterior' => UnidadesController::getSiglaUp($dadosServidore->fk_lotacao_anterior),
                'lotacaoRemocao' => UnidadesController::getSiglaUp($dadosServidore->fk_lotacao_nova),
                'idLotacaoRemocao' => $dadosServidore->fk_lotacao_nova,
            ));
        }

        $sortServidor = [
            ['column' => 'nomeServidor', 'order' => 'asc'],
        ];

        $servidores = collect($servidores);
        $servidores = self::multiPropertySort($servidores, $sortServidor);
        $servidores = json_decode(json_encode($servidores), FALSE);

        $pdf = \PDF::loadView('concursoRemocao.listaRemocoesPDF', compact('unidades','servidores','tipo'))
            ->setOption('encoding', 'utf-8')
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 30)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('header-html', view('template.header.headerListaRemocoesPdf'))
            ->setOption ('footer-center', 'Gerado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]')
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');
        return $pdf->inline();

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
}