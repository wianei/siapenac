@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-table"></i> Estatísticas do {{ config('app.sistema_estado') }}- Tabelas</h3>
        <hr>
        <p>
            Tabelas com dados estatísticos relacionados aos internos ativos, atualizados em tempo real, relativos ao {{ config('app.sistema_estado') }}, a confiabilidade dos dados abaixo depende da inserção manual deles nos perfis de cada interno, portanto pode haver eventuais inconsistências.
        </p>
    </div>

    @php

    $secretario = \App\Http\Controllers\Facade\FacadeController::isLogadoSecretario();

    $quantidade = 0;

    $maxValue = 0;

    $adjunto = \App\Http\Controllers\Facade\FacadeController::isLogadoAdjunto();

    $siglaUpLogado = \App\Http\Controllers\Facade\FacadeController::getLotacaoServidor(\Illuminate\Support\Facades\Auth::user()->cpf, "LOTAÇÃO NO RH");

    @endphp

    @include('estatisticas.show._menuEstatisticas')


<div style="display: inline;">
<form action="{{ route('estatisticas.regimeUnidade') }}" method="post" enctype="multipart/form-data">
        {!! csrf_field() !!}
          <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="unidade_prisional">Unidade Prisional</label>
                <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">
                    <option value="">
                        Todas
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
        <button type="submit" id="btnSubmitForm" class="btn btn-primary"><i class="fas fa-redo"></i> Recalcular Estatísticas</button>
</form>
<br>
<br>
<form action="{{ route('estatistica.internoPdf') }}" method="post">
    {!! csrf_field() !!}
    <button type="submit" class="btn btn-primary"><i class="fas fa-print"></i> Imprimir Estatística</button>
</form>
</div>


    <hr/>


    <h4>Informações estatísticas
        @if(is_null($unidadeSigla)) do {{ config('app.sistema_estado') }} @else da unidade: {{$unidadeSigla}}@endif
        @if(!is_null($pavilhaoNome)), pavilhão: {{$pavilhaoNome}}@endif
        @if(!is_null($alaNome)), ala: {{$alaNome}}@endif
        @if(!is_null($celaNome))cela: {{$celaNome}}@endif
        coletadas em {{ $dataHora }}.</h4>
    <br>

    <div id="resultados">
        <h5>Regime por Unidade
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Unidade</th>
                <th scope="col">ABERTO</th>
                <th scope="col">SEMIABERTO</th>
                <th scope="col">FECHADO</th>
                <th scope="col">PROVISÓRIO</th>
                <th scope="col">SENTENCIADO</th>
                <th scope="col">MED. DE SEG.</th>
                <th scope="col">PROVI. EM MED. SEG.</th>
                <th scope="col">NÃO DETECTADO</th>
                <th scope="col">TOTAL</th>
                <tbody>
                @php
                $totalaberto =
                $totalsemiaberto =
                $totalfechado =
                $totalprovisorio =
                $totalsentenciado =
                $totalmedida_seg =
                $totalmedida_seg_provisorio =
                $totalinconsistente =
                $totalgeral = 0;
                @endphp
                @foreach($detalhesUnidades as $unidade)
                    <tr class="">
                        <td>{{$unidade->sigla}}</td>
                        <td>
                            {{$unidade->aberto}}
                            @php
                            $totalaberto = $totalaberto + $unidade->aberto;
                            @endphp
                        </td>
                        <td>
                            {{$unidade->semiaberto}}
                            @php
                            $totalsemiaberto = $totalsemiaberto + $unidade->semiaberto;
                            @endphp
                        </td>
                        <td>
                            {{$unidade->fechado}}
                            @php
                            $totalfechado = $totalfechado + $unidade->fechado;
                            @endphp
                        </td>
                        <td>
                            {{$unidade->provisorio}}
                            @php
                            $totalprovisorio = $totalprovisorio + $unidade->provisorio;
                            @endphp
                        </td>
                        <td>
                            {{$unidade->sentenciado}}
                            @php
                            $totalsentenciado = $totalsentenciado + $unidade->sentenciado;
                            @endphp
                        </td>
                        <td>
                            {{$unidade->medida_seg}}
                            @php
                            $totalmedida_seg = $totalmedida_seg + $unidade->medida_seg;
                            @endphp
                        </td>
                        <td>
                            {{$unidade->medida_seg_provisorio}}
                            @php
                            $totalmedida_seg_provisorio = $totalmedida_seg_provisorio + $unidade->medida_seg_provisorio;
                            @endphp
                        </td>
                        <td>
                            {{$unidade->inconsistente}}
                            @php
                            $totalinconsistente = $totalinconsistente + $unidade->inconsistente;
                            @endphp
                        </td>
                        <td>
                            {{$unidade->total}}
                            @php
                            $totalgeral = $totalgeral + $unidade->total;
                            @endphp
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td>TOTAL GERAL</td>
                    <td>{{$totalaberto}}</td>
                    <td>{{$totalsemiaberto}}</td>
                    <td>{{$totalfechado}}</td>
                    <td>{{$totalprovisorio}}</td>
                    <td>{{$totalsentenciado}}</td>
                    <td>{{$totalmedida_seg}}</td>
                    <td>{{$totalmedida_seg_provisorio}}</td>
                    <td>{{$totalinconsistente}}</td>
                    <td>{{$totalgeral}}</td>
                </tr>
            </table>
        </div>

        <br/>


    </div>


    <a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
@endsection
@push('css')
    <link href="{{ asset('css/unidades/show.css') }}" rel="stylesheet">
@endpush
@push('script')
    <script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ asset('js/estatisticas/buscar.js') }}"></script>
@endpush