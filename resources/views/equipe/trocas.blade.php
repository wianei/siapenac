@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fa fa-exchange-alt"></i> Registrar trocas</h3>
        <hr>
        <p>
            Registre e consulte as trocas de plantão registradas para os próximos dias.
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

    <form action="{{ route('equipe.trocasStore') }}" id="formCadastro" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="dataA">Data</label>
                <input type="text" name="dataA" class="form-control dataSelect" id="dataA" placeholder="Data" autocomplete="off" required>
            </div>

            <div class="form-group col-md-9">
                <label for="agenteA">Agente</label>
                <select id="agenteA" name="agenteA" class="form-control" required>
                    <option value="">
                        Selecione
                    </option>
                    @foreach($servidoresLotacao as $sl)
                        <option value="{{ $sl->matricula }}">
                            {{ utf8_encode($sl->nome) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="dataB">Data</label>
                <input type="text" name="dataB" class="form-control dataSelect" id="dataB" placeholder="Data" autocomplete="off" required>
            </div>

            <div class="form-group col-md-9">
                <label for="agenteB">Agente</label>
                <select id="agenteB" name="agenteB" class="form-control" required>
                    <option value="">
                        Selecione
                    </option>
                    @foreach($servidoresLotacao as $sl)
                        <option value="{{ $sl->matricula }}">
                            {{ utf8_encode($sl->nome) }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

            <div class="form-row">
                <div class="form-group col-sm-3">
                    <label for="inputFile">Arquivo Autorizando a permuta, assinado pelas partes</label>
                    <input type="file" name="inputFile[]" class="form-control" id="inputFile" accept="application/pdf">
                </div>
            </div>

        <div id="flashRetorno"></div>

        <button id="buttonSubmit" type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Registrar troca</button>
    </form>

    <hr>

    @if(count($dadosAgentes) > 0)

    <h4 class="mt-3">Trocas registradas para os próximos dias</h4>

    <div class="table-responsive-md mt-3">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">Data</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome / Matrícula</th>
                <th scope="col">Data</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome / Matrícula</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dadosAgentes as $dA)
                <tr class="">
                    <td scope="row" class="background_bddaff">{{ $dA['data'] }}</td>
                    <td class="background_bddaff"><img style="text-align: center" src="{{ $dA['foto'] }}" class="" alt="Foto do Perfil"></td>
                    <td class="background_bddaff">{{ $dA['nome']." (".$dA['matricula'].")" }}</td>
                    <td class="background_a1d5ff" scope="row">{{ $dA['permutaData'] }}</td>
                    <td class="background_a1d5ff"><img style="text-align: center" src="{{ $dA['foto'] }}" class="" alt="Foto do Perfil"></td>
                    <td class="background_a1d5ff">{{ $dA['permutaNome']." (".$dA['permutaMat'].")" }}</td>
                    <td class="text-center btn-acoes">
                        <a href="{{ route('equipe.trocaCancelar',$dA['id']) }}" data-toggle="tooltip" data-placement="top" title="Cancelar troca" onclick="return confirm('Deseja realmente excluir essa permuta?');"><i class="fas fa-trash-alt color_red"></i></a>

                        <a href="{{ route('equipe.arquivoPermuta',$dA['arquivo']) }}" data-toggle="tooltip" data-placement="top" title="Baixar arquivo"><i class="mdi mdi mdi-cloud-download"></i></a>
                    </td>
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
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/equipe/troca.js') }}"></script>
@endpush