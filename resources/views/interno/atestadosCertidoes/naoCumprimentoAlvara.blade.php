<head>
    <meta charset="UTF-8">
    <title>Certidão Negativa de Alvará</title>

    <style type="text/css">
        body {
            font-family: Arial !important;
            letter-spacing: 1px;
        }


    </style>

</head>
<body>
@foreach($internos as $interno)
    <br>
    <br>

    <div style="width: 100%; height: 20px; background: #ccc7ab; padding: 3px; text-align: center">
        CERTIDÃO NEGATIVA DE CUMPRIMENTO DE ALVARÁ
    </div>
    <br>
    <div id="conteudo">
        <br>
        <br>
        <p style=" text-align: justify; text-justify: inter-word; font-size: 16px !important; line-height: 1.8">
            CERTIFICO para os devidos fins que se fizerem necessários,
            @if($interno['sexo'] == 'F')
                que a reeducanda
                <strong>{{ $interno['nome'] }}</strong>,
            @else
                que o reeducando
                <strong>{{ $interno['nome'] }}</strong>,
            @endif

            @if($interno['naturalidade_cidade'] != null)
                natural de <strong>{{$interno['naturalidade_cidade']}}</strong>
                @if($interno['naturalidade_estado'] != null)
                    , <strong>{{$interno['naturalidade_estado']}}</strong>
                @endif
                @if($interno['naturalidade_pais'] != null)
                    , <strong>{{$interno['naturalidade_pais']}}</strong>
                @endif
            @endif
            @if($interno['sexo'] == 'F')
                , nascida em
            @else
                , nascido em
            @endif
            <strong>{{ $interno['dataNascimento'] }}</strong>

            @if($interno['sexo'] == 'F')
                ,filha
            @else
                ,filho
            @endif

            de
            <strong>{{$interno['pai']}}</strong>
            e de
            <strong>{{$interno['mae']}}</strong>
            ,
            <strong><u>NÃO</u></strong>
            foi

            @if($interno['sexo'] == 'F')
                posta
            @else
                posto
            @endif
            em incontinente liberdade após ter recebido Alvará de Soltura expedido pela {{$vara}}, nos autos do processo de nº {{$processo}}.
            <br>
            <br>
            <strong>JUSTIFICATIVA:</strong>{{$justificativa}}.
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