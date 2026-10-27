@extends('template.tema_geral.theme')

@section('content')
<div class="titlePage color_365f80 mb-4">
    <h3><i class="mdi mdi-swap-horizontal"></i> Progressões e regressões de regime</h3>
    <hr/>
    <p>
        Registre as progressões e regressões de regime dos internos.
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
        <a class="nav-item nav-link active" id="nav-historicoProgressaoRegressao-tab" data-toggle="tab" href="#nav-historicoProgressaoRegressao" role="tab" aria-controls="nav-historicoProgressaoRegressao" aria-selected="true"><i class="fas fa-history"></i> Histórico de Progressões e Regressões</a>
        <a class="nav-item nav-link" id="nav-progressaoregressao-tab" data-toggle="tab" href="#nav-progressaoregressao" role="tab" aria-controls="nav-progressaoregressao" aria-selected="false"><i class="fas mdi mdi-swap-horizontal"></i> Progredir ou Regredir Regime de Internos</a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-historicoProgressaoRegressao" role="tabpanel" aria-labelledby="nav-historicoProgressaoRegressao-tab">@include('movimentacoes.progressaoregressao.includes._historico')</div>
    <div class="tab-pane fade" id="nav-progressaoregressao" role="tabpanel" aria-labelledby="nav-progressaoregressao-tab">@include('movimentacoes.progressaoregressao.includes._progressaoregressao')</div>
</div>

<a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
@endsection
@push('css')
    <link href="{{ asset('css/movimentacoes/progressaoregressao/cadastro.css') }}" rel="stylesheet">
    <link href="{{ asset('css/movimentacoes/progressaoregressao/historico.css') }}" rel="stylesheet">
@endpush
@push('script')
    <script src="{{asset('js/movimentacoes/progressaoregressao/cadastro.js')}}"></script>
    <script src="{{asset('js/movimentacoes/progressaoregressao/historico.js')}}"></script>
@endpush