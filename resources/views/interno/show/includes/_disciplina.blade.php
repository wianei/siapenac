<div class="container">
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-gavel"></i> Disciplina</h3>
        <hr>
        <p>
            Histórico de Comportamento, faltas disciplinares, sindicâncias e emissão de atestado de conduta carcerária.
        </p>
    </div>
</div>
<div class="card-body pl-1 pr-1 row">
    <div class="col-md-2 esconderNaImpressao">
        <h4 class="card-title">Filtros</h4>
        <p class="card-description">Deixe marcado as opções a exibir:</p>
        <form class="forms-sample">
            <div class="form-group">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" name="checkFiltroFalta" value="fGrave" class="form-check-input"> Falta Grave
                        <i class="input-helper"></i></label>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" name="checkFiltroFalta" value="fMédia" class="form-check-input"> Falta Média
                        <i class="input-helper"></i></label>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" name="checkFiltroFalta" value="fLeve" class="form-check-input"> Falta Leve
                        <i class="input-helper"></i></label>
                </div>
                <div class="mb-2">
                    <button type="button" id="btnPesquisarFaltas" class="btn btn-dark m-t-20 buscaAjax">Filtrar</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col allItensFaltas">

        @foreach($faltas as $falta)
        <div class="f{{$falta['dadosSindicancia'][0]['gravidadeFalta']}} itensFalta">
            <!--first section-->
            <div class="row align-items-center how-it-works d-flex">
                <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                    <div style="z-index: 2; background:white !important" class="font-weight-bold">
                        <div class="mt-0 right-col text-right">
                            <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                {{ substr($falta['dadosSindicancia'][0]['dataIncidente'],8,2) }}<br>
                                {{ strtoupper($falta['dadosSindicancia'][0]['mesData']) }}<br>
                                <div style="font-size:0.7em;">{{ substr($falta['dadosSindicancia'][0]['dataIncidente'],0,4) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <h5>Falta {{ $falta['dadosSindicancia'][0]['gravidadeFalta'] }}</h5>
                    <p>
                        <strong>Título:</strong> {{ $falta['dadosSindicancia'][0]['titulo'] }}
                        <br/><strong>Status:</strong> {{ $falta['dadosSindicancia'][0]['status'] }}
                        <br/><strong>Unidade:</strong> {{ $falta['dadosSindicancia'][0]['unidade'] }}
                        @if($falta['dadosSindicanciaAgentes'] != null)
                        <br/><strong>Agentes Envolvidos:</strong>
                            @foreach($falta['dadosSindicanciaAgentes'] as $agentes)
                                {{ $agentes['nomeAgente']." (".$agentes['matricula']."), " }}
                            @endforeach
                        @endif
                    </p>
                    @if($falta['dadosSindicanciaArquivos'] != null)
                        @foreach($falta['dadosSindicanciaArquivos'] as $arquivosSind)
                            <a href="{{ route('sindicancia.arquivoVisualizar',['id'=>$falta['dadosSindicancia'][0]['id'],'filename'=>$arquivosSind['nomeArquivo']]) }}" onclick="" class="btn btn-primary mr-1 mb-1 fileDownload" id="fileDownload" title="{{ $arquivosSind['nomeArquivo'] }}" data-toggle="tooltip" data-placement="right"><i class="mdi mdi-cloud-download"></i></a>
                        @endforeach
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
        </div>
        @endforeach

    </div>
</div>
@push('script')
<script src="{{ asset('js/interno/_disciplina.js') }}"></script>
@endpush