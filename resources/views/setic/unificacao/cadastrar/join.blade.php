@extends('template.tema_geral.theme')

@section('content')

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div>

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-users-cog"></i> Unificar Internos</h3>
        <hr>
        <p>
            Unifica perfis de dois internos em um só, procedimento indicado quando acontecer cadastro de internos em duplicidade.
            <br/>
            <br/>
            Ao clicar em 'Usar PRONTUÁRIO' o que ocorre é apenas a alteração do campo de texto, as alterações nos perfis só são realizadas ao clicar no botão "Realizar Unificação" no fim da página.
        </p>
    </div>

    <form action="{{ route('setic.unificacao.cadastrar.join') }}" id="proximo" method="post">
        {{ csrf_field() }}
        <div class="form-row">
            <div class="form-group col">
                <h5 class="titleInterno" for="interno">Interno principal <i class="mdi mdi-arrow-bottom-right"></i></h5>
                <input type="text" name="interno" class="form-control" id="internoA" placeholder="Busque por nome ou prontuário" autocomplete="off">
                <div class="form-group col" id="filter-recordsA"></div>
            </div>
        </div>

        <div id="tableInternosA" class="table-responsive displayNone">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Mãe</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoInternoA">

                </tbody>
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
            </table>
        </div>

        <br/>

        <div class="form-row">
            <div class="form-group col">
                <h5 class="titleInterno" for="interno">Interno que será excluído <i class="mdi mdi-arrow-bottom-right"></i></h5>
                <input type="text" name="interno" class="form-control" id="internoB" placeholder="Busque por nome ou prontuário" autocomplete="off">
                <div class="form-group col" id="filter-recordsB"></div>
            </div>
        </div>

        <div id="tableInternosB" class="table-responsive displayNone">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Mãe</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoInternoB">

                </tbody>
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
            </table>
        </div>

        <br/>

        <div id="flashRetorno"></div>
        <button type="submit" class="btn btn-primary">Próximo <i class="fas fa-chevron-right"></i></button>
    </form>

    <br/>
    <hr/>
    <br/>

