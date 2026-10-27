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

    @endphp

    @include('estatisticas.show._menuEstatisticas')


<div style="display: inline;">
<form action="{{ route('estatisticas.regimeUnidade') }}" method="post" enctype="multipart/form-data">
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
    <input type="hidden" name="tipo" value="internoRegimePorUnidade" />
    <input type="hidden" name="unidades" value="{{ json_encode($unidades) }}" />
    <input type="hidden" name="internos" value="{{ json_encode($internos) }}" />
    <input type="hidden" name="detalhesUnidades" value="{{ json_encode($detalhesUnidades) }}" />
    
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

    @if(!is_null($internos))
    
        <div id="resultados">
            <h5>Regime por Unidade
                <i class="mdi mdi-arrow-bottom-right"></i>
            </h5>
            <div class="table-responsive-md">
                <table class="table">
                    <thead class="background_30516f color_FFF">
                    <th scope="col">Unidade</th>
                    <th scope="col">ABERTO</th>
                    <th scope="col">SEMIABERTO</th>
                    <th scope="col">FECHADO</th>
                    <th scope="col">PROVISÓRIO</th>
                    <th scope="col">SENTENCIADO</th>
                    <th scope="col">MED. DE SEG.</th>
                    <th scope="col">PROVI. EM MED. SEG.</th>
                    <th scope="col">NÃO DETECTADO</th>
                    <th scope="col">TOTAL</th>
                    <tbody>
                    @php
                    $totalaberto =
                    $totalsemiaberto =
                    $totalfechado =
                    $totalprovisorio =
                    $totalsentenciado =
                    $totalmedida_seg =
                    $totalmedida_seg_provisorio =
                    $totalinconsistente =
                    $totalgeral = 0;
                    @endphp
                    
                    @foreach($unidades as $unidade)
                        @foreach($internos as $sigla => $interno)
                        @if($unidade->sigla == $sigla)
                        <tr class="">
                            <td>{{ $sigla }}</td>
                            <td>
                                @if(isset($interno['ABERTO']))
                                {{ count($interno['ABERTO']) }}
                                
                                @php 
                                    $totalaberto = $totalaberto + count($interno['ABERTO']); 
                                    $totalgeral = $totalgeral + count($interno['ABERTO']);
                                @endphp
                                
                                @else
                                0
                                @endif
                            </td>
                            <td>
                                @if(isset($interno['SEMIABERTO']))
                                {{ count($interno['SEMIABERTO']) }}
                                
                                @php 
                                    $totalsemiaberto = $totalsemiaberto + count($interno['SEMIABERTO']); 
                                    $totalgeral = $totalgeral + count($interno['SEMIABERTO']);
                                @endphp
                                
                                @else
                                0
                                @endif
                            </td>
                            <td>
                                @if(isset($interno['FECHADO']))
                                {{ count($interno['FECHADO']) }}
                                
                                @php 
                                    $totalfechado = $totalfechado + count($interno['FECHADO']); 
                                    $totalgeral = $totalgeral + count($interno['FECHADO']);
                                @endphp
                                
                                @else
                                0
                                @endif
                            </td>
                            <td>
                                @if(isset($interno['PROVISORIO']))
                                {{ count($interno['PROVISORIO']) }}
                                
                                @php 
                                    $totalprovisorio = $totalprovisorio + count($interno['PROVISORIO']); 
                                    $totalgeral = $totalgeral + count($interno['PROVISORIO']);
                                @endphp
                                
                                @else
                                0
                                @endif
                            </td>
                            <td>
                                {{ $totalfechado +  $totalsemiaberto + $totalaberto }}
                                @php
                                $totalsentenciado = $totalsentenciado + $totalfechado +  $totalsemiaberto + $totalaberto;
                                @endphp
                            </td>
                            <td>
                                @if(isset($interno['MEDIDASEGURANCA']))
                                {{ count($interno['MEDIDASEGURANCA']) }}
                                
                                @php 
                                    $totalmedida_seg = $totalmedida_seg + count($interno['MEDIDASEGURANCA']); 
                                    $totalgeral = $totalgeral + count($interno['MEDIDASEGURANCA']);
                                @endphp
                                
                                @else
                                0
                                @endif
                            </td>
                            <td>
                                0
                            </td>
                            <td>
                                
                                @if(isset($interno['ND']))
                                {{ count($interno['ND']) }}
                                
                                @php 
                                    $totalinconsistente = $totalinconsistente + count($interno['ND']); 
                                    $totalgeral = $totalgeral + count($interno['ND']);
                                @endphp
                                
                                @else
                                0
                                @endif
                            </td>
                            <td>
                                {{$totalgeral}}
                            </td>
                        </tr>
                        @endif
                    @endforeach
                    @endforeach
                    <tr>
                        <td>TOTAL GERAL</td>
                        <td>{{$totalaberto}}</td>
                        <td>{{$totalsemiaberto}}</td>
                        <td>{{$totalfechado}}</td>
                        <td>{{$totalprovisorio}}</td>
                        <td>{{$totalsentenciado}}</td>
                        <td>{{$totalmedida_seg}}</td>
                        <td>0</td>
                        <td>{{$totalinconsistente}}</td>
                        <td>{{$totalgeral}}</td>
                    </tr>
                </table>
            </div>

            <br/>

        </div>
    
    @endif
    
    @if(!is_null($detalhesUnidades))
        <div id="resultados">
            <h5>Regime por Unidade
                <i class="mdi mdi-arrow-bottom-right"></i>
            </h5>
            <div class="table-responsive-md">
                <table class="table">
                    <thead class="background_30516f color_FFF">
                    <th scope="col">Unidade</th>
                    <th scope="col">ABERTO</th>
                    <th scope="col">SEMIABERTO</th>
                    <th scope="col">FECHADO</th>
                    <th scope="col">PROVISÓRIO</th>
                    <th scope="col">SENTENCIADO</th>
                    <th scope="col">MED. DE SEG.</th>
                    <th scope="col">PROVI. EM MED. SEG.</th>
                    <th scope="col">NÃO DETECTADO</th>
                    <th scope="col">TOTAL</th>
                    <tbody>
                    @php
                    $totalaberto =
                    $totalsemiaberto =
                    $totalfechado =
                    $totalprovisorio =
                    $totalsentenciado =
                    $totalmedida_seg =
                    $totalmedida_seg_provisorio =
                    $totalinconsistente =
                    $totalgeral = 0;
                    @endphp
                    @foreach($detalhesUnidades as $unidade)
                        <tr class="">
                            <td>{{$unidade->sigla}}</td>
                            <td>
                                {{$unidade->aberto}}
                                @php
                                $totalaberto = $totalaberto + $unidade->aberto;
                                @endphp
                            </td>
                            <td>
                                {{$unidade->semiaberto}}
                                @php
                                $totalsemiaberto = $totalsemiaberto + $unidade->semiaberto;
                                @endphp
                            </td>
                            <td>
                                {{$unidade->fechado}}
                                @php
                                $totalfechado = $totalfechado + $unidade->fechado;
                                @endphp
                            </td>
                            <td>
                                {{$unidade->provisorio}}
                                @php
                                $totalprovisorio = $totalprovisorio + $unidade->provisorio;
                                @endphp
                            </td>
                            <td>
                                {{$unidade->aberto + $unidade->semiaberto + $unidade->fechado}}
                                @php
                                $totalsentenciado = $unidade->aberto + $unidade->semiaberto + $unidade->fechado;
                                @endphp
                            </td>
                            <td>
                                {{$unidade->medida_seg}}
                                @php
                                $totalmedida_seg = $totalmedida_seg + $unidade->medida_seg;
                                @endphp
                            </td>
                            <td>
                                {{$unidade->medida_seg_provisorio}}
                                @php
                                $totalmedida_seg_provisorio = $totalmedida_seg_provisorio + $unidade->medida_seg_provisorio;
                                @endphp
                            </td>
                            <td>
                                {{$unidade->total - $unidade->provisorio - ($unidade->aberto + $unidade->semiaberto + $unidade->fechado + $unidade->medida_seg)}}
                                @php
                                $totalinconsistente = $unidade->total - $unidade->provisorio - ($unidade->aberto + $unidade->semiaberto + $unidade->fechado + $unidade->medida_seg);
                                @endphp
                            </td>
                            <td>
                                {{$unidade->total}}
                                @php
                                $totalgeral = $totalgeral + $unidade->total;
                                @endphp
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td>TOTAL GERAL</td>
                        <td>{{$totalaberto}}</td>
                        <td>{{$totalsemiaberto}}</td>
                        <td>{{$totalfechado}}</td>
                        <td>{{$totalprovisorio}}</td>
                        <td>{{$totalsentenciado}}</td>
                        <td>{{$totalmedida_seg}}</td>
                        <td>{{$totalmedida_seg_provisorio}}</td>
                        <td>{{$totalinconsistente}}</td>
                        <td>{{$totalgeral}}</td>
                    </tr>
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