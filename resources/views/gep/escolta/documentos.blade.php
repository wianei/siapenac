@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-file-pdf"></i> Documentos da Escolta</h3>
        <hr>
        <p>
            Gera os documentos da escolta
        </p>
    </div>
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div>

    <form action="" method="get">
        <div class="form-row">

            <div class="form-group col-sm-2">
                <label for="dataDocumento">Data das Escoltas</label>
                <input type="text" name="dataDocumento" class="form-control" id="dataDocumento" placeholder="Data" autocomplete="off">
            </div>

            <div class="form-group col-sm-2 mt-2">
                <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-search"></i> Buscar Documentos</button>
            </div>

            <div class="form-group col-sm-12 mt-4">
                <a href="{{ route('gep.docMissoesCapaMissao', str_replace("/","-",$dataBusca)) }}" class="btn btn-secondary"><i class="mdi mdi-file-pdf"></i>PDF da Capa de Missão de {{ $dataBusca }}</a>
            </div>

            <div class="form-group col-sm-12 mt-4">
                <a href="{{ route('gep.docMissoes', str_replace("/","-",$dataBusca)) }}" class="btn btn-secondary"><i class="mdi mdi-file-pdf"></i>PDF dos Ofícios de Missão {{ $dataBusca }}</a>
            </div>

            <div class="form-group col-sm-12 mt-4">
                <a href="{{ route('gep.separacaoInternosUnidadePdf', str_replace("/","-",$dataBusca)) }}" class="btn btn-secondary"><i class="mdi mdi-file-pdf"></i>Internos por Unidade - PDF</a>
            </div>



        </div>

    </form>


@endsection

@push('css')
<link href="{{ asset('css/gep/escolta/pautaDoDia.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/gep/escolta/documentos.js') }}"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>

@endpush