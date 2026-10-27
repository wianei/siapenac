<div class="container">
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-briefcase-medical"></i> Saúde e Assistência</h3>
        <hr>
        <p>
            Atendimentos de saúde e assistência social recebidos, além de medicamentos prescritos e doenças que o interno possui.
        </p>
    </div>
</div>
<div class="card-body pl-1 pr-1 row">
    <div class="col-md-2 esconderNaImpressao">
        <h4 class="card-title">Filtros</h4>
        <p class="card-description">Deixe marcado as opções a exibir:</p>
        <form class="forms-sample">
            <div class="form-group">
                <div  class="mb-2">
                    <label for="data_inicio">Data inicial</label>
                    <input type="text" autocomplete="off" class="form-control" name="dataInicioPesquisaSaudeAssist" id="dataInicioPesquisaSaudeAssist" placeholder="">
                </div>
                <div  class="mb-2">
                    <label for="data_final">Data final</label>
                    <input type="text" autocomplete="off" class="form-control" name="dataFinalSaudeAssist" id="dataFinalSaudeAssist" placeholder="">
                </div>
                <button type="button" id="btnPesquisarSaudeAssist" class="btn btn-dark m-t-20 buscaAjax">Filtrar</button>
            </div>
        </form>
    </div>
    <div class="col" id="itensSaudeAssistCol">

        @foreach($saudeAssist as $saude)
        <!--first section-->
        <div class="row align-items-center how-it-works d-flex">
            <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                <div style="z-index: 2; background:white !important" class="font-weight-bold">
                    <div class="mt-0 right-col text-right">
                        <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                            {{ substr($saude['dataAtendimento'],0,2) }}<br>{{ strtoupper($saude['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($saude['dataAtendimento'],6,4) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md">
                <h5>{{ $saude['setor'] }}</h5>
                <p>
                    @if($saude['crm'] && $saude['nomeMedico'])
                    <strong>Médico:</strong> {{ $saude['nomeMedico'] }} [{{$saude['crm']}}]
                    @endif
                    <br/><strong>Horário de atendimento:</strong> {{ $saude['horaAtendimento']  }}
                    <br/><strong>Descrição do Atendimento</strong> {!! $saude['descricao'] !!}
                </p>

                <p class="text-left">{{ $saude['log'] }}</p>
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
@push('script')
<script src="{{ asset('js/interno/_saudeAssistencia.js') }}"></script>
@endpush