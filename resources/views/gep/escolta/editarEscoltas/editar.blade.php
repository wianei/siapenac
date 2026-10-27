@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-plus"></i> Editar Escolta</h3>
        <hr>
        <p>
            Editar escolta.
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

    <form action="{{ route('gep.editarStore', $id) }}" id="formEditar" method="post" enctype="multipart/form-data">
        {!! csrf_field() !!}

        {{-- lista de internos pendentes de escolta --}}
        <h5 class="titleInternosCela"> Internos aguardando escolta <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableInternosCela" class="table-responsive">
            <table class="table mb-3" id="recebimento">
                <thead class="background_e65251 color_FFF">
                <tr class="">
                    <th scope="col"><input type="checkbox" id="btnCheckTodosInternosRecebimento"></th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Destino</th>
                    <th scope="col">Periculosidade</th>
                    <th scope="col">Liderança</th>
                    <th scope="col">Facção</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Data Escolta</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody class="retintrec" id="retornoInternosRecebimento">
                @foreach($internosPendentes as $interno)
                    <tr class="internopendente" id="{{$interno['id']}}">
                        <td scope="row" class="checkboxTD"><input type="checkbox" name="internosCelaCheck[]" class="checkbox" id="check_{{$interno['id']}}" value="{{$interno['id']}}/{{$interno['fk_escolta']}}"></td>
                        <td>
                            @if(file_exists(storage_path('app/arquivos/internos/fotos/' . $interno['id'] . "/" . FotoService::getFotoPrincipalByIdTipo($interno['id'], 'interno') )))
                                <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $interno['id'] . "/". FotoService::getFotoPrincipalByIdTipo($interno['id'], 'interno'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                            @else
                                <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                            @endif
                        </td>
                        <td style="font-size: 12px !important;">{{$interno['nome']}}</td>
                        <td style="white-space: pre-wrap !important; word-wrap:break-word !important; font-size: 12px">{{$interno['destino']}}</td>
                        <td style="font-size: 11px !important;">{{$interno['periculosidade']}}</td>
                        <td style="font-size: 11px !important;">{{$interno['lideranca']}}</td>
                        <td style="font-size: 11px !important;">{{$interno['faccao_nome']}}</td>
                        <td style="white-space: pre-wrap !important; word-wrap:break-word !important; font-size: 12px">{{$interno['localizacao']}}</td>
                        <td style="font-size: 11px !important;">{{$interno['hora_solicitado']}}</td>
                        <td class="btn-acoes color_01579b">
                            <a href="{{ route('interno.show',$interno['id']) }}" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>
                            <a href="#" data-toggle="tooltip" data-placement="top" title="Remover" class="botaoCancelar displayNone"><i class="mdi mdi-close-circle-outline"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <button type="button" id="btnAddTodosInternos" class="btn btn-success mb-3"><i class="fa fa-plus"></i> Adicionar Internos na Escolta</button>
        </div>
        </span>

    <span id="spanInternosCelaRecebimento">
            <h5 class="titleInterno">Internos incluidos nesta escolta <i class="mdi mdi-arrow-bottom-right"></i></h5>
            <div id="tableInternos" class="table-responsive">
                <table class="table mb-3">
                    <thead class="background_30516f color_FFF">
                    <tr class="">
                        <th scope="col">Foto</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Destino</th>
                        <th scope="col">Periculosidade</th>
                        <th scope="col">Liderança</th>
                        <th scope="col">Facção</th>
                        <th scope="col">Localização</th>
                        <th scope="col">Solicitado em</th>
                        <th scope="col">Ações</th>
                    </tr>
                    </thead>
                    <tbody id="retornoInterno">

                    @foreach($internosEscolta as $internoEsc)
                        <tr class="internopendente background_bddaff" id="{{$internoEsc['id']}}">
                            <td scope="row" class="checkboxTD displayNone"><input type="checkbox" name="internosCelaCheck[]" class="checkbox" id="check_{{$internoEsc['id']}}" value="{{$internoEsc['id']}}/{{$internoEsc['fk_escolta']}}"></td>
                            <td>
                                @if(file_exists(storage_path('app/arquivos/internos/fotos/' . $internoEsc['id'] . "/" . FotoService::getFotoPrincipalByIdTipo($internoEsc['id'], 'interno') )))
                                    <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $internoEsc['id'] . "/". FotoService::getFotoPrincipalByIdTipo($internoEsc['id'], 'interno'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                                @else
                                    <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                                @endif
                            </td>
                            <td style="font-size: 12px !important;">{{$internoEsc['nome']}}</td>
                            <td style="white-space: pre-wrap !important; word-wrap:break-word !important; font-size: 12px">{{$internoEsc['destino']}}</td>
                            <td style="font-size: 11px !important;">{{$internoEsc['periculosidade']}}</td>
                            <td style="font-size: 11px !important;">{{$internoEsc['lideranca']}}</td>
                            <td style="font-size: 11px !important;">{{$internoEsc['facNome']}}</td>
                            <td style="white-space: pre-wrap !important; word-wrap:break-word !important; font-size: 12px">{{$internoEsc['sigla']}}</td>
                            <td style="font-size: 11px !important;">{{$internoEsc['hora_solicitado']}}</td>
                            <td class="btn-acoes color_01579b">
                                <a href="/interno/show/{{$internoEsc['id']}}" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>
                                {{--<input type="hidden" name="internosEscoltaHidden[]" value="{{$internoEsc['id']}}" id="internosEscoltaHidden">--}}
                                <a href="#/" data-toggle="tooltip" data-placement="top" title="Remover Interno da Escolta" data-content="{{$internoEsc['id']}}" class="removerInternoEscolta"><i class="mdi mdi-close-circle-outline"></i></a>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
    </span>
        {{-- lista de internos pendentes de escolta --}}

        <div class="form-row">

            <div class="form-group col-sm-3" id="divPlacaVTR">
                <label for="vtr_placa">Placa VTR</label>
                <input type="text" name="vtr_placa" class="form-control" id="vtr_placa" value="{{ $dados[0]->vtr_placa }}" placeholder="Placa da VTR" autocomplete="off">
            </div>

        </div>

        <div class="form-row" id="divRowAgentes">

            <div class="form-group col-sm-12">
                <label for="agente">Nome do Agente</label>
                <input type="text" name="agente" class="form-control" id="agente" placeholder="Informe o nome do agente ou matrícula" autocomplete="off">
                <div class="form-group col-sm-12" id="divRetornoAgentes"></div>
            </div>

        </div>

        <h5 class="titleAgente">Agentes <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableAgentes" class="table-responsive">
            <table class="table mb-3">
                <thead class="background_1c2331 color_FFF">
                <tr class="">
                    <th scope="col">Matrícula</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Lotação</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoAgente">
                @foreach($agentes as $agente)
                    <tr class="">
                        <td scope="row">{{ $agente['matricula'] }}</td>
                        <td>
                            <img id="04234205346img" src="" class="imagemSrc cursorPointer"
                                 title="Clique para ampliar a foto" alt="Foto do Servidor">
                        </td>
                        <td>{{ $agente['nome'] }}</td>
                        <td>{{ $agente['lotacao'] }}</td>
                        <td class="btn-acoes color_01579b">
                            <a href="#/" data-toggle="tooltip" data-placement="top"
                               class="removerAgentesCadastrados" data-content="{{ $agente['matricula'] }}" title="Remover desta Escolta"><i
                                        class="mdi mdi-close-circle-outline"></i></a>
                            <a href="#/" data-toggle="tooltip"
                               data-placement="top" data-name="{{ $agente['nome'] }}" data-content="{{ $agente['matricula'] }}" class="comandanteEscolta" title="Comandante da Escolta"><i class="mdi mdi-star-circle"></i></a>
                            <a href="#/" data-toggle="tooltip"
                               data-placement="top" data-name="{{ $agente['nome'] }}" data-content="{{ $agente['matricula'] }}" class="motoristaEscolta" title="Motorista da Escolta"><i class="mdi mdi-steering"></i></a>
                            <input type="hidden" name="agentesHidden[]" class="agentesHidden" id="agentesHidden" value="{{ $agente['matricula'] }}">
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="data_saida">Data Saída</label>
                <input type="text" name="data_saida" class="form-control" id="data_saida" value="{{ $dados[0]->data_saida }}" placeholder="Data de Saída" autocomplete="off">
            </div>

            <div class="form-group col-sm-3" id="divHoraSaida">
                <label for="hora_saida">Hora Saída</label>
                <input type="text" name="hora_saida" class="form-control" id="hora_saida" value="{{ $dados[0]->hora_saida }}" placeholder="Hora da Saída" autocomplete="off">
            </div>

            <div class="form-group col-sm-3" id="divDataRetorno">
                <label for="data_retorno">Data Retorno</label>
                <input type="text" name="data_retorno" class="form-control" id="data_retorno" value="{{ $dados[0]->data_retorno }}" placeholder="Data da Chegada" autocomplete="off">
            </div>

            <div class="form-group col-sm-3" id="divHoraRetorno">
                <label for="hora_retorno">Hora do Retorno</label>
                <input type="text" name="hora_retorno" class="form-control" id="hora_retorno" value="{{ $dados[0]->hora_retorno }}" placeholder="Hora do Retorno" autocomplete="off">
            </div>


            <div class="form-group col-sm-3" id="divComandanteVTR">
                <label for="vtr_comandante">Comandante VTR</label>
                <input type="text" name="vtr_comandante" class="form-control"  value="{{ $comandanteVtr }}"id="vtr_comandante" value="" disabled>
            </div>
            <input type="hidden" name="vtrComandanteHidden" id="vtrComandanteHidden" value="{{ $dados[0]->vtr_comandante }}">
            <input type="hidden" name="vtrMotoristaHidden" id="vtrMotoristaHidden" value="{{ $dados[0]->vtr_motorista }}">
            <div class="form-group col-sm-3" id="divMotoristaVTR">
                <label for="vtr_motorista">Motorista VTR</label>
                <input type="text" name="vtr_motorista" class="form-control" value="{{ $motoristaVtr }}" id="vtr_motorista" value="" disabled>
            </div>

        </div>

        <div class="form-row mb-4">
            <label for="obs">Motivo da Alteração da Escolta</label>
            <textarea name="obs" id="obs" class="form-control" required></textarea>
        </div>

        <div id="flashRetorno"></div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Alterar Dados</button>

    </form>

@endsection

@push('css')
<link href="{{ asset('css/gep/escolta/editarEscolta.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/gep/escolta/editarEscolta.js') }}"></script>
<script src="{{ asset('js/outros/buscaInterno.js') }}"></script>
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
@endpush

