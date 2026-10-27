@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-security"></i> Permissões</h3>
        <hr>
        <p>
            Permissões do SIAPEN.
        </p>
    </div>


    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('setic.permissoesInserirEvento', $id) }}">Inserir Evento</a>
        </li>
    </ul>

<h5>Controllers Cadastrados Para o Perfil {{ $nomePerfil }}</h5>

        <div class="table-responsive-md mt-3">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">#</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach($controllerPermissao as $controller)
                    <tr class="">
                        <td scope="row">{{ $loop->iteration }} </td>
                        <td scope="row">{{ $controller->texto}}</td>
                        <td class="btn-acoes color_01579b">
                            
                        <a href="{{ route('setic.permissoesGerenciarMetodos', ['idController' => $controller->id, 'idPerfil' => $id ]) }}" data-toggle="tooltip" data-placement="top" title="Ver Eventos"><i class="mdi mdi-playlist-check"></i></a>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>


@endsection
@push('css')
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
@endpush