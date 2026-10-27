<head>
    <meta charset="UTF-8">
    <title>Confere Geral</title>

    <style type="text/css">
        body{
            font-family: Arial !important;
            letter-spacing: 1px;
        }
        #internos tr td{
            width: 100%;
            padding: 3px;
            font-size: 11px;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        #internos tr:nth-child(odd) td{
            background-color: #FFF !important;
        }
        #internos tr:nth-child(even) td{
            background-color: #e5e5e5 !important;
        }
        div.page
        {
            page-break-after: always;
            page-break-inside: avoid;
        }
    </style>

</head>
<body>

@foreach($dadosPavilhao as $dadosPv)
    <br>
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 33.33%; background-color: #c8c8c8; padding: 3px; border: 1px solid #c8c8c8; font-size: 12px; font-weight: bold">{{ $dadosPv['nome'] }}</td>
            <td style="width: 33.33%; background-color: #c8c8c8; padding: 3px; border: 1px solid #c8c8c8; font-size: 11px"></td>
            <td style="width: 33.33%; background-color: #c8c8c8; padding: 3px; border: 1px solid #c8c8c8; font-size: 11px">Total de Internos no Pavilhão: {{ $dadosPv['qtdInternos'] }}</td>
        </tr>
    </table>
    @foreach($dadosAla as $dAla)
        @if($dAla['fk_pavilhao'] == $dadosPv['id'])
            {{--<h5>{{ $dAla['nome'] }}</h5>--}}
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="width: 33.33%; background-color: #d0d0d0; padding: 3px; border: 1px solid #d0d0d0; font-size: 12px; font-weight: bold">{{ $dAla['nome'] }}</td>
                    <td style="width: 33.33%; background-color: #d0d0d0; padding: 3px; border: 1px solid #d0d0d0; font-size: 11px">Total de Celas: {{ $dAla['qtdCelas'] }}</td>
                    <td style="width: 33.33%; background-color: #d0d0d0; padding: 3px; border: 1px solid #d0d0d0; font-size: 11px">Total de Internos na Ala: {{ $dAla['qtdInternos'] }}</td>
                </tr>
            </table>

            @foreach($dadosCela as $dCela)
                @if($dCela['fk_ala'] == $dAla['id'])
                    {{--<h6>{{ $dCela['nome'] }}</h6>--}}
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="width: 33.33%; background-color: #d8d8d8; padding: 3px; border: 1px solid #d8d8d8; font-size: 12px; font-weight: bold">{{ $dCela['nome'] }}</td>
                            <td style="width: 33.33%; background-color: #d8d8d8; padding: 3px; border: 1px solid #d8d8d8; font-size: 11px"></td>
                            <td style="width: 33.33%; background-color: #d8d8d8; padding: 3px; border: 1px solid #d8d8d8; font-size: 11px">Total de Internos na Cela: {{ $dCela['qtdInternos'] }}</td>
                        </tr>
                    </table>

                    <table id="internos" width="100%" cellpadding="0" cellspacing="0">
                        @php
                        $i = 1;
                        @endphp
                        @if($dCela['qtdInternos'] == 0)
                            <tr>
                                <td style="">-- Vazia --</td>
                            </tr>

                        @endif
                        @foreach($visita as $vis)
                            @foreach($vis['internos'] as $interno)
                                @if($interno->cela == $dCela['id'])

                                    <tr>
                                        <td style="width: 45%">
                                            @if($vis['suspensao'] == null)
                                                @if($interno->fimSancaoTriagem != null  && $interno->statusSancaoTriagem == "A")
                                                <span style="text-decoration: line-through">{{ $i ."- ".strtoupper($vis['nome']) }}  - {{ $interno->grau_parentesco }} do interno<br></span>Interno na {{ $interno->tipoSancaoTriagem }} até {{ $interno->fimSancaoTriagem }}
                                                    @else
                                                {{ $i ."- ".strtoupper($vis['nome']) }}  - {{ $interno->grau_parentesco }} do interno
                                                @endif
                                                @else
                                                <span style="text-decoration: line-through">{{  $i ."- ".strtoupper($vis['nome']) }}</span><br>
                                                Visita suspensa de entrar nas unidades prisionais do Estado<br>
                                                Até: {{ $vis['suspensao'][0]->data_final }}<br>
                                                Motivação: {{ $vis['suspensao'][0]->motivacao }}
                                            @endif
                                        </td>
                                        <td style="width: 12%; text-align: left;">{{$vis['cpf']}}</td>
                                        <td style="width: 43%">
                                            {{ $interno->nomeInterno }}
                                        </td>
                                    </tr>

                                    @php
                                    $i++;
                                    @endphp
                                @endif
                            @endforeach
                        @endforeach

                    </table>

                @endif
            @endforeach
        @endif
    @endforeach
@endforeach
</body>