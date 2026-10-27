@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-plus"></i> Cadastro de Visitantes</h3>
        <hr>
        <p>
            Realizar o cadastro de visitantes
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

    @if(isset($visita))
    <form action="{{ route('visita.update', $visita->id) }}" enctype="multipart/form-data" method="post">
        <input type="hidden" name="idPessoa" id="idPessoa" value="{{$visita->id}}">
        <input type="hidden" name="tabelaFoto" id="tabelaFoto" value="visita">

    @else
    <form action="{{ route('visita.store') }}" id="formCadastroVisita" enctype="multipart/form-data" method="post">
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
                    <th scope="col">Parentesco da Visita</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoInterno">

                @if(isset($visita))
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
                            <td id="{{ $di['id']}}">
                                <select id="parentesco" name="parentesco" class="form-control parentesco" required>
                                    <option value="">Selecione</option>
                                    <option value="AVÔ (Ó)" @if( isset( $visita ) && $di['grau_parentesco'] == "AVÔ (Ó)") selected @endif>AVÔ (Ó) do interno</option>
                                    <option value="BISAVÔ (Ó)" @if( isset( $visita ) && $di['grau_parentesco'] == "BISAVÔ (Ó)") selected @endif>BISAVÔ (Ó) do interno</option>
                                    <option value="COMPANHEIRO (A)" @if( isset( $visita ) && $di['grau_parentesco'] == "COMPANHEIRO (A)") selected @endif>COMPANHEIRO (A) do interno</option>
                                    <option value="CONJUGE" @if( isset( $visita ) && $di['grau_parentesco'] == "CONJUGE") selected @endif>CÔNJUGE do interno</option>
                                    <option value="CUNHADO (A)" @if( isset( $visita ) && $di['grau_parentesco'] == "CUNHADO (A)") selected @endif>CUNHADO (A) do interno</option>
                                    <option value="ENTEADO (A)" @if( isset( $visita ) && $di['grau_parentesco'] == "ENTEADO (A)") selected @endif>ENTEADO (A) do interno</option>
                                    <option value="FILHO (A)" @if( isset( $visita ) && $di['grau_parentesco'] == "FILHO (A)") selected @endif>FILHO (A) do interno</option>
                                    <option value="GENRO" @if( isset( $visita ) && $di['grau_parentesco'] == "GENRO") selected @endif>GENRO do interno</option>
                                    <option value="IRMÃ (O)" @if( isset( $visita ) && $di['grau_parentesco'] == "IRMÃ (O)") selected @endif>IRMÃ (O) do interno</option>
                                    <option value="MADRASTA" @if( isset( $visita ) && $di['grau_parentesco'] == "MADRASTA") selected @endif>MADRASTA do interno</option>
                                    <option value="MÃE" @if( isset( $visita ) && $di['grau_parentesco'] == "MÃE") selected @endif>MÃE do interno</option>
                                    <option value="NETO (A)" @if( isset( $visita ) && $di['grau_parentesco'] == "NETO (A)") selected @endif>NETO (A) do interno</option>
                                    <option value="NORA" @if( isset( $visita ) && $di['grau_parentesco'] == "NORA") selected @endif>NORA do interno</option>
                                    <option value="PADRASTO" @if( isset( $visita ) && $di['grau_parentesco'] == "PADRASTO") selected @endif>PADRASTO do interno</option>
                                    <option value="PAI" @if( isset( $visita ) && $di['grau_parentesco'] == "PAI") selected @endif>PAI do interno</option>
                                    <option value="SOBRINHO (A)" @if( isset( $visita ) && $di['grau_parentesco'] == "SOBRINHO (A)") selected @endif>SOBRINHO (A) do interno</option>
                                    <option value="PRIMO (A)" @if( isset( $visita ) && $di['grau_parentesco'] == "PRIMO (A)") selected @endif>PRIMO (A) do interno</option>
                                    <option value="SOGRO (A)" @if( isset( $visita ) && $di['grau_parentesco'] == "SOGRO (A)") selected @endif>SOGRO (A) do interno</option>
                                    <option value="TIO (A)" @if( isset( $visita ) && $di['grau_parentesco'] == "TIO (A)") selected @endif>TIO (A) do interno</option>
                                    <option value="AMIGO (A)" @if( isset( $visita ) && $di['grau_parentesco'] == "AMIGO (A)") selected @endif>AMIGO (A) do interno</option>
                                    <option value="OUTRO" @if( isset( $visita ) && $di['grau_parentesco'] == "OUTRO") selected @endif>Parentesco não informado</option>
                                    <!-- {{--<option value=" -- Vinculo Inativo -- " @if( isset( $visita ) && $di['grau_parentesco'] == " -- Vinculo Inativo -- ") selected @endif> -- Vinculo Inativo -- </option>--}} -->
                                </select>
                            </td>
                            <td class="btn-acoes color_01579b">

                                {{--<a href="#" data-toggle="tooltip" data-placement="top" class="suspender" title="Suspender"><i class="mdi mdi-pause-circle text-warning"></i></a>--}}

                                @if($di['suspenso'] == true)
                                    <a href="{{ route('visita.permitirVisita', ['idVisita' => $visita->id, 'idInterno' => $di['id']]) }}" data-toggle="tooltip" data-placement="top" class="proibir" title="Permitir"><i class="mdi mdi-check-circle color_green"></i></a>
                                @else
                                    <a href="{{ route('visita.suspenderVisita', ['idVisita' => $visita->id, 'idInterno' => $di['id']]) }}" data-toggle="tooltip" data-placement="top" class="suspender" title="Suspender"><i class="mdi mdi-pause-circle text-warning"></i></a>
                                @endif

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
                    <input type="text" name="nome" value="{{ $visita->nome or old('nome') }}" class="form-control" id="nome" placeholder="Nome" required>
                </div>

                <div class="form-group col-sm-3">
                    <label for="sexo">Sexo</label>
                    <select id="sexo" name="sexo" class="form-control" required>
                        <option value="" @if( isset( $visita ) && $visita->sexo == "" ) selected @endif>
                            Selecione
                        </option>
                        <option value="M" @if( isset( $visita ) && $visita->sexo == "M" ) selected @endif>
                            Masculino
                        </option>
                        <option value="F" @if( isset( $visita ) && $visita->sexo == "F" ) selected @endif>
                            Feminino
                        </option>
                    </select>
                </div>

            <div class="form-group col-sm-3">
                    <label for="data_nascimento">Data de nascimento</label>
                    <input type="text" class="form-control" value="{{ $visita->data_nascimento or old('data_nascimento') }}" name="data_nascimento" id="data_nascimento" placeholder="Data de Nascimento" required>
                </div>

            </div>

        <div class="form-row">
