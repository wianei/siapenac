<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Escolta de Interno - {{ $escolta[0]->unidade }}</title>
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

    </style>
</head>
<body>

<div class="certificado">


    {{--<div style="font-size: 15px; font-weight: bold; text-align: center">Termo de Responsabilidade de Cautela Especial de Arma de Fogo</div>--}}
    <table style="width: 100%; border: 1px solid #ccc; padding: 3px" cellpadding="3">
        {{--<tr>--}}
            {{--<td style="width: 10px; text-align: center; font-size: 13px; font-weight: bold;" colspan="2">{{ $escolta[0]->unidade }}</td>--}}
        {{--</tr>--}}
        <tbody style="font-size: 11px">
        <tr>
            <td style="; border-bottom: 1px solid #CCCCCC" colspan="2"><strong>Origem:</strong> {{ $escolta[0]->unidade }}</td>
        </tr>
        <tr style="">
            <td style="; border-bottom: 1px solid #CCCCCC"><strong>Destino:</strong> {{ $escolta[0]->destino }}</td>
            <td style="; border-bottom: 1px solid #CCCCCC; border-left: 1px solid #CCCCCC">VTR: {{$escolta[0]->vtr_placa}} </td>
        </tr>

        <tr style="">
            <td style="; border-bottom: 1px solid #CCCCCC"><strong>Data de Saída:</strong> {{ $escolta[0]->data_saida." - ".$escolta[0]->hora_saida }}</td>
            <td style="; border-bottom: 1px solid #CCCCCC; border-left: 1px solid #CCCCCC">Data de Retorno:
                @if($escolta[0]->data_retorno != "")
                    {{ $escolta[0]->data_retorno." - ".$escolta[0]->hora_retorno }}
                @endif
            </td>
        </tr>
        <tr>
            <td style=" border-bottom: 1px solid #CCCCCC" colspan="2"><strong>Motivação:</strong> {{ $escolta[0]->motivo }}</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #CCCCCC" colspan="2">
                <h4><strong>Internos:</strong></h4>
                <table width="100%">
                    @foreach($dadosInterno as $di)
                    <tr>
                        <td style="text-align: center !important; border-bottom: 1px solid #CCCCCC !important;">
                            @if(file_exists(storage_path('app/arquivos/internos/fotos/' . $di['id'] . "/" . FotoService::getFotoPrincipalByIdTipo($di['id'],'interno'))))
                                <img style="text-align: center; height: 200px;" src="{{storage_path('app/arquivos/internos/fotos/' . $di['id'] . "/" . FotoService::getFotoPrincipalByIdTipo($di['id'],'interno'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil"><br>
                                Prontuário: {{$di['id']}}
                            @else
                                <img style="text-align: center; height: 200px" src="{{ public_path('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil"><br>
                                -- SEM FOTO --<br>
                                Prontuário: {{$di['id']}}
                            @endif

                        </td>
                        <td style="border-bottom: 1px solid #CCCCCC !important;">
                            {{ $di['nome'] }}
                            <br>Localização: {{ $di['sigla'] }}
                            <br>Mãe: {{ $di['mae'] }}
                            <br>Data de Nascimento: {{ $di['nascimento'] }}
                            <br>Periculosidade: {{ $di['periculosidade'] }}
                            <br>Faccção: {{ $di['faccao'] }}
                            <br>Liderança: {{ $di['lideranca'] }}
                        </td>
                    </tr>
                    @endforeach
                </table>

            </td>
        </tr>

        <tr>
            <td style="border-bottom: 1px solid #CCCCCC" colspan="2">
                <h4><strong>Agentes:</strong></h4>
                @foreach($agentes as $agente)

                {{ $agente['matricula']." - ". $agente['nome']}}<br>

                @endforeach

            </td>
        </tr>
        </tbody>
    </table>
    <br>
    <br>
    <div style="text-align: center; font-size: 12px">
        ______________________________________________<br>
        Assinatura Diretor(a) ou Responsável

    </div>
</div><!--Certificado-->

</body>
</html>
