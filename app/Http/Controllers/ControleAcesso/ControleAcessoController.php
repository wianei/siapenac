<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados ao controle de acesso
|
*/

namespace App\Http\Controllers\ControleAcesso;

use App\Classes\Constantes;
use App\Classes\Tdata;
use App\Http\Controllers\Advogado\AdvogadoController;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Interno\InternoController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Http\Controllers\Visita\VisitaController;
use App\Http\Controllers\Ocorrencia\OcorrenciaController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Permissoes\PermissoesController;

class ControleAcessoController extends Controller
{

    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->middleware('auth');


    }

    public function callAction($method, $parameters)
    {

        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::verifyPermission("ControleAcessoController", $method);
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

//        $metodosAdm = collect(['suspenderEntrada','liberarEntrada','suspenderEntradaStore']);
//        $metodosComum = collect(['registrarEntrada','registrarEntradaStore','registrarSaida','registrarSaidaStore','editarCadastro','editarCadastroStore','historico','alertaEntrada','alertaEntradaStore']);
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
//
        return parent::callAction($method, $parameters);
    }

    public function buscaAjaxKeyUp(Request $request)
    {

        $nome = $request->get('nome');

        $dados = DB::table('controle_acesso_cadastro')
            ->select('controle_acesso_cadastro.id','controle_acesso_cadastro.nome','controle_acesso_cadastro.cpf')
            ->where('controle_acesso_cadastro.nome','LIKE', "%".$nome."%")
            ->orderBy('controle_acesso_cadastro.nome', 'ASC')
            ->get();


        return $dados;

    }

    public static function getCadastroVisitanteExterno($cpf)
    {
        $dados = DB::table('controle_acesso_cadastro')->where('cpf', $cpf)->get();

        return $dados;
    }
    public static function getDadosVisitanteExterno($id)
    {
        $dados = DB::table('controle_acesso_cadastro')->where('id', $id)->get();

        return $dados;
    }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * requisicoes via ajax
     */

    public function getHorarioSaidaAberto(Request $request)
    {
        $cpf = $request->get('cpf');
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $horariosAbertos = DB::table('controle_acesso')
            ->where('cpf', $cpf)
            ->where('unidade', $lotacaoServidor)
            ->where('horario_saida','=',null)
            ->get();

        $horarios = array();

        foreach ($horariosAbertos as $horariosAberto) {
            array_push($horarios, Tdata::TdataBR($horariosAberto->data)." - ".$horariosAberto->horario);
        }
        return $horarios;
    }

    public function getFotoCadastroExterno($cpf)
    {
        $dados = DB::table('controle_acesso_cadastro')->where('cpf', $cpf)->get();

        $foto = DB::table('foto')->where('fk_id', $dados[0]->id)->where('fk_tabela','controle_acesso_cadastro')->where('tipo','principal')->orderBy('created_at','DESC')->get();

        if(json_decode($foto, true) == [])
        {
            return asset("imagens/usuario/avatarInterno.png");
        }else{
            return asset("storage/app/arquivos/controle_acesso/fotos/".$dados[0]->id."/".$foto[0]->nome_arquivo);
        }

    }

    public function buscarCpfAjax(Request $request)
    {
        $cpf = $request->get('cpf');

        /**
         * depois buscar na tabela de cadastro do controle de acesso
         */
        $buscaVisita = VisitaController::getDadosVisitaCpf($cpf);
        $buscaAdvogado = AdvogadoController::getDadosAdvCpf($cpf);
        $buscaServidor = FacadeController::getDadosServidor($cpf, "nome");
        $buscaInterno = InternoController::getDadosInternoCpf($cpf);
        $buscaVisitanteExterno = self::getCadastroVisitanteExterno($cpf);

        $dadosPessoa = array();


            if (!empty(json_decode($buscaVisita, true))) {
                $suspensao = self::getSuspensao($cpf);
                if($suspensao != false)
                {
                    array_push($dadosPessoa, array("suspensao"=>true, 'dataFinal'=> $suspensao[0]->data_final, 'motivacao'=> $suspensao[0]->motivacao, "foto"=>VisitaController::getFotoVisitaCPF($cpf), 'nomeEntrada' => $buscaVisita[0]->nome));
                }else {

                    array_push($dadosPessoa, array("status" => true, "tipo" => "visita", "foto"=>VisitaController::getFotoVisitaCPF($cpf), 'nomeEntrada' => $buscaVisita[0]->nome));

                    $alerta = self::getAlertaEntrada($cpf);
                    if($alerta != false)
                    {
                        array_push($dadosPessoa, array( 'alerta'=>$alerta[0]->texto, "tipo" => "", "foto"=>VisitaController::getFotoVisitaCPF($cpf), 'nomeEntrada' => $buscaVisita[0]->nome));
                    }else{
                        array_push($dadosPessoa, array( 'alerta'=>false, "tipo" => "", "foto"=>VisitaController::getFotoVisitaCPF($cpf), 'nomeEntrada' => $buscaVisita[0]->nome));
                    }


                }
            }

            if (!empty(json_decode($buscaAdvogado, true))) {
                $suspensao = self::getSuspensao($cpf);
                if($suspensao != false)
                {
                    array_push($dadosPessoa, array("suspensao"=>true, 'dataFinal'=> $suspensao[0]->data_final, 'motivacao'=> $suspensao[0]->motivacao,  "foto" => AdvogadoController::getFotoAdvCPF($cpf), 'nomeEntrada' => $buscaAdvogado[0]->nome));
                }else {

                    array_push($dadosPessoa, array("status" => true, "tipo" => "advogado", "foto" => AdvogadoController::getFotoAdvCPF($cpf), 'nomeEntrada' => $buscaAdvogado[0]->nome));

                    $alerta = self::getAlertaEntrada($cpf);
                    if($alerta != false)
                    {
                        array_push($dadosPessoa, array( 'alerta'=>$alerta[0]->texto, "tipo" => "", "foto" => AdvogadoController::getFotoAdvCPF($cpf), 'nomeEntrada' => $buscaAdvogado[0]->nome));
                    }else{
                        array_push($dadosPessoa, array( 'alerta'=>false, "tipo" => "",  "foto" => AdvogadoController::getFotoAdvCPF($cpf), 'nomeEntrada' => $buscaAdvogado[0]->nome));
                    }

                }
            }

            if (!empty(json_decode($buscaServidor, true))) {
                $suspensao = self::getSuspensao($cpf);
                if($suspensao != false)
                {
                    array_push($dadosPessoa, array("suspensao"=>true, 'dataFinal'=> $suspensao[0]->data_final, 'motivacao'=> $suspensao[0]->motivacao, "foto" => "", 'nomeEntrada' => $buscaServidor[0]->nome));
                }else {
                    array_push($dadosPessoa, array("status" => true, "tipo" => "servidor",  "foto" => "", 'nomeEntrada' => $buscaServidor[0]->nome));

                    $alerta = self::getAlertaEntrada($cpf);
                    if($alerta != false)
                    {
                        array_push($dadosPessoa, array( 'alerta'=>$alerta[0]->texto, "tipo" => "",  "foto" => "", 'nomeEntrada' => $buscaServidor[0]->nome));
                    }else{
                        array_push($dadosPessoa, array( 'alerta'=>false, "tipo" => "",  "foto" => "", 'nomeEntrada' => $buscaServidor[0]->nome));
                    }
                }
            }

            if (!empty(json_decode($buscaInterno, true))) {
                $suspensao = self::getSuspensao($cpf);

                if($suspensao != false)
                {
                    array_push($dadosPessoa, array("suspensao"=>true, 'dataFinal'=> $suspensao[0]->data_final, 'motivacao'=> $suspensao[0]->motivacao,  "foto" =>InternoController::getFotoInternoCPF($cpf), 'nomeEntrada' => $buscaInterno[0]->nome));
                }else {
                    array_push($dadosPessoa, array("status" => true, "tipo" => "interno", "foto" =>InternoController::getFotoInternoCPF($cpf), 'nomeEntrada' => $buscaInterno[0]->nome));

                    $alerta = self::getAlertaEntrada($cpf);
                    if($alerta != false)
                    {
                        array_push($dadosPessoa, array( 'alerta'=>$alerta[0]->texto, "tipo" => "", "foto" =>InternoController::getFotoInternoCPF($cpf), 'nomeEntrada' => $buscaInterno[0]->nome));
                    }else{
                        array_push($dadosPessoa, array( 'alerta'=>false, "tipo" => "",  "foto" =>InternoController::getFotoInternoCPF($cpf), 'nomeEntrada' => $buscaInterno[0]->nome));
                    }

                }
            }

            if (!empty(json_decode($buscaVisitanteExterno, true))) {
                $suspensao = self::getSuspensao($cpf);
                if($suspensao != false)
                {
                    array_push($dadosPessoa, array("suspensao"=>true, 'dataFinal'=> $suspensao[0]->data_final, 'motivacao'=> $suspensao[0]->motivacao, "foto" => self::getFotoCadastroExterno($cpf), 'nomeEntrada' => $buscaVisitanteExterno[0]->nome));
                }else{
                    array_push($dadosPessoa, array("suspensao"=>false,"status"=>true, "tipo" => "visitante externo", "foto" => self::getFotoCadastroExterno($cpf), 'nomeEntrada' => $buscaVisitanteExterno[0]->nome));

                    $alerta = self::getAlertaEntrada($cpf);
                    if($alerta != false)
                    {
                        array_push($dadosPessoa, array( 'alerta'=>$alerta[0]->texto, "tipo" => "", "foto" => self::getFotoCadastroExterno($cpf), 'nomeEntrada' => $buscaVisitanteExterno[0]->nome));
                    }else{
                        array_push($dadosPessoa, array( 'alerta'=>false, "tipo" => "", "foto" => self::getFotoCadastroExterno($cpf), 'nomeEntrada' => $buscaVisitanteExterno[0]->nome));
                    }

                }



            }

            if (empty($dadosPessoa)) {
                array_push($dadosPessoa, array("status"=>false, "tipo" => "false"));
            }



        return $dadosPessoa;

    }
    public function registrarEntrada()
    {
        return view('controleAcesso.registroEntrada.registrarEntrada');
    }
    public function registrarEntradaStore(Request $request)
    {
        //dd($request->all());

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        
        $dataInicioPlantao = OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor);
        $horaInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
        $horaTerminoPlantao = OcorrenciaController::getHorarioReferenciaProxFechamentoLivroUnidade($lotacaoServidor);

        $stringInfoPlantao = "O livro de ocorrência em aberto inicia no dia ". Tdata::TdataBR(OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor)) ." às ".OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor)." e vai até ". Tdata::TdataBR(OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor))." às ".OcorrenciaController::getHorarioReferenciaProxFechamentoLivroUnidade($lotacaoServidor);


        $tipoCadastro = $request->get('tipoCadastro');
        $tipoEntrada = $request->get('tipoEntrada');
        $dataEntrada = $request->get('dataEntrada');
        $horaEntrada = $request->get('horarioEntrada');


        if(Tdata::TdataMysql($dataEntrada)." ".$horaEntrada < $dataInicioPlantao." ".$horaInicioPlantao || Tdata::TdataMysql($dataEntrada)." ".$horaEntrada > $dataTerminoPlantao." ".$horaTerminoPlantao)
        {
            return redirect()->back()->withInput()->with('alert-danger', '<strong>ATENÇÃO! </strong>Você não pode inserir uma entrada para datas passadas ou futuras! '.$stringInfoPlantao);
        }


        if($tipoCadastro == "false") {

            $insertCadastro = DB::table('controle_acesso_cadastro')->insert([
                'nome' => $request->get('nome'),
                'cpf' => $request->get('cpf'),
                'rg_numero' => $request->get('rg'),
                'rg_orgao_expedidor' => $request->get('rg_orgao_expedidor'),
                'contato' => $request->get('contato'),
            ]);

            $idCadastro = DB::getPdo()->lastInsertId();

            LogController::log($idCadastro,"controle_acesso_cadastro","add", Auth::user()->cpf, "Criação de cadastro.");

            // -------------------- inserir as fotos --------------------------- //

            $imagens = $request->get('imagens');

            if(isset($imagens)){

                foreach($imagens as $images){

                    list($baseType, $image) = explode(';', $images);
                    list(, $image) = explode(',', $image);
                    $image = base64_decode($image);
                    $imageName = $idCadastro . "_vistante_externo_" . time() . "_" . '.'.'jpg';
                    $p = Storage::disk('local')->put(Constantes::ARQUIVOS_CONTROLE_ACESSO.'/fotos/'.$idCadastro.'/'. $imageName, $image, 'public');

                    $insertFoto = DB::table('foto')->insert(
                        ['fk_id' => $idCadastro,
                            'fk_tabela' => "controle_acesso_cadastro",
                            'nome_arquivo' => $imageName,
                            'tipo' => 'outros',
                            'created_at' => date("Y-m-d H:i:s")
                        ]
                    );

                    $idInsertFoto = DB::getPdo()->lastInsertId();

                    LogController::log($idInsertFoto,"foto","add", Auth::user()->cpf, "Inserção de foto no cadastro do controle de acesso.");

                }

            }

            // se enviar o arquivo
            if($request->file('inputFile')[0] != null) {


                $qtdFoto = count($request->file('inputFile'));

                for ($x = 0; $x < $qtdFoto; $x++) {

                    $destinationPath = Constantes::ARQUIVOS_CONTROLE_ACESSO . "/fotos/" . $idCadastro;

                    $name = $idCadastro . "_" . time() . "_" . $request->file('inputFile')[$x]->getClientOriginalName();

                    $request->file('inputFile')[$x]->storeAs($destinationPath, $name);

                    $insertFoto = DB::table('foto')->insert(
                        ['fk_id' => $idCadastro,
                            'fk_tabela' => "controle_acesso_cadastro",
                            'nome_arquivo' => $name,
                            'tipo' => 'principal',
                            'created_at' => date("Y-m-d H:i:s")
                        ]
                    );

                    $idInsertFotoArquivo = DB::getPdo()->lastInsertId();

                    LogController::log($idInsertFotoArquivo,"foto","add", Auth::user()->cpf, "Inserção de foto no cadastro do controle de acesso.");


                }
            }


            // -------------------- !! inserir as fotos --------------------------- //


            $insertAcesso = DB::table('controle_acesso')->insert([
                'unidade' => $lotacaoServidor,
                'tabela' => 'visitante externo',
                'cpf' => $request->get('cpf'),
                'motivo' => $request->get('motivo'),
                'data' => Tdata::TdataMysql($dataEntrada),
                'horario' => $request->get('horarioEntrada'),
                'created_at' => date("Y-m-d")
            ]);

            $insertAcesso = DB::getPdo()->lastInsertId();

            LogController::log($insertAcesso,"controle_acesso","add", Auth::user()->cpf, "Inserção de acesso na unidade.<br>Visitante externo (".$request->get('cpf')."");


        }else{

            $insertAcesso = DB::table('controle_acesso')->insert([
                'unidade' => $lotacaoServidor,
                'tabela' => $request->get('tipoEntrada'),
                'cpf' => $request->get('cpf'),
                'motivo' => $request->get('motivo'),
                'data' => Tdata::TdataMysql($dataEntrada),
                'horario' => $request->get('horarioEntrada'),
                'created_at' => date("Y-m-d")
            ]);

            $insertAcesso = DB::getPdo()->lastInsertId();

            LogController::log($insertAcesso,"controle_acesso","add", Auth::user()->cpf, "Inserção de acesso na unidade.<br>".$request->get('tipoEntrada')." (".$request->get('cpf')."");

            if($tipoEntrada == "visita")
            {
                $idVisita = VisitaController::getDadosVisitaCpf($request->get('cpf'));
                $internos = $request->get('checkInterno');

                if(isset($internos)) {

                    foreach ($internos as $interno) {

                        $insertVisitaInterno = DB::table('controle_acesso_visita_interno')->insert([
                            'unidade' => $lotacaoServidor,
                            'fk_visita' => $idVisita[0]->id,
                            'fk_interno' => $interno,
                            'data' => Tdata::TdataMysql($dataEntrada),
                            'horario' => $request->get('horarioEntrada'),
                        ]);

                        $idInsertVisitaInterno = DB::getPdo()->lastInsertId();

                        $dadosVisita = VisitaController::getDadosVisita($idVisita[0]->id);
                        $dadosInterno = InternoController::getInternoNome($interno);

                        LogController::log($idInsertVisitaInterno, "controle_acesso_visita_interno", "add", Auth::user()->cpf, "Acesso na unidade da visita: <i><strong>" . $dadosVisita[0]->nome . "</i></strong> para visitar o interno: <i><strong>" . $dadosInterno . "</i></strong><br>");


                    }
                }

            }


        }

        if ($insertAcesso) {

            return redirect()->route('controleAcesso.registrarEntrada')->with('alert-success', 'Entrada registrada com sucesso!');

        }



    }

    public function registrarSaidaVisita()
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        
        $timestampInicioPlantao = OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor)." ". OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
        $timestampTerminaPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor)." ". OcorrenciaController::getHorarioReferenciaProxFechamentoLivroUnidade($lotacaoServidor);
        
        
        $dados = DB::table('controle_acesso')
           // ->leftJoin('visita','visita.cpf','=','controle_acesso.cpf')
            ->where('controle_acesso.unidade', $lotacaoServidor)
            ->where('controle_acesso.tabela', 'visita')
            ->whereNull('controle_acesso.data_saida')
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '>=', $timestampInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '<=', $timestampTerminaPlantao)

                //->where('controle_acesso.data', Tdata::TdataHojeMysql())
            
                ->select('controle_acesso.*','controle_acesso.id AS idControleAcesso')
           // ->orderBy('visita.nome', 'ASC')
