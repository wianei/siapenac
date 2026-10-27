<?php /*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a visita
|
*/

//namespace App\Http\Controllers\estatisticas;
//
//use App\Http\Controllers\Facade\FacadeController;
//use App\Http\Controllers\Interno\InternoController;
//use App\Http\Controllers\Log\LogController;
//use App\Http\Controllers\Unidades\UnidadesController;
//use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;
//use App\Classes\Tdata;
//use Illuminate\Support\Arr;
//use Illuminate\Support\Facades\DB;
//use Mockery\CountValidator\Exception;
//use PhpParser\Node\Expr\Array_;
//
//class EstatisticasController extends Controller
//{
//
//    public function __construct()
//    {
//        //date_default_timezone_set('America/Fortaleza');
//        $this->middleware('auth');
//    }
//
//    public function callAction($method, $parameters)
//    {
//        LogController::logAcessoUser();
//
//        return parent::callAction($method, $parameters);
//    }
//
//    public function show(Request $request){
//
//
//        $unidade = $request->get('unidade_prisional');
//        if(!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
//        else $unidadeSigla = null;
//        $pavilhao = $request->get('pavilhao');
//        if(!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
//        else $pavilhaoNome = null;
//        $ala = $request->get('ala');
//        if(!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
//        else $alaNome = null;
//        $cela = $request->get('cela');
//        if(!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
//        else $celaNome = null;
//
//        $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);
//
//        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
//
//        $qtdInternosLotSelecionada = DB::table('interno')->where('status','A');
//
//        $qtdUnidadeHA = DB::table('interno')
//            ->leftJoin('unidade','unidade.id','interno.unidade_prisional')
//            ->where('sexo', 'M')->where('interno.status','A')
//            ->select(DB::raw('unidade.sigla, count(*) as qtd'))
//            ->groupBy('unidade.sigla')->orderBy('qtd','DESC');
//
//        $qtdUnidadeMA = DB::table('interno')
//            ->leftJoin('unidade','unidade.id','interno.unidade_prisional')
//            ->where('sexo', 'F')->where('interno.status','A')
//            ->select(DB::raw('unidade.sigla, count(*) as qtd'))
//            ->groupBy('unidade.sigla')->orderBy('qtd','DESC');
//
//        $qtdUnidadeTotal  = DB::table('interno')
//            ->leftJoin('unidade','unidade.id','interno.unidade_prisional')
//            ->select(DB::raw('unidade.sigla, count(*) as qtd'))
//            ->where('interno.status','A')->groupBy('unidade.sigla')->orderBy('qtd','DESC');
//
//        $qtdRegimeNIH = DB::table('interno')->where('regime', null)->where('sentenciado','S')->where('sexo','M')->where('medida_seguranca','=','N')->where('status','A');
//        $qtdRegimeNIM = DB::table('interno')->where('regime', null)->where('sentenciado','S')->where('sexo','F')->where('medida_seguranca','=','N')->where('status','A');
//        $qtdRegimeFechadoH = DB::table('interno')->where('sexo','M')->where('regime', 'FECHADO')->where('status','A');
//        $qtdRegimeFechadoM = DB::table('interno')->where('sexo','F')->where('regime', 'FECHADO')->where('status','A');
//        $qtdRegimeSemiabertoH = DB::table('interno')->where('sexo','M')->where('regime', 'SEMIABERTO')->where('status','A');
//        $qtdRegimeSemiabertoM = DB::table('interno')->where('sexo','F')->where('regime', 'SEMIABERTO')->where('status','A');
//        $qtdRegimeAbertoH = DB::table('interno')->where('sexo','M')->where('regime', 'ABERTO')->where('status','A');
//        $qtdRegimeAbertoM = DB::table('interno')->where('sexo','F')->where('regime', 'ABERTO')->where('status','A');
//
//        $qtdSentenciadosNIH = DB::table('interno')->where('sexo','M')->where('sentenciado', null)->where('medida_seguranca','N')->where('status','A');
//        $qtdSentenciadosNIM = DB::table('interno')->where('sexo','F')->where('sentenciado', null)->where('medida_seguranca','N')->where('status','A');
//        $qtdSentenciadosH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'S')->where('medida_seguranca','N')->where('status','A');
//        $qtdSentenciadosM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'S')->where('medida_seguranca','N')->where('status','A');
//        $qtdProvisoriosH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'N')->where('medida_seguranca','N')->where('status','A');
//        $qtdProvisoriosM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'N')->where('medida_seguranca','N')->where('status','A');
//        
//        $qtdSentenciadosMSNIH = DB::table('interno')->where('sexo','M')->where('sentenciado', null)->where('medida_seguranca','S')->where('status','A');
//        $qtdSentenciadosMSNIM = DB::table('interno')->where('sexo','F')->where('sentenciado', null)->where('medida_seguranca','S')->where('status','A');
//        $qtdSentenciadosMSH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'S')->where('medida_seguranca','S')->where('status','A');
//        $qtdSentenciadosMSM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'S')->where('medida_seguranca','S')->where('status','A');
//        $qtdProvisoriosMSH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'N')->where('medida_seguranca','S')->where('status','A');
//        $qtdProvisoriosMSM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'N')->where('medida_seguranca','S')->where('status','A');
//
//        $qtdMedidaDeSegurancaH = DB::table('interno')->where('sexo','M')->where('medida_seguranca', 'S')->where('status','A');
//        $qtdMedidaDeSegurancaM = DB::table('interno')->where('sexo','F')->where('medida_seguranca', 'S')->where('status','A');
//
//        $qtdHA = DB::table('interno')->where('sexo', 'M')->where('status','A');
//        $qtdMA = DB::table('interno')->where('sexo', 'F')->where('status','A');
//
//        $faixaEtaria_naoInformadaH = 0;
//        $faixaEtaria_zero_a_dezesseteH = 0;
//        $faixaEtaria_dezoito_a_vinteequatroH = 0;
//        $faixaEtaria_vinteecinco_a_vinteenoveH = 0;
//        $faixaEtaria_trinta_a_trintaequatroH = 0;
//        $faixaEtaria_trintaecinco_a_quarentaecincoH = 0;
//        $faixaEtaria_quarentaeseis_a_sessentaH = 0;
//        $faixaEtaria_sessentaeum_a_setentaH = 0;
//        $faixaEtaria_maisDeSetentaH = 0;
//
//        $faixaEtaria_naoInformadaM = 0;
//        $faixaEtaria_zero_a_dezesseteM = 0;
//        $faixaEtaria_dezoito_a_vinteequatroM = 0;
//        $faixaEtaria_vinteecinco_a_vinteenoveM = 0;
//        $faixaEtaria_trinta_a_trintaequatroM = 0;
//        $faixaEtaria_trintaecinco_a_quarentaecincoM = 0;
//        $faixaEtaria_quarentaeseis_a_sessentaM = 0;
//        $faixaEtaria_sessentaeum_a_setentaM = 0;
//        $faixaEtaria_maisDeSetentaM = 0;
//
//        $internosH = DB::table('interno')->select('data_nascimento')->where('sexo','M')->where('status','A');
//        $internosM = DB::table('interno')->select('data_nascimento')->where('sexo','F')->where('status','A');
//
//        $qtdFaccaoNIH = DB::table('interno')->where('status','A')->where('sexo','M')->where('faccao', null);
//        $qtdFaccaoNIM = DB::table('interno')->where('status','A')->where('sexo','F')->where('faccao', null);
//
//        //periculosidade
//        $periculosidadeAltaH = DB::table('interno')->where('periculosidade','Alta')->where('sexo','M')->where('status','A');
//        $periculosidadeMediaH = DB::table('interno')->where('periculosidade','Média')->where('sexo','M')->where('status','A');
//        $periculosidadeBaixaH = DB::table('interno')->where('periculosidade','Baixa')->where('sexo','M')->where('status','A');
//        $periculosidadeNIH = DB::table('interno')->where('periculosidade',null)->where('sexo','M')->where('status','A');
//        $periculosidadeAltaM = DB::table('interno')->where('periculosidade','Alta')->where('sexo','F')->where('status','A');
//        $periculosidadeMediaM = DB::table('interno')->where('periculosidade','Média')->where('sexo','F')->where('status','A');
//        $periculosidadeBaixaM = DB::table('interno')->where('periculosidade','Baixa')->where('sexo','F')->where('status','A');
//        $periculosidadeNIM = DB::table('interno')->where('periculosidade',null)->where('sexo','F')->where('status','A');
//
//        //liderança
//        $qtdLiderancaSimH = DB::table('interno')->where('lideranca','S')->where('sexo','M')->where('status','A');
//        $qtdLiderancaNaoH = DB::table('interno')->where('lideranca','N')->where('sexo','M')->where('status','A');
//        $qtdLiderancaNIH = DB::table('interno')->where('lideranca',null)->where('sexo','M')->where('status','A');
//        $qtdLiderancaSimM = DB::table('interno')->where('lideranca','S')->where('sexo','F')->where('status','A');
//        $qtdLiderancaNaoM = DB::table('interno')->where('lideranca','N')->where('sexo','F')->where('status','A');
//        $qtdLiderancaNIM = DB::table('interno')->where('lideranca',null)->where('sexo','F')->where('status','A');
//
//        // estado_civil
//        $qtdSolteirosH = DB::table('interno')->where('estado_civil','solteiro')->where('sexo','M')->where('status','A');
//        $qtdSolteirosM = DB::table('interno')->where('estado_civil','solteiro')->where('sexo','F')->where('status','A');
//        $qtdCasadosH = DB::table('interno')->where('estado_civil','casado')->where('sexo','M')->where('status','A');
//        $qtdCasadosM = DB::table('interno')->where('estado_civil','casado')->where('sexo','F')->where('status','A');
//        $qtdUniaoEstavelH = DB::table('interno')->where('estado_civil','uniao estavel')->where('sexo','M')->where('status','A');
//        $qtdUniaoEstavelM = DB::table('interno')->where('estado_civil','uniao estavel')->where('sexo','F')->where('status','A');
//        $qtdSeparadosH = DB::table('interno')->where('estado_civil','separado')->where('sexo','M')->where('status','A');
//        $qtdSeparadosM = DB::table('interno')->where('estado_civil','separado')->where('sexo','F')->where('status','A');
//        $qtdDivorciadosH = DB::table('interno')->where('estado_civil','divorciado')->where('sexo','M')->where('status','A');
//        $qtdDivorciadosM = DB::table('interno')->where('estado_civil','divorciado')->where('sexo','F')->where('status','A');
//        $qtdViuvosH = DB::table('interno')->where('estado_civil','viuvo')->where('sexo','M')->where('status','A');
//        $qtdViuvosM = DB::table('interno')->where('estado_civil','viuvo')->where('sexo','F')->where('status','A');
//        $qtdEstadoCivilNIH = DB::table('interno')->where('estado_civil',null)->where('sexo','M')->where('status','A');
//        $qtdEstadoCivilNIM = DB::table('interno')->where('estado_civil',null)->where('sexo','F')->where('status','A');
//
//        // opcao_sexual
//        $qtdHeteroH = DB::table('interno')->where('opcao_sexual','HETEROSSEXUAL')->where('sexo','M')->where('status','A');
//        $qtdHeteroM = DB::table('interno')->where('opcao_sexual','HETEROSSEXUAL')->where('sexo','F')->where('status','A');
//        $qtdHomoH = DB::table('interno')->where('opcao_sexual','HOMOSSEXUAL')->where('sexo','M')->where('status','A');
//        $qtdHomoM = DB::table('interno')->where('opcao_sexual','HOMOSSEXUAL')->where('sexo','F')->where('status','A');
//        $qtdBiH = DB::table('interno')->where('opcao_sexual','BISSEXUAL')->where('sexo','M')->where('status','A');
//        $qtdBiM = DB::table('interno')->where('opcao_sexual','BISSEXUAL')->where('sexo','F')->where('status','A');
//        $qtdSexualidadeNaoDeclH = DB::table('interno')->where('opcao_sexual','nao declarado')->where('sexo','M')->where('status','A');
//        $qtdSexualidadeNaoDeclM = DB::table('interno')->where('opcao_sexual','nao declarado')->where('sexo','F')->where('status','A');
//        $qtdSexualidadeNIH = DB::table('interno')->where('opcao_sexual',null)->where('sexo','M')->where('status','A');
//        $qtdSexualidadeNIM = DB::table('interno')->where('opcao_sexual',null)->where('sexo','F')->where('status','A');
//
//        // etnia
//        $qtdEtniaBrancaH = DB::table('interno')->where('etnia','Branca')->where('sexo','M')->where('status','A');
//        $qtdEtniaBrancaM = DB::table('interno')->where('etnia','Branca')->where('sexo','F')->where('status','A');
//        $qtdEtniaIndígenaH = DB::table('interno')->where('etnia','Indígena')->where('sexo','M')->where('status','A');
//        $qtdEtniaIndígenaM = DB::table('interno')->where('etnia','Indígena')->where('sexo','F')->where('status','A');
//        $qtdEtniaPretaH = DB::table('interno')->where('etnia','Preta')->where('sexo','M')->where('status','A');
//        $qtdEtniaPretaM = DB::table('interno')->where('etnia','Preta')->where('sexo','F')->where('status','A');
//        $qtdEtniaPardaH = DB::table('interno')->where('etnia','Parda')->where('sexo','M')->where('status','A');
//        $qtdEtniaPardaM = DB::table('interno')->where('etnia','Parda')->where('sexo','F')->where('status','A');
//        $qtdEtniaAmarelaH = DB::table('interno')->where('etnia','Amarela')->where('sexo','M')->where('status','A');
//        $qtdEtniaAmarelaM = DB::table('interno')->where('etnia','Amarela')->where('sexo','F')->where('status','A');
//        $qtdEtniaNIH = DB::table('interno')->where('etnia',null)->where('sexo','M')->where('status','A');
//        $qtdEtniaNIM = DB::table('interno')->where('etnia',null)->where('sexo','F')->where('status','A');
//
//        // escolaridade
//        $qtdAnalfabetoH = DB::table('interno')->where('escolaridade','Analfabeto')->where('sexo','M')->where('status','A');
//        $qtdAnalfabetoM = DB::table('interno')->where('escolaridade','Analfabeto')->where('sexo','F')->where('status','A');
//        $qtdAlfabetizadoH = DB::table('interno')->where('escolaridade','Alfabetizado')->where('sexo','M')->where('status','A');
//        $qtdAlfabetizadoM = DB::table('interno')->where('escolaridade','Alfabetizado')->where('sexo','F')->where('status','A');
//        $qtdFundIncH = DB::table('interno')->where('escolaridade','Ensino Fundamental Incompleto')->where('sexo','M')->where('status','A');
//        $qtdFundIncM = DB::table('interno')->where('escolaridade','Ensino Fundamental Incompleto')->where('sexo','F')->where('status','A');
//        $qtdFundCompH = DB::table('interno')->where('escolaridade','Ensino Fundamental Completo')->where('sexo','M')->where('status','A');
//        $qtdFundCompM = DB::table('interno')->where('escolaridade','Ensino Fundamental Completo')->where('sexo','F')->where('status','A');
//        $qtdMedioIncompH = DB::table('interno')->where('escolaridade','Ensino Médio Incompleto')->where('sexo','M')->where('status','A');
//        $qtdMedioIncompM = DB::table('interno')->where('escolaridade','Ensino Médio Incompleto')->where('sexo','F')->where('status','A');
//        $qtdMedioCompH = DB::table('interno')->where('escolaridade','Ensino Médio Completo')->where('sexo','M')->where('status','A');
//        $qtdMedioCompM = DB::table('interno')->where('escolaridade','Ensino Médio Completo')->where('sexo','F')->where('status','A');
//        $qtdSuperiorIncompH = DB::table('interno')->where('escolaridade','Ensino Superior Incompleto')->where('sexo','M')->where('status','A');
//        $qtdSuperiorIncompM = DB::table('interno')->where('escolaridade','Ensino Superior Incompleto')->where('sexo','F')->where('status','A');
//        $qtdSuperiorCompH = DB::table('interno')->where('escolaridade','Ensino Superior Completo')->where('sexo','M')->where('status','A');
//        $qtdSuperiorCompM = DB::table('interno')->where('escolaridade','Ensino Superior Completo')->where('sexo','F')->where('status','A');
//        $qtdPosLatoSensuH = DB::table('interno')->where('escolaridade','Pós-graduação lato sensu')->where('sexo','M')->where('status','A');
//        $qtdPosLatoSensuM = DB::table('interno')->where('escolaridade','Pós-graduação lato sensu')->where('sexo','F')->where('status','A');
//        $qtdMestradoH = DB::table('interno')->where('escolaridade','Mestrado')->where('sexo','M')->where('status','A');
//        $qtdMestradoM = DB::table('interno')->where('escolaridade','Mestrado')->where('sexo','F')->where('status','A');
//        $qtdDoutoradoH = DB::table('interno')->where('escolaridade','Doutorado')->where('sexo','M')->where('status','A');
//        $qtdDoutoradoM = DB::table('interno')->where('escolaridade','Doutorado')->where('sexo','F')->where('status','A');
//        $qtdPosDoutoradoH = DB::table('interno')->where('escolaridade','Pós-Doutorado')->where('sexo','M')->where('status','A');
//        $qtdPosDoutoradoM = DB::table('interno')->where('escolaridade','Pós-Doutorado')->where('sexo','F')->where('status','A');
//        $qtdEscolaridadeNIH = DB::table('interno')->where('escolaridade',null)->where('sexo','M')->where('status','A');
//        $qtdEscolaridadeNIM = DB::table('interno')->where('escolaridade',null)->where('sexo','F')->where('status','A');
//        
//        $qtdNacionalidadeNIH = DB::table('interno')->where('naturalidade_pais',null)->where('sexo','M')->where('status','A');
//        $qtdNacionalidadeNIM = DB::table('interno')->where('naturalidade_pais',null)->where('sexo','F')->where('status','A');
//        
//        //religião
//        $qtdAdventistaH = DB::table('interno')->where('religiao','Adventista')->where('sexo','M')->where('status','A');
//        $qtdAdventistaM = DB::table('interno')->where('religiao','Adventista')->where('sexo','F')->where('status','A');
//        $qtdBudistaH = DB::table('interno')->where('religiao','Budista')->where('sexo','M')->where('status','A');
//        $qtdBudistaM = DB::table('interno')->where('religiao','Budista')->where('sexo','F')->where('status','A');
//        $qtdCatolicaH = DB::table('interno')->where('religiao','Católica')->where('sexo','M')->where('status','A');
//        $qtdCatolicaM = DB::table('interno')->where('religiao','Católica')->where('sexo','F')->where('status','A');
//        $qtdEspiritaH = DB::table('interno')->where('religiao','Espírita')->where('sexo','M')->where('status','A');
//        $qtdEspiritaM = DB::table('interno')->where('religiao','Espírita')->where('sexo','F')->where('status','A');
//        $qtdEvangelicaH = DB::table('interno')->where('religiao','Evangélica')->where('sexo','M')->where('status','A');
//        $qtdEvangelicaM = DB::table('interno')->where('religiao','Evangélica')->where('sexo','F')->where('status','A');
//        $qtdJudeuH = DB::table('interno')->where('religiao','Judeu')->where('sexo','M')->where('status','A');
//        $qtdJudeuM = DB::table('interno')->where('religiao','Judeu')->where('sexo','F')->where('status','A');
//        $qtdMormonH = DB::table('interno')->where('religiao','Mórmon')->where('sexo','M')->where('status','A');
//        $qtdMormonM = DB::table('interno')->where('religiao','Mórmon')->where('sexo','F')->where('status','A');
//        $qtdMulcumanaH = DB::table('interno')->where('religiao','Muçulmana')->where('sexo','M')->where('status','A');
//        $qtdMulcumanaM = DB::table('interno')->where('religiao','Muçulmana')->where('sexo','F')->where('status','A');
//        $qtdProtestanteH = DB::table('interno')->where('religiao','Protestante')->where('sexo','M')->where('status','A');
//        $qtdProtestanteM = DB::table('interno')->where('religiao','Protestante')->where('sexo','F')->where('status','A');
//        $qtdAgnosticoH = DB::table('interno')->where('religiao','Agnóstico')->where('sexo','M')->where('status','A');
//        $qtdAgnosticoM = DB::table('interno')->where('religiao','Agnóstico')->where('sexo','F')->where('status','A');
//        $qtdAteuH = DB::table('interno')->where('religiao','Ateu')->where('sexo','M')->where('status','A');
//        $qtdAteuM = DB::table('interno')->where('religiao','Ateu')->where('sexo','F')->where('status','A');
//        $qtdSemReligiaoH = DB::table('interno')->where('religiao','Sem Religião')->where('sexo','M')->where('status','A');
//        $qtdSemReligiaoM = DB::table('interno')->where('religiao','Sem Religião')->where('sexo','F')->where('status','A');
//        $qtdOutrasH = DB::table('interno')->where('religiao','Outras')->where('sexo','M')->where('status','A');
//        $qtdOutrasM = DB::table('interno')->where('religiao','Outras')->where('sexo','F')->where('status','A');
//        $qtdReligiaoNIH = DB::table('interno')->where('religiao',null)->where('sexo','M')->where('status','A');
//        $qtdReligiaoNIM = DB::table('interno')->where('religiao',null)->where('sexo','F')->where('status','A');
//
//
//        //Saída da Unidade Prisional
//        $qtdAlvara = DB::table('movimentacao')->where('tipo','alvara');
//        $qtdFuga = DB::table('movimentacao')->where('tipo','fuga');
//        $qtdRecambiamento = DB::table('movimentacao')->where('tipo','recambiamento');
//        $qtdSaidaJudicial = DB::table('movimentacao')->where('tipo','saidajudicial');
//        $qtdExtradicao = DB::table('movimentacao')->where('tipo','extradicao');
//        $qtdLivramento = DB::table('movimentacao')->where('tipo','livramentocondicional');
//        $qtdObito = DB::table('movimentacao')->where('tipo','obito');
//        $qtdExtincaopunibilidade = DB::table('movimentacao')->where('tipo','extincaopunibilidade');
//
//        $qtdAlvara->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
//        $qtdFuga->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
//        $qtdRecambiamento->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
//        $qtdSaidaJudicial->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
//        $qtdExtradicao->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
//        $qtdLivramento->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
//        $qtdObito->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
//        $qtdExtincaopunibilidade->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
//
//
//        if(!is_null($unidade)){
//            $qtdUnidadeHA->where('unidade_prisional', $unidade);
//            $qtdUnidadeMA->where('unidade_prisional', $unidade);
//            $qtdUnidadeTotal->where('unidade_prisional', $unidade);
//
//            $qtdInternosLotSelecionada->where('unidade_prisional',$unidade);
//
//            $qtdRegimeNIH->where('unidade_prisional',$unidade);
//            $qtdRegimeNIM->where('unidade_prisional',$unidade);
//            $qtdRegimeFechadoH->where('unidade_prisional',$unidade);
//            $qtdRegimeFechadoM->where('unidade_prisional',$unidade);
//            $qtdRegimeSemiabertoH->where('unidade_prisional',$unidade);
//            $qtdRegimeSemiabertoM->where('unidade_prisional',$unidade);
//            $qtdRegimeAbertoH->where('unidade_prisional',$unidade);
//            $qtdRegimeAbertoM->where('unidade_prisional',$unidade);
//
//            $qtdSentenciadosNIH->where('unidade_prisional',$unidade);
//            $qtdSentenciadosNIM->where('unidade_prisional',$unidade);
//            $qtdSentenciadosH->where('unidade_prisional',$unidade);
//            $qtdSentenciadosM->where('unidade_prisional',$unidade);
//            $qtdProvisoriosH->where('unidade_prisional',$unidade);
//            $qtdProvisoriosM->where('unidade_prisional',$unidade);
//            
//            $qtdSentenciadosMSNIH->where('unidade_prisional',$unidade);
//            $qtdSentenciadosMSNIM->where('unidade_prisional',$unidade);
//            $qtdSentenciadosMSH->where('unidade_prisional',$unidade);
//            $qtdSentenciadosMSM->where('unidade_prisional',$unidade);
//            $qtdProvisoriosMSH->where('unidade_prisional',$unidade);
//            $qtdProvisoriosMSM->where('unidade_prisional',$unidade);
//
//            $qtdMedidaDeSegurancaH->where('unidade_prisional',$unidade);
//            $qtdMedidaDeSegurancaM->where('unidade_prisional',$unidade);
//
//            $qtdHA->where('unidade_prisional',$unidade);
//            $qtdMA->where('unidade_prisional',$unidade);
//
//            $internosH->where('unidade_prisional',$unidade);
//            $internosM->where('unidade_prisional',$unidade);
//
//            $qtdFaccaoNIH->where('unidade_prisional',$unidade);
//            $qtdFaccaoNIM->where('unidade_prisional',$unidade);
//
//            $periculosidadeAltaH->where('unidade_prisional',$unidade);
//            $periculosidadeMediaH->where('unidade_prisional',$unidade);
//            $periculosidadeBaixaH->where('unidade_prisional',$unidade);
//            $periculosidadeNIH->where('unidade_prisional',$unidade);
//            $periculosidadeAltaM->where('unidade_prisional',$unidade);
//            $periculosidadeMediaM->where('unidade_prisional',$unidade);
//            $periculosidadeBaixaM->where('unidade_prisional',$unidade);
//            $periculosidadeNIM->where('unidade_prisional',$unidade);
//
//            $qtdLiderancaSimH->where('unidade_prisional',$unidade);
//            $qtdLiderancaNaoH->where('unidade_prisional',$unidade);
//            $qtdLiderancaNIH->where('unidade_prisional',$unidade);
//            $qtdLiderancaSimM->where('unidade_prisional',$unidade);
//            $qtdLiderancaNaoM->where('unidade_prisional',$unidade);
//            $qtdLiderancaNIM->where('unidade_prisional',$unidade);
//
//            //estado_civil
//            $qtdSolteirosH->where('unidade_prisional',$unidade);
//            $qtdSolteirosM->where('unidade_prisional',$unidade);
//            $qtdCasadosH->where('unidade_prisional',$unidade);
//            $qtdCasadosM->where('unidade_prisional',$unidade);
//            $qtdUniaoEstavelH->where('unidade_prisional',$unidade);
//            $qtdUniaoEstavelM->where('unidade_prisional',$unidade);
//            $qtdSeparadosH->where('unidade_prisional',$unidade);
//            $qtdSeparadosM->where('unidade_prisional',$unidade);
//            $qtdDivorciadosH->where('unidade_prisional',$unidade);
//            $qtdDivorciadosM->where('unidade_prisional',$unidade);
//            $qtdViuvosH->where('unidade_prisional',$unidade);
//            $qtdViuvosM->where('unidade_prisional',$unidade);
//            $qtdEstadoCivilNIH->where('unidade_prisional',$unidade);
//            $qtdEstadoCivilNIM->where('unidade_prisional',$unidade);
//
//            //opcao_sexual
//            $qtdHeteroH->where('unidade_prisional',$unidade);
//            $qtdHeteroM->where('unidade_prisional',$unidade);
//            $qtdHomoH->where('unidade_prisional',$unidade);
//            $qtdHomoM->where('unidade_prisional',$unidade);
//            $qtdBiH->where('unidade_prisional',$unidade);
//            $qtdBiM->where('unidade_prisional',$unidade);
//            $qtdSexualidadeNaoDeclH->where('unidade_prisional',$unidade);
//            $qtdSexualidadeNaoDeclM->where('unidade_prisional',$unidade);
//            $qtdSexualidadeNIH->where('unidade_prisional',$unidade);
//            $qtdSexualidadeNIM->where('unidade_prisional',$unidade);
//
//            //etnia
//            $qtdEtniaBrancaH->where('unidade_prisional',$unidade);
//            $qtdEtniaBrancaM->where('unidade_prisional',$unidade);
//            $qtdEtniaIndígenaH->where('unidade_prisional',$unidade);
//            $qtdEtniaIndígenaM->where('unidade_prisional',$unidade);
//            $qtdEtniaPretaH->where('unidade_prisional',$unidade);
//            $qtdEtniaPretaM->where('unidade_prisional',$unidade);
//            $qtdEtniaPardaH->where('unidade_prisional',$unidade);
//            $qtdEtniaPardaM->where('unidade_prisional',$unidade);
//            $qtdEtniaAmarelaH->where('unidade_prisional',$unidade);
//            $qtdEtniaAmarelaM->where('unidade_prisional',$unidade);
//            $qtdEtniaNIH->where('unidade_prisional',$unidade);
//            $qtdEtniaNIM->where('unidade_prisional',$unidade);
//
//            // escolaridade
//            $qtdAnalfabetoH->where('unidade_prisional',$unidade);
//            $qtdAnalfabetoM->where('unidade_prisional',$unidade);
//            $qtdAlfabetizadoH->where('unidade_prisional',$unidade);
//            $qtdAlfabetizadoM->where('unidade_prisional',$unidade);
//            $qtdFundIncH->where('unidade_prisional',$unidade);
//            $qtdFundIncM->where('unidade_prisional',$unidade);
//            $qtdFundCompH->where('unidade_prisional',$unidade);
//            $qtdFundCompM->where('unidade_prisional',$unidade);
//            $qtdMedioIncompH->where('unidade_prisional',$unidade);
//            $qtdMedioIncompM->where('unidade_prisional',$unidade);
//            $qtdMedioCompH->where('unidade_prisional',$unidade);
//            $qtdMedioCompM->where('unidade_prisional',$unidade);
//            $qtdSuperiorIncompH->where('unidade_prisional',$unidade);
//            $qtdSuperiorIncompM->where('unidade_prisional',$unidade);
//            $qtdSuperiorCompH->where('unidade_prisional',$unidade);
//            $qtdSuperiorCompM->where('unidade_prisional',$unidade);
//            $qtdPosLatoSensuH->where('unidade_prisional',$unidade);
//            $qtdPosLatoSensuM->where('unidade_prisional',$unidade);
//            $qtdMestradoH->where('unidade_prisional',$unidade);
//            $qtdMestradoM->where('unidade_prisional',$unidade);
//            $qtdDoutoradoH->where('unidade_prisional',$unidade);
//            $qtdDoutoradoM->where('unidade_prisional',$unidade);
//            $qtdPosDoutoradoH->where('unidade_prisional',$unidade);
//            $qtdPosDoutoradoM->where('unidade_prisional',$unidade);
//            $qtdEscolaridadeNIH->where('unidade_prisional',$unidade);
//            $qtdEscolaridadeNIM->where('unidade_prisional',$unidade);
//
//            $qtdNacionalidadeNIH->where('unidade_prisional',$unidade);
//            $qtdNacionalidadeNIM->where('unidade_prisional',$unidade);
//
//            $qtdAdventistaH->where('unidade_prisional',$unidade);
//            $qtdAdventistaM->where('unidade_prisional',$unidade);
//            $qtdBudistaH->where('unidade_prisional',$unidade);
//            $qtdBudistaM->where('unidade_prisional',$unidade);
//            $qtdCatolicaH->where('unidade_prisional',$unidade);
//            $qtdCatolicaM->where('unidade_prisional',$unidade);
//            $qtdEspiritaH->where('unidade_prisional',$unidade);
//            $qtdEspiritaM->where('unidade_prisional',$unidade);
//            $qtdEvangelicaH->where('unidade_prisional',$unidade);
//            $qtdEvangelicaM->where('unidade_prisional',$unidade);
//            $qtdJudeuH->where('unidade_prisional',$unidade);
//            $qtdJudeuM->where('unidade_prisional',$unidade);
//            $qtdMormonH->where('unidade_prisional',$unidade);
//            $qtdMormonM->where('unidade_prisional',$unidade);
//            $qtdMulcumanaH->where('unidade_prisional',$unidade);
//            $qtdMulcumanaM->where('unidade_prisional',$unidade);
//            $qtdProtestanteH->where('unidade_prisional',$unidade);
//            $qtdProtestanteM->where('unidade_prisional',$unidade);
//            $qtdAgnosticoH->where('unidade_prisional',$unidade);
//            $qtdAgnosticoM->where('unidade_prisional',$unidade);
//            $qtdAteuH->where('unidade_prisional',$unidade);
//            $qtdAteuM->where('unidade_prisional',$unidade);
//            $qtdSemReligiaoH->where('unidade_prisional',$unidade);
//            $qtdSemReligiaoM->where('unidade_prisional',$unidade);
//            $qtdOutrasH->where('unidade_prisional',$unidade);
//            $qtdOutrasM->where('unidade_prisional',$unidade);
//            $qtdReligiaoNIH->where('unidade_prisional',$unidade);
//            $qtdReligiaoNIM->where('unidade_prisional',$unidade);
//
//            $qtdAlvara->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
//            $qtdAlvara->where('movimentacao_interno.status','Finalizada');
//            $qtdAlvara->where('interno.unidade_prisional', $unidade);
//            $qtdFuga->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
//            $qtdFuga->where('movimentacao_interno.status','Finalizada');
//            $qtdFuga->where('interno.unidade_prisional', $unidade);
//            $qtdRecambiamento->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
//            $qtdRecambiamento->where('movimentacao_interno.status','Finalizada');
//            $qtdRecambiamento->where('interno.unidade_prisional', $unidade);
//            $qtdSaidaJudicial->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
//            $qtdSaidaJudicial->where('movimentacao_interno.status','Finalizada');
//            $qtdSaidaJudicial->where('interno.unidade_prisional', $unidade);
//            $qtdExtradicao->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
//            $qtdExtradicao->where('movimentacao_interno.status','Finalizada');
//            $qtdExtradicao->where('interno.unidade_prisional', $unidade);
//            $qtdLivramento->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
//            $qtdLivramento->where('movimentacao_interno.status','Finalizada');
//            $qtdLivramento->where('interno.unidade_prisional', $unidade);
//            $qtdObito->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
//            $qtdObito->where('movimentacao_interno.status','Finalizada');
//            $qtdObito->where('interno.unidade_prisional', $unidade);
//            $qtdExtincaopunibilidade->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
//            $qtdExtincaopunibilidade->where('movimentacao_interno.status','Finalizada');
//            $qtdExtincaopunibilidade->where('interno.unidade_prisional', $unidade);
//        }
//        if(!is_null($pavilhao)){
//            $qtdUnidadeHA->where('pavilhao', $pavilhao);
//            $qtdUnidadeMA->where('pavilhao', $pavilhao);
//            $qtdUnidadeTotal->where('pavilhao', $pavilhao);
//
//            $qtdInternosLotSelecionada->where('pavilhao',$pavilhao);
//
//            $qtdRegimeNIH->where('pavilhao',$pavilhao);
//            $qtdRegimeNIM->where('pavilhao',$pavilhao);
//            $qtdRegimeFechadoH->where('pavilhao',$pavilhao);
//            $qtdRegimeFechadoM->where('pavilhao',$pavilhao);
//            $qtdRegimeSemiabertoH->where('pavilhao',$pavilhao);
//            $qtdRegimeSemiabertoM->where('pavilhao',$pavilhao);
//            $qtdRegimeAbertoH->where('pavilhao',$pavilhao);
//            $qtdRegimeAbertoM->where('pavilhao',$pavilhao);
//
//            $qtdSentenciadosNIH->where('pavilhao',$pavilhao);
//            $qtdSentenciadosNIM->where('pavilhao',$pavilhao);
//            $qtdSentenciadosH->where('pavilhao',$pavilhao);
//            $qtdSentenciadosM->where('pavilhao',$pavilhao);
//            $qtdProvisoriosH->where('pavilhao',$pavilhao);
//            $qtdProvisoriosM->where('pavilhao',$pavilhao);
//
//            $qtdSentenciadosMSNIH->where('pavilhao',$pavilhao);
//            $qtdSentenciadosMSNIM->where('pavilhao',$pavilhao);
//            $qtdSentenciadosMSH->where('pavilhao',$pavilhao);
//            $qtdSentenciadosMSM->where('pavilhao',$pavilhao);
//            $qtdProvisoriosMSH->where('pavilhao',$pavilhao);
//            $qtdProvisoriosMSM->where('pavilhao',$pavilhao);
//
//            $qtdMedidaDeSegurancaH->where('pavilhao',$pavilhao);
//            $qtdMedidaDeSegurancaM->where('pavilhao',$pavilhao);
//
//            $qtdHA->where('pavilhao',$pavilhao);
//            $qtdMA->where('pavilhao',$pavilhao);
//
//            $internosH->where('pavilhao',$pavilhao);
//            $internosM->where('pavilhao',$pavilhao);
//
//            $qtdFaccaoNIH->where('pavilhao',$pavilhao);
//            $qtdFaccaoNIM->where('pavilhao',$pavilhao);
//
//            $periculosidadeAltaH->where('pavilhao',$pavilhao);
//            $periculosidadeMediaH->where('pavilhao',$pavilhao);
//            $periculosidadeBaixaH->where('pavilhao',$pavilhao);
//            $periculosidadeNIH->where('pavilhao',$pavilhao);
//            $periculosidadeAltaM->where('pavilhao',$pavilhao);
//            $periculosidadeMediaM->where('pavilhao',$pavilhao);
//            $periculosidadeBaixaM->where('pavilhao',$pavilhao);
//            $periculosidadeNIM->where('pavilhao',$pavilhao);
//
//            $qtdLiderancaSimH->where('pavilhao',$pavilhao);
//            $qtdLiderancaNaoH->where('pavilhao',$pavilhao);
//            $qtdLiderancaNIH->where('pavilhao',$pavilhao);
//            $qtdLiderancaSimM->where('pavilhao',$pavilhao);
//            $qtdLiderancaNaoM->where('pavilhao',$pavilhao);
//            $qtdLiderancaNIM->where('pavilhao',$pavilhao);
//
//            //estado_civil
//            $qtdSolteirosH->where('pavilhao',$pavilhao);
//            $qtdSolteirosM->where('pavilhao',$pavilhao);
//            $qtdCasadosH->where('pavilhao',$pavilhao);
//            $qtdCasadosM->where('pavilhao',$pavilhao);
//            $qtdUniaoEstavelH->where('pavilhao',$pavilhao);
//            $qtdUniaoEstavelM->where('pavilhao',$pavilhao);
//            $qtdSeparadosH->where('pavilhao',$pavilhao);
//            $qtdSeparadosM->where('pavilhao',$pavilhao);
//            $qtdDivorciadosH->where('pavilhao',$pavilhao);
//            $qtdDivorciadosM->where('pavilhao',$pavilhao);
//            $qtdViuvosH->where('pavilhao',$pavilhao);
//            $qtdViuvosM->where('pavilhao',$pavilhao);
//            $qtdEstadoCivilNIH->where('pavilhao',$pavilhao);
//            $qtdEstadoCivilNIM->where('pavilhao',$pavilhao);
//
//            //opcao_sexual
//            $qtdHeteroH->where('pavilhao',$pavilhao);
//            $qtdHeteroM->where('pavilhao',$pavilhao);
//            $qtdHomoH->where('pavilhao',$pavilhao);
//            $qtdHomoM->where('pavilhao',$pavilhao);
//            $qtdBiH->where('pavilhao',$pavilhao);
//            $qtdBiM->where('pavilhao',$pavilhao);
//            $qtdSexualidadeNaoDeclH->where('pavilhao',$pavilhao);
//            $qtdSexualidadeNaoDeclM->where('pavilhao',$pavilhao);
//            $qtdSexualidadeNIH->where('pavilhao',$pavilhao);
//            $qtdSexualidadeNIM->where('pavilhao',$pavilhao);
//
//            //etnia
//            $qtdEtniaBrancaH->where('pavilhao',$pavilhao);
//            $qtdEtniaBrancaM->where('pavilhao',$pavilhao);
//            $qtdEtniaIndígenaH->where('pavilhao',$pavilhao);
//            $qtdEtniaIndígenaM->where('pavilhao',$pavilhao);
//            $qtdEtniaPretaH->where('pavilhao',$pavilhao);
//            $qtdEtniaPretaM->where('pavilhao',$pavilhao);
//            $qtdEtniaPardaH->where('pavilhao',$pavilhao);
//            $qtdEtniaPardaM->where('pavilhao',$pavilhao);
//            $qtdEtniaAmarelaH->where('pavilhao',$pavilhao);
//            $qtdEtniaAmarelaM->where('pavilhao',$pavilhao);
//            $qtdEtniaNIH->where('pavilhao',$pavilhao);
//            $qtdEtniaNIM->where('pavilhao',$pavilhao);
//
//            // escolaridade
//            $qtdAnalfabetoH->where('pavilhao',$pavilhao);
//            $qtdAnalfabetoM->where('pavilhao',$pavilhao);
//            $qtdAlfabetizadoH->where('pavilhao',$pavilhao);
//            $qtdAlfabetizadoM->where('pavilhao',$pavilhao);
//            $qtdFundIncH->where('pavilhao',$pavilhao);
//            $qtdFundIncM->where('pavilhao',$pavilhao);
//            $qtdFundCompH->where('pavilhao',$pavilhao);
//            $qtdFundCompM->where('pavilhao',$pavilhao);
//            $qtdMedioIncompH->where('pavilhao',$pavilhao);
//            $qtdMedioIncompM->where('pavilhao',$pavilhao);
//            $qtdMedioCompH->where('pavilhao',$pavilhao);
//            $qtdMedioCompM->where('pavilhao',$pavilhao);
//            $qtdSuperiorIncompH->where('pavilhao',$pavilhao);
//            $qtdSuperiorIncompM->where('pavilhao',$pavilhao);
//            $qtdSuperiorCompH->where('pavilhao',$pavilhao);
//            $qtdSuperiorCompM->where('pavilhao',$pavilhao);
//            $qtdPosLatoSensuH->where('pavilhao',$pavilhao);
//            $qtdPosLatoSensuM->where('pavilhao',$pavilhao);
//            $qtdMestradoH->where('pavilhao',$pavilhao);
//            $qtdMestradoM->where('pavilhao',$pavilhao);
//            $qtdDoutoradoH->where('pavilhao',$pavilhao);
//            $qtdDoutoradoM->where('pavilhao',$pavilhao);
//            $qtdPosDoutoradoH->where('pavilhao',$pavilhao);
//            $qtdPosDoutoradoM->where('pavilhao',$pavilhao);
//            $qtdEscolaridadeNIH->where('pavilhao',$pavilhao);
//            $qtdEscolaridadeNIM->where('pavilhao',$pavilhao);
//
//            $qtdNacionalidadeNIH->where('pavilhao',$pavilhao);
//            $qtdNacionalidadeNIM->where('pavilhao',$pavilhao);
//
//            $qtdAdventistaH->where('pavilhao',$pavilhao);
//            $qtdAdventistaM->where('pavilhao',$pavilhao);
//            $qtdBudistaH->where('pavilhao',$pavilhao);
//            $qtdBudistaM->where('pavilhao',$pavilhao);
//            $qtdCatolicaH->where('pavilhao',$pavilhao);
//            $qtdCatolicaM->where('pavilhao',$pavilhao);
//            $qtdEspiritaH->where('pavilhao',$pavilhao);
//            $qtdEspiritaM->where('pavilhao',$pavilhao);
//            $qtdEvangelicaH->where('pavilhao',$pavilhao);
//            $qtdEvangelicaM->where('pavilhao',$pavilhao);
//            $qtdJudeuH->where('pavilhao',$pavilhao);
//            $qtdJudeuM->where('pavilhao',$pavilhao);
//            $qtdMormonH->where('pavilhao',$pavilhao);
//            $qtdMormonM->where('pavilhao',$pavilhao);
//            $qtdMulcumanaH->where('pavilhao',$pavilhao);
//            $qtdMulcumanaM->where('pavilhao',$pavilhao);
//            $qtdProtestanteH->where('pavilhao',$pavilhao);
//            $qtdProtestanteM->where('pavilhao',$pavilhao);
//            $qtdAgnosticoH->where('pavilhao',$pavilhao);
//            $qtdAgnosticoM->where('pavilhao',$pavilhao);
//            $qtdAteuH->where('pavilhao',$pavilhao);
//            $qtdAteuM->where('pavilhao',$pavilhao);
//            $qtdSemReligiaoH->where('pavilhao',$pavilhao);
//            $qtdSemReligiaoM->where('pavilhao',$pavilhao);
//            $qtdOutrasH->where('pavilhao',$pavilhao);
//            $qtdOutrasM->where('pavilhao',$pavilhao);
//            $qtdReligiaoNIH->where('pavilhao',$pavilhao);
//            $qtdReligiaoNIM->where('pavilhao',$pavilhao);
//
//            //saida da unidade
//            $qtdAlvara->where('interno.pavilhao',$pavilhao);
//            $qtdFuga->where('interno.pavilhao',$pavilhao);
//            $qtdRecambiamento->where('interno.pavilhao',$pavilhao);
//            $qtdSaidaJudicial->where('interno.pavilhao',$pavilhao);
//            $qtdExtradicao->where('interno.pavilhao',$pavilhao);
//            $qtdLivramento->where('interno.pavilhao',$pavilhao);
//            $qtdObito->where('interno.pavilhao',$pavilhao);
//            $qtdExtincaopunibilidade->where('interno.pavilhao',$pavilhao);
//        }
//        if(!is_null($ala)) {
//            $qtdUnidadeHA->where('ala', $ala);
//            $qtdUnidadeMA->where('ala', $ala);
//            $qtdUnidadeTotal->where('ala', $ala);
//
//            $qtdInternosLotSelecionada->where('ala',$ala);
//
//            $qtdRegimeNIH->where('ala',$ala);
//            $qtdRegimeNIM->where('ala',$ala);
//            $qtdRegimeFechadoH->where('ala',$ala);
//            $qtdRegimeFechadoM->where('ala',$ala);
//            $qtdRegimeSemiabertoH->where('ala',$ala);
//            $qtdRegimeSemiabertoM->where('ala',$ala);
//            $qtdRegimeAbertoH->where('ala',$ala);
//            $qtdRegimeAbertoM->where('ala',$ala);
//
//            $qtdSentenciadosNIH->where('ala',$ala);
//            $qtdSentenciadosNIM->where('ala',$ala);
//            $qtdSentenciadosH->where('ala',$ala);
//            $qtdSentenciadosM->where('ala',$ala);
//            $qtdProvisoriosH->where('ala',$ala);
//            $qtdProvisoriosM->where('ala',$ala);
//
//            $qtdSentenciadosMSNIH->where('ala',$ala);
//            $qtdSentenciadosMSNIM->where('ala',$ala);
//            $qtdSentenciadosMSH->where('ala',$ala);
//            $qtdSentenciadosMSM->where('ala',$ala);
//            $qtdProvisoriosMSH->where('ala',$ala);
//            $qtdProvisoriosMSM->where('ala',$ala);
//
//            $qtdMedidaDeSegurancaH->where('ala',$ala);
//            $qtdMedidaDeSegurancaM->where('ala',$ala);
//
//            $qtdHA->where('ala',$ala);
//            $qtdMA->where('ala',$ala);
//
//            $internosH->where('ala',$ala);
//            $internosM->where('ala',$ala);
//
//            $qtdFaccaoNIH->where('ala',$ala);
//            $qtdFaccaoNIM->where('ala',$ala);
//
//            $periculosidadeAltaH->where('ala',$ala);
//            $periculosidadeMediaH->where('ala',$ala);
//            $periculosidadeBaixaH->where('ala',$ala);
//            $periculosidadeNIH->where('ala',$ala);
//            $periculosidadeAltaM->where('ala',$ala);
//            $periculosidadeMediaM->where('ala',$ala);
//            $periculosidadeBaixaM->where('ala',$ala);
//            $periculosidadeNIM->where('ala',$ala);
//
//            $qtdLiderancaSimH->where('ala',$ala);
//            $qtdLiderancaNaoH->where('ala',$ala);
//            $qtdLiderancaNIH->where('ala',$ala);
//            $qtdLiderancaSimM->where('ala',$ala);
//            $qtdLiderancaNaoM->where('ala',$ala);
//            $qtdLiderancaNIM->where('ala',$ala);
//
//            //estado_civil
//            $qtdSolteirosH->where('ala',$ala);
//            $qtdSolteirosM->where('ala',$ala);
//            $qtdCasadosH->where('ala',$ala);
//            $qtdCasadosM->where('ala',$ala);
//            $qtdUniaoEstavelH->where('ala',$ala);
//            $qtdUniaoEstavelM->where('ala',$ala);
//            $qtdSeparadosH->where('ala',$ala);
//            $qtdSeparadosM->where('ala',$ala);
//            $qtdDivorciadosH->where('ala',$ala);
//            $qtdDivorciadosM->where('ala',$ala);
//            $qtdViuvosH->where('ala',$ala);
//            $qtdViuvosM->where('ala',$ala);
//            $qtdEstadoCivilNIH->where('ala',$ala);
//            $qtdEstadoCivilNIM->where('ala',$ala);
//
//            //opcao_sexual
//            $qtdHeteroH->where('ala',$ala);
//            $qtdHeteroM->where('ala',$ala);
//            $qtdHomoH->where('ala',$ala);
//            $qtdHomoM->where('ala',$ala);
//            $qtdBiH->where('ala',$ala);
//            $qtdBiM->where('ala',$ala);
//            $qtdSexualidadeNaoDeclH->where('ala',$ala);
//            $qtdSexualidadeNaoDeclM->where('ala',$ala);
//            $qtdSexualidadeNIH->where('ala',$ala);
//            $qtdSexualidadeNIM->where('ala',$ala);
//
//            //etnia
//            $qtdEtniaBrancaH->where('ala',$ala);
//            $qtdEtniaBrancaM->where('ala',$ala);
//            $qtdEtniaIndígenaH->where('ala',$ala);
//            $qtdEtniaIndígenaM->where('ala',$ala);
//            $qtdEtniaPretaH->where('ala',$ala);
//            $qtdEtniaPretaM->where('ala',$ala);
//            $qtdEtniaPardaH->where('ala',$ala);
//            $qtdEtniaPardaM->where('ala',$ala);
//            $qtdEtniaAmarelaH->where('ala',$ala);
//            $qtdEtniaAmarelaM->where('ala',$ala);
//            $qtdEtniaNIH->where('ala',$ala);
//            $qtdEtniaNIM->where('ala',$ala);
//
//            // escolaridade
//            $qtdAnalfabetoH->where('ala',$ala);
//            $qtdAnalfabetoM->where('ala',$ala);
//            $qtdAlfabetizadoH->where('ala',$ala);
//            $qtdAlfabetizadoM->where('ala',$ala);
//            $qtdFundIncH->where('ala',$ala);
//            $qtdFundIncM->where('ala',$ala);
//            $qtdFundCompH->where('ala',$ala);
//            $qtdFundCompM->where('ala',$ala);
//            $qtdMedioIncompH->where('ala',$ala);
//            $qtdMedioIncompM->where('ala',$ala);
//            $qtdMedioCompH->where('ala',$ala);
//            $qtdMedioCompM->where('ala',$ala);
//            $qtdSuperiorIncompH->where('ala',$ala);
//            $qtdSuperiorIncompM->where('ala',$ala);
//            $qtdSuperiorCompH->where('ala',$ala);
//            $qtdSuperiorCompM->where('ala',$ala);
//            $qtdPosLatoSensuH->where('ala',$ala);
//            $qtdPosLatoSensuM->where('ala',$ala);
//            $qtdMestradoH->where('ala',$ala);
//            $qtdMestradoM->where('ala',$ala);
//            $qtdDoutoradoH->where('ala',$ala);
//            $qtdDoutoradoM->where('ala',$ala);
//            $qtdPosDoutoradoH->where('ala',$ala);
//            $qtdPosDoutoradoM->where('ala',$ala);
//            $qtdEscolaridadeNIH->where('ala',$ala);
//            $qtdEscolaridadeNIM->where('ala',$ala);
//
//            $qtdNacionalidadeNIH->where('ala',$ala);
//            $qtdNacionalidadeNIM->where('ala',$ala);
//
//
//            $qtdAdventistaH->where('ala',$ala);
//            $qtdAdventistaM->where('ala',$ala);
//            $qtdBudistaH->where('ala',$ala);
//            $qtdBudistaM->where('ala',$ala);
//            $qtdCatolicaH->where('ala',$ala);
//            $qtdCatolicaM->where('ala',$ala);
//            $qtdEspiritaH->where('ala',$ala);
//            $qtdEspiritaM->where('ala',$ala);
//            $qtdEvangelicaH->where('ala',$ala);
//            $qtdEvangelicaM->where('ala',$ala);
//            $qtdJudeuH->where('ala',$ala);
//            $qtdJudeuM->where('ala',$ala);
//            $qtdMormonH->where('ala',$ala);
//            $qtdMormonM->where('ala',$ala);
//            $qtdMulcumanaH->where('ala',$ala);
//            $qtdMulcumanaM->where('ala',$ala);
//            $qtdProtestanteH->where('ala',$ala);
//            $qtdProtestanteM->where('ala',$ala);
//            $qtdAgnosticoH->where('ala',$ala);
//            $qtdAgnosticoM->where('ala',$ala);
//            $qtdAteuH->where('ala',$ala);
//            $qtdAteuM->where('ala',$ala);
//            $qtdSemReligiaoH->where('ala',$ala);
//            $qtdSemReligiaoM->where('ala',$ala);
//            $qtdOutrasH->where('ala',$ala);
//            $qtdOutrasM->where('ala',$ala);
//            $qtdReligiaoNIH->where('ala',$ala);
//            $qtdReligiaoNIM->where('ala',$ala);
//
//            //saida da unidade
//            $qtdAlvara->where('interno.ala',$ala);
//            $qtdFuga->where('interno.ala',$ala);
//            $qtdRecambiamento->where('interno.ala',$ala);
//            $qtdSaidaJudicial->where('interno.ala',$ala);
//            $qtdExtradicao->where('interno.ala',$ala);
//            $qtdLivramento->where('interno.ala',$ala);
//            $qtdObito->where('interno.ala',$ala);
//            $qtdExtincaopunibilidade->where('interno.ala',$ala);
//        }
//        if(!is_null($cela)) {
//            $qtdUnidadeHA->where('cela', $cela);
//            $qtdUnidadeMA->where('cela', $cela);
//            $qtdUnidadeTotal->where('cela', $cela);
//
//            $qtdInternosLotSelecionada->where('cela',$cela);
//
//            $qtdRegimeNIH->where('cela',$cela);
//            $qtdRegimeNIM->where('cela',$cela);
//            $qtdRegimeFechadoH->where('cela',$cela);
//            $qtdRegimeFechadoM->where('cela',$cela);
//            $qtdRegimeSemiabertoH->where('cela',$cela);
//            $qtdRegimeSemiabertoM->where('cela',$cela);
//            $qtdRegimeAbertoH->where('cela',$cela);
//            $qtdRegimeAbertoM->where('cela',$cela);
//
//            $qtdSentenciadosNIH->where('cela',$cela);
//            $qtdSentenciadosNIM->where('cela',$cela);
//            $qtdSentenciadosH->where('cela',$cela);
//            $qtdSentenciadosM->where('cela',$cela);
//            $qtdProvisoriosH->where('cela',$cela);
//            $qtdProvisoriosM->where('cela',$cela);
//
//            $qtdSentenciadosMSNIH->where('cela',$cela);
//            $qtdSentenciadosMSNIM->where('cela',$cela);
//            $qtdSentenciadosMSH->where('cela',$cela);
//            $qtdSentenciadosMSM->where('cela',$cela);
//            $qtdProvisoriosMSH->where('cela',$cela);
//            $qtdProvisoriosMSM->where('cela',$cela);
//
//            $qtdMedidaDeSegurancaH->where('cela',$cela);
//            $qtdMedidaDeSegurancaM->where('cela',$cela);
//
//            $qtdHA->where('cela',$cela);
//            $qtdMA->where('cela',$cela);
//
//            $internosH->where('cela',$cela);
//            $internosM->where('cela',$cela);
//
//            $qtdFaccaoNIH->where('cela',$cela);
//            $qtdFaccaoNIM->where('cela',$cela);
//
//            $periculosidadeAltaH->where('cela',$cela);
//            $periculosidadeMediaH->where('cela',$cela);
//            $periculosidadeBaixaH->where('cela',$cela);
//            $periculosidadeNIH->where('cela',$cela);
//            $periculosidadeAltaM->where('cela',$cela);
//            $periculosidadeMediaM->where('cela',$cela);
//            $periculosidadeBaixaM->where('cela',$cela);
//            $periculosidadeNIM->where('cela',$cela);
//
//            $qtdLiderancaSimH->where('cela',$cela);
//            $qtdLiderancaNaoH->where('cela',$cela);
//            $qtdLiderancaNIH->where('cela',$cela);
//            $qtdLiderancaSimM->where('cela',$cela);
//            $qtdLiderancaNaoM->where('cela',$cela);
//            $qtdLiderancaNIM->where('cela',$cela);
//            
//            //estado_civil
//            $qtdSolteirosH->where('cela',$cela);
//            $qtdSolteirosM->where('cela',$cela);
//            $qtdCasadosH->where('cela',$cela);
//            $qtdCasadosM->where('cela',$cela);
//            $qtdUniaoEstavelH->where('cela',$cela);
//            $qtdUniaoEstavelM->where('cela',$cela);
//            $qtdSeparadosH->where('cela',$cela);
//            $qtdSeparadosM->where('cela',$cela);
//            $qtdDivorciadosH->where('cela',$cela);
//            $qtdDivorciadosM->where('cela',$cela);
//            $qtdViuvosH->where('cela',$cela);
//            $qtdViuvosM->where('cela',$cela);
//            $qtdEstadoCivilNIH->where('cela',$cela);
//            $qtdEstadoCivilNIM->where('cela',$cela);
//
//            //opcao_sexual
//            $qtdHeteroH->where('cela',$cela);
//            $qtdHeteroM->where('cela',$cela);
//            $qtdHomoH->where('cela',$cela);
//            $qtdHomoM->where('cela',$cela);
//            $qtdBiH->where('cela',$cela);
//            $qtdBiM->where('cela',$cela);
//            $qtdSexualidadeNaoDeclH->where('cela',$cela);
//            $qtdSexualidadeNaoDeclM->where('cela',$cela);
//            $qtdSexualidadeNIH->where('cela',$cela);
//            $qtdSexualidadeNIM->where('cela',$cela);
//
//            //etnia
//            $qtdEtniaBrancaH->where('cela',$cela);
//            $qtdEtniaBrancaM->where('cela',$cela);
//            $qtdEtniaIndígenaH->where('cela',$cela);
//            $qtdEtniaIndígenaM->where('cela',$cela);
//            $qtdEtniaPretaH->where('cela',$cela);
//            $qtdEtniaPretaM->where('cela',$cela);
//            $qtdEtniaPardaH->where('cela',$cela);
//            $qtdEtniaPardaM->where('cela',$cela);
//            $qtdEtniaAmarelaH->where('cela',$cela);
//            $qtdEtniaAmarelaM->where('cela',$cela);
//            $qtdEtniaNIH->where('cela',$cela);
//            $qtdEtniaNIM->where('cela',$cela);
//
//            // escolaridade
//            $qtdAnalfabetoH->where('cela',$cela);
//            $qtdAnalfabetoM->where('cela',$cela);
//            $qtdAlfabetizadoH->where('cela',$cela);
//            $qtdAlfabetizadoM->where('cela',$cela);
//            $qtdFundIncH->where('cela',$cela);
//            $qtdFundIncM->where('cela',$cela);
//            $qtdFundCompH->where('cela',$cela);
//            $qtdFundCompM->where('cela',$cela);
//            $qtdMedioIncompH->where('cela',$cela);
//            $qtdMedioIncompM->where('cela',$cela);
//            $qtdMedioCompH->where('cela',$cela);
//            $qtdMedioCompM->where('cela',$cela);
//            $qtdSuperiorIncompH->where('cela',$cela);
//            $qtdSuperiorIncompM->where('cela',$cela);
//            $qtdSuperiorCompH->where('cela',$cela);
//            $qtdSuperiorCompM->where('cela',$cela);
//            $qtdPosLatoSensuH->where('cela',$cela);
//            $qtdPosLatoSensuM->where('cela',$cela);
//            $qtdMestradoH->where('cela',$cela);
//            $qtdMestradoM->where('cela',$cela);
//            $qtdDoutoradoH->where('cela',$cela);
//            $qtdDoutoradoM->where('cela',$cela);
//            $qtdPosDoutoradoH->where('cela',$cela);
//            $qtdPosDoutoradoM->where('cela',$cela);
//            $qtdEscolaridadeNIH->where('cela',$cela);
//            $qtdEscolaridadeNIM->where('cela',$cela);
//
//            $qtdNacionalidadeNIH->where('cela',$cela);
//            $qtdNacionalidadeNIM->where('cela',$cela);
//
//            $qtdAdventistaH->where('cela',$cela);
//            $qtdAdventistaM->where('cela',$cela);
//            $qtdBudistaH->where('cela',$cela);
//            $qtdBudistaM->where('cela',$cela);
//            $qtdCatolicaH->where('cela',$cela);
//            $qtdCatolicaM->where('cela',$cela);
//            $qtdEspiritaH->where('cela',$cela);
//            $qtdEspiritaM->where('cela',$cela);
//            $qtdEvangelicaH->where('cela',$cela);
//            $qtdEvangelicaM->where('cela',$cela);
//            $qtdJudeuH->where('cela',$cela);
//            $qtdJudeuM->where('cela',$cela);
//            $qtdMormonH->where('cela',$cela);
//            $qtdMormonM->where('cela',$cela);
//            $qtdMulcumanaH->where('cela',$cela);
//            $qtdMulcumanaM->where('cela',$cela);
//            $qtdProtestanteH->where('cela',$cela);
//            $qtdProtestanteM->where('cela',$cela);
//            $qtdAgnosticoH->where('cela',$cela);
//            $qtdAgnosticoM->where('cela',$cela);
//            $qtdAteuH->where('cela',$cela);
//            $qtdAteuM->where('cela',$cela);
//            $qtdSemReligiaoH->where('cela',$cela);
//            $qtdSemReligiaoM->where('cela',$cela);
//            $qtdOutrasH->where('cela',$cela);
//            $qtdOutrasM->where('cela',$cela);
//            $qtdReligiaoNIH->where('cela',$cela);
//            $qtdReligiaoNIM->where('cela',$cela);
//
//            //saida da unidade
//            $qtdAlvara->where('interno.cela',$cela);
//            $qtdFuga->where('interno.cela',$cela);
//            $qtdRecambiamento->where('interno.cela',$cela);
//            $qtdSaidaJudicial->where('interno.cela',$cela);
//            $qtdExtradicao->where('interno.cela',$cela);
//            $qtdLivramento->where('interno.cela',$cela);
//            $qtdObito->where('interno.cela',$cela);
//            $qtdExtincaopunibilidade->where('interno.cela',$cela);
//        }
//
//        $internosH=$internosH->get();
//        foreach($internosH as $interno){
//            if(is_null($interno->data_nascimento)){
//                $faixaEtaria_naoInformadaH++;
//            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 17){
//                $faixaEtaria_zero_a_dezesseteH++;
//            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 18 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 24){
//                $faixaEtaria_dezoito_a_vinteequatroH++;
//            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 25 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 29){
//                $faixaEtaria_vinteecinco_a_vinteenoveH++;
//            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 30 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 34){
//                $faixaEtaria_trinta_a_trintaequatroH++;
//            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 35 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 45){
//                $faixaEtaria_trintaecinco_a_quarentaecincoH++;
//            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 46 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 60){
//                $faixaEtaria_quarentaeseis_a_sessentaH++;
//            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 61 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 70){
//                $faixaEtaria_sessentaeum_a_setentaH++;
//            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) > 70){
//                $faixaEtaria_maisDeSetentaH++;
//            }
//        }
//
//        $internosM=$internosM->get();
//        foreach($internosM as $interno){
//            if(is_null($interno->data_nascimento)){
//                $faixaEtaria_naoInformadaM++;
//            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 17){
//                $faixaEtaria_zero_a_dezesseteM++;
//            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 18 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 24){
//                $faixaEtaria_dezoito_a_vinteequatroM++;
//            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 25 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 29){
//                $faixaEtaria_vinteecinco_a_vinteenoveM++;
//            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 30 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 34){
//                $faixaEtaria_trinta_a_trintaequatroM++;
//            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 35 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 45){
//                $faixaEtaria_trintaecinco_a_quarentaecincoM++;
//            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 46 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 60){
//                $faixaEtaria_quarentaeseis_a_sessentaM++;
//            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 61 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 70){
//                $faixaEtaria_sessentaeum_a_setentaM++;
//            }elseif(Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) > 70){
//                $faixaEtaria_maisDeSetentaM++;
//            }
//        }
//
//        $nacionalidades = DB::table('interno')->select(DB::raw('count(*) as qtdd, naturalidade_pais'))
//            ->where('status','A')
//            ->groupBy('naturalidade_pais')->orderBy('qtdd','DESC')->get();
//        foreach($nacionalidades as $key=>$nacionalidade){
//            $nacionalidades[$key]->qtdH = DB::table('interno')->where('sexo','M')->where('status','A')->where('naturalidade_pais', $nacionalidade->naturalidade_pais);
//            $nacionalidades[$key]->qtdM = DB::table('interno')->where('sexo','F')->where('status','A')->where('naturalidade_pais', $nacionalidade->naturalidade_pais);
//
//            if(!is_null($unidade)) {
//                $nacionalidades[$key]->qtdH->where('unidade_prisional',$unidade);
//                $nacionalidades[$key]->qtdM->where('unidade_prisional',$unidade);
//            }
//            if(!is_null($pavilhao)) {
//                $nacionalidades[$key]->qtdH->where('pavilhao',$pavilhao);
//                $nacionalidades[$key]->qtdM->where('pavilhao',$pavilhao);
//            }
//            if(!is_null($ala)) {
//                $nacionalidades[$key]->qtdH->where('ala',$ala);
//                $nacionalidades[$key]->qtdM->where('ala',$ala);
//            }
//            if(!is_null($cela)) {
//                $nacionalidades[$key]->qtdH->where('cela',$cela);
//                $nacionalidades[$key]->qtdM->where('cela',$cela);
//            }
//
//            $nacionalidades[$key]->qtdH = $nacionalidades[$key]->qtdH->count();
//            $nacionalidades[$key]->qtdM = $nacionalidades[$key]->qtdM->count();
//            $nacionalidades[$key]->qtd = $nacionalidades[$key]->qtdH + $nacionalidades[$key]->qtdM;
//        }
//
//        $qtdNacionalidadeNIH = $qtdNacionalidadeNIH->count();
//        $qtdNacionalidadeNIM = $qtdNacionalidadeNIM->count();
//
//        $faccoes = DB::table('faccao')->select('id','nome','sigla')->get();
//        foreach($faccoes as $key=>$faccao){
//            $faccoes[$key]->qtdH = DB::table('interno')->where('sexo','M')->where('status','A')->where('faccao', $faccao->id);
//            $faccoes[$key]->qtdM = DB::table('interno')->where('sexo','F')->where('status','A')->where('faccao', $faccao->id);
//
//            if(!is_null($unidade)) {
//                $faccoes[$key]->qtdH->where('unidade_prisional',$unidade);
//                $faccoes[$key]->qtdM->where('unidade_prisional',$unidade);
//            }
//            if(!is_null($pavilhao)) {
//                $faccoes[$key]->qtdH->where('pavilhao',$pavilhao);
//                $faccoes[$key]->qtdM->where('pavilhao',$pavilhao);
//            }
//            if(!is_null($ala)) {
//                $faccoes[$key]->qtdH->where('ala',$ala);
//                $faccoes[$key]->qtdM->where('ala',$ala);
//            }
//            if(!is_null($cela)) {
//                $faccoes[$key]->qtdH->where('cela',$cela);
//                $faccoes[$key]->qtdM->where('cela',$cela);
//            }
//
//            $faccoes[$key]->qtdH = $faccoes[$key]->qtdH->count();
//            $faccoes[$key]->qtdM = $faccoes[$key]->qtdM->count();
//            $faccoes[$key]->qtd = $faccoes[$key]->qtdH + $faccoes[$key]->qtdM;
//        }
//
//        $faccoes = $faccoes->sortBy('qtd')->reverse();
//
//        $qtdUnidadeHA = $qtdUnidadeHA->get();
//        $qtdUnidadeMA = $qtdUnidadeMA->get();
//        $qtdUnidadeTotal = $qtdUnidadeTotal->get();
//
//        $qtdFaccaoNIH = $qtdFaccaoNIH->count();
//        $qtdFaccaoNIM = $qtdFaccaoNIM->count();
//
//        $qtdInternosLotSelecionada=$qtdInternosLotSelecionada->count();
//
//        $qtdRegimeNIH=$qtdRegimeNIH->count();
//        $qtdRegimeNIM=$qtdRegimeNIM->count();
//        $qtdRegimeFechadoH=$qtdRegimeFechadoH->count();
//        $qtdRegimeFechadoM=$qtdRegimeFechadoM->count();
//        $qtdRegimeSemiabertoH=$qtdRegimeSemiabertoH->count();
//        $qtdRegimeSemiabertoM=$qtdRegimeSemiabertoM->count();
//        $qtdRegimeAbertoH=$qtdRegimeAbertoH->count();
//        $qtdRegimeAbertoM=$qtdRegimeAbertoM->count();
//
//        $qtdSentenciadosNIH=$qtdSentenciadosNIH->count();
//        $qtdSentenciadosNIM=$qtdSentenciadosNIM->count();
//        $qtdSentenciadosH=$qtdSentenciadosH->count();
//        $qtdSentenciadosM=$qtdSentenciadosM->count();
//        $qtdProvisoriosH=$qtdProvisoriosH->count();
//        $qtdProvisoriosM=$qtdProvisoriosM->count();
//
//        $qtdSentenciadosMSNIH=$qtdSentenciadosMSNIH->count();
//        $qtdSentenciadosMSNIM=$qtdSentenciadosMSNIM->count();
//        $qtdSentenciadosMSH=$qtdSentenciadosMSH->count();
//        $qtdSentenciadosMSM=$qtdSentenciadosMSM->count();
//        $qtdProvisoriosMSH=$qtdProvisoriosMSH->count();
//        $qtdProvisoriosMSM=$qtdProvisoriosMSM->count();
//
//        $qtdMedidaDeSegurancaH=$qtdMedidaDeSegurancaH->count();
//        $qtdMedidaDeSegurancaM=$qtdMedidaDeSegurancaM->count();
//
//        $qtdHA=$qtdHA->count();
//        $qtdMA=$qtdMA->count();
//
//        $periculosidadeAltaH=$periculosidadeAltaH->count();
//        $periculosidadeMediaH=$periculosidadeMediaH->count();
//        $periculosidadeBaixaH=$periculosidadeBaixaH->count();
//        $periculosidadeNIH=$periculosidadeNIH->count();
//        $periculosidadeAltaM=$periculosidadeAltaM->count();
//        $periculosidadeMediaM=$periculosidadeMediaM->count();
//        $periculosidadeBaixaM=$periculosidadeBaixaM->count();
//        $periculosidadeNIM=$periculosidadeNIM->count();
//
//        $qtdLiderancaSimH=$qtdLiderancaSimH->count();
//        $qtdLiderancaNaoH=$qtdLiderancaNaoH->count();
//        $qtdLiderancaNIH=$qtdLiderancaNIH->count();
//        $qtdLiderancaSimM=$qtdLiderancaSimM->count();
//        $qtdLiderancaNaoM=$qtdLiderancaNaoM->count();
//        $qtdLiderancaNIM=$qtdLiderancaNIM->count();
//
//        //estado_civil
//        $qtdSolteirosH=$qtdSolteirosH->count();
//        $qtdSolteirosM=$qtdSolteirosM->count();
//        $qtdCasadosH=$qtdCasadosH->count();
//        $qtdCasadosM=$qtdCasadosM->count();
//        $qtdUniaoEstavelH=$qtdUniaoEstavelH->count();
//        $qtdUniaoEstavelM=$qtdUniaoEstavelM->count();
//        $qtdSeparadosH=$qtdSeparadosH->count();
//        $qtdSeparadosM=$qtdSeparadosM->count();
//        $qtdDivorciadosH=$qtdDivorciadosH->count();
//        $qtdDivorciadosM=$qtdDivorciadosM->count();
//        $qtdViuvosH=$qtdViuvosH->count();
//        $qtdViuvosM=$qtdViuvosM->count();
//        $qtdEstadoCivilNIH=$qtdEstadoCivilNIH->count();
//        $qtdEstadoCivilNIM=$qtdEstadoCivilNIM->count();
//
//        //opcao_sexual
//        $qtdHeteroH = $qtdHeteroH->count();
//        $qtdHeteroM = $qtdHeteroM->count();
//        $qtdHomoH = $qtdHomoH->count();
//        $qtdHomoM = $qtdHomoM->count();
//        $qtdBiH = $qtdBiH->count();
//        $qtdBiM = $qtdBiM->count();
//        $qtdSexualidadeNaoDeclH = $qtdSexualidadeNaoDeclH->count();
//        $qtdSexualidadeNaoDeclM = $qtdSexualidadeNaoDeclM->count();
//        $qtdSexualidadeNIH = $qtdSexualidadeNIH->count();
//        $qtdSexualidadeNIM = $qtdSexualidadeNIM->count();
//
//        //etnia
//        $qtdEtniaBrancaH=$qtdEtniaBrancaH->count();
//        $qtdEtniaBrancaM=$qtdEtniaBrancaM->count();
//        $qtdEtniaIndígenaH=$qtdEtniaIndígenaH->count();
//        $qtdEtniaIndígenaM=$qtdEtniaIndígenaM->count();
//        $qtdEtniaPretaH=$qtdEtniaPretaH->count();
//        $qtdEtniaPretaM=$qtdEtniaPretaM->count();
//        $qtdEtniaPardaH=$qtdEtniaPardaH->count();
//        $qtdEtniaPardaM=$qtdEtniaPardaM->count();
//        $qtdEtniaAmarelaH=$qtdEtniaAmarelaH->count();
//        $qtdEtniaAmarelaM=$qtdEtniaAmarelaM->count();
//        $qtdEtniaNIH=$qtdEtniaNIH->count();
//        $qtdEtniaNIM=$qtdEtniaNIM->count();
//
//        // escolaridade
//        $qtdAnalfabetoH=$qtdAnalfabetoH->count();
//        $qtdAnalfabetoM=$qtdAnalfabetoM->count();
//        $qtdAlfabetizadoH=$qtdAlfabetizadoH->count();
//        $qtdAlfabetizadoM=$qtdAlfabetizadoM->count();
//        $qtdFundIncH=$qtdFundIncH->count();
//        $qtdFundIncM=$qtdFundIncM->count();
//        $qtdFundCompH=$qtdFundCompH->count();
//        $qtdFundCompM=$qtdFundCompM->count();
//        $qtdMedioIncompH=$qtdMedioIncompH->count();
//        $qtdMedioIncompM=$qtdMedioIncompM->count();
//        $qtdMedioCompH=$qtdMedioCompH->count();
//        $qtdMedioCompM=$qtdMedioCompM->count();
//        $qtdSuperiorIncompH=$qtdSuperiorIncompH->count();
//        $qtdSuperiorIncompM=$qtdSuperiorIncompM->count();
//        $qtdSuperiorCompH=$qtdSuperiorCompH->count();
//        $qtdSuperiorCompM=$qtdSuperiorCompM->count();
//        $qtdPosLatoSensuH=$qtdPosLatoSensuH->count();
//        $qtdPosLatoSensuM=$qtdPosLatoSensuM->count();
//        $qtdMestradoH=$qtdMestradoH->count();
//        $qtdMestradoM=$qtdMestradoM->count();
//        $qtdDoutoradoH=$qtdDoutoradoH->count();
//        $qtdDoutoradoM=$qtdDoutoradoM->count();
//        $qtdPosDoutoradoH=$qtdPosDoutoradoH->count();
//        $qtdPosDoutoradoM=$qtdPosDoutoradoM->count();
//        $qtdEscolaridadeNIH=$qtdEscolaridadeNIH->count();
//        $qtdEscolaridadeNIM=$qtdEscolaridadeNIM->count();
//
//        $qtdAdventistaH=$qtdAdventistaH->count();
//        $qtdAdventistaM=$qtdAdventistaM->count();
//        $qtdBudistaH=$qtdBudistaH->count();
//        $qtdBudistaM=$qtdBudistaM->count();
//        $qtdCatolicaH=$qtdCatolicaH->count();
//        $qtdCatolicaM=$qtdCatolicaM->count();
//        $qtdEspiritaH=$qtdEspiritaH->count();
//        $qtdEspiritaM=$qtdEspiritaM->count();
//        $qtdEvangelicaH=$qtdEvangelicaH->count();
//        $qtdEvangelicaM=$qtdEvangelicaM->count();
//        $qtdJudeuH=$qtdJudeuH->count();
//        $qtdJudeuM=$qtdJudeuM->count();
//        $qtdMormonH=$qtdMormonH->count();
//        $qtdMormonM=$qtdMormonM->count();
//        $qtdMulcumanaH=$qtdMulcumanaH->count();
//        $qtdMulcumanaM=$qtdMulcumanaM->count();
//        $qtdProtestanteH=$qtdProtestanteH->count();
//        $qtdProtestanteM=$qtdProtestanteM->count();
//        $qtdAgnosticoH=$qtdAgnosticoH->count();
//        $qtdAgnosticoM=$qtdAgnosticoM->count();
//        $qtdAteuH=$qtdAteuH->count();
//        $qtdAteuM=$qtdAteuM->count();
//        $qtdSemReligiaoH=$qtdSemReligiaoH->count();
//        $qtdSemReligiaoM=$qtdSemReligiaoM->count();
//        $qtdOutrasH=$qtdOutrasH->count();
//        $qtdOutrasM=$qtdOutrasM->count();
//        $qtdReligiaoNIH=$qtdReligiaoNIH->count();
//        $qtdReligiaoNIM=$qtdReligiaoNIM->count();
//
//        //saida da unidade
//        $qtdAlvara = $qtdAlvara->count();
//        $qtdFuga = $qtdFuga->count();
//        $qtdRecambiamento = $qtdRecambiamento->count();
//        $qtdSaidaJudicial = $qtdSaidaJudicial->count();
//        $qtdExtradicao = $qtdExtradicao->count();
//        $qtdLivramento = $qtdLivramento->count();
//        $qtdObito = $qtdObito->count();
//        $qtdExtincaopunibilidade = $qtdExtincaopunibilidade->count();
//
//        $dataHora = Tdata::TtimeStampHojeBrTxt();
//
//        $qntReingresso = $this->getReigressoUnidade(TData::TdataMysql($request->get('data')));
//
//        //$this->idadeVisita();
//
//        return view('estatisticas.show.estatisticas', compact('unidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome','faccoes','qtdFaccaoNIH','qtdFaccaoNIM',
//            'qtdInternosLotSelecionada','qtdRegimeFechadoH', 'qtdRegimeSemiabertoH', 'qtdRegimeAbertoH','qtdRegimeNIH', 'qtdRegimeNIM', 'dataHora',
//            'qtdRegimeFechadoM', 'qtdRegimeSemiabertoM', 'qtdRegimeAbertoM','qtdMedidaDeSegurancaH', 'qtdMedidaDeSegurancaM',
//            'qtdSentenciadosH','qtdProvisoriosH', 'qtdSentenciadosNIH', 'qtdSentenciadosM','qtdProvisoriosM', 'qtdSentenciadosNIM', 'qtdHA', 'qtdMA', 'qtdSexoNI',
//            'faixaEtaria_naoInformada','faixaEtaria_zero_a_dezessete','faixaEtaria_dezoito_a_vinteequatro','faixaEtaria_vinteecinco_a_vinteenove',
//            'faixaEtaria_trinta_a_trintaequatro','faixaEtaria_trintaecinco_a_quarentaecinco','faixaEtaria_quarentaeseis_a_sessenta',
//            'faixaEtaria_sessentaeum_a_setenta','faixaEtaria_maisDeSetenta',
//            'faixaEtaria_naoInformadaH','faixaEtaria_zero_a_dezesseteH','faixaEtaria_dezoito_a_vinteequatroH','faixaEtaria_vinteecinco_a_vinteenoveH',
//            'faixaEtaria_trinta_a_trintaequatroH','faixaEtaria_trintaecinco_a_quarentaecincoH','faixaEtaria_quarentaeseis_a_sessentaH',
//            'faixaEtaria_sessentaeum_a_setentaH','faixaEtaria_maisDeSetentaH',
//            'faixaEtaria_naoInformadaM','faixaEtaria_zero_a_dezesseteM','faixaEtaria_dezoito_a_vinteequatroM','faixaEtaria_vinteecinco_a_vinteenoveM',
//            'faixaEtaria_trinta_a_trintaequatroM','faixaEtaria_trintaecinco_a_quarentaecincoM','faixaEtaria_quarentaeseis_a_sessentaM',
//            'faixaEtaria_sessentaeum_a_setentaM','faixaEtaria_maisDeSetentaM','periculosidadeAltaH','periculosidadeMediaH','periculosidadeBaixaH','periculosidadeNIH',
//            'periculosidadeAltaM','periculosidadeMediaM','periculosidadeBaixaM','periculosidadeNIM','qtdLiderancaSimH','qtdLiderancaNaoH','qtdLiderancaNIH',
//            'qtdLiderancaSimM','qtdLiderancaNaoM','qtdLiderancaNIM', 'nacionalidades', 'qtdNacionalidadeNIH', 'qtdNacionalidadeNIM', 'qtdUnidadeHA', 'qtdUnidadeMA', 'qtdUnidadeTotal',
//
//            'qtdSentenciadosMSNIH',
//            'qtdSentenciadosMSNIM',
//            'qtdSentenciadosMSH',
//            'qtdSentenciadosMSM',
//            'qtdProvisoriosMSH',
//            'qtdProvisoriosMSM',
//
//            //estado_civil
//            'qtdSolteirosH','qtdSolteirosM','qtdCasadosH', 'qtdCasadosM', 'qtdUniaoEstavelH', 'qtdUniaoEstavelM', 'qtdSeparadosH', 'qtdSeparadosM',
//            'qtdDivorciadosH', 'qtdDivorciadosM', 'qtdViuvosH', 'qtdViuvosM', 'qtdEstadoCivilNIH', 'qtdEstadoCivilNIM',
//
//            //etnia
//            'qtdEtniaBrancaH', 'qtdEtniaBrancaM', 'qtdEtniaIndígenaH', 'qtdEtniaIndígenaM', 'qtdEtniaPretaH', 'qtdEtniaPretaM', 'qtdEtniaPardaH',
//            'qtdEtniaPardaM', 'qtdEtniaAmarelaH', 'qtdEtniaAmarelaM', 'qtdEtniaNIH', 'qtdEtniaNIM',
//
//            //opcao_sexual
//            'qtdHeteroH', 'qtdHeteroM', 'qtdHomoH', 'qtdHomoM', 'qtdBiH', 'qtdBiM', 'qtdSexualidadeNaoDeclH', 'qtdSexualidadeNaoDeclM',
//            'qtdSexualidadeNIH', 'qtdSexualidadeNIM',
//
//            // escolaridade
//            'qtdAnalfabetoH', 'qtdAnalfabetoM', 'qtdAlfabetizadoH', 'qtdAlfabetizadoM', 'qtdFundIncH', 'qtdFundIncM', 'qtdFundCompH', 'qtdFundCompM',
//            'qtdMedioIncompH', 'qtdMedioIncompM', 'qtdMedioCompH', 'qtdMedioCompM', 'qtdSuperiorIncompH', 'qtdSuperiorIncompM', 'qtdSuperiorCompH',
//            'qtdSuperiorCompM', 'qtdPosLatoSensuH', 'qtdPosLatoSensuM', 'qtdMestradoH', 'qtdMestradoM', 'qtdDoutoradoH', 'qtdDoutoradoM',
//            'qtdPosDoutoradoH', 'qtdPosDoutoradoM', 'qtdEscolaridadeNIH', 'qtdEscolaridadeNIM',
//
//            //religião
//            'qtdAdventistaH', 'qtdAdventistaM', 'qtdBudistaH', 'qtdBudistaM', 'qtdCatolicaH', 'qtdCatolicaM', 'qtdEspiritaH', 'qtdEspiritaM', 'qtdEvangelicaH',
//            'qtdEvangelicaM', 'qtdJudeuH', 'qtdJudeuM', 'qtdMormonH', 'qtdMormonM', 'qtdMulcumanaH', 'qtdMulcumanaM', 'qtdProtestanteH', 'qtdProtestanteM',
//            'qtdAgnosticoH', 'qtdAgnosticoM', 'qtdAteuH', 'qtdAteuM', 'qtdSemReligiaoH', 'qtdSemReligiaoM', 'qtdOutrasH', 'qtdOutrasM', 'qtdReligiaoNIH', 'qtdReligiaoNIM',
//
//            //saida da unidade
//            'qtdAlvara', 'qtdFuga', 'qtdRecambiamento', 'qtdSaidaJudicial', 'qtdExtradicao', 'qtdLivramento', 'qtdObito', 'qtdExtincaopunibilidade', 'qntReingresso', 'detalhesUnidades'
//
//        ));
//    }
//
//    public static function getTotalInternosBysigla($sigla){
//
//        $internos =  DB::table('interno')
//            ->leftJoin('unidade', 'unidade.id', 'interno.unidade_prisional')
//            ->where('unidade.sigla', $sigla)
//            ->where('interno.status', 'A')
//            ->groupBy('unidade.sigla')
//            ->select('unidade.sigla', DB::raw('COUNT(*) as soma'))
//            ->get()->first();
//
//        return $internos;
//    }
//
//    public function extrairBySexo($dados, $campo){
//        $extraidos = Array();
//        foreach ($dados as $dado){
//            array_push($extraidos, [$dado->$campo, $dado->sexo]);
//        }
//        return $extraidos;
//    }
//
//    public function idadeVisita($filtros, $valoresFiltros){
//        // $visitas = DB::table('visita')->where('visita.status', 'A');
//        $visitas = DB::table('visita');
//
//        if($filtros != []){
//            $visitas->leftJoin('visita_interno', 'visita_interno.fk_visita', 'visita.id');
//            $visitas->leftJoin('interno', 'visita_interno.fk_interno', 'interno.id');
//        }
//        foreach($filtros as $x => $filtro){
//            $visitas->where($filtro, $valoresFiltros[$x]);
//        }
//
//        $visitasLiderancaSim = clone $visitas;
//        if($filtros == []) {
//            $visitasLiderancaSim->leftJoin('visita_interno', 'visita_interno.fk_visita', 'visita.id');
//            $visitasLiderancaSim->leftJoin('interno', 'visita_interno.fk_interno', 'interno.id');
//        }
//
//        $visitasLiderancaSim = $visitasLiderancaSim->select('interno.lideranca', 'visita.sexo',DB::raw('COUNT( * ) as qntLideranca'))->groupBy('interno.lideranca','visita.sexo')->orderBy('interno.lideranca','visita.sexo')->get();
//
//        if(isset($visitasLiderancaSim[0])) $visitasLiderancaSimF = $visitasLiderancaSim[0];
//        else $visitasLiderancaSimF = 0;
//        if(isset($visitasLiderancaSim[1]))  $visitasLiderancaSimM = $visitasLiderancaSim[1];
//        else $visitasLiderancaSimM = 0;
//        if(isset($visitasLiderancaSim[2]))  $visitasLiderancaNaoF = $visitasLiderancaSim[2];
//        else $visitasLiderancaNaoF = 0;
//        if(isset($visitasLiderancaSim[3]))  $visitasLiderancaNaoM = $visitasLiderancaSim[3];
//        else $visitasLiderancaNaoM = 0;
//        if(isset($visitasLiderancaSim[4]))  $visitasLiderancaNaoInformadoF = $visitasLiderancaSim[4];
//        else $visitasLiderancaNaoInformadoF = 0;
//        if(isset($visitasLiderancaSim[5]))  $visitasLiderancaNaoInformadoM = $visitasLiderancaSim[5];
//        else $visitasLiderancaNaoInformadoM = 0;
//
//
//        $visitas->select('visita.*');
//        $visitas = $visitas->get();
//
//        $dados = $this->extrairBySexo($visitas, 'data_nascimento');
//        $arrayIdadesM = array_fill(0, 9, 0);
//        $arrayIdadesF = array_fill(0, 9, 0);
//        $qntM = 0;
//        $qntF = 0;
//        foreach($dados as $dado){
//            if($dado[1] == 'M'){
//                $qntM++;
//            } else {
//                $qntF++;
//            }
//            if(is_null($dado[0])){
//                if($dado[1] == 'M'){
//                    $arrayIdadesM[0]++;
//                } else {
//                    $arrayIdadesF[0]++;
//                }
//            }elseif(Tdata::getIdade(Tdata::TdataBR($dado[0])) <= 17){
//                if($dado[1] == 'M'){
//                    $arrayIdadesM[1]++;
//                } else {
//                    $arrayIdadesF[1]++;
//                }
//            }elseif(Tdata::getIdade(Tdata::TdataBR($dado[0])) >= 18 && Tdata::getIdade(Tdata::TdataBR($dado[0])) <= 24){
//                if($dado[1] == 'M'){
//                    $arrayIdadesM[2]++;
//                } else {
//                    $arrayIdadesF[2]++;
//                }
//            }elseif(Tdata::getIdade(Tdata::TdataBR($dado[0])) >= 25 && Tdata::getIdade(Tdata::TdataBR($dado[0])) <= 29){
//                if($dado[1] == 'M'){
//                    $arrayIdadesM[3]++;
//                } else {
//                    $arrayIdadesF[3]++;
//                }
//            }elseif(Tdata::getIdade(Tdata::TdataBR($dado[0])) >= 30 && Tdata::getIdade(Tdata::TdataBR($dado[0])) <= 34){
//                if($dado[1] == 'M'){
//                    $arrayIdadesM[4]++;
//                } else {
//                    $arrayIdadesF[4]++;
//                }
//            }elseif(Tdata::getIdade(Tdata::TdataBR($dado[0])) >= 35 && Tdata::getIdade(Tdata::TdataBR($dado[0])) <= 45){
//                if($dado[1] == 'M'){
//                    $arrayIdadesM[5]++;
//                } else {
//                    $arrayIdadesF[5]++;
//                }
//            }elseif(Tdata::getIdade(Tdata::TdataBR($dado[0])) >= 46 && Tdata::getIdade(Tdata::TdataBR($dado[0])) <= 60){
//                if($dado[1] == 'M'){
//                    $arrayIdadesM[6]++;
//                } else {
//                    $arrayIdadesF[6]++;
//                }
//            }elseif(Tdata::getIdade(Tdata::TdataBR($dado[0])) >= 61 && Tdata::getIdade(Tdata::TdataBR($dado[0])) <= 70){
//                if($dado[1] == 'M'){
//                    $arrayIdadesM[7]++;
//                } else {
//                    $arrayIdadesF[7]++;
//                }
//            }elseif(Tdata::getIdade(Tdata::TdataBR($dado[0])) > 70){
//                if($dado[1] == 'M'){
//                    $arrayIdadesM[8]++;
//                } else {
//                    $arrayIdadesF[8]++;
//                }
//            }
//        }
//        return [$arrayIdadesM, $arrayIdadesF, $qntM, $qntF, $visitasLiderancaSimF, $visitasLiderancaSimM, $visitasLiderancaNaoF, $visitasLiderancaNaoM, $visitasLiderancaNaoInformadoF, $visitasLiderancaNaoInformadoM];
//    }
//
//    public function showVisita(Request $request){
//
//
//        $filtros = [];
//        $valoresFiltros = [];
//        $unidade = $request->get('unidade_prisional');
//        if(!is_null($unidade)){
//            $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
//            array_push($filtros, 'unidade_prisional');
//            array_push($valoresFiltros, $unidade);
//        }
//        else $unidadeSigla = null;
//        $pavilhao = $request->get('pavilhao');
//        if(!is_null($pavilhao)){
//            $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
//            array_push($filtros, 'pavilhao');
//            array_push($valoresFiltros, $pavilhao);
//        }
//        else $pavilhaoNome = null;
//        $ala = $request->get('ala');
//        if(!is_null($ala)){
//            $alaNome = UnidadesController::getNomeAla($request->get('ala'));
//            array_push($filtros, 'ala');
//            array_push($valoresFiltros, $ala);
//        }
//        else $alaNome = null;
//        $cela = $request->get('cela');
//        if(!is_null($cela)){
//            $celaNome = UnidadesController::getNomeCela($request->get('cela'));
//            array_push($filtros, 'cela');
//            array_push($valoresFiltros, $cela);
//        }
//        else $celaNome = null;
//
//        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
//        $dataHora = Tdata::TtimeStampHojeBrTxt();
//
//        $resultado = $this->idadeVisita($filtros, $valoresFiltros);
//
//        $idadesM = $resultado[0];
//        $idadesF = $resultado[1];
//        $qntM = $resultado[2];
//        $qntF = $resultado[3];
//
//        if($resultado[4] != null) {
//            $visitasLiderancaSimF = $resultado[4]->qntLideranca;
//            try{
//                $visitasLiderancaSimM = $resultado[5]->qntLideranca;
//            } catch (\Exception $e){
//                $visitasLiderancaSimM = 0;
//            }
//        } else {
//            $visitasLiderancaSimF = $visitasLiderancaSimM = 0;
//        }
//
//        if($resultado[6] != null){
//            $visitasLiderancaNaoF = $resultado[6]->qntLideranca;
//            try{
//                $visitasLiderancaNaoM = $resultado[7]->qntLideranca;
//            } catch(\Exception $e){
//                $visitasLiderancaNaoM = 0;
//            }
//        } else {
//            $visitasLiderancaNaoF = $visitasLiderancaNaoM = 0;
//        }
//
//        if($resultado[8] != null) {
//                $visitasLiderancaNaoInformadoF = $resultado[8]->qntLideranca;
//            try{
//                $visitasLiderancaNaoInformadoM = $resultado[9]->qntLideranca;
//            } catch (\Exception $e) {
//                $visitasLiderancaNaoInformadoM = 0;
//            }
//        } else {
//            $visitasLiderancaNaoInformadoF = $visitasLiderancaNaoInformadoM = 0;
//        }
//
//        return view('estatisticas.show.estatisticasVisita', compact('unidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome', 'idadesF', 'idadesM', 'dataHora', 'qntM', 'qntF',
//            'visitasLiderancaNaoInformadoF', 'visitasLiderancaNaoInformadoM', 'visitasLiderancaSimF', 'visitasLiderancaSimM', 'visitasLiderancaNaoF', 'visitasLiderancaNaoM'));
//    }
//
//    public function showAdvogado(Request $request){
//        $unidade = $request->get('unidade_prisional');
//
//        $data_atendimento = $request->get('data_atendimento');
//        $atendimento_advogado = DB::table('advogado_atendimento');
//        $atendimento_advogado->leftJoin('unidade', 'unidade.id', 'advogado_atendimento.unidade');
//        if($data_atendimento == null){
//            $atendimento_advogado->where('advogado_atendimento.data_atendimento', '>=', Tdata::TdataHojeMysql());
//        } else $atendimento_advogado->where('advogado_atendimento.data_atendimento', '>=', Tdata::TdataMysql($data_atendimento));
//
//        if(!is_null($unidade)) {
//            $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
//            $atendimento_advogado->leftJoin('interno', 'interno.id', 'advogado_atendimento.fk_interno');
//            $atendimento_advogado->leftJoin('pavilhao', 'interno.pavilhao', 'pavilhao.id');
//            $atendimento_advogado->leftJoin('ala', 'interno.ala', 'ala.id');
//            $atendimento_advogado->leftJoin('cela', 'interno.cela', 'cela.id');
//            $atendimento_advogado->where('interno.status', 'A');
//            $atendimento_advogado->where('interno.unidade_prisional', $unidade);
//            $atendimento_advogado->where('unidade.id', $unidade);
//        }
//        else $unidadeSigla = null;
//        $pavilhao = $request->get('pavilhao');
//        if(!is_null($pavilhao)){
//            $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
//            $atendimento_advogado->where('interno.pavilhao',$pavilhao);
//        }
//        else $pavilhaoNome = null;
//        $ala = $request->get('ala');
//        if(!is_null($ala)){
//            $alaNome = UnidadesController::getNomeAla($request->get('ala'));
//            $atendimento_advogado->where('interno.ala', $ala);
//        }
//        else $alaNome = null;
//        $cela = $request->get('cela');
//        if(!is_null($cela)){
//            $celaNome = UnidadesController::getNomeCela($request->get('cela'));
//            $atendimento_advogado->where('interno.cela', $cela);
//        }
//        else $celaNome = null;
//
//        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
//        $dataHora = Tdata::TtimeStampHojeBrTxt();
//
//
//
//        if(!is_null($ala)) {
//            $atendimento_advogado->groupBy('cela.nome','cela.id');
//            $atendimento_advogado->orderBy('cela.nome');
//            $atendimento_advogado = $atendimento_advogado->select(DB::raw('(cela.nome) as sigla'), 'cela.id', DB::raw('count(advogado_atendimento.id) as qnt'))->get();
//            $nome_campo = "Cela";
//        } else
//        if(!is_null($pavilhao)){
//            $atendimento_advogado->groupBy('ala.nome','ala.id');
//            $atendimento_advogado->orderBy('ala.nome');
//            $atendimento_advogado = $atendimento_advogado->select(DB::raw('(ala.nome) as sigla'), 'ala.id',DB::raw('count(advogado_atendimento.id) as qnt'))->get();
//            $nome_campo = "Ala";
//        } else
//        if(!is_null($unidade)) {
//            $atendimento_advogado->groupBy('pavilhao.nome','pavilhao.id');
//            $atendimento_advogado->orderBy('pavilhao.nome');
//            $atendimento_advogado = $atendimento_advogado->select(DB::raw('(pavilhao.nome) as sigla'), 'pavilhao.id', DB::raw('count(advogado_atendimento.id) as qnt'))->get();
//            $nome_campo = "Pavilhão";
//        } else{
//            $atendimento_advogado->groupBy('unidade.sigla','unidade.id');
//            $atendimento_advogado->orderBy('unidade.sigla');
//            $atendimento_advogado = $atendimento_advogado->select('unidade.sigla', 'unidade.id',DB::raw('count(advogado_atendimento.id) as qnt'))->get();
//            $nome_campo = "Unidade";
//        }
//
//        $soma = 0; //SERÁ UTILIZADA NA VIEW PARA O CALCULO SER DINÂMICO, APROVEITANDO O PROCESSAMENTO DO FOREACH
//
//
//        return view('estatisticas.show.estatisticasAdvogado', compact('unidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome','dataHora', 'atendimento_advogado', 'nome_campo', 'soma', 'data_atendimento'));
//    }
//
//    public function showServidor(Request $request){
//        $unidade = $request->get('unidade_prisional');
//        if(!is_null($unidade)){
//            $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
//        }
//        else $unidadeSigla = null;
//
//        $servidores = [];
//        $servidoresBySexo = [];
//        $internos = [];
//
//        if(is_null($unidade)) {
//            $unidades = DB::table('unidade')
//                ->where('status', 1)
//                ->get();
//            foreach ($unidades as $unidade) {
//                array_push($servidores, [$unidade->sigla, FacadeController::getTotalServidoresLotacao($unidade->sigla)]);
//                array_push($servidoresBySexo, [FacadeController::getTotalServidoresLotacaoBySexo($unidade->sigla, 'Masculino'),FacadeController::getTotalServidoresLotacaoBySexo($unidade->sigla, 'Feminino')]);
//                array_push($internos, self::getTotalInternosBysigla($unidade->sigla));
//            }
//        } else {
//            array_push($servidores, [$unidadeSigla, FacadeController::getTotalServidoresLotacao($unidadeSigla)]);
//            array_push($servidoresBySexo, [FacadeController::getTotalServidoresLotacaoBySexo($unidadeSigla, 'Masculino'),FacadeController::getTotalServidoresLotacaoBySexo($unidadeSigla, 'Feminino')]);
//            array_push($internos, self::getTotalInternosBysigla($unidadeSigla));
//        }
//
//
//        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
//        $dataHora = Tdata::TtimeStampHojeBrTxt();
//
//        return view('estatisticas.show.estatisticasServidor', compact('unidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome','dataHora','servidores', 'servidoresBySexo','internos'));
//    }
//
//    public function getReigressoUnidade($data){
//
//        $qntReigressoAlvara = DB::table('movimentacao')
//            ->leftJoin('movimentacao_interno', 'movimentacao.id', 'movimentacao_interno.fk_movimentacao')
//            ->leftJoin('interno', 'interno.id', 'movimentacao_interno.fk_interno')
//            ->where('movimentacao.tipo', 'alvara')
//            ->where('movimentacao_interno.status', 'Finalizada');
//        $qntReigressoEntrada = DB::table('movimentacao')
//            ->leftJoin('movimentacao_interno', 'movimentacao.id', 'movimentacao_interno.fk_movimentacao')
//            ->leftJoin('interno', 'interno.id', 'movimentacao_interno.fk_interno')
//            ->where('movimentacao.tipo', 'entradaunidadeprisional')
//            ->where('movimentacao_interno.status', 'Finalizada');
//
//        if($data != null){
//            $qntReigressoAlvara->where('movimentacao.data','>=',$data);
//        }
//
//        $qntReigressoAlvara = $qntReigressoAlvara->groupBy('interno.id')
//            ->orderBy('interno.id')
//            ->select('interno.id', DB::raw('MAX(movimentacao.data) as data'))
//            ->get();
//
//
//
//        $qntReigressoEntrada =$qntReigressoEntrada->groupBy('interno.id')
//            ->orderBy('interno.id')
//            ->select('interno.id', DB::raw('MAX(movimentacao.data) as data'))
//            ->get();
//
//        $qntReigresso = 0;
//
//        foreach($qntReigressoAlvara as $qntAlvara){
//            foreach($qntReigressoEntrada as $qntEntrada){
//                if($qntEntrada->id > $qntAlvara->id){
//                    break;
//                }
//                if($qntEntrada->id == $qntAlvara->id){
//                    if($qntEntrada->data > $qntAlvara->data){
//                        $qntReigresso = $qntReigresso + 1;
//                        break;
//                    }
//                }
//            }
//        }
//
//        return $qntReigresso;
//
//    }
//
//    public function getQntVisitaAtiva(Request $request){
//
//        $unidade = $request->get('unidade');
//        $pavilhao = $request->get('pavilha');
//        $ala = $request->get('ala');
//        $cela = $request->get('cela');
//
//        $visitas = DB::table('visita_interno')
//            ->leftJoin('visita', 'visita.id', 'visita_interno.fk_visita')
//            ->leftJoin('interno', 'interno.id', 'visita_interno.fk_interno')
//            ->leftJoin('unidade', 'unidade.id', 'interno.unidade_prisional')
//            ->where('interno.status', 'A')
//            ->where('visita.status', 'A')
//            ->groupBy('unidade.sigla')
//            ->groupBy('interno.sexo')
//            ->orderBy('unidade.sigla');
//
//        if($unidade != null && $unidade > 0){
//            $visitas = $visitas
//                ->where('interno.unidade_prisional', $unidade);
//            if($pavilhao != null && $pavilhao > 0){
//                $visitas = $visitas->where('interno.pavilhao', $pavilhao);
//                if($ala != null && $ala > 0){
//                    $visitas = $visitas->where('interno.ala', $ala);
//                    if($cela != null && $cela > 0){
//                        $visitas = $visitas->where('interno.cela', $cela);
//                    }
//                }
//            }
//        }
//
//
//        $visitas = $visitas->select('unidade.sigla as unidade', 'interno.sexo as sexo', DB::raw('COUNT(DISTINCT (interno.id)) AS qnt'));
//
//        return $visitas->get();
//
//
//    }
//
//    public function getUnidadesSenteRegimeMediSegu(Request $request, $unidade, $pavilhao, $ala, $cela){
//
//
//        $query = DB::table('interno')
//            ->leftJoin('unidade', 'unidade.id', 'interno.unidade_prisional')
//            ->where('interno.status', 'A')
//            ->where('unidade.status', '1')
//            ->groupBy('unidade.sigla');
//
//        if($unidade != null && $unidade > 0){
//            $query = $query
//                ->where('interno.unidade_prisional', $unidade);
//            if($pavilhao != null && $pavilhao > 0){
//                $query = $query->where('interno.pavilhao', $pavilhao);
//                if($ala != null && $ala > 0){
//                    $query = $query->where('interno.ala', $ala);
//                    if($cela != null && $cela > 0){
//                        $query = $query->where('interno.cela', $cela);
//                    }
//                }
//            }
//        }
//
//
//        $query = $query->select('unidade.sigla AS sigla',
//            DB::raw('COUNT(CASE WHEN interno.sentenciado = \'S\' AND interno.regime=\'ABERTO\' AND interno.medida_seguranca=\'N\'  THEN 1 END) AS aberto'),
//            DB::raw('COUNT(CASE WHEN interno.sentenciado = \'S\' AND interno.regime=\'SEMIABERTO\' AND interno.medida_seguranca=\'N\' THEN 1 END) AS semiaberto'),
//            DB::raw('COUNT(CASE WHEN interno.sentenciado = \'S\' AND interno.regime=\'FECHADO\' AND interno.medida_seguranca=\'N\' THEN 1 END) AS fechado'),
//            DB::raw('COUNT(CASE WHEN interno.sentenciado=\'N\'  AND interno.regime IS NULL AND interno.medida_seguranca = \'N\'  THEN 1 END) AS provisorio'),
//            DB::raw('COUNT(CASE WHEN interno.sentenciado=\'S\' AND interno.regime IS NOT NULL  AND interno.medida_seguranca = \'N\' THEN 1 END) AS sentenciado'),
//            DB::raw('COUNT(CASE WHEN interno.medida_seguranca=\'S\' AND interno.regime IS NULL AND interno.sentenciado = \'S\' THEN 1 END) AS medida_seg'),
//            DB::raw('COUNT(CASE WHEN interno.sentenciado=\'N\'  AND interno.regime IS NULL  AND interno.medida_seguranca = \'S\'  THEN 1 END) AS medida_seg_provisorio'),
//            DB::raw('COUNT(CASE WHEN (interno.sentenciado=\'S\'  AND interno.regime IS NOT NULL  AND interno.medida_seguranca = \'S\')
//	            OR (interno.sentenciado=\'N\'  AND interno.regime IS NOT NULL )
//	            OR (interno.sentenciado=\'S\'  AND interno.regime IS NULL AND interno.medida_seguranca = \'N\')
//	            OR (interno.medida_seguranca=\'S\'  AND interno.regime IS NOT NULL )
//	            OR (interno.medida_seguranca IS NULL  OR interno.sentenciado IS NULL ) THEN 1 END) AS inconsistente'),
//            DB::raw('COUNT(CASE WHEN interno.nome IS NOT NULL     THEN 1 END) AS total'));
//
//        return $query->get();
//
//
//    }
//
//    public static function arredondarEscala($valor, $escala){
//        $novoValor = strval(round($valor/$escala));
//        $contemZeros = true;
//        if(strlen ($novoValor) == 1){
//            return 10;
//        }
//        for($i = 1; $i < strlen ($novoValor); $i++){
//            if($novoValor[$i] != 0){
//                $contemZeros = false;
//                break;
//            }
//        }
//        if(!$contemZeros){
//            return (((int) $novoValor[0]) + 1) * ((int) '1'.str_repeat('0',strlen($novoValor)-1));
//        }
//        return $novoValor;
//    }
//
//
//    public function internoPdf(Request $request){
//        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
//        return view('estatisticas.impressao.interno', compact('unidades'));
//    }
//
//    public function internoPdfGerar(Request $request){
//
//        ini_set('max_execution_time', 3000);
//        ini_set('memory_limit','256M');
//
//        $unidade = $request->get('unidade_prisional');
//        if(!is_null($unidade)) $unidadeSigla = UnidadesController::getSiglaUp($request->get('unidade_prisional'));
//        else $unidadeSigla = null;
//        $pavilhao = $request->get('pavilhao');
//        if(!is_null($pavilhao)) $pavilhaoNome = UnidadesController::getNomePav($request->get('pavilhao'));
//        else $pavilhaoNome = null;
//        $ala = $request->get('ala');
//        if(!is_null($ala)) $alaNome = UnidadesController::getNomeAla($request->get('ala'));
//        else $alaNome = null;
//        $cela = $request->get('cela');
//        if(!is_null($cela)) $celaNome = UnidadesController::getNomeCela($request->get('cela'));
//        else $celaNome = null;
//
//
//        $internosunidade = $request->get('internosunidade');
//        $regimeunidade = $request->get('regimeunidade');
//        $sexo = $request->get('sexo');
//        $regimeprisional = $request->get('regimeprisional');
//        $sentprov_sem_ms = $request->get('sentprov_sem_ms');
//        $sentprov_em_ms = $request->get('sentprov_em_ms');
//        $sentprov_geral = $request->get('sentprov_geral');
//        $medidaseg = $request->get('medidaseg');
//        $faixaetaria = $request->get('faixaetaria');
//        $nacionalidade_filtro = $request->get('nacionalidade');
//        $estadocivil = $request->get('estadocivil');
//        $escolaridade = $request->get('escolaridade');
//        $corpele = $request->get('corpele');
//        $orientacaosexual = $request->get('orientacaosexual');
//        $saidaunidade = $request->get('saidaunidade');
//        $religiao = $request->get('religiao');
//        $setic = $request->get('setic');
//
//        /*
//         * Processamento de FILTROS ****************************************
//        */
//
//        if($internosunidade == '') $internosunidade = false; else $internosunidade = true;
//        if($regimeunidade == '')$regimeunidade = false; else $regimeunidade = true;
//        if($sexo == '') $sexo = false; else $sexo = true;
//        if($regimeprisional == '') $regimeprisional = false; else $regimeprisional = true;
//        if($sentprov_sem_ms == '') $sentprov_sem_ms = false; else $sentprov_sem_ms = true;
//        if($sentprov_em_ms == '') $sentprov_em_ms = false; else $sentprov_em_ms = true;
//        if($sentprov_geral == '') $sentprov_geral= false; else $sentprov_geral = true;
//        if($medidaseg == '') $medidaseg = false; else $medidaseg = true;
//        if($faixaetaria == '') $faixaetaria = false; else $faixaetaria = true;
//        if($nacionalidade_filtro == '') $nacionalidade_filtro = false; else $nacionalidade_filtro = true;
//        if($estadocivil == '') $estadocivil = false; else $estadocivil = true;
//        if($escolaridade == '') $escolaridade = false; else $escolaridade = true;
//        if($corpele == '') $corpele = false; else $corpele = true;
//        if($orientacaosexual == '') $orientacaosexual = false; else $orientacaosexual = true;
//        if($saidaunidade == '') $saidaunidade = false; else $saidaunidade = true;
//        if($religiao == '') $religiao = false; else $religiao = true;
//        if($setic == '') $setic = false; else $setic = true;
//
//
//        /*
//         * FIM do Processamento de FILTROS ****************************************
//        */
//
//        if($regimeunidade) {
//            $detalhesUnidades = EstatisticasController::getUnidadesSenteRegimeMediSegu($request, $unidade, $pavilhao, $ala, $cela);
//        } else {
//            $detalhesUnidades = [];
//        }
//
//
//        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->where('status','1')->get();
//
//        $qtdInternosLotSelecionada = DB::table('interno')->where('status','A');
//
//        $qtdUnidadeHA = DB::table('interno')
//            ->leftJoin('unidade','unidade.id','interno.unidade_prisional')
//            ->where('sexo', 'M')->where('interno.status','A')
//            ->select(DB::raw('unidade.sigla, count(*) as qtd'))
//            ->groupBy('unidade.sigla')->orderBy('qtd','DESC');
//
//        $qtdUnidadeMA = DB::table('interno')
//            ->leftJoin('unidade','unidade.id','interno.unidade_prisional')
//            ->where('sexo', 'F')->where('interno.status','A')
//            ->select(DB::raw('unidade.sigla, count(*) as qtd'))
//            ->groupBy('unidade.sigla')->orderBy('qtd','DESC');
//
//        $qtdUnidadeTotal  = DB::table('interno')
//            ->leftJoin('unidade','unidade.id','interno.unidade_prisional')
//            ->select(DB::raw('unidade.sigla, count(*) as qtd'))
//            ->where('interno.status','A')->groupBy('unidade.sigla')->orderBy('qtd','DESC');
//
//        $qtdRegimeNIH = DB::table('interno')->where('regime', null)->where('sentenciado','S')->where('sexo','M')->where('medida_seguranca','=','N')->where('status','A');
//        $qtdRegimeNIM = DB::table('interno')->where('regime', null)->where('sentenciado','S')->where('sexo','F')->where('medida_seguranca','=','N')->where('status','A');
//        $qtdRegimeFechadoH = DB::table('interno')->where('sexo','M')->where('regime', 'FECHADO')->where('status','A');
//        $qtdRegimeFechadoM = DB::table('interno')->where('sexo','F')->where('regime', 'FECHADO')->where('status','A');
//        $qtdRegimeSemiabertoH = DB::table('interno')->where('sexo','M')->where('regime', 'SEMIABERTO')->where('status','A');
//        $qtdRegimeSemiabertoM = DB::table('interno')->where('sexo','F')->where('regime', 'SEMIABERTO')->where('status','A');
//        $qtdRegimeAbertoH = DB::table('interno')->where('sexo','M')->where('regime', 'ABERTO')->where('status','A');
//        $qtdRegimeAbertoM = DB::table('interno')->where('sexo','F')->where('regime', 'ABERTO')->where('status','A');
//
//        $qtdSentenciadosNIH = DB::table('interno')->where('sexo','M')->where('sentenciado', null)->where('medida_seguranca','N')->where('status','A');
//        $qtdSentenciadosNIM = DB::table('interno')->where('sexo','F')->where('sentenciado', null)->where('medida_seguranca','N')->where('status','A');
//        $qtdSentenciadosH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'S')->where('medida_seguranca','N')->where('status','A');
//        $qtdSentenciadosM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'S')->where('medida_seguranca','N')->where('status','A');
//        $qtdProvisoriosH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'N')->where('medida_seguranca','N')->where('status','A');
//        $qtdProvisoriosM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'N')->where('medida_seguranca','N')->where('status','A');
//
//        $qtdSentenciadosMSNIH = DB::table('interno')->where('sexo','M')->where('sentenciado', null)->where('medida_seguranca','S')->where('status','A');
//        $qtdSentenciadosMSNIM = DB::table('interno')->where('sexo','F')->where('sentenciado', null)->where('medida_seguranca','S')->where('status','A');
//        $qtdSentenciadosMSH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'S')->where('medida_seguranca','S')->where('status','A');
//        $qtdSentenciadosMSM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'S')->where('medida_seguranca','S')->where('status','A');
//        $qtdProvisoriosMSH = DB::table('interno')->where('sexo','M')->where('sentenciado', 'N')->where('medida_seguranca','S')->where('status','A');
//        $qtdProvisoriosMSM = DB::table('interno')->where('sexo','F')->where('sentenciado', 'N')->where('medida_seguranca','S')->where('status','A');
//
//        $qtdMedidaDeSegurancaH = DB::table('interno')->where('sexo','M')->where('medida_seguranca', 'S')->where('status','A');
//        $qtdMedidaDeSegurancaM = DB::table('interno')->where('sexo','F')->where('medida_seguranca', 'S')->where('status','A');
//
//        $qtdHA = DB::table('interno')->where('sexo', 'M')->where('status','A');
//        $qtdMA = DB::table('interno')->where('sexo', 'F')->where('status','A');
//
//        $faixaEtaria_naoInformadaH = 0;
//        $faixaEtaria_zero_a_dezesseteH = 0;
//        $faixaEtaria_dezoito_a_vinteequatroH = 0;
//        $faixaEtaria_vinteecinco_a_vinteenoveH = 0;
//        $faixaEtaria_trinta_a_trintaequatroH = 0;
//        $faixaEtaria_trintaecinco_a_quarentaecincoH = 0;
//        $faixaEtaria_quarentaeseis_a_sessentaH = 0;
//        $faixaEtaria_sessentaeum_a_setentaH = 0;
//        $faixaEtaria_maisDeSetentaH = 0;
//
//        $faixaEtaria_naoInformadaM = 0;
//        $faixaEtaria_zero_a_dezesseteM = 0;
//        $faixaEtaria_dezoito_a_vinteequatroM = 0;
//        $faixaEtaria_vinteecinco_a_vinteenoveM = 0;
//        $faixaEtaria_trinta_a_trintaequatroM = 0;
//        $faixaEtaria_trintaecinco_a_quarentaecincoM = 0;
//        $faixaEtaria_quarentaeseis_a_sessentaM = 0;
//        $faixaEtaria_sessentaeum_a_setentaM = 0;
//        $faixaEtaria_maisDeSetentaM = 0;
//
//        $internosH = DB::table('interno')->select('data_nascimento')->where('sexo','M')->where('status','A');
//        $internosM = DB::table('interno')->select('data_nascimento')->where('sexo','F')->where('status','A');
//
//        $qtdFaccaoNIH = DB::table('interno')->where('status','A')->where('sexo','M')->where('faccao', null);
//        $qtdFaccaoNIM = DB::table('interno')->where('status','A')->where('sexo','F')->where('faccao', null);
//
//        //periculosidade
//        $periculosidadeAltaH = DB::table('interno')->where('periculosidade','Alta')->where('sexo','M')->where('status','A');
//        $periculosidadeMediaH = DB::table('interno')->where('periculosidade','Média')->where('sexo','M')->where('status','A');
//        $periculosidadeBaixaH = DB::table('interno')->where('periculosidade','Baixa')->where('sexo','M')->where('status','A');
//        $periculosidadeNIH = DB::table('interno')->where('periculosidade',null)->where('sexo','M')->where('status','A');
//        $periculosidadeAltaM = DB::table('interno')->where('periculosidade','Alta')->where('sexo','F')->where('status','A');
//        $periculosidadeMediaM = DB::table('interno')->where('periculosidade','Média')->where('sexo','F')->where('status','A');
//        $periculosidadeBaixaM = DB::table('interno')->where('periculosidade','Baixa')->where('sexo','F')->where('status','A');
//        $periculosidadeNIM = DB::table('interno')->where('periculosidade',null)->where('sexo','F')->where('status','A');
//
//        //liderança
//        $qtdLiderancaSimH = DB::table('interno')->where('lideranca','S')->where('sexo','M')->where('status','A');
//        $qtdLiderancaNaoH = DB::table('interno')->where('lideranca','N')->where('sexo','M')->where('status','A');
//        $qtdLiderancaNIH = DB::table('interno')->where('lideranca',null)->where('sexo','M')->where('status','A');
//        $qtdLiderancaSimM = DB::table('interno')->where('lideranca','S')->where('sexo','F')->where('status','A');
//        $qtdLiderancaNaoM = DB::table('interno')->where('lideranca','N')->where('sexo','F')->where('status','A');
//        $qtdLiderancaNIM = DB::table('interno')->where('lideranca',null)->where('sexo','F')->where('status','A');
//
//        // estado_civil
//        if($estadocivil){
//            $qtdSolteirosH = DB::table('interno')->where('estado_civil','solteiro')->where('sexo','M')->where('status','A');
//            $qtdSolteirosM = DB::table('interno')->where('estado_civil','solteiro')->where('sexo','F')->where('status','A');
//            $qtdCasadosH = DB::table('interno')->where('estado_civil','casado')->where('sexo','M')->where('status','A');
//            $qtdCasadosM = DB::table('interno')->where('estado_civil','casado')->where('sexo','F')->where('status','A');
//            $qtdUniaoEstavelH = DB::table('interno')->where('estado_civil','uniao estavel')->where('sexo','M')->where('status','A');
//            $qtdUniaoEstavelM = DB::table('interno')->where('estado_civil','uniao estavel')->where('sexo','F')->where('status','A');
//            $qtdSeparadosH = DB::table('interno')->where('estado_civil','separado')->where('sexo','M')->where('status','A');
//            $qtdSeparadosM = DB::table('interno')->where('estado_civil','separado')->where('sexo','F')->where('status','A');
//            $qtdDivorciadosH = DB::table('interno')->where('estado_civil','divorciado')->where('sexo','M')->where('status','A');
//            $qtdDivorciadosM = DB::table('interno')->where('estado_civil','divorciado')->where('sexo','F')->where('status','A');
//            $qtdViuvosH = DB::table('interno')->where('estado_civil','viuvo')->where('sexo','M')->where('status','A');
//            $qtdViuvosM = DB::table('interno')->where('estado_civil','viuvo')->where('sexo','F')->where('status','A');
//            $qtdEstadoCivilNIH = DB::table('interno')->where('estado_civil',null)->where('sexo','M')->where('status','A');
//            $qtdEstadoCivilNIM = DB::table('interno')->where('estado_civil',null)->where('sexo','F')->where('status','A');
//        }
//
//        // opcao_sexual
//        $qtdHeteroH = DB::table('interno')->where('opcao_sexual','HETEROSSEXUAL')->where('sexo','M')->where('status','A');
//        $qtdHeteroM = DB::table('interno')->where('opcao_sexual','HETEROSSEXUAL')->where('sexo','F')->where('status','A');
//        $qtdHomoH = DB::table('interno')->where('opcao_sexual','HOMOSSEXUAL')->where('sexo','M')->where('status','A');
//        $qtdHomoM = DB::table('interno')->where('opcao_sexual','HOMOSSEXUAL')->where('sexo','F')->where('status','A');
//        $qtdBiH = DB::table('interno')->where('opcao_sexual','BISSEXUAL')->where('sexo','M')->where('status','A');
//        $qtdBiM = DB::table('interno')->where('opcao_sexual','BISSEXUAL')->where('sexo','F')->where('status','A');
//        $qtdSexualidadeNaoDeclH = DB::table('interno')->where('opcao_sexual','nao declarado')->where('sexo','M')->where('status','A');
//        $qtdSexualidadeNaoDeclM = DB::table('interno')->where('opcao_sexual','nao declarado')->where('sexo','F')->where('status','A');
//        $qtdSexualidadeNIH = DB::table('interno')->where('opcao_sexual',null)->where('sexo','M')->where('status','A');
//        $qtdSexualidadeNIM = DB::table('interno')->where('opcao_sexual',null)->where('sexo','F')->where('status','A');
//
//        // etnia
//        $qtdEtniaBrancaH = DB::table('interno')->where('etnia','Branca')->where('sexo','M')->where('status','A');
//        $qtdEtniaBrancaM = DB::table('interno')->where('etnia','Branca')->where('sexo','F')->where('status','A');
//        $qtdEtniaIndígenaH = DB::table('interno')->where('etnia','Indígena')->where('sexo','M')->where('status','A');
//        $qtdEtniaIndígenaM = DB::table('interno')->where('etnia','Indígena')->where('sexo','F')->where('status','A');
//        $qtdEtniaPretaH = DB::table('interno')->where('etnia','Preta')->where('sexo','M')->where('status','A');
//        $qtdEtniaPretaM = DB::table('interno')->where('etnia','Preta')->where('sexo','F')->where('status','A');
//        $qtdEtniaPardaH = DB::table('interno')->where('etnia','Parda')->where('sexo','M')->where('status','A');
//        $qtdEtniaPardaM = DB::table('interno')->where('etnia','Parda')->where('sexo','F')->where('status','A');
//        $qtdEtniaAmarelaH = DB::table('interno')->where('etnia','Amarela')->where('sexo','M')->where('status','A');
//        $qtdEtniaAmarelaM = DB::table('interno')->where('etnia','Amarela')->where('sexo','F')->where('status','A');
//        $qtdEtniaNIH = DB::table('interno')->where('etnia',null)->where('sexo','M')->where('status','A');
//        $qtdEtniaNIM = DB::table('interno')->where('etnia',null)->where('sexo','F')->where('status','A');
//
//        // escolaridade
//        if($escolaridade) {
//            $qtdAnalfabetoH = DB::table('interno')->where('escolaridade', 'Analfabeto')->where('sexo', 'M')->where('status', 'A');
//            $qtdAnalfabetoM = DB::table('interno')->where('escolaridade', 'Analfabeto')->where('sexo', 'F')->where('status', 'A');
//            $qtdAlfabetizadoH = DB::table('interno')->where('escolaridade', 'Alfabetizado')->where('sexo', 'M')->where('status', 'A');
//            $qtdAlfabetizadoM = DB::table('interno')->where('escolaridade', 'Alfabetizado')->where('sexo', 'F')->where('status', 'A');
//            $qtdFundIncH = DB::table('interno')->where('escolaridade', 'Ensino Fundamental Incompleto')->where('sexo', 'M')->where('status', 'A');
//            $qtdFundIncM = DB::table('interno')->where('escolaridade', 'Ensino Fundamental Incompleto')->where('sexo', 'F')->where('status', 'A');
//            $qtdFundCompH = DB::table('interno')->where('escolaridade', 'Ensino Fundamental Completo')->where('sexo', 'M')->where('status', 'A');
//            $qtdFundCompM = DB::table('interno')->where('escolaridade', 'Ensino Fundamental Completo')->where('sexo', 'F')->where('status', 'A');
//            $qtdMedioIncompH = DB::table('interno')->where('escolaridade', 'Ensino Médio Incompleto')->where('sexo', 'M')->where('status', 'A');
//            $qtdMedioIncompM = DB::table('interno')->where('escolaridade', 'Ensino Médio Incompleto')->where('sexo', 'F')->where('status', 'A');
//            $qtdMedioCompH = DB::table('interno')->where('escolaridade', 'Ensino Médio Completo')->where('sexo', 'M')->where('status', 'A');
//            $qtdMedioCompM = DB::table('interno')->where('escolaridade', 'Ensino Médio Completo')->where('sexo', 'F')->where('status', 'A');
//            $qtdSuperiorIncompH = DB::table('interno')->where('escolaridade', 'Ensino Superior Incompleto')->where('sexo', 'M')->where('status', 'A');
//            $qtdSuperiorIncompM = DB::table('interno')->where('escolaridade', 'Ensino Superior Incompleto')->where('sexo', 'F')->where('status', 'A');
//            $qtdSuperiorCompH = DB::table('interno')->where('escolaridade', 'Ensino Superior Completo')->where('sexo', 'M')->where('status', 'A');
//            $qtdSuperiorCompM = DB::table('interno')->where('escolaridade', 'Ensino Superior Completo')->where('sexo', 'F')->where('status', 'A');
//            $qtdPosLatoSensuH = DB::table('interno')->where('escolaridade', 'Pós-graduação lato sensu')->where('sexo', 'M')->where('status', 'A');
//            $qtdPosLatoSensuM = DB::table('interno')->where('escolaridade', 'Pós-graduação lato sensu')->where('sexo', 'F')->where('status', 'A');
//            $qtdMestradoH = DB::table('interno')->where('escolaridade', 'Mestrado')->where('sexo', 'M')->where('status', 'A');
//            $qtdMestradoM = DB::table('interno')->where('escolaridade', 'Mestrado')->where('sexo', 'F')->where('status', 'A');
//            $qtdDoutoradoH = DB::table('interno')->where('escolaridade', 'Doutorado')->where('sexo', 'M')->where('status', 'A');
//            $qtdDoutoradoM = DB::table('interno')->where('escolaridade', 'Doutorado')->where('sexo', 'F')->where('status', 'A');
//            $qtdPosDoutoradoH = DB::table('interno')->where('escolaridade', 'Pós-Doutorado')->where('sexo', 'M')->where('status', 'A');
//            $qtdPosDoutoradoM = DB::table('interno')->where('escolaridade', 'Pós-Doutorado')->where('sexo', 'F')->where('status', 'A');
//            $qtdEscolaridadeNIH = DB::table('interno')->where('escolaridade', null)->where('sexo', 'M')->where('status', 'A');
//            $qtdEscolaridadeNIM = DB::table('interno')->where('escolaridade', null)->where('sexo', 'F')->where('status', 'A');
//        }
//
//        $qtdNacionalidadeNIH = DB::table('interno')->where('naturalidade_pais',null)->where('sexo','M')->where('status','A');
//        $qtdNacionalidadeNIM = DB::table('interno')->where('naturalidade_pais',null)->where('sexo','F')->where('status','A');
//
//        //religião
//        if($religiao) {
//            $qtdAdventistaH = DB::table('interno')->where('religiao', 'Adventista')->where('sexo', 'M')->where('status', 'A');
//            $qtdAdventistaM = DB::table('interno')->where('religiao', 'Adventista')->where('sexo', 'F')->where('status', 'A');
//            $qtdBudistaH = DB::table('interno')->where('religiao', 'Budista')->where('sexo', 'M')->where('status', 'A');
//            $qtdBudistaM = DB::table('interno')->where('religiao', 'Budista')->where('sexo', 'F')->where('status', 'A');
//            $qtdCatolicaH = DB::table('interno')->where('religiao', 'Católica')->where('sexo', 'M')->where('status', 'A');
//            $qtdCatolicaM = DB::table('interno')->where('religiao', 'Católica')->where('sexo', 'F')->where('status', 'A');
//            $qtdEspiritaH = DB::table('interno')->where('religiao', 'Espírita')->where('sexo', 'M')->where('status', 'A');
//            $qtdEspiritaM = DB::table('interno')->where('religiao', 'Espírita')->where('sexo', 'F')->where('status', 'A');
//            $qtdEvangelicaH = DB::table('interno')->where('religiao', 'Evangélica')->where('sexo', 'M')->where('status', 'A');
//            $qtdEvangelicaM = DB::table('interno')->where('religiao', 'Evangélica')->where('sexo', 'F')->where('status', 'A');
//            $qtdJudeuH = DB::table('interno')->where('religiao', 'Judeu')->where('sexo', 'M')->where('status', 'A');
//            $qtdJudeuM = DB::table('interno')->where('religiao', 'Judeu')->where('sexo', 'F')->where('status', 'A');
//            $qtdMormonH = DB::table('interno')->where('religiao', 'Mórmon')->where('sexo', 'M')->where('status', 'A');
//            $qtdMormonM = DB::table('interno')->where('religiao', 'Mórmon')->where('sexo', 'F')->where('status', 'A');
//            $qtdMulcumanaH = DB::table('interno')->where('religiao', 'Muçulmana')->where('sexo', 'M')->where('status', 'A');
//            $qtdMulcumanaM = DB::table('interno')->where('religiao', 'Muçulmana')->where('sexo', 'F')->where('status', 'A');
//            $qtdProtestanteH = DB::table('interno')->where('religiao', 'Protestante')->where('sexo', 'M')->where('status', 'A');
//            $qtdProtestanteM = DB::table('interno')->where('religiao', 'Protestante')->where('sexo', 'F')->where('status', 'A');
//            $qtdAgnosticoH = DB::table('interno')->where('religiao', 'Agnóstico')->where('sexo', 'M')->where('status', 'A');
//            $qtdAgnosticoM = DB::table('interno')->where('religiao', 'Agnóstico')->where('sexo', 'F')->where('status', 'A');
//            $qtdAteuH = DB::table('interno')->where('religiao', 'Ateu')->where('sexo', 'M')->where('status', 'A');
//            $qtdAteuM = DB::table('interno')->where('religiao', 'Ateu')->where('sexo', 'F')->where('status', 'A');
//            $qtdSemReligiaoH = DB::table('interno')->where('religiao', 'Sem Religião')->where('sexo', 'M')->where('status', 'A');
//            $qtdSemReligiaoM = DB::table('interno')->where('religiao', 'Sem Religião')->where('sexo', 'F')->where('status', 'A');
//            $qtdOutrasH = DB::table('interno')->where('religiao', 'Outras')->where('sexo', 'M')->where('status', 'A');
//            $qtdOutrasM = DB::table('interno')->where('religiao', 'Outras')->where('sexo', 'F')->where('status', 'A');
//            $qtdReligiaoNIH = DB::table('interno')->where('religiao', null)->where('sexo', 'M')->where('status', 'A');
//            $qtdReligiaoNIM = DB::table('interno')->where('religiao', null)->where('sexo', 'F')->where('status', 'A');
//        }
//
//
//        //Saída da Unidade Prisional
//        $qtdAlvara = DB::table('movimentacao')->where('tipo','alvara');
//        $qtdFuga = DB::table('movimentacao')->where('tipo','fuga');
//        $qtdRecambiamento = DB::table('movimentacao')->where('tipo','recambiamento');
//        $qtdSaidaJudicial = DB::table('movimentacao')->where('tipo','saidajudicial');
//        $qtdExtradicao = DB::table('movimentacao')->where('tipo','extradicao');
//        $qtdLivramento = DB::table('movimentacao')->where('tipo','livramentocondicional');
//        $qtdObito = DB::table('movimentacao')->where('tipo','obito');
//        $qtdExtincaopunibilidade = DB::table('movimentacao')->where('tipo','extincaopunibilidade');
//
//        $qtdAlvara->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
//        $qtdFuga->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
//        $qtdRecambiamento->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
//        $qtdSaidaJudicial->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
//        $qtdExtradicao->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
//        $qtdLivramento->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
//        $qtdObito->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
//        $qtdExtincaopunibilidade->leftJoin('movimentacao_interno', 'movimentacao_interno.fk_movimentacao', 'movimentacao.id');
//
//
//        if(!is_null($unidade)) {
//            $qtdUnidadeHA->where('unidade_prisional', $unidade);
//            $qtdUnidadeMA->where('unidade_prisional', $unidade);
//            $qtdUnidadeTotal->where('unidade_prisional', $unidade);
//
//            $qtdInternosLotSelecionada->where('unidade_prisional', $unidade);
//
//            $qtdRegimeNIH->where('unidade_prisional', $unidade);
//            $qtdRegimeNIM->where('unidade_prisional', $unidade);
//            $qtdRegimeFechadoH->where('unidade_prisional', $unidade);
//            $qtdRegimeFechadoM->where('unidade_prisional', $unidade);
//            $qtdRegimeSemiabertoH->where('unidade_prisional', $unidade);
//            $qtdRegimeSemiabertoM->where('unidade_prisional', $unidade);
//            $qtdRegimeAbertoH->where('unidade_prisional', $unidade);
//            $qtdRegimeAbertoM->where('unidade_prisional', $unidade);
//
//            $qtdSentenciadosNIH->where('unidade_prisional', $unidade);
//            $qtdSentenciadosNIM->where('unidade_prisional', $unidade);
//            $qtdSentenciadosH->where('unidade_prisional', $unidade);
//            $qtdSentenciadosM->where('unidade_prisional', $unidade);
//            $qtdProvisoriosH->where('unidade_prisional', $unidade);
//            $qtdProvisoriosM->where('unidade_prisional', $unidade);
//
//            $qtdSentenciadosMSNIH->where('unidade_prisional', $unidade);
//            $qtdSentenciadosMSNIM->where('unidade_prisional', $unidade);
//            $qtdSentenciadosMSH->where('unidade_prisional', $unidade);
//            $qtdSentenciadosMSM->where('unidade_prisional', $unidade);
//            $qtdProvisoriosMSH->where('unidade_prisional', $unidade);
//            $qtdProvisoriosMSM->where('unidade_prisional', $unidade);
//
//            $qtdMedidaDeSegurancaH->where('unidade_prisional', $unidade);
//            $qtdMedidaDeSegurancaM->where('unidade_prisional', $unidade);
//
//            $qtdHA->where('unidade_prisional', $unidade);
//            $qtdMA->where('unidade_prisional', $unidade);
//
//            $internosH->where('unidade_prisional', $unidade);
//            $internosM->where('unidade_prisional', $unidade);
//
//            $qtdFaccaoNIH->where('unidade_prisional', $unidade);
//            $qtdFaccaoNIM->where('unidade_prisional', $unidade);
//
//            $periculosidadeAltaH->where('unidade_prisional', $unidade);
//            $periculosidadeMediaH->where('unidade_prisional', $unidade);
//            $periculosidadeBaixaH->where('unidade_prisional', $unidade);
//            $periculosidadeNIH->where('unidade_prisional', $unidade);
//            $periculosidadeAltaM->where('unidade_prisional', $unidade);
//            $periculosidadeMediaM->where('unidade_prisional', $unidade);
//            $periculosidadeBaixaM->where('unidade_prisional', $unidade);
//            $periculosidadeNIM->where('unidade_prisional', $unidade);
//
//            $qtdLiderancaSimH->where('unidade_prisional', $unidade);
//            $qtdLiderancaNaoH->where('unidade_prisional', $unidade);
//            $qtdLiderancaNIH->where('unidade_prisional', $unidade);
//            $qtdLiderancaSimM->where('unidade_prisional', $unidade);
//            $qtdLiderancaNaoM->where('unidade_prisional', $unidade);
//            $qtdLiderancaNIM->where('unidade_prisional', $unidade);
//
//            //estado_civil
//            if ($estadocivil){
//                $qtdSolteirosH->where('unidade_prisional', $unidade);
//                $qtdSolteirosM->where('unidade_prisional', $unidade);
//                $qtdCasadosH->where('unidade_prisional', $unidade);
//                $qtdCasadosM->where('unidade_prisional', $unidade);
//                $qtdUniaoEstavelH->where('unidade_prisional', $unidade);
//                $qtdUniaoEstavelM->where('unidade_prisional', $unidade);
//                $qtdSeparadosH->where('unidade_prisional', $unidade);
//                $qtdSeparadosM->where('unidade_prisional', $unidade);
//                $qtdDivorciadosH->where('unidade_prisional', $unidade);
//                $qtdDivorciadosM->where('unidade_prisional', $unidade);
//                $qtdViuvosH->where('unidade_prisional', $unidade);
//                $qtdViuvosM->where('unidade_prisional', $unidade);
//                $qtdEstadoCivilNIH->where('unidade_prisional', $unidade);
//                $qtdEstadoCivilNIM->where('unidade_prisional', $unidade);
//            }
//
//            //opcao_sexual
//            $qtdHeteroH->where('unidade_prisional',$unidade);
//            $qtdHeteroM->where('unidade_prisional',$unidade);
//            $qtdHomoH->where('unidade_prisional',$unidade);
//            $qtdHomoM->where('unidade_prisional',$unidade);
//            $qtdBiH->where('unidade_prisional',$unidade);
//            $qtdBiM->where('unidade_prisional',$unidade);
//            $qtdSexualidadeNaoDeclH->where('unidade_prisional',$unidade);
//            $qtdSexualidadeNaoDeclM->where('unidade_prisional',$unidade);
//            $qtdSexualidadeNIH->where('unidade_prisional',$unidade);
//            $qtdSexualidadeNIM->where('unidade_prisional',$unidade);
//
//            //etnia
//            $qtdEtniaBrancaH->where('unidade_prisional',$unidade);
//            $qtdEtniaBrancaM->where('unidade_prisional',$unidade);
//            $qtdEtniaIndígenaH->where('unidade_prisional',$unidade);
//            $qtdEtniaIndígenaM->where('unidade_prisional',$unidade);
//            $qtdEtniaPretaH->where('unidade_prisional',$unidade);
//            $qtdEtniaPretaM->where('unidade_prisional',$unidade);
//            $qtdEtniaPardaH->where('unidade_prisional',$unidade);
//            $qtdEtniaPardaM->where('unidade_prisional',$unidade);
//            $qtdEtniaAmarelaH->where('unidade_prisional',$unidade);
//            $qtdEtniaAmarelaM->where('unidade_prisional',$unidade);
//            $qtdEtniaNIH->where('unidade_prisional',$unidade);
//            $qtdEtniaNIM->where('unidade_prisional',$unidade);
//
//            // escolaridade
//            if($escolaridade){
//                $qtdAnalfabetoH->where('unidade_prisional',$unidade);
//                $qtdAnalfabetoM->where('unidade_prisional',$unidade);
//                $qtdAlfabetizadoH->where('unidade_prisional',$unidade);
//                $qtdAlfabetizadoM->where('unidade_prisional',$unidade);
//                $qtdFundIncH->where('unidade_prisional',$unidade);
//                $qtdFundIncM->where('unidade_prisional',$unidade);
//                $qtdFundCompH->where('unidade_prisional',$unidade);
//                $qtdFundCompM->where('unidade_prisional',$unidade);
//                $qtdMedioIncompH->where('unidade_prisional',$unidade);
//                $qtdMedioIncompM->where('unidade_prisional',$unidade);
//                $qtdMedioCompH->where('unidade_prisional',$unidade);
//                $qtdMedioCompM->where('unidade_prisional',$unidade);
//                $qtdSuperiorIncompH->where('unidade_prisional',$unidade);
//                $qtdSuperiorIncompM->where('unidade_prisional',$unidade);
//                $qtdSuperiorCompH->where('unidade_prisional',$unidade);
//                $qtdSuperiorCompM->where('unidade_prisional',$unidade);
//                $qtdPosLatoSensuH->where('unidade_prisional',$unidade);
//                $qtdPosLatoSensuM->where('unidade_prisional',$unidade);
//                $qtdMestradoH->where('unidade_prisional',$unidade);
//                $qtdMestradoM->where('unidade_prisional',$unidade);
//                $qtdDoutoradoH->where('unidade_prisional',$unidade);
//                $qtdDoutoradoM->where('unidade_prisional',$unidade);
//                $qtdPosDoutoradoH->where('unidade_prisional',$unidade);
//                $qtdPosDoutoradoM->where('unidade_prisional',$unidade);
//                $qtdEscolaridadeNIH->where('unidade_prisional',$unidade);
//                $qtdEscolaridadeNIM->where('unidade_prisional',$unidade);
//            }
//
//            $qtdNacionalidadeNIH->where('unidade_prisional',$unidade);
//            $qtdNacionalidadeNIM->where('unidade_prisional',$unidade);
//
//            //religião
//            if($religiao){
//                $qtdAdventistaH->where('unidade_prisional',$unidade);
//                $qtdAdventistaM->where('unidade_prisional',$unidade);
//                $qtdBudistaH->where('unidade_prisional',$unidade);
//                $qtdBudistaM->where('unidade_prisional',$unidade);
//                $qtdCatolicaH->where('unidade_prisional',$unidade);
//                $qtdCatolicaM->where('unidade_prisional',$unidade);
//                $qtdEspiritaH->where('unidade_prisional',$unidade);
//                $qtdEspiritaM->where('unidade_prisional',$unidade);
//                $qtdEvangelicaH->where('unidade_prisional',$unidade);
//                $qtdEvangelicaM->where('unidade_prisional',$unidade);
//                $qtdJudeuH->where('unidade_prisional',$unidade);
//                $qtdJudeuM->where('unidade_prisional',$unidade);
//                $qtdMormonH->where('unidade_prisional',$unidade);
//                $qtdMormonM->where('unidade_prisional',$unidade);
//                $qtdMulcumanaH->where('unidade_prisional',$unidade);
//                $qtdMulcumanaM->where('unidade_prisional',$unidade);
//                $qtdProtestanteH->where('unidade_prisional',$unidade);
//                $qtdProtestanteM->where('unidade_prisional',$unidade);
//                $qtdAgnosticoH->where('unidade_prisional',$unidade);
//                $qtdAgnosticoM->where('unidade_prisional',$unidade);
//                $qtdAteuH->where('unidade_prisional',$unidade);
//                $qtdAteuM->where('unidade_prisional',$unidade);
//                $qtdSemReligiaoH->where('unidade_prisional',$unidade);
//                $qtdSemReligiaoM->where('unidade_prisional',$unidade);
//                $qtdOutrasH->where('unidade_prisional',$unidade);
//                $qtdOutrasM->where('unidade_prisional',$unidade);
//                $qtdReligiaoNIH->where('unidade_prisional',$unidade);
//                $qtdReligiaoNIM->where('unidade_prisional',$unidade);
//            }
//
//            $qtdAlvara->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
//            $qtdAlvara->where('movimentacao_interno.status','Finalizada');
//            $qtdAlvara->where('interno.unidade_prisional', $unidade);
//            $qtdFuga->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
//            $qtdFuga->where('movimentacao_interno.status','Finalizada');
//            $qtdFuga->where('interno.unidade_prisional', $unidade);
//            $qtdRecambiamento->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
//            $qtdRecambiamento->where('movimentacao_interno.status','Finalizada');
//            $qtdRecambiamento->where('interno.unidade_prisional', $unidade);
//            $qtdSaidaJudicial->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
//            $qtdSaidaJudicial->where('movimentacao_interno.status','Finalizada');
//            $qtdSaidaJudicial->where('interno.unidade_prisional', $unidade);
//            $qtdExtradicao->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
//            $qtdExtradicao->where('movimentacao_interno.status','Finalizada');
//            $qtdExtradicao->where('interno.unidade_prisional', $unidade);
//            $qtdLivramento->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
//            $qtdLivramento->where('movimentacao_interno.status','Finalizada');
//            $qtdLivramento->where('interno.unidade_prisional', $unidade);
//            $qtdObito->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
//            $qtdObito->where('movimentacao_interno.status','Finalizada');
//            $qtdObito->where('interno.unidade_prisional', $unidade);
//            $qtdExtincaopunibilidade->leftJoin('interno', 'movimentacao_interno.fk_interno', 'interno.id');
//            $qtdExtincaopunibilidade->where('movimentacao_interno.status','Finalizada');
//            $qtdExtincaopunibilidade->where('interno.unidade_prisional', $unidade);
//        }
//        if(!is_null($pavilhao)){
//            $qtdUnidadeHA->where('pavilhao', $pavilhao);
//            $qtdUnidadeMA->where('pavilhao', $pavilhao);
//            $qtdUnidadeTotal->where('pavilhao', $pavilhao);
//
//            $qtdInternosLotSelecionada->where('pavilhao',$pavilhao);
//
//            $qtdRegimeNIH->where('pavilhao',$pavilhao);
//            $qtdRegimeNIM->where('pavilhao',$pavilhao);
//            $qtdRegimeFechadoH->where('pavilhao',$pavilhao);
//            $qtdRegimeFechadoM->where('pavilhao',$pavilhao);
//            $qtdRegimeSemiabertoH->where('pavilhao',$pavilhao);
//            $qtdRegimeSemiabertoM->where('pavilhao',$pavilhao);
//            $qtdRegimeAbertoH->where('pavilhao',$pavilhao);
//            $qtdRegimeAbertoM->where('pavilhao',$pavilhao);
//
//            $qtdSentenciadosNIH->where('pavilhao',$pavilhao);
//            $qtdSentenciadosNIM->where('pavilhao',$pavilhao);
//            $qtdSentenciadosH->where('pavilhao',$pavilhao);
//            $qtdSentenciadosM->where('pavilhao',$pavilhao);
//            $qtdProvisoriosH->where('pavilhao',$pavilhao);
//            $qtdProvisoriosM->where('pavilhao',$pavilhao);
//
//            $qtdSentenciadosMSNIH->where('pavilhao',$pavilhao);
//            $qtdSentenciadosMSNIM->where('pavilhao',$pavilhao);
//            $qtdSentenciadosMSH->where('pavilhao',$pavilhao);
//            $qtdSentenciadosMSM->where('pavilhao',$pavilhao);
//            $qtdProvisoriosMSH->where('pavilhao',$pavilhao);
//            $qtdProvisoriosMSM->where('pavilhao',$pavilhao);
//
//            $qtdMedidaDeSegurancaH->where('pavilhao',$pavilhao);
//            $qtdMedidaDeSegurancaM->where('pavilhao',$pavilhao);
//
//            $qtdHA->where('pavilhao',$pavilhao);
//            $qtdMA->where('pavilhao',$pavilhao);
//
//            $internosH->where('pavilhao',$pavilhao);
//            $internosM->where('pavilhao',$pavilhao);
//
//            $qtdFaccaoNIH->where('pavilhao',$pavilhao);
//            $qtdFaccaoNIM->where('pavilhao',$pavilhao);
//
//            $periculosidadeAltaH->where('pavilhao',$pavilhao);
//            $periculosidadeMediaH->where('pavilhao',$pavilhao);
//            $periculosidadeBaixaH->where('pavilhao',$pavilhao);
//            $periculosidadeNIH->where('pavilhao',$pavilhao);
//            $periculosidadeAltaM->where('pavilhao',$pavilhao);
//            $periculosidadeMediaM->where('pavilhao',$pavilhao);
//            $periculosidadeBaixaM->where('pavilhao',$pavilhao);
//            $periculosidadeNIM->where('pavilhao',$pavilhao);
//
//            $qtdLiderancaSimH->where('pavilhao',$pavilhao);
//            $qtdLiderancaNaoH->where('pavilhao',$pavilhao);
//            $qtdLiderancaNIH->where('pavilhao',$pavilhao);
//            $qtdLiderancaSimM->where('pavilhao',$pavilhao);
//            $qtdLiderancaNaoM->where('pavilhao',$pavilhao);
//            $qtdLiderancaNIM->where('pavilhao',$pavilhao);
//
//            //estado_civil
//            if($estadocivil){
//                $qtdSolteirosH->where('pavilhao',$pavilhao);
//                $qtdSolteirosM->where('pavilhao',$pavilhao);
//                $qtdCasadosH->where('pavilhao',$pavilhao);
//                $qtdCasadosM->where('pavilhao',$pavilhao);
//                $qtdUniaoEstavelH->where('pavilhao',$pavilhao);
//                $qtdUniaoEstavelM->where('pavilhao',$pavilhao);
//                $qtdSeparadosH->where('pavilhao',$pavilhao);
//                $qtdSeparadosM->where('pavilhao',$pavilhao);
//                $qtdDivorciadosH->where('pavilhao',$pavilhao);
//                $qtdDivorciadosM->where('pavilhao',$pavilhao);
//                $qtdViuvosH->where('pavilhao',$pavilhao);
//                $qtdViuvosM->where('pavilhao',$pavilhao);
//                $qtdEstadoCivilNIH->where('pavilhao',$pavilhao);
//                $qtdEstadoCivilNIM->where('pavilhao',$pavilhao);
//            }
//
//            //opcao_sexual
//            $qtdHeteroH->where('pavilhao',$pavilhao);
//            $qtdHeteroM->where('pavilhao',$pavilhao);
//            $qtdHomoH->where('pavilhao',$pavilhao);
//            $qtdHomoM->where('pavilhao',$pavilhao);
//            $qtdBiH->where('pavilhao',$pavilhao);
//            $qtdBiM->where('pavilhao',$pavilhao);
//            $qtdSexualidadeNaoDeclH->where('pavilhao',$pavilhao);
//            $qtdSexualidadeNaoDeclM->where('pavilhao',$pavilhao);
//            $qtdSexualidadeNIH->where('pavilhao',$pavilhao);
//            $qtdSexualidadeNIM->where('pavilhao',$pavilhao);
//
//            //etnia
//            $qtdEtniaBrancaH->where('pavilhao',$pavilhao);
//            $qtdEtniaBrancaM->where('pavilhao',$pavilhao);
//            $qtdEtniaIndígenaH->where('pavilhao',$pavilhao);
//            $qtdEtniaIndígenaM->where('pavilhao',$pavilhao);
//            $qtdEtniaPretaH->where('pavilhao',$pavilhao);
//            $qtdEtniaPretaM->where('pavilhao',$pavilhao);
//            $qtdEtniaPardaH->where('pavilhao',$pavilhao);
//            $qtdEtniaPardaM->where('pavilhao',$pavilhao);
//            $qtdEtniaAmarelaH->where('pavilhao',$pavilhao);
//            $qtdEtniaAmarelaM->where('pavilhao',$pavilhao);
//            $qtdEtniaNIH->where('pavilhao',$pavilhao);
//            $qtdEtniaNIM->where('pavilhao',$pavilhao);
//
//            // escolaridade
//            if($escolaridade) {
//                $qtdAnalfabetoH->where('pavilhao', $pavilhao);
//                $qtdAnalfabetoM->where('pavilhao', $pavilhao);
//                $qtdAlfabetizadoH->where('pavilhao', $pavilhao);
//                $qtdAlfabetizadoM->where('pavilhao', $pavilhao);
//                $qtdFundIncH->where('pavilhao', $pavilhao);
//                $qtdFundIncM->where('pavilhao', $pavilhao);
//                $qtdFundCompH->where('pavilhao', $pavilhao);
//                $qtdFundCompM->where('pavilhao', $pavilhao);
//                $qtdMedioIncompH->where('pavilhao', $pavilhao);
//                $qtdMedioIncompM->where('pavilhao', $pavilhao);
//                $qtdMedioCompH->where('pavilhao', $pavilhao);
//                $qtdMedioCompM->where('pavilhao', $pavilhao);
//                $qtdSuperiorIncompH->where('pavilhao', $pavilhao);
//                $qtdSuperiorIncompM->where('pavilhao', $pavilhao);
//                $qtdSuperiorCompH->where('pavilhao', $pavilhao);
//                $qtdSuperiorCompM->where('pavilhao', $pavilhao);
//                $qtdPosLatoSensuH->where('pavilhao', $pavilhao);
//                $qtdPosLatoSensuM->where('pavilhao', $pavilhao);
//                $qtdMestradoH->where('pavilhao', $pavilhao);
//                $qtdMestradoM->where('pavilhao', $pavilhao);
//                $qtdDoutoradoH->where('pavilhao', $pavilhao);
//                $qtdDoutoradoM->where('pavilhao', $pavilhao);
//                $qtdPosDoutoradoH->where('pavilhao', $pavilhao);
//                $qtdPosDoutoradoM->where('pavilhao', $pavilhao);
//                $qtdEscolaridadeNIH->where('pavilhao', $pavilhao);
//                $qtdEscolaridadeNIM->where('pavilhao', $pavilhao);
//            }
//
//            $qtdNacionalidadeNIH->where('pavilhao',$pavilhao);
//            $qtdNacionalidadeNIM->where('pavilhao',$pavilhao);
//
//            if($religiao){
//                $qtdAdventistaH->where('pavilhao',$pavilhao);
//                $qtdAdventistaM->where('pavilhao',$pavilhao);
//                $qtdBudistaH->where('pavilhao',$pavilhao);
//                $qtdBudistaM->where('pavilhao',$pavilhao);
//                $qtdCatolicaH->where('pavilhao',$pavilhao);
//                $qtdCatolicaM->where('pavilhao',$pavilhao);
//                $qtdEspiritaH->where('pavilhao',$pavilhao);
//                $qtdEspiritaM->where('pavilhao',$pavilhao);
//                $qtdEvangelicaH->where('pavilhao',$pavilhao);
//                $qtdEvangelicaM->where('pavilhao',$pavilhao);
//                $qtdJudeuH->where('pavilhao',$pavilhao);
//                $qtdJudeuM->where('pavilhao',$pavilhao);
//                $qtdMormonH->where('pavilhao',$pavilhao);
//                $qtdMormonM->where('pavilhao',$pavilhao);
//                $qtdMulcumanaH->where('pavilhao',$pavilhao);
//                $qtdMulcumanaM->where('pavilhao',$pavilhao);
//                $qtdProtestanteH->where('pavilhao',$pavilhao);
//                $qtdProtestanteM->where('pavilhao',$pavilhao);
//                $qtdAgnosticoH->where('pavilhao',$pavilhao);
//                $qtdAgnosticoM->where('pavilhao',$pavilhao);
//                $qtdAteuH->where('pavilhao',$pavilhao);
//                $qtdAteuM->where('pavilhao',$pavilhao);
//                $qtdSemReligiaoH->where('pavilhao',$pavilhao);
//                $qtdSemReligiaoM->where('pavilhao',$pavilhao);
//                $qtdOutrasH->where('pavilhao',$pavilhao);
//                $qtdOutrasM->where('pavilhao',$pavilhao);
//                $qtdReligiaoNIH->where('pavilhao',$pavilhao);
//                $qtdReligiaoNIM->where('pavilhao',$pavilhao);
//            }
//
//            //saida da unidade
//            $qtdAlvara->where('interno.pavilhao',$pavilhao);
//            $qtdFuga->where('interno.pavilhao',$pavilhao);
//            $qtdRecambiamento->where('interno.pavilhao',$pavilhao);
//            $qtdSaidaJudicial->where('interno.pavilhao',$pavilhao);
//            $qtdExtradicao->where('interno.pavilhao',$pavilhao);
//            $qtdLivramento->where('interno.pavilhao',$pavilhao);
//            $qtdObito->where('interno.pavilhao',$pavilhao);
//            $qtdExtincaopunibilidade->where('interno.pavilhao',$pavilhao);
//        }
//        if(!is_null($ala)) {
//            $qtdUnidadeHA->where('ala', $ala);
//            $qtdUnidadeMA->where('ala', $ala);
//            $qtdUnidadeTotal->where('ala', $ala);
//
//            $qtdInternosLotSelecionada->where('ala',$ala);
//
//            $qtdRegimeNIH->where('ala',$ala);
//            $qtdRegimeNIM->where('ala',$ala);
//            $qtdRegimeFechadoH->where('ala',$ala);
//            $qtdRegimeFechadoM->where('ala',$ala);
//            $qtdRegimeSemiabertoH->where('ala',$ala);
//            $qtdRegimeSemiabertoM->where('ala',$ala);
//            $qtdRegimeAbertoH->where('ala',$ala);
//            $qtdRegimeAbertoM->where('ala',$ala);
//
//            $qtdSentenciadosNIH->where('ala',$ala);
//            $qtdSentenciadosNIM->where('ala',$ala);
//            $qtdSentenciadosH->where('ala',$ala);
//            $qtdSentenciadosM->where('ala',$ala);
//            $qtdProvisoriosH->where('ala',$ala);
//            $qtdProvisoriosM->where('ala',$ala);
//
//            $qtdSentenciadosMSNIH->where('ala',$ala);
//            $qtdSentenciadosMSNIM->where('ala',$ala);
//            $qtdSentenciadosMSH->where('ala',$ala);
//            $qtdSentenciadosMSM->where('ala',$ala);
//            $qtdProvisoriosMSH->where('ala',$ala);
//            $qtdProvisoriosMSM->where('ala',$ala);
//
//            $qtdMedidaDeSegurancaH->where('ala',$ala);
//            $qtdMedidaDeSegurancaM->where('ala',$ala);
//
//            $qtdHA->where('ala',$ala);
//            $qtdMA->where('ala',$ala);
//
//            $internosH->where('ala',$ala);
//            $internosM->where('ala',$ala);
//
//            $qtdFaccaoNIH->where('ala',$ala);
//            $qtdFaccaoNIM->where('ala',$ala);
//
//            $periculosidadeAltaH->where('ala',$ala);
//            $periculosidadeMediaH->where('ala',$ala);
//            $periculosidadeBaixaH->where('ala',$ala);
//            $periculosidadeNIH->where('ala',$ala);
//            $periculosidadeAltaM->where('ala',$ala);
//            $periculosidadeMediaM->where('ala',$ala);
//            $periculosidadeBaixaM->where('ala',$ala);
//            $periculosidadeNIM->where('ala',$ala);
//
//            $qtdLiderancaSimH->where('ala',$ala);
//            $qtdLiderancaNaoH->where('ala',$ala);
//            $qtdLiderancaNIH->where('ala',$ala);
//            $qtdLiderancaSimM->where('ala',$ala);
//            $qtdLiderancaNaoM->where('ala',$ala);
//            $qtdLiderancaNIM->where('ala',$ala);
//
//            //estado_civil
//            if($estadocivil) {
//                $qtdSolteirosH->where('ala', $ala);
//                $qtdSolteirosM->where('ala', $ala);
//                $qtdCasadosH->where('ala', $ala);
//                $qtdCasadosM->where('ala', $ala);
//                $qtdUniaoEstavelH->where('ala', $ala);
//                $qtdUniaoEstavelM->where('ala', $ala);
//                $qtdSeparadosH->where('ala', $ala);
//                $qtdSeparadosM->where('ala', $ala);
//                $qtdDivorciadosH->where('ala', $ala);
//                $qtdDivorciadosM->where('ala', $ala);
//                $qtdViuvosH->where('ala', $ala);
//                $qtdViuvosM->where('ala', $ala);
//                $qtdEstadoCivilNIH->where('ala', $ala);
//                $qtdEstadoCivilNIM->where('ala', $ala);
//            }
//
//            //opcao_sexual
//            $qtdHeteroH->where('ala',$ala);
//            $qtdHeteroM->where('ala',$ala);
//            $qtdHomoH->where('ala',$ala);
//            $qtdHomoM->where('ala',$ala);
//            $qtdBiH->where('ala',$ala);
//            $qtdBiM->where('ala',$ala);
//            $qtdSexualidadeNaoDeclH->where('ala',$ala);
//            $qtdSexualidadeNaoDeclM->where('ala',$ala);
//            $qtdSexualidadeNIH->where('ala',$ala);
//            $qtdSexualidadeNIM->where('ala',$ala);
//
//            //etnia
//            $qtdEtniaBrancaH->where('ala',$ala);
//            $qtdEtniaBrancaM->where('ala',$ala);
//            $qtdEtniaIndígenaH->where('ala',$ala);
//            $qtdEtniaIndígenaM->where('ala',$ala);
//            $qtdEtniaPretaH->where('ala',$ala);
//            $qtdEtniaPretaM->where('ala',$ala);
//            $qtdEtniaPardaH->where('ala',$ala);
//            $qtdEtniaPardaM->where('ala',$ala);
//            $qtdEtniaAmarelaH->where('ala',$ala);
//            $qtdEtniaAmarelaM->where('ala',$ala);
//            $qtdEtniaNIH->where('ala',$ala);
//            $qtdEtniaNIM->where('ala',$ala);
//
//            // escolaridade
//            if($escolaridade) {
//                $qtdAnalfabetoH->where('ala', $ala);
//                $qtdAnalfabetoM->where('ala', $ala);
//                $qtdAlfabetizadoH->where('ala', $ala);
//                $qtdAlfabetizadoM->where('ala', $ala);
//                $qtdFundIncH->where('ala', $ala);
//                $qtdFundIncM->where('ala', $ala);
//                $qtdFundCompH->where('ala', $ala);
//                $qtdFundCompM->where('ala', $ala);
//                $qtdMedioIncompH->where('ala', $ala);
//                $qtdMedioIncompM->where('ala', $ala);
//                $qtdMedioCompH->where('ala', $ala);
//                $qtdMedioCompM->where('ala', $ala);
//                $qtdSuperiorIncompH->where('ala', $ala);
//                $qtdSuperiorIncompM->where('ala', $ala);
//                $qtdSuperiorCompH->where('ala', $ala);
//                $qtdSuperiorCompM->where('ala', $ala);
//                $qtdPosLatoSensuH->where('ala', $ala);
//                $qtdPosLatoSensuM->where('ala', $ala);
//                $qtdMestradoH->where('ala', $ala);
//                $qtdMestradoM->where('ala', $ala);
//                $qtdDoutoradoH->where('ala', $ala);
//                $qtdDoutoradoM->where('ala', $ala);
//                $qtdPosDoutoradoH->where('ala', $ala);
//                $qtdPosDoutoradoM->where('ala', $ala);
//                $qtdEscolaridadeNIH->where('ala', $ala);
//                $qtdEscolaridadeNIM->where('ala', $ala);
//            }
//
//            $qtdNacionalidadeNIH->where('ala',$ala);
//            $qtdNacionalidadeNIM->where('ala',$ala);
//
//            if($religiao) {
//                $qtdAdventistaH->where('ala', $ala);
//                $qtdAdventistaM->where('ala', $ala);
//                $qtdBudistaH->where('ala', $ala);
//                $qtdBudistaM->where('ala', $ala);
//                $qtdCatolicaH->where('ala', $ala);
//                $qtdCatolicaM->where('ala', $ala);
//                $qtdEspiritaH->where('ala', $ala);
//                $qtdEspiritaM->where('ala', $ala);
//                $qtdEvangelicaH->where('ala', $ala);
//                $qtdEvangelicaM->where('ala', $ala);
//                $qtdJudeuH->where('ala', $ala);
//                $qtdJudeuM->where('ala', $ala);
//                $qtdMormonH->where('ala', $ala);
//                $qtdMormonM->where('ala', $ala);
//                $qtdMulcumanaH->where('ala', $ala);
//                $qtdMulcumanaM->where('ala', $ala);
//                $qtdProtestanteH->where('ala', $ala);
//                $qtdProtestanteM->where('ala', $ala);
//                $qtdAgnosticoH->where('ala', $ala);
//                $qtdAgnosticoM->where('ala', $ala);
//                $qtdAteuH->where('ala', $ala);
//                $qtdAteuM->where('ala', $ala);
//                $qtdSemReligiaoH->where('ala', $ala);
//                $qtdSemReligiaoM->where('ala', $ala);
//                $qtdOutrasH->where('ala', $ala);
//                $qtdOutrasM->where('ala', $ala);
//                $qtdReligiaoNIH->where('ala', $ala);
//                $qtdReligiaoNIM->where('ala', $ala);
//            }
//
//            //saida da unidade
//            $qtdAlvara->where('interno.ala',$ala);
//            $qtdFuga->where('interno.ala',$ala);
//            $qtdRecambiamento->where('interno.ala',$ala);
//            $qtdSaidaJudicial->where('interno.ala',$ala);
//            $qtdExtradicao->where('interno.ala',$ala);
//            $qtdLivramento->where('interno.ala',$ala);
//            $qtdObito->where('interno.ala',$ala);
//            $qtdExtincaopunibilidade->where('interno.ala',$ala);
//        }
//        if(!is_null($cela)) {
//            $qtdUnidadeHA->where('cela', $cela);
//            $qtdUnidadeMA->where('cela', $cela);
//            $qtdUnidadeTotal->where('cela', $cela);
//
//            $qtdInternosLotSelecionada->where('cela',$cela);
//
//            $qtdRegimeNIH->where('cela',$cela);
//            $qtdRegimeNIM->where('cela',$cela);
//            $qtdRegimeFechadoH->where('cela',$cela);
//            $qtdRegimeFechadoM->where('cela',$cela);
//            $qtdRegimeSemiabertoH->where('cela',$cela);
//            $qtdRegimeSemiabertoM->where('cela',$cela);
//            $qtdRegimeAbertoH->where('cela',$cela);
//            $qtdRegimeAbertoM->where('cela',$cela);
//
//            $qtdSentenciadosNIH->where('cela',$cela);
//            $qtdSentenciadosNIM->where('cela',$cela);
//            $qtdSentenciadosH->where('cela',$cela);
//            $qtdSentenciadosM->where('cela',$cela);
//            $qtdProvisoriosH->where('cela',$cela);
//            $qtdProvisoriosM->where('cela',$cela);
//
//            $qtdSentenciadosMSNIH->where('cela',$cela);
//            $qtdSentenciadosMSNIM->where('cela',$cela);
//            $qtdSentenciadosMSH->where('cela',$cela);
//            $qtdSentenciadosMSM->where('cela',$cela);
//            $qtdProvisoriosMSH->where('cela',$cela);
//            $qtdProvisoriosMSM->where('cela',$cela);
//
//            $qtdMedidaDeSegurancaH->where('cela',$cela);
//            $qtdMedidaDeSegurancaM->where('cela',$cela);
//
//            $qtdHA->where('cela',$cela);
//            $qtdMA->where('cela',$cela);
//
//            $internosH->where('cela',$cela);
//            $internosM->where('cela',$cela);
//
//            $qtdFaccaoNIH->where('cela',$cela);
//            $qtdFaccaoNIM->where('cela',$cela);
//
//            $periculosidadeAltaH->where('cela',$cela);
//            $periculosidadeMediaH->where('cela',$cela);
//            $periculosidadeBaixaH->where('cela',$cela);
//            $periculosidadeNIH->where('cela',$cela);
//            $periculosidadeAltaM->where('cela',$cela);
//            $periculosidadeMediaM->where('cela',$cela);
//            $periculosidadeBaixaM->where('cela',$cela);
//            $periculosidadeNIM->where('cela',$cela);
//
//            $qtdLiderancaSimH->where('cela',$cela);
//            $qtdLiderancaNaoH->where('cela',$cela);
//            $qtdLiderancaNIH->where('cela',$cela);
//            $qtdLiderancaSimM->where('cela',$cela);
//            $qtdLiderancaNaoM->where('cela',$cela);
//            $qtdLiderancaNIM->where('cela',$cela);
//
//            //estado_civil
//            if($estadocivil) {
//                $qtdSolteirosH->where('cela', $cela);
//                $qtdSolteirosM->where('cela', $cela);
//                $qtdCasadosH->where('cela', $cela);
//                $qtdCasadosM->where('cela', $cela);
//                $qtdUniaoEstavelH->where('cela', $cela);
//                $qtdUniaoEstavelM->where('cela', $cela);
//                $qtdSeparadosH->where('cela', $cela);
//                $qtdSeparadosM->where('cela', $cela);
//                $qtdDivorciadosH->where('cela', $cela);
//                $qtdDivorciadosM->where('cela', $cela);
//                $qtdViuvosH->where('cela', $cela);
//                $qtdViuvosM->where('cela', $cela);
//                $qtdEstadoCivilNIH->where('cela', $cela);
//                $qtdEstadoCivilNIM->where('cela', $cela);
//            }
//
//            //opcao_sexual
//            $qtdHeteroH->where('cela',$cela);
//            $qtdHeteroM->where('cela',$cela);
//            $qtdHomoH->where('cela',$cela);
//            $qtdHomoM->where('cela',$cela);
//            $qtdBiH->where('cela',$cela);
//            $qtdBiM->where('cela',$cela);
//            $qtdSexualidadeNaoDeclH->where('cela',$cela);
//            $qtdSexualidadeNaoDeclM->where('cela',$cela);
//            $qtdSexualidadeNIH->where('cela',$cela);
//            $qtdSexualidadeNIM->where('cela',$cela);
//
//            //etnia
//            $qtdEtniaBrancaH->where('cela',$cela);
//            $qtdEtniaBrancaM->where('cela',$cela);
//            $qtdEtniaIndígenaH->where('cela',$cela);
//            $qtdEtniaIndígenaM->where('cela',$cela);
//            $qtdEtniaPretaH->where('cela',$cela);
//            $qtdEtniaPretaM->where('cela',$cela);
//            $qtdEtniaPardaH->where('cela',$cela);
//            $qtdEtniaPardaM->where('cela',$cela);
//            $qtdEtniaAmarelaH->where('cela',$cela);
//            $qtdEtniaAmarelaM->where('cela',$cela);
//            $qtdEtniaNIH->where('cela',$cela);
//            $qtdEtniaNIM->where('cela',$cela);
//
//            // escolaridade
//            if($escolaridade) {
//                $qtdAnalfabetoH->where('cela', $cela);
//                $qtdAnalfabetoM->where('cela', $cela);
//                $qtdAlfabetizadoH->where('cela', $cela);
//                $qtdAlfabetizadoM->where('cela', $cela);
//                $qtdFundIncH->where('cela', $cela);
//                $qtdFundIncM->where('cela', $cela);
//                $qtdFundCompH->where('cela', $cela);
//                $qtdFundCompM->where('cela', $cela);
//                $qtdMedioIncompH->where('cela', $cela);
//                $qtdMedioIncompM->where('cela', $cela);
//                $qtdMedioCompH->where('cela', $cela);
//                $qtdMedioCompM->where('cela', $cela);
//                $qtdSuperiorIncompH->where('cela', $cela);
//                $qtdSuperiorIncompM->where('cela', $cela);
//                $qtdSuperiorCompH->where('cela', $cela);
//                $qtdSuperiorCompM->where('cela', $cela);
//                $qtdPosLatoSensuH->where('cela', $cela);
//                $qtdPosLatoSensuM->where('cela', $cela);
//                $qtdMestradoH->where('cela', $cela);
//                $qtdMestradoM->where('cela', $cela);
//                $qtdDoutoradoH->where('cela', $cela);
//                $qtdDoutoradoM->where('cela', $cela);
//                $qtdPosDoutoradoH->where('cela', $cela);
//                $qtdPosDoutoradoM->where('cela', $cela);
//                $qtdEscolaridadeNIH->where('cela', $cela);
//                $qtdEscolaridadeNIM->where('cela', $cela);
//            }
//
//            $qtdNacionalidadeNIH->where('cela',$cela);
//            $qtdNacionalidadeNIM->where('cela',$cela);
//
//            if($religiao) {
//                $qtdAdventistaH->where('cela', $cela);
//                $qtdAdventistaM->where('cela', $cela);
//                $qtdBudistaH->where('cela', $cela);
//                $qtdBudistaM->where('cela', $cela);
//                $qtdCatolicaH->where('cela', $cela);
//                $qtdCatolicaM->where('cela', $cela);
//                $qtdEspiritaH->where('cela', $cela);
//                $qtdEspiritaM->where('cela', $cela);
//                $qtdEvangelicaH->where('cela', $cela);
//                $qtdEvangelicaM->where('cela', $cela);
//                $qtdJudeuH->where('cela', $cela);
//                $qtdJudeuM->where('cela', $cela);
//                $qtdMormonH->where('cela', $cela);
//                $qtdMormonM->where('cela', $cela);
//                $qtdMulcumanaH->where('cela', $cela);
//                $qtdMulcumanaM->where('cela', $cela);
//                $qtdProtestanteH->where('cela', $cela);
//                $qtdProtestanteM->where('cela', $cela);
//                $qtdAgnosticoH->where('cela', $cela);
//                $qtdAgnosticoM->where('cela', $cela);
//                $qtdAteuH->where('cela', $cela);
//                $qtdAteuM->where('cela', $cela);
//                $qtdSemReligiaoH->where('cela', $cela);
//                $qtdSemReligiaoM->where('cela', $cela);
//                $qtdOutrasH->where('cela', $cela);
//                $qtdOutrasM->where('cela', $cela);
//                $qtdReligiaoNIH->where('cela', $cela);
//                $qtdReligiaoNIM->where('cela', $cela);
//            }
//
//            //saida da unidade
//            $qtdAlvara->where('interno.cela',$cela);
//            $qtdFuga->where('interno.cela',$cela);
//            $qtdRecambiamento->where('interno.cela',$cela);
//            $qtdSaidaJudicial->where('interno.cela',$cela);
//            $qtdExtradicao->where('interno.cela',$cela);
//            $qtdLivramento->where('interno.cela',$cela);
//            $qtdObito->where('interno.cela',$cela);
//            $qtdExtincaopunibilidade->where('interno.cela',$cela);
//        }
//
//        $internosH=$internosH->get();
//        if($faixaetaria) {
//            foreach ($internosH as $interno) {
//                if (is_null($interno->data_nascimento)) {
//                    $faixaEtaria_naoInformadaH++;
//                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 17) {
//                    $faixaEtaria_zero_a_dezesseteH++;
//                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 18 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 24) {
//                    $faixaEtaria_dezoito_a_vinteequatroH++;
//                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 25 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 29) {
//                    $faixaEtaria_vinteecinco_a_vinteenoveH++;
//                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 30 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 34) {
//                    $faixaEtaria_trinta_a_trintaequatroH++;
//                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 35 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 45) {
//                    $faixaEtaria_trintaecinco_a_quarentaecincoH++;
//                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 46 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 60) {
//                    $faixaEtaria_quarentaeseis_a_sessentaH++;
//                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 61 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 70) {
//                    $faixaEtaria_sessentaeum_a_setentaH++;
//                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) > 70) {
//                    $faixaEtaria_maisDeSetentaH++;
//                }
//            }
//        }
//
//        $internosM=$internosM->get();
//        if($faixaetaria) {
//            foreach ($internosM as $interno) {
//                if (is_null($interno->data_nascimento)) {
//                    $faixaEtaria_naoInformadaM++;
//                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 17) {
//                    $faixaEtaria_zero_a_dezesseteM++;
//                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 18 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 24) {
//                    $faixaEtaria_dezoito_a_vinteequatroM++;
//                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 25 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 29) {
//                    $faixaEtaria_vinteecinco_a_vinteenoveM++;
//                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 30 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 34) {
//                    $faixaEtaria_trinta_a_trintaequatroM++;
//                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 35 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 45) {
//                    $faixaEtaria_trintaecinco_a_quarentaecincoM++;
//                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 46 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 60) {
//                    $faixaEtaria_quarentaeseis_a_sessentaM++;
//                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) >= 61 && Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) <= 70) {
//                    $faixaEtaria_sessentaeum_a_setentaM++;
//                } elseif (Tdata::getIdade(Tdata::TdataBR($interno->data_nascimento)) > 70) {
//                    $faixaEtaria_maisDeSetentaM++;
//                }
//            }
//        }
//
//        $nacionalidades = DB::table('interno')->select(DB::raw('count(*) as qtdd, naturalidade_pais'))
//            ->where('status','A')
//            ->groupBy('naturalidade_pais')->orderBy('qtdd','DESC')->get();
//        foreach($nacionalidades as $key=>$nacionalidade){
//            $nacionalidades[$key]->qtdH = DB::table('interno')->where('sexo','M')->where('status','A')->where('naturalidade_pais', $nacionalidade->naturalidade_pais);
//            $nacionalidades[$key]->qtdM = DB::table('interno')->where('sexo','F')->where('status','A')->where('naturalidade_pais', $nacionalidade->naturalidade_pais);
//
//            if(!is_null($unidade)) {
//                $nacionalidades[$key]->qtdH->where('unidade_prisional',$unidade);
//                $nacionalidades[$key]->qtdM->where('unidade_prisional',$unidade);
//            }
//            if(!is_null($pavilhao)) {
//                $nacionalidades[$key]->qtdH->where('pavilhao',$pavilhao);
//                $nacionalidades[$key]->qtdM->where('pavilhao',$pavilhao);
//            }
//            if(!is_null($ala)) {
//                $nacionalidades[$key]->qtdH->where('ala',$ala);
//                $nacionalidades[$key]->qtdM->where('ala',$ala);
//            }
//            if(!is_null($cela)) {
//                $nacionalidades[$key]->qtdH->where('cela',$cela);
//                $nacionalidades[$key]->qtdM->where('cela',$cela);
//            }
//
//            $nacionalidades[$key]->qtdH = $nacionalidades[$key]->qtdH->count();
//            $nacionalidades[$key]->qtdM = $nacionalidades[$key]->qtdM->count();
//            $nacionalidades[$key]->qtd = $nacionalidades[$key]->qtdH + $nacionalidades[$key]->qtdM;
//        }
//
//        $qtdNacionalidadeNIH = $qtdNacionalidadeNIH->count();
//        $qtdNacionalidadeNIM = $qtdNacionalidadeNIM->count();
//
//        $faccoes = DB::table('faccao')->select('id','nome','sigla')->get();
//        foreach($faccoes as $key=>$faccao){
//            $faccoes[$key]->qtdH = DB::table('interno')->where('sexo','M')->where('status','A')->where('faccao', $faccao->id);
//            $faccoes[$key]->qtdM = DB::table('interno')->where('sexo','F')->where('status','A')->where('faccao', $faccao->id);
//
//            if(!is_null($unidade)) {
//                $faccoes[$key]->qtdH->where('unidade_prisional',$unidade);
//                $faccoes[$key]->qtdM->where('unidade_prisional',$unidade);
//            }
//            if(!is_null($pavilhao)) {
//                $faccoes[$key]->qtdH->where('pavilhao',$pavilhao);
//                $faccoes[$key]->qtdM->where('pavilhao',$pavilhao);
//            }
//            if(!is_null($ala)) {
//                $faccoes[$key]->qtdH->where('ala',$ala);
//                $faccoes[$key]->qtdM->where('ala',$ala);
//            }
//            if(!is_null($cela)) {
//                $faccoes[$key]->qtdH->where('cela',$cela);
//                $faccoes[$key]->qtdM->where('cela',$cela);
//            }
//
//            $faccoes[$key]->qtdH = $faccoes[$key]->qtdH->count();
//            $faccoes[$key]->qtdM = $faccoes[$key]->qtdM->count();
//            $faccoes[$key]->qtd = $faccoes[$key]->qtdH + $faccoes[$key]->qtdM;
//        }
//
//        $faccoes = $faccoes->sortBy('qtd')->reverse();
//
//        $qtdUnidadeHA = $qtdUnidadeHA->get();
//        $qtdUnidadeMA = $qtdUnidadeMA->get();
//        $qtdUnidadeTotal = $qtdUnidadeTotal->get();
//
//        $qtdFaccaoNIH = $qtdFaccaoNIH->count();
//        $qtdFaccaoNIM = $qtdFaccaoNIM->count();
//
//        $qtdInternosLotSelecionada=$qtdInternosLotSelecionada->count();
//
//        $qtdRegimeNIH=$qtdRegimeNIH->count();
//        $qtdRegimeNIM=$qtdRegimeNIM->count();
//        $qtdRegimeFechadoH=$qtdRegimeFechadoH->count();
//        $qtdRegimeFechadoM=$qtdRegimeFechadoM->count();
//        $qtdRegimeSemiabertoH=$qtdRegimeSemiabertoH->count();
//        $qtdRegimeSemiabertoM=$qtdRegimeSemiabertoM->count();
//        $qtdRegimeAbertoH=$qtdRegimeAbertoH->count();
//        $qtdRegimeAbertoM=$qtdRegimeAbertoM->count();
//
//        $qtdSentenciadosNIH=$qtdSentenciadosNIH->count();
//        $qtdSentenciadosNIM=$qtdSentenciadosNIM->count();
//        $qtdSentenciadosH=$qtdSentenciadosH->count();
//        $qtdSentenciadosM=$qtdSentenciadosM->count();
//        $qtdProvisoriosH=$qtdProvisoriosH->count();
//        $qtdProvisoriosM=$qtdProvisoriosM->count();
//
//        $qtdSentenciadosMSNIH=$qtdSentenciadosMSNIH->count();
//        $qtdSentenciadosMSNIM=$qtdSentenciadosMSNIM->count();
//        $qtdSentenciadosMSH=$qtdSentenciadosMSH->count();
//        $qtdSentenciadosMSM=$qtdSentenciadosMSM->count();
//        $qtdProvisoriosMSH=$qtdProvisoriosMSH->count();
//        $qtdProvisoriosMSM=$qtdProvisoriosMSM->count();
//
//        $qtdMedidaDeSegurancaH=$qtdMedidaDeSegurancaH->count();
//        $qtdMedidaDeSegurancaM=$qtdMedidaDeSegurancaM->count();
//
//        $qtdHA=$qtdHA->count();
//        $qtdMA=$qtdMA->count();
//
//        $periculosidadeAltaH=$periculosidadeAltaH->count();
//        $periculosidadeMediaH=$periculosidadeMediaH->count();
//        $periculosidadeBaixaH=$periculosidadeBaixaH->count();
//        $periculosidadeNIH=$periculosidadeNIH->count();
//        $periculosidadeAltaM=$periculosidadeAltaM->count();
//        $periculosidadeMediaM=$periculosidadeMediaM->count();
//        $periculosidadeBaixaM=$periculosidadeBaixaM->count();
//        $periculosidadeNIM=$periculosidadeNIM->count();
//
//        $qtdLiderancaSimH=$qtdLiderancaSimH->count();
//        $qtdLiderancaNaoH=$qtdLiderancaNaoH->count();
//        $qtdLiderancaNIH=$qtdLiderancaNIH->count();
//        $qtdLiderancaSimM=$qtdLiderancaSimM->count();
//        $qtdLiderancaNaoM=$qtdLiderancaNaoM->count();
//        $qtdLiderancaNIM=$qtdLiderancaNIM->count();
//
//        //estado_civil
//        if($estadocivil) {
//            $qtdSolteirosH = $qtdSolteirosH->count();
//            $qtdSolteirosM = $qtdSolteirosM->count();
//            $qtdCasadosH = $qtdCasadosH->count();
//            $qtdCasadosM = $qtdCasadosM->count();
//            $qtdUniaoEstavelH = $qtdUniaoEstavelH->count();
//            $qtdUniaoEstavelM = $qtdUniaoEstavelM->count();
//            $qtdSeparadosH = $qtdSeparadosH->count();
//            $qtdSeparadosM = $qtdSeparadosM->count();
//            $qtdDivorciadosH = $qtdDivorciadosH->count();
//            $qtdDivorciadosM = $qtdDivorciadosM->count();
//            $qtdViuvosH = $qtdViuvosH->count();
//            $qtdViuvosM = $qtdViuvosM->count();
//            $qtdEstadoCivilNIH = $qtdEstadoCivilNIH->count();
//            $qtdEstadoCivilNIM = $qtdEstadoCivilNIM->count();
//        }else{
//            $qtdSolteirosH =
//            $qtdSolteirosM =
//            $qtdCasadosH =
//            $qtdCasadosM =
//            $qtdUniaoEstavelH =
//            $qtdUniaoEstavelM =
//            $qtdSeparadosH =
//            $qtdSeparadosM =
//            $qtdDivorciadosH =
//            $qtdDivorciadosM =
//            $qtdViuvosH =
//            $qtdViuvosM =
//            $qtdEstadoCivilNIH =
//            $qtdEstadoCivilNIM = 0;
//        }
//
//        //opcao_sexual
//        $qtdHeteroH = $qtdHeteroH->count();
//        $qtdHeteroM = $qtdHeteroM->count();
//        $qtdHomoH = $qtdHomoH->count();
//        $qtdHomoM = $qtdHomoM->count();
//        $qtdBiH = $qtdBiH->count();
//        $qtdBiM = $qtdBiM->count();
//        $qtdSexualidadeNaoDeclH = $qtdSexualidadeNaoDeclH->count();
//        $qtdSexualidadeNaoDeclM = $qtdSexualidadeNaoDeclM->count();
//        $qtdSexualidadeNIH = $qtdSexualidadeNIH->count();
//        $qtdSexualidadeNIM = $qtdSexualidadeNIM->count();
//
//        //etnia
//        $qtdEtniaBrancaH=$qtdEtniaBrancaH->count();
//        $qtdEtniaBrancaM=$qtdEtniaBrancaM->count();
//        $qtdEtniaIndígenaH=$qtdEtniaIndígenaH->count();
//        $qtdEtniaIndígenaM=$qtdEtniaIndígenaM->count();
//        $qtdEtniaPretaH=$qtdEtniaPretaH->count();
//        $qtdEtniaPretaM=$qtdEtniaPretaM->count();
//        $qtdEtniaPardaH=$qtdEtniaPardaH->count();
//        $qtdEtniaPardaM=$qtdEtniaPardaM->count();
//        $qtdEtniaAmarelaH=$qtdEtniaAmarelaH->count();
//        $qtdEtniaAmarelaM=$qtdEtniaAmarelaM->count();
//        $qtdEtniaNIH=$qtdEtniaNIH->count();
//        $qtdEtniaNIM=$qtdEtniaNIM->count();
//
//        // escolaridade
//        if($escolaridade) {
//            $qtdAnalfabetoH = $qtdAnalfabetoH->count();
//            $qtdAnalfabetoM = $qtdAnalfabetoM->count();
//            $qtdAlfabetizadoH = $qtdAlfabetizadoH->count();
//            $qtdAlfabetizadoM = $qtdAlfabetizadoM->count();
//            $qtdFundIncH = $qtdFundIncH->count();
//            $qtdFundIncM = $qtdFundIncM->count();
//            $qtdFundCompH = $qtdFundCompH->count();
//            $qtdFundCompM = $qtdFundCompM->count();
//            $qtdMedioIncompH = $qtdMedioIncompH->count();
//            $qtdMedioIncompM = $qtdMedioIncompM->count();
//            $qtdMedioCompH = $qtdMedioCompH->count();
//            $qtdMedioCompM = $qtdMedioCompM->count();
//            $qtdSuperiorIncompH = $qtdSuperiorIncompH->count();
//            $qtdSuperiorIncompM = $qtdSuperiorIncompM->count();
//            $qtdSuperiorCompH = $qtdSuperiorCompH->count();
//            $qtdSuperiorCompM = $qtdSuperiorCompM->count();
//            $qtdPosLatoSensuH = $qtdPosLatoSensuH->count();
//            $qtdPosLatoSensuM = $qtdPosLatoSensuM->count();
//            $qtdMestradoH = $qtdMestradoH->count();
//            $qtdMestradoM = $qtdMestradoM->count();
//            $qtdDoutoradoH = $qtdDoutoradoH->count();
//            $qtdDoutoradoM = $qtdDoutoradoM->count();
//            $qtdPosDoutoradoH = $qtdPosDoutoradoH->count();
//            $qtdPosDoutoradoM = $qtdPosDoutoradoM->count();
//            $qtdEscolaridadeNIH = $qtdEscolaridadeNIH->count();
//            $qtdEscolaridadeNIM = $qtdEscolaridadeNIM->count();
//        } else {
//            $qtdAnalfabetoH =
//            $qtdAnalfabetoM =
//            $qtdAlfabetizadoH =
//            $qtdAlfabetizadoM =
//            $qtdFundIncH =
//            $qtdFundIncM =
//            $qtdFundCompH =
//            $qtdFundCompM =
//            $qtdMedioIncompH =
//            $qtdMedioIncompM =
//            $qtdMedioCompH =
//            $qtdMedioCompM =
//            $qtdSuperiorIncompH =
//            $qtdSuperiorIncompM =
//            $qtdSuperiorCompH =
//            $qtdSuperiorCompM =
//            $qtdPosLatoSensuH =
//            $qtdPosLatoSensuM =
//            $qtdMestradoH =
//            $qtdMestradoM =
//            $qtdDoutoradoH =
//            $qtdDoutoradoM =
//            $qtdPosDoutoradoH =
//            $qtdPosDoutoradoM =
//            $qtdEscolaridadeNIH =
//            $qtdEscolaridadeNIM = 0;
//        }
//
//        if($religiao) {
//            $qtdAdventistaH = $qtdAdventistaH->count();
//            $qtdAdventistaM = $qtdAdventistaM->count();
//            $qtdBudistaH = $qtdBudistaH->count();
//            $qtdBudistaM = $qtdBudistaM->count();
//            $qtdCatolicaH = $qtdCatolicaH->count();
//            $qtdCatolicaM = $qtdCatolicaM->count();
//            $qtdEspiritaH = $qtdEspiritaH->count();
//            $qtdEspiritaM = $qtdEspiritaM->count();
//            $qtdEvangelicaH = $qtdEvangelicaH->count();
//            $qtdEvangelicaM = $qtdEvangelicaM->count();
//            $qtdJudeuH = $qtdJudeuH->count();
//            $qtdJudeuM = $qtdJudeuM->count();
//            $qtdMormonH = $qtdMormonH->count();
//            $qtdMormonM = $qtdMormonM->count();
//            $qtdMulcumanaH = $qtdMulcumanaH->count();
//            $qtdMulcumanaM = $qtdMulcumanaM->count();
//            $qtdProtestanteH = $qtdProtestanteH->count();
//            $qtdProtestanteM = $qtdProtestanteM->count();
//            $qtdAgnosticoH = $qtdAgnosticoH->count();
//            $qtdAgnosticoM = $qtdAgnosticoM->count();
//            $qtdAteuH = $qtdAteuH->count();
//            $qtdAteuM = $qtdAteuM->count();
//            $qtdSemReligiaoH = $qtdSemReligiaoH->count();
//            $qtdSemReligiaoM = $qtdSemReligiaoM->count();
//            $qtdOutrasH = $qtdOutrasH->count();
//            $qtdOutrasM = $qtdOutrasM->count();
//            $qtdReligiaoNIH = $qtdReligiaoNIH->count();
//            $qtdReligiaoNIM = $qtdReligiaoNIM->count();
//        } else {
//            $qtdAdventistaH =
//            $qtdAdventistaM =
//            $qtdBudistaH =
//            $qtdBudistaM =
//            $qtdCatolicaH =
//            $qtdCatolicaM =
//            $qtdEspiritaH =
//            $qtdEspiritaM =
//            $qtdEvangelicaH =
//            $qtdEvangelicaM =
//            $qtdJudeuH =
//            $qtdJudeuM =
//            $qtdMormonH =
//            $qtdMormonM =
//            $qtdMulcumanaH =
//            $qtdMulcumanaM =
//            $qtdProtestanteH =
//            $qtdProtestanteM =
//            $qtdAgnosticoH =
//            $qtdAgnosticoM =
//            $qtdAteuH =
//            $qtdAteuM =
//            $qtdSemReligiaoH =
//            $qtdSemReligiaoM =
//            $qtdOutrasH =
//            $qtdOutrasM =
//            $qtdReligiaoNIH =
//            $qtdReligiaoNIM = 0;
//        }
//
//        //saida da unidade
//        $qtdAlvara = $qtdAlvara->count();
//        $qtdFuga = $qtdFuga->count();
//        $qtdRecambiamento = $qtdRecambiamento->count();
//        $qtdSaidaJudicial = $qtdSaidaJudicial->count();
//        $qtdExtradicao = $qtdExtradicao->count();
//        $qtdLivramento = $qtdLivramento->count();
//        $qtdObito = $qtdObito->count();
//        $qtdExtincaopunibilidade = $qtdExtincaopunibilidade->count();
//
//        $dataHora = Tdata::TtimeStampHojeBrTxt();
//
//        $qntReingresso = $this->getReigressoUnidade(TData::TdataMysql($request->get('data')));
//
//        //$this->idadeVisita();
//
//
//
//        $pdf = \PDF::loadView('estatisticas.pdfs.pdfInterno',  compact('unidades', 'unidadeSigla', 'pavilhaoNome', 'alaNome', 'celaNome','faccoes','qtdFaccaoNIH','qtdFaccaoNIM',
//            'qtdInternosLotSelecionada','qtdRegimeFechadoH', 'qtdRegimeSemiabertoH', 'qtdRegimeAbertoH','qtdRegimeNIH', 'qtdRegimeNIM', 'dataHora',
//            'qtdRegimeFechadoM', 'qtdRegimeSemiabertoM', 'qtdRegimeAbertoM','qtdMedidaDeSegurancaH', 'qtdMedidaDeSegurancaM',
//            'qtdSentenciadosH','qtdProvisoriosH', 'qtdSentenciadosNIH', 'qtdSentenciadosM','qtdProvisoriosM', 'qtdSentenciadosNIM', 'qtdHA', 'qtdMA', 'qtdSexoNI',
//            'faixaEtaria_naoInformada','faixaEtaria_zero_a_dezessete','faixaEtaria_dezoito_a_vinteequatro','faixaEtaria_vinteecinco_a_vinteenove',
//            'faixaEtaria_trinta_a_trintaequatro','faixaEtaria_trintaecinco_a_quarentaecinco','faixaEtaria_quarentaeseis_a_sessenta',
//            'faixaEtaria_sessentaeum_a_setenta','faixaEtaria_maisDeSetenta',
//            'faixaEtaria_naoInformadaH','faixaEtaria_zero_a_dezesseteH','faixaEtaria_dezoito_a_vinteequatroH','faixaEtaria_vinteecinco_a_vinteenoveH',
//            'faixaEtaria_trinta_a_trintaequatroH','faixaEtaria_trintaecinco_a_quarentaecincoH','faixaEtaria_quarentaeseis_a_sessentaH',
//            'faixaEtaria_sessentaeum_a_setentaH','faixaEtaria_maisDeSetentaH',
//            'faixaEtaria_naoInformadaM','faixaEtaria_zero_a_dezesseteM','faixaEtaria_dezoito_a_vinteequatroM','faixaEtaria_vinteecinco_a_vinteenoveM',
//            'faixaEtaria_trinta_a_trintaequatroM','faixaEtaria_trintaecinco_a_quarentaecincoM','faixaEtaria_quarentaeseis_a_sessentaM',
//            'faixaEtaria_sessentaeum_a_setentaM','faixaEtaria_maisDeSetentaM','periculosidadeAltaH','periculosidadeMediaH','periculosidadeBaixaH','periculosidadeNIH',
//            'periculosidadeAltaM','periculosidadeMediaM','periculosidadeBaixaM','periculosidadeNIM','qtdLiderancaSimH','qtdLiderancaNaoH','qtdLiderancaNIH',
//            'qtdLiderancaSimM','qtdLiderancaNaoM','qtdLiderancaNIM', 'nacionalidades', 'qtdNacionalidadeNIH', 'qtdNacionalidadeNIM', 'qtdUnidadeHA', 'qtdUnidadeMA', 'qtdUnidadeTotal',
//
//            'qtdSentenciadosMSNIH',
//            'qtdSentenciadosMSNIM',
//            'qtdSentenciadosMSH',
//            'qtdSentenciadosMSM',
//            'qtdProvisoriosMSH',
//            'qtdProvisoriosMSM',
//
//            //Filtros
//            'internosunidade', 'regimeunidade', 'sexo', 'regimeprisional', 'sentprov_sem_ms', 'sentprov_em_ms', 'sentprov_geral', 'medidaseg', 'faixaetaria',
//            'nacionalidade_filtro', 'estadocivil', 'escolaridade', 'corpele', 'orientacaosexual', 'saidaunidade' , 'religiao', 'setic',
//
//            //estado_civil
//            'qtdSolteirosH','qtdSolteirosM','qtdCasadosH', 'qtdCasadosM', 'qtdUniaoEstavelH', 'qtdUniaoEstavelM', 'qtdSeparadosH', 'qtdSeparadosM',
//            'qtdDivorciadosH', 'qtdDivorciadosM', 'qtdViuvosH', 'qtdViuvosM', 'qtdEstadoCivilNIH', 'qtdEstadoCivilNIM',
//
//            //etnia
//            'qtdEtniaBrancaH', 'qtdEtniaBrancaM', 'qtdEtniaIndígenaH', 'qtdEtniaIndígenaM', 'qtdEtniaPretaH', 'qtdEtniaPretaM', 'qtdEtniaPardaH',
//            'qtdEtniaPardaM', 'qtdEtniaAmarelaH', 'qtdEtniaAmarelaM', 'qtdEtniaNIH', 'qtdEtniaNIM',
//
//            //opcao_sexual
//            'qtdHeteroH', 'qtdHeteroM', 'qtdHomoH', 'qtdHomoM', 'qtdBiH', 'qtdBiM', 'qtdSexualidadeNaoDeclH', 'qtdSexualidadeNaoDeclM',
//            'qtdSexualidadeNIH', 'qtdSexualidadeNIM',
//
//            // escolaridade
//            'qtdAnalfabetoH', 'qtdAnalfabetoM', 'qtdAlfabetizadoH', 'qtdAlfabetizadoM', 'qtdFundIncH', 'qtdFundIncM', 'qtdFundCompH', 'qtdFundCompM',
//            'qtdMedioIncompH', 'qtdMedioIncompM', 'qtdMedioCompH', 'qtdMedioCompM', 'qtdSuperiorIncompH', 'qtdSuperiorIncompM', 'qtdSuperiorCompH',
//            'qtdSuperiorCompM', 'qtdPosLatoSensuH', 'qtdPosLatoSensuM', 'qtdMestradoH', 'qtdMestradoM', 'qtdDoutoradoH', 'qtdDoutoradoM',
//            'qtdPosDoutoradoH', 'qtdPosDoutoradoM', 'qtdEscolaridadeNIH', 'qtdEscolaridadeNIM',
//
//            //religião
//            'qtdAdventistaH', 'qtdAdventistaM', 'qtdBudistaH', 'qtdBudistaM', 'qtdCatolicaH', 'qtdCatolicaM', 'qtdEspiritaH', 'qtdEspiritaM', 'qtdEvangelicaH',
//            'qtdEvangelicaM', 'qtdJudeuH', 'qtdJudeuM', 'qtdMormonH', 'qtdMormonM', 'qtdMulcumanaH', 'qtdMulcumanaM', 'qtdProtestanteH', 'qtdProtestanteM',
//            'qtdAgnosticoH', 'qtdAgnosticoM', 'qtdAteuH', 'qtdAteuM', 'qtdSemReligiaoH', 'qtdSemReligiaoM', 'qtdOutrasH', 'qtdOutrasM', 'qtdReligiaoNIH', 'qtdReligiaoNIM',
//
//            //saida da unidade
//            'qtdAlvara', 'qtdFuga', 'qtdRecambiamento', 'qtdSaidaJudicial', 'qtdExtradicao', 'qtdLivramento', 'qtdObito', 'qtdExtincaopunibilidade', 'qntReingresso', 'detalhesUnidades'
//
//        ))
//            ->setOption('encoding', 'UTF-8')
//            ->setOption('margin-bottom', 10)
//            ->setOption('margin-top', 30)
//            ->setOption('margin-left', 10)
//            ->setOption('margin-right', 10)
//            ->setOption('margin-bottom', 10)
//            ->setOption('header-html', view('template.header.headerEstatistica'))
//            ->setOption ('footer-center', utf8_decode('Coletado em'.date('\ d/m/Y\ ').' | Pagina [page] de [topage]'))
//            ->setOption('footer-font-size', 7)
//            ->setPaper('a4');
//        return $pdf->inline();
//
//    }

//}
