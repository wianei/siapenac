@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        @if(isset($dados))
        <h3><i class="mdi mdi-pencil-circle-outline"></i> Alterar Cadastro do Usuário</h3>
        <hr>
        <p>
            Realiza a alteração no cadastro dos usuários no sistema.
        </p>
            @else
        <h3><i class="fas fa-plus"></i> Cadastrar Usuários</h3>
        <hr>
        <p>
            Realiza o cadastro dos usuários no sistema.
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
        <form action="{{ route('setic.editarUsuariosStore', $id) }}" method="post" id="formCadastro">
            @else
            <form action="{{ route('setic.cadastrarUsuariosStore') }}" method="post" id="formCadastro">
        @endif

        {!! csrf_field() !!}
        <div class="form-row">

            <div class="form-group col-sm-6">
                <label for="nome">Nome</label>
                <input type="text" name="nome" value="{{ $dados[0]->name or old('nome') }}" class="form-control" id="nome" placeholder="Nome" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="cpf">CPF</label>
                <input type="text" name="cpf" value="{{ $dados[0]->cpf or old('cpf') }}" class="form-control" id="cpf" placeholder="CPF" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="email">E-mail</label>
                <input type="email" name="email" value="{{ $dados[0]->email or old('email') }}" class="form-control" id="email" placeholder="informe o email" required>
            </div>

            <div class="form-group col-sm-6">
                <label for="orgao">Órgão</label>
                <input type="text" name="orgao" value="{{ $dados[0]->orgao or old('orgao') }}" class="form-control" id="orgao" placeholder="Órgão" required>
            </div>

            <div class="form-group col-sm-6">
                <label for="cargo">Cargo</label>
                <input type="text" name="cargo" value="{{ $dados[0]->cargo or old('orgao') }}" class="form-control" id="cargo" placeholder="Cargo" required>
            </div>


            <div class="form-group col-sm-3">
                <label for="tipo">Tipo de Permissão</label>
                <select name="tipo" id="tipo" class="form-control" required>
                    <option value=""  @if( isset( $dados ) && $dados[0]->tipo == "" ) selected @endif>Selecione</option>
                    @foreach($perfilPermissao as $permissao)
                        <option value="{{$permissao->id}}" data-content="{{ $permissao->tipo }}" @if( isset( $dados ) && $dados[0]->tipo == $permissao->id ) selected @endif>{{$permissao->nome}} - {{$permissao->tipo}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="A" @if( isset( $dados ) && $dados[0]->status == "A" ) selected @endif>Ativo</option>
                    <option value="I" @if( isset( $dados ) && $dados[0]->status == "I" ) selected @endif>Inativo</option>
                </select>
            </div>
            
            <div class="form-group col-sm-3 displayNone" id="divVisualizaOutrasUnidade">
                <label for="visualiza_outras_up">Visualiza outras unidades</label>
                <select name="visualiza_outras_up" id="visualiza_outras_up" class="form-control" required>
                    <option value="N" @if( isset( $dados ) && $dados[0]->visualiza_outras_up == "N" ) selected @endif>Não</option>
                    <option value="S" @if( isset( $dados ) && $dados[0]->visualiza_outras_up == "S" ) selected @endif>Sim - TODAS</option>
                    <option value="S" @if( isset( $dados ) && $dados[0]->visualiza_outras_up == "S" && $dados[0]->visualizacao_up_liberada != null) selected @endif>Sim - ALGUMAS</option>
                </select>
            </div>

        </div>
        <h5 id="titleUnidadesVisualizacao" class="displayNone">Unidades para Visualização</h5>
        <div class="form-row pr-3 pl-3 displayNone" id="divUnidadesVisualizacao">

           @foreach($unidadesTroca as $unidade)
            <div class="form-group form-check col-sm-6 divCheck">
                <input class="form-check-input" type="checkbox" value="{{ $unidade->id }}" name="unidadesTroca[]" id="unidadesTroca{{ $unidade->id }}" @if(!is_null($unidadesLiberadas) && in_array($unidade->id, $unidadesLiberadas)) checked @endif @if($unidade->id == $lotacaoServidor) disabled="disabled" @endif>
                <label class="" for="unidadesTroca{{ $unidade->id }}">
                  {{ $unidade->nome }} - {{ $unidade->sigla }}
                </label>
            </div>
            @endforeach
        </div>
        <div id="flashRetorno"></div>
        @if(isset($dados))
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Alterações</button>
            @else
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cadastrar Usuário</button>
        @endif

    </form>

@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/setic/cadastroUsuario.js') }}"></script>
<script src="{{ asset('js/setic/listarContas.js') }}"></script>
@endpush