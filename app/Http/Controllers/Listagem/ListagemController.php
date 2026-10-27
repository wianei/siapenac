<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a Agenda
|
*/

namespace App\Http\Controllers\Listagem;

use App\Classes\Constantes;
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

class ListagemController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

    }

    public function callAction($method, $parameters)
    {
        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::verifyPermission("ListagemController", $method);
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }
        
//        $metodosAdm = collect(['frequenciaAberto','frequenciaAbertoStore','frequenciaSemiaberto','frequenciaSemiabertoStore','frequenciaServidores','frequenciaServidoresStore','frequenciaDiariaInternoCriarLista','frequenciaDiariaInternoCriarListaStore','frequenciaDiariaInternoGerenciarListas','editarLista','editarListaStore','frequenciaDiariaInternoRegistrarPresenca','frequenciaDiariaInternoRegistrarPresencaStore','relatorioFrequenciaDiaria','relatorioFrequenciaDiariaStore']);
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


    /*
     * funcoes ajax
     */
    public function getInternosLista(Request $request)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $listaId = DB::table("lista_frequencia")->where('id', $request->get('idLista'))->where('unidade', $lotacaoServidor)->get()[0]->id;

        $listaInternos = DB::table('lista_frequencia_interno')->where('fk_lista', $listaId)->where('status','A')->get();

        $internos = array();
        foreach($listaInternos as $listaInterno)
        {
            array_push($internos, array(
               'id' => $listaInterno->fk_interno,
               'nome' => InternoController::getInternoNome($listaInterno->fk_interno),
               'localizacao' => InternoController::getLotacaoInterno($listaInterno->fk_interno),
            ));
        }


        $internos = collect($internos);
        $internos = $internos->sortBy('nomeInterno');

        return $internos;

    }

    /*
     * geters
     */
    public static function getFaltasMes($lista, $idInterno, $mes, $ano)
    {
        $dados = DB::table("lista_frequencia_interno_presenca")->where("fk_lista", $lista)->where("fk_interno", $idInterno)->whereMonth("data", $mes)->whereYear("data", $ano)->where('status',"F")->count();

        return $dados;
    }



    public function frequenciaAberto()
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $usuarioLogadoUP = DB::table('unidade')->where('sigla', FacadeController::getLotacaoServidor(Auth::user()->cpf))->get();
        $usuarioLogadoUP = json_decode($usuarioLogadoUP, true)[0];

        return view('listagem.frequenciaAberto.frequenciaAberto', compact('usuarioLogadoUP'));
    }

    public function frequenciaAbertoStore(Request $request)
    {
        ini_set('max_execution_time', 3600);


//        dd($request->all());
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $internosForm = $request->get('internos');
        $siglaUnidade = UnidadesController::getSiglaUp($lotacaoServidor);
        $nomeUnidade = UnidadesController::getNomeUp($lotacaoServidor);
        $folhaIndividual = $request->get('folhaIndividual');

        $datas = explode(',',$request->data);
        $qtdDiasMes = cal_days_in_month(CAL_GREGORIAN, Tdata::getMesData(Tdata::TdataMysql($datas[0])), Tdata::getAnoData(Tdata::TdataMysql($datas[0])));
        $diasData = array();
        foreach($datas as $data)
        {
            array_push($diasData, Tdata::getDiaData(Tdata::TdataMysql($data)));
        }
        $mes = Tdata::getMesExt(Tdata::getMesData(Tdata::TdataMysql($datas[0])));

        $internos = array();

        foreach($internosForm as $interno)
        {
            array_push($internos, array(
                'nome' => InternoController::getInternoNome($interno),
                'prontuário' => $interno,
                'localizacao' =>InternoController::getLotacaoInterno($interno)
            ));
        }

        if($folhaIndividual == "N") {

            $pdf = \PDF::loadView('listagem.frequenciaAberto.pdfFrequenciaAberto', compact('internos', 'datas'))
                ->setOption('orientation', 'landscape')
                ->setOption('margin-bottom', 10)
                ->setOption('margin-top', 40)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('header-html', view('listagem.frequenciaAberto.PdfHeader', compact('siglaUnidade', 'mes', 'nomeUnidade')))
                ->setOption('footer-center', utf8_decode('Gerado em' . date('\ d/m/Y\ ') . ' | Pagina [page] de [topage]'))
                ->setOption('footer-font-size', 7)
                ->setPaper('a4');

            return $pdf->inline();
        }
        if($folhaIndividual == "S"){

            $pdf = \PDF::loadView('listagem.frequenciaAberto.pdfFrequenciaAbertoIndividual', compact('internos', 'datas','qtdDiasMes','diasData','mes'))
                ->setOption('margin-bottom', 10)
                ->setOption('margin-top', 40)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('header-html', view('listagem.frequenciaAberto.PdfHeader', compact('siglaUnidade', 'mes', 'nomeUnidade')))
                ->setOption('footer-center', utf8_decode('Gerado em' . date('\ d/m/Y\ ') . ' | Pagina [page] de [topage]'))
                ->setOption('footer-font-size', 7)
                ->setPaper('a4');

            return $pdf->inline();


        }

    }

    public function frequenciaSemiaberto(){

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $usuarioLogadoUP = DB::table('unidade')->where('sigla', FacadeController::getLotacaoServidor(Auth::user()->cpf))->get();
        $usuarioLogadoUP = json_decode($usuarioLogadoUP, true)[0];

        return view('listagem.frequenciaSemiaberto.frequenciaSemiaberto', compact('usuarioLogadoUP'));

    }

    public function frequenciaSemiabertoStore(Request $request){

        ini_set('max_execution_time', 3600);

        //dd($request->all());
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $internosForm = $request->get('internos');
        $siglaUnidade = UnidadesController::getSiglaUp($lotacaoServidor);
        $nomeUnidade = UnidadesController::getNomeUp($lotacaoServidor);
        $mesNum = $request->get('mesImpressao');
        $ano = Tdata::getAnoData(Tdata::TdataMysql($request->get('data')));


        $datas = explode(',',$request->data);
        $qtdDiasMes = cal_days_in_month(CAL_GREGORIAN, $mesNum, $ano);
        $diasData = array();
        foreach($datas as $data)
        {
            array_push($diasData, Tdata::getDiaData(Tdata::TdataMysql($data)));
        }
        $mes = Tdata::getMesExt($request->get('mesImpressao'));
        $diaInicioAssinatura = Tdata::getDiaData(Tdata::TdataMysql($request->get('data')));

        $internos = array();

        foreach($internosForm as $interno)
        {
            array_push($internos, array(
                'nome' => InternoController::getInternoNome($interno),
                'prontuario' => $interno,
                'localizacao' =>InternoController::getLotacaoInterno($interno),
                'pavilhao' => UnidadesController::getNomePav(InternoController::getIdpavilhaoInterno($interno)),
                'cela' => UnidadesController::getNomeCela(InternoController::getIdCelaInterno($interno)),
            ));
        }

        $pdf = \PDF::loadView('listagem.frequenciaSemiaberto.pdfFrequenciaSemiaberto', compact('internos', 'datas','qtdDiasMes','diaInicioAssinatura','mes','ano','mesNum'))
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 40)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('header-html', view('listagem.frequenciaSemiaberto.pdfHeaderFrequenciaSemiaberto', compact('siglaUnidade', 'mes', 'ano', 'nomeUnidade')))
            ->setOption('footer-center', utf8_decode('Gerado em' . date('\ d/m/Y\ ') . ' | Pagina [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');

        return $pdf->inline();


    }

    public function frequenciaServidores()
    {
        return view('listagem.servidores.frequenciaServidores');
    }

    public function frequenciaServidoresStore(Request $request)
    {
        ini_set('max_execution_time', 3600);

        //dd($request->all());

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $servidoresForm = $request->get('agentesHidden');
        $siglaUnidade = UnidadesController::getSiglaUp($lotacaoServidor);
        $nomeUnidade = UnidadesController::getNomeUp($lotacaoServidor);
        $mesNum = $request->get('mesImpressao');
        $ano = Tdata::getAnoData(Tdata::TdataMysql($request->get('data')));
        $qtdDiasMes = cal_days_in_month(CAL_GREGORIAN, $mesNum, $ano);


        $mes = Tdata::getMesExt($request->get('mesImpressao'));
        $diaInicioAssinatura = Tdata::getDiaData(Tdata::TdataMysql($request->get('data')));

        $servidores = array();

        foreach($servidoresForm as $servidorForm)
        {
            array_push($servidores, array(
                'nome' => FacadeController::getNomeServidor($servidorForm),
                'matricula' => $servidorForm,
            ));
        }

        $pdf = \PDF::loadView('listagem.servidores.pdfFrequenciaServidor', compact('servidores', 'qtdDiasMes','diaInicioAssinatura','mes','ano','mesNum','nomeUnidade'))
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 30)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('header-html', view('listagem.servidores.pdfHeaderFrequenciaServidor', compact('siglaUnidade', 'mes', 'ano', 'nomeUnidade')))
            ->setOption('footer-center', utf8_decode('Gerado em' . date('\ d/m/Y\ ') . ' | Pagina [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');

        return $pdf->inline();

    }

    public function frequenciaDiariaInternoCriarLista()
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $usuarioLogadoUP = DB::table('unidade')->where('sigla', FacadeController::getLotacaoServidor(Auth::user()->cpf))->get();
        $usuarioLogadoUP = json_decode($usuarioLogadoUP, true)[0];

        return view('listagem.frequenciaDiariaInterno.frequenciaDiaria', compact('usuarioLogadoUP'));
    }

    public static function frequenciaDiariaInternoCriarListaStore(Request $request)
    {

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

//       dd($request->all());

        $nomeLista = $request->get('nomeLista');
        $internos = $request->get('internos');
        $tipo = $request->get('tipo');
        $legenda = $request->get('legenda');

        $insertLista = DB::table('lista_frequencia')->insert([
            'unidade' => $lotacaoServidor,
            'tipo' => $tipo,
            'legenda' => $legenda,
            'nome' => $nomeLista,
            'status' => 'A',
            'created_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        $idFrequencia = DB::getPdo()->lastInsertId();

        foreach($internos as $interno)
        {
            $insertInternoLista = DB::table('lista_frequencia_interno')->insert([
                'fk_lista' => $idFrequencia,
                'fk_interno' => $interno,
                'status' => 'A',
                'created_at' => Tdata::TtimeStampHojeMysql(),
            ]);

        }

        if ($idFrequencia) {

            return redirect()->route('listagem.frequenciaDiariaInternoGerenciarListas')->with('alert-success', 'Lista Criada com sucesso');

        }


    }

    public function frequenciaDiariaInternoGerenciarListas()
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $listas = DB::table('lista_frequencia')->where('unidade',$lotacaoServidor)->select('lista_frequencia.*', DB::raw("DATE_FORMAT(created_at,'%d/%m/%Y') as dataCriacao"))->orderBy('created_at', 'DESC')->get();

        return view('listagem.frequenciaDiariaInterno.gerenciarListas', compact('listas'));
    }

    public function editarLista($id)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $lista = DB::table('lista_frequencia')->where('id', $id)->where('unidade',$lotacaoServidor)->get()[0];

        $listaInternos = DB::table("lista_frequencia_interno")->where('fk_lista', $id)->where('status','A')->get();

        $internos = array();
        foreach($listaInternos as $listaInterno)
        {
            array_push($internos, array(
                'id'=> $listaInterno->fk_interno,
                'nome'=> InternoController::getInternoNome($listaInterno->fk_interno),
                'faccao'=> InternoController::getFaccaoInterno($listaInterno->fk_interno),
                'lideranca'=> InternoController::getIsLideranca($listaInterno->fk_interno),
                'localizacao'=> InternoController::getLotacaoInterno($listaInterno->fk_interno),
            ));
        }

        return view('listagem.frequenciaDiariaInterno.editarLista', compact('lista','internos','id'));
    }

    public function editarListaStore(Request $request)
    {
//        dd($request->all());

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $diferencas = array();
        $alteracoes = "Alteração da listagem<br>";
        array_push($diferencas, $alteracoes);

        $dataForm = array('unidade'=> $lotacaoServidor, 'tipo' => $request->get('tipo'), 'nome' => $request->get('nomeLista'), 'status' => $request->get('status'), 'legenda' => $request->get('legenda'));

//        $dataForm['data'] = Tdata::TdataMysql($dataForm['data']);

        // LOG - array que armazena os textos do log
        $diferencas = array();

        $dadosBD = DB::table("lista_frequencia")->where('id', $request->get('idLista'))->get();
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


        $updatetLista = DB::table('lista_frequencia')->where('id', $request->get('idLista'))->update([
                'unidade' => $lotacaoServidor,
                'tipo' => $request->get('tipo'),
                'nome' => $request->get('nomeLista'),
                'status' => $request->get('status'),
                'legenda' => $request->get('legenda'),
                'created_at' => Tdata::TtimeStampHojeMysql(),
            ]
        );



        $internosRemover = $request->get('internosRemover');
        if(isset($internosRemover)){
            foreach ($internosRemover as $idInternoRemover) {
                $deleteInterno = DB::table('lista_frequencia_interno')
                    ->where('fk_lista', $request->get('idLista'))
                    ->where('fk_interno', $idInternoRemover)
                    ->update([
                        'status' => 'I'
                    ]);

                // LOG
                $alteracoes = "Inativação de interno da lista: <i><strong>".InternoController::getInternoNome($idInternoRemover)."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }

        }
        $internos = $request->input('internos');
        if(isset($internos)) {
            foreach ($internos as $interno) {
                if (is_numeric($interno)) {
                    $verificaInterno = DB::table('lista_frequencia_interno')->where('fk_lista', $request->get('idLista'))->where('fk_interno', $interno)->get();

                    if (empty(json_decode($verificaInterno))) {
                        $insertInternos = DB::table('lista_frequencia_interno')->insert([
                                'fk_lista' => $request->get('idLista'),
                                'fk_interno' => $interno,
                                'status' => "A",
                                'created_at' => Tdata::TtimeStampHojeMysql(),
                            ]
                        );

                        // LOG
                        $alteracoes = "Inserção de interno na lista: <i><strong>".InternoController::getInternoNome($interno)."</i></strong><br>";
                        array_push($diferencas, $alteracoes);

                    }
                }
            }
        }

        if($request)
        {
            $textoLog = implode(" ", $diferencas);
            LogController::log($request->get('idLista'),"lista_frequencia","update", Auth::user()->cpf, $textoLog);

            return redirect()->route('listagem.frequenciaDiariaInternoGerenciarListas')->with('alert-success', 'Lista alterada com sucesso!');


        }

    }
    public function frequenciaDiariaInternoRegistrarPresenca()
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $listas = DB::table('lista_frequencia')->where('status','A')->where('unidade',$lotacaoServidor) ->orderBy('nome', 'ASC')->get();

        return view('listagem.frequenciaDiariaInterno.registrarPresenca', compact('listas'));
    }

    public function frequenciaDiariaInternoRegistrarPresencaStore(Request $request)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $data = $request->get('data');
        $idStatus = $request->get('idStatus');
        $lista = $request->get('lista');

        $contador = count($idStatus);

        foreach($idStatus as $id)
        {
            $dados = explode(',',$id);

            $verificaRegistro = DB::table('lista_frequencia_interno_presenca')->where('fk_lista', $lista)->where('fk_interno',$dados[1])->where('data', Tdata::TdataMysql($data))->count();

            if($verificaRegistro == 0) {

                $insertFrequencia = DB::table('lista_frequencia_interno_presenca')->insert([
                    'fk_lista' => $lista,
                    'fk_interno' => $dados[1],
                    'data' => Tdata::TdataMysql($data),
                    'status' => $dados[0],
                    'created_at' => Tdata::TtimeStampHojeMysql(),
                ]);

                $contador = $contador - 1;
            }else{
                $contador = $contador - 1;
            }

        }

        if($contador == 0)
        {
            return redirect()->route('listagem.frequenciaDiariaInternoGerenciarListas')->with('alert-success', 'Presença inserida com sucesso');

        }
    }

    public function relatorioFrequenciaDiaria()
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $listas = DB::table('lista_frequencia')->where('status','A')->where('unidade',$lotacaoServidor) ->orderBy('nome', 'ASC')->get();

        return view('listagem.frequenciaDiariaInterno.relatorioFrequencia', compact('listas'));

    }

    public function relatorioFrequenciaDiariaStore(Request $request)
    {

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $mes = $request->get('mes');
        $ano = $request->get('ano');
        $lista = $request->get('lista');
        $qtdDiasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

        $dados = DB::table('lista_frequencia_interno_presenca')->where('fk_lista', $lista)->whereYear('data', $ano)->whereMonth('data',$mes)->get();
        $dadosLista = DB::table('lista_frequencia')->where('id', $lista)->get()[0];

        $internos = array();
        $faltasInterno = array();
        foreach($dados as $dado)
        {
            array_push($internos, array(
                'nome' => InternoController::getInternoNome($dado->fk_interno),
                'prontuario' => $dado->fk_interno,
                'dia' => Tdata::getDiaData($dado->data),
                'data' => Tdata::TdataBR($dado->data),
                'status' => $dado->status,
                'mes' => $mes,
                'ano' => $ano,
                'lista' => $lista,
            ));

            array_push($faltasInterno, array(
                'id' => $dado->fk_interno,
                'faltas' => self::getFaltasMes($lista, $dado->fk_interno,$mes,$ano),

            ));
        }

        $sort = [
            ['column' => 'dia', 'order' => 'asc'],
            ['column' => 'nome', 'order' => 'asc'],

        ];

        // dd($internos);

        $internos = collect($internos);
        $internos = self::multiPropertySort($internos, $sort);
        $internos = json_decode($internos, true);


        $relatorio = array();

        foreach($internos as $array){
            $relatorio[$array["nome"]][$array["dia"]][$array["status"]][] = $array;
        }

//        dd($relatorio);

        $siglaUnidade = UnidadesController::getSiglaUp($lotacaoServidor);
        $nomeUnidade = UnidadesController::getNomeUp($lotacaoServidor);

        $pdf = \PDF::loadView('listagem.frequenciaDiariaInterno.pdfRelatorioFrequencia', compact('relatorio', 'qtdDiasMes', 'mes','ano','faltasInterno'))
            ->setOption('orientation', 'landscape')
            ->setOption('margin-bottom', 12)
            ->setOption('margin-top', 30)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('header-html', view('listagem.frequenciaDiariaInterno.headerPdf', compact('dadosLista','siglaUnidade','nomeUnidade')))
            ->setOption('footer-html', view('listagem.frequenciaDiariaInterno.footerPdf', compact('dadosLista','siglaUnidade','nomeUnidade')))
            ->setOption('footer-center', utf8_decode('Gerado em' . date('\ d/m/Y\ ') . ' | Pagina [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');

        return $pdf->inline();

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