<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Escolta de Interno - {{ $unidade }}</title>
    <style>
        body {
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

        .tituloSeccao {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #CCCCCC;
        }

        .footer {
            position: fixed;
            left: 0px;
            bottom: -190px;
            right: 0px;
            height: 50px;
            text-align: center;
        }

        .footer .pagenum:before {
            content: counter(page);
        }

        div.page {
            page-break-after: always;
            page-break-inside: avoid;
        }


    </style>
</head>
<body>

<div class="certificado">

    @php
    $total = count($dadosInterno);
    $t = 0;
    @endphp

@foreach($dadosInterno as $di)
        @php
        $t = $t + 1;
        @endphp

        <table width="100%" style="font-size: 12px !important; margin-bottom: 20px">
            <tr>
                <td style="border-bottom: 1px solid #CCCCCC !important;">
                    <strong>{{ $di['nome'] }}</strong>
                    <br>Localização: {{ $di['sigla'] }}
                    <br>Destino: {{ $di['destinoInterno'] }}
                    <br>Mãe: {{ $di['mae'] }}
                    <br>Data de Nascimento: {{ $di['nascimento'] }}
                    <br>Periculosidade: {{ $di['periculosidade'] }}
                    <br>Faccção: {{ $di['faccao'] }}
                    <br>Liderança: {{ $di['lideranca'] }}
                </td>
            </tr>
        </table>

        @if(!is_null($di['foto']))

            @php
            $i = 0;
            @endphp

            @foreach($di['foto'] as $foto)
                @if($foto->tipo == "principal" || $foto->tipo == "tatuagem")
                    @if($i <= 3)
                        <div style="float: left; width: 425px; height: 425px; overflow: hidden; margin-right: 10px">
                            <img style="text-align: center; height: 85%"
                                 src="{{storage_path('app/arquivos/internos/fotos/' . $di['id'] . "/" . $foto->nome_arquivo)}}"
                                 class="imagemSrc cursorPointer" title="Clique para ampliar a foto"
                                 alt="Foto do Perfil">
                        </div>
                    @endif
                    @php
                    $i++;
                    @endphp

                @endif
            @endforeach

        @else
            -- INTERNO SEM FOTO --
        @endif

        @if($t < $total)
            <div class="page"></div>
        @endif
    @endforeach

    {{--@foreach($unidadesPavilhao as $unPv)--}}
    {{--@foreach($dadosInterno as $di)--}}
    {{--@if($unPv['unidade'] == $di['idUnidade'])--}}
    {{--@if($unPv['pavilhaoInterno'] == $di['idPavilhao'])--}}
    {{--<table width="100%" style="font-size: 12px !important;">--}}
    {{--<tr>--}}
    {{--<td style="text-align: center !important; border-bottom: 1px solid #CCCCCC !important;">--}}
    {{--@if(file_exists(storage_path('app/arquivos/internos/fotos/' . $di['id'] . "/" . FotoService::getFotoPrincipalByIdTipo($di['id'],'interno'))))--}}
    {{--<img style="text-align: center; height: 200px;"--}}
    {{--src="{{storage_path('app/arquivos/internos/fotos/' . $di['id'] . "/" . FotoService::getFotoPrincipalByIdTipo($di['id'],'interno'))}}"--}}
    {{--class="imagemSrc cursorPointer" title="Clique para ampliar a foto"--}}
    {{--alt="Foto do Perfil"><br>--}}
    {{--Prontuário: {{$di['id']}}--}}
    {{--@else--}}
    {{--<img style="text-align: center; height: 200px"--}}
    {{--src="{{ public_path('imagens/usuario/avatarInterno.png') }}" class=""--}}
    {{--alt="Foto do Perfil"><br>--}}
    {{---- SEM FOTO --<br>--}}
    {{--Prontuário: {{$di['id']}}--}}
    {{--@endif--}}

    {{--</td>--}}
    {{--<td style="border-bottom: 1px solid #CCCCCC !important;">--}}
    {{--<strong>{{ $di['nome'] }}</strong>--}}
    {{--<br>Localização: {{ $di['sigla'] }}--}}
    {{--<br>Destino: {{ $di['destinoInterno'] }}--}}
    {{--<br>Mãe: {{ $di['mae'] }}--}}
    {{--<br>Data de Nascimento: {{ $di['nascimento'] }}--}}
    {{--<br>Periculosidade: {{ $di['periculosidade'] }}--}}
    {{--<br>Faccção: {{ $di['faccao'] }}--}}
    {{--<br>Liderança: {{ $di['lideranca'] }}--}}
    {{--</td>--}}
    {{--</tr>--}}
    {{--</table>--}}
    {{--@else--}}

    {{--<div class="page"></div>--}}

    {{--@endif--}}
    {{--@else--}}
    {{--<div class="page"></div>--}}
    {{--@endif--}}
    {{--@endforeach--}}
    {{--@endforeach--}}

</div><!--Certificado-->


</body>
</html>
