@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        @if(!isset($equipamento))
            <h3><i class="mdi mdi-plus"></i> Cadastro de Equipamento</h3>
        @else
            <h3><i class="mdi mdi-pencil-circle-outline"></i> Alterar Equipamento</h3>
        @endif
        <hr>
        <p>
            @if(!isset($equipamento))
                Cadastrar os equipamentos sob responsabilidade da {{ config('app.secretaria') }} {{ config('app.uf') }}.
            @else
                Alterar os equipamentos sob responsabilidade da {{ config('app.secretaria') }} {{ config('app.uf') }}.
            @endif
        </p>
    </div>
    @if(!isset($equipamento))
    <form action="{{ route('equipamento.store') }}" method="post"  id="formCadastroArmamento">
        @else
    <form action="{{ route('armaria.updateEquipamento', $equipamento[0]->id) }}" method="post"  id="formCadastroArmamento">
    @endif

        {!! csrf_field() !!}

        @if(!isset($equipamento))
            <div class="form-row">

                <div class="form-group col-sm-3" id="divTipoArmamento">
                    <label for="tipoArmamento">Tipo de Equipamento</label>
                    <select id="tipoArmamento" name="tipoArmamento" class="form-control">
                        <option value="">
                            Selecione
                        </option>
                        <option value="armaFogo">
                            Arma de Fogo
                        </option>
                        <option value="municao">
                            Munição
                        </option>
                        <option value="carregador">
                            Carregador
                        </option>
                        <option value="algema">
                            Algema
                        </option>
                        <option value="granada">
                            Granada
                        </option>
                        <option value="epi">
                            EPI
                        </option>
                        <option value="colete">
                            Colete
                        </option>
                        <option value="espargidor">
                            Espargidor
                        </option>
                        <option value="dei">
                            Dispositivo Elétrico Incapacitante
                        </option>
                    </select>
                </div>

            </div>
    @endif
        <input type="hidden" name="tipoEqupEditar" id="tipoEqupEditar" value="{{ $tipo or old('tipoEqupEditar') }}">
        @if(isset($tipo) && $tipo == "armaFogo")
            <input type="hidden" name="equipamento_especie" id="equipamento_especie" value="{{ $equipamento[0]->equipamento_especie }}">
            <input type="hidden" name="equipamento_especificacao" id="equipamento_especificacao" value="{{ $equipamento[0]->equipamento_especificacao }}">
        @endif

        @if(isset($tipo) && $tipo == "dei")
            <input type="hidden" name="equipamento_especie" id="equipamento_especie" value="{{ $equipamento[0]->equipamento_especie }}">
            <input type="hidden" name="equipamento_especificacao" id="equipamento_especificacao" value="{{ $equipamento[0]->equipamento_especificacao }}">
        @endif

        @if(isset($tipo) && $tipo == "municao")
            <input type="hidden" name="equipamento_especie" id="equipamento_especie" value="{{ $equipamento[0]->equipamento_especie }}">
            <input type="hidden" name="equipamento_especificacao" id="equipamento_especificacao" value="{{ $equipamento[0]->equipamento_especificacao }}">
            <input type="hidden" name="municaoEspecie" id="municaoEspecie" value="{{ $equipamento[0]->modelo }}">
        @endif

        @if(isset($tipo) && $tipo == "carregador")
            <input type="hidden" name="equipamento_especie" id="equipamento_especie" value="{{ $equipamento[0]->equipamento_especie }}">
            <input type="hidden" name="armamentoCarregador" id="armamentoCarregador" value="{{ $equipamento[0]->armamento }}">
            <input type="hidden" name="capacidadeCarregador" id="capacidadeCarregador" value="{{ $equipamento[0]->volume_peso }}">
        @endif

        @if(isset($tipo) && $tipo == "algema")
            <input type="hidden" name="equipamento_especie" id="equipamento_especie" value="{{ $equipamento[0]->equipamento_especie }}">
            <input type="hidden" name="equipamento_especificacao" id="equipamento_especificacao" value="{{ $equipamento[0]->equipamento_especificacao }}">
        @endif

        @if(isset($tipo) && $tipo == "granada")
            <input type="hidden" name="equipamento_especie" id="equipamento_especie" value="{{ $equipamento[0]->equipamento_especie }}">
            <input type="hidden" name="equipamento_especificacao" id="equipamento_especificacao" value="{{ $equipamento[0]->equipamento_especificacao }}">
            <input type="hidden" name="modeloGranadaEditar" id="modeloGranadaEditar" value="{{ $equipamento[0]->modelo }}">
        @endif

        @if(isset($tipo) && $tipo == "epi")
            <input type="hidden" name="equipamento_especie" id="equipamento_especie" value="{{ $equipamento[0]->equipamento_especie }}">
        @endif

        <div class="form-row" id="divInputs">

            <div class="form-group col-sm-3" id="divTipo">
                <label for="tipo">Tipo</label>
                <select id="tipo" name="tipo" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divArmamento">
                <label for="armamento">Armamento</label>
                <select id="armamento" name="armamento" class="form-control">
                </select>
            </div>

            <div class="form-group col-sm-3" id="divTipoAlgema">
                <label for="tipoAlgema">Tipo</label>
                <select id="tipoAlgema" name="tipoAlgema" class="form-control">
                </select>
            </div>

            <div class="form-group col-sm-3" id="divTipoGranada">
                <label for="tipoGranada">Tipo</label>
                <select id="tipoGranada" name="tipoGranada" class="form-control">
                </select>
            </div>

            <div class="form-group col-sm-3" id="divModeloGranada">
                <label for="modeloGranada">Modelo</label>
                <select id="modeloGranada" name="modeloGranada" class="form-control">
                </select>
            </div>

            <div class="form-group col-sm-3" id="divCalibre">
                <label for="calibre">Calibre</label>
                <select id="calibre" name="calibre" class="form-control">
                    <option value="" @if( isset( $equipamento ) && $equipamento[0]->calibre == "" ) selected @endif>
                        Selecione
                    </option>
                    <optgroup label="LETAL">
                        <option value="12" @if( isset( $equipamento ) && $equipamento[0]->calibre == "12" ) selected @endif>
                            12
                        </option>
                        <option value="38" @if( isset( $equipamento ) && $equipamento[0]->calibre == "38" ) selected @endif>
                            38
                        </option>
                        <option value="40" @if( isset( $equipamento ) && $equipamento[0]->calibre == "40" ) selected @endif>
                            40
                        </option>
                        <option value="45" @if( isset( $equipamento ) && $equipamento[0]->calibre == "45" ) selected @endif>
                            45
                        </option>
                        <option value="9" @if( isset( $equipamento ) && $equipamento[0]->calibre == "9" ) selected @endif>
                            9mm
                        </option>
                        <option value="556" @if( isset( $equipamento ) && $equipamento[0]->calibre == "556" ) selected @endif>
                            5.56
                        </option>
                        <option value="762" @if( isset( $equipamento ) && $equipamento[0]->calibre == "762" ) selected @endif>
                            7.62
                        </option>
                    </optgroup>
                    <optgroup label="MENOS LETAL">
                        <option value="37/38mm" @if( isset( $equipamento ) && $equipamento[0]->calibre == "37/38mm" ) selected @endif>
                            37/38mm
                        </option>
                        <option value="37/40mm" @if( isset( $equipamento ) && $equipamento[0]->calibre == "37/40mm" ) selected @endif>
                            37/40mm
                        </option>
                        <option value="38.1mm" @if( isset( $equipamento ) && $equipamento[0]->calibre == "38.1mm" ) selected @endif>
                            38.1mm
                        </option>
                        <option value="40mm" @if( isset( $equipamento ) && $equipamento[0]->calibre == "40mm" ) selected @endif>
                            40mm
                        </option>
                    </optgroup>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divCapacidade">
                <label for="capacidade">Capacidade</label>
                <select id="capacidade" name="capacidade" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divTipoMunicao">
                <label for="tipoMunicao">Tipo de Munição</label>
                <select id="tipoMunicao" name="tipoMunicao" class="form-control">
                    <option value="" @if( isset( $equipamento ) && $equipamento[0]->equipamento_especificacao == "" ) selected @endif>
                        Selecione
                    </option>
                    <option value="real" @if( isset( $equipamento ) && $equipamento[0]->equipamento_especificacao == "real" ) selected @endif>
                        Real
                    </option>
                    <option value="treina" @if( isset( $equipamento ) && $equipamento[0]->equipamento_especificacao == "treina" ) selected @endif>
                        Treina
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divMunicao">
                <label for="municao">Munição</label>
                <select id="municao" name="municao" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="real">
                        CHPP
                    </option>
                    <option value="treina">
                        CHOG
                    </option>
                    <option value="treina">
                        CSCV
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divFabricante">
                <label for="fabricante">Fabricante</label>
                <select id="fabricante" name="fabricante" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    @if(isset($equipamento))
                        @foreach($fabricantes as $fabricante)
                            <option value="{{ $fabricante->id }}" @if( isset( $equipamento ) && $fabricante->id == $equipamento[0]->fk_fabricante ) selected @endif>{{ $fabricante->nome }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="form-group col-sm-3" id="divModelo">
                <label for="modelo">Modelo</label>
                <input type="text" name="modelo" value="{{ $equipamento[0]->modelo or old('modelo')}}" class="form-control" id="modelo" placeholder="Informe o modelo">
            </div>

            <div class="form-group col-sm-3" id="divValidade">
                <label for="validade">Validade</label>
                <input type="text" name="validade" value="{{ $equipamento[0]->validade or old('validade')}}" class="form-control" id="validade" placeholder="Informe a validade">
            </div>

            <div class="form-group col-sm-3" id="divTamanho">
                <label for="tamanho">Tamanho</label>
                <select id="tamanho" name="tamanho" class="form-control">
                    <option value="" @if( isset( $equipamento ) && $equipamento[0]->tamanho == "" ) selected @endif>
                        Selecione
                    </option>
                    <option value="PP" @if( isset( $equipamento ) && $equipamento[0]->tamanho == "PP" ) selected @endif>
                        PP
                    </option>
                    <option value="P" @if( isset( $equipamento ) && $equipamento[0]->tamanho == "P" ) selected @endif>
                        P
                    </option>
                    <option value="M" @if( isset( $equipamento ) && $equipamento[0]->tamanho == "M" ) selected @endif>
                        M
                    </option>
                    <option value="G" @if( isset( $equipamento ) && $equipamento[0]->tamanho == "G" ) selected @endif>
                        G
                    </option>
                    <option value="GG" @if( isset( $equipamento ) && $equipamento[0]->tamanho == "GG" ) selected @endif>
                        GG
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divLote">
                <label for="lote">Lote</label>
                <input type="text" name="lote" value="{{ $equipamento[0]->lote or old('lote')}}" class="form-control" id="lote" placeholder="Informe o Lote">
            </div>

            <div class="form-group col-sm-3" id="divVolumePeso">
                <label for="volume_peso">Volume ou Peso</label>
                <input type="text" name="volume_peso" value="{{ $equipamento[0]->volume_peso or old('volume_peso')}}" class="form-control" id="volume_peso" placeholder="Ex: 500ml ou 100g">
            </div>

            <div class="form-group col-sm-3" id="divN_serie">
                <label for="n_serie">Nº de Série</label>
                <input type="text" name="n_serie" value="{{ $equipamento[0]->n_serie or old('n_serie')}}" class="form-control" id="n_serie" placeholder="Informe o número de série">
            </div>

        </div>

        <div class="form-row @if(!isset($equipamento)) displayNone @endif" id="dadosGerais">

            <div class="form-group col-sm-3">
                <label for="n_tombo">Nº de Tompo</label>
                <input type="text" name="n_tombo" value="{{ $equipamento[0]->n_tombo or old('n_tombo')}}" class="form-control" id="n_tombo" placeholder="Informe o número de tombo">
            </div>

            <div class="form-group col-sm-3">
                <label for="origem">Origem da Aquisição</label>
                <select id="origem" name="origem" class="form-control">
                    <option value="" @if( isset( $equipamento ) && $equipamento[0]->origem_aquisicao == "" ) selected @endif>
                        Selecione
                    </option>
                    <option value="doacao" @if( isset( $equipamento ) && $equipamento[0]->origem_aquisicao == "doacao" ) selected @endif>
                        Doação
                    </option>
                    <option value="cautela" @if( isset( $equipamento ) && $equipamento[0]->origem_aquisicao == "cautela" ) selected @endif>
                        Cautela
                    </option>
                    <option value="aquisicao" @if( isset( $equipamento ) && $equipamento[0]->origem_aquisicao == "aquisicao" ) selected @endif>
                        Aquisição Própria
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-6">
                <label for="obs">Observação</label>
                <input type="text" name="obs" value="{{ $equipamento[0]->obs or old('obs')}}" class="form-control" id="obs" placeholder="Obsertvação">
            </div>

        </div>

        @if(!isset($equipamento))
        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Salvar Equipamento</button>
        @else
        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Salvar Alterações</button>
        @endif

    </form>

@endsection

@push('css')
@if(!isset($equipamento))
<link href="{{ asset('css/armaria/cadastro/cadastro.css') }}" rel="stylesheet">
    @else
    <link href="{{ asset('css/armaria/cadastro/cadastro.css') }}" rel="stylesheet">
@endif
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
@if(!isset($equipamento))
<script src="{{ asset('js/armaria/cadastroArmamento.js') }}"></script>
    @else
    <script src="{{ asset('js/armaria/editarArmamento.js') }}"></script>
@endif
@endpush

