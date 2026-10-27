<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Estatística de Agendamentos de Advogados</title>
    <style>
        body{
            font-family: Arial;
            font-size: 16px;
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
        .strong{
            font-weight: bold;
        }
        .alignCenter
        {
            text-align: center;
        }
        .titulo{
            height: 25px; background-color: #d1d1d1; vertical-align: middle; text-transform: uppercase;
        }
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

        table th td {
            vertical-align: middle !important;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2 !important;
        }
        .dadoRetornado{
            font-weight: bold;
        }

        .text-center{
            text-align: center;
        }
    </style>
</head>
<body>

<h4 class="text-center">Estatística de Teleatendimento das Unidade Prisionais do Mês {{ $mes }}/{{ date("Y") }}</h4>
<table class="table" style="width: 100%">
    <thead>
    <th class="titulo" scope="col">Unidade</th>
    <th class="titulo text-center" scope="col">Atendidos</th>
    <th class="titulo text-center" scope="col">Agendados</th>
    <th class="titulo text-center" scope="col">Faltas</th>
    <th class="titulo text-center" scope="col">Cancelados</th>
    </thead>

    <tbody>
    @php
    $atendidos = 0;
    $agendado = 0;
    $falta = 0;
    $cancelada = 0;
    @endphp
    @foreach($unidades as $unidade)

        @if(array_key_exists($unidade, $agendamentoMes))
            <tr>
                <td>{{ $unidade }}</td>
                @if(array_key_exists("Atendido", $agendamentoMes[$unidade]))
                    @php
                    $atendidos = $atendidos + count($agendamentoMes[$unidade]["Atendido"]);
                    @endphp

                    <td class="text-center dadoRetornado">{{ count($agendamentoMes[$unidade]["Atendido"]) }}</td>
                @else
                    <td class="text-center">0</td>
                @endif

                @if(array_key_exists("Agendado", $agendamentoMes[$unidade]))
                    @php
                    $agendado = $agendado + count($agendamentoMes[$unidade]["Agendado"]);
                    @endphp

                    <td class="text-center dadoRetornado">{{ count($agendamentoMes[$unidade]["Agendado"]) }}</td>
                @else
                    <td class="text-center">0</td>
                @endif

                @if(array_key_exists("Falta", $agendamentoMes[$unidade]))
                    @php
                    $falta = $falta + count($agendamentoMes[$unidade]["Falta"]);
                    @endphp

                    <td class="text-center dadoRetornado">{{ count($agendamentoMes[$unidade]["Falta"]) }}</td>
                @else
                    <td class="text-center">0</td>
                @endif

                @if(array_key_exists("Cancelada", $agendamentoMes[$unidade]))
                    @php
                    $cancelada = $cancelada + count($agendamentoMes[$unidade]["Cancelada"]);
                    @endphp

                    <td class="text-center dadoRetornado">{{ count($agendamentoMes[$unidade]["Cancelada"]) }}</td>
                @else
                    <td class="text-center">0</td>
                @endif

            </tr>

        @else
            <tr>
                <td>{{ $unidade }}</td>
                <td class="text-center">0</td>
                <td class="text-center">0</td>
                <td class="text-center">0</td>
                <td class="text-center">0</td>
            </tr>

        @endif


    @endforeach
    <tr>
        <td class="dadoRetornado">TOTAL</td>
        <td class="text-center dadoRetornado">{{ $atendidos }}</td>
        <td class="text-center dadoRetornado">{{ $agendado }}</td>
        <td class="text-center dadoRetornado">{{ $falta }}</td>
        <td class="text-center dadoRetornado">{{ $cancelada }}</td>
    </tr>
    </tbody>

</table>


</body>
</html>
