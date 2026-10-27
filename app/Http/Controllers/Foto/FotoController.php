<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a Escolta
|
*/

namespace App\Http\Controllers\Foto;

use App\Classes\Tdata;
use App\Http\Controllers\Interno\InternoController;
use App\Http\Controllers\Log\LogController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Classes\Constantes;

class FotoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    /**
     * *************************************************
     * funcoes para migracao de fotos
     * *************************************************
     */

    public static function migracaoFotoAdv()
    {

        // $diretorio = storage_path('app/arquivos/advogado/fotos/extracao_fotos_def_PB');
        // $files = scandir($diretorio);

        $fotos = DB::table('foto')->where('fk_tabela','advogado')->get();
        foreach(json_decode($fotos, true) as $foto){
            if(!File::isDirectory(storage_path('app/arquivos/advogados/fotos/'.$foto['fk_id']))){
                File::makeDirectory(storage_path('app/arquivos/advogados/fotos/'.$foto['fk_id'], 0777, true, true));
            }
            $move = Storage::move('arquivos/advogados/fotos/extracao_fotos_def_BA/'.$foto['nome_arquivo'], 'arquivos/advogados/fotos/'.$foto['fk_id'].'/'.$foto['nome_arquivo']);
        }

        // dd("FIM - migracaoFotoAdv()");
        
    }
    public static function migracaoFotoVisita()
    {
        
        $fotos = DB::table('foto')->where('fk_tabela','visita')->get();
        foreach(json_decode($fotos, true) as $foto){
            if(!File::isDirectory(storage_path('app/arquivos/visitas/fotos/'.$foto['fk_id']))){
                File::makeDirectory(storage_path('app/arquivos/visitas/fotos/'.$foto['fk_id'], 0777, true, true));
            }
            

            $move = Storage::move('arquivos/visitas/fotos/extracao_fotos_vis_BA/'.$foto['nome_arquivo'], 'arquivos/visitas/fotos/'.$foto['fk_id'].'/'.$foto['nome_arquivo']);
        }

        // dd("FIM - migracaoFotoVisita()");
    }

    public static function migracaoArquivoVis()
    {
        
        $fotos = DB::table('documento')->where('fk_tabela','visita')->get();
        foreach(json_decode($fotos, true) as $foto){
            if(!File::isDirectory(storage_path('app/arquivos/visitas/documentos/'.$foto['fk_id']))){
                File::makeDirectory(storage_path('app/arquivos/visitas/documentos/'.$foto['fk_id'], 0777, true, true));
            }
            $move = Storage::move('arquivos/visitas/documentos/extracao_fotos_vis_PB/'.$foto['nome_arquivo'], 'arquivos/visita/documentos/'.$foto['fk_id'].'/'.$foto['nome_arquivo']);
        }
        
        // dd("FIM - migracaoArquivoVis()");

    }

    public function migracaoFotoVisitaAjax()
    {
        //dd("ok");
        $diretorio = storage_path('app/arquivos/fotos_visitantes');
        $files = scandir($diretorio);
        $nameFile = explode("_", str_replace(".jpg","",$files[2]));


        if(isset($nameFile[1])) {
            $tipoFoto = $words = preg_replace('/[0-9]+/', '', $nameFile[1]);
        }else{
            $tipoFoto = "principal";
        }

        if($tipoFoto == "tatuagem")
        {
            $tipoFoto = "Tatuagem";
        }


        $move = Storage::move(('arquivos/fotos_visitantes')."/".$files[2], Constantes::ARVQUIVOS_VISITA . "/fotos/" . $nameFile[0] . "/" . $files[2]);

        $saveBD = DB::table("foto")->insert([
            'fk_id' => $nameFile[0],
            'fk_tabela' => "visita",
            'nome_arquivo' => $files[2],
            'tipo' => $tipoFoto,
            'created_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        //dd($diretorio."/".$files[2]);
        return $nameFile[0];
    }
    public static function migracaoFotoInterno()
    {
        $fotos = DB::table('foto')->where('fk_tabela','interno')->get();
        
        foreach(json_decode($fotos, true) as $foto){
            if(!File::isDirectory(storage_path('app/arquivos/internos/fotos/'.$foto['fk_id']))){
                
                File::makeDirectory(storage_path('app/arquivos/internos/fotos/'.$foto['fk_id'], 0777, true, true));
                
                //dd(!File::isDirectory('arquivos/internos/fotos/'.$foto['fk_id']));
            }
            if(File::isFile(storage_path('app/arquivos/internos/fotos/extracao_fotos_cust_BA/'.$foto['nome_arquivo']))){
                
                $move = Storage::move('arquivos/internos/fotos/extracao_fotos_cust_BA/'.$foto['nome_arquivo'], 'arquivos/internos/fotos/'.$foto['fk_id'].'/'.$foto['nome_arquivo']);
            }
        }
        
        // dd("FIM");
    }
    public function migracaoFotos()
    {
        self::migracaoFotoAdv();
        self::migracaoFotoVisita();
        self::migracaoFotoInterno();
        self::migracaoArquivoVis();
        dd("fotos de interno, visitas e advogados migradas.", "arquivos de visitas migrados.");
    }
    public function migracaoFotoInternoAjax()
    {
       $diretorio = storage_path('app/arquivos/fotos_internos');
        $files = scandir($diretorio);
        $nameFile = explode("_", str_replace(".jpg","",$files[2]));

        if(isset($nameFile[1])) {
            $tipoFoto = $words = preg_replace('/[0-9]+/', '', $nameFile[1]);
        }else{
            $tipoFoto = "principal";
        }

        if($tipoFoto == "tatuagem")
        {
            $tipoFoto = "Tatuagem";
        }

        $move = Storage::move(('arquivos/fotos_internos')."/".$files[2], Constantes::ARVQUIVOS_INTERNO . "/fotos/" . $nameFile[0] . "/" . $files[2]);

        $saveBD = DB::table("foto")->insert([
            'fk_id' => $nameFile[0],
            'fk_tabela' => "interno",
            'nome_arquivo' => $files[2],
            'tipo' => $tipoFoto,
            'created_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        //dd($diretorio."/".$files[2]);
        return $nameFile[0];
    }
    public function gravarFoto(Request $request){


        if($request->get('tipo') == 'principal')
        {
            $selectFoto = DB::table('foto')->where('fk_id', $request->get('idPessoa'))->where('fk_tabela',$request->get('tabela'))->where('tipo','principal')->get();

            if(count(json_decode($selectFoto,true)) > 0) {

                $renameOld = time() .$selectFoto[0]->nome_arquivo;
                Storage::move("arquivos/".$request->get('tabela')."s/fotos/" . $request->get('idPessoa') . "/" . $selectFoto[0]->nome_arquivo, "arquivos/".$request->get('tabela')."s/fotos/" . $request->get('idPessoa') . "/" . $renameOld);

                $updateTipo = DB::table('foto')->where('id', $selectFoto[0]->id)->update([
                    'tipo' => 'outros',
                    'nome_arquivo' => $renameOld,
                ]);


                $selectNewFoto = DB::table('foto')->where('id', $request->get('foto'))->get();
                Storage::move("arquivos/".$request->get('tabela')."s/fotos/" . $request->get('idPessoa') . "/" . $selectNewFoto[0]->nome_arquivo, "arquivos/".$request->get('tabela')."s/fotos/" . $request->get('idPessoa') . "/" . $selectNewFoto[0]->fk_id.".jpg");

                $updateNome = DB::table('foto')->where('id', $selectNewFoto[0]->id)->update([
                    'nome_arquivo' => $selectNewFoto[0]->fk_id.".jpg",
                    'tipo' => $request->get('tipo'),
                    'legenda' => $request->get('legenda'),
                    'updated_at' => Tdata::TtimeStampHojeMysql()

                ]);


                //$update = self::saveFoto($request->get('foto'),$request->get('tipo'),$request->get('legenda'));


                if($updateTipo)
                {
                    return "success,".$selectFoto[0]->id;

                }else{

                    return "error,e";

                }


            }else{

                $update = self::saveFoto($request->get('foto'),$request->get('tipo'),$request->get('legenda'));

                if($update)
                {
                    return "success,s";

                }else{

                    return "error,e";

                }

            }
        }else{

            $update = self::saveFoto($request->get('foto'),$request->get('tipo'),$request->get('legenda'));

            if($update)
            {
                return "success,s";

            }else{

                return "error,e";

            }

        }

//        if($request->get('tipo') == 'principal')
//        {
//            $selectFoto = DB::table('foto')->where('fk_id', $request->get('idPessoa'))->where('fk_tabela',$request->get('tabela'))->where('tipo','principal')->get();
//
//            if(count(json_decode($selectFoto,true)) > 0) {
//                $updateTipo = DB::table('foto')->where('id', $selectFoto[0]->id)->update([
//                    'tipo' => 'outros',
//                ]);
//
//                $update = self::saveFoto($request->get('foto'),$request->get('tipo'),$request->get('legenda'));
//
//
//                if($update)
//                {
//                    return "success,".$selectFoto[0]->id;
//
//                }else{
//
//                    return "error,e";
//
//                }
//
//
//            }else{
//
//                $update = self::saveFoto($request->get('foto'),$request->get('tipo'),$request->get('legenda'));
//
//                if($update)
//                {
//                    return "success,s";
//
//                }else{
//
//                    return "error,e";
//
//                }
//
//            }
//        }else{
//
//            $update = self::saveFoto($request->get('foto'),$request->get('tipo'),$request->get('legenda'));
//
//            if($update)
//            {
//                return "success,s";
//
//            }else{
//
//                return "error,e";
//
//            }
//
//        }
//

    }

    public function excluirFoto(Request $request)
    {
        $selectFoto = DB::table('foto')->where('id', $request->get('foto'))->where('fk_tabela',$request->get('tabela'))->get();

        if($request->get('tabela') == 'interno') {

            if (file_exists(storage_path('app/arquivos/internos/fotos/' . $selectFoto[0]->fk_id . "/" . $selectFoto[0]->nome_arquivo))) {

                $move = Storage::move(Constantes::ARVQUIVOS_INTERNO . "/fotos/" . $selectFoto[0]->fk_id . "/" . $selectFoto[0]->nome_arquivo, Constantes::ARQUIVO_MORTO . "/" . $selectFoto[0]->nome_arquivo);
            }else{

                $delete = DB::table('foto')->where('id', $request->get('foto'))->delete();
                LogController::log($request->get('foto'),"foto","delete", Auth::user()->cpf, "Exclusão de foto da tabela ".$request->get('tabela'));

                return "errorMove";
            }

        }

        if($request->get('tabela') == 'visita') {

            if (file_exists(storage_path('app/arquivos/visitas/fotos/' . $selectFoto[0]->fk_id . "/" . $selectFoto[0]->nome_arquivo))) {

                $move = Storage::move(Constantes::ARVQUIVOS_VISITA . "/fotos/" . $selectFoto[0]->fk_id . "/" . $selectFoto[0]->nome_arquivo, Constantes::ARQUIVO_MORTO . "/" . $selectFoto[0]->nome_arquivo);
            }else{

                $delete = DB::table('foto')->where('id', $request->get('foto'))->delete();
                LogController::log($request->get('foto'),"foto","delete", Auth::user()->cpf, "Exclusão de foto da tabela ".$request->get('tabela'));

                return "errorMove";
            }

        }

        if($request->get('tabela') == 'advogado') {

            if (file_exists(storage_path('app/arquivos/advogados/fotos/' . $selectFoto[0]->fk_id . "/" . $selectFoto[0]->nome_arquivo))) {

                $move = Storage::move(Constantes::ARVQUIVOS_ADVOGADO . "/fotos/" . $selectFoto[0]->fk_id . "/" . $selectFoto[0]->nome_arquivo, Constantes::ARQUIVO_MORTO . "/" . $selectFoto[0]->nome_arquivo);
            }else{

                $delete = DB::table('foto')->where('id', $request->get('foto'))->delete();
                LogController::log($request->get('foto'),"foto","delete", Auth::user()->cpf, "Exclusão de foto da tabela ".$request->get('tabela'));

                return "errorMove";
            }

        }

        $delete = DB::table('foto')->where('id', $request->get('foto'))->delete();

        LogController::log($request->get('foto'),"foto","delete", Auth::user()->cpf, "Exclusão de foto da tabela ".$request->get('tabela'));

        if($delete == 1)
        {
            return "success";

        }else{

            return "error";
        }


    }

    /**
     * salva os dados da foto no BD
     * @param $idFoto
     * @param $tipo
     * @param $legenda
     * @return bool
     */
    function saveFoto($idFoto,$tipo,$legenda)
    {
        $update = DB::table('foto')->where('id', $idFoto)->update([
            'tipo' => $tipo,
            'legenda' => $legenda,
            'updated_at' => Tdata::TtimeStampHojeMysql()
        ]);

        if($update)
        {
            return true;
        }else{
            return false;
        }

    }

    /**
     * retorna o nome da foto principal de um usuário
     * @param $idUser
     * @param $nomeTable
     * @return string
     */
    public static function getFotoPrincipalByIdTipo($idUser, $nomeTable){
        $foto = DB::table('foto')
            ->where('fk_id', $idUser)
            ->where('fk_tabela', $nomeTable)
            ->where('tipo', 'principal')
        ->select('nome_arquivo')->first();

        if(!is_null($foto)){
            return $foto->nome_arquivo;
        }

        return "null";
    }
    
    
    public static function getFotoPrincipalInternoBase64($id)
    {
        $foto = DB::table('foto')->where('fk_id', $id)->where('fk_tabela','interno')->where('tipo','principal')->get();

        if(count($foto) == 0)
        {
            $imagem = 'imagens/usuario/avatarInterno.png';
            $imgBase64 = InternoController::imagensInternoMergeCpfUser($id, public_path($imagem));

            return $imgBase64;
            
        }else{
            
            $imagem = 'app/arquivos/internos/fotos/' . $id . '/' . $foto[0]->nome_arquivo;
            $imgBase64 = InternoController::imagensInternoMergeCpfUser($id, storage_path($imagem));
            return $imgBase64;
        }

    }


}