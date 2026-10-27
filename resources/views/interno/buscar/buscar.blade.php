@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-search"></i> Buscar Interno</h3>
        <hr>
        <p>
            Buscar interno na base de dados.
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

    <form action="{{ route('interno.buscar') }}" method="get">

        {{--{!! csrf_field() !!}--}}

        <button type="button" id="btnBuscaAvancada" class="btn btn-rounded btn-sm background_81c784 float-right mb-3"><i class="mdi mdi-filter"></i>
            Busca Avançada
        </button>

        <div class="cf"></div>
        <div class="form-row">

            <div class="form-group col-sm-6">
                <label for="nome">Nome</label>
                <input type="text" name="nome" class="form-control" id="nome" placeholder="Nome">
            </div>

            <div class="form-group col-sm-6">
                <label for="nome">Nome Social (Alterado Oficialmente)</label>
                <input type="text" name="nome_social" class="form-control" id="nome_social" placeholder="Nome Social (Alterado Oficialmente)">
            </div>

            <div class="form-group col-6">
                <label for="outrosNomes">Outros Nomes, Vulgos ou Apelidos</label>
                <input type="text" name="outrosNomes" class="form-control" id="outrosNomes" placeholder="Outros Nomes, Vulgos ou Apelidos">
            </div>

            <div class="form-group col-6">
                <label for="cpf">CPF</label>
                <input type="text" name="cpf" class="form-control" id="cpf" placeholder="CPF">
            </div>

            <div class="form-group col-sm-6">
                <label for="mae">Mãe</label>
                <input type="text" name="mae" class="form-control" id="mae" placeholder="Mãe">
            </div>
            <div class="form-group col-sm-6">
                <label for="pai">Pai</label>
                <input type="text" name="pai" class="form-control" id="pai" placeholder="Pai">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="prontuario">Prontuário</label>
                <input type="text" name="prontuario" class="form-control" id="prontuario" placeholder="Prontuário">
            </div>

            <div class="form-group col-sm-3">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="" selected>
                        Ambos
                    </option>
                    <option value="A">
                        Ativo
                    </option>
                    <option value="I">
                        Inativo
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="sexo">Sexo</label>
                <select id="sexo" name="sexo" class="form-control">
                    <option value="">
                        Ambos
                    </option>
                    <option value="M">
                        Masculino
                    </option>
                    <option value="F">
                        Feminino
                    </option>
                    <option value="naoPreenchido">Não Preenchido</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="sentenciado">Provisório</label>
                <select id="sentenciado" name="sentenciado" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="N">
                        Sim
                    </option>
                    <option value="S">
                        Não
                    </option>
                    <option value="naoPreenchido">Não Preenchido</option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="provReg">
                <label for="regime">Regime Atual</label>
                <select id="regime" name="regime" class="form-control">
                    <option value="">
                        Todos
                    </option>
                    <option value="Aberto">
                        Aberto
                    </option>
                    <option value="Semiaberto">
                        Semiaberto
                    </option>
                    <option value="Fechado">
                        Fechado
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="medida_seguranca">Medida de Segurança</label>
                <select id="medida_seguranca" name="medida_seguranca" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="S">
                        Sim
                    </option>
                    <option value="N">
                        Não
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="condicao">Condição</label>
                <select id="condicao" name="condicao" class="form-control">
                    <option value="">
                        Todas
                    </option>
                    @foreach($condicaoLista as $condicao)
                        <option value="{{ $condicao->id }}">
                            {{ $condicao->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="periculosidade">Periculosidade</label>
                <select id="periculosidade" name="periculosidade" class="form-control">
                    <option value="" selected>
                        Todas
                    </option>
                    <option value="Alta">
                        Alta
                    </option>
                    <option value="Média">
                        Média
                    </option>
                    <option value="Baixa">
                        Baixa
                    </option>
                    <option value="naoPreenchida">
                        Não Informada
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="faccao">Facção</label>
                <select id="faccao" name="faccao" class="form-control">
                    <option value="" @if( isset( $interno ) && $interno->faccao == "") selected @endif>
                        Todas
                    </option>
                    <option value="SemFaccao" @if( isset( $interno ) && $interno->faccao == "SemFaccao") selected @endif>
                        Não Faccionado
                    </option>
                    @foreach($faccaoLista as $fac)
                        <option value="{{ $fac->id }}"  @if( isset( $interno ) && $interno->faccao == $fac->id) selected @endif>
                            {{ $fac->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="lideranca">Liderança</label>
                <select id="lideranca" name="lideranca" class="form-control">
                    <option value="" selected>
                        Selecione
                    </option>
                    <option value="S">
                        Sim
                    </option>
                    <option value="N">
                        Não
                    </option>
                </select>
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
            @if(PermissaoService::getTipoPermissao() == "I")
                <div class="form-group col-sm-3 displayNone" id="divPavilhao">
                    <label for="pavilhao">Pavilhão</label>
                    <select id="pavilhao" name="pavilhao" class="form-control selectUnidade">
                        <option value="">
                            Selecione
                        </option>
                    </select>
                </div>

                <div class="form-group col-sm-3 displayNone" id="divAla">
                    <label for="ala">Ala</label>
                    <select id="ala" name="ala" class="form-control selectUnidade">
                        <option value="">
                            Selecione
                        </option>
                    </select>
                </div>

                <div class="form-group col-sm-3 displayNone" id="divCela">
                    <label for="cela">Cela</label>
                    <select id="cela" name="cela" class="form-control">
                        <option value="">
                            Selecione
                        </option>
                    </select>
                </div>
            @endif

        </div>

        <div class="form-row displayNone" id="divBuscaAvancada">
            
            <div class="form-group col-sm-3">
                <label for="apenas_internos_fotos">Apenas internos (fotos):</label>
                <select id="apenas_internos_fotos" name="apenas_internos_fotos" class="form-control">
                    <option value="">
                        Com ou Sem Fotos
                    </option>
                    <option value="comFotos">
                        Com Fotos
                    </option>
                    <option value="semFotos">
                        Sem Fotos
                    </option>
                </select>
            </div>
            
            <div class="form-group col-sm-3">
                <label for="apenas_internos_dataNascimento">Apenas internos (data de nascimento):</label>
                <select id="apenas_internos_dataNascimento" name="apenas_internos_dataNascimento" class="form-control">
                    <option value="">
                        Com ou Sem Data de Nascimento Preenchida
                    </option>
                    <option value="comDataNascimento">
                        Com Data de Nascimento Preenchida
                    </option>
                    <option value="semDataNascimento">
                        Sem Data de Nascimento Preenchida
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="endereco_pais">Endereço - País</label>
                <select id="endereco_pais" name="endereco_pais" class="form-control">
                    <option value="" selected>
                        Selecione
                    </option>
                    <option value="África do Sul">África do Sul</option>
                    <option value="Albânia">Albânia</option>
                    <option value="Alemanha">Alemanha</option>
                    <option value="Andorra">Andorra</option>
                    <option value="Angola">Angola</option>
                    <option value="Anguilla">Anguilla</option>
                    <option value="Antigua">Antigua</option>
                    <option value="Arábia Saudita">Arábia Saudita</option>
                    <option value="Argentina">Argentina</option>
                    <option value="Armênia">Armênia</option>
                    <option value="Aruba">Aruba</option>
                    <option value="Austrália">Austrália</option>
                    <option value="Áustria">Áustria</option>
                    <option value="Azerbaijão">Azerbaijão</option>
                    <option value="Bahamas">Bahamas</option>
                    <option value="Bahrein">Bahrein</option>
                    <option value="Bangladesh">Bangladesh</option>
                    <option value="Barbados">Barbados</option>
                    <option value="Bélgica">Bélgica</option>
                    <option value="Benin">Benin</option>
                    <option value="Bermudas">Bermudas</option>
                    <option value="Bolívia">Bolívia</option>
                    <option value="Botsuana">Botsuana</option>
                    <option value="Brasil">Brasil</option>
                    <option value="Brunei">Brunei</option>
                    <option value="Bulgária">Bulgária</option>
                    <option value="Burkina Fasso">Burkina Fasso</option>
                    <option value="Butão">Butão</option>
                    <option value="Cabo Verde">Cabo Verde</option>
                    <option value="Camarões">Camarões</option>
                    <option value="Camboja">Camboja</option>
                    <option value="Canadá">Canadá</option>
                    <option value="Cazaquistão">Cazaquistão</option>
                    <option value="Chade">Chade</option>
                    <option value="Chile">Chile</option>
                    <option value="China">China</option>
                    <option value="Cidade do Vaticano">Cidade do Vaticano</option>
                    <option value="Colômbia">Colômbia</option>
                    <option value="Congo">Congo</option>
                    <option value="Coréia do Sul">Coréia do Sul</option>
                    <option value="Costa do Marfim">Costa do Marfim</option>
                    <option value="Costa Rica">Costa Rica</option>
                    <option value="Croácia">Croácia</option>
                    <option value="Dinamarca">Dinamarca</option>
                    <option value="Djibuti">Djibuti</option>
                    <option value="Dominica">Dominica</option>
                    <option value="EUA">EUA</option>
                    <option value="Egito">Egito</option>
                    <option value="El Salvador">El Salvador</option>
                    <option value="Emirados Árabes">Emirados Árabes</option>
                    <option value="Equador">Equador</option>
                    <option value="Eritréia">Eritréia</option>
                    <option value="Escócia">Escócia</option>
                    <option value="Eslováquia">Eslováquia</option>
                    <option value="Eslovênia">Eslovênia</option>
                    <option value="Espanha">Espanha</option>
                    <option value="Estônia">Estônia</option>
                    <option value="Etiópia">Etiópia</option>
                    <option value="Fiji">Fiji</option>
                    <option value="Filipinas">Filipinas</option>
                    <option value="Finlândia">Finlândia</option>
                    <option value="França">França</option>
                    <option value="Gabão">Gabão</option>
                    <option value="Gâmbia">Gâmbia</option>
                    <option value="Gana">Gana</option>
                    <option value="Geórgia">Geórgia</option>
                    <option value="Gibraltar">Gibraltar</option>
                    <option value="Granada">Granada</option>
                    <option value="Grécia">Grécia</option>
                    <option value="Guadalupe">Guadalupe</option>
                    <option value="Guam">Guam</option>
                    <option value="Guatemala">Guatemala</option>
                    <option value="Guiana">Guiana</option>
                    <option value="Guiana Francesa">Guiana Francesa</option>
                    <option value="Guiné-bissau">Guiné-bissau</option>
                    <option value="Haiti">Haiti</option>
                    <option value="Holanda">Holanda</option>
                    <option value="Honduras">Honduras</option>
                    <option value="Hong Kong">Hong Kong</option>
                    <option value="Hungria">Hungria</option>
                    <option value="Iêmen">Iêmen</option>
                    <option value="Ilhas Cayman">Ilhas Cayman</option>
                    <option value="Ilhas Cook">Ilhas Cook</option>
                    <option value="Ilhas Curaçao">Ilhas Curaçao</option>
                    <option value="Ilhas Marshall">Ilhas Marshall</option>
                    <option value="Ilhas Turks & Caicos">Ilhas Turks & Caicos</option>
                    <option value="Ilhas Virgens (brit.)">Ilhas Virgens (brit.)</option>
                    <option value="Ilhas Virgens(amer.)">Ilhas Virgens(amer.)</option>
                    <option value="Ilhas Wallis e Futuna">Ilhas Wallis e Futuna</option>
                    <option value="Índia">Índia</option>
                    <option value="Indonésia">Indonésia</option>
                    <option value="Inglaterra">Inglaterra</option>
                    <option value="Irlanda">Irlanda</option>
                    <option value="Islândia">Islândia</option>
                    <option value="Israel">Israel</option>
                    <option value="Itália">Itália</option>
                    <option value="Jamaica">Jamaica</option>
                    <option value="Japão">Japão</option>
                    <option value="Jordânia">Jordânia</option>
                    <option value="Kuwait">Kuwait</option>
                    <option value="Latvia">Latvia</option>
                    <option value="Líbano">Líbano</option>
                    <option value="Liechtenstein">Liechtenstein</option>
                    <option value="Lituânia">Lituânia</option>
                    <option value="Luxemburgo">Luxemburgo</option>
                    <option value="Macau">Macau</option>
                    <option value="Macedônia">Macedônia</option>
                    <option value="Madagascar">Madagascar</option>
                    <option value="Malásia">Malásia</option>
                    <option value="Malaui">Malaui</option>
                    <option value="Mali">Mali</option>
                    <option value="Malta">Malta</option>
                    <option value="Marrocos">Marrocos</option>
                    <option value="Martinica">Martinica</option>
                    <option value="Mauritânia">Mauritânia</option>
                    <option value="Mauritius">Mauritius</option>
                    <option value="México">México</option>
                    <option value="Moldova">Moldova</option>
                    <option value="Mônaco">Mônaco</option>
                    <option value="Montserrat">Montserrat</option>
                    <option value="Nepal">Nepal</option>
                    <option value="Nicarágua">Nicarágua</option>
                    <option value="Niger">Niger</option>
                    <option value="Nigéria">Nigéria</option>
                    <option value="Noruega">Noruega</option>
                    <option value="Nova Caledônia">Nova Caledônia</option>
                    <option value="Nova Zelândia">Nova Zelândia</option>
                    <option value="Omã">Omã</option>
                    <option value="Palau">Palau</option>
                    <option value="Panamá">Panamá</option>
                    <option value="Papua-nova Guiné">Papua-nova Guiné</option>
                    <option value="Paquistão">Paquistão</option>
                    <option value="Peru">Peru</option>
                    <option value="Polinésia Francesa">Polinésia Francesa</option>
                    <option value="Polônia">Polônia</option>
                    <option value="Porto Rico">Porto Rico</option>
                    <option value="Portugal">Portugal</option>
                    <option value="Qatar">Qatar</option>
                    <option value="Quênia">Quênia</option>
                    <option value="Rep. Dominicana">Rep. Dominicana</option>
                    <option value="Rep. Tcheca">Rep. Tcheca</option>
                    <option value="Reunion">Reunion</option>
                    <option value="Romênia">Romênia</option>
                    <option value="Ruanda">Ruanda</option>
                    <option value="Rússia">Rússia</option>
                    <option value="Saipan">Saipan</option>
                    <option value="Samoa Americana">Samoa Americana</option>
                    <option value="Senegal">Senegal</option>
                    <option value="Serra Leone">Serra Leone</option>
                    <option value="Seychelles">Seychelles</option>
                    <option value="Singapura">Singapura</option>
                    <option value="Síria">Síria</option>
                    <option value="Sri Lanka">Sri Lanka</option>
                    <option value="St. Kitts & Nevis">St. Kitts & Nevis</option>
                    <option value="St. Lúcia">St. Lúcia</option>
                    <option value="St. Vincent">St. Vincent</option>
                    <option value="Sudão">Sudão</option>
                    <option value="Suécia">Suécia</option>
                    <option value="Suiça">Suiça</option>
                    <option value="Suriname">Suriname</option>
                    <option value="Tailândia">Tailândia</option>
                    <option value="Taiwan">Taiwan</option>
                    <option value="Tanzânia">Tanzânia</option>
                    <option value="Togo">Togo</option>
                    <option value="Trinidad & Tobago">Trinidad & Tobago</option>
                    <option value="Tunísia">Tunísia</option>
                    <option value="Turquia">Turquia</option>
                    <option value="Ucrânia">Ucrânia</option>
                    <option value="Uganda">Uganda</option>
                    <option value="Uruguai">Uruguai</option>
                    <option value="Venezuela">Venezuela</option>
                    <option value="Vietnã">Vietnã</option>
                    <option value="Zaire">Zaire</option>
                    <option value="Zâmbia">Zâmbia</option>
                    <option value="Zimbábue">Zimbábue</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="endereco_estado">Endereço - Estado</label>
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
                    <option value="PB">
                        Paraíba
                    </option>
                    <option value="RS">
                        Rio Grande do Sul
                    </option>
                    <option value="RN">
                        Rio Grande do Norte
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

            <div class="form-group col-sm-3">
                <label for="endereco_cidade">Endereço - Cidade</label>
                <input type="text" name="endereco_cidade" class="form-control" id="endereco_cidade"
                       placeholder="Endereço - Cidade">
            </div>

            <div class="form-group col-sm-3">
                <label for="endereco_bairro">Endereço - Bairro</label>
                <input type="text" name="endereco_bairro" class="form-control" id="endereco_bairro"
                       placeholder="Endereço - Bairro">
            </div>

            <div class="form-group col-sm-3">
                <label for="endereco_cep">Endereço - CEP</label>
                <input type="text" name="endereco_cep" class="form-control" id="endereco_cep"
                       placeholder="Endereço - CEP">
            </div>

            <div class="form-group col-sm-6">
                <label for="endereco_logradouro">Endereço - Logradouro</label>
                <input type="text" name="endereco_logradouro" class="form-control" id="endereco_logradouro" placeholder="Endereço - Logradouro">
            </div>

            <div class="form-group col-sm-3">
                <label for="endereco_numero">Endereço - Número</label>
                <input type="text" name="endereco_numero" class="form-control" id="endereco_numero"
                       placeholder="Endereço - Número">
            </div>

            <div class="form-group col-sm-3">
                <label for="endereco_complemento">Endereço - Complemento</label>
                <input type="text" name="endereco_complemento" class="form-control" id="endereco_complemento"
                       placeholder="Endereço - Complemento">
            </div>

            <div class="form-group col-sm-3">
                <label for="naturalidade_pais">Naturalidade - País</label>
                <select id="naturalidade_pais" name="naturalidade_pais" class="form-control">
                    <option value="" selected>
                        Selecione
                    </option>
                    <option value="África do Sul">África do Sul</option>
                    <option value="Albânia">Albânia</option>
                    <option value="Alemanha">Alemanha</option>
                    <option value="Andorra">Andorra</option>
                    <option value="Angola">Angola</option>
                    <option value="Anguilla">Anguilla</option>
                    <option value="Antigua">Antigua</option>
                    <option value="Arábia Saudita">Arábia Saudita</option>
                    <option value="Argentina">Argentina</option>
                    <option value="Armênia">Armênia</option>
                    <option value="Aruba">Aruba</option>
                    <option value="Austrália">Austrália</option>
                    <option value="Áustria">Áustria</option>
                    <option value="Azerbaijão">Azerbaijão</option>
                    <option value="Bahamas">Bahamas</option>
                    <option value="Bahrein">Bahrein</option>
                    <option value="Bangladesh">Bangladesh</option>
                    <option value="Barbados">Barbados</option>
                    <option value="Bélgica">Bélgica</option>
                    <option value="Benin">Benin</option>
                    <option value="Bermudas">Bermudas</option>
                    <option value="Bolívia">Bolívia</option>
                    <option value="Botsuana">Botsuana</option>
                    <option value="Brasil">Brasil</option>
                    <option value="Brunei">Brunei</option>
                    <option value="Bulgária">Bulgária</option>
                    <option value="Burkina Fasso">Burkina Fasso</option>
                    <option value="Butão">Butão</option>
                    <option value="Cabo Verde">Cabo Verde</option>
                    <option value="Camarões">Camarões</option>
                    <option value="Camboja">Camboja</option>
                    <option value="Canadá">Canadá</option>
                    <option value="Cazaquistão">Cazaquistão</option>
                    <option value="Chade">Chade</option>
                    <option value="Chile">Chile</option>
                    <option value="China">China</option>
                    <option value="Cidade do Vaticano">Cidade do Vaticano</option>
                    <option value="Colômbia">Colômbia</option>
                    <option value="Congo">Congo</option>
                    <option value="Coréia do Sul">Coréia do Sul</option>
                    <option value="Costa do Marfim">Costa do Marfim</option>
                    <option value="Costa Rica">Costa Rica</option>
                    <option value="Croácia">Croácia</option>
                    <option value="Dinamarca">Dinamarca</option>
                    <option value="Djibuti">Djibuti</option>
                    <option value="Dominica">Dominica</option>
                    <option value="EUA">EUA</option>
                    <option value="Egito">Egito</option>
                    <option value="El Salvador">El Salvador</option>
                    <option value="Emirados Árabes">Emirados Árabes</option>
                    <option value="Equador">Equador</option>
                    <option value="Eritréia">Eritréia</option>
                    <option value="Escócia">Escócia</option>
                    <option value="Eslováquia">Eslováquia</option>
                    <option value="Eslovênia">Eslovênia</option>
                    <option value="Espanha">Espanha</option>
                    <option value="Estônia">Estônia</option>
                    <option value="Etiópia">Etiópia</option>
                    <option value="Fiji">Fiji</option>
                    <option value="Filipinas">Filipinas</option>
                    <option value="Finlândia">Finlândia</option>
                    <option value="França">França</option>
                    <option value="Gabão">Gabão</option>
                    <option value="Gâmbia">Gâmbia</option>
                    <option value="Gana">Gana</option>
                    <option value="Geórgia">Geórgia</option>
                    <option value="Gibraltar">Gibraltar</option>
                    <option value="Granada">Granada</option>
                    <option value="Grécia">Grécia</option>
                    <option value="Guadalupe">Guadalupe</option>
                    <option value="Guam">Guam</option>
                    <option value="Guatemala">Guatemala</option>
                    <option value="Guiana">Guiana</option>
                    <option value="Guiana Francesa">Guiana Francesa</option>
                    <option value="Guiné-bissau">Guiné-bissau</option>
                    <option value="Haiti">Haiti</option>
                    <option value="Holanda">Holanda</option>
                    <option value="Honduras">Honduras</option>
                    <option value="Hong Kong">Hong Kong</option>
                    <option value="Hungria">Hungria</option>
                    <option value="Iêmen">Iêmen</option>
                    <option value="Ilhas Cayman">Ilhas Cayman</option>
                    <option value="Ilhas Cook">Ilhas Cook</option>
                    <option value="Ilhas Curaçao">Ilhas Curaçao</option>
                    <option value="Ilhas Marshall">Ilhas Marshall</option>
                    <option value="Ilhas Turks & Caicos">Ilhas Turks & Caicos</option>
                    <option value="Ilhas Virgens (brit.)">Ilhas Virgens (brit.)</option>
                    <option value="Ilhas Virgens(amer.)">Ilhas Virgens(amer.)</option>
                    <option value="Ilhas Wallis e Futuna">Ilhas Wallis e Futuna</option>
                    <option value="Índia">Índia</option>
                    <option value="Indonésia">Indonésia</option>
                    <option value="Inglaterra">Inglaterra</option>
                    <option value="Irlanda">Irlanda</option>
                    <option value="Islândia">Islândia</option>
                    <option value="Israel">Israel</option>
                    <option value="Itália">Itália</option>
                    <option value="Jamaica">Jamaica</option>
                    <option value="Japão">Japão</option>
                    <option value="Jordânia">Jordânia</option>
                    <option value="Kuwait">Kuwait</option>
                    <option value="Latvia">Latvia</option>
                    <option value="Líbano">Líbano</option>
                    <option value="Liechtenstein">Liechtenstein</option>
                    <option value="Lituânia">Lituânia</option>
                    <option value="Luxemburgo">Luxemburgo</option>
                    <option value="Macau">Macau</option>
                    <option value="Macedônia">Macedônia</option>
                    <option value="Madagascar">Madagascar</option>
                    <option value="Malásia">Malásia</option>
                    <option value="Malaui">Malaui</option>
                    <option value="Mali">Mali</option>
                    <option value="Malta">Malta</option>
                    <option value="Marrocos">Marrocos</option>
                    <option value="Martinica">Martinica</option>
                    <option value="Mauritânia">Mauritânia</option>
                    <option value="Mauritius">Mauritius</option>
                    <option value="México">México</option>
                    <option value="Moldova">Moldova</option>
                    <option value="Mônaco">Mônaco</option>
                    <option value="Montserrat">Montserrat</option>
                    <option value="Nepal">Nepal</option>
                    <option value="Nicarágua">Nicarágua</option>
                    <option value="Niger">Niger</option>
                    <option value="Nigéria">Nigéria</option>
                    <option value="Noruega">Noruega</option>
                    <option value="Nova Caledônia">Nova Caledônia</option>
                    <option value="Nova Zelândia">Nova Zelândia</option>
                    <option value="Omã">Omã</option>
                    <option value="Palau">Palau</option>
                    <option value="Panamá">Panamá</option>
                    <option value="Papua-nova Guiné">Papua-nova Guiné</option>
                    <option value="Paquistão">Paquistão</option>
                    <option value="Peru">Peru</option>
                    <option value="Polinésia Francesa">Polinésia Francesa</option>
                    <option value="Polônia">Polônia</option>
                    <option value="Porto Rico">Porto Rico</option>
                    <option value="Portugal">Portugal</option>
                    <option value="Qatar">Qatar</option>
                    <option value="Quênia">Quênia</option>
                    <option value="Rep. Dominicana">Rep. Dominicana</option>
                    <option value="Rep. Tcheca">Rep. Tcheca</option>
                    <option value="Reunion">Reunion</option>
                    <option value="Romênia">Romênia</option>
                    <option value="Ruanda">Ruanda</option>
                    <option value="Rússia">Rússia</option>
                    <option value="Saipan">Saipan</option>
                    <option value="Samoa Americana">Samoa Americana</option>
                    <option value="Senegal">Senegal</option>
                    <option value="Serra Leone">Serra Leone</option>
                    <option value="Seychelles">Seychelles</option>
                    <option value="Singapura">Singapura</option>
                    <option value="Síria">Síria</option>
                    <option value="Sri Lanka">Sri Lanka</option>
                    <option value="St. Kitts & Nevis">St. Kitts & Nevis</option>
                    <option value="St. Lúcia">St. Lúcia</option>
                    <option value="St. Vincent">St. Vincent</option>
                    <option value="Sudão">Sudão</option>
                    <option value="Suécia">Suécia</option>
                    <option value="Suiça">Suiça</option>
                    <option value="Suriname">Suriname</option>
                    <option value="Tailândia">Tailândia</option>
                    <option value="Taiwan">Taiwan</option>
                    <option value="Tanzânia">Tanzânia</option>
                    <option value="Togo">Togo</option>
                    <option value="Trinidad & Tobago">Trinidad & Tobago</option>
                    <option value="Tunísia">Tunísia</option>
                    <option value="Turquia">Turquia</option>
                    <option value="Ucrânia">Ucrânia</option>
                    <option value="Uganda">Uganda</option>
                    <option value="Uruguai">Uruguai</option>
                    <option value="Venezuela">Venezuela</option>
                    <option value="Vietnã">Vietnã</option>
                    <option value="Zaire">Zaire</option>
                    <option value="Zâmbia">Zâmbia</option>
                    <option value="Zimbábue">Zimbábue</option>
                    <option value="naoPreenchida">Não Preenchida</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="naturalidade_estado">Naturalidade - Estado</label>
                <select id="naturalidade_estado" name="naturalidade_estado" class="form-control selectEstados">
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
                    <option value="RN">
                        Rio Grande do Norte
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
                    <option value="naoPreenchida">Não Preenchida</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="naturalidade_cidade">Naturalidade - Cidade</label>
                <input type="text" name="naturalidade_cidade" class="form-control" id="naturalidade_cidade"
                       placeholder="Naturalidade - Cidade">
            </div>

            <div class="form-group col-sm-3">
                <label for="escolaridade">Escolaridade</label>
                <select id="escolaridade" name="escolaridade" class="form-control">
                    <option value="">
                        Todas
                    </option>
                    <option value="Analfabeto">Analfabetos</option>
                    <option value="Alfabetizado">Alfabetizados</option>
                    <option value="Ensino Fundamental Incompleto">Ensino Fundamental Incompleto</option>
                    <option value="Ensino Fundamental Completo">Ensino Fundamental Completo</option>
                    <option value="Ensino Médio Incompleto">Ensino Médio Incompleto</option>
                    <option value="Ensino Médio Completo">Ensino Médio Completo</option>
                    <option value="Ensino Superior Incompleto">Ensino Superior Incompleto</option>
                    <option value="Ensino Superior Completo">Ensino Superior Completo</option>
                    <option value="Pós-graduação lato sensu">Pós-graduação lato sensu</option>
                    <option value="Mestrado">Mestrado</option>
                    <option value="Doutorado">Doutorado</option>
                    <option value="Pós-Doutorado">Pós-Doutorado</option>
                    <option value="naoPreenchida">Não Preenchida</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="profissao">Profissão</label>
                <select id="profissao" name="profissao" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($profissao as $prof)
                        <option value="{{ $prof->id }}">
                            {{ $prof->nome }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="estado_civil">Estado Civil</label>
                <select id="estado_civil" name="estado_civil" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="solteiro">
                        Solteiro(a)
                    </option>
                    <option value="casado">
                        Casado(a)
                    </option>
                    <option value="uniao estavel">
                        União Estável
                    </option>
                    <option value="separado">
                        Separado(a)
                    </option>
                    <option value="divorciado">
                        Divorciado(a)
                    </option>
                    <option value="viuvo">
                        Viúvo(a)
                    </option>
                    <option value="naoPreenchido">
                        Não Preenchido
                    </option>

                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="descricao_foto">Legenda da Foto</label>
                <input type="text" name="descricao_foto" class="form-control" id="descricao_foto" placeholder="Tatuagem de peixe...">
            </div>

            <div class="form-group col-sm-3">
                <label for="ultima_visita">Não recebe visita desde:</label>
                <input type="text" class="form-control" name="ultima_visita" id="ultima_visita" placeholder="00/00/0000">
            </div>

            <div class="form-group col-sm-3">
                <label for="ultimo_advogado">Não recebe advogado desde:</label>
                <input type="text" class="form-control" name="ultimo_advogado" id="ultimo_advogado" placeholder="00/00/0000">
            </div>



            <div class="form-group col-sm-3">
                <label for="religiao">Religião</label>
                <select id="religiao" name="religiao" class="form-control">
                    <option value="">
                        Todas
                    </option>
                    @foreach($religioes as $rel)
                        <option value="{{ $rel->religiao }}">
                            {{ $rel->religiao }}
                        </option>
                    @endforeach
                    <option value="naoPreenchida">Não Preenchida</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="etnia">Cor da Pele</label>
                <select id="etnia" name="etnia" class="form-control">
                    <option value="">
                        Todas
                    </option>
                    <option value="Branca">
                        Branca
                    </option>
                    <option value="Indígena">
                        Indígena
                    </option>
                    <option value="Preta">
                        Preta
                    </option>
                    <option value="Parda">
                        Parda
                    </option>
                    <option value="Amarela">
                        Amarela
                    </option>
                    <option value="naoPreenchida">Não Preenchida</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="opcao_sexual">Orientação Sexual</label>
                <select id="opcao_sexual" name="opcao_sexual" class="form-control">
                    <option value="" selected>
                        Selecione
                    </option>
                    <option value="heterossexual">
                        Heterossexual
                    </option>
                    <option value="homossexual">
                        Homossexual
                    </option>
                    <option value="bissexual">
                        Bissexual
                    </option>
                    <option value="nao declarado">
                        Não Declarado
                    </option>
                    <option value="naoPreenchida">Não Preenchida</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="nascimento_de">Data de Nascimento</label>
                <div class="row">
                    <div class="col-sm-4">
                        <label for="nascimento_de">De</label>
                        <input type="text" class="form-control" name="nascimento_de" id="nascimento_de" placeholder="00/00/0000">
                    </div>
                    <div class="col-sm-4">
                        <label for="nascimento_ate">Até</label>
                        <input type="text" class="form-control" name="nascimento_ate" id="nascimento_ate" placeholder="00/00/0000">
                    </div>
                </div>
            </div>


        </div>
        
       
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="checkNomeMaeResultado" id="checkNomeMaeResultado" checked>
                <label class="custom-control-label" for="checkNomeMaeResultado">Mostrar Nome da Mãe no Resultado da Busca</label>
            </div>
        
        <br>

        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar Interno</button>

    </form>

    @if($dados != false)

        <div id="links" class="mt-5">
            {{ $dados->links() }}
        </div>

        @if($dados->total() > 0)
            <form action="{{ route('interno.gerarListaBusca') }}" method="post">
                {!! csrf_field() !!}
                <h6 class="mt-2"><i>Sua busca retornou
                        {{ $dados->total() }} resultados
                    </i>
                </h6>
                <button type="submit" class="btn btn-primary"><i class="fas fa-print"></i> Imprimir Busca</button>
            </form>
        @else

            <h5 class="mt-5"><i>Sua busca não obteve resultados! </i>
            </h5>

        @endif

        <div class="table-responsive">
        <table class="table mt-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                {{--<th scope="col">#</th>--}}
                <th scope="col">Prontuário</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                {{--<th scope="col">Mãe</th>--}}
                {{--<th scope="col">Liderança</th>--}}
                <th scope="col">Localização</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            {{-- modal para abrir a foto do interno --}}
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
            {{-- !! modal para abrir a foto do interno --}}
            @foreach($dados as $dado)
            <tr @if($dado->status == "I") class="background_ffd7d7" @endif>
                {{-- calculo da ordem do registro --}}
                    {{--<td scope="row" class="fontSize-10px"><i><strong>{{($dados->currentPage() * $dados->perPage()) - $dados->perPage() + $loop->iteration  }}</strong></i></td>--}}
                    <td scope="row">{{$dado->id }}</td>
                    <td>
                        @if(file_exists(storage_path('app/arquivos/internos/fotos/' . $dado->id . "/" . FotoService::getFotoPrincipalByIdTipo($dado->id, 'interno') )))
                            <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $dado->id . "/". FotoService::getFotoPrincipalByIdTipo($dado->id, 'interno'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                            @else
                            <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                        @endif
                        {{--@if($dado->foto != null)--}}
                                {{--<img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $dado->id . "/$dado->foto")}}" data-content="{{$dado->fototipo}},{{$dado->fotolegenda}},{{$dado->fotodatainserida}},{{$dado->fotodataalterada}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">--}}
                        {{--@else--}}
                                {{--<img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">--}}
                        {{--@endif--}}
                    </td>
                    <td>{{ mb_strtoupper($dado->nome, 'UTF-8') }}
                        <br>
                        @if($dado->nome_social != null)
                            <i style="font-size: 10px">Nome Social (Alterado Oficialmente): {{$dado->nome_social}}</i>
                        @endif
                        <br>
                        @if($nomeMaeBusca == 'S')
                            Mãe: {{ $dado->mae }}
                        @endif
                    </td>
                    {{--<td style="font-size: 11px !important;">{{ $dado->mae }}</td>--}}
                    {{--<td>{{ $dado->lideranca }}</td>--}}
                    <td style="font-size: 10px !important;">
                        {{ $dado->sigla }}
                            {{ " > ".$dado->pvNome }}
                            @if(PermissaoService::getTipoPermissao() == "I")
                                {{" > ".$dado->ala." > ".$dado->cela }}
                            @endif
                    </td>
                    <td class="btn-acoes color_01579b">

                        <a href="{{ route('interno.show',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>

                        @if(PermissaoService::getVerifyPermissionView("InternoController","editar"))
                        <a href="{{ route('interno.editar',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>
                        @endif

                        @if($dado->status == "I")
                            @if(PermissaoService::getVerifyPermissionView("InternoController","reativar"))
                                <a href="{{ route('interno.reativar',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Reativar interno"><i class="mdi mdi-play-circle-outline"></i></a>
                            @endif
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
    @else
        <h5 class="mt-5"><i>Sua busca @if(isset($nomeFiltro) && $nomeFiltro != "")pelo interno {{ $nomeFiltro }}@endif não obteve resultados! </i>
    @endif
@endsection
@push('css')
<link href="{{ asset('css/interno/buscar/buscar.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/interno/buscar.js') }}"></script>
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
@endpush