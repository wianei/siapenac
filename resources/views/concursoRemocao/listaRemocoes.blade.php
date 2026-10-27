@extends('template.tema_geral.theme')

@section('content')
    <style type="text/css">

        .tableT3 tr:nth-child(even) {background: #FFF !important;}
        .tableT3 tr:nth-child(odd) {background: #f2f8f9 !important;}

        .tableT2 tr:nth-child(even) {background: #FFF !important;}
        .tableT2 tr:nth-child(odd) {background: #f2f8f9 !important;}

        .tableT1 tr:nth-child(even) {background: #FFF !important;}
        .tableT1 tr:nth-child(odd) {background: #f2f8f9 !important;}

    </style>
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-playlist-check"></i> Lista de Remoções</h3>
        <hr>
        <p>
            Lista as remoções realizadas a partir das opções fornecidas pelos servidores.
        </p>
    </div>

    <a href="{{ route('concursoRemocao.listaRemocoesPdf',"unidade") }}" class="btn btn-primary">
        Gerar PDF por Unidade
    </a>
    <a href="{{ route('concursoRemocao.listaRemocoesPdf',"servidores") }}" class="btn btn-primary">
        Gerar PDF dos servidores
    </a>
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg)  !!}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    @foreach($unidades as $unidade)
        @if($unidade->qtdServidores > 0)
            <h4 class="mt-3">Unidade: {{ $unidade->nomeUnidade }}</h4>
            <h6 class="mt-3">Total de servidores removidos: {{ $unidade->qtdServidores }}</h6>
            <div class="table-responsive-md mt-3">
                <table class="table mb-3">
                    <thead class="background_30516f color_FFF">
                    <tr class="">
                        <th scope="col">#</th>
                        <th scope="col">Servidor</th>
                        <th scope="col">Matrícula</th>
                        <th scope="col">Lot. Atual</th>
                        <th scope="col">Lot. Nova</th>
                        <th scope="col">Turma</th>
                        <th scope="col">Opção</th>
                    </tr>
                    </thead>
                    <tbody class="tableT3">
                    @php
                    $i = 1;
                    @endphp

                    @foreach($servidores as $servidor)
                        @if($servidor->idLotacaoRemocao == $unidade->idUnidade)

                            <tr class="">
                                <td class="border1pxSolidRGBA0125 fontSize-11px">{{ $i }}</td>
                                <td class="border1pxSolidRGBA0125 fontSize-11px">{{ $servidor->nomeServidor }}</td>
                                <td class="border1pxSolidRGBA0125 fontSize-11px">{{ $servidor->matriculaServidor }}</td>
                                <td class="border1pxSolidRGBA0125 fontSize-11px">{{ $servidor->lotacaoAnterior }}</td>
                                <td class="border1pxSolidRGBA0125 fontSize-11px">{{ $servidor->lotacaoRemocao }}</td>
                                <td class="border1pxSolidRGBA0125 fontSize-11px">{{ $servidor->turma }}ª</td>
                                <td class="border1pxSolidRGBA0125 fontSize-11px">{{ $servidor->ordemEscolha }}ª</td>
                            </tr>
                            @php
                            $i++;
                            @endphp

                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <br>
    @endforeach


@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endpush