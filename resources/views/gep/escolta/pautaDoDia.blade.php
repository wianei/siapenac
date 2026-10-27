@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-cogs"></i> Equipes do Dia</h3>
        <hr>
        <p>
            Cadastro de equipes
        </p>
    </div>
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div>

    <form action="{{ route('gep.pautaDiaStore') }}" id="formCadastrarEquipe" method="post" class="displayNone mb-5">

        {!! csrf_field() !!}
        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="dataEquipe">Data da Equipe</label>
                <input type="text" name="dataEquipe" class="form-control" id="dataEquipe" placeholder="Data da Equipe" autocomplete="off">
            </div>

            <div class="form-group col-sm-3">
                <label for="regiao">Região</label>
                <input type="text" name="regiao" class="form-control" id="regiao" placeholder="Ex.: zona sul">
            </div>

            <div class="form-group col-sm-3">
                <label for="vtr">Placa VTR</label>
                <input type="text" name="vtr" class="form-control" id="vtr" placeholder="Placa da VTR">
            </div>

            <div class="form-group col-sm-3">
                <label for="equipeNome">Nome Equipe</label>
                <input type="text" name="equipeNome" class="form-control" id="equipeNome" placeholder="Nome da equipe, ex.: FTPEN 01">
            </div>

        </div>

        <div class="form-row" id="divRowAgentes">

            <div class="form-group col-sm-12">
                <label for="agente">Nome do Agente</label>
                <input type="text" name="agente" class="form-control" id="agente" placeholder="Informe o nome do agente ou matrícula" autocomplete="off">
                <div class="form-group col-sm-12" id="divRetornoAgentes"></div>
            </div>

        </div>

        <h5 class="titleAgente">Agentes <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableAgentes" class="table-responsive">
            <table class="table mb-3">
                <thead class="background_1c2331 color_FFF">
                <tr class="">
                    <th scope="col">Matrícula</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Lotação</th>
                    <th scope="col">Status</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoAgente">

                </tbody>
            </table>
        </div>

        <div class="form-row">

            <div class="form-group col-sm-3" id="divComandanteVTR">
                <label for="vtr_comandante">Comandante VTR</label>
                <input type="text" name="vtr_comandante" class="form-control" id="vtr_comandante" value="" disabled>
            </div>
            <input type="hidden" name="vtrComandanteHidden" id="vtrComandanteHidden" value="">
            <input type="hidden" name="vtrMotoristaHidden" id="vtrMotoristaHidden" value="">
            <div class="form-group col-sm-3" id="divMotoristaVTR">
                <label for="vtr_motorista">Motorista VTR</label>
                <input type="text" name="vtr_motorista" class="form-control" id="vtr_motorista" value="" disabled>
            </div>

        </div>

        <div class="form-row mb-4">
            <label for="obs">Observações</label>
            <textarea name="obs" id="obs" class="form-control"></textarea>
        </div>

        <div id="flashRetorno"></div>

        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Salvar Equipe</button>

        <button type="button" id="btnCancelarEquipe" class="btn btn-danger mt-3"><i class="mdi mdi-close"></i> Cancelar Cadastro</button>


    </form>



    <form action="" method="get">
        <div class="form-row">

            <div class="form-group col-sm-2">
                <label for="dataBuscar">Data da Equipe</label>
                <input type="text" name="dataBuscar" class="form-control" id="dataBuscar" placeholder="Data" autocomplete="off">
            </div>

            <div class="form-group col-sm-2 mt-2">
                <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-search"></i> Buscar Equipe</button>
            </div>

            <div class="form-group col-sm-2 mt-4">
                <button type="button" id="btnCadastrarEquipe" class="btn btn-secondary"><i class="mdi mdi-plus"></i>Cadastrar Equipe</button>
            </div>

            <div class="form-group col-sm-2 mt-4">
                <a href="{{ route('gep.pautaDiaPdf', str_replace("/","-",$dataBusca)) }}" class="btn btn-secondary"><i class="mdi mdi-file-pdf"></i>Gerar PDF</a>
            </div>

        </div>

    </form>

    <h4 class="mt-5">Equipes do dia {{ $dataBusca }}</h4>
    <div class="row">
        @foreach($equipeDados as $dado)
            <div class="col-sm-4" style="border: 1px solid #CCCCCC; padding: 5px; margin-top: 5px; background-color: #FFF">
                <div class="width100Percent" style="background-color: #CCCCCC; padding: 3px">
                   {{ $dado['regiao'] }}
                    <a href="{{ route('gep.equipeDiaEditar', $dado['id']) }}" class="float-right btn-rounded btn-warning"><i class="mdi mdi-pencil"></i></a>
                </div>
                <div class="width100Percent" style="background-color: #e4e4e4; padding: 3px">
                    {{ $dado['placa_vtr'] }} ({{ $dado['nome_equipe'] }})
                </div>
                <span style="font-size: 12px">Agentes:</span>
                <ul style="font-size: 12px">
                    @foreach($dado['agentes'] as $agente)
                        <li>{{ $agente['nome'] }} ({{ $agente['matricula'] }})</li>
                    @endforeach
                </ul>
                <div class="width100Percent" style="font-size: 12px">
                    {{ $dado['obs'] }}
                </div>
                @if($dado['total'] > 0)
                    <div style="background-color: #fdfce0; padding: 5px">
                        <span style="font-size: 12px">Unidades:</span>
                        <ul style="font-size: 12px">
                            @foreach($dado['internos'] as $interno)
                                <li>{{ $interno['unidade'] }} ({{ $interno['qtd'] }})</li>
                            @endforeach
                            <li><strong>Total: {{ $dado['total'] }}</strong></li>
                        </ul>
                    </div>
                @else
                    <div style="background-color: #fdfce0; padding: 5px">
                        <span style="font-size: 12px">Unidades:</span>
                        <ul style="font-size: 12px">
                            <li><strong>Sem escoltas para esta equipe</strong></li>
                        </ul>
                    </div>

                @endif

            </div>
        @endforeach
    </div>


@endsection

@push('css')
<link href="{{ asset('css/gep/escolta/pautaDoDia.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/gep/escolta/equipesDia.js') }}"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>

@endpush