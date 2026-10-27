<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Controllers\Permissoes\PermissoesController;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('auth');
    }

    public function callAction($method, $parameters)
    {

        $retornoPermissao = PermissoesController::getVerifyPermissionController("RegisterController");
        
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

        return parent::callAction($method, $parameters);
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|max:11|unique:users',
            'email' => 'required|string|email|max:400|unique:users',
            'tipo' => 'required|string|max:100',
            'cargo' => 'required|string|max:255',
            'orgao' => 'required|string|max:255',
            'visualiza_outras_up' => 'required|string|max:2',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'cpf' => $data['cpf'],
            'email' => $data['email'],
            'tipo' => $data['tipo'],
            'cargo' => $data['cargo'],
            'orgao' => $data['orgao'],
            'visualiza_outras_up' => $data['visualiza_outras_up'],
            'password' => bcrypt($data['password']),
        ]);
    }
}
