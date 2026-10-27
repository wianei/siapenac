<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Rules\ValidRecaptcha;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Usuario\UsuarioController;
use App\Classes\Tdata;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Permissoes\PermissoesController;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/inicio';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'cpf';
    }
    
    protected function sendLoginResponse(Request $request)
    {
        $userData = DB::table("users")->where('cpf', Auth::user()->cpf)->get();
        
        $dataUtimoLogin = LogController::getUltimoLoginNavegacao();

        if($dataUtimoLogin != "") {
            $diferencaTempo = Tdata::diffDate($dataUtimoLogin, date("Y-m-d"), "D");

            if($userData[0]->status == "A" && $diferencaTempo >= 60)
            {
                DB::table('users')->where('cpf', Auth::user()->cpf)->update([
                    'status'=> 'I',
                    'ultimo_login'=> null,
                ]);

                LogController::log($userData[0]->id,"user","update",Auth::user()->cpf,"Inativacao de conta por inatividade de mais de 60 dias sem uso.");


                return redirect()->route('usuario.contaInativada',"inativada");
            }
            // login ok
            if($userData[0]->status == "A" && $diferencaTempo < 60)
            {
                DB::table('users')->where('cpf', Auth::user()->cpf)->update(['ultimo_login'=> Tdata::TtimeStampHojeMysql()]);

                $tempoSenha = UsuarioController::verificaTrocaSenha6meses( Auth::user()->cpf);
                if($tempoSenha == 0)
                {
                    return redirect()->route('usuario.alterarMinhaSenha')->with('alert-danger', 'Por segurança, é necessário que sua senha seja alterada. Evite o bloqueio da sua conta!');
                }
            }

        }else{

            // login ok
            if($userData[0]->status == "A")
            {
                DB::table('users')->where('cpf', Auth::user()->cpf)->update(['ultimo_login'=> Tdata::TtimeStampHojeMysql()]);

                $tempoSenha = UsuarioController::verificaTrocaSenha6meses( Auth::user()->cpf);
                if($tempoSenha == 0)
                {
                    return redirect()->route('usuario.alterarMinhaSenha')->with('alert-danger', 'Por segurança, é necessário que sua senha seja alterada. Evite o bloqueio da sua conta!');
                }
            }

        }


        if($userData[0]->status == "I")
        {
            return redirect()->route('usuario.contaInativada',"inativa");
        }
        
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPath());

    }

    
    public function authenticated(Request $request, $user)
    {
        //
        PermissoesController::loadPermissionUser();
        
        if (!file_exists(storage_path("app/arquivos/usuarios/cpfimg/".Auth::user()->cpf.".png")))
        {

            header ("Content-type: image/png");
            $text = Auth::user()->cpf;
            $string = $text;
            $font   = 4;
            $width  = 100;
            $height = 50;

            $im = @imagecreate ($width,$height);
            $background_color = imagecolorallocate ($im, 255, 255, 255);
            $text_color = imagecolorallocate ($im, 202, 202,202);
            imagestring ($im, $font, 0, 0,  $string, $text_color);
            imagepng($im, storage_path("app/arquivos/usuarios/cpfimg/".Auth::user()->cpf.".png"));

        }

    }


    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
    */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
            'g-recaptcha-response' => ['required', new ValidRecaptcha]
        ]);

        // // verifica se a conta do usuario possui prazo de acesso
        // $userData = DB::table("users")->where('cpf', Auth::user()->cpf)->get();

        // $dataLimiteAcesso = $userData[0]->data_limite_acesso;
        // if($dataLimiteAcesso != null || $dataLimiteAcesso != "") {
        //     if ($userData[0]->status == "A" && $dataLimiteAcesso <= date("Y-m-d")) {

        //         DB::table('users')->where('cpf', Auth::user()->cpf)->update([
        //             'status'=> 'I',
        //             'ultimo_login'=> null,
        //         ]);

        //         LogController::log($userData[0]->id,"user","update",Auth::user()->cpf,"Inativação de conta por prazo de acesso expirado.");


        //         return redirect()->route('usuario.contaInativada',"expiracaoTempoAcesso");

        //     }
	    // }

    }

    public function logout(){

        Auth::logout();
        return redirect('/login');

    }

}
