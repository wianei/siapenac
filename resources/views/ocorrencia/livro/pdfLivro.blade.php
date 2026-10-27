<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Livro de Ocorrência do Plantão {{ $dataBusca }} - {{ $nomeUnidade }}</title>

    <style type="text/css">
    body{
        font-size: 11px !important;
        font-family: Arial;
        letter-spacing: 1px;
    }
    
    thead { display: table-header-group }
    tfoot { display: table-row-group }
    tr { page-break-inside: avoid }
    
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

    div.page
    {
        page-break-after: always;
        page-break-inside: avoid;
    }
    .cf:before, .cf:after{
        content:"";
        display:table;
    }
    .cf:after{
        clear:both;
    }
    .cf{
        *zoom:1;
    }

    .footer { position: fixed; left: 0px; bottom: -190px; right: 0px; height: 50px;text-align: center;}
    .footer .pagenum:before { content: counter(page); }

    </style>

</head>
<body>

<h3>I - Informações Sobre as Chefias do Plantão</h3>

    <table class="table mb-3" cellpadding="0" cellspacing="0" style="" width="100%">
        <thead>
        <tr class="">
            <th scope="col" style="background-color: #1c2331; color: #FFF; border: 1px solid #1c2331; padding: 5px" >#</th>
            <th scope="col" style="background-color: #1c2331; color: #FFF; border: 1px solid #1c2331; padding: 5px" >Matrícula</th>
            <th scope="col" style="background-color: #1c2331; color: #FFF; border: 1px solid #1c2331; padding: 5px" >Função</th>
            <th scope="col" style="background-color: #1c2331; color: #FFF; border: 1px solid #1c2331; padding: 5px" >Nome</th>
            <th scope="col" style="background-color: #1c2331; color: #FFF; border: 1px solid #1c2331; padding: 5px" >Escala</th>
            <th scope="col" style="background-color: #1c2331; color: #FFF; border: 1px solid #1c2331; padding: 5px" >Lot. na Unidade</th>
            <th scope="col" style="background-color: #1c2331; color: #FFF; border: 1px solid #1c2331; padding: 5px" >Status</th>
        </tr>
        </thead>
        <tbody id="retornoAgente">
        @foreach($dadosEquipe as $dA)
            @if($dA['funcao'] != "Padrão")
                <tr class="">
                    <td scope="row">{{ $loop->iteration }}</td>
                    <td scope="row">{{ $dA['matricula'] }}</td>
                    <td>{{ $dA['funcao'] }}</td>
                    <td>{{ $dA['nome'] }}</td>
                    <td>{{ $dA['escala'] }}</td>
                    <td>{{ $dA['lotacaoUnidade'] }}</td>
                    <td>{{ $dA['status'] }}</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>

<h3 class="tituloSeccao">II - Componentes da Equipe</h3>

    <table class="table mb-3" cellpadding="0" cellspacing="0" style="" width="100%">
        <thead class="background_1c2331 color_FFF">
        <tr class="">
            <th scope="col" style="background-color: #1c2331; color: #FFF; border: 1px solid #1c2331; padding: 5px" >#</th>
            <th scope="col" style="background-color: #1c2331; color: #FFF; border: 1px solid #1c2331; padding: 5px" >Matrícula</th>
            <th scope="col" style="background-color: #1c2331; color: #FFF; border: 1px solid #1c2331; padding: 5px" >Função</th>
            <th scope="col" style="background-color: #1c2331; color: #FFF; border: 1px solid #1c2331; padding: 5px" >Nome</th>
            <th scope="col" style="background-color: #1c2331; color: #FFF; border: 1px solid #1c2331; padding: 5px" >Escala</th>
            <th scope="col" style="background-color: #1c2331; color: #FFF; border: 1px solid #1c2331; padding: 5px" >Lot. na Unidade</th>
            <th scope="col" style="background-color: #1c2331; color: #FFF; border: 1px solid #1c2331; padding: 5px" >Status</th>
        </tr>
        </thead>
        <tbody id="retornoAgente">
        @foreach($dadosEquipe as $dA)
            @if($dA['funcao'] == "Padrão")
                <tr class="">
                    <td scope="row">{{ $loop->iteration }}</td>
                    <td scope="row">{{ $dA['matricula'] }}</td>
                    <td>{{ $dA['funcao'] }}</td>
                    <td>{{ $dA['nome'] }}</td>
                    <td>{{ $dA['escala'] }}</td>
                    <td>{{ $dA['lotacaoUnidade'] }}</td>
                    <td>{{ $dA['status'] }}</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>

