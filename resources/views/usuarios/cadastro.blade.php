@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fa fa-users"></i> Funcionários</h3>
        <hr>
        <p>
            Busca e cadastro de usuários do {{ config('app.name') }}.
        </p>
    </div>

    <form action="#">

        <h5>Dados do Pessoais</h5>
        <div class="form-row">

            <div class="form-group col-sm-6">
                <label for="nome">Nome</label>
                <input type="text" name="nome" class="form-control" id="nome" placeholder="Nome" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="sexo">Sexo</label>
                <select id="sexo" name="sexo" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="Masculino">
                        Masculino
                    </option>
                    <option value="Feminino">
                        Feminino
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="data_nascimento">Data de nascimento</label>
                <input type="text" class="form-control" name="data_nascimento" id="data_nascimento"
                       placeholder="Data de Nascimento" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-sm">
                <label for="nome">Outros Nomes</label>
                <input type="text" name="outro_nome" class="form-control" id="outro_nome"
                       placeholder="vulgo, nome social...">
                <i class="fas fa-plus-square m-l-05 cursorPointer" id="addFone"></i>

                <div id="groupyOutrosNomes">
                </div>

            </div>


        </div>

        <div class="form-row">
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
                <label for="rg">RG</label>
                <input type="text" name="rg" class="form-control" id="rg" placeholder="Número do RG">
            </div>
            <div class="form-group col-sm-3">
                <label for="rg_orgao_emissor">Órgão Emissor</label>
                <input type="text" name="rg_orgao_emissor" class="form-control" id="rg_orgao_emissor"
                       placeholder="Órgão Emissor do RG">
            </div>
            <div class="form-group col-sm-3">
                <label for="cpf">CPF</label>
                <input type="text" name="cpf" class="form-control" id="cpf" placeholder="Número do CPF">
            </div>
            <div class="form-group col-sm-3">
                <label for="estado_civil">Estado civil</label>
                <select id="estado_civil" name="estado_civil" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="Solteiro">
                        Solteiro(a)
                    </option>
                    <option value="Casado">
                        Casado(a)
                    </option>
                    <option value="União Estável">
                        União Estável
                    </option>
                    <option value="Separado">
                        Separado(a)
                    </option>
                    <option value="Divorciado">
                        Divorciado(a)
                    </option>
                    <option value="Viúvo">
                        Viúvo(a)
                    </option>
                </select>
            </div>
            <div class="form-group col-sm-3">
                <label for="naturalidade_estado">Estado Naturalidade</label>
                <select id="naturalidade_estado" name="naturalidade_estado" class="form-control">
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
            <div class="form-group col-sm-3">
                <label for="naturalidade_cidade">Cidade Naturalidade</label>
                <select id="naturalidade_cidade" name="naturalidade_cidade" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="nacionalidade">Nacionalidade</label>
                <select id="nacionalidade" name="nacionalidade" class="form-control">
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
                    <option value="Botsuana">Botsuana</option>
                    <option value="Brasil" selected>Brasil</option>
                    <option value="Brunei">Brunei</option>
                    <option value="Bulgária">Bulgária</option>
                    <option value="Burkina Fasso">Burkina Fasso</option>
                    <option value="botão">botão</option>
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
                <label for="escolaridade">Escolaridade</label>
                <select id="escolaridade" name="escolaridade" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="Alfabetizado">
                        Alfabetizado
                    </option>
                    <option value="Analfabeto">
                        Analfabeto
                    </option>
                    <option value="Doutorado">
                        Doutorado
                    </option>
                    <option value="Ensino Fundamental Completo">
                        Ensino Fundamental Completo
                    </option>
                    <option value="Ensino Fundamental Incompleto">
                        Ensino Fundamental Incompleto
                    </option>
                    <option value="Ensino Médio Completo">
                        Ensino Médio Completo
                    </option>
                    <option value="Ensino Médio Incompleto">
                        Ensino Médio Incompleto
                    </option>
                    <option value="Ensino Superior Completo">
                        Ensino Superior Completo
                    </option>
                    <option value="Ensino Superior Incompleto">
                        Ensino Superior Incompleto
                    </option>
                    <option value="Mestrado">
                        Mestrado
                    </option>
                    <option value="Não Informado">
                        Não Informado
                    </option>
                    <option value="Pós-graduação">
                        Pós-graduação
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-4">
                <label for="profissao">Profissão</label>
                <select id="profissao" name="profissao" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-4">
                <label for="orientacao_sexual">Orientação Sexual</label>
                <select id="orientacao_sexual" name="orientacao_sexual" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="Heterossexual">
                        Heterossexual
                    </option>
                    <option value="Bissexual">
                        Bissexual
                    </option>
                    <option value="Não Declarado">
                        Não Declarado
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-4">
                <label for="religiao">Religião</label>
                <select id="religiao" name="religiao" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="Adventista">
                        Adventista
                    </option>
                    <option value="Agnóstico">
                        Agnóstico
                    </option>
                    <option value="Ateu">
                        Ateu
                    </option>
                    <option value="Budista">
                        Budista
                    </option>
                    <option value="Católica">
                        Católica
                    </option>
                    <option value="Espírita">
                        Espírita
                    </option>
                    <option value="Evangélica">
                        Evangélica
                    </option>
                    <option value="Judeu">
                        Judeu
                    </option>
                    <option value="Mórmon">
                        Mórmon
                    </option>
                    <option value="Muçulmana">
                        Muçulmana
                    </option>
                    <option value="Protestante">
                        Protestante
                    </option>
                    <option value="Sem Religião">
                        Sem Religião
                    </option>
                    <option value="Outras">
                        Outras
                    </option>
                    <option value="Não Informado">
                        Não Informado
                    </option>
                </select>
            </div>
        </div>

        {{-- Endereco --}}
        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="endereco_cep">CEP</label>
                <input type="text" name="endereco_cep" class="form-control" id="endereco_cep"
                       placeholder="Informe o CEP">
            </div>

            <div class="form-group col-sm-6">
                <label for="endereco_logradouro">Logradouro</label>
                <input type="text" name="endereco_logradouro" class="form-control" id="endereco_logradouro"
                       placeholder="Rua, av..">
            </div>

            <div class="form-group col-sm-3">
                <label for="endereco_numero">Número</label>
                <input type="text" name="endereco_numero" class="form-control" id="endereco_numero"
                       placeholder="Número">
            </div>

            <div class="form-group col-sm-3">
                <label for="endereco_bairro">Bairro</label>
                <input type="text" name="endereco_bairro" class="form-control" id="endereco_bairro"
                       placeholder="Bairro">
            </div>

            <div class="form-group col-sm-3">
                <label for="endereco_complemento">Complemento</label>
                <input type="text" name="endereco_complemento" class="form-control" id="endereco_complemento"
                       placeholder="Complemento">
            </div>

            <div class="form-group col-sm-3">
                <label for="endereco_cidade">Cidade</label>
                <input type="text" name="endereco_cidade" class="form-control" id="endereco_cidade"
                       placeholder="Cidade">
            </div>

            <div class="form-group col-sm-3">
                <label for="endereco_estado">Estado</label>
                <select id="endereco_estado" name="endereco_estado" class="form-control">
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
        <div id="groupEndereco">

        </div>

        {{-- End Endereco --}}

        <div class="row">

            <div class="form-group col-sm-3">
                <label for="altura">Altura</label>
                <input type="text" name="altura" class="form-control" id="altura" placeholder="Informe a Altura">
            </div>

            <div class="form-group col-sm-3">
                <label for="peso">Peso</label>
                <input type="text" name="peso" class="form-control" id="peso" placeholder="Informe o Peso">
            </div>

            <div class="form-group col-sm-3">
                <label for="etnia">Cor da Pele</label>
                <select id="etnia" name="etnia" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="Branca">
                        Branca
                    </option>
                    <option value="Indígena">
                        Indígena
                    </option>
                    <option value="Negra">
                        Negra
                    </option>
                    <option value="Parda">
                        Parda
                    </option>
                    <option value="Amarela">
                        Amarela
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="cor_olhos">Cor dos Olhos</label>
                <input type="text" name="cor_olhos" class="form-control" id="cor_olhos" placeholder="Informe a cor dos olhos">
            </div>

            <div class="form-group col-sm-3">
                <label for="cor_cabelos">Cor dos Cabelos</label>
                <input type="text" name="cor_cabelos" class="form-control" id="cor_cabelos" placeholder="Informe a cor dos cabelos">
            </div>

        </div>

        <h5 class="mt-4">Dados da Entrada na {{ config('app.secretaria') }} {{ config('app.uf') }}</h5>
        <div class="row">

            <div class="form-group col-sm-3">
                <label for="pavilhao">Pavilhão</label>
                <select id="pavilhao" name="pavilhao" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="ala">Ala</label>
                <select id="ala" name="ala" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="cela">Cela</label>
                <select id="cela" name="cela" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="procedencia">Procedência</label>
                <select id="procedencia" name="procedencia" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="documento_entrada_numero">Documento de Entrada</label>
                <input type="text" name="documento_entrada_numero" class="form-control" id="documento_entrada_numero" placeholder="Informe o numero">
            </div>

            <div class="form-group col-sm-3">
                <label for="presidio_origem">Presídio de Origem</label>
                <select id="presidio_origem" name="presidio_origem" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="condicao_interno">Condição do Interno</label>
                <select id="condicao_interno" name="condicao_interno" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="Provisório">
                        Provisório
                    </option>
                    <option value="Sentenciado">
                        Sentenciado
                    </option>
                    <option value="Livramento Condicional">
                        Livramento Condicional
                    </option>
                    <option value="Foragido">
                        Foragido
                    </option>
                    <option value="Internamento">
                        Internamento
                    </option>
                    <option value="Prisão Domiciliar">
                        Prisão Domiciliar
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="regime_atual">Regime Atual</label>
                <select id="regime_atual" name="regime_atual" class="form-control">
                    <option value="">
                        Selecione
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
                    <option value="Medida de Segurança">
                        Medida de Segurança
                    </option>
                    <option value="Provisório">
                        Provisório
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-4">
                <label for="grau_periculosidade">Grau de Periculosidade</label>
                <select id="grau_periculosidade" name="grau_periculosidade" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="Nível 1">Nível 1</option>
                    <option value="Nível 1-A">Nível 1-A</option>
                    <option value="Nível 2">Nível 2</option>
                    <option value="Nível 2-A">Nível 2-A</option>
                    <option value="Nível 3">Nível 3</option>
                    <option value="Nível 3-A">Nível 3-A</option>
                    <option value="Nível 4">Nível 4</option>
                    <option value="Nível 4-A">Nível 4-A</option>
                    <option value="Nível 5">Nível 5</option>
                    <option value="Nível 5-A">Nível 5-A</option>
                </select>
            </div>

            <div class="form-group col-sm-4">
                <label for="faccao">Facção</label>
                <select id="faccao" name="faccao" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-4">
                <label for="lideranca_negativa">Liderança Negativa</label>
                <select id="lideranca_negativa" name="lideranca_negativa" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="Sim">
                        Sim
                    </option>
                    <option value="Não">
                        Não
                    </option>
                </select>
            </div>

        </div>

        <h5 class="mt-4">Fotos</h5>
        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="fotos">Fotos</label>
                <input type="file" name="fotos" class="form-control" id="fotos" accept="image/*" multiple>
            </div>

        </div>


        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar interno</button>
    </form>
@endsection
@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/interno/cadastro.js') }}"></script>
@endpush