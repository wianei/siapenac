<head>
    <meta charset="UTF-8">
    <title>Internos Sentenciados e Provisórios Sem Med. Seg. - Estatísticas SIAPEN</title>

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

        $totalSentenciado = $totalgeral =  $totalProvisorio = 0;

    @endphp
    
    <br>
    <br>
<h3>Informações estatísticas - {{ $unidadeEstatistica }} em {{ $dataEstatistica }}</h3>

    @if($internos)
    <div id="resultados">
        <h5>Regime prisional
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>

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
                    <td>Sentenciados</td>
                    <td>
                        @if(isset($internos['S']))
                            @if(isset($internos['S']['M']))
                            {{ count($internos['S']['M']) }}

                            @php 
                                $totalSentenciado = $totalSentenciado + count($internos['S']['M']); 
                                $totalgeral = $totalgeral + count($internos['S']['M']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>
                        @if(isset($internos['S']))
                            @if(isset($internos['S']['F']))
                            {{ count($internos['S']['F']) }}

                            @php 
                                $totalSentenciado = $totalSentenciado + count($internos['S']['F']); 
                                $totalgeral = $totalgeral + count($internos['S']['F']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>{{ $totalSentenciado }}</td>
                </tr>
                <tr class="">
                    <td>Provisório</td>
                    <td>
                        @if(isset($internos['N']))
                            @if(isset($internos['N']['M']))
                            {{ count($internos['N']['M']) }}

                            @php 
                                $totalProvisorio = $totalProvisorio + count($internos['N']['M']); 
                                $totalgeral = $totalgeral + count($internos['N']['M']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>
                        @if(isset($internos['N']))
                            @if(isset($internos['N']['F']))
                            {{ count($internos['N']['F']) }}

                            @php 
                                $totalProvisorio = $totalProvisorio + count($internos['N']['F']); 
                                $totalgeral = $totalgeral + count($internos['N']['F']);
                            @endphp

                            @else
                                0
                            @endif
                            
                            @else
                                0
                        @endif
                    </td>
                    <td>{{ $totalProvisorio }}</td>
                </tr>
            </table>
        </div>

        <br/>

    </div>
    @endif
    
    @if($detalhesUnidades)
    <div id="resultados">
        <h5>Regime prisional
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>

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
                    <td>Sentenciados</td>
                    <td>{{ $qtdSentenciadoM }}</td>
                    <td>{{ $qtdSentenciadoF }}</td>
                    <td>{{ $qtdSentenciadoM + $qtdSentenciadoF }}</td>
                </tr>
                <tr class="">
                    <td>Provisórios</td>
                    <td>{{ $qtdProvisorioM }}</td>
                    <td>{{ $qtdProvisorioF }}</td>
                    <td>{{ $qtdProvisorioM + $qtdProvisorioF }}</td>
                </tr>
                    <tr class="">
                        <td>Regime Não Informado no SIAPEN</td>
                        <td>{{ $qtdNdM }}</td>
                        <td>{{ $qtdNdF }}</td>
                        <td>{{ $qtdNdM + $qtdNdF }}</td>
                    </tr>
                
            </table>
        </div>

        <br/>

    </div>
    @endif
        
</body>


