@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-search"></i> Buscar Escoltas</h3>
        <hr>
        <p>
            Buscar escoltas na base de dados.
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

    <form action="#">

        <div class="form-row">

            <div class="form-group col-sm-6">
                <label for="destino">Destino</label>
                <input type="text" name="destino" class="form-control" id="destino" placeholder="Informe o destino">
            </div>
            <div class="form-group col-sm-3">
                <label for="unidade_prisional">Unidade Prisional</label>
                <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">
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
                <label for="data_saida">Data de Saída</label>
                <input type="text" name="data_saida" class="form-control" id="data_saida" placeholder="Informe a data de Saída" autocomplete="off">
            </div>

            <div class="form-group col-sm-3">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="Cancelada">
                        Cancelada
                    </option>
                    <option value="Confirmada">
                        Confirmada
                    </option>
                    <option value="Em Andamento">
                        Em Andamento
                    </option>
                    <option value="Finalizada">
                        Finalizada
                    </option>
                    <option value="Solicitada">
                        Solicitada
                    </option>
                </select>

            </div>

            <div class="form-group col-sm-3">
                <label for="tipo_escolta">Tipo de Escolta</label>
                <select id="tipo_escolta" name="tipo_escolta" class="form-control">
                    <option value="" selected>
                        Selecione
                    </option>
                    <option value="interna">
                        Interna
                    </option>
                    <option value="externa">
                        Externa
                    </option>
                    <!-- <option value="gep">
                        FTPEN
                    </option> -->
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="vtr">Utilização de VTR</label>
                <select id="vtr" name="vtr" class="form-control">
                    <option value="" selected>
                        Selecione
                    </option>
                    <option value="S">
                        Sim
                    </option>
                    <option value="N">
                        Não
                    </option>

                </select>
            </div>

        </div>

        <div class="form-row" id="divRowAgentes">

            <div class="form-group col-sm-12">
                <label for="agente">Nome do Agente</label>
                <input type="text" name="agente" class="form-control" id="agente" placeholder="Informe o nome do agente ou matrícula" autocomplete="off">
                <div class="form-group col-sm-12" id="divRetornoAgentes"></div>

            </div>

        </div>

        <h5 class="titleAgente">Agentes <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableAgentes" class="table-responsive">
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


        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar Escoltas</button>

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


        <div class="table-responsive">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">Nº</th>
                <th scope="col">Saída</th>
                <th scope="col">Retorno</th>
                <th scope="col">Destino</th>
                <th scope="col">VTR</th>
                <th scope="col">Status</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados as $dado)
                <tr class="">
                    <td>{{ $dado->id }}</td>
                    <td>{{ $dado->dataSaida."-".$dado->hora_saida }}</td>
                    <td>{{ $dado->dataRetorno."-".$dado->hora_retorno }}</td>
                    <td scope="row">{{ $dado->destino }}</td>
                    <td>
                        @if($dado->vtr_utilizacao == "S")
                            {{ $dado->vtr_placa }}
                            @else
                            Não Utilizado
                        @endif
                    </td>
                    <td>
                        @if($dado->status == "Confirmada")
                            <button type="button" style="cursor: default;" class="btn btn-success btn-fw">Confirmada</button>
                        @elseif($dado->status == "Em Andamento")
                            <button type="button" style="cursor: default;" class="btn btn-primary btn-fw">Em Andamento</button>
                        @elseif($dado->status == "Cancelada")
                            <button type="button" style="cursor: default;" class="btn btn-danger btn-fw">Cancelada</button>
                        @elseif($dado->status == "Parcialmente Cancelada")
                            <button type="button" style="cursor: default;" class="btn btn-danger btn-fw">Parcialmente Cancelada</button>
                        @elseif($dado->status == "Finalizada")
                            <button type="button" style="cursor: default;" class="btn btn-dark btn-fw">Finalizada</button>
                        @elseif($dado->status == "Solicitada")
                            <button type="button" style="cursor: default;" class="btn btn-warning btn-fw">Solicitada</button>
                        @endif
                    </td>
                    <td class="btn-acoes color_01579b">
                        <a href="{{ route('escolta.show',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>
                        <a href="{{ route('escolta.arquivos',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Arquivos"><i class="mdi mdi mdi-cloud-download"></i></a>
                        @if($dado->tipo != "gep")
                        <a href="{{ route('escolta.documento',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Documento"><i class="mdi mdi-file-document"></i></a>
                        <a href="{{ route('escolta.editar',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @endif

@endsection
@push('css')
<link href="{{ asset('css/escolta/buscar/buscar.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/escolta/buscar.js') }}"></script>
<script src="{{ asset('js/outros/buscaServidor.js') }}"></script>
@endpush