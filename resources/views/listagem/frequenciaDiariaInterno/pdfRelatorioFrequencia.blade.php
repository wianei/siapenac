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

        tr:nth-child(even) {
            background-color: #f2f2f2 !important;
        }

    </style>
</head>
<body>

    <table>
        <thead class="">
        <tr>
            <th colspan="2" style="height: 25px; background-color: #dcdcdc">
                INTERNOS
            </th>
            <th colspan="{{$qtdDiasMes}}" style="height: 25px; background-color: #dcdcdc">
                DIAS
            </th>
        </tr>
        <tr class="">
            <th scope="col" style="width: 20px; height: 25px; background-color: #CCCCCC">#</th>
            <th scope="col" style="width: 30%; height: 25px; background-color: #CCCCCC">Nome</th>
            @for($i = 1; $i <= $qtdDiasMes; $i ++)
                @if(\App\Classes\Tdata::isFimSemana($ano."-".$mes."-".$i) == 1)
                    <th scope="col" style="width: 30px; height: 25px; background-color: #ffffeb">{{ $i }}</th>
                    @else
                    <th scope="col" style="width: 30px; height: 25px; background-color: #CCCCCC">{{ $i }}</th>
                @endif

            @endfor
        </tr>
        </thead>
        <tbody>
        @foreach($relatorio as $interno => $rel)
            <tr>
                <td style="text-align: center">{{ $loop->iteration }}</td>
                <td>{{ $interno }}</td>

                    @for($i = 1; $i <= $qtdDiasMes; $i ++)

                    @if(\App\Classes\Tdata::isFimSemana($ano."-".$mes."-".$i) == 1)
                        <td scope="col" style="width: 30px; background-color: #ffffeb; text-align: center">
                    @else
                        <td scope="col" style="width: 30px; text-align: center">
                    @endif


                                @if(!array_key_exists (str_pad($i, 2, "0", STR_PAD_LEFT), $rel))
                                    <span style="color: #b1b1b1;">x</span>
                                    @else
                                    @foreach($rel as $dia=>$dados)

                                    @if($i == $dia)
                                            <span style="font-weight: bold">{{ current(array_keys($dados)) }}</span>
                                    @endif

                                    @endforeach
                                @endif
                            </td>

                    @endfor

            </tr>
        @endforeach

        </tbody>

    </table>

</body>
</html>