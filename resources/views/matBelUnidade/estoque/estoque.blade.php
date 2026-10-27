@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-3">
        <h3><i class="mdi mdi-map-marker-radius"></i>Mapa de Localização dos Equipamentos</h3>
        <hr>
        <p>
            Quadro informativo da localização dos equipamentos distribuídos nas unidades prisionais da {{ config('app.uf') }}.
        </p>
    </div>


    @if($dados != false)
        <h5 class="titleInterno mt-5">Arma de Fogo ({{ count($armaFogo) }})<i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div class="table-responsive-md" id="tableArmaFogo">
            <table class="table mb-3">
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
                <tbody>
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

        <h5 class="titleInterno mt-5">Dispositivo Elétrico Incapacitante ({{ count($dei) }})<i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div class="table-responsive-md" id="tableArmaFogo">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">#</th>
                    <th scope="col">Nº de Série</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Fabricante</th>
                    <th scope="col">Modelo</th>
                </tr>
                </thead>
                <tbody>
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

        <h5 class="titleInterno mt-5">Munição<i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div class="table-responsive-md" id="tableArmaFogo">
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
                <tbody>
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

        <h5 class="titleInterno mt-5">Carregador<i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div class="table-responsive-md" id="tableArmaFogo">
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

        <h5 class="titleInterno mt-5">Algema<i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div class="table-responsive-md" id="tableArmaFogo">
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

        <h5 class="titleInterno mt-5">Granada<i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div class="table-responsive-md" id="tableArmaFogo">
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
                <tbody>
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

        <h5 class="titleInterno mt-5">EPI'S<i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div class="table-responsive-md" id="tableArmaFogo">
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

        <h5 class="titleInterno mt-5">Coletes ({{ count($armaFogo) }})<i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div class="table-responsive-md" id="tableArmaFogo">
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
                <tbody>
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

        <h5 class="titleInterno mt-5">Espargidor<i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div class="table-responsive-md" id="tableArmaFogo">
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

{{--@push('script')--}}
{{--<script src="{{ asset('js/armaria/cautela.js') }}"></script>--}}
{{--@endpush--}}