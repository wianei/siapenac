@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-plus"></i> Arquivos da Solicitação</h3>
        <hr>
        <p>
            Lista dos arquivos referentes a solicitação.
        </p>
    </div>

    <div class="row">
        <div class="col-sm-3">
            Nome:<br>
            <h6 class="float-left">{{ $dados[0]->name }}</h6>
        </div>
        <div class="col-sm-4">
            CPF:<br>
            <h6 class="float-left">{{ $dados[0]->cpf }}</h6>
        </div>
        <div class="col-sm-4">
            E-mail:<br>
            <h6 class="float-left">	{{ $dados[0]->email }}</h6>
        </div>
        <div class="col-sm-12">
            Órgão:<br>
            <h6 class="float-left">	{{ $dados[0]->orgao }}</h6>
        </div>
    </div>
    <hr>


    <div class="table-responsive">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Nome</th>
                <th scope="col">Download</th>
            </tr>
            </thead>
            <tbody>
            @foreach($arquivos  as $arquivo)
                <tr class="">
                    <td scope="row" class="fontSize-10px"><i><strong>{{ $loop->iteration  }}</strong></i></td>
                    <td>{{ $arquivo->nome_arquivo }}</td>
                    <td class="btn-acoes">
                        <a href="{{ route('setic.arquivoRecuperacaoContaVisualizar',['id' => $arquivo->fk_recuperacao, 'filename' => $arquivo->nome_arquivo])}}" data-toggle="tooltip" data-placement="top" title="Arquivos"><i class="mdi mdi mdi-cloud-download"></i></a>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection


@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
@endpush

