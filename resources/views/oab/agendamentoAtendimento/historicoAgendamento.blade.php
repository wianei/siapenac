@extends('template.advogado.themeAdvogado')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-history"></i> Histótico de Agendamentos</h3>
        <hr>
        <p>
            Histórico de agendamentos.
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

    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Data</th>
                <th scope="col">Horário</th>
                <th scope="col">Interno</th>
                <th scope="col">Status</th>
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
                        @if($dado->status == "Agendado")
                            <span class="text-primary">{{ strtoupper($dado->status) }}</span>
                        @elseif($dado->status == "Cancelada")
                            <span class="text-danger">{{ strtoupper($dado->status) }}</span>
                        @elseif($dado->status == "Atendido")
                            <span class="text-success">{{ strtoupper($dado->status) }}</span>
                        @elseif($dado->status == "Falta")
                            <span class="text-danger"><strong>{{ strtoupper($dado->status) }}</strong></span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>


@endsection

