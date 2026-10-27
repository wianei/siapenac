@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-format-list-numbers"></i> Listagem para o Regime Semiaberto</h3>
        <hr>
        <p>
            Listagem de frequência do regime semiaberto.
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

    <form action="{{ route('listagem.frequenciaSemiabertoStore') }}" method="post" enctype="multipart/form-data" id="formSemiaberto">
        {!! csrf_field() !!}
        <div class="form-row">
            <div class="form-group col-12">
                <label for="interno">Nome do interno</label>
                <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome do interno" autocomplete="off">
                <div class="form-group col-sm-12" id="filter-records"></div>
            </div>

            <div class="form-group col-sm-3">
                <label for="mesImpressao">Mês de Impressão</label>
                <select id="mesImpressao" name="mesImpressao" class="form-control">
                    <option value="">Selecione</option>
                    <option value="1">Janeiro</option>
                    <option value="2">Fevereiro</option>
                    <option value="3">Março</option>
                    <option value="4">Abril</option>
                    <option value="5">Maio</option>
                    <option value="6">Junho</option>
                    <option value="7">Julho</option>
                    <option value="8">Agosto</option>
                    <option value="9">Setembro</option>
                    <option value="10">Outrubro</option>
                    <option value="11">Novembro</option>
                    <option value="12">Dezembro</option>
                </select>
            </div>

            <div class="form-group col-3">
                <label for="data">Data de Início das Assinaturas</label>
                <input type="text" name="data" class="form-control" id="data" placeholder="Data" autocomplete="off">
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
            <h5 class="titleInternosCela"><span id="txtCela"></span>Gerar Lista com os Internos Abaixo <i class="mdi mdi-arrow-bottom-right"></i></h5>
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
        <button type="submit" id="btnSubmitForm" class="btn btn-primary"><i class="fas fa-check"></i> Gerar Listagem</button>

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
<script src="{{ asset('js/listagem/semiaberto/listagemSemiaberto.js') }}"></script>
@endpush