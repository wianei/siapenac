@extends('template.tema_geral.theme')

@section('content')
<div class="titlePage color_365f80 mb-4">
    <h3><i class="mdi mdi-church"></i> Óbitos</h3>
    <hr/>
    <p>
        Registros de Óbitos.
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
        <a class="nav-item nav-link active" id="nav-historicoObito-tab" data-toggle="tab" href="#nav-historicoObito" role="tab" aria-controls="nav-historicoObito" aria-selected="true"><i class="fas fa-history"></i> Histórico de Óbitos</a>
        <a class="nav-item nav-link" id="nav-obito-tab" data-toggle="tab" href="#nav-obito" role="tab" aria-controls="nav-obito" aria-selected="false"><i class="mdi mdi-church"></i> Registrar Óbito</a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-historicoObito" role="tabpanel" aria-labelledby="nav-historicoObito-tab">@include('movimentacoes.obito.includes._historico')</div>
    <div class="tab-pane fade" id="nav-obito" role="tabpanel" aria-labelledby="nav-obito-tab">@include('movimentacoes.obito.includes._obito')</div>
</div>

<a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
@endsection
@push('css')
    <link href="{{ asset('css/movimentacoes/obito/cadastro.css') }}" rel="stylesheet">
    <link href="{{ asset('css/movimentacoes/obito/historico.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{asset('js/movimentacoes/obito/cadastro.js')}}"></script>
    <script src="{{asset('js/movimentacoes/obito/historico.js')}}"></script>
@endpush