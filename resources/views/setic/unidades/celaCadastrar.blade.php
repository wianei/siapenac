@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        @if(isset($dados))
            <h3><i class="mdi mdi-pencil-circle-outline"></i> Alterar Dados da Cela</h3>
            <hr>
            <p>
                Realiza a alteração no cadastro da cela.
            </p>
        @else
            <h3><i class="fas fa-plus"></i> Cadastrar Cela</h3>
            <hr>
            <p>
                Realiza o cadastro de uma cela em uma unidade.
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
        <form action="{{ route('setic.unidadesCelasEditarStore', $idCela) }}" id="formDados" method="post">
            @else
                <form action="{{ route('setic.unidadesCelasCadastrarStore', $idAla) }}" id="formDados" method="post">
                    @endif

                    {!! csrf_field() !!}
                    <div class="form-row">

                        <div class="form-group col-sm-6">
                            <label for="nome">Nome</label>
                            <input type="text" name="nome" value="{{ $dados[0]->nome or old('nome') }}" class="form-control" id="nome" placeholder="Nome" required>
                        </div>
                        <div class="form-group col-sm-2">
                            <label for="capacidade">Capacidade</label>
                            <input type="number" name="capacidade" value="{{ $dados[0]->capacidade or old('capacidade') }}" class="form-control" id="capacidade" placeholder="Capacidade" required>
                        </div>


                        @if(isset($dados))
                            <div class="form-group col-sm-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="" @if( isset( $dados ) && $dados[0]->status == "" ) selected @endif>Selecione</option>
                                    <option value="A" @if( isset( $dados ) && $dados[0]->status == "A" ) selected @endif>Ativo</option>
                                    <option value="I" @if( isset( $dados ) && $dados[0]->status == "I" ) selected @endif>Inativo</option>
                                </select>
                            </div>
                        @endif


                    </div>

                    <div id="flashRetorno"></div>
                    @if(isset($dados))
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Alterações</button>
                    @else
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cadastrar Cela</button>
                    @endif

                </form>

                @endsection

                @push('script')
                <script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
                @endpush