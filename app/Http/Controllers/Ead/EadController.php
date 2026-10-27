<?php
/*
|--------------------------------------------------------------------------
| Desenvolvimento: saulo.ricardo_@hotmail.com
|--------------------------------------------------------------------------
|
|
*/

namespace App\Http\Controllers\Ead;

use App\Classes\Tdata;
use App\Http\Controllers\Facade\FacadeController;
use App\Http\Controllers\Interno\InternoController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Unidades\UnidadesController;
use App\Http\Controllers\Permissoes\PermissoesController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Classes\Constantes;

class EadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function callAction($method, $parameters)
    {
        LogController::logAcessoUser();
        $retornoPermissao = PermissoesController::verifyPermission("EadController", $method);
        if($retornoPermissao == false)
        {
            return redirect()->route('inicio')->with('alert-danger', 'Você não tem permissão para acessar essa página!');
        }

        return parent::callAction($method, $parameters);
    }

    public function listaCursos()
    {
        $cursos = DB::table("cursos")
            ->where("status", "A")
            ->select("cursos.*", DB::raw("DATE_FORMAT(cursos.data_inicio,'%d/%m/%Y') as dataInicio"))
            ->get();

        $meusCursos  = DB::table('cursos_servidor')
            ->where("cursos_servidor.cpf", Auth::user()->cpf)
            ->select("cursos_servidor.fk_curso")
            ->get();

        $cursosInscrito = array_column(json_decode($meusCursos), 'fk_curso');

        return view("ead.cursos.listaCursos", compact('cursos','cursosInscrito'));
    }

    public function inscricaoConcurso($idCurso)
    {

        $cpf = Auth::user()->cpf;

        $verificaInscricao = DB::table('cursos_servidor')->where('fk_curso', $idCurso)->where('cpf', $cpf)->count();

        $dadosCurso = DB::table('cursos')->where('id', $idCurso)->get()[0];

        if($dadosCurso->restrito == 'S'){

            if ($verificaInscricao == 0) {

                $verificaLiberado = DB::table('curso_restrito_inscricao')->where('fk_curso', $idCurso)->where('cpf', $cpf)->count();

                if ($verificaLiberado > 0) {
                    $inscricao = DB::table('cursos_servidor')->insert([
                            'data_inscricao' => Tdata::TdataHojeMysql(),
                            'fk_curso' => $idCurso,
                            'cpf' => $cpf,
                        ]

                    );
                    $idInsert = DB::getPdo()->lastInsertId();

                    if ($idInsert) {
                        return redirect()->route('ead.meusCursos')->with('alert-success', 'Inscrição realizada com sucesso!');

                    }

                } else {

                    return redirect()->back()->with('alert-danger', 'Esse curso necessita de indicação para inscrição');
                }
            }
            else {

                return redirect()->back()->with('alert-danger', 'Você já possui uma inscrição ativa para este curso');

            }
        }else {

            if ($verificaInscricao == 0) {
                $inscricao = DB::table('cursos_servidor')->insert([
                        'data_inscricao' => Tdata::TdataHojeMysql(),
                        'fk_curso' => $idCurso,
                        'cpf' => $cpf,
                    ]

                );
                $idInsert = DB::getPdo()->lastInsertId();

                if ($idInsert) {
                    return redirect()->route('ead.meusCursos')->with('alert-success', 'Inscrição realizada com sucesso!');

                }
            } else {

                return redirect()->back()->with('alert-danger', 'Você já possui uma inscrição ativa para este curso');

            }

        }

    }

    public function meusCursos()
    {

        $meusCursos  = DB::table('cursos_servidor')
            ->leftJoin("cursos","cursos.id","=","cursos_servidor.fk_curso")
            ->where("cursos_servidor.cpf", Auth::user()->cpf)
            ->select("cursos.*", "cursos_servidor.*", DB::raw("DATE_FORMAT(cursos_servidor.data_inscricao,'%d/%m/%Y') as dataInscricao"), "cursos.id as cursoId")
            ->get();

       // dd($meusCursos);

        return view('ead.cursos.meusCursos', compact('meusCursos'));
    }
    public function aulas($idCurso, $idAula)
    {
        $servidor = Auth::user()->cpf;

        $vInscricaoCurso = DB::table('cursos_servidor')->where('cpf', $servidor)->where('fk_curso', $idCurso)->count();
        if($vInscricaoCurso == 0)
        {
            return redirect()->route('ead.listaCursos')->with('alert-danger', 'Você não está inscrito nesse curso!');

        }

        $modulos = DB::table("cursos_modulo")->where("fk_curso", $idCurso)->where("status", "A")->orderBy("ordem", "ASC")->get();

        $dados = array();

        foreach($modulos as $modulo){

            $aulas = DB::table("cursos_aulas")->where("fk_modulo", $modulo->id)->where("status", "A")->orderBy("ordem", "ASC")->get();
            $vConclusaoModulo = DB::table('cursos_servidor_modulos')->where("fk_modulo", $modulo->id)->count();
            if($vConclusaoModulo > 0)
            {
                $conclusaoModulo = "S";
            }else{
                $conclusaoModulo = null;
            }

            array_push($dados, array(
               'id' => $modulo->id,
               'titulo' => $modulo->titulo,
                'ordem' => $modulo->ordem,
                'conclusao' => $conclusaoModulo,
                'aulas' => $aulas,
            ));

        }


        if($idAula != 0)
        {
            $aulaSelecionada = DB::table("cursos_aulas")->where("id", $idAula)->where("status", "A")->get()[0];
            $vProgresso = DB::table('cursos_servidor_aulas')->where('servidor', Auth::user()->cpf)->where('fk_aula', $aulaSelecionada->id)->count();
            if($vProgresso == 0)
            {
                DB::table('cursos_servidor_aulas')->insert(
                    [
                        'servidor' => Auth::user()->cpf,
                        'fk_modulo' => $aulaSelecionada->fk_modulo,
                        'fk_aula' => $aulaSelecionada->id,
                        'tempo' => 0,
                        'data' => Tdata::TtimeStampHojeMysql(),
                    ]
                );
                $progresso = 0;
            }else{
                $ultimoProgresso = DB::table('cursos_servidor_aulas')->where('servidor', Auth::user()->cpf)->where('fk_aula', $aulaSelecionada->id)->get()[0];
                $progresso = $ultimoProgresso->tempo;
            }

            $aulasConcluidasDB = DB::table('cursos_servidor_aulas')->where('servidor', Auth::user()->cpf)->where('conclusao', "S")->get();
            $aulasConcluidas = array_column(json_decode($aulasConcluidasDB), 'fk_aula');

            $proximaAula = self::getProximaAula($aulaSelecionada->id);

            return view("ead.cursos.aulas", compact('dados','aulaSelecionada','idCurso','progresso','aulasConcluidas','proximaAula'));

        }else{



            $ultimaAulaAssistida = DB::table("cursos_servidor_aulas")
                ->leftJoin('cursos_aulas','cursos_aulas.id','=','cursos_servidor_aulas.fk_aula')
                ->leftJoin('cursos_modulo','cursos_modulo.id','=','cursos_aulas.fk_modulo')
                ->where("cursos_servidor_aulas.servidor", Auth::user()->cpf)
                ->where("cursos_modulo.fk_curso", $idCurso)
                ->where("cursos_servidor_aulas.tempo",'!=', 0)
                ->select('cursos_aulas.*','cursos_aulas.id as id','cursos_modulo.*','cursos_servidor_aulas.*')
                ->orderBy('cursos_servidor_aulas.data', 'DESC')
//                ->orderBy('cursos_modulo.ordem', 'DESC')
//                ->orderBy('cursos_aulas.ordem', 'DESC')
                ->get();

            if(count($ultimaAulaAssistida) > 0){

                $aulaSelecionada = DB::table("cursos_aulas")->where("id", $ultimaAulaAssistida[0]->fk_aula)->get()[0];

                return redirect()->route('ead.aulas', ['idCurso' => $idCurso, 'idAula'=>$aulaSelecionada->id]);


            }else{

                $modulosUm = DB::table("cursos_modulo")->where("fk_curso", $idCurso)->where("status", "A")->orderBy("ordem", "ASC")->get()[0];
                $aulaSelecionada = DB::table("cursos_aulas")->where("fk_modulo", $modulosUm->id)->where("status", "A")->get();

                return redirect()->route('ead.aulas', ['idCurso' => $idCurso, 'idAula'=>$aulaSelecionada[0]->id]);


//                $aulasConcluidasDB = DB::table('cursos_servidor_aulas')->where('servidor', Auth::user()->cpf)->where('conclusao', "S")->get();
//                $aulasConcluidas = array_column(json_decode($aulasConcluidasDB), 'fk_aula');
//                return view("ead.cursos.aulas", compact('dados','aulaSelecionada','idCurso','progresso','aulasConcluidas'));

            }


        }

//        $aulasConcluidasDB = DB::table('cursos_servidor_aulas')->where('servidor', Auth::user()->cpf)->where('conclusao', "S")->get();
//        $aulasConcluidas = array_column(json_decode($aulasConcluidasDB), 'fk_aula');
//
//        return view("ead.cursos.aulas", compact('dados','aulaSelecionada','idCurso','progresso','aulasConcluidas'));
    }

    public function saveProgressAjax(Request $request)
    {
        $progresso = $request->get('progresso');
        $idAula = $request->get('idAula');
        $conclusao = $request->get('end');

        $progressoAnterior = DB::table("cursos_servidor_aulas")->where("servidor", Auth::user()->cpf)->where("fk_aula", $idAula)->get()[0];

        if($progressoAnterior->conclusao != 'S') {


            if ($progresso >= $progressoAnterior->tempo) {

                DB::table("cursos_servidor_aulas")->where("servidor", Auth::user()->cpf)->where("fk_aula", $idAula)->update([
                    'tempo' => $progresso,
                    'conclusao' => $conclusao,
                    'data' => Tdata::TtimeStampHojeMysql(),
                ]);

                $qtdAulasModulo = self::getQtdAulasModulo($progressoAnterior->fk_modulo);

                $qtdAulasModuloConcluidas = self::getQtdAulasModuloConcluidas($progressoAnterior->fk_modulo);

                $idCurso = self::getIdCursoModulo($progressoAnterior->fk_modulo);

                if($qtdAulasModuloConcluidas >= $qtdAulasModulo)
                {
                    $vConclusaoModulo = DB::table("cursos_servidor_modulos")->where('servidor', Auth::user()->cpf)->where('fk_modulo', $progressoAnterior->fk_modulo)->get();

                    if(count($vConclusaoModulo) == 0) {

                        DB::table("cursos_servidor_modulos")->insert([
                            'servidor' => Auth::user()->cpf,
                            'fk_curso' => $idCurso,
                            'fk_modulo' => $progressoAnterior->fk_modulo,
                            'conclusao' => 'S',
                            'data' => Tdata::TtimeStampHojeMysql()
                        ]);
                    }
                }

                $qtdModulosCurso = self::getQtdModulosCurso($idCurso);
                $qtdModulosConcluidos = self::getQtdModulosConcluidos($idCurso);

                if($qtdModulosConcluidos >= $qtdModulosCurso)
                {
                    DB::table('cursos_servidor')->where("cpf", Auth::user()->cpf)->where('fk_curso', $idCurso)->update([
                        'conclusao' => 'S',
                        'conclusao_data' => Tdata::TtimeStampHojeMysql()
                    ]);
                }
            }


        }
    }

    public static function getQtdAulasModulo($idModulo)
    {
        $qtd = DB::table("cursos_aulas")->where("fk_modulo", $idModulo)->count();

        return $qtd;
    }

    public static function getQtdAulasModuloConcluidas($idModulo)
    {
        $qtd = DB::table("cursos_servidor_aulas")->where('servidor', Auth::user()->cpf)->where("fk_modulo", $idModulo)->where("conclusao", "S")->count();

        return $qtd;
    }

    public static function getQtdModulosCurso($idCurso)
    {
        $qtd = DB::table("cursos_modulo")->where("fk_curso", $idCurso)->count();

        return $qtd;
    }

    public static function getIdCursoModulo($idModulo)
    {
        $dados = DB::table("cursos_modulo")->where("id", $idModulo)->get()[0];

        return $dados->fk_curso;
    }

    public static function getQtdModulosConcluidos($idCurso)
    {
        $qtd = DB::table("cursos_servidor_modulos")->where('servidor', Auth::user()->cpf)->where("fk_curso", $idCurso)->where("conclusao", "S")->count();

        return $qtd;
    }

    public static function getProximaAula($idAula)
    {
        $aulaDados = DB::table('cursos_aulas')
            ->leftJoin('cursos_modulo','cursos_modulo.id','=', 'cursos_aulas.fk_modulo')
            ->where('cursos_aulas.id', $idAula)
            ->select('cursos_aulas.*', 'cursos_aulas.ordem AS ordemAula', 'cursos_modulo.*', 'cursos_modulo.ordem AS ordemModulo')
            ->get()[0];

        $proximaAula = DB::table('cursos_aulas')->where('fk_modulo', $aulaDados->fk_modulo)->where('ordem', $aulaDados->ordemAula +1)->get();

        if(count($proximaAula) > 0){

            //dd("proxima aula no mesmo módulo", $proximaAula[0]);
            return $proximaAula[0]->id;

        }else{

            $proximoModulo = DB::table('cursos_modulo')->where('fk_curso', $aulaDados->fk_curso)->where('ordem', $aulaDados->ordemModulo +1)->get();

            if(count($proximoModulo) > 0)
            {
                $proxAula = DB::table('cursos_aulas')->where('fk_modulo', $proximoModulo[0]->id)->orderBy('ordem', 'ASC')->get()[0];

                //dd('proxima aula do proximo modulo', $proxAula);

                return $proxAula->id;

            }else{

                return 0;
                //dd("curso acabou");
            }

        }

    }
}
