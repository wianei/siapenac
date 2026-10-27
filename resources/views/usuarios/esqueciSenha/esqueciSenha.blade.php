@extends('layouts.app')

@section('content')

    <div style="width: 60%; margin: 0 auto !important; margin-top: 80px">

        <div class="flash-message">
            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has('alert-' . $msg))

                    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                @endif
            @endforeach
        </div> <!-- end .flash-message -->

        <div class="form-row">
            <div class="form-group col-sm-12">
                <h3>Esqueceu sua senha?</h3>
                <p style="background: #e2e7ea; padding: 5px">
                    Para receber uma nova senha, confirme o e-mail cadastrado.
                </p>
            </div>
        </div>

        <form style="width: 100%" action="{{ route('usuario.esqueciMinhaSenhaStore') }}" id="formRecuperarSenha" method="post" enctype="multipart/form-data">

            {!! csrf_field() !!}

            <div class="form-row">
                <div class="form-group col-sm-6">
                    <label for="cpf">Informe o CPF cadastrado</label>
                    <input type="text" class="form-control" name="cpf" id="cpf" placeholder="Informe o CPF" required>
                </div>

                <div class="form-group col-sm-6">
                    <label for="email">E-mail</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Informe o e-mail" required>
                </div>

                <div class="form-group col-sm-12 mt-3">
                    <div id="flashRetorno"></div>
                    <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Enviar Senha
                    </button>
                </div>

            </div>

        </form>
    </div>

@endsection
@push('css')
<link href="{{ asset('css/login/login.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/usuario/recuperarConta.js') }}"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
@endpush
