@extends('template.tema_geral.theme')

@section('content')
    <style type="text/css">

        .tableT3 tr:nth-child(even) {background: #FFF !important;}
        .tableT3 tr:nth-child(odd) {background: #f2f8f9 !important;}

        .tableT2 tr:nth-child(even) {background: #FFF !important;}
        .tableT2 tr:nth-child(odd) {background: #f2f8f9 !important;}

        .tableT1 tr:nth-child(even) {background: #FFF !important;}
        .tableT1 tr:nth-child(odd) {background: #f2f8f9 !important;}

        @media print {
            .sidebar, .navbar, .formEscolhaLotacao {
                display: none !important;
            }
            .headTable{
                background-color: #000000 !important;
                -webkit-print-color-adjust: exact !important;
            }
        }


    </style>
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-playlist-check"></i> Lista de Escolhas</h3>
        <hr>
        <p>
            Lista a escolha dos servidores separados por unidade prisional.
        </p>
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg)  !!}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    <form action="" method="get" class="formEscolhaLotacao">
        <div class="form-row" id="divLotacoes">

            <div class="form-group col-sm">
                <label for="lotacao"> Lotação</label>
                <select id="lotacao" name="lotacao" class="form-control tipoEscolha">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}">
                            {{ $unidade->nome }} - {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <button type="submit" id="btnSalvar" class="btn btn-primary"><i class="fas fa-check"></i> Selecionar Lotação</button>

    </form>

    @if($dados != false)

    <hr>
    <a href="{{ route('concursoRemocao.listaEscolhasPdf', compact('unidadeSelecionada')) }}" class="btn btn-primary float-right"><i class="mdi mdi-file-pdf"></i> Gerar PDF da lista</a>
    @if(count($dadosT1) > 0)
    <h4 class="mt-3">Unidade: {{ $unidadeNome }}</h4>
    <h6 class="mt-3">Vagas 1ª Turma: {{ $vagasT1 }}</h6>
    <div class="table-responsive-md mt-3">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF headTable">
            <tr class="">
                {{--<th scope="col">#</th>--}}
                <th scope="col">#</th>
                <th scope="col">Servidor</th>
                <th scope="col">Lot. Atual</th>
                <th scope="col">1ª Opção</th>
                <th scope="col">2ª Opção</th>
                <th scope="col">3ª Opção</th>
                <th class="text-center" scope="col">Ações</th>
            </tr>
            </thead>
            <tbody class="tableT3">
            @foreach($dadosT1 as $dt1)
                <tr class="">
                    <td class="border1pxSolidRGBA0125 fontSize-11px">{{ $loop->iteration }}º</td>
                    <td class="border1pxSolidRGBA0125 fontSize-11px">{{ $dt1->nome }}<br>Data de Exercício: {{ $dt1->data_exercicioBr }}</td>
                    <td class="border1pxSolidRGBA0125 fontSize-11px">{{ $dt1->lotacaoAtual }}</td>
                    <td class="border1pxSolidRGBA0125 fontSize-11px @if($unidadeSelecionada == $dt1->opcao1Id) background_81c784 @endif">
                        @if($unidadeSelecionada == $dt1->opcao1Id)
                            {{ $dt1->opcao1 }}
                            @else
                            <span class="color_C5C5C5">{{ $dt1->opcao1 }}</span>
                        @endif
                    </td>
                    <td class="border1pxSolidRGBA0125 fontSize-11px @if($unidadeSelecionada == $dt1->opcao2Id) background_ffffc4 @endif">
                        @if($unidadeSelecionada == $dt1->opcao2Id)
                            {{ $dt1->opcao2 }}
                        @else
                            <span class="color_C5C5C5">{{ $dt1->opcao2 }}</span>

                        @endif

                    </td>
                    <td class="border1pxSolidRGBA0125 fontSize-11px @if($unidadeSelecionada == $dt1->opcao3Id) background_ffffc4 @endif">
                        @if($unidadeSelecionada == $dt1->opcao3Id)
                            {{ $dt1->opcao3 }}
                        @else
                            <span class="color_C5C5C5">{{ $dt1->opcao3 }}</span>

                        @endif


                    </td>
                    <td class="border1pxSolidRGBA0125 btn-acoes color_01579b text-center">

                        <a href="{{ route("concursoRemocao.definirLotacaoServidor", $dt1->id) }}" data-toggle="tooltip" data-placement="top" title="Definir Lotação"> <i class="mdi mdi-arrow-right-bold"></i></a>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(count($dadosT2) > 0)
    <h4 class="mt-3">Unidade: {{ $unidadeNome }}</h4>
    <h6 class="mt-3">Vagas 2ª Turma: {{ $vagasT2 }}</h6>
    <div class="table-responsive-md mt-3">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                {{--<th scope="col">#</th>--}}
                <th scope="col">#</th>
                <th scope="col">Servidor</th>
                <th scope="col">Lot. Atual</th>
                <th scope="col">1ª Opção</th>
                <th scope="col">2ª Opção</th>
                <th scope="col">3ª Opção</th>
                <th class="text-center" scope="col">Ações</th>
            </tr>
            </thead>
            <tbody class="tableT2">
            @foreach($dadosT2 as $dt2)
                <tr class="">
                    <td class="border1pxSolidRGBA0125 fontSize-11px">{{  $loop->iteration }}º</td>
                    <td class="border1pxSolidRGBA0125 fontSize-11px">{{ $dt2->nome }}<br>Data de Exercício: {{ $dt2->data_exercicioBr }}</td>
                    <td class="border1pxSolidRGBA0125 fontSize-11px">{{ $dt2->lotacaoAtual }}</td>
                    <td class="border1pxSolidRGBA0125 fontSize-11px @if($unidadeSelecionada == $dt2->opcao1Id) background_81c784 @endif">
                        @if($unidadeSelecionada == $dt2->opcao1Id)
                            {{ $dt2->opcao1 }}
                            @else
                            <span class="color_C5C5C5">{{ $dt2->opcao1 }}</span>
                        @endif
                    </td>
                    <td class="border1pxSolidRGBA0125 fontSize-11px @if($unidadeSelecionada == $dt2->opcao2Id) background_ffffc4 @endif">
                        @if($unidadeSelecionada == $dt2->opcao2Id)
                            {{ $dt2->opcao2 }}
                        @else
                            <span class="color_C5C5C5">{{ $dt2->opcao2 }}</span>

                        @endif

                    </td>
                    <td class="border1pxSolidRGBA0125 fontSize-11px @if($unidadeSelecionada == $dt2->opcao3Id) background_ffffc4 @endif">
                        @if($unidadeSelecionada == $dt2->opcao3Id)
                            {{ $dt2->opcao3 }}
                        @else
                            <span class="color_C5C5C5">{{ $dt2->opcao3 }}</span>

                        @endif

                    </td>
                    <td class="border1pxSolidRGBA0125 btn-acoes color_01579b text-center">

                        <a href="{{ route("concursoRemocao.definirLotacaoServidor", $dt2->id) }}" data-toggle="tooltip" data-placement="top" title="Definir Lotação"> <i class="mdi mdi-arrow-right-bold"></i></a>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(count($dadosT3) > 0)
    <h4 class="mt-3">Unidade: {{ $unidadeNome }}</h4>
    <h6 class="mt-3">Vagas 3ª Turma: {{ $vagasT3 }}</h6>
    <div class="table-responsive-md mt-3">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                {{--<th scope="col">#</th>--}}
                <th scope="col">#</th>
                <th scope="col">Servidor</th>
                <th scope="col">Lot. Atual</th>
                <th scope="col">1ª Opção</th>
                <th scope="col">2ª Opção</th>
                <th scope="col">3ª Opção</th>
                <th class="text-center" scope="col">Ações</th>
            </tr>
            </thead>
            <tbody class="tableT1">
            @foreach($dadosT3 as $dt3)
                <tr class="">
                    <td class="border1pxSolidRGBA0125 fontSize-11px">{{  $loop->iteration }}º</td>
                    <td class="border1pxSolidRGBA0125 fontSize-11px">{{ $dt3->nome }}<br>Data de Exercício: {{ $dt3->data_exercicioBr }}</td>
                    <td class="border1pxSolidRGBA0125 fontSize-11px">{{ $dt3->lotacaoAtual }}</td>
                    <td class="border1pxSolidRGBA0125 fontSize-11px @if($unidadeSelecionada == $dt3->opcao1Id) background_81c784 @endif">
                        @if($unidadeSelecionada == $dt3->opcao1Id)
                            {{ $dt3->opcao1 }}
                            @else
                            <span class="color_C5C5C5">{{ $dt3->opcao1 }}</span>
                        @endif
                    </td>
                    <td class="border1pxSolidRGBA0125 fontSize-11px @if($unidadeSelecionada == $dt3->opcao2Id) background_ffffc4 @endif">
                        @if($unidadeSelecionada == $dt3->opcao2Id)
                            {{ $dt3->opcao2 }}
                        @else
                            <span class="color_C5C5C5">{{ $dt3->opcao2 }}</span>

                        @endif

                    </td>
                    <td class="border1pxSolidRGBA0125 fontSize-11px @if($unidadeSelecionada == $dt3->opcao3Id) background_ffffc4 @endif">
                        @if($unidadeSelecionada == $dt3->opcao3Id)
                            {{ $dt3->opcao3 }}
                        @else
                            <span class="color_C5C5C5">{{ $dt3->opcao3 }}</span>

                        @endif

                    </td>
                    <td class="border1pxSolidRGBA0125 btn-acoes color_01579b text-center">

                        <a href="{{ route("concursoRemocao.definirLotacaoServidor", $dt3->id) }}" data-toggle="tooltip" data-placement="top" title="Definir Lotação"> <i class="mdi mdi-arrow-right-bold"></i></a>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @endif

@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endpush