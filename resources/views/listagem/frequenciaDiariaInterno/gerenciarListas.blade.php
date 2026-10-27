@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-settings"></i> Gerenciar Listas</h3>
        <hr>
        <p>
            Gerencia as listas criadas.
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

    @include('listagem.frequenciaDiariaInterno._includes._navMenuFrequencia')

    <div id="divListas" class="table-responsive mt-3">
        <table class="table mb-3">
            <thead class="background_287ec3 color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Nome</th>
                <th scope="col">Tipo</th>
                <th scope="col">Status</th>
                <th scope="col">Data Criação</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($listas as $lista)
                <tr class="">
                    <td class="">{{ $loop->iteration }}</td>
                    <td class="">{{ $lista->nome }}</td>
                    <td class="">{{ $lista->tipo }}</td>
                    <td class="">{{ $lista->status }}</td>
                    <td class="">{{ $lista->dataCriacao }}</td>
                    <td class="btn-acoes color_01579b">

                        <a href="{{ route('listagem.editarLista', $lista->id) }}" data-toggle="tooltip" data-placement="top" title="Editar dados da lista" class="finalizar"><i class="mdi mdi-pencil-circle-outline"></i></a>

                        {{--<a href="{{ route('solicitacaoInterno.CancelarSolicitacao',$solicitacao['chaveAutorizacao']) }}" data-toggle="tooltip" data-placement="top" title="Cancelar Solicitação" class="cancelar text-danger"><i class="mdi mdi-close-circle-outline"></i></a>--}}


                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{ asset('css/listagem/aberto/listagemAberto.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/listagem/frequenciaDiariaInterno/registrarPresenca.js') }}"></script>
@endpush