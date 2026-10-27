@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-file-plus"></i> Cadastro de Sindicâncias</h3>
        <hr>
        <p>
            Cadastrar sindicâncias.
        </p>
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    @if(isset($dados))
            <form action="{{ route('sindicancia.editarStore',$dados[0]->id) }}" method="post" enctype="multipart/form-data">
        @else
            <form action="{{ route('sindicancia.store') }}" id="formCadastroSindicancia" method="post" enctype="multipart/form-data">
    @endif

            {!! csrf_field() !!}

        <div class="form-row">

            <div class="form-group col-sm-12">
                <label for="interno">Nome do interno</label>
                <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome do interno" autocomplete="off">
                <div class="form-group col-sm-12" id="filter-records"></div>

            </div>

            <div class="form-group col-sm-3">
                <label for="unidade_prisional">Presídio</label>
                <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}" @if( isset( $interno ) && $unidade->id == $interno->unidade_prisional ) selected @endif>
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3" id="divPavilhao">
                <label for="pavilhao">Pavilhão</label>
                <select id="pavilhao" name="pavilhao" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divAla">
                <label for="ala">Ala</label>
                <select id="ala" name="ala" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divCela">
                <label for="cela">Cela</label>
                <select id="cela" name="cela" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

        </div>

        <span id="spanInternosCela" class="displayNone">
        <h5 class="titleInternosCela"><span id="txtCela"></span> <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableInternosCela" class="table-responsive-md">
            <table class="table mb-3">
                <thead class="btn-danger color_FFF">
                <tr class="">
                    <th scope="col"><input type="checkbox" id="btnCheckTodosInternos"></th>
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Facção</th>
                    <th scope="col">Liderança</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoInternosCela">

                </tbody>
            </table>
            <button type="button" id="btnAddTodosInternos" class="btn btn-success mt-3 mb-3"><i class="mdi mdi-account-plus"></i> Adicionar</button>
        </div>

        </span>


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
                @if(isset($dados))
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
                @endif

                </tbody>
            </table>
        </div>

        <div class="form-row" id="divRowAgentes">

            <div class="form-group col-sm-12">
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
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoAgente">
                @if(isset($dadosAgentes))
                    @foreach($dadosAgentes as $dA)
                        <tr class="">
                            <td scope="row">{{ $dA['matricula'] }}</td>
                            <td><img src="{{ $dA['foto'] }}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Servidor"/></td>
                            <td>{{ $dA['nome'] }}</td>
                            <td>{{ utf8_encode($dA['lotacao']) }}</td>
                            <td class="btn-acoes color_01579b">
                                <a href="#/" data-toggle="tooltip" data-placement="top" data-content="{{ $dA['matricula'] }}" class="removerAgentesCadastrados" title="Remover da lista"><i class="mdi mdi-close-circle-outline"></i></a>
                                <input type="hidden" name="agentesHidden[]" id="agentesHidden{{ $dA['matricula'] }}" value="{{ $dA['matricula'] }}"/>
                                </td>
                            </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>

        <div class="form-row">
            <div class="form-group col-sm-6">
                <label for="titulo">Título</label>
                <input type="text" name="titulo" value="{{ $dados[0]->titulo or old('titulo') }}" class="form-control" id="titulo" placeholder="Informe o Título" required>
            </div>

            <div class="form-group col-sm-2">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control" required>
                    <option value="" @if( isset( $dados ) && $dados[0]->status == "" ) selected @endif>
                        Selecione
                    </option>
                    <option value="Aberta" @if( isset( $dados ) && $dados[0]->status == "Aberta" ) selected @endif>
                        Aberta
                    </option>

                    <option value="Protocolada" @if( isset( $dados ) && $dados[0]->status == "Protocolada" ) selected @endif>
                        Protocolada
                    </option>

                    <option value="Homologada" @if( isset( $dados ) && $dados[0]->status == "Homologada" ) selected @endif>
                        Homologada
                    </option>

                </select>
            </div>

            <div class="form-group col-sm-2">
                <label for="gravidade_falta">Gravidade da Falta</label>
                <select id="gravidade_falta" name="gravidade_falta" class="form-control" required>
                    <option value=""  @if( isset( $dados ) && $dados[0]->gravidade_falta == "" ) selected @endif>
                        Selecione
                    </option>
                    <option value="Leve" @if( isset( $dados ) && $dados[0]->gravidade_falta == "Leve" ) selected @endif>
                        Leve
                    </option>

                    <option value="Média" @if( isset( $dados ) && $dados[0]->gravidade_falta == "Média" ) selected @endif>
                        Média
                    </option>

                    <option value="Grave" @if( isset( $dados ) && $dados[0]->gravidade_falta == "Grave" ) selected @endif>
                        Grave
                    </option>

                </select>
            </div>


            <div class="form-group col-sm-2">
                <label for="data_incidente">Data do Incidente</label>
                <input type="text" name="data_incidente" value="{{ $dados[0]->data or old('data') }}" class="form-control" id="data_incidente" placeholder="Informe o Título" autocomplete="off" required>
            </div>

        </div>
        <div class="form-row">
            <div class="form-group col-sm-12">
                <label for="descricao">Descrição</label>
            </div>
        </div>

        <textarea name="descricao" id="descricao" class="form-control" required>
            @if((isset($dados)))
                {{ $dados[0]->descricao }}
            @endif
        </textarea>

        <div class="form-row mt-3">
            <div class="form-group col-sm-3">
                <label for="inputFile">Arquivos</label>
                <input type="file" name="inputFile[]" class="form-control" id="inputFile" accept="application/pdf" multiple>
            </div>
        </div>
            @if(isset($dadosArquivos))
                    <div class="table-responsive-md">
                        <table class="table mt-5">
                            <thead class="background_30516f color_FFF">
                            <tr class="">
                                <th scope="col">#</th>
                                <th scope="col">Nome</th>
                                <th scope="col">Data</th>
                                <th scope="col">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($dadosArquivos  as $dado)
                                <tr class="">
                                    <td scope="row" class="fontSize-10px"><i><strong>{{ $loop->iteration  }}</strong></i></td>
                                    <td>{{ $dado->nome_arquivo }}</td>
                                    <td>{{ $dado->data }}</td>
                                    <td class="btn-acoes">
                                        <a href="{{ route('sindicancia.arquivoDelete',['id' => $dado->id])}}" data-toggle="tooltip" data-placement="top" title="Deletar Arquivo" onclick="return confirm('Deseja realmente apagar o arquivo?');"><i class="mdi mdi mdi-delete color_red"></i></a>

                                        <a href="{{ route('sindicancia.arquivoVisualizar',['id'=>$dado->fk_sindicancia, 'fileName'=>$dado->nome_arquivo]) }}" data-toggle="tooltip" data-placement="top" title="Ver Arquivo"><i class="mdi mdi-cloud-download"></i></a>

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <br>
                @endif
                <div id="flashRetorno"></div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Gravar Sindicância</button>

    </form>

@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@if(isset($dados))
    <link href="{{ asset('css/sindicancia/cadastro/editar.css') }}" rel="stylesheet">
@else
    <link href="{{ asset('css/sindicancia/cadastro/cadastro.css') }}" rel="stylesheet">
@endif
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@if(isset($dados))
    <script src="{{ asset('js/sindicancia/editar.js') }}"></script>
@else
    <script src="{{ asset('js/sindicancia/cadastro.js') }}"></script>
    <script src="{{ asset('js/outros/buscaServidor.js') }}"></script>
@endif
{{--<script src="{{ asset('js/vendor/buscaInterno.js') }}"></script>--}}
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
@endpush

