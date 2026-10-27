@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fa fa-plus"></i> Informar Telefone do Servidor</h3>
        <hr>
        <p>
            Informar o telefone do servidor para liberação da cautela de equipamentos.
        </p>
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg)  !!}
                </p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->


    <form action="{{ route('armaria.inserirTelefoneServidorStore') }}" method="post">

        {!! csrf_field() !!}

        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="matricula">Matrícula</label>
                <input type="text" name="matricula" value="" class="form-control" id="matricula" placeholder="Informe a matricula do servidor" required>
            </div>
            <div class="form-group col-sm-3">
                <label for="telefone">Telefone (DDD + número)</label>
                <input type="text" name="telefone" value="" class="form-control" id="telefone" placeholder="Informe o número de telefone do servidor" required>
            </div>
            <div class="form-group col-sm-3">
                <button type="submit" class="btn btn-primary mt-4"><i class="fas fa-save"></i> Salvar Dados</button>
            </div>
        </div>

    </form>


@endsection

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/armaria/inserirTelefone.js') }}"></script>
@endpush