@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-3">
        <h3><i class="mdi mdi-share"></i> Lista de Descautelas</h3>
        <hr>
        <p>
            Listagens das cautelas realizadas.
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

    <form action="">
        <div class="form-row">

            <div class="form-group col-sm-3" id="divTipoArmamento">
                <label for="tipoArmamento">Tipo de Armamento</label>
                <select id="tipoArmamento" name="tipoArmamento" class="form-control">
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
                </select>
            </div>

            <div class="form-group col-sm-3" id="divTipoArmamento">
                <label for="cautelaPara">Descautela Para</label>
                <select id="cautelaPara" name="cautelaPara" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="unidade">
                        Unidade
                    </option>
                    <option value="servidor">
                        Servidor
                    </option>
                    <option value="outros">
                        Outros
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3 displayNone" id="divUnidade">
                <label for="unidade_prisional">Unidade</label>
                <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                    <option value="120">ESPEN</option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}" @if( isset( $interno ) && $unidade->id == $interno->unidade_prisional ) selected @endif>
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3" id="dataInicial">
                <label for="data_inicial">Data Inicial</label>
                <input type="text" name="data_inicial" class="form-control" id="data_inicial" placeholder="Data de Início">
            </div>

            <div class="form-group col-sm-3" id="dataFinal">
                <label for="data_final">Data Final</label>
                <input type="text" name="data_final" class="form-control" id="data_final" placeholder="Data Final">
            </div>

        </div>

        <div class="form-row" id="divRowAgentes">

            <div class="form-group col-sm-12">
                <label for="agente">Servidor Responsável</label>
                <input type="text" name="agente" class="form-control" id="agente" placeholder="Informe o nome do agente ou matrícula" autocomplete="off">
                <div class="form-group col-sm-12" id="divRetornoAgentes"></div>

            </div>

        </div>

        <h5 class="titleAgente displayNone">Servidor <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableAgentes" class="table-responsive-md displayNone">
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

        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar Descautelas</button>

    </form>
    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Nº da Descautela</th>
                <th scope="col">Destino</th>
                <th scope="col">Tempo</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados as $dado)
                <tr class="">
                    <td scope="row" class="fontSize-10px"><i><strong>{{ $loop->iteration  }}</strong></i></td>
                    <td>00{{$dado->id}}</td>
                    <td>{{ strtoupper($dado->tipo_cautela) }}</td>
                    <td>
                        @if($dado->tempo_indeterminado == "S")
                            Indeterminado
                        @else
                            {{ "De: ".$dado->dataInicial." Até: ".$dado->dataFinal}}
                        @endif
                    </td>
                    <td class="btn-acoes color_01579b">

                        <a href="{{ route('armaria.descautelaInformacao',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Informação da Cautela"><i class="mdi mdi-information"></i></a>

                        <a href="{{ route('armaria.documentoCautelaHistorico',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Download do documento de Cautela"><i class="mdi mdi-cloud-download"></i></a>

                        <a href="{{ route('armaria.documentoDescautela',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Download do documento de Descautela"><i class="mdi mdi-cloud-download"></i></a>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>


@endsection

@push('css')
<link href="{{ asset('css/armaria/cautela/listaCautela.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/armaria/listarCautela.js') }}"></script>
<script src="{{ asset('js/vendor/buscaServidor.js') }}"></script>
@endpush

