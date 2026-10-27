@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-file-find"></i> Buscar Ocorrências</h3>
        <hr>
        <p>
            Buscar Ocorrêcias e Livros de Ocorrências na base de dados.
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


    <form action="#" id="formBuscar">

        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="status">Buscar por</label>
                <div class="ml-1 mt-2 fontSize-13px">
                    Livro           <input type="radio" name="buscaTipo" class="radioTipo" id="buscaTipoLivro" value="livro">
                    Ocorrência      <input type="radio" name="buscaTipo" class="radioTipo" id="buscaTipoOcorrencia" value="ocorrencia"  checked="">
                </div>
            </div>
        </div>

        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="dt_ocorrencia">Data Inicial da Ocorrência</label>
                <input type="text" name="data_inicial" class="form-control" id="data_inicial" placeholder="Informe a data inicial da ocorrência" autocomplete="off">
            </div>

            <div class="form-group col-sm-3" id="divRowDataFinal">
                <label for="dt_ocorrencia">Data Final da Ocorrência</label>
                <input type="text" name="data_final" class="form-control" id="data_final" placeholder="Informe a data final da ocorrência" autocomplete="off">
            </div>

            <div class="form-group col-sm-3" id="divRowUnidade">
                <label for="unidade">Unidade</label>
                <select id="unidade" name="unidade" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}">
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach

                </select>
            </div>
            
            <div class="form-group col-sm-3 displayNone" id="divHorarioInicioPlantao">
                <label for="horaInicioPlantao">Horário(s) de Início do Plantão</label>
                <select id="horaInicioPlantao" name="horaInicioPlantao" class="form-control">
                    <option value="">
                        Selecione um horário
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divRowNatureza">
                <label for="natureza">Natureza</label>
                <select id="natureza" name="natureza" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($naturezaOcorrencia as $natureza)
                        <option value="{{ $natureza->id }}">
                            {{ $natureza->natureza }}
                        </option>
                    @endforeach
                </select>
            </div>


        </div>

        <div class="form-row" id="formRowDivInternos">
            <div class="form-group col-sm-12">
                <label for="interno">Interno(s) Envolvido(s)</label>
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
                <label for="agente">Servidor(es) Envolvido(s)</label>
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

        <div class="form-row" id="divRowVisitas">
            <div class="form-group col-sm-12">
                <label for="visita">Visita(s) Envolvida(s)</label>
                <input type="text" name="visita" class="form-control" id="visita" placeholder="Informe o nome da visita" autocomplete="off">
                <div class="form-group col-sm-12" id="filter-records-visita"></div>
            </div>
        </div>
        <h5 class="titleVisita displayNone">Visita(s) <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableVisitas" class="table-responsive-md displayNone">
            <table class="table mb-3">
                <thead class="background_81c784 color_FFF">
                <tr class="">
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">CPF</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoVisita">

                </tbody>
            </table>
        </div>

        <div class="form-row" id="divRowAdv">
            <div class="form-group col-sm-12">
                <label for="advogado">Advogado(s) Envolvido(s)</label>
                <input type="text" name="advogado" class="form-control" id="advogado" placeholder="Informe o nome do advogado" autocomplete="off">
                <div class="form-group col-sm-12" id="filter-records-advogado"></div>
            </div>
        </div>
        <h5 class="titleAdvogado displayNone">Advogado(s) <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableAdvogados" class="table-responsive-md displayNone">
            <table class="table mb-3">
                <thead class="background_e65251 color_FFF">
                <tr class="">
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Nº OAB</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoAdvogado">

                </tbody>
            </table>
        </div>

        <div id="flashRetorno"></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar Ocorrências</button>

    </form>

    @if($livro == true)

        <div class="table-responsive-md">
            <table class="table mt-5">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">#</th>
                    <th scope="col">Data</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody>
                    <tr class="">
                        <td scope="row" class="fontSize-10px"><i><strong>1</strong></i></td>
                        <td>{{ $dados['dataLivro'] }}</td>
                        <td>{{ $dados['nomeUnidade']}}</td>
                        <td> Livro de Ocorrência</td>
                        <td class="btn-acoes color_01579b">
                            {{--<a href="#" data-toggle="tooltip" data-placement="top" title="Inserir Ocorrência"><i class="mdi mdi-plus-circle-outline"></i></a>--}}
                            <a href="{{ route('ocorrencia.livroDownload', ['dataLivro' => $dados['dataLivroMysql'], 'unidade' => $dados['idUnidade'], 'horario' => $dados['horario']]) }}" data-toggle="tooltip" data-placement="top" title="Download do Livro"><i class="mdi mdi-cloud-download"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>

            @endif

    @if($dados != false && $livro == false)

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
                <th scope="col">Localização</th>
                <th scope="col">Tipo</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados as $dado)
                <tr class="">
                    <td scope="row" class="fontSize-10px"><i><strong>{{($dados->currentPage() * $dados->perPage()) - $dados->perPage() + $loop->iteration  }}</strong></i></td>
                    <td>{{ $dado->data." - ".$dado->horario }}</td>
                    <td>{{ $dado->unidadePrisional }}</td>
                    <td>{{ $dado->naturezaOcorrencia }}</td>
                    <td class="btn-acoes color_01579b">
                        <a href="{{ route('ocorrencia.visualizarOcorrencia',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Ver Ocorrência"><i class="mdi mdi-information-outline"></i></a>
                        {{--<a href="#" data-toggle="tooltip" data-placement="top" title="Inserir Ocorrência"><i class="mdi mdi-plus-circle-outline"></i></a>--}}
                        {{--<a href="#" data-toggle="tooltip" data-placement="top" title="Download do Livro"><i class="mdi mdi-cloud-download"></i></a>--}}
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
<link href="{{ asset('css/ocorrencia/buscar/buscar.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/ocorrencia/buscar.js') }}"></script>
<script src="{{ asset('js/outros/buscaServidor.js') }}"></script>
<script src="{{ asset('js/outros/buscaVisita.js') }}"></script>
<script src="{{ asset('js/outros/buscaInterno.js') }}"></script>
<script src="{{ asset('js/outros/buscaAdvogado.js') }}"></script>
@endpush