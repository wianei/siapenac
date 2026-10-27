@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-check"></i> Definir Lotação</h3>
        <hr>
        <p>
            Define a lotação do servidor.
        </p>
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg)  !!}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    <h5>Unidade Atual: {{ $unidadeAtual }}</h5>
    <h5>Unidade de Destino: {{ $unidadeDestinoNome }}</h5>
    <h5>Servidor(a): {{ $nomeServidor }} ({{ $matriculaServidor }}) / {{ $turma }}ª Turma</h5>
    <br>
        <form action="{{ route('concursoRemocao.definirLotacaoServidorStore') }}" method="post" id="formEsolhaLotacao">
            {!! csrf_field() !!}
            <div class="form-row">

                <div class="form-group col-sm">
                    <label for="lotacao"> Definir Lotação</label>
                    <select id="lotacao" name="lotacao" class="form-control">
                        @foreach($escolhas as $escolha)
                            <option value="{{ $escolha['id']}}" @if($escolha['id'] == $unidadeDestinoId) selected @endif>
                                {{ $escolha['opcao'] }}ª opção - {{ $escolha['nome'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="matriculaServidor" value="{{$matriculaServidor}}">
                <input type="hidden" name="ordemOpcao" value="{{$ordemOpcao}}">
                <input type="hidden" name="unidadeAtual" value="{{$unidadeAtualId}}">
                <input type="hidden" name="turma" value="{{$turma}}">

            </div>

            <button type="submit" id="btnConfirmarEscolha" class="btn btn-success mt-3"><i class="fas fa-check"></i> Confirmar</button>

        </form>


@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/concursoRemocao/escolhaLotacao.js') }}"></script>
@endpush