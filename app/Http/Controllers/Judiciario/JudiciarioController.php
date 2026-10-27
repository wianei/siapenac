<?php


/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados ao Judiciario
|
*/

namespace App\Http\Controllers\Judiciario;

use App\Classes\Constantes;
use App\Classes\Tdata;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Gep\GepController;
use App\Http\Controllers\Interno\InternoController;
use \App\Http\Controllers\Permissoes\PermissoesController;
use App\Http\Controllers\Log\LogController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JudiciarioController extends Controller
{

    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');

        $this->middleware('auth');

    }

    public static function getNomeVaraComarca($id)
    {
        $dados = DB::table('judiciario_dados')->where('id', $id)->get();

        return $dados[0]->vara." - ".$dados[0]->comarca;
    }
    public static function getQualificacaoVara($id)
    {
        $dados = DB::table('judiciario_dados')->where('id', $id)->get();

        return $dados[0]->qualificacao;
    }
    public static function getJuizVara($id)
    {
        $dados = DB::table('judiciario_dados')->where('id', $id)->get();

        return $dados[0]->juiz;
    }
    public static function getComarcaVara($id)
    {
        $dados = DB::table('judiciario_dados')->where('id', $id)->get();

        return $dados[0]->comarca;
    }

    public static function getdadosJudiciario($id)
    {
        $dados = DB::table('judiciario_dados')->where('id', $id)->get();

        return $dados[0];
    }
    public function removeAccents($string) {
        $string = utf8_encode($string);
        return strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8'))), ' '));
    }
    public function agendamentos(Request $request){

        LogController::logAcessoUser();

        //dd($request->all());

        $retornoPermissao = PermissoesController::getVerifyPermissionController("JudiciarioController");
        
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }


        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        /**
         * se não realizar uma busca
         * - Evita que mostre todos os resultados sem realizar uma busca
         */
        if(!strpos($url,"?"))
        {

            $dados = false;

        }else {

            $escolta = DB::table('escolta');

            $data = Tdata::TdataMysql($request->get('data'));
            $dataFinal = Tdata::TdataMysql($request->get('dataFinal'));
            $horario = $request->get('horario');
            $documento = $request->get('documento');
            $status = $request->get('status');

            if ($data != null) {
                $escolta->where('escolta.data_saida','>=', $data);
            }

            if ($dataFinal != null) {
                $escolta->where('escolta.data_saida','<=', $dataFinal);
            }


            if ($horario != null) {
                $escolta->where('escolta.hora_saida', $horario);
            }

            if ($documento != null) {
                $escolta->where('escolta.documento','LIKE', '%'.$documento.'%');
            }

            if ($status != null) {
                $escolta->where('escolta.status', $status);
            }


            $escolta->where('escolta.solicitante', Auth::user()->cpf);

            // passa a query para a paginacao "->appends..."
            $dados = $escolta->select("escolta.data_saida","escolta.id","escolta.hora_saida","escolta.destino", "escolta.documento","escolta.status", DB::raw("DATE_FORMAT(escolta.data_saida,'%d/%m/%Y') as dataSaida"))->orderBy('escolta.data_saida','DESC')->paginate(50)->appends($request->input());


        }


        return view('judiciario.agendamentos.agendamentos', compact('dados'));

    }

    public function show($id)
    {
        LogController::logAcessoUser();

        $retornoPermissao = PermissoesController::getVerifyPermissionController("JudiciarioController");
        
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

        $escolta = DB::table('escolta')->where('id',$id)->get();


        $relacaoInterno = DB::table('escolta_internos')->where('fk_escolta',$id)->get();

        $dadosInterno = array();
        foreach($relacaoInterno as $relacao)
        {
            $dadosInternos = (InternoController::getDadosPrincipaisInterno($relacao->fk_interno));

            $dadosFoto = DB::table('foto')
                ->where('fk_id', $dadosInternos[0]['id'])
                ->where('fk_tabela', 'interno')
                ->where('tipo','principal')->get();
            $dadosFoto = json_decode($dadosFoto,true);

            if($dadosFoto == []){
                $dadosFoto[0]['tipo'] = "";
                $dadosFoto[0]['legenda'] = "";
                $dadosFoto[0]['created_at'] = "";
                $dadosFoto[0]['updated_at'] = "";
            }

            array_push($dadosInterno, array(
                "nome" => $dadosInternos[0]['nome'],
                "id" => $dadosInternos[0]['id'],
                "faccao" => InternoController::getNomeFaccao($dadosInternos[0]['faccao']),
                "lideranca" => $dadosInternos[0]['lideranca'],
                "foto" => $dadosInternos[0]['foto'],
                "fototipo" => $dadosFoto[0]['tipo'],
                "fotolegenda" => $dadosFoto[0]['legenda'],
                "fotodatainserida" => $dadosFoto[0]['created_at'],
                "fotodataalterada" => $dadosFoto[0]['updated_at'],
                "sigla" => $dadosInternos[0]['sigla']
            ));

        }

        $arquivos = DB::table('escolta_arquivos')->where('fk_escolta', $id)->get();

        foreach($arquivos as $arquivo)
        {
            $arquivo->created_at = Tdata::TdataBR($arquivo->created_at);
        }


        return view('judiciario.agendamentos.show', compact('escolta','dadosInterno','arquivos'));

    }

    public function novoAgendamento(){

        LogController::logAcessoUser();


        $retornoPermissao = PermissoesController::getVerifyPermissionController("JudiciarioController");
        
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }


        $destinos = DB::table('judiciario_dados')->orderBy('vara', 'ASC')->get();

        return view('judiciario.agendamentos.novoAgendamento',compact('destinos'));

    }

    public function novoAgendamentoStore(Request $request){

        LogController::logAcessoUser();

        $retornoPermissao = PermissoesController::getVerifyPermissionController("JudiciarioController");
        
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

        $internos = $request->get('internos');
        $gepResponsavel = GepController::getIdGepResponsavel(InternoController::getIdUnidadeInterno($internos[0]));
        //dd($request->all());

        $insertEscolta = DB::table('escolta')->insert([
//                'unidade' => "judiciario",
                'tipo' => "gep",
                'destino' => $request->get('destino'),
                'documento' => $request->get('documento'),
                'data_saida' => Tdata::TdataMysql($request->get('data')),
                'hora_saida' => $request->get('horario'),
                'status' => "Solicitada",
                'solicitante' => Auth::user()->cpf,
                'responsavel' => $gepResponsavel,
                'created_at' => date("Y-m-d"),
            ]
        );

        $idEscolta = DB::getPdo()->lastInsertId();

        foreach($internos as $interno)
        {
            $insertServidores = DB::table('escolta_internos')->insert([
                    'fk_escolta' => $idEscolta,
                    'fk_interno' => $interno,
                ]
            );

        }

        // se enviar o arquivo
        if($request->file('inputFile')[0] != null) {

            $qtdArquivo = count($request->file('inputFile'));

            for ($x = 0; $x < $qtdArquivo; $x++) {

                $destinationPath = Constantes::ARQUIVOS_ESCOLTA . "/" . $idEscolta;

                $name = "escolta".$idEscolta . "_" . time() . "_" . $request->file('inputFile')[$x]->getClientOriginalName();

                $request->file('inputFile')[$x]->storeAs($destinationPath, $name);

                $insertArquivo = DB::table('escolta_arquivos')->insert(
                    ['fk_escolta' => $idEscolta,
                        'nome_arquivo' => $name,
                        'created_at' => date("Y-m-d")
                    ]
                );


            }
        }

        LogController::log($idEscolta,"escolta","add", Auth::user()->cpf, "Solicitação de escolta realizada com destino a ".$request->get('destino').", em ".Tdata::TtimestampBR(date("Y-m-d H:i:s")));

        if ($idEscolta) {

            return redirect()->route('judiciario.agendamentos')->with('alert-success', 'Escolta solicitada com sucesso!');

        }else{

            return redirect('judiciario.novoAgendamento')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }

    }

    public function cancelarAgendamento($id)
    {

        LogController::logAcessoUser();

        $retornoPermissao = PermissoesController::getVerifyPermissionController("JudiciarioController");
        
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

        $cancelarAgendamento = DB::table('escolta')->where('id',$id)->update(['status'=>'Cancelada']);

        if ($cancelarAgendamento) {

            return redirect()->route('judiciario.agendamentos')->with('alert-success', 'Escolta cancelada com sucesso!');

        }else{

            return redirect()->route('judiciario.agendamentos')->with('alert-danger', 'Ocorreu um problema ao cancelar, por favor, tente novamente!');

        }
    }
}
