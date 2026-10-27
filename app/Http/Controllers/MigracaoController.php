<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Classes\Tdata;
use App\Jobs\Confere;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class MigracaoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     *
     */
    public function __construct()
    {
        ini_set('max_execution_time', 900000);        
    }

    
    
//    public static function paisDeID($id) {
//            return DB::table('_mig_estrutura_fisica_csv')->where('ID_ESTRUTURA_FISICA_PAI', $id)->get();
//    }
    
    
    // unidade, x, pavilhao, ala, cela
    
    public $nivelMaximo = 0;
    public $answer = [];
    
    public function calculaNiveis($id, $nivel = 0) {
        $estrutura_fisica = DB::table('_mig_estrutura_fisica_csv')->where('ID_ESTRUTURA_FISICA', $id)->first();
                
        if(!is_null($estrutura_fisica->ID_ESTRUTURA_FISICA_PAI)){ // tem pai
            $this->calculaNiveis($estrutura_fisica->ID_ESTRUTURA_FISICA_PAI, $nivel + 1);
        } else { // não tem pai
            if($this->nivelMaximo < $nivel) {
                $this->nivelMaximo = $nivel;
            }
            array_push($this->answer, [$estrutura_fisica->ID_ESTRUTURA_FISICA => $estrutura_fisica->NOME]);
        }
    }
    
    public function calculaNivelMaximo(){
        $estruturas_fisicas = DB::table('_mig_estrutura_fisica_csv')->get();
        
        foreach($estruturas_fisicas as $estrutura_fisica){
            $this->calculaNiveis($estrutura_fisica->ID_ESTRUTURA_FISICA);
        }
    }

    public function criaEstruturaUmNivelAcimaDoPV(){
        $pavilhoes = DB::table('pavilhao')->get();
        foreach($pavilhoes as $pavilhao){
            $estrutura_fisica = DB::table('_mig_estrutura_fisica_csv')->where('ID_ESTRUTURA_FISICA', $pavilhao->id)->first();
            // se a estrutura fisica tem pai, concatena o nome dela com hífena no nome do pavilhão
            if(!is_null($estrutura_fisica) && !is_null($estrutura_fisica->ID_ESTRUTURA_FISICA_PAI)){
                $estrutura_fisica_pai = DB::table('_mig_estrutura_fisica_csv')->where('ID_ESTRUTURA_FISICA', $estrutura_fisica->ID_ESTRUTURA_FISICA_PAI)->first();
                $pavilhoes = DB::table('pavilhao')->where('id', $pavilhao->id)->update([
                    'nome' => $pavilhao->nome.' - '.$estrutura_fisica_pai->NOME
                ]);
            }
        }
    }
    
    public function updateDocumentosInterno() {
        
                
        $documentos = DB::table('_mig_interno_documentos_csv')->get();
        
        //dd($documentos);
        
        foreach($documentos as $documento)
        {
            if(!is_null($documento->RG_EXPEDICAO))
            {
                $dataEmissaoRg = Tdata::TdataMysql($documento->RG_EXPEDICAO);
            }else{
                $dataEmissaoRg = null;
            }
            
            
            DB::table('interno')->where('id', $documento->ID_CUSTODIADO)
                    ->update([
                        'cpf' => $documento->CPF,
                        'rg_numero' => $documento->RG_NUMERO,
                        'rg_orgaoexpedidor' => $documento->RG_ORGAO_EMISSOR,
                        'rg_data' => $dataEmissaoRg,
                    ]);
        }
        
        dd("FIM - updateDocumentosInterno");
        
    }

    public static function calcularMovsPendentes()
    {
        $listaInternos = DB::table('interno')->select('id')->get()->pluck('id');

        $movs = DB::table('movimentacao_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao_interno.status','!=','Cancelada')
            ->orderBy('movimentacao.data','DESC')
            ->orderBy('movimentacao.id','DESC')
            ->get();
        $movs = json_decode($movs, true);
        error_log('iniciou');
        foreach($listaInternos as $internoId){

            $ultimasMovs = array_filter(
                $movs, 
                function ($item) use ($internoId) {
                    return $item['fk_interno'] == $internoId;
                }
            );
            
            // pega a posição da transferencia mais recente
            $posicao = -1;
            foreach($ultimasMovs as $key=>$mov){
                if($mov['tipo'] == "transferencia"){
                    $posicao = $key;
                }
            }
            // se entre a movimentação mais recente e a transferência mais recente não tiver transferenciaentrada nem entradaunidadeprisional, atualiza para pendente
            foreach($ultimasMovs as $key=>$mov){
                if($key == $posicao) {
                    $internoDados = DB::table('interno')->where('id',$mov['fk_interno'])->first();
                    if($internoDados->status == 'I'){
                        continue;
                    }
                    DB::table('movimentacao_interno')
                    ->where('movimentacao_interno.fk_movimentacao', $mov['fk_movimentacao'])
                    ->where('movimentacao_interno.fk_interno', $mov['fk_interno'])
                    ->where('movimentacao_interno.status','!=','Cancelada')
                    ->update(
                        [
                            'movimentacao_interno.status' => 'Pendente'
                        ]
                    );
                    DB::table('interno')
                    ->where('interno.id','=',$mov['fk_interno'])
                    ->update(
                        [
                            'interno.transferencia_pendente' => 'S',
                            'interno.unidade_prisional' => $mov['up_anterior'],
                            'interno.pavilhao' => $mov['pv_anterior'],
                            'interno.ala' => $mov['ala_anterior'],
                            'interno.cela' =>$mov['cela_anterior']
                        ]
                    );
                    error_log('interno com mov pendente: ', $mov['fk_interno']);
                }
                if($mov['tipo'] == "transferenciaentrada" || $mov['tipo'] == "entradaunidadeprisional"){
                    break;
                }
            }
        }
    }

    public static function getMovs($idInterno){
        $ultimasMovs = DB::table('movimentacao_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao_interno.fk_interno', $idInterno)
            ->where('movimentacao_interno.status','!=','Cancelada')
            ->orderBy('movimentacao.data','DESC')
            ->get();
        return json_decode($ultimasMovs, true);
    }

    public static function alteracao($movs){
        foreach($movs as $mov){
            if($mov['tipo'] == 'alvara'){
                error_log($mov['fk_interno']." - alvara");
                DB::table('interno')
                    ->where('interno.id', $mov['fk_interno'])
                    ->update(
                        [
                            'interno.status' => 'I',
                            'interno.condicao_interno' => 638
                        ]
                    );
                return;
            } elseif ($mov['tipo'] == 'fuga') {
                error_log($mov['fk_interno']." - fuga");
                DB::table('interno')
                    ->where('interno.id', $mov['fk_interno'])
                    ->update(
                        [
                            'interno.status' => 'I',
                            'interno.condicao_interno' => 631
                        ]
                    );
                return;
            } elseif ($mov['tipo'] == 'extradicao') {
                error_log($mov['fk_interno']." - extradicao");
                DB::table('interno')
                    ->where('interno.id', $mov['fk_interno'])
                    ->update(
                        [
                            'interno.status' => 'I',
                            'interno.condicao_interno' => 650
                        ]
                    );
                return;
            } elseif ($mov['tipo'] == 'obito') {
                error_log($mov['fk_interno']." - obito");
                DB::table('interno')
                    ->where('interno.id', $mov['fk_interno'])
                    ->update(
                        [
                            'interno.status' => 'I',
                            'interno.condicao_interno' => 637
                        ]
                    );
                return;
            } elseif ($mov['tipo'] == 'prisaodomiciliar') {
                if($mov['up_anterior'] == 4369){
                    error_log($mov['fk_interno']." - prisão domiciliar - ".config('app.tz'));
                    DB::table('interno')
                    ->where('interno.id', $mov['fk_interno'])
                    ->update(
                        [
                            'interno.status' => 'A',
                            'interno.condicao_interno' => 635
                        ]
                    );
                } else {
                    error_log($mov['fk_interno']." - prisão domiciliar");
                    DB::table('interno')
                    ->where('interno.id', $mov['fk_interno'])
                    ->update(
                        [
                            'interno.status' => 'I',
                            'interno.condicao_interno' => 635
                        ]
                    );
                }
                return;                
            } elseif ($mov['tipo'] == 'recambiamento') {
                error_log($mov['fk_interno']." - recambiamento");
                DB::table('interno')
                    ->where('interno.id', $mov['fk_interno'])
                    ->update(
                        [
                            'interno.status' => 'I',
                            'interno.condicao_interno' => 636
                        ]
                    );
                return;
            } elseif ($mov['tipo'] == 'saidajudicial') {
                error_log($mov['fk_interno']." - saidajudicial");
                DB::table('interno')
                    ->where('interno.id', $mov['fk_interno'])
                    ->update(
                        [
                            'interno.status' => 'I',
                            'interno.condicao_interno' => 647
                        ]
                    );
                return;
            }
        }
        error_log($mov['fk_interno']." - Ativo");
        DB::table('interno')
            ->where('interno.id', $mov['fk_interno'])
            ->update(
                [
                    'interno.status' => 'A',
                    'interno.condicao_interno' => 644
                ]
            );
    }

    public static function calcularStatusCondicaoInternos()
    {
        $listaInternos = DB::table('interno')->select('id')->get()->pluck('id');
        
        foreach($listaInternos as $internoId){
            // pega as movimentações de um interno
            $movs = DB::table('movimentacao_interno')
            ->leftJoin('movimentacao', 'movimentacao.id', '=', 'movimentacao_interno.fk_movimentacao')
            ->where('movimentacao_interno.fk_interno', $internoId)
            ->where('movimentacao.tipo','!=','mudancacela')
            ->where('movimentacao_interno.status','!=','Cancelada')
            ->orderBy('movimentacao.data','DESC')
            ->orderBy('movimentacao.id','DESC')
            ->get();
            $movs = json_decode($movs, true);
            // deixa as mais recentes de mesmo horário e remove o restante do array
            if($movs != []){
                $horarioMovRecente = $movs[0]['data'];
                $ultimasMovs = [];
                foreach($movs as $mov){
                    if($mov['data'] == $horarioMovRecente){
                        array_push($ultimasMovs, $mov);
                    }
                }
                // caso alguma delas seja de inativação, inativar o interno, caso contrário ativar (status e condição)
                self::alteracao($ultimasMovs);
            }
        }

    }

    //falta testar
    public function preencheAlaMov(){
        // pega as movimentações que tem cela destino e não tem ala destino
        $movs = DB::table('movimentacao')->whereNotNull('cela_destino')->whereNull('ala_destino')->get();
        foreach($movs as $mov){
            // verifica se aquela cela tem uma ala respectiva
            $cela = DB::table('cela')->where('id', $mov->cela_destino)->select('fk_ala')->get();
            $ala = DB::table('ala')->where('id', $cela->fk_ala)->exists();
            if (!$ala) {
                // caso não tenha ala respectiva, verifica se aquele id de cela tem pai na estrutura_fisica
                $ef_ala = DB::table('_mig_estrutura_fisica_csv')->where('ID_ESTRUTURA_FISICA', $mov->cela_destino)->whereNotNull('ID_ESTRUTURA_FISICA_PAI')->first(); 
                if (!is_null($ef_ala)){
                    //caso tenha, cria a ala com as características da estrutura_fisica e vincula à movimentação
                    DB::table('ala')->insert([
                        'id' => $ef_ala->ID_ESTRUTURA_FISICA,
                        'nome' => $ef_ala->NOME,
                        'fk_pavilhao' => $ef_ala->ID_ESTRUTURA_FISICA_PAI,
                        'status' => $ef_ala->STATUS,
                        'created_at' => Tdata::TtimeStampHojeMysql(),
                    ]);
                    $idAla = DB::getPdo()->lastInsertId();
                    DB::table('movimentacao')
                    ->where('id', $mov->id)
                    ->update(
                        [
                            'ala_destino' => $idAla,
                        ]
                    );
                } else {
                    //caso não tenha, cria ala igual à cela e atualiza movimentação
                    DB::table('ala')->insert([
                        'nome' => $mov->nome,
                        'status' => $ef_ala->STATUS,
                        'created_at' => Tdata::TtimeStampHojeMysql(),
                    ]);
                    $idAla = DB::getPdo()->lastInsertId();
                    DB::table('movimentacao')
                    ->where('id', $mov->id)
                    ->update(
                        [
                            'ala_destino' => $idAla,
                        ]
                    );
                }
            }
        }


        // executar o código abaixo adaptado para ala e pavilhão.
        /*
            UPDATE siapenpb.movimentacao m
            JOIN siapenpb.cela c ON c.id = m.cela_destino
            SET m.ala_destino = c.fk_ala
            where m.cela_destino is not null and m.ala_destino is null
        */

        // converter status de Ativo para A e Inativ para I
    }

    public function criaCela(){
        $movs = DB::table('movimentacao')->whereNotNull('cela_destino')->get();
        foreach($movs as $mov){
            // error_log($mov->id);
            $cela = DB::table('cela')->where('id', $mov->cela_destino)->get();
            if(count($cela) == 0){
                $ef_cela = DB::table('_mig_estrutura_fisica_csv')->where('ID_ESTRUTURA_FISICA', $mov->cela_destino)->first(); 
                if (!is_null($ef_cela)){
                    // error_log($mov->id);
                    // DB::table('cela')->insert([
                    //     'id' => $ef_cela->ID_ESTRUTURA_FISICA,
                    //     'nome' => $ef_cela->NOME,
                    //     'fk_ala' => $ef_cela->ID_ESTRUTURA_FISICA_PAI,
                    //     'status' => $ef_cela->STATUS,
                    //     'created_at' => Tdata::TtimeStampHojeMysql(),
                    // ]);
                    error_log('movId-'.$mov->id.' celaId-'.DB::getPdo()->lastInsertId());
                }
            }
        }
    }






    public function temFilho($estrutura){
        return DB::table('_mig_estrutura_fisica_csv')->where('ID_ESTRUTURA_FISICA_PAI', $estrutura->ID_ESTRUTURA_FISICA)->exists();
    }


    public function criarCelasEstruturaFisica(){
        $celas = [];
        $estrutura_fisica = DB::table('_mig_estrutura_fisica_csv')->get();
        foreach($estrutura_fisica as $estrutura){
            if(!self::temFilho($estrutura)){
                array_push($celas, $estrutura);
                error_log("cela adicionada:".$estrutura->ID_ESTRUTURA_FISICA);
            }
        }
        foreach($celas as $cela) {
            error_log("cela criada:".$cela->ID_ESTRUTURA_FISICA);
            
            if(!DB::table('cela')->where('id',$cela->ID_ESTRUTURA_FISICA)->exists()){
                $status = $cela->STATUS == 'Ativo' ? 'A' : 'I';
                DB::table('cela')->insert([
                    'id' => $cela->ID_ESTRUTURA_FISICA,
                    'nome' => $cela->NOME,
                    'fk_ala' => $cela->ID_ESTRUTURA_FISICA_PAI,
                    'capacidade' => $cela->CAPACIDADE,
                    'status' => $status,
                    'created_at' => Tdata::TtimeStampHojeMysql(),
                ]);
            }
        }
    }

    public function criarAlasEstruturaFisica(){
        $celas = DB::table('cela')->whereNotNull('fk_ala')->get();
        foreach($celas as $cela) {
            if(!DB::table('ala')->where('id',$cela->fk_ala)->exists()){
                $ala = DB::table('_mig_estrutura_fisica_csv')->where('ID_ESTRUTURA_FISICA',$cela->fk_ala)->first();
                $status = $ala->STATUS == 'Ativo' ? 'A' : 'I';
                DB::table('ala')->insert([
                    'id' => $ala->ID_ESTRUTURA_FISICA,
                    'nome' => $ala->NOME,
                    'fk_pavilhao' => $ala->ID_ESTRUTURA_FISICA_PAI,
                    'status' => $status,
                    'created_at' => Tdata::TtimeStampHojeMysql(),
                ]);
            }
        }
    }

    public function criarPavihoesEstruturaFisica(){
        $alas = DB::table('ala')->whereNotNull('fk_pavilhao')->get();
        foreach($alas as $ala) {
            if(!DB::table('pavilhao')->where('id',$ala->fk_pavilhao)->exists()){
                $pavilhao = DB::table('_mig_estrutura_fisica_csv')->where('ID_ESTRUTURA_FISICA',$ala->fk_pavilhao)->first();
                $status = $pavilhao->STATUS == 'Ativo' ? 'A' : 'I';
                DB::table('pavilhao')->insert([
                    'id' => $pavilhao->ID_ESTRUTURA_FISICA,
                    'nome' => $pavilhao->NOME,
                    'fk_up' => $pavilhao->ID_ESTRUTURA_ORG,
                    'status' => $status,
                    'created_at' => Tdata::TtimeStampHojeMysql(),
                ]);
            }
        }
    }

    public function criarAlaPvRestantes(){
        $celas = DB::table('cela')->whereNull('fk_ala')->get();
        foreach($celas as $cela) {
            $idUp = DB::table('_mig_estrutura_fisica_csv')->where('ID_ESTRUTURA_FISICA',$cela->id)->first()->ID_ESTRUTURA_ORG;
            DB::table('pavilhao')->insert([
                'nome' => $cela->nome,
                'fk_up' => $idUp,
                'status' => $cela->status,
                'created_at' => Tdata::TtimeStampHojeMysql(),
            ]);
            $idPav = DB::getPdo()->lastInsertId();
            DB::table('ala')->insert([
                'nome' => $cela->nome,
                'fk_pavilhao' => $idPav,
                'status' => $cela->status,
                'created_at' => Tdata::TtimeStampHojeMysql(),
            ]);
            $idAla = DB::getPdo()->lastInsertId();
            DB::table('cela')
            ->where('id',$cela->id)
            ->update(
                [
                    'fk_ala' => $idAla
                ]
            );
        }
    }

    public function criarPavilhoesRestantes(){
        $alas = DB::table('ala')->whereNull('fk_pavilhao')->get();
        foreach($alas as $ala) {
            $idUp = DB::table('_mig_estrutura_fisica_csv')->where('ID_ESTRUTURA_FISICA',$ala->id)->first()->ID_ESTRUTURA_ORG;
            DB::table('pavilhao')->insert([
                'nome' => $ala->nome,
                'fk_up' => $idUp,
                'status' => $ala->status,
                'created_at' => Tdata::TtimeStampHojeMysql(),
            ]);
            $idPav = DB::getPdo()->lastInsertId();
            DB::table('ala')
            ->where('id',$ala->id)
            ->update(
                [
                    'fk_pavilhao' => $idPav
                ]
            );
        }
    }

    public function criarEstruturaFisica(){
        $this->criarCelasEstruturaFisica();
        $this->criarAlasEstruturaFisica();
        $this->criarPavihoesEstruturaFisica();
        $this->criarAlaPvRestantes();
        $this->criarPavilhoesRestantes();
        $this->criaEstruturaUmNivelAcimaDoPV();
    }

    public function recalcularMovs(){
        $movs = DB::table('movimentacao')->whereNotNull('cela_destino')->get();
        foreach($movs as $mov){
            $existeCela = DB::table('cela')->where('id',$mov->cela_destino)->exists();
            if(!$existeCela){
                $existeAla = DB::table('ala')->where('id',$mov->cela_destino)->exists();
                if(!$existeAla){
                    $existePavilhao = DB::table('pavilhao')->where('id',$mov->cela_destino)->exists();
                    if(!$existePavilhao){
                        DB::table('movimentacao_interno')
                        ->where('fk_movimentacao',$mov->id)
                        ->delete();
                        DB::table('movimentacao')
                        ->where('id',$mov->id)
                        ->delete();
                    } else {
                        DB::table('movimentacao')
                        ->where('id',$mov->id)
                        ->update(
                            [
                                'pavilhao_destino' => $mov->cela_destino,
                                'cela_destino' => null
                            ]
                        );
                    }
                } else {
                    DB::table('movimentacao')
                    ->where('id',$mov->id)
                    ->update(
                        [
                            'ala_destino' => $mov->cela_destino,
                            'cela_destino' => null
                        ]
                    );
                }
            }
        }
    }

    public static function presoEmCelaInexistente(){
        $celasInexistentes = [];
        $internos = DB::table('interno')->whereNotNull('cela')->get();
        foreach($internos as $interno){
            if(!DB::table('cela')->where('id',$interno->cela)->exists()){
                array_push($celasInexistentes, $interno->cela);
            }
        }
        $celasInexistentes = array_unique($celasInexistentes);
        dd($celasInexistentes);
    }
    
    public function checarCelasInexistentes(){
        $celasInexistentes = [];
        $movs = DB::table('movimentacao')->whereNotNull('cela_destino')->get();
        foreach($movs as $mov){
            if(!DB::table('cela')->where('id',$mov->cela_destino)->exists()){
                array_push($celasInexistentes, $mov->cela_destino);
            }
        }
        $celasInexistentes = array_unique($celasInexistentes);
        dd($celasInexistentes);
    }

    public function preencherMovs(){
        $movs = DB::table('movimentacao')->whereNotNull('ala_destino')->whereNull('cela_destino')->get();
        foreach($movs as $mov){
            $pavilhao = DB::table('ala')->where('id',$mov->ala_destino)->first()->fk_pavilhao; 
            $unidade = DB::table('pavilhao')->where('id',$pavilhao)->first()->fk_up; 
            DB::table('movimentacao')
            ->where('id',$mov->id)
            ->update(
                [
                    'unidade_destino' => $unidade,
                    'pavilhao_destino' => $pavilhao
                ]
            );
        }
        $movs = DB::table('movimentacao')->whereNotNull('cela_destino')->get();
        foreach($movs as $mov){
            $ala = DB::table('cela')->where('id',$mov->cela_destino)->first()->fk_ala; 
            $pavilhao = DB::table('ala')->where('id',$ala)->first()->fk_pavilhao; 
            $unidade = DB::table('pavilhao')->where('id',$pavilhao)->first()->fk_up; 
            DB::table('movimentacao')
            ->where('id',$mov->id)
            ->update(
                [
                    'unidade_destino' => $unidade,
                    'pavilhao_destino' => $pavilhao,
                    'ala_destino' => $ala
                ]
            );
        }
    }

    public function preencherMovsInts(){
        $movs = DB::table('movimentacao_interno')->whereNotNull('cela_anterior')->get();
        foreach($movs as $mov){
            if(DB::table('cela')->where('id',$mov->cela_anterior)->exists()){
                $ala = DB::table('cela')->where('id',$mov->cela_anterior)->first()->fk_ala; 
                $pavilhao = DB::table('ala')->where('id',$ala)->first()->fk_pavilhao; 
                $unidade = DB::table('pavilhao')->where('id',$pavilhao)->first()->fk_up; 
                DB::table('movimentacao_interno')
                ->where('id',$mov->id)
                ->update(
                    [
                        'up_anterior' => $unidade,
                        'pv_anterior' => $pavilhao,
                        'ala_anterior' => $ala
                    ]
                );
            }
            else{
                DB::table('movimentacao_interno')->where('id',$mov->id)->delete();
            }
        }
    }

    public function checaExistenciaCelas(){
        $movs = DB::table('movimentacao_interno')->whereNotNull('cela_anterior')->get();
        $celasInexistentes = [];
        foreach($movs as $mov){
            if(!DB::table('cela')->where('id',$mov->cela_anterior)->exists()){
                error_log($mov->cela_anterior);
                array_push($celasInexistentes, $mov->cela_anterior);
            }
        }
        $celasInexistentes = array_unique($celasInexistentes);
        dd($celasInexistentes);
    }

    public static function defineLotacaoInternos(){
        $internos = DB::table('interno')->get();
        foreach($internos as $interno){
            $ultimaLotacao = DB::table('movimentacao')
                ->join('movimentacao_interno','movimentacao_interno.fk_movimentacao','=','movimentacao.id')
                ->whereNotNull('movimentacao.ala_destino')
                ->where('movimentacao_interno.fk_interno',$interno->id)
                ->where('movimentacao.unidade_destino',$interno->unidade_prisional)
                ->where('movimentacao.tipo','mudancacela')
                ->where('movimentacao_interno.status','Finalizada')
                ->orderBy('movimentacao.data','DESC')
                ->first();
            if(is_null($ultimaLotacao)){
                DB::table('interno')
                ->where('id',$interno->id)
                ->update(
                    [
                        'pavilhao' => null,
                        'ala' => null,
                        'cela' => null
                    ]
                );
            } else {
                DB::table('interno')
                ->where('id',$interno->id)
                ->update(
                    [
                        'pavilhao' => $ultimaLotacao->pavilhao_destino,
                        'ala' => $ultimaLotacao->ala_destino,
                        'cela' => $ultimaLotacao->cela_destino
                    ]
                );
            }
        }
    }

    public function apagarMovsCanceladas(){
        $movimentacao_interno = DB::table('movimentacao_interno')->where('movimentacao_interno.status','Cancelada')->get();
        foreach($movimentacao_interno as $mi){
            DB::table('movimentacao')->where('id',$mi->fk_movimentacao)->delete();
        }
        DB::table('movimentacao_interno')->where('movimentacao_interno.status','Cancelada')->delete();
    }

    public static function moverFotos(){
        $fotos = DB::table('foto')->where('fk_tabela','interno')->get();
        foreach($fotos as $foto){
            if (!file_exists(storage_path("app/arquivos/internos/fotos/" . $foto->fk_id . "/" . $foto->nome_arquivo))) {
                if(file_exists(storage_path("app/arquivos/internos/fotos2/" . $foto->nome_arquivo))){
                    if(!File::isDirectory('arquivos/internos/fotos/'.$foto->fk_id)){
                        File::makeDirectory('arquivos/internos/fotos/'.$foto->fk_id, 0775, true, true);
                    }
                    Storage::move('arquivos/internos/fotos2/'.$foto->nome_arquivo, 'arquivos/internos/fotos/'.$foto->fk_id.'/'.$foto->nome_arquivo);   
                }
            }
        }
    }

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

    public static function migracaoFotoInterno()
    {
        $fotos = DB::table('foto')->where('fk_tabela','interno')->get();
        
        foreach(json_decode($fotos, true) as $foto){
            if(!File::isDirectory(storage_path('app/arquivos/internos/fotos/'.$foto['fk_id']))){
                
                File::makeDirectory(storage_path('app/arquivos/internos/fotos/'.$foto['fk_id'], 0777, true, true));
                
                //dd(!File::isDirectory('arquivos/internos/fotos/'.$foto['fk_id']));
            }
            if(File::isFile(storage_path('app/arquivos/internos/fotos/extracao_fotos_cust_AC/'.$foto['nome_arquivo']))){
                
                $move = Storage::move('arquivos/internos/fotos/extracao_fotos_cust_AC/'.$foto['nome_arquivo'], 'arquivos/internos/fotos/'.$foto['fk_id'].'/'.$foto['nome_arquivo']);
            }
        }
        
         dd("FIM- migracaoFotoInterno");
    }

    public static function migracaoArquivoVis()
    {
        
        $fotos = DB::table('documento')->where('fk_tabela','visita')->get();
        foreach(json_decode($fotos, true) as $foto){
            if(!File::isDirectory(storage_path('app/arquivos/visitas/documentos/'.$foto['fk_id']))){
                File::makeDirectory(storage_path('app/arquivos/visitas/documentos/'.$foto['fk_id'], 0777, true, true));
            }
            $move = Storage::move('arquivos/visitas/documentos/extracao_anexos_vis_BA/'.$foto['nome_arquivo'], 'arquivos/visita/documentos/'.$foto['fk_id'].'/'.$foto['nome_arquivo']);
        }
        
        // dd("FIM - migracaoArquivoVis()");

    }

    public static function migracaoFotos()
    {
        self::migracaoFotoAdv();
        self::migracaoFotoVisita();
        self::migracaoFotoInterno();
        dd("fotos de interno, visitas e advogados migradas");
    }

    public function mainSaulo(){
        
        // atualizar status de movimentações pendentes
        //self::calcularMovsPendentes();
        //error_log('checkpoint F');

        //dd("fim");
    }
    
    public static function migrarTabelaInterno() {
        
        $dados = DB::table('_mig_internos_dados_pessoais')->get();
        
        foreach($dados as $dado)
        {
            
            DB::table('interno')->insert([
               
                'id' => $dado->ID_CUSTODIADO,
                'nome' => $dado->NOME_SOCIAL,
                'nome_social' => $dado->NOME_SOCIAL,
                'mae' => $dado->NOME_MAE,
                'pai' => $dado->NOME_PAI,
                'unidade_prisional' => null,
                'pavilhao' => null,
                'ala' => null,
                'cela' => null,
                'sexo' => $dado->SEXO,
                'cpf' => null,
                'rg_numero' => null,
                'rg_orgaoexpedidor' => null,
                'rg_data' => null,
                'estado_civil' => $dado->ESTADO_CIVIL,
                'altura' => null,
                'peso' => null,
                'profissao' => $dado->PROFISSAO,
                'etnia' => $dado->RACA_COR,
                'cor_olhos' => null,
                'cor_cabelos' => null,
                'data_nascimento' => Tdata::TdataMysql($dado->DT_NASCIMENTO),
                'escolaridade' => $dado->ESCOLARIDADE,
                'sentenciado' => null,
                'regime' => $dado->REGIME,
                'condicao_interno' => null,
                'medida_seguranca' => null,
                'transferencia_pendente' => null,
                'saidasexternas' => null,
                'faccao' => null,
                'faccao_posto' => null,
                'faccao_padrinho' => null,
                'faccao_quebrada' => null,
                'lideranca' => null,
                'naturalidade_pais' => $dado->PAIS_NASCIMENTO,
                'naturalidade_estado' => $dado->UF_NASCIMENTO,
                'naturalidade_cidade' => $dado->MUNICIPIO_NASCIMENTO,
                'opcao_sexual' => strtolower($dado->ORIENTACAO_SEXUAL),
                'periculosidade' => $dado->PERICULOSIDADE,
                'documento_entrada_numero' => null,
                'religiao' => $dado->RELIGIAO,
                'status' => null,
                'observacao' => null,
                'created_at' => Tdata::TtimeStampHojeMysql(),
                'updated_at' => null,
                
            ]);
            
        }
        
        dd("FIM - migrarTabelaInterno");
        
    }

    public static function temMovimentacaoEmNaoUnidade(){
        $resulta = [];
        $resultb = [];
        $movimentacoes = DB::table('_mig_movimentacao_csv')->get();
        foreach($movimentacoes as $m){
            if(!is_null($m->ID_ESTRUTURA_ORG_DESTINO)){
                if(!DB::table('unidade')->where('id',$m->ID_ESTRUTURA_ORG_DESTINO)->exists()){
                    array_push($resulta, $m);
                }
            }
            if(!is_null($m->ID_ESTRUTURA_ORG_VINCULO)){
                if(!DB::table('unidade')->where('id',$m->ID_ESTRUTURA_ORG_VINCULO)->exists()){
                    array_push($resultb, $m);
                }
            }
        }
        if($resulta != [] || $resultb != [])
            dd("temMovimentacaoEmNaoUnidade ",$resulta, $resultb);
    }

    public static function temMovimentacaoEmNaoCela(){
        $resulta = [];
        $resultb = [];
        $movimentacoes = DB::table('_mig_movimentacao_interna_csv')->get();
        foreach($movimentacoes as $mi){
            if(!is_null($mi->ID_ESTRUTURA_FISICA_DEST)){
                if(!DB::table('cela')->where('id',$mi->ID_ESTRUTURA_FISICA_DEST)->exists()){
                    array_push($resulta, $mi);
                }
            }
            if(!is_null($mi->ID_ESTRUTURA_FISICA_VINC)){
                if(!DB::table('cela')->where('id',$mi->ID_ESTRUTURA_FISICA_VINC)->exists()){
                    array_push($resultb, $mi);
                }
            }
        }
        if($resulta != [] || $resultb != [])
            dd("temMovimentacaoEmNaoCela ",$resulta, $resultb);
    }

    public static function apagarMovimentacoesSemReferencia(){
       $movimentacoes = DB::table('_mig_movimentacao_interna_csv')->get();
       foreach($movimentacoes as $mi){
           if(!is_null($mi->ID_ESTRUTURA_FISICA_DEST)){
               if(!DB::table('cela')->where('id',$mi->ID_ESTRUTURA_FISICA_DEST)->exists()){
                   DB::table('_mig_movimentacao_interna_csv')->where('ID_MOVIMENTACAO_INTERNA',$mi->ID_MOVIMENTACAO_INTERNA)->update([
                       'ID_ESTRUTURA_FISICA_DEST' => null
                   ]);
               }
           }
           if(!is_null($mi->ID_ESTRUTURA_FISICA_VINC)){
               if(!DB::table('cela')->where('id',$mi->ID_ESTRUTURA_FISICA_VINC)->exists()){
                   DB::table('_mig_movimentacao_interna_csv')->where('ID_MOVIMENTACAO_INTERNA',$mi->ID_MOVIMENTACAO_INTERNA)->update([
                       'ID_ESTRUTURA_FISICA_VINC' => null
                   ]);
               }
           }
           if(is_null($mi->ID_ESTRUTURA_FISICA_DEST) && is_null($mi->ID_ESTRUTURA_FISICA_VINC)){
               DB::table('_mig_movimentacao_interna_csv')->where('ID_MOVIMENTACAO_INTERNA', $mi->ID_MOVIMENTACAO_INTERNA)->delete();
           }
       }

        $movimentacoes = DB::table('_mig_movimentacao_csv')->get();
        foreach($movimentacoes as $m){
            if(!is_null($m->ID_ESTRUTURA_ORG_DESTINO)){
                if(!DB::table('unidade')->where('id',$m->ID_ESTRUTURA_ORG_DESTINO)->exists()){
                    DB::table('_mig_movimentacao_csv')->where('ID_MOVIMENTACAO',$m->ID_MOVIMENTACAO)->update([
                        'ID_ESTRUTURA_ORG_DESTINO' => null
                    ]);
                }
            }
            if(!is_null($m->ID_ESTRUTURA_ORG_VINCULO)){
                if(!DB::table('unidade')->where('id',$m->ID_ESTRUTURA_ORG_VINCULO)->exists()){
                    DB::table('_mig_movimentacao_csv')->where('ID_MOVIMENTACAO',$m->ID_MOVIMENTACAO)->update([
                        'ID_ESTRUTURA_ORG_VINCULO' => null
                    ]);
                }
            }
            if(is_null($m->ID_ESTRUTURA_ORG_DESTINO) && is_null($m->ID_ESTRUTURA_ORG_VINCULO)){
                DB::table('_mig_movimentacao_csv')->where('ID_MOVIMENTACAO', $m->ID_MOVIMENTACAO)->delete();
            }
        }
    }

    public static function migrarMovimentacoes(){
        $migMovs = DB::table('_mig_movimentacao_csv')->get();
        foreach($migMovs as $migMov){
            if(DB::table('_mig_interno_movimentacao_cancelada_csv')->where('ID_MOVIMENTACAO', $migMov->ID_MOVIMENTACAO)->exists()){
                continue;
            }

            $tipo = "";
            $unidade_destino = null;
            $pavilhao_destino = null;
            $ala_destino = null;
            $cela_destino = null;
            $unidade_origem = null;
            $pavilhao_origem = null;
            $ala_origem = null;
            $cela_origem = null;

            if($migMov->IN_MOTIVO == "Remoção para Outro Estado"){
                $tipo = "recambiamento";
            } elseif($migMov->IN_MOTIVO == "Óbito"){
                $tipo = "obito";
                DB::table('obito')->insert([
                    'fk_movimentacao' => $migMov->ID_MOVIMENTACAO,
                    'interno' => $migMov->ID_CUSTODIADO,
                    'data_obito' => $migMov->TE_MOVIMENTACAO,
                    'obito_sispern' => 'S',
                    'created_at' => $migMov->TE_OPERACAO_MOVI
                ]);
            } elseif($migMov->IN_MOTIVO == "Recaptura"){
                $tipo = "entradaunidadeprisional";
            } elseif($migMov->IN_MOTIVO == "Abandono"){
                $tipo = "fuga";
            } elseif($migMov->IN_MOTIVO == "Transferência"){
                $tipo = "transferencia";
                if($migMov->TP_MOVIMENTACAO == "Entrada"){
                    $tipo = "transferenciaentrada";
                }
            } elseif($migMov->IN_MOTIVO == "Prisão"){
                $tipo = "entradaunidadeprisional";
            } elseif($migMov->IN_MOTIVO == "Fuga"){
                $tipo = "fuga";
            } elseif($migMov->IN_MOTIVO == "Soltura"){
                $tipo = "alvara";
            } elseif($migMov->IN_MOTIVO == "Saída Temporária"){
                $tipo = "ocorrencia";
            } elseif($migMov->IN_MOTIVO == "Saída para Prisão Domiciliar"){
                $tipo = "prisaodomiciliar";
            } elseif($migMov->IN_MOTIVO == "Retorno"){
                $tipo = "ocorrencia";
            } elseif($migMov->IN_MOTIVO == "Audiência"){
                $tipo = "ocorrencia";
            } elseif($migMov->IN_MOTIVO == "Retorno de Audiência"){
                $tipo = "ocorrencia";
            } elseif($migMov->IN_MOTIVO == "Desinternação"){
                $tipo = "alvara";
            } elseif($migMov->IN_MOTIVO == "Internação"){
                $tipo = "entradaunidadeprisional";
            } elseif($migMov->IN_MOTIVO == "Transferência Compulsória"){
                $tipo = "transferencia";
            } elseif($migMov->IN_MOTIVO == "Retorno de Saída Temporária"){
                $tipo = "ocorrencia";
            } elseif($migMov->IN_MOTIVO == "Permissão de Saída"){
                $tipo = "ocorrencia";
            } elseif($migMov->IN_MOTIVO == "Trabalho Externo"){
                $tipo = "ocorrencia";
            } elseif($migMov->IN_MOTIVO == "Internação para Tratamento Médico"){
                $tipo = "ocorrencia";
            } elseif($migMov->IN_MOTIVO == "Apresentação Espontânea"){
                $tipo = "entradaunidadeprisional";
            } elseif($migMov->IN_MOTIVO == "Saída para Atendimento Externo"){
                $tipo = "ocorrencia";
            } elseif($migMov->IN_MOTIVO == "Retorno por Remoção"){
                $tipo = "entradaunidadeprisional";
            } elseif($migMov->IN_MOTIVO == "Retorno da pernoite"){
                $tipo = "ocorrencia";
            } elseif($migMov->IN_MOTIVO == "Saída para pernoite"){
                $tipo = "ocorrencia";
            } elseif($migMov->IN_MOTIVO == "Remoção"){
                $tipo = "saidajudicial";
            } elseif($migMov->IN_MOTIVO == "Não Apresentação no Horário"){
                $tipo = "fuga";
            } elseif($migMov->IN_MOTIVO == "Retorno de Permissão de Saída"){
                $tipo = "ocorrencia";
            } elseif($migMov->IN_MOTIVO == "Estudo Externo"){
                $tipo = "ocorrencia";
            } elseif($migMov->IN_MOTIVO == "Extradição"){
                $tipo = "extradicao";
            } elseif($migMov->IN_MOTIVO == "Atendimento SAMU"){
                $tipo = "ocorrencia";
            }

            if(!is_null($migMov->ID_MOVIMENTACAO_INTERNA)){
                $mi = DB::table('_mig_movimentacao_interna_csv')->where('ID_MOVIMENTACAO_INTERNA',$migMov->ID_MOVIMENTACAO_INTERNA)->first();
                if(DB::table('_mig_interno_movimentacao_interna_cancelada_csv')->where('ID_MOVIMENTACAO_INTERNA', $migMov->ID_MOVIMENTACAO_INTERNA)->exists()){
                    continue;
                }

                if(DB::table('cela')->where('id',$mi->ID_ESTRUTURA_FISICA_VINC)->exists()){
                    $cela_origem = $mi->ID_ESTRUTURA_FISICA_VINC;
                    $ala_origem = (DB::table('cela')->where('id',$mi->ID_ESTRUTURA_FISICA_VINC)->first())->fk_ala;
                    $pavilhao_origem = (DB::table('ala')->where('id',$ala_origem)->first())->fk_pavilhao;
                    $unidade_origem = (DB::table('pavilhao')->where('id',$pavilhao_origem)->first())->fk_up;
                }
                if(DB::table('cela')->where('id',$mi->ID_ESTRUTURA_FISICA_DEST)->exists()){
                    $cela_destino = $mi->ID_ESTRUTURA_FISICA_DEST;
                    $ala_destino = (DB::table('cela')->where('id',$mi->ID_ESTRUTURA_FISICA_DEST)->first())->fk_ala;
                    $pavilhao_destino = (DB::table('ala')->where('id',$ala_destino)->first())->fk_pavilhao;
                    $unidade_destino = (DB::table('pavilhao')->where('id',$pavilhao_destino)->first())->fk_up;
                }

                if(is_null($cela_origem)){
                    $unidade_origem = !is_null($migMov->ID_ESTRUTURA_ORG_VINCULO)?$migMov->ID_ESTRUTURA_ORG_VINCULO:null;
                }
                if(is_null($cela_destino)){
                    $unidade_destino = !is_null($migMov->ID_ESTRUTURA_ORG_DESTINO)?$migMov->ID_ESTRUTURA_ORG_DESTINO:null;
                    if(is_null($cela_destino)){
                        $trocarOrigemPorDestino = [
                            'Apresentação Espontânea',
                            'Internação',
                            'Prisão',
                            'Recaptura',
                            'Retorno',
                            'Retorno por Remoção',
                            'Transferência Compulsória'
                        ];
                        if(in_array($migMov->IN_MOTIVO, $trocarOrigemPorDestino)){
                            $temp = "";
                            $temp = $unidade_destino;
                            $unidade_destino = $unidade_origem;
                            $unidade_origem = $temp;
                            
                            $temp = "";
                            $temp = $pavilhao_destino;
                            $pavilhao_destino = $pavilhao_origem;
                            $pavilhao_origem = $temp;
    
                            $temp = "";
                            $temp = $ala_destino;
                            $ala_destino = $ala_origem;
                            $ala_origem = $temp;
    
                            $temp = "";
                            $temp = $cela_destino;
                            $cela_destino = $cela_origem;
                            $cela_origem = $temp;
                        }
                        if($tipo == "transferenciaentrada"){
                            $unidade_destino = $unidade_origem;
                        }
                    }
                }
            } else {
                $unidade_origem = $migMov->ID_ESTRUTURA_ORG_VINCULO;
                $unidade_destino = $migMov->ID_ESTRUTURA_ORG_DESTINO;
                if(is_null($unidade_destino)){
                    $trocarOrigemPorDestino = [
                        'Apresentação Espontânea',
                        'Internação',
                        'Prisão',
                        'Recaptura',
                        'Retorno',
                        'Retorno por Remoção',
                        'Transferência Compulsória'
                    ];
                    if(in_array($migMov->IN_MOTIVO, $trocarOrigemPorDestino)){
                        $unidade_destino = $unidade_origem;
                        $unidade_origem = null;
                    }
                    if($tipo == "transferenciaentrada"){
                        $unidade_destino = $unidade_origem;
                    }
                }
            }

            if($tipo != "ocorrencia"){
                DB::table('movimentacao')->insert([
                    'id' => $migMov->ID_MOVIMENTACAO,
                    'tipo' => $tipo,
                    'unidade_destino' => $unidade_destino,
                    'pavilhao_destino' => $pavilhao_destino,
                    'ala_destino' => $ala_destino,
                    'cela_destino' => $cela_destino,
                    'motivo' => 'Motivo: '.$migMov->IN_MOTIVO.' --- Situação: '.$migMov->IN_SITUACAO.' --- Tipo de Movimentação: '.$migMov->TP_MOVIMENTACAO.' --- Justificativa: '.$migMov->TX_JUSTIFICATIVA,
                    'data' => Tdata::TtimestampMysql($migMov->TE_MOVIMENTACAO.':00'),
                    'created_at' => Tdata::TtimestampMysql($migMov->TE_OPERACAO_MOVI.':00')
                ]);

                DB::table('movimentacao_interno')->insert([
                    'status' => 'Finalizada',
                    'fk_interno' => $migMov->ID_CUSTODIADO,
                    'fk_movimentacao' => $migMov->ID_MOVIMENTACAO,
                    'up_anterior' => $unidade_origem,
                    'pv_anterior' => $pavilhao_origem,
                    'ala_anterior' => $ala_origem,
                    'cela_anterior' => $cela_origem,
                    'obs' => ''
                ]);
            } else {
                $unidade = !is_null($unidade_destino)?$unidade_destino:$unidade_origem;
                DB::table('ocorrencia')->insert([
                    'unidade' => $unidade,
                    'natureza' => 21,
                    'data' => Tdata::TdataMysql(substr($migMov->TE_MOVIMENTACAO,0,10)),
                    'horario' => substr($migMov->TE_MOVIMENTACAO,11,16),
                    'descricao' => 'Motivo: '.$migMov->IN_MOTIVO.' --- Situação: '.$migMov->IN_SITUACAO.' --- Tipo de Movimentação: '.$migMov->TP_MOVIMENTACAO.' --- Justificativa: '.$migMov->TX_JUSTIFICATIVA,
                    'status' => 'A',
                    'created_at' => Tdata::TtimeStampHojeMysql(),
                    'update_at' => null
                ]);
                
                $idOcorrencia = DB::getPdo()->lastInsertId();

                DB::table('ocorrencia_envolvidos')->insert([
                    'fk_ocorrencia' => $idOcorrencia,
                    'fk_tabela' => 'interno',
                    'fk_id_envolvido' => $migMov->ID_CUSTODIADO
                ]);
            }
        }
    }

    public static function migrarMudancasCela(){
        $migMovs = DB::table('_mig_movimentacao_interna_csv')->whereNull('IN_MOTIVO_SAIDA')->get();
        foreach($migMovs as $migMov){
            if(DB::table('_mig_interno_movimentacao_interna_cancelada_csv')->where('ID_MOVIMENTACAO_INTERNA', $migMov->ID_MOVIMENTACAO_INTERNA)->exists()){
                continue;
            }

            $unidade_destino = null;
            $pavilhao_destino = null;
            $ala_destino = null;
            $cela_destino = null;
            $unidade_origem = null;
            $pavilhao_origem = null;
            $ala_origem = null;
            $cela_origem = null;

            if(!is_null($migMov->ID_ESTRUTURA_FISICA_VINC)){
                if(DB::table('cela')->where('id',$migMov->ID_ESTRUTURA_FISICA_VINC)->exists()){
                    $cela_origem = $migMov->ID_ESTRUTURA_FISICA_VINC;
                    $ala_origem = (DB::table('cela')->where('id',$migMov->ID_ESTRUTURA_FISICA_VINC)->first())->fk_ala;
                    $pavilhao_origem = (DB::table('ala')->where('id',$ala_origem)->first())->fk_pavilhao;
                    $unidade_origem = (DB::table('pavilhao')->where('id',$pavilhao_origem)->first())->fk_up;
                }
            }

            if(!is_null($migMov->ID_ESTRUTURA_FISICA_DEST)){
                if(DB::table('cela')->where('id',$migMov->ID_ESTRUTURA_FISICA_DEST)->exists()){
                    $cela_destino = $migMov->ID_ESTRUTURA_FISICA_DEST;
                    $ala_destino = (DB::table('cela')->where('id',$migMov->ID_ESTRUTURA_FISICA_DEST)->first())->fk_ala;
                    $pavilhao_destino = (DB::table('ala')->where('id',$ala_destino)->first())->fk_pavilhao;
                    $unidade_destino = (DB::table('pavilhao')->where('id',$pavilhao_destino)->first())->fk_up;
                }
            }


            if(is_null($migMov->ID_ESTRUTURA_FISICA_VINC) && is_null($migMov->ID_ESTRUTURA_FISICA_DEST)){
                continue;
            }
            
            DB::table('movimentacao')->insert([
                'tipo' => "mudancacela",
                'unidade_destino' => $unidade_destino,
                'pavilhao_destino' => $pavilhao_destino,
                'ala_destino' => $ala_destino,
                'cela_destino' => $cela_destino,
                'motivo' => $migMov->IN_MOTIVO_SAIDA,
                'data' => Tdata::TtimestampMysql($migMov->TE_MOVIMENTACAO_INTERNA.':00'),
                'created_at' => Tdata::TtimestampMysql($migMov->TE_OPERACAO_MOIN.':00')
            ]);

            $idMov = DB::getPdo()->lastInsertId();

            DB::table('movimentacao_interno')->insert([
                'status' => 'Finalizada',
                'fk_interno' => $migMov->ID_CUSTODIADO,
                'fk_movimentacao' => $idMov,
                'up_anterior' => $unidade_origem,
                'pv_anterior' => $pavilhao_origem,
                'ala_anterior' => $ala_origem,
                'cela_anterior' => $cela_origem,
                'obs' => ''
            ]);
        }
    }

    public static function soma1segTransferenciaEntrada() {
        
        $dados = DB::table('movimentacao')->where('tipo', 'transferencia')->get();
        
        foreach($dados as $dado)
        {
            $transfEntrada = DB::table('movimentacao')->where('tipo', 'transferenciaentrada')->where('data', $dado->data)->get();
            
            if(count($transfEntrada) > 0)
            {
                
                foreach($transfEntrada as $entrada)
                {
                    
//                    $data = Tdata::getDataTimestamp($entrada->data);
//                    $hora = Tdata::getTempoTimestamp($entrada->data);
                    
                    $novaData = date('Y-m-d H:i:s',strtotime('+1 second', strtotime($entrada->data)));
                                        
                   DB::table("movimentacao")->where('id', $entrada->id)->update(['data' => $novaData]);
                           
                }
                
            }
        }
        
    }
    
    public static function preencheUpPvAlaCelAnteriorMovimentacaoInterno()
    {
        $saidas = array(
            'alvara',
            'extincaopunibilidade',
            'extradicao',
            'fuga',
            'livramentocondicional',
            'obito',
            'prisaodomiciliar',
            'saidajudicial',
            'recambiamento'
        );
        
        
        $dados = DB::table('movimentacao_interno')
                ->leftJoin("movimentacao","movimentacao.id","=","movimentacao_interno.fk_movimentacao")
                ->select("movimentacao.*", "movimentacao_interno.*", "movimentacao.id as idMov", "movimentacao_interno.id as idMovInterno")
                ->orderBy("movimentacao.data", "DESC")
                ->get();

        foreach($dados as $dado)
        {
           $movAnterior = self::getTipoMovimentacaoAnterior($dado->idMov, $dado->fk_interno);
           error_log('A: '.$dado->fk_interno);
           //corrigir up,pv,ala,cela de origem
           if(!is_null($movAnterior) && // se só tiver uma unidade, não preenche
                !(in_array($movAnterior->tipo, $saidas) && !in_array($dado->tipo, $saidas)) && // // se for uma entrada após uma saída, não preenche
                !(in_array($movAnterior->tipo, $saidas) && in_array($dado->tipo, $saidas)) && // se for entrada após entrada, não preenche
                !(!in_array($movAnterior->tipo, $saidas) && !in_array($dado->tipo, $saidas)) // se for saída após saída, não preenche
            )
           {
               if(self::getPrimeiraMovInterno($dado->fk_interno) != $dado->idMov)
               {
                   DB::table("movimentacao_interno")->where('id', $dado->idMovInterno)->update([
                       'up_anterior' => $movAnterior->unidade_destino,
                       'pv_anterior' => $movAnterior->pavilhao_destino,
                       'ala_anterior' => $movAnterior->ala_destino,
                       'cela_anterior' => $movAnterior->cela_destino
                   ]);
                   error_log('B: '.$dado->idMovInterno);
               }
           }
        }
        
    }
    
    public static function getPrimeiraMovInterno($idInterno)
    {
            $dados = DB::table('movimentacao_interno')
                ->leftJoin("movimentacao","movimentacao.id","=","movimentacao_interno.fk_movimentacao")
                ->where('movimentacao_interno.fk_interno', $idInterno)
                ->orderBy('movimentacao.id', 'ASC')
                ->select("movimentacao.*", "movimentacao_interno.*", "movimentacao.id as idMov", "movimentacao_interno.id as idMovInterno")

                ->get();
            
            if(count($dados) > 0)
            {
                return $dados[0]->idMov;
            }

    }
    public static function getTipoMovimentacaoAnterior($idMovimentacao, $fkInterno) {
        
            $dados = DB::table('movimentacao_interno')
                ->leftJoin("movimentacao","movimentacao.id","=","movimentacao_interno.fk_movimentacao")
                ->where('movimentacao_interno.fk_interno', $fkInterno)
                ->where('movimentacao.id', '<', $idMovimentacao)
                ->orderBy('movimentacao.id', 'DESC')
                ->get();
            
            if(count($dados) > 1)
            {
                return ($dados[0]);
                
            }else{
                
                return null;
            
                
            }
        
    }

    public static function calcularLocalizacoesInternos(){
        $internos = DB::table('interno')->get();
        $movs = DB::table('movimentacao_interno')
            ->join('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')
            ->select('movimentacao.id as movId', 'movimentacao_interno.id as movIntId', 'movimentacao.*', 'movimentacao_interno.*')
            ->orderBy('movimentacao.data', 'DESC')
            ->get();
        $movs = json_decode($movs, true);
        
        $saidas = array(
            'alvara',
            'extincaopunibilidade',
            'extradicao',
            'fuga',
            'livramentocondicional',
            'obito',
            'prisaodomiciliar',
            'saidajudicial',
            'recambiamento'
        );

        foreach($internos as $interno){
            $unidade = $pavilhao = $ala = $cela = null;
            // pega a última mov do interno, se for saída pega a origem, caso não seja, pega o destino
            $movInterno = array_filter(
                $movs, 
                function ($item) use ($interno) {
                    return $item['fk_interno'] == $interno->id;
                }
            );
            $movInterno = array_reverse($movInterno);
            $movInterno = array_pop($movInterno);
            if(in_array($movInterno['tipo'], $saidas)){
                $unidade = $movInterno['up_anterior'];
                $pavilhao = $movInterno['pv_anterior'];
                $ala = $movInterno['ala_anterior'];
                $cela = $movInterno['cela_anterior'];
            } else {
                $unidade = $movInterno['unidade_destino'];
                $pavilhao = $movInterno['pavilhao_destino'];
                $ala = $movInterno['ala_destino'];
                $cela = $movInterno['cela_destino'];
            }
            DB::table('interno')->where('id',$interno->id)->update([
                'unidade_prisional' => $unidade,
                'pavilhao' => $pavilhao,
                'ala' => $ala,
                'cela' => $cela
            ]);
        }
    }
    
    public static function removerDuplicidadeEstruturaFisica(){
        $estruturas_fisicas = DB::table('_mig_estrutura_fisica_csv')->get();
        foreach($estruturas_fisicas as $item) {
            $query = DB::table('_mig_estrutura_fisica_csv')->where('ID_ESTRUTURA_FISICA', $item->ID_ESTRUTURA_FISICA);
            if($query->count() >= 2){
                $primeira = $query->first()->id;
                DB::table('_mig_estrutura_fisica_csv')->where('ID_ESTRUTURA_FISICA',$item->ID_ESTRUTURA_FISICA)->where('id','!=',$primeira)->delete();
            }
        }
    }

    public function mainCassemiro(){
        // apagar duplicidades da estrutura física, manter primeiro resultado
        // self::removerDuplicidadeEstruturaFisica();

        // migrar estrura fisica
        // $this->criarEstruturaFisica();
        
        // migrar fotos
        // self::migracaoFotos();
        
        
        // verifica se tem movimentação em algo que não seja cela
            self::apagarMovimentacoesSemReferencia();

            // self::temMovimentacaoEmNaoCela();
            // self::temMovimentacaoEmNaoUnidade();

        // migrar movimentações
            self::migrarMovimentacoes();
            // dd("deu certo");
        // quando tiver transferencia e transferenciaentrada no mesmo horário, somar 1s no transferenciaentrada
            self::soma1segTransferenciaEntrada();
            // dd("deu certo");
        
        // migrar mudanças de celas
            self::migrarMudancasCela();
            // error_log('checkpoint C');
            
        // preencher up_anterior, pv_anterior, ala_anterior, cela_anterior de entradas que não tiverem
            self::preencheUpPvAlaCelAnteriorMovimentacaoInterno();

        // // apaga movimentação sem origem e destino
        // $results = DB::table('movimentacao_interno')->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')->select('movimentacao.*','movimentacao.id as idMov', 'movimentacao_interno.*')->get();
        // foreach($results as $result){
        //     if(is_null($result->up_anterior) && is_null($result->unidade_destino)){
        //         DB::table('movimentacao')->where('id', $result->idMov)->delete();
        //     }
        // }

        // // colocar destino igual a origem na transferenciaentrada e entradaunidadeprisional que o destino esteja nulo
        $results = DB::table('movimentacao_interno')->leftJoin('movimentacao','movimentacao.id','=','movimentacao_interno.fk_movimentacao')->select('movimentacao.*','movimentacao.id as idMov', 'movimentacao_interno.*')->get();
        foreach($results as $result){
            if(($result->tipo == "transferenciaentrada" || $result->tipo == "entradaunidadeprisional") && !is_null($result->up_anterior) && is_null($result->unidade_destino)){
                DB::table('movimentacao')->where('id', $result->idMov)->update([
                    'unidade_destino' => $result->up_anterior
                ]);
            }
        }

        // calcular movimentações pendentes
            self::calcularMovsPendentes();

        // calcular localização de internos
            self::calcularLocalizacoesInternos();
            // error_log('checkpoint D');
            
        // calcular status e condição de internos
            self::calcularStatusCondicaoInternos();
            // error_log('checkpoint E');

        dd("fim.");
        
    }

    public static function getEntradaSaidaDuplicadaInterno() {
        
        $dados = DB::table('movimentacao_interno')
            ->leftJoin("movimentacao","movimentacao.id","=","movimentacao_interno.fk_movimentacao")
            ->select("movimentacao.*", "movimentacao_interno.*", "movimentacao.id as idMov", "movimentacao_interno.id as idMovInterno")
            ->get();
            
            $duplicidade = array();
            
            foreach($dados as $dado)
            {
                $movAnterior = self::getTipoMovimentacaoAnterior($dado->idMov, $dado->fk_interno);
                if(!is_null($movAnterior)){
                    if($dado->tipo == $movAnterior->tipo)
                    {
                        array_push($duplicidade, array(
                            'tipo' => $dado->tipo,
                            'idMov' => $dado->idMov,
                            'idMovInterno' => $dado->idMovInterno
                        ));
                    }
                }

            }
            
            
            dd($duplicidade);
        
    }
    
    public static function defineGrauParentescoPaiMae()
    {
        $dados = DB::table("visita_interno")->where('grau_parentesco', 'Pai/Mãe')->get();
        
        foreach($dados as $dado)
        {
            
            $sexoVisitante = DB::table('visita')->where("id", $dado->fk_visita)->get()[0]->sexo;
            
            if($sexoVisitante == "F")
            {
                DB::table('visita_interno')->where('id', $dado->id)->update([
                    'grau_parentesco' => 'Mãe'
                ]);
            }
            
            if($sexoVisitante == "M")
            {
                DB::table('visita_interno')->where('id', $dado->id)->update([
                    'grau_parentesco' => 'Pai'
                ]);
            }
            
            else
            {
                DB::table('visita_interno')->where('id', $dado->id)->update([
                    'grau_parentesco' => 'Mãe'
                ]);
            }
            
        }
        
        dd("END");

    }
    
    public function upperGrauParentesco() {
        
        $dados = DB::table("visita_interno")->get();
        
        foreach($dados as $dado)
        {
            DB::table('visita_interno')->where('id', $dado->id)->update(['grau_parentesco'=> mb_strtoupper($dado->grau_parentesco)]);
        }
        
        dd("FIM");
    }
    
    public function mainArtigo(){
        
        // Migrar Interno
        //self::migrarTabelaInterno();

        // atualizar documentos
        //self::updateDocumentosInterno();
        
        /*
         * para migrar os processos e artigos, executar um por vez
         */
        
        // 1º - atualizar dados
        //self::updateNatureza();
        
        // 2º pegar referencias
        //self::getIdNatureza();
        
        // 3º ver se algum tipo penal ficou de fora
        //self::getDistinctTipoPenal();
        
        // 4º - migrar dados
        //self::migProcesso();
        
        // 5º migrar dispositivos legais
        //self::migDispositivoLegal();
        
        # FIM MIGRACAO PROCESSOS E ARTIGOS
        
        // migrar endereco
        //self::migrarEnderecoInterno();
        
        // migrar filhos do interno
        //self::migrarFilhosInterno();
        
        // migrar contato interno
        //self::migrarContatoInterno();
        
        // inserir usuarios
        //self::migracaoTabelaUser();
        
        /*
         * Inserir o primeiro fechamento do livro de ocorrencias
         * >> ATENÇÃO << - alterar a data que esta no campo data para a data do start do sistema
         */
        //self::insertDataLivro();
        
        
        /**
         * inserir registros de visitantes no controle de acesso
         */
        //self::insertRegistroVisitaControleAcesso();
        
        /**
         * inserir registros de advogados no controle de acesso
         */
        //self::insertRegistroAdvogadoControleAcesso();
        
        /**
         * inserir registros de atendimento de advogados
         */
        //self::insertRegistroAdvogadoAtendimento();
        
        //self::updateZeroEsquerdaCPF();
        
        //self::consertaNomeServidors();
        
        //self::consertaNomeServidors();
        
        //self::migControleAcessoVisitaInterno();
        
        //self::ajusteDataNascimento();

        //self::updateNomeInterno();
        
        /**
         * Migracao de fotos do interno
         */
        //self::migracaoFotoInterno();
        
        //self::migrarDeficiencia();
        
        //self::migrarIdGenero();
        
    }

    public static function migrarIdGenero() {
        
        $dados = DB::table("_mig_internos_dados_pessoais")->whereNotNull('IDENTIDADE_GENERO')->where('IDENTIDADE_GENERO', "!=", "")->get();
        
        foreach($dados as $dado)
        {
            $idInterno = $dado->ID_CUSTODIADO;
            
            // verifica se houve unificacao
            $verificaUnificacao = DB::table("unificacao")->where("fk_A", $dado->ID_CUSTODIADO)->orWhere("fk_B", $dado->ID_CUSTODIADO)->get();
            
            if(count($verificaUnificacao) > 0){
                $idInterno = $verificaUnificacao[0]->fk_novo;
            }
            
            DB::table("interno")->where('id', $idInterno)->update([
                'identidade_genero' => $dado->IDENTIDADE_GENERO
            ]);

        }
        
        dd("FIM - migrarIdGenero()");
        
    }
    
    public static function migrarDeficiencia() {
        
        $dados = DB::table("_mig_internos_dados_pessoais")->where('DEFICIENCIA', "!=", "")->get();
        
        foreach($dados as $dado)
        {
            $tipoDeficiencia = null;
            $idInterno = $dado->ID_CUSTODIADO;
            
            switch ($dado->DEFICIENCIA){
                case "Auditiva":
                    $tipoDeficiencia = "Deficiência Auditiva";
                break;
            
                case "Física":
                    $tipoDeficiencia = "Deficiência Física";
                break;
            
                case "Intelectual":
                    $tipoDeficiencia = "Deficiência Intelectual";
                break;
            
                case "Visual":
                    $tipoDeficiencia = "Deficiência Visual";
                break;
            
                case "Física, Visual":
                    $tipoDeficiencia = "Deficiências Múltiplas";
                break;
            
            }
            
            // verifica se houve unificacao
            $verificaUnificacao = DB::table("unificacao")->where("fk_A", $dado->ID_CUSTODIADO)->orWhere("fk_B", $dado->ID_CUSTODIADO)->get();
            
            if(count($verificaUnificacao) > 0){
                $idInterno = $verificaUnificacao[0]->fk_novo;
            }
            
            if(DB::table('interno_deficiencia')->where('fk_interno', $idInterno)->count() == 0){
                DB::table('interno_deficiencia')->insert([
                    'fk_interno' => $idInterno,
                    'deficiente' => 'S',
                    'tipo_deficiencia' => $tipoDeficiencia,
                    'created_at' => Tdata::TtimeStampHojeMysql(),
                ]);
            }
            
        }
        
        dd("FIM - migrarDeficiencia()");
                
    }

    public static function updateNomeInterno()
    {
        $dados = DB::table("_mig_internos_dados_pessoais")->get();

        foreach($dados as $dado)
        {
            DB::table("interno")->where("id", $dado->ID_CUSTODIADO)->update([
                'nome' => $dado->NOME_CIVIL,
            ]);
        }

        dd("FIM - updateNomeInterno");
    }
    
    public function migracaoTabelaUser() {
        
        $dados = DB::table("_mig_users_dados_csv")->get();
        
        foreach($dados as $dado)
        {
         
            $retirar = array(".","-","(",")");

            $verifica = DB::table("users")->where("cpf",  str_replace($retirar, "", $dado->cpf))->count();
            
            if($verifica == 0){
                
            $senha = str_random(8);
            $senhaDB = Hash::make($senha);

                DB::table("users")->insert([
                    'name' => $dado->nome,
                    'cpf' => str_replace($retirar, "", $dado->cpf),
                    'email' => $dado->mail,
                    'password' => $senhaDB,
                    'tipo' => 16,
                    'cargo' => $dado->cargo,
                    'orgao' => "IAPEN-AC",
                    'status' => "A",
                    'created_at' => Tdata::TtimeStampHojeMysql(),
                     'ultima_alteracao_senha' => Tdata::TtimeStampHojeMysql()
                ]);
            }
        }
        
        dd("FIM - migracaoTabelaUser");
        
    }
    
    public static function migrarContatoInterno() {
        
        $dados = DB::table("_mig_internos_dados_pessoais")->where("TELEFONE", "!=", "")->whereNotNull("TELEFONE")->get();
        
        foreach($dados as $dado)
        {
            DB::table("contato")->insert([
                'fk_id' => $dado->ID_CUSTODIADO,
                'fk_tabela' => "interno",
                'numero' => str_replace("-", "", $dado->TELEFONE),
                'created_at' => Tdata::TtimeStampHojeMysql(),
            ]);
        }
        
        dd("FIM - migrarContatoInterno");
        
    }
    
    public static function migrarFilhosInterno() {
        
        $dados = DB::table("_mig_internos_filhos_csv")->get();
        
        foreach($dados as $dado)
        {
            DB::table("filhos")->insert([
                'fk_interno' => $dado->ID_CUSTODIADO,
                'nome' => null,
                'data_nascimento' => Tdata::TdataMysql($dado->DT_NASC_CUSTODIADO_FILHO),
                'numero_cartao_sus' => null,
                'na_unidade_prisional' => $dado->FILHO_NO_ESTABELECIMENTO,
                'incapaz' => $dado->INCAPAZ,
            ]);
        }
        
    }

    public static function migrarEnderecoInterno() {
        
        $dados = DB::table("_mig_internos_endereco_csv")->get();
        
        foreach($dados as $dado)
        {
            DB::table('endereco')->insert([
                'fk_id' => $dado->ID_CUSTODIADO,
                'fk_tabela' => "interno",
                'pais' => "Brasil",
                'estado' => $dado->UF,
                'cidade' => $dado->M_NO_MUNICIPIO,
                'bairro' => $dado->CUSTED_ED_BAIRRO_CUEN,
                'cep' => $dado->CUSTED_ED_CEP_CUEN,
                'logradouro' => $dado->CUSTED_ED_LOGRADOURO_CUEN,
                'numero' => $dado->CUSTED_ED_NUMERO_CUEN,
                'complemento' => $dado->CUSTED_ED_COMPLEMENTO_CUEN,
                'created_at' => Tdata::TtimeStampHojeMysql(),
            ]);
        }
        
        dd("FIM - migrarEnderecoInterno");
        
    }
    public static function ajusteDataNascimento() {
        
        $dados = DB::table('_mig_internos_data_nascimento_csv')->get();
        
        foreach($dados as $dado)
        {
            if($dado->DT_NASCIMENTO != "" && $dado->DT_NASCIMENTO != null){
                $dataNascimento = DB::table("interno")->where('id', $dado->ID_CUSTODIADO)->update([
                    'data_nascimento' => Tdata::TdataMysql($dado->DT_NASCIMENTO)
                ]);
            }
        }
        
        dd("FIM - ajusteDataNascimento()");
    }
    
    public static function migControleAcessoVisitaInterno()
    {
        $dados = DB::table('_mig_visitas_interno_registros_csv')
                ->get();
        
        foreach($dados as $dado)
        {
            
            DB::table('controle_acesso_visita_interno')->insert([
                'unidade' => $dado->ID_ESTRUTURA_ORG,
                'fk_visita' => $dado->ID_VISITANTE,
                'fk_interno' => $dado->ID_CUSTODIADO,
                'horario' => Tdata::getTempoTimestamp(Tdata::TtimestampMysql($dado->DATA_HORA_ENTRADA.":00")),
                'data' => Tdata::getDataTimestamp(Tdata::TtimestampMysql($dado->DATA_HORA_ENTRADA.":00")),
            ]);
        }
        
        dd("FIM - migControleAcessoVisitaInterno()");
        
    }
    public static function consertaNomeCharset()
    {
        $dados = DB::table('users')
                ->where('name', 'LIKE', '%?%')
                ->get();
        
        foreach($dados as $dado)
        {
            $nomeServidor = DB::connection('mysql2')->table('servidors')
                    ->where('cpf', $dado->cpf)
                    ->get();
            
            if(count($nomeServidor) > 0){
            
                DB::table("users")->where('cpf', $dado->cpf)->update([
                    'name' => $nomeServidor[0]->nome,
                ]);
                
            }
            
        }
        
        dd("FIM - consertaNomeCharset()");
    }


    public static function consertaNomeServidors()
    {
        
        $dados = DB::table('users')
                    ->where('name', 'LIKE', '%Masculino%')
                    ->get();
        
         $retirar = array("Masculino", "MASCULINO");
            foreach($dados as $dado)
            {

                DB::table('users')
                        ->where("id", $dado->id)
                        ->update([
                            'name' => strtoupper(str_replace($retirar, "M", $dado->name))
                        ]);


            }
                    
//        $colunas = DB::connection('mysql2')->getSchemaBuilder()->getColumnListing('servidors');
//        
//        foreach($colunas as $coluna){
//        
//            $dados = DB::connection('mysql2')->table('servidors')
//                    ->where($coluna, 'LIKE', '%Masculino%')
//                    ->get();
//
//            //dd($dados);
//
//            foreach($dados as $dado)
//            {
//
//                DB::connection('mysql2')->table('servidors')
//                        ->where("id", $dado->id)
//                        ->update([
//                            $coluna => str_replace("Masculino", "M", $dado->$coluna)
//                        ]);
//
//
//            }
//            
//        }
        
        dd("FIM");

    }

        public function updateZeroEsquerdaCPF() {
        
        //visita
        $dados = DB::table('interno')->get();
        
        foreach($dados as $dado)
        {
            if(!is_null($dado->cpf) &&  $dado->cpf != "")
            {
                $qtdCaracteres = strlen($dado->cpf);
                
                if($qtdCaracteres < 11)
                {
                    $novoCpf = "";
                    $diferenca = 11 - $qtdCaracteres;
                    $zero = 0;
                    for($i = 0; $i < $diferenca; $i++)
                    {
                        $novoCpf = $zero.$novoCpf;
                    }
                    
                    $novoCpf = $novoCpf.$dado->cpf;
                    
                    DB::table('interno')->where("id", $dado->id)->update([
                        'cpf' => $novoCpf,
                    ]);
                }
            }
        }
        
        dd("FIM - updateZeroEsquerdaCPF()");
        
    }
    public static function insertUsersSiapen() {
        
        $dados = DB::connection('mysql2')->table('servidors')->get();
        
            foreach($dados as $dado)
            {
                
                if(!is_null($dado->cpf) && !is_null($dado->email)){
                    
                    $verificaCadastro = DB::table("users")->where('cpf', $dado->cpf)->get();
                    
                        if(count($verificaCadastro) == 0){
                            
                        $senha = str_random(8);

                        $senhaDB = Hash::make($senha);

                        $insertUsuario = DB::table('users')->insert([
                           'name' => $dado->nome,
                           'cpf' => $dado->cpf,
                           'email' => $dado->email,
                           'password' => $senhaDB,
                           'tipo' => 16,
                           'cargo' => $dado->cargo_texto,
                           'orgao' => "SEAP-BA",
                           'status' => "A",
                           'created_at' => Tdata::TtimeStampHojeMysql(),
                            'ultima_alteracao_senha' => Tdata::TtimeStampHojeMysql()

                        ]);
                        
                    }
                    
                }

            }
            
            dd("FIM - insertUsersSiapen()");

        
    }

    public static function insertContatoRH() {
        
        $dados = DB::connection('mysql2')->table('servidors')->get();
        foreach($dados as $dado)
        {
            $retirar = array(".","-","(",")");
            
            if(!is_null($dado->telefone_texto)){
                DB::connection('mysql2')->table('contatos')->insert([
                    'id_servidor' => $dado->id,
                    'numero' => str_replace($retirar, "", $dado->telefone_texto),
                ]);
            }

        }
        
    }
    public static function updateDatasRH() {
        
        $dados = DB::connection('mysql2')->table('servidors')->get();
        
        foreach($dados as $dado)
        {
            $retirar = array(".","-");
            
            DB::connection('mysql2')->table('servidors')->where('id', $dado->id)->update([
                'cpf' => str_replace($retirar, "", $dado->cpf),
            ]);
            
//            DB::connection('mysql2')->table('servidors')->where('id', $dado->id)->update([
//                'data_nascimento' => str_replace("-", "/", $dado->data_nascimento),
//                'rg_data_expedicao' => str_replace("-", "/", $dado->rg_data_expedicao),
//                'data_admissao_estado' => str_replace("-", "/", $dado->data_admissao_estado),
//            ]);
            
//            DB::connection('mysql2')->table('servidors')->where('id', $dado->id)->update([
//                'data_nascimento' => Tdata::TdataMysql($dado->data_nascimento),
//                'rg_data_expedicao' => Tdata::TdataMysql($dado->rg_data_expedicao),
//                'data_admissao_estado' => Tdata::TdataMysql($dado->data_admissao_estado),
//            ]);
        }
        
        dd("FIM - updateDatasRH()");
        
    }
    
    public static function updateFuncaoFuncaoRH() {
        
        $dados = DB::connection('mysql2')->table('servidors')->get();
        
        foreach($dados as $dado)
        {
            $idCargo = DB::connection('mysql2')->table('funcao')->where('nome', 'LIKE', '%' . trim($dado->funcao_texto) . '%')->get()[0]->id;
            
            DB::connection('mysql2')->table('servidors')->where('id', $dado->id)->update([
                'funcao' => $idCargo,
            ]);
        }
        
        dd("FIM - updateFuncaoFuncaoRH()");
        
    }
    
    public static function updateCargoFuncaoRH() {
        
        $dados = DB::connection('mysql2')->table('servidors')->get();
        
        foreach($dados as $dado)
        {
            $idCargo = DB::connection('mysql2')->table('cargos')->where('nome', 'LIKE', '%' . trim($dado->cargo_texto) . '%')->get()[0]->id;
            
            DB::connection('mysql2')->table('servidors')->where('id', $dado->id)->update([
                'cargo' => $idCargo,
            ]);
        }
        
        dd("FIM");
        
    }
    
    public function insertRegistroAdvogadoAtendimento() {
        
        $dados = DB::table('_mig_advogado_atendimento_csv')->where('CANCELADO', 'Não')->get();
        
        foreach($dados as $dado)
        {
            
            $data = Tdata::getDataTimestamp(Tdata::TtimestampMysql($dado->DATA_HORA_ENTRADA));
            $horario = Tdata::getTempoTimestamp(Tdata::TtimestampMysql($dado->DATA_HORA_ENTRADA));
            $horarioSaida = Tdata::getTempoTimestamp(Tdata::TtimestampMysql($dado->DATA_HORA_SAIDA));
            $dataSaida = Tdata::getDataTimestamp(Tdata::TtimestampMysql($dado->DATA_HORA_SAIDA));
            
            DB::table('advogado_atendimento')->insert([
                'unidade' => $dado->ID_ESTRUTURA_ORG,
                'fk_advogado' => $dado->ID_DEFENSOR,
                'fk_interno' => $dado->ID_CUSTODIADO,
                'data_atendimento' => $data,
                'horaInicio' => $horario,
                'horaTermino' => $horarioSaida,
                'created_at' => Tdata::TtimeStampHojeMysql(),
            ]);
        }
        
        dd("FIM - insertRegistroAdvogadoAtendimento");
        
    }
    
    public function insertRegistroAdvogadoControleAcesso() {
        
        $dados = DB::table('_mig_advogado_atendimento_csv')->where('CANCELADO', 'Não')->get();
        
        foreach($dados as $dado)
        {
            
            $data = Tdata::getDataTimestamp(Tdata::TtimestampMysql($dado->DATA_HORA_ENTRADA));
            $horario = Tdata::getTempoTimestamp(Tdata::TtimestampMysql($dado->DATA_HORA_ENTRADA));
            $horarioSaida = Tdata::getTempoTimestamp(Tdata::TtimestampMysql($dado->DATA_HORA_SAIDA));
            $dataSaida = Tdata::getDataTimestamp(Tdata::TtimestampMysql($dado->DATA_HORA_SAIDA));
            
            DB::table('controle_acesso')->insert([
                'unidade' => $dado->ID_ESTRUTURA_ORG,
                'tabela' => 'advogado',
                'cpf' => Advogado\AdvogadoController::getDadosAdv($dado->ID_DEFENSOR)[0]->cpf,
                'motivo' => "ATENDIMENTO DE ADVOGADO(A)/DEFENSOR(A) AO INTERNO",
                'data' => $data,
                'data_saida' => $dataSaida,
                'horario' => $horario,
                'horario_saida' => $horarioSaida,
                'created_at' => Tdata::TtimeStampHojeMysql(),
            ]);
        }
        
        dd("FIM - insertRegistroAdvogadoControleAcesso");
        
    }
    
    public function insertRegistroVisitaControleAcesso() {
        
        $dados = DB::table('_mig_visitantes_visita_csv')->get();
        
        foreach($dados as $dado)
        {
            
            $data = Tdata::getDataTimestamp(Tdata::TtimestampMysql($dado->DATA_HORA_ENTRADA));
            $horario = Tdata::getTempoTimestamp(Tdata::TtimestampMysql($dado->DATA_HORA_ENTRADA));
            $horarioSaida = Tdata::getTempoTimestamp(Tdata::TtimestampMysql($dado->DATA_HORA_SAIDA));
            $dataSaida = Tdata::getDataTimestamp(Tdata::TtimestampMysql($dado->DATA_HORA_SAIDA));
            
            DB::table('controle_acesso')->insert([
                'unidade' => $dado->ID_ESTRUTURA_ORG,
                'tabela' => 'visita',
                'cpf' => Visita\VisitaController::getCpfVisita($dado->ID_VISITANTE),
                'motivo' => "VISITA FAMILIAR",
                'data' => $data,
                'data_saida' => $dataSaida,
                'horario' => $horario,
                'horario_saida' => $horarioSaida,
                'created_at' => Tdata::TtimeStampHojeMysql(),
            ]);
        }
        
        dd("FIM - insertRegistroVisitaControleAcesso");
        
    }
    public static function insertDataLivro() {
        
        $dados = DB::table("unidade")->whereNotNull('qtdHorasPlantao')->get();
        
        foreach($dados as $dado)
        {
            DB::table('ocorrencia_fechamento_livro')->insert([
                'unidade' => $dado->id,
                'data' => '2020-11-30',
                'horarioReferencia' => $dado->horaInicioPlantao,
                'created_at' => Tdata::TtimeStampHojeMysql(),
            ]);
            
        }
        
        dd("FIM - insertDataLivro");
        
    }

    public static function getDistinctTipoPenal() {
        
        
        $dados = DB::table('_mig_fase_tipo_penal_csv')->whereNull('fk_legislacao')->select('TIPO_PENAL')->orderBy('TIPO_PENAL', 'ASC')->get();
        $dados =        collect($dados);
        $dados = $dados->unique('TIPO_PENAL');
                
        $array = array_column(json_decode($dados, true), 'TIPO_PENAL');
        
        dd($array);
        
    }
    
    public static function updateNatureza() {
        
        $natureza = array(
          'Art. 125 da LEI 6.815/1980 Inc. XI da LEI 6.815/1980 (Revogada pela Lei nº 13.445, de 2017) ' => 'Artigo 125 Art. 125 da LEI 6.815/1980,Inciso XII Inc. XI da LEI 6.815/1980 (Revogada pela Lei nº 13.445, de 2017)',
            'Art. 13 Parágrafo único do DECRETO 22.626/1933 ' => 'Artigo 13 Art. 13,Parágrafo único Parágrafo único do DECRETO 22.626/1933',
            'Art. 133 § 2° do CPB ' => 'Artigo 133 Art. 133 do CPB',
            'Art. 133 § 3° Inc. I  do CPB ' => 'Artigo 133 Art. 133 do CPB',
            'Art. 149 § 1° In c. I do CPB ' => 'Artigo 149 Art. 149,Parágrafo 1° § 1°,Inciso I In c. I do CPB',
            'Art. 155 § 4º-A do CPB (Transformado em crime hediondo pela Lei 13.964/2019) ' => 'Artigo  Art. 155, § 4°-A',
            //'Art. 157, § 2º A Inc. II do CPB § 2º A Inc. II do CPB ', 'Art. 157, § 2º A Inc. II do CPB',
            'Art. 16 Paragrafo único da Lei 13.869/2019 ' => 'Artigo 16 Art. 16,Parágrafo único Paragrafo único da Lei 13.869/2019',
            'Art. 16 § 1° Inc. I da Lei 10.826/2003 (Redação dada pela Lei nº 13.964, de 2019) ' => 'Artigo 16 Art. 16,Parágrafo 1° § 1°,Inciso I Inc. I da Lei 10.826/2003 (Redação dada pela Lei nº 13.964, de 2019)',
            'Art. 16 § 1° Inc. II da Lei 10.826/2003 (Redação dada pela Lei nº 13.964, de 2019) ' => 'Artigo 16 Art. 16,Parágrafo 1° § 1°,Inciso II Inc. II da Lei 10.826/2003 (Redação dada pela Lei nº 13.964, de 2019)',
            'Art. 16 § 1° Inc. III da Lei 10.826/2003 (Redação dada pela Lei nº 13.964, de 2019) ' => 'Artigo 16 Art. 16,Parágrafo 1° § 1°,Inciso III Inc. III da Lei 10.826/2003 (Redação dada pela Lei nº 13.964, de 2019)',
            'Art. 16 § 1° Inc. IV da Lei 10.826/2003(Redação dada pela Lei nº ' => 'Artigo 16 Art. 16,Parágrafo 1° § 1°,Inciso IV Inc. IV da Lei 10.826/2003(Redação dada pela Lei nº',
            'Art. 16 § 1° Inc. V da Lei 10.826/2003 (Redação dada pela Lei nº 13.964, de 2019) ' => 'Artigo 16 Art. 16,Parágrafo 1° § 1°,Inciso V Inc. V da Lei 10.826/2003 (Redação dada pela Lei nº 13.964, de 2019)',
            'Art. 16 § 1° VI da Lei 10.826/2003 (Redação dada pela Lei nº 13.964, de 2019) ' => 'Artigo 16 Art. 16,Parágrafo 1° § 1°,Inciso VI VI da Lei 10.826/2003 (Redação dada pela Lei nº 13.964, de 2019)',
            'Art. 16 § 2° da Lei 10.826/2003 (Inclusão e Redação dada pela Lei nº 13.964, de 2019) ' => 'Artigo 16 Art. 16,Parágrafo 2° § 2° da Lei 10.826/2003 (Inclusão e Redação dada pela Lei nº 13.964, de 2019)',
            'Art. 17 § 1° da Lei 10.826/2003 (Redação dada pela Lei nº 13.964, de 2019) ' => 'Artigo 17 Art. 17,Parágrafo 1° § 1° da Lei 10.826/2003 (Redação dada pela Lei nº 13.964, de 2019)',
            'Art. 18 Parágrafo único da Lei 10.826/2003 (Redação dada pela Lei nº 13.964, de 2019) ' => 'Artigo 18 Art. 18,Parágrafo único Parágrafo único da Lei 10.826/2003 (Redação dada pela Lei nº 13.964, de 2019)',
            'Art. 1° § 1° Inc. II da Lei 9.613/1998 ' => 'Artigo 1° Art. 1°,Parágrafo 1° § 1°,Inciso I Inc. I da Lei 9.613/1998',
            'Art. 1° § 2° Inc. II da Lei 9613/1998 ' => 'Artigo 1° Art. 1°,Parágrafo 2° § 2°,Inciso II Inc. II da Lei 9613/1998',
            'Art. 1° § 4° da Lei 9613/1998 '=> 'Artigo 1° Art. 1°,Parágrafo 4° § 4° da Lei 9613/1998',
            'Art. 20 da Lei 7.716/1989 ' => 'Artigo  Art. 20 Caput da Lei 7.716/1989',
            'Art. 20 Inc. II da Leei 10826/2003  (Redação dada pela Lei nº 13.964, de 2019) ' => 'Artigo 20 Art. 20,Inciso II Inc. II da Leei 10826/2003  (Redação dada pela Lei nº 13.964, de 2019)',
            'Art. 218 B § 2º Inc. I do CPB ' => 'Artigo 218 B Art. 218 B,Parágrafo 2° § 2º,Inciso I Inc. I do CPB',
            'Art. 22 Parágrafo único da Lei 5.478/1968 ' => 'Artigo 22 Art. 22,Parágrafo único Parágrafo único da Lei 5.478/1968',
            'Art. 244 A § 1° da LEI 8.069/1990 - ECA ' => 'Artigo 244 A Art. 244 A,Parágrafo 1° § 1° da LEI 8.069/1990 - ECA',
            'Art. 288 Parágrafo único do CPB Parágrafo único do CPB ' => 'Artigo 288 Art. 288 Caput do CPB',
            'Art. 297 § 3° Inc. I do CPB ' => 'Artigo 297 Art. 297,Parágrafo 3° § 3°,Inciso I Inc. I do CPB',
            'Art. 302 § 1° Inc. I da Lei 9.503/1997 - CTB ' => 'Artigo 302 Art. 302,Parágrafo 1° § 1°,Inciso I Inc. I da Lei 9.503/1997 - CTB',
            'Art. 302 § 1° Inc. II da Lei 9.503/1997 - CTB ' => 'Artigo 302 Art. 302,Parágrafo 1° § 1°,Inciso II Inc. II da Lei 9.503/1997 - CTB',
            'Art. 302 § 1° Inc. III da Lei 9.503/1997 - CTB ' => 'Artigo 302 Art. 302,Parágrafo 1° § 1°,Inciso III Inc. III da Lei 9.503/1997 - CTB',
            'Art. 302 § 1° Inc. IV da Lei 9503/1997 - CTB ' => 'Artigo 302 Art. 302,Parágrafo 1° § 1°,Inciso IV Inc. IV da Lei 9503/1997 - CTB',
            'Art. 302 § 3º da LEI 9.503/1997-CTB ' => 'Artigo 302 Art. 302,Parágrafo 3° § 3º da LEI 9.503/1997-CTB',
            'Art. 303 § 1° da Lei 9.503/1997 - CTB ' => 'Artigo 303 Art. 303 da Lei 9.503/1997 - CTB',
            'Art. 303 § 2º da Lei 9.503/1997 - CTB ' => 'Artigo 303 Art. 303,Parágrafo 2° § 2º da Lei 9.503/1997 - CTB',
            'Art. 304 Parágrafo único da Lei 9.503/1997 - CTB ' => 'Artigo 304 Art. 304 da Lei 9.503/1997 - CTB',
            'Art. 306 da Lei 9.503/1997 - CTB ' => 'Artigo 306 Art. 306 da Lei 9.503/1977 - CTB',
            'Art. 306 § 1° Inc. I da Lei 9.503/1997 - CTB ' => 'Artigo 306 Art. 306,Parágrafo 1° § 1°,Item I Inc. I da Lei 9.503/1997 - CTB',
            'Art. 306 § 1° Inc. II da Lei 9.503/1997 -CTB ' => 'Artigo 306 Art. 306,Parágrafo 1 § 1°,Inciso II Inc. II da Lei 9.503/1997 -CTB',
            'Art. 307 Parágrafo único da Lei 9.503/1997 - CTB ' => 'Artigo 307 Art. 307 da Lei 9.503/1997 - CTB',
            'Art. 308 da Lei dos Crimes de Trânsito - CTB ' => 'Artigo 308 Art. 308 da Lei 9.503/1997 - CTB',
            'Art. 33 § 1° Inc. IV da Lei 11.343/2006 (Incluído pela Lei nº 13.964, de 2019) ' => 'Artigo 33 Art. 33,Parágrafo 1° § 1°,Inciso IV Inc. IV da Lei 11.343/2006 (Incluído pela Lei nº 13.964, de 2019)',
            'Art. 4° Alínea "a" da Lei 4.898/1965 ' => 'Artigo 4° Art. 4°,Alínea a Alínea "a" da Lei 4.898/1965',
            'Art. 52 Paragrafo único do DECRETO 99.684/1990 ' => 'Artigo 52 Art. 52,Parágrafo Único Paragrafo único do DECRETO 99.684/1990',
            //'Art. 157, § 2º A Inc. II do CPB § 2º A Inc. II do CPB ' => 'Art. 157, § 2º A Inc. II do CPB'
        );
        
        foreach($natureza as $key => $nat)
        {
            $verifica =  DB::table('_mig_fase_tipo_penal_csv')->where("TIPO_PENAL", $key)->get();
            
            if(count($verifica) > 0){
                
                DB::table('_mig_fase_tipo_penal_csv')->where("TIPO_PENAL", $key)->update([
                    'TIPO_PENAL' => $nat
                ]);
                
            }
        }
        
    }


    public static function getIdNatureza()
    {
        ini_set('max_execution_time', 900000);

        //dd("ok");

        $dados = DB::table('_mig_fase_tipo_penal_csv')->whereNull('fk_legislacao')->get();

        $retirar = array(',',' ');

        foreach($dados as $dado)
        {

            
            $natureza = DB::table('lei_natureza')                
                ->where(DB::raw("REPLACE(REPLACE(artigo, ',', ''), ' ','')"), 'LIKE', '%' . trim(str_replace($retirar, "", $dado->TIPO_PENAL)) . '%')
                ->get();

            if(count($natureza) > 0){

               
               $update = DB::table('_mig_fase_tipo_penal_csv')->where('TIPO_PENAL', $dado->TIPO_PENAL)
                   ->update([
                       'fk_legislacao' => $natureza[0]->fk_legislacao,
                       'fk_titulo' => $natureza[0]->fk_titulo,
                       'fk_capitulo' => $natureza[0]->fk_capitulo,
                       'fk_grupo' => $natureza[0]->fk_grupo,
                       'fk_artigo' => $natureza[0]->id,
                   ]);

           }

        }

        dd("END - getIdNatureza()");

    }
    public static function migProcesso()
    {
        

        
        //dd("ok");
        
        $dados = DB::table("_mig_fase_processo_csv")->get();

        foreach($dados as $dado)
        {
            DB::table('interno_processos')->insert([
               'id' => $dado->ID_CUSTODIADO_FASE,
               'fk_interno' => $dado->ID_CUSTODIADO,
                'tipo' => $dado->FASE,
                'data_prisao' => Tdata::TdataMysql($dado->DT_PRISAO),
                'status' => "A",
                'created_at' => Tdata::TtimeStampHojeMysql(),
            ]);

            //$idprocesso = DB::getPdo()->lastInsertId();

            if($dado->FASE == 'condenacao'){
                DB::table('interno_processos_condenacao')->insert([
                   'fk_processo' => $dado->ID_CUSTODIADO_FASE,
                    'tipo' => $dado->FASE,
                    'numero' => $dado->NUMERO,
                    'quantidade_anos' => $dado->ANO_PENA,
                    'quantidade_meses' => $dado->MES_PENA,
                    'quantidade_dias' => $dado->DIA_PENA,
                ]);
            }

            if($dado->FASE == 'inqueritoPolicial'){
                DB::table('interno_processos_inquerito')->insert([
                   'fk_processo' => $dado->ID_CUSTODIADO_FASE,
                    'tipo' => $dado->FASE,
                    'numero' => $dado->NUMERO,
                    'data_prisao' => Tdata::TdataMysql($dado->DT_PRISAO),
                ]);
            }

            if($dado->FASE == 'processo'){
                DB::table('interno_processos_processo')->insert([
                   'fk_processo' => $dado->ID_CUSTODIADO_FASE,
                    'tipo' => $dado->FASE,
                    'numero' => $dado->NUMERO,
                ]);
            }

        }

        dd("FIM");
    }
    
    public static function migDispositivoLegal() {
        
        
            $dadosArtigos = DB::table("_mig_fase_tipo_penal_csv")->get();

            foreach($dadosArtigos as $dadosArtigo)
            {

                DB::table('interno_processos_dispositivo_legal')
                    ->insert([
                        'id_processo' => $dadosArtigo->ID_CUSTODIADO_FASE,
                        'dispositivo_legal' => $dadosArtigo->fk_legislacao,
                        'artigo' => $dadosArtigo->fk_artigo,
                        'fk_grupo' => $dadosArtigo->fk_grupo,
                    ]);

            }

            
            dd("FIM - migDispositivoLegal");
        
    }

}
