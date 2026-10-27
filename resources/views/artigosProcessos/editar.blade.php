@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-note-text"></i> Editar Artigos e Processos</h3>
        <hr>
        <p>
            Cadastrar artigo de crime ou processo que o interno responda.
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

    <form id="cadastroForm" action="{{ route('artigosProcessos.update',$id) }}" method="post">

        {!! csrf_field() !!}

        <input type="hidden" name="tipoCadastro" id="tipoCadastro" value="{{  $dadosProcesso[0]["tipo"] }}">
        <input type="hidden" name="idProcesso" id="idProcesso" value="{{  $dadosProcesso[0]["dadosProcesso"][0]["id"] }}">


        <h4 class="mb-4">Interno: {{ $dadosProcesso[0]["nomeInterno"] }}</h4>

        @if($dadosProcesso[0]["tipo"] == "inqueritoPolicial" )
        <div class="form-row border1pxSolid_CCC p-3 tipoCadastro" id="divInquerito">
            <div class="form-group col-sm-3">
                <label for="numeroInquerito">Número do Inquérito</label>
                <input type="text" value="{{ $dadosProcesso[0]['dadosProcesso'][0]['numero'] or old('numeroInquerito') }}" name="numeroInquerito" class="form-control" id="numeroInquerito" placeholder="Informe o número do inquérito policial">
            </div>

            <div class="form-group col-sm-3">
                <label for="dataInquerito">Data do Inquérito Policial</label>
                <input type="text" value="{{ $dadosProcesso[0]['dadosProcesso'][0]['dataInquerito'] or old('dataInquerito') }}" name="dataInquerito" class="form-control" id="dataInquerito" placeholder="Informe a data do inquérito policial">
            </div>

            <div class="form-group col-sm-3">
                <label for="dataFato">Data do Fato</label>
                <input type="text"  value="{{ $dadosProcesso[0]['dadosProcesso'][0]['dataFato'] or old('dataFato') }}" name="dataFato" class="form-control" id="dataFato" placeholder="Informe a data que o fato ocorreu">
            </div>

            <div class="form-group col-sm-3">
                <label for="dataPrisao">Data da Prisão</label>
                <input type="text" value="{{ $dadosProcesso[0]['dadosProcesso'][0]['dataPrisao'] or old('dataPrisao') }}" name="dataPrisao" class="form-control" id="dataPrisao" placeholder="Informe a data da prisão">
            </div>

            <div class="form-group col-sm-3">
                <label for="ufInquerito">UF</label>
                <select id="ufInquerito" name="ufInquerito" class="form-control selectEstados">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidadesFederativas as $uf)
                        <option value="{{ $uf->uf }}" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["uf"] == "$uf->uf") selected @endif>{{ $uf->nome }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="municipioInquerito">Município</label>
                <select id="municipioInquerito" name="municipioInquerito" class="form-control">
                    @foreach($municipios as $muncipio)
                        <option value="{{ $muncipio->Nome }}" @if( isset( $municipios ) && $dadosProcesso[0]['dadosProcesso'][0]["municipio"] == $muncipio->Nome) selected @endif> {{ $muncipio->Nome }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-6">
                <label for="delegacia">Delegacia</label>
                <input type="text" value="{{ $dadosProcesso[0]['dadosProcesso'][0]['delegacia'] or old('delegacia') }}" name="delegacia" class="form-control" id="delegacia" placeholder="Informe a delegacia">
            </div>

            <div class="form-group col-sm-3">
                <label for="motivoPrisao">Motivo da Prisão</label>
                <select id="motivoPrisao" name="motivoPrisao" class="form-control">
                    <option value="Nenhum" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "") selected @endif>Nenhum</option>
                    <option value="Cumprimento de Sentença" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "Cumprimento de Sentença") selected @endif>Cumprimento de Sentença</option>
                    <option value="Decorrente de Sentença de Pronúncia" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "Decorrente de Sentença de Pronúncia") selected @endif>Decorrente de Sentença de Pronúncia</option>
                    <option value="Flagrante" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "Flagrante") selected @endif>Flagrante</option>
                    <option value="Mandado de Prisão Preventiva" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "Mandado de Prisão Preventiva") selected @endif>Mandado de Prisão Preventiva</option>
                    <option value="Mandado de Prisão Provisório" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "Mandado de Prisão Provisório") selected @endif>Mandado de Prisão Provisório</option>
                    <option value="Mandado de Prisão Temporária" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "Mandado de Prisão Temporária") selected @endif>Mandado de Prisão Temporária</option>
                    <option value="Mandado de Prisão Condenatório" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "Mandado de Prisão Condenatório") selected @endif>Mandado de Prisão Condenatório</option>
                    <option value="Recomendação de Prisão" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "Recomendação de Prisão") selected @endif>Recomendação de Prisão</option>
                    <option value="Sentença Penal Recorrível" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "Sentença Penal Recorrível") selected @endif>Sentença Penal Recorrível</option>
                </select>
            </div>
        </div>
        @endif

        @if($dadosProcesso[0]["tipo"] == "processo" )
            <div class="form-row border1pxSolid_CCC p-3 tipoCadastro" id="divProcesso">

            <div class="form-group col-sm-3">
                <label for="numeroProcesso">Nº do Processo</label>
                <input type="text" value="{{ $dadosProcesso[0]['dadosProcesso'][0]['numero'] or old('numeroProcesso') }}" name="numeroProcesso" class="form-control" id="numeroProcesso" placeholder="Informe o número do processo">
            </div>

            <div class="form-group col-sm-3">
                <label for="dataProcesso">Data do Processo</label>
                <input type="text" value="{{ $dadosProcesso[0]['dadosProcesso'][0]['dataProcesso'] or old('dataProcesso') }}"  name="dataProcesso" class="form-control" id="dataProcesso" placeholder="Informe a data do processo">
            </div>

            <div class="form-group col-sm-3">
                <label for="guiaRecolhimento">Guia de Recolhimento</label>
                <input type="text" value="{{ $dadosProcesso[0]['dadosProcesso'][0]['guia_recolhimento'] or old('guiaRecolhimento') }}" name="guiaRecolhimento" class="form-control" id="guiaRecolhimento" placeholder="Informe a guia de recolhimento">
            </div>

            <div class="form-group col-sm-3">
                <label for="motivoPrisaoProcesso">Motivo da Prisão</label>
                <select id="motivoPrisaoProcesso" name="motivoPrisaoProcesso" class="form-control">
                    <option value="Nenhum" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "") selected @endif>Nenhum</option>
                    <option value="Cumprimento de Sentença" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "Cumprimento de Sentença") selected @endif>Cumprimento de Sentença</option>
                    <option value="Decorrente de Sentença de Pronúncia" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "Decorrente de Sentença de Pronúncia") selected @endif>Decorrente de Sentença de Pronúncia</option>
                    <option value="Flagrante" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "Flagrante") selected @endif>Flagrante</option>
                    <option value="Mandado de Prisão Preventiva" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "Mandado de Prisão Preventiva") selected @endif>Mandado de Prisão Preventiva</option>
                    <option value="Mandado de Prisão Provisório" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "Mandado de Prisão Provisório") selected @endif>Mandado de Prisão Provisório</option>
                    <option value="Mandado de Prisão Temporária" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "Mandado de Prisão Temporária") selected @endif>Mandado de Prisão Temporária</option>
                    <option value="Mandado de Prisão Condenatório" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "Mandado de Prisão Condenatório") selected @endif>Mandado de Prisão Condenatório</option>
                    <option value="Recomendação de Prisão" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "Recomendação de Prisão") selected @endif>Recomendação de Prisão</option>
                    <option value="Sentença Penal Recorrível" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["motivo_prisao"] == "Sentença Penal Recorrível") selected @endif>Sentença Penal Recorrível</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="esferaJustica">Esfera Judicial</label>
                <select id="esferaJustica" name="esferaJustica" class="form-control">
                    <option value="Estadual" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["esfera_judicial"] == "Estadual") selected @endif>Estadual</option>
                    <option value="Federal" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["esfera_judicial"] == "Federal") selected @endif>Federal</option>
                    <option value="Militar" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["esfera_judicial"] == "Militar") selected @endif>Militar</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="reu">Réu</label>
                <select id="reu" name="reu" class="form-control">
                    <option value="" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["reu"] == "") selected @endif>Selecione</option>
                    <option value="Primário" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["reu"] == "Primário") selected @endif>Primário</option>
                    <option value="Reincidente" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["reu"] == "Reincidente") selected @endif>Reincidente</option>
                    <option value="Tecnicamente Primário" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["reu"] == "Tecnicamente Primário") selected @endif>Tecnicamente Primário</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="ufProcesso">UF</label>
                <select id="ufProcesso" name="ufProcesso" class="form-control selectEstados">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidadesFederativas as $uf)
                        <option value="{{ $uf->uf }}" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["uf"] == "$uf->uf") selected @endif>{{ $uf->nome }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="municipioProcesso">Município</label>
                <select id="municipioProcesso" name="municipioProcesso" class="form-control">
                    @foreach($municipios as $muncipio)
                        <option value="{{ $muncipio->Nome }}" @if( isset( $municipios ) && $dadosProcesso[0]['dadosProcesso'][0]["municipio"] == $muncipio->Nome) selected @endif> {{ $muncipio->Nome }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-6">
                <label for="varaProcesso">Vara</label>
                <input type="text" value="{{ $dadosProcesso[0]['dadosProcesso'][0]['vara'] or old('varaProcesso') }}" name="varaProcesso" class="form-control" id="varaProcesso" placeholder="Informe a vara do processo">
            </div>

        </div>
        @endif

        @if($dadosProcesso[0]["tipo"] == "condenacao" )
            <div class="form-row border1pxSolid_CCC p-3 tipoCadastro" id="divCondenacao">

            <div class="form-group col-sm-3">
                <label for="numeroProcessoCondenacao">Nº do Processo</label>
                <input type="text" value="{{ $dadosProcesso[0]['dadosProcesso'][0]['numero'] or old('numeroProcessoCondenacao') }}" name="numeroProcessoCondenacao" class="form-control" id="numeroProcessoCondenacao" placeholder="Informe o número do processo">
            </div>

            <div class="form-group col-sm-3">
                <label for="dataCondenacaoTransJulgado">Data do Trânsito em Julgado</label>
                <input type="text" value="{{ $dadosProcesso[0]['dadosProcesso'][0]['dataTransitoJulgado'] or old('dataCondenacaoTransJulgado') }}" name="dataCondenacaoTransJulgado" class="form-control" id="dataCondenacaoTransJulgado" placeholder="Informe a data da condenação">
            </div>

            <div class="form-group col-sm-3">
                <label for="dataCondenacaoSentenca">Data da Sentença</label>
                <input type="text" value="{{ $dadosProcesso[0]['dadosProcesso'][0]['dataSentenca'] or old('dataCondenacaoSentenca') }}" name="dataCondenacaoSentenca" class="form-control" id="dataCondenacaoSentenca" placeholder="Informe a data da sentença">
            </div>

            <div class="form-group col-sm-3">
                <label for="condenacaoAnos">Quantidade de Anos</label>
                <input type="text" value="{{ $dadosProcesso[0]['dadosProcesso'][0]['quantidade_anos'] or old('condenacaoAnos') }}" name="condenacaoAnos" class="form-control" id="condenacaoAnos" placeholder="Informe a quantidade de anos">
            </div>

            <div class="form-group col-sm-3">
                <label for="condenacaoMeses">Quantidade de Meses</label>
                <input type="text" value="{{ $dadosProcesso[0]['dadosProcesso'][0]['quantidade_meses'] or old('condenacaoMeses') }}" name="condenacaoMeses" class="form-control" id="condenacaoMeses" placeholder="Informe a quantidade de meses">
            </div>

            <div class="form-group col-sm-3">
                <label for="condenacaoDias">Quantidade de Dias</label>
                <input type="text" value="{{ $dadosProcesso[0]['dadosProcesso'][0]['quantidade_dias'] or old('condenacaoDias') }}" name="condenacaoDias" class="form-control" id="condenacaoDias" placeholder="Informe a quantidade de dias">
            </div>

            <div class="form-group col-sm-3">
                <label for="tipoPrisao">Tipo de Prisão</label>
                <select id="tipoPrisao" name="tipoPrisao" class="form-control">
                    <option value="" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["tipo_prisao"] == "") selected @endif>Selecione</option>
                    <option value="Detenção" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["tipo_prisao"] == "Detenção") selected @endif>Detenção</option>
                    <option value="Prisão Civil" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["tipo_prisao"] == "Prisão Civil") selected @endif>Prisão Civil</option>
                    <option value="Prisão Simples" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["tipo_prisao"] == "Prisão Simples") selected @endif>Prisão Simples</option>
                    <option value="Reclusão" @if( isset( $dadosProcesso ) && $dadosProcesso[0]['dadosProcesso'][0]["tipo_prisao"] == "Reclusão") selected @endif>Reclusão</option>
                </select>
            </div>

        </div>
        @endif

        <div class="form-row mt-3 border1pxSolid_CCC p-3" id="divDispositivoLegal">

            <div class="form-group col-sm-6">
                <label for="dispositivoLegal">Dispositivo Legal</label>
                <select id="dispositivoLegal" name="dispositivoLegal[]" class="form-control dispositivoLegal">
                    <option value="">Selecione</option>
                    @foreach($dispositivoLegal as $dispositivo)
                        <option value="{{ $dispositivo->id }}">{{ $dispositivo->nome }} - {{$dispositivo->texto}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-6 displayNone divArtigo">
                <label for="artigo">Artigo</label>
                <select id="artigo" name="artigo[]" class="form-control artigo">
                    <option value="">Selecione</option>
                </select>
            </div>

            <div class="form-group col-sm-6 displayNone divParagrafo">
                <label for="paragrafo"><span class="tipoParagrafo" id="tipoParagrafo"></span></label>
                <select id="paragrafo" name="paragrafo[]" class="form-control paragrafo">
                    <option value="">Selecione</option>
                </select>
            </div>

            <input type="hidden" name="tipoSequencia[]" class="tipoSequencia" id="tipoSequencia" value="">
            <input type="hidden" name="tipoSequenciaUm[]" class="tipoSequenciaUm" id="tipoSequenciaUm" value="">
            <input type="hidden" name="tipoSequenciaDois[]" class="tipoSequenciaDois" id="tipoSequenciaDois" value="">

            <div class="form-group col-sm-6 displayNone divSubDois">
                <label for="subDois"><span class="txtSubDois"></span></label>
                <select id="subDois" name="subDois[]" class="form-control subDois">
                    <option value="">Selecione</option>
                </select>
            </div>
            
            <div class="alert alert-warning col-12 mt-3 resumo displayNone">
            </div>

            <div class="col-6 mt-4">
                <button type="button" id="addDispositivo" class="btn btn-primary">Adicionar +</button>
                <button type="button" class="btn btn-danger btnDelDispositivo" id="">Remover Campo</button>
            </div>

        </div>

        <div id="divDispositivoLegalClone"></div>

        <h4 class="mt-5">Dispositivos Legais Cadastrados <i class="mdi mdi-arrow-bottom-right"></i></h4>
        <div id="divLeisRemover"></div>
        <div class="row fontsize-12px">
            @if($dadosProcesso[0]["dispositivoLegal"] != null)
                @foreach($dadosProcesso[0]["dispositivoLegal"] as $lei)
                    <div class="col-12 p-3 border1pxSolidRGBA0125 mb-4" style="border-left: 5px solid #f3f3f3;">
                        <div class="row fontsize-11px">
                            <div class="col-12 mb-2 text" style="font-style: italic; font-size: 14px !important;">
                                <strong>{{ $lei["lei"] }}</strong><br>
                                {{ $lei["artigo"] }}<br>
                                {{ $lei["nome"] }}<br>
                                {{ $lei["glossario"] }}<br>
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger removerLei" data-content="{{ $lei['id'] }}"><i class="mdi mdi-delete"></i>Remover</button>
                    </div>
                @endforeach
            @else
                Sem informações.
            @endif
        </div>

        <div id="flashRetorno" class="mt-2"></div>
        <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Salvar Dados</button>

    </form>

@endsection
@push('css')
<link href="{{ asset('css/ocorrencia/buscar/buscar.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/artigosProcessos/editarArtigosProcessos.js') }}"></script>
@endpush