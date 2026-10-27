@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-plus"></i> Cadastro de Escoltas</h3>
        <hr>
        <p>
            Cadastrar escoltas externas (consultas médicas, prestação de socorro, oitiva em delegacia, etc...) e escoltas internas.
        </p>
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg) !!}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    <form action="{{ route('escolta.store') }}" id="formCadastroEscolta" method="post" enctype="multipart/form-data">
        {!! csrf_field() !!}
        <input type="hidden" name="tipoEscoltaHidden" id="tipoEscoltaHidden" value="">
        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="interno">Tipo de Escolta</label>

                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-outline-dark active radioTipoEscolta">
                        <input type="radio" name="tipo_escolta" id="tipo_escoltaInterna" value="interna" autocomplete="off" checked> Interna
                    </label>
                    <label class="btn btn-outline-dark radioTipoEscolta">
                        <input type="radio" name="tipo_escolta" id="tipo_escoltaExterna" value="externa" autocomplete="off"> Externa
                    </label>
                    <!-- <label class="btn btn-outline-dark radioTipoEscolta">
                        <input type="radio" name="tipo_escolta" id="tipo_escoltaGEP" value="gep" autocomplete="off"> FTPEN
                    </label> -->
                </div>
            </div>

        </div>

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
        <div id="tableInternosCela" class="table-responsive">
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
        <div id="tableInternos" class="table-responsive">
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
        <div id="tableAgentes" class="table-responsive">
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

                </tbody>
            </table>
        </div>

<!--        <div class="divEquipamentoUtilizado">
            <h6>Equipamento Utilizado</h6>
            <div class="form-row">
                <div class="form-group col-sm-3" id="divTipoArmamento">
                    <label for="tipoEquipamento">Tipo de Equipamento</label>
                    <select id="tipoEquipamento" name="tipoEquipamento" class="form-control">
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
                    </select>
                </div>

                <div class="form-group col-sm-3">
                    <label for="tipoArmamento">Tipo Armamento</label>
                    <select id="tipo" name="tipo" class="form-control">
                        <option value="">
                            Selecione
                        </option>
                        <option value="Longa">
                            Longa
                        </option>
                        <option value="Curta">
                            Curta
                        </option>

                    </select>
                </div>

            </div>
        </div>

        <div id="tableEquipamentos" class="displayNone">
            <h5 class="titleEquipamento">Equipamentos <i class="mdi mdi-arrow-bottom-right"></i></h5>
            <div id="equipamentosAgentes" class="table-responsive">
                <table class="table mb-3">
                    <thead class="background_3E4551 color_FFF">
                    <tr class="">
                        <th scope="col">#</th>
                        <th scope="col">Equipamento</th>
                    </tr>
                    </thead>
                    <tbody id="retornoEquipamentos">

                    </tbody>
                </table>
                <button type="button" id="btnAddEquipamentos" class="btn btn-success mt-3 mb-3"><i class="mdi mdi-plus"></i> Adicionar</button>
            </div>

        </div>

        <div id="tableEquipamentosCautela" class="displayNone">

            <h5 class="titleEquipamentoCautela">Equipamentos para Cautela<i class="mdi mdi-arrow-bottom-right"></i></h5>
            <div id="equipamentosEscolta" class="table-responsive">
                <table class="table mb-3">
                    <thead class="background_287ec3 color_FFF">
                    <tr class="">
                        <th scope="col">#</th>
                        <th scope="col">Equipamentos da Cautela</th>
                        <th scope="col">Ações</th>
                    </tr>
                    </thead>
                    <tbody id="retornoEquipamentosEscolta">

                    </tbody>
                </table>
            </div>

        </div>-->

        <div class="form-row mb-4">
            <label for="obs">Motivo</label>
            <textarea name="obs" id="obs" class="form-control" required></textarea>
        </div>

        <div class="form-row">

            <div class="form-group col-sm-6">
                <label for="destino">Destino</label>
                <input type="text" name="destino" class="form-control" id="destino" placeholder="Informe o Destino" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="data_saida">Data Saída</label>
                <input type="text" name="data_saida" class="form-control" id="data_saida" placeholder="Data de Saída" autocomplete="off" required>
            </div>

            <div class="form-group col-sm-3" id="divHoraSaida">
                <label for="hora_saida">Hora Saída</label>
                <input type="text" name="hora_saida" class="form-control" id="hora_saida" placeholder="Hora da Saída" autocomplete="off">
            </div>

            <div class="form-group col-sm-3" id="divDataRetorno">
                <label for="data_retorno">Data Retorno</label>
                <input type="text" name="data_retorno" class="form-control" id="data_retorno" placeholder="Data do retorno" autocomplete="off">
            </div>

            <div class="form-group col-sm-3" id="divHoraRetorno">
                <label for="hora_retorno">Hora do Retorno</label>
                <input type="text" name="hora_retorno" class="form-control" id="hora_retorno" placeholder="Hora do Retorno">
            </div>

            <div class="form-group col-sm-3" id="divUtilizacaoVTR">
                <label for="vtr">Utilização de VTR</label>
                <div class="ml-1 mt-2 fontSize-13px">
                    Sim       <input type="radio" name="vtr" id="vtr" value="S" class="mr-3">
                    Não       <input type="radio" name="vtr" id="vtr" value="N" checked="">
                </div>

            </div>

            <div class="form-group col-sm-3" id="divPlacaVTR">
                <label for="vtr_placa">Placa VTR</label>
                <input type="text" name="vtr_placa" class="form-control" id="vtr_placa" placeholder="Placa da VTR" maxlength="7">
            </div>

            <div class="form-group col-sm-6" id="divComandanteVTR">
                <label for="vtr_comandante">Comandante VTR</label>
                <input type="text" name="vtr_comandante" class="form-control" id="vtr_comandante" value="" disabled>
            </div>
            <input type="hidden" name="vtrComandanteHidden" id="vtrComandanteHidden" value="">
            <input type="hidden" name="vtrMotoristaHidden" id="vtrMotoristaHidden" value="">
            <div class="form-group col-sm-6" id="divMotoristaVTR">
                <label for="vtr_motorista">Motorista VTR</label>
                <input type="text" name="vtr_motorista" class="form-control" id="vtr_motorista" value="" disabled>
            </div>

        </div>
        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="inputFile">Arquivos</label>
                <input type="file" name="inputFile[]" class="form-control" id="inputFile" accept="application/pdf" multiple>
            </div>
        </div>

        <div id="flashRetorno"></div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Salvar Escolta</button>

    </form>

@endsection

@push('css')
<link href="{{ asset('css/escolta/cadastro/cadastro.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/escolta/cadastro.js') }}"></script>
<script src="{{ asset('js/outros/buscaInterno.js') }}"></script>
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>

@endpush

