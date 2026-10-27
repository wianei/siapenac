@if(\Illuminate\Support\Facades\Auth::user()->tipo >= 600)
<div class="container">
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-folder-open"></i> Documentos do interno</h3>
        <hr>
        <p>
            Pasta virtual do interno, todos os documentos relativos ao interno, sob a posse do IAPEN e emissão de certidões.
        </p>
        <a href="{{route('interno.editar',$interno['id'])}}" data-toggle="tooltip" data-placement="top" title="Inserir ou Editar" class="esconderNaImpressao btn btn-primary text-right"><i class="fas fa-edit"></i>Inserir ou Editar</a>
    </div>
</div>

@if(count($documentos) == 0)
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
                <a href="{{ route('interno.fileDoc',['id' => $documento->fk_id, 'filename' => $documento->nome_arquivo])}}" id="" onclick="" class="btn btn-primary mr-1 mb-1 fileDownload docModal" title="Visualizar Arquivo" data-toggle="tooltip" data-placement="right"><i class="mdi mdi-cloud-download"></i></a>
            <p class="text-left">Inserido por: {{ $documento->cpf_usuario }} em {{ $documento->logData }}</p>
        </div>
    </div>
@endforeach

{{--<div class="modal fade bd-example-modal-lg" id="modalIframe" tabindex="-1" role="dialog"--}}
     {{--aria-labelledby="myLargeModalLabel" aria-hidden="true">--}}
    {{--<div class="modal-dialog modal-lg">--}}
        {{--<div class="modal-content">--}}
            {{--<iframe src="{{ route('documentosView',111) }}" id="iframeTest" data-parm="" width="100%"--}}
                    {{--height="100%" style="height: 40em" frameborder="0"></iframe>--}}
        {{--</div>--}}
        {{--<div class="modal-footer">--}}
            {{--<button type="button" class="btn btn-light" id="closebtn" data-dismiss="modal">Fechar</button>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}
@endif
@push('script')
<script src="{{asset('js/interno/documentos.js')}}"></script>
@endpush