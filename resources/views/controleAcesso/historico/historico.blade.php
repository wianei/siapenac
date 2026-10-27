@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fa fa-history"></i> Histórico</h3>
        <hr>
        <p>
            Histórico de entradas na unidade.
        </p>
    </div>
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    <form action="#">

        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="presidio">Unidade</label>
                <select id="presidio" name="presidio" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}">
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="tipo">Tipo</label>
                <select id="tipo" name="tipo" class="form-control">
                    <option value="">Selecione</option>
                    <option value="visita">Visita</option>
                    <option value="advogado">Advogado</option>
                    <option value="visitante externo">Visitante Externo</option>
                    <option value="servidor">Servidor</option>
                    <option value="interno">Interno</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="cpf">CPF</label>
                <input type="text" name="cpf" class="form-control" id="cpf" placeholder="Informe o CPF">
            </div>

            <div class="form-group col-sm-3">
                <label for="dataInicial">Data Inicial</label>
                <input type="text" name="dataInicial" class="form-control" id="dataInicial" placeholder="Informe a data inicial" autocomplete="off">
            </div>

            <div class="form-group col-sm-3">
                <label for="dataFinal">Data Final</label>
                <input type="text" name="dataFinal" class="form-control" id="dataFinal" placeholder="Informe a data final" autocomplete="off">
            </div>

        </div>

        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-search"></i> Buscar Registros</button>

    </form>

    @if(count($historico) > 0)
        <h6 class="mt-2"><i>Sua busca retornou
                {{ count($historico) }} resultados
            </i>
        </h6>

    @else

        <h5 class="mt-5"><i>Sua busca não obteve resultados! </i>
        </h5>

    @endif

@if(count($historico) > 0)
    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">Nome</th>
                <th scope="col">Entrada</th>
                <th scope="col">Saída</th>
                <th scope="col">Localização</th>
                <th scope="col">Tipo</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($historico as $his)
                <tr class="">
                    <td>{{ $his['nome'] }}<br>CPF: {{ $his['cpf'] }}</td>
                    <td>{{ $his['dataUltimaEntrada']." - ".$his['horaUltimaEntrada'] }}</td>
                    <td>{{ $his['dataUltimaSaida']." - ".$his['horaUltimaSaida'] }}</td>
                    <td>{{ $his['localizacao'] }}</td>
                    <td> {{ strtoupper($his['tabela']) }}</td>
                    <td class="btn-acoes color_01579b">
                        @if($his['tabela'] == "visitante externo")
                            <a href="{{ route('controleAcesso.editarCadastro', $his['cpf']) }}" data-toggle="tooltip" data-placement="top" title="Ver Cadastro"><i class="mdi mdi-information-outline"></i></a>
                        @endif
                        @if($his['suspensao'] == false)
                        <a href="{{ route('controleAcesso.suspenderEntrada', $his['cpf']) }}" data-toggle="tooltip" data-placement="top" title="Suspender Entrada"><i class="mdi mdi-cancel color_red"></i></a>
                            @else
                        <a href="{{ route('controleAcesso.liberarEntrada', $his['cpf']) }}" data-toggle="tooltip" data-placement="top" title="Liberar Entrada"><i class="mdi mdi-arrow-up-bold color_green"></i></a>
                        @endif
                        @if(PermissaoService::getVerifyPermissionView("ControleAcessoController","editarRegistrarSaida") and strtoupper($his['tabela']) == "VISITA" and $his['editavel'] and ($his['dataUltimaSaida'] != null))
                            <a href="{{ route('controleAcesso.editarRegistrarSaida',$his['id']) }}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>
                        @endif

                        <a href=" {{ route('controleAcesso.alertaEntrada', $his['cpf']) }}" data-toggle="tooltip" data-placement="top" title="Alerta"><i class="mdi mdi-alert color_orange"></i></a>
                        {{--<a href="#" data-toggle="tooltip" data-placement="top" title="Rastreamento"><i class="mdi mdi-map-marker-radius"></i></a>--}}
                    </td>
                </tr>
                <tr>
                    <td style="white-space: unset !important;" colspan="6" class="border1pxSolidRGBA0125"><em>Motivo: {{ $his['motivo'] }}</em></td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@endif

@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/controleAcesso/historico/historico.js') }}"></script>
@endpush