@if(isset($idA) && isset($idB))
    <form id="campos" action="{{ route('setic.unificacao.cadastrar.join.store') }}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}

        <h4 class="mb-4">Unificação do Perfil dos Internos {{ $idA }} e {{ $idB }}<i class="mdi mdi-arrow-bottom-right"></i></h4>

        <input type="hidden" value="{{ $idA }}" name="idA">
        <input type="hidden" value="{{ $idB }}" name="idB">

        {{-- nome --}}
        <div id="nomePrincipal" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="nome"><strong>Nome Principal</strong></label>
                    <input type="text" name="nome" class="form-control" id="nome" value="{{$dadosA->nome}}" required placeholder="Nome Principal">
                    <label id="nomeUsarA" for="nome" class="mt-1">{{$idA}} - Nome Principal: {{$dadosA->nome}}</label>
                 </div>
                <div class="form-group col-6">
                    <label for="nomePrincipalB">{{$idB}} - Nome Principal</label>
                    <input type="text" name="nomePrincipalB" disabled class="form-control" id="nomePrincipalB" value="{{$dadosB->nome}}">
                </div>
            </div>
            <button type="button" id="buttonNomeUsarA" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>
            <button type="button" id="buttonNomeUsarB" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>
        </div>

        {{-- outrosnomes --}}
        @if($outrosnomesA != collect([]) || $outrosnomesB != collect([]))
            <div id="outrosnomes" class="form-group mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
                <label for="outrosnomes"><strong>Outros Nomes</strong></label>
                <div class="col pl-5 pr-5">
                    @foreach($outrosnomesA as $key=>$outronome)
                        <div class="form-row">
                            <div class="form-group col"  style="border: 1px solid; padding: 5px; border-radius: 10px;">
                                <input type="checkbox" checked id="outrosnomesA{{$key+1}}" name="outrosnomes[]" value="{{$outrosnomesA[$key]->id}}" class="form-check-input">
                                <label for="outrosnomesA{{$key+1}}">{{$idA}} - Outro Nome: {{$outrosnomesA[$key]->nome}}</label>
                                <input type="text" class="form-control" name="outrosnomesTxt[{{$outrosnomesA[$key]->id}}]" value="{{$outrosnomesA[$key]->nome}}" placeholder="Outro Nome">
                            </div>
                        </div>
                    @endforeach
                    @foreach($outrosnomesB as $key=>$outronome)
                        <div class="form-row">
                            <div class="form-group col"  style="border: 1px solid; padding: 5px; border-radius: 10px;">
                                <input type="checkbox" checked id="outrosnomesB{{$key+1}}" name="outrosnomes[]" value="{{$outrosnomesB[$key]->id}}" class="form-check-input">
                                <label for="outrosnomesB{{$key+1}}">{{$idB}} - Outro Nome: {{$outrosnomesB[$key]->nome}}</label>
                                <input type="text" class="form-control" name="outrosnomesTxt[{{$outrosnomesB[$key]->id}}]" value="{{$outrosnomesB[$key]->nome}}" placeholder="Outro Nome">
                            </div>
                        </div>
                    @endforeach
                </div>
                {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Selecionar todos</button>--}}
                {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-square"></i> Desselecionar todos</button>--}}
            </div>
        @endif

        {{-- contatos --}}
        @if($contatosA != collect([]) || $contatosB != collect([]))
            <div id="contatos" class="form-group mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
                <label for="contatos"><strong>Contatos</strong></label>
                <div class="col-6 pl-5 pr-5">
                    @foreach($contatosA as $key=>$contato)
                        <div class="form-row">
                            <div class="form-group col"  style="border: 1px solid; padding: 5px; border-radius: 10px;">
                                <input type="checkbox" checked id="contatosA{{$key+1}}" name="contatos[]" value="{{$contatosA[$key]->fk_id}}" class="form-check-input">
                                <label for="contatosA{{$key+1}}">{{$idA}} - Contato: {{$contatosA[$key]->numero}}</label>
                                <input type="text" class="form-control" name="contatosTxt[{{$contatosA[$key]->id}}]" value="{{$contatosA[$key]->numero}}" placeholder="Contato">
                            </div>
                        </div>
                    @endforeach
                    @foreach($contatosB as $key=>$contato)
                        <div class="form-row">
                            <div class="form-group col"  style="border: 1px solid; padding: 5px; border-radius: 10px;">
                                <input type="checkbox" checked id="contatosB{{$key+1}}" name="contatos[]" value="{{$contatosB[$key]->fk_id}}" class="form-check-input">
                                <label for="contatosB{{$key+1}}">{{$idB}} - Contato: {{$contatosB[$key]->numero}}</label>
                                <input type="text" class="form-control" name="contatosTxt[{{$contatosB[$key]->id}}]" value="{{$contatosB[$key]->numero}}" placeholder="Contato">
                            </div>
                        </div>
                    @endforeach
                </div>
                {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Selecionar todos</button>--}}
                {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-square"></i> Desselecionar todos</button>--}}
            </div>
        @endif

        {{-- enderecos --}}
        @if($enderecosA != collect([]) || $enderecosB != collect([]))
            <div id="enderecos" class="form-group mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
                <label for="enderecos"><strong>Endereços</strong></label>
                <div class="col pl-5 pr-5">
                    @foreach($enderecosA as $key=>$endereco)
                        <div class="form-row">
                            <div class="form-group col" style="border: 1px solid; padding: 5px; border-radius: 10px;">
                                <input type="checkbox" checked id="enderecosA{{$key+1}}" name="enderecos[]" value="{{$enderecosA[$key]->id}}" class="form-check-input">
                                <label for="enderecosA{{$key+1}}"><em>Endereço {{ $key+1 }} do Prontuário {{$idA}}</em></label><br/>
                                <div class="ml-3">
                                    <label for="paisA{{$enderecosA[$key]->fk_id}}"><strong>País</strong></label>
                                    <select id="paisA{{$enderecosA[$key]->fk_id}}" name="enderecosTxt[{{$enderecosA[$key]->id}}][pais]" class="form-control">
                                        <option value="" @if( $enderecosA[$key]->pais == "" ) selected @endif>Selecione</option>
                                        <option value="África do Sul" @if( $enderecosA[$key]->pais == "África do Sul" ) selected @endif>África do Sul</option>
                                        <option value="Albânia" @if( $enderecosA[$key]->pais == "Albânia" ) selected @endif>Albânia</option>
                                        <option value="Alemanha" @if( $enderecosA[$key]->pais == "Alemanha" ) selected @endif>Alemanha</option>
                                        <option value="Andorra" @if( $enderecosA[$key]->pais == "Andorra" ) selected @endif>Andorra</option>
                                        <option value="Angola" @if( $enderecosA[$key]->pais == "Angola" ) selected @endif>Angola</option>
                                        <option value="Anguilla" @if( $enderecosA[$key]->pais == "Anguilla" ) selected @endif>Anguilla</option>
                                        <option value="Antigua" @if( $enderecosA[$key]->pais == "Antigua" ) selected @endif>Antigua</option>
                                        <option value="Arábia Saudita" @if( $enderecosA[$key]->pais == "Arábia Saudita" ) selected @endif>Arábia Saudita</option>
                                        <option value="Argentina" @if( $enderecosA[$key]->pais == "Argentina" ) selected @endif>Argentina</option>
                                        <option value="Armênia" @if( $enderecosA[$key]->pais == "Armênia" ) selected @endif>Armênia</option>
                                        <option value="Aruba" @if( $enderecosA[$key]->pais == "Aruba" ) selected @endif>Aruba</option>
                                        <option value="Austrália" @if( $enderecosA[$key]->pais == "Austrália" ) selected @endif>Austrália</option>
                                        <option value="Áustria" @if( $enderecosA[$key]->pais == "Áustria" ) selected @endif>Áustria</option>
                                        <option value="Azerbaijão" @if( $enderecosA[$key]->pais == "Azerbaijão" ) selected @endif>Azerbaijão</option>
                                        <option value="Bahamas" @if( $enderecosA[$key]->pais == "Bahamas" ) selected @endif>Bahamas</option>
                                        <option value="Bahrein" @if( $enderecosA[$key]->pais == "Bahrein" ) selected @endif>Bahrein</option>
                                        <option value="Bangladesh" @if( $enderecosA[$key]->pais == "Bangladesh" ) selected @endif>Bangladesh</option>
                                        <option value="Barbados" @if( $enderecosA[$key]->pais == "Barbados" ) selected @endif>Barbados</option>
                                        <option value="Bélgica" @if( $enderecosA[$key]->pais == "Bélgica" ) selected @endif>Bélgica</option>
                                        <option value="Benin" @if( $enderecosA[$key]->pais == "Benin" ) selected @endif>Benin</option>
                                        <option value="Bermudas" @if( $enderecosA[$key]->pais == "Bermudas" ) selected @endif>Bermudas</option>
                                        <option value="Botsuana" @if( $enderecosA[$key]->pais == "Botsuana" ) selected @endif>Botsuana</option>
                                        <option value="Brasil" @if( $enderecosA[$key]->pais == "Brasil" ) selected @endif>Brasil</option>
                                        <option value="Brunei" @if( $enderecosA[$key]->pais == "Brunei" ) selected @endif>Brunei</option>
                                        <option value="Bulgária" @if( $enderecosA[$key]->pais == "Bulgária" ) selected @endif>Bulgária</option>
                                        <option value="Burkina Fasso" @if( $enderecosA[$key]->pais == "Burkina Fasso" ) selected @endif>Burkina Fasso</option>
                                        <option value="botão" @if( $enderecosA[$key]->pais == "botão" ) selected @endif>botão</option>
                                        <option value="Cabo Verde" @if( $enderecosA[$key]->pais == "Cabo Verde" ) selected @endif>Cabo Verde</option>
                                        <option value="Camarões" @if( $enderecosA[$key]->pais == "Camarões" ) selected @endif>Camarões</option>
                                        <option value="Camboja" @if( $enderecosA[$key]->pais == "Camboja" ) selected @endif>Camboja</option>
                                        <option value="Canadá" @if( $enderecosA[$key]->pais == "Canadá" ) selected @endif>Canadá</option>
                                        <option value="Cazaquistão" @if( $enderecosA[$key]->pais == "Cazaquistão" ) selected @endif>Cazaquistão</option>
                                        <option value="Chade" @if( $enderecosA[$key]->pais == "Chade" ) selected @endif>Chade</option>
                                        <option value="Chile" @if( $enderecosA[$key]->pais == "Chile" ) selected @endif>Chile</option>
                                        <option value="China" @if( $enderecosA[$key]->pais == "China" ) selected @endif>China</option>
                                        <option value="Cidade do Vaticano" @if( $enderecosA[$key]->pais == "Cidade do Vaticano" ) selected @endif>Cidade do Vaticano</option>
                                        <option value="Colômbia" @if( $enderecosA[$key]->pais == "Colômbia" ) selected @endif>Colômbia</option>
                                        <option value="Congo" @if( $enderecosA[$key]->pais == "Congo" ) selected @endif>Congo</option>
                                        <option value="Coréia do Sul" @if( $enderecosA[$key]->pais == "Coréia do Sul" ) selected @endif>Coréia do Sul</option>
                                        <option value="Costa do Marfim" @if( $enderecosA[$key]->pais == "Costa do Marfim" ) selected @endif>Costa do Marfim</option>
                                        <option value="Costa Rica" @if( $enderecosA[$key]->pais == "Costa Rica" ) selected @endif>Costa Rica</option>
                                        <option value="Croácia" @if( $enderecosA[$key]->pais == "Croácia" ) selected @endif>Croácia</option>
                                        <option value="Dinamarca" @if( $enderecosA[$key]->pais == "Dinamarca" ) selected @endif>Dinamarca</option>
                                        <option value="Djibuti" @if( $enderecosA[$key]->pais == "Djibuti" ) selected @endif>Djibuti</option>
                                        <option value="Dominica" @if( $enderecosA[$key]->pais == "Dominica" ) selected @endif>Dominica</option>
                                        <option value="EUA" @if( $enderecosA[$key]->pais == "EUA" ) selected @endif>EUA</option>
                                        <option value="Egito" @if( $enderecosA[$key]->pais == "Egito" ) selected @endif>Egito</option>
                                        <option value="El Salvador" @if( $enderecosA[$key]->pais == "El Salvador" ) selected @endif>El Salvador</option>
                                        <option value="Emirados Árabes" @if( $enderecosA[$key]->pais == "Emirados Árabes" ) selected @endif>Emirados Árabes</option>
                                        <option value="Equador" @if( $enderecosA[$key]->pais == "Equador" ) selected @endif>Equador</option>
                                        <option value="Eritréia" @if( $enderecosA[$key]->pais == "Eritréia" ) selected @endif>Eritréia</option>
                                        <option value="Escócia" @if( $enderecosA[$key]->pais == "Escócia" ) selected @endif>Escócia</option>
                                        <option value="Eslováquia" @if( $enderecosA[$key]->pais == "Eslováquia" ) selected @endif>Eslováquia</option>
                                        <option value="Eslovênia" @if( $enderecosA[$key]->pais == "Eslovênia" ) selected @endif>Eslovênia</option>
                                        <option value="Espanha" @if( $enderecosA[$key]->pais == "Espanha" ) selected @endif>Espanha</option>
                                        <option value="Estônia" @if( $enderecosA[$key]->pais == "Estônia" ) selected @endif>Estônia</option>
                                        <option value="Etiópia" @if( $enderecosA[$key]->pais == "Etiópia" ) selected @endif>Etiópia</option>
                                        <option value="Fiji" @if( $enderecosA[$key]->pais == "Fiji" ) selected @endif>Fiji</option>
                                        <option value="Filipinas" @if( $enderecosA[$key]->pais == "Filipinas" ) selected @endif>Filipinas</option>
                                        <option value="Finlândia" @if( $enderecosA[$key]->pais == "Finlândia" ) selected @endif>Finlândia</option>
                                        <option value="França" @if( $enderecosA[$key]->pais == "França" ) selected @endif>França</option>
                                        <option value="Gabão" @if( $enderecosA[$key]->pais == "Gabão" ) selected @endif>Gabão</option>
                                        <option value="Gâmbia" @if( $enderecosA[$key]->pais == "Gâmbia" ) selected @endif>Gâmbia</option>
                                        <option value="Gana" @if( $enderecosA[$key]->pais == "Gana" ) selected @endif>Gana</option>
                                        <option value="Geórgia" @if( $enderecosA[$key]->pais == "Geórgia" ) selected @endif>Geórgia</option>
                                        <option value="Gibraltar" @if( $enderecosA[$key]->pais == "Gibraltar" ) selected @endif>Gibraltar</option>
                                        <option value="Granada" @if( $enderecosA[$key]->pais == "Granada" ) selected @endif>Granada</option>
                                        <option value="Grécia" @if( $enderecosA[$key]->pais == "Grécia" ) selected @endif>Grécia</option>
                                        <option value="Guadalupe" @if( $enderecosA[$key]->pais == "Guadalupe" ) selected @endif>Guadalupe</option>
                                        <option value="Guam" @if( $enderecosA[$key]->pais == "Guam" ) selected @endif>Guam</option>
                                        <option value="Guatemala" @if( $enderecosA[$key]->pais == "Guatemala" ) selected @endif>Guatemala</option>
                                        <option value="Guiana" @if( $enderecosA[$key]->pais == "Guiana" ) selected @endif>Guiana</option>
                                        <option value="Guiana Francesa" @if( $enderecosA[$key]->pais == "Guiana Francesa" ) selected @endif>Guiana Francesa</option>
                                        <option value="Guiné-bissau" @if( $enderecosA[$key]->pais == "Guiné-bissau" ) selected @endif>Guiné-bissau</option>
                                        <option value="Haiti" @if( $enderecosA[$key]->pais == "Haiti" ) selected @endif>Haiti</option>
                                        <option value="Holanda" @if( $enderecosA[$key]->pais == "Holanda" ) selected @endif>Holanda</option>
                                        <option value="Honduras" @if( $enderecosA[$key]->pais == "Honduras" ) selected @endif>Honduras</option>
                                        <option value="Hong Kong" @if( $enderecosA[$key]->pais == "Hong Kong" ) selected @endif>Hong Kong</option>
                                        <option value="Hungria" @if( $enderecosA[$key]->pais == "Hungria" ) selected @endif>Hungria</option>
                                        <option value="Iêmen" @if( $enderecosA[$key]->pais == "Iêmen" ) selected @endif>Iêmen</option>
                                        <option value="Ilhas Cayman" @if( $enderecosA[$key]->pais == "Ilhas Cayman" ) selected @endif>Ilhas Cayman</option>
                                        <option value="Ilhas Cook" @if( $enderecosA[$key]->pais == "Ilhas Cook" ) selected @endif>Ilhas Cook</option>
                                        <option value="Ilhas Curaçao" @if( $enderecosA[$key]->pais == "Ilhas Curaçao" ) selected @endif>Ilhas Curaçao</option>
                                        <option value="Ilhas Marshall" @if( $enderecosA[$key]->pais == "Ilhas Marshall" ) selected @endif>Ilhas Marshall</option>
                                        <option value="Ilhas Turks & Caicos" @if( $enderecosA[$key]->pais == "Ilhas Turks & Caicos" ) selected @endif>Ilhas Turks & Caicos</option>
                                        <option value="Ilhas Virgens (brit.)" @if( $enderecosA[$key]->pais == "Ilhas Virgens (brit.)" ) selected @endif>Ilhas Virgens (brit.)</option>
                                        <option value="Ilhas Virgens(amer.)" @if( $enderecosA[$key]->pais == "Ilhas Virgens(amer.)" ) selected @endif>Ilhas Virgens(amer.)</option>
                                        <option value="Ilhas Wallis e Futuna" @if( $enderecosA[$key]->pais == "Ilhas Wallis e Futuna" ) selected @endif>Ilhas Wallis e Futuna</option>
                                        <option value="Ilhas do Pacífico (EUA)" @if( $enderecosA[$key]->pais == "Ilhas do Pacífico (EUA)" ) selected @endif>Ilhas do Pacífico (EUA)</option>
                                        <option value="Índia" @if( $enderecosA[$key]->pais == "Índia" ) selected @endif>Índia</option>
                                        <option value="Indonésia" @if( $enderecosA[$key]->pais == "Indonésia" ) selected @endif>Indonésia</option>
                                        <option value="Inglaterra" @if( $enderecosA[$key]->pais == "Inglaterra" ) selected @endif>Inglaterra</option>
                                        <option value="Irlanda" @if( $enderecosA[$key]->pais == "Irlanda" ) selected @endif>Irlanda</option>
                                        <option value="Islândia" @if( $enderecosA[$key]->pais == "Islândia" ) selected @endif>Islândia</option>
                                        <option value="Israel" @if( $enderecosA[$key]->pais == "Israel" ) selected @endif>Israel</option>
                                        <option value="Itália" @if( $enderecosA[$key]->pais == "Itália" ) selected @endif>Itália</option>
                                        <option value="Jamaica" @if( $enderecosA[$key]->pais == "Jamaica" ) selected @endif>Jamaica</option>
                                        <option value="Japão" @if( $enderecosA[$key]->pais == "Japão" ) selected @endif>Japão</option>
                                        <option value="Jordânia" @if( $enderecosA[$key]->pais == "Jordânia" ) selected @endif>Jordânia</option>
                                        <option value="Kuwait" @if( $enderecosA[$key]->pais == "Kuwait" ) selected @endif>Kuwait</option>
                                        <option value="Latvia" @if( $enderecosA[$key]->pais == "Latvia" ) selected @endif>Latvia</option>
                                        <option value="Líbano" @if( $enderecosA[$key]->pais == "Líbano" ) selected @endif>Líbano</option>
                                        <option value="Liechtenstein" @if( $enderecosA[$key]->pais == "Liechtenstein" ) selected @endif>Liechtenstein</option>
                                        <option value="Lituânia" @if( $enderecosA[$key]->pais == "Lituânia" ) selected @endif>Lituânia</option>
                                        <option value="Luxemburgo" @if( $enderecosA[$key]->pais == "Luxemburgo" ) selected @endif>Luxemburgo</option>
                                        <option value="Macau" @if( $enderecosA[$key]->pais == "Macau" ) selected @endif>Macau</option>
                                        <option value="Macedônia" @if( $enderecosA[$key]->pais == "Macedônia" ) selected @endif>Macedônia</option>
                                        <option value="Madagascar" @if( $enderecosA[$key]->pais == "Madagascar" ) selected @endif>Madagascar</option>
                                        <option value="Malásia" @if( $enderecosA[$key]->pais == "Malásia" ) selected @endif>Malásia</option>
                                        <option value="Malaui" @if( $enderecosA[$key]->pais == "Malaui" ) selected @endif>Malaui</option>
                                        <option value="Mali" @if( $enderecosA[$key]->pais == "Mali" ) selected @endif>Mali</option>
                                        <option value="Malta" @if( $enderecosA[$key]->pais == "Malta" ) selected @endif>Malta</option>
                                        <option value="Marrocos" @if( $enderecosA[$key]->pais == "Marrocos" ) selected @endif>Marrocos</option>
                                        <option value="Martinica" @if( $enderecosA[$key]->pais == "Martinica" ) selected @endif>Martinica</option>
                                        <option value="Mauritânia" @if( $enderecosA[$key]->pais == "Mauritânia" ) selected @endif>Mauritânia</option>
                                        <option value="Mauritius" @if( $enderecosA[$key]->pais == "Mauritius" ) selected @endif>Mauritius</option>
                                        <option value="México" @if( $enderecosA[$key]->pais == "México" ) selected @endif>México</option>
                                        <option value="Moldova" @if( $enderecosA[$key]->pais == "Moldova" ) selected @endif>Moldova</option>
                                        <option value="Mônaco" @if( $enderecosA[$key]->pais == "Mônaco" ) selected @endif>Mônaco</option>
                                        <option value="Montserrat" @if( $enderecosA[$key]->pais == "Montserrat" ) selected @endif>Montserrat</option>
                                        <option value="Nepal" @if( $enderecosA[$key]->pais == "Nepal" ) selected @endif>Nepal</option>
                                        <option value="Nicarágua" @if( $enderecosA[$key]->pais == "Nicarágua" ) selected @endif>Nicarágua</option>
                                        <option value="Niger" @if( $enderecosA[$key]->pais == "Niger" ) selected @endif>Niger</option>
                                        <option value="Nigéria" @if( $enderecosA[$key]->pais == "Nigéria" ) selected @endif>Nigéria</option>
                                        <option value="Noruega" @if( $enderecosA[$key]->pais == "Noruega" ) selected @endif>Noruega</option>
                                        <option value="Nova Caledônia" @if( $enderecosA[$key]->pais == "Nova Caledônia" ) selected @endif>Nova Caledônia</option>
                                        <option value="Nova Zelândia" @if( $enderecosA[$key]->pais == "Nova Zelândia" ) selected @endif>Nova Zelândia</option>
                                        <option value="Omã" @if( $enderecosA[$key]->pais == "Omã" ) selected @endif>Omã</option>
                                        <option value="Palau" @if( $enderecosA[$key]->pais == "Palau" ) selected @endif>Palau</option>
                                        <option value="Panamá" @if( $enderecosA[$key]->pais == "Panamá" ) selected @endif>Panamá</option>
                                        <option value="Papua-nova Guiné" @if( $enderecosA[$key]->pais == "Papua-nova Guiné" ) selected @endif>Papua-nova Guiné</option>
                                        <option value="Paquistão" @if( $enderecosA[$key]->pais == "Paquistão" ) selected @endif>Paquistão</option>
                                        <option value="Peru" @if( $enderecosA[$key]->pais == "Peru" ) selected @endif>Peru</option>
                                        <option value="Polinésia Francesa" @if( $enderecosA[$key]->pais == "Polinésia Francesa" ) selected @endif>Polinésia Francesa</option>
                                        <option value="Polônia" @if( $enderecosA[$key]->pais == "Polônia" ) selected @endif>Polônia</option>
                                        <option value="Porto Rico" @if( $enderecosA[$key]->pais == "Porto Rico" ) selected @endif>Porto Rico</option>
                                        <option value="Portugal" @if( $enderecosA[$key]->pais == "Portugal" ) selected @endif>Portugal</option>
                                        <option value="Qatar" @if( $enderecosA[$key]->pais == "Qatar" ) selected @endif>Qatar</option>
                                        <option value="Quênia" @if( $enderecosA[$key]->pais == "Quênia" ) selected @endif>Quênia</option>
                                        <option value="Rep. Dominicana" @if( $enderecosA[$key]->pais == "Rep. Dominicana" ) selected @endif>Rep. Dominicana</option>
                                        <option value="Rep. Tcheca" @if( $enderecosA[$key]->pais == "Rep. Tcheca" ) selected @endif>Rep. Tcheca</option>
                                        <option value="Reunion" @if( $enderecosA[$key]->pais == "Reunion" ) selected @endif>Reunion</option>
                                        <option value="Romênia" @if( $enderecosA[$key]->pais == "Romênia" ) selected @endif>Romênia</option>
                                        <option value="Ruanda" @if( $enderecosA[$key]->pais == "Ruanda" ) selected @endif>Ruanda</option>
                                        <option value="Rússia" @if( $enderecosA[$key]->pais == "Rússia" ) selected @endif>Rússia</option>
                                        <option value="Saipan" @if( $enderecosA[$key]->pais == "Saipan" ) selected @endif>Saipan</option>
                                        <option value="Samoa Americana" @if( $enderecosA[$key]->pais == "Samoa Americana" ) selected @endif>Samoa Americana</option>
                                        <option value="Senegal" @if( $enderecosA[$key]->pais == "Senegal" ) selected @endif>Senegal</option>
                                        <option value="Serra Leone" @if( $enderecosA[$key]->pais == "Serra Leone" ) selected @endif>Serra Leone</option>
                                        <option value="Seychelles" @if( $enderecosA[$key]->pais == "Seychelles" ) selected @endif>Seychelles</option>
                                        <option value="Singapura" @if( $enderecosA[$key]->pais == "Singapura" ) selected @endif>Singapura</option>
                                        <option value="Síria" @if( $enderecosA[$key]->pais == "Síria" ) selected @endif>Síria</option>
                                        <option value="Sri Lanka" @if( $enderecosA[$key]->pais == "Sri Lanka" ) selected @endif>Sri Lanka</option>
                                        <option value="St. Kitts & Nevis" @if( $enderecosA[$key]->pais == "St. Kitts & Nevis" ) selected @endif>St. Kitts & Nevis</option>
                                        <option value="St. Lúcia" @if( $enderecosA[$key]->pais == "St. Lúcia" ) selected @endif>St. Lúcia</option>
                                        <option value="St. Vincent" @if( $enderecosA[$key]->pais == "St. Vincent" ) selected @endif>St. Vincent</option>
                                        <option value="Sudão" @if( $enderecosA[$key]->pais == "Sudão" ) selected @endif>Sudão</option>
                                        <option value="Suécia" @if( $enderecosA[$key]->pais == "Suécia" ) selected @endif>Suécia</option>
                                        <option value="Suíça" @if( $enderecosA[$key]->pais == "Suíça" ) selected @endif>Suíça</option>
                                        <option value="Suriname" @if( $enderecosA[$key]->pais == "Suriname" ) selected @endif>Suriname</option>
                                        <option value="Tailândia" @if( $enderecosA[$key]->pais == "Tailândia" ) selected @endif>Tailândia</option>
                                        <option value="Taiwan" @if( $enderecosA[$key]->pais == "Taiwan" ) selected @endif>Taiwan</option>
                                        <option value="Tanzânia" @if( $enderecosA[$key]->pais == "Tanzânia" ) selected @endif>Tanzânia</option>
                                        <option value="Togo" @if( $enderecosA[$key]->pais == "Togo" ) selected @endif>Togo</option>
                                        <option value="Trinidad & Tobago" @if( $enderecosA[$key]->pais == "Trinidad & Tobago" ) selected @endif>Trinidad & Tobago</option>
                                        <option value="Tunísia" @if( $enderecosA[$key]->pais == "Tunísia" ) selected @endif>Tunísia</option>
                                        <option value="Turquia" @if( $enderecosA[$key]->pais == "Turquia" ) selected @endif>Turquia</option>
                                        <option value="Ucrânia" @if( $enderecosA[$key]->pais == "Ucrânia" ) selected @endif>Ucrânia</option>
                                        <option value="Uganda" @if( $enderecosA[$key]->pais == "Uganda" ) selected @endif>Uganda</option>
                                        <option value="Uruguai" @if( $enderecosA[$key]->pais == "Uruguai" ) selected @endif>Uruguai</option>
                                        <option value="Venezuela" @if( $enderecosA[$key]->pais == "Venezuela" ) selected @endif>Venezuela</option>
                                        <option value="Vietnã" @if( $enderecosA[$key]->pais == "Vietnã" ) selected @endif>Vietnã</option>
                                        <option value="Zaire" @if( $enderecosA[$key]->pais == "Zaire" ) selected @endif>Zaire</option>
                                        <option value="Zâmbia" @if( $enderecosA[$key]->pais == "Zâmbia" ) selected @endif>Zâmbia</option>
                                        <option value="Zimbábue" @if( $enderecosA[$key]->pais == "Zimbábue" ) selected @endif>Zimbábue</option>
                                    </select>
                                    <label for="paisA{{$enderecosA[$key]->fk_id}}" class="mt-1">{{$idA}} - Endereço {{$key+1}} - País: {{$enderecosA[$key]->pais}}</label><br/>
                                    <label for="estadoA{{$enderecosA[$key]->fk_id}}"><strong>Estado</strong></label>
                                    <select id="estadoA{{$enderecosA[$key]->fk_id}}" name="enderecosTxt[{{$enderecosA[$key]->id}}][estado]" class="form-control">
                                        <option value="" @if( $enderecosA[$key]->estado == "" ) selected @endif>
                                            Selecione
                                        </option>
                                        <option value="AC" @if( $enderecosA[$key]->estado == "AC" ) selected @endif>
                                            Acre
                                        </option>
                                        <option value="AL" @if( $enderecosA[$key]->estado == "AL" ) selected @endif>
                                            Alagoas
                                        </option>
                                        <option value="AP" @if( $enderecosA[$key]->estado == "AP" ) selected @endif>
                                            Amapá
                                        </option>
                                        <option value="AM" @if( $enderecosA[$key]->estado == "AM" ) selected @endif>
                                            Amazonas
                                        </option>
                                        <option value="BA" @if( $enderecosA[$key]->estado == "BA" ) selected @endif>
                                            Bahia
                                        </option>
                                        <option value="CE" @if( $enderecosA[$key]->estado == "CE" ) selected @endif>
                                            Ceará
                                        </option>
                                        <option value="DF" @if( $enderecosA[$key]->estado == "DF" ) selected @endif>
                                            Distrito Federal
                                        </option>
                                        <option value="ES" @if( $enderecosA[$key]->estado == "ES" ) selected @endif>
                                            Espírito Santo
                                        </option>
                                        <option value="GO" @if( $enderecosA[$key]->estado == "GO" ) selected @endif>
                                            Goiás
                                        </option>
                                        <option value="MA" @if( $enderecosA[$key]->estado == "MA" ) selected @endif>
                                            Maranhão
                                        </option>
                                        <option value="MT" @if( $enderecosA[$key]->estado == "MT" ) selected @endif>
                                            Mato Grosso
                                        </option>
                                        <option value="MS" @if( $enderecosA[$key]->estado == "MS" ) selected @endif>
                                            Mato Grosso do Sul
                                        </option>
                                        <option value="MG" @if( $enderecosA[$key]->estado == "MG" ) selected @endif>
                                            Minas Gerais
                                        </option>
                                        <option value="PA" @if( $enderecosA[$key]->estado == "PA" ) selected @endif>
                                            Pará
                                        </option>
                                        <option value="PB" @if( $enderecosA[$key]->estado == "PB" ) selected @endif>
                                            Paraíba
                                        </option>
                                        <option value="PR" @if( $enderecosA[$key]->estado == "PR" ) selected @endif>
                                            Paraná
                                        </option>
                                        <option value="PE" @if( $enderecosA[$key]->estado == "PE" ) selected @endif>
                                            Pernambuco
                                        </option>
                                        <option value="PI" @if( $enderecosA[$key]->estado == "PI" ) selected @endif>
                                            Piauí
                                        </option>
                                        <option value="RJ" @if( $enderecosA[$key]->estado == "RJ" ) selected @endif>
                                            Rio de Janeiro
                                        </option>
                                        <option value="RN" @if( $enderecosA[$key]->estado == "RN" ) selected @endif>
                                            Rio Grande do Norte
                                        </option>
                                        <option value="RS" @if( $enderecosA[$key]->estado == "RS" ) selected @endif>
                                            Rio Grande do Sul
                                        </option>
                                        <option value="RO" @if( $enderecosA[$key]->estado == "RO" ) selected @endif>
                                            Rondônia
                                        </option>
                                        <option value="RR" @if( $enderecosA[$key]->estado == "RR" ) selected @endif>
                                            Roraima
                                        </option>
                                        <option value="SC" @if( $enderecosA[$key]->estado == "SC" ) selected @endif>
                                            Santa Catarina
                                        </option>
                                        <option value="SP" @if( $enderecosA[$key]->estado == "SP" ) selected @endif>
                                            São Paulo
                                        </option>
                                        <option value="SE" @if( $enderecosA[$key]->estado == "SE" ) selected @endif>
                                            Sergipe
                                        </option>
                                        <option value="TO" @if( $enderecosA[$key]->estado == "TO" ) selected @endif>
                                            Tocantins
                                        </option>
                                    </select>
                                    <label for="estadoA{{$enderecosA[$key]->fk_id}}" class="mt-1">{{$idA}} - Endereço {{$key+1}} - Estado: {{$enderecosA[$key]->estado}}</label><br/>
                                    <label for="cidadeA{{$enderecosA[$key]->fk_id}}"><strong>Cidade</strong></label>
                                    <input type="text" id="cidadeA{{$enderecosA[$key]->fk_id}}" name="enderecosTxt[{{$enderecosA[$key]->id}}][cidade]" class="form-control" value="{{$enderecosA[$key]->cidade}}" placeholder="Cidade">
                                    <label for="cidadeA{{$enderecosA[$key]->fk_id}}" class="mt-1">{{$idA}} - Endereço {{$key+1}} - Cidade: {{$enderecosA[$key]->cidade}}</label><br/>
                                    <label for="bairroA{{$enderecosA[$key]->fk_id}}"><strong>Bairro</strong></label>
                                    <input type="text" id="bairroA{{$enderecosA[$key]->fk_id}}" name="enderecosTxt[{{$enderecosA[$key]->id}}][bairro]" class="form-control" value="{{$enderecosA[$key]->bairro}}" placeholder="Bairro">
                                    <label for="bairroA{{$enderecosA[$key]->fk_id}}" class="mt-1">{{$idA}} - Endereço {{$key+1}} - Bairro: {{$enderecosA[$key]->bairro}}</label><br/>
                                    <label for="cepA{{$enderecosA[$key]->fk_id}}"><strong>CEP</strong></label>
                                    <input type="text" id="cepA{{$enderecosA[$key]->fk_id}}" name="enderecosTxt[{{$enderecosA[$key]->id}}][cep]" class="form-control ceps" value="{{$enderecosA[$key]->cep}}" placeholder="CEP">
                                    <label for="cepA{{$enderecosA[$key]->fk_id}}" class="mt-1">{{$idA}} - Endereço {{$key+1}} - CEP: {{$enderecosA[$key]->cep}}</label><br/>
                                    <label for="logradouroA{{$enderecosA[$key]->fk_id}}"><strong>Logradouro</strong></label>
                                    <input type="text" id="logradouroA{{$enderecosA[$key]->fk_id}}" name="enderecosTxt[{{$enderecosA[$key]->id}}][logradouro]" class="form-control" value="{{$enderecosA[$key]->logradouro}}" placeholder="Logradouro">
                                    <label for="logradouroA{{$enderecosA[$key]->fk_id}}" class="mt-1">{{$idA}} - Endereço {{$key+1}} - Logradouro: {{$enderecosA[$key]->logradouro}}</label><br/>
                                    <label for="numeroA{{$enderecosA[$key]->fk_id}}"><strong>Número</strong></label>
                                    <input type="text" id="numeroA{{$enderecosA[$key]->fk_id}}" name="enderecosTxt[{{$enderecosA[$key]->id}}][numero]" class="form-control" value="{{$enderecosA[$key]->numero}}" placeholder="Número">
                                    <label for="numeroA{{$enderecosA[$key]->fk_id}}" class="mt-1">{{$idA}} - Endereço {{$key+1}} - Número: {{$enderecosA[$key]->numero}}</label><br/>
                                    <label for="complementoA{{$enderecosA[$key]->fk_id}}"><strong>Complemento</strong></label>
                                    <input type="text" id="complementoA{{$enderecosA[$key]->fk_id}}" name="enderecosTxt[{{$enderecosA[$key]->id}}][complemento]" class="form-control" value="{{$enderecosA[$key]->complemento}}" placeholder="Complemento">
                                    <label for="complementoA{{$enderecosA[$key]->fk_id}}" class="mt-1">{{$idA}} - Endereço {{$key+1}} - Complemento: {{$enderecosA[$key]->complemento}}</label><br/>
                                    <label for="latitudeA{{$enderecosA[$key]->fk_id}}"><strong>Latitude</strong></label>
                                    <input type="text" id="latitudeA{{$enderecosA[$key]->fk_id}}" name="enderecosTxt[{{$enderecosA[$key]->id}}][latitude]" class="form-control" value="{{$enderecosA[$key]->latitude}}" placeholder="Latitude">
                                    <label for="latitudeA{{$enderecosA[$key]->fk_id}}" class="mt-1">{{$idA}} - Endereço {{$key+1}} - Latitude: {{$enderecosA[$key]->latitude}}</label><br/>
                                    <label for="longitudeA{{$enderecosA[$key]->fk_id}}"><strong>Longitude</strong></label>
                                    <input type="text" id="longitudeA{{$enderecosA[$key]->fk_id}}" name="enderecosTxt[{{$enderecosA[$key]->id}}][longitude]" class="form-control" value="{{$enderecosA[$key]->longitude}}" placeholder="Longitude">
                                    <label for="longitudeA{{$enderecosA[$key]->fk_id}}" class="mt-1">{{$idA}} - Endereço {{$key+1}} - Longitude: {{$enderecosA[$key]->longitude}}</label><br/>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @foreach($enderecosB as $key=>$endereco)
                        <div class="form-row">
                            <div class="form-group col" style="border: 1px solid; padding: 5px; border-radius: 10px;">
                                <input type="checkbox" checked id="enderecosB{{$key+1}}" name="enderecos[]" value="{{$enderecosB[$key]->id}}" class="form-check-input">
                                <label for="enderecosB{{$key+1}}"><em>Endereço {{ $key+1 }} do Prontuário {{$idB}}</em></label><br/>
                                <div class="ml-3">
                                    <label for="paisB{{$enderecosB[$key]->fk_id}}"><strong>País</strong></label>
                                    <select id="paisB{{$enderecosB[$key]->fk_id}}" name="enderecosTxt[{{$enderecosB[$key]->id}}][pais]" class="form-control">
                                        <option value="" @if( $enderecosB[$key]->pais == "" ) selected @endif>Selecione</option>
                                        <option value="África do Sul" @if( $enderecosB[$key]->pais == "África do Sul" ) selected @endif>África do Sul</option>
                                        <option value="Albânia" @if( $enderecosB[$key]->pais == "Albânia" ) selected @endif>Albânia</option>
                                        <option value="Alemanha" @if( $enderecosB[$key]->pais == "Alemanha" ) selected @endif>Alemanha</option>
                                        <option value="Andorra" @if( $enderecosB[$key]->pais == "Andorra" ) selected @endif>Andorra</option>
                                        <option value="Angola" @if( $enderecosB[$key]->pais == "Angola" ) selected @endif>Angola</option>
                                        <option value="Anguilla" @if( $enderecosB[$key]->pais == "Anguilla" ) selected @endif>Anguilla</option>
                                        <option value="Antigua" @if( $enderecosB[$key]->pais == "Antigua" ) selected @endif>Antigua</option>
                                        <option value="Arábia Saudita" @if( $enderecosB[$key]->pais == "Arábia Saudita" ) selected @endif>Arábia Saudita</option>
                                        <option value="Argentina" @if( $enderecosB[$key]->pais == "Argentina" ) selected @endif>Argentina</option>
                                        <option value="Armênia" @if( $enderecosB[$key]->pais == "Armênia" ) selected @endif>Armênia</option>
                                        <option value="Aruba" @if( $enderecosB[$key]->pais == "Aruba" ) selected @endif>Aruba</option>
                                        <option value="Austrália" @if( $enderecosB[$key]->pais == "Austrália" ) selected @endif>Austrália</option>
                                        <option value="Áustria" @if( $enderecosB[$key]->pais == "Áustria" ) selected @endif>Áustria</option>
                                        <option value="Azerbaijão" @if( $enderecosB[$key]->pais == "Azerbaijão" ) selected @endif>Azerbaijão</option>
                                        <option value="Bahamas" @if( $enderecosB[$key]->pais == "Bahamas" ) selected @endif>Bahamas</option>
                                        <option value="Bahrein" @if( $enderecosB[$key]->pais == "Bahrein" ) selected @endif>Bahrein</option>
                                        <option value="Bangladesh" @if( $enderecosB[$key]->pais == "Bangladesh" ) selected @endif>Bangladesh</option>
                                        <option value="Barbados" @if( $enderecosB[$key]->pais == "Barbados" ) selected @endif>Barbados</option>
                                        <option value="Bélgica" @if( $enderecosB[$key]->pais == "Bélgica" ) selected @endif>Bélgica</option>
                                        <option value="Benin" @if( $enderecosB[$key]->pais == "Benin" ) selected @endif>Benin</option>
                                        <option value="Bermudas" @if( $enderecosB[$key]->pais == "Bermudas" ) selected @endif>Bermudas</option>
                                        <option value="Botsuana" @if( $enderecosB[$key]->pais == "Botsuana" ) selected @endif>Botsuana</option>
                                        <option value="Brasil" @if( $enderecosB[$key]->pais == "Brasil" ) selected @endif>Brasil</option>
                                        <option value="Brunei" @if( $enderecosB[$key]->pais == "Brunei" ) selected @endif>Brunei</option>
                                        <option value="Bulgária" @if( $enderecosB[$key]->pais == "Bulgária" ) selected @endif>Bulgária</option>
                                        <option value="Burkina Fasso" @if( $enderecosB[$key]->pais == "Burkina Fasso" ) selected @endif>Burkina Fasso</option>
                                        <option value="botão" @if( $enderecosB[$key]->pais == "botão" ) selected @endif>botão</option>
                                        <option value="Cabo Verde" @if( $enderecosB[$key]->pais == "Cabo Verde" ) selected @endif>Cabo Verde</option>
                                        <option value="Camarões" @if( $enderecosB[$key]->pais == "Camarões" ) selected @endif>Camarões</option>
                                        <option value="Camboja" @if( $enderecosB[$key]->pais == "Camboja" ) selected @endif>Camboja</option>
                                        <option value="Canadá" @if( $enderecosB[$key]->pais == "Canadá" ) selected @endif>Canadá</option>
                                        <option value="Cazaquistão" @if( $enderecosB[$key]->pais == "Cazaquistão" ) selected @endif>Cazaquistão</option>
                                        <option value="Chade" @if( $enderecosB[$key]->pais == "Chade" ) selected @endif>Chade</option>
                                        <option value="Chile" @if( $enderecosB[$key]->pais == "Chile" ) selected @endif>Chile</option>
                                        <option value="China" @if( $enderecosB[$key]->pais == "China" ) selected @endif>China</option>
                                        <option value="Cidade do Vaticano" @if( $enderecosB[$key]->pais == "Cidade do Vaticano" ) selected @endif>Cidade do Vaticano</option>
                                        <option value="Colômbia" @if( $enderecosB[$key]->pais == "Colômbia" ) selected @endif>Colômbia</option>
                                        <option value="Congo" @if( $enderecosB[$key]->pais == "Congo" ) selected @endif>Congo</option>
                                        <option value="Coréia do Sul" @if( $enderecosB[$key]->pais == "Coréia do Sul" ) selected @endif>Coréia do Sul</option>
                                        <option value="Costa do Marfim" @if( $enderecosB[$key]->pais == "Costa do Marfim" ) selected @endif>Costa do Marfim</option>
                                        <option value="Costa Rica" @if( $enderecosB[$key]->pais == "Costa Rica" ) selected @endif>Costa Rica</option>
                                        <option value="Croácia" @if( $enderecosB[$key]->pais == "Croácia" ) selected @endif>Croácia</option>
                                        <option value="Dinamarca" @if( $enderecosB[$key]->pais == "Dinamarca" ) selected @endif>Dinamarca</option>
                                        <option value="Djibuti" @if( $enderecosB[$key]->pais == "Djibuti" ) selected @endif>Djibuti</option>
                                        <option value="Dominica" @if( $enderecosB[$key]->pais == "Dominica" ) selected @endif>Dominica</option>
                                        <option value="EUA" @if( $enderecosB[$key]->pais == "EUA" ) selected @endif>EUA</option>
                                        <option value="Egito" @if( $enderecosB[$key]->pais == "Egito" ) selected @endif>Egito</option>
                                        <option value="El Salvador" @if( $enderecosB[$key]->pais == "El Salvador" ) selected @endif>El Salvador</option>
                                        <option value="Emirados Árabes" @if( $enderecosB[$key]->pais == "Emirados Árabes" ) selected @endif>Emirados Árabes</option>
                                        <option value="Equador" @if( $enderecosB[$key]->pais == "Equador" ) selected @endif>Equador</option>
                                        <option value="Eritréia" @if( $enderecosB[$key]->pais == "Eritréia" ) selected @endif>Eritréia</option>
                                        <option value="Escócia" @if( $enderecosB[$key]->pais == "Escócia" ) selected @endif>Escócia</option>
                                        <option value="Eslováquia" @if( $enderecosB[$key]->pais == "Eslováquia" ) selected @endif>Eslováquia</option>
                                        <option value="Eslovênia" @if( $enderecosB[$key]->pais == "Eslovênia" ) selected @endif>Eslovênia</option>
                                        <option value="Espanha" @if( $enderecosB[$key]->pais == "Espanha" ) selected @endif>Espanha</option>
                                        <option value="Estônia" @if( $enderecosB[$key]->pais == "Estônia" ) selected @endif>Estônia</option>
                                        <option value="Etiópia" @if( $enderecosB[$key]->pais == "Etiópia" ) selected @endif>Etiópia</option>
                                        <option value="Fiji" @if( $enderecosB[$key]->pais == "Fiji" ) selected @endif>Fiji</option>
                                        <option value="Filipinas" @if( $enderecosB[$key]->pais == "Filipinas" ) selected @endif>Filipinas</option>
                                        <option value="Finlândia" @if( $enderecosB[$key]->pais == "Finlândia" ) selected @endif>Finlândia</option>
                                        <option value="França" @if( $enderecosB[$key]->pais == "França" ) selected @endif>França</option>
                                        <option value="Gabão" @if( $enderecosB[$key]->pais == "Gabão" ) selected @endif>Gabão</option>
                                        <option value="Gâmbia" @if( $enderecosB[$key]->pais == "Gâmbia" ) selected @endif>Gâmbia</option>
                                        <option value="Gana" @if( $enderecosB[$key]->pais == "Gana" ) selected @endif>Gana</option>
                                        <option value="Geórgia" @if( $enderecosB[$key]->pais == "Geórgia" ) selected @endif>Geórgia</option>
                                        <option value="Gibraltar" @if( $enderecosB[$key]->pais == "Gibraltar" ) selected @endif>Gibraltar</option>
                                        <option value="Granada" @if( $enderecosB[$key]->pais == "Granada" ) selected @endif>Granada</option>
                                        <option value="Grécia" @if( $enderecosB[$key]->pais == "Grécia" ) selected @endif>Grécia</option>
                                        <option value="Guadalupe" @if( $enderecosB[$key]->pais == "Guadalupe" ) selected @endif>Guadalupe</option>
                                        <option value="Guam" @if( $enderecosB[$key]->pais == "Guam" ) selected @endif>Guam</option>
                                        <option value="Guatemala" @if( $enderecosB[$key]->pais == "Guatemala" ) selected @endif>Guatemala</option>
                                        <option value="Guiana" @if( $enderecosB[$key]->pais == "Guiana" ) selected @endif>Guiana</option>
                                        <option value="Guiana Francesa" @if( $enderecosB[$key]->pais == "Guiana Francesa" ) selected @endif>Guiana Francesa</option>
                                        <option value="Guiné-bissau" @if( $enderecosB[$key]->pais == "Guiné-bissau" ) selected @endif>Guiné-bissau</option>
                                        <option value="Haiti" @if( $enderecosB[$key]->pais == "Haiti" ) selected @endif>Haiti</option>
                                        <option value="Holanda" @if( $enderecosB[$key]->pais == "Holanda" ) selected @endif>Holanda</option>
                                        <option value="Honduras" @if( $enderecosB[$key]->pais == "Honduras" ) selected @endif>Honduras</option>
                                        <option value="Hong Kong" @if( $enderecosB[$key]->pais == "Hong Kong" ) selected @endif>Hong Kong</option>
                                        <option value="Hungria" @if( $enderecosB[$key]->pais == "Hungria" ) selected @endif>Hungria</option>
                                        <option value="Iêmen" @if( $enderecosB[$key]->pais == "Iêmen" ) selected @endif>Iêmen</option>
                                        <option value="Ilhas Cayman" @if( $enderecosB[$key]->pais == "Ilhas Cayman" ) selected @endif>Ilhas Cayman</option>
                                        <option value="Ilhas Cook" @if( $enderecosB[$key]->pais == "Ilhas Cook" ) selected @endif>Ilhas Cook</option>
                                        <option value="Ilhas Curaçao" @if( $enderecosB[$key]->pais == "Ilhas Curaçao" ) selected @endif>Ilhas Curaçao</option>
                                        <option value="Ilhas Marshall" @if( $enderecosB[$key]->pais == "Ilhas Marshall" ) selected @endif>Ilhas Marshall</option>
                                        <option value="Ilhas Turks & Caicos" @if( $enderecosB[$key]->pais == "Ilhas Turks & Caicos" ) selected @endif>Ilhas Turks & Caicos</option>
                                        <option value="Ilhas Virgens (brit.)" @if( $enderecosB[$key]->pais == "Ilhas Virgens (brit.)" ) selected @endif>Ilhas Virgens (brit.)</option>
                                        <option value="Ilhas Virgens(amer.)" @if( $enderecosB[$key]->pais == "Ilhas Virgens(amer.)" ) selected @endif>Ilhas Virgens(amer.)</option>
                                        <option value="Ilhas Wallis e Futuna" @if( $enderecosB[$key]->pais == "Ilhas Wallis e Futuna" ) selected @endif>Ilhas Wallis e Futuna</option>
                                        <option value="Ilhas do Pacífico (EUA)" @if( $enderecosB[$key]->pais == "Ilhas do Pacífico (EUA)" ) selected @endif>Ilhas do Pacífico (EUA)</option>
                                        <option value="Índia" @if( $enderecosB[$key]->pais == "Índia" ) selected @endif>Índia</option>
                                        <option value="Indonésia" @if( $enderecosB[$key]->pais == "Indonésia" ) selected @endif>Indonésia</option>
                                        <option value="Inglaterra" @if( $enderecosB[$key]->pais == "Inglaterra" ) selected @endif>Inglaterra</option>
                                        <option value="Irlanda" @if( $enderecosB[$key]->pais == "Irlanda" ) selected @endif>Irlanda</option>
                                        <option value="Islândia" @if( $enderecosB[$key]->pais == "Islândia" ) selected @endif>Islândia</option>
                                        <option value="Israel" @if( $enderecosB[$key]->pais == "Israel" ) selected @endif>Israel</option>
                                        <option value="Itália" @if( $enderecosB[$key]->pais == "Itália" ) selected @endif>Itália</option>
                                        <option value="Jamaica" @if( $enderecosB[$key]->pais == "Jamaica" ) selected @endif>Jamaica</option>
                                        <option value="Japão" @if( $enderecosB[$key]->pais == "Japão" ) selected @endif>Japão</option>
                                        <option value="Jordânia" @if( $enderecosB[$key]->pais == "Jordânia" ) selected @endif>Jordânia</option>
                                        <option value="Kuwait" @if( $enderecosB[$key]->pais == "Kuwait" ) selected @endif>Kuwait</option>
                                        <option value="Latvia" @if( $enderecosB[$key]->pais == "Latvia" ) selected @endif>Latvia</option>
                                        <option value="Líbano" @if( $enderecosB[$key]->pais == "Líbano" ) selected @endif>Líbano</option>
                                        <option value="Liechtenstein" @if( $enderecosB[$key]->pais == "Liechtenstein" ) selected @endif>Liechtenstein</option>
                                        <option value="Lituânia" @if( $enderecosB[$key]->pais == "Lituânia" ) selected @endif>Lituânia</option>
                                        <option value="Luxemburgo" @if( $enderecosB[$key]->pais == "Luxemburgo" ) selected @endif>Luxemburgo</option>
                                        <option value="Macau" @if( $enderecosB[$key]->pais == "Macau" ) selected @endif>Macau</option>
                                        <option value="Macedônia" @if( $enderecosB[$key]->pais == "Macedônia" ) selected @endif>Macedônia</option>
                                        <option value="Madagascar" @if( $enderecosB[$key]->pais == "Madagascar" ) selected @endif>Madagascar</option>
                                        <option value="Malásia" @if( $enderecosB[$key]->pais == "Malásia" ) selected @endif>Malásia</option>
                                        <option value="Malaui" @if( $enderecosB[$key]->pais == "Malaui" ) selected @endif>Malaui</option>
                                        <option value="Mali" @if( $enderecosB[$key]->pais == "Mali" ) selected @endif>Mali</option>
                                        <option value="Malta" @if( $enderecosB[$key]->pais == "Malta" ) selected @endif>Malta</option>
                                        <option value="Marrocos" @if( $enderecosB[$key]->pais == "Marrocos" ) selected @endif>Marrocos</option>
                                        <option value="Martinica" @if( $enderecosB[$key]->pais == "Martinica" ) selected @endif>Martinica</option>
                                        <option value="Mauritânia" @if( $enderecosB[$key]->pais == "Mauritânia" ) selected @endif>Mauritânia</option>
                                        <option value="Mauritius" @if( $enderecosB[$key]->pais == "Mauritius" ) selected @endif>Mauritius</option>
                                        <option value="México" @if( $enderecosB[$key]->pais == "México" ) selected @endif>México</option>
                                        <option value="Moldova" @if( $enderecosB[$key]->pais == "Moldova" ) selected @endif>Moldova</option>
                                        <option value="Mônaco" @if( $enderecosB[$key]->pais == "Mônaco" ) selected @endif>Mônaco</option>
                                        <option value="Montserrat" @if( $enderecosB[$key]->pais == "Montserrat" ) selected @endif>Montserrat</option>
                                        <option value="Nepal" @if( $enderecosB[$key]->pais == "Nepal" ) selected @endif>Nepal</option>
                                        <option value="Nicarágua" @if( $enderecosB[$key]->pais == "Nicarágua" ) selected @endif>Nicarágua</option>
                                        <option value="Niger" @if( $enderecosB[$key]->pais == "Niger" ) selected @endif>Niger</option>
                                        <option value="Nigéria" @if( $enderecosB[$key]->pais == "Nigéria" ) selected @endif>Nigéria</option>
                                        <option value="Noruega" @if( $enderecosB[$key]->pais == "Noruega" ) selected @endif>Noruega</option>
                                        <option value="Nova Caledônia" @if( $enderecosB[$key]->pais == "Nova Caledônia" ) selected @endif>Nova Caledônia</option>
                                        <option value="Nova Zelândia" @if( $enderecosB[$key]->pais == "Nova Zelândia" ) selected @endif>Nova Zelândia</option>
                                        <option value="Omã" @if( $enderecosB[$key]->pais == "Omã" ) selected @endif>Omã</option>
                                        <option value="Palau" @if( $enderecosB[$key]->pais == "Palau" ) selected @endif>Palau</option>
                                        <option value="Panamá" @if( $enderecosB[$key]->pais == "Panamá" ) selected @endif>Panamá</option>
                                        <option value="Papua-nova Guiné" @if( $enderecosB[$key]->pais == "Papua-nova Guiné" ) selected @endif>Papua-nova Guiné</option>
                                        <option value="Paquistão" @if( $enderecosB[$key]->pais == "Paquistão" ) selected @endif>Paquistão</option>
                                        <option value="Peru" @if( $enderecosB[$key]->pais == "Peru" ) selected @endif>Peru</option>
                                        <option value="Polinésia Francesa" @if( $enderecosB[$key]->pais == "Polinésia Francesa" ) selected @endif>Polinésia Francesa</option>
                                        <option value="Polônia" @if( $enderecosB[$key]->pais == "Polônia" ) selected @endif>Polônia</option>
                                        <option value="Porto Rico" @if( $enderecosB[$key]->pais == "Porto Rico" ) selected @endif>Porto Rico</option>
                                        <option value="Portugal" @if( $enderecosB[$key]->pais == "Portugal" ) selected @endif>Portugal</option>
                                        <option value="Qatar" @if( $enderecosB[$key]->pais == "Qatar" ) selected @endif>Qatar</option>
                                        <option value="Quênia" @if( $enderecosB[$key]->pais == "Quênia" ) selected @endif>Quênia</option>
                                        <option value="Rep. Dominicana" @if( $enderecosB[$key]->pais == "Rep. Dominicana" ) selected @endif>Rep. Dominicana</option>
                                        <option value="Rep. Tcheca" @if( $enderecosB[$key]->pais == "Rep. Tcheca" ) selected @endif>Rep. Tcheca</option>
                                        <option value="Reunion" @if( $enderecosB[$key]->pais == "Reunion" ) selected @endif>Reunion</option>
                                        <option value="Romênia" @if( $enderecosB[$key]->pais == "Romênia" ) selected @endif>Romênia</option>
                                        <option value="Ruanda" @if( $enderecosB[$key]->pais == "Ruanda" ) selected @endif>Ruanda</option>
                                        <option value="Rússia" @if( $enderecosB[$key]->pais == "Rússia" ) selected @endif>Rússia</option>
                                        <option value="Saipan" @if( $enderecosB[$key]->pais == "Saipan" ) selected @endif>Saipan</option>
                                        <option value="Samoa Americana" @if( $enderecosB[$key]->pais == "Samoa Americana" ) selected @endif>Samoa Americana</option>
                                        <option value="Senegal" @if( $enderecosB[$key]->pais == "Senegal" ) selected @endif>Senegal</option>
                                        <option value="Serra Leone" @if( $enderecosB[$key]->pais == "Serra Leone" ) selected @endif>Serra Leone</option>
                                        <option value="Seychelles" @if( $enderecosB[$key]->pais == "Seychelles" ) selected @endif>Seychelles</option>
                                        <option value="Singapura" @if( $enderecosB[$key]->pais == "Singapura" ) selected @endif>Singapura</option>
                                        <option value="Síria" @if( $enderecosB[$key]->pais == "Síria" ) selected @endif>Síria</option>
                                        <option value="Sri Lanka" @if( $enderecosB[$key]->pais == "Sri Lanka" ) selected @endif>Sri Lanka</option>
                                        <option value="St. Kitts & Nevis" @if( $enderecosB[$key]->pais == "St. Kitts & Nevis" ) selected @endif>St. Kitts & Nevis</option>
                                        <option value="St. Lúcia" @if( $enderecosB[$key]->pais == "St. Lúcia" ) selected @endif>St. Lúcia</option>
                                        <option value="St. Vincent" @if( $enderecosB[$key]->pais == "St. Vincent" ) selected @endif>St. Vincent</option>
                                        <option value="Sudão" @if( $enderecosB[$key]->pais == "Sudão" ) selected @endif>Sudão</option>
                                        <option value="Suécia" @if( $enderecosB[$key]->pais == "Suécia" ) selected @endif>Suécia</option>
                                        <option value="Suíça" @if( $enderecosB[$key]->pais == "Suíça" ) selected @endif>Suíça</option>
                                        <option value="Suriname" @if( $enderecosB[$key]->pais == "Suriname" ) selected @endif>Suriname</option>
                                        <option value="Tailândia" @if( $enderecosB[$key]->pais == "Tailândia" ) selected @endif>Tailândia</option>
                                        <option value="Taiwan" @if( $enderecosB[$key]->pais == "Taiwan" ) selected @endif>Taiwan</option>
                                        <option value="Tanzânia" @if( $enderecosB[$key]->pais == "Tanzânia" ) selected @endif>Tanzânia</option>
                                        <option value="Togo" @if( $enderecosB[$key]->pais == "Togo" ) selected @endif>Togo</option>
                                        <option value="TrinidBd & Tobago" @if( $enderecosB[$key]->pais == "TrinidBd & Tobago" ) selected @endif>TrinidBd & Tobago</option>
                                        <option value="Tunísia" @if( $enderecosB[$key]->pais == "Tunísia" ) selected @endif>Tunísia</option>
                                        <option value="Turquia" @if( $enderecosB[$key]->pais == "Turquia" ) selected @endif>Turquia</option>
                                        <option value="Ucrânia" @if( $enderecosB[$key]->pais == "Ucrânia" ) selected @endif>Ucrânia</option>
                                        <option value="Uganda" @if( $enderecosB[$key]->pais == "Uganda" ) selected @endif>Uganda</option>
                                        <option value="Uruguai" @if( $enderecosB[$key]->pais == "Uruguai" ) selected @endif>Uruguai</option>
                                        <option value="Venezuela" @if( $enderecosB[$key]->pais == "Venezuela" ) selected @endif>Venezuela</option>
                                        <option value="Vietnã" @if( $enderecosB[$key]->pais == "Vietnã" ) selected @endif>Vietnã</option>
                                        <option value="Zaire" @if( $enderecosB[$key]->pais == "Zaire" ) selected @endif>Zaire</option>
                                        <option value="Zâmbia" @if( $enderecosB[$key]->pais == "Zâmbia" ) selected @endif>Zâmbia</option>
                                        <option value="Zimbábue" @if( $enderecosB[$key]->pais == "Zimbábue" ) selected @endif>Zimbábue</option>
                                    </select>
                                    <label for="paisB{{$enderecosB[$key]->fk_id}}" class="mt-1">{{$idB}} - Endereço {{$key+1}} - País: {{$enderecosB[$key]->pais}}</label><br/>
                                    <label for="estadoB{{$enderecosB[$key]->fk_id}}"><strong>Estado</strong></label>
                                    <select id="estadoB{{$enderecosB[$key]->fk_id}}" name="enderecosTxt[{{$enderecosB[$key]->id}}][estado]" class="form-control">
                                        <option value="" @if( $enderecosB[$key]->estado == "" ) selected @endif>
                                            Selecione
                                        </option>
                                        <option value="AC" @if( $enderecosB[$key]->estado == "AC" ) selected @endif>
                                            Acre
                                        </option>
                                        <option value="AL" @if( $enderecosB[$key]->estado == "AL" ) selected @endif>
                                            Alagoas
                                        </option>
                                        <option value="AP" @if( $enderecosB[$key]->estado == "AP" ) selected @endif>
                                            Amapá
                                        </option>
                                        <option value="AM" @if( $enderecosB[$key]->estado == "AM" ) selected @endif>
                                            Amazonas
                                        </option>
                                        <option value="BA" @if( $enderecosB[$key]->estado == "BA" ) selected @endif>
                                            Bahia
                                        </option>
                                        <option value="CE" @if( $enderecosB[$key]->estado == "CE" ) selected @endif>
                                            Ceará
                                        </option>
                                        <option value="DF" @if( $enderecosB[$key]->estado == "DF" ) selected @endif>
                                            Distrito Federal
                                        </option>
                                        <option value="ES" @if( $enderecosB[$key]->estado == "ES" ) selected @endif>
                                            Espírito Santo
                                        </option>
                                        <option value="GO" @if( $enderecosB[$key]->estado == "GO" ) selected @endif>
                                            Goiás
                                        </option>
                                        <option value="MA" @if( $enderecosB[$key]->estado == "MA" ) selected @endif>
                                            Maranhão
                                        </option>
                                        <option value="MT" @if( $enderecosB[$key]->estado == "MT" ) selected @endif>
                                            Mato Grosso
                                        </option>
                                        <option value="MS" @if( $enderecosB[$key]->estado == "MS" ) selected @endif>
                                            Mato Grosso do Sul
                                        </option>
                                        <option value="MG" @if( $enderecosB[$key]->estado == "MG" ) selected @endif>
                                            Minas Gerais
                                        </option>
                                        <option value="PA" @if( $enderecosB[$key]->estado == "PA" ) selected @endif>
                                            Pará
                                        </option>
                                        <option value="PB" @if( $enderecosB[$key]->estado == "PB" ) selected @endif>
                                            Paraíba
                                        </option>
                                        <option value="PR" @if( $enderecosB[$key]->estado == "PR" ) selected @endif>
                                            Paraná
                                        </option>
                                        <option value="PE" @if( $enderecosB[$key]->estado == "PE" ) selected @endif>
                                            Pernambuco
                                        </option>
                                        <option value="PI" @if( $enderecosB[$key]->estado == "PI" ) selected @endif>
                                            Piauí
                                        </option>
                                        <option value="RJ" @if( $enderecosB[$key]->estado == "RJ" ) selected @endif>
                                            Rio de Janeiro
                                        </option>
                                        <option value="RN" @if( $enderecosB[$key]->estado == "RN" ) selected @endif>
                                            Rio Grande do Norte
                                        </option>
                                        <option value="RS" @if( $enderecosB[$key]->estado == "RS" ) selected @endif>
                                            Rio Grande do Sul
                                        </option>
                                        <option value="RO" @if( $enderecosB[$key]->estado == "RO" ) selected @endif>
                                            Rondônia
                                        </option>
                                        <option value="RR" @if( $enderecosB[$key]->estado == "RR" ) selected @endif>
                                            Roraima
                                        </option>
                                        <option value="SC" @if( $enderecosB[$key]->estado == "SC" ) selected @endif>
                                            Santa Catarina
                                        </option>
                                        <option value="SP" @if( $enderecosB[$key]->estado == "SP" ) selected @endif>
                                            São Paulo
                                        </option>
                                        <option value="SE" @if( $enderecosB[$key]->estado == "SE" ) selected @endif>
                                            Sergipe
                                        </option>
                                        <option value="TO" @if( $enderecosB[$key]->estado == "TO" ) selected @endif>
                                            Tocantins
                                        </option>
                                    </select>
                                    <label for="estadoB{{$enderecosB[$key]->fk_id}}" class="mt-1">{{$idB}} - Endereço {{$key+1}} - Estado: {{$enderecosB[$key]->estado}}</label><br/>
                                    <label for="cidadeB{{$enderecosB[$key]->fk_id}}"><strong>Cidade</strong></label>
                                    <input type="text" id="cidadeB{{$enderecosB[$key]->fk_id}}" name="enderecosTxt[{{$enderecosB[$key]->id}}][cidade]" class="form-control" value="{{$enderecosB[$key]->cidade}}" placeholder="Cidade">
                                    <label for="cidadeB{{$enderecosB[$key]->fk_id}}" class="mt-1">{{$idB}} - Endereço {{$key+1}} - Cidade: {{$enderecosB[$key]->cidade}}</label><br/>
                                    <label for="bairroB{{$enderecosB[$key]->fk_id}}"><strong>Bairro</strong></label>
                                    <input type="text" id="bairroB{{$enderecosB[$key]->fk_id}}" name="enderecosTxt[{{$enderecosB[$key]->id}}][bairro]" class="form-control" value="{{$enderecosB[$key]->bairro}}" placeholder="Bairro">
                                    <label for="bairroB{{$enderecosB[$key]->fk_id}}" class="mt-1">{{$idB}} - Endereço {{$key+1}} - Bairro: {{$enderecosB[$key]->bairro}}</label><br/>
                                    <label for="cepB{{$enderecosB[$key]->fk_id}}"><strong>CEP</strong></label>
                                    <input type="text" id="cepB{{$enderecosB[$key]->fk_id}}" name="enderecosTxt[{{$enderecosB[$key]->id}}][cep]" class="form-control ceps" value="{{$enderecosB[$key]->cep}}" placeholder="CEP">
                                    <label for="cepB{{$enderecosB[$key]->fk_id}}" class="mt-1">{{$idB}} - Endereço {{$key+1}} - CEP: {{$enderecosB[$key]->cep}}</label><br/>
                                    <label for="logradouroB{{$enderecosB[$key]->fk_id}}"><strong>Logradouro</strong></label>
                                    <input type="text" id="logradouroB{{$enderecosB[$key]->fk_id}}" name="enderecosTxt[{{$enderecosB[$key]->id}}][logradouro]" class="form-control" value="{{$enderecosB[$key]->logradouro}}" placeholder="Logradouro">
                                    <label for="logradouroB{{$enderecosB[$key]->fk_id}}" class="mt-1">{{$idB}} - Endereço {{$key+1}} - Logradouro: {{$enderecosB[$key]->logradouro}}</label><br/>
                                    <label for="numeroB{{$enderecosB[$key]->fk_id}}"><strong>Número</strong></label>
                                    <input type="text" id="numeroB{{$enderecosB[$key]->fk_id}}" name="enderecosTxt[{{$enderecosB[$key]->id}}][numero]" class="form-control" value="{{$enderecosB[$key]->numero}}" placeholder="Número">
                                    <label for="numeroB{{$enderecosB[$key]->fk_id}}" class="mt-1">{{$idB}} - Endereço {{$key+1}} - Número: {{$enderecosB[$key]->numero}}</label><br/>
                                    <label for="complementoB{{$enderecosB[$key]->fk_id}}"><strong>Complemento</strong></label>
                                    <input type="text" id="complementoB{{$enderecosB[$key]->fk_id}}" name="enderecosTxt[{{$enderecosB[$key]->id}}][complemento]" class="form-control" value="{{$enderecosB[$key]->complemento}}" placeholder="Complemento">
                                    <label for="complementoB{{$enderecosB[$key]->fk_id}}" class="mt-1">{{$idB}} - Endereço {{$key+1}} - Complemento: {{$enderecosB[$key]->complemento}}</label><br/>
                                    <label for="latitudeB{{$enderecosB[$key]->fk_id}}"><strong>Latitude</strong></label>
                                    <input type="text" id="latitudeB{{$enderecosB[$key]->fk_id}}" name="enderecosTxt[{{$enderecosB[$key]->id}}][latitude]" class="form-control" value="{{$enderecosB[$key]->latitude}}" placeholder="Latitude">
                                    <label for="latitudeB{{$enderecosB[$key]->fk_id}}" class="mt-1">{{$idB}} - Endereço {{$key+1}} - Latitude: {{$enderecosB[$key]->latitude}}</label><br/>
                                    <label for="longitudeB{{$enderecosB[$key]->fk_id}}"><strong>Longitude</strong></label>
                                    <input type="text" id="longitudeB{{$enderecosB[$key]->fk_id}}" name="enderecosTxt[{{$enderecosB[$key]->id}}][longitude]" class="form-control" value="{{$enderecosB[$key]->longitude}}" placeholder="Longitude">
                                    <label for="longitudeB{{$enderecosB[$key]->fk_id}}" class="mt-1">{{$idB}} - Endereço {{$key+1}} - Longitude: {{$enderecosB[$key]->longitude}}</label><br/>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Selecionar todos</button>--}}
                {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-square"></i> Desselecionar todos</button>--}}
            </div>
        @endif

        {{-- mae --}}
        <div id="nomeMae" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="mae"><strong>Nome da Mãe</strong></label>
                    <input type="text" name="mae" class="form-control" id="mae" value="{{$dadosA->mae}}" placeholder="Nome da Mãe">
                    <label for="mae" id="maeUsarA" class="mt-1">{{$idA}} - Nome da Mãe: {{$dadosA->mae}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="nomeMaeB">{{$idB}} - Nome da Mãe</label>
                    <input type="text" name="nomeMaeB" disabled class="form-control" id="nomeMaeB" value="{{$dadosB->mae}}">
                </div>
            </div>
            <button type="button" id="buttonMaeUsarA" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>
            <button type="button" id="buttonMaeUsarB" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>
        </div>

        {{-- pai --}}
        <div id="nomePai" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="pai"><strong>Nome do Pai</strong></label>
                    <input type="text" name="pai" class="form-control" id="pai" value="{{$dadosA->pai}}" placeholder="Nome do Pai">
                    <label for="pai" id="paiUsarA" class="mt-1">{{$idA}} - Nome do Pai: {{$dadosA->pai}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="nomePaiB">{{$idB}} - Nome do Pai</label>
                    <input type="text" name="nomePaiB" disabled class="form-control" id="nomePaiB" value="{{$dadosB->pai}}">
                </div>
            </div>
            <button type="button" id="buttonPaiUsarA" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>
            <button type="button" id="buttonPaiUsarB" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>
        </div>

        {{-- Lotação (unidade, pavilhão, ala e cela) --}}
        <div id="lotacao" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-3">
                    <label for="unidade_prisional"><strong>Unidade Prisional</strong></label>
                    <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade" required>
                        <option value="" @if( $dadosA->unidade_prisional == "" ) selected @endif>
                            Selecione
                        </option>
                        @foreach($unidades as $unidade)
                            <option value="{{ $unidade->id }}" @if($unidade->id == $dadosA->unidade_prisional ) selected @endif>
                                {{ $unidade->sigla }}
                            </option>
                        @endforeach
                    </select>
                    <label for="unidade_prisional" class="mt-1">{{ $dadosA->id }} - Unidade Prisional: {{ \App\Http\Controllers\Unidades\UnidadesController::getSiglaUp($dadosA->unidade_prisional) }}</label>
                </div>

                <div class="form-group col-3">
                    <label for="pavilhao"><strong>Pavilhão</strong></label>
                    <select id="pavilhao" name="pavilhao" class="form-control selectUnidade">
                        @if( $dadosA->pavilhao == "" )
                            <option value="" selected>
                                Selecione
                            </option>
                        @else
                            <option value="{{ $dadosA->pavilhao }}" selected>
                                {{ \App\Http\Controllers\Unidades\UnidadesController::getNomePav($dadosA->pavilhao) }}
                            </option>
                        @endif
                    </select>
                    <label for="pavilhao" class="mt-1">{{ $dadosA->id }} - Pavilhão: {{ \App\Http\Controllers\Unidades\UnidadesController::getNomePav($dadosA->pavilhao) }}</label>
                </div>

                <div class="form-group col-3">
                    <label for="ala"><strong>Ala</strong></label>
                    <select id="ala" name="ala" class="form-control selectUnidade">
                        @if( $dadosA->ala == "" )
                            <option value="" selected>
                                Selecione
                            </option>
                        @else
                            <option value="{{ $dadosA->ala }}" selected>
                                {{ \App\Http\Controllers\Unidades\UnidadesController::getNomeAla($dadosA->ala) }}
                            </option>
                        @endif
                    </select>
                    <label for="ala" class="mt-1">{{ $dadosA->id }} - Ala: {{ \App\Http\Controllers\Unidades\UnidadesController::getNomeAla($dadosA->ala) }}</label>
                </div>

                <div class="form-group col-3">
                    <label for="cela"><strong>Cela</strong></label>
                    <select id="cela" name="cela" class="form-control">
                        @if( $dadosA->cela == "" )
                            <option value="" selected>
                                Selecione
                            </option>
                        @else
                            <option value="{{ $dadosA->cela }}" selected>
                                {{ \App\Http\Controllers\Unidades\UnidadesController::getNomeCela($dadosA->cela) }}
                            </option>
                        @endif
                    </select>
                    <label for="cela" class="mt-1">{{ $dadosA->id }} - Ala: {{ \App\Http\Controllers\Unidades\UnidadesController::getNomeCela($dadosA->cela) }}</label>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-3">
                    <label for="unidade_prisionalB">{{ $dadosB->id }} - Unidade Prisional</label>
                    <select id="unidade_prisionalB" name="unidade_prisionalB" class="form-control selectUnidade" disabled>
                            <option value="{{ $dadosB->unidade_prisional }}" selected>
                                {{ \App\Http\Controllers\Unidades\UnidadesController::getSiglaUp($dadosB->unidade_prisional) }}
                            </option>
                    </select>
                </div>

                <div class="form-group col-3">
                    <label for="pavilhaoB">{{ $dadosB->id }} - Pavilhão</label>
                    <select id="pavilhaoB" name="pavilhaoB" class="form-control selectUnidade" disabled>
                        <option value="{{ $dadosB->pavilhao }}" selected>
                            {{ \App\Http\Controllers\Unidades\UnidadesController::getNomePav($dadosB->pavilhao) }}
                        </option>
                    </select>
                </div>

                <div class="form-group col-3">
                    <label for="alaB">{{ $dadosB->id }} - Ala</label>
                    <select id="alaB" name="alaB" class="form-control selectUnidade" disabled>
                        <option value="{{ $dadosB->ala }}" selected>
                            {{ \App\Http\Controllers\Unidades\UnidadesController::getNomeAla($dadosB->ala) }}
                        </option>
                    </select>
                </div>

                <div class="form-group col-3">
                    <label for="celaB">{{ $dadosB->id }} - Cela</label>
                    <select id="celaB" name="celaB" class="form-control" disabled>
                        <option value="{{ $dadosB->cela }}" selected>
                            {{ \App\Http\Controllers\Unidades\UnidadesController::getNomeCela($dadosB->cela) }}
                        </option>
                    </select>
                </div>
            </div>

            {{--<button type="button" class="btn btn-facebook mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- sexo --}}
        <div id="sexoDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="sexo"><strong>Sexo</strong></label>
                    <select id="sexo" name="sexo" class="form-control" required>
                        <option value="" @if( $dadosA->sexo == "" ) selected @endif>
                            Selecione
                        </option>
                        <option value="M" @if( $dadosA->sexo == "M" ) selected @endif>
                            Masculino
                        </option>
                        <option value="F" @if( $dadosA->sexo == "F" ) selected @endif>
                            Feminino
                        </option>
                    </select>
                    <label for="sexo" class="mt-1">{{$idA}} - Sexo: {{ $dadosA->sexo=="M"?"Masculino":($dadosA->sexo=="F"?"Feminino":"Não Informado") }}</label>
                </div>
                <div class="form-group col-6">
                    <label for="sexoB">{{$idB}} - Sexo</label>
                    <input type="text" name="sexoB" disabled class="form-control" id="sexoB" value="{{ $dadosB->sexo=="M"?"Masculino":($dadosB->sexo=="F"?"Feminino":"Não Informado") }}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- cpf --}}
        <div id="cpfDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="cpf"><strong>CPF</strong></label>
                    <input type="text" name="cpf" class="form-control" id="cpf" value="{{$dadosA->cpf}}" placeholder="CPF">
                    <label for="cpf" id="cpfUsarA" class="mt-1">{{$idA}} - CPF: {{$dadosA->cpf}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="cpfB">{{$idB}} - CPF</label>
                    <input type="text" name="cpfB" disabled class="form-control" id="cpfB" value="{{$dadosB->cpf}}">
                </div>
            </div>
            <button type="button" id="buttonCpfUsarA" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>
            <button type="button" id="buttonCpfUsarB" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>
        </div>

        {{-- rg_numero --}}
        <div id="rg_numeroDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="rg_numero"><strong>Número do RG</strong></label>
                    <input type="text" name="rg_numero" class="form-control" id="rg_numero" value="{{$dadosA->rg_numero}}" placeholder="Número do RG">
                    <label for="rg_numero" id="rg_numeroUsarA" class="mt-1">{{$idA}} - Número do RG: {{$dadosA->rg_numero}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="rg_numeroB">{{$idB}} - Número do RG</label>
                    <input type="text" name="rg_numeroB" disabled class="form-control" id="rg_numeroB" value="{{$dadosB->rg_numero}}">
                </div>
            </div>
            <button type="button" id="buttonrg_numeroUsarA" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>
            <button type="button" id="buttonrg_numeroUsarB" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>
        </div>

        {{-- rg_orgaoexpedidor --}}
        <div id="rg_orgaoexpedidorDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="rg_orgaoexpedidor"><strong>Órgão Expedidor do RG</strong></label>
                    <input type="text" name="rg_orgaoexpedidor" class="form-control" id="rg_orgaoexpedidor" value="{{$dadosA->rg_orgaoexpedidor}}" placeholder="Órgão Expedidor do RG">
                    <label for="rg_orgaoexpedidor" id="rg_orgaoexpedidorUsarA" class="mt-1">{{$idA}} - Órgão Expedidor do RG: {{$dadosA->rg_orgaoexpedidor}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="rg_orgaoexpedidorB">{{$idB}} - Órgão Expedidor do RG</label>
                    <input type="text" name="rg_orgaoexpedidorB" disabled class="form-control" id="rg_orgaoexpedidorB" value="{{$dadosB->rg_orgaoexpedidor}}">
                </div>
            </div>
            <button type="button" id="buttonrg_orgaoexpedidorUsarA" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>
            <button type="button" id="buttonrg_orgaoexpedidorUsarB" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>
        </div>

        {{-- rg_data --}}
        <div id="rg_dataDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="rg_data"><strong>Data de Expedição do RG</strong></label>
                    <input type="text" name="rg_data" class="form-control" id="rg_data" value="{{$dadosA->rg_data}}" placeholder="Data de Expedição do RG">
                    <label for="rg_data" id="rg_dataUsarA" class="mt-1">{{$idA}} - Data de Expedição do RG: {{$dadosA->rg_data}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="rg_dataB">{{$idB}} - Data de Expedição do RG</label>
                    <input type="text" name="rg_dataB" disabled class="form-control" id="rg_dataB" value="{{$dadosB->rg_data}}">
                </div>
            </div>
            <button type="button" id="buttonrg_dataUsarA" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>
            <button type="button" id="buttonrg_dataUsarB" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>
        </div>

        {{-- passaporte --}}
        <div id="passaportenumDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="passaportenum"><strong>Nº do Passaporte</strong></label>
                    <input type="text" name="passaportenum" class="form-control" id="passaportenum" value="{{$dadosA->passaportenum}}" placeholder="Nº do Passaporte">
                    <label for="passaportenum" id="passaportenumUsarA" class="mt-1">{{$idA}} - Nº do Passaporte: {{$dadosA->passaportenum}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="passaportenumB">{{$idB}} - Nº do Passaporte</label>
                    <input type="text" name="passaportenumB" disabled class="form-control" id="passaportenumB" value="{{$dadosB->passaportenum}}">
                </div>
            </div>
            <button type="button" id="buttonpassaportenumUsarA" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>
            <button type="button" id="buttonpassaportenumUsarB" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>
        </div>

        {{-- cartaosus --}}
        <div id="cartaosusDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="cartaosus"><strong>Nº do Cartão SUS</strong></label>
                    <input type="text" name="cartaosus" class="form-control" id="cartaosus" value="{{$dadosA->cartaosus}}" placeholder="Nº do Cartão SUS">
                    <label for="cartaosus" id="cartaosusUsarA" class="mt-1">{{$idA}} - Nº do Cartão SUS: {{$dadosA->cartaosus}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="cartaosusB">{{$idB}} - Nº do Cartão SUS</label>
                    <input type="text" name="cartaosusB" disabled class="form-control" id="cartaosusB" value="{{$dadosB->cartaosus}}">
                </div>
            </div>
            <button type="button" id="buttoncartaosusUsarA" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>
            <button type="button" id="buttoncartaosusUsarB" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>
        </div>

        {{-- estado_civil --}}
        <div id="estado_civilDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="estado_civil"><strong>Estado Civil</strong></label>
                    <select id="estado_civil" name="estado_civil" class="form-control">
                        <option value="" @if( $dadosA->estado_civil == "" ) selected @endif>
                            Selecione
                        </option>
                        <option value="solteiro" @if( $dadosA->estado_civil == "solteiro" ) selected @endif>
                            Solteiro(a)
                        </option>
                        <option value="casado" @if( $dadosA->estado_civil == "casado" ) selected @endif>
                            Casado(a)
                        </option>
                        <option value="uniao estavel" @if( $dadosA->estado_civil == "uniao estavel" ) selected @endif>
                            União Estável
                        </option>
                        <option value="separado" @if( $dadosA->estado_civil == "separado" ) selected @endif>
                            Separado(a)
                        </option>
                        <option value="divorciado" @if( $dadosA->estado_civil == "divorciado" ) selected @endif>
                            Divorciado(a)
                        </option>
                        <option value="viuvo" @if( $dadosA->estado_civil == "viuvo" ) selected @endif>
                            Viúvo(a)
                        </option>
                    </select>
                    <label for="estado_civil" class="mt-1">{{$idA}} - Estado Civil: {{$dadosA->estado_civil}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="estado_civilB">{{$idB}} - Estado Civil</label>
                    <input type="text" name="estado_civilB" disabled class="form-control" id="estado_civilB" value="{{$dadosB->estado_civil}}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- altura --}}
        <div id="alturaDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="altura"><strong>Altura</strong></label>
                    <input type="text" name="altura" class="form-control" id="altura" value="{{$dadosA->altura}}" placeholder="Altura">
                    <label for="altura" id="alturaUsarA" class="mt-1">{{$idA}} - Altura: {{$dadosA->altura}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="alturaB">{{$idB}} - Altura</label>
                    <input type="text" name="alturaB" disabled class="form-control" id="alturaB" value="{{$dadosB->altura}}">
                </div>
            </div>
            <button type="button" id="buttonAlturaUsarA" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>
            <button type="button" id="buttonAlturaUsarB" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>
        </div>

        {{-- peso --}}
        <div id="pesoDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="peso"><strong>Peso</strong></label>
                    <input type="text" name="peso" class="form-control" id="peso" value="{{$dadosA->peso}}" placeholder="Peso">
                    <label for="peso" id="pesoUsarA" class="mt-1">{{$idA}} - Peso: {{$dadosA->peso}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="pesoB">{{$idB}} - Peso</label>
                    <input type="text" name="pesoB" disabled class="form-control" id="pesoB" value="{{$dadosB->peso}}">
                </div>
            </div>
            <button type="button" id="buttonPesoUsarA" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>
            <button type="button" id="buttonPesoUsarB" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>
        </div>

        {{-- contato --}}


        {{-- profissao --}}
        <div id="profissaoDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="profissao"><strong>Profissão</strong></label>
                    <select id="profissao" name="profissao" class="form-control">
                        <option value="" @if( $dadosA->profissao == "" ) selected @endif>
                            Selecione
                        </option>
                        @foreach($profissoes as $prof)
                            <option value="{{ $prof->id }}" @if( $dadosA->profissao == $prof->id ) selected @endif>
                                {{ $prof->nome }}
                            </option>
                        @endforeach
                    </select>
                    <label for="profissao" class="mt-1">{{$idA}} - Profissão: {{\App\Http\Controllers\Interno\InternoController::getNomeProfissao($dadosA->profissao)}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="profissaoB">{{$idB}} - Profissão</label>
                    <input type="text" name="profissaoB" disabled class="form-control" id="profissaoB" value="{{\App\Http\Controllers\Interno\InternoController::getNomeProfissao($dadosB->profissao)}}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>


        <div id="etniaDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="etnia"><strong>Cor da Pele</strong></label>
                    <select id="etnia" name="etnia" class="form-control">
                        <option value="" @if( $dadosA->etnia == "" ) selected @endif>
                            Selecione
                        </option>
                        <option value="Branca" @if( $dadosA->etnia == "Branca" ) selected @endif>
                            Branca
                        </option>
                        <option value="Indígena" @if( $dadosA->etnia == "Indígena" ) selected @endif>
                            Indígena
                        </option>
                        <option value="Preta" @if( $dadosA->etnia == "Preta" ) selected @endif>
                            Preta
                        </option>
                        <option value="Parda" @if( $dadosA->etnia == "Parda" ) selected @endif>
                            Parda
                        </option>
                        <option value="Amarela" @if( $dadosA->etnia == "Amarela" ) selected @endif>
                            Amarela
                        </option>
                    </select>
                    <label for="etnia" class="mt-1">{{$idA}} - Cor dos Olhos: {{$dadosA->etnia}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="etniaB">{{$idB}} - Cor da Pele</label>
                    <input type="text" name="etniaB" disabled class="form-control" id="etniaB" value="{{$dadosB->etnia}}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- cor_olhos --}}
        <div id="cor_olhosDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="cor_olhos"><strong>Cor dos Olhos</strong></label>
                    <select id="cor_olhos" name="cor_olhos" class="form-control">
                        <option value="" @if( $dadosA->cor_olhos == "" ) selected @endif>
                            Selecione
                        </option>
                        <option value="Azul" @if( $dadosA->cor_olhos == "Azul" ) selected @endif>
                            Azul
                        </option>
                        <option value="Azul-esverdeado" @if( $dadosA->cor_olhos == "Azul-esverdeado" ) selected @endif>
                            Azul-esverdeado
                        </option>
                        <option value="Castanho" @if( $dadosA->cor_olhos == "Castanho" ) selected @endif>
                            Castanho
                        </option>
                        <option value="Castanho-esverdeado" @if( $dadosA->cor_olhos == "Castanho-esverdeado" ) selected @endif>
                            Castanho-esverdeado
                        </option>
                        <option value="Castanho-claro" @if( $dadosA->cor_olhos == "Castanho-claro" ) selected @endif>
                            Castanho-claro
                        </option>
                        <option value="Castanho-escuro" @if( $dadosA->cor_olhos == "Castanho-escuro" ) selected @endif>
                            Castanho-escuro
                        </option>
                        <option value="Mel" @if( $dadosA->cor_olhos == "Mel" ) selected @endif>
                            Mel
                        </option>
                        <option value="Preto" @if( $dadosA->cor_olhos == "Preto" ) selected @endif>
                            Preto
                        </option>
                        <option value="Verde" @if( $dadosA->cor_olhos == "Verde" ) selected @endif>
                            Verde
                        </option>
                    </select>
                    <label for="cor_olhos" class="mt-1">{{$idA}} - Cor dos Olhos: {{$dadosA->cor_olhos}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="cor_olhosB">{{$idB}} - Cor dos Olhos</label>
                    <input type="text" name="cor_olhosB" disabled class="form-control" id="cor_olhosB" value="{{$dadosB->cor_olhos}}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- cor_cabelos --}}
        <div id="cor_cabelosDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="cor_cabelos"><strong>Cor dos Cabelos</strong></label>
                    <input type="text" name="cor_cabelos" class="form-control" id="cor_cabelos" value="{{$dadosA->cor_cabelos}}" placeholder="Cor dos Cabelos">
                    <label for="cor_cabelos" id="cor_cabelosUsarA" class="mt-1">{{$idA}} - Cor dos Cabelos: {{$dadosA->cor_cabelos}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="cor_cabelosB">{{$idB}} - Cor dos Cabelos</label>
                    <input type="text" name="cor_cabelosB" disabled class="form-control" id="cor_cabelosB" value="{{$dadosB->cor_cabelos}}">
                </div>
            </div>
            <button type="button" id="buttoncor_cabelosUsarA" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>
            <button type="button" id="buttoncor_cabelosUsarB" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>
        </div>

        {{-- data_nascimento --}}
        <div id="data_nascimentoDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="data_nascimento"><strong>Data de Nascimento</strong></label>
                    <input type="text" name="data_nascimento" class="form-control" id="data_nascimento" value="{{$dadosA->data_nascimento}}" placeholder="Data de Nascimento">
                    <label for="data_nascimento" id="data_nascimentoUsarA" class="mt-1">{{$idA}} - Data de Nascimento: {{$dadosA->data_nascimento}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="data_nascimentoB">{{$idB}} - Data de Nascimento</label>
                    <input type="text" name="data_nascimentoB" disabled class="form-control" id="data_nascimentoB" value="{{$dadosB->data_nascimento}}">
                </div>
            </div>
            <button type="button" id="buttondata_nascimentoUsarA" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>
            <button type="button" id="buttondata_nascimentoUsarB" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>
        </div>

        {{-- escolaridade --}}
        <div id="escolaridadeDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="escolaridade"><strong>Escolaridade</strong></label>
                    <select id="escolaridade" name="escolaridade" class="form-control">
                        <option value="" @if( $dadosA->escolaridade == "" ) selected @endif>
                            Selecione
                        </option>
                        <option value="Analfabeto" @if( $dadosA->escolaridade == "Analfabeto" ) selected @endif>
                            Analfabeto
                        </option>
                        <option value="Alfabetizado" @if( $dadosA->escolaridade == "Alfabetizado" ) selected @endif>
                            Alfabetizado
                        </option>
                        <option value="Ensino Fundamental Incompleto" @if( $dadosA->escolaridade == "Ensino Fundamental Incompleto" ) selected @endif>
                            Ensino Fundamental Incompleto
                        </option>
                        <option value="Ensino Fundamental Completo" @if( $dadosA->escolaridade == "Ensino Fundamental Completo" ) selected @endif>
                            Ensino Fundamental Completo
                        </option>
                        <option value="Ensino Médio Incompleto" @if( $dadosA->escolaridade == "Ensino Médio Incompleto" ) selected @endif>
                            Ensino Médio Incompleto
                        </option>
                        <option value="Ensino Médio Completo" @if( $dadosA->escolaridade == "Ensino Médio Completo" ) selected @endif>
                            Ensino Médio Completo
                        </option>
                        <option value="Ensino Superior Incompleto" @if( $dadosA->escolaridade == "Ensino Superior Incompleto" ) selected @endif>
                            Ensino Superior Incompleto
                        </option>
                        <option value="Ensino Superior Completo" @if( $dadosA->escolaridade == "Ensino Superior Completo" ) selected @endif>
                            Ensino Superior Completo
                        </option>
                        <option value="Pós-graduação lato sensu" @if( $dadosA->escolaridade == "Pós-graduação lato sensu" ) selected @endif>
                            Pós-graduação lato sensu
                        </option>
                        <option value="Mestrado" @if( $dadosA->escolaridade == "Mestrado" ) selected @endif>
                            Mestrado
                        </option>
                        <option value="Doutorado" @if( $dadosA->escolaridade == "Doutorado" ) selected @endif>
                            Doutorado
                        </option>
                        <option value="Pós-Doutorado" @if( $dadosA->escolaridade == "Pós-Doutorado" ) selected @endif>
                            Pós-Doutorado
                        </option>
                    </select>
                    <label for="escolaridade" class="mt-1">{{$idA}} - Escolaridade: {{$dadosA->escolaridade}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="escolaridadeB">{{$idB}} - Escolaridade</label>
                    <input type="text" name="escolaridadeB" disabled class="form-control" id="escolaridadeB" value="{{$dadosB->escolaridade}}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- sentenciado --}}
        <div id="sentenciadoDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="sentenciado"><strong>Sentenciado</strong></label>
                    <select id="sentenciado" name="sentenciado" class="form-control">
                        <option value="" @if( $dadosA->sentenciado == "") selected @endif>
                            Selecione
                        </option>
                        <option value="S" @if( $dadosA->sentenciado == "S") selected @endif>
                            Sim
                        </option>
                        <option value="N" @if( $dadosA->sentenciado == "N") selected @endif>
                            Não
                        </option>
                    </select>
                    <label for="sentenciado" class="mt-1">{{$idA}} - Sentenciado: {{$dadosA->sentenciado}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="sentenciadoB">{{$idB}} - Sentenciado</label>
                    <input type="text" name="sentenciadoB" disabled class="form-control" id="sentenciadoB" value="{{$dadosB->sentenciado}}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- regime --}}
        <div id="regimeDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="regime"><strong>Regime</strong></label>
                    <select id="regime" name="regime" class="form-control">
                        <option value="" @if( $dadosA->regime == "") selected @endif>
                            Selecione
                        </option>
                        <option value="ABERTO" @if( $dadosA->regime == "ABERTO") selected @endif>
                            ABERTO
                        </option>
                        <option value="SEMIABERTO" @if( $dadosA->regime == "SEMIABERTO") selected @endif>
                            SEMIABERTO
                        </option>
                        <option value="FECHADO" @if( $dadosA->regime == "FECHADO") selected @endif>
                            FECHADO
                        </option>
                    </select>
                    <label for="regime" class="mt-1">{{$idA}} - Regime: {{$dadosA->regime}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="regimeB">{{$idB}} - Regime</label>
                    <input type="text" name="regimeB" disabled class="form-control" id="regimeB" value="{{$dadosB->regime}}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- condicao_interno --}}
        <div id="condicao_internoDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="condicao_interno"><strong>Condição do Interno</strong></label>
                    <select id="condicao_interno" name="condicao_interno" class="form-control">
                        <option value="" @if( $dadosA->condicao_interno == "" ) selected @endif>
                            Selecione
                        </option>
                        @foreach($condicoes as $cond)
                            <option value="{{ $cond->id }}" @if( $dadosA->condicao_interno == $cond->id ) selected @endif>
                                {{ $cond->nome }}
                            </option>
                        @endforeach
                    </select>
                    <label for="condicao_interno" class="mt-1">{{$idA}} - Condição do Interno: {{\App\Http\Controllers\Interno\InternoController::getNomeCondicao($dadosA->condicao_interno)}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="condicao_internoB">{{$idB}} - Condição do Interno</label>
                    <input type="text" name="condicao_internoB" disabled class="form-control" id="condicao_internoB" value="{{\App\Http\Controllers\Interno\InternoController::getNomeCondicao($dadosB->condicao_interno)}}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- Medida de Segurança --}}
        <div id="medidaSeguranca" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="medida_seguranca"><strong>Medida de Segurança</strong></label>
                    <select class="form-control" id="medida_seguranca">
                        <option value="S" @if( $dadosA->medida_seguranca == "S" ) selected @endif>Sim</option>
                        <option value="N" @if( $dadosA->medida_seguranca == "N" ) selected @endif>Não</option>
                        <option value="" @if( $dadosA->medida_seguranca == "" ) selected @endif>Não Informado</option>
                    </select>
                    <label for="medida_seguranca" class="mt-1">{{$idA}} - Medida de Segurança: {{$dadosA->medida_seguranca}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="medidaSegurancaB">{{$idB}} - Medida de Segurança</label>
                    <input type="text" name="medidaSegurancaB" disabled class="form-control" id="medidaSegurancaB" value="{{$dadosB->medida_seguranca}}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- saidasexternas --}}
        <div id="saidasexternasDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="saidasexternas"><strong>Autorização para Saídas Externas</strong></label>
                    <select id="saidasexternas" name="saidasexternas" class="form-control">
                        <option value="" @if( $dadosA->saidasexternas == "") selected @endif>
                            Selecione
                        </option>
                        <option value="Sim" @if( $dadosA->saidasexternas == "Sim") selected @endif>
                            Sim
                        </option>
                        <option value="Não" @if( $dadosA->saidasexternas == "Não") selected @endif>
                            Não
                        </option>
                    </select>
                    <label for="saidasexternas" class="mt-1">{{$idA}} - Autorização para Saídas Externas: {{$dadosA->saidasexternas}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="saidasexternasB">{{$idB}} - Autorização para Saídas Externas</label>
                    <input type="text" name="saidasexternasB" disabled class="form-control" id="saidasexternasB" value="{{$dadosB->saidasexternas}}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- faccao --}}
        <div id="faccaoDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="faccao"><strong>Facção</strong></label>
                    <select id="faccao" name="faccao" class="form-control">
                        <option value="" @if( $dadosA->faccao == "" ) selected @endif>
                            Selecione
                        </option>
                        @foreach($faccoes as $fac)
                            <option value="{{ $fac->id }}" @if( $dadosA->faccao == $fac->id ) selected @endif>
                                {{ $fac->nome }}
                            </option>
                        @endforeach
                    </select>
                    <label for="faccao" class="mt-1">{{$idA}} - Facção: {{\App\Http\Controllers\Interno\InternoController::getNomeFaccao($dadosA->faccao)}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="faccaoB">{{$idB}} - Facção</label>
                    <input type="text" name="faccaoB" disabled class="form-control" id="faccaoB" value="{{\App\Http\Controllers\Interno\InternoController::getNomeFaccao($dadosB->faccao)}}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- faccao_posto --}}
        <div id="faccao_postoDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="faccao_posto"><strong>Posto na Facção</strong></label>
                    <input type="text" name="faccao_posto" class="form-control" id="faccao_posto" value="{{$dadosA->faccao_posto}}" placeholder="Posto na Facção">
                    <label for="faccao_posto" id="faccao_postoUsarA" class="mt-1">{{$idA}} - Posto na Facção: {{$dadosA->faccao_posto}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="faccao_postoB">{{$idB}} - Posto na Facção</label>
                    <input type="text" name="faccao_postoB" disabled class="form-control" id="faccao_postoB" value="{{$dadosB->faccao_posto}}">
                </div>
            </div>
            <button type="button" id="buttonfaccao_postoUsarA" class="btn btn-facebook mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>
            <button type="button" id="buttonfaccao_postoUsarB" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>
        </div>

        {{-- faccao_padrinho --}}
        <div id="faccao_padrinhoDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="faccao_padrinho"><strong>Padrinho na Facção</strong></label>
                    <input type="text" name="faccao_padrinho" class="form-control" id="faccao_padrinho" value="{{$dadosA->faccao_padrinho}}" placeholder="Padrinho na Facção">
                    <label for="faccao_padrinho" id="faccao_padrinhoUsarA" class="mt-1">{{$idA}} - Padrinho na Facção: {{$dadosA->faccao_padrinho}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="faccao_padrinhoB">{{$idB}} - Padrinho na Facção</label>
                    <input type="text" name="faccao_padrinhoB" disabled class="form-control" id="faccao_padrinhoB" value="{{$dadosB->faccao_padrinho}}">
                </div>
            </div>
            <button type="button" id="buttonfaccao_padrinhoUsarA" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>
            <button type="button" id="buttonfaccao_padrinhoUsarB" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>
        </div>

        {{-- faccao_quebrada --}}
        <div id="faccao_quebradaDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="faccao_quebrada"><strong>Quebrada na Facção</strong></label>
                    <input type="text" name="faccao_quebrada" class="form-control" id="faccao_quebrada" value="{{$dadosA->faccao_quebrada}}" placeholder="Quebrada (Facção)">
                    <label for="faccao_quebrada" id="faccao_quebradaUsarA" class="mt-1">{{$idA}} - Quebrada na Facção: {{$dadosA->faccao_quebrada}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="faccao_quebradaB">{{$idB}} - Quebrada na Facção</label>
                    <input type="text" name="faccao_quebradaB" disabled class="form-control" id="faccao_quebradaB" value="{{$dadosB->faccao_quebrada}}">
                </div>
            </div>
            <button type="button" id="buttonfaccao_quebradaUsarA" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>
            <button type="button" id="buttonfaccao_quebradaUsarB" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>
        </div>

        {{-- lideranca --}}
        <div id="liderancaDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="lideranca"><strong>Liderança</strong></label>
                    <select id="lideranca" name="lideranca" class="form-control">
                        <option value="" @if( $dadosA->lideranca == "") selected @endif>
                            Selecione
                        </option>
                        <option value="S" @if( $dadosA->lideranca == "S") selected @endif>
                            Sim
                        </option>
                        <option value="N" @if( $dadosA->lideranca == "N") selected @endif>
                            Não
                        </option>
                    </select>
                    <label for="lideranca" class="mt-1">{{$idA}} - Liderança: {{$dadosA->lideranca}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="liderancaB">{{$idB}} - Liderança</label>
                    <input type="text" name="liderancaB" disabled class="form-control" id="liderancaB" value="{{$dadosB->lideranca}}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- naturalidade_pais --}}
        <div id="naturalidade_paisDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="naturalidade_pais"><strong>País de Naturalidade</strong></label>
                    <select id="naturalidade_pais" name="naturalidade_pais" class="form-control">
                        <option value="" @if( $dadosA->naturalidade_pais == "" ) selected @endif>Selecione</option>
                        <option value="África do Sul" @if( $dadosA->naturalidade_pais == "África do Sul" ) selected @endif>África do Sul</option>
                        <option value="Albânia" @if( $dadosA->naturalidade_pais == "Albânia" ) selected @endif>Albânia</option>
                        <option value="Alemanha" @if( $dadosA->naturalidade_pais == "Alemanha" ) selected @endif>Alemanha</option>
                        <option value="Andorra" @if( $dadosA->naturalidade_pais == "Andorra" ) selected @endif>Andorra</option>
                        <option value="Angola" @if( $dadosA->naturalidade_pais == "Angola" ) selected @endif>Angola</option>
                        <option value="Anguilla" @if( $dadosA->naturalidade_pais == "Anguilla" ) selected @endif>Anguilla</option>
                        <option value="Antigua" @if( $dadosA->naturalidade_pais == "Antigua" ) selected @endif>Antigua</option>
                        <option value="Arábia Saudita" @if( $dadosA->naturalidade_pais == "Arábia Saudita" ) selected @endif>Arábia Saudita</option>
                        <option value="Argentina" @if( $dadosA->naturalidade_pais == "Argentina" ) selected @endif>Argentina</option>
                        <option value="Armênia" @if( $dadosA->naturalidade_pais == "Armênia" ) selected @endif>Armênia</option>
                        <option value="Aruba" @if( $dadosA->naturalidade_pais == "Aruba" ) selected @endif>Aruba</option>
                        <option value="Austrália" @if( $dadosA->naturalidade_pais == "Austrália" ) selected @endif>Austrália</option>
                        <option value="Áustria" @if( $dadosA->naturalidade_pais == "Áustria" ) selected @endif>Áustria</option>
                        <option value="Azerbaijão" @if( $dadosA->naturalidade_pais == "Azerbaijão" ) selected @endif>Azerbaijão</option>
                        <option value="Bahamas" @if( $dadosA->naturalidade_pais == "Bahamas" ) selected @endif>Bahamas</option>
                        <option value="Bahrein" @if( $dadosA->naturalidade_pais == "Bahrein" ) selected @endif>Bahrein</option>
                        <option value="Bangladesh" @if( $dadosA->naturalidade_pais == "Bangladesh" ) selected @endif>Bangladesh</option>
                        <option value="Barbados" @if( $dadosA->naturalidade_pais == "Barbados" ) selected @endif>Barbados</option>
                        <option value="Bélgica" @if( $dadosA->naturalidade_pais == "Bélgica" ) selected @endif>Bélgica</option>
                        <option value="Benin" @if( $dadosA->naturalidade_pais == "Benin" ) selected @endif>Benin</option>
                        <option value="Bermudas" @if( $dadosA->naturalidade_pais == "Bermudas" ) selected @endif>Bermudas</option>
                        <option value="Botsuana" @if( $dadosA->naturalidade_pais == "Botsuana" ) selected @endif>Botsuana</option>
                        <option value="Brasil" @if( $dadosA->naturalidade_pais == "Brasil" ) selected @endif>Brasil</option>
                        <option value="Brunei" @if( $dadosA->naturalidade_pais == "Brunei" ) selected @endif>Brunei</option>
                        <option value="Bulgária" @if( $dadosA->naturalidade_pais == "Bulgária" ) selected @endif>Bulgária</option>
                        <option value="Burkina Fasso" @if( $dadosA->naturalidade_pais == "Burkina Fasso" ) selected @endif>Burkina Fasso</option>
                        <option value="botão" @if( $dadosA->naturalidade_pais == "botão" ) selected @endif>botão</option>
                        <option value="Cabo Verde" @if( $dadosA->naturalidade_pais == "Cabo Verde" ) selected @endif>Cabo Verde</option>
                        <option value="Camarões" @if( $dadosA->naturalidade_pais == "Camarões" ) selected @endif>Camarões</option>
                        <option value="Camboja" @if( $dadosA->naturalidade_pais == "Camboja" ) selected @endif>Camboja</option>
                        <option value="Canadá" @if( $dadosA->naturalidade_pais == "Canadá" ) selected @endif>Canadá</option>
                        <option value="Cazaquistão" @if( $dadosA->naturalidade_pais == "Cazaquistão" ) selected @endif>Cazaquistão</option>
                        <option value="Chade" @if( $dadosA->naturalidade_pais == "Chade" ) selected @endif>Chade</option>
                        <option value="Chile" @if( $dadosA->naturalidade_pais == "Chile" ) selected @endif>Chile</option>
                        <option value="China" @if( $dadosA->naturalidade_pais == "China" ) selected @endif>China</option>
                        <option value="Cidade do Vaticano" @if( $dadosA->naturalidade_pais == "Cidade do Vaticano" ) selected @endif>Cidade do Vaticano</option>
                        <option value="Colômbia" @if( $dadosA->naturalidade_pais == "Colômbia" ) selected @endif>Colômbia</option>
                        <option value="Congo" @if( $dadosA->naturalidade_pais == "Congo" ) selected @endif>Congo</option>
                        <option value="Coréia do Sul" @if( $dadosA->naturalidade_pais == "Coréia do Sul" ) selected @endif>Coréia do Sul</option>
                        <option value="Costa do Marfim" @if( $dadosA->naturalidade_pais == "Costa do Marfim" ) selected @endif>Costa do Marfim</option>
                        <option value="Costa Rica" @if( $dadosA->naturalidade_pais == "Costa Rica" ) selected @endif>Costa Rica</option>
                        <option value="Croácia" @if( $dadosA->naturalidade_pais == "Croácia" ) selected @endif>Croácia</option>
                        <option value="Dinamarca" @if( $dadosA->naturalidade_pais == "Dinamarca" ) selected @endif>Dinamarca</option>
                        <option value="Djibuti" @if( $dadosA->naturalidade_pais == "Djibuti" ) selected @endif>Djibuti</option>
                        <option value="Dominica" @if( $dadosA->naturalidade_pais == "Dominica" ) selected @endif>Dominica</option>
                        <option value="EUA" @if( $dadosA->naturalidade_pais == "EUA" ) selected @endif>EUA</option>
                        <option value="Egito" @if( $dadosA->naturalidade_pais == "Egito" ) selected @endif>Egito</option>
                        <option value="El Salvador" @if( $dadosA->naturalidade_pais == "El Salvador" ) selected @endif>El Salvador</option>
                        <option value="Emirados Árabes" @if( $dadosA->naturalidade_pais == "Emirados Árabes" ) selected @endif>Emirados Árabes</option>
                        <option value="Equador" @if( $dadosA->naturalidade_pais == "Equador" ) selected @endif>Equador</option>
                        <option value="Eritréia" @if( $dadosA->naturalidade_pais == "Eritréia" ) selected @endif>Eritréia</option>
                        <option value="Escócia" @if( $dadosA->naturalidade_pais == "Escócia" ) selected @endif>Escócia</option>
                        <option value="Eslováquia" @if( $dadosA->naturalidade_pais == "Eslováquia" ) selected @endif>Eslováquia</option>
                        <option value="Eslovênia" @if( $dadosA->naturalidade_pais == "Eslovênia" ) selected @endif>Eslovênia</option>
                        <option value="Espanha" @if( $dadosA->naturalidade_pais == "Espanha" ) selected @endif>Espanha</option>
                        <option value="Estônia" @if( $dadosA->naturalidade_pais == "Estônia" ) selected @endif>Estônia</option>
                        <option value="Etiópia" @if( $dadosA->naturalidade_pais == "Etiópia" ) selected @endif>Etiópia</option>
                        <option value="Fiji" @if( $dadosA->naturalidade_pais == "Fiji" ) selected @endif>Fiji</option>
                        <option value="Filipinas" @if( $dadosA->naturalidade_pais == "Filipinas" ) selected @endif>Filipinas</option>
                        <option value="Finlândia" @if( $dadosA->naturalidade_pais == "Finlândia" ) selected @endif>Finlândia</option>
                        <option value="França" @if( $dadosA->naturalidade_pais == "França" ) selected @endif>França</option>
                        <option value="Gabão" @if( $dadosA->naturalidade_pais == "Gabão" ) selected @endif>Gabão</option>
                        <option value="Gâmbia" @if( $dadosA->naturalidade_pais == "Gâmbia" ) selected @endif>Gâmbia</option>
                        <option value="Gana" @if( $dadosA->naturalidade_pais == "Gana" ) selected @endif>Gana</option>
                        <option value="Geórgia" @if( $dadosA->naturalidade_pais == "Geórgia" ) selected @endif>Geórgia</option>
                        <option value="Gibraltar" @if( $dadosA->naturalidade_pais == "Gibraltar" ) selected @endif>Gibraltar</option>
                        <option value="Granada" @if( $dadosA->naturalidade_pais == "Granada" ) selected @endif>Granada</option>
                        <option value="Grécia" @if( $dadosA->naturalidade_pais == "Grécia" ) selected @endif>Grécia</option>
                        <option value="Guadalupe" @if( $dadosA->naturalidade_pais == "Guadalupe" ) selected @endif>Guadalupe</option>
                        <option value="Guam" @if( $dadosA->naturalidade_pais == "Guam" ) selected @endif>Guam</option>
                        <option value="Guatemala" @if( $dadosA->naturalidade_pais == "Guatemala" ) selected @endif>Guatemala</option>
                        <option value="Guiana" @if( $dadosA->naturalidade_pais == "Guiana" ) selected @endif>Guiana</option>
                        <option value="Guiana Francesa" @if( $dadosA->naturalidade_pais == "Guiana Francesa" ) selected @endif>Guiana Francesa</option>
                        <option value="Guiné-bissau" @if( $dadosA->naturalidade_pais == "Guiné-bissau" ) selected @endif>Guiné-bissau</option>
                        <option value="Haiti" @if( $dadosA->naturalidade_pais == "Haiti" ) selected @endif>Haiti</option>
                        <option value="Holanda" @if( $dadosA->naturalidade_pais == "Holanda" ) selected @endif>Holanda</option>
                        <option value="Honduras" @if( $dadosA->naturalidade_pais == "Honduras" ) selected @endif>Honduras</option>
                        <option value="Hong Kong" @if( $dadosA->naturalidade_pais == "Hong Kong" ) selected @endif>Hong Kong</option>
                        <option value="Hungria" @if( $dadosA->naturalidade_pais == "Hungria" ) selected @endif>Hungria</option>
                        <option value="Iêmen" @if( $dadosA->naturalidade_pais == "Iêmen" ) selected @endif>Iêmen</option>
                        <option value="Ilhas Cayman" @if( $dadosA->naturalidade_pais == "Ilhas Cayman" ) selected @endif>Ilhas Cayman</option>
                        <option value="Ilhas Cook" @if( $dadosA->naturalidade_pais == "Ilhas Cook" ) selected @endif>Ilhas Cook</option>
                        <option value="Ilhas Curaçao" @if( $dadosA->naturalidade_pais == "Ilhas Curaçao" ) selected @endif>Ilhas Curaçao</option>
                        <option value="Ilhas Marshall" @if( $dadosA->naturalidade_pais == "Ilhas Marshall" ) selected @endif>Ilhas Marshall</option>
                        <option value="Ilhas Turks & Caicos" @if( $dadosA->naturalidade_pais == "Ilhas Turks & Caicos" ) selected @endif>Ilhas Turks & Caicos</option>
                        <option value="Ilhas Virgens (brit.)" @if( $dadosA->naturalidade_pais == "Ilhas Virgens (brit.)" ) selected @endif>Ilhas Virgens (brit.)</option>
                        <option value="Ilhas Virgens(amer.)" @if( $dadosA->naturalidade_pais == "Ilhas Virgens(amer.)" ) selected @endif>Ilhas Virgens(amer.)</option>
                        <option value="Ilhas Wallis e Futuna" @if( $dadosA->naturalidade_pais == "Ilhas Wallis e Futuna" ) selected @endif>Ilhas Wallis e Futuna</option>
                        <option value="Ilhas do Pacífico (EUA)" @if( $dadosA->naturalidade_pais == "Ilhas do Pacífico (EUA)" ) selected @endif>Ilhas do Pacífico (EUA)</option>
                        <option value="Índia" @if( $dadosA->naturalidade_pais == "Índia" ) selected @endif>Índia</option>
                        <option value="Indonésia" @if( $dadosA->naturalidade_pais == "Indonésia" ) selected @endif>Indonésia</option>
                        <option value="Inglaterra" @if( $dadosA->naturalidade_pais == "Inglaterra" ) selected @endif>Inglaterra</option>
                        <option value="Irlanda" @if( $dadosA->naturalidade_pais == "Irlanda" ) selected @endif>Irlanda</option>
                        <option value="Islândia" @if( $dadosA->naturalidade_pais == "Islândia" ) selected @endif>Islândia</option>
                        <option value="Israel" @if( $dadosA->naturalidade_pais == "Israel" ) selected @endif>Israel</option>
                        <option value="Itália" @if( $dadosA->naturalidade_pais == "Itália" ) selected @endif>Itália</option>
                        <option value="Jamaica" @if( $dadosA->naturalidade_pais == "Jamaica" ) selected @endif>Jamaica</option>
                        <option value="Japão" @if( $dadosA->naturalidade_pais == "Japão" ) selected @endif>Japão</option>
                        <option value="Jordânia" @if( $dadosA->naturalidade_pais == "Jordânia" ) selected @endif>Jordânia</option>
                        <option value="Kuwait" @if( $dadosA->naturalidade_pais == "Kuwait" ) selected @endif>Kuwait</option>
                        <option value="Latvia" @if( $dadosA->naturalidade_pais == "Latvia" ) selected @endif>Latvia</option>
                        <option value="Líbano" @if( $dadosA->naturalidade_pais == "Líbano" ) selected @endif>Líbano</option>
                        <option value="Liechtenstein" @if( $dadosA->naturalidade_pais == "Liechtenstein" ) selected @endif>Liechtenstein</option>
                        <option value="Lituânia" @if( $dadosA->naturalidade_pais == "Lituânia" ) selected @endif>Lituânia</option>
                        <option value="Luxemburgo" @if( $dadosA->naturalidade_pais == "Luxemburgo" ) selected @endif>Luxemburgo</option>
                        <option value="Macau" @if( $dadosA->naturalidade_pais == "Macau" ) selected @endif>Macau</option>
                        <option value="Macedônia" @if( $dadosA->naturalidade_pais == "Macedônia" ) selected @endif>Macedônia</option>
                        <option value="Madagascar" @if( $dadosA->naturalidade_pais == "Madagascar" ) selected @endif>Madagascar</option>
                        <option value="Malásia" @if( $dadosA->naturalidade_pais == "Malásia" ) selected @endif>Malásia</option>
                        <option value="Malaui" @if( $dadosA->naturalidade_pais == "Malaui" ) selected @endif>Malaui</option>
                        <option value="Mali" @if( $dadosA->naturalidade_pais == "Mali" ) selected @endif>Mali</option>
                        <option value="Malta" @if( $dadosA->naturalidade_pais == "Malta" ) selected @endif>Malta</option>
                        <option value="Marrocos" @if( $dadosA->naturalidade_pais == "Marrocos" ) selected @endif>Marrocos</option>
                        <option value="Martinica" @if( $dadosA->naturalidade_pais == "Martinica" ) selected @endif>Martinica</option>
                        <option value="Mauritânia" @if( $dadosA->naturalidade_pais == "Mauritânia" ) selected @endif>Mauritânia</option>
                        <option value="Mauritius" @if( $dadosA->naturalidade_pais == "Mauritius" ) selected @endif>Mauritius</option>
                        <option value="México" @if( $dadosA->naturalidade_pais == "México" ) selected @endif>México</option>
                        <option value="Moldova" @if( $dadosA->naturalidade_pais == "Moldova" ) selected @endif>Moldova</option>
                        <option value="Mônaco" @if( $dadosA->naturalidade_pais == "Mônaco" ) selected @endif>Mônaco</option>
                        <option value="Montserrat" @if( $dadosA->naturalidade_pais == "Montserrat" ) selected @endif>Montserrat</option>
                        <option value="Nepal" @if( $dadosA->naturalidade_pais == "Nepal" ) selected @endif>Nepal</option>
                        <option value="Nicarágua" @if( $dadosA->naturalidade_pais == "Nicarágua" ) selected @endif>Nicarágua</option>
                        <option value="Niger" @if( $dadosA->naturalidade_pais == "Niger" ) selected @endif>Niger</option>
                        <option value="Nigéria" @if( $dadosA->naturalidade_pais == "Nigéria" ) selected @endif>Nigéria</option>
                        <option value="Noruega" @if( $dadosA->naturalidade_pais == "Noruega" ) selected @endif>Noruega</option>
                        <option value="Nova Caledônia" @if( $dadosA->naturalidade_pais == "Nova Caledônia" ) selected @endif>Nova Caledônia</option>
                        <option value="Nova Zelândia" @if( $dadosA->naturalidade_pais == "Nova Zelândia" ) selected @endif>Nova Zelândia</option>
                        <option value="Omã" @if( $dadosA->naturalidade_pais == "Omã" ) selected @endif>Omã</option>
                        <option value="Palau" @if( $dadosA->naturalidade_pais == "Palau" ) selected @endif>Palau</option>
                        <option value="Panamá" @if( $dadosA->naturalidade_pais == "Panamá" ) selected @endif>Panamá</option>
                        <option value="Papua-nova Guiné" @if( $dadosA->naturalidade_pais == "Papua-nova Guiné" ) selected @endif>Papua-nova Guiné</option>
                        <option value="Paquistão" @if( $dadosA->naturalidade_pais == "Paquistão" ) selected @endif>Paquistão</option>
                        <option value="Peru" @if( $dadosA->naturalidade_pais == "Peru" ) selected @endif>Peru</option>
                        <option value="Polinésia Francesa" @if( $dadosA->naturalidade_pais == "Polinésia Francesa" ) selected @endif>Polinésia Francesa</option>
                        <option value="Polônia" @if( $dadosA->naturalidade_pais == "Polônia" ) selected @endif>Polônia</option>
                        <option value="Porto Rico" @if( $dadosA->naturalidade_pais == "Porto Rico" ) selected @endif>Porto Rico</option>
                        <option value="Portugal" @if( $dadosA->naturalidade_pais == "Portugal" ) selected @endif>Portugal</option>
                        <option value="Qatar" @if( $dadosA->naturalidade_pais == "Qatar" ) selected @endif>Qatar</option>
                        <option value="Quênia" @if( $dadosA->naturalidade_pais == "Quênia" ) selected @endif>Quênia</option>
                        <option value="Rep. Dominicana" @if( $dadosA->naturalidade_pais == "Rep. Dominicana" ) selected @endif>Rep. Dominicana</option>
                        <option value="Rep. Tcheca" @if( $dadosA->naturalidade_pais == "Rep. Tcheca" ) selected @endif>Rep. Tcheca</option>
                        <option value="Reunion" @if( $dadosA->naturalidade_pais == "Reunion" ) selected @endif>Reunion</option>
                        <option value="Romênia" @if( $dadosA->naturalidade_pais == "Romênia" ) selected @endif>Romênia</option>
                        <option value="Ruanda" @if( $dadosA->naturalidade_pais == "Ruanda" ) selected @endif>Ruanda</option>
                        <option value="Rússia" @if( $dadosA->naturalidade_pais == "Rússia" ) selected @endif>Rússia</option>
                        <option value="Saipan" @if( $dadosA->naturalidade_pais == "Saipan" ) selected @endif>Saipan</option>
                        <option value="Samoa Americana" @if( $dadosA->naturalidade_pais == "Samoa Americana" ) selected @endif>Samoa Americana</option>
                        <option value="Senegal" @if( $dadosA->naturalidade_pais == "Senegal" ) selected @endif>Senegal</option>
                        <option value="Serra Leone" @if( $dadosA->naturalidade_pais == "Serra Leone" ) selected @endif>Serra Leone</option>
                        <option value="Seychelles" @if( $dadosA->naturalidade_pais == "Seychelles" ) selected @endif>Seychelles</option>
                        <option value="Singapura" @if( $dadosA->naturalidade_pais == "Singapura" ) selected @endif>Singapura</option>
                        <option value="Síria" @if( $dadosA->naturalidade_pais == "Síria" ) selected @endif>Síria</option>
                        <option value="Sri Lanka" @if( $dadosA->naturalidade_pais == "Sri Lanka" ) selected @endif>Sri Lanka</option>
                        <option value="St. Kitts & Nevis" @if( $dadosA->naturalidade_pais == "St. Kitts & Nevis" ) selected @endif>St. Kitts & Nevis</option>
                        <option value="St. Lúcia" @if( $dadosA->naturalidade_pais == "St. Lúcia" ) selected @endif>St. Lúcia</option>
                        <option value="St. Vincent" @if( $dadosA->naturalidade_pais == "St. Vincent" ) selected @endif>St. Vincent</option>
                        <option value="Sudão" @if( $dadosA->naturalidade_pais == "Sudão" ) selected @endif>Sudão</option>
                        <option value="Suécia" @if( $dadosA->naturalidade_pais == "Suécia" ) selected @endif>Suécia</option>
                        <option value="Suíça" @if( $dadosA->naturalidade_pais == "Suíça" ) selected @endif>Suíça</option>
                        <option value="Suriname" @if( $dadosA->naturalidade_pais == "Suriname" ) selected @endif>Suriname</option>
                        <option value="Tailândia" @if( $dadosA->naturalidade_pais == "Tailândia" ) selected @endif>Tailândia</option>
                        <option value="Taiwan" @if( $dadosA->naturalidade_pais == "Taiwan" ) selected @endif>Taiwan</option>
                        <option value="Tanzânia" @if( $dadosA->naturalidade_pais == "Tanzânia" ) selected @endif>Tanzânia</option>
                        <option value="Togo" @if( $dadosA->naturalidade_pais == "Togo" ) selected @endif>Togo</option>
                        <option value="Trinidad & Tobago" @if( $dadosA->naturalidade_pais == "Trinidad & Tobago" ) selected @endif>Trinidad & Tobago</option>
                        <option value="Tunísia" @if( $dadosA->naturalidade_pais == "Tunísia" ) selected @endif>Tunísia</option>
                        <option value="Turquia" @if( $dadosA->naturalidade_pais == "Turquia" ) selected @endif>Turquia</option>
                        <option value="Ucrânia" @if( $dadosA->naturalidade_pais == "Ucrânia" ) selected @endif>Ucrânia</option>
                        <option value="Uganda" @if( $dadosA->naturalidade_pais == "Uganda" ) selected @endif>Uganda</option>
                        <option value="Uruguai" @if( $dadosA->naturalidade_pais == "Uruguai" ) selected @endif>Uruguai</option>
                        <option value="Venezuela" @if( $dadosA->naturalidade_pais == "Venezuela" ) selected @endif>Venezuela</option>
                        <option value="Vietnã" @if( $dadosA->naturalidade_pais == "Vietnã" ) selected @endif>Vietnã</option>
                        <option value="Zaire" @if( $dadosA->naturalidade_pais == "Zaire" ) selected @endif>Zaire</option>
                        <option value="Zâmbia" @if( $dadosA->naturalidade_pais == "Zâmbia" ) selected @endif>Zâmbia</option>
                        <option value="Zimbábue" @if( $dadosA->naturalidade_pais == "Zimbábue" ) selected @endif>Zimbábue</option>
                    </select>
                    <label for="naturalidade_pais" class="mt-1">{{$idA}} - País de Naturalidade: {{$dadosA->naturalidade_pais}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="naturalidade_paisB">{{$idB}} - País de Naturalidade</label>
                    <input type="text" name="naturalidade_paisB" disabled class="form-control" id="naturalidade_paisB" value="{{$dadosB->naturalidade_pais}}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- naturalidade_estado --}}
        <div id="naturalidade_estadoDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="naturalidade_estado"><strong>Estado de Naturalidade</strong></label>
                    <select id="naturalidade_estado" name="naturalidade_estado" class="form-control">
                        <option value="" @if( $dadosA->naturalidade_estado == "" ) selected @endif>
                            Selecione
                        </option>
                        <option value="AC" @if( $dadosA->naturalidade_estado == "AC" ) selected @endif>
                            Acre
                        </option>
                        <option value="AL" @if( $dadosA->naturalidade_estado == "AL" ) selected @endif>
                            Alagoas
                        </option>
                        <option value="AP" @if( $dadosA->naturalidade_estado == "AP" ) selected @endif>
                            Amapá
                        </option>
                        <option value="AM" @if( $dadosA->naturalidade_estado == "AM" ) selected @endif>
                            Amazonas
                        </option>
                        <option value="BA" @if( $dadosA->naturalidade_estado == "BA" ) selected @endif>
                            Bahia
                        </option>
                        <option value="CE" @if( $dadosA->naturalidade_estado == "CE" ) selected @endif>
                            Ceará
                        </option>
                        <option value="DF" @if( $dadosA->naturalidade_estado == "DF" ) selected @endif>
                            Distrito Federal
                        </option>
                        <option value="ES" @if( $dadosA->naturalidade_estado == "ES" ) selected @endif>
                            Espírito Santo
                        </option>
                        <option value="GO" @if( $dadosA->naturalidade_estado == "GO" ) selected @endif>
                            Goiás
                        </option>
                        <option value="MA" @if( $dadosA->naturalidade_estado == "MA" ) selected @endif>
                            Maranhão
                        </option>
                        <option value="MT" @if( $dadosA->naturalidade_estado == "MT" ) selected @endif>
                            Mato Grosso
                        </option>
                        <option value="MS" @if( $dadosA->naturalidade_estado == "MS" ) selected @endif>
                            Mato Grosso do Sul
                        </option>
                        <option value="MG" @if( $dadosA->naturalidade_estado == "MG" ) selected @endif>
                            Minas Gerais
                        </option>
                        <option value="PA" @if( $dadosA->naturalidade_estado == "PA" ) selected @endif>
                            Pará
                        </option>
                        <option value="PB" @if( $dadosA->naturalidade_estado == "PB" ) selected @endif>
                            Paraíba
                        </option>
                        <option value="PR" @if( $dadosA->naturalidade_estado == "PR" ) selected @endif>
                            Paraná
                        </option>
                        <option value="PE" @if( $dadosA->naturalidade_estado == "PE" ) selected @endif>
                            Pernambuco
                        </option>
                        <option value="PI" @if( $dadosA->naturalidade_estado == "PI" ) selected @endif>
                            Piauí
                        </option>
                        <option value="RJ" @if( $dadosA->naturalidade_estado == "RJ" ) selected @endif>
                            Rio de Janeiro
                        </option>
                        <option value="RN" @if( $dadosA->naturalidade_estado == "RN" ) selected @endif>
                            Rio Grande do Norte
                        </option>
                        <option value="RS" @if( $dadosA->naturalidade_estado == "RS" ) selected @endif>
                            Rio Grande do Sul
                        </option>
                        <option value="RO" @if( $dadosA->naturalidade_estado == "RO" ) selected @endif>
                            Rondônia
                        </option>
                        <option value="RR" @if( $dadosA->naturalidade_estado == "RR" ) selected @endif>
                            Roraima
                        </option>
                        <option value="SC" @if( $dadosA->naturalidade_estado == "SC" ) selected @endif>
                            Santa Catarina
                        </option>
                        <option value="SP" @if( $dadosA->naturalidade_estado == "SP" ) selected @endif>
                            São Paulo
                        </option>
                        <option value="SE" @if( $dadosA->naturalidade_estado == "SE" ) selected @endif>
                            Sergipe
                        </option>
                        <option value="TO" @if( $dadosA->naturalidade_estado == "TO" ) selected @endif>
                            Tocantins
                        </option>
                    </select>
                    <label for="naturalidade_estado" class="mt-1">{{$idA}} - Estado de Naturalidade: {{$dadosA->naturalidade_estado}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="naturalidade_estadoB">{{$idB}} - Estado de Naturalidade</label>
                    <input type="text" name="naturalidade_estadoB" disabled class="form-control" id="naturalidade_estadoB" value="{{$dadosB->naturalidade_estado}}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- naturalidade_cidade --}}
        <div id="naturalidade_cidadeDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="naturalidade_cidade"><strong>Cidade de Naturalidade</strong></label>
                    <select id="naturalidade_cidade" name="naturalidade_cidade" class="form-control">
                        <option value="" @if( $dadosA->naturalidade_cidade == "" ) selected @endif>
                            Selecione
                        </option>
                        @foreach($naturalidadeMunicipios as $natMun)
                            <option value="{{ $natMun->Nome }}" @if( $dadosA->naturalidade_cidade == $natMun->Nome ) selected @endif>
                                {{ $natMun->Nome }}
                            </option>
                        @endforeach
                    </select>
                    <label for="naturalidade_cidade" class="mt-1">{{$idA}} - Cidade de Naturalidade: {{$dadosA->naturalidade_cidade}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="naturalidade_cidadeB">{{$idB}} - Cidade de Naturalidade</label>
                    <input type="text" name="naturalidade_cidadeB" disabled class="form-control" id="naturalidade_cidadeB" value="{{$dadosB->naturalidade_cidade}}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- opcao_sexual --}}
        <div id="opcao_sexualDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="opcao_sexual"><strong>Opção Sexual</strong></label>
                    <select id="opcao_sexual" name="opcao_sexual" class="form-control">
                        <option value="" @if( $dadosA->opcao_sexual == "" ) selected @endif>
                            Selecione
                        </option>
                        <option value="heterossexual" @if( $dadosA->opcao_sexual == "heterossexual" ) selected @endif>
                            Heterossexual
                        </option>
                        <option value="homossexual" @if( $dadosA->opcao_sexual == "homossexual" ) selected @endif>
                            Homossexual
                        </option>
                        <option value="bissexual" @if( $dadosA->opcao_sexual == "bissexual" ) selected @endif>
                            Bissexual
                        </option>
                        <option value="nao declarado" @if( $dadosA->opcao_sexual == "nao declarado" ) selected @endif>
                            Não Declarado
                        </option>
                    </select>
                    <label for="opcao_sexual" class="mt-1">{{$idA}} - Opção Sexual: {{$dadosA->opcao_sexual}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="opcao_sexualB">{{$idB}} - Opção Sexual</label>
                    <input type="text" name="opcao_sexualB" disabled class="form-control" id="opcao_sexualB" value="{{$dadosB->opcao_sexual}}">
                </div>

        {{-- Identidade de Genero --}}
        <div id="opcao_sexualDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="identidade_genero"><strong>Identidade de Gênero</strong></label>
                    <select id="identidade_genero" name="identidade_genero" class="form-control">
                        <option value="" @if( $dadosA->identidade_genero == "" ) selected @endif>
                            Selecione
                        </option>
                        <option value="Travesti" @if( $dadosA->identidade_genero == "Travesti" ) selected @endif>
                            Travesti
                        </option>
                        <option value="Transexual" @if( $dadosA->identidade_genero == "Transexual" ) selected @endif>
                            Transexual
                        </option>
                        <option value="Intersexual" @if( $dadosA->identidade_genero == "Intersexual" ) selected @endif>
                            Intersexual
                        </option>
                        <option value="Cisgênero" @if( $dadosA->identidade_genero == "Cisgênero" ) selected @endif>
                            Cisgênero
                        </option>
                    </select>
                    <label for="identidade_genero" class="mt-1">{{$idA}} - Opção Sexual: {{$dadosA->identidade_genero}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="identidade_generoB">{{$idB}} - Opção Sexual</label>
                    <input type="text" name="identidade_generoB" disabled class="form-control" id="identidade_generoB" value="{{$dadosB->identidade_genero}}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- periculosidade --}}
        <div id="periculosidadeDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="periculosidade"><strong>Periculosidade</strong></label>
                    <select id="periculosidade" name="periculosidade" class="form-control">
                        <option value="" @if( $dadosA->periculosidade == "") selected @endif>
                            Selecione
                        </option>
                        <option value="Baixa" @if( $dadosA->periculosidade == "Baixa") selected @endif>Baixa</option>
                        <option value="Média" @if( $dadosA->periculosidade == "Média") selected @endif>Média</option>
                        <option value="Alta" @if( $dadosA->periculosidade == "Alta") selected @endif>Alta</option>
                    </select>
                    <label for="periculosidade" class="mt-1">{{$idA}} - Periculosidade: {{$dadosA->periculosidade}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="periculosidadeB">{{$idB}} - Periculosidade</label>
                    <input type="text" name="periculosidadeB" disabled class="form-control" id="periculosidadeB" value="{{$dadosB->periculosidade}}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- documento_entrada_numero --}}
        <div id="documento_entrada_numeroDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="documento_entrada_numero"><strong>Número do Documento de Entrada</strong></label>
                    <input type="text" name="documento_entrada_numero" class="form-control" id="documento_entrada_numero" value="{{$dadosA->documento_entrada_numero}}" placeholder="Número do Documento de Entrada">
                    <label for="documento_entrada_numero" id="documento_entrada_numeroUsarA" class="mt-1">{{$idA}} - Número do Documento de Entrada: {{$dadosA->documento_entrada_numero}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="documento_entrada_numeroB">{{$idB}} - Número do Documento de Entrada</label>
                    <input type="text" name="documento_entrada_numeroB" disabled class="form-control" id="documento_entrada_numeroB" value="{{$dadosB->documento_entrada_numero}}">
                </div>
            </div>
            <button type="button" id="buttondocumento_entrada_numeroUsarA" class="btn btn-facebook mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>
            <button type="button" id="buttondocumento_entrada_numeroUsarB" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>
        </div>

        {{-- religiao --}}
        <div id="religiaoDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="religiao"><strong>Religião</strong></label>
                    <select id="religiao" name="religiao" class="form-control">
                        <option value="" @if( $dadosA->religiao == "" ) selected @endif>
                            Selecione
                        </option>
                        <option value="Adventista" @if( $dadosA->religiao == "Adventista" ) selected @endif>
                            Adventista
                        </option>
                        <option value="Agnóstico" @if( $dadosA->religiao == "Agnóstico" ) selected @endif>
                            Agnóstico
                        </option>
                        <option value="Ateu" @if( $dadosA->religiao == "Ateu" ) selected @endif>
                            Ateu
                        </option>
                        <option value="Budista" @if( $dadosA->religiao == "Budista" ) selected @endif>
                            Budista
                        </option>
                        <option value="Católica" @if( $dadosA->religiao == "Católica" ) selected @endif>
                            Católica
                        </option>
                        <option value="Espírita" @if( $dadosA->religiao == "Espírita" ) selected @endif>
                            Espírita
                        </option>
                        <option value="Evangélica" @if( $dadosA->religiao == "Evangélica" ) selected @endif>
                            Evangélica
                        </option>
                        <option value="Judeu" @if( $dadosA->religiao == "Judeu" ) selected @endif>
                            Judeu
                        </option>
                        <option value="Mórmon" @if( $dadosA->religiao == "Mórmon" ) selected @endif>
                            Mórmon
                        </option>
                        <option value="Muçulmana" @if( $dadosA->religiao == "Muçulmana" ) selected @endif>
                            Muçulmana
                        </option>
                        <option value="Protestante" @if( $dadosA->religiao == "Protestante" ) selected @endif>
                            Protestante
                        </option>
                        <option value="Sem Religião" @if( $dadosA->religiao == "Sem Religião" ) selected @endif>
                            Sem Religião
                        </option>
                        <option value="Outras" @if( $dadosA->religiao == "Outras" ) selected @endif>
                            Outras
                        </option>
                    </select>
                    <label for="religiao" class="mt-1">{{$idA}} - Religião: {{$dadosA->religiao}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="religiaoB">{{$idB}} - Religião</label>
                    <input type="text" name="religiaoB" disabled class="form-control" id="religiaoB" value="{{$dadosB->religiao}}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- status --}}
        <div id="statusDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="status"><strong>Status</strong></label>
                    <select id="status" name="status" class="form-control">
                        <option value="" @if( $dadosA->status == "") selected @endif>
                            Selecione
                        </option>
                        <option value="A" @if( $dadosA->status == "A") selected @endif>
                            Ativo
                        </option>
                        <option value="I" @if( $dadosA->status == "I") selected @endif>
                            Inativo
                        </option>
                    </select>
                    <label for="status" class="mt-1">{{$idA}} - Status: {{$dadosA->status}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="statusB">{{$idB}} - Status</label>
                    <input type="text" name="statusB" disabled class="form-control" id="statusB" value="{{$dadosB->status}}">
                </div>
            </div>
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        </div>

        {{-- historico --}}
        {{--@if($historico != collect([]))--}}
            {{--<div id="historico" class="form-group mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">--}}
                {{--<label for="historico"><strong>Histórico ({{ config('app.name') }} Antigo)</strong></label>--}}
                {{--<div class="col pl-5 pr-5">--}}
                    {{--@foreach($historico as $key=>$hist)--}}
                        {{--<div class="form-row">--}}
                            {{--<label class="mt-1">--}}
                                {{--<input type="checkbox" checked name="historico[]" value="{{$historico[$key]->id}}" class="form-check-input">--}}
                                {{--Prontuário {{  $historico[$key]->idinterno }} --- Data do Fato: {{ \App\Classes\Tdata::TdataBR($historico[$key]->data_hora) }} --- Descrição: {{ $historico[$key]->descricao }} --- Unidade Prisional de Registro: {{ \App\Http\Controllers\Unidades\UnidadesController::getSiglaUp($historico[$key]->idup) }}.--}}
                            {{--</label>--}}
                        {{--</div>--}}
                    {{--@endforeach--}}
                {{--</div>--}}
                {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Selecionar todos</button>--}}
                {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-square"></i> Desselecionar todos</button>--}}
            {{--</div>--}}
        {{--@endif--}}

        {{-- movimentacao --}}
        {{--@if($movimentacao != collect([]))--}}
            {{--<div id="movimentacao" class="form-group mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">--}}
                {{--<label for="movimentacao"><strong>Movimentações</strong></label>--}}
                {{--<div class="col pl-5 pr-5">--}}
                    {{--@foreach($movimentacao as $key=>$mov)--}}
                        {{--<div class="form-row">--}}
                            {{--<label class="mt-1">--}}
                                {{--<input type="checkbox" checked name="movimentacao[]" value="{{$movimentacao[$key]->movintid}}" class="form-check-input">--}}
                                {{--Prontuário {{  $movimentacao[$key]->fk_interno }} --- Data do Fato: {{ \App\Classes\Tdata::TtimestampBR($movimentacao[$key]->data) }} --- Tipo: {{ $movimentacao[$key]->tipo }} --- Motivo: {{ $movimentacao[$key]->motivo }}@if(!is_null($movimentacao[$key]->up_anterior)) --- Origem: {{ \App\Http\Controllers\Unidades\UnidadesController::getSiglaUp($movimentacao[$key]->up_anterior), \App\Http\Controllers\Unidades\UnidadesController::getNomePav($movimentacao[$key]->pv_anterior), \App\Http\Controllers\Unidades\UnidadesController::getNomeAla($movimentacao[$key]->ala_anterior), \App\Http\Controllers\Unidades\UnidadesController::getNomeCela($movimentacao[$key]->cela_anterior) }}@endif @if(!is_null($movimentacao[$key]->unidade_destino))--- Destino: {{ \App\Http\Controllers\Unidades\UnidadesController::getSiglaUp($movimentacao[$key]->unidade_destino), \App\Http\Controllers\Unidades\UnidadesController::getNomePav($movimentacao[$key]->pavilhao_destino), \App\Http\Controllers\Unidades\UnidadesController::getNomeAla($movimentacao[$key]->ala_destino), \App\Http\Controllers\Unidades\UnidadesController::getNomeCela($movimentacao[$key]->cela_destino) }}@endif.--}}
                            {{--</label>--}}
                        {{--</div>--}}
                    {{--@endforeach--}}
                {{--</div>--}}
                {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Selecionar todos</button>--}}
                {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-square"></i> Desselecionar todos</button>--}}
            {{--</div>--}}
        {{--@endif--}}

        {{-- sindicancia --}}
        {{--@if($sindicancia != collect([]))--}}
            {{--<div id="sindicancia" class="form-group mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">--}}
                {{--<label for="sindicancia"><strong>Sindicâncias</strong></label>--}}
                {{--<div class="col pl-5 pr-5">--}}
                    {{--@foreach($sindicancia as $key=>$mov)--}}
                        {{--<div class="form-row">--}}
                            {{--<label class="mt-1">--}}
                                {{--<input type="checkbox" checked name="sindicancia[]" value="{{$sindicancia[$key]->sinintid}}" class="form-check-input">--}}
                                {{--Prontuário {{  $sindicancia[$key]->fk_interno }} --- Data do Fato: {{ \App\Classes\Tdata::TdataBR($sindicancia[$key]->data_incidente) }} --- Status: {{ $sindicancia[$key]->status }} --- Gravidade: {{ $sindicancia[$key]->gravidade_falta }} --- Título: {{ $sindicancia[$key]->titulo }} --- Descrição: {{ $sindicancia[$key]->descricao }} --- Unidade: {{ \App\Http\Controllers\Unidades\UnidadesController::getSiglaUp($sindicancia[$key]->unidade) }}.--}}
                            {{--</label>--}}
                        {{--</div>--}}
                    {{--@endforeach--}}
                {{--</div>--}}
                {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Selecionar todos</button>--}}
                {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-square"></i> Desselecionar todos</button>--}}
            {{--</div>--}}
        {{--@endif--}}

        {{-- ocorrência --}}
        {{--@if($ocorrencia != collect([]))--}}
            {{--<div id="ocorrencia" class="form-group mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">--}}
                {{--<label for="ocorrencia"><strong>Ocorrências</strong></label>--}}
                {{--<div class="col pl-5 pr-5">--}}
                    {{--@foreach($ocorrencia as $key=>$mov)--}}
                        {{--<div class="form-row">--}}
                            {{--<label class="mt-1">--}}
                                {{--<input type="checkbox" checked name="ocorrencia[]" value="{{$ocorrencia[$key]->ocointid}}" class="form-check-input">--}}
                                {{--Prontuário {{  $ocorrencia[$key]->fk_id_envolvido }} --- Data do Fato: {{ \App\Classes\Tdata::TdataBR($ocorrencia[$key]->data) }} {{ $ocorrencia[$key]->horario }} --- Natureza: {{ $ocorrencia[$key]->natureza }} --- Status: {{ $ocorrencia[$key]->status }} --- Descrição: {{ $ocorrencia[$key]->descricao }} --- Unidade: {{ \App\Http\Controllers\Unidades\UnidadesController::getSiglaUp($ocorrencia[$key]->unidade) }}.--}}
                            {{--</label>--}}
                        {{--</div>--}}
                    {{--@endforeach--}}
                {{--</div>--}}
                {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Selecionar todos</button>--}}
                {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-square"></i> Desselecionar todos</button>--}}
            {{--</div>--}}
        {{--@endif--}}

        {{-- escolta --}}
        {{--@if($escolta != collect([]))--}}
            {{--<div id="escolta" class="form-group mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">--}}
                {{--<label for="escolta"><strong>Tabela Escolta</strong></label>--}}
                {{--<div class="col pl-5 pr-5">--}}
                    {{--@foreach($escolta as $key=>$mov)--}}
                        {{--<div class="form-row">--}}
                            {{--<label class="mt-1">--}}
                                {{--<input type="checkbox" checked name="escolta[]" value="{{$escolta[$key]->escintid}}" class="form-check-input">--}}
                                {{--Prontuário {{  $escolta[$key]->fk_interno }} --- Data Saída: {{ \App\Classes\Tdata::TdataBR($escolta[$key]->data_saida) }} {{ $escolta[$key]->hora_saida }} --- Data Retorno: {{ \App\Classes\Tdata::TdataBR($escolta[$key]->data_retorno) }} {{ $escolta[$key]->hora_retorno }} --- Tipo: {{ $escolta[$key]->tipo }} --- Status: {{ $escolta[$key]->status }} --- Documento: {{ $escolta[$key]->documento }} --- Motivo: {{ $escolta[$key]->motivo }} --- Destino: {{ $escolta[$key]->destino }} --- FTPEN Responsável: {{ \App\Http\Controllers\Unidades\UnidadesController::getSiglaUp($escolta[$key]->responsavel) }} --- Utilizou VTR: {{ $escolta[$key]->vtr_utilizacao }} --- Placa: {{ $escolta[$key]->vtr_placa }} --- VTR Comandante: {{ $escolta[$key]->vtr_comandante }} --- VTR Motorista: {{ $escolta[$key]->vtr_motorista }} --- Observação: {{ $escolta[$key]->obs }} --- Unidade Solicitante: {{ \App\Http\Controllers\Unidades\UnidadesController::getSiglaUp($escolta[$key]->unidade) }}  --- Solicitante do judiciário: {{ $escolta[$key]->solicitante }}.--}}
                            {{--</label>--}}
                        {{--</div>--}}
                    {{--@endforeach--}}
                {{--</div>--}}
                {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Selecionar todos</button>--}}
                {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-square"></i> Desselecionar todos</button>--}}
            {{--</div>--}}
        {{--@endif--}}

        {{-- gep --}}
        {{--@if($gep != collect([]))--}}
            {{--<div id="gep" class="form-group mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">--}}
                {{--<label for="gep"><strong>Tabela FTPEN</strong></label>--}}
                {{--<div class="col pl-5 pr-5">--}}
                    {{--@foreach($gep as $key=>$mov)--}}
                        {{--<div class="form-row">--}}
                            {{--<label class="mt-1">--}}
                                {{--<input type="checkbox" checked name="gep[]" value="{{$gep[$key]->gepintid}}" class="form-check-input">--}}
                                {{--Prontuário {{  $gep[$key]->fk_interno }} --- Data Saída: {{ \App\Classes\Tdata::TdataBR($gep[$key]->data_saida) }} {{ $gep[$key]->hora_saida }} --- Data Retorno: {{ \App\Classes\Tdata::TdataBR($gep[$key]->data_retorno) }} {{ $gep[$key]->hora_retorno }} --- Status: {{ $gep[$key]->status }} --- Placa: {{ $gep[$key]->vtr_placa }} --- VTR Comandante: {{ $gep[$key]->vtr_comandante }} --- VTR Motorista: {{ $gep[$key]->vtr_motorista }}.--}}
                            {{--</label>--}}
                        {{--</div>--}}
                    {{--@endforeach--}}
                {{--</div>--}}
                {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Selecionar todos</button>--}}
                {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-square"></i> Desselecionar todos</button>--}}
            {{--</div>--}}
        {{--@endif--}}

        {{-- observacao --}}
        <div id="observacaoDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="observacao"><strong>Observação</strong></label>
                    <input type="text" name="observacao" class="form-control" id="observacao" value="{{$dadosA->observacao}}" placeholder="Observação">
                    <label for="observacao" id="observacaoUsarA" class="mt-1">{{$idA}} - Observação: {{$dadosA->observacao}}</label>
                </div>
                <div class="form-group col-6">
                    <label for="observacaoB">{{$idB}} - Observação</label>
                    <input type="text" name="observacaoB" disabled class="form-control" id="observacaoB" value="{{$dadosB->observacao}}">
                </div>
            </div>
            <button type="button" id="buttonobservacaoUsarA" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>
            <button type="button" id="buttonobservacaoUsarB" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>
        </div>

         {{--created_at --}}
        {{--<div id="created_atDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">--}}
            {{--<div class="form-row">--}}
                {{--<div class="form-group col-6">--}}
                    {{--<label for="created_at"><strong>created_at</strong></label>--}}
                    {{--<input type="text" name="created_at" class="form-control" id="created_at" value="{{$dadosA->created_at}}" placeholder="created_at">--}}
                    {{--<label for="created_at" class="mt-1">{{$idA}} - created_at: {{$dadosA->created_at}}</label>--}}
                {{--</div>--}}
                {{--<div class="form-group col-6">--}}
                    {{--<label for="created_atB">{{$idB}} - created_at</label>--}}
                    {{--<input type="text" name="created_atB" disabled class="form-control" id="created_atB" value="{{$dadosB->created_at}}">--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        {{--</div>--}}

         {{--updated_at --}}
        {{--<div id="updated_atDiv" class="mt-3" style="border: 1px solid; padding: 10px; border-radius: 10px;">--}}
            {{--<div class="form-row">--}}
                {{--<div class="form-group col-6">--}}
                    {{--<label for="updated_at"><strong>updated_at</strong></label>--}}
                    {{--<input type="text" name="updated_at" class="form-control" id="updated_at" value="{{$dadosA->updated_at}}" placeholder="updated_at">--}}
                    {{--<label for="updated_at" class="mt-1">{{$idA}} - updated_at: {{$dadosA->updated_at}}</label>--}}
                {{--</div>--}}
                {{--<div class="form-group col-6">--}}
                    {{--<label for="updated_atB">{{$idB}} - updated_at</label>--}}
                    {{--<input type="text" name="updated_atB" disabled class="form-control" id="updated_atB" value="{{$dadosB->updated_at}}">--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<button type="button" class="btn btn-facebook  mt-1"><i class="fas fa-check-square"></i> Usar {{$idA}}</button>--}}
            {{--<button type="button" class="btn btn-twitter mt-1"><i class="fas fa-check-square"></i> Usar {{$idB}}</button>--}}
        {{--</div>--}}

        <button id="unificarBtn" type="submit" onclick="return confirm('Deseja realmente unificar os cadastros?');" class="btn btn-behance btn-lg btn-block mt-4"><i class="fas fa-users-cog"></i> Realizar Unificação <i class="fas fa-check"></i></button>

    </form>
@endif

@endsection

@push('css')
<link href="{{ asset('css/setic/unificacaoInternosJoin.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/setic/unificacaoInternosJoin.js') }}"></script>
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
@endpush