<h3 class="tituloSeccao">III - Escala de Postos Diurnos</h3>
@if(empty(json_decode($escalaPostoDiurno, true)))
    -- Sem Informações
@else
    @foreach($escalaPostoDiurno as $escalaDiurna)

        <div class="fontSize-13px">
            {!! $escalaDiurna->descricao !!}
        </div>
    @endforeach
@endif

<div class="cf"></div>
<h3 class="tituloSeccao">IV - Escala de Postos Noturnos</h3>
@if(empty(json_decode($escalaPostoNoturno, true)))
    -- Sem Informações
@else

    @foreach($escalaPostoNoturno as $escalaNoturno)

        <div class="fontSize-13px">
            {!! $escalaNoturno->descricao !!}
        </div>

    @endforeach

@endif

<div class="cf"></div>
<h3 class="tituloSeccao">V - Servidores Responsáveis Pela Ronda</h3>
@if(empty(json_decode($rondas, true)))
    -- Sem Informações
@else

    @foreach($rondas as $ronda)

        <div class="fontSize-13px">
            {!! $ronda->descricao !!}
        </div>

    @endforeach

@endif

<div class="cf"></div>
<h3 class="tituloSeccao">VI - Conferência dos Internos</h3>
@if(empty(json_decode($confere, true)))
    -- Sem Informações
@else

    @foreach($confere as $conf)

        <div class="fontSize-13px">
            {!! $conf->descricao !!}
        </div>

    @endforeach

@endif

<div class="cf"></div>
<h3 class="tituloSeccao">VII - Revistas nas Celas</h3>
@if(empty(json_decode($revistas, true)))
    -- Sem Informações
@else

    @foreach($revistas as $revista)

        <div class="fontSize-13px">
            {!! $revista->descricao !!}
        </div>

    @endforeach

@endif

<div class="cf"></div>
<h3 class="tituloSeccao">VIII - Escala de Banho de Sol</h3>
@if(empty(json_decode($escalaBanhoSol, true)))
    -- Sem Informações
@else

    @foreach($escalaBanhoSol as $banhoSol)

        <div class="fontSize-13px">
            {!! $banhoSol->descricao !!}
        </div>

    @endforeach

@endif

<div class="cf"></div>
<h3 class="tituloSeccao">IX - Revistas nos Pátios</h3>
@if(empty(json_decode($revistaPatios, true)))
    -- Sem Informações
@else

    @foreach($revistaPatios as $revistaPatio)

        <div class="fontSize-13px">
            {!! $revistaPatio->descricao !!}
        </div>

    @endforeach

@endif

<div class="cf"></div>
<h3 class="tituloSeccao">X - Alimentação</h3>
@if(empty(json_decode($alimentacao, true)))
    -- Sem Informações
@else

    @foreach($alimentacao as $aliment)

        <div class="fontSize-13px">
            {!! $aliment->descricao !!}
        </div>

    @endforeach

@endif

<div class="cf"></div>
<h3 class="tituloSeccao">XI - Videoconferência</h3>
@if(empty(json_decode($videoconferencia, true)))
    -- Sem Informações
@else

    @foreach($videoconferencia as $video)

        <div class="fontSize-13px">
            {!! $video->descricao !!}
        </div>

    @endforeach

@endif

<div class="cf"></div>
<h3 class="tituloSeccao">XII - Ocorrências Diversas</h3>
@if(empty($dadosOcorrencia))
    -- Sem Informações
