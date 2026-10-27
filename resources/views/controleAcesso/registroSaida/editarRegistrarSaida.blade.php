@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-logout"></i> Editar Registro de Saída</h3>
        <hr>
        <p>
            Editar um registro de saída de pessoas na unidade.

        </p>
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg) !!}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->


    <form action="{{ route('controleAcesso.editarRegistrarSaidaStore')}}" id="formRegistroSaida" method="get">
{{ csrf_field() }}
        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="cpf">CPF</label>
                <input type="text" name="cpf" class="form-control" id="cpf" value="{{$registroSaida->cpf}}" autocomplete="off" disabled>
            </div>

            <div class="form-group col-sm-3">
                <label for="dataDarSaida">Entrada</label>
                <select id="dataDarSaida" name="dataDarSaida"  class="form-control" disabled>
                    <option value="{{$registroSaida->data}}">
                        {{explode('-',$registroSaida->data)[2] . '-' . explode('-',$registroSaida->data)[1]. '-' . explode('-',$registroSaida->data)[0] . ' ' . $registroSaida->horario}}
                    </option>
                </select>
            </div>


            <div class="form-group col-sm-3">
                <label for="dataSaida">Data da Saída</label>
                <input type="text" name="dataSaida" class="form-control" id="dataSaida" value="{{explode('-',$registroSaida->data_saida)[2] . explode('-',$registroSaida->data_saida)[1]. explode('-',$registroSaida->data_saida)[0]}}" autocomplete="off" disabled>
            </div>

            <div class="form-group col-sm-3">
                <label for="horarioSaida">Horário da Saída</label>
                <input type="text" name="horarioSaida" class="form-control" id="horarioSaida" placeholder="Informe o horário de saida" autocomplete="off" required>
            </div>

        </div>

        <div id="flashRetorno"></div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Editar Registro de Saída</button>

    </form>

@endsection
@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/controleAcesso/registrarSaida/registrarSaida.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endpush


