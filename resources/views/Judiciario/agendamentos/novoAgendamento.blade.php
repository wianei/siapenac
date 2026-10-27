@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fa fa-plus"></i> Agendamento de Escolta para Audiência</h3>
        <hr>
        <p>
            Agendar Escolta para audiência.
        </p>
    </div>

    <div class="alert alert-danger displayNone" id="divDanger" role="alert">
    </div>

    <div id="flashRetorno"></div>

    <form action="{{ route('judiciario.novoAgendamentoStore') }}" id="formCadastroAgendamento" method="post" enctype="multipart/form-data">
        {!! csrf_field() !!}
        <div class="form-row">
            <div class="form-group col-sm-12">
                <label for="interno">Nome do interno</label>
                <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome do interno" autocomplete="off">
                <div class="form-group col-sm-12" id="filter-records"></div>

            </div>


        </div>

        <h5 class="titleInterno">Internos <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableInternos" class="table-responsive-md">
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

        <div class="form-row">

            <div class="form-group col-sm-12">
                <label for="destino">Destino</label>
                <select id="destino" name="destino" class="form-control" required>
                    <option value="">
                        Selecione
                    </option>
                    @foreach($destinos as $destino)
                        <option value="{{ $destino->vara." - ".$destino->comarca }}">
                            {{ $destino->vara." - ".$destino->comarca }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3" id="divHoraRetorno">
                <label for="data">Data</label>
                <input type="text" name="data" class="form-control" id="data" placeholder="Data da escolta" autocomplete="off" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="horario">Horário</label>
                <select id="horario" name="horario" class="form-control" required>
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divHoraRetorno">
                <label for="documento">Número Documento</label>
                <input type="text" name="documento" class="form-control" id="documento" placeholder="Informe o número documento">
            </div>

        </div>

        <div class="form-row">
            <div class="form-group col-sm-12">
                <label for="descricao">Observações</label>
            </div>
        </div>

        <textarea name="descricao" id="descricao" class="form-control"></textarea>

        <div class="form-row mt-3">
            <div class="form-group col-sm-3">
                <label for="inputFile">Arquivos</label>
                <input type="file" name="inputFile[]" class="form-control" id="inputFile" accept="application/pdf" multiple>
            </div>
        </div>


        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Salvar Solicitação</button>


    </form>

@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{ asset('css/judiciario/agendamento/novoAgendamento.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/judiciario/agendamento/novoAgendamento.js') }}"></script>
{{--<script src="{{ asset('js/vendor/buscaInternoGeral.js') }}"></script>--}}
@endpush

