@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        @if(isset($dados))
        <h3><i class="mdi mdi-pencil-circle-outline"></i> Alterar Dados de Objetos</h3>
        <hr>
        <p>
            Realiza a alteração no cadastro dos objetos relacionados à ocorrência.
        </p>
            @else
        <h3><i class="fas fa-plus"></i> Cadastrar Objetos das Ocorrências</h3>
        <hr>
        <p>
            Realiza o cadastro dos objetos relacionados à ocorrência.
        </p>
        @endif
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    @if(isset($dados))
        <form action="{{ route('setic.updateObjetosOcorrencia', $id) }}" method="post">
            @else
        <form action="{{ route('setic.novoObjetosOcorrenciaStore') }}" method="post">
        @endif

        {!! csrf_field() !!}
        <div class="form-row">

            <div class="form-group col-sm-6">
                <label for="nome">Nome do Objeto</label>
                <input type="text" name="nome" value="{{ $dados->nome or old('nome') }}" class="form-control" id="nome" placeholder="Nome" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="natureza">Natureza</label>
                <select name="natureza" id="natureza" class="form-control" required>
                    
                    <option value=""  @if( isset( $dados ) && $dados->fk_natureza == "" ) selected @endif>Selecione</option>
                    @foreach($naturezas as $natureza)
                    <option value="{{ $natureza->id }}"  @if( isset( $dados ) && $dados->fk_natureza == $natureza->id ) selected @endif>{{ $natureza->natureza }}</option>
                    @endforeach
                    
                </select>
            </div>
        </div>

        @if(isset($dados))
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Alterações</button>
            @else
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cadastrar Objeto</button>
        @endif

    </form>

@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/setic/listarContas.js') }}"></script>
@endpush