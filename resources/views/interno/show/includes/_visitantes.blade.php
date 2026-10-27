<div class="container">
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-users"></i> Visitantes</h3>
        <hr>
        <p>
            Lista de Visitas que visitaram o interno em alguma unidade prisional da BA, bem como todos os registros dessas visitas.
        </p>
    </div>
</div>
<div class="row pl-4 pr-4">
    @if($interno['visitas'] == [])
        <p>Sem visitantes cadastrados.</p>
    @endif
    @foreach($interno['visitas'] as $visita)
        <div class="card mr-1 mb-1">
            @if($visita['dadosVisita']['fotoPrincipal']['nome_arquivo'] != 'storage/app/semfoto.jpg')
                <img src="{{asset($visita['dadosVisita']['fotoPrincipal']['nome_arquivo'])}}" style="max-height: 8rem;" data-content="{{$visita['dadosVisita']['fotoPrincipal']['tipo']}},{{$visita['dadosVisita']['fotoPrincipal']['legenda']}},{{$visita['dadosVisita']['fotoPrincipal']['created_at']}}" class="mx-auto d-block imagemSrc cursorPointer text-center figure-img img-fluid" title="Clique para ampliar a foto" alt="{{$visita['dadosVisita']['fotoPrincipal']['tipo']}}"/>
            @else
                <img src="{{asset('imagens/usuario/avatarInterno.png')}}" style="max-height: 8rem;" class="mx-auto d-block text-center figure-img img-fluid"/>
            @endif
            <div class="card-body">
                <h5 class="card-title">{{$visita['dadosVisita']['nome']}}</h5>
                <p class="card-text">{{$visita['grau_parentesco']}} do interno (a),<br/><br/>
                    Status: {{ $visita['statusSuspensao'] }}</p>
                <a href="{{ route('visita.show', $visita['dadosVisita']['id']) }}" class="btn btn-primary">Ver Detalhes</a>
            </div>
        </div>
    @endforeach
</div>
<hr/>
<div class="card-body pl-1 pr-1 row">
    <div class="col-md-2 esconderNaImpressao">
        <h4 class="card-title">Filtros</h4>
        <p class="card-description">Deixe marcado as opções a exibir:</p>
        <form class="forms-sample">
            <div class="form-group">
                <div  class="mb-2">
                    <label for="data_inicio">Data inicial</label>
                    <input type="text" autocomplete="off" class="form-control" name="dataInicioPesquisaVisitas" id="dataInicioPesquisaVisitas" placeholder="">
                </div>
                <div  class="mb-2">
                    <label for="data_final">Data final</label>
                    <input type="text" autocomplete="off" class="form-control" name="dataFinalPesquisaVisitas" id="dataFinalPesquisaVisitas" placeholder="">
                </div>
                <button type="button" id="btnPesquisarVisitasData" class="btn btn-dark m-t-20 buscaAjax">Filtrar</button>
            </div>
        </form>
    </div>

    <div class="col" id="itensVisitaCol">

@foreach($visitanteHistorico as $visitante)
        <!--first section-->
        <div class="row align-items-center how-it-works d-flex">
            <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center" style="z-index: 1 !important;">
                <div style="z-index: 2; background:white !important" class="font-weight-bold">
                    <div class="mt-0 right-col text-right">
                        <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                            {{ substr($visitante['dataEntrada'],0,2) }}<br>{{ strtoupper($visitante['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($visitante['dataEntrada'],6,4) }}</div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md">
                <!-- Trigger the Modal -->
                <img id="myImg" src="{{ asset($visitante['fotoVisita']) }}" alt="Legenda." class="img-thumbnail img-fluid rounded float-left mb-2">
            </div>
            <div class="col-md-8">
                <h5>{{ $visitante['nomeVisita'] }}</h5>
                <p>{{ $visitante['relacao'] }}
                    <br/><strong>Horário de entrada na unidade prisional:</strong> {{ $visitante['horaEntrada'] }}
                    <br/><strong>Horário de saída na unidade prisional:</strong> {{ $visitante['horaSaida'] }}
                    <br/><strong>Duração do contato com o interno:</strong> {{ $visitante['duracao'] }}</p>
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
<script src="{{ asset('js/interno/_visitantes.js') }}"></script>
@endpush