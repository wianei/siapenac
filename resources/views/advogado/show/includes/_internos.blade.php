<div class="container">
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-users"></i> Internos</h3>
        <hr>
        <p>
            Lista de internos atendidos por este advogado em alguma unidade prisional da {{ config('app.uf') }}, bem como todos os registros desses atendimentos.
        </p>
    </div>
</div>
<div class="row pl-4 pr-4">
    @if($advogado['presos_visitados'] == collect([]))
        <p>Não há registros de atendimento para este advogado.</p>
    @endif
    @foreach($advogado['presos_visitados'] as $preso_visitado)
        <div class="col-md-2 card mr-1 mb-1">
            <div>
                @if($preso_visitado['dadosInterno']['foto'] != 'storage/app/semfoto.jpg')
                    <img src="{{asset($preso_visitado['dadosInterno']['foto'])}}" style="max-height: 8rem;" data-content="{{$preso_visitado['dadosInterno']['fototipo']}},{{$preso_visitado['dadosInterno']['fotolegenda']}},{{$preso_visitado['dadosInterno']['fotoinserida']}}" class="mx-auto d-block imagemSrc cursorPointer text-center figure-img img-fluid" title="Clique para ampliar a foto" alt="{{$preso_visitado['dadosInterno']['fototipo']}}"/>
                @else
                    <img src="{{asset($preso_visitado['dadosInterno']['foto'])}}" style="max-height: 8rem;" class="mx-auto d-block text-center figure-img img-fluid"/>
                @endif
                <h5>{{$preso_visitado['dadosInterno']['nome']}}</h5>
                <p>
                    {{$preso_visitado['dadosInterno']['sigla']}},<br/><br/>
                    @if(!is_null($preso_visitado['dadosInterno']['facNome']))Facção: <strong>{{$preso_visitado['dadosInterno']['facNome']}}</strong>,<br/>@endif
                    @if(!is_null($preso_visitado['dadosInterno']['lideranca']) && $preso_visitado['dadosInterno']['lideranca'] == 'Sim')Liderança: <strong>{{$preso_visitado['dadosInterno']['lideranca']}}</strong>,<br/>@endif
                    @if(!is_null($preso_visitado['dadosInterno']['facNome']) && !is_null($preso_visitado['dadosInterno']['lideranca']))<br/>@endif
                    Status: {{$preso_visitado['status']=="A"?"Autorizado":($preso_visitado['status']=="S"?"Suspenso":"Proibido")}}
                </p>
                <a href="{{ route('interno.show', $preso_visitado['dadosInterno']['id']) }}" class="btn btn-primary">Ver Detalhes</a>
            </div>
        </div>
    @endforeach
</div>
<hr/>
<div class="card-body pl-1 pr-1 row">
    <div class="col-md-2 esconderNaImpressao">
        <h4 class="card-title">Filtros</h4>
        <form class="forms-sample">
            <div class="form-group">
                <div  class="mb-2">
                    <label for="data_inicio">Data inicial</label>
                    <input type="text" autocomplete="off" class="form-control" name="dataInicioPesquisaAdv" id="dataInicioPesquisaAdv" placeholder="">
                </div>
                <div  class="mb-2">
                    <label for="data_final">Data final</label>
                    <input type="text" autocomplete="off" class="form-control" name="dataFinalPesquisaAdv" id="dataFinalPesquisaAdv" placeholder="">
                </div>
                <button type="button" id="btnPesquisarAdv" class="btn btn-dark m-t-20 buscaAjax">Filtrar</button>
            </div>
        </form>
    </div>

    <div class="col" id="itensAdvCol">
        @foreach($advogadoHistorico as $advHistorico)
                <!--first section-->
        <div class="row align-items-center how-it-works d-flex">
            <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                <div style="z-index: 2; background:white !important" class="font-weight-bold">
                    <div class="mt-0 right-col text-right">
                        <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                            {{ substr($advHistorico['data'],0,2) }}<br>{{ strtoupper($advHistorico['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($advHistorico['data'],6,4) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md">
                <!-- Trigger the Modal -->
                <img id="myImg" src="{{ $advHistorico['fotoInterno'] }}" class="img-thumbnail img-fluid rounded float-left mb-2">

                <!-- The Modal -->
                <div id="myModal" style="z-index:10000" class="modal">

                    <!-- The Close Button -->
                    <span class="close" style="z-index:10001">&times;</span>

                    <!-- Modal Content (The Image) -->
                    <img class="modal-content" id="img01">

                    <!-- Modal Caption (Image Text) -->
                    <div id="caption"></div>
                </div>
            </div>
            <div class="col-md-8">
                <h5>{{ $advHistorico['nomeInterno'] }}</h5>
                <p>
                    <strong>Prontuário:</strong> {{ $advHistorico['prontuario'] }}
                    <br/><strong>Unidade:</strong> {{ $advHistorico['unidade'] }}
                    <br/><strong>Horário de início do atendimento:</strong> {{ $advHistorico['horaInicio'] }}
                    <br/><strong>Horário de término do atendimento:</strong> {{ $advHistorico['horaTermino'] }}
                    <br/><strong>Duração do contato com o interno:</strong> {{ $advHistorico['duracao'] }}
                </p>
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
<script src="{{ asset('js/advogado/_internos.js') }}"></script>
@endpush