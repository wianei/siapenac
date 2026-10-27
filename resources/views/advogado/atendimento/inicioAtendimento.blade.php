@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-play"></i> Iniciar Atendimento</h3>
        <hr>
        <p>
            Iniciar atendimento do advogado.
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

    <form action="{{ route('advogado.inicioAtendimentoStore') }}" id="formAtendimentoAdv" method="post">
{{ csrf_field() }}
        <div class="form-row">

            <div class="form-group col-sm-12 alert alert-warning w-100 p-3 fontSize-13px text-black">
                <strong>ATENÇÃO! </strong>Os advogados que aparecem na lista são os que entraram na unidade(já foram inseridos no controle de acesso) na data do Livro de Ocorrência em aberto ({{$dataPlantaoAberto}})!
            </div>

            <div class="form-group col-sm-12">
                <label for="advogado">Selecione o Advogado</label>
                <select id="advogado" name="advogado" class="form-control" required>
                    <option value="">
                        Selecione
                    </option>
                    @foreach($advogados as $advogado)

                        <option value="{{ $advogado->advId }}">
                            {{ $advogado->nome }}
                        </option>

                    @endforeach
                </select>
            </div>

        </div>

            <div class="table-responsive-md  mb-3 displayNone" id="divTableInternos">
                <h5>Selecione o(s) Interno(s) <i class="mdi mdi-arrow-bottom-right"></i></h5>
                <table  class="table">
                    <thead class="background_30516f color_FFF">
                    <tr class="">
                        <th scope="col">#</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Localização</th>
                    </tr>
                    </thead>
                    <tbody id="internosVisitados" >
                    </tbody>
                </table>
            </div>

        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="dataAtendimento">Data do Atendimento</label>
                <input type="text" name="dataAtendimento" class="form-control" id="dataAtendimento" placeholder="Informe a data do Atendimento" autocomplete="off" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="horaInicio">Hora de Início</label>
                <input type="text" name="horaInicio" class="form-control" id="horaInicio" placeholder="Informe a hora de inicio" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="horaTermino">Hora do Término</label>
                <input type="text" name="horaTermino" class="form-control" id="horaTermino" placeholder="Informe a hora do término" required>
            </div>

        </div>

        <div id="flashRetorno"></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Registrar Atendimento</button>

    </form>

    @if($advogadosLivro != false)

       {{-- <div id="links" class="mt-5">
            {{ $advogadosLivro->links() }}
        </div>--}}

        <div class="table-responsive">
            <br><br>
            <label for="advogado">Lista de atendimentos de advogado ocorridos até o fechamento do livro de ocorrências em aberto ({{$dataPlantaoAberto}})</label>
            <table class="table mt-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">OAB</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Interno Atendido</th>
                    <th scope="col">Hora de Entrada</th>
                    <th scope="col">Hora de Saída</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody>

                @foreach($advogadosLivro as $dado)
                    <tr>
                        {{-- calculo da ordem do registro --}}
                        {{--<td scope="row" class="fontSize-10px"><i><strong>{{($dados->currentPage() * $dados->perPage()) - $dados->perPage() + $loop->iteration  }}</strong></i></td>--}}
                        <td scope="row">{{$dado['oab']}}</td>


                        <td>{{ strtoupper($dado['nomeAdvogado']) }}</td>

                        <td style="font-size: 10px !important;">
                            {{ $dado['nomeInterno'] }}
                        </td>

                        <td>
                            {{ $dado['inicioAtendimento'] }}
                        </td>

                        <td >
                            {{ $dado['terminoAtendimento'] }}
                        </td>

                        <td class="btn-acoes color_01579b">


                        @if(PermissaoService::getVerifyPermissionView("AdvogadoController","inicioAtendimentoUpdate"))
                            <a href="{{ route('advogado.inicioAtendimentoUpdate',$dado['id']) }}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>
                        @endif


                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
       {{-- <div id="links" class="mt-5">
            {{ $advogadosLivro->links() }}
        </div>--}}

    @endif

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