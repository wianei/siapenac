@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-file-document-box"></i> Detalhes da Escolta</h3>
        <hr>
        <p>
            Informações detalhadas sobre a Escolta.
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

    <div class="row">
        <div class="col-sm-4">
            Nº:<br>
            <h6 class="float-left">{{ $gep[0]->id }}</h6>
        </div>
        <div class="col-sm-4">
            Responsável:<br>
            <h6 class="float-left">{{ $gep[0]->responsavel }}</h6>
        </div>
        <div class="col-sm-4">
            Status:<br>
            <h6 class="float-left">{{ $gep[0]->status}}</h6>
        </div>
    </div>
    <hr>
    @if($gep[0]->vtr_comandante || $gep[0]->vtr_motorista)
        <div class="row">
            @if($gep[0]->vtr_comandante)
                <div class="col-sm-5">
                    Comandante:<br>
                    <h6 class="float-left">{{ $gep[0]->vtr_comandante }}</h6>
                </div>
            @endif
            @if($gep[0]->vtr_motorista)
                <div class="col-sm-5">
                    Motorista:<br>
                    <h6 class="float-left">	{{ $gep[0]->vtr_motorista }}</h6>
                </div>
            @endif
            @if($gep[0]->vtr_placa)
                <div class="col-sm-1">
                    <i class="mdi mdi-car-side fontSize-2em color_3e7099"></i>
                </div>
                <div class="col-sm-1">
                    Placa:<br>
                    <h6 class="float-left">{{ $gep[0]->vtr_placa }}</h6>
                </div>
            @endif
        </div>
        <hr>
    @endif

    @if($gep[0]->data_saida != "" || $gep[0]->hora_saida != "" || $gep[0]->data_retorno != "" || $gep[0]->hora_retorno != "")
        <div class="row">
            <div class="col-sm-3">
                Início:<br>
                <h6 class="float-left">{{ $gep[0]->data_saida." - ".$gep[0]->hora_saida }}</h6>
            </div>
            <div class="col-sm-1">
                <i class="mdi mdi-clock-start fontSize-2em color_3e7099"></i>
            </div>
            <div class="col-sm-3">
                Fim:<br>
                <h6 class="float-left">
                    {{ $gep[0]->data_retorno." - ".$gep[0]->hora_retorno }}
                </h6>
            </div>
        </div>
        <hr>
    @endif

    @if($internos != [])
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

                @foreach($internos as $di)
                    <tr class="">
                        <td scope="row">{{ $di['fk_interno'] }}</td>
                        <td>
                            @if($di['foto'] != null)
                                <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $di['fk_interno']. "/".$di['foto'])}}" data-content="{{$di['fototipo']}},{{$di['fotolegenda']}},{{$di['fotodatainserida']}},{{$di['fotodataalterada']}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                            @else
                                <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                            @endif

                        </td>
                        <td>{{ $di['nome'] }}</td>
                        <td>{{ $di['faccao'] }}</td>
                        <td>{{ $di['periculosidade'] }}</td>
                        <td>{{ $di['lideranca'] }}</td>
                        <td>{{ $di['lotacao'] }}</td>
                        <td class="btn-acoes color_01579b"><a href="{{ route('interno.show',$di['fk_interno']) }}" data-toggle="tooltip" data-placement="top" title="Detalhes do Interno"><i class="mdi mdi-information-outline"></i></a><a href="{{ route('escolta.show',$di['fk_escolta']) }}" data-toggle="tooltip" data-placement="top" title="Detalhes da Solicitação"><i class="mdi mdi-information-outline"></i></a></td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    @endif

    @if($agentes != [])
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
                        <td>
                            <img style="text-align: center" src="{{ asset(config('app.rh').'/storage/app/arquivos/servidores/'.$agente['id'].'/foto/'.$agente['id'].'.jpeg') }}" class="imagemSrc cursorPointer" data-content="Perfil,Foto do Agente no Sistema de RH,Não Informada,Não Informada" title="Clique para ampliar a foto" alt="Foto do Perfil">
                        </td>
                        <td>{{ $agente['nome'] }}</td>
                        <td>{{ $agente['lotacao'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($equipamentos != [])
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


    <p class="text-left mt-5">{{ $textoLog }}</p>

@endsection

@push('css')
<link href="{{ asset('css/escolta/show/show.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/escolta/cadastro.js') }}"></script>
@endpush

