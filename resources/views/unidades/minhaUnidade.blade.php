@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-search"></i> Gerenciar Unidade</h3>
        <hr>

    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg)  !!}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->


    @if($dados != false)

        <div id="links" class="mt-5">
            {{ $dados->links() }}
        </div>

        @if($dados->total() > 0)
            <h6 class="mt-2"><i>
                    {{ $dados->total() }} unidade(s) gerenciavel(eis)
                </i>
            </h6>

        @else

            <h5 class="mt-5"><i>Você não pertence a uma unidade</i>
            </h5>

        @endif

        <div class="table-responsive-md mt-3">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">#</th>
                    <th scope="col">ID</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Sigla</th>
                    <th scope="col">Status</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach($dados as $dado)
                    <tr class="">
                        <td scope="row">{{ $loop->iteration }} </td>
                        <td scope="row">{{ $dado->id}}</td>
                        <td scope="row">{{ $dado->nome}}</td>
                        <td>{{ $dado->sigla }}</td>
                        <td>
                            {{ $dado->status }}
                        </td>
                        <td class="btn-acoes color_01579b">

                            <a href="{{ route('unidade.pavilhaoGerenciar', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Ver Pavilhões"><i class="mdi mdi-lan"></i></a>

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
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/setic/listarContas.js') }}"></script>
@endpush