@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fa fa-history"></i> Log de Acessos</h3>
        <hr>
        <p>
            Páginas que os usuários visitaram em determinado período.
        </p>
    </div>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('setic.log') }}">Log de Eventos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('setic.getLogAcesso') }}">Log de Acesso</a>
        </li>
    </ul>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))
                <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg) !!}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div>

<div class="alert alert-warning fontSize-13px">
    Ao informar as datas, insira um período de no máximo sete dias.
</div>
<form action="{{ route('setic.getLogAcesso') }}" method="post">
        {!! csrf_field() !!}
        <div class="form-row">
            
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
        
        <div id="flashRetorno"></div>
        <button type="submit" name="btnSubmitForm" value="buscar" id="btnSubmitForm" class="btn btn-primary btBuscaConsulta mt-3"><i class="fas fa-search"></i> Buscar Eventos</button>


    </form>
    <hr>
    
        @if($dados == "nd")

            <h4>Sua pesquisa não obteve resultados!</h4>

        @else

    @if($dados != false)
    

        @foreach($dados as $key => $dado)
        <div>
            <h2>{{ $key }}</h2>
            @foreach($dado as $item)
                <div style="border-bottom: 1px solid #e4e4e4; padding-bottom: 3px; margin-top: 5px;">{{ $item['url'] }}</div>
            @endforeach
            <hr>
        </div>
        @endforeach
        @endif
    @endif

@endsection
@push('css')
<link href="{{ asset('css/agenda/consulta.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/setic/logAcesso.js') }}"></script>
@endpush