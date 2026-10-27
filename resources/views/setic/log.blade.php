@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fa fa-history"></i> Log</h3>
        <hr>
        <p>
            Log de registros de ações no {{ config('app.ti') }}.
        </p>
    </div>

    <form action="">
        <div class="form-row">
            <div class="form-group col-sm-3" id="divDataEvento">
                <label for="dataInicio">Data de Início</label>
                <input type="text" name="dataInicio" class="form-control" id="dataInicio" placeholder="Informe a data de inicio">
            </div>

            <div class="form-group col-sm-3" id="divDataEvento">
                <label for="dataTermino">Data de Término</label>
                <input type="text" name="dataTermino" class="form-control" id="dataTermino" placeholder="Informe a data de término">
            </div>
            <button type="submit" id="btnSubmitForm" class="btn btn-primary btBuscaConsulta mt-3"><i class="fas fa-search"></i> Buscar Eventos</button>

        </div>
    </form>
    <hr>
    <div class="row row-striped">
        <div class="col-1 text-right color_3e7099">
            <h1 class="display-4"><span class="">06</span></h1>
            <h2>SET</h2>
        </div>
        <div class="col-11">
            <h3 class="text-uppercase"><strong>Atendimento médico</strong><a href="#"><i class="float-right mdi mdi-arrow-top-right"></i></a></h3>
            <ul class="list-inline">
                <li class="list-inline-item"><i class="fa fa-calendar-o" aria-hidden="true"></i> Segunda</li>
                <li class="list-inline-item"><i class="fa fa-clock-o" aria-hidden="true"></i> 08:00 - 11:00</li>
                <li class="list-inline-item"><i class="fa fa-location-arrow" aria-hidden="true"></i> Enfermaria</li>
            </ul>
            <p>Lorem ipsum dolsit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
        </div>
    </div>
    <hr>

    <div class="row row-striped">
        <div class="col-1 text-right color_3e7099">
            <h1 class="display-4"><span class="">06</span></h1>
            <h2>SET</h2>
        </div>
        <div class="col-11">
            <h3 class="text-uppercase"><strong>Atendimento médico</strong><a href="#"><i class="float-right mdi mdi-arrow-top-right"></i></a></h3>
            <ul class="list-inline">
                <li class="list-inline-item"><i class="fa fa-calendar-o" aria-hidden="true"></i> Segunda</li>
                <li class="list-inline-item"><i class="fa fa-clock-o" aria-hidden="true"></i> 08:00 - 11:00</li>
                <li class="list-inline-item"><i class="fa fa-location-arrow" aria-hidden="true"></i> Enfermaria</li>
            </ul>
            <p>Lorem ipsum dolsit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
        </div>
    </div>
    <hr>

    <div class="row row-striped">
        <div class="col-1 text-right color_3e7099">
            <h1 class="display-4"><span class="">06</span></h1>
            <h2>SET</h2>
        </div>
        <div class="col-11">
            <h3 class="text-uppercase"><strong>Atendimento médico</strong><a href="#"><i class="float-right mdi mdi-arrow-top-right"></i></a></h3>
            <ul class="list-inline">
                <li class="list-inline-item"><i class="fa fa-calendar-o" aria-hidden="true"></i> Segunda</li>
                <li class="list-inline-item"><i class="fa fa-clock-o" aria-hidden="true"></i> 08:00 - 11:00</li>
                <li class="list-inline-item"><i class="fa fa-location-arrow" aria-hidden="true"></i> Enfermaria</li>
            </ul>
            <p>Lorem ipsum dolsit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
        </div>
    </div>
    <hr>

    <div class="row row-striped">
        <div class="col-1 text-right color_3e7099">
            <h1 class="display-4"><span class="">06</span></h1>
            <h2>SET</h2>
        </div>
        <div class="col-11">
            <h3 class="text-uppercase"><strong>Atendimento médico</strong><a href="#"><i class="float-right mdi mdi-arrow-top-right"></i></a></h3>
            <ul class="list-inline">
                <li class="list-inline-item"><i class="fa fa-calendar-o" aria-hidden="true"></i> Segunda</li>
                <li class="list-inline-item"><i class="fa fa-clock-o" aria-hidden="true"></i> 08:00 - 11:00</li>
                <li class="list-inline-item"><i class="fa fa-location-arrow" aria-hidden="true"></i> Enfermaria</li>
            </ul>
            <p>Lorem ipsum dolsit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
        </div>
    </div>
    <hr>
    <div class="row row-striped">
        <div class="col-1 text-right color_3e7099">
            <h1 class="display-4"><span class="">07</span></h1>
            <h2>SET</h2>
        </div>
        <div class="col-11">
            <h3 class="text-uppercase"><strong>Atendimento médico</strong><a href="#"><i class="float-right mdi mdi-arrow-top-right"></i></a></h3>
            <ul class="list-inline">
                <li class="list-inline-item"><i class="fa fa-calendar-o" aria-hidden="true"></i> Segunda</li>
                <li class="list-inline-item"><i class="fa fa-clock-o" aria-hidden="true"></i> 08:00 - 11:00</li>
                <li class="list-inline-item"><i class="fa fa-location-arrow" aria-hidden="true"></i> Enfermaria</li>
            </ul>
            <p>Lorem ipsum dolsit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
        </div>
    </div>
    <hr>
    <div class="row row-striped">
        <div class="col-1 text-right color_3e7099">
            <h1 class="display-4"><span class="">07</span></h1>
            <h2>SET</h2>
        </div>
        <div class="col-11">
            <h3 class="text-uppercase"><strong>Atendimento médico</strong><a href="#"><i class="float-right mdi mdi-arrow-top-right"></i></a></h3>
            <ul class="list-inline">
                <li class="list-inline-item"><i class="fa fa-calendar-o" aria-hidden="true"></i> Segunda</li>
                <li class="list-inline-item"><i class="fa fa-clock-o" aria-hidden="true"></i> 08:00 - 11:00</li>
                <li class="list-inline-item"><i class="fa fa-location-arrow" aria-hidden="true"></i> Enfermaria</li>
            </ul>
            <p>Lorem ipsum dolsit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
        </div>
    </div>
    <hr>
    <div class="row row-striped">
        <div class="col-1 text-right color_3e7099">
            <h1 class="display-4"><span class="">07</span></h1>
            <h2>SET</h2>
        </div>
        <div class="col-11">
            <h3 class="text-uppercase"><strong>Atendimento médico</strong><a href="#"><i class="float-right mdi mdi-arrow-top-right"></i></a></h3>
            <ul class="list-inline">
                <li class="list-inline-item"><i class="fa fa-calendar-o" aria-hidden="true"></i> Segunda</li>
                <li class="list-inline-item"><i class="fa fa-clock-o" aria-hidden="true"></i> 08:00 - 11:00</li>
                <li class="list-inline-item"><i class="fa fa-location-arrow" aria-hidden="true"></i> Enfermaria</li>
            </ul>
            <p>Lorem ipsum dolsit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
        </div>
    </div>
    <hr>
    <div class="row row-striped">
        <div class="col-1 text-right color_3e7099">
            <h1 class="display-4"><span class="">07</span></h1>
            <h2>SET</h2>
        </div>
        <div class="col-11">
            <h3 class="text-uppercase"><strong>Atendimento médico</strong><a href="#"><i class="float-right mdi mdi-arrow-top-right"></i></a></h3>
            <ul class="list-inline">
                <li class="list-inline-item"><i class="fa fa-calendar-o" aria-hidden="true"></i> Segunda</li>
                <li class="list-inline-item"><i class="fa fa-clock-o" aria-hidden="true"></i> 08:00 - 11:00</li>
                <li class="list-inline-item"><i class="fa fa-location-arrow" aria-hidden="true"></i> Enfermaria</li>
            </ul>
            <p>Lorem ipsum dolsit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
        </div>
    </div>

@endsection
@push('css')
<link href="{{ asset('css/agenda/consulta.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
@endpush