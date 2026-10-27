@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-search"></i> Buscar Sindicâncias</h3>
        <hr>
        <p>
            Buscar sindicâncias na base de dados.
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

    <form action="{{ route('sindicancia.buscar') }}" method="get">

        <div class="cf"></div>

        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="unidade">Unidade</label>
                <select id="unidade" name="unidade" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}" @if( isset( $interno ) && $unidade->id == $interno->unidade_prisional ) selected @endif>
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach

                </select>

            </div>


            <div class="form-group col-sm-3">
                <label for="data_inicial">Data Inicial</label>
                <input type="text" name="data_inicial" class="form-control" id="data_inicial" placeholder="Informe a Data de Início" autocomplete="off">
            </div>

            <div class="form-group col-sm-3">
                <label for="data_final">Data Final</label>
                <input type="text" name="data_final" class="form-control" id="data_final" placeholder="Informe a Data Final" autocomplete="off">
            </div>

            <div class="form-group col-sm-3">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="Aberta">
                        Aberta
                    </option>

                    <option value="Protocolada">
                        Protocolada
                    </option>

                    <option value="Homologada">
                        Homologada
                    </option>

                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="gravidade_falta">Gravidade da Falta</label>
                <select id="gravidade_falta" name="gravidade_falta" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="Leve">
                        Leve
                    </option>

                    <option value="Média">
                        Média
                    </option>

                    <option value="Grave">
                        Grave
                    </option>

                </select>
            </div>


        </div>

        <div class="form-row">
            <div class="form-group col-sm-12">
                <label for="interno">Nome do interno</label>
                <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome do interno" autocomplete="off">
                <div class="form-group col-sm-12" id="filter-records"></div>

            </div>
        </div>

        <h5 class="titleInterno">Internos <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableInternos" class="table-responsive-md">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Facção</th>
                    <th scope="col">Liderança</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoInterno">

                </tbody>
            </table>
        </div>



        <div class="form-row" id="divRowAgentes">

            <div class="form-group col-sm-12">
                <label for="agente">Nome do Agente</label>
                <input type="text" name="agente" class="form-control" id="agente" placeholder="Informe o nome do agente ou matrícula" autocomplete="off">
                <div class="form-group col-sm-12" id="divRetornoAgentes"></div>

            </div>

        </div>

        <h5 class="titleAgente">Agentes <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableAgentes" class="table-responsive-md">
            <table class="table mb-3">
                <thead class="background_1c2331 color_FFF">
                <tr class="">
                    <th scope="col">Matrícula</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoAgente">

                </tbody>
            </table>
        </div>


        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar Sindicâncias</button>

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

        <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Data</th>
                <th scope="col">Local</th>
                <th scope="col">Título</th>
                <th scope="col">Gravidade</th>
                <th scope="col">Status</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados  as $dado)
                <tr class="">
                    <td scope="row" class="fontSize-10px"><i><strong>{{($dados->currentPage() * $dados->perPage()) - $dados->perPage() + $loop->iteration  }}</strong></i></td>
                    <td>{{ $dado->data }}</td>
                    <td scope="row">{{ $dado->unidadePrisional }}</td>
                    <td>{{ $dado->titulo }}</td>
                    <td class="color_red">{{ $dado->gravidade_falta }}</td>
                    <td>{{ $dado->status }}</td>
                    <td class="btn-acoes color_01579b">

                        <a href="{{ route('sindicancia.visualizar', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>

                        <a href="{{ route('sindicancia.editar', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>

                        {{--<a href="{{ route('sindicancia.pdf', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-file-pdf"></i></a>--}}

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
    @endif
@endsection
@push('css')
<link href="{{ asset('css/sindicancia/buscar/buscar.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/sindicancia/buscar.js') }}"></script>
<script src="{{ asset('js/outros/buscaInterno.js') }}"></script>
<script src="{{ asset('js/outros/buscaServidor.js') }}"></script>
@endpush