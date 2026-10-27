<?php


/*
|--------------------------------------------------------------------------
| Desenvolvimento: geovanni_ti@hotmail.com / saulo.ricardo_@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados às Unidades
|
*/

namespace App\Http\Controllers\Permissoes;

use App\Classes\Tdata;
use App\Http\Controllers\Facade\FacadeController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;



class PermissoesController extends Controller
{
    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->middleware('auth');



    }
    
    public static function deletePermissionUser() {
       
        Session::forget('permissions');
        Session::forget('tipoPerfil');
        
    }

    public static function loadPermissionUser() {
        
        $permissaoDados = DB::table('permissao_perfil_eventos')
        ->leftJoin('permissao_controller_eventos', 'permissao_controller_eventos.id','=','permissao_perfil_eventos.fk_evento')
        ->leftJoin('permissao_controller', 'permissao_controller.id','=','permissao_controller_eventos.fk_controller')
        ->select("permissao_controller_eventos.metodo","permissao_controller.nome")
        ->where('fk_perfil', Auth::user()->tipo)->get();
        
        //dd($permissaoDados);
        
        $permissoes = array();
        foreach($permissaoDados as $permissaoDado)
        {
            array_push($permissoes, array(
               'controller' => $permissaoDado->nome,
                'metodo' => $permissaoDado->metodo,
            ));
        }
        
        $dados = array();

        foreach($permissoes as $array){
            $dados[$array["controller"]][] = $array;
        }
        
        Session::put('permissions', $dados);
        
        $tipoPermissao = DB::table('permissao_perfil')->where('id', Auth::user()->tipo)->get()[0]->tipo;
        
        Session::put('tipoPerfil', $tipoPermissao);
       
        
    }
    

    /**
     * Metodo que e chamado dentro do callAction dos Controllers
     * 
     * @param type $classe
     * @param type $metodo
     * @return type
     */
    public static function verifyPermission($classe, $metodo)
    {
              
        $permissions = Session::get('permissions');
        /*
         * verifica se no nome do metodo tem o termo ajax
         */
        if (strpos(strtolower($metodo), 'ajax') !== false) {
            return true;
        }
        if (strpos(strtolower($metodo), 'get') !== false) {
            return true;
        }
        else{
        
            if(array_key_exists($classe, $permissions))
            {


                    $array = array_column($permissions[$classe], 'metodo');  

                    if(!in_array($metodo, $array))
                    {

                        return false;
                        //return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
                    }else
                    {
                        return true;
                    }

            }else{

                return false;
                //return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');

            }
            
        }
        
        //dd($dados);
    }
    
    /**
     * Metodo que e chamado nas views para verificar se tem permissao de visualizar determinado elemento
     * 
     * @param type $classe
     * @param type $metodo
     * @return type
     */
    public static function getVerifyPermissionView($classe, $metodo)
    {
        
        $permissions = Session::get('permissions');
        
        if(array_key_exists($classe, $permissions))
        {
            
                $array = array_column($permissions[$classe], 'metodo');            
                if(in_array($metodo, $array))
                {
                    
                    return true;
                }else{
                    
                    return false;
                }
            
        }else{
            return false;

        }
        //dd($dados);
    }
    
    /**
     * retorna se e um perfil interno (de servidores) ou externo (quem nao eh servidor)
     * 
     * @return type
     */
    public static function getTipoPermissao() {
        
        $tipoPerfil = Session::get('tipoPerfil');
        
        return $tipoPerfil;
        
    }
    
    public static function getNomePerfil($idPerfil) {
        
        $dados = DB::table('permissao_perfil')->where('id', $idPerfil)->get()[0]->nome;
        
        return $dados;
        
    }
    
    /**
     * Verifica se possui permissao apenas para o controle
     * @important nao verifica o metodo
     * 
     * @param type $NomeController
     * @return boolean
     */
    public static function getVerifyPermissionController($NomeController) {
        
        $permissions = Session::get('permissions');
        
        if(array_key_exists($NomeController, $permissions))
        {
            return true;
        }else{
            return false;
        }
        
    }

}