//            ->orderBy('controle_acesso.data', 'DESC')
//            ->orderBy('controle_acesso.horario', 'DESC')
            ->get();


        $historico = array();


        foreach ($dados as $dado) {

            if ($dado->tabela == "visita") {
                $dadosVisita =  VisitaController::getDadosVisitaCpf($dado->cpf);
                $nome = "";
                $id = 0;
                $contador = 0;
                foreach($dadosVisita as $dadoVisita){
                    if($contador == 0){
                        $nome = $dadoVisita->nome;
                        $id = $dadoVisita->id;
                    }
                    $contador++;
                }
                array_push($historico, array(
                    'nome' => $nome,
                    'id' => $id,
                    'idControleAcesso' => $dado->idControleAcesso,
                    'cpf' => $dado->cpf,
                    'dataUltimaEntrada' => Tdata::TdataBR($dado->data),
                    'horaUltimaEntrada' => $dado->horario,
                    'localizacao' => UnidadesController::getSiglaUp($dado->unidade),
                    'tipo' => $dado->tipo,
                    'tabela' => $dado->tabela,
                    'motivo' => $dado->motivo,

                ));
            }

            //$dadosUltimaEntrada = self::getUltimoRegistroEntradaControle($dado->cpf, $lotacaoServidor);





        }
        //dd($historico);

        $historico = collect($historico)->sortBy('nome');


        $dataInicioPlantao =        Tdata::TtimestampBR($timestampInicioPlantao);
        $dataTerminoPlantao =        Tdata::TtimestampBR($timestampTerminaPlantao);
        
        return view('controleAcesso.registroSaida.registrarSaidaVisita', compact('historico','dataInicioPlantao','dataTerminoPlantao'));

    }

    public function registrarSaidaVisitaStore(Request $request)
    {
        if(count($request->get('checkVisitas')) == 0){
            return redirect()->back()->with('alert-danger', '<strong>ATENÇÃO! </strong>Selecione pelo uma visita');
        }


        $visitas = $request->get('checkVisitas');
        $dataSaida = $request->get('dataSaida');
        $horarioSaida = $request->get('horarioSaida');

        if(isset($visitas))
        {
            foreach($visitas as $visita)
            {

                $insertAcesso = DB::table('controle_acesso')
                    ->where('id', $visita)
                    ->update([
                        'data_saida' => Tdata::TdataMysql($dataSaida),
                        'horario_saida' => $horarioSaida,
                        'update_at' => date("Y-m-d")
                    ]);

                LogController::log($visita,"controle_acesso","update", Auth::user()->cpf, "Sáida da unidade.<br>");


            }

            return redirect()->route('controleAcesso.historico')->with('alert-success', 'Dados inserirdos com sucesso!');

        }


    }

    public function registrarSaida()
    {
        return view('controleAcesso.registroSaida.registrarSaida');
    }

    public function registrarSaidaStore(Request $request)
    {
        //dd($request->all());

        $dataDarSaida = $request->get('dataDarSaida');
        $newDataDarSaida = explode("-",str_replace(" ","",$dataDarSaida));

        $dataSaida = $request->get('dataSaida');
        $horarioSaida = $request->get('horarioSaida');
        $cpf = $request->get('cpf');

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dadoEntrada = DB::table('controle_acesso')->where('data',Tdata::TdataMysql($newDataDarSaida[0]))
            ->where('horario', $newDataDarSaida[1])
            ->where('unidade', $lotacaoServidor)
            ->where('cpf', $cpf)
            ->get();


        $insertAcesso = DB::table('controle_acesso')->where('data',Tdata::TdataMysql($newDataDarSaida[0]))
            ->where('horario', $newDataDarSaida[1])
            ->where('unidade', $lotacaoServidor)
            ->where('cpf', $cpf)
            ->update([
            'data_saida' => Tdata::TdataMysql($dataSaida),
            'horario_saida' => $horarioSaida,
            'update_at' => date("Y-m-d")
        ]);

        LogController::log($dadoEntrada[0]->id,"controle_acesso","update", Auth::user()->cpf, "Sáida da unidade<br>");



        if ($insertAcesso) {

            return redirect()->route('controleAcesso.historico')->with('alert-success', 'Dados inserirdos com sucesso!');

        }


    }

    public function editarCadastro($id)
    {
        $dados = DB::table('controle_acesso_cadastro')->where('cpf', $id)->get();

        return view('controleAcesso.cadastro.editar', compact('dados'));

    }

    public function editarCadastroStore(Request $request, $id)
    {

        $dataForm = array('nome' => $request->get('nome'), 'rg_numero' => $request->get('rg'), 'rg_orgao_expedidor' => $request->get('rg_orgao_expedidor'), 'contato' => $request->get('contato'));

        // LOG - array que armazena os textos do log
        $diferencas = array();

        //-------------------- LOG -------------------------------------

        $dadosBD = DB::table("controle_acesso_cadastro")->where('cpf', $id)->get();
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

        //-------------------- !LOG -------------------------------------


        $updateCadastro = DB::table('controle_acesso_cadastro')->where('cpf', $id)->update([
            'nome' => $request->get('nome'),
            'rg_numero' => $request->get('rg'),
            'rg_orgao_expedidor' => $request->get('rg_orgao_expedidor'),
            'contato' => $request->get('contato'),
        ]);


        // -------------------- inserir as fotos --------------------------- //

        $dadosPessoa = self::getCadastroVisitanteExterno($id);
        $idUsuario = $dadosPessoa[0]->id;

        $imagens = $request->get('imagens');

        if(isset($imagens)){

            foreach($imagens as $images){

                list($baseType, $image) = explode(';', $images);
                list(, $image) = explode(',', $image);
                $image = base64_decode($image);
                $imageName = $id . "_vistante_externo_" . time() . "_" . '.'.'jpg';
                $p = Storage::disk('local')->put(Constantes::ARQUIVOS_CONTROLE_ACESSO.'/fotos/'.$idUsuario.'/'. $imageName, $image, 'public');

                $insertFoto = DB::table('foto')->insert(
                    ['fk_id' => $idUsuario,
                        'fk_tabela' => "controle_acesso_cadastro",
                        'nome_arquivo' => $imageName,
                        'tipo' => 'outros',
                        'created_at' => date("Y-m-d H:i:s")
                    ]
                );

                $alteracoes = "Inserção de foto no cadastro, arquivo: <i><strong>".$imageName."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }

        }

        // se enviar o arquivo
        if($request->file('inputFile')[0] != null) {


            $qtdFoto = count($request->file('inputFile'));

            for ($x = 0; $x < $qtdFoto; $x++) {

                $destinationPath = Constantes::ARQUIVOS_CONTROLE_ACESSO . "/fotos/" . $idUsuario;

                $name = $id . "_" . time() . "_" . $request->file('inputFile')[$x]->getClientOriginalName();

                $request->file('inputFile')[$x]->storeAs($destinationPath, $name);

                $insertFoto = DB::table('foto')->insert(
                    ['fk_id' => $idUsuario,
                        'fk_tabela' => "controle_acesso_cadastro",
                        'nome_arquivo' => $name,
                        'tipo' => 'principal',
                        'created_at' => date("Y-m-d H:i:s")
                    ]
                );

                $alteracoes = "Inserção de foto no cadastro, arquivo: <i><strong>".$name."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }
        }


        // -------------------- !! inserir as fotos --------------------------- //

        if ($dataForm) {

            $textoLog = implode(" ", $diferencas);
            LogController::log($dadosBD[0]->id,"controle_acesso_cadastro","update", Auth::user()->cpf, $textoLog);

            return redirect()->route('controleAcesso.historico')->with('alert-success', 'Dados alterados com sucesso!');

        }

    }

    public function gerenciamento()
    {
        return view('controleAcesso.gerenciamento.home');
    }

    public function historico(Request $request)
    {

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $unidades = DB::table('unidade')->where('status', '1')->orderBy('sigla', 'ASC')->get();

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        /**
         * se não realizar uma busca
         * - Evita que mostre todos os resultados sem realizar uma busca
         */
        if(!strpos($url,"?"))
        {

            $dados = DB::table('controle_acesso')->where('unidade', $lotacaoServidor)->orderBy('data', 'DESC')->orderBy('horario', 'DESC')->limit(100)->get();


            $historico = array();

            foreach ($dados as $dado) {
                
                if ($dado->tabela == "visita") {
                    $nome = VisitaController::getDadosVisitaCpf($dado->cpf);
                }
                if ($dado->tabela == "advogado") {
                    $nome = AdvogadoController::getDadosAdvCpf($dado->cpf);
                }
                if ($dado->tabela == "servidor") {
                    $nome = FacadeController::getDadosServidor($dado->cpf, "nome");
                }
                if ($dado->tabela == "interno") {
                    $nome = InternoController::getDadosInternoCpf($dado->cpf);
                }
                if ($dado->tabela == "visitante externo") {
                    $nome = self::getCadastroVisitanteExterno($dado->cpf);
                }
                //$dadosUltimaEntrada = self::getUltimoRegistroEntradaControle($dado->cpf, $lotacaoServidor);

                //dd($nome);
                
                if(collect([]) != $nome && $nome != "" && $nome != null)
                {
                    $nome = $nome[0]->nome;
                }
                else{ 
                    $nome = "";
                }

                $suspensaoGet = self::getSuspensao($dado->cpf);

                if($suspensaoGet != false)
                {
                    $suspensao = true;

                }else{

                    $suspensao = false;
                }

                $editavel = false;
                // dd($getFechamento);

                $dataInicioPlantao =  OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
                $dataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor);
                $horarioInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
                $horarioTerminoPlantao = OcorrenciaController::getHorarioFechamento59Seg($lotacaoServidor);
                
                
                //dd(Tdata::TdataMysql($dado->data)." ". $dado->horario . '  ' .$dataTerminoPlantao." 08:00");
                if((Tdata::TdataMysql($dado->data)." ". $dado->horario < $dataTerminoPlantao." ".$horarioTerminoPlantao) && (Tdata::TdataMysql($dado->data)." ". $dado->horario >= $dataInicioPlantao." ".$horarioInicioPlantao)){
                    $editavel = true;
                }

                array_push($historico, array(
                    'id' => $dado->id,
                    'nome' => $nome,
                    'cpf' => $dado->cpf,
                    'dataUltimaEntrada' => Tdata::TdataBR($dado->data),
                    'horaUltimaEntrada' => $dado->horario,
                    'dataUltimaSaida' => Tdata::TdataBR($dado->data_saida),
                    'horaUltimaSaida' => $dado->horario_saida,
                    'localizacao' => UnidadesController::getSiglaUp($dado->unidade),
                    'tipo' => $dado->tipo,
                    'tabela' => $dado->tabela,
                    'motivo' => $dado->motivo,
                    'suspensao' => $suspensao,
                    'editavel' => $editavel,
                ));
            }

            

        }else {

            //dd($request->all());

            $unidade = $request->get('presidio');
            $dataInicial = $request->get('dataInicial');
            $dataFinal = $request->get('dataFinal');
            $cpf = $request->get('cpf');
            $tipo = $request->get('tipo');

            $dados = DB::table('controle_acesso');
            $dados->where('unidade', $lotacaoServidor);
            if($dataInicial != null){
                $dados->where('data','>=', Tdata::TdataMysql($dataInicial));
            }
            if($dataFinal != null){
                $dados->where('data','<=', Tdata::TdataMysql($dataFinal));
            }
            if($cpf != null){
                $dados->where('cpf', $cpf);
            }
            if($tipo != null){
                $dados->where('tabela', $tipo);
            }
            if($unidade != null){
                $dados->where('unidade', $unidade);
            }
            $dados->orderBy('data', 'DESC');
            $dados->orderBy('horario', 'DESC');
            $dadosHistorico =$dados->limit(150)->get();

            $historico = array();
            $nome = '';

            
            
            foreach ($dadosHistorico as $dado) {
                if ($dado->tabela == "visita") {
                    $nome = VisitaController::getDadosVisitaCpf($dado->cpf);
                }
                if ($dado->tabela == "advogado") {
                    $nome = AdvogadoController::getDadosAdvCpf($dado->cpf);
                }
                if ($dado->tabela == "servidor") {
                    $nome = FacadeController::getDadosServidor($dado->cpf, "nome");
                }
                if ($dado->tabela == "interno") {
                    $nome = InternoController::getDadosInternoCpf($dado->cpf);
                }
                if ($dado->tabela == "visitante externo") {
                    $nome = self::getCadastroVisitanteExterno($dado->cpf);
                }
                //$dadosUltimaEntrada = self::getUltimoRegistroEntradaControle($dado->cpf, $lotacaoServidor);

                if($nome != ''){
                    //dd($nome);
                    if(collect([]) != $nome) $nome = $nome[0]->nome;
                }

                else $nome = "";

                $suspensaoGet = self::getSuspensao($dado->cpf);
                if($suspensaoGet != false)
                {
                    $suspensao = true;
                }else{
                    $suspensao = false;
                }

                $editavel = false;

                $dataInicioPlantao =  OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
                $dataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor);
                $horarioInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
                $horarioTerminoPlantao = OcorrenciaController::getHorarioFechamento59Seg($lotacaoServidor);
                
                if((Tdata::TdataMysql($dado->data)." ". $dado->horario < $dataTerminoPlantao." ".$horarioTerminoPlantao) && (Tdata::TdataMysql($dado->data)." ". $dado->horario >= $dataInicioPlantao." ".$horarioInicioPlantao)){
                    $editavel = true;
                }

                array_push($historico, array(
                    'id' => $dado->id,
                    'nome' => $nome,
                    'cpf' => $dado->cpf,
                    'dataUltimaEntrada' => Tdata::TdataBR($dado->data),
                    'horaUltimaEntrada' => $dado->horario,
                    'dataUltimaSaida' => Tdata::TdataBR($dado->data_saida),
                    'horaUltimaSaida' => $dado->horario_saida,
                    'localizacao' => UnidadesController::getSiglaUp($dado->unidade),
                    'tipo' => $dado->tipo,
                    'tabela' => $dado->tabela,
                    'motivo' => $dado->motivo,
                    'suspensao' => $suspensao,
                    'editavel' => $editavel,
                ));
            }


        }
        return view('controleAcesso.historico.historico', compact('unidades','historico'));
    }

    public function editarRegistrarSaida(Request $request,$id)
    {
        $registroSaida = DB::table('controle_acesso')->where('id', $id)->get()->first();
        $request->session()->put('id', $id);

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));
        // dd($getFechamento);

        $dataInicioPlantao =  OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor);
        $horarioInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
        $horarioTerminoPlantao = OcorrenciaController::getHorarioFechamento59Seg($lotacaoServidor);
        
        if((Tdata::TdataMysql($registroSaida->data)." ". $registroSaida->horario < Tdata::TdataMysql($dataTerminoPlantao)." ".$horarioTerminoPlantao) && (Tdata::TdataMysql($registroSaida->data)." ". $registroSaida->horario >= Tdata::TdataMysql($dataInicioPlantao)." ".$horarioInicioPlantao)){
            return view('controleAcesso.registroSaida.editarRegistrarSaida', compact('registroSaida'));
        }

        return redirect()->route('controleAcesso.historico')->with('alert-danger', 'Não é permitido realizar esse tipo de operação!');

    }

    public function editarRegistrarSaidaStore(Request $request){

        $id = $request->session()->get('id');

        $registroSaida = DB::table('controle_acesso')->where('id', $id);
        $horarioSaida = $request->get('horarioSaida');

        $insertAcesso = $registroSaida->update([
            'horario_saida' => $horarioSaida,
        ]);

        $request->session()->forget('id');


        LogController::log($id,"controle_acesso","update", Auth::user()->cpf, "Sáida da unidade" .$horarioSaida."<br>");




        return redirect()->route('controleAcesso.historico')->with('alert-success', 'Dados alterados com sucesso!');



    }

    public function suspenderEntrada($id)
    {
        return view('controleAcesso.suspenderEntrada.suspender', compact('id'));

    }

    public function liberarEntrada($id)
    {

        $insertLiberarEntrada = DB::table('controle_acesso_suspensos')
            ->where('cpf',$id)
            ->where('status','A')
            ->update([
            'status' => 'I',
            'update_at' => Tdata::TdataHojeMysql(),
        ]);

        if ($insertLiberarEntrada) {

            return redirect()->route('controleAcesso.historico')->with('alert-success', 'Entrada liberada com sucesso!');

        }


    }

    public function suspenderEntradaStore(Request $request, $id)
    {
        //dd($request->all());

        $dias = $request->get('dias');
        $motivacao = $request->get('motivo');

        $datahj = Tdata::TdataHojeMysql();

        $dataFinal = Tdata::addDias($datahj, $dias);

        $insertSuspensao = DB::table('controle_acesso_suspensos')->insert([
            'cpf' => $id,
            'motivacao' => $motivacao,
            'data_final' => $dataFinal,
            'status' => 'A',
            'created_at' => $datahj,
        ]);

        if ($insertSuspensao) {

            return redirect()->route('controleAcesso.historico')->with('alert-success', 'Suspensão inserida com sucesso!');

        }


    }

    public function alertaEntrada($id)
    {
        $alerta = true;
        return view('controleAcesso.suspenderEntrada.suspender', compact('id','alerta'));

    }

    public static function alertaEntradaStore(Request $request, $id)
    {
        $dias = $request->get('dias');
        $motivacao = $request->get('motivo');

        $datahj = Tdata::TdataHojeMysql();

        $dataFinal = Tdata::addDias($datahj, $dias);

        $insertAlerta = DB::table('controle_acesso_alerta')->insert([
            'cpf' => $id,
            'texto' => $motivacao,
            'data_final' => $dataFinal,
            'created_at' => $datahj,
        ]);

        if ($insertAlerta) {

            return redirect()->route('controleAcesso.historico')->with('alert-success', 'Suspensão inserida com sucesso!');

        }

    }

    public function alertaEntradaGeral()
    {
        return view('controleAcesso.alerta.formAlerta');
    }

    public function alertaEntradaGeralStore(Request $request)
    {
        //dd($request->input());
        $diferencas = array();

        $dias = $request->get('dias');
        $motivacao = $request->get('motivo');
        $tipo = $request->get('tipo');

        $datahj = Tdata::TdataHojeMysql();

        $dataFinal = Tdata::addDias($datahj, $dias);

        if($tipo == "visita")
        {
            $visitas = $request->get('visitas');

            foreach($visitas as $visita) {

                $dadosVisita = VisitaController::getDadosVisita($visita);

                if(!empty($dadosVisita[0]->cpf)){

                    $verificaAlertaAtivo = DB::table('controle_acesso_alerta')->where('cpf', $dadosVisita[0]->cpf)->where('data_final','>=', Tdata::TdataHojeMysql())->get();

                    if(count($verificaAlertaAtivo) > 0)
                    {
                        return redirect()->back()->withInput()->with('alert-danger', 'Já existe um alerta ativo para '.$dadosVisita[0]->nome.".");

                    }

                    $insertAlerta = DB::table('controle_acesso_alerta')->insert([
                        'cpf' => $dadosVisita[0]->cpf,
                        'tipo' => $tipo,
                        'texto' => $motivacao,
                        'data_final' => $dataFinal,
                        'created_at' => $datahj,
                    ]);

                    $idAlerta = DB::getPdo()->lastInsertId();

                    $alteracoes = "Criação de alerta<br><i><strong><br>Tipo: ".$tipo."<br>Nome: ".$dadosVisita[0]->nome." / CPF: ".$dadosVisita[0]->cpf."<br>Data de final: ".$dataFinal."</i></strong><br>";
                    array_push($diferencas, $alteracoes);


                }

            }
        }

        if($tipo == "advogado")
        {
            $advogados = $request->get('advogados');

            foreach($advogados as $advogado) {

                $dadosAdvogado = AdvogadoController::getDadosAdv($advogado);

                if(!empty($dadosAdvogado[0]->cpf)){

                    $insertAlerta = DB::table('controle_acesso_alerta')->insert([
                        'cpf' => $dadosAdvogado[0]->cpf,
                        'tipo' => $tipo,
                        'texto' => $motivacao,
                        'data_final' => $dataFinal,
                        'created_at' => $datahj,
                    ]);

                    $idAlerta = DB::getPdo()->lastInsertId();

                    $alteracoes = "Criação de alerta<br><i><strong><br>Tipo: ".$tipo."<br>Nome: ".$dadosAdvogado[0]->nome." / CPF: ".$dadosAdvogado[0]->cpf."<br>Data de final: ".$dataFinal."</i></strong><br>";
                    array_push($diferencas, $alteracoes);


                }

            }
        }

        if($tipo == "visitanteExterno")
        {
            $visitantesExternos = $request->get('visitantesExternos');

            foreach($visitantesExternos as $visitantesExterno) {

                $dadosVisitantesExternos = self::getDadosVisitanteExterno($visitantesExterno);

                if(!empty($dadosVisitantesExternos[0]->cpf)){

                    $insertAlerta = DB::table('controle_acesso_alerta')->insert([
                        'cpf' => $dadosVisitantesExternos[0]->cpf,
                        'tipo' => $tipo,
                        'texto' => $motivacao,
                        'data_final' => $dataFinal,
                        'created_at' => $datahj,
                    ]);

                    $idAlerta = DB::getPdo()->lastInsertId();

                    $alteracoes = "Criação de alerta<br><i><strong><br>Tipo: ".$tipo."<br>Nome: ".$dadosVisitantesExternos[0]->nome." / CPF: ".$dadosVisitantesExternos[0]->cpf."<br>Data de final: ".$dataFinal."</i></strong><br>";
                    array_push($diferencas, $alteracoes);


                }

            }
        }

        if ($tipo) {

            $textoLog = implode(" ", $diferencas);
            LogController::log($idAlerta,"controle_acesso_alerta","add", Auth::user()->cpf, $textoLog);


            return redirect()->route('controleAcesso.alertaEntradaGeralBuscar')->with('alert-success', 'Alerta inserido com sucesso!');

        }



    }

    public function alertaEntradaGeralBuscar(Request $request)
    {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        /**
         * se não realizar uma busca
         * - Evita que mostre todos os resultados sem realizar uma busca
         */
        if(!strpos($url,"?"))
        {

            $alertasDados = DB::table('controle_acesso_alerta')->orderBy('data_final','DESC')->get();



        }else {

            $tipoForm = $request->get('tipo');
            $cpf = $request->get('cpf');
            $dataInicial = $request->get('dataInicial');
            $dataFinal = $request->get('dataFinal');

            $alertasDados = DB::table('controle_acesso_alerta');

            if($tipoForm)
            {
                $alertasDados->where('tipo',$tipoForm);
            }
            if($cpf)
            {
                $alertasDados->where('cpf',$cpf);
            }
            if($dataInicial)
            {
                $alertasDados->where('created_at','>=', Tdata::TdataMysql($dataInicial));
            }
            if($dataFinal)
            {
                $alertasDados->where('data_final','<=', Tdata::TdataMysql($dataFinal));
            }
            $alertasDados->orderBy('data_final','DESC');
            $alertasDados = $alertasDados->get();


        }
        $alertas = array();

        foreach ($alertasDados as $alertasDado) {
            $nome = "";
            $tipo = "";
            if($alertasDado->tipo == "visita")
            {
                $dadosVisita = VisitaController::getDadosVisitaCpf($alertasDado->cpf);
                $nome = $dadosVisita[0]->nome;
                $tipo = "Visita";
            }

            if($alertasDado->tipo == "visitanteExterno")
            {
                $dadosVisitanteExterno = self::getCadastroVisitanteExterno($alertasDado->cpf);
                $nome = $dadosVisitanteExterno[0]->nome;
                $tipo = "Vis. Externo";
            }

            if($alertasDado->tipo == "advogado")
            {
                $dadosAdvogado = AdvogadoController::getDadosAdvCpf($alertasDado->cpf);
                $nome = $dadosAdvogado[0]->nome;
                $tipo = "Advogado";
            }

            if($alertasDado->data_final < Tdata::TdataHojeMysql())
            {
                $ativo = "N";
            }else{
                $ativo = "S";
            }
            array_push($alertas, array(
                'nome' => $nome,
                'id' => $alertasDado->id,
                'cpf' => $alertasDado->cpf,
                'dataInicial' => Tdata::TdataBR($alertasDado->created_at),
                'dataFinal' => Tdata::TdataBR($alertasDado->data_final),
                'qtdDias' => Tdata::subDatas($alertasDado->data_final, $alertasDado->created_at),
                'tipo' => $tipo,
                'ativo' => $ativo,
                'motivo' => $alertasDado->texto,
            ));
        }



        return view('controleAcesso.alerta.buscar', compact('alertas'));
    }

    public function alertaEntradaGeralCancelar($id)
    {
        return view('controleAcesso.alerta.cancelarAlerta', compact('id'));
    }
    public function alertaEntradaGeralCancelarStore(Request $request, $id)
    {
       // dd($id, $request->input());
        $dadosAlerta = DB::table('controle_acesso_alerta')->where('id', $id)->get();
        $dataOntem = Tdata::subDias(date("Y-m-d"),1);

        $textoCancelamento = $dadosAlerta[0]->texto." | Alerta cancelado em ".date("d/m/Y")." / motivação: ".$request->get('motivo');

        $update = DB::table('controle_acesso_alerta')->where('id', $id)->update([
            'data_final' => $dataOntem,
            'texto' => $textoCancelamento
        ]);

        if ($id) {

            LogController::log($id,"controle_acesso_alerta","update", Auth::user()->cpf, "Cancelamento de alerta no controle de acesso.");

            return redirect()->route('controleAcesso.alertaEntradaGeralBuscar')->with('alert-success', 'Alerta cancelado com sucesso!');

        }

    }
    public static function getUltimoRegistroEntradaControle($cpf, $unidade)
    {
        $dados = DB::table('controle_acesso')->where('cpf', $cpf)->where('tipo', 'E')->where('unidade', $unidade)->orderBy('data', 'DESC')->orderBy('horario','DESC')->first();

        return $dados;
    }

    public static function getSuspensao($cpf)
    {
        $dados = DB::table('controle_acesso_suspensos')->select("*", DB::raw("DATE_FORMAT(data_final,'%d/%m/%Y') as data_final"))->where('cpf',$cpf)->where('data_final','>=',Tdata::TdataHojeMysql())->where('status','A')->get();

        if (empty(json_decode($dados, true))) {

            return false;

        }else{
            return $dados;
        }
    }

    public static function getAlertaEntrada($cpf)
    {
        $dados = DB::table('controle_acesso_alerta')->select("*", DB::raw("DATE_FORMAT(data_final,'%d/%m/%Y') as data_final"))->where('cpf',$cpf)->where('data_final','>=',Tdata::TdataHojeMysql())->get();

        if (empty(json_decode($dados, true))) {
            return false;
        }else{
            return $dados;
        }

    }
    public static function getUltimoRegistroSaidaControle($cpf, $unidade)
    {

        $dados = DB::table('controle_acesso')
            ->where('cpf', $cpf)
            ->where('tipo', 'S')
           ->where('unidade', $unidade)
            ->orderBy('data', 'DESC')
            ->orderBy('horario','DESC')
            ->first();

        return $dados;
    }


}