@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-login-variant"></i> Alterar Minha Lotação</h3>
        <hr>
        <p>
            Alterar a lotação para visualizar dados de outras unidades.
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


    <form action="{{ route('unidadde.alterarLotacaoSiapenStore') }}" method="post" enctype="multipart/form-data">

        {!! csrf_field() !!}
        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="unidade">Unidade</label>
                <select id="unidade" name="unidade" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}">
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach

                </select>

            </div>

        </div>
        <div id="flashRetorno"></div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i>
            Alterar Minha Lotação no {{ config('app.name') }}
        </button>

    </form>

@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@if(isset($dados))
    <link href="{{ asset('css/sindicancia/cadastro/editar.css') }}" rel="stylesheet">
@else
    <link href="{{ asset('css/sindicancia/cadastro/cadastro.css') }}" rel="stylesheet">
@endif
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@if(isset($dados))
    <script src="{{ asset('js/sindicancia/editar.js') }}"></script>
@else
    <script src="{{ asset('js/sindicancia/cadastro.js') }}"></script>
    <script src="{{ asset('js/outros/buscaServidor.js') }}"></script>
@endif
{{--<script src="{{ asset('js/outros/buscaInterno.js') }}"></script>--}}
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
@endpush

