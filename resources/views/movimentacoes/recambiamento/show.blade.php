@extends('template.tema_geral.theme')

@section('content')
<div class="titlePage color_365f80 mb-4">
    <h3><i class="fas fa-angle-double-right"></i> Recambiamento</h3>
    <hr/>
    <p>
        Registros de Recambiamentos.
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
        <a class="nav-item nav-link active" id="nav-historicoRecambiamento-tab" data-toggle="tab" href="#nav-historicoRecambiamento" role="tab" aria-controls="nav-historicoRecambiamento" aria-selected="true"><i class="fas fa-history"></i> Histórico de Recambiamentos</a>
        <a class="nav-item nav-link" id="nav-recambiamento-tab" data-toggle="tab" href="#nav-recambiamento" role="tab" aria-controls="nav-recambiamento" aria-selected="false"><i class="fas fa-angle-double-right"></i> Registrar Recambiamento</a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-historicoRecambiamento" role="tabpanel" aria-labelledby="nav-historicoRecambiamento-tab">@include('movimentacoes.recambiamento.includes._historico')</div>
    <div class="tab-pane fade" id="nav-recambiamento" role="tabpanel" aria-labelledby="nav-recambiamento-tab">@include('movimentacoes.recambiamento.includes._recambiamento')</div>
</div>

<a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
@endsection
@push('css')
    <link href="{{ asset('css/movimentacoes/recambiamento/cadastro.css') }}" rel="stylesheet">
    <link href="{{ asset('css/movimentacoes/recambiamento/historico.css') }}" rel="stylesheet">
@endpush
@push('script')
    <script src="{{asset('js/movimentacoes/recambiamento/cadastro.js')}}"></script>
    <script src="{{asset('js/movimentacoes/recambiamento/historico.js')}}"></script>
@endpush