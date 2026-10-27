@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fa fa-users"></i> Equipe por dia</h3>
        <hr>
        <p>
            Consulte a equipe de agentes penitenciários trabalhando na unidade no dia.
        </p>
    </div>

    <form action="{{ route('equipe.dia') }}" method="get">

        <div class="form-row">

            <div class="form-group col-sm-6">
                <label for="unidade">Unidade</label>
                <select id="unidade" name="unidade" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}">
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="horaInicioPlantao">Horário(s) de Início do Plantão</label>
                <select id="horaInicioPlantao" name="horaInicioPlantao" class="form-control">
                    <option value="">
                        Selecione um horário
                    </option>
                </select>
            </div>

            <div class="form-group col-md-3">
                <label for="data">Data</label>
                <input type="text" name="data" class="form-control" id="data" placeholder="Data" autocomplete="off">
            </div>

        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>

    </form>

    <h4 class="mt-3">Equipe do Dia</h4>

    <div class="table-responsive-md mt-3">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Matrícula</th>
                <th scope="col">Nome</th>
                <th scope="col">Contato</th>
                <th scope="col">Escala</th>
                <th scope="col">Lot. na Unidade</th>
                <th scope="col">Status</th>
                <th scope="col">Função</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dadosAgentes as $dA)
                <tr class="">
                    <td scope="row">{{ $loop->iteration }}</td>
                    <td scope="row">{{ $dA['matricula'] }}</td>
                    <td>{{ $dA['nome'] }}</td>
                    <td>{{ $dA['fone'] }}</td>
                    <td>{{ $dA['escala'] }}</td>
                    <td>{{ $dA['lotacaoUnidade'] }}</td>
                    <td>{{ $dA['status'] }}</td>
                    <td>{{ $dA['funcao'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @if(count($dadosAgentesPermuta) > 0)
        <h4 class="mt-3">Trocas</h4>
        <div class="table-responsive-md mt-3">
            <table class="table mb-3">
                <thead class="background_8f8f8f color_FFF">
                <tr class="">
                    <th scope="col">#</th>
                    <th scope="col">Matrícula</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Contato</th>
                    <th scope="col">Troca realizada com</th>
                    <th scope="col">Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($dadosAgentesPermuta as $dAP)
                    <tr class="">
                        <td scope="row">{{ $loop->iteration }}</td>
                        <td scope="row">{{ $dAP['matricula'] }}</td>
                        <td><img style="text-align: center" src="{{ $dAP['foto'] }}" class="" alt="Foto do Perfil"></td>
                        <td>{{ $dAP['nome'] }}</td>
                        <td>{{ $dAP['fone'] }}</td>
                        <td>{{ $dAP['permutaNome']." (".$dAP['permutaMat'].")" }}<br>
                            @if($dAP['permutaData'] > date("d/m/Y"))
                                que trabalhará em {{ $dAP['permutaData'] }}
                                @else
                                que trabalhou em {{ $dAP['permutaData'] }}
                            @endif
                        </td>
                        <td>{{ $dAP['status'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

@endsection
@push('css')
<link href="{{ asset('css/agenda/consulta.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/equipe/dia.js') }}"></script>
@endpush