<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatório Diário de Entradas e Saídas - {{ config('app.sistema_estado') }} </title>
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

    </style>
</head>
<body>

<div class="certificado">

<table style="width: 100%">
    <thead>
        <tr>
            <th class="titulo">Unidades</th>
            <th class="titulo">Entradas</th>
            <th class="titulo">Saídas</th>
            <th class="titulo">Progressão Semiaberto</th>
            <th class="titulo">Progressão Aberto</th>
        </tr>
    </thead>
    <tbody>
        @foreach($unidades as $unidade)

            @if(array_key_exists($unidade, $dados))

                <tr>

                    <td>{{$unidade}}</td>

                    {{--{{ dd(count(array_column($dados[$unidade["nome"]], "S"))) }}--}}

                    @if(array_key_exists("E", $dados[$unidade]))

                        <td class="strong alignCenter">{{ count($dados[$unidade]["E"]) }}</td>

                            @else

                        <td class="alignCenter">0</td>

                    @endif

                    @if(array_key_exists("S", $dados[$unidade]))

                        <td class="strong alignCenter">{{ count($dados[$unidade]["S"]) }}</td>

                            @else

                        <td class="alignCenter">0</td>

                    @endif

                    @if(array_key_exists("SA", $dados[$unidade]))

                        <td class="strong alignCenter">{{ count($dados[$unidade]["SA"]) }}</td>

                            @else

                        <td class="alignCenter">0</td>

                    @endif

                    @if(array_key_exists("A", $dados[$unidade]))

                        <td class="strong alignCenter">{{ count($dados[$unidade]["A"]) }}</td>

                    @else

                        <td class="alignCenter">0</td>

                    @endif

                </tr>

                @else

                <tr>
                    <td>{{ $unidade }}</td>
                    <td class="alignCenter">0</td>
                    <td class="alignCenter">0</td>
                    <td class="alignCenter">0</td>
                    <td class="alignCenter">0</td>
                </tr>
            @endif

        @endforeach
            <tr>
                <td class="strong titulo">TOTAL DO DIA - {{ $dataBr }}</td>
                <td class="strong alignCenter titulo">{{ $countEntradas }}</td>
                <td class="strong alignCenter titulo">{{ $countSaidas }}</td>
                <td class="strong alignCenter titulo">{{ $countProgressaoSemiaberto }}</td>
                <td class="strong alignCenter titulo">{{ $countProgressaoAberto }}</td>
            </tr>
    </tbody>
</table>

</div><!--Certificado-->



</body>
</html>
