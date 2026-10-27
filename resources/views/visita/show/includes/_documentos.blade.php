<div class="container">
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-folder-open"></i> Documentos da visita</h3>
        <hr>
        <p>
            Pasta virtual da visita, todos os documentos relativos a visitante, sob a posse do IAPEN e emissão de certidões.
        </p>

        @if(PermissaoService::getVerifyPermissionView("VisitaController","editar"))
        <a href="{{route('visita.editar',$visita['id'])}}" data-toggle="tooltip" data-placement="top" title="Inserir ou Editar" class="esconderNaImpressao btn btn-primary text-right"><i class="fas fa-edit"></i>Inserir ou Editar</a>
        @endif

    </div>
</div>
@if(empty($documentos))
    <p>Sem documentos cadastrados.</p>
@endif

@foreach($documentos as $documento)
    <div class="row timeline">
        <div class="col-md">
            <hr/>
        </div>
    </div>
    <div class="row align-items-center how-it-works d-flex">
        <div class="col-md">
            <h5>{{ $documento->descricao }}</h5>
            <a href="{{ route('visita.fileDoc',['id' => $documento->fk_id, 'filename' => $documento->nome_arquivo])}}" id="" onclick="" class="btn btn-primary mr-1 mb-1 fileDownload docModal" title="Visualizar Arquivo" data-toggle="tooltip" data-placement="right"><i class="mdi mdi-cloud-download"></i></a>
            <p class="text-left">Inserido por: {{ utf8_encode($documento->cpf_usuario) }} em {{ $documento->logData }}</p>
        </div>
    </div>
@endforeach

@push('script')
<script src="{{asset('js/interno/documentos.js')}}"></script>
@endpush