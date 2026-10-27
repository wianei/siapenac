
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
        $totalSentenciado = $totalgeral =  $totalProvisorio = 0;
    @endphp

    @include('estatisticas.show._menuEstatisticas')


<div style="display: inline;">
<form action="{{ route('estatisticas.medidaSeguranca') }}" method="post" enctype="multipart/form-data">
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
    <input type="hidden" name="tipo" value="internoMedSeg" />
    <input type="hidden" name="internos" value="{{ json_encode($internos) }}" />
    <input type="hidden" name="detalhesUnidades" value="{{ json_encode($detalhesUnidades) }}" />
    <input type="hidden" name="qtdMedSegM" value="{{ $qtdMedSegM }}" />
    <input type="hidden" name="qtdMedSegF" value="{{ $qtdMedSegF }}" />
    
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
                    <td>Sentenciados</td>
                    <td>
                        @if(isset($internos['S']))
                            @if(isset($internos['S']['M']))
                            {{ count($internos['S']['M']) }}

                            @php 
                                $totalSentenciado = $totalSentenciado + count($internos['S']['M']); 
                                $totalgeral = $totalgeral + count($internos['S']['M']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>
                        @if(isset($internos['S']))
                            @if(isset($internos['S']['F']))
                            {{ count($internos['S']['F']) }}

                            @php 
                                $totalSentenciado = $totalSentenciado + count($internos['S']['F']); 
                                $totalgeral = $totalgeral + count($internos['S']['F']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>{{ $totalSentenciado }}</td>
                </tr>
                <tr class="">
                    <td>Provisório</td>
                    <td>
                        @if(isset($internos['N']))
                            @if(isset($internos['N']['M']))
                            {{ count($internos['N']['M']) }}

                            @php 
                                $totalProvisorio = $totalProvisorio + count($internos['N']['M']); 
                                $totalgeral = $totalgeral + count($internos['N']['M']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>
                        @if(isset($internos['N']))
                            @if(isset($internos['N']['F']))
                            {{ count($internos['N']['F']) }}

                            @php 
                                $totalProvisorio = $totalProvisorio + count($internos['N']['F']); 
                                $totalgeral = $totalgeral + count($internos['N']['F']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>{{ $totalProvisorio }}</td>
                </tr>
            </table>
        </div>

        <br/>

    </div>
    @endif
    
    @if($detalhesUnidades)
    <div id="resultados">
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">Classificação</th>
                    <th scope="col">Quantidade</th>
                </tr>
                <tbody>
                <tr class="">
                    <td>Homens</td>
                    <td>{{ $qtdMedSegM }}</td>
                </tr>
                <tr class="">
                    <td>Mulheres</td>
                    <td>{{ $qtdMedSegF }}</td>
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