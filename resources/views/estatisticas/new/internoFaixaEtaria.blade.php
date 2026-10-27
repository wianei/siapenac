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
<form action="{{ route('estatisticas.faixaEtaria') }}" method="post" enctype="multipart/form-data">
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
    <input type="hidden" name="tipo" value="internoFaixaEtaria" />
    <input type="hidden" name="internos" value="{{ json_encode($internos) }}" />
    
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
        <h5>Faixa etária
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
                <tr class="">
                    <td>0 a 17</td>
                    <td>
                        @if(isset($internos['017']))
                            @if(isset($internos['017']['M']))
                            {{ count($internos['017']['M']) }}

                            @php 
                                $totalGeralM = $totalGeralM + count($internos['017']['M']); 
                                $totalgeral = $totalgeral + count($internos['017']['M']);
                                $total017 = $total017 + count($internos['017']['M']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>
                        @if(isset($internos['017']))
                            @if(isset($internos['017']['F']))
                            {{ count($internos['017']['F']) }}

                            @php 
                                $totalGeralF = $totalGeralF + count($internos['017']['F']); 
                                $totalgeral = $totalgeral + count($internos['017']['F']);
                                $total017 = $total017 + count($internos['017']['F']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>{{ $total017 }}</td>
                </tr>
                <tr class="">
                    <td>18 a 24</td>
                    <td>
                        @if(isset($internos['1824']))
                            @if(isset($internos['1824']['M']))
                            {{ count($internos['1824']['M']) }}

                            @php 
                                $totalGeralM = $totalGeralM + count($internos['1824']['M']); 
                                $totalgeral = $totalgeral + count($internos['1824']['M']);
                                $total1824 = $total1824 + count($internos['1824']['M']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>
                        @if(isset($internos['1824']))
                            @if(isset($internos['1824']['F']))
                            {{ count($internos['1824']['F']) }}

                            @php 
                                $totalGeralF = $totalGeralF + count($internos['1824']['F']); 
                                $totalgeral = $totalgeral + count($internos['1824']['F']);
                                $total1824 = $total1824 + count($internos['1824']['F']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>{{ $total1824 }}</td>
                </tr>
                <tr class="">
                    <td>25 a 29</td>
                    <td>
                        @if(isset($internos['2529']))
                            @if(isset($internos['2529']['M']))
                            {{ count($internos['2529']['M']) }}

                            @php 
                                $totalGeralM = $totalGeralM + count($internos['2529']['M']); 
                                $totalgeral = $totalgeral + count($internos['2529']['M']);
                                $total2529 = $total2529 + count($internos['2529']['M']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>
                        @if(isset($internos['2529']))
                            @if(isset($internos['2529']['F']))
                            {{ count($internos['2529']['F']) }}

                            @php 
                                $totalGeralF = $totalGeralF + count($internos['2529']['F']); 
                                $totalgeral = $totalgeral + count($internos['2529']['F']);
                                $total2529 = $total2529 + count($internos['2529']['F']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>{{ $total2529 }}</td>
                </tr>
                <tr class="">
                    <td>30 a 34</td>
                    <td>
                        @if(isset($internos['3034']))
                            @if(isset($internos['3034']['M']))
                            {{ count($internos['3034']['M']) }}

                            @php 
                                $totalGeralM = $totalGeralM + count($internos['3034']['M']); 
                                $totalgeral = $totalgeral + count($internos['3034']['M']);
                                $total3034 = $total3034 + count($internos['3034']['M']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>
                        @if(isset($internos['3034']))
                            @if(isset($internos['3034']['F']))
                            {{ count($internos['3034']['F']) }}

                            @php 
                                $totalGeralF = $totalGeralF + count($internos['3034']['F']); 
                                $totalgeral = $totalgeral + count($internos['3034']['F']);
                                $total3034 = $total3034 + count($internos['3034']['F']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>{{ $total3034 }}</td>
                </tr>
                <tr class="">
                    <td>35 a 45</td>
                    <td>
                        @if(isset($internos['3545']))
                            @if(isset($internos['3545']['M']))
                            {{ count($internos['3545']['M']) }}

                            @php 
                                $totalGeralM = $totalGeralM + count($internos['3545']['M']); 
                                $totalgeral = $totalgeral + count($internos['3545']['M']);
                                $total3545 = $total3545 + count($internos['3545']['M']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>
                        @if(isset($internos['3545']))
                            @if(isset($internos['3545']['F']))
                            {{ count($internos['3545']['F']) }}

                            @php 
                                $totalGeralF = $totalGeralF + count($internos['3545']['F']); 
                                $totalgeral = $totalgeral + count($internos['3545']['F']);
                                $total3545 = $total3545 + count($internos['3545']['F']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>{{ $total3545 }}</td>
                </tr>
                <tr class="">
                    <td>46 a 60</td>
                    <td>
                        @if(isset($internos['4660']))
                            @if(isset($internos['4660']['M']))
                            {{ count($internos['4660']['M']) }}

                            @php 
                                $totalGeralM = $totalGeralM + count($internos['4660']['M']); 
                                $totalgeral = $totalgeral + count($internos['4660']['M']);
                                $total4660 = $total4660 + count($internos['4660']['M']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>
                        @if(isset($internos['4660']))
                            @if(isset($internos['4660']['F']))
                            {{ count($internos['4660']['F']) }}

                            @php 
                                $totalGeralF = $totalGeralF + count($internos['4660']['F']); 
                                $totalgeral = $totalgeral + count($internos['4660']['F']);
                                $total4660 = $total4660 + count($internos['4660']['F']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>{{ $total4660 }}</td>
                </tr>
                <tr class="">
                    <td>61 a 70</td>
                    <td>
                        @if(isset($internos['6170']))
                            @if(isset($internos['6170']['M']))
                            {{ count($internos['6170']['M']) }}

                            @php 
                                $totalGeralM = $totalGeralM + count($internos['6170']['M']); 
                                $totalgeral = $totalgeral + count($internos['6170']['M']);
                                $total6170 = $total6170 + count($internos['6170']['M']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>
                        @if(isset($internos['6170']))
                            @if(isset($internos['6170']['F']))
                            {{ count($internos['6170']['F']) }}

                            @php 
                                $totalGeralF = $totalGeralF + count($internos['6170']['F']); 
                                $totalgeral = $totalgeral + count($internos['6170']['F']);
                                $total6170 = $total6170 + count($internos['6170']['F']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>{{ $total6170 }}</td>
                </tr>            
                <tr class="">
                    <td>Mais de 70</td>
                    <td>
                        @if(isset($internos['70']))
                            @if(isset($internos['70']['M']))
                            {{ count($internos['70']['M']) }}

                            @php 
                                $totalGeralM = $totalGeralM + count($internos['70']['M']); 
                                $totalgeral = $totalgeral + count($internos['70']['M']);
                                $totalMaior70 = $totalMaior70 + count($internos['70']['M']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>
                        @if(isset($internos['70']))
                            @if(isset($internos['70']['F']))
                            {{ count($internos['70']['F']) }}

                            @php 
                                $totalGeralF = $totalGeralF + count($internos['70']['F']); 
                                $totalgeral = $totalgeral + count($internos['70']['F']);
                                $totalMaior70 = $totalMaior70 + count($internos['70']['F']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>{{ $totalMaior70 }}</td>
                </tr>
                    <tr class="">
                        <td>Não Informado no SIAPEN</td>
                        <td>
                            @if(isset($internos['ND']))
                                @if(isset($internos['ND']['M']))
                                {{ count($internos['ND']['M']) }}

                                @php 
                                    $totalGeralM = $totalGeralM + count($internos['ND']['M']); 
                                    $totalgeral = $totalgeral + count($internos['ND']['M']);
                                    $totalNd = $totalNd + count($internos['ND']['M']);
                                @endphp

                                @else
                                    0
                                @endif

                                @else
                                    0
                            @endif
                        </td>
                        <td>
                            @if(isset($internos['ND']))
                                @if(isset($internos['ND']['F']))
                                {{ count($internos['ND']['F']) }}

                                @php 
                                    $totalGeralF = $totalGeralF + count($internos['ND']['F']); 
                                    $totalgeral = $totalgeral + count($internos['ND']['F']);
                                    $totalNd = $totalNd + count($internos['ND']['F']);
                                @endphp

                                @else
                                    0
                                @endif

                                @else
                                    0
                            @endif
                        </td>
                        <td>{{ $totalNd }}</td>
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