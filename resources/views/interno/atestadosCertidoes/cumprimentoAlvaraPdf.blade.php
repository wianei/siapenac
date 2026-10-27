<head>
    <meta charset="UTF-8">
    <title>Certidão de Alvará de Soltura</title>

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
        CERTIDÃO DE CUMPRIMENTO DE ALVARÁ
    </div>
    <br>
    <BR>
    <div>
        <p style="text-align: justify; font-size: 16px !important; line-height: 1.6">
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
            de <strong>{{$interno['pai']}}</strong> e de <strong>{{$interno['mae']}}</strong>, e, inexistindo óbice à liberdade,
            <strong><u> FOI</u></strong>
            @if($interno['sexo'] == 'F')
                posta
            @else
                posto
            @endif
            em liberdade
            @if($data_alvara != "//" && $data_alvara != null)
                na data {{$data_alvara}}
            @endif
            após ter recebido Alvará de Soltura expedido pela {{$vara}}, nos autos do processo de nº {{$processo}}.
            <br>

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

        <br>
        <br>
        <br>


    </div>
    <br>
    <br>
    <br>
    <div style="text-align: center">
        ________________________________________<br>
        <strong>{{ $interno['nome'] }}</strong><br>
        @if($interno['sexo'] == 'F')
            LIBERADA
        @else
            LIBERADO
        @endif<br>
    </div>
    <div style="width: 100% !important; text-align: center">
        <img src="{{public_path('imagens/template/emBranco.jpg')}}" width="500px" alt="">
    </div>
    @if($loop->iteration != count($internos))
        <div class="page"></div>
    @endif
@endforeach
</body>