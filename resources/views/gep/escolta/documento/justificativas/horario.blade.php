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
            ORIGEM: Grupo de Escolta Penal <br>
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

            Por motivos de segurança não é viável a realização de escoltas de presos no período da tarde/noite, salvo em casos excepcionais, tendo em vista, entre outros fatores, o congestionamento no trânsito em horários de pico (entre 17h e 18h), diminuição da visibilidade e dispersão das outras forças de segurança pública, notadamente a Polícia Militar, em razão dos atendimentos das ocorrências policiais que predominam neste período, o que dificultaria o seu acionamento para apoio em caso de emergência, potencializando o risco a segurança dos internos, da população e dos operadores da escolta.
        </p>
        <p style="text-indent: 5em; text-align: justify">

            Dessa forma, comunicamos que não será possível realizar a escolta do interno abaixo identificado, com Audiência aprazada para o dia e hora também abaixo descritos.
        </p>

        <p style="text-indent: 5em; text-align: justify">

            Certo da compreensão, nos colocamos a inteira disposição deste juízo para conduzir o acusado em outra oportunidade previamente designada por Vossa Excelência.
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

    <p style="text-indent: 5em; text-align: justify; margin-top: 30px">
        Sem mais para o momento, reitero os votos de elevada estima e consideração.
    </p>

    @if($i < $total)
        <div class="page"></div>
    @endif

@endforeach


</body>
</html>
