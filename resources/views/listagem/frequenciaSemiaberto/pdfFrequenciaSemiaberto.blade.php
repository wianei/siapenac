<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Listagem de Assinatura - Semiaberto</title>
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

@foreach($internos as $interno)
    {{--<div style="width: 100%; margin-bottom: 10px">--}}
        {{--<div style="width: 50%; float: left">FREQUÊNCIA MENSAL DO REGIME: <strong>ABERTO</strong></div>--}}
        {{--<div style="width: 30%; float: left">MÊS/ANO: <strong>{{ strtoupper($mes."/".date("Y")) }}</strong></div>--}}
        {{--<div style="width: 20%; float: left; text-align: right">Nº <strong>{{ $loop->iteration  }}</strong></div>--}}
    {{--</div>--}}

    <table width="100%" cellpadding="1" cellspacing="1" style="padding: 5px !important;" class="tableDadosInterno">
        <tr>
            <td>PAVILHÃO:</td>
            <td>{{ $interno['pavilhao'] }}</td>
            <td>CELA:</td>
            <td>{{ $interno['cela'] }}</td>
            <td style="text-align: center">Nº:</td>
            <td style="text-align: center; font-size: 16px"><strong>{{ $loop->iteration  }}</strong></td>
        </tr>
        <tr>
            <td>APENADO:</td>
            <td colspan="3" style="font-size: 14px"><strong>{{ $interno['nome'] }}</strong></td>
            <td>PRONTUÁRIO:</td>
            <td style="font-size: 14px"><strong>{{ $interno['prontuario'] }}</strong></td>

        </tr>
    </table>

    {{--<div>LOCALIZAÇÃO: <strong>{{ $interno['localizacao'] }}</strong></div>--}}
    {{--<div style="font-size: 14px">NOME: <strong>{{ $interno['nome'] }}</strong></div>--}}
    <br>
    <table width="100%">
        <thead class="">
        <tr class="">
            <th scope="col" style="width: 75px; height: 25px; background-color: #CCCCCC">DIA</th>
            <th scope="col" style="width: 80px; height: 35px; background-color: #CCCCCC">ENTRADA</th>
            <th scope="col" style="width: 80px; height: 35px; background-color: #CCCCCC">SAÍDA</th>
            <th scope="col" style="width: 70%; height: 25px; background-color: #CCCCCC">ASSINATURA DO INTERNO</th>
        </tr>
        </thead>
        <tbody>
        @for($i=1; $i <= $qtdDiasMes; $i++)
            <tr>
                <td style="width: 80px; height: 28px; text-align: left; padding-left: 5px">{{ $i }} - {{ \App\Classes\Tdata::diaNomeAbreviatura($ano."-".$mesNum."-".$i) }}</td>
                <td style="width: 80px; height: 28px; text-align: center">
                    @if($diaInicioAssinatura != 1 && $i < $diaInicioAssinatura)
                        *******
                    @endif

                </td>
                <td style="width: 80px; height: 28px; text-align: center">
                    @if($diaInicioAssinatura != 1 && $i < $diaInicioAssinatura)
                        *******
                    @endif

                </td>
                <td style="height: 28px; text-align: center">
                    @if($diaInicioAssinatura != 1 && $i < $diaInicioAssinatura)
                        ***************************************************************************
                    @endif
                </td>
            </tr>
        @endfor
        </tbody>

    </table>
    <div class="page"></div>
@endforeach
</body>
</html>