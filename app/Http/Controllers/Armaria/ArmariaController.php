<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com / geovanni_ti@hotmail.com
|--------------------------------------------------------------------------
|
| Classe com metodos relacionados a Armaria
|
*/

namespace App\Http\Controllers\Armaria;

use App\Classes\Constantes;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Http\Controllers\Permissoes\PermissoesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Classes\Tdata;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ArmariaController extends Controller
{
    public function __construct()
    {
        //date_default_timezone_set('America/Fortaleza');
        $this->middleware('auth');

    }
    
    public function callAction($method, $parameters)
    {
        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::verifyPermission("ArmariaController", $method);
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

        return parent::callAction($method, $parameters);
    }

    public static function getEquipamentoCautelado($idCautela)
    {
        $dadosEquipamentoCautela = array();
        $dadosEquipamento = DB::table('armaria_cautela_equipamento')->where('id_cautela', $idCautela)->get();

        foreach($dadosEquipamento as $item) {

            $equipamento = DB::table('armaria_equipamento')->where('id', $item->id_equipamento)->select('equipamento_tipo')->get()[0]->equipamento_tipo;

            if ($equipamento == "armaFogo") {

                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $item->id_equipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('calibre', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $dados[0]->equipamento_especificacao . ' ' . $dados[0]->calibre . " - " . $dados[0]->modelo." | Nº de Série: ". $dados[0]->n_serie." | Fabricante: ". $dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "quantidade" => $item->quantidade,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,
                ));

            }

            if ($equipamento == "dei") {

                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $item->id_equipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('calibre', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $dados[0]->equipamento_especificacao .  " - " . $dados[0]->modelo." | Nº de Série: ". $dados[0]->n_serie." | Fabricante: ". $dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "quantidade" => $item->quantidade,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,
                ));

            }

            if ($equipamento == "municao") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $item->id_equipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('armaria_equipamento.modelo', 'ASC')->orderBy('calibre', 'ASC')->get();


                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $item->quantidade." Munições - ". $dados[0]->modelo." - (". $dados[0]->equipamento_especificacao.") - .". $dados[0]->calibre." - ". $dados[0]->equipamento_especie." - ".$dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "quantidade" => $item->quantidade,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));


            }

            if ($equipamento == "carregador") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $item->id_equipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.modelo', 'ASC')->orderBy('calibre', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $item->quantidade." Carregador(es) - ". $dados[0]->modelo." - .". $dados[0]->calibre." - ". $dados[0]->equipamento_especie." - Capacidade: ".$dados[0]->volume_peso."Mun. - ".$dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "quantidade" => $item->quantidade,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));


            }

            if ($equipamento == "algema") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $item->id_equipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especificacao', 'ASC')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $item->quantidade. " Algema(s) - " . $dados[0]->equipamento_especificacao. " - ". $dados[0]->equipamento_especie . "  - ".$dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "quantidade" => $item->quantidade,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));

            }

            if ($equipamento == "granada") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $item->id_equipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.modelo', 'ASC')->orderBy('calibre', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $item->quantidade. " Granada(s) - " .$dados[0]->equipamento_especificacao." - ". $dados[0]->equipamento_especie." - ".$dados[0]->modelo." - ".$dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "quantidade" => $item->quantidade,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));



            }

            if ($equipamento == "epi") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $item->id_equipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.modelo', 'ASC')->orderBy('calibre', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $dados[0]->equipamento_especie,
                    "id" => $dados[0]->id,
                    "quantidade" => $item->quantidade,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));


            }

            if ($equipamento == "colete") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->leftJoin('armaria_cautela_equipamento', function ($query) {
                    $query->on('armaria_cautela_equipamento.id_equipamento', '=', 'armaria_equipamento.id');
                });

                $select->where('armaria_equipamento.id', $item->id_equipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('tamanho', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => "Colete - Tamanho ". $dados[0]->tamanho." | Nº de Série: ". $dados[0]->n_serie." | Fabricante: ". $dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "quantidade" => $item->quantidade,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));

            }

            if ($equipamento == "espargidor") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $item->id_equipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('modelo', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $item->quantidade. " Espargidor(es) - " .$dados[0]->modelo ." - Capacidade: ". $dados[0]->volume_peso,
                    "id" => $dados[0]->id,
                    "quantidade" => $item->quantidade,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));


            }
        }

        return $dadosEquipamentoCautela;

    }

    public static function getEquipamentoDescautelaForm($idEquipamento, $quantidade)
    {
        $dadosEquipamentoCautela = array();


            $equipamento = DB::table('armaria_equipamento')->where('id', $idEquipamento)->select('equipamento_tipo')->get()[0]->equipamento_tipo;

            if ($equipamento == "armaFogo") {

                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $idEquipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('calibre', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $dados[0]->equipamento_especificacao . ' ' . $dados[0]->calibre . " - " . $dados[0]->modelo." | Nº de Série: ". $dados[0]->n_serie." | Fabricante: ". $dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "quantidade" => $quantidade,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,
                ));

            }

            if ($equipamento == "dei") {

                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $idEquipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('calibre', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $dados[0]->equipamento_especificacao . " - " . $dados[0]->modelo." | Nº de Série: ". $dados[0]->n_serie." | Fabricante: ". $dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "quantidade" => $quantidade,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,
                ));

            }

            if ($equipamento == "municao") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $idEquipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('armaria_equipamento.modelo', 'ASC')->orderBy('calibre', 'ASC')->get();


                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $quantidade." Munições - ". $dados[0]->modelo." - (". $dados[0]->equipamento_especificacao.") - .". $dados[0]->calibre." - ". $dados[0]->equipamento_especie." - ".$dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "quantidade" => $quantidade,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));


            }

            if ($equipamento == "carregador") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $idEquipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.modelo', 'ASC')->orderBy('calibre', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $quantidade." Carregador(es) - ". $dados[0]->modelo." - .". $dados[0]->calibre." - ". $dados[0]->equipamento_especie." - Capacidade: ".$dados[0]->volume_peso."Mun. - ".$dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "quantidade" => $quantidade,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));


            }

            if ($equipamento == "algema") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $idEquipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especificacao', 'ASC')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $quantidade. " Algema(s) - " . $dados[0]->equipamento_especificacao. " - ". $dados[0]->equipamento_especie . "  - ".$dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "quantidade" => $quantidade,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));

            }

            if ($equipamento == "granada") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $idEquipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.modelo', 'ASC')->orderBy('calibre', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $quantidade. " Granada(s) - " .$dados[0]->equipamento_especificacao." - ". $dados[0]->equipamento_especie." - ".$dados[0]->modelo." - ".$dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "quantidade" => $quantidade,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));



            }

            if ($equipamento == "epi") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $idEquipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.modelo', 'ASC')->orderBy('calibre', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $dados[0]->equipamento_especie,
                    "id" => $dados[0]->id,
                    "quantidade" => $quantidade,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));


            }

            if ($equipamento == "colete") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->leftJoin('armaria_cautela_equipamento', function ($query) {
                    $query->on('armaria_cautela_equipamento.id_equipamento', '=', 'armaria_equipamento.id');
                });

                $select->where('armaria_equipamento.id', $idEquipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('tamanho', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => "Colete - Tamanho ". $dados[0]->tamanho." | Nº de Série: ". $dados[0]->n_serie." | Fabricante: ". $dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "quantidade" => $quantidade,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));

            }

            if ($equipamento == "espargidor") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $idEquipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('modelo', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $quantidade. " Espargidor(es) - " .$dados[0]->modelo ." - Capacidade: ". $dados[0]->volume_peso,
                    "id" => $dados[0]->id,
                    "quantidade" => $quantidade,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));


            }

        return $dadosEquipamentoCautela;

    }

    public static function getTextoEquipamento ($idEquipamento)
    {
        $dadosEquipamentoCautela = array();


            $equipamento = DB::table('armaria_equipamento')->where('id', $idEquipamento)->select('equipamento_tipo')->get()[0]->equipamento_tipo;

            if ($equipamento == "armaFogo") {

                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $idEquipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('calibre', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $dados[0]->equipamento_especificacao . ' ' . $dados[0]->calibre . " - " . $dados[0]->modelo." | Nº de Série: ". $dados[0]->n_serie." | Fabricante: ". $dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,
                ));

            }

            if ($equipamento == "dei") {

                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $idEquipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('calibre', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $dados[0]->equipamento_especificacao . " - " . $dados[0]->modelo." | Nº de Série: ". $dados[0]->n_serie." | Fabricante: ". $dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,
                ));

            }

            if ($equipamento == "municao") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $idEquipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('armaria_equipamento.modelo', 'ASC')->orderBy('calibre', 'ASC')->get();


                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => "Munição - ". $dados[0]->modelo." - (". $dados[0]->equipamento_especificacao.") - .". $dados[0]->calibre." - ". $dados[0]->equipamento_especie." - ".$dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));


            }

            if ($equipamento == "carregador") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $idEquipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.modelo', 'ASC')->orderBy('calibre', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => "Carregador - ". $dados[0]->modelo." - .". $dados[0]->calibre." - ". $dados[0]->equipamento_especie." - Capacidade: ".$dados[0]->volume_peso."Mun. - ".$dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));


            }

            if ($equipamento == "algema") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $idEquipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especificacao', 'ASC')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => "Algema - " . $dados[0]->equipamento_especificacao. " - ". $dados[0]->equipamento_especie . "  - ".$dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));

            }

            if ($equipamento == "granada") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $idEquipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.modelo', 'ASC')->orderBy('calibre', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => "Granada - " .$dados[0]->equipamento_especificacao." - ". $dados[0]->equipamento_especie." - ".$dados[0]->modelo." - ".$dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));



            }

            if ($equipamento == "epi") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $idEquipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.modelo', 'ASC')->orderBy('calibre', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $dados[0]->equipamento_especie,
                    "id" => $dados[0]->id,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));


            }

            if ($equipamento == "colete") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->leftJoin('armaria_cautela_equipamento', function ($query) {
                    $query->on('armaria_cautela_equipamento.id_equipamento', '=', 'armaria_equipamento.id');
                });

                $select->where('armaria_equipamento.id', $idEquipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('tamanho', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => "Colete - Tamanho ". $dados[0]->tamanho." | Nº de Série: ". $dados[0]->n_serie." | Fabricante: ". $dados[0]->fabNome,
                    "id" => $dados[0]->id,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));

            }

            if ($equipamento == "espargidor") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $idEquipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('modelo', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => "Espargidor - " .$dados[0]->modelo ." - Capacidade: ". $dados[0]->volume_peso,
                    "id" => $dados[0]->id,
                    "tipoEquipamento" => $dados[0]->equipamento_tipo,

                ));


            }

        return $dadosEquipamentoCautela;

    }

    public static function getEquipamentoDescautelado($idDescautela)
    {
        $dadosEquipamentoCautela = array();
        $dadosEquipamento = DB::table('armaria_historico_cautela_equipamento')->where('id_cautela', $idDescautela)->get();

        foreach($dadosEquipamento as $item) {

            $equipamento = DB::table('armaria_equipamento')->where('id', $item->id_equipamento)->select('equipamento_tipo')->get()[0]->equipamento_tipo;

            if ($equipamento == "armaFogo") {

                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $item->id_equipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('calibre', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $dados[0]->equipamento_especificacao . ' ' . $dados[0]->calibre . " - " . $dados[0]->modelo." | Nº de Série: ". $dados[0]->n_serie." | Fabricante: ". $dados[0]->fabNome,
                ));

            }

            if ($equipamento == "municao") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $item->id_equipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('armaria_equipamento.modelo', 'ASC')->orderBy('calibre', 'ASC')->get();


                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $item->quantidade." Munições - ". $dados[0]->modelo." - (". $dados[0]->equipamento_especificacao.") - .". $dados[0]->calibre." - ". $dados[0]->equipamento_especie." - ".$dados[0]->fabNome,
                ));


            }

            if ($equipamento == "carregador") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $item->id_equipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.modelo', 'ASC')->orderBy('calibre', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $item->quantidade." Carregador(es) - ". $dados[0]->modelo." - .". $dados[0]->calibre." - ". $dados[0]->equipamento_especie." - Capacidade: ".$dados[0]->volume_peso."Mun. - ".$dados[0]->fabNome,
                ));


            }

            if ($equipamento == "algema") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $item->id_equipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especificacao', 'ASC')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $item->quantidade. " Algema(s) - " . $dados[0]->equipamento_especificacao. " - ". $dados[0]->equipamento_especie . "  - ".$dados[0]->fabNome,
                ));

            }

            if ($equipamento == "granada") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $item->id_equipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.modelo', 'ASC')->orderBy('calibre', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $item->quantidade. " Granada(s) - " .$dados[0]->equipamento_especificacao." - ". $dados[0]->equipamento_especie." - ".$dados[0]->modelo." - ".$dados[0]->fabNome,
                ));



            }

            if ($equipamento == "epi") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $item->id_equipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.modelo', 'ASC')->orderBy('calibre', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $dados[0]->equipamento_especie,
                ));


            }

            if ($equipamento == "colete") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->leftJoin('armaria_cautela_equipamento', function ($query) {
                    $query->on('armaria_cautela_equipamento.id_equipamento', '=', 'armaria_equipamento.id');
                });

                $select->where('armaria_equipamento.id', $item->id_equipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('tamanho', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => "Colete - Tamanho ". $dados[0]->tamanho." | Nº de Série: ". $dados[0]->n_serie." | Fabricante: ". $dados[0]->fabNome,
                ));

            }

            if ($equipamento == "espargidor") {
                $select = DB::table('armaria_equipamento');

                $select->leftJoin('armaria_equipamento_fabricante', function ($query) {
                    $query->on('armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante');
                });

                $select->where('armaria_equipamento.id', $item->id_equipamento)->get();

                $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('modelo', 'ASC')->get();

                array_push($dadosEquipamentoCautela, array(
                    "equipamento" => $item->quantidade. " Espargidor(es) - " .$dados[0]->modelo ." - Capacidade: ". $dados[0]->volume_peso,
                ));


            }
        }

        return $dadosEquipamentoCautela;

    }

    public static function getQuantidadeUsoMaterialUnidade($idEquipamento, $unidade)
    {
        $dados = DB::table('armaria_uso_material_unidade_equipamento')
            ->leftJoin('armaria_uso_material_unidade', 'armaria_uso_material_unidade.id', '=', 'armaria_uso_material_unidade_equipamento.fk_relatorio_uso')
            ->where('armaria_uso_material_unidade_equipamento.fk_equipamento', $idEquipamento)
            ->where('armaria_uso_material_unidade.unidade', $unidade)
            ->sum('quantidade');

        //dd($dados);
        return $dados;
    }
    public static function quantidadeEquipamento($id)
    {
//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }

        $dado = DB::table('armaria_equipamento_quantidade')->where('fk_equipamento', $id)->get();


        if(count($dado) > 0){
            return $dado[0]->quantidade;
        }else{
            return 0;
        }

    }


    public static function estoqueEquipamento($id)
    {
//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }

        $dado = DB::table('armaria_cautela_equipamento')->where('id_equipamento',$id)->get();

        $quantidadeCautelada = 0;

        foreach($dado as $d)
        {
            $quantidadeCautelada = $quantidadeCautelada + $d->quantidade;
        }
        $estoque = self::quantidadeEquipamento($id) - $quantidadeCautelada;

        return $estoque;
    }

    public function buscaEquipamentoUnidadeAjax(Request $request)
    {
        //dd($request->all());

        $equipamento = $request->get('equipamento');
        $equipamentoEspecie = $request->get('especie');


        $lotacaoServidor = FacadeController::getLotacaoServidor(Auth::user()->cpf);

        $idUnidadeServidor = UnidadesController::getIdUpPorSigla($lotacaoServidor);

        $select = DB::table('armaria_cautela_unidade');

        $select->leftJoin('armaria_cautela_equipamento', 'armaria_cautela_equipamento.id_cautela', '=', 'armaria_cautela_unidade.fk_cautela');

        $select->leftJoin('armaria_equipamento', 'armaria_equipamento.id', '=', 'armaria_cautela_equipamento.id_equipamento');

        $select->leftJoin('armaria_equipamento_fabricante',function($query){
            $query->on('armaria_equipamento_fabricante.id','=','armaria_equipamento.fk_fabricante');
        });

        $select->where('armaria_cautela_unidade.unidade', $idUnidadeServidor);

        if($equipamento == "armaFogo") {

            $select->where('armaria_equipamento.equipamento_tipo', $equipamento)
                ->where('armaria_equipamento.status', 'A')
                ->where('armaria_equipamento.equipamento_especie', $equipamentoEspecie);

            $dados = $select->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('calibre', 'ASC')->get();

            return $dados;

        }
        if($equipamento == "municao") {

            $select->where('armaria_equipamento.equipamento_tipo', $equipamento)->where('armaria_equipamento.equipamento_especie', $equipamentoEspecie);

            $dados = $select->select('armaria_equipamento.*','armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie','ASC')->orderBy('calibre', 'ASC')->get();

            $dados = $dados->unique('modelo');

            return $dados;

        }

        if($equipamento == "carregador") {

            $select->where('armaria_equipamento.equipamento_tipo', $equipamento)->where('armaria_equipamento.equipamento_especie', $equipamentoEspecie);

            $dados = $select->select('armaria_equipamento.*','armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.modelo','ASC')->orderBy('modelo', 'ASC')->get();

            $dados = $dados->unique('modelo');

            return $dados;

        }

        if($equipamento == "algema") {

            $select->where('equipamento_tipo',$equipamento);

            $dados = $select->select('armaria_equipamento.*','armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especificacao','ASC')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->get();

            return $dados;

        }

        if($equipamento == "granada") {

            $select->where('equipamento_tipo',$equipamento)->where('equipamento_especificacao', $equipamentoEspecie)->get();

            $dados = $select->select('armaria_equipamento.*','armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.modelo','ASC')->orderBy('modelo', 'ASC')->get();

            $dados = $dados->unique('modelo');
            return $dados;

        }
        if($equipamento == "epi") {

            $select->where('equipamento_tipo',$equipamento)->where('equipamento_especie', $request->get('especie'))->get();

            $dados = $select->select('armaria_equipamento.*','armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.modelo','ASC')->orderBy('calibre', 'ASC')->get();

            return $dados;

        }
        if($equipamento == "colete") {

            $select->where('equipamento_tipo',$equipamento)->where('armaria_equipamento.status', 'A')->get();

            $dados = $select->select('armaria_equipamento.*','armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie','ASC')->orderBy('tamanho', 'ASC')->get();

            return $dados;

        }

        if($equipamento == "espargidor")
        {

            $select->where('equipamento_tipo',$equipamento)->get();

            $dados = $select->select('armaria_equipamento.*','armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie','ASC')->orderBy('modelo', 'ASC')->get();

            $dados = $dados->unique('modelo');

            return $dados;

        }

        //dd($dados);

    }

    public function equipamentoBuscaAjax(Request $request)
    {
        //dd($request->all());

        $equipamento = $request->get('equipamento');

        if($equipamento == "armaFogo")
        {
            $select = DB::table('armaria_equipamento');

            $select->leftJoin('armaria_equipamento_fabricante',function($query){
                $query->on('armaria_equipamento_fabricante.id','=','armaria_equipamento.fk_fabricante');
            });

            $select->leftJoin('armaria_cautela_equipamento',function($query){
                $query->on('armaria_cautela_equipamento.id_equipamento','=','armaria_equipamento.id');
            });

            $select->where('equipamento_tipo',$equipamento)->where('armaria_equipamento.status', 'A')->where('equipamento_especie', $request->get('especie'))->get();

            $select->whereNull('armaria_cautela_equipamento.id_equipamento');

            $dados = $select->select('armaria_equipamento.*','armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie','ASC')->orderBy('calibre', 'ASC')->get();

            return $dados;

        }

        if($equipamento == "dei")
        {
            $select = DB::table('armaria_equipamento');

            $select->leftJoin('armaria_equipamento_fabricante',function($query){
                $query->on('armaria_equipamento_fabricante.id','=','armaria_equipamento.fk_fabricante');
            });

            $select->leftJoin('armaria_cautela_equipamento',function($query){
                $query->on('armaria_cautela_equipamento.id_equipamento','=','armaria_equipamento.id');
            });

            $select->where('equipamento_tipo',$equipamento)->where('armaria_equipamento.status', 'A')->where('equipamento_especie', $request->get('especie'))->get();

            $select->whereNull('armaria_cautela_equipamento.id_equipamento');

            $dados = $select->select('armaria_equipamento.*','armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie','ASC')->orderBy('calibre', 'ASC')->get();

            return $dados;

        }

        if($equipamento == "municao")
        {
            $select = DB::table('armaria_equipamento');

            $select->leftJoin('armaria_equipamento_fabricante',function($query){
                $query->on('armaria_equipamento_fabricante.id','=','armaria_equipamento.fk_fabricante');
            });

            $select->where('equipamento_tipo',$equipamento)->where('equipamento_especie', $request->get('especie'))->get();

            $dados = $select->select('armaria_equipamento.*','armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->orderBy('armaria_equipamento.modelo','ASC')->orderBy('calibre', 'ASC')->get();

            //
            $dados = json_decode($dados,true);
                foreach($dados as $key => $dado)
                {
                    $dados[$key]['estoque'] = self::estoqueEquipamento($dado['id']);
                }
            //
            return $dados;

        }

        if($equipamento == "carregador")
        {
            $select = DB::table('armaria_equipamento');

            $select->leftJoin('armaria_equipamento_fabricante',function($query){
                $query->on('armaria_equipamento_fabricante.id','=','armaria_equipamento.fk_fabricante');
            });

            $select->where('equipamento_tipo',$equipamento)->where('equipamento_especie', $request->get('especie'))->get();

            $dados = $select->select('armaria_equipamento.*','armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.modelo','ASC')->orderBy('calibre', 'ASC')->get();

            //
            $dados = json_decode($dados,true);
            foreach($dados as $key => $dado)
            {
                $dados[$key]['estoque'] = self::estoqueEquipamento($dado['id']);
            }
            //

            return $dados;

        }

        if($equipamento == "algema")
        {
            $select = DB::table('armaria_equipamento');

            $select->leftJoin('armaria_equipamento_fabricante',function($query){
                $query->on('armaria_equipamento_fabricante.id','=','armaria_equipamento.fk_fabricante');
            });

            $select->where('equipamento_tipo',$equipamento)->get();

            $dados = $select->select('armaria_equipamento.*','armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especificacao','ASC')->orderBy('armaria_equipamento.equipamento_especie', 'ASC')->get();

            //
            $dados = json_decode($dados,true);
            foreach($dados as $key => $dado)
            {
                $dados[$key]['estoque'] = self::estoqueEquipamento($dado['id']);
            }
            //

            return $dados;

        }

        if($equipamento == "granada")
        {
            $select = DB::table('armaria_equipamento');

            $select->leftJoin('armaria_equipamento_fabricante',function($query){
                $query->on('armaria_equipamento_fabricante.id','=','armaria_equipamento.fk_fabricante');
            });

            $select->where('equipamento_tipo',$equipamento)->where('equipamento_especificacao', $request->get('especie'))->get();

            $dados = $select->select('armaria_equipamento.*','armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.modelo','ASC')->orderBy('calibre', 'ASC')->get();

            //
            $dados = json_decode($dados,true);
            foreach($dados as $key => $dado)
            {
                $dados[$key]['estoque'] = self::estoqueEquipamento($dado['id']);
            }
            //

            return $dados;

        }

        if($equipamento == "epi")
        {
            $select = DB::table('armaria_equipamento');

            $select->leftJoin('armaria_equipamento_fabricante',function($query){
                $query->on('armaria_equipamento_fabricante.id','=','armaria_equipamento.fk_fabricante');
            });

            if($request->get('especie') == "capacete" || $request->get('especie') == "escudo")
            {
                $select->leftJoin('armaria_cautela_equipamento',function($query){
                    $query->on('armaria_cautela_equipamento.id_equipamento','=','armaria_equipamento.id');
                });

                $select->whereNull('armaria_cautela_equipamento.id_equipamento');

            }
            $select->where('equipamento_tipo',$equipamento)->where('equipamento_especie', $request->get('especie'))->get();

            $dados = $select->select('armaria_equipamento.*','armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.modelo','ASC')->orderBy('calibre', 'ASC')->get();

            //
            $dados = json_decode($dados,true);
            foreach($dados as $key => $dado)
            {
                $dados[$key]['estoque'] = self::estoqueEquipamento($dado['id']);
            }
            //

            return $dados;

        }

        if($equipamento == "colete")
        {
            $select = DB::table('armaria_equipamento');

            $select->leftJoin('armaria_equipamento_fabricante',function($query){
                $query->on('armaria_equipamento_fabricante.id','=','armaria_equipamento.fk_fabricante');
            });

            $select->leftJoin('armaria_cautela_equipamento',function($query){
                $query->on('armaria_cautela_equipamento.id_equipamento','=','armaria_equipamento.id');
            });

            $select->where('equipamento_tipo',$equipamento)->where('armaria_equipamento.status', 'A')->get();

            $select->whereNull('armaria_cautela_equipamento.id_equipamento');

            $dados = $select->select('armaria_equipamento.*','armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie','ASC')->orderBy('tamanho', 'ASC')->get();

            return $dados;

        }

        if($equipamento == "espargidor")
        {
            $select = DB::table('armaria_equipamento');

            $select->leftJoin('armaria_equipamento_fabricante',function($query){
                $query->on('armaria_equipamento_fabricante.id','=','armaria_equipamento.fk_fabricante');
            });

            $select->where('equipamento_tipo',$equipamento)->get();

            $dados = $select->select('armaria_equipamento.*','armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie','ASC')->orderBy('modelo', 'ASC')->get();

            //
            $dados = json_decode($dados,true);
            foreach($dados as $key => $dado)
            {
                $dados[$key]['estoque'] = self::estoqueEquipamento($dado['id']);
            }
            //

            return $dados;

        }


    }

    public function cadastro()
    {
//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }


        return view('armaria.cadastro.cadastro');
    }

    public function equipamentoStore(Request $request)
    {

//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }

        $tipo = $request->get('tipoArmamento');

        switch($tipo){

            case "armaFogo" :

                $insert = DB::table('armaria_equipamento')->insert([
                        'equipamento_tipo' => $request->get('tipoArmamento'),
                        'equipamento_especie' => $request->get('tipo'),
                        'equipamento_especificacao' => $request->get('armamento'),
                        'calibre' => $request->get('calibre'),
                        'fk_fabricante' => $request->get('fabricante'),
                        'modelo' => strtoupper($request->get('modelo')),
                        'n_serie' => strtoupper($request->get('n_serie')),
                        'n_tombo' => strtoupper($request->get('n_tombo')),
                        'origem_aquisicao' => $request->get('origem'),
                        'obs' => $request->get('obs'),
                    ]
                );

                $idInsert = DB::getPdo()->lastInsertId();
                LogController::log($idInsert,"armaria_equipamento","add", Auth::user()->cpf, "Inserção de Arma de Fogo");


                break;

            case "dei" :

                $insert = DB::table('armaria_equipamento')->insert([
                        'equipamento_tipo' => $request->get('tipoArmamento'),
                        'equipamento_especie' => $request->get('tipo'),
                        'equipamento_especificacao' => $request->get('armamento'),
                        'fk_fabricante' => $request->get('fabricante'),
                        'modelo' => strtoupper($request->get('modelo')),
                        'n_serie' => strtoupper($request->get('n_serie')),
                        'n_tombo' => strtoupper($request->get('n_tombo')),
                        'origem_aquisicao' => $request->get('origem'),
                        'obs' => $request->get('obs'),
                    ]
                );

                $idInsert = DB::getPdo()->lastInsertId();
                LogController::log($idInsert,"armaria_equipamento","add", Auth::user()->cpf, "Inserção de Dispositivo Elétrico Icapacitante");


                break;

            case "municao" :

                $insert = DB::table('armaria_equipamento')->insert([
                        'equipamento_tipo' => $request->get('tipoArmamento'),
                        'equipamento_especie' => $request->get('tipo'),
                        'equipamento_especificacao' => $request->get('tipoMunicao'),
                        'calibre' => $request->get('calibre'),
                        'fk_fabricante' => $request->get('fabricante'),
                        'modelo' => $request->get('municao'),
                        'validade' => Tdata::TdataMysql($request->get('validade')),
                        'lote' => strtoupper($request->get('lote')),
                        'n_tombo' => strtoupper($request->get('n_tombo')),
                        'origem_aquisicao' => $request->get('origem'),
                        'obs' => $request->get('obs'),
                        'adc_sub' => "A",

                    ]
                );

                $idInsert = DB::getPdo()->lastInsertId();
                LogController::log($idInsert,"armaria_equipamento","add", Auth::user()->cpf, "Inserção de Munição");

                break;

            case "carregador" :

                $insert = DB::table('armaria_equipamento')->insert([
                        'equipamento_tipo' => $request->get('tipoArmamento'),
                        'equipamento_especie' => $request->get('tipo'),
                        'armamento' => $request->get('armamento'),
                        'calibre' => $request->get('calibre'),
                        'volume_peso' => $request->get('capacidade'),
                        'fk_fabricante' => $request->get('fabricante'),
                        'modelo' => strtoupper($request->get('modelo')),
                        'n_tombo' => strtoupper($request->get('n_tombo')),
                        'origem_aquisicao' => $request->get('origem'),
                        'obs' => $request->get('obs'),

                    ]
                );

                $idInsert = DB::getPdo()->lastInsertId();
                LogController::log($idInsert,"armaria_equipamento","add", Auth::user()->cpf, "Inserção de Carregador");

                break;

            case "algema" :

                $insert = DB::table('armaria_equipamento')->insert([
                        'equipamento_tipo' => $request->get('tipoArmamento'),
                        'equipamento_especie' => $request->get('tipoAlgema'),
                        'equipamento_especificacao' => $request->get('tipo'),
                        'fk_fabricante' => $request->get('fabricante'),
                        'n_serie' => strtoupper($request->get('n_serie')),
                        'n_tombo' => strtoupper($request->get('n_tombo')),
                        'origem_aquisicao' => $request->get('origem'),
                        'obs' => $request->get('obs'),

                    ]
                );

                $idInsert = DB::getPdo()->lastInsertId();
                LogController::log($idInsert,"armaria_equipamento","add", Auth::user()->cpf, "Inserção de Algema");

                break;

            case "granada" :

                $insert = DB::table('armaria_equipamento')->insert([
                        'equipamento_tipo' => $request->get('tipoArmamento'),
                        'equipamento_especie' => $request->get('tipo'),
                        'equipamento_especificacao' => $request->get('tipoGranada'),
                        'fk_fabricante' => $request->get('fabricante'),
                        'modelo' => $request->get('modeloGranada'),
                        'validade' => Tdata::TdataMysql($request->get('validade')),
                        'lote' => strtoupper($request->get('lote')),
                        'n_tombo' => strtoupper($request->get('n_tombo')),
                        'origem_aquisicao' => $request->get('origem'),
                        'obs' => $request->get('obs'),

                    ]
                );

                $idInsert = DB::getPdo()->lastInsertId();
                LogController::log($idInsert,"armaria_equipamento","add", Auth::user()->cpf, "Inserção de Granada");

                break;

            case "epi" :

                if($request->get('tipo') == 'capacete' || $request->get('tipo') == 'escudo')
                {
                    $insert = DB::table('armaria_equipamento')->insert([
                            'equipamento_tipo' => $request->get('tipoArmamento'),
                            'equipamento_especie' => $request->get('tipo'),
                            'fk_fabricante' => $request->get('fabricante'),
                            'validade' => Tdata::TdataMysql($request->get('validade')),
                            'lote' => strtoupper($request->get('lote')),
                            'n_serie' => strtoupper($request->get('n_serie')),
                            'n_tombo' => strtoupper($request->get('n_tombo')),
                            'origem_aquisicao' => $request->get('origem'),
                            'obs' => $request->get('obs'),

                        ]
                    );

                    $idInsert = DB::getPdo()->lastInsertId();
                    LogController::log($idInsert,"armaria_equipamento","add", Auth::user()->cpf, "Inserção de EPI");

                }else{

                    $insert = DB::table('armaria_equipamento')->insert([
                            'equipamento_tipo' => $request->get('tipoArmamento'),
                            'equipamento_especie' => $request->get('tipo'),
                            'n_tombo' => strtoupper($request->get('n_tombo')),
                            'origem_aquisicao' => $request->get('origem'),
                            'obs' => $request->get('obs'),
                        ]
                    );

                    $idInsert = DB::getPdo()->lastInsertId();
                    LogController::log($idInsert,"armaria_equipamento","add", Auth::user()->cpf, "Inserção de EPI");

                }

            break;

            case "colete" :

                $insert = DB::table('armaria_equipamento')->insert([
                        'equipamento_tipo' => $request->get('tipoArmamento'),
                        'fk_fabricante' => $request->get('fabricante'),
                        'validade' => Tdata::TdataMysql($request->get('validade')),
                        'tamanho' => strtoupper($request->get('tamanho')),
                        'n_serie' => $request->get('n_serie'),
                        'n_tombo' => strtoupper($request->get('n_tombo')),
                        'origem_aquisicao' => $request->get('origem'),
                        'obs' => $request->get('obs'),

                    ]
                );

                $idInsert = DB::getPdo()->lastInsertId();
                LogController::log($idInsert,"armaria_equipamento","add", Auth::user()->cpf, "Inserção de Colete");

                break;

            case "espargidor" :

                $insert = DB::table('armaria_equipamento')->insert([
                        'equipamento_tipo' => $request->get('tipoArmamento'),
                        'fk_fabricante' => $request->get('fabricante'),
                        'modelo' => $request->get('modelo'),
                        'validade' => Tdata::TdataMysql($request->get('validade')),
                        'volume_peso' => strtoupper($request->get('volume_peso')),
                        'n_tombo' => strtoupper($request->get('n_tombo')),
                        'origem_aquisicao' => $request->get('origem'),
                        'obs' => $request->get('obs'),

                    ]
                );

                $idInsert = DB::getPdo()->lastInsertId();
                LogController::log($idInsert,"armaria_equipamento","add", Auth::user()->cpf, "Inserção de Espargidor");

                break;

        }

        if ($insert) {

            return redirect()->route('armaria.buscar')->with('alert-success', 'O equipamento foi inserido com sucesso!');

        }else{

            return redirect('armaria.cadastro')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }

    public function buscar(Request $request)
    {
//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }
//

        $unidades = DB::table('unidade')->where('troca','1')->orderBy('sigla', 'ASC')->get();

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        /**
         * se não realizar uma busca
         * - Evita que mostre todos os resultados sem realizar uma busca
         */
        if(!strpos($url,"?"))
        {

            $dados = false;

        }else {

            $tipo = $request->get('tipoArmamento');

            $equipamento = DB::table('armaria_equipamento');

            $equipamento->leftJoin('armaria_equipamento_fabricante',function($query){
                $query->on('armaria_equipamento_fabricante.id','=','armaria_equipamento.fk_fabricante');
            });

            switch($tipo){

                case "armaFogo" :

                    if($request->get('tipoArmamento') != null)
                    {
                        $equipamento->where('armaria_equipamento.equipamento_tipo',$request->get('tipoArmamento'));
                        $equipamento->where('armaria_equipamento.status', 'A');
                    }

                    if($request->get('tipo') != null)
                    {
                        $equipamento->where('armaria_equipamento.equipamento_especie',$request->get('tipo'));
                    }

                    if($request->get('armamento') != null)
                    {
                        $equipamento->where('armaria_equipamento.equipamento_especificacao',$request->get('armamento'));
                    }

                    if($request->get('calibre') != null)
                    {
                        $equipamento->where('armaria_equipamento.calibre',$request->get('calibre'));
                    }

                    if($request->get('modelo') != null)
                    {
                        $equipamento->where('armaria_equipamento.modelo','LIKE', '%'.$request->get('modelo').'%');
                    }

                    if($request->get('n_serie') != null)
                    {
                        $equipamento->where('armaria_equipamento.n_serie','LIKE', '%'.$request->get('n_serie').'%');
                    }

                    break;

                case "dei" :

                    if($request->get('tipoArmamento') != null)
                    {
                        $equipamento->where('armaria_equipamento.equipamento_tipo',$request->get('tipoArmamento'));
                        $equipamento->where('armaria_equipamento.status', 'A');
                    }

                    if($request->get('tipo') != null)
                    {
                        $equipamento->where('armaria_equipamento.equipamento_especie',$request->get('tipo'));
                    }

                    if($request->get('armamento') != null)
                    {
                        $equipamento->where('armaria_equipamento.equipamento_especificacao',$request->get('armamento'));
                    }

                    if($request->get('modelo') != null)
                    {
                        $equipamento->where('armaria_equipamento.modelo','LIKE', '%'.$request->get('modelo').'%');
                    }

                    if($request->get('n_serie') != null)
                    {
                        $equipamento->where('armaria_equipamento.n_serie','LIKE', '%'.$request->get('n_serie').'%');
                    }

                    break;

                case "municao" :



                    if($request->get('tipoArmamento') != null)
                    {
                        $equipamento->where('armaria_equipamento.equipamento_tipo',$request->get('tipoArmamento'));
                    }

                    if($request->get('tipo') != null)
                    {
                        $equipamento->where('armaria_equipamento.equipamento_especie',$request->get('tipo'));
                    }

                    if($request->get('calibre') != null)
                    {
                        $equipamento->where('armaria_equipamento.calibre',$request->get('calibre'));
                    }

                    if($request->get('modelo') != null)
                    {
                        $equipamento->where('armaria_equipamento.modelo','LIKE', '%'.$request->get('modelo').'%');
                    }

                    if($request->get('validade') != null)
                    {
                        $equipamento->where('armaria_equipamento.validade', Tdata::TdataMysql($request->get('validade')));
                    }

                    if($request->get('lote') != null)
                    {
                        $equipamento->where('armaria_equipamento.lote','LIKE', '%'.$request->get('lote').'%');
                    }


                    break;

                case "carregador" :

                    if($request->get('tipoArmamento') != null)
                    {
                        $equipamento->where('armaria_equipamento.equipamento_tipo',$request->get('tipoArmamento'));
                    }

                    if($request->get('tipo') != null)
                    {
                        $equipamento->where('armaria_equipamento.equipamento_especie',$request->get('tipo'));
                    }

                    if($request->get('armamento') != null)
                    {
                        $equipamento->where('armaria_equipamento.armamento',$request->get('armamento'));
                    }

                    if($request->get('calibre') != null)
                    {
                        $equipamento->where('armaria_equipamento.calibre',$request->get('calibre'));
                    }

                    if($request->get('capacidade') != null)
                    {
                        $equipamento->where('armaria_equipamento.volume_peso',$request->get('capacidade'));
                    }

                    if($request->get('fabricante') != null)
                    {
                        $equipamento->where('armaria_equipamento.fk_fabricante',$request->get('fabricante'));
                    }

                    if($request->get('modelo') != null)
                    {
                        $equipamento->where('armaria_equipamento.modelo','LIKE', '%'.$request->get('modelo').'%');
                    }


                    break;

                case "algema" :

                    if($request->get('tipoArmamento') != null)
                    {
                        $equipamento->where('armaria_equipamento.equipamento_tipo',$request->get('tipoArmamento'));
                    }

                    if($request->get('tipoAlgema') != null)
                    {
                        $equipamento->where('armaria_equipamento.equipamento_especie',$request->get('tipoAlgema'));
                    }

                    if($request->get('fabricante') != null)
                    {
                        $equipamento->where('armaria_equipamento.fk_fabricante',$request->get('fabricante'));
                    }

                    if($request->get('n_serie') != null)
                    {
                        $equipamento->where('armaria_equipamento.n_serie','LIKE', '%'.$request->get('n_serie').'%');
                    }

                    break;

                case "granada" :

                    if($request->get('tipoArmamento') != null)
                    {
                        $equipamento->where('armaria_equipamento.equipamento_tipo',$request->get('tipoArmamento'));
                    }

                    if($request->get('tipo') != null)
                    {
                        $equipamento->where('armaria_equipamento.equipamento_especie',$request->get('tipo'));
                    }

                    if($request->get('tipoGranada') != null)
                    {
                        $equipamento->where('armaria_equipamento.equipamento_especificacao',$request->get('tipoGranada'));
                    }

                    if($request->get('fabricante') != null)
                    {
                        $equipamento->where('armaria_equipamento.fk_fabricante',$request->get('fabricante'));
                    }

                    if($request->get('modelo') != null)
                    {
                        $equipamento->where('armaria_equipamento.modelo','LIKE', '%'.$request->get('modelo').'%');
                    }

                    if($request->get('validade') != null)
                    {
                        $equipamento->where('armaria_equipamento.validade', Tdata::TdataMysql($request->get('validade')));
                    }

                    if($request->get('lote') != null)
                    {
                        $equipamento->where('armaria_equipamento.lote','LIKE', '%'.$request->get('lote').'%');
                    }

                    break;

                case "epi" :

                    if($request->get('tipo') == 'capacete' || $request->get('tipo') == 'escudo')
                    {
                        if($request->get('tipoArmamento') != null)
                        {
                            $equipamento->where('armaria_equipamento.equipamento_tipo',$request->get('tipoArmamento'));
                        }

                        if($request->get('tipo') != null)
                        {
                            $equipamento->where('armaria_equipamento.equipamento_especie',$request->get('tipo'));
                        }

                        if($request->get('fabricante') != null)
                        {
                            $equipamento->where('armaria_equipamento.fk_fabricante',$request->get('fabricante'));
                        }

                        if($request->get('validade') != null)
                        {
                            $equipamento->where('armaria_equipamento.validade', Tdata::TdataMysql($request->get('validade')));
                        }

                        if($request->get('lote') != null)
                        {
                            $equipamento->where('armaria_equipamento.lote','LIKE', '%'.$request->get('lote').'%');
                        }

                        if($request->get('n_serie') != null)
                        {
                            $equipamento->where('armaria_equipamento.n_serie','LIKE', '%'.$request->get('n_serie').'%');
                        }

                    }else{

                        if($request->get('tipoArmamento') != null)
                        {
                            $equipamento->where('armaria_equipamento.equipamento_tipo',$request->get('tipoArmamento'));
                        }

                        if($request->get('tipo') != null)
                        {
                            $equipamento->where('armaria_equipamento.equipamento_especie',$request->get('tipo'));
                        }

                    }

                    break;

                case "colete" :

                    if($request->get('tipoArmamento') != null)
                    {
                        $equipamento->where('armaria_equipamento.equipamento_tipo',$request->get('tipoArmamento'));
                        $equipamento->where('armaria_equipamento.status', 'A');
                    }

                    if($request->get('fabricante') != null)
                    {
                        $equipamento->where('armaria_equipamento.fk_fabricante',$request->get('fabricante'));
                    }

                    if($request->get('validade') != null)
                    {
                        $equipamento->where('armaria_equipamento.validade', Tdata::TdataMysql($request->get('validade')));
                    }

                    if($request->get('tamanho') != null)
                    {
                        $equipamento->where('armaria_equipamento.tamanho',$request->get('tamanho'));
                    }

                    if($request->get('n_serie') != null)
                    {
                        $equipamento->where('armaria_equipamento.n_serie','LIKE', '%'.$request->get('n_serie').'%');
                    }

                    break;

                case "espargidor" :

                    if($request->get('tipoArmamento') != null)
                    {
                        $equipamento->where('armaria_equipamento.equipamento_tipo',$request->get('tipoArmamento'));
                    }

                    if($request->get('fabricante') != null)
                    {
                        $equipamento->where('armaria_equipamento.fk_fabricante',$request->get('fabricante'));
                    }

                    if($request->get('modelo') != null)
                    {
                        $equipamento->where('armaria_equipamento.modelo','LIKE', '%'.$request->get('modelo').'%');
                    }

                    if($request->get('validade') != null)
                    {
                        $equipamento->where('armaria_equipamento.validade', Tdata::TdataMysql($request->get('validade')));
                    }

                    if($request->get('volume_peso') != null)
                    {
                        $equipamento->where('armaria_equipamento.volume_peso',$request->get('volume_peso'));
                    }

                    break;
           }
            if($request->get('origem') != null)
            {
                $equipamento->where('armaria_equipamento.origem_aquisicao',$request->get('origem'));
            }

            $dados = $equipamento->select('armaria_equipamento.*','armaria_equipamento_fabricante.nome AS fabNome')->orderBy('armaria_equipamento.equipamento_especie','ASC')->paginate(50)->appends($request->input());

        }


        return view('armaria.buscar.buscar', compact('unidades','dados','tipo'));

    }

    public function editarEquipamento($id)
    {
//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }


        $equipamento = DB::table('armaria_equipamento')->where('id',$id)->get();

        $equipamento[0]->validade = Tdata::TdataBR($equipamento[0]->validade);
        $tipo = json_decode($equipamento,true)[0]['equipamento_tipo'];

        $fabricantes = DB::table('armaria_equipamento_fabricante')->where('tipo',$tipo)->get();


        return view('armaria.cadastro.cadastro', compact('equipamento', 'fabricantes','tipo'));

    }

    public function updateEquipamento(Request $request, $id)
    {
//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }


        //dd($request->all());

        $tipo = $request->get('tipoEqupEditar');

        switch($tipo){

            case "armaFogo" :

                $updateEquipamento= DB::table('armaria_equipamento')->where('id',$id)->update([
                        'equipamento_especie' => $request->get('tipo'),
                        'equipamento_especificacao' => $request->get('armamento'),
                        'calibre' => $request->get('calibre'),
                        'fk_fabricante' => $request->get('fabricante'),
                        'modelo' => strtoupper($request->get('modelo')),
                        'n_serie' => strtoupper($request->get('n_serie')),
                        'n_tombo' => strtoupper($request->get('n_tombo')),
                        'origem_aquisicao' => $request->get('origem'),
                        'obs' => $request->get('obs'),
                    ]
                );

                LogController::log($id,"armaria_equipamento","update", Auth::user()->cpf, "Alteração de Arma de Fogo");

                break;

            case "dei" :

                $updateEquipamento= DB::table('armaria_equipamento')->where('id',$id)->update([
                        'equipamento_especie' => $request->get('tipo'),
                        'equipamento_especificacao' => $request->get('armamento'),
                        'fk_fabricante' => $request->get('fabricante'),
                        'modelo' => strtoupper($request->get('modelo')),
                        'n_serie' => strtoupper($request->get('n_serie')),
                        'n_tombo' => strtoupper($request->get('n_tombo')),
                        'origem_aquisicao' => $request->get('origem'),
                        'obs' => $request->get('obs'),
                    ]
                );

                LogController::log($id,"armaria_equipamento","update", Auth::user()->cpf, "Alteração de Dispositivo Elétrico Incapacitante");

                break;

            case "municao" :

                $updateEquipamento= DB::table('armaria_equipamento')->where('id',$id)->update([
                        'equipamento_especie' => $request->get('tipo'),
                        'equipamento_especificacao' => $request->get('tipoMunicao'),
                        'calibre' => $request->get('calibre'),
                        'fk_fabricante' => $request->get('fabricante'),
                        'modelo' => $request->get('municao'),
                        'validade' => Tdata::TdataMysql($request->get('validade')),
                        'lote' => strtoupper($request->get('lote')),
                        'n_tombo' => strtoupper($request->get('n_tombo')),
                        'origem_aquisicao' => $request->get('origem'),
                        'obs' => $request->get('obs'),

                    ]
                );

                LogController::log($id,"armaria_equipamento","update", Auth::user()->cpf, "Alteração de Munição");

                break;

            case "carregador" :

                $updateEquipamento= DB::table('armaria_equipamento')->where('id',$id)->update([
                        'equipamento_especie' => $request->get('tipo'),
                        'armamento' => $request->get('armamento'),
                        'calibre' => $request->get('calibre'),
                        'volume_peso' => $request->get('capacidade'),
                        'fk_fabricante' => $request->get('fabricante'),
                        'modelo' => strtoupper($request->get('modelo')),
                        'n_tombo' => strtoupper($request->get('n_tombo')),
                        'origem_aquisicao' => $request->get('origem'),
                        'obs' => $request->get('obs'),

                    ]
                );

                LogController::log($id,"armaria_equipamento","update", Auth::user()->cpf, "Alteração de Carregador");

                break;

            case "algema" :

                $updateEquipamento= DB::table('armaria_equipamento')->where('id',$id)->update([
                        'equipamento_especie' => $request->get('tipoAlgema'),
                        'equipamento_especificacao' => $request->get('tipo'),
                        'fk_fabricante' => $request->get('fabricante'),
                        'n_serie' => strtoupper($request->get('n_serie')),
                        'n_tombo' => strtoupper($request->get('n_tombo')),
                        'origem_aquisicao' => $request->get('origem'),
                        'obs' => $request->get('obs'),

                    ]
                );

                LogController::log($id,"armaria_equipamento","update", Auth::user()->cpf, "Alteração de Algema");

                break;

            case "granada" :

                $updateEquipamento= DB::table('armaria_equipamento')->where('id',$id)->update([
                        'equipamento_especie' => $request->get('tipo'),
                        'equipamento_especificacao' => $request->get('tipoGranada'),
                        'fk_fabricante' => $request->get('fabricante'),
                        'modelo' => $request->get('modeloGranada'),
                        'validade' => Tdata::TdataMysql($request->get('validade')),
                        'lote' => strtoupper($request->get('lote')),
                        'n_tombo' => strtoupper($request->get('n_tombo')),
                        'origem_aquisicao' => $request->get('origem'),
                        'obs' => $request->get('obs'),

                    ]
                );

                LogController::log($id,"armaria_equipamento","update", Auth::user()->cpf, "Alteração de Granada");

                break;

            case "epi" :

                if($request->get('tipo') == 'capacete' || $request->get('tipo') == 'escudo')
                {
                    $updateEquipamento= DB::table('armaria_equipamento')->where('id',$id)->update([
                            'equipamento_especie' => $request->get('tipo'),
                            'fk_fabricante' => $request->get('fabricante'),
                            'validade' => Tdata::TdataMysql($request->get('validade')),
                            'lote' => strtoupper($request->get('lote')),
                            'n_serie' => strtoupper($request->get('n_serie')),
                            'n_tombo' => strtoupper($request->get('n_tombo')),
                            'origem_aquisicao' => $request->get('origem'),
                            'obs' => $request->get('obs'),

                        ]
                    );

                    LogController::log($id,"armaria_equipamento","update", Auth::user()->cpf, "Alteração de EPI");

                }else{

                    $updateEquipamento= DB::table('armaria_equipamento')->where('id',$id)->update([
                            'equipamento_especie' => $request->get('tipo'),
                            'n_tombo' => strtoupper($request->get('n_tombo')),
                            'origem_aquisicao' => $request->get('origem'),
                            'obs' => $request->get('obs'),
                        ]
                    );

                    LogController::log($id,"armaria_equipamento","update", Auth::user()->cpf, "Alteração de EPI");

                }

                break;

            case "colete" :

                $updateEquipamento= DB::table('armaria_equipamento')->where('id',$id)->update([
                        'fk_fabricante' => $request->get('fabricante'),
                        'validade' => Tdata::TdataMysql($request->get('validade')),
                        'tamanho' => strtoupper($request->get('tamanho')),
                        'n_serie' => $request->get('n_serie'),
                        'n_tombo' => strtoupper($request->get('n_tombo')),
                        'origem_aquisicao' => $request->get('origem'),
                        'obs' => $request->get('obs'),

                    ]
                );

                LogController::log($id,"armaria_equipamento","update", Auth::user()->cpf, "Alteração de Colete");

                break;

            case "espargidor" :

                $updateEquipamento= DB::table('armaria_equipamento')->where('id',$id)->update([
                        'fk_fabricante' => $request->get('fabricante'),
                        'modelo' => $request->get('modelo'),
                        'validade' => Tdata::TdataMysql($request->get('validade')),
                        'volume_peso' => strtoupper($request->get('volume_peso')),
                        'n_tombo' => strtoupper($request->get('n_tombo')),
                        'origem_aquisicao' => $request->get('origem'),
                        'obs' => $request->get('obs'),

                    ]
                );

                LogController::log($id,"armaria_equipamento","update", Auth::user()->cpf, "Alteração de Espargidor");

                break;


        }

        if ($updateEquipamento) {

            return redirect()->route('armaria.buscar')->with('alert-success', 'Os dados do equipamento foram alterados com sucesso!');

        }else{

            return redirect('armaria.cadastro')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }

    public function equipamentoQuantidade($id)
    {
//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }

        $usoEquipamento = DB::table("armaria_uso_material_unidade_equipamento")->where('fk_equipamento', $id)->sum('quantidade');

        $select = DB::table('armaria_equipamento_quantidade')->where('fk_equipamento',$id)->get();
        if(!empty(json_decode($select,true)))
        {
            $quantidadeTotal = json_decode($select,true)[0]['quantidade'];

            $quantidade = $quantidadeTotal - $usoEquipamento;

        }else{
            $quantidade = "";
        }

        return view('armaria.quantidade.quantidade',compact('id','quantidade'));
    }

    public function equipamentoQuantidadeUpdate(Request $request, $id)
    {
//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }
//

        $select = DB::table('armaria_equipamento_quantidade')->where('fk_equipamento',$id)->get();
        if(!empty(json_decode($select,true)))
        {
            $insert = DB::table('armaria_equipamento_quantidade')->where('fk_equipamento',$id)->update([
                    'quantidade' => $request->get('quantidade'),
                ]
            );

            LogController::log($id,"armaria_equipamento_quantidade","update", Auth::user()->cpf, "Alteração de quantidade de equipamento");


        }else{

            $insert = DB::table('armaria_equipamento_quantidade')->insert([
                    'fk_equipamento' => $id,
                    'quantidade' => $request->get('quantidade'),
                ]
            );

            LogController::log($id,"armaria_equipamento_quantidade","update", Auth::user()->cpf, "Alteração de quantidade de equipamento");

        }


        if ($insert) {

            return redirect()->route('armaria.buscar')->with('alert-success', 'Quantidade informada com sucesso!');

        }else{

            return redirect('armaria.buscar')->with('alert-danger', 'Ocorreu um problema ao salvar o formulário, por favor, tente novamente!');

        }


    }

    public function cautela()
    {
//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }
//

        $unidades = DB::table('unidade')->where('troca','1')->orderBy('sigla', 'ASC')->get();

        return view('armaria.cautela.cautela',compact('unidades'));
    }

    public function cautelaStore(Request $request)
    {
//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }

        $servidores = $request->get('agentesHidden');
        $cautelaPara = $request->get('cautelaPara');


        $verificaDadosServidor = FacadeController::getDadosServidorPorMat($servidores[0]);
        if($cautelaPara != "outros") {
            $foneServidor = FacadeController::getDadosFoneServidor($verificaDadosServidor[0]->id);
            if($verificaDadosServidor[0]->residencia_logradouro == "" || $foneServidor[0]['numero'] == "")
            {
                if($verificaDadosServidor[0]->residencia_logradouro == "")
                {
                    $textoEnd = "<strong>Endereço</strong><br>";
                }else{
                    $textoEnd = "";
                }
                if($foneServidor[0]['numero'] == "")
                {
                    $textoFone = "<strong>Telefone</strong><br>";
                }else{
                    $textoFone = "";
                }
                return redirect()->back()->withInput()->with('alert-danger', "É necessário que o servidor possua os seguintes dados cadastrados no Setor de Recursos Humanos da SEAP:<br>".$textoEnd.$textoFone);

            }
        }

//dd($request->all());

        if($request->get('data_inicial') != null || $request->get('data_final') != null) {
            $insertCautela = DB::table('armaria_cautela')->insert([
                'tipo_cautela' => $request->get('cautelaPara'),
                'unidade_prisional' => $request->get('unidade_prisional'),
                'pavilhao' => $request->get('pavilhao'),
                'data_inicial' => Tdata::TdataMysql($request->get('data_inicial')),
                'data_final' => Tdata::TdataMysql($request->get('data_final')),
                'tempo_indeterminado' => $request->get('tempoIndeterminado'),
                'created_at' => date("Y-m-d"),
                'obs' => $request->get('obs'),
                ]
            );

            $idInsert = DB::getPdo()->lastInsertId();
            $idCautela = DB::getPdo()->lastInsertId();

            LogController::log($idInsert,"armaria_cautela","add", Auth::user()->cpf, "Inserção de Cautela");


            $periodoCautela = "De: ".$request->get('data_inicial')." Até: ".$request->get('data_final');

        }else{
            $insertCautela = DB::table('armaria_cautela')->insert([
                    'tipo_cautela' => $request->get('cautelaPara'),
                    'unidade_prisional' => $request->get('unidade_prisional'),
                    'pavilhao' => $request->get('pavilhao'),
                    'tempo_indeterminado' => $request->get('tempoIndeterminado'),
                    'created_at' => date("Y-m-d"),
                    'obs' => $request->get('obs'),
                ]
            );

            $idInsert = DB::getPdo()->lastInsertId();
            $idCautela = DB::getPdo()->lastInsertId();

            LogController::log($idInsert,"armaria_cautela","add", Auth::user()->cpf, "Inserção de Cautela");


            $periodoCautela = "TEMPO INDETERMINADO";
        }


//        dd($id, $insertCautela);

        $equipamentos = $request->get('hiddenEquipamentoCautela');
        //dd($equipamentos);

        foreach($equipamentos as $equipamento)
        {
            $pos = strpos( $equipamento, "/" );

            if ($pos === false) {
                // nao encontrado
                $insertEquipamento = DB::table('armaria_cautela_equipamento')->insert([
                        'id_cautela' => $idCautela,
                        'id_equipamento' => $equipamento,
                    ]
                );

            } else {
                $equip = explode('/', $equipamento);

                $insertEquipamento = DB::table('armaria_cautela_equipamento')->insert([
                        'id_cautela' => $idCautela,
                        'id_equipamento' => $equip[0],
                        'quantidade' => $equip[1],
                    ]
                );

            }
        }

        $responsavel = array();

        if($cautelaPara != "outros") {


            foreach ($servidores as $servidor) {
                $insertServidores = DB::table('armaria_cautela_servidores')->insert([
                        'id_cautela' => $idCautela,
                        'matricula' => $servidor,
                    ]
                );


                $dados = DB::connection('mysql2')->table('servidors')->whereIn('matricula', array($servidores))
                    ->leftJoin('lotacaos', 'lotacaos.id', '=', 'servidors.lotacao')
                    ->select('servidors.*', 'lotacaos.descricao')->get();

                if ($dados[0]->residencia_logradouro != "" || $dados[0]->residencia_logradouro != NULL) {
                    $endereco = $dados[0]->residencia_logradouro . ", " . $dados[0]->residencia_numero . ", " . $dados[0]->residencia_complemento . ", " . $dados[0]->residencia_bairro . ", " . $dados[0]->residencia_cidade . "-" . $dados[0]->residencia_estado;

                } else {
                    $endereco = "";
                }

                array_push($responsavel, array(
                    "nome" => utf8_encode($dados[0]->nome),
                    "matricula" => $dados[0]->matricula,
                    "cpf" => $dados[0]->cpf,
                    "lotacao" => utf8_encode($dados[0]->descricao),
                    "endereco" => utf8_encode($endereco),
                    "fone" => FacadeController::getDadosFoneServidor($dados[0]->id),
                ));

            }

            if ($cautelaPara == "unidade") {

                $insertUnidade = DB::table('armaria_cautela_unidade')->insert([
                        'fk_cautela' => $idCautela,
                        'unidade' => $request->get('unidade_prisional'),
                        'pavilhao' => $request->get('pavilhao'),
                    ]
                );


                if ($request->get('pavilhao') != null || $request->get('pavilhao') != "") {

                    $nomeUnidade = UnidadesController::getNomeUp($request->get('unidade_prisional')) . " - " . UnidadesController::getNomePav($request->get('pavilhao'));

                } else {
                    $nomeUnidade = UnidadesController::getNomeUp($request->get('unidade_prisional'));
                }

            } else {
                $nomeUnidade = "";
            }
        }

        if($cautelaPara == "outros")
        {

            $insertCautelaOutros = DB::table('armaria_cautela_outros')->insert([
                    'fk_cautela' => $idCautela,
                    'cpf' => $request->get('outrosCpf'),
                    'nome' => $request->get('outrosNome'),
                    'orgao' => $request->get('outrosOrgao'),
                    'telefone' => $request->get('outrosTelefone'),
                    'created_at' => Tdata::TtimeStampHojeMysql()
                ]
            );

            array_push($responsavel, array(
                'cpf' => $request->get('outrosCpf'),
                'nome' => $request->get('outrosNome'),
                'orgao' => $request->get('outrosOrgao'),
                'telefone' => $request->get('outrosTelefone'),
            ));

        }

        $obs = $request->get('obs');
        $numeroCautela = $idCautela;
        $dataCautela = date("d/m/Y");
        $localData = "Nísia Floresta, ".date("d")." de ".Tdata::getMesExt(date("m"))." de ".date("Y").".";


        return \PDF::loadView('armaria.cautela.pdfCautela', compact('responsavel','dados','numeroCautela','equipamentos','dataCautela','periodoCautela','nomeUnidade','localData','cautelaPara','obs'))
            // Se quiser que fique no formato a4 retrato: ->setPaper('a4', 'landscape')
            //->download('nome-arquivo-pdf-gerado.pdf');
                // mostra na tela
            //->stream();
            ->save(storage_path("app/arquivos/cautela/cautela_".$numeroCautela.".pdf"))->stream();

        //return view('armaria.cautela.pdfCautela');
    }

    public  function listaCautela(Request $request)
    {
//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }
//

        $unidades = DB::table('unidade')->where('troca','1')->orderBy('sigla', 'ASC')->get();

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        /**
         * se não realizar uma busca
         * - Evita que mostre todos os resultados sem realizar uma busca
         */
        if(!strpos($url,"?"))
        {

            $dados = DB::table('armaria_cautela')->select(DB::raw("DATE_FORMAT(data_inicial,'%d/%m/%Y') as dataInicial"), DB::raw("DATE_FORMAT(data_final,'%d/%m/%Y') as dataFinal"), 'tipo_cautela', 'tempo_indeterminado','id')->orderBy('id','DESC')->get();


        }else {

//dd($request->input());

            $listar = DB::table('armaria_cautela');

            $tipoArmamento = $request->get('tipoArmamento');
            $cautelaPara = $request->get('cautelaPara');
            $unidade_prisional = $request->get('unidade_prisional');
            $data_inicial = $request->get('data_inicial');
            $data_final = $request->get('data_final');
            $servidores = $request->get('agentesHidden');

            if ($tipoArmamento != null) {
                $listar->leftJoin('armaria_cautela_equipamento', 'armaria_cautela_equipamento.id_cautela', '=', 'armaria_cautela.id');
                $listar->leftJoin('armaria_equipamento', 'armaria_equipamento.id', '=', 'armaria_cautela_equipamento.id_equipamento');

                $listar->where('armaria_equipamento.equipamento_tipo', $tipoArmamento);
            }

            if ($servidores != null) {
                $listar->leftJoin('armaria_cautela_servidores', 'armaria_cautela_servidores.id_cautela', '=', 'armaria_cautela.id');
                $listar->where('armaria_cautela_servidores.matricula', $servidores);
            }

            if ($cautelaPara != null) {
                $listar->where('armaria_cautela.tipo_cautela', $cautelaPara);
            }

            if ($unidade_prisional != null) {
                $listar->where('armaria_cautela.unidade_prisional', $unidade_prisional);
            }

            if ($data_inicial != null) {
                $listar->where('armaria_cautela.created_at', '>=' , Tdata::TdataMysql($data_inicial));
            }

            if ($data_final != null) {
                $listar->where('armaria_cautela.created_at', '<=' , Tdata::TdataMysql($data_final));
            }

            $dados = $listar->select('armaria_cautela.*',DB::raw("DATE_FORMAT(armaria_cautela.data_inicial,'%d/%m/%Y') as dataInicial"), DB::raw("DATE_FORMAT(armaria_cautela.data_final,'%d/%m/%Y') as dataFinal"))->orderBy('id','DESC')->get();

        }


        return view('armaria.cautela.listaCautela', compact('dados','unidades'));
    }

    public  function listaDescautela(Request $request)
    {
//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }
//
        $unidades = DB::table('unidade')->where('troca','1')->orderBy('sigla', 'ASC')->get();

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        /**
         * se não realizar uma busca
         * - Evita que mostre todos os resultados sem realizar uma busca
         */
        if(!strpos($url,"?"))
        {

            $dados = DB::table('armaria_historico_cautela')->select(DB::raw("DATE_FORMAT(data_inicial,'%d/%m/%Y') as dataInicial"), DB::raw("DATE_FORMAT(data_final,'%d/%m/%Y') as dataFinal"), 'tipo_cautela', 'tempo_indeterminado','id')->orderBy('id','DESC')->get();


        }else {

            $listar = DB::table('armaria_historico_cautela');

            $tipoArmamento = $request->get('tipoArmamento');
            $cautelaPara = $request->get('cautelaPara');
            $unidade_prisional = $request->get('unidade_prisional');
            $data_inicial = $request->get('data_inicial');
            $data_final = $request->get('data_final');
            $servidores = $request->get('agentesHidden');

            if ($tipoArmamento != null) {
                $listar->leftJoin('armaria_historico_cautela_equipamento', 'armaria_historico_cautela_equipamento.id_cautela', '=', 'armaria_historico_cautela.id');
                $listar->leftJoin('armaria_historico_equipamento', 'armaria_historico_equipamento.id', '=', 'armaria_historico_cautela_equipamento.id_equipamento');

                $listar->where('armaria_historico_equipamento.equipamento_tipo', $tipoArmamento);
            }

            if ($servidores != null) {
                $listar->leftJoin('armaria_historico_cautela_servidores', 'armaria_historico_cautela_servidores.id_cautela', '=', 'armaria_historico_cautela.id');
                $listar->where('armaria_historico_cautela_servidores.matricula', $servidores);
            }

            if ($cautelaPara != null) {
                $listar->where('armaria_historico_cautela.tipo_cautela', $cautelaPara);
            }

            if ($unidade_prisional != null) {
                $listar->where('armaria_historico_cautela.unidade_prisional', $unidade_prisional);
            }

            if ($data_inicial != null) {
                $listar->where('armaria_historico_cautela.created_at', '>=' , Tdata::TdataMysql($data_inicial));
            }

            if ($data_final != null) {
                $listar->where('armaria_historico_cautela.created_at', '<=' , Tdata::TdataMysql($data_final));
            }

            $dados = $listar->select('armaria_historico_cautela.*',DB::raw("DATE_FORMAT(armaria_historico_cautela.data_inicial,'%d/%m/%Y') as dataInicial"), DB::raw("DATE_FORMAT(armaria_historico_cautela.data_final,'%d/%m/%Y') as dataFinal"))->orderBy('id','DESC')->get();

        }


        return view('armaria.cautela.listarDescautela', compact('dados','unidades'));
    }

    public function cautelaInformacao($id)
    {
        $cautela = DB::table("armaria_cautela")->where('id', $id)->get();

        $dadosCautela = array(
            'tipo' => $cautela[0]->tipo_cautela,
            'tempo_indeterminado' => $cautela[0]->tempo_indeterminado,
            'obs' => $cautela[0]->obs,
            'unidade' => UnidadesController::getNomeUp($cautela[0]->unidade_prisional),
            'dataInicial' => Tdata::TdataBR($cautela[0]->data_inicial),
            'dataFinal' => Tdata::TdataBR($cautela[0]->data_final),
        );

        if($cautela[0]->tipo_cautela == "servidor" || $cautela[0]->tipo_cautela == "unidade")
        {
            $dadosCautelaServidor = DB::table('armaria_cautela_servidores')->where('id_cautela', $id)->get();

            $dadosServidor = FacadeController::getDadosServidorPorMat($dadosCautelaServidor[0]->matricula);

            $fotoServidor = file_get_contents(config('app.rh').'/webservice/fotos-servidores/1wBd794bG5BFkHU71xltg6Gbo2Tx6a4y/'.$dadosServidor[0]->cpf);

        }else{
            $dadosServidor = null;
            $fotoServidor = null;
        }

        if($cautela[0]->tipo_cautela == "outros"){

            $dadosOutros = DB::table('armaria_cautela_outros')->where('fk_cautela', $id)->get();
            $fotoServidor = null;
        }

        $dadosEquipamentoCautela = self::getEquipamentoCautelado($id);


        return view('armaria.cautela.informacaoCautela', compact('dadosEquipamentoCautela','dadosServidor','dadosCautela','dadosOutros','id','fotoServidor'));

    }

    public function descautelaInformacao($id)
    {
        $cautela = DB::table("armaria_historico_cautela")->where('id', $id)->get();

        $dadosCautela = array(
            'tipo' => $cautela[0]->tipo_cautela,
            'tempo_indeterminado' => $cautela[0]->tempo_indeterminado,
            'obs' => $cautela[0]->obs,
            'unidade' => UnidadesController::getNomeUp($cautela[0]->unidade_prisional),
            'dataInicial' => Tdata::TdataBR($cautela[0]->data_inicial),
            'dataFinal' => Tdata::TdataBR($cautela[0]->data_final),
        );

        if($cautela[0]->tipo_cautela == "servidor" || $cautela[0]->tipo_cautela == "unidade")
        {
            $dadosCautelaServidor = DB::table('armaria_historico_cautela_servidores')->where('id_cautela', $id)->get();

            $dadosServidor = FacadeController::getDadosServidorPorMat($dadosCautelaServidor[0]->matricula);

        }else{
            $dadosServidor = null;
        }

        if($cautela[0]->tipo_cautela == "outros"){

            $dadosOutros = DB::table('armaria_historico_cautela_outros')->where('fk_cautela', $id)->get();
        }

        $dadosEquipamentoCautela = self::getEquipamentoDescautelado($id);


        return view('armaria.cautela.informacaoDescautela', compact('dadosEquipamentoCautela','dadosServidor','dadosCautela','dadosOutros','id'));

    }

    public function descautelaForm($id)
    {
        $cautela = DB::table("armaria_cautela")->where('id', $id)->get();

        $dadosEquipamento = DB::table('armaria_cautela_equipamento')
            ->leftJoin('armaria_equipamento','armaria_equipamento.id','=','armaria_cautela_equipamento.id_equipamento')
            ->where('armaria_cautela_equipamento.id_cautela', $id)
            ->select('armaria_cautela_equipamento.*','armaria_equipamento.equipamento_tipo')
            ->get();

        $dadosCautela = array(
            'tipo' => $cautela[0]->tipo_cautela,
            'tempo_indeterminado' => $cautela[0]->tempo_indeterminado,
            'obs' => $cautela[0]->obs,
            'unidade' => UnidadesController::getNomeUp($cautela[0]->unidade_prisional),
            'dataInicial' => Tdata::TdataBR($cautela[0]->data_inicial),
            'dataFinal' => Tdata::TdataBR($cautela[0]->data_final),
        );

        if($cautela[0]->tipo_cautela == "servidor" || $cautela[0]->tipo_cautela == "unidade")
        {
            $dadosCautelaServidor = DB::table('armaria_cautela_servidores')->where('id_cautela', $id)->get();

            $dadosServidor = FacadeController::getDadosServidorPorMat($dadosCautelaServidor[0]->matricula);

            $fotoServidor = file_get_contents(env('APP_RH, https://rhinfopenpb.mj.gov.br').'/webservice/fotos-servidores/1wBd794bG5BFkHU71xltg6Gbo2Tx6a4y/'.$dadosServidor[0]->cpf);

        }else{
            $dadosServidor = null;
            $fotoServidor = null;
        }

        if($cautela[0]->tipo_cautela == "outros"){

            $dadosOutros = DB::table('armaria_cautela_outros')->where('fk_cautela', $id)->get();
            $fotoServidor = null;
        }

        $dadosEquipamentoCautela = self::getEquipamentoCautelado($id);

//        dd($dadosEquipamentoCautela,$dadosEquipamento);

        return view('armaria.cautela.descautelaForm', compact('dadosEquipamento','dadosEquipamentoCautela','id'));
    }

    public function descautelaStore(Request $request, $id){

        $equipamentosCautelados = DB::table('armaria_cautela_equipamento')->where('id_cautela', $id)->select('id_equipamento')->get();
        $idsEquipamentos = array_column(json_decode($equipamentosCautelados), 'id_equipamento');
        $equipamentosDescautela = $request->get('equipamentoCheck');
        $equipamentosPermanecem = array_diff($idsEquipamentos, $request->get('equipamentoCheck'));
        //dd($equipamentosDescautela,count($equipamentosPermanecem));


        $dadosCautelaEquipamento = $request->get('equipamento');

        $dadosCautela = DB::table('armaria_cautela')->where('id', $id)->get();
        $dadosCautelaOutros = DB::table('armaria_cautela_outros')->where('fk_cautela', $id)->get();
        $dadosCautelaServidor = DB::table('armaria_cautela_servidores')->where('id_cautela', $id)->get();
        $dadosCautelaUnidade = DB::table('armaria_cautela_unidade')->where('fk_cautela', $id)->get();
        $servidores = array();


        foreach($dadosCautela as $dadoCautela)
        {
            DB::table("armaria_historico_cautela")->insert(get_object_vars($dadoCautela));
            DB::table("armaria_historico_cautela")->where('id',$id)->update(['created_at' => Tdata::TdataHojeMysql()]);

        }


        $equipamentos = array();

        foreach($dadosCautelaEquipamento as $dadosEquipamento)
        {

            $equipamento = explode("/",$dadosEquipamento);

            if(in_array($equipamento[1], $equipamentosDescautela, true)) {
                array_push($equipamentos, self::getEquipamentoDescautelaForm($equipamento[1], $equipamento[0]));

                $quantidadeCautela = DB::table('armaria_cautela_equipamento')->where('id_cautela', $id)->where('id_equipamento', $equipamento[1])->get()[0]->quantidade;

                //dd($equipamentos);

                            DB::table("armaria_historico_cautela_equipamento")->insert([
                                'id_cautela' => $id,
                                'id_equipamento' => $equipamento[1],
                                'quantidade' => $equipamento[0],
                            ]);

                if ($quantidadeCautela != "" || $quantidadeCautela != null) {

                    if ($quantidadeCautela > $equipamento[0]) {
                        $diferencaEquipamento = $quantidadeCautela - $equipamento[0];

                        $totalNovo = self::quantidadeEquipamento($equipamento[1]) - $diferencaEquipamento;

                        DB::table("armaria_equipamento_quantidade")->where('fk_equipamento', $equipamento[1])->update([
                            'quantidade' => $totalNovo,
                        ]);

                    }
                }
            }
        }


        if($dadosCautela[0]->tipo_cautela == "outros") {
            foreach ($dadosCautelaOutros as $dadoCautelaOutros) {
                DB::table("armaria_historico_cautela_outros")->insert(get_object_vars($dadoCautelaOutros));
            }
        }else{

            foreach ($dadosCautelaServidor as $dadoCautelaServidor) {
                DB::table("armaria_historico_cautela_servidores")->insert(get_object_vars($dadoCautelaServidor));
                array_push($servidores, $dadoCautelaServidor->matricula);
            }

            if(!is_null($dadosCautela[0]->unidade_prisional))
            {
                foreach ($dadosCautelaUnidade as $dadoCautelaUnidade) {
                    DB::table("armaria_historico_cautela_unidade")->insert(get_object_vars($dadoCautelaUnidade));
                }

            }

        }

        if(count($equipamentosPermanecem) > 0) {
            /*
             * equipamentos que permanecem cautelados e nova cautela
             */

            $insertCautela = DB::table('armaria_cautela')->insert([
                    'tipo_cautela' => $dadosCautela[0]->tipo_cautela,
                    'unidade_prisional' => $dadosCautela[0]->unidade_prisional,
                    'pavilhao' => $dadosCautela[0]->pavilhao,
                    'tempo_indeterminado' => $dadosCautela[0]->tempo_indeterminado,
                    'created_at' => date("Y-m-d"),
                    'obs' => $dadosCautela[0]->obs,
                ]
            );
            $idInsert = DB::getPdo()->lastInsertId();

            $equipamentosPermanecemCautelados = array();
            $equipamentoCauteladoTotal = self::getEquipamentoCautelado($id);
            foreach ($equipamentosPermanecem as $equipamentosPerman) {

                $key = array_search($equipamentosPerman, array_column($equipamentoCauteladoTotal, 'id'));
                array_push($equipamentosPermanecemCautelados, $equipamentoCauteladoTotal[$key]);
                $insertEquipamento = DB::table('armaria_cautela_equipamento')->insert([
                        'id_cautela' => $idInsert,
                        'id_equipamento' => $equipamentoCauteladoTotal[$key]['id'],
                        'quantidade' => $equipamentoCauteladoTotal[$key]['quantidade'],
                    ]
                );

            }

            if ($dadosCautela[0]->tipo_cautela == "outros") {
                foreach ($dadosCautelaOutros as $dadoCautelaOutros) {
                    $insertCautelaOutros = DB::table('armaria_cautela_outros')->insert([
                            'fk_cautela' => $idInsert,
                            'cpf' => $dadoCautelaOutros->cpf,
                            'nome' => $dadoCautelaOutros->nome,
                            'orgao' => $dadoCautelaOutros->orgao,
                            'telefone' => $dadoCautelaOutros->telefone,
                            'created_at' => date("Y-m-d"),
                        ]
                    );

                }
            } else {

                foreach ($dadosCautelaServidor as $dadoCautelaServidor) {
                    $insertServidores = DB::table('armaria_cautela_servidores')->insert([
                            'id_cautela' => $idInsert,
                            'matricula' => $dadoCautelaServidor->matricula,
                        ]
                    );
                }

                if (!is_null($dadosCautela[0]->unidade_prisional)) {
                    foreach ($dadosCautelaUnidade as $dadoCautelaUnidade) {
                        $insertUnidade = DB::table('armaria_cautela_unidade')->insert([
                                'fk_cautela' => $idInsert,
                                'unidade' => $dadoCautelaUnidade->unidade,
                                'pavilhao' => $dadoCautelaUnidade->pavilhao,
                            ]
                        );

                    }

                }

            }

            if($dadosCautela[0]->data_inicial != null || $dadosCautela[0]->data_final != null)
            {
                $periodoCautela = "De: ".Tdata::TdataBR($dadosCautela[0]->data_inicial)." Até: ".Tdata::TdataBR($dadosCautela[0]->data_final);

            }else{
                $periodoCautela = "TEMPO INDETERMINADO";
            }

            /*
             * ********************************************************
             */
        }else{
            $equipamentosPermanecemCautelados = null;
        }

        // move o arquivo da cautela para a pasta descautela

        $move = Storage::move(('arquivos/cautela')."/cautela_".$id.".pdf", "arquivos/descautela/cautela_".$id.".pdf");


        // dados para o PDF
        $numeroCautela = $id;
        $dataDescautela = date("d/m/Y");
        $dataCautela = date("d/m/Y");
        $localData = "Nísia Floresta, ".date("d")." de ".Tdata::getMesExt(date("m"))." de ".date("Y").".";
        $cautelaPara = $dadosCautela[0]->tipo_cautela;

        $responsavel = array();

        if($dadosCautela[0]->tipo_cautela != "outros") {

                $dados = DB::connection('mysql2')->table('servidors')->whereIn('matricula', array($servidores))
                    ->leftJoin('lotacaos', 'lotacaos.id', '=', 'servidors.lotacao')
                    ->select('servidors.*', 'lotacaos.descricao')->get();

                if ($dados[0]->residencia_logradouro != "" || $dados[0]->residencia_logradouro != NULL) {
                    $endereco = $dados[0]->residencia_logradouro . ", " . $dados[0]->residencia_numero . ", " . $dados[0]->residencia_complemento . ", " . $dados[0]->residencia_bairro . ", " . $dados[0]->residencia_cidade . "-" . $dados[0]->residencia_estado;

                } else {
                    $endereco = "";
                }

                array_push($responsavel, array(
                    "nome" => $dados[0]->nome,
                    "matricula" => $dados[0]->matricula,
                    "cpf" => $dados[0]->cpf,
                    "lotacao" => utf8_encode($dados[0]->descricao),
                    "endereco" => utf8_encode($endereco),
                    "fone" => FacadeController::getDadosFoneServidor($dados[0]->id),
                ));

            }

            if ($dadosCautela[0]->tipo_cautela == "unidade") {

                    $nomeUnidade = UnidadesController::getNomeUp($dadosCautela[0]->unidade_prisional);


            } else {
                $nomeUnidade = "";
            }


        if($dadosCautela[0]->tipo_cautela == "outros")
        {

            array_push($responsavel, array(
                'cpf' => $dadosCautelaOutros[0]->cpf,
                'nome' => $dadosCautelaOutros[0]->nome,
                'orgao' => $dadosCautelaOutros[0]->orgao,
                'telefone' => $dadosCautelaOutros[0]->telefone,
            ));

        }


        DB::table('armaria_cautela')->where('id', $id)->delete();
        DB::table('armaria_cautela_equipamento')->where('id_cautela', $id)->delete();
        DB::table('armaria_cautela_outros')->where('fk_cautela', $id)->delete();
        DB::table('armaria_cautela_servidores')->where('id_cautela', $id)->delete();
        DB::table('armaria_cautela_unidade')->where('fk_cautela', $id)->delete();


        if(count($equipamentosPermanecem) > 0) {

            $obs = $dadosCautela[0]->obs;
            $numeroCautela = $idInsert;
            \PDF::loadView('armaria.cautela.pdfCautelaEquipamentoPermanece', compact('responsavel', 'dados', 'numeroCautela', 'equipamentos', 'dataCautela', 'periodoCautela', 'nomeUnidade', 'localData', 'cautelaPara', 'obs','equipamentosPermanecemCautelados'))
                // Se quiser que fique no formato a4 retrato: ->setPaper('a4', 'landscape')
                //->download('nome-arquivo-pdf-gerado.pdf');
                // mostra na tela
                //->stream();
                ->save(storage_path("app/arquivos/cautela/cautela_" . $idInsert . ".pdf"));
        }

        return \PDF::loadView('armaria.cautela.descautela', compact('responsavel','dados','numeroCautela','equipamentos','dataDescautela','periodoCautela','nomeUnidade','localData','cautelaPara','obs','equipamentosPermanecemCautelados','dataCautela'))
            ->save(storage_path("app/arquivos/descautela/descautela_".$numeroCautela.".pdf"))->stream();



    }

    public function documentoCautela($id)
    {
//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }
//

        // se o arquivo existir no servidor
        if (file_exists(storage_path('app/'.Constantes::ARQUIVOS_CAUTELA . "/cautela_" .$id.".pdf"))) {

            return response()->file(storage_path('app/'.Constantes::ARQUIVOS_CAUTELA . "/cautela_" .$id.".pdf"));
        } else {
            return redirect()->back()->with('alert-danger', 'Arquivo não encontrado!');
        }

    }

    public function documentoCautelaHistorico($id)
    {
//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }


        // se o arquivo existir no servidor
        if (file_exists(storage_path('app/'.Constantes::ARQUIVOS_DESCAUTELA . "/cautela_" .$id.".pdf"))) {

            return response()->file(storage_path('app/'.Constantes::ARQUIVOS_DESCAUTELA . "/cautela_" .$id.".pdf"));
        } else {
            return redirect()->back()->with('alert-danger', 'Arquivo não encontrado!');
        }

    }

    public function documentoDescautela($id)
    {
//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }


        // se o arquivo existir no servidor
        if (file_exists(storage_path('app/'.Constantes::ARQUIVOS_DESCAUTELA . "/descautela_" .$id.".pdf"))) {

            return response()->file(storage_path('app/'.Constantes::ARQUIVOS_DESCAUTELA . "/descautela_" .$id.".pdf"));
        } else {
            return redirect()->back()->with('alert-danger', 'Arquivo não encontrado!');
        }

    }

    public function inserirTelefoneServidor(){

//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }
//

        return view('armaria.cautela.inserirTelefoneServidor');
    }

    public function inserirTelefoneServidorStore(Request $request){

//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }
//

        $matricula = $request->get('matricula');
        $telefone = $request->get('telefone');

        $dados = DB::connection('mysql2')->table('servidors')->where('matricula', $matricula)->select('servidors.id', 'servidors.nome', 'servidors.matricula')->get()[0];

        $verificaTelefone = DB::connection('mysql2')->table('contatos')->where('id_servidor', $dados->id)->count();

        if($verificaTelefone == 0)
        {
            $insertTelefone = DB::connection('mysql2')->table('contatos')->insert(['id_servidor' => $dados->id,'numero' => $telefone]);

            if($insertTelefone)
            {
                $insertTelefone = DB::connection('mysql2')->table('logaltservidors')->insert([
                    'id_servidor' => $dados->id,
                    'matricula_usuario' => $dados->matricula,
                    'data' => Tdata::TdataHojeMysql(),
                    'hora' => Tdata::hora(),
                    'texto' => "Inclusao de telefone do servidor: <strong>".$telefone."</strong><br>Inclusao realizada pelo setor de armaria",
                    'nome_usuario' => $dados->nome,
                ]);

                return redirect()->route('armaria.cautela')->with('alert-success', 'Telefone inserido com sucesso!');
            }
        }else{
            return redirect()->back()->with('alert-danger', "O servidor <strong>".$dados->nome."</strong> já possui telefone cadastrado.");
        }



    }


    public function mapaEquipamento(Request $request)
    {
//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }
//
        $unidadePrisional = $request->get('unidade_prisional');

        $unidades = DB::table('unidade')->where('troca','1')->orderBy('sigla', 'ASC')->get();

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if(!strpos($url,"?"))
        {

            $dados = false;

        }else {

            $dados = true;

            if ($unidadePrisional == 0) {


                $dei = DB::table('armaria_equipamento')
                    ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
                    ->where('armaria_equipamento.equipamento_tipo', 'dei')
                    ->where('armaria_equipamento.status', 'A')
                    ->whereNotIn('armaria_equipamento.id',function($query) {
                    $query->select('id_equipamento')->from('armaria_cautela_equipamento');
                    })
                    ->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome')
                    ->get();

                $armaFogo = DB::table('armaria_equipamento')
                    ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
                    ->where('armaria_equipamento.equipamento_tipo', 'armaFogo')
                    ->where('armaria_equipamento.status', 'A')
                    ->whereNotIn('armaria_equipamento.id',function($query) {
                    $query->select('id_equipamento')->from('armaria_cautela_equipamento');
                    })
                    ->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome')
                    ->get();



                $municaoDados = DB::table('armaria_equipamento')
                    ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
                    ->where('armaria_equipamento.equipamento_tipo', 'municao')
                    ->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome','armaria_equipamento.id AS idEquipamento')
                    ->get();

                $municao = array();
                foreach ($municaoDados as $municaoDado) {

                    //junta os valores de equipamentos iguais (quando tiver a ficha de uso, subtrair os valores)
                    $id = $municaoDado->idEquipamento;
                    $key = array_search($id, array_column($municao, 'idEquipamento'));
                    if ($key !== false) {
                        //$municao[$key]['quantidade'] = $municao[$key]['quantidade'] + $municaoDado->quantidade;
                    } else {

                        array_push($municao, array(
                            'idEquipamento' => $municaoDado->idEquipamento,
                            'equipamento_especie' => $municaoDado->equipamento_especie,
                            'equipamento_especificacao' => $municaoDado->equipamento_especificacao,
                            'fabNome' => $municaoDado->fabNome,
                            'modelo' => $municaoDado->modelo,
                            'calibre' => $municaoDado->calibre,
                            'quantidade' => self::estoqueEquipamento($municaoDado->idEquipamento),
                        ));
                    }
                }

                $municao = json_decode(json_encode($municao), FALSE);

                $carregadorDados = DB::table('armaria_equipamento')
                    ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
                    ->where('armaria_equipamento.equipamento_tipo', 'carregador')
                    ->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome','armaria_equipamento.id AS idEquipamento')
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
                            'quantidade' => self::estoqueEquipamento($carregadorDado->idEquipamento),
                        ));
                    }
                }

                $carregador = json_decode(json_encode($carregador), FALSE);

                $algemaDados = DB::table('armaria_equipamento')
                    ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
                    ->where('armaria_equipamento.equipamento_tipo', 'algema')
                    ->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome','armaria_equipamento.id AS idEquipamento')
                    ->get();

                $algemas = array();
                foreach ($algemaDados as $algemaDado) {

                    $id = $algemaDado->idEquipamento;
                    $key = array_search($id, array_column($algemas, 'idEquipamento'));
                    if ($key !== false) {
                        $algemas[$key]['quantidade'] = $algemas[$key]['quantidade'] + $algemaDado->quantidade;
                    } else {

                        array_push($algemas, array(
                            'idEquipamento' => $algemaDado->idEquipamento,
                            'n_serie' => $algemaDado->n_serie,
                            'equipamento_tipo' => $algemaDado->equipamento_tipo,
                            'equipamento_especie' => $algemaDado->equipamento_especie,
                            'fabNome' => $algemaDado->fabNome,
                            'quantidade' => self::estoqueEquipamento($algemaDado->idEquipamento),
                        ));
                    }
                }

                $algemas = json_decode(json_encode($algemas), FALSE);

                $granadasDados = DB::table('armaria_equipamento')
                    ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
                    ->where('armaria_equipamento.equipamento_tipo', 'granada')
                    ->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome','armaria_equipamento.id AS idEquipamento')
                    ->get();

                $granadas = array();
                foreach ($granadasDados as $granadasDado) {

                    $id = $granadasDado->idEquipamento;
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
                            'quantidade' => self::estoqueEquipamento($granadasDado->idEquipamento),
                        ));
                    }
                }

                $granadas = json_decode(json_encode($granadas), FALSE);

                $epiDados = DB::table('armaria_equipamento')
                    ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
                    ->where('armaria_equipamento.equipamento_tipo', 'epi')
                    ->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome','armaria_equipamento.id AS idEquipamento')
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
                            'quantidade' => self::estoqueEquipamento($epiDado->idEquipamento),
                        ));
                    }
                }

                $epis = json_decode(json_encode($epis), FALSE);

                $coleteDados = DB::table('armaria_equipamento')
                    ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
                    ->where('armaria_equipamento.equipamento_tipo', 'colete')
                    ->where('armaria_equipamento.status', 'A')
                    ->whereNotIn('armaria_equipamento.id',function($query) {
                        $query->select('id_equipamento')->from('armaria_cautela_equipamento');
                    })
                    ->select('armaria_equipamento.*','armaria_equipamento.id AS idEquipamento', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome')
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

                $espargidorDados = DB::table('armaria_equipamento')
                    ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
                    ->where('armaria_equipamento.equipamento_tipo', 'espargidor')
                    ->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome','armaria_equipamento.id AS idEquipamento')
                    ->get();

                $espargidores = array();
                foreach ($espargidorDados as $espargidorDado) {

                    $id = $espargidorDado->idEquipamento;
                    $key = array_search($id, array_column($espargidores, 'idEquipamento'));
                    if ($key !== false) {
                        $espargidores[$key]['quantidade'] = $espargidores[$key]['quantidade'] + $espargidorDado->quantidade;
                    } else {

                        array_push($espargidores, array(
                            'idEquipamento' => $espargidorDado->idEquipamento,
                            'equipamento_tipo' => $espargidorDado->equipamento_tipo,
                            'modelo' => $espargidorDado->modelo,
                            'fabNome' => $espargidorDado->fabNome,
                            'quantidade' => self::estoqueEquipamento($espargidorDado->idEquipamento),
                        ));
                    }
                }

                $espargidores = json_decode(json_encode($espargidores), FALSE);

            } else {

                $dei = DB::table('armaria_cautela')
                    ->leftJoin('armaria_cautela_equipamento', 'armaria_cautela_equipamento.id_cautela', '=', 'armaria_cautela.id')
                    ->leftJoin('armaria_equipamento', 'armaria_equipamento.id', '=', 'armaria_cautela_equipamento.id_equipamento')
                    ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
                    ->where('armaria_cautela.tipo_cautela', 'unidade')
                    ->where('armaria_equipamento.equipamento_tipo', 'dei')
                    ->where('armaria_equipamento.status', 'A')
                    ->where('armaria_cautela.unidade_prisional', $unidadePrisional)
                    ->select('armaria_cautela.*', 'armaria_equipamento.*', 'armaria_cautela_equipamento.*', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome')
                    ->get();

                $armaFogo = DB::table('armaria_cautela')
                    ->leftJoin('armaria_cautela_equipamento', 'armaria_cautela_equipamento.id_cautela', '=', 'armaria_cautela.id')
                    ->leftJoin('armaria_equipamento', 'armaria_equipamento.id', '=', 'armaria_cautela_equipamento.id_equipamento')
                    ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
                    ->where('armaria_cautela.tipo_cautela', 'unidade')
                    ->where('armaria_equipamento.equipamento_tipo', 'armaFogo')
                    ->where('armaria_equipamento.status', 'A')
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
                    $qtdUsoMaterialUnidade = self::getQuantidadeUsoMaterialUnidade($id, $unidadePrisional);
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
                    $qtdUsoMaterialUnidade = self::getQuantidadeUsoMaterialUnidade($id, $unidadePrisional);
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
                    ->where('armaria_equipamento.status', 'A')
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
                    $qtdUsoMaterialUnidade = self::getQuantidadeUsoMaterialUnidade($id, $unidadePrisional);
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
            }
        }
        // dd($epis);

        return view('armaria.mapa.mapaEquipamento', compact('unidades','armaFogo','municao','carregador','algemas','granadas','epis','coletes','espargidores','dei','dados'));
    }

    public function localizacaoEquipamento($id)
    {
        $localizacao = DB::table('armaria_cautela_equipamento')
            ->leftJoin('armaria_cautela','armaria_cautela.id','=','armaria_cautela_equipamento.id_cautela')
            ->leftJoin('armaria_equipamento','armaria_equipamento.id','=','armaria_cautela_equipamento.id_equipamento')
            ->leftJoin('armaria_cautela_outros','armaria_cautela_outros.fk_cautela','=','armaria_cautela.id')
            ->leftJoin('armaria_cautela_servidores','armaria_cautela_servidores.id_cautela','=','armaria_cautela.id')
            ->leftJoin('armaria_equipamento_fabricante','armaria_equipamento_fabricante.id','=','armaria_equipamento.fk_fabricante')
            ->where('armaria_cautela_equipamento.id_equipamento', $id)
            ->select('armaria_cautela.*','armaria_cautela.unidade_prisional as unidadeCautela','armaria_equipamento.*','armaria_equipamento_fabricante.nome AS fabNome','armaria_cautela_outros.*','armaria_cautela_servidores.*')
            ->get();

        if(count($localizacao) == 0)
        {
            return redirect()->back()->with('alert-danger', 'Equipamento no setor bélico.');
        }

        $dado = array();
        foreach($localizacao as $loc)
        {
            if($loc->tipo_cautela == "unidade")
            {
                $nomeUnidade = UnidadesController::getNomeUp($loc->unidade_prisional);
            }else{
                $nomeUnidade = "** Cautela Individual **";
            }
            if($loc->cpf != null)
            {
                $dadosResponsavel = "$loc->nome ($loc->cpf), $loc->orgao, contato: $loc->telefone";

            }else{
                $dadosServidor = FacadeController::getServidorMatricula($loc->matricula);
                $dadosResponsavel = $dadosServidor[0]->nome." (".$dadosServidor[0]->cpf."), SEAP, contato: ".$dadosServidor[0]->numero;
            }
            array_push($dado, array(
               'equipamento_tipo' => $loc->equipamento_tipo,
               'modelo' => $loc->modelo,
               'fabNome' => $loc->fabNome,
               'calibre' => $loc->calibre,
               'status' => $loc->status,
               'equipamento_especificacao' => $loc->equipamento_especificacao,
               'n_serie' => $loc->n_serie,
               'tamanho' => $loc->tamanho,
               'nomeUnidade' => $nomeUnidade,
               'responsavel' => $dadosResponsavel,
            ));
        }

        $dado = json_decode(json_encode($dado), FALSE);

        return view('armaria.localizacaoEquipamento.localizacaoEquipamento', compact('dado'));
    }

    public function relatorioUsoMaterial(Request $request)
    {
//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }

        $unidadePrisional = $request->get('unidade_prisional');

        $unidades = DB::table('unidade')->where('troca','1')->orderBy('sigla', 'ASC')->get();

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if(!strpos($url,"?"))
        {
            $dadosUso = DB::table('armaria_uso_material_unidade')->orderBy('created_at', 'DESC')->limit('50')->get();

            $dados = array();

            foreach ($dadosUso as $dU) {
                array_push($dados, array(
                    'id' => $dU->id,
                    'data' => Tdata::TtimestampBR($dU->created_at),
                    'servidor' => FacadeController::getNomeServidor($dU->servidor),
                    'matricula' => $dU->servidor,
                    'unidade' => UnidadesController::getSiglaUp($dU->unidade),
                ));
            }

            $dados = json_decode(json_encode($dados), FALSE);


        }else {

            $dadosUso = DB::table('armaria_uso_material_unidade')->where('unidade', $unidadePrisional)->orderBy('created_at', 'DESC')->get();

            $dados = array();

            foreach ($dadosUso as $dU) {
                array_push($dados, array(
                    'id' => $dU->id,
                    'data' => Tdata::TtimestampBR($dU->created_at),
                    'servidor' => FacadeController::getNomeServidor($dU->servidor),
                    'matricula' => $dU->servidor,
                    'unidade' => UnidadesController::getSiglaUp($dU->unidade),
                ));
            }

            $dados = json_decode(json_encode($dados), FALSE);
        }

        return view('armaria.relatorioUsoMaterial.listarRelatorioUso', compact('dados','unidades'));

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

        return view('armaria.relatorioUsoMaterial.visualizarRelatorioUso', compact('dados'));

    }

    public function extravioEquipamento($id)
    {
        $dadosEquipamento = DB::table('armaria_equipamento')->where('id',$id)->get()[0];

        $textoEquipamento = self::getTextoEquipamento($id);

        return view('armaria.extravioEquipamento.extravioForm', compact('id','dadosEquipamento','textoEquipamento'));
    }

    public function extravioEquipamentoStore(Request $request, $id)
    {
        //dd($request->input(), $id);

        $dados = DB::table('armaria_equipamento')->where('id',$id)->get()[0];

        $textoEquipamento = self::getTextoEquipamento($id);
        if($dados->status == 'I')
        {
            return redirect()->route('armaria.buscar')->with('alert-danger', 'O registro de extravio desse equipamento já foi informado. '.$textoEquipamento[0]['equipamento']);
        }else{

            if($dados->equipamento_tipo == "armaFogo" || $dados->equipamento_tipo == "colete" || $dados->equipamento_tipo == "dei")
            {
                $inativar = DB::table('armaria_equipamento')->where('id',$id)->update([
                    'status' => 'I'
                ]);

                DB::table('armaria_equipamento_extravio')->insert([
                    'fk_equipamento' => $id,
                    'tipo_equipamento' => $dados->equipamento_tipo,
                    'quantidade' => $request->get('quantidade'),
                    'motivacao' => $request->get('motivacao'),
                    'created_at' => Tdata::TtimeStampHojeMysql(),
                ]);

                if($inativar)
                {
                    LogController::log($id,"armaria_equipamento_quantidade","update", Auth::user()->cpf, "Inativação de equipamento proviniente de extravio. Equipamento: ".$textoEquipamento[0]['equipamento']);

                    return redirect()->route('armaria.buscar')->with('alert-success', 'Extravio Registrado com sucesso');

                }

            }else{

                DB::table('armaria_equipamento_extravio')->insert([
                    'fk_equipamento' => $id,
                    'tipo_equipamento' => $dados->equipamento_tipo,
                    'quantidade' => $request->get('quantidade'),
                    'motivacao' => $request->get('motivacao'),
                    'created_at' => Tdata::TtimeStampHojeMysql(),
                ]);

                $qtd = DB::table('armaria_equipamento_quantidade')->where('fk_equipamento', $id)->count();

                if($qtd > 0) {

                    $quantidadeAnterior = DB::table('armaria_equipamento_quantidade')->where('fk_equipamento', $id)->get()[0]->quantidade;

                    $novaQuantidade = $quantidadeAnterior - $request->get('quantidade');

                $alterarQTD = DB::table('armaria_equipamento_quantidade')->where('fk_equipamento', $id)->update([
                    'quantidade' => $novaQuantidade
                ]);


                if ($alterarQTD) {
                    LogController::log($id, "armaria_equipamento_quantidade", "update", Auth::user()->cpf, "Alteração de quantidade do estoque proviniente de extravio de equipamento: " . $textoEquipamento[0]['equipamento']);

                    return redirect()->route('armaria.buscar')->with('alert-success', 'Extravio Registrado com sucesso');

                }

                }

                else{

                    return redirect()->route('armaria.buscar')->with('alert-danger', 'O equipamento não está em estoque!');

                }

            }

        }

    }

    public function extravioEquipamentoListar()
    {
        $dados = DB::table('armaria_equipamento_extravio')->get();

        $equipamentos = array();

        foreach($dados as $dado)
        {
            $dadosEquip = DB::table('armaria_equipamento')->where('id', $dado->fk_equipamento)->get();

            array_push($equipamentos, array(
                'equipamento' => self::getTextoEquipamento($dado->fk_equipamento)[0]['equipamento'],
                'tipo' => $dado->tipo_equipamento,
                'quantidade' => $dado->quantidade,
                'id' => $dado->fk_equipamento,
                'motivacao' => $dado->motivacao,
                'status' => $dadosEquip[0]->status,
            ));
        }

        $equipamentos = collect($equipamentos)->sortBy('tipo');

        return view('armaria.extravioEquipamento.extravioListar', compact('equipamentos'));
    }

    public function extravioEquipamentoAtivar($id)
    {
        $ativar = DB::table('armaria_equipamento')->where('id',$id)->update([
            'status' => 'A'
        ]);

        if($ativar)
        {
            LogController::log($id, "armaria_equipamento", "update", Auth::user()->cpf, "Ativação de equipamento extraviado.");

            return redirect()->route('armaria.buscar')->with('alert-success', 'Equipamento Ativado com sucesso!');

        }

    }

    public function EquipamentoHistorico($id)
    {
        $equipamento = self::getTextoEquipamento($id)[0]['equipamento'];

        $cautelas = DB::table('armaria_cautela_equipamento')
            ->leftJoin('armaria_cautela','armaria_cautela.id','=','armaria_cautela_equipamento.id_cautela')
            ->where('armaria_cautela_equipamento.id_equipamento', $id)
            ->select('armaria_cautela.*')->get();

        $descautelas = DB::table('armaria_historico_cautela_equipamento')
            ->leftJoin('armaria_historico_cautela','armaria_historico_cautela.id','=','armaria_historico_cautela_equipamento.id_cautela')
            ->where('armaria_historico_cautela_equipamento.id_equipamento', $id)
            ->select('armaria_historico_cautela.*')->get();

//       dd($cautelas, $descautelas);

        $historicoCautela = array();
        foreach($cautelas as $cautela)
        {

            if($cautela->tipo_cautela == "servidor" || $cautela->tipo_cautela == "unidade")
            {

                $dadosCautelaServidor = DB::table('armaria_cautela_servidores')->where('id_cautela', $cautela->id)->get();

                if($cautela->tipo_cautela == "unidade")
                {
                    $nomeUnidade = UnidadesController::getNomeUp($cautela->unidade_prisional);
                    $dadosServidor = FacadeController::getServidorMatricula($dadosCautelaServidor[0]->matricula);
                    $dadosResponsavel = $dadosServidor[0]->nome." (".$dadosServidor[0]->cpf."), SEAP, contato: ".$dadosServidor[0]->numero;


                }else{

                    $nomeUnidade = "Cautela Individual";
                    $dadosServidor = FacadeController::getServidorMatricula($dadosCautelaServidor[0]->matricula);
                    $dadosResponsavel = $dadosServidor[0]->nome." (".$dadosServidor[0]->cpf."), SEAP, contato: ".$dadosServidor[0]->numero;

                }


            }else{
                $dadosServidor = null;
            }

            if($cautela->tipo_cautela == "outros"){

                $dadosOutros = DB::table('armaria_cautela_outros')->where('fk_cautela', $id)->get();
                $dadosResponsavel = "$dadosOutros->nome ($dadosOutros->cpf), $dadosOutros->orgao, contato: $dadosOutros->telefone";
            }


            array_push($historicoCautela, array(
                'tipoHistorico' => 'cautela',
                'cautelaDestinoTipo' => $nomeUnidade,
                'cautelaDestinoDados' => $dadosResponsavel,
                'created_at' => $cautela->created_at,
                'data' => Tdata::TdataBR($cautela->created_at),
            ));
        }


        $historicoDescautela = array();
        foreach($descautelas as $descautela)
        {

            if($descautela->tipo_cautela == "servidor" || $descautela->tipo_cautela == "unidade")
            {

                $dadosCautelaServidor = DB::table('armaria_historico_cautela_servidores')->where('id_cautela', $descautela->id)->get();

                if($descautela->tipo_cautela == "unidade")
                {
                    $nomeUnidade = UnidadesController::getNomeUp($descautela->unidade_prisional);
                    $dadosServidor = FacadeController::getServidorMatricula($dadosCautelaServidor[0]->matricula);
                    $dadosResponsavel = $dadosServidor[0]->nome." (".$dadosServidor[0]->cpf."), SEAP, contato: ".$dadosServidor[0]->numero;


                }else{

                    $nomeUnidade = "Cautela Individual";
                    $dadosServidor = FacadeController::getServidorMatricula($dadosCautelaServidor[0]->matricula);
                    $dadosResponsavel = $dadosServidor[0]->nome." (".$dadosServidor[0]->cpf."), SEAP, contato: ".$dadosServidor[0]->numero;

                }


            }else{
                $dadosServidor = null;
            }

            if($descautela->tipo_cautela == "outros"){

                $dadosOutros = DB::table('armaria_cautela_outros')->where('fk_cautela', $id)->get();
                $dadosResponsavel = "$dadosOutros->nome ($dadosOutros->cpf), $dadosOutros->orgao, contato: $dadosOutros->telefone";
            }


            array_push($historicoDescautela, array(
                'tipoHistorico' => 'descautela',
                'cautelaDestinoTipo' => $nomeUnidade,
                'cautelaDestinoDados' => $dadosResponsavel,
                'created_at' => $descautela->created_at,
                'data' => Tdata::TdataBR($descautela->created_at),
            ));
        }

        $historico = array_merge($historicoCautela, $historicoDescautela);

        $sort = [
            ['column' => 'created_at', 'order' => 'desc'],
        ];

        $historico = collect($historico);
        $historico = self::multiPropertySort($historico, $sort);

        return view('armaria.cautela.historicoEquipamento', compact('equipamento','historico'));
    }

    public function mapaLocalizacaoEquipamento(Request $request)
    {

        $tipoEquipamento = $request->get('tipoEquipamento');

        $modelosEquipamento = DB::table('armaria_cautela_equipamento')
            ->leftJoin('armaria_cautela','armaria_cautela.id','=','armaria_cautela_equipamento.id_cautela')
            ->leftJoin('armaria_equipamento','armaria_equipamento.id','=','armaria_cautela_equipamento.id_equipamento')
            ->where('armaria_equipamento.equipamento_tipo', "armaFogo")
            ->where('armaria_cautela.tipo_cautela','unidade')
            ->where('armaria_equipamento.modelo','!=','')
            ->select('armaria_equipamento.modelo')
            ->distinct()
            ->get();


        if($tipoEquipamento == "colete"){
            $mapaCautela = DB::table('armaria_cautela_equipamento')
                ->leftJoin('armaria_cautela','armaria_cautela.id','=','armaria_cautela_equipamento.id_cautela')
                ->leftJoin('armaria_equipamento','armaria_equipamento.id','=','armaria_cautela_equipamento.id_equipamento')
                ->where('armaria_equipamento.equipamento_tipo', "colete")
                ->where('armaria_cautela.tipo_cautela','unidade')
                ->select('armaria_cautela_equipamento.*', 'armaria_cautela.*', 'armaria_equipamento.*', 'armaria_cautela_equipamento.id_equipamento AS idEquip')
                ->get();

        }

        //dd($mapaColete);

        else{
            $mapaCautela = DB::table('armaria_cautela_equipamento')
                ->leftJoin('armaria_cautela','armaria_cautela.id','=','armaria_cautela_equipamento.id_cautela')
                ->leftJoin('armaria_equipamento','armaria_equipamento.id','=','armaria_cautela_equipamento.id_equipamento')
                ->where('armaria_equipamento.equipamento_tipo', "armaFogo")
                ->where('armaria_cautela.tipo_cautela','unidade')
                ->where('armaria_equipamento.modelo', $request->get('modelo'))
                ->select('armaria_cautela_equipamento.*', 'armaria_cautela.*', 'armaria_equipamento.*', 'armaria_cautela_equipamento.id_equipamento AS idEquip')
                ->get();

        }

        $dadosMapa = array();
        foreach($mapaCautela as $mapa)
        {
            array_push($dadosMapa, array(
                'unidade' => UnidadesController::getNomeUp($mapa->unidade_prisional),
                'equipamentoModelo' => $mapa->modelo,
                'equipamentoTexto' => self::getTextoEquipamento($mapa->idEquip)[0]['equipamento'],
                'equipamentoEspecie' => $mapa->equipamento_especie
            ));
        }

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if(strpos($url,"?")) {

            $equipamentoArmaria = DB::table('armaria_equipamento')
                ->leftJoin('armaria_equipamento_fabricante', 'armaria_equipamento_fabricante.id', '=', 'armaria_equipamento.fk_fabricante')
                ->where('armaria_equipamento.modelo', $request->get('modelo'))
                ->where('armaria_equipamento.status', 'A')
                ->whereNotIn('armaria_equipamento.id', function ($query) {
                    $query->select('id_equipamento')->from('armaria_cautela_equipamento');
                })
                ->select('armaria_equipamento.*', 'armaria_equipamento_fabricante.*', 'armaria_equipamento_fabricante.nome as fabNome', 'armaria_equipamento.id AS idEquip')
                ->get();

            foreach ($equipamentoArmaria as $equipArmaria) {
                array_push($dadosMapa, array(
                    'unidade' => "SETOR BÉLICO",
                    'equipamentoModelo' => $equipArmaria->modelo,
                    'equipamentoTexto' => self::getTextoEquipamento($equipArmaria->idEquip)[0]['equipamento'],
                    'equipamentoEspecie' => $equipArmaria->equipamento_especie
                ));
            }
        }

        $localizacaoArmamento = array();
        foreach($dadosMapa as $array){
            $localizacaoArmamento[$array["equipamentoModelo"]][$array["unidade"]][] = $array;
        }
        $localizacaoArmamento = array_sort_recursive($localizacaoArmamento);

        return view('armaria.mapa.localizacaoPorEquipamento', compact('localizacaoArmamento','modelosEquipamento'));

    }

    /**
     * funcoes acessadas pelo ajax
     */
    public function fabricanteEquipamento(Request $request)
    {
//        if (Auth::user()->tipo != 400 && Auth::user()->tipo != 1000 ) {
//
//            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
//
//        }
//

        $select = DB::table('armaria_equipamento_fabricante')->where('tipo',$request->get('tipo'))->select('id','nome')->orderBy('nome','asc')->get();
        return (json_decode($select, true));
    }

    public static function multiPropertySort($collection, array $sorting_instructions){

        return $collection->sort(function ($a, $b) use ($sorting_instructions){

            foreach($sorting_instructions as $sorting_instruction){

                $a[$sorting_instruction['column']] = (isset($a[$sorting_instruction['column']])) ? $a[$sorting_instruction['column']] : '';
                $b[$sorting_instruction['column']] = (isset($b[$sorting_instruction['column']])) ? $b[$sorting_instruction['column']] : '';

                if(empty($sorting_instruction['order']) or strtolower($sorting_instruction['order']) == 'asc'){
                    $x = ($a[$sorting_instruction['column']] <=> $b[$sorting_instruction['column']]);
                }else{
                    $x = ($b[$sorting_instruction['column']] <=> $a[$sorting_instruction['column']]);

                }

                if($x != 0){
                    return $x;
                }

            }

            return 0;

        });
    }

}