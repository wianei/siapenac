@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-table"></i> Estatísticas do {{ config('app.sistema_estado') }} - Tabelas</h3>
        <hr>
        <p>
            Tabelas com dados estatísticos relacionados aos Servidores, atualizados em tempo real, relativos ao {{ config('app.sistema_estado') }}, a confiabilidade dos dados abaixo depende de inserção manual, portanto pode haver eventuais inconsistências.
        </p>
    </div>

    @php

    $secretario = \App\Http\Controllers\Facade\FacadeController::isLogadoSecretario();

    $adjunto = \App\Http\Controllers\Facade\FacadeController::isLogadoAdjunto();

    $siglaUpLogado = \App\Http\Controllers\Facade\FacadeController::getLotacaoServidor(\Illuminate\Support\Facades\Auth::user()->cpf, "LOTAÇÃO NO RH");

    $soma = 0;

    $somaH = 0;

    $somaM = 0;

    $maxValue = 0;

    @endphp

    <form action="{{ route('estatisticas.showServidor') }}" method="post" enctype="multipart/form-data">
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

        </div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary"><i class="fas fa-redo"></i> Recalcular Estatísticas</button>
    </form>

    <hr/>

    <h4>Informações estatísticas
        @if(is_null($unidadeSigla)) do {{ config('app.sistema_estado') }} @else da unidade: {{$unidadeSigla}}@endif
        coletadas em {{ $dataHora }}.</h4>

    <div id="resultados">

        <br/>
        <h5>Servidores
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Unidade</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total Unidade</th>
                <tbody>
                @foreach($servidores as $x => $servidor)
                <tr class="">
                    <td>{{$servidor[0]}}</td>
                    <td>{{$servidoresBySexo[$x][0]}}</td>
                    <td>{{$servidoresBySexo[$x][1]}}</td>
                    <td>{{$servidor[1]}}</td>
                    @php
                        $soma = $soma + $servidor[1];
                        $somaH = $somaH + $servidoresBySexo[$x][0];
                        $somaM = $somaM + $servidoresBySexo[$x][1];
                        if($servidor[1] > $maxValue){
                            $maxValue = $servidor[1];
                        }
                    @endphp
                </tr>
                @endforeach
                <tr class="">
                    <td>Total</td>
                    <td>{{$somaH}}</td>
                    <td>{{$somaM}}</td>
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
                            var tamanhominimo = 50 + (20 + 10) * parseInt('{{count($servidores)}}',10);
                            var espacoinicial = 35;
                            var espaco = 50;

                            if(tamanhotela < 1300 && tamanhotela > 1100){
                                larguraconluna = 25;
                                espaco = 35;
                            }

                            var tamanhomaximo = 50 + (larguraconluna + 10) * parseInt('{{count($servidores)}}',10);

                            c.setAttribute("width", tamanhomaximo);
                            var ctx = c.getContext("2d");
                            var maxvalue = '{{\App\Http\Controllers\estatisticas\EstatisticasController::arredondarEscala($maxValue,9)}}';
                            maxvalue = parseInt(maxvalue, 10);


                            ctx.font = "15px Arial";

                            var contador = maxvalue*9;
                            var intervalo = 40;

                            //DESENHA AS LINHAS E OS NÚMEROS
                            for(var i=0; i<400; i=i+intervalo){
                                ctx.moveTo(30,i+30);
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
                            @foreach($servidores as $servidor){
                                ctx.fillStyle = cores[proximacor];
                                resultado = '{{$servidor[1]}}';
                                var posicao = indicemax - parseInt(resultado, 10)*escala;
                                ctx.fillRect(espacoinicial, posicao, larguraconluna, 390-posicao);
                                proximacor = (proximacor+1)%resto;

                                ctx.save();
                                ctx.fillStyle = 'black';
                                ctx.translate(espacoinicial + larguraconluna/2, 400);
                                ctx.rotate(Math.PI/4);
                                ctx.fillText('{{$servidor[0]}}', 0, 10);
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
        <h5>Proporção Servidor x Interno
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Unidade</th>
                <th scope="col">Servidores</th>
                <th scope="col">Internos</th>
                <tbody>
                @foreach($servidores as $x => $servidor)
                    <tr class="">
                        <td>{{$servidor[0]}}</td>
                        <td>{{$servidor[1]}}</td>
                        <td>{{!is_null($internos[$x]) ? $internos[$x]->soma : -1}}</td>
                    </tr>
                @endforeach
            </table>
        </div>
        {{--<h5>Faixa etária
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
                    <td>{{ $idadesM[1] }}</td>
                    <td>{{ $idadesF[1] }}</td>
                    <td>{{ $idadesM[1] + $idadesF[1] }}</td>
                </tr>
                <tr class="">
                    <td>18 a 24</td>
                    <td>{{ $idadesM[2] }}</td>
                    <td>{{ $idadesF[2] }}</td>
                    <td>{{ $idadesM[2] + $idadesF[2] }}</td>
                </tr>
                <tr class="">
                    <td>25 a 29</td>
                    <td>{{ $idadesM[3] }}</td>
                    <td>{{ $idadesF[3] }}</td>
                    <td>{{ $idadesM[3] + $idadesF[3] }}</td>
                </tr>
                <tr class="">
                    <td>30 a 34</td>
                    <td>{{ $idadesM[4] }}</td>
                    <td>{{ $idadesF[4] }}</td>
                    <td>{{ $idadesM[4] + $idadesF[4] }}</td>
                </tr>
                <tr class="">
                    <td>35 a 45</td>
                    <td>{{ $idadesM[5] }}</td>
                    <td>{{ $idadesF[5] }}</td>
                    <td>{{ $idadesM[5] + $idadesF[5] }}</td>
                </tr>
                <tr class="">
                    <td>46 a 60</td>
                    <td>{{ $idadesM[6] }}</td>
                    <td>{{ $idadesF[6] }}</td>
                    <td>{{ $idadesM[6] + $idadesF[6] }}</td>
                </tr>
                <tr class="">
                    <td>61 a 70</td>
                    <td>{{ $idadesM[7] }}</td>
                    <td>{{ $idadesF[7] }}</td>
                    <td>{{ $idadesM[7] + $idadesF[7] }}</td>
                </tr>            <tr class="">
                    <td>Mais de 70</td>
                    <td>{{ $idadesM[8] }}</td>
                    <td>{{ $idadesF[8] }}</td>
                    <td>{{ $idadesM[8] + $idadesF[8] }}</td>
                </tr>
                @if($idadesM[0] + $idadesF[0] != 0)
                <tr class="">
                    <td>Não Informado no SIAPEN</td>
                    <td>{{ $idadesM[0] }}</td>
                    <td>{{ $idadesF[0] }}</td>
                    <td>{{ $idadesM[0] + $idadesF[0] }}</td>
                </tr>
                @endif
            </table>
        </div>--}}




    </div>

    <a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
@endsection
@push('css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="{{ asset('css/unidades/show.css') }}" rel="stylesheet">
@endpush
@push('script')
    <script src="{{ asset('js/estatisticas/buscar.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endpush