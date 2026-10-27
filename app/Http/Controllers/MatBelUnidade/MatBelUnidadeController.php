<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a Escolta
|
*/

namespace App\Http\Controllers\MatBelUnidade;

use App\Classes\Tdata;
use App\Http\Controllers\Armaria\ArmariaController;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Unidades\UnidadesController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MatBelUnidadeController extends Controller
{
    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');

    }

    public function callAction($method, $parameters)
    {
        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::getVerifyPermissionController("MatBelUnidadeController");
        
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }


//        $metodosControlador = collect(['estoqueUnidade']);
//        $metodosComum = collect(['usoMaterial','listarUsoMaterial','visualizarRelatorioUsoMaterial']);
////
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
//        if($metodosComum->contains($method))
//        {
//            if (Auth::user()->tipo < 300) {
//
//                return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//            }
//
//        }

        return parent::callAction($method, $parameters);
    }

    public function usoMaterial()
    {
        return view('matBelUnidade.usoMaterial.fichaUsoDeMaterial');
    }

    public function UsoMaterialStore(Request $request)
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        /**
         * sempre será 1 servidor, o uso é individual
         */
        $servidor = $request->get('agentesHidden');

        $insert = DB::table('armaria_uso_material_unidade')->insert([
            'unidade' => $lotacaoServidor,
            'servidor' => $servidor[0],
            'relatorio' => $request->get('relatorio'),
            'created_at' => Tdata::TtimeStampHojeMysql(),
        ]);

        $idInsert = DB::getPdo()->lastInsertId();

        $equipamentos = $request->get('hiddenEquipamentoCautela');

        foreach($equipamentos as $equipamento)
        {
            $equip = explode('/', $equipamento);

            $insertEquipamento = DB::table('armaria_uso_material_unidade_equipamento')->insert([
                    'fk_relatorio_uso' => $idInsert,
                    'fk_equipamento' => $equip[0],
                    'quantidade' => $equip[1],
                ]
            );

        }

        LogController::log($idInsert,"armaria_uso_material_unidade","add", Auth::user()->cpf, "Inserção relatório de uso de material da Unidade<br>Unidade: $lotacaoServidor<br>Servidor: $servidor[0]");

        if ($idInsert) {

            return redirect()->route('matBelUnidade.listarUsoMaterial')->with('alert-success', 'Relatório inserido com sucesso!');

        }else{

            return redirect()->back()->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }

    }

    public function listarUsoMaterial()
    {
        $lotacaoServidor = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $dadosUso = DB::table('armaria_uso_material_unidade')->where('unidade', $lotacaoServidor)->orderBy('created_at','DESC')->get();

        $dados = array();

        foreach($dadosUso as $dU)
        {
            array_push($dados, array(
                'id' => $dU->id,
                'data' => Tdata::TtimestampBR($dU->created_at),
                'servidor' => FacadeController::getNomeServidor($dU->servidor),
                'matricula' => $dU->servidor,
                'unidade' => UnidadesController::getNomeUp($dU->unidade),
            ));
        }

        $dados = json_decode(json_encode($dados), FALSE);

        return view('matBelUnidade.usoMaterial.listarUsoDeMaterial', compact('dados'));
    }

    public function visualizarRelatorioUsoMaterial($id)
    {
        $equipamentos = DB::table('armaria_uso_material_unidade_equipamento')
            ->leftJoin('armaria_uso_material_unidade','armaria_uso_material_unidade.id','=','armaria_uso_material_unidade_equipamento.fk_relatorio_uso')
            ->leftJoin('armaria_equipamento','armaria_equipamento.id','=','armaria_uso_material_unidade_equipamento.fk_equipamento')
            ->leftJoin('armaria_equipamento_fabricante','armaria_equipamento_fabricante.id','=','armaria_equipamento.fk_fabricante')
            ->where('armaria_uso_material_unidade_equipamento.fk_relatorio_uso', $id)
            ->select('armaria_uso_material_unidade_equipamento.*','armaria_equipamento.*','armaria_equipamento_fabricante.nome AS fabNome','armaria_uso_material_unidade.*','armaria_uso_material_unidade.created_at AS dataCadastro')
            ->get();

        $dados = array();

        foreach($equipamentos as $equipamento)
        {
            if($equipamento->equipamento_tipo == "municao")
            {
                if($equipamento->quantidade == 1)
                {
                    $qtd = $equipamento->quantidade." Munição - ";
                }else{
                    $qtd = $equipamento->quantidade." Munições - ";
                }

                $texto = $qtd . $equipamento->modelo. " - ".$equipamento->equipamento_especie."(".$equipamento->equipamento_especificacao.") / Calibre: ".$equipamento->calibre." / ".$equipamento->fabNome;
            }

            if($equipamento->equipamento_tipo == "granada")
            {
                if($equipamento->quantidade == 1)
                {
                    $qtd = $equipamento->quantidade." Granada - ";
                }else{
                    $qtd = $equipamento->quantidade." Granadas - ";
                }

                $texto = $qtd . $equipamento->equipamento_especificacao. " - ".$equipamento->modelo." / ".$equipamento->fabNome;
            }

            if($equipamento->equipamento_tipo == "espargidor")
            {

                if($equipamento->quantidade == 1)
                {
                    $qtd = $equipamento->quantidade." Espargidor - ";

                }else{

                    $qtd = $equipamento->quantidade." Espargidores - ";
                }

                $texto = $qtd . $equipamento->modelo. " - Capacidade: ".$equipamento->volume_peso." / ".$equipamento->fabNome;
            }

            array_push($dados, array(
               'quantidade' => $equipamento->quantidade,
                'textoEquipamento' => mb_strtoupper($texto),
                'data' => Tdata::TtimestampBR($equipamento->dataCadastro),
                'servidor' => FacadeController::getNomeServidor($equipamento->servidor),
                'matricula' => $equipamento->servidor,
                'unidade' => UnidadesController::getNomeUp($equipamento->unidade),
                'relatorio' => $equipamento->relatorio,
            ));
        }

        $dados = json_decode(json_encode($dados), FALSE);

        return view('matBelUnidade.usoMaterial.visualizarRelatorio', compact('dados'));

    }

    public function estoqueUnidade()
    {

        $unidadePrisional = UnidadesController::getIdUpPorSigla(FacadeController::getLotacaoServidor(Auth::user()->cpf));

        $armaFogo = DB::table('armaria_cautela')
            ->leftJoin('armaria_cautela_equipamento', 'armaria_cautela_equipamento.id_cautela', '=', 'armaria_cautela.id')
            ->leftJoin('armaria_equipamento', 'armaria_equipamento.id', '=', 'armaria_cautela_equipamento.id_equipamento')
            ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
            ->where('armaria_cautela.tipo_cautela', 'unidade')
            ->where('armaria_equipamento.equipamento_tipo', 'armaFogo')
            ->where('armaria_cautela.unidade_prisional', $unidadePrisional)
            ->select('armaria_cautela.*', 'armaria_equipamento.*', 'armaria_cautela_equipamento.*', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome')
            ->get();

        $dei = DB::table('armaria_cautela')
            ->leftJoin('armaria_cautela_equipamento', 'armaria_cautela_equipamento.id_cautela', '=', 'armaria_cautela.id')
            ->leftJoin('armaria_equipamento', 'armaria_equipamento.id', '=', 'armaria_cautela_equipamento.id_equipamento')
            ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
            ->where('armaria_cautela.tipo_cautela', 'unidade')
            ->where('armaria_equipamento.equipamento_tipo', 'dei')
            ->where('armaria_cautela.unidade_prisional', $unidadePrisional)
            ->select('armaria_cautela.*', 'armaria_equipamento.*', 'armaria_cautela_equipamento.*', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome')
            ->get();

        $municaoDados = DB::table('armaria_cautela')
            ->leftJoin('armaria_cautela_equipamento', 'armaria_cautela_equipamento.id_cautela', '=', 'armaria_cautela.id')
            ->leftJoin('armaria_equipamento', 'armaria_equipamento.id', '=', 'armaria_cautela_equipamento.id_equipamento')
            ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
            ->where('armaria_cautela.tipo_cautela', 'unidade')
            ->where('armaria_equipamento.equipamento_tipo', 'municao')
            ->where('armaria_cautela.unidade_prisional', $unidadePrisional)
            ->select('armaria_cautela.*', 'armaria_equipamento.*', 'armaria_equipamento.id AS idEquipamento', 'armaria_cautela_equipamento.*', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome')
            ->get();

        $municao = array();
        foreach ($municaoDados as $municaoDado) {

            //junta os valores de equipamentos iguais (quando tiver a ficha de uso, subtrair os valores)
            $id = $municaoDado->idEquipamento;
            $qtdUsoMaterialUnidade = ArmariaController::getQuantidadeUsoMaterialUnidade($id, $unidadePrisional);
            $key = array_search($id, array_column($municao, 'idEquipamento'));
            if ($key !== false) {
                $municao[$key]['quantidade'] = $municao[$key]['quantidade'] + $municaoDado->quantidade;
            } else {

                array_push($municao, array(
                    'idEquipamento' => $municaoDado->idEquipamento,
                    'equipamento_especie' => $municaoDado->equipamento_especie,
                    'equipamento_especificacao' => $municaoDado->equipamento_especificacao,
                    'fabNome' => $municaoDado->fabNome,
                    'modelo' => $municaoDado->modelo,
                    'calibre' => $municaoDado->calibre,
                    'quantidade' => $municaoDado->quantidade - $qtdUsoMaterialUnidade,
                ));
            }
        }

        $municao = json_decode(json_encode($municao), FALSE);

        $carregadorDados = DB::table('armaria_cautela')
            ->leftJoin('armaria_cautela_equipamento', 'armaria_cautela_equipamento.id_cautela', '=', 'armaria_cautela.id')
            ->leftJoin('armaria_equipamento', 'armaria_equipamento.id', '=', 'armaria_cautela_equipamento.id_equipamento')
            ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
            ->where('armaria_cautela.tipo_cautela', 'unidade')
            ->where('armaria_equipamento.equipamento_tipo', 'carregador')
            ->where('armaria_cautela.unidade_prisional', $unidadePrisional)
            ->select('armaria_cautela.*', 'armaria_equipamento.*', 'armaria_equipamento.id AS idEquipamento', 'armaria_cautela_equipamento.*', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome')
            ->get();

        $carregador = array();
        foreach ($carregadorDados as $carregadorDado) {

            $id = $carregadorDado->idEquipamento;
            $key = array_search($id, array_column($carregador, 'idEquipamento'));
            if ($key !== false) {
                $carregador[$key]['quantidade'] = $carregador[$key]['quantidade'] + $carregadorDado->quantidade;
            } else {

                array_push($carregador, array(
                    'idEquipamento' => $carregadorDado->idEquipamento,
                    'equipamento_tipo' => $carregadorDado->equipamento_tipo,
                    'equipamento_especie' => $carregadorDado->equipamento_especie,
                    'armamento' => $carregadorDado->armamento,
                    'calibre' => $carregadorDado->calibre,
                    'modelo' => $carregadorDado->modelo,
                    'volume_peso' => $carregadorDado->volume_peso,
                    'quantidade' => $carregadorDado->quantidade,
                ));
            }
        }

        $carregador = json_decode(json_encode($carregador), FALSE);

        $algemasDados = DB::table('armaria_cautela')
            ->leftJoin('armaria_cautela_equipamento', 'armaria_cautela_equipamento.id_cautela', '=', 'armaria_cautela.id')
            ->leftJoin('armaria_equipamento', 'armaria_equipamento.id', '=', 'armaria_cautela_equipamento.id_equipamento')
            ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
            ->where('armaria_cautela.tipo_cautela', 'unidade')
            ->where('armaria_equipamento.equipamento_tipo', 'algema')
            ->where('armaria_cautela.unidade_prisional', $unidadePrisional)
            ->select('armaria_cautela.*', 'armaria_equipamento.*', 'armaria_equipamento.id AS idEquipamento', 'armaria_cautela_equipamento.*', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome')
            ->get();

        $algemas = array();
        foreach ($algemasDados as $algemasDado) {

            $id = $algemasDado->idEquipamento;
            $key = array_search($id, array_column($algemas, 'idEquipamento'));
            if ($key !== false) {
                $algemas[$key]['quantidade'] = $algemas[$key]['quantidade'] + $algemasDado->quantidade;
            } else {

                array_push($algemas, array(
                    'idEquipamento' => $algemasDado->idEquipamento,
                    'n_serie' => $algemasDado->n_serie,
                    'equipamento_tipo' => $algemasDado->equipamento_tipo,
                    'equipamento_especie' => $algemasDado->equipamento_especie,
                    'fabNome' => $algemasDado->fabNome,
                    'quantidade' => $algemasDado->quantidade,
                ));
            }
        }

        $algemas = json_decode(json_encode($algemas), FALSE);

        $granadasDados = DB::table('armaria_cautela')
            ->leftJoin('armaria_cautela_equipamento', 'armaria_cautela_equipamento.id_cautela', '=', 'armaria_cautela.id')
            ->leftJoin('armaria_equipamento', 'armaria_equipamento.id', '=', 'armaria_cautela_equipamento.id_equipamento')
            ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
            ->where('armaria_cautela.tipo_cautela', 'unidade')
            ->where('armaria_equipamento.equipamento_tipo', 'granada')
            ->where('armaria_cautela.unidade_prisional', $unidadePrisional)
            ->select('armaria_cautela.*', 'armaria_equipamento.*', 'armaria_equipamento.id AS idEquipamento', 'armaria_cautela_equipamento.*', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome')
            ->get();

        $granadas = array();
        foreach ($granadasDados as $granadasDado) {

            $id = $granadasDado->idEquipamento;
            $qtdUsoMaterialUnidade = ArmariaController::getQuantidadeUsoMaterialUnidade($id, $unidadePrisional);
            $key = array_search($id, array_column($granadas, 'idEquipamento'));
            if ($key !== false) {
                $granadas[$key]['quantidade'] = $granadas[$key]['quantidade'] + $granadasDado->quantidade;
            } else {

                array_push($granadas, array(
                    'idEquipamento' => $granadasDado->idEquipamento,
                    'equipamento_tipo' => $granadasDado->equipamento_tipo,
                    'equipamento_especie' => $granadasDado->equipamento_especie,
                    'equipamento_especificacao' => $granadasDado->equipamento_especificacao,
                    'fabNome' => $granadasDado->fabNome,
                    'modelo' => $granadasDado->modelo,
                    'quantidade' => $granadasDado->quantidade - $qtdUsoMaterialUnidade,
                ));
            }
        }

        $granadas = json_decode(json_encode($granadas), FALSE);

        $epiDados = DB::table('armaria_cautela')
            ->leftJoin('armaria_cautela_equipamento', 'armaria_cautela_equipamento.id_cautela', '=', 'armaria_cautela.id')
            ->leftJoin('armaria_equipamento', 'armaria_equipamento.id', '=', 'armaria_cautela_equipamento.id_equipamento')
            ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
            ->where('armaria_cautela.tipo_cautela', 'unidade')
            ->where('armaria_equipamento.equipamento_tipo', 'epi')
            ->where('armaria_cautela.unidade_prisional', $unidadePrisional)
            ->select('armaria_cautela.*', 'armaria_equipamento.*', 'armaria_equipamento.id AS idEquipamento', 'armaria_cautela_equipamento.*', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome')
            ->get();

        $epis = array();
        foreach ($epiDados as $epiDado) {

            $id = $epiDado->idEquipamento;
            $key = array_search($id, array_column($epis, 'idEquipamento'));
            if ($key !== false) {
                $epis[$key]['quantidade'] = $epis[$key]['quantidade'] + $epiDado->quantidade;
            } else {

                array_push($epis, array(
                    'idEquipamento' => $epiDado->idEquipamento,
                    'n_serie' => $epiDado->n_serie,
                    'equipamento_especie' => $epiDado->equipamento_especie,
                    'fabNome' => $epiDado->fabNome,
                    'quantidade' => $epiDado->quantidade,
                ));
            }
        }

        $epis = json_decode(json_encode($epis), FALSE);

        $coleteDados = DB::table('armaria_cautela')
            ->leftJoin('armaria_cautela_equipamento', 'armaria_cautela_equipamento.id_cautela', '=', 'armaria_cautela.id')
            ->leftJoin('armaria_equipamento', 'armaria_equipamento.id', '=', 'armaria_cautela_equipamento.id_equipamento')
            ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
            ->where('armaria_cautela.tipo_cautela', 'unidade')
            ->where('armaria_equipamento.equipamento_tipo', 'colete')
            ->where('armaria_cautela.unidade_prisional', $unidadePrisional)
            ->select('armaria_cautela.*', 'armaria_equipamento.*', 'armaria_equipamento.id AS idEquipamento', 'armaria_cautela_equipamento.*', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome')
            ->get();

        $coletes = array();
        foreach ($coleteDados as $coleteDado) {

            array_push($coletes, array(
                'idEquipamento' => $coleteDado->idEquipamento,
                'n_serie' => $coleteDado->n_serie,
                'equipamento_tipo' => $coleteDado->equipamento_tipo,
                'fabNome' => $coleteDado->fabNome,
                'tamanho' => $coleteDado->tamanho,
            ));
        }

        $coletes = json_decode(json_encode($coletes), FALSE);

        $espargidorDados = DB::table('armaria_cautela')
            ->leftJoin('armaria_cautela_equipamento', 'armaria_cautela_equipamento.id_cautela', '=', 'armaria_cautela.id')
            ->leftJoin('armaria_equipamento', 'armaria_equipamento.id', '=', 'armaria_cautela_equipamento.id_equipamento')
            ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
            ->where('armaria_cautela.tipo_cautela', 'unidade')
            ->where('armaria_equipamento.equipamento_tipo', 'espargidor')
            ->where('armaria_cautela.unidade_prisional', $unidadePrisional)
            ->select('armaria_cautela.*', 'armaria_equipamento.*', 'armaria_equipamento.id AS idEquipamento', 'armaria_cautela_equipamento.*', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome')
            ->get();

        $espargidores = array();
        foreach ($espargidorDados as $espargidorDado) {

            $id = $espargidorDado->idEquipamento;
            $qtdUsoMaterialUnidade = ArmariaController::getQuantidadeUsoMaterialUnidade($id, $unidadePrisional);
            $key = array_search($id, array_column($espargidores, 'idEquipamento'));
            if ($key !== false) {
                $espargidores[$key]['quantidade'] = $espargidores[$key]['quantidade'] + $espargidorDado->quantidade;
            } else {

                array_push($espargidores, array(
                    'idEquipamento' => $espargidorDado->idEquipamento,
                    'equipamento_tipo' => $espargidorDado->equipamento_tipo,
                    'modelo' => $espargidorDado->modelo,
                    'fabNome' => $espargidorDado->fabNome,
                    'quantidade' => $espargidorDado->quantidade - $qtdUsoMaterialUnidade,
                ));
            }
        }

        $espargidores = json_decode(json_encode($espargidores), FALSE);

        $dados = true;


        return view('matBelUnidade.estoque.estoque', compact('armaFogo','dei','municao','carregador','algemas','granadas','epis','coletes','espargidores','dados'));


    }
}