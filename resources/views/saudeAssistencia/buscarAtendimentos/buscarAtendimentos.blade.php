@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-file-find"></i> Buscar Atendimentos</h3>
        <hr>
        <p>
            Buscar Atendimentos na base de dados.
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


    <form action="#">

        <div class="form-row">

            <div class="form-group col-sm-3">
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
                <label for="setor">Setor</label>
                <select id="setor" name="setor" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="Assistência Social">Assistência Social</option>
                    <option value="Assistência Jurídica">Assistência Jurídica</option>
                    <option value="Clinica Médica">Clinica Médica</option>
                    <option value="Controle de Dieta">Controle de Dieta</option>
                    <option value="Deficiência">Deficiência</option>
                    <option value="Enfermagem">Enfermagem</option>
                    <option value="Enfermidade">Enfermidade</option>
                    <option value="Odontologia">Odontologia</option>
                    <option value="Psicologia">Psicologia</option>
                    <option value="Psiquiatria">Psiquiatria</option>
                    <option value="Saúde">Saúde</option>
                    <option value="Terapia Ocupacional">Terapia Ocupacional</option>
                    <option value="Vacina">Vacina</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="dataInicioAtendimento">Data Inicial do Atendimento</label>
                <input type="text" name="dataInicioAtendimento" class="form-control" id="dataInicioAtendimento" placeholder="Informe a data inicial do atendimento" autocomplete="off">
            </div>

            <div class="form-group col-sm-3" id="divRowDataFinal">
                <label for="dataTerminoAtendimento">Data Final do Atendimento</label>
                <input type="text" name="dataTerminoAtendimento" class="form-control" id="dataTerminoAtendimento" placeholder="Informe a data final do Atendimento" autocomplete="off">
            </div>

        </div>

        <div class="form-row" id="formRowDivInternos">
            <div class="form-group col-sm-12">
                <label for="interno">Interno(s) Envolvido(s)</label>
                <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome do interno" autocomplete="off">
                <div class="form-group col-sm-12" id="filter-records"></div>

            </div>
        </div>

        <h5 class="titleInterno">Internos <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableInternos" class="table-responsive-md">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Facção</th>
                    <th scope="col">Liderança</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoInterno">

                </tbody>
            </table>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar Atendimentos</button>

    </form>

            @if($dados != false)

                <div id="links" class="mt-5">
                    {{ $dados->links() }}
                </div>

                @if($dados->total() > 0)
                    <h6 class="mt-2"><i>Sua busca retornou
                            {{ $dados->total() }} resultados
                        </i>
                    </h6>

                @else

                    <h5 class="mt-5"><i>Sua busca não obteve resultados! </i>
                    </h5>

                @endif

                <div class="table-responsive-md">
                    <table class="table mt-5">
                        <thead class="background_30516f color_FFF">
                        <tr class="">
                            <th scope="col">#</th>
                            <th scope="col">Data</th>
                            <th scope="col">Localização</th>
                            <th scope="col">Interno</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($dados as $dado)
                            <tr class="">
                                <td scope="row" class="fontSize-10px"><i><strong>{{($dados->currentPage() * $dados->perPage()) - $dados->perPage() + $loop->iteration  }}</strong></i></td>
                                <td>{{ $dado->data_atendimento." - ".$dado->hora_atendimento }}</td>
                                <td>{{ $dado->unidadeNome }}</td>
                                <td>{{ $dado->nomeInterno }}</td>
                                <td>{{ $dado->setor }}</td>
                                <td class="btn-acoes color_01579b">
                                    <a href="{{ route('saudeAssistencia.showAtendimento',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Ver Atendimento"><i class="mdi mdi-information-outline"></i></a>

                                    <a href="{{ route('saudeAssistencia.editarAtendimento', $dado->id) }}" data-toggle="tooltip" data-placement="top" title="Editar Atendimento"><i class="mdi mdi-pencil-circle-outline"></i></a>

                                    <a href="{{ route('saudeAssistencia.excluirAtendimento',['id' => $dado->id])}}" data-toggle="tooltip" data-placement="top" title="Apagar Atendimento" onclick="return confirm('Deseja realmente apagar este atendimento?');"><i class="mdi mdi mdi-delete color_red"></i></a>


                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            @endif
@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{ asset('css/ocorrencia/buscar/buscar.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/saudeAssistencia/buscarAtendimento.js') }}"></script>
<script src="{{ asset('js/outros/buscaServidor.js') }}"></script>
<script src="{{ asset('js/outros/buscaVisita.js') }}"></script>
<script src="{{ asset('js/outros/buscaInternoGeral.js') }}"></script>
<script src="{{ asset('js/outros/buscaAdvogado.js') }}"></script>
@endpush