@else

    @foreach($dadosOcorrencia as $ocorrencia)

        <div style="margin-top: 5px; font-size: 12px; padding: 5px; border: 1px dashed #CCCCCC">

            <div class="row ocorrencia">
                <div style="width: 25%; float: left">
                    Nº ocorrência: {{ $ocorrencia['id'] }}
                </div>

                <div style="width: 50%; float: left">
                    Natureza: {{ $ocorrencia['natureza'] }}
                </div>

                <div style="width: 25%; float: left; text-align: right">
                    {{ $ocorrencia['data'] }}  às {{ $ocorrencia['horario'] }}
                </div>
                <div class="cf"></div>
                <div style="font-size: 12px; text-align: justify !important;">
                    <p>
                        {!! $ocorrencia['descricao'] !!}
                    </p>
                </div>
                <div class="col-md-12 fontSize-13px">
                    <i>
                        @if(strpos($ocorrencia['envol'],'inter') != false)
                            <span>Internos Envolvidos:</span>
                            @foreach($ocorrencia['envolvidos'] as $envolvidos)
                                @if(isset($envolvidos['interno']))
                                    @foreach($envolvidos['interno'] as $interno)
                                        {{$interno['nome'].", "}}
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                        @if(strpos($ocorrencia['envol'],'agnt,') >= 0)

                            <br>
                            <span>Agentes Envolvidos: </span>
                            @foreach($ocorrencia['envolvidos'] as $envolvidos)
                                @if(isset($envolvidos['servidor']))
                                    @foreach($envolvidos['servidor'] as $servidor)
                                        {{$servidor->nome.", "}}
                                    @endforeach
                                @endif
                            @endforeach
                        @endif


                        @if(strpos($ocorrencia['envol'],'adv') != false)
                            <br>
                            <span>Advogados Envolvidos: </span>
                            @foreach($ocorrencia['envolvidos'] as $envolvidos)
                                @if(isset($envolvidos['advogado']))
                                    @foreach($envolvidos['advogado'] as $advogado)
                                        {{$advogado->nome.", "}}
                                    @endforeach
                                @endif
                            @endforeach
                        @endif

                        @if(strpos($ocorrencia['envol'],'vis') != false)
                            <br>
                            <span>Visitas Envolvidas: </span>
                            @foreach($ocorrencia['envolvidos'] as $envolvidos)
                                @if(isset($envolvidos['visita']))
                                    @foreach($envolvidos['visita'] as $visita)
                                        {{$visita->nome.", "}}
                                    @endforeach
                                @endif
                            @endforeach
                        @endif

                    </i>
                </div>

                <div class="col-md-12 mt-4">
                    <div class="float-left  blockquote-footer">
                        <i>{{ $ocorrencia['registro'] }}</i>
                        @if($ocorrencia['atualizacao'] != false)
                            <br>
                            <i class="color_red">- Última atualização em {{ $ocorrencia['atualizacao'] }}</i>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    @endforeach

@endif

<div class="cf"></div>
<h3 class="tituloSeccao">XIII - Escoltas</h3>
@if(count($escolta) == 0)
    -- Sem Informações
@else

    @foreach($escolta as $esc)

        <div class="ocorrencias border1pxDashedCCC paddingBottom15px fontSize-13px mb-3">

            <div class="row ocorrencia">
                <div class="col-md-12">
                    Nº {{ $esc['id'] }} | Tipo: {{ strtoupper($esc['tipo']) }}
                </div>
                <div class="col-md-6">
                    @if($esc['tipo'] != "gep")
                        Data de Saída: {{ $esc['data_saida'] }}  às {{ $esc['hora_saida'] }}
                        <i class="mdi mdi-ray-start-arrow fontSize-18px mr-2 ml-2"></i>
                        Data de Retorno: {{ $esc['data_retorno'] }}  às {{ $esc['hora_retorno'] }}
                    @else
                        Data da Escolta {{ $esc['data_saida'] }}
                    @endif
                </div>
                <div class="col-md-6">
                    {{ $esc['destino'] }}
                </div>
                <div class="cf"></div>
                <div class="col-md-12 mt-3 textP15px">
                    <p class="fontSize-15px">
                        {!! $esc['motivo'] !!}
                    </p>
                </div>
                <div class="col-md-12 fontSize-13px">
                    <i>
                        @if(count($esc['agentes']) > 0)
                            <span>Agentes Envolvidos:</span>
                            @foreach($esc['agentes'] as $agente)
                                {{ $agente['nome'].", " }}
                                {{--@foreach($envolvidos['interno'] as $interno)--}}
                                {{--{{$interno['nome'].", "}}--}}
                                {{--@endforeach--}}
                            @endforeach
                        @endif

                        <br>
                        <span>Internos Envolvidos: </span>
                        @foreach($esc['internos'] as $interno)
                            {{ $interno['nome'].", " }}
                            {{--@if(isset($envolvidos['servidor']))--}}
                            {{--@foreach($envolvidos['servidor'] as $servidor)--}}
                            {{--{{$servidor->nome.", "}}--}}
                            {{--@endforeach--}}
                            {{--@endif--}}
                        @endforeach



                    </i>
                </div>

            </div>
        </div>

    @endforeach


