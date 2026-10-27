@extends('layouts.app')

@section('content')

    <div style="width: 60%; margin: 0 auto !important; margin-top: 80px">

        @if($tipo == 'expiracaoTempoAcesso')
            <div class="flash-message">
                <div class="alert alert-danger">
                    <p>
                        <strong>ATENÇÃO!</strong>
                        <br>Sua conta possui uma data limite para acesso ao sistema, entre em contato com o {{ config('app.ti') }} para estender esse prazo: E-mail: siapenacre@gmail.com
                    </p>
                </div>
            </div> <!-- end .flash-message -->
        @endif

        @if($tipo == 'inativada')
            <div class="flash-message">
                <div class="alert alert-danger">
                    <p>
                        <strong>ATENÇÃO!</strong>
                        <br>Você passou muito tempo sem acessar o {{ config('app.name') }}. Sua conta foi inativada, por segurança. <br>Entre em contato com a {{ config('app.ti') }}, E-mail: siapenacre@gmail.com
                    </p>
                </div>
            </div> <!-- end .flash-message -->
        @endif

        @if($tipo == 'inativa')
            <div class="flash-message">
                <div class="alert alert-danger">
                    <p>
                        <strong>ATENÇÃO!</strong>
                        <br>Sua conta está inativa! <br>Entre em contato com a {{ config('app.ti') }}, E-mail: siapenacre@gmail.com
                    </p>
                </div>
            </div> <!-- end .flash-message -->
        @endif


    </div>
@endsection
@push('css')
<link href="{{ asset('css/login/login.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/usuario/recuperarConta.js') }}"></script>
@endpush
