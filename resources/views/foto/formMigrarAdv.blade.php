@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fa fa-users"></i> Migracao de Foto dos Adv</h3>
        <hr>
    </div>

    <form action="" method="post">

        {!! csrf_field() !!}

        <button type="button" id="btnMigrar" class="btn btn-primary"><i class="fas fa-search"></i> Migrar!</button>
        <div id="retorno"></div>


    </form>

@endsection
@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="{{ asset('js/foto/migrarAdv.js') }}"></script>
@endpush