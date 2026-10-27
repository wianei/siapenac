@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-plus"></i> Arquivos da Escolta</h3>
        <hr>
        <p>
            Lista dos arquivos referentes a escolta.
        </p>
    </div>
@if(isset($dados[0]))
    <div class="row">
        <div class="col-sm-3">
            Nº:<br>
            <h6 class="float-left">{{ $dados[0]->id }}</h6>
        </div>
        <div class="col-sm-4">
            Origem:<br>
            <h6 class="float-left">{{ $dados[0]->unidade }}</h6>
        </div>
        <div class="col-sm-1">
            <i class="mdi mdi-ray-start-arrow fontSize-2em color_3e7099"></i>
        </div>
        <div class="col-sm-4">
            Destino:<br>
            <h6 class="float-left">	{{ $dados[0]->destino }}</h6>
        </div>
    </div>
    <hr>

    <div class="row">
        <div class="col-sm-4">
            Solicitante:<br>
            @if($dados[0]->unidade != "")
                <h6 class="float-left">{{ $dados[0]->unidade }}</h6>
            @else
                <h6 class="float-left">Poder Judiciário: {{ $dados[0]->solicitante }}</h6>
            @endif
        </div>
        @if($dados[0]->tipo == "gep")
            <div class="col-sm-4">
                Responsável:<br>
                <h6 class="float-left">{{ $dados[0]->responsavel }}</h6>
            </div>
        @endif
        <div class="col-sm-4">
            Status:<br>
            <h6 class="float-left">{{ $dados[0]->status}}</h6>
        </div>
    </div>
    <hr>

    <div class="table-responsive">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Nome</th>
                <th scope="col">Data</th>
                <th scope="col">Download</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados  as $dado)
                <tr class="">
                    <td scope="row" class="fontSize-10px"><i><strong>{{ $loop->iteration  }}</strong></i></td>
                    <td>{{ $dado->nome_arquivo }}</td>
                    <td>{{ $dado->created_at }}</td>
                    <td class="btn-acoes">
                        <a href="{{ route('escolta.arquivo',['id' => $dado->fk_escolta, 'filename' => $dado->nome_arquivo])}}" data-toggle="tooltip" data-placement="top" title="Arquivos"><i class="mdi mdi mdi-cloud-download"></i></a>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    
    @endif

@endsection

@push('css')
<link href="{{ asset('css/escolta/cadastro/cadastro.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/escolta/cadastro.js') }}"></script>
<script src="{{ asset('js/outros/buscaInterno.js') }}"></script>
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>

@endpush

