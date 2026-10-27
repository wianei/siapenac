@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">

            <h3><i class="mdi mdi-pencil-circle-outline"></i> Alterar Dados da Cela</h3>
            <hr>
            <p>
                Realiza a alteração no cadastro da cela.
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

        <form action="{{ route('unidade.celaEditarStore', $idCela) }}" id="formDados" method="post">

                    {!! csrf_field() !!}
                    <div class="form-column">

                        <div class="form-group col-sm-6">
                            <label for="nome">Nome: <strong>{{ $dados[0]->nome or old('nome') }}</strong></label>
                        </div>
                        <div class="form-group col-sm-2">
                            <label for="capacidade">Capacidade</label>
                            <input type="number" name="capacidade" value="{{ $dados[0]->capacidade or old('capacidade') }}" class="form-control" id="capacidade" placeholder="Capacidade" required>
                        </div>



                    </div>

                    <div id="flashRetorno"></div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Alterações</button>

                </form>

                @endsection

                @push('script')
                <script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
                @endpush