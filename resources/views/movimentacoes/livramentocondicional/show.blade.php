@extends('template.tema_geral.theme')

@section('content')
<div class="titlePage color_365f80 mb-4">
    <h3><i class="far fa-file-alt"></i> Livramento Condicional</h3>
    <hr/>
    <p>
        Registros de Livramento Condicional.
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
        <a class="nav-item nav-link active" id="nav-historicoLivramentoCondicional-tab" data-toggle="tab" href="#nav-historicoLivramentoCondicional" role="tab" aria-controls="nav-historicoLivramentoCondicional" aria-selected="true"><i class="fas fa-history"></i> Histórico de Livramento Condicional</a>
        <a class="nav-item nav-link" id="nav-livramentocondicional-tab" data-toggle="tab" href="#nav-livramentocondicional" role="tab" aria-controls="nav-livramentocondicional" aria-selected="false"><i class="far fa-file-alt"></i> Registrar Livramento Condicional</a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-historicoLivramentoCondicional" role="tabpanel" aria-labelledby="nav-historicoLivramentoCondicional-tab">@include('movimentacoes.livramentocondicional.includes._historico')</div>
    <div class="tab-pane fade" id="nav-livramentocondicional" role="tabpanel" aria-labelledby="nav-livramentocondicional-tab">@include('movimentacoes.livramentocondicional.includes._livramentocondicional')</div>
</div>

<a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
@endsection
@push('css')
    <link href="{{ asset('css/movimentacoes/livramentocondicional/cadastro.css') }}" rel="stylesheet">
    <link href="{{ asset('css/movimentacoes/livramentocondicional/historico.css') }}" rel="stylesheet">
@endpush
@push('script')
    <script src="{{asset('js/movimentacoes/livramentocondicional/cadastro.js')}}"></script>
    <script src="{{asset('js/movimentacoes/livramentocondicional/historico.js')}}"></script>
@endpush