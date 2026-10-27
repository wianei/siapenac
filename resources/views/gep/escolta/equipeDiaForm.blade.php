@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-cogs"></i> Equipes do Dia</h3>
        <hr>
        <p>
            Cadastro de equipes
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
    @foreach($equipeDados as $dado)
    <form action="{{ route('gep.equipeDiaEditarStore', $dado['id']) }}" id="formCadastrarEquipe" method="post">

        {!! csrf_field() !!}
        <div class="form-row">

            <input type="hidden" name="dataEquipe" id="dataEquipe" value="{{ $dado['data'] }}">

            <div class="form-group col-sm-3">
                <label for="regiao">Região</label>
                <input type="text" name="regiao" value="{{ $dado['regiao'] or old('regiao') }}" class="form-control" id="regiao" placeholder="Ex.: zona sul" autocomplete="off">
            </div>

            <div class="form-group col-sm-3">
                <label for="vtr">Placa VTR</label>
                <input type="text" name="vtr" value="{{ $dado['placa_vtr'] or old('placa_vtr') }}" class="form-control" id="vtr" placeholder="Placa da VTR" autocomplete="off">
            </div>

            <div class="form-group col-sm-3">
                <label for="equipeNome">Nome Equipe</label>
                <input type="text" name="equipeNome" value="{{ $dado['nome_equipe'] or old('nome_equipe') }}" class="form-control" id="equipeNome" placeholder="Nome da equipe, ex.: FTPEN 01" autocomplete="off">
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
                    <th scope="col">Status</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoAgente">
                @foreach($dado['agentes'] as $agente)
                    <td scope="row">{{ $agente['matricula'] }}</td>
                    <td><img id="img" src="{{ $agente['foto'] }}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Servidor"/></td>
                    <td>{{ $agente['nome'] }}</td>
                    <td>{{ $agente['lotacao'] }}</td>
                    <td scope="row"><select id="status" name="status[]" class="form-control parentesco" required>
                            <option value="Em exercício" @if($agente['status'] == "Em exercício") selected @endif>Em exercício</option>
                            <option value="D.O" @if($agente['status'] == "D.O") selected @endif>D.O</option>
                            </select></td>
                    <td class="btn-acoes color_01579b">

                        <a href="#/" data-toggle="tooltip" data-placement="top" data-content="{{ $agente['matricula'] }}" class="removerAgentesCadastrados" title="Remover desta Equipe"><i class="mdi mdi-close-circle-outline"></i></a>

                        <a href="#/" data-toggle="tooltip" data-placement="top" data-name="{{ $agente['nome'] }}" data-content="{{ $agente['matricula'] }}" class="comandanteEscolta" title="Comandante da Escolta"><i class="mdi mdi-star-circle"></i></a>

                        <a href="#/" data-toggle="tooltip" data-placement="top"  data-name="{{ $agente['nome'] }}" data-content="{{ $agente['matricula'] }}"  class="motoristaEscolta" title="Motorista da Escolta"><i class="mdi mdi-steering"></i></a>

                        <input type="hidden" name="agentesHidden[]" class="agentesHidden" id="agentesHidden" value="{{ $agente['matricula'] }}"/>

                        </td>
                    </tr>

                @endforeach
                </tbody>
            </table>
        </div>

        <div class="form-row">

            <div class="form-group col-sm-3" id="divComandanteVTR">
                <label for="vtr_comandante">Comandante VTR</label>
                <input type="text" name="vtr_comandante"  class="form-control" id="vtr_comandante" value="{{ $dado['vtr_comandante'] or old('vtr_comandante') }}" disabled>
            </div>

            <input type="hidden" name="vtrComandanteHidden" id="vtrComandanteHidden" value="{{ $dado['vtr_comandante_matricula'] or old('vtrComandanteHidden') }}">

            <input type="hidden" name="vtrMotoristaHidden" id="vtrMotoristaHidden" value="{{ $dado['vtr_motorista_matricula'] or old('vtrMotoristaHidden') }}">

            <div class="form-group col-sm-3" id="divMotoristaVTR">
                <label for="vtr_motorista">Motorista VTR</label>
                <input type="text" name="vtr_motorista" class="form-control" id="vtr_motorista" value="{{ $dado['vtr_motorista'] or old('vtr_motorista') }}" disabled>
            </div>

        </div>

        <div class="form-row mb-4">
            <label for="obs">Observações</label>
            <textarea name="obs" id="obs" class="form-control">{{ $dado['obs']}}</textarea>
        </div>

        <div id="flashRetorno"></div>

        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Salvar Equipe</button>

    </form>
    @endforeach



@endsection

@push('css')
<link href="{{ asset('css/gep/escolta/pautaDoDiaEditar.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/gep/escolta/equipesDia.js') }}"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>

@endpush