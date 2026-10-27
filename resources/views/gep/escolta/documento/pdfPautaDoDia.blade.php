<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>FTPEN - Pauta do Dia </title>
    <style>
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

<div class="certificado">

        @foreach($equipeDados as $dado)
            <div style="width: 400px; float: left; height: 360px; border: 1px solid #CCCCCC; padding: 5px; margin-top: 5px; margin-right: 5px; background-color: #FFF">
                <div style="width:100%; background-color: #CCCCCC; padding: 3px">
                    {{ $dado['regiao'] }}
                    <a href="#" class="float-right btn-rounded btn-warning"><i class="mdi mdi-pencil"></i></a>
                </div>
                <div style="width:100%; background-color: #e4e4e4; padding: 3px">
                    {{ $dado['placa_vtr'] }} ({{ $dado['nome_equipe'] }})
                </div>
                <span style="font-size: 12px">Agentes:</span>
                <ul style="font-size: 12px">
                    @foreach($dado['agentes'] as $agente)
                        <li>{{ $agente['nome'] }} ({{ $agente['matricula'] }})</li>
                    @endforeach
                </ul>
                <div style="width:100%; font-size: 12px">
                    {{ $dado['obs'] }}
                </div>
                @if($dado['total'] > 0)
                    <div style="background-color: #fdfce0; padding: 5px">
                        <span style="font-size: 12px">Unidades:</span>
                        <ul style="font-size: 12px">
                            @foreach($dado['internos'] as $interno)
                                <li>{{ $interno['unidade'] }} ({{ $interno['qtd'] }})</li>
                            @endforeach
                            <li><strong>Total: {{ $dado['total'] }}</strong></li>
                        </ul>
                    </div>
                    @else
                    <div style="background-color: #fdfce0; padding: 5px">
                        <span style="font-size: 12px">Unidades:</span>
                        <ul style="font-size: 12px">
                            <li><strong>Sem escoltas para esta equipe</strong></li>
                        </ul>
                    </div>

                @endif

            </div>
        @endforeach


</div><!--Certificado-->



</body>
</html>
