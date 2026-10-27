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
<table id="internos" width="100%" cellpadding="0" cellspacing="0">
    @php
    $i = 1;
    @endphp

    @foreach($visita as $vis)
        @if(count($vis['internos']) >= 1)
                <tr>
                    <td style="width: 40%">
                        @if($vis['suspensao'] == null)
                            {{ $i ."- ".strtoupper($vis['nome']) }}
                        @else
                            <span style="text-decoration: line-through">{{  $i ."- ".strtoupper($vis['nome']) }}</span><br>
                            Visita suspensa de entrar nas unidades prisionais do Estado<br>
                            Até: {{ $vis['suspensao'][0]->data_final }}<br>
                            Motivação: {{ $vis['suspensao'][0]->motivacao }}
                        @endif
                    </td>
                    <td style="width: 12%; text-align: left;">{{$vis['cpf']}}</td>
                    <td style="width: 48%">
                        @php
                        $x = 1;
                        @endphp
                        @foreach($vis['internos'] as $interno)
                            @if($interno->fimSancaoTriagem != null && $interno->statusSancaoTriagem == "A")
                                <span style="text-decoration: line-through">{{ $interno->nomeInterno }} </span>  - Na {{ $interno->tipoSancaoTriagem }} até {{ $interno->fimSancaoTriagem }}<br>
                            {{--@endif--}}
                            @elseif($interno->vinculoSuspenso != null && $interno->vinculoSuspenso == 'A')
                                <span style="text-decoration: line-through">{{ $interno->nomeInterno }} </span>  - Suspensão de vínculo<br>
                            {{--@endif--}}
                            @elseif($interno->vinculoSuspenso == null)
                                {{ $x ."- ".$interno->nomeInterno." - ".$interno->sigla }}<br>
                            @endif
                            @php
                            $x++;
                            @endphp

                        @endforeach

                    </td>
                </tr>

                @php
                $i++;
                @endphp
        @endif
    @endforeach

</table>

</body>