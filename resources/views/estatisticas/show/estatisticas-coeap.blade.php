@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-table"></i> Estatísticas do {{ config('app.sistema_estado') }} - Movimentações envolvendo Internos</h3>
        <hr>
        {{--<p>
            Tabelas com dados estatísticos relacionados aos internos ativos, atualizados em tempo real, relativos ao {{ config('app.sistema_estado') }}, a confiabilidade dos dados abaixo depende da inserção manual deles nos perfis de cada interno, portanto pode haver eventuais inconsistências.
        </p>--}}
    </div>


    @php

    $secretario = \App\Http\Controllers\Facade\FacadeController::isLogadoSecretario();

    $quantidade = 0;

    $maxValue = 0;

    $adjunto = \App\Http\Controllers\Facade\FacadeController::isLogadoAdjunto();

    $siglaUpLogado = \App\Http\Controllers\Facade\FacadeController::getLotacaoServidor(\Illuminate\Support\Facades\Auth::user()->cpf, "LOTAÇÃO NO RH");


    @endphp


    {{--@include('estatisticas.show._menuEstatisticas')--}}

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->


<div style="display: inline;">
<form action="{{ route('estatisticas.showCoeap') }}" method="post" enctype="multipart/form-data">
        {!! csrf_field() !!}
          {{--<div class="form-row">
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
        </div>--}}
        <p class="um">Data Inicial:</p>
        <input type="text" class="form-control" name="dataini" id="dataini" placeholder="Data Inicial - 00/00/0000" style="width:200px" required><br>
        <p class="um">Data Final:</p>
        <input type="text" class="form-control" name="datafin" id="datafin" placeholder="Data Final - 00/00/0000" style="width:200px" required><br>

        <button type="submit" id="btnSubmitForm" class="btn btn-primary"><i class="fas fa-redo"></i> Calcular Estatísticas</button>
</form>
<br>
{{--<form action="{{ route('estatistica.internoPdf') }}" method="post">
    {!! csrf_field() !!}
    <button type="submit" class="btn btn-primary"><i class="fas fa-print"></i> Imprimir Estatística</button>
</form>--}}
</div>


    <hr/>

    @if(!is_null($dataini))
    <h4>Informações estatísticas
        @if(is_null($unidadeSigla)) do {{ config('app.sistema_estado') }} @else da unidade: {{$unidadeSigla}}@endif
        @if(!is_null($pavilhaoNome)), pavilhão: {{$pavilhaoNome}}@endif
        @if(!is_null($alaNome)), ala: {{$alaNome}}@endif
        @if(!is_null($celaNome))cela: {{$celaNome}}@endif
        coletadas em {{ $dataHora }}.</h4>

    <div id="resultados">
        <br/>
        <h5>Total de Movimentações envolvendo Internos

            <label for="periodo">no período de: @if(is_null($dataini)) @else{{date('d-m-Y', strtotime(str_replace("/", "-", $dataini)))}} a {{date('d-m-Y', strtotime(str_replace("/", "-", \App\Classes\TData::subDias($datafin, 1))))}}@endif</label>

        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Unidade</th>
                <th scope="col">Qtde Entradas</th>
                <th scope="col">Qtde Saídas</th>
                <th scope="col">Óbitos</th>
                <th scope="col">Alvarás</th>
                <th scope="col">Fugas do Fechado</th>
                <th scope="col">Fugas do Semiaberto</th>
                <th scope="col">Fugas do Aberto</th>
                <th scope="col">Fugas do Provisório</th>
                <tbody>
                @foreach($unidades as $dado)
                <tr class="">
                    <td>{{ $dado->sigla }}</td>
                    <td>{{ $dado->qntEntradas }}</td>
                    <td>{{ $dado->qntSaidas }}</td>
                    <td>{{ $dado->qntObtos }}</td>
                    <td>{{ $dado->qntAlvara }}</td>
                    <td>{{ $dado->qntFugaFech }}</td>
                    <td>{{ $dado->qntFugaSemi }}</td>
                    <td>{{ $dado->qntFugaAberto }}</td>
                    <td>{{ $dado->qntFugaProv }}</td>
                </tr>
                @endforeach
                <tr class="">
                    <td><strong>TOTAIS</strong></td>
                    <td><strong>{{ $totalqntEntradas }}</strong></td>
                    <td><strong>{{ $totalqntSaidas }}</strong></td>
                    <td><strong>{{ $totalqntObtos }}</strong></td>
                    <td><strong>{{ $totalqntAlvara }}</strong></td>
                    <td><strong>{{ $totalqntFugaFech }}</strong></td>
                    <td><strong>{{ $totalqntFugaSemi }}</strong></td>
                    <td><strong>{{ $totalqntFugaAberto }}</strong></td>
                    <td><strong>{{ $totalqntFugaProv }}</strong></td>
                </tr>

            </table>
        </div>

        <br/>


        <br/>

    </div>
    @endif


    <a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
@endsection
@push('css')
    <link href="{{ asset('css/unidades/show.css') }}" rel="stylesheet">
@endpush
@push('script')
    <script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ asset('js/estatisticas/buscar.js') }}"></script>
@endpush