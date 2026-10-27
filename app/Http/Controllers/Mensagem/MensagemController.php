<?php


/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados ao interno
|
*/

namespace App\Http\Controllers\Mensagem;

use App\Classes\Constantes;
use App\Classes\Tdata;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Http\Controllers\Permissoes\PermissoesController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MensagemController extends Controller
{

    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->middleware('auth');


    }

    public function callAction($method, $parameters)
    {
        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::verifyPermission("MensagemController", $method);
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }


//        if(($method) && $method != "getMensagensNaoLido")
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


    public static function getMensagensNaoLido()
    {
        if (PermissoesController::getTipoPermissao() == "I") {


            $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf, 'matricula');
            $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

            $mensagensLidas = DB::table('mensagem_destinatario_lido')->where('fk_destinatario', $matriculaServidorLogado[0]->matricula)->get();
            $mensagensLidasArray = array();
            foreach ($mensagensLidas as $mensagensLida) {
                array_push($mensagensLidasArray, ($mensagensLida->fk_mensagem));
            }

            $mensagens = array();

            $mensagensNaoLidas = DB::table('mensagem')
                ->leftJoin('mensagem_destinatarios', 'mensagem_destinatarios.fk_mensagem', '=', 'mensagem.id')
                ->where('mensagem_destinatarios.destinatario', $matriculaServidorLogado[0]->matricula)
                ->where('mensagem.remetente', '!=', $matriculaServidorLogado[0]->matricula)
                ->whereNotIn('mensagem.id', $mensagensLidasArray)
                ->select('*', 'mensagem.id as msgId')
                ->take(5)
                ->get();

            foreach ($mensagensNaoLidas as $mensagensNaoLida) {
                $dadosRemetente = FacadeController::getDadosServidorPorMat($mensagensNaoLida->remetente);

                if ($mensagensNaoLida->tipo == "unidade") {
                    $nomeDestinatario = UnidadesController::getNomeUp($mensagensNaoLida->destinatario);
                }
                if ($mensagensNaoLida->tipo == "servidor") {
                    $servidorDestinatario = FacadeController::getDadosServidorPorMat($mensagensNaoLida->destinatario);
                    $nomeDestinatario = "Você";
                }

                array_push($mensagens, array(
                    'id' => $mensagensNaoLida->msgId,
                    'remetente' => $dadosRemetente[0]->nome,
                    'destinatario' => $nomeDestinatario,
                    'hora' => $mensagensNaoLida->hora,
                    'data' => Tdata::TdataBR($mensagensNaoLida->created_at),
                    'assunto' => $mensagensNaoLida->assunto,
                    'tipo' => $mensagensNaoLida->tipo,
                ));

            }

            $mensagensNaoLidasUnidade = DB::table('mensagem')
                ->leftJoin('mensagem_destinatarios', 'mensagem_destinatarios.fk_mensagem', '=', 'mensagem.id')
                ->where('mensagem_destinatarios.tipo', 'unidade')
                ->where('mensagem_destinatarios.destinatario', $lotacaoServidor)
//            ->where('mensagem.remetente', '!=', $matriculaServidorLogado[0]->matricula)
                ->whereNotIn('mensagem.id', $mensagensLidasArray)
                ->select('*', 'mensagem.id as msgId')
                ->orderBy('mensagem.created_at','DESC')
                ->orderBy('mensagem.hora','DESC')
                ->take(5)
                ->get();

            foreach ($mensagensNaoLidasUnidade as $mensagensNaoLida) {
                $dadosRemetente = FacadeController::getDadosServidorPorMat($mensagensNaoLida->remetente);

                if ($mensagensNaoLida->tipo == "unidade") {
                    $nomeDestinatario = UnidadesController::getNomeUp($mensagensNaoLida->destinatario);
                }
                if ($mensagensNaoLida->tipo == "servidor") {
                    $servidorDestinatario = FacadeController::getDadosServidorPorMat($mensagensNaoLida->destinatario);
                    $nomeDestinatario = "Você";
                }

                array_push($mensagens, array(
                    'id' => $mensagensNaoLida->msgId,
                    'remetente' => $dadosRemetente[0]->nome,
                    'destinatario' => UnidadesController::getSiglaUp($mensagensNaoLida->destinatario),
                    'hora' => $mensagensNaoLida->hora,
                    'data' => Tdata::TdataBR($mensagensNaoLida->created_at),
                    'assunto' => $mensagensNaoLida->assunto,
                    'tipo' => $mensagensNaoLida->tipo,
                ));

            }
            return $mensagens;
        }else{
            return 0;
        }

    }

    public function ler($id)
    {
        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula');
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));


        $mensagem = DB::table('mensagem');
        $mensagem->leftJoin('mensagem_destinatarios','mensagem_destinatarios.fk_mensagem','=','mensagem.id');
        $mensagem->where('mensagem.id',$id);

        $verificaTipoMensagem = $mensagem->get();
        //dd($verificaTipoMensagem);

        $destinatarios = array();

        foreach($verificaTipoMensagem as $tipoMensagem) {

            array_push($destinatarios, $tipoMensagem->destinatario);

        }

        $mensagemDados = $mensagem->get();

        // verifica se está na lista de destinatario
        $verificaDestinatario = array_intersect(array($matriculaServidorLogado[0]->matricula,$lotacaoServidor),$destinatarios);

        if(empty(($verificaDestinatario)))
        {
            return redirect()->route('mensagem.recebidas')->with('alert-danger', 'Você não é destinatário dessa mensagem!');
        }


        $dadosMensagem = array();

        foreach($mensagemDados as $mensagemDado)
        {
            $dadosRemetente = FacadeController::getDadosServidorPorMat($mensagemDado->remetente);


            $arquivo = DB::table('mensagem_arquivo')->where('fk_mensagem',$id)->get();
            if(empty(json_decode($arquivo, true)))
            {
                $anexo = false;
            }else{

                $anexo = array();

                foreach($arquivo as $item)
                {
                    array_push($anexo, array(
                        'nome' => $item->nome_arquivo,
                        'fk_mensagem' => $item->fk_mensagem,
                    ));
                }
            }

            if($mensagemDado->tipo == "unidade")
            {
                $nomeDestinatario = UnidadesController::getNomeUp($mensagemDado->destinatario);
            }
            if($mensagemDado->tipo == "servidor")
            {
                $servidorDestinatario = FacadeController::getDadosServidorPorMat($mensagemDado->destinatario);
                $nomeDestinatario = $servidorDestinatario[0]->nome;
            }

            array_push($dadosMensagem,array(
                'id'=> $id,
                'remetente'=> $dadosRemetente[0]->nome,
                'destinatario'=> $nomeDestinatario,
                'hora'=> $mensagemDado->hora,
                'data'=> Tdata::TdataBR($mensagemDado->created_at),
                'assunto'=> $mensagemDado->assunto,
                'mensagem'=> $mensagemDado->mensagem,
                'anexo'=> $anexo,
                'tipo'=> $mensagemDado->tipo,
            ));
        }

        $leitura = DB::table('mensagem_destinatario_lido')->where('fk_mensagem',$id)->where('fk_destinatario', $matriculaServidorLogado[0]->matricula)->where('lido','S')->get();

        if(empty(json_decode($leitura, true)))
        {
            $marcarLida = DB::table('mensagem_destinatario_lido')->insert([
                    'fk_mensagem' => $id,
                    'fk_destinatario' => $matriculaServidorLogado[0]->matricula,
                    'lido' => 'S',
                    'hora' => date("H:i"),
                    'created_at' => date("Y-m-d"),
                ]
            );

        }

        LogController::log($id,"mensagem_destinatario_lido","add", Auth::user()->cpf, "Mensagem lida.<br>");


        return view('mensagem.ler.ler',compact('dadosMensagem'));
    }

    /**
     *
     */
    public function enviar()
    {
        $unidades = DB::table('unidade')->where('status','1')->orderBy('sigla', 'ASC')->get();

        return view('mensagem.enviar.formEnvio',compact('unidades'));
    }

    public function enviarStore(Request $request)
    {
        //dd($request->all());

        /**
         * validacoes
         */

        $destinatario = $request->get('destimatario');
        if(!isset($destinatario))
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Informe um ou mais destinatários!');

        }
        $assuntoValidar = $request->get('assunto');
        $descricaoValidar = $request->get('descricao');

        if($assuntoValidar == null || $descricaoValidar == null)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Preencha os campos obrigatórios!');

        }

        //--------------------- !validacoes ----------------------------------

        $diferencas = array();

        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula');

        $assunto = $request->get('assunto');
        $descricao = $request->get('descricao');
        $destinatarios = $request->get('destimatario');

        $insertMensagem = DB::table('mensagem')->insert([
                'remetente' => $matriculaServidorLogado[0]->matricula,
                'assunto' => $assunto,
                'mensagem' => $descricao,
                'created_at' => date("Y-m-d"),
                'hora' => date("H:i"),
            ]
        );

        $idMensagem = DB::getPdo()->lastInsertId();

        // LOG
        $alteracoes = "Criação de mensagem:<br>";
        array_push($diferencas, $alteracoes);


        foreach($destinatarios as $destinatario)
        {
            // se for para um servidor
            if(is_numeric($destinatario))
            {
                $insertDestinatario = DB::table('mensagem_destinatarios')->insert([
                        'fk_mensagem' => $idMensagem,
                        'tipo' => 'servidor',
                        'destinatario' => $destinatario,
                        'created_at' => date("Y-m-d"),
                        'hora' => date("H:i"),
                    ]
                );

                // LOG
                $dadosServidor = FacadeController::getDadosServidorPorMat($destinatario);
                $alteracoes = "Inserção de servidor como destinatario da mensagem: <i><strong>".$dadosServidor[0]->nome."(".$destinatario.")</i></strong><br>";
                array_push($diferencas, $alteracoes);


            }else{

                $unidade = explode(".",$destinatario);

                $insertDestinatario = DB::table('mensagem_destinatarios')->insert([
                        'fk_mensagem' => $idMensagem,
                        'tipo' => 'unidade',
                        'destinatario' => $unidade[1],
                        'created_at' => date("Y-m-d"),
                        'hora' => date("H:i"),
                    ]
                );

                $dadosUnidade = UnidadesController::getNomeUp($unidade[1]);
                $alteracoes = "Inserção de unidade como destinatario da mensagem: <i><strong>".$dadosUnidade."</i></strong><br>";
                array_push($diferencas, $alteracoes);


            }
        }

        // se enviar o arquivo
        if($request->file('inputFile')[0] != null) {

            $qtdArquivo = count($request->file('inputFile'));

            for ($x = 0; $x < $qtdArquivo; $x++) {

                $destinationPath = Constantes::ARQUIVOS_MENSAGEM . "/" . $idMensagem;

                $name = "mensagem".$idMensagem . "_" . time() . "_" . $request->file('inputFile')[$x]->getClientOriginalName();

                $request->file('inputFile')[$x]->storeAs($destinationPath, $name);

                $insertArquivo = DB::table('mensagem_arquivo')->insert(
                    ['fk_mensagem' => $idMensagem,
                        'nome_arquivo' => $name,
                        'created_at' => date("Y-m-d H:i:s")
                    ]
                );

                $alteracoes = "Inserção de arquivo como anexo da mensagem: <i><strong>".$name."</i></strong><br>";
                array_push($diferencas, $alteracoes);

            }
        }


        if ($insertDestinatario) {

            $textoLog = implode(" ", $diferencas);
            LogController::log($idMensagem,"mensagem","add", Auth::user()->cpf, $textoLog);

            return redirect()->route('mensagem.enviada')->with('alert-success', 'Mensagem enviada com sucesso!');

        }else{

            return redirect('mensagem.enviada')->with('alert-danger', 'Ocorreu um problema ao enviar a mensagem, por favor, tente novamente!');

        }


    }

    public function recebidas()
    {
        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula');
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $mensagensServidor = DB::table('mensagem_destinatarios')
            ->join('mensagem', 'mensagem.id', '=', 'mensagem_destinatarios.fk_mensagem')
            ->where('mensagem_destinatarios.destinatario',$matriculaServidorLogado[0]->matricula)
            ->where('mensagem.status','A') ->where('mensagem_destinatarios.status','A')
            ->orWhere('mensagem_destinatarios.destinatario', $lotacaoServidor)
            ->orderBy('mensagem.created_at', 'DESC')->orderBy('mensagem.hora','DESC')->get();

//dd($mensagensServidor);

        $mensagens = array();

        foreach($mensagensServidor as $mensagem)
        {

            $dadosRemetente = FacadeController::getDadosServidorPorMat($mensagem->remetente);

            $leitura = DB::table('mensagem_destinatario_lido')->where('fk_mensagem',$mensagem->id)->where('fk_destinatario', $matriculaServidorLogado[0]->matricula)->get();

            if(empty(json_decode($leitura, true)))
            {
                $lido = "N";
            }else{
                $lido = "S";
            }

            $arquivo = DB::table('mensagem_arquivo')->where('fk_mensagem',$mensagem->id)->get();
            if(empty(json_decode($arquivo, true)))
            {
                $anexo = "N";
            }else{
                $anexo = "S";
            }

            if($mensagem->tipo == "unidade")
            {
                $nomeDestinatario = UnidadesController::getNomeUp($mensagem->destinatario);
            }
            if($mensagem->tipo == "servidor")
            {
                $servidorDestinatario = FacadeController::getDadosServidorPorMat($mensagem->destinatario);
                //$nomeDestinatario = $servidorDestinatario[0]->nome;
                $nomeDestinatario = "Você";
            }

            array_push($mensagens,array(
                'id'=> $mensagem->id,
                'remetente'=> $dadosRemetente[0]->nome,
                'destinatario'=> $nomeDestinatario,
                'hora'=> $mensagem->hora,
                'data'=> Tdata::TdataBR($mensagem->created_at),
                'assunto'=> $mensagem->assunto,
                'mensagem'=> $mensagem->mensagem,
                'lido'=> $lido,
                'anexo'=> $anexo,
                'tipo'=> $mensagem->tipo,
            ));
        }

        //dd($mensagens);
        return view('mensagem.recebidas.recebidas', compact('mensagens'));
    }


    public function marcarLida(Request $request)
    {
        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula');
        //dd($request->all());
        $idMensagens = $request->get('idMensagens');
        $sucessos = array();

        foreach($idMensagens as $mensagem)
        {
            $idMensagem = explode('.',$mensagem);

            $leitura = DB::table('mensagem_destinatario_lido')->where('fk_mensagem',$idMensagem[1])->where('fk_destinatario', $matriculaServidorLogado[0]->matricula)->where('lido','S')->get();

            if(empty(json_decode($leitura, true)))
            {
                $marcarLida = DB::table('mensagem_destinatario_lido')->insert([
                        'fk_mensagem' => $idMensagem[1],
                        'fk_destinatario' => $matriculaServidorLogado[0]->matricula,
                        'lido' => 'S',
                        'hora' => date("H:i"),
                        'created_at' => date("Y-m-d"),
                    ]
                );

                LogController::log($idMensagem[1],"mensagem_destinatario_lido","add", Auth::user()->cpf, "Mensagem marcada como lida.<br>");
            }

            array_push($sucessos,$idMensagem[1]);
        }

        return $sucessos;
    }

    public function excluirMensagem(Request $request)
    {
        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula');
        //dd($request->all());
        $idMensagens = $request->get('idMensagens');
        $sucessos = array();

        foreach($idMensagens as $mensagem)
        {
            $idMensagem = explode('.',$mensagem);

            // verifica se a mensagem foi para um usuario especifico (nao apaga msg enviada para unidade)
            $verificarDominio = DB::table('mensagem_destinatarios')->where('fk_mensagem',$idMensagem[1])->where('destinatario', $matriculaServidorLogado[0]->matricula)->where('tipo',$idMensagem[0])->get();

            if(!empty(json_decode($verificarDominio, true)))
            {
                $excluirMensagem = DB::table('mensagem_destinatarios')->where('fk_mensagem',$idMensagem[1])->where('destinatario', $matriculaServidorLogado[0]->matricula)->where('tipo',$idMensagem[0])->update([
                        'status' => "I",
                        'update_at' => date("Y-m-d"),
                    ]
                );

                if($excluirMensagem){
                    array_push($sucessos,$idMensagem[1]);
                }


            }


        }

        return $sucessos;

    }

    public function mensagemExcluir($id)
    {
        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula');

        //dd($request->all());
        $idMensagens = $id;
        $sucessos = array();

            $idMensagem = explode('.',$idMensagens);

            // verifica se a mensagem foi para um usuario especifico (nao apaga msg enviada para unidade)
            $verificarDominio = DB::table('mensagem_destinatarios')->where('fk_mensagem',$idMensagem[1])->where('destinatario', $matriculaServidorLogado[0]->matricula)->where('tipo',$idMensagem[0])->get();

            if(!empty(json_decode($verificarDominio, true)))
            {
                $excluirMensagem = DB::table('mensagem_destinatarios')->where('fk_mensagem',$idMensagem[1])->where('destinatario', $matriculaServidorLogado[0]->matricula)->where('tipo',$idMensagem[0])->update([
                        'status' => "I",
                        'update_at' => date("Y-m-d"),
                    ]
                );

                LogController::log($idMensagem[1],"mensagem_destinatarios","delete", Auth::user()->cpf, "Mensagem excluída.<br>");

                if($excluirMensagem){
                    return redirect()->route('mensagem.recebidas')->with('alert-success', 'Mensagem excluída com sucesso!');

                }


            }else{

                LogController::log($idMensagem[1],"mensagem_destinatarios","delete", Auth::user()->cpf, "Tentativa de excluir mensagem enviada para unidade.<br>");

                return redirect()->back()->with('alert-danger', 'Mensagens enviadas para unidade não podem ser excluídas!');

            }



        return $sucessos;

    }

    public function responder($id)
    {
        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula');

        //dd($id);
        $unidades = DB::table('unidade')->where('status','1')->orderBy('sigla', 'ASC')->get();
        $resposta = true;

        // verifica se a mensagem foi para um usuario especifico (nao apaga msg enviada para unidade)
        $verificarDominio = DB::table('mensagem_destinatarios')->where('fk_mensagem',$id)->where('destinatario', $matriculaServidorLogado[0]->matricula)->get();

        if(!empty(json_decode($verificarDominio, true))) {

            $mensagem = DB::table('mensagem')->where('id',$id)->get();

        }else{

            return redirect()->back()->with('alert-danger', 'Você não pode responder mensagens enviadas para unidade!');

        }

        $msgs = array();

        foreach($mensagem as $item)
        {
            $dadosRemetente = FacadeController::getDadosServidorPorMat($item->remetente);

            array_push($msgs,array(
                'id'=> $item->id,
                'remetente'=> $dadosRemetente[0]->nome,
                'remetenteMatricula'=> $dadosRemetente[0]->matricula,
                'hora'=> $item->hora,
                'data'=> Tdata::TdataBR($item->created_at),
                'assunto'=> $item->assunto,
                'mensagem'=> $item->mensagem,
            ));

        }


        return view('mensagem.enviar.formEnvio', compact('unidades','resposta','msgs'));
    }

    public function encaminhar($id)
    {
        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula');

        //dd($id);
        $unidades = DB::table('unidade')->where('status','1')->orderBy('sigla', 'ASC')->get();
        $encaminhar = true;

        // verifica se a mensagem foi para um usuario especifico (nao apaga msg enviada para unidade)
        $verificarDominio = DB::table('mensagem_destinatarios')->where('fk_mensagem',$id)->where('destinatario', $matriculaServidorLogado[0]->matricula)->get();

        if(!empty(json_decode($verificarDominio, true))) {

            $mensagem = DB::table('mensagem')->where('id',$id)->get();

        }else{

            return redirect()->back()->with('alert-danger', 'Você não pode encaminhar mensagens enviadas para unidade!');

        }

        $msgs = array();

        foreach($mensagem as $item)
        {
            $dadosRemetente = FacadeController::getDadosServidorPorMat($item->remetente);

            array_push($msgs,array(
                'id'=> $item->id,
                'remetente'=> $dadosRemetente[0]->nome,
                'remetenteMatricula'=> $dadosRemetente[0]->matricula,
                'hora'=> $item->hora,
                'data'=> Tdata::TdataBR($item->created_at),
                'assunto'=> $item->assunto,
                'mensagem'=> $item->mensagem,
            ));

        }

        return view('mensagem.enviar.formEnvio', compact('unidades','encaminhar','msgs'));

    }


    public function enviadas()
    {
        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula');
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $mensagensServidor = DB::table('mensagem')
            ->where('mensagem.status','A')
            ->where('mensagem.remetente', $matriculaServidorLogado[0]->matricula)
            ->orderBy('mensagem.created_at', 'DESC')->orderBy('mensagem.hora','DESC')->get();


//dd($mensagensServidor);

        $mensagens = array();

        foreach($mensagensServidor as $mensagem)
        {

            array_push($mensagens,array(
                'id'=> $mensagem->id,
                'remetente'=> "Você",
                'destinatario'=> "",
                'hora'=> $mensagem->hora,
                'data'=> Tdata::TdataBR($mensagem->created_at),
                'assunto'=> $mensagem->assunto,
                'destinatarios'=> self::getDestinatarioMensagem($mensagem->id),
                'mensagem'=> $mensagem->mensagem,
            ));
        }

        //dd($mensagens);
        return view('mensagem.enviadas.enviadas', compact('mensagens'));

    }

    public function lerEnviada($id)
    {
        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula');
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));


        $mensagem = DB::table('mensagem');
        $mensagem->where('mensagem.id',$id);

        $verificaTipoMensagem = $mensagem->get();

        $remetente = array();

        foreach($verificaTipoMensagem as $tipoMensagem) {

            array_push($remetente, $tipoMensagem->remetente);

        }

        $mensagemDados = $mensagem->get();

        // verifica se está na lista de destinatario
        $verificaDestinatario = array_intersect(array($matriculaServidorLogado[0]->matricula),$remetente);

        if(empty(($verificaDestinatario)))
        {
            return redirect()->route('mensagem.enviada')->with('alert-danger', 'Não foi você que enviou essa mensagem!');
        }


        $dadosMensagem = array();

        foreach($mensagemDados as $mensagemDado)
        {
            $dadosRemetente = FacadeController::getDadosServidorPorMat($mensagemDado->remetente);


            $arquivo = DB::table('mensagem_arquivo')->where('fk_mensagem',$id)->get();
            if(empty(json_decode($arquivo, true)))
            {
                $anexo = false;
            }else{

                $anexo = array();

                foreach($arquivo as $item)
                {
                    array_push($anexo, array(
                        'nome' => $item->nome_arquivo,
                        'fk_mensagem' => $item->fk_mensagem,
                    ));
                }
            }

            array_push($dadosMensagem,array(
                'id'=> $id,
                'remetente'=> $dadosRemetente[0]->nome,
                'hora'=> $mensagemDado->hora,
                'data'=> Tdata::TdataBR($mensagemDado->created_at),
                'assunto'=> $mensagemDado->assunto,
                'mensagem'=> $mensagemDado->mensagem,
                'destinatarios' => self::getDestinatarioMensagem($id,"array"),
                'anexo'=> $anexo,
            ));
        }

        return view('mensagem.ler.lerEnviadas',compact('dadosMensagem'));

    }

    public function mensagemExcluirEnviada($id)
    {
        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula');


        // verifica se a mensagem foi para um usuario especifico (nao apaga msg enviada para unidade)
        $verificarDominio = DB::table('mensagem')->where('remetente',$matriculaServidorLogado[0]->matricula)->where('id', $id)->get();

        //dd($verificarDominio);

        if(!empty(json_decode($verificarDominio, true)))
        {
            $verificaLido = DB::table('mensagem_destinatario_lido')->where('fk_mensagem', $id)->get();

            if(!empty(json_decode($verificaLido, true)))
            {
                return redirect()->route('mensagem.enviada')->with('alert-danger', 'Sua mensagem já foi visualizada por um destinatário, não é possível excluí-la!');

            }else {

                $excluirMensagem = DB::table('mensagem')->where('id', $id)->update([
                        'status' => "I",
                        'update_at' => date("Y-m-d"),
                    ]
                );

                LogController::log($id,"mensagem","delete", Auth::user()->cpf, "Mensagem enviada excluída.<br>");

                if ($excluirMensagem) {
                    return redirect()->route('mensagem.enviada')->with('alert-success', 'Mensagem excluída com sucesso!');

                }

            }


        }else{

            LogController::log($id,"mensagem","delete", Auth::user()->cpf, "Tentativa de excluir uma mensagem que não é de propriedade do usuário.<br>");

            return redirect()->back()->with('alert-danger', 'Não foi você que enviou essa mensagem!');

        }


    }

    public function mensagemExcluirEnviadaAjax(Request $request)
    {
        $ids = $request->get('idMensagens');

        $matriculaServidorLogado = FacadeController::getDadosServidor(Auth::user()->cpf,'matricula');

        $sucessos = array();

        foreach($ids as $id) {
            // verifica se a mensagem foi para um usuario especifico (nao apaga msg enviada para unidade)
            $verificarDominio = DB::table('mensagem')->where('remetente', $matriculaServidorLogado[0]->matricula)->where('id', $id)->get();

            //dd($verificarDominio);

            if (!empty(json_decode($verificarDominio, true))) {

                $verificaLido = DB::table('mensagem_destinatario_lido')->where('fk_mensagem', $id)->get();

                if (!empty(json_decode($verificaLido, true))) {

                    array_push($sucessos, false);

                } else {

                    $excluirMensagem = DB::table('mensagem')->where('id', $id)->update([
                            'status' => "I",
                            'update_at' => date("Y-m-d"),
                        ]
                    );

                    LogController::log($id,"mensagem","delete", Auth::user()->cpf, "Mensagem enviada excluída.<br>");


                    if ($excluirMensagem) {

                        array_push($sucessos,$id);

                    }

                }


            } else {

                //return false;

            }

        }

        return $sucessos;
    }

    public static function getDestinatarioMensagem($idMensagem,$tipo='string')
    {
        $dados = DB::table('mensagem_destinatarios')->where('fk_mensagem',$idMensagem)->get();

        if($tipo == "string")
        {
            $dadosDestinatario = "";

            foreach($dados as $dado) {
                if ($dado->tipo == "unidade") {
                    $nomeDestinatario = UnidadesController::getSiglaUp($dado->destinatario);
                }
                if ($dado->tipo == "servidor") {
                    $servidorDestinatario = FacadeController::getDadosServidorPorMat($dado->destinatario);
                    $nomeDestinatario = substr($servidorDestinatario[0]->nome, 0, 10)."...";
                    //$nomeDestinatario = "Você";
                }

                $dadosDestinatario .= $nomeDestinatario.", ";
            }

            return substr($dadosDestinatario, 0, -2);

        }

        if($tipo == "array")
        {
            $dadosDestinatario = array();

            foreach($dados as $dado) {
                if ($dado->tipo == "unidade") {
                    $nomeDestinatario = UnidadesController::getNomeUp($dado->destinatario);
                }
                if ($dado->tipo == "servidor") {
                    $servidorDestinatario = FacadeController::getDadosServidorPorMat($dado->destinatario);
                    $nomeDestinatario = $servidorDestinatario[0]->nome;
                }

            array_push($dadosDestinatario, array(
               'nome' => $nomeDestinatario,
               'id' => $dado->id,
               'tipo' => $dado->tipo,
            ));
            }

        }

        return $dadosDestinatario;

    }
}
