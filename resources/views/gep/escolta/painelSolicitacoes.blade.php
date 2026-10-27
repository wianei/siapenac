@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-cogs"></i> Painel de Solicitações</h3>
        <hr>
        <p>
            Painel de Solicitações.
        </p>
    </div>
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div>

    <form action="#">

        <div class="form-row">
            <div class="form-group col-3">
                <label for="solicitante">Solicitante</label>
                <select id="solicitante" name="solicitante" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="poderJudiciario">
                        Poder Judiciário
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}" @if( isset( $interno ) && $unidade->id == $interno->unidade_prisional ) selected @endif>
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-3">
                <label for="responsavel">Responsável</label>
                <select id="responsavel" name="responsavel" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="6405">
                        FTPEN-JOÃO PESSOA
                    </option>
                    <!-- <option value="130">
                        GEP Caicó
                    </option>
                    <option value="140">
                        GEP Caraúbas
                    </option>
                    <option value="150">
                        GEP Mossoró
                    </option>
                    <option value="165">
                        GEP Pau dos Ferros
                    </option> -->
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="Solicitada">
                        Solicitada
                    </option>
                    <option value="Confirmada">
                        Confirmada
                    </option>
                    <option value="Finalizada">
                        Finalizada
                    </option>
                    <option value="Cancelada">
                        Cancelada
                    </option>
                </select>

            </div>


        </div>

        <div class="form-row">

            <div class="form-group col-sm">
                <label for="created_at_inicio">Data da Solicitação da Escolta, de:</label>
                <input type="text" name="created_at_inicio" class="form-control" id="created_at_inicio" placeholder="Informe a data" autocomplete="off">
            </div>

            <div class="form-group col-sm">
                <label for="created_at_fim">Data da Solicitação da Escolta, até:</label>
                <input type="text" name="created_at_fim" class="form-control" id="created_at_fim" placeholder="Informe a data" autocomplete="off">
            </div>

            <div class="form-group col-sm">
                <label for="data_escolta_inicio">Data da Escolta, de:</label>
                <input type="text" name="data_escolta_inicio" class="form-control" id="data_escolta_inicio" placeholder="Informe a data" autocomplete="off">
            </div>

            <div class="form-group col-sm">
                <label for="data_escolta_fim">Data da Escolta, até:</label>
                <input type="text" name="data_escolta_fim" class="form-control" id="data_escolta_fim" placeholder="Informe a data" autocomplete="off">
            </div>

        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar Solicitações</button>

    </form>

    <hr>

    {{--@if($escoltas != false)--}}

        <div id="links" class="mt-5">
            {{ $escoltas->links() }}
        </div>

        @if($escoltas->total() > 0)
            <h6 class="mt-2"><i>Sua busca retornou
                    {{ $escoltas->total() }} resultados
                </i>
            </h6>

            <div class="table-responsive">
                <table class="table mt-5">
                    <thead class="background_30516f color_FFF">
                    <tr class="">
                        <th scope="col">Nº</th>
                        <th scope="col">Data da Solicitação</th>
                        <th scope="col">Data da Escolta</th>
                        <th scope="col">Destino</th>
                        <th scope="col">Solicitante</th>
                        <th scope="col">Responsável</th>
                        <th scope="col" class="text-center">Quantidade de Internos</th>
                        <th scope="col" class="text-center">Status</th>
                        <th scope="col">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($escoltas as $escolta)
                    <tr id="tr{{$escolta['id']}}" class="">
                        <td style="font-size: 12px !important;">{{$escolta['id']}}</td>
                        <td style="font-size: 12px !important; text-align: center">{{$escolta['created_at']}}</td>
                        <td style="font-size: 12px !important; text-align: center">{{$escolta['data_saida']}}</td>
                        <td scope="row" style="white-space: pre-wrap !important; word-wrap:break-word !important; font-size: 12px">{{$escolta['destino']}}</td>
                        <td style="font-size: 12px !important;">{{$escolta['solicitante']}}</td>
                        <td style="font-size: 12px !important;">{{$escolta['responsavel']}}</td>
                        <td style="font-size: 12px !important;" class="text-center">{{$escolta['qtd_internos']}}</td>
                        <td style="font-size: 12px !important;">
                            @if($escolta['status'] == "Solicitada")
                                <button id="bt{{$escolta['id']}}" type="button" style="cursor: default;" class="btn btn-warning btn-fw">{{$escolta['status']}}</button>
                            @elseif($escolta['status'] == "Confirmada")
                                <button id="bt{{$escolta['id']}}" type="button" style="cursor: default;" class="btn btn-success btn-fw">{{$escolta['status']}}</button>
                            @elseif($escolta['status'] == "Finalizada")
                                <button id="bt{{$escolta['id']}}" type="button" style="cursor: default;" class="btn btn-dark btn-fw">{{$escolta['status']}}</button>
                            @elseif($escolta['status'] == "Cancelada" || $escolta['status'] == "Parcialmente Cancelada")
                                <button id="bt{{$escolta['id']}}" type="button" style="cursor: default;" class="btn btn-danger btn-fw">{{$escolta['status']}}</button>
                            @endif
                        </td>
                        <td style="font-size: 12px !important;" id="acoes{{$escolta['id']}}" class="btn-acoes color_01579b">
                            <a href="{{route('gep.showEscolta',$escolta['id'])}}" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>
                            <a href="{{ route('gep.arquivosEscolta',$escolta['id']) }}" data-toggle="tooltip" data-placement="top" title="Arquivos"><i class="mdi mdi mdi-cloud-download"></i></a>
                            @if($escolta['status'] == "Solicitada")
                            <a href="#/" data-content="{{$escolta['id']}}" data-toggle="tooltip" data-placement="top" title="Confirmar" class="confirmar"><i class="mdi mdi-check-circle-outline"></i></a>
                            <a href="#/" data-content="{{$escolta['id']}}" data-toggle="tooltip" data-placement="top" title="Repassar" class="repassar"><i class="mdi mdi-arrow-right-bold-circle-outline"></i></a>
                            <a href="#/" data-content="{{$escolta['id']}}" data-toggle="tooltip" data-placement="top" title="Cancelar" class="cancelar"><i class="mdi mdi-close-circle-outline"></i></a>
                            @endif
                            @if($escolta['status'] == "Confirmada")
                            <a href="#/" data-content="{{$escolta['id']}}" data-toggle="tooltip" data-placement="top" title="Cancelar" class="cancelar"><i class="mdi mdi-close-circle-outline"></i></a>
                            @endif
                        </td>
                    </tr>
                    <tr id="trr{{$escolta['id']}}" class="displayNone">
                        <td colspan="6" class="text-right"><input style="width:75%" class="mr-1" type="text" id="btc{{$escolta['id']}}" name="obs" class="" id="obs" placeholder="Motivo do Cancelamento (preenchimento obrigatório)"><button style="width:25%" type="button" class="cancelarComObs text-center btn btn-danger btn-fw"><i class="fas fa-trash"></i> Confirmar Cancelamento</button></td>
                    </tr>
                    <tr id="trrrepassar{{$escolta['id']}}" class="displayNone">
                        <td colspan="6" class="text-right">
                            <select name="responsavel" id="btcrepassar{{$escolta['id']}}" class="form-control">
                                @if($escolta['responsavel'] != "FTPEN-JOÃO PESSOA")
                                    <option value="6405">
                                        FTPEN-JOÃO PESSOA
                                    </option>
                                @endif
                                <!-- @if($escolta['responsavel'] != "GEP-CAICÓ")
                                    <option value="130">
                                        GEP Caicó
                                    </option>
                                @endif
                                @if($escolta['responsavel'] != "GEP-CARAÚBAS")
                                    <option value="140">
                                        GEP Caraúbas
                                    </option>
                                @endif
                                @if($escolta['responsavel'] != "GEP-MOSSORÓ")
                                    <option value="150">
                                        GEP Mossoró
                                    </option>
                                @endif
                                @if($escolta['responsavel'] != "GEP-PAU DOS FERROS")
                                    <option value="165">
                                        GEP Pau dos Ferros
                                    </option>
                                @endif -->
                            </select>
                        </td>
                        <td colspan="6" class="text-right">
                            <button style="width:25%" type="button" class="repassarBT text-center btn btn-behance btn-fw"><i class="mdi mdi-arrow-right-bold-circle-outline"></i>Repassar</button>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        @else
            @if(strpos($url,"?"))
                <h5 class="mt-5"><i>Sua busca não obteve resultados! </i>
                </h5>
            @endif
        @endif

@endsection

@push('css')
<link href="{{ asset('css/gep/escolta/escoltaDoDia.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/gep/escolta/painelSolicitacoes.js') }}"></script>
@endpush