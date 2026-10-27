@extends('template.tema_geral.theme')

@section('content')
    <style type="text/css">
        .tbodyVis tr:nth-child(even) {background: #FFF !important;}
        .tbodyVis tr:nth-child(odd) {background: #f2f8f9 !important;}
    </style>

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-search"></i> Buscar Visitante</h3>
        <hr>
        <p>
            Buscar visitante na base de dados.
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

    <form action="{{ route('visita.buscar') }}" method="get">

        <div class="cf"></div>
        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="nome">Nome</label>
                <input type="text" name="nome" class="form-control" id="nome" placeholder="Nome">
            </div>

            <div class="form-group col-sm-3">
                <label for="cpf">CPF</label>
                <input type="text" name="cpf" class="form-control" id="cpf" placeholder="CPF">
            </div>

            <div class="form-group col-sm-3">
                <label class="form-check-label">Sexo</label><br>
                <div class="ml-1 fontSize-13px">
                    Masculino   <input type="checkbox" name="sexo[]" id="sexo" value="M" class="mr-3">
                    Feminino    <input type="checkbox" name="sexo[]" id="sexo" value="F">
                </div>

            </div>

            <div class="form-group col-sm-3">
                <label for="status">Status</label>
                <div class="ml-1 fontSize-13px">
                    Ativo       <input type="checkbox" name="status[]" id="status" value="A" class="mr-3">
                    Inativo     <input type="checkbox" name="status[]" id="status" value="I" >
                </div>

            </div>


        </div>


        <div class="form-row">
            @if(PermissaoService::getTipoPermissao() == "I")
                <div class="form-group col-sm-6">
                    <label for="endereco_logradouro">Logradouro</label>
                    <input type="text" name="endereco_logradouro" class="form-control" id="endereco_logradouro" placeholder="Rua, av..">
                </div>

                <div class="form-group col-sm-3">
                    <label for="endereco_bairro">Bairro</label>
                    <input type="text" name="endereco_bairro" class="form-control" id="endereco_bairro"  placeholder="Bairro">
                </div>
            @endif

            <div class="form-group col-sm-3">
                <label for="endereco_estado">Estado</label>
                <select id="endereco_estado" name="endereco_estado" class="form-control selectEstados">
                    <option value="">
                        Selecione
                    </option>
                    <option value="AC">
                        Acre
                    </option>
                    <option value="AL">
                        Alagoas
                    </option>
                    <option value="AP">
                        Amapá
                    </option>
                    <option value="AM">
                        Amazonas
                    </option>
                    <option value="BA">
                        Bahia
                    </option>
                    <option value="CE">
                        Ceará
                    </option>
                    <option value="DF">
                        Distrito Federal
                    </option>
                    <option value="ES">
                        Espírito Santo
                    </option>
                    <option value="GO">
                        Goiás
                    </option>
                    <option value="MA">
                        Maranhão
                    </option>
                    <option value="MT">
                        Mato Grosso
                    </option>
                    <option value="MS">
                        Mato Grosso do Sul
                    </option>
                    <option value="MG">
                        Minas Gerais
                    </option>
                    <option value="PA">
                        Pará
                    </option>
                    <option value="PB">
                        Paraíba
                    </option>
                    <option value="PR">
                        Paraná
                    </option>
                    <option value="PE">
                        Pernambuco
                    </option>
                    <option value="PI">
                        Piauí
                    </option>
                    <option value="RJ">
                        Rio de Janeiro
                    </option>
                    <option value="RN">
                        Rio Grande do Norte
                    </option>
                    <option value="RS">
                        Rio Grande do Sul
                    </option>
                    <option value="RO">
                        Rondônia
                    </option>
                    <option value="RR">
                        Roraima
                    </option>
                    <option value="SC">
                        Santa Catarina
                    </option>
                    <option value="SP">
                        São Paulo
                    </option>
                    <option value="SE">
                        Sergipe
                    </option>
                    <option value="TO">
                        Tocantins
                    </option>
                </select>
            </div>

            @if(PermissaoService::getTipoPermissao() == "I")
                <div class="form-group col-sm-3">
                    <label for="endereco_cidade">Cidade</label>
                    <select id="endereco_cidade" name="endereco_cidade" class="form-control">
                        <option value="">
                            Selecione
                        </option>
                    </select>
                </div>
            @endif


        </div>
        <hr>

        RELACIONADO COM INTERNO

        <div class="form-row"  id="divBuscaAvancada" style="margin-top: 20px">

            <div class="form-group col-sm-6">
                <label for="nome_interno">Nome do Interno</label>
                <input type="text" name="nome_interno" class="form-control" id="nome_interno" placeholder="Nome">
            </div>


            <div class="form-group col-sm-3">
                <label for="unidade_prisional">Presídio</label>
                <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">
                    <option value="">
                        Todos
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}" @if( isset( $interno ) && $unidade->id == $interno->unidade_prisional ) selected @endif>
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3 displayNone" id="divPavilhao">
                <label for="pavilhao">Pavilhão</label>
                <select id="pavilhao" name="pavilhao" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>
            {{--@endif--}}

            {{--@if(\Illuminate\Support\Facades\Auth::user()->tipo >= 300)--}}
            <div class="form-group col-sm-3 displayNone" id="divAla">
                <label for="ala">Ala</label>
                <select id="ala" name="ala" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>
            {{--@endif--}}

            {{--@if(\Illuminate\Support\Facades\Auth::user()->tipo >= 300)--}}
            <div class="form-group col-sm-3 displayNone" id="divCela">
                <label for="cela">Cela</label>
                <select id="cela" name="cela" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>
        </div>


        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar Visitante</button>

    </form>
    @if($dados != false)


        <div id="links" class="mt-5">
            {{ $dados->links() }}
        </div>

        @if($dados->total() > 0)
            <h6 class="mt-2"><i>Sua busca retornou
                    {{ $dados->total() }} resultados
                </i>
            </h6>

        @else

            <h5 class="mt-5"><i>Sua busca não obteve resultados! </i>
            </h5>

        @endif


        <div class="table-responsive">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">Registro</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                {{--<th scope="col">Endereço</th>--}}
                <th scope="col">CPF</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody class="tbodyVis">
                @foreach($dados as $dado)
                    <tr @if($dado->status == "I") class="background_ffd7d7" @endif>
                        <td>{{ $dado->id }}</td>
                        <td>
                            @if(file_exists(storage_path('app/arquivos/visitas/fotos/' . $dado->id . "/" . FotoService::getFotoPrincipalByIdTipo($dado->id,'visita'))))
                                <img style="text-align: center" src="{{asset('storage/app/arquivos/visitas/fotos/' . $dado->id . "/". FotoService::getFotoPrincipalByIdTipo($dado->id,'visita'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                            @else
                                <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                            @endif
                        </td>
                        <td scope="row">{{ $dado->nome }}</td>
                        {{--<td>--}}
                            {{--@if(!empty($dado->logradouro))--}}
                            {{--{{ $dado->logradouro.", ".$dado->numero.", ".$dado->bairro.", ".$dado->cidade."-".$dado->estado }}--}}
                                {{--@else--}}
                                    {{--Sem endereço--}}
                                {{--@endif--}}
                        {{--</td>--}}
                        <td>{{ $dado->cpf }}</td>
                        <td class="btn-acoes color_01579b">

                            <a href="{{ route('visita.show',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>

                            @if(PermissaoService::getVerifyPermissionView("VisitaController","editar"))
                            <a href="{{ route('visita.editar',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>
                            @endif

                            @if(PermissaoService::getVerifyPermissionView("ControleAcessoController","suspenderEntrada"))
                                @if($dado->controleAcessosSuspenso == null)
                                    <a href="{{ route('controleAcesso.suspenderEntrada', $dado->cpf) }}" data-toggle="tooltip" data-placement="top" title="Proibir Entrada"><i class="mdi mdi-cancel color_red"></i></a>
                                @else
                                    <a href="{{ route('controleAcesso.liberarEntrada', $dado->cpf) }}" data-toggle="tooltip" data-placement="top" title="Liberar Entrada"><i class="mdi mdi-arrow-up-bold color_green"></i></a>
                                @endif
                            @endif
                            
                            @if(PermissaoService::getVerifyPermissionView("VisitaController","emitirCarteira"))
                                
                            <a href="{{ route('visita.emitirCarteira', $dado->cpf) }}" data-toggle="tooltip" data-placement="top" title="Emitir Carteira"><i class="mdi mdi-account-box"></i></a>

                            @endif

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

        <div id="links" class="mt-5">
            {{ $dados->links() }}
        </div>

    @endif


    {{-- modal para abrir foto --}}
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
    {{-- !! modal para abrir foto --}}

@endsection

@push('script')
<script src="{{ asset('js/visita/buscar.js') }}"></script>
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
@endpush