@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-search"></i> Buscar Unidades</h3>
        <hr>
        <p>
            <a href="{{ route('setic.unidadesCadastrar') }}" data-toggle="tooltip" data-placement="top" title="Editar" class="btn btn-primary text-right"><i class="fas fa-plus"></i>Cadastrar Nova Unidade</a>

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

            {{--<div class="form-group col-sm-3">--}}
                {{--<label for="sigla">Sigla</label>--}}
                {{--<input type="text" name="sigla" class="form-control" id="sigla" placeholder="Sigla">--}}
            {{--</div>--}}

            <div class="form-group col-sm-3">
                <label for="sigla">Sigla</label>
                <select id="sigla" name="sigla" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->sigla }}">
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="idUnidade">ID da Unidade</label>
                <input type="text" name="idUnidade" class="form-control" id="idUnidade" placeholder="ID da unidade">
            </div>


            <div class="form-group col-sm-3">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="">Selecione</option>
                    <option value="1">Ativo</option>
                    <option value="0">Inativo</option>
                </select>
            </div>

        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar Unidades</button>

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
                    <th scope="col">ID</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Sigla</th>
                    <th scope="col">Status</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach($dados as $dado)
                    <tr class="">
                        <td scope="row">{{ $loop->iteration }} </td>
                        <td scope="row">{{ $dado->id}}</td>
                        <td scope="row">{{ $dado->nome}}</td>
                        <td>{{ $dado->sigla }}</td>
                        <td>
                            {{ $dado->status }}
                        </td>
                        <td class="btn-acoes color_01579b">

                            <a href="{{ route('setic.unidadesEditar', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Editar Unidade"><i class="mdi mdi-pencil-circle-outline"></i></a>

                            <a href="{{ route('setic.unidadesPavilhoes', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Ver Pavilhões"><i class="mdi mdi-lan"></i></a>

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