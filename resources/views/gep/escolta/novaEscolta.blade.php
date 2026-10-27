@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-plus"></i> Cadastro de Escoltas</h3>
        <hr>
        <p>
            Defina abaixo quais internos serão escoltados em qual viatura, quais agentes participarão e qual o equipamento será utilizado. Apenas será possível realizar esse registro se houver alguma solicitação de escolta em aberto.
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

<form action="{{ route('gep.storeNovaEscolta') }}" id="formNovaEscolta" method="post" enctype="multipart/form-data" class="mb-4 mt-5">
    {!! csrf_field() !!}

@if($internosPendentes != [])
<span id="spanInternosCela">

<a href="{{ route('gep.internosPendentesEscoltaPdf') }}" class="btn btn-primary float-right mb-1"><i class="mdi mdi-file-pdf"></i>Gerar PDF da Lista</a>

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
                    @if($interno['foto'] != null && $interno['foto'] != "Não Informado")
                        <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $interno['id']. "/".$interno['foto'])}}" data-content="{{$interno['fototipo']}},{{$interno['fotolegenda']}},{{$interno['fotodatainserida']}},{{$interno['fotodataalterada']}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto">
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

                </tbody>
            </table>
        </div>
</span>

    <div class="form-row">

        <div class="form-group col-sm-3" id="divPlacaVTR">
            <label for="vtr_placa">Placa VTR</label>
            <input type="text" name="vtr_placa" class="form-control" id="vtr_placa" placeholder="Placa da VTR" autocomplete="off">
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

                </tbody>
            </table>
        </div>

        {{-------------------------------------------- início equipamentos --------------------------------------------------------}}

        <div class="divEquipamentoUtilizado">
            <h6>Equipamento Utilizado</h6>
            <div class="form-row">
                <div class="form-group col-sm-3" id="divTipoArmamento">
                    <label for="tipoEquipamento">Tipo de Equipamento</label>
                    <select id="tipoEquipamento" name="tipoEquipamento" class="form-control">
                        <option value="">
                            Selecione
                        </option>
                        <option value="armaFogo">
                            Arma de Fogo
                        </option>
                        <option value="municao">
                            Munição
                        </option>
                        <option value="carregador">
                            Carregador
                        </option>
                        <option value="algema">
                            Algema
                        </option>
                        <option value="granada">
                            Granada
                        </option>
                        <option value="epi">
                            EPI
                        </option>
                        <option value="colete">
                            Colete
                        </option>
                        <option value="espargidor">
                            Espargidor
                        </option>
                    </select>
                </div>

                <div class="form-group col-sm-3">
                    <label for="tipoArmamento">Tipo Armamento</label>
                    <select id="tipo" name="tipo" class="form-control">
                        <option value="">
                            Selecione
                        </option>
                        <option value="Longa">
                            Longa
                        </option>
                        <option value="Curta">
                            Curta
                        </option>

                    </select>
                </div>

            </div>
        </div>

        <div id="tableEquipamentos" class="displayNone">
            <h5 class="titleEquipamento">Equipamentos <i class="mdi mdi-arrow-bottom-right"></i></h5>
            <div id="equipamentosAgentes" class="table-responsive">
                <table class="table mb-3">
                    <thead class="background_3E4551 color_FFF">
                    <tr class="">
                        <th scope="col">#</th>
                        <th scope="col">Equipamento</th>
                    </tr>
                    </thead>
                    <tbody id="retornoEquipamentos">

                    </tbody>
                </table>
                <button type="button" id="btnAddEquipamentos" class="btn btn-success mt-3 mb-3"><i class="mdi mdi-plus"></i> Adicionar</button>
            </div>

        </div>

        <div id="tableEquipamentosCautela" class="displayNone">

            <h5 class="titleEquipamentoCautela">Equipamentos para Escolta<i class="mdi mdi-arrow-bottom-right"></i></h5>
            <div id="equipamentosEscolta" class="table-responsive">
                <table class="table mb-3">
                    <thead class="background_287ec3 color_FFF">
                    <tr class="">
                        <th scope="col">#</th>
                        <th scope="col">Equipamentos da Escolta</th>
                        <th scope="col">Ações</th>
                    </tr>
                    </thead>
                    <tbody id="retornoEquipamentosEscolta">

                    </tbody>
                </table>
            </div>

        </div>

        {{------------------------------------------- fim equipamentos --------------------------------------------------------}}

        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="data_saida">Data Saída</label>
                <input type="text" name="data_saida" class="form-control" id="data_saida" placeholder="Data de Saída" autocomplete="off">
            </div>

            <div class="form-group col-sm-3" id="divHoraSaida">
                <label for="hora_saida">Hora Saída</label>
                <input type="text" name="hora_saida" class="form-control" id="hora_saida" placeholder="Hora da Saída" autocomplete="off">
            </div>

            <div class="form-group col-sm-3" id="divDataRetorno">
                <label for="data_retorno">Data Retorno</label>
                <input type="text" name="data_retorno" class="form-control" id="data_retorno" placeholder="Data da Chegada" autocomplete="off">
            </div>

            <div class="form-group col-sm-3" id="divHoraRetorno">
                <label for="hora_retorno">Hora do Retorno</label>
                <input type="text" name="hora_retorno" class="form-control" id="hora_retorno" placeholder="Hora do Retorno" autocomplete="off">
            </div>


            <div class="form-group col-sm-3" id="divComandanteVTR">
                <label for="vtr_comandante">Comandante VTR</label>
                <input type="text" name="vtr_comandante" class="form-control" id="vtr_comandante" value="" disabled>
            </div>
            <input type="hidden" name="vtrComandanteHidden" id="vtrComandanteHidden" value="">
            <input type="hidden" name="vtrMotoristaHidden" id="vtrMotoristaHidden" value="">
            <div class="form-group col-sm-3" id="divMotoristaVTR">
                <label for="vtr_motorista">Motorista VTR</label>
                <input type="text" name="vtr_motorista" class="form-control" id="vtr_motorista" value="" disabled>
            </div>

        </div>
        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="fotos">Arquivos</label>
                <input type="file" name="fotos" class="form-control" id="fotos" accept="application/pdf" multiple>
            </div>
        </div>

        <div id="motivacaoEscoltaSemInterno" class="form-row mb-4 displayNone">
            <label for="obs">Justificativa da escolta sem interno(s)</label>
            <textarea name="obs" id="obs" class="form-control"></textarea>
        </div>

        <div id="flashRetorno"></div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Salvar Escolta</button>

</form>
@else
    Não há escoltas solicitadas sem resposta para esta unidade, no momento.
@endif

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
<link href="{{ asset('css/gep/escolta/novaEscolta.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/gep/escolta/novaEscolta.js') }}"></script>
@endpush