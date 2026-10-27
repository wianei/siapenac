@extends('layouts.app')

@section('content')

    <div style="width: 60%; margin: 0 auto !important; margin-top: 80px">

        <div class="flash-message">
            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has('alert-' . $msg))

                    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                @endif
            @endforeach
        </div> <!-- end .flash-message -->

        <div class="form-row">
            <div class="form-group col-sm-12">
                <h3>Recuperação de Conta da Versão Anterior</h3>
                <p style="background: #e2e7ea; padding: 5px">
                    Para recuperar o acesso ao {{ config('app.name') }} é necessário preencher os dados abaixo e, após validação, os dados para acesso chegarão no e-mail informado. Caso deseje alterar o e-mail, ou ocorra algum problema, entre em contato com a {{ config('app.ti') }} através do e-mail siapenacre@gmail.com
                </p>
            </div>
        </div>

        <form style="width: 100%" action="{{ route('usuario.recuperarContaStore') }}" id="formRecuperarConta" method="post" enctype="multipart/form-data">

            {!! csrf_field() !!}

            <div class="form-row">
                <div class="form-group col-sm-6">
                    <label for="cpf">Informe o CPF cadastrado</label>
                    <input type="text" class="form-control" name="cpf" id="cpf" placeholder="Informe o CPF">
                </div>
                <div class="form-group col-sm-6">
                    <label for="email">E-Mail Cadastrado na Versão Anterior</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Informe o seu e-mail cadastrado na versão anterior">
                </div>

                <div class="form-group col-sm-6">
                    <label for="orgao">Órgão de Vinculação</label>
                    <input type="text" class="form-control" name="orgao" id="orgao" placeholder="informe o órgão de vinculação do servidor">
                </div>

                <div class="form-group col-sm-6">
                    <label for="inputFile">Documento comprobatório de vínculo atual no formato PDF (obrigatório apenas para quem não é agente penitenciário)</label>
                    <input type="file" name="inputFile[]" class="form-control" id="inputFile"  accept="application/pdf" multiple required="required">

                </div>


                <div class="form-group col-sm-12 mt-3">
                    <div id="flashRetorno"></div>
                    <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Recuperar Conta
                    </button>
                </div>

            </div>

        </form>
    </div>

@endsection
@push('css')
<link href="{{ asset('css/login/login.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/usuario/recuperarConta.js') }}"></script>
@endpush
