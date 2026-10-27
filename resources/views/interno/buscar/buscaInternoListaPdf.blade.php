<head>
    <meta charset="UTF-8">
    <title>Lista de Internos</title>

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


    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 33.33%; background-color: #c8c8c8; padding: 3px; border: 1px solid #c8c8c8; font-size: 11px">Total de Internos: {{ sizeof($dados)}}</td>
        </tr>
        <tr>
            <td style="width: 33.33%; background-color: #c8c8c8; padding: 3px; border: 1px solid #c8c8c8; font-size: 11px">Filtros Utilizados: {{$filtros}}</td>
        </tr>
        <tr>
            <td style="width: 33.33%; background-color: #c8c8c8; padding: 3px; border: 1px solid #c8c8c8; font-size: 11px">
                Nº Sequência - (Prontuário) - Nome do Interno - CPF - Nome da Mãe
            </td>
        </tr>
    </table>

    <table id="internos" width="100%" cellpadding="0" cellspacing="0">
        @php
        $i = 1;
        @endphp
        @foreach($dados as $dado)


            <tr>
                <td style="width: 50%">
                    {{ $i ."- (".$dado->id.") - ".strtoupper($dado->nome). " - ". $dado->cpf . " - ".strtoupper($dado->mae) }}

                </td>

            </tr>

            @php
            $i++;
            @endphp
        @endforeach

    </table>

</body>