@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-table"></i> Estatísticas do {{ config('app.sistema_estado') }} - Tabelas</h3>
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
<form action="{{ route('estatisticas.regimePrisional') }}" method="post" enctype="multipart/form-data">
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
        <h5>Regime prisional
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>

        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">Classificação</th>
                    <th scope="col">Homens</th>
                    <th scope="col">Mulheres</th>
                    <th scope="col">Total</th>
                </tr>
                <tbody>
                <tr class="">
                    <td>Regime Fechado</td>
                    <td>{{ $qtdRegimeFechadoH }}</td>
                    <td>{{ $qtdRegimeFechadoM }}</td>
                    <td>{{ $qtdRegimeFechadoH + $qtdRegimeFechadoM }}</td>
                </tr>
                <tr class="">
                    <td>Regime Semiaberto</td>
                    <td>{{ $qtdRegimeSemiabertoH }}</td>
                    <td>{{ $qtdRegimeSemiabertoM }}</td>
                    <td>{{ $qtdRegimeSemiabertoH + $qtdRegimeSemiabertoM }}</td>
                </tr>
                <tr class="">
                    <td>Regime Aberto</td>
                    <td>{{ $qtdRegimeAbertoH }}</td>
                    <td>{{ $qtdRegimeAbertoM }}</td>
                    <td>{{ $qtdRegimeAbertoH + $qtdRegimeAbertoM }}</td>
                </tr>
                @if($qtdRegimeNIH + $qtdRegimeNIM != 0)
                    <tr class="">
                        <td>Regime Não Informado no SIAPEN</td>
                        <td>{{ $qtdRegimeNIH }}</td>
                        <td>{{ $qtdRegimeNIM }}</td>
                        <td>{{ $qtdRegimeNIH + $qtdRegimeNIM }}</td>
                    </tr>
                @endif
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