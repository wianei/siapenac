<?php


/*
|--------------------------------------------------------------------------
| Desenvolvimento: geovanni_ti@hotmail.com / saulo.ricardo_@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados às Unidades
|
*/

namespace App\Http\Controllers\Unidades;

use App\Classes\Tdata;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Permissoes\PermissoesController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;


class UnidadesController extends Controller
{
    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->middleware('auth');


    }

    public function callAction($method, $parameters)
    {
        LogController::logAcessoUser();
        $except = collect(["alterarLotacaoSiapen","alterarLotacaoSiapenStore"]);
        if(!$except->contains($method))
        {

            $retornoPermissao = PermissoesController::verifyPermission("UnidadesController", $method);
            if($retornoPermissao == false)
            {
                return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
            }
            
        }

        return parent::callAction($method, $parameters);
    }
    
    public static function getHorariosPlantaoAjax(Request $request) {
        
        $unidade = $request->get('unidade');
        
        $horarios = self::getHorariosPlantao($unidade);
        
        return $horarios;
        
    }
    
    
    /**
     * 
     * @param Request $request
     * @return array
     * 
     * calcula os horarios de livro de ocorrencia de acordo com a data e a unidade passada
     */
    public static function getHorariosPlantaoBuscaAjax(Request $request) {
        
        $unidade = $request->get('unidade');
        $dataLivro = $request->get('dataLivro');
        $qtdHorasPlantao = null;
        $horaInicioPlantao = null;
        
        $qtdHorasPlantaoDados = DB::table('unidade_historico')->where('fk_unidade', $unidade)->get();
        
       
        if(count($qtdHorasPlantaoDados) == 0)
        {
            $qtdHorasPlantao = self::getHorasPlantaoUnidade($unidade);
            $horaInicioPlantao = self::getHorarioInicioPlantaoUnidade($unidade);

        }
        
        if(count($qtdHorasPlantaoDados) >= 1)
        {
            $dadosHistoricoUnidade = DB::table('unidade_historico')
                    ->where('fk_unidade', $unidade)
                    ->where('data_modificacao', '<=', Tdata::TdataMysql($dataLivro))
                    ->orderBy('data_modificacao', 'DESC')
                    ->get();
                        
            $qtdHorasPlantao = $dadosHistoricoUnidade[0]->qtdHorasPlantao;
            
            $horaInicioPlantao = $dadosHistoricoUnidade[0]->horarioInicioPlantao;
        }
        

        $qtdFechamentos = 24 / $qtdHorasPlantao;
        
        $horarios = array();
        
        // se o plantao for de 24h
        if($qtdFechamentos == 1)
        {
            array_push($horarios, $horaInicioPlantao);  
        }
        
        // se o plantao for menor que 24h
        if($qtdFechamentos > 1)
        {
            $novoHorario = "";
            array_push($horarios, $horaInicioPlantao);
            
            for($i = 0; $i < $qtdFechamentos ; $i++)
            {
                $timestampHora = date("Y-m-d")." ".$horaInicioPlantao;
                $date = new DateTime($timestampHora);
                $date->add(new \DateInterval(("PT".$qtdHorasPlantao."H")));

                $novoHorario = Tdata::getTempoTimestamp($date->format('Y-m-d H:i:s'));;
              
                array_push($horarios, $novoHorario);
                
                $horaInicioPlantao = $novoHorario;
            }
            
            $horario = array_pop($horarios);
            
        }
        
        return $horarios;
        
    }
    
    /**
     * 
     * @param type $idUnidade
     * @return array
     * 
     * retorna um array com a lista de horarios da unidade
     */
    public static function getHorariosPlantao($idUnidade) {
     
        $qtdHorasPlantao = self::getHorasPlantaoUnidade($idUnidade);
        $horaInicioPlantao = self::getHorarioInicioPlantaoUnidade($idUnidade);
        $qtdFechamentos = 24 / $qtdHorasPlantao;
        
        $horarios = array();
        
        // se o plantao for de 24h
        if($qtdFechamentos == 1)
        {
            array_push($horarios, $horaInicioPlantao);  
        }
        
        // se o plantao for menor que 24h
        if($qtdFechamentos > 1)
        {
            $novoHorario = "";
            array_push($horarios, $horaInicioPlantao);
            
            for($i = 0; $i < $qtdFechamentos ; $i++)
            {
                $timestampHora = date("Y-m-d")." ".$horaInicioPlantao;
                $date = new DateTime($timestampHora);
                $date->add(new \DateInterval(("PT".$qtdHorasPlantao."H")));

                $novoHorario = Tdata::getTempoTimestamp($date->format('Y-m-d H:i:s'));;
              
                array_push($horarios, $novoHorario);
                
                $horaInicioPlantao = $novoHorario;
            }
            
            $horario = array_pop($horarios);
            
        }
        
        
        return $horarios;

    }
    
    public static function getDiasAtendimentoAdv($idUnidade)
    {
        $dado = DB::table("unidade")->where('id', $idUnidade)->get();

        return $dado[0]->diasSemAtendimentoAdv;

    }

    public static function getHorarioInicioAtendimentoAdv($idUnidade)
    {
        $dado = DB::table("unidade")->where('id', $idUnidade)->get();

        return $dado[0]->horarioInicioAtendimentoAdv;

    }

    public static function getHorarioTerminoAtendimentoAdv($idUnidade)
    {
        $dado = DB::table("unidade")->where('id', $idUnidade)->get();

        return $dado[0]->horarioTerminoAtendimentoAdv;

    }

    public static function getHorarioInicioIntervaloAlmoco($idUnidade)
    {
        $dado = DB::table("unidade")->where('id', $idUnidade)->get();

        return $dado[0]->horarioInicioIntervaloAlmoco;

    }

    public static function getHorarioTerminoIntervaloAlmoco($idUnidade)
    {
        $dado = DB::table("unidade")->where('id', $idUnidade)->get();

        return $dado[0]->horarioTerminoIntervaloAlmoco;

    }

    public static function getQtdParlatorioPavilhao($idPavilhao)
    {
        $dado = DB::table('pavilhao')->where('id',$idPavilhao)->get();

         return $dado[0]->qtdParlatorio;

    }

    public static function getQtdParlatorioUnidade($idUnidade)
    {
        $dado = DB::table('unidade')->where('id',$idUnidade)->get();

         return $dado[0]->qtdParlatorio;

    }
    
    /**
     * 
     * @param type $idUnidade
     * retorna a quantidade de livros de ocorrencia que uma unidade pode ter a cada 24h
     * baseado na quantidade de horas de cada plantão
     * 
     */
    public static function getQtdLivroUnidade($idUnidade)
    {
        $dado = DB::table('unidade')->where('id', $idUnidade)->get()[0]->qtdHorasPlantao;
        
        if(!is_null($dado))
        {
            $qtdLivro = 24 / $dado;
            
            return $qtdLivro;
            
        }else{
            
            return 1;
        }
    }
    
    public static function getHorasPlantaoUnidade($idUnidade) {
        $dados = DB::table('unidade')
                ->where('id', $idUnidade)
                ->get()[0]->qtdHorasPlantao;
        
        return $dados;
    }
       
    public static function getHorarioInicioPlantaoUnidade($idUnidade) {
        $dados = DB::table('unidade')
                ->where('id', $idUnidade)
                ->get()[0]->horaInicioPlantao;
        
        return $dados;
    }
       
    
    
    public function show()
    {
        LogController::logAcessoUser();

        $unidades = DB::table('unidade')
            ->where('status',1)
            ->get();
        $unidades = json_decode($unidades, true);
        foreach($unidades as $key=>$unidade){
            $unidades[$key]['diretor'] = FacadeController::getDiretorPorSigla($unidades[$key]['sigla']);
            $unidades[$key]['vicediretor'] = FacadeController::getViceDiretorPorSigla($unidades[$key]['sigla']);
        }
        return view('unidades.show', compact('unidades'));
    }

