
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cautela de Armas e Equipamentos</title>
    <style>
        body{
            font-family:Verdana;
        }

        div.page
        {
            page-break-after: always;
            page-break-inside: avoid;
        }

    </style>
</head>
<body>

<div class="certificado">
    <div id="header" style="margin-bottom: 30px;">
        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="width: 55px;">
                    <img src="{{public_path('imagens/template/logo_estado.png')}}" height="85" alt="">
                </td>
                <td style="width: 485px; text-align: center;">
                    <h5 style="width: 100%; font-size: 16px; font-weight: bold; text-align: center; font-family: Arial; letter-spacing: 1px">GOVERNO DO ESTADO DO
                        ACRE</h5>
                    <h6 style="width: 100%; font-size: 14px; text-align: center; margin-top: -20px;  font-family: Arial; letter-spacing: 1px">{{ config('app.secretaria_extenso') }} - {{ config('app.secretaria') }}</h6>
                    <h6 style="width: 100%; font-size: 14px; text-align: center; margin-top: -25px;  font-family: Arial; letter-spacing: 1px">SETOR BÉLICO/ARMARIA</h6>
                </td>
                <td style="width: 55px;">
                    <img src="{{public_path('imagens/template/logo_secretaria.png')}}" height="65" alt="">
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="width:100%; border-top: 1px solid #CCC; margin-top: 5px; height: 15px"></div>

    {{--<div style="font-size: 15px; font-weight: bold; text-align: center">Termo de Responsabilidade de Cautela Especial de Arma de Fogo</div>--}}
    <table style="width: 100%; border: 1px solid #ccc; padding: 5px" cellpadding="5">
        <tr>
            <td style="width: 10px; text-align: center; font-size: 13px; font-weight: bold;" colspan="2">
                TERMO DE DESCAUTELAMENTO Nº 00{{$numeroCautela}}
                @if($cautelaPara == "unidade")
                    <br>Cautela para Unidade Prisional
                @endif

            </td>
        </tr>
        <tbody style="font-size: 13px">
        <tr>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC"><strong>Processo nº:</strong> 00{{$numeroCautela}}</td>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC; border-left: 1px solid #CCCCCC">Data do Processo: {{$dataDescautela}} </td>
        </tr>
        {{-- responsaveis --}}
        @if(!empty($nomeUnidade))
            <tr>
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC" colspan="2"><strong>Unidade:</strong> {{ $nomeUnidade }}</td>
            </tr>
        @endif
        <tr>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC" colspan="2"><strong>Responsável:</strong> {{$responsavel[0]["nome"]}}</td>
        </tr>
        {{-- se cautela for diferente de outros --}}
        @if($cautelaPara != "outros")
            <tr>
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC"><strong>Matrícula nº:</strong> {{$responsavel[0]["matricula"]}}</td>
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC; border-left: 1px solid #CCCCCC"><strong>CPF nº:</strong> {{ $responsavel[0]["cpf"] }} </td>
            </tr>
            <tr>
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC" colspan="2"><strong>Lotação:</strong> {{ $responsavel[0]["lotacao"]}}</td>
            </tr>
            <tr>
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC" colspan="2"><strong>Endereço:</strong>
                    @if(!empty($responsavel[0]["endereco"]))
                        {{ $responsavel[0]["endereco"]}}
                    @else
                        ** ENDREÇO NÃO INFORMADO AO RH-SEAP **
                    @endif
                </td>
            </tr>
            @if($responsavel[0]["fone"] != false)
                <tr>
                    @foreach($responsavel[0]["fone"] as $f)
                        <td style="width: 50%; border-bottom: 1px solid #CCCCCC"><strong>Telefone: </strong>{{ $f['numero'] }}</td>
                    @endforeach
                </tr>
            @else
                <tr>
                    <td style="width: 50%; border-bottom: 1px solid #CCCCCC"><strong>Telefone: </strong> ** NÃO INFORMADO AO RH-SEAP **</td>
                </tr>
            @endif
        @endif
        {{-- !se cautela for diferente de outros --}}

        {{-- se cautela for igual outros --}}
        @if($cautelaPara == "outros")
            <tr>
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC"><strong>CPF nº:</strong> {{$responsavel[0]["cpf"]}}</td>
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC; border-left: 1px solid #CCCCCC"><strong>Órgão:</strong> {{ $responsavel[0]["orgao"] }} </td>
            </tr>
            <tr>
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC"><strong>Telefone: </strong>{{$responsavel[0]["telefone"]}}</td>
            </tr>
        @endif
        {{-- !se cautela for igual outros --}}

        <tr>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: justify; font-size: 14px" colspan="2">
                <p style="text-indent: 5em; text-align: justify">
                @if($cautelaPara != "outros")
                    @if($cautelaPara == "unidade")
                    Pelo presente termo venho, em nome da Armaria do Sistema Prisional do Estado da {{ config('app.uf_extenso') }}, atestar o <strong>DESCAUTELAMENTO</strong> do material abaixo listado anteriormente cautelado para <strong>{{$nomeUnidade}}</strong>.
                    @endif

                    @if($cautelaPara == "servidor")
                    Pelo presente termo venho, em nome da Armaria do Sistema Prisional do Estado da {{ config('app.uf_extenso') }}, atestar o <strong>DESCAUTELAMENTO</strong> do material abaixo listado anteriormente cautelado para o SERVIDOR(A) <strong>{{$responsavel[0]["nome"]}}</strong>,
                        @if($cautelaPara)
                                <strong>MATRÍCULA. nº {{ $responsavel[0]["matricula"] }}</strong>.
                        @endif
                    @endif
                @endif
                    @if($cautelaPara == "outros")
                        Pelo presente termo venho, em nome da Armaria do Sistema Prisional do Estado da {{ config('app.uf_extenso') }}, atestar o <strong>DESCAUTELAMENTO</strong> do material abaixo listado anteriormente cautelado para o Sr.(a)  <strong>{{$responsavel[0]["nome"]}}</strong>,
                        @if($cautelaPara == "outros")
                            <strong>CPF. nº {{ $responsavel[0]["cpf"] }}</strong>.
                        @endif
                    @endif

                </p>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: left; font-size: 13px; font-weight: bold" colspan="2">
                EQUIPAMENTOS DESCAUTELADOS:
            </td>
        </tr>
        @foreach($equipamentos as $equipamento)
            <tr>
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: left; font-size: 12px; font-weight: bold; font-style: italic" colspan="2">
                    {{ strtoupper($equipamento[0]['equipamento']) }}
                    <br>

                </td>
            </tr>
        @endforeach
        @if(!is_null($equipamentosPermanecemCautelados))
            <tr>
                <td style="width: 50%; text-align: left; font-size: 11px; font-weight: bold" colspan="2">
                </td>
            </tr>
            <tr>
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: left; font-size: 11px; font-weight: bold" colspan="2">
                    EQUIPAMENTOS QUE PERMANECERAM CAUTELADOS:
                </td>
            </tr>
            @foreach($equipamentosPermanecemCautelados as $equipamentoPermaneceram)
                <tr>
                    <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: left; font-size: 11px; font-weight: bold; font-style: italic" colspan="2">
                        {{ mb_strtoupper($loop->iteration."º - ".$equipamentoPermaneceram['equipamento']) }}

                        <br>

                    </td>
                </tr>
            @endforeach
        @endif

        {{--<tr>--}}
        {{--<td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: center; font-size: 13px; font-weight: bold" colspan="2">--}}
        {{--** CAUTELA **--}}
        {{--</td>--}}
        {{--</tr>--}}
        {{--@if(!is_null($obs ))--}}
            {{--<tr>--}}
                {{--<td style="width: 50%; text-align: left; font-size: 13px; font-weight: bold" colspan="2">--}}
                    {{--OBSERVAÇÃO:--}}
                {{--</td>--}}
            {{--</tr>--}}

            {{--<tr>--}}
                {{--<td style="border-bottom: 1px solid #CCCCCC;" colspan="2">--}}
                    {{--{{ $obs }}--}}
                {{--</td>--}}
            {{--</tr>--}}
        {{--@endif--}}
        <tr>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: left; font-size: 13px; font-style: italic" colspan="2">
                Li e estou de acordo com os itens citados neste termo de descautela.
            </td>
        </tr>

        <tr>
            <td style="width: 50%; text-align: right; font-size: 13px; font-style: italic" colspan="2">
                {{ $localData }}
            </td>
        </tr>

        @if($cautelaPara != "outros")
            <tr>
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: center; font-size: 10px;" colspan="2">
                    <br><br>
                    ____________________________________________________________<br><br>
                    Ass. {{ $dados[0]->nome }} ({{$responsavel[0]["matricula"]}})<br><br><br>
                </td>
            </tr>
        @else
            <tr>
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: center; font-size: 10px;" colspan="2">
                    <br><br>
                    ____________________________________________________________<br><br>
                    Ass. {{$responsavel[0]["nome"]}} ({{$responsavel[0]["cpf"]}})<br><br><br>
                </td>
            </tr>

        @endif

        <tr>
            <td style="width: 50%; text-align: center; font-size: 10px;" colspan="2">
                <br><br>
                ____________________________________________________________<br><br>
                Ass. JOSÉ CARLOS DE LIMA (169.084-1) <br>
                Diretor do Setor Bélico
                <br><br>
            </td>
        </tr>


        </tbody>
    </table>
</div><!--Certificado-->
@if(!is_null($equipamentosPermanecemCautelados))
    <div class="page"></div>

    <div class="certificado">
        <div id="header" style="margin-bottom: 30px;">
            <table style="width: 100%;">
                <tbody>
                <tr>
                    <td style="width: 55px;">
                        <img src="{{public_path('imagens/template/logo_estado.png')}}" height="85" alt="">
                    </td>
                    <td style="width: 485px; text-align: center;">
                        <h5 style="width: 100%; font-size: 16px; font-weight: bold; text-align: center; font-family: Arial; letter-spacing: 1px">GOVERNO DO ESTADO DO
                            {{ config('app.uf_extenso') }}</h5>
                        <h6 style="width: 100%; font-size: 14px; text-align: center; margin-top: -20px;  font-family: Arial; letter-spacing: 1px">{{ config('app.secretaria_extenso') }} - {{ config('app.secretaria') }}</h6>
                        <h6 style="width: 100%; font-size: 14px; text-align: center; margin-top: -25px;  font-family: Arial; letter-spacing: 1px">SETOR BÉLICO/ARMARIA</h6>
                    </td>
                    <td style="width: 55px;">
                        <img src="{{public_path('imagens/template/logo_secretaria.png')}}" height="65" alt="">
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div style="width:100%; border-top: 1px solid #CCC; margin-top: 5px; height: 15px"></div>

        {{--<div style="font-size: 15px; font-weight: bold; text-align: center">Termo de Responsabilidade de Cautela Especial de Arma de Fogo</div>--}}
        <table style="width: 100%; border: 1px solid #ccc; padding: 5px" cellpadding="5">
            <tr>
                <td style="width: 10px; text-align: center; font-size: 13px; font-weight: bold;" colspan="2">
                    TERMO DE RESPONSABILIDADE CAUTELA DE EQUIPAMENTO Nº 00{{$numeroCautela}}
                    @if($cautelaPara == "unidade")
                        <br>Cautela para Unidade Prisional
                    @endif

                </td>
            </tr>
            <tbody style="font-size: 13px">
            <tr style="">
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC"><strong>Processo nº:</strong> 00{{$numeroCautela}}</td>
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC; border-left: 1px solid #CCCCCC">Data do Processo: {{$dataCautela}} </td>
            </tr>
            {{-- responsaveis --}}
            @if(!empty($nomeUnidade))
                <tr>
                    <td style="width: 50%; border-bottom: 1px solid #CCCCCC" colspan="2"><strong>Unidade:</strong> {{ $nomeUnidade }}</td>
                </tr>
            @endif
            <tr>
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC" colspan="2"><strong>Responsável:</strong> {{$responsavel[0]["nome"]}}</td>
            </tr>
            {{-- se cautela for diferente de outros --}}
            @if($cautelaPara != "outros")
                <tr>
                    <td style="width: 50%; border-bottom: 1px solid #CCCCCC"><strong>Matrícula nº:</strong> {{$responsavel[0]["matricula"]}}</td>
                    <td style="width: 50%; border-bottom: 1px solid #CCCCCC; border-left: 1px solid #CCCCCC"><strong>CPF nº:</strong> {{ $responsavel[0]["cpf"] }} </td>
                </tr>
                <tr>
                    <td style="width: 50%; border-bottom: 1px solid #CCCCCC" colspan="2"><strong>Lotação:</strong> {{ $responsavel[0]["lotacao"]}}</td>
                </tr>
                <tr>
                    <td style="width: 50%; border-bottom: 1px solid #CCCCCC" colspan="2"><strong>Endereço:</strong>
                        @if(!empty($responsavel[0]["endereco"]))
                            {{ $responsavel[0]["endereco"]}}
                        @else
                            ** ENDREÇO NÃO INFORMADO AO RH-SEAP **
                        @endif
                    </td>
                </tr>
                @if($responsavel[0]["fone"] != false)
                    <tr>
                        @foreach($responsavel[0]["fone"] as $f)
                            <td style="width: 50%; border-bottom: 1px solid #CCCCCC"><strong>Telefone: </strong>{{ $f['numero'] }}</td>
                        @endforeach
                    </tr>
                @else
                    <tr>
                        <td style="width: 50%; border-bottom: 1px solid #CCCCCC"><strong>Telefone: </strong> ** NÃO INFORMADO AO RH-SEAP **</td>
                    </tr>
                @endif
            @endif
            {{-- !se cautela for diferente de outros --}}

            {{-- se cautela for igual outros --}}
            @if($cautelaPara == "outros")
                <tr>
                    <td style="width: 50%; border-bottom: 1px solid #CCCCCC"><strong>CPF nº:</strong> {{$responsavel[0]["cpf"]}}</td>
                    <td style="width: 50%; border-bottom: 1px solid #CCCCCC; border-left: 1px solid #CCCCCC"><strong>Órgão:</strong> {{ $responsavel[0]["orgao"] }} </td>
                </tr>
                <tr>
                    <td style="width: 50%; border-bottom: 1px solid #CCCCCC"><strong>Telefone: </strong>{{$responsavel[0]["telefone"]}}</td>
                </tr>
            @endif
            {{-- !se cautela for igual outros --}}

            <tr>
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC" colspan="2"><strong>Período da Cautela:  </strong>{{ $periodoCautela }}
                </td>
            </tr>
            <tr>
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: justify; font-size: 11px" colspan="2">
                    @if($cautelaPara == "unidade")

                        I – <strong>Termo de Responsabilidade:</strong> Recebo o material bélico em perfeito estado de funcionamento e assumo total responsabilidade pela guarda, conservação e manutenção do mesmo, estando sujeito as sanções administrativas, cíveis e criminais quanto a sua inobservância. Comprometendo-me ao ressarcimento do valor atual do material bélico, para a {{ config('app.secretaria_extenso') }} – SEAP do Estado da {{ config('app.uf_extenso') }}.
                        I.I – Em caso de dano, roubo, furto na sua forma simples ou qualificada, ou qualquer outra forma de extravio, por dolo ou culpa, caso fortuito ou força maior, enquadra-se ao ressarcimento conforme item anterior;
                        I.II – Realizar o registro de Boletim de Ocorrência junto aos órgãos competentes quando efetuado(s) disparo(s) com a arma de fogo e as munições desta Secretaria e encaminhar cópia ao Setor Bélico/Armaria.
                        II	– Desde já fica Notificado(a) o(a) agente penitenciário(a) da entrega do material cautelado quando solicitado pelo Setor bélico/Armaria e/ou nos casos da cessação ou suspensão da cautela e/ou suspensão ou perda do porte de arma de fogo, será obrigatório a imediata descautela do material abaixo especificado junto ao Setor Bélico/Armaria, sob pena de responsabilização administrativa, cível e criminal.
                        III – A direção se compromete a realizar o levantamento do inventário bélico a de forma quadrimestral, o qual deverá ser enviado para Setor Bélico/Armaria via SEI. Além disso, será necessário tal levantamento quando ocorrer mudança de direção ou quando solicitado diretamente pelo Setor Bélico/Armaria.
                        IV – Salientamos que o uso do material bélico pertencente a unidade é de uso exclusivo em serviço. Não podendo ser usado em caráter particular ou fora do serviço da unidade.


                    @else
                        I – <strong>Termo de Responsabilidade:</strong> Recebo o material bélico em perfeito estado de funcionamento e assumo total responsabilidade pela guarda, conservação e manutenção do mesmo, estando sujeito as sanções administrativas, cíveis e criminais quanto a sua inobservância. Comprometendo-me ao ressarcimento do valor atual do material bélico, para a {{ config('app.secretaria_extenso') }} – {{ config('app.secretaria') }} do Estado da {{ config('app.uf_extenso') }}.

                        I.I – Em caso de dano, roubo, furto na sua forma simples ou qualificada, ou qualquer outra forma de extravio, por dolo ou culpa, caso fortuito ou força maior, enquadra-se ao ressarcimento conforme item anterior;
                        I.II – Realizar o registro de Boletim de Ocorrência junto aos órgãos competentes quando efetuado(s) disparo(s) com a arma de fogo e as munições desta Secretaria e encaminhar cópia ao Setor Bélico/Armaria.
                        II	– Desde já fica Notificado(a) o(a) agente penitenciário(a) da entrega do material cautelado quando solicitado pelo Setor bélico/Armaria e/ou nos casos da cessação ou suspensão da cautela e/ou suspensão ou perda do porte de arma de fogo, será obrigatório a imediata descautela do material abaixo especificado junto ao Setor Bélico/Armaria, sob pena de responsabilização administrativa, cível e criminal.

                        III – O servidor deverá apresentar o material bélico a Armaria sempre que solicitado.

                    @endif
                </td>
            </tr>
            <tr>
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: left; font-size: 13px; font-weight: bold" colspan="2">
                    EQUIPAMENTOS:
                </td>
            </tr>
            @foreach($equipamentosPermanecemCautelados as $equipamento)
                <tr>
                    <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: left; font-size: 12px; font-weight: bold; font-style: italic" colspan="2">

                        {{ mb_strtoupper($loop->iteration."º - ".$equipamento['equipamento']) }}
                        <br>

                    </td>
                </tr>
            @endforeach
            {{--<tr>--}}
            {{--<td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: center; font-size: 13px; font-weight: bold" colspan="2">--}}
            {{--** CAUTELA **--}}
            {{--</td>--}}
            {{--</tr>--}}
            @if(!is_null($obs ))
                <tr>
                    <td style="width: 50%; text-align: left; font-size: 13px; font-weight: bold" colspan="2">
                        OBSERVAÇÃO:
                    </td>
                </tr>

                <tr>
                    <td style="border-bottom: 1px solid #CCCCCC;" colspan="2">
                        {{ $obs }}
                    </td>
                </tr>
            @endif
            <tr>
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: left; font-size: 13px; font-style: italic" colspan="2">
                    Li e estou de acordo com os itens citados neste termo de cautela.
                </td>
            </tr>

            <tr>
                <td style="width: 50%; text-align: right; font-size: 13px; font-style: italic" colspan="2">
                    {{ $localData }}
                </td>
            </tr>

            @if($cautelaPara != "outros")
                <tr>
                    <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: center; font-size: 10px;" colspan="2">
                        <br><br>
                        ____________________________________________________________<br><br>
                        Ass. {{ utf8_encode($dados[0]->nome) }} ({{$responsavel[0]["matricula"]}})<br><br><br>
                    </td>
                </tr>
            @else
                <tr>
                    <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: center; font-size: 10px;" colspan="2">
                        <br><br>
                        ____________________________________________________________<br><br>
                        Ass. {{$responsavel[0]["nome"]}} ({{$responsavel[0]["cpf"]}})<br><br><br>
                    </td>
                </tr>

            @endif

            <tr>
                <td style="width: 50%; text-align: center; font-size: 10px;" colspan="2">
                    <br><br>
                    ____________________________________________________________<br><br>
                    Ass. JOSÉ CARLOS DE LIMA (169.084-1) <br>
                    Diretor do Setor Bélico
                    <br><br>
                </td>
            </tr>


            </tbody>
        </table>
    </div><!--Certificado-->

@endif

</body>
</html>
