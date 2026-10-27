@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-format-list-bulleted"></i> Gerenciamento da Lista de Objetos da Ocorrência</h3>
        <hr>
        <p>
            Faz o Gerenciamento da lista de objetos relacionados à ocorrência.
        </p>
    </div>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('setic.listaObjetosOcorrencia') }}">Listar Todos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('setic.novoObjetosOcorrencia') }}">Novo Objeto</a>
        </li>
    </ul>

    <hr>
    
    @if($dados != false)

        <div class="table-responsive-md mt-3">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">#</th>
                    <th scope="col">Natureza</th>
                    <th scope="col">Objeto</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach($dados as $dado)
                    <tr class="">
                        <td scope="row">{{ $loop->iteration }} </td>
                        <td scope="row">{{ $dado->natureza}}</td>
                        <td scope="row">{{ $dado->nome}}</td>
                        <td class="btn-acoes color_01579b">

                            <a href="{{ route('setic.editarObjetosOcorrencia', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Editar Objeto"><i class="mdi mdi-pencil-circle-outline"></i></a>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    @endif

@endsection
@push('css')
<link href="{{ asset('css/agenda/consulta.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/setic/log.js') }}"></script>
@endpush