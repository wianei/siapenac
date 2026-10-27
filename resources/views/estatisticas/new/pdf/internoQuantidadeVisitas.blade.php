<head>
    <meta charset="UTF-8">
    <title>Quantidade de Visitas - Estatísticas SIAPEN</title>

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
    $totalgeral = $totalGeralF = $totalGeralM = $somaGeralF = $somaGeralM  = $total017 = $total1824 = $total2529 = $total3034 = $total3545 =  $total4660 = $total6170 = $totalMaior70 = $totalNd = 0;
    $maxValue = 0;

    @endphp
    
    <br>
    <br>
<h3>Informações estatísticas - {{ $unidadeEstatistica }} em {{ $dataEstatistica }}</h3>

    <div id="resultados">
        <br/>
        <h3>Quantidade de visitas registradas
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h3>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Unidade</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                    @foreach($internos as $unidade => $interno)
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
                                            $somaGeralM = $somaGeralM + count($interno['M']); 
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
                                            $somaGeralF = $somaGeralF + count($interno['F']); 
                                            
                                        @endphp

                                        @else
                                            0
                                    @endif
                                </td>
                                <td>{{ $totalGeralM + $totalGeralF }}</td>
                                @php
                                    $totalgeral = $totalgeral + $totalGeralM + $totalGeralF;
                                @endphp
                            </tr>
                            @endif
                    @endforeach
                    <tr>
                        <td>TOTAL</td>
                        <td>{{ $somaGeralM }}</td>
                        <td>{{ $somaGeralF }}</td>
                        <td>{{ $totalgeral }}</td>
                    </tr>
            </table>
        </div>

        <br/>

    </div>

</body>