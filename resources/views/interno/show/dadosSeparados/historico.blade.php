@extends('template.tema_geral.theme')
<input type="hidden" name="idInterno" id="idInterno" value="{{ $id }}">
@section('content')
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div>
    @php
    $acoesLiberadas = \Illuminate\Support\Facades\Auth::user()->acoesLiberadas;
    $teclaf12 = "teclaf12";
    $btRightMouse = "rightButtonMouse";
    $cpfShowInterno = "cpfShowInterno";
    $lF12 = strpos($acoesLiberadas, $teclaf12);
    $lRightMouse = strpos($acoesLiberadas, $btRightMouse);
    $lCpfShowInterno = strpos($acoesLiberadas, $cpfShowInterno);
    @endphp


    <style type="text/css">
        @if ($lCpfShowInterno === false)

        .bgNoPrint{
            background-image: url('{{asset("storage/app/arquivos/usuarios/cpfimg/".\Illuminate\Support\Facades\Auth::user()->cpf.".png")}}');
            background-repeat: repeat;

        }
        @else
        .bgNoPrint{
            background-color: #FFF;
        }

        @endif

    </style>



    @if ($lF12 === false)
    <script>
        window.addEventListener('keydown', function (e) {
            var code = e.which || e.keyCode;
            if (code == 123) e.preventDefault();
            else return;

            console.log("invalid");
        });
        window.addEventListener('keydown', function (e) {
            var code = e.which || e.keyCode;
            if (code == 73) e.preventDefault();
            else return;

            console.log("invalid");
        });
    </script>
    @endif
    @if ($lRightMouse === false)
        <script>

            document.oncontextmenu = document.body.oncontextmenu = function() {return false;}

        </script>
    @endif

    <h3 style="display: none" class="text-center mostrarNaImpressao">
            <img src="data:image/png;base64, {{ $fotoPerfilDados }}" style="max-height: 20em;" class="mx-auto d-block imagemSrc cursorPointer text-center figure-img img-fluid" title="Clique para ampliar a foto"/>
        @if($interno['nome_social'] == "Não Informado")
                {{ $interno['nome'] }} - Prontuário: {{ $interno['id'] }}
        @else
                {{ $interno['nome_social'] }} - Prontuário: {{ $interno['id'] }}
                <br/>
                <i style="font-size: 16px"> {{ $interno['nome'] }}</i>
        @endif

        <hr/>
    </h3>
    <div class="row">
        <div class="col-md-2 p-2 rounded m-1 esconderNaImpressao bgNoPrint">
            @if( $interno['status'] == 'A')<h5 class="text-center text-green">{{ $interno['condicao_nome'] }}</h5>
            @elseif( $interno['status'] == 'I')<h5 class="text-center text-red">{{ $interno['condicao_nome'] }}</h5>@endif
            <img src="data:image/png;base64, {{ $fotoPerfilDados }}" style="max-height: 20em;" class="mx-auto d-block imagemSrc cursorPointer text-center figure-img img-fluid" title="Clique para ampliar a foto" />
            <h5 class="mt-4 text-center">
                @if($interno['nome_social'] == "Não Informado")
                    {{ strtoupper($interno)['nome'] }}
                @else
                    {{ strtoupper($interno['nome_social']) }}
                    <br/>
                    <i style="font-size: 16px">{{ strtoupper($interno['nome']) }}</i>
                @endif
            </h5>
            @if($interno['periculosidade'] == 'Alta') <h4 class="mt-2 text-center"><strong class="color_red">{{ $interno['periculosidade'] }} Periculosidade</strong> </h4>@endif
            @if($interno['periculosidade'] == 'Média') <h4 class="mt-2 text-center"><strong class="color_orange">{{ $interno['periculosidade'] }} Periculosidade</strong> </h4>@endif
                <p class="text-left">
                Prontuário: <strong>{{ $interno['id'] }}</strong>,<br/>
                @if( $interno['status'] == 'A' ) Unidade Prisional: <strong>{{ $interno['unidade_prisional_nome'] }}</strong>,<br/>
                @elseif( $interno['status'] == 'I' ) Última Unidade Prisional: <strong>{{ $interno['unidade_prisional_nome'] }}</strong>,<br/>@endif
                @if( $interno['status'] == 'A' )
                    @if(PermissaoService::getTipoPermissao() == "I")
                        Pavilhão: <strong>{{ $interno['pavilhao_nome'] }}</strong>,<br/>
                        Ala: <strong>{{ $interno['ala_nome'] }}</strong>,<br/>
                        Cela: <strong>{{ $interno['cela_nome'] }}</strong>,<br/>
                    @endif
                    Medida de Segurança: <strong>{{ $interno['medida_seguranca']=="S"?"Sim":"Não" }}</strong>,<br/>
                    Sentenciado: <strong>{{ $interno['sentenciado']=='N'?"Não":($interno['sentenciado']=='S'?"Sim":"Não Informado") }}</strong>,<br/>
                    @if($interno['sentenciado'] == 'S') Regime: <strong>{{ $interno['regime'] }}</strong>,<br/>@endif
                    @if($interno['regime'] == "SEMIABERTO")Autorização de Saída no Regime Semiaberto: <strong>{{ $interno['saidasexternas'] }}</strong>,<br/>@endif
                @elseif( $interno['status'] == 'I' )
                    @if(PermissaoService::getTipoPermissao() == "I")
                        Último Pavilhão: <strong>{{ $interno['pavilhao_nome'] }}</strong>,<br/>
                        Última Ala: <strong>{{ $interno['ala_nome'] }}</strong>,<br/>
                        Última Cela: <strong>{{ $interno['cela_nome'] }}</strong>,<br/>
                    @endif
                    Medida de Segurança: <strong>{{ $interno['medida_seguranca']=="S"?"Sim":"Não" }}</strong>,<br/>
                    Sentenciado: <strong>{{ $interno['sentenciado']=='N'?"Não":($interno['sentenciado']=='S'?"Sim":"Não Informado") }}</strong>,<br/>
                    @if($interno['sentenciado'] == 'S')Último Regime: <strong>{{ $interno['regime'] }}</strong>,<br/>@endif
                    @if($interno['regime'] == "SEMIABERTO")Tinha Autorização de Saída no Regime Semiaberto: <strong>{{ $interno['saidasexternas'] }}</strong>,<br/>@endif
                @endif
                Facção: <strong>{{ $interno['faccao_nome'] }}</strong>,<br/>
                Liderança: <strong>{{ $interno['lideranca'] }}</strong>,<br/>
                @if($interno['periculosidade'] == 'Baixa' || $interno['periculosidade'] == 'Não Informado') Periculosidade: <strong>{{ $interno['periculosidade'] }}</strong> @endif
                {{--Biometria Registrada na {{ config('app.secretaria') }} {{ config('app.uf') }}: <strong>{{ $interno['biometria_sejuc'] }}</strong>,<br/>--}}
                {{--Identidade Criminal Biométricamente Confirmada na SESED-RN: <strong>{{ $interno['identificacao_criminal_sesed'] }}</strong>--}}
                @if(\App\Http\Controllers\Setic\SeticController::existeUnificacao_id('interno',$interno['id']))<br/><br/><i class="fas fa-user-cog"></i> Este perfil passou pelo processo de unificação e pode haver eventuais inconsistências.@endif
            </p>
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical" style="background-color: white">
            
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.show', $id)}}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-id-card"></i> Dados Pessoais</a>

                @if(PermissaoService::getVerifyPermissionView("InternoController","showFotos"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showFotos', $id)}}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-camera"></i> Fotos</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","companheirosCela"))
                <a class="nav-link" id="companheirocela"  href="{{ route('interno.companheirosCela',$interno['id']) }}" ><i class="mdi mdi-sitemap"></i> Companheiros de Cela</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","showHistorico"))
                <a class="nav-link active" id="" data-toggle="" href="{{ route('interno.showHistorico',$id) }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-history"></i> Histórico</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","showProcessos"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showProcessos', $id)}}" role="tab" aria-controls="" aria-selected="false"><i class="mdi mdi-clipboard-text"></i> Processos e Crimes</a>
                @endif                

                @if(PermissaoService::getVerifyPermissionView("InternoController","showVisitantes"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showVisitantes',$id) }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-users"></i> Visitantes</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","showAdvogados"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showAdvogados',$id) }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-user-tie"></i> Advogados</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","showDisciplina"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showDisciplina',$id) }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-gavel"></i> Disciplina</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","showOcorrencia"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showOcorrencia',$id) }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-exclamation-triangle"></i> Ocorrências</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","showEscoltas"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showEscoltas',$id) }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-taxi"></i> Escoltas</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","showSaudeAssistencia"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showSaudeAssistencia',$id) }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-briefcase-medical"></i> Saúde e Assistência</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","showDocumentos"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showDocumentos',$id) }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-folder-open"></i> Documentos</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","showImprimir"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showImprimir',$id) }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-print"></i> Imprimir</a>
                @endif
            
            </div>
        </div>
        <div class="col p-2 m-1 w-100 rounded bgNoPrint" style="background: white">
            <div class="tab-content" id="v-pills-tabContent">
            
            <!-- dados da rota -->
            
            <!-- INICIO DO CONTAINER DA ROTA -->

            <div class="container">
                <div class="titlePage color_365f80 mb-5">
                    <h3><i class="fas fa-history"></i> Histórico do interno</h3>
                    <hr>
                    <p>
                        Histórico de movimentações do interno no {{ config('app.sistema_estado') }}.
                    </p>
                </div>
                <div class="card-body pl-1 pr-1 row">
                    <div class="col-md-2 esconderNaImpressao">
                        <h4 class="card-title">Filtros</h4>
                        <p class="card-description">Deixe marcado apenas os filtros a exibir:</p>
                        <form action="#" class="forms-sample">
                            <div class="form-group">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="checkFiltro" value="transferencia" class="form-check-input"> Transferência
                                        <i class="input-helper"></i></label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="checkFiltro" value="mudancacela" class="form-check-input"> Mudança de Cela
                                        <i class="input-helper"></i></label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="checkFiltro" value="entradaunidadeprisional" class="form-check-input"> Entrada na Unidade Prisional
                                        <i class="input-helper"></i></label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="checkFiltro" value="saidajusicial" class="form-check-input"> Saída Judicial
                                        <i class="input-helper"></i></label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="checkFiltro" value="progressaoaberto" class="form-check-input"> Progressão Definitiva Aberto
                                        <i class="input-helper"></i></label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="checkFiltro" value="progressaosemiaberto" class="form-check-input"> Progressão Definitiva Semiaberto
                                        <i class="input-helper"></i></label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="checkFiltro" value="regressaosemiaberto" class="form-check-input"> Regressão Definitiva Semiaberto
                                        <i class="input-helper"></i></label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="checkFiltro" value="regressaofechado" class="form-check-input"> Regressão Definitiva Fechado
                                        <i class="input-helper"></i></label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="checkFiltro" value="alvara" class="form-check-input"> Alvará de Soltura
                                        <i class="input-helper"></i></label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="checkFiltro" value="mudancasentenca" class="form-check-input"> Mudança para Sentenciado ou Provisório
                                        <i class="input-helper"></i></label>
                                </div>
                                <div class="mb-2">
                                    <button type="button" id="btnFiltro" class="btn btn-dark m-t-20 buscaAjax">Filtrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col allItens">
                        @if($historicos != null)
                        @foreach($historicos as $historico)
                        @if($historico['tipo'] == 'transferencia')
                        <div class="{{ $historico['tipo'] }} itens">
                            <!-- transferencia -->
                            <div class="row align-items-center how-it-works d-flex ">
                                <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                                    <div style="z-index: 2; background:white !important" class="font-weight-bold">
                                        <div class="mt-0 right-col text-right">
                                            <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                                {{ substr($historico['data'],8,2) }}<br>{{ strtoupper($historico['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($historico['data'],0,4) }}</div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5>Transferência</h5>
                                    <p>
                                        <strong>Destino:</strong> {{ $historico['nomeUnidade_destino'] }}
                                        <br/><strong>Origem:</strong> {{ $historico['nomeUp_anterior'] }}
                                        <br/><strong>Motivo:</strong> {{ $historico['motivo'] }}
                                        <br/><strong>Status:</strong> {{ $historico['status'] }}
                                        <br/>
                                        @foreach($transferenciasHist as $transferencia)
                                        @foreach($transferencia['arquivos'] as $arquivo)
                                        @if($transferencia['idmov'] == $historico['fk_movimentacao'])
                                        <a href="{{ route('movimentacao.transferenciaHist.arquivoVisualizar',['id'=>$transferencia['idmov'],'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                                        @endif
                                        @endforeach
                                        @endforeach
                                    </p>

                                    @if($historico['logCancelamento'] != [])
                                    <p class="text-left">
                                        {{ "Transferência cancelada por: ".$historico['logCancelamento'][0]['realizadoPor']." em ".$historico['logCancelamento'][0]['data']." às ".$historico['logCancelamento'][0]['hora'] }}
                                    </p>
                                    @endif
                                    @if($historico['log'] != [])
                                    <p class="text-left">
                                        {{ "Transferência enviada por: ".$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <!-- !transferencia -->
                            <div class="row timeline">
                                <div class="col-md-2">
                                    <div class="corner top-right"></div>
                                </div>
                                <div class="col-md-8">
                                    <hr/>
                                </div>
                                <div class="col-md-2">
                                    <div class="corner left-bottom"></div>
                                </div>
                            </div>
                        </div>
                        @endif


                        <!--mudanca de progressaoaberto -->
                        @if($historico['tipo'] == 'progressaoaberto')
                        <div class="{{ $historico['tipo'] }} itens">
                            <div class="row align-items-center how-it-works d-flex">
                                <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                                    <div style="z-index: 2; background:white !important" class="font-weight-bold">
                                        <div class="mt-0 right-col text-right">
                                            <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                                {{ substr($historico['data'],8,2) }}<br>{{ strtoupper($historico['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($historico['data'],0,4) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5>Progressão para o Aberto</h5>
                                    <p>
                                        <strong>Motivo:</strong> {{ $historico['motivo'] }}
                                        <br/><strong>Origem:</strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                    </p>
                                    <br/>
                                    @foreach($abertosHist as $aberto)
                                    @foreach($aberto['arquivos'] as $arquivo)
                                    @if($aberto['idmov'] == $historico['fk_movimentacao'])
                                    <a href="{{ route('movimentacao.progressaoregressao.arquivoVisualizar',['id'=>$aberto['idmov'],'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @if($historico['log'] != null)
                                    <p class="text-left">
                                        {{ "Registrado por: ".$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <!--path between 2-3-->
                            <div class="row timeline">
                                <div class="col-md-2">
                                    <div class="corner right-bottom"></div>
                                </div>
                                <div class="col-md-8">
                                    <hr/>
                                </div>
                                <div class="col-md-2">
                                    <div class="corner top-left"></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!--mudanca de cela -->
                        @if($historico['tipo'] == 'mudancacela')
                        <div class="{{ $historico['tipo'] }} itens">
                            <div class="row align-items-center how-it-works d-flex">
                                <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                                    <div style="z-index: 2; background:white !important" class="font-weight-bold">
                                        <div class="mt-0 right-col text-right">
                                            <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                                {{ substr($historico['data'],8,2) }}<br>{{ strtoupper($historico['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($historico['data'],0,4) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5>Mudança de Cela</h5>
                                    <p>
                                        <strong>Motivo:</strong> {{ $historico['motivo'] }}
                                        <br/><strong>Destino:</strong> {{ $historico['nomeUnidade_destino'].", " .$historico['nomePavilhao_destino'].", " .$historico['nomeAla_destino'].", "  .$historico['nomeCela_destino'].", " }}
                                        <br/><strong>Origem:</strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                    </p>
                                    <br/>
                                    @foreach($mudancacelasHist as $mudanca)
                                    @foreach($mudanca['arquivos'] as $arquivo)
                                    @if($mudanca['idmov'] == $historico['fk_movimentacao'])
                                    <a href="{{ route('movimentacao.mudancaCela.arquivoVisualizar',['id'=>$mudanca['idmov'],'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @if($historico['log'] != null)
                                    <p class="text-left">
                                        {{ "Registrado por: ".$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <!--path between 2-3-->
                            <div class="row timeline">
                                <div class="col-md-2">
                                    <div class="corner right-bottom"></div>
                                </div>
                                <div class="col-md-8">
                                    <hr/>
                                </div>
                                <div class="col-md-2">
                                    <div class="corner top-left"></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!--mudanca para sentenciado ou provisório -->
                        @if($historico['tipo'] == 'mudancafechado' || $historico['tipo'] == 'mudancasemiaberto' || $historico['tipo'] == 'mudancaaberto' || $historico['tipo'] == 'mudancaprovisorio')
                        <div class="mudancasentenca itens">
                            <div class="row align-items-center how-it-works d-flex">
                                <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                                    <div style="z-index: 2; background:white !important" class="font-weight-bold">
                                        <div class="mt-0 right-col text-right">
                                            <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                                {{ substr($historico['data'],8,2) }}<br>{{ strtoupper($historico['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($historico['data'],0,4) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5>Mudança
                                        @if($historico['tipo'] == 'mudancafechado')
                                        de Provisório para o Regime Fechado
                                        @elseif($historico['tipo'] == 'mudancasemiaberto')
                                        de Provisório para o Regime Semiaberto
                                        @elseif($historico['tipo'] == 'mudancaaberto')
                                        de Provisório para o Regime Aberto
                                        @elseif($historico['tipo'] == 'mudancaprovisorio')
                                        de Sentenciado para Provisório
                                        @endif
                                    </h5>
                                    <p>
                                        <strong>Motivo:</strong> {{ $historico['motivo'] }}
                                    </p>
                                    <br/>
                                    @foreach($mudancasentencaHist as $mudanca)
                                    @foreach($mudanca['arquivos'] as $arquivo)
                                    @if($mudanca['idmov'] == $historico['fk_movimentacao'])
                                    <a href="{{ route('movimentacao.mudancasentenca.arquivoVisualizar',['id'=>$mudanca['idmov'],'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @if($historico['log'] != null)
                                    <p class="text-left">
                                        {{ "Registrado por: ".$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <!--path between 2-3-->
                            <div class="row timeline">
                                <div class="col-md-2">
                                    <div class="corner right-bottom"></div>
                                </div>
                                <div class="col-md-8">
                                    <hr/>
                                </div>
                                <div class="col-md-2">
                                    <div class="corner top-left"></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- recambiamento -->
                        @if($historico['tipo'] == 'recambiamento')
                        <div class="{{ $historico['tipo'] }} itens">
                            <div class="row align-items-center how-it-works d-flex">
                                <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                                    <div style="z-index: 2; background:white !important" class="font-weight-bold">
                                        <div class="mt-0 right-col text-right">
                                            <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                                {{ substr($historico['data'],8,2) }}<br>{{ strtoupper($historico['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($historico['data'],0,4) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5>Recambiamento</h5>
                                    <p>
                                        <strong>Destino:</strong> {{ UnidadesService::getNomeUp($historico['unidade_destino']) }}
                                        <br/><strong>Motivo e Observações:</strong> {{ $historico['motivo'] }}
                                        <br/><strong>Origem:</strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                    </p>
                                    <br/>
                                    @foreach($recambiamentosHist as $recambiamento)
                                    @foreach($recambiamento['arquivos'] as $arquivo)
                                    @if($recambiamento['idmov'] == $historico['fk_movimentacao'])
                                    <a href="{{ route('movimentacao.recambiamento.arquivoVisualizar',['id'=>$recambiamento['idmov'],'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @if($historico['log'] != null)
                                    <p class="text-left">
                                        {{ "Registrado por: ".$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <!--path between 2-3-->
                            <div class="row timeline">
                                <div class="col-md-2">
                                    <div class="corner right-bottom"></div>
                                </div>
                                <div class="col-md-8">
                                    <hr/>
                                </div>
                                <div class="col-md-2">
                                    <div class="corner top-left"></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!--mudanca de regressao semiaberto -->
                        @if($historico['tipo'] == 'regressaosemiaberto')
                        <div class="{{ $historico['tipo'] }} itens">
                            <div class="row align-items-center how-it-works d-flex">
                                <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                                    <div style="z-index: 2; background:white !important" class="font-weight-bold">
                                        <div class="mt-0 right-col text-right">
                                            <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                                {{ substr($historico['data'],8,2) }}<br>{{ strtoupper($historico['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($historico['data'],0,4) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5>Regressão para o Semiaberto</h5>
                                    <p>
                                        <strong>Motivo:</strong> {{ $historico['motivo'] }}
                                        <br/><strong>Destino:</strong> {{ $historico['nomeUnidade_destino'].", " .$historico['nomePavilhao_destino'].", " .$historico['nomeAla_destino'].", "  .$historico['nomeCela_destino'].", " }}
                                        <br/><strong>Origem:</strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                    </p>
                                    <br/>
                                    @foreach($regressaoesSemiHist as $regressoes)
                                    @foreach($regressoes['arquivos'] as $arquivo)
                                    @if($regressoes['idmov'] == $historico['fk_movimentacao'])
                                    <a href="{{ route('movimentacao.progressaoregressao.arquivoVisualizar',['id'=>$regressoes['idmov'],'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @if($historico['log'] != null)
                                    <p class="text-left">
                                        {{ "Registrado por: ".$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <!--path between 2-3-->
                            <div class="row timeline">
                                <div class="col-md-2">
                                    <div class="corner right-bottom"></div>
                                </div>
                                <div class="col-md-8">
                                    <hr/>
                                </div>
                                <div class="col-md-2">
                                    <div class="corner top-left"></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!--mudanca de progressao semiaberto -->
                        @if($historico['tipo'] == 'progressaosemiaberto')
                        <div class="{{ $historico['tipo'] }} itens">
                            <div class="row align-items-center how-it-works d-flex">
                                <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                                    <div style="z-index: 2; background:white !important" class="font-weight-bold">
                                        <div class="mt-0 right-col text-right">
                                            <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                                {{ substr($historico['data'],8,2) }}<br>{{ strtoupper($historico['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($historico['data'],0,4) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5>Progressão para o Semiaberto</h5>
                                    <p>
                                        <strong>Motivo:</strong> {{ $historico['motivo'] }}
                                        <br/><strong>Destino:</strong> {{ $historico['nomeUnidade_destino'].", " .$historico['nomePavilhao_destino'].", " .$historico['nomeAla_destino'].", "  .$historico['nomeCela_destino'].", " }}
                                        <br/><strong>Origem:</strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                    </p>
                                    <br/>
                                    @foreach($progressoesSemiHist as $progressao)
                                    @foreach($progressao['arquivos'] as $arquivo)
                                    @if($progressao['idmov'] == $historico['fk_movimentacao'])
                                    <a href="{{ route('movimentacao.progressaoregressao.arquivoVisualizar',['id'=>$progressao['idmov'],'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @if($historico['log'] != null)
                                    <p class="text-left">
                                        {{ "Registrado por: ".$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <!--path between 2-3-->
                            <div class="row timeline">
                                <div class="col-md-2">
                                    <div class="corner right-bottom"></div>
                                </div>
                                <div class="col-md-8">
                                    <hr/>
                                </div>
                                <div class="col-md-2">
                                    <div class="corner top-left"></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!--saida judicial -->
                        @if($historico['tipo'] == 'saidajudicial')
                        <div class="{{ $historico['tipo'] }} itens">
                            <div class="row align-items-center how-it-works d-flex">
                                <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                                    <div style="z-index: 2; background:white !important" class="font-weight-bold">
                                        <div class="mt-0 right-col text-right">
                                            <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                                {{ substr($historico['data'],8,2) }}<br>{{ strtoupper($historico['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($historico['data'],0,4) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5>Saída Judicial</h5>
                                    <p>
                                        <strong>Observação:</strong> {{ $historico['motivo'] }}
                                    </p>
                                    <br/>
                                    @foreach($saidaJudicialHist as $saidaHist)
                                    @foreach($saidaHist['arquivos'] as $arquivo)
                                    @if($saidaHist['idmov'] == $historico['fk_movimentacao'])
                                    <a href="{{ route('movimentacao.saidajudicial.arquivoVisualizar',['id'=>$saidaHist['idmov'],'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @if($historico['log'] != null)
                                    <p class="text-left">
                                        {{ "Registrado por: ".$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <!--path between 2-3-->
                            <div class="row timeline">
                                <div class="col-md-2">
                                    <div class="corner right-bottom"></div>
                                </div>
                                <div class="col-md-8">
                                    <hr/>
                                </div>
                                <div class="col-md-2">
                                    <div class="corner top-left"></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!--regressao regressaofechado -->
                        @if($historico['tipo'] == 'regressaofechado')
                        <div class="{{ $historico['tipo'] }} itens">
                            <div class="row align-items-center how-it-works d-flex">
                                <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                                    <div style="z-index: 2; background:white !important" class="font-weight-bold">
                                        <div class="mt-0 right-col text-right">
                                            <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                                {{ substr($historico['data'],8,2) }}<br>{{ strtoupper($historico['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($historico['data'],0,4) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5>Regressão para o Fechado</h5>
                                    <p>
                                        <strong>Motivo:</strong> {{ $historico['motivo'] }}
                                        <br/><strong>Destino:</strong> {{ $historico['nomeUnidade_destino'].", " .$historico['nomePavilhao_destino'].", " .$historico['nomeAla_destino'].", "  .$historico['nomeCela_destino'].", " }}
                                        <br/><strong>Origem:</strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                    </p>
                                    <br/>
                                    @foreach($regressoesFechadoHist as $regressoes)
                                    @foreach($regressoes['arquivos'] as $arquivo)
                                    @if($regressoes['idmov'] == $historico['fk_movimentacao'])
                                    <a href="{{ route('movimentacao.progressaoregressao.arquivoVisualizar',['id'=>$regressoes['idmov'],'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @if($historico['log'] != null)
                                    <p class="text-left">
                                        {{ "Registrado por: ".$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <!--path between 2-3-->
                            <div class="row timeline">
                                <div class="col-md-2">
                                    <div class="corner right-bottom"></div>
                                </div>
                                <div class="col-md-8">
                                    <hr/>
                                </div>
                                <div class="col-md-2">
                                    <div class="corner top-left"></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!--extincaopunibilidade -->
                        @if($historico['tipo'] == 'extincaopunibilidade')
                        <div class="{{ $historico['tipo'] }} itens">
                            <div class="row align-items-center how-it-works d-flex">
                                <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                                    <div style="z-index: 2; background:white !important" class="font-weight-bold">
                                        <div class="mt-0 right-col text-right">
                                            <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                                {{ substr($historico['data'],8,2) }}<br>{{ strtoupper($historico['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($historico['data'],0,4) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5>Extinção de Punibilidade</h5>
                                    <p>
                                        <strong>Observações:</strong> {{ $historico['motivo'] }}
                                        <br/><strong>Origem:</strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                    </p>
                                    <br/>
                                    @foreach($extincoespunibilidadesHist as $ep)
                                    @foreach($ep['arquivos'] as $arquivo)
                                    @if($ep['idmov'] == $historico['fk_movimentacao'])
                                    <a href="{{ route('movimentacao.extincaopunibilidade.arquivoVisualizar',['id'=>$ep['idmov'],'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @if($historico['log'] != null)
                                    <p class="text-left">
                                        {{ "Registrado por: ".$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <!--path between 2-3-->
                            <div class="row timeline">
                                <div class="col-md-2">
                                    <div class="corner right-bottom"></div>
                                </div>
                                <div class="col-md-8">
                                    <hr/>
                                </div>
                                <div class="col-md-2">
                                    <div class="corner top-left"></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!--extradicao -->
                        @if($historico['tipo'] == 'extradicao')
                        <div class="{{ $historico['tipo'] }} itens">
                            <div class="row align-items-center how-it-works d-flex">
                                <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                                    <div style="z-index: 2; background:white !important" class="font-weight-bold">
                                        <div class="mt-0 right-col text-right">
                                            <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                                {{ substr($historico['data'],8,2) }}<br>{{ strtoupper($historico['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($historico['data'],0,4) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5>Extradição</h5>
                                    <p>
                                        <strong>Observações:</strong> {{ $historico['motivo'] }}
                                        <br/><strong>Origem:</strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                    </p>
                                    <br/>
                                    @foreach($extradicoesHist as $ex)
                                    @foreach($ex['arquivos'] as $arquivo)
                                    @if($ex['idmov'] == $historico['fk_movimentacao'])
                                    <a href="{{ route('movimentacao.extradicao.arquivoVisualizar',['id'=>$ex['idmov'],'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @if($historico['log'] != null)
                                    <p class="text-left">
                                        {{ "Registrado por: ".$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <!--path between 2-3-->
                            <div class="row timeline">
                                <div class="col-md-2">
                                    <div class="corner right-bottom"></div>
                                </div>
                                <div class="col-md-8">
                                    <hr/>
                                </div>
                                <div class="col-md-2">
                                    <div class="corner top-left"></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- prisão domiciliar -->
                        @if($historico['tipo'] == 'prisaodomiciliar')
                        <div class="{{ $historico['tipo'] }} itens">
                            <div class="row align-items-center how-it-works d-flex">
                                <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                                    <div style="z-index: 2; background:white !important" class="font-weight-bold">
                                        <div class="mt-0 right-col text-right">
                                            <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                                {{ substr($historico['data'],8,2) }}<br>{{ strtoupper($historico['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($historico['data'],0,4) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5>Prisão Domiciliar</h5>
                                    <p>
                                        <strong>Motivo e Observações:</strong> {{ $historico['motivo'] }}
                                        <br/><strong>Origem:</strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                    </p>
                                    <br/>
                                    @foreach($prisoesdomiciliaresHist as $pd)
                                    @foreach($pd['arquivos'] as $arquivo)
                                    @if($pd['idmov'] == $historico['fk_movimentacao'])
                                    <a href="{{ route('movimentacao.prisaodomiciliar.arquivoVisualizar',['id'=>$pd['idmov'],'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @if($historico['log'] != null)
                                    <p class="text-left">
                                        {{ "Registrado por: ".$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <!--path between 2-3-->
                            <div class="row timeline">
                                <div class="col-md-2">
                                    <div class="corner right-bottom"></div>
                                </div>
                                <div class="col-md-8">
                                    <hr/>
                                </div>
                                <div class="col-md-2">
                                    <div class="corner top-left"></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- obito -->
                        @if($historico['tipo'] == 'obito')
                        <div class="{{ $historico['tipo'] }} itens">
                            <div class="row align-items-center how-it-works d-flex">
                                <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                                    <div style="z-index: 2; background:white !important" class="font-weight-bold">
                                        <div class="mt-0 right-col text-right">
                                            <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                                {{ substr($historico['data'],8,2) }}<br>{{ strtoupper($historico['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($historico['data'],0,4) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5>Óbito</h5>
                                    <p>
                                        <strong>Observações:</strong> {{ $historico['motivo'] }}
                                        <br/><strong>Origem:</strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                    </p>
                                    <br/>
                                    @foreach($obitosHist as $obt)
                                    @foreach($obt['arquivos'] as $arquivo)
                                    @if($obt['idmov'] == $historico['fk_movimentacao'])
                                    <a href="{{ route('movimentacao.obito.arquivoVisualizar',['id'=>$obt['idmov'],'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @if($historico['log'] != null)
                                    <p class="text-left">
                                        {{ "Registrado por: ".$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <!--path between 2-3-->
                            <div class="row timeline">
                                <div class="col-md-2">
                                    <div class="corner right-bottom"></div>
                                </div>
                                <div class="col-md-8">
                                    <hr/>
                                </div>
                                <div class="col-md-2">
                                    <div class="corner top-left"></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!--livramentocondicional -->
                        @if($historico['tipo'] == 'livramentocondicional')
                        <div class="{{ $historico['tipo'] }} itens">
                            <div class="row align-items-center how-it-works d-flex">
                                <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                                    <div style="z-index: 2; background:white !important" class="font-weight-bold">
                                        <div class="mt-0 right-col text-right">
                                            <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                                {{ substr($historico['data'],8,2) }}<br>{{ strtoupper($historico['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($historico['data'],0,4) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5>Livramento Condicional</h5>
                                    <p>
                                        <strong>Observações:</strong> {{ $historico['motivo'] }}
                                        <br/><strong>Origem:</strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                    </p>
                                    <br/>
                                    @foreach($livramentoscondicionaisHist as $lc)
                                    @foreach($lc['arquivos'] as $arquivo)
                                    @if($lc['idmov'] == $historico['fk_movimentacao'])
                                    <a href="{{ route('movimentacao.livramentocondicional.arquivoVisualizar',['id'=>$lc['idmov'],'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @if($historico['log'] != null)
                                    <p class="text-left">
                                        {{ "Registrado por: ".$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <!--path between 2-3-->
                            <div class="row timeline">
                                <div class="col-md-2">
                                    <div class="corner right-bottom"></div>
                                </div>
                                <div class="col-md-8">
                                    <hr/>
                                </div>
                                <div class="col-md-2">
                                    <div class="corner top-left"></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!--alvara -->
                        @if($historico['tipo'] == 'alvara')
                        <div class="{{ $historico['tipo'] }} itens">
                            <div class="row align-items-center how-it-works d-flex">
                                <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                                    <div style="z-index: 2; background:white !important" class="font-weight-bold">
                                        <div class="mt-0 right-col text-right">
                                            <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                                {{ substr($historico['data'],8,2) }}<br>{{ strtoupper($historico['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($historico['data'],0,4) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5>Alvará de Soltura</h5>
                                    <p>
                                        <strong>Observações:</strong> {{ $historico['motivo'] }}
                                        <br/><strong>Origem:</strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                    </p>
                                    <br/>
                                    @foreach($alvarasHist as $alvara)
                                    @foreach($alvara['arquivos'] as $arquivo)
                                    @if($alvara['idmov'] == $historico['fk_movimentacao'])
                                    <a href="{{ route('movimentacao.alvara.arquivoVisualizar',['id'=>$alvara['idmov'],'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @if($historico['log'] != null)
                                    <p class="text-left">
                                        {{ "Registrado por: ".$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <!--path between 2-3-->
                            <div class="row timeline">
                                <div class="col-md-2">
                                    <div class="corner right-bottom"></div>
                                </div>
                                <div class="col-md-8">
                                    <hr/>
                                </div>
                                <div class="col-md-2">
                                    <div class="corner top-left"></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!--fuga -->
                        @if($historico['tipo'] == 'fuga')
                        <div class="{{ $historico['tipo'] }} itens">
                            <div class="row align-items-center how-it-works d-flex">
                                <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                                    <div style="z-index: 2; background:white !important" class="font-weight-bold">
                                        <div class="mt-0 right-col text-right">
                                            <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                                {{ substr($historico['data'],8,2) }}<br>{{ strtoupper($historico['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($historico['data'],0,4) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5>Fuga</h5>
                                    <p>
                                        <strong>Observações:</strong> {{ $historico['motivo'] }}
                                        <br/><strong>Origem:</strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                    </p>
                                    @foreach($fugasHist as $fugas)
                                    @foreach($fugas['arquivos'] as $arquivo)
                                    @if($fugas['idmov'] == $historico['fk_movimentacao'])
                                    <a href="{{ route('movimentacao.fuga.arquivoVisualizar',['id'=>$fugas['idmov'],'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @if($historico['log'] != null)
                                    <p class="text-left">
                                        {{ "Registrado por: ".$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <!--path between 2-3-->
                            <div class="row timeline">
                                <div class="col-md-2">
                                    <div class="corner right-bottom"></div>
                                </div>
                                <div class="col-md-8">
                                    <hr/>
                                </div>
                                <div class="col-md-2">
                                    <div class="corner top-left"></div>
                                </div>
                            </div>
                        </div>
                        @endif


                        @if($historico['tipo'] == 'entradaunidadeprisional' || $historico['tipo'] == 'transferenciaentrada')
                        <div class="{{ $historico['tipo'] }} itens">
                            <!-- entradaunidadeprisional -->
                            <div class="row align-items-center how-it-works d-flex">
                                <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                                    <div style="z-index: 2; background:white !important" class="font-weight-bold">
                                        <div class="mt-0 right-col text-right">
                                            <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                                {{ substr($historico['data'],8,2) }}<br>{{ strtoupper($historico['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($historico['data'],0,4) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5>Entrada na Unidade Prisional</h5>
                                    <p>@if(!is_null($historico['motivo'])) {{"Motivo: ".$historico['motivo']}} @endif <br> Entrada na unidade prisional {{ $historico['nomeUnidade_destino'] }}, {{ $historico['nomePavilhao_destino'] }}, {{ $historico['nomeAla_destino'] }}, {{ $historico['nomeCela_destino'] }}.</p>
                                    <br/>
                                    @foreach($entradasUnidadeHist as $entrada)
                                    @foreach($entrada['arquivos'] as $arquivo)
                                    @if($entrada['idmov'] == $historico['fk_movimentacao'])
                                    <a href="{{ route('interno.fileDoc',['id'=>$historico['fk_interno'],'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                                    @endif
                                    @endforeach
                                    @endforeach
                                    @if($historico['log'] != null)
                                    <p class="text-left">
                                        {{ "Registrado por: ".$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                    </p>
                                    @endif

                                </div>
                            </div>
                            <!--path between 2-3-->
                            <div class="row timeline">
                                <div class="col-md-2">
                                    <div class="corner right-bottom"></div>
                                </div>
                                <div class="col-md-8">
                                    <hr/>
                                </div>
                                <div class="col-md-2">
                                    <div class="corner top-left"></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @endforeach
                        @endif
                        <div>
                            @if(count($historicoAntigo) > 0)
                            <h6 class="text-center">-- Histórico {{ config('app.name') }} Antigo -- </h6>
                            @endif

                            @foreach($historicoAntigo as $hisAnt)
                            <div class="historicoAntigo">
                                <div class="row align-items-center how-it-works d-flex">
                                    <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                                        <div style="z-index: 2; background:white !important" class="font-weight-bold">
                                            <div class="mt-0 right-col text-right">
                                                <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                                    {{ substr($hisAnt['data'],0,2) }}<br>{{ strtoupper($hisAnt['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($hisAnt['data'],6,4) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <h5>{{ $hisAnt['motivoSaida'] }}</h5>
                                        <p>
                                            <strong>Unidade Prisional:</strong> {{ $hisAnt['unidadePrisional'] }}<br/>
                                            <strong>Descrição:</strong> {{ $hisAnt['descricao'] }}<br/>
                                        </p>
                                    </div>
                                </div>
                                <!--path between 2-3-->
                                <div class="row timeline">
                                    <div class="col-md-2">
                                        <div class="corner right-bottom"></div>
                                    </div>
                                    <div class="col-md-8">
                                        <hr/>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="corner top-left"></div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <!-- FIM DO CONTAINER DA ROTA -->
            </div>
        </div>
    </div>
    <a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
    {{-- modal para abrir a foto do interno --}}
        <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <div id="fototipo"></div>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
                    </div>
                    <div class="modal-body embed-responsive text-center">
                        <img src="" id="imagepreview" style="max-width:90%">
                    </div>
                    <hr/>
                    <div class="text-left">
                        <div id="fotolegenda"></div>
                        <div id="fotodatainserida"></div>
                    </div>
                </div>
            </div>
        </div>
    {{-- !! modal para abrir a foto do interno --}}
@endsection
@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{ asset('css/interno/show/show.css') }}" rel="stylesheet">
@endpush
@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{asset('js/interno/show.js')}}"></script>
<script src="{{ asset('js/interno/_historico.js') }}"></script>
@endpush