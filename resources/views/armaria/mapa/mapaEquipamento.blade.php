@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-3">
        <h3><i class="mdi mdi-map-marker-radius"></i>Mapa de Localização dos Equipamentos</h3>
        <hr>
        <p>
            Quadro informativo da localização dos equipamentos distribuídos nas unidades prisionais da {{ config('app.uf') }}.
        </p>
    </div>


    <form action="" id="formBuscarArmamento">

        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="unidade_prisional">Presídio</label>
                <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                    <option value="0">
                        ** Estoque Armaria**
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}" @if( isset( $interno ) && $unidade->id == $interno->unidade_prisional ) selected @endif>
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>

    </form>


    @if($dados != false)
        <h5 class="titleInterno mt-5 padding02em background_e5e5e5" style="height: 42px">
            <span class="float-left mt-2">Arma de Fogo ({{ count($armaFogo) }}) <i class="mdi mdi-arrow-bottom-right"></i></span>
            <button class="btn btn-inverse-dark ml-5 float-right" id="expandirArmaFogo"><i class="mdi mdi-arrow-down-drop-circle"></i> Mostrar Lista</button>
        </h5>

        <div class="table-responsive-md displayNone" id="tableArmaFogo">
            <div class="float-right w-25">
                <input type="text" name="filtroArmaFogo" class="form-control" id="filtroArmaFogo" placeholder="Digite um termo para filtrar a lista de equipamentos" autocomplete="off">
            </div>

            <table class="table mb-3" id="TableArmaFogo">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Nº de Série</th>
                <th scope="col">Tipo</th>
                <th scope="col">Fabricante</th>
                <th scope="col">Modelo</th>
                <th scope="col">Calibre</th>
            </tr>
            </thead>
            <tbody id="tBodyArmaFogo">
            @foreach($armaFogo as $armafogo)
                <tr class="">
                    <td class="fontSize-10px border1pxSolidRGBA0125"><strong>{{ $loop->iteration }}</strong></td>
                    <td class="border1pxSolidRGBA0125">{{ $armafogo->n_serie }}</td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($armafogo->equipamento_especificacao) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ $armafogo->fabNome }}</td>
                    <td class="border1pxSolidRGBA0125">{{ $armafogo->modelo }}</td>
                    <td class="border1pxSolidRGBA0125">.{{ $armafogo->calibre }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

        <h5 class="titleInterno mt-5 padding02em background_e5e5e5" style="height: 42px">
            <span class="float-left mt-2">Dispositivo Elétrico Incapacitante ({{ count($dei) }}) <i class="mdi mdi-arrow-bottom-right"></i></span>
            <button class="btn btn-inverse-dark ml-5 float-right" id="expandirDei"><i class="mdi mdi-arrow-down-drop-circle"></i> Mostrar Lista</button>
        </h5>

        <div class="table-responsive-md displayNone" id="tableDei">
            <div class="float-right w-25">
                <input type="text" name="filtroDei" class="form-control" id="filtroDei" placeholder="Digite um termo para filtrar a lista de equipamentos" autocomplete="off">
            </div>

            <table class="table mb-3" id="TableDei">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Nº de Série</th>
                <th scope="col">Tipo</th>
                <th scope="col">Fabricante</th>
                <th scope="col">Modelo</th>
            </tr>
            </thead>
            <tbody id="tBodyDei">
            @foreach($dei as $d)
                <tr class="">
                    <td class="fontSize-10px border1pxSolidRGBA0125"><strong>{{ $loop->iteration }}</strong></td>
                    <td class="border1pxSolidRGBA0125">{{ $d->n_serie }}</td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($d->equipamento_especificacao) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ $d->fabNome }}</td>
                    <td class="border1pxSolidRGBA0125">{{ $d->modelo }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <h5 class="titleInterno mt-5 padding02em background_e5e5e5" style="height: 42px">
        <span class="float-left mt-2">Munição <i class="mdi mdi-arrow-bottom-right"></i></span>
        <button class="btn btn-inverse-dark ml-5 float-right" id="expandirMunicao"><i class="mdi mdi-arrow-down-drop-circle"></i> Mostrar Lista</button>
    </h5>
    <div class="table-responsive-md displayNone" id="tableMunicao">
        <div class="float-right w-25">
            <input type="text" name="filtroMunicao" class="form-control" id="filtroMunicao" placeholder="Digite um termo para filtrar a lista de equipamentos" autocomplete="off">
        </div>

        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Espécie</th>
                <th scope="col">Tipo</th>
                <th scope="col">Fabricante</th>
                <th scope="col">Modelo</th>
                <th scope="col">Calibre</th>
                <th scope="col">Quantidade</th>
            </tr>
            </thead>
            <tbody id="tBodyMunicao">
            @foreach($municao as $mun)
                <tr class="">
                    <td class="fontSize-10px border1pxSolidRGBA0125"><strong>{{ $loop->iteration }}</strong></td>
                    <td class="border1pxSolidRGBA0125">{{ $mun->equipamento_especie }}</td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($mun->equipamento_especificacao) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ $mun->fabNome }}</td>
                    <td class="border1pxSolidRGBA0125">{{ $mun->modelo }}</td>
                    <td class="border1pxSolidRGBA0125">.{{ $mun->calibre }}</td>
                    <td class="border1pxSolidRGBA0125">{{ $mun->quantidade }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <h5 class="titleInterno mt-5 padding02em background_e5e5e5" style="height: 42px">
        <span class="float-left mt-2">Carregador <i class="mdi mdi-arrow-bottom-right"></i></span>
        <button class="btn btn-inverse-dark ml-5 float-right" id="expandirCarregador"><i class="mdi mdi-arrow-down-drop-circle"></i> Mostrar Lista</button>
    </h5>
    <div class="table-responsive-md displayNone" id="tableCarregador">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Espécie</th>
                <th scope="col">Tipo</th>
                <th scope="col">Armamento</th>
                <th scope="col">Calibre</th>
                <th scope="col">Modelo</th>
                <th scope="col">Capacidade</th>
                <th scope="col">Quantidade</th>
            </tr>
            </thead>
            <tbody>
            @foreach($carregador as $carr)
                <tr class="">
                    <td class="fontSize-10px border1pxSolidRGBA0125"><strong>{{ $loop->iteration }}</strong></td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($carr->equipamento_tipo) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($carr->equipamento_especie) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($carr->armamento) }}</td>
                    <td class="border1pxSolidRGBA0125">.{{ $carr->calibre }}</td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($carr->modelo) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ $carr->volume_peso }}</td>
                    <td class="border1pxSolidRGBA0125">{{ $carr->quantidade }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <h5 class="titleInterno mt-5 padding02em background_e5e5e5" style="height: 42px">
        <span class="float-left mt-2">Algema <i class="mdi mdi-arrow-bottom-right"></i></span>
        <button class="btn btn-inverse-dark ml-5 float-right" id="expandirAlgema"><i class="mdi mdi-arrow-down-drop-circle"></i> Mostrar Lista</button>
    </h5>
    <div class="table-responsive-md displayNone" id="tableAlgema">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Nº de Série</th>
                <th scope="col">Espécie</th>
                <th scope="col">Tipo</th>
                <th scope="col">Fabricante</th>
                <th scope="col">Quantidade</th>
            </tr>
            </thead>
            <tbody>
            @foreach($algemas as $algema)
                <tr class="">
                    <td class="fontSize-10px border1pxSolidRGBA0125"><strong>{{ $loop->iteration }}</strong></td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($algema->n_serie) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($algema->equipamento_tipo) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($algema->equipamento_especie) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ $algema->fabNome }}</td>
                    <td class="border1pxSolidRGBA0125">{{ $algema->quantidade }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <h5 class="titleInterno mt-5 padding02em background_e5e5e5" style="height: 42px">
        <span class="float-left mt-2">Granada <i class="mdi mdi-arrow-bottom-right"></i></span>
        <button class="btn btn-inverse-dark ml-5 float-right" id="expandirGranada"><i class="mdi mdi-arrow-down-drop-circle"></i> Mostrar Lista</button>
    </h5>
    <div class="table-responsive-md displayNone" id="tableGranada">
        <div class="float-right w-25">
            <input type="text" name="filtroGranada" class="form-control" id="filtroGranada" placeholder="Digite um termo para filtrar a lista de equipamentos" autocomplete="off">
        </div>

        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Equipamento</th>
                <th scope="col">Espécie</th>
                <th scope="col">Especificação</th>
                <th scope="col">Fabricante</th>
                <th scope="col">Modelo</th>
                <th scope="col">Quantidade</th>
            </tr>
            </thead>
            <tbody id="tBodyGranada">
            @foreach($granadas as $granada)
                <tr class="">
                    <td class="fontSize-10px border1pxSolidRGBA0125"><strong>{{ $loop->iteration }}</strong></td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($granada->equipamento_tipo) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($granada->equipamento_especie) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($granada->equipamento_especificacao) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ $granada->fabNome }}</td>
                    <td class="border1pxSolidRGBA0125">{{ $granada->modelo }}</td>
                    <td class="border1pxSolidRGBA0125">{{ $granada->quantidade }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <h5 class="titleInterno mt-5 padding02em background_e5e5e5" style="height: 42px">
        <span class="float-left mt-2">EPI'S <i class="mdi mdi-arrow-bottom-right"></i></span>
        <button class="btn btn-inverse-dark ml-5 float-right" id="expandirEpi"><i class="mdi mdi-arrow-down-drop-circle"></i> Mostrar Lista</button>
    </h5>
    <div class="table-responsive-md displayNone" id="tableEpi">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Nº de Série</th>
                <th scope="col">Equipamento</th>
                <th scope="col">Fabricante</th>
                <th scope="col">Quantidade</th>
            </tr>
            </thead>
            <tbody>
            @foreach($epis as $epi)
                <tr class="">
                    <td class="fontSize-10px border1pxSolidRGBA0125"><strong>{{ $loop->iteration }}</strong></td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($epi->n_serie) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($epi->equipamento_especie) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($epi->fabNome) }}</td>
                    <td class="border1pxSolidRGBA0125">
                        @if($epi->quantidade != "undefined")
                            {{ $epi->quantidade }}
                            @else
                                X
                        @endif
                        </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

        <h5 class="titleInterno mt-5 padding02em background_e5e5e5" style="height: 42px">
            <span class="float-left mt-2">Coletes ({{ count($coletes) }}) <i class="mdi mdi-arrow-bottom-right"></i></span>
            <button class="btn btn-inverse-dark ml-5 float-right" id="expandirColete"><i class="mdi mdi-arrow-down-drop-circle"></i> Mostrar Lista</button>
        </h5>

        <div class="table-responsive-md displayNone" id="tableColete">
            <div class="float-right w-25">
                <input type="text" name="filtroColete" class="form-control" id="filtroColete" placeholder="Digite um termo para filtrar a lista de equipamentos" autocomplete="off">
            </div>

            <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Nº de Série</th>
                <th scope="col">Equipamento</th>
                <th scope="col">Fabricante</th>
                <th scope="col">Tamanho</th>
            </tr>
            </thead>
            <tbody id="tBodyColete">
            @foreach($coletes as $colete)
                <tr class="">
                    <td class="fontSize-10px border1pxSolidRGBA0125"><strong>{{ $loop->iteration }}</strong></td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($colete->n_serie) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($colete->equipamento_tipo) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($colete->fabNome) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($colete->tamanho) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <h5 class="titleInterno mt-5 padding02em background_e5e5e5" style="height: 42px">
        <span class="float-left mt-2">Espargidor <i class="mdi mdi-arrow-bottom-right"></i></span>
        <button class="btn btn-inverse-dark ml-5 float-right" id="expandirEspargidor"><i class="mdi mdi-arrow-down-drop-circle"></i> Mostrar Lista</button>
    </h5>
    <div class="table-responsive-md displayNone" id="tableEspargidor">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Tipo</th>
                <th scope="col">Modelo</th>
                <th scope="col">Fabricante</th>
                <th scope="col">Quantidade</th>
            </tr>
            </thead>
            <tbody>
            @foreach($espargidores as $esp)
                <tr class="">
                    <td class="fontSize-10px border1pxSolidRGBA0125"><strong>{{ $loop->iteration }}</strong></td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($esp->equipamento_tipo) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($esp->modelo) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($esp->fabNome) }}</td>
                    <td class="border1pxSolidRGBA0125">{{ strtoupper($esp->quantidade) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

@endsection
@push('css')
<link href="{{ asset('css/armaria/mapa/mapaEquipamento.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/armaria/mapaEquipamento.js') }}"></script>
@endpush