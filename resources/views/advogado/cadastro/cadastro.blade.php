@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-plus"></i> Cadastro de Advogados</h3>
        <hr>
        <p>
            Realizar o cadastro de advogados
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

    @if(isset($advogado))
    <form action="{{ route('advogado.update', $advogado->id) }}" id="formCadastro" enctype="multipart/form-data" method="post">
        <input type="hidden" name="idPessoa" id="idPessoa" value="{{$advogado->id}}">
        <input type="hidden" name="tabelaFoto" id="tabelaFoto" value="advogado">
    @else
        <form action="{{ route('advogado.store') }}" method="post" id="formCadastro" enctype="multipart/form-data">
    @endif
        {!! csrf_field() !!}
        <div class="form-row">
            <div class="form-group col-sm-12">
                <label for="interno">Nome do interno</label>
                <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome do interno" autocomplete="off">
                <div class="form-group col-sm-12" id="filter-records"></div>
            </div>
        </div>

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

                @if(isset($advogado))
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
                            <td>{{ $di['sigla'] }}</td>
                            <td class="btn-acoes color_01579b">
                                {{--<a href="#" data-toggle="tooltip" data-placement="top" class="proibir" title="Proibir"><i class="mdi mdi-cancel color_red"></i></a>--}}

                                <input type="hidden" name="internos[]" class="hiddenInternoId" id="internos{{ $di['id'] }}" value="{{ $di['id'] }}"/>
                                <a href="/interno/show/{{ $di['id'] }}" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>
                            </td>
                        </tr>
                    @endforeach
                @endif

                </tbody>
            </table>
        </div>

        <div class="form-row">
            <div class="form-group col-sm-12" id="tagNomeInterno">
            </div>

        </div>

        <div class="form-row">
            <div class="form-group col-sm-6">
                <label for="nome">Nome</label>
                <input type="text" name="nome" value="{{ $advogado->nome or old('nome') }}" class="form-control" id="nome" placeholder="Nome" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="oab_numero">Nº OAB</label>
                <input type="text" name="oab_numero" value="{{ $advogado->oab_numero or old('oab_numero') }}" class="form-control" id="oab_numero" placeholder="Número da OAB" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="oab_uf">OAB UF</label>
                <select id="oab_uf" name="oab_uf" class="form-control" required>
                    <option value="" @if( isset( $advogado ) && $advogado->oab_uf == "" ) selected @endif>
                        Selecione
                    </option>
                    <option value="AC" @if( isset( $advogado ) && $advogado->oab_uf == "AC" ) selected @endif>
                        Acre
                    </option>
                    <option value="AL" @if( isset( $advogado ) && $advogado->oab_uf == "AL" ) selected @endif>
                        Alagoas
                    </option>
                    <option value="AP" @if( isset( $advogado ) && $advogado->oab_uf == "AP" ) selected @endif>
                        Amapá
                    </option>
                    <option value="AM" @if( isset( $advogado ) && $advogado->oab_uf == "AM" ) selected @endif>
                        Amazonas
                    </option>
                    <option value="BA" @if( isset( $advogado ) && $advogado->oab_uf == "BA" ) selected @endif>
                        Bahia
                    </option>
                    <option value="CE" @if( isset( $advogado ) && $advogado->oab_uf == "CE" ) selected @endif>
                        Ceará
                    </option>
                    <option value="DF" @if( isset( $advogado ) && $advogado->oab_uf == "DF" ) selected @endif>
                        Distrito Federal
                    </option>
                    <option value="ES" @if( isset( $advogado ) && $advogado->oab_uf == "ES" ) selected @endif>
                        Espírito Santo
                    </option>
                    <option value="GO" @if( isset( $advogado ) && $advogado->oab_uf == "GO" ) selected @endif>
                        Goiás
                    </option>
                    <option value="MA" @if( isset( $advogado ) && $advogado->oab_uf == "MA" ) selected @endif>
                        Maranhão
                    </option>
                    <option value="MT" @if( isset( $advogado ) && $advogado->oab_uf == "MT" ) selected @endif>
                        Mato Grosso
                    </option>
                    <option value="MS" @if( isset( $advogado ) && $advogado->oab_uf == "MS" ) selected @endif>
                        Mato Grosso do Sul
                    </option>
                    <option value="MG" @if( isset( $advogado ) && $advogado->oab_uf == "MG" ) selected @endif>
                        Minas Gerais
                    </option>
                    <option value="PA" @if( isset( $advogado ) && $advogado->oab_uf == "PA" ) selected @endif>
                        Pará
                    </option>
                    <option value="PB" @if( isset( $advogado ) && $advogado->oab_uf == "PB" ) selected @endif>
                        Paraíba
                    </option>
                    <option value="PR" @if( isset( $advogado ) && $advogado->oab_uf == "PR" ) selected @endif>
                        Paraná
                    </option>
                    <option value="PE" @if( isset( $advogado ) && $advogado->oab_uf == "PE" ) selected @endif>
                        Pernambuco
                    </option>
                    <option value="PI" @if( isset( $advogado ) && $advogado->oab_uf == "PI" ) selected @endif>
                        Piauí
                    </option>
                    <option value="RJ" @if( isset( $advogado ) && $advogado->oab_uf == "RJ" ) selected @endif>
                        Rio de Janeiro
                    </option>
                    <option value="RS" @if( isset( $advogado ) && $advogado->oab_uf == "RS" ) selected @endif>
                        Rio Grande do Sul
                    </option>
                    <option value="RN" @if( isset( $advogado ) && $advogado->oab_uf == "RN" ) selected @endif>
                        Rio Grande do Norte
                    </option>
                    <option value="RO" @if( isset( $advogado ) && $advogado->oab_uf == "RO" ) selected @endif>
                        Rondônia
                    </option>
                    <option value="RR" @if( isset( $advogado ) && $advogado->oab_uf == "RR" ) selected @endif>
                        Roraima
                    </option>
                    <option value="SC" @if( isset( $advogado ) && $advogado->oab_uf == "SC" ) selected @endif>
                        Santa Catarina
                    </option>
                    <option value="SP" @if( isset( $advogado ) && $advogado->oab_uf == "SP" ) selected @endif>
                        São Paulo
                    </option>
                    <option value="SE" @if( isset( $advogado ) && $advogado->oab_uf == "SE" ) selected @endif>
                        Sergipe
                    </option>
                    <option value="TO" @if( isset( $advogado ) && $advogado->oab_uf == "TO" ) selected @endif>
                        Tocantins
                    </option>
                </select>
            </div>

        </div>

        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="sexo">Sexo</label>
                <select id="sexo" name="sexo" class="form-control" required>
                    <option value="" @if( isset( $advogado ) && $advogado->sexo == "" ) selected @endif>
                        Selecione
                    </option>
                    <option value="M" @if( isset( $advogado ) && $advogado->sexo == "M" ) selected @endif>
                        Masculino
                    </option>
                    <option value="F" @if( isset( $advogado ) && $advogado->sexo == "F" ) selected @endif>
                        Feminino
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="cpf">CPF</label>
                <input type="text" name="cpf" value="{{ $advogado->cpf or old('cpf') }}" class="form-control" id="cpf" placeholder="Número do CPF" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="rg_numero">RG</label>
                <input type="text" name="rg_numero" value="{{ $advogado->rg_numero or old('rg_numero') }}" class="form-control" id="rg_numero" placeholder="Número do RG">
            </div>
            <div class="form-group col-sm-3">
                <label for="rg_orgaoexpedidor">Órgão Emissor</label>
                <input type="text" name="rg_orgaoexpedidor" value="{{ $advogado->rg_orgaoexpedidor or old('rg_orgaoexpedidor') }}" class="form-control" id="rg_orgaoexpedidor" placeholder="Órgão Emissor do RG">
            </div>
            <div class="form-group col-sm-3">
                <label for="rg_data">Data da Emissão</label>
                <input type="text" name="rg_data" value="{{ $advogado->rg_data or old('rg_data') }}" class="form-control" id="rg_data" placeholder="Data de Emissão do RG">
            </div>

            <div class="form-group col-sm-6">
                <label for="email">Email</label>
                <input type="email" name="email" value="{{ $advogado->email or old('email') }}" class="form-control" id="email" placeholder="Informe o email" required>
            </div>

        </div>
        <div class="form-row" id="grupoContato">
            <div id="divTelefoneContato"></div>
            @if(isset($telefone))
                @if(count($telefone) != 0)
                    @foreach($telefone as $fone)
                        <div class="form-group col-sm-3" data-content="{{ $fone->id }}">
                            <label for="telefone">Telefone</label>
                            <input type="text" name="telefone[]" value="{{ $fone->numero or old('telefone') }}" class="form-control" id="telefone" placeholder="Informe o telefone">
                            @if($loop->iteration == 1)
                                <i class="fas fa-plus-square cursorPointer btAddFone" id="addFone"></i>
                                <i class="fas fa-minus-circle cursorPointer mr-5 remove_fieldTelefone"></i>
                            @else
                                <i class="fas fa-minus-circle cursorPointer remove_fieldTelefone"></i>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="form-group col-sm-3">
                        <label for="telefone">Telefone</label>
                        <input type="text" name="telefone[]" class="form-control" id="telefone" placeholder="Informe o telefone">
                        <i class="fas fa-plus-square cursorPointer btAddFone" id="addFone"></i>
                    </div>

                @endif
            @else
                <div class="form-group col-sm-3">
                    <label for="telefone">Telefone</label>
                    <input type="text" name="telefone[]" class="form-control" id="telefone" placeholder="Informe o telefone">
                    <i class="fas fa-plus-square cursorPointer btAddFone" id="addFone"></i>
                </div>
            @endif
        </div>


        <div class="form-row">
            <label for="obs">Observações</label>
            <textarea name="obs" id="obs" class="form-control">@if( isset( $advogado ) ){{ $advogado->obs  }} @endif </textarea>
        </div>

        @if(isset($advogado))
            <hr>
            <h5 class="mt-4">Galeria de Fotos</h5>
            @if(!empty($fotos))
                @foreach($fotos as $foto)

                    <div id="card{{$foto->id}}" class="card float-left mr-2" style="width: 18rem;">
                        <img class="card-img-top" src="{{asset('storage/app/arquivos/advogados/fotos/' . $advogado->id . "/$foto->nome_arquivo")}}" height="250" alt="foto do Interno{{$advogado->nome}}">

                        <div class="card-body">
                            <h6 id="h6{{$foto->id}}">{{strtoupper($foto->tipo)}}</h6>
                            <p id="p{{$foto->id}}" class="card-text" style="height: 3.5em;">{{ $foto->legenda }}</p>

                            <div id="div{{$foto->id}}" class="formAlterarFoto displayNone">
                                <div>
                                    <label for="tipoFoto">Tipo da Foto</label>
                                    <select id="tipoFoto{{$foto->id}}" name="tipoFotoRegistrada[]" class="form-control">
                                        <option value="">
                                            Selecione
                                        </option>
                                        <option value="principal" @if( isset( $advogado ) && $foto->tipo == "principal") selected @endif>
                                            Principal
                                        </option>
                                        <option value="tatuagem" @if( isset( $advogado ) && $foto->tipo == "Tatuagem") selected @endif>
                                            tatuagem
                                        </option>
                                        <option value="perfil" @if( isset( $advogado ) && $foto->tipo == "perfil") selected @endif>
                                            Perfil
                                        </option>
                                        <option value="cicatriz" @if( isset( $advogado ) && $foto->tipo == "cicatriz") selected @endif>
                                            Cicatriz
                                        </option>
                                        <option value="sinais" @if( isset( $advogado ) && $foto->tipo == "sinais") selected @endif>
                                            Sinais
                                        </option>
                                        <option value="outros" @if( isset( $advogado ) && $foto->tipo == "outros") selected @endif>
                                            Outros
                                        </option>
                                    </select>
                                </div>
                                <div class="mt-2">
                                    <textarea name="" id="textArea{{$foto->id}}" style="width: 14.5em;" cols="26" rows="10">{{$foto->legenda}}</textarea>
                                </div>
                                <button type="button" class="btn btn-icons btn-outline-danger fecharFoto" data-content="{{$foto->id}}"><i class="mdi mdi-close"></i></button>
                                <button type="button" class="btn btn-icons btn-success gravarFoto" id="btGravarFoto{{$foto->id}}" data-content="{{$foto->id}}"><i class="fas fa-save"></i></button>
                                <button type="button" class="btn btn-icons btn-danger excluirFoto" id="btExcluirFoto{{$foto->id}}" data-content="{{$foto->id}}"><i class="mdi mdi-delete"></i></button>
                            </div>

                            <button type="button" class="btn btn-icons btn-outline-warning btAlterarFoto" id="btAlterarFoto{{$foto->id}}" data-content="{{$foto->id}}"><i class="mdi mdi-lead-pencil"></i></button>

                        </div>
                    </div>
                @endforeach
            @endif
        @endif

        <div class="cf"></div>
        <h5 class="mt-4">Fotos</h5>
        <div class="form-row" id="grupoEndereco">

            <div class="form-group col-sm-3">
                <label for="fotos">Fotos</label>
                <input type="file" name="inputFile[]" class="form-control" id="inputFile" accept="image/*">
            </div>

            <div class="form-group col-sm-3">
                <label for="tipoFoto">Tipo da Foto</label>
                <select id="tipoFoto" name="tipoFoto[]" class="form-control tipoFoto">
                    <option value="">
                        Selecione
                    </option>
                    <option value="principal">
                        Principal
                    </option>
                    <option value="Tatuagem">
                        tatuagem
                    </option>
                    <option value="perfil">
                        Perfil
                    </option>
                    <option value="cicatriz">
                        Cicatriz
                    </option>
                    <option value="sinais">
                        Sinais
                    </option>
                    <option value="outros">
                        Outros
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-6">
                <label for="legenda">Legenda</label>
                <input type="text" name="legenda[]" class="form-control" id="legenda" placeholder="Legenda para foto">
                <i class="fas fa-plus-square cursorPointer btAddFoto" id="addFoto"></i>
            </div>

        </div>

        <div class="row mt-3 displayNone">
            <div class="form-group col-sm-3">
                <button type="button" class="btn btn-dark mt-4 btn-circle" id="btnAbrirWebcam"><i class="mdi mdi-webcam"></i>Abrir Webcam</button>
            </div>
        </div>

        <button type="button" class="btn btn-secondary mb-3 displayNone" id="btnSavePhoto"><i class="mdi mdi-camera-enhance"></i>Capturar Foto</button>

        <div class="row displayNone" id="divWebcam">
            <div class="col-sm-3">
                <div id="camera">

                </div>
            </div>
            <div class="col-sm-9">
                <div id="imgOut">

                </div>
            </div>
        </div>

            <hr>
            <h5 class="mt-4">Documentos</h5>
            @if(isset($advogado))

                @if(empty(json_decode($documentos, true)))
                    <p> -- Sem documentos --<br> </p>
                @else
                    <div class="table-responsive-md">
                        <table class="table mt-2 mb-5">
                            <thead class="background_30516f color_FFF">
                            <tr class="">
                                <th scope="col">#</th>
                                <th scope="col">Nome</th>
                                <th scope="col">Data</th>
                                <th scope="col">Download</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($documentos  as $documento)
                                <tr class="">
                                    <td scope="row" class="fontSize-10px"><i><strong>{{ $loop->iteration  }}</strong></i></td>
                                    <td>{{ $documento->descricao }}</td>
                                    <td>{{ $documento->created_at }}</td>
                                    <td class="btn-acoes">
                                        <a href="{{ route('advogado.fileDoc',['id' => $documento->fk_id, 'filename' => $documento->nome_arquivo])}}" data-toggle="tooltip" data-placement="top" title="Arquivos"><i class="mdi mdi mdi-cloud-download"></i></a>

                                        <a href="{{ route('advogado.documentoDelete',['id' => $documento->fk_id, 'filename' => $documento->nome_arquivo])}}" data-toggle="tooltip" data-placement="top" title="Apagar Arquivo" onclick="return confirm('Deseja realmente apagar o arquivo?');"><i class="mdi mdi-delete color_red"></i></a>

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            @endif

            <div class="cf"></div>
            <div class="form-row" id="grupoDoc">

                <div class="form-group col-sm-3">
                    <label for="doc">Documento</label>
                    <input type="file" name="inputFileDoc[]" class="form-control" id="inputFileDoc" accept="application/pdf">
                </div>

                <div class="form-group col-sm-3">
                    <label for="nomeDoc">Nome</label>
                    <input type="text" name="nomeDoc[]" class="form-control" id="nomeDoc" placeholder="Informe um nome ou descrição">
                    <i class="fas fa-plus-square cursorPointer btAddDoc" id="addDoc"></i>
                </div>

            </div>

            <div id="flashRetorno"></div>
            <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Salvar Advogado</button>


    </form>

@endsection
@push('css')
@if(isset($advogado))
<link href="{{ asset('css/advogado/cadastro/alterar.css') }}" rel="stylesheet">
@else
<link href="{{ asset('css/advogado/cadastro/cadastro.css') }}" rel="stylesheet">
@endif
@endpush

@push('script')
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
@if(isset($advogado))
    <script src="{{ asset('js/advogado/alterar.js') }}"></script>
    <script src="{{ asset('js/outros/fotoMultiploAlterar.js') }}"></script>
@else
    <script src="{{ asset('js/advogado/cadastro.js') }}"></script>
    <script src="{{ asset('js/outros/fotoMultiploAdd.js') }}"></script>
@endif
<script src="{{ asset('vendor/userCam/jquery.userCam.min.js') }}"></script>
@endpush