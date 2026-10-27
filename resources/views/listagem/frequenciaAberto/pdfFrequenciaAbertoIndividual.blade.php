<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Listagem de Assinatura - Aberto</title>
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
$total = count($internos);
$t = 0;
@endphp

@foreach($internos as $interno)
    @php
    $t = $t + 1;
    @endphp

    <div style="width: 100%; margin-bottom: 10px">
        <div style="width: 50%; float: left">FREQUÊNCIA MENSAL DO REGIME: <strong>ABERTO</strong></div>
        <div style="width: 30%; float: left">MÊS/ANO: <strong>{{ strtoupper($mes."/".date("Y")) }}</strong></div>
        <div style="width: 20%; float: left; text-align: right">Nº <strong>{{ $loop->iteration  }}</strong></div>
    </div>

    {{--<div>LOCALIZAÇÃO: <strong>{{ $interno['localizacao'] }}</strong></div>--}}
    <div style="font-size: 14px">NOME: <strong>{{ $interno['nome'] }}</strong></div>
    <br>
    <table width="100%">
        <thead class="">
        <tr class="">
            <th scope="col" style="width: 10px; height: 25px; background-color: #CCCCCC">Dia</th>
            <th scope="col" style="width: 80%; height: 25px; background-color: #CCCCCC">Assinatura</th>
            <th scope="col" style="width: 100px; height: 25px; background-color: #CCCCCC">Matrícula do Agente</th>
        </tr>
        </thead>
        <tbody>
        @for($i=1; $i <= $qtdDiasMes; $i++)
            <tr>
                <td style="width: 10px; height: 28px; text-align: center">{{ $i }}</td>
                <td style="height: 28px; text-align: center">
                    @if(!in_array($i, $diasData))
                        ********************************************************************************************
                        @endif
                </td>
                <td style="height: 28px; text-align: center">
                    @if(!in_array($i, $diasData))
                        ***********
                    @endif

                </td>
            </tr>
        @endfor
        </tbody>

    </table>
    @if($t < $total)
        <div class="page"></div>
    @endif
@endforeach
</body>
</html>