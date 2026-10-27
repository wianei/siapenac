<head>
    <meta charset="UTF-8">
    <title>Atestado de Permanência Carcerária</title>

    <style type="text/css">
        body {
            font-family: Arial !important;
            letter-spacing: 1px;
        }

        #internos tr td {
            width: 100%;
            padding: 3px;
            font-size: 11px;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        #internos tr:nth-child(odd) td {
            background-color: #FFF !important;
        }

        #internos tr:nth-child(even) td {
            background-color: #e5e5e5 !important;
        }

        div.page
        {
            page-break-after: always;
            page-break-inside: avoid;
        }

    </style>

</head>
<body>
@foreach($internos as $interno)
    <br>
    <br>

    <div style="width: 100%; height: 20px; background: #ccc7ab; padding: 3px; text-align: center">
        ATESTADO DE PERMANÊNCIA CARCERÁRIA
    </div>
    <br>
    <div>
        <p style="text-align: justify; font-size: 16px !important; line-height: 18px">
            APC Nº: {{ $numero }}
        </p>
        <p style="text-align: justify; font-size: 16px !important; line-height: 18px">
            ATESTO para os devidos fins que se fizerem necessários que
            <strong>{{ $interno['nome'] }}</strong>,
            

            @if(isset($interno['naturalidade_pais']))
                natural do(a) {{ $interno['naturalidade_pais'] }},
            @endif
            
            @if($interno['sexo'] == 'F')
                nascida em
            @else
                nascido em
            @endif

            <strong>{{ $interno['dataNascimento'] }}</strong>,
            @if($interno['sexo'] == 'F')
                filha
            @else
                filho
            @endif
            de <strong>{{$interno['pai']}}</strong> e de <strong>{{$interno['mae']}}</strong>, ingressou
            nesta Unidade Prisional em <strong>{{$interno['dataIngressoUnidade']}}</strong>
            @if($interno['sentenciado'] == 'S')
                e, nesta data ({{ date("d/m/Y") }}), cumpre pena no regime
                @if($interno['regime'] == null)
                    <strong>*** NÃO INFORMADO ***</strong>
                @else
                    <strong>{{$interno['regime']}}</strong>
                @endif
                nesta unidade.
            @elseif($interno['sentenciado'] == 'N')
                e, nesta data ({{ date("d/m/Y") }}), encontra-se recolhido provisoriamente nesta unidade.
            @endif
        </p>
        <br>
        <br>
        <p style="text-align: right; font-size: 16px !important; line-height: 18px">
            {{ $nomeUnidade }} , {{ strtoupper($cidadeUnidade) }}/AC, {{ date("d/m/Y") }}
        </p>
    </div>

    <br>
    <br>
    <br>
    <br>
    <div style="text-align: center">

        @if($assinaturaDoc == "diretor")
            @if($assinatura == null)
                Diretor não informado ao Setor de Recursos Humanos do IAPEN
            @else
                ________________________________________<br>
                {{ $assinatura['nome'] }} ({{ $assinatura['matricula'] }})<br>
                @if($assinatura['sexo'] == "Masculino")
                    Diretor da Unidade Prisional
                @elseif($assinatura['sexo'] == "Feminino")
                    Diretora da Unidade Prisional
                @else
                    Diretor(a) da Unidade Prisional
                @endif
            @endif
        @elseif($assinaturaDoc == "viceDiretor")
            @if($assinatura == null)
                Vice-Diretor não informado ao Setor de Recursos Humanos do IAPEN
            @else
                ________________________________________<br>
                {{ $assinatura['nome'] }} ({{ $assinatura['matricula'] }})<br>
                @if($assinatura['sexo'] == "Masculino")
                    Vice-Diretor da Unidade Prisional
                @elseif($assinatura['sexo'] == "Feminino")
                    Vice-Diretora da Unidade Prisional
                @else
                    Vice-Diretor(a) da Unidade Prisional
                @endif
            @endif
        @endif

    </div>
    <br>
    <br>
    <br>
    <div style="width: 100% !important; text-align: center">
        <img src="{{public_path('imagens/template/emBranco.jpg')}}" width="500px" alt="">
    </div>
    @if($loop->iteration != count($internos))
        <div class="page"></div>
    @endif
@endforeach
</body>