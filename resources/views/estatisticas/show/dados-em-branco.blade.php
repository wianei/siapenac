@extends('template.tema_geral.theme')

@section('content')
    <style>
        tr:nth-child(even) {
            background-color: #ececec !important;
        }

        .dadoRetornado{
            font-weight: bold;
        }
    </style>

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-table"></i> Estatísticas de Dados em Branco</h3>
        <hr>
        <p>
            Estatísticas de dados em branco atualmente no sistema SIAPEN-{{ config('app.uf') }}.
        </p>
    </div>

    <hr/>

    <div class="table-responsive">
        <table class="table">
            <thead class="background_30516f color_FFF">
                <tr>
                <th scope="col">Unidade</th>
                <th scope="col">Fotos</th>
                <th scope="col">Sentenciado ou Provisório</th>
                <th scope="col">Sexo</th>
                <th scope="col">Dt Nasc</th>
                <th scope="col">Mãe</th>
                <th scope="col">Pai</th>
                <th scope="col">Naturalidade - País</th>
                <th scope="col">Naturalidade - UF</th>
                <th scope="col">Naturalidade - Cidade</th>
                <th scope="col">Estado Civil</th>
                <th scope="col">Orientação Sexual</th>
                <th scope="col">Religião</th>
                <th scope="col">Escolaridade</th>
                <th scope="col">Cor da Pele</th>
                <th scope="col">Cor dos Olhos</th>
                <th scope="col">Cor dos Cabelos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($unidades as $unidade)
                <tr>
                    <td>{{ $unidade->sigla }}</td>
                    <td>{{ EstatisticasService::qtdSemFotos($unidade->id) }}</td>
                    <td>{{ EstatisticasService::qtdSemSentenciadoProvisorio($unidade->id) }}</td>
                    <td>{{ EstatisticasService::qtdSemSexo($unidade->id) }}</td>
                    <td>{{ EstatisticasService::qtdSemDataNascimento($unidade->id) }}</td>
                    <td>{{ EstatisticasService::qtdSemMae($unidade->id) }}</td>
                    <td>{{ EstatisticasService::qtdSemPai($unidade->id) }}</td>
                    <td>{{ EstatisticasService::qtdSemNaturalidadePais($unidade->id) }}</td>
                    <td>{{ EstatisticasService::qtdSemNaturalidadeEstado($unidade->id) }}</td>
                    <td>{{ EstatisticasService::qtdSemNaturalidadeCidade($unidade->id) }}</td>
                    <td>{{ EstatisticasService::qtdSemEstadoCivil($unidade->id) }}</td>
                    <td>{{ EstatisticasService::qtdSemOrientacaoSexual($unidade->id) }}</td>
                    <td>{{ EstatisticasService::qtdSemReligiao($unidade->id) }}</td>
                    <td>{{ EstatisticasService::qtdSemEscolaridade($unidade->id) }}</td>
                    <td>{{ EstatisticasService::qtdSemCorPele($unidade->id) }}</td>
                    <td>{{ EstatisticasService::qtdSemCorOlhos($unidade->id) }}</td>
                    <td>{{ EstatisticasService::qtdSemCorCabelos($unidade->id) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    <a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
@endsection
@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{ asset('css/unidades/show.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/estatisticas/unidade.js') }}"></script>
@endpush