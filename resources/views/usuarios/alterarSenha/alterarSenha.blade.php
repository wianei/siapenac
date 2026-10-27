@extends('template.tema_geral.theme')

@section('content')

    <style type="text/css">

        .sidebar .nav{
            display: none;
        }

        .nav .nav-item.dropdown .navbar-dropdown, .navbar-nav .nav-item.dropdown .navbar-dropdown{
            display: none;
        }

    </style>

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-lock-reset"></i> Alterar Minha Senha</h3>
        <hr>
        <p>
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

    <form action="{{ route('usuario.alterarMinhaSenhaStore') }}" id="formAlterarSenha" method="post">
 {!! csrf_field() !!}
        <div class="form-row">

            <div class="form-group col-sm">
                <label for="senha">Senha Atual</label>
                <input type="password" name="senha" class="form-control" id="senha" placeholder="informe a senha atual">
            </div>

            <div class="form-group col-sm">
                <label for="novaSenha">Nova Senha</label>
                <input type="password" name="novaSenha" class="form-control" id="novaSenha" placeholder="informe a nova senha" autocomplete="off">
            </div>

            <div class="form-group col-sm">
                <label for="novaSenhaR">Repita a Nova Senha</label>
                <input type="password" name="novaSenhaR" class="form-control" id="novaSenhaR" placeholder="repita a nova senha" autocomplete="off">
            </div>

        </div>

        <div id="flashRetorno"></div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Alterar Minha Senha</button>

    </form>


@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/usuario/alterarSenha.js') }}"></script>
@endpush