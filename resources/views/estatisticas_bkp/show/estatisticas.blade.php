@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-table"></i> Estatísticas da {{ config('app.secretaria') }} {{ config('app.uf') }} - Tabelas</h3>
        <hr>
        <p>
            Tabelas com dados estatísticos relacionados aos internos ativos, atualizados em tempo real, relativos ao {{ config('app.sistema_estado') }}, a confiabilidade dos dados abaixo depende da inserção manual deles nos perfis de cada interno, portanto pode haver eventuais inconsistências.
        </p>
    </div>

    @php

    $secretario = \App\Http\Controllers\Facade\FacadeController::isLogadoSecretario();

    $quantidade = 0;

    $maxValue = 0;

    $adjunto = \App\Http\Controllers\Facade\FacadeController::isLogadoAdjunto();

    $siglaUpLogado = \App\Http\Controllers\Facade\FacadeController::getLotacaoServidor(\Illuminate\Support\Facades\Auth::user()->cpf, "LOTAÇÃO NO RH");

    function removeAccents($string) {
        $string = utf8_encode($string);
        return strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8'))), ' '));
    }

    @endphp

<div style="display: inline;">
<form action="{{ route('estatisticas.show') }}" method="post" enctype="multipart/form-data">
        {!! csrf_field() !!}
          <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="unidade_prisional">Unidade Prisional</label>
                <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">
                    <option value="">
                        Todas
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}">
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3 displayNone" id="divPavilhao">
                <label for="pavilhao">Pavilhão</label>
                <select id="pavilhao" name="pavilhao" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3 displayNone" id="divAla">
                <label for="ala">Ala</label>
                <select id="ala" name="ala" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3 displayNone" id="divCela">
                <label for="cela">Cela</label>
                <select id="cela" name="cela" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>
        </div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary"><i class="fas fa-redo"></i> Recalcular Estatísticas</button>
</form>
<br>
<br>
<form action="{{ route('estatistica.internoPdf') }}" method="post">
    {!! csrf_field() !!}
    <button type="submit" class="btn btn-primary"><i class="fas fa-print"></i> Imprimir Estatística</button>
