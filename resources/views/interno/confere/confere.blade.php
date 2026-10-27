@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-format-list-numbers"></i> Confere Geral de Internos</h3>
        <hr>
        <p>
            Lista de internos por pavilhão, ala e cela.
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

    <form action="{{ route('interno.confereGeralStore') }}" method="post">

        {!! csrf_field() !!}

        <div class="form-row">

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

            <div class="form-group col-sm-3">
                <label for="confere">Confere</label>
                <select id="confere" name="confere" class="form-control selectUnidade">
                    <option value="resumido">
                        Confere Resumido
                    </option>
                    <option value="simples">
                        Confere Completo
                    </option>
                    <!-- <option value="completo">
                        Confere Completo
                    </option> -->
                </select>
            </div>

           {{-- <div class="form-group col-sm-12" id="divConfere">
                <div class="radio-inline">
                    <label><input type="radio" id="resumido" name="resumido" checked>Confere Resumido</label>
                    <label><input type="radio" id="simples" name="simples">Confere Simples</label>
                    <label><input type="radio" id="completo" name="completo">Confere Completo</label>
                </div>
            </div>--}}

            @if(PermissaoService::getTipoPermissao() == "I")
                <div class="col-sm-3 displayNone" id="divIncluirFotos">
                    <input type="checkbox" id="fotos" name="fotos">
                    <label>Incluir Fotos</label>
                </div>
            @endif

        </div>
        <div id="flashRetorno"></div>
        <button type="button" id="btnGerarConfere" class="btn btn-primary"><i class="mdi mdi-file-document"></i> Gerar Confere</button>


    </form>

@endsection
@push('css')
{{--<link href="{{ asset('css/interno/buscar/buscar.css') }}" rel="stylesheet">--}}
@endpush

@push('script')
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/interno/buscar.js') }}"></script>
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
<script src="{{ asset('js/interno/confere.js') }}"></script>
@endpush