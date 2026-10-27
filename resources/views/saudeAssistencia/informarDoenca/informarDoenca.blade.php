@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-plus"></i> Informar Doenças do Interno</h3>
        <hr>
        <p>
            Informar possíveis doenças do interno.
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

    <form action="#">
        <h4>Doenças do Interno</h4>
        <div class="form-row">

            <div class="form-check">
                <label>
                    <input type="checkbox" name="checkFiltro" value="entradaunidadeprisional"> Tornozeleira - Deixou descarregar
                </label>
            </div>
            <div class="form-check">
                <label>
                    <input type="checkbox" name="checkFiltro" value="saidajusicial"> Tornozeleira - Área não permitida
                </label>
            </div>

        </div>
    </form>
@endsection

@push('css')
<link href="{{ asset('css/saudeAssistencia/registrarAtendimento.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
@if(isset($dados))
    <script src="{{ asset('js/saudeAssistencia/editarAtendimento.js') }}"></script>
@else
    <script src="{{ asset('js/saudeAssistencia/registrarAtendimento.js') }}"></script>
    <script src="{{ asset('js/outros/buscaInterno.js') }}"></script>
@endif
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
@endpush