// --------------------------------------------

    public function getDados(Request $request)
    {
        $tipo = substr($request->get('dado'), 0, 2);
        $id = substr($request->get('dado'), 2);

        if($tipo == "pv")
        {
            $dado = DB::table('pavilhao')->where('fk_up',$id)->where('status','A')->orderBy('nome','ASC')->get();
        }
        if($tipo == "al")
        {
            $dado = DB::table('ala')->where('fk_pavilhao',$id)->where('status','A')->orderBy('nome','ASC')->get();
        }
        if($tipo == "ce")
        {
            $dado = DB::table('cela')->where('fk_ala',$id)->where('status','A')->orderBy('nome','ASC')->get();
        }

        $opcoes = array();

        foreach(json_decode($dado) as $d)
        {
            array_push($opcoes, array($d->nome => $d->id));
        }

        return json_encode($opcoes);

    }

    public function getDadosB(Request $request)
    {
        $tipo = substr($request->get('dado'), 0, 2);
        $id = substr($request->get('dado'), 2);
        if($tipo == "al")
        {
            $dado = DB::table('ala')->where('fk_pavilhao',$id)->where('status','A')->orderBy('nome','ASC')->get();
        }
        if($tipo == "ce")
        {
            $dado = DB::table('cela')->where('fk_ala',$id)->where('status','A')->orderBy('nome','ASC')->get();
        }

        $opcoes = array();

        foreach(json_decode($dado) as $d)
        {
            array_push($opcoes, array($d->nome => $d->id));
        }
        return json_encode($opcoes);
    }

