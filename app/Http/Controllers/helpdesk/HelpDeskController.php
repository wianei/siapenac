<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a Escolta
|
*/

namespace App\Http\Controllers\helpdesk;

use App\Classes\Tdata;
use App\Http\Controllers\Interno\InternoController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Usuario\UsuarioController;
use App\Http\Controllers\Permissoes\PermissoesController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Classes\Constantes;

class HelpDeskController extends Controller
{
    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->middleware('auth');

    }

    public function callAction($method, $parameters)
    {
        LogController::logAcessoUser();
        
        return parent::callAction($method, $parameters);
    }

    public function chamadosListar()
    {
        $chamados = DB::table('helpdesk')->where('user', Auth::user()->cpf)->orderBy('update_at', 'DESC')->orderBy('prioridade', 'DESC')->orderBy('status', 'DESC')->get();

        foreach($chamados as $chamado)
        {
            $chamado->created_at = Tdata::TtimestampBR($chamado->created_at);
            $chamado->update_at = Tdata::TtimestampBR($chamado->update_at);

        }
        return view('helpdesk.chamados.listar', compact('chamados'));
    }

    public function abrirChamados()
    {
        return view('helpdesk.chamados.abrir');
    }

    public function abrirChamadosStore(Request $request)
    {
        //dd($request->input());

        $assunto = $request->get('assunto');
        $descricao = $request->get('descricao');
        $prioridade = $request->get('prioridade');


        $insertChamado = DB::table('helpdesk')->insert([
           'user' => Auth::user()->cpf,
           'prioridade' => $prioridade,
           'assunto' => $assunto,
           'descricao' => $descricao,
           'status' => 'A',
           'created_at' => Tdata::TtimeStampHojeMysql(),
           'update_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        $idChamado = DB::getPdo()->lastInsertId();

        if(PermissoesController::getTipoPermissao() == "I") {

            LogController::log($idChamado, "helpdesk", "add", Auth::user()->cpf, "Abertura de Chamado<br>Prioridade: $prioridade<br>Assunto: $assunto<br>Descricao: $descricao");
        }

        $insertChamado = DB::table('helpdesk_notification')->insert([
            'fk_chamado' => $idChamado,
            'user' => Auth::user()->cpf,
            'status' => 'I',
            'status_chamado' => 'A',
        ]);

        // -------------------- inserir os Documentos --------------------------- //

        if($request->file('inputFile')[0] != null) {

            $qtdDoc = count($request->file('inputFile'));

            for ($x = 0; $x < $qtdDoc; $x++) {

                $destinationPath = Constantes::ARQUIVOS_HELPDESK . "/" . $idChamado;

                $name = $idChamado ."_" . $x."_" . time() . "_" . $request->file('inputFile')[$x]->getClientOriginalName();

                $request->file('inputFile')[$x]->storeAs($destinationPath, $name);
                //Storage::put($name, $destinationPath);

                $insertDoc = DB::table('helpdesk_arquivos')->insert(
                    [
                        'fk_chamado' => $idChamado,
                        'nome_arquivo' => $name,
                        'created_at' => Tdata::TtimeStampHojeMysql()
                    ]
                );

                $idDoc = DB::getPdo()->lastInsertId();

                if(PermissoesController::getTipoPermissao() == "I") {
                    LogController::log($idDoc, "helpdesk_arquivos", "add", Auth::user()->cpf, "Inclusão de documento no chamado<br>Arquivo: <i><strong>" . $name . "</strong></i>");
                }


            }
        }

        // -------------------- !! inserir os Fotos --------------------------- //

        if($idChamado)
        {
            return redirect()->route('helpDesk.chamadosListar')->with('alert-success', 'Chamado aberto com sucesso, em breve retornaremos!');

        }


    }

    public function chamadosVisualizar($id)
    {

        $chamadoDados = DB::table('helpdesk')
            ->where('id', $id)
            ->orderBy('update_at', 'DESC')
            ->orderBy('prioridade', 'DESC')
            ->orderBy('status', 'DESC')
            ->get();

        if($chamadoDados[0]->user != Auth::user()->cpf){

            $retornoPermissao = PermissoesController::getVerifyPermissionController("SeticController");
            if($retornoPermissao == false){

                return redirect()->route('helpDesk.chamadosListar')->with('alert-danger', 'Você não tem permissão para visualizar esse chamado.');

            }

        }



            if($chamadoDados[0]->user == Auth::user()->cpf)
        {
            $zeraNotificacao = DB::table('helpdesk_notification')->where('fk_chamado', $id)->update(['status'=>'I']);

        }

        $chamados = array();

        foreach($chamadoDados as $chamadoDado)
        {
            $interacoes = DB::table('helpdesk_interation')->where('fk_chamado', $chamadoDado->id)->orderBy('created_at','DESC')->get();
            $interacoesDados = array();
            foreach($interacoes as $interacoe)
            {
                $interacoesArquivos = DB::table('helpdesk_arquivos_interation')->where('fk_interation', $interacoe->id)->get();

                if($interacoe->user == "0000000000")
                {
                    $nomeUser = "".config('app.name');
                }else{
                    $nomeUser = UsuarioController::getNomeUser($interacoe->user);
                }
                array_push($interacoesDados, array(
                    'nome' =>  $nomeUser,
                    'user' =>  $interacoe->user,
                    'data' => Tdata::TtimestampBR($interacoe->created_at),
                    'descricao' => $interacoe->descricao,
                    'arquivos' => $interacoesArquivos,
                ));

                $interacoe->created_at = Tdata::TtimestampBR($interacoe->created_at);
                $interacoe->user = $nomeUser;
            }

            $arquivos = DB::table('helpdesk_arquivos')->where('fk_chamado', $chamadoDado->id)->orderBy('created_at','DESC')->get();
            $dadosUser = UsuarioController::getDadosUser($chamadoDado->user);

            array_push($chamados, array(
               'id' => $chamadoDado->id,
               'user' => $chamadoDado->user,
               'nomeUser' => $dadosUser[0]->name,
               'prioridade' => $chamadoDado->prioridade,
               'assunto' => $chamadoDado->assunto,
               'descricao' => $chamadoDado->descricao,
               'status' => $chamadoDado->status,
               'created_at' => Tdata::TtimestampBR($chamadoDado->created_at),
               'interacoes' => $interacoesDados,
               'arquivos' => $arquivos,
            ));
        }

        //dd($chamados);

        return view('helpdesk.chamados.visualizar', compact('chamados'));
    }

    public function responderChamado(Request $request, $id)
    {

        $chamadoDados = DB::table('helpdesk')
            ->where('id', $id)
            ->orderBy('update_at', 'DESC')
            ->orderBy('prioridade', 'DESC')
            ->orderBy('status', 'DESC')
            ->get();



        if(Auth::user()->cpf != $chamadoDados[0]->user)
        {

            $notificacao = DB::table('helpdesk_notification')->where('fk_chamado', $id)->update(['status'=>'A']);

        }

        $insertInteracao = DB::table('helpdesk_interation')->insert([
            'fk_chamado' => $id,
            'user' => Auth::user()->cpf,
            'descricao' => $request->get('descricao'),
            'created_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        $idInteration = DB::getPdo()->lastInsertId();

        $update = DB::table('helpdesk')->where('id', $id)->update(['update_at'=>Tdata::TtimeStampHojeMysql()]);


        // -------------------- inserir os Documentos --------------------------- //

        if($request->file('inputFile')[0] != null) {

            $qtdDoc = count($request->file('inputFile'));

            for ($x = 0; $x < $qtdDoc; $x++) {

                $destinationPath = Constantes::ARQUIVOS_HELPDESK . "/" . $id;

                $name = $id ."_" . $x."_" . time() . "_" . $request->file('inputFile')[$x]->getClientOriginalName();

                $request->file('inputFile')[$x]->storeAs($destinationPath, $name);
                //Storage::put($name, $destinationPath);

                $insertDoc = DB::table('helpdesk_arquivos_interation')->insert(
                    [
                        'fk_interation' => $idInteration,
                        'nome_arquivo' => $name,
                        'created_at' => Tdata::TtimeStampHojeMysql()
                    ]
                );

                $idDoc = DB::getPdo()->lastInsertId();

                LogController::log($idDoc,"helpdesk_arquivos","add", Auth::user()->cpf, "Inclusão de documento no chamado<br>Arquivo: <i><strong>". $name ."</strong></i>");


            }
        }

        // -------------------- !! inserir os Fotos --------------------------- //


        if($insertInteracao)
        {
            return redirect()->back()->with('alert-succsess', 'Resposta postada com sucesso!');
        }
    }

    public function fecharChamado($id)
    {

        if(PermissoesController::getVerifyPermissionController("SeticController")) {

            $update = DB::table('helpdesk')->where('id', $id)->update([
                'status' => 'F'
            ]);

            $update = DB::table('helpdesk_notification')->where('fk_chamado', $id)->update([
                'status' => 'A',
                'status_chamado' => 'F'
            ]);

            $insertInteracao = DB::table('helpdesk_interation')->insert([
                'fk_chamado' => $id,
                'user' => Auth::user()->cpf,
                'descricao' => "Chamado Fechado",
                'created_at' => Tdata::TtimeStampHojeMysql(),
            ]);

            return redirect()->route('setic.listarChamadosHelpDesk')->with('alert-succsess', 'Chamado fechado com sucesso!');

        }else{

            $update = DB::table('helpdesk')->where('user', Auth::user()->cpf)->where('id', $id)->update([
                'status' => 'F'
            ]);

            $update = DB::table('helpdesk_notification')->where('fk_chamado', $id)->update([
                'status_chamado' => 'F'
            ]);

            $insertInteracao = DB::table('helpdesk_interation')->insert([
                'fk_chamado' => $id,
                'user' => Auth::user()->cpf,
                'descricao' => "Chamado Fechado",
                'created_at' => Tdata::TtimeStampHojeMysql(),
            ]);


        }

        return redirect()->route('helpDesk.chamadosListar')->with('alert-succsess', 'Chamado fechado com sucesso!');
    }

}