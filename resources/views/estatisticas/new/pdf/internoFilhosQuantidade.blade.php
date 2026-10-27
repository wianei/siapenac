<head>
    <meta charset="UTF-8">
    <title>Filhos por Quantidade - Estatísticas SIAPEN</title>

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
        <h5>Pessoas privadas de liberdade por número de filhos
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Quantidade</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                    @foreach($internos as $quantidade => $interno)
                        @if($quantidade != "")
                            @php 
                                $totalGeralF = $totalGeralM = 0; 
                            @endphp
                            <tr class="">
                                <td>
                                    @if($quantidade <= 1) 
                                        {{ str_pad($quantidade , 2 , '0' , STR_PAD_LEFT)." Filho" }} 
                                        @else
                                            {{ str_pad($quantidade , 2 , '0' , STR_PAD_LEFT)." Filhos" }} 
                                    @endif
                                    
                                </td>
                                <td>
                                    @if(isset($interno['M']))
                                        {{ count($interno['M']) }}

                                        @php 
                                            $totalGeralM = $totalGeralM + count($interno['M']); 
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
                                        @endphp

                                        @else
                                            0
                                    @endif
                                </td>
                                <td>{{ $totalGeralM + $totalGeralF }}</td>
                            </tr>
                            @endif
                    @endforeach
            </table>
        </div>

        <br/>

    </div>

</body>