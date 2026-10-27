@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-3">
        <h3><i class="mdi mdi-map-marker-radius"></i>Localização dos Equipamentos</h3>
        <hr>
        <p>
            Quadro informativo da localização dos equipamentos.
        </p>
    </div>


    <form action="" id="formBuscarArmamento">

        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="tipoEquipamento">Tipo de Equipamento</label>
                <select id="tipoEquipamento" name="tipoEquipamento" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                    <option value="armaFogo">
                        Arma de Fogo
                    </option>
                    <option value="colete">
                        Colete
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3 displayNone divModelo">
                <label for="modelo">Armamento</label>
                <select id="modelo" name="modelo" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($modelosEquipamento as $modelo)
                        <option value="{{ $modelo->modelo }}">
                            {{ $modelo->modelo }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>

    </form>


        @foreach($localizacaoArmamento as $key => $armamento)

        <h5 class="titleInterno mt-5 padding02em background_e5e5e5" style="height: 42px">
            <span class="float-left mt-2">{{ $key }} <i class="mdi mdi-arrow-bottom-right"></i></span>
            <button class="btn btn-inverse-dark ml-5 float-right" id="expandirArmaFogo"><i class="mdi mdi-arrow-down-drop-circle"></i> Mostrar Lista</button>
        </h5>

            @foreach($armamento as $unidade => $arm)
                <h5>{{ $unidade }} ({{ count($arm) }})</h5>
                <div class="table-responsive-md">
                    <table class="table mb-3">
                        <thead class="background_f9f9f9 color_333">
                        <tr class="">
                            <th scope="col">#</th>
                            <th scope="col">Armamento</th>
                        </tr>
                        </thead>
                        <tbody id="tBodyArmaFogo">
                        @foreach($arm as $armafogo)
                            <tr class="">
                                <td class="fontSize-10px border1pxSolidRGBA0125"><strong>{{ $loop->iteration }}</strong></td>
                                <td class="border1pxSolidRGBA0125">{{ $armafogo['equipamentoTexto'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endforeach

@endsection
@push('css')
{{--<link href="{{ asset('css/armaria/mapa/mapaEquipamento.css') }}" rel="stylesheet">--}}
@endpush

@push('script')
<script src="{{ asset('js/armaria/localizacaoPorEquipamento.js') }}"></script>
@endpush