<head>
    <meta charset="UTF-8">
    <title>Carteira de Visitante</title>

    <style type="text/css">
        body{
            font-family: Arial !important;
            letter-spacing: 1px;
        }
        #internos tr td{
            width: 100%;
            padding: 3px;
            font-size: 11px;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        #internos tr:nth-child(odd) td{
            background-color: #FFF !important;
        }
        #internos tr:nth-child(even) td{
            background-color: #e5e5e5 !important;
        }
        div.page
        {
            page-break-after: always;
            page-break-inside: avoid;
        }
        .pne{
            background-color: red !important;
        }
    </style>

</head>
<body>
    <div style="width: 450px">
        <div style="position: absolute">
            <img src="{{public_path('imagens/carteiraVisitante/frente.jpg')}}" width="450" alt="">
        </div>
        <div style="position: relative; font-size: 13px; text-align: center; width: 300px; padding-top: 5px; float: left;">
            GOVERNO DO ESTADO DO ACRE <br>
            IAPEN-AC<br>
            CARTEIRA DE VISITANTE
        </div>
        <div style="position: relative; float: right; padding-right: 5px; padding-top: 5px;">
            <img src="{{public_path('imagens/carteiraVisitante/brasao.png')}}" width="95" alt="">
        </div>
        <div style="position: relative; background-color: #FFF; width: 167px; height: 200px; margin-left: 12px; top: 12px; float: left;">
            <div style="position: relative; width: 100%; height: 180px; text-align: center; bottom: 0">
            
                    <img style="text-align: center; width: 167px; height: 200px;" src="{{storage_path('app/arquivos/visitas/fotos/' . $dados['id'] . "/". FotoService::getFotoPrincipalByIdTipo($dados['id'], 'visita'))}}">
                    
            </div>
            <div style="position: relative; width: 100%; height: 21px; background-color: #35D535; color: #FFF; text-align: center; bottom: 0" @if($dados['pne'] == "S") class="pne" @endif>
                @if($dados['pne'] == "S")
                    PNE
                    @else
                        COMUM
                @endif
            </div>
        </div>
        <div style="font-size: 11px; position: relative; width: 250; padding: 5px; margin-right: 5px; float: right;">
            NOME DO VISITANTE:
        </div>
        <div style="font-size: 12px; display: table; position: relative; width: 250; background-color: #FFF; text-transform: uppercase; padding:3px 5px 3px 3px; margin-right: 5px; float: right; border: 1px solid #000;">
            {{ $dados['nome'] }}
        </div>
        <div style="font-size: 11px; position: relative; width: 250; padding: 5px; margin-right: 5px; float: right;">
            CPF:
        </div>
        <div style="font-size: 12px; display: table; position: relative; width: 250; background-color: #FFF; text-transform: uppercase; padding:3px 5px 3px 3px; margin-right: 5px; float: right; border: 1px solid #000;">
            {{ $dados['cpf'] }}
        </div>
        @if (!empty($dados['responsavel'][0]->nome))
        <div style="font-size: 11px; position: relative; width: 250; height: 12px; padding-right: 5px; margin-right: 5px; float: right;">
            REPONSÁVEL PELA VISITA:
        </div>
        <div style="font-size: 12px; display: table; position: relative; width: 250; background-color: #FFF; text-transform: uppercase; padding:3px 5px 3px 3px; margin-right: 5px; float: right; border: 1px solid #000;">
            {{$dados['responsavel'][0]->nome}}
        </div>
        @endif
        <div style="font-size: 11px; position: relative; width: 250; padding: 5px; margin-right: 5px; float: right;">
            EXPEDIÇÃO - VALIDADE:
        </div>
        <div style="font-size: 12px; display: table; position: relative; width: 250; background-color: #FFF; text-transform: uppercase; padding:3px 5px 3px 3px; margin-right: 5px; float: left; border: 1px solid #000;">
            {{ date("d/m/Y") }} - {{ $dados['validade']}}
        </div>
    </div>

    <div style="clear:both"></div>
    
    <div style="width: 450px; margin-top: 25px;">
        <div style="position: absolute">
            <img src="{{public_path('imagens/carteiraVisitante/verso.jpg')}}" width="450" alt="">
        </div>
        <div style="position: relative; font-size: 12px; text-align: left; width: 450px; padding-top: 10px; padding-left: 5px; float: left;">
            Detentos Autorizados para Visitação:
        </div>
        <div style="font-size: 12px; display: table; position: relative; width: 440; background-color: #FFF; text-transform: uppercase; padding:3px 5px 3px 3px; margin-right: 5px; float: right; border: 1px solid #000;">
            @foreach($dados['internos'] as $interno)
                {!! $interno['nome'] ." (".$interno['parentesco'].")<br>"!!}
            @endforeach
        </div>
        
        <div style="font-size: 11px; position: relative; width: 250; height: 100px; padding-right: 5px; margin-right: 5px; float: right;">
            
        </div>
        
        <div style="font-size: 11px; position: relative; width: 250; height: 3px; padding-right: 5px; margin-right: 5px; float: right;">
            
        </div>

        <div style="font-size: 11px; position: absolute; width: 450; text-align: center; bottom: 750px; left: 0;">
            _______________________________________________
        </div>
        
        <div style="font-size: 12px; background-color: #FFF; position: absolute; width: 440; margin-left: 12px; text-align: center; text-transform: uppercase; bottom: 725px; padding: 3px 0; left: 0;">
            {{ $dados['chefeAssinatura'] }}
        </div>
        
        <div style="font-size: 14px; position: absolute; width: 440; margin-left: 12px; text-align: center; text-transform: uppercase; bottom: 702px; padding: 3px 0; left: 0;">
            CHEFE DE DEPARTAMENTO DE REINTEGRAÇÃO
        </div>
        
        <div style="font-size: 14px; position: absolute; width: 440; margin-left: 12px; text-align: center; text-transform: uppercase; bottom: 660px; padding: 3px 0; left: 0;">
           {!! $dados['codBar'] !!}
        </div>
        
    </div>
    
</body>

