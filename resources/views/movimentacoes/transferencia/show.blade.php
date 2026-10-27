@extends('template.tema_geral.theme')

@section('content')
<div class="titlePage color_365f80 mb-4">
    <h3><i class="fas fa-arrow-right"></i> Transferência de Unidade</h3>
    <hr/>
    <p>
        Registre as trasferências de internos entre unidades prisionais.
    </p>
</div>
<div class="flash-message">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))

            <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg)  !!}
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>

<nav>
    <div class="nav nav-tabs fontSize-13px" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-historicoTransferencia-tab" data-toggle="tab" href="#nav-historicoTransferencia" role="tab" aria-controls="nav-historicoTransferencia" aria-selected="true"><i class="fas fa-history"></i> Histórico de Transferências</a>
        <a class="nav-item nav-link" id="" data-toggle="" href="{{ route('movimentacoes.transferenciaTransferir') }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-arrow-up"></i> Transferir Internos</a>
        <a class="nav-item nav-link" id="" data-toggle="" href="{{ route('movimentacoes.transferencia.transferenciaProgressao') }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-arrow-up"></i> Transferir Internos com Progressão ou Regressão de Regime</a>
        <a class="nav-item nav-link" id="" data-toggle="" href="{{ route('movimentacoes.receberInterno') }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-arrow-down"></i> Receber Internos</a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-historicoTransferencia" role="tabpanel" aria-labelledby="nav-historicoTransferencia-tab">@include('movimentacoes.transferencia.includes._historico')</div>
</div>

<a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
@endsection
@push('css')
    <link href="{{ asset('css/movimentacoes/transferencia/cadastro.css') }}" rel="stylesheet">
    <link href="{{ asset('css/movimentacoes/transferencia/historico.css') }}" rel="stylesheet">
@endpush
@push('script')
    <script src="{{asset('js/movimentacoes/transferencia/historico.js')}}"></script>
@endpush