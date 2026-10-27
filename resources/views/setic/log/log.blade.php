@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fa fa-history"></i> Log</h3>
        <hr>
        <p>
            Log de registros de ações no {{ config('app.ti') }}.
        </p>
    </div>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('setic.log') }}">Log de Eventos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('setic.getLogAcesso') }}">Log de Acesso</a>
        </li>
    </ul>

    <form action="">
        <div class="form-row">
            
            <div class="form-group col-sm-3">
                <label for="evento">Eventos</label>
                <select id="evento" name="evento" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="add">
                        ADD
                    </option>
                    <option value="update">
                        UPDATE
                    </option>
                    <option value="delete">
                        DELETE
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="unidade_prisional">Unidade Prisional</label>
                <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">
                    <option value="">
                        Todos
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}" @if( isset( $interno ) && $unidade->id == $interno->unidade_prisional ) selected @endif>
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="cpf">CPF</label>
                <input type="text" name="cpf" class="form-control" id="cpf" placeholder="Informe o CPF de um usuário">
            </div>

            <div class="form-group col-sm-3" id="divDataEvento">
                <label for="dataInicio">Data de Início</label>
                <input type="text" name="dataInicio" class="form-control" id="dataInicio" placeholder="Informe a data de inicio">
            </div>

            <div class="form-group col-sm-3" id="divDataEvento">
                <label for="dataTermino">Data de Término</label>
                <input type="text" name="dataTermino" class="form-control" id="dataTermino" placeholder="Informe a data de término">
            </div>
        </div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary btBuscaConsulta mt-3"><i class="fas fa-search"></i> Buscar Eventos</button>


    </form>
    <hr>
    
    @if($dados != false)
    
        @foreach($dados as $dado)
        <div class="row row-striped">
            <div class="col-1 text-right color_3e7099">
                <h1 class="display-4"><span class="">
                    {{ \App\Classes\Tdata::getDiaData(\App\Classes\Tdata::getDataTimestamp($dado->created_at)) }}
                    </span></h1>
                <h2>{{ \App\Classes\Tdata::getMesExtAbrev(\App\Classes\Tdata::getMesData($dado->created_at)) }}</h2>
                <h3 class="fontSize-13px">{{ \App\Classes\Tdata::getAnoData(\App\Classes\Tdata::getDataTimestamp($dado->created_at)) }}</h3>
            </div>
            <div class="col-11">
                <h3 class="text-uppercase"><strong>{{ $dado->tabela_alterada }}</strong></h3>
                <ul class="list-inline">
                    <li class="list-inline-item"><i class="mdi mdi-account" aria-hidden="true"></i> {{  \App\Http\Controllers\Facade\FacadeController::getNomeServidorCpf($dado->cpf_usuario) }}({{$dado->cpf_usuario}})</li>
                    <li class="list-inline-item"><i class="mdi mdi-database" aria-hidden="true"></i> {{ strtoupper($dado->tipo_alteracao) }} - {{ \App\Classes\Tdata::getTempoTimestamp($dado->created_at) }}</li>
                </ul>
                <p>
                    {!! $dado->texto !!}
                </p>
            </div>
        </div>
        <hr>
        @endforeach

        <div id="links" class="mt-5">
            {{ $dados->links() }}
        </div>

    @endif

@endsection
@push('css')
<link href="{{ asset('css/agenda/consulta.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/setic/log.js') }}"></script>
@endpush