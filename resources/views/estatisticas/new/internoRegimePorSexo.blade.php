@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-table"></i> Estatísticas do {{ config('app.sistema_estado') }}- Tabelas</h3>
        <hr>
        <p>
            Tabelas com dados estatísticos relacionados aos internos ativos, atualizados em tempo real, relativos ao {{ config('app.sistema_estado') }}, a confiabilidade dos dados abaixo depende da inserção manual deles nos perfis de cada interno, portanto pode haver eventuais inconsistências.
        </p>
    </div>

    @php
        $totalfechado = $totalgeral =  $totalsemiaberto = $totalaberto = 0;
    @endphp

    @include('estatisticas.show._menuEstatisticas')


<div style="display: inline;">
<form action="{{ route('estatisticas.regimePrisional') }}" method="post" enctype="multipart/form-data">
        {!! csrf_field() !!}
          <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="unidade_prisional">Unidade Prisional</label>
                <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">
                    <option value="">
                        Todas
                    </option>
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
    <input type="hidden" name="tipo" value="internoRegimePorSexo" />
    <input type="hidden" name="internos" value="{{ json_encode($internos) }}" />
    <input type="hidden" name="detalhesUnidades" value="{{ json_encode($detalhesUnidades) }}" />
    <input type="hidden" name="qtdRegimeFechadoH" value="{{ ($qtdRegimeFechadoH) }}" />
    <input type="hidden" name="qtdRegimeFechadoM" value="{{ ($qtdRegimeFechadoM) }}" />
    <input type="hidden" name="qtdRegimeSemiabertoH" value="{{ ($qtdRegimeSemiabertoH) }}" />
    <input type="hidden" name="qtdRegimeSemiabertoM" value="{{ ($qtdRegimeSemiabertoM) }}" />
    <input type="hidden" name="qtdRegimeAbertoH" value="{{ ($qtdRegimeAbertoH) }}" />
    <input type="hidden" name="qtdRegimeAbertoM" value="{{ ($qtdRegimeAbertoM) }}" />
    <input type="hidden" name="qtdRegimeNIH" value="{{ ($qtdRegimeNIH) }}" />
    <input type="hidden" name="qtdRegimeNIM" value="{{ ($qtdRegimeNIM) }}" />
    
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
    <br>
    
    @if($internos)
    <div id="resultados">
        <h5>Regime prisional
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>

        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">Classificação</th>
                    <th scope="col">Homens</th>
                    <th scope="col">Mulheres</th>
                    <th scope="col">Total</th>
                </tr>
                <tbody>
                <tr class="">
                    <td>Regime Fechado</td>
                    <td>
                        @if(isset($internos['FECHADO']['M']))
                        {{ count($internos['FECHADO']['M']) }}

                        @php 
                            $totalfechado = $totalfechado + count($internos['FECHADO']['M']); 
                            $totalgeral = $totalgeral + count($internos['FECHADO']['M']);
                        @endphp

                        @else
                        0
                        @endif
                    </td>
                    <td>
                        @if(isset($internos['FECHADO']['F']))
                        {{ count($internos['FECHADO']['F']) }}

                        @php 
                            $totalfechado = $totalfechado + count($internos['FECHADO']['F']); 
                            $totalgeral = $totalgeral + count($internos['FECHADO']['F']);
                        @endphp

                        @else
                        0
                        @endif
                    </td>
                    <td>{{ $totalfechado }}</td>
                </tr>
                <tr class="">
                    <td>Regime Semiaberto</td>
                    <td>
                        @if(isset($internos['SEMIABERTO']['M']))
                        {{ count($internos['SEMIABERTO']['M']) }}

                        @php 
                            $totalsemiaberto = $totalsemiaberto + count($internos['SEMIABERTO']['M']); 
                            $totalgeral = $totalgeral + count($internos['SEMIABERTO']['M']);
                        @endphp

                        @else
                        0
                        @endif
                    </td>
                    <td>
                        @if(isset($internos['SEMIABERTO']['F']))
                        {{ count($internos['SEMIABERTO']['F']) }}

                        @php 
                            $totalsemiaberto = $totalsemiaberto + count($internos['SEMIABERTO']['F']); 
                            $totalgeral = $totalgeral + count($internos['SEMIABERTO']['F']);
                        @endphp

                        @else
                        0
                        @endif
                    </td>
                    <td>{{ $totalsemiaberto }}</td>
                </tr>
                <tr class="">
                    <td>Regime Aberto</td>
                    <td>
                        @if(isset($internos['ABERTO']['M']))
                        {{ count($internos['ABERTO']['M']) }}

                        @php 
                            $totalaberto = $totalaberto + count($internos['ABERTO']['M']); 
                            $totalgeral = $totalgeral + count($internos['ABERTO']['M']);
                        @endphp

                        @else
                        0
                        @endif
                    </td>
                    <td>
                        @if(isset($internos['ABERTO']['F']))
                        {{ count($internos['ABERTO']['F']) }}

                        @php 
                            $totalaberto = $totalaberto + count($internos['ABERTO']['F']); 
                            $totalgeral = $totalgeral + count($internos['ABERTO']['F']);
                        @endphp

                        @else
                        0
                        @endif
                    </td>
                    <td>{{ $totalaberto }}</td>
                </tr>
            </table>
        </div>

        <br/>

    </div>
    @endif
    
    @if($detalhesUnidades)
    <div id="resultados">
        <h5>Regime prisional
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>

        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">Classificação</th>
                    <th scope="col">Homens</th>
                    <th scope="col">Mulheres</th>
                    <th scope="col">Total</th>
                </tr>
                <tbody>
                <tr class="">
                    <td>Regime Fechado</td>
                    <td>{{ $qtdRegimeFechadoH }}</td>
                    <td>{{ $qtdRegimeFechadoM }}</td>
                    <td>{{ $qtdRegimeFechadoH + $qtdRegimeFechadoM }}</td>
                </tr>
                <tr class="">
                    <td>Regime Semiaberto</td>
                    <td>{{ $qtdRegimeSemiabertoH }}</td>
                    <td>{{ $qtdRegimeSemiabertoM }}</td>
                    <td>{{ $qtdRegimeSemiabertoH + $qtdRegimeSemiabertoM }}</td>
                </tr>
                <tr class="">
                    <td>Regime Aberto</td>
                    <td>{{ $qtdRegimeAbertoH }}</td>
                    <td>{{ $qtdRegimeAbertoM }}</td>
                    <td>{{ $qtdRegimeAbertoH + $qtdRegimeAbertoM }}</td>
                </tr>
                @if($qtdRegimeNIH + $qtdRegimeNIM != 0)
                    <tr class="">
                        <td>Regime Não Informado no SIAPEN</td>
                        <td>{{ $qtdRegimeNIH }}</td>
                        <td>{{ $qtdRegimeNIM }}</td>
                        <td>{{ $qtdRegimeNIH + $qtdRegimeNIM }}</td>
                    </tr>
                @endif
            </table>
        </div>

        <br/>

    </div>
    @endif
    
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