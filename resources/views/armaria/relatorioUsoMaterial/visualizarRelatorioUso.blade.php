@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-3">
        <h3><i class="mdi mdi-file-document"></i>Visualização de Relatório de Uso de Material</h3>
        <hr>
        <p>
            Visualiza o relatório selecionado.
        </p>
    </div>

    <div class="row">
        <div class="col-sm-6 mt-2">
            Localização:<br>
            <h6 class="float-left">{{ $dados[0]->unidade }}</h6>
        </div>

        <div class="col-sm-6 mt-2">
            Responsável:<br>
            <h6 class="float-left">{{ $dados[0]->servidor }} ({{ $dados[0]->matricula }})</h6>
        </div>
        <div class="col-sm-12 mt-2">
            Equipamento(s):<br>
            @foreach($dados as $dado)
                {{ $dado->textoEquipamento }}<br>
            @endforeach
        </div>

        <div class="col-sm-12 mt-2">
            Relatório:<br>
            {!! $dados[0]->relatorio !!}
        </div>



    </div>

@endsection
@push('css')
<link href="{{ asset('css/armaria/mapa/mapaEquipamento.css') }}" rel="stylesheet">
@endpush

