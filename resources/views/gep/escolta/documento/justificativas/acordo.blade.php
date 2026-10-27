<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Justificativa FTPEN</title>
    <style>
        body{
            font-family: Arial;
            /*letter-spacing: 1px;*/
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

    </style>
</head>
<body>

@php
$total = count($internosDados);
$i = 0;
@endphp

@foreach($internosDados as $interno)

    @php
    $i = $i + 1;
    @endphp

    <br>
    <br>
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; font-size: 16px; font-weight: bold">Ofício 0{{$interno['oficio']}}/{{ date("Y") }}-FTPEN</td>
            <td style="width: 50%; text-align: right; font-size: 16px; font-weight: bold">{{ $textoData }}</td>
        </tr>
    </table>
    <div>
        <p style="font-size: 15px; font-weight: bold">
        ORIGEM: FTPEN <br>
        DESTINO: Exmo(a). Senhor(a)<br>
        {{ $dadosJudiciario['juiz'] }}<br>
        {{ $dadosJudiciario['qualificacao'] }}<br>
        {{ $dadosJudiciario['comarca'] }}<br>
        </p>
    </div>

    <h4>Assunto: Justificativa</h4>

    <div>

        <p style="text-indent: 5em; margin-bottom: -15px">
            MM. Juiz(a)
        </p>
        <p style="text-indent: 5em; text-align: justify">

            Considerando o acordo firmado entre os membros do Poder Judiciário, do Ministério Público, da SEAP e da Coordenadoria de Executiva de Administração Penitenciária (COEAPE), no qual ficaram estabelecidos os dias específicos para serem realizadas as audiências criminais entres os Distritos Judiciários da Capital, Região Metropolitana e do Interior do Estado, conforme discriminação abaixo, além do reduzido efetivo operacional do Grupo de Escolta Penal, não será possível realizar a condução do(a)(s) interno(a)(s) abaixo descriminado, na data e hora também abaixo descritas, por se tratar de dia da semana não designado para este Distrito. Vide quadro na sequência:

        </p>

    </div>

    <div>
        Preso de Justiça:<br>
        <span><strong>{{ $interno['nome'] }}</strong></span><br>
        Data designada:<br>
        <span><strong>{{ $dataDesignada}}</strong></span><br>
        Hora:<br>
        <span><strong>{{ $horaDesignada}}</strong></span><br>
    </div>

    <div style="width: 100%; border: 1px solid #CCCCCC; margin-top: 50px">
        <ul>
            <li>Segunda, Terça e Sexta-feira: Complexo Judiciário de Natal/RN;</li>
            <li>Quarta, Quinta e Sexta-Feira: Fórum Miguel Seabra Fagundes e Comarcas do interior do Estado.</li>
        </ul>
    </div>

    <p style="text-indent: 5em; text-align: justify; margin-top: 30px">
    Sem mais para o momento, reitero os votos de elevada estima e consideração.
    </p>

    @if($i < $total)
        <div class="page"></div>
    @endif

@endforeach


</body>
</html>
<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 27/08/2019
 * Time: 11:03
 */