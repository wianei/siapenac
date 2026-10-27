@extends('template.advogado.themeAdvogado')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-close"></i> Cancelar Agendamento</h3>
        <hr>
        <p>
            Cancelar agendamento de Atendimento.
        </p>
    </div>

    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Data</th>
                <th scope="col">Horário</th>
                <th scope="col">Interno</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados as $dado)
                <tr class="">
                    <td scope="row" class="fontSize-10px"><i><strong>{{ $loop->iteration  }}</strong></i></td>
                    <td>{{ $dado->data }}</td>
                    <td>{{ $dado->horario }}</td>
                    <td>{{ $dado->nome }}</td>
                    <td class="btn-acoes color_01579b">
                        <a href="{{ route('oab.cancelarAgendamentoUpdate', $dado->id) }}" onclick="return confirm('Deseja cancelar este agendamento?');" data-toggle="tooltip" data-placement="top" title="Cancelar agendamento"><i class="mdi mdi-cancel color_red"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>


@endsection

