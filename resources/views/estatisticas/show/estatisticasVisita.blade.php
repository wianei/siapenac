@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-table"></i> Estatísticas do {{ config('app.sistema_estado') }} - Tabelas</h3>
        <hr>
        <p>
            Tabelas com dados estatísticos relacionados às visitas ativas, atualizados em tempo real, relativos ao {{ config('app.sistema_estado') }}, a confiabilidade dos dados abaixo depende da inserção manual deles nos perfis de cada interno, portanto pode haver eventuais inconsistências.
        </p>
    </div>

    @php

    $secretario = \App\Http\Controllers\Facade\FacadeController::isLogadoSecretario();

    $adjunto = \App\Http\Controllers\Facade\FacadeController::isLogadoAdjunto();

    $siglaUpLogado = \App\Http\Controllers\Facade\FacadeController::getLotacaoServidor(\Illuminate\Support\Facades\Auth::user()->cpf, "LOTAÇÃO NO RH");

    @endphp

    <form action="{{ route('estatisticas.showVisita') }}" method="post" enctype="multipart/form-data">
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

    <hr/>

    <h4>Informações estatísticas
        @if(is_null($unidadeSigla)) do {{ config('app.sistema_estado') }} @else da unidade: {{$unidadeSigla}}@endif
        @if(!is_null($pavilhaoNome)), pavilhão: {{$pavilhaoNome}}@endif
        @if(!is_null($alaNome)), ala: {{$alaNome}}@endif
        @if(!is_null($celaNome))cela: {{$celaNome}}@endif
        coletadas em {{ $dataHora }}.</h4>

    <div id="resultados">

        <br/>
        <h5>Sexo
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Classificação</th>
                <th scope="col">Quantidade</th>
                <tbody>
                <tr class="">
                    <td>Homens</td>
                    <td>{{ $qntM }}</td>
                </tr>
                <tr class="">
                    <td>Mulheres</td>
                    <td>{{ $qntF }}</td>
                </tr>
                <tr class="">
                    <td>Total</td>
                    <td>{{ $qntM + $qntF }}</td>
                </tr>
            </table>
        </div>


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
                    <td>{{ $idadesM[1] }}</td>
                    <td>{{ $idadesF[1] }}</td>
                    <td>{{ $idadesM[1] + $idadesF[1] }}</td>
                </tr>
                <tr class="">
                    <td>18 a 24</td>
                    <td>{{ $idadesM[2] }}</td>
                    <td>{{ $idadesF[2] }}</td>
                    <td>{{ $idadesM[2] + $idadesF[2] }}</td>
                </tr>
                <tr class="">
                    <td>25 a 29</td>
                    <td>{{ $idadesM[3] }}</td>
                    <td>{{ $idadesF[3] }}</td>
                    <td>{{ $idadesM[3] + $idadesF[3] }}</td>
                </tr>
                <tr class="">
                    <td>30 a 34</td>
                    <td>{{ $idadesM[4] }}</td>
                    <td>{{ $idadesF[4] }}</td>
                    <td>{{ $idadesM[4] + $idadesF[4] }}</td>
                </tr>
                <tr class="">
                    <td>35 a 45</td>
                    <td>{{ $idadesM[5] }}</td>
                    <td>{{ $idadesF[5] }}</td>
                    <td>{{ $idadesM[5] + $idadesF[5] }}</td>
                </tr>
                <tr class="">
                    <td>46 a 60</td>
                    <td>{{ $idadesM[6] }}</td>
                    <td>{{ $idadesF[6] }}</td>
                    <td>{{ $idadesM[6] + $idadesF[6] }}</td>
                </tr>
                <tr class="">
                    <td>61 a 70</td>
                    <td>{{ $idadesM[7] }}</td>
                    <td>{{ $idadesF[7] }}</td>
                    <td>{{ $idadesM[7] + $idadesF[7] }}</td>
                </tr>            <tr class="">
                    <td>Mais de 70</td>
                    <td>{{ $idadesM[8] }}</td>
                    <td>{{ $idadesF[8] }}</td>
                    <td>{{ $idadesM[8] + $idadesF[8] }}</td>
                </tr>
                @if($idadesM[0] + $idadesF[0] != 0)
                <tr class="">
                    <td>Não Informado no SIAPEN</td>
                    <td>{{ $idadesM[0] }}</td>
                    <td>{{ $idadesF[0] }}</td>
                    <td>{{ $idadesM[0] + $idadesF[0] }}</td>
                </tr>
                @endif
            </table>
        </div>


        <br/>
        <h5>Quantidade de internos que recebem visita
            <i class="mdi mdi-arrow-bottom-right"></i>
        </h5>
        <div id="internosvisitados" class="table-responsive-md">
            <table class="table">
                <thead class="background_30516f color_FFF">
                <th scope="col">Unidade Prisional</th>
                <th scope="col">Internos</th>
                <th scope="col">Internas</th>
                <th scope="col">Total</th>
                <tbody id="qntvisitaativa">

                </tbody>
            </table>
        </div>



        @if($siglaUpLogado == config('app.TI') || $siglaUpLogado == "SI" || $secretario || $adjunto)
        {{--<br/>
        <h5>Faccionados e não-faccionados
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
                        <td>Faccionados</td>
                        <td>{{ $qtdHA - $qtdFaccaoNIH }}</td>
                        <td>{{ $qtdMA - $qtdFaccaoNIM }}</td>
                        <td>{{ ($qtdHA+$qtdMA) - ($qtdFaccaoNIH+$qtdFaccaoNIM) }}</td>
                    </tr>
                    @if($qtdFaccaoNIH+$qtdFaccaoNIM != 0)
                    <tr class="">
                        <td>Não-Faccionados ou Não Informado no SIAPEN</td>
                        <td>{{ $qtdFaccaoNIH }}</td>
                        <td>{{ $qtdFaccaoNIM }}</td>
                        <td>{{ ($qtdFaccaoNIH+$qtdFaccaoNIM) }}</td>
                    </tr>
                    @endif
            </table>
        </div>

        <br/>
        <h5>Facção criminosa
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
                @foreach($faccoes as $faccao)
                    @if($faccao->qtd > 0)
                        <tr class="">
                            <td>{{ $faccao->nome }} ({{ $faccao->sigla }})</td>
                            <td>{{ $faccao->qtdH }}</td>
                            <td>{{ $faccao->qtdM }}</td>
                            <td>{{ $faccao->qtd }}</td>
                        </tr>
                    @endif
                @endforeach
            </table>
        </div>

        <br/>
        <h5>Periculosidade
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
                        <td>Periculosidade Alta</td>
                        <td>{{ $periculosidadeAltaH }}</td>
                        <td>{{ $periculosidadeAltaM }}</td>
                        <td>{{ $periculosidadeAltaH + $periculosidadeAltaM }}</td>
                    </tr>
                    <tr class="">
                        <td>Periculosidade Média</td>
                        <td>{{ $periculosidadeMediaH }}</td>
                        <td>{{ $periculosidadeMediaM }}</td>
                        <td>{{ $periculosidadeMediaH + $periculosidadeAltaM }}</td>
                    </tr>
                    <tr class="">
                        <td>Periculosidade Baixa</td>
                        <td>{{ $periculosidadeBaixaH }}</td>
                        <td>{{ $periculosidadeBaixaM }}</td>
                        <td>{{ $periculosidadeBaixaH + $periculosidadeBaixaM }}</td>
                    </tr>
                    @if($periculosidadeNIH + $periculosidadeNIM != 0)
                    <tr class="">
                        <td>Periculosidade Não Informada no SIAPEN</td>
                        <td>{{ $periculosidadeNIH }}</td>
                        <td>{{ $periculosidadeNIM }}</td>
                        <td>{{ $periculosidadeNIH + $periculosidadeNIM }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>--}}

        <br/>
        <h5>Visitam Liderança
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
                    <td>Visitam Lideranças</td>
                    <td>{{ $visitasLiderancaSimM }}</td>
                    <td>{{ $visitasLiderancaSimF  }}</td>
                    <td>{{ $visitasLiderancaSimF + $visitasLiderancaSimM }}</td>
                </tr>
                <tr class="">
                    <td>Não Visitam Lideranças</td>
                    <td>{{ $visitasLiderancaNaoM }}</td>
                    <td>{{ $visitasLiderancaNaoF  }}</td>
                    <td>{{ $visitasLiderancaNaoM + $visitasLiderancaNaoF }}</td>
                </tr>
                @if($visitasLiderancaNaoInformadoM + $visitasLiderancaNaoInformadoF != 0)
                <tr class="">
                    <td>Não verificado no SIAPEN</td>
                    <td>{{ $visitasLiderancaNaoInformadoM }}</td>
                    <td>{{ $visitasLiderancaNaoInformadoF  }}</td>
                    <td>{{ $visitasLiderancaNaoInformadoM + $visitasLiderancaNaoInformadoF }}</td>
                </tr>
                @endif
            </table>
        </div>
        @endif
    </div>

    <a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
@endsection
@push('css')
    <link href="{{ asset('css/unidades/show.css') }}" rel="stylesheet">
@endpush
@push('script')
    <script src="{{ asset('js/estatisticas/buscar.js') }}"></script>
@endpush