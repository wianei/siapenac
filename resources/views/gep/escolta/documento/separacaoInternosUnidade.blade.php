<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>FTPEN -Internos por Unidade Prisional </title>
    <style>
        body{
            font-family: Arial;
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

        .tableDados tr:nth-child(odd) {background: #FFF !important;}
        .tableDados tr:nth-child(even) {background: #e5ebec !important;}

        div.page
        {
            page-break-after: always;
            page-break-inside: avoid;
        }



    </style>
</head>
<body>

<div class="certificado">

    @foreach($missoes as $key=>$miss)
        <h3 style="text-align: center">Internos para Escolta</h3>
        <h5>{{ $key }}</h5>
        <table width="100%" cellspacing="0" cellpadding="2" border="1" bordercolor="000" style="font-size: 11px !important;">
            <tr>
                <td style="text-align: center !important;">#</td>
                <td>Nome do Interno</td>
                <td>Localização</td>
            </tr>
            <tbody>
                @foreach($miss as $interno)
                    <tr>
                        <td style="text-align: center !important;">{{ $loop->iteration }}</td>
                        <td>{{ $interno['nomeInterno'] }}</td>
                        <td>{{ $interno['localizacao'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="page"></div>
    @endforeach

</div><!--Certificado-->



</body>
</html>