// --------------------------------------------

    // retorna o Id da Unidade Prisional do interno cujo Id foi passado por parâmetro
    public static function getIdUP($idInterno)
    {
        if(is_null($idInterno)) return "";
        $idUp = DB::table('interno')->select('unidade_prisional')->where('id', $idInterno)->get();
        return $idUp[0]->unidade_prisional;
    }

    // retorna o Id do Pavilhão do interno cujo Id foi passado por parâmetro
    public static function getIdPavilhao($idInterno)
    {
        if(is_null($idInterno)) return "";
        $idPav = DB::table('interno')->select('pavilhao')->where('id', $idInterno)->get();
        return $idPav[0]->pavilhao;
    }

    // retorna o Id da Ala do interno cujo Id foi passado por parâmetro
    public static function getIdAla($idInterno)
    {
        if(is_null($idInterno)) return "";
        $idAla = DB::table('interno')->select('ala')->where('id', $idInterno)->get();
        return $idAla[0]->ala;
    }

    // retorna o Id da Cela do interno cujo Id foi passado por parâmetro
    public static function getIdCela($idInterno)
    {
        if(is_null($idInterno)) return "";
        $idCela = DB::table('interno')->select('cela')->where('id', $idInterno)->get();
        return $idCela[0]->cela;
    }


