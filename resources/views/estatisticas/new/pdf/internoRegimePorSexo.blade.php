<head>
    <meta charset="UTF-8">
    <title>Regime por Sexo - Estatísticas SIAPEN</title>

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

        $totalfechado = $totalgeral =  $totalsemiaberto = $totalaberto = 0;

    @endphp
    <br>
    <br>
<h3>Informações estatísticas - {{ $unidadeEstatistica }} em {{ $dataEstatistica }}</h3>

    @if($internos)
    <div id="resultados">
        <h3>Regime prisional
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h3>

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
        <h3>Regime prisional
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h3>

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
        
</body>



