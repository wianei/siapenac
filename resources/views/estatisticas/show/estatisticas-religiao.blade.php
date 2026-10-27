@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-table"></i> Estatísticas do {{ config('app.sistema_estado') }}- Tabelas</h3>
        <hr>
        <p>
            Tabelas com dados estatísticos relacionados aos internos ativos, atualizados em tempo real, relativos ao Sistema Penitenciário da BA, a confiabilidade dos dados abaixo depende da inserção manual deles nos perfis de cada interno, portanto pode haver eventuais inconsistências.
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
<form action="{{ route('estatisticas.religiao') }}" method="post" enctype="multipart/form-data">
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
        <h5>Religião
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
                    <td>Adventistas</td>
                    <td>{{ $qtdAdventistaH }}</td>
                    <td>{{ $qtdAdventistaM  }}</td>
                    <td>{{ $qtdAdventistaH + $qtdAdventistaM }}</td>
                </tr>
                <tr class="">
                    <td>Budistas</td>
                    <td>{{ $qtdBudistaH }}</td>
                    <td>{{ $qtdBudistaM  }}</td>
                    <td>{{ $qtdBudistaH + $qtdBudistaM }}</td>
                </tr>
                <tr class="">
                    <td>Católicos</td>
                    <td>{{ $qtdCatolicaH }}</td>
                    <td>{{ $qtdCatolicaM  }}</td>
                    <td>{{ $qtdCatolicaH + $qtdCatolicaM }}</td>
                </tr>
                <tr class="">
                    <td>Espíritas</td>
                    <td>{{ $qtdEspiritaH }}</td>
                    <td>{{ $qtdEspiritaM  }}</td>
                    <td>{{ $qtdEspiritaH + $qtdEspiritaM }}</td>
                </tr>
                <tr class="">
                    <td>Evangélicos</td>
                    <td>{{ $qtdEvangelicaH }}</td>
                    <td>{{ $qtdEvangelicaM  }}</td>
                    <td>{{ $qtdEvangelicaH + $qtdEvangelicaM }}</td>
                </tr>
                <tr class="">
                    <td>Judeus</td>
                    <td>{{ $qtdJudeuH }}</td>
                    <td>{{ $qtdJudeuM  }}</td>
                    <td>{{ $qtdJudeuH + $qtdJudeuM }}</td>
                </tr>
                <tr class="">
                    <td>Matriz Africana</td>
                    <td>{{ $qtdReligiaoMAfricanaM }}</td>
                    <td>{{ $qtdReligiaoMAfricanaF  }}</td>
                    <td>{{ $qtdReligiaoMAfricanaM + $qtdReligiaoMAfricanaF }}</td>
                </tr>
                <tr class="">
                    <td>Mórmons</td>
                    <td>{{ $qtdMormonH }}</td>
                    <td>{{ $qtdMormonM  }}</td>
                    <td>{{ $qtdMormonH + $qtdMormonM }}</td>
                </tr>
                <tr class="">
                    <td>Muçulmanos</td>
                    <td>{{ $qtdMulcumanaH }}</td>
                    <td>{{ $qtdMulcumanaM  }}</td>
                    <td>{{ $qtdMulcumanaH + $qtdMulcumanaM }}</td>
                </tr>
                <tr class="">
                    <td>Protestantes</td>
                    <td>{{ $qtdProtestanteH }}</td>
                    <td>{{ $qtdProtestanteM  }}</td>
                    <td>{{ $qtdProtestanteH + $qtdProtestanteM }}</td>
                </tr>
                <tr class="">
                    <td>Agnósticos</td>
                    <td>{{ $qtdAgnosticoH }}</td>
                    <td>{{ $qtdAgnosticoM  }}</td>
                    <td>{{ $qtdAgnosticoH + $qtdAgnosticoM }}</td>
                </tr>
                <tr class="">
                    <td>Ateus</td>
                    <td>{{ $qtdAteuH }}</td>
                    <td>{{ $qtdAteuM  }}</td>
                    <td>{{ $qtdAteuH + $qtdAteuM }}</td>
                </tr>
                <tr class="">
                    <td>Sem Religião</td>
                    <td>{{ $qtdSemReligiaoH }}</td>
                    <td>{{ $qtdSemReligiaoM  }}</td>
                    <td>{{ $qtdSemReligiaoH + $qtdSemReligiaoM }}</td>
                </tr>
                <tr class="">
                    <td>Outras</td>
                    <td>{{ $qtdOutrasH }}</td>
                    <td>{{ $qtdOutrasM  }}</td>
                    <td>{{ $qtdOutrasH + $qtdOutrasM }}</td>
                </tr>
                @if($qtdReligiaoNIH + $qtdReligiaoNIM != 0)
                    <tr class="">
                        <td>Não informado no SIAPEN</td>
                        <td>{{ $qtdReligiaoNIH }}</td>
                        <td>{{ $qtdReligiaoNIM  }}</td>
                        <td>{{ $qtdReligiaoNIH + $qtdReligiaoNIM }}</td>
                    </tr>
                @endif
            </table>
        </div>




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