</form>
</div>


    <hr/>


    <h4>Informações estatísticas
        @if(is_null($unidadeSigla)) da {{ config('app.secretaria') }} {{ config('app.uf') }} @else da unidade: {{$unidadeSigla}}@endif
        @if(!is_null($pavilhaoNome)), pavilhão: {{$pavilhaoNome}}@endif
        @if(!is_null($alaNome)), ala: {{$alaNome}}@endif
        @if(!is_null($celaNome))cela: {{$celaNome}}@endif
        coletadas em {{ $dataHora }}.</h4>

    <div id="resultados">
        <br>
        <h5>Internos Por Unidade
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Unidade</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total Unidade</th>
                @php
                    $qntGeralUnidaHA = 0;
                    $qntGeralUnidaMA = 0;
                    $qntGeralUnidaGeral = 0;
                @endphp
                <tbody>
                @foreach($qtdUnidadeTotal as $dado)
                    <tr class="">
                        <td>{{$dado->sigla}}</td>
                        @php
                            $quantidade = 0;
                        @endphp
                        <td>
                            @foreach($qtdUnidadeHA as $qtdUHA)
                                @if($qtdUHA->sigla == $dado->sigla)
                                    @php
                                        $quantidade = $qtdUHA->qtd;
                                        break;
                                    @endphp
                                @endif
                            @endforeach
                            {{$quantidade}}
                            @php
                                $qntGeralUnidaHA = $qntGeralUnidaHA + $quantidade;
                            @endphp
                        </td>
                        @php
                            $quantidade = 0;
                        @endphp
                        <td>
                            @foreach($qtdUnidadeMA as $qtdUMA)
                                @if($qtdUMA->sigla == $dado->sigla)
                                    @php
                                        $quantidade = $qtdUMA->qtd;
                                        break;
                                    @endphp
                                @endif
                            @endforeach
                            {{$quantidade}}
                            @php
                                $qntGeralUnidaMA = $qntGeralUnidaMA + $quantidade;
                            @endphp
                        </td>
                        <td>{{$dado->qtd}}</td>
                        @php
                            $qntGeralUnidaGeral = $qntGeralUnidaGeral + $dado->qtd;
                        @endphp
                        @php
                            if($dado->qtd > $maxValue){
                                $maxValue = $dado->qtd;
                            }
                        @endphp
                    </tr>
                @endforeach
                <tr>
                    <td>
                        TOTAL GERAL
                    </td>
                    <td>
                        {{$qntGeralUnidaHA}}
                    </td>
                    <td>
                        {{$qntGeralUnidaMA}}
                    </td>
                    <td>
                        {{$qntGeralUnidaGeral}}
                    </td>
                </tr>
            </table>
        </div>
        {{-- Gráfico --}}
        <i class="mdi mdi-poll-box graficoServidorUnidade" style="cursor: pointer;color: blue;"> Exibir Gráficos </i>
        <div  id="imagemodal" class="displayNone">
            <div  id="containercanvas" style="background-color: white;">
                <div>

                    <div>
                        <div>
                            <canvas id="myCanvas" height="520" style="border:0.1em solid #d3d3d3;margin-left: 50px; margin-top:15px; "></canvas>
                        </div>

                        <script>
                            var c = document.getElementById("myCanvas");

                            //CONFIGURA AS VARIAVEIS
                            var tamanhotela = window.innerWidth;
                            var larguraconluna = 40;
                            var tamanhominimo = 50 + (20 + 10) * parseInt('{{count($qtdUnidadeTotal)}}',10);
                            var espacoinicial = 45;
                            var espaco = 50;

                            if(tamanhotela < 1300 && tamanhotela > 1100){
                                larguraconluna = 25;
                                espaco = 35;
                            }

                            var tamanhomaximo = 50 + (larguraconluna + 10) * parseInt('{{count($qtdUnidadeTotal)}}',10);

                            c.setAttribute("width", tamanhomaximo);
                            var ctx = c.getContext("2d");
                            var maxvalue = '{{\App\Http\Controllers\estatisticas\EstatisticasController::arredondarEscala($maxValue,9)}}';
                            maxvalue = parseInt(maxvalue, 10);


                            ctx.font = "15px Arial";

                            var contador = maxvalue*9;
                            var intervalo = 40;

                            //DESENHA AS LINHAS E OS NÚMEROS
                            for(var i=0; i<400; i=i+intervalo){
                                ctx.moveTo(40,i+30);
                                ctx.lineTo(tamanhomaximo,i+30);
                                ctx.fillText(contador, 0, i+35);
                                contador = contador - maxvalue;
                            }
                            ctx.stroke();

                            var cores = ['#308ee0', '#e65251','#9bbb58', '#fb9678', '#58d8a3', '#8862e0','#57c7d4','#ffaf00', '#ab8ce4', '#6c757d', '#292b2c', '#00aeef'];
                            var proximacor = 0;
                            var resto = cores.length;
                            var indicemax = 390;
                            var escala = intervalo/maxvalue;
                            var primeiracoluna = false;

                            ctx.font = "12px Arial";

                            //DESENHA AS COLUNAS
                            @foreach($qtdUnidadeTotal as $dado){
                                ctx.fillStyle = cores[proximacor];
                                resultado = '{{$dado->qtd}}';
                                var posicao = indicemax - parseInt(resultado, 10)*escala;
                                ctx.fillRect(espacoinicial, posicao, larguraconluna, 390-posicao);
                                proximacor = (proximacor+1)%resto;

                                ctx.save();
                                ctx.fillStyle = 'black';
                                ctx.translate(espacoinicial + larguraconluna/2, 400);
                                ctx.rotate(Math.PI/4);
                                ctx.fillText('{{$dado->sigla}}', 0, 10);
                                ctx.textAlign = "right";
                                ctx.restore();

                                espacoinicial = espacoinicial + espaco;
                            }
                            @endforeach


                        </script>
                    </div>

                </div>

            </div>
        </div>
        {{-- !! Gráfico  --}}

        <br/>
        <br/>
        <h5>Regime por Unidade
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Unidade</th>
                <th scope="col">ABERTO</th>
                <th scope="col">SEMIABERTO</th>
                <th scope="col">FECHADO</th>
                <th scope="col">PROVISÓRIO</th>
                <th scope="col">SENTENCIADO</th>
                <th scope="col">MED. DE SEG.</th>
                <th scope="col">PROVI. EM MED. SEG.</th>
                <th scope="col">NÃO DETECTADO</th>
                <th scope="col">TOTAL</th>
                <tbody>
                @php
                    $totalaberto =
                    $totalsemiaberto =
                    $totalfechado =
                    $totalprovisorio =
                    $totalsentenciado =
                    $totalmedida_seg =
                    $totalmedida_seg_provisorio =
                    $totalinconsistente =
                    $totalgeral = 0;
                @endphp
                @foreach($detalhesUnidades as $unidade)
                    <tr class="">
                        <td>{{$unidade->sigla}}</td>
                        <td>
                            {{$unidade->aberto}}
                            @php
                                $totalaberto = $totalaberto + $unidade->aberto;
                            @endphp
                        </td>
                        <td>
                            {{$unidade->semiaberto}}
                            @php
                                $totalsemiaberto = $totalsemiaberto + $unidade->semiaberto;
                            @endphp
                        </td>
                        <td>
                            {{$unidade->fechado}}
                            @php
                                $totalfechado = $totalfechado + $unidade->fechado;
                            @endphp
                        </td>
                        <td>
                            {{$unidade->provisorio}}
                            @php
                                $totalprovisorio = $totalprovisorio + $unidade->provisorio;
                            @endphp
                        </td>
                        <td>
                            {{$unidade->sentenciado}}
                            @php
                                $totalsentenciado = $totalsentenciado + $unidade->sentenciado;
                            @endphp
                        </td>
                        <td>
                            {{$unidade->medida_seg}}
                            @php
                                $totalmedida_seg = $totalmedida_seg + $unidade->medida_seg;
                            @endphp
                        </td>
                        <td>
                            {{$unidade->medida_seg_provisorio}}
                            @php
                                $totalmedida_seg_provisorio = $totalmedida_seg_provisorio + $unidade->medida_seg_provisorio;
                            @endphp
                        </td>
                        <td>
                            {{$unidade->inconsistente}}
                            @php
                                $totalinconsistente = $totalinconsistente + $unidade->inconsistente;
                            @endphp
                        </td>
                        <td>
                            {{$unidade->total}}
                            @php
                                $totalgeral = $totalgeral + $unidade->total;
                            @endphp
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td>TOTOAL GERAL</td>
                    <td>{{$totalaberto}}</td>
                    <td>{{$totalsemiaberto}}</td>
                    <td>{{$totalfechado}}</td>
                    <td>{{$totalprovisorio}}</td>
                    <td>{{$totalsentenciado}}</td>
                    <td>{{$totalmedida_seg}}</td>
                    <td>{{$totalmedida_seg_provisorio}}</td>
                    <td>{{$totalinconsistente}}</td>
                    <td>{{$totalgeral}}</td>
                </tr>
            </table>
        </div>

        <br/>
        <h5>Sexo
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Quantidade</th>
                <tbody>
                <tr class="">
                    <td>Homens</td>
                    <td>{{ $qtdHA }}</td>
                </tr>
                <tr class="">
                    <td>Mulheres</td>
                    <td>{{ $qtdMA }}</td>
                </tr>
                <tr class="">
                    <td>Total</td>
                    <td>{{ $qtdHA + $qtdMA }}</td>
                </tr>
            </table>
        </div>

        <br/>
        <h5>Regime prisional
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>

        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">Classificação</th>
                    <th scope="col">Homens</th>
                    <th scope="col">Mulheres</th>
                    <th scope="col">Total</th>
                </tr>
                <tbody>
                <tr class="">
                    <td>Regime Fechado</td>
                    <td>{{ $qtdRegimeFechadoH }}</td>
                    <td>{{ $qtdRegimeFechadoM }}</td>
                    <td>{{ $qtdRegimeFechadoH + $qtdRegimeFechadoM }}</td>
                </tr>
                <tr class="">
                    <td>Regime Semiaberto</td>
                    <td>{{ $qtdRegimeSemiabertoH }}</td>
                    <td>{{ $qtdRegimeSemiabertoM }}</td>
                    <td>{{ $qtdRegimeSemiabertoH + $qtdRegimeSemiabertoM }}</td>
                </tr>
                <tr class="">
                    <td>Regime Aberto</td>
                    <td>{{ $qtdRegimeAbertoH }}</td>
                    <td>{{ $qtdRegimeAbertoM }}</td>
                    <td>{{ $qtdRegimeAbertoH + $qtdRegimeAbertoM }}</td>
                </tr>
                @if($qtdRegimeNIH + $qtdRegimeNIM != 0)
                <tr class="">
                    <td>Regime Não Informado no {{ config('app.name') }}</td>
                    <td>{{ $qtdRegimeNIH }}</td>
                    <td>{{ $qtdRegimeNIM }}</td>
                    <td>{{ $qtdRegimeNIH + $qtdRegimeNIM }}</td>
                </tr>
                @endif
            </table>
        </div>

        <br/>
        <h5>Sentenciados e provisórios (exceto medida de segurança)
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                <tr class="">
                    <td>Sentenciados</td>
                    <td>{{ $qtdSentenciadosH }}</td>
                    <td>{{ $qtdSentenciadosM }}</td>
                    <td>{{ $qtdSentenciadosH + $qtdSentenciadosM }}</td>
                </tr>
                <tr class="">
                    <td>Provisórios</td>
                    <td>{{ $qtdProvisoriosH }}</td>
                    <td>{{ $qtdProvisoriosM }}</td>
                    <td>{{ $qtdProvisoriosH + $qtdProvisoriosM }}</td>
                </tr>
                @if($qtdSentenciadosNIH + $qtdSentenciadosNIM != 0)
                <tr class="">
                    <td>Não Informado no {{ config('app.name') }}</td>
                    <td>{{ $qtdSentenciadosNIH }}</td>
                    <td>{{ $qtdSentenciadosNIM }}</td>
                    <td>{{ $qtdSentenciadosNIH + $qtdSentenciadosNIM }}</td>
                </tr>
                @endif
            </table>
        </div>

        <br/>
        <h5>Sentenciados e provisórios (em medida de segurança)
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                <tr class="">
                    <td>Sentenciados</td>
                    <td>{{ $qtdSentenciadosMSH }}</td>
                    <td>{{ $qtdSentenciadosMSM }}</td>
                    <td>{{ $qtdSentenciadosMSH + $qtdSentenciadosMSM }}</td>
                </tr>
                <tr class="">
                    <td>Provisórios</td>
                    <td>{{ $qtdProvisoriosMSH }}</td>
                    <td>{{ $qtdProvisoriosMSM }}</td>
                    <td>{{ $qtdProvisoriosMSH + $qtdProvisoriosMSM }}</td>
                </tr>
                @if($qtdSentenciadosMSNIH + $qtdSentenciadosMSNIM != 0)
                <tr class="">
                    <td>Não Informado no {{ config('app.name') }}</td>
                    <td>{{ $qtdSentenciadosMSNIH }}</td>
                    <td>{{ $qtdSentenciadosMSNIM }}</td>
                    <td>{{ $qtdSentenciadosMSNIH + $qtdSentenciadosMSNIM }}</td>
                </tr>
                @endif
            </table>
        </div>

        <br/>
        <h5>Sentenciados e provisórios (inclusive medida de segurança)
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                <tr class="">
                    <td>Sentenciados</td>
                    <td>{{ $qtdSentenciadosH + $qtdSentenciadosMSH }}</td>
                    <td>{{ $qtdSentenciadosM + $qtdSentenciadosMSM }}</td>
                    <td>{{ $qtdSentenciadosH + $qtdSentenciadosM + $qtdSentenciadosMSH + $qtdSentenciadosMSM }}</td>
                </tr>
                <tr class="">
                    <td>Provisórios</td>
                    <td>{{ $qtdProvisoriosH + $qtdProvisoriosMSH }}</td>
                    <td>{{ $qtdProvisoriosM + $qtdProvisoriosMSM }}</td>
                    <td>{{ $qtdProvisoriosH + $qtdProvisoriosM + $qtdProvisoriosMSH + $qtdProvisoriosMSM }}</td>
                </tr>
                @if($qtdSentenciadosNIH + $qtdSentenciadosNIM + $qtdSentenciadosMSNIH + $qtdSentenciadosMSNIM != 0)
                <tr class="">
                    <td>Não Informado no {{ config('app.name') }}</td>
                    <td>{{ $qtdSentenciadosNIH + $qtdSentenciadosMSNIH }}</td>
                    <td>{{ $qtdSentenciadosNIM + $qtdSentenciadosMSNIM }}</td>
                    <td>{{ $qtdSentenciadosNIH + $qtdSentenciadosNIM + $qtdSentenciadosMSNIH + $qtdSentenciadosMSNIM }}</td>
                </tr>
                @endif
            </table>
        </div>

        <br/>
        <h5>Medida de segurança
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Quantidade</th>
                <tbody>
                <tr class="">
                    <td>Homens</td>
                    <td>{{ $qtdMedidaDeSegurancaH }}</td>
                </tr>
                <tr class="">
                    <td>Mulheres</td>
                    <td>{{ $qtdMedidaDeSegurancaM }}</td>
                </tr>
                <tr class="">
                    <td>Total</td>
                    <td>{{ $qtdMedidaDeSegurancaH + $qtdMedidaDeSegurancaM }}</td>
                </tr>
            </table>
        </div>

        <br/>
        <h5>Faixa etária
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                <tr class="">
                    <td>0 a 17</td>
                    <td>{{ $faixaEtaria_zero_a_dezesseteH }}</td>
                    <td>{{ $faixaEtaria_zero_a_dezesseteM }}</td>
                    <td>{{ $faixaEtaria_zero_a_dezesseteH + $faixaEtaria_zero_a_dezesseteM }}</td>
                </tr>
                <tr class="">
                    <td>18 a 24</td>
                    <td>{{ $faixaEtaria_dezoito_a_vinteequatroH }}</td>
                    <td>{{ $faixaEtaria_dezoito_a_vinteequatroM }}</td>
                    <td>{{ $faixaEtaria_dezoito_a_vinteequatroH + $faixaEtaria_dezoito_a_vinteequatroM }}</td>
                </tr>
                <tr class="">
                    <td>25 a 29</td>
                    <td>{{ $faixaEtaria_vinteecinco_a_vinteenoveH }}</td>
                    <td>{{ $faixaEtaria_vinteecinco_a_vinteenoveM }}</td>
                    <td>{{ $faixaEtaria_vinteecinco_a_vinteenoveH + $faixaEtaria_vinteecinco_a_vinteenoveM }}</td>
                </tr>
                <tr class="">
                    <td>30 a 34</td>
                    <td>{{ $faixaEtaria_trinta_a_trintaequatroH }}</td>
                    <td>{{ $faixaEtaria_trinta_a_trintaequatroM }}</td>
                    <td>{{ $faixaEtaria_trinta_a_trintaequatroH + $faixaEtaria_trinta_a_trintaequatroM }}</td>
                </tr>
                <tr class="">
                    <td>35 a 45</td>
                    <td>{{ $faixaEtaria_trintaecinco_a_quarentaecincoH }}</td>
                    <td>{{ $faixaEtaria_trintaecinco_a_quarentaecincoM }}</td>
                    <td>{{ $faixaEtaria_trintaecinco_a_quarentaecincoH + $faixaEtaria_trintaecinco_a_quarentaecincoM }}</td>
                </tr>
                <tr class="">
                    <td>46 a 60</td>
                    <td>{{ $faixaEtaria_quarentaeseis_a_sessentaH }}</td>
                    <td>{{ $faixaEtaria_quarentaeseis_a_sessentaM }}</td>
                    <td>{{ $faixaEtaria_quarentaeseis_a_sessentaH + $faixaEtaria_quarentaeseis_a_sessentaM }}</td>
                </tr>
                <tr class="">
                    <td>61 a 70</td>
                    <td>{{ $faixaEtaria_sessentaeum_a_setentaH }}</td>
                    <td>{{ $faixaEtaria_sessentaeum_a_setentaM }}</td>
                    <td>{{ $faixaEtaria_sessentaeum_a_setentaH + $faixaEtaria_sessentaeum_a_setentaM }}</td>
                </tr>            <tr class="">
                    <td>Mais de 70</td>
                    <td>{{ $faixaEtaria_maisDeSetentaH }}</td>
                    <td>{{ $faixaEtaria_maisDeSetentaM }}</td>
                    <td>{{ $faixaEtaria_maisDeSetentaH + $faixaEtaria_maisDeSetentaM }}</td>
                </tr>
                @if($faixaEtaria_naoInformadaH + $faixaEtaria_naoInformadaM != 0)
                <tr class="">
                    <td>Não Informado no {{ config('app.name') }}</td>
                    <td>{{ $faixaEtaria_naoInformadaH }}</td>
                    <td>{{ $faixaEtaria_naoInformadaM }}</td>
                    <td>{{ $faixaEtaria_naoInformadaH + $faixaEtaria_naoInformadaM }}</td>
                </tr>
                @endif
            </table>
        </div>

        <br/>
        <h5>Nacionalidade
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                @foreach($nacionalidades as $nacionalidade)
                    <tr class="">
                        <td>{{ $nacionalidade->naturalidade_pais }}</td>
                        <td>{{ $nacionalidade->qtdH }}</td>
                        <td>{{ $nacionalidade->qtdM }}</td>
                        <td>{{ $nacionalidade->qtdH + $nacionalidade->qtdM }}</td>
                    </tr>
                @endforeach
                @if($qtdNacionalidadeNIH + $qtdNacionalidadeNIM != 0)
                <tr class="">
                    <td>Não informado no {{ config('app.name') }}</td>
                    <td>{{ $qtdNacionalidadeNIH }}</td>
                    <td>{{ $qtdNacionalidadeNIM  }}</td>
                    <td>{{ $qtdNacionalidadeNIH + $qtdNacionalidadeNIM }}</td>
                </tr>
                @endif
            </table>
        </div>

        <br/>
        <h5>Estado civil
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                <tr class="">
                    <td>Solteiros</td>
                    <td>{{ $qtdSolteirosH }}</td>
                    <td>{{ $qtdSolteirosM  }}</td>
                    <td>{{ $qtdSolteirosH + $qtdSolteirosM }}</td>
                </tr>
                <tr class="">
                    <td>Casados</td>
                    <td>{{ $qtdCasadosH }}</td>
                    <td>{{ $qtdCasadosM  }}</td>
                    <td>{{ $qtdCasadosH + $qtdCasadosM }}</td>
                </tr>
                <tr class="">
                    <td>União Estável</td>
                    <td>{{ $qtdUniaoEstavelH }}</td>
                    <td>{{ $qtdUniaoEstavelM  }}</td>
                    <td>{{ $qtdUniaoEstavelH + $qtdUniaoEstavelM }}</td>
                </tr>
                <tr class="">
                    <td>Separados</td>
                    <td>{{ $qtdSeparadosH }}</td>
                    <td>{{ $qtdSeparadosM  }}</td>
                    <td>{{ $qtdSeparadosH + $qtdSeparadosM }}</td>
                </tr>
                <tr class="">
                    <td>Divorciados</td>
                    <td>{{ $qtdDivorciadosH }}</td>
                    <td>{{ $qtdDivorciadosM  }}</td>
                    <td>{{ $qtdDivorciadosH + $qtdDivorciadosM }}</td>
                </tr>
                <tr class="">
                    <td>Viúvos</td>
                    <td>{{ $qtdViuvosH }}</td>
                    <td>{{ $qtdViuvosM  }}</td>
                    <td>{{ $qtdViuvosH + $qtdViuvosM }}</td>
                </tr>
                @if($qtdEstadoCivilNIH + $qtdEstadoCivilNIM != 0)
                <tr class="">
                    <td>Não informado no {{ config('app.name') }}</td>
                    <td>{{ $qtdEstadoCivilNIH }}</td>
                    <td>{{ $qtdEstadoCivilNIM  }}</td>
                    <td>{{ $qtdEstadoCivilNIH + $qtdEstadoCivilNIM }}</td>
                </tr>
                @endif
            </table>
        </div>

        <br/>
        <h5>Escolaridade
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                <tr class="">
                    <td>Analfabetos</td>
                    <td>{{ $qtdAnalfabetoH }}</td>
                    <td>{{ $qtdAnalfabetoM  }}</td>
                    <td>{{ $qtdAnalfabetoH + $qtdAnalfabetoM }}</td>
                </tr>
                <tr class="">
                    <td>Alfabetizados</td>
                    <td>{{ $qtdAlfabetizadoH }}</td>
                    <td>{{ $qtdAlfabetizadoM  }}</td>
                    <td>{{ $qtdAlfabetizadoH + $qtdAlfabetizadoM }}</td>
                </tr>
                <tr class="">
                    <td>Ensino Fundamental Incompleto</td>
                    <td>{{ $qtdFundIncH }}</td>
                    <td>{{ $qtdFundIncM  }}</td>
                    <td>{{ $qtdFundIncH + $qtdFundIncM }}</td>
                </tr>
                <tr class="">
                    <td>Ensino Fundamental Completo</td>
                    <td>{{ $qtdFundCompH }}</td>
                    <td>{{ $qtdFundCompM  }}</td>
                    <td>{{ $qtdFundCompH + $qtdFundCompM }}</td>
                </tr>
                <tr class="">
                    <td>Ensino Médio Incompleto</td>
                    <td>{{ $qtdMedioIncompH }}</td>
                    <td>{{ $qtdMedioIncompM  }}</td>
                    <td>{{ $qtdMedioIncompH + $qtdMedioIncompM }}</td>
                </tr>
                <tr class="">
                    <td>Ensino Médio Completo</td>
                    <td>{{ $qtdMedioCompH }}</td>
                    <td>{{ $qtdMedioCompM  }}</td>
                    <td>{{ $qtdMedioCompH + $qtdMedioCompM }}</td>
                </tr>
                <tr class="">
                    <td>Ensino Superior Incompleto</td>
                    <td>{{ $qtdSuperiorIncompH }}</td>
                    <td>{{ $qtdSuperiorIncompM  }}</td>
                    <td>{{ $qtdSuperiorIncompH + $qtdSuperiorIncompM }}</td>
                </tr>
                <tr class="">
                    <td>Ensino Superior Completo</td>
                    <td>{{ $qtdSuperiorCompH }}</td>
                    <td>{{ $qtdSuperiorCompM  }}</td>
                    <td>{{ $qtdSuperiorCompH + $qtdSuperiorCompM }}</td>
                </tr>
                <tr class="">
                    <td>Pós-graduação lato sensu</td>
                    <td>{{ $qtdPosLatoSensuH }}</td>
                    <td>{{ $qtdPosLatoSensuM  }}</td>
                    <td>{{ $qtdPosLatoSensuH + $qtdPosLatoSensuM }}</td>
                </tr>
                <tr class="">
                    <td>Mestrado</td>
                    <td>{{ $qtdMestradoH }}</td>
                    <td>{{ $qtdMestradoM  }}</td>
                    <td>{{ $qtdMestradoH + $qtdMestradoM }}</td>
                </tr>
                <tr class="">
                    <td>Doutorado</td>
                    <td>{{ $qtdDoutoradoH }}</td>
                    <td>{{ $qtdDoutoradoM  }}</td>
                    <td>{{ $qtdDoutoradoH + $qtdDoutoradoM }}</td>
                </tr>
                <tr class="">
                    <td>Pós-Doutorado</td>
                    <td>{{ $qtdPosDoutoradoH }}</td>
                    <td>{{ $qtdPosDoutoradoM  }}</td>
                    <td>{{ $qtdPosDoutoradoH + $qtdPosDoutoradoM }}</td>
                </tr>
                @if($qtdEscolaridadeNIH + $qtdEscolaridadeNIM != 0)
                <tr class="">
                    <td>Não informado no {{ config('app.name') }}</td>
                    <td>{{ $qtdEscolaridadeNIH }}</td>
                    <td>{{ $qtdEscolaridadeNIM  }}</td>
                    <td>{{ $qtdEscolaridadeNIH + $qtdEscolaridadeNIM }}</td>
                </tr>
                @endif
            </table>
        </div>

        <br/>
        <h5>Cor da pele
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                <tr class="">
                    <td>Amarela</td>
                    <td>{{ $qtdEtniaAmarelaH }}</td>
                    <td>{{ $qtdEtniaAmarelaM  }}</td>
                    <td>{{ $qtdEtniaAmarelaH + $qtdEtniaAmarelaM }}</td>
                </tr>
                <tr class="">
                    <td>Branca</td>
                    <td>{{ $qtdEtniaBrancaH }}</td>
                    <td>{{ $qtdEtniaBrancaM  }}</td>
                    <td>{{ $qtdEtniaBrancaH + $qtdEtniaBrancaM }}</td>
                </tr>
                <tr class="">
                    <td>Indígena</td>
                    <td>{{ $qtdEtniaIndígenaH }}</td>
                    <td>{{ $qtdEtniaIndígenaM  }}</td>
                    <td>{{ $qtdEtniaIndígenaH + $qtdEtniaIndígenaM }}</td>
                </tr>
                <tr class="">
                    <td>Parda</td>
                    <td>{{ $qtdEtniaPardaH }}</td>
                    <td>{{ $qtdEtniaPardaM  }}</td>
                    <td>{{ $qtdEtniaPardaH + $qtdEtniaPardaM }}</td>
                </tr>
                <tr class="">
                    <td>Preta</td>
                    <td>{{ $qtdEtniaPretaH }}</td>
                    <td>{{ $qtdEtniaPretaM  }}</td>
                    <td>{{ $qtdEtniaPretaH + $qtdEtniaPretaM }}</td>
                </tr>
                @if($qtdEtniaNIH + $qtdEtniaNIM != 0)
                <tr class="">
                    <td>Não informado no {{ config('app.name') }}</td>
                    <td>{{ $qtdEtniaNIH }}</td>
                    <td>{{ $qtdEtniaNIM  }}</td>
                    <td>{{ $qtdEtniaNIH + $qtdEtniaNIM }}</td>
                </tr>
                @endif
            </table>
        </div>

        <br/>
        <h5>Orientação Sexual
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                <tr class="">
                    <td>Heterossexual</td>
                    <td>{{ $qtdHeteroH }}</td>
                    <td>{{ $qtdHeteroM  }}</td>
                    <td>{{ $qtdHeteroH + $qtdHeteroM }}</td>
                </tr>
                <tr class="">
                    <td>Homossexual</td>
                    <td>{{ $qtdHomoH }}</td>
                    <td>{{ $qtdHomoM  }}</td>
                    <td>{{ $qtdHomoH + $qtdHomoM }}</td>
                </tr>
                <tr class="">
                    <td>Bissexual</td>
                    <td>{{ $qtdBiH }}</td>
                    <td>{{ $qtdBiM  }}</td>
                    <td>{{ $qtdBiH + $qtdBiM }}</td>
                </tr>
                <tr class="">
                    <td>Não declarou</td>
                    <td>{{ $qtdSexualidadeNaoDeclH }}</td>
                    <td>{{ $qtdSexualidadeNaoDeclM  }}</td>
                    <td>{{ $qtdSexualidadeNaoDeclH + $qtdSexualidadeNaoDeclM }}</td>
                </tr>
                @if($qtdSexualidadeNIH + $qtdSexualidadeNIM != 0)
                <tr class="">
                    <td>Não informado no {{ config('app.name') }}</td>
                    <td>{{ $qtdSexualidadeNIH }}</td>
                    <td>{{ $qtdSexualidadeNIM  }}</td>
                    <td>{{ $qtdSexualidadeNIH + $qtdSexualidadeNIM }}</td>
                </tr>
                @endif
            </table>
        </div>

        <br/>
        <h5>Saída da Unidade Prisional
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Motivo</th>
                <th scope="col">Quantidade</th>
                <tbody>
                <tr class="">
                    <td>Alvará</td>
                    <td>{{ $qtdAlvara }}</td>
                </tr>
                <tr class="">
                    <td>Fuga</td>
                    <td>{{ $qtdFuga }}</td>
                </tr>
                <tr class="">
                    <td>Recambiamento</td>
                    <td>{{ $qtdRecambiamento }}</td>
                </tr>
                <tr class="">
                    <td>Saída Judicial</td>
                    <td>{{ $qtdSaidaJudicial }}</td>
                </tr>
                <tr class="">
                    <td>Extradição</td>
                    <td>{{ $qtdExtradicao }}</td>
                </tr>
                <tr class="">
                    <td>Livramento Condicional</td>
                    <td>{{ $qtdLivramento }}</td>
                </tr>
                <tr class="">
                    <td>Óbito</td>
                    <td>{{ $qtdObito }}</td>
                </tr>
                <tr class="">
                    <td>Extinção da Punibilidade</td>
                    <td>{{ $qtdExtincaopunibilidade }}</td>
                </tr>
                <tr class="">
                    <td>Total</td>
                    <td>{{ $qtdAlvara + $qtdFuga + $qtdRecambiamento + $qtdSaidaJudicial + $qtdExtradicao + $qtdLivramento + $qtdObito + $qtdExtincaopunibilidade }}</td>
                </tr>
            </table>
        </div>

        <br/>
        <h5>Reingresso ao Sistema Prisional
            <i class="mdi mdi-arrow-bottom-right"></i>
            <label for="data_atendimento">A partir de:</label>
            <div style="display: inline-block;">
                <input type="text" class="form-control " name="data" id="data" placeholder="00/00/0000" style="width:200px">
            </div>
            <button type="submit" id="btnSubmitForm2" class="btn btn-primary"><i class="fas fa-redo"></i> Recalcular Estatísticas</button>

        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Saída por</th>
                <th scope="col">Quantidade</th>
                <tbody>
                <tr class="">
                    <td>Alvará</td>
                    <td>{{ $qntReingresso }}</td>
                </tr>
            </table>
        </div>

        <br/>
        <h5>Religião
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                <tr class="">
                    <td>Adventistas</td>
                    <td>{{ $qtdAdventistaH }}</td>
                    <td>{{ $qtdAdventistaM  }}</td>
                    <td>{{ $qtdAdventistaH + $qtdAdventistaM }}</td>
                </tr>
                <tr class="">
                    <td>Budistas</td>
                    <td>{{ $qtdBudistaH }}</td>
                    <td>{{ $qtdBudistaM  }}</td>
                    <td>{{ $qtdBudistaH + $qtdBudistaM }}</td>
                </tr>
                <tr class="">
                    <td>Católicos</td>
                    <td>{{ $qtdCatolicaH }}</td>
                    <td>{{ $qtdCatolicaM  }}</td>
                    <td>{{ $qtdCatolicaH + $qtdCatolicaM }}</td>
                </tr>
                <tr class="">
                    <td>Espíritas</td>
                    <td>{{ $qtdEspiritaH }}</td>
                    <td>{{ $qtdEspiritaM  }}</td>
                    <td>{{ $qtdEspiritaH + $qtdEspiritaM }}</td>
                </tr>
                <tr class="">
                    <td>Evangélicos</td>
                    <td>{{ $qtdEvangelicaH }}</td>
                    <td>{{ $qtdEvangelicaM  }}</td>
                    <td>{{ $qtdEvangelicaH + $qtdEvangelicaM }}</td>
                </tr>
                <tr class="">
                    <td>Judeus</td>
                    <td>{{ $qtdJudeuH }}</td>
                    <td>{{ $qtdJudeuM  }}</td>
                    <td>{{ $qtdJudeuH + $qtdJudeuM }}</td>
                </tr>
                <tr class="">
                    <td>Mórmons</td>
                    <td>{{ $qtdMormonH }}</td>
                    <td>{{ $qtdMormonM  }}</td>
                    <td>{{ $qtdMormonH + $qtdMormonM }}</td>
                </tr>
                <tr class="">
                    <td>Muçulmanos</td>
                    <td>{{ $qtdMulcumanaH }}</td>
                    <td>{{ $qtdMulcumanaM  }}</td>
                    <td>{{ $qtdMulcumanaH + $qtdMulcumanaM }}</td>
                </tr>
                <tr class="">
                    <td>Protestantes</td>
                    <td>{{ $qtdProtestanteH }}</td>
                    <td>{{ $qtdProtestanteM  }}</td>
                    <td>{{ $qtdProtestanteH + $qtdProtestanteM }}</td>
                </tr>
                <tr class="">
                    <td>Agnósticos</td>
                    <td>{{ $qtdAgnosticoH }}</td>
                    <td>{{ $qtdAgnosticoM  }}</td>
                    <td>{{ $qtdAgnosticoH + $qtdAgnosticoM }}</td>
                </tr>
                <tr class="">
                    <td>Ateus</td>
                    <td>{{ $qtdAteuH }}</td>
                    <td>{{ $qtdAteuM  }}</td>
                    <td>{{ $qtdAteuH + $qtdAteuM }}</td>
                </tr>
                <tr class="">
                    <td>Sem Religião</td>
                    <td>{{ $qtdSemReligiaoH }}</td>
                    <td>{{ $qtdSemReligiaoM  }}</td>
                    <td>{{ $qtdSemReligiaoH + $qtdSemReligiaoM }}</td>
                </tr>
                <tr class="">
                    <td>Outras</td>
                    <td>{{ $qtdOutrasH }}</td>
                    <td>{{ $qtdOutrasM  }}</td>
                    <td>{{ $qtdOutrasH + $qtdOutrasM }}</td>
                </tr>
                @if($qtdReligiaoNIH + $qtdReligiaoNIM != 0)
                <tr class="">
                    <td>Não informado no {{ config('app.name') }}</td>
                    <td>{{ $qtdReligiaoNIH }}</td>
                    <td>{{ $qtdReligiaoNIM  }}</td>
                    <td>{{ $qtdReligiaoNIH + $qtdReligiaoNIM }}</td>
                </tr>
                @endif
            </table>
        </div>

        @if(removeAccents($siglaUpLogado) == config('app.ti') || removeAccents($siglaUpLogado) == removeAccents("GISOP-OPERAÇÕES") || removeAccents($siglaUpLogado) == removeAccents("GISOP-CONTRAINTELIGÊNCIA") || removeAccents($siglaUpLogado) == removeAccents("GISOP-ANÁLISE") || $secretario || $adjunto)
        <br/>
        <h5>Faccionados e não-faccionados
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                    <tr class="">
                        <td>Faccionados</td>
                        <td>{{ $qtdHA - $qtdFaccaoNIH }}</td>
                        <td>{{ $qtdMA - $qtdFaccaoNIM }}</td>
                        <td>{{ ($qtdHA+$qtdMA) - ($qtdFaccaoNIH+$qtdFaccaoNIM) }}</td>
                    </tr>
                    @if($qtdFaccaoNIH+$qtdFaccaoNIM != 0)
                    <tr class="">
                        <td>Não-Faccionados ou Não Informado no {{ config('app.name') }}</td>
                        <td>{{ $qtdFaccaoNIH }}</td>
                        <td>{{ $qtdFaccaoNIM }}</td>
                        <td>{{ ($qtdFaccaoNIH+$qtdFaccaoNIM) }}</td>
                    </tr>
                    @endif
            </table>
        </div>

        <br/>
        <h5>Facção criminosa
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                @foreach($faccoes as $faccao)
                    @if($faccao->qtd > 0)
                        <tr class="">
                            <td>{{ $faccao->nome }} ({{ $faccao->sigla }})</td>
                            <td>{{ $faccao->qtdH }}</td>
                            <td>{{ $faccao->qtdM }}</td>
                            <td>{{ $faccao->qtd }}</td>
                        </tr>
                    @endif
                @endforeach
            </table>
        </div>

        <br/>
        <h5>Periculosidade
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                    <tr class="">
                        <td>Periculosidade Alta</td>
                        <td>{{ $periculosidadeAltaH }}</td>
                        <td>{{ $periculosidadeAltaM }}</td>
                        <td>{{ $periculosidadeAltaH + $periculosidadeAltaM }}</td>
                    </tr>
                    <tr class="">
                        <td>Periculosidade Média</td>
                        <td>{{ $periculosidadeMediaH }}</td>
                        <td>{{ $periculosidadeMediaM }}</td>
                        <td>{{ $periculosidadeMediaH + $periculosidadeAltaM }}</td>
                    </tr>
                    <tr class="">
                        <td>Periculosidade Baixa</td>
                        <td>{{ $periculosidadeBaixaH }}</td>
                        <td>{{ $periculosidadeBaixaM }}</td>
                        <td>{{ $periculosidadeBaixaH + $periculosidadeBaixaM }}</td>
                    </tr>
                    @if($periculosidadeNIH + $periculosidadeNIM != 0)
                    <tr class="">
                        <td>Periculosidade Não Informada no {{ config('app.name') }}</td>
                        <td>{{ $periculosidadeNIH }}</td>
                        <td>{{ $periculosidadeNIM }}</td>
                        <td>{{ $periculosidadeNIH + $periculosidadeNIM }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <br/>
        <h5>Liderança
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                <tr class="">
                    <td>São Lideranças</td>
                    <td>{{ $qtdLiderancaSimH }}</td>
                    <td>{{ $qtdLiderancaSimM  }}</td>
                    <td>{{ $qtdLiderancaSimH + $qtdLiderancaSimM }}</td>
                </tr>
                <tr class="">
                    <td>Não São Lideranças</td>
                    <td>{{ $qtdLiderancaNaoH }}</td>
                    <td>{{ $qtdLiderancaNaoM  }}</td>
                    <td>{{ $qtdLiderancaNaoH + $qtdLiderancaNaoM }}</td>
                </tr>
                @if($qtdLiderancaNIH + $qtdLiderancaNIM != 0)
                <tr class="">
                    <td>Não informado no {{ config('app.name') }}</td>
                    <td>{{ $qtdLiderancaNIH }}</td>
                    <td>{{ $qtdLiderancaNIM  }}</td>
                    <td>{{ $qtdLiderancaNIH + $qtdLiderancaNIM }}</td>
                </tr>
                @endif
            </table>
        </div>
        @endif
    </div>


    <a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
@endsection
@push('css')
    <link href="{{ asset('css/unidades/show.css') }}" rel="stylesheet">
@endpush
@push('script')
    <script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ asset('js/estatisticas/buscar.js') }}"></script>
@endpush