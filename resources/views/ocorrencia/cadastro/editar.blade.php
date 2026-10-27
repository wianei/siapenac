@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-file-document"></i> Edição de Ocorrência</h3>
        <hr>
        <p>
            Edição da ocorrência selecionada.
        </p>
    </div>
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))
                <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg) !!}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

<form action="{{ route('ocorrencia.editarStore', ['id'=>$ocorrencia[0]->id, 'returnLivro' => $returnLivro]) }}" method="post" enctype="multipart/form-data" class="mt-5" id="formAddOcorrencia">

    {!! csrf_field() !!}

    <div class="form-row">

        <div class="form-group col-sm-3">
            <label for="dt_ocorrencia">Data da Ocorrência</label>
            <input type="text" name="dt_ocorrencia" value="{{ $ocorrencia[0]->data }}" class="form-control" id="dt_ocorrencia" placeholder="Informe a data da ocorrência" autocomplete="off" required>
        </div>

        <div class="form-group col-sm-3">
            <label for="hr_ocorrencia">Horário da Ocorrência</label>
            <input type="text" name="hr_ocorrencia" value="{{ $ocorrencia[0]->horario }}" class="form-control" id="hr_ocorrencia" placeholder="Informe o horário da ocorrência" autocomplete="off" required>
        </div>

        <div class="form-group col-sm-6">
            <label for="natureza">Natureza</label>
            <select id="natureza" name="natureza" class="form-control" required>
                <option value="">
                    Selecione
                </option>
                @foreach($naturezaOcorrencia as $natureza)
                    <option value="{{ $natureza->id }}" @if($ocorrencia[0]->natureza == $natureza->id) selected @endif>
                        {{ $natureza->natureza }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <input type="hidden" name="idTipoObjeto" id="idTipoObjeto" value="{{ $ocorrencia[0]->natureza_objeto }}">
        
        <div class="form-group col-sm-3 @if($ocorrencia[0]->natureza != 1 && $ocorrencia[0]->natureza != 47) displayNone @endif" id="divTipoObjeto">
            <label for="tipo_objeto">Tipo do Objeto/Substância Apreendida</label>
            <select id="tipo_objeto" name="tipo_objeto" class="form-control">
            </select>
        </div>

    </div>

    <div class="form-row" id="divRowAgentes">
        <div class="form-group col-sm-12">
            <label for="agente">Servidor(es) Envolvido(s)</label>
            <input type="text" name="agente" class="form-control" id="agente" placeholder="Informe o nome do agente ou matrícula" autocomplete="off">
            <div class="form-group col-sm-12" id="divRetornoAgentes"></div>
        </div>

    </div>

    <h5 class="titleAgente">Servidor <i class="mdi mdi-arrow-bottom-right"></i></h5>
    <div id="tableAgentes" class="table-responsive-md">
        <table class="table mb-3">
            <thead class="background_1c2331 color_FFF">
            <tr class="">
                <th scope="col">Matrícula</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">Localização</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody id="retornoAgente">
            @foreach($dadosAgentes as $dA)
                <tr class="">
                    <td scope="row">{{ $dA['matricula'] }}</td>
                    <td><img src="{{ $dA['foto'] }}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Servidor"/></td>
                    <td>{{ $dA['nome'] }}</td>
                    <td>{{ utf8_encode($dA['lotacao']) }}</td>
                    <td class="btn-acoes color_01579b">
                        <a href="#/" data-toggle="tooltip" data-placement="top" data-content="{{ $dA['matricula'] }}" class="removerAgentesCadastrados" title="Remover desta Escolta"><i class="mdi mdi-close-circle-outline"></i></a>
                        <input type="hidden" name="agentesHidden[]" id="agentesHidden{{ $dA['matricula'] }}" value="{{ $dA['matricula'] }}"/>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>

    <div class="form-row">
        <div class="form-group col-sm-12">
            <label for="interno">Interno(s) Envolvido(s)</label>
            <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome do interno" autocomplete="off">
            <div class="form-group col-sm-12" id="filter-records"></div>

        </div>
    </div>

    <h5 class="titleInterno">Internos <i class="mdi mdi-arrow-bottom-right"></i></h5>
    <div id="tableInternos" class="table-responsive-md">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">Prontuário</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">Facção</th>
                <th scope="col">Liderança</th>
                <th scope="col">Localização</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody id="retornoInterno">
            @foreach($dadosInterno as $di)
                <tr class="">
                    <td scope="row">{{ $di['id'] }}</td>
                    <td>
                        @if($di['foto'] != null)
                            <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $di['id']. "/".$di['foto'])}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                        @else
                            <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                        @endif

                    </td>
                    <td>{{ $di['nome'] }}</td>
                    <td>{{ $di['faccao'] }}</td>
                    <td>{{ $di['lideranca'] }}</td>
                    <td>'{{ $di['sigla'] }}</td>
                    <td class="btn-acoes color_01579b">
                        <a href="#" data-toggle="tooltip" data-placement="top" data-content="{{ $di['id'] }}" class="removerInternoCadastrado" title="Remover da lista"><i class="mdi mdi-close-circle-outline"></i></a>

                        <input type="hidden" name="internos[]" class="hiddenInternoId" id="internos{{ $di['id'] }}" value="{{ $di['id'] }}"/>
                        <a href="/interno/show/{{ $di['id'] }}" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>

    <div class="form-row">
        <div class="form-group col-sm-12">
            <label for="visita">Visita(s) Envolvida(s)</label>
            <input type="text" name="visita" class="form-control" id="visita" placeholder="Informe o nome da visita" autocomplete="off">
            <div class="form-group col-sm-12" id="filter-records-visita"></div>
        </div>
    </div>
    <h5 class="titleVisita">Visita(s) <i class="mdi mdi-arrow-bottom-right"></i></h5>
    <div id="tableVisitas" class="table-responsive-md">
        <table class="table mb-3">
            <thead class="background_81c784 color_FFF">
            <tr class="">
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">CPF</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody id="retornoVisita">

            @foreach($dadosVisitas as $dV)
                <tr class="" id="trVisita{{ $dV['id'] }}">
                    <td scope="row"><img src="{{ asset($dV['foto']) }}"> </td>
                    <td>{{ $dV['nome'] }}</td>
                    <td>{{ $dV['cpf'] }}</td>
                    <td class="btn-acoes color_01579b">
                        <a href="#/" data-toggle="tooltip" data-placement="top" data-content="{{ $dV['id'] }}" class="removerVisitasCadastradas" title="Remover desta Lista"><i class="mdi mdi-close-circle-outline"></i></a>
                        <input type="hidden" name="visitas[]" id="visitas{{ $dV['id'] }}" value="{{ $dV['id'] }}"/>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>

    <div class="form-row">
        <div class="form-group col-sm-12">
            <label for="advogado">Advogado(s) Envolvido(s)</label>
            <input type="text" name="advogado" class="form-control" id="advogado" placeholder="Informe o nome do advogado" autocomplete="off">
            <div class="form-group col-sm-12" id="filter-records-advogado"></div>
        </div>
    </div>
    <h5 class="titleAdvogado">Advogado(s) <i class="mdi mdi-arrow-bottom-right"></i></h5>
    <div id="tableAdvogados" class="table-responsive-md">
        <table class="table mb-3">
            <thead class="background_e65251 color_FFF">
            <tr class="">
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">Nº OAB</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody id="retornoAdvogado">

            @foreach($dadosAdvogados as $dAdv)
                <tr class="" id="trAdv{{ $dAdv['id'] }}">
                    <td scope="row"><img src="{{ asset($dAdv['foto']) }}"> </td>
                    <td>{{ $dAdv['nome'] }}</td>
                    <td>{{ $dAdv['oab'] }}</td>
                    <td class="btn-acoes color_01579b">
                        <a href="#/" data-toggle="tooltip" data-placement="top" data-content="{{ $dAdv['id'] }}" class="removerAdvogadosCadastrados" title="Remover desta Lista"><i class="mdi mdi-close-circle-outline"></i></a>
                        <input type="hidden" name="advogados[]" id="advogados{{ $dAdv['id'] }}" value="{{ $dAdv['id'] }}"/>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>


    <div class="form-row">
        <label for="descricao">Descrição</label>
    </div>

    <textarea name="descricao" id="descricao" class="form-control" required>{{ $ocorrencia[0]->descricao }}</textarea>

    <div class="form-row mt-3">
        <div class="form-group col-sm-3">
            <label for="inputFile">Arquivos</label>
            <input type="file" name="inputFile[]" class="form-control" id="inputFile" accept="application/pdf" multiple>
        </div>
    </div>

    <div class="table-responsive-md" id="divArquivosOcorrencia">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Nome</th>
                <th scope="col">Data</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody id="arquivosOcorrencia">
            @foreach($dadosArquivos  as $dado)
                <tr class="">
                    <td scope="row" class="fontSize-10px"><i><strong>{{ $loop->iteration  }}</strong></i></td>
                    <td>{{ $dado->nome_arquivo }}</td>
                    <td>{{ $dado->data }}</td>
                    <td class="btn-acoes">
                        <a href="{{ route('ocorrencia.deletarArquivo',['id' => $dado->id])}}" data-toggle="tooltip" data-placement="top" title="Deletar Arquivo" onclick="return confirm('Deseja realmente apagar este arquivo?');"><i class="mdi mdi mdi-delete color_red"></i></a>

                        <a href="{{ route('ocorrencia.arquivoVisualizar',['id'=>$ocorrencia[0]->id,'filename'=>$dado->nome_arquivo]) }}" data-toggle="tooltip" data-placement="top" title="Ver Arquivo"><i class="mdi mdi-cloud-download"></i></a>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <br>

    <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Salvar Ocorrência</button>


</form>

@endsection
@push('css')
<link href="{{ asset('css/ocorrencia/cadastro/editar.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/ocorrencia/editar.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
@endpush