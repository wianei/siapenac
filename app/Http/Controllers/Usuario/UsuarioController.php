<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a Escolta
|
*/

namespace App\Http\Controllers\Usuario;

use App\Classes\Constantes;
use App\Classes\Tdata;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Permissoes\PermissoesController;
use App\Mail\SendMailRecuperacaoConta;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class UsuarioController extends Controller
{

    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->middleware('auth')->except('recuperarConta','recuperarContaStore','esqueciMinhaSenha','esqueciMinhaSenhaStore');


    }

    public static function verificaTrocaSenha6meses($cpf)
    {
        $dados = DB::table('users')->where('cpf', $cpf)->get()[0]->ultima_alteracao_senha;

        $dataAlteracaoSenha = Tdata::getDataTimestamp($dados);

        $diferencaTempo = Tdata::diffDate($dataAlteracaoSenha, date("Y-m-d"), "D");

        if($diferencaTempo >= 180)
        {

            return 0;

        }else{
            return 1;
        }

    }

    public static function getDadosUser($cpf)
    {
        $dados = DB::table('users')->where('cpf', $cpf)->get();

        return $dados;

    }

    public static function getTipoConta($cpf)
    {
        $dados = DB::table('users')->where('cpf', $cpf)->get();

        return $dados[0]->tipo;
    }

    public static function getNomeUser($cpf)
    {
        $dados = self::getDadosUser($cpf);

        return $dados[0]->name;
    }

    public static function alterarMinhaSenha()
    {
        PermissoesController::loadPermissionUser();
        return view('usuarios.alterarSenha.alterarSenha');
    }

    public function alterarMinhaSenhaStore(Request $request)
    {
        $userData = DB::table("users")->where('cpf', Auth::user()->cpf)->get();

        $nome = explode(" ",$userData[0]->name);

        $senhaAtual = $request->get('senha');
        $novaSenha = $request->get('novaSenha');
        $novaSenhaR = $request->get('novaSenhaR');

        $senhasProibidas = collect(['123456', '1234567','12345678','123456789','321321','654321','456789','987654','147258','963852','abc123','123abc','senhasiapen','siapen','111111','1234567890','password','666666','222222','333333','444444','555555','777777','888888','999999','1q2w3e4r', strtoupper($nome[0]), strtolower($nome[0])]);

        if($novaSenha != $novaSenhaR)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'As senhas não coincidem!');
        }

        if($senhasProibidas->contains($novaSenha) )
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Senha muito fácil! Não é permitido senha com sequencias numericas, Ex.: 123456, 321123 ou seu nome.');
        }

        if(Hash::check($novaSenha, $userData[0]->password)){

            return redirect()->back()->withInput()->with('alert-danger', 'A nova senha deve ser diferente da anterior!');

        }

        if (Hash::check($senhaAtual, $userData[0]->password)) {

            $updateSenha = DB::table("users")->where('cpf', Auth::user()->cpf)->update([
                'password'=> Hash::make($novaSenha),
                'ultima_alteracao_senha' => Tdata::TtimeStampHojeMysql()
            ]);

            if($updateSenha)
            {
                LogController::log($userData[0]->id,'user','update', Auth::user()->cpf,"Alteração de senha de acesso ao {{ config('app.name') }} realizada pelo usuario");
                return redirect()->route('inicio')->with('alert-success', 'Senha alterada com sucesso!');
            }

        }else{

            return redirect()->back()->withInput()->with('alert-danger', 'A senha atual não confere!');
        }
    }
    public static function recuperarConta()
    {

        return view('usuarios.recuperarConta.recuperarConta');

    }

    public static function esqueciMinhaSenha()
    {

        return view('usuarios.esqueciSenha.esqueciSenha');

    }

    public static function esqueciMinhaSenhaStore(Request $request)
    {
        $cpf = $request->get('cpf');
        $email = $request->get('email');

        $dadosConta = DB::table('users')->where('cpf', $cpf)->get();
        if(!isset($dadosConta) || $dadosConta == collect([])) return redirect()->back()->withInput()->with('alert-danger', 'CPF não encontrado.');

        if($dadosConta[0]->status == 'I')
        {
            return redirect()->back()->withInput()->with('alert-danger', 'A conta do CPF informado encontra-se inativa, faça a recuperação de conta.');

        }elseif(strtolower($dadosConta[0]->email) != strtolower($email))
        {
            return redirect()->back()->withInput()->with('alert-danger', 'O email informado não corresponde ao cadastrado para o CPF.');

        }
        else{

//            $emailBD = $dadosConta[0]->email;
//            $emailShowUser = explode('@', $email);
//            $emailShow = substr($emailShowUser[0],0,3)."*****@".$emailShowUser[1];

            $senha = str_random(8);

            $senhaDB = Hash::make($senha);

            $updateSenha = DB::table('users')->where('cpf', $cpf)->update([
                'password' => $senhaDB,
                'ultima_alteracao_senha' => Tdata::TtimeStampHojeMysql()

            ]);

            $mensagem = "Login: $cpf<br>Senha: $senha";

            $enviado = Mail::to($email)->send(new SendMailRecuperacaoConta($dadosConta[0]->name,$cpf,$mensagem,$email,$dadosConta[0]->orgao,$senha,'Envio de senha '.config('app.name')));

            if(count(Mail::failures()) == 0){

                return redirect()->back()->withInput()->with('alert-success', 'Senha enviada com sucesso!');

            }



        }
    }

    public function contaInativada($tipo)
    {
        Session::flush();

        return view('usuarios.contaInativada.contaInativada', compact('tipo'));
    }

    public static function recuperarContaStore(Request $request)
    {
        $cpf = $request->get("cpf");
        $email = $request->get("email");
        $orgao = $request->get("orgao");

        $verificaUsuario = DB::table('users')->where('cpf', $cpf)->where('email', $email)->get();

        if (count($verificaUsuario) == 0) {
            return redirect()->back()->withInput()->with('alert-danger', 'CPF ou Email não encontrado em nossa Base de Dados, entre em contato diretamente com a '.config('app.ti').'-'.config('app.secretaria').'/'.config('app.uf').'.');

        } else {

            $verificaUsuarioInativo = DB::table('users')->where('cpf', $cpf)->where('status', 'I')->get();

            if (count($verificaUsuarioInativo) == 0) {
                return redirect()->back()->withInput()->with('alert-danger', 'O CPF informado encontra-se com uma conta ativa, entre em contato diretamente com a '.config('app.ti').'-'.config('app.secretaria').'/'.config('app.uf').'.');

            } else {

                $verificaSolicitacao = DB::table('users_recuperacao')->where('cpf', $cpf)->get();

                if (count($verificaSolicitacao) >= 1) {
                    return redirect()->back()->withInput()->with('alert-danger', 'Solicitação de recuperação de conta realizada anteriormente, aguarde ou  entre em contato diretamente com a '.config('app.ti').'-'.config('app.secretaria').'/'.config('app.uf').'.');

                } else {

                    $verificaEmail = DB::table('users')->where('email', $email)->get();
                    if (count($verificaEmail) > 1) {
                        return redirect()->back()->withInput()->with('alert-danger', 'O e-mail está sendo usado em outro cadastro, entre em contato diretamente com a '.config('app.ti').'-'.config('app.secretaria').'/'.config('app.uf').'.');

                    } else {

                        $insertRecuperacao = DB::table('users_recuperacao')->insert([
                            'cpf' => $cpf,
                            'email' => $email,
                            'orgao' => $orgao,
                            'recuperado' => 'N',
                            'created_at' => Tdata::TtimeStampHojeMysql(),
                        ]);

                        $idInsert = DB::getPdo()->lastInsertId();

                        if ($request->file('inputFile')[0] != null) {

                            $qtdDoc = count($request->file('inputFile'));

                            for ($x = 0; $x < $qtdDoc; $x++) {

                                $destinationPath = Constantes::ARQUIVOS_RECUPERACAO_CONTA . "/" . $idInsert;

                                $name = $cpf . "_" . time() . "_" . $x . "_" . $request->file('inputFile')[$x]->getClientOriginalName();

                                $request->file('inputFile')[$x]->storeAs($destinationPath, $name);
                                //Storage::put($name, $destinationPath);

                                $insertDoc = DB::table('users_recuperacao_arquivos')->insert(
                                    [
                                        'fk_recuperacao' => $idInsert,
                                        'nome_arquivo' => $name,
                                    ]
                                );


                            }
                        }

                        return redirect()->back()->withInput()->with('alert-success', 'Solicitação de recuperação de conta realizada com sucesso! As instruções para recuperação serão enviadas por e-mail, após análise.');

                    }
                }

            }
        }
    }
}