// --------------------------------------------


    // retorna o nome da Unidade Prisional cujo id foi passado por parâmetro
    public static function getNomeUp($idUp)
    {
        if(is_null($idUp) || $idUp == 0) return "";
        if($idUp == "judiciario") return "Judiciário";
        $nome = DB::table('unidade')->select('nome')->where('id', $idUp)->get();
        if(sizeof($nome) != 0){
            return $nome[0]->nome;
        } else return '';
    }

    public static function getCidadeUp($idUp)
    {
        if(is_null($idUp) || $idUp == 0) return "";
        if($idUp == "judiciario") return "Judiciário";
        $nome = DB::table('unidade')->select('cidade')->where('id', $idUp)->get();
        if(sizeof($nome) != 0){
            return $nome[0]->cidade;
        }
        else return '';
    }

    // retorna a sigla da Unidade Prisional cujo id foi passado por parâmetro
    public static function getSiglaUp($idUp)
    {
        if(is_null($idUp) || $idUp == 0) return "";
        $nome = DB::table('unidade')->select('sigla')->where('id', $idUp)->get();
        if(sizeof($nome) != 0){
            return $nome[0]->sigla;
        }
        else return '';
    }

    // retorna o nome do Pavilhão cujo id foi passado por parâmetro
    public static function getNomePav($idPav)
    {
        if(is_null($idPav) || $idPav == 0) return "";
        $nome = DB::table('pavilhao')->select('nome')->where('id', $idPav)->get();
        if(sizeof($nome) != 0){
            return $nome[0]->nome;
        } else return '';
    }

    // retorna o nome da Ala cujo id foi passado por parâmetro
    public static function getNomeAla($idAla)
    {
        if(is_null($idAla) || $idAla == 0) return "";
        $nome = DB::table('ala')->select('nome')->where('id', $idAla)->get();
        if(sizeof($nome) != 0){
            return $nome[0]->nome;
        } else return '';
    }

    // retorna o nome da Cela cujo id foi passado por parâmetro
    public static function getNomeCela($idCela)
    {
        if(is_null($idCela) || $idCela == 0) return "";
        $nome = DB::table('cela')->select('nome')->where('id', $idCela)->get();
        if(sizeof($nome) != 0){
            return $nome[0]->nome;
        } else return '';
    }

    // retorna o id da unidade quando passar a sigla por parametro
    public static function getIdUpPorSigla($sigla)
    {
        if(is_null($sigla)) return "";
        $unidade = DB::table('unidade')->select('id')->where('sigla', utf8_encode($sigla))->get();
        //CASO O USUÁRIO NÃO TENHA UMA UNIDADE
        if(sizeof($unidade) == 0){
            return null;
        }
        return $unidade[0]->id;
    }

    public static function getNomeUpPorSigla($sigla)
    {
        if(is_null($sigla)) return "";
        $unidade = DB::table('unidade')->select('nome')->where('sigla', $sigla)->get();
        if(sizeof($unidade) == 0){
            return null;
        }
        return $unidade[0]->nome;
    }

