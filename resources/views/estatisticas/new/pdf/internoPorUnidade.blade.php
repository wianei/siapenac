<head>
    <meta charset="UTF-8">
    <title>Internos por Unidade - Estatísticas SIAPEN</title>

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

    <div id="resultados">
        <br>
        <h3>
            Internos Por Unidade
        </h3>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Unidade</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total Unidade</th>
                @php
                    $totalUnidade = 0;
                    $totalGeralM = 0;
                    $totalGeralF = 0;
                @endphp
                <tbody>
                @foreach($unidades as $unidade)
               
                    @foreach($internos as $sigla => $interno)
                    @if($unidade['sigla'] == $sigla)
                    <tr class="">
                        @php
                            $totalUnidade = 0;
                        @endphp
                        <td>{{$sigla}}</td>
                        <td>
                            
                            @if(isset($interno['M']))
                                {{ count($interno['M']) }}
                                
                                @php 
                                    $totalUnidade = $totalUnidade + count($interno['M']); 
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
                                        $totalUnidade = $totalUnidade + count($interno['F']); 
                                        $totalGeralF = $totalGeralF + count($interno['F']);
                                    @endphp
                                    
                                    @else
                                    0
                                
                            @endif
                        </td>
                        <td>
                            {{ $totalUnidade }}
                            @php
                                if($totalUnidade >= $maxValue)
                                {
                                    $maxValue = $totalUnidade;
                                }
                            @endphp
                        </td>
                    </tr>
                    @endif
                    @endforeach
                @endforeach
                <tr>
                    <td>
                        TOTAL GERAL
                    </td>
                    <td>
                        {{ $totalGeralM }}
                    </td>
                    <td>
                        {{ $totalGeralF }}
                    </td>
                    <td>
                        {{ $totalGeralM + $totalGeralF }}
                    </td>
                </tr>
            </table>
        </div>
        
</body>

