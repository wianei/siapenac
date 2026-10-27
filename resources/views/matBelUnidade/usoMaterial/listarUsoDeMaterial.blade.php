@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-3">
        <h3><i class="mdi mdi-format-list-numbers "></i> Listagem de Uso de Material Bélico</h3>
        <hr>
        <p>
            Listagens dos relatórios de uso do material bélico.
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
                <th scope="col">Servidor</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados as $dado)
                <tr class="">
                    <td scope="row" class="fontSize-10px"><i><strong>{{ $loop->iteration  }}</strong></i></td>
                    <td>{{$dado->data}}</td>
                    <td>{{ $dado->servidor  }} ({{ $dado->matricula }})</td>
                    <td class="btn-acoes color_01579b">
                        <a href="{{ route('matBelUnidade.visualizarRelatorioUsoMaterial',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Visualizar relatório"><i class="mdi mdi-information-outline"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>


@endsection
@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/armaria/listarCautela.js') }}"></script>
@endpush

