@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="imgLogo"><img src="{{ asset("imagens/template/logo_secretaria.png") }}" alt="Logo estado"></div>

        <div class="flash-message">
            @if(Session::has('message'))
                <p class="alert alert-info">{{ Session::get('message') }}</p>
            @endif
        </div> <!-- end .flash-message -->

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                    
                    @if(config('app.ambiente') == "hml")
                        <div class="alert alert-warning text-center">
                            <strong>** ATENÇÃO! AMBIENTE DE TREINAMENTO **</strong>
                        </div>
                    @endif

                <div class="panel panel-default">
                    <div class="panel-heading">Insira os dados para acessar o sistema</div>
                                       
                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('cpf') ? ' has-error' : '' }}">
                                <label for="cpf" class="col-md-4 control-label">CPF</label>

                                <div class="col-md-6">
                                    <input id="cpf" type="text" class="form-control" name="cpf" value="{{ old('cpf') }}" required autofocus>

                                    @if ($errors->has('cpf'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('cpf') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-4 control-label">Senha</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="password" required>

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <div class="g-recaptcha" data-sitekey="6LdogMEZAAAAAIEgvZPvvpGIaspdvB2FlMpDO02K"></div>
                                </div>
                                <div id="flashRetorno" class="col-md-8 col-md-offset-4">
                                </div>
                                <div class="col-md-8 col-md-offset-4">
                                    <br>
                                    <button type="submit" class="btn btn-primary">
                                        Entrar
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <a href="{{ route('usuario.esqueciMinhaSenha') }}"> Esqueci minha senha</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('css')
<link href="{{ asset('css/login/login.css') }}" rel="stylesheet">
@endpush
@push('script')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="{{ asset('js/auth/login.js') }}"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
@endpush