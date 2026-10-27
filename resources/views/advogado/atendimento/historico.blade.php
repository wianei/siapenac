@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fa fa-history"></i> Histórico de Atendimento de Advogado</h3>
        <hr>
        <p>
            Histórico de atendimento dos advogados na unidade.

        </p>
    </div>

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
                <label for="cpf">CPF Advogado</label>
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
    <div id="links" class="mt-5">
        {{ $historico->links() }}
    </div>

    @if($historico->total() > 0)
        <h6 class="mt-2"><i>
                {{ $historico->total() }} resultados encontrados
            </i>
        </h6>
        @else
        <h6 class="mt-2"><i>
                Sua busca não obteve resultados!
            </i>
        </h6>

    @endif

    @if($historico->total() > 0)
    <div class="table-responsive-md">
        <table class="table tableLista mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Unidade</th>
                <th scope="col">Advogado</th>
                <th scope="col">Interno</th>
                <th scope="col">Data do Atendimento</th>
                <th scope="col">Horário Início</th>
                <th scope="col">Horário Término</th>
                <th scope="col">Duração</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($historico as $his)
                <tr class="">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $his->sigla }}</td>
                    <td>{{ $his->advNome }}</td>
                    <td>{{ $his->internoNome }}</td>
                    <td>{{ $his->data_atendimento }}</td>
                    <td>{{ $his->horaInicio }}</td>
                    <td>{{ $his->horaTermino }}</td>
                    <td>{{ $his->duracao."min" }}</td>
                    <td class="btn-acoes color_01579b">
                        <a href="{{ route('advogado.show',$his->advId) }}" data-toggle="tooltip" data-placement="top" title="Detalhes do Advogado"><i class="mdi mdi-information-outline"></i></a>
                        <a href="{{ route('interno.show', $his->internoId) }}" data-toggle="tooltip" data-placement="top" title="Detalhes do Interno"><i class="mdi mdi-information-outline"></i></a>

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
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/advogado/historicoAtendimento.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endpush

