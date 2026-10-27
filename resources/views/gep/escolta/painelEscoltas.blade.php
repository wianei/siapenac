@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-list-ol mr-1"></i> Painel de Escoltas</h3>
        <hr>
        <p>
            Painel de Escoltas.
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
                <label for="data_saida">Data de Saída da Escolta, a partir de:</label>
                <input type="text" name="data_saida" class="form-control" id="data_saida" placeholder="Informe a data de Saída" autocomplete="off">
            </div>

            <div class="form-group col-3">
                <label for="data_retorno">Data de Retorno da Escolta, até:</label>
                <input type="text" name="data_retorno" class="form-control" id="data_retorno" placeholder="Informe a data de Retorno" autocomplete="off">
            </div>

            <div class="form-group col-6">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="Pendente">
                        Pendente
                    </option>
                    <option value="Finalizada">
                        Finalizada
                    </option>
                </select>

            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-6">
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

            <div class="form-group col-3">
                <label for="seminternos">Escolta Sem Internos</label>
                <select id="seminternos" name="seminternos" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="S">
                        Sim
                    </option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar Escoltas</button>

    </form>

    <hr>


    {{--@if($geps != false)--}}

        <div id="links" class="mt-5">
            {{ $geps->links() }}
        </div>

        @if($geps->total() > 0)
            <h6 class="mt-2"><i>Sua busca retornou
                    {{ $geps->total() }} resultados
                </i>
            </h6>

            <div class="table-responsive">
                <table class="table mt-5">
                    <thead class="background_30516f color_FFF">
                    <tr class="">
                        <th scope="col">Nº</th>
                        <th scope="col">Data da Saída</th>
                        <th scope="col">Data do Retorno</th>
                        <th scope="col">Destino</th>
                        <th scope="col">Solicitante</th>
                        <th scope="col">Responsável</th>
                        <th scope="col" class="text-center">Quant. Internos</th>
                        <th scope="col">Status</th>
                        <th scope="col">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($geps as $key=>$gep)
                    <tr id="tr{{$gep['id']}}">
                        <td style="font-size: 12px !important;">{{$gep['id']}}</td>
                        <td style="font-size: 12px !important;">{{$gep['data_saida']}}</td>
                        <td style="font-size: 11px !important;">{{$gep['data_retorno']}}</td>
                        <td scope="row" style="white-space: pre-wrap !important; word-wrap:break-word !important; font-size: 12px">{{$gep['destino']}}</td>
                        <td style="font-size: 11px !important;">
                            @if(is_null($gep['solicitante']))
                                {{$gep['unidade']}}
                               @else Judiciário
                            @endif
                        </td>
                        <td style="font-size: 11px !important;">{{$gep['responsavel']}}</td>
                        <td class="text-center" style="font-size: 11px !important;">{{$gep['qtd_internos']}}</td>
                        <td>
                            @if($gep['status'] == "Pendente")
                                <button id="bt{{$gep['id']}}" type="button" style="cursor: default;" class="btn btn-warning btn-fw">{{$gep['status']}}</button>
                           @elseif($gep['status'] == "Finalizada")
                                <button id="bt{{$gep['id']}}" type="button" style="cursor: default;" class="btn btn-dark btn-fw">{{$gep['status']}}</button>
                            @endif
                        </td>

                        <td id="acoes{{$gep['id']}}" class="btn-acoes color_01579b">

                            <a href="{{route('gep.show',$gep['id'])}}" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>

                            <a href="{{route('gep.documentoEscolta',$gep['id'])}}" data-toggle="tooltip" data-placement="top" title="Documento Escolta"><i class="mdi mdi mdi-file-document"></i></a>

                           @if($gep['status'] == "Pendente")

                                <a href="#/" data-content="{{$gep['id']}}" data-toggle="tooltip" data-placement="top" title="Finalizar" class="finalizar"><i class="mdi mdi-check-circle-outline"></i></a>

                                <a href="{{ route('gep.editar',$gep['id']) }}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>

                                <a href="#/" data-content="{{$gep['id']}}" data-toggle="tooltip" data-placement="top" title="Apagar" class="cancelar"><i class="mdi mdi-close-circle-outline"></i></a>

                            @endif
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
<script src="{{ asset('js/gep/escolta/painelEscoltas.js') }}"></script>
@endpush