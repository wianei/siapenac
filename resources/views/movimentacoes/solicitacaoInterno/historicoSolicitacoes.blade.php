@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-arrow-down"></i> Solicitação de Interno</h3>
        <hr>
        <p>
            Solicitar interno de outra unidade prisional.
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
        <form action="{{ route('solicitacaoInterno.cancelarSolicitacaoStore') }}" id="formCancelarSolicitacao">
            <div id="divInternosSolicitacao" class="table-responsive mt-5">
                <table class="table mb-3" id="tableInternosSolicitacao">
                    <thead class="background_287ec3 color_FFF">
                    <tr class="">
                        <th scope="col"><input type="checkbox" id="checkTodosInternos"></th>
                        <th scope="col">Prontuário</th>
                        <th scope="col">Foto</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Regime</th>
                        <th scope="col">Status</th>
                        <th scope="col">Data Solicitação</th>
                        <th scope="col">Localização</th>
                        <th scope="col">Observação</th>
                    </tr>
                    </thead>
                    <tbody id="retornoInternosRecebimento">
                    @foreach($solicitacoes as $solicitacao)
                        <tr class="">
                            <td><input type="checkbox" name="cancelamentoSolicitacaoCheck[]" class="checkbox" id="check_{{$solicitacao['idInterno']}}" value="{{$solicitacao['idInterno']}}/{{$solicitacao['idSolicitacao']}}/{{ $solicitacao['idSolicitacaoInterno'] }}"></td>
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
                            <td class="fontSize-11px">{{$solicitacao['unidadeSolicitada']}}</td>
                            <td style="white-space: pre-wrap !important; word-wrap:break-word !important; font-size: 10px">{{$solicitacao['obs']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="motivo">Motivo e observações</label>
                    <textarea class="form-control" id="motivo" name="motivo" aria-label="motivo"></textarea>
                </div>
            </div>
            <div id="flashRetorno"></div>

            <button type="submit" id="btnSubmitForm" onclick="return confirm('Deseja cancelar a solicitação?');" class="btn btn-primary mt-2"><i class="fas fa-check"></i> Cancelar Solicitações Selecionada(s)</button>

        </form>
        @else
        <h5 class="mt-5">Sem Solicitações Pendentes.</h5>
    @endif

    @if(count($solicitacoesCanceladas) > 0)
        <h5 class="mt-5">Solicitações Canceladas <i class="mdi mdi-arrow-down"></i></h5>
        <div id="divInternosSolicitacao" class="table-responsive mt-3">
            <table class="table mb-3" id="tableInternosSolicitacao">
                <thead class="background_e65251 color_FFF">
                <tr class="">
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Status</th>
                    <th scope="col">Data Solicitação</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Observação</th>
                </tr>
                </thead>
                <tbody id="retornoInternos">
                @foreach($solicitacoesCanceladas as $solicitacoesCancelada)
                    @if(is_null($solicitacoesCancelada['obsCancelamento']))
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
                        <td class="fontSize-11px">{{ strtoupper($solicitacoesCancelada['status'])}}</td>
                        <td class="fontSize-11px">{{$solicitacoesCancelada['dataSolicitacao']}}</td>
                        <td class="fontSize-11px">{{$solicitacoesCancelada['unidadeSolicitada']}}</td>
                        <td style="white-space: pre-wrap !important; word-wrap:break-word !important; font-size: 10px">{{$solicitacoesCancelada['obs']}}</td>
                    </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if(count($solicitacoesCanceladasUnidadeSolicitada) > 0)
        <h5 class="mt-5">Solicitações Canceladas Pela Unidade Solicitada <i class="mdi mdi-arrow-down"></i></h5>
        <div id="divInternosSolicitacao" class="table-responsive mt-3">
            <table class="table mb-3" id="tableInternosSolicitacao">
                <thead class="background_e65251 color_FFF">
                <tr class="">
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Status</th>
                    <th scope="col">Data Solicitação</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Observação</th>
                </tr>
                </thead>
                <tbody id="retornoInterno">
                @foreach($solicitacoesCanceladasUnidadeSolicitada as $solicitacoesCanceladasUnidadeSolic)
                    <tr class="">
                        <td class="fontSize-11px">{{$solicitacoesCanceladasUnidadeSolic['idInterno']}}</td>
                        <td>
                            @if(file_exists(storage_path('app/arquivos/internos/fotos/' . $solicitacoesCanceladasUnidadeSolic['idInterno'] . "/" . FotoService::getFotoPrincipalByIdTipo($solicitacoesCanceladasUnidadeSolic['idInterno'],'interno') )))
                                <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $solicitacoesCanceladasUnidadeSolic['idInterno'] . "/". FotoService::getFotoPrincipalByIdTipo($solicitacoesCanceladasUnidadeSolic['idInterno'],'interno'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                            @else
                                <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                            @endif
                        </td>
                        <td class="fontSize-11px">{{$solicitacoesCanceladasUnidadeSolic['nomeInterno']}}</td>
                        <td class="fontSize-11px">{{ strtoupper($solicitacoesCanceladasUnidadeSolic['status'])}}</td>
                        <td class="fontSize-11px">{{$solicitacoesCanceladasUnidadeSolic['dataSolicitacao']}}</td>
                        <td class="fontSize-11px">{{$solicitacoesCanceladasUnidadeSolic['unidadeSolicitada']}}</td>
                        <td style="white-space: pre-wrap !important; word-wrap:break-word !important; font-size: 10px">{!! $solicitacoesCanceladasUnidadeSolic['obsCancelamento'] !!} </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif


@endsection
@push('css')
<link href="{{ asset('css/solicitacaoInterno/solicitacaoInterno.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/solicitacaoInterno/historicoSolicitacoes.js') }}"></script>
@endpush