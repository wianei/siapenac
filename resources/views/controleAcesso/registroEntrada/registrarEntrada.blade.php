@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-login"></i> Registro de Entrada na Unidade</h3>
        <hr>
        <p>
            Registrar a entrada de quem entra na unidade.
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

    <div style="width: 250px; margin-bottom: 20px" id="divFoto">
    </div>

    <div id="divNomeEntrada"></div>

    <form action="{{ route('controleAcesso.registrarEntradaStore') }}" id="formRegistroEntrada" method="post" enctype="multipart/form-data">
    {{ csrf_field() }}
        <div class="form-row">
            <input type="hidden" name="tipoCadastro" id="tipoCadastro" value="">

            <div class="form-group col-sm-3">
                <label for="cpf">CPF</label>
                <input type="text" name="cpf" class="form-control" id="cpf" placeholder="Informe o CPF" autocomplete="off" required>
            </div>

            <div class="form-group col-sm-3 displayNone" id="divTipoEntrada">
                <label for="tipoEntrada">Entrada como:</label>
                <select id="tipoEntrada" name="tipoEntrada" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>
            <input type="hidden" name="tipoEntradaHidden" id="tipoEntradaHidden" value="">

            <div class="form-group col-sm-3" id="divRg">
                <label for="rg">RG</label>
                <input type="text" name="rg" class="form-control" id="rg" placeholder="Informe o RG">
            </div>

            <div class="form-group col-sm-3" id="divRg">
                <label for="rg_orgao_expedidor">RG Órgão Expedidor</label>
                <input type="text" name="rg_orgao_expedidor" class="form-control" id="rg_orgao_expedidor" placeholder="Informe o órgão expedidor do RG">
            </div>

            <div class="form-group col-sm-3" id="divContato">
                <label for="contato">Contato</label>
                <input type="text" name="contato" class="form-control" id="contato" placeholder="Informe o contato">
            </div>

            <div class="form-group col-sm-9" id="divNome">
                <label for="nome">Nome</label>
                <input type="text" name="nome" class="form-control" id="nome" placeholder="Informe o nome">
            </div>

            <div class="form-group col-sm-3">
                <label for="dataEntrada">Data da Entrada</label>
                <input type="text" name="dataEntrada" class="form-control" id="dataEntrada" placeholder="Informe a data da entrada" autocomplete="off" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="horarioEntrada">Horário da Entrada</label>
                <input type="text" name="horarioEntrada" class="form-control" id="horarioEntrada" placeholder="Informe o horário de entrada" autocomplete="off" required>
            </div>

        </div>

        <div class="table-responsive-md displayNone" id="divTableInternos">
            <table  class="table mt-5">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">#</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Parentesco</th>
                    <th scope="col">Localização</th>
                </tr>
                </thead>
                <tbody id="internosVisitados" >
                </tbody>
            </table>
        </div>

        <div class="form-row">
            <label for="motivo">Motivo</label>
            <textarea name="motivo" id="motivo" class="form-control"></textarea>
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

        <div id="flashRetorno"></div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Registrar Entrada</button>

    </form>

@endsection

@push('css')
<link href="{{ asset('css/controleAcesso/registroEntrada/registrarEntrada.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('vendor/userCam/jquery.userCam.min.js') }}"></script>
<script src="{{ asset('js/controleAcesso/registroEntrada/registrarEntrada.js') }}"></script>
@endpush