@endif
@if(count($escoltaGep) != 0)
    <br>
    <span style="font-size: 14px">Escoltas realizadas pelo FTPEN</span><br>
    <table style="width: 100%; font-size: 12px !important; " border="1" cellspacing="0" cellpadding="1">
        <thead>
        <tr>
            <th style="width: 10%; text-align: center" scope="col">#</th>
            <th style="width: 30%;" scope="col">Interno</th>
            <th style="width: 15%; text-align: center" scope="col">Saída</th>
            <th style="width: 15%; text-align: center" scope="col">Retorno</th>
            <th style="width: 15%; text-align: center" scope="col">VTR</th>
            <th style="width: 15%; text-align: center" scope="col">Destino</th>
        </tr>
        </thead>
        <tbody>
        @foreach($escoltaGep as $gep)
            <tr>
                <td colspan="6">
                    <table style="width: 100%;" cellspacing="1" cellpadding="1">
                        <tbody>
                        <tr>
                            <td style="width: 10%; text-align: center; border-right: 1px solid #808080">{{ $loop->iteration }}</td>
                            <td style="width: 30%; border-right: 1px solid #808080">{{ $gep->nome }}</td>
                            <td style="width: 15%; text-align: center; border-right: 1px solid #808080;">{{ $gep->data_saida." - ".$gep->hora_saida }}</td>
                            <td style="width: 15%; text-align: center; border-right: 1px solid #808080">{{ $gep->data_retorno." - ".$gep->hora_retorno }}</td>
                            <td style="width: 15%; text-align: center; border-right: 1px solid #808080">{{ $gep->vtr_placa }}</td>
                            <td style="width: 15%; text-align: center; border-right: 1px solid #808080">{{ $gep->destino }}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>

@endif

{{--<h3>XII - Quantitativo Prisional</h3>--}}
{{--@if(empty($qtdPavilhao))--}}
{{---- Sem Informações--}}
{{--@else--}}
{{--{{ dd($qtdPavilhao) }}--}}
{{--@foreach($qtdPavilhao as $qtdPv)--}}

{{--<table style="width: 400px; font-size: 12px !important; border: 1px solid #000; margin-bottom: 10px">--}}
{{--<tbody>--}}
{{--<tr>--}}
{{--<td style="width: 100%; text-align: center; background-color: deepskyblue;" colspan="4"><strong>{{$qtdPv['nomePavilhao']}}</strong></td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td style="width: 30%; text-align: center;"><strong>Ala</strong></td>--}}
{{--<td style="width: 70%;  border-right: 1px solid #000"><strong>Quantidade</strong></td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td colspan="4">--}}
{{--@foreach($qtdAla as $qtdAl)--}}
{{--@if($qtdAl['pv'] == $qtdPv['idPavilhao'])--}}
{{--<table style="width: 100%;">--}}
{{--<tbody>--}}
{{--<tr>--}}
{{--<td style="width: 30%; text-align: center;">{{ $qtdAl['nomeAla'] }}</td>--}}
{{--<td style="width: 70%;">{{ $qtdAl['qtdAla'] }}</td>--}}
{{--</tr>--}}
{{--</tbody>--}}
{{--</table>--}}
{{--@endif--}}
{{--@endforeach--}}
{{--</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td style="width: 85px;"><strong>Total:</strong></td>--}}
{{--<td style="width: 496px;" colspan="3">{{ $qtdPv['qtdPavilhao'] }}</td>--}}
{{--</tr>--}}
{{--</tbody>--}}
{{--</table>--}}

