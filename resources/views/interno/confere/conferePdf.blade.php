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
                                <td style=""> -- Vazia -- </td>
                            </tr>

                        @endif
                        @foreach($internos as $interno)
                            @if($interno->interno_cela == $dCela['id'])

                                <div style="border-bottom: 1px solid black">
                                    @if($exibirFoto)
                                        <div style="width: auto; height: auto; display: inline-block">
                                            @if(file_exists(storage_path('app/arquivos/internos/fotos/' . $interno->id . "/" . FotoService::getFotoPrincipalByIdTipo($interno->id,'interno'))))
                                                <img style="width: 170px; height: 200px" src="{{storage_path('app/arquivos/internos/fotos/' . $interno->id . "/" . FotoService::getFotoPrincipalByIdTipo($interno->id,'interno'))}}" >
                                            @else
                                                <img style="width: 170px; height: 200px" src="{{ storage_path('public/imagens/usuario/avatarInterno.png') }}" >
                                            @endif
                                        </div>
                                    @endif
                                    <div style="width: 350px; display: inline-block; vertical-align: top; border-right: 1px solid black; @if($exibirFoto) height:200px @endif">
                                        @if($interno->interno_nome_social != null)
                                            {{ $i ."- (".$interno->fk_interno.") - ".strtoupper($interno->interno_nome_social) }}
                                            <br><strong><i>Nome Principal: {{$interno->interno_nome}}</i></strong>
                                        @else
                                            {{ $i ."- (".$interno->fk_interno.") - ".strtoupper($interno->interno_nome) }}
                                        @endif
                                        @if($interno->interno_outros_nomes != null)
                                            <br><strong>Outros nomes:</strong> {{ $interno->interno_outros_nomes }}
                                        @endif
                                        @if($interno->interno_observacao != null)
                                            <br><strong>Observação:</strong> {{ $interno->interno_observacao }}
                                        @endif
                                            <br><strong>Mãe:</strong> {{ $interno->interno_mae }}
                                        

                                    </div>
                                    <div style="width: 150px; display: inline-block; vertical-align: top ;@if($exibirFoto)font-size:15px @endif">
                                        [ ] [ ] [ ] [ ] [ ]
                                        @if($exibirFoto)
                                            <br/>[ ] [ ] [ ] [ ] [ ]
                                            <br/>[ ] [ ] [ ] [ ] [ ]
                                            <br/>[ ] [ ] [ ] [ ] [ ]
                                            <br/>[ ] [ ] [ ] [ ] [ ]
                                        @endif
                                    </div>
                                    <div style="display: inline-block; vertical-align: top; @if($exibirFoto)font-size: 10px @endif">
                                        @if($interno->sancao_triagem != null)
                                            Saída da {{ strtoupper($interno->sancao_triagem) }}:
                                                    {!! $interno->sancao_triagem_data_final."<br>" !!}
                                        @endif
                                        @if($interno->movimentacao_transferencia_pendente == "S")
                                            Transferência para outra unidade em aberto.
                                        @endif
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