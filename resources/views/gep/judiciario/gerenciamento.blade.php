@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-cog"></i> Gerenciamento de Dados do Judiciário</h3>
        <hr>
        <p>
            Inserir e Alterar dados do Judiciário.
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

    <a href="{{ route('gep.dadosJudiciarioInserir') }}" id="btnAddOcorrencia" class="btn btn-dark"><i class="fa fa-plus"></i> Inserir Dados</a>

    <div class="table-responsive">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">Vara</th>
                <th scope="col">Juiz(a)</th>
                <th scope="col">Comarca</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados as $dado)
            <tr class="">
                <td>{{$dado->vara}}</td>
                <td>{{$dado->juiz}}</td>
                <td>{{$dado->comarca}}</td>
                <td class="btn-acoes color_01579b">
                    <a href="{{ route('gep.dadosJudiciarioEditar',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>

    </div>


@endsection

@push('css')
<link href="{{ asset('css/gep/escolta/escoltaDoDia.css') }}" rel="stylesheet">
@endpush


