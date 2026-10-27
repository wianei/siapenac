@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-arrow-down"></i> Solicitações Realizadas por Outras Unidades</h3>
        <hr>
        <p>
            Solicitações de internos realizadas por outras unidade prisionais.
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

    @include('movimentacoes.solicitacaoInterno._includes._navSolicitacaoInterno')

    @if(count($solicitacoes) > 0)
        <h5 class="mt-5">Internos Solicitados por Outras Unidades<i class="mdi mdi-arrow-down"></i></h5>
        <div id="divInternosSolicitacao" class="table-responsive mt-3">
            <table class="table mb-3" id="tableInternosSolicitacao">
                <thead class="background_287ec3 color_FFF">
                <tr class="">
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Regime</th>
                    <th scope="col">Status</th>
                    <th scope="col">Data Solicitação</th>
                    <th scope="col">Solicitante</th>
                    <th scope="col">Observação</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoInternosRecebimento">
                @foreach($solicitacoes as $solicitacao)
                    <tr class="">
                        <td class="fontSize-11px">{{$solicitacao['idInterno']}}</td>
                        <td>
                            @if(file_exists(storage_path('app/arquivos/internos/fotos/' . $solicitacao['idInterno'] . "/" . FotoService::getFotoPrincipalByIdTipo($solicitacao['idInterno'],'interno') )))
                                <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $solicitacao['idInterno'] . "/". FotoService::getFotoPrincipalByIdTipo($solicitacao['idInterno'],'interno'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                            @else
                                <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                            @endif
                        </td>
                        <td class="fontSize-11px">{{$solicitacao['nomeInterno']}}</td>
                        <td class="fontSize-11px">{{$solicitacao['regime']}}</td>
                        <td class="fontSize-11px">{{ strtoupper($solicitacao['status'])}}</td>
                        <td class="fontSize-11px">{{$solicitacao['dataSolicitacao']}}</td>
                        <td class="fontSize-11px">{{$solicitacao['unidadeSolicitante']}}</td>
                        {{--<td class="fontSize-11px">{{$solicitacao['unidadeSolicitada']}}-{{$solicitacao['pavilhaoSolicitada']}}-{{$solicitacao['alaSolicitada']}}-{{$solicitacao['celaSolicitada']}}</td>--}}
                        <td style="white-space: pre-wrap !important; word-wrap:break-word !important; font-size: 10px">{{$solicitacao['obs']}}</td>
                        <td class="btn-acoes color_01579b">

                            <a href="{{ route('solicitacaoInterno.confirmarSolicitacaoStore',$solicitacao['chaveAutorizacao']) }}" data-toggle="tooltip" data-placement="top" title="Enviar Interno" class="finalizar"><i class="mdi mdi-check-circle-outline"></i></a>

                            <a href="{{ route('solicitacaoInterno.CancelarSolicitacao',$solicitacao['chaveAutorizacao']) }}" data-toggle="tooltip" data-placement="top" title="Cancelar Solicitação" class="cancelar text-danger"><i class="mdi mdi-close-circle-outline"></i></a>


                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <h5 class="mt-5">Sem Solicitações Pendentes de Confirmação.</h5>
    @endif

    @if(count($solicitacoesCanceladas) > 0)
        <h5 class="mt-5">Internos Solicitados por Outras Unidades Cancelados<i class="mdi mdi-arrow-down"></i></h5>
        <div id="divInternosSolicitacao" class="table-responsive mt-3">
            <table class="table mb-3" id="tableInternosSolicitacao">
                <thead class="background_e65251 color_FFF">
                <tr class="">
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Regime</th>
                    <th scope="col">Status</th>
                    <th scope="col">Data Solicitação</th>
                    <th scope="col">Solicitante</th>
                    <th scope="col">Observação</th>
                </tr>
                </thead>
                <tbody id="retornoInternosRecebimento">
                @foreach($solicitacoesCanceladas as $solicitacoesCancelada)
                    <tr class="">
                        <td class="fontSize-11px">{{$solicitacoesCancelada['idInterno']}}</td>
                        <td>
                            @if(file_exists(storage_path('app/arquivos/internos/fotos/' . $solicitacoesCancelada['idInterno'] . "/" . FotoService::getFotoPrincipalByIdTipo($solicitacoesCancelada['idInterno'],'interno') )))
                                <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $solicitacoesCancelada['idInterno'] . "/". FotoService::getFotoPrincipalByIdTipo($solicitacoesCancelada['idInterno'],'interno'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                            @else
                                <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                            @endif
                        </td>
                        <td class="fontSize-11px">{{$solicitacoesCancelada['nomeInterno']}}</td>
                        <td class="fontSize-11px">{{$solicitacoesCancelada['regime']}}</td>
                        <td class="fontSize-11px">{{ strtoupper($solicitacoesCancelada['status'])}}</td>
                        <td class="fontSize-11px">{{$solicitacoesCancelada['dataSolicitacao']}}</td>
                        <td class="fontSize-11px">{{$solicitacoesCancelada['unidadeSolicitante']}}</td>
                        {{--<td class="fontSize-11px">{{$solicitacao['unidadeSolicitada']}}-{{$solicitacao['pavilhaoSolicitada']}}-{{$solicitacao['alaSolicitada']}}-{{$solicitacao['celaSolicitada']}}</td>--}}
                        <td style="white-space: pre-wrap !important; word-wrap:break-word !important; font-size: 10px">{{$solicitacoesCancelada['obsCancelamento']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif


    {{--<h5 class="mt-5">Solicitações Canceladas <i class="mdi mdi-arrow-down"></i></h5>--}}
    {{--<div id="divInternosSolicitacao" class="table-responsive mt-3">--}}
        {{--<table class="table mb-3" id="tableInternosSolicitacao">--}}
            {{--<thead class="background_e65251 color_FFF">--}}
            {{--<tr class="">--}}
                {{--<th scope="col">Prontuário</th>--}}
                {{--<th scope="col">Foto</th>--}}
                {{--<th scope="col">Nome</th>--}}
                {{--<th scope="col">Status</th>--}}
                {{--<th scope="col">Data Solicitação</th>--}}
                {{--<th scope="col">Localização</th>--}}
                {{--<th scope="col">Observação</th>--}}
            {{--</tr>--}}
            {{--</thead>--}}
            {{--<tbody id="retornoInternosRecebimento">--}}
            {{--@foreach($solicitacoesCanceladas as $solicitacoesCancelada)--}}
                {{--<tr class="">--}}
                    {{--<td class="fontSize-11px">{{$solicitacoesCancelada['idInterno']}}</td>--}}
                    {{--<td>--}}
                        {{--@if(file_exists(storage_path('app/arquivos/internos/fotos/' . $solicitacoesCancelada['idInterno'] . "/" . FotoService::getFotoPrincipalByIdTipo($solicitacoesCancelada['idInterno'],'interno') )))--}}
                            {{--<img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $solicitacoesCancelada['idInterno'] . "/". FotoService::getFotoPrincipalByIdTipo($solicitacoesCancelada['idInterno'],'interno'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">--}}
                        {{--@else--}}
                            {{--<img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">--}}
                        {{--@endif--}}
                    {{--</td>--}}
                    {{--<td class="fontSize-11px">{{$solicitacoesCancelada['nomeInterno']}}</td>--}}
                    {{--<td class="fontSize-11px">{{ strtoupper($solicitacoesCancelada['status'])}}</td>--}}
                    {{--<td class="fontSize-11px">{{$solicitacoesCancelada['dataSolicitacao']}}</td>--}}
                    {{--<td class="fontSize-11px">{{$solicitacoesCancelada['unidadeSolicitada']}}</td>--}}
                    {{--<td style="white-space: pre-wrap !important; word-wrap:break-word !important; font-size: 10px">{{$solicitacoesCancelada['obs']}}</td>--}}
                {{--</tr>--}}
            {{--@endforeach--}}
            {{--</tbody>--}}
        {{--</table>--}}
    {{--</div>--}}


@endsection
@push('css')
<link href="{{ asset('css/solicitacaoInterno/solicitacaoInterno.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/solicitacaoInterno/historicoSolicitacoes.js') }}"></script>
@endpush