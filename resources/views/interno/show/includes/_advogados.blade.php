@if(\Illuminate\Support\Facades\Auth::user()->tipo >= 300)
<div class="container">
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-user-tie"></i> Advogados</h3>
        <hr>
        <p>
            Lista de Advogados que já visitaram o interno em alguma unidade prisional da BA, bem como todos os registros dessas visitas.
        </p>
    </div>
</div>
<div class="row pl-4 pr-4">
    @if($interno['advogados'] == [])
        <p>Sem advogados cadastrados.</p>
    @endif
    @foreach($interno['advogados'] as $advogado)
        <div class="card mr-1 mb-1">
            @if($advogado['dadosAdvogado']['fotoPrincipal']['nome_arquivo'] != 'storage/app/semfoto.jpg')
                <img src="{{asset($advogado['dadosAdvogado']['fotoPrincipal']['nome_arquivo'])}}" style="max-height: 8rem;" data-content="{{$advogado['dadosAdvogado']['fotoPrincipal']['tipo']}},{{$advogado['dadosAdvogado']['fotoPrincipal']['legenda']}},{{$advogado['dadosAdvogado']['fotoPrincipal']['created_at']}}" class="mx-auto d-block imagemSrc cursorPointer text-center figure-img img-fluid" title="Clique para ampliar a foto" alt="{{$advogado['dadosAdvogado']['fotoPrincipal']['tipo']}}"/>
            @else
                <img src="{{asset($advogado['dadosAdvogado']['fotoPrincipal']['nome_arquivo'])}}" style="max-height: 8rem;" class="mx-auto d-block text-center figure-img img-fluid"/>
            @endif
            <div class="card-body">
                <h5 class="card-title">{{$advogado['dadosAdvogado']['nome']}}</h5>
                <p class="card-text">OAB {{$advogado['dadosAdvogado']['oab_numero']}}/{{$advogado['dadosAdvogado']['oab_uf']}}</p>
                <a href="{{ route('advogado.show', $advogado['dadosAdvogado']['id']) }}" class="btn btn-primary">Ver Detalhes</a>
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
                <img id="myImg" src="{{ $advHistorico['fotoAdv'] }}" class="img-thumbnail img-fluid rounded float-left mb-2">

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
                <h5>{{ $advHistorico['nomeAdv'] }}</h5>
                <p>
                    <strong>OAB:</strong> {{ $advHistorico['oab'] }}
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
@endif

@push('script')
<script src="{{ asset('js/interno/_advogados.js') }}"></script>
@endpush