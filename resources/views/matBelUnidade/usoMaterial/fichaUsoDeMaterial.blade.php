@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-3">
        <h3><i class="mdi mdi-file-document"></i> Ficha de Uso de Material</h3>
        <hr>
        <p>
            Relatar o uso de material belico por servidor na unidade.
        </p>
    </div>


    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    <div class="alert alert-warning fontSize-13px">
            <strong>Atenção</strong>, ao preencher e salvar o relatório, não vai ser possível alterá-lo.
    </div>
    <form action="{{ route('matBelUnidade.UsoMaterialStore') }}" method="post" id="formRelatorioUso">
        {!! csrf_field() !!}
        <div class="divEquipamentoUtilizado">
            <h6>Equipamento</h6>
            <div class="form-row">
                <div class="form-group col-sm-3" id="divTipoArmamento">
                    <label for="tipoEquipamento">Tipo de Equipamento</label>
                    <select id="tipoEquipamento" name="tipoEquipamento" class="form-control">
                        <option value="">
                            Selecione
                        </option>
                        <option value="municao">
                            Munição
                        </option>
                        <option value="granada">
                            Granada
                        </option>
                        <option value="espargidor">
                            Espargidor
                        </option>
                    </select>
                </div>

                <div class="form-group col-sm-3 displayNone" id="divTipo">
                    <label for="tipo">Tipo Armamento</label>
                    <select id="tipo" name="tipo" class="form-control">
                        <option value="">
                            Selecione
                        </option>
                    </select>
                </div>

                <div class="form-group col-sm-3 displayNone" id="divQtd">
                    <label for="qtd">Quantidade</label>
                    <input type="text" name="qtd" class="form-control" id="qtd" placeholder="Informe a quantidade">
                </div>

                <div class="form-group col-sm-3 displayNone mt-4" id="divAdd">
                    <button type="button" class="btn btn-icons btn-inverse-primary">
                        <i class="mdi mdi-plus"></i>
                    </button>
                </div>


            </div>
        </div>

        <div id="tableEquipamentos" class="displayNone">
            <h5 class="titleEquipamento">Equipamentos <i class="mdi mdi-arrow-bottom-right"></i></h5>
            <div id="equipamentosAgentes" class="table-responsive-md">
                <table class="table mb-3">
                    <thead class="background_3E4551 color_FFF">
                    <tr class="">
                        <th scope="col">#</th>
                        <th scope="col">Equipamento</th>
                    </tr>
                    </thead>
                    <tbody id="retornoEquipamentos">

                    </tbody>
                </table>
                <button type="button" id="btnAddEquipamentos" class="btn btn-success mt-3 mb-3"><i class="mdi mdi-plus"></i> Adicionar</button>
            </div>

        </div>

        <div id="tableEquipamentosCautela" class="displayNone">

            <h5 class="titleEquipamentoCautela">Equipamentos Utilizados<i class="mdi mdi-arrow-bottom-right"></i></h5>
            <div id="equipamentosEscolta" class="table-responsive-md">
                <table class="table mb-3">
                    <thead class="background_287ec3 color_FFF">
                    <tr class="">
                        <th scope="col">#</th>
                        <th scope="col">Equipamentos da Cautela</th>
                        <th scope="col">Ações</th>
                    </tr>
                    </thead>
                    <tbody id="retornoEquipamentosEscolta">

                    </tbody>
                </table>
            </div>

        </div>

        <div class="form-row" id="divRowAgentes">

            <div class="form-group col-sm-12">
                <label for="agente">Servidor Responsável</label>
                <input type="text" name="agente" class="form-control" id="agente" placeholder="Informe o nome do agente ou matrícula" autocomplete="off">
                <div class="form-group col-sm-12" id="divRetornoAgentes"></div>

            </div>

        </div>

        <h5 class="titleAgente displayNone">Servidor <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableAgentes" class="table-responsive-md displayNone">
            <table class="table mb-3">
                <thead class="background_1c2331 color_FFF">
                <tr class="">
                    <th scope="col">Matrícula</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoAgente">

                </tbody>
            </table>
        </div>

        <div class="form-row">
            <label for="relatorio">Relatório</label>
        </div>

        <textarea name="relatorio" id="relatorio" class="form-control" required></textarea>

        <div class="mt-3" id="flashRetorno"></div>

        <button type="submit" class="btn btn-primary mt-3"><i class="mdi mdi-content-save"></i> Salvar Relatório</button>

    </form>

    {{-- !! modal --}}
@endsection
@push('css')
<link href="{{ asset('css/armaria/cautela/cautela.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/matBelUnidade/fichaUsoDeMaterial.js') }}"></script>
<script src="{{ asset('js/outros/buscaServidor.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
@endpush