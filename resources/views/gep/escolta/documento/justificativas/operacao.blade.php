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

            Considerando toda a logística acerca da condução de presos para suas respectivas audiências  nos diversos fóruns da Capital e Região Metropolitana, temos que é imprescindível que as solicitações judiciais sejam enviadas com uma antecedência mínima de 48 (quarenta e oito) horas, a fim de viabilizar a organização do transporte em questão, levando em consideração, principalmente, a grande dispersão entre os juízos.
        </p>
        <p style="text-indent: 5em; text-align: justify">

            Informamos que não foi possível realizar a condução do interno abaixo descriminado para participar da audiência de instrução e julgamento marcada para a data e hora também abaixo descrita, perante este juízo, em razão do início de uma tentativa de motim ocorrida
            @if($interno['opreracaoNaCela'] == 'sim')
                especificamente em sua cela
            @endif
                no(a) {{ $interno['unidadeInterno'] }}.
        </p>

        <p style="text-indent: 5em; text-align: justify">
            Nesse contexto, em razão do ocorrido, todos os internos envolvidos estão sendo alvos de um procedimento preliminar de apuração inviabilizando sua retirada da Unidade Prisional.
        </p>

        <p style="text-indent: 5em; text-align: justify">
            Ademais, nos colocamos a inteira disposição para encaminhar o respectivo interno em outra oportunidade a ser designada por Vossa Excelência.
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
