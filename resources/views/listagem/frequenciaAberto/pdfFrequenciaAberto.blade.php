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
    <table width="100%">
        <thead class="">
        <tr class="">
            <th scope="col" style="width: 20px; height: 25px; background-color: #CCCCCC">Nº</th>
            <th scope="col" style="width: 30%; height: 25px; background-color: #CCCCCC">Nome</th>
            <th scope="col" style="width: 100px; height: 25px; background-color: #CCCCCC">Datas</th>
            <th scope="col"  style="height: 25px; background-color: #CCCCCC">Assinatura</th>
        </tr>
        </thead>

        <tbody>
            @foreach($internos as $interno)
                <tr>
                    <td style="width: 20px; height: 120px; text-align: center; font-weight: bold">{{ $loop->iteration }}</td>
                    <td style="width: 30%; height: 120px">{{ $interno['nome'] }}</td>
                    <td style="width: 100px; height: 120px" colspan="2">
                        <table width="100%" border="0" style="margin-left: -2px; margin-top: -2px; border: none !important;">
                            @foreach($datas as $data)
                                <tr class="teste">
                                    <td style="width: 100px; height: 25px; border-bottom: 1px solid #000 !important; border-right: 1px solid #000 !important;; border-top: none; text-align: center">{{ $data }}</td>
                                    <td style="height: 25px; border-right: none !important;"></td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
</body>
</html>