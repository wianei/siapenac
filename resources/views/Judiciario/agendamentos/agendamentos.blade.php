@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="far fa-calendar-alt"></i> Agendamentos</h3>
        <hr>
        <p>
            Lista de agendamentos para ascolta de audiência.
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
    <form action="{{ route('judiciario.agendamentos') }}" method="get">

        <div class="form-row">
            <div class="form-group col-sm-3" id="divHoraRetorno">
                <label for="data">Data Inicial</label>
                <input type="text" name="data" class="form-control" id="data" placeholder="Data inicial" autocomplete="off">
            </div>
            <div class="form-group col-sm-3" id="divHoraRetorno">
                <label for="dataFinal">Data Final</label>
                <input type="text" name="dataFinal" class="form-control" id="dataFinal" placeholder="Data final" autocomplete="off">
            </div>
            <div class="form-group col-sm-3">
                <label for="horario">Horário</label>
                <select id="horario" name="horario" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divHoraRetorno">
                <label for="documento">Número Documento</label>
                <input type="text" name="documento" class="form-control" id="documento" placeholder="Informe o número documento">
            </div>

            <div class="form-group col-sm-3">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="Cancelada">
                        Cancelada
                    </option>
                    <option value="Confirmada">
                        Confirmada
                    </option>
                    <option value="Solicitada">
                        Solicitada
                    </option>
                </select>

            </div>

        </div>

        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-search"></i> Pesquisar Agendamentos</button>

    </form>
    @if($dados != false)

        <div id="links" class="mt-5">
            {{ $dados->links() }}
        </div>

        @if($dados->total() > 0)
            <h6 class="mt-2"><i>Sua busca retornou
                    {{ $dados->total() }} resultados
                </i>
            </h6>

        @else

            <h5 class="mt-5"><i>Sua busca não obteve resultados! </i>
            </h5>

        @endif

        <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Data</th>
                <th scope="col">Horário</th>
                <th scope="col">Documento</th>
                <th scope="col">Destino</th>
                <th scope="col">Status</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados as $dado)
            <tr class="">
                <td scope="row" class="fontSize-10px"><i><strong>{{($dados->currentPage() * $dados->perPage()) - $dados->perPage() + $loop->iteration  }}</strong></i></td>
                <td>{{ $dado->dataSaida}}</td>
                <td>{{ $dado->hora_saida }}</td>
                <td>{{ $dado->documento }}</td>
                <td>{{ $dado->destino }}</td>
                <td>
                    @if($dado->status == "Confirmada")
                        <button type="button" class="btn btn-success btn-fw">Confirmada</button>
                    @elseif($dado->status == "Cancelada")
                        <button type="button" class="btn btn-danger btn-fw">Cancelada</button>
                    @elseif($dado->status == "Parcialmente Cancelada")
                        <button type="button" class="btn btn-danger btn-fw">Parcialmente Cancelada</button>
                    @elseif($dado->status == "Solicitada")
                        <button type="button" class="btn btn-warning btn-fw">Solicitada</button>
                    @endif
                </td>
                <td class="btn-acoes color_01579b">
                    <a href="{{ route('judiciario.show', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>

                    @if($dado->status != "Cancelada")
                        <a href="{{ route('judiciario.cancelarAgendamento', $dado->id) }}" onclick="return confirm('Deseja mesmo fazer o cancelamento?');" data-toggle="tooltip" data-placement="top" title="Cancelar"><i class="mdi mdi-close-circle-outline color_red"></i></a>
                    @endif
                </td>
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
<script src="{{ asset('js/judiciario/agendamento/agendamentos.js') }}"></script>
@endpush