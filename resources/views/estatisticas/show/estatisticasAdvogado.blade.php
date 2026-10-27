@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-table"></i> Estatísticas do {{ config('app.sistema_estado') }} - Tabelas</h3>
        <hr>
        <p>
            Tabelas com dados estatísticos relacionados aos atendimentos de advogado, atualizados em tempo real, relativos ao {{ config('app.sistema_estado') }}, a confiabilidade dos dados abaixo depende da inserção manual deles nos perfis de cada interno, portanto pode haver eventuais inconsistências.
        </p>
    </div>

    @php

    $maxValue = 0;

    $secretario = \App\Http\Controllers\Facade\FacadeController::isLogadoSecretario();

    $adjunto = \App\Http\Controllers\Facade\FacadeController::isLogadoAdjunto();

    $siglaUpLogado = \App\Http\Controllers\Facade\FacadeController::getLotacaoServidor(\Illuminate\Support\Facades\Auth::user()->cpf, "LOTAÇÃO NO RH");

    @endphp

    <form action="{{ route('estatisticas.showAdvogado') }}" method="post" enctype="multipart/form-data">
        {!! csrf_field() !!}
          <div class="form-row">
              <div class="form-group col-sm-3">
                  <label for="data_atendimento">Atendimentos a partir:</label>
                  <input type="text" class="form-control" name="data_atendimento" id="data_atendimento" placeholder="00/00/0000">
              </div>
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
        <button type="submit" id="btnSubmitForm" class="btn btn-primary"><i class="fas fa-redo"></i> Calcular Estatísticas</button>


    <hr/>

    <h4>Informações estatísticas
        @if(is_null($unidadeSigla)) do {{ config('app.sistema_estado') }} @else da unidade: {{$unidadeSigla}}@endif
        @if(!is_null($pavilhaoNome)), pavilhão: {{$pavilhaoNome}}@endif
        @if(!is_null($alaNome)), ala: {{$alaNome}}@endif
        @if(!is_null($celaNome))cela: {{$celaNome}}@endif
        coletadas em {{ $dataHora }}.</h4>

    <div id="resultados">

        <br/>
        <h5>Atendimentos de Advogado (Atendimento desde @if (!is_null($data_atendimento)) {{$data_atendimento }} )  @else {{substr($dataHora,0,10)}} ) @endif
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">{{ $nome_campo }}</th>
                <th scope="col">Quantidade</th>
                <tbody>
                @foreach($atendimento_advogado as $atd)
                    <tr class="">
                        <td>{{$atd->sigla}}</td>
                        <td>{{$atd->qnt}}</td>
                        <div style="display: none">{{$soma = $soma + $atd->qnt}}</div>
                    </tr>
                    @php
                        if($maxValue < $atd->qnt){
                            $maxValue = $atd->qnt;
                        }
                    @endphp
                @endforeach
                <tr class="">
                    <td>Total</td>
                    <td>{{$soma}}</td>
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
                            var tamanhominimo = 50 + (20 + 10) * parseInt('{{count($atendimento_advogado)}}',10);
                            var espacoinicial = 45;
                            var espaco = 50;

                            if(tamanhotela < 1300 && tamanhotela > 1100){
                                larguraconluna = 25;
                                espaco = 35;
                            }

                            var tamanhomaximo = 50 + (larguraconluna + 10) * parseInt('{{count($atendimento_advogado)}}',10);

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
                                @foreach($atendimento_advogado as $atendimento){
                                ctx.fillStyle = cores[proximacor];
                                resultado = '{{$atendimento->qnt}}';
                                var posicao = indicemax - parseInt(resultado, 10)*escala;
                                ctx.fillRect(espacoinicial, posicao, larguraconluna, 390-posicao);
                                proximacor = (proximacor+1)%resto;

                                ctx.save();
                                ctx.fillStyle = 'black';
                                ctx.translate(espacoinicial + larguraconluna/2, 400);
                                ctx.rotate(Math.PI/4);
                                ctx.fillText('{{$atendimento->sigla}}', 0, 10);
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
    </form>

    <a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
@endsection
@push('css')
    <link href="{{ asset('css/unidades/show.css') }}" rel="stylesheet">
@endpush
@push('script')
    <script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ asset('js/estatisticas/buscar.js') }}"></script>
@endpush