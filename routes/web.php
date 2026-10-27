<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/logout/','Auth\LoginController@logout')->name('logout');
Route::get('/', 'HomeController@index')->name('inicio');
Route::get('/inicio', 'HomeController@index')->name('inicio');
Route::get('/home', 'HomeController@index')->name('inicio');
Route::get('/mapa-unidades', 'HomeController@exibirMapa')->name('mapa-unidades');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request')->middleware('auth');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email')->middleware('auth');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset')->middleware('auth');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->middleware('auth');
Route::any('passwordreset', 'Auth\ResetPasswordController@alterarSenha')->name('alterar.senha')->middleware('auth');


/**
 * rotas que se relacionam com o objeto interno
 */
Route::get('/interno/separacao/{dia}', 'Interno\InternoController@separacao')->name('interno.separacao');
Route::post('/interno/get-obito-ajax', 'Interno\InternoController@getObito')->name('interno.getObito');
Route::get('/interno/saida-sancao-triagem', 'Interno\InternoController@saidaSancaoTriagem')->name('interno.saidaSancaoTriagem');
Route::any('/interno/get-sancao-triagem-ativa', 'Interno\InternoController@getSancaoOuTriagemAtiva')->name('interno.getSancaoOuTriagemAtiva');
Route::post('/interno/saida-sancao-triagem-store/{tipo?}', 'Interno\InternoController@saidaSancaoTriagemStore')->name('interno.saidaSancaoTriagemStore');
Route::get('/interno/cadastro', 'Interno\InternoController@cadastro')->name('interno.cadastro');
Route::post('/interno/cadastro/store', 'Interno\InternoController@store')->name('interno.cadastro.store');
Route::post('/interno/cadastro/validar', 'Interno\InternoController@validarCadastro')->name('interno.cadastro.validar');
Route::get('/interno/buscar', 'Interno\InternoController@buscar')->name('interno.buscar');
Route::any('/interno/lista-busca-internos', 'Interno\InternoController@gerarListaBusca')->name('interno.gerarListaBusca');
Route::any('/interno/prontuario-pdf-interno/{id?}', 'Interno\InternoController@gerarPdfProntuario')->name('interno.prontuarioPdf');
Route::any('/interno/editar/{id}', 'Interno\InternoController@editar')->name('interno.editar');
Route::any('/interno/reativar/{id}', 'Interno\InternoController@reativar')->name('interno.reativar');
Route::any('/interno/reativar-action/{id}', 'Interno\InternoController@reativarFunction')->name('interno.reativarFunction');
Route::any('/interno/update/{id?}', 'Interno\InternoController@update')->name('interno.update');
//Route::get('/interno/show/{id?}', 'Interno\InternoController@show')->name('interno.show');
// rotas show interno
Route::get('/interno/show/{id?}', 'Interno\InternoController@showDadosPessoais')->name('interno.show');
Route::get('/interno/show/dados-fotos/{id?}', 'Interno\InternoController@showFotos')->name('interno.showFotos');
Route::get('/interno/show/dados-processos/{id?}', 'Interno\InternoController@showProcessos')->name('interno.showProcessos');
Route::get('/interno/show/dados-historico/{id?}', 'Interno\InternoController@showHistorico')->name('interno.showHistorico');
Route::get('/interno/show/dados-visitantes/{id?}', 'Interno\InternoController@showVisitantes')->name('interno.showVisitantes');
Route::get('/interno/show/dados-advogados/{id?}', 'Interno\InternoController@showAdvogados')->name('interno.showAdvogados');
Route::get('/interno/show/dados-disciplina/{id?}', 'Interno\InternoController@showDisciplina')->name('interno.showDisciplina');
Route::get('/interno/show/dados-ocorrencias/{id?}', 'Interno\InternoController@showOcorrencia')->name('interno.showOcorrencia');
Route::get('/interno/show/dados-escoltas/{id?}', 'Interno\InternoController@showEscoltas')->name('interno.showEscoltas');
Route::get('/interno/show/dados-saude-assistencia/{id?}', 'Interno\InternoController@showSaudeAssistencia')->name('interno.showSaudeAssistencia');
Route::get('/interno/show/dados-documentos/{id?}', 'Interno\InternoController@showDocumentos')->name('interno.showDocumentos');
Route::get('/interno/show/dados-imprimir/{id?}', 'Interno\InternoController@showImprimir')->name('interno.showImprimir');
// -- fim rotas show interno

Route::any('/interno/show/{id}/companheiros-cela', 'Interno\InternoController@companheirosCela')->name('interno.companheirosCela');
// busca via ajax
Route::any('/interno/update/{id}', 'Interno\InternoController@update')->name('interno.update');
Route::get('/interno/show/{id}', 'Interno\InternoController@show')->name('interno.show');
// busca por ajax
Route::any('/interno/busca-ajax', 'Interno\InternoController@buscaNomeAjax')->name('interno.buscaNomeAjax');
Route::any('/interno/get-visitas-interno-ajax', 'Interno\InternoController@getVisitasInternoAjax')->name('interno.getVisitasInternoAjax');
Route::any('/interno/get-atendimento-adv-interno-ajax', 'Interno\InternoController@getAtendimentoAdvogadoInternoAjx')->name('interno.getAtendimentoAdvogadoInternoAjx');
Route::any('/interno/get-saude-assistencia-ajax', 'Interno\InternoController@getSaudeAssistenciaAjax')->name('interno.getSaudeAssistenciaAjax');
Route::any('/interno/documento-delete/{id}/{filename}', 'Interno\InternoController@documentoDelete')->name('interno.documentoDelete');
Route::any('/interno/confere-geral/', 'Interno\InternoController@confereGeral')->name('interno.confereGeral');
Route::any('/interno/confere-geral-store/', 'Interno\InternoController@confereGeralStore')->name('interno.confereGeralStore');
Route::any('/interno/confere-geral-estatus-ajax/', 'Interno\InternoController@confereGeralStatusAjax')->name('interno.confereGeralStatusAjax');
Route::any('/interno/atestados-certidoes/', 'Interno\InternoController@atestadosCertidoes')->name('interno.atestadosCertidoes');
Route::any('/interno/atestados-certidoes-store/', 'Interno\InternoController@atestadosCertidoesStore')->name('interno.atestadosCertidoesStore');

Route::get('fotoap/{id}', function ($id)
{
    $path = "https://rhsiapenba.depen.gov.br/storage/app/arquivos/servidores/".$id."/foto/".$id.".jpeg";

    $contents = file_get_contents($path);

    $response = Response::make($contents, 200);
    $response->header("Content-Type:","image");

    return $response;

})->middleware('auth');