@if(!isset($visita))
            <div class="form-group col-sm-2">
                <label for="tipo">Tipo</label>
                <select id="tipo" name="tipo" class="form-control" required>
                    <option value="" @if( isset( $visita ) && $visita->tipo == "" ) selected @endif>
                        Selecione
                    </option>
                    <option value="A" @if( isset( $visita ) && $visita->tipo == "A" ) selected @endif>
                        Autorizado(a)
                    </option>
                    <option value="S" @if( isset( $visita ) && $visita->tipo == "S" ) selected @endif>
                        Suspenso(a)
                    </option>
                    <option value="P" @if( isset( $visita ) && $visita->tipo == "P" ) selected @endif>
                        Proibido(a)
                    </option>
                </select>
            </div>
@endif
            <div class="form-group col-sm-2">
                <label for="status">Status</label>
                <div class="ml-1 mt-2 fontSize-13px">
                    Ativo       <input type="radio" name="status" id="status" value="A" @if( isset( $visita ) && $visita->status == "A" ) checked @endif @ class="mr-3" @if(!isset($visita))checked="" @endif>
                    Inativo     <input type="radio" name="status" id="status" value="I" @if( isset( $visita ) && $visita->status == "I" ) checked @endif>
                </div>

            </div>

            <div class="form-group col-sm-2">
                <label for="status">Preferencial</label>
                <div class="ml-1 mt-2 fontSize-13px">
                    Sim       <input type="radio" name="preferencial" id="preferencialA" value="S" @if( isset( $visita ) && $visita->preferencial == "S" ) checked @endif class="mr-3">
                    Não       <input type="radio" name="preferencial" id="preferencialI" value="N"  @if( isset( $visita ) && $visita->preferencial == "N" ) checked @endif @if(!isset($visita))checked="" @endif>
                </div>

            </div>
            <div class="form-group col-sm-2">
                <label for="pne">Portador de Necessidades Especiais</label>
                <select id="pne" name="pne" class="form-control" required>
                    <option value="" @if( isset( $visita ) && $visita->pne == "" ) selected @endif>
                        Selecione
                    </option>
                    <option value="S" @if( isset( $visita ) && $visita->pne == "S" ) selected @endif>
                        Sim
                    </option>
                    <option value="N" @if( isset( $visita ) && $visita->pne == "N" || isset( $visita ) && is_null($visita->pne)) selected @endif>
                        Não
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-6">
                <label for="email">Email</label>
                <input type="email" name="email" value="{{ $visita->email or old('email') }}" class="form-control" id="email" placeholder="Informe seu email">
            </div>

        </div>

        <div class="form-row" id="grupoContato">
            <div id="divTelefoneContato"></div>
            @if(isset($telefone))
                @if(count($telefone) != 0)
                    @foreach($telefone as $fone)
                        <div class="form-group col-sm-3" data-content="{{ $fone->id }}">
                            <label for="telefone">Telefone</label>
                            <input type="text" name="telefone[]" value="{{ $fone->numero or old('telefone') }}" class="form-control" id="telefone" placeholder="Informe o telefone" required>
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
                        <input type="text" name="telefone[]" class="form-control" id="telefone" placeholder="Informe o telefone" required>
                        <i class="fas fa-plus-square cursorPointer btAddFone" id="addFone"></i>
                    </div>

                @endif
            @else
                <div class="form-group col-sm-3">
                    <label for="telefone">Telefone</label>
                    <input type="text" name="telefone[]" class="form-control" id="telefone" placeholder="Informe o telefone" required>
                    <i class="fas fa-plus-square cursorPointer btAddFone" id="addFone"></i>
                </div>
            @endif
        </div>


        <div class="form-row">
            <div class="form-group col-sm-6">
                <label for="mae">Mãe</label>
                <input type="text" name="mae" value="{{ $visita->mae or old('mae') }}" class="form-control" id="mae" placeholder="Mãe">
            </div>
            <div class="form-group col-sm-6">
                <label for="pai">Pai</label>
                <input type="text" name="pai" value="{{ $visita->pai or old('pai') }}" class="form-control" id="pai" placeholder="Pai">
            </div>
        
            @if(isset($nomeresponsavel[0]->nome))
            <div class="form-group col-sm-6">
                <label for="visita">Selecione um Responsável para quando o Visitante for menor</label>
                <input type="text" name="visita" class="form-control" id="visita" value="{{$nomeresponsavel[0]->nome}}" placeholder="Informe o nome da visita" autocomplete="off">
                <div class="form-group col-sm-12" id="filter-records-visita"></div>
            </div>
            @else
            <div class="form-group col-sm-6">
                <label for="visita">Selecione um Responsável para quando o Visitante for menor</label>
                <input type="text" name="visita" class="form-control" id="visita" placeholder="Informe o nome da visita" autocomplete="off">
                <div class="form-group col-sm-12" id="filter-records-visita"></div>
            </div>
            @endif
            <h5 class="titleVisita displayNone">Visita(s) <i class="mdi mdi-arrow-bottom-right"></i></h5>
            <div id="tableVisitas" class="table-responsive-md displayNone">
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

                    </tbody>
                </table>
            </div>
            
            <div class="form-group col-sm-3">
                <label for="validade_carteira">Data de Validade da Carteirinha</label>
                <input type="text" name="validade_carteira" value="{{ $visita->validade_carteira or old('validade_carteira') }}" class="form-control" id="validade_carteira" placeholder="Data de Validade">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="rg_numero">RG</label>
                <input type="text" name="rg_numero" value="{{ $visita->rg_numero or old('rg_numero') }}" class="form-control" id="rg_numero" placeholder="Número do RG">
            </div>
            <div class="form-group col-sm-3">
                <label for="rg_orgaoexpedidor">Órgão Emissor</label>
                <input type="text" name="rg_orgaoexpedidor" value="{{ $visita->rg_orgaoexpedidor or old('rg_orgaoexpedidor') }}" class="form-control" id="rg_orgaoexpedidor" placeholder="Órgão Emissor do RG">
            </div>
            <div class="form-group col-sm-3">
                <label for="rg_data">Data da Emissão</label>
                <input type="text" name="rg_data" value="{{ $visita->rg_data or old('rg_data') }}" class="form-control" id="rg_data" placeholder="Data de Emissão do RG">
            </div>
            <div class="form-group col-sm-3">
                <label for="cpf">CPF</label>
                <input type="text" name="cpf" value="{{ $visita->cpf or old('cpf') }}" class="form-control" id="cpf" placeholder="Número do CPF" required>
            </div>

        </div>

        {{-- Endereco --}}
        {{-- Se for uma alteracao --}}
        @if(isset($endereco))
            {{-- adiciona mais enderecos--}}
            <div class="text-right col-sm-12">
                <h6 class="btn btn-outline-primary float-right" id="addEndereco">Adicionar Mais Endereços <i class="mdi mdi-map-marker-plus"></i></h6>
            </div>
            <br>
            <br>
            {{-- os enderecos adicionados sao impressos na div abaixo --}}
            <div id="groupEndereco">
            </div>
            <div id="divEnderecosRemovidos"></div>
            @foreach($endereco as $end)
                <div  data-content="{{ $end->id }}">
                    <div class="form-row">
                        <div class="form-group col-sm-3">
                            <label for="endereco_cep">CEP</label>
                            <input type="text" name="endereco_cep[]" value="{{ $end->cep or old('endereco_cep') }}" class="form-control cep" id="endereco_cep{{ $loop->iteration }}" placeholder="Informe o CEP">
                        </div>

                        <div class="form-group col-sm-6">
                            <label for="endereco_logradouro">Logradouro</label>
                            <input type="text" name="endereco_logradouro[]" value="{{ $end->logradouro or old('logradouro') }}" class="form-control" id="endereco_logradouro{{ $loop->iteration }}"
                                   placeholder="Rua, av..">
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="endereco_numero">Número</label>
                            <input type="text" name="endereco_numero[]" value="{{ $end->numero or old('numero') }}" class="form-control endereco_numero" id="endereco_numero{{ $loop->iteration }}"
                                   placeholder="Número">
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="endereco_bairro">Bairro</label>
                            <input type="text" name="endereco_bairro[]" value="{{ $end->bairro or old('bairro') }}" class="form-control" id="endereco_bairro{{ $loop->iteration }}"
                                   placeholder="Bairro">
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="endereco_complemento">Complemento</label>
                            <input type="text" name="endereco_complemento[]" value="{{ $end->complemento or old('complemento') }}" class="form-control" id="endereco_complemento"
                                   placeholder="Complemento">
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="endereco_cidade">Cidade</label>
                            <input type="text" name="endereco_cidade[]" value="{{ $end->cidade or old('cidade') }}" class="form-control" id="endereco_cidade{{ $loop->iteration }}"
                                   placeholder="Cidade">
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="endereco_estado">Estado</label>
                            <select id="endereco_estado{{ $loop->iteration }}" name="endereco_estado[]" class="form-control">
                                <option value="" @if( isset( $visita ) && $end->estado == "" ) selected @endif>
                                    Selecione
                                </option>
                                <option value="AC" @if( isset( $visita ) && $end->estado == "AC" ) selected @endif>
                                    Acre
                                </option>
                                <option value="AL" @if( isset( $visita ) && $end->estado == "AL" ) selected @endif>
                                    Alagoas
                                </option>
                                <option value="AP" @if( isset( $visita ) && $end->estado == "AP" ) selected @endif>
                                    Amapá
                                </option>
                                <option value="AM" @if( isset( $visita ) && $end->estado == "AM" ) selected @endif>
                                    Amazonas
                                </option>
                                <option value="BA" @if( isset( $visita ) && $end->estado == "BA" ) selected @endif>
                                    Bahia
                                </option>
                                <option value="CE" @if( isset( $visita ) && $end->estado == "CE" ) selected @endif>
                                    Ceará
                                </option>
                                <option value="DF" @if( isset( $visita ) && $end->estado == "DF" ) selected @endif>
                                    Distrito Federal
                                </option>
                                <option value="ES" @if( isset( $visita ) && $end->estado == "ES" ) selected @endif>
                                    Espírito Santo
                                </option>
                                <option value="GO" @if( isset( $visita ) && $end->estado == "GO" ) selected @endif>
                                    Goiás
                                </option>
                                <option value="MA" @if( isset( $visita ) && $end->estado == "MA" ) selected @endif>
                                    Maranhão
                                </option>
                                <option value="MT" @if( isset( $visita ) && $end->estado == "MT" ) selected @endif>
                                    Mato Grosso
                                </option>
                                <option value="MS" @if( isset( $visita ) && $end->estado == "MS" ) selected @endif>
                                    Mato Grosso do Sul
                                </option>
                                <option value="MG" @if( isset( $visita ) && $end->estado == "MG" ) selected @endif>
                                    Minas Gerais
                                </option>
                                <option value="PA" @if( isset( $visita ) && $end->estado == "PA" ) selected @endif>
                                    Pará
                                </option>
                                <option value="PB" @if( isset( $visita ) && $end->estado == "PB" ) selected @endif>
                                    Paraíba
                                </option>
                                <option value="PR" @if( isset( $visita ) && $end->estado == "PR" ) selected @endif>
                                    Paraná
                                </option>
                                <option value="PE" @if( isset( $visita ) && $end->estado == "PE" ) selected @endif>
                                    Pernambuco
                                </option>
                                <option value="PI" @if( isset( $visita ) && $end->estado == "PI" ) selected @endif>
                                    Piauí
                                </option>
                                <option value="RJ" @if( isset( $visita ) && $end->estado == "RJ" ) selected @endif>
                                    Rio de Janeiro
                                </option>
                                <option value="RN" @if( isset( $visita ) && $end->estado == "RN" ) selected @endif>
                                    Rio Grande do Norte
                                </option>
                                <option value="RS" @if( isset( $visita ) && $end->estado == "RS" ) selected @endif>
                                    Rio Grande do Sul
                                </option>
                                <option value="RO" @if( isset( $visita ) && $end->estado == "RO" ) selected @endif>
                                    Rondônia
                                </option>
                                <option value="RR" @if( isset( $visita ) && $end->estado == "RR" ) selected @endif>
                                    Roraima
                                </option>
                                <option value="SC" @if( isset( $visita ) && $end->estado == "SC" ) selected @endif>
                                    Santa Catarina
                                </option>
                                <option value="SP" @if( isset( $visita ) && $end->estado == "SP" ) selected @endif>
                                    São Paulo
                                </option>
                                <option value="SE" @if( isset( $visita ) && $end->estado == "SE" ) selected @endif>
                                    Sergipe
                                </option>
                                <option value="TO" @if( isset( $visita ) && $end->estado == "TO" ) selected @endif>
                                    Tocantins
                                </option>
                            </select>
                        </div>
                    </div>
                    <h6 class="btn btn-outline-danger float-right" id="removeEnderecoEdit">
                        Excluir endereço
                        <i class="mdi mdi-map-marker-minus"></i></h6><br><br>
                </div>

            @endforeach


        @else
            {{-- Se estiver na tela de cadastro --}}
            <div class="form-row">
                <div class="form-group col-sm-3">
                    <label for="endereco_cep">CEP</label>
                    <input type="text" name="endereco_cep[]" class="form-control cep" id="endereco_cep"
                           placeholder="Informe o CEP">
                </div>

                <div class="form-group col-sm-6">
                    <label for="endereco_logradouro">Logradouro</label>
                    <input type="text" name="endereco_logradouro[]" class="form-control" id="endereco_logradouro"
                           placeholder="Rua, av..">
                </div>

                <div class="form-group col-sm-3">
                    <label for="endereco_numero">Número</label>
                    <input type="text" name="endereco_numero[]" class="form-control endereco_numero" id="endereco_numero"
                           placeholder="Número">
                </div>

                <div class="form-group col-sm-3">
                    <label for="endereco_bairro">Bairro</label>
                    <input type="text" name="endereco_bairro[]" class="form-control" id="endereco_bairro"
                           placeholder="Bairro">
                </div>

                <div class="form-group col-sm-3">
                    <label for="endereco_complemento">Complemento</label>
                    <input type="text" name="endereco_complemento[]" class="form-control" id="endereco_complemento"
                           placeholder="Complemento">
                </div>

                <div class="form-group col-sm-3">
                    <label for="endereco_cidade">Cidade</label>
                    <input type="text" name="endereco_cidade[]" class="form-control" id="endereco_cidade"
                           placeholder="Cidade">
                </div>

                <div class="form-group col-sm-3">
                    <label for="endereco_estado">Estado</label>
                    <select id="endereco_estado" name="endereco_estado[]" class="form-control">
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
                <div class="text-right col-sm-12">
                    <h6 class="btn btn-outline-primary float-right" id="addEndereco">Adicionar Mais Endereços <i class="mdi mdi-map-marker-plus"></i></h6>
                </div>

            </div>
            <div id="groupEndereco"></div>

        @endif

        {{-- End Endereco --}}

        @if(isset($visita))
            <hr>
            <h5 class="mt-4">Galeria de Fotos</h5>

            @if(!empty($fotos))
                @foreach($fotos as $foto)

                    <div id="card{{$foto->id}}" class="card float-left mr-2" style="width: 18rem;">
                        <img class="card-img-top" src="{{asset('storage/app/arquivos/visitas/fotos/' . $visita->id . "/$foto->nome_arquivo")}}" height="250" alt="foto do Interno{{$visita->nome}}">

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
                                        <option value="principal" @if( isset( $visita ) && $foto->tipo == "principal") selected @endif>
                                            Principal
                                        </option>
                                        <option value="tatuagem" @if( isset( $visita ) && $foto->tipo == "tatuagem") selected @endif>
                                            tatuagem
                                        </option>
                                        <option value="perfil" @if( isset( $visita ) && $foto->tipo == "perfil") selected @endif>
                                            Perfil
                                        </option>
                                        <option value="cicatriz" @if( isset( $visita ) && $foto->tipo == "cicatriz") selected @endif>
                                            Cicatriz
                                        </option>
                                        <option value="sinais" @if( isset( $visita ) && $foto->tipo == "sinais") selected @endif>
                                            Sinais
                                        </option>
                                        <option value="outros" @if( isset( $visita ) && $foto->tipo == "outros") selected @endif>
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
                <select id="tipoFotoUm" name="tipoFoto[]" class="form-control tipoFoto">
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

        <div class="row displayNone">
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
        @if(isset($visita))

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
                                    <a href="{{ route('visita.fileDoc',['id' => $documento->fk_id, 'filename' => $documento->nome_arquivo])}}" data-toggle="tooltip" data-placement="top" title="Arquivos"><i class="mdi mdi mdi-cloud-download"></i></a>

                                    <a href="{{ route('visita.documentoDelete',['id' => $documento->fk_id, 'filename' => $documento->nome_arquivo])}}" data-toggle="tooltip" data-placement="top" title="Apagar Arquivo"><i class="mdi mdi-delete color_red"></i></a>

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

        <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Salvar Visita</button>
    </form>

    @endsection
@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{ asset('css/ocorrencia/buscar/buscar.css') }}" rel="stylesheet">
@if(isset($visita))
<link href="{{ asset('css/visita/cadastro/alterar.css') }}" rel="stylesheet">
    @else
    <link href="{{ asset('css/visita/cadastro/cadastro.css') }}" rel="stylesheet">
@endif
@endpush

@push('script')
<!-- <script src="{{ asset('js/outros/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('js/outros/select2.min.js') }}"></script> -->
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/outros/buscaVisita.js') }}"></script>
@if(isset($visita))
<script src="{{ asset('js/visita/alterar.js') }}"></script>
<script src="{{ asset('js/outros/enderecoAlterar.js') }}"></script>
<script src="{{ asset('js/outros/fotoMultiploAlterar.js') }}"></script>

            @else
    <script src="{{ asset('js/visita/cadastro.js') }}"></script>
    <script src="{{ asset('js/outros/endereco.js') }}"></script>
    <script src="{{ asset('js/outros/fotoMultiploAdd.js') }}"></script>
            @endif

<script src="{{ asset('vendor/userCam/jquery.userCam.min.js') }}"></script>
@endpush