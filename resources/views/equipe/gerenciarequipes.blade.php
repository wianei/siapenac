@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fa fa-plus"></i> Registrar Equipe</h3>
        <hr>
        <p>
            Registre quem está de serviço na data do livro de ocorrência em aberto.<br/><br/>
        </p>
    </div>

    @php

    $siglaUpLogado = \App\Http\Controllers\Facade\FacadeController::getLotacaoServidor(\Illuminate\Support\Facades\Auth::user()->cpf, "LOTAÇÃO NO RH");

    @endphp

    <form action="{{ route('equipe.gerenciarequipesStore', ['returnLivro' => $returnLivro]) }}" method="post">
        {{ csrf_field() }}
        <input type="hidden" name="lotacaoAgente" id="lotacaoAgente" value="{{ $lotacaoServidor }}">
        <div class="form-row" id="divRowAgentes">

            <div class="form-group col">
                <label for="agente">Nome do Agente</label>
                <input type="text" name="agente" class="form-control" id="agente" placeholder="Informe o nome do agente ou matrícula" autocomplete="off">
                <div class="form-group col-sm-12" id="divRetornoAgentes"></div>

            </div>

        </div>

        <h5 class="titleAgente">Agentes <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableAgentes" class="table-responsive-md">
            <table class="table mb-3">
                <thead class="background_1c2331 color_FFF">
                <tr class="">
                    <th scope="col">Matrícula</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Lot. Oficial</th>
                    <th scope="col">Status</th>
                    <th scope="col">Lot. Unidade</th>
                    <th scope="col">Escala</th>
                    <th scope="col">Função</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoAgente">
                @if(isset($dadosAgentes))
                    @foreach($dadosAgentes as $dA)
                        <tr class="">
                            <td scope="row">{{ $dA['matricula'] }}</td>
                            <td>{{ $dA['nome'] }}</td>
                            <td>{{ utf8_encode($dA['lotacao']) }}</td>
                            <td>
                                <select id="status" name="status[]" class="form-control parentesco" required>
                                    <option value="" @if($dA['status'] == "") selected @endif>Selecione</option>
                                    <option value="Em exercício" @if($dA['status'] == "Em exercício") selected @endif>Em exercício</option>
                                    <option value="Plantão Extra 12h" @if($dA['status'] == "Plantão Extra 12h") selected @endif>Plantão Extra 12h</option>
                                    <option value="Plantão Extra 24h" @if($dA['status'] == "Plantão Extra 24h") selected @endif>Plantão Extra 24h</option>
                                    {{--<option value="D.O. 6h" @if($dA['status'] == "D.O. 6h") selected @endif>D.O. 6h</option>--}}
                                    {{--<option value="D.O. 12h" @if($dA['status'] == "D.O. 12h") selected @endif>D.O. 12h</option>--}}
                                    {{--<option value="D.O. 18h" @if($dA['status'] == "D.O. 18h") selected @endif>D.O. 18h</option>--}}
                                    {{--<option value="D.O. 24h" @if($dA['status'] == "D.O. 24h") selected @endif>D.O. 24h</option>--}}
                                    {{--<option value="D.O. 30h" @if($dA['status'] == "D.O. 30h") selected @endif>D.O. 30h</option>--}}
                                    {{--<option value="D.O. 36h" @if($dA['status'] == "D.O. 36h") selected @endif>D.O. 36h</option>--}}
                                    {{--<option value="D.O. 42h" @if($dA['status'] == "D.O. 42h") selected @endif>D.O. 42h</option>--}}
                                    {{--<option value="D.O. 48h" @if($dA['status'] == "D.O. 48h") selected @endif>D.O. 48h</option>--}}
                                    <option value="Atestado Médico" @if($dA['status'] == "Atestado Médico") selected @endif>Atestado Médico</option>
                                    <option value="Falta" @if($dA['status'] == "Falta") selected @endif>Falta</option>
                                    <option value="Licença" @if($dA['status'] == "Licença") selected @endif>Licença</option>
                                    <option value="Férias" @if($dA['status'] == "Férias") selected @endif>Férias</option>
                                    <option value="Concessão" @if($dA['status'] == "Concessão") selected @endif>Concessão</option>
                                    <option value="Afastamento" @if($dA['status'] == "Afastamento") selected @endif>Afastamento</option>
                                    <option value="Ausente por Outro Motivo" @if($dA['status'] == "Ausente por Outro Motivo") selected @endif>Ausente por Outro Motivo</option>
                                </select>
                            </td>
                            <td>
                                <select id="status" name="lotacaoUnidade[]" class="form-control parentesco" required>
                                    <option value="" @if($dA['lotacaoUnidade'] == "") selected @endif>Selecione</option>
                                    <option value="Administrativo" @if($dA['lotacaoUnidade'] == "Administrativo") selected @endif>Administrativo</option>
                                    <option value="Unidade" @if($dA['lotacaoUnidade'] == "Unidade") selected @endif>Unidade</option>
                                    @foreach($lotacaoUnidade as $lotUn)
                                        <option value="{{ $lotUn->id }}" @if($dA['lotacaoUnidade'] == $lotUn->id) selected @endif>{{ $lotUn->nome }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select id="escala" name="escala[]" class="form-control parentesco" required>
                                    <option value="" @if($dA['escala'] == "") selected @endif>Selecione</option>
                                    <option value="Plantão" @if($dA['escala'] == "Plantão") selected @endif>Plantão</option>
                                    <option value="Expediente" @if($dA['escala'] == "Expediente") selected @endif>Expediente</option>
                                </select>
                            </td>
                            <td>
                                <select id="funcao" name="funcao[]" class="form-control parentesco" required>'+
                                    <option value="" @if($dA['funcao'] == "") selected @endif>Selecione</option>
                                    <option value="Padrão"  @if($dA['funcao'] == "Padrão") selected @endif>Padrão</option>
                                    <option value="Chefe do Administrativo"  @if($dA['funcao'] == "Chefe do Administrativo") selected @endif>Chefe do Administrativo</option>
                                    <option value="Chefe do Cartório"  @if($dA['funcao'] == "Chefe do Cartório") selected @endif>Chefe do Cartório</option>
                                    <option value="Chefe de Arquivos e Prontuários"  @if($dA['funcao'] == "Chefe de Arquivos e Prontuários") selected @endif>Chefe de Arquivos e Prontuários</option>
                                    <option value="Chefe de Expediente"  @if($dA['funcao'] == "Chefe de Expediente") selected @endif>Chefe de Expediente</option>
                                    <option value="Chefe de Visita"  @if($dA['funcao'] == "Chefe de Visita") selected @endif>Chefe de Visita</option>
                                    <option value="Chefe de Assistência aos Internos"  @if($dA['funcao'] == "Chefe de Assistência aos Internos") selected @endif>Chefe de Assistência aos Internos</option>
                                    <option value="Chefe de Equipe"  @if($dA['funcao'] == "Chefe de Equipe") selected @endif>Chefe de Equipe</option>
                                    <option value="Chefe de Disciplina"  @if($dA['funcao'] == "Chefe de Disciplina") selected @endif>Chefe de Disciplina</option>
                                    <option value="Chefe de Segurança"  @if($dA['funcao'] == "Chefe de Segurança") selected @endif>Chefe de Segurança</option>
                                    <option value="Chefe de Plantão"  @if($dA['funcao'] == "Chefe de Plantão") selected @endif>Chefe de Plantão</option>
                                    <option value="Chefe da Armaria"  @if($dA['funcao'] == "Chefe da Armaria") selected @endif>Chefe da Armaria</option>
                                    <option value="Chefe de Manutenção"  @if($dA['funcao'] == "Chefe de Manutenção") selected @endif>Chefe de Manutenção</option>
                                    <option value="Despachante"  @if($dA['funcao'] == "Despachante") selected @endif>Despachante</option>
                                    <option value="Monitoramento de Câmeras"  @if($dA['funcao'] == "Monitoramento de Câmeras") selected @endif>Monitoramento de Câmeras</option>
                                </select>
                            </td>
                            <td class="btn-acoes color_01579b">
                                <input type="hidden" name="agentesHidden[]" id="agentesHidden{{ $dA['matricula'] }}" value="{{ $dA['matricula'] }}"/>

                            @if($dA['permuta'] != "S")
                                    <a href="#/" data-toggle="tooltip" data-placement="top" data-content="{{ $dA['matricula'] }}" class="removerAgentesCadastrados" title="Remover da equipe do dia"><i class="mdi mdi-close-circle-outline"></i></a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>


        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
    </form>

@endsection


@push('css')
<link href="{{ asset('css/equipe/registrarEquipe.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/equipe/registrarEquipe.js') }}"></script>
@endpush