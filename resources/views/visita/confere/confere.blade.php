@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-format-list-numbers"></i> Confere Geral de Visitas</h3>
        <hr>
        <p>
            Lista de visitas por pavilhão, ala e cela.
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

    <form action="{{ route('visita.confereVisitaStore') }}" method="post">

        {!! csrf_field() !!}

        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="tipoConfere">Tipo de Listagem</label>
                <select id="tipoConfere" name="tipoConfere" class="form-control" required>
                    <option value="">
                        Selecione
                    </option>
                    <option value="nome">
                        Separado por Visitante (ordem alfabetica)
                    </option>
                    <option value="interno">
                        Separado por interno (ordem alfabetica)
                    </option>
                </select>
            </div>
            <div class="form-group col-sm-3">
                <label for="unidade_prisional">Presídio</label>
                <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}">
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3 displayNone" id="divPavilhao">
                <label for="pavilhao">Pavilhão</label>
                <select id="pavilhao" name="pavilhao" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3 displayNone" id="divAla">
                <label for="ala">Ala</label>
                <select id="ala" name="ala" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3 displayNone" id="divCela">
                <label for="cela">Cela</label>
                <select id="cela" name="cela" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

        </div>


        <button type="submit" class="btn btn-primary"><i class="mdi mdi-file-document"></i> Gerar Confere</button>

    </form>

@endsection
@push('css')
{{--<link href="{{ asset('css/interno/buscar/buscar.css') }}" rel="stylesheet">--}}
@endpush

@push('script')
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/interno/buscar.js') }}"></script>
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
@endpush
