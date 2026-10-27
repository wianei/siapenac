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

    @include('estatisticas.show._menuEstatisticas')

<div style="display: inline;">
<form action="{{ route('estatisticas.internoCrimes') }}" method="post" enctype="multipart/form-data">
        {!! csrf_field() !!}
          <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="unidade_prisional">Unidade Prisional</label>
                <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">
                    <<option value="{{ $lotacaoServidor }}">Selecione</option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}">
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-sm-3">
                <label for="data">Data</label>
                <input type="text" name="data" class="form-control" id="data" placeholder="Informe a Data" autocomplete="off">
            </div>
        </div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary"><i class="fas fa-redo"></i> Recalcular Estatísticas</button>
</form>
<br>
<br>
<form action="{{ route('estatisticas.internoPdfNew') }}" method="post">
    {!! csrf_field() !!}
    
    <!-- especificos -->
    <input type="hidden" name="tipo" value="internoCrimesSisdepen" />
    <input type="hidden" name="internosCPB" value="{{ json_encode($internosCPB) }}" />
    <input type="hidden" name="internosLeisExtra" value="{{ json_encode($internosLeisExtra) }}" />
    
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
        <h5>Crimes do Código Penal Brasileiro
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                    @foreach($internosCPB as $leiCPB => $artigos)
                        @foreach($artigos as $key => $artigo)
                            
                            @if($leiCPB != "")
                                @php 
                                    $totalGeralF = $totalGeralM = 0; 
                                @endphp
                                <tr class="">
                                    <td>{{ $key }}</td>
                                    <td>
                                        @if(isset($artigo['M']))
                                            {{ count($artigo['M']) }}

                                            @php 
                                                $totalGeralM = $totalGeralM + count($artigo['M']); 
                                            @endphp

                                            @else
                                                0
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($artigo['F']))
                                            {{ count($artigo['F']) }}

                                            @php 
                                                $totalGeralF = $totalGeralF + count($artigo['F']); 
                                            @endphp

                                            @else
                                                0
                                        @endif
                                    </td>
                                    <td>{{ $totalGeralM + $totalGeralF }}</td>
                                </tr>
                                @endif
                        @endforeach
                    @endforeach
            </table>
        </div>

        <br/>

    <div id="resultados">
        <br/>
        <h5>Crimes de Legislações Extravagantes
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                    @foreach($internosLeisExtra as $leiExtra => $artigos)
                        @foreach($artigos as $key => $artigo)
                            
                            @if($leiExtra != "")
                                @php 
                                    $totalGeralF = $totalGeralM = 0; 
                                @endphp
                                <tr class="">
                                    <td>
                                        @if(strlen($key) > 100)
                                            {{ $leiExtra."- ".mb_strimwidth($key, 0, 100)."..." }}
                                        @else
                                            {{ $leiExtra."- ".$key }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($artigo['M']))
                                            {{ count($artigo['M']) }}

                                            @php 
                                                $totalGeralM = $totalGeralM + count($artigo['M']); 
                                            @endphp

                                            @else
                                                0
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($artigo['F']))
                                            {{ count($artigo['F']) }}

                                            @php 
                                                $totalGeralF = $totalGeralF + count($artigo['F']); 
                                            @endphp

                                            @else
                                                0
                                        @endif
                                    </td>
                                    <td>{{ $totalGeralM + $totalGeralF }}</td>
                                </tr>
                                @endif
                        @endforeach
                    @endforeach
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