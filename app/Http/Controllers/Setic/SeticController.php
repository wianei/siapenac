<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a Agenda
|
*/

namespace App\Http\Controllers\Setic;

use App\Classes\Constantes;
use App\Classes\Tdata;
use App\Http\Controllers\Interno\InternoController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Http\Controllers\Usuario\UsuarioController;
use App\Http\Controllers\Permissoes\PermissoesController;
use App\Http\Controllers\Facade\FacadeController;
use App\Mail\SendMailCadastroUser;
use App\Mail\SendMailRecuperacaoConta;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SeticController extends Controller
{

    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->middleware('auth');
    }

    public function callAction($method, $parameters)
    {

        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::verifyPermission("SeticController", $method);
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

        return parent::callAction($method, $parameters);
    }

    public function recuperacaoContas(Request $request)
    {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        /**
         * se não realizar uma busca
         * - Evita que mostre todos os resultados sem realizar uma busca
         */
        if(!strpos($url,"?"))
        {
            $recuperacao = DB::table('users_recuperacao');
            $recuperacao->leftJoin('users', 'users.cpf', '=', 'users_recuperacao.cpf');
            $recuperacao->where('users_recuperacao.recuperado', 'N');

            $dados = $recuperacao->select('users.name',"users.cpf","users_recuperacao.id AS idRecuperacao","users_recuperacao.email","users_recuperacao.orgao","users_recuperacao.created_at", DB::raw('(CASE WHEN users_recuperacao.recuperado = "S" THEN "Sim" ELSE "Não" END) AS recuperado'),DB::raw("DATE_FORMAT(users_recuperacao.created_at,'%d/%m/%Y %H:%i:%s') as created_at"))->orderBy('users_recuperacao.created_at','DESC')->get();



        }else {

            $recuperacao = DB::table('users_recuperacao');
            $recuperacao->leftJoin('users', 'users.cpf', '=', 'users_recuperacao.cpf');

            $cpf = $request->get('cpf');
            $dataInicial = $request->get('dataInicial');
            $dataFinal = $request->get('dataFinal');
            $email = $request->get('email');
            $recuperado = $request->get('recuperado');

            if($cpf)
            {
                $recuperacao->where('users_recuperacao.cpf', $cpf);

            }

            if($recuperado)
            {
                $recuperacao->where('users_recuperacao.recuperado', $recuperado);

            }

            if($email)
            {
                $recuperacao->where('users_recuperacao.email', 'LIKE', '%' . $email . '%');
            }

            if($dataInicial){
                $recuperacao->where('users_recuperacao.created_at','>=', Tdata::TdataMysql($dataInicial));
            }
            if($dataFinal){
                $recuperacao->where('users_recuperacao.created_at','<=', Tdata::TdataMysql($dataFinal));
            }

            $dados = $recuperacao->select('users.name',"users.cpf","users_recuperacao.id AS idRecuperacao","users_recuperacao.email","users_recuperacao.orgao","users_recuperacao.created_at", DB::raw('(CASE WHEN users_recuperacao.recuperado = "S" THEN "Sim" ELSE "Não" END) AS recuperado'),DB::raw("DATE_FORMAT(users_recuperacao.created_at,'%d/%m/%Y %H:%i:%s') as created_at"))->orderBy('users_recuperacao.created_at','DESC')->get();

            $dados = $dados->unique('cpf');

        }

        if($dados != false) {
            $dados = collect($dados)->paginate(50)->appends($request->input());
        }


        return view('setic.recuperacaoConta.listarContas',compact('dados'));
    }

    public function recuperacaoContasArquivos($id)
    {
        $arquivos = DB::table('users_recuperacao_arquivos')->where('fk_recuperacao', $id)->get();

        $dados = DB::table('users_recuperacao')
            ->leftJoin('users', 'users.cpf', '=', 'users_recuperacao.cpf')
            ->where('users_recuperacao.id', $id)
            ->select('users.name',"users.cpf","users_recuperacao.id AS idRecuperacao","users_recuperacao.email","users_recuperacao.orgao","users_recuperacao.created_at", DB::raw('(CASE WHEN users_recuperacao.recuperado = "S" THEN "Sim" ELSE "Não" END) AS recuperado'),DB::raw("DATE_FORMAT(users_recuperacao.created_at,'%d/%m/%Y %H:%i:%s') as created_at"))
            ->get();

        return view('setic.recuperacaoConta.arquivos',compact('arquivos','dados'));
    }

    public function recuperacaoContasEnviarSenha($cpf)
    {
        $senha = str_random(8);

        $senhaDB = Hash::make($senha);

        $dados = DB::table('users_recuperacao')
            ->leftJoin('users', 'users.cpf', '=', 'users_recuperacao.cpf')
            ->where('users_recuperacao.cpf', $cpf)
            ->select('users.name',"users.cpf","users_recuperacao.id AS idRecuperacao","users_recuperacao.email","users_recuperacao.orgao","users_recuperacao.created_at", DB::raw('(CASE WHEN users_recuperacao.recuperado = "S" THEN "Sim" ELSE "Não" END) AS recuperado'),DB::raw("DATE_FORMAT(users_recuperacao.created_at,'%d/%m/%Y %H:%i:%s') as created_at"))
            ->get();

        $updateSenha = DB::table('users')->where('cpf', $cpf)->update([
            'password' => $senhaDB,
            'email' => $dados[0]->email,
            'status' => 'A',
            'ultima_alteracao_senha' => Tdata::TtimeStampHojeMysql()

        ]);

        $updateRecuperacao = DB::table('users_recuperacao')->where('cpf', $cpf)->update([
            'recuperado' => 'S',
        ]);

        $mensagem = "Login: $cpf<br>Senha: $senha";

        $enviado = Mail::to($dados[0]->email)->send(new SendMailRecuperacaoConta($dados[0]->name,$cpf,$mensagem,$dados[0]->email,$dados[0]->orgao,$senha,"Recuperação de Conta ".config('app.name')));

        if(count(Mail::failures()) == 0){

            LogController::log($dados[0]->idRecuperacao,"users_recuperacao","add",Auth::user()->cpf,"Recuperacao de conta do CPF: ".$cpf);

            return redirect()->back()->withInput()->with('alert-success', 'E-mail enviado com sucesso!');

        }


    }

    public function listarChamadosHelpDesk()
    {
        $datahj = Tdata::TdataHojeMysql();

        /*
        * Fecha os chamados automaticamente se nao houver interacao
        */
        // $chamadosSemInteracao = DB::table('helpdesk')->where('created_at', '<=', Tdata::subDias($datahj, 7))->where('status','A')->get();
        // if(count($chamadosSemInteracao) > 0)
        // {
        //     foreach($chamadosSemInteracao as $chamadosParados)
        //     {
        //         DB::table('helpdesk_interation')->insert([
        //             'fk_chamado' => $chamadosParados->id,
        //             'user' => '00000000000',
        //             'descricao' => '<p>Chamado fechado automáticamente por falta de interação.</p>',
        //             'created_at' => Tdata::TtimeStampHojeMysql()
        //         ]);

        //         DB::table('helpdesk')->where('id', $chamadosParados->id)->update([
        //             'status' => 'F',
        //             'update_at' => Tdata::TtimeStampHojeMysql()
        //         ]);
        //     }

        // }

        $chamadosDados = DB::table('helpdesk')->orderBy('status', 'ASC')->orderByRaw('DATE(created_at) DESC')->orderBy('prioridade', 'ASC')->orderByRaw('HOUR(created_at) ASC')->get();

        $chamados = array();

        foreach($chamadosDados as $chamado)
        {
            $countInteracoes = DB::table('helpdesk_interation')->where('fk_chamado', $chamado->id)->count();
            $ultimaInteracao = DB::table('helpdesk_interation')->where('fk_chamado', $chamado->id)->orderBy('created_at','DESC')->first();
            
            if(!is_null($ultimaInteracao)){
                
                if(( $ultimaInteracao)) {

                    if($ultimaInteracao->user != '00000000000') {

                        $permissaoUsuario = UsuarioController::getTipoConta($ultimaInteracao->user);

                        if ($permissaoUsuario != 1000) {

                            $novaInteracao = 'S';

                        } else {

                            $novaInteracao = 'N';
                        }
                    }else{

                        $novaInteracao = 'N';
                    }

                }else{
                    $novaInteracao = 'N';
                }

            }else{
                $novaInteracao = 'N';
            }

            array_push($chamados, array(
               'id' => $chamado->id,
               'user' => $chamado->user,
               'prioridade' => $chamado->prioridade,
               'assunto' => $chamado->assunto,
               'descricao' => $chamado->descricao,
               'status' => $chamado->status,
               'created_at' => Tdata::TtimestampBR($chamado->created_at),
               'update_at' => Tdata::TtimestampBR($chamado->update_at),
               'interacoes' => $countInteracoes,
               'novaInteracao' => $novaInteracao,
            ));

        }

        return view('setic.helpdesk.listar', compact('chamados'));

    }

    public function listarUsuarios(Request $request)
    {

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if(!strpos($url,"?")) {
            $dados = false;
        }else{


            $cpf = $request->get('cpf');
            $email = $request->get('email');
            $tipo = $request->get('tipo');
            $status = $request->get('status');
            $nome = $request->get('nome');
            $visualiza_outras_up = $request->get('visualiza_outras_up');

            $usuarios = DB::table('users');

            if($nome)
            {
                $usuarios->where('users.name', 'LIKE', '%' . $nome . '%');

            }

            if($cpf)
            {
                $usuarios->where('users.cpf', $cpf);

            }

            if($email)
            {
                $usuarios->where('users.email', 'LIKE', '%' . $email . '%');

            }

            if($tipo)
            {
                
                $usuarios->where('users.tipo', $tipo);

            }

            if($status)
            {
                $usuarios->where('users.status', $status);

            }

            if($visualiza_outras_up)
            {
                $usuarios->where('users.visualiza_outras_up', $visualiza_outras_up);

            }

            $dados = $usuarios->select('users.*',DB::raw("DATE_FORMAT(users.ultimo_login,'%d/%m/%Y %H:%i:%s') as ultimo_login"),DB::raw("DATE_FORMAT(users.created_at,'%d/%m/%Y %H:%i:%s') as created_at"), DB::raw('(CASE WHEN users.status = "A" THEN "Ativo" ELSE "Inativo" END) AS status'), DB::raw('(CASE WHEN users.visualiza_outras_up = "S" THEN "Sim" ELSE "Não" END) AS visualiza_outras_up'))->orderBy('name','ASC')->get();

            if($dados != false) {
                $dados = collect($dados)->paginate(50)->appends($request->input());
            }


        }
        
        $permissoes = DB::table("permissao_perfil")
                ->select('permissao_perfil.*', DB::raw('(CASE WHEN permissao_perfil.tipo = "I" THEN "Perfil Interno (Servidores)" WHEN permissao_perfil.tipo = "E" THEN "Perfil Externo" ELSE "Não Informado" END) AS tipo'))
                ->orderBy('nome')->get();
        
        $permissoes =        json_decode($permissoes, TRUE);
            return view('setic.usuarios.listarUsuarios', compact('dados','permissoes'));
    }

    public function cadastrarUsuarios()
    {
        $perfilPermissao = DB::table('permissao_perfil')
                ->select('permissao_perfil.*', DB::raw('(CASE WHEN permissao_perfil.tipo = "I" THEN "Perfil Interno (Servidores)" WHEN permissao_perfil.tipo = "E" THEN "Perfil Externo" ELSE "Não Informado" END) AS tipo'))
                ->orderBy('tipo', 'ASC')
                ->orderBy('nome', 'ASC')
                ->get();
        
        $unidadesTroca = DB::table("unidade")->where('status', 1)->where("troca", 1)->orderBy('nome', 'ASC')->get();

        $unidadesLiberadas = $lotacaoServidor = null;
        
        return view('setic.usuarios.cadastrarUsuarios', compact('perfilPermissao','unidadesTroca','unidadesLiberadas','lotacaoServidor'));
    }

    public function cadastrarUsuariosStore(Request $request)
    {
        //dd($request->input());

        $nome = $request->get('nome');
        $cpf = $request->get('cpf');
        $email = $request->get('email');
        $orgao = $request->get('orgao');
        $cargo = $request->get('cargo');
        $tipo = $request->get('tipo');
        $visualizaOutrasUp = $request->get('visualiza_outras_up');
        $status = $request->get('status');
        $unidadesTroca = $request->get('unidadesTroca');

        $verificaUsuarioCpf = DB::table('users')->where('cpf', $cpf)->get();
        $verificaUsuarioEmail = DB::table('users')->where('email', $email)->get();

        if (count($verificaUsuarioCpf) >= 1) {

            return redirect()->back()->withInput($request->input())->with('alert-danger', 'O CPF informado já está em uso!');

        }

        if (count($verificaUsuarioEmail) >= 1) {

            return redirect()->back()->withInput($request->input())->with('alert-danger', 'O Email informado já está em uso!');

        }

        $senha = str_random(8);

        $senhaDB = Hash::make($senha);

        /**
         * Unidades Liberadas
         */
        $lotacoes = null;
        $verificaServidor = FacadeController::servidorNoRH($cpf);
        
        if($verificaServidor){
            if(isset($unidadesTroca) && $visualizaOutrasUp == "S")
            {
                $lotacaoServidor = FacadeController::getIdLotacaoServidor($cpf, "S");
                $lotacoes = implode(",", $unidadesTroca).",".$lotacaoServidor;

                $alteracoes = "Alteração de dados no campo de unidades liberadas para visualizacao. Lotacoes Liberadas: " . $lotacoes;
                array_push($diferencas, $alteracoes);
            }

            if(!isset($unidadesTroca) && $visualizaOutrasUp == "S")
            {
                $lotacoes = null;
            }
        }else{
            $visualizaOutrasUp = "N";
        }


        $insertUsuario = DB::table('users')->insert([
           'name' => $nome,
           'cpf' => $cpf,
           'email' => $email,
           'password' => $senhaDB,
           'tipo' => $tipo,
           'cargo' => $cargo,
           'orgao' => $orgao,
           'visualiza_outras_up' => $visualizaOutrasUp,
           'visualizacao_up_liberada' => $lotacoes,
           'status' => $status,
           'created_at' => Tdata::TtimeStampHojeMysql(),
            'ultima_alteracao_senha' => Tdata::TtimeStampHojeMysql()

        ]);

        $idInsert = DB::getPdo()->lastInsertId();

        LogController::log($idInsert,"users","add",Auth::user()->cpf,"Criação de conta do CPF: ".$cpf."<br>Email: ".$email);

        $mensagem = "Login: $cpf<br>Senha: $senha";

        $enviado = Mail::to($email)->send(new SendMailCadastroUser($nome,$cpf,$mensagem,$email,$orgao,$senha,"Cadastro de Conta ".config('app.name')));

        if(count(Mail::failures()) == 0){

            return redirect()->route('setic.listarUsuarios')->with('alert-success', 'Cadastro realizado com sucesso! O email com a senha foi enviado para: <strong>'.$email."</strong>");

        }

    }

    public function editarUsuarios($id)
    {
       // dd($id);
        $perfilPermissao = DB::table('permissao_perfil')
                ->select('permissao_perfil.*', DB::raw('(CASE WHEN permissao_perfil.tipo = "I" THEN "Perfil Interno (Servidores)" WHEN permissao_perfil.tipo = "E" THEN "Perfil Externo" ELSE "Não Informado" END) AS tipo'))
                ->orderBy('tipo', 'ASC')
                ->orderBy('nome', 'ASC')
                ->get();

        $dados = DB::table('users')->where('id', $id)->get();
        
        $verificaServidor = FacadeController::servidorNoRH($dados[0]->cpf);
        if($verificaServidor){
            $lotacaoServidor = FacadeController::getIdLotacaoServidor($dados[0]->cpf, "S");
        }else{
            $lotacaoServidor = null;
        }
        
        $unidadesTroca = DB::table("unidade")->where('status', 1)->where("troca", 1)->orderBy('nome', 'ASC')->get();
        
        $unidadesLiberadas = null;
        if(!is_null($dados[0]->visualizacao_up_liberada))
        {
            $unidadesLiberadas = explode(",", $dados[0]->visualizacao_up_liberada);
        }

        return view('setic.usuarios.cadastrarUsuarios', compact('dados','id','perfilPermissao','unidadesTroca','unidadesLiberadas','lotacaoServidor'));
    }

    public function editarUsuariosStore(Request $request, $id)
    {
        //dd($request->all());
        
        $nome = $request->get('nome');
        $cpf = $request->get('cpf');
        $email = $request->get('email');
        $orgao = $request->get('orgao');
        $cargo = $request->get('cargo');
        $tipo = $request->get('tipo');
        $visualizaOutrasUp = $request->get('visualiza_outras_up');
        $status = $request->get('status');
        $unidadesTroca = $request->get('unidadesTroca');

        $dadosConta = DB::table('users')->where('id', $id)->get();
        if($dadosConta[0]->cpf != $cpf)
        {
            $verificaCpf = DB::table('users')->where('cpf', $cpf)->get();
            if(count($verificaCpf) > 0)
            {
                return redirect()->back()->withInput($request->input())->with('alert-danger', 'O CPF informado já está em uso!');
            }
        }

        if($dadosConta[0]->email != $email)
        {
            $verificaEmail = DB::table('users')->where('email', $email)->get();
            if(count($verificaEmail) > 0)
            {
                return redirect()->back()->withInput($request->input())->with('alert-danger', 'O Email informado já está em uso!');
            }
        }

        //-------------------- LOG -------------------------------------

        // LOG - array que armazena os textos do log
        $diferencas = array();
        $alteracoes = "Alteração de Dados do usuario<br>";
        array_push($diferencas, $alteracoes);

        $dataForm = array(
            'name' => $nome,
            'cpf' => $cpf,
            'email' => $email,
            'tipo' => $tipo,
            'cargo' => $cargo,
            'orgao' => $orgao,
            'visualiza_outras_up' => $visualizaOutrasUp,
            'status' => $status,
        );

        $dadosBD = DB::table("users")->where('id', $id)->get();
        // quando a conta estiver inativa e for ativa-la
        if($dataForm['status'] == "A")
        {
            if($dadosBD[0]->status == "I") {
                $dtHoje = Tdata::TdataHojeMysql();
                if (!file_exists(storage_path('app/arquivos/loguser/' . $dataForm['cpf'] . "/" . $dtHoje . ".txt"))) {

                    Storage::put('arquivos/loguser/' . $dataForm['cpf'] . "/" . $dtHoje . ".txt", "");
                    $txt = date("H:i:s") . " - Ativação de conta" . "\n\r";
                    Storage::prepend('arquivos/loguser/' . $dataForm['cpf'] . "/" . $dtHoje . ".txt", $txt);

                } else {

                    $txt = date("H:i:s") . " - Ativação de conta" . "\n\r";
                    Storage::prepend('arquivos/loguser/' . $dataForm['cpf'] . "/" . $dtHoje . ".txt", $txt);

                }
            }

        }
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

        /**
         * Unidades Liberadas
         */
        $lotacoes = null;
        if(isset($unidadesTroca) && $visualizaOutrasUp == "S")
        {
            $lotacaoServidor = FacadeController::getIdLotacaoServidor($dadosConta[0]->cpf, "S");
            $lotacoes = implode(",", $unidadesTroca).",".$lotacaoServidor;
            
            $alteracoes = "Alteração de dados no campo de unidades liberadas para visualizacao. Lotacoes Liberadas: " . $lotacoes;
            array_push($diferencas, $alteracoes);
        }

        if(!isset($unidadesTroca) && $visualizaOutrasUp == "S")
        {
            $lotacoes = null;
        }

        $updateUsuario = DB::table('users')->where('id', $id)->update([
            'name' => $nome,
            'cpf' => $cpf,
            'email' => $email,
            'tipo' => $tipo,
            'cargo' => $cargo,
            'orgao' => $orgao,
            'visualiza_outras_up' => $visualizaOutrasUp,
            'visualizacao_up_liberada' => $lotacoes,
            'status' => $status,
            'updated_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        if ($updateUsuario) {

            if(!empty($diferencas))
            {
                $textoLog = implode(" ", $diferencas);
                LogController::log($id,"users","update", Auth::user()->cpf, $textoLog);
            }

            return redirect()->route('setic.listarUsuarios')->with('alert-success', 'O cadastro da usuário foi alterado com sucesso!');

        }else{

            return redirect()->route('setic.listarUsuarios')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }

    public function enviarNovaSenha($id)
    {
        $dadosUser = DB::table('users')->where('id', $id)->get();

        $senha = str_random(8);

        $senhaDB = Hash::make($senha);

        $updateUsuario = DB::table('users')->where('id', $id)->update([
            'password' => $senhaDB,
            'updated_at' => Tdata::TtimeStampHojeMysql(),
            'ultima_alteracao_senha' => Tdata::TtimeStampHojeMysql(),

        ]);


        LogController::log($id,"users","update",Auth::user()->cpf, "Envio de senha da conta CPF: ".$dadosUser[0]->cpf."<br>Email: ".$dadosUser[0]->email);

        $mensagem = "Login: ".$dadosUser[0]->cpf."<br>Senha: $senha";

        $enviado = Mail::to($dadosUser[0]->email)->send(new SendMailCadastroUser($dadosUser[0]->name,$dadosUser[0]->cpf,$mensagem,$dadosUser[0]->email,$dadosUser[0]->orgao,$senha,"Senha de Acesso ao ".config('app.name')));

        if(count(Mail::failures()) == 0){

            return redirect()->route('setic.listarUsuarios')->with('alert-success', 'O email com a nova senha foi enviado para: <strong>'.$dadosUser[0]->email."</strong>");

        }

    }

    public function unidadesListar(Request $request)
    {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";


        if(!strpos($url,"?")) {
            $dados = false;
        }else{


            $sigla = $request->get('sigla');
            $status = $request->get('status');
            $nome = $request->get('nome');
            $idUnidade = $request->get('idUnidade');

            $unidades = DB::table('unidade');

            if($nome)
            {
                $unidades->where('unidade.nome', 'LIKE', '%' . $nome . '%');

            }

            if($sigla)
            {
                $unidades->where('unidade.sigla', $sigla);

            }


            if($status)
            {
                $unidades->where('unidade.status', $status);

            }

            if($idUnidade)
            {
                $unidades->where('unidade.id', $idUnidade);

            }

            $dados = $unidades->select('unidade.*',DB::raw('(CASE WHEN unidade.status = "1" THEN "Ativo" ELSE "Inativo" END) AS status'))->orderBy('nome','ASC')->get();

            if($dados != false) {
                $dados = collect($dados)->paginate(50)->appends($request->input());
            }


        }
        $unidades = DB::table('unidade')->orderBy('sigla', 'ASC')->get();

        return view('setic.unidades.listarUnidades', compact('dados','unidades'));

    }

    public function unidadesCadastrar()
    {
        return view('setic.unidades.unidadeCadastrar');
    }

    public function unidadesCadastrarStore(Request $request)
    {
        //dd($request->input());

        $insert = DB::table('unidade')->insert([
            'nome' => $request->get('nome'),
            'sigla' => $request->get('sigla'),
            'status' => 1,
            'troca' => $request->get('troca'),
            'cidade' => $request->get('cidade'),
            'qtdHorasPlantao' => $request->get('qtdHorasPlantao'),
            'horaInicioPlantao' => $request->get('horaInicioPlantao'),
            'diasSemAtendimentoAdv' => $request->get('diasSemAtendimentoAdv'),
            'qtdParlatorio' => $request->get('qtdParlatorio'),
            'horarioInicioAtendimentoAdv' => $request->get('horarioInicioAtendimentoAdv'),
            'horarioTerminoAtendimentoAdv' => $request->get('horarioTerminoAtendimentoAdv'),
            'horarioInicioIntervaloAlmoco' => $request->get('horarioInicioIntervaloAlmoco'),
            'horarioTerminoIntervaloAlmoco' => $request->get('horarioTerminoIntervaloAlmoco'),
            'created_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        $idInsert = DB::getPdo()->lastInsertId();

        LogController::log($idInsert,"unidade","add",Auth::user()->cpf,"Criação de unidade");

        if($idInsert)
        {
            return redirect()->route('setic.unidadesListar')->with('alert-success', 'Unidade Cadastrada com sucesso!');

        }else{

            return redirect()->route('setic.unidadesListar')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }

    }

    public function unidadesEditar($id)
    {
        $dados = DB::table('unidade')->where('id', $id)->get();

        return view('setic.unidades.unidadeCadastrar', compact('dados'));
    }

    public function unidadesEditarStore(Request $request, $id)
    {
        //dd($id);

        //-------------------- LOG -------------------------------------

        // LOG - array que armazena os textos do log
        $diferencas = array();
        $alteracoes = "Alteração de Dados de Unidade<br>";
        array_push($diferencas, $alteracoes);

        $dataForm = array(
            'nome' => $request->get('nome'),
            'sigla' => $request->get('sigla'),
            'status' => $request->get('status'),
            'troca' => $request->get('troca'),
            'cidade' => $request->get('cidade'),
            'qtdHorasPlantao' => $request->get('qtdHorasPlantao'),
            'horaInicioPlantao' => $request->get('horaInicioPlantao'),
            'diasSemAtendimentoAdv' => $request->get('diasSemAtendimentoAdv'),
            'qtdParlatorio' => $request->get('qtdParlatorio'),
            'horarioInicioAtendimentoAdv' => $request->get('horarioInicioAtendimentoAdv'),
            'horarioTerminoAtendimentoAdv' => $request->get('horarioTerminoAtendimentoAdv'),
            'horarioInicioIntervaloAlmoco' => $request->get('horarioInicioIntervaloAlmoco'),
            'horarioTerminoIntervaloAlmoco' => $request->get('horarioTerminoIntervaloAlmoco'),
            'created_at' => Tdata::TtimeStampHojeMysql(),
        );

        $dadosBD = DB::table("unidade")->where('id', $id)->get();
        json_decode(json_encode($dadosBD[0]), true);

        if($dadosBD[0]->qtdHorasPlantao != $dataForm['qtdHorasPlantao'])
        {
            DB::table('unidade_historico')->insert([
                'fk_unidade' => $id,
                'data_modificacao' => Tdata::TdataHojeMysql(),
                'qtdHorasPlantao' => $request->get('qtdHorasPlantao'),
                'horarioInicioPlantao' => $request->get('horaInicioPlantao')
            ]);
        }

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

        $update = DB::table('unidade')->where('id', $id)->update([
            'nome' => $request->get('nome'),
            'sigla' => $request->get('sigla'),
            'status' => $request->get('status'),
            'troca' => $request->get('troca'),
            'cidade' => $request->get('cidade'),
            'qtdHorasPlantao' => $request->get('qtdHorasPlantao'),
            'horaInicioPlantao' => $request->get('horaInicioPlantao'),
            'diasSemAtendimentoAdv' => $request->get('diasSemAtendimentoAdv'),
            'qtdParlatorio' => $request->get('qtdParlatorio'),
            'horarioInicioAtendimentoAdv' => $request->get('horarioInicioAtendimentoAdv'),
            'horarioTerminoAtendimentoAdv' => $request->get('horarioTerminoAtendimentoAdv'),
            'horarioInicioIntervaloAlmoco' => $request->get('horarioInicioIntervaloAlmoco'),
            'horarioTerminoIntervaloAlmoco' => $request->get('horarioTerminoIntervaloAlmoco'),
            'created_at' => Tdata::TtimeStampHojeMysql(),
        ]);



        //-------------------- LOG -------------------------------------

        if($id)
        {
            if(!empty($diferencas))
            {
                $textoLog = implode(" ", $diferencas);
                LogController::log($id,"unidade","update", Auth::user()->cpf, $textoLog);
            }

            return redirect()->route('setic.unidadesListar')->with('alert-success', 'Dados Alterados com sucesso!');

        }else{

            return redirect()->route('setic.unidadesListar')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }

    public function unidadesPavilhoes($id)
    {
        $dados = DB::table('pavilhao')->where('fk_up', $id)->orderBy('nome', 'ASC')->get();

        $nomeUnidade = UnidadesController::getNomeUp($id);

        return view('setic.unidades.listaPavilhoes', compact('dados','nomeUnidade', 'id'));

    }

    public function unidadesPavilhoesCadastrar($id)
    {
        return view('setic.unidades.pavilhaoCadastrar', compact('id'));
    }

    public function unidadesPavilhoesCadastrarStore(Request $request, $id)
    {
        $insert = DB::table('pavilhao')->insert([
            'nome' => $request->get('nome'),
            'qtdParlatorio' => $request->get('qtdParlatorio'),
            'fk_up' => $id,
            'status' => 'A',
            'created_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        $idInsert = DB::getPdo()->lastInsertId();

        LogController::log($idInsert,"pavilhao","add",Auth::user()->cpf,"Criação de pavilhao para unidade ".UnidadesController::getNomeUp($id));

        if($idInsert)
        {
            return redirect()->route('setic.unidadesPavilhoes', $id)->with('alert-success', 'Pavilhão Cadastrado com sucesso!');

        }else{

            return redirect()->route('setic.unidadesListar')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }

    public function unidadesPavilhoesEditar($id)
    {
        $dados = DB::table('pavilhao')->where('id', $id)->get();

        return view('setic.unidades.pavilhaoCadastrar', compact('id','dados'));
    }

    public function unidadesPavilhoesEditarStore(Request $request, $id)
    {

        //-------------------- LOG -------------------------------------

        // LOG - array que armazena os textos do log
        $diferencas = array();
        $alteracoes = "Alteração de Dados do Pavilhão<br>";
        array_push($diferencas, $alteracoes);

        $dataForm = array(
            'nome' => $request->get('nome'),
            'qtdParlatorio' => $request->get('qtdParlatorio'),
            'status' => $request->get('status'),
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
            'nome' => $request->get('nome'),
            'qtdParlatorio' => $request->get('qtdParlatorio'),
            'status' => $request->get('status'),
            'updated_at' => Tdata::TtimeStampHojeMysql(),
        ]);


        if($id)
        {
            if(!empty($diferencas))
            {
                $textoLog = implode(" ", $diferencas);
                LogController::log($id,"pavilhao","update", Auth::user()->cpf, $textoLog);
            }

            return redirect()->route('setic.unidadesListar')->with('alert-success', 'Dados Alterados com sucesso!');

        }else{

            return redirect()->route('setic.unidadesListar')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }

    }

    public function unidadesAlas($idPv)
    {
        $dados = DB::table('ala')->where('fk_pavilhao', $idPv)->orderBy('nome', 'ASC')->get();

        $nomePavilhao = UnidadesController::getNomePav($idPv);

        return view('setic.unidades.listaAlas', compact('dados','nomePavilhao','idPv'));
    }

    public function unidadesAlasCadastrar($idPv)
    {
        return view('setic.unidades.alaCadastrar', compact('idPv'));

    }

    public function unidadesAlasCadastrarStore(Request $request, $idPv)
    {
        $insert = DB::table('ala')->insert([
            'nome' => $request->get('nome'),
            'fk_pavilhao' => $idPv,
            'status' => 'A',
            'created_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        $idInsert = DB::getPdo()->lastInsertId();

        LogController::log($idInsert,"ala","add",Auth::user()->cpf,"Criação de ala para unidade.");

        if($idInsert)
        {
            return redirect()->route('setic.unidadesAlas', $idPv)->with('alert-success', 'Ala Cadastrada com sucesso!');

        }else{

            return redirect()->route('setic.unidadesListar')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }

    }

    public function unidadesAlasEditar($idAla)
    {
        $dados = DB::table('ala')->where('id', $idAla)->get();

        return view('setic.unidades.alaCadastrar', compact('idAla','dados'));

    }

    public function unidadesAlasEditarStore(Request $request, $id)
    {

        //-------------------- LOG -------------------------------------

        // LOG - array que armazena os textos do log
        $diferencas = array();
        $alteracoes = "Alteração de Dados da Ala<br>";
        array_push($diferencas, $alteracoes);

        $dataForm = array(
            'nome' => $request->get('nome'),
            'status' => $request->get('status'),
            'updated_at' => Tdata::TtimeStampHojeMysql(),
        );

        $dadosBD = DB::table("ala")->where('id', $id)->get();
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

        $insert = DB::table('ala')->where('id', $id)->update([
            'nome' => $request->get('nome'),
            'status' => $request->get('status'),
            'updated_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        if($id)
        {
            if(!empty($diferencas))
            {
                $textoLog = implode(" ", $diferencas);
                LogController::log($id,"ala","update", Auth::user()->cpf, $textoLog);
            }

            return redirect()->route('setic.unidadesListar')->with('alert-success', 'Dados Alterados com sucesso!');

        }else{

            return redirect()->route('setic.unidadesListar')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }

    }

    public function unidadesCelas($idAla)
    {
        $dados = DB::table('cela')
            ->where('fk_ala', $idAla)
            ->orderBy('nome', 'ASC')
            ->get();

        $nomeAla = UnidadesController::getNomeAla($idAla);

        return view('setic.unidades.listaCelas', compact('dados','nomeAla','idAla'));
    }

    public function unidadesCelasCadastrar($idAla)
    {
        return view('setic.unidades.celaCadastrar', compact('idAla'));

    }

    public function unidadesCelasCadastrarStore(Request $request, $idAla)
    {
        $insert = DB::table('cela')->insert([
            'nome' => $request->get('nome'),
            'capacidade' => $request->get('capacidade'),
            'fk_ala' => $idAla,
            'status' => 'A',
            'created_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        $idInsert = DB::getPdo()->lastInsertId();

        LogController::log($idInsert,"cela","add",Auth::user()->cpf,"Criação de cela para unidade.");

        if($idInsert)
        {
            return redirect()->route('setic.unidadesCelas', $idAla)->with('alert-success', 'Cela Cadastrada com sucesso!');

        }else{

            return redirect()->route('setic.unidadesListar')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }

    }

    public function unidadesCelasEditar($idCela)
    {
        $dados = DB::table('cela')->where('id', $idCela)->get();

        return view('setic.unidades.celaCadastrar', compact('idCela','dados'));

    }

    public function unidadesCelasEditarStore(Request $request, $idCela)
    {
        //-------------------- LOG -------------------------------------

        $status = $request->get('status');
        if($status == "I")
        {
            $checkCelaVazia = DB::table('interno')->where('cela', $idCela)->where('status','A')->count();

            if($checkCelaVazia >= 1)
            {
                return redirect()->back()->with('alert-danger', 'A cela não está vazia, não é possível inativá-la');
            }
        }
        // LOG - array que armazena os textos do log
        $diferencas = array();
        $alteracoes = "Alteração de Dados da Cela<br>";
        array_push($diferencas, $alteracoes);

        $dataForm = array(
            'nome' => $request->get('nome'),
            'status' => $request->get('status'),
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
            'nome' => $request->get('nome'),
            'status' => $request->get('status'),
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

            return redirect()->route('setic.unidadesListar')->with('alert-success', 'Dados Alterados com sucesso!');

        }else{

            return redirect()->route('setic.unidadesListar')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }

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

    public function buscarUnificacao(Request $request)
    {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if(!strpos($url,"?"))
        {
            $unificacoes = false;

        }else {
            $unificacoes = DB::table('unificacao');

            $tipo = $request->get('tipo');
            if ($tipo != null) {
                $unificacoes->where('fk_tabela', $tipo);
            }
            $idGerado = $request->get('gerado');
            if ($idGerado != null) {
                $unificacoes->where('fk_novo', $idGerado);
            }
            $idA = $request->get('principal');
            if ($idA != null) {
                $unificacoes->where('fk_A', $idA);
            }
            $idB = $request->get('secundario');
            if ($idB != null) {
                $unificacoes->where('fk_B', $idB);
            }
            $ate = $request->get('ate');
            $apos = $request->get('apos');

            if($apos != null && $ate != null){
                if(Tdata::TdataMysql($apos) > Tdata::TdataMysql($ate)){
                    return redirect()->route('setic.unificacao.buscar.buscar')->with('alert-danger', 'A data inicial não deve ser maior que a data final!');
                }
            }

            if ($apos != null){
                $unificacoes->where('unificacao.created_at', '>=', Tdata::TdataMysql($apos)." 00:00:00");
            }

            if($ate != null){
                $unificacoes->where('unificacao.created_at', '<=', Tdata::TdataMysql($ate)." 23:59:59");
            }

            $unificacoes = $unificacoes->orderBy('created_at','DESC')->get();
            foreach($unificacoes as $key=>$unificacao){
                $unificacoes[$key]->idGeradoStatus = InternoController::getStatus($unificacoes[$key]->fk_novo);
                $unificacoes[$key]->idAStatus = self::getStatusInternoUnificado($unificacoes[$key]->fk_A);
                $unificacoes[$key]->idBStatus = self::getStatusInternoUnificado($unificacoes[$key]->fk_B);
                $unificacoes[$key]->created_at = Tdata::TtimestampBR($unificacoes[$key]->created_at);
            }
            $unificacoes = collect($unificacoes)->paginate(50)->appends($request->input());
        }

        return view('setic.unificacao.buscar.buscar',compact('unificacoes'));
    }

//    // busca se existe o CPF $cpf na tabela $tabela em internos com id diferente de $idA e $idB
//    public static function existeCpf($cpf, $tabela, $idA, $idB){
//        $qtdCpf = DB::table($tabela)->where('cpf',$cpf)->where('id','!=',$idA)->where('id','!=',$idB)->count();
//        if($qtdCpf > 0){
//            return true;
//        }
//        else{
//            return false;
//        }
//    }

    // busca se o $tipo ('interno', 'visita', 'advogado') de ID $id passou pelo processo de unificação
    public static function existeUnificacao_id($tipo, $id){
        $qtd = DB::table('unificacao')->where('fk_novo',$id)->where('fk_tabela',$tipo)->count();
        if($qtd > 0){
            return true;
        }
        else{
            return false;
        }
    }

    // busca se existe Unificação de $tipo ('interno', 'visita', 'advogado') na tabela 'unificacao' entre os ids $idA e $idB
    public static function existeUnificacao($tipo, $idA, $idB){
        $qtdUni = DB::table('unificacao')->where('fk_A',$idA)->where('fk_B',$idB)->where('fk_tabela',$tipo)->count();
        if($qtdUni > 0){
            return true;
        }
        else{
            return false;
        }
    }

    public function unificacaoInterno(Request $request)
    {
        ini_set('max_execution_time',180);
        if(!is_null($request->get('idA')) && !is_null($request->get('idB'))){
            
            $pavilhao = $ala = $cela = null;

            $idA = $request->get('idA')[0];
            $idB = $request->get('idB')[0];

            $dadosA = DB::table('interno')->where('id',$idA)->get()[0];
            $dadosA->data_nascimento = Tdata::TdataBR($dadosA->data_nascimento);
            $dadosA->rg_data = Tdata::TdataBR($dadosA->rg_data);
            $dadosB = DB::table('interno')->where('id',$idB)->get()[0];
            $dadosB->data_nascimento = Tdata::TdataBR($dadosB->data_nascimento);
            $dadosB->rg_data = Tdata::TdataBR($dadosB->rg_data);

            $unidades = DB::table('unidade')->whereNotNull('troca')->orderBy('sigla', 'ASC')->get();
            $profissoes = DB::table('profissao')->orderBy('nome', 'ASC')->get();
            $faccoes = DB::table('faccao')->orderBy('nome','ASC')->get();
            $condicoes = DB::table('condicao')->orderBy('nome','ASC')->get();
            $naturalidadeMunicipios = DB::table('municipio')->orderBy('Nome', 'ASC')->get();

            $outrosnomesA = DB::table('outrosnomes')->where('pk_id',$idA)->where('pk_tabela','interno')->get();
            $outrosnomesB = DB::table('outrosnomes')->where('pk_id',$idB)->where('pk_tabela','interno')->get();

            $contatosA = DB::table('contato')->where('fk_id',$idA)->where('fk_tabela','interno')->get();
            $contatosB = DB::table('contato')->where('fk_id',$idB)->where('fk_tabela','interno')->get();

            $enderecosA = DB::table('endereco')->where('fk_id',$idA)->where('fk_tabela','interno')->get();
            $enderecosB = DB::table('endereco')->where('fk_id',$idB)->where('fk_tabela','interno')->get();

            $historico = DB::table('historico')->where('idinterno',$idA)->get()->toArray();
            $historico = array_merge($historico, DB::table('historico')->where('idinterno',$idB)->get()->toArray());
            $historico = collect($historico)->sortByDesc('data_hora');
            
            $movimentacao = DB::table('movimentacao_interno')->join('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')->select('movimentacao_interno.id as movintid','movimentacao_interno.*','movimentacao.*')->where('movimentacao_interno.fk_interno',$idA)->get()->toArray();
            $movimentacao = array_merge($movimentacao, DB::table('movimentacao_interno')->join('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')->select('movimentacao_interno.id as movintid','movimentacao_interno.*','movimentacao.*')->where('movimentacao_interno.fk_interno',$idB)->get()->toArray());
            $movimentacao = collect($movimentacao)->sortByDesc('data');
            
            $sindicancia = DB::table('sindicancia_interno')->join('sindicancia','sindicancia.id','=','sindicancia_interno.fk_sindicancia')->select('sindicancia_interno.id as sinintid','sindicancia_interno.*','sindicancia.*')->where('sindicancia_interno.fk_interno',$idA)->get()->toArray();
            $sindicancia = array_merge($sindicancia, DB::table('sindicancia_interno')->join('sindicancia','sindicancia.id','=','sindicancia_interno.fk_sindicancia')->select('sindicancia_interno.id as sinintid','sindicancia_interno.*','sindicancia.*')->where('sindicancia_interno.fk_interno',$idB)->get()->toArray());
            $sindicancia = collect($sindicancia)->sortByDesc('data_incidente');
            
            $ocorrencia = DB::table('ocorrencia_envolvidos')->join('ocorrencia','ocorrencia.id','=','ocorrencia_envolvidos.fk_ocorrencia')->select('ocorrencia_envolvidos.id as ocointid','ocorrencia_envolvidos.*','ocorrencia.*')->where('ocorrencia_envolvidos.fk_id_envolvido',$idA)->where('ocorrencia_envolvidos.fk_tabela','interno')->get()->toArray();
            $ocorrencia = array_merge($ocorrencia, DB::table('ocorrencia_envolvidos')->join('ocorrencia','ocorrencia.id','=','ocorrencia_envolvidos.fk_ocorrencia')->select('ocorrencia_envolvidos.id as ocointid','ocorrencia_envolvidos.*','ocorrencia.*')->where('ocorrencia_envolvidos.fk_id_envolvido',$idB)->where('ocorrencia_envolvidos.fk_tabela','interno')->get()->toArray());
            $ocorrencia = collect($ocorrencia)->sortByDesc('data')->sortByDesc('hora');
            
            $escolta = DB::table('escolta_internos')->join('escolta','escolta.id','=','escolta_internos.fk_escolta')->select('escolta_internos.id as escintid','escolta_internos.*','escolta.*')->where('escolta_internos.fk_interno',$idA)->get()->toArray();
            $escolta = array_merge($escolta, DB::table('escolta_internos')->join('escolta','escolta.id','=','escolta_internos.fk_escolta')->select('escolta_internos.id as escintid','escolta_internos.*','escolta.*')->where('escolta_internos.fk_interno',$idB)->get()->toArray());
            $escolta = collect($escolta)->sortByDesc('data_saida')->sortByDesc('hora_saida');
            
            $gep = DB::table('gep_internos')->join('gep','gep.id','=','gep_internos.fk_gep')->select('gep_internos.id as gepintid','gep_internos.*','gep.*')->where('gep_internos.fk_interno',$idA)->get()->toArray();
            $gep = array_merge($gep, DB::table('gep_internos')->join('gep','gep.id','=','gep_internos.fk_gep')->select('gep_internos.id as gepintid','gep_internos.*','gep.*')->where('gep_internos.fk_interno',$idB)->get()->toArray());
            $gep = collect($gep)->sortByDesc('data_saida')->sortByDesc('hora_saida');

            return view('setic.unificacao.cadastrar.join',compact('idA','idB','dadosA','dadosB','unidades','pavilhao','ala','cela','profissoes','faccoes','condicoes','naturalidadeMunicipios',
                'outrosnomesA','outrosnomesB','contatosA','contatosB','enderecosA','enderecosB','historico','movimentacao','sindicancia','ocorrencia','escolta','gep'));
        }
        else{
            return view('setic.unificacao.cadastrar.join');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unificacaoInternoStore(Request $request)
    {
        ini_set('max_execution_time',360);
        $idA = $request->get('idA');
        $idB = $request->get('idB');
        $outrosnomes = $request->only('outrosnomes');
        $outrosnomesTxt = $request->only('outrosnomesTxt');
        $contatos = $request->only('contatos');
        $contatosTxt = $request->only('contatosTxt');
        $enderecos = $request->only('enderecos');
        $enderecosTxt = $request->only('enderecosTxt');
        $historico = $request->only('historico');
        $movimentacao = $request->only('movimentacao');
        $sindicancia = $request->only('sindicancia');
        $ocorrencia = $request->only('ocorrencia');
        $escolta = $request->only('escolta');
        $gep = $request->only('gep');
        $dataFormInterno = $request->except('idA','idB','_token','outrosnomes','outrosnomesTxt','contatos','contatosTxt',
            'enderecos','enderecosTxt','historico','movimentacao','sindicancia','ocorrencia','escolta','gep');
        $dataFormInterno['updated_at'] = Tdata::TtimeStampHojeMysql();

        if(self::existeUnificacao('interno', $idA, $idB)){
            return redirect()->route('setic.unificacao.cadastrar.join')->with('alert-danger', 'Esta unificação já foi realizada.');
        }

//        if(self::existeCpf($dataFormInterno['cpf'], 'interno', $idA, $idB)){
//            return redirect()->route('setic.unificacao.cadastrar.join')->with('alert-danger', 'O CPF escolhido já está em uso, repita a unificação após corrigir os CPFs.');
//        }

        // interno
            $internoA = DB::table('interno')->where('id',$idA)->get()->first();
            $internoB = DB::table('interno')->where('id',$idB)->get()->first();

            $data = get_object_vars($internoA);
            $data['id_original'] = $idA;
            unset($data['id']);
            if($data['created_at'] == "0000-00-00 00:00:00"){
                $data['created_at'] = null;
            }
            $criacaoInterno = $data['created_at'];
            if($data['updated_at'] == "0000-00-00 00:00:00"){
                $data['updated_at'] = null;
            }
            DB::table('unificacao_interno')->insert($data);

            $data = get_object_vars($internoB);
            $data['id_original'] = $idB;
            unset($data['id']);
            if($data['created_at'] == "0000-00-00 00:00:00"){
                $data['created_at'] = null;
            }
            if(!is_null($criacaoInterno) && !is_null($data['created_at']) && ($criacaoInterno > $data['created_at'])){
                $criacaoInterno = $data['created_at'];
            }
            if($data['updated_at'] == "0000-00-00 00:00:00"){
                $data['updated_at'] = null;
            }
            DB::table('unificacao_interno')->insert($data);

            $data = get_object_vars($internoA);
            unset($data['id']);
            $data['created_at'] = $criacaoInterno;
            $data['updated_at'] = Tdata::TtimeStampHojeMysql();
            DB::table('interno')->insert($data);
            $id = DB::getPdo()->lastInsertId();

            $dataFormInterno['data_nascimento'] = Tdata::TdataMysql($dataFormInterno['data_nascimento']);
            if($dataFormInterno['data_nascimento'] == "") $dataFormInterno['data_nascimento'] = null;
            $dataFormInterno['rg_data'] = Tdata::TdataMysql($dataFormInterno['rg_data']);
            if($dataFormInterno['rg_data'] == "") $dataFormInterno['rg_data'] = null;

            DB::table('interno')->where('id',$id)->update($dataFormInterno);

            DB::table('interno')->where('id',$idA)->delete();
            DB::table('interno')->where('id',$idB)->delete();

            //salva log do A na tabela unificacao_log
            $listaLogA = DB::table('log')->where('fk_id',$idA)->where('tabela_alterada','interno')->get();
            foreach($listaLogA as $itemA){
                $data = get_object_vars($itemA);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['updated_at'] == "0000-00-00 00:00:00"){
                    $data['updated_at'] = null;
                }
                DB::table('unificacao_log')->insert($data);
            }

            //salva log do B na tabela unificacao_log
            $listaLogB = DB::table('log')->where('fk_id',$idB)->where('tabela_alterada','interno')->get();
            foreach($listaLogB as $itemB){
                $data = get_object_vars($itemB);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['updated_at'] == "0000-00-00 00:00:00"){
                    $data['updated_at'] = null;
                }
                DB::table('unificacao_log')->insert($data);
            }

            //transforma log do A e B no novo
            DB::table('log')->where('fk_id',$idA)->where('tabela_alterada','interno')->update([
                'fk_id' => $id
            ]);
            DB::table('log')->where('fk_id',$idB)->where('tabela_alterada','interno')->update([
                'fk_id' => $id
            ]);

            //mantem o log de criação de interno mais antigo
            $listaLogCriacao = DB::table('log')->where('fk_id',$id)->where('tabela_alterada','interno')->where('tipo_alteracao','add')->orderBy('created_at','ASC')->get();
            if(count($listaLogCriacao) > 1){
                foreach($listaLogCriacao as $item){
                    if($item->created_at != '' and !is_null($item->created_at)) {
                        DB::table('log')->where('fk_id', $id)->where('tabela_alterada', 'interno')->where('tipo_alteracao', 'add')->where('id', '!=', $item->id)->delete();
                        break;
                    }
                }
            }


        // documentos e fotos do interno
            // documentos
            if(Storage::exists(Constantes::ARVQUIVOS_INTERNO . "/documentos/" . $idA)){
                Storage::move(Constantes::ARVQUIVOS_INTERNO . "/documentos/" . $idA, Constantes::ARVQUIVOS_INTERNO . "/documentos/" . $id);
            }
            if(Storage::exists(Constantes::ARVQUIVOS_INTERNO . "/documentos/" . $id)){
                foreach(Storage::allFiles(Constantes::ARVQUIVOS_INTERNO . "/documentos/" . $idB) as $file){
                    Storage::move($file, Constantes::ARVQUIVOS_INTERNO . "/documentos/". $id ."/" . basename($file));
                }
                if(Storage::exists(Constantes::ARVQUIVOS_INTERNO . "/documentos/" . $idB)) {
                    Storage::deleteDirectory(Constantes::ARVQUIVOS_INTERNO . "/documentos/" . $idB);
                }
            }
            else if(Storage::exists(Constantes::ARVQUIVOS_INTERNO . "/documentos/" . $idB)){
                Storage::move(Constantes::ARVQUIVOS_INTERNO . "/documentos/" . $idB, Constantes::ARVQUIVOS_INTERNO . "/documentos/" . $id);
            }

            // docLog A
            $docLogA = DB::table('log')->join('documento','documento.id','=','log.fk_id')->select('log.id')->where('documento.fk_id',$idA)->where('documento.fk_tabela','interno')->where('log.tabela_alterada','documento')->get();
            foreach($docLogA as $itemA){
                $data = DB::table('log')->where('id',$itemA->id)->get()[0];
                $data = get_object_vars($data);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['updated_at'] == "0000-00-00 00:00:00"){
                    $data['updated_at'] = null;
                }
                DB::table('unificacao_log')->insert($data);
            }

            // docLog B
            $docLogB = DB::table('log')->join('documento','documento.id','=','log.fk_id')->select('log.id')->where('documento.fk_id',$idB)->where('documento.fk_tabela','interno')->where('log.tabela_alterada','documento')->get();
            foreach($docLogB as $itemB){
                $data = DB::table('log')->where('id',$itemB->id)->get()[0];
                $data = get_object_vars($data);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['updated_at'] == "0000-00-00 00:00:00"){
                    $data['updated_at'] = null;
                }
                DB::table('unificacao_log')->insert($data);
            }

            DB::table('documento')->where('fk_id',$idA)->where('fk_tabela','interno')->update([
                    'fk_id' => $id
            ]);
            DB::table('documento')->where('fk_id',$idB)->where('fk_tabela','interno')->update([
                    'fk_id' => $id
            ]);

            // arquivos
            if(Storage::exists(Constantes::ARVQUIVOS_INTERNO . "/fotos/" . $idA)) {
                Storage::move(Constantes::ARVQUIVOS_INTERNO . "/fotos/" . $idA, Constantes::ARVQUIVOS_INTERNO . "/fotos/" . $id);
            }
            if(Storage::exists(Constantes::ARVQUIVOS_INTERNO . "/fotos/" . $id)){
                foreach(Storage::allFiles(Constantes::ARVQUIVOS_INTERNO . "/fotos/" . $idB) as $file){
                    Storage::move($file, Constantes::ARVQUIVOS_INTERNO . "/fotos/". $id ."/" . basename($file));
                }
                if(Storage::exists(Constantes::ARVQUIVOS_INTERNO . "/fotos/" . $idB)) {
                    Storage::deleteDirectory(Constantes::ARVQUIVOS_INTERNO . "/fotos/" . $idB);
                }
            }
            else if(Storage::exists(Constantes::ARVQUIVOS_INTERNO . "/fotos/" . $idB)){
                Storage::move(Constantes::ARVQUIVOS_INTERNO . "/fotos/" . $idB, Constantes::ARVQUIVOS_INTERNO . "/fotos/" . $id);
            }

            // fotoLogA
            $fotoLogA = DB::table('log')->join('foto','foto.id','=','log.fk_id')->select('log.id')->where('foto.fk_id',$idA)->where('foto.fk_tabela','interno')->where('log.tabela_alterada','foto')->get();
            foreach($fotoLogA as $itemA){
                $data = DB::table('log')->where('id',$itemA->id)->get()[0];
                $data = get_object_vars($data);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['updated_at'] == "0000-00-00 00:00:00"){
                    $data['updated_at'] = null;
                }
                DB::table('unificacao_log')->insert($data);
            }

            // fotoLogB
            $fotoLogB = DB::table('log')->join('foto','foto.id','=','log.fk_id')->select('log.id')->where('foto.fk_id',$idB)->where('foto.fk_tabela','interno')->where('log.tabela_alterada','foto')->get();
            foreach($fotoLogB as $itemB){
                $data = DB::table('log')->where('id',$itemB->id)->get()[0];
                $data = get_object_vars($data);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['updated_at'] == "0000-00-00 00:00:00"){
                    $data['updated_at'] = null;
                }
                DB::table('unificacao_log')->insert($data);
            }

            DB::table('foto')->where('fk_id',$idA)->where('fk_tabela','interno')->update([
                'fk_id' => $id
            ]);
            DB::table('foto')->where('fk_id',$idB)->where('fk_tabela','interno')->where('tipo','principal')->update([
                'tipo' => 'outros'
            ]);
            DB::table('foto')->where('fk_id',$idB)->where('fk_tabela','interno')->update([
                'fk_id' => $id
            ]);


        // unificacao
            DB::table('unificacao')->insert([
                'fk_novo' => $id,
                'fk_A' => $idA,
                'fk_B' => $idB,
                'fk_tabela' => 'interno',
                'created_at' => Tdata::TtimeStampHojeMysql()
            ]);
            $idUnificacao = DB::getPdo()->lastInsertId();


        // outrosnomes
            // salva outrosnomes do A na tabela unificacao_outrosnomes
            $listaOutrosNomesA = DB::table('outrosnomes')->where('pk_id',$idA)->where('pk_tabela','interno')->get();
            foreach($listaOutrosNomesA as $itemA){
                $data = get_object_vars($itemA);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                DB::table('unificacao_outrosnomes')->insert($data);
            }

            // salva outrosnomes do B na tabela unificacao_outrosnomes
            $listaOutrosNomesB = DB::table('outrosnomes')->where('pk_id',$idB)->where('pk_tabela','interno')->get();
            foreach($listaOutrosNomesB as $itemB) {
                $data = get_object_vars($itemB);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                DB::table('unificacao_outrosnomes')->insert($data);
            }

            // transforma os outrosnomes de A e B no novo
            DB::table('outrosnomes')->where('pk_id',$idA)->where('pk_tabela','interno')->update([
                'pk_id' => $id
            ]);
            DB::table('outrosnomes')->where('pk_id',$idB)->where('pk_tabela','interno')->update([
                'pk_id' => $id
            ]);

            // altera outrosnomes
            if(isset($outrosnomesTxt['outrosnomesTxt'])) {
                foreach ($outrosnomesTxt['outrosnomesTxt'] as $key => $nome) {
                    DB::table('outrosnomes')->where('id', $key)->update([
                        'nome' => $nome
                    ]);
                }
            }

            // retira os outrosnomes do novo que não estejam marcados em checkbox
            $listaOutrosNomes = DB::table('outrosnomes')->where('pk_id',$id)->where('pk_tabela','interno')->get();
            foreach($listaOutrosNomes as $item) {
                if(isset($outrosnomes['outrosnomes']) && !in_array($item->id,$outrosnomes['outrosnomes'])){
                    DB::table('outrosnomes')->where('id',$item->id)->delete();
                }
            }
            if(isset($outrosnomes['outrosnomes']) == false){
                DB::table('outrosnomes')->where('pk_id',$id)->where('pk_tabela','interno')->delete();
            }


        // contato
            // salva contato do A na tabela unificacao_contato
            $listacontatoA = DB::table('contato')->where('fk_id',$idA)->where('fk_tabela','interno')->get();
            foreach($listacontatoA as $itemA){
                $data = get_object_vars($itemA);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['updated_at'] == "0000-00-00 00:00:00"){
                    $data['updated_at'] = null;
                }
                DB::table('unificacao_contato')->insert($data);
            }

            // salva contato do B na tabela unificacao_contato
            $listacontatoB = DB::table('contato')->where('fk_id',$idB)->where('fk_tabela','interno')->get();
            foreach($listacontatoB as $itemB) {
                $data = get_object_vars($itemB);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['updated_at'] == "0000-00-00 00:00:00"){
                    $data['updated_at'] = null;
                }
                DB::table('unificacao_contato')->insert($data);
            }

            // transforma os contato de A e B no novo
            DB::table('contato')->where('fk_id',$idA)->where('fk_tabela','interno')->update([
                'fk_id' => $id
            ]);
            DB::table('contato')->where('fk_id',$idB)->where('fk_tabela','interno')->update([
                'fk_id' => $id
            ]);

            // atualiza os contatos
            if(isset($contatosTxt['contatosTxt'])) {
                foreach ($contatosTxt['contatosTxt'] as $key => $num) {
                    DB::table('contato')->where('id', $key)->update([
                        'numero' => $num
                    ]);
                }
            }

            // retira os contatos do novo que não estejam marcados em checkbox
            $listacontato = DB::table('contato')->where('fk_id',$id)->where('fk_tabela','interno')->get();
            foreach($listacontato as $item) {
                if(isset($contatos['contatos']) && !in_array($item->id,$contatos['contatos'])){
                    DB::table('contato')->where('id',$item->id)->delete();
                }
            }
            if(isset($contatos['contatos']) == false){
                DB::table('contato')->where('fk_id',$id)->where('fk_tabela','interno')->delete();
            }



        // endereco
            // salva endereco do A na tabela unificacao_endereco
            $listaenderecoA = DB::table('endereco')->where('fk_id',$idA)->where('fk_tabela','interno')->get();
            foreach($listaenderecoA as $itemA){
                $data = get_object_vars($itemA);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['updated_at'] == "0000-00-00 00:00:00"){
                    $data['updated_at'] = null;
                }
                DB::table('unificacao_endereco')->insert($data);
            }

            // salva endereco do B na tabela unificacao_endereco
            $listaenderecoB = DB::table('endereco')->where('fk_id',$idB)->where('fk_tabela','interno')->get();
            foreach($listaenderecoB as $itemB) {
                $data = get_object_vars($itemB);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['updated_at'] == "0000-00-00 00:00:00"){
                    $data['updated_at'] = null;
                }
                DB::table('unificacao_endereco')->insert($data);
            }

            // transforma os endereco de A e B no novo
            DB::table('endereco')->where('fk_id',$idA)->where('fk_tabela','interno')->update([
                'fk_id' => $id
            ]);
            DB::table('endereco')->where('fk_id',$idB)->where('fk_tabela','interno')->update([
                'fk_id' => $id
            ]);

            // atualiza os endereços
            if(isset($enderecosTxt['enderecosTxt'])) {
                foreach ($enderecosTxt['enderecosTxt'] as $key => $end) {
                    DB::table('endereco')->where('id', $key)->update([
                        'pais' => $end['pais'],
                        'estado' => $end['estado'],
                        'cidade' => $end['cidade'],
                        'bairro' => $end['bairro'],
                        'cep' => $end['cep'],
                        'logradouro' => $end['logradouro'],
                        'numero' => $end['numero'],
                        'complemento' => $end['complemento'],
                        'latitude' => $end['latitude'],
                        'longitude' => $end['longitude']
                    ]);
                }
            }

            // retira os endereços do novo que não estejam marcados em checkbox
            $listaendereco = DB::table('endereco')->where('fk_id',$id)->where('fk_tabela','interno')->get();
            foreach($listaendereco as $item) {
                if(isset($enderecos['enderecos']) && !in_array($item->id,$enderecos['enderecos'])){
                    DB::table('endereco')->where('id',$item->id)->delete();
                }
            }
            if(isset($enderecos['enderecos']) == false){
                DB::table('endereco')->where('fk_id',$id)->where('fk_tabela','interno')->delete();
            }


        // historico
            //salva historico do A na tabela unificacao_historico
            $listaHistoricoA = DB::table('historico')->where('idinterno',$idA)->get();
            foreach($listaHistoricoA as $itemA){
                $data = get_object_vars($itemA);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['data_hora'] == "0000-00-00"){
                    $data['data_hora'] = null;
                }
                DB::table('unificacao_historico')->insert($data);
            }

            //salva historico do B na tabela unificacao_historico
            $listaHistoricoB = DB::table('historico')->where('idinterno',$idB)->get();
            foreach($listaHistoricoB as $itemB){
                $data = get_object_vars($itemB);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['data_hora'] == "0000-00-00"){
                    $data['data_hora'] = null;
                }
                DB::table('unificacao_historico')->insert($data);
            }

            //transforma historico do A e B no novo
            DB::table('historico')->where('idinterno',$idA)->update([
                'idinterno' => $id
            ]);
            DB::table('historico')->where('idinterno',$idB)->update([
                'idinterno' => $id
            ]);

//            // retira o historico do novo que não esteja marcado por checkbox
//            $listaHistorico = DB::table('historico')->where('idinterno',$id)->get();
//            foreach($listaHistorico as $item) {
//                if(isset($historico['historico']) && !in_array($item->id,$historico['historico'])){
//                    DB::table('historico')->where('idinterno',$item->id)->delete();
//                }
//            }


        // movimentacao
            //salva movimentacao do A na tabela unificacao_movimentacao
            $listamovimentacaoA = DB::table('movimentacao_interno')->where('fk_interno',$idA)->get();
            foreach($listamovimentacaoA as $itemA){
                $data = get_object_vars($itemA);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                DB::table('unificacao_movimentacao_interno')->insert($data);
            }

            //salva movimentacao do B na tabela unificacao_movimentacao
            $listamovimentacaoB = DB::table('movimentacao_interno')->where('fk_interno',$idB)->get();
            foreach($listamovimentacaoB as $itemB){
                $data = get_object_vars($itemB);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                DB::table('unificacao_movimentacao_interno')->insert($data);
            }

            //transforma movimentacao do A e B no novo
            DB::table('movimentacao_interno')->where('fk_interno',$idA)->update([
                'fk_interno' => $id
            ]);
            DB::table('movimentacao_interno')->where('fk_interno',$idB)->update([
                'fk_interno' => $id
            ]);

//            // retira o movimentacao_interno do novo que não esteja marcado por checkbox
//            $listamovimentacaointerno = DB::table('movimentacao_interno')->where('fk_interno',$id)->get();
//            foreach($listamovimentacaointerno as $item) {
//                if(!in_array($item->id,$movimentacao['movimentacao'])){
//                    $idMovTemp = $item->fk_movimentacao;
//                    $idMovIntTemp = $item->fk_movimentacao; // falta corrigir o valor dessa variável
//                    DB::table('movimentacao_interno')->where('id',$item->id)->delete();
//                    // copia o log de movimentacao_interno para unificacao_log e apaga log da movimentacao_interno
//                    $logCopy = DB::table('log')->where('tabela_alterada','movimentacao_interno')->where('fk_id',$idMovIntTemp)->get();
//                    foreach($logCopy as $log){
//                        $data = get_object_vars($log);
//                        $data['id_original'] = $data['id'];
//                        unset($data['id']);
//                        DB::table('unificacao_log')->insert($data);
//                    }
//                    DB::table('log')->where('tabela_alterada','movimentacao_interno')->where('fk_id',$idMovIntTemp)->delete();
//
//                    //apaga a movimentação se o movimentacao_interno deletado for o último
//                    if(DB::table('movimentacao')->where('id',$idMovTemp)->count() > 0 && DB::table('movimentacao_interno')->where('fk_movimentacao',$idMovTemp)->count() == 0){
//                        //salva movimentacao na tabela unificacao_movimentacao
//                        $movCopy = DB::table('movimentacao')->where('id',$idMovTemp)->get()->first();
//                        DB::table('unificacao_movimentacao')->insert(get_object_vars($movCopy));
//
//                        //salva o log de movimentacao na tabela unificacao_log
//                        $logCopy = DB::table('log')->where('tabela_alterada','movimentacao')->where('fk_id',$idMovTemp)->get();
//                        foreach($logCopy as $log){
//                            $data = get_object_vars($log);
//                            $data['id_original'] = $data['id'];
//                            unset($data['id']);
//                            DB::table('unificacao_log')->insert($data);
//                        }
//
//                        // apaga a movimentação e log
//                        DB::table('movimentacao')->where('id',$idMovTemp)->delete();
//                        DB::table('log')->where('tabela_alterada','movimentacao')->where('fk_id',$idMovTemp)->delete();
//                    }
//                }
//            }


        // Óbito
            // salva cópia na tabela unificacao_obito
            $listaobitoA = DB::table('obito')->where('interno',$idA)->get();
            foreach($listaobitoA as $itemA){
                $data = get_object_vars($itemA);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['data_obito'] == "0000-00-00"){
                    $data['data_obito'] = null;
                }
                DB::table('unificacao_obito')->insert($data);
            }
            $listaobitoB = DB::table('obito')->where('interno',$idB)->get();
            foreach($listaobitoB as $itemB){
                $data = get_object_vars($itemB);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['data_obito'] == "0000-00-00"){
                    $data['data_obito'] = null;
                }
                DB::table('unificacao_obito')->insert($data);
            }
            // log
            $obitoLogA = DB::table('log')->join('obito','obito.id','=','log.fk_id')->select('log.id')->where('obito.interno',$idA)->where('log.tabela_alterada','obito')->get();
            foreach($obitoLogA as $itemA){
                $data = DB::table('log')->where('id',$itemA->id)->get()[0];
                $data = get_object_vars($data);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['updated_at'] == "0000-00-00 00:00:00"){
                    $data['updated_at'] = null;
                }
                DB::table('unificacao_log')->insert($data);
            }

            $obitoLogB = DB::table('log')->join('obito','obito.id','=','log.fk_id')->select('log.id')->where('obito.interno',$idB)->where('log.tabela_alterada','obito')->get();
            foreach($obitoLogB as $itemB){
                $data = DB::table('log')->where('id',$itemB->id)->get()[0];
                $data = get_object_vars($data);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['updated_at'] == "0000-00-00 00:00:00"){
                    $data['updated_at'] = null;
                }
                DB::table('unificacao_log')->insert($data);
            }
            // alterar para novo
            DB::table('obito')->where('interno',$idA)->update([
                'interno' => $id
            ]);
            DB::table('obito')->where('interno',$idB)->update([
                'interno' => $id
            ]);

        // Sindicância
            //salva sindicancia_interno do A na tabela unificacao_sindicancia_interno
            $listaSindicanciaA = DB::table('sindicancia_interno')->where('fk_interno',$idA)->get();
            foreach($listaSindicanciaA as $itemA){
                $data = get_object_vars($itemA);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                DB::table('unificacao_sindicancia_interno')->insert($data);
            }

            //salva sindicancia_interno do B na tabela unificacao_sindicancia_interno
            $listaSindicanciaB = DB::table('sindicancia_interno')->where('fk_interno',$idB)->get();
            foreach($listaSindicanciaB as $itemB){
                $data = get_object_vars($itemB);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                DB::table('unificacao_sindicancia_interno')->insert($data);
            }

            //transforma sindicancia_interno do A e B no novo
            DB::table('sindicancia_interno')->where('fk_interno',$idA)->update([
                'fk_interno' => $id
            ]);
            DB::table('sindicancia_interno')->where('fk_interno',$idB)->update([
                'fk_interno' => $id
            ]);


        // Ocorrência
            //salva ocorrencia_envolvidos do A na tabela unificacao_ocorrencia_envolvidos
            $listaOcorrenciaA = DB::table('ocorrencia_envolvidos')->where('fk_id_envolvido',$idA)->where('fk_tabela','interno')->get();
            foreach($listaOcorrenciaA as $itemA){
                $data = get_object_vars($itemA);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                DB::table('unificacao_ocorrencia_envolvidos')->insert($data);
            }

            //salva ocorrencia_envolvidos do B na tabela unificacao_ocorrencia_envolvidos
            $listaOcorrenciaB = DB::table('ocorrencia_envolvidos')->where('fk_id_envolvido',$idB)->where('fk_tabela','interno')->get();
            foreach($listaOcorrenciaB as $itemB){
                $data = get_object_vars($itemB);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                DB::table('unificacao_ocorrencia_envolvidos')->insert($data);
            }

            //transforma ocorrencia_envolvidos do A e B no novo
            DB::table('ocorrencia_envolvidos')->where('fk_id_envolvido',$idA)->where('fk_tabela','interno')->update([
                'fk_id_envolvido' => $id
            ]);
            DB::table('ocorrencia_envolvidos')->where('fk_id_envolvido',$idB)->where('fk_tabela','interno')->update([
                'fk_id_envolvido' => $id
            ]);


        // Escolta
            //salva escolta_internos do A na tabela unificacao_escolta_internos
            $listaEscoltaA = DB::table('escolta_internos')->where('fk_interno',$idA)->get();
            foreach($listaEscoltaA as $itemA){
                $data = get_object_vars($itemA);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                DB::table('unificacao_escolta_internos')->insert($data);
            }

            //salva escolta_internos do B na tabela unificacao_escolta_internos
            $listaEscoltaB = DB::table('escolta_internos')->where('fk_interno',$idB)->get();
            foreach($listaEscoltaB as $itemB){
                $data = get_object_vars($itemB);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                DB::table('unificacao_escolta_internos')->insert($data);
            }

            //transforma escolta_internos do A e B no novo
            DB::table('escolta_internos')->where('fk_interno',$idA)->update([
                'fk_interno' => $id
            ]);
            DB::table('escolta_internos')->where('fk_interno',$idB)->update([
                'fk_interno' => $id
            ]);


        // FTPEN
            //salva gep_internos do A na tabela unificacao_gep_internos
            $listagepA = DB::table('gep_internos')->where('fk_interno',$idA)->get();
            foreach($listagepA as $itemA){
                $data = get_object_vars($itemA);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                DB::table('unificacao_gep_internos')->insert($data);
            }

            //salva gep_internos do B na tabela unificacao_gep_internos
            $listagepB = DB::table('gep_internos')->where('fk_interno',$idB)->get();
            foreach($listagepB as $itemB){
                $data = get_object_vars($itemB);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                DB::table('unificacao_gep_internos')->insert($data);
            }

            //transforma gep_internos do A e B no novo
            DB::table('gep_internos')->where('fk_interno',$idA)->update([
                'fk_interno' => $id
            ]);
            DB::table('gep_internos')->where('fk_interno',$idB)->update([
                'fk_interno' => $id
            ]);


        // vínculos, entradas e saídas
            // controle_acesso_visita_interno
            $controle_acesso_visita_internoLogA = DB::table('log')->join('controle_acesso_visita_interno','controle_acesso_visita_interno.id','=','log.fk_id')->select('log.id')->where('controle_acesso_visita_interno.fk_interno',$idA)->where('log.tabela_alterada','controle_acesso_visita_interno')->get();
            foreach($controle_acesso_visita_internoLogA as $itemA){
                $data = DB::table('log')->where('id',$itemA->id)->get()[0];
                $data = get_object_vars($data);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['updated_at'] == "0000-00-00 00:00:00"){
                    $data['updated_at'] = null;
                }
                DB::table('unificacao_log')->insert($data);
            }

            $controle_acesso_visita_internoLogB = DB::table('log')->join('controle_acesso_visita_interno','controle_acesso_visita_interno.id','=','log.fk_id')->select('log.id')->where('controle_acesso_visita_interno.fk_interno',$idB)->where('log.tabela_alterada','controle_acesso_visita_interno')->get();
            foreach($controle_acesso_visita_internoLogB as $itemB){
                $data = DB::table('log')->where('id',$itemB->id)->get()[0];
                $data = get_object_vars($data);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['updated_at'] == "0000-00-00 00:00:00"){
                    $data['updated_at'] = null;
                }
                DB::table('unificacao_log')->insert($data);
            }

            $caVisIntA = DB::table('controle_acesso_visita_interno')->where('fk_interno',$idA)->get();
            foreach($caVisIntA as $itemA){
                $data = get_object_vars($itemA);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['data'] == "0000-00-00"){
                    $data['data'] = null;
                }
                DB::table('unificacao_controle_acesso_visita_interno')->insert($data);
            }
            DB::table('controle_acesso_visita_interno')->where('fk_interno',$idA)->update([
                'fk_interno' => $id
            ]);

            $caVisIntB = DB::table('controle_acesso_visita_interno')->where('fk_interno',$idB)->get();
            foreach($caVisIntB as $itemB){
                $data = get_object_vars($itemB);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['data'] == "0000-00-00"){
                    $data['data'] = null;
                }
                DB::table('unificacao_controle_acesso_visita_interno')->insert($data);
            }
            DB::table('controle_acesso_visita_interno')->where('fk_interno',$idB)->update([
                'fk_interno' => $id
            ]);

            // controle_acesso_visita_interno_suspenso
            $controle_acesso_visita_interno_suspensoLogA = DB::table('log')->join('controle_acesso_visita_interno_suspenso','controle_acesso_visita_interno_suspenso.id','=','log.fk_id')->select('log.id')->where('controle_acesso_visita_interno_suspenso.fk_interno',$idA)->where('log.tabela_alterada','controle_acesso_visita_interno_suspenso')->get();
            foreach($controle_acesso_visita_interno_suspensoLogA as $itemA){
                $data = DB::table('log')->where('id',$itemA->id)->get()[0];
                $data = get_object_vars($data);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['updated_at'] == "0000-00-00 00:00:00"){
                    $data['updated_at'] = null;
                }
                DB::table('unificacao_log')->insert($data);
            }

            $controle_acesso_visita_interno_suspensoLogB = DB::table('log')->join('controle_acesso_visita_interno_suspenso','controle_acesso_visita_interno_suspenso.id','=','log.fk_id')->select('log.id')->where('controle_acesso_visita_interno_suspenso.fk_interno',$idB)->where('log.tabela_alterada','controle_acesso_visita_interno_suspenso')->get();
            foreach($controle_acesso_visita_interno_suspensoLogB as $itemB){
                $data = DB::table('log')->where('id',$itemB->id)->get()[0];
                $data = get_object_vars($data);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['updated_at'] == "0000-00-00 00:00:00"){
                    $data['updated_at'] = null;
                }
                DB::table('unificacao_log')->insert($data);
            }

            $caVisIntSusA = DB::table('controle_acesso_visita_interno_suspenso')->where('fk_interno',$idA)->get();
            foreach($caVisIntSusA as $itemA){
                $data = get_object_vars($itemA);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['data'] == "0000-00-00"){
                    $data['data'] = null;
                }
                DB::table('unificacao_controle_acesso_visita_interno_suspenso')->insert($data);
            }
            DB::table('controle_acesso_visita_interno_suspenso')->where('fk_interno',$idA)->update([
                'fk_interno' => $id
            ]);
            $caVisIntSusB = DB::table('controle_acesso_visita_interno_suspenso')->where('fk_interno',$idB)->get();
            foreach($caVisIntSusB as $itemB){
                $data = get_object_vars($itemB);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['data'] == "0000-00-00"){
                    $data['data'] = null;
                }
                DB::table('unificacao_controle_acesso_visita_interno_suspenso')->insert($data);
            }
            DB::table('controle_acesso_visita_interno_suspenso')->where('fk_interno',$idB)->update([
                'fk_interno' => $id
            ]);

            // visita_interno
            $VisIntA = DB::table('visita_interno')->where('fk_interno',$idA)->get();
            foreach($VisIntA as $itemA){
                $data = get_object_vars($itemA);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['update_at'] == "0000-00-00 00:00:00"){
                    $data['update_at'] = null;
                }
                DB::table('unificacao_visita_interno')->insert($data);
            }
            DB::table('visita_interno')->where('fk_interno',$idA)->update([
                'fk_interno' => $id
            ]);
    
            $VisIntB = DB::table('visita_interno')->where('fk_interno',$idB)->get();
            foreach($VisIntB as $itemB){
                $data = get_object_vars($itemB);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['update_at'] == "0000-00-00 00:00:00"){
                    $data['update_at'] = null;
                }
                DB::table('unificacao_visita_interno')->insert($data);
            }
    
            $VisIntB = DB::table('visita_interno')->where('fk_interno',$idB)->get();
            foreach($VisIntB as $itemB){
                $itemB_VisInt = DB::table('visita_interno')->where('fk_interno',$id)->where('fk_visita',$itemB->fk_visita)->get();
                if($itemB_VisInt != collect([])){
                    $itemB_VisInt = $itemB_VisInt[0];
                    $status = $itemB_VisInt->status;
    
                    $grau_parentesco = $itemB_VisInt->grau_parentesco;
                    if(is_null($grau_parentesco)){
                        $grau_parentesco = $itemB->grau_parentesco;
                    }
    
                    if($itemB->created_at == "0000-00-00 00:00:00"){
                        $itemB->created_at = null;
                    }
                    if($itemB_VisInt->created_at == "0000-00-00 00:00:00"){
                        $itemB_VisInt->created_at = null;
                    }
    
                    if(is_null($itemB_VisInt->created_at) && !is_null($itemB->created_at)){
                        $created_at = $itemB->created_at;
                    }
                    else if(!is_null($itemB_VisInt->created_at) && is_null($itemB->created_at)){
                        $created_at = $itemB_VisInt->created_at;
                    }
                    else if(!is_null($itemB_VisInt->created_at) && !is_null($itemB->created_at) && $itemB_VisInt->created_at <= $itemB->created_at){
                        $created_at = $itemB_VisInt->created_at;
                    }
                    else if(!is_null($itemB_VisInt->created_at) && !is_null($itemB->created_at) && $itemB_VisInt->created_at >= $itemB->created_at){
                        $created_at = $itemB->created_at;
                    }
                    else{
                        $created_at = null;
                    }
    
                    DB::table('visita_interno')->where('id',$itemB_VisInt->id)->update([
                        'status' => $status,
                        'grau_parentesco' => $grau_parentesco,
                        'created_at' => $created_at,
                        'update_at' => Tdata::TtimeStampHojeMysql()
                    ]);
                }
                else{
                    DB::table('visita_interno')->where('id',$itemB->id)->update([
                        'fk_interno' => $id
                    ]);
                }
            }
            DB::table('visita_interno')->where('fk_interno',$idB)->delete();

        
