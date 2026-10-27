@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        @if(isset($alerta))
        <h3><i class="mdi mdi-alert"></i> Alerta para Entrada</h3>
        @else
        <h3><i class="mdi mdi-cancel"></i> Proibir Entrada</h3>
        @endif
        <hr>
            @if(isset($alerta))

            <p>
            Mostrar alerta ao entrar na unidade prisional.
            </p>
            <p>
                @else
            Proibir entrada na unidade prisional.
            </p>
            @endif
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    @if(isset($alerta))
    <form action="{{ route('controleAcesso.alertaEntradaStore', $id) }}" method="post" enctype="multipart/form-data">
        @else
    <form action="{{ route('controleAcesso.suspenderEntradaStore', $id) }}" method="post" enctype="multipart/form-data">
    @endif
        {{ csrf_field() }}
        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="dias">Dias a partir de hoje</label>
                <input type="text" name="dias" class="form-control" id="dias" placeholder="Ex: 10" required>
            </div>

            <div class="form-group col-sm-12">
                @if(isset($alerta))
                <label for="motivo">Texto para alerta</label>
                @else
                <label for="motivo">Motivo</label>
                @endif
                <textarea name="motivo" id="motivo" class="form-control" required></textarea>
            </div>
        </div>

        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3">
            @if(isset($alerta))
            <i class="fas fa-save"></i>Criar Alerta
                @else
            <i class="fas fa-save"></i>Proibir Entrada
            @endif
        </button>

    </form>

@endsection

@push('css')
<link href="{{ asset('css/controleAcesso/cadastro/editar.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('vendor/userCam/jquery.userCam.min.js') }}"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/controleAcesso/suspenderEntrada/suspender.js') }}"></script>
@endpush

