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

    <form action="{{ route('listagem.frequenciaDiariaInternoCriarListaStore') }}" method="post" enctype="multipart/form-data" id="formCriaLista">
        {!! csrf_field() !!}
        <div class="form-row">
            <div class="form-group col-9">
                <label for="nomeLista">Nome da Lista</label>
                <input type="text" name="nomeLista" class="form-control" id="nomeLista" placeholder="Informe o nome da lista" autocomplete="off">
            </div>

            <div class="form-group col-sm-3">
                <label for="tipo">Tipo de Listagem</label>
                <select id="tipo" name="tipo" class="form-control">
                    <option value="">Selecione</option>
                    <option value="educacao">Educação</option>
                    <option value="trabalho">Trabalho</option>
                    <option value="aberto">Aberto</option>
                    <option value="semiaberto">Semiaberto</option>

                </select>
            </div>

            <div class="form-group col-12">
                <label for="legenda">Legenda</label>
                <input type="text" name="legenda" class="form-control" id="legenda" placeholder="informe uma legenda para o registro de presença. Ex.: ATM: Atestado Médico. - Presença(P) e Falta(F) não são necessários" autocomplete="off">
            </div>

        </div>

        <div class="form-row">
            <div class="form-group col-12">
                <label for="interno">Nome do interno</label>
                <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome do interno" autocomplete="off">
                <div class="form-group col-sm-12" id="filter-records"></div>
            </div>

        </div>

        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="unidade_prisional">Unidade Prisional</label>
                <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                    <option value="{{ $usuarioLogadoUP['id'] }}">
                        {{ $usuarioLogadoUP['sigla'] }}
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="pavilhao">Pavilhão</label>
                <select id="pavilhao" name="pavilhao" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>

                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="ala">Ala</label>
                <select id="ala" name="ala" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>

                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="cela">Cela</label>
                <select id="cela" name="cela" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>

                </select>
            </div>

        </div>

        <span id="spanInternosCela" class="displayNone">
            <h5 class="titleInternosCela"><span id="txtCela"></span> <i class="mdi mdi-arrow-bottom-right"></i></h5>
            <div id="tableInternosCela" class="table-responsive">
                <table class="table mb-3" id="internosCela">
                    <thead class="btn-danger color_FFF">
                    <tr class="">
                        <th scope="col"><input type="checkbox" id="btnCheckTodosInternos"></th>
                        <th scope="col">Prontuário</th>
                        <th scope="col">Foto</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Facção</th>
                        <th scope="col">Liderança</th>
                        <th scope="col">Localização</th>
                        <th scope="col">Ações</th>
                    </tr>
                    </thead>
                    <tbody id="retornoInternosCela">

                    </tbody>
                </table>
                <button type="button" id="btnAddTodosInternos" class="btn btn-success mt-3 mb-3"><i class="mdi mdi-account-plus"></i> Adicionar</button>
            </div>

        </span>

        <div id="tableInternos" class="table-responsive">
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

                </tbody>
            </table>
        </div>


        <div id="flashRetorno">

        </div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary"><i class="fas fa-check"></i> Criar Listagem</button>

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
<script src="{{ asset('js/listagem/frequenciaDiariaInterno/criarLista.js') }}"></script>
@endpush