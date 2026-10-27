@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-arrow-down"></i> Solicitação de Interno</h3>
        <hr>
        <p>
            Solicitar interno de outra unidade prisional.
        </p>
    </div>
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg)  !!}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    @include('movimentacoes.solicitacaoInterno._includes._navSolicitacaoInterno')


    <form action="{{ route('solicitacaoInterno.solicitarStore') }}" method="post" enctype="multipart/form-data" id="formSolicitacao">
        {!! csrf_field() !!}

        <h5 class="mt-4">Interno(s) Solicitado(s):</h5>

    <div class="form-row mt-2">
        <div class="form-group col-sm-12">
            <label for="interno">Nome do interno</label>
            <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome do interno" autocomplete="off">
            <div class="form-group col-sm-12" id="filter-records"></div>

        </div>
    </div>

    <h5 class="titleInterno">Internos <i class="mdi mdi-arrow-bottom-right"></i></h5>
    <div id="tableInternos" class="table-responsive-md">
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

    <h5 class="mt-4">Arquivos e Motivação:</h5>
    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="motivo">Motivo e observações</label>
            <textarea class="form-control" id="motivo" name="motivo" aria-label="motivo"></textarea>
        </div>
        <div class="form-group col-md-3">
            <label for="dataEntrada">Data de Entrada do Interno na Unidade Prisional:</label>
            <input type="text" name="dataEntrada" class="form-control" id="dataEntrada" placeholder="Data de Entrada" autocomplete="off" required>
        </div>
        <div class="form-group col-3">
            <label for="hora">Horário da Entrada do Interno</label>
            <input type="text" name="hora" class="form-control" id="hora" placeholder="Hora" autocomplete="off" required>
        </div>

        <div class="form-group col-md-6">
            <label for="documentos">Documentos</label>
            <input type="file" name="documentos[]" class="form-control" id="documentos" accept="application/pdf" multiple>
        </div>
    </div>

    <h5 class="mt-4">Alocação de Cela:</h5>
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
            <select id="cela" name="cela" class="form-control">
                <option value="">
                    Selecione
                </option>

            </select>
        </div>

    </div>
    <div id="flashRetorno"></div>

    <button type="submit" onclick="return confirm('Deseja confirmar a solicitação?');" class="btn btn-primary mt-2"><i class="fas fa-check"></i> Solicitar Interno</button>
    </form>

@endsection
@push('css')
<link href="{{ asset('css/solicitacaoInterno/solicitacaoInterno.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/solicitacaoInterno/solicitarInterno.js') }}"></script>
<script src="{{ asset('js/outros/buscaInternoGeral.js') }}"></script>
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
@endpush