<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com
|--------------------------------------------------------------------------
|
|
*/

namespace App\Http\Controllers\ArtigosProcessos;

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
use Illuminate\Support\Facades\Storage;
use App\Classes\Constantes;

class ArtigosProcessosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function callAction($method, $parameters)
    {
        LogController::logAcessoUser();
        
        $retornoPermissao = PermissoesController::verifyPermission("ArtigosProcessosController", $method);
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

//        $metodosAdm = collect(['cadastro','cadastroStore','editar','update','buscar','excluir']);
//        
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

    public function cadastro()
    {
        $unidadesFederativas = DB::table('unidades_federativas')->orderBy('nome', 'ASC')->get();

        $dispositivoLegal = DB::table('lei')->orderBy('nome','ASC')->get();

        return view("artigosProcessos.cadastro", compact('unidadesFederativas','dispositivoLegal'));
    }

    public function getArtigosLei(Request $request)
    {

        $artigo = DB::table("lei_natureza")
                ->where('fk_legislacao', $request->get('dispositivo'))
                ->where('ativo', 'S')
                ->where('tipo_natureza', 'T')
                ->orderBy('artigo', 'ASC')->get();

        return $artigo;
    }
    
    public function getDadosArtigosAjax(Request $request) {
        
        $id = $request->get('dispositivo');
        
        $artigo = DB::table("lei_natureza")
                ->where('id', $id)
                ->orderBy('artigo', 'ASC')->get();
        
        return $artigo;

    }

    public static function getGrupoArtigo($idNatureza) {
        
       
        $artigo = DB::table("lei_natureza")
                ->where('id', $idNatureza)->get();
        
        return $artigo[0]->fk_grupo;

    }
    
    public static function getNomeGrupo($idGrupo) {
        
        $dados = DB::table('lei_grupo')
                ->where('id', $idGrupo)
                ->get()[0];
        
        return $dados->nome;
        
    }
    
    public function getDispositivoSequencia(Request $request)
    {
        $dispositivoLegal = $request->get('dispositivo');

        $dispositivo = explode(",",$dispositivoLegal);
        $artigoNumero = $dispositivo[1];

        //dd($dispositivo);

        $paragrafo = DB::table('lei_paragrafo')->where('tb_alterada','lei_artigo')->where("id_tb_alterada", $dispositivo[0])->orderByRaw('LENGTH(numero)', 'ASC')->orderBy('numero', 'ASC')->get();

        //$inciso = DB::table('lei_inciso')->where('tb_alterada','lei_artigo')->where("id_tb_alterada", $dispositivo)->orderByRaw('LENGTH(numero)', 'ASC')->orderBy('numero', 'ASC')->get();

        $inciso = DB::table('lei_inciso')->where('tb_alterada','lei_artigo')->where("id_tb_alterada", $dispositivo[0])->orderByRaw('LENGTH(numero)', 'ASC')->orderBy('numero', 'ASC')->get();


        if(count($inciso) > 0)
            {
                $retorno = array("lei_inciso", $inciso);
                return $retorno;

            }else{
                $retorno = array("lei_paragrafo",$paragrafo);
                return $retorno;

            }

        }

    public function getDispositivoSequenciaDois(Request $request)
    {
        $id = $request->get('dispositivo');
        $tabela = $request->get('tipoSequencia');

        $alinea = DB::table('lei_alinea')->where('tb_alterada', $tabela)->where('id_tb_alterada', $id)->orderBy('letra_alinea', 'ASC')->get();

        $inciso = DB::table('lei_inciso')->where('tb_alterada', $tabela)->where('id_tb_alterada', $id)->orderBy('numero', 'ASC')->get();

        $paragrafo = DB::table('lei_paragrafo')->where('tb_alterada', $tabela)->where('id_tb_alterada', $id)->orderByRaw('LENGTH(numero)', 'ASC')->orderBy('numero', 'ASC')->get();

        if(count($alinea) > 0)
        {
            $dados = array("Alínea", $alinea,"lei_alinea");
            return $dados;
        }else{

            if(count($inciso) > 0)
            {
                $dados = array("Inciso", $inciso,"lei_inciso");
                return $dados;
            }else{

                if(count($paragrafo) > 0)
                {
                    $dados = array("Parágrafo", $paragrafo, "lei_paragrafo");
                    return $dados;
                }else{
                    return null;
                }

            }

        }
    }

    public function cadastroStore(Request $request)
    {
        //dd($request->all());

        // dispositivo legal
        $dispositivoLegal = $request->get('dispositivoLegal');
        $artigo = $request->get('artigo');
        $paragrafo = $request->get('paragrafo');
        $tipoSequenciaUm = $request->get('tipoSequenciaUm');
        $tipoSequenciaDois = $request->get('tipoSequenciaDois');
        $subDois = $request->get('subDois');

        if($artigo[0] == null || $artigo[0] == "")
        {
            return redirect()->back()->with('alert-danger', 'O artigo não pode ser em branco!');

        }
        
        $diferencas = array();

        $tipo = $request->get("tipo");
        $interno = $request->get('internos');

        // inquerito
        $inqueritoNumero = $request->get('numeroInquerito');
        $inqueritoData = $request->get('dataInquerito');
        $inqueritoDataFato = $request->get('dataFato');
        $inqueritoDataPrisao = $request->get('dataPrisao');
        $inqueritoUf = $request->get('ufInquerito');
        if(isset($inqueritoUf))
        {
            $inqueritoUnidadeFederativa = $inqueritoUf;
        }else{
            $inqueritoUnidadeFederativa = null;
        }
        $inqueritoCidade = $request->get('municipioInquerito');
        if(isset($inqueritoCidade))
        {
            $inqueritoMunicipio = $inqueritoCidade;
        }else{
            $inqueritoMunicipio = null;
        }

        $inqueritoDelegacia = $request->get('delegacia');
        $inqueritoMotivoPrisao = $request->get('motivoPrisao');

        // processo
        $processoNumero = $request->get('numeroProcesso');
        $processoData = $request->get('dataProcesso');
        $processoGuiaRecolhimento = $request->get('guiaRecolhimento');
        $processoMotivoPrisao = $request->get('motivoPrisaoProcesso');
        $processoEsferaJustica = $request->get('esferaJustica');
        $processoReu = $request->get('reu');
        $processoUf = $request->get('ufProcesso');
        $processoCidade = $request->get('municipioProcesso');
        if(isset($processoCidade))
        {
            $processoMunicipio = $processoCidade;
        }else{
            $processoMunicipio = null;
        }

        $processoVara = $request->get('varaProcesso');

        // condenacao
        $condenacaoNumeroProcesso = $request->get('numeroProcessoCondenacao');
        $condenacaoDataTransitoEmJulgado = $request->get('dataCondenacaoTransJulgado');
        $condenacaoDataSentenca = $request->get('dataCondenacaoSentenca');
        $condenacaoQuantidadeAnos = $request->get('condenacaoAnos');
        $condenacaoMeses = $request->get('condenacaoMeses');
        $condenacaoDias = $request->get('condenacaoDias');
        $condenacaoTipoPrisao = $request->get('tipoPrisao');


        //dd(count($dispositivoLegal));

        $insertProcessos = DB::table("interno_processos")->insert([
            "fk_interno" => $interno[0],
            "tipo" => $tipo,
            "status" => "A",
            "created_at" => Tdata::TtimeStampHojeMysql(),
        ]);
        $idInsertProcesso = DB::getPdo()->lastInsertId();

        if($tipo == "inqueritoPolicial")
        {
            $tipoTexto = "Inquérito Policial";

            $insertInquerito = DB::table("interno_processos_inquerito")->insert([
                "fk_processo" => $idInsertProcesso,
                "tipo" => $tipo,
                "numero" => $inqueritoNumero,
                "data_inquerito" => Tdata::TdataMysql($inqueritoData),
                "data_fato" => Tdata::TdataMysql($inqueritoDataFato),
                "data_prisao" => Tdata::TdataMysql($inqueritoDataPrisao),
                "uf" => $inqueritoUnidadeFederativa,
                "municipio" => $inqueritoMunicipio,
                "delegacia" => $inqueritoDelegacia,
                "motivo_prisao" => $inqueritoMotivoPrisao,
                "created_at" => Tdata::TtimeStampHojeMysql()
            ]);
        }

        if($tipo == "processo")
        {
            $tipoTexto = "Processo";

            $insertProcesso = DB::table("interno_processos_processo")->insert([
                "fk_processo" => $idInsertProcesso,
                "tipo" => $tipo,
                "numero" => $processoNumero,
                "data_processo" => Tdata::TdataMysql($processoData),
                "guia_recolhimento" => $processoGuiaRecolhimento,
                "motivo_prisao" => $processoMotivoPrisao,
                "esfera_judicial" => $processoEsferaJustica,
                "reu" => $processoReu,
                "uf" => $processoUf,
                "municipio" => $processoMunicipio,
                "vara" => $processoVara,
            ]);
        }

        if($tipo == "condenacao")
        {
            $tipoTexto = "Condenação";

            $insertCondenacao = DB::table("interno_processos_condenacao")->insert([
               "fk_processo" => $idInsertProcesso,
               "tipo" => $tipo,
               "numero" => $condenacaoNumeroProcesso,
               "data_transito_julgado" => Tdata::TdataMysql($condenacaoDataTransitoEmJulgado),
               "data_sentenca" => Tdata::TdataMysql($condenacaoDataSentenca),
               "quantidade_anos" => $condenacaoQuantidadeAnos,
               "quantidade_meses" => $condenacaoMeses,
               "quantidade_dias" => $condenacaoDias,
               "tipo_prisao" => $condenacaoTipoPrisao,
            ]);
        }


        $quantidadeDispositivoLegal = count($dispositivoLegal);

        for($i = 0; $i < $quantidadeDispositivoLegal; $i++)
        {
            $idArtigo = explode(",", $artigo[$i]);

            $insertDispositivoLegal = DB::table('interno_processos_dispositivo_legal')->insert([
               "id_processo" => $idInsertProcesso,
               "dispositivo_legal" => $dispositivoLegal[$i],
               "artigo" => $idArtigo[0],
               "fk_grupo" => self::getGrupoArtigo($idArtigo[0]),
               "seq_um_tipo" => $tipoSequenciaUm[$i],
               "seq_um" => $paragrafo[$i],
               "seq_dois_tipo" => $tipoSequenciaDois[$i],
               "seq_dois" => $subDois[$i],
            ]);

            if(!is_null($paragrafo[$i]))
            {
                $seqUm = ArtigosProcessosController::getSequencia($tipoSequenciaUm[$i], $paragrafo[$i]);
            }else{
                $seqUm = null;
            }
            if(!is_null($subDois[$i]))
            {
                $seqDois = ArtigosProcessosController::getSequencia($tipoSequenciaDois[$i], $subDois[$i]);
            }else{
                $seqDois = null;
            }

            $alteracoes = "Inserção do dispositivo legal: ".self::getNomeLei($dispositivoLegal[$i]).", Artigo: ".self::getNomeArtigo($idArtigo[0])." ".$seqUm." ".$seqDois;

            array_push($diferencas, $alteracoes);

        }

        if($idInsertProcesso)
        {
            $textoLog = implode(", ", $diferencas);
            if(!empty($textoLog))
            {
                LogController::log($idInsertProcesso,"interno_processos","add", Auth::user()->cpf, "Inclusão de ".$tipoTexto." para o interno: ".InternoController::getInternoNome($interno[0])." - ".$textoLog);

            }

            return redirect()->route('artigosProcessos.buscar')->with('alert-success', 'Cadastro realizado com sucesso!');

        }
    }

    public static function editar(Request $request, $id)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $processo = DB::table("interno_processos")->where("id", $id)->get();

        $unidadeInterno = InternoController::getIdUnidadeInterno($processo[0]->fk_interno);

        $municipios = null;

        if($unidadeInterno != $lotacaoServidor)
        {
            return redirect()->back()->with('alert-danger', 'O interno ".InternoController::getInternoNome($processo[0]->fk_interno." não pertence a sua unidade');

        }

        $dadosProcesso = array();

        foreach($processo as $item)
        {
            $dispositivoLegal = array();

            $tipo = "";

            $dispositivoLegalDados = DB::table("interno_processos_dispositivo_legal")->where("id_processo", $item->id)->get();

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
                    "id" => $lei->id,
                    "lei" => ArtigosProcessosController::getNomeLei($lei->dispositivo_legal),
                    "artigo" => ArtigosProcessosController::getNomeArtigo($lei->artigo),
                    "nome" => ArtigosProcessosController::getNomeNatureza($lei->artigo),
                    "glossario" => ArtigosProcessosController::getGlossarioNatureza($lei->artigo),
                    "sequenciaUm" => $seqUm,
                    "sequenciaDois" => $seqDois,
                ));
            }

            if($item->tipo == "inqueritoPolicial"){

                $tipo = "inqueritoPolicial";
                $dados = DB::table("interno_processos_inquerito")->where("fk_processo", $item->id)
                    ->select("interno_processos_inquerito.*",DB::raw("DATE_FORMAT(data_inquerito,'%d/%m/%Y') as dataInquerito"), DB::raw("DATE_FORMAT(data_fato,'%d/%m/%Y') as dataFato"), DB::raw("DATE_FORMAT(data_prisao,'%d/%m/%Y') as dataPrisao"))
                    ->get();

                $municipios = DB::table("municipio")->where("Uf", $dados[0]->uf)->get();
            }

            if($item->tipo == "processo"){

                $tipo = "processo";
                $dados = DB::table("interno_processos_processo")
                    ->where("fk_processo", $item->id)
                    ->select("interno_processos_processo.*",DB::raw("DATE_FORMAT(data_processo,'%d/%m/%Y') as dataProcesso"))
                    ->get();

                $municipios = DB::table("municipio")->where("Uf", $dados[0]->uf)->get();
            }

            if($item->tipo == "condenacao"){

                $tipo = "condenacao";
                $dados = DB::table("interno_processos_condenacao")
                    ->where("fk_processo", $item->id)
                    ->select("interno_processos_condenacao.*",DB::raw("DATE_FORMAT(data_transito_julgado,'%d/%m/%Y') as dataTransitoJulgado"),DB::raw("DATE_FORMAT(data_sentenca,'%d/%m/%Y') as dataSentenca"))
                    ->get();
            }

            if($tipo == "inqueritoPolicial")
            {
                $tipoNome = "Inquérito Policial";
            }
            if($tipo == "processo")
            {
                $tipoNome = "Processo";
            }
            if($tipo == "condenacao")
            {
                $tipoNome = "Condenação";
            }

            array_push($dadosProcesso, array(
                "idInterno" => $processo[0]->fk_interno,
                "nomeInterno" => InternoController::getInternoNome($processo[0]->fk_interno),
                "tipo" => $tipo,
                "tipoNome" => $tipoNome,
                "dadosProcesso" => json_decode($dados, true),
                "dispositivoLegal" => $dispositivoLegal,
            ));

        }

