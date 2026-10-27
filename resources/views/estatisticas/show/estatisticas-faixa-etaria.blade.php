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
<form action="{{ route('estatisticas.faixaEtaria') }}" method="post" enctype="multipart/form-data">
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

    <div id="resultados">
        <br/>
        <h5>Faixa etária
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Homens</th>
                <th scope="col">Mulheres</th>
                <th scope="col">Total</th>
                <tbody>
                <tr class="">
                    <td>0 a 17</td>
                    <td>{{ $faixaEtaria_zero_a_dezesseteH }}</td>
                    <td>{{ $faixaEtaria_zero_a_dezesseteM }}</td>
                    <td>{{ $faixaEtaria_zero_a_dezesseteH + $faixaEtaria_zero_a_dezesseteM }}</td>
                </tr>
                <tr class="">
                    <td>18 a 24</td>
                    <td>{{ $faixaEtaria_dezoito_a_vinteequatroH }}</td>
                    <td>{{ $faixaEtaria_dezoito_a_vinteequatroM }}</td>
                    <td>{{ $faixaEtaria_dezoito_a_vinteequatroH + $faixaEtaria_dezoito_a_vinteequatroM }}</td>
                </tr>
                <tr class="">
                    <td>25 a 29</td>
                    <td>{{ $faixaEtaria_vinteecinco_a_vinteenoveH }}</td>
                    <td>{{ $faixaEtaria_vinteecinco_a_vinteenoveM }}</td>
                    <td>{{ $faixaEtaria_vinteecinco_a_vinteenoveH + $faixaEtaria_vinteecinco_a_vinteenoveM }}</td>
                </tr>
                <tr class="">
                    <td>30 a 34</td>
                    <td>{{ $faixaEtaria_trinta_a_trintaequatroH }}</td>
                    <td>{{ $faixaEtaria_trinta_a_trintaequatroM }}</td>
                    <td>{{ $faixaEtaria_trinta_a_trintaequatroH + $faixaEtaria_trinta_a_trintaequatroM }}</td>
                </tr>
                <tr class="">
                    <td>35 a 45</td>
                    <td>{{ $faixaEtaria_trintaecinco_a_quarentaecincoH }}</td>
                    <td>{{ $faixaEtaria_trintaecinco_a_quarentaecincoM }}</td>
                    <td>{{ $faixaEtaria_trintaecinco_a_quarentaecincoH + $faixaEtaria_trintaecinco_a_quarentaecincoM }}</td>
                </tr>
                <tr class="">
                    <td>46 a 60</td>
                    <td>{{ $faixaEtaria_quarentaeseis_a_sessentaH }}</td>
                    <td>{{ $faixaEtaria_quarentaeseis_a_sessentaM }}</td>
                    <td>{{ $faixaEtaria_quarentaeseis_a_sessentaH + $faixaEtaria_quarentaeseis_a_sessentaM }}</td>
                </tr>
                <tr class="">
                    <td>61 a 70</td>
                    <td>{{ $faixaEtaria_sessentaeum_a_setentaH }}</td>
                    <td>{{ $faixaEtaria_sessentaeum_a_setentaM }}</td>
                    <td>{{ $faixaEtaria_sessentaeum_a_setentaH + $faixaEtaria_sessentaeum_a_setentaM }}</td>
                </tr>            <tr class="">
                    <td>Mais de 70</td>
                    <td>{{ $faixaEtaria_maisDeSetentaH }}</td>
                    <td>{{ $faixaEtaria_maisDeSetentaM }}</td>
                    <td>{{ $faixaEtaria_maisDeSetentaH + $faixaEtaria_maisDeSetentaM }}</td>
                </tr>
                @if($faixaEtaria_naoInformadaH + $faixaEtaria_naoInformadaM != 0)
                    <tr class="">
                        <td>Não Informado no SIAPEN</td>
                        <td>{{ $faixaEtaria_naoInformadaH }}</td>
                        <td>{{ $faixaEtaria_naoInformadaM }}</td>
                        <td>{{ $faixaEtaria_naoInformadaH + $faixaEtaria_naoInformadaM }}</td>
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