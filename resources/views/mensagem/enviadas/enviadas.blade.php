@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-arrow-top-right"></i> Mensagens Enviadas</h3>
        <hr>
        <p>
            Mensagens enviadas.
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
        <button type="button" class="btn btn-danger ml-1" id="btnExcluir">
            <i class="mdi mdi-delete-forever"></i> Excluir
        </button>
    </div>
    <hr>
    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col" class="text-center"><input type="checkbox" id="btnCheckTodosInternos" name="mensagem"></th>
                <th scope="col">Destinatário</th>
                <th scope="col">Assunto</th>
                <th scope="col">Data</th>
            </tr>
            </thead>

            <tbody id="retornoMensagens">
            @foreach($mensagens as $mensagem)
                <tr id="trMensagem{{ $mensagem['id'] }}" class="trMensagem font-weight-bold naolida">

                    <td scope="row" class="text-center"><input type="checkbox" value="{{ $mensagem['id'] }}" class="checkbox checkMensagem" id="checkMensagem{{ $mensagem['id'] }}" name="checkMensagem"></td>
                    <td><a href="{{ route('mensagem.lerEnviada',$mensagem['id']) }}">{{ $mensagem['destinatario'] }}

                        </a>
                        ({{ $mensagem['destinatarios'] }})
                    </td>
                    <td><a href="{{ route('mensagem.lerEnviada',$mensagem['id']) }}">{{ $mensagem['assunto'] }}</a></td>
                    <td>
                        @if($mensagem['data'] == date('d/m/Y'))
                            {{ $mensagem['hora'] }}
                        @else
                            {{ $mensagem['data'] }}
                        @endif
                    </td>

                </tr>

            @endforeach
            </tbody>
        </table>

    </div>

@endsection

@push('css')
<link href="{{ asset('css/mensagem/recebidas/recebidas.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/mensagem/enviadas.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
@endpush