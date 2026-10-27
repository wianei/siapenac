@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-close-circle-outline"></i> Cancelar Solicitação de Interno</h3>
        <hr>
        <p>
            Cancelar a solicitação do interno realizado por outra unidade prisional.
        </p>
    </div>
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg)  !!}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    <form action="{{ route('solicitacaoInterno.CancelarSolicitacaoUnidadeSolicitadaStore', $chaveCancelamento) }}" method="post" enctype="multipart/form-data" id="formCancelarSolicitação">
        {!! csrf_field() !!}

        <h5 class="mt-4">Cancelar Solicitação do Interno:</h5>
        <h6 class="mt-2">{{ $solicitacaoDados->nome }}</h6>
        <h5 class="mt-4">Unidade Solicitante:</h5>
        <h6 class="mt-2">{{ $solicitacaoDados->nome_unidade_solicitante }}</h6>


        <h5 class="mt-4">Motivação:</h5>
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="motivo">Motivo e observações</label>
                <textarea class="form-control" id="motivo" name="motivo" aria-label="motivo"></textarea>
            </div>
        </div>

        <div id="flashRetorno"></div>

        <button type="submit" onclick="return confirm('Deseja cancelar essa solicitação?');" class="btn btn-primary mt-2"><i class="fas fa-check"></i> Cancelar Solicitação de Interno</button>
    </form>

@endsection
@push('css')
<link href="{{ asset('css/solicitacaoInterno/solicitacaoInterno.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/solicitacaoInterno/cancelarSolicitacao.js') }}"></script>
@endpush