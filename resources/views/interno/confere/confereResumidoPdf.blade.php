<head>
    <meta charset="utf-8">
    <title>Confere Geral</title>

    <style type="text/css">
        body{
            font-family: Arial !important;
            letter-spacing: 1px;
        }
        #internos tr td{
            width: 100%;
            padding: 3px;
            font-size: 9px;
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
                            <td style="width: 33.33%; background-color: #d8d8d8; padding: 3px; border: 1px solid #d8d8d8; font-size: 11px">Total na Cela: {{ $dCela['qtdInternos'] }}</td>
                        </tr>
                    </table>

                    <table style="display: inline-block" id="internos" width="100%" cellpadding="0" cellspacing="0">
                        @php
                        $i = 1;
                        @endphp
                        @if($dCela['qtdInternos'] == 0)
                            <<div style="display:inline-block; width: 50%; border-bottom: 1px solid black; font-size: 12px; border-right: 1px solid black; margin-right: -1px; border-left: 1px solid black; margin-left: -1px;">
                               --- Vazia ---
                            </div>

                        @endif
                        @foreach($internos as $interno)
                            @if($interno->interno_cela == $dCela['id'])

                                <div style="display:inline-block; width: 50%; border-bottom: 1px solid black; font-size: 12px; border-right: 1px solid black; margin-right: -1px; border-left: 1px solid black; margin-left: -1px;">
                                    <div style="margin-left:3px; width: 25px; display: inline-block; vertical-align: top;">
                                        {{ $i ."- "}}
                                    </div>
                                    <div style="width: 275px; display: inline-block; vertical-align: top;">
                                        @if($interno->interno_nome_social != null)
                                            {{ strtoupper($interno->interno_nome_social) }}
                                            <br><strong><i>Nome Principal: {{$interno->interno_nome}}</i></strong>
                                        @else
                                            {{ strtoupper($interno->interno_nome) }}
                                        @endif
                                    </div>
                                    <div style="display: inline; vertical-align: top;">
                                        <strong>[ ][ ][ ][ ][ ]</strong>
                                    </div>
                                </div>

                                @php
                                $i++;
                                @endphp

                                {{--{{ $interno->interno_nome }}<br>--}}
                            @endif
                        @endforeach

                    </table>

                @endif
            @endforeach
        @endif
    @endforeach
@endforeach
</body>