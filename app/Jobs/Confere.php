<?php

namespace app\Jobs;

use App\Http\Controllers\Interno\InternoController;
use App\Classes\Tdata;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Exception;

class Confere implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $tries = 1;
    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        DB::table('confere_dados')->truncate(); // limpa a tabela temporária de cálculo do confere
    
        $dados = DB::table('interno')->where('status', 'A')->get();
    
        foreach ($dados as $dado)
        {
            $outrosNomes = DB::table('outrosnomes')->where('pk_tabela', 'interno')->where('pk_id', $dado->id)->select('nome')->get();
            $outrosNomes = json_decode($outrosNomes,true);
            if(!is_null($outrosNomes))
            {
            
                $outrosNomes = implode(',',array_column($outrosNomes, 'nome'));

            }
            $faccaoInterno = InternoController::getFaccaoInterno($dado->id);
            $sansaoTriagem = InternoController::getSancaoTriagem($dado->id);
            $sansao_triagem_data = null;
            $sansao_triagem_status = null;
            if($sansaoTriagem != null || $sansaoTriagem != [])
            {
            
                $sansao_triagem_status = "S";
                $sansao_triagem_data = $sansaoTriagem->data_fim;
            }
            
            DB::table('confere_dados')->insert([
                'fk_interno' => $dado->id,
                'interno_nome' => $dado->nome,
                'interno_nome_social' => $dado->nome_social,
                'interno_outros_nomes' => $outrosNomes,
                'interno_mae' => $dado->mae,
                'interno_unidade' => $dado->unidade_prisional,
                'interno_pavilhao' => $dado->pavilhao,
                'interno_ala' => $dado->ala,
                'interno_cela' => $dado->cela,
                'interno_observacao' => $dado->observacao,
                'interno_regime' => $dado->regime,
                'interno_faccao' => $faccaoInterno,
                'sancao_triagem' => $sansao_triagem_status,
                'sancao_triagem_data_final' => $sansao_triagem_data,
                'movimentacao_transferencia_pendente' => $dado->transferencia_pendente,
            ]);
        }

        DB::table('confere_pdf_status')->update(['status'=> 1, 'data_atualizacao' => Tdata::TtimeStampHojeMysql()]);
        DB::table('confere_pdf')->truncate();
        DB::insert('INSERT INTO confere_pdf SELECT * FROM confere_dados');
        DB::table('confere_pdf_status')->update(['status'=> 0, 'data_atualizacao' => Tdata::TtimeStampHojeMysql()]);

    }

}
