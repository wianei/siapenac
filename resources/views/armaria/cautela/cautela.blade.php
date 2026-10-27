@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-3">
        <h3><i class="mdi mdi-share"></i> Cautela de Equipamento</h3>
        <hr>
        <p>
            Buscar armamento na base de dados.
        </p>
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg)  !!}
                </p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    <form action="{{route('armaria.cautelaStore')}}" method="post" id="formCautela">
        {!! csrf_field() !!}
        <div class="divEquipamentoUtilizado">
            <h6>Equipamento</h6>
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
                        <option value="dei">
                            Dispositivo Elétrico Incapacitante
                        </option>
                    </select>
                </div>

                <div class="form-group col-sm-3 displayNone" id="divTipo">
                    <label for="tipo">Tipo Armamento</label>
                    <select id="tipo" name="tipo" class="form-control">
                        <option value="">
                            Selecione
                        </option>
                    </select>
                </div>

                <div class="form-group col-sm-3 displayNone" id="divQtd">
                    <label for="qtd">Quantidade</label>
                    <input type="text" name="qtd" class="form-control" id="qtd" placeholder="Informe a quantidade">
                </div>

                <div class="form-group col-sm-3 displayNone mt-4" id="divAdd">
                    <button type="button" class="btn btn-icons btn-inverse-primary">
                        <i class="mdi mdi-plus"></i>
                    </button>
                </div>


            </div>
        </div>

        <div id="tableEquipamentos" class="displayNone">

            <div class="row mb-1">
                <div class="col-sm-8">
                    <h5 class="titleEquipamento mt-3">Equipamentos <i class="mdi mdi-arrow-bottom-right"></i></h5>
                </div>

                <div class="col-sm-4">
                    <input type="text" name="filtroNome" class="form-control" id="filtroNome" placeholder="Digite um termo para filtrar a lista de equipamentos" autocomplete="off">
                </div>
            </div>
            <div id="equipamentosAgentes" class="table-responsive-md">
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
            <div id="equipamentosEscolta" class="table-responsive-md">
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

        </div>

        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="status">Cautela para</label>
                <div class="ml-1 mt-2 fontSize-13px">
                    Unidade       <input type="radio" name="cautelaPara" id="cautelaParaUnidade" value="unidade" checked=""> |
                    Servidor      <input type="radio" name="cautelaPara" id="cautelaParaServidor" value="servidor"> |
                    Outros        <input type="radio" name="cautelaPara" id="cautelaParaOutros" value="outros">
                </div>
            </div>

            <div class="form-group col-sm-3" id="divUnidade">
                <label for="unidade_prisional">Unidade Prisional</label>
                <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                    <option value="120">ESPEN</option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}">
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

        </div>

        <div class="form-row displayNone" id="divCautelaOutros">

            <div class="form-group col-sm-9">
                <label for="outrosNome">Nome</label>
                <input type="text" name="outrosNome" class="form-control" id="outrosNome" placeholder="Nome completo" autocomplete="off">
            </div>

            <div class="form-group col-sm-3">
                <label for="outrosCpf">CPF</label>
                <input type="text" name="outrosCpf" class="form-control" id="outrosCpf" placeholder="CPF" autocomplete="off">
            </div>

            <div class="form-group col-sm-6">
                <label for="outrosOrgao">Órgão de Vinculação</label>
                <input type="text" name="outrosOrgao" class="form-control" id="outrosOrgao" placeholder="Órgão de Vinculação" autocomplete="off">
            </div>

            <div class="form-group col-sm-3">
                <label for="outrosTelefone">Telefone</label>
                <input type="text" name="outrosTelefone" class="form-control" id="outrosTelefone" placeholder="Telefone para contato" autocomplete="off">
            </div>

        </div>


        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="sexo">Tempo Indeterminado</label>
                <select id="tempoIndeterminado" name="tempoIndeterminado" class="form-control">
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

            <div class="form-group col-sm-3 displayNone" id="dataInicial">
                <label for="rg_numero">Data Inicial</label>
                <input type="text" name="data_inicial" class="form-control" id="data_inicio" placeholder="Data de Início">
            </div>

            <div class="form-group col-sm-3 displayNone" id="dataFinal">
                <label for="rg_numero">Data Final</label>
                <input type="text" name="data_final" class="form-control" id="data_final" placeholder="Data Final">
            </div>

        </div>

        <div class="form-row" id="divRowAgentes">

            <div class="form-group col-sm-12">
                <label for="agente">Servidor Responsável</label>
                <input type="text" name="agente" class="form-control" id="agente" placeholder="Informe o nome do agente ou matrícula" autocomplete="off">
                <div class="form-group col-sm-12" id="divRetornoAgentes"></div>

            </div>

        </div>

        <h5 class="titleAgente displayNone">Servidor <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableAgentes" class="table-responsive-md displayNone">
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

        <div class="form-row">
            <label for="obs">Observações</label>
            <textarea name="obs" id="obs" class="form-control"></textarea>
        </div>
        <br/>

        <div class="mt-3" id="flashRetorno"></div>
        <button type="submit" class="btn btn-primary"><i class="mdi mdi-content-save"></i> Salvar Cautela</button>

    </form>

    {{-- modal --}}
    <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div id="fototipo"></div>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
                </div>
                <div class="modal-body embed-responsive text-center">
                    <img src="" id="imagepreview" style="max-width:100%">
                </div>
            </div>
        </div>
    </div>
    {{-- !! modal --}}
@endsection
@push('css')
<link href="{{ asset('css/armaria/cautela/cautela.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/armaria/cautela.js') }}"></script>
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
<script src="{{ asset('js/outros/buscaServidor.js') }}"></script>
@endpush