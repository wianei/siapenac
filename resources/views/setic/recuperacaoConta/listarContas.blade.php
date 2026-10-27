@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-search"></i> Buscar Solicitações de Recuperação de conta</h3>
        <hr>
        <p>
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

    <form action="#" method="get">

        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="cpf">CPF</label>
                <input type="text" name="cpf" class="form-control" id="cpf" placeholder="CPF">
            </div>

            <div class="form-group col-sm-3">
                <label for="dataInicial">Data Inicial</label>
                <input type="text" name="dataInicial" class="form-control" id="dataInicial" placeholder="informe a data inicial" autocomplete="off">
            </div>

            <div class="form-group col-sm-3">
                <label for="dataFinal">Data Final</label>
                <input type="text" name="dataFinal" class="form-control" id="dataFinal" placeholder="informe a data final" autocomplete="off">
            </div>

            <div class="form-group col-sm-3">
                <label for="email">E-mail</label>
                <input type="text" name="email" class="form-control" id="email" placeholder="informe o email">
            </div>

            <div class="form-group col-sm-3">
                <label for="recuperado">Recuperado</label>
                <select name="recuperado" id="recuperado" class="form-control">
                    <option value="">Selecione</option>
                    <option value="S">Sim</option>
                    <option value="N">Não</option>
                </select>
            </div>


        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar Solicitações</button>

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
                    {{--<th scope="col">#</th>--}}
                    <th scope="col">Data</th>
                    <th scope="col">Nome</th>
                    <th scope="col">CPF</th>
                    <th scope="col">Email</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach($dados as $dado)
                    <tr class="">
                        <td scope="row">{{ $dado->created_at}}</td>
                        <td scope="row">{{ $dado->name}}</td>
                        <td>{{ $dado->cpf }}</td>
                        <td>{{ $dado->email }}</td>
                        <td class="btn-acoes color_01579b">

                            <a href="{{ route('setic.recuperacaoContasArquivos', $dado->idRecuperacao) }}" data-toggle="tooltip" data-placement="top" title="Ver Arquivos"><i class="mdi mdi-file-document"></i></a>

                            <a href="{{ route('setic.recuperacaoContasEnviarSenha', $dado->cpf) }}" data-toggle="tooltip" data-placement="top" title="Enviar email de recuperação"><i class="mdi mdi-email"></i></a>

                       </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="border-bottom: 1px solid #CCCCCC">Recuperado: {{ $dado->recuperado }} | Órgão: {{ $dado->orgao}}
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