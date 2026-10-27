<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Internos Pendentes de Escolta</title>
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

    {{--@foreach($internosPendentes as $interno)--}}
        <table style="font-size: 11px !important;" border="0" cellpadding="1" cellspacing="0" width="100%">
            <tr>
                <td style="width: 5% !important; background-color: #000; color: #FFF">Horário</td>
                <td style="width: 30% !important; background-color: #000; color: #FFF">Nome do Interno</td>
                <td style="width: 35% !important; background-color: #000; color: #FFF">Local de Custódia</td>
                <td style="width: 30% !important; background-color: #000; color: #FFF">Destino</td>
            </tr>

            <tbody class="tableDados">
                @foreach($internosPendentes as $interno)
                    <tr>
                        <td style="border-bottom: 1px solid #CCCCCC">{{ $interno->hora_solicitado }}</td>
                        <td style="border-bottom: 1px solid #CCCCCC">{{ $interno->nome }}</td>
                        <td style="border-bottom: 1px solid #CCCCCC">{{ $interno->localizacao }}</td>
                        <td style="border-bottom: 1px solid #CCCCCC">{{ $interno->destino }}</td>
                    </tr>

                @endforeach
            </tbody>
        </table>
                    {{--<table width="100%" style="max-height:300px !important; font-size: 12px !important;">--}}
                        {{--<tr>--}}
                            {{--<td style="width: 300px; text-align: center !important; border-bottom: 1px solid #CCCCCC !important;">--}}
                                {{--@if(file_exists(storage_path('app/arquivos/internos/fotos/' . $interno->id . "/" . $interno->id.".jpg")))--}}
                                    {{--<img style="text-align: center; height: 200px;"--}}
                                         {{--src="{{storage_path('app/arquivos/internos/fotos/' . $interno->id . "/" . $interno->id.".jpg")}}"--}}
                                         {{--class="imagemSrc cursorPointer" title="Clique para ampliar a foto"--}}
                                         {{--alt="Foto do Perfil"><br>--}}
                                    {{--Prontuário: {{$interno->id}}--}}
                                {{--@else--}}
                                    {{--<img style="text-align: center; height: 200px"--}}
                                         {{--src="{{ public_path('imagens/usuario/avatarInterno.png') }}" class=""--}}
                                         {{--alt="Foto do Perfil"><br>--}}
                                    {{---- SEM FOTO --<br>--}}
                                    {{--Prontuário: {{$interno->id}}--}}
                                {{--@endif--}}

                            {{--</td>--}}
                            {{--<td style="border-bottom: 1px solid #CCCCCC !important;">--}}
                                {{--<strong>{{ $interno->nome }}</strong>--}}
                                {{--<br>Destino: {{ $interno->destino }}--}}
                                {{--<br>Data da Solicitação: {{ $interno->hora_solicitado }}--}}
                                {{--<br>Localização: {{ $interno->localizacao }}--}}
                                {{--<br>Mãe: {{ $interno->mae }}--}}
                                {{--<br>Data de Nascimento: {{ $interno->data_nascimento }}--}}
                                {{--<br>Periculosidade: {{ $interno->periculosidade }}--}}
                                {{--<br>Faccção: {{ $interno->faccao_nome }}--}}
                                {{--<br>Liderança: {{ $interno->lideranca }}--}}
                            {{--</td>--}}
                        {{--</tr>--}}
                    {{--</table>--}}
    {{--@endforeach--}}

</div><!--Certificado-->



</body>
</html>
