@extends('template.tema_geral.theme')

@section('content')
    <input type="hidden" name="idAdv" id="idAdv" value="{{ $id }}">
    <h3 style="display: none" class="text-center mostrarNaImpressao">
        @if($fotoPerfilDados['nome_arquivo'] != 'storage/app/semfoto.jpg')
            <img src="{{asset($fotoPerfilDados['nome_arquivo'])}}" style="max-height: 8rem;" data-content="{{$fotoPerfilDados['tipo']}},{{$fotoPerfilDados['legenda']}},{{$fotoPerfilDados['created_at']}}" class="mx-auto d-block imagemSrc cursorPointer text-center figure-img img-fluid" title="Clique para ampliar a foto" alt="{{$fotoPerfilDados['tipo']}}"/>
        @else
            <img src="{{asset($fotoPerfilDados['nome_arquivo'])}}" style="max-height: 8rem;" class="mx-auto d-block text-center figure-img img-fluid"/>
        @endif
        {{$advogado['nome']}} - OAB: {{$advogado['oab_numero']}}/{{$advogado['oab_uf']}}
        <hr/>
    </h3>
    <div class="row">
        <div class="col-md-2 p-2 rounded m-1 esconderNaImpressao" style="background: white;">
            @if($fotoPerfilDados['nome_arquivo'] != 'storage/app/semfoto.jpg')
                <img src="{{asset($fotoPerfilDados['nome_arquivo'])}}" style="max-height: 8rem;" data-content="{{$fotoPerfilDados['tipo']}},{{$fotoPerfilDados['legenda']}},{{$fotoPerfilDados['created_at']}}" class="mx-auto d-block imagemSrc cursorPointer text-center figure-img img-fluid" title="Clique para ampliar a foto" alt="{{$fotoPerfilDados['tipo']}}"/>
            @else
                <img src="{{asset($fotoPerfilDados['nome_arquivo'])}}" style="max-height: 8rem;" class="mx-auto d-block text-center figure-img img-fluid"/>
            @endif
            <h5 class="mt-4" style="text-align: center">{{$advogado['nome']}}</h5>
            <p style="text-align: center">
                OAB: <strong>{{$advogado['oab_numero']}}/{{$advogado['oab_uf']}}</strong>,<br/>
                Ocorrências Registradas: <strong>{{$advogado['qtd_ocorrencias']}}</strong>,<br/>
                Presos Visitados: <strong>{{$advogado['qtd_presos_visitados']}}</strong>,<br/>
                Observação: <strong>{{$advogado['obs']}}</strong><br/>
            {{--Biometria Registrada: Sim--}}
            </p>
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <a class="nav-link active" id="v-pills-dadospessoais-tab" data-toggle="pill" href="#v-pills-dadospessoais" role="tab" aria-controls="v-pills-dadospessoais" aria-selected="true"><i class="fas fa-id-card"></i> Dados Pessoais</a>
                <a class="nav-link" id="v-pills-galeria-tab" data-toggle="pill" href="#v-pills-fotos" role="tab" aria-controls="v-pills-fotos" aria-selected="false"><i class="fas fa-camera"></i> Fotos</a>
                <a class="nav-link" id="v-pills-ocorrencias-tab" data-toggle="pill" href="#v-pills-ocorrencias" role="tab" aria-controls="v-pills-ocorrencias" aria-selected="false"><i class="fas fa-history"></i> Ocorrências</a>
                <a class="nav-link" id="v-pills-internos-tab" data-toggle="pill" href="#v-pills-internos" role="tab" aria-controls="v-pills-internos" aria-selected="false"><i class="fas fa-users"></i> Internos</a>
                <a class="nav-link" id="v-pills-documentos-tab" data-toggle="pill" href="#v-pills-documentos" role="tab" aria-controls="v-pills-documentos" aria-selected="true"><i class="fas fa-folder-open"></i> Documentos</a>
            </div>
        </div>
        <div class="col p-2 m-1 w-100 rounded" style="background: white">
            <div class="tab-content" id="v-pills-tabContent">
                <div class="tab-pane fade show active" id="v-pills-dadospessoais" role="tabpanel" aria-labelledby="v-pills-dadospessoais-tab">@include('advogado.show.includes._dadospessoais')</div>
                <div class="tab-pane fade" id="v-pills-fotos" role="tabpanel" aria-labelledby="v-pills-fotos-tab">@include('advogado.show.includes._fotos')</div>
                <div class="tab-pane fade" id="v-pills-ocorrencias" role="tabpanel" aria-labelledby="v-pills-ocorrencias-tab">@include('advogado.show.includes._ocorrencias')</div>
                <div class="tab-pane fade" id="v-pills-internos" role="tabpanel" aria-labelledby="v-pills-internos-tab">@include('advogado.show.includes._internos')</div>
                <div class="tab-pane fade" id="v-pills-documentos" role="tabpanel" aria-labelledby="v-pills-documentos-tab">@include('advogado.show.includes._documentos')</div>
            </div>
        </div>
    </div>
    <a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
    {{-- modal para abrir a foto do visita --}}
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
    {{-- !! modal para abrir a foto do visita --}}
@endsection
@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{ asset('css/advogado/show/show.css') }}" rel="stylesheet">
@endpush
@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{asset('js/interno/show.js')}}"></script>
@endpush