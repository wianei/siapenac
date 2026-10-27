<head>
    <meta charset="UTF-8">
    <title>Internos por Sexo - Estatísticas SIAPEN</title>

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
    $totalUnidade = $totalGeralF = $totalGeralM = $totalGeralF = 0;
    $maxValue = 0;

    @endphp
    <br>
    <br>
<h3>Informações estatísticas - {{ $unidadeEstatistica }} em {{ $dataEstatistica }}</h3>

    <div id="resultados">
        <br/>
        <h3>
            Quantidade por Sexo
        </h3>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Quantidade</th>
                <tbody>
                <tr class="">
                    <td>Homens</td>
                    <td>
                        @if(isset($internos['M']))
                            {{ count($internos['M']) }}

                            @php 
                                $totalUnidade = $totalUnidade + count($internos['M']); 
                                $totalGeralM = $totalGeralM + count($internos['M']);
                            @endphp

                            @else
                            0

                        @endif
                    </td>
                </tr>
                <tr class="">
                    <td>Mulheres</td>
                    <td>
                        @if(isset($internos['F']))
                        
                            {{ count($internos['F']) }}

                            @php 
                                $totalUnidade = $totalUnidade + count($internos['F']); 
                                $totalGeralF = $totalGeralF + count($internos['F']);
                            @endphp

                            @else
                            0

                        @endif
                    </td>
                </tr>
                <tr class="">
                    <td>Total</td>
                    <td>{{ $totalGeralM + $totalGeralF }}</td>
                </tr>
            </table>
        </div>

        <br/>

    </div>
        
</body>

