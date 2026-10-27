<head>
    <meta charset="UTF-8">
    <title>Faltas por Gravidade - Estatísticas SIAPEN</title>

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
    $totalgeral = $totalGeralF = $totalGeralM  = $total017 = $total1824 = $total2529 = $total3034 = $total3545 =  $total4660 = $total6170 = $totalMaior70 = $totalNd = 0;
    $maxValue = 0;

    @endphp
    
    <br>
    <br>
<h3>Informações estatísticas - {{ $unidadeEstatistica }} em {{ $dataEstatistica }}</h3>

    <div id="resultados">
        <br/>
        <h3>Faltas por Status da Sindicância
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h3>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Unidade</th>
                <th scope="col">Aberta</th>
                <th scope="col">Protocolada</th>
                <th scope="col">Homologada</th>
                <th scope="col">Total</th>
                <tbody>
                    @foreach($faltas as $unidade => $falta)
                        @if($unidade != "")
                            @php 
                                $totalGeralA = $totalGeralP = $totalGeralH = 0; 
                            @endphp
                            <tr class="">
                                <td>{{ $unidade }}</td>
                                <td>
                                    @if(isset($falta['Aberta']))
                                        {{ count($falta['Aberta']) }}

                                        @php 
                                            $totalGeralA = $totalGeralA + count($falta['Aberta']); 
                                            $totalgeral = $totalgeral + $totalGeralA;
                                        @endphp

                                        @else
                                            0
                                    @endif
                                </td>
                                <td>
                                    @if(isset($falta['Protocolada']))
                                        {{ count($falta['Protocolada']) }}

                                        @php 
                                            $totalGeralP = $totalGeralP + count($falta['Protocolada']);
                                            $totalgeral = $totalgeral + $totalGeralP;
                                        @endphp

                                        @else
                                            0
                                    @endif
                                </td>
                                <td>
                                    @if(isset($falta['Homologada']))
                                        {{ count($falta['Homologada']) }}

                                        @php 
                                            $totalGeralH = $totalGeralH + count($falta['Homologada']);
                                            $totalgeral = $totalgeral + $totalGeralH;
                                        @endphp

                                        @else
                                            0
                                    @endif
                                </td>
                                <td>{{ $totalGeralA + $totalGeralP + $totalGeralH }}</td>
                            </tr>
                            @endif
                    @endforeach
                    <tr>
                        <td>TOTAL</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ $totalgeral }}</td>
                    </tr>
            </table>
        </div>

        <br/>

    </div>

</body>