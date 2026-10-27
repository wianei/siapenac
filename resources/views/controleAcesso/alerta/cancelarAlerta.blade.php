@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-close-circle"></i> Cancelar Alerta</h3>
        <p>
            Cancelar alerta ao registrar entrada através do controle de acesso.
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

    <form action="{{ route('controleAcesso.alertaEntradaGeralCancelarStore', $id) }}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}


        <div class="form-row">
            <div class="form-group col-sm-12">
                <label for="motivo">Motivação do cancelamento</label>
                <textarea name="motivo" id="motivo" class="form-control" required></textarea>
            </div>
        </div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3">
            <i class="fas fa-save"></i>Cancelar Alerta
        </button>

    </form>

@endsection

@push('css')
<link href="{{ asset('css/controleAcesso/alerta/cadastro.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/controleAcesso/alerta/cadastroAlerta.js') }}"></script>
<script src="{{ asset('vendor/userCam/jquery.userCam.min.js') }}"></script>
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/outros/buscaVisita.js') }}"></script>
<script src="{{ asset('js/outros/buscaAdvogado.js') }}"></script>
<script src="{{ asset('js/outros/buscaVisitanteExterno.js') }}"></script>

@endpush

