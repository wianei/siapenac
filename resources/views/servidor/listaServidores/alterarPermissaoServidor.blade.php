@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
            <h3><i class="mdi mdi-pencil-circle-outline"></i> Alterar Permissão do Usuário</h3>
            <hr>
            <p>
                Realiza a alteração da permissão do usuário no {{ config('app.name') }}.
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

        <form action="{{ route('servidor.alterarPermissaoStore', $dados[0]->cpf) }}" method="post">

            <h5>Nome: {{ $nomeServidor }}</h5>
                    {!! csrf_field() !!}
                    <div class="form-row">

                        <div class="form-group col-sm-3">
                            <label for="tipo">Tipo de Permissão</label>
                            <select name="tipo" id="tipo" class="form-control" required>
                                <option value="16" @if( isset( $dados ) && $dados[0]->tipo == "16" ) selected @endif>-- INICIAL --</option>
                                <option value="5" @if( isset( $dados ) && $dados[0]->tipo == "5" ) selected @endif>Administrativo</option>
                                <option value="6" @if( isset( $dados ) && $dados[0]->tipo == "6" ) selected @endif>Controlador</option>
                                <option value="1" @if( isset( $dados ) && $dados[0]->tipo == "1" ) selected @endif>Operacional</option>
                            </select>
                        </div>

                    </div>

                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Alterações</button>

                </form>

                @endsection

                @push('css')
                <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                @endpush

                @push('script')
                <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
                <script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
                <script src="{{ asset('js/setic/listarContas.js') }}"></script>
        @endpush