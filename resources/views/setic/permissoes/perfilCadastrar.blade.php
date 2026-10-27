@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-security"></i> Permissões</h3>
        <hr>
        <p>
            Permissões do SIAPEN.
        </p>
    </div>


    <form action="{{ route('setic.permissoesCadastrarPerfilStore') }}">
        {{ csrf_field() }}
        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="nome">Nome do Perfil de Permissão</label>
                <input type="text" name="nome" class="form-control" id="nome" placeholder="Informe o nome do perfil de permissão" required="">
            </div>


            <div class="form-group col-sm-3">
                <label for="tipo">Tipo de Usuário do Perfil</label>
                <select id="tipo" name="tipo" class="form-control" required="">
                    <option value="">
                        Selecione
                    </option>
                    <option value="I">
                        Usuários Internos (servidores)
                    </option>
                    <option value="E">
                        Usuários Externos
                    </option>
                </select>
            </div>

        </div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Salvar Perfil</button>


    </form>

@endsection
@push('css')
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
@endpush