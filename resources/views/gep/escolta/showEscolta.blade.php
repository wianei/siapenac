@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        @if($escolta[0]->tipo=="gep")
            <h3><i class="mdi mdi-file-document-box"></i> Detalhes da Solicitação</h3>
            <hr>
            <p>
                Informações detalhadas sobre a Solicitação.
            </p>
        @else
            <h3><i class="mdi mdi-file-document-box"></i> Detalhes da Escolta</h3>
            <hr>
            <p>
                Informações detalhadas sobre a Escolta.
            </p>
        @endif
    </div>

    <div class="row">
        <div class="col-sm-3">
            Nº:<br>
            <h6 class="float-left">{{ $escolta[0]->id }}</h6>
        </div>
        <div class="col-sm-4">
            Origem:<br>
            <h6 class="float-left">{{ $escolta[0]->unidade }}</h6>
        </div>
        <div class="col-sm-1">
            <i class="mdi mdi-ray-start-arrow fontSize-2em color_3e7099"></i>
        </div>
        <div class="col-sm-4">
            Destino:<br>
            <h6 class="float-left">	{{ $escolta[0]->destino }}</h6>
        </div>
    </div>
    <hr>

    <div class="row">
        <div class="col-sm-4">
            Solicitante:<br>
            @if($escolta[0]->unidade != "")
                <h6 class="float-left">{{ $escolta[0]->unidade }}</h6>
            @else
                <h6 class="float-left">Poder Judiciário: {{ $escolta[0]->solicitante }}</h6>
            @endif
        </div>
        @if($escolta[0]->tipo == "gep")
            <div class="col-sm-4">
                Responsável:<br>
                <h6 class="float-left">{{ $escolta[0]->responsavel }}</h6>
            </div>
        @endif
        <div class="col-sm-4">
            Status:<br>
            <h6 class="float-left">{{ $escolta[0]->status}}</h6>
        </div>
    </div>
    <hr>
    @if($VTR['comandante'] || $VTR['motorista'])
        <div class="row">
            @if($VTR['comandante'])
                <div class="col-sm-5">
                    Comandante:<br>
                    <h6 class="float-left">{{ $VTR['comandante'] }}</h6>
                </div>
            @endif
            @if($VTR['motorista'])
                <div class="col-sm-5">
                    Motorista:<br>
                    <h6 class="float-left">	{{ $VTR['motorista'] }}</h6>
                </div>
            @endif
            @if($escolta[0]->vtr_placa)
                <div class="col-sm-1">
                    <i class="mdi mdi-car-side fontSize-2em color_3e7099"></i>
                </div>
                <div class="col-sm-1">
                    VTR:<br>
                    <h6 class="float-left">{{ $escolta[0]->vtr_placa }}</h6>
                </div>
            @endif
        </div>
        <hr>
    @endif

    @if(($escolta[0]->data_saida != "" || $escolta[0]->hora_saida != "" || $escolta[0]->data_retorno != "" || $escolta[0]->hora_retorno != "") && $escolta[0]->tipo!="gep")
        <div class="row">
            <div class="col-sm-3">
                Início:<br>
                <h6 class="float-left">{{ $escolta[0]->data_saida." - ".$escolta[0]->hora_saida }}</h6>
            </div>
            <div class="col-sm-1">
                <i class="mdi mdi-clock-start fontSize-2em color_3e7099"></i>
            </div>
            <div class="col-sm-3">
                Fim:<br>
                <h6 class="float-left">
                    {{ $escolta[0]->data_retorno." - ".$escolta[0]->hora_retorno }}
                </h6>
            </div>
        </div>
        <hr>
    @endif

    <div class="row mt-3">
        <div class="col-sm-6">
            <h5>Motivo <i class="mdi mdi-arrow-bottom-right"></i></h5>
            <p>
                {{ $escolta[0]->motivo }}
            </p>
        </div>

        @if($escolta[0]->obs != "")
            <div class="col-sm-6">
                Observação:<br>
                <h6 class="float-left">{{ $escolta[0]->obs}}</h6>
            </div>
        @endif
    </div>
    <hr>

    @foreach($arquivos  as $arquivo)

        <a href="{{ route('escolta.arquivo',['id' => $arquivo->fk_escolta, 'filename' => $arquivo->nome_arquivo])}}" onclick="" class="btn btn-primary mr-1 mb-1 fileDownload" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo->nome_arquivo }}"><i class="mdi mdi-cloud-download"></i>
        </a>

    @endforeach

    @if($escolta[0]->tipo == "gep" && $viaturas != [])
        <h5 class="titleInterno mt-3">Viaturas <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableInternos" class="table-responsive">
            <table class="table mb-3">
                <thead class="background_8f8f8f color_FFF">
                <tr class="">
                    <th scope="col">Placa</th>
                    <th scope="col">Comandante</th>
                    <th scope="col">Motorista</th>
                    <th scope="col">Quantidade de Internos</th>
                    <th scope="col">Data/Hora Saída</th>
                    <th scope="col">Data/Hora Retorno</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoInterno">
                @foreach($viaturas as $viatura)
                    <tr class="">
                        <td scope="row">{{$viatura['placa']}}</td>
                        <td scope="row">{{$viatura['comandante']}}</td>
                        <td scope="row">{{$viatura['motorista']}}</td>
                        <td scope="row">{{$viatura['qtd_internos']}}</td>
                        <td scope="row">{{$viatura['data_hora_saida']}}</td>
                        <td scope="row">{{$viatura['data_hora_retorno']}}</td>
                        <td class="btn-acoes color_01579b"><a href="{{ route('gep.show',$viatura['id']) }}" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a></td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    @endif

    @if($dadosInterno != [])
        <h5 class="titleInterno mt-3">Internos <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableInternos" class="table-responsive">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Facção</th>
                    <th scope="col">Periculosidade</th>
                    <th scope="col">Liderança</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoInterno">

                @foreach($dadosInterno as $di)
                    <tr class="">
                        <td scope="row">{{ $di['id'] }}</td>
                        <td>
                            @if($di['foto'] != null)
                                <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $di['id']. "/".$di['foto'])}}" data-content="{{$di['fototipo']}},{{$di['fotolegenda']}},{{$di['fotodatainserida']}},{{$di['fotodataalterada']}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                            @else
                                <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                            @endif
                        </td>
                        <td>{{ $di['nome'] }}</td>
                        <td>{{ $di['faccao'] }}</td>
                        <td>{{ $di['periculosidade'] }}</td>
                        <td>{{ $di['lideranca'] }}</td>
                        <td>{{ $di['sigla'] }}</td>
                        <td class="btn-acoes color_01579b"><a href="{{ route('interno.show',$di['id']) }}" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a></td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    @endif

    @if($agentes != [] && $escolta[0]->tipo!="gep")
        <h5 class="titleAgente mt-3">Agentes <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableAgentes" class="table-responsive">
            <table class="table mb-3">
                <thead class="background_1c2331 color_FFF">
                <tr class="">
                    <th scope="col">Matrícula</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Lotação</th>
                </tr>
                </thead>
                <tbody id="retornoAgente">
                @foreach($agentes as $agente)
                    <tr class="">
                        <td scope="row">{{ $agente['matricula'] }}</td>
                        <td scope="row">
                            <img class="img-xs rounded-circle imagemSrc cursorPointer" src="{{ asset(config('app.rh').'/storage/app/arquivos/servidores/'.$agente['id'].'/foto/'.$agente['id'].'.jpeg') }}" data-content="Perfil,Foto do Agente no Sistema de RH,Não Informada,Não Informada" title="Clique para ampliar a foto" alt="Foto do Perfil"></td>
                        <td>{{ $agente['nome'] }}</td>
                        <td>{{ $agente['lotacao'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($equipamentos != [] && $escolta[0]->tipo!="gep")
        <h5 class="titleEquipamentos mt-3">Equipamentos <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableEquipamentos" class="table-responsive">
            <table class="table mb-3">
                <thead class="background_287ec3 color_FFF">
                <tr class="">
                    <th scope="col">Nome</th>
                    <th scope="col">Quantidade</th>
                </tr>
                </thead>
                <tbody id="equipamentos">

                @foreach($equipamentos as $eq)
                    <tr class="">
                        <td>{{ $eq['nome'] }}</td>
                        <td>{{ $eq['quantidade'] }}</td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
        <hr>
    @endif

    <p class="text-left mt-5">{{ $textoLog }}</p>

    <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div id="fototipo"></div>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
                </div>
                <div class="modal-body embed-responsive text-center">
                    <img src="" id="imagepreview" style="max-width:90%">
                </div>
                <hr/>
                <div class="text-left">
                    <div id="fotolegenda"></div>
                    <div id="fotodatainserida"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('css')
<link href="{{ asset('css/escolta/show/show.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/escolta/cadastro.js') }}"></script>
@endpush

