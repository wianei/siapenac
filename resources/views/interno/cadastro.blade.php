@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
    @if(isset($interno))
        <h3><i class="mdi mdi-account-edit"></i>
            Alteração de Cadastro do interno
        </h3>
            <hr>
            <p>
                Formulário para alterar os dados do interno selecionado.
            </p>

        @else
            <h3><i class="mdi mdi-account-plus"></i>
                Cadastro de interno
            </h3>
        <hr>
        <p>
            Realizar o cadastro de um novo interno do {{ config('app.sistema_estado') }}.
        </p>
    @endif
</div>
<div class="flash-message">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))

            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div> <!-- end .flash-message -->

@if(isset($interno))
<form action="{{ route('interno.update',$interno->id) }}" id="formUpdateInterno" method="post" enctype="multipart/form-data">
    <input type="hidden" name="idPessoa" id="idPessoa" value="{{$interno->id}}">
    <input type="hidden" name="tabelaFoto" id="tabelaFoto" value="interno">
@else
<form action="{{ route('interno.cadastro.validar') }}" id="formCadastroInterno" method="post" enctype="multipart/form-data">
@endif
    {!! csrf_field() !!}

    <h5>Dados do Pessoais</h5>
        <div class="form-row">

            <div class="form-group col-sm-6">
                <label for="nome">Nome *</label>
                <input type="text" name="nome" value="{{ $interno->nome or old('nome') }}" class="form-control" id="nome" placeholder="Nome" required>
            </div>

            <div class="form-group col-sm-6">
                <label for="nome_social">Nome Social (Alterado Oficialmente)</label>
                <input type="text" name="nome_social" value="{{ $interno->nome_social or old('nome_social') }}" class="form-control" id="nome_social" placeholder="Nome Social (Alterado Oficialmente)">
            </div>

            <div class="form-group col-sm-3">
                <label for="sexo">Sexo *</label>
                <select id="sexo" name="sexo" class="form-control" required>
                    <option value="" @if( isset( $interno ) && $interno->sexo == "" ) selected @endif>
                        Selecione
                    </option>
                    <option value="M" @if( isset( $interno ) && $interno->sexo == "M" ) selected @endif>
                        Masculino
                    </option>
                    <option value="F" @if( isset( $interno ) && $interno->sexo == "F" ) selected @endif>
                        Feminino
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="data_nascimento">Data de nascimento *</label>
                <input type="text" class="form-control" name="data_nascimento" value="{{ $interno->data_nascimento or old('data_nascimento') }}" id="data_nascimento" placeholder="Data de Nascimento" required>
            </div>
        </div>
        <div class="form-row">
            <div id="divOutrosNomes"></div>
            @if(isset($interno))
                @if(count($outrosNomes) == 0)
                    <div class="form-group col-sm-12">
                        <label for="nome">Outros Nomes</label>
                        <input type="text" name="outro_nome[]" class="form-control outro_nome" id="outro_nome" placeholder="vulgo, apelhido...">
                        <i class="fas fa-plus-square m-l-05 cursorPointer" id="addFoneEdit"></i>

                        <div id="groupyOutrosNomes">
                        </div>
                    </div>
                @else
                    @foreach($outrosNomes as $outroNome)
                        <div class="form-group col-sm-12" data-content="{{ $outroNome->id }}">
                            <label for="nome">Outros Nomes</label>
                            <input type="text" name="outro_nome[]" value="{{ $outroNome->nome }}" class="form-control outro_nome" id="outro_nome{{$loop->iteration}}" placeholder="vulgo, nome social...">
                            <i class="fas fa-minus-circle cursorPointer remove_field removeEdit" id="ioutro_nome{{$loop->iteration}}"></i>
                            @if($loop->iteration == 1)
                            <i class="fas fa-plus-square m-l-05 cursorPointer" id="addFoneEdit"></i>
                            @endif
                        </div>
                    @endforeach
                        <div class="form-group col-sm-12" id="groupyOutrosNomes">
                        </div>
                @endif
            @else
                <div class="form-group col-sm-12">
                    <label for="nome">Outros Nomes</label>
                    <input type="text" name="outro_nome[]" class="form-control outro_nome" id="outro_nome" placeholder="vulgo, nome social...">
                    <i class="fas fa-plus-square m-l-05 cursorPointer" id="addFone"></i>

                    <div id="groupyOutrosNomes">
                    </div>
                </div>
            @endif


        </div>

        <div class="form-row">
            <div class="form-group col-sm-6">
                <label for="mae">Mãe</label>
                <input type="text" name="mae" value="{{ $interno->mae or old('mae') }}" class="form-control" id="mae" placeholder="Mãe">
            </div>
            <div class="form-group col-sm-6">
                <label for="pai">Pai</label>
                <input type="text" name="pai" value="{{ $interno->pai or old('pai') }}" class="form-control" id="pai" placeholder="Pai">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="rg_numero">Número do RG</label>
                <input type="text" name="rg_numero" value="{{ $interno->rg_numero or old('rg_numero') }}" class="form-control" id="rg_numero" placeholder="Número do RG">
            </div>
            <div class="form-group col-sm-3">
                <label for="rg_data">Data de Expedição do RG</label>
                <input type="text" name="rg_data" value="{{ $interno->rg_data or old('rg_data') }}" class="form-control" id="rg_data" placeholder="Data de expedição do RG">
            </div>
            <div class="form-group col-sm-3">
                <label for="rg_orgaoexpedidor">Órgão e Estado Emissor do RG</label>
                <input type="text" name="rg_orgaoexpedidor" value="{{ $interno->rg_orgaoexpedidor or old('rg_orgaoexpedidor') }}" class="form-control" id="rg_orgaoexpedidor" placeholder="Órgão Emissor do RG">
            </div>
            <div class="form-group col-sm-3">
                <label for="cpf">CPF</label>
                <input type="text" name="cpf" value="{{ $interno->cpf or old('cpf') }}" maxlength="11" class="form-control" id="cpf" placeholder="Número do CPF">
            </div>
            <div class="form-group col-sm-3">
                <label for="estado_civil">Estado civil *</label>
                <select id="estado_civil" name="estado_civil" class="form-control" required>
                    <option value="" @if( isset( $interno ) && $interno->estado_civil == "" ) selected @endif>
                        Selecione
                    </option>
                    <option value="solteiro" @if( isset( $interno ) && $interno->estado_civil == "solteiro" ) selected @endif>
                        Solteiro(a)
                    </option>
                    <option value="casado" @if( isset( $interno ) && $interno->estado_civil == "casado" ) selected @endif>
                        Casado(a)
                    </option>
                    <option value="uniao estavel" @if( isset( $interno ) && $interno->estado_civil == "uniao estavel" ) selected @endif>
                        União Estável
                    </option>
                    <option value="separado" @if( isset( $interno ) && $interno->estado_civil == "separado" ) selected @endif>
                        Separado(a)
                    </option>
                    <option value="divorciado" @if( isset( $interno ) && $interno->estado_civil == "divorciado" ) selected @endif>
                        Divorciado(a)
                    </option>
                    <option value="viuvo" @if( isset( $interno ) && $interno->estado_civil == "viuvo" ) selected @endif>
                        Viúvo(a)
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="naturalidade_pais">Naturalidade - País *</label>
                <select id="naturalidade_pais" name="naturalidade_pais" class="form-control" required>
                    <option value="" @if( isset( $interno ) && $interno->naturalidade_pais == "" ) selected @endif>Selecione</option>
                    <option value="África do Sul" @if( isset( $interno ) && $interno->naturalidade_pais == "África do Sul" ) selected @endif>África do Sul</option>
                    <option value="Albânia" @if( isset( $interno ) && $interno->naturalidade_pais == "Albânia" ) selected @endif>Albânia</option>
                    <option value="Alemanha" @if( isset( $interno ) && $interno->naturalidade_pais == "Alemanha" ) selected @endif>Alemanha</option>
                    <option value="Andorra" @if( isset( $interno ) && $interno->naturalidade_pais == "Andorra" ) selected @endif>Andorra</option>
                    <option value="Angola" @if( isset( $interno ) && $interno->naturalidade_pais == "Angola" ) selected @endif>Angola</option>
                    <option value="Anguilla" @if( isset( $interno ) && $interno->naturalidade_pais == "Anguilla" ) selected @endif>Anguilla</option>
                    <option value="Antigua" @if( isset( $interno ) && $interno->naturalidade_pais == "Antigua" ) selected @endif>Antigua</option>
                    <option value="Arábia Saudita" @if( isset( $interno ) && $interno->naturalidade_pais == "Arábia Saudita" ) selected @endif>Arábia Saudita</option>
                    <option value="Argentina" @if( isset( $interno ) && $interno->naturalidade_pais == "Argentina" ) selected @endif>Argentina</option>
                    <option value="Armênia" @if( isset( $interno ) && $interno->naturalidade_pais == "Armênia" ) selected @endif>Armênia</option>
                    <option value="Aruba" @if( isset( $interno ) && $interno->naturalidade_pais == "Aruba" ) selected @endif>Aruba</option>
                    <option value="Austrália" @if( isset( $interno ) && $interno->naturalidade_pais == "Austrália" ) selected @endif>Austrália</option>
                    <option value="Áustria" @if( isset( $interno ) && $interno->naturalidade_pais == "Áustria" ) selected @endif>Áustria</option>
                    <option value="Azerbaijão" @if( isset( $interno ) && $interno->naturalidade_pais == "Azerbaijão" ) selected @endif>Azerbaijão</option>
                    <option value="Bahamas" @if( isset( $interno ) && $interno->naturalidade_pais == "Bahamas" ) selected @endif>Bahamas</option>
                    <option value="Bahrein" @if( isset( $interno ) && $interno->naturalidade_pais == "Bahrein" ) selected @endif>Bahrein</option>
                    <option value="Bangladesh" @if( isset( $interno ) && $interno->naturalidade_pais == "Bangladesh" ) selected @endif>Bangladesh</option>
                    <option value="Barbados" @if( isset( $interno ) && $interno->naturalidade_pais == "Barbados" ) selected @endif>Barbados</option>
                    <option value="Bélgica" @if( isset( $interno ) && $interno->naturalidade_pais == "Bélgica" ) selected @endif>Bélgica</option>
                    <option value="Benin" @if( isset( $interno ) && $interno->naturalidade_pais == "Benin" ) selected @endif>Benin</option>
                    <option value="Bermudas" @if( isset( $interno ) && $interno->naturalidade_pais == "Bermudas" ) selected @endif>Bermudas</option>
                    <option value="Botsuana" @if( isset( $interno ) && $interno->naturalidade_pais == "Botsuana" ) selected @endif>Botsuana</option>
                    <option value="Bolívia" @if( isset( $interno ) && $interno->naturalidade_pais == "Bolívia" ) selected @endif>Bolívia</option>
                    <option value="Brasil" @if( isset( $interno ) && $interno->naturalidade_pais == "Brasil" ) selected @endif>Brasil</option>
                    <option value="Brunei" @if( isset( $interno ) && $interno->naturalidade_pais == "Brunei" ) selected @endif>Brunei</option>
                    <option value="Bulgária" @if( isset( $interno ) && $interno->naturalidade_pais == "Bulgária" ) selected @endif>Bulgária</option>
                    <option value="Burkina Fasso" @if( isset( $interno ) && $interno->naturalidade_pais == "Burkina Fasso" ) selected @endif>Burkina Fasso</option>
                    <option value="Butão" @if( isset( $interno ) && $interno->naturalidade_pais == "Butão" ) selected @endif>Butão</option>
                    <option value="Cabo Verde" @if( isset( $interno ) && $interno->naturalidade_pais == "Cabo Verde" ) selected @endif>Cabo Verde</option>
                    <option value="Camarões" @if( isset( $interno ) && $interno->naturalidade_pais == "Camarões" ) selected @endif>Camarões</option>
                    <option value="Camboja" @if( isset( $interno ) && $interno->naturalidade_pais == "Camboja" ) selected @endif>Camboja</option>
                    <option value="Canadá" @if( isset( $interno ) && $interno->naturalidade_pais == "Canadá" ) selected @endif>Canadá</option>
                    <option value="Cazaquistão" @if( isset( $interno ) && $interno->naturalidade_pais == "Cazaquistão" ) selected @endif>Cazaquistão</option>
                    <option value="Chade" @if( isset( $interno ) && $interno->naturalidade_pais == "Chade" ) selected @endif>Chade</option>
                    <option value="Chile" @if( isset( $interno ) && $interno->naturalidade_pais == "Chile" ) selected @endif>Chile</option>
                    <option value="China" @if( isset( $interno ) && $interno->naturalidade_pais == "China" ) selected @endif>China</option>
                    <option value="Cidade do Vaticano" @if( isset( $interno ) && $interno->naturalidade_pais == "Cidade do Vaticano" ) selected @endif>Cidade do Vaticano</option>
                    <option value="Colômbia" @if( isset( $interno ) && $interno->naturalidade_pais == "Colômbia" ) selected @endif>Colômbia</option>
                    <option value="Congo" @if( isset( $interno ) && $interno->naturalidade_pais == "Congo" ) selected @endif>Congo</option>
                    <option value="Coréia do Sul" @if( isset( $interno ) && $interno->naturalidade_pais == "Coréia do Sul" ) selected @endif>Coréia do Sul</option>
                    <option value="Costa do Marfim" @if( isset( $interno ) && $interno->naturalidade_pais == "Costa do Marfim" ) selected @endif>Costa do Marfim</option>
                    <option value="Costa Rica" @if( isset( $interno ) && $interno->naturalidade_pais == "Costa Rica" ) selected @endif>Costa Rica</option>
                    <option value="Croácia" @if( isset( $interno ) && $interno->naturalidade_pais == "Croácia" ) selected @endif>Croácia</option>
                    <option value="Dinamarca" @if( isset( $interno ) && $interno->naturalidade_pais == "Dinamarca" ) selected @endif>Dinamarca</option>
                    <option value="Djibuti" @if( isset( $interno ) && $interno->naturalidade_pais == "Djibuti" ) selected @endif>Djibuti</option>
                    <option value="Dominica" @if( isset( $interno ) && $interno->naturalidade_pais == "Dominica" ) selected @endif>Dominica</option>
                    <option value="EUA" @if( isset( $interno ) && $interno->naturalidade_pais == "EUA" ) selected @endif>EUA</option>
                    <option value="Egito" @if( isset( $interno ) && $interno->naturalidade_pais == "Egito" ) selected @endif>Egito</option>
                    <option value="El Salvador" @if( isset( $interno ) && $interno->naturalidade_pais == "El Salvador" ) selected @endif>El Salvador</option>
                    <option value="Emirados Árabes" @if( isset( $interno ) && $interno->naturalidade_pais == "Emirados Árabes" ) selected @endif>Emirados Árabes</option>
                    <option value="Equador" @if( isset( $interno ) && $interno->naturalidade_pais == "Equador" ) selected @endif>Equador</option>
                    <option value="Eritréia" @if( isset( $interno ) && $interno->naturalidade_pais == "Eritréia" ) selected @endif>Eritréia</option>
                    <option value="Escócia" @if( isset( $interno ) && $interno->naturalidade_pais == "Escócia" ) selected @endif>Escócia</option>
                    <option value="Eslováquia" @if( isset( $interno ) && $interno->naturalidade_pais == "Eslováquia" ) selected @endif>Eslováquia</option>
                    <option value="Eslovênia" @if( isset( $interno ) && $interno->naturalidade_pais == "Eslovênia" ) selected @endif>Eslovênia</option>
                    <option value="Espanha" @if( isset( $interno ) && $interno->naturalidade_pais == "Espanha" ) selected @endif>Espanha</option>
                    <option value="Estônia" @if( isset( $interno ) && $interno->naturalidade_pais == "Estônia" ) selected @endif>Estônia</option>
                    <option value="Etiópia" @if( isset( $interno ) && $interno->naturalidade_pais == "Etiópia" ) selected @endif>Etiópia</option>
                    <option value="Fiji" @if( isset( $interno ) && $interno->naturalidade_pais == "Fiji" ) selected @endif>Fiji</option>
                    <option value="Filipinas" @if( isset( $interno ) && $interno->naturalidade_pais == "Filipinas" ) selected @endif>Filipinas</option>
                    <option value="Finlândia" @if( isset( $interno ) && $interno->naturalidade_pais == "Finlândia" ) selected @endif>Finlândia</option>
                    <option value="França" @if( isset( $interno ) && $interno->naturalidade_pais == "França" ) selected @endif>França</option>
                    <option value="Gabão" @if( isset( $interno ) && $interno->naturalidade_pais == "Gabão" ) selected @endif>Gabão</option>
                    <option value="Gâmbia" @if( isset( $interno ) && $interno->naturalidade_pais == "Gâmbia" ) selected @endif>Gâmbia</option>
                    <option value="Gana" @if( isset( $interno ) && $interno->naturalidade_pais == "Gana" ) selected @endif>Gana</option>
                    <option value="Geórgia" @if( isset( $interno ) && $interno->naturalidade_pais == "Geórgia" ) selected @endif>Geórgia</option>
                    <option value="Gibraltar" @if( isset( $interno ) && $interno->naturalidade_pais == "Gibraltar" ) selected @endif>Gibraltar</option>
                    <option value="Granada" @if( isset( $interno ) && $interno->naturalidade_pais == "Granada" ) selected @endif>Granada</option>
                    <option value="Grécia" @if( isset( $interno ) && $interno->naturalidade_pais == "Grécia" ) selected @endif>Grécia</option>
                    <option value="Guadalupe" @if( isset( $interno ) && $interno->naturalidade_pais == "Guadalupe" ) selected @endif>Guadalupe</option>
                    <option value="Guam" @if( isset( $interno ) && $interno->naturalidade_pais == "Guam" ) selected @endif>Guam</option>
                    <option value="Guatemala" @if( isset( $interno ) && $interno->naturalidade_pais == "Guatemala" ) selected @endif>Guatemala</option>
                    <option value="Guiana" @if( isset( $interno ) && $interno->naturalidade_pais == "Guiana" ) selected @endif>Guiana</option>
                    <option value="Guiana Francesa" @if( isset( $interno ) && $interno->naturalidade_pais == "Guiana Francesa" ) selected @endif>Guiana Francesa</option>
                    <option value="Guiné-bissau" @if( isset( $interno ) && $interno->naturalidade_pais == "Guiné-bissau" ) selected @endif>Guiné-bissau</option>
                    <option value="Haiti" @if( isset( $interno ) && $interno->naturalidade_pais == "Haiti" ) selected @endif>Haiti</option>
                    <option value="Holanda" @if( isset( $interno ) && $interno->naturalidade_pais == "Holanda" ) selected @endif>Holanda</option>
                    <option value="Honduras" @if( isset( $interno ) && $interno->naturalidade_pais == "Honduras" ) selected @endif>Honduras</option>
                    <option value="Hong Kong" @if( isset( $interno ) && $interno->naturalidade_pais == "Hong Kong" ) selected @endif>Hong Kong</option>
                    <option value="Hungria" @if( isset( $interno ) && $interno->naturalidade_pais == "Hungria" ) selected @endif>Hungria</option>
                    <option value="Iêmen" @if( isset( $interno ) && $interno->naturalidade_pais == "Iêmen" ) selected @endif>Iêmen</option>
                    <option value="Ilhas Cayman" @if( isset( $interno ) && $interno->naturalidade_pais == "Ilhas Cayman" ) selected @endif>Ilhas Cayman</option>
                    <option value="Ilhas Cook" @if( isset( $interno ) && $interno->naturalidade_pais == "Ilhas Cook" ) selected @endif>Ilhas Cook</option>
                    <option value="Ilhas Curaçao" @if( isset( $interno ) && $interno->naturalidade_pais == "Ilhas Curaçao" ) selected @endif>Ilhas Curaçao</option>
                    <option value="Ilhas Marshall" @if( isset( $interno ) && $interno->naturalidade_pais == "Ilhas Marshall" ) selected @endif>Ilhas Marshall</option>
                    <option value="Ilhas Turks & Caicos" @if( isset( $interno ) && $interno->naturalidade_pais == "Ilhas Turks & Caicos" ) selected @endif>Ilhas Turks & Caicos</option>
                    <option value="Ilhas Virgens (brit.)" @if( isset( $interno ) && $interno->naturalidade_pais == "Ilhas Virgens (brit.)" ) selected @endif>Ilhas Virgens (brit.)</option>
                    <option value="Ilhas Virgens(amer.)" @if( isset( $interno ) && $interno->naturalidade_pais == "Ilhas Virgens(amer.)" ) selected @endif>Ilhas Virgens(amer.)</option>
                    <option value="Ilhas Wallis e Futuna" @if( isset( $interno ) && $interno->naturalidade_pais == "Ilhas Wallis e Futuna" ) selected @endif>Ilhas Wallis e Futuna</option>
                    <option value="Ilhas do Pacífico (EUA)" @if( isset( $interno ) && $interno->naturalidade_pais == "Ilhas do Pacífico (EUA)" ) selected @endif>Ilhas do Pacífico (EUA)</option>
                    <option value="Índia" @if( isset( $interno ) && $interno->naturalidade_pais == "Índia" ) selected @endif>Índia</option>
                    <option value="Indonésia" @if( isset( $interno ) && $interno->naturalidade_pais == "Indonésia" ) selected @endif>Indonésia</option>
                    <option value="Inglaterra" @if( isset( $interno ) && $interno->naturalidade_pais == "Inglaterra" ) selected @endif>Inglaterra</option>
                    <option value="Irlanda" @if( isset( $interno ) && $interno->naturalidade_pais == "Irlanda" ) selected @endif>Irlanda</option>
                    <option value="Islândia" @if( isset( $interno ) && $interno->naturalidade_pais == "Islândia" ) selected @endif>Islândia</option>
                    <option value="Israel" @if( isset( $interno ) && $interno->naturalidade_pais == "Israel" ) selected @endif>Israel</option>
                    <option value="Itália" @if( isset( $interno ) && $interno->naturalidade_pais == "Itália" ) selected @endif>Itália</option>
                    <option value="Jamaica" @if( isset( $interno ) && $interno->naturalidade_pais == "Jamaica" ) selected @endif>Jamaica</option>
                    <option value="Japão" @if( isset( $interno ) && $interno->naturalidade_pais == "Japão" ) selected @endif>Japão</option>
                    <option value="Jordânia" @if( isset( $interno ) && $interno->naturalidade_pais == "Jordânia" ) selected @endif>Jordânia</option>
                    <option value="Kuwait" @if( isset( $interno ) && $interno->naturalidade_pais == "Kuwait" ) selected @endif>Kuwait</option>
                    <option value="Latvia" @if( isset( $interno ) && $interno->naturalidade_pais == "Latvia" ) selected @endif>Latvia</option>
                    <option value="Líbano" @if( isset( $interno ) && $interno->naturalidade_pais == "Líbano" ) selected @endif>Líbano</option>
                    <option value="Liechtenstein" @if( isset( $interno ) && $interno->naturalidade_pais == "Liechtenstein" ) selected @endif>Liechtenstein</option>
                    <option value="Lituânia" @if( isset( $interno ) && $interno->naturalidade_pais == "Lituânia" ) selected @endif>Lituânia</option>
                    <option value="Luxemburgo" @if( isset( $interno ) && $interno->naturalidade_pais == "Luxemburgo" ) selected @endif>Luxemburgo</option>
                    <option value="Macau" @if( isset( $interno ) && $interno->naturalidade_pais == "Macau" ) selected @endif>Macau</option>
                    <option value="Macedônia" @if( isset( $interno ) && $interno->naturalidade_pais == "Macedônia" ) selected @endif>Macedônia</option>
                    <option value="Madagascar" @if( isset( $interno ) && $interno->naturalidade_pais == "Madagascar" ) selected @endif>Madagascar</option>
                    <option value="Malásia" @if( isset( $interno ) && $interno->naturalidade_pais == "Malásia" ) selected @endif>Malásia</option>
                    <option value="Malaui" @if( isset( $interno ) && $interno->naturalidade_pais == "Malaui" ) selected @endif>Malaui</option>
                    <option value="Mali" @if( isset( $interno ) && $interno->naturalidade_pais == "Mali" ) selected @endif>Mali</option>
                    <option value="Malta" @if( isset( $interno ) && $interno->naturalidade_pais == "Malta" ) selected @endif>Malta</option>
                    <option value="Marrocos" @if( isset( $interno ) && $interno->naturalidade_pais == "Marrocos" ) selected @endif>Marrocos</option>
                    <option value="Martinica" @if( isset( $interno ) && $interno->naturalidade_pais == "Martinica" ) selected @endif>Martinica</option>
                    <option value="Mauritânia" @if( isset( $interno ) && $interno->naturalidade_pais == "Mauritânia" ) selected @endif>Mauritânia</option>
                    <option value="Mauritius" @if( isset( $interno ) && $interno->naturalidade_pais == "Mauritius" ) selected @endif>Mauritius</option>
                    <option value="México" @if( isset( $interno ) && $interno->naturalidade_pais == "México" ) selected @endif>México</option>
                    <option value="Moldova" @if( isset( $interno ) && $interno->naturalidade_pais == "Moldova" ) selected @endif>Moldova</option>
                    <option value="Mônaco" @if( isset( $interno ) && $interno->naturalidade_pais == "Mônaco" ) selected @endif>Mônaco</option>
                    <option value="Montserrat" @if( isset( $interno ) && $interno->naturalidade_pais == "Montserrat" ) selected @endif>Montserrat</option>
                    <option value="Nepal" @if( isset( $interno ) && $interno->naturalidade_pais == "Nepal" ) selected @endif>Nepal</option>
                    <option value="Nicarágua" @if( isset( $interno ) && $interno->naturalidade_pais == "Nicarágua" ) selected @endif>Nicarágua</option>
                    <option value="Niger" @if( isset( $interno ) && $interno->naturalidade_pais == "Niger" ) selected @endif>Niger</option>
                    <option value="Nigéria" @if( isset( $interno ) && $interno->naturalidade_pais == "Nigéria" ) selected @endif>Nigéria</option>
                    <option value="Noruega" @if( isset( $interno ) && $interno->naturalidade_pais == "Noruega" ) selected @endif>Noruega</option>
                    <option value="Nova Caledônia" @if( isset( $interno ) && $interno->naturalidade_pais == "Nova Caledônia" ) selected @endif>Nova Caledônia</option>
                    <option value="Nova Zelândia" @if( isset( $interno ) && $interno->naturalidade_pais == "Nova Zelândia" ) selected @endif>Nova Zelândia</option>
                    <option value="Omã" @if( isset( $interno ) && $interno->naturalidade_pais == "Omã" ) selected @endif>Omã</option>
                    <option value="Palau" @if( isset( $interno ) && $interno->naturalidade_pais == "Palau" ) selected @endif>Palau</option>
                    <option value="Panamá" @if( isset( $interno ) && $interno->naturalidade_pais == "Panamá" ) selected @endif>Panamá</option>
                    <option value="Papua-nova Guiné" @if( isset( $interno ) && $interno->naturalidade_pais == "Papua-nova Guiné" ) selected @endif>Papua-nova Guiné</option>
                    <option value="Paquistão" @if( isset( $interno ) && $interno->naturalidade_pais == "Paquistão" ) selected @endif>Paquistão</option>
                    <option value="Peru" @if( isset( $interno ) && $interno->naturalidade_pais == "Peru" ) selected @endif>Peru</option>
                    <option value="Polinésia Francesa" @if( isset( $interno ) && $interno->naturalidade_pais == "Polinésia Francesa" ) selected @endif>Polinésia Francesa</option>
                    <option value="Polônia" @if( isset( $interno ) && $interno->naturalidade_pais == "Polônia" ) selected @endif>Polônia</option>
                    <option value="Porto Rico" @if( isset( $interno ) && $interno->naturalidade_pais == "Porto Rico" ) selected @endif>Porto Rico</option>
                    <option value="Portugal" @if( isset( $interno ) && $interno->naturalidade_pais == "Portugal" ) selected @endif>Portugal</option>
                    <option value="Qatar" @if( isset( $interno ) && $interno->naturalidade_pais == "Qatar" ) selected @endif>Qatar</option>
                    <option value="Quênia" @if( isset( $interno ) && $interno->naturalidade_pais == "Quênia" ) selected @endif>Quênia</option>
                    <option value="Rep. Dominicana" @if( isset( $interno ) && $interno->naturalidade_pais == "Rep. Dominicana" ) selected @endif>Rep. Dominicana</option>
                    <option value="Rep. Tcheca" @if( isset( $interno ) && $interno->naturalidade_pais == "Rep. Tcheca" ) selected @endif>Rep. Tcheca</option>
                    <option value="Reunion" @if( isset( $interno ) && $interno->naturalidade_pais == "Reunion" ) selected @endif>Reunion</option>
                    <option value="Romênia" @if( isset( $interno ) && $interno->naturalidade_pais == "Romênia" ) selected @endif>Romênia</option>
                    <option value="Ruanda" @if( isset( $interno ) && $interno->naturalidade_pais == "Ruanda" ) selected @endif>Ruanda</option>
                    <option value="Rússia" @if( isset( $interno ) && $interno->naturalidade_pais == "Rússia" ) selected @endif>Rússia</option>
                    <option value="Saipan" @if( isset( $interno ) && $interno->naturalidade_pais == "Saipan" ) selected @endif>Saipan</option>
                    <option value="Samoa Americana" @if( isset( $interno ) && $interno->naturalidade_pais == "Samoa Americana" ) selected @endif>Samoa Americana</option>
                    <option value="Senegal" @if( isset( $interno ) && $interno->naturalidade_pais == "Senegal" ) selected @endif>Senegal</option>
                    <option value="Serra Leone" @if( isset( $interno ) && $interno->naturalidade_pais == "Serra Leone" ) selected @endif>Serra Leone</option>
                    <option value="Seychelles" @if( isset( $interno ) && $interno->naturalidade_pais == "Seychelles" ) selected @endif>Seychelles</option>
                    <option value="Singapura" @if( isset( $interno ) && $interno->naturalidade_pais == "Singapura" ) selected @endif>Singapura</option>
                    <option value="Síria" @if( isset( $interno ) && $interno->naturalidade_pais == "Síria" ) selected @endif>Síria</option>
                    <option value="Sri Lanka" @if( isset( $interno ) && $interno->naturalidade_pais == "Sri Lanka" ) selected @endif>Sri Lanka</option>
                    <option value="St. Kitts & Nevis" @if( isset( $interno ) && $interno->naturalidade_pais == "St. Kitts & Nevis" ) selected @endif>St. Kitts & Nevis</option>
                    <option value="St. Lúcia" @if( isset( $interno ) && $interno->naturalidade_pais == "St. Lúcia" ) selected @endif>St. Lúcia</option>
                    <option value="St. Vincent" @if( isset( $interno ) && $interno->naturalidade_pais == "St. Vincent" ) selected @endif>St. Vincent</option>
                    <option value="Sudão" @if( isset( $interno ) && $interno->naturalidade_pais == "Sudão" ) selected @endif>Sudão</option>
                    <option value="Suécia" @if( isset( $interno ) && $interno->naturalidade_pais == "Suécia" ) selected @endif>Suécia</option>
                    <option value="Suíça" @if( isset( $interno ) && $interno->naturalidade_pais == "Suíça" ) selected @endif>Suíça</option>
                    <option value="Suriname" @if( isset( $interno ) && $interno->naturalidade_pais == "Suriname" ) selected @endif>Suriname</option>
                    <option value="Tailândia" @if( isset( $interno ) && $interno->naturalidade_pais == "Tailândia" ) selected @endif>Tailândia</option>
                    <option value="Taiwan" @if( isset( $interno ) && $interno->naturalidade_pais == "Taiwan" ) selected @endif>Taiwan</option>
                    <option value="Tanzânia" @if( isset( $interno ) && $interno->naturalidade_pais == "Tanzânia" ) selected @endif>Tanzânia</option>
                    <option value="Togo" @if( isset( $interno ) && $interno->naturalidade_pais == "Togo" ) selected @endif>Togo</option>
                    <option value="Trinidad & Tobago" @if( isset( $interno ) && $interno->naturalidade_pais == "Trinidad & Tobago" ) selected @endif>Trinidad & Tobago</option>
                    <option value="Tunísia" @if( isset( $interno ) && $interno->naturalidade_pais == "Tunísia" ) selected @endif>Tunísia</option>
                    <option value="Turquia" @if( isset( $interno ) && $interno->naturalidade_pais == "Turquia" ) selected @endif>Turquia</option>
                    <option value="Ucrânia" @if( isset( $interno ) && $interno->naturalidade_pais == "Ucrânia" ) selected @endif>Ucrânia</option>
                    <option value="Uganda" @if( isset( $interno ) && $interno->naturalidade_pais == "Uganda" ) selected @endif>Uganda</option>
                    <option value="Uruguai" @if( isset( $interno ) && $interno->naturalidade_pais == "Uruguai" ) selected @endif>Uruguai</option>
                    <option value="Venezuela" @if( isset( $interno ) && $interno->naturalidade_pais == "Venezuela" ) selected @endif>Venezuela</option>
                    <option value="Vietnã" @if( isset( $interno ) && $interno->naturalidade_pais == "Vietnã" ) selected @endif>Vietnã</option>
                    <option value="Zaire" @if( isset( $interno ) && $interno->naturalidade_pais == "Zaire" ) selected @endif>Zaire</option>
                    <option value="Zâmbia" @if( isset( $interno ) && $interno->naturalidade_pais == "Zâmbia" ) selected @endif>Zâmbia</option>
                    <option value="Zimbábue" @if( isset( $interno ) && $interno->naturalidade_pais == "Zimbábue" ) selected @endif>Zimbábue</option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divNaturalidadeEstado">
                <label for="naturalidade_estado">Estado Naturalidade</label>
                <select id="naturalidade_estado" name="naturalidade_estado" class="form-control selectEstados">
                    <option value="" @if( isset( $interno ) && $interno->naturalidade_estado == "" ) selected @endif>
                        Selecione
                    </option>
                    <option value="AC" @if( isset( $interno ) && $interno->naturalidade_estado == "AC" ) selected @endif>
                        Acre
                    </option>
                    <option value="AL" @if( isset( $interno ) && $interno->naturalidade_estado == "AL" ) selected @endif>
                        Alagoas
                    </option>
                    <option value="AP" @if( isset( $interno ) && $interno->naturalidade_estado == "AP" ) selected @endif>
                        Amapá
                    </option>
                    <option value="AM" @if( isset( $interno ) && $interno->naturalidade_estado == "AM" ) selected @endif>
                        Amazonas
                    </option>
                    <option value="BA" @if( isset( $interno ) && $interno->naturalidade_estado == "BA" ) selected @endif>
                        Bahia
                    </option>
                    <option value="CE" @if( isset( $interno ) && $interno->naturalidade_estado == "CE" ) selected @endif>
                        Ceará
                    </option>
                    <option value="DF" @if( isset( $interno ) && $interno->naturalidade_estado == "DF" ) selected @endif>
                        Distrito Federal
                    </option>
                    <option value="ES" @if( isset( $interno ) && $interno->naturalidade_estado == "ES" ) selected @endif>
                        Espírito Santo
                    </option>
                    <option value="GO" @if( isset( $interno ) && $interno->naturalidade_estado == "GO" ) selected @endif>
                        Goiás
                    </option>
                    <option value="MA" @if( isset( $interno ) && $interno->naturalidade_estado == "MA" ) selected @endif>
                        Maranhão
                    </option>
                    <option value="MT" @if( isset( $interno ) && $interno->naturalidade_estado == "MT" ) selected @endif>
                        Mato Grosso
                    </option>
                    <option value="MS" @if( isset( $interno ) && $interno->naturalidade_estado == "MS" ) selected @endif>
                        Mato Grosso do Sul
                    </option>
                    <option value="MG" @if( isset( $interno ) && $interno->naturalidade_estado == "MG" ) selected @endif>
                        Minas Gerais
                    </option>
                    <option value="PA" @if( isset( $interno ) && $interno->naturalidade_estado == "PA" ) selected @endif>
                        Pará
                    </option>
                    <option value="PB" @if( isset( $interno ) && $interno->naturalidade_estado == "PB" ) selected @endif>
                        Paraíba
                    </option>
                    <option value="PR" @if( isset( $interno ) && $interno->naturalidade_estado == "PR" ) selected @endif>
                        Paraná
                    </option>
                    <option value="PE" @if( isset( $interno ) && $interno->naturalidade_estado == "PE" ) selected @endif>
                        Pernambuco
                    </option>
                    <option value="PI" @if( isset( $interno ) && $interno->naturalidade_estado == "PI" ) selected @endif>
                        Piauí
                    </option>
                    <option value="RJ" @if( isset( $interno ) && $interno->naturalidade_estado == "RJ" ) selected @endif>
                        Rio de Janeiro
                    </option>
                    <option value="RN" @if( isset( $interno ) && $interno->naturalidade_estado == "RN" ) selected @endif>
                        Rio Grande do Norte
                    </option>
                    <option value="RS" @if( isset( $interno ) && $interno->naturalidade_estado == "RS" ) selected @endif>
                        Rio Grande do Sul
                    </option>
                    <option value="RO" @if( isset( $interno ) && $interno->naturalidade_estado == "RO" ) selected @endif>
                        Rondônia
                    </option>
                    <option value="RR" @if( isset( $interno ) && $interno->naturalidade_estado == "RR" ) selected @endif>
                        Roraima
                    </option>
                    <option value="SC" @if( isset( $interno ) && $interno->naturalidade_estado == "SC" ) selected @endif>
                        Santa Catarina
                    </option>
                    <option value="SP" @if( isset( $interno ) && $interno->naturalidade_estado == "SP" ) selected @endif>
                        São Paulo
                    </option>
                    <option value="SE" @if( isset( $interno ) && $interno->naturalidade_estado == "SE" ) selected @endif>
                        Sergipe
                    </option>
                    <option value="TO" @if( isset( $interno ) && $interno->naturalidade_estado == "TO" ) selected @endif>
                        Tocantins
                    </option>
                </select>
            </div>
            <div class="form-group col-sm-3" id="divNaturalidadeCidade">
                <label for="naturalidade_cidade">Cidade Naturalidade</label>
                <select id="naturalidade_cidade" name="naturalidade_cidade" class="form-control">
                    <option value=""  @if( isset( $interno ) && $interno->naturalidade_cidade == "" ) selected @endif>
                        Selecione
                    </option>
                    @if(isset($interno))
                        @foreach($naturalidadeMunicipio as $municipio)
                            <option value="{{ $municipio->Nome }}"
                                @if( isset( $interno ) && $interno->naturalidade_cidade == $municipio->Nome ) selected @endif>
                                {{ $municipio->Nome }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>


            <div class="form-group col-sm-3">
                <label for="escolaridade">Escolaridade *</label>
                <select id="escolaridade" name="escolaridade" class="form-control" required>
                    <option value="" @if( isset( $interno ) && $interno->escolaridade == "" ) selected @endif>
                        Selecione
                    </option>
                    <option value="Analfabeto" @if( isset( $interno ) && $interno->escolaridade == "Analfabeto" ) selected @endif>
                        Analfabeto
                    </option>
                    <option value="Alfabetizado" @if( isset( $interno ) && $interno->escolaridade == "Alfabetizado" ) selected @endif>
                        Alfabetizado
                    </option>
                    <option value="Ensino Fundamental Incompleto" @if( isset( $interno ) && $interno->escolaridade == "Ensino Fundamental Incompleto" ) selected @endif>
                        Ensino Fundamental Incompleto
                    </option>
                    <option value="Ensino Fundamental Completo" @if( isset( $interno ) && $interno->escolaridade == "Ensino Fundamental Completo" ) selected @endif>
                        Ensino Fundamental Completo
                    </option>
                    <option value="Ensino Médio Incompleto" @if( isset( $interno ) && $interno->escolaridade == "Ensino Médio Incompleto" ) selected @endif>
                        Ensino Médio Incompleto
                    </option>
                    <option value="Ensino Médio Completo" @if( isset( $interno ) && $interno->escolaridade == "Ensino Médio Completo" ) selected @endif>
                        Ensino Médio Completo
                    </option>
                    <option value="Ensino Superior Incompleto" @if( isset( $interno ) && $interno->escolaridade == "Ensino Superior Incompleto" ) selected @endif>
                        Ensino Superior Incompleto
                    </option>
                    <option value="Ensino Superior Completo" @if( isset( $interno ) && $interno->escolaridade == "Ensino Superior Completo" ) selected @endif>
                        Ensino Superior Completo
                    </option>
                    <option value="Pós-graduação lato sensu" @if( isset( $interno ) && $interno->escolaridade == "Pós-graduação lato sensu" ) selected @endif>
                        Pós-graduação lato sensu
                    </option>
                    <option value="Mestrado" @if( isset( $interno ) && $interno->escolaridade == "Mestrado" ) selected @endif>
                        Mestrado
                    </option>
                    <option value="Doutorado" @if( isset( $interno ) && $interno->escolaridade == "Doutorado" ) selected @endif>
                        Doutorado
                    </option>
                    <option value="Pós-Doutorado" @if( isset( $interno ) && $interno->escolaridade == "Pós-Doutorado" ) selected @endif>
                        Pós-Doutorado
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="profissao">Profissão</label>
                <select id="profissao" name="profissao" class="form-control">
                    <option value="" @if( isset( $interno ) && $interno->profissao == "" ) selected @endif>
                        Selecione
                    </option>
                    @foreach($profissao as $prof)
                        <option value="{{ $prof->id }}"  @if( isset( $interno ) && $interno->profissao == $prof->id ) selected @endif>
                            {{ $prof->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="opcao_sexual">Orientação Sexual *</label>
                <select id="opcao_sexual" name="opcao_sexual" class="form-control" required>
                    <option value="" @if( isset( $interno ) && $interno->opcao_sexual == "" ) selected @endif>
                        Selecione
                    </option>
                    <option value="heterossexual" @if( isset( $interno ) && $interno->opcao_sexual == "heterossexual" ) selected @endif>
                        Heterossexual
                    </option>
                    <option value="homossexual" @if( isset( $interno ) && $interno->opcao_sexual == "homossexual" ) selected @endif>
                        Homossexual
                    </option>
                    <option value="bissexual" @if( isset( $interno ) && $interno->opcao_sexual == "bissexual" ) selected @endif>
                        Bissexual
                    </option>
                    <option value="nao declarado" @if( isset( $interno ) && $interno->opcao_sexual == "nao declarado" ) selected @endif>
                        Não Declarado
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="identidade_genero">Identidade de Gênero</label>
                <select id="identidade_genero" name="identidade_genero" class="form-control">
                    <option value="" @if( isset( $interno ) && $interno->identidade_genero == "" ) selected @endif>
                        Selecione
                    </option>
                    <option value="Travesti" @if( isset( $interno ) && $interno->identidade_genero == "Travesti" ) selected @endif>
                        Travesti
                    </option>
                    <option value="Transexual" @if( isset( $interno ) && $interno->identidade_genero == "Transexual" ) selected @endif>
                        Transexual
                    </option>
                    <option value="Intersexual" @if( isset( $interno ) && $interno->identidade_genero == "Intersexual" ) selected @endif>
                        Intersexual
                    </option>
                    <option value="Cisgênero" @if( isset( $interno ) && $interno->identidade_genero == "Cisgênero" ) selected @endif>
                        Cisgênero
                    </option>
                </select>
            </div>
            
            <div class="form-group col-sm-3">
                <label for="religiao">Religião *</label>
                <select id="religiao" name="religiao" class="form-control" required>
                    <option value="" @if( isset( $interno ) && $interno->religiao == "" ) selected @endif>
                        Selecione
                    </option>
                    <option value="Adventista" @if( isset( $interno ) && $interno->religiao == "Adventista" ) selected @endif>
                        Adventista
                    </option>
                    <option value="Agnóstico" @if( isset( $interno ) && $interno->religiao == "Agnóstico" ) selected @endif>
                        Agnóstico
                    </option>
                    <option value="Ateu" @if( isset( $interno ) && $interno->religiao == "Ateu" ) selected @endif>
                        Ateu
                    </option>
                    <option value="Budista" @if( isset( $interno ) && $interno->religiao == "Budista" ) selected @endif>
                        Budista
                    </option>
                    <option value="Católica" @if( isset( $interno ) && $interno->religiao == "Católica" ) selected @endif>
                        Católica
                    </option>
                    <option value="Espírita" @if( isset( $interno ) && $interno->religiao == "Espírita" ) selected @endif>
                        Espírita
                    </option>
                    <option value="Evangélica" @if( isset( $interno ) && $interno->religiao == "Evangélica" ) selected @endif>
                        Evangélica
                    </option>
                    <option value="Judeu" @if( isset( $interno ) && $interno->religiao == "Judeu" ) selected @endif>
                        Judeu
                    </option>
                    <option value="Matriz Africana" @if( isset( $interno ) && $interno->religiao == "Matriz Africana" ) selected @endif>
                        Matriz Africana
                    </option>
                    <option value="Mórmon" @if( isset( $interno ) && $interno->religiao == "Mórmon" ) selected @endif>
                        Mórmon
                    </option>
                    <option value="Muçulmana" @if( isset( $interno ) && $interno->religiao == "Muçulmana" ) selected @endif>
                        Muçulmana
                    </option>
                    <option value="Protestante" @if( isset( $interno ) && $interno->religiao == "Protestante" ) selected @endif>
                        Protestante
                    </option>
                    <option value="Sem Religião" @if( isset( $interno ) && $interno->religiao == "Sem Religião" ) selected @endif>
                        Sem Religião
                    </option>
                    <option value="Outras" @if( isset( $interno ) && $interno->religiao == "Outras" ) selected @endif>
                        Outras
                    </option>
                </select>
            </div>
            <div class="form-group col-sm-3">
                <label for="passaportenum">Nº do Passaporte</label>
                <input type="text" name="passaportenum" value="{{ $interno->passaportenum or old('passaportenum') }}" maxlength="50" class="form-control" id="passaportenum" placeholder="Número do Passaporte">
            </div>
            <div class="form-group col-sm-3">
                <label for="cartaosus">Nº do Cartão SUS</label>
                <input type="text" name="cartaosus" value="{{ $interno->cartaosus or old('cartaosus') }}" maxlength="50" class="form-control" id="cartaosus" placeholder="Número do Cartão SUS">
            </div>
        </div>

    <h5 class="mt-4">Dados Sobre Filhos</h5>
    <div id="divFilhosRemovidos"></div>
    @if(isset($filhos))
    
        @if(count($filhos) == 0)
            <div class="form-row mt-3" id="divFilhos">
                <div class="form-group col-sm-6">
                    <label for="filhoNome">Filho(a) Nome</label>
                    <input type="text" name="filhoNome[]" value="" class="form-control filhoNome" placeholder="Nome do filho(a) se houver">
                </div>

                <div class="form-group col-sm-3">
                    <label for="filhoDataNascimento">Data de Nascimento</label>
                    <input type="text" name="filhoDataNascimento[]" data-mask="00/00/0000" value="" class="form-control" placeholder="Data de nascimento filho(a)">
                </div>

                <div class="form-group col-sm-3">
                    <label for="filhoNumeroCartaoSus">Número do Cartão SUS</label>
                    <input type="text" name="filhoNumeroCartaoSus[]" value="" class="form-control" placeholder="Número do Cartão do SUS do filho(a)">
                </div>

                <div class="form-group col-sm-3">
                    <label for="filhoNaUnidadePrisional">O filho(a) está no Estabelecimento Prisional?</label>
                    <select id="estado_civil" name="filhoNaUnidadePrisional[]" class="form-control">
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
                    <label for="filhoIncapaz">É Incapaz?</label>
                    <select id="estado_civil" name="filhoIncapaz[]" class="form-control">
                        <option value="" @if( isset( $interno ) && $interno->estado_civil == "" ) selected @endif>
                            Selecione
                        </option>
                        <option value="S" @if( isset( $interno ) && $interno->estado_civil == "" ) selected @endif>
                            Sim
                        </option>
                        <option value="N" @if( isset( $interno ) && $interno->estado_civil == "" ) selected @endif>
                            Não
                        </option>
                    </select>
                </div>
                <div class="text-right col-sm-12">
                        <button type="button" class="btn btn-primary addFilhos">Adicionar Novo Filho +</button>
                        <button type="button" class="btn btn-danger btnDelFilhos">Remover Campo -</button>
                </div>

            </div>
        @endif
    
        @foreach($filhos as $filho)
            <input type="hidden" value="{{ $filho->id }}" name="idFilhoCadastrado[]">
            <div class="form-row mt-3" id="divFilhos" data-content="{{ $filho->id }}">
                <div class="form-group col-sm-6">
                    <label for="filhoNome">Filho(a) Nome</label>
                    <input type="text" name="filhoNome[]" value="{{ $filho->nome }}" class="form-control filhoNome" placeholder="Nome do filho(a) se houver">
                </div>

                <div class="form-group col-sm-3">
                    <label for="filhoDataNascimento">Data de Nascimento</label>
                    <input type="text" name="filhoDataNascimento[]" data-mask="00/00/0000" value="{{ $filho->data_nascimento }}" class="form-control" placeholder="Data de nascimento filho(a)">
                </div>

                <div class="form-group col-sm-3">
                    <label for="filhoNumeroCartaoSus">Número do Cartão SUS</label>
                    <input type="text" name="filhoNumeroCartaoSus[]" value="{{ $filho->numero_cartao_sus }}" class="form-control" placeholder="Número do Cartão do SUS do filho(a)">
                </div>

                <div class="form-group col-sm-3">
                    <label for="filhoNaUnidadePrisional">O filho(a) está no Estabelecimento Prisional?</label>
                    <select id="estado_civil" name="filhoNaUnidadePrisional[]" class="form-control">
                        <option value="" @if( isset( $filhos ) && $filho->na_unidade_prisional == "" ) selected @endif>
                            Selecione
                        </option>
                        <option value="S" @if( isset( $filhos ) && $filho->na_unidade_prisional == "S" ) selected @endif>
                            Sim
                        </option>
                        <option value="N" @if( isset( $filhos ) && $filho->na_unidade_prisional == "N" ) selected @endif>
                            Não
                        </option>
                    </select>
                </div>

                <div class="form-group col-sm-3">
                    <label for="filhoIncapaz">É Incapaz?</label>
                    <select id="estado_civil" name="filhoIncapaz[]" class="form-control">
                        <option value="" @if( isset( $filhos ) && $filho->incapaz == "" ) selected @endif>
                            Selecione
                        </option>
                        <option value="S" @if( isset( $filhos ) && $filho->incapaz == "S" ) selected @endif>
                            Sim
                        </option>
                        <option value="N" @if( isset( $filhos ) && $filho->incapaz == "N" ) selected @endif>
                            Não
                        </option>
                    </select>
                </div>
                <div class="text-right col-sm-12">
                        <button type="button" class="btn btn-primary addFilhos">Adicionar Novo Filho +</button>
                        <button type="button" class="btn btn-danger btnDelFilhosCadastrados">Remover Campo -</button>
                </div>

            </div>
        @endforeach
    @endif
    
    @if(!isset($filhos))
    <div class="form-row mt-3" id="divFilhos">
        <div class="form-group col-sm-6">
            <label for="filhoNome">Filho(a) Nome</label>
            <input type="text" name="filhoNome[]" value="" class="form-control filhoNome" placeholder="Nome do filho(a) se houver">
        </div>

        <div class="form-group col-sm-3">
            <label for="filhoDataNascimento">Data de Nascimento</label>
            <input type="text" name="filhoDataNascimento[]" data-mask="00/00/0000" value="" class="form-control" placeholder="Data de nascimento filho(a)">
        </div>

        <div class="form-group col-sm-3">
            <label for="filhoNumeroCartaoSus">Número do Cartão SUS</label>
            <input type="text" name="filhoNumeroCartaoSus[]" value="" class="form-control" placeholder="Número do Cartão do SUS do filho(a)">
        </div>

        <div class="form-group col-sm-3">
            <label for="filhoNaUnidadePrisional">O filho(a) está no Estabelecimento Prisional?</label>
            <select id="estado_civil" name="filhoNaUnidadePrisional[]" class="form-control">
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
            <label for="filhoIncapaz">É Incapaz?</label>
            <select id="estado_civil" name="filhoIncapaz[]" class="form-control">
                <option value="" @if( isset( $interno ) && $interno->estado_civil == "" ) selected @endif>
                    Selecione
                </option>
                <option value="S" @if( isset( $interno ) && $interno->estado_civil == "" ) selected @endif>
                    Sim
                </option>
                <option value="N" @if( isset( $interno ) && $interno->estado_civil == "" ) selected @endif>
                    Não
                </option>
            </select>
        </div>
        <div class="text-right col-sm-12">
                <button type="button" class="btn btn-primary addFilhos">Adicionar Novo Filho +</button>
                <button type="button" class="btn btn-danger btnDelFilhos">Remover Campo -</button>
        </div>

    </div>
    @endif
    <div id="cloneDivFilhos"></div>
    
    <h5 class="mt-4">Dados Sobre Deficiências</h5>
    <div class="form-row mt-3" id="divDeficiencias">
        <div class="form-group col-sm-3">
            <label for="possuiDeficiencia">Possui Alguma Deficiência?</label>
            <select id="possuiDeficiencia" name="possuiDeficiencia" class="form-control" required="">
                <option value="" @if( isset( $interno ) && !is_null($deficiencia) && $deficiencia->deficiente == "" ) selected @endif>
                    Selecione
                </option>
                <option value="S" @if( isset( $interno ) && !is_null($deficiencia) && $deficiencia->deficiente == "S" ) selected @endif>
                    Sim
                </option>
                <option value="N" @if( isset( $interno ) && !is_null($deficiencia) && $deficiencia->deficiente == "N" ) selected @endif>
                    Não
                </option>
            </select>
        </div>
        
        <div class="form-group col-sm-3 displayNone" id="divTipoDeficiencia">
            <label for="tipoDeficiencia">Tipo de Deficiência</label>
            <select id="tipoDeficiencia" name="tipoDeficiencia" class="form-control">
                <option value="" @if( isset( $interno ) && !is_null($deficiencia) && $deficiencia->tipo_deficiencia == "" ) selected @endif>
                    Selecione
                </option>
                <option value="Deficiência Intelectual" @if( isset( $interno ) && !is_null($deficiencia) && $deficiencia->tipo_deficiencia == "Deficiência Intelectual" ) selected @endif>
                    Deficiência Intelectual
                </option>
                <option value="Deficiência Física" @if( isset( $interno ) && !is_null($deficiencia) && $deficiencia->tipo_deficiencia == "Deficiência Física" ) selected @endif>
                    Deficiência Física
                </option>
                <option value="Deficiência Auditiva" @if( isset( $interno ) && !is_null($deficiencia) && $deficiencia->tipo_deficiencia == "Deficiência Auditiva" ) selected @endif>
                    Deficiência Auditiva
                </option>
                <option value="Deficiência Visual" @if( isset( $interno ) && !is_null($deficiencia) && $deficiencia->tipo_deficiencia == "Deficiência Visual" ) selected @endif>
                    Deficiência Visual
                </option>
                <option value="Deficiências Múltiplas" @if( isset( $interno ) && !is_null($deficiencia) && $deficiencia->tipo_deficiencia == "Deficiência Múltiplas" ) selected @endif>
                    Deficiências Múltiplas
                </option>
            </select>
        </div>
        
        <div class="form-group col-sm-3 displayNone" id="divCadeirante">
            <label for="cadeirante">É Cadeirante?</label>
            <select id="cadeirante" name="cadeirante" class="form-control">
                <option value="" @if( isset( $interno ) && !is_null($deficiencia) && $deficiencia->cadeirante == "" ) selected @endif>
                    Selecione
                </option>
                <option value="N" @if( isset( $interno ) && !is_null($deficiencia) && $deficiencia->cadeirante == "N" ) selected @endif>
                    Não
                </option>
                <option value="S" @if( isset( $interno ) && !is_null($deficiencia) && $deficiencia->cadeirante == "S" ) selected @endif>
                    Sim
                </option>
            </select>
        </div>
        
        <div class="form-group col-sm-12 alert-warning p-2 displayNone fontsize-11px" id="textTipoDeficiencia">
            
        </div>
    </div>

    
    <h5 class="mt-4">Endereços</h5>
{{-- Endereco --}}
    {{-- Se for uma alteracao --}}
    @if(isset($endereco))
        {{-- adiciona mais enderecos--}}
        <br>
        <br>
        {{-- os enderecos adicionados sao impressos na div abaixo --}}
        <div id="groupEndereco">
        </div>
    <div id="divEnderecosRemovidos"></div>
    @foreach($endereco as $end)
    <div  data-content="{{ $end->id }}">
        <input type="hidden" value="{{ $end->id }}" name="idEnderecoCadastrado[]">
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
                    <option value="" @if( isset( $interno ) && $end->estado == "" ) selected @endif>
                        Selecione
                    </option>
                    <option value="AC" @if( isset( $interno ) && $end->estado == "AC" ) selected @endif>
                        Acre
                    </option>
                    <option value="AL" @if( isset( $interno ) && $end->estado == "AL" ) selected @endif>
                        Alagoas
                    </option>
                    <option value="AP" @if( isset( $interno ) && $end->estado == "AP" ) selected @endif>
                        Amapá
                    </option>
                    <option value="AM" @if( isset( $interno ) && $end->estado == "AM" ) selected @endif>
                        Amazonas
                    </option>
                    <option value="BA" @if( isset( $interno ) && $end->estado == "BA" ) selected @endif>
                        Bahia
                    </option>
                    <option value="CE" @if( isset( $interno ) && $end->estado == "CE" ) selected @endif>
                        Ceará
                    </option>
                    <option value="DF" @if( isset( $interno ) && $end->estado == "DF" ) selected @endif>
                        Distrito Federal
                    </option>
                    <option value="ES" @if( isset( $interno ) && $end->estado == "ES" ) selected @endif>
                        Espírito Santo
                    </option>
                    <option value="GO" @if( isset( $interno ) && $end->estado == "GO" ) selected @endif>
                        Goiás
                    </option>
                    <option value="MA" @if( isset( $interno ) && $end->estado == "MA" ) selected @endif>
                        Maranhão
                    </option>
                    <option value="MT" @if( isset( $interno ) && $end->estado == "MT" ) selected @endif>
                        Mato Grosso
                    </option>
                    <option value="MS" @if( isset( $interno ) && $end->estado == "MS" ) selected @endif>
                        Mato Grosso do Sul
                    </option>
                    <option value="MG" @if( isset( $interno ) && $end->estado == "MG" ) selected @endif>
                        Minas Gerais
                    </option>
                    <option value="PA" @if( isset( $interno ) && $end->estado == "PA" ) selected @endif>
                        Pará
                    </option>
                    <option value="PB" @if( isset( $interno ) && $end->estado == "PB" ) selected @endif>
                        Paraíba
                    </option>
                    <option value="PR" @if( isset( $interno ) && $end->estado == "PR" ) selected @endif>
                        Paraná
                    </option>
                    <option value="PE" @if( isset( $interno ) && $end->estado == "PE" ) selected @endif>
                        Pernambuco
                    </option>
                    <option value="PI" @if( isset( $interno ) && $end->estado == "PI" ) selected @endif>
                        Piauí
                    </option>
                    <option value="RJ" @if( isset( $interno ) && $end->estado == "RJ" ) selected @endif>
                        Rio de Janeiro
                    </option>
                    <option value="RN" @if( isset( $interno ) && $end->estado == "RN" ) selected @endif>
                        Rio Grande do Norte
                    </option>
                    <option value="RS" @if( isset( $interno ) && $end->estado == "RS" ) selected @endif>
                        Rio Grande do Sul
                    </option>
                    <option value="RO" @if( isset( $interno ) && $end->estado == "RO" ) selected @endif>
                        Rondônia
                    </option>
                    <option value="RR" @if( isset( $interno ) && $end->estado == "RR" ) selected @endif>
                        Roraima
                    </option>
                    <option value="SC" @if( isset( $interno ) && $end->estado == "SC" ) selected @endif>
                        Santa Catarina
                    </option>
                    <option value="SP" @if( isset( $interno ) && $end->estado == "SP" ) selected @endif>
                        São Paulo
                    </option>
                    <option value="SE" @if( isset( $interno ) && $end->estado == "SE" ) selected @endif>
                        Sergipe
                    </option>
                    <option value="TO" @if( isset( $interno ) && $end->estado == "TO" ) selected @endif>
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

        @if(count($endereco) < 1)
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
        @endif

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
        
    <h5 class="mt-4">Características Físicas</h5>

        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="altura">Altura (cm)</label>
                <input type="text" name="altura" value="{{ $interno->altura or old('altura') }}" class="form-control" id="altura" placeholder="Informe a Altura">
            </div>

            <div class="form-group col-sm-3">
                <label for="peso">Peso (Kg)</label>
                <input type="text" name="peso" value="{{ $interno->peso or old('altura') }}" class="form-control" id="peso" placeholder="Informe o Peso">
            </div>

            <div class="form-group col-sm-3">
                <label for="etnia">Cor da Pele *</label>
                <select id="etnia" name="etnia" class="form-control" required>
                    <option value="" @if( isset( $interno ) && $interno->etnia == "" ) selected @endif>
                        Selecione
                    </option>
                    <option value="Branca" @if( isset( $interno ) && $interno->etnia == "Branca" ) selected @endif>
                        Branca
                    </option>
                    <option value="Indígena" @if( isset( $interno ) && $interno->etnia == "Indígena" ) selected @endif>
                        Indígena
                    </option>
                    <option value="Preta" @if( isset( $interno ) && $interno->etnia == "Preta" ) selected @endif>
                        Preta
                    </option>
                    <option value="Parda" @if( isset( $interno ) && $interno->etnia == "Parda" ) selected @endif>
                        Parda
                    </option>
                    <option value="Amarela" @if( isset( $interno ) && $interno->etnia == "Amarela" ) selected @endif>
                        Amarela
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="cor_olhos">Cor dos Olhos</label>
                <select id="cor_olhos" name="cor_olhos" class="form-control">
                    <option value="" @if( isset( $interno ) && $interno->cor_olhos == "" ) selected @endif>
                        Selecione
                    </option>
                    <option value="Azul" @if( isset( $interno ) && $interno->cor_olhos == "Azul" ) selected @endif>
                        Azul
                    </option>
                    <option value="Azul-esverdeado" @if( isset( $interno ) && $interno->cor_olhos == "Azul-esverdeado" ) selected @endif>
                        Azul-esverdeado
                    </option>
                    <option value="Castanho" @if( isset( $interno ) && $interno->cor_olhos == "Castanho" ) selected @endif>
                        Castanho
                    </option>
                    <option value="Castanho-esverdeado" @if( isset( $interno ) && $interno->cor_olhos == "Castanho-esverdeado" ) selected @endif>
                        Castanho-esverdeado
                    </option>
                    <option value="Castanho-claro" @if( isset( $interno ) && $interno->cor_olhos == "Castanho-claro" ) selected @endif>
                        Castanho-claro
                    </option>
                    <option value="Castanho-escuro" @if( isset( $interno ) && $interno->cor_olhos == "Castanho-escuro" ) selected @endif>
                        Castanho-escuro
                    </option>
                    <option value="Mel" @if( isset( $interno ) && $interno->cor_olhos == "Mel" ) selected @endif>
                        Mel
                    </option>
                    <option value="Preto" @if( isset( $interno ) && $interno->cor_olhos == "Preto" ) selected @endif>
                        Preto
                    </option>
                    <option value="Verde" @if( isset( $interno ) && $interno->cor_olhos == "Verde" ) selected @endif>
                        Verde
                    </option>
                </select>

            </div>

            <div class="form-group col-sm-3">
                <label for="cor_cabelos">Cor dos Cabelos</label>
                <input type="text" name="cor_cabelos" value="{{ $interno->cor_cabelos or old('cor_cabelos') }}" class="form-control" id="cor_cabelos" placeholder="Informe a cor dos cabelos">
            </div>

        </div>

    <h5 class="mt-4">Contato</h5>
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

    {{--<h5 class="mt-4">Artigos que o Interno Responde</h5>--}}
    {{--<div class="form-row" id="grupoArtigo">--}}
        {{--<div class="form-group col-sm-3">--}}
            {{--<label for="artigo">Artigo</label>--}}
            {{--<select name="artigo[]" class="form-control" id="artigo">--}}
                {{--<option value="teste">Teste</option>--}}
            {{--</select>--}}
            {{--<input type="text" name="artigo[]" class="form-control" id="artigo" placeholder="Informe o artigo">--}}
            {{--<i class="fas fa-plus-square cursorPointer btAddArtigo" id="addArtigo"></i>--}}
        {{--</div>--}}
    {{--</div>--}}

    {{--<h5 class="mt-4">Artigos que o Interno Responde</h5>--}}
    {{--<div class="form-row" id="grupoArtigo">--}}
        {{--<div class="form-group col-sm-3">--}}
            {{--<label for="artigo">Artigo</label>--}}
            {{--<input type="text" name="artigo[]" class="form-control" id="artigo" placeholder="Informe o artigo">--}}
            {{--<i class="fas fa-plus-square cursorPointer btAddArtigo" id="addArtigo"></i>--}}
        {{--</div>--}}
    {{--</div>--}}


    <h5 class="mt-4">Dados da Entrada na {{ config('app.secretaria') }} {{ config('app.uf') }}</h5>
        <div class="form-row">

            @if(!isset($interno))
            <div class="form-group col-sm-3">
                <label for="unidade_prisional">Unidade Prisional *</label>
                <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade" required>
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

            <div class="form-group col-sm-3">
                <label for="pavilhao">Pavilhão *</label>
                <select id="pavilhao" name="pavilhao" class="form-control selectUnidade" required>
                    <option value="">
                        Selecione
                    </option>

                    @if(isset($interno))
                        @foreach($pavilhao as $pv)
                            <option value="{{ $pv->id }}" @if( isset( $interno ) && $pv->id == $interno->pavilhao ) selected @endif>
                                {{ $pv->nome }}
                            </option>
                        @endforeach
                    @endif

                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="ala">Ala *</label>
                <select id="ala" name="ala" class="form-control selectUnidade" required>
                    <option value="">
                        Selecione
                    </option>
                    @if(isset($interno))
                        @foreach($ala as $a)
                            <option value="{{ $a->id }}" @if( isset( $interno ) && $a->id == $interno->ala ) selected @endif>
                                {{ $a->nome }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="cela">Cela *</label>
                <select id="cela" name="cela" class="form-control" required>
                    <option value="">
                        Selecione
                    </option>
                    @if(isset($interno))
                        @foreach($cela as $cel)
                            <option value="{{ $cel->id }}" @if( isset( $interno ) && $cel->id == $interno->cela ) selected @endif>
                                {{ $cel->nome }}
                            </option>
                        @endforeach
                    @endif

                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="data_entrada">Data de entrada *</label>
                <input type="text" class="form-control" name="data_entrada" id="data_entrada" placeholder="Data de Entrada" autocomplete="off" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="hora_entrada">Hora da Entrada *</label>
                <input type="text" name="hora_entrada" class="form-control" id="hora_entrada" placeholder="Hora de Entrada" autocomplete="off" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="diasTriagem">Dias na triagem (0 não cumpre triagem) *</label>
                <input type="text" name="diasTriagem" class="form-control" id="diasTriagem" placeholder="informe os dias que o interno ficará na triagem" required>
            </div>

            @endif

            <div class="form-group col-sm-3">
                <label for="documento_entrada_numero">Documento de Entrada</label>
                <input type="text" name="documento_entrada_numero" value="{{ $interno->documento_entrada_numero or old('documento_entrada_numero') }}" class="form-control" id="documento_entrada_numero" placeholder="Informe o numero">
            </div>

            {{--<div class="form-group col-sm-3">--}}
                {{--<label for="condicao_interno">Condição do Interno</label>--}}
                {{--<select id="condicao_interno" name="condicao_interno" class="form-control">--}}
                    {{--<option value="" @if( isset( $interno ) && $interno->condicao_interno == "") selected @endif>--}}
                        {{--Selecione--}}
                    {{--</option>--}}
                    {{--<option value="Livramento Condicional" @if( isset( $interno ) && $interno->condicao_interno == "Livramento Condicional") selected @endif>--}}
                        {{--Livramento Condicional--}}
                    {{--</option>--}}
                    {{--<option value="Foragido" @if( isset( $interno ) && $interno->condicao_interno == "Foragido") selected @endif>--}}
                        {{--Foragido--}}
                    {{--</option>--}}
                    {{--<option value="Internamento" @if( isset( $interno ) && $interno->condicao_interno == "Internamento") selected @endif>--}}
                        {{--Internamento--}}
                    {{--</option>--}}
                    {{--<option value="Prisão Domiciliar" @if( isset( $interno ) && $interno->condicao_interno == "Prisão Domiciliar") selected @endif>--}}
                        {{--Prisão Domiciliar--}}
                    {{--</option>--}}
                {{--</select>--}}
            {{--</div>--}}
            @if(!isset($interno))
            <div class="form-group col-sm-3">
                <label for="sentenciado">Sentenciado *</label>
                <select id="sentenciado" name="sentenciado" class="form-control" required>
                    <option value="" @if( isset( $interno ) && $interno->sentenciado == "") selected @endif>
                        Selecione
                    </option>
                    <option value="S" @if( isset( $interno ) && $interno->sentenciado == "S") selected @endif>
                        Sim
                    </option>
                    <option value="N" @if( isset( $interno ) && $interno->sentenciado == "N") selected @endif>
                        Não
                    </option>
                </select>
            </div>
            @endif

            @if(!isset( $interno ) || $interno->regime == "" && $interno->status == "A" && $interno->medida_seguranca == "N" && $interno->sentenciado == "S")
                <div class="form-group col-sm-3" id="divRegime">
                    <label for="regime">Regime Atual</label>
                    <select id="regime" name="regime" class="form-control">
                        <option value="" @if( isset( $interno ) && $interno->regime == "") selected @endif>
                            Selecione
                        </option>
                        <option value="ABERTO" @if( isset( $interno ) && $interno->regime == "ABERTO") selected @endif>
                            ABERTO
                        </option>
                        <option value="SEMIABERTO" @if( isset( $interno ) && $interno->regime == "SEMIABERTO") selected @endif>
                            SEMIABERTO
                        </option>
                        <option value="FECHADO" @if( isset( $interno ) && $interno->regime == "FECHADO") selected @endif>
                            FECHADO
                        </option>
                    </select>
                </div>
            @endif

            @if(isset( $interno ))
                <div class="form-group col-sm-3">
                    <label for="medida_seguranca">Medida de Segurança</label>
                    <select id="medida_seguranca" name="medida_seguranca" class="form-control">
                        <option value="" @if( isset( $interno ) && $interno->medida_seguranca == "") selected @endif>
                            Selecione
                        </option>
                        <option value="S" @if( isset( $interno ) && $interno->medida_seguranca == "S") selected @endif>
                            Sim
                        </option>
                        <option value="N" @if( isset( $interno ) && $interno->medida_seguranca == "N") selected @endif>
                            Não
                        </option>
                    </select>
                </div>
            @endif

            <div class="form-group col-sm-3 displayNone" id="divSaidasExternas">
                <label for="saidasexternas">Saídas Externas *</label>
                <select id="saidasexternas" name="saidasexternas" class="form-control">
                    <option value="" @if( isset( $interno ) && $interno->saidasexternas == "") selected @endif>
                        Selecione
                    </option>
                    <option value="Sim" @if( isset( $interno ) && $interno->saidasexternas == "Sim") selected @endif>
                        Sim
                    </option>
                    <option value="Não" @if( isset( $interno ) && $interno->saidasexternas == "Não") selected @endif>
                        Não
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="periculosidade">Periculosidade</label>
                <select id="periculosidade" name="periculosidade" class="form-control">
                    <option value="" @if( isset( $interno ) && $interno->periculosidade == "") selected @endif>
                        Selecione
                    </option>
                    <option value="Baixa" @if( isset( $interno ) && $interno->periculosidade == "Baixa") selected @endif>Baixa</option>
                    <option value="Média" @if( isset( $interno ) && $interno->periculosidade == "Média") selected @endif>Média</option>
                    <option value="Alta" @if( isset( $interno ) && $interno->periculosidade == "Alta") selected @endif>Alta</option>
                    {{--<option value="Nível 1" @if( isset( $interno ) && $interno->periculosidade == "Nível 1") selected @endif>Nível 1</option>--}}
                    {{--<option value="Nível 1-A" @if( isset( $interno ) && $interno->periculosidade == "Nível 1-A") selected @endif>Nível 1-A</option>--}}
                    {{--<option value="Nível 2" @if( isset( $interno ) && $interno->periculosidade == "Nível 2") selected @endif>Nível 2</option>--}}
                    {{--<option value="Nível 2-A" @if( isset( $interno ) && $interno->periculosidade == "Nível 2-A") selected @endif>Nível 2-A</option>--}}
                    {{--<option value="Nível 3" @if( isset( $interno ) && $interno->periculosidade == "Nível 3") selected @endif>Nível 3</option>--}}
                    {{--<option value="Nível 3-A" @if( isset( $interno ) && $interno->periculosidade == "Nível 3-A") selected @endif>Nível 3-A</option>--}}
                    {{--<option value="Nível 4" @if( isset( $interno ) && $interno->periculosidade == "Nível 4") selected @endif>Nível 4</option>--}}
                    {{--<option value="Nível 4-A" @if( isset( $interno ) && $interno->periculosidade == "Nível 4-A") selected @endif>Nível 4-A</option>--}}
                    {{--<option value="Nível 5" @if( isset( $interno ) && $interno->periculosidade == "Nível 5") selected @endif>Nível 5</option>--}}
                    {{--<option value="Nível 5-A" @if( isset( $interno ) && $interno->periculosidade == "Nível 5-A") selected @endif>Nível 5-A</option>--}}
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="faccao">Facção</label>
                <select id="faccao" name="faccao" class="form-control">
                    <option value="" @if( isset( $interno ) && $interno->faccao == "") selected @endif>
                        Selecione
                    </option>
                    @foreach($faccao as $fac)
                    <option value="{{ $fac->id }}"  @if( isset( $interno ) && $interno->faccao == $fac->id) selected @endif>
                        {{ $fac->nome }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="faccao_padrinho">Padrinho do Faccionado</label>
                <input type="text" name="faccao_padrinho" value="{{ $interno->faccao_padrinho or old('faccao_padrinho') }}" class="form-control" id="faccao_padrinho" placeholder="Padrinho do faccionado">
            </div>

            <div class="form-group col-sm-3">
                <label for="faccao_posto">Posto na Faccção</label>
                <input type="text" name="faccao_posto" value="{{ $interno->faccao_posto or old('faccao_posto') }}" class="form-control" id="faccao_posto" placeholder="Posto na facção">
            </div>

            <div class="form-group col-sm-3">
                <label for="faccao_quebrada">Quebrada</label>
                <input type="text" name="faccao_quebrada" value="{{ $interno->faccao_quebrada or old('faccao_quebrada') }}" class="form-control" id="faccao_quebrada" placeholder="Posto na facção">
            </div>

            <div class="form-group col-sm-3">
                <label for="lideranca">Liderança</label>
                <select id="lideranca" name="lideranca" class="form-control">
                    <option value="" @if( isset( $interno ) && $interno->lideranca == "") selected @endif>
                        Selecione
                    </option>
                    <option value="S" @if( isset( $interno ) && $interno->lideranca == "S") selected @endif>
                        Sim
                    </option>
                    <option value="N" @if( isset( $interno ) && $interno->lideranca == "N") selected @endif>
                        Não
                    </option>
                </select>
            </div>

        </div>

    <div class="form-row">
        <label for="observacao">Observações</label>
        <textarea name="observacao" id="observacao" class="form-control">@if( isset( $interno ) ){{ $interno->observacao  }} @endif </textarea>
    </div>

@if(isset($interno))
        <hr>
        <h5 class="mt-4">Galeria de Fotos</h5>

        @if(!empty($fotos))
            @foreach($fotos as $foto)

            <div id="card{{$foto->id}}" class="card float-left mr-2" style="width: 18rem;">
                <img class="card-img-top" src="{{asset('storage/app/arquivos/internos/fotos/' . $interno->id . "/$foto->nome_arquivo")}}" style="height: 250px;" alt="foto do Interno{{$interno->nome}}">

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
                                <option value="principal" @if( isset( $interno ) && $foto->tipo == "principal") selected @endif>
                                    Principal
                                </option>
                                <option value="tatuagem" @if( isset( $interno ) && $foto->tipo == "tatuagem") selected @endif>
                                    tatuagem
                                </option>
                                <option value="perfil" @if( isset( $interno ) && $foto->tipo == "perfil") selected @endif>
                                    Perfil
                                </option>
                                <option value="cicatriz" @if( isset( $interno ) && $foto->tipo == "cicatriz") selected @endif>
                                    Cicatriz
                                </option>
                                <option value="sinais" @if( isset( $interno ) && $foto->tipo == "sinais") selected @endif>
                                    Sinais
                                </option>
                                <option value="outros" @if( isset( $interno ) && $foto->tipo == "outros") selected @endif>
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
    <h5 class="mt-4">Fotos - Orientação Horizontal - Resolução ideal de 1024 x 768 pixels </h5>
    <div class="form-row" id="grupoEndereco">

        <div class="form-group col-sm-3">
            <label for="fotos">Fotos</label>
            <input type="file" name="inputFile[]" class="form-control" id="inputFile" accept="image/jpg,image/jpeg,image/png,image/gif">
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
            <input type="text" name="legenda[]" class="form-control" id="legenda" placeholder="Legenda para foto" style="width: 95% !important;">
            <i class="fas fa-plus-square cursorPointer btAddFoto" id="addFoto"></i>
        </div>

    </div>

    <hr>
    <h5 class="mt-4">Documentos</h5>
    @if(isset($interno))

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
                                <a href="{{ route('interno.fileDoc',['id' => $documento->fk_id, 'filename' => $documento->nome_arquivo])}}" data-toggle="tooltip" data-placement="top" title="Arquivos"><i class="mdi mdi mdi-cloud-download"></i></a>

                                <a href="{{ route('interno.documentoDelete',['id' => $documento->fk_id, 'filename' => $documento->nome_arquivo])}}" data-toggle="tooltip" data-placement="top" title="Apagar Arquivo" onclick="return confirm('Deseja realmente excluir o arquivo?');"><i class="mdi mdi-delete color_red"></i></a>

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
    <button type="submit" id="insertInterno" class="btn btn-primary"><i class="fas fa-save"></i> Salvar interno</button>

</form>

@endsection
@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
@if(isset($interno))
    <script src="{{ asset('js/interno/AlterarCadastro.js') }}"></script>
    <script src="{{ asset('js/outros/fotoMultiploAlterar.js') }}"></script>
@else
    <script src="{{ asset('js/interno/cadastro.js') }}"></script>
    <script src="{{ asset('js/outros/fotoMultiploAdd.js') }}"></script>
@endif
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
@endpush
