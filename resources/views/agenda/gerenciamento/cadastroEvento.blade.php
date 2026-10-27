@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-plus"></i> Cadastro de Evento</h3>
        <hr>
        <p>Cadastro de eventos na agenda da unidade.
        </p>
    </div>

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

            <div class="form-group col-sm-3">
                <label for="recorrente">Evento Recorrente</label>
                <div class="ml-1 mt-2 fontSize-13px">
                    Não       <input type="radio" name="recorrente" id="recorrenteNao" value="N" class="radioRecorrente" checked="">
                    Sim       <input type="radio" name="recorrente" id="recorrenteSim" value="S" class="radioRecorrente mr-3">
                </div>

            </div>

            <div class="form-group col-sm-3" id="divDataEvento">
                <label for="dataEvento">Data do Evento</label>
                <input type="text" name="dataEvento" class="form-control" id="dataEvento" placeholder="Informe a data do evento">
            </div>

            <div class="form-group col-sm-3 displayNone" id="divDiasSemana">
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

            <div class="form-group col-sm-3">
                <label for="local">Setor</label>
                <input type="text" name="local" class="form-control" id="local" placeholder="Informe o setor">
            </div>

            <div class="form-group col-sm-12">
                <label for="titulo">Título</label>
                <input type="text" name="titulo" class="form-control" id="titulo" placeholder="Informe o título">
            </div>

        </div>
        <div class="form-row">
            <div class="form-group col-sm-12">
                <label for="descricao">Descrição</label>
            </div>
        </div>
        <textarea name="descricao" id="descricao" rows="5" class="form-control"></textarea>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Salvar Evento</button>

    </form>


@endsection
@push('css')
<link href="{{ asset('css/agenda/cadastroEvento.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/agenda/cadastroEvento.js') }}"></script>
@endpush