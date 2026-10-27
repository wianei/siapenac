@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-3">
        <h3><i class="mdi mdi-information"></i>Informação da Descautela</h3>
        <hr>
        <p>
            Dados da descutela de equipamento(s).
        </p>
    </div>


    <div class="row">
        <div class="col-sm-6">
            Responsável:<br>
            @if($dadosCautela['tipo'] != "outros")
                <h6 class="float-left">{{ $dadosServidor[0]->nome." (".$dadosServidor[0]->matricula.")" }}</h6>
            @else
                <h6 class="float-left">{{ $dadosOutros[0]->nome." (".$dadosOutros[0]->cpf.") - Órgão: ".$dadosOutros[0]->orgao }}</h6>
            @endif
        </div>

        <div class="col-sm-6">
            Período<br>
            @if($dadosCautela['tempo_indeterminado'] == "S")
                <h6 class="float-left">Tempo Indeterminado</h6>
            @else
                De: {{ $dadosCautela['dataInicial'] }} Até: {{ $dadosCautela['dataFinal'] }}
            @endif
        </div>

        @if($dadosCautela['unidade'] != "")
            <div class="col-sm-12">
                Localização:<br>
                <h6 class="float-left">{{ $dadosCautela['unidade'] }}</h6>
            </div>
        @endif

        <div class="col-sm-12">
            Equipamento(s):<br>

            <ul>
                @foreach($dadosEquipamentoCautela as $item)
                    <li>{{ $item['equipamento'] }}</li>
                @endforeach
            </ul>

        </div>


        <div class="col-sm-12">
            Observação:<br>
            <h6 class="float-left">{{ $dadosCautela['obs'] }}</h6>
        </div>


    </div>

@endsection
@push('css')
<link href="{{ asset('css/armaria/mapa/mapaEquipamento.css') }}" rel="stylesheet">
@endpush

