<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a Escolta
|
*/

namespace App\Http\Controllers\Facade;

use App\Http\Controllers\Unidades\UnidadesController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \App\Http\Controllers\Permissoes\PermissoesController;

class FacadeController extends Controller
{
    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');

    }

    public function verificaAgepen(Request $request)
    {
        $dados = DB::connection('mysql2')->table('servidors')->where('cpf', $request->get('cpf'))->get();

        if(count($dados) > 0)
        {
            return "true";

        }else{

            return "false";

        }

    }


    public static function getDadosRh($table,$select,$orderBy){

        $dados = DB::connection('mysql2')->table($table)->select($select)->orderBy($orderBy,'ASC')->get();

        return $dados;

    }

    public static function getTurmaServidor($matricula){


        $dados = DB::connection('mysql2')->table('servidors')->where('servidors.matricula', $matricula)->get();

        if($dados[0]->data_exercicio >= '2002-05-01' && $dados[0]->data_exercicio <= '2009-12-31')
        {
            return 1;
        }

        if($dados[0]->data_exercicio >= '2010-05-01' && $dados[0]->data_exercicio <= '2016-12-31')
        {
            return 2;

        }

        if($dados[0]->data_exercicio >= '2017-05-01')
        {
            return 3;

        }

        if($dados[0]->data_exercicio == null)
        {
            return null;
        }

        return $dados;

    }
    public static function getDadosDiretorPorSigla($sigla){
        $dadosDiretorComissionado = DB::connection('mysql2')
            ->table('servidors')
            ->join('lotacaos','lotacaos.id','=','servidors.lotacao')
            ->select('servidors.nome','servidors.matricula','servidors.sexo')
            ->where('servidors.funcao',26)
            ->where('lotacaos.sigla', $sigla)
            ->get();
        if($dadosDiretorComissionado != collect([])) return ($dadosDiretorComissionado);

        $dadosDiretorNaoComissionado = DB::connection('mysql2')
            ->table('servidors')
            ->join('lotacaos','lotacaos.id','=','servidors.lotacao')
            ->select('servidors.nome','servidors.matricula','servidors.sexo')
            ->where('servidors.funcao',30)
            ->where('lotacaos.sigla', $sigla)
            ->get();
        if($dadosDiretorNaoComissionado != collect([])) return ($dadosDiretorNaoComissionado);

        return null;
    }

    public static function getDadosViceDiretorPorSigla($sigla){
        $dadosViceDiretorComissionado = DB::connection('mysql2')
            ->table('servidors')
            ->join('lotacaos','lotacaos.id','=','servidors.lotacao')
            ->select('servidors.nome','servidors.matricula','servidors.sexo')
            ->where('servidors.funcao',31)
            ->where('lotacaos.sigla', $sigla)
            ->get();
        if($dadosViceDiretorComissionado != collect([])) return ($dadosViceDiretorComissionado);

        $dadosViceDiretorNaoComissionado = DB::connection('mysql2')
            ->table('servidors')
            ->join('lotacaos','lotacaos.id','=','servidors.lotacao')
            ->select('servidors.nome','servidors.matricula','servidors.sexo')
            ->where('servidors.funcao',36)
            ->where('lotacaos.sigla', $sigla)
            ->get();
        if($dadosViceDiretorNaoComissionado != collect([])) return ($dadosViceDiretorNaoComissionado);

        return null;
    }

    public static function getDiretorPorSigla($sigla){
        $nomeDiretorComissionado = DB::connection('mysql2')
            ->table('servidors')
            ->join('lotacaos','lotacaos.id','=','servidors.lotacao')
            ->select('servidors.nome')
            ->where('servidors.funcao',26)
            ->where('lotacaos.sigla', $sigla)
            ->get();
        if($nomeDiretorComissionado != collect([])) {
            return utf8_encode($nomeDiretorComissionado[0]->nome);
        }

        $nomeDiretorNaoComissionado = DB::connection('mysql2')
            ->table('servidors')
            ->join('lotacaos','lotacaos.id','=','servidors.lotacao')
            ->select('servidors.nome')
            ->where('servidors.funcao',30)
            ->where('lotacaos.sigla', $sigla)
            ->get();
        if($nomeDiretorNaoComissionado != collect([])) {
            return utf8_encode($nomeDiretorNaoComissionado[0]->nome);
        }
        return "Não informado ao RH";
    }

    public static function getCpfDiretorPorSigla($sigla){
        $nomeDiretorComissionado = DB::connection('mysql2')
            ->table('servidors')
            ->join('lotacaos','lotacaos.id','=','servidors.lotacao')
            ->select('servidors.cpf')
            ->where('servidors.funcao',26)
            ->where('lotacaos.sigla', $sigla)
            ->get();
        if($nomeDiretorComissionado != collect([])) {
            return ($nomeDiretorComissionado[0]->cpf);
        }

        $nomeDiretorNaoComissionado = DB::connection('mysql2')
            ->table('servidors')
            ->join('lotacaos','lotacaos.id','=','servidors.lotacao')
            ->select('servidors.cpf')
            ->where('servidors.funcao',30)
            ->where('lotacaos.sigla', $sigla)
            ->get();
        if($nomeDiretorNaoComissionado != collect([])) {
            return ($nomeDiretorNaoComissionado[0]->cpf);
        }
        return "Não informado ao RH";
    }

    public static function getViceDiretorPorSigla($sigla){
        $nomeViceDiretorComissionado = DB::connection('mysql2')
            ->table('servidors')
            ->join('lotacaos','lotacaos.id','=','servidors.lotacao')
            ->select('servidors.nome')
            ->where('servidors.funcao',31)
            ->where('lotacaos.sigla', $sigla)
            ->get();
        if($nomeViceDiretorComissionado != collect([])) {
            return utf8_encode($nomeViceDiretorComissionado[0]->nome);
        }

        $nomeViceDiretorNaoComissionado = DB::connection('mysql2')
            ->table('servidors')
            ->join('lotacaos','lotacaos.id','=','servidors.lotacao')
            ->select('servidors.nome')
            ->where('servidors.funcao',36)
            ->where('lotacaos.sigla', $sigla)
            ->get();
        if($nomeViceDiretorNaoComissionado != collect([])) {
            return utf8_encode($nomeViceDiretorNaoComissionado[0]->nome);
        }
        return "Não informado ao RH";
    }

    public static function getNomeServidor($matricula)
    {
        if($matricula == null) return "";
        $dados = self::getDadosServidorPorMat($matricula);

        return $dados[0]->nome;
    }

    public static function getFotoServidor()
    {
        if (PermissoesController::getTipoPermissao() == "I") {

            $image = self::getSSLPage(config('app.rh').'/webservice/fotos-servidores/1wBd794bG5BFkHU71xltg6Gbo2Tx6a4y/'.Auth::user()->cpf);

            return $image;

        }else{

            return null;
        }

    }

    public static function getSSLPage($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSLVERSION,3); 
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public static function getFotoServidorAjax(Request $request)
    {
        if (PermissoesController::getTipoPermissao() == "I") {

            $image = file_get_contents(config('app.rh').'/webservice/fotos-servidores/1wBd794bG5BFkHU71xltg6Gbo2Tx6a4y/'.$request->get('cpf'));

            return $image;

        }else{

            return null;
        }

    }

    public static function getTotalServidoresLotacao($sigla)
    {

        $dados = DB::connection('mysql2')->table('servidors')
            ->leftJoin('lotacaos', 'lotacaos.id', '=', 'servidors.lotacao')
            ->select('lotacaos.*','servidors.*')
            ->where('lotacaos.sigla', $sigla)->count();

        return $dados;
    }

    public static function  getTotalServidoresLotacaoBySexo($sigla,$sexo){

        $dados = DB::connection('mysql2')->table('servidors')
            ->leftJoin('lotacaos', 'lotacaos.id', '=', 'servidors.lotacao')
            ->leftJoin('contatos',function($query){
                $query->on('contatos.id_servidor', '=' ,'servidors.id');
            })
            ->where('sexo', $sexo)
            ->select('lotacaos.*','servidors.*','contatos.*','contatos.numero AS fone')->where('lotacaos.sigla', $sigla)->orderBy('servidors.nome', 'ASC')->get();

        return count($dados);
    }

    public static function getNomeServidorCpf($cpf)
    {
        if($cpf == null) return "";
        $dados = DB::connection('mysql2')->table('servidors')->where('cpf', $cpf)->get();
        if(count($dados) > 0)
        {
            return $dados[0]->nome;
        }else{
            return "";
        }
    }
    public static function getDadosServidor($cpf,$select){

        if (PermissoesController::getTipoPermissao() == "E") {

            $dados = DB::table('users')->where('cpf', $cpf)->get();

            $dados->map(function ($item) {

                $item->nome = $item->name;

                return $item;
            });

            return $dados;

        }


        $dados = DB::connection('mysql2')->table('servidors')->select($select)->where('cpf', $cpf)->get();

        return $dados;

    }

    public static function getDadosFoneServidor($id){

        $dados = DB::connection('mysql2')->table('contatos')->select('numero')->where('id_servidor', $id)->get();

        if(count(json_decode($dados, true)) == 0){
            return false;
        }else{
            return json_decode($dados, true);
        }
        //return json_decode($dados, true);

    }

    public static function getDadosServidorPorMat($matricula){

        $dados = DB::connection('mysql2')->table('servidors')->where('matricula', $matricula)->get();

        return $dados;

    }

    public static function getServidoresDaLotacao($sigla)
    {
        $dados = DB::connection('mysql2')->table('servidors')
            ->leftJoin('lotacaos', 'lotacaos.id', '=', 'servidors.lotacao')
            ->leftJoin('contatos',function($query){
                $query->on('contatos.id_servidor', '=' ,'servidors.id');
            })
            ->select('lotacaos.*','servidors.*','contatos.*','contatos.numero AS fone')->where('lotacaos.sigla', $sigla)->orderBy('servidors.nome', 'ASC')->get();

        return $dados;

    }

    public static function servidorNoRH($cpf){
        return DB::connection('mysql2')->table('servidors')->where('servidors.cpf', $cpf)->exists();
    }

    public static function getLotacaoServidor($cpf, $rh = "N"){

        if (PermissoesController::getTipoPermissao() == "E") {

            return "Usuário Externo";

        }

        if (Auth::user()->visualiza_outras_up == 'S' && $rh == "N") {

            $lotacao = DB::table('unidade_user_temp')->where('cpf', Auth::user()->cpf)->get();

            if(count($lotacao) == 0)
            {
                $dados = DB::connection('mysql2')->table('servidors')
                    ->leftJoin('lotacaos', 'lotacaos.id', '=', 'servidors.lotacao')
                    ->select('lotacaos.sigla')->where('servidors.cpf', $cpf)->get();

                return $dados[0]->sigla;

            }else{
                $siglaUp = UnidadesController::getSiglaUp($lotacao[0]->unidade);

                return $siglaUp;

            }

        }else {


            $dados = DB::connection('mysql2')->table('servidors')
                ->leftJoin('lotacaos', 'lotacaos.id', '=', 'servidors.lotacao')
                ->select('lotacaos.sigla')->where('servidors.cpf', $cpf)->get();
            
            if(isset($dados[0])){
                return $dados[0]->sigla;
            }
            else{
                return "Sem Lotação no RH";
            }

        }

    }

    public static function getIdLotacaoServidor($cpf, $rh = "N"){



        if (Auth::user()->visualiza_outras_up == 'S' && $rh == "N") {

            $lotacao = DB::table('unidade_user_temp')->where('cpf', Auth::user()->cpf)->get();

            if(count($lotacao) == 0)
            {

                $dados = DB::connection('mysql2')->table('servidors')
                    ->leftJoin('lotacaos', 'lotacaos.id', '=', 'servidors.lotacao')
                    ->select('lotacaos.sigla')->where('servidors.cpf', $cpf)->get();

                return UnidadesController::getIdUpPorSigla($dados[0]->sigla);

            }else{
                $siglaUp = UnidadesController::getSiglaUp($lotacao[0]->unidade);

                return UnidadesController::getIdUpPorSigla($siglaUp);

            }

        }else {


            $dados = DB::connection('mysql2')->table('servidors')
                ->leftJoin('lotacaos', 'lotacaos.id', '=', 'servidors.lotacao')
                ->select('lotacaos.sigla')->where('servidors.cpf', $cpf)->get();


            return UnidadesController::getIdUpPorSigla($dados[0]->sigla);

        }

    }

    public static function getServidorLotacao($sigla){

        $dados = DB::connection('mysql2')->table('servidors')
            ->leftJoin('lotacaos', 'lotacaos.id', '=', 'servidors.lotacao')
            ->select('lotacaos.*','servidors.*')->where('lotacaos.sigla', $sigla)->get();

        return $dados;

    }
    public static function getServidorMatricula($matricula){

        $dados = DB::connection('mysql2')->table('servidors')
            ->leftJoin('contatos',function($query){
                $query->on('contatos.id_servidor', '=' ,'servidors.id');
            })
            ->leftJoin('funcao',function($query){
                $query->on('funcao.id', '=' ,'servidors.funcao');
            })
            ->select('servidors.*','servidors.id as idServidor','contatos.*','funcao.id','funcao.nome AS funcNome')
            ->where('servidors.matricula', $matricula)
            ->orderBy('servidors.nome','ASC')
            ->get();

        return $dados;

    }

    public function getDadosServidorPorNomeKeyUp(Request $request)
    {
        $nome = $request->get('nome');

        if(is_numeric($nome))
        {
            $dados = DB::connection('mysql2')->table('servidors')
                ->leftJoin('lotacaos', 'lotacaos.id', '=', 'servidors.lotacao')
                ->select("servidors.id","servidors.nome","servidors.matricula","servidors.cpf","lotacaos.sigla AS lotacao")->where('matricula','LIKE', "%".$nome."%")->get();

        }else{

            $dados = DB::connection('mysql2')->table('servidors')
                ->leftJoin('lotacaos', 'lotacaos.id', '=', 'servidors.lotacao')
                ->select("servidors.id","servidors.nome","servidors.matricula","servidors.cpf","lotacaos.sigla AS lotacao")->where('nome','LIKE', "%".$nome."%")->get();

        }


        $data = array();

        foreach($dados as $d)
        {
            array_push($data, array(
                "id" => $d->id,
                "nome" => strtoupper($d->nome),
                "lotacao" => utf8_encode($d->lotacao),
                "foto" => config('app.rh')."/storage/app/arquivos/servidores/".$d->id."/foto/".$d->id.".jpeg",
                "matricula" => $d->matricula,
                "cpf" => $d->cpf
            ));

        }

        return $data;

    }

    public function getTodosServidoresUnidade(Request $request)
    {
        $lotacaoServidor = FacadeController::getLotacaoServidor(Auth::user()->cpf);

        $dados = DB::connection('mysql2')->table('servidors')
            ->leftJoin('lotacaos', 'lotacaos.id', '=', 'servidors.lotacao')
            ->select("servidors.id","servidors.nome","servidors.matricula","servidors.cpf","lotacaos.sigla AS lotacao")
            ->where('lotacaos.sigla',$lotacaoServidor)
            //->where('cargo',1)
            ->get();

        $data = array();

        foreach($dados as $d)
        {
            array_push($data, array(
                "id" => $d->id,
                "nome" => $d->nome,
                "lotacao" => utf8_encode($d->lotacao),
                "foto" => config('app.rh')."/storage/app/arquivos/servidores/".$d->id."/foto/".$d->id.".jpeg",
                "matricula" => $d->matricula,
                "cpf" => $d->cpf
            ));

        }

        return $data;

    }

    public static function isLogadoSecretario(){
        return (Auth::user()->cpf == "11180625234");
    }

    public static function isLogadoAdjunto(){
        return (Auth::user()->cpf == "02508041408");
    }


}
