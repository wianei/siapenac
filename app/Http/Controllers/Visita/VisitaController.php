<?php


/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a visita
|
*/

namespace App\Http\Controllers\Visita;

use App\Http\Controllers\Advogado\AdvogadoController;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Interno\InternoController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Permissoes\PermissoesController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Interno;
use App\Visita;
use App\Contato;
use App\Endereco;
use App\Classes\Constantes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Classes\GeocodingMaps;
use App\Classes\Tdata;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Milon\Barcode\DNS1D;

class VisitaController extends Controller
{
    private $visita;

    public function __construct(Visita $visita)
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->$visita = $visita;
        $this->middleware('auth');
    }

    public function callAction($method, $parameters)
    {

        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::verifyPermission("VisitaController", $method);
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

        
//        $metodosAdm = collect(['cadastro','store','editar','update','documentoDelete','suspenderVisita','permitirVisita']);
//        $metodosComum = collect(['confereVisita','confereVisitaStore']);
//        $metodosExterno = collect(['buscar','show']);
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
//        if($metodosExterno->contains($method))
//        {
//            if (Auth::user()->tipo < 100) {
//
//                return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//            }
//
//        }
//


        return parent::callAction($method, $parameters);
    }

    public static function base64_to_jpeg($base64_string, $output_file) {
        // open the output file for writing
        $ifp = fopen( $output_file, 'wb' );

        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode( ',', $base64_string );

        // we could add validation here with ensuring count( $data ) > 1
        fwrite( $ifp, base64_decode( $data[ 1 ] ) );

        // clean up the file resource
        fclose( $ifp );

        return $output_file;
    }

    /*
    |--------------------------------------------------------------------------
    | Funcoes acessadas via ajax
    |--------------------------------------------------------------------------
    |
    */
        public function getFotoPrincipalVisitaAjax(Request $request)
    {
       
        
        $idVisita = $request->get('idInterno');

        
        $fotoDados = DB::table('foto')
                ->where('fk_tabela','visita')
                ->where('tipo', 'principal')
                ->where('fk_id', $idVisita)
                ->get();
        if(count($fotoDados) > 0)
        {
            if (is_dir(storage_path('app/arquivos/visitas/fotos/' . $idVisita . "/"))) {

                $dir = storage_path('app/arquivos/visitas/fotos/' . $idVisita . "/");

                $b = scandir($dir, 1);

                // se houver foto cadastrada
                if (file_exists(storage_path('app/arquivos/visitas/fotos/' . $idVisita . "/" . $fotoDados[0]->nome_arquivo))) {

                    $path = storage_path('app/arquivos/visitas/fotos/' . $idVisita . "/" . $fotoDados[0]->nome_arquivo);
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $foto = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    
                    
                }

                return $foto;

                } else {

                    $foto = null;
                }
                
        }
        else{
            $foto = null;
        }

    }


    public function buscaAjaxKeyUp(Request $request)
    {
        $url = url()->current();
        $url = explode('/',$url);
        $newUrl = $url[0]."//".$url[2]."/";

        $nome = $request->get('nome');

        $dados = DB::table('visita')
//            ->leftJoin('foto', function ($query) {
//                $query->on('foto.fk_id', '=', 'visita.id')->where('foto.fk_tabela', '=', 'visita')->where('visita.tipo', 'principal');
//            })
            ->select('visita.id','visita.nome','visita.cpf','visita.status')
            ->where('visita.nome','LIKE', "%".$nome."%")
            ->orderBy('visita.nome', 'ASC')
            ->get();


        return $dados;

    }
    public function getVisitasInternoAjax(Request $request)
    {
        $idInterno = $request->get('idVisita');
        $dataInicio = Tdata::TdataMysql($request->get('dataInicio'));
        $dataFinal = Tdata::TdataMysql($request->get('dataFinal'));

        $visitanteHistoricoDados = DB::table('controle_acesso_visita_interno')->where('fk_visita', $idInterno);

        if(!empty($dataInicio) && !empty($dataFinal) )
        {
            $visitanteHistoricoDados->where('data', '>=', $dataInicio);
            $visitanteHistoricoDados->where('data', '<=', $dataFinal);
        }

        if(!empty($dataInicio) && empty($dataFinal) )
        {
            $visitanteHistoricoDados->where('data', '>=', $dataInicio);
        }

        if(empty($dataInicio) && !empty($dataFinal) )
        {
            $visitanteHistoricoDados->where('data', '<=', $dataFinal);
        }

        $dadosVisitasInterno = $visitanteHistoricoDados->orderBy('data', 'DESC')->orderBy('horario', 'DESC')->limit(50)->get();

        $visitanteHistorico = array();

        foreach($dadosVisitasInterno as $visitanteHistoricoDado)
        {
            $cpfVisita = VisitaController::getCpfVisita($visitanteHistoricoDado->fk_visita);

            $dadosSaida = DB::table('controle_acesso')->where('tabela', 'visita')->where('cpf', $cpfVisita)->where('data', $visitanteHistoricoDado->data)->where('horario', $visitanteHistoricoDado->horario)->get();

            if($dadosSaida[0]->horario_saida == null || $dadosSaida[0]->data_saida == null)
            {
                $duracao = "";
                $horaSaida = "";
                $dataSaida = "";
            }else{
                $duracao = ( strtotime($dadosSaida[0]->horario_saida) - strtotime($visitanteHistoricoDado->horario) ) / 60 ."min";
                $horaSaida = $dadosSaida[0]->horario_saida;
                $dataSaida =  $dadosSaida[0]->data_saida;
            }

            array_push($visitanteHistorico, array(
                'nomeInterno' => InternoController::getInternoNome($visitanteHistoricoDado->fk_interno),
                'fotoVisita' => asset(InternoController::getFotoInterno($visitanteHistoricoDado->fk_interno)),
                'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($visitanteHistoricoDado->data)),
                'relacao' => InternoController::getRelacaoVisita($visitanteHistoricoDado->fk_interno, $visitanteHistoricoDado->fk_visita),
                'horaEntrada' => $visitanteHistoricoDado->horario,
                'dataEntrada' => Tdata::TdataBR($visitanteHistoricoDado->data),
                'diaEntrada' => substr(Tdata::TdataBR($visitanteHistoricoDado->data),0,2),
                'anoEntrada' => substr(Tdata::TdataBR($visitanteHistoricoDado->data),6,4),
                'horaSaida' => $horaSaida,
                'dataSaida' => Tdata::TdataBR($dataSaida),
                'duracao' => $duracao
            ));
        }

        return $visitanteHistorico;

    }


    /*
    |--------------------------------------------------------------------------
    | Getters e Setters
    |--------------------------------------------------------------------------
    |
    */
    public static function getDadosVisita($id)
    {
        $dados = DB::table('visita')->where('id', $id)->get();

        return $dados;
    }

    public static function getNomeVisita($id)
    {
        $dados = DB::table('visita')->where('id', $id)->get();

        return $dados[0]->nome;
    }

    public static function getCpfVisita($id)
    {
        $dados = DB::table('visita')->where('id', $id)->get();

        return $dados[0]->cpf;
    }

    public static function getDadosVisitaCpf($cpf)
    {
        $dados = DB::table('visita')->where('cpf', $cpf)->get();

        return $dados;
    }

    public static function getFotoVisita($id)
    {
        $foto = DB::table('foto')->where('fk_id', $id)->where('fk_tabela','visita')->where('tipo','principal')->get();

        if(json_decode($foto, true) == [])
        {
            return "imagens/usuario/avatarInterno.png";
        }else{
            return "storage/app/arquivos/visitas/fotos/".$id."/".$foto[0]->nome_arquivo;
        }

    }

    public static function getFotoVisitaCPF($cpf)
    {
        $dados = self::getDadosVisitaCpf($cpf);

        $foto = self::getFotoVisita($dados[0]->id);

        return asset($foto);
    }

    public function getRelacaoInternos(Request $request)
    {
    //dd($request->get('cpf'));

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $idVisita = VisitaController::getDadosVisitaCpf($request->get('cpf'));

        $proibidos = DB::table('controle_acesso_visita_interno_suspenso')->where('fk_visita', $idVisita[0]->id)->get();

        $relacaoProibidos = array();

        foreach($proibidos as $proibido)
        {
            array_push($relacaoProibidos, $proibido->fk_interno);
        }



        $relacaoInterno = DB::table('visita_interno')
            ->leftJoin('interno','interno.id','=','visita_interno.fk_interno')
            ->leftJoin('sancao_triagem', function ($query) {
                $query->on('sancao_triagem.fk_interno', '=', 'interno.id');
            })
            ->where(function ($query) {
                $query->where('sancao_triagem.status', 'I')
                    ->orWhereNull('sancao_triagem.status');
            })
            ->where('interno.unidade_prisional',$lotacaoServidor)
            ->where('visita_interno.fk_visita',$idVisita[0]->id)
            ->whereNotIn('visita_interno.fk_interno', $relacaoProibidos)
            ->select('interno.*', 'visita_interno.*','visita_interno.fk_interno AS vFk_interno','sancao_triagem.*','sancao_triagem.fk_interno AS stFk_interno')
            ->get();


        $dadosInterno = array();
        foreach($relacaoInterno as $relacao)
        {
            $dadosInternos = (InternoController::getDadosPrincipaisInterno($relacao->vFk_interno));

            array_push($dadosInterno, array(
                "nome" => $dadosInternos[0]['nome'],
                "id" => $dadosInternos[0]['id'],
                "grau_parentesco" => InternoController::getRelacaoVisita($dadosInternos[0]['id'], $idVisita[0]->id),
                "sigla" => $dadosInternos[0]['sigla']
            ));


        }

        $dadosInterno = collect($dadosInterno);
        $dadosInterno = $dadosInterno->unique('id');

        return $dadosInterno;


    }

    public static function getRelacaoSuspensao($idVisita, $idInterno)
    {
        $dados = DB::table("controle_acesso_visita_interno_suspenso")->where("fk_visita", $idVisita)->where('fk_interno', $idInterno)->get();

        if(empty(json_decode($dados, true)))
        {
            return false;
        }else{
            return true;
        }
    }

    public static function getSuspensaoVisita($cpf)
    {
        $dados = DB::table('controle_acesso_suspensos')->where('status','A')->where('cpf', $cpf)->select('controle_acesso_suspensos.*',DB::raw("DATE_FORMAT(controle_acesso_suspensos.data_final,'%d/%m/%Y') AS data_final"))->get();

        return $dados;
    }

    public function cadastro()
    {
        return view('visita.cadastro.cadastro');
    }

    public function store(Request $request, Visita $visita)
    {
        //dd($request, $visita);

        // ---------------------- VALIDACOES --------------------------------

        $internos = $request->input('internos');
        if (!isset($internos)) {
            return redirect()->back()->withInput()->with('alert-danger', 'Informe os internos desta visita');
        }

        if($request->get('cpf') != null){
            $unique = DB::table('visita')->where('cpf', $request->get('cpf'))->get();

            if (count($unique) >= 1) {
                return redirect()->back()->withInput()->with('alert-danger', 'CPF cadastrado no sistema');
            }

        }


        // ---------------------- !VALIDACOES -------------------------------
       

        $dataForm = $request->except('_token','parentesco','interno','fotos', 'internos','endereco_cep', 'endereco_logradouro', 'endereco_numero', 'endereco_bairro', 'endereco_complemento','endereco_cidade','endereco_estado', 'telefone','inputFile','imagens','tipoFoto','legenda','tabelaFoto','inputFileDoc','nomeDoc');

        

        $dataForm['data_nascimento'] = Tdata::TdataMysql($dataForm['data_nascimento']);
        $dataForm['rg_data'] = Tdata::TdataMysql($dataForm['rg_data']);
        $dataForm['validade_carteira'] = Tdata::TdataMysql($dataForm['validade_carteira']);

        // se os campos de datas estivivem vazios, para não retorna erro no bd, assumem o valor null
        if ($dataForm['data_nascimento'] == "") {
            $dataForm['data_nascimento'] = (NULL);
        }

        if ($dataForm['rg_data'] == "") {
            $dataForm['rg_data'] = (NULL);
        }

        $dataForm['status'] = "A";
        if(isset($dataForm['visitas'])){
            $dataForm['responsavel'] = $dataForm['visitas'][0];
        }
        //dd($dataForm);
        $idVisita = $visita->create($dataForm)->id;

        if($idVisita)
        {
            LogController::log($idVisita,"visita","add",Auth::user()->cpf,"Criação do cadastro");
        }


        // -------------------- inserir os enderecos --------------------------- //

        $logradouroDefault = $request->get('endereco_logradouro')[0];


        if($logradouroDefault != null) {

            $qtdEndereco = count($request->get('endereco_logradouro'));

            for ($i = 0; $i < $qtdEndereco; $i++) {

                $endereco_cep = $request->get('endereco_cep');
                $endereco_logradouro = $request->get('endereco_logradouro');
                $endereco_numero = $request->get('endereco_numero');
                $endereco_bairro = $request->get('endereco_bairro');
                $endereco_complemento = $request->get('endereco_complemento');
                $endereco_cidade = $request->get('endereco_cidade');
                $endereco_estado = $request->get('endereco_estado');

                $address = $endereco_logradouro[$i] . " " . $endereco_numero[$i] . " " . $endereco_bairro[$i] . " " . $endereco_cidade[$i] . " " . $endereco_estado[$i];
                $coordenadas = GeocodingMaps::getGeocodeData($address);


                $insertEndereco = DB::table('endereco')->insert([
                    'fk_id' => $idVisita,
                    'fk_tabela' => "visita",
                    'pais' => "null",
                    'estado' => $endereco_estado[$i],
                    'cidade' => $endereco_cidade[$i],
                    'bairro' => $endereco_bairro[$i],
                    'cep' => $endereco_cep[$i],
                    'logradouro' => $endereco_logradouro[$i],
                    'numero' => $endereco_numero[$i],
                    'complemento' => $endereco_complemento[$i],
                    'latitude' => $coordenadas[0],
                    'longitude' => $coordenadas[1],
                    'created_at' => date("Y-m-d H:i:s"),
                ]);

            }

        } // se o logradouroDefault for preenchido

        // -------------------- !! inserir os enderecos ------------------------------

        // -------------------- inserir os internos --------------------------- //

        $internos = $request->input('internos');

        if ($internos[0] != null) {

            foreach ($internos as $preso) {

                $interno = explode("-", $preso);

                $insertRelacao = DB::table('visita_interno')->insert([
                        'fk_interno' => $interno[1],
                        'fk_visita' => $idVisita,
                        'status' => 'A',
                        'grau_parentesco' => $interno[0],
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
                        'fk_id' => $idVisita,
                        'fk_tabela' => "visita",
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
                $imageName = $idVisita . "_visita_" . time() . "_" . '.'.'jpg';
                $p = Storage::disk('local')->put(Constantes::ARVQUIVOS_VISITA.'/fotos/'.$idVisita.'/'. $imageName, $image, 'storage');

                $insertFoto = DB::table('foto')->insert(
                    ['fk_id' => $idVisita,
                        'fk_tabela' => "visita",
                        'nome_arquivo' => $imageName,
                        'tipo' => 'outros',
                        'created_at' => date("Y-m-d H:i:s")
                    ]
                );

                $idFoto = DB::getPdo()->lastInsertId();

                LogController::log($idFoto,"foto","add", Auth::user()->cpf, "Inclusão de foto da visita<br>Arquivo: <i><strong>". $imageName ."</i></strong><br>Tipo: <strong><i>outros</i></strong>");


            }

        }

        // se enviar o arquivo
        if($request->file('inputFile')[0] != null) {

            $qtdFoto = count($request->file('inputFile'));
            $tipoFoto = $request->get('tipoFoto');
            $legenda = $request->get('legenda');


            for ($x = 0; $x < $qtdFoto; $x++) {

                $destinationPath = Constantes::ARVQUIVOS_VISITA . "/fotos/" . $idVisita;


                if($tipoFoto[$x] == null)
                {
                    $tipofoto = "outros";
                }else{
                    $tipofoto = $tipoFoto[$x];
                }

                if($tipoFoto[$x] == "principal")
                {
                    $name = $idVisita .".jpg";

                }else {

                    $name = $idVisita . "_" . time() . "_" . $request->file('inputFile')[$x]->getClientOriginalName();
                }

                $request->file('inputFile')[$x]->storeAs($destinationPath, $name);
                //Storage::put($name, $destinationPath);


                $insertFoto = DB::table('foto')->insert(
                    ['fk_id' => $idVisita,
                        'fk_tabela' => "visita",
                        'nome_arquivo' => $name,
                        'legenda' => $legenda[$x],
                        'tipo' => $tipofoto,
                        'created_at' => date("Y-m-d H:i:s")
                    ]
                );

                $idFoto = DB::getPdo()->lastInsertId();

                LogController::log($idFoto,"foto","add", Auth::user()->cpf, "Inclusão de foto da visita<br>Arquivo: <i><strong>". $name ."</strong></i> <br>Legenda: <strong><i>".$legenda[$x]."</i></strong><br>Tipo: <strong><i>".$tipoFoto[$x]."</i></strong>");


            }
        }


        // -------------------- !! inserir as fotos --------------------------- //

        // -------------------- inserir os Documentos --------------------------- //

        if($request->file('inputFileDoc')[0] != null) {

            $qtdDoc = count($request->file('inputFileDoc'));
            $legendaDoc = $request->get('nomeDoc');

            for ($x = 0; $x < $qtdDoc; $x++) {

                $destinationPath = Constantes::ARVQUIVOS_VISITA . "/documentos/" . $idVisita;

                $name = $idVisita . "_" . time() . "_" . $request->file('inputFileDoc')[$x]->getClientOriginalName();

                $request->file('inputFileDoc')[$x]->storeAs($destinationPath, $name);
                //Storage::put($name, $destinationPath);

                $insertDoc = DB::table('documento')->insert(
                    ['fk_id' => $idVisita,
                        'fk_tabela' => "visita",
                        'nome_arquivo' => $name,
                        'descricao' => $legendaDoc[$x],
                        'created_at' => date("Y-m-d H:i:s")
                    ]
                );

                $idDoc = DB::getPdo()->lastInsertId();

                LogController::log($idDoc,"documento","add", Auth::user()->cpf, "Inclusão de documento da visita<br>Arquivo: <i><strong>". $name ."</strong></i>");

            }
        }


        // -------------------- !! inserir os Fotos --------------------------- //


        if ($idVisita != "" || $idVisita != "null") {

            return redirect()->route('visita.buscar')->with('alert-success', 'O cadastro da visita foi realizado com sucesso!');

        }else{

            return redirect('visita.cadastro')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }

    public function buscar(Request $request)
    {

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $unidades = DB::table('unidade')->where('status','1')->orderBy('sigla', 'ASC')->get();

        /**
         * se não realizar uma busca
         * - Evita que mostre todos os resultados sem realizar uma busca
         */
        if(!strpos($url,"?"))
        {

            $dados = false;

        }else {

            $visita = DB::table('visita');


            $visita->leftJoin('controle_acesso_suspensos',function($query){
                $query->on('controle_acesso_suspensos.cpf','=','visita.cpf')->where('controle_acesso_suspensos.status',"A");
            });

//            $visita->leftJoin('controle_acesso_suspensos','controle_acesso_suspensos.cpf','=','visita.cpf');

            $nome = $request->get('nome');
            $sexo = $request->get('sexo');
            $status = $request->get('status');
            $endereco_logradouro = $request->get('endereco_logradouro');
            $endereco_bairro = $request->get('endereco_bairro');
            $endereco_estado = $request->get('endereco_estado');
            $endereco_cidade = $request->get('endereco_cidade');
            $cpf = $request->get('cpf');

            $nome_interno = $request->get('nome_interno');
            $unidade_prisional = $request->get('unidade_prisional');
            $pavilhao = $request->get('pavilhao');
            $ala = $request->get('ala');
            $cela = $request->get('cela');


            if ($nome != null) {
                $visita->where('visita.nome', 'LIKE', '%' . $nome . '%');
            }


            if ($sexo != null) {
                foreach($sexo as $s)
                {
                    $visita->where('visita.sexo', $s);

                }
            }

            if ($status != null) {
                foreach($status as $stat)
                {
                    $visita->where('visita.status', $stat);

                }
            }

            if($endereco_cidade != null || $endereco_logradouro != null || $endereco_bairro != null || $endereco_estado != null)
            {
                $visita->leftJoin('endereco',function($query){
                $query->on('endereco.fk_id','=','visita.id')->where('endereco.fk_tabela','=', 'visita');
            });

                    if($endereco_cidade)
                    {
                        $visita->where('endereco.cidade', 'LIKE', '%' . $endereco_cidade . '%');
                    }

                    if($endereco_estado)
                    {
                        $visita->where('endereco.estado', 'LIKE', '%' . $endereco_estado . '%');
                    }

            }

            if($nome_interno != null || $unidade_prisional != null || $pavilhao != null || $ala != null || $cela != null){
                $visita->leftJoin('visita_interno',function($query){
                    $query->on('visita_interno.fk_visita','=','visita.id');
                });

                $visita->leftJoin('interno',function($query){
                    $query->on('interno.id','=','visita_interno.fk_interno');
                });
            }

            if($nome_interno != null){
                $visita->where('interno.nome','LIKE', '%' . $nome_interno . '%');
            }

            if ($unidade_prisional != null) {
                $visita->where('interno.unidade_prisional', $unidade_prisional);
            }

            if ($pavilhao != null) {
                $visita->where('interno.pavilhao', $pavilhao);
            }

            if ($ala != null) {
                $visita->where('interno.ala', $ala);
            }

            if ($cela != null) {
                $visita->where('interno.cela', $cela);
            }

            if ($endereco_logradouro != null) {

                $visita->where('endereco.logradouro', 'LIKE', '%' .$endereco_logradouro. '%');
            }

            if ($endereco_bairro != null) {

                $visita->where('endereco.bairro', 'LIKE', '%' .$endereco_bairro. '%');
            }

            if ($endereco_estado != null) {
                $visita->where('endereco.estado', $endereco_estado);
            }

            if ($endereco_cidade != null) {
                $visita->where('endereco.cidade', $endereco_cidade);
            }

            if ($cpf != null) {
                $visita->where('visita.cpf', $cpf);
            }

            if ($nome_interno != null){

            }


            // passa a query para a paginacao "->appends..."
        $dados = $visita->select('visita.nome',"visita.id","visita.cpf","visita.status", 'controle_acesso_suspensos.status as controleAcessosSuspenso')->orderBy('visita.nome','ASC')->get();

            $dados = $dados->unique('id');

        }
        if($dados != false) {
            $dados = collect($dados)->paginate(50)->appends($request->input());
        }

        return view('visita.buscar.buscar', compact('dados', 'unidades'));
    }

    public function editar(Visita $visita, $id)
    {

        $visita = $visita->find($id);

        $visita->data_nascimento = Tdata::TdataBR($visita->data_nascimento);
        $visita->rg_data = Tdata::TdataBR($visita->rg_data);
        $visita->validade_carteira = Tdata::TdataBR($visita->validade_carteira);

        $relacaoInterno = DB::table('visita_interno')->where('fk_visita',$id)->get();

        $endereco = DB::table('endereco')->where('fk_id',$id)->where('fk_tabela','visita')->get();

        $fotos = DB::table('foto')->where('fk_id',$id)->where('fk_tabela','visita')->get();

        $telefone = DB::table('contato')->where('fk_id',$id)->where('fk_tabela','visita')->get();

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
                  "grau_parentesco" => InternoController::getRelacaoVisita($dadosInternos[0]['id'], $id),
                  "suspenso" => self::getRelacaoSuspensao($id, $dadosInternos[0]['id']),
                  "sigla" => $dadosInternos[0]['sigla']
            ));

        }

        $documentos = DB::table('documento')->where('fk_id',$id)->where('fk_tabela','visita')->get();
        foreach($documentos as $documento)
        {
            $documento->created_at = Tdata::TtimestampBR($documento->created_at);
        }
        $nomeresponsavel=DB::table('visita')->where('visita.id',$visita->responsavel)->get();
        
        return view('visita.cadastro.cadastro', compact('visita','dadosInterno','endereco','telefone','fotos','documentos', 'nomeresponsavel'));
    }


    public function update(Request $request, Visita $visita, $id)
    {

        $diferencas = array();

        $dataForm = $request->except('_token','parentesco','interno','fotos', 'internos','endereco_cep', 'endereco_logradouro', 'endereco_numero', 'endereco_bairro', 'endereco_complemento','endereco_cidade','endereco_estado', 'telefone','inputFile','imagens','idPessoa','tipoFoto','tipoFotoRegistrada','legenda','tabelaFoto','inputFileDoc','nomeDoc','enderecosRemovidos');

        $dataForm['data_nascimento'] = Tdata::TdataMysql($dataForm['data_nascimento']);
        $dataForm['rg_data'] = Tdata::TdataMysql($dataForm['rg_data']);
        $dataForm['validade_carteira'] = Tdata::TdataMysql($dataForm['validade_carteira']);
        //dd($dataForm['validade_carteira']);
        $cpfUnico  = DB::table('visita')->where('visita.cpf', $dataForm['cpf'])->get()->first();

        if($cpfUnico != null){
            if($cpfUnico->id != $id){
                return redirect()->back()->with('alert-danger', 'O CPF informado já está sendo utilizado em outro cadastro!');
            }
        }

        // se os campos de datas estivivem vazios, para não retorna erro no bd, assumem o valor null
        if ($dataForm['data_nascimento'] == "") {
            $dataForm['data_nascimento'] = (NULL);
        }

        if ($dataForm['rg_data'] == "") {
            $dataForm['rg_data'] = (NULL);
        }



        // inserir e alterar o grau de parentesco do interno
        $internos = $request->input('internos');

        foreach($internos as $interno)
        {

            if(!is_numeric($interno))
            {
                $preso = explode("-", $interno);

                $visitaRelacionamento = DB::table('visita_interno')->where('fk_interno',$preso[1])->where('fk_visita',$id)->get();
                $nomeInterno = InternoController::getDadosPrincipaisInterno($preso[1]);

                // se for um novo registro
                if(empty(json_decode($visitaRelacionamento)))
                {
                    $insertRelacao = DB::table('visita_interno')->insert([
                            'fk_interno' => $preso[1],
                            'fk_visita' => $id,
                            'status' => 'A',
                            'grau_parentesco' => $preso[0],
                            'created_at' => date("Y-m-d H:i:s"),
                        ]
                    );


                    // LOG
                    $alteracoes = "Inserção de vinculo da visita com o interno (<i><strong>". $nomeInterno[0]['nome'] ."</i></strong>).<br>";
                    array_push($diferencas, $alteracoes);


                }else{
                    // se for uma alteracao de grau de parentesco
                    $updateRelacao = DB::table('visita_interno')->where('fk_interno',$preso[1])->where('fk_visita',$id)->update([
                            'grau_parentesco' => $preso[0],
                            'update_at' => date("Y-m-d H:i:s"),
                        ]
                    );

                    // LOG
                    $alteracoes = "Alteração de grau de parentesco: <i><strong>". $nomeInterno[0]['nome'] ." - ". $preso[0]."</i></strong><br>";
                    array_push($diferencas, $alteracoes);


                }
            }
        }

        // ---------------------- ! inserir e alterar o grau de parentesco -----------------------------


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
                            'fk_tabela' => "visita",
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

        //-------------- Endereco ----------------------------//

        // remove os enderecos selecionados
        $enderecoRemovido = $request->get('enderecosRemovidos');
        if($enderecoRemovido != null || !empty($enderecoRemovido))
        {
            foreach($enderecoRemovido as $endId)
            {
                $verificaEnderecoDel = DB::table('endereco')->where('id', $endId)->get();

                // LOG
                $alteracoes = "Exclusão de dados do campo  endereco: <i><strong>".$verificaEnderecoDel[0]->logradouro.", ".$verificaEnderecoDel[0]->numero.", ".$verificaEnderecoDel[0]->complemento.", ".$verificaEnderecoDel[0]->bairro.", ".$verificaEnderecoDel[0]->cidade."-".$verificaEnderecoDel[0]->estado."</i></strong><br>";
                array_push($diferencas, $alteracoes);

                $deleteEndereco = DB::table('endereco')->where('id', $endId)->delete();

            }

        }
        // faz a busca do endereco, o que nao constar no banco faz a insercao
        $enderecoPost = $request->get('endereco_logradouro');

        if($enderecoPost != null) {

            foreach ($enderecoPost as $end) {

                $verificaEndereco = Endereco::where('logradouro', 'LIKE', '%' . $end . '%')->select('logradouro')->where('fk_id', $id)->get();

                if (empty(json_decode($verificaEndereco, true))) {
                    // pega o indice do array do endereco
                    $key = array_search($end, $enderecoPost);

                    // pega latitude e longitude
                    $address = $request->get('endereco_logradouro')[$key] . " " . $request->get('endereco_numero')[$key] . " " . $request->get('endereco_bairro')[$key] . " " . $request->get('endereco_cidade')[$key] . " " . $request->get('endereco_estado')[$key];
                    $coordenadas = GeocodingMaps::getGeocodeData($address);

                    // faz a insercao do endereco
                    $insertEndereco = DB::table('endereco')->insert([
                        'fk_id' => $id,
                        'fk_tabela' => "visita",
                        'pais' => "null",
                        'estado' => $request->get('endereco_estado')[$key],
                        'cidade' => $request->get('endereco_cidade')[$key],
                        'bairro' => $request->get('endereco_bairro')[$key],
                        'cep' => $request->get('endereco_cep')[$key],
                        'logradouro' => $request->get('endereco_logradouro')[$key],
                        'numero' => $request->get('endereco_numero')[$key],
                        'complemento' => $request->get('endereco_complemento')[$key],
                        'latitude' => $coordenadas[0],
                        'longitude' => $coordenadas[1],
                        'created_at' => date("Y-m-d H:i:s"),
                    ]);

                    if($insertEndereco)
                    {
                        $alteracoes = "Inclusão de dados de endereco: <i><strong>".$address."</i></strong><br>";
                        array_push($diferencas, $alteracoes);

                    }

                }

            }
        }


        //-------------- !! Endereco -------------------------//


        // -------------------- inserir os Fotos --------------------------- //

        $imagens = $request->get('imagens');

        if(isset($imagens)){

            foreach($imagens as $images){

                list($baseType, $image) = explode(';', $images);
                list(, $image) = explode(',', $image);
                $image = base64_decode($image);
                $imageName = $id . "_visita_" . time() . "_" . '.'.'jpg';
                $p = Storage::disk('local')->put(Constantes::ARVQUIVOS_VISITA.'/fotos/'.$id.'/'. $imageName, $image, 'public');

                $insertFoto = DB::table('foto')->insert(
                    ['fk_id' => $id,
                        'fk_tabela' => "visita",
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
        //dd($request->file('inputFile'));
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

                    $selectFoto = DB::table('foto')->where('fk_id', $id)->where('fk_tabela', 'visita')->where('tipo','principal')->get();

                    if(count(json_decode($selectFoto,true)) > 0) {

                        $renameOld = time() .$selectFoto[0]->nome_arquivo;
                        Storage::move("arquivos/visitas/fotos/" . $id . "/" . $selectFoto[0]->nome_arquivo, "arquivos/visitas/fotos/" . $id . "/" . $renameOld);

                        $updateTipo = DB::table('foto')->where('id', $selectFoto[0]->id)->update([
                            'tipo' => 'outros',
                            'nome_arquivo' => $renameOld,
                        ]);

                    }

                }else{

                    $name = $id . "__".$x."__" . time() . "_" . $request->file('inputFile')[$x]->getClientOriginalName();

                }

                $destinationPath = Constantes::ARVQUIVOS_VISITA . "/fotos/" . $id;


                $request->file('inputFile')[$x]->storeAs($destinationPath, $name);
                //Storage::put($name, $destinationPath);


                $insertFoto = DB::table('foto')->insert(
                    ['fk_id' => $id,
                        'fk_tabela' => "visita",
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


        // -------------------- !! inserir os Fotos --------------------------- //

        // -------------------- inserir os Documentos --------------------------- //

        if($request->file('inputFileDoc') != null) {

            $qtdDoc = count($request->file('inputFileDoc'));
            $legendaDoc = $request->get('nomeDoc');

            for ($x = 0; $x < $qtdDoc; $x++) {

                if($legendaDoc[$x] == null)
                {
                    return redirect()->back()->with('alert-danger', 'Informe o nome do documento');

                }

                $destinationPath = Constantes::ARVQUIVOS_VISITA . "/documentos/" . $id;

                $name = $id . "_" . time() . "_" . $request->file('inputFileDoc')[$x]->getClientOriginalName();

                $request->file('inputFileDoc')[$x]->storeAs($destinationPath, $name);
                //Storage::put($name, $destinationPath);

                $insertDoc = DB::table('documento')->insert(
                    ['fk_id' => $id,
                        'fk_tabela' => "visita",
                        'nome_arquivo' => $name,
                        'descricao' => $legendaDoc[$x],
                        'created_at' => date("Y-m-d H:i:s")
                    ]
                );

                $idDoc = DB::getPdo()->lastInsertId();

                LogController::log($idDoc,"documento","add", Auth::user()->cpf, "Inclusão de documento da visita<br>Arquivo: <i><strong>". $name ."</strong></i>");

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
        if(isset($dataForm['visitas'])){
            $dataForm['responsavel'] = $dataForm['visitas'][0];
        }

        unset($dataForm['visita']);
        unset($dataForm['visitas']);
        
        $dadosBD = $visita->where('id', $id)->get();
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

                    $alteracoes = "Alteração de dados no campo " . ucfirst(str_replace("_", " ", $k)) . ": <i><strong>" . $dataForm[$k] . "</i></strong><br>";
                    array_push($diferencas, $alteracoes);
                }
            }

        }

        //-------------------- LOG -------------------------------------

        //-------------- Faz o update no controle de acesso ----------------------------//

        $visitaBD = DB::table('visita')->where('id', $id)->get()->first();
        $controleAcesso = DB::table('controle_acesso')->where('controle_acesso.cpf', $visitaBD->cpf)->get();
        foreach($controleAcesso as $controle){
            DB::table('controle_acesso')->where('id', $controle->id)->update(['cpf' => $dataForm['cpf']]);
        }

        //-------------- Faz o update no BD ----------------------------//

        $update = $visita->find($id)->update($dataForm);

        //-------------- !! Faz o update no BD -------------------------//


        if ($update) {

            if(!empty($diferencas))
            {
                $textoLog = implode(" ", $diferencas);
                LogController::log($id,"visita","update", Auth::user()->cpf, $textoLog);
            }

            return redirect()->route('visita.buscar')->with('alert-success', 'O cadastro da visita foi alterado com sucesso!');

        }else{

            return redirect('visita.cadastro')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }

    public function documentoDelete($id, $filename)
    {

        if (file_exists(storage_path('app/arquivos/visitas/documentos/' . $id . "/" . $filename))) {

            $move = Storage::move(Constantes::ARVQUIVOS_VISITA . "/documentos/" . $id . "/" . $filename, Constantes::ARQUIVO_MORTO . "/" . $filename);

            $delete = DB::table('documento')->where('fk_id', $id)->where('fk_tabela', 'visita')->where('nome_arquivo', $filename)->delete();

        }else{

            $delete = DB::table('documento')->where('fk_id', $id)->where('fk_tabela', 'visita')->where('nome_arquivo', $filename)->delete();

            return "errorMove";
        }

        if ($delete) {

            LogController::log($id,"documento","delete", Auth::user()->cpf, "Exclusão de documento da Visita<br>Arquivo: <i><strong>". $filename ."</strong></i>");

            return redirect()->back()->with('alert-success', 'Documento deletado com sucesso!');

        }else{

            return redirect()->back()->with('alert-danger', 'Ocorreu um problema ao deletar o documento, por favor, tente novamente!');

        }


    }

    public function suspenderVisita($idVisita, $idInterno)
    {
        $insertSuspensao = DB::table('controle_acesso_visita_interno_suspenso')->insert([

            'fk_visita' => $idVisita,
            'fk_interno' => $idInterno,
            'data' => Tdata::TdataHojeMysql(),
            'created_at' => Tdata::TtimeStampHojeMysql(),

        ]);

        $idInsert = DB::getPdo()->lastInsertId();

        if ($insertSuspensao) {

            LogController::log($idInsert,"controle_acesso_visita_interno_suspenso","add", Auth::user()->cpf, "Inclusão de suspensão da visita ".self::getNomeVisita($idVisita)." para interno ".InternoController::getInternoNome($idInterno)."");

            return redirect()->back()->with('alert-success', 'Suspensão Inserida com sucesso!');

        }else{

            return redirect()->back()->with('alert-danger', 'Ocorreu um problema ao realizar a suspensão, por favor, tente novamente!');

        }

    }

    public function permitirVisita($idVisita, $idInterno)
    {
        $deleteSuspensaoId = DB::table('controle_acesso_visita_interno_suspenso')->where('fk_visita', $idVisita)->where('fk_interno', $idInterno)->get();

        $id = $deleteSuspensaoId[0]->id;

        $deleteSuspensao = DB::table('controle_acesso_visita_interno_suspenso')->where('fk_visita', $idVisita)->where('fk_interno', $idInterno)->delete();


        if ($deleteSuspensao) {

            LogController::log($id,"controle_acesso_visita_interno_suspenso","delete", Auth::user()->cpf, "Exclusão de suspensão da visita ".self::getNomeVisita($idVisita)." para interno ".InternoController::getInternoNome($idInterno)."");

            return redirect()->back()->with('alert-success', 'Permissão Inserida com sucesso!');

        }else{

            return redirect()->back()->with('alert-danger', 'Ocorreu um problema ao realizar a suspensão, por favor, tente novamente!');

        }

    }
    public function show($id)
    {
        $visita = DB::table('visita')->where('id', $id)->get();
        $visita = json_decode($visita, true)[0];

        $visita['status'] = DB::table('visita')->where('id',$id)->get();
        $visita['status'] = json_decode($visita['status'],true)[0]['status'];

        $outrosnomes = DB::table('outrosnomes')->where('pk_tabela', 'visita')->where('pk_id', $id)->get();
        $visita['outrosnomes'] = [];
        if(!is_null($outrosnomes)) {
            foreach (json_decode($outrosnomes, true) as $i) {
                array_push($visita['outrosnomes'], $i['nome']);
            }
        }
        $contatos = DB::table('contato')->where('fk_tabela', 'visita')->where('fk_id', $id)->get();
        $visita['contatos'] = [];
        if(!is_null($contatos)){
            foreach(json_decode($contatos,true) as $i){
                array_push($visita['contatos'], $i['numero']);
            }
        }
        $enderecos = DB::table('endereco')->where('fk_tabela', 'visita')->where('fk_id', $id)->get();
        $visita['enderecos'] = [];
        if(!is_null($enderecos)){
            foreach(json_decode($enderecos,true) as $i){
                array_push($visita['enderecos'], $i);
            }
        }
        if(!is_null($visita['rg_data'])) $visita['rg_data'] = Tdata::TdataBR($visita['rg_data']);
        if(!is_null($visita['data_nascimento'])) $visita['data_nascimento'] = Tdata::TdataBR($visita['data_nascimento']);
        if(!is_null($visita['data_nascimento'])) $visita['idade'] = Tdata::getIdade($visita['data_nascimento']);
        else $visita['idade'] = "";
        if(!is_null($visita['created_at'])) $visita['created_at'] = "às ".Tdata::getTempoTimestamp($visita['created_at'])." em ".Tdata::TdataBR(Tdata::getDataTimestamp($visita['created_at']));
        else $visita['created_at'] = "";
        if(!is_null($visita['updated_at'])) $visita['updated_at'] = "às ".Tdata::getTempoTimestamp($visita['updated_at'])." em ".Tdata::TdataBR(Tdata::getDataTimestamp($visita['updated_at']));
        else $visita['updated_at'] = "";
        $visita['qtd_ocorrencias'] = DB::table('ocorrencia_envolvidos')->where('fk_tabela', 'visita')->where('fk_ocorrencia', $id)->count();
        $visita['qtd_ocorrencias'] = DB::table('ocorrencia_envolvidos')->where('fk_tabela', 'visita')->where('fk_ocorrencia', $id)->count();
        if($visita['qtd_ocorrencias'] == 0) $visita['qtd_ocorrencias'] = "0";
        $visita['presos_visitados'] = [];
        $visita['presos_visitados'] = json_decode(DB::table('visita_interno')->where('visita_interno.fk_visita', $id)->get(), true);
        foreach($visita['presos_visitados'] as $key=>$presoVisitado){
            $visita['presos_visitados'][$key]['dadosInterno'] = InternoController::getDadosPrincipaisInterno($presoVisitado['fk_interno'])[0];
            $visita['presos_visitados'][$key]['statusSuspensao'] = DB::table('controle_acesso_visita_interno_suspenso')->where('fk_visita', $visita['id'])->where('fk_interno',$presoVisitado['fk_interno'])->get();
            if($visita['presos_visitados'][$key]['statusSuspensao'] != collect([])){
                $visita['presos_visitados'][$key]['statusSuspensao'] = json_decode($visita['presos_visitados'][$key]['statusSuspensao'], true)[0]['status_suspensao'];
                if($visita['presos_visitados'][$key]['statusSuspensao'] == 'A'){
                    $visita['presos_visitados'][$key]['statusSuspensao'] = "Visita Suspensa";
                }
                else if($visita['presos_visitados'][$key]['statusSuspensao'] == 'I'){
                    $visita['presos_visitados'][$key]['statusSuspensao'] = "Visita Autorizada";
                }
            }
            else{
                $visita['presos_visitados'][$key]['statusSuspensao'] = "Visita Autorizada";
            }
            if($visita['tipo'] == 'P'){
                $visita['presos_visitados'][$key]['statusSuspensao'] = "Visita Proibida";
            }
            // foto do preso
            if(is_null($visita['presos_visitados'][$key]['dadosInterno']['foto'])){
                $visita['presos_visitados'][$key]['dadosInterno']['foto'] = 'storage/app/semfoto.jpg';
            }
            else{
                $visita['presos_visitados'][$key]['dadosInterno']['foto'] = 'storage/app/arquivos/internos/fotos/'.$visita['presos_visitados'][$key]['dadosInterno']['id'].'/'.$visita['presos_visitados'][$key]['dadosInterno']['foto'];
            }
            $fotopresotemp = DB::table('foto')->where('fk_tabela', 'interno')->where('fk_id', $visita['presos_visitados'][$key]['dadosInterno']['id'])->where('tipo','principal')->get();
            if($fotopresotemp != collect([])){
                $fotopresotemp = json_decode($fotopresotemp, true);
                $visita['presos_visitados'][$key]['dadosInterno']['fotolegenda'] = $fotopresotemp[0]['legenda'];
                $visita['presos_visitados'][$key]['dadosInterno']['fototipo'] = $fotopresotemp[0]['tipo'];
                $visita['presos_visitados'][$key]['dadosInterno']['fotoinserida'] = Tdata::TdataBR(Tdata::getDataTimestamp($fotopresotemp[0]['created_at']));
                $visita['presos_visitados'][$key]['dadosInterno']['fotoalterada'] = Tdata::TdataBR(Tdata::getDataTimestamp($fotopresotemp[0]['updated_at']));
            }
        }
        //dd($visita['presos_visitados']);
        $visita['qtd_presos_visitados'] = count($visita['presos_visitados']);
        if($visita['qtd_presos_visitados'] == 0) $visita['qtd_presos_visitados'] = "0";

        //dd($visita);

        // -------------------------- Proibicao -----------------------------------------------------------------------------------
        $cpfVis = self::getCpfVisita($id);
        $proibicaoVerifica = DB::table('controle_acesso_suspensos')->where('cpf', $cpfVis)->where('status', 'A')->get();
        if(count($proibicaoVerifica) > 0)
        {
            $proibicao = "S";
        }else{
            $proibicao = "N";
        }

        // -------------------------- !Proibicao ----------------------------------------------------------------------------------

        // -------------------------- FOTOS -----------------------------------------------------------------------------------

        $visita['fotos'] = DB::table('foto')->where('fk_tabela', 'visita')->where('fk_id', $id)->select('foto.*', DB::raw("DATE_FORMAT(foto.updated_at,'%d/%m/%Y') as updated_at"), DB::raw("DATE_FORMAT(foto.created_at,'%d/%m/%Y') as created_at"))->get();
        if($visita['fotos'] != collect([])) $visita['fotos'] = json_decode($visita['fotos'], true);

        $fotoPerfilDados = DB::table('foto')->where('fk_tabela', 'visita')->where('fk_id', $id)->where('tipo','principal')->get();
        if($fotoPerfilDados != collect([])){
            $fotoPerfilDados = json_decode($fotoPerfilDados, true)[0];
            $fotoPerfilDados['nome_arquivo'] = 'storage/app/arquivos/visitas/fotos/'.$visita['id'].'/'.$fotoPerfilDados['nome_arquivo'];
            $fotoPerfilDados['created_at'] = Tdata::TdataBR(Tdata::getDataTimestamp($fotoPerfilDados['created_at']));
            $fotoPerfilDados['updated_at'] = Tdata::TdataBR(Tdata::getDataTimestamp($fotoPerfilDados['updated_at']));
        }
        else{
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
            ->where('documento.fk_tabela','visita')
            ->select("log.*", "log.created_at as logData","documento.*")
            ->get();

        foreach($documentos as $documento)
        {
            $documento->created_at = Tdata::TtimestampBR($documento->created_at);
            $documento->logData = Tdata::TtimestampBR($documento->logData);
            $documento->cpf_usuario = FacadeController::getNomeServidorCpf($documento->cpf_usuario)." (".$documento->cpf_usuario.")";
        }

        // ------------------------------------------------------------------------------------------------------------------------

        // ------------------------- visitas -----------------------------------------------------------------------------------

        $visitanteHistoricoDados = DB::table('controle_acesso_visita_interno')->where('fk_visita', $id)->orderBy('data', 'DESC')->orderBy('horario', 'DESC')->limit(50)->get();

        //dd($visitanteHistoricoDados);

        $visitanteHistorico = array();

        foreach($visitanteHistoricoDados as $visitanteHistoricoDado) {
            $cpfVisita = VisitaController::getCpfVisita($visitanteHistoricoDado->fk_visita);

            if (!empty($cpfVisita)) {

                $dadosSaida = DB::table('controle_acesso')->where('tabela', 'visita')->where('cpf', $cpfVisita)->where('data', $visitanteHistoricoDado->data)->where('horario', $visitanteHistoricoDado->horario)->get();

//            dd($cpfVisita);
                try {
                    if ($dadosSaida[0]->horario_saida == null || $dadosSaida[0]->data_saida == null) {
                        $duracao = "";
                        $horaSaida = "";
                        $dataSaida = "";
                    } else {
                        $duracao = (strtotime($dadosSaida[0]->horario_saida) - strtotime($visitanteHistoricoDado->horario)) / 60 . "min";
                        $horaSaida = $dadosSaida[0]->horario_saida;
                        $dataSaida = $dadosSaida[0]->data_saida;
                    }
                } catch(\Exception $e){ //No caso da consulta não retornar nada e tentar acessar o indice [0]
                    $duracao = "";
                    $horaSaida = "";
                    $dataSaida = "";
                }

                array_push($visitanteHistorico, array(
                    'nomeInterno' => InternoController::getInternoNome($visitanteHistoricoDado->fk_interno),
                    'fotoVisita' => InternoController::getFotoInterno($visitanteHistoricoDado->fk_interno),
                    'mesData' => Tdata::getMesExtAbrev(Tdata::getMesData($visitanteHistoricoDado->data)),
                    'relacao' => InternoController::getRelacaoVisita($visitanteHistoricoDado->fk_interno, $visitanteHistoricoDado->fk_visita),
                    'horaEntrada' => $visitanteHistoricoDado->horario,
                    'dataEntrada' => Tdata::TdataBR($visitanteHistoricoDado->data),
                    'horaSaida' => $horaSaida,
                    'dataSaida' => Tdata::TdataBR($dataSaida),
                    'duracao' => $duracao
                ));
            }
        }

        //dd($visitanteHistorico);

        // ------------------------- !visitas ----------------------------------------------------------------------------------

        // ------------------------- ocorrencias -----------------------------------------------------------------------------------
        $dadosOcorrencias = DB::table('ocorrencia_envolvidos')
            ->leftJoin('ocorrencia','ocorrencia.id','=','ocorrencia_envolvidos.fk_ocorrencia')
            ->leftJoin('ocorrencia_natureza','ocorrencia_natureza.id','=','ocorrencia.natureza')
            ->where('ocorrencia_envolvidos.fk_tabela','visita')
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

            $dadosVisitantes = DB::table('ocorrencia_envolvidos')->where('fk_tabela', 'visita')->where('fk_ocorrencia', $dadosOcorrencia->idOcorrencia)->where('fk_id_envolvido', '!=', $id)->get();
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

            $dadosAdvs = DB::table('ocorrencia_envolvidos')->where('fk_tabela', 'advogado')->where('fk_ocorrencia', $dadosOcorrencia->idOcorrencia)->get();
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


        $i = 0;
        $keys = array_keys($visita);
        foreach($visita as $item){
            if(is_null($item) || $item == ""){
                $visita[$keys[$i]] = "Não Informado";
            }
            $i++;
        }
//        $visita['ocorrencia_registrada'] = ;
//        $visita['biometria_sejuc'] = ;
        return view('visita.show.show', compact('visita','proibicao','fotoPerfilDados','documentos','visitanteHistorico','id','ocorrencias'));
    }

    public function confereVisita()
    {
        $lotacaoServidor = FacadeController::getLotacaoServidor(Auth::user()->cpf);

        $unidades = DB::table('unidade')->where('sigla',$lotacaoServidor)->orderBy('sigla', 'ASC')->get();

        return view('visita.confere.confere', compact('unidades'));

    }

    public function confereVisitaStore(Request $request)
    {
        ini_set('max_execution_time', 3000);
        ini_set('memory_limit','256M');

        $unidade = $request->get('unidade_prisional');
        $pavilhao = $request->get('pavilhao');
        $ala = $request->get('ala');
        $cela = $request->get('cela');
        $tipoConfere = $request->get('tipoConfere');

        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $visitaDados = DB::table("visita_interno");

        $visitaDados->leftJoin('visita', function ($query) {
            $query->on('visita.id', '=', 'visita_interno.fk_visita');
        });

        $visitaDados->leftJoin('controle_acesso_visita_interno_suspenso', function ($query) {
            $query->on('controle_acesso_visita_interno_suspenso.fk_visita', '=', 'visita_interno.fk_visita');
        });

        $visitaDados->leftJoin('interno', function ($query) {
            $query->on('interno.id', '=', 'visita_interno.fk_interno');
        });

        if($unidade != null) {
            $visitaDados->where('interno.unidade_prisional', $unidade);
        }else{
            $unidade = $lotacaoServidor;
            $visitaDados->where('interno.unidade_prisional', $lotacaoServidor);

        }

        $visitaDados->whereNotNull('visita.nome');

        $visitaBD = $visitaDados->select('visita.*','controle_acesso_visita_interno_suspenso.status_suspensao')->orderBy('visita.nome', 'ASC')->get();

        $visitaBD = $visitaBD->unique('cpf');

        //dd($visitaBD);

        $visita = array();
        foreach($visitaBD as $visitaItem)
        {
            $internos = InternoController::getRelacaoVisitaUnidade($visitaItem->id, $lotacaoServidor);
            $suspensaoDados = self::getSuspensaoVisita($visitaItem->cpf);
            if(count($suspensaoDados) == 0)
            {
                $suspensao = null;
            }else{
                $suspensao = $suspensaoDados;
            }

            array_push($visita, array(
                'nome' => $visitaItem->nome,
                'cpf' => $visitaItem->cpf,
                'suspensao' => $suspensao,
                'suspensaoVinculo' => $visitaItem->status_suspensao,
                'internos' => $internos,
            ));
        }

        foreach($visita as $key => $vis)
        {
            if($vis['suspensaoVinculo'] == 'A')
            {
                unset($visita[$key]);
            }
        }

        //dd($visita);

        if(!is_null($pavilhao))
        {
            $pavilhaoDados = DB::table('pavilhao')->where('fk_up', $unidade)->where('id', $pavilhao)->where('status', 'A')->orderBy('nome','ASC')->get();
        }else{
            $pavilhaoDados = DB::table('pavilhao')->where('fk_up', $unidade)->where('status', 'A')->orderBy('nome','ASC')->get();

        }
        $dadosPavilhao = array();
        $pvArray = array();
        foreach ($pavilhaoDados as $dadosPv) {
            array_push($pvArray, $dadosPv->id);
        }

        if(!is_null($ala))
        {
            $AlaDados = DB::table('ala')->whereIn('fk_pavilhao', $pvArray)->where('id', $ala)->where('status','A')->orderBy('nome','ASC')->get();

        }else{
            $AlaDados = DB::table('ala')->whereIn('fk_pavilhao', $pvArray)->where('status','A')->orderBy('nome','ASC')->get();

        }
        $dadosAla = array();
        $alaArray = array();
        foreach($AlaDados as $dAla)
        {
            array_push($alaArray, $dAla->id);
        }

        foreach($pavilhaoDados as $dPv)
        {
            array_push($dadosPavilhao, array(
                'nome' => $dPv->nome,
                'id' => $dPv->id,
                'qtdInternos' => DB::table('interno')->where('unidade_prisional', $unidade)->where('pavilhao', $dPv->id)->where('status','A')->count()
            ));
        }

        if(!is_null($cela))
        {
            $celaDados = DB::table('cela')->whereIn('fk_ala', $alaArray)->where('id', $cela)->where('status','A')->orderBy('nome','ASC')->get();

        }else{
            $celaDados = DB::table('cela')->whereIn('fk_ala', $alaArray)->where('status','A')->orderBy('nome','ASC')->get();

        }
        $dadosCela = array();
        foreach($AlaDados as $dAl)
        {
            array_push($dadosAla, array(
                'nome' => $dAl->nome,
                'id' => $dAl->id,
                'fk_pavilhao' => $dAl->fk_pavilhao,
                'qtdCelas' => DB::table('cela')->where('fk_ala', $dAl->id)->where('status','A')->orderBy('nome','ASC')->count(),
                'qtdInternos' => DB::table('interno')->where('unidade_prisional', $unidade)->where('ala', $dAl->id)->where('status','A')->count()

            ));
        }
        foreach($celaDados as $celaDado)
        {
            array_push($dadosCela, array(
                'nome' => $celaDado->nome,
                'id' => $celaDado->id,
                'fk_ala' => $celaDado->fk_ala,
                'qtdInternos' => DB::table('interno')->where('unidade_prisional', $unidade)->where('cela', $celaDado->id)->where('status','A')->count(),
//                'internos' => DB::table('interno')->where('unidade_prisional', $unidade)->where('cela', $celaDado->id)->where('status','A')->get(),

            ));
        }

        //dd($visita);

        $nomeUnidade = UnidadesController::getNomeUp($unidade);
        $nomeServidor = FacadeController::getNomeServidorCpf(Auth::user()->cpf)." (".Auth::user()->cpf.")";
        if($tipoConfere == "interno") {
            $pdf = \PDF::loadView('visita.confere.conferePdf', compact('visita', 'dadosPavilhao', 'dadosAla', 'dadosCela'))
                ->setOption('encoding', 'UTF-8')
                ->setOption('margin-bottom', 10)
                ->setOption('margin-top', 30)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('margin-bottom', 10)
                ->setOption('header-html', view('template.header.headerConfereVisitaPdf', compact('nomeUnidade')))
                ->setOption('footer-center', utf8_encode('Gerado por ' . $nomeServidor . ' em' . date('\ d/m/Y\ ') . ' | Pagina [page] de [topage]'))
                ->setOption('footer-font-size', 7)
                ->setPaper('a4');
            return $pdf->inline();
        }

        if($tipoConfere == "nome") {
            $pdf = \PDF::loadView('visita.confere.conferePdfNome', compact('visita'))
                ->setOption('encoding', 'UTF-8')
                ->setOption('margin-bottom', 10)
                ->setOption('margin-top', 30)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('margin-bottom', 10)
                ->setOption('header-html', view('template.header.headerConfereVisitaPdf', compact('nomeUnidade')))
                ->setOption('footer-center', utf8_encode('Gerado por ' . $nomeServidor . ' em' . date('\ d/m/Y\ ') . ' | Pagina [page] de [topage]'))
                ->setOption('footer-font-size', 7)
                ->setPaper('a4');
            return $pdf->inline();
        }


    }
    
    public function emitirCarteira($cpf) {
               
        if(is_null($cpf) || $cpf == "")
        {
            return redirect()->back()->withInput()->with('alert-danger', 'Visitante sem CPF cadastrado');
        }
        
        $dadosVisita =        self::getDadosVisitaCpf($cpf);
        $dados = array();
        
        foreach($dadosVisita as $dVisita)
        {
            $foto =                    \App\Http\Controllers\Foto\FotoController::getFotoPrincipalByIdTipo($dVisita->id, 'visita');
            if(is_null($foto) || $foto == "" || $foto == "null")
            {
                return redirect()->back()->withInput()->with('alert-danger', 'Visitante sem FOTO cadastrada');
            }
            
            $internosVinculo = array();
            $internosSuspensos = DB::table('controle_acesso_visita_interno_suspenso')->where('fk_visita', $dVisita->id)->where('status_suspensao', 'A')->pluck('fk_interno');
            $internos =  DB::table('visita_interno')->where('fk_visita', $dVisita->id)->whereNotIn('fk_interno', $internosSuspensos)->where('status', 'A')->get();
            if(count($internos) == 0)
            {
                return redirect()->back()->withInput()->with('alert-danger', 'Visitante sem vículo com interno ativo');
            }
            
            foreach($internos as $interno)
            {
                array_push($internosVinculo, array(
                    'nome' => InternoController::getInternoNome($interno->fk_interno),
                    'prontuario' => $interno->fk_interno,
                    'parentesco' => $interno->grau_parentesco
                ));
            }
            
            $codBar = DNS1D::getBarcodeSVG($cpf, 'C39');
            
            $nomeChefeAssinatura = null;
            $nomeChefeAssinaturaDados = DB::connection('mysql2')
            ->table('servidors')
            ->select('servidors.nome')
            ->where('servidors.funcao',46)
            ->get();
            if(count($nomeChefeAssinaturaDados) > 0)
            {
                $nomeChefeAssinatura = $nomeChefeAssinaturaDados[0];
                
            }else{
                return redirect()->back()->withInput()->with('alert-danger', 'Deve ser atribuído no sistema de RH a função de Chefe de Departamento de Reintegração');
            }
            
            array_push($dados, array(
                'id' => $dVisita->id,
                'nome' => $dVisita->nome,
                'responsavel' => DB::table('visita')->where('id', $dVisita->responsavel)->get(),
                'validade' => date("d/m/Y", strtotime($dVisita->validade_carteira)),
                'cpf' => \App\Classes\Util::formata($dVisita->cpf),
                'internos' => $internosVinculo,
                'pne' => $dVisita->pne,
                'codBar' => $codBar,
                'chefeAssinatura' => $nomeChefeAssinatura->nome,
            ));
        }
        //dd($dados);
        $dados = $dados[0];
        //dd($dados['responsavel'][0]->nome);
        //dd($dados['responsavel']);
        
            $pdf = \PDF::loadView('visita.carteira.carteiraPdf', compact('dados'))
                ->setOption('encoding', 'UTF-8')
                ->setOption('margin-bottom', 10)
                ->setOption('margin-top', 30)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10)
                ->setOption('margin-bottom', 10)
                ->setOption('footer-font-size', 7)
                ->setPaper('a4');
            return $pdf->inline();
    }
}
