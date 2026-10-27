@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-lock"></i>Fechamento do Livro de Ocorrência</h3>
        <hr>
        <p>
            Fecha o livro de ocorrência da unidade impedindo novas inserções.
        </p>
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg) !!}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->


    <form action="{{ route('ocorrencia.livroFechamentoStore') }}" id="formFechamento" method="post">
{!! csrf_field() !!}
        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="dt_fechamento">Data do Fechamento do Livro</label>
                <input type="text" name="dt_fechamento" class="form-control" id="dt_fechamento" value="{{ $dataFechamento }} {{ $horarioFechamento }}" autocomplete="off" disabled>
            </div>

            <div class="form-group col-sm-3">
                <label for="password">Senha de Acesso ao {{ config('app.name') }}:</label>
                <input type="password" name="password" class="form-control" id="password" autocomplete="off" required>
            </div>

        </div>
        <button type="submit" id="btnSubmitForm" onclick="return confirm('Deseja realmente fechar o livro de ocorrência?');" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Fechar Livro de Ocorrência</button>

    </form>

@endsection

@push('css')
<link href="{{ asset('css/ocorrencia/cadastro/cadastro.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/ocorrencia/fechamentoLivro.js') }}"></script>
@endpush

