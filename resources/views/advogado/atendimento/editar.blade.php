@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-play"></i> Editar Atendimento</h3>
        <hr>
        <p>
            Edita um atendimento do advogado.
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

    <form action="{{ route('advogado.atendimentoUpdate', $atendimento->id) }}" id="formAtendimentoUpdate" method="post">
        {{ csrf_field() }}
        <div class="form-row">



            <div class="form-group col-sm-12">
                <label for="advogado">Advogado</label>
                <input type="text" id="advogado" name="advogado" value="{{$advogado->nome}}" class="form-control" readonly>
            </div>

        </div>

        <div class="table-responsive-md  mb-3 displayNone" id="divTableInternos">
            <h5>Interno<i class="mdi mdi-arrow-bottom-right"></i></h5>
            <table  class="table">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">#</th>
                    <th scope="col">Nome</th>
                </tr>
                </thead>
                <tbody id="internosVisitados" >
                    <td>{{$interno->id}}</td>
                    <td>{{$interno->nome}}</td>
                </tbody>
            </table>
        </div>

        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="dataAtendimento">Data do Atendimento</label>
                <input type="text" value="{{$atendimento->data_atendimento}}" name="dataAtendimento" class="form-control" id="dataAtendimento"  autocomplete="off" readonly>
            </div>

            <div class="form-group col-sm-3">
                <label for="horaInicio">Hora de Início</label>
                <input type="text" name="horaInicio" class="form-control" id="horaInicio" value="{{$horaEntrada}}" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="horaTermino">Hora do Término</label>
                <input type="text" name="horaTermino" class="form-control" id="horaTermino" value="{{$horaSaida}}" required>
            </div>

        </div>

        <div id="flashRetorno"></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Salvar Alterações</button>

    </form>


@endsection
@push('css')
<link href="{{ asset('css/interno/buscar/buscar.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="{{ asset('js/advogado/inicioAtendimento.js') }}"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endpush