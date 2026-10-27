<head>
    <meta charset="UTF-8">
    <title>Regime por Unidade - Estatísticas SIAPEN</title>

    <style type="text/css">
    thead { display: table-header-group }
    tfoot { display: table-row-group }
    tr { page-break-inside: avoid }

        h5{
            font-size:30px;
        }
        table{
            border-collapse: collapse;
            width: 100% !important;
        }
        tr{
            border-bottom: 1px solid #000000;
        }
        th, td{
            padding-right: 20px;
        }
        table td:not(:first-child)
        {
            text-align: center
        }
        table td:first-child{
            text-align: left;
        }
        table th:first-child{
            text-align: left;
        }
        table th{
            background: #000;
            color: #FFF;
        }
    </style>

</head>
<body>
    @php

    $quantidade = 0;

    $maxValue = 0;

    @endphp
    <br>
    <br>
<h3>Informações estatísticas - {{ $unidadeEstatistica }} em {{ $dataEstatistica }}</h3>

    <br>

    @if(!is_null($internos))
    
        <div id="resultados">
            <h3>Regime por Unidade
                <i class="mdi mdi-arrow-bottom-right"></i>
            </h3>
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
            <h3>
                Regime por Unidade
            </h3>
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
        
</body>

