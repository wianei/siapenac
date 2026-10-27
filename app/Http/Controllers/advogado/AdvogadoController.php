<?php


/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a visita
|
*/

namespace App\Http\Controllers\Advogado;

use App\Advogado;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Interno\InternoController;
use App\Http\Controllers\Ocorrencia\OcorrenciaController;
use App\Contato;
use DateTime;
use App\Classes\Constantes;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Oab\OabController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Http\Controllers\Permissoes\PermissoesController;
use App\Http\Controllers\Visita\VisitaController;
use App\Mail\SendMailCadastroAdv;
use App\Mail\SendMailCancelamentoAgendamentoAdv;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Classes\Tdata;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AdvogadoController extends Controller
{

    private $advogado;

    public function __construct(Advogado $advogado)
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->$advogado = $advogado;
        $this->middleware('auth');

    }

    public function callAction($method, $parameters)
    {
        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::verifyPermission("AdvogadoController", $method);
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

//        $metodosControlador = collect(['cancelarAgendamentos','cancelarAgendamentosStore']);
//        $metodosAdm = collect(['cadastro','store','editar','update','documentoDelete']);
//        $metodosComum = collect(['envioSenha','inicioAtendimento','inicioAtendimentoStore','atendimentoHistorico','buscar','show']);
//
//        if($metodosControlador->contains($method))
//        {
//            if (Auth::user()->tipo < 700) {
//
//                return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//            }
//
//        }
//
//        if($metodosAdm->contains($method))
//        {
//            if (Auth::user()->tipo < 600) {
//
//                return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//            }
//
//        }
//
//        if($metodosComum->contains($method))
//        {
//            if (Auth::user()->tipo < 300) {
//
//                return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//            }
//
//        }
//


        return parent::callAction($method, $parameters);
    }


    /*
    |--------------------------------------------------------------------------
    | Getters e Setters
    |--------------------------------------------------------------------------
    |
    */
    public static function getDadosAdv($id)
    {
        $dados = DB::table('advogado')->where('id', $id)->get();

        return $dados;
    }

    public static function getNomeAdv($id)
    {
        $dados = DB::table('advogado')->where('id', $id)->get();

        return $dados[0]->nome;
    }

    public static function getNomeAdvCPF($cpf)
    {
        $dados = DB::table('advogado')->where('cpf', $cpf)->get();

        return $dados[0]->nome;
    }

    public static function getOabAdv($id)
    {
        $dados = DB::table('advogado')->where('id', $id)->get();

        return $dados[0]->oab_numero."/".$dados[0]->oab_uf;
    }

    public static function getDadosAdvCpf($cpf)
    {
        $dados = DB::table('advogado')->where('cpf', $cpf)->get();

        return $dados;
    }

    public static function getFotoAdv($id)
    {
        $foto = DB::table('foto')->where('fk_id', $id)->where('fk_tabela','advogado')->where('tipo','principal')->get();

        if(json_decode($foto, true) == [])
        {
            return "imagens/usuario/avatarInterno.png";
        }else{
            return "storage/app/arquivos/advogados/fotos/".$id."/".$foto[0]->nome_arquivo;
        }

    }

    public static function getFotoAdvCPF($cpf)
    {

        $dados = self::getDadosAdvCpf($cpf);

        $foto = self::getFotoAdv($dados[0]->id);

        return asset($foto);

    }


    /*
    |--------------------------------------------------------------------------
    | Funcoes acessadas via ajax
    |--------------------------------------------------------------------------
    |
    */
    public function getAtendimentoAdvogadoInternoAjx(Request $request)
    {

        $idInterno = $request->get('idAdv');

        $dataInicio = Tdata::TdataMysql($request->get('dataInicio'));
        $dataFinal = Tdata::TdataMysql($request->get('dataFinal'));

        $advogadoHistoricoDados = DB::table('advogado_atendimento')->where('fk_advogado', $idInterno);

        if(!empty($dataInicio) && !empty($dataFinal) )
        {
            $advogadoHistoricoDados->where('data_atendimento', '>=', $dataInicio);
            $advogadoHistoricoDados->where('data_atendimento', '<=', $dataFinal);
        }

        if(!empty($dataInicio) && empty($dataFinal) )
        {
            $advogadoHistoricoDados->where('data_atendimento', '>=', $dataInicio);
        }

        if(empty($dataInicio) && !empty($dataFinal) )
        {
            $advogadoHistoricoDados->where('data_atendimento', '<=', $dataFinal);
        }

        $dadosAtendimentoAdv = $advogadoHistoricoDados->orderBy('data_atendimento', 'DESC')->orderBy('horaInicio', 'DESC')->limit(50)->get();

        $advogadoHistorico = array();

        foreach($dadosAtendimentoAdv as $advogadoHistoricoDado)
        {
            array_push($advogadoHistorico, array(
                'nomeInterno' => InternoController::getInternoNome($advogadoHistoricoDado->fk_interno),
                'fotoInterno' => asset(InternoController::getFotoInterno($advogadoHistoricoDado->fk_interno)),
                'data' => Tdata::TdataBR($advogadoHistoricoDado->data_atendimento),
                'diaAtendimento' => substr(Tdata::TdataBR($advogadoHistoricoDado->data_atendimento),0,2),
                'anoAtendimento' => substr(Tdata::TdataBR($advogadoHistoricoDado->data_atendimento),6,4),
                'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($advogadoHistoricoDado->data_atendimento)),
                'prontuario' => $advogadoHistoricoDado->fk_interno,
                'horaInicio' => $advogadoHistoricoDado->horaInicio,
                'horaTermino' => $advogadoHistoricoDado->horaTermino,
                'duracao' => $advogadoHistoricoDado->duracao."min.",
            ));

        }

        return $advogadoHistorico;


    }

    public function buscaAjaxKeyUp(Request $request)
    {
        $url = url()->current();
        $url = explode('/',$url);
        $newUrl = $url[0]."//".$url[2]."/";

        $nome = $request->get('nome');

        $dados = DB::table('advogado')
//            ->leftJoin('foto', function ($query) {
//                $query->on('foto.fk_id', '=', 'advogado.id')->where('foto.fk_tabela', '=', 'advogado')->where('foto.tipo', 'principal');
//            })
            ->select('advogado.id','advogado.nome','advogado.oab_numero','advogado.oab_uf','advogado.status')
            ->where('advogado.nome','LIKE', "%".$nome."%")
            ->orderBy('advogado.nome', 'ASC')
            ->get();


        return $dados;

    }

    public function relacionamentoInternoAjax(Request $request)
    {
        //dd($request->all());

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $id = $request->get('id');

        $relacaoInterno = DB::table('advogado_interno')
            ->leftJoin('interno','interno.id','=','advogado_interno.fk_interno')
            ->where('interno.unidade_prisional', $lotacaoServidor)
            ->where('advogado_interno.fk_advogado',$id)->get();
        $dadosInterno = array();
        foreach($relacaoInterno as $relacao)
        {
            $dadosInternos = (InternoController::getDadosPrincipaisInterno($relacao->fk_interno));

            array_push($dadosInterno, array(
                "nome" => $dadosInternos[0]['nome'],
                "id" => $dadosInternos[0]['id'],
                "sigla" => $dadosInternos[0]['sigla']
            ));

        }

        return $dadosInterno;

    }

    public function envioSenha($id){
        

        $dadosAdv = DB::table('advogado')->where('id', $id)->get();

        if($dadosAdv[0]->email == null)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'O advogado não possui email cadastrado!');

        }

        $verificaCadastroUser = DB::table('users')->where('cpf', $dadosAdv[0]->cpf)->get();

        if(count($verificaCadastroUser) > 0 )
        {
            return redirect()->back()->withInput()->with('alert-danger', 'O sistema já possui uma conta ativa para este advogado!');

        }

        $verficaEmailUser = DB::table('users')->where('email', $dadosAdv[0]->email)->count();
        if($verficaEmailUser > 0)
        {
            return redirect()->back()->withInput()->with('alert-danger', 'O sistema já possui uma conta para o email informado (<strong>'.$dadosAdv[0]->email.'</strong>)');

        }
        else {

            $senha = str_random(8);

            $senhaDB = Hash::make($senha);

            $insertUser = DB::table('users')->insert([
                'name' => $dadosAdv[0]->nome,
                'cpf' => $dadosAdv[0]->cpf,
                'email' => $dadosAdv[0]->email,
                'password' => $senhaDB,
                'tipo' => "10",
                'cargo' => "ADVOGADO",
                'orgao' => "OAB",
                'status' => "A",
                'visualiza_outras_up' => "N",
                'ultima_alteracao_senha' => Tdata::TtimeStampHojeMysql(),
                'created_at' => Tdata::TtimeStampHojeMysql(),
            ]);

            $idInsert = DB::getPdo()->lastInsertId();

            $enviado = Mail::to($dadosAdv[0]->email)->send(new SendMailCadastroAdv($dadosAdv[0]->nome, $dadosAdv[0]->cpf, $dadosAdv[0]->email, $senha, "[SEAP] Envio de senha para Conta no SIAPEN-BA"));

            if(count(Mail::failures()) == 0){

                LogController::log($idInsert,"users","add",Auth::user()->cpf,"Autorização para Agendamento de Atendimento do Advogado, CPF: ".$dadosAdv[0]->cpf);

                return redirect()->back()->withInput()->with('alert-success', 'Autorização realizada com sucesso!');

            }

        }
    }

    public function cadastro()
    {
        return view('advogado.cadastro.cadastro');
    }

    /**
     * @param Request $request
     * @param Advogado $advogado
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Advogado $advogado)
    {

        //dd($request->all());

        //-------------- Validacoes ----------------------------//

        if($request->get('email') != null){

            $uniqueEmailAdv = DB::table('advogado')->where('email', $request->get('email'))->get();

            $uniqueEmailUser = DB::table('users')->where('email', $request->get('email'))->get();

            //dd(count($unique));

            if (count($uniqueEmailAdv) >= 1) {

                return redirect()->back()->withInput()->with('alert-danger', 'O email informado já está sendo utilizado em nosso sistema!');
            }

            if (count($uniqueEmailUser) >= 1) {

                return redirect()->back()->withInput()->with('alert-danger', 'O email informado já está sendo utilizado em nosso sistema!');
            }

        }

        if($request->get('cpf') != null){

            $unique = DB::table('advogado')->where('cpf', $request->get('cpf'))->get();

            //dd(count($unique));

            if (count($unique) >= 1) {

                return redirect()->back()->withInput()->with('alert-danger', 'CPF já cadastrado no sistema');
            }

        }

        $internoValidacao = $request->get('internos');
        if(!isset($internoValidacao))
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Informe o interno cliente deste advogado!');
        }

        //------------- !! Validacoes --------------------------//

        $dataForm = $request->except('_token','interno','internos','imagens','telefone','inputFile','imagens','tipoFoto','legenda','tabelaFoto','inputFileDoc','nomeDoc');

        $dataForm['rg_data'] = Tdata::TdataMysql($dataForm['rg_data']);

        // se os campos de datas estivivem vazios, para não retorna erro no bd, assumem o valor null
        if ($dataForm['rg_data'] == "") {
            $dataForm['rg_data'] = (NULL);
        }

        $dataForm['status'] = "A";

        $idAdvogado = $advogado->create($dataForm)->id;

        if($idAdvogado)
        {
            LogController::log($idAdvogado,"advogado","add",Auth::user()->cpf,"Criação do cadastro");
        }

        // -------------------- inserir os internos --------------------------- //

        $internos = $request->input('internos');

//        dd($internos);

        if ($internos[0] != null) {

            foreach ($internos as $interno) {

                $insertRelacao = DB::table('advogado_interno')->insert([
                        'fk_interno' => $interno,
                        'fk_advogado' => $idAdvogado,
                        'status' => 'A',
                        'created_at' => date("Y-m-d H:i:s"),
                    ]
                );

            }
        }

        // -------------------- !! inserir os internos --------------------------- //

        // -------------------- inserir os telefones --------------------------- //

        $fone = $request->input('telefone');

        if ($fone[0] != null) {

            foreach ($fone as $f) {

                $insertFone = DB::table('contato')->insert([
                        'fk_id' => $idAdvogado,
                        'fk_tabela' => "advogado",
                        'numero' => $f,
                        'created_at' => date("Y-m-d H:i:s"),
                    ]
                );

            }
        }

        // -------------------- !! inserir os telefones --------------------------- //

        // -------------------- inserir as fotos --------------------------- //

        $imagens = $request->get('imagens');

        if(isset($imagens)){

            foreach($imagens as $images){

                list($baseType, $image) = explode(';', $images);
                list(, $image) = explode(',', $image);
                $image = base64_decode($image);
                $imageName = $idAdvogado . "_advogado_" . time() . "_" . '.'.'jpg';
                $p = Storage::disk('local')->put(Constantes::ARVQUIVOS_ADVOGADO.'/fotos/'.$idAdvogado.'/'. $imageName, $image, 'public');

                $insertFoto = DB::table('foto')->insert(
                    ['fk_id' => $idAdvogado,
                        'fk_tabela' => "advogado",
                        'nome_arquivo' => $imageName,
                        'tipo' => 'outros',
                        'created_at' => date("Y-m-d H:i:s")
                    ]
                );

                $idFoto = DB::getPdo()->lastInsertId();

                LogController::log($idFoto,"foto","add", Auth::user()->cpf, "Inclusão de foto do advogado<br>Arquivo: <i><strong>". $imageName ."</i></strong><br>Tipo: <strong><i>outros</i></strong>");

            }

        }

        // se enviar o arquivo
        if($request->file('inputFile')[0] != null) {

            $qtdFoto = count($request->file('inputFile'));
            $tipoFoto = $request->get('tipoFoto');
            $legenda = $request->get('legenda');


            for ($x = 0; $x < $qtdFoto; $x++) {

                $destinationPath = Constantes::ARVQUIVOS_ADVOGADO . "/fotos/" . $idAdvogado;


                if($tipoFoto[$x] == null)
                {
                    $tipofoto = "outros";
                }else{
                    $tipofoto = $tipoFoto[$x];
                }

                if($tipoFoto[$x] == "principal")
                {
                    $name = $idAdvogado .".jpg";

                }else {

                    $name = $idAdvogado . "_" . time() . "_" . $request->file('inputFile')[$x]->getClientOriginalName();
                }

                $request->file('inputFile')[$x]->storeAs($destinationPath, $name);
                //Storage::put($name, $destinationPath);


                $insertFoto = DB::table('foto')->insert(
                    ['fk_id' => $idAdvogado,
                        'fk_tabela' => "advogado",
                        'nome_arquivo' => $name,
                        'legenda' => $legenda[$x],
                        'tipo' => $tipofoto,
                        'created_at' => date("Y-m-d H:i:s")
                    ]
                );

                $idFoto = DB::getPdo()->lastInsertId();

                LogController::log($idFoto,"foto","add", Auth::user()->cpf, "Inclusão de foto do advogado<br>Arquivo: <i><strong>". $name ."</strong></i> <br>Legenda: <strong><i>".$legenda[$x]."</i></strong><br>Tipo: <strong><i>".$tipoFoto[$x]."</i></strong>");

            }
        }


        // -------------------- !! inserir as fotos --------------------------- //

        // -------------------- inserir os Documentos --------------------------- //

        if($request->file('inputFileDoc')[0] != null) {

            $qtdDoc = count($request->file('inputFileDoc'));
            $legendaDoc = $request->get('nomeDoc');

            for ($x = 0; $x < $qtdDoc; $x++) {

                $destinationPath = Constantes::ARVQUIVOS_ADVOGADO . "/documentos/" . $idAdvogado;

                $name = $idAdvogado . "_" . time() . "_" . $request->file('inputFileDoc')[$x]->getClientOriginalName();

                $request->file('inputFileDoc')[$x]->storeAs($destinationPath, $name);
                //Storage::put($name, $destinationPath);

                $insertDoc = DB::table('documento')->insert(
                    ['fk_id' => $idAdvogado,
                        'fk_tabela' => "advogado",
                        'nome_arquivo' => $name,
                        'descricao' => $legendaDoc[$x],
                        'created_at' => date("Y-m-d H:i:s")
                    ]
                );

                $idDoc = DB::getPdo()->lastInsertId();

                LogController::log($idDoc,"documento","add", Auth::user()->cpf, "Inclusão de documento do advogado<br>Arquivo: <i><strong>". $name ."</strong></i>");

            }
        }


        // -------------------- !! inserir os Fotos --------------------------- //

        // -------------------- cadastro na tb user e envio de senha --------------------------- //

        // -------- DESABILITADO O CADASTRO DE UM USUÁRIO ADVOGADO ----------
        /*$senha = str_random(8);

        $senhaDB = Hash::make($senha);

        $insertUser = DB::table('users')->insert([
            'name' => $dataForm['nome'],
            'cpf' => $dataForm['cpf'],
            'email' => $dataForm['email'],
            'password' => $senhaDB,
            'tipo' => "oab",
            'status' => "A",
            'visualiza_outras_up' => "N",
            'created_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        $enviado = Mail::to($dataForm['email'])->send(new SendMailCadastroAdv($dataForm['nome'],$dataForm['cpf'], $dataForm['email'],$senha,"[".config('app.secretaria')." ".config('app.uf')."] Envio de senha para Conta no ".config('app.name')));*/


        // -------------------- !cadastro na tb user e envio de senha -------------------------- //


        if ($idAdvogado != "" || $idAdvogado != "null") {

            return redirect()->route('advogado.buscar')->with('alert-success', 'O cadastro do advogado foi realizado com sucesso!');

        }else{

            return redirect('advogado.cadastro')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }

    public function buscar(Request $request)
    {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        /**
         * se não realizar uma busca
         * - Evita que mostre todos os resultados sem realizar uma busca
         */
        if(!strpos($url,"?"))
        {

            $dados = false;

        }else {

            $advogado = DB::table('advogado');

//            $advogado->leftJoin('foto',function($query){
//                $query->on('foto.fk_id','=','advogado.id')->where('foto.fk_tabela','=', 'advogado')->where('foto.tipo','principal');
//            });

            $nome = $request->get('nome');
            $sexo = $request->get('sexo');
            $oabNumero = $request->get('oab_numero');

            if ($nome != null) {
                $advogado->where('advogado.nome', 'LIKE', '%' . $nome . '%');
            }


            if ($sexo != null) {
                foreach($sexo as $s)
                {
                    $advogado->where('advogado.sexo', $s);

                }
            }

            if ($oabNumero != null) {
                $advogado->where('advogado.oab_numero', 'LIKE', '%' . $oabNumero . '%');
            }
            

            // passa a query para a paginacao "->appends..."
            $dados = $advogado->select('advogado.nome',"advogado.id","advogado.cpf","advogado.oab_numero")->orderBy('advogado.nome','ASC')->paginate(50)->appends($request->input());

        }

        return view('advogado.buscar.buscar', compact('dados'));

    }

    public function editar(Advogado $advogado, $id)
    {
        $advogado = $advogado->find($id);

        $advogado->rg_data = Tdata::TdataBR($advogado->rg_data);

        $relacaoInterno = DB::table('advogado_interno')->where('fk_advogado',$id)->get();

        //dd($relacaoInterno);

        $fotos = DB::table('foto')->where('fk_id',$id)->where('fk_tabela','advogado')->get();

        $telefone = DB::table('contato')->where('fk_id',$id)->where('fk_tabela','advogado')->get();

        $dadosInterno = array();

        foreach($relacaoInterno as $relacao)
        {
            $dadosInternos = (InternoController::getDadosPrincipaisInterno($relacao->fk_interno));

            array_push($dadosInterno, array(
                "nome" => $dadosInternos[0]['nome'],
                "id" => $dadosInternos[0]['id'],
                "faccao" => InternoController::getNomeFaccao($dadosInternos[0]['faccao']),
                "lideranca" => $dadosInternos[0]['lideranca'],
                "foto" => $dadosInternos[0]['foto'],
                "sigla" => $dadosInternos[0]['sigla']
            ));

        }

        $documentos = DB::table('documento')->where('fk_id',$id)->where('fk_tabela','advogado')->get();
        foreach($documentos as $documento)
        {
            $documento->created_at = Tdata::TtimestampBR($documento->created_at);
        }

        return view('advogado.cadastro.cadastro', compact('advogado','dadosInterno','telefone','fotos','documentos'));

    }

    public function update(Request $request, Advogado $advogado, $id)
    {

        //dd($request->input());

        $diferencas = array();
        $cpf = $request->get('cpf');

        if($cpf != null){
            $unique = DB::table('advogado')->where('cpf', $cpf)->get();
            $advogadoId = DB::table('advogado')->where('id', $id)->get()->first();

            if (count($unique) >= 1 && $cpf != $advogadoId->cpf) {
                return redirect()->back()->withInput()->with('alert-danger', 'CPF já cadastrado no sistema');
            }

        }

        $dataForm = $request->except('_token','interno','fotos', 'internos','telefone','inputFile','imagens','idPessoa','tipoFoto','legenda','tabelaFoto','inputFileDoc','nomeDoc','tipoFotoRegistrada','TelefoneContato');

        $dataForm['rg_data'] = Tdata::TdataMysql($dataForm['rg_data']);

        // se os campos de datas estivivem vazios, para não retorna erro no bd, assumem o valor null
        if ($dataForm['rg_data'] == "") {
            $dataForm['rg_data'] = (NULL);
        }


        // ---------------------- inserir interno -----------------------------

        $internos = $request->input('internos');
        if($internos != null) {
            foreach ($internos as $interno) {
                if (is_numeric($interno)) {
                    $nomeInterno = InternoController::getDadosPrincipaisInterno($interno);

                    $visitaRelacionamento = DB::table('advogado_interno')->where('fk_interno', $interno)->where('fk_advogado', $id)->get();

                    // se for um novo registro
                    if (empty(json_decode($visitaRelacionamento))) {
                        $insertRelacao = DB::table('advogado_interno')->insert([
                                'fk_interno' => $interno,
                                'fk_advogado' => $id,
                                'status' => 'A',
                                'created_at' => date("Y-m-d H:i:s"),
                            ]
                        );

                        // LOG
                        $alteracoes = "Inserção de vinculo do advogado com o interno (<i><strong>" . $nomeInterno[0]['nome'] . "</i></strong>).<br>";
                        array_push($diferencas, $alteracoes);

                    }
                }
            }
        }

        // ---------------------- ! inserir interno -----------------------------

        //-------------- Fone ----------------------------//

        $fonesRemovidos = $request->get('TelefoneContato');

        if($fonesRemovidos != null || !empty($fonesRemovidos))
        {
            foreach($fonesRemovidos as $fone)
            {

                $verificaFoneDel = DB::table('contato')->where('id', $fone)->get();

                // LOG
                $alteracoes = "Exclusão de dados do campo telefone: <i><strong>".$verificaFoneDel[0]->numero."</i></strong><br>";
                array_push($diferencas, $alteracoes);

                $deleteFone = DB::table('contato')->where('id', $fone)->delete();

            }

        }

        $fonePost = $request->get('telefone');

        if($fonePost[0] != null) {
            foreach ($fonePost as $fp) {

                $verificaFone = Contato::where('numero', $fp)->select('numero')->where('fk_id', $id)->get();

                if (empty(json_decode($verificaFone, true))) {

                    $insertFone = DB::table('contato')->insert([
                            'fk_id' => $id,
                            'fk_tabela' => "advogado",
                            'numero' => $fp,
                            'created_at' => date("Y-m-d H:i:s"),
                        ]
                    );

                    if($insertFone)
                    {
                        // LOG
                        $alteracoes = "Inserção de dados do campo telefone: <i><strong>".$fp."</i></strong><br>";
                        array_push($diferencas, $alteracoes);

                    }

                }

            }
        }


        //-------------- !! Fone -------------------------//


        // -------------------- inserir as fotos --------------------------- //

        $imagens = $request->get('imagens');

        if(isset($imagens)){

            foreach($imagens as $images){

                list($baseType, $image) = explode(';', $images);
                list(, $image) = explode(',', $image);
                $image = base64_decode($image);
                $imageName = $id . "_advogado_" . time() . "_" . '.'.'jpg';
                $p = Storage::disk('local')->put(Constantes::ARVQUIVOS_ADVOGADO.'/fotos/'.$id.'/'. $imageName, $image, 'public');

                $insertFoto = DB::table('foto')->insert(
                    ['fk_id' => $id,
                        'fk_tabela' => "advogado",
                        'nome_arquivo' => $imageName,
                        'tipo' => 'outros',
                        'created_at' => date("Y-m-d H:i:s")
                    ]
                );

                if($insertFoto)
                {
                    // LOG
                    $alteracoes = "Inserção de foto <i><strong> arquivo: ".$imageName."  / tipo: outros</i></strong><br>";
                    array_push($diferencas, $alteracoes);

                }

            }

        }

        // se enviar o arquivo
        if($request->file('inputFile')[0] != null) {

            $qtdFoto = count($request->file('inputFile'));
            $tipoFoto = $request->get('tipoFoto');
            $legenda = $request->get('legenda');

            for ($x = 0; $x < $qtdFoto; $x++) {


                if($tipoFoto[$x] == null)
                {
                    $tipofoto = "outros";
                }else{
                    $tipofoto = $tipoFoto[$x];
                }

                if($tipofoto == "principal")
                {
                    $name = $id.".jpg";

                    $selectFoto = DB::table('foto')->where('fk_id', $id)->where('fk_tabela', 'advogado')->where('tipo','principal')->get();

                    if(count(json_decode($selectFoto,true)) > 0) {

                        $renameOld = time() .$selectFoto[0]->nome_arquivo;
                        Storage::move("arquivos/advogados/fotos/" . $id . "/" . $selectFoto[0]->nome_arquivo, "arquivos/advogados/fotos/" . $id . "/" . $renameOld);

                        $updateTipo = DB::table('foto')->where('id', $selectFoto[0]->id)->update([
                            'tipo' => 'outros',
                            'nome_arquivo' => $renameOld,
                        ]);

                    }

                }else{

                    $name = $id . "__".$x."__" . time() . "_" . $request->file('inputFile')[$x]->getClientOriginalName();

                }

                $destinationPath = Constantes::ARVQUIVOS_ADVOGADO . "/fotos/" . $id;


                $request->file('inputFile')[$x]->storeAs($destinationPath, $name);
                //Storage::put($name, $destinationPath);


                $insertFoto = DB::table('foto')->insert(
                    ['fk_id' => $id,
                        'fk_tabela' => "advogado",
                        'nome_arquivo' => $name,
                        'legenda' => $legenda[$x],
                        'tipo' => $tipofoto,
                        'created_at' => date("Y-m-d H:i:s")
                    ]
                );


                if($insertFoto)
                {
                    // LOG
                    $alteracoes = "Inserção de foto <i><strong> arquivo: ".$name." / legenda: ".$legenda[$x]." / tipo: ".$tipoFoto[$x]."</i></strong><br>";
                    array_push($diferencas, $alteracoes);

                }


            }
        }

        // -------------------- !! inserir as fotos --------------------------- //

        // -------------------- inserir os Documentos --------------------------- //

        if($request->file('inputFileDoc')[0] != null) {

            $qtdDoc = count($request->file('inputFileDoc'));
            $legendaDoc = $request->get('nomeDoc');

            for ($x = 0; $x < $qtdDoc; $x++) {

                if($legendaDoc[$x] == null)
                {
                    return redirect()->back()->with('alert-danger', 'Informe o nome do documento');

                }

                $destinationPath = Constantes::ARVQUIVOS_ADVOGADO . "/documentos/" . $id;

                $name = $id . "_" . time() . "_" . $request->file('inputFileDoc')[$x]->getClientOriginalName();

                $request->file('inputFileDoc')[$x]->storeAs($destinationPath, $name);
                //Storage::put($name, $destinationPath);

                $insertDoc = DB::table('documento')->insert(
                    ['fk_id' => $id,
                        'fk_tabela' => "advogado",
                        'nome_arquivo' => $name,
                        'descricao' => $legendaDoc[$x],
                        'created_at' => date("Y-m-d H:i:s")
                    ]
                );

                $idDoc = DB::getPdo()->lastInsertId();

                LogController::log($idDoc,"documento","add", Auth::user()->cpf, "Inclusão de documento do advogado<br>Arquivo: <i><strong>". $name ."</strong></i>");


                if($insertDoc)
                {
                    // LOG
                    $alteracoes = "Inserção de documento <i><strong> arquivo: ".$name." / legenda: ".$legendaDoc[$x]."</i></strong><br>";
                    array_push($diferencas, $alteracoes);

                }

            }
        }


        // -------------------- !! inserir os Documentos --------------------------- //


        //-------------------- LOG -------------------------------------

        $dadosBD = $advogado->where('id', $id)->get();
        json_decode($dadosBD[0]);


        foreach ($dataForm as $k => $v) {
            if ($dataForm[$k] != json_decode($dadosBD[0], true)[$k]) {

                // se apagar um dado no form
                if (empty($dataForm[$k])) {

                    $alteracoes = "Exclusão de dados do campo " . ucfirst(str_replace("_", " ", $k)) . ": <i><strong>" . $dataForm[$k] . "</i></strong><br>";
                    array_push($diferencas, $alteracoes);

                }

                // se inserir um dado no form
                if(empty(json_decode($dadosBD[0], true)[$k]))
                {
                    $alteracoes = "Inclusão de dados do campo " . ucfirst(str_replace("_", " ", $k)) . ": <i><strong>" . $dataForm[$k] . "</i></strong><br>";
                    array_push($diferencas, $alteracoes);

                }
                // se alterar um dado no form
                else {


                    if($k == "email")
                    {
                        DB::table('users')->where('cpf', $dadosBD[0]->cpf)->update(['email'=>$request->get('email')]);
                    }

                    $alteracoes = "Alteração de dados no campo " . ucfirst(str_replace("_", " ", $k)) . ": <i><strong>" . $dataForm[$k] . "</i></strong><br>";
                    array_push($diferencas, $alteracoes);
                }
            }

        }

        //-------------------- LOG -------------------------------------


        //-------------- Faz o update no BD ----------------------------//

        $update = $advogado->find($id)->update($dataForm);

        //-------------- !! Faz o update no BD -------------------------//

        if ($update) {

            if(!empty($diferencas))
            {
                $textoLog = implode(" ", $diferencas);
                LogController::log($id,"advogado","update", Auth::user()->cpf, $textoLog);
            }

            return redirect()->route('advogado.buscar')->with('alert-success', 'O cadastro do advogado foi alterado com sucesso!');

        }else{

            return redirect('advogado.cadastro')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }

    public function inicioAtendimento()
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dataInicioPlantao = OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor);
        $horaInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor);
        $horaTerminoPlantao = OcorrenciaController::getHorarioReferenciaProxFechamentoLivroUnidade($lotacaoServidor);

        $dataPlantao = $dataInicioPlantao;

        $timestampHoraProximoFechamento = OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor)." ".OcorrenciaController::getHorarioReferenciaProxFechamentoLivroUnidade($lotacaoServidor);
        $date = new DateTime($timestampHoraProximoFechamento);
        $date->sub(new \DateInterval(('PT1S')));


        $ocorrenciaHoraInicioPlantao = $horaInicioPlantao;
        $ocorrenciaDataInicioPlantao = $dataInicioPlantao." ".$ocorrenciaHoraInicioPlantao;
        $ocorrenciaHoraTerminoPlantao = Tdata::getTempoTimestamp($date->format('Y-m-d H:i:s'));
        $ocorrenciaDataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor)." ".$ocorrenciaHoraTerminoPlantao;

        $advogados = DB::table('controle_acesso')
            ->leftJoin('advogado','advogado.cpf','=','controle_acesso.cpf')
            ->where('controle_acesso.tabela','advogado')
            ->where('controle_acesso.unidade',$lotacaoServidor)
