@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-search"></i> Buscar Usuários Cadastrados no Sistema</h3>
        <hr>
        <p>
            <a href="{{ route('setic.cadastrarUsuarios') }}" data-toggle="tooltip" data-placement="top" title="Editar" class="btn btn-primary text-right"><i class="fas fa-plus"></i>Cadastrar Novo Usuário</a>

        </p>
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg)  !!}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    <form action="#" method="get">

        <div class="form-row">

            <div class="form-group col-sm-6">
                <label for="nome">Nome</label>
                <input type="text" name="nome" class="form-control" id="nome" placeholder="Nome">
            </div>

            <div class="form-group col-sm-3">
                <label for="cpf">CPF</label>
                <input type="text" name="cpf" class="form-control" id="cpf" placeholder="CPF">
            </div>

            <div class="form-group col-sm-3">
                <label for="email">E-mail</label>
                <input type="text" name="email" class="form-control" id="email" placeholder="informe o email">
            </div>

            <div class="form-group col-sm-3">
                <label for="tipo">Tipo</label>
                <select name="tipo" id="tipo" class="form-control">
                    <option value="">Selecione</option>
                    @foreach($permissoes as $permissao)
                        <option value="{{ $permissao['id'] }}">{{ $permissao['nome'] }} - {{ $permissao['tipo'] }}</option>
                    @endforeach
                    
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="visualiza_outras_up">Visualiza qualquer unidade?</label>
                <select name="visualiza_outras_up" id="visualiza_outras_up" class="form-control">
                    <option value="">Selecione</option>
                    <option value="S">Sim</option>
                    <option value="N">Não</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="">Selecione</option>
                    <option value="A">Ativo</option>
                    <option value="I">Inativo</option>
                </select>
            </div>

        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar Usuários</button>

    </form>
    @if($dados != false)

        <div id="links" class="mt-5">
            {{ $dados->links() }}
        </div>

        @if($dados->total() > 0)
            <h6 class="mt-2"><i>Sua busca retornou
                    {{ $dados->total() }} resultados
                </i>
            </h6>

        @else

            <h5 class="mt-5"><i>Sua busca não obteve resultados! </i>
            </h5>

        @endif

        <div class="table-responsive-md mt-3">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">#</th>
                    <th scope="col">Nome</th>
                    <th scope="col">CPF</th>
                    <th scope="col">Email</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Status</th>
                    <th scope="col">Visualiza Qualquer Unidade</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach($dados as $dado)
                    <tr class="">
                        <td scope="row">{{ $loop->iteration }} </td>
                        <td scope="row">{{ $dado->name }}</td>
                        <td>{{ $dado->cpf }}</td>
                        <td>{{ $dado->email }}</td>
                        <td>
                            @php
                                $key = array_search($dado->tipo, array_column($permissoes, 'id'));
                            @endphp
                            {{ $permissoes[$key]['nome'] }}
                        </td>
                        <td>{{ $dado->status }}</td>
                        <td>{{ $dado->visualiza_outras_up }}</td>
                        <td class="btn-acoes color_01579b">

                            <a href="{{ route('setic.editarUsuarios', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Editar Usuário"><i class="mdi mdi-pencil-circle-outline"></i></a>

                            <a href="{{ route('setic.enviarNovaSenha', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Enviar nova senha por email" onclick="return confirm('Deseja realmente gerar uma nova senha e enviar por email?');"><i class="mdi mdi-email"></i></a>

                            {{--<a href="{{ route('setic.recuperacaoContasEnviarSenha', $dado->cpf) }}" data-toggle="tooltip" data-placement="top" title="Enviar email de recuperação"><i class="mdi mdi-email"></i></a>--}}

                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" style="border-bottom: 1px solid #CCCCCC">Último Login: {{ $dado->ultimo_login }} | Órgão: {{ $dado->orgao}} | Cargo: {{ $dado->cargo}} |   Criação: {{ $dado->created_at }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    @endif

@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/setic/listarContas.js') }}"></script>
@endpush