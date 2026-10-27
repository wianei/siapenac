@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-check"></i> Registrar Presença</h3>
        <hr>
        <p>
            Registrar presença.
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

    @include('listagem.frequenciaDiariaInterno._includes._navMenuFrequencia')

    <form action="{{ route('listagem.relatorioFrequenciaDiariaStore') }}" method="post" enctype="multipart/form-data" id="formRelatorio">
        {!! csrf_field() !!}

        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="lista">Lista de Frequência</label>
                <select id="lista" name="lista" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($listas as $lista)
                        <option value="{{ $lista->id }}">{{ $lista->nome }}</option>
                    @endforeach

                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="mes">Mês</label>
                <select id="mes" name="mes" class="form-control">
                    <option value="">Selecione</option>
                    <option value="1">Janeiro</option>
                    <option value="2">Fevereiro</option>
                    <option value="3">Março</option>
                    <option value="4">Abril</option>
                    <option value="5">Maio</option>
                    <option value="6">Junho</option>
                    <option value="7">Julho</option>
                    <option value="8">Agosto</option>
                    <option value="9">Setembro</option>
                    <option value="10">Outrubro</option>
                    <option value="11">Novembro</option>
                    <option value="12">Dezembro</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="ano">Ano</label>
                <select id="ano" name="ano" class="form-control">
                    <option value="">Selecione</option>
                    <option value="{{ date("Y")-1 }}">{{ date("Y")-1 }}</option>
                    <option value="{{ date("Y") }}">{{ date("Y") }}</option>
                    <option value="{{ date("Y")+1 }}">{{ date("Y")+1 }}</option>
                </select>
            </div>

        </div>




        <div id="flashRetorno">

        </div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary"><i class="fas fa-check"></i> Gerar Relatório</button>

    </form>

@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{ asset('css/listagem/aberto/listagemAberto.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/listagem/frequenciaDiariaInterno/relatorioFrequencia.js') }}"></script>
@endpush