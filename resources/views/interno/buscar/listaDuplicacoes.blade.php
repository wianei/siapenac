@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-search"></i> Cadastrar Interno</h3>
        <hr>
        <p>
            Lista de possíveis duplicidades
        </p>
    </div>
    <div class="flash-message">

        <p class="alert alert-warning">
            Já exite(m) interno(s) com os dados infromados, caso tenha certeza de que não se trata de uma duplicação, prossiga clicando no botão abaixo da listagem
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        </p>

    </div>

    @if($internosSemelhantes != false)

        <div class="table-responsive">
        <table class="table mt-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">Prontuário</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">Mãe</th>
                <th scope="col">Localização</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            {{-- modal para abrir a foto do interno --}}
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
            {{-- !! modal para abrir a foto do interno --}}
            @foreach($internosSemelhantes as $key => $dado)
                <tr @if($dado->status == "I") class="background_ffd7d7" @endif>
                    {{-- calculo da ordem do registro --}}
                    <td scope="row">{{$dado->id }}</td>
                    <td>
                        @if(file_exists(storage_path('app/arquivos/internos/fotos/' . $dado->id . "/" . FotoService::getFotoPrincipalByIdTipo($dado->id,'interno'))))
                            <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $dado->id . "/". FotoService::getFotoPrincipalByIdTipo($dado->id,'interno'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                            @else
                            <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                        @endif
                    </td>
                    <td>{{ strtoupper($dado->nome) }}</td>
                    <td>{{ strtoupper($dado->mae) }}</td>
                    <td style="font-size: 10px !important;">
                        {{$lotacao[$key]['sigla']}}
                        @if(PermissaoService::getTipoPermissao() == "I")
                           > {{$lotacao[$key]['pavilhao']}} > {{$lotacao[$key]['ala'] }} > {{$lotacao[$key]['cela']}}
                        @endif
                    </td>
                    <td class="btn-acoes color_01579b">

                        <a href="{{ route('interno.show',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>

                        @if($dado->status == "I")
                            @if(PermissaoService::getVerifyPermissionView("InternoController","reativar"))
                                <a href="{{ route('interno.reativar',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Reativar interno"><i class="mdi mdi-play-circle-outline"></i></a>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>


    <form id="form_cadastro" action="{{ route('interno.cadastro.store') }}" method="post" enctype="multipart/form-data">
        {!! csrf_field() !!}

        <button id="cadastrarMesmoAssim" type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar interno mesmo assim</button>

    </form>

    @endif
@endsection
@push('css')
<link href="{{ asset('css/interno/buscar/buscar.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/interno/buscar.js') }}"></script>
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
@endpush