@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
            <h3><i class="mdi mdi-file-document-box"></i> Detalhes da Solicitação</h3>
            <hr>
            <p>
                Informações detalhadas sobre a Solicitação.
            </p>
    </div>

    <div class="row">
        <div class="col-sm-3">
            Nº:<br>
            <h6 class="float-left">{{ $escolta[0]->id }}</h6>
        </div>
        <div class="col-sm-1">
            <i class="mdi mdi-ray-start-arrow fontSize-2em color_3e7099"></i>
        </div>
        <div class="col-sm-4">
            Destino:<br>
            <h6 class="float-left">	{{ $escolta[0]->destino }}</h6>
        </div>
        @if(!is_null($escolta[0]->obs))
            <div class="col-sm-12">
                Observação:<br>
                <h6 class="float-left">	{{ $escolta[0]->obs }}</h6>
            </div>
        @endif
    </div>
    <hr>

    <h5 class="titleInterno mt-3">Arquivos <i class="mdi mdi-arrow-bottom-right"></i></h5>
    @foreach($arquivos  as $arquivo)

        <a href="{{ route('escolta.arquivo',['id' => $arquivo->fk_escolta, 'filename' => $arquivo->nome_arquivo])}}" onclick="" class="btn btn-primary mr-1 mb-1 fileDownload" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo->nome_arquivo }}"><i class="mdi mdi-cloud-download"></i>
        </a>

    @endforeach

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
                        <td>{{ $di['lideranca'] }}</td>
                        <td>{{ $di['sigla'] }}</td>
                        <td class="btn-acoes color_01579b"><a href="{{ route('interno.show',$di['id']) }}" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a></td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
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

@endsection

@push('css')
<link href="{{ asset('css/escolta/show/show.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/escolta/cadastro.js') }}"></script>
@endpush

