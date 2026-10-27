@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-play-circle-outline"></i> Reativar Interno</h3>
        <hr>
        <p>
            Reativar Interno na sua Unidade.
        </p>
    </div>

    <h5 class="mb-4">{{ $nomeInterno }}</h5>
    <form id="reativarForm" action="{{ route('interno.reativarFunction', $id) }}" method="get">

        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="unidade_prisional">Escolha o Presídio</label>
                <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade" required>
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}" @if( isset( $interno ) && $unidade->id == $interno->unidade_prisional ) selected @endif>
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3 displayNone" id="divPavilhao">
                <label for="pavilhao">Pavilhão</label>
                <select id="pavilhao" name="pavilhao" class="form-control selectUnidade" required>
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3 displayNone" id="divAla">
                <label for="ala">Ala</label>
                <select id="ala" name="ala" class="form-control selectUnidade" required>
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3 displayNone" id="divCela">
                <label for="cela">Cela</label>
                <select id="cela" name="cela" class="form-control" required>
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="data_entrada">Data de Entrada</label>
                <input type="text" class="form-control" name="data_entrada" id="data_entrada" placeholder="Data de Entrada" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="hora_entrada">Horário da Entrada</label>
                <input type="text" name="hora_entrada" class="form-control" id="hora_entrada" placeholder="Hora" autocomplete="off" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="diasTriagem">Dias na triagem (0 não cumpre triagem)</label>
                <input type="number" name="diasTriagem" class="form-control" id="diasTriagem" placeholder="informe os dias que o interno ficará na triagem" required>
            </div>

            <div class="form-group col-sm-12">
                <label for="motivo_da_reativacao">Informe o Motivo da Reativação</label>
                <textarea name="motivo_da_reativacao" class="form-control" id="motivo_da_reativacao" required></textarea>
            </div>

        </div>

        <div id="flashRetorno"></div>
        <button id="reativarBtn" type="submit" onclick="return confirm('Deseja confirmar a reativação?');" class="btn btn-primary"><i class="mdi mdi-play-circle-outline"></i> Reativar Interno</button>

    </form>

@endsection
@push('css')
<link href="{{ asset('css/interno/buscar/buscar.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/interno/buscar.js') }}"></script>
<script src="{{ asset('js/interno/reativar.js') }}"></script>
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
@endpush