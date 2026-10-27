@extends('template.advogado.themeAdvogado')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-calendar"></i> Agendamento de Atendimento</h3>
        <hr>
        <p>
            Realizar agendamento de atendimento.
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

    @if($penalidadeFalta == 'I')

    <form action="{{ route('oab.store') }}" method="post" id="formCadastro">

        {!! csrf_field() !!}

        <div class="form-row">
            <div class="form-group col-sm-12">
                <label for="interno">Interno</label>
                <select id="interno" name="interno" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($dadosInternos as $unidade => $internos)

                        <optgroup label=" &diams; {{ $unidade }} &diams; ">

                            @foreach($internos as $key => $interno)
                                <option value="{{ $interno['id'] }}">
                                {{ $interno['nome'] }}
                                </option>
                            @endforeach

                        </optgroup>

                    @endforeach

                </select>
            </div>
        </div>

        <div class="form-row displayNone" id="dadosAtendimento">
            <div class="form-group col-sm-3">
                <label for="dataAtendimento">Data do atendimento</label>
                <input type="text" class="form-control" name="dataAtendimento" id="dataAtendimento" placeholder="Data do atendimento" autocomplete="off">
            </div>

            <div class="form-group col-sm-3">
                <label for="horario">Horário</label>
                <select id="horario" name="horario" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

        </div>

        <div id="flashRetorno"></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Agendamento</button>

    </form>
        @else

        <div class="alert alert-danger fontSize-13px">
            Você não pode mais agendar atendimentos neste mês, o número de faltas  foi atingido.
        </div>
    @endif

@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/oab/agendamento/agendar.js') }}"></script>
@endpush