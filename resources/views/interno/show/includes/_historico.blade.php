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
@push('script')
<script src="{{ asset('js/interno/_historico.js') }}"></script>
@endpush