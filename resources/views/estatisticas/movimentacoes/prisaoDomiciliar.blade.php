@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-table"></i> Estatísticas do {{ config('app.sistema_estado') }} - Tabelas</h3>
        <hr>
        <p>
            Tabelas com dados estatísticos relacionados aos internos ativos, atualizados em tempo real, relativos ao {{ config('app.sistema_estado') }}, a confiabilidade dos dados abaixo depende da inserção manual deles nos perfis de cada interno, portanto pode haver eventuais inconsistências.
        </p>
    </div>

    @php

    $quantidade = 0;
    $totalgeral = $totalGeralF = $totalGeralM  = $total017 = $total1824 = $total2529 = $total3034 = $total3545 =  $total4660 = $total6170 = $totalMaior70 = $totalNd = 0;
    $maxValue = 0;

    @endphp

    @include('estatisticas.movimentacoes._includes._menu')

<div style="display: inline;">
<form action="{{ route('estatisticas.movimentacoes', 'prisaoDomiciliar') }}" method="post" enctype="multipart/form-data">
        {!! csrf_field() !!}
          <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="unidade_prisional">Unidade Prisional</label>
                <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">
                    <<option value="">Selecione</option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}">
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-sm-3">
                <label for="data">Data Inicial</label>
                <input type="text" name="data" class="form-control" id="data" placeholder="Informe a Data" autocomplete="off">
            </div>
            <div class="form-group col-sm-3">
                <label for="dataFinal">Data Final</label>
                <input type="text" name="dataFinal" class="form-control" id="dataFinal" placeholder="Informe a Data" autocomplete="off">
            </div>
        </div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary"><i class="fas fa-redo"></i> Recalcular Estatísticas</button>
</form>
<br>
<br>
<form action="{{ route('estatisticas.movimentacoesPdfs') }}" method="post">
    {!! csrf_field() !!}
    
    <!-- especificos -->
    <input type="hidden" name="titulo" value="Prisão Domiciliar" />
    <input type="hidden" name="tipo" value="prisaoDomiciliar" />
    <input type="hidden" name="movimentacoes" value="{{ json_encode($movimentacoes) }}" />
    
    <!-- Geral -->
    <input type="hidden" name="unidadeEstatistica" value="{{ $unidadeEstatistica }}" />
    <input type="hidden" name="dataEstatistica" value="{{ $dataEstatistica }}" />
    
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-print"></i> 
        Imprimir Estatística
    </button>
</form>
</div>


    <hr/>

    <h4>Informações estatísticas - {{ $unidadeEstatistica }} em {{ $dataEstatistica }}</h4>

    <div id="resultados">
        <br/>
        <h5>Prisão Domiciliar
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Unidade</th>
                <th scope="col">Total</th>
                <tbody>
                    @foreach($movimentacoes as $tipo => $movimentacao)
                        @if($tipo != "")
                            <tr class="">
                                <td>{{ $tipo }}</td>
                                <td>
                                    {{ count($movimentacoes[$tipo]) }}
                                </td>
                            </tr>
                            @php
                                $totalgeral = $totalgeral + count($movimentacoes[$tipo]);
                            @endphp
                            
                            @endif
                    @endforeach
                    <tr>
                        <td>TOTAL</td>
                        <td>{{ $totalgeral }}</td>
                    </tr>
            </table>
        </div>

        <br/>

    </div>

    <a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
@endsection
@push('css')
    <link href="{{ asset('css/unidades/show.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush
@push('script')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ asset('js/estatisticas/buscar.js') }}"></script>
@endpush