//        dd($dadosProcesso);
        $unidadesFederativas = DB::table('unidades_federativas')->orderBy('nome', 'ASC')->get();

        $dispositivoLegal = DB::table('lei')->orderBy('nome','ASC')->get();

        return view('artigosProcessos.editar', compact("dadosProcesso","unidadesFederativas","dispositivoLegal","municipios","id"));
    }

    public function update(Request $request, $id)
    {
        //dd($id, $request->all());

        $diferencas = array();


        $tipo = $request->get('tipoCadastro');
        $idProcesso = $request->get('idProcesso');

        // inquerito
        $inqueritoNumero = $request->get('numeroInquerito');
        $inqueritoData = $request->get('dataInquerito');
        $inqueritoDataFato = $request->get('dataFato');
        $inqueritoDataPrisao = $request->get('dataPrisao');
        $inqueritoUf = $request->get('ufInquerito');
        if(isset($inqueritoUf))
        {
            $inqueritoUnidadeFederativa = $inqueritoUf;
        }else{
            $inqueritoUnidadeFederativa = null;
        }
        $inqueritoCidade = $request->get('municipioInquerito');
        if(isset($inqueritoCidade))
        {
            $inqueritoMunicipio = $inqueritoCidade;
        }else{
            $inqueritoMunicipio = null;
        }

        $inqueritoDelegacia = $request->get('delegacia');
        $inqueritoMotivoPrisao = $request->get('motivoPrisao');

        // processo
        $processoNumero = $request->get('numeroProcesso');
        $processoData = $request->get('dataProcesso');
        $processoGuiaRecolhimento = $request->get('guiaRecolhimento');
        $processoMotivoPrisao = $request->get('motivoPrisaoProcesso');
        $processoEsferaJustica = $request->get('esferaJustica');
        $processoReu = $request->get('reu');
        $processoUf = $request->get('ufProcesso');
        $processoCidade = $request->get('municipioProcesso');
        if(isset($processoCidade))
        {
            $processoMunicipio = $processoCidade;
        }else{
            $processoMunicipio = null;
        }

        $processoVara = $request->get('varaProcesso');

        // condenacao
        $condenacaoNumeroProcesso = $request->get('numeroProcessoCondenacao');
        $condenacaoDataTransitoEmJulgado = $request->get('dataCondenacaoTransJulgado');
        $condenacaoDataSentenca = $request->get('dataCondenacaoSentenca');
        $condenacaoQuantidadeAnos = $request->get('condenacaoAnos');
        $condenacaoMeses = $request->get('condenacaoMeses');
        $condenacaoDias = $request->get('condenacaoDias');
        $condenacaoTipoPrisao = $request->get('tipoPrisao');

        // dispositivo legal
        $dispositivoLegal = $request->get('dispositivoLegal');
        $artigo = $request->get('artigo');
        $paragrafo = $request->get('paragrafo');
        $tipoSequenciaUm = $request->get('tipoSequenciaUm');
        $tipoSequenciaDois = $request->get('tipoSequenciaDois');
        $subDois = $request->get('subDois');

        //dd(count($dispositivoLegal));

        $insertProcessos = DB::table("interno_processos")->where("id", $id)
            ->update([
            "update_at" => Tdata::TtimeStampHojeMysql(),
        ]);

        if($tipo == "inqueritoPolicial")
        {
            $dataForm = array('numero'=> $inqueritoNumero, 'data_inquerito' => Tdata::TdataMysql($inqueritoData), 'data_fato' => Tdata::TdataMysql($inqueritoDataFato), 'data_prisao' => Tdata::TdataMysql($inqueritoDataPrisao), 'uf' => $inqueritoUnidadeFederativa, 'municipio' => $inqueritoMunicipio, 'delegacia' => $inqueritoDelegacia, 'motivo_prisao' => $inqueritoMotivoPrisao);

            $dadosBD = DB::table("interno_processos_inquerito")->where("id", $idProcesso)->get();
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

            $insertInquerito = DB::table("interno_processos_inquerito")
                ->where("id", $idProcesso)
                ->update([
                "numero" => $inqueritoNumero,
                "data_inquerito" => Tdata::TdataMysql($inqueritoData),
                "data_fato" => Tdata::TdataMysql($inqueritoDataFato),
                "data_prisao" => Tdata::TdataMysql($inqueritoDataPrisao),
                "uf" => $inqueritoUnidadeFederativa,
                "municipio" => $inqueritoMunicipio,
                "delegacia" => $inqueritoDelegacia,
                "motivo_prisao" => $inqueritoMotivoPrisao,
            ]);
        }

        if($tipo == "processo")
        {
            $dataForm = array('numero'=> $processoNumero, 'data_processo' => Tdata::TdataMysql($processoData), 'guia_recolhimento' => $processoGuiaRecolhimento, 'motivo_prisao' => $processoMotivoPrisao, 'esfera_judicial' => $processoEsferaJustica, 'reu' => $processoReu, 'uf' => $processoUf, 'municipio' => $processoMunicipio, 'vara' => $processoVara);

            $dadosBD = DB::table("interno_processos_processo")->where("id", $idProcesso)->get();
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

            $insertProcesso = DB::table("interno_processos_processo")
                ->where("id", $idProcesso)
                ->update([
                "numero" => $processoNumero,
                "data_processo" => Tdata::TdataMysql($processoData),
                "guia_recolhimento" => $processoGuiaRecolhimento,
                "motivo_prisao" => $processoMotivoPrisao,
                "esfera_judicial" => $processoEsferaJustica,
                "reu" => $processoReu,
                "uf" => $processoUf,
                "municipio" => $processoMunicipio,
                "vara" => $processoVara,
            ]);
        }

        if($tipo == "condenacao")
        {
            $dataForm = array('numero'=> $condenacaoNumeroProcesso, 'data_transito_julgado' => Tdata::TdataMysql($condenacaoDataTransitoEmJulgado), 'data_sentenca' => Tdata::TdataMysql($condenacaoDataSentenca), 'quantidade_anos' => $condenacaoQuantidadeAnos, 'quantidade_meses' => $condenacaoMeses, 'quantidade_dias' => $condenacaoDias, 'tipo_prisao' => $condenacaoTipoPrisao);

            $dadosBD = DB::table("interno_processos_condenacao")->where("id", $idProcesso)->get();
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

            $insertCondenacao = DB::table("interno_processos_condenacao")
                ->where("id", $idProcesso)
                ->update([
                "numero" => $condenacaoNumeroProcesso,
                "data_transito_julgado" => Tdata::TdataMysql($condenacaoDataTransitoEmJulgado),
                "data_sentenca" => Tdata::TdataMysql($condenacaoDataSentenca),
                "quantidade_anos" => $condenacaoQuantidadeAnos,
                "quantidade_meses" => $condenacaoMeses,
                "quantidade_dias" => $condenacaoDias,
                "tipo_prisao" => $condenacaoTipoPrisao,
            ]);
        }

        if(!is_null($dispositivoLegal[0])) {

            $quantidadeDispositivoLegal = count($dispositivoLegal);

            for ($i = 0; $i < $quantidadeDispositivoLegal; $i++) {

                if(!is_null($artigo[$i])) {

                    $idArtigo = explode(",", $artigo[$i]);

                    $insertDispositivoLegal = DB::table('interno_processos_dispositivo_legal')->insert([
                        "id_processo" => $id,
                        "dispositivo_legal" => $dispositivoLegal[$i],
                        "artigo" => $idArtigo[0],
                        "fk_grupo" => self::getGrupoArtigo($idArtigo[0]),
                        "seq_um_tipo" => $tipoSequenciaUm[$i],
                        "seq_um" => $paragrafo[$i],
                        "seq_dois_tipo" => $tipoSequenciaDois[$i],
                        "seq_dois" => $subDois[$i],
                    ]);

                    if (!is_null($paragrafo[$i])) {
                        $seqUm = ArtigosProcessosController::getSequencia($tipoSequenciaUm[$i], $paragrafo[$i]);
                    } else {
                        $seqUm = null;
                    }
                    if (!is_null($subDois[$i])) {
                        $seqDois = ArtigosProcessosController::getSequencia($tipoSequenciaDois[$i], $subDois[$i]);
                    } else {
                        $seqDois = null;
                    }

                    $alteracoes = "Inserção do dispositivo legal: " . self::getNomeLei($dispositivoLegal[$i]) . ", Artigo: " . self::getNomeArtigo($idArtigo[0]) . " " . $seqUm . " " . $seqDois;

                    array_push($diferencas, $alteracoes);
                }


            }
        }

        $leiRemover = $request->get('LeisRemover');
        if(isset($leiRemover))
        {
            foreach($leiRemover as $remover)
            {
                $dLegal = DB::table("interno_processos_dispositivo_legal")->where('id', $remover)->get();

                $alteracoes = "dispositivo legal id: ".self::getNomeLei($dLegal[0]->dispositivo_legal)." removido";
                array_push($diferencas, $alteracoes);
                
                DB::table("interno_processos_dispositivo_legal")->where('id', $remover)->delete();
            }
        }

        if($id)
        {
            $textoLog = implode(", ", $diferencas);
            if(!empty($textoLog))
            {
                LogController::log($idProcesso,"interno_processos","update", Auth::user()->cpf, $textoLog);

            }

            return redirect()->route('artigosProcessos.buscar')->with('alert-success', 'Cadastro realizado com sucesso!');

        }


    }

    public function buscar(Request $request)
    {
        //dd($request->all());
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dispositivoLegalLeis = DB::table('lei')->orderBy('nome','ASC')->get();
        $grupoCrimes = DB::table('lei_grupo')->orderBy('nome','ASC')->get();

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if(!strpos($url,"?"))
        {
            $dadosProcesso = false;

            $tipoDado = false;

            return view("artigosProcessos.buscar", compact("dispositivoLegalLeis","grupoCrimes", "dadosProcesso", "tipoDado"));

        }else {

            $internos = $request->get('internos');

            $lei = $request->get('dispositivoLegal');
            $artigo = $request->get('artigo');
            $paragrafo = $request->get('paragrafo');
            $tipoSequenciaUm = $request->get('tipoSequenciaUm');
            $tipoSequenciaDois = $request->get('tipoSequenciaDois');
            $subDois = $request->get('subDois');
            $grupoPost = $request->get('grupoCrime');

            
            
            if (!is_null($grupoPost)) {

                $dadosLei = DB::table("interno_processos_dispositivo_legal")->where('fk_grupo', $grupoPost);
                $dadosLei->leftJoin("interno_processos", "interno_processos.id", "=", "interno_processos_dispositivo_legal.id_processo");
                $dadosLei->leftJoin("interno", "interno.id", "=", "interno_processos.fk_interno");
                $dadosLei->where("interno.unidade_prisional", $lotacaoServidor);


                $dados = $dadosLei->select("interno_processos_dispositivo_legal.*", "interno_processos.fk_interno", "interno_processos.tipo", "interno.unidade_prisional")->get();

                $dados = $dados->unique('id_processo');
                
                $dadosInterno = array();


                foreach ($dados as $dado) {
                    if ($dado->tipo == "inqueritoPolicial") {
                        $idProcesso = DB::table("interno_processos_inquerito")->where("fk_processo", $dado->id_processo)->get();
                        $dadosTipo = "Inquérito Policial";
                    }

                    if ($dado->tipo == "processo") {
                        $idProcesso = DB::table("interno_processos_processo")->where("fk_processo", $dado->id_processo)->get();
                        $dadosTipo = "Processo";
                    }

                    if ($dado->tipo == "condenacao") {
                        $idProcesso = DB::table("interno_processos_condenacao")->where("fk_processo", $dado->id_processo)->get();
                        $dadosTipo = "Condenação";
                    }

                    array_push($dadosInterno, array(
                        "prontuario" => $dado->fk_interno,
                        "nomeInterno" => InternoController::getInternoNome($dado->fk_interno),
                        "localizacao" => InternoController::getLotacaoInterno($dado->fk_interno),
                        "idProcesso" => $dado->id_processo,
                        "tipo" => $dadosTipo,
                    ));
                }

                $tipoDado = "lei";
                
                $qtdInternosUnicos = collect($dadosInterno);
                $qtdInternosUnicos = count($qtdInternosUnicos->unique('prontuario'));

                return view("artigosProcessos.buscar", compact("dispositivoLegalLeis","grupoCrimes", "dadosInterno", "tipoDado","qtdInternosUnicos"));

            }

            if (!is_null($lei[0])) {

                $dadosLei = DB::table("interno_processos_dispositivo_legal")->where("dispositivo_legal", $lei[0]);
                $dadosLei->leftJoin("interno_processos", "interno_processos.id", "=", "interno_processos_dispositivo_legal.id_processo");
                $dadosLei->leftJoin("interno", "interno.id", "=", "interno_processos.fk_interno");
                $dadosLei->where("interno.unidade_prisional", $lotacaoServidor);

                if (isset($artigo) && !is_null($artigo[0])) {
                    $dadosLei->where("artigo", $artigo[0]);
                }

                if (isset($paragrafo) && !is_null($paragrafo[0])) {
                    $dadosLei->where("seq_um_tipo", $tipoSequenciaUm[0])->where("seq_um", $paragrafo[0]);
                }

                if (isset($subDois) && !is_null($subDois[0])) {
                    $dadosLei->where("seq_dois_tipo", $tipoSequenciaDois[0])->where("seq_dois", $subDois[0]);
                }

                $dados = $dadosLei->select("interno_processos_dispositivo_legal.*", "interno_processos.fk_interno", "interno_processos.tipo", "interno.unidade_prisional")->get();

                $dados = $dados->unique('id_processo');
                
                $dadosInterno = array();


                foreach ($dados as $dado) {
                    if ($dado->tipo == "inqueritoPolicial") {
                        $idProcesso = DB::table("interno_processos_inquerito")->where("fk_processo", $dado->id_processo)->get();
                        $dadosTipo = "Inquérito Policial";
                    }

                    if ($dado->tipo == "processo") {
                        $idProcesso = DB::table("interno_processos_processo")->where("fk_processo", $dado->id_processo)->get();
                        $dadosTipo = "Processo";
                    }

                    if ($dado->tipo == "condenacao") {
                        $idProcesso = DB::table("interno_processos_condenacao")->where("fk_processo", $dado->id_processo)->get();
                        $dadosTipo = "Condenação";
                    }

                    array_push($dadosInterno, array(
                        "prontuario" => $dado->fk_interno,
                        "nomeInterno" => InternoController::getInternoNome($dado->fk_interno),
                        "localizacao" => InternoController::getLotacaoInterno($dado->fk_interno),
                        "idProcesso" => $dado->id_processo,
                        "tipo" => $dadosTipo,
                    ));
                }

                $tipoDado = "lei";
                
                $qtdInternosUnicos = collect($dadosInterno);
                $qtdInternosUnicos = count($qtdInternosUnicos->unique('prontuario'));

                return view("artigosProcessos.buscar", compact("dispositivoLegalLeis","grupoCrimes", "dadosInterno", "tipoDado","qtdInternosUnicos"));

            } else {

                if (is_null($internos) && is_null($lei[0])) {
                    return redirect()->route('artigosProcessos.buscar');
                }


                $unidadeInterno = InternoController::getIdUnidadeInterno($internos[0]);


                if ($unidadeInterno != $lotacaoServidor) {
                    return redirect()->route('artigosProcessos.buscar')->with('alert-danger', 'O interno não pertence a sua unidade!');
                }


                $processo = DB::table("interno_processos")->where("status", "A")->where('fk_interno', $internos[0])->get();

                $dadosProcesso = array();

                foreach ($processo as $item) {
                    $dispositivoLegal = array();

                    $tipo = "";

                    $dispositivoLegalDados = DB::table("interno_processos_dispositivo_legal")->where("id_processo", $item->id)->get();

                    foreach ($dispositivoLegalDados as $lei) {
                        if (!is_null($lei->seq_um)) {
                            $seqUm = ArtigosProcessosController::getSequencia($lei->seq_um_tipo, $lei->seq_um);
                        } else {
                            $seqUm = null;
                        }
                        if (!is_null($lei->seq_dois)) {
                            $seqDois = ArtigosProcessosController::getSequencia($lei->seq_dois_tipo, $lei->seq_dois);
                        } else {
                            $seqDois = null;
                        }
                        array_push($dispositivoLegal, array(
                            "id" => $lei->id,
                            "lei" => ArtigosProcessosController::getNomeLei($lei->dispositivo_legal),
                            "artigo" => ArtigosProcessosController::getNomeArtigo($lei->artigo),
                            "sequenciaUm" => $seqUm,
                            "sequenciaDois" => $seqDois,
                        ));
                    }

                    if ($item->tipo == "inqueritoPolicial") {

                        $tipo = "inqueritoPolicial";
                        $dados = DB::table("interno_processos_inquerito")->where("fk_processo", $item->id)
                            ->select("interno_processos_inquerito.*", DB::raw("DATE_FORMAT(data_inquerito,'%d/%m/%Y') as dataInquerito"), DB::raw("DATE_FORMAT(data_fato,'%d/%m/%Y') as dataFato"), DB::raw("DATE_FORMAT(data_prisao,'%d/%m/%Y') as dataPrisao"))
                            ->get();

                        $municipios = DB::table("municipio")->where("Uf", $dados[0]->uf)->get();
                    }

                    if ($item->tipo == "processo") {

                        $tipo = "processo";
                        $dados = DB::table("interno_processos_processo")
                            ->where("fk_processo", $item->id)
                            ->select("interno_processos_processo.*", DB::raw("DATE_FORMAT(data_processo,'%d/%m/%Y') as dataProcesso"))
                            ->get();

                        $municipios = DB::table("municipio")->where("Uf", $dados[0]->uf)->get();
                    }

                    if ($item->tipo == "condenacao") {

                        $tipo = "condenacao";
                        $dados = DB::table("interno_processos_condenacao")
                            ->where("fk_processo", $item->id)
                            ->select("interno_processos_condenacao.*", DB::raw("DATE_FORMAT(data_transito_julgado,'%d/%m/%Y') as dataTransitoJulgado"), DB::raw("DATE_FORMAT(data_sentenca,'%d/%m/%Y') as dataSentenca"))
                            ->get();
                    }

                    if ($tipo == "inqueritoPolicial") {
                        $tipoNome = "Inquérito Policial";
                    }
                    if ($tipo == "processo") {
                        $tipoNome = "Processo";
                    }
                    if ($tipo == "condenacao") {
                        $tipoNome = "Condenação";
                    }

                    array_push($dadosProcesso, array(
                        "idInterno" => $processo[0]->fk_interno,
                        "nomeInterno" => InternoController::getInternoNome($processo[0]->fk_interno),
                        "tipo" => $tipo,
                        "tipoNome" => $tipoNome,
                        "dadosProcesso" => json_decode($dados, true),
                        "dispositivoLegal" => $dispositivoLegal,
                    ));

                }


            }

//        dd($dadosProcesso);

            $tipoDado = "interno";

            
            return view("artigosProcessos.buscar", compact("dispositivoLegalLeis","grupoCrimes", "dadosProcesso", "tipoDado"));

        }
    }

    public function excluir($id)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $processo = DB::table("interno_processos")->where("id", $id)->get();

        $unidadeInterno = InternoController::getIdUnidadeInterno($processo[0]->fk_interno);


        if($unidadeInterno != $lotacaoServidor)
        {
            return redirect()->route('artigosProcessos.buscar')->with('alert-danger', 'O interno não pertence a sua unidade!');

        }

        $updateProcesso = DB::table("interno_processos")->where("id", $id)->update(["status"=>"I"]);

        if($updateProcesso)
        {
            return redirect()->route('artigosProcessos.buscar')->with('alert-success', 'Dado excluido com sucesso!');

        }

    }

    public static function getNomeLei($idLei)
    {
        $dados = DB::table("lei")->where("id", $idLei)->get()[0]->nome;

        return $dados;
    }

    public static function getNomeArtigo($idArtigo)
    {
        $dados = DB::table("lei_natureza")->where("id", $idArtigo)->get()[0];

        $retorno = $dados->artigo;

        return $retorno;
    }

    public static function getNomeNatureza($idArtigo)
    {
        $dados = DB::table("lei_natureza")->where("id", $idArtigo)->get()[0];

        $retorno = $dados->nome;

        return $retorno;
    }

    public static function getGlossarioNatureza($idArtigo)
    {
        $dados = DB::table("lei_natureza")->where("id", $idArtigo)->get()[0];

        $retorno = $dados->glossario;

        return $retorno;
    }

    public static function getSequencia($tabela, $id)
    {
        if($tabela == "lei_paragrafo")
        {
            $dados = DB::table("lei_paragrafo")->where("id", $id)->get()[0];

            return $dados->numero." - ".$dados->descricao;
        }

        if($tabela == "lei_inciso")
        {
            $dados = DB::table("lei_inciso")->where("id", $id)->get()[0];

            return $dados->numero." - ".$dados->descricao;
        }

        if($tabela == "lei_alinea")
        {
            $dados = DB::table("lei_alinea")->where("id", $id)->get()[0];

            return $dados->letra_alinea." - ".$dados->descricao;
        }
    }

}