// ---------------------------------------------------

    // retorna a lista de id dos internos da cela
    public static function getInternosCela(Request $request)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $idCela = $request->get('cela');
        $interno = DB::table('interno');
        $restrito = $request->get('restrito');


        $interno->leftJoin('unidade', 'unidade.id', '=', 'interno.unidade_prisional');
        $interno->leftJoin('pavilhao', 'pavilhao.id', '=', 'interno.pavilhao');
        $interno->leftJoin('ala', 'ala.id', '=', 'interno.ala');
        $interno->leftJoin('cela', 'cela.id', '=', 'interno.cela');
        $interno->where('interno.status', 'A');

        $interno->leftJoin('faccao',function($query){
            $query->on('faccao.id','=','interno.faccao');
        });

        $interno->leftJoin('foto',function($query){
            $query->on('foto.fk_id','=','interno.id')->where('foto.fk_tabela','=', 'interno')->where('tipo','principal');
        });

        if(isset($restrito))
        {
            $interno->where('interno.unidade_prisional', $lotacaoServidor);
        }

        // passa a query para a paginacao "->appends..."
        $dados = $interno->select('interno.nome',"interno.id",'interno.faccao AS facId',DB::raw('IFNULL(faccao.nome,"Não informado") AS faccao'), DB::raw('(CASE WHEN interno.lideranca = "S" THEN "Sim" ELSE "Não" END) AS lideranca'),"interno.status","unidade.sigla","pavilhao.nome AS pvNome","ala.nome AS ala","cela.nome AS cela", DB::raw('(CASE WHEN foto.nome_arquivo is NULL THEN "/imagens/usuario/avatarInterno.png" ELSE CONCAT_WS("/", "/storage/app/arquivos/internos/fotos", interno.id, foto.nome_arquivo) END) AS foto'),DB::raw('IFNULL(foto.legenda,"Não informado") AS fotolegenda'),DB::raw('IFNULL(foto.tipo,"Não informado") AS fototipo'),DB::raw('IFNULL(foto.created_at,"Não informado") AS fotodatainserida'),DB::raw('IFNULL(foto.updated_at,"Não informado") AS fotodataalterada'))->orderBy('interno.nome','ASC')->where('interno.cela',$idCela)->get();

        return $dados;
    }

    public static function getPavilhoes($idUp){
        $pv = DB::table('pavilhao')->select('id','nome')->where('fk_up', $idUp)->where('status','A')->orderBy('nome', 'ASC')->get();
        $pv = json_decode($pv, true);
        return $pv;
    }

    public static function getAlas($idPav){
        $alas = DB::table('ala')->select('id','nome')->where('fk_pavilhao', $idPav)->where('status','A')->orderBy('nome', 'ASC')->get();
        $alas = json_decode($alas, true);
        return $alas;
    }

    public static function getCelas($idAla){
        $celas = DB::table('cela')->select('id','nome')->where('fk_ala', $idAla)->where('status','A')->orderBy('nome', 'ASC')->get();
        $celas = json_decode($celas, true);
        return $celas;
    }

    public static function getQuantitativoEstaticoOntem($fk_id, $tipo)
    {
        $ontem = Tdata::subDias(date("Y-m-d"), 1);

        $qtd = DB::table('unidade_quantitativo')->where('tipo', $tipo)->where('fk_id', $fk_id)->where('data', $ontem)->count();

       return $qtd;

    }

    public static function addQuantitativo($dataPlantao)
    {
        ini_set('max_execution_time', 300);

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $qtd = array();

            $qtdInternoUnidade = DB::table('interno')->where('status', 'A')->where('unidade_prisional', $lotacaoServidor)->count();

            $insertQtdUnidade = DB::table('unidade_quantitativo')->insert([
                'fk_referencia' => $lotacaoServidor,
                'tipo' => 'unidade',
                'fk_id' => $lotacaoServidor,
                'quantidade' => $qtdInternoUnidade,
                'data' => $dataPlantao,
                'dataTime' => Tdata::TtimeStampHojeMysql(),
            ]);

            $pavilhoes = DB::table('pavilhao')->where('fk_up', $lotacaoServidor)->where('status', 'A')->get();

            foreach($pavilhoes as $pavilhao)
            {
                $qtdInternoPavilhao = DB::table('interno')->where('status', 'A')->where('pavilhao', $pavilhao->id)->count();

                $insertQtdPavilhao = DB::table('unidade_quantitativo')->insert([
                    'fk_referencia' => $lotacaoServidor,
                    'tipo' => 'pavilhao',
                    'fk_id' => $pavilhao->id,
                    'quantidade' => $qtdInternoPavilhao,
                    'data' => $dataPlantao,
                    'dataTime' => Tdata::TtimeStampHojeMysql(),
                ]);

                $alas = DB::table('ala')->where('fk_pavilhao', $pavilhao->id)->where('status', 'A')->get();
                foreach ($alas as $ala) {

                    $qtdInternoAla = DB::table('interno')->where('status', 'A')->where('ala', $ala->id)->count();

                    $insertQtdAla = DB::table('unidade_quantitativo')->insert([
                        'fk_referencia' => $pavilhao->id,
                        'tipo' => 'ala',
                        'fk_id' => $ala->id,
                        'quantidade' => $qtdInternoAla,
                        'data' => $dataPlantao,
                        'dataTime' => Tdata::TtimeStampHojeMysql(),
                    ]);


                    $celas = DB::table('cela')->where('fk_ala', $ala->id)->where('status', 'A')->get();
                    foreach($celas as $cela)
                    {

                        $qtdInternoCela = DB::table('interno')->where('status', 'A')->where('cela', $cela->id)->count();

                        $insertQtdCela = DB::table('unidade_quantitativo')->insert([
                            'fk_referencia' => $ala->id,
                            'tipo' => 'cela',
                            'fk_id' => $cela->id,
                            'quantidade' => $qtdInternoCela,
                            'data' => $dataPlantao,
                            'dataTime' => Tdata::TtimeStampHojeMysql(),
                    ]);
                    }
                }


            }
    }

    public function alterarLotacaoSiapen()
    {
        LogController::logAcessoUser();

        if(Auth::user()->visualiza_outras_up != "S")
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

        $unidadesLiberadas = DB::table('users')->where('cpf', Auth::user()->cpf)->get()[0]->visualizacao_up_liberada;

        if(!is_null($unidadesLiberadas))
        {
            $unidades = array();
            $idUnidades = explode(',',$unidadesLiberadas);
            foreach($idUnidades as $idUnidade)
            {
                array_push($unidades, array(
                    'id' => $idUnidade,
                    'sigla' => self::getSiglaUp($idUnidade)
                ));
            }

            $unidades = json_decode(json_encode($unidades), FALSE);
        }else{
            $unidades = DB::table('unidade')->where('troca',1)->orderBy('sigla', 'ASC')->get();

        }

        return view('unidades.alterarLotacao', compact('unidades'));


    }

    public function alterarLotacaoSiapenStore(Request $request)
    {
        LogController::logAcessoUser();

        if(Auth::user()->visualiza_outras_up != "S")
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }


        $insert = DB::table('unidade_user_temp')->where('cpf', Auth::user()->cpf)->get();

        if(count($insert) > 0)
        {
            DB::table('unidade_user_temp')->where('cpf', Auth::user()->cpf)->update([
                'unidade' => $request->get('unidade')
            ]);
        }else{

            DB::table('unidade_user_temp')->insert([
                'unidade' => $request->get('unidade'),
                'cpf' => Auth::user()->cpf,
                'created_at' => Tdata::TtimeStampHojeMysql()
            ]);
        }

        return redirect()->route('inicio')->with('alert-success', 'Lotação alterada com sucesso!');

    }

    public function unidadeGerenciar(Request $request)
    {


//        if(Auth::user()->tipo < 700){
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem autorização para realizar essa operação!');
//        }
//

        $unidadeLogado = FacadeController::getLotacaoServidor(Auth::user()->cpf);
        $unidadeLogado = UnidadesController::getIdUpPorSigla($unidadeLogado);

        $unidades = DB::table('unidade');

        $unidades->where('unidade.id', $unidadeLogado);


        $dados = $unidades->select('unidade.*',DB::raw('(CASE WHEN unidade.status = "1" THEN "Ativo" ELSE "Inativo" END) AS status'))->orderBy('nome','ASC')->get();

        if($dados != false) {
            $dados = collect($dados)->paginate(50)->appends($request->input());
        }


        return view('unidades.minhaUnidade', compact('dados','unidades'));

    }

    public function celaGerenciar($idAla)
    {

//        if(Auth::user()->tipo < 700){
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem autorização para realizar essa operação!');
//        }

        $dados = DB::table('cela')
            ->where('fk_ala', $idAla)
            ->orderBy('nome', 'ASC')
            ->get();

        $nomeAla = UnidadesController::getNomeAla($idAla);

        return view('unidades.minhasCelas', compact('dados','nomeAla','idAla'));
    }

    public function celaEditar($idCela)
    {
//        if(Auth::user()->tipo < 700){
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem autorização para realizar essa operação!');
//        }
        $dados = DB::table('cela')->where('id', $idCela)->get();

        if($dados[0]->status != 'A'){
            return redirect()->route('unidade.unidadeGerenciar')->with('alert-danger', 'Não é permitido alterar uma cela inativa!');
        }

        return view('unidades.celaEditar', compact('idCela','dados'));

    }

    public function celaEditarStore(Request $request, $idCela)
    {
//        if(Auth::user()->tipo < 700){
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem autorização para realizar essa operação!');
//        }
        //-------------------- LOG -------------------------------------

        $status = $request->get('status');


        // LOG - array que armazena os textos do log
        $diferencas = array();
        $alteracoes = "Alteração de Dados da Cela<br>";
        array_push($diferencas, $alteracoes);

        $dataForm = array(
            'capacidade' => $request->get('capacidade'),
            'updated_at' => Tdata::TtimeStampHojeMysql(),
        );

        $dadosBD = DB::table("cela")->where('id', $idCela)->get();
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

                    $alteracoes = "Alteração de dados no campo " . ucfirst(str_replace("_", " ", $k)) . ": De ".json_decode(json_encode($dadosBD[0]), true)[$k] ." Para <i><strong>" . $dataForm[$k] . "</i></strong><br>";
                    array_push($diferencas, $alteracoes);
                }
            }

        }



        //-------------------- LOG -------------------------------------

        $insert = DB::table('cela')->where('id', $idCela)->update([
            'capacidade' => $request->get('capacidade'),
            'updated_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        if($idCela)
        {
            if(!empty($diferencas))
            {
                $textoLog = implode(" ", $diferencas);
                LogController::log($idCela,"cela","update", Auth::user()->cpf, $textoLog);
            }

            return redirect()->route('unidade.unidadeGerenciar')->with('alert-success', 'Dados Alterados com sucesso!');

        }else{

            return redirect()->route('unidade.unidadeGerenciar')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }

    }

    public function pavilhaoGerenciar($id)
    {
//        if(Auth::user()->tipo < 700){
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem autorização para realizar essa operação!');
//        }
        $dados = DB::table('pavilhao')->where('fk_up', $id)->orderBy('nome', 'ASC')->get();

        $nomeUnidade = UnidadesController::getNomeUp($id);

        return view('unidades.meusPavilhoes', compact('dados','nomeUnidade'));
    }

    public function pavilhaoEditar($id)
    {
//        if(Auth::user()->tipo < 700){
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem autorização para realizar essa operação!');
//        }
        $dados = DB::table('pavilhao')->where('id', $id)->get();

        if($dados[0]->status != 'A'){
            return redirect()->route('unidade.unidadeGerenciar')->with('alert-danger', 'Não é permitido alterar um pavilhão inativo!');
        }

        return view('unidades.pavilhaoEditar', compact('id','dados'));

    }

    public function pavilhaoEditarStore(Request $request, $id)
    {
//        if(Auth::user()->tipo < 700){
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem autorização para realizar essa operação!');
//        }
        //-------------------- LOG -------------------------------------

        // LOG - array que armazena os textos do log
        $diferencas = array();
        $alteracoes = "Alteração de Dados do Pavilhão<br>";
        array_push($diferencas, $alteracoes);

        $dataForm = array(
            'qtdParlatorio' => $request->get('qtdParlatorio'),
            'updated_at' => Tdata::TtimeStampHojeMysql(),
        );

        $dadosBD = DB::table("pavilhao")->where('id', $id)->get();
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

                    $alteracoes = "Alteração de dados no campo " . ucfirst(str_replace("_", " ", $k)) . ": De ".json_decode(json_encode($dadosBD[0]), true)[$k] ." Para <i><strong>" . $dataForm[$k] . "</i></strong><br>";
                    array_push($diferencas, $alteracoes);
                }
            }

        }



        //-------------------- LOG -------------------------------------

        $insert = DB::table('pavilhao')->where('id', $id)->update([
            'qtdParlatorio' => $request->get('qtdParlatorio'),
            'updated_at' => Tdata::TtimeStampHojeMysql(),
        ]);


        if($id)
        {
            if(!empty($diferencas))
            {
                $textoLog = implode(" ", $diferencas);
                LogController::log($id,"pavilhao","update", Auth::user()->cpf, $textoLog);
            }

            return redirect()->route('unidade.unidadeGerenciar')->with('alert-success', 'Dados Alterados com sucesso!');

        }else{

            return redirect()->route('unidade.unidadeGerenciar')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }

    }

    public function alaGerenciar($idPv)
    {
//        if(Auth::user()->tipo < 700){
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem autorização para realizar essa operação!');
//        }
        $dados = DB::table('ala')->where('fk_pavilhao', $idPv)->orderBy('nome', 'ASC')->get();

        $nomePavilhao = UnidadesController::getNomePav($idPv);

        return view('unidades.minhasAlas', compact('dados','nomePavilhao','idPv'));
    }


    // dado o id do interno, retorna seu status "A" ou "I", busca na tabela unificacao_interno
    public static function getStatusInternoUnificado($id){
        $status = DB::table('unificacao_interno')->where('id_original',$id)->first();
        if(isset($status)){
            return $status->status;
        }
        else{
            return "";
        }
    }




}