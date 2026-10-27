@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-search"></i> Buscar Armamento</h3>
        <hr>
        <p>
            Buscar armamento na base de dados.
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

    <form action="{{ route('armaria.buscar') }}" id="formBuscarArmamento">


        <div class="form-row">

            <div class="form-group col-sm-3" id="divTipoArmamento">
                <label for="tipoArmamento">Tipo de Armamento</label>
                <select id="tipoArmamento" name="tipoArmamento" class="form-control" required>
                    <option value="">
                        Selecione
                    </option>
                    <option value="armaFogo">
                        Arma de Fogo
                    </option>
                    <option value="municao">
                        Munição
                    </option>
                    <option value="carregador">
                        Carregador
                    </option>
                    <option value="algema">
                        Algema
                    </option>
                    <option value="granada">
                        Granada
                    </option>
                    <option value="epi">
                        EPI
                    </option>
                    <option value="colete">
                        Colete
                    </option>
                    <option value="espargidor">
                        Espargidor
                    </option>
                    <option value="dei">
                        Dispositivo Elétrico Incapacitante
                    </option>
                </select>
            </div>

            {{--<div class="form-group col-sm-3">--}}
                {{--<label for="unidade_prisional">Presídio</label>--}}
                {{--<select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">--}}
                    {{--<option value="">--}}
                        {{--Selecione--}}
                    {{--</option>--}}
                    {{--@foreach($unidades as $unidade)--}}
                        {{--<option value="{{ $unidade->id }}" @if( isset( $interno ) && $unidade->id == $interno->unidade_prisional ) selected @endif>--}}
                            {{--{{ $unidade->sigla }}--}}
                        {{--</option>--}}
                    {{--@endforeach--}}
                {{--</select>--}}
            {{--</div>--}}

            <div class="form-group col-sm-3 displayNone" id="divPavilhao">
                <label for="pavilhao">Pavilhão</label>
                <select id="pavilhao" name="pavilhao" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

        </div>

        <div class="form-row" id="divInputs">

            <div class="form-group col-sm-3" id="divTipo">
                <label for="tipo">Tipo</label>
                <select id="tipo" name="tipo" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="pistola">
                        Pistola
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divArmamento">
                <label for="armamento">Armamento</label>
                <select id="armamento" name="armamento" class="form-control">
                </select>
            </div>

            <div class="form-group col-sm-3" id="divTipoAlgema">
                <label for="tipoAlgema">Tipo</label>
                <select id="tipoAlgema" name="tipoAlgema" class="form-control">
                </select>
            </div>

            <div class="form-group col-sm-3" id="divTipoGranada">
                <label for="tipoGranada">Tipo</label>
                <select id="tipoGranada" name="tipoGranada" class="form-control">
                </select>
            </div>

            <div class="form-group col-sm-3" id="divModeloGranada">
                <label for="modeloGranada">Modelo</label>
                <select id="modeloGranada" name="modeloGranada" class="form-control">
                </select>
            </div>

            <div class="form-group col-sm-3" id="divCalibre">
                <label for="calibre">Calibre</label>
                <select id="calibre" name="calibre" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <optgroup label="LETAL">
                        <option value="12">
                            12
                        </option>
                        <option value="38">
                            38
                        </option>
                        <option value="40">
                            40
                        </option>
                        <option value="45">
                            45
                        </option>
                        <option value="9">
                            9mm
                        </option>
                        <option value="556">
                            5.56
                        </option>
                        <option value="762">
                            7.62
                        </option>
                    </optgroup>
                    <optgroup label="MENOS LETAL">
                        <option value="37/38mm">
                            37/38mm
                        </option>
                        <option value="37/40mm">
                            37/40mm
                        </option>
                        <option value="38.1mm">
                            38.1mm
                        </option>
                        <option value="40mm">
                            40mm
                        </option>
                    </optgroup>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divCapacidade">
                <label for="capacidade">Capacidade</label>
                <select id="capacidade" name="capacidade" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divTipoMunicao">
                <label for="tipoMunicao">Tipo de Munição</label>
                <select id="tipoMunicao" name="tipoMunicao" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="real">
                        Real
                    </option>
                    <option value="treina">
                        Treina
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divMunicao">
                <label for="municao">Munição</label>
                <select id="municao" name="municao" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divFabricante">
                <label for="fabricante">Fabricante</label>
                <select id="fabricante" name="fabricante" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="pistola">
                        Taurus
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divModelo">
                <label for="modelo">Modelo</label>
                <input type="text" name="modelo" class="form-control" id="modelo" placeholder="Informe o modelo">
            </div>

            <div class="form-group col-sm-3" id="divValidade">
                <label for="validade">Validade</label>
                <input type="text" name="validade" class="form-control" id="validade" placeholder="Informe a validade">
            </div>

            <div class="form-group col-sm-3" id="divTamanho">
                <label for="tamanho">Tamanho</label>
                <select id="tamanho" name="tamanho" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="PP">
                        PP
                    </option>
                    <option value="P">
                        P
                    </option>
                    <option value="M">
                        M
                    </option>
                    <option value="G">
                        G
                    </option>
                    <option value="GG">
                        GG
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divLote">
                <label for="lote">Lote</label>
                <input type="text" name="lote" class="form-control" id="lote" placeholder="Informe o Lote">
            </div>

            <div class="form-group col-sm-3" id="divVolumePeso">
                <label for="volume_peso">Volume ou Peso</label>
                <input type="text" name="volume_peso" class="form-control" id="volume_peso" placeholder="Ex: 500ml ou 100g">
            </div>

            <div class="form-group col-sm-3" id="divN_serie">
                <label for="n_serie">Nº de Série</label>
                <input type="text" name="n_serie" class="form-control" id="n_serie" placeholder="Informe o número de série">
            </div>

        </div>

        <div class="form-row displayNone" id="dadosGerais">
            <div class="form-group col-sm-3">
                <label for="origem">Origem da Aquisição</label>
                <select id="origem" name="origem" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="doacao">
                        Doação
                    </option>
                    <option value="cautela">
                        Cautela
                    </option>
                    <option value="aquisicao">
                        Aquisição Própria
                    </option>
                </select>
            </div>

        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar Equipamento</button>

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
@if($tipo == "armaFogo")
    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Nº de Série</th>
                <th scope="col">Tipo</th>
                <th scope="col">Fabricante</th>
                <th scope="col">Modelo</th>
                <th scope="col">Calibre</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados as $dado)
                <tr class="">
                    <td scope="row" class="fontSize-10px"><i><strong>{{($dados->currentPage() * $dados->perPage()) - $dados->perPage() + $loop->iteration  }}</strong></i></td>
                    <td>{{ $dado->n_serie }}</td>
                    <td>{{ $dado->equipamento_especificacao }}</td>
                    <td>{{ $dado->fabNome }}</td>
                    <td>{{ $dado->modelo }}</td>
                    <td>.{{ $dado->calibre }}</td>
                    <td class="btn-acoes color_01579b">
                        <a href="{{ route('armaria.editarEquipamento',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>

                        <a href="{{ route('armaria.localizacaoEquipamento', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Localização do Equipamento"><i class="mdi mdi-map-marker-radius"></i></a>

                        <a href="{{ route('armaria.historicoEquipamento', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Histórico do Armamento"><i class="mdi mdi-history"></i></a>

                        <a href="{{ route('armaria.extravioEquipamento', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Informar Extravio" class="color_red"><i class="mdi mdi-cancel"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@endif

@if($tipo == "dei")
    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Nº de Série</th>
                <th scope="col">Tipo</th>
                <th scope="col">Fabricante</th>
                <th scope="col">Modelo</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados as $dado)
                <tr class="">
                    <td scope="row" class="fontSize-10px"><i><strong>{{($dados->currentPage() * $dados->perPage()) - $dados->perPage() + $loop->iteration  }}</strong></i></td>
                    <td>{{ $dado->n_serie }}</td>
                    <td>{{ $dado->equipamento_especificacao }}</td>
                    <td>{{ $dado->fabNome }}</td>
                    <td>{{ $dado->modelo }}</td>
                    <td class="btn-acoes color_01579b">
                        <a href="{{ route('armaria.editarEquipamento',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>

                        <a href="{{ route('armaria.localizacaoEquipamento', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Localização do Equipamento"><i class="mdi mdi-map-marker-radius"></i></a>

                        <a href="{{ route('armaria.historicoEquipamento', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Histórico do Armamento"><i class="mdi mdi-history"></i></a>

                        <a href="{{ route('armaria.extravioEquipamento', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Informar Extravio" class="color_red"><i class="mdi mdi-cancel"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@endif

