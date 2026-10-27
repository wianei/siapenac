<head>
    <meta charset="UTF-8">
    <title>Filhos por Faixa-Etária - Estatísticas SIAPEN</title>

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
        <h3>Faixa etária dos filhos/as que estão no estabelecimento prisional
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h3>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Faixa Etária</th>
                <th scope="col">Quantidade</th>
                <tbody>
                    @foreach($internos as $categoria => $interno)
                        @if($categoria != "")
                            <tr class="">
                                <td>{{ $categoria }}</td>
                                <td>
                                    {{ count($internos[$categoria]) }}
                                    @php
                                        $totalgeral = $totalgeral + count($internos[$categoria]);
                                    @endphp
                                </td>
                            </tr>
                            @endif
                    @endforeach
                    <tr>
                        <td>TOTAL</td>
                        <td>{{ $totalgeral }}</td>
                    </tr>
            </table>
        </div>

        <br/>

    </div>

</body>