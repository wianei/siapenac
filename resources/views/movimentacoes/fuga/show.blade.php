@extends('template.tema_geral.theme')

@section('content')
<div class="titlePage color_365f80 mb-4">
    <h3><i class="fas fa-walking"></i> Fugas</h3>
    <hr/>
    <p>
        Registros de fugas.
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
        <a class="nav-item nav-link active" id="nav-historicoFuga-tab" data-toggle="tab" href="#nav-historicoFuga" role="tab" aria-controls="nav-historicoFuga" aria-selected="true"><i class="fas fa-history"></i> Histórico de Fugas</a>
        <a class="nav-item nav-link" id="nav-fuga-tab" data-toggle="tab" href="#nav-fuga" role="tab" aria-controls="nav-fuga" aria-selected="false"><i class="fas fa-walking"></i> Registrar Fuga</a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-historicoFuga" role="tabpanel" aria-labelledby="nav-historicoFuga-tab">@include('movimentacoes.fuga.includes._historico')</div>
    <div class="tab-pane fade" id="nav-fuga" role="tabpanel" aria-labelledby="nav-fuga-tab">@include('movimentacoes.fuga.includes._fuga')</div>
</div>

<a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
@endsection
@push('css')
    <link href="{{ asset('css/movimentacoes/fuga/cadastro.css') }}" rel="stylesheet">
    <link href="{{ asset('css/movimentacoes/fuga/historico.css') }}" rel="stylesheet">
@endpush
@push('script')
    <script src="{{asset('js/movimentacoes/fuga/cadastro.js')}}"></script>
    <script src="{{asset('js/movimentacoes/fuga/historico.js')}}"></script>
@endpush