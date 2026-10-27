@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-3">
        <h3><i class="mdi mdi-settings"></i> Gerenciamento da Agenda</h3>
        <hr>
        <p>Gerenciamento de tarefas da agenda.
        </p>
    </div>

    <a href="{{ route('agenda.cadastroEvento') }}" class="btn btn-success"> <i class="fa fa-plus"></i> Cadastrar Novo Evento</a>
    <hr>

    <form action="">
        <div class="form-row">
            <div class="form-group col-sm-3" id="divUnidade">
                <label for="unidade">Unidade</label>
                <select id="unidade" name="unidade" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>

            </div>

            <div class="form-group col-sm-3" id="divPavilhao">
                <label for="unidade">Pavilhão</label>
                <select id="unidade" name="unidade" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>

            </div>

            <div class="form-group col-sm-3" id="divDataEvento">
                <label for="dataEvento">Data do Evento</label>
                <input type="text" name="dataEvento" class="form-control" id="dataEvento" placeholder="Informe a data do evento">
            </div>

            <div class="form-group col-sm-3" id="divDiasSemana">
                <label for="diasSemana">Dia da Semana</label>
                <select id="diasSemana" name="diasSemana" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="segunda">
                        Segunda-Feira
                    </option>
                    <option value="terca">
                        Terça-Feira
                    </option>
                    <option value="quarta">
                        Quarta-Feira
                    </option>
                    <option value="quinta">
                        Quinta-Feira
                    </option>
                    <option value="sexta">
                        Sexta-Feira
                    </option>
                    <option value="sabado">
                        Sábado
                    </option>
                    <option value="domingo">
                        Domingo
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="horarioInicio">Horário de Início</label>
                <input type="text" name="horarioInicio" class="form-control" id="horarioInicio" placeholder="Informe o horário de inicio">
            </div>

            <div class="form-group col-sm-3">
                <label for="horarioTermino">Horário de Término</label>
                <input type="text" name="horarioTermino" class="form-control" id="horarioTermino" placeholder="Informe o horário de término">
            </div>

            <div class="form-group col-sm-12">
                <label for="titulo">Título</label>
                <input type="text" name="titulo" class="form-control" id="titulo" placeholder="Informe o título">
            </div>

        </div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-search"></i> Buscar Eventos</button>

    </form>

    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">Data</th>
                <th scope="col">Título</th>
                <th scope="col">Dia da Semana</th>
                <th scope="col">Horário</th>
                <th scope="col">Localização</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            <tr class="">
                <td>06/09</td>
                <td>Atendimento Médico</td>
                <td>Segunda</td>
                <td>08:00 - 11:00</td>
                <td>PEA - Pavilhão 01</td>
                <td class="btn-acoes color_01579b">
                    <a href="#" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>
                    <a href="#" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>
                    <a href="#" data-toggle="tooltip" data-placement="top" title="Cancelar"><i class="mdi mdi-cancel"></i></a>
                </td>
            </tr>
            </tbody>
        </table>

    </div>


@endsection
@push('css')
<link href="{{ asset('css/agenda/cadastroEvento.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/agenda/cadastroEvento.js') }}"></script>
@endpush