//            $VisIntA = DB::table('visita_interno')->where('fk_interno',$idA)->get();
//            foreach($VisIntA as $itemA){
//            foreach($VisIntA as $itemA){
//                $data = get_object_vars($itemA);
//                $data['id_original'] = $data['id'];
//                unset($data['id']);
//                if($data['created_at'] == "0000-00-00 00:00:00"){
//                    $data['created_at'] = null;
//                }
//                if($data['update_at'] == "0000-00-00 00:00:00"){
//                    $data['update_at'] = null;
//                }
//                DB::table('unificacao_visita_interno')->insert($data);
//            }
//            DB::table('visita_interno')->where('fk_interno',$idA)->update([
//                'fk_interno' => $id
//            ]);
//            $VisIntB = DB::table('visita_interno')->where('fk_interno',$idB)->get();
//            foreach($VisIntB as $itemB){
//                $data = get_object_vars($itemB);
//                $data['id_original'] = $data['id'];
//                unset($data['id']);
//                if($data['created_at'] == "0000-00-00 00:00:00"){
//                    $data['created_at'] = null;
//                }
//                if($data['update_at'] == "0000-00-00 00:00:00"){
//                    $data['update_at'] = null;
//                }
//                DB::table('unificacao_visita_interno')->insert($data);
//            }
//            DB::table('visita_interno')->where('fk_interno',$idB)->update([
//                'fk_interno' => $id
//            ]);

            // advogado_interno
            $AdvIntA = DB::table('advogado_interno')->where('fk_interno',$idA)->get();
            foreach($AdvIntA as $itemA){
                $data = get_object_vars($itemA);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['update_at'] == "0000-00-00 00:00:00"){
                    $data['update_at'] = null;
                }
                DB::table('unificacao_advogado_interno')->insert($data);
            }
            DB::table('advogado_interno')->where('fk_interno',$idA)->update([
                'fk_interno' => $id
            ]);

            $AdvIntB = DB::table('advogado_interno')->where('fk_interno',$idB)->get();
            foreach($AdvIntB as $itemB){
                $data = get_object_vars($itemB);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00 00:00:00"){
                    $data['created_at'] = null;
                }
                if($data['update_at'] == "0000-00-00 00:00:00"){
                    $data['update_at'] = null;
                }
                DB::table('unificacao_advogado_interno')->insert($data);
            }

            $AdvIntB = DB::table('advogado_interno')->where('fk_interno',$idB)->get();
            foreach($AdvIntB as $itemB){
                $itemB_AdvInt = DB::table('advogado_interno')->where('fk_interno',$id)->where('fk_advogado',$itemB->fk_advogado)->get();
                if($itemB_AdvInt != collect([])){
                    $itemB_AdvInt = $itemB_AdvInt[0];
                    $status = $itemB_AdvInt->status;
                    
                    $observacao = "";
                    if(!is_null($itemB_AdvInt->observacao)){
                        $observacao = $observacao.$itemB_AdvInt->observacao.". ";
                    }
                    if(!is_null($itemB->observacao)){
                        $observacao = $observacao.$itemB->observacao;                        
                    }

                    if($itemB->created_at == "0000-00-00 00:00:00"){
                        $itemB->created_at = null;
                    }
                    if($itemB_AdvInt->created_at == "0000-00-00 00:00:00"){
                        $itemB_AdvInt->created_at = null;
                    }

                    if(is_null($itemB_AdvInt->created_at) && !is_null($itemB->created_at)){
                        $created_at = $itemB->created_at;
                    }
                    else if(!is_null($itemB_AdvInt->created_at) && is_null($itemB->created_at)){
                        $created_at = $itemB_AdvInt->created_at;
                    }
                    else if(!is_null($itemB_AdvInt->created_at) && !is_null($itemB->created_at) && $itemB_AdvInt->created_at <= $itemB->created_at){
                        $created_at = $itemB_AdvInt->created_at;
                    }
                    else if(!is_null($itemB_AdvInt->created_at) && !is_null($itemB->created_at) && $itemB_AdvInt->created_at >= $itemB->created_at){
                        $created_at = $itemB->created_at;
                    }
                    else{
                        $created_at = null;
                    }

                    DB::table('advogado_interno')->where('id',$itemB_AdvInt->id)->update([
                        'status' => $status,
                        'observacao' => $observacao,
                        'created_at' => $created_at,
                        'update_at' => Tdata::TtimeStampHojeMysql()
                    ]);
                }
                else{
                    DB::table('advogado_interno')->where('id',$itemB->id)->update([
                        'fk_interno' => $id
                    ]);
                }
            }
            DB::table('advogado_interno')->where('fk_interno',$idB)->delete();

            // advogado_atendimento
            $AdvAteA = DB::table('advogado_atendimento')->where('fk_interno',$idA)->get();
            foreach($AdvAteA as $itemA){
                $data = get_object_vars($itemA);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00"){
                    $data['created_at'] = null;
                }
                DB::table('unificacao_advogado_atendimento')->insert($data);
            }
            DB::table('advogado_atendimento')->where('fk_interno',$idA)->update([
                'fk_interno' => $id
            ]);
            $AdvAteB = DB::table('advogado_atendimento')->where('fk_interno',$idB)->get();
            foreach($AdvAteB as $itemB){
                $data = get_object_vars($itemB);
                $data['id_original'] = $data['id'];
                unset($data['id']);
                if($data['created_at'] == "0000-00-00"){
                    $data['created_at'] = null;
                }
                DB::table('unificacao_advogado_atendimento')->insert($data);
            }
            DB::table('advogado_atendimento')->where('fk_interno',$idB)->update([
                'fk_interno' => $id
            ]);


        // // INTEGRACAO_SISDEPEN
        //     $intSISDEPENA = DB::table('INTEGRACAO_SISDEPEN')->where('ID_ORIGEM',$idA)->get();
        //     foreach($intSISDEPENA as $itemA){
        //         $data = get_object_vars($itemA);
        //         DB::table('unificacao_INTEGRACAO_SISDEPEN')->insert($data);
        //     }
        //     DB::table('INTEGRACAO_SISDEPEN')->where('ID_ORIGEM',$idA)->delete();
        //     $intSISDEPENB = DB::table('INTEGRACAO_SISDEPEN')->where('ID_ORIGEM',$idB)->get();
        //     foreach($intSISDEPENB as $itemB){
        //         $data = get_object_vars($itemB);
        //         DB::table('unificacao_INTEGRACAO_SISDEPEN')->insert($data);
        //     }
        //     DB::table('INTEGRACAO_SISDEPEN')->where('ID_ORIGEM',$idB)->delete();
        //     // TODO: FAZER A REQUISIÇÃO SOAP UI DE INTEGRAÇÃO


        // sancao_triagem
        DB::table('sancao_triagem')->where('fk_interno',$idA)->update([
            'fk_interno' => $id
        ]);
        DB::table('sancao_triagem')->where('fk_interno',$idB)->update([
            'fk_interno' => $id
        ]);

        $textoLog = "Registrada a unificação dos internos de prontuário ".$idA." e ".$idB." que agora possuem o prontuário ".$id.". Os dados do perfil resultante são a mescla dos dois perfis preexistentes com alterações que o operador que registrou a unificação julgou necessárias para a melhor compreensão dos dados (quem registrou a unificação não editou dados referentes a: movimentações, óbitos, ocorrências, sindicâncias, históricos, controles de acesso, advogados, visitas, escoltas, gep, sanção, triagem, fotos e documentos, os dados foram apenas unificados sem interferência manual), além disso os dados dos perfis preexistentes estão nas tabelas unificacao_* assim como eram no momento da unificação e seguem, a seguir, apenas os dados do perfil resultante no momento imediato à finalização do registro da unificação: ";
        $textoLog = $textoLog."Nome Principal: ".$dataFormInterno['nome']."; ";
        if(isset($outrosnomesTxt['outrosnomesTxt'])) {
            foreach ($outrosnomesTxt['outrosnomesTxt'] as $key => $nome) {
                if (in_array($key, $outrosnomes)) {
                    if ($nome == array_first($outrosnomesTxt['outrosnomesTxt'])) {
                        $textoLog = $textoLog . "Outros Nomes: ";
                    }
                    if ($nome == array_last($outrosnomesTxt['outrosnomesTxt'])) {
                        $textoLog = $textoLog . $nome . "; ";
                    } else {
                        $textoLog = $textoLog . $nome . ", ";
                    }
                }
            }
        }
        if(isset($contatosTxt['contatosTxt'])) {
            foreach ($contatosTxt['contatosTxt'] as $key => $contato) {
                if (in_array($key, $contatos)) {
                    if ($contato == array_first($contatosTxt['contatosTxt'])) {
                        $textoLog = $textoLog . "Contatos: ";
                    }
                    if ($contato == array_last($contatosTxt['contatosTxt'])) {
                        $textoLog = $textoLog . $contato . "; ";
                    } else {
                        $textoLog = $textoLog . $contato . ", ";
                    }
                }
            }
        }
        if(isset($enderecosTxt['enderecosTxt'])) {
            foreach ($enderecosTxt['enderecosTxt'] as $key => $endereco) {
                if (in_array($key, $enderecos)) {
                    if ($endereco == array_first($enderecosTxt['enderecosTxt'])) {
                        $textoLog = $textoLog . "Endereços: ";
                    }
                    if ($endereco == array_last($enderecosTxt['enderecosTxt'])) {
                        $textoLog = $textoLog . "[Estado: " . $endereco['pais'] . ", Estado: " . $endereco['estado'] . ", Cidade: " . $endereco['cidade'] . ", Bairro: " . $endereco['bairro'] . ", CEP: " . $endereco['cep'] . ", Logradouro: " . $endereco['logradouro'] . ", Número:" . $endereco['numero'] . ", Complemento: " . $endereco['complemento'] . ", Latitude: " . $endereco['latitude'] . ", Longitude: " . $endereco['longitude'] . "]; ";
                    } else {
                        $textoLog = $textoLog . "[Estado: " . $endereco['pais'] . ", Estado: " . $endereco['estado'] . ", Cidade: " . $endereco['cidade'] . ", Bairro: " . $endereco['bairro'] . ", CEP: " . $endereco['cep'] . ", Logradouro: " . $endereco['logradouro'] . ", Número:" . $endereco['numero'] . ", Complemento: " . $endereco['complemento'] . ", Latitude: " . $endereco['latitude'] . ", Longitude: " . $endereco['longitude'] . "], ";
                    }
                }
            }
        }
        $textoLog = $textoLog."Mãe: ".$dataFormInterno['mae']."; ";
        $textoLog = $textoLog."Pai: ".$dataFormInterno['pai']."; ";
        $textoLog = $textoLog."Unidade Prisional: ".UnidadesController::getSiglaUp($dataFormInterno['unidade_prisional'])."; ";
        $textoLog = $textoLog."Pavilhão: ".UnidadesController::getNomePav($dataFormInterno['pavilhao'])."; ";
        $textoLog = $textoLog."Ala: ".UnidadesController::getNomeAla($dataFormInterno['ala'])."; ";
        $textoLog = $textoLog."Cela: ".UnidadesController::getNomeCela($dataFormInterno['cela'])."; ";
        $textoLog = $textoLog."Sexo: ".$dataFormInterno['sexo']."; ";
        $textoLog = $textoLog."CPF: ".$dataFormInterno['cpf']."; ";
        $textoLog = $textoLog."Número do RG: ".$dataFormInterno['rg_numero']."; ";
        $textoLog = $textoLog."Órgão Expedidor do RG: ".$dataFormInterno['rg_orgaoexpedidor']."; ";
        $textoLog = $textoLog."Data do RG: ".$dataFormInterno['rg_data']."; ";
        $textoLog = $textoLog."Estado Civil: ".$dataFormInterno['estado_civil']."; ";
        $textoLog = $textoLog."Altura: ".$dataFormInterno['altura']."; ";
        $textoLog = $textoLog."Peso: ".$dataFormInterno['peso']."; ";
        $textoLog = $textoLog."Profissão: ".InternoController::getNomeProfissao($dataFormInterno['profissao'])."; ";
        $textoLog = $textoLog."Cor da Pele: ".$dataFormInterno['etnia']."; ";
        $textoLog = $textoLog."Cor dos Olhos: ".$dataFormInterno['cor_olhos']."; ";
        $textoLog = $textoLog."Cor dos Cabelos: ".$dataFormInterno['cor_cabelos']."; ";
        $textoLog = $textoLog."Data de Nascimento: ".$dataFormInterno['data_nascimento']."; ";
        $textoLog = $textoLog."Escolaridade: ".$dataFormInterno['escolaridade']."; ";
        $textoLog = $textoLog."Sentenciado: ".$dataFormInterno['sentenciado']."; ";
        $textoLog = $textoLog."Regime: ".$dataFormInterno['regime']."; ";
        $textoLog = $textoLog."Condição do Interno: ".InternoController::getNomeCondicao($dataFormInterno['condicao_interno'])."; ";
        $textoLog = $textoLog."Autorização para Saídas Externas: ".$dataFormInterno['saidasexternas']."; ";
        $textoLog = $textoLog."Facção: ".InternoController::getNomeFaccao($dataFormInterno['faccao'])."; ";
        $textoLog = $textoLog."Posto na Facção: ".$dataFormInterno['faccao_posto']."; ";
        $textoLog = $textoLog."Padrinho na Facção: ".$dataFormInterno['faccao_padrinho']."; ";
        $textoLog = $textoLog."Quebrada na Facção: ".$dataFormInterno['faccao_quebrada']."; ";
        $textoLog = $textoLog."Liderança: ".$dataFormInterno['lideranca']."; ";
        $textoLog = $textoLog."País de Naturalidade: ".$dataFormInterno['naturalidade_pais']."; ";
        $textoLog = $textoLog."Estado de Naturalidade: ".$dataFormInterno['naturalidade_estado']."; ";
        $textoLog = $textoLog."Cidade de Naturalidade: ".$dataFormInterno['naturalidade_cidade']."; ";
        $textoLog = $textoLog."Opção Sexual: ".$dataFormInterno['opcao_sexual']."; ";
        $textoLog = $textoLog."Periculosidade: ".$dataFormInterno['periculosidade']."; ";
        $textoLog = $textoLog."Número do Documento de Entrada: ".$dataFormInterno['documento_entrada_numero']."; ";
        $textoLog = $textoLog."Religião: ".$dataFormInterno['religiao']."; ";
        $textoLog = $textoLog."Status: ".$dataFormInterno['status']."; ";
        $textoLog = $textoLog."Observação: ".$dataFormInterno['observacao'].".";

        LogController::log($idUnificacao,"unificacao","add", Auth::user()->cpf, $textoLog);

        return redirect()->route('interno.show',$id)->with('alert-success', 'Unificação realizada, confira o perfil unificado resultante.');
    }

    public function usuariosBusca()
    {
        return view('usuarios.busca');
    }

    public function usuariosCadastro()
    {
        return view('usuarios.cadastro');
    }

    public function log(Request $request)
    {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $unidades = DB::table('unidade')->whereNotNull('troca')->orderBy('sigla', 'ASC')->get();
        
        if(!strpos($url,"?"))
        {
            $dados = false;

        }else {
            
            $eventos = $request->get('evento');
            $unidade = $request->get('unidade_prisional');
            $cpf = $request->get('cpf');
            $dataInicio = $request->get('dataInicio');
            $dataTermino = $request->get('dataTermino');

            $log = DB::table('log');
            
            if (!is_null($eventos)) {
                $log->where('log.tipo_alteracao', $eventos);
            }
            
            if (!is_null($unidade)) {
                $log->where('log.unidade', $unidade);
            }
            
            if (!is_null($cpf)) {
                $log->where('log.cpf_usuario', $cpf);
            }
            
            if (!is_null($dataInicio)) {
                $log->where('log.created_at', ">=", Tdata::TdataMysql($dataInicio));
            }
            
            if (!is_null($dataTermino)) {
                $log->where('log.created_at', "<=", Tdata::TdataMysql($dataTermino));
                
            }
            
            $dados = $log->select("log.*")->orderBy('log.id','DESC')->paginate(50)->appends($request->input());

        }


        return view('setic.log.log', compact('unidades','dados'));
    }

    public function getLogAcesso(Request $request) {
        
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $unidades = DB::table('unidade')->whereNotNull('troca')->orderBy('sigla', 'ASC')->get();
        
        if ($request->btnSubmitForm == 'buscar') {

            $dataInicial = $request->get('dataInicio');
            $dataFinal = $request->get('dataTermino');
            $cpf = $request->get('cpf');
        
            $dir =        (storage_path('app/arquivos/loguser/' . $cpf . "/"));
            $dirArquivo =        storage_path('app/arquivos/loguser/' . $cpf . "/");
            $allFiles = array();
            $caracteresExcluir = array("\n","\r");

            foreach (new \DirectoryIterator($dir) as $infoArquivo)
            {
                if($infoArquivo->isFile())
                {

                    if(Tdata::TdataInteiro(substr($infoArquivo->getFilename(), 0, 10)) >= Tdata::TdataInteiro(Tdata::TdataMysql($dataInicial)) && Tdata::TdataInteiro(substr($infoArquivo->getFilename(), 0, 10)) <= Tdata::TdataInteiro(Tdata::TdataMysql($dataFinal)))
                    {
                        if($file = fopen($dirArquivo.$infoArquivo->getFilename(), "r")) {
                                    while(!feof($file)) {
                                        $line = fgets($file);
                                        if($line != "\r\n"){
                                            array_push($allFiles, array(
                                               "data" => Tdata::TdataBR(substr($infoArquivo->getFilename(), 0, 10)),
                                                "url" => str_replace($caracteresExcluir, "", $line),
                                            ));
                                        }
                                    }
                                    fclose($file);
                        }


                }
            }
            }

            $dados = array();
            foreach($allFiles as $array){
                $dados[$array["data"]][] = $array;
            }

            if(count($dados) < 1 || $dados == [])
            {
                $dados = "nd";
            }
            
        }else{
            $dados = false;
        }
        
        //dd($dados);
        
        return view('setic.log.logAcesso', compact('dados'));
        
    }

    public function permissoesCadastrarPerfil() {
        
        return view('setic.permissoes.perfilCadastrar');
        
    }
    
    public function permissoesCadastrarPerfilStore(Request $request) {
        
        $insert = DB::table('permissao_perfil')->insert([
            'nome' => $request->get('nome'),
            'tipo' => $request->get('tipo'),
        ]);
        
        $idInsert = DB::getPdo()->lastInsertId();

        LogController::log($idInsert,"permissao_perfil","add",Auth::user()->cpf,"Criação de perfil de permissão: ".$request->get('nome').", para usuários do tipo: ".$request->get('tipo'));
        
            return redirect()->route('setic.permissoesGerenciarEventos', $idInsert)->with('alert-success', 'Cadastro de Perfil Realizado com Sucesso!');
        
    }

    public function permissoesGerenciarEventos($id) {
        
        $controllerPermissao = DB::table("permissao_perfil_eventos")
                ->leftJoin('permissao_controller_eventos', 'permissao_controller_eventos.id','=', 'permissao_perfil_eventos.fk_evento')
                ->leftJoin('permissao_controller', 'permissao_controller.id','=', 'permissao_controller_eventos.fk_controller')
                ->select('permissao_controller.texto', 'permissao_controller.id')
                ->where('fk_perfil', $id)
                ->orderBy('permissao_controller.texto', 'ASC')
                ->distinct()
                ->get();
        
        $nomePerfil = self::getNomePerfilPermissao($id);
        
        return view('setic.permissoes.gerenciarEventos', compact('controllerPermissao','id','nomePerfil'));
        
        
    }
    
    public function permissoesInserirEvento($id) {
        
        $controllers = DB::table('permissao_controller')->orderBy('texto', 'ASC')->get();
        
        return view('setic.permissoes.inserirEventos', compact('controllers','id'));
    }
    
    public function getEventosAjax(Request $request) {
        
        $idController = $request->get('idController');
        $idPerfil = $request->get('idPerfil');
        
        $tipoPerfil = DB::table('permissao_perfil')->where('id', $idPerfil)->get()[0]->tipo;
        if($tipoPerfil != "E")
        {
            $dados = DB::table("permissao_controller_eventos")
                    ->where('fk_controller',$idController)
                    ->select('id', 'metodo','nome')
                    ->orderBy('grupo','DESC')
                    ->orderBy('nome', 'ASC')
                    ->get();
            
            return $dados;
            
        }else{
            
            $dados = DB::table("permissao_controller_eventos")
                    ->where('tipo', 'E')
                    ->where('fk_controller',$idController)
                    ->select('id', 'metodo', 'nome')
                    ->orderBy('grupo','DESC')
                    ->orderBy('nome', 'ASC')
                    ->get();
            
            return $dados;
        }
        
    }
    
    public function permissoesInserirEventoStore(Request $request, $id) {
        
        //dd($id, $request->all());
        
        $metodos = $request->get('metodos');
        $dados = [];
        foreach($metodos as $metodo){
            
            $verificaPermissaoExixtente = DB::table("permissao_perfil_eventos")->where('fk_perfil', $id)->where('fk_evento', $metodo)->count();
            
            if($verificaPermissaoExixtente == 0){
                $dados[] = [
                    'fk_perfil' => $id,
                    'fk_evento' => $metodo,
                ];
            }
            
        }
        
        
        
        $insert = DB::table("permissao_perfil_eventos")->insert($dados);
        
        if($insert)
        {
            return redirect()->route('setic.permissoesGerenciarEventos', $id)->with('alert-success', 'Permissões Inseridas com Sucesso!');
        }
    }
    
    public function permissoesListarPerfis() {
        
        $perfis = DB::table('permissao_perfil')
                ->select('permissao_perfil.*', DB::raw('(CASE WHEN permissao_perfil.tipo = "I" THEN "Perfil Interno (Servidores)" WHEN permissao_perfil.tipo = "E" THEN "Perfil Externo" ELSE "Não Informado" END) AS tipo'))
                ->orderBy('nome', 'ASC')->get();
        
        return view('setic.permissoes.listarPerfis', compact('perfis'));
        
    }
    
    public function permissoesGerenciarMetodos($idController, $idPerfil) {
        
        $tipoPerfil = DB::table('permissao_perfil')->where('id', $idPerfil)->get()[0]->tipo;
        
        if($tipoPerfil == "I"){
            $metodos = DB::table("permissao_controller_eventos")->where('fk_controller', $idController)->get();
        }else{
            $metodos = DB::table("permissao_controller_eventos")->where('fk_controller', $idController)->where('tipo','E')->get();
        }
        
        $controllerPermissao = DB::table("permissao_perfil_eventos")
                ->leftJoin('permissao_controller_eventos', 'permissao_controller_eventos.id','=', 'permissao_perfil_eventos.fk_evento')
                ->select('permissao_controller_eventos.id')
                ->where('permissao_controller_eventos.fk_controller', $idController)
                ->where('permissao_perfil_eventos.fk_perfil', $idPerfil)
                ->get();
        $controllerPermissao = json_decode($controllerPermissao, true);
        $permissoesArray = array_column($controllerPermissao, 'id');
        
        $permissoes = array();
        foreach($metodos as $metodo)
        {
            if(in_array($metodo->id, $permissoesArray))
            {
                $status = "A";
            }else{
                $status = "I";
            }
            
            array_push($permissoes, array(
                'nome' => $metodo->nome,
                'id' => $metodo->id,
                'status' => $status,
            ));
            
        }
        
        $sort = [
            ['column' => 'nome', 'order' => 'asc'],
        ];

        $permissoes = collect($permissoes);
        $permissoes = self::multiPropertySort($permissoes, $sort);

        $nomePerfil = self::getNomePerfilPermissao($idPerfil);
        
        return view('setic.permissoes.gerenciarMetodos', compact('permissoes','idPerfil', 'nomePerfil'));

    }
    
    public function permissoesExcluirMetodo($idPerfil, $idMetodo) {
        
        
        $permissaoExcluir = DB::table('permissao_perfil_eventos')
                ->where('fk_perfil', $idPerfil)
                ->where('fk_evento', $idMetodo)
                ->delete();
        
        return redirect()->back()->with('alert-success', 'Método Excluído com Sucesso!');
        
    }
    
    public function permissoesIncluirMetodo($idPerfil, $idMetodo) {
        
        
        $permissaoIncluir = DB::table('permissao_perfil_eventos')->insert([
            'fk_perfil' => $idPerfil,
            'fk_evento' => $idMetodo
        ]);
        
        return redirect()->back()->with('alert-success', 'Método Incluído com Sucesso!');
        
    }
    
    public function permissoesExcluirPerfil($idPerfil) {
        
        //dd($idPerfil);
        
        $dadosPerfil = DB::table('permissao_perfil')->where('id', $idPerfil)->get()[0];
        
        // deleta todos os eventos do perfil
        DB::table('permissao_perfil_eventos')->where('fk_perfil', $idPerfil)->delete();
        
        // verifica se o tipo do perfil eh interno
        if($dadosPerfil->tipo == "I")
        {
            DB::table("users")->where('tipo', $idPerfil)->update(['tipo'=> "1"]);
        }
        
        // verifica se o tipo eh externo
        if($dadosPerfil->tipo == "E")
        {
            DB::table("users")->where('tipo', $idPerfil)->update(['tipo'=> "10"]);
        }
        
        // deleta o perfil
        DB::table('permissao_perfil')->where('id', $idPerfil)->delete();
        
        
        return redirect()->back()->with('alert-success', 'Perfil Excluído com Sucesso!');
        
    }
    
    public static function getNomePerfilPermissao($idPerfil)
    {
        $dados = DB::table("permissao_perfil")->where('id', $idPerfil)->get()[0]->nome;
        
        return $dados;
        
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
    
    public function listaObjetosOcorrencia() {
        
        $dados = DB::table('ocorrencia_natureza_objeto')
                ->leftJoin('ocorrencia_natureza', 'ocorrencia_natureza.id', 'ocorrencia_natureza_objeto.fk_natureza')
                ->select('ocorrencia_natureza.natureza', 'ocorrencia_natureza_objeto.*')
                ->orderBy('ocorrencia_natureza_objeto.id', 'DESC')
                ->get();
        
        return view('setic.ocorrenciaObjetos.lista', compact('dados'));
        
    }
    
    public function novoObjetosOcorrencia() {
        
        $naturezas = DB::table('ocorrencia_natureza')->whereIn('id', [1,47])->get();
        
        return view('setic.ocorrenciaObjetos.cadastro', compact('naturezas'));
        
    }
    
    public function novoObjetosOcorrenciaStore(Request $request) {
        
        $insert = DB::table('ocorrencia_natureza_objeto')->insert([
            'fk_natureza' => $request->get('natureza'),
            'nome' => $request->get('nome'),
            'status' => 'A',
            'created_at' => Tdata::TtimeStampHojeMysql()
        ]);
        
        return redirect()->route('setic.listaObjetosOcorrencia')->with('alert-success', 'Cadastro Realizado com sucesso');
        
    }
    
    public function editarObjetosOcorrencia($id) {
        
        $naturezas = DB::table('ocorrencia_natureza')->whereIn('id', [1,47])->get();
        
        $dados = DB::table('ocorrencia_natureza_objeto')->where('id', $id)->first();
        
        return view('setic.ocorrenciaObjetos.cadastro', compact('naturezas', 'dados', 'id'));
        
    }
    
    public function updateObjetosOcorrencia(Request $request, $id) {
        
        $insert = DB::table('ocorrencia_natureza_objeto')->where('id', $id)->update([
            'fk_natureza' => $request->get('natureza'),
            'nome' => $request->get('nome'),
            'status' => 'A',
            'created_at' => Tdata::TtimeStampHojeMysql()
        ]);
        
        return redirect()->route('setic.listaObjetosOcorrencia')->with('alert-success', 'Cadastro Alterado com sucesso');
        
    }
}