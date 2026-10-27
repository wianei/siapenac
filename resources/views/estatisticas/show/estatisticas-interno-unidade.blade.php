@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-table"></i> Estatísticas do {{ config('app.sistema_estado') }} - Tabelas</h3>
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

    @endphp

    @include('estatisticas.show._menuEstatisticas')

<div style="display: inline;">
<form action="{{ route('estatisticas.internosUnidade') }}" method="post" enctype="multipart/form-data">
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
        @if(is_null($unidadeSigla)) do {{ config('app.sistema_estado') }} @else da unidade: {{$unidadeSigla}}@endif
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