@if($tipo == "municao")
    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Espécie</th>
                <th scope="col">Tipo</th>
                <th scope="col">Fabricante</th>
                <th scope="col">Modelo</th>
                <th scope="col">Calibre</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados as $dado)
                <tr class="">
                    <td scope="row" class="fontSize-10px"><i><strong>{{($dados->currentPage() * $dados->perPage()) - $dados->perPage() + $loop->iteration  }}</strong></i></td>
                    <td>{{ strtoupper($dado->equipamento_especie) }}</td>
                    <td>{{ strtoupper($dado->equipamento_especificacao) }}</td>
                    <td>{{ $dado->fabNome }}</td>
                    <td>{{ $dado->modelo }}</td>
                    <td>.{{ $dado->calibre }}</td>
                    <td class="btn-acoes color_01579b">
                        <a href="{{ route('armaria.editarEquipamento',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>
                        <a href="{{ route('equipamento.quantidade', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Informar Quantidade"><i class="mdi mdi-cart-plus"></i></a>
                        <a href="{{ route('armaria.extravioEquipamento', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Informar Extravio" class="color_red"><i class="mdi mdi-cancel"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@endif

@if($tipo == "carregador")
    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Espécie</th>
                <th scope="col">Tipo</th>
                <th scope="col">Armamento</th>
                <th scope="col">Calibre</th>
                <th scope="col">Modelo</th>
                <th scope="col">Capacidade</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados as $dado)
                <tr class="">
                    <td scope="row" class="fontSize-10px"><i><strong>{{($dados->currentPage() * $dados->perPage()) - $dados->perPage() + $loop->iteration  }}</strong></i></td>
                    <td>{{ strtoupper($dado->equipamento_tipo) }}</td>
                    <td>{{ strtoupper($dado->equipamento_especie) }}</td>
                    <td>{{ strtoupper($dado->armamento) }}</td>
                    <td>.{{ $dado->calibre }}</td>
                    <td>{{ $dado->modelo }}</td>
                    <td>{{ $dado->volume_peso }}</td>
                    <td class="btn-acoes color_01579b">
                        <a href="{{ route('armaria.editarEquipamento',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>
                        <a href="{{ route('equipamento.quantidade', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Informar Quantidade"><i class="mdi mdi-cart-plus"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@endif

