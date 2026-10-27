<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Listagem Frequência dos Servidores</title>
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
        .tableDadosInterno td{
            height: 30px;
            padding-left: 5px;
        }

    </style>
</head>
<body>
@php
$total = count($dadosTransferencia);
$t = 0;
@endphp

<table width="100%">
    <thead>
        <tr>
            <th style="height: 35px; background-color: #CCCCCC">#</th>
            <th style="height: 35px; background-color: #CCCCCC">DATA</th>
            <th style="height: 35px; background-color: #CCCCCC">NOME INTERNO</th>
            {{--<th>MOTIVO</th>--}}
        </tr>
    </thead>
    <tbody>
    @foreach($dadosTransferencia as $transferencia)
        <tr>
        <td style="text-align: center">{{ $loop->iteration }}</td>
        <td style="text-align: center">{{ $transferencia['dataBr'] }}</td>
        <td style="width: 85%;">
            @foreach($transferencia['internos'] as $interno)
                <table width="100%">
                    <tr>
                        <td>
                            @if(count($transferencia['internos']) > 1)
                                {{ $loop->iteration }} -
                            @endif
                            {{ $interno['nome'] }}</td>
                        <td width="30%">{{ $interno['status'] }}</td>
                    </tr>
                </table>
            @endforeach
        </td>
        {{--<td>{{ $transferencia['motivo'] }}</td>--}}

        </tr>
    </tbody>
    @endforeach
</table>
{{--@foreach($dadosTransferencia as $transferencia)--}}
    {{--@php--}}
    {{--$t = $t + 1;--}}
    {{--@endphp--}}

    {{--@if($t < $total)--}}
        {{--<div class="page"></div>--}}
    {{--@endif--}}
{{--@endforeach--}}
</body>
</html>