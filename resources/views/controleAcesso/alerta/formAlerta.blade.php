@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-alert"></i> Alerta para Entrada</h3>
        <p>
            Definir alerta para aparecer ao registrar a entrada na unidade prisional.
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

    <form action="{{ route('controleAcesso.alertaEntradaGeralStore') }}" id="formCadastro" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="tipo">Tipo</label>
                <select id="tipo" name="tipo" class="form-control" required>
                    <option value="">Selecione</option>
                    <option value="advogado">Advogado</option>
                    <option value="visita">Visita</option>
                    <option value="visitanteExterno">Visitante Externo</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="dias">Dias a partir de hoje</label>
                <input type="text" name="dias" class="form-control" id="dias" placeholder="Ex: 10" required>
            </div>

        </div>

        <div id="divVisitaAll" class="displayNone">
            <div class="form-row">
                <div class="form-group col-sm-12">
                    <label for="visita">Visita(s)</label>
                    <input type="text" name="visita" class="form-control" id="visita" placeholder="Informe o nome da visita" autocomplete="off">
                    <div class="form-group col-sm-12" id="filter-records-visita"></div>
                </div>
            </div>
            <h5 class="titleVisita displayNone">Visita(s) <i class="mdi mdi-arrow-bottom-right"></i></h5>
            <div id="tableVisitas" class="table-responsive-md displayNone">
                <table class="table mb-3">
                    <thead class="background_81c784 color_FFF">
                    <tr class="">
                        <th scope="col">Foto</th>
                        <th scope="col">Nome</th>
                        <th scope="col">CPF</th>
                        <th scope="col">Ações</th>
                    </tr>
                    </thead>
                    <tbody id="retornoVisita">

                    </tbody>
                </table>
            </div>
        </div>

        <div id="divAdvAll" class="displayNone">
            <div class="form-row">
                <div class="form-group col-sm-12">
                    <label for="advogado">Advogado(s)</label>
                    <input type="text" name="advogado" class="form-control" id="advogado" placeholder="Informe o nome do advogado" autocomplete="off">
                    <div class="form-group col-sm-12" id="filter-records-advogado"></div>
                </div>
            </div>
            <h5 class="titleAdvogado displayNone">Advogado(s) <i class="mdi mdi-arrow-bottom-right"></i></h5>
            <div id="tableAdvogados" class="table-responsive-md displayNone">
                <table class="table mb-3">
                    <thead class="background_e65251 color_FFF">
                    <tr class="">
                        <th scope="col">Foto</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Nº OAB</th>
                        <th scope="col">Ações</th>
                    </tr>
                    </thead>
                    <tbody id="retornoAdvogado">

                    </tbody>
                </table>
            </div>
        </div>

        <div id="divVisitanteExternoAll" class="displayNone">
            <div class="form-row">
                <div class="form-group col-sm-12">
                    <label for="visitanteExterno">Visitante Externo</label>
                    <input type="text" name="visitanteExterno" class="form-control" id="visitanteExterno" placeholder="Informe o nome do visitante externo" autocomplete="off">
                    <div class="form-group col-sm-12" id="filter-records-visitanteExterno"></div>
                </div>
            </div>
            <h5 class="titleVisitanteExterno displayNone">Visitante Externo <i class="mdi mdi-arrow-bottom-right"></i></h5>
            <div id="tableVisitanteExterno" class="table-responsive-md displayNone">
                <table class="table mb-3">
                    <thead class="background_e65251 color_FFF">
                    <tr class="">
                        <th scope="col">Foto</th>
                        <th scope="col">Nome</th>
                        <th scope="col">CPF</th>
                        <th scope="col">Ações</th>
                    </tr>
                    </thead>
                    <tbody id="retornoVisitanteExterno">

                    </tbody>
                </table>
            </div>
        </div>


        <div class="form-row">
            <div class="form-group col-sm-12">
                <label for="motivo">Texto para alerta</label>
                <textarea name="motivo" id="motivo" class="form-control" required></textarea>
            </div>
        </div>

        <div id="flashRetorno"></div>

        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3">
                <i class="fas fa-save"></i>Salvar Alerta
        </button>

    </form>

    @endsection

@push('css')
<link href="{{ asset('css/controleAcesso/alerta/cadastro.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/controleAcesso/alerta/cadastroAlerta.js') }}"></script>
<script src="{{ asset('vendor/userCam/jquery.userCam.min.js') }}"></script>
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/outros/buscaVisita.js') }}"></script>
<script src="{{ asset('js/outros/buscaAdvogado.js') }}"></script>
<script src="{{ asset('js/outros/buscaVisitanteExterno.js') }}"></script>

@endpush

