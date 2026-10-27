@extends('template.tema_geral.theme')

@section('content')
    <style>
        tr:nth-child(even) {
            background-color: #ececec !important;
        }

        .dadoRetornado{
            font-weight: bold;
        }
    </style>

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-table"></i> Estatísticas do {{ config('app.sistema_estado') }} - Unidades</h3>
        <hr>
        <p>
            Tabelas com dados estatísticos relacionadas às unidades prisionais, atualizados em tempo real,a confiabilidade dos dados abaixo depende da inserção manual deles nos perfis de cada interno, portanto pode haver eventuais inconsistências.
        </p>
    </div>

    <hr/>

    <form action="" method="post" class="mb-3">
        <div class="form-row">
            {!! csrf_field() !!}

            <div class="form-group col-sm-3">
                <label for="data">Dia</label>
                <input type="text" name="data" class="form-control" id="data" autocomplete="off">
            </div>

            <div class="form-group col-sm-3">
                <label for="mes">Mês</label>
                <select id="mes" name="mes" class="form-control">
                    <option value="">Selecione</option>
                    @foreach($mesesEscala as $mes)
                        <option value="{{ sprintf("%02d", $mes['mesNum']) }}">{{ $mes['mesNome'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3">
                <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-4"><i class="fas fa-redo"></i> Recalcular Estatísticas</button>
            </div>
        </div>

    </form>

    <h5 class="@if($mesShow == 1) displayNone @endif">Teleatendimento de {{ $diaEstatistica }}
        <i class="mdi mdi-arrow-bottom-right"></i>
        <a href="{{ route('estatisticas.showUnidadeDiaPdf', $diaEstatisticaMysql) }}" class="btn btn-primary mb-2 float-right">Gerar Pdf</a>
    </h5>
    <div class="table-responsive-md @if($mesShow == 1) displayNone @endif">
        <table class="table">
            <thead class="background_30516f color_FFF">
            <th scope="col">Unidade</th>
            <th class="text-center" scope="col">Atendidos</th>
            <th class="text-center" scope="col">Agendados</th>
            <th class="text-center" scope="col">Faltas</th>
            <th class="text-center" scope="col">Cancelados</th>
            </thead>

            <tbody>
            @php
            $atendidosDia = 0;
            $agendadoDia = 0;
            $faltaDia = 0;
            $canceladaDia = 0;
            @endphp

            @foreach($unidades as $unidade)

                @if(array_key_exists($unidade, $agendamentoDia))
                    <tr>
                        <td>{{ $unidade }}</td>
                        @if(array_key_exists("Atendido", $agendamentoDia[$unidade]))
                            @php
                            $atendidosDia = $atendidosDia + count($agendamentoDia[$unidade]["Atendido"]);
                            @endphp

                            <td class="text-center dadoRetornado">{{ count($agendamentoDia[$unidade]["Atendido"]) }}</td>
                        @else
                            <td class="text-center">0</td>
                        @endif

                        @if(array_key_exists("Agendado", $agendamentoDia[$unidade]))
                            @php
                            $agendadoDia = $agendadoDia + count($agendamentoDia[$unidade]["Agendado"]);
                            @endphp

                            <td class="text-center dadoRetornado">{{ count($agendamentoDia[$unidade]["Agendado"]) }}</td>
                        @else
                            <td class="text-center">0</td>
                        @endif

                        @if(array_key_exists("Falta", $agendamentoDia[$unidade]))
                            @php
                            $faltaDia = $faltaDia + count($agendamentoDia[$unidade]["Falta"]);
                            @endphp

                            <td class="text-center dadoRetornado">{{ count($agendamentoDia[$unidade]["Falta"]) }}</td>
                        @else
                            <td class="text-center">0</td>
                        @endif

                        @if(array_key_exists("Cancelada", $agendamentoDia[$unidade]))
                            @php
                            $canceladaDia = $canceladaDia + count($agendamentoDia[$unidade]["Cancelada"]);
                            @endphp

                            <td class="text-center dadoRetornado">{{ count($agendamentoDia[$unidade]["Cancelada"]) }}</td>
                        @else
                            <td class="text-center">0</td>
                        @endif

                    </tr>

                @else
                    <tr>
                        <td>{{ $unidade }}</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                    </tr>

                @endif


            @endforeach
                <tr>
                    <td class="dadoRetornado">TOTAL</td>
                    <td class="text-center dadoRetornado">{{ $atendidosDia }}</td>
                    <td class="text-center dadoRetornado">{{ $agendadoDia }}</td>
                    <td class="text-center dadoRetornado">{{ $faltaDia }}</td>
                    <td class="text-center dadoRetornado">{{ $canceladaDia }}</td>
                </tr>
            </tbody>
        </table>

    </div>

    <h5 class="mt-5 @if($diaShow == 1) displayNone @endif">Teleatendimento de {{ $mesNome }}
        <i class="mdi mdi-arrow-bottom-right"></i>
        <a href="{{ route('estatisticas.showUnidadeMesPdf', $mesEstatistica) }}" class="btn btn-primary mb-2 float-right">Gerar Pdf</a>

    </h5>
    <div class="table-responsive-md  @if($diaShow == 1) displayNone @endif">
        <table class="table">
            <thead class="background_30516f color_FFF">
                <th scope="col">Unidade</th>
                <th class="text-center" scope="col">Atendidos</th>
                <th class="text-center" scope="col">Agendados</th>
                <th class="text-center" scope="col">Faltas</th>
                <th class="text-center" scope="col">Cancelados</th>
            </thead>

            <tbody>
            @php
            $atendidos = 0;
            $agendado = 0;
            $falta = 0;
            $cancelada = 0;
            @endphp
                @foreach($unidades as $unidade)

                    @if(array_key_exists($unidade, $agendamentoMes))
                        <tr>
                            <td>{{ $unidade }}</td>
                            @if(array_key_exists("Atendido", $agendamentoMes[$unidade]))
                                @php
                                    $atendidos = $atendidos + count($agendamentoMes[$unidade]["Atendido"]);
                                @endphp

                                <td class="text-center dadoRetornado">{{ count($agendamentoMes[$unidade]["Atendido"]) }}</td>
                                @else
                                <td class="text-center">0</td>
                            @endif

                            @if(array_key_exists("Agendado", $agendamentoMes[$unidade]))
                                @php
                                $agendado = $agendado + count($agendamentoMes[$unidade]["Agendado"]);
                                @endphp

                                <td class="text-center dadoRetornado">{{ count($agendamentoMes[$unidade]["Agendado"]) }}</td>
                                @else
                                <td class="text-center">0</td>
                            @endif

                            @if(array_key_exists("Falta", $agendamentoMes[$unidade]))
                                @php
                                $falta = $falta + count($agendamentoMes[$unidade]["Falta"]);
                                @endphp

                                <td class="text-center dadoRetornado">{{ count($agendamentoMes[$unidade]["Falta"]) }}</td>
                                @else
                                <td class="text-center">0</td>
                            @endif

                            @if(array_key_exists("Cancelada", $agendamentoMes[$unidade]))
                                @php
                                $cancelada = $cancelada + count($agendamentoMes[$unidade]["Cancelada"]);
                                @endphp

                                <td class="text-center dadoRetornado">{{ count($agendamentoMes[$unidade]["Cancelada"]) }}</td>
                                @else
                                <td class="text-center">0</td>
                            @endif

                        </tr>

                    @else
                        <tr>
                            <td>{{ $unidade }}</td>
                            <td class="text-center">0</td>
                            <td class="text-center">0</td>
                            <td class="text-center">0</td>
                            <td class="text-center">0</td>
                        </tr>

                    @endif


                @endforeach
                <tr>
                    <td class="dadoRetornado">TOTAL</td>
                    <td class="text-center dadoRetornado">{{ $atendidos }}</td>
                    <td class="text-center dadoRetornado">{{ $agendado }}</td>
                    <td class="text-center dadoRetornado">{{ $falta }}</td>
                    <td class="text-center dadoRetornado">{{ $cancelada }}</td>
                </tr>
            </tbody>
        </table>

    </div>



    <a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
@endsection
@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{ asset('css/unidades/show.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/estatisticas/unidade.js') }}"></script>
@endpush