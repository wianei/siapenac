<head>
    <meta charset="utf-8">
    <title>Lista de Remoções</title>

    <style type="text/css">
        body{
            font-family: Arial !important;
            letter-spacing: 1px;
        }
        .tableT3 tr:nth-child(odd) {background: #FFF !important;}
        .tableT3 tr:nth-child(even) {background: #e5ebec !important;}

        div.page
        {
            overflow: hidden;
            page-break-after: always;
        }
    </style>

</head>
<body>
@php
$unidadeID = $unidades[0]->idUnidade;
@endphp
@if($tipo == "unidade")
@foreach($unidades as $unidade)

    @if($unidade->qtdServidores > 0)
        <h4>Unidade: {{ $unidade->nomeUnidade }}</h4>
        <h6>Total de servidores removidos: {{ $unidade->qtdServidores }}</h6>
        <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="background-color: #d0d0d0; padding: 3px; border: 1px solid #d0d0d0; font-size: 12px; font-weight: bold">#</td>
                    <td style="background-color: #d0d0d0; padding: 3px; border: 1px solid #d0d0d0; font-size: 12px; font-weight: bold" >Servidor</td>
                    <td style="background-color: #d0d0d0; padding: 3px; border: 1px solid #d0d0d0; font-size: 12px; font-weight: bold; text-align: center" >Matrícula</td>
                    <td style="background-color: #d0d0d0; padding: 3px; border: 1px solid #d0d0d0; font-size: 12px; font-weight: bold; text-align: center" >Lot. Atual</td>
                    <td style="background-color: #d0d0d0; padding: 3px; border: 1px solid #d0d0d0; font-size: 12px; font-weight: bold; text-align: center" >Lot. Nova</td>
                    <td style="background-color: #d0d0d0; padding: 3px; border: 1px solid #d0d0d0; font-size: 12px; font-weight: bold; text-align: center">Turma</td>
                    <td style="background-color: #d0d0d0; padding: 3px; border: 1px solid #d0d0d0; font-size: 12px; font-weight: bold; text-align: center">Opção</td>
                </tr>
                <tbody class="tableT3">
                @php
                $i = 1;
                @endphp

                @foreach($servidores as $servidor)
                    @if($servidor->idLotacaoRemocao == $unidade->idUnidade)

                        <tr style="font-size: 11px !important;">
                            <td>{{ $i }}</td>
                            <td>{{ $servidor->nomeServidor }}</td>
                            <td style="text-align: center">{{ $servidor->matriculaServidor }}</td>
                            <td style="text-align: center">{{ $servidor->lotacaoAnterior }}</td>
                            <td style="text-align: center">{{ $servidor->lotacaoRemocao }}</td>
                            <td style="text-align: center">{{ $servidor->turma }}ª</td>
                            <td style="text-align: center">{{ $servidor->ordemEscolha }}ª</td>
                        </tr>
                        @php
                        $i++;
                        @endphp

                    @endif
                @endforeach
                </tbody>
            </table>
        @if($unidadeID != $unidade->idUnidade)
            <div class="page"></div>
        @endif
    @endif
@endforeach
    @elseif($tipo == "servidores")

    <table width="100%" cellpadding="2" cellspacing="0">
        <tr>
            <td style="background-color: #d0d0d0; padding: 5px; border: 1px solid #d0d0d0; font-size: 12px; font-weight: bold">#</td>
            <td style="background-color: #d0d0d0; padding: 5px; border: 1px solid #d0d0d0; font-size: 12px; font-weight: bold" >Servidor</td>
            <td style="background-color: #d0d0d0; padding: 5px; border: 1px solid #d0d0d0; font-size: 12px; font-weight: bold; text-align: center" >Matrícula</td>
            <td style="background-color: #d0d0d0; padding: 5px; border: 1px solid #d0d0d0; font-size: 12px; font-weight: bold; text-align: center" >Lot. Atual</td>
            <td style="background-color: #d0d0d0; padding: 5px; border: 1px solid #d0d0d0; font-size: 12px; font-weight: bold; text-align: center" >Lot. Nova</td>
            <td style="background-color: #d0d0d0; padding: 5px; border: 1px solid #d0d0d0; font-size: 12px; font-weight: bold; text-align: center">Turma</td>
            <td style="background-color: #d0d0d0; padding: 5px; border: 1px solid #d0d0d0; font-size: 12px; font-weight: bold; text-align: center">Opção</td>
        </tr>
        <tbody class="tableT3">
        @php
        $i = 1;
        @endphp

        @foreach($servidores as $servidor)

                <tr style="font-size: 11px !important;">
                    <td>{{ $i }}</td>
                    <td>{{ $servidor->nomeServidor }}</td>
                    <td style="text-align: center">{{ $servidor->matriculaServidor }}</td>
                    <td style="text-align: center">{{ $servidor->lotacaoAnterior }}</td>
                    <td style="text-align: center">{{ $servidor->lotacaoRemocao }}</td>
                    <td style="text-align: center">{{ $servidor->turma }}ª</td>
                    <td style="text-align: center">{{ $servidor->ordemEscolha }}ª</td>
                </tr>
                @php
                $i++;
                @endphp
        @endforeach
        </tbody>
    </table>

@endif
</body>