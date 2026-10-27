@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-check"></i> Frequencia Diária dos Internos</h3>
        <hr>
        <p>
            Cria listagens de presença.
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

    @include('listagem.frequenciaDiariaInterno._includes._navMenuFrequencia')

    <form action="{{ route('listagem.editarListaStore') }}" method="post" enctype="multipart/form-data" id="formCriaLista">
        {!! csrf_field() !!}
        <input type="hidden" name="idLista" value="{{ $id }}">
        <div class="form-row">
            <div class="form-group col-6">
                <label for="nomeLista">Nome da Lista</label>
                <input type="text" name="nomeLista" class="form-control" value="{{ $lista->nome or old('nomeLista') }}" id="nomeLista" placeholder="Informe o nome da lista" autocomplete="off">
            </div>

            <div class="form-group col-sm-3">
                <label for="tipo">Tipo de Listagem</label>
                <select id="tipo" name="tipo" class="form-control">
                    <option value="" @if( isset( $lista ) && $lista->tipo == "" ) selected @endif>Selecione</option>
                    <option value="educacao" @if( isset( $lista ) && $lista->tipo == "educacao" ) selected @endif>Educação</option>
                    <option value="trabalho" @if( isset( $lista ) && $lista->tipo == "trabalho" ) selected @endif>Trabalho</option>
                    <option value="aberto" @if( isset( $lista ) && $lista->tipo == "aberto" ) selected @endif>Aberto</option>
                    <option value="semiaberto" @if( isset( $lista ) && $lista->tipo == "semiaberto" ) selected @endif>Semiaberto</option>

                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="status">Status</label>
                <select id="tipo" name="status" class="form-control">
                    <option value="A" @if( isset( $lista ) && $lista->status == "A" ) selected @endif>Ativa</option>
                    <option value="I" @if( isset( $lista ) && $lista->tipo == "I" ) selected @endif>Inativa</option>
                </select>
            </div>

            <div class="form-group col-12">
                <label for="legenda">Legenda</label>
                <input type="text" name="legenda" class="form-control" id="legenda" value="{{ $lista->legenda or old('legenda') }}" placeholder="informe uma legenda para o registro de presença. Ex.: ATM: Atestado Médico. - Presença(P) e Falta(F) não são necessários" autocomplete="off">
            </div>

        </div>

        <div class="form-row">
            <div class="form-group col-12">
                <label for="interno">Nome do interno</label>
                <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome do interno" autocomplete="off">
                <div class="form-group col-sm-12" id="filter-records"></div>
            </div>

        </div>


        <div id="tableInternos" class="table-responsive" style="display: block !important;">
            <h5 class="titleInternosCela"><span id="txtCela"></span>Criar Lista com os Internos Abaixo <i class="mdi mdi-arrow-bottom-right"></i></h5>
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
                @foreach($internos as $interno)
                    <tr id="tr{{$interno['id']}}">
                        <td scope="row">{{$interno['id']}}</td>
                        <td>
                            @if(file_exists(storage_path('app/arquivos/internos/fotos/' . $interno['id'] . "/" . FotoService::getFotoPrincipalByIdTipo($interno['id'], 'interno') )))
                                <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $interno['id'] . "/". FotoService::getFotoPrincipalByIdTipo($interno['id'], 'interno'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                            @else
                                <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                            @endif
                        </td>
                        <td>{{$interno['nome']}}</td>
                        <td>{{$interno['faccao']}}</td>
                        <td>{{$interno['lideranca']}}</td>
                        <td>{{$interno['localizacao']}}</td>
                        <td class="btn-acoes color_01579b">

                            <a href="#" data-toggle="tooltip" data-placement="top" class="removerInternoLista" data-content="{{ $interno['id'] }}" title="Remover da lista"><i class="mdi mdi-close-circle-outline"></i></a>

                            <a href="/interno/show/{{$interno['id']}}" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>


        <div id="flashRetorno">

        </div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary"><i class="fas fa-check"></i> Alterar Lista de Frequência</button>

    </form>

@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{ asset('css/listagem/aberto/listagemAberto.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
<script src="{{ asset('js/outros/buscaInterno.js') }}"></script>
<script src="{{ asset('js/listagem/frequenciaDiariaInterno/editarLista.js') }}"></script>
@endpush