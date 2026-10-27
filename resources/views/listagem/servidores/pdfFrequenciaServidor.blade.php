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
$total = count($servidores);
$t = 0;
@endphp

@foreach($servidores as $servidor)
    @php
    $t = $t + 1;
    @endphp

    <table width="100%" cellpadding="1" cellspacing="1" style="padding: 3px !important;" class="tableDadosInterno">
        <tr>
            <td style="font-size: 14px">SERVIDOR: <strong>{{ $servidor['nome'] }}</strong></td>
            <td>MATRÍCULA: <strong>{{ $servidor['matricula'] }}</strong></td>
        </tr>
        <tr>
            <td>LOTAÇÃO: <strong>{{ $nomeUnidade }}</strong></td>
            <td>MÊS/ANO: <strong>{{ strtoupper($mes)."/".$ano }}</strong></td>
        </tr>
    </table>
    <table width="100%">
        <thead class="">
        <tr>
            <th scope="col" style="width: 75px; height: 25px; background-color: #CCCCCC" rowspan="2">DIA</th>
           <th colspan="2" style="height: 25px; background-color: #dcdcdc">
               ENTRADA
           </th>
           <th colspan="2" style="height: 25px; background-color: #dcdcdc">
               SAÍDA
           </th>
        </tr>
        <tr class="">
            <th scope="col" style="width: 80px; height: 35px; background-color: #CCCCCC">HORA</th>
            <th scope="col" style="width: 35%; height: 35px; background-color: #CCCCCC">RUBRICA</th>
            <th scope="col" style="width: 80px; height: 35px; background-color: #CCCCCC">HORA</th>
            <th scope="col" style="width: 35%; height: 25px; background-color: #CCCCCC">RUBRICA</th>
        </tr>
        </thead>
        <tbody>
        @for($i=1; $i <= $qtdDiasMes; $i++)
            <tr>
                <td style="width: 80px; height: 28px; text-align: left; padding-left: 5px">{{ $i }} - {{ \App\Classes\Tdata::diaNomeAbreviatura($ano."-".$mesNum."-".$i) }}</td>
                <td style="width: 80px; height: 23px; text-align: center">
                    @if($diaInicioAssinatura != 1 && $i < $diaInicioAssinatura)
                        *******
                    @endif

                </td>
                <td style="width: 35%; height: 23px; text-align: center">
                    @if($diaInicioAssinatura != 1 && $i < $diaInicioAssinatura)
                        *******
                    @endif

                </td>
                <td style="width: 80px; height: 23px; text-align: center">
                    @if($diaInicioAssinatura != 1 && $i < $diaInicioAssinatura)
                        *******
                    @endif

                </td>
                <td style="width: 35%; height: 23px; text-align: center">
                    @if($diaInicioAssinatura != 1 && $i < $diaInicioAssinatura)
                        *******
                    @endif

                </td>
            </tr>
        @endfor
        </tbody>

    </table>

    <table width="100%" style="margin-top: -1px">
        <tr>
            <td style="text-align: center; height: 70px; width: 40%">
                <br> _______________________ <br>
                VISTO DA CHEFIA

            </td>
            <td style="text-align: center; font-size: 10px; width: 50%">
                <br>__________________________________________ <br>
                ASSINATURA DO SERVIDOR <br>
                {{ $servidor['nome']." (".$servidor['matricula'].")" }}

            </td>
        </tr>

    </table>
    @if($t < $total)
        <div class="page"></div>
    @endif
@endforeach
</body>
</html>