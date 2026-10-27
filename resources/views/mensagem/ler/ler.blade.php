@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-file-document"></i> Visualização de Mensagem</h3>
        <hr>
        <p>
            Visualizar mensagem selecionada.
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

    <div class="row ml-1">
        <a href="{{ route('mensagem.enviar') }}" class="btn btn-primary">
            <i class="mdi mdi-plus"></i> Nova mensagem
        </a>
        <a href="{{ route('mensagem.mensagemExcluir', $dadosMensagem[0]['tipo'].".".$dadosMensagem[0]['id']) }}" class="btn btn-danger ml-1" id="btnExcluir">
            <i class="mdi mdi-delete-forever" onclick="return confirm('Deseja realmente excluir a mensagem?');"></i> Excluir
        </a>
        <a href="{{ route('mensagem.responder', $dadosMensagem[0]['id']) }}" class="btn btn-secondary ml-1">
            <i class="mdi mdi-arrow-left"></i> Responder
        </a>
        <a href="{{ route('mensagem.encaminhar', $dadosMensagem[0]['id']) }}" class="btn btn-secondary ml-1">
            <i class="mdi mdi-arrow-right"></i> Encaminhar
        </a>
    </div>
    <hr>
        <h5>Assunto: {{ $dadosMensagem[0]['assunto'] }}</h5>
    <hr>
    <div class="row dadosMensagem">
        <div class="col-sm-9">De: {{ $dadosMensagem[0]['remetente'] }}</div>
        <div class="col-sm-3">Em {{ $dadosMensagem[0]['data']." às ".$dadosMensagem[0]['hora'] }}</div>
        <br>
        @if($dadosMensagem[0]['anexo'] != false)

            @foreach($dadosMensagem[0]['anexo'] as $an)
                <div class="col-sm-4 msgAnexo"><a href="{{ route('mensagem.arquivoVisualizar',['id'=>$dadosMensagem[0]['id'], 'filename'=>$an['nome']]) }}">Anexo {{ $loop->iteration }}: {{ $an['nome'] }}</a></div>
            @endforeach

        @endif

    </div>

    <div class="corpoMsg">
        {!!   $dadosMensagem[0]['mensagem'] !!}
    </div>

@endsection

@push('css')
<link href="{{ asset('css/mensagem/ler/ler.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/mensagem/recebidas.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
@endpush