//            ->where('controle_acesso.data', $dataPlantao)
//            ->where('controle_acesso.horario', '<=' ,$horaTerminoPlantao)
                
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '>=', $ocorrenciaDataInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data, horario)'), '<=', $ocorrenciaDataTerminoPlantao)

            ->select('advogado.*','advogado.id as advId','controle_acesso.*')
            ->get();

        
        $dadosAdvs = DB::table('advogado_atendimento')->where('unidade', $lotacaoServidor)
            ->where(DB::raw('CONCAT_WS(" ", data_atendimento, horaInicio)'), '>=', $ocorrenciaDataInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data_atendimento, horaInicio)'), '<=', $ocorrenciaDataTerminoPlantao)
            ->get();

        $advogadosLivro = array();
        //dd($dadosAdvs);
        foreach($dadosAdvs as $adv)
        {
            $dadosAdv = AdvogadoController::getDadosAdv($adv->fk_advogado);
            array_push($advogadosLivro, array(
                'id' => $adv->id,
                'nomeAdvogado' => $dadosAdv[0]->nome,
                'oab' =>  AdvogadoController::getOabAdv($adv->fk_advogado),
                'nomeInterno' => InternoController::getInternoNome($adv->fk_interno)." ".InternoController::getLotacaoInterno($adv->fk_interno),
                'inicioAtendimento' => $adv->horaInicio,
                'terminoAtendimento' => $adv->horaTermino,
                'duracao' => $adv->duracao,
            ));

        }

        //$advogadosLivro = collect($advogados)->sortBy('inicioAtendimento');

        $dataPlantaoAberto = Tdata::TdataBR($dataPlantao)." de ".$horaInicioPlantao." até ".Tdata::TdataBR($dataTerminoPlantao)." às ".$horaTerminoPlantao;


        return view('advogado.atendimento.inicioAtendimento', compact('advogados','dataPlantaoAberto', 'advogadosLivro'));
    }

    public function inicioAtendimentoStore(Request $request)
    {
       //dd($request->all());

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $advogado = $request->get('advogado');
        $dataAtendimento = $request->get('dataAtendimento');
        $horaInicio = $request->get('horaInicio');
        $horaTermino = $request->get('horaTermino');
        $internos = $request->get('checkInterno');

        if(is_null($internos))
        {
            return redirect()->back()->with('alert-danger', 'Selecione o interno que foi atendido!');
        }
        $duracao = ( strtotime($horaTermino) - strtotime($horaInicio) ) / 60;

        foreach($internos as $interno)
        {

            $insertAtendimento = DB::table('advogado_atendimento')->insert([
                'unidade'=> $lotacaoServidor,
                'fk_advogado'=>$advogado,
                'fk_interno'=>$interno,
                'data_atendimento'=> Tdata::TdataMysql($dataAtendimento),
                'horaInicio'=>$horaInicio,
                'horaTermino'=>$horaTermino,
                'duracao'=>$duracao,
                'created_at'=> Tdata::TdataHojeMysql(),
            ]);

            OabController::getVerificaAtendimento($advogado, $interno, Tdata::TdataMysql($dataAtendimento), $lotacaoServidor);

        }

        if ($insertAtendimento) {

            return redirect()->route('advogado.atendimentoHistorico')->with('alert-success', 'Atendimento registrado com sucesso!');

        }

    }

    public function inicioAtendimentoUpdate($id){

        $atendimento = DB::table('advogado_atendimento')->where('id', $id)->select('advogado_atendimento.*', DB::raw("DATE_FORMAT(advogado_atendimento.data_atendimento,'%d/%m/%Y') AS data_atendimento"))->first();
        $interno = InternoController::getDadosInterno($atendimento->fk_interno)[0];
        $advogado = self::getDadosAdv($atendimento->fk_advogado)[0];
        $horaEntrada = $atendimento->horaInicio;
        $horaSaida = $atendimento->horaTermino;

        // ---------------- validador para evitar alteração de outros atendimentos via link

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $getFechamento = DB::table('ocorrencia_fechamento_livro')->where('unidade', $lotacaoServidor)->orderBy('data', 'DESC')->first();

        $dataInicioPlantao = OcorrenciaController::getDataUltimoFechamentoLivro($lotacaoServidor);
        $dataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor);

        $ocorrenciaHoraInicioPlantao = OcorrenciaController::getHorarioReferenciaUltimoFechamentoLivro($lotacaoServidor)."00";
        $ocorrenciaDataInicioPlantao = $dataInicioPlantao." ".$ocorrenciaHoraInicioPlantao;
        $ocorrenciaHoraTerminoPlantao = OcorrenciaController::getHorarioFechamento59Seg($lotacaoServidor);;
        $ocorrenciaDataTerminoPlantao = OcorrenciaController::getDataProxFechamentoLivroUnidade($lotacaoServidor)." ".$ocorrenciaHoraTerminoPlantao;

        $dadosAdvs = DB::table('advogado_atendimento')->where('unidade', $lotacaoServidor)
            ->where(DB::raw('CONCAT_WS(" ", data_atendimento, horaInicio)'), '>=', $ocorrenciaDataInicioPlantao)
            ->where(DB::raw('CONCAT_WS(" ", data_atendimento, horaInicio)'), '<=', $ocorrenciaDataTerminoPlantao)
            ->where('id', $id)
            ->select('advogado_atendimento.*', DB::raw("DATE_FORMAT(advogado_atendimento.data_atendimento,'%d/%m/%Y') AS data_atendimento"))
            ->get();


        if(count($dadosAdvs) <= 0){
            return redirect()->route('advogado.inicioAtendimento')->with('alert-danger', 'Operação de alteração não permitida!');
        }

        // ---------------- FIM !!! validador para evitar alteração de outros atendimentos via link


        return view('advogado.atendimento.editar', compact('interno', 'atendimento', 'advogado', 'horaEntrada', 'horaSaida'));
    }

    public function updateAtendimento(Request $request, $id){
        $horaInicio = $request->get('horaInicio');
        $horaTermino = $request->get('horaTermino');
        $duracao = ( strtotime($horaTermino) - strtotime($horaInicio) ) / 60;

        if($duracao < 0){
            return redirect()->back()->with('alert-danger', 'A hora de inicio não pode ser maior que a hora de termino!');
        }
        if($duracao == 0){
            return redirect()->back()->with('alert-danger', 'A hora de inicio não pode ser igual a hora de termino!');
        }

        DB::table('advogado_atendimento')->where('id', $id)->update(['horaInicio'=> $horaInicio]);
        DB::table('advogado_atendimento')->where('id', $id)->update(['horaTermino'=> $horaTermino]);
        DB::table('advogado_atendimento')->where('id', $id)->update(['duracao'=> $duracao]);

        LogController::log($id,"atendimento","update", Auth::user()->cpf, "Update no atendimento de advogado<br>Atendimento: <i><strong>". $id ."</strong></i> <br>HoraInicio: <strong><i>".$horaInicio."</i></strong><br>HoraTermino: <strong><i>".$horaTermino."</i></strong>");

        return redirect()->route('advogado.inicioAtendimento')->with('alert-success', 'Aletração realizada com sucesso!');

    }

    public function atendimentoHistorico(Request $request)
    {

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $unidades = DB::table('unidade')->where('status', '1')->orderBy('sigla', 'ASC')->get();

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if(!strpos($url,"?")) {


            $historico = DB::table('advogado_atendimento')
                ->leftJoin('advogado','advogado.id','=','advogado_atendimento.fk_advogado')
                ->leftJoin('interno','interno.id','=','advogado_atendimento.fk_interno')
                ->leftJoin('unidade','unidade.id','=','advogado_atendimento.unidade')
                ->where('advogado_atendimento.unidade', $lotacaoServidor)
                ->select('interno.*','interno.nome as internoNome','interno.id as internoId','advogado_atendimento.*',DB::raw("DATE_FORMAT(advogado_atendimento.data_atendimento,'%d/%m/%Y') as data_atendimento"),'advogado.*','advogado.nome as advNome','advogado.id as advId','unidade.*')
                ->orderBy('advogado_atendimento.data_atendimento', 'DESC')
                ->orderBy('advogado_atendimento.horaTermino', 'DESC')
                ->paginate(50)->appends($request->input());


        }
        // se realizar uma busca
        else{

            //dd($request->all());

            $unidade = $request->get('presidio');
            $cpf = $request->get('cpf');
            $dataInicial = $request->get('dataInicial');
            $dataFinal = $request->get('dataFinal');

            $dadosAtendimentos = DB::table('advogado_atendimento');
            $dadosAtendimentos->leftJoin('advogado','advogado.id','=','advogado_atendimento.fk_advogado');
            $dadosAtendimentos->leftJoin('interno','interno.id','=','advogado_atendimento.fk_interno');
            $dadosAtendimentos->leftJoin('unidade','unidade.id','=','advogado_atendimento.unidade');


            if($unidade != null){
                $dadosAtendimentos->where('advogado_atendimento.unidade', $unidade);
            }else{
                $dadosAtendimentos->where('advogado_atendimento.unidade', $lotacaoServidor);
            }
            if($dataInicial != null){
                $dadosAtendimentos->where('advogado_atendimento.data_atendimento','>=', Tdata::TdataMysql($dataInicial));
            }
            if($dataFinal != null){
                $dadosAtendimentos->where('advogado_atendimento.data_atendimento','<=', Tdata::TdataMysql($dataFinal));
            }
            if($cpf != null) {
                $dadosAdv = self::getDadosAdvCpf($cpf);
                if(!empty(json_decode($dadosAdv,true))) {
                    $dadosAtendimentos->where('advogado_atendimento.fk_advogado', $dadosAdv[0]->id);
                }
            }


            $dadosAtendimentos->select('interno.*','interno.nome as internoNome','interno.id as internoId','advogado_atendimento.*',DB::raw("DATE_FORMAT(advogado_atendimento.data_atendimento,'%d/%m/%Y') as data_atendimento"),'advogado.*','advogado.nome as advNome','advogado.id as advId','unidade.*');
            $historico = $dadosAtendimentos->orderBy('advogado_atendimento.horaTermino', 'DESC')->orderBy('advogado_atendimento.data_atendimento', 'DESC')->paginate(50)->appends($request->input());;

        }

        return view('advogado.atendimento.historico', compact('historico','unidades'));

    }

    public function documentoDelete($id, $filename)
    {

        if (file_exists(storage_path('app/arquivos/advogados/documentos/' . $id . "/" . $filename))) {

            $move = Storage::move(Constantes::ARVQUIVOS_ADVOGADO . "/documentos/" . $id . "/" . $filename, Constantes::ARQUIVO_MORTO . "/" . $filename);

            $delete = DB::table('documento')->where('fk_id', $id)->where('fk_tabela', 'advogado')->where('nome_arquivo', $filename)->delete();

        }else{

            $delete = DB::table('documento')->where('fk_id', $id)->where('fk_tabela', 'advogado')->where('nome_arquivo', $filename)->delete();

            return "errorMove";
        }

        if ($delete) {

            LogController::log($id,"documento","delete", Auth::user()->cpf, "Exclusão de documento do Advogado<br>Arquivo: <i><strong>". $filename ."</strong></i>");

            return redirect()->back()->with('alert-success', 'Documento deletado com sucesso!');

        }else{

            return redirect()->back()->with('alert-danger', 'Ocorreu um problema ao deletar o documento, por favor, tente novamente!');

        }


    }


    public function show($id)
    {
        $advogado = DB::table('advogado')->where('id', $id)->select('advogado.*',DB::raw('(CASE WHEN advogado.status= "A" THEN "ATIVO" ELSE "INATIVO" END) AS status'))->get();
        $advogado = json_decode($advogado, true)[0];


        $advogado['presos_visitados'] = DB::table('advogado_interno')->where('advogado_interno.fk_advogado', $id)->get();
        if ($advogado['presos_visitados'] != collect([])){
            $advogado['presos_visitados'] = json_decode($advogado['presos_visitados'], true);
        }
        foreach ($advogado['presos_visitados'] as $key => $presoVisitado) {
            $advogado['presos_visitados'][$key]['dadosInterno'] = InternoController::getDadosPrincipaisInterno($presoVisitado['fk_interno'])[0];
            // foto do preso
            if (is_null($advogado['presos_visitados'][$key]['dadosInterno']['foto'])) {
                $advogado['presos_visitados'][$key]['dadosInterno']['foto'] = 'storage/app/semfoto.jpg';
            } else {
                $advogado['presos_visitados'][$key]['dadosInterno']['foto'] = 'storage/app/arquivos/internos/fotos/' . $advogado['presos_visitados'][$key]['dadosInterno']['id'] . '/' . $advogado['presos_visitados'][$key]['dadosInterno']['foto'];
            }
            $fotopresotemp = DB::table('foto')->where('fk_tabela', 'interno')->where('fk_id', $advogado['presos_visitados'][$key]['dadosInterno']['id'])->where('tipo', 'principal')->get();
            if ($fotopresotemp != collect([])) {
                $fotopresotemp = json_decode($fotopresotemp, true);
                $advogado['presos_visitados'][$key]['dadosInterno']['fotolegenda'] = $fotopresotemp[0]['legenda'];
                $advogado['presos_visitados'][$key]['dadosInterno']['fototipo'] = $fotopresotemp[0]['tipo'];
                $advogado['presos_visitados'][$key]['dadosInterno']['fotoinserida'] = Tdata::TdataBR(Tdata::getDataTimestamp($fotopresotemp[0]['created_at']));
            }
        }
        $advogado['qtd_presos_visitados'] = count($advogado['presos_visitados']);
        if ($advogado['qtd_presos_visitados'] == 0) $advogado['qtd_presos_visitados'] = "0";


        $advogado['qtd_ocorrencias'] = DB::table('ocorrencia_envolvidos')->where('fk_tabela', 'advogado')->where('fk_id_envolvido', $id)->count();
        if($advogado['qtd_ocorrencias'] == 0) $advogado['qtd_ocorrencias'] = "0";


        $contatos = DB::table('contato')->where('fk_tabela', 'advogado')->where('fk_id', $id)->get();
        $advogado['contatos'] = [];
        if(!is_null($contatos)){
            foreach(json_decode($contatos,true) as $i){
                array_push($advogado['contatos'], $i['numero']);
            }
        }

        if(!is_null($advogado['rg_data'])) $advogado['rg_data'] = Tdata::TdataBR($advogado['rg_data']);
        if(!is_null($advogado['created_at'])) $advogado['created_at'] = "às ".Tdata::getTempoTimestamp($advogado['created_at'])." em ".Tdata::TdataBR(Tdata::getDataTimestamp($advogado['created_at']));
        else $advogado['created_at'] = "";
        if(!is_null($advogado['updated_at'])) $advogado['updated_at'] = "às ".Tdata::getTempoTimestamp($advogado['updated_at'])." em ".Tdata::TdataBR(Tdata::getDataTimestamp($advogado['updated_at']));
        else $advogado['updated_at'] = "";

        // -------------------------- FOTOS -----------------------------------------------------------------------------------

        $advogado['fotos'] = DB::table('foto')->where('fk_tabela', 'advogado')->where('fk_id', $id)->get();
        if ($advogado['fotos'] != collect([])){
            $advogado['fotos'] = json_decode($advogado['fotos'], true);
        }

        $fotoPerfilDados = DB::table('foto')->where('fk_tabela', 'advogado')->where('fk_id', $id)->where('tipo', 'principal')->get();
        if ($fotoPerfilDados != collect([])) {
            $fotoPerfilDados = json_decode($fotoPerfilDados, true)[0];
            $fotoPerfilDados['nome_arquivo'] = 'storage/app/arquivos/advogados/fotos/' . $advogado['id'] . '/' . $fotoPerfilDados['nome_arquivo'];
            $fotoPerfilDados['created_at'] = Tdata::TdataBR(Tdata::getDataTimestamp($fotoPerfilDados['created_at']));
            $fotoPerfilDados['updated_at'] = Tdata::TdataBR(Tdata::getDataTimestamp($fotoPerfilDados['updated_at']));
        } else {
            $fotoPerfilDados['nome_arquivo'] = 'storage/app/semfoto.jpg';
        }

        // ------------------------- DOCUMENTOS -----------------------------------------------------------------------------------

        $documentos = DB::table('documento')
            ->leftJoin('log', function ($query) {
                $query->on('log.fk_id', '=', 'documento.id')
                    ->where('log.tipo_alteracao',"add")
                    ->where('log.tabela_alterada',"documento");
            })
            ->where('documento.fk_id',$id)
            ->where('documento.fk_tabela','advogado')
            ->select("log.*", "log.created_at as logData","documento.*")
            ->get();

        foreach($documentos as $documento)
        {
            $documento->created_at = Tdata::TtimestampBR($documento->created_at);
            $documento->logData = Tdata::TtimestampBR($documento->logData);
            $documento->cpf_usuario = FacadeController::getNomeServidorCpf($documento->cpf_usuario)." (".$documento->cpf_usuario.")";
        }

        // ------------------------------------------------------------------------------------------------------------------------

        // ------------------------- ocorrencias -----------------------------------------------------------------------------------
        $dadosOcorrencias = DB::table('ocorrencia_envolvidos')
            ->leftJoin('ocorrencia','ocorrencia.id','=','ocorrencia_envolvidos.fk_ocorrencia')
            ->leftJoin('ocorrencia_natureza','ocorrencia_natureza.id','=','ocorrencia.natureza')
            ->where('ocorrencia_envolvidos.fk_tabela','advogado')
            ->where('ocorrencia.status','A')
            ->where('ocorrencia_envolvidos.fk_id_envolvido', $id)
            ->select('ocorrencia.*', 'ocorrencia.id as idOcorrencia','ocorrencia_envolvidos.*','ocorrencia_natureza.natureza as naturezaOcorrencia')
            ->orderBy('ocorrencia.data','DESC')
            ->get();

        $ocorrencias = array();
        foreach($dadosOcorrencias as $dadosOcorrencia)
        {
            $dadosServidores = DB::table('ocorrencia_envolvidos')->where('fk_tabela', 'servidor')->where('fk_ocorrencia', $dadosOcorrencia->idOcorrencia)->get();
            $dadosServidoresArray = array();
            if(count($dadosServidores) > 0)
            {
                foreach($dadosServidores as $dadosServidore) {

                    array_push($dadosServidoresArray, array(
                        'nomeAgente' => FacadeController::getNomeServidor($dadosServidore->fk_id_envolvido),
                        'matricula' => $dadosServidore->fk_id_envolvido

                    ));
                }

            }else{
                $dadosServidoresArray = null;
            }

            $dadosVisitantes = DB::table('ocorrencia_envolvidos')->where('fk_tabela', 'visita')->where('fk_ocorrencia', $dadosOcorrencia->idOcorrencia)->get();
            $dadosVisitanteArray = array();
            if(count($dadosVisitantes) > 0)
            {
                foreach($dadosVisitantes as $dadosVisitante) {

                    array_push($dadosVisitanteArray, array(
                        'nomeVisita' => VisitaController::getNomeVisita($dadosVisitante->fk_id_envolvido),
                        'cpfVisita' => VisitaController::getCpfVisita($dadosVisitante->fk_id_envolvido),

                    ));
                }

            }else{
                $dadosVisitanteArray = null;
            }

            $dadosInternos = DB::table('ocorrencia_envolvidos')->where('fk_tabela', 'interno')->where('fk_ocorrencia', $dadosOcorrencia->idOcorrencia)->get();
            $dadosInternoArray = array();
            if(count($dadosInternos) > 0)
            {
                foreach($dadosInternos as $dadosInterno) {

                    array_push($dadosInternoArray, array(
                        'nomeInterno' => InternoController::getInternoNome($dadosInterno->fk_id_envolvido),
                        'prontuario' => $dadosInterno->fk_id_envolvido,

                    ));
                }

            }else{
                $dadosInternoArray = null;
            }

            $dadosAdvs = DB::table('ocorrencia_envolvidos')->where('fk_tabela', 'advogado')->where('fk_ocorrencia', $dadosOcorrencia->idOcorrencia)->where('fk_id_envolvido', '!=', $id)->get();
            $dadosAdvsArray = array();
            if(count($dadosAdvs) > 0)
            {
                foreach($dadosAdvs as $dadosAdv) {

                    array_push($dadosAdvsArray, array(
                        'nomeAdv' => AdvogadoController::getNomeAdv($dadosAdv->fk_id_envolvido),
                        'oab' => AdvogadoController::getOabAdv($dadosAdv->fk_id_envolvido),

                    ));
                }

            }else{
                $dadosAdvsArray = null;
            }
            //$dadosAdvsArray = null;

            $dadosLog = DB::table('log')->where('tabela_alterada', 'ocorrencia')->where('tipo_alteracao', 'add')->where('fk_id', $dadosOcorrencia->idOcorrencia)->get();
            $logArray = array();
            foreach ($dadosLog as $log) {
                $nomeServidor = FacadeController::getNomeServidorCpf($log->cpf_usuario);

                array_push($logArray, array(
                    'data' => Tdata::TdataBR(Tdata::getDataTimestamp($log->created_at)),
                    'hora' => Tdata::getTempoTimestamp($log->created_at),
                    'tabela' => str_replace("_", " ", $log->tabela_alterada),
                    'descricao' => $log->texto,
                    'realizadoPor' => $nomeServidor . " (" . $log->cpf_usuario . ")",
                ));
            }



            array_push($ocorrencias, array(
                'unidade' => UnidadesController::getNomeUp($dadosOcorrencia->unidade),
                'naturezaOcorrencia' => $dadosOcorrencia->naturezaOcorrencia,
                'idOcorrencia' => $dadosOcorrencia->idOcorrencia,
                'data' => Tdata::TdataBR($dadosOcorrencia->data),
                'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($dadosOcorrencia->data)),
                'horario' => $dadosOcorrencia->horario,
                'servidoresEnvolvidos' => $dadosServidoresArray,
                'visitasEnvolvidas' => $dadosVisitanteArray,
                'internosEnvolvidos' => $dadosInternoArray,
                'advsEnvolvidos' => $dadosAdvsArray,
                'dadosLog' => $logArray,


            ));
        }
//        dd($ocorrencias);

        // ------------------------- !ocorrencias ----------------------------------------------------------------------------------

        // ------------------------- Atendimento advogado historico -----------------------------------------------------------------------------------

        $advogadoHistoricoDados = DB::table('advogado_atendimento')->where('fk_advogado', $id)->orderBy('data_atendimento', 'DESC')->orderBy('horaInicio', 'DESC')->limit(50)->get();

        $advogadoHistorico = array();

        foreach($advogadoHistoricoDados as $advogadoHistoricoDado)
        {
            array_push($advogadoHistorico, array(
                'unidade' => UnidadesController::getSiglaUp($advogadoHistoricoDado->unidade),
                'nomeInterno' => InternoController::getInternoNome($advogadoHistoricoDado->fk_interno),
                'fotoInterno' => asset(InternoController::getFotoInterno($advogadoHistoricoDado->fk_interno)),
                'data' => Tdata::TdataBR($advogadoHistoricoDado->data_atendimento),
                'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($advogadoHistoricoDado->data_atendimento)),
                'prontuario' => $advogadoHistoricoDado->fk_interno,
                'horaInicio' => $advogadoHistoricoDado->horaInicio,
                'horaTermino' => $advogadoHistoricoDado->horaTermino,
                'duracao' => $advogadoHistoricoDado->duracao."min.",
            ));

        }

//        dd($advogadoHistorico);

        // ------------------------- !Atendimento advogado historico ----------------------------------------------------------------------------------

        $i = 0;
            $keys = array_keys($advogado);
            foreach ($advogado as $item) {
                if (is_null($item) || $item == "") {
                    $advogado[$keys[$i]] = "Não Informado";
                }
                $i++;
            }

        return view('advogado.show.show', compact('advogado', 'fotoPerfilDados','documentos','ocorrencias','id','advogadoHistorico'));
    }

    public function cancelarAgendamentos()
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $lotacaoServidorSigla = FacadeController::getLotacaoServidor(Auth::user()->cpf);

        $unidades = DB::table('unidade')->where('sigla',$lotacaoServidorSigla)->orderBy('sigla', 'ASC')->get();


        $diasAtendimento = UnidadesController::getDiasAtendimentoAdv($lotacaoServidor);

        return view('advogado.cancelarAgendamentos.cancelarAgendamentos', compact('diasAtendimento','unidades'));
    }

    public function cancelarAgendamentosStore(Request $request)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $data = $request->get('dataAtendimento');
        $motivacao = $request->get('motivacao');
        $unidade = $request->get('unidade_prisional');
        $pavilhao = $request->get('pavilhao');
        $ala = $request->get('ala');
        $cela = $request->get('cela');


        $insert = DB::table('advogado_agendamento_data_cancelada')->insert([
            'unidade' => $lotacaoServidor,
            'data' => Tdata::TdataMysql($data),
            'motivacao' => $motivacao,
            'created_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        $idInsert = DB::getPdo()->lastInsertId();


        $dadosAdvs = array();

        $agendamentosData = DB::table("advogado_agendamento");
        $agendamentosData->leftJoin('interno', 'interno.id','=', 'advogado_agendamento.fk_interno');

        if(!is_null($unidade))
        {
            $agendamentosData->where('interno.unidade_prisional', $unidade);
        }else{
            $agendamentosData->where('interno.unidade_prisional', $lotacaoServidor);

        }
        if(!is_null($pavilhao))
        {
            $agendamentosData->where('interno.pavilhao', $pavilhao);
        }

        if(!is_null($ala))
        {
            $agendamentosData->where('interno.ala', $ala);
        }

        if(!is_null($cela))
        {
            $agendamentosData->where('interno.cela', $cela);
        }

        $agendamentosData->where('data', Tdata::TdataMysql($data));
        $agendamentosData->select('advogado_agendamento.*','interno.unidade_prisional','interno.pavilhao','interno.ala','interno.cela');
        $agendamentosData = $agendamentosData->get();


        $i = count($agendamentosData);
        foreach($agendamentosData as $agendamento)
        {
            DB::table('advogado_agendamento')->where('id', $agendamento->id)->update(['status'=> 'Cancelada']);
            $dadoAdv = self::getDadosAdv($agendamento->fk_advogado);

            array_push($dadosAdvs, array(
                'nome' => $dadoAdv[0]->nome,
                'email' => $dadoAdv[0]->email,
                'unidade' => UnidadesController::getNomeUp($lotacaoServidor),
                'interno' => InternoController::getInternoNome($agendamento->fk_interno),
                'data' => $data,
                'horario' => $agendamento->horario,
            ));
        }

        foreach($dadosAdvs as $adv) {

            $enviado = Mail::to($adv['email'])->send(new SendMailCancelamentoAgendamentoAdv($adv['email'], $adv['interno'], $adv['data'], $adv['horario'], $adv['unidade'], $motivacao, "Cancelamento de agendamento - Atendimento a Interno - ".config('app.secretaria').'/'.config('app.uf')));

            $i--;
        }

        if($i == 0){

            LogController::log($idInsert,"advogado_agendamento_data_cancelada","add",Auth::user()->cpf,"Cancelamento de agendamento pela unidade de Atendimento a Interno por advogado");

            return redirect()->route('inicio')->withInput()->with('alert-success', 'Agendamentos Cancelados com sucesso!');

        }

    }
    
    public function registrarFaltaAdvAgendamento($idAgendamento) {
    
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $updateAgendamento = DB::table('advogado_agendamento')->where('unidade', $lotacaoServidor)->where('id', $idAgendamento)->where('status','Agendado')->update(['status'=>'Falta']);
        
        if($updateAgendamento)
        {
            LogController::log($idAgendamento,"advogado_agendamento","update",Auth::user()->cpf,"Registro de falta no agendamento de Atendimento a Interno por advogado");

            return redirect()->back()->with('alert-success', 'Falta registrada com sucesso!');
        }else{
            
            return redirect()->back()->with('alert-danger', 'Erro ao registrar a falta no agendamento, informe a equipe de TI!');
        }

    }
    
    public function confirmarRealizacaoAdvAgendamento($idAgendamento) {
        
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $updateAgendamento = DB::table('advogado_agendamento')->where('unidade', $lotacaoServidor)->where('id', $idAgendamento)->where('status','Agendado')->update(['status'=>'Atendido']);
        
        if($updateAgendamento)
        {
            LogController::log($idAgendamento,"advogado_agendamento","update",Auth::user()->cpf,"Confirmação de realização Atendimento a Interno por advogado");

            return redirect()->back()->with('alert-success', 'Confirmação de realização de atendimento a interno por advogado registrado com sucesso!');
        }else{
            
            return redirect()->back()->with('alert-danger', 'Erro ao registrar a falta no agendamento, informe a equipe de TI!');
        }

    }
    
}
