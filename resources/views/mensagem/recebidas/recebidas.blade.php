@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-arrow-bottom-left"></i> Mensagens Recebidas</h3>
        <hr>
        <p>
            Mensagens recebidas.
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
        <button type="button" class="btn btn-secondary ml-1" id="btMarcarLida">
            <i class="mdi mdi-email-open"></i> Marcar como lida
        </button>
    </div>
    <hr>
    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col" class="text-center"><input type="checkbox" id="btnCheckTodosInternos" name="mensagem"></th>
                <th scope="col">Remetente</th>
                <th scope="col">Destinatário</th>
                <th scope="col"></th>
                <th scope="col">Assunto</th>
                <th scope="col">Data</th>
            </tr>
            </thead>

            <tbody id="retornoMensagens">
            @foreach($mensagens as $mensagem)
                <tr id="trMensagem{{ $mensagem['id'] }}" class="trMensagem font-weight-bold @if($mensagem['lido'] == "N") naoLida @else lida @endif">

                    <td scope="row" class="text-center"><input type="checkbox" value="{{ $mensagem['tipo'].".".$mensagem['id'] }}" class="checkbox checkMensagem" id="checkMensagem{{ $mensagem['id'] }}" name="checkMensagem"></td>
                    <td>
                        <a href="{{ route('mensagem.ler',$mensagem['id']) }}">
                            @if($mensagem['lido'] == "N")
                                <i class="mdi mdi-email"></i>
                            @else
                                <i class="mdi mdi-email-open"></i>
                            @endif
                            {{ $mensagem['remetente'] }}
                        </a>
                    </td>
                    <td><a href="{{ route('mensagem.ler',$mensagem['id']) }}">{{ $mensagem['destinatario'] }}</a></td>
                    <td  class="text-right">@if($mensagem['anexo'] == "S")<i class="mdi mdi-paperclip"></i> @endif</td>
                    <td><a href="{{ route('mensagem.ler',$mensagem['id']) }}">{{ $mensagem['assunto'] }}</a></td>
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
<script src="{{ asset('js/mensagem/recebidas.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
@endpush