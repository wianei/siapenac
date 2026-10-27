@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fa fa-plus"></i> Inserir Dados do Judiciário</h3>
        <hr>
        <p>
            Inserir dados do Judiciário.
        </p>
    </div>
@if(isset($dados))
    <form action="{{ route('gep.updateDadosJudiciario') }}" method="post" enctype="multipart/form-data" class="mb-4 mt-5">
@else
    <form action="{{ route('gep.storeDadosJudiciario') }}" method="post" enctype="multipart/form-data" class="mb-4 mt-5">
@endif

        <div class="flash-message">
            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has('alert-' . $msg))

                    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                @endif
            @endforeach
        </div>

        {!! csrf_field() !!}

        @if(isset($dados))
            <input type="hidden" name="id" class="form-control" id="id" value="{{$dados['id']}}">
        @endif

        <div class="form-row">
            <div class="form-group col-sm-6">
                <label for="vara">Vara</label>
                @if(isset($dados))
                    <input type="text" name="vara" class="form-control" id="vara" placeholder="Informe o nome da vara" value="{{$dados['vara']}}">
                @else
                    <input type="text" name="vara" class="form-control" id="vara" placeholder="Informe o nome da vara">
                @endif
            </div>

            <div class="form-group col-sm-6">
                <label for="magistrado">Juiz(a)</label>
                @if(isset($dados))
                    <input type="text" name="magistrado" class="form-control" id="magistrado" placeholder="Informe o nome do magistrado" value="{{$dados['juiz']}}">
                @else
                    <input type="text" name="magistrado" class="form-control" id="magistrado" placeholder="Informe o nome do magistrado">
                @endif

            </div>

            <div class="form-group col-sm-6">
                <label for="qualificacao">Qualificação</label>
                @if(isset($dados))
                    <input type="text" name="qualificacao" class="form-control" id="qualificacao" placeholder="Informe a qualificação do magistrado" value="{{$dados['qualificacao']}}">
                @else
                    <input type="text" name="qualificacao" class="form-control" id="qualificacao" placeholder="Informe a qualificação do magistrado">
                @endif
            </div>

            <div class="form-group col-sm-6">
                <label for="comarca">Comarca</label>
                @if(isset($dados))
                    <input type="text" name="comarca" class="form-control" id="comarca" placeholder="Informe a comarca da vara" value="{{$dados['comarca']}}">
                @else
                    <input type="text" name="comarca" class="form-control" id="comarca" placeholder="Informe a comarca da vara">
                @endif
            </div>

        </div>

        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Salvar Dados</button>

    </form>

@endsection

@push('css')
<link href="{{ asset('css/gep/escolta/escoltaDoDia.css') }}" rel="stylesheet">
@endpush


