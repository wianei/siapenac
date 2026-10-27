<?php


/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a visita
|
*/

namespace app\Http\Controllers\estatisticas;

use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Http\Controllers\ArtigosProcessos\ArtigosProcessosController;
use App\Http\Controllers\Permissoes\PermissoesController;
use App\Http\Controllers\Interno\InternoController;
use Illuminate\Http\Request;
use App\Classes\Tdata;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Session;

class EstatisticasController extends Controller
{

    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->middleware('auth');
    }

    public function callAction($method, $parameters)
    {
        LogController::logAcessoUser();
        
        $except = collect(["internoPdfNew"]);
        
        if(!$except->contains($method))
        {
            $retornoPermissao = PermissoesController::verifyPermission("EstatisticasController", $method);
            
            if($retornoPermissao == false)
            {
                return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
            }
        }

        return parent::callAction($method, $parameters);
    }

    public function internosUnidade(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if(!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if(!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if(!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if(!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        $qtdInternosLotSelecionada = DB::table('interno')->where('status','A');


        $qtdUnidadeHA = DB::table('interno')
            ->leftJoin('unidade','unidade.id','interno.unidade_prisional')
            ->where('sexo', 'M')->where('interno.status','A')
            ->select(DB::raw('unidade.sigla, count(*) as qtd'))
            ->groupBy('unidade.sigla')->orderBy('qtd','DESC');

        $qtdUnidadeMA = DB::table('interno')
            ->leftJoin('unidade','unidade.id','interno.unidade_prisional')
            ->where('sexo', 'F')->where('interno.status','A')
            ->select(DB::raw('unidade.sigla, count(*) as qtd'))
            ->groupBy('unidade.sigla')->orderBy('qtd','DESC');

        $qtdUnidadeTotal  = DB::table('interno')
            ->leftJoin('unidade','unidade.id','interno.unidade_prisional')
            ->select(DB::raw('unidade.sigla, count(*) as qtd'))
            ->where('interno.status','A')->groupBy('unidade.sigla')->orderBy('qtd','DESC');

        if(!is_null($unidade)) {
            $qtdUnidadeHA->where('unidade_prisional', $unidade);
            $qtdUnidadeMA->where('unidade_prisional', $unidade);
            $qtdUnidadeTotal->where('unidade_prisional', $unidade);

            $qtdInternosLotSelecionada->where('unidade_prisional', $unidade);

        }

        if(!is_null($pavilhao)) {
            $qtdUnidadeHA->where('pavilhao', $pavilhao);
            $qtdUnidadeMA->where('pavilhao', $pavilhao);
            $qtdUnidadeTotal->where('pavilhao', $pavilhao);

            $qtdInternosLotSelecionada->where('pavilhao', $pavilhao);

        }

        if(!is_null($ala)) {
            $qtdUnidadeHA->where('ala', $ala);
            $qtdUnidadeMA->where('ala', $ala);
            $qtdUnidadeTotal->where('ala', $ala);

            $qtdInternosLotSelecionada->where('ala', $ala);

        }

        if(!is_null($cela)) {
            $qtdUnidadeHA->where('cela', $cela);
            $qtdUnidadeMA->where('cela', $cela);
            $qtdUnidadeTotal->where('cela', $cela);

            $qtdInternosLotSelecionada->where('cela', $cela);

        }


        $qtdUnidadeHA = $qtdUnidadeHA->get();
        $qtdUnidadeMA = $qtdUnidadeMA->get();
        $qtdUnidadeTotal = $qtdUnidadeTotal->get();

        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-interno-unidade', compact('unidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome', 'qtdUnidadeTotal',
            'qtdUnidadeHA', 'qtdUnidadeMA','dataHora', 'detalhesUnidades', 'qtdInternosLotSelecionada'));

    }

    public function regimeUnidade(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if(!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if(!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if(!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if(!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        $qtdRegimeNIH = DB::table('interno')->where('regime', null)->where('sentenciado','S')->where('sexo','M')->where('medida_seguranca','=','N')->where('status','A');
        $qtdRegimeNIM = DB::table('interno')->where('regime', null)->where('sentenciado','S')->where('sexo','F')->where('medida_seguranca','=','N')->where('status','A');
        $qtdRegimeFechadoH = DB::table('interno')->where('sexo','M')->where('regime', 'FECHADO')->where('status','A');
        $qtdRegimeFechadoM = DB::table('interno')->where('sexo','F')->where('regime', 'FECHADO')->where('status','A');
        $qtdRegimeSemiabertoH = DB::table('interno')->where('sexo','M')->where('regime', 'SEMIABERTO')->where('status','A');
        $qtdRegimeSemiabertoM = DB::table('interno')->where('sexo','F')->where('regime', 'SEMIABERTO')->where('status','A');
        $qtdRegimeAbertoH = DB::table('interno')->where('sexo','M')->where('regime', 'ABERTO')->where('status','A');
        $qtdRegimeAbertoM = DB::table('interno')->where('sexo','F')->where('regime', 'ABERTO')->where('status','A');

        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-regime-unidade', compact('qtdRegimeNIH', 'qtdRegimeNIM', 'qtdRegimeFechadoH', 'qtdRegimeFechadoM', 'qtdRegimeSemiabertoH', 'qtdRegimeSemiabertoM',
            'qtdRegimeAbertoH', 'qtdRegimeAbertoM','dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome'));
    }

    public function estatisticaSexo(Request $request)
    {
        $unidade = $request->get('unidade_prisional');
        if(!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if(!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if(!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if(!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        $qtdHA = DB::table('interno')->where('sexo', 'M')->where('status','A');
        $qtdMA = DB::table('interno')->where('sexo', 'F')->where('status','A');

        if(!is_null($unidade)) {

            $qtdHA->where('unidade_prisional', $unidade);
            $qtdMA->where('unidade_prisional', $unidade);
        }

        if(!is_null($pavilhao)) {

            $qtdHA->where('pavilhao', $pavilhao);
            $qtdMA->where('pavilhao', $pavilhao);

        }

        if(!is_null($ala)) {

            $qtdHA->where('ala', $ala);
            $qtdMA->where('ala', $ala);

        }

        if(!is_null($cela)) {

            $qtdHA->where('cela', $cela);
            $qtdMA->where('cela', $cela);

        }

        $qtdHA=$qtdHA->count();
        $qtdMA=$qtdMA->count();

        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-sexo', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome',
            'celaNome', 'qtdHA', 'qtdMA'));
    }

    public function regimePrisional(Request $request)
    {
        $unidade = $request->get('unidade_prisional');
        if(!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if(!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if(!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if(!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        $qtdRegimeNIH = DB::table('interno')->where('regime', null)->where('sentenciado','S')->where('sexo','M')->where('medida_seguranca','=','N')->where('status','A');
        $qtdRegimeNIM = DB::table('interno')->where('regime', null)->where('sentenciado','S')->where('sexo','F')->where('medida_seguranca','=','N')->where('status','A');
        $qtdRegimeFechadoH = DB::table('interno')->where('sexo','M')->where('regime', 'FECHADO')->where('status','A');
        $qtdRegimeFechadoM = DB::table('interno')->where('sexo','F')->where('regime', 'FECHADO')->where('status','A');
        $qtdRegimeSemiabertoH = DB::table('interno')->where('sexo','M')->where('regime', 'SEMIABERTO')->where('status','A');
        $qtdRegimeSemiabertoM = DB::table('interno')->where('sexo','F')->where('regime', 'SEMIABERTO')->where('status','A');
        $qtdRegimeAbertoH = DB::table('interno')->where('sexo','M')->where('regime', 'ABERTO')->where('status','A');
        $qtdRegimeAbertoM = DB::table('interno')->where('sexo','F')->where('regime', 'ABERTO')->where('status','A');

        if(!is_null($unidade)) {

            $qtdRegimeNIH->where('unidade_prisional', $unidade);
            $qtdRegimeNIM->where('unidade_prisional', $unidade);
            $qtdRegimeFechadoH->where('unidade_prisional', $unidade);
            $qtdRegimeFechadoM->where('unidade_prisional', $unidade);
            $qtdRegimeSemiabertoH->where('unidade_prisional', $unidade);
            $qtdRegimeSemiabertoM->where('unidade_prisional', $unidade);
            $qtdRegimeAbertoH->where('unidade_prisional', $unidade);
            $qtdRegimeAbertoM->where('unidade_prisional', $unidade);

        }

        if(!is_null($pavilhao)) {

            $qtdRegimeNIH->where('pavilhao', $pavilhao);
            $qtdRegimeNIM->where('pavilhao', $pavilhao);
            $qtdRegimeFechadoH->where('pavilhao', $pavilhao);
            $qtdRegimeFechadoM->where('pavilhao', $pavilhao);
            $qtdRegimeSemiabertoH->where('pavilhao', $pavilhao);
            $qtdRegimeSemiabertoM->where('pavilhao', $pavilhao);
            $qtdRegimeAbertoH->where('pavilhao', $pavilhao);
            $qtdRegimeAbertoM->where('pavilhao', $pavilhao);

        }

        if(!is_null($ala)) {

            $qtdRegimeNIH->where('ala', $ala);
            $qtdRegimeNIM->where('ala', $ala);
            $qtdRegimeFechadoH->where('ala', $ala);
            $qtdRegimeFechadoM->where('ala', $ala);
            $qtdRegimeSemiabertoH->where('ala', $ala);
            $qtdRegimeSemiabertoM->where('ala', $ala);
            $qtdRegimeAbertoH->where('ala', $ala);
            $qtdRegimeAbertoM->where('ala', $ala);

        }

        if(!is_null($cela)) {

            $qtdRegimeNIH->where('cela', $cela);
            $qtdRegimeNIM->where('cela', $cela);
            $qtdRegimeFechadoH->where('cela', $cela);
            $qtdRegimeFechadoM->where('cela', $cela);
            $qtdRegimeSemiabertoH->where('cela', $cela);
            $qtdRegimeSemiabertoM->where('cela', $cela);
            $qtdRegimeAbertoH->where('cela', $cela);
            $qtdRegimeAbertoM->where('cela', $cela);
        }


        $qtdRegimeNIH=$qtdRegimeNIH->count();
        $qtdRegimeNIM=$qtdRegimeNIM->count();
        $qtdRegimeFechadoH=$qtdRegimeFechadoH->count();
        $qtdRegimeFechadoM=$qtdRegimeFechadoM->count();
        $qtdRegimeSemiabertoH=$qtdRegimeSemiabertoH->count();
        $qtdRegimeSemiabertoM=$qtdRegimeSemiabertoM->count();
        $qtdRegimeAbertoH=$qtdRegimeAbertoH->count();
        $qtdRegimeAbertoM=$qtdRegimeAbertoM->count();

        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-regime-prisional', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'qtdRegimeNIM', 'qtdRegimeFechadoH', 'qtdRegimeFechadoM', 'qtdRegimeSemiabertoH', 'qtdRegimeSemiabertoM', 'qtdRegimeAbertoH',
            'qtdRegimeAbertoM', 'qtdRegimeNIH', 'celaNome'));
    }

    public function sentProvSemMs(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if(!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if(!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if(!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if(!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        $qtdInternosLotSelecionada = DB::table('interno')->where('status','A');

        $qtdSentenciadosNIH = DB::table('interno')->where('sexo','M')->where('sentenciado', null)->where('medida_seguranca','N')->where('status','A');
        $qtdSentenciadosNIM = DB::table('interno')->where('sexo','F')->where('sentenciado', null)->where('medida_seguranca','N')->where('status','A');
        $qtdSentenciadosH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'S')->where('medida_seguranca','N')->where('status','A');
        $qtdSentenciadosM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'S')->where('medida_seguranca','N')->where('status','A');
        $qtdProvisoriosH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'N')->where('medida_seguranca','N')->where('status','A');
        $qtdProvisoriosM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'N')->where('medida_seguranca','N')->where('status','A');


        if(!is_null($unidade)) {

            $qtdSentenciadosNIH->where('unidade_prisional', $unidade);
            $qtdSentenciadosNIM->where('unidade_prisional', $unidade);
            $qtdSentenciadosH->where('unidade_prisional', $unidade);
            $qtdSentenciadosM->where('unidade_prisional', $unidade);
            $qtdProvisoriosH->where('unidade_prisional', $unidade);
            $qtdProvisoriosM->where('unidade_prisional', $unidade);

        }

        if(!is_null($pavilhao)) {

            $qtdSentenciadosNIH->where('pavilhao', $pavilhao);
            $qtdSentenciadosNIM->where('pavilhao', $pavilhao);
            $qtdSentenciadosH->where('pavilhao', $pavilhao);
            $qtdSentenciadosM->where('pavilhao', $pavilhao);
            $qtdProvisoriosH->where('pavilhao', $pavilhao);
            $qtdProvisoriosM->where('pavilhao', $pavilhao);

        }

        if(!is_null($ala)) {

            $qtdSentenciadosNIH->where('ala', $ala);
            $qtdSentenciadosNIM->where('ala', $ala);
            $qtdSentenciadosH->where('ala', $ala);
            $qtdSentenciadosM->where('ala', $ala);
            $qtdProvisoriosH->where('ala', $ala);
            $qtdProvisoriosM->where('ala', $ala);

        }
        if(!is_null($cela)) {

            $qtdSentenciadosNIH->where('cela', $cela);
            $qtdSentenciadosNIM->where('cela', $cela);
            $qtdSentenciadosH->where('cela', $cela);
            $qtdSentenciadosM->where('cela', $cela);
            $qtdProvisoriosH->where('cela', $cela);
            $qtdProvisoriosM->where('cela', $cela);

        }

        $qtdSentenciadosNIH=$qtdSentenciadosNIH->count();
        $qtdSentenciadosNIM=$qtdSentenciadosNIM->count();
        $qtdSentenciadosH=$qtdSentenciadosH->count();
        $qtdSentenciadosM=$qtdSentenciadosM->count();
        $qtdProvisoriosH=$qtdProvisoriosH->count();
        $qtdProvisoriosM=$qtdProvisoriosM->count();


        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-sentenciados-prov-sem-ms', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
        'qtdInternosLotSelecionada', 'qtdSentenciadosNIH', 'qtdSentenciadosNIM', 'qtdSentenciadosH', 'qtdSentenciadosM','qtdProvisoriosH','qtdProvisoriosM'));


    }

    public function sentProvEmMs(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if(!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if(!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if(!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if(!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        $qtdInternosLotSelecionada = DB::table('interno')->where('status','A');

        $qtdSentenciadosMSNIH = DB::table('interno')->where('sexo','M')->where('sentenciado', null)->where('medida_seguranca','S')->where('status','A');
        $qtdSentenciadosMSNIM = DB::table('interno')->where('sexo','F')->where('sentenciado', null)->where('medida_seguranca','S')->where('status','A');
        $qtdSentenciadosMSH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'S')->where('medida_seguranca','S')->where('status','A');
        $qtdSentenciadosMSM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'S')->where('medida_seguranca','S')->where('status','A');
        $qtdProvisoriosMSH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'N')->where('medida_seguranca','S')->where('status','A');
        $qtdProvisoriosMSM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'N')->where('medida_seguranca','S')->where('status','A');

        if(!is_null($unidade)) {

            $qtdSentenciadosMSNIH->where('unidade_prisional', $unidade);
            $qtdSentenciadosMSNIM->where('unidade_prisional', $unidade);
            $qtdSentenciadosMSH->where('unidade_prisional', $unidade);
            $qtdSentenciadosMSM->where('unidade_prisional', $unidade);
            $qtdProvisoriosMSH->where('unidade_prisional', $unidade);
            $qtdProvisoriosMSM->where('unidade_prisional', $unidade);

        }

        if(!is_null($pavilhao)) {

            $qtdSentenciadosMSNIH->where('pavilhao', $pavilhao);
            $qtdSentenciadosMSNIM->where('pavilhao', $pavilhao);
            $qtdSentenciadosMSH->where('pavilhao', $pavilhao);
            $qtdSentenciadosMSM->where('pavilhao', $pavilhao);
            $qtdProvisoriosMSH->where('pavilhao', $pavilhao);
            $qtdProvisoriosMSM->where('pavilhao', $pavilhao);

        }

        if(!is_null($ala)) {

            $qtdSentenciadosMSNIH->where('ala', $ala);
            $qtdSentenciadosMSNIM->where('ala', $ala);
            $qtdSentenciadosMSH->where('ala', $ala);
            $qtdSentenciadosMSM->where('ala', $ala);
            $qtdProvisoriosMSH->where('ala', $ala);
            $qtdProvisoriosMSM->where('ala', $ala);

        }

        if(!is_null($cela)) {

            $qtdSentenciadosMSNIH->where('cela', $cela);
            $qtdSentenciadosMSNIM->where('cela', $cela);
            $qtdSentenciadosMSH->where('cela', $cela);
            $qtdSentenciadosMSM->where('cela', $cela);
            $qtdProvisoriosMSH->where('cela', $cela);
            $qtdProvisoriosMSM->where('cela', $cela);

        }

        $qtdSentenciadosMSNIH=$qtdSentenciadosMSNIH->count();
        $qtdSentenciadosMSNIM=$qtdSentenciadosMSNIM->count();
        $qtdSentenciadosMSH=$qtdSentenciadosMSH->count();
        $qtdSentenciadosMSM=$qtdSentenciadosMSM->count();
        $qtdProvisoriosMSH=$qtdProvisoriosMSH->count();
        $qtdProvisoriosMSM=$qtdProvisoriosMSM->count();

        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-sentenciados-prov-em-ms', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
            'qtdInternosLotSelecionada', 'qtdSentenciadosMSNIH', 'qtdSentenciadosMSNIM', 'qtdSentenciadosMSH', 'qtdSentenciadosMSM','qtdProvisoriosMSH','qtdProvisoriosMSM'));


    }

    public function sentProvIncMs(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if(!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if(!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if(!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if(!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        $qtdInternosLotSelecionada = DB::table('interno')->where('status','A');

        $qtdSentenciadosNIH = DB::table('interno')->where('sexo','M')->where('sentenciado', null)->where('medida_seguranca','N')->where('status','A');
        $qtdSentenciadosNIM = DB::table('interno')->where('sexo','F')->where('sentenciado', null)->where('medida_seguranca','N')->where('status','A');
        $qtdSentenciadosH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'S')->where('medida_seguranca','N')->where('status','A');
        $qtdSentenciadosM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'S')->where('medida_seguranca','N')->where('status','A');
        $qtdProvisoriosH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'N')->where('medida_seguranca','N')->where('status','A');
        $qtdProvisoriosM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'N')->where('medida_seguranca','N')->where('status','A');
        $qtdSentenciadosMSNIH = DB::table('interno')->where('sexo','M')->where('sentenciado', null)->where('medida_seguranca','S')->where('status','A');
        $qtdSentenciadosMSNIM = DB::table('interno')->where('sexo','F')->where('sentenciado', null)->where('medida_seguranca','S')->where('status','A');
        $qtdSentenciadosMSH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'S')->where('medida_seguranca','S')->where('status','A');
        $qtdSentenciadosMSM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'S')->where('medida_seguranca','S')->where('status','A');
        $qtdProvisoriosMSH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'N')->where('medida_seguranca','S')->where('status','A');
        $qtdProvisoriosMSM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'N')->where('medida_seguranca','S')->where('status','A');

        if(!is_null($unidade)) {

            $qtdSentenciadosMSNIH->where('unidade_prisional', $unidade);
            $qtdSentenciadosMSNIM->where('unidade_prisional', $unidade);
            $qtdSentenciadosMSH->where('unidade_prisional', $unidade);
            $qtdSentenciadosMSM->where('unidade_prisional', $unidade);
            $qtdProvisoriosMSH->where('unidade_prisional', $unidade);
            $qtdProvisoriosMSM->where('unidade_prisional', $unidade);
            $qtdSentenciadosNIH->where('unidade_prisional', $unidade);
            $qtdSentenciadosNIM->where('unidade_prisional', $unidade);
            $qtdSentenciadosH->where('unidade_prisional', $unidade);
            $qtdSentenciadosM->where('unidade_prisional', $unidade);
            $qtdProvisoriosH->where('unidade_prisional', $unidade);
            $qtdProvisoriosM->where('unidade_prisional', $unidade);


        }

        if(!is_null($pavilhao)) {

            $qtdSentenciadosMSNIH->where('pavilhao', $pavilhao);
            $qtdSentenciadosMSNIM->where('pavilhao', $pavilhao);
            $qtdSentenciadosMSH->where('pavilhao', $pavilhao);
            $qtdSentenciadosMSM->where('pavilhao', $pavilhao);
            $qtdProvisoriosMSH->where('pavilhao', $pavilhao);
            $qtdProvisoriosMSM->where('pavilhao', $pavilhao);
            $qtdSentenciadosNIM->where('pavilhao', $pavilhao);
            $qtdSentenciadosH->where('pavilhao', $pavilhao);
            $qtdSentenciadosM->where('pavilhao', $pavilhao);
            $qtdProvisoriosH->where('pavilhao', $pavilhao);
            $qtdProvisoriosM->where('pavilhao', $pavilhao);
            $qtdSentenciadosNIH->where('pavilhao', $pavilhao);


        }

        if(!is_null($ala)) {

            $qtdSentenciadosMSNIH->where('ala', $ala);
            $qtdSentenciadosMSNIM->where('ala', $ala);
            $qtdSentenciadosMSH->where('ala', $ala);
            $qtdSentenciadosMSM->where('ala', $ala);
            $qtdProvisoriosMSH->where('ala', $ala);
            $qtdProvisoriosMSM->where('ala', $ala);
            $qtdSentenciadosNIH->where('ala', $ala);
            $qtdSentenciadosNIM->where('ala', $ala);
            $qtdSentenciadosH->where('ala', $ala);
            $qtdSentenciadosM->where('ala', $ala);
            $qtdProvisoriosH->where('ala', $ala);
            $qtdProvisoriosM->where('ala', $ala);

        }

        if(!is_null($cela)) {

            $qtdSentenciadosMSNIH->where('cela', $cela);
            $qtdSentenciadosMSNIM->where('cela', $cela);
            $qtdSentenciadosMSH->where('cela', $cela);
            $qtdSentenciadosMSM->where('cela', $cela);
            $qtdProvisoriosMSH->where('cela', $cela);
            $qtdProvisoriosMSM->where('cela', $cela);
            $qtdSentenciadosNIH->where('cela', $cela);
            $qtdSentenciadosNIM->where('cela', $cela);
            $qtdSentenciadosH->where('cela', $cela);
            $qtdSentenciadosM->where('cela', $cela);
            $qtdProvisoriosH->where('cela', $cela);
            $qtdProvisoriosM->where('cela', $cela);

        }

        $qtdSentenciadosMSNIH=$qtdSentenciadosMSNIH->count();
        $qtdSentenciadosMSNIM=$qtdSentenciadosMSNIM->count();
        $qtdSentenciadosMSH=$qtdSentenciadosMSH->count();
        $qtdSentenciadosMSM=$qtdSentenciadosMSM->count();
        $qtdProvisoriosMSH=$qtdProvisoriosMSH->count();
        $qtdProvisoriosMSM=$qtdProvisoriosMSM->count();
        $qtdSentenciadosNIH=$qtdSentenciadosNIH->count();
        $qtdSentenciadosNIM=$qtdSentenciadosNIM->count();
        $qtdSentenciadosH=$qtdSentenciadosH->count();
        $qtdSentenciadosM=$qtdSentenciadosM->count();
        $qtdProvisoriosH=$qtdProvisoriosH->count();
        $qtdProvisoriosM=$qtdProvisoriosM->count();

        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-sentenciados-prov-inclusive-ms', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
            'qtdInternosLotSelecionada', 'qtdSentenciadosMSNIH', 'qtdSentenciadosMSNIM', 'qtdSentenciadosMSH', 'qtdSentenciadosMSM','qtdProvisoriosMSH','qtdProvisoriosMSM',
            'qtdSentenciadosNIH', 'qtdSentenciadosNIM', 'qtdSentenciadosH', 'qtdSentenciadosM','qtdProvisoriosH','qtdProvisoriosM'));


    }

    public function medidaSeguranca(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if (!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if (!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if (!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if (!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status', '1')->get();

        $qtdInternosLotSelecionada = DB::table('interno')->where('status', 'A');

        $qtdMedidaDeSegurancaH = DB::table('interno')->where('sexo', 'M')->where('medida_seguranca', 'S')->where('status', 'A');
        $qtdMedidaDeSegurancaM = DB::table('interno')->where('sexo', 'F')->where('medida_seguranca', 'S')->where('status', 'A');


        if (!is_null($unidade)) {

            $qtdMedidaDeSegurancaH->where('unidade_prisional', $unidade);
            $qtdMedidaDeSegurancaM->where('unidade_prisional', $unidade);

        }

        if (!is_null($pavilhao)) {

            $qtdMedidaDeSegurancaH->where('pavilhao', $pavilhao);
            $qtdMedidaDeSegurancaM->where('pavilhao', $pavilhao);

        }

        if (!is_null($ala)) {

            $qtdMedidaDeSegurancaH->where('ala', $ala);
            $qtdMedidaDeSegurancaM->where('ala', $ala);

        }
        if (!is_null($cela)) {

            $qtdMedidaDeSegurancaH->where('cela', $cela);
            $qtdMedidaDeSegurancaM->where('cela', $cela);

        }

        $qtdMedidaDeSegurancaH = $qtdMedidaDeSegurancaH->count();
        $qtdMedidaDeSegurancaM = $qtdMedidaDeSegurancaM->count();


        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-medida-seguranca', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
            'qtdInternosLotSelecionada', 'qtdMedidaDeSegurancaH', 'qtdMedidaDeSegurancaM'));

    }

    public function faixaEtaria(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if (!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if (!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if (!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if (!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status', '1')->get();

        $qtdInternosLotSelecionada = DB::table('interno')->where('status', 'A');

        $faixaEtaria_naoInformadaH = 0;
        $faixaEtaria_zero_a_dezesseteH = 0;
        $faixaEtaria_dezoito_a_vinteequatroH = 0;
        $faixaEtaria_vinteecinco_a_vinteenoveH = 0;
        $faixaEtaria_trinta_a_trintaequatroH = 0;
        $faixaEtaria_trintaecinco_a_quarentaecincoH = 0;
        $faixaEtaria_quarentaeseis_a_sessentaH = 0;
        $faixaEtaria_sessentaeum_a_setentaH = 0;
        $faixaEtaria_maisDeSetentaH = 0;

        $faixaEtaria_naoInformadaM = 0;
        $faixaEtaria_zero_a_dezesseteM = 0;
        $faixaEtaria_dezoito_a_vinteequatroM = 0;
        $faixaEtaria_vinteecinco_a_vinteenoveM = 0;
        $faixaEtaria_trinta_a_trintaequatroM = 0;
        $faixaEtaria_trintaecinco_a_quarentaecincoM = 0;
        $faixaEtaria_quarentaeseis_a_sessentaM = 0;
        $faixaEtaria_sessentaeum_a_setentaM = 0;
        $faixaEtaria_maisDeSetentaM = 0;

        $internosH = DB::table('interno')->select('data_nascimento')->where('sexo','M')->where('status','A');
        $internosM = DB::table('interno')->select('data_nascimento')->where('sexo','F')->where('status','A');

        if (!is_null($unidade)) {

            $internosH->where('unidade_prisional',$unidade);
            $internosM->where('unidade_prisional',$unidade);

        }

        if (!is_null($pavilhao)) {

            $internosH->where('pavilhao',$pavilhao);
            $internosM->where('pavilhao',$pavilhao);

        }

        if (!is_null($ala)) {

            $internosH->where('ala',$ala);
            $internosM->where('ala',$ala);

        }
        if (!is_null($cela)) {

            $internosH->where('cela',$cela);
            $internosM->where('cela',$cela);

        }


        $internosH=$internosH->get();
        foreach($internosH as $interno){
            if(is_null($interno->data_nascimento)){
                $faixaEtaria_naoInformadaH++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 17){
                $faixaEtaria_zero_a_dezesseteH++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 18 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 24){
                $faixaEtaria_dezoito_a_vinteequatroH++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 25 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 29){
                $faixaEtaria_vinteecinco_a_vinteenoveH++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 30 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 34){
                $faixaEtaria_trinta_a_trintaequatroH++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 35 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 45){
                $faixaEtaria_trintaecinco_a_quarentaecincoH++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 46 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 60){
                $faixaEtaria_quarentaeseis_a_sessentaH++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 61 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 70){
                $faixaEtaria_sessentaeum_a_setentaH++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) > 70){
                $faixaEtaria_maisDeSetentaH++;
            }
        }

        $internosM=$internosM->get();
        foreach($internosM as $interno){
            if(is_null($interno->data_nascimento)){
                $faixaEtaria_naoInformadaM++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 17){
                $faixaEtaria_zero_a_dezesseteM++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 18 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 24){
                $faixaEtaria_dezoito_a_vinteequatroM++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 25 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 29){
                $faixaEtaria_vinteecinco_a_vinteenoveM++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 30 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 34){
                $faixaEtaria_trinta_a_trintaequatroM++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 35 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 45){
                $faixaEtaria_trintaecinco_a_quarentaecincoM++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 46 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 60){
                $faixaEtaria_quarentaeseis_a_sessentaM++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 61 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 70){
                $faixaEtaria_sessentaeum_a_setentaM++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) > 70){
                $faixaEtaria_maisDeSetentaM++;
            }
        }


        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-faixa-etaria', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
            'qtdInternosLotSelecionada', 'faixaEtaria_naoInformadaH', 'faixaEtaria_zero_a_dezesseteH', 'faixaEtaria_dezoito_a_vinteequatroH', 'faixaEtaria_vinteecinco_a_vinteenoveH',
            'faixaEtaria_trinta_a_trintaequatroH', 'faixaEtaria_trintaecinco_a_quarentaecincoH', 'faixaEtaria_quarentaeseis_a_sessentaH', 'faixaEtaria_sessentaeum_a_setentaH',
            'faixaEtaria_maisDeSetentaH', 'faixaEtaria_naoInformadaM', 'faixaEtaria_zero_a_dezesseteM', 'faixaEtaria_dezoito_a_vinteequatroM', 'faixaEtaria_vinteecinco_a_vinteenoveM',
            'faixaEtaria_trinta_a_trintaequatroM', 'faixaEtaria_trintaecinco_a_quarentaecincoM', 'faixaEtaria_quarentaeseis_a_sessentaM', 'faixaEtaria_sessentaeum_a_setentaM',
            'faixaEtaria_maisDeSetentaM'));

    }

    public function nacionalidade(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if (!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if (!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if (!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if (!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status', '1')->get();


        $qtdNacionalidadeNIH = DB::table('interno')->where('naturalidade_pais',null)->where('sexo','M')->where('status','A');
        $qtdNacionalidadeNIM = DB::table('interno')->where('naturalidade_pais',null)->where('sexo','F')->where('status','A');


        if (!is_null($unidade)) {

            $qtdNacionalidadeNIH->where('unidade_prisional',$unidade);
            $qtdNacionalidadeNIM->where('unidade_prisional',$unidade);

        }

        if (!is_null($pavilhao)) {

            $qtdNacionalidadeNIH->where('pavilhao',$pavilhao);
            $qtdNacionalidadeNIM->where('pavilhao',$pavilhao);

        }

        if (!is_null($ala)) {

            $qtdNacionalidadeNIH->where('ala',$ala);
            $qtdNacionalidadeNIM->where('ala',$ala);

        }
        if (!is_null($cela)) {

            $qtdNacionalidadeNIH->where('cela',$cela);
            $qtdNacionalidadeNIM->where('cela',$cela);

        }

        $nacionalidades = DB::table('interno')->select(DB::raw('count(*) as qtdd, naturalidade_pais'))
            ->where('status','A')
            ->groupBy('naturalidade_pais')->orderBy('qtdd','DESC')->get();
        foreach($nacionalidades as $key=>$nacionalidade){
            $nacionalidades[$key]->qtdH = DB::table('interno')->where('sexo','M')->where('status','A')->where('naturalidade_pais', $nacionalidade->naturalidade_pais);
            $nacionalidades[$key]->qtdM = DB::table('interno')->where('sexo','F')->where('status','A')->where('naturalidade_pais', $nacionalidade->naturalidade_pais);

            if(!is_null($unidade)) {
                $nacionalidades[$key]->qtdH->where('unidade_prisional',$unidade);
                $nacionalidades[$key]->qtdM->where('unidade_prisional',$unidade);
            }
            if(!is_null($pavilhao)) {
                $nacionalidades[$key]->qtdH->where('pavilhao',$pavilhao);
                $nacionalidades[$key]->qtdM->where('pavilhao',$pavilhao);
            }
            if(!is_null($ala)) {
                $nacionalidades[$key]->qtdH->where('ala',$ala);
                $nacionalidades[$key]->qtdM->where('ala',$ala);
            }
            if(!is_null($cela)) {
                $nacionalidades[$key]->qtdH->where('cela',$cela);
                $nacionalidades[$key]->qtdM->where('cela',$cela);
            }

            $nacionalidades[$key]->qtdH = $nacionalidades[$key]->qtdH->count();
            $nacionalidades[$key]->qtdM = $nacionalidades[$key]->qtdM->count();
            $nacionalidades[$key]->qtd = $nacionalidades[$key]->qtdH + $nacionalidades[$key]->qtdM;
        }

        $qtdNacionalidadeNIH = $qtdNacionalidadeNIH->count();
        $qtdNacionalidadeNIM = $qtdNacionalidadeNIM->count();


        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-nacionalidade', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
            'qtdNacionalidadeNIH', 'qtdNacionalidadeNIM', 'nacionalidades'));

    }

    public function estadoCivil(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if (!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if (!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if (!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if (!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status', '1')->get();

        $qtdInternosLotSelecionada = DB::table('interno')->where('status', 'A');

        $qtdSolteirosH = DB::table('interno')->where('estado_civil','solteiro')->where('sexo','M')->where('status','A');
        $qtdSolteirosM = DB::table('interno')->where('estado_civil','solteiro')->where('sexo','F')->where('status','A');
        $qtdCasadosH = DB::table('interno')->where('estado_civil','casado')->where('sexo','M')->where('status','A');
        $qtdCasadosM = DB::table('interno')->where('estado_civil','casado')->where('sexo','F')->where('status','A');
        $qtdUniaoEstavelH = DB::table('interno')->where('estado_civil','uniao estavel')->where('sexo','M')->where('status','A');
        $qtdUniaoEstavelM = DB::table('interno')->where('estado_civil','uniao estavel')->where('sexo','F')->where('status','A');
        $qtdSeparadosH = DB::table('interno')->where('estado_civil','separado')->where('sexo','M')->where('status','A');
        $qtdSeparadosM = DB::table('interno')->where('estado_civil','separado')->where('sexo','F')->where('status','A');
        $qtdDivorciadosH = DB::table('interno')->where('estado_civil','divorciado')->where('sexo','M')->where('status','A');
        $qtdDivorciadosM = DB::table('interno')->where('estado_civil','divorciado')->where('sexo','F')->where('status','A');
        $qtdViuvosH = DB::table('interno')->where('estado_civil','viuvo')->where('sexo','M')->where('status','A');
        $qtdViuvosM = DB::table('interno')->where('estado_civil','viuvo')->where('sexo','F')->where('status','A');
        $qtdEstadoCivilNIH = DB::table('interno')->where('estado_civil',null)->where('sexo','M')->where('status','A');
        $qtdEstadoCivilNIM = DB::table('interno')->where('estado_civil',null)->where('sexo','F')->where('status','A');


        if (!is_null($unidade)) {

            $qtdSolteirosH->where('unidade_prisional',$unidade);
            $qtdSolteirosM->where('unidade_prisional',$unidade);
            $qtdCasadosH->where('unidade_prisional',$unidade);
            $qtdCasadosM->where('unidade_prisional',$unidade);
            $qtdUniaoEstavelH->where('unidade_prisional',$unidade);
            $qtdUniaoEstavelM->where('unidade_prisional',$unidade);
            $qtdSeparadosH->where('unidade_prisional',$unidade);
            $qtdSeparadosM->where('unidade_prisional',$unidade);
            $qtdDivorciadosH->where('unidade_prisional',$unidade);
            $qtdDivorciadosM->where('unidade_prisional',$unidade);
            $qtdViuvosH->where('unidade_prisional',$unidade);
            $qtdViuvosM->where('unidade_prisional',$unidade);
            $qtdEstadoCivilNIH->where('unidade_prisional',$unidade);
            $qtdEstadoCivilNIM->where('unidade_prisional',$unidade);

        }

        if (!is_null($pavilhao)) {

            $qtdSolteirosH->where('pavilhao',$pavilhao);
            $qtdSolteirosM->where('pavilhao',$pavilhao);
            $qtdCasadosH->where('pavilhao',$pavilhao);
            $qtdCasadosM->where('pavilhao',$pavilhao);
            $qtdUniaoEstavelH->where('pavilhao',$pavilhao);
            $qtdUniaoEstavelM->where('pavilhao',$pavilhao);
            $qtdSeparadosH->where('pavilhao',$pavilhao);
            $qtdSeparadosM->where('pavilhao',$pavilhao);
            $qtdDivorciadosH->where('pavilhao',$pavilhao);
            $qtdDivorciadosM->where('pavilhao',$pavilhao);
            $qtdViuvosH->where('pavilhao',$pavilhao);
            $qtdViuvosM->where('pavilhao',$pavilhao);
            $qtdEstadoCivilNIH->where('pavilhao',$pavilhao);
            $qtdEstadoCivilNIM->where('pavilhao',$pavilhao);

        }

        if (!is_null($ala)) {

            $qtdSolteirosH->where('ala',$ala);
            $qtdSolteirosM->where('ala',$ala);
            $qtdCasadosH->where('ala',$ala);
            $qtdCasadosM->where('ala',$ala);
            $qtdUniaoEstavelH->where('ala',$ala);
            $qtdUniaoEstavelM->where('ala',$ala);
            $qtdSeparadosH->where('ala',$ala);
            $qtdSeparadosM->where('ala',$ala);
            $qtdDivorciadosH->where('ala',$ala);
            $qtdDivorciadosM->where('ala',$ala);
            $qtdViuvosH->where('ala',$ala);
            $qtdViuvosM->where('ala',$ala);
            $qtdEstadoCivilNIH->where('ala',$ala);
            $qtdEstadoCivilNIM->where('ala',$ala);

        }
        if (!is_null($cela)) {

            $qtdSolteirosH->where('cela',$cela);
            $qtdSolteirosM->where('cela',$cela);
            $qtdCasadosH->where('cela',$cela);
            $qtdCasadosM->where('cela',$cela);
            $qtdUniaoEstavelH->where('cela',$cela);
            $qtdUniaoEstavelM->where('cela',$cela);
            $qtdSeparadosH->where('cela',$cela);
            $qtdSeparadosM->where('cela',$cela);
            $qtdDivorciadosH->where('cela',$cela);
            $qtdDivorciadosM->where('cela',$cela);
            $qtdViuvosH->where('cela',$cela);
            $qtdViuvosM->where('cela',$cela);
            $qtdEstadoCivilNIH->where('cela',$cela);
            $qtdEstadoCivilNIM->where('cela',$cela);

        }

        $qtdSolteirosH=$qtdSolteirosH->count();
        $qtdSolteirosM=$qtdSolteirosM->count();
        $qtdCasadosH=$qtdCasadosH->count();
        $qtdCasadosM=$qtdCasadosM->count();
        $qtdUniaoEstavelH=$qtdUniaoEstavelH->count();
        $qtdUniaoEstavelM=$qtdUniaoEstavelM->count();
        $qtdSeparadosH=$qtdSeparadosH->count();
        $qtdSeparadosM=$qtdSeparadosM->count();
        $qtdDivorciadosH=$qtdDivorciadosH->count();
        $qtdDivorciadosM=$qtdDivorciadosM->count();
        $qtdViuvosH=$qtdViuvosH->count();
        $qtdViuvosM=$qtdViuvosM->count();
        $qtdEstadoCivilNIH=$qtdEstadoCivilNIH->count();
        $qtdEstadoCivilNIM=$qtdEstadoCivilNIM->count();


        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-estado-civil', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
            'qtdInternosLotSelecionada', 'qtdSolteirosH', 'qtdSolteirosM', 'qtdCasadosH', 'qtdCasadosM', 'qtdUniaoEstavelH', 'qtdUniaoEstavelM', 'qtdSeparadosH', 'qtdSeparadosM',
            'qtdDivorciadosH', 'qtdDivorciadosM', 'qtdViuvosH', 'qtdViuvosM', 'qtdEstadoCivilNIH', 'qtdEstadoCivilNIM'));

    }

    public function escolaridade(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if (!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if (!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if (!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if (!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status', '1')->get();

        $qtdInternosLotSelecionada = DB::table('interno')->where('status', 'A');

        $qtdAnalfabetoH = DB::table('interno')->where('escolaridade','Analfabeto')->where('sexo','M')->where('status','A');
        $qtdAnalfabetoM = DB::table('interno')->where('escolaridade','Analfabeto')->where('sexo','F')->where('status','A');
        $qtdAlfabetizadoH = DB::table('interno')->where('escolaridade','Alfabetizado')->where('sexo','M')->where('status','A');
        $qtdAlfabetizadoM = DB::table('interno')->where('escolaridade','Alfabetizado')->where('sexo','F')->where('status','A');
        $qtdFundIncH = DB::table('interno')->where('escolaridade','Ensino Fundamental Incompleto')->where('sexo','M')->where('status','A');
        $qtdFundIncM = DB::table('interno')->where('escolaridade','Ensino Fundamental Incompleto')->where('sexo','F')->where('status','A');
        $qtdFundCompH = DB::table('interno')->where('escolaridade','Ensino Fundamental Completo')->where('sexo','M')->where('status','A');
        $qtdFundCompM = DB::table('interno')->where('escolaridade','Ensino Fundamental Completo')->where('sexo','F')->where('status','A');
        $qtdMedioIncompH = DB::table('interno')->where('escolaridade','Ensino Médio Incompleto')->where('sexo','M')->where('status','A');
        $qtdMedioIncompM = DB::table('interno')->where('escolaridade','Ensino Médio Incompleto')->where('sexo','F')->where('status','A');
        $qtdMedioCompH = DB::table('interno')->where('escolaridade','Ensino Médio Completo')->where('sexo','M')->where('status','A');
        $qtdMedioCompM = DB::table('interno')->where('escolaridade','Ensino Médio Completo')->where('sexo','F')->where('status','A');
        $qtdSuperiorIncompH = DB::table('interno')->where('escolaridade','Ensino Superior Incompleto')->where('sexo','M')->where('status','A');
        $qtdSuperiorIncompM = DB::table('interno')->where('escolaridade','Ensino Superior Incompleto')->where('sexo','F')->where('status','A');
        $qtdSuperiorCompH = DB::table('interno')->where('escolaridade','Ensino Superior Completo')->where('sexo','M')->where('status','A');
        $qtdSuperiorCompM = DB::table('interno')->where('escolaridade','Ensino Superior Completo')->where('sexo','F')->where('status','A');
        $qtdPosLatoSensuH = DB::table('interno')->where('escolaridade','Pós-graduação lato sensu')->where('sexo','M')->where('status','A');
        $qtdPosLatoSensuM = DB::table('interno')->where('escolaridade','Pós-graduação lato sensu')->where('sexo','F')->where('status','A');
        $qtdMestradoH = DB::table('interno')->where('escolaridade','Mestrado')->where('sexo','M')->where('status','A');
        $qtdMestradoM = DB::table('interno')->where('escolaridade','Mestrado')->where('sexo','F')->where('status','A');
        $qtdDoutoradoH = DB::table('interno')->where('escolaridade','Doutorado')->where('sexo','M')->where('status','A');
        $qtdDoutoradoM = DB::table('interno')->where('escolaridade','Doutorado')->where('sexo','F')->where('status','A');
        $qtdPosDoutoradoH = DB::table('interno')->where('escolaridade','Pós-Doutorado')->where('sexo','M')->where('status','A');
        $qtdPosDoutoradoM = DB::table('interno')->where('escolaridade','Pós-Doutorado')->where('sexo','F')->where('status','A');
        $qtdEscolaridadeNIH = DB::table('interno')->where('escolaridade',null)->where('sexo','M')->where('status','A');
        $qtdEscolaridadeNIM = DB::table('interno')->where('escolaridade',null)->where('sexo','F')->where('status','A');


        if (!is_null($unidade)) {

            $qtdAnalfabetoH->where('unidade_prisional',$unidade);
            $qtdAnalfabetoM->where('unidade_prisional',$unidade);
            $qtdAlfabetizadoH->where('unidade_prisional',$unidade);
            $qtdAlfabetizadoM->where('unidade_prisional',$unidade);
            $qtdFundIncH->where('unidade_prisional',$unidade);
            $qtdFundIncM->where('unidade_prisional',$unidade);
            $qtdFundCompH->where('unidade_prisional',$unidade);
            $qtdFundCompM->where('unidade_prisional',$unidade);
            $qtdMedioIncompH->where('unidade_prisional',$unidade);
            $qtdMedioIncompM->where('unidade_prisional',$unidade);
            $qtdMedioCompH->where('unidade_prisional',$unidade);
            $qtdMedioCompM->where('unidade_prisional',$unidade);
            $qtdSuperiorIncompH->where('unidade_prisional',$unidade);
            $qtdSuperiorIncompM->where('unidade_prisional',$unidade);
            $qtdSuperiorCompH->where('unidade_prisional',$unidade);
            $qtdSuperiorCompM->where('unidade_prisional',$unidade);
            $qtdPosLatoSensuH->where('unidade_prisional',$unidade);
            $qtdPosLatoSensuM->where('unidade_prisional',$unidade);
            $qtdMestradoH->where('unidade_prisional',$unidade);
            $qtdMestradoM->where('unidade_prisional',$unidade);
            $qtdDoutoradoH->where('unidade_prisional',$unidade);
            $qtdDoutoradoM->where('unidade_prisional',$unidade);
            $qtdPosDoutoradoH->where('unidade_prisional',$unidade);
            $qtdPosDoutoradoM->where('unidade_prisional',$unidade);
            $qtdEscolaridadeNIH->where('unidade_prisional',$unidade);
            $qtdEscolaridadeNIM->where('unidade_prisional',$unidade);

        }

        if (!is_null($pavilhao)) {

            $qtdAnalfabetoH->where('pavilhao',$pavilhao);
            $qtdAnalfabetoM->where('pavilhao',$pavilhao);
            $qtdAlfabetizadoH->where('pavilhao',$pavilhao);
            $qtdAlfabetizadoM->where('pavilhao',$pavilhao);
            $qtdFundIncH->where('pavilhao',$pavilhao);
            $qtdFundIncM->where('pavilhao',$pavilhao);
            $qtdFundCompH->where('pavilhao',$pavilhao);
            $qtdFundCompM->where('pavilhao',$pavilhao);
            $qtdMedioIncompH->where('pavilhao',$pavilhao);
            $qtdMedioIncompM->where('pavilhao',$pavilhao);
            $qtdMedioCompH->where('pavilhao',$pavilhao);
            $qtdMedioCompM->where('pavilhao',$pavilhao);
            $qtdSuperiorIncompH->where('pavilhao',$pavilhao);
            $qtdSuperiorIncompM->where('pavilhao',$pavilhao);
            $qtdSuperiorCompH->where('pavilhao',$pavilhao);
            $qtdSuperiorCompM->where('pavilhao',$pavilhao);
            $qtdPosLatoSensuH->where('pavilhao',$pavilhao);
            $qtdPosLatoSensuM->where('pavilhao',$pavilhao);
            $qtdMestradoH->where('pavilhao',$pavilhao);
            $qtdMestradoM->where('pavilhao',$pavilhao);
            $qtdDoutoradoH->where('pavilhao',$pavilhao);
            $qtdDoutoradoM->where('pavilhao',$pavilhao);
            $qtdPosDoutoradoH->where('pavilhao',$pavilhao);
            $qtdPosDoutoradoM->where('pavilhao',$pavilhao);
            $qtdEscolaridadeNIH->where('pavilhao',$pavilhao);
            $qtdEscolaridadeNIM->where('pavilhao',$pavilhao);

        }

        if (!is_null($ala)) {

            $qtdAnalfabetoH->where('ala',$ala);
            $qtdAnalfabetoM->where('ala',$ala);
            $qtdAlfabetizadoH->where('ala',$ala);
            $qtdAlfabetizadoM->where('ala',$ala);
            $qtdFundIncH->where('ala',$ala);
            $qtdFundIncM->where('ala',$ala);
            $qtdFundCompH->where('ala',$ala);
            $qtdFundCompM->where('ala',$ala);
            $qtdMedioIncompH->where('ala',$ala);
            $qtdMedioIncompM->where('ala',$ala);
            $qtdMedioCompH->where('ala',$ala);
            $qtdMedioCompM->where('ala',$ala);
            $qtdSuperiorIncompH->where('ala',$ala);
            $qtdSuperiorIncompM->where('ala',$ala);
            $qtdSuperiorCompH->where('ala',$ala);
            $qtdSuperiorCompM->where('ala',$ala);
            $qtdPosLatoSensuH->where('ala',$ala);
            $qtdPosLatoSensuM->where('ala',$ala);
            $qtdMestradoH->where('ala',$ala);
            $qtdMestradoM->where('ala',$ala);
            $qtdDoutoradoH->where('ala',$ala);
            $qtdDoutoradoM->where('ala',$ala);
            $qtdPosDoutoradoH->where('ala',$ala);
            $qtdPosDoutoradoM->where('ala',$ala);
            $qtdEscolaridadeNIH->where('ala',$ala);
            $qtdEscolaridadeNIM->where('ala',$ala);

        }
        if (!is_null($cela)) {

            $qtdAnalfabetoH->where('cela',$cela);
            $qtdAnalfabetoM->where('cela',$cela);
            $qtdAlfabetizadoH->where('cela',$cela);
            $qtdAlfabetizadoM->where('cela',$cela);
            $qtdFundIncH->where('cela',$cela);
            $qtdFundIncM->where('cela',$cela);
            $qtdFundCompH->where('cela',$cela);
            $qtdFundCompM->where('cela',$cela);
            $qtdMedioIncompH->where('cela',$cela);
            $qtdMedioIncompM->where('cela',$cela);
            $qtdMedioCompH->where('cela',$cela);
            $qtdMedioCompM->where('cela',$cela);
            $qtdSuperiorIncompH->where('cela',$cela);
            $qtdSuperiorIncompM->where('cela',$cela);
            $qtdSuperiorCompH->where('cela',$cela);
            $qtdSuperiorCompM->where('cela',$cela);
            $qtdPosLatoSensuH->where('cela',$cela);
            $qtdPosLatoSensuM->where('cela',$cela);
            $qtdMestradoH->where('cela',$cela);
            $qtdMestradoM->where('cela',$cela);
            $qtdDoutoradoH->where('cela',$cela);
            $qtdDoutoradoM->where('cela',$cela);
            $qtdPosDoutoradoH->where('cela',$cela);
            $qtdPosDoutoradoM->where('cela',$cela);
            $qtdEscolaridadeNIH->where('cela',$cela);
            $qtdEscolaridadeNIM->where('cela',$cela);

        }

        $qtdAnalfabetoH=$qtdAnalfabetoH->count();
        $qtdAnalfabetoM=$qtdAnalfabetoM->count();
        $qtdAlfabetizadoH=$qtdAlfabetizadoH->count();
        $qtdAlfabetizadoM=$qtdAlfabetizadoM->count();
        $qtdFundIncH=$qtdFundIncH->count();
        $qtdFundIncM=$qtdFundIncM->count();
        $qtdFundCompH=$qtdFundCompH->count();
        $qtdFundCompM=$qtdFundCompM->count();
        $qtdMedioIncompH=$qtdMedioIncompH->count();
        $qtdMedioIncompM=$qtdMedioIncompM->count();
        $qtdMedioCompH=$qtdMedioCompH->count();
        $qtdMedioCompM=$qtdMedioCompM->count();
        $qtdSuperiorIncompH=$qtdSuperiorIncompH->count();
        $qtdSuperiorIncompM=$qtdSuperiorIncompM->count();
        $qtdSuperiorCompH=$qtdSuperiorCompH->count();
        $qtdSuperiorCompM=$qtdSuperiorCompM->count();
        $qtdPosLatoSensuH=$qtdPosLatoSensuH->count();
        $qtdPosLatoSensuM=$qtdPosLatoSensuM->count();
        $qtdMestradoH=$qtdMestradoH->count();
        $qtdMestradoM=$qtdMestradoM->count();
        $qtdDoutoradoH=$qtdDoutoradoH->count();
        $qtdDoutoradoM=$qtdDoutoradoM->count();
        $qtdPosDoutoradoH=$qtdPosDoutoradoH->count();
        $qtdPosDoutoradoM=$qtdPosDoutoradoM->count();
        $qtdEscolaridadeNIH=$qtdEscolaridadeNIH->count();
        $qtdEscolaridadeNIM=$qtdEscolaridadeNIM->count();


        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-escolaridade', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
            'qtdInternosLotSelecionada', 'qtdAnalfabetoH', 'qtdAnalfabetoM', 'qtdAlfabetizadoH', 'qtdAlfabetizadoM', 'qtdFundIncH', 'qtdFundIncM', 'qtdFundCompH', 'qtdEscolaridadeNIM',
            'qtdFundCompM', 'qtdMedioIncompH', 'qtdMedioIncompM', 'qtdMedioCompH', 'qtdMedioCompM', 'qtdSuperiorIncompH', 'qtdSuperiorIncompM', 'qtdSuperiorCompH', 'qtdSuperiorCompM',
            'qtdPosLatoSensuH', 'qtdPosLatoSensuM', 'qtdMestradoH', 'qtdMestradoM', 'qtdDoutoradoH', 'qtdDoutoradoM', 'qtdPosDoutoradoH', 'qtdPosDoutoradoM', 'qtdEscolaridadeNIH' ));

    }

    public function corPele(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if (!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if (!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if (!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if (!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status', '1')->get();

        $qtdInternosLotSelecionada = DB::table('interno')->where('status', 'A');

        $qtdEtniaBrancaH = DB::table('interno')->where('etnia','Branca')->where('sexo','M')->where('status','A');
        $qtdEtniaBrancaM = DB::table('interno')->where('etnia','Branca')->where('sexo','F')->where('status','A');
        $qtdEtniaIndígenaH = DB::table('interno')->where('etnia','Indígena')->where('sexo','M')->where('status','A');
        $qtdEtniaIndígenaM = DB::table('interno')->where('etnia','Indígena')->where('sexo','F')->where('status','A');
        $qtdEtniaPretaH = DB::table('interno')->where('etnia','Preta')->where('sexo','M')->where('status','A');
        $qtdEtniaPretaM = DB::table('interno')->where('etnia','Preta')->where('sexo','F')->where('status','A');
        $qtdEtniaPardaH = DB::table('interno')->where('etnia','Parda')->where('sexo','M')->where('status','A');
        $qtdEtniaPardaM = DB::table('interno')->where('etnia','Parda')->where('sexo','F')->where('status','A');
        $qtdEtniaAmarelaH = DB::table('interno')->where('etnia','Amarela')->where('sexo','M')->where('status','A');
        $qtdEtniaAmarelaM = DB::table('interno')->where('etnia','Amarela')->where('sexo','F')->where('status','A');
        $qtdEtniaNIH = DB::table('interno')->where('etnia',null)->where('sexo','M')->where('status','A');
        $qtdEtniaNIM = DB::table('interno')->where('etnia',null)->where('sexo','F')->where('status','A');


        if (!is_null($unidade)) {

            $qtdEtniaBrancaH->where('unidade_prisional',$unidade);
            $qtdEtniaBrancaM->where('unidade_prisional',$unidade);
            $qtdEtniaIndígenaH->where('unidade_prisional',$unidade);
            $qtdEtniaIndígenaM->where('unidade_prisional',$unidade);
            $qtdEtniaPretaH->where('unidade_prisional',$unidade);
            $qtdEtniaPretaM->where('unidade_prisional',$unidade);
            $qtdEtniaPardaH->where('unidade_prisional',$unidade);
            $qtdEtniaPardaM->where('unidade_prisional',$unidade);
            $qtdEtniaAmarelaH->where('unidade_prisional',$unidade);
            $qtdEtniaAmarelaM->where('unidade_prisional',$unidade);
            $qtdEtniaNIH->where('unidade_prisional',$unidade);
            $qtdEtniaNIM->where('unidade_prisional',$unidade);

        }

        if (!is_null($pavilhao)) {

            $qtdEtniaBrancaH->where('pavilhao',$pavilhao);
            $qtdEtniaBrancaM->where('pavilhao',$pavilhao);
            $qtdEtniaIndígenaH->where('pavilhao',$pavilhao);
            $qtdEtniaIndígenaM->where('pavilhao',$pavilhao);
            $qtdEtniaPretaH->where('pavilhao',$pavilhao);
            $qtdEtniaPretaM->where('pavilhao',$pavilhao);
            $qtdEtniaPardaH->where('pavilhao',$pavilhao);
            $qtdEtniaPardaM->where('pavilhao',$pavilhao);
            $qtdEtniaAmarelaH->where('pavilhao',$pavilhao);
            $qtdEtniaAmarelaM->where('pavilhao',$pavilhao);
            $qtdEtniaNIH->where('pavilhao',$pavilhao);
            $qtdEtniaNIM->where('pavilhao',$pavilhao);

        }

        if (!is_null($ala)) {

            $qtdEtniaBrancaH->where('ala',$ala);
            $qtdEtniaBrancaM->where('ala',$ala);
            $qtdEtniaIndígenaH->where('ala',$ala);
            $qtdEtniaIndígenaM->where('ala',$ala);
            $qtdEtniaPretaH->where('ala',$ala);
            $qtdEtniaPretaM->where('ala',$ala);
            $qtdEtniaPardaH->where('ala',$ala);
            $qtdEtniaPardaM->where('ala',$ala);
            $qtdEtniaAmarelaH->where('ala',$ala);
            $qtdEtniaAmarelaM->where('ala',$ala);
            $qtdEtniaNIH->where('ala',$ala);
            $qtdEtniaNIM->where('ala',$ala);

        }
        if (!is_null($cela)) {

            $qtdEtniaBrancaH->where('cela',$cela);
            $qtdEtniaBrancaM->where('cela',$cela);
            $qtdEtniaIndígenaH->where('cela',$cela);
            $qtdEtniaIndígenaM->where('cela',$cela);
            $qtdEtniaPretaH->where('cela',$cela);
            $qtdEtniaPretaM->where('cela',$cela);
            $qtdEtniaPardaH->where('cela',$cela);
            $qtdEtniaPardaM->where('cela',$cela);
            $qtdEtniaAmarelaH->where('cela',$cela);
            $qtdEtniaAmarelaM->where('cela',$cela);
            $qtdEtniaNIH->where('cela',$cela);
            $qtdEtniaNIM->where('cela',$cela);

        }

        $qtdEtniaBrancaH=$qtdEtniaBrancaH->count();
        $qtdEtniaBrancaM=$qtdEtniaBrancaM->count();
        $qtdEtniaIndígenaH=$qtdEtniaIndígenaH->count();
        $qtdEtniaIndígenaM=$qtdEtniaIndígenaM->count();
        $qtdEtniaPretaH=$qtdEtniaPretaH->count();
        $qtdEtniaPretaM=$qtdEtniaPretaM->count();
        $qtdEtniaPardaH=$qtdEtniaPardaH->count();
        $qtdEtniaPardaM=$qtdEtniaPardaM->count();
        $qtdEtniaAmarelaH=$qtdEtniaAmarelaH->count();
        $qtdEtniaAmarelaM=$qtdEtniaAmarelaM->count();
        $qtdEtniaNIH=$qtdEtniaNIH->count();
        $qtdEtniaNIM=$qtdEtniaNIM->count();


        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-cor-pele', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
            'qtdInternosLotSelecionada', 'qtdEtniaBrancaH', 'qtdEtniaBrancaM', 'qtdEtniaIndígenaH', 'qtdEtniaIndígenaM', 'qtdEtniaPretaH', 'qtdEtniaPretaM', 'qtdEtniaPardaH',
            'qtdEtniaPardaM', 'qtdEtniaPardaM', 'qtdEtniaAmarelaH', 'qtdEtniaAmarelaM', 'qtdEtniaNIH', 'qtdEtniaNIM' ));

    }

    public function orientacaoSexual(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if (!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if (!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if (!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if (!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status', '1')->get();

        $qtdInternosLotSelecionada = DB::table('interno')->where('status', 'A');

        $qtdHeteroH = DB::table('interno')->where('opcao_sexual','HETEROSSEXUAL')->where('sexo','M')->where('status','A');
        $qtdHeteroM = DB::table('interno')->where('opcao_sexual','HETEROSSEXUAL')->where('sexo','F')->where('status','A');
        $qtdHomoH = DB::table('interno')->where('opcao_sexual','HOMOSSEXUAL')->where('sexo','M')->where('status','A');
        $qtdHomoM = DB::table('interno')->where('opcao_sexual','HOMOSSEXUAL')->where('sexo','F')->where('status','A');
        $qtdBiH = DB::table('interno')->where('opcao_sexual','BISSEXUAL')->where('sexo','M')->where('status','A');
        $qtdBiM = DB::table('interno')->where('opcao_sexual','BISSEXUAL')->where('sexo','F')->where('status','A');
        $qtdSexualidadeNaoDeclH = DB::table('interno')->where('opcao_sexual','nao declarado')->where('sexo','M')->where('status','A');
        $qtdSexualidadeNaoDeclM = DB::table('interno')->where('opcao_sexual','nao declarado')->where('sexo','F')->where('status','A');
        $qtdSexualidadeNIH = DB::table('interno')->where('opcao_sexual',null)->where('sexo','M')->where('status','A');
        $qtdSexualidadeNIM = DB::table('interno')->where('opcao_sexual',null)->where('sexo','F')->where('status','A');


        if (!is_null($unidade)) {

            $qtdHeteroH->where('unidade_prisional',$unidade);
            $qtdHeteroM->where('unidade_prisional',$unidade);
            $qtdHomoH->where('unidade_prisional',$unidade);
            $qtdHomoM->where('unidade_prisional',$unidade);
            $qtdBiH->where('unidade_prisional',$unidade);
            $qtdBiM->where('unidade_prisional',$unidade);
            $qtdSexualidadeNaoDeclH->where('unidade_prisional',$unidade);
            $qtdSexualidadeNaoDeclM->where('unidade_prisional',$unidade);
            $qtdSexualidadeNIH->where('unidade_prisional',$unidade);
            $qtdSexualidadeNIM->where('unidade_prisional',$unidade);

        }

        if (!is_null($pavilhao)) {

            $qtdHeteroH->where('pavilhao',$pavilhao);
            $qtdHeteroM->where('pavilhao',$pavilhao);
            $qtdHomoH->where('pavilhao',$pavilhao);
            $qtdHomoM->where('pavilhao',$pavilhao);
            $qtdBiH->where('pavilhao',$pavilhao);
            $qtdBiM->where('pavilhao',$pavilhao);
            $qtdSexualidadeNaoDeclH->where('pavilhao',$pavilhao);
            $qtdSexualidadeNaoDeclM->where('pavilhao',$pavilhao);
            $qtdSexualidadeNIH->where('pavilhao',$pavilhao);
            $qtdSexualidadeNIM->where('pavilhao',$pavilhao);

        }

        if (!is_null($ala)) {

            $qtdHeteroH->where('ala',$ala);
            $qtdHeteroM->where('ala',$ala);
            $qtdHomoH->where('ala',$ala);
            $qtdHomoM->where('ala',$ala);
            $qtdBiH->where('ala',$ala);
            $qtdBiM->where('ala',$ala);
            $qtdSexualidadeNaoDeclH->where('ala',$ala);
            $qtdSexualidadeNaoDeclM->where('ala',$ala);
            $qtdSexualidadeNIH->where('ala',$ala);
            $qtdSexualidadeNIM->where('ala',$ala);

        }
        if (!is_null($cela)) {

            $qtdHeteroH->where('cela',$cela);
            $qtdHeteroM->where('cela',$cela);
            $qtdHomoH->where('cela',$cela);
            $qtdHomoM->where('cela',$cela);
            $qtdBiH->where('cela',$cela);
            $qtdBiM->where('cela',$cela);
            $qtdSexualidadeNaoDeclH->where('cela',$cela);
            $qtdSexualidadeNaoDeclM->where('cela',$cela);
            $qtdSexualidadeNIH->where('cela',$cela);
            $qtdSexualidadeNIM->where('cela',$cela);

        }

        $qtdHeteroH = $qtdHeteroH->count();
        $qtdHeteroM = $qtdHeteroM->count();
        $qtdHomoH = $qtdHomoH->count();
        $qtdHomoM = $qtdHomoM->count();
        $qtdBiH = $qtdBiH->count();
        $qtdBiM = $qtdBiM->count();
        $qtdSexualidadeNaoDeclH = $qtdSexualidadeNaoDeclH->count();
        $qtdSexualidadeNaoDeclM = $qtdSexualidadeNaoDeclM->count();
        $qtdSexualidadeNIH = $qtdSexualidadeNIH->count();
        $qtdSexualidadeNIM = $qtdSexualidadeNIM->count();


        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-orientacao-sexual', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
            'qtdInternosLotSelecionada', 'qtdHeteroH', 'qtdHeteroM', 'qtdHomoH', 'qtdHomoM', 'qtdBiH', 'qtdBiM', 'qtdSexualidadeNaoDeclH', 'qtdSexualidadeNaoDeclM',
            'qtdSexualidadeNIH', 'qtdSexualidadeNIM'));

    }

    public function saidaUnidadePrisional(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if (!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if (!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if (!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if (!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status', '1')->get();

        $qtdInternosLotSelecionada = DB::table('interno')->where('status', 'A');

        $qtdAlvara = DB::table('movimentacao')->where('tipo','alvara');
        $qtdFuga = DB::table('movimentacao')->where('tipo','fuga');
        $qtdRecambiamento = DB::table('movimentacao')->where('tipo','recambiamento');
        $qtdSaidaJudicial = DB::table('movimentacao')->where('tipo','saidajudicial');
        $qtdExtradicao = DB::table('movimentacao')->where('tipo','extradicao');
        $qtdLivramento = DB::table('movimentacao')->where('tipo','livramentocondicional');
        $qtdObito = DB::table('movimentacao')->where('tipo','obito');
        $qtdExtincaopunibilidade = DB::table('movimentacao')->where('tipo','extincaopunibilidade');

        $qtdAlvara->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdFuga->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdRecambiamento->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdSaidaJudicial->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdExtradicao->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdLivramento->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdObito->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdExtincaopunibilidade->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');


        if (!is_null($unidade)) {

            $qtdAlvara->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdAlvara->where('movimentacao_interno.status','Finalizada');
            $qtdAlvara->where('interno.unidade_prisional', $unidade);
            $qtdFuga->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdFuga->where('movimentacao_interno.status','Finalizada');
            $qtdFuga->where('interno.unidade_prisional', $unidade);
            $qtdRecambiamento->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdRecambiamento->where('movimentacao_interno.status','Finalizada');
            $qtdRecambiamento->where('interno.unidade_prisional', $unidade);
            $qtdSaidaJudicial->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdSaidaJudicial->where('movimentacao_interno.status','Finalizada');
            $qtdSaidaJudicial->where('interno.unidade_prisional', $unidade);
            $qtdExtradicao->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdExtradicao->where('movimentacao_interno.status','Finalizada');
            $qtdExtradicao->where('interno.unidade_prisional', $unidade);
            $qtdLivramento->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdLivramento->where('movimentacao_interno.status','Finalizada');
            $qtdLivramento->where('interno.unidade_prisional', $unidade);
            $qtdObito->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdObito->where('movimentacao_interno.status','Finalizada');
            $qtdObito->where('interno.unidade_prisional', $unidade);
            $qtdExtincaopunibilidade->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdExtincaopunibilidade->where('movimentacao_interno.status','Finalizada');
            $qtdExtincaopunibilidade->where('interno.unidade_prisional', $unidade);

        }

        if (!is_null($pavilhao)) {

            $qtdAlvara->where('interno.pavilhao',$pavilhao);
            $qtdFuga->where('interno.pavilhao',$pavilhao);
            $qtdRecambiamento->where('interno.pavilhao',$pavilhao);
            $qtdSaidaJudicial->where('interno.pavilhao',$pavilhao);
            $qtdExtradicao->where('interno.pavilhao',$pavilhao);
            $qtdLivramento->where('interno.pavilhao',$pavilhao);
            $qtdObito->where('interno.pavilhao',$pavilhao);
            $qtdExtincaopunibilidade->where('interno.pavilhao',$pavilhao);

        }

        if (!is_null($ala)) {

            $qtdAlvara->where('interno.ala',$ala);
            $qtdFuga->where('interno.ala',$ala);
            $qtdRecambiamento->where('interno.ala',$ala);
            $qtdSaidaJudicial->where('interno.ala',$ala);
            $qtdExtradicao->where('interno.ala',$ala);
            $qtdLivramento->where('interno.ala',$ala);
            $qtdObito->where('interno.ala',$ala);
            $qtdExtincaopunibilidade->where('interno.ala',$ala);

        }
        if (!is_null($cela)) {

            $qtdAlvara->where('interno.cela',$cela);
            $qtdFuga->where('interno.cela',$cela);
            $qtdRecambiamento->where('interno.cela',$cela);
            $qtdSaidaJudicial->where('interno.cela',$cela);
            $qtdExtradicao->where('interno.cela',$cela);
            $qtdLivramento->where('interno.cela',$cela);
            $qtdObito->where('interno.cela',$cela);
            $qtdExtincaopunibilidade->where('interno.cela',$cela);

        }

        $qtdAlvara = $qtdAlvara->count();
        $qtdFuga = $qtdFuga->count();
        $qtdRecambiamento = $qtdRecambiamento->count();
        $qtdSaidaJudicial = $qtdSaidaJudicial->count();
        $qtdExtradicao = $qtdExtradicao->count();
        $qtdLivramento = $qtdLivramento->count();
        $qtdObito = $qtdObito->count();
        $qtdExtincaopunibilidade = $qtdExtincaopunibilidade->count();


        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-saida-unidade-prisional', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
            'qtdInternosLotSelecionada', 'qtdAlvara', 'qtdFuga', 'qtdRecambiamento', 'qtdSaidaJudicial', 'qtdExtradicao', 'qtdLivramento', 'qtdObito', 'qtdExtincaopunibilidade'));

    }

    public function reingressoSistemaPrisional(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if (!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if (!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if (!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if (!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;
        if (!is_null($request->get('data'))) $data = TData::TdataMysql($request->get('data'));
        else $data = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status', '1')->get();

        $qtdInternosLotSelecionada = DB::table('interno')->where('status', 'A');

        $datai = mktime(00,00,00,02,19,2019);

        if($data != null){


            if (preg_match('/^[0-9]{4}[-][0-9]{1,2}[-][0-9]{1,2}$/', $data)) {

                if ($data < date("Y-m-d", $datai)) {


                    //return redirect()->back()->withInput()->with('alert-danger', 'Não foi possível atender a solicitação. Digite uma data a partir de 19/02/2019.');
                    $msg = redirect()->back()->withInput()->with('alert-danger', 'Não foi possível atender a solicitação. Digite uma data a partir de 19/02/2019.');
                    $dataHora = Tdata::TtimeStampHojeBrTxt();
                    $qntReigressoH = "-";
                    $qntReigressoM = "-";
                    $soma = "-";
                    return view('estatisticas.show.estatisticas-reingresso-sistema-prisional', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
                        'qtdInternosLotSelecionada', 'qntReigressoH', 'qntReigressoM', 'data', 'msg', 'soma'));

                }else {

                        $qntReigressoH = DB::table('log')
                            ->leftJoin('interno', 'log.fk_id', 'interno.id')
                            ->where('log.texto', 'reativação de interno')
                            ->where('log.created_at', '>=', $data)
                            ->where('interno.status','A')
                            ->where('interno.sexo', 'M');

                        $qntReigressoM = DB::table('log')
                            ->leftJoin('interno', 'log.fk_id', 'interno.id')
                            ->where('log.texto', 'reativação de interno')
                            ->where('log.created_at', '>=', $data)
                            ->where('interno.status','A')
                            ->where('interno.sexo', 'F');

                        if(!is_null($unidade)){

                            $qntReigressoH->where('log.unidade', $unidade);

                            $qntReigressoM->where('log.unidade', $unidade);
                        }

                        if(!is_null($pavilhao)){

                        $qntReigressoH->where('interno.pavilhao', $pavilhao);

                        $qntReigressoM->where('interno.pavilhao', $pavilhao);
                        }

                        if(!is_null($ala)){

                        $qntReigressoH->where('interno.ala', $ala);

                        $qntReigressoM->where('interno.ala', $ala);
                        }

                        if(!is_null($cela)){

                        $qntReigressoH->where('interno.cela', $cela);

                        $qntReigressoM->where('interno.cela', $cela);
                        }

                    $qntReigressoH = $qntReigressoH->count();
                    $qntReigressoM = $qntReigressoM->count();
                    $soma = $qntReigressoH + $qntReigressoM;
                }
            }else {

                //return redirect()->back()->withInput()->with('alert-danger', 'Data inválida. Digite uma data válida. Ex.: 00/00/0000');
                $msg = redirect()->back()->withInput()->with('alert-danger', 'Data inválida. Digite uma data válida. Ex.: 00/00/0000');
                $dataHora = Tdata::TtimeStampHojeBrTxt();
                $qntReigressoH = "-";
                $qntReigressoM = "-";
                $soma = "-";
                return view('estatisticas.show.estatisticas-reingresso-sistema-prisional', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
                    'qtdInternosLotSelecionada', 'qntReigressoH', 'qntReigressoM', 'data', 'msg', 'soma'));

            }
        }else {


            $qntReigressoH = "-";
            $qntReigressoM = "-";
            $soma = "-";


        }


        $dataHora = Tdata::TtimeStampHojeBrTxt();



        return view('estatisticas.show.estatisticas-reingresso-sistema-prisional', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
            'qtdInternosLotSelecionada', 'qntReigressoH', 'qntReigressoM', 'data', 'soma'));

    }

    public function religiao(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if (!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if (!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if (!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if (!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status', '1')->get();


        $qtdAdventistaH = DB::table('interno')->where('religiao','Adventista')->where('sexo','M')->where('status','A');
        $qtdAdventistaM = DB::table('interno')->where('religiao','Adventista')->where('sexo','F')->where('status','A');
        $qtdBudistaH = DB::table('interno')->where('religiao','Budista')->where('sexo','M')->where('status','A');
        $qtdBudistaM = DB::table('interno')->where('religiao','Budista')->where('sexo','F')->where('status','A');
        $qtdCatolicaH = DB::table('interno')->where('religiao','Católica')->where('sexo','M')->where('status','A');
        $qtdCatolicaM = DB::table('interno')->where('religiao','Católica')->where('sexo','F')->where('status','A');
        $qtdEspiritaH = DB::table('interno')->where('religiao','Espírita')->where('sexo','M')->where('status','A');
        $qtdEspiritaM = DB::table('interno')->where('religiao','Espírita')->where('sexo','F')->where('status','A');
        $qtdEvangelicaH = DB::table('interno')->where('religiao','Evangélica')->where('sexo','M')->where('status','A');
        $qtdEvangelicaM = DB::table('interno')->where('religiao','Evangélica')->where('sexo','F')->where('status','A');
        $qtdJudeuH = DB::table('interno')->where('religiao','Judeu')->where('sexo','M')->where('status','A');
        $qtdJudeuM = DB::table('interno')->where('religiao','Judeu')->where('sexo','F')->where('status','A');
        $qtdMormonH = DB::table('interno')->where('religiao','Mórmon')->where('sexo','M')->where('status','A');
        $qtdMormonM = DB::table('interno')->where('religiao','Mórmon')->where('sexo','F')->where('status','A');
        $qtdMulcumanaH = DB::table('interno')->where('religiao','Muçulmana')->where('sexo','M')->where('status','A');
        $qtdMulcumanaM = DB::table('interno')->where('religiao','Muçulmana')->where('sexo','F')->where('status','A');
        $qtdProtestanteH = DB::table('interno')->where('religiao','Protestante')->where('sexo','M')->where('status','A');
        $qtdProtestanteM = DB::table('interno')->where('religiao','Protestante')->where('sexo','F')->where('status','A');
        $qtdAgnosticoH = DB::table('interno')->where('religiao','Agnóstico')->where('sexo','M')->where('status','A');
        $qtdAgnosticoM = DB::table('interno')->where('religiao','Agnóstico')->where('sexo','F')->where('status','A');
        $qtdAteuH = DB::table('interno')->where('religiao','Ateu')->where('sexo','M')->where('status','A');
        $qtdAteuM = DB::table('interno')->where('religiao','Ateu')->where('sexo','F')->where('status','A');
        $qtdSemReligiaoH = DB::table('interno')->where('religiao','Sem Religião')->where('sexo','M')->where('status','A');
        $qtdSemReligiaoM = DB::table('interno')->where('religiao','Sem Religião')->where('sexo','F')->where('status','A');
        $qtdOutrasH = DB::table('interno')->where('religiao','Outras')->where('sexo','M')->where('status','A');
        $qtdOutrasM = DB::table('interno')->where('religiao','Outras')->where('sexo','F')->where('status','A');
        $qtdReligiaoNIH = DB::table('interno')->where('religiao',null)->where('sexo','M')->where('status','A');
        $qtdReligiaoNIM = DB::table('interno')->where('religiao',null)->where('sexo','F')->where('status','A');
        $qtdReligiaoMAfricanaM = DB::table('interno')->where('religiao','Matriz Africana')->where('sexo','M')->where('status','A');
        $qtdReligiaoMAfricanaF = DB::table('interno')->where('religiao','Matriz Africana')->where('sexo','F')->where('status','A');


        if (!is_null($unidade)) {

            $qtdAdventistaH->where('unidade_prisional',$unidade);
            $qtdAdventistaM->where('unidade_prisional',$unidade);
            $qtdBudistaH->where('unidade_prisional',$unidade);
            $qtdBudistaM->where('unidade_prisional',$unidade);
            $qtdCatolicaH->where('unidade_prisional',$unidade);
            $qtdCatolicaM->where('unidade_prisional',$unidade);
            $qtdEspiritaH->where('unidade_prisional',$unidade);
            $qtdEspiritaM->where('unidade_prisional',$unidade);
            $qtdEvangelicaH->where('unidade_prisional',$unidade);
            $qtdEvangelicaM->where('unidade_prisional',$unidade);
            $qtdJudeuH->where('unidade_prisional',$unidade);
            $qtdJudeuM->where('unidade_prisional',$unidade);
            $qtdMormonH->where('unidade_prisional',$unidade);
            $qtdMormonM->where('unidade_prisional',$unidade);
            $qtdMulcumanaH->where('unidade_prisional',$unidade);
            $qtdMulcumanaM->where('unidade_prisional',$unidade);
            $qtdProtestanteH->where('unidade_prisional',$unidade);
            $qtdProtestanteM->where('unidade_prisional',$unidade);
            $qtdAgnosticoH->where('unidade_prisional',$unidade);
            $qtdAgnosticoM->where('unidade_prisional',$unidade);
            $qtdAteuH->where('unidade_prisional',$unidade);
            $qtdAteuM->where('unidade_prisional',$unidade);
            $qtdSemReligiaoH->where('unidade_prisional',$unidade);
            $qtdSemReligiaoM->where('unidade_prisional',$unidade);
            $qtdOutrasH->where('unidade_prisional',$unidade);
            $qtdOutrasM->where('unidade_prisional',$unidade);
            $qtdReligiaoNIH->where('unidade_prisional',$unidade);
            $qtdReligiaoNIM->where('unidade_prisional',$unidade);
            $qtdReligiaoMAfricanaM->where('unidade_prisional',$unidade);
            $qtdReligiaoMAfricanaF->where('unidade_prisional',$unidade);

        }

        if (!is_null($pavilhao)) {

            $qtdAdventistaH->where('pavilhao',$pavilhao);
            $qtdAdventistaM->where('pavilhao',$pavilhao);
            $qtdBudistaH->where('pavilhao',$pavilhao);
            $qtdBudistaM->where('pavilhao',$pavilhao);
            $qtdCatolicaH->where('pavilhao',$pavilhao);
            $qtdCatolicaM->where('pavilhao',$pavilhao);
            $qtdEspiritaH->where('pavilhao',$pavilhao);
            $qtdEspiritaM->where('pavilhao',$pavilhao);
            $qtdEvangelicaH->where('pavilhao',$pavilhao);
            $qtdEvangelicaM->where('pavilhao',$pavilhao);
            $qtdJudeuH->where('pavilhao',$pavilhao);
            $qtdJudeuM->where('pavilhao',$pavilhao);
            $qtdMormonH->where('pavilhao',$pavilhao);
            $qtdMormonM->where('pavilhao',$pavilhao);
            $qtdMulcumanaH->where('pavilhao',$pavilhao);
            $qtdMulcumanaM->where('pavilhao',$pavilhao);
            $qtdProtestanteH->where('pavilhao',$pavilhao);
            $qtdProtestanteM->where('pavilhao',$pavilhao);
            $qtdAgnosticoH->where('pavilhao',$pavilhao);
            $qtdAgnosticoM->where('pavilhao',$pavilhao);
            $qtdAteuH->where('pavilhao',$pavilhao);
            $qtdAteuM->where('pavilhao',$pavilhao);
            $qtdSemReligiaoH->where('pavilhao',$pavilhao);
            $qtdSemReligiaoM->where('pavilhao',$pavilhao);
            $qtdOutrasH->where('pavilhao',$pavilhao);
            $qtdOutrasM->where('pavilhao',$pavilhao);
            $qtdReligiaoNIH->where('pavilhao',$pavilhao);
            $qtdReligiaoNIM->where('pavilhao',$pavilhao);
            $qtdReligiaoMAfricanaM->where('pavilhao',$pavilhao);
            $qtdReligiaoMAfricanaF->where('pavilhao',$pavilhao);

        }

        if (!is_null($ala)) {

            $qtdAdventistaH->where('ala',$ala);
            $qtdAdventistaM->where('ala',$ala);
            $qtdBudistaH->where('ala',$ala);
            $qtdBudistaM->where('ala',$ala);
            $qtdCatolicaH->where('ala',$ala);
            $qtdCatolicaM->where('ala',$ala);
            $qtdEspiritaH->where('ala',$ala);
            $qtdEspiritaM->where('ala',$ala);
            $qtdEvangelicaH->where('ala',$ala);
            $qtdEvangelicaM->where('ala',$ala);
            $qtdJudeuH->where('ala',$ala);
            $qtdJudeuM->where('ala',$ala);
            $qtdMormonH->where('ala',$ala);
            $qtdMormonM->where('ala',$ala);
            $qtdMulcumanaH->where('ala',$ala);
            $qtdMulcumanaM->where('ala',$ala);
            $qtdProtestanteH->where('ala',$ala);
            $qtdProtestanteM->where('ala',$ala);
            $qtdAgnosticoH->where('ala',$ala);
            $qtdAgnosticoM->where('ala',$ala);
            $qtdAteuH->where('ala',$ala);
            $qtdAteuM->where('ala',$ala);
            $qtdSemReligiaoH->where('ala',$ala);
            $qtdSemReligiaoM->where('ala',$ala);
            $qtdOutrasH->where('ala',$ala);
            $qtdOutrasM->where('ala',$ala);
            $qtdReligiaoNIH->where('ala',$ala);
            $qtdReligiaoNIM->where('ala',$ala);
            $qtdReligiaoMAfricanaM->where('ala',$ala);
            $qtdReligiaoMAfricanaF->where('ala',$ala);

        }
        if (!is_null($cela)) {

            $qtdAdventistaH->where('cela',$cela);
            $qtdAdventistaM->where('cela',$cela);
            $qtdBudistaH->where('cela',$cela);
            $qtdBudistaM->where('cela',$cela);
            $qtdCatolicaH->where('cela',$cela);
            $qtdCatolicaM->where('cela',$cela);
            $qtdEspiritaH->where('cela',$cela);
            $qtdEspiritaM->where('cela',$cela);
            $qtdEvangelicaH->where('cela',$cela);
            $qtdEvangelicaM->where('cela',$cela);
            $qtdJudeuH->where('cela',$cela);
            $qtdJudeuM->where('cela',$cela);
            $qtdMormonH->where('cela',$cela);
            $qtdMormonM->where('cela',$cela);
            $qtdMulcumanaH->where('cela',$cela);
            $qtdMulcumanaM->where('cela',$cela);
            $qtdProtestanteH->where('cela',$cela);
            $qtdProtestanteM->where('cela',$cela);
            $qtdAgnosticoH->where('cela',$cela);
            $qtdAgnosticoM->where('cela',$cela);
            $qtdAteuH->where('cela',$cela);
            $qtdAteuM->where('cela',$cela);
            $qtdSemReligiaoH->where('cela',$cela);
            $qtdSemReligiaoM->where('cela',$cela);
            $qtdOutrasH->where('cela',$cela);
            $qtdOutrasM->where('cela',$cela);
            $qtdReligiaoNIH->where('cela',$cela);
            $qtdReligiaoNIM->where('cela',$cela);
            $qtdReligiaoMAfricanaM->where('cela',$cela);
            $qtdReligiaoMAfricanaF->where('cela',$cela);

        }

        $qtdAdventistaH=$qtdAdventistaH->count();
        $qtdAdventistaM=$qtdAdventistaM->count();
        $qtdBudistaH=$qtdBudistaH->count();
        $qtdBudistaM=$qtdBudistaM->count();
        $qtdCatolicaH=$qtdCatolicaH->count();
        $qtdCatolicaM=$qtdCatolicaM->count();
        $qtdEspiritaH=$qtdEspiritaH->count();
        $qtdEspiritaM=$qtdEspiritaM->count();
        $qtdEvangelicaH=$qtdEvangelicaH->count();
        $qtdEvangelicaM=$qtdEvangelicaM->count();
        $qtdJudeuH=$qtdJudeuH->count();
        $qtdJudeuM=$qtdJudeuM->count();
        $qtdMormonH=$qtdMormonH->count();
        $qtdMormonM=$qtdMormonM->count();
        $qtdMulcumanaH=$qtdMulcumanaH->count();
        $qtdMulcumanaM=$qtdMulcumanaM->count();
        $qtdProtestanteH=$qtdProtestanteH->count();
        $qtdProtestanteM=$qtdProtestanteM->count();
        $qtdAgnosticoH=$qtdAgnosticoH->count();
        $qtdAgnosticoM=$qtdAgnosticoM->count();
        $qtdAteuH=$qtdAteuH->count();
        $qtdAteuM=$qtdAteuM->count();
        $qtdSemReligiaoH=$qtdSemReligiaoH->count();
        $qtdSemReligiaoM=$qtdSemReligiaoM->count();
        $qtdOutrasH=$qtdOutrasH->count();
        $qtdOutrasM=$qtdOutrasM->count();
        $qtdReligiaoNIH=$qtdReligiaoNIH->count();
        $qtdReligiaoNIM=$qtdReligiaoNIM->count();
        $qtdReligiaoMAfricanaM=$qtdReligiaoMAfricanaM->count();
        $qtdReligiaoMAfricanaF=$qtdReligiaoMAfricanaF->count();


        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-religiao', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
            'qtdAdventistaH', 'qtdAdventistaM', 'qtdBudistaH', 'qtdBudistaM', 'qtdCatolicaH', 'qtdCatolicaM', 'qtdEspiritaH', 'qtdEspiritaM', 'qtdEvangelicaH', 'qtdEvangelicaM',
            'qtdJudeuH', 'qtdJudeuM', 'qtdMormonH', 'qtdMormonM', 'qtdMulcumanaH', 'qtdMulcumanaM', 'qtdProtestanteH', 'qtdProtestanteM', 'qtdAgnosticoH', 'qtdAgnosticoM',
            'qtdAteuH', 'qtdAteuM', 'qtdSemReligiaoH', 'qtdSemReligiaoM', 'qtdOutrasH', 'qtdOutrasM', 'qtdReligiaoNIH', 'qtdReligiaoNIM','qtdReligiaoMAfricanaM','qtdReligiaoMAfricanaF'));

    }

    public function faccionadosNaofaccionados(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if (!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if (!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if (!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if (!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status', '1')->get();


        $qtdHA = DB::table('interno')->where('sexo', 'M')->where('status','A');
        $qtdMA = DB::table('interno')->where('sexo', 'F')->where('status','A');
        $qtdFaccaoNIH = DB::table('interno')->where('status','A')->where('sexo','M')->where('faccao', null);
        $qtdFaccaoNIM = DB::table('interno')->where('status','A')->where('sexo','F')->where('faccao', null);


        if (!is_null($unidade)) {

            $qtdHA->where('unidade_prisional',$unidade);
            $qtdMA->where('unidade_prisional',$unidade);
            $qtdFaccaoNIH->where('unidade_prisional',$unidade);
            $qtdFaccaoNIM->where('unidade_prisional',$unidade);

        }

        if (!is_null($pavilhao)) {

            $qtdHA->where('pavilhao',$pavilhao);
            $qtdMA->where('pavilhao',$pavilhao);
            $qtdFaccaoNIH->where('pavilhao',$pavilhao);
            $qtdFaccaoNIM->where('pavilhao',$pavilhao);

        }

        if (!is_null($ala)) {

            $qtdHA->where('ala',$ala);
            $qtdMA->where('ala',$ala);
            $qtdFaccaoNIH->where('ala',$ala);
            $qtdFaccaoNIM->where('ala',$ala);

        }
        if (!is_null($cela)) {

            $qtdHA->where('cela',$cela);
            $qtdMA->where('cela',$cela);
            $qtdFaccaoNIH->where('cela',$cela);
            $qtdFaccaoNIM->where('cela',$cela);

        }


        $qtdHA=$qtdHA->count();
        $qtdMA=$qtdMA->count();
        $qtdFaccaoNIH = $qtdFaccaoNIH->count();
        $qtdFaccaoNIM = $qtdFaccaoNIM->count();


        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-faccionados-naofaccionados', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome', 'qtdHA', 'qtdMA', 'qtdFaccaoNIH', 'qtdFaccaoNIM'));

    }

    public function faccaoCriminosa(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if (!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if (!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if (!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if (!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status', '1')->get();




        $faccoes = DB::table('faccao')->select('id','nome','sigla')->get();
        foreach($faccoes as $key=>$faccao){
            $faccoes[$key]->qtdH = DB::table('interno')->where('sexo','M')->where('status','A')->where('faccao', $faccao->id);
            $faccoes[$key]->qtdM = DB::table('interno')->where('sexo','F')->where('status','A')->where('faccao', $faccao->id);

            if(!is_null($unidade)) {
                $faccoes[$key]->qtdH->where('unidade_prisional',$unidade);
                $faccoes[$key]->qtdM->where('unidade_prisional',$unidade);
            }
            if(!is_null($pavilhao)) {
                $faccoes[$key]->qtdH->where('pavilhao',$pavilhao);
                $faccoes[$key]->qtdM->where('pavilhao',$pavilhao);
            }
            if(!is_null($ala)) {
                $faccoes[$key]->qtdH->where('ala',$ala);
                $faccoes[$key]->qtdM->where('ala',$ala);
            }
            if(!is_null($cela)) {
                $faccoes[$key]->qtdH->where('cela',$cela);
                $faccoes[$key]->qtdM->where('cela',$cela);
            }

            $faccoes[$key]->qtdH = $faccoes[$key]->qtdH->count();
            $faccoes[$key]->qtdM = $faccoes[$key]->qtdM->count();
            $faccoes[$key]->qtd = $faccoes[$key]->qtdH + $faccoes[$key]->qtdM;
        }

        $faccoes = $faccoes->sortBy('qtd')->reverse();




        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-faccao-criminosa', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
            'faccoes'));

    }

    public function crimeGrupo(Request $request)
    {
        
        $unidade = $request->get('unidade_prisional');
        if (!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if (!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if (!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if (!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $unidadePost = $request->get('unidade_prisional');
        $pavilhaoPost = $request->get('pavilhao');
        $alaPost = $request->get('ala');
        $celaPost = $request->get('cela');
                
        //$detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status', '1')->get();

        $dataHora = Tdata::TtimeStampHojeBrTxt();

        $dadosGrupos = DB::table('interno_processos_dispositivo_legal')
                ->leftJoin('interno_processos', 'interno_processos.id', '=', 'interno_processos_dispositivo_legal.id_processo')
                ->where('interno_processos.status','A')
                ->select('interno_processos_dispositivo_legal.fk_grupo')
                ->distinct('fk_grupo')
                ->get();
        
        $grupos = array();
        
        $dadosInternosDB = DB::table('interno_processos_dispositivo_legal')
        ->leftJoin('interno_processos', 'interno_processos.id', '=', 'interno_processos_dispositivo_legal.id_processo')
        ->leftJoin('interno', 'interno.id', '=', 'interno_processos.fk_interno');
        $dadosInternosDB->where('interno_processos.status','A');
        
         if(!is_null($unidadePost))
         {
             $dadosInternosDB->where('unidade_prisional', $unidadePost);
         }
         if(!is_null($pavilhaoPost))
         {
             $dadosInternosDB->where('pavilhao', $pavilhaoPost);
         }
         if(!is_null($alaPost))
         {
             $dadosInternosDB->where('ala', $alaPost);
         }
         if(!is_null($celaPost))
         {
             $dadosInternosDB->where('cela', $celaPost);
         }
         $dadosInternosDB->select('interno_processos_dispositivo_legal.*','interno_processos.*','interno.unidade_prisional','interno.pavilhao','interno.ala','interno.cela');
         
        $dadosInternos = $dadosInternosDB->get();

        $internos = array();
        $qtdF = 0;
        $qtdM = 0;

        foreach($dadosGrupos as $dadosGrupo)
        {
            array_push($grupos, array(
                'id'=> $dadosGrupo->fk_grupo,
                'nome'=> ArtigosProcessosController::getNomeGrupo($dadosGrupo->fk_grupo),
                    ));
            
            foreach($dadosInternos as $dadosInterno)
            {
                if($dadosGrupo->fk_grupo == $dadosInterno->fk_grupo)
                {
                
                    $internoDados = InternoController::getDadosInterno($dadosInterno->fk_interno)[0];

                    if($internoDados->status == "A"){
                        
                        if($internoDados->sexo == "M")
                        {
                            $qtdM = $qtdM + 1;
                        }

                        if($internoDados->sexo == "F")
                        {
                            $qtdF = $qtdF + 1;
                        }

                    }

                }

            }
            
                array_push($internos, array(
                    'grupo' => $dadosGrupo->fk_grupo,
                    'qtdM' => $qtdM,
                    'qtdF' => $qtdF,
                ));

                $qtdM = 0;
                $qtdF = 0;

        }
        
                
        return view('estatisticas.show.estatisticas-crimes', compact('grupos','internos','dataHora', 'unidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome'));
        
    }


    public function periculosidade(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if (!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if (!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if (!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if (!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status', '1')->get();


        $periculosidadeAltaH = DB::table('interno')->where('periculosidade','Alta')->where('sexo','M')->where('status','A');
        $periculosidadeMediaH = DB::table('interno')->where('periculosidade','Média')->where('sexo','M')->where('status','A');
        $periculosidadeBaixaH = DB::table('interno')->where('periculosidade','Baixa')->where('sexo','M')->where('status','A');
        $periculosidadeNIH = DB::table('interno')->where('periculosidade',null)->where('sexo','M')->where('status','A');
        $periculosidadeAltaM = DB::table('interno')->where('periculosidade','Alta')->where('sexo','F')->where('status','A');
        $periculosidadeMediaM = DB::table('interno')->where('periculosidade','Média')->where('sexo','F')->where('status','A');
        $periculosidadeBaixaM = DB::table('interno')->where('periculosidade','Baixa')->where('sexo','F')->where('status','A');
        $periculosidadeNIM = DB::table('interno')->where('periculosidade',null)->where('sexo','F')->where('status','A');

        if (!is_null($unidade)) {

            $periculosidadeAltaH->where('unidade_prisional',$unidade);
            $periculosidadeMediaH->where('unidade_prisional',$unidade);
            $periculosidadeBaixaH->where('unidade_prisional',$unidade);
            $periculosidadeNIH->where('unidade_prisional',$unidade);
            $periculosidadeAltaM->where('unidade_prisional',$unidade);
            $periculosidadeMediaM->where('unidade_prisional',$unidade);
            $periculosidadeBaixaM->where('unidade_prisional',$unidade);
            $periculosidadeNIM->where('unidade_prisional',$unidade);

        }

        if (!is_null($pavilhao)) {

            $periculosidadeAltaH->where('pavilhao',$pavilhao);
            $periculosidadeMediaH->where('pavilhao',$pavilhao);
            $periculosidadeBaixaH->where('pavilhao',$pavilhao);
            $periculosidadeNIH->where('pavilhao',$pavilhao);
            $periculosidadeAltaM->where('pavilhao',$pavilhao);
            $periculosidadeMediaM->where('pavilhao',$pavilhao);
            $periculosidadeBaixaM->where('pavilhao',$pavilhao);
            $periculosidadeNIM->where('pavilhao',$pavilhao);

        }

        if (!is_null($ala)) {

            $periculosidadeAltaH->where('ala',$ala);
            $periculosidadeMediaH->where('ala',$ala);
            $periculosidadeBaixaH->where('ala',$ala);
            $periculosidadeNIH->where('ala',$ala);
            $periculosidadeAltaM->where('ala',$ala);
            $periculosidadeMediaM->where('ala',$ala);
            $periculosidadeBaixaM->where('ala',$ala);
            $periculosidadeNIM->where('ala',$ala);

        }
        if (!is_null($cela)) {

            $periculosidadeAltaH->where('cela',$cela);
            $periculosidadeMediaH->where('cela',$cela);
            $periculosidadeBaixaH->where('cela',$cela);
            $periculosidadeNIH->where('cela',$cela);
            $periculosidadeAltaM->where('cela',$cela);
            $periculosidadeMediaM->where('cela',$cela);
            $periculosidadeBaixaM->where('cela',$cela);
            $periculosidadeNIM->where('cela',$cela);

        }

        $periculosidadeAltaH=$periculosidadeAltaH->count();
        $periculosidadeMediaH=$periculosidadeMediaH->count();
        $periculosidadeBaixaH=$periculosidadeBaixaH->count();
        $periculosidadeNIH=$periculosidadeNIH->count();
        $periculosidadeAltaM=$periculosidadeAltaM->count();
        $periculosidadeMediaM=$periculosidadeMediaM->count();
        $periculosidadeBaixaM=$periculosidadeBaixaM->count();
        $periculosidadeNIM=$periculosidadeNIM->count();


        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-periculosidade', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
            'periculosidadeAltaH', 'periculosidadeMediaH', 'periculosidadeBaixaH', 'periculosidadeNIH', 'periculosidadeAltaM', 'periculosidadeMediaM', 'periculosidadeBaixaM',
            'periculosidadeNIM'));

    }

    public function lideranca(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if (!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if (!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if (!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if (!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status', '1')->get();


        $qtdLiderancaSimH = DB::table('interno')->where('lideranca','S')->where('sexo','M')->where('status','A');
        $qtdLiderancaNaoH = DB::table('interno')->where('lideranca','N')->where('sexo','M')->where('status','A');
        $qtdLiderancaNIH = DB::table('interno')->where('lideranca',null)->where('sexo','M')->where('status','A');
        $qtdLiderancaSimM = DB::table('interno')->where('lideranca','S')->where('sexo','F')->where('status','A');
        $qtdLiderancaNaoM = DB::table('interno')->where('lideranca','N')->where('sexo','F')->where('status','A');
        $qtdLiderancaNIM = DB::table('interno')->where('lideranca',null)->where('sexo','F')->where('status','A');

        if (!is_null($unidade)) {

            $qtdLiderancaSimH->where('unidade_prisional',$unidade);
            $qtdLiderancaNaoH->where('unidade_prisional',$unidade);
            $qtdLiderancaNIH->where('unidade_prisional',$unidade);
            $qtdLiderancaSimM->where('unidade_prisional',$unidade);
            $qtdLiderancaNaoM->where('unidade_prisional',$unidade);
            $qtdLiderancaNIM->where('unidade_prisional',$unidade);

        }

        if (!is_null($pavilhao)) {

            $qtdLiderancaSimH->where('pavilhao',$pavilhao);
            $qtdLiderancaNaoH->where('pavilhao',$pavilhao);
            $qtdLiderancaNIH->where('pavilhao',$pavilhao);
            $qtdLiderancaSimM->where('pavilhao',$pavilhao);
            $qtdLiderancaNaoM->where('pavilhao',$pavilhao);
            $qtdLiderancaNIM->where('pavilhao',$pavilhao);

        }

        if (!is_null($ala)) {

            $qtdLiderancaSimH->where('ala',$ala);
            $qtdLiderancaNaoH->where('ala',$ala);
            $qtdLiderancaNIH->where('ala',$ala);
            $qtdLiderancaSimM->where('ala',$ala);
            $qtdLiderancaNaoM->where('ala',$ala);
            $qtdLiderancaNIM->where('ala',$ala);

        }
        if (!is_null($cela)) {

            $qtdLiderancaSimH->where('cela',$cela);
            $qtdLiderancaNaoH->where('cela',$cela);
            $qtdLiderancaNIH->where('cela',$cela);
            $qtdLiderancaSimM->where('cela',$cela);
            $qtdLiderancaNaoM->where('cela',$cela);
            $qtdLiderancaNIM->where('cela',$cela);

        }

        $qtdLiderancaSimH=$qtdLiderancaSimH->count();
        $qtdLiderancaNaoH=$qtdLiderancaNaoH->count();
        $qtdLiderancaNIH=$qtdLiderancaNIH->count();
        $qtdLiderancaSimM=$qtdLiderancaSimM->count();
        $qtdLiderancaNaoM=$qtdLiderancaNaoM->count();
        $qtdLiderancaNIM=$qtdLiderancaNIM->count();


        $dataHora = Tdata::TtimeStampHojeBrTxt();


        return view('estatisticas.show.estatisticas-lideranca', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
            'qtdLiderancaSimH', 'qtdLiderancaNaoH', 'qtdLiderancaNIH', 'qtdLiderancaSimM', 'qtdLiderancaNaoM', 'qtdLiderancaNIM'));

    }

    public function entradaSaidaPorUnidade(Request $request)
    {

        $unidade = $request->get('unidade_prisional');
        if (!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if (!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if (!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if (!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;
        if (!is_null($request->get('dataini'))){

            $dataini = TData::TdataMysql($request->get('dataini'));

        } else $dataini = null;

        if (!is_null($request->get('datafin'))){

            $datafin = $request->get('datafin');

            $datafin = TData::TdataMysql($datafin);

            $datafin = TData::addDias($datafin, 1);

            //dd($datafin);
        } else $datafin = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status', '1')->get();

        $qtdInternosLotSelecionada = DB::table('interno')->where('status', 'A');

        $datai = mktime(00,00,00,02,19,2019);


        /*$query = DB::table('interno')
            ->leftJoin('unidade', 'unidade.id', 'interno.unidade_prisional')
            ->where('interno.status', 'A')
            ->where('unidade.status', '1')
            ->groupBy('unidade.sigla');

        if($unidade != null && $unidade > 0){
            $query = $query
                ->where('interno.unidade_prisional', $unidade);
            if($pavilhao != null && $pavilhao > 0){
                $query = $query->where('interno.pavilhao', $pavilhao);
                if($ala != null && $ala > 0){
                    $query = $query->where('interno.ala', $ala);
                    if($cela != null && $cela > 0){
                        $query = $query->where('interno.cela', $cela);
                    }
                }
            }
        }


        $query = $query->select('unidade.sigla AS sigla',
            DB::raw('COUNT(CASE WHEN interno.sentenciado = \'S\' AND interno.regime=\'ABERTO\' AND interno.medida_seguranca=\'N\'  THEN 1 END) AS aberto'),
            DB::raw('COUNT(CASE WHEN interno.sentenciado = \'S\' AND interno.regime=\'SEMIABERTO\' AND interno.medida_seguranca=\'N\' THEN 1 END) AS semiaberto'),
            DB::raw('COUNT(CASE WHEN interno.sentenciado = \'S\' AND interno.regime=\'FECHADO\' AND interno.medida_seguranca=\'N\' THEN 1 END) AS fechado'),
            DB::raw('COUNT(CASE WHEN interno.sentenciado=\'N\'  AND interno.regime IS NULL AND interno.medida_seguranca = \'N\'  THEN 1 END) AS provisorio'),
            DB::raw('COUNT(CASE WHEN interno.sentenciado=\'S\' AND interno.regime IS NOT NULL  AND interno.medida_seguranca = \'N\' THEN 1 END) AS sentenciado'),
            DB::raw('COUNT(CASE WHEN interno.medida_seguranca=\'S\' AND interno.regime IS NULL AND interno.sentenciado = \'S\' THEN 1 END) AS medida_seg'),
            DB::raw('COUNT(CASE WHEN interno.sentenciado=\'N\'  AND interno.regime IS NULL  AND interno.medida_seguranca = \'S\'  THEN 1 END) AS medida_seg_provisorio'),
            DB::raw('COUNT(CASE WHEN (interno.sentenciado=\'S\'  AND interno.regime IS NOT NULL  AND interno.medida_seguranca = \'S\')
	            OR (interno.sentenciado=\'N\'  AND interno.regime IS NOT NULL )
	            OR (interno.sentenciado=\'S\'  AND interno.regime IS NULL AND interno.medida_seguranca = \'N\')
	            OR (interno.medida_seguranca=\'S\'  AND interno.regime IS NOT NULL )
	            OR (interno.medida_seguranca IS NULL  OR interno.sentenciado IS NULL ) THEN 1 END) AS inconsistente'),
            DB::raw('COUNT(CASE WHEN interno.nome IS NOT NULL     THEN 1 END) AS total'));

        return $query->get();*/



         if($dataini != null){


            if (preg_match('/^[0-9]{4}[-][0-9]{1,2}[-][0-9]{1,2}$/', $dataini) && preg_match('/^[0-9]{4}[-][0-9]{1,2}[-][0-9]{1,2}$/', $datafin)) {

                if ($dataini < date("Y-m-d", $datai)) {


                    //return redirect()->back()->withInput()->with('alert-danger', 'Não foi possível atender a solicitação. Digite uma data a partir de 19/02/2019.');
                    $msg = redirect()->back()->withInput()->with('alert-danger', 'Não foi possível atender a solicitação. Digite uma data inicial a partir de 19/02/2019.');
                    $dataHora = Tdata::TtimeStampHojeBrTxt();
                    $qntEntradas = "-";
                    $qntSaidas = "-";

                    return view('estatisticas.show.estatisticas-Entrada-saida-por-unidade', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
                        'qtdInternosLotSelecionada', 'qntEntradas', 'qntSaidas', 'dataini', 'msg', 'datafin'));

                }else {

                    $qntEntradas = DB::table('movimentacao')
                        ->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id')
                        ->where('movimentacao.unidade_destino', '=', $unidade)
                        ->where('movimentacao.tipo', 'entradaunidadeprisional')
                        ->where('movimentacao_interno.status','Finalizada')
                        ->where('movimentacao.data', '>', $dataini)
                        ->where('movimentacao.data', '<', $datafin);


                    $qntSaidas = DB::table('movimentacao')
                        ->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id')
                        ->where('movimentacao_interno.up_anterior', '=', $unidade)
                        ->where('movimentacao_interno.status','Finalizada')
                        ->where('movimentacao.data', '>', $dataini)
                        ->where('movimentacao.data', '<', $datafin)
                    ->where(function($query){
                    $query->where('movimentacao.tipo', 'alvara')
                        ->orwhere('movimentacao.tipo', 'obito')
                        ->orwhere('movimentacao.tipo', 'recambiamento')
                        ->orwhere('movimentacao.tipo', 'saidajudicial')
                        ->orwhere('movimentacao.tipo', 'fuga')
                        ->orwhere('movimentacao.tipo', 'livramentocondicional')
                        ->orwhere('movimentacao.tipo', 'extincaopunibilidade')
                        ->orwhere('movimentacao.tipo', 'prisaodomiciliar')
                        ->orwhere('movimentacao.tipo', 'extradicao');
                    });

                    $proSemiaberto = DB::table('movimentacao_interno')
                        ->leftJoin('movimentacao', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id')
                        ->where('movimentacao_interno.up_anterior', '=', $unidade)
                        ->where('movimentacao.tipo', 'progressaosemiaberto')
                        ->where('movimentacao_interno.status','Finalizada')
                        ->where('movimentacao.data', '>', $dataini)
                        ->where('movimentacao.data', '<', $datafin);

                    $proAberto = DB::table('movimentacao_interno')
                        ->leftJoin('movimentacao', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id')
                        ->where('movimentacao_interno.up_anterior', '=', $unidade)
                        ->where('movimentacao.tipo', 'progressaoaberto')
                        ->where('movimentacao_interno.status','Finalizada')
                        ->where('movimentacao.data', '>', $dataini)
                        ->where('movimentacao.data', '<', $datafin);

                    if(!is_null($unidade)){

                        $qntEntradas->where('movimentacao.unidade_destino', '=', $unidade);

                        $qntSaidas->where('movimentacao_interno.up_anterior', '=', $unidade);

                        $proSemiaberto->where('movimentacao_interno.up_anterior', '=', $unidade);

                        $proAberto->where('movimentacao_interno.up_anterior', '=', $unidade);
                    }

                    if(!is_null($pavilhao)){

                        $qntEntradas->where('movimentacao.pavilhao_destino', '=', $pavilhao);

                        $qntSaidas->where('movimentacao_interno.pv_anterior', '=', $pavilhao);

                        $proSemiaberto->where('movimentacao_interno.pv_anterior', '=', $pavilhao);

                        $proAberto->where('movimentacao_interno.pv_anterior', '=', $pavilhao);
                    }

                    if(!is_null($ala)){

                        $qntEntradas->where('movimentacao.ala_destino', '=', $ala);

                        $qntSaidas->where('movimentacao_interno.ala_anterior', '=', $ala);

                        $proSemiaberto->where('movimentacao_interno.ala_anterior', '=', $ala);

                        $proAberto->where('movimentacao_interno.ala_anterior', '=', $ala);
                    }

                    if(!is_null($cela)){

                        $qntEntradas->where('movimentacao.cela_destino', '=', $cela);

                        $qntSaidas->where('movimentacao_interno.cela_anterior', '=', $cela);

                        $proSemiaberto->where('movimentacao_interno.cela_anterior', '=', $cela);

                        $proAberto->where('movimentacao_interno.cela_anterior', '=', $cela);
                    }

                    $qntEntradas = $qntEntradas->count();
                    $qntSaidas = $qntSaidas->count();
                    $proSemiaberto = $proSemiaberto->count();
                    $proAberto = $proAberto->count();

                }
            }else {

                //return redirect()->back()->withInput()->with('alert-danger', 'Data inválida. Digite uma data válida. Ex.: 00/00/0000');
                $msg = redirect()->back()->withInput()->with('alert-danger', 'Verifique as datas digitadas. Digite datas válidas. Ex.: 00/00/0000');
                $dataHora = Tdata::TtimeStampHojeBrTxt();
                $qntEntradas = "-";
                $qntSaidas = "-";

                return view('estatisticas.show.estatisticas-Entrada-saida-por-unidade', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
                    'qtdInternosLotSelecionada', 'qntEntradas', 'qntSaidas', 'dataini', 'proAberto', 'proSemiaberto', 'msg', 'datafin'));

            }
        }else {


                $qntEntradas = "-";
                $qntSaidas = "-";
                $proSemiaberto = "-";
                $proAberto = "-";



        }


        $dataHora = Tdata::TtimeStampHojeBrTxt();



        return view('estatisticas.show.estatisticas-Entrada-saida-por-unidade', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
            'qtdInternosLotSelecionada', 'qntEntradas', 'qntSaidas', 'dataini','datafin', 'proSemiaberto', 'proAberto'));



    }


    public function show(Request $request){
    
        set_time_limit(3600);

        $unidade = $request->get('unidade_prisional');
        if(!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if(!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if(!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if(!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        $qtdInternosLotSelecionada = DB::table('interno')->where('status','A');

        $qtdUnidadeHA = DB::table('interno')
            ->leftJoin('unidade','unidade.id','interno.unidade_prisional')
            ->where('sexo', 'M')->where('interno.status','A')
            ->select(DB::raw('unidade.sigla, count(*) as qtd'))
            ->groupBy('unidade.sigla')->orderBy('qtd','DESC');

        $qtdUnidadeMA = DB::table('interno')
            ->leftJoin('unidade','unidade.id','interno.unidade_prisional')
            ->where('sexo', 'F')->where('interno.status','A')
            ->select(DB::raw('unidade.sigla, count(*) as qtd'))
            ->groupBy('unidade.sigla')->orderBy('qtd','DESC');

        $qtdUnidadeTotal  = DB::table('interno')
            ->leftJoin('unidade','unidade.id','interno.unidade_prisional')
            ->select(DB::raw('unidade.sigla, count(*) as qtd'))
            ->where('interno.status','A')->groupBy('unidade.sigla')->orderBy('qtd','DESC');

        $qtdRegimeNIH = DB::table('interno')->where('regime', null)->where('sentenciado','S')->where('sexo','M')->where('medida_seguranca','=','N')->where('status','A');
        $qtdRegimeNIM = DB::table('interno')->where('regime', null)->where('sentenciado','S')->where('sexo','F')->where('medida_seguranca','=','N')->where('status','A');
        $qtdRegimeFechadoH = DB::table('interno')->where('sexo','M')->where('regime', 'FECHADO')->where('status','A');
        $qtdRegimeFechadoM = DB::table('interno')->where('sexo','F')->where('regime', 'FECHADO')->where('status','A');
        $qtdRegimeSemiabertoH = DB::table('interno')->where('sexo','M')->where('regime', 'SEMIABERTO')->where('status','A');
        $qtdRegimeSemiabertoM = DB::table('interno')->where('sexo','F')->where('regime', 'SEMIABERTO')->where('status','A');
        $qtdRegimeAbertoH = DB::table('interno')->where('sexo','M')->where('regime', 'ABERTO')->where('status','A');
        $qtdRegimeAbertoM = DB::table('interno')->where('sexo','F')->where('regime', 'ABERTO')->where('status','A');

        $qtdSentenciadosNIH = DB::table('interno')->where('sexo','M')->where('sentenciado', null)->where('medida_seguranca','N')->where('status','A');
        $qtdSentenciadosNIM = DB::table('interno')->where('sexo','F')->where('sentenciado', null)->where('medida_seguranca','N')->where('status','A');
        $qtdSentenciadosH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'S')->where('medida_seguranca','N')->where('status','A');
        $qtdSentenciadosM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'S')->where('medida_seguranca','N')->where('status','A');
        $qtdProvisoriosH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'N')->where('medida_seguranca','N')->where('status','A');
        $qtdProvisoriosM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'N')->where('medida_seguranca','N')->where('status','A');
        
        $qtdSentenciadosMSNIH = DB::table('interno')->where('sexo','M')->where('sentenciado', null)->where('medida_seguranca','S')->where('status','A');
        $qtdSentenciadosMSNIM = DB::table('interno')->where('sexo','F')->where('sentenciado', null)->where('medida_seguranca','S')->where('status','A');
        $qtdSentenciadosMSH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'S')->where('medida_seguranca','S')->where('status','A');
        $qtdSentenciadosMSM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'S')->where('medida_seguranca','S')->where('status','A');
        $qtdProvisoriosMSH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'N')->where('medida_seguranca','S')->where('status','A');
        $qtdProvisoriosMSM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'N')->where('medida_seguranca','S')->where('status','A');

        $qtdMedidaDeSegurancaH = DB::table('interno')->where('sexo','M')->where('medida_seguranca', 'S')->where('status','A');
        $qtdMedidaDeSegurancaM = DB::table('interno')->where('sexo','F')->where('medida_seguranca', 'S')->where('status','A');

        $qtdHA = DB::table('interno')->where('sexo', 'M')->where('status','A');
        $qtdMA = DB::table('interno')->where('sexo', 'F')->where('status','A');

        $faixaEtaria_naoInformadaH = 0;
        $faixaEtaria_zero_a_dezesseteH = 0;
        $faixaEtaria_dezoito_a_vinteequatroH = 0;
        $faixaEtaria_vinteecinco_a_vinteenoveH = 0;
        $faixaEtaria_trinta_a_trintaequatroH = 0;
        $faixaEtaria_trintaecinco_a_quarentaecincoH = 0;
        $faixaEtaria_quarentaeseis_a_sessentaH = 0;
        $faixaEtaria_sessentaeum_a_setentaH = 0;
        $faixaEtaria_maisDeSetentaH = 0;

        $faixaEtaria_naoInformadaM = 0;
        $faixaEtaria_zero_a_dezesseteM = 0;
        $faixaEtaria_dezoito_a_vinteequatroM = 0;
        $faixaEtaria_vinteecinco_a_vinteenoveM = 0;
        $faixaEtaria_trinta_a_trintaequatroM = 0;
        $faixaEtaria_trintaecinco_a_quarentaecincoM = 0;
        $faixaEtaria_quarentaeseis_a_sessentaM = 0;
        $faixaEtaria_sessentaeum_a_setentaM = 0;
        $faixaEtaria_maisDeSetentaM = 0;

        $internosH = DB::table('interno')->select('data_nascimento')->where('sexo','M')->where('status','A');
        $internosM = DB::table('interno')->select('data_nascimento')->where('sexo','F')->where('status','A');

        $qtdFaccaoNIH = DB::table('interno')->where('status','A')->where('sexo','M')->where('faccao', null);
        $qtdFaccaoNIM = DB::table('interno')->where('status','A')->where('sexo','F')->where('faccao', null);

        //periculosidade
        $periculosidadeAltaH = DB::table('interno')->where('periculosidade','Alta')->where('sexo','M')->where('status','A');
        $periculosidadeMediaH = DB::table('interno')->where('periculosidade','Média')->where('sexo','M')->where('status','A');
        $periculosidadeBaixaH = DB::table('interno')->where('periculosidade','Baixa')->where('sexo','M')->where('status','A');
        $periculosidadeNIH = DB::table('interno')->where('periculosidade',null)->where('sexo','M')->where('status','A');
        $periculosidadeAltaM = DB::table('interno')->where('periculosidade','Alta')->where('sexo','F')->where('status','A');
        $periculosidadeMediaM = DB::table('interno')->where('periculosidade','Média')->where('sexo','F')->where('status','A');
        $periculosidadeBaixaM = DB::table('interno')->where('periculosidade','Baixa')->where('sexo','F')->where('status','A');
        $periculosidadeNIM = DB::table('interno')->where('periculosidade',null)->where('sexo','F')->where('status','A');

        //liderança
        $qtdLiderancaSimH = DB::table('interno')->where('lideranca','S')->where('sexo','M')->where('status','A');
        $qtdLiderancaNaoH = DB::table('interno')->where('lideranca','N')->where('sexo','M')->where('status','A');
        $qtdLiderancaNIH = DB::table('interno')->where('lideranca',null)->where('sexo','M')->where('status','A');
        $qtdLiderancaSimM = DB::table('interno')->where('lideranca','S')->where('sexo','F')->where('status','A');
        $qtdLiderancaNaoM = DB::table('interno')->where('lideranca','N')->where('sexo','F')->where('status','A');
        $qtdLiderancaNIM = DB::table('interno')->where('lideranca',null)->where('sexo','F')->where('status','A');

        // estado_civil
        $qtdSolteirosH = DB::table('interno')->where('estado_civil','solteiro')->where('sexo','M')->where('status','A');
        $qtdSolteirosM = DB::table('interno')->where('estado_civil','solteiro')->where('sexo','F')->where('status','A');
        $qtdCasadosH = DB::table('interno')->where('estado_civil','casado')->where('sexo','M')->where('status','A');
        $qtdCasadosM = DB::table('interno')->where('estado_civil','casado')->where('sexo','F')->where('status','A');
        $qtdUniaoEstavelH = DB::table('interno')->where('estado_civil','uniao estavel')->where('sexo','M')->where('status','A');
        $qtdUniaoEstavelM = DB::table('interno')->where('estado_civil','uniao estavel')->where('sexo','F')->where('status','A');
        $qtdSeparadosH = DB::table('interno')->where('estado_civil','separado')->where('sexo','M')->where('status','A');
        $qtdSeparadosM = DB::table('interno')->where('estado_civil','separado')->where('sexo','F')->where('status','A');
        $qtdDivorciadosH = DB::table('interno')->where('estado_civil','divorciado')->where('sexo','M')->where('status','A');
        $qtdDivorciadosM = DB::table('interno')->where('estado_civil','divorciado')->where('sexo','F')->where('status','A');
        $qtdViuvosH = DB::table('interno')->where('estado_civil','viuvo')->where('sexo','M')->where('status','A');
        $qtdViuvosM = DB::table('interno')->where('estado_civil','viuvo')->where('sexo','F')->where('status','A');
        $qtdEstadoCivilNIH = DB::table('interno')->where('estado_civil',null)->where('sexo','M')->where('status','A');
        $qtdEstadoCivilNIM = DB::table('interno')->where('estado_civil',null)->where('sexo','F')->where('status','A');

        // opcao_sexual
        $qtdHeteroH = DB::table('interno')->where('opcao_sexual','HETEROSSEXUAL')->where('sexo','M')->where('status','A');
        $qtdHeteroM = DB::table('interno')->where('opcao_sexual','HETEROSSEXUAL')->where('sexo','F')->where('status','A');
        $qtdHomoH = DB::table('interno')->where('opcao_sexual','HOMOSSEXUAL')->where('sexo','M')->where('status','A');
        $qtdHomoM = DB::table('interno')->where('opcao_sexual','HOMOSSEXUAL')->where('sexo','F')->where('status','A');
        $qtdBiH = DB::table('interno')->where('opcao_sexual','BISSEXUAL')->where('sexo','M')->where('status','A');
        $qtdBiM = DB::table('interno')->where('opcao_sexual','BISSEXUAL')->where('sexo','F')->where('status','A');
        $qtdSexualidadeNaoDeclH = DB::table('interno')->where('opcao_sexual','nao declarado')->where('sexo','M')->where('status','A');
        $qtdSexualidadeNaoDeclM = DB::table('interno')->where('opcao_sexual','nao declarado')->where('sexo','F')->where('status','A');
        $qtdSexualidadeNIH = DB::table('interno')->where('opcao_sexual',null)->where('sexo','M')->where('status','A');
        $qtdSexualidadeNIM = DB::table('interno')->where('opcao_sexual',null)->where('sexo','F')->where('status','A');

        // etnia
        $qtdEtniaBrancaH = DB::table('interno')->where('etnia','Branca')->where('sexo','M')->where('status','A');
        $qtdEtniaBrancaM = DB::table('interno')->where('etnia','Branca')->where('sexo','F')->where('status','A');
        $qtdEtniaIndígenaH = DB::table('interno')->where('etnia','Indígena')->where('sexo','M')->where('status','A');
        $qtdEtniaIndígenaM = DB::table('interno')->where('etnia','Indígena')->where('sexo','F')->where('status','A');
        $qtdEtniaPretaH = DB::table('interno')->where('etnia','Preta')->where('sexo','M')->where('status','A');
        $qtdEtniaPretaM = DB::table('interno')->where('etnia','Preta')->where('sexo','F')->where('status','A');
        $qtdEtniaPardaH = DB::table('interno')->where('etnia','Parda')->where('sexo','M')->where('status','A');
        $qtdEtniaPardaM = DB::table('interno')->where('etnia','Parda')->where('sexo','F')->where('status','A');
        $qtdEtniaAmarelaH = DB::table('interno')->where('etnia','Amarela')->where('sexo','M')->where('status','A');
        $qtdEtniaAmarelaM = DB::table('interno')->where('etnia','Amarela')->where('sexo','F')->where('status','A');
        $qtdEtniaNIH = DB::table('interno')->where('etnia',null)->where('sexo','M')->where('status','A');
        $qtdEtniaNIM = DB::table('interno')->where('etnia',null)->where('sexo','F')->where('status','A');

        // escolaridade
        $qtdAnalfabetoH = DB::table('interno')->where('escolaridade','Analfabeto')->where('sexo','M')->where('status','A');
        $qtdAnalfabetoM = DB::table('interno')->where('escolaridade','Analfabeto')->where('sexo','F')->where('status','A');
        $qtdAlfabetizadoH = DB::table('interno')->where('escolaridade','Alfabetizado')->where('sexo','M')->where('status','A');
        $qtdAlfabetizadoM = DB::table('interno')->where('escolaridade','Alfabetizado')->where('sexo','F')->where('status','A');
        $qtdFundIncH = DB::table('interno')->where('escolaridade','Ensino Fundamental Incompleto')->where('sexo','M')->where('status','A');
        $qtdFundIncM = DB::table('interno')->where('escolaridade','Ensino Fundamental Incompleto')->where('sexo','F')->where('status','A');
        $qtdFundCompH = DB::table('interno')->where('escolaridade','Ensino Fundamental Completo')->where('sexo','M')->where('status','A');
        $qtdFundCompM = DB::table('interno')->where('escolaridade','Ensino Fundamental Completo')->where('sexo','F')->where('status','A');
        $qtdMedioIncompH = DB::table('interno')->where('escolaridade','Ensino Médio Incompleto')->where('sexo','M')->where('status','A');
        $qtdMedioIncompM = DB::table('interno')->where('escolaridade','Ensino Médio Incompleto')->where('sexo','F')->where('status','A');
        $qtdMedioCompH = DB::table('interno')->where('escolaridade','Ensino Médio Completo')->where('sexo','M')->where('status','A');
        $qtdMedioCompM = DB::table('interno')->where('escolaridade','Ensino Médio Completo')->where('sexo','F')->where('status','A');
        $qtdSuperiorIncompH = DB::table('interno')->where('escolaridade','Ensino Superior Incompleto')->where('sexo','M')->where('status','A');
        $qtdSuperiorIncompM = DB::table('interno')->where('escolaridade','Ensino Superior Incompleto')->where('sexo','F')->where('status','A');
        $qtdSuperiorCompH = DB::table('interno')->where('escolaridade','Ensino Superior Completo')->where('sexo','M')->where('status','A');
        $qtdSuperiorCompM = DB::table('interno')->where('escolaridade','Ensino Superior Completo')->where('sexo','F')->where('status','A');
        $qtdPosLatoSensuH = DB::table('interno')->where('escolaridade','Pós-graduação lato sensu')->where('sexo','M')->where('status','A');
        $qtdPosLatoSensuM = DB::table('interno')->where('escolaridade','Pós-graduação lato sensu')->where('sexo','F')->where('status','A');
        $qtdMestradoH = DB::table('interno')->where('escolaridade','Mestrado')->where('sexo','M')->where('status','A');
        $qtdMestradoM = DB::table('interno')->where('escolaridade','Mestrado')->where('sexo','F')->where('status','A');
        $qtdDoutoradoH = DB::table('interno')->where('escolaridade','Doutorado')->where('sexo','M')->where('status','A');
        $qtdDoutoradoM = DB::table('interno')->where('escolaridade','Doutorado')->where('sexo','F')->where('status','A');
        $qtdPosDoutoradoH = DB::table('interno')->where('escolaridade','Pós-Doutorado')->where('sexo','M')->where('status','A');
        $qtdPosDoutoradoM = DB::table('interno')->where('escolaridade','Pós-Doutorado')->where('sexo','F')->where('status','A');
        $qtdEscolaridadeNIH = DB::table('interno')->where('escolaridade',null)->where('sexo','M')->where('status','A');
        $qtdEscolaridadeNIM = DB::table('interno')->where('escolaridade',null)->where('sexo','F')->where('status','A');
        
        $qtdNacionalidadeNIH = DB::table('interno')->where('naturalidade_pais',null)->where('sexo','M')->where('status','A');
        $qtdNacionalidadeNIM = DB::table('interno')->where('naturalidade_pais',null)->where('sexo','F')->where('status','A');
        
        //religião
        $qtdAdventistaH = DB::table('interno')->where('religiao','Adventista')->where('sexo','M')->where('status','A');
        $qtdAdventistaM = DB::table('interno')->where('religiao','Adventista')->where('sexo','F')->where('status','A');
        $qtdBudistaH = DB::table('interno')->where('religiao','Budista')->where('sexo','M')->where('status','A');
        $qtdBudistaM = DB::table('interno')->where('religiao','Budista')->where('sexo','F')->where('status','A');
        $qtdCatolicaH = DB::table('interno')->where('religiao','Católica')->where('sexo','M')->where('status','A');
        $qtdCatolicaM = DB::table('interno')->where('religiao','Católica')->where('sexo','F')->where('status','A');
        $qtdEspiritaH = DB::table('interno')->where('religiao','Espírita')->where('sexo','M')->where('status','A');
        $qtdEspiritaM = DB::table('interno')->where('religiao','Espírita')->where('sexo','F')->where('status','A');
        $qtdEvangelicaH = DB::table('interno')->where('religiao','Evangélica')->where('sexo','M')->where('status','A');
        $qtdEvangelicaM = DB::table('interno')->where('religiao','Evangélica')->where('sexo','F')->where('status','A');
        $qtdJudeuH = DB::table('interno')->where('religiao','Judeu')->where('sexo','M')->where('status','A');
        $qtdJudeuM = DB::table('interno')->where('religiao','Judeu')->where('sexo','F')->where('status','A');
        $qtdMormonH = DB::table('interno')->where('religiao','Mórmon')->where('sexo','M')->where('status','A');
        $qtdMormonM = DB::table('interno')->where('religiao','Mórmon')->where('sexo','F')->where('status','A');
        $qtdMulcumanaH = DB::table('interno')->where('religiao','Muçulmana')->where('sexo','M')->where('status','A');
        $qtdMulcumanaM = DB::table('interno')->where('religiao','Muçulmana')->where('sexo','F')->where('status','A');
        $qtdProtestanteH = DB::table('interno')->where('religiao','Protestante')->where('sexo','M')->where('status','A');
        $qtdProtestanteM = DB::table('interno')->where('religiao','Protestante')->where('sexo','F')->where('status','A');
        $qtdAgnosticoH = DB::table('interno')->where('religiao','Agnóstico')->where('sexo','M')->where('status','A');
        $qtdAgnosticoM = DB::table('interno')->where('religiao','Agnóstico')->where('sexo','F')->where('status','A');
        $qtdAteuH = DB::table('interno')->where('religiao','Ateu')->where('sexo','M')->where('status','A');
        $qtdAteuM = DB::table('interno')->where('religiao','Ateu')->where('sexo','F')->where('status','A');
        $qtdSemReligiaoH = DB::table('interno')->where('religiao','Sem Religião')->where('sexo','M')->where('status','A');
        $qtdSemReligiaoM = DB::table('interno')->where('religiao','Sem Religião')->where('sexo','F')->where('status','A');
        $qtdOutrasH = DB::table('interno')->where('religiao','Outras')->where('sexo','M')->where('status','A');
        $qtdOutrasM = DB::table('interno')->where('religiao','Outras')->where('sexo','F')->where('status','A');
        $qtdReligiaoNIH = DB::table('interno')->where('religiao',null)->where('sexo','M')->where('status','A');
        $qtdReligiaoNIM = DB::table('interno')->where('religiao',null)->where('sexo','F')->where('status','A');


        //Saída da Unidade Prisional
        $qtdAlvara = DB::table('movimentacao')->where('tipo','alvara');
        $qtdFuga = DB::table('movimentacao')->where('tipo','fuga');
        $qtdRecambiamento = DB::table('movimentacao')->where('tipo','recambiamento');
        $qtdSaidaJudicial = DB::table('movimentacao')->where('tipo','saidajudicial');
        $qtdExtradicao = DB::table('movimentacao')->where('tipo','extradicao');
        $qtdLivramento = DB::table('movimentacao')->where('tipo','livramentocondicional');
        $qtdObito = DB::table('movimentacao')->where('tipo','obito');
        $qtdExtincaopunibilidade = DB::table('movimentacao')->where('tipo','extincaopunibilidade');

        $qtdAlvara->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdFuga->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdRecambiamento->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdSaidaJudicial->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdExtradicao->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdLivramento->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdObito->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdExtincaopunibilidade->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');


        if(!is_null($unidade)){
            $qtdUnidadeHA->where('unidade_prisional', $unidade);
            $qtdUnidadeMA->where('unidade_prisional', $unidade);
            $qtdUnidadeTotal->where('unidade_prisional', $unidade);

            $qtdInternosLotSelecionada->where('unidade_prisional',$unidade);

            $qtdRegimeNIH->where('unidade_prisional',$unidade);
            $qtdRegimeNIM->where('unidade_prisional',$unidade);
            $qtdRegimeFechadoH->where('unidade_prisional',$unidade);
            $qtdRegimeFechadoM->where('unidade_prisional',$unidade);
            $qtdRegimeSemiabertoH->where('unidade_prisional',$unidade);
            $qtdRegimeSemiabertoM->where('unidade_prisional',$unidade);
            $qtdRegimeAbertoH->where('unidade_prisional',$unidade);
            $qtdRegimeAbertoM->where('unidade_prisional',$unidade);

            $qtdSentenciadosNIH->where('unidade_prisional',$unidade);
            $qtdSentenciadosNIM->where('unidade_prisional',$unidade);
            $qtdSentenciadosH->where('unidade_prisional',$unidade);
            $qtdSentenciadosM->where('unidade_prisional',$unidade);
            $qtdProvisoriosH->where('unidade_prisional',$unidade);
            $qtdProvisoriosM->where('unidade_prisional',$unidade);
            
            $qtdSentenciadosMSNIH->where('unidade_prisional',$unidade);
            $qtdSentenciadosMSNIM->where('unidade_prisional',$unidade);
            $qtdSentenciadosMSH->where('unidade_prisional',$unidade);
            $qtdSentenciadosMSM->where('unidade_prisional',$unidade);
            $qtdProvisoriosMSH->where('unidade_prisional',$unidade);
            $qtdProvisoriosMSM->where('unidade_prisional',$unidade);

            $qtdMedidaDeSegurancaH->where('unidade_prisional',$unidade);
            $qtdMedidaDeSegurancaM->where('unidade_prisional',$unidade);

            $qtdHA->where('unidade_prisional',$unidade);
            $qtdMA->where('unidade_prisional',$unidade);

            $internosH->where('unidade_prisional',$unidade);
            $internosM->where('unidade_prisional',$unidade);

            $qtdFaccaoNIH->where('unidade_prisional',$unidade);
            $qtdFaccaoNIM->where('unidade_prisional',$unidade);

            $periculosidadeAltaH->where('unidade_prisional',$unidade);
            $periculosidadeMediaH->where('unidade_prisional',$unidade);
            $periculosidadeBaixaH->where('unidade_prisional',$unidade);
            $periculosidadeNIH->where('unidade_prisional',$unidade);
            $periculosidadeAltaM->where('unidade_prisional',$unidade);
            $periculosidadeMediaM->where('unidade_prisional',$unidade);
            $periculosidadeBaixaM->where('unidade_prisional',$unidade);
            $periculosidadeNIM->where('unidade_prisional',$unidade);

            $qtdLiderancaSimH->where('unidade_prisional',$unidade);
            $qtdLiderancaNaoH->where('unidade_prisional',$unidade);
            $qtdLiderancaNIH->where('unidade_prisional',$unidade);
            $qtdLiderancaSimM->where('unidade_prisional',$unidade);
            $qtdLiderancaNaoM->where('unidade_prisional',$unidade);
            $qtdLiderancaNIM->where('unidade_prisional',$unidade);

            //estado_civil
            $qtdSolteirosH->where('unidade_prisional',$unidade);
            $qtdSolteirosM->where('unidade_prisional',$unidade);
            $qtdCasadosH->where('unidade_prisional',$unidade);
            $qtdCasadosM->where('unidade_prisional',$unidade);
            $qtdUniaoEstavelH->where('unidade_prisional',$unidade);
            $qtdUniaoEstavelM->where('unidade_prisional',$unidade);
            $qtdSeparadosH->where('unidade_prisional',$unidade);
            $qtdSeparadosM->where('unidade_prisional',$unidade);
            $qtdDivorciadosH->where('unidade_prisional',$unidade);
            $qtdDivorciadosM->where('unidade_prisional',$unidade);
            $qtdViuvosH->where('unidade_prisional',$unidade);
            $qtdViuvosM->where('unidade_prisional',$unidade);
            $qtdEstadoCivilNIH->where('unidade_prisional',$unidade);
            $qtdEstadoCivilNIM->where('unidade_prisional',$unidade);

            //opcao_sexual
            $qtdHeteroH->where('unidade_prisional',$unidade);
            $qtdHeteroM->where('unidade_prisional',$unidade);
            $qtdHomoH->where('unidade_prisional',$unidade);
            $qtdHomoM->where('unidade_prisional',$unidade);
            $qtdBiH->where('unidade_prisional',$unidade);
            $qtdBiM->where('unidade_prisional',$unidade);
            $qtdSexualidadeNaoDeclH->where('unidade_prisional',$unidade);
            $qtdSexualidadeNaoDeclM->where('unidade_prisional',$unidade);
            $qtdSexualidadeNIH->where('unidade_prisional',$unidade);
            $qtdSexualidadeNIM->where('unidade_prisional',$unidade);

            //etnia
            $qtdEtniaBrancaH->where('unidade_prisional',$unidade);
            $qtdEtniaBrancaM->where('unidade_prisional',$unidade);
            $qtdEtniaIndígenaH->where('unidade_prisional',$unidade);
            $qtdEtniaIndígenaM->where('unidade_prisional',$unidade);
            $qtdEtniaPretaH->where('unidade_prisional',$unidade);
            $qtdEtniaPretaM->where('unidade_prisional',$unidade);
            $qtdEtniaPardaH->where('unidade_prisional',$unidade);
            $qtdEtniaPardaM->where('unidade_prisional',$unidade);
            $qtdEtniaAmarelaH->where('unidade_prisional',$unidade);
            $qtdEtniaAmarelaM->where('unidade_prisional',$unidade);
            $qtdEtniaNIH->where('unidade_prisional',$unidade);
            $qtdEtniaNIM->where('unidade_prisional',$unidade);

            // escolaridade
            $qtdAnalfabetoH->where('unidade_prisional',$unidade);
            $qtdAnalfabetoM->where('unidade_prisional',$unidade);
            $qtdAlfabetizadoH->where('unidade_prisional',$unidade);
            $qtdAlfabetizadoM->where('unidade_prisional',$unidade);
            $qtdFundIncH->where('unidade_prisional',$unidade);
            $qtdFundIncM->where('unidade_prisional',$unidade);
            $qtdFundCompH->where('unidade_prisional',$unidade);
            $qtdFundCompM->where('unidade_prisional',$unidade);
            $qtdMedioIncompH->where('unidade_prisional',$unidade);
            $qtdMedioIncompM->where('unidade_prisional',$unidade);
            $qtdMedioCompH->where('unidade_prisional',$unidade);
            $qtdMedioCompM->where('unidade_prisional',$unidade);
            $qtdSuperiorIncompH->where('unidade_prisional',$unidade);
            $qtdSuperiorIncompM->where('unidade_prisional',$unidade);
            $qtdSuperiorCompH->where('unidade_prisional',$unidade);
            $qtdSuperiorCompM->where('unidade_prisional',$unidade);
            $qtdPosLatoSensuH->where('unidade_prisional',$unidade);
            $qtdPosLatoSensuM->where('unidade_prisional',$unidade);
            $qtdMestradoH->where('unidade_prisional',$unidade);
            $qtdMestradoM->where('unidade_prisional',$unidade);
            $qtdDoutoradoH->where('unidade_prisional',$unidade);
            $qtdDoutoradoM->where('unidade_prisional',$unidade);
            $qtdPosDoutoradoH->where('unidade_prisional',$unidade);
            $qtdPosDoutoradoM->where('unidade_prisional',$unidade);
            $qtdEscolaridadeNIH->where('unidade_prisional',$unidade);
            $qtdEscolaridadeNIM->where('unidade_prisional',$unidade);

            $qtdNacionalidadeNIH->where('unidade_prisional',$unidade);
            $qtdNacionalidadeNIM->where('unidade_prisional',$unidade);

            $qtdAdventistaH->where('unidade_prisional',$unidade);
            $qtdAdventistaM->where('unidade_prisional',$unidade);
            $qtdBudistaH->where('unidade_prisional',$unidade);
            $qtdBudistaM->where('unidade_prisional',$unidade);
            $qtdCatolicaH->where('unidade_prisional',$unidade);
            $qtdCatolicaM->where('unidade_prisional',$unidade);
            $qtdEspiritaH->where('unidade_prisional',$unidade);
            $qtdEspiritaM->where('unidade_prisional',$unidade);
            $qtdEvangelicaH->where('unidade_prisional',$unidade);
            $qtdEvangelicaM->where('unidade_prisional',$unidade);
            $qtdJudeuH->where('unidade_prisional',$unidade);
            $qtdJudeuM->where('unidade_prisional',$unidade);
            $qtdMormonH->where('unidade_prisional',$unidade);
            $qtdMormonM->where('unidade_prisional',$unidade);
            $qtdMulcumanaH->where('unidade_prisional',$unidade);
            $qtdMulcumanaM->where('unidade_prisional',$unidade);
            $qtdProtestanteH->where('unidade_prisional',$unidade);
            $qtdProtestanteM->where('unidade_prisional',$unidade);
            $qtdAgnosticoH->where('unidade_prisional',$unidade);
            $qtdAgnosticoM->where('unidade_prisional',$unidade);
            $qtdAteuH->where('unidade_prisional',$unidade);
            $qtdAteuM->where('unidade_prisional',$unidade);
            $qtdSemReligiaoH->where('unidade_prisional',$unidade);
            $qtdSemReligiaoM->where('unidade_prisional',$unidade);
            $qtdOutrasH->where('unidade_prisional',$unidade);
            $qtdOutrasM->where('unidade_prisional',$unidade);
            $qtdReligiaoNIH->where('unidade_prisional',$unidade);
            $qtdReligiaoNIM->where('unidade_prisional',$unidade);

            $qtdAlvara->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdAlvara->where('movimentacao_interno.status','Finalizada');
            $qtdAlvara->where('interno.unidade_prisional', $unidade);
            $qtdFuga->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdFuga->where('movimentacao_interno.status','Finalizada');
            $qtdFuga->where('interno.unidade_prisional', $unidade);
            $qtdRecambiamento->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdRecambiamento->where('movimentacao_interno.status','Finalizada');
            $qtdRecambiamento->where('interno.unidade_prisional', $unidade);
            $qtdSaidaJudicial->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdSaidaJudicial->where('movimentacao_interno.status','Finalizada');
            $qtdSaidaJudicial->where('interno.unidade_prisional', $unidade);
            $qtdExtradicao->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdExtradicao->where('movimentacao_interno.status','Finalizada');
            $qtdExtradicao->where('interno.unidade_prisional', $unidade);
            $qtdLivramento->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdLivramento->where('movimentacao_interno.status','Finalizada');
            $qtdLivramento->where('interno.unidade_prisional', $unidade);
            $qtdObito->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdObito->where('movimentacao_interno.status','Finalizada');
            $qtdObito->where('interno.unidade_prisional', $unidade);
            $qtdExtincaopunibilidade->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdExtincaopunibilidade->where('movimentacao_interno.status','Finalizada');
            $qtdExtincaopunibilidade->where('interno.unidade_prisional', $unidade);
        }
        if(!is_null($pavilhao)){
            $qtdUnidadeHA->where('pavilhao', $pavilhao);
            $qtdUnidadeMA->where('pavilhao', $pavilhao);
            $qtdUnidadeTotal->where('pavilhao', $pavilhao);

            $qtdInternosLotSelecionada->where('pavilhao',$pavilhao);

            $qtdRegimeNIH->where('pavilhao',$pavilhao);
            $qtdRegimeNIM->where('pavilhao',$pavilhao);
            $qtdRegimeFechadoH->where('pavilhao',$pavilhao);
            $qtdRegimeFechadoM->where('pavilhao',$pavilhao);
            $qtdRegimeSemiabertoH->where('pavilhao',$pavilhao);
            $qtdRegimeSemiabertoM->where('pavilhao',$pavilhao);
            $qtdRegimeAbertoH->where('pavilhao',$pavilhao);
            $qtdRegimeAbertoM->where('pavilhao',$pavilhao);

            $qtdSentenciadosNIH->where('pavilhao',$pavilhao);
            $qtdSentenciadosNIM->where('pavilhao',$pavilhao);
            $qtdSentenciadosH->where('pavilhao',$pavilhao);
            $qtdSentenciadosM->where('pavilhao',$pavilhao);
            $qtdProvisoriosH->where('pavilhao',$pavilhao);
            $qtdProvisoriosM->where('pavilhao',$pavilhao);

            $qtdSentenciadosMSNIH->where('pavilhao',$pavilhao);
            $qtdSentenciadosMSNIM->where('pavilhao',$pavilhao);
            $qtdSentenciadosMSH->where('pavilhao',$pavilhao);
            $qtdSentenciadosMSM->where('pavilhao',$pavilhao);
            $qtdProvisoriosMSH->where('pavilhao',$pavilhao);
            $qtdProvisoriosMSM->where('pavilhao',$pavilhao);

            $qtdMedidaDeSegurancaH->where('pavilhao',$pavilhao);
            $qtdMedidaDeSegurancaM->where('pavilhao',$pavilhao);

            $qtdHA->where('pavilhao',$pavilhao);
            $qtdMA->where('pavilhao',$pavilhao);

            $internosH->where('pavilhao',$pavilhao);
            $internosM->where('pavilhao',$pavilhao);

            $qtdFaccaoNIH->where('pavilhao',$pavilhao);
            $qtdFaccaoNIM->where('pavilhao',$pavilhao);

            $periculosidadeAltaH->where('pavilhao',$pavilhao);
            $periculosidadeMediaH->where('pavilhao',$pavilhao);
            $periculosidadeBaixaH->where('pavilhao',$pavilhao);
            $periculosidadeNIH->where('pavilhao',$pavilhao);
            $periculosidadeAltaM->where('pavilhao',$pavilhao);
            $periculosidadeMediaM->where('pavilhao',$pavilhao);
            $periculosidadeBaixaM->where('pavilhao',$pavilhao);
            $periculosidadeNIM->where('pavilhao',$pavilhao);

            $qtdLiderancaSimH->where('pavilhao',$pavilhao);
            $qtdLiderancaNaoH->where('pavilhao',$pavilhao);
            $qtdLiderancaNIH->where('pavilhao',$pavilhao);
            $qtdLiderancaSimM->where('pavilhao',$pavilhao);
            $qtdLiderancaNaoM->where('pavilhao',$pavilhao);
            $qtdLiderancaNIM->where('pavilhao',$pavilhao);

            //estado_civil
            $qtdSolteirosH->where('pavilhao',$pavilhao);
            $qtdSolteirosM->where('pavilhao',$pavilhao);
            $qtdCasadosH->where('pavilhao',$pavilhao);
            $qtdCasadosM->where('pavilhao',$pavilhao);
            $qtdUniaoEstavelH->where('pavilhao',$pavilhao);
            $qtdUniaoEstavelM->where('pavilhao',$pavilhao);
            $qtdSeparadosH->where('pavilhao',$pavilhao);
            $qtdSeparadosM->where('pavilhao',$pavilhao);
            $qtdDivorciadosH->where('pavilhao',$pavilhao);
            $qtdDivorciadosM->where('pavilhao',$pavilhao);
            $qtdViuvosH->where('pavilhao',$pavilhao);
            $qtdViuvosM->where('pavilhao',$pavilhao);
            $qtdEstadoCivilNIH->where('pavilhao',$pavilhao);
            $qtdEstadoCivilNIM->where('pavilhao',$pavilhao);

            //opcao_sexual
            $qtdHeteroH->where('pavilhao',$pavilhao);
            $qtdHeteroM->where('pavilhao',$pavilhao);
            $qtdHomoH->where('pavilhao',$pavilhao);
            $qtdHomoM->where('pavilhao',$pavilhao);
            $qtdBiH->where('pavilhao',$pavilhao);
            $qtdBiM->where('pavilhao',$pavilhao);
            $qtdSexualidadeNaoDeclH->where('pavilhao',$pavilhao);
            $qtdSexualidadeNaoDeclM->where('pavilhao',$pavilhao);
            $qtdSexualidadeNIH->where('pavilhao',$pavilhao);
            $qtdSexualidadeNIM->where('pavilhao',$pavilhao);

            //etnia
            $qtdEtniaBrancaH->where('pavilhao',$pavilhao);
            $qtdEtniaBrancaM->where('pavilhao',$pavilhao);
            $qtdEtniaIndígenaH->where('pavilhao',$pavilhao);
            $qtdEtniaIndígenaM->where('pavilhao',$pavilhao);
            $qtdEtniaPretaH->where('pavilhao',$pavilhao);
            $qtdEtniaPretaM->where('pavilhao',$pavilhao);
            $qtdEtniaPardaH->where('pavilhao',$pavilhao);
            $qtdEtniaPardaM->where('pavilhao',$pavilhao);
            $qtdEtniaAmarelaH->where('pavilhao',$pavilhao);
            $qtdEtniaAmarelaM->where('pavilhao',$pavilhao);
            $qtdEtniaNIH->where('pavilhao',$pavilhao);
            $qtdEtniaNIM->where('pavilhao',$pavilhao);

            // escolaridade
            $qtdAnalfabetoH->where('pavilhao',$pavilhao);
            $qtdAnalfabetoM->where('pavilhao',$pavilhao);
            $qtdAlfabetizadoH->where('pavilhao',$pavilhao);
            $qtdAlfabetizadoM->where('pavilhao',$pavilhao);
            $qtdFundIncH->where('pavilhao',$pavilhao);
            $qtdFundIncM->where('pavilhao',$pavilhao);
            $qtdFundCompH->where('pavilhao',$pavilhao);
            $qtdFundCompM->where('pavilhao',$pavilhao);
            $qtdMedioIncompH->where('pavilhao',$pavilhao);
            $qtdMedioIncompM->where('pavilhao',$pavilhao);
            $qtdMedioCompH->where('pavilhao',$pavilhao);
            $qtdMedioCompM->where('pavilhao',$pavilhao);
            $qtdSuperiorIncompH->where('pavilhao',$pavilhao);
            $qtdSuperiorIncompM->where('pavilhao',$pavilhao);
            $qtdSuperiorCompH->where('pavilhao',$pavilhao);
            $qtdSuperiorCompM->where('pavilhao',$pavilhao);
            $qtdPosLatoSensuH->where('pavilhao',$pavilhao);
            $qtdPosLatoSensuM->where('pavilhao',$pavilhao);
            $qtdMestradoH->where('pavilhao',$pavilhao);
            $qtdMestradoM->where('pavilhao',$pavilhao);
            $qtdDoutoradoH->where('pavilhao',$pavilhao);
            $qtdDoutoradoM->where('pavilhao',$pavilhao);
            $qtdPosDoutoradoH->where('pavilhao',$pavilhao);
            $qtdPosDoutoradoM->where('pavilhao',$pavilhao);
            $qtdEscolaridadeNIH->where('pavilhao',$pavilhao);
            $qtdEscolaridadeNIM->where('pavilhao',$pavilhao);

            $qtdNacionalidadeNIH->where('pavilhao',$pavilhao);
            $qtdNacionalidadeNIM->where('pavilhao',$pavilhao);

            $qtdAdventistaH->where('pavilhao',$pavilhao);
            $qtdAdventistaM->where('pavilhao',$pavilhao);
            $qtdBudistaH->where('pavilhao',$pavilhao);
            $qtdBudistaM->where('pavilhao',$pavilhao);
            $qtdCatolicaH->where('pavilhao',$pavilhao);
            $qtdCatolicaM->where('pavilhao',$pavilhao);
            $qtdEspiritaH->where('pavilhao',$pavilhao);
            $qtdEspiritaM->where('pavilhao',$pavilhao);
            $qtdEvangelicaH->where('pavilhao',$pavilhao);
            $qtdEvangelicaM->where('pavilhao',$pavilhao);
            $qtdJudeuH->where('pavilhao',$pavilhao);
            $qtdJudeuM->where('pavilhao',$pavilhao);
            $qtdMormonH->where('pavilhao',$pavilhao);
            $qtdMormonM->where('pavilhao',$pavilhao);
            $qtdMulcumanaH->where('pavilhao',$pavilhao);
            $qtdMulcumanaM->where('pavilhao',$pavilhao);
            $qtdProtestanteH->where('pavilhao',$pavilhao);
            $qtdProtestanteM->where('pavilhao',$pavilhao);
            $qtdAgnosticoH->where('pavilhao',$pavilhao);
            $qtdAgnosticoM->where('pavilhao',$pavilhao);
            $qtdAteuH->where('pavilhao',$pavilhao);
            $qtdAteuM->where('pavilhao',$pavilhao);
            $qtdSemReligiaoH->where('pavilhao',$pavilhao);
            $qtdSemReligiaoM->where('pavilhao',$pavilhao);
            $qtdOutrasH->where('pavilhao',$pavilhao);
            $qtdOutrasM->where('pavilhao',$pavilhao);
            $qtdReligiaoNIH->where('pavilhao',$pavilhao);
            $qtdReligiaoNIM->where('pavilhao',$pavilhao);

            //saida da unidade
            $qtdAlvara->where('interno.pavilhao',$pavilhao);
            $qtdFuga->where('interno.pavilhao',$pavilhao);
            $qtdRecambiamento->where('interno.pavilhao',$pavilhao);
            $qtdSaidaJudicial->where('interno.pavilhao',$pavilhao);
            $qtdExtradicao->where('interno.pavilhao',$pavilhao);
            $qtdLivramento->where('interno.pavilhao',$pavilhao);
            $qtdObito->where('interno.pavilhao',$pavilhao);
            $qtdExtincaopunibilidade->where('interno.pavilhao',$pavilhao);
        }
        if(!is_null($ala)) {
            $qtdUnidadeHA->where('ala', $ala);
            $qtdUnidadeMA->where('ala', $ala);
            $qtdUnidadeTotal->where('ala', $ala);

            $qtdInternosLotSelecionada->where('ala',$ala);

            $qtdRegimeNIH->where('ala',$ala);
            $qtdRegimeNIM->where('ala',$ala);
            $qtdRegimeFechadoH->where('ala',$ala);
            $qtdRegimeFechadoM->where('ala',$ala);
            $qtdRegimeSemiabertoH->where('ala',$ala);
            $qtdRegimeSemiabertoM->where('ala',$ala);
            $qtdRegimeAbertoH->where('ala',$ala);
            $qtdRegimeAbertoM->where('ala',$ala);

            $qtdSentenciadosNIH->where('ala',$ala);
            $qtdSentenciadosNIM->where('ala',$ala);
            $qtdSentenciadosH->where('ala',$ala);
            $qtdSentenciadosM->where('ala',$ala);
            $qtdProvisoriosH->where('ala',$ala);
            $qtdProvisoriosM->where('ala',$ala);

            $qtdSentenciadosMSNIH->where('ala',$ala);
            $qtdSentenciadosMSNIM->where('ala',$ala);
            $qtdSentenciadosMSH->where('ala',$ala);
            $qtdSentenciadosMSM->where('ala',$ala);
            $qtdProvisoriosMSH->where('ala',$ala);
            $qtdProvisoriosMSM->where('ala',$ala);

            $qtdMedidaDeSegurancaH->where('ala',$ala);
            $qtdMedidaDeSegurancaM->where('ala',$ala);

            $qtdHA->where('ala',$ala);
            $qtdMA->where('ala',$ala);

            $internosH->where('ala',$ala);
            $internosM->where('ala',$ala);

            $qtdFaccaoNIH->where('ala',$ala);
            $qtdFaccaoNIM->where('ala',$ala);

            $periculosidadeAltaH->where('ala',$ala);
            $periculosidadeMediaH->where('ala',$ala);
            $periculosidadeBaixaH->where('ala',$ala);
            $periculosidadeNIH->where('ala',$ala);
            $periculosidadeAltaM->where('ala',$ala);
            $periculosidadeMediaM->where('ala',$ala);
            $periculosidadeBaixaM->where('ala',$ala);
            $periculosidadeNIM->where('ala',$ala);

            $qtdLiderancaSimH->where('ala',$ala);
            $qtdLiderancaNaoH->where('ala',$ala);
            $qtdLiderancaNIH->where('ala',$ala);
            $qtdLiderancaSimM->where('ala',$ala);
            $qtdLiderancaNaoM->where('ala',$ala);
            $qtdLiderancaNIM->where('ala',$ala);

            //estado_civil
            $qtdSolteirosH->where('ala',$ala);
            $qtdSolteirosM->where('ala',$ala);
            $qtdCasadosH->where('ala',$ala);
            $qtdCasadosM->where('ala',$ala);
            $qtdUniaoEstavelH->where('ala',$ala);
            $qtdUniaoEstavelM->where('ala',$ala);
            $qtdSeparadosH->where('ala',$ala);
            $qtdSeparadosM->where('ala',$ala);
            $qtdDivorciadosH->where('ala',$ala);
            $qtdDivorciadosM->where('ala',$ala);
            $qtdViuvosH->where('ala',$ala);
            $qtdViuvosM->where('ala',$ala);
            $qtdEstadoCivilNIH->where('ala',$ala);
            $qtdEstadoCivilNIM->where('ala',$ala);

            //opcao_sexual
            $qtdHeteroH->where('ala',$ala);
            $qtdHeteroM->where('ala',$ala);
            $qtdHomoH->where('ala',$ala);
            $qtdHomoM->where('ala',$ala);
            $qtdBiH->where('ala',$ala);
            $qtdBiM->where('ala',$ala);
            $qtdSexualidadeNaoDeclH->where('ala',$ala);
            $qtdSexualidadeNaoDeclM->where('ala',$ala);
            $qtdSexualidadeNIH->where('ala',$ala);
            $qtdSexualidadeNIM->where('ala',$ala);

            //etnia
            $qtdEtniaBrancaH->where('ala',$ala);
            $qtdEtniaBrancaM->where('ala',$ala);
            $qtdEtniaIndígenaH->where('ala',$ala);
            $qtdEtniaIndígenaM->where('ala',$ala);
            $qtdEtniaPretaH->where('ala',$ala);
            $qtdEtniaPretaM->where('ala',$ala);
            $qtdEtniaPardaH->where('ala',$ala);
            $qtdEtniaPardaM->where('ala',$ala);
            $qtdEtniaAmarelaH->where('ala',$ala);
            $qtdEtniaAmarelaM->where('ala',$ala);
            $qtdEtniaNIH->where('ala',$ala);
            $qtdEtniaNIM->where('ala',$ala);

            // escolaridade
            $qtdAnalfabetoH->where('ala',$ala);
            $qtdAnalfabetoM->where('ala',$ala);
            $qtdAlfabetizadoH->where('ala',$ala);
            $qtdAlfabetizadoM->where('ala',$ala);
            $qtdFundIncH->where('ala',$ala);
            $qtdFundIncM->where('ala',$ala);
            $qtdFundCompH->where('ala',$ala);
            $qtdFundCompM->where('ala',$ala);
            $qtdMedioIncompH->where('ala',$ala);
            $qtdMedioIncompM->where('ala',$ala);
            $qtdMedioCompH->where('ala',$ala);
            $qtdMedioCompM->where('ala',$ala);
            $qtdSuperiorIncompH->where('ala',$ala);
            $qtdSuperiorIncompM->where('ala',$ala);
            $qtdSuperiorCompH->where('ala',$ala);
            $qtdSuperiorCompM->where('ala',$ala);
            $qtdPosLatoSensuH->where('ala',$ala);
            $qtdPosLatoSensuM->where('ala',$ala);
            $qtdMestradoH->where('ala',$ala);
            $qtdMestradoM->where('ala',$ala);
            $qtdDoutoradoH->where('ala',$ala);
            $qtdDoutoradoM->where('ala',$ala);
            $qtdPosDoutoradoH->where('ala',$ala);
            $qtdPosDoutoradoM->where('ala',$ala);
            $qtdEscolaridadeNIH->where('ala',$ala);
            $qtdEscolaridadeNIM->where('ala',$ala);

            $qtdNacionalidadeNIH->where('ala',$ala);
            $qtdNacionalidadeNIM->where('ala',$ala);


            $qtdAdventistaH->where('ala',$ala);
            $qtdAdventistaM->where('ala',$ala);
            $qtdBudistaH->where('ala',$ala);
            $qtdBudistaM->where('ala',$ala);
            $qtdCatolicaH->where('ala',$ala);
            $qtdCatolicaM->where('ala',$ala);
            $qtdEspiritaH->where('ala',$ala);
            $qtdEspiritaM->where('ala',$ala);
            $qtdEvangelicaH->where('ala',$ala);
            $qtdEvangelicaM->where('ala',$ala);
            $qtdJudeuH->where('ala',$ala);
            $qtdJudeuM->where('ala',$ala);
            $qtdMormonH->where('ala',$ala);
            $qtdMormonM->where('ala',$ala);
            $qtdMulcumanaH->where('ala',$ala);
            $qtdMulcumanaM->where('ala',$ala);
            $qtdProtestanteH->where('ala',$ala);
            $qtdProtestanteM->where('ala',$ala);
            $qtdAgnosticoH->where('ala',$ala);
            $qtdAgnosticoM->where('ala',$ala);
            $qtdAteuH->where('ala',$ala);
            $qtdAteuM->where('ala',$ala);
            $qtdSemReligiaoH->where('ala',$ala);
            $qtdSemReligiaoM->where('ala',$ala);
            $qtdOutrasH->where('ala',$ala);
            $qtdOutrasM->where('ala',$ala);
            $qtdReligiaoNIH->where('ala',$ala);
            $qtdReligiaoNIM->where('ala',$ala);

            //saida da unidade
            $qtdAlvara->where('interno.ala',$ala);
            $qtdFuga->where('interno.ala',$ala);
            $qtdRecambiamento->where('interno.ala',$ala);
            $qtdSaidaJudicial->where('interno.ala',$ala);
            $qtdExtradicao->where('interno.ala',$ala);
            $qtdLivramento->where('interno.ala',$ala);
            $qtdObito->where('interno.ala',$ala);
            $qtdExtincaopunibilidade->where('interno.ala',$ala);
        }
        if(!is_null($cela)) {
            $qtdUnidadeHA->where('cela', $cela);
            $qtdUnidadeMA->where('cela', $cela);
            $qtdUnidadeTotal->where('cela', $cela);

            $qtdInternosLotSelecionada->where('cela',$cela);

            $qtdRegimeNIH->where('cela',$cela);
            $qtdRegimeNIM->where('cela',$cela);
            $qtdRegimeFechadoH->where('cela',$cela);
            $qtdRegimeFechadoM->where('cela',$cela);
            $qtdRegimeSemiabertoH->where('cela',$cela);
            $qtdRegimeSemiabertoM->where('cela',$cela);
            $qtdRegimeAbertoH->where('cela',$cela);
            $qtdRegimeAbertoM->where('cela',$cela);

            $qtdSentenciadosNIH->where('cela',$cela);
            $qtdSentenciadosNIM->where('cela',$cela);
            $qtdSentenciadosH->where('cela',$cela);
            $qtdSentenciadosM->where('cela',$cela);
            $qtdProvisoriosH->where('cela',$cela);
            $qtdProvisoriosM->where('cela',$cela);

            $qtdSentenciadosMSNIH->where('cela',$cela);
            $qtdSentenciadosMSNIM->where('cela',$cela);
            $qtdSentenciadosMSH->where('cela',$cela);
            $qtdSentenciadosMSM->where('cela',$cela);
            $qtdProvisoriosMSH->where('cela',$cela);
            $qtdProvisoriosMSM->where('cela',$cela);

            $qtdMedidaDeSegurancaH->where('cela',$cela);
            $qtdMedidaDeSegurancaM->where('cela',$cela);

            $qtdHA->where('cela',$cela);
            $qtdMA->where('cela',$cela);

            $internosH->where('cela',$cela);
            $internosM->where('cela',$cela);

            $qtdFaccaoNIH->where('cela',$cela);
            $qtdFaccaoNIM->where('cela',$cela);

            $periculosidadeAltaH->where('cela',$cela);
            $periculosidadeMediaH->where('cela',$cela);
            $periculosidadeBaixaH->where('cela',$cela);
            $periculosidadeNIH->where('cela',$cela);
            $periculosidadeAltaM->where('cela',$cela);
            $periculosidadeMediaM->where('cela',$cela);
            $periculosidadeBaixaM->where('cela',$cela);
            $periculosidadeNIM->where('cela',$cela);

            $qtdLiderancaSimH->where('cela',$cela);
            $qtdLiderancaNaoH->where('cela',$cela);
            $qtdLiderancaNIH->where('cela',$cela);
            $qtdLiderancaSimM->where('cela',$cela);
            $qtdLiderancaNaoM->where('cela',$cela);
            $qtdLiderancaNIM->where('cela',$cela);
            
            //estado_civil
            $qtdSolteirosH->where('cela',$cela);
            $qtdSolteirosM->where('cela',$cela);
            $qtdCasadosH->where('cela',$cela);
            $qtdCasadosM->where('cela',$cela);
            $qtdUniaoEstavelH->where('cela',$cela);
            $qtdUniaoEstavelM->where('cela',$cela);
            $qtdSeparadosH->where('cela',$cela);
            $qtdSeparadosM->where('cela',$cela);
            $qtdDivorciadosH->where('cela',$cela);
            $qtdDivorciadosM->where('cela',$cela);
            $qtdViuvosH->where('cela',$cela);
            $qtdViuvosM->where('cela',$cela);
            $qtdEstadoCivilNIH->where('cela',$cela);
            $qtdEstadoCivilNIM->where('cela',$cela);

            //opcao_sexual
            $qtdHeteroH->where('cela',$cela);
            $qtdHeteroM->where('cela',$cela);
            $qtdHomoH->where('cela',$cela);
            $qtdHomoM->where('cela',$cela);
            $qtdBiH->where('cela',$cela);
            $qtdBiM->where('cela',$cela);
            $qtdSexualidadeNaoDeclH->where('cela',$cela);
            $qtdSexualidadeNaoDeclM->where('cela',$cela);
            $qtdSexualidadeNIH->where('cela',$cela);
            $qtdSexualidadeNIM->where('cela',$cela);

            //etnia
            $qtdEtniaBrancaH->where('cela',$cela);
            $qtdEtniaBrancaM->where('cela',$cela);
            $qtdEtniaIndígenaH->where('cela',$cela);
            $qtdEtniaIndígenaM->where('cela',$cela);
            $qtdEtniaPretaH->where('cela',$cela);
            $qtdEtniaPretaM->where('cela',$cela);
            $qtdEtniaPardaH->where('cela',$cela);
            $qtdEtniaPardaM->where('cela',$cela);
            $qtdEtniaAmarelaH->where('cela',$cela);
            $qtdEtniaAmarelaM->where('cela',$cela);
            $qtdEtniaNIH->where('cela',$cela);
            $qtdEtniaNIM->where('cela',$cela);

            // escolaridade
            $qtdAnalfabetoH->where('cela',$cela);
            $qtdAnalfabetoM->where('cela',$cela);
            $qtdAlfabetizadoH->where('cela',$cela);
            $qtdAlfabetizadoM->where('cela',$cela);
            $qtdFundIncH->where('cela',$cela);
            $qtdFundIncM->where('cela',$cela);
            $qtdFundCompH->where('cela',$cela);
            $qtdFundCompM->where('cela',$cela);
            $qtdMedioIncompH->where('cela',$cela);
            $qtdMedioIncompM->where('cela',$cela);
            $qtdMedioCompH->where('cela',$cela);
            $qtdMedioCompM->where('cela',$cela);
            $qtdSuperiorIncompH->where('cela',$cela);
            $qtdSuperiorIncompM->where('cela',$cela);
            $qtdSuperiorCompH->where('cela',$cela);
            $qtdSuperiorCompM->where('cela',$cela);
            $qtdPosLatoSensuH->where('cela',$cela);
            $qtdPosLatoSensuM->where('cela',$cela);
            $qtdMestradoH->where('cela',$cela);
            $qtdMestradoM->where('cela',$cela);
            $qtdDoutoradoH->where('cela',$cela);
            $qtdDoutoradoM->where('cela',$cela);
            $qtdPosDoutoradoH->where('cela',$cela);
            $qtdPosDoutoradoM->where('cela',$cela);
            $qtdEscolaridadeNIH->where('cela',$cela);
            $qtdEscolaridadeNIM->where('cela',$cela);

            $qtdNacionalidadeNIH->where('cela',$cela);
            $qtdNacionalidadeNIM->where('cela',$cela);

            $qtdAdventistaH->where('cela',$cela);
            $qtdAdventistaM->where('cela',$cela);
            $qtdBudistaH->where('cela',$cela);
            $qtdBudistaM->where('cela',$cela);
            $qtdCatolicaH->where('cela',$cela);
            $qtdCatolicaM->where('cela',$cela);
            $qtdEspiritaH->where('cela',$cela);
            $qtdEspiritaM->where('cela',$cela);
            $qtdEvangelicaH->where('cela',$cela);
            $qtdEvangelicaM->where('cela',$cela);
            $qtdJudeuH->where('cela',$cela);
            $qtdJudeuM->where('cela',$cela);
            $qtdMormonH->where('cela',$cela);
            $qtdMormonM->where('cela',$cela);
            $qtdMulcumanaH->where('cela',$cela);
            $qtdMulcumanaM->where('cela',$cela);
            $qtdProtestanteH->where('cela',$cela);
            $qtdProtestanteM->where('cela',$cela);
            $qtdAgnosticoH->where('cela',$cela);
            $qtdAgnosticoM->where('cela',$cela);
            $qtdAteuH->where('cela',$cela);
            $qtdAteuM->where('cela',$cela);
            $qtdSemReligiaoH->where('cela',$cela);
            $qtdSemReligiaoM->where('cela',$cela);
            $qtdOutrasH->where('cela',$cela);
            $qtdOutrasM->where('cela',$cela);
            $qtdReligiaoNIH->where('cela',$cela);
            $qtdReligiaoNIM->where('cela',$cela);

            //saida da unidade
            $qtdAlvara->where('interno.cela',$cela);
            $qtdFuga->where('interno.cela',$cela);
            $qtdRecambiamento->where('interno.cela',$cela);
            $qtdSaidaJudicial->where('interno.cela',$cela);
            $qtdExtradicao->where('interno.cela',$cela);
            $qtdLivramento->where('interno.cela',$cela);
            $qtdObito->where('interno.cela',$cela);
            $qtdExtincaopunibilidade->where('interno.cela',$cela);
        }

        $internosH=$internosH->get();
        foreach($internosH as $interno){
            if(is_null($interno->data_nascimento)){
                $faixaEtaria_naoInformadaH++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 17){
                $faixaEtaria_zero_a_dezesseteH++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 18 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 24){
                $faixaEtaria_dezoito_a_vinteequatroH++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 25 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 29){
                $faixaEtaria_vinteecinco_a_vinteenoveH++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 30 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 34){
                $faixaEtaria_trinta_a_trintaequatroH++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 35 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 45){
                $faixaEtaria_trintaecinco_a_quarentaecincoH++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 46 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 60){
                $faixaEtaria_quarentaeseis_a_sessentaH++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 61 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 70){
                $faixaEtaria_sessentaeum_a_setentaH++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) > 70){
                $faixaEtaria_maisDeSetentaH++;
            }
        }

        $internosM=$internosM->get();
        foreach($internosM as $interno){
            if(is_null($interno->data_nascimento)){
                $faixaEtaria_naoInformadaM++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 17){
                $faixaEtaria_zero_a_dezesseteM++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 18 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 24){
                $faixaEtaria_dezoito_a_vinteequatroM++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 25 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 29){
                $faixaEtaria_vinteecinco_a_vinteenoveM++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 30 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 34){
                $faixaEtaria_trinta_a_trintaequatroM++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 35 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 45){
                $faixaEtaria_trintaecinco_a_quarentaecincoM++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 46 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 60){
                $faixaEtaria_quarentaeseis_a_sessentaM++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 61 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 70){
                $faixaEtaria_sessentaeum_a_setentaM++;
            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) > 70){
                $faixaEtaria_maisDeSetentaM++;
            }
        }

        $nacionalidades = DB::table('interno')->select(DB::raw('count(*) as qtdd, naturalidade_pais'))
            ->where('status','A')
            ->groupBy('naturalidade_pais')->orderBy('qtdd','DESC')->get();
        foreach($nacionalidades as $key=>$nacionalidade){
            $nacionalidades[$key]->qtdH = DB::table('interno')->where('sexo','M')->where('status','A')->where('naturalidade_pais', $nacionalidade->naturalidade_pais);
            $nacionalidades[$key]->qtdM = DB::table('interno')->where('sexo','F')->where('status','A')->where('naturalidade_pais', $nacionalidade->naturalidade_pais);

            if(!is_null($unidade)) {
                $nacionalidades[$key]->qtdH->where('unidade_prisional',$unidade);
                $nacionalidades[$key]->qtdM->where('unidade_prisional',$unidade);
            }
            if(!is_null($pavilhao)) {
                $nacionalidades[$key]->qtdH->where('pavilhao',$pavilhao);
                $nacionalidades[$key]->qtdM->where('pavilhao',$pavilhao);
            }
            if(!is_null($ala)) {
                $nacionalidades[$key]->qtdH->where('ala',$ala);
                $nacionalidades[$key]->qtdM->where('ala',$ala);
            }
            if(!is_null($cela)) {
                $nacionalidades[$key]->qtdH->where('cela',$cela);
                $nacionalidades[$key]->qtdM->where('cela',$cela);
            }

            $nacionalidades[$key]->qtdH = $nacionalidades[$key]->qtdH->count();
            $nacionalidades[$key]->qtdM = $nacionalidades[$key]->qtdM->count();
            $nacionalidades[$key]->qtd = $nacionalidades[$key]->qtdH + $nacionalidades[$key]->qtdM;
        }

        $qtdNacionalidadeNIH = $qtdNacionalidadeNIH->count();
        $qtdNacionalidadeNIM = $qtdNacionalidadeNIM->count();

        $faccoes = DB::table('faccao')->select('id','nome','sigla')->get();
        foreach($faccoes as $key=>$faccao){
            $faccoes[$key]->qtdH = DB::table('interno')->where('sexo','M')->where('status','A')->where('faccao', $faccao->id);
            $faccoes[$key]->qtdM = DB::table('interno')->where('sexo','F')->where('status','A')->where('faccao', $faccao->id);

            if(!is_null($unidade)) {
                $faccoes[$key]->qtdH->where('unidade_prisional',$unidade);
                $faccoes[$key]->qtdM->where('unidade_prisional',$unidade);
            }
            if(!is_null($pavilhao)) {
                $faccoes[$key]->qtdH->where('pavilhao',$pavilhao);
                $faccoes[$key]->qtdM->where('pavilhao',$pavilhao);
            }
            if(!is_null($ala)) {
                $faccoes[$key]->qtdH->where('ala',$ala);
                $faccoes[$key]->qtdM->where('ala',$ala);
            }
            if(!is_null($cela)) {
                $faccoes[$key]->qtdH->where('cela',$cela);
                $faccoes[$key]->qtdM->where('cela',$cela);
            }

            $faccoes[$key]->qtdH = $faccoes[$key]->qtdH->count();
            $faccoes[$key]->qtdM = $faccoes[$key]->qtdM->count();
            $faccoes[$key]->qtd = $faccoes[$key]->qtdH + $faccoes[$key]->qtdM;
        }

        $faccoes = $faccoes->sortBy('qtd')->reverse();

        $qtdUnidadeHA = $qtdUnidadeHA->get();
        $qtdUnidadeMA = $qtdUnidadeMA->get();
        $qtdUnidadeTotal = $qtdUnidadeTotal->get();

        $qtdFaccaoNIH = $qtdFaccaoNIH->count();
        $qtdFaccaoNIM = $qtdFaccaoNIM->count();

        $qtdInternosLotSelecionada=$qtdInternosLotSelecionada->count();

        $qtdRegimeNIH=$qtdRegimeNIH->count();
        $qtdRegimeNIM=$qtdRegimeNIM->count();
        $qtdRegimeFechadoH=$qtdRegimeFechadoH->count();
        $qtdRegimeFechadoM=$qtdRegimeFechadoM->count();
        $qtdRegimeSemiabertoH=$qtdRegimeSemiabertoH->count();
        $qtdRegimeSemiabertoM=$qtdRegimeSemiabertoM->count();
        $qtdRegimeAbertoH=$qtdRegimeAbertoH->count();
        $qtdRegimeAbertoM=$qtdRegimeAbertoM->count();

        $qtdSentenciadosNIH=$qtdSentenciadosNIH->count();
        $qtdSentenciadosNIM=$qtdSentenciadosNIM->count();
        $qtdSentenciadosH=$qtdSentenciadosH->count();
        $qtdSentenciadosM=$qtdSentenciadosM->count();
        $qtdProvisoriosH=$qtdProvisoriosH->count();
        $qtdProvisoriosM=$qtdProvisoriosM->count();

        $qtdSentenciadosMSNIH=$qtdSentenciadosMSNIH->count();
        $qtdSentenciadosMSNIM=$qtdSentenciadosMSNIM->count();
        $qtdSentenciadosMSH=$qtdSentenciadosMSH->count();
        $qtdSentenciadosMSM=$qtdSentenciadosMSM->count();
        $qtdProvisoriosMSH=$qtdProvisoriosMSH->count();
        $qtdProvisoriosMSM=$qtdProvisoriosMSM->count();

        $qtdMedidaDeSegurancaH=$qtdMedidaDeSegurancaH->count();
        $qtdMedidaDeSegurancaM=$qtdMedidaDeSegurancaM->count();

        $qtdHA=$qtdHA->count();
        $qtdMA=$qtdMA->count();

        $periculosidadeAltaH=$periculosidadeAltaH->count();
        $periculosidadeMediaH=$periculosidadeMediaH->count();
        $periculosidadeBaixaH=$periculosidadeBaixaH->count();
        $periculosidadeNIH=$periculosidadeNIH->count();
        $periculosidadeAltaM=$periculosidadeAltaM->count();
        $periculosidadeMediaM=$periculosidadeMediaM->count();
        $periculosidadeBaixaM=$periculosidadeBaixaM->count();
        $periculosidadeNIM=$periculosidadeNIM->count();

        $qtdLiderancaSimH=$qtdLiderancaSimH->count();
        $qtdLiderancaNaoH=$qtdLiderancaNaoH->count();
        $qtdLiderancaNIH=$qtdLiderancaNIH->count();
        $qtdLiderancaSimM=$qtdLiderancaSimM->count();
        $qtdLiderancaNaoM=$qtdLiderancaNaoM->count();
        $qtdLiderancaNIM=$qtdLiderancaNIM->count();

        //estado_civil
        $qtdSolteirosH=$qtdSolteirosH->count();
        $qtdSolteirosM=$qtdSolteirosM->count();
        $qtdCasadosH=$qtdCasadosH->count();
        $qtdCasadosM=$qtdCasadosM->count();
        $qtdUniaoEstavelH=$qtdUniaoEstavelH->count();
        $qtdUniaoEstavelM=$qtdUniaoEstavelM->count();
        $qtdSeparadosH=$qtdSeparadosH->count();
        $qtdSeparadosM=$qtdSeparadosM->count();
        $qtdDivorciadosH=$qtdDivorciadosH->count();
        $qtdDivorciadosM=$qtdDivorciadosM->count();
        $qtdViuvosH=$qtdViuvosH->count();
        $qtdViuvosM=$qtdViuvosM->count();
        $qtdEstadoCivilNIH=$qtdEstadoCivilNIH->count();
        $qtdEstadoCivilNIM=$qtdEstadoCivilNIM->count();

        //opcao_sexual
        $qtdHeteroH = $qtdHeteroH->count();
        $qtdHeteroM = $qtdHeteroM->count();
        $qtdHomoH = $qtdHomoH->count();
        $qtdHomoM = $qtdHomoM->count();
        $qtdBiH = $qtdBiH->count();
        $qtdBiM = $qtdBiM->count();
        $qtdSexualidadeNaoDeclH = $qtdSexualidadeNaoDeclH->count();
        $qtdSexualidadeNaoDeclM = $qtdSexualidadeNaoDeclM->count();
        $qtdSexualidadeNIH = $qtdSexualidadeNIH->count();
        $qtdSexualidadeNIM = $qtdSexualidadeNIM->count();

        //etnia
        $qtdEtniaBrancaH=$qtdEtniaBrancaH->count();
        $qtdEtniaBrancaM=$qtdEtniaBrancaM->count();
        $qtdEtniaIndígenaH=$qtdEtniaIndígenaH->count();
        $qtdEtniaIndígenaM=$qtdEtniaIndígenaM->count();
        $qtdEtniaPretaH=$qtdEtniaPretaH->count();
        $qtdEtniaPretaM=$qtdEtniaPretaM->count();
        $qtdEtniaPardaH=$qtdEtniaPardaH->count();
        $qtdEtniaPardaM=$qtdEtniaPardaM->count();
        $qtdEtniaAmarelaH=$qtdEtniaAmarelaH->count();
        $qtdEtniaAmarelaM=$qtdEtniaAmarelaM->count();
        $qtdEtniaNIH=$qtdEtniaNIH->count();
        $qtdEtniaNIM=$qtdEtniaNIM->count();

        // escolaridade
        $qtdAnalfabetoH=$qtdAnalfabetoH->count();
        $qtdAnalfabetoM=$qtdAnalfabetoM->count();
        $qtdAlfabetizadoH=$qtdAlfabetizadoH->count();
        $qtdAlfabetizadoM=$qtdAlfabetizadoM->count();
        $qtdFundIncH=$qtdFundIncH->count();
        $qtdFundIncM=$qtdFundIncM->count();
        $qtdFundCompH=$qtdFundCompH->count();
        $qtdFundCompM=$qtdFundCompM->count();
        $qtdMedioIncompH=$qtdMedioIncompH->count();
        $qtdMedioIncompM=$qtdMedioIncompM->count();
        $qtdMedioCompH=$qtdMedioCompH->count();
        $qtdMedioCompM=$qtdMedioCompM->count();
        $qtdSuperiorIncompH=$qtdSuperiorIncompH->count();
        $qtdSuperiorIncompM=$qtdSuperiorIncompM->count();
        $qtdSuperiorCompH=$qtdSuperiorCompH->count();
        $qtdSuperiorCompM=$qtdSuperiorCompM->count();
        $qtdPosLatoSensuH=$qtdPosLatoSensuH->count();
        $qtdPosLatoSensuM=$qtdPosLatoSensuM->count();
        $qtdMestradoH=$qtdMestradoH->count();
        $qtdMestradoM=$qtdMestradoM->count();
        $qtdDoutoradoH=$qtdDoutoradoH->count();
        $qtdDoutoradoM=$qtdDoutoradoM->count();
        $qtdPosDoutoradoH=$qtdPosDoutoradoH->count();
        $qtdPosDoutoradoM=$qtdPosDoutoradoM->count();
        $qtdEscolaridadeNIH=$qtdEscolaridadeNIH->count();
        $qtdEscolaridadeNIM=$qtdEscolaridadeNIM->count();

        $qtdAdventistaH=$qtdAdventistaH->count();
        $qtdAdventistaM=$qtdAdventistaM->count();
        $qtdBudistaH=$qtdBudistaH->count();
        $qtdBudistaM=$qtdBudistaM->count();
        $qtdCatolicaH=$qtdCatolicaH->count();
        $qtdCatolicaM=$qtdCatolicaM->count();
        $qtdEspiritaH=$qtdEspiritaH->count();
        $qtdEspiritaM=$qtdEspiritaM->count();
        $qtdEvangelicaH=$qtdEvangelicaH->count();
        $qtdEvangelicaM=$qtdEvangelicaM->count();
        $qtdJudeuH=$qtdJudeuH->count();
        $qtdJudeuM=$qtdJudeuM->count();
        $qtdMormonH=$qtdMormonH->count();
        $qtdMormonM=$qtdMormonM->count();
        $qtdMulcumanaH=$qtdMulcumanaH->count();
        $qtdMulcumanaM=$qtdMulcumanaM->count();
        $qtdProtestanteH=$qtdProtestanteH->count();
        $qtdProtestanteM=$qtdProtestanteM->count();
        $qtdAgnosticoH=$qtdAgnosticoH->count();
        $qtdAgnosticoM=$qtdAgnosticoM->count();
        $qtdAteuH=$qtdAteuH->count();
        $qtdAteuM=$qtdAteuM->count();
        $qtdSemReligiaoH=$qtdSemReligiaoH->count();
        $qtdSemReligiaoM=$qtdSemReligiaoM->count();
        $qtdOutrasH=$qtdOutrasH->count();
        $qtdOutrasM=$qtdOutrasM->count();
        $qtdReligiaoNIH=$qtdReligiaoNIH->count();
        $qtdReligiaoNIM=$qtdReligiaoNIM->count();

        //saida da unidade
        $qtdAlvara = $qtdAlvara->count();
        $qtdFuga = $qtdFuga->count();
        $qtdRecambiamento = $qtdRecambiamento->count();
        $qtdSaidaJudicial = $qtdSaidaJudicial->count();
        $qtdExtradicao = $qtdExtradicao->count();
        $qtdLivramento = $qtdLivramento->count();
        $qtdObito = $qtdObito->count();
        $qtdExtincaopunibilidade = $qtdExtincaopunibilidade->count();

        $dataHora = Tdata::TtimeStampHojeBrTxt();

        $qntReingresso = $this->getReigressoUnidade(TData::TdataMysql($request->get('data')));

        //$this->idadeVisita();

        return view('estatisticas.show.estatisticas', compact('unidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome','faccoes','qtdFaccaoNIH','qtdFaccaoNIM',
            'qtdInternosLotSelecionada','qtdRegimeFechadoH', 'qtdRegimeSemiabertoH', 'qtdRegimeAbertoH','qtdRegimeNIH', 'qtdRegimeNIM', 'dataHora',
            'qtdRegimeFechadoM', 'qtdRegimeSemiabertoM', 'qtdRegimeAbertoM','qtdMedidaDeSegurancaH', 'qtdMedidaDeSegurancaM',
            'qtdSentenciadosH','qtdProvisoriosH', 'qtdSentenciadosNIH', 'qtdSentenciadosM','qtdProvisoriosM', 'qtdSentenciadosNIM', 'qtdHA', 'qtdMA', 'qtdSexoNI',
            'faixaEtaria_naoInformada','faixaEtaria_zero_a_dezessete','faixaEtaria_dezoito_a_vinteequatro','faixaEtaria_vinteecinco_a_vinteenove',
            'faixaEtaria_trinta_a_trintaequatro','faixaEtaria_trintaecinco_a_quarentaecinco','faixaEtaria_quarentaeseis_a_sessenta',
            'faixaEtaria_sessentaeum_a_setenta','faixaEtaria_maisDeSetenta',
            'faixaEtaria_naoInformadaH','faixaEtaria_zero_a_dezesseteH','faixaEtaria_dezoito_a_vinteequatroH','faixaEtaria_vinteecinco_a_vinteenoveH',
            'faixaEtaria_trinta_a_trintaequatroH','faixaEtaria_trintaecinco_a_quarentaecincoH','faixaEtaria_quarentaeseis_a_sessentaH',
            'faixaEtaria_sessentaeum_a_setentaH','faixaEtaria_maisDeSetentaH',
            'faixaEtaria_naoInformadaM','faixaEtaria_zero_a_dezesseteM','faixaEtaria_dezoito_a_vinteequatroM','faixaEtaria_vinteecinco_a_vinteenoveM',
            'faixaEtaria_trinta_a_trintaequatroM','faixaEtaria_trintaecinco_a_quarentaecincoM','faixaEtaria_quarentaeseis_a_sessentaM',
            'faixaEtaria_sessentaeum_a_setentaM','faixaEtaria_maisDeSetentaM','periculosidadeAltaH','periculosidadeMediaH','periculosidadeBaixaH','periculosidadeNIH',
            'periculosidadeAltaM','periculosidadeMediaM','periculosidadeBaixaM','periculosidadeNIM','qtdLiderancaSimH','qtdLiderancaNaoH','qtdLiderancaNIH',
            'qtdLiderancaSimM','qtdLiderancaNaoM','qtdLiderancaNIM', 'nacionalidades', 'qtdNacionalidadeNIH', 'qtdNacionalidadeNIM', 'qtdUnidadeHA', 'qtdUnidadeMA', 'qtdUnidadeTotal',

            'qtdSentenciadosMSNIH',
            'qtdSentenciadosMSNIM',
            'qtdSentenciadosMSH',
            'qtdSentenciadosMSM',
            'qtdProvisoriosMSH',
            'qtdProvisoriosMSM',

            //estado_civil
            'qtdSolteirosH','qtdSolteirosM','qtdCasadosH', 'qtdCasadosM', 'qtdUniaoEstavelH', 'qtdUniaoEstavelM', 'qtdSeparadosH', 'qtdSeparadosM',
            'qtdDivorciadosH', 'qtdDivorciadosM', 'qtdViuvosH', 'qtdViuvosM', 'qtdEstadoCivilNIH', 'qtdEstadoCivilNIM',

            //etnia
            'qtdEtniaBrancaH', 'qtdEtniaBrancaM', 'qtdEtniaIndígenaH', 'qtdEtniaIndígenaM', 'qtdEtniaPretaH', 'qtdEtniaPretaM', 'qtdEtniaPardaH',
            'qtdEtniaPardaM', 'qtdEtniaAmarelaH', 'qtdEtniaAmarelaM', 'qtdEtniaNIH', 'qtdEtniaNIM',

            //opcao_sexual
            'qtdHeteroH', 'qtdHeteroM', 'qtdHomoH', 'qtdHomoM', 'qtdBiH', 'qtdBiM', 'qtdSexualidadeNaoDeclH', 'qtdSexualidadeNaoDeclM',
            'qtdSexualidadeNIH', 'qtdSexualidadeNIM',

            // escolaridade
            'qtdAnalfabetoH', 'qtdAnalfabetoM', 'qtdAlfabetizadoH', 'qtdAlfabetizadoM', 'qtdFundIncH', 'qtdFundIncM', 'qtdFundCompH', 'qtdFundCompM',
            'qtdMedioIncompH', 'qtdMedioIncompM', 'qtdMedioCompH', 'qtdMedioCompM', 'qtdSuperiorIncompH', 'qtdSuperiorIncompM', 'qtdSuperiorCompH',
            'qtdSuperiorCompM', 'qtdPosLatoSensuH', 'qtdPosLatoSensuM', 'qtdMestradoH', 'qtdMestradoM', 'qtdDoutoradoH', 'qtdDoutoradoM',
            'qtdPosDoutoradoH', 'qtdPosDoutoradoM', 'qtdEscolaridadeNIH', 'qtdEscolaridadeNIM',

            //religião
            'qtdAdventistaH', 'qtdAdventistaM', 'qtdBudistaH', 'qtdBudistaM', 'qtdCatolicaH', 'qtdCatolicaM', 'qtdEspiritaH', 'qtdEspiritaM', 'qtdEvangelicaH',
            'qtdEvangelicaM', 'qtdJudeuH', 'qtdJudeuM', 'qtdMormonH', 'qtdMormonM', 'qtdMulcumanaH', 'qtdMulcumanaM', 'qtdProtestanteH', 'qtdProtestanteM',
            'qtdAgnosticoH', 'qtdAgnosticoM', 'qtdAteuH', 'qtdAteuM', 'qtdSemReligiaoH', 'qtdSemReligiaoM', 'qtdOutrasH', 'qtdOutrasM', 'qtdReligiaoNIH', 'qtdReligiaoNIM',

            //saida da unidade
            'qtdAlvara', 'qtdFuga', 'qtdRecambiamento', 'qtdSaidaJudicial', 'qtdExtradicao', 'qtdLivramento', 'qtdObito', 'qtdExtincaopunibilidade', 'qntReingresso', 'detalhesUnidades'

        ));
    }

    public static function getTotalInternosBysigla($sigla){

        $internos =  DB::table('interno')
            ->leftJoin('unidade', 'unidade.id', 'interno.unidade_prisional')
            ->where('unidade.sigla', $sigla)
            ->where('interno.status', 'A')
            ->groupBy('unidade.sigla')
            ->select('unidade.sigla', DB::raw('COUNT(*) as soma'))
            ->get()->first();

        return $internos;
    }

    public function extrairBySexo($dados, $campo){
        $extraidos = Array();
        foreach ($dados as $dado){
            array_push($extraidos, [$dado->$campo, $dado->sexo]);
        }
        return $extraidos;
    }

    public function idadeVisita($filtros, $valoresFiltros){
        $visitas = DB::table('visita')->where('visita.status', 'A');

        if($filtros != []){
            $visitas->leftJoin('visita_interno', 'visita_interno.fk_visita', 'visita.id');
            $visitas->leftJoin('interno', 'visita_interno.fk_interno', 'interno.id');
        }
        foreach($filtros as $x => $filtro){
            $visitas->where($filtro, $valoresFiltros[$x]);
        }

        $visitasLiderancaSim = clone $visitas;
        if($filtros == []) {
            $visitasLiderancaSim->leftJoin('visita_interno', 'visita_interno.fk_visita', 'visita.id');
            $visitasLiderancaSim->leftJoin('interno', 'visita_interno.fk_interno', 'interno.id');
        }


        $visitasLiderancaSim = $visitasLiderancaSim->select('interno.lideranca', 'visita.sexo',DB::raw('COUNT( * ) as qntLideranca'))->groupBy('interno.lideranca','visita.sexo')->orderBy('interno.lideranca','visita.sexo')->get();
   
        $visitasLiderancaSimF = count($visitasLiderancaSim) != 0 ? $visitasLiderancaSim[0] : -1;
        $visitasLiderancaSimM = count($visitasLiderancaSim) != 0 ? $visitasLiderancaSim[1] : -1;
        $visitasLiderancaNaoF = count($visitasLiderancaSim) != 0 ? $visitasLiderancaSim[2] : -1;
        $visitasLiderancaNaoM = count($visitasLiderancaSim) != 0 ? $visitasLiderancaSim[3] : -1;
        $visitasLiderancaNaoInformadoF = count($visitasLiderancaSim) != 0 ? $visitasLiderancaSim[4] : -1;
        $visitasLiderancaNaoInformadoM = count($visitasLiderancaSim) != 0 ? $visitasLiderancaSim[5] : -1;



        $visitas->select('visita.*');
        $visitas = $visitas->get();

        $dados = $this->extrairBySexo($visitas, 'data_nascimento');
        $arrayIdadesM = array_fill(0, 9, 0);
        $arrayIdadesF = array_fill(0, 9, 0);
        $qntM = 0;
        $qntF = 0;
        foreach($dados as $dado){
            if($dado[1] == 'M'){
                $qntM++;
            } else {
                $qntF++;
            }
            if(is_null($dado[0])){
                if($dado[1] == 'M'){
                    $arrayIdadesM[0]++;
                } else {
                    $arrayIdadesF[0]++;
                }
            }elseif(Tdata::getIdade(Tdata::TdataBR($dado[0])) <= 17){
                if($dado[1] == 'M'){
                    $arrayIdadesM[1]++;
                } else {
                    $arrayIdadesF[1]++;
                }
            }elseif(Tdata::getIdade(Tdata::TdataBR($dado[0])) >= 18 && Tdata::getIdade(Tdata::TdataBR($dado[0])) <= 24){
                if($dado[1] == 'M'){
                    $arrayIdadesM[2]++;
                } else {
                    $arrayIdadesF[2]++;
                }
            }elseif(Tdata::getIdade(Tdata::TdataBR($dado[0])) >= 25 && Tdata::getIdade(Tdata::TdataBR($dado[0])) <= 29){
                if($dado[1] == 'M'){
                    $arrayIdadesM[3]++;
                } else {
                    $arrayIdadesF[3]++;
                }
            }elseif(Tdata::getIdade(Tdata::TdataBR($dado[0])) >= 30 && Tdata::getIdade(Tdata::TdataBR($dado[0])) <= 34){
                if($dado[1] == 'M'){
                    $arrayIdadesM[4]++;
                } else {
                    $arrayIdadesF[4]++;
                }
            }elseif(Tdata::getIdade(Tdata::TdataBR($dado[0])) >= 35 && Tdata::getIdade(Tdata::TdataBR($dado[0])) <= 45){
                if($dado[1] == 'M'){
                    $arrayIdadesM[5]++;
                } else {
                    $arrayIdadesF[5]++;
                }
            }elseif(Tdata::getIdade(Tdata::TdataBR($dado[0])) >= 46 && Tdata::getIdade(Tdata::TdataBR($dado[0])) <= 60){
                if($dado[1] == 'M'){
                    $arrayIdadesM[6]++;
                } else {
                    $arrayIdadesF[6]++;
                }
            }elseif(Tdata::getIdade(Tdata::TdataBR($dado[0])) >= 61 && Tdata::getIdade(Tdata::TdataBR($dado[0])) <= 70){
                if($dado[1] == 'M'){
                    $arrayIdadesM[7]++;
                } else {
                    $arrayIdadesF[7]++;
                }
            }elseif(Tdata::getIdade(Tdata::TdataBR($dado[0])) > 70){
                if($dado[1] == 'M'){
                    $arrayIdadesM[8]++;
                } else {
                    $arrayIdadesF[8]++;
                }
            }
        }
        return [$arrayIdadesM, $arrayIdadesF, $qntM, $qntF, $visitasLiderancaSimF, $visitasLiderancaSimM, $visitasLiderancaNaoF, $visitasLiderancaNaoM, $visitasLiderancaNaoInformadoF, $visitasLiderancaNaoInformadoM];
    }

    public function showVisita(Request $request){


        $filtros = [];
        $valoresFiltros = [];
        $unidade = $request->get('unidade_prisional');
        if(!is_null($unidade)){
            $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            array_push($filtros, 'unidade_prisional');
            array_push($valoresFiltros, $unidade);
        }
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if(!is_null($pavilhao)){
            $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
            array_push($filtros, 'pavilhao');
            array_push($valoresFiltros, $pavilhao);
        }
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if(!is_null($ala)){
            $alaNome = UnidadesController::getNomeAla($request->get('ala'));
            array_push($filtros, 'ala');
            array_push($valoresFiltros, $ala);
        }
        else $alaNome = null;
        $cela = $request->get('cela');
        if(!is_null($cela)){
            $celaNome = UnidadesController::getNomeCela($request->get('cela'));
            array_push($filtros, 'cela');
            array_push($valoresFiltros, $cela);
        }
        else $celaNome = null;

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        $dataHora = Tdata::TtimeStampHojeBrTxt();

        $resultado = $this->idadeVisita($filtros, $valoresFiltros);

        $idadesM = $resultado[0];
        $idadesF = $resultado[1];
        $qntM = $resultado[2];
        $qntF = $resultado[3];
        try{
            $visitasLiderancaSimF = $resultado[4]->qntLideranca;
        } catch (\Exception $e){
            $visitasLiderancaSimF = 0;
        }

        try{
            $visitasLiderancaSimM = $resultado[5]->qntLideranca;
        } catch (\Exception $e){
            $visitasLiderancaSimM = 0;
        }    

        try{
            $visitasLiderancaNaoF = $resultado[6]->qntLideranca;
        } catch (\Exception $e){
            $visitasLiderancaNaoF = 0;
        }

        try{
            $visitasLiderancaNaoM = $resultado[7]->qntLideranca;
        } catch (\Exception $e){
            $visitasLiderancaNaoM = 0;
        }

        try{
            $visitasLiderancaNaoInformadoF = $resultado[8]->qntLideranca;
        } catch (\Exception $e){
            $visitasLiderancaNaoInformadoF = 0;
        }

        try{
            $visitasLiderancaNaoInformadoM = $resultado[9]->qntLideranca;
        } catch (\Exception $e){
            $visitasLiderancaNaoInformadoM = 0;
        }

        return view('estatisticas.show.estatisticasVisita', compact('unidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome', 'idadesF', 'idadesM', 'dataHora', 'qntM', 'qntF',
            'visitasLiderancaNaoInformadoF', 'visitasLiderancaNaoInformadoM', 'visitasLiderancaSimF', 'visitasLiderancaSimM', 'visitasLiderancaNaoF', 'visitasLiderancaNaoM'));
    }

    public function showAdvogado(Request $request){
        $unidade = $request->get('unidade_prisional');

        $data_atendimento = $request->get('data_atendimento');
        $atendimento_advogado = DB::table('advogado_atendimento');
        $atendimento_advogado->leftJoin('unidade', 'unidade.id', 'advogado_atendimento.unidade');
        if($data_atendimento == null){
            $atendimento_advogado->where('advogado_atendimento.data_atendimento', '>=', Tdata::TdataHojeMysql());
        } else $atendimento_advogado->where('advogado_atendimento.data_atendimento', '>=', Tdata::TdataMysql($data_atendimento));

        if(!is_null($unidade)) {
            $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            $atendimento_advogado->leftJoin('interno', 'interno.id', 'advogado_atendimento.fk_interno');
            $atendimento_advogado->leftJoin('pavilhao', 'interno.pavilhao', 'pavilhao.id');
            $atendimento_advogado->leftJoin('ala', 'interno.ala', 'ala.id');
            $atendimento_advogado->leftJoin('cela', 'interno.cela', 'cela.id');
            $atendimento_advogado->where('interno.status', 'A');
            $atendimento_advogado->where('interno.unidade_prisional', $unidade);
            $atendimento_advogado->where('unidade.id', $unidade);
        }
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if(!is_null($pavilhao)){
            $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
            $atendimento_advogado->where('interno.pavilhao',$pavilhao);
        }
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if(!is_null($ala)){
            $alaNome = UnidadesController::getNomeAla($request->get('ala'));
            $atendimento_advogado->where('interno.ala', $ala);
        }
        else $alaNome = null;
        $cela = $request->get('cela');
        if(!is_null($cela)){
            $celaNome = UnidadesController::getNomeCela($request->get('cela'));
            $atendimento_advogado->where('interno.cela', $cela);
        }
        else $celaNome = null;

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        $dataHora = Tdata::TtimeStampHojeBrTxt();



        if(!is_null($ala)) {
            $atendimento_advogado->groupBy('cela.nome','cela.id');
            $atendimento_advogado->orderBy('cela.nome');
            $atendimento_advogado = $atendimento_advogado->select(DB::raw('(cela.nome) as sigla'), 'cela.id', DB::raw('count(advogado_atendimento.id) as qnt'))->get();
            $nome_campo = "Cela";
        } else
        if(!is_null($pavilhao)){
            $atendimento_advogado->groupBy('ala.nome','ala.id');
            $atendimento_advogado->orderBy('ala.nome');
            $atendimento_advogado = $atendimento_advogado->select(DB::raw('(ala.nome) as sigla'), 'ala.id',DB::raw('count(advogado_atendimento.id) as qnt'))->get();
            $nome_campo = "Ala";
        } else
        if(!is_null($unidade)) {
            $atendimento_advogado->groupBy('pavilhao.nome','pavilhao.id');
            $atendimento_advogado->orderBy('pavilhao.nome');
            $atendimento_advogado = $atendimento_advogado->select(DB::raw('(pavilhao.nome) as sigla'), 'pavilhao.id', DB::raw('count(advogado_atendimento.id) as qnt'))->get();
            $nome_campo = "Pavilhão";
        } else{
            $atendimento_advogado->groupBy('unidade.sigla','unidade.id');
            $atendimento_advogado->orderBy('unidade.sigla');
            $atendimento_advogado = $atendimento_advogado->select('unidade.sigla', 'unidade.id',DB::raw('count(advogado_atendimento.id) as qnt'))->get();
            $nome_campo = "Unidade";
        }

        $soma = 0; //SERÁ UTILIZADA NA VIEW PARA O CALCULO SER DINÂMICO, APROVEITANDO O PROCESSAMENTO DO FOREACH


        return view('estatisticas.show.estatisticasAdvogado', compact('unidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome','dataHora', 'atendimento_advogado', 'nome_campo', 'soma', 'data_atendimento'));
    }

    public function showDadosEmBranco(Request $request){
        $unidades = DB::table('unidade')->where('status', 1 )->get();
        return view('estatisticas.show.dados-em-branco', compact('unidades'));
    }

    public function showServidor(Request $request){
        $unidade = $request->get('unidade_prisional');
        if(!is_null($unidade)){
            $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        }
        else $unidadeSigla = null;

        $servidores = [];
        $servidoresBySexo = [];
        $internos = [];

        if(is_null($unidade)) {
            $unidades = DB::table('unidade')
                ->where('status', 1)
                ->get();
            foreach ($unidades as $unidade) {
                array_push($servidores, [$unidade->sigla, FacadeController::getTotalServidoresLotacao($unidade->sigla)]);
                array_push($servidoresBySexo, [FacadeController::getTotalServidoresLotacaoBySexo($unidade->sigla, 'Masculino'),FacadeController::getTotalServidoresLotacaoBySexo($unidade->sigla, 'Feminino')]);
                array_push($internos, self::getTotalInternosBysigla($unidade->sigla));
            }
        } else {
            array_push($servidores, [$unidadeSigla, FacadeController::getTotalServidoresLotacao($unidadeSigla)]);
            array_push($servidoresBySexo, [FacadeController::getTotalServidoresLotacaoBySexo($unidadeSigla, 'Masculino'),FacadeController::getTotalServidoresLotacaoBySexo($unidadeSigla, 'Feminino')]);
            array_push($internos, self::getTotalInternosBysigla($unidadeSigla));
        }


        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        $dataHora = Tdata::TtimeStampHojeBrTxt();

        return view('estatisticas.show.estatisticasServidor', compact('unidades', 'unidadeSigla','dataHora','servidores', 'servidoresBySexo','internos'));
    }

    /**
     * @param $data
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getReigressoUnidade($data){

        $datai = mktime(00,00,00,02,19,2019);

        if($data != null){


            if (preg_match('/^[0-9]{4}[-][0-9]{1,2}[-][0-9]{1,2}$/', $data)) {

                if ($data < date("Y-m-d", $datai)) {

                    return redirect()->back()->withInput()->with('alert-danger', 'Não foi possível atender a solicitação. Digite uma data a partir de 19/02/2019.');

                } else {

                    $qntReigressoAlvara = DB::table('log')
                        ->where('log.texto', 'reativação de interno')
                        ->where('log.created_at', '>=', $data)->count();
                }
            }else{

                return redirect()->back()->withInput()->with('alert-danger', 'Data inválida. Digite uma data válida. Ex.: 00/00/0000');

            }
        }else {

                $qntReigressoAlvara = DB::table('log')
                    ->where('log.texto', 'reativação de interno')->count();

        }

        $qntReigresso = $qntReigressoAlvara;


        //$qntReigressoEntrada = DB::table('movimentacao')
            //->leftJoin('movimentacao_interno', 'movimentacao.id', 'movimentacao_interno.fk_movimentacao')
            //->leftJoin('interno', 'interno.id', 'movimentacao_interno.fk_interno')
            //->where('movimentacao.tipo', 'entradaunidadeprisional')
            //->where('movimentacao_interno.status', 'Finalizada');



        //$qntReigressoAlvara = $qntReigressoAlvara->groupBy('interno.id')
            //->orderBy('interno.id')
           // ->select('interno.id', DB::raw('MAX(movimentacao.data) as data'))
           // ->get();



        //$qntReigressoEntrada =$qntReigressoEntrada->groupBy('interno.id')
            //->orderBy('interno.id')
            //->select('interno.id', DB::raw('MAX(movimentacao.data) as data'))
            //->get();



        //foreach($qntReigressoAlvara as $qntAlvara){
           // foreach($qntReigressoEntrada as $qntEntrada){
                //if($qntEntrada->id > $qntAlvara->id){
                    //break;
               // }
                //if($qntEntrada->id == $qntAlvara->id){
                   // if($qntEntrada->data > $qntAlvara->data){
                       // $qntReigresso = $qntReigresso + 1;
                        //break;
                   // }
               // }
           // }
       // }

        return $qntReigresso;

    }

    public static function getInternosUnidadeData($unidadePrisional, $data) {
        
            if(is_null(Session::get('dataEstatistica')))
            {
                $internosDados = InternoController::getInternosUnidadeData($unidadePrisional, Tdata::TdataMysql($data));
                Session::put('dataEstatistica', array('data' => $data, 'unidade' => $unidadePrisional, 'internos' => $internosDados));

            }else{
                
                if((Session::get('dataEstatistica')['data'] != $data) || (Session::get('dataEstatistica')['unidade'] != $unidadePrisional))
                {
                    Session::forget('dataEstatistica');
                    $internosDados = InternoController::getInternosUnidadeData($unidadePrisional, Tdata::TdataMysql($data));
                    Session::put('dataEstatistica', array('data' => $data, 'unidade' => $unidadePrisional, 'internos' => $internosDados));

                }
            }
            
            $sessionInternosDataEstatistica = Session::get('dataEstatistica');
            
            return $sessionInternosDataEstatistica;
                    
    }
    
    public function getQntVisitaAtiva(Request $request){

        $unidade = $request->get('unidade');
        $pavilhao = $request->get('pavilha');
        $ala = $request->get('ala');
        $cela = $request->get('cela');

        $visitas = DB::table('visita_interno')
            ->leftJoin('visita', 'visita.id', 'visita_interno.fk_visita')
            ->leftJoin('interno', 'interno.id', 'visita_interno.fk_interno')
            ->leftJoin('unidade', 'unidade.id', 'interno.unidade_prisional')
            ->where('interno.status', 'A')
            ->where('visita.status', 'A')
            ->groupBy('unidade.sigla')
            ->groupBy('interno.sexo')
            ->orderBy('unidade.sigla');

        if($unidade != null && $unidade > 0){
            $visitas = $visitas
                ->where('interno.unidade_prisional', $unidade);
            if($pavilhao != null && $pavilhao > 0){
                $visitas = $visitas->where('interno.pavilhao', $pavilhao);
                if($ala != null && $ala > 0){
                    $visitas = $visitas->where('interno.ala', $ala);
                    if($cela != null && $cela > 0){
                        $visitas = $visitas->where('interno.cela', $cela);
                    }
                }
            }
        }


        $visitas = $visitas->select('unidade.sigla as unidade', 'interno.sexo as sexo', DB::raw('COUNT(DISTINCT (interno.id)) AS qnt'));

        return $visitas->get();


    }

    public function getUnidadesSenteRegimeMediSegu(Request $request, $unidade, $pavilhao, $ala, $cela){


        $query = DB::table('interno')
            ->leftJoin('unidade', 'unidade.id', 'interno.unidade_prisional')
            ->where('interno.status', 'A')
            ->where('unidade.status', '1')
            ->groupBy('unidade.sigla');

        if($unidade != null && $unidade > 0){
            $query = $query
                ->where('interno.unidade_prisional', $unidade);
            if($pavilhao != null && $pavilhao > 0){
                $query = $query->where('interno.pavilhao', $pavilhao);
                if($ala != null && $ala > 0){
                    $query = $query->where('interno.ala', $ala);
                    if($cela != null && $cela > 0){
                        $query = $query->where('interno.cela', $cela);
                    }
                }
            }
        }


        $query = $query->select('unidade.sigla AS sigla',
            DB::raw('COUNT(CASE WHEN interno.sentenciado = \'S\' AND interno.regime=\'ABERTO\' AND interno.medida_seguranca=\'N\'  THEN 1 END) AS aberto'),
            DB::raw('COUNT(CASE WHEN interno.sentenciado = \'S\' AND interno.regime=\'SEMIABERTO\' AND interno.medida_seguranca=\'N\' THEN 1 END) AS semiaberto'),
            DB::raw('COUNT(CASE WHEN interno.sentenciado = \'S\' AND interno.regime=\'FECHADO\' AND interno.medida_seguranca=\'N\' THEN 1 END) AS fechado'),
            DB::raw('COUNT(CASE WHEN interno.sentenciado=\'N\'  AND interno.regime IS NULL AND interno.medida_seguranca = \'N\'  THEN 1 END) AS provisorio'),
            DB::raw('COUNT(CASE WHEN interno.sentenciado=\'S\' AND interno.regime IS NOT NULL  AND interno.medida_seguranca = \'N\' THEN 1 END) AS sentenciado'),
            DB::raw('COUNT(CASE WHEN interno.medida_seguranca=\'S\' AND interno.regime IS NULL AND interno.sentenciado = \'S\' THEN 1 END) AS medida_seg'),
            DB::raw('COUNT(CASE WHEN interno.sentenciado=\'N\'  AND interno.regime IS NULL  AND interno.medida_seguranca = \'S\'  THEN 1 END) AS medida_seg_provisorio'),
            DB::raw('COUNT(CASE WHEN (interno.sentenciado=\'S\'  AND interno.regime IS NOT NULL  AND interno.medida_seguranca = \'S\')
	            OR (interno.sentenciado=\'N\'  AND interno.regime IS NOT NULL )
	            OR (interno.sentenciado=\'S\'  AND interno.regime IS NULL AND interno.medida_seguranca = \'N\')
	            OR (interno.medida_seguranca=\'S\'  AND interno.regime IS NOT NULL )
	            OR (interno.medida_seguranca IS NULL  OR interno.sentenciado IS NULL ) THEN 1 END) AS inconsistente'),
            DB::raw('COUNT(CASE WHEN interno.nome IS NOT NULL     THEN 1 END) AS total'));

        return $query->get();


    }

    public static function arredondarEscala($valor, $escala){
        $novoValor = strval(round($valor/$escala));
        $contemZeros = true;
        if(strlen ($novoValor) == 1){
            return 10;
        }
        for($i = 1; $i < strlen ($novoValor); $i++){
            if($novoValor[$i] != 0){
                $contemZeros = false;
                break;
            }
        }
        if(!$contemZeros){
            return (((int) $novoValor[0]) + 1) * ((int) '1'.str_repeat('0',strlen($novoValor)-1));
        }
        return $novoValor;
    }


    public function internoPdf(Request $request){
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        return view('estatisticas.impressao.interno', compact('unidades'));
    }

    public function internoPdfGerar(Request $request){

        ini_set('max_execution_time', 3000);
        ini_set('memory_limit','256M');

        $unidade = $request->get('unidade_prisional');
        if(!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if(!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if(!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if(!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;


        $internosunidade = $request->get('internosunidade');
        $regimeunidade = $request->get('regimeunidade');
        $sexo = $request->get('sexo');
        $crimes = $request->get('crimes');
        $regimeprisional = $request->get('regimeprisional');
        $sentprov_sem_ms = $request->get('sentprov_sem_ms');
        $sentprov_em_ms = $request->get('sentprov_em_ms');
        $sentprov_geral = $request->get('sentprov_geral');
        $medidaseg = $request->get('medidaseg');
        $faixaetaria = $request->get('faixaetaria');
        $nacionalidade_filtro = $request->get('nacionalidade');
        $estadocivil = $request->get('estadocivil');
        $escolaridade = $request->get('escolaridade');
        $corpele = $request->get('corpele');
        $orientacaosexual = $request->get('orientacaosexual');
        $saidaunidade = $request->get('saidaunidade');
        $religiao = $request->get('religiao');
        $setic = $request->get('setic');

        /*
         * Processamento de FILTROS ****************************************
        */

        if($internosunidade == '') $internosunidade = false; else $internosunidade = true;
        if($regimeunidade == '')$regimeunidade = false; else $regimeunidade = true;
        if($sexo == '') $sexo = false; else $sexo = true;
        if($regimeprisional == '') $regimeprisional = false; else $regimeprisional = true;
        if($sentprov_sem_ms == '') $sentprov_sem_ms = false; else $sentprov_sem_ms = true;
        if($sentprov_em_ms == '') $sentprov_em_ms = false; else $sentprov_em_ms = true;
        if($sentprov_geral == '') $sentprov_geral= false; else $sentprov_geral = true;
        if($medidaseg == '') $medidaseg = false; else $medidaseg = true;
        if($faixaetaria == '') $faixaetaria = false; else $faixaetaria = true;
        if($nacionalidade_filtro == '') $nacionalidade_filtro = false; else $nacionalidade_filtro = true;
        if($estadocivil == '') $estadocivil = false; else $estadocivil = true;
        if($escolaridade == '') $escolaridade = false; else $escolaridade = true;
        if($corpele == '') $corpele = false; else $corpele = true;
        if($orientacaosexual == '') $orientacaosexual = false; else $orientacaosexual = true;
        if($saidaunidade == '') $saidaunidade = false; else $saidaunidade = true;
        if($religiao == '') $religiao = false; else $religiao = true;
        if($setic == '') $setic = false; else $setic = true;


        /*
         * FIM do Processamento de FILTROS ****************************************
        */

        if($regimeunidade) {
            $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);
        } else {
            $detalhesUnidades = [];
        }


        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        $qtdInternosLotSelecionada = DB::table('interno')->where('status','A');

        $qtdUnidadeHA = DB::table('interno')
            ->leftJoin('unidade','unidade.id','interno.unidade_prisional')
            ->where('sexo', 'M')->where('interno.status','A')
            ->select(DB::raw('unidade.sigla, count(*) as qtd'))
            ->groupBy('unidade.sigla')->orderBy('qtd','DESC');

        $qtdUnidadeMA = DB::table('interno')
            ->leftJoin('unidade','unidade.id','interno.unidade_prisional')
            ->where('sexo', 'F')->where('interno.status','A')
            ->select(DB::raw('unidade.sigla, count(*) as qtd'))
            ->groupBy('unidade.sigla')->orderBy('qtd','DESC');

        $qtdUnidadeTotal  = DB::table('interno')
            ->leftJoin('unidade','unidade.id','interno.unidade_prisional')
            ->select(DB::raw('unidade.sigla, count(*) as qtd'))
            ->where('interno.status','A')->groupBy('unidade.sigla')->orderBy('qtd','DESC');

        $qtdRegimeNIH = DB::table('interno')->where('regime', null)->where('sentenciado','S')->where('sexo','M')->where('medida_seguranca','=','N')->where('status','A');
        $qtdRegimeNIM = DB::table('interno')->where('regime', null)->where('sentenciado','S')->where('sexo','F')->where('medida_seguranca','=','N')->where('status','A');
        $qtdRegimeFechadoH = DB::table('interno')->where('sexo','M')->where('regime', 'FECHADO')->where('status','A');
        $qtdRegimeFechadoM = DB::table('interno')->where('sexo','F')->where('regime', 'FECHADO')->where('status','A');
        $qtdRegimeSemiabertoH = DB::table('interno')->where('sexo','M')->where('regime', 'SEMIABERTO')->where('status','A');
        $qtdRegimeSemiabertoM = DB::table('interno')->where('sexo','F')->where('regime', 'SEMIABERTO')->where('status','A');
        $qtdRegimeAbertoH = DB::table('interno')->where('sexo','M')->where('regime', 'ABERTO')->where('status','A');
        $qtdRegimeAbertoM = DB::table('interno')->where('sexo','F')->where('regime', 'ABERTO')->where('status','A');

        $qtdSentenciadosNIH = DB::table('interno')->where('sexo','M')->where('sentenciado', null)->where('medida_seguranca','N')->where('status','A');
        $qtdSentenciadosNIM = DB::table('interno')->where('sexo','F')->where('sentenciado', null)->where('medida_seguranca','N')->where('status','A');
        $qtdSentenciadosH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'S')->where('medida_seguranca','N')->where('status','A');
        $qtdSentenciadosM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'S')->where('medida_seguranca','N')->where('status','A');
        $qtdProvisoriosH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'N')->where('medida_seguranca','N')->where('status','A');
        $qtdProvisoriosM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'N')->where('medida_seguranca','N')->where('status','A');

        $qtdSentenciadosMSNIH = DB::table('interno')->where('sexo','M')->where('sentenciado', null)->where('medida_seguranca','S')->where('status','A');
        $qtdSentenciadosMSNIM = DB::table('interno')->where('sexo','F')->where('sentenciado', null)->where('medida_seguranca','S')->where('status','A');
        $qtdSentenciadosMSH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'S')->where('medida_seguranca','S')->where('status','A');
        $qtdSentenciadosMSM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'S')->where('medida_seguranca','S')->where('status','A');
        $qtdProvisoriosMSH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'N')->where('medida_seguranca','S')->where('status','A');
        $qtdProvisoriosMSM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'N')->where('medida_seguranca','S')->where('status','A');

        $qtdMedidaDeSegurancaH = DB::table('interno')->where('sexo','M')->where('medida_seguranca', 'S')->where('status','A');
        $qtdMedidaDeSegurancaM = DB::table('interno')->where('sexo','F')->where('medida_seguranca', 'S')->where('status','A');

        $qtdHA = DB::table('interno')->where('sexo', 'M')->where('status','A');
        $qtdMA = DB::table('interno')->where('sexo', 'F')->where('status','A');

        $qtdSexoNI = 0;
        $faixaEtaria_trinta_a_trintaequatro = 0;
        $faixaEtaria_naoInformada = 0;
        $faixaEtaria_zero_a_dezessete = 0;
        $faixaEtaria_dezoito_a_vinteequatro = 0;
        $faixaEtaria_vinteecinco_a_vinteenove = 0;
        $faixaEtaria_trintaecinco_a_quarentaecinco = 0;
        $faixaEtaria_quarentaeseis_a_sessenta = 0;
        $faixaEtaria_sessentaeum_a_setenta = 0;
        $faixaEtaria_maisDeSetenta = 0;
        
        $faixaEtaria_naoInformadaH = 0;
        $faixaEtaria_zero_a_dezesseteH = 0;
        $faixaEtaria_dezoito_a_vinteequatroH = 0;
        $faixaEtaria_vinteecinco_a_vinteenoveH = 0;
        $faixaEtaria_trinta_a_trintaequatroH = 0;
        $faixaEtaria_trintaecinco_a_quarentaecincoH = 0;
        $faixaEtaria_quarentaeseis_a_sessentaH = 0;
        $faixaEtaria_sessentaeum_a_setentaH = 0;
        $faixaEtaria_maisDeSetentaH = 0;

        $faixaEtaria_naoInformadaM = 0;
        $faixaEtaria_zero_a_dezesseteM = 0;
        $faixaEtaria_dezoito_a_vinteequatroM = 0;
        $faixaEtaria_vinteecinco_a_vinteenoveM = 0;
        $faixaEtaria_trinta_a_trintaequatroM = 0;
        $faixaEtaria_trintaecinco_a_quarentaecincoM = 0;
        $faixaEtaria_quarentaeseis_a_sessentaM = 0;
        $faixaEtaria_sessentaeum_a_setentaM = 0;
        $faixaEtaria_maisDeSetentaM = 0;
        $grupos = null;
        $internos = null;

        $internosH = DB::table('interno')->select('data_nascimento')->where('sexo','M')->where('status','A');
        $internosM = DB::table('interno')->select('data_nascimento')->where('sexo','F')->where('status','A');

        $qtdFaccaoNIH = DB::table('interno')->where('status','A')->where('sexo','M')->where('faccao', null);
        $qtdFaccaoNIM = DB::table('interno')->where('status','A')->where('sexo','F')->where('faccao', null);

        //periculosidade
        $periculosidadeAltaH = DB::table('interno')->where('periculosidade','Alta')->where('sexo','M')->where('status','A');
        $periculosidadeMediaH = DB::table('interno')->where('periculosidade','Média')->where('sexo','M')->where('status','A');
        $periculosidadeBaixaH = DB::table('interno')->where('periculosidade','Baixa')->where('sexo','M')->where('status','A');
        $periculosidadeNIH = DB::table('interno')->where('periculosidade',null)->where('sexo','M')->where('status','A');
        $periculosidadeAltaM = DB::table('interno')->where('periculosidade','Alta')->where('sexo','F')->where('status','A');
        $periculosidadeMediaM = DB::table('interno')->where('periculosidade','Média')->where('sexo','F')->where('status','A');
        $periculosidadeBaixaM = DB::table('interno')->where('periculosidade','Baixa')->where('sexo','F')->where('status','A');
        $periculosidadeNIM = DB::table('interno')->where('periculosidade',null)->where('sexo','F')->where('status','A');

        //liderança
        $qtdLiderancaSimH = DB::table('interno')->where('lideranca','S')->where('sexo','M')->where('status','A');
        $qtdLiderancaNaoH = DB::table('interno')->where('lideranca','N')->where('sexo','M')->where('status','A');
        $qtdLiderancaNIH = DB::table('interno')->where('lideranca',null)->where('sexo','M')->where('status','A');
        $qtdLiderancaSimM = DB::table('interno')->where('lideranca','S')->where('sexo','F')->where('status','A');
        $qtdLiderancaNaoM = DB::table('interno')->where('lideranca','N')->where('sexo','F')->where('status','A');
        $qtdLiderancaNIM = DB::table('interno')->where('lideranca',null)->where('sexo','F')->where('status','A');

        // estado_civil
        if($estadocivil){
            $qtdSolteirosH = DB::table('interno')->where('estado_civil','solteiro')->where('sexo','M')->where('status','A');
            $qtdSolteirosM = DB::table('interno')->where('estado_civil','solteiro')->where('sexo','F')->where('status','A');
            $qtdCasadosH = DB::table('interno')->where('estado_civil','casado')->where('sexo','M')->where('status','A');
            $qtdCasadosM = DB::table('interno')->where('estado_civil','casado')->where('sexo','F')->where('status','A');
            $qtdUniaoEstavelH = DB::table('interno')->where('estado_civil','uniao estavel')->where('sexo','M')->where('status','A');
            $qtdUniaoEstavelM = DB::table('interno')->where('estado_civil','uniao estavel')->where('sexo','F')->where('status','A');
            $qtdSeparadosH = DB::table('interno')->where('estado_civil','separado')->where('sexo','M')->where('status','A');
            $qtdSeparadosM = DB::table('interno')->where('estado_civil','separado')->where('sexo','F')->where('status','A');
            $qtdDivorciadosH = DB::table('interno')->where('estado_civil','divorciado')->where('sexo','M')->where('status','A');
            $qtdDivorciadosM = DB::table('interno')->where('estado_civil','divorciado')->where('sexo','F')->where('status','A');
            $qtdViuvosH = DB::table('interno')->where('estado_civil','viuvo')->where('sexo','M')->where('status','A');
            $qtdViuvosM = DB::table('interno')->where('estado_civil','viuvo')->where('sexo','F')->where('status','A');
            $qtdEstadoCivilNIH = DB::table('interno')->where('estado_civil',null)->where('sexo','M')->where('status','A');
            $qtdEstadoCivilNIM = DB::table('interno')->where('estado_civil',null)->where('sexo','F')->where('status','A');
        }

        // opcao_sexual
        $qtdHeteroH = DB::table('interno')->where('opcao_sexual','HETEROSSEXUAL')->where('sexo','M')->where('status','A');
        $qtdHeteroM = DB::table('interno')->where('opcao_sexual','HETEROSSEXUAL')->where('sexo','F')->where('status','A');
        $qtdHomoH = DB::table('interno')->where('opcao_sexual','HOMOSSEXUAL')->where('sexo','M')->where('status','A');
        $qtdHomoM = DB::table('interno')->where('opcao_sexual','HOMOSSEXUAL')->where('sexo','F')->where('status','A');
        $qtdBiH = DB::table('interno')->where('opcao_sexual','BISSEXUAL')->where('sexo','M')->where('status','A');
        $qtdBiM = DB::table('interno')->where('opcao_sexual','BISSEXUAL')->where('sexo','F')->where('status','A');
        $qtdSexualidadeNaoDeclH = DB::table('interno')->where('opcao_sexual','nao declarado')->where('sexo','M')->where('status','A');
        $qtdSexualidadeNaoDeclM = DB::table('interno')->where('opcao_sexual','nao declarado')->where('sexo','F')->where('status','A');
        $qtdSexualidadeNIH = DB::table('interno')->where('opcao_sexual',null)->where('sexo','M')->where('status','A');
        $qtdSexualidadeNIM = DB::table('interno')->where('opcao_sexual',null)->where('sexo','F')->where('status','A');

        // etnia
        $qtdEtniaBrancaH = DB::table('interno')->where('etnia','Branca')->where('sexo','M')->where('status','A');
        $qtdEtniaBrancaM = DB::table('interno')->where('etnia','Branca')->where('sexo','F')->where('status','A');
        $qtdEtniaIndígenaH = DB::table('interno')->where('etnia','Indígena')->where('sexo','M')->where('status','A');
        $qtdEtniaIndígenaM = DB::table('interno')->where('etnia','Indígena')->where('sexo','F')->where('status','A');
        $qtdEtniaPretaH = DB::table('interno')->where('etnia','Preta')->where('sexo','M')->where('status','A');
        $qtdEtniaPretaM = DB::table('interno')->where('etnia','Preta')->where('sexo','F')->where('status','A');
        $qtdEtniaPardaH = DB::table('interno')->where('etnia','Parda')->where('sexo','M')->where('status','A');
        $qtdEtniaPardaM = DB::table('interno')->where('etnia','Parda')->where('sexo','F')->where('status','A');
        $qtdEtniaAmarelaH = DB::table('interno')->where('etnia','Amarela')->where('sexo','M')->where('status','A');
        $qtdEtniaAmarelaM = DB::table('interno')->where('etnia','Amarela')->where('sexo','F')->where('status','A');
        $qtdEtniaNIH = DB::table('interno')->where('etnia',null)->where('sexo','M')->where('status','A');
        $qtdEtniaNIM = DB::table('interno')->where('etnia',null)->where('sexo','F')->where('status','A');

        // escolaridade
        if($escolaridade) {
            $qtdAnalfabetoH = DB::table('interno')->where('escolaridade', 'Analfabeto')->where('sexo', 'M')->where('status', 'A');
            $qtdAnalfabetoM = DB::table('interno')->where('escolaridade', 'Analfabeto')->where('sexo', 'F')->where('status', 'A');
            $qtdAlfabetizadoH = DB::table('interno')->where('escolaridade', 'Alfabetizado')->where('sexo', 'M')->where('status', 'A');
            $qtdAlfabetizadoM = DB::table('interno')->where('escolaridade', 'Alfabetizado')->where('sexo', 'F')->where('status', 'A');
            $qtdFundIncH = DB::table('interno')->where('escolaridade', 'Ensino Fundamental Incompleto')->where('sexo', 'M')->where('status', 'A');
            $qtdFundIncM = DB::table('interno')->where('escolaridade', 'Ensino Fundamental Incompleto')->where('sexo', 'F')->where('status', 'A');
            $qtdFundCompH = DB::table('interno')->where('escolaridade', 'Ensino Fundamental Completo')->where('sexo', 'M')->where('status', 'A');
            $qtdFundCompM = DB::table('interno')->where('escolaridade', 'Ensino Fundamental Completo')->where('sexo', 'F')->where('status', 'A');
            $qtdMedioIncompH = DB::table('interno')->where('escolaridade', 'Ensino Médio Incompleto')->where('sexo', 'M')->where('status', 'A');
            $qtdMedioIncompM = DB::table('interno')->where('escolaridade', 'Ensino Médio Incompleto')->where('sexo', 'F')->where('status', 'A');
            $qtdMedioCompH = DB::table('interno')->where('escolaridade', 'Ensino Médio Completo')->where('sexo', 'M')->where('status', 'A');
            $qtdMedioCompM = DB::table('interno')->where('escolaridade', 'Ensino Médio Completo')->where('sexo', 'F')->where('status', 'A');
            $qtdSuperiorIncompH = DB::table('interno')->where('escolaridade', 'Ensino Superior Incompleto')->where('sexo', 'M')->where('status', 'A');
            $qtdSuperiorIncompM = DB::table('interno')->where('escolaridade', 'Ensino Superior Incompleto')->where('sexo', 'F')->where('status', 'A');
            $qtdSuperiorCompH = DB::table('interno')->where('escolaridade', 'Ensino Superior Completo')->where('sexo', 'M')->where('status', 'A');
            $qtdSuperiorCompM = DB::table('interno')->where('escolaridade', 'Ensino Superior Completo')->where('sexo', 'F')->where('status', 'A');
            $qtdPosLatoSensuH = DB::table('interno')->where('escolaridade', 'Pós-graduação lato sensu')->where('sexo', 'M')->where('status', 'A');
            $qtdPosLatoSensuM = DB::table('interno')->where('escolaridade', 'Pós-graduação lato sensu')->where('sexo', 'F')->where('status', 'A');
            $qtdMestradoH = DB::table('interno')->where('escolaridade', 'Mestrado')->where('sexo', 'M')->where('status', 'A');
            $qtdMestradoM = DB::table('interno')->where('escolaridade', 'Mestrado')->where('sexo', 'F')->where('status', 'A');
            $qtdDoutoradoH = DB::table('interno')->where('escolaridade', 'Doutorado')->where('sexo', 'M')->where('status', 'A');
            $qtdDoutoradoM = DB::table('interno')->where('escolaridade', 'Doutorado')->where('sexo', 'F')->where('status', 'A');
            $qtdPosDoutoradoH = DB::table('interno')->where('escolaridade', 'Pós-Doutorado')->where('sexo', 'M')->where('status', 'A');
            $qtdPosDoutoradoM = DB::table('interno')->where('escolaridade', 'Pós-Doutorado')->where('sexo', 'F')->where('status', 'A');
            $qtdEscolaridadeNIH = DB::table('interno')->where('escolaridade', null)->where('sexo', 'M')->where('status', 'A');
            $qtdEscolaridadeNIM = DB::table('interno')->where('escolaridade', null)->where('sexo', 'F')->where('status', 'A');
        }

        $qtdNacionalidadeNIH = DB::table('interno')->where('naturalidade_pais',null)->where('sexo','M')->where('status','A');
        $qtdNacionalidadeNIM = DB::table('interno')->where('naturalidade_pais',null)->where('sexo','F')->where('status','A');

        //religião
        if($religiao) {
            $qtdAdventistaH = DB::table('interno')->where('religiao', 'Adventista')->where('sexo', 'M')->where('status', 'A');
            $qtdAdventistaM = DB::table('interno')->where('religiao', 'Adventista')->where('sexo', 'F')->where('status', 'A');
            $qtdBudistaH = DB::table('interno')->where('religiao', 'Budista')->where('sexo', 'M')->where('status', 'A');
            $qtdBudistaM = DB::table('interno')->where('religiao', 'Budista')->where('sexo', 'F')->where('status', 'A');
            $qtdCatolicaH = DB::table('interno')->where('religiao', 'Católica')->where('sexo', 'M')->where('status', 'A');
            $qtdCatolicaM = DB::table('interno')->where('religiao', 'Católica')->where('sexo', 'F')->where('status', 'A');
            $qtdEspiritaH = DB::table('interno')->where('religiao', 'Espírita')->where('sexo', 'M')->where('status', 'A');
            $qtdEspiritaM = DB::table('interno')->where('religiao', 'Espírita')->where('sexo', 'F')->where('status', 'A');
            $qtdEvangelicaH = DB::table('interno')->where('religiao', 'Evangélica')->where('sexo', 'M')->where('status', 'A');
            $qtdEvangelicaM = DB::table('interno')->where('religiao', 'Evangélica')->where('sexo', 'F')->where('status', 'A');
            $qtdJudeuH = DB::table('interno')->where('religiao', 'Judeu')->where('sexo', 'M')->where('status', 'A');
            $qtdJudeuM = DB::table('interno')->where('religiao', 'Judeu')->where('sexo', 'F')->where('status', 'A');
            $qtdMormonH = DB::table('interno')->where('religiao', 'Mórmon')->where('sexo', 'M')->where('status', 'A');
            $qtdMormonM = DB::table('interno')->where('religiao', 'Mórmon')->where('sexo', 'F')->where('status', 'A');
            $qtdMulcumanaH = DB::table('interno')->where('religiao', 'Muçulmana')->where('sexo', 'M')->where('status', 'A');
            $qtdMulcumanaM = DB::table('interno')->where('religiao', 'Muçulmana')->where('sexo', 'F')->where('status', 'A');
            $qtdProtestanteH = DB::table('interno')->where('religiao', 'Protestante')->where('sexo', 'M')->where('status', 'A');
            $qtdProtestanteM = DB::table('interno')->where('religiao', 'Protestante')->where('sexo', 'F')->where('status', 'A');
            $qtdAgnosticoH = DB::table('interno')->where('religiao', 'Agnóstico')->where('sexo', 'M')->where('status', 'A');
            $qtdAgnosticoM = DB::table('interno')->where('religiao', 'Agnóstico')->where('sexo', 'F')->where('status', 'A');
            $qtdAteuH = DB::table('interno')->where('religiao', 'Ateu')->where('sexo', 'M')->where('status', 'A');
            $qtdAteuM = DB::table('interno')->where('religiao', 'Ateu')->where('sexo', 'F')->where('status', 'A');
            $qtdSemReligiaoH = DB::table('interno')->where('religiao', 'Sem Religião')->where('sexo', 'M')->where('status', 'A');
            $qtdSemReligiaoM = DB::table('interno')->where('religiao', 'Sem Religião')->where('sexo', 'F')->where('status', 'A');
            $qtdOutrasH = DB::table('interno')->where('religiao', 'Outras')->where('sexo', 'M')->where('status', 'A');
            $qtdOutrasM = DB::table('interno')->where('religiao', 'Outras')->where('sexo', 'F')->where('status', 'A');
            $qtdReligiaoNIH = DB::table('interno')->where('religiao', null)->where('sexo', 'M')->where('status', 'A');
            $qtdReligiaoNIM = DB::table('interno')->where('religiao', null)->where('sexo', 'F')->where('status', 'A');
        }


        //Saída da Unidade Prisional
        $qtdAlvara = DB::table('movimentacao')->where('tipo','alvara');
        $qtdFuga = DB::table('movimentacao')->where('tipo','fuga');
        $qtdRecambiamento = DB::table('movimentacao')->where('tipo','recambiamento');
        $qtdSaidaJudicial = DB::table('movimentacao')->where('tipo','saidajudicial');
        $qtdExtradicao = DB::table('movimentacao')->where('tipo','extradicao');
        $qtdLivramento = DB::table('movimentacao')->where('tipo','livramentocondicional');
        $qtdObito = DB::table('movimentacao')->where('tipo','obito');
        $qtdExtincaopunibilidade = DB::table('movimentacao')->where('tipo','extincaopunibilidade');

        $qtdAlvara->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdFuga->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdRecambiamento->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdSaidaJudicial->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdExtradicao->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdLivramento->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdObito->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
        $qtdExtincaopunibilidade->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');


        if(!is_null($unidade)) {
            $qtdUnidadeHA->where('unidade_prisional', $unidade);
            $qtdUnidadeMA->where('unidade_prisional', $unidade);
            $qtdUnidadeTotal->where('unidade_prisional', $unidade);

            $qtdInternosLotSelecionada->where('unidade_prisional', $unidade);

            $qtdRegimeNIH->where('unidade_prisional', $unidade);
            $qtdRegimeNIM->where('unidade_prisional', $unidade);
            $qtdRegimeFechadoH->where('unidade_prisional', $unidade);
            $qtdRegimeFechadoM->where('unidade_prisional', $unidade);
            $qtdRegimeSemiabertoH->where('unidade_prisional', $unidade);
            $qtdRegimeSemiabertoM->where('unidade_prisional', $unidade);
            $qtdRegimeAbertoH->where('unidade_prisional', $unidade);
            $qtdRegimeAbertoM->where('unidade_prisional', $unidade);

            $qtdSentenciadosNIH->where('unidade_prisional', $unidade);
            $qtdSentenciadosNIM->where('unidade_prisional', $unidade);
            $qtdSentenciadosH->where('unidade_prisional', $unidade);
            $qtdSentenciadosM->where('unidade_prisional', $unidade);
            $qtdProvisoriosH->where('unidade_prisional', $unidade);
            $qtdProvisoriosM->where('unidade_prisional', $unidade);

            $qtdSentenciadosMSNIH->where('unidade_prisional', $unidade);
            $qtdSentenciadosMSNIM->where('unidade_prisional', $unidade);
            $qtdSentenciadosMSH->where('unidade_prisional', $unidade);
            $qtdSentenciadosMSM->where('unidade_prisional', $unidade);
            $qtdProvisoriosMSH->where('unidade_prisional', $unidade);
            $qtdProvisoriosMSM->where('unidade_prisional', $unidade);

            $qtdMedidaDeSegurancaH->where('unidade_prisional', $unidade);
            $qtdMedidaDeSegurancaM->where('unidade_prisional', $unidade);

            $qtdHA->where('unidade_prisional', $unidade);
            $qtdMA->where('unidade_prisional', $unidade);

            $internosH->where('unidade_prisional', $unidade);
            $internosM->where('unidade_prisional', $unidade);

            $qtdFaccaoNIH->where('unidade_prisional', $unidade);
            $qtdFaccaoNIM->where('unidade_prisional', $unidade);

            $periculosidadeAltaH->where('unidade_prisional', $unidade);
            $periculosidadeMediaH->where('unidade_prisional', $unidade);
            $periculosidadeBaixaH->where('unidade_prisional', $unidade);
            $periculosidadeNIH->where('unidade_prisional', $unidade);
            $periculosidadeAltaM->where('unidade_prisional', $unidade);
            $periculosidadeMediaM->where('unidade_prisional', $unidade);
            $periculosidadeBaixaM->where('unidade_prisional', $unidade);
            $periculosidadeNIM->where('unidade_prisional', $unidade);

            $qtdLiderancaSimH->where('unidade_prisional', $unidade);
            $qtdLiderancaNaoH->where('unidade_prisional', $unidade);
            $qtdLiderancaNIH->where('unidade_prisional', $unidade);
            $qtdLiderancaSimM->where('unidade_prisional', $unidade);
            $qtdLiderancaNaoM->where('unidade_prisional', $unidade);
            $qtdLiderancaNIM->where('unidade_prisional', $unidade);

            //estado_civil
            if ($estadocivil){
                $qtdSolteirosH->where('unidade_prisional', $unidade);
                $qtdSolteirosM->where('unidade_prisional', $unidade);
                $qtdCasadosH->where('unidade_prisional', $unidade);
                $qtdCasadosM->where('unidade_prisional', $unidade);
                $qtdUniaoEstavelH->where('unidade_prisional', $unidade);
                $qtdUniaoEstavelM->where('unidade_prisional', $unidade);
                $qtdSeparadosH->where('unidade_prisional', $unidade);
                $qtdSeparadosM->where('unidade_prisional', $unidade);
                $qtdDivorciadosH->where('unidade_prisional', $unidade);
                $qtdDivorciadosM->where('unidade_prisional', $unidade);
                $qtdViuvosH->where('unidade_prisional', $unidade);
                $qtdViuvosM->where('unidade_prisional', $unidade);
                $qtdEstadoCivilNIH->where('unidade_prisional', $unidade);
                $qtdEstadoCivilNIM->where('unidade_prisional', $unidade);
            }

            //opcao_sexual
            $qtdHeteroH->where('unidade_prisional',$unidade);
            $qtdHeteroM->where('unidade_prisional',$unidade);
            $qtdHomoH->where('unidade_prisional',$unidade);
            $qtdHomoM->where('unidade_prisional',$unidade);
            $qtdBiH->where('unidade_prisional',$unidade);
            $qtdBiM->where('unidade_prisional',$unidade);
            $qtdSexualidadeNaoDeclH->where('unidade_prisional',$unidade);
            $qtdSexualidadeNaoDeclM->where('unidade_prisional',$unidade);
            $qtdSexualidadeNIH->where('unidade_prisional',$unidade);
            $qtdSexualidadeNIM->where('unidade_prisional',$unidade);

            //etnia
            $qtdEtniaBrancaH->where('unidade_prisional',$unidade);
            $qtdEtniaBrancaM->where('unidade_prisional',$unidade);
            $qtdEtniaIndígenaH->where('unidade_prisional',$unidade);
            $qtdEtniaIndígenaM->where('unidade_prisional',$unidade);
            $qtdEtniaPretaH->where('unidade_prisional',$unidade);
            $qtdEtniaPretaM->where('unidade_prisional',$unidade);
            $qtdEtniaPardaH->where('unidade_prisional',$unidade);
            $qtdEtniaPardaM->where('unidade_prisional',$unidade);
            $qtdEtniaAmarelaH->where('unidade_prisional',$unidade);
            $qtdEtniaAmarelaM->where('unidade_prisional',$unidade);
            $qtdEtniaNIH->where('unidade_prisional',$unidade);
            $qtdEtniaNIM->where('unidade_prisional',$unidade);

            // escolaridade
            if($escolaridade){
                $qtdAnalfabetoH->where('unidade_prisional',$unidade);
                $qtdAnalfabetoM->where('unidade_prisional',$unidade);
                $qtdAlfabetizadoH->where('unidade_prisional',$unidade);
                $qtdAlfabetizadoM->where('unidade_prisional',$unidade);
                $qtdFundIncH->where('unidade_prisional',$unidade);
                $qtdFundIncM->where('unidade_prisional',$unidade);
                $qtdFundCompH->where('unidade_prisional',$unidade);
                $qtdFundCompM->where('unidade_prisional',$unidade);
                $qtdMedioIncompH->where('unidade_prisional',$unidade);
                $qtdMedioIncompM->where('unidade_prisional',$unidade);
                $qtdMedioCompH->where('unidade_prisional',$unidade);
                $qtdMedioCompM->where('unidade_prisional',$unidade);
                $qtdSuperiorIncompH->where('unidade_prisional',$unidade);
                $qtdSuperiorIncompM->where('unidade_prisional',$unidade);
                $qtdSuperiorCompH->where('unidade_prisional',$unidade);
                $qtdSuperiorCompM->where('unidade_prisional',$unidade);
                $qtdPosLatoSensuH->where('unidade_prisional',$unidade);
                $qtdPosLatoSensuM->where('unidade_prisional',$unidade);
                $qtdMestradoH->where('unidade_prisional',$unidade);
                $qtdMestradoM->where('unidade_prisional',$unidade);
                $qtdDoutoradoH->where('unidade_prisional',$unidade);
                $qtdDoutoradoM->where('unidade_prisional',$unidade);
                $qtdPosDoutoradoH->where('unidade_prisional',$unidade);
                $qtdPosDoutoradoM->where('unidade_prisional',$unidade);
                $qtdEscolaridadeNIH->where('unidade_prisional',$unidade);
                $qtdEscolaridadeNIM->where('unidade_prisional',$unidade);
            }

            $qtdNacionalidadeNIH->where('unidade_prisional',$unidade);
            $qtdNacionalidadeNIM->where('unidade_prisional',$unidade);

            //religião
            if($religiao){
                $qtdAdventistaH->where('unidade_prisional',$unidade);
                $qtdAdventistaM->where('unidade_prisional',$unidade);
                $qtdBudistaH->where('unidade_prisional',$unidade);
                $qtdBudistaM->where('unidade_prisional',$unidade);
                $qtdCatolicaH->where('unidade_prisional',$unidade);
                $qtdCatolicaM->where('unidade_prisional',$unidade);
                $qtdEspiritaH->where('unidade_prisional',$unidade);
                $qtdEspiritaM->where('unidade_prisional',$unidade);
                $qtdEvangelicaH->where('unidade_prisional',$unidade);
                $qtdEvangelicaM->where('unidade_prisional',$unidade);
                $qtdJudeuH->where('unidade_prisional',$unidade);
                $qtdJudeuM->where('unidade_prisional',$unidade);
                $qtdMormonH->where('unidade_prisional',$unidade);
                $qtdMormonM->where('unidade_prisional',$unidade);
                $qtdMulcumanaH->where('unidade_prisional',$unidade);
                $qtdMulcumanaM->where('unidade_prisional',$unidade);
                $qtdProtestanteH->where('unidade_prisional',$unidade);
                $qtdProtestanteM->where('unidade_prisional',$unidade);
                $qtdAgnosticoH->where('unidade_prisional',$unidade);
                $qtdAgnosticoM->where('unidade_prisional',$unidade);
                $qtdAteuH->where('unidade_prisional',$unidade);
                $qtdAteuM->where('unidade_prisional',$unidade);
                $qtdSemReligiaoH->where('unidade_prisional',$unidade);
                $qtdSemReligiaoM->where('unidade_prisional',$unidade);
                $qtdOutrasH->where('unidade_prisional',$unidade);
                $qtdOutrasM->where('unidade_prisional',$unidade);
                $qtdReligiaoNIH->where('unidade_prisional',$unidade);
                $qtdReligiaoNIM->where('unidade_prisional',$unidade);
            }

            $qtdAlvara->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdAlvara->where('movimentacao_interno.status','Finalizada');
            $qtdAlvara->where('interno.unidade_prisional', $unidade);
            $qtdFuga->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdFuga->where('movimentacao_interno.status','Finalizada');
            $qtdFuga->where('interno.unidade_prisional', $unidade);
            $qtdRecambiamento->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdRecambiamento->where('movimentacao_interno.status','Finalizada');
            $qtdRecambiamento->where('interno.unidade_prisional', $unidade);
            $qtdSaidaJudicial->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdSaidaJudicial->where('movimentacao_interno.status','Finalizada');
            $qtdSaidaJudicial->where('interno.unidade_prisional', $unidade);
            $qtdExtradicao->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdExtradicao->where('movimentacao_interno.status','Finalizada');
            $qtdExtradicao->where('interno.unidade_prisional', $unidade);
            $qtdLivramento->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdLivramento->where('movimentacao_interno.status','Finalizada');
            $qtdLivramento->where('interno.unidade_prisional', $unidade);
            $qtdObito->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdObito->where('movimentacao_interno.status','Finalizada');
            $qtdObito->where('interno.unidade_prisional', $unidade);
            $qtdExtincaopunibilidade->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
            $qtdExtincaopunibilidade->where('movimentacao_interno.status','Finalizada');
            $qtdExtincaopunibilidade->where('interno.unidade_prisional', $unidade);
        }
        if(!is_null($pavilhao)){
            $qtdUnidadeHA->where('pavilhao', $pavilhao);
            $qtdUnidadeMA->where('pavilhao', $pavilhao);
            $qtdUnidadeTotal->where('pavilhao', $pavilhao);

            $qtdInternosLotSelecionada->where('pavilhao',$pavilhao);

            $qtdRegimeNIH->where('pavilhao',$pavilhao);
            $qtdRegimeNIM->where('pavilhao',$pavilhao);
            $qtdRegimeFechadoH->where('pavilhao',$pavilhao);
            $qtdRegimeFechadoM->where('pavilhao',$pavilhao);
            $qtdRegimeSemiabertoH->where('pavilhao',$pavilhao);
            $qtdRegimeSemiabertoM->where('pavilhao',$pavilhao);
            $qtdRegimeAbertoH->where('pavilhao',$pavilhao);
            $qtdRegimeAbertoM->where('pavilhao',$pavilhao);

            $qtdSentenciadosNIH->where('pavilhao',$pavilhao);
            $qtdSentenciadosNIM->where('pavilhao',$pavilhao);
            $qtdSentenciadosH->where('pavilhao',$pavilhao);
            $qtdSentenciadosM->where('pavilhao',$pavilhao);
            $qtdProvisoriosH->where('pavilhao',$pavilhao);
            $qtdProvisoriosM->where('pavilhao',$pavilhao);

            $qtdSentenciadosMSNIH->where('pavilhao',$pavilhao);
            $qtdSentenciadosMSNIM->where('pavilhao',$pavilhao);
            $qtdSentenciadosMSH->where('pavilhao',$pavilhao);
            $qtdSentenciadosMSM->where('pavilhao',$pavilhao);
            $qtdProvisoriosMSH->where('pavilhao',$pavilhao);
            $qtdProvisoriosMSM->where('pavilhao',$pavilhao);

            $qtdMedidaDeSegurancaH->where('pavilhao',$pavilhao);
            $qtdMedidaDeSegurancaM->where('pavilhao',$pavilhao);

            $qtdHA->where('pavilhao',$pavilhao);
            $qtdMA->where('pavilhao',$pavilhao);

            $internosH->where('pavilhao',$pavilhao);
            $internosM->where('pavilhao',$pavilhao);

            $qtdFaccaoNIH->where('pavilhao',$pavilhao);
            $qtdFaccaoNIM->where('pavilhao',$pavilhao);

            $periculosidadeAltaH->where('pavilhao',$pavilhao);
            $periculosidadeMediaH->where('pavilhao',$pavilhao);
            $periculosidadeBaixaH->where('pavilhao',$pavilhao);
            $periculosidadeNIH->where('pavilhao',$pavilhao);
            $periculosidadeAltaM->where('pavilhao',$pavilhao);
            $periculosidadeMediaM->where('pavilhao',$pavilhao);
            $periculosidadeBaixaM->where('pavilhao',$pavilhao);
            $periculosidadeNIM->where('pavilhao',$pavilhao);

            $qtdLiderancaSimH->where('pavilhao',$pavilhao);
            $qtdLiderancaNaoH->where('pavilhao',$pavilhao);
            $qtdLiderancaNIH->where('pavilhao',$pavilhao);
            $qtdLiderancaSimM->where('pavilhao',$pavilhao);
            $qtdLiderancaNaoM->where('pavilhao',$pavilhao);
            $qtdLiderancaNIM->where('pavilhao',$pavilhao);

            //estado_civil
            if($estadocivil){
                $qtdSolteirosH->where('pavilhao',$pavilhao);
                $qtdSolteirosM->where('pavilhao',$pavilhao);
                $qtdCasadosH->where('pavilhao',$pavilhao);
                $qtdCasadosM->where('pavilhao',$pavilhao);
                $qtdUniaoEstavelH->where('pavilhao',$pavilhao);
                $qtdUniaoEstavelM->where('pavilhao',$pavilhao);
                $qtdSeparadosH->where('pavilhao',$pavilhao);
                $qtdSeparadosM->where('pavilhao',$pavilhao);
                $qtdDivorciadosH->where('pavilhao',$pavilhao);
                $qtdDivorciadosM->where('pavilhao',$pavilhao);
                $qtdViuvosH->where('pavilhao',$pavilhao);
                $qtdViuvosM->where('pavilhao',$pavilhao);
                $qtdEstadoCivilNIH->where('pavilhao',$pavilhao);
                $qtdEstadoCivilNIM->where('pavilhao',$pavilhao);
            }

            //opcao_sexual
            $qtdHeteroH->where('pavilhao',$pavilhao);
            $qtdHeteroM->where('pavilhao',$pavilhao);
            $qtdHomoH->where('pavilhao',$pavilhao);
            $qtdHomoM->where('pavilhao',$pavilhao);
            $qtdBiH->where('pavilhao',$pavilhao);
            $qtdBiM->where('pavilhao',$pavilhao);
            $qtdSexualidadeNaoDeclH->where('pavilhao',$pavilhao);
            $qtdSexualidadeNaoDeclM->where('pavilhao',$pavilhao);
            $qtdSexualidadeNIH->where('pavilhao',$pavilhao);
            $qtdSexualidadeNIM->where('pavilhao',$pavilhao);

            //etnia
            $qtdEtniaBrancaH->where('pavilhao',$pavilhao);
            $qtdEtniaBrancaM->where('pavilhao',$pavilhao);
            $qtdEtniaIndígenaH->where('pavilhao',$pavilhao);
            $qtdEtniaIndígenaM->where('pavilhao',$pavilhao);
            $qtdEtniaPretaH->where('pavilhao',$pavilhao);
            $qtdEtniaPretaM->where('pavilhao',$pavilhao);
            $qtdEtniaPardaH->where('pavilhao',$pavilhao);
            $qtdEtniaPardaM->where('pavilhao',$pavilhao);
            $qtdEtniaAmarelaH->where('pavilhao',$pavilhao);
            $qtdEtniaAmarelaM->where('pavilhao',$pavilhao);
            $qtdEtniaNIH->where('pavilhao',$pavilhao);
            $qtdEtniaNIM->where('pavilhao',$pavilhao);

            // escolaridade
            if($escolaridade) {
                $qtdAnalfabetoH->where('pavilhao', $pavilhao);
                $qtdAnalfabetoM->where('pavilhao', $pavilhao);
                $qtdAlfabetizadoH->where('pavilhao', $pavilhao);
                $qtdAlfabetizadoM->where('pavilhao', $pavilhao);
                $qtdFundIncH->where('pavilhao', $pavilhao);
                $qtdFundIncM->where('pavilhao', $pavilhao);
                $qtdFundCompH->where('pavilhao', $pavilhao);
                $qtdFundCompM->where('pavilhao', $pavilhao);
                $qtdMedioIncompH->where('pavilhao', $pavilhao);
                $qtdMedioIncompM->where('pavilhao', $pavilhao);
                $qtdMedioCompH->where('pavilhao', $pavilhao);
                $qtdMedioCompM->where('pavilhao', $pavilhao);
                $qtdSuperiorIncompH->where('pavilhao', $pavilhao);
                $qtdSuperiorIncompM->where('pavilhao', $pavilhao);
                $qtdSuperiorCompH->where('pavilhao', $pavilhao);
                $qtdSuperiorCompM->where('pavilhao', $pavilhao);
                $qtdPosLatoSensuH->where('pavilhao', $pavilhao);
                $qtdPosLatoSensuM->where('pavilhao', $pavilhao);
                $qtdMestradoH->where('pavilhao', $pavilhao);
                $qtdMestradoM->where('pavilhao', $pavilhao);
                $qtdDoutoradoH->where('pavilhao', $pavilhao);
                $qtdDoutoradoM->where('pavilhao', $pavilhao);
                $qtdPosDoutoradoH->where('pavilhao', $pavilhao);
                $qtdPosDoutoradoM->where('pavilhao', $pavilhao);
                $qtdEscolaridadeNIH->where('pavilhao', $pavilhao);
                $qtdEscolaridadeNIM->where('pavilhao', $pavilhao);
            }

            $qtdNacionalidadeNIH->where('pavilhao',$pavilhao);
            $qtdNacionalidadeNIM->where('pavilhao',$pavilhao);

            if($religiao){
                $qtdAdventistaH->where('pavilhao',$pavilhao);
                $qtdAdventistaM->where('pavilhao',$pavilhao);
                $qtdBudistaH->where('pavilhao',$pavilhao);
                $qtdBudistaM->where('pavilhao',$pavilhao);
                $qtdCatolicaH->where('pavilhao',$pavilhao);
                $qtdCatolicaM->where('pavilhao',$pavilhao);
                $qtdEspiritaH->where('pavilhao',$pavilhao);
                $qtdEspiritaM->where('pavilhao',$pavilhao);
                $qtdEvangelicaH->where('pavilhao',$pavilhao);
                $qtdEvangelicaM->where('pavilhao',$pavilhao);
                $qtdJudeuH->where('pavilhao',$pavilhao);
                $qtdJudeuM->where('pavilhao',$pavilhao);
                $qtdMormonH->where('pavilhao',$pavilhao);
                $qtdMormonM->where('pavilhao',$pavilhao);
                $qtdMulcumanaH->where('pavilhao',$pavilhao);
                $qtdMulcumanaM->where('pavilhao',$pavilhao);
                $qtdProtestanteH->where('pavilhao',$pavilhao);
                $qtdProtestanteM->where('pavilhao',$pavilhao);
                $qtdAgnosticoH->where('pavilhao',$pavilhao);
                $qtdAgnosticoM->where('pavilhao',$pavilhao);
                $qtdAteuH->where('pavilhao',$pavilhao);
                $qtdAteuM->where('pavilhao',$pavilhao);
                $qtdSemReligiaoH->where('pavilhao',$pavilhao);
                $qtdSemReligiaoM->where('pavilhao',$pavilhao);
                $qtdOutrasH->where('pavilhao',$pavilhao);
                $qtdOutrasM->where('pavilhao',$pavilhao);
                $qtdReligiaoNIH->where('pavilhao',$pavilhao);
                $qtdReligiaoNIM->where('pavilhao',$pavilhao);
            }

            //saida da unidade
            $qtdAlvara->where('interno.pavilhao',$pavilhao);
            $qtdFuga->where('interno.pavilhao',$pavilhao);
            $qtdRecambiamento->where('interno.pavilhao',$pavilhao);
            $qtdSaidaJudicial->where('interno.pavilhao',$pavilhao);
            $qtdExtradicao->where('interno.pavilhao',$pavilhao);
            $qtdLivramento->where('interno.pavilhao',$pavilhao);
            $qtdObito->where('interno.pavilhao',$pavilhao);
            $qtdExtincaopunibilidade->where('interno.pavilhao',$pavilhao);
        }
        if(!is_null($ala)) {
            $qtdUnidadeHA->where('ala', $ala);
            $qtdUnidadeMA->where('ala', $ala);
            $qtdUnidadeTotal->where('ala', $ala);

            $qtdInternosLotSelecionada->where('ala',$ala);

            $qtdRegimeNIH->where('ala',$ala);
            $qtdRegimeNIM->where('ala',$ala);
            $qtdRegimeFechadoH->where('ala',$ala);
            $qtdRegimeFechadoM->where('ala',$ala);
            $qtdRegimeSemiabertoH->where('ala',$ala);
            $qtdRegimeSemiabertoM->where('ala',$ala);
            $qtdRegimeAbertoH->where('ala',$ala);
            $qtdRegimeAbertoM->where('ala',$ala);

            $qtdSentenciadosNIH->where('ala',$ala);
            $qtdSentenciadosNIM->where('ala',$ala);
            $qtdSentenciadosH->where('ala',$ala);
            $qtdSentenciadosM->where('ala',$ala);
            $qtdProvisoriosH->where('ala',$ala);
            $qtdProvisoriosM->where('ala',$ala);

            $qtdSentenciadosMSNIH->where('ala',$ala);
            $qtdSentenciadosMSNIM->where('ala',$ala);
            $qtdSentenciadosMSH->where('ala',$ala);
            $qtdSentenciadosMSM->where('ala',$ala);
            $qtdProvisoriosMSH->where('ala',$ala);
            $qtdProvisoriosMSM->where('ala',$ala);

            $qtdMedidaDeSegurancaH->where('ala',$ala);
            $qtdMedidaDeSegurancaM->where('ala',$ala);

            $qtdHA->where('ala',$ala);
            $qtdMA->where('ala',$ala);

            $internosH->where('ala',$ala);
            $internosM->where('ala',$ala);

            $qtdFaccaoNIH->where('ala',$ala);
            $qtdFaccaoNIM->where('ala',$ala);

            $periculosidadeAltaH->where('ala',$ala);
            $periculosidadeMediaH->where('ala',$ala);
            $periculosidadeBaixaH->where('ala',$ala);
            $periculosidadeNIH->where('ala',$ala);
            $periculosidadeAltaM->where('ala',$ala);
            $periculosidadeMediaM->where('ala',$ala);
            $periculosidadeBaixaM->where('ala',$ala);
            $periculosidadeNIM->where('ala',$ala);

            $qtdLiderancaSimH->where('ala',$ala);
            $qtdLiderancaNaoH->where('ala',$ala);
            $qtdLiderancaNIH->where('ala',$ala);
            $qtdLiderancaSimM->where('ala',$ala);
            $qtdLiderancaNaoM->where('ala',$ala);
            $qtdLiderancaNIM->where('ala',$ala);

            //estado_civil
            if($estadocivil) {
                $qtdSolteirosH->where('ala', $ala);
                $qtdSolteirosM->where('ala', $ala);
                $qtdCasadosH->where('ala', $ala);
                $qtdCasadosM->where('ala', $ala);
                $qtdUniaoEstavelH->where('ala', $ala);
                $qtdUniaoEstavelM->where('ala', $ala);
                $qtdSeparadosH->where('ala', $ala);
                $qtdSeparadosM->where('ala', $ala);
                $qtdDivorciadosH->where('ala', $ala);
                $qtdDivorciadosM->where('ala', $ala);
                $qtdViuvosH->where('ala', $ala);
                $qtdViuvosM->where('ala', $ala);
                $qtdEstadoCivilNIH->where('ala', $ala);
                $qtdEstadoCivilNIM->where('ala', $ala);
            }

            //opcao_sexual
            $qtdHeteroH->where('ala',$ala);
            $qtdHeteroM->where('ala',$ala);
            $qtdHomoH->where('ala',$ala);
            $qtdHomoM->where('ala',$ala);
            $qtdBiH->where('ala',$ala);
            $qtdBiM->where('ala',$ala);
            $qtdSexualidadeNaoDeclH->where('ala',$ala);
            $qtdSexualidadeNaoDeclM->where('ala',$ala);
            $qtdSexualidadeNIH->where('ala',$ala);
            $qtdSexualidadeNIM->where('ala',$ala);

            //etnia
            $qtdEtniaBrancaH->where('ala',$ala);
            $qtdEtniaBrancaM->where('ala',$ala);
            $qtdEtniaIndígenaH->where('ala',$ala);
            $qtdEtniaIndígenaM->where('ala',$ala);
            $qtdEtniaPretaH->where('ala',$ala);
            $qtdEtniaPretaM->where('ala',$ala);
            $qtdEtniaPardaH->where('ala',$ala);
            $qtdEtniaPardaM->where('ala',$ala);
            $qtdEtniaAmarelaH->where('ala',$ala);
            $qtdEtniaAmarelaM->where('ala',$ala);
            $qtdEtniaNIH->where('ala',$ala);
            $qtdEtniaNIM->where('ala',$ala);

            // escolaridade
            if($escolaridade) {
                $qtdAnalfabetoH->where('ala', $ala);
                $qtdAnalfabetoM->where('ala', $ala);
                $qtdAlfabetizadoH->where('ala', $ala);
                $qtdAlfabetizadoM->where('ala', $ala);
                $qtdFundIncH->where('ala', $ala);
                $qtdFundIncM->where('ala', $ala);
                $qtdFundCompH->where('ala', $ala);
                $qtdFundCompM->where('ala', $ala);
                $qtdMedioIncompH->where('ala', $ala);
                $qtdMedioIncompM->where('ala', $ala);
                $qtdMedioCompH->where('ala', $ala);
                $qtdMedioCompM->where('ala', $ala);
                $qtdSuperiorIncompH->where('ala', $ala);
                $qtdSuperiorIncompM->where('ala', $ala);
                $qtdSuperiorCompH->where('ala', $ala);
                $qtdSuperiorCompM->where('ala', $ala);
                $qtdPosLatoSensuH->where('ala', $ala);
                $qtdPosLatoSensuM->where('ala', $ala);
                $qtdMestradoH->where('ala', $ala);
                $qtdMestradoM->where('ala', $ala);
                $qtdDoutoradoH->where('ala', $ala);
                $qtdDoutoradoM->where('ala', $ala);
                $qtdPosDoutoradoH->where('ala', $ala);
                $qtdPosDoutoradoM->where('ala', $ala);
                $qtdEscolaridadeNIH->where('ala', $ala);
                $qtdEscolaridadeNIM->where('ala', $ala);
            }

            $qtdNacionalidadeNIH->where('ala',$ala);
            $qtdNacionalidadeNIM->where('ala',$ala);

            if($religiao) {
                $qtdAdventistaH->where('ala', $ala);
                $qtdAdventistaM->where('ala', $ala);
                $qtdBudistaH->where('ala', $ala);
                $qtdBudistaM->where('ala', $ala);
                $qtdCatolicaH->where('ala', $ala);
                $qtdCatolicaM->where('ala', $ala);
                $qtdEspiritaH->where('ala', $ala);
                $qtdEspiritaM->where('ala', $ala);
                $qtdEvangelicaH->where('ala', $ala);
                $qtdEvangelicaM->where('ala', $ala);
                $qtdJudeuH->where('ala', $ala);
                $qtdJudeuM->where('ala', $ala);
                $qtdMormonH->where('ala', $ala);
                $qtdMormonM->where('ala', $ala);
                $qtdMulcumanaH->where('ala', $ala);
                $qtdMulcumanaM->where('ala', $ala);
                $qtdProtestanteH->where('ala', $ala);
                $qtdProtestanteM->where('ala', $ala);
                $qtdAgnosticoH->where('ala', $ala);
                $qtdAgnosticoM->where('ala', $ala);
                $qtdAteuH->where('ala', $ala);
                $qtdAteuM->where('ala', $ala);
                $qtdSemReligiaoH->where('ala', $ala);
                $qtdSemReligiaoM->where('ala', $ala);
                $qtdOutrasH->where('ala', $ala);
                $qtdOutrasM->where('ala', $ala);
                $qtdReligiaoNIH->where('ala', $ala);
                $qtdReligiaoNIM->where('ala', $ala);
            }

            //saida da unidade
            $qtdAlvara->where('interno.ala',$ala);
            $qtdFuga->where('interno.ala',$ala);
            $qtdRecambiamento->where('interno.ala',$ala);
            $qtdSaidaJudicial->where('interno.ala',$ala);
            $qtdExtradicao->where('interno.ala',$ala);
            $qtdLivramento->where('interno.ala',$ala);
            $qtdObito->where('interno.ala',$ala);
            $qtdExtincaopunibilidade->where('interno.ala',$ala);
        }
        if(!is_null($cela)) {
            $qtdUnidadeHA->where('cela', $cela);
            $qtdUnidadeMA->where('cela', $cela);
            $qtdUnidadeTotal->where('cela', $cela);

            $qtdInternosLotSelecionada->where('cela',$cela);

            $qtdRegimeNIH->where('cela',$cela);
            $qtdRegimeNIM->where('cela',$cela);
            $qtdRegimeFechadoH->where('cela',$cela);
            $qtdRegimeFechadoM->where('cela',$cela);
            $qtdRegimeSemiabertoH->where('cela',$cela);
            $qtdRegimeSemiabertoM->where('cela',$cela);
            $qtdRegimeAbertoH->where('cela',$cela);
            $qtdRegimeAbertoM->where('cela',$cela);

            $qtdSentenciadosNIH->where('cela',$cela);
            $qtdSentenciadosNIM->where('cela',$cela);
            $qtdSentenciadosH->where('cela',$cela);
            $qtdSentenciadosM->where('cela',$cela);
            $qtdProvisoriosH->where('cela',$cela);
            $qtdProvisoriosM->where('cela',$cela);

            $qtdSentenciadosMSNIH->where('cela',$cela);
            $qtdSentenciadosMSNIM->where('cela',$cela);
            $qtdSentenciadosMSH->where('cela',$cela);
            $qtdSentenciadosMSM->where('cela',$cela);
            $qtdProvisoriosMSH->where('cela',$cela);
            $qtdProvisoriosMSM->where('cela',$cela);

            $qtdMedidaDeSegurancaH->where('cela',$cela);
            $qtdMedidaDeSegurancaM->where('cela',$cela);

            $qtdHA->where('cela',$cela);
            $qtdMA->where('cela',$cela);

            $internosH->where('cela',$cela);
            $internosM->where('cela',$cela);

            $qtdFaccaoNIH->where('cela',$cela);
            $qtdFaccaoNIM->where('cela',$cela);

            $periculosidadeAltaH->where('cela',$cela);
            $periculosidadeMediaH->where('cela',$cela);
            $periculosidadeBaixaH->where('cela',$cela);
            $periculosidadeNIH->where('cela',$cela);
            $periculosidadeAltaM->where('cela',$cela);
            $periculosidadeMediaM->where('cela',$cela);
            $periculosidadeBaixaM->where('cela',$cela);
            $periculosidadeNIM->where('cela',$cela);

            $qtdLiderancaSimH->where('cela',$cela);
            $qtdLiderancaNaoH->where('cela',$cela);
            $qtdLiderancaNIH->where('cela',$cela);
            $qtdLiderancaSimM->where('cela',$cela);
            $qtdLiderancaNaoM->where('cela',$cela);
            $qtdLiderancaNIM->where('cela',$cela);

            //estado_civil
            if($estadocivil) {
                $qtdSolteirosH->where('cela', $cela);
                $qtdSolteirosM->where('cela', $cela);
                $qtdCasadosH->where('cela', $cela);
                $qtdCasadosM->where('cela', $cela);
                $qtdUniaoEstavelH->where('cela', $cela);
                $qtdUniaoEstavelM->where('cela', $cela);
                $qtdSeparadosH->where('cela', $cela);
                $qtdSeparadosM->where('cela', $cela);
                $qtdDivorciadosH->where('cela', $cela);
                $qtdDivorciadosM->where('cela', $cela);
                $qtdViuvosH->where('cela', $cela);
                $qtdViuvosM->where('cela', $cela);
                $qtdEstadoCivilNIH->where('cela', $cela);
                $qtdEstadoCivilNIM->where('cela', $cela);
            }

            //opcao_sexual
            $qtdHeteroH->where('cela',$cela);
            $qtdHeteroM->where('cela',$cela);
            $qtdHomoH->where('cela',$cela);
            $qtdHomoM->where('cela',$cela);
            $qtdBiH->where('cela',$cela);
            $qtdBiM->where('cela',$cela);
            $qtdSexualidadeNaoDeclH->where('cela',$cela);
            $qtdSexualidadeNaoDeclM->where('cela',$cela);
            $qtdSexualidadeNIH->where('cela',$cela);
            $qtdSexualidadeNIM->where('cela',$cela);

            //etnia
            $qtdEtniaBrancaH->where('cela',$cela);
            $qtdEtniaBrancaM->where('cela',$cela);
            $qtdEtniaIndígenaH->where('cela',$cela);
            $qtdEtniaIndígenaM->where('cela',$cela);
            $qtdEtniaPretaH->where('cela',$cela);
            $qtdEtniaPretaM->where('cela',$cela);
            $qtdEtniaPardaH->where('cela',$cela);
            $qtdEtniaPardaM->where('cela',$cela);
            $qtdEtniaAmarelaH->where('cela',$cela);
            $qtdEtniaAmarelaM->where('cela',$cela);
            $qtdEtniaNIH->where('cela',$cela);
            $qtdEtniaNIM->where('cela',$cela);

            // escolaridade
            if($escolaridade) {
                $qtdAnalfabetoH->where('cela', $cela);
                $qtdAnalfabetoM->where('cela', $cela);
                $qtdAlfabetizadoH->where('cela', $cela);
                $qtdAlfabetizadoM->where('cela', $cela);
                $qtdFundIncH->where('cela', $cela);
                $qtdFundIncM->where('cela', $cela);
                $qtdFundCompH->where('cela', $cela);
                $qtdFundCompM->where('cela', $cela);
                $qtdMedioIncompH->where('cela', $cela);
                $qtdMedioIncompM->where('cela', $cela);
                $qtdMedioCompH->where('cela', $cela);
                $qtdMedioCompM->where('cela', $cela);
                $qtdSuperiorIncompH->where('cela', $cela);
                $qtdSuperiorIncompM->where('cela', $cela);
                $qtdSuperiorCompH->where('cela', $cela);
                $qtdSuperiorCompM->where('cela', $cela);
                $qtdPosLatoSensuH->where('cela', $cela);
                $qtdPosLatoSensuM->where('cela', $cela);
                $qtdMestradoH->where('cela', $cela);
                $qtdMestradoM->where('cela', $cela);
                $qtdDoutoradoH->where('cela', $cela);
                $qtdDoutoradoM->where('cela', $cela);
                $qtdPosDoutoradoH->where('cela', $cela);
                $qtdPosDoutoradoM->where('cela', $cela);
                $qtdEscolaridadeNIH->where('cela', $cela);
                $qtdEscolaridadeNIM->where('cela', $cela);
            }

            $qtdNacionalidadeNIH->where('cela',$cela);
            $qtdNacionalidadeNIM->where('cela',$cela);

            if($religiao) {
                $qtdAdventistaH->where('cela', $cela);
                $qtdAdventistaM->where('cela', $cela);
                $qtdBudistaH->where('cela', $cela);
                $qtdBudistaM->where('cela', $cela);
                $qtdCatolicaH->where('cela', $cela);
                $qtdCatolicaM->where('cela', $cela);
                $qtdEspiritaH->where('cela', $cela);
                $qtdEspiritaM->where('cela', $cela);
                $qtdEvangelicaH->where('cela', $cela);
                $qtdEvangelicaM->where('cela', $cela);
                $qtdJudeuH->where('cela', $cela);
                $qtdJudeuM->where('cela', $cela);
                $qtdMormonH->where('cela', $cela);
                $qtdMormonM->where('cela', $cela);
                $qtdMulcumanaH->where('cela', $cela);
                $qtdMulcumanaM->where('cela', $cela);
                $qtdProtestanteH->where('cela', $cela);
                $qtdProtestanteM->where('cela', $cela);
                $qtdAgnosticoH->where('cela', $cela);
                $qtdAgnosticoM->where('cela', $cela);
                $qtdAteuH->where('cela', $cela);
                $qtdAteuM->where('cela', $cela);
                $qtdSemReligiaoH->where('cela', $cela);
                $qtdSemReligiaoM->where('cela', $cela);
                $qtdOutrasH->where('cela', $cela);
                $qtdOutrasM->where('cela', $cela);
                $qtdReligiaoNIH->where('cela', $cela);
                $qtdReligiaoNIM->where('cela', $cela);
            }

            //saida da unidade
            $qtdAlvara->where('interno.cela',$cela);
            $qtdFuga->where('interno.cela',$cela);
            $qtdRecambiamento->where('interno.cela',$cela);
            $qtdSaidaJudicial->where('interno.cela',$cela);
            $qtdExtradicao->where('interno.cela',$cela);
            $qtdLivramento->where('interno.cela',$cela);
            $qtdObito->where('interno.cela',$cela);
            $qtdExtincaopunibilidade->where('interno.cela',$cela);
        }

        $internosH=$internosH->get();
        if($faixaetaria) {
            foreach ($internosH as $interno) {
                if (is_null($interno->data_nascimento)) {
                    $faixaEtaria_naoInformadaH++;
                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 17) {
                    $faixaEtaria_zero_a_dezesseteH++;
                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 18 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 24) {
                    $faixaEtaria_dezoito_a_vinteequatroH++;
                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 25 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 29) {
                    $faixaEtaria_vinteecinco_a_vinteenoveH++;
                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 30 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 34) {
                    $faixaEtaria_trinta_a_trintaequatroH++;
                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 35 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 45) {
                    $faixaEtaria_trintaecinco_a_quarentaecincoH++;
                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 46 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 60) {
                    $faixaEtaria_quarentaeseis_a_sessentaH++;
                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 61 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 70) {
                    $faixaEtaria_sessentaeum_a_setentaH++;
                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) > 70) {
                    $faixaEtaria_maisDeSetentaH++;
                }
            }
        }

        $internosM=$internosM->get();
        if($faixaetaria) {
            foreach ($internosM as $interno) {
                if (is_null($interno->data_nascimento)) {
                    $faixaEtaria_naoInformadaM++;
                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 17) {
                    $faixaEtaria_zero_a_dezesseteM++;
                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 18 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 24) {
                    $faixaEtaria_dezoito_a_vinteequatroM++;
                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 25 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 29) {
                    $faixaEtaria_vinteecinco_a_vinteenoveM++;
                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 30 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 34) {
                    $faixaEtaria_trinta_a_trintaequatroM++;
                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 35 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 45) {
                    $faixaEtaria_trintaecinco_a_quarentaecincoM++;
                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 46 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 60) {
                    $faixaEtaria_quarentaeseis_a_sessentaM++;
                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 61 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 70) {
                    $faixaEtaria_sessentaeum_a_setentaM++;
                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) > 70) {
                    $faixaEtaria_maisDeSetentaM++;
                }
            }
        }

        $nacionalidades = DB::table('interno')->select(DB::raw('count(*) as qtdd, naturalidade_pais'))
            ->where('status','A')
            ->groupBy('naturalidade_pais')->orderBy('qtdd','DESC')->get();
        foreach($nacionalidades as $key=>$nacionalidade){
            $nacionalidades[$key]->qtdH = DB::table('interno')->where('sexo','M')->where('status','A')->where('naturalidade_pais', $nacionalidade->naturalidade_pais);
            $nacionalidades[$key]->qtdM = DB::table('interno')->where('sexo','F')->where('status','A')->where('naturalidade_pais', $nacionalidade->naturalidade_pais);

            if(!is_null($unidade)) {
                $nacionalidades[$key]->qtdH->where('unidade_prisional',$unidade);
                $nacionalidades[$key]->qtdM->where('unidade_prisional',$unidade);
            }
            if(!is_null($pavilhao)) {
                $nacionalidades[$key]->qtdH->where('pavilhao',$pavilhao);
                $nacionalidades[$key]->qtdM->where('pavilhao',$pavilhao);
            }
            if(!is_null($ala)) {
                $nacionalidades[$key]->qtdH->where('ala',$ala);
                $nacionalidades[$key]->qtdM->where('ala',$ala);
            }
            if(!is_null($cela)) {
                $nacionalidades[$key]->qtdH->where('cela',$cela);
                $nacionalidades[$key]->qtdM->where('cela',$cela);
            }

            $nacionalidades[$key]->qtdH = $nacionalidades[$key]->qtdH->count();
            $nacionalidades[$key]->qtdM = $nacionalidades[$key]->qtdM->count();
            $nacionalidades[$key]->qtd = $nacionalidades[$key]->qtdH + $nacionalidades[$key]->qtdM;
        }

        $qtdNacionalidadeNIH = $qtdNacionalidadeNIH->count();
        $qtdNacionalidadeNIM = $qtdNacionalidadeNIM->count();

        $faccoes = DB::table('faccao')->select('id','nome','sigla')->get();
        foreach($faccoes as $key=>$faccao){
            $faccoes[$key]->qtdH = DB::table('interno')->where('sexo','M')->where('status','A')->where('faccao', $faccao->id);
            $faccoes[$key]->qtdM = DB::table('interno')->where('sexo','F')->where('status','A')->where('faccao', $faccao->id);

            if(!is_null($unidade)) {
                $faccoes[$key]->qtdH->where('unidade_prisional',$unidade);
                $faccoes[$key]->qtdM->where('unidade_prisional',$unidade);
            }
            if(!is_null($pavilhao)) {
                $faccoes[$key]->qtdH->where('pavilhao',$pavilhao);
                $faccoes[$key]->qtdM->where('pavilhao',$pavilhao);
            }
            if(!is_null($ala)) {
                $faccoes[$key]->qtdH->where('ala',$ala);
                $faccoes[$key]->qtdM->where('ala',$ala);
            }
            if(!is_null($cela)) {
                $faccoes[$key]->qtdH->where('cela',$cela);
                $faccoes[$key]->qtdM->where('cela',$cela);
            }

            $faccoes[$key]->qtdH = $faccoes[$key]->qtdH->count();
            $faccoes[$key]->qtdM = $faccoes[$key]->qtdM->count();
            $faccoes[$key]->qtd = $faccoes[$key]->qtdH + $faccoes[$key]->qtdM;
        }

        $faccoes = $faccoes->sortBy('qtd')->reverse();

        $qtdUnidadeHA = $qtdUnidadeHA->get();
        $qtdUnidadeMA = $qtdUnidadeMA->get();
        $qtdUnidadeTotal = $qtdUnidadeTotal->get();

        $qtdFaccaoNIH = $qtdFaccaoNIH->count();
        $qtdFaccaoNIM = $qtdFaccaoNIM->count();

        $qtdInternosLotSelecionada=$qtdInternosLotSelecionada->count();

        $qtdRegimeNIH=$qtdRegimeNIH->count();
        $qtdRegimeNIM=$qtdRegimeNIM->count();
        $qtdRegimeFechadoH=$qtdRegimeFechadoH->count();
        $qtdRegimeFechadoM=$qtdRegimeFechadoM->count();
        $qtdRegimeSemiabertoH=$qtdRegimeSemiabertoH->count();
        $qtdRegimeSemiabertoM=$qtdRegimeSemiabertoM->count();
        $qtdRegimeAbertoH=$qtdRegimeAbertoH->count();
        $qtdRegimeAbertoM=$qtdRegimeAbertoM->count();

        $qtdSentenciadosNIH=$qtdSentenciadosNIH->count();
        $qtdSentenciadosNIM=$qtdSentenciadosNIM->count();
        $qtdSentenciadosH=$qtdSentenciadosH->count();
        $qtdSentenciadosM=$qtdSentenciadosM->count();
        $qtdProvisoriosH=$qtdProvisoriosH->count();
        $qtdProvisoriosM=$qtdProvisoriosM->count();

        $qtdSentenciadosMSNIH=$qtdSentenciadosMSNIH->count();
        $qtdSentenciadosMSNIM=$qtdSentenciadosMSNIM->count();
        $qtdSentenciadosMSH=$qtdSentenciadosMSH->count();
        $qtdSentenciadosMSM=$qtdSentenciadosMSM->count();
        $qtdProvisoriosMSH=$qtdProvisoriosMSH->count();
        $qtdProvisoriosMSM=$qtdProvisoriosMSM->count();

        $qtdMedidaDeSegurancaH=$qtdMedidaDeSegurancaH->count();
        $qtdMedidaDeSegurancaM=$qtdMedidaDeSegurancaM->count();

        $qtdHA=$qtdHA->count();
        $qtdMA=$qtdMA->count();

        $periculosidadeAltaH=$periculosidadeAltaH->count();
        $periculosidadeMediaH=$periculosidadeMediaH->count();
        $periculosidadeBaixaH=$periculosidadeBaixaH->count();
        $periculosidadeNIH=$periculosidadeNIH->count();
        $periculosidadeAltaM=$periculosidadeAltaM->count();
        $periculosidadeMediaM=$periculosidadeMediaM->count();
        $periculosidadeBaixaM=$periculosidadeBaixaM->count();
        $periculosidadeNIM=$periculosidadeNIM->count();

        $qtdLiderancaSimH=$qtdLiderancaSimH->count();
        $qtdLiderancaNaoH=$qtdLiderancaNaoH->count();
        $qtdLiderancaNIH=$qtdLiderancaNIH->count();
        $qtdLiderancaSimM=$qtdLiderancaSimM->count();
        $qtdLiderancaNaoM=$qtdLiderancaNaoM->count();
        $qtdLiderancaNIM=$qtdLiderancaNIM->count();

        //estado_civil
        if($estadocivil) {
            $qtdSolteirosH = $qtdSolteirosH->count();
            $qtdSolteirosM = $qtdSolteirosM->count();
            $qtdCasadosH = $qtdCasadosH->count();
            $qtdCasadosM = $qtdCasadosM->count();
            $qtdUniaoEstavelH = $qtdUniaoEstavelH->count();
            $qtdUniaoEstavelM = $qtdUniaoEstavelM->count();
            $qtdSeparadosH = $qtdSeparadosH->count();
            $qtdSeparadosM = $qtdSeparadosM->count();
            $qtdDivorciadosH = $qtdDivorciadosH->count();
            $qtdDivorciadosM = $qtdDivorciadosM->count();
            $qtdViuvosH = $qtdViuvosH->count();
            $qtdViuvosM = $qtdViuvosM->count();
            $qtdEstadoCivilNIH = $qtdEstadoCivilNIH->count();
            $qtdEstadoCivilNIM = $qtdEstadoCivilNIM->count();
        }else{
            $qtdSolteirosH =
            $qtdSolteirosM =
            $qtdCasadosH =
            $qtdCasadosM =
            $qtdUniaoEstavelH =
            $qtdUniaoEstavelM =
            $qtdSeparadosH =
            $qtdSeparadosM =
            $qtdDivorciadosH =
            $qtdDivorciadosM =
            $qtdViuvosH =
            $qtdViuvosM =
            $qtdEstadoCivilNIH =
            $qtdEstadoCivilNIM = 0;
        }

        //opcao_sexual
        $qtdHeteroH = $qtdHeteroH->count();
        $qtdHeteroM = $qtdHeteroM->count();
        $qtdHomoH = $qtdHomoH->count();
        $qtdHomoM = $qtdHomoM->count();
        $qtdBiH = $qtdBiH->count();
        $qtdBiM = $qtdBiM->count();
        $qtdSexualidadeNaoDeclH = $qtdSexualidadeNaoDeclH->count();
        $qtdSexualidadeNaoDeclM = $qtdSexualidadeNaoDeclM->count();
        $qtdSexualidadeNIH = $qtdSexualidadeNIH->count();
        $qtdSexualidadeNIM = $qtdSexualidadeNIM->count();

        //etnia
        $qtdEtniaBrancaH=$qtdEtniaBrancaH->count();
        $qtdEtniaBrancaM=$qtdEtniaBrancaM->count();
        $qtdEtniaIndígenaH=$qtdEtniaIndígenaH->count();
        $qtdEtniaIndígenaM=$qtdEtniaIndígenaM->count();
        $qtdEtniaPretaH=$qtdEtniaPretaH->count();
        $qtdEtniaPretaM=$qtdEtniaPretaM->count();
        $qtdEtniaPardaH=$qtdEtniaPardaH->count();
        $qtdEtniaPardaM=$qtdEtniaPardaM->count();
        $qtdEtniaAmarelaH=$qtdEtniaAmarelaH->count();
        $qtdEtniaAmarelaM=$qtdEtniaAmarelaM->count();
        $qtdEtniaNIH=$qtdEtniaNIH->count();
        $qtdEtniaNIM=$qtdEtniaNIM->count();

        // escolaridade
        if($escolaridade) {
            $qtdAnalfabetoH = $qtdAnalfabetoH->count();
            $qtdAnalfabetoM = $qtdAnalfabetoM->count();
            $qtdAlfabetizadoH = $qtdAlfabetizadoH->count();
            $qtdAlfabetizadoM = $qtdAlfabetizadoM->count();
            $qtdFundIncH = $qtdFundIncH->count();
            $qtdFundIncM = $qtdFundIncM->count();
            $qtdFundCompH = $qtdFundCompH->count();
            $qtdFundCompM = $qtdFundCompM->count();
            $qtdMedioIncompH = $qtdMedioIncompH->count();
            $qtdMedioIncompM = $qtdMedioIncompM->count();
            $qtdMedioCompH = $qtdMedioCompH->count();
            $qtdMedioCompM = $qtdMedioCompM->count();
            $qtdSuperiorIncompH = $qtdSuperiorIncompH->count();
            $qtdSuperiorIncompM = $qtdSuperiorIncompM->count();
            $qtdSuperiorCompH = $qtdSuperiorCompH->count();
            $qtdSuperiorCompM = $qtdSuperiorCompM->count();
            $qtdPosLatoSensuH = $qtdPosLatoSensuH->count();
            $qtdPosLatoSensuM = $qtdPosLatoSensuM->count();
            $qtdMestradoH = $qtdMestradoH->count();
            $qtdMestradoM = $qtdMestradoM->count();
            $qtdDoutoradoH = $qtdDoutoradoH->count();
            $qtdDoutoradoM = $qtdDoutoradoM->count();
            $qtdPosDoutoradoH = $qtdPosDoutoradoH->count();
            $qtdPosDoutoradoM = $qtdPosDoutoradoM->count();
            $qtdEscolaridadeNIH = $qtdEscolaridadeNIH->count();
            $qtdEscolaridadeNIM = $qtdEscolaridadeNIM->count();
        } else {
            $qtdAnalfabetoH =
            $qtdAnalfabetoM =
            $qtdAlfabetizadoH =
            $qtdAlfabetizadoM =
            $qtdFundIncH =
            $qtdFundIncM =
            $qtdFundCompH =
            $qtdFundCompM =
            $qtdMedioIncompH =
            $qtdMedioIncompM =
            $qtdMedioCompH =
            $qtdMedioCompM =
            $qtdSuperiorIncompH =
            $qtdSuperiorIncompM =
            $qtdSuperiorCompH =
            $qtdSuperiorCompM =
            $qtdPosLatoSensuH =
            $qtdPosLatoSensuM =
            $qtdMestradoH =
            $qtdMestradoM =
            $qtdDoutoradoH =
            $qtdDoutoradoM =
            $qtdPosDoutoradoH =
            $qtdPosDoutoradoM =
            $qtdEscolaridadeNIH =
            $qtdEscolaridadeNIM = 0;
        }

        if($religiao) {
            $qtdAdventistaH = $qtdAdventistaH->count();
            $qtdAdventistaM = $qtdAdventistaM->count();
            $qtdBudistaH = $qtdBudistaH->count();
            $qtdBudistaM = $qtdBudistaM->count();
            $qtdCatolicaH = $qtdCatolicaH->count();
            $qtdCatolicaM = $qtdCatolicaM->count();
            $qtdEspiritaH = $qtdEspiritaH->count();
            $qtdEspiritaM = $qtdEspiritaM->count();
            $qtdEvangelicaH = $qtdEvangelicaH->count();
            $qtdEvangelicaM = $qtdEvangelicaM->count();
            $qtdJudeuH = $qtdJudeuH->count();
            $qtdJudeuM = $qtdJudeuM->count();
            $qtdMormonH = $qtdMormonH->count();
            $qtdMormonM = $qtdMormonM->count();
            $qtdMulcumanaH = $qtdMulcumanaH->count();
            $qtdMulcumanaM = $qtdMulcumanaM->count();
            $qtdProtestanteH = $qtdProtestanteH->count();
            $qtdProtestanteM = $qtdProtestanteM->count();
            $qtdAgnosticoH = $qtdAgnosticoH->count();
            $qtdAgnosticoM = $qtdAgnosticoM->count();
            $qtdAteuH = $qtdAteuH->count();
            $qtdAteuM = $qtdAteuM->count();
            $qtdSemReligiaoH = $qtdSemReligiaoH->count();
            $qtdSemReligiaoM = $qtdSemReligiaoM->count();
            $qtdOutrasH = $qtdOutrasH->count();
            $qtdOutrasM = $qtdOutrasM->count();
            $qtdReligiaoNIH = $qtdReligiaoNIH->count();
            $qtdReligiaoNIM = $qtdReligiaoNIM->count();
        } else {
            $qtdAdventistaH =
            $qtdAdventistaM =
            $qtdBudistaH =
            $qtdBudistaM =
            $qtdCatolicaH =
            $qtdCatolicaM =
            $qtdEspiritaH =
            $qtdEspiritaM =
            $qtdEvangelicaH =
            $qtdEvangelicaM =
            $qtdJudeuH =
            $qtdJudeuM =
            $qtdMormonH =
            $qtdMormonM =
            $qtdMulcumanaH =
            $qtdMulcumanaM =
            $qtdProtestanteH =
            $qtdProtestanteM =
            $qtdAgnosticoH =
            $qtdAgnosticoM =
            $qtdAteuH =
            $qtdAteuM =
            $qtdSemReligiaoH =
            $qtdSemReligiaoM =
            $qtdOutrasH =
            $qtdOutrasM =
            $qtdReligiaoNIH =
            $qtdReligiaoNIM = 0;
        }

        //saida da unidade
        $qtdAlvara = $qtdAlvara->count();
        $qtdFuga = $qtdFuga->count();
        $qtdRecambiamento = $qtdRecambiamento->count();
        $qtdSaidaJudicial = $qtdSaidaJudicial->count();
        $qtdExtradicao = $qtdExtradicao->count();
        $qtdLivramento = $qtdLivramento->count();
        $qtdObito = $qtdObito->count();
        $qtdExtincaopunibilidade = $qtdExtincaopunibilidade->count();

        /*************************************************************
         * CRIMES
         * ***********************************************************
         */
        
        if($crimes){
             
            $unidadePost = $request->get('unidade_prisional');
            $pavilhaoPost = $request->get('pavilhao');
            $alaPost = $request->get('ala');
            $celaPost = $request->get('cela');

            $dadosGrupos = DB::table('interno_processos_dispositivo_legal')
            ->leftJoin('interno_processos', 'interno_processos.id', '=', 'interno_processos_dispositivo_legal.id_processo')
            ->where('interno_processos.status','A')
            ->select('interno_processos_dispositivo_legal.fk_grupo')
            ->distinct('fk_grupo')
            ->get();

            $grupos = array();

            $dadosInternosDB = DB::table('interno_processos_dispositivo_legal')
            ->leftJoin('interno_processos', 'interno_processos.id', '=', 'interno_processos_dispositivo_legal.id_processo')
            ->leftJoin('interno', 'interno.id', '=', 'interno_processos.fk_interno');
            $dadosInternosDB->where('interno_processos.status','A');

             if(!is_null($unidadePost))
             {
                 $dadosInternosDB->where('unidade_prisional', $unidadePost);
             }
             if(!is_null($pavilhaoPost))
             {
                 $dadosInternosDB->where('pavilhao', $pavilhaoPost);
             }
             if(!is_null($alaPost))
             {
                 $dadosInternosDB->where('ala', $alaPost);
             }
             if(!is_null($celaPost))
             {
                 $dadosInternosDB->where('cela', $celaPost);
             }
             $dadosInternosDB->select('interno_processos_dispositivo_legal.*','interno_processos.*','interno.unidade_prisional','interno.pavilhao','interno.ala','interno.cela');

            $dadosInternos = $dadosInternosDB->get();

            $internos = array();
            $qtdF = 0;
            $qtdM = 0;

            foreach($dadosGrupos as $dadosGrupo)
            {
                array_push($grupos, array(
                    'id'=> $dadosGrupo->fk_grupo,
                    'nome'=> ArtigosProcessosController::getNomeGrupo($dadosGrupo->fk_grupo),
                        ));

                foreach($dadosInternos as $dadosInterno)
                {
                    if($dadosGrupo->fk_grupo == $dadosInterno->fk_grupo)
                    {

                        $internoDados = InternoController::getDadosInterno($dadosInterno->fk_interno)[0];

                        if($internoDados->status == "A"){

                            if($internoDados->sexo == "M")
                            {
                                $qtdM = $qtdM + 1;
                            }

                            if($internoDados->sexo == "F")
                            {
                                $qtdF = $qtdF + 1;
                            }

                        }

                    }

                }

                    array_push($internos, array(
                        'grupo' => $dadosGrupo->fk_grupo,
                        'qtdM' => $qtdM,
                        'qtdF' => $qtdF,
                    ));

                    $qtdM = 0;
                    $qtdF = 0;

            }
        }
        
        /**
         * fim das estatisticas de crimes
         */

        
        $dataHora = Tdata::TtimeStampHojeBrTxt();

        $qntReingresso = $this->getReigressoUnidade(TData::TdataMysql($request->get('data')));

        //$this->idadeVisita();



        $pdf = \PDF::loadView('estatisticas.pdfs.pdfInterno',  compact('crimes','grupos','internos','unidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome','faccoes','qtdFaccaoNIH','qtdFaccaoNIM',
            'qtdInternosLotSelecionada','qtdRegimeFechadoH', 'qtdRegimeSemiabertoH', 'qtdRegimeAbertoH','qtdRegimeNIH', 'qtdRegimeNIM', 'dataHora',
            'qtdRegimeFechadoM', 'qtdRegimeSemiabertoM', 'qtdRegimeAbertoM','qtdMedidaDeSegurancaH', 'qtdMedidaDeSegurancaM',
            'qtdSentenciadosH','qtdProvisoriosH', 'qtdSentenciadosNIH', 'qtdSentenciadosM','qtdProvisoriosM', 'qtdSentenciadosNIM', 'qtdHA', 'qtdMA', 'qtdSexoNI',
            'faixaEtaria_naoInformada','faixaEtaria_zero_a_dezessete','faixaEtaria_dezoito_a_vinteequatro','faixaEtaria_vinteecinco_a_vinteenove',
            'faixaEtaria_trinta_a_trintaequatro','faixaEtaria_trintaecinco_a_quarentaecinco','faixaEtaria_quarentaeseis_a_sessenta',
            'faixaEtaria_sessentaeum_a_setenta','faixaEtaria_maisDeSetenta',
            'faixaEtaria_naoInformadaH','faixaEtaria_zero_a_dezesseteH','faixaEtaria_dezoito_a_vinteequatroH','faixaEtaria_vinteecinco_a_vinteenoveH',
            'faixaEtaria_trinta_a_trintaequatroH','faixaEtaria_trintaecinco_a_quarentaecincoH','faixaEtaria_quarentaeseis_a_sessentaH',
            'faixaEtaria_sessentaeum_a_setentaH','faixaEtaria_maisDeSetentaH',
            'faixaEtaria_naoInformadaM','faixaEtaria_zero_a_dezesseteM','faixaEtaria_dezoito_a_vinteequatroM','faixaEtaria_vinteecinco_a_vinteenoveM',
            'faixaEtaria_trinta_a_trintaequatroM','faixaEtaria_trintaecinco_a_quarentaecincoM','faixaEtaria_quarentaeseis_a_sessentaM',
            'faixaEtaria_sessentaeum_a_setentaM','faixaEtaria_maisDeSetentaM','periculosidadeAltaH','periculosidadeMediaH','periculosidadeBaixaH','periculosidadeNIH',
            'periculosidadeAltaM','periculosidadeMediaM','periculosidadeBaixaM','periculosidadeNIM','qtdLiderancaSimH','qtdLiderancaNaoH','qtdLiderancaNIH',
            'qtdLiderancaSimM','qtdLiderancaNaoM','qtdLiderancaNIM', 'nacionalidades', 'qtdNacionalidadeNIH', 'qtdNacionalidadeNIM', 'qtdUnidadeHA', 'qtdUnidadeMA', 'qtdUnidadeTotal',

            'qtdSentenciadosMSNIH',
            'qtdSentenciadosMSNIM',
            'qtdSentenciadosMSH',
            'qtdSentenciadosMSM',
            'qtdProvisoriosMSH',
            'qtdProvisoriosMSM',

            //Filtros
            'internosunidade', 'regimeunidade', 'sexo', 'regimeprisional', 'sentprov_sem_ms', 'sentprov_em_ms', 'sentprov_geral', 'medidaseg', 'faixaetaria',
            'nacionalidade_filtro', 'estadocivil', 'escolaridade', 'corpele', 'orientacaosexual', 'saidaunidade' , 'religiao', 'setic',

            //estado_civil
            'qtdSolteirosH','qtdSolteirosM','qtdCasadosH', 'qtdCasadosM', 'qtdUniaoEstavelH', 'qtdUniaoEstavelM', 'qtdSeparadosH', 'qtdSeparadosM',
            'qtdDivorciadosH', 'qtdDivorciadosM', 'qtdViuvosH', 'qtdViuvosM', 'qtdEstadoCivilNIH', 'qtdEstadoCivilNIM',

            //etnia
            'qtdEtniaBrancaH', 'qtdEtniaBrancaM', 'qtdEtniaIndígenaH', 'qtdEtniaIndígenaM', 'qtdEtniaPretaH', 'qtdEtniaPretaM', 'qtdEtniaPardaH',
            'qtdEtniaPardaM', 'qtdEtniaAmarelaH', 'qtdEtniaAmarelaM', 'qtdEtniaNIH', 'qtdEtniaNIM',

            //opcao_sexual
            'qtdHeteroH', 'qtdHeteroM', 'qtdHomoH', 'qtdHomoM', 'qtdBiH', 'qtdBiM', 'qtdSexualidadeNaoDeclH', 'qtdSexualidadeNaoDeclM',
            'qtdSexualidadeNIH', 'qtdSexualidadeNIM',

            // escolaridade
            'qtdAnalfabetoH', 'qtdAnalfabetoM', 'qtdAlfabetizadoH', 'qtdAlfabetizadoM', 'qtdFundIncH', 'qtdFundIncM', 'qtdFundCompH', 'qtdFundCompM',
            'qtdMedioIncompH', 'qtdMedioIncompM', 'qtdMedioCompH', 'qtdMedioCompM', 'qtdSuperiorIncompH', 'qtdSuperiorIncompM', 'qtdSuperiorCompH',
            'qtdSuperiorCompM', 'qtdPosLatoSensuH', 'qtdPosLatoSensuM', 'qtdMestradoH', 'qtdMestradoM', 'qtdDoutoradoH', 'qtdDoutoradoM',
            'qtdPosDoutoradoH', 'qtdPosDoutoradoM', 'qtdEscolaridadeNIH', 'qtdEscolaridadeNIM',

            //religião
            'qtdAdventistaH', 'qtdAdventistaM', 'qtdBudistaH', 'qtdBudistaM', 'qtdCatolicaH', 'qtdCatolicaM', 'qtdEspiritaH', 'qtdEspiritaM', 'qtdEvangelicaH',
            'qtdEvangelicaM', 'qtdJudeuH', 'qtdJudeuM', 'qtdMormonH', 'qtdMormonM', 'qtdMulcumanaH', 'qtdMulcumanaM', 'qtdProtestanteH', 'qtdProtestanteM',
            'qtdAgnosticoH', 'qtdAgnosticoM', 'qtdAteuH', 'qtdAteuM', 'qtdSemReligiaoH', 'qtdSemReligiaoM', 'qtdOutrasH', 'qtdOutrasM', 'qtdReligiaoNIH', 'qtdReligiaoNIM',

            //saida da unidade
            'qtdAlvara', 'qtdFuga', 'qtdRecambiamento', 'qtdSaidaJudicial', 'qtdExtradicao', 'qtdLivramento', 'qtdObito', 'qtdExtincaopunibilidade', 'qntReingresso', 'detalhesUnidades'

        ))
            ->setOption('encoding', 'UTF-8')
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 30)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('header-html', view('template.header.headerEstatistica'))
            ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');
        return $pdf->inline();

    }

    public function entradasSaidasDatas($data)
    {

        $countSaidas = 0;
        $countEntradas = 0;
        $countProgressaoAberto = 0;
        $countProgressaoSemiaberto = 0;

        $selectSaidas = DB::table("movimentacao_interno")
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->leftJoin('unidade', 'movimentacao_interno.up_anterior', '=', 'unidade.id')
            ->leftJoin('interno', 'movimentacao_interno.fk_interno', '=', 'interno.id')
            ->whereYear("data", Tdata::getAnoData($data))
            ->whereMonth("data", Tdata::getMesData($data))
            ->whereDay("data", Tdata::getDiaData($data))
            ->where(function ($query) {
                $query->where("movimentacao.tipo", "=", "alvara")
                        ->orWhere("movimentacao.tipo", "=", "obito")
                        ->orWhere("movimentacao.tipo", "=", "recambiamento")
                        ->orWhere("movimentacao.tipo", "=", "saidajudicial")
                        //->orWhere("movimentacao.tipo", "=", "fuga")
                        ->orWhere("movimentacao.tipo", "=", "livramentocondicional")
                        ->orWhere("movimentacao.tipo", "=", "extincaopunibilidade")
                        ->orWhere("movimentacao.tipo", "=", "prisaodomiciliar")
                        ->orWhere("movimentacao.tipo", "=", "extradicao");
            })
            ->where("unidade.status", 1)
            ->where("movimentacao_interno.status", "Finalizada")
            ->select("movimentacao.*", "movimentacao_interno.*","unidade.*","interno.sexo")
            ->get();


        $saidas = array();
        //dd($selectSaidas);
        foreach($selectSaidas as $saida)
        {

            $countSaidas = $countSaidas + 1;

            if($saida->nome == "COMPLEXO PENAL JOÃO CHAVES")
            {
                if($saida->sexo == "M")
                {
                    $unidade = "COMPLEXO PENAL JOÃO CHAVES - MASC";
                }
                if($saida->sexo == "F")
                {
                    $unidade = "COMPLEXO PENAL JOÃO CHAVES - FEM";
                }
            }else{
                $unidade = $saida->nome;
            }

            array_push($saidas, array(
                "unidade" => $unidade,
                "tipo" => "S",
                "tipoNome" => $saida->tipo,
            ));
        }

        $selectSaidasFuga = DB::table("movimentacao")
            ->leftJoin('movimentacao_interno', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->leftJoin('unidade', 'movimentacao_interno.up_anterior', '=', 'unidade.id')
            ->leftJoin('interno', 'movimentacao_interno.fk_interno', '=', 'interno.id')
            ->whereYear("data", Tdata::getAnoData($data))
            ->whereMonth("data", Tdata::getMesData($data))
            ->whereDay("data", Tdata::getDiaData($data))
            ->where("movimentacao.tipo", "=", "fuga")
            ->where("interno.regime", "!=", "SEMIABERTO")
            ->where("interno.regime", "!=", "ABERTO")
            ->where("unidade.status", 1)
            ->where("movimentacao_interno.status", "Finalizada")
            ->select("movimentacao.*", "movimentacao_interno.*","unidade.*","interno.sexo")
            ->get();

        foreach($selectSaidasFuga as $saidaf)
        {

            $countSaidas = $countSaidas + 1;

            if($saidaf->nome == "COMPLEXO PENAL JOÃO CHAVES")
            {
                if($saidaf->sexo == "M")
                {
                    $unidade = "COMPLEXO PENAL JOÃO CHAVES - MASC";
                }
                if($saidaf->sexo == "F")
                {
                    $unidade = "COMPLEXO PENAL JOÃO CHAVES - FEM";
                }
            }else{
                $unidade = $saidaf->nome;
            }

            array_push($saidas, array(
                "unidade" => $unidade,
                "tipo" => "S",
                "tipoNome" => $saidaf->tipo,
            ));
        }

        $selectProgressaoSemiaberto = DB::table("movimentacao_interno")
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->leftJoin('unidade', 'movimentacao_interno.up_anterior', '=', 'unidade.id')
            ->leftJoin('interno', 'movimentacao_interno.fk_interno', '=', 'interno.id')
            ->whereYear("data", Tdata::getAnoData($data))
            ->whereMonth("data", Tdata::getMesData($data))
            ->whereDay("data", Tdata::getDiaData($data))
            ->where("movimentacao.tipo", "=", "progressaosemiaberto")
            ->where("unidade.status", 1)
            ->where("movimentacao_interno.status", "Finalizada")
            ->select("movimentacao.*", "movimentacao_interno.*","unidade.*","interno.sexo")
            ->get();


        foreach($selectProgressaoSemiaberto as $semiaberto)
        {
            $countProgressaoSemiaberto = $countProgressaoSemiaberto + 1;

            if($semiaberto->nome == "COMPLEXO PENAL JOÃO CHAVES")
            {
                if($semiaberto->sexo == "M")
                {
                    $unidadeSA = "COMPLEXO PENAL JOÃO CHAVES - MASC";
                }
                if($semiaberto->sexo == "F")
                {
                    $unidadeSA = "COMPLEXO PENAL JOÃO CHAVES - FEM";
                }
            }else{
                $unidadeSA = $semiaberto->nome;
            }

            array_push($saidas, array(
                "unidade" => $unidadeSA,
                "tipo" => "SA",
                "tipoNome" => $semiaberto->tipo,
            ));
        }


        $selectProgressaoAberto = DB::table("movimentacao_interno")
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->leftJoin('unidade', 'movimentacao_interno.up_anterior', '=', 'unidade.id')
            ->leftJoin('interno', 'movimentacao_interno.fk_interno', '=', 'interno.id')
            ->whereYear("data", Tdata::getAnoData($data))
            ->whereMonth("data", Tdata::getMesData($data))
            ->whereDay("data", Tdata::getDiaData($data))
            ->where("movimentacao.tipo", "=", "progressaoaberto")
            ->where("unidade.status", 1)
            ->where("movimentacao_interno.status", "Finalizada")
            ->select("movimentacao.*", "movimentacao_interno.*","unidade.*","interno.sexo")
            ->get();


        foreach($selectProgressaoAberto as $aberto)
        {
            $countProgressaoAberto = $countProgressaoAberto + 1;

            if($aberto->nome == "COMPLEXO PENAL JOÃO CHAVES")
            {
                if($aberto->sexo == "M")
                {
                    $unidadeA = "COMPLEXO PENAL JOÃO CHAVES - MASC";
                }
                if($aberto->sexo == "F")
                {
                    $unidadeA = "COMPLEXO PENAL JOÃO CHAVES - FEM";
                }
            }else{
                $unidadeA = $aberto->nome;
            }

            array_push($saidas, array(
                "unidade" => $unidadeA,
                "tipo" => "A",
                "tipoNome" => $aberto->tipo,
            ));
        }



        $entradas = DB::table("movimentacao_interno")
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->leftJoin('unidade', 'movimentacao.unidade_destino', '=', 'unidade.id')
            ->leftJoin('interno', 'movimentacao_interno.fk_interno', '=', 'interno.id')
            ->whereYear("data", Tdata::getAnoData($data))
            ->whereMonth("data", Tdata::getMesData($data))
            ->whereDay("data", Tdata::getDiaData($data))
            ->where("movimentacao.tipo", "=", "entradaunidadeprisional")
            ->where("unidade.status", 1)
            ->where("movimentacao_interno.status", "Finalizada")
            ->select("movimentacao.*", "movimentacao_interno.*","unidade.*","interno.sexo")
            ->get();

        foreach($entradas as $entrada)
        {
            $countEntradas = $countEntradas + 1;

            if($entrada->nome == "COMPLEXO PENAL JOÃO CHAVES")
            {
                if($entrada->sexo == "M")
                {
                    $unidadeE = "COMPLEXO PENAL JOÃO CHAVES - MASC";
                }
                if($entrada->sexo == "F")
                {
                    $unidadeE = "COMPLEXO PENAL JOÃO CHAVES - FEM";
                }
            }else{
                $unidadeE = $entrada->nome;
            }

            array_push($saidas, array(
                "unidade" => $unidadeE,
                "tipo" => "E",
                "tipoNome" => $entrada->tipo,
            ));
        }


        $sort = [
            ['column' => 'unidade', 'order' => 'asc'],

        ];

        // dd($internos);

        $saidas = collect($saidas);
        $saidas = self::multiPropertySort($saidas, $sort);
        $saidas = json_decode($saidas, true);

        $dados = array();
        foreach($saidas as $array){
            $dados[$array["unidade"]][$array["tipo"]][] = $array;
        }


        $dataBr = Tdata::TdataBR($data);

        $unidadesDb = DB::table("unidade")->where('status', "1")->orderBy("nome", "ASC")->select("nome")->get();
        $unidades = array();
        foreach($unidadesDb as $unidadeDd)
        {
            if($unidadeDd->nome != "COMPLEXO PENAL JOÃO CHAVES")
            {
                if($unidadeDd->nome == "COMPLEXO PENAL ESTADUAL AGRÍCOLA MÁRIO NEGÓCIO")
                {
                    array_push($unidades, $unidadeDd->nome);
                    array_push($unidades, "COMPLEXO PENAL JOÃO CHAVES - FEM");
                    array_push($unidades, "COMPLEXO PENAL JOÃO CHAVES - MASC");
                }else{
                    array_push($unidades, $unidadeDd->nome);
                }

            }
        }
        //$unidades = json_decode($unidades, true);

        //dd($dados,$unidades);

        $pdf = \PDF::loadView('estatisticas.entradaSaida.pdfEntradaSaida', compact('unidades','dados','dataBr','countSaidas','countEntradas','countProgressaoAberto','countProgressaoSemiaberto'))
            ->setOption('orientation', 'landscape')
            ->setOption('margin-bottom', 30)
            ->setOption('margin-top', 40)
            ->setOption('margin-left', 5)
            ->setOption('margin-right', 5)
            ->setOption('header-html', view('template.header.headerEstatisticasEntradaSaida'))
            ->setOption ('footer-center', utf8_decode('Gerado em'.date('\ d/m/Y\ ').' às '.date('H:i')))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');

        return $pdf->inline();


    }

    public function showUnidade(Request $request)
    {
        $mesEstatistica = $request->get('mes');
        $diaEstatistica = $request->get('data');
        $diaShow = null;
        $mesShow = null;

        if(!is_null($diaEstatistica)){
            $diaShow = 1;
            $dadosAgendamentoDia = DB::table('advogado_agendamento')->where('data', Tdata::TdataMysql($diaEstatistica))->get();
        }else{
            $diaEstatistica = Tdata::TdataHoje();
            $dadosAgendamentoDia = DB::table('advogado_agendamento')->where('data', Tdata::TdataHojeMysql())->get();

        }
        $agendamentoDiaArray = array();
        foreach($dadosAgendamentoDia as $dAgendamentoDia)
        {
            array_push($agendamentoDiaArray, array(
                "unidadeNome" => UnidadesController::getNomeUp($dAgendamentoDia->unidade),
                "unidadeId" => $dAgendamentoDia->unidade,
                "tipo" => $dAgendamentoDia->status,
            ));

        }

        $sort = [
            ['column' => 'unidadeNome', 'order' => 'asc'],

        ];

        // dd($internos);

        $agendamentoDiaArray = collect($agendamentoDiaArray);
        $agendamentoDiaArray = self::multiPropertySort($agendamentoDiaArray, $sort);
        $agendamentoDiaArray = json_decode($agendamentoDiaArray, true);

        $agendamentoDia = array();
        foreach($agendamentoDiaArray as $array){
            $agendamentoDia[$array["unidadeNome"]][$array["tipo"]][] = $array;
        }


        if(!is_null($mesEstatistica)){
            $mesShow = 1;
            $mesNome = Tdata::getMesExt($mesEstatistica);
            $dadosAgendamentoMes = DB::table('advogado_agendamento')->whereMonth('data', $mesEstatistica)->whereYear('data', date("Y"))->get();
        }else{
            $mesNome = Tdata::getMesExt(Tdata::mes());
            $dadosAgendamentoMes = DB::table('advogado_agendamento')->whereMonth('data', Tdata::getMesData(Tdata::TdataHojeMysql()))->get();
        }
        $agendamentoMesArray = array();
        foreach($dadosAgendamentoMes as $dAgendamentoMes)
        {
            array_push($agendamentoMesArray, array(
                "unidadeNome" => UnidadesController::getNomeUp($dAgendamentoMes->unidade),
                "unidadeId" => $dAgendamentoMes->unidade,
                "tipo" => $dAgendamentoMes->status,
            ));

        }

        $sort = [
            ['column' => 'unidadeNome', 'order' => 'asc'],

        ];

        $agendamentoMesArray = collect($agendamentoMesArray);
        $agendamentoMesArray = self::multiPropertySort($agendamentoMesArray, $sort);
        $agendamentoMesArray = json_decode($agendamentoMesArray, true);

        $agendamentoMes = array();
        foreach($agendamentoMesArray as $array){
            $agendamentoMes[$array["unidadeNome"]][$array["tipo"]][] = $array;
        }



        $unidadePrisional = DB::table("unidade")->where('status', "1")->select("nome")->orderBy("nome", "ASC")->get();
        $unidades = array();
        foreach($unidadePrisional as $up)
        {
            array_push($unidades, $up->nome);
        }

        $mesesEscala = array();

        for($i = 1; $i <= 12; $i++)
        {
            array_push($mesesEscala, array(
                'mesNum' => $i,
                'mesNome' => Tdata::getMesExt($i),
            ));
        }

        $diaEstatisticaMysql = Tdata::TdataMysql($diaEstatistica);
        if($mesEstatistica == null)
        {
            $mesEstatistica = date("m");
        }
        return view('estatisticas.unidade.unidade', compact('unidades','agendamentoDia','agendamentoMes','mesNome','mesEstatistica','mesesEscala','diaEstatistica','diaEstatisticaMysql','mesShow','diaShow'));

    }

    public function showCoeap(Request $request)
    {
        $totalqntEntradas = 0;
        $totalqntSaidas = 0;
        $totalqntObtos = 0;
        $totalqntAlvara = 0;
        $totalqntFugaFech = 0;
        $totalqntFugaSemi = 0;
        $totalqntFugaAberto = 0;
        $totalqntFugaProv = 0;

        $unidade = $request->get('unidade_prisional');
        if (!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        else $unidadeSigla = null;
        $pavilhao = $request->get('pavilhao');
        if (!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
        else $pavilhaoNome = null;
        $ala = $request->get('ala');
        if (!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
        else $alaNome = null;
        $cela = $request->get('cela');
        if (!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
        else $celaNome = null;
        if (!is_null($request->get('dataini'))){

            $dataini = TData::TdataMysql($request->get('dataini'));

        } else $dataini = null;

        if (!is_null($request->get('datafin'))){

            $datafin = $request->get('datafin');

            $datafin = TData::TdataMysql($datafin);

            $datafin = TData::addDias($datafin, 1);

            //dd($datafin);
        } else $datafin = null;

        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status', '1')->get();

        $qtdInternosLotSelecionada = DB::table('interno')->where('status', 'A');

        $datai = mktime(00,00,00,02,19,2019);

        if($dataini != null){


            if (preg_match('/^[0-9]{4}[-][0-9]{1,2}[-][0-9]{1,2}$/', $dataini) && preg_match('/^[0-9]{4}[-][0-9]{1,2}[-][0-9]{1,2}$/', $datafin)) {

                if ($dataini < date("Y-m-d", $datai)) {

                    $msg = redirect()->back()->withInput()->with('alert-danger', 'Não foi possível atender a solicitação. Digite uma data inicial a partir de 19/02/2019.');
                    $dataHora = Tdata::TtimeStampHojeBrTxt();

                    return view('estatisticas.show.estatisticas-coeap', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
                        'qtdInternosLotSelecionada', 'dataini', 'msg', 'datafin'));

                }else {

                    $totalqntEntradas = 0;
                    $totalqntSaidas = 0;
                    $totalqntObtos = 0;
                    $totalqntAlvara = 0;
                    $totalqntFugaFech = 0;
                    $totalqntFugaSemi = 0;
                    $totalqntFugaAberto = 0;
                    $totalqntFugaProv = 0;

                    foreach($unidades AS $unit) {


                            $unit->qntEntradas = DB::table('movimentacao')
                            ->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id')
                            ->leftJoin('unidade', 'movimentacao.unidade_destino', 'unidade.id')
                            ->where('movimentacao.unidade_destino', '=', $unit->id)
                            ->where('movimentacao.tipo', 'entradaunidadeprisional')
                            ->where('movimentacao_interno.status', 'Finalizada')
                            ->where('movimentacao.data', '>', $dataini)
                            ->where('movimentacao.data', '<', $datafin)->count();


                            $unit->qntSaidas = DB::table('movimentacao')
                            ->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id')
                            ->leftJoin('unidade', 'movimentacao_interno.up_anterior', 'unidade.id')
                            ->where('movimentacao_interno.up_anterior', $unit->id)
                            ->where('movimentacao_interno.status', 'Finalizada')
                            ->where('movimentacao.data', '>', $dataini)
                            ->where('movimentacao.data', '<', $datafin)
                            ->where(function ($query) {
                                $query->where('movimentacao.tipo', 'alvara')
                                    ->orwhere('movimentacao.tipo', 'obito')
                                    ->orwhere('movimentacao.tipo', 'recambiamento')
                                    ->orwhere('movimentacao.tipo', 'saidajudicial')
                                    ->orwhere('movimentacao.tipo', 'fuga')
                                    ->orwhere('movimentacao.tipo', 'livramentocondicional')
                                    ->orwhere('movimentacao.tipo', 'extincaopunibilidade')
                                    ->orwhere('movimentacao.tipo', 'prisaodomiciliar')
                                    ->orwhere('movimentacao.tipo', 'extradicao');
                            })->count();

                            $unit->qntObtos = DB::table('movimentacao')
                                ->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id')
                                ->leftJoin('unidade', 'movimentacao_interno.up_anterior', 'unidade.id')
                                ->where('movimentacao_interno.up_anterior', $unit->id)
                                ->where('movimentacao_interno.status', 'Finalizada')
                                ->where('movimentacao.data', '>', $dataini)
                                ->where('movimentacao.data', '<', $datafin)
                                ->where(function ($query) {
                                    $query->where('movimentacao.tipo', 'obito');
                            })->count();

                            $unit->qntAlvara = DB::table('movimentacao')
                                ->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id')
                                ->leftJoin('unidade', 'movimentacao_interno.up_anterior', 'unidade.id')
                                ->where('movimentacao_interno.up_anterior', $unit->id)
                                ->where('movimentacao_interno.status', 'Finalizada')
                                ->where('movimentacao.data', '>', $dataini)
                                ->where('movimentacao.data', '<', $datafin)
                                ->where(function ($query) {
                                    $query->where('movimentacao.tipo', 'alvara');
                            })->count();

                            $unit->qntFugaFech = DB::table('movimentacao')
                                ->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id')
                                ->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id')
                                ->leftJoin('unidade', 'movimentacao_interno.up_anterior', 'unidade.id')
                                ->where('interno.regime', 'FECHADO')
                                ->where('movimentacao_interno.up_anterior', $unit->id)
                                ->where('movimentacao.tipo', 'Fuga')
                                ->where('movimentacao_interno.status', 'Finalizada')
                                ->where('movimentacao.data', '>', $dataini)
                                ->where('movimentacao.data', '<', $datafin)
                                ->count();

                            $unit->qntFugaSemi = DB::table('movimentacao')
                                ->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id')
                                ->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id')
                                ->leftJoin('unidade', 'movimentacao_interno.up_anterior', 'unidade.id')
                                ->where('interno.regime', 'SEMIABERTO')
                                ->where('movimentacao_interno.up_anterior', $unit->id)
                                ->where('movimentacao.tipo', 'Fuga')
                                ->where('movimentacao_interno.status', 'Finalizada')
                                ->where('movimentacao.data', '>', $dataini)
                                ->where('movimentacao.data', '<', $datafin)
                                ->count();

                            $unit->qntFugaAberto = DB::table('movimentacao')
                                ->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id')
                                ->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id')
                                ->leftJoin('unidade', 'movimentacao_interno.up_anterior', 'unidade.id')
                                ->where('interno.regime', 'ABERTO')
                                ->where('movimentacao_interno.up_anterior', $unit->id)
                                ->where('movimentacao.tipo', 'Fuga')
                                ->where('movimentacao_interno.status', 'Finalizada')
                                ->where('movimentacao.data', '>', $dataini)
                                ->where('movimentacao.data', '<', $datafin)
                                ->count();

                            $unit->qntFugaProv = DB::table('movimentacao')
                                ->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id')
                                ->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id')
                                ->leftJoin('unidade', 'movimentacao_interno.up_anterior', 'unidade.id')
                                ->where('interno.sentenciado', 'N')
                                ->where('movimentacao_interno.up_anterior', $unit->id)
                                ->where('movimentacao.tipo', 'Fuga')
                                ->where('movimentacao_interno.status', 'Finalizada')
                                ->where('movimentacao.data', '>', $dataini)
                                ->where('movimentacao.data', '<', $datafin)
                                ->count();


                        $totalqntEntradas += $unit->qntEntradas;
                        $totalqntSaidas += $unit->qntSaidas;
                        $totalqntObtos += $unit->qntObtos;
                        $totalqntAlvara += $unit->qntAlvara;
                        $totalqntFugaFech += $unit->qntFugaFech;
                        $totalqntFugaSemi += $unit->qntFugaSemi;
                        $totalqntFugaAberto += $unit->qntFugaAberto;
                        $totalqntFugaProv += $unit->qntFugaProv;




                    }

                    /*$qntEntradas = $qntEntradas->count();
                    $qntSaidas = $qntSaidas->count();
                    $proSemiaberto = $proSemiaberto->count();
                    $proAberto = $proAberto->count();*/

                }
            }else {

                $msg = redirect()->back()->withInput()->with('alert-danger', 'Verifique as datas digitadas. Digite datas válidas. Ex.: 00/00/0000');
                $dataHora = Tdata::TtimeStampHojeBrTxt();
                $qntEntradas = "-";
                $qntSaidas = "-";

                return view('estatisticas.show.estatisticas-coeap', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
                    'qtdInternosLotSelecionada', 'dataini', 'msg', 'datafin'));

            }
        }else {


            /*$qntEntradas = "-";
            $qntSaidas = "-";
            $proSemiaberto = "-";
            $proAberto = "-";*/



        }


        $dataHora = Tdata::TtimeStampHojeBrTxt();



        return view('estatisticas.show.estatisticas-coeap', compact('dataHora', 'unidades', 'detalhesUnidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome',
            'qtdInternosLotSelecionada', 'dataini','datafin','totalqntEntradas','totalqntSaidas','totalqntObtos','totalqntAlvara','totalqntFugaFech','totalqntFugaSemi',
            'totalqntFugaAberto','totalqntFugaProv'));

    }



    public function showUnidadeDiaPdf($data)
    {

        $diaEstatistica = Tdata::TdataBR($data);
        $diaShow = null;
        $mesShow = null;

        if(!is_null($diaEstatistica)){
            $diaShow = 1;
            $dadosAgendamentoDia = DB::table('advogado_agendamento')->where('data', Tdata::TdataMysql($diaEstatistica))->get();
        }else{
            $diaEstatistica = Tdata::TdataHoje();
            $dadosAgendamentoDia = DB::table('advogado_agendamento')->where('data', Tdata::TdataHojeMysql())->get();

        }
        $agendamentoDiaArray = array();
        foreach($dadosAgendamentoDia as $dAgendamentoDia)
        {
            array_push($agendamentoDiaArray, array(
                "unidadeNome" => UnidadesController::getNomeUp($dAgendamentoDia->unidade),
                "unidadeId" => $dAgendamentoDia->unidade,
                "tipo" => $dAgendamentoDia->status,
            ));

        }

        $sort = [
            ['column' => 'unidadeNome', 'order' => 'asc'],

        ];

        // dd($internos);

        $agendamentoDiaArray = collect($agendamentoDiaArray);
        $agendamentoDiaArray = self::multiPropertySort($agendamentoDiaArray, $sort);
        $agendamentoDiaArray = json_decode($agendamentoDiaArray, true);

        $agendamentoDia = array();
        foreach($agendamentoDiaArray as $array){
            $agendamentoDia[$array["unidadeNome"]][$array["tipo"]][] = $array;
        }

        $unidadePrisional = DB::table("unidade")->where('status', "1")->select("nome","sigla")->orderBy("nome", "ASC")->get();
        $unidades = array();
        foreach($unidadePrisional as $up)
        {
            if($up->sigla != "CEME"){
                array_push($unidades, $up->nome);
            }

        }

        $pdf = \PDF::loadView('estatisticas.unidade.pdfDiaAgendamentoAdv', compact('unidades','agendamentoDia','diaEstatistica'))
            ->setOption('orientation', 'landscape')
            ->setOption('margin-bottom', 30)
            ->setOption('margin-top', 40)
            ->setOption('margin-left', 5)
            ->setOption('margin-right', 5)
            ->setOption('header-html', view('template.header.headerEstatisticasEntradaSaida'))
            ->setOption ('footer-center', utf8_decode('Gerado em'.date('\ d/m/Y\ ').' às '.date('H:i')))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');

        return $pdf->inline();

    }

    public function showUnidadeMesPdf($mes)
    {
        if(!is_null($mes)){
            $mesShow = 1;
            $mesNome = Tdata::getMesExt($mes);
            $dadosAgendamentoMes = DB::table('advogado_agendamento')->whereMonth('data', $mes)->whereYear('data', date("Y"))->get();
        }else{
            $mesNome = Tdata::getMesExt(Tdata::mes());
            $dadosAgendamentoMes = DB::table('advogado_agendamento')->whereMonth('data', Tdata::getMesData(Tdata::TdataHojeMysql()))->get();
        }
        $agendamentoMesArray = array();
        foreach($dadosAgendamentoMes as $dAgendamentoMes)
        {
            array_push($agendamentoMesArray, array(
                "unidadeNome" => UnidadesController::getNomeUp($dAgendamentoMes->unidade),
                "unidadeId" => $dAgendamentoMes->unidade,
                "tipo" => $dAgendamentoMes->status,
            ));

        }

        $sort = [
            ['column' => 'unidadeNome', 'order' => 'asc'],

        ];

        $agendamentoMesArray = collect($agendamentoMesArray);
        $agendamentoMesArray = self::multiPropertySort($agendamentoMesArray, $sort);
        $agendamentoMesArray = json_decode($agendamentoMesArray, true);

        $agendamentoMes = array();
        foreach($agendamentoMesArray as $array){
            $agendamentoMes[$array["unidadeNome"]][$array["tipo"]][] = $array;
        }



        $unidadePrisional = DB::table("unidade")->where('status', "1")->select("nome","sigla")->orderBy("nome", "ASC")->get();
        $unidades = array();
        foreach($unidadePrisional as $up)
        {
            if($up->sigla != "CEME"){
                array_push($unidades, $up->nome);
            }

        }

        $pdf = \PDF::loadView('estatisticas.unidade.pdfMesAgendamentoAdv', compact('unidades','agendamentoMes','mes'))
            ->setOption('orientation', 'landscape')
            ->setOption('margin-bottom', 30)
            ->setOption('margin-top', 40)
            ->setOption('margin-left', 5)
            ->setOption('margin-right', 5)
            ->setOption('header-html', view('template.header.headerEstatisticasEntradaSaida'))
            ->setOption ('footer-center', utf8_decode('Gerado em'.date('\ d/m/Y\ ').' às '.date('H:i')))
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

    public static function qtdSemMae($unidade, $sexo = ''){
        if($sexo != ''){
            return DB::table('interno')->whereNull('mae')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('mae', '')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count();
        }
        return DB::table('interno')->whereNull('mae')->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('mae','')->where('unidade_prisional',$unidade)->where('status','A')->count();
    }

    public static function qtdSemPai($unidade, $sexo = ''){
        if($sexo != ''){
            return DB::table('interno')->whereNull('pai')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('pai','')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count();
        }
        return DB::table('interno')->whereNull('pai')->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('pai','')->where('unidade_prisional',$unidade)->where('status','A')->count();
    }

    public static function qtdSemSexo($unidade){
        return DB::table('interno')->whereNull('sexo')->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('sexo','')->where('unidade_prisional',$unidade)->where('status','A')->count();
    }

    public static function qtdSemDataNascimento($unidade, $sexo = ''){
        if($sexo != ''){
            return DB::table('interno')->whereNull('data_nascimento')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('data_nascimento','')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count();
        }
        return DB::table('interno')->whereNull('data_nascimento')->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('data_nascimento','')->where('unidade_prisional',$unidade)->where('status','A')->count();
    }

    public static function qtdSemEstadoCivil($unidade, $sexo = ''){
        if($sexo != ''){
            return DB::table('interno')->whereNull('estado_civil')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('estado_civil','')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count();
        }
        return DB::table('interno')->whereNull('estado_civil')->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('estado_civil','')->where('unidade_prisional',$unidade)->where('status','A')->count();
    }

    public static function qtdSemNaturalidadePais($unidade, $sexo = ''){
        if($sexo != ''){
            return DB::table('interno')->whereNull('naturalidade_pais')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('naturalidade_pais','')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count();
        }
        return DB::table('interno')->whereNull('naturalidade_pais')->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('naturalidade_pais','')->where('unidade_prisional',$unidade)->where('status','A')->count();
    }

    public static function qtdSemNaturalidadeEstado($unidade, $sexo = ''){
        if($sexo != ''){
            return DB::table('interno')->whereNull('naturalidade_estado')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('naturalidade_estado','')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count();
        }
        return DB::table('interno')->whereNull('naturalidade_estado')->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('naturalidade_estado','')->where('unidade_prisional',$unidade)->where('status','A')->count();
    }

    public static function qtdSemNaturalidadeCidade($unidade, $sexo = ''){
        if($sexo != ''){
            return DB::table('interno')->whereNull('naturalidade_cidade')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('naturalidade_cidade','')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count();
        }
        return DB::table('interno')->whereNull('naturalidade_cidade')->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('naturalidade_cidade','')->where('unidade_prisional',$unidade)->where('status','A')->count();
    }

    public static function qtdSemOrientacaoSexual($unidade, $sexo = ''){
        if($sexo != ''){
            return DB::table('interno')->whereNull('opcao_sexual')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('opcao_sexual','')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count();
        }
        return DB::table('interno')->whereNull('opcao_sexual')->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('opcao_sexual','')->where('unidade_prisional',$unidade)->where('status','A')->count();
    }

    public static function qtdSemReligiao($unidade, $sexo = ''){
        if($sexo != ''){
            return DB::table('interno')->whereNull('religiao')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('religiao','')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count();
        }
        return DB::table('interno')->whereNull('religiao')->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('religiao','')->where('unidade_prisional',$unidade)->where('status','A')->count();
    }

    public static function qtdSemEscolaridade($unidade, $sexo = ''){
        if($sexo != ''){
            return DB::table('interno')->whereNull('escolaridade')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('escolaridade','')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count();
        }
        return DB::table('interno')->whereNull('escolaridade')->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('escolaridade','')->where('unidade_prisional',$unidade)->where('status','A')->count();
    }

    public static function qtdSemCorPele($unidade, $sexo = ''){
        if($sexo != ''){
            return DB::table('interno')->whereNull('etnia')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('etnia','')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count();
        }
        return DB::table('interno')->whereNull('etnia')->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('etnia','')->where('unidade_prisional',$unidade)->where('status','A')->count();
    }

    public static function qtdSemCorOlhos($unidade, $sexo = ''){
        if($sexo != ''){
            return DB::table('interno')->whereNull('cor_olhos')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('cor_olhos','')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count();
        }
        return DB::table('interno')->whereNull('cor_olhos')->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('cor_olhos','')->where('unidade_prisional',$unidade)->where('status','A')->count();
    }

    public static function qtdSemCorCabelos($unidade, $sexo = ''){
        if($sexo != ''){
            return DB::table('interno')->whereNull('cor_cabelos')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('cor_cabelos','')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count();
        }
        return DB::table('interno')->whereNull('cor_cabelos')->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('cor_cabelos','')->where('unidade_prisional',$unidade)->where('status','A')->count();
    }

    public static function qtdSemSentenciadoProvisorio($unidade, $sexo = ''){
        if($sexo != ''){
            return DB::table('interno')->whereNull('sentenciado')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('sentenciado','')->where('sexo', $sexo)->where('unidade_prisional',$unidade)->where('status','A')->count();
        }
        return DB::table('interno')->whereNull('sentenciado')->where('unidade_prisional',$unidade)->where('status','A')->count() + DB::table('interno')->where('sentenciado','')->where('unidade_prisional',$unidade)->where('status','A')->count();
    }

    public static function qtdSemFotos($unidade, $sexo = ''){
        if($sexo != ''){
            $qtd = 0;
            $internos = DB::table('interno')->where('sexo',$sexo)->where('unidade_prisional',$unidade)->where('status','A')->get();
            foreach($internos as $interno){
                if(DB::table('foto')
                ->where('fk_tabela','interno')
                ->where('fk_id',$interno->id)
                ->count() == 0){
                    $qtd++;
                }
            }
            return $qtd;
        }

        $qtd = 0;
        $internos = DB::table('interno')->where('unidade_prisional',$unidade)->where('status','A')->get();
        foreach($internos as $interno){
            if(DB::table('foto')
            ->where('fk_tabela','interno')
            ->where('fk_id',$interno->id)
            ->count() == 0){
                $qtd++;
            }
        }
        return $qtd;
    }

    ## NOVAS ESTATISTICAS ##
    
    ################################ calculos com datas retroativas e revisão ############################################
    public function internoPdfNew(Request $request) {
        // Dados gerais
        $unidadeEstatistica = $request->get('unidadeEstatistica');
        $dataEstatistica = $request->get('dataEstatistica');
        $tipo = $request->get('tipo');
        
        switch ($tipo){
            
            case "internoPorUnidade":
                
                $unidades = json_decode($request->get('unidades'), true);
                $internos = json_decode($request->get('internos'), true);

                $pdf = \PDF::loadView('estatisticas.new.pdf.internoPorUnidade',  compact('unidades','internos','dataEstatistica','unidadeEstatistica'))
                    ->setOption('encoding', 'UTF-8')
                    ->setOption('margin-bottom', 10)
                    ->setOption('margin-top', 30)
                    ->setOption('margin-left', 10)
                    ->setOption('margin-right', 10)
                    ->setOption('margin-bottom', 10)
                    ->setOption('header-html', view('template.header.headerEstatistica'))
                    ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                    ->setOption('footer-font-size', 7)
                    ->setPaper('a4');
                return $pdf->inline();
                
                break;
            
            case "internoRegimePorUnidade":
                
                $unidades = json_decode($request->get('unidades'));
                $internos = json_decode($request->get('internos'), true);
                $detalhesUnidades = json_decode($request->get('detalhesUnidades'));

                $pdf = \PDF::loadView('estatisticas.new.pdf.internoRegimePorUnidade',  compact('unidades','internos','detalhesUnidades','dataEstatistica','unidadeEstatistica'))
                    ->setOption('encoding', 'UTF-8')
                    ->setOption('margin-bottom', 10)
                    ->setOption('margin-top', 30)
                    ->setOption('margin-left', 10)
                    ->setOption('margin-right', 10)
                    ->setOption('margin-bottom', 10)
                    ->setOption('header-html', view('template.header.headerEstatistica'))
                    ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                    ->setOption('footer-font-size', 7)
                    ->setPaper('a4');
                return $pdf->inline();
                
                break;
            
            case "internoPorSexo":
                
                $internos = json_decode($request->get('internos'), true);

                $pdf = \PDF::loadView('estatisticas.new.pdf.internoPorSexo',  compact('internos','dataEstatistica','unidadeEstatistica'))
                    ->setOption('encoding', 'UTF-8')
                    ->setOption('margin-bottom', 10)
                    ->setOption('margin-top', 30)
                    ->setOption('margin-left', 10)
                    ->setOption('margin-right', 10)
                    ->setOption('margin-bottom', 10)
                    ->setOption('header-html', view('template.header.headerEstatistica'))
                    ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                    ->setOption('footer-font-size', 7)
                    ->setPaper('a4');
                return $pdf->inline();
                
                break;
            
            case "internoCrimeGrupo":
                
                $internos = json_decode($request->get('internos'), true);

                $pdf = \PDF::loadView('estatisticas.new.pdf.internoCrimeGrupo',  compact('internos','dataEstatistica','unidadeEstatistica'))
                    ->setOption('encoding', 'UTF-8')
                    ->setOption('margin-bottom', 10)
                    ->setOption('margin-top', 30)
                    ->setOption('margin-left', 10)
                    ->setOption('margin-right', 10)
                    ->setOption('margin-bottom', 10)
                    ->setOption('header-html', view('template.header.headerEstatistica'))
                    ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                    ->setOption('footer-font-size', 7)
                    ->setPaper('a4');
                return $pdf->inline();
                
                break;
            
            case "internoCrimesSisdepen":
                
                $internosCPB = json_decode($request->get('internosCPB'), true);
                $internosLeisExtra = json_decode($request->get('internosLeisExtra'), true);

                $pdf = \PDF::loadView('estatisticas.new.pdf.internoCrimesSisdepen',  compact('internosCPB','internosLeisExtra','dataEstatistica','unidadeEstatistica'))
                    ->setOption('encoding', 'UTF-8')
                    ->setOption('margin-bottom', 10)
                    ->setOption('margin-top', 30)
                    ->setOption('margin-left', 10)
                    ->setOption('margin-right', 10)
                    ->setOption('margin-bottom', 10)
                    ->setOption('header-html', view('template.header.headerEstatistica'))
                    ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                    ->setOption('footer-font-size', 7)
                    ->setPaper('a4');
                return $pdf->inline();
                
                break;
            
            case "internoRegimePorSexo":
                
                $internos = json_decode($request->get('internos'), true);
                $detalhesUnidades = json_decode($request->get('detalhesUnidades'));
                $qtdRegimeFechadoH = ($request->get('qtdRegimeFechadoH'));
                $qtdRegimeFechadoM = ($request->get('qtdRegimeFechadoM'));
                $qtdRegimeSemiabertoH = ($request->get('qtdRegimeSemiabertoH'));
                $qtdRegimeSemiabertoM = ($request->get('qtdRegimeSemiabertoM'));
                $qtdRegimeAbertoH = ($request->get('qtdRegimeAbertoH'));
                $qtdRegimeAbertoM = ($request->get('qtdRegimeAbertoM'));
                $qtdRegimeNIH = ($request->get('qtdRegimeNIH'));
                $qtdRegimeNIM = ($request->get('qtdRegimeNIM'));
                

                $pdf = \PDF::loadView('estatisticas.new.pdf.internoRegimePorSexo',  compact('internos','detalhesUnidades','qtdRegimeFechadoH','qtdRegimeFechadoM','qtdRegimeSemiabertoH',
                        'qtdRegimeSemiabertoM',
                        'qtdRegimeAbertoH',
                        'qtdRegimeSemiabertoM',
                        'qtdRegimeAbertoH',
                        'qtdRegimeAbertoM',
                        'qtdRegimeNIH',
                        'qtdRegimeNIM',
                        'dataEstatistica','unidadeEstatistica'))
                    ->setOption('encoding', 'UTF-8')
                    ->setOption('margin-bottom', 10)
                    ->setOption('margin-top', 30)
                    ->setOption('margin-left', 10)
                    ->setOption('margin-right', 10)
                    ->setOption('margin-bottom', 10)
                    ->setOption('header-html', view('template.header.headerEstatistica'))
                    ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                    ->setOption('footer-font-size', 7)
                    ->setPaper('a4');
                return $pdf->inline();
                
                break;
            
                case "internoSentenciadoProvisorioSMedSeg":

                    $internos = json_decode($request->get('internos'), true);
                    $detalhesUnidades = json_decode($request->get('detalhesUnidades'));
                    $qtdSentenciadoM = $request->get('qtdSentenciadoM');
                    $qtdSentenciadoF = $request->get('qtdSentenciadoF');
                    $qtdProvisorioM = $request->get('qtdProvisorioM');
                    $qtdProvisorioF = $request->get('qtdProvisorioF');
                    $qtdNdM = $request->get('qtdNdM');
                    $qtdNdF = $request->get('qtdNdF');

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoSentenciadoProvisorioSMedSeg',  compact('internos','dataEstatistica','detalhesUnidades','qtdSentenciadoM','qtdSentenciadoF',
                            'qtdProvisorioM',
                            'qtdProvisorioF',
                            'qtdNdM',
                            'qtdNdF',
                            'unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoSentenciadoProvisorioEmMedSeg":

                    $internos = json_decode($request->get('internos'), true);
                    $detalhesUnidades = json_decode($request->get('detalhesUnidades'));
                    $qtdSentenciadoM = $request->get('qtdSentenciadoM');
                    $qtdSentenciadoF = $request->get('qtdSentenciadoF');
                    $qtdProvisorioM = $request->get('qtdProvisorioM');
                    $qtdProvisorioF = $request->get('qtdProvisorioF');
                    $qtdNdM = $request->get('qtdNdM');
                    $qtdNdF = $request->get('qtdNdF');

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoSentenciadoProvisorioEmMedSeg',  compact('internos','dataEstatistica','detalhesUnidades','qtdSentenciadoM','qtdSentenciadoF',
                            'qtdProvisorioM',
                            'qtdProvisorioF',
                            'unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoMedSeg":

                    $internos = json_decode($request->get('internos'), true);
                    $detalhesUnidades = json_decode($request->get('detalhesUnidades'));
                    $qtdMedSegM = $request->get('qtdMedSegM');
                    $qtdMedSegF = $request->get('qtdMedSegF');

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoMedSeg',  compact('internos','dataEstatistica','detalhesUnidades','qtdMedSegM','qtdMedSegF','unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoFaixaEtaria":

                    $internos = json_decode($request->get('internos'), true);

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoFaixaEtaria',  compact('internos','dataEstatistica','unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoEstadoCivil":

                    $internos = json_decode($request->get('internos'), true);

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoEstadoCivil',  compact('internos','dataEstatistica','unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoEscolaridade":

                    $internos = json_decode($request->get('internos'), true);

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoEscolaridade',  compact('internos','dataEstatistica','unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoCorPele":

                    $internos = json_decode($request->get('internos'), true);

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoCorPele',  compact('internos','dataEstatistica','unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoOrientacaoSexual":

                    $internos = json_decode($request->get('internos'), true);

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoOrientacaoSexual',  compact('internos','dataEstatistica','unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoSaidaUnidadePrisional":

                    $movimentacoes = json_decode($request->get('movimentacoes'), true);

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoSaidaUnidadePrisional',  compact('movimentacoes','dataEstatistica','unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoReingresso":

                    $internos = json_decode($request->get('internos'), true);

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoReingresso',  compact('internos','dataEstatistica','unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoReligiao":

                    $internos = json_decode($request->get('internos'), true);

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoReligiao',  compact('internos','dataEstatistica','unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoFaccao":

                    $internos = json_decode($request->get('internos'), true);

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoFaccao',  compact('internos','dataEstatistica','unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoPericulosidade":

                    $internos = json_decode($request->get('internos'), true);

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoPericulosidade',  compact('internos','dataEstatistica','unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoLideranca":

                    $internos = json_decode($request->get('internos'), true);

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoLideranca',  compact('internos','dataEstatistica','unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoFilhosQuantidade":

                    $internos = json_decode($request->get('internos'), true);

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoFilhosQuantidade',  compact('internos','dataEstatistica','unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoFilhosFaixaEtaria":

                    $internos = json_decode($request->get('internos'), true);

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoFilhosFaixaEtaria',  compact('internos','dataEstatistica','unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoTransferenciaUnidade":

                    $internos = json_decode($request->get('internos'), true);

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoTransferenciaUnidade',  compact('internos','dataEstatistica','unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoDeficiencias":

                    $internos = json_decode($request->get('internos'), true);
                    $qtdInternos = $request->get('qtdInternos');

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoDeficiencias',  compact('internos','qtdInternos','dataEstatistica','unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoVisitantesQuantidade":

                    $internos = json_decode($request->get('internos'), true);

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoVisitantesQuantidade',  compact('internos','dataEstatistica','unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoQuantidadeVisitas":

                    $internos = json_decode($request->get('internos'), true);

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoQuantidadeVisitas',  compact('internos','dataEstatistica','unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
                
                case "internoIdentidadeGenero":

                    $internos = json_decode($request->get('internos'), true);

                    $pdf = \PDF::loadView('estatisticas.new.pdf.internoIdentidadeGenero',  compact('internos','dataEstatistica','unidadeEstatistica'))
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('margin-bottom', 10)
                        ->setOption('margin-top', 30)
                        ->setOption('margin-left', 10)
                        ->setOption('margin-right', 10)
                        ->setOption('margin-bottom', 10)
                        ->setOption('header-html', view('template.header.headerEstatistica'))
                        ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                        ->setOption('footer-font-size', 7)
                        ->setPaper('a4');
                    return $pdf->inline();

                    break;
        }
    }
    
    public function internosPorUnidade(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = $data = null;
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        $internosDados = DB::table('interno')
            ->leftJoin('unidade','unidade.id','interno.unidade_prisional')
            ->where('interno.status','A')
            ->select('interno.sexo', 'unidade.sigla');
        
        if(!is_null($unidadePrisional) && is_null($unidadePrisional))
        {
            $internosDados->where('interno.unidade_prisional', $unidadePrisional);
        }

        $internosDados = $internosDados->orderBy('unidade.sigla','ASC')->get();
        
        if(!is_null($data) && !is_null($unidadePrisional))
        {
            
            $internosDadosArray = self::getInternosUnidadeData($unidadePrisional, $data);
           
            $internosDados = array();
            foreach($internosDadosArray['internos'] as $internoId)
            {
                $dadosInterno = InternoController::getDadosInterno($internoId);
                array_push($internosDados, array(
                    'sexo' => $dadosInterno[0]->sexo,
                    'sigla' => UnidadesController::getSiglaUp($unidadePrisional),
                ));
            }
            
           $internosDados = json_encode($internosDados, TRUE);
            
            
        }
            
        
        $qtdUnidadeTotal  = DB::table('interno')
            ->leftJoin('unidade','unidade.id','interno.unidade_prisional')
            ->select(DB::raw('unidade.sigla, count(*) as qtd'))
            ->where('interno.status','A')->groupBy('unidade.sigla')->orderBy('qtd','DESC')->get();

        $internos = array();

        foreach(json_decode($internosDados, TRUE) as $array){
            $internos[$array["sigla"]][$array["sexo"]][] = $array;
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
                        
        return view('estatisticas.new.internoPorUnidade', compact('unidades','internos','qtdUnidadeTotal','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
        
    }
    
    public function internosRegimeUnidade(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = $data = null;
        }
        $internosRegime = null;
        // se selecionar uma uma data ou unidade
        if($_POST){
            
            if(!is_null($data) && !is_null($unidadePrisional))
            {

                //$internosData = Session::get('dataEstatistica')['internos'];
                $internosData = self::getInternosUnidadeData($unidadePrisional, $data);

                $internosRegime = array();

                foreach($internosData['internos'] as $interno){

                    $dados = DB::table("movimentacao_interno")
                            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                            ->where('movimentacao_interno.status', 'Finalizada')
                            ->where('movimentacao_interno.fk_interno', $interno)
                            ->where('movimentacao.data', '<=', Tdata::TdataMysql(Session::get('dataEstatistica')['data']))
                            ->whereIn('tipo', ['mudancaaberto','mudancafechado','mudancaprovisorio','mudancasemiaberto','progressaoaberto','progressaosemiaberto','regressaofechado','regressaosemiaberto'])
                            ->select('movimentacao.tipo','movimentacao.id')
                            ->orderBy('movimentacao.data', 'DESC')
                            ->get();

                    $dadosInterno = InternoController::getDadosInterno($interno)[0];
                    $regime = null;
                    $unidade = UnidadesController::getSiglaUp($unidadePrisional);
                    $idMov = 0;

                    if(count($dados) > 0)
                    {
                        switch ($dados[0]->tipo){

                            case 'mudancaaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                }else{
                                    $regime =  'ABERTO';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'progressaoaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                }else{
                                    $regime =  'ABERTO';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'mudancafechado':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                }else{
                                    $regime =  'FECHADO';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'regressaofechado':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                }else{
                                    $regime =  'FECHADO';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'mudancaprovisorio':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                }else{
                                    $regime =  "ND";
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'mudancasemiaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                }else{
                                    $regime =  'SEMIABERTO';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'progressaosemiaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                }else{
                                    $regime =  'SEMIABERTO';
                                }
                                $idMov = $dados[0]->id;

                                break;

                            case 'regressaosemiaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                }else{
                                    $regime =  'SEMIABERTO';
                                }
                                $idMov = $dados[0]->id;
                                break;

                        }
                        
                        if($dadosInterno->medida_seguranca == "S")
                        {
                            $regime = 'MEDIDASEGURANCA';
                        }

                    }else{

                        if(!is_null($dadosInterno->regime) && $dadosInterno->regime != "")
                        {
                            if($dadosInterno->sentenciado == "N")
                            {
                                $regime = 'PROVISORIO';

                            }else{
                                $regime = $dadosInterno->regime;
                            }


                        }else{

                            if($dadosInterno->sentenciado == "N")
                            {
                                $regime = 'PROVISORIO';

                            }else{
                                $regime = "ND";
                            }
                        }
                        
                        if($dadosInterno->medida_seguranca == "S")
                        {
                            $regime = 'MEDIDASEGURANCA';
                        }

                    }

                    array_push($internosRegime, array('regime' => $regime, 'sigla' => $unidade, 'idMov' => $idMov, 'idInterno' => $dadosInterno->id));

                }
            }
            
        $detalhesUnidades = null;
        
        }else{
            
            $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, null, null, null, null);
            $internosRegime = null;
            
        }
        
        
        if(!is_null($internosRegime)){
            $internos = array();

            foreach($internosRegime as $array){
                $internos[$array["sigla"]][$array["regime"]][] = $array;
            }
        }else{
            $internos = null;
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        return view('estatisticas.new.internoRegimePorUnidade', compact('unidades','internos','detalhesUnidades','lotacaoServidor','dataEstatistica','unidadeEstatistica'));


    }
    
    public static function internoPorSexo(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = $data = null;
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        $internosDados = DB::table('interno')
            ->leftJoin('unidade','unidade.id','interno.unidade_prisional')
            ->where('interno.status','A')
            ->select('interno.sexo', 'unidade.sigla');
        
        if(!is_null($unidadePrisional) && is_null($unidadePrisional))
        {
            $internosDados->where('interno.unidade_prisional', $unidadePrisional);
        }

        $internosDados = $internosDados->orderBy('unidade.sigla','ASC')->get();
        
        if(!is_null($data) && !is_null($unidadePrisional))
        {
            
            $internosDadosArray = self::getInternosUnidadeData($unidadePrisional, $data);
           
            $internosDados = array();
            foreach($internosDadosArray['internos'] as $internoId)
            {
                $dadosInterno = InternoController::getDadosInterno($internoId);
                array_push($internosDados, array(
                    'sexo' => $dadosInterno[0]->sexo,
                    'sigla' => UnidadesController::getSiglaUp($unidadePrisional),
                ));
            }
            
           $internosDados = json_encode($internosDados, TRUE);
            
            
        }
            
        
        $internos = array();

        foreach(json_decode($internosDados, TRUE) as $array){
            $internos[$array["sexo"]][] = $array;
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        return view('estatisticas.new.internoPorSexo', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
    }
    
    public function internoRegimeSexo(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = $data = null;
        }
        
        // se selecionar uma uma data ou unidade
        if($_POST){
            
            if(!is_null($data) && !is_null($unidadePrisional))
            {
                $internosData = self::getInternosUnidadeData($unidadePrisional, $data);

                $internosRegime = array();

                foreach($internosData['internos'] as $interno){

                    $dados = DB::table("movimentacao_interno")
                            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                            ->where('movimentacao_interno.status', 'Finalizada')
                            ->where('movimentacao_interno.fk_interno', $interno)
                            ->where('movimentacao.data', '<=', Tdata::TdataMysql(Session::get('dataEstatistica')['data']))
                            ->whereIn('tipo', ['mudancaaberto','mudancafechado','mudancasemiaberto','progressaoaberto','progressaosemiaberto','regressaofechado','regressaosemiaberto'])
                            ->select('movimentacao.tipo','movimentacao.id')
                            ->orderBy('movimentacao.data', 'DESC')
                            ->get();

                    $dadosInterno = InternoController::getDadosInterno($interno)[0];
                    $regime = null;
                    $sexo = $dadosInterno->sexo;
                    $unidade = UnidadesController::getSiglaUp($unidadePrisional);
                    $idMov = 0;

                    if(count($dados) > 0)
                    {
                        switch ($dados[0]->tipo){

                            case 'mudancaaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                }else{
                                    $regime =  'ABERTO';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'progressaoaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                }else{
                                    $regime =  'ABERTO';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'mudancafechado':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                }else{
                                    $regime =  'FECHADO';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'regressaofechado':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                }else{
                                    $regime =  'FECHADO';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'mudancaprovisorio':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                }else{
                                    $regime =  "ND";
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'mudancasemiaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                }else{
                                    $regime =  'SEMIABERTO';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'progressaosemiaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                }else{
                                    $regime =  'SEMIABERTO';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'regressaosemiaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                }else{
                                    $regime =  'SEMIABERTO';
                                }
                                $idMov = $dados[0]->id;
                                break;

                        }
                        
                        if($dadosInterno->medida_seguranca == "S")
                        {
                            $regime = 'MEDIDASEGURANCA';
                        }

                    }else{

                        if(!is_null($dadosInterno->regime) && $dadosInterno->regime != "")
                        {
                            if($dadosInterno->sentenciado == "N")
                            {
                                $regime = 'PROVISORIO';

                            }else{
                                $regime = $dadosInterno->regime;
                            }


                        }else{

                            if($dadosInterno->sentenciado == "N")
                            {
                                $regime = 'PROVISORIO';

                            }else{
                                $regime = "ND";
                            }
                        }
                        
                        if($dadosInterno->medida_seguranca == "S")
                        {
                            $regime = 'MEDIDASEGURANCA';
                        }

                    }

                    array_push($internosRegime, array('regime' => $regime, 'sigla' => $unidade, 'idMov' => $idMov, 'idInterno' => $dadosInterno->id, 'sexo' => $sexo));

                }
            }
            
        $detalhesUnidades = $qtdRegimeFechadoH = $qtdRegimeFechadoM = $qtdRegimeSemiabertoH = $qtdRegimeSemiabertoM = $qtdRegimeAbertoH = $qtdRegimeAbertoM = $qtdRegimeNIH = $qtdRegimeNIM = null;
        
        }else{
            
            $detalhesUnidades = true;
            $qtdRegimeFechadoH = DB::table('interno')->where('sexo','M')->where('regime', 'FECHADO')->where('status','A')->count();
            $qtdRegimeFechadoM = DB::table('interno')->where('sexo','F')->where('regime', 'FECHADO')->where('status','A')->count();
            $qtdRegimeSemiabertoH = DB::table('interno')->where('sexo','M')->where('regime', 'SEMIABERTO')->where('status','A')->count();
            $qtdRegimeSemiabertoM = DB::table('interno')->where('sexo','F')->where('regime', 'SEMIABERTO')->where('status','A')->count();
            $qtdRegimeAbertoH = DB::table('interno')->where('sexo','M')->where('regime', 'ABERTO')->where('status','A')->count();
            $qtdRegimeAbertoM = DB::table('interno')->where('sexo','F')->where('regime', 'ABERTO')->where('status','A')->count();
            $qtdRegimeNIH = DB::table('interno')->where('regime', null)->where('sentenciado','S')->where('sexo','M')->where('medida_seguranca','=','N')->where('status','A')->count();
            $qtdRegimeNIM = DB::table('interno')->where('regime', null)->where('sentenciado','S')->where('sexo','F')->where('medida_seguranca','=','N')->where('status','A')->count();
            $internosRegime = null;
            
        }
        
        
        if(!is_null($internosRegime)){
            $internos = array();

            foreach($internosRegime as $array){
                $internos[$array["regime"]][$array["sexo"]][] = $array;
            }
        }else{
            $internos = null;
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        //dd($internos);
        
        return view('estatisticas.new.internoRegimePorSexo', compact('unidades','internos','detalhesUnidades','lotacaoServidor','dataEstatistica','unidadeEstatistica','qtdRegimeFechadoH','qtdRegimeFechadoM','qtdRegimeSemiabertoH', 'qtdRegimeSemiabertoM','qtdRegimeAbertoH','qtdRegimeAbertoM','qtdRegimeNIH', 'qtdRegimeNIM'));
        
    }
    
    public function internosSentenciadosProvisoriosSMedSeg(Request $request) {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = $data = null;
        }
        
        $internosRegime = null;
        
        // se selecionar uma uma data ou unidade
        if($_POST){
            
            if(!is_null($data) && !is_null($unidadePrisional))
            {
                $internosData = self::getInternosUnidadeData($unidadePrisional, $data);

                $internosRegime = array();

                foreach($internosData['internos'] as $interno){

                    $dados = DB::table("movimentacao_interno")
                            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                            ->where('movimentacao_interno.status', 'Finalizada')
                            ->where('movimentacao_interno.fk_interno', $interno)
                            ->where('movimentacao.data', '<=', Tdata::TdataMysql(Session::get('dataEstatistica')['data']))
                            ->whereIn('tipo', ['mudancaaberto','mudancafechado','mudancasemiaberto','progressaoaberto','progressaosemiaberto','regressaofechado','regressaosemiaberto'])
                            ->select('movimentacao.tipo','movimentacao.id')
                            ->orderBy('movimentacao.data', 'DESC')
                            ->get();

                    $dadosInterno = InternoController::getDadosInterno($interno)[0];
                    $regime = null;
                    $sexo = $dadosInterno->sexo;
                    $sentenciado = null;
                    $unidade = UnidadesController::getSiglaUp($unidadePrisional);
                    $idMov = 0;

                    if(count($dados) > 0)
                    {
                        switch ($dados[0]->tipo){

                            case 'mudancaaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'ABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'progressaoaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'ABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'mudancafechado':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'FECHADO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'regressaofechado':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'FECHADO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'mudancaprovisorio':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  "ND";
                                    $sentenciado = 'ND';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'mudancasemiaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'SEMIABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'progressaosemiaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'SEMIABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'regressaosemiaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'SEMIABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                        }
                        
                        if($dadosInterno->medida_seguranca == "S")
                        {
                            $regime = 'MEDIDASEGURANCA';
                            $sentenciado = 'ND';
                        }

                    }else{

                        if(!is_null($dadosInterno->regime) && $dadosInterno->regime != "")
                        {
                            if($dadosInterno->sentenciado == "N")
                            {
                                $regime = 'PROVISORIO';
                                $sentenciado = 'N';

                            }else{
                                $regime = $dadosInterno->regime;
                                $sentenciado = 'S';
                            }


                        }else{

                            if($dadosInterno->sentenciado == "N")
                            {
                                $regime = 'PROVISORIO';
                                $sentenciado = 'N';

                            }else{
                                $regime = "ND";
                                $sentenciado = 'ND';
                            }
                        }
                        
                        if($dadosInterno->medida_seguranca == "S")
                        {
                            $regime = 'MEDIDASEGURANCA';
                            $sentenciado = 'ND';
                        }

                    }

                    array_push($internosRegime, array('regime' => $regime, 'sigla' => $unidade, 'idMov' => $idMov, 'idInterno' => $dadosInterno->id, 'sexo' => $sexo, 'sentenciado' => $sentenciado));

                }
            }
            
        $detalhesUnidades = $qtdSentenciadoM = $qtdSentenciadoF = $qtdProvisorioM = $qtdProvisorioF = $qtdNdM = $qtdNdF = null;
        
        }else{
            
            $detalhesUnidades = true;
            $qtdSentenciadoM = DB::table('interno')->where('sexo','M')->where('sentenciado', 'S')->where('medida_seguranca','N')->where('status','A')->count();
            $qtdSentenciadoF = DB::table('interno')->where('sexo','F')->where('sentenciado', 'S')->where('medida_seguranca','N')->where('status','A')->count();
            
            $qtdProvisorioM = DB::table('interno')->where('sexo','M')->where('sentenciado', 'N')->where('medida_seguranca','N')->where('status','A')->count();
            $qtdProvisorioF = DB::table('interno')->where('sexo','F')->where('sentenciado', 'N')->where('medida_seguranca','N')->where('status','A')->count();
            
            $qtdNdM = DB::table('interno')
                    ->where('sexo','M')
                    ->where('status','A')
                    ->where(function ($query) {
                        $query->where('sentenciado','')
                              ->where('sentenciado', null);
                    })
                    ->count();
            $qtdNdF = DB::table('interno')
                    ->where('sexo','F')
                    ->where('status','A')
                    ->where(function ($query) {
                        $query->where('sentenciado','')
                              ->where('sentenciado', null);
                    })
                    ->count();
                    
        }
        
        
        if(!is_null($internosRegime)){
            $internos = array();

            foreach($internosRegime as $array){
                $internos[$array["sentenciado"]][$array["sexo"]][] = $array;
            }
        }else{
            $internos = null;
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        //dd($internos);
        
        // a mesma view e utilizada nas funcoes internosSentenciadosProvisoriosSMedSeg e internosSentenciadosProvisoriosIncMedSeg
        return view('estatisticas.new.internoSentenciadoProvisorioSMedSeg', compact('unidades','internos','detalhesUnidades', 'internosRegime','lotacaoServidor','dataEstatistica','unidadeEstatistica','qtdSentenciadoM','qtdSentenciadoF','qtdProvisorioM','qtdProvisorioF','qtdNdM','qtdNdF'));
        
    }
    
    public function internosSentenciadosProvisoriosIncMedSeg(Request $request) {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = $data = null;
        }
        
        $internosRegime = null;
        
        // se selecionar uma uma data ou unidade
        if($_POST){
            
            if(!is_null($data) && !is_null($unidadePrisional))
            {
                $internosData = self::getInternosUnidadeData($unidadePrisional, $data);

                $internosRegime = array();

                foreach($internosData['internos'] as $interno){

                    $dados = DB::table("movimentacao_interno")
                            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                            ->where('movimentacao_interno.status', 'Finalizada')
                            ->where('movimentacao_interno.fk_interno', $interno)
                            ->where('movimentacao.data', '<=', Tdata::TdataMysql(Session::get('dataEstatistica')['data']))
                            ->whereIn('tipo', ['mudancaaberto','mudancafechado','mudancasemiaberto','progressaoaberto','progressaosemiaberto','regressaofechado','regressaosemiaberto'])
                            ->select('movimentacao.tipo','movimentacao.id')
                            ->orderBy('movimentacao.data', 'DESC')
                            ->get();

                    $dadosInterno = InternoController::getDadosInterno($interno)[0];
                    $regime = null;
                    $sexo = $dadosInterno->sexo;
                    $sentenciado = null;
                    $unidade = UnidadesController::getSiglaUp($unidadePrisional);
                    $idMov = 0;

                    if(count($dados) > 0)
                    {
                        switch ($dados[0]->tipo){

                            case 'mudancaaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'ABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'progressaoaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'ABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'mudancafechado':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'FECHADO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'regressaofechado':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'FECHADO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'mudancaprovisorio':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  "ND";
                                    $sentenciado = 'ND';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'mudancasemiaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'SEMIABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'progressaosemiaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'SEMIABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'regressaosemiaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'SEMIABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                        }
                        
                        if($dadosInterno->medida_seguranca == "S")
                        {
                            $regime = 'MEDIDASEGURANCA';
                            $sentenciado = 'S';
                        }

                    }else{

                        if(!is_null($dadosInterno->regime) && $dadosInterno->regime != "")
                        {
                            if($dadosInterno->sentenciado == "N")
                            {
                                $regime = 'PROVISORIO';
                                $sentenciado = 'N';

                            }else{
                                $regime = $dadosInterno->regime;
                                $sentenciado = 'S';
                            }


                        }else{

                            if($dadosInterno->sentenciado == "N")
                            {
                                $regime = 'PROVISORIO';
                                $sentenciado = 'N';

                            }else{
                                $regime = "ND";
                                $sentenciado = 'ND';
                            }
                        }
                        
                        if($dadosInterno->medida_seguranca == "S")
                        {
                            $regime = 'MEDIDASEGURANCA';
                            $sentenciado = 'S';
                        }

                    }

                    array_push($internosRegime, array('regime' => $regime, 'sigla' => $unidade, 'idMov' => $idMov, 'idInterno' => $dadosInterno->id, 'sexo' => $sexo, 'sentenciado' => $sentenciado));

                }
            }
            
        $detalhesUnidades = $qtdSentenciadoM = $qtdSentenciadoF = $qtdProvisorioM = $qtdProvisorioF = $qtdNdM = $qtdNdF = null;
        
        }else{
            
            $detalhesUnidades = true;
            $qtdSentenciadoM = DB::table('interno')->where('sexo','M')->where('sentenciado', 'S')->where('status','A')->count();
            $qtdSentenciadoF = DB::table('interno')->where('sexo','F')->where('sentenciado', 'S')->where('status','A')->count();
            
            $qtdProvisorioM = DB::table('interno')->where('sexo','M')->where('sentenciado', 'N')->where('status','A')->count();
            $qtdProvisorioF = DB::table('interno')->where('sexo','F')->where('sentenciado', 'N')->where('status','A')->count();
            
            $qtdNdM = DB::table('interno')
                    ->where('sexo','M')
                    ->where('status','A')
                    ->where(function ($query) {
                        $query->where('sentenciado','')
                              ->where('sentenciado', null);
                    })
                    ->count();
            $qtdNdF = DB::table('interno')
                    ->where('sexo','F')
                    ->where('status','A')
                    ->where(function ($query) {
                        $query->where('sentenciado','')
                              ->where('sentenciado', null);
                    })
                    ->count();
                    
        }
        
        
        if(!is_null($internosRegime)){
            $internos = array();

            foreach($internosRegime as $array){
                $internos[$array["sentenciado"]][$array["sexo"]][] = $array;
            }
        }else{
            $internos = null;
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        //dd($internos);
        
        // a mesma view e utilizada nas funcoes internosSentenciadosProvisoriosSMedSeg e internosSentenciadosProvisoriosIncMedSeg
        return view('estatisticas.new.internoSentenciadoProvisorioSMedSeg', compact('unidades','internos','detalhesUnidades', 'internosRegime','lotacaoServidor','dataEstatistica','unidadeEstatistica','qtdSentenciadoM','qtdSentenciadoF','qtdProvisorioM','qtdProvisorioF','qtdNdM','qtdNdF'));
        
    }
    
    public function internosSentenciadosProvisoriosEmMedSeg(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = $data = null;
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        $internosRegime = null;
        
        if($_POST){
                        
            if(!is_null($data) && !is_null($unidadePrisional))
            {
                $internosData = self::getInternosUnidadeData($unidadePrisional, $data);
                $internosMData = $internosRegime = array();

                foreach($internosData['internos'] as $internoData){

                    $dadosInterno = InternoController::getDadosInterno($internoData)[0];
                    if($dadosInterno->medida_seguranca == "S")
                    {
                        array_push($internosMData, $internoData);
                    }

                }
                
                foreach($internosMData as $interno)
                {
                    
                    $dados = DB::table("movimentacao_interno")
                            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                            ->where('movimentacao_interno.status', 'Finalizada')
                            ->where('movimentacao_interno.fk_interno', $interno)
                            ->where('movimentacao.data', '<=', Tdata::TdataMysql(Session::get('dataEstatistica')['data']))
                            ->whereIn('tipo', ['mudancaaberto','mudancafechado','mudancasemiaberto','progressaoaberto','progressaosemiaberto','regressaofechado','regressaosemiaberto'])
                            ->select('movimentacao.tipo','movimentacao.id')
                            ->orderBy('movimentacao.data', 'DESC')
                            ->get();

                    $dadosInterno = InternoController::getDadosInterno($interno)[0];
                    $regime = null;
                    $sexo = $dadosInterno->sexo;
                    $sentenciado = null;
                    $unidade = UnidadesController::getSiglaUp($unidadePrisional);
                    $idMov = 0;

                    if(count($dados) > 0)
                    {
                        switch ($dados[0]->tipo){

                            case 'mudancaaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'ABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'progressaoaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'ABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'mudancafechado':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'FECHADO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'regressaofechado':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'FECHADO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'mudancaprovisorio':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  "ND";
                                    $sentenciado = 'ND';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'mudancasemiaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'SEMIABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'progressaosemiaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'SEMIABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'regressaosemiaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'SEMIABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                        }
                        
                        if($dadosInterno->medida_seguranca == "S")
                        {
                            $regime = 'MEDIDASEGURANCA';
                            $sentenciado = 'S';
                        }

                    }else{

                        if(!is_null($dadosInterno->regime) && $dadosInterno->regime != "")
                        {
                            if($dadosInterno->sentenciado == "N")
                            {
                                $regime = 'PROVISORIO';
                                $sentenciado = 'N';

                            }else{
                                $regime = $dadosInterno->regime;
                                $sentenciado = 'S';
                            }


                        }else{

                            if($dadosInterno->sentenciado == "N")
                            {
                                $regime = 'PROVISORIO';
                                $sentenciado = 'N';

                            }else{
                                $regime = "ND";
                                $sentenciado = 'ND';
                            }
                        }
                        
                        if($dadosInterno->medida_seguranca == "S")
                        {
                            $regime = 'MEDIDASEGURANCA';
                            $sentenciado = 'S';
                        }

                    }

                    array_push($internosRegime, array('regime' => $regime, 'sigla' => $unidade, 'idMov' => $idMov, 'idInterno' => $dadosInterno->id, 'sexo' => $sexo, 'sentenciado' => $sentenciado));

                }
                    
                }
                
            $detalhesUnidades = $qtdSentenciadoM = $qtdSentenciadoF = $qtdProvisorioM = $qtdProvisorioF = $qtdNdM = $qtdNdF = null;
            
            }else{
            
            $detalhesUnidades = true;
            $qtdSentenciadoM = DB::table('interno')->where('sexo','M')->where('sentenciado', 'S')->where('medida_seguranca', 'S')->where('status','A')->count();
            $qtdSentenciadoF = DB::table('interno')->where('sexo','F')->where('sentenciado', 'S')->where('medida_seguranca', 'S')->where('status','A')->count();
            
            $qtdProvisorioM = DB::table('interno')->where('sexo','M')->where('sentenciado', 'N')->where('medida_seguranca', 'S')->where('status','A')->count();
            $qtdProvisorioF = DB::table('interno')->where('sexo','F')->where('sentenciado', 'N')->where('medida_seguranca', 'S')->where('status','A')->count();
            
            $qtdNdM = DB::table('interno')
                    ->where('sexo','M')
                    ->where('status','A')
                    ->where(function ($query) {
                        $query->where('sentenciado','')
                              ->where('sentenciado', null);
                    })
                    ->count();
            $qtdNdF = DB::table('interno')
                    ->where('sexo','F')
                    ->where('status','A')
                    ->where(function ($query) {
                        $query->where('sentenciado','')
                              ->where('sentenciado', null);
                    })
                    ->count();
                    
        }
            
        if(!is_null($internosRegime)){
            $internos = array();

            foreach($internosRegime as $array){
                $internos[$array["sentenciado"]][$array["sexo"]][] = $array;
            }
        }else{
            $internos = null;
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        return view('estatisticas.new.internoSentenciadoProvisorioEmMedSeg', compact('unidades','internos','detalhesUnidades', 'internosRegime','lotacaoServidor','dataEstatistica','unidadeEstatistica','qtdSentenciadoM','qtdSentenciadoF','qtdProvisorioM','qtdProvisorioF','qtdNdM','qtdNdF'));
        
    }
    public function internosMedSeg(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = $data = null;
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        $internosRegime = null;
        
        if($_POST){
                        
            if(!is_null($data) && !is_null($unidadePrisional))
            {
                $internosData = self::getInternosUnidadeData($unidadePrisional, $data);
                $internosMData = $internosRegime = array();

                foreach($internosData['internos'] as $internoData){

                    $dadosInterno = InternoController::getDadosInterno($internoData)[0];
                    if($dadosInterno->medida_seguranca == "S")
                    {
                        array_push($internosMData, $internoData);
                    }

                }
                
                foreach($internosMData as $interno)
                {
                    
                    $dados = DB::table("movimentacao_interno")
                            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                            ->where('movimentacao_interno.status', 'Finalizada')
                            ->where('movimentacao_interno.fk_interno', $interno)
                            ->where('movimentacao.data', '<=', Tdata::TdataMysql(Session::get('dataEstatistica')['data']))
                            ->whereIn('tipo', ['mudancaaberto','mudancafechado','mudancasemiaberto','progressaoaberto','progressaosemiaberto','regressaofechado','regressaosemiaberto'])
                            ->select('movimentacao.tipo','movimentacao.id')
                            ->orderBy('movimentacao.data', 'DESC')
                            ->get();

                    $dadosInterno = InternoController::getDadosInterno($interno)[0];
                    $regime = null;
                    $sexo = $dadosInterno->sexo;
                    $sentenciado = null;
                    $unidade = UnidadesController::getSiglaUp($unidadePrisional);
                    $idMov = 0;

                    if(count($dados) > 0)
                    {
                        switch ($dados[0]->tipo){

                            case 'mudancaaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'ABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'progressaoaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'ABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'mudancafechado':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'FECHADO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'regressaofechado':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'FECHADO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'mudancaprovisorio':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  "ND";
                                    $sentenciado = 'ND';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'mudancasemiaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'SEMIABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'progressaosemiaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'SEMIABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                            case 'regressaosemiaberto':
                                if($dadosInterno->sentenciado == "N"){
                                    $regime = 'PROVISORIO';
                                    $sentenciado = 'N';
                                }else{
                                    $regime =  'SEMIABERTO';
                                    $sentenciado = 'S';
                                }
                                $idMov = $dados[0]->id;
                                break;

                        }
                        
                        if($dadosInterno->medida_seguranca == "S")
                        {
                            $regime = 'MEDIDASEGURANCA';
                            $sentenciado = 'S';
                        }

                    }else{

                        if(!is_null($dadosInterno->regime) && $dadosInterno->regime != "")
                        {
                            if($dadosInterno->sentenciado == "N")
                            {
                                $regime = 'PROVISORIO';
                                $sentenciado = 'N';

                            }else{
                                $regime = $dadosInterno->regime;
                                $sentenciado = 'S';
                            }


                        }else{

                            if($dadosInterno->sentenciado == "N")
                            {
                                $regime = 'PROVISORIO';
                                $sentenciado = 'N';

                            }else{
                                $regime = "ND";
                                $sentenciado = 'ND';
                            }
                        }
                        
                        if($dadosInterno->medida_seguranca == "S")
                        {
                            $regime = 'MEDIDASEGURANCA';
                            $sentenciado = 'S';
                        }

                    }

                    array_push($internosRegime, array('regime' => $regime, 'sigla' => $unidade, 'idMov' => $idMov, 'idInterno' => $dadosInterno->id, 'sexo' => $sexo, 'sentenciado' => $sentenciado));

                }
                    
                }
                
            $detalhesUnidades = $qtdMedSegM = $qtdMedSegF = null;
            
            }else{
            
            $detalhesUnidades = true;
            $qtdMedSegM = DB::table('interno')->where('sexo','F')->where('medida_seguranca', 'S')->where('status','A')->count();
            $qtdMedSegF = DB::table('interno')->where('sexo','M')->where('medida_seguranca', 'S')->where('status','A')->count();
                    
        }
            
        if(!is_null($internosRegime)){
            $internos = array();

            foreach($internosRegime as $array){
                $internos[$array["sexo"]][] = $array;
            }
        }else{
            $internos = null;
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        return view('estatisticas.new.internoMedSeg', compact('unidades','internos','detalhesUnidades', 'internosRegime','lotacaoServidor','dataEstatistica','unidadeEstatistica','qtdMedSegM','qtdMedSegF'));
        
    }
    
    public function internoFaixaEtaria(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        $internosFaixaEtaria = array();
        $faixaEtaria = null;
        
        if($_POST){
            
            $internosData = self::getInternosUnidadeData($unidadePrisional, $data);

            foreach($internosData['internos'] as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $date1 = $dadosInterno->data_nascimento;

                if(!is_null($date1) && $date1 != ""){

                    $date2 = Tdata::TdataMysql($data);

                    $diff = abs(strtotime($date2) - strtotime($date1));

                    $idade = floor($diff / (365*60*60*24));

                    if($idade >= 0 && $idade <= 17)
                    {
                        
                        array_push($internosFaixaEtaria, array('faixaEtaria' => '017', 'sexo' => $dadosInterno->sexo));
                    }

                    if($idade >= 18 && $idade <= 24)
                    {
                        array_push($internosFaixaEtaria, array('faixaEtaria' => '1824', 'sexo' => $dadosInterno->sexo));
                    }

                    if($idade >= 25 && $idade <= 29)
                    {
                        array_push($internosFaixaEtaria, array('faixaEtaria' => '2529', 'sexo' => $dadosInterno->sexo));
                    }

                    if($idade >= 30 && $idade <= 34)
                    {
                        array_push($internosFaixaEtaria, array('faixaEtaria' => '3034', 'sexo' => $dadosInterno->sexo));
                    }

                    if($idade >= 35 && $idade <= 45)
                    {
                        array_push($internosFaixaEtaria, array('faixaEtaria' => '3545', 'sexo' => $dadosInterno->sexo));
                    }

                    if($idade >= 46 && $idade <= 60)
                    {
                        array_push($internosFaixaEtaria, array('faixaEtaria' => '4660', 'sexo' => $dadosInterno->sexo));
                    }

                    if($idade >= 61 && $idade <= 70)
                    {
                        array_push($internosFaixaEtaria, array('faixaEtaria' => '6170', 'sexo' => $dadosInterno->sexo));
                    }

                    if($idade > 70)
                    {
                        array_push($internosFaixaEtaria, array('faixaEtaria' => '70', 'sexo' => $dadosInterno->sexo));
                    }

                }else{
                    array_push($internosFaixaEtaria, array('faixaEtaria' => "ND", 'sexo' => $dadosInterno->sexo));
                }

            }
        }else{
            
            $internosGeral = DB::table('interno')->where('status', 'A')->pluck('id');
            
            foreach($internosGeral as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $date1 = $dadosInterno->data_nascimento;

                if(!is_null($date1) && $date1 != ""){

                    $date2 = $data;

                    $diff = abs(strtotime($date2) - strtotime($date1));

                    $idade = floor($diff / (365*60*60*24));

                    if($idade >= 0 && $idade <= 17)
                    {
                        array_push($internosFaixaEtaria, array('faixaEtaria' => '017', 'sexo' => $dadosInterno->sexo));
                    }

                    if($idade >= 18 && $idade <= 24)
                    {
                        array_push($internosFaixaEtaria, array('faixaEtaria' => '1824', 'sexo' => $dadosInterno->sexo));
                    }

                    if($idade >= 25 && $idade <= 29)
                    {
                        array_push($internosFaixaEtaria, array('faixaEtaria' => '2529', 'sexo' => $dadosInterno->sexo));
                    }

                    if($idade >= 30 && $idade <= 34)
                    {
                        array_push($internosFaixaEtaria, array('faixaEtaria' => '3034', 'sexo' => $dadosInterno->sexo));
                    }

                    if($idade >= 35 && $idade <= 45)
                    {
                        array_push($internosFaixaEtaria, array('faixaEtaria' => '3545', 'sexo' => $dadosInterno->sexo));
                    }

                    if($idade >= 46 && $idade <= 60)
                    {
                        array_push($internosFaixaEtaria, array('faixaEtaria' => '4660', 'sexo' => $dadosInterno->sexo));
                    }

                    if($idade >= 61 && $idade <= 70)
                    {
                        array_push($internosFaixaEtaria, array('faixaEtaria' => '6170', 'sexo' => $dadosInterno->sexo));
                    }

                    if($idade > 70)
                    {
                        array_push($internosFaixaEtaria, array('faixaEtaria' => '70', 'sexo' => $dadosInterno->sexo));
                    }

                }else{
                    array_push($internosFaixaEtaria, array('faixaEtaria' => "ND", 'sexo' => $dadosInterno->sexo));
                }

            }
            
        }
        
        $internos = array();

        foreach($internosFaixaEtaria as $array){
            $internos[$array["faixaEtaria"]][$array["sexo"]][] = $array;
        }

        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        //dd($internos);
        
        return view('estatisticas.new.internoFaixaEtaria', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
        
    }
    
    public function internoNacionalidade(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        $internosNacionalidade = array();
        
        if($_POST){
            
            $internosData = self::getInternosUnidadeData($unidadePrisional, $data);

            foreach($internosData['internos'] as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $nacionalidade = $dadosInterno->naturalidade_pais;

                array_push($internosNacionalidade, array('nacionalidade' => $nacionalidade, 'sexo' => $dadosInterno->sexo));


            }
        }else{
            
            $internosGeral = DB::table('interno')->where('status', 'A')->pluck('id');
            
            foreach($internosGeral as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $nacionalidade = $dadosInterno->naturalidade_pais;

                array_push($internosNacionalidade, array('nacionalidade' => $nacionalidade, 'sexo' => $dadosInterno->sexo));

            }
            
        }
        
        $internos = array();

        $internosNacionalidade = self::array_orderby($internosNacionalidade, 'nacionalidade', SORT_ASC);

        foreach($internosNacionalidade as $array){
            $internos[$array["nacionalidade"]][$array["sexo"]][] = $array;
        }

        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        //dd($internos);
        
        return view('estatisticas.new.internoNacionalidade', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
        
    }
    
    public function internoEstadoCivil(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        $internosEstadoCivil = array();
        
        if($_POST){
            
            $internosData = self::getInternosUnidadeData($unidadePrisional, $data);

            foreach($internosData['internos'] as $internoData)
            {
                
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $estadoCivil = null;
                if($dadosInterno->estado_civil == "casado")
                {
                    $estadoCivil = 'Casado';
                }
                if($dadosInterno->estado_civil == "divorciado")
                {
                    $estadoCivil = 'Divorciado';
                }
                if($dadosInterno->estado_civil == "separado")
                {
                    $estadoCivil = 'Separado';
                }
                if($dadosInterno->estado_civil == "solteiro")
                {
                    $estadoCivil = 'Solteiro';
                }
                if($dadosInterno->estado_civil == "uniao estavel")
                {
                    $estadoCivil = 'União Estável';
                }
                if($dadosInterno->estado_civil == "viuvo")
                {
                    $estadoCivil = 'Viúvo';
                }

                array_push($internosEstadoCivil, array('estadoCivil' => $estadoCivil, 'sexo' => $dadosInterno->sexo));


            }
        }else{
            
            $internosGeral = DB::table('interno')->where('status', 'A')->pluck('id');
            
            foreach($internosGeral as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $estadoCivil = null;
                if($dadosInterno->estado_civil == "casado")
                {
                    $estadoCivil = 'Casado';
                }
                if($dadosInterno->estado_civil == "divorciado")
                {
                    $estadoCivil = 'Divorciado';
                }
                if($dadosInterno->estado_civil == "separado")
                {
                    $estadoCivil = 'Separado';
                }
                if($dadosInterno->estado_civil == "solteiro")
                {
                    $estadoCivil = 'Solteiro';
                }
                if($dadosInterno->estado_civil == "uniao estavel")
                {
                    $estadoCivil = 'União Estável';
                }
                if($dadosInterno->estado_civil == "viuvo")
                {
                    $estadoCivil = 'Viúvo';
                }

                array_push($internosEstadoCivil, array('estadoCivil' => $estadoCivil, 'sexo' => $dadosInterno->sexo));

            }
            
        }
        
        $internos = array();

        $internosEstadoCivil = self::array_orderby($internosEstadoCivil, 'estadoCivil', SORT_ASC);
        
        foreach($internosEstadoCivil as $array){
            $internos[$array["estadoCivil"]][$array["sexo"]][] = $array;
        }

        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        //dd($internos);
        
        return view('estatisticas.new.internoEstadoCivil', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
        
    }
    
    public function internoEscolaridade(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        $internosEscolaridade = array();
        
        if($_POST){
            
            $internosData = self::getInternosUnidadeData($unidadePrisional, $data);

            foreach($internosData['internos'] as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $escolaridade = null;
                if($dadosInterno->escolaridade == "" && $dadosInterno->escolaridade == null)
                {
                    
                    $escolaridade = "Não Informado";
                                   
                }else{
                    $escolaridade = $dadosInterno->escolaridade;
                }

                array_push($internosEscolaridade, array('escolaridade' => $escolaridade, 'sexo' => $dadosInterno->sexo));


            }
        }else{
            
            $internosGeral = DB::table('interno')->where('status', 'A')->pluck('id');
            
            foreach($internosGeral as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $escolaridade = null;
                if($dadosInterno->escolaridade == "" && $dadosInterno->escolaridade == null)
                {
                    $escolaridade = "Não Informado";
                                   
                }else{
                    $escolaridade = $dadosInterno->escolaridade;
                }

                array_push($internosEscolaridade, array('escolaridade' => $escolaridade, 'sexo' => $dadosInterno->sexo));

            }
            
        }
        
        $internos = array();
        
        $internosEscolaridade = self::array_orderby($internosEscolaridade, 'escolaridade', SORT_ASC);

        foreach($internosEscolaridade as $array){
            $internos[$array["escolaridade"]][$array["sexo"]][] = $array;
        }

        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        
        return view('estatisticas.new.internoEscolaridade', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
        
    }
    
    public function internoCorPele(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        $internosCorPele = array();
        
        if($_POST){
            
            $internosData = self::getInternosUnidadeData($unidadePrisional, $data);

            foreach($internosData['internos'] as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $corPele = null;
                if($dadosInterno->etnia == "" && $dadosInterno->etnia == null)
                {
                    
                    $corPele = "Não Informado";
                                   
                }else{
                    $corPele = $dadosInterno->etnia;
                }

                array_push($internosCorPele, array('corPele' => $corPele, 'sexo' => $dadosInterno->sexo));


            }
        }else{
            
            $internosGeral = DB::table('interno')->where('status', 'A')->pluck('id');
            
            foreach($internosGeral as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $corPele = null;
                if($dadosInterno->etnia == "" && $dadosInterno->etnia == null)
                {
                    $corPele = "Não Informado";
                                   
                }else{
                    $corPele = $dadosInterno->etnia;
                }

                array_push($internosCorPele, array('corPele' => $corPele, 'sexo' => $dadosInterno->sexo));

            }
            
        }
        
        $internos = array();
        
        $internosCorPele = self::array_orderby($internosCorPele, 'corPele', SORT_ASC);

        foreach($internosCorPele as $array){
            $internos[$array["corPele"]][$array["sexo"]][] = $array;
        }

        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        
        return view('estatisticas.new.internoCorPele', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
        
    }
    
    public function internoOrientacaoSexual(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        $internosOpcaoSexual = array();
        
        if($_POST){
            
            $internosData = self::getInternosUnidadeData($unidadePrisional, $data);

            foreach($internosData['internos'] as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $opcaoSexual = null;
                if($dadosInterno->opcao_sexual == "" && $dadosInterno->opcao_sexual == null)
                {
                    
                    $opcaoSexual = "Não Informado";
                                   
                }else{
                    $opcaoSexual = $dadosInterno->opcao_sexual;
                }

                array_push($internosOpcaoSexual, array('opcaoSexual' => $opcaoSexual, 'sexo' => $dadosInterno->sexo));


            }
        }else{
            
            $internosGeral = DB::table('interno')->where('status', 'A')->pluck('id');
            
            foreach($internosGeral as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $opcaoSexual = null;
                if($dadosInterno->opcao_sexual == "" && $dadosInterno->opcao_sexual == null)
                {
                    $opcaoSexual = "Não Informado";
                                   
                }else{
                    if($dadosInterno->opcao_sexual == 'nao declarado')
                    {
                        $opcaoSexual = "Não Declarado";
                    }else{
                        $opcaoSexual = ucfirst($dadosInterno->opcao_sexual);
                    }
                    
                }

                array_push($internosOpcaoSexual, array('opcaoSexual' => $opcaoSexual, 'sexo' => $dadosInterno->sexo));

            }
            
        }
        
        $internos = array();
        
        $internosOpcaoSexual = self::array_orderby($internosOpcaoSexual, 'opcaoSexual', SORT_ASC);

        foreach($internosOpcaoSexual as $array){
            $internos[$array["opcaoSexual"]][$array["sexo"]][] = $array;
        }

        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
       //dd($internos);
        
        return view('estatisticas.new.internoOrientacaoSexual', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
        
    }
    
    public function internoSaidaUnidade(Request $request) {

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
            $dataFinal = $request->get('dataFinal');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        $internosOpcaoSexual = array();
        
        if($_POST){
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->where('movimentacao_interno.up_anterior', $unidadePrisional)
                    ->whereIn('tipo', ['alvara','evasao','extincaopunibilidade','extradicao','fuga','livramentocondicional','obito','prisaodomiciliar','recambiamento','saidajudicial']);
            if(!is_null($data))
            {
                $movimentacaoDados->whereDate('movimentacao.data', '>=', Tdata::TdataMysql($data));
            }
            
            if(!is_null($dataFinal))
            {
                $movimentacaoDados->whereDate('movimentacao.data', '<=', Tdata::TdataMysql($dataFinal));
            }
            $movimentacaoDados = $movimentacaoDados->pluck('movimentacao.tipo');
            
            $movimentacoesArray = array();
            
            foreach($movimentacaoDados as $mov)
            {
                $tipoMov = null;
                switch ($mov){
                    
                    case "alvara":
                        $tipoMov = "Alvará";
                        break;
                    
                    case "evasao":
                        $tipoMov = "Evasão";
                        break;
                    
                    case "extincaopunibilidade":
                        $tipoMov = "Extinção de Punibilidade";
                        break;
                    
                    case "extradicao":
                        $tipoMov = "Extradição";
                        break;
                    
                    case "fuga":
                        $tipoMov = "Fuga";
                        break;
                    
                    case "livramentocondicional":
                        $tipoMov = "Livramento Condicional";
                        break;
                    
                    case "obito":
                        $tipoMov = "Óbito";
                        break;
                    
                    case "prisaodomiciliar":
                        $tipoMov = "Prisão Domiciliar";
                        break;
                    
                    case "recambiamento":
                        $tipoMov = "Recambiamento";
                        break;
                    
                    case "saidajudicial":
                        $tipoMov = "Saída Judicial";
                        break;
                    
                }
                array_push($movimentacoesArray, array('tipo' => $tipoMov));
            }

            $movimentacoes = array();

            $movimentacoesArray = self::array_orderby($movimentacoesArray, 'tipo', SORT_ASC);

            foreach($movimentacoesArray as $array){
                $movimentacoes[$array["tipo"]][] = $array;
            }
            
        }else{
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['alvara','evasao','extincaopunibilidade','extradicao','fuga','livramentocondicional','obito','prisaodomiciliar','recambiamento','saidajudicial'])
                    ->pluck('movimentacao.tipo');

            $movimentacoesArray = array();
            foreach($movimentacaoDados as $mov)
            {
                $tipoMov = null;
                switch ($mov){
                    
                    case "alvara":
                        $tipoMov = "Alvará";
                        break;
                    
                    case "evasao":
                        $tipoMov = "Evasão";
                        break;
                    
                    case "extincaopunibilidade":
                        $tipoMov = "Extinção de Punibilidade";
                        break;
                    
                    case "extradicao":
                        $tipoMov = "Extradição";
                        break;
                    
                    case "fuga":
                        $tipoMov = "Fuga";
                        break;
                    
                    case "livramentocondicional":
                        $tipoMov = "Livramento Condicional";
                        break;
                    
                    case "obito":
                        $tipoMov = "Óbito";
                        break;
                    
                    case "prisaodomiciliar":
                        $tipoMov = "Prisão Domiciliar";
                        break;
                    
                    case "recambiamento":
                        $tipoMov = "Recambiamento";
                        break;
                    
                    case "saidajudicial":
                        $tipoMov = "Saída Judicial";
                        break;
                    
                }
                array_push($movimentacoesArray, array('tipo' => $tipoMov));
            }

            $movimentacoes = array();

            $movimentacoesArray = self::array_orderby($movimentacoesArray, 'tipo', SORT_ASC);

            foreach($movimentacoesArray as $array){
                $movimentacoes[$array["tipo"]][] = $array;
            }

                //dd($movimentacoes);

        }

        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Penitenciário";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
       //dd($internos);
        
        return view('estatisticas.new.internoSaidaUnidade', compact('unidades','movimentacoes','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
        
    }
    
    public function internoReingresso(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
            $dataFinal = $request->get('dataFinal');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        $internosReingresso = array();
        
        if($_POST)
        {
            $dadosReigresso = DB::table('log')
                ->leftJoin('interno', 'log.fk_id', 'interno.id')
                ->leftJoin('unidade', 'unidade.id', 'log.unidade')
                ->where('log.texto', 'reativação de interno')
                ->where('log.unidade', $unidadePrisional);
                if(!is_null($data))
                {
                    $dadosReigresso->where('log.created_at', '>=', Tdata::TdataMysql($data));
                }
                if(!is_null($dataFinal))
                {
                    $dadosReigresso->where('log.created_at', '<=', Tdata::TdataMysql($dataFinal));
                }
                $dadosReigresso = $dadosReigresso->select('interno.sexo', 'unidade.sigla')->get();
                
        }else{
            
            $dadosReigresso = DB::table('log')
                ->leftJoin('interno', 'log.fk_id', 'interno.id')
                ->leftJoin('unidade', 'unidade.id', 'log.unidade')
                ->where('log.texto', 'reativação de interno')
                ->where('interno.status','A')
                ->select('interno.sexo', 'unidade.sigla')
                ->get();
        }
        
        foreach($dadosReigresso as $reingresso)
        {
            array_push($internosReingresso, array('unidade' => $reingresso->sigla, 'sexo' => $reingresso->sexo));
        }
        
        $internos = array();
        
        $internosReingresso = self::array_orderby($internosReingresso, 'unidade', SORT_ASC);

        foreach($internosReingresso as $array){
            $internos[$array["unidade"]][$array["sexo"]][] = $array;
        }

        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        return view('estatisticas.new.internoReingresso', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));

        
    }
    
    public function internoReligiao(Request $request) {
        
        $adventista = array('Adventista');
        $budista = array('Budista','Budista, Católica');
        $catolico = array('Católica','Católica, Afro Brasileira','Católica, Afro Brasileira, Protestante','Católica, Espírita','Católica, Neopentecostal','Católica, Outras','Católica, Protestante','Católica, Sem Religião','Católica, Sem Religião, Protestante');
        $espirita = array('Espírita','Espírita, Protestante');
        $evangelico = array('Evangélica');
        $judeu = array('Judeu');
        $matrizAfricana = array('Afro Brasileira','Afro Brasileira, Neopentecostal','Matriz Africana');
        $mormon = array('Mórmon');
        $mulcumana = array('Muçulmana');
        $protestante = array('Protestante','Protestante, Neopentecostal');
        $agnostico = array('Agnóstico');
        $ateu = array('Ateu');
        $semReligiao = array('Sem Religião','Sem Religião, Neopentecostal','Sem Religião, Outras','Sem Religião, Protestante');
        $outras = array('Outras','Outras, Afro Brasileira','Outras, Protestante');
        
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        $internosReligiao = array();
        
        if($_POST){
            
            $internosData = self::getInternosUnidadeData($unidadePrisional, $data);

            foreach($internosData['internos'] as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $religiao = null;
                if($dadosInterno->religiao == "" && $dadosInterno->religiao == null)
                {
                    $religiao = "Não Informada";
                                   
                }else{
                    
                    switch($dadosInterno->religiao){
                        
                        case in_array($dadosInterno->religiao, $adventista):
                            $religiao = "Adventistas";
                            break;
                        
                        case in_array($dadosInterno->religiao, $budista):
                            $religiao = "Budista";
                            break;
                        
                        case in_array($dadosInterno->religiao, $catolico):
                            $religiao = "Católicos";
                            break;
                        
                        case in_array($dadosInterno->religiao, $espirita):
                            $religiao = "Espíritas";
                            break;
                        
                        case in_array($dadosInterno->religiao, $evangelico):
                            $religiao = "Evangélicos";
                            break;
                        
                        case in_array($dadosInterno->religiao, $judeu):
                            $religiao = "Judeus";
                            break;
                        
                        case in_array($dadosInterno->religiao, $matrizAfricana):
                            $religiao = "Matriz Africana";
                            break;
                        
                        case in_array($dadosInterno->religiao, $mormon):
                            $religiao = "Mórmons";
                            break;
                        
                        case in_array($dadosInterno->religiao, $mulcumana):
                            $religiao = "Muçulmanos";
                            break;
                        
                        case in_array($dadosInterno->religiao, $protestante):
                            $religiao = "Protestantes";
                            break;
                        
                        case in_array($dadosInterno->religiao, $agnostico):
                            $religiao = "Agnósticos";
                            break;
                        
                        case in_array($dadosInterno->religiao, $ateu):
                            $religiao = "Ateus";
                            break;
                        
                        case in_array($dadosInterno->religiao, $semReligiao):
                            $religiao = "Sem Religião";
                            break;
                        
                        case in_array($dadosInterno->religiao, $outras):
                            $religiao = "Outras";
                            break;
                        
                    }               
                }

                array_push($internosReligiao, array('religiao' => $religiao, 'sexo' => $dadosInterno->sexo));


            }
        }else{
            
            $internosGeral = DB::table('interno')->where('status', 'A')->pluck('id');
            
            foreach($internosGeral as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $religiao = null;
                if($dadosInterno->religiao == "" && $dadosInterno->religiao == null)
                {
                    $religiao = "Não Informada";
                                   
                }else{
                    
                    switch($dadosInterno->religiao){
                        
                        case in_array($dadosInterno->religiao, $adventista):
                            $religiao = "Adventistas";
                            break;
                        
                        case in_array($dadosInterno->religiao, $budista):
                            $religiao = "Budista";
                            break;
                        
                        case in_array($dadosInterno->religiao, $catolico):
                            $religiao = "Católicos";
                            break;
                        
                        case in_array($dadosInterno->religiao, $espirita):
                            $religiao = "Espíritas";
                            break;
                        
                        case in_array($dadosInterno->religiao, $evangelico):
                            $religiao = "Evangélicos";
                            break;
                        
                        case in_array($dadosInterno->religiao, $judeu):
                            $religiao = "Judeus";
                            break;
                        
                        case in_array($dadosInterno->religiao, $matrizAfricana):
                            $religiao = "Matriz Africana";
                            break;
                        
                        case in_array($dadosInterno->religiao, $mormon):
                            $religiao = "Mórmons";
                            break;
                        
                        case in_array($dadosInterno->religiao, $mulcumana):
                            $religiao = "Muçulmanos";
                            break;
                        
                        case in_array($dadosInterno->religiao, $protestante):
                            $religiao = "Protestantes";
                            break;
                        
                        case in_array($dadosInterno->religiao, $agnostico):
                            $religiao = "Agnósticos";
                            break;
                        
                        case in_array($dadosInterno->religiao, $ateu):
                            $religiao = "Ateus";
                            break;
                        
                        case in_array($dadosInterno->religiao, $semReligiao):
                            $religiao = "Sem Religião";
                            break;
                        
                        case in_array($dadosInterno->religiao, $outras):
                            $religiao = "Outras";
                            break;
                        
                    }               
                }

                array_push($internosReligiao, array('religiao' => $religiao, 'sexo' => $dadosInterno->sexo));

            }
            
        }
        
        $internos = array();
        
        $internosReligiao = self::array_orderby($internosReligiao, 'religiao', SORT_ASC);

        foreach($internosReligiao as $array){
            $internos[$array["religiao"]][$array["sexo"]][] = $array;
        }

        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
              
        //dd($internos);
        
        return view('estatisticas.new.internoReligiao', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
        
    }

    public function internoFaccionados(Request $request)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        $internosFaccao = array();
        
        if($_POST){
            
            $internosData = self::getInternosUnidadeData($unidadePrisional, $data);

            foreach($internosData['internos'] as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $faccao = null;
                if($dadosInterno->faccao == "" && $dadosInterno->faccao == null)
                {
                    $faccao = "Não Informado ou Não Faccionado";
                                   
                }else{
                    $faccao = "S";
                    
                    }    
                
                array_push($internosFaccao, array('faccionado' => $faccao, 'sexo' => $dadosInterno->sexo));

            }


        }else{
            
            $internosGeral = DB::table('interno')->where('status', 'A')->pluck('id');
            
            foreach($internosGeral as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $faccao = null;
                if($dadosInterno->faccao == "" && $dadosInterno->faccao == null)
                {
                    $faccao = "Não Informado ou Não Faccionado";
                                   
                }else{
                    $faccao = "Faccionado";
                    
                    }     
                
                array_push($internosFaccao, array('faccionado' => $faccao, 'sexo' => $dadosInterno->sexo));
            }

                

        }
            
        $internos = array();
        
        //$internosFaccao = self::array_orderby($internosFaccao, 'religiao', SORT_ASC);

        foreach($internosFaccao as $array){
            $internos[$array["faccionado"]][$array["sexo"]][] = $array;
        }

        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
              
        
        return view('estatisticas.new.internoFaccao', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
        
    }
    
    public function internoFaccao(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        $internosFaccao = array();
        
        if($_POST){
            
            $internosData = self::getInternosUnidadeData($unidadePrisional, $data);

            foreach($internosData['internos'] as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $faccao = null;
                if($dadosInterno->faccao == "" && $dadosInterno->faccao == null)
                {
                    $faccao = "Não Informado ou Não Faccionado";
                                   
                }else{
                    $faccao = InternoController::getNomeFaccao($dadosInterno->faccao);
                    
                    }    
                
                array_push($internosFaccao, array('faccionado' => $faccao, 'sexo' => $dadosInterno->sexo));

            }


        }else{
            
            $internosGeral = DB::table('interno')->where('status', 'A')->pluck('id');
            
            foreach($internosGeral as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $faccao = null;
                if($dadosInterno->faccao == "" && $dadosInterno->faccao == null)
                {
                    $faccao = "Não Informado ou Não Faccionado";
                                   
                }else{
                    $faccao = $faccao = InternoController::getNomeFaccao($dadosInterno->faccao);;
                    
                    }     
                
                array_push($internosFaccao, array('faccionado' => $faccao, 'sexo' => $dadosInterno->sexo));
            }

                

        }
            
        $internos = array();
        
        //$internosFaccao = self::array_orderby($internosFaccao, 'religiao', SORT_ASC);

        foreach($internosFaccao as $array){
            $internos[$array["faccionado"]][$array["sexo"]][] = $array;
        }

        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        $faccao = true;
        
        return view('estatisticas.new.internoFaccao', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica','faccao'));
    }
    
    public function internoPericulosidade(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        $internosPericulosidade = array();
        
        if($_POST){
            
            $internosData = self::getInternosUnidadeData($unidadePrisional, $data);

            foreach($internosData['internos'] as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $periculosidade = null;
                if($dadosInterno->periculosidade == "" && $dadosInterno->periculosidade == null)
                {
                    $periculosidade = "Não Informado";
                                   
                }else{
                    $periculosidade = $dadosInterno->periculosidade;
                    
                    }    
                
                array_push($internosPericulosidade, array('periculosidade' => $periculosidade, 'sexo' => $dadosInterno->sexo));

            }


        }else{
            
            $internosGeral = DB::table('interno')->where('status', 'A')->pluck('id');
            
            foreach($internosGeral as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $periculosidade = null;
                if($dadosInterno->periculosidade == "" && $dadosInterno->periculosidade == null)
                {
                    $periculosidade = "Não Informado";
                                   
                }else{
                    $periculosidade = $dadosInterno->periculosidade;
                    
                    }    
                
                array_push($internosPericulosidade, array('periculosidade' => $periculosidade, 'sexo' => $dadosInterno->sexo));
            }

                
        }
            
        $internos = array();
        
        $internosPericulosidade = self::array_orderby($internosPericulosidade, 'periculosidade', SORT_ASC);

        foreach($internosPericulosidade as $array){
            $internos[$array["periculosidade"]][$array["sexo"]][] = $array;
        }

        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        return view('estatisticas.new.internoPericulosidade', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
    }
    
    public function internoLideranca(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        $internosLideranca = array();
        
        if($_POST){
            
            $internosData = self::getInternosUnidadeData($unidadePrisional, $data);

            foreach($internosData['internos'] as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $lideranca = null;
                if($dadosInterno->lideranca == "" && $dadosInterno->lideranca == null)
                {
                    $lideranca = "Não Informado";
                                   
                }else{
                    
                        $lideranca = $dadosInterno->lideranca == "S" ? "São Lideranças" : "Não São Lideranças";
                    
                    }    
                
                array_push($internosLideranca, array('lideranca' => $lideranca, 'sexo' => $dadosInterno->sexo));

            }


        }else{
            
            $internosGeral = DB::table('interno')->where('status', 'A')->pluck('id');
            
            foreach($internosGeral as $internoData)
            {
                $dadosInterno = InternoController::getDadosInterno($internoData)[0];

                $lideranca = null;
                if($dadosInterno->lideranca == "" && $dadosInterno->lideranca == null)
                {
                    $lideranca = "Não Informado";
                                   
                }else{
                    
                        $lideranca = $dadosInterno->lideranca == "S" ? "São Lideranças" : "Não São Lideranças";
                    
                    }    
                
                array_push($internosLideranca, array('lideranca' => $lideranca, 'sexo' => $dadosInterno->sexo));
            }

                

        }
            
        $internos = array();
        
        $internosLideranca = self::array_orderby($internosLideranca, 'lideranca', SORT_ASC);

        foreach($internosLideranca as $array){
            $internos[$array["lideranca"]][$array["sexo"]][] = $array;
        }

        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        return view('estatisticas.new.internoLideranca', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
    }
    
    public function internoCrimes(Request $request) {
                   
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        if($_POST){
            
            $internosData = self::getInternosUnidadeData($unidadePrisional, $data);
            
            $artigosCPB = DB::table('interno_processos')
                    ->leftJoin('interno', 'interno.id', '=', 'interno_processos.fk_interno')
                    ->leftJoin('interno_processos_dispositivo_legal', 'interno_processos_dispositivo_legal.id_processo','=', 'interno_processos.id')
                    ->leftJoin('lei_natureza', 'lei_natureza.id', '=', 'interno_processos_dispositivo_legal.artigo')
                    ->leftJoin('lei_titulo', 'lei_titulo.id', '=', 'lei_natureza.fk_titulo')
                    ->where('interno.status', 'A')
                    ->whereIn('interno_processos.fk_interno', $internosData["internos"])
                    ->whereIn('lei_natureza.fk_titulo',[1,3,6,9,10,11])
                    ->select('interno.sexo','interno_processos.*','interno_processos_dispositivo_legal.*','lei_natureza.*','lei_natureza.*','lei_titulo.nome AS nomeTitulo')
                    ->orderBy('lei_titulo.nome', 'ASC')
                    ->get();
            
        $internosCPB = array();
        
        foreach(json_decode($artigosCPB, TRUE) as $array){
            $internosCPB[$array["nomeTitulo"]][$array["artigo"]][$array["sexo"]][] = $array;
        }

            
            $artigosLeisExtra = DB::table('interno_processos')
                    ->leftJoin('interno', 'interno.id', '=', 'interno_processos.fk_interno')
                    ->leftJoin('interno_processos_dispositivo_legal', 'interno_processos_dispositivo_legal.id_processo','=', 'interno_processos.id')
                    ->leftJoin('lei_natureza', 'lei_natureza.id', '=', 'interno_processos_dispositivo_legal.artigo')
                    ->leftJoin('lei', 'lei.id', '=', 'lei_natureza.fk_legislacao')
                    ->where('interno.status', 'A')
                    ->whereIn('interno_processos.fk_interno', $internosData["internos"])
                    ->whereIn('lei_natureza.fk_legislacao',[24,22,21,19,10,11,71,20,29])
                    ->select('interno.sexo','interno_processos.*','interno_processos_dispositivo_legal.*','lei_natureza.*','lei.nome AS nomeLei')
                    ->orderBy('lei.nome', 'ASC')
                    ->get();
            
        $internosLeisExtra = array();
        
        foreach(json_decode($artigosLeisExtra, TRUE) as $array){
            $internosLeisExtra[$array["nomeLei"]][$array["artigo"]][$array["sexo"]][] = $array;
        }
            
            //dd($internosLeisExtra);
            
        }else{
            
            $artigosCPB = DB::table('interno_processos')
                    ->leftJoin('interno', 'interno.id', '=', 'interno_processos.fk_interno')
                    ->leftJoin('interno_processos_dispositivo_legal', 'interno_processos_dispositivo_legal.id_processo','=', 'interno_processos.id')
                    ->leftJoin('lei_natureza', 'lei_natureza.id', '=', 'interno_processos_dispositivo_legal.artigo')
                    ->leftJoin('lei_titulo', 'lei_titulo.id', '=', 'lei_natureza.fk_titulo')
                    ->where('interno.status', 'A')
                    ->whereIn('lei_natureza.fk_titulo',[1,3,6,9,10,11,])
                    ->select('interno.sexo','interno_processos.*','interno_processos_dispositivo_legal.*','lei_natureza.*','lei_natureza.*','lei_titulo.nome AS nomeTitulo')
                    ->orderBy('lei_titulo.nome', 'ASC')
                    ->get();
            
        $internosCPB = array();
        
        foreach(json_decode($artigosCPB, TRUE) as $array){
            $internosCPB[$array["nomeTitulo"]][$array["artigo"]][$array["sexo"]][] = $array;
        }
            
            $artigosLeisExtra = DB::table('interno_processos')
                    ->leftJoin('interno', 'interno.id', '=', 'interno_processos.fk_interno')
                    ->leftJoin('interno_processos_dispositivo_legal', 'interno_processos_dispositivo_legal.id_processo','=', 'interno_processos.id')
                    ->leftJoin('lei_natureza', 'lei_natureza.id', '=', 'interno_processos_dispositivo_legal.artigo')
                    ->leftJoin('lei', 'lei.id', '=', 'lei_natureza.fk_legislacao')
                    ->where('interno.status', 'A')
                    ->whereIn('lei_natureza.fk_legislacao',[24,22,21,19,10,11,71,20,29])
                    ->select('interno.sexo','interno_processos.*','interno_processos_dispositivo_legal.*','lei_natureza.*','lei.nome AS nomeLei')
                    ->orderBy('lei.nome', 'ASC')
                    ->get();
            
        $internosLeisExtra = array();
        
        foreach(json_decode($artigosLeisExtra, TRUE) as $array){
            $internosLeisExtra[$array["nomeLei"]][$array["artigo"]][$array["sexo"]][] = $array;
        }
            
            //dd($internosLeisExtra);
            
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        return view('estatisticas.new.internoCrimesSisdepen', compact('unidades','internosLeisExtra','internosCPB','lotacaoServidor','dataEstatistica','unidadeEstatistica'));

    }
    
    public function internoCrimesGrupo(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        if($_POST){
            
            $internosData = self::getInternosUnidadeData($unidadePrisional, $data);
            
            $artigos = DB::table('interno_processos_dispositivo_legal')
                    ->leftJoin('lei_grupo','lei_grupo.id','=','interno_processos_dispositivo_legal.fk_grupo')
                    ->leftJoin('interno_processos','interno_processos.id','=','interno_processos_dispositivo_legal.id_processo')
                    ->leftJoin('interno','interno.id','=','interno_processos.fk_interno')
                    ->where('interno_processos.status', 'A')
                    ->where('interno.status', 'A')
                    ->whereIn('interno.id', $internosData['internos'])
                    ->select('interno.sexo','interno_processos.*','interno_processos_dispositivo_legal.*','lei_grupo.nome AS nomeGrupo')
                    ->get();

            $artigos = json_decode($artigos, true);
            
        }else{

            $artigos = DB::table('interno_processos_dispositivo_legal')
                    ->leftJoin('lei_grupo','lei_grupo.id','=','interno_processos_dispositivo_legal.fk_grupo')
                    ->leftJoin('interno_processos','interno_processos.id','=','interno_processos_dispositivo_legal.id_processo')
                    ->leftJoin('interno','interno.id','=','interno_processos.fk_interno')
                    ->where('interno_processos.status', 'A')
                    ->where('interno.status', 'A')
                    ->select('interno.sexo','interno_processos.*','interno_processos_dispositivo_legal.*','lei_grupo.nome AS nomeGrupo')
                    ->get();

            $artigos = json_decode($artigos, true);
        }
        
        $internos = array();
        
        $artigos = self::array_orderby($artigos, 'nomeGrupo', SORT_ASC);

        foreach($artigos as $array){
            $internos[$array["nomeGrupo"]][$array["sexo"]][] = $array;
        }

        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }

        return view('estatisticas.new.internoCrimeGrupo', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));

    }

    public function internoTransferencias(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
            $dataFinal = $request->get('dataFinal');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        $movimentacoesArray = array();
        
        if($_POST){
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                ->leftJoin('interno','interno.id','=','movimentacao_interno.fk_interno')
                ->where('movimentacao_interno.status', 'Finalizada')
                ->whereIn('tipo', ['transferencia','transferenciaentrada']);
            
            if(!is_null($data))
            {
                $movimentacaoDados->whereDate('movimentacao.data', '>=', Tdata::TdataMysql($data));
            }
            
            if(!is_null($dataFinal))
            {
                $movimentacaoDados->whereDate('movimentacao.data', '<=', Tdata::TdataMysql($dataFinal));
            }
            
            $movimentacaoDados = $movimentacaoDados->get();

                $movimentacoesArray = array();

                foreach($movimentacaoDados as $mov)
                {

                        $tipoMov = null;
                        switch ($mov->tipo){

                            case "transferencia":
                                $tipoMov = "Transferências para Outros Estabelecimentos Prisionais";
                                break;

                            case "transferenciaentrada":
                                $tipoMov = "Inclusões por Transferências de Outras Unidades Prisionais";
                                break;

                        }
                        array_push($movimentacoesArray, array('tipo' => $tipoMov, 'sexo'=>$mov->sexo));

                }
            
        }else{
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                ->leftJoin('interno','interno.id','=','movimentacao_interno.fk_interno')
                ->where('movimentacao_interno.status', 'Finalizada')
                ->whereIn('tipo', ['transferencia','transferenciaentrada'])
                ->get();

                $movimentacoesArray = array();

                foreach($movimentacaoDados as $mov)
                {

                        $tipoMov = null;
                        switch ($mov->tipo){

                            case "transferencia":
                                $tipoMov = "Transferências para Outros Estabelecimentos Prisionais";
                                break;

                            case "transferenciaentrada":
                                $tipoMov = "Inclusões por Transferências de Outras Unidades Prisionais";
                                break;

                        }
                        array_push($movimentacoesArray, array('tipo' => $tipoMov, 'sexo'=>$mov->sexo));

                }
        }
            
        $internos = array();
        
        foreach($movimentacoesArray as $array){
            $internos[$array["tipo"]][$array["sexo"]][] = $array;
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        return view('estatisticas.new.internoTransferencia', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));

    }
    
    public function internoFilhosQuantidade(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        $internosQtdFilhos = array();

        if($_POST){
            
            $internosData = self::getInternosUnidadeData($unidadePrisional, $data);
            
            foreach($internosData['internos'] as $idInterno)
            {
                $dadosInterno = InternoController::getDadosInterno($idInterno);

                if(count($dadosInterno) > 0){

                    $qtdFilhos = DB::table('filhos')->where('fk_interno', $idInterno)->count();

                    if($dadosInterno[0]->status == "A"){
                        
                        $qtdFilhos = DB::table('filhos')->where('fk_interno', $idInterno)->count();

                        array_push($internosQtdFilhos, array(
                            'id' => $idInterno,
                            'quantidade' => $qtdFilhos,
                            'sexo' => $dadosInterno[0]->sexo
                        ));
                    }

                }
            }
            
        }else{
            
            $filhosDados = DB::table('filhos')->pluck('fk_interno');

            foreach($filhosDados as $idInterno)
            {
                $dadosInterno = InternoController::getDadosInterno($idInterno);

                if(count($dadosInterno) > 0){

                    if($dadosInterno[0]->status == "A"){
                        $qtdFilhos = DB::table('filhos')->where('fk_interno', $idInterno)->count();

                        array_push($internosQtdFilhos, array(
                            'id' => $idInterno,
                            'quantidade' => $qtdFilhos,
                            'sexo' => $dadosInterno[0]->sexo
                        ));
                    }

                }
            }
        }
        
        $internos = array();
        
        $internosQtdFilhos = self::array_orderby($internosQtdFilhos, 'quantidade', SORT_ASC);

        foreach($internosQtdFilhos as $array){
            $internos[$array["quantidade"]][$array["sexo"]][$array["id"]][] = $array;
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }

        return view('estatisticas.new.internoFilhosQuantidade', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));

        
    }
    
    public function internoFilhosFaixaEtaria(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        $internosFilhosFaixaEtaria = array();

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        if($_POST){
            
            $internosData = self::getInternosUnidadeData($unidadePrisional, $data);
            

            $filhosDados = DB::table('filhos')->whereIn('fk_interno', $internosData['internos'])->where('na_unidade_prisional', 'S')->get();
            
            foreach($filhosDados as $dados)
            {
                $dadosInterno = InternoController::getDadosInterno($dados->fk_interno);

                if(count($dadosInterno) > 0){
                    
                    if($dadosInterno[0]->status == "A"){
                        
                        if(!is_null($dados->data_nascimento) && !empty($dados->data_nascimento)){

                            $date1 = new \DateTime($dados->data_nascimento);
                            $date2 = new \DateTime(Tdata::TdataMysql($data));
                            $idade = $date1->diff($date2)->m + ($date1->diff($date2)->y*12);

                            if($idade >= 0 && $idade <= 6)
                            {
                                array_push($internosFilhosFaixaEtaria, array('faixaEtaria' => '0 a 6 meses', 'ordem'=> 1));
                            }

                            if($idade >= 7 && $idade <= 12)
                            {
                                array_push($internosFilhosFaixaEtaria, array('faixaEtaria' => 'Mais de 6 meses a 1 ano', 'ordem'=> 2));
                            }

                            if($idade >= 13 && $idade <= 24)
                            {
                                array_push($internosFilhosFaixaEtaria, array('faixaEtaria' => 'Mais de 1 ano a 2 anos', 'ordem'=>3));
                            }

                            if($idade >= 25 && $idade <= 36)
                            {
                                array_push($internosFilhosFaixaEtaria, array('faixaEtaria' => 'Mais de 2 a 3 anos', 'ordem'=>4));
                            }

                            if($idade >= 37)
                            {
                                array_push($internosFilhosFaixaEtaria, array('faixaEtaria' => 'Mais de 3 anos', 'ordem'=>5));
                            }

                        }
                    }
                }
            }
                
        }else{

            $filhosDados = DB::table('filhos')->where('na_unidade_prisional', 'S')->get();

            foreach($filhosDados as $dados)
            {
                $dadosInterno = InternoController::getDadosInterno($dados->fk_interno);

                if(count($dadosInterno) > 0){
                    
                    if($dadosInterno[0]->status == "A"){
                                           
                        if(!is_null($dados->data_nascimento) && !empty($dados->data_nascimento)){

                            $date1 = new \DateTime($dados->data_nascimento);
                            $date2 = new \DateTime($data);
                            $idade = $date1->diff($date2)->m + ($date1->diff($date2)->y*12);

                            if($idade >= 0 && $idade <= 6)
                            {
                                array_push($internosFilhosFaixaEtaria, array('faixaEtaria' => '0 a 6 meses', 'ordem'=> 1));
                            }

                            if($idade >= 7 && $idade <= 12)
                            {
                                array_push($internosFilhosFaixaEtaria, array('faixaEtaria' => 'Mais de 6 meses a 1 ano', 'ordem'=> 2));
                            }

                            if($idade >= 13 && $idade <= 24)
                            {
                                array_push($internosFilhosFaixaEtaria, array('faixaEtaria' => 'Mais de 1 ano a 2 anos', 'ordem'=>3));
                            }

                            if($idade >= 25 && $idade <= 36)
                            {
                                array_push($internosFilhosFaixaEtaria, array('faixaEtaria' => 'Mais de 2 a 3 anos', 'ordem'=>4));
                            }

                            if($idade >= 37)
                            {
                                array_push($internosFilhosFaixaEtaria, array('faixaEtaria' => 'Mais de 3 anos', 'ordem'=>5));
                            }

                        }
                    }
                }
            } 
        }

        $internos = array();
        
        $internosFilhosFaixaEtaria = self::array_orderby($internosFilhosFaixaEtaria, 'ordem', SORT_ASC);
        
        foreach($internosFilhosFaixaEtaria as $array){
            $internos[$array["faixaEtaria"]][] = $array;
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        return view('estatisticas.new.internoFilhosFaixaEtaria', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));

    }

    public function internoDeficiencia(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        $internosFilhosFaixaEtaria = array();

        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        $qtdInternos = DB::table('interno')->where('status', 'A')->count();
        
        if($_POST){
            
            $internosData = self::getInternosUnidadeData($unidadePrisional, $data);
            
            $deficienciaDados = DB::table('interno_deficiencia')
                ->leftJoin('interno', 'interno.id', 'interno_deficiencia.fk_interno')
                ->whereIn('interno.id', $internosData['internos'])
                ->select('interno.sexo', 'interno_deficiencia.*')
                ->get();

        }else{
            $deficienciaDados = DB::table('interno_deficiencia')
                ->leftJoin('interno', 'interno.id', 'interno_deficiencia.fk_interno')
                ->where('interno.status', 'A')
                ->select('interno.sexo', 'interno_deficiencia.*')
                ->get();
        }
        
        $deficienciaDados = json_decode($deficienciaDados, true);
        
        $internos = array();
        
        $deficienciaDados = self::array_orderby($deficienciaDados, 'tipo_deficiencia', SORT_ASC);
        
        foreach($deficienciaDados as $array){
            $internos[$array["tipo_deficiencia"]][$array["sexo"]][] = $array;
        }

        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        return view('estatisticas.new.internoDeficiencia', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica','qtdInternos'));
                
        
    }
    
    public function internoVisitantes(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        if($_POST){
            
            $internosData = self::getInternosUnidadeData($unidadePrisional, $data);
            
            $internosVisitantes = DB::table('visita_interno')
                ->leftJoin('interno', 'interno.id', 'visita_interno.fk_interno')
                ->whereIn('interno.id', $internosData['internos'])
                ->where('visita_interno.status', 'A')
                ->select('interno.sexo','visita_interno.status')
                ->groupBy('fk_interno')
                ->get();
            
        }else{
            
            $internosVisitantes = DB::table('visita_interno')
                    ->leftJoin('interno', 'interno.id', 'visita_interno.fk_interno')
                    ->where('interno.status', 'A')
                    ->where('visita_interno.status', 'A')
                    ->select('interno.sexo','visita_interno.status')
                    ->groupBy('fk_interno')
                    ->get();
        }
        
        $internos = array();
        
        foreach(json_decode($internosVisitantes, true) as $array){
            $internos[$array["status"]][$array["sexo"]][] = $array;
        }

        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }

        return view('estatisticas.new.internoQuantidadeVisitantes', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
        
    }

    public function internoQuantidadeVisitas(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
            $dataFinal = $request->get('dataFinal');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();

        if($_POST){
            
            $quantidadeVisitas = DB::table('controle_acesso_visita_interno')
                    ->leftJoin('interno', 'interno.id', 'controle_acesso_visita_interno.fk_interno')
                    ->leftJoin('unidade', 'unidade.id', 'controle_acesso_visita_interno.unidade')
                    ->where('unidade', $unidadePrisional);
            
            if(!is_null($data))
            {
                $quantidadeVisitas->whereDate('controle_acesso_visita_interno.data', '>=', Tdata::TdataMysql($data));
            }
            
            if(!is_null($dataFinal))
            {
                $quantidadeVisitas->whereDate('controle_acesso_visita_interno.data', '<=', Tdata::TdataMysql($dataFinal));
            }
            
            $quantidadeVisitas = $quantidadeVisitas->select('interno.sexo','unidade.sigla','controle_acesso_visita_interno.*')->get();


        }else{
            $quantidadeVisitas = DB::table('controle_acesso_visita_interno')
                    ->leftJoin('interno', 'interno.id', 'controle_acesso_visita_interno.fk_interno')
                    ->leftJoin('unidade', 'unidade.id', 'controle_acesso_visita_interno.unidade')
                    ->select('interno.sexo','unidade.sigla','controle_acesso_visita_interno.*')
                    ->get();
        }
        
        
        $internos = array();
        
        $quantidadeVisitas = self::array_orderby(json_decode($quantidadeVisitas, true), 'sigla', SORT_ASC);
        
        foreach($quantidadeVisitas as $array){
            $internos[$array["sigla"]][$array["sexo"]][] = $array;
        }

        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        return view('estatisticas.new.internoQuantidadeVisitas', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
        
    }
    
    public function internoIdentidadeGenero(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
       
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
            $dataFinal = $request->get('dataFinal');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        if($_POST){
            
            $internosData = self::getInternosUnidadeData($unidadePrisional, $data);
            
            $internosIdGen = DB::table("interno")
                    ->whereIn('id', $internosData["internos"])
                    ->whereNotNull('identidade_genero')
                    ->where('identidade_genero', '!=',"")
                    ->select('sexo','identidade_genero')
                    ->get();
        }else{
            $internosIdGen = DB::table("interno")->where('status', 'A')->whereNotNull('identidade_genero')->where('identidade_genero', '!=',"")->select('sexo','identidade_genero')->get();

        }
        
        $internos = array();
        
        $internosIdGen = self::array_orderby(json_decode($internosIdGen, true), 'identidade_genero', SORT_ASC);
        
        foreach($internosIdGen as $array){
            $internos[$array["identidade_genero"]][$array["sexo"]][] = $array;
        }

        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Prisional";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
            
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
        }
        
        return view('estatisticas.new.internoIdentidadeGenero', compact('unidades','internos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));

        
    }
    
    
    /**
     * MOVIMENTACOES
     */
    
    public function movimentacoes(Request $request, $tipo) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
            $dataFinal = $request->get('dataFinal');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Penitenciário";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
            
            if(!is_null($request->get('dataFinal')))
            {
                $dataEstatistica = $request->get('data')." até ".$request->get('dataFinal');
            }
        }
        
        switch ($tipo){
            
            case "entradas":
                $movimentacoes = $this->_movEntradas($_POST, $request);
                return view('estatisticas.movimentacoes.entradasUnidade', compact('unidades','movimentacoes','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
                
                break;
                
            case "novosCadastros":
                $movimentacoes = $this->_movNovosCadastros($_POST, $request);
                return view('estatisticas.movimentacoes.novosCadastros', compact('unidades','movimentacoes','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
                
                break;
                
            case "mudancaCela":
                $movimentacoes = $this->_movMudancaCela($_POST, $request);
                return view('estatisticas.movimentacoes.mudancaCela', compact('unidades','movimentacoes','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
                
                break;
                
            case "progressao":
                $movimentacoes = $this->_movProgressao($_POST, $request);
                return view('estatisticas.movimentacoes.progressao', compact('unidades','movimentacoes','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
                
                break;
                
            case "regressao":
                $movimentacoes = $this->_movRegressao($_POST, $request);
                return view('estatisticas.movimentacoes.progressao', compact('unidades','movimentacoes','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
                
                break;
                
            case "transferencia":
                $movimentacoes = $this->_movTransferencia($_POST, $request);
                return view('estatisticas.movimentacoes.transferencia', compact('unidades','movimentacoes','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
                
                break;
                
            case "alvaras":
                $movimentacoes = $this->_movAlvaras($_POST, $request);
                return view('estatisticas.movimentacoes.alvaras', compact('unidades','movimentacoes','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
                
                break;
                
            case "livramentoCondicional":
                $movimentacoes = $this->_movLivramentoCondicional($_POST, $request);
                return view('estatisticas.movimentacoes.livramentoCondicional', compact('unidades','movimentacoes','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
                
                break;
                
            case "fuga":
                $movimentacoes = $this->_movFuga($_POST, $request);
                return view('estatisticas.movimentacoes.fuga', compact('unidades','movimentacoes','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
                
                break;
                
            case "recambiamento":
                $movimentacoes = $this->_movRecambiamento($_POST, $request);
                return view('estatisticas.movimentacoes.recambiamento', compact('unidades','movimentacoes','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
                
                break;
                
            case "obito":
                $movimentacoes = $this->_movObito($_POST, $request);
                return view('estatisticas.movimentacoes.obito', compact('unidades','movimentacoes','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
                
                break;
                
            case "extincaoPunibilidade":
                $movimentacoes = $this->_movExtincaoPunibilidade($_POST, $request);
                return view('estatisticas.movimentacoes.extincaoPunibilidade', compact('unidades','movimentacoes','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
                
                break;
                
            case "extradicao":
                $movimentacoes = $this->_movExtradicao($_POST, $request);
                return view('estatisticas.movimentacoes.extradicao', compact('unidades','movimentacoes','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
                
                break;
                
            case "prisaoDomiciliar":
                $movimentacoes = $this->_movPrisaoDomiciliar($_POST, $request);
                return view('estatisticas.movimentacoes.prisaoDomiciliar', compact('unidades','movimentacoes','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
                
                break;
                
        }
        
    }
    
    function _movPrisaoDomiciliar($post, $request){
        //dd($post);
        if($post != []){
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['prisaodomiciliar']);
                    if(!is_null($request->get('unidade_prisional'))){
                        $movimentacaoDados->where('movimentacao_interno.up_anterior', $request->get('unidade_prisional'));
                    }
                    if(!is_null($request->get('data'))){
                        $movimentacaoDados->whereDate('data', '>=', Tdata::TdataMysql($request->get('data')));
                    }
                    if(!is_null($request->get('dataFinal'))){
                        $movimentacaoDados->whereDate('data', '<=', Tdata::TdataMysql($request->get('dataFinal')));
                    }
                   $movimentacaoDados = $movimentacaoDados->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }else{
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['prisaodomiciliar'])
                    ->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }
            
            $movimentacoesArray = array();
            $tipoEntrdada = "";
            foreach($movimentacaoDados as $mov)
            {
                array_push($movimentacoesArray, array('tipo' => $mov->tipo,'unidade' => $mov->sigla));
            }
            
            $movimentacoes = array();

            $movimentacoesArray = self::array_orderby($movimentacoesArray, 'unidade', SORT_ASC);

            foreach($movimentacoesArray as $array){
                $movimentacoes[$array["unidade"]][] = $array;
            }

            return $movimentacoes;
    }
    
    function _movExtradicao($post, $request){
        //dd($post);
        if($post != []){
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['extradicao']);
                    if(!is_null($request->get('unidade_prisional'))){
                        $movimentacaoDados->where('movimentacao_interno.up_anterior', $request->get('unidade_prisional'));
                    }
                    if(!is_null($request->get('data'))){
                        $movimentacaoDados->whereDate('data', '>=', Tdata::TdataMysql($request->get('data')));
                    }
                    if(!is_null($request->get('dataFinal'))){
                        $movimentacaoDados->whereDate('data', '<=', Tdata::TdataMysql($request->get('dataFinal')));
                    }
                   $movimentacaoDados = $movimentacaoDados->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }else{
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['extradicao'])
                    ->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }
            
            $movimentacoesArray = array();
            $tipoEntrdada = "";
            foreach($movimentacaoDados as $mov)
            {
                array_push($movimentacoesArray, array('tipo' => $mov->tipo,'unidade' => $mov->sigla));
            }
            
            $movimentacoes = array();

            $movimentacoesArray = self::array_orderby($movimentacoesArray, 'unidade', SORT_ASC);

            foreach($movimentacoesArray as $array){
                $movimentacoes[$array["unidade"]][] = $array;
            }

            return $movimentacoes;
    }
    
    function _movExtincaoPunibilidade($post, $request){
        //dd($post);
        if($post != []){
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['extincaopunibilidade']);
                    if(!is_null($request->get('unidade_prisional'))){
                        $movimentacaoDados->where('movimentacao_interno.up_anterior', $request->get('unidade_prisional'));
                    }
                    if(!is_null($request->get('data'))){
                        $movimentacaoDados->whereDate('data', '>=', Tdata::TdataMysql($request->get('data')));
                    }
                    if(!is_null($request->get('dataFinal'))){
                        $movimentacaoDados->whereDate('data', '<=', Tdata::TdataMysql($request->get('dataFinal')));
                    }
                   $movimentacaoDados = $movimentacaoDados->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }else{
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['extincaopunibilidade'])
                    ->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }
            
            $movimentacoesArray = array();
            $tipoEntrdada = "";
            foreach($movimentacaoDados as $mov)
            {
                array_push($movimentacoesArray, array('tipo' => $mov->tipo,'unidade' => $mov->sigla));
            }
            
            $movimentacoes = array();

            $movimentacoesArray = self::array_orderby($movimentacoesArray, 'unidade', SORT_ASC);

            foreach($movimentacoesArray as $array){
                $movimentacoes[$array["unidade"]][] = $array;
            }

            return $movimentacoes;
    }
    
    function _movObito($post, $request){
        //dd($post);
        if($post != []){
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['obito']);
                    if(!is_null($request->get('unidade_prisional'))){
                        $movimentacaoDados->where('movimentacao_interno.up_anterior', $request->get('unidade_prisional'));
                    }
                    if(!is_null($request->get('data'))){
                        $movimentacaoDados->whereDate('data', '>=', Tdata::TdataMysql($request->get('data')));
                    }
                    if(!is_null($request->get('dataFinal'))){
                        $movimentacaoDados->whereDate('data', '<=', Tdata::TdataMysql($request->get('dataFinal')));
                    }
                   $movimentacaoDados = $movimentacaoDados->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }else{
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['obito'])
                    ->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }
            
            $movimentacoesArray = array();
            $tipoEntrdada = "";
            foreach($movimentacaoDados as $mov)
            {
                array_push($movimentacoesArray, array('tipo' => $mov->tipo,'unidade' => $mov->sigla));
            }
            
            $movimentacoes = array();

            $movimentacoesArray = self::array_orderby($movimentacoesArray, 'unidade', SORT_ASC);

            foreach($movimentacoesArray as $array){
                $movimentacoes[$array["unidade"]][] = $array;
            }

            return $movimentacoes;
    }
    
    function _movRecambiamento($post, $request){
        //dd($post);
        if($post != []){
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['recambiamento']);
                    if(!is_null($request->get('unidade_prisional'))){
                        $movimentacaoDados->where('movimentacao_interno.up_anterior', $request->get('unidade_prisional'));
                    }
                    if(!is_null($request->get('data'))){
                        $movimentacaoDados->whereDate('data', '>=', Tdata::TdataMysql($request->get('data')));
                    }
                    if(!is_null($request->get('dataFinal'))){
                        $movimentacaoDados->whereDate('data', '<=', Tdata::TdataMysql($request->get('dataFinal')));
                    }
                   $movimentacaoDados = $movimentacaoDados->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }else{
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['recambiamento'])
                    ->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }
            
            $movimentacoesArray = array();
            $tipoEntrdada = "";
            foreach($movimentacaoDados as $mov)
            {
                array_push($movimentacoesArray, array('tipo' => $mov->tipo,'unidade' => $mov->sigla));
            }
            
            $movimentacoes = array();

            $movimentacoesArray = self::array_orderby($movimentacoesArray, 'unidade', SORT_ASC);

            foreach($movimentacoesArray as $array){
                $movimentacoes[$array["unidade"]][] = $array;
            }

            return $movimentacoes;
    }
    
    function _movFuga($post, $request){
        //dd($post);
        if($post != []){
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['fuga']);
                    if(!is_null($request->get('unidade_prisional'))){
                        $movimentacaoDados->where('movimentacao_interno.up_anterior', $request->get('unidade_prisional'));
                    }
                    if(!is_null($request->get('data'))){
                        $movimentacaoDados->whereDate('data', '>=', Tdata::TdataMysql($request->get('data')));
                    }
                    if(!is_null($request->get('dataFinal'))){
                        $movimentacaoDados->whereDate('data', '<=', Tdata::TdataMysql($request->get('dataFinal')));
                    }
                   $movimentacaoDados = $movimentacaoDados->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }else{
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['fuga'])
                    ->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }
            
            $movimentacoesArray = array();
            $tipoEntrdada = "";
            foreach($movimentacaoDados as $mov)
            {
                array_push($movimentacoesArray, array('tipo' => $mov->tipo,'unidade' => $mov->sigla));
            }
            
            $movimentacoes = array();

            $movimentacoesArray = self::array_orderby($movimentacoesArray, 'unidade', SORT_ASC);

            foreach($movimentacoesArray as $array){
                $movimentacoes[$array["unidade"]][] = $array;
            }

            return $movimentacoes;
    }
    
    function _movLivramentoCondicional($post, $request){
        //dd($post);
        if($post != []){
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['livramentocondicional']);
                    if(!is_null($request->get('unidade_prisional'))){
                        $movimentacaoDados->where('movimentacao_interno.up_anterior', $request->get('unidade_prisional'));
                    }
                    if(!is_null($request->get('data'))){
                        $movimentacaoDados->whereDate('data', '>=', Tdata::TdataMysql($request->get('data')));
                    }
                    if(!is_null($request->get('dataFinal'))){
                        $movimentacaoDados->whereDate('data', '<=', Tdata::TdataMysql($request->get('dataFinal')));
                    }
                   $movimentacaoDados = $movimentacaoDados->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }else{
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['livramentocondicional'])
                    ->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }
            
            $movimentacoesArray = array();
            $tipoEntrdada = "";
            foreach($movimentacaoDados as $mov)
            {
                array_push($movimentacoesArray, array('tipo' => $mov->tipo,'unidade' => $mov->sigla));
            }
            
            $movimentacoes = array();

            $movimentacoesArray = self::array_orderby($movimentacoesArray, 'unidade', SORT_ASC);

            foreach($movimentacoesArray as $array){
                $movimentacoes[$array["unidade"]][] = $array;
            }

            return $movimentacoes;
    }
    
    function _movAlvaras($post, $request){
        //dd($post);
        if($post != []){
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['alvara']);
                    if(!is_null($request->get('unidade_prisional'))){
                        $movimentacaoDados->where('movimentacao_interno.up_anterior', $request->get('unidade_prisional'));
                    }
                    if(!is_null($request->get('data'))){
                        $movimentacaoDados->whereDate('data', '>=', Tdata::TdataMysql($request->get('data')));
                    }
                    if(!is_null($request->get('dataFinal'))){
                        $movimentacaoDados->whereDate('data', '<=', Tdata::TdataMysql($request->get('dataFinal')));
                    }
                   $movimentacaoDados = $movimentacaoDados->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }else{
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['alvara'])
                    ->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }
            
            $movimentacoesArray = array();
            $tipoEntrdada = "";
            foreach($movimentacaoDados as $mov)
            {
                array_push($movimentacoesArray, array('tipo' => $mov->tipo,'unidade' => $mov->sigla));
            }
            
            $movimentacoes = array();

            $movimentacoesArray = self::array_orderby($movimentacoesArray, 'unidade', SORT_ASC);

            foreach($movimentacoesArray as $array){
                $movimentacoes[$array["unidade"]][] = $array;
            }

            return $movimentacoes;
    }
    
    function _movTransferencia($post, $request){
        //dd($post);
        if($post != []){
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['transferencia']);
                    if(!is_null($request->get('unidade_prisional'))){
                        $movimentacaoDados->where('movimentacao_interno.up_anterior', $request->get('unidade_prisional'));
                    }
                    if(!is_null($request->get('data'))){
                        $movimentacaoDados->whereDate('data', '>=', Tdata::TdataMysql($request->get('data')));
                    }
                    if(!is_null($request->get('dataFinal'))){
                        $movimentacaoDados->whereDate('data', '<=', Tdata::TdataMysql($request->get('dataFinal')));
                    }
                   $movimentacaoDados = $movimentacaoDados->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }else{
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['transferencia'])
                    ->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }
            
            $movimentacoesArray = array();
            $tipoEntrdada = "";
            foreach($movimentacaoDados as $mov)
            {
                array_push($movimentacoesArray, array('tipo' => $mov->tipo,'unidade' => $mov->sigla));
            }
            
            $movimentacoes = array();

            $movimentacoesArray = self::array_orderby($movimentacoesArray, 'unidade', SORT_ASC);

            foreach($movimentacoesArray as $array){
                $movimentacoes[$array["unidade"]][] = $array;
            }

            return $movimentacoes;
    }
    
    function _movRegressao($post, $request){
        //dd($post);
        if($post != []){
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['regressaofechado','regressaosemiaberto']);
                    if(!is_null($request->get('unidade_prisional'))){
                        $movimentacaoDados->where('movimentacao.up_anterior', $request->get('unidade_prisional'));
                    }
                    if(!is_null($request->get('data'))){
                        $movimentacaoDados->whereDate('data', '>=', Tdata::TdataMysql($request->get('data')));
                    }
                    if(!is_null($request->get('dataFinal'))){
                        $movimentacaoDados->whereDate('data', '<=', Tdata::TdataMysql($request->get('dataFinal')));
                    }
                   $movimentacaoDados = $movimentacaoDados->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }else{
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['regressaofechado','regressaosemiaberto'])
                    ->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }
            
            $movimentacoesArray = array();
            $tipoEntrdada = "";
            foreach($movimentacaoDados as $mov)
            {
                //$tipoEntrdada = $mov->tipo == "entradaunidadeprisional" ? "entrada" : "entrada";
                
                array_push($movimentacoesArray, array('tipo' => $mov->tipo,'unidade' => $mov->sigla));
            }
            
            $movimentacoes = array();

            $movimentacoesArray = self::array_orderby($movimentacoesArray, 'unidade', SORT_ASC);

            foreach($movimentacoesArray as $array){
                $movimentacoes[$array["unidade"]][] = $array;
            }

            return $movimentacoes;
    }
    
    function _movProgressao($post, $request){
        //dd($post);
        if($post != []){
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['progressaoaberto','progressaosemiaberto']);
                    if(!is_null($request->get('unidade_prisional'))){
                        $movimentacaoDados->where('movimentacao.up_anterior', $request->get('unidade_prisional'));
                    }
                    if(!is_null($request->get('data'))){
                        $movimentacaoDados->whereDate('data', '>=', Tdata::TdataMysql($request->get('data')));
                    }
                    if(!is_null($request->get('dataFinal'))){
                        $movimentacaoDados->whereDate('data', '<=', Tdata::TdataMysql($request->get('dataFinal')));
                    }
                   $movimentacaoDados = $movimentacaoDados->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }else{
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao_interno.up_anterior')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['progressaoaberto','progressaosemiaberto'])
                    ->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }
            
            $movimentacoesArray = array();
            $tipoEntrdada = "";
            foreach($movimentacaoDados as $mov)
            {
                $tipoEntrdada = $mov->tipo == "entradaunidadeprisional" ? "entrada" : "entrada";
                
                array_push($movimentacoesArray, array('tipo' => $tipoEntrdada,'unidade' => $mov->sigla));
            }
            
            $movimentacoes = array();

            $movimentacoesArray = self::array_orderby($movimentacoesArray, 'unidade', SORT_ASC);

            foreach($movimentacoesArray as $array){
                $movimentacoes[$array["unidade"]][] = $array;
            }

            return $movimentacoes;
    }
    
    function _movMudancaCela($post, $request){
        //dd($post);
        if($post != []){
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao.unidade_destino')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['mudancacela']);
                    if(!is_null($request->get('unidade_prisional'))){
                        $movimentacaoDados->where('movimentacao.unidade_destino', $request->get('unidade_prisional'));
                    }
                    if(!is_null($request->get('data'))){
                        $movimentacaoDados->whereDate('data', '>=', Tdata::TdataMysql($request->get('data')));
                    }
                    if(!is_null($request->get('dataFinal'))){
                        $movimentacaoDados->whereDate('data', '<=', Tdata::TdataMysql($request->get('dataFinal')));
                    }
                   $movimentacaoDados = $movimentacaoDados->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }else{
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao.unidade_destino')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['mudancacela'])
                    ->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }
            
            $movimentacoesArray = array();
            $tipoEntrdada = "";
            foreach($movimentacaoDados as $mov)
            {
                $tipoEntrdada = $mov->tipo == "entradaunidadeprisional" ? "entrada" : "entrada";
                
                array_push($movimentacoesArray, array('tipo' => $tipoEntrdada,'unidade' => $mov->sigla));
            }
            
            $movimentacoes = array();

            $movimentacoesArray = self::array_orderby($movimentacoesArray, 'unidade', SORT_ASC);

            foreach($movimentacoesArray as $array){
                $movimentacoes[$array["unidade"]][] = $array;
            }

            return $movimentacoes;
    }
    
    function _movNovosCadastros($post, $request){
        
        if($post != []){
            
            $movimentacaoDados = DB::table("log")
                    ->leftJoin('unidade','unidade.id','log.unidade')
                    ->where('log.tabela_alterada', 'interno')
                    ->where('log.tipo_alteracao', 'add')
                    ->where('log.texto', 'LIKE', '%Criação do cadastro%');                    
                    if(!is_null($request->get('unidade_prisional'))){
                        $movimentacaoDados->where('log.unidade', $request->get('unidade_prisional'));
                    }
                    if(!is_null($request->get('data'))){
                        $movimentacaoDados->whereDate('log.created_at', '>=', Tdata::TdataMysql($request->get('data')));
                    }
                    if(!is_null($request->get('dataFinal'))){
                        $movimentacaoDados->whereDate('log.created_at', '<=', Tdata::TdataMysql($request->get('dataFinal')));
                    }
                   $movimentacaoDados = $movimentacaoDados->select('unidade.sigla', 'log.tipo_alteracao')->orderBy('unidade.sigla','ASC')->get();
        }else{
            
            $movimentacaoDados = DB::table("log")
                    ->leftJoin('unidade','unidade.id','log.unidade')
                    ->where('log.tabela_alterada', 'interno')
                    ->where('log.tipo_alteracao', 'add')
                    ->where('log.texto', 'LIKE', '%Criação do cadastro%')
                    ->select('unidade.sigla', 'log.tipo_alteracao')->orderBy('unidade.sigla','ASC')->get();
        }
            
            $movimentacoesArray = array();
            $tipoEntrdada = "";
            foreach($movimentacaoDados as $mov)
            {
                $tipoEntrdada = $mov->tipo_alteracao == "add" ? "add" : "add";
                
                array_push($movimentacoesArray, array('tipo' => $tipoEntrdada,'unidade' => $mov->sigla));
            }
            
            $movimentacoes = array();

            $movimentacoesArray = self::array_orderby($movimentacoesArray, 'unidade', SORT_ASC);

            foreach($movimentacoesArray as $array){
                $movimentacoes[$array["unidade"]][] = $array;
            }

            return $movimentacoes;
    }
    
    function _movEntradas($post, $request){
        //dd($post);
        if($post != []){
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao.unidade_destino')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['entradaunidadeprisional','transferenciaentrada']);
                    if(!is_null($request->get('unidade_prisional'))){
                        $movimentacaoDados->where('movimentacao.unidade_destino', $request->get('unidade_prisional'));
                    }
                    if(!is_null($request->get('data'))){
                        $movimentacaoDados->whereDate('data', '>=', Tdata::TdataMysql($request->get('data')));
                    }
                    if(!is_null($request->get('dataFinal'))){
                        $movimentacaoDados->whereDate('data', '<=', Tdata::TdataMysql($request->get('dataFinal')));
                    }
                   $movimentacaoDados = $movimentacaoDados->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }else{
            
            $movimentacaoDados = DB::table("movimentacao_interno")
                    ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
                    ->leftJoin('unidade','unidade.id','movimentacao.unidade_destino')
                    ->where('movimentacao_interno.status', 'Finalizada')
                    ->whereIn('tipo', ['entradaunidadeprisional','transferenciaentrada'])
                    ->select('unidade.sigla', 'movimentacao.tipo')->orderBy('unidade.sigla','ASC')->get();
        }
            
            $movimentacoesArray = array();
            $tipoEntrdada = "";
            foreach($movimentacaoDados as $mov)
            {
                $tipoEntrdada = $mov->tipo == "entradaunidadeprisional" ? "entrada" : "entrada";
                
                array_push($movimentacoesArray, array('tipo' => $tipoEntrdada,'unidade' => $mov->sigla));
            }
            
            $movimentacoes = array();

            $movimentacoesArray = self::array_orderby($movimentacoesArray, 'unidade', SORT_ASC);

            foreach($movimentacoesArray as $array){
                $movimentacoes[$array["unidade"]][] = $array;
            }

            return $movimentacoes;
    }
    
    public function movimentacoesPdf(Request $request) {
        
        // Dados gerais
        $unidadeEstatistica = $request->get('unidadeEstatistica');
        $dataEstatistica = $request->get('dataEstatistica');
        $tipo = $request->get('tipo');
        
        switch ($tipo){
            
            case "entradas" :
            case "novosCadastros" :
            case "mudancaCela" :
            case "progressao" :
            case "regressao" :
            case "transferencia" :
            case "alvaras" :
            case "livramentoCondicional" :
            case "fuga" :
            case "recambiamento" :
            case "obito" :
            case "extincaoPunibilidade" :
            case "extradicao" :
            case "prisaoDomiciliar" :

                $titulo = $request->get('titulo');
                $movimentacoes = json_decode($request->get('movimentacoes'), true);

                $pdf = \PDF::loadView('estatisticas.movimentacoes.pdf.padrao',  compact('movimentacoes','titulo','dataEstatistica','unidadeEstatistica'))
                    ->setOption('encoding', 'UTF-8')
                    ->setOption('margin-bottom', 10)
                    ->setOption('margin-top', 30)
                    ->setOption('margin-left', 10)
                    ->setOption('margin-right', 10)
                    ->setOption('margin-bottom', 10)
                    ->setOption('header-html', view('template.header.headerEstatistica'))
                    ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
                    ->setOption('footer-font-size', 7)
                    ->setPaper('a4');
                return $pdf->inline();
                
                break;
        }
        
    }
    

    /**
     * Faltas
     */
    
    public function faltasGravidade(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
            $dataFinal = $request->get('dataFinal');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Penitenciário";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
            
            if(!is_null($request->get('dataFinal')))
            {
                $dataEstatistica = $request->get('data')." até ".$request->get('dataFinal');
            }
        }
        
        if($_POST){
            
            $faltaDados = DB::table('sindicancia_interno')
                    ->leftJoin('interno', 'interno.id', 'sindicancia_interno.fk_interno')
                    ->leftJoin('sindicancia', 'sindicancia.id', 'sindicancia_interno.fk_sindicancia')
                    ->leftJoin('unidade', 'unidade.id', 'sindicancia.unidade');
            
            if(!is_null($request->get('unidade_prisional'))){
                $faltaDados->where('sindicancia.unidade', $request->get('unidade_prisional'));
            }
            if(!is_null($request->get('data'))){
                $faltaDados->whereDate('data_incidente', '>=', Tdata::TdataMysql($request->get('data')));
            }
            if(!is_null($request->get('dataFinal'))){
                $faltaDados->whereDate('data_incidente', '<=', Tdata::TdataMysql($request->get('dataFinal')));
            }
           $faltaDados = $faltaDados->select('unidade.sigla', 'interno.sexo', 'sindicancia.gravidade_falta')->orderBy('sindicancia.gravidade_falta','ASC')->get();
           
        }else{
            $faltaDados = DB::table('sindicancia_interno')
                    ->leftJoin('interno', 'interno.id', 'sindicancia_interno.fk_interno')
                    ->leftJoin('sindicancia', 'sindicancia.id', 'sindicancia_interno.fk_sindicancia')
                    ->leftJoin('unidade', 'unidade.id', 'sindicancia.unidade')
                    ->select('unidade.sigla', 'interno.sexo', 'sindicancia.gravidade_falta')
                    ->orderBy('sindicancia.gravidade_falta', 'ASC')
                    ->get();
        }
        
        $faltas = array();

        foreach(json_decode($faltaDados, true) as $array){
            $faltas[$array["gravidade_falta"]][$array["sexo"]][] = $array;
        }
        
        return view('estatisticas.faltas.gravidade', compact('unidades','faltas','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
        
    }
    
    public function faltasGravidadePdf(Request $request) {
       
        // Dados gerais
        $unidadeEstatistica = $request->get('unidadeEstatistica');
        $dataEstatistica = $request->get('dataEstatistica');
        $tipo = $request->get('tipo');
        
        $faltas = json_decode($request->get('faltas'), true);

        $pdf = \PDF::loadView('estatisticas.faltas.pdf.gravidade',  compact('faltas','dataEstatistica','unidadeEstatistica'))
            ->setOption('encoding', 'UTF-8')
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 30)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('header-html', view('template.header.headerEstatistica'))
            ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');
        return $pdf->inline();

       
    }
    
    public function faltasStatus(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
            $dataFinal = $request->get('dataFinal');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Penitenciário";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
            
            if(!is_null($request->get('dataFinal')))
            {
                $dataEstatistica = $request->get('data')." até ".$request->get('dataFinal');
            }
        }
        
        if($_POST){
            
             $faltaDados = DB::table('sindicancia')
                    ->leftJoin('unidade', 'unidade.id', 'sindicancia.unidade');
            
            if(!is_null($request->get('unidade_prisional'))){
                $faltaDados->where('sindicancia.unidade', $request->get('unidade_prisional'));
            }
            if(!is_null($request->get('data'))){
                $faltaDados->whereDate('data_incidente', '>=', Tdata::TdataMysql($request->get('data')));
            }
            if(!is_null($request->get('dataFinal'))){
                $faltaDados->whereDate('data_incidente', '<=', Tdata::TdataMysql($request->get('dataFinal')));
            }
           $faltaDados = $faltaDados->select('unidade.sigla', 'sindicancia.status')->orderBy('unidade.sigla','ASC')->get();
           
        }else{
            $faltaDados = DB::table('sindicancia')
                    ->leftJoin('unidade', 'unidade.id', 'sindicancia.unidade')
                    ->select('unidade.sigla', 'sindicancia.status')
                    ->orderBy('unidade.sigla', 'ASC')
                    ->get();
        }
        
        $faltas = array();

        foreach(json_decode($faltaDados, true) as $array){
            $faltas[$array["sigla"]][$array["status"]][] = $array;
        }
        
        return view('estatisticas.faltas.status', compact('unidades','faltas','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
        
    }
    
    public function faltasStatusPdf(Request $request) {
       
        // Dados gerais
        $unidadeEstatistica = $request->get('unidadeEstatistica');
        $dataEstatistica = $request->get('dataEstatistica');
        $tipo = $request->get('tipo');
        
        $faltas = json_decode($request->get('faltas'), true);

        $pdf = \PDF::loadView('estatisticas.faltas.pdf.status',  compact('faltas','dataEstatistica','unidadeEstatistica'))
            ->setOption('encoding', 'UTF-8')
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 30)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('header-html', view('template.header.headerEstatistica'))
            ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');
        return $pdf->inline();

       
    }
    
    public function sindicancias(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
            $dataFinal = $request->get('dataFinal');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Penitenciário";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
            
            if(!is_null($request->get('dataFinal')))
            {
                $dataEstatistica = $request->get('data')." até ".$request->get('dataFinal');
            }
        }
        
        if($_POST){
            
             $faltaDados = DB::table('sindicancia')
                    ->leftJoin('unidade', 'unidade.id', 'sindicancia.unidade');
            
            if(!is_null($request->get('unidade_prisional'))){
                $faltaDados->where('sindicancia.unidade', $request->get('unidade_prisional'));
            }
            if(!is_null($request->get('data'))){
                $faltaDados->whereDate('data_incidente', '>=', Tdata::TdataMysql($request->get('data')));
            }
            if(!is_null($request->get('dataFinal'))){
                $faltaDados->whereDate('data_incidente', '<=', Tdata::TdataMysql($request->get('dataFinal')));
            }
           $faltaDados = $faltaDados->select('unidade.sigla', 'sindicancia.status')->orderBy('unidade.sigla','ASC')->get();
           
        }else{
            $faltaDados = DB::table('sindicancia')
                    ->leftJoin('unidade', 'unidade.id', 'sindicancia.unidade')
                    ->select('unidade.sigla', 'sindicancia.status')
                    ->orderBy('unidade.sigla', 'ASC')
                    ->get();
        }
        
        $faltas = array();

        foreach(json_decode($faltaDados, true) as $array){
            $faltas[$array["sigla"]][] = $array;
        }
        
        return view('estatisticas.faltas.sindicancias', compact('unidades','faltas','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
        
    }
    
    public function sindicanciasPdf(Request $request) {
       
        // Dados gerais
        $unidadeEstatistica = $request->get('unidadeEstatistica');
        $dataEstatistica = $request->get('dataEstatistica');
        $tipo = $request->get('tipo');
        
        $faltas = json_decode($request->get('faltas'), true);

        $pdf = \PDF::loadView('estatisticas.faltas.pdf.sindicancias',  compact('faltas','dataEstatistica','unidadeEstatistica'))
            ->setOption('encoding', 'UTF-8')
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 30)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('header-html', view('template.header.headerEstatistica'))
            ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');
        return $pdf->inline();
       
    }
    
    public function saudeSetor(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
            $dataFinal = $request->get('dataFinal');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Penitenciário";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
            
            if(!is_null($request->get('dataFinal')))
            {
                $dataEstatistica = $request->get('data')." até ".$request->get('dataFinal');
            }
        }
        
        if($_POST){
            
            $saudeDados = DB::table('saude_assistencia')
                    ->leftJoin('unidade', 'unidade.id', 'saude_assistencia.unidade');
            
            if(!is_null($request->get('unidade_prisional'))){
                $saudeDados->where('unidade.id', $request->get('unidade_prisional'));
            }
            if(!is_null($request->get('data'))){
                $saudeDados->whereDate('data_atendimento', '>=', Tdata::TdataMysql($request->get('data')));
            }
            if(!is_null($request->get('dataFinal'))){
                $saudeDados->whereDate('data_atendimento', '<=', Tdata::TdataMysql($request->get('dataFinal')));
            }
           $saudeDados = $saudeDados->select('unidade.sigla', 'saude_assistencia.setor')
                   ->orderBy('saude_assistencia.setor', 'ASC')->get();
           
        }else{
            $saudeDados = DB::table('saude_assistencia')
                    ->leftJoin('unidade', 'unidade.id', 'saude_assistencia.unidade')
                    ->select('unidade.sigla', 'saude_assistencia.setor')
                    ->orderBy('saude_assistencia.setor', 'ASC')
                    ->get();
        }
        
        $atendimentos = array();

        foreach(json_decode($saudeDados, true) as $array){
            $atendimentos[$array["setor"]][] = $array;
        }
        
        
        return view('estatisticas.atendimentos.saudeSetor', compact('unidades','atendimentos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
        
    }
    
    public function saudeSetorPdf(Request $request) {
       
        // Dados gerais
        $unidadeEstatistica = $request->get('unidadeEstatistica');
        $dataEstatistica = $request->get('dataEstatistica');
        $tipo = $request->get('tipo');
        
        $atendimentos = json_decode($request->get('atendimentos'), true);

        $pdf = \PDF::loadView('estatisticas.atendimentos.pdf.saudeSetor',  compact('atendimentos','dataEstatistica','unidadeEstatistica'))
            ->setOption('encoding', 'UTF-8')
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 30)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('header-html', view('template.header.headerEstatistica'))
            ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');
        return $pdf->inline();
       
    }
    
    public function saudeUnidade(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
            $dataFinal = $request->get('dataFinal');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Penitenciário";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
            
            if(!is_null($request->get('dataFinal')))
            {
                $dataEstatistica = $request->get('data')." até ".$request->get('dataFinal');
            }
        }
        
        if($_POST){
            
            $saudeDados = DB::table('saude_assistencia')
                    ->leftJoin('unidade', 'unidade.id', 'saude_assistencia.unidade');
            
            if(!is_null($request->get('unidade_prisional'))){
                $saudeDados->where('unidade.id', $request->get('unidade_prisional'));
            }
            if(!is_null($request->get('data'))){
                $saudeDados->whereDate('data_atendimento', '>=', Tdata::TdataMysql($request->get('data')));
            }
            if(!is_null($request->get('dataFinal'))){
                $saudeDados->whereDate('data_atendimento', '<=', Tdata::TdataMysql($request->get('dataFinal')));
            }
           $saudeDados = $saudeDados->select('unidade.sigla', 'saude_assistencia.setor')
                   ->orderBy('unidade.sigla','ASC')->get();
           
        }else{
            $saudeDados = DB::table('saude_assistencia')
                    ->leftJoin('unidade', 'unidade.id', 'saude_assistencia.unidade')
                    ->select('unidade.sigla', 'saude_assistencia.setor')
                    ->orderBy('unidade.sigla', 'ASC')
                    ->get();
        }
        
        $atendimentos = array();

        foreach(json_decode($saudeDados, true) as $array){
            $atendimentos[$array["sigla"]][] = $array;
        }
        
        
        return view('estatisticas.atendimentos.saudeUnidade', compact('unidades','atendimentos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
        
    }
    
    public function saudeUnidadePdf(Request $request) {
       
        // Dados gerais
        $unidadeEstatistica = $request->get('unidadeEstatistica');
        $dataEstatistica = $request->get('dataEstatistica');
        $tipo = $request->get('tipo');
        
        $atendimentos = json_decode($request->get('atendimentos'), true);

        $pdf = \PDF::loadView('estatisticas.atendimentos.pdf.setorUnidade',  compact('atendimentos','dataEstatistica','unidadeEstatistica'))
            ->setOption('encoding', 'UTF-8')
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 30)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('header-html', view('template.header.headerEstatistica'))
            ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');
        return $pdf->inline();
       
    }
    
    public function atendimentoAdvogadosPorUnidade(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
            $dataFinal = $request->get('dataFinal');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Penitenciário";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
            
            if(!is_null($request->get('dataFinal')))
            {
                $dataEstatistica = $request->get('data')." até ".$request->get('dataFinal');
            }
        }
        
        if($_POST){
            
            $advDados = DB::table('advogado_atendimento')
                    ->leftJoin('interno', 'interno.id', 'advogado_atendimento.fk_interno')
                    ->leftJoin('unidade', 'unidade.id', 'advogado_atendimento.unidade');
            
            if(!is_null($request->get('unidade_prisional'))){
                $advDados->where('unidade.id', $request->get('unidade_prisional'));
            }
            if(!is_null($request->get('data'))){
                $advDados->whereDate('data_atendimento', '>=', Tdata::TdataMysql($request->get('data')));
            }
            if(!is_null($request->get('dataFinal'))){
                $advDados->whereDate('data_atendimento', '<=', Tdata::TdataMysql($request->get('dataFinal')));
            }
           $advDados = $advDados->select('unidade.sigla', 'interno.sexo')
                   ->orderBy('unidade.sigla','ASC')->get();
           
        }else{
            $advDados = DB::table('advogado_atendimento')
                    ->leftJoin('interno', 'interno.id', 'advogado_atendimento.fk_interno')
                    ->leftJoin('unidade', 'unidade.id', 'advogado_atendimento.unidade')
                    ->select('unidade.sigla', 'interno.sexo')
                    ->orderBy('unidade.sigla', 'ASC')
                    ->get();
        }
        
        $atendimentos = array();

        foreach(json_decode($advDados, true) as $array){
            $atendimentos[$array["sigla"]][$array["sexo"]][] = $array;
        }
        
        return view('estatisticas.atendimentos.advogadoUnidade', compact('unidades','atendimentos','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
        
    }
    
    public function atendimentoAdvogadosPorUnidadePdf(Request $request) {
       
        // Dados gerais
        $unidadeEstatistica = $request->get('unidadeEstatistica');
        $dataEstatistica = $request->get('dataEstatistica');
        $tipo = $request->get('tipo');
        
        $atendimentos = json_decode($request->get('atendimentos'), true);

        $pdf = \PDF::loadView('estatisticas.atendimentos.pdf.atendimentoAdvogadosPorUnidade',  compact('atendimentos','dataEstatistica','unidadeEstatistica'))
            ->setOption('encoding', 'UTF-8')
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 30)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('header-html', view('template.header.headerEstatistica'))
            ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');
        return $pdf->inline();
       
    }
    
    public function escoltaUnidade(Request $request) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
            $dataFinal = $request->get('dataFinal');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Penitenciário";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
            
            if(!is_null($request->get('dataFinal')))
            {
                $dataEstatistica = $request->get('data')." até ".$request->get('dataFinal');
            }
        }

        if($_POST){
            
            $escoltaDados = DB::table('escolta')
                    ->leftJoin('unidade', 'unidade.id','escolta.unidade')
                    ->where('escolta.status', 'Finalizada');

            if(!is_null($request->get('unidade_prisional'))){
                $escoltaDados->where('unidade.id', $request->get('unidade_prisional'));
            }
            if(!is_null($request->get('data'))){
                $escoltaDados->whereDate('data_saida', '>=', Tdata::TdataMysql($request->get('data')));
            }
            if(!is_null($request->get('dataFinal'))){
                $escoltaDados->whereDate('data_saida', '<=', Tdata::TdataMysql($request->get('dataFinal')));
            }
           $escoltaDados = $escoltaDados->select('unidade.sigla','escolta.tipo')
                   ->orderBy('unidade.sigla', 'ASC')->get();
            
        }else{
            
            $escoltaDados = DB::table('escolta')
                    ->leftJoin('unidade', 'unidade.id','escolta.unidade')
                    ->where('escolta.status', 'Finalizada')
                    ->orderBy('unidade.sigla', 'ASC')
                    ->select('unidade.sigla','escolta.tipo')
                    ->get();
        }
        
        $escoltas = array();

        foreach(json_decode($escoltaDados, true) as $array){
            $escoltas[$array["sigla"]][$array["tipo"]][] = $array;
        }
        
        
        return view('estatisticas.escoltas.unidade', compact('unidades','escoltas','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
    }
    
    public function escoltaUnidadePdf(Request $request) {
        
        // Dados gerais
        $unidadeEstatistica = $request->get('unidadeEstatistica');
        $dataEstatistica = $request->get('dataEstatistica');
        $tipo = $request->get('tipo');
        
        $escoltas = json_decode($request->get('escoltas'), true);

        $pdf = \PDF::loadView('estatisticas.escoltas.pdf.unidade',  compact('escoltas','dataEstatistica','unidadeEstatistica'))
            ->setOption('encoding', 'UTF-8')
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 30)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('header-html', view('template.header.headerEstatistica'))
            ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');
        return $pdf->inline();
        
    }
    
    public function ocorrenciasNatureza(Request $request){
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
            $dataFinal = $request->get('dataFinal');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Penitenciário";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
            
            if(!is_null($request->get('dataFinal')))
            {
                $dataEstatistica = $request->get('data')." até ".$request->get('dataFinal');
            }
        }
        
        if($_POST){
            
            $dadosOcorrencias = DB::table('ocorrencia')
                    ->leftJoin('ocorrencia_natureza', 'ocorrencia_natureza.id','ocorrencia.natureza')
                    ->where('ocorrencia.status', 'A');
            
            if(!is_null($request->get('unidade_prisional'))){
                $dadosOcorrencias->where('ocorrencia.unidade', $request->get('unidade_prisional'));
            }
            if(!is_null($request->get('data'))){
                $dadosOcorrencias->whereDate('data', '>=', Tdata::TdataMysql($request->get('data')));
            }
            if(!is_null($request->get('dataFinal'))){
                $dadosOcorrencias->whereDate('data', '<=', Tdata::TdataMysql($request->get('dataFinal')));
            }
           $dadosOcorrencias = $dadosOcorrencias->select('ocorrencia_natureza.natureza')
                   ->orderBy('ocorrencia_natureza.natureza','ASC')->get();
            
        }else{
            
            $dadosOcorrencias = DB::table('ocorrencia')
                    ->leftJoin('ocorrencia_natureza', 'ocorrencia_natureza.id','ocorrencia.natureza')
                    ->where('ocorrencia.status', 'A')
                    ->select('ocorrencia_natureza.natureza')
                    ->orderBy('ocorrencia_natureza.natureza', 'ASC')
                    ->get();
        }        
       
        $ocorrencias = array();

        foreach(json_decode($dadosOcorrencias, true) as $array){
            $ocorrencias[$array["natureza"]][] = $array;
        }
        
        return view('estatisticas.ocorrencias.natureza', compact('unidades','ocorrencias','lotacaoServidor','dataEstatistica','unidadeEstatistica'));

    }
    
    public function ocorrenciasNaturezaPdf(Request $request) {
        
        // Dados gerais
        $unidadeEstatistica = $request->get('unidadeEstatistica');
        $dataEstatistica = $request->get('dataEstatistica');
        $tipo = $request->get('tipo');
        
        $ocorrencias = json_decode($request->get('ocorrencias'), true);

        $pdf = \PDF::loadView('estatisticas.ocorrencias.pdf.natureza',  compact('ocorrencias','dataEstatistica','unidadeEstatistica'))
            ->setOption('encoding', 'UTF-8')
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 30)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('header-html', view('template.header.headerEstatistica'))
            ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');
        return $pdf->inline();
        
    }
    
    public function ocorrenciasNaturezaObjeto(Request $request) {
               
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
        
        if($_POST){
            $unidadePrisional = $request->get('unidade_prisional');
            $data = $request->get('data');
            $dataFinal = $request->get('dataFinal');
        }else{
            $unidadePrisional = null;
            $data = date("Y-m-d");
        }
        
        $dataEstatistica = Tdata::TdataHoje();
        $unidadeEstatistica = "Sistema Penitenciário";
        if(!is_null($request->get('unidade_prisional')))
        {
            $unidadeEstatistica = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
        }
        if(!is_null($request->get('data')))
        {
            $dataEstatistica = $request->get('data');
            
            if(!is_null($request->get('dataFinal')))
            {
                $dataEstatistica = $request->get('data')." até ".$request->get('dataFinal');
            }
        }
        
        if($_POST){
            
            $ocorrenciaDados = DB::table('ocorrencia')
                    ->leftJoin('ocorrencia_natureza_objeto','ocorrencia_natureza_objeto.id','ocorrencia.natureza_objeto')
                    ->leftJoin('ocorrencia_natureza','ocorrencia_natureza.id','ocorrencia_natureza_objeto.fk_natureza')
                    ->whereNotNull('ocorrencia.natureza_objeto');
            
            if(!is_null($request->get('unidade_prisional'))){
                $ocorrenciaDados->where('ocorrencia.unidade', $request->get('unidade_prisional'));
            }
            if(!is_null($request->get('data'))){
                $ocorrenciaDados->whereDate('data', '>=', Tdata::TdataMysql($request->get('data')));
            }
            if(!is_null($request->get('dataFinal'))){
                $ocorrenciaDados->whereDate('data', '<=', Tdata::TdataMysql($request->get('dataFinal')));
            }
           $ocorrenciaDados = $ocorrenciaDados->select(DB::raw('CONCAT(ocorrencia_natureza.natureza," - ", ocorrencia_natureza_objeto.nome) AS nome'),'ocorrencia_natureza.natureza')
                   ->orderBy('ocorrencia_natureza_objeto.fk_natureza','ASC')->get();
            
        }else{
            $ocorrenciaDados = DB::table('ocorrencia')
                    ->leftJoin('ocorrencia_natureza_objeto','ocorrencia_natureza_objeto.id','ocorrencia.natureza_objeto')
                    ->leftJoin('ocorrencia_natureza','ocorrencia_natureza.id','ocorrencia_natureza_objeto.fk_natureza')
                    ->whereNotNull('ocorrencia.natureza_objeto')
                    ->select(DB::raw('CONCAT(ocorrencia_natureza.natureza," - ", ocorrencia_natureza_objeto.nome) AS nome'),'ocorrencia_natureza.natureza')
                    ->orderBy('ocorrencia_natureza_objeto.fk_natureza','ASC')
                    ->get();
        }
        

        $ocorrencias = array();

        foreach(json_decode($ocorrenciaDados, true) as $array){
            $ocorrencias[$array["nome"]][] = $array;
        }
        
        
        return view('estatisticas.ocorrencias.naturezaObjeto', compact('unidades','ocorrencias','lotacaoServidor','dataEstatistica','unidadeEstatistica'));
        
    }
    
    public function ocorrenciasNaturezaObjetoPdf(Request $request) {
        
        // Dados gerais
        $unidadeEstatistica = $request->get('unidadeEstatistica');
        $dataEstatistica = $request->get('dataEstatistica');
        $tipo = $request->get('tipo');
        
        $ocorrencias = json_decode($request->get('ocorrencias'), true);

        $pdf = \PDF::loadView('estatisticas.ocorrencias.pdf.naturezaObjeto',  compact('ocorrencias','dataEstatistica','unidadeEstatistica'))
            ->setOption('encoding', 'UTF-8')
            ->setOption('margin-bottom', 10)
            ->setOption('margin-top', 30)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('header-html', view('template.header.headerEstatistica'))
            ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
            ->setOption('footer-font-size', 7)
            ->setPaper('a4');
        return $pdf->inline();
        
    }
    
    public static function array_orderby()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
                }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }   
    
    ################################ !calculos com datas retroativas e revisão ############################################
    
}
