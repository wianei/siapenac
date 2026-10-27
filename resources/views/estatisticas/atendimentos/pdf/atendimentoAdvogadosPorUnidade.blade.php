<head>
    <meta charset="UTF-8">
    <title>Atendimentos de Advogado por Unidade - Estatísticas SIAPEN</title>

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
    $totalgeral = $totalGeralF = $totalGeralM  = $total017 = $total1824 = $total2529 = $total3034 = $total3545 =  $total4660 = $total6170 = $totalMaior70 = $totalNd = $geralM = $geralF = 0;
    $maxValue = 0;

    @endphp
    
    <br>
    <br>
<h3>Informações estatísticas - {{ $unidadeEstatistica }} em {{ $dataEstatistica }}</h3>

    <div id="resultados">
        <br/>
        <h3>Atendimentos de Advogados por Unidade
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h3>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                    @foreach($atendimentos as $unidade => $interno)
                        @if($unidade != "")
                            @php 
                                $totalGeralF = $totalGeralM = 0; 
                            @endphp
                            <tr class="">
                                <td>{{ $unidade }}</td>
                                <td>
                                    @if(isset($interno['M']))
                                        {{ count($interno['M']) }}

                                        @php 
                                            $totalGeralM = $totalGeralM + count($interno['M']); 
                                            $geralM = $geralM + $totalGeralM;
                                        @endphp

                                        @else
                                            0
                                    @endif
                                </td>
                                <td>
                                    @if(isset($interno['F']))
                                        {{ count($interno['F']) }}

                                        @php 
                                            $totalGeralF = $totalGeralF + count($interno['F']); 
                                            $geralF = $geralF + $totalGeralF;
                                        @endphp

                                        @else
                                            0
                                    @endif
                                </td>
                                <td>{{ $totalGeralM + $totalGeralF }}</td>
                            </tr>
                            @endif
                    @endforeach
                    <tr>
                        <td>TOTAL</td>
                        <td>{{ $geralM }}</td>
                        <td>{{ $geralF }}</td>
                        <td>{{ $geralM + $geralF }}</td>
                    </tr>
            </table>
        </div>

        <br/>

    </div>

</body>