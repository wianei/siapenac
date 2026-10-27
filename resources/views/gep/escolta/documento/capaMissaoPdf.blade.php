<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Escolta de Interno </title>
    <style>
        body{
            font-family: Arial;
            font-size: 12px;
            letter-spacing: 1px;
        }
        #header {
            position: fixed;
            left: 0px;
            top: -150px;
            right: 0px;
            height: 150px;
            text-align: left;
        }
        .tituloSeccao{
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #CCCCCC;
        }
        .footer { position: fixed; left: 0px; bottom: -190px; right: 0px; height: 50px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }

        div.page
        {
            page-break-after: always;
            page-break-inside: avoid;
        }

        table {
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

    </style>
</head>
<body>

@php
$total = count($capaMissao);
$i = 0;
@endphp
    @foreach($capaMissao as $regiao => $capa)
        @php
        $i = $i + 1;
        @endphp

        <div>
    <div style="text-align: center; font-size: 30px; font-style: italic; margin-bottom: 10px">CAPA DE MISSÃO</div>
    <div style="text-align: center; font-size: 25px">{{ $regiao }}</div>
    <div style="text-align: center; font-size: 25px; margin-top: 20px; margin-bottom: 20px">EQUIPE(S)</div>

        <div style="width: 40px; padding: 5px; float: left;margin-right: 10px">
            <div style="font-size: 13px; font-weight: bold">VTR</div>
            <div style="font-size: 13px;">MO</div>
            <div style="font-size: 13px;">CMDO</div>
        </div>

    @foreach($capa as $k => $equipe)

        <div style=" width: 350px; padding: 5px; float: left;margin-right: 10px; border-left: 1px solid #CCCCCC;">
            <div style="font-size: 12px; font-weight: bold">{{ $equipe[0]['vtr'] }} - {{ $equipe[0]['nome_equipe'] }}</div>
            <div style="font-size: 12px;">{{ $equipe[0]['vtr_motorista'] }}</div>
            <div style="font-size: 12px;">{{ $equipe[0]['vtr_comandante'] }}</div>
            <div style="font-size: 10px !important; margin-top: 5px">
                Agentes: <br>
                @foreach($equipe[0]['servidores'] as $agente)
                    {{$loop->iteration}} - {{ $agente['nome'] }}<br>
                @endforeach
            </div>
            <div style="font-size: 10px; margin-top: 3px; font-style: italic">{{ $equipe[0]['obs'] }}</div>
        </div>
    @endforeach

    <div style="clear: both"></div>
    <div style="text-align: center; font-size: 25px; margin-top: 30px; margin-bottom: 20px; border-top: 1px solid #CCCCCC; padding-top: 20px">AUDIÊNCIAS</div>

    <table width="100%" cellspacing="0" cellpadding="2" border="1" bordercolor="000" style="font-size: 11px !important;">
        <tr>
            <td style="background-color: #000; color: #FFF; width: 5% !important;">HORÁRIO</td>
            <td style="background-color: #000; color: #FFF; width: 30%">NOME DO APENADO</td>
            <td style="background-color: #000; color: #FFF; width: 35%">DESTINO</td>
            <td style="background-color: #000; color: #FFF; width: 30%">ORIGEM</td>
        </tr>
        <tbody>
        @if(array_key_exists($regiao,$internosMissao))
            @foreach($internosMissao[$regiao] as $interno)
            <tr>
                <td style="width: 5% !important; text-align: center">{{$interno['horario_saida']}}</td>
                <td style="width: 30%">{{$interno['nomeInterno']}}</td>
                <td style="width: 35%">{{$interno['destino']}}</td>
                <td style="width: 30%">{{$interno['localizacao']}}</td>
            </tr>
            @endforeach
        @endif

        </tbody>
    </table>
    <div style="margin-top: 6px">
        @if(array_key_exists($regiao,$internosMissao))
            <strong>TOTAL: {{ count($internosMissao[$regiao]) }}</strong>
        @endif
    </div>

    <div style="border: 1px solid #000; width: 100%; height: 150px; margin-top: 20px">
        <div style="text-align: center; font-size: 20px;  margin-bottom: 20px; border-bottom: 1px solid #000000; padding: 10px 0">OBSERVAÇÕES</div>
    </div>

            @if($i < $total)
                    <div class="page"></div>
            @endif
</div>
    @endforeach


</body>
</html>
