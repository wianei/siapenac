@extends('template.tema_geral.theme')

@section('content')
<div class="titlePage color_365f80 mb-4">
    <h3><i class="fas fa-sync"></i> Mudanças de Cela</h3>
    <hr/>
    <p>
        Registros de mudanças de cela.
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
        <a class="nav-item nav-link active" id="nav-historicoMudancaCela-tab" data-toggle="tab" href="#nav-historicoMudancaCela" role="tab" aria-controls="nav-historicoMudancaCela" aria-selected="true"><i class="fas fa-history"></i> Histórico de Mudanças de Cela</a>
        <a class="nav-item nav-link" id="nav-mudancacela-tab" data-toggle="tab" href="#nav-mudancacela" role="tab" aria-controls="nav-mudancacela" aria-selected="false"><i class="fas fa-sync"></i> Registrar Mudança de Cela</a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-historicoMudancaCela" role="tabpanel" aria-labelledby="nav-historicoMudancaCela-tab">@include('movimentacoes.mudancacela.includes._historico')</div>
    <div class="tab-pane fade" id="nav-mudancacela" role="tabpanel" aria-labelledby="nav-mudancacela-tab">@include('movimentacoes.mudancacela.includes._mudancacela')</div>
</div>

<a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>

<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div id="fototipo"></div>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
            </div>
            <div class="modal-body embed-responsive text-center">
                <img src="" id="imagepreview" style="max-width:90%">
            </div>
            <hr/>
            <div class="text-left">
                <div id="fotolegenda"></div>
                <div id="fotodatainserida"></div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('css')
    <link href="{{ asset('css/movimentacoes/mudancacela/cadastro.css') }}" rel="stylesheet">
    <link href="{{ asset('css/movimentacoes/mudancacela/historico.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush
@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/movimentacoes/mudancacela/cadastro.js') }}"></script>
<script src="{{ asset('js/movimentacoes/mudancacela/historico.js') }}"></script>
<script src="{{ asset('js/outros/buscaInterno.js') }}"></script>
@endpush