{{--@endforeach--}}

{{--@endif--}}
{{--<h3>Total de Internos na Unidade: {{ $internosUnidadeTotal }}</h3>--}}
{{--<hr>--}}
{{--<br>--}}

<div class="cf"></div>
<h3 class="tituloSeccao">XIV - Visitantes</h3>
@if(json_decode($visitantes) == [])
    -- Sem Informações de visitas a internos
@else
    {{--{{ dd($qtdPavilhao) }}--}}
    <h4>Visitas a Internos</h4>
    <table style="width: 100%; font-size: 12px !important;" border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th style="width: 10%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">#</th>
            <th style="width: 30%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Visitante</th>
            <th style="width: 30%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Interno</th>
            <th style="width: 15%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Hor&aacute;rio Entrada</th>
            <th style="width: 15%; text-align: center; background-color: #CCCCCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Hor&aacute;rio Sa&iacute;da</th>
        </tr>
        </thead>
        <tbody>
        @foreach($visitantes as $visitante)
            <tr>
                <td colspan="5">
                    <table style="width: 100%;" cellspacing="1" cellpadding="1">
                        <tbody>
                        <tr>
                            <td style="width: 10%; text-align: center; border-bottom: 1px solid #CCC">{{ $loop->iteration }}</td>
                            <td style="width: 30%; border-bottom: 1px solid #CCC">{{ $visitante['nomeVisitante'] }}</td>
                            <td style="width: 30%; border-bottom: 1px solid #CCC">{{ $visitante['nomeInterno'] }}</td>
                            <td style="width: 15%; text-align: center; border-bottom: 1px solid #CCC">{{ $visitante['horarioEntrada'] }}</td>
                            <td style="width: 15%; text-align: center; border-bottom: 1px solid #CCC">{{ $visitante['horarioSaida'] }}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>


@endif

@if(json_decode($visitantesExterno) != [])

    <h4>Visitantes Externos</h4>
    <table style="width: 100%; font-size: 12px !important;" border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th style="width: 10%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">#</th>
            <th style="width: 30%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Visitante</th>
            <th style="width: 30%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Motivo</th>
            <th style="width: 15%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Hor&aacute;rio Entrada</th>
            <th style="width: 15%; text-align: center; background-color: #CCCCCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Hor&aacute;rio Sa&iacute;da</th>
        </tr>
        </thead>
        <tbody>
        @foreach($visitantesExterno as $visitantesExt)
            <tr>
                <td colspan="5">
                    <table style="width: 100%;" cellspacing="1" cellpadding="1">
                        <tbody>
                        <tr>
                            <td style="width: 10%; text-align: center; border-bottom: 1px solid #CCC">{{ $loop->iteration }}</td>
                            <td style="width: 30%; border-bottom: 1px solid #CCC">{{ $visitantesExt['nomeVisitante'] }}</td>
                            <td style="width: 30%; border-bottom: 1px solid #CCC">{{ $visitantesExt['motivo'] }}</td>
                            <td style="width: 15%; text-align: center; border-bottom: 1px solid #CCC">{{ $visitantesExt['horarioEntrada'] }}</td>
                            <td style="width: 15%; text-align: center; border-bottom: 1px solid #CCC">{{ $visitantesExt['horarioSaida'] }}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>


@endif

