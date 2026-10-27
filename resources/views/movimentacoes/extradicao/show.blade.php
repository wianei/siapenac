@extends('template.tema_geral.theme')

@section('content')
<div class="titlePage color_365f80 mb-4">
    <h3><i class="fas fa-globe-americas"></i> Extradição</h3>
    <hr/>
    <p>
        Registros de Extradições.
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
        <a class="nav-item nav-link active" id="nav-historicoExtradicao-tab" data-toggle="tab" href="#nav-historicoExtradicao" role="tab" aria-controls="nav-historicoExtradicao" aria-selected="true"><i class="fas fa-history"></i> Histórico de Extradição</a>
        <a class="nav-item nav-link" id="nav-extradicao-tab" data-toggle="tab" href="#nav-extradicao" role="tab" aria-controls="nav-extradicao" aria-selected="false"><i class="fas fa-globe-americas"></i> Registrar Extradição</a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-historicoExtradicao" role="tabpanel" aria-labelledby="nav-historicoExtradicao-tab">@include('movimentacoes.extradicao.includes._historico')</div>
    <div class="tab-pane fade" id="nav-extradicao" role="tabpanel" aria-labelledby="nav-extradicao-tab">@include('movimentacoes.extradicao.includes._extradicao')</div>
</div>

<a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
@endsection
@push('css')
    <link href="{{ asset('css/movimentacoes/extradicao/cadastro.css') }}" rel="stylesheet">
    <link href="{{ asset('css/movimentacoes/extradicao/historico.css') }}" rel="stylesheet">
@endpush
@push('script')
    <script src="{{asset('js/movimentacoes/extradicao/cadastro.js')}}"></script>
    <script src="{{asset('js/movimentacoes/extradicao/historico.js')}}"></script>
@endpush