Route::get('storage/app/arquivos/internos/fotos/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/internos/fotos/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        $path = storage_path('app/semfoto.jpg');
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->middleware('auth');


Route::get('storage/app/arquivos/internos/documentos/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/internos/documentos/' . $id . '/' . $filename);

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('interno.fileDoc')->middleware('auth');


Route::get('storage/app/semfoto.jpg', function ()
{
    $path = storage_path('app/semfoto.jpg');

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->middleware('auth');

Route::get('storage/app/avatarsemfoto.png', function ()
{
    $path = storage_path('app/avatarsemfoto.png');

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->middleware('auth');
// --------------------------------------       !interno           -------------------------------------------------------------------


/**
 * rotas que se relacionam com o objeto Visita
 */
Route::any('/visita/buscar', 'Visita\VisitaController@buscar')->name('visita.buscar');
Route::any('/visita/editar/{id}', 'Visita\VisitaController@editar')->name('visita.editar');
Route::any('/visita/update/{id}', 'Visita\VisitaController@update')->name('visita.update');
Route::get('/visita/show/{id?}', 'Visita\VisitaController@show')->name('visita.show');
Route::get('/visita/cadastro', 'Visita\VisitaController@cadastro')->name('visita.cadastro');
Route::post('/visita/store', 'Visita\VisitaController@store')->name('visita.store');
Route::any('/visita/busca-ajax', 'Visita\VisitaController@buscaAjaxKeyUp')->name('visita.buscaAjaxKeyUp');
Route::any('/visita/get-relacao-internos-ajax', 'Visita\VisitaController@getRelacaoInternos')->name('visita.getRelacaoInternos');
Route::any('/visita/documento-delete/{id}/{filename}', 'Visita\VisitaController@documentoDelete')->name('visita.documentoDelete');
Route::any('/visita/suspender/{idVisita}/{idInterno}', 'Visita\VisitaController@suspenderVisita')->name('visita.suspenderVisita');
Route::any('/visita/permitir/{idVisita}/{idInterno}', 'Visita\VisitaController@permitirVisita')->name('visita.permitirVisita');
Route::any('/visita/get-visitas-interno-ajax', 'Visita\VisitaController@getVisitasInternoAjax')->name('visita.getVisitasInternoAjax');
Route::any('/visita/confere-visita', 'Visita\VisitaController@confereVisita')->name('visita.confereVisita');
Route::any('/visita/confere-visita-store', 'Visita\VisitaController@confereVisitaStore')->name('visita.confereVisitaStore');

Route::any('/visita/emitir-carteira/{cpf}', 'Visita\VisitaController@emitirCarteira')->name('visita.emitirCarteira');

Route::get('storage/app/arquivos/visitas/fotos/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/visitas/fotos/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('visitas.fotoPerfil')->middleware('auth');

Route::get('storage/app/arquivos/visitas/documentos/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/visitas/documentos/' . $id . '/' . $filename);

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('visita.fileDoc')->middleware('auth');
// --------------------------------------       !visita           -------------------------------------------------------------------


/**
 * rotas que se relacionam com o objeto Advogado
 */
Route::get('/advogado/buscar', 'Advogado\AdvogadoController@buscar')->name('advogado.buscar');
Route::get('/advogado/show/{id?}', 'Advogado\AdvogadoController@show')->name('advogado.show');
Route::any('/advogado/get-atendimento-adv-interno-ajax', 'Advogado\AdvogadoController@getAtendimentoAdvogadoInternoAjx')->name('advogado.getAtendimentoAdvogadoInternoAjx');
Route::any('/advogado/editar/{id}', 'Advogado\AdvogadoController@editar')->name('advogado.editar');
Route::any('/advogado/update/{id}', 'Advogado\AdvogadoController@update')->name('advogado.update');
Route::get('/advogado/cadastro', 'Advogado\AdvogadoController@cadastro')->name('advogado.cadastro');
Route::post('/advogado/store', 'Advogado\AdvogadoController@store')->name('advogado.store');
Route::any('/advogado/busca-ajax', 'Advogado\AdvogadoController@buscaAjaxKeyUp')->name('advogado.buscaAjaxKeyUp');
Route::any('/advogado/get-relacao-interno-ajax', 'Advogado\AdvogadoController@relacionamentoInternoAjax')->name('advogado.relacionamentoInternoAjax');
Route::any('/advogado/inicio-atendimento', 'Advogado\AdvogadoController@inicioAtendimento')->name('advogado.inicioAtendimento');
Route::any('/advogado/inicio-atendimento-store', 'Advogado\AdvogadoController@inicioAtendimentoStore')->name('advogado.inicioAtendimentoStore');
Route::any('/advogado/inicio-atendimento-update/{id}', 'Advogado\AdvogadoController@inicioAtendimentoUpdate')->name('advogado.inicioAtendimentoUpdate');
Route::post('/advogado/atendimento-update/{id}', 'Advogado\AdvogadoController@updateAtendimento')->name('advogado.atendimentoUpdate');
Route::any('/advogado/atendimento-historico', 'Advogado\AdvogadoController@atendimentoHistorico')->name('advogado.atendimentoHistorico');
Route::any('/advogado/documento-delete/{id}/{filename}', 'Advogado\AdvogadoController@documentoDelete')->name('advogado.documentoDelete');
Route::any('/advogado/envio-senha-acesso/{id}', 'Advogado\AdvogadoController@envioSenha')->name('advogado.envioSenha');
Route::any('/advogado/cancelar-agendamentos/', 'Advogado\AdvogadoController@cancelarAgendamentos')->name('advogado.cancelarAgendamentos');
Route::any('/advogado/cancelar-agendamentos-store/', 'Advogado\AdvogadoController@cancelarAgendamentosStore')->name('advogado.cancelarAgendamentosStore');
// cancela um unico agendamento
Route::any('/advogado/registrar-falta-advogado-agendamento/{idAgendamento}', 'Advogado\AdvogadoController@registrarFaltaAdvAgendamento')->name('advogado.registrarFaltaAdvAgendamento');
Route::any('/advogado/confirmar-realizacao-agendamento/{idAgendamento}', 'Advogado\AdvogadoController@confirmarRealizacaoAdvAgendamento')->name('advogado.confirmarRealizacaoAdvAgendamento');

Route::get('storage/app/arquivos/advogados/fotos/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/advogados/fotos/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('advogados.fotoPerfil')->middleware('auth');

Route::get('storage/app/arquivos/advogados/documentos/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/advogados/documentos/' . $id . '/' . $filename);

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('advogado.fileDoc')->middleware('auth');
// --------------------------------------       !advogado           -------------------------------------------------------------------


/**
 * Rotas de Migração
 */
Route::get('/migracao/main/saulo', 'MigracaoController@mainSaulo');
Route::get('/migracao/main/cassemiro', 'MigracaoController@mainCassemiro');
Route::get('/migracao/main-artigo', 'MigracaoController@mainArtigo');
Route::any('/migracao/documento-interno/', 'MigracaoController@updateDocumentosInterno');


/**
 * rotas que se relacionam com o objeto Movimentações
 */

Route::get('/movimentacoes/mudancacela', 'Movimentacoes\MovimentacoesController@mudancacelaShow')->name('movimentacoes.mudancacelaShow');
Route::get('/movimentacoes/mudancacela/buscar', 'Movimentacoes\MovimentacoesController@mudancacelaBuscar')->name('movimentacoes.mudancacelaBuscar');
Route::post('/movimentacoes/mudancacela/storeMudarCela', 'Movimentacoes\MovimentacoesController@storeMudarCela')->name('movimentacoes.mudancacelaStore');

Route::get('/movimentacoes/progressaoregressao', 'Movimentacoes\MovimentacoesController@progressaoregressaoShow')->name('movimentacoes.progressaoregressaoShow');
Route::post('/movimentacoes/progressaoregressao/store', 'Movimentacoes\MovimentacoesController@storeProgressaoRegressao')->name('movimentacoes.progressaoregressaoStore');
Route::get('/movimentacoes/mudancasentenca', 'Movimentacoes\MovimentacoesController@mudancaSentencaShow')->name('movimentacoes.mudancasentencaStoreShow');
Route::post('/movimentacoes/mudancasentenca/store', 'Movimentacoes\MovimentacoesController@storeMudancaSentenca')->name('movimentacoes.mudancasentencaStore');

Route::get('/movimentacoes/alvara', 'Movimentacoes\MovimentacoesController@alvaraShow')->name('movimentacoes.alvaraShow');
Route::post('/movimentacoes/alvara/store', 'Movimentacoes\MovimentacoesController@storeAlvara')->name('movimentacoes.alvaraStore');

Route::get('/movimentacoes/saidajudicial', 'Movimentacoes\MovimentacoesController@saidajudicialShow')->name('movimentacoes.saidajudicialShow');
Route::post('/movimentacoes/saidajudicial/store', 'Movimentacoes\MovimentacoesController@saidajudicialStore')->name('movimentacoes.saidajudicialStore');

Route::get('/movimentacoes/livramentocondicional', 'Movimentacoes\MovimentacoesController@livramentocondicionalShow')->name('movimentacoes.livramentocondicionalShow');
Route::post('/movimentacoes/livramentocondicional/store', 'Movimentacoes\MovimentacoesController@storeLivramentoCondicional')->name('movimentacoes.livramentocondicionalStore');

Route::get('/movimentacoes/extradicao', 'Movimentacoes\MovimentacoesController@extradicaoShow')->name('movimentacoes.extradicaoShow');
Route::post('/movimentacoes/extradicao/store', 'Movimentacoes\MovimentacoesController@storeExtradicao')->name('movimentacoes.extradicaoStore');

Route::get('/movimentacoes/fuga', 'Movimentacoes\MovimentacoesController@fugaShow')->name('movimentacoes.fugaShow');
Route::post('/movimentacoes/fuga/store', 'Movimentacoes\MovimentacoesController@storeFuga')->name('movimentacoes.fugaStore');

Route::get('/movimentacoes/recambiamento', 'Movimentacoes\MovimentacoesController@recambiamentoShow')->name('movimentacoes.recambiamentoShow');
Route::post('/movimentacoes/recambiamento/store', 'Movimentacoes\MovimentacoesController@storeRecambiamento')->name('movimentacoes.recambiamentoStore');

Route::get('/movimentacoes/obito', 'Movimentacoes\MovimentacoesController@obitoShow')->name('movimentacoes.obitoShow');
Route::post('/movimentacoes/obito/store/{RegistroOcorrencia?}', 'Movimentacoes\MovimentacoesController@storeObito')->name('movimentacoes.obitoStore');

Route::get('/movimentacoes/extincaopunibilidade', 'Movimentacoes\MovimentacoesController@extincaopunibilidadeShow')->name('movimentacoes.extincaopunibilidadeShow');
Route::post('/movimentacoes/extincaopunibilidade/store', 'Movimentacoes\MovimentacoesController@storeExtincaoPunibilidade')->name('movimentacoes.extincaopunibilidadeStore');

Route::get('/movimentacoes/prisaodomiciliar', 'Movimentacoes\MovimentacoesController@prisaodomiciliarShow')->name('movimentacoes.prisaodomiciliarShow');
Route::post('/movimentacoes/prisaodomiciliar/store', 'Movimentacoes\MovimentacoesController@storePrisaoDomiciliar')->name('movimentacoes.prisaodomiciliarStore');

Route::get('/movimentacoes/outros', 'Movimentacoes\MovimentacoesController@outrosShow')->name('movimentacoes.outrosShow');
Route::post('/movimentacoes/outros/store', 'Movimentacoes\MovimentacoesController@storeOutros')->name('movimentacoes.outrosStore');

Route::get('/movimentacoes/transferencia', 'Movimentacoes\MovimentacoesController@transferenciaShow')->name('movimentacoes.transferenciaShow');
Route::post('/movimentacoes/transferir/store', 'Movimentacoes\MovimentacoesController@storeTransferir')->name('movimentacoes.transferir.store'); // registra a transferência
Route::post('/movimentacoes/transferir/receberInterno', 'Movimentacoes\MovimentacoesController@receberInterno')->name('movimentacoes.transferencia.receberInterno'); // recebe ou cancela o recebimento
Route::post('/movimentacoes/transferir/cancelarTransfEnv', 'Movimentacoes\MovimentacoesController@cancelarTransfEnv')->name('movimentacoes.transferencia.cancelarTransfEnv'); // cancela o envio
Route::post('/movimentacoes/transferir/alocarInterno', 'Movimentacoes\MovimentacoesController@alocarInterno')->name('movimentacoes.transferencia.alocarInterno'); // aloca na cela

Route::any('/movimentacoes/transferencia/transferir', 'Movimentacoes\MovimentacoesController@transferenciaTransferir')->name('movimentacoes.transferenciaTransferir');
Route::any('/movimentacoes/transferencia/receber-interno', 'Movimentacoes\MovimentacoesController@transferenciaReceberInterno')->name('movimentacoes.receberInterno');

Route::any('/movimentacoes/transferencia/internos-pendentes-confirmacao-ajax', 'Movimentacoes\MovimentacoesController@internosPendentesConfirmacaoAjax')->name('movimentacoes.internosPendentesConfirmacaoAjax');
Route::any('/movimentacoes/transferencia/internos-pendentes-recebimento-confirmacao-ajax', 'Movimentacoes\MovimentacoesController@internosPendentesRecebimentoConfirmacaoAjax')->name('movimentacoes.internosPendentesRecebimentoConfirmacaoAjax');
Route::any('/movimentacoes/transferencia/internos-pendentes-alocacao-cela-ajax', 'Movimentacoes\MovimentacoesController@internosPendentesAlocacaoAjax')->name('movimentacoes.internosPendentesAlocacaoAjax');

Route::any('/movimentacoes/transferencia/receber-internos', 'Movimentacoes\MovimentacoesController@ReceberInternos')->name('movimentacoes.ReceberInternos');

Route::any('/movimentacoes/transferencia-com-progressao', 'Movimentacoes\MovimentacoesController@transferenciaProgressao')->name('movimentacoes.transferencia.transferenciaProgressao');
Route::any('/movimentacoes/transferencia-com-progressao/store', 'Movimentacoes\MovimentacoesController@transferenciaProgressaoStore')->name('movimentacoes.transferencia.transferenciaProgressaoStore');

Route::get('storage/app/movimentacao/transferencia/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/movimentacao/transferencia/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('movimentacao.transferenciaHist.arquivoVisualizar')->middleware('auth');

Route::get('storage/app/movimentacao/recambiamento/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/movimentacao/recambiamento/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('movimentacao.recambiamento.arquivoVisualizar')->middleware('auth');

Route::get('storage/app/movimentacao/progressaoregressao/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/movimentacao/progressaoregressao/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('movimentacao.progressaoregressao.arquivoVisualizar')->middleware('auth');

Route::get('storage/app/movimentacao/mudancasentenca/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/movimentacao/mudancasentenca/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('movimentacao.mudancasentenca.arquivoVisualizar')->middleware('auth');

Route::get('storage/app/movimentacao/prisaodomiciliar/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/movimentacao/prisaodomiciliar/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('movimentacao.prisaodomiciliar.arquivoVisualizar')->middleware('auth');

Route::get('storage/app/movimentacao/obito/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/movimentacao/obito/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('movimentacao.obito.arquivoVisualizar')->middleware('auth');

Route::get('storage/app/movimentacao/mudancacela/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/movimentacao/mudancacela/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('movimentacao.mudancaCela.arquivoVisualizar')->middleware('auth');

Route::get('storage/app/movimentacao/livramentocondicional/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/movimentacao/livramentocondicional/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('movimentacao.livramentocondicional.arquivoVisualizar')->middleware('auth');

Route::get('storage/app/movimentacao/fuga/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/movimentacao/fuga/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('movimentacao.fuga.arquivoVisualizar')->middleware('auth');

Route::get('storage/app/movimentacao/extincaopunibilidade/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/movimentacao/extincaopunibilidade/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('movimentacao.extincaopunibilidade.arquivoVisualizar')->middleware('auth');

Route::get('storage/app/movimentacao/extradicao/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/movimentacao/extradicao/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('movimentacao.extradicao.arquivoVisualizar')->middleware('auth');

Route::get('storage/app/movimentacao/alvara/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/movimentacao/alvara/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('movimentacao.alvara.arquivoVisualizar')->middleware('auth');

Route::get('storage/app/movimentacao/saidajudicial/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/movimentacao/saidajudicial/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('movimentacao.saidajudicial.arquivoVisualizar')->middleware('auth');

/**
 * rotas que se relacionam com o objeto Escolta
 */
Route::get('/escolta/cadastro', 'Escolta\EscoltaController@cadastro')->name('escolta.cadastro');
Route::post('/escolta/store', 'Escolta\EscoltaController@store')->name('escolta.store');
Route::get('/escolta/buscar', 'Escolta\EscoltaController@buscar')->name('escolta.buscar');
Route::get('/escolta/editar/{id}', 'Escolta\EscoltaController@editar')->name('escolta.editar');
Route::any('/escolta/editar-store/{id}', 'Escolta\EscoltaController@editarStore')->name('escolta.editarStore');
Route::any('/escolta/arquivos/{id}', 'Escolta\EscoltaController@arquivos')->name('escolta.arquivos');
Route::get('/escolta/show/{id?}', 'Escolta\EscoltaController@show')->name('escolta.show');
Route::get('/escolta/documento/{id?}', 'Escolta\EscoltaController@documento')->name('escolta.documento');

Route::get('storage/app/arquivos/escolta/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/escolta/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('escolta.arquivo')->middleware('auth');


/**
 * rotas que se relacionam com o objeto GEP
 */
/*
Route::get('/gep/show/{id}', 'Gep\GepController@show')->name('gep.show');
Route::get('/gep/show-escolta/{id}', 'Gep\GepController@showEscolta')->name('gep.showEscolta');
Route::get('/gep/arquivos-escolta/{id}', 'Gep\GepController@arquivosEscolta')->name('gep.arquivosEscolta');
Route::get('/gep/nova-solicitacao', 'Gep\GepController@novaSolicitacao')->name('gep.novaSolicitacao');
Route::post('/gep/store-nova-solicitacao', 'Gep\GepController@storeNovaSolicitacao')->name('gep.storeNovaSolicitacao');
Route::post('/gep/confirmar-solicitacao', 'Gep\GepController@confirmarSolicitacao')->name('gep.confirmarSolicitacao');
Route::post('/gep/cancelar-solicitacao', 'Gep\GepController@cancelarSolicitacao')->name('gep.cancelarSolicitacao');
Route::post('/gep/repassar-solicitacao', 'Gep\GepController@repassarSolicitacao')->name('gep.repassarSolicitacao');
Route::get('/gep/painel-solicitacoes', 'Gep\GepController@painelSolicitacoes')->name('gep.painelSolicitacoes');
Route::get('/gep/nova-escolta', 'Gep\GepController@novaEscolta')->name('gep.novaEscolta');
Route::get('/gep/pdf-lista-internos-pendentes-de-escolta', 'Gep\GepController@internosPendentesEscoltaPdf')->name('gep.internosPendentesEscoltaPdf');
Route::post('/gep/store-nova-escolta', 'Gep\GepController@storeNovaEscolta')->name('gep.storeNovaEscolta');
Route::get('/gep/painel-escoltas', 'Gep\GepController@painelEscoltas')->name('gep.painelEscoltas');
Route::any('/gep/arquivos/{id}', 'Gep\GepController@arquivos')->name('gep.arquivos');
Route::post('/gep/cancelar-escolta', 'Gep\GepController@cancelarEscolta')->name('gep.cancelarEscolta');
Route::post('/gep/finalizar-escolta', 'Gep\GepController@finalizarEscolta')->name('gep.finalizarEscolta');
Route::get('/gep/dados-do-judiciario', 'Gep\GepController@dadosJudiciario')->name('gep.dadosJudiciario');
Route::get('/gep/dados-do-judiciario-inserir', 'Gep\GepController@dadosJudiciarioInserir')->name('gep.dadosJudiciarioInserir');
Route::get('/gep/dados-do-judiciario-editar/{id}', 'Gep\GepController@dadosJudiciarioEditar')->name('gep.dadosJudiciarioEditar');
Route::post('/gep/store-dados-do-judiciario', 'Gep\GepController@storeDadosJudiciario')->name('gep.storeDadosJudiciario');
Route::post('/gep/update-dados-do-judiciario', 'Gep\GepController@updateDadosJudiciario')->name('gep.updateDadosJudiciario');

Route::get('/gep/editar/{id}', 'Gep\GepController@editar')->name('gep.editar');
Route::any('/gep/editar-store/{id}', 'Gep\GepController@editarStore')->name('gep.editarStore');

Route::any('/gep/documento-escolta/{id}', 'Gep\GepController@documentoEscolta')->name('gep.documentoEscolta');
Route::any('/gep/pauta-do-dia/', 'Gep\GepController@pautaDia')->name('gep.pautaDia');
Route::any('/gep/pauta-do-dia-pdf/{data}', 'Gep\GepController@pautaDiaPdf')->name('gep.pautaDiaPdf');
Route::any('/gep/pauta-do-dia-store', 'Gep\GepController@pautaDiaStore')->name('gep.pautaDiaStore');
Route::any('/gep/equipe-do-dia-editar/{id}', 'Gep\GepController@equipeDiaEditar')->name('gep.equipeDiaEditar');
Route::any('/gep/equipe-do-dia-editar-store/{id}', 'Gep\GepController@equipeDiaEditarStore')->name('gep.equipeDiaEditarStore');
Route::any('/gep/busca-equipe-vtr', 'Gep\GepController@buscaEquipeVtrAjax')->name('gep.buscaEquipeVtrAjax');

Route::any('/gep/documentos', 'Gep\GepController@documentos')->name('gep.documentos');
Route::any('/gep/documento-missoes/{data}', 'Gep\GepController@docMissoes')->name('gep.docMissoes');
Route::any('/gep/documento-missoes-capa-missao/{data}', 'Gep\GepController@docMissoesCapaMissao')->name('gep.docMissoesCapaMissao');

Route::any('/gep/separacao-internos-unidade/{data}', 'Gep\GepController@separacaoInternosUnidadePdf')->name('gep.separacaoInternosUnidadePdf');

Route::any('/gep/justificativa-nao-realizacao-escolta', 'Gep\GepController@justificativaNaoRealizacao')->name('gep.justificativaNaoRealizacao');
Route::any('/gep/justificativa-nao-realizacao-escolta/pdf', 'Gep\GepController@justificativaNaoRealizacaoPdf')->name('gep.justificativaNaoRealizacaoPdf');
*/
/**
 * rotas que se relacionam com o objeto Armaria
 */
Route::get('/armaria/cadastro-equipamento', 'Armaria\ArmariaController@cadastro')->name('armaria.cadastro');
Route::any('/armaria/equipamento-store', 'Armaria\ArmariaController@equipamentoStore')->name('equipamento.store');
Route::any('/armaria/editar-equipamento/{id}', 'Armaria\ArmariaController@editarEquipamento')->name('armaria.editarEquipamento');
Route::any('/armaria/update-equipamento/{id}', 'Armaria\ArmariaController@updateEquipamento')->name('armaria.updateEquipamento');
Route::any('/armaria/equipamento-quantidade/{id}', 'Armaria\ArmariaController@equipamentoQuantidade')->name('equipamento.quantidade');
Route::any('/armaria/equipamento-quantidade-update/{id}', 'Armaria\ArmariaController@equipamentoQuantidadeUpdate')->name('equipamento.quantidadeUpdate');
Route::post('/armaria/ajax-equipamento-fabricante', 'Armaria\ArmariaController@fabricanteEquipamento')->name('equipamento.fabricante');
Route::get('/armaria/buscar', 'Armaria\ArmariaController@buscar')->name('armaria.buscar');
Route::get('/armaria/cautela', 'Armaria\ArmariaController@cautela')->name('armaria.cautela');
Route::get('/armaria/lista-cautela', 'Armaria\ArmariaController@listaCautela')->name('armaria.listaCautela');
Route::any('/armaria/documento-cautela/{id?}', 'Armaria\ArmariaController@documentoCautela')->name('armaria.documentoCautela');
Route::post('/armaria/cautela-store', 'Armaria\ArmariaController@cautelaStore')->name('armaria.cautelaStore');
Route::any('/armaria/cautela-informacao/{id}', 'Armaria\ArmariaController@cautelaInformacao')->name('armaria.cautelaInformacao');
Route::any('/armaria/descautela-form/{id}', 'Armaria\ArmariaController@descautelaForm')->name('armaria.descautelaForm');
Route::any('/armaria/descautela-store/{id}', 'Armaria\ArmariaController@descautelaStore')->name('armaria.descautelaStore');
Route::get('/armaria/lista-descautela', 'Armaria\ArmariaController@listaDescautela')->name('armaria.listaDescautela');
Route::any('/armaria/descautela-informacao/{id}', 'Armaria\ArmariaController@descautelaInformacao')->name('armaria.descautelaInformacao');
Route::any('/armaria/documento-cautela-historico/{id?}', 'Armaria\ArmariaController@documentoCautelaHistorico')->name('armaria.documentoCautelaHistorico');
Route::any('/armaria/documento-descautela/{id?}', 'Armaria\ArmariaController@documentoDescautela')->name('armaria.documentoDescautela');
Route::get('/armaria/mapa-equipamento', 'Armaria\ArmariaController@mapaEquipamento')->name('armaria.mapaequipamento');
Route::get('/armaria/localizacao-equipamento/{id}', 'Armaria\ArmariaController@localizacaoEquipamento')->name('armaria.localizacaoEquipamento');
Route::any('/armaria/equipamento-busca-ajax', 'Armaria\ArmariaController@equipamentoBuscaAjax')->name('equipamento.buscaAjax');
Route::any('/armaria/busca-equipamento-unidade-ajax', 'Armaria\ArmariaController@buscaEquipamentoUnidadeAjax')->name('equipamento.buscaEquipamentoUnidadeAjax');
Route::any('/armaria/relatorio-uso-material', 'Armaria\ArmariaController@relatorioUsoMaterial')->name('armaria.relatorioUsoMaterial');
Route::any('/armaria/visualizar-relatorio-uso-material/{id}', 'Armaria\ArmariaController@visualizarRelatorioUsoMaterial')->name('armaria.visualizarRelatorioUsoMaterial');

Route::any('/armaria/inserir-telefone-servidor', 'Armaria\ArmariaController@inserirTelefoneServidor')->name('armaria.inserirTelefoneServidor');
Route::any('/armaria/inserir-telefone-servidor-store', 'Armaria\ArmariaController@inserirTelefoneServidorStore')->name('armaria.inserirTelefoneServidorStore');

Route::any('/armaria/extravio-equipamento/{id}', 'Armaria\ArmariaController@extravioEquipamento')->name('armaria.extravioEquipamento');
Route::any('/armaria/extravio-equipamento-store/{id}', 'Armaria\ArmariaController@extravioEquipamentoStore')->name('armaria.extravioEquipamentoStore');
Route::any('/armaria/listar-extravio-equipamento', 'Armaria\ArmariaController@extravioEquipamentoListar')->name('armaria.extravioEquipamentoListar');
Route::any('/armaria/ativar-equipamento/{id}', 'Armaria\ArmariaController@extravioEquipamentoAtivar')->name('armaria.extravioEquipamentoAtivar');

Route::any('/armaria/historico-equipamento/{id}', 'Armaria\ArmariaController@EquipamentoHistorico')->name('armaria.historicoEquipamento');

Route::any('/armaria/mapa-localizacao-equipamento/', 'Armaria\ArmariaController@mapaLocalizacaoEquipamento')->name('armaria.mapaLocalizacaoEquipamento');


// --------------------------------------       !Armaria           -------------------------------------------------------------------

/**
 * rotas que se relacionam com o objeto material belico unidade
 */
Route::any('/material-belico-unidade/estoque-unidade', 'MatBelUnidade\MatBelUnidadeController@estoqueUnidade')->name('matBelUnidade.estoqueUnidade');
Route::any('/material-belico-unidade/ficha-uso-material', 'MatBelUnidade\MatBelUnidadeController@usoMaterial')->name('matBelUnidade.UsoMaterial');
Route::any('/material-belico-unidade/ficha-uso-material-store', 'MatBelUnidade\MatBelUnidadeController@usoMaterialStore')->name('matBelUnidade.UsoMaterialStore');
Route::any('/material-belico-unidade/listar-uso-material', 'MatBelUnidade\MatBelUnidadeController@listarUsoMaterial')->name('matBelUnidade.listarUsoMaterial');
Route::any('/material-belico-unidade/visualizar-relatorio-uso-material/{id}', 'MatBelUnidade\MatBelUnidadeController@visualizarRelatorioUsoMaterial')->name('matBelUnidade.visualizarRelatorioUsoMaterial');


// --------------------------------------       !MatBelUnidade           -------------------------------------------------------------------


/**
 * rotas que se relacionam com o objeto ocorrência
 */
Route::get('/ocorrencia/cadastro/{id?}/{returnLivro?}', 'Ocorrencia\OcorrenciaController@cadastro')->name('ocorrencia.cadastro');
Route::post('/ocorrencia/get-ocorrencia-envolvido-ajax', 'Ocorrencia\OcorrenciaController@getOcorrenciaEnvolvidosAjax')->name('ocorrencia.getOcorrenciaEnvolvidosAjax');
Route::any('/ocorrencia/store/{id?}/{returnLivro?}', 'Ocorrencia\OcorrenciaController@store')->name('ocorrencia.store');
Route::any('/ocorrencia/editar/{id}/{returnLivro?}', 'Ocorrencia\OcorrenciaController@editar')->name('ocorrencia.editar');
Route::any('/ocorrencia/editar-store/{id}/{returnLivro?}', 'Ocorrencia\OcorrenciaController@editarStore')->name('ocorrencia.editarStore');
Route::get('/ocorrencia/buscar', 'Ocorrencia\OcorrenciaController@buscar')->name('ocorrencia.buscar');
Route::any('/ocorrencia/deletar-arquivo/{id}', 'Ocorrencia\OcorrenciaController@deletarArquivo')->name('ocorrencia.deletarArquivo');
Route::any('/ocorrencia/visualizar-ocorrencia/{id}', 'Ocorrencia\OcorrenciaController@visualizarOcorrencia')->name('ocorrencia.visualizarOcorrencia');
Route::any('/ocorrencia/excluir-ocorrencia/{id}', 'Ocorrencia\OcorrenciaController@excluirOcorrencia')->name('ocorrencia.excluirOcorrencia');
Route::any('/ocorrencia/livro', 'Ocorrencia\OcorrenciaController@livro')->name('ocorrencia.livro');
Route::any('/ocorrencia/livro-download/{dataLivro}/{unidade}/{horario}', 'Ocorrencia\OcorrenciaController@livroDownload')->name('ocorrencia.livroDownload');
Route::post('/ocorrencia/get-natureza-objeto', 'Ocorrencia\OcorrenciaController@getNaturezaObjeto')->name('ocorrencia.getNaturezaObjeto');


Route::get('storage/app/ocorrencia/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/ocorrencia/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('ocorrencia.arquivoVisualizar')->middleware('auth');


/**
 * rotas que se relacionam com o objeto Mensagem
 */
Route::get('/mensagem/enviar', 'Mensagem\MensagemController@enviar')->name('mensagem.enviar');
Route::get('/mensagem/ler/{id}', 'Mensagem\MensagemController@ler')->name('mensagem.ler');
Route::get('/mensagem/ler-enviada/{id}', 'Mensagem\MensagemController@lerEnviada')->name('mensagem.lerEnviada');
Route::post('/mensagem/enviarStore', 'Mensagem\MensagemController@enviarStore')->name('mensagem.enviarStore');
Route::get('/mensagem/recebidas', 'Mensagem\MensagemController@recebidas')->name('mensagem.recebidas');
Route::post('/mensagem/marcar-lida', 'Mensagem\MensagemController@marcarLida')->name('mensagem.marcarLida');
// excluir por ajax
Route::post('/mensagem/excluir-mensagem-ajax', 'Mensagem\MensagemController@excluirMensagem')->name('mensagem.excluirMensagem');
// excluir mensagem pela rota
Route::get('/mensagem/excluir-mensagem/{id}', 'Mensagem\MensagemController@mensagemExcluir')->name('mensagem.mensagemExcluir');
Route::get('/mensagem/excluir-mensagem-enviada/{id}', 'Mensagem\MensagemController@mensagemExcluirEnviada')->name('mensagem.mensagemExcluirEnviada');
Route::post('/mensagem/excluir-mensagem-enviada-ajax', 'Mensagem\MensagemController@mensagemExcluirEnviadaAjax')->name('mensagem.mensagemExcluirEnviadaAjax');
Route::get('/mensagem/responter-mensagem/{id}', 'Mensagem\MensagemController@responder')->name('mensagem.responder');
Route::get('/mensagem/encaminhar-mensagem/{id}', 'Mensagem\MensagemController@encaminhar')->name('mensagem.encaminhar');
Route::get('/mensagem/enviadas', 'Mensagem\MensagemController@enviadas')->name('mensagem.enviada');

Route::get('storage/app/mensagem/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/mensagem/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('mensagem.arquivoVisualizar')->middleware('auth');


/**
 * rotas que se relacionam com o objeto Agenda
 */
Route::get('/agenda/consulta', 'Agenda\AgendaController@consulta')->name('agenda.consulta');
Route::get('/agenda/gerenciar', 'Agenda\AgendaController@gerenciamento')->name('agenda.gerenciamento');
Route::get('/agenda/cadastro', 'Agenda\AgendaController@cadastro')->name('agenda.cadastroEvento');

/**
 * rotas que se relacionam com o objeto controle de acesso
 */
Route::get('/controle-acesso/registrar-entrada', 'ControleAcesso\ControleAcessoController@registrarEntrada')->name('controleAcesso.registrarEntrada');
Route::post('/controle-acesso/registrar-entrada-store', 'ControleAcesso\ControleAcessoController@registrarEntradaStore')->name('controleAcesso.registrarEntradaStore');
Route::get('/controle-acesso/suspender-entrada/{id}', 'ControleAcesso\ControleAcessoController@suspenderEntrada')->name('controleAcesso.suspenderEntrada');
Route::get('/controle-acesso/liberar-entrada/{id}', 'ControleAcesso\ControleAcessoController@liberarEntrada')->name('controleAcesso.liberarEntrada');
Route::any('/controle-acesso/suspender-entrada-store/{id}', 'ControleAcesso\ControleAcessoController@suspenderEntradaStore')->name('controleAcesso.suspenderEntradaStore');
Route::get('/controle-acesso/alerta-entrada/{id}', 'ControleAcesso\ControleAcessoController@alertaEntrada')->name('controleAcesso.alertaEntrada');
Route::any('/controle-acesso/alerta-entrada-store/{id}', 'ControleAcesso\ControleAcessoController@alertaEntradaStore')->name('controleAcesso.alertaEntradaStore');

Route::any('/controle-acesso/busca-visitante-ajax', 'ControleAcesso\ControleAcessoController@buscaAjaxKeyUp')->name('controleAcesso.buscaAjaxKeyUp');

Route::any('/controle-acesso/alerta-entrada-geral', 'ControleAcesso\ControleAcessoController@alertaEntradaGeral')->name('controleAcesso.alertaEntradaGeral');
Route::any('/controle-acesso/alerta-entrada-geral-store', 'ControleAcesso\ControleAcessoController@alertaEntradaGeralStore')->name('controleAcesso.alertaEntradaGeralStore');

Route::any('/controle-acesso/alerta-entrada-geral-buscar', 'ControleAcesso\ControleAcessoController@alertaEntradaGeralBuscar')->name('controleAcesso.alertaEntradaGeralBuscar');

Route::any('/controle-acesso/alerta-entrada-geral-cancelar/{id}', 'ControleAcesso\ControleAcessoController@alertaEntradaGeralCancelar')->name('controleAcesso.alertaEntradaGeralCancelar');
Route::any('/controle-acesso/alerta-entrada-geral-cancelar-store/{id}', 'ControleAcesso\ControleAcessoController@alertaEntradaGeralCancelarStore')->name('controleAcesso.alertaEntradaGeralCancelarStore');

Route::get('/controle-acesso/editar-cadastro/{id}', 'ControleAcesso\ControleAcessoController@editarCadastro')->name('controleAcesso.editarCadastro');
Route::any('/controle-acesso/editar-cadastro-store/{id}', 'ControleAcesso\ControleAcessoController@editarCadastroStore')->name('controleAcesso.editarCadastroStore');

Route::post('/controle-acesso/buscar-cpf-ajax', 'ControleAcesso\ControleAcessoController@buscarCpfAjax')->name('controleAcesso.buscarCpfAjax');

Route::post('/controle-acesso/buscar-horario-aberto-ajax', 'ControleAcesso\ControleAcessoController@getHorarioSaidaAberto')->name('controleAcesso.getHorarioSaidaAberto');
Route::get('/controle-acesso/registrar-saida', 'ControleAcesso\ControleAcessoController@registrarSaida')->name('controleAcesso.registrarSaida');

Route::post('/controle-acesso/registrar-saida-store', 'ControleAcesso\ControleAcessoController@registrarSaidaStore')->name('controleAcesso.registrarSaidaStore');

Route::get('/controle-acesso/registrar-saida-visita', 'ControleAcesso\ControleAcessoController@registrarSaidaVisita')->name('controleAcesso.registrarSaidaVisita');

Route::any('/controle-acesso/registrar-saida-visita-store', 'ControleAcesso\ControleAcessoController@registrarSaidaVisitaStore')->name('controleAcesso.registrarSaidaVisitaStore');

Route::get('/controle-acesso/gerenciamento', 'ControleAcesso\ControleAcessoController@gerenciamento')->name('controleAcesso.gerenciamento');
Route::get('/controle-acesso/historico', 'ControleAcesso\ControleAcessoController@historico')->name('controleAcesso.historico');
Route::any('/controle-acesso/editar-registro-saida/{id}', 'ControleAcesso\ControleAcessoController@editarRegistrarSaida')->name('controleAcesso.editarRegistrarSaida');
Route::any('/controle-acesso/editar-registro-saida-store', 'ControleAcesso\ControleAcessoController@editarRegistrarSaidaStore')->name('controleAcesso.editarRegistrarSaidaStore');
Route::get('storage/app/arquivos/controle_acesso/fotos/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/controle_acesso/fotos/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('controleAcesso.foto')->middleware('auth');

/**
 * rotas que se relacionam com o objeto Equipe
 */
Route::get('/equipe/dia', 'Equipe\EquipeController@dia')->name('equipe.dia');
Route::get('/equipe/status', 'Equipe\EquipeController@status')->name('equipe.status');
Route::get('/equipe/trocas', 'Equipe\EquipeController@trocas')->name('equipe.trocas');
Route::post('/equipe/trocas-store', 'Equipe\EquipeController@trocasStore')->name('equipe.trocasStore');
Route::get('/equipe/trocas-cancelar/{id}', 'Equipe\EquipeController@trocaCancelar')->name('equipe.trocaCancelar');
Route::get('/equipe/do', 'Equipe\EquipeController@diariaoperacional')->name('equipe.do');
Route::get('/equipe/registrar-equipe/{returnLivro?}', 'Equipe\EquipeController@gerenciarEquipes')->name('equipe.gerenciarequipes');
Route::any('/equipe/registrar-equipe-store/{returnLivro?}', 'Equipe\EquipeController@gerenciarEquipesStore')->name('equipe.gerenciarequipesStore');
Route::get('storage/app/arquivos/equipe/permuta/{filename}', function ($filename)
{
    $path = storage_path('app/arquivos/equipe/permuta/'. $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('equipe.arquivoPermuta')->middleware('auth');


/**
 * rotas que se relacionam com o objeto Sindicancia
 */
Route::get('/sindicancia/cadastro', 'Sindicancia\SindicanciaController@cadastro')->name('sindicancia.cadastro');
Route::post('/sindicancia/store', 'Sindicancia\SindicanciaController@store')->name('sindicancia.store');
Route::any('/sindicancia/buscar', 'Sindicancia\SindicanciaController@buscar')->name('sindicancia.buscar');
Route::any('/sindicancia/visualizar/{id}', 'Sindicancia\SindicanciaController@visualizar')->name('sindicancia.visualizar');
Route::get('/sindicancia/pdf/{id}', 'Sindicancia\SindicanciaController@pdf')->name('sindicancia.pdf');
Route::get('/sindicancia/editar/{id}', 'Sindicancia\SindicanciaController@editar')->name('sindicancia.editar');
Route::any('/sindicancia/editar-store/{id}', 'Sindicancia\SindicanciaController@editarStore')->name('sindicancia.editarStore');
Route::any('/sindicancia/arquivo-delete/{id}', 'Sindicancia\SindicanciaController@arquivoDelete')->name('sindicancia.arquivoDelete');
Route::get('storage/app/arquivos/sindicancia/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/sindicancia/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('sindicancia.arquivoVisualizar')->middleware('auth');

Route::get('storage/app/arquivos/usuarios/cpfimg/{filename}', function ($filename)
{
    $path = storage_path('app/arquivos/usuarios/cpfimg/'. $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('usuarios.cpfimg')->middleware('auth');


/**
 * rotas que se relacionam com o objeto Unidades
 */
Route::get('/unidades/show', 'Unidades\UnidadesController@show')->name('unidades.show');
Route::post('/unidades/get-dados', 'Unidades\UnidadesController@getDados')->name('unidades.getDados');
Route::post('/unidades/get-dados-b', 'Unidades\UnidadesController@getDadosB')->name('unidades.getDadosB');
Route::post('/unidades/listaInternosCela-ajax', 'Unidades\UnidadesController@getInternosCela');
Route::get('/unidades/addqtd', 'Unidades\UnidadesController@addQuantitativo');
Route::any('/unidades/get-horarios-plantao-ajax', 'Unidades\UnidadesController@getHorariosPlantaoAjax');
// utilizado na busca de livro de ocorrencia
Route::any('/unidades/get-horarios-plantao-busca-ajax', 'Unidades\UnidadesController@getHorariosPlantaoBuscaAjax');


/**
 * rotas que se relacionam com o objeto Estatísticas e Relatórios
 */
//Route::any('/estatisticas/show', 'estatisticas\EstatisticasController@show')->name('estatisticas.show');
//Route::any('/estatisticas/show-internos-unidade', 'estatisticas\EstatisticasController@internosUnidade')->name('estatisticas.internosUnidade');
//Route::any('/estatisticas/show-regime-unidade', 'estatisticas\EstatisticasController@regimeUnidade')->name('estatisticas.regimeUnidade');
//Route::any('/estatisticas/show-crime-grupo', 'estatisticas\EstatisticasController@crimeGrupo')->name('estatisticas.crimeGrupo');
//Route::any('/estatisticas/show-estatistica-sexo', 'estatisticas\EstatisticasController@estatisticaSexo')->name('estatisticas.sexo');
//Route::any('/estatisticas/show-regime-prisional', 'estatisticas\EstatisticasController@regimePrisional')->name('estatisticas.regimePrisional');
//Route::any('/estatisticas/show-sentenciados-provisorios-sem-ms', 'estatisticas\EstatisticasController@sentProvSemMs')->name('estatisticas.sentProvSemMs');
//Route::any('/estatisticas/show-sentenciados-provisorios-em-ms', 'estatisticas\EstatisticasController@sentProvEmMs')->name('estatisticas.sentProvEmMs');
//Route::any('/estatisticas/show-sentenciados-provisorios-inclusive-ms', 'estatisticas\EstatisticasController@sentProvIncMs')->name('estatisticas.sentProvIncMs');
//Route::any('/estatisticas/show-sentenciados-medida-seguranca', 'estatisticas\EstatisticasController@medidaSeguranca')->name('estatisticas.medidaSeguranca');
//Route::any('/estatisticas/show-faixa-etaria', 'estatisticas\EstatisticasController@faixaEtaria')->name('estatisticas.faixaEtaria');
//Route::any('/estatisticas/show-nacionalidade', 'estatisticas\EstatisticasController@nacionalidade')->name('estatisticas.nacionalidade');
//Route::any('/estatisticas/show-estado-civil', 'estatisticas\EstatisticasController@estadoCivil')->name('estatisticas.estadoCivil');
//Route::any('/estatisticas/show-escolaridade', 'estatisticas\EstatisticasController@escolaridade')->name('estatisticas.escolaridade');
//Route::any('/estatisticas/show-cor-pele', 'estatisticas\EstatisticasController@corPele')->name('estatisticas.corPele');
//Route::any('/estatisticas/show-orientacao-sexual', 'estatisticas\EstatisticasController@orientacaoSexual')->name('estatisticas.orientacaoSexual');
//Route::any('/estatisticas/show-saida-unidade-prisional', 'estatisticas\EstatisticasController@saidaUnidadePrisional')->name('estatisticas.saidaUnidadePrisional');
//Route::any('/estatisticas/show-reingresso-sistema-prisional', 'estatisticas\EstatisticasController@reingressoSistemaPrisional')->name('estatisticas.reingressoSistemaPrisional');
//Route::any('/estatisticas/show-religiao', 'estatisticas\EstatisticasController@religiao')->name('estatisticas.religiao');
//Route::any('/estatisticas/show-faccionados-naofaccionados', 'estatisticas\EstatisticasController@faccionadosNaofaccionados')->name('estatisticas.faccionadosNaofaccionados');
//Route::any('/estatisticas/show-faccao-criminosa', 'estatisticas\EstatisticasController@faccaoCriminosa')->name('estatisticas.faccaoCriminosa');
//Route::any('/estatisticas/show-periculosidade', 'estatisticas\EstatisticasController@periculosidade')->name('estatisticas.periculosidade');
//Route::any('/estatisticas/show-lideranca', 'estatisticas\EstatisticasController@lideranca')->name('estatisticas.lideranca');

Route::any('/estatisticas/show-Entrada-saida-por-unidade', 'estatisticas\EstatisticasController@entradaSaidaPorUnidade')->name('estatisticas.entradaSaidaPorUnidade');
Route::any('/estatisticas/show-visita', 'estatisticas\EstatisticasController@showVisita')->name('estatisticas.showVisita');
Route::any('/estatisticas/show-advogado', 'estatisticas\EstatisticasController@showAdvogado')->name('estatisticas.showAdvogado');
Route::any('/estatisticas/show-servidor', 'estatisticas\EstatisticasController@showServidor')->name('estatisticas.showServidor');
Route::any('/estatisticas/show-dados-em-branco', 'estatisticas\EstatisticasController@showDadosEmBranco')->name('estatisticas.showDadosEmBranco');
Route::any('/estatisticas/lista-estatistica-internos', 'estatisticas\EstatisticasController@internoPdf')->name('estatistica.internoPdf');
Route::any('/estatisticas/lista-estatistica-internos-gerar', 'estatisticas\EstatisticasController@internoPdfGerar')->name('estatistica.internoPdfGerar');
//estatística ajax
Route::any('/estatisticsa/get-internos-visita', 'estatisticas\EstatisticasController@getQntVisitaAtiva')->name('estatisticas.getQntVisitaAtiva');

Route::any('/estatistica/entrada-saida/{data}', 'estatisticas\EstatisticasController@entradasSaidasDatas')->name('estatisticas.entradasSaidasDatas');

Route::any('/estatisticas/show-unidade', 'estatisticas\EstatisticasController@showUnidade')->name('estatisticas.showUnidade');
Route::any('/estatisticas/show-coeap', 'estatisticas\EstatisticasController@showCoeap')->name('estatisticas.showCoeap');
Route::any('/estatisticas/show-unidade-dia-pdf/{data}', 'estatisticas\EstatisticasController@showUnidadeDiaPdf')->name('estatisticas.showUnidadeDiaPdf');
Route::any('/estatisticas/show-unidade-mes-pdf/{mes}', 'estatisticas\EstatisticasController@showUnidadeMesPdf')->name('estatisticas.showUnidadeMesPdf');

############## NOVAS ESTATISTICAS ###########################
//alterada
Route::any('/estatisticas/internos-cor-pele', 'estatisticas\EstatisticasController@internoCorPele')->name('estatisticas.corPele');
Route::any('/estatisticas/internos-unidade', 'estatisticas\EstatisticasController@internosPorUnidade')->name('estatisticas.internosUnidade');
Route::any('/estatisticas/internos-regime-unidade', 'estatisticas\EstatisticasController@internosRegimeUnidade')->name('estatisticas.regimeUnidade');
Route::any('/estatisticas/internos-sexo', 'estatisticas\EstatisticasController@internoPorSexo')->name('estatisticas.sexo');
Route::any('/estatisticas/internos-crimes-grupo', 'estatisticas\EstatisticasController@internoCrimesGrupo')->name('estatisticas.crimeGrupo');
Route::any('/estatisticas/internos-regime-sexo', 'estatisticas\EstatisticasController@internoRegimeSexo')->name('estatisticas.regimePrisional');
Route::any('/estatisticas/internos-sentenciados-provisorios-s-med-seg', 'estatisticas\EstatisticasController@internosSentenciadosProvisoriosSMedSeg')->name('estatisticas.sentProvSemMs');
Route::any('/estatisticas/internos-sentenciados-provisorios-inc-med-seg', 'estatisticas\EstatisticasController@internosSentenciadosProvisoriosIncMedSeg')->name('estatisticas.sentProvIncMs');
Route::any('/estatisticas/internos-sentenciados-provisorios-em-med-seg', 'estatisticas\EstatisticasController@internosSentenciadosProvisoriosEmMedSeg')->name('estatisticas.sentProvEmMs');
Route::any('/estatisticas/internos-med-seg', 'estatisticas\EstatisticasController@internosMedSeg')->name('estatisticas.medidaSeguranca');
Route::any('/estatisticas/internos-faixa-etaria', 'estatisticas\EstatisticasController@internoFaixaEtaria')->name('estatisticas.faixaEtaria');
Route::any('/estatisticas/internos-nacionalidade', 'estatisticas\EstatisticasController@internoNacionalidade')->name('estatisticas.nacionalidade');
Route::any('/estatisticas/internos-estado-civil', 'estatisticas\EstatisticasController@internoEstadoCivil')->name('estatisticas.estadoCivil');
Route::any('/estatisticas/internos-escolaridade', 'estatisticas\EstatisticasController@internoEscolaridade')->name('estatisticas.escolaridade');
Route::any('/estatisticas/internos-orientacao-sexual', 'estatisticas\EstatisticasController@internoOrientacaoSexual')->name('estatisticas.orientacaoSexual');
Route::any('/estatisticas/internos-saida-unidade', 'estatisticas\EstatisticasController@internoSaidaUnidade')->name('estatisticas.saidaUnidadePrisional');
Route::any('/estatisticas/internos-reigresso-sistema-prisional', 'estatisticas\EstatisticasController@internoReingresso')->name('estatisticas.reingressoSistemaPrisional');
Route::any('/estatisticas/internos-religiao', 'estatisticas\EstatisticasController@internoReligiao')->name('estatisticas.religiao');
Route::any('/estatisticas/internos-faccionados', 'estatisticas\EstatisticasController@internoFaccionados')->name('estatisticas.faccionadosNaofaccionados');
Route::any('/estatisticas/internos-faccao', 'estatisticas\EstatisticasController@internoFaccao')->name('estatisticas.faccaoCriminosa');
Route::any('/estatisticas/internos-periculosidade', 'estatisticas\EstatisticasController@internoPericulosidade')->name('estatisticas.periculosidade');
Route::any('/estatisticas/internos-lideranca', 'estatisticas\EstatisticasController@internoLideranca')->name('estatisticas.lideranca');

// NOVAS ESTATISTICAS BASEADAS NO FORMULARIO DO SISDEPEN
Route::any('/estatisticas/internos-crimes-depen', 'estatisticas\EstatisticasController@internoCrimes')->name('estatisticas.internoCrimes');
Route::any('/estatisticas/internos-filhos-quantidade', 'estatisticas\EstatisticasController@internoFilhosQuantidade')->name('estatisticas.internoFilhosQuantidade');
Route::any('/estatisticas/internos-filhos-faixa-etaria-unidade-prisional', 'estatisticas\EstatisticasController@internoFilhosFaixaEtaria')->name('estatisticas.internoFilhosFaixaEtaria');
Route::any('/estatisticas/internos-transferencias', 'estatisticas\EstatisticasController@internoTransferencias')->name('estatisticas.internoTransferencias');
Route::any('/estatisticas/internos-deficiencia', 'estatisticas\EstatisticasController@internoDeficiencia')->name('estatisticas.internoDeficiencia');
Route::any('/estatisticas/internos-visitantes', 'estatisticas\EstatisticasController@internoVisitantes')->name('estatisticas.internoVisitantes');
Route::any('/estatisticas/internos-quantidade-visitas', 'estatisticas\EstatisticasController@internoQuantidadeVisitas')->name('estatisticas.internoQuantidadeVisitas');
Route::any('/estatisticas/internos-identidade-genero', 'estatisticas\EstatisticasController@internoIdentidadeGenero')->name('estatisticas.internoIdentidadeGenero');

//ESTATISTICAS DE MOVIMENTACOES
Route::any('/estatisticas/movimentacoes/{tipo}', 'estatisticas\EstatisticasController@movimentacoes')->name('estatisticas.movimentacoes');
Route::any('/estatisticas/movimentacoes-pdf', 'estatisticas\EstatisticasController@movimentacoesPdf')->name('estatisticas.movimentacoesPdfs');

// ESTATISTICAS DE FALTAS E SINDICANCIAS
Route::any('/estatisticas/faltas-gravidade', 'estatisticas\EstatisticasController@faltasGravidade')->name('estatisticas.faltasGravidade');
Route::any('/estatisticas/faltas-gravidade-pdf', 'estatisticas\EstatisticasController@faltasGravidadePdf')->name('estatisticas.faltasGravidadePdf');
Route::any('/estatisticas/faltas-status', 'estatisticas\EstatisticasController@faltasStatus')->name('estatisticas.faltasStatus');
Route::any('/estatisticas/faltas-status-pdf', 'estatisticas\EstatisticasController@faltasStatusPdf')->name('estatisticas.faltasStatusPdf');
Route::any('/estatisticas/sindicancias', 'estatisticas\EstatisticasController@sindicancias')->name('estatisticas.sindicancias');
Route::any('/estatisticas/sindicancias-pdf', 'estatisticas\EstatisticasController@sindicanciasPdf')->name('estatisticas.sindicanciasPdf');

// ESTATISTICAS DE ATENDIMENTOS
Route::any('/estatisticas/saude-por-setor', 'estatisticas\EstatisticasController@saudeSetor')->name('estatisticas.saudeSetor');
Route::any('/estatisticas/saude-por-setor-pdf', 'estatisticas\EstatisticasController@saudeSetorPdf')->name('estatisticas.saudeSetorPdf');
Route::any('/estatisticas/saude-por-unidade', 'estatisticas\EstatisticasController@saudeUnidade')->name('estatisticas.saudeUnidade');
Route::any('/estatisticas/saude-por-unidade-pdf', 'estatisticas\EstatisticasController@saudeUnidadePdf')->name('estatisticas.saudeUnidadePdf');
Route::any('/estatisticas/atendimento-advogado-por-unidade', 'estatisticas\EstatisticasController@atendimentoAdvogadosPorUnidade')->name('estatisticas.atendimentoAdvogadosPorUnidade');
Route::any('/estatisticas/atendimento-advogado-por-unidade-pdf', 'estatisticas\EstatisticasController@atendimentoAdvogadosPorUnidadePdf')->name('estatisticas.atendimentoAdvogadosPorUnidadePdf');

//ESTATISTICAS DE ESCOLTAS
Route::any('/estatisticas/escoltas-unidade', 'estatisticas\EstatisticasController@escoltaUnidade')->name('estatisticas.escoltaUnidade');
Route::any('/estatisticas/escoltas-unidade-pdf', 'estatisticas\EstatisticasController@escoltaUnidadePdf')->name('estatisticas.escoltaUnidadePdf');

//ESTATISTICAS DE OCORRENCIAS
Route::any('/estatisticas/ocorrencias-natureza', 'estatisticas\EstatisticasController@ocorrenciasNatureza')->name('estatisticas.ocorrenciasNatureza');
Route::any('/estatisticas/ocorrencias-natureza-pdf', 'estatisticas\EstatisticasController@ocorrenciasNaturezaPdf')->name('estatisticas.ocorrenciasNaturezaPdf');
Route::any('/estatisticas/ocorrencias-natureza-objeto', 'estatisticas\EstatisticasController@ocorrenciasNaturezaObjeto')->name('estatisticas.ocorrenciasNaturezaObjeto');
Route::any('/estatisticas/ocorrencias-natureza-objeto-pdf', 'estatisticas\EstatisticasController@ocorrenciasNaturezaObjetoPdf')->name('estatisticas.ocorrenciasNaturezaObjetoPdf');


// PDF
Route::any('/estatisticas/pdf/', 'estatisticas\EstatisticasController@internoPdfNew')->name('estatisticas.internoPdfNew');

/**
 * rotas que se relacionam com o objeto TI
 */
Route::get('/usuarios/busca', 'Setic\SeticController@usuariosBusca')->name('usuarios.busca');
Route::get('/usuarios/cadastro', 'Setic\SeticController@usuariosCadastro')->name('usuarios.cadastro');

Route::any('/setic/log', 'Setic\SeticController@log')->name('setic.log');
Route::any('/setic/get-log', 'Setic\SeticController@getLogAcesso')->name('setic.getLogAcesso');

// Route::any('/setic/recuperacao-contas', 'Setic\SeticController@recuperacaoContas')->name('setic.recuperacaoContas');
// Route::any('/setic/recuperacao-contas-arquivos/{id}', 'Setic\SeticController@recuperacaoContasArquivos')->name('setic.recuperacaoContasArquivos');
// Route::any('/setic/recuperacao-contas-enviar-senha/{id}', 'Setic\SeticController@recuperacaoContasEnviarSenha')->name('setic.recuperacaoContasEnviarSenha');

Route::any('/setic/helpdesk-listar', 'Setic\SeticController@listarChamadosHelpDesk')->name('setic.listarChamadosHelpDesk');

Route::any('/setic/usuarios-listar', 'Setic\SeticController@listarUsuarios')->name('setic.listarUsuarios');
Route::any('/setic/usuarios-cadastrar', 'Setic\SeticController@cadastrarUsuarios')->name('setic.cadastrarUsuarios');
Route::any('/setic/usuarios-cadastrar-store', 'Setic\SeticController@cadastrarUsuariosStore')->name('setic.cadastrarUsuariosStore');
Route::any('/setic/usuarios-editar/{id}', 'Setic\SeticController@editarUsuarios')->name('setic.editarUsuarios');
Route::any('/setic/usuarios-editar-store/{id}', 'Setic\SeticController@editarUsuariosStore')->name('setic.editarUsuariosStore');
Route::any('/setic/usuarios-enviar-nova-senha/{id}', 'Setic\SeticController@enviarNovaSenha')->name('setic.enviarNovaSenha');

Route::any('/setic/unidades-listar', 'Setic\SeticController@unidadesListar')->name('setic.unidadesListar');
Route::any('/setic/unidades-cadastrar', 'Setic\SeticController@unidadesCadastrar')->name('setic.unidadesCadastrar');
Route::any('/setic/unidades-cadastrar-store', 'Setic\SeticController@unidadesCadastrarStore')->name('setic.unidadesCadastrarStore');
Route::any('/setic/unidades-editar/{id}', 'Setic\SeticController@unidadesEditar')->name('setic.unidadesEditar');
Route::any('/setic/unidades-editar-store/{id}', 'Setic\SeticController@unidadesEditarStore')->name('setic.unidadesEditarStore');
Route::any('/setic/unidades-pavilhoes/{id}', 'Setic\SeticController@unidadesPavilhoes')->name('setic.unidadesPavilhoes');
Route::any('/setic/unidades-pavilhoes-cadastrar/{id}', 'Setic\SeticController@unidadesPavilhoesCadastrar')->name('setic.unidadesPavilhoesCadastrar');
Route::any('/setic/unidades-pavilhoes-cadastrar-store/{id}', 'Setic\SeticController@unidadesPavilhoesCadastrarStore')->name('setic.unidadesPavilhoesCadastrarStore');
Route::any('/setic/unidades-pavilhoes-editar/{id}', 'Setic\SeticController@unidadesPavilhoesEditar')->name('setic.unidadesPavilhoesEditar');
Route::any('/setic/unidades-pavilhoes-editar-store/{id}', 'Setic\SeticController@unidadesPavilhoesEditarStore')->name('setic.unidadesPavilhoesEditarStore');
Route::any('/setic/unidades-alas/{id}', 'Setic\SeticController@unidadesAlas')->name('setic.unidadesAlas');
Route::any('/setic/unidades-alas-cadastrar/{id}', 'Setic\SeticController@unidadesAlasCadastrar')->name('setic.unidadesAlasCadastrar');
Route::any('/setic/unidades-alas-cadastrar-store/{id}', 'Setic\SeticController@unidadesAlasCadastrarStore')->name('setic.unidadesAlasCadastrarStore');
Route::any('/setic/unidades-alas-editar/{id}', 'Setic\SeticController@unidadesAlasEditar')->name('setic.unidadesAlasEditar');
Route::any('/setic/unidades-alas-editar-store/{id}', 'Setic\SeticController@unidadesAlasEditarStore')->name('setic.unidadesAlasEditarStore');
Route::any('/setic/unidades-celas/{id}', 'Setic\SeticController@unidadesCelas')->name('setic.unidadesCelas');
Route::any('/setic/unidades-celas-cadastrar/{id}', 'Setic\SeticController@unidadesCelasCadastrar')->name('setic.unidadesCelasCadastrar');
Route::any('/setic/unidades-celas-cadastrar-store/{id}', 'Setic\SeticController@unidadesCelasCadastrarStore')->name('setic.unidadesCelasCadastrarStore');
Route::any('/setic/unidades-celas-editar/{id}', 'Setic\SeticController@unidadesCelasEditar')->name('setic.unidadesCelasEditar');
Route::any('/setic/unidades-celas-editar-store/{id}', 'Setic\SeticController@unidadesCelasEditarStore')->name('setic.unidadesCelasEditarStore');

Route::any('/setic/unificacao/cadastrar/join', 'Setic\SeticController@unificacaoInterno')->name('setic.unificacao.cadastrar.join');
Route::get('/setic/unificacao/buscar', 'Setic\SeticController@buscarUnificacao')->name('setic.unificacao.buscar.buscar');
Route::any('/setic/unificacao/cadastrar/join-store', 'Setic\SeticController@unificacaoInternoStore')->name('setic.unificacao.cadastrar.join.store');

Route::any('/setic/permissoes-cadastrar-perfil', 'Setic\SeticController@permissoesCadastrarPerfil')->name('setic.permissoesCadastrarPerfil');
Route::any('/setic/permissoes-cadastrar-perfil-store', 'Setic\SeticController@permissoesCadastrarPerfilStore')->name('setic.permissoesCadastrarPerfilStore');
Route::any('/setic/permissoes-gerenciar-eventos/{id}', 'Setic\SeticController@permissoesGerenciarEventos')->name('setic.permissoesGerenciarEventos');
Route::any('/setic/permissoes-inserir-evento/{id}', 'Setic\SeticController@permissoesInserirEvento')->name('setic.permissoesInserirEvento');
Route::any('/setic/permissoes-get-eventos-ajax', 'Setic\SeticController@getEventosAjax')->name('setic.getEventosAjax');
Route::any('/setic/permissoes-inserir-evento-store/{id}', 'Setic\SeticController@permissoesInserirEventoStore')->name('setic.permissoesInserirEventoStore');
Route::any('/setic/permissoes-gerenciar-metodos/{idController}/{idPerfil}', 'Setic\SeticController@permissoesGerenciarMetodos')->name('setic.permissoesGerenciarMetodos');
Route::any('/setic/permissoes-gerenciar-excluir-metodo/{idPerfil}/{idMetodo}', 'Setic\SeticController@permissoesExcluirMetodo')->name('setic.permissoesExcluirMetodo');
Route::any('/setic/permissoes-gerenciar-incluir-metodo/{idPerfil}/{idMetodo}', 'Setic\SeticController@permissoesIncluirMetodo')->name('setic.permissoesIncluirMetodo');
Route::any('/setic/permissoes-listar-perfis', 'Setic\SeticController@permissoesListarPerfis')->name('setic.permissoesListarPerfis');
Route::any('/setic/permissoes-excluir-perfil/{idPerfil}', 'Setic\SeticController@permissoesExcluirPerfil')->name('setic.permissoesExcluirPerfil');

Route::any('/setic/objeto-ocorrencia/listar', 'Setic\SeticController@listaObjetosOcorrencia')->name('setic.listaObjetosOcorrencia');
Route::any('/setic/objeto-ocorrencia/novo', 'Setic\SeticController@novoObjetosOcorrencia')->name('setic.novoObjetosOcorrencia');
Route::any('/setic/objeto-ocorrencia/store', 'Setic\SeticController@novoObjetosOcorrenciaStore')->name('setic.novoObjetosOcorrenciaStore');
Route::any('/setic/objeto-ocorrencia/editar/{id}', 'Setic\SeticController@editarObjetosOcorrencia')->name('setic.editarObjetosOcorrencia');
Route::any('/setic/objeto-ocorrencia/update/{id}', 'Setic\SeticController@updateObjetosOcorrencia')->name('setic.updateObjetosOcorrencia');


// Route::get('storage/app/arquivos/recuperacao_conta/{id}/{filename}', function ($id, $filename)
// {
//     $path = storage_path('app/arquivos/recuperacao_conta/' . $id . '/' . $filename);

//     if (!File::exists($path)) {
//         abort(404);
//     }

//     $file = File::get($path);
//     $type = File::mimeType($path);

//     $response = Response::make($file, 200);
//     $response->header("Content-Type", $type);

//     return $response;

// })->name('setic.arquivoRecuperacaoContaVisualizar')->middleware('auth');

/**
 * rotas que se relacionam com o objeto OAB
 */

Route::get('/oab/home', 'Oab\OabController@home')->name('oab.home');
Route::get('/oab/alterar-senha', 'Oab\OabController@alterarSenha')->name('oab.alterarSenha');
Route::post('/oab/alterar-senha-store', 'Oab\OabController@alterarSenhaStore')->name('oab.alterarSenhaStore');
Route::any('/oab/verifica-horario-ajax', 'Oab\OabController@horario')->name('oab.horario');
Route::any('/oab/verifica-unidade-interno-ajax', 'Oab\OabController@verificaUnidadeInternoAjax')->name('oab.verificaUnidadeInternoAjax');
Route::post('/oab/store', 'Oab\OabController@store')->name('oab.store');
Route::get('/oab/cancelar-agendamento', 'Oab\OabController@cancelarAgendamento')->name('oab.cancelarAgendamento');
Route::get('/oab/cancelar-agendamento-update/{id}', 'Oab\OabController@cancelarAgendamentoUpdate')->name('oab.cancelarAgendamentoUpdate');
Route::get('/oab/historico-agendamento', 'Oab\OabController@historicoAgendamento')->name('oab.historicoAgendamento');

/**
 * rotas que se relacionam com o objeto Judiciario
 */
Route::any('/judiciario/agendamentos', 'Judiciario\JudiciarioController@agendamentos')->name('judiciario.agendamentos');
Route::get('/judiciario/novo-agendamento', 'Judiciario\JudiciarioController@novoAgendamento')->name('judiciario.novoAgendamento');
Route::post('/judiciario/novo-agendamento-store', 'Judiciario\JudiciarioController@novoAgendamentoStore')->name('judiciario.novoAgendamentoStore');
Route::get('/judiciario/cancelar-agendamento/{id}', 'Judiciario\JudiciarioController@cancelarAgendamento')->name('judiciario.cancelarAgendamento');
Route::get('/judiciario/show/{id}', 'Judiciario\JudiciarioController@show')->name('judiciario.show');



/**
 * rotas que se relacionam com o objeto Municipio
 */
// rota para pesquisa dos municipios
Route::post('municipios/pesquisa-ajax', 'Municipios\MunicipiosController@getMunicipio')->name('municipio_pesquisa_ajax');

/**
 * -------------- rota de ajuste no banco ------------------------------
 */

Route::get('/interno/bd', 'Interno\InternoController@bd')->name('interno.bd');

/**
 * rotas que se relacionam com o objeto Foto
 */
Route::post('foto/gravar-ajax', 'Foto\FotoController@gravarFoto')->name('foto.gravar');
Route::post('foto/excluir-ajax', 'Foto\FotoController@excluirFoto')->name('foto.excluir');

/**
 * rotas que se relacionam com o objeto Solicitacao Interno
 */
Route::any('solicitacao/interno/solicitar', 'Solicitacao\SolicitacaoInternoController@solicitar')->name('solicitacaoInterno.solicitar');
Route::any('solicitacao/interno/solicitar-store', 'Solicitacao\SolicitacaoInternoController@solicitarStore')->name('solicitacaoInterno.solicitarStore');
Route::any('solicitacao/interno/historico', 'Solicitacao\SolicitacaoInternoController@historico')->name('solicitacaoInterno.historico');
Route::any('solicitacao/interno/cancelar-solicitacao-store', 'Solicitacao\SolicitacaoInternoController@cancelarSolicitacaoStore')->name('solicitacaoInterno.cancelarSolicitacaoStore');
Route::any('solicitacao/interno/pedidos', 'Solicitacao\SolicitacaoInternoController@pedidos')->name('solicitacaoInterno.pedidos');
Route::any('solicitacao/interno/confirmar-solicitacao-store/{chaveAutorizacao}', 'Solicitacao\SolicitacaoInternoController@confirmarSolicitacaoStore')->name('solicitacaoInterno.confirmarSolicitacaoStore');
Route::any('solicitacao/interno/cancelar-solicitacao/{chaveCancelamento}', 'Solicitacao\SolicitacaoInternoController@CancelarSolicitacao')->name('solicitacaoInterno.CancelarSolicitacao');
Route::any('solicitacao/interno/cancelar-solicitacao-store/{chaveCancelamento}', 'Solicitacao\SolicitacaoInternoController@CancelarSolicitacaoUnidadeSolicitadaStore')->name('solicitacaoInterno.CancelarSolicitacaoUnidadeSolicitadaStore');


/**
 * rotas que se relacionam com os dados do rh
 */
Route::post('facade/busca-servidor-ajax', 'Facade\FacadeController@getDadosServidorPorNomeKeyUp')->name('buscaServidor.keyUp');
Route::post('facade/get-foto-servidor', 'Facade\FacadeController@getFotoServidorAjax')->name('buscaServidor.getFotoServidorAjax');
Route::any('facade/verifica-agepen', 'Facade\FacadeController@verificaAgepen')->name('facade.verificaAgepen');
Route::any('facade/get-todos-servidores', 'Facade\FacadeController@getTodosServidoresUnidade')->name('facade.getTodosServidoresUnidade');

/*
 * Rotas que se relacionam com documentos
 */
Route::any('/documento-view/{id?}', 'Documentos\DocumentosController@documentosView')->name('documentosView');


/*
 * Rotas que se relacionam com saude e assistencia
 */
Route::any('/saude-e-assistencia/registrar-atendimento', 'SaudeAssistencia\SaudeAssistenciaController@cadastroAtendimento')->name('saudeAssistencia.cadastroAtendimento');
Route::any('/saude-e-assistencia/registrar-atendimento-store', 'SaudeAssistencia\SaudeAssistenciaController@cadastroAtendimentoStore')->name('saudeAssistencia.cadastroAtendimentoStore');
Route::any('/saude-e-assistencia/historico-atendimento', 'SaudeAssistencia\SaudeAssistenciaController@historicoAtendimento')->name('saudeAssistencia.historicoAtendimento');
Route::any('/saude-e-assistencia/show-atendimento/{id}', 'SaudeAssistencia\SaudeAssistenciaController@showAtendimento')->name('saudeAssistencia.showAtendimento');
Route::any('/saude-e-assistencia/editar-atendimento/{id}', 'SaudeAssistencia\SaudeAssistenciaController@editarAtendimento')->name('saudeAssistencia.editarAtendimento');
Route::any('/saude-e-assistencia/editar-atendimento-store/{id}', 'SaudeAssistencia\SaudeAssistenciaController@editarAtendimentoStore')->name('saudeAssistencia.editarAtendimentoStore');
Route::any('/saude-e-assistencia/excluir-atendimento/{id}', 'SaudeAssistencia\SaudeAssistenciaController@excluirAtendimento')->name('saudeAssistencia.excluirAtendimento');
Route::any('/saude-e-assistencia/internos-para-atendimento', 'SaudeAssistencia\SaudeAssistenciaController@internosAtendimento')->name('saudeAssistencia.internosAtendimento');
Route::any('/saude-e-assistencia/internos-para-atendimento-store', 'SaudeAssistencia\SaudeAssistenciaController@internosAtendimentoStore')->name('saudeAssistencia.internosAtendimentoStore');
Route::any('/saude-e-assistencia/lista-internos-para-atendimento', 'SaudeAssistencia\SaudeAssistenciaController@listaInternosAtendimento')->name('saudeAssistencia.listaInternosAtendimento');
Route::any('/saude-e-assistencia/atendimento-realizado', 'SaudeAssistencia\SaudeAssistenciaController@atendimentoRealizado')->name('saudeAssistencia.atendimentoRealizado');

//Route::any('/saude-e-assistencia/informar-doenca', 'SaudeAssistencia\SaudeAssistenciaController@informarDoenca')->name('saudeAssistencia.informarDoenca');


/*
 * Rotas que se relacionam notificacoes
 */
Route::any('/notificacoes/', 'Notificacao\NotificacaoController@notificacoes')->name('notificacao.notificacoes');


/*
 * Rotas que se relacionam livro de ocorrencia
 */
Route::any('/ocorrencia/fechamento-livro/', 'Ocorrencia\OcorrenciaController@livroFechamento')->name('ocorrencia.livroFechamento');
Route::any('/ocorrencia/get-fechamento-livro-ajax/', 'Ocorrencia\OcorrenciaController@livroFechamentoGetAjax')->name('ocorrencia.livroFechamentoGetAjax');
Route::any('/ocorrencia/fechamento-livro-store/', 'Ocorrencia\OcorrenciaController@livroFechamentoStore')->name('ocorrencia.livroFechamentoStore');


/*
 * ******************************************************************
 * rotas Artigos e Processos dos Internos
 * ******************************************************************
 */
Route::any('/interno/buscar-artigos-processos', 'ArtigosProcessos\ArtigosProcessosController@buscar')->name('artigosProcessos.buscar');

Route::any('/interno/cadastro-artigos-processos', 'ArtigosProcessos\ArtigosProcessosController@cadastro')->name('artigosProcessos.cadastro');
Route::any('/interno/cadastro-artigos-processos-store', 'ArtigosProcessos\ArtigosProcessosController@cadastroStore')->name('artigosProcessos.cadastroStore');
Route::any('/interno/editar-artigos-processos/{id}', 'ArtigosProcessos\ArtigosProcessosController@editar')->name('artigosProcessos.editar');
Route::any('/interno/editar-artigos-processos-update/{id}', 'ArtigosProcessos\ArtigosProcessosController@update')->name('artigosProcessos.update');
Route::any('/interno/excluir-artigos-processos/{id}', 'ArtigosProcessos\ArtigosProcessosController@excluir')->name('artigosProcessos.excluir');

Route::any('/lei/get-dados-artigos-ajax', 'ArtigosProcessos\ArtigosProcessosController@getDadosArtigosAjax')->name('artigosProcessos.getDadosArtigosAjax');
Route::any('/lei/artigos-ajax', 'ArtigosProcessos\ArtigosProcessosController@getArtigosLei')->name('artigosProcessos.getArtigosLei');
Route::any('/lei/dispositivo-sequencia-ajax', 'ArtigosProcessos\ArtigosProcessosController@getDispositivoSequencia')->name('artigosProcessos.getDispositivoSequencia');

Route::any('/lei/dispositivo-sequencia-dois-ajax', 'ArtigosProcessos\ArtigosProcessosController@getDispositivoSequenciaDois')->name('artigosProcessos.getDispositivoSequenciaDois');



/*
 * ******************************************************************
 * rota para migracao das fotos
 * ******************************************************************
 */
//Route::any('/foto/migracao-fotos-internos/', 'Foto\FotoController@migracaoFotoInterno')->name('foto.migracaoFotoInterno');
// Route::any('/foto/migracao-fotos-internos-ajax/', 'Foto\FotoController@migracaoFotoInternoAjax')->name('foto.migracaoFotoInternoAjax');

//Route::any('/foto/migracao-fotos-visitas/', 'Foto\FotoController@migracaoFotoVisita')->name('foto.migracaoFotoVisita');
// Route::any('/foto/migracao-fotos-vistas-ajax', 'Foto\FotoController@migracaoFotoVisitaAjax')->name('foto.migracaoFotoVisitaAjax');

//Route::any('/foto/migracao-fotos-adv/', 'Foto\FotoController@migracaoFotoAdv')->name('foto.migracaoFotoAdv');
// Route::any('/foto/migracao-fotos-adv-ajax', 'Foto\FotoController@migracaoFotoAdvAjax')->name('foto.migracaoFotoAdvAjax');


// Route::any('/arquivos/migracao-arquivos-visitas/', 'Foto\FotoController@migracaoArquivoVis')->name('foto.migracaoArquivoVis');


/*
 * ******************************************************************
 * rotas dos usuarios
 * ******************************************************************
 */

// Route::any('/usuarios/recuperar-conta', 'Usuario\UsuarioController@recuperarConta')->name('usuario.recuperarConta');
// Route::any('/usuarios/recuperar-conta-store', 'Usuario\UsuarioController@recuperarContaStore')->name('usuario.recuperarContaStore');

Route::any('/usuarios/escqueci-minha-senha', 'Usuario\UsuarioController@esqueciMinhaSenha')->name('usuario.esqueciMinhaSenha');
Route::any('/usuarios/escqueci-minha-senha-store', 'Usuario\UsuarioController@esqueciMinhaSenhaStore')->name('usuario.esqueciMinhaSenhaStore');

Route::any('/usuarios/conta-inativada/{tipo?}', 'Usuario\UsuarioController@contaInativada')->name('usuario.contaInativada');

Route::any('/usuarios/alterar-minha-senha', 'Usuario\UsuarioController@alterarMinhaSenha')->name('usuario.alterarMinhaSenha')->middleware('auth');
Route::any('/usuarios/alterar-minha-senha-store', 'Usuario\UsuarioController@alterarMinhaSenhaStore')->name('usuario.alterarMinhaSenhaStore')->middleware('auth');

/*
 * ******************************************************************
 * rotas da lotacao
 * ******************************************************************
 */

Route::any('/unidade/alterar-lotacao', 'Unidades\UnidadesController@alterarLotacaoSiapen')->name('unidadde.alterarLotacaoSiapen');
Route::any('/unidade/alterar-lotacao-store', 'Unidades\UnidadesController@alterarLotacaoSiapenStore')->name('unidadde.alterarLotacaoSiapenStore');
Route::any('/unidade/unidade-gerenciar', 'Unidades\UnidadesController@unidadeGerenciar')->name('unidade.unidadeGerenciar');
Route::any('/unidade/pavilhao-gerenciar/{id}', 'Unidades\UnidadesController@pavilhaoGerenciar')->name('unidade.pavilhaoGerenciar');
Route::any('/unidade/pavilhao-editar/{id}', 'Unidades\UnidadesController@pavilhaoEditar')->name('unidade.pavilhaoEditar');
Route::any('/unidade/pavilhaoEditarStore/{id}', 'Unidades\UnidadesController@pavilhaoEditarStore')->name('unidade.pavilhaoEditarStore');
Route::any('/unidade/ala-gerenciar/{id}', 'Unidades\UnidadesController@alaGerenciar')->name('unidade.alaGerenciar');
Route::any('/unidade/cela-gerenciar/{id}', 'Unidades\UnidadesController@celaGerenciar')->name('unidade.celaGerenciar');
Route::any('/unidade/cela-editar/{id}', 'Unidades\UnidadesController@celaEditar')->name('unidade.celaEditar');
Route::any('/unidade/cela-editar-store/{id}', 'Unidades\UnidadesController@celaEditarStore')->name('unidade.celaEditarStore');

/*
 * ******************************************************************
 * rotas helpDesk
 * ******************************************************************
 */
Route::any('/helpdesk/chamados', 'helpdesk\HelpDeskController@chamadosListar')->name('helpDesk.chamadosListar');
Route::any('/helpdesk/abrir-chamado', 'helpdesk\HelpDeskController@abrirChamados')->name('helpDesk.abrirChamados');
Route::any('/helpdesk/abrir-chamado-store', 'helpdesk\HelpDeskController@abrirChamadosStore')->name('helpDesk.abrirChamadosStore');
Route::any('/helpdesk/abrir-chamado-visualizar/{id}', 'helpdesk\HelpDeskController@chamadosVisualizar')->name('helpDesk.chamadosVisualizar');
Route::any('/helpdesk/responder-chamado/{id}', 'helpdesk\HelpDeskController@responderChamado')->name('helpDesk.responderChamado');
Route::any('/helpdesk/fechar-chamado/{id}', 'helpdesk\HelpDeskController@fecharChamado')->name('helpDesk.fecharChamado');

Route::get('storage/app/arquivos/helpdesk/{id}/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/helpdesk/' . $id . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('helpDesk.arquivoChamado')->middleware('auth');

/*
 * ******************************************************************
 * rotas Servidores
 * ******************************************************************
 */
Route::any('/servidor/lista-servidores', 'Servidor\ServidorController@listarServidores')->name('servidor.listarServidores');
Route::any('/servidor/alterar-permissao/{cpf}', 'Servidor\ServidorController@alterarPermissao')->name('servidor.alterarPermissao');
Route::any('/servidor/alterar-permissao-store/{cpf}', 'Servidor\ServidorController@alterarPermissaoStore')->name('servidor.alterarPermissaoStore');

/*
 * ******************************************************************
 * rotas Concurso Remocao
 * ******************************************************************
 */
//Route::any('/concurso-remocao/', 'ConcursoRemocao\ConcursoRemocaoController@escolhaLotacao')->name('concursoRemocao.escolhaLotacao');
Route::any('/concurso-remocao/lista-escolhas', 'ConcursoRemocao\ConcursoRemocaoController@listaEscolhas')->name('concursoRemocao.listaEscolhas');
Route::any('/concurso-remocao/lista-escolhas-pdf/{lotacao}', 'ConcursoRemocao\ConcursoRemocaoController@listaEscolhasPdf')->name('concursoRemocao.listaEscolhasPdf');
Route::any('/concurso-remocao/store-escolha', 'ConcursoRemocao\ConcursoRemocaoController@escolhaLotacaoStore')->name('concursoRemocao.escolhaLotacaoStore');
Route::any('/concurso-remocao/mapa-vagas-servidor', 'ConcursoRemocao\ConcursoRemocaoController@mapaVagasServidor')->name('concursoRemocao.mapaVagasServidor');
Route::any('/concurso-remocao/definir-lotacao/{id}', 'ConcursoRemocao\ConcursoRemocaoController@definirLotacaoServidor')->name('concursoRemocao.definirLotacaoServidor');
Route::any('/concurso-remocao/definir-lotacao-store', 'ConcursoRemocao\ConcursoRemocaoController@definirLotacaoServidorStore')->name('concursoRemocao.definirLotacaoServidorStore');
Route::any('/concurso-remocao/lista-remocoes', 'ConcursoRemocao\ConcursoRemocaoController@listaRemocoes')->name('concursoRemocao.listaRemocoes');
Route::any('/concurso-remocao/lista-remocoes-pdf/{tipo}', 'ConcursoRemocao\ConcursoRemocaoController@listaRemocoesPdf')->name('concursoRemocao.listaRemocoesPdf');


/*
 * ******************************************************************
 * rotas Cursos EAD
 * ******************************************************************
 */
Route::any('/ead/cursos', 'Ead\EadController@listaCursos')->name('ead.listaCursos');
Route::any('/ead/aulas/{idCurso}/{idAula}', 'Ead\EadController@aulas')->name('ead.aulas');
Route::any('/ead/inscricao-curso/{idCurso}', 'Ead\EadController@inscricaoConcurso')->name('ead.inscricaoConcurso');
Route::any('/ead/meus-cursos/', 'Ead\EadController@meusCursos')->name('ead.meusCursos');
Route::any('/ead/save-progress/', 'Ead\EadController@saveProgressAjax')->name('ead.saveProgress');

Route::get('storage/app/arquivos/cursos/{id}/imagens/{filename}', function ($id, $filename)
{
    $path = storage_path('app/arquivos/cursos/' . $id . '/imagens/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('cursos.imagens')->middleware('auth');

Route::get('storage/app/arquivos/cursos/{id}/aulas/{idAula}/{filename}', function ($id, $idAula, $filename)
{
    $path = storage_path('app/arquivos/cursos/' . $id . '/aulas/' . $idAula.'/'.$filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;

})->name('cursos.aulasFile')->middleware('auth');



/*
 * ******************************************************************
 * rotas listagem
 * ******************************************************************
 */
Route::any('/listagem/folha-frequencia-aberto', 'Listagem\ListagemController@frequenciaAberto')->name('listagem.frequenciaAberto');
Route::any('/listagem/folha-frequencia-aberto-store', 'Listagem\ListagemController@frequenciaAbertoStore')->name('listagem.frequenciaAbertoStore');

Route::any('/listagem/folha-frequencia-semiaberto', 'Listagem\ListagemController@frequenciaSemiaberto')->name('listagem.frequenciaSemiaberto');
Route::any('/listagem/folha-frequencia-semiaberto-store', 'Listagem\ListagemController@frequenciaSemiabertoStore')->name('listagem.frequenciaSemiabertoStore');

Route::any('/listagem/folha-frequencia-servidores', 'Listagem\ListagemController@frequenciaServidores')->name('listagem.frequenciaServidores');
Route::any('/listagem/folha-frequencia-servidores-store', 'Listagem\ListagemController@frequenciaServidoresStore')->name('listagem.frequenciaServidoresStore');

Route::any('/listagem/frequencia-diaria-interno-criar-lista', 'Listagem\ListagemController@frequenciaDiariaInternoCriarLista')->name('listagem.frequenciaDiariaInternoCriarLista');
Route::any('/listagem/frequencia-diaria-interno-criar-lista-store', 'Listagem\ListagemController@frequenciaDiariaInternoCriarListaStore')->name('listagem.frequenciaDiariaInternoCriarListaStore');
Route::any('/listagem/frequencia-diaria-gerenciar-listas', 'Listagem\ListagemController@frequenciaDiariaInternoGerenciarListas')->name('listagem.frequenciaDiariaInternoGerenciarListas');
Route::any('/listagem/frequencia-diaria-regitrar-presenca', 'Listagem\ListagemController@frequenciaDiariaInternoRegistrarPresenca')->name('listagem.frequenciaDiariaInternoRegistrarPresenca');
Route::any('/listagem/frequencia-diaria-regitrar-presenca-store', 'Listagem\ListagemController@frequenciaDiariaInternoRegistrarPresencaStore')->name('listagem.frequenciaDiariaInternoRegistrarPresencaStore');

Route::any('/listagem/frequencia-diaria-editar/{id}', 'Listagem\ListagemController@editarLista')->name('listagem.editarLista');
Route::any('/listagem/frequencia-diaria-editar-store', 'Listagem\ListagemController@editarListaStore')->name('listagem.editarListaStore');


Route::any('/listagem/relatorio-frequencia-diaria', 'Listagem\ListagemController@relatorioFrequenciaDiaria')->name('listagem.relatorioFrequenciaDiaria');
Route::any('/listagem/relatorio-frequencia-diaria-store', 'Listagem\ListagemController@relatorioFrequenciaDiariaStore')->name('listagem.relatorioFrequenciaDiariaStore');


# ********************           AJAX            ******************** #
Route::any('/listagem/get-internos-lista', 'Listagem\ListagemController@getInternosLista')->name('listagem.getInternosLista');




/*
 * ******************************************************************
 * rotas Uteis
 * ******************************************************************
 */
Route::any('/interno/interno-fotoprincipal-ajax', 'Interno\InternoController@getFotoPrincipalInternoAjax')->name('foto.getFotoPrincipalInternoAjax');
Route::any('/visita/visita-fotoprincipal-ajax', 'Visita\VisitaController@getFotoPrincipalVisitaAjax')->name('foto.getFotoPrincipalVisitaAjax');
Route::any('/git/g1tpull', 'Git\GitController@gitPull')->name('git.gitPull');