@if(json_decode($visitantesServidor) != [])

    <h4>Servidores</h4>
    <table style="width: 100%; font-size: 12px !important;" border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th style="width: 10%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">#</th>
            <th style="width: 30%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Servidor</th>
            <th style="width: 30%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Motivo</th>
            <th style="width: 15%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Hor&aacute;rio Entrada</th>
            <th style="width: 15%; text-align: center; background-color: #CCCCCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Hor&aacute;rio Sa&iacute;da</th>
        </tr>
        </thead>
        <tbody>
        @foreach($visitantesServidor as $visitantesServ)
            <tr>
                <td colspan="5">
                    <table style="width: 100%;" cellspacing="1" cellpadding="1">
                        <tbody>
                        <tr>
                            <td style="width: 10%; text-align: center; border-bottom: 1px solid #CCC">{{ $loop->iteration }}</td>
                            <td style="width: 30%; border-bottom: 1px solid #CCC">{{ $visitantesServ['nomeVisitante'] }}</td>
                            <td style="width: 30%; border-bottom: 1px solid #CCC">{{ $visitantesServ['motivo'] }}</td>
                            <td style="width: 15%; text-align: center; border-bottom: 1px solid #CCC">{{ $visitantesServ['horarioEntrada'] }}</td>
                            <td style="width: 15%; text-align: center; border-bottom: 1px solid #CCC">{{ $visitantesServ['horarioSaida'] }}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>


@endif

<div class="cf"></div>
<h3 class="tituloSeccao">XV - Advogados</h3>
@if(json_decode($advogados) == [])
    -- Sem Informações
@else

    <table style="width: 100%; font-size: 12px !important;" border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th style="width: 10%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">#</th>
            <th style="width: 30%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Advogado</th>
            <th style="width: 30%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Interno</th>
            <th style="width: 15%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Atendimento</th>
            <th style="width: 15%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Duração</th>
        </tr>
        </thead>
        <tbody>
        @foreach($advogados as $advogado)
            <tr>
                <td colspan="5">
                    <table style="width: 100%;" cellspacing="1" cellpadding="1">
                        <tbody>
                        <tr>
                            <td style="width: 10%; text-align: center; border-bottom: 1px solid #CCCCCC">{{ $loop->iteration }}</td>
                            <td style="width: 30%; border-bottom: 1px solid #CCCCCC">{{ $advogado['nomeAdvogado'] }}</td>
                            <td style="width: 30%; border-bottom: 1px solid #CCCCCC">{{ $advogado['nomeInterno'] }}</td>
                            <td style="width: 15%; text-align: center; border-bottom: 1px solid #CCCCCC">{{ $advogado['inicioAtendimento']." - ".$advogado['terminoAtendimento'] }}</td>
                            <td style="width: 15%; text-align: center; border-bottom: 1px solid #CCCCCC">{{ $advogado['duracao'] }}min</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>


@endif

<div class="cf"></div>
<h3 class="tituloSeccao">XVI - Entrada de Internos na Unidade</h3>
@if($entradaInterno == [])
    -- Sem Informações
@else

    <table style="width: 100%; font-size: 12px !important;" border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th style="width: 5%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">#</th>
            <th style="width: 40%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Interno</th>
            <th style="width: 10%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Prontuário</th>
            <th style="width: 20%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Origem</th>
            <th style="width: 20%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Tipo</th>
            <th style="width: 5%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Hora</th>
        </tr>
        </thead>
        <tbody>
        @foreach($entradaInterno as $entrada)
            <tr>
                <td colspan="6">
                    <table style="width: 100%;" cellspacing="1" cellpadding="1">
                        <tbody>
                        <tr>
                            <td style="width: 5%; text-align: center; border-bottom: 1px solid #CCCCCC">{{ $loop->iteration }}</td>
                            <td style="width: 40%; border-bottom: 1px solid #CCCCCC">{{ $entrada['nome'] }}</td>
                            <td style="width: 10%; text-align: center; border-bottom: 1px solid #CCCCCC">{{ $entrada['prontuario'] }}</td>
                            <td style="width: 20%; text-align: center; border-bottom: 1px solid #CCCCCC; font-size: 10px !important;">{{ $entrada['pvAnteior']." - ".$entrada['alaAnteior']." - ".$entrada['celaAnteior'] }}</td>
                            <td style="width: 20%; text-align: center; border-bottom: 1px solid #CCCCCC; font-size: 10px !important;">{{ strtoupper($entrada['tipo']) }}</td>
                            <td style="width: 5%; text-align: center; border-bottom: 1px solid #CCCCCC; font-size: 10px !important;">{{ strtoupper($entrada['horario']) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>

@endif

<div class="cf"></div>
<h3 class="tituloSeccao">XVII - Saída de Internos na Unidade</h3>
@if($saidaInterno == [])
    -- Sem Informações
@else

    <table style="width: 100%; font-size: 12px !important;" border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th style="width: 5%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">#</th>
            <th style="width: 40%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Interno</th>
            <th style="width: 10%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Prontuário</th>
            <th style="width: 20%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Origem</th>
            <th style="width: 20%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Tipo</th>
            <th style="width: 5%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Hora</th>
        </tr>
        </thead>
        <tbody>
        @foreach($saidaInterno as $saida)
            <tr>
                <td colspan="6">
                    <table style="width: 100%;" cellspacing="1" cellpadding="1">
                        <tbody>
                        <tr>
                            <td style="width: 5%; text-align: center; border-bottom: 1px solid #CCCCCC">{{ $loop->iteration }}</td>
                            <td style="width: 40%; border-bottom: 1px solid #CCCCCC">{{ $saida['nome'] }}</td>
                            <td style="width: 10%; text-align: center; border-bottom: 1px solid #CCCCCC">{{ $saida['prontuario'] }}</td>
                            <td style="width: 20%; text-align: center; border-bottom: 1px solid #CCCCCC; font-size: 10px !important;">{{ $saida['pvAnteior']." - ".$saida['alaAnteior']." - ".$saida['celaAnteior'] }}</td>
                            <td style="width: 20%; text-align: center; border-bottom: 1px solid #CCCCCC; font-size: 10px !important;">{{ strtoupper($saida['tipo']) }}</td>
                            <td style="width: 5%; text-align: center; border-bottom: 1px solid #CCCCCC; font-size: 10px !important;">{{ strtoupper($saida['horario']) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>

@endif


<div class="cf"></div>
<h3 class="tituloSeccao">XVIII - Movimentação Interna</h3>
@if($dadosMudancaCela == [])
    -- Sem Informações
@else

    <table style="width: 100%; font-size: 12px !important;" border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th style="width: 5%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">#</th>
            <th style="width: 30%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Interno</th>
            <th style="width: 10%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Prontuário</th>
            <th style="width: 25%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Origem</th>
            <th style="width: 25%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Destino</th>
            <th style="width: 5%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Hora</th>
        </tr>
        </thead>
        <tbody>
        @foreach($dadosMudancaCela as $mudancaCela)
            <tr>
                <td colspan="6">
                    <table style="width: 100%;" cellspacing="1" cellpadding="1">
                        <tbody>
                        <tr>
                            <td style="width: 5%; text-align: center; border-bottom: 1px solid #CCCCCC">{{ $loop->iteration }}</td>
                            <td style="width: 30%; border-bottom: 1px solid #CCCCCC">{{ $mudancaCela['nome'] }}</td>
                            <td style="width: 10%; text-align: center; border-bottom: 1px solid #CCCCCC">{{ $mudancaCela['prontuario'] }}</td>
                            <td style="width: 25%; text-align: center; border-bottom: 1px solid #CCCCCC; font-size: 10px !important;">{{ $mudancaCela['pvAnteior']." - ".$mudancaCela['alaAnteior']." - ".$mudancaCela['celaAnteior'] }}</td>
                            <td style="width: 25%; text-align: center; border-bottom: 1px solid #CCCCCC; font-size: 10px !important;">{{ $mudancaCela['pvDestino']." - ".$mudancaCela['alaDestino']." - ".$mudancaCela['celaDestino'] }}</td>
                            <td style="width: 5%; text-align: center; border-bottom: 1px solid #CCCCCC; font-size: 10px !important;">{{ $mudancaCela['horario'] }}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>


@endif

<div class="cf"></div>
<h3 class="tituloSeccao">XVIV - Armamento, Munições e Demais Materiais</h3>
@if(empty(json_decode($materialBelico, true)))
    -- Sem Informações
@else

    @foreach($materialBelico as $matBel)
        <a href="{{ route('ocorrencia.editar', ['id'=>$matBel->id, 'returnLivro' => true]) }}" title="Alterar Ocorrência" target="_self" class=""><i class="mdi mdi-pencil-circle-outline fontSize-20px color_BE8900"></i> </a>

        <div class="fontSize-13px">
            {!! $matBel->descricao !!}
        </div>

    @endforeach

@endif

<div class="cf"></div>
<br>
<h3 class="tituloSeccao">XVIV - Atendimentos Assistenciais</h3>
@if(count($atendimentosSaudeAssistencia) == 0)
    -- Sem Informações <br>
@else

    <table style="width: 100%; font-size: 12px !important; " border="1" cellspacing="0" cellpadding="1">
        <thead>
        <tr>
            <th style="width: 5%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">#</th>
            <th style="width: 30%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Interno</th>
            <th style="width: 10%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Prontuário</th>
            <th style="width: 25%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Setor</th>
            <th style="width: 25%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Localização</th>
            <th style="width: 5%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Hora</th>
        </tr>
        </thead>
        <tbody>
        @foreach($atendimentosSaudeAssistencia as $atendimento)
            <tr>
                <td colspan="6">
                    <table style="width: 100%;" cellspacing="1" cellpadding="1">
                        <tbody>
                        <tr>
                            <td style="width: 5%; text-align: center; border-right: 1px solid #808080">{{ $loop->iteration }}</td>
                            <td style="width: 30%; border-right: 1px solid #808080">{{ $atendimento['internoNome'] }}</td>
                            <td style="width: 10%; text-align: center; border-right: 1px solid #808080">{{ $atendimento['internoProntuario'] }}</td>
                            <td style="width: 25%; text-align: center; border-right: 1px solid #808080">{{ $atendimento['setor'] }}</td>
                            <td style="width: 25%; text-align: center; border-right: 1px solid #808080">{{ $atendimento['internoLocalizacao'] }}</td>
                            <td style="width: 5%; text-align: center; border-right: 1px solid #808080">{{ $atendimento['horarioAtendimento'] }}</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>

@endif
<hr>
<br>

<div class="page"></div>
<h3 class="tituloSeccao">XX - Quantitativo Prisional</h3>
@if(empty($qtdPavilhao))
    -- Sem Informações
@else
    {{--{{ dd($qtdPavilhao) }}--}}
    @foreach($qtdPavilhao as $qtdPv)

        <div class="divQtd" style="border: 1px solid #000; width: 45%; float: left; margin-left: 10px; margin-bottom: 10px; clear: left;">
            <div style="width: 100%; height: 10px; background-color: #1c2331; border: 1px solid #1c2331; color: #FFF; padding: 2px 0">
                <strong>{{$qtdPv['nomePavilhao']}} | Total: {{$qtdPv['qtdPavilhao']}}</strong>
            </div>
            @foreach($qtdAla as $qtdAl)
                @if($qtdAl['pv'] == $qtdPv['idPavilhao'])
                    <div style="width: 100%; height: 10px; background-color: #CCC; color: #000; border: 1px solid #CCCCCC;">
                        <strong>{{ $qtdAl['nomeAla'] }} | Total: {{ $qtdAl['qtdAla'] }}</strong>
                    </div>

                    @foreach($qtdCela as $qtdCel)
                        @if($qtdCel['ala'] == $qtdAl['alaId'])
                            <div style="width: 100%; height: 10px; padding: 2px 0; border-bottom: 1px solid #CCCCCC; margin-left: 3px">
                                @if($qtdCel['qtdCela'] == 0)
                                    {{ $qtdCel['nomeCela'] }}: -- vazia --
                                @else
                                    {{ $qtdCel['nomeCela'] }}: {{ $qtdCel['qtdCela'] }}
                                @endif
                            </div>

                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

    @endforeach

@endif



</body>
