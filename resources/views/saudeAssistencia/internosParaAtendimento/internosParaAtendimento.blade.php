@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fa fa-plus"></i> Registrar Internos para Atendimento</h3>
        <hr>
        <p>
            Registra os internos que necessitam de atendimento médico, odontológico ou da assistente social
        </p>
    </div>

    <form action="{{ route('saudeAssistencia.internosAtendimentoStore') }}" method="post">
        {{ csrf_field() }}

        <div class="form-row">
            <div class="form-group col-sm-12">
                <label for="interno">Nome do interno</label>
                <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome do interno" autocomplete="off">
                <div class="form-group col-sm-12" id="filter-records"></div>
            </div>
        </div>

        <button type="submit" id="btnSave" class="btn btn-primary mb-2"><i class="fas fa-save"></i> Salvar</button>

        <div id="tableInternos" class="table-responsive-md">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Tipo de Atendimento</th>
                    <th scope="col">Prioridade</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoInterno">
                @if(isset($lista))
                    @foreach($lista as $list)
                        <tr class="">
                            <td>{{ $list->id }}</td>
                            <td>

                                @if(file_exists(storage_path('app/arquivos/internos/fotos/' . $list->id . "/" . FotoService::getFotoPrincipalByIdTipo($list->id, 'interno') )))
                                    <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $list->id . "/". FotoService::getFotoPrincipalByIdTipo($list->id, 'interno'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                                @else
                                    <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                                @endif

                            </td>
                            <td>{{ $list->nome }}</td>
                            <td>
                                <select id="tipoAtendimento" name="tipoAtendimento[]" class="form-control parentesco" disabled>
                                    <option value="" @if( isset( $lista ) && $list->tipo_atendimento == "") selected @endif>Selecione</option>
                                    <option value="Médico" @if( isset( $lista ) && $list->tipo_atendimento == "Médico") selected @endif>Médico</option>
                                    <option value="Odontológico" @if( isset( $lista ) && $list->tipo_atendimento == "Odontológico") selected @endif>Odontológico</option>
                                    <option value="Assistente Social" @if( isset( $lista ) && $list->tipo_atendimento == "Assistente Social") selected @endif>Assistente Social</option>
                                    <option value="Psicológico" @if( isset( $lista ) && $list->tipo_atendimento == "Psicológico") selected @endif>Psicológico</option>
                                </select>
                            </td>
                            <td>
                                <select id="prioridade" name="prioridade[]" class="form-control parentesco" disabled>'+
                                    <option value="" @if( isset( $lista ) && $list->prioridade == "") selected @endif>Selecione</option>'+
                                    <option value="A" @if( isset( $lista ) && $list->prioridade == "A") selected @endif>Alta</option>'+
                                    <option value="B" @if( isset( $lista ) && $list->prioridade == "B") selected @endif>Média</option>'+
                                    <option value="C" @if( isset( $lista ) && $list->prioridade == "C") selected @endif>Baixa</option>'+
                                </select>
                            </td>
                            <td>{{ $list->sigla }}</td>
                            <td class="btn-acoes">
                                {{--<input type="hidden" name="internos[]" class="hiddenInternoId" id="internos" value="{{ $list->fk_interno }}"/>--}}
                                {{--<input type="hidden" name="descricao[]" class="hiddenDescricao" id="descricao" value="{{ $list->descricao }}"/>--}}
                                <a href="#" data-toggle="tooltip" data-content="{{ $list->fk_interno }}/{{ $list->tipo_atendimento }}" class="removerInternosCadastrados" data-placement="top" title="Excluir da Lista" onclick="return confirm('Deseja realmente remover este interno da lista?');"><i class="mdi mdi mdi-delete color_red"></i></a>

                            </td>
                        </tr>
                        <tr><td colspan="7" style="border-bottom: 1px solid #CCCCCC">Descrição: {{ $list->descricao }}</td></tr>

                    @endforeach
                @endif
                </tbody>
            </table>
        </div>

    </form>

@endsection


@push('css')
<link href="{{ asset('css/equipe/registrarEquipe.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/saudeAssistencia/internosParaAtendimento.js') }}"></script>
@endpush