@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-check"></i> Escolha de Lotações</h3>
        <hr>
        <p>
            Escolha três lotações selecionando-as por ordem de preferência.
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

    @if(count($escolha) == 0)
    <form action="{{ route('concursoRemocao.escolhaLotacaoStore') }}" method="post" id="formEsolhaLotacao">
        {!! csrf_field() !!}
        <div class="form-row" id="divLotacoes">

            <div class="form-group col-sm">
                <label for="lotacao1"> 1ª Opção de Lotação</label>
                <select id="lotacao1" name="lotacao1" class="form-control tipoEscolha">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}">
                           {{ $unidade->nome }} - {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm">
                <label for="lotacao2"> 2ª Opção de Lotação</label>
                <select id="lotacao2" name="lotacao2" class="form-control tipoEscolha">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}">
                           {{ $unidade->nome }} - {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm">
                <label for="lotacao3"> 3ª Opção de Lotação</label>
                <select id="lotacao3" name="lotacao3" class="form-control tipoEscolha">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}">
                           {{ $unidade->nome }} - {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div id="divConfirm">

        </div>
        <button type="button" id="btnSalvar" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Escolha</button>
        <button type="submit" id="btnConfirmarEscolha" class="btn btn-success mt-3 displayNone"><i class="fas fa-check"></i> Confirmar</button>
        <button type="button" id="btnSelecionarNovamente" class="btn btn-danger displayNone mt-3"><i class="mdi mdi-close"></i> Corrigir</button>

    </form>
        @else
        <div class="alert alert-danger fontSize-13px">
            Escolha realizada em {{ $escolha[0]->created_at }}.
        </div>
    @endif


@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/concursoRemocao/escolhaLotacao.js') }}"></script>
@endpush