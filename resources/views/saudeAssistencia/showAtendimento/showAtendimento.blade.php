@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
            <h3><i class="mdi mdi-file-document-box"></i> Detalhes do Atendimento</h3>
            <hr>
            <p>
                Informações detalhadas sobre o atendimento.
            </p>
    </div>

    <div class="row">
        <div class="col-sm">
            Data Atendimento:<br>
            <h6 class="float-left">{{ $dados[0]['dataAtendimento']}} às {{ $dados[0]['horaAtendimento'] }}</h6>
        </div>
        <div class="col-sm">
            Setor:<br>
            <h6 class="float-left">{{ $dados[0]['setor'] }}</h6>
        </div>
        <div class="col-sm">
            Unidade:<br>
            <h6 class="float-left">	{{ $dados[0]['unidade'] }}</h6>
        </div>
    </div>
    <hr>


    <div class="row mt-3">
        <div class="col-sm-12">
            <h5>Descrição do Atendimento <i class="mdi mdi-arrow-bottom-right"></i></h5>
                {!! $dados[0]['descricao'] !!}
        </div>

    </div>
    <hr>

        <h5 class="titleInterno mt-3">Internos <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableInternos" class="table-responsive">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoInterno">

                    <tr class="">
                        <td scope="row">{{ $dados[0]['prontuario'] }}</td>
                        <td>
                            <img style="text-align: center" src="{{ $dados[0]['foto'] }}" class="" alt="Foto do Perfil">
                        </td>
                        <td>{{ $dados[0]['interno'] }}</td>
                        <td>{{ $dados[0]['localizacao'] }}</td>
                        <td class="btn-acoes color_01579b"><a href="{{ route('interno.show', $dados[0]['prontuario']) }}" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a></td>
                    </tr>

                </tbody>
            </table>
        </div>

    <p class="text-left mt-5">{{ $dados[0]['log'] }}</p>

    <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div id="fototipo"></div>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
                </div>
                <div class="modal-body embed-responsive text-center">
                    <img src="" id="imagepreview" style="max-width:90%">
                </div>
                <hr/>
                <div class="text-left">
                    <div id="fotolegenda"></div>
                    <div id="fotodatainserida"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('css')
<link href="{{ asset('css/escolta/show/show.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/escolta/cadastro.js') }}"></script>
@endpush

