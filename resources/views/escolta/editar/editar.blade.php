@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-plus"></i> Editar Escolta</h3>
        <hr>
        <p>
            Adicionar dados e arquivos a escolta cadastrada.
        </p>
    </div>

    <form action="{{ route('escolta.editarStore', $id) }}" method="post" enctype="multipart/form-data">
        {!! csrf_field() !!}

        @if(is_null($dataRetorno))
        <div class="form-row">

            <div class="form-group col-sm-3" id="divDataRetorno">
                <label for="data_retorno">Data Retorno</label>
                <input type="text" name="data_retorno" class="form-control" id="data_retorno" placeholder="Data do retorno" autocomplete="off">
            </div>

            <div class="form-group col-sm-3" id="divHoraRetorno">
                <label for="hora_retorno">Hora do Retorno</label>
                <input type="text" name="hora_retorno" class="form-control" id="hora_retorno" placeholder="Hora do Retorno" autocomplete="off">
            </div>

        </div>
        @endif

        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="inputFile">Arquivos</label>
                <input type="file" name="inputFile[]" class="form-control" id="inputFile" accept="application/pdf" multiple>
            </div>
        </div>

        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Salvar Dados</button>

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

