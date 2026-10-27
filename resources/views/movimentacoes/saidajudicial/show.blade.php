@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-4">
        <h3><i class="fas fa-user-minus"></i> Saída Judicial</h3>
        <hr/>
        <p>
            Saídas que inativam internos e não se enquadram em nenhuma outra.
        </p>
    </div>
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div>

    <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-historicoAlvara-tab" data-toggle="tab" href="#nav-historicoAlvara" role="tab" aria-controls="nav-historicoAlvara" aria-selected="true"><i class="fas fa-history"></i> Histórico de Saídas Judiciais</a>
            <a class="nav-item nav-link" id="nav-alvara-tab" data-toggle="tab" href="#nav-alvara" role="tab" aria-controls="nav-alvara" aria-selected="false"><i class="fas fa-user-minus"></i> Registrar Saída Judicial</a>
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-historicoAlvara" role="tabpanel" aria-labelledby="nav-historicoAlvara-tab">@include('movimentacoes.saidajudicial.includes._historico')</div>
        <div class="tab-pane fade" id="nav-alvara" role="tabpanel" aria-labelledby="nav-alvara-tab">@include('movimentacoes.saidajudicial.includes._saidajudicial')</div>
    </div>

    <a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
@endsection
@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{ asset('css/movimentacoes/alvara/cadastro.css') }}" rel="stylesheet">
<link href="{{ asset('css/movimentacoes/alvara/historico.css') }}" rel="stylesheet">
@endpush
@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/movimentacoes/saidajudicial/saidajudicial.js') }}"></script>
<script src="{{ asset('js/movimentacoes/saidajudicial/cadastro.js') }}"></script>
<script src="{{ asset('js/movimentacoes/alvara/historico.js') }}"></script>
@endpush