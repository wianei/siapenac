@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-login"></i> Edição de Cadastro</h3>
        <hr>
        <p>
            Edição do cadastro selecionado.
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

    <form action="{{ route('controleAcesso.editarCadastroStore', $dados[0]->cpf) }}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="form-row">
            <input type="hidden" name="tipoCadastro" id="tipoCadastro" value="">

            <div class="form-group col-sm-3" id="divRg">
                <label for="rg">RG</label>
                <input type="text" name="rg" value="{{ $dados[0]->rg_numero }}" class="form-control" id="rg" placeholder="Informe o RG">
            </div>

            <div class="form-group col-sm-3" id="divRg">
                <label for="rg_orgao_expedidor">RG Órgão Expedidor</label>
                <input type="text" name="rg_orgao_expedidor" value="{{ $dados[0]->rg_orgao_expedidor }}" class="form-control" id="rg_orgao_expedidor" placeholder="Informe o órgão expedidor do RG">
            </div>

            <div class="form-group col-sm-3" id="divContato">
                <label for="contato">Contato</label>
                <input type="text" name="contato" class="form-control" value="{{ $dados[0]->contato }}" id="contato" placeholder="Informe o contato">
            </div>

            <div class="form-group col-sm-9" id="divNome">
                <label for="nome">Nome</label>
                <input type="text" name="nome" value="{{ $dados[0]->nome }}" class="form-control" id="nome" placeholder="Informe o nome" required>
            </div>

        </div>

        <div class="row mt-1" id="rowFoto">
            <div class="form-group col-sm-3">
                <label for="inputFile">Fotos</label>
                <input type="file" name="inputFile[]" class="form-control" id="inputFile" accept="image/*" multiple>
            </div>

            <div class="form-group col-sm-3">
                <button type="button" class="btn btn-dark mt-4 btn-circle" id="btnAbrirWebcam"><i class="mdi mdi-webcam"></i>Abrir Webcam</button>
            </div>

        </div>

        <button type="button" class="btn btn-secondary mb-3 displayNone" id="btnSavePhoto"><i class="mdi mdi-camera-enhance"></i>Capturar Foto</button>

        <div class="row displayNone" id="divWebcam">
            <div class="col-sm-3">
                <div id="camera">

                </div>
            </div>
            <div class="col-sm-9">
                <div id="imgOut">

                </div>
            </div>
        </div>

        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i>Alterar Cadastro</button>

    </form>

@endsection

@push('css')
<link href="{{ asset('css/controleAcesso/cadastro/editar.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('vendor/userCam/jquery.userCam.min.js') }}"></script>
<script src="{{ asset('js/controleAcesso/cadastro/editarCadastro.js') }}"></script>
@endpush