@if($tipo == "algema")
    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Nº de Série</th>
                <th scope="col">Espécie</th>
                <th scope="col">Tipo</th>
                <th scope="col">Fabricante</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados as $dado)
                <tr class="">
                    <td scope="row" class="fontSize-10px"><i><strong>{{($dados->currentPage() * $dados->perPage()) - $dados->perPage() + $loop->iteration  }}</strong></i></td>
                    <td>{{ strtoupper($dado->n_serie) }}</td>
                    <td>{{ strtoupper($dado->equipamento_tipo) }}</td>
                    <td>{{ strtoupper($dado->equipamento_especie) }}</td>
                    <td>{{ $dado->fabNome }}</td>
                    <td class="btn-acoes color_01579b">
                        <a href="{{ route('armaria.editarEquipamento',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>
                        <a href="{{ route('equipamento.quantidade', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Informar Quantidade"><i class="mdi mdi-cart-plus"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@endif

@if($tipo == "granada")
    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Equipamento</th>
                <th scope="col">Espécie</th>
                <th scope="col">Especificação</th>
                <th scope="col">Fabricante</th>
                <th scope="col">Modelo</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados as $dado)
                <tr class="">
                    <td scope="row" class="fontSize-10px"><i><strong>{{($dados->currentPage() * $dados->perPage()) - $dados->perPage() + $loop->iteration  }}</strong></i></td>
                    <td>{{ strtoupper($dado->equipamento_tipo) }}</td>
                    <td>{{ strtoupper($dado->equipamento_especie) }}</td>
                    <td>{{ strtoupper($dado->equipamento_especificacao) }}</td>
                    <td>{{ $dado->fabNome }}</td>
                    <td>{{ $dado->modelo }}</td>
                    <td class="btn-acoes color_01579b">
                        <a href="{{ route('armaria.editarEquipamento',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>
                        <a href="{{ route('equipamento.quantidade', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Informar Quantidade"><i class="mdi mdi-cart-plus"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@if($tipo == "epi")
    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Nº de Série</th>
                <th scope="col">Equipamento</th>
                <th scope="col">Fabricante</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados as $dado)
                <tr class="">
                    <td scope="row" class="fontSize-10px"><i><strong>{{($dados->currentPage() * $dados->perPage()) - $dados->perPage() + $loop->iteration  }}</strong></i></td>
                    <td>{{ strtoupper($dado->n_serie) }}</td>
                    <td>{{ strtoupper($dado->equipamento_especie) }}</td>
                    <td>{{ $dado->fabNome }}</td>
                    <td class="btn-acoes color_01579b">
                        @if($dado->equipamento_especie != "capacete" && $dado->equipamento_especie != "escudo" )
                            <a href="{{ route('equipamento.quantidade', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Informar Quantidade"><i class="mdi mdi-cart-plus"></i></a>
                        @endif

                        @if($dado->equipamento_especie == "capacete" || $dado->equipamento_especie == "escudo" )
                        <a href="{{ route('armaria.editarEquipamento',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>
                        @endif

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@if($tipo == "colete")
    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Nº de Série</th>
                <th scope="col">Equipamento</th>
                <th scope="col">Fabricante</th>
                <th scope="col">Tamanho</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados as $dado)
                <tr class="">
                    <td scope="row" class="fontSize-10px"><i><strong>{{($dados->currentPage() * $dados->perPage()) - $dados->perPage() + $loop->iteration  }}</strong></i></td>
                    <td>{{ strtoupper($dado->n_serie) }}</td>
                    <td>{{ strtoupper($dado->equipamento_tipo) }}</td>
                    <td>{{ $dado->fabNome }}</td>
                    <td>{{ $dado->tamanho }}</td>
                    <td class="btn-acoes color_01579b">
                        <a href="{{ route('armaria.editarEquipamento',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>

                        <a href="{{ route('armaria.localizacaoEquipamento', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Localização Equipamento"><i class="mdi mdi-map-marker-radius"></i></a>

                        <a href="{{ route('armaria.extravioEquipamento', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Informar Extravio" class="color_red"><i class="mdi mdi-cancel"></i></a>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@if($tipo == "espargidor")
    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Tipo</th>
                <th scope="col">Modelo</th>
                <th scope="col">Fabricante</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados as $dado)
                <tr class="">
                    <td scope="row" class="fontSize-10px"><i><strong>{{($dados->currentPage() * $dados->perPage()) - $dados->perPage() + $loop->iteration  }}</strong></i></td>
                    <td>{{ strtoupper($dado->equipamento_tipo) }}</td>
                    <td>{{ strtoupper($dado->modelo) }}</td>
                    <td>{{ $dado->fabNome }}</td>
                    <td class="btn-acoes color_01579b">
                        <a href="{{ route('armaria.editarEquipamento',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>
                        <a href="{{ route('equipamento.quantidade', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Informar Quantidade"><i class="mdi mdi-cart-plus"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@endif
@endsection
@push('css')
<link href="{{ asset('css/armaria/buscar/buscar.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/armaria/buscarArmamento.js') }}"></script>
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
@endpush