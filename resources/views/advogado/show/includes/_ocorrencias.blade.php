<div class="container">
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-history"></i> Ocorrências envolvendo este advogado</h3>
        <hr>
        <p>
            Histórico completo de ocorrências envolvendo este advogado no sistema penitenciário da {{ config('app.uf') }}.
        </p>
    </div>
    <div class="card-body pl-1 pr-1 row">
        <div class="col-md-2 esconderNaImpressao">
            <h4 class="card-title">Filtros</h4>
            <form class="forms-sample">
                <div class="form-group">
                    <div  class="mb-2">
                        <label for="data_inicio">Data inicial</label>
                        <input type="text" autocomplete="off" class="form-control" name="dataInicioPesquisaOcorrencia" id="dataInicioPesquisaOcorrencia" placeholder="">
                    </div>
                    <div  class="mb-2">
                        <label for="data_final">Data final</label>
                        <input type="text" autocomplete="off" class="form-control" name="dataFinalPesquisaOcorrencia" id="dataFinalPesquisaOcorrencia" placeholder="">
                    </div>
                    <button type="button" id="btnPesquisarOcorrencias" class="btn btn-dark m-t-20 buscaAjax">Filtrar</button>
                </div>
            </form>
        </div>
        <div class="col" id="itensOcorrenciaCol">
            @foreach($ocorrencias as $ocorrencia)
                    <!--first section-->
            <div class="row align-items-center how-it-works d-flex">
                <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                    <div style="z-index: 2; background:white !important" class="font-weight-bold">
                        <div class="mt-0 right-col text-right">
                            <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                {{ substr($ocorrencia['data'],0,2) }}<br>{{ strtoupper($ocorrencia['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($ocorrencia['data'],6,4) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <h5>{{ $ocorrencia['naturezaOcorrencia'] }}</h5>
                    <p>
                        <strong>Horário:</strong> {{ $ocorrencia['horario'] }}
                        <br/><strong>Unidade Prisional:</strong> {{ $ocorrencia['unidade'] }}
                        @if($ocorrencia['servidoresEnvolvidos'] != null)
                            <br/><strong>Agentes Envolvidos:</strong>
                            @foreach($ocorrencia['servidoresEnvolvidos'] as $servidores)
                                {{ $servidores['nomeAgente']." (".$servidores['matricula']."), " }}
                            @endforeach
                        @endif
                        @if($ocorrencia['internosEnvolvidos'] != null)
                            <br/><strong>Internos Envolvidos:</strong>
                            @foreach($ocorrencia['internosEnvolvidos'] as $internos)
                                {{ $internos['nomeInterno']." (".$internos['prontuario']."), " }}
                            @endforeach
                        @endif
                        @if($ocorrencia['advsEnvolvidos'] != null)
                            <br/><strong>Advogados Envolvidos:</strong>
                            @foreach($ocorrencia['advsEnvolvidos'] as $advs)
                                {{ $advs['nomeAdv']." (".$advs['oab']."), " }}
                            @endforeach
                        @endif
                        @if($ocorrencia['visitasEnvolvidas'] != null)
                            <br/><strong>Outras Visitantes Envolvidas:</strong>
                            @foreach($ocorrencia['visitasEnvolvidas'] as $visitas)
                                {{ $visitas['nomeVisita']." (".$visitas['cpfVisita']."), " }}
                            @endforeach
                        @endif
                    </p>
                    <a href="{{ route('ocorrencia.visualizarOcorrencia', $ocorrencia['idOcorrencia']) }}" onclick="" class="btn btn-primary mr-1 mb-1 fileDownload" id="fileDownload" title="Ver Ocorrência" data-toggle="tooltip" data-placement="right"><i class="mdi mdi-link"></i></a>
                    @if($ocorrencia['dadosLog'] != null)
                        <p class="text-left">
                            {{ "Registrado por: ".$ocorrencia['dadosLog'][0]['realizadoPor']." em ".$ocorrencia['dadosLog'][0]['data']." às ".$ocorrencia['dadosLog'][0]['hora'] }}
                        </p>
                    @endif

                </div>
            </div>
            <!--path between 1-2-->
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
            @endforeach

        </div>
    </div>
</div>
@push('script')
<script src="{{ asset('js/advogado/_ocorrencias.js') }}"></script>
@endpush