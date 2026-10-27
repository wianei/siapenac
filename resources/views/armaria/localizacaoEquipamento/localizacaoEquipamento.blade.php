@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-3">
        <h3><i class="mdi mdi-map-marker-radius"></i>Localização de Equipamento</h3>
        <hr>
        <p>
            Localiza o equipamento.
        </p>
    </div>

    <div class="row">
        <div class="col-sm-12">
            Equipamento:<br>
            @if($dado[0]->equipamento_tipo == "armaFogo")
            <h6 class="float-left">{{ strtoupper($dado[0]->equipamento_especificacao). " - Calibre: .".$dado[0]->calibre." - Modelo: ".$dado[0]->modelo." | Fabricante: ".$dado[0]->fabNome." | Nº de Série: ".$dado[0]->n_serie }}</h6>

            @elseif($dado[0]->equipamento_tipo == "colete")
                <h6 class="float-left">{{ strtoupper($dado[0]->equipamento_tipo). " - Tamanho: ".$dado[0]->tamanho." - | Fabricante: ".$dado[0]->fabNome." | Nº de Série: ".$dado[0]->n_serie }}</h6>
            @endif
        </div>

        <div class="col-sm-12">
            Localização:<br>
            <h6 class="float-left">{{ $dado[0]->nomeUnidade }}</h6>
        </div>

        <div class="col-sm-12">
            Responsável:<br>
            <h6 class="float-left">{{ $dado[0]->responsavel }}</h6>
        </div>

    </div>

@endsection
@push('css')
<link href="{{ asset('css/armaria/mapa/mapaEquipamento.css') }}" rel="stylesheet">
@endpush

