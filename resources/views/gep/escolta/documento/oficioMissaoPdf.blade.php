<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Escolta de Interno </title>
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



    </style>
</head>
<body>

<div class="certificado">


    @foreach($missoes as $key=>$miss)

        @foreach($miss as $mi)

            @foreach($mi as $m)

    <table width="100%">
        <tr>
            <td width="50%" style="font-weight: bold">Ofício Nº {{ $m[0]['fk_escolta'] }} - FTPEN

                @if( $m[0]['documento'] != "")
                    <br>
                    Proc. nº {{ $m[0]['documento'] }}
                @endif

            </td>
            <td width="50%" style="text-align: right">João Pessoa (PB), {{ $m[0]['data_extenso'] }}</td>
        </tr>
    </table>

    <table width="100%" style="margin-top: 30px">
        <tr>
            <td width="50%" valign="top" style="font-weight: bold">
                Exmo(a). Senhor(a)<br>
                {{ $m[0]['juiz'] }}<br>
                {{ $m[0]['qualificacao'] }}<br>
                {{ $m[0]['comarca'] }}
            </td>
            <td width="50%" valign="top">
                <div style="border: 1px solid #000; padding: 5px; width: 270px; float: right; font-size: 14px !important;">
                    DESTINO<br>
                    RECEBI EM_____/_____/______<br>
                    HORA_______________________<br>
                    MATRICULA__________________<br>
                    NOME_______________________
                </div>
            </td>
        </tr>
    </table>

    <table width="100%" style="margin-top: 20px">
        <tr>
            <td width="50%" style="font-weight: bold">
                Assunto: Audiência
            </td>
            <td width="50%" valign="top">
                <div style="border: 1px solid #000; padding: 5px; width: 270px; float: right; font-size: 14px !important;">
                    ORIGEM - DEVOLUÇÃO<br>
                    RECEBI EM_____/_____/______<br>
                    HORA_______________________<br>
                    MATRICULA__________________<br>
                    NOME_______________________
                </div>
            </td>
        </tr>
    </table>
    <p style="text-indent: 100px; margin-top: 30px">
        Pelo presente, em atenção ao ofício referência, apresento a Vossa Excelência a(s) pessoa(s) presa(s) de justiça abaixo relacionada(s) às <strong>{{ $m[0]['horario_saida'] }}</strong> hs a fim de participar(em) de audiência(s) aprazada(s) para esta data.

    </p>
        <table width="100%" style="font-weight: bold; margin-top: 30px">
            @foreach($m as $interno)

            <tr>
                <td width="50%">
                    {{ $loop->iteration }} - {{ $interno['nomeInterno'] }}
                </td>
                <td width="50%" style="text-align: right">
                    {{ $interno['localizacao'] }}
                </td>
            </tr>
            @endforeach

        </table>

    <p style="text-indent: 100px;  margin-top: 30px">
        Segue(m) a(s) mesma(s) devidamente escoltada(s) e algemada(s) em consonância com  a  súmula  vinculante  11  do  Supremo  Tribunal  Federal da República Federativa do Brasil¹.
    </p>
    <p>
        Respeitosamente.
    </p>

    <table width="100%" style="margin-top: 80px">
        <tr>
            <td width="50%" style="text-align: center">
                __________________________________<br>
                Liberação
            </td>
            <td width="50%" style="text-align: center">
                __________________________________<br>
                Dir. ou Vice do FTPEN
            </td>
        </tr>
    </table>


                <div class="page"></div>
    @endforeach
                <div class="page"></div>
    @endforeach
            <div class="page"></div>
    @endforeach

</div><!--Certificado-->



</body>
</html>
