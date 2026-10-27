@extends('template.tema_geral.theme')
@section('content')

    @php

    $siglaUpLogado = \App\Http\Controllers\Facade\FacadeController::getLotacaoServidor(\Illuminate\Support\Facades\Auth::user()->cpf);
    $idUpLogado = \App\Http\Controllers\Unidades\UnidadesController::getIdUpPorSigla($siglaUpLogado);

    @endphp

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    @if(PermissaoService::getTipoPermissao() == "I")
    <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics"><a href="{{ route('interno.buscar', ['unidade_prisional'=>$idUpLogado, 'status'=>'A']) }}">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-left">
                            <i class="mdi mdi-poll-box  text-danger icon-lg"></i>
                        </div>
                        <div class="float-right">
                            <p class="mb-0 text-right">Internos nesta unidade</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0">{{ $internosUnidade }}</h3>
                            </div>
                        </div>
                    </div>
                </div></a>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics"><a href="{{ route('interno.buscar', ['status'=>'A']) }}">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-left">
                            <i class="mdi mdi-poll-box  text-warning icon-lg"></i>
                        </div>
                        <div class="float-right">
                            <p class="mb-0 text-right">Total de internos no {{ config('app.uf') }}</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0">{{ $internosSispern }}</h3>
                            </div>
                        </div>
                    </div>
                </div></a>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics"><a href="{{ route('interno.separacao', 0) }}">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-left">
                            <i class="mdi mdi-grid-off text-success icon-lg"></i>
                        </div>
                        <div class="float-right">
                            <p class="mb-0 text-right">Separação de Internos</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0">{{ $internosSeparacao }} </h3>
                            </div>
                        </div>
                    </div>
                </div></a>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics"><a href="{{ route('servidor.listarServidores') }}">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-left">
                            <i class="mdi mdi-account-location text-info icon-lg"></i>
                        </div>
                        <div class="float-right">
                            <p class="mb-0 text-right">Servidores nesta unidade</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0">{{ $servidoresUnidade }}</h3>
                            </div>
                        </div>
                    </div>
                </div></a>
            </div>
        </div>
    </div>

    <hr>

    @if($qtdTransferenciasNaoAceitas > 0)
    <div class="alert alert-warning">
        <h6>Quantidade de internos com saídas do (a) {{ $siglaUpLogado }}, por transferência, não confirmadas no destino: {{ $qtdTransferenciasNaoAceitas }} <a style="text-align: right" href="{{ route('movimentacoes.transferenciaShow') }}" data-toggle="tooltip" data-placement="top" title="Abrir"><i class="mdi mdi-information-outline"></i></a></h6>
    </div>
    @endif

    @if($qtdTransferenciasRecebidasNaoAceitas > 0)
    <div class="alert alert-warning">
        <h6>Quantidade de internos com entradas no (a) {{ $siglaUpLogado }}, por transferência, não confirmadas: {{ $qtdTransferenciasRecebidasNaoAceitas }}<a style="text-align: right" href="{{ route('movimentacoes.transferenciaShow') }}" data-toggle="tooltip" data-placement="top" title="Abrir"><i class="mdi mdi-information-outline"></i></a>
    </h6>
    </div>
    @endif

    @if($qtdAlocacaoCelaPendente > 0)
    <div class="alert alert-warning">
        <h6>Quantidade de internos com alocação em cela pendente: {{ $qtdAlocacaoCelaPendente }}<a style="text-align: right" href="{{ route('movimentacoes.transferenciaShow') }}" data-toggle="tooltip" data-placement="top" title="Abrir"><i class="mdi mdi-information-outline"></i></a>
    </h6>
    </div>
    @endif

    <script>
        window.onload = function () {

            var options = {
                animationEnabled: true,
                title: {
                    fontColor: '#1C2331',
                    fontFamily: 'Poppins, sans-serif',
                    fontSize: 24,
                    text: "Total de Internos por Unidade Prisional no <?= config('app.uf') ?>"
                },
                animation: {
                    duration: 6000,
                    xAxis: true,
                    yAxis: true,
                },
                axisX:{
                    title: "Internos ativos nos três regimes",
                    labelAngle: 90,
                    labelAutoFit: true,
                    interval:1
                },
                data: [
                    {
                        // Change type to "doughnut", "line", "splineArea", etc.
                        type: "column", //change type to bar, line, area, pie, etc
                        dataPoints: <?php echo json_encode($qtdPresosUnidade, JSON_NUMERIC_CHECK); ?>
                        }
                ]
            };

            $("#chartContainer").CanvasJSChart(options);
        }
    </script>
    <div id="chartContainer" style="height: 340px; width: 100%;" class="mt-5 mb-5"></div>



    <div class="row w-100">

        <div id="recent-activities-wrapper" class="card updates activities col-lg-4 col-md-12">
            <div id="activitemdheader" class="card-header d-flex justify-content-between align-items-center  mb-3">
                <h2 class="h5 display"><a data-toggle="collapse" data-parent="#recent-activities-wrapper" href="#activities-box" aria-expanded="true" aria-controls="activities-box">ATIVIDADES RECENTES</a>
                </h2>
                <a data-toggle="collapse" data-parent="#recent-activities-wrapper" href="#activities-box" aria-expanded="true" aria-controls="activities-box"><i class="fas fa-external-link-alt"></i></a>
            </div>
            <div id="activities-box" role="tabpanel" class="collapse show">
                <ul class="activities list-unstyled">

                    @foreach($logUnidade as $log)
                        <li>
                            <div class="row">
                                <div class="col-4 date-holder text-right">
                                    <div class="icon fontSize-2em color_365f80"><i class="far fa-clock"></i></div>
                                    <div class="date color_365f80">
                                        <span>{{ $log['hora'] }}</span><br>
                                        <span>{{ $log['data'] }}</span>
                                    </div>
                                </div>
                                <div class="col-8 content"><strong>{!! $log['tabela'] !!} </strong>
                                    <p>
                                        {!! strip_tags($log['descricao']) !!}
                                        <span class="text-muted"><br>Realizado por {{ $log['realizadoPor'] }}</span>
                                    </p>
                                </div>
                            </div>
                        </li>
                        <!-- Item-->
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- mensagens          -->
        <div class="col-lg-4 col-md-12">
            <!-- Recent Updates Widget          -->
            <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 display"><a href="{{ route('mensagem.recebidas') }}">ÚLTIMAS MENSAGENS RECEBIDAS</a>
                    </h2>
                    <a href="{{ route('mensagem.recebidas') }}"><i class="fas fa-external-link-alt"></i></a>
                </div>
                <div id="updates-box" role="tabpanel" class="collapse show">
                    <ul class="news list-unstyled">
                        @foreach($mensagens as $mensagem)
                                <!-- Item-->
                        <li class="d-flex justify-content-between border1pxSolidRGBA0125 pt-2">
                            <div class="left-col d-flex">
                                <div class="icon alert-secondary fontSize-1_5em mr-4 text-primary">
                                    <i class="mdi mdi-arrow-bottom-left"></i>
                                </div>
                                <div class="title"><strong class="color_333">{{ $mensagem['assunto'] }}</strong>
                                    <p>{{ substr(strip_tags($mensagem['mensagem']),0,100) }}</p>
                                </div>
                            </div>
                            <div class="right-col text-right">
                                <div class="update-date text-center fontSize-1_5em line-heigt-1em mt-2 color_365f80">
                                    {{ substr($mensagem['data'], 0, 2) }}<br>
                                    <span class="month">{{ $mensagem['mesData'] }}<br></span>
                                    <span class="fontSize-13px">{{ substr($mensagem['data'], 6, 4) }}</span>
                                </div>
                            </div>
                        </li>
                        <!-- Item-->
                        @endforeach
                    </ul>
                </div>
            </div>
            <!-- Recent Updates Widget End-->
        </div>


        <!--    Notificacoes          -->
        <div class="col-lg-4 col-md-12">
            <!-- Recent Updates Widget          -->
            <div id="new-updates" class="card updates recent-updated">
                <div id="updates-header" class="card-header d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 display">
                        <a href="{{ route('interno.saidaSancaoTriagem') }}">SANÇÃO E TRIAGEM</a>
                    </h2>
                    <a href="{{ route('interno.saidaSancaoTriagem') }}"><i class="fas fa-external-link-alt"></i></a>
                </div>
                <div id="updates-box" role="tabpanel" class="collapse show">
                    <ul class="news list-unstyled">
                        @foreach($saidaSancao as $saida)
                            <!-- Item-->
                            <li class="d-flex justify-content-between">
                                <div class="left-col d-flex">
                                    <div class="icon alert-secondary fontSize-1_5em mr-4 color_365f80">
                                        <i class="mdi mdi-alert"></i>
                                    </div>
                                    <div class="title"><strong class="color_333">Saída
                                            @if($saida['tipoSt'] == "sancao")
                                                Sanção Disciplinar
                                            @elseif($saida['tipoSt'] == "triagem")
                                                Triagem
                                            @endif
                                            </strong>
                                        <p>{!! $saida['nome'] !!}</p>
                                    </div>
                                </div>
                                <div class="right-col text-right">
                                    <div class="update-date text-center fontSize-1_5em line-heigt-1em mt-2 color_365f80">
                                        {{ substr($saida['dataFim'],0,2) }}<br>
                                        <span class="month">{{ $saida['mesData'] }}<br></span>
                                        <span class="fontSize-13px">{{ substr($saida['dataFim'], 6, 4) }}</span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <!-- Recent Updates Widget End-->
        </div>

    </div>
        @else

        <script>
            window.onload = function () {

                var options = {
                    animationEnabled: true,
                    title: {
                        fontColor: '#1C2331',
                        fontFamily: 'Poppins, sans-serif',
                        fontSize: 24,
                        text: "Total de Internos por Unidade Prisional no <?= config('app.uf') ?>"
                    },
                    animation: {
                        duration: 6000,
                        xAxis: true,
                        yAxis: true,
                    },
                    axisX:{
                        title: "Internos ativos nos três regimes",
                        labelAngle: 90,
                        labelAutoFit: true,
                        interval:1
                    },
                    data: [
                        {
                            // Change type to "doughnut", "line", "splineArea", etc.
                            type: "column", //change type to bar, line, area, pie, etc
                            dataPoints: <?php echo json_encode($qtdPresosUnidade, JSON_NUMERIC_CHECK); ?>
                        }
                    ]
                };

                $("#chartContainer").CanvasJSChart(options);
            }
        </script>
        <div id="chartContainer" style="height: 340px; width: 100%;"></div>


    @endif
@endsection
@push('css')
<link rel="stylesheet" href="{{asset('css/home/home.css')}}">
@endpush
@push('script')
<script src="{{ asset('js/jquery.mask.js') }}"></script>
<script src="{{ asset('js/cadastro.js') }}"></script>
<script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
<script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>

@endpush