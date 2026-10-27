<head>
    <meta charset="utf-8">
    <title>Lista de Remoções</title>

    <style type="text/css">
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

@if(count($dadosT1) > 0)
<h4 class="mt-3">Unidade: {{ $unidadeNome }}</h4>
<h5 class="mt-3">1ª Turma</h5>
<h6 class="mt-3">Vagas {{ $vagasT1 }}</h6>
<div class="table-responsive-md mt-3">
    <table width="100%" cellspacing="0" cellpadding="2" border="1" bordercolor="000" style="font-size: 11px !important;">
        <thead class="background_30516f color_FFF headTable">
        <tr class="">
            {{--<th scope="col">#</th>--}}
            <th scope="col" style="width: 5%;">#</th>
            <th scope="col" style="width: 35%;">Servidor(a)</th>
            <th scope="col" style="width: 8%; text-align: center">Exercício</th>
            <th scope="col" style="width: 12%; text-align: center">Lot. Atual</th>
            <th scope="col" style="width: 12%; text-align: center">1ª Opção</th>
            <th scope="col" style="width: 12%; text-align: center">2ª Opção</th>
            <th scope="col" style="width: 12%; text-align: center">3ª Opção</th>
        </tr>
        </thead>
        <tbody class="tableT3">
        @foreach($dadosT1 as $dt1)
            <tr class="">
                <td style="width: 5%;">{{ $loop->iteration }}º</td>
                <td style="width: 35%;">{{ $dt1->nome }}</td>
                <td style="width: 8%; text-align: center">{{ $dt1->data_exercicioBr }}</td>
                <td style="width: 12%; text-align: center">{{ $dt1->lotacaoAtual }}</td>
                <td style="width: 12%; text-align: center">{{ $dt1->opcao1 }}</td>
                <td style="width: 12%; text-align: center">{{ $dt1->opcao2 }}</td>
                <td style="width: 12%; text-align: center">{{ $dt1->opcao3 }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif
<div class="page"></div>
@if(count($dadosT2) > 0)
    <h4 class="mt-3">Unidade: {{ $unidadeNome }}</h4>
    <h5 class="mt-3">2ª Turma</h5>
    <h6 class="mt-3">Vagas {{ $vagasT2 }}</h6>
    <div class="table-responsive-md mt-3">
        <table width="100%" cellspacing="0" cellpadding="2" border="1" bordercolor="000" style="font-size: 11px !important;">
            <thead class="background_30516f color_FFF headTable">
            <tr class="">
                {{--<th scope="col">#</th>--}}
                <th scope="col" style="width: 5%;">#</th>
                <th scope="col" style="width: 35%;">Servidor(a)</th>
                <th scope="col" style="width: 8%; text-align: center">Exercício</th>
                <th scope="col" style="width: 12%; text-align: center">Lot. Atual</th>
                <th scope="col" style="width: 12%; text-align: center">1ª Opção</th>
                <th scope="col" style="width: 12%; text-align: center">2ª Opção</th>
                <th scope="col" style="width: 12%; text-align: center">3ª Opção</th>
            </tr>
            </thead>
            <tbody class="tableT3">
            @foreach($dadosT2 as $dt2)
                <tr class="">
                    <td style="width: 5%;">{{ $loop->iteration }}º</td>
                    <td style="width: 35%;">{{ $dt2->nome }}</td>
                    <td style="width: 8%; text-align: center">{{ $dt2->data_exercicioBr }}</td>
                    <td style="width: 12%; text-align: center">{{ $dt2->lotacaoAtual }}</td>
                    <td style="width: 12%; text-align: center">{{ $dt2->opcao1 }}</td>
                    <td style="width: 12%; text-align: center">{{ $dt2->opcao2 }}</td>
                    <td style="width: 12%; text-align: center">{{ $dt2->opcao3 }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
<div class="page"></div>
@if(count($dadosT3) > 0)
    <h4 class="mt-3">Unidade: {{ $unidadeNome }}</h4>
    <h6 class="mt-3">Vagas 3ª Turma: {{ $vagasT3 }}</h6>
    <div class="table-responsive-md mt-3">
        <table width="100%" cellspacing="0" cellpadding="2" border="1" bordercolor="000" style="font-size: 11px !important;">
            <thead class="background_30516f color_FFF headTable">
            <tr class="">
                {{--<th scope="col">#</th>--}}
                <th scope="col" style="width: 5%;">#</th>
                <th scope="col" style="width: 35%;">Servidor(a)</th>
                <th scope="col" style="width: 8%; text-align: center">Exercício</th>
                <th scope="col" style="width: 12%; text-align: center">Lot. Atual</th>
                <th scope="col" style="width: 12%; text-align: center">1ª Opção</th>
                <th scope="col" style="width: 12%; text-align: center">2ª Opção</th>
                <th scope="col" style="width: 12%; text-align: center">3ª Opção</th>
            </tr>
            </thead>
            <tbody class="tableT3">
            @foreach($dadosT3 as $dt3)
                <tr class="">
                    <td style="width: 5%;">{{ $loop->iteration }}º</td>
                    <td style="width: 35%;">{{ $dt3->nome }}</td>
                    <td style="width: 8%;  text-align: center">{{ $dt3->data_exercicioBr }}</td>
                    <td style="width: 12%; text-align: center">{{ $dt3->lotacaoAtual }}</td>
                    <td style="width: 12%; text-align: center">{{ $dt3->opcao1 }}</td>
                    <td style="width: 12%; text-align: center">{{ $dt3->opcao2 }}</td>
                    <td style="width: 12%; text-align: center">{{ $dt3->opcao3 }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

</body>