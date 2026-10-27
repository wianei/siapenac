@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-arrow-up"></i> Transferência de Internos com Progressão de Regime</h3>
        <hr>
        <p>
            Transferir interno de unidade prisional com progressão ou regressão de regime.
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
    <nav>
        <div class="nav nav-tabs fontSize-13px" id="nav-tab" role="tablist">
            <a class="nav-item nav-link" id="" data-toggle="" href="{{ route('movimentacoes.transferenciaShow') }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-history"></i> Histórico de Transferências</a>
            <a class="nav-item nav-link" id="" data-toggle="" href="{{ route('movimentacoes.transferenciaTransferir') }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-arrow-up"></i> Transferir Internos</a>
            <a class="nav-item nav-link active" id="" data-toggle="" href="{{ route('movimentacoes.transferencia.transferenciaProgressao') }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-arrow-up"></i> Transferir Internos com Progressão ou Regressão de Regime</a>
            <a class="nav-item nav-link" id="" data-toggle="" href="{{ route('movimentacoes.receberInterno') }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-arrow-down"></i> Receber Internos</a>
        </div>
    </nav>

    <form action="{{ route('movimentacoes.transferencia.transferenciaProgressaoStore') }}" id="transferirInternoForm" method="post" enctype="multipart/form-data">
        {!! csrf_field() !!}
        <div class="background_f9f9f9 p-3" style="border: 1px solid #CCCCCC">
        <h4>Dados da Transferência <i class="mdi mdi-arrow-bottom-right"></i></h4>
        <h5>Origem:</h5>
        <div class="form-row">
            <div class="form-group col-sm-6">
                <label for="interno">Nome do interno</label>
                <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome do interno" autocomplete="off">
                <div class="form-group col-sm-12" id="filter-records"></div>

            </div>

            <div class="form-group col-3">
                <label for="data">Data da Saída</label>
                <input type="text" name="data" class="form-control" id="data" placeholder="Data" autocomplete="off">
            </div>

            <div class="form-group col-3">
                <label for="hora">Horário da Saída</label>
                <input type="text" name="hora" class="form-control" id="hora" placeholder="Hora" autocomplete="off">
            </div>
        </div>

        <h5 class="titleInterno">Internos que Serão Transferidos de Unidade Prisional <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableInternos" class="table-responsive-md">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Facção</th>
                    <th scope="col">Liderança</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoInterno">

                </tbody>
            </table>
        </div>

        <h5>Destino:</h5>
        <div class="form-row">
            <div class="form-group col-sm">
                <label for="destino">Unidade Prisional</label>
                <select id="destino" name="destino" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidadesTransferir as $unidade)
                        @if($unidade['sigla'] != $lotacaoServidor)
                            <option value="{{ $unidade['id'] }}">
                                {{ $unidade['sigla'] }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>


        <div class="form-row">
            <div class="form-group col-md">
                <label for="motivo">Motivo e Observações da Transferência</label>
                <textarea class="form-control" id="motivo" name="motivo" aria-label="Motivo"></textarea>
            </div>
            <div class="form-group col-md">
                <label for="documentos">Documentos Anexos</label>
                <input type="file" name="documentos[]" class="form-control" id="documentos" accept="application/pdf" multiple>
            </div>
        </div>

   </div>

        <div class="background_f9f9f9  p-3 mt-3 mb-3" style="border: 1px solid #CCCCCC">
            <h4 class="mb-4">Dados da Progressão ou Regressão de Regime <i class="mdi mdi-arrow-bottom-right"></i></h4>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="dataPr">Data da Progressão ou Regressão</label>
                    <input type="text" name="dataPr" class="form-control" id="dataPr" placeholder="Data da Progressão" autocomplete="off">
                </div>

                <div class="form-group col-md-6">
                    <label for="horaPr">Horário da Progressão ou Regressão</label>
                    <input type="text" name="horaPr" class="form-control" id="horaPr" placeholder="Hora da Progressão" autocomplete="off">
                </div>
            </div>

        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="tipo">Tipo</label>
                <select id="tipo" name="tipo" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="progressaosemiaberto">
                        Progressão para o semiaberto
                    </option>
                    <option value="progressaoaberto">
                        Progressão para o aberto
                    </option>
                    <option value="regressaofechado">
                        Regressão para o fechado
                    </option>
                    <option value="regressaosemiaberto">
                        Regressão para o semiaberto
                    </option>
                </select>
            </div>
            <div id="fimsancaoDiv" class="form-group col-md-3 displayNone">
                <label for="fimsancao">Progressão por fim de regressão provisória?</label>
                <select id="fimsancao" name="fimsancao" class="form-control">
                    <option value="Não">
                        Não
                    </option>
                    <option value="Sim">
                        Sim
                    </option>
                </select>
            </div>
            <div id="temporariaDiv" class="form-group col-md-3 displayNone">
                <label for="temporaria">Regressão provisória?</label>
                <select id="temporaria" name="temporaria" class="form-control">
                    <option value="Não">
                        Não
                    </option>
                    <option value="Sim">
                        Sim
                    </option>
                </select>
            </div>
            <div id="diasDiv" class="form-group col-md-3 displayNone">
                <label for="dias">Quantidade de dias de regressão provisória:</label>
                <input type="text" name="dias" class="form-control" id="dias" placeholder="Quantidade de Dias" value="0" autocomplete="off">
            </div>
            <div id="assinaunidadeDiv" class="form-group col-md-3 displayNone">
                <label for="assinaunidade">Cumprirá o regime no Sistema Penitenciário?</label>
                <select id="assinaunidade" name="assinaunidade" class="form-control">
                    <option value="Sim">
                        Sim
                    </option>
                    <option value="Não">
                        Não
                    </option>
                </select>
            </div>
            <div id="saidasexternasDiv" class="form-group col-md-3 displayNone">
                <label for="saidasexternas">Com autorização de saídas externas?</label>
                <select id="saidasexternas" name="saidasexternas" class="form-control">
                    <option value="Não">
                        Não
                    </option>
                    <option value="Sim">
                        Sim
                    </option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div id="unidade_prisionalDiv" class="form-group col-md-3">
                <label for="unidade_prisionalAlocacao">Unidade Prisional de Alocação do Regime</label>
                <select id="unidade_prisionalAlocacao" name="unidade_prisionalAlocacao" class="form-control selectUnidadeAlocacao">
                    <option value="">
                        Selecione
                    </option>
                    <option value="{{ $usuarioLogadoUP['id'] }}">
                        {{ $usuarioLogadoUP['sigla'] }}
                    </option>
                </select>
            </div>

            <div id="pavilhaoDiv" class="form-group col-sm-3">
                <label for="pavilhaoAlocacao">Pavilhão de Alocação do Regime</label>
                <select id="pavilhaoAlocacao" name="pavilhaoAlocacao" class="form-control selectUnidadeAlocacao">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div id="alaDiv" class="form-group col-sm-3">
                <label for="alaAlocacao">Ala de Alocação do Regime</label>
                <select id="alaAlocacao" name="alaAlocacao" class="form-control selectUnidadeAlocacao">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div id="celaDiv" class="form-group col-sm-3">
                <label for="celaAlocacao">Cela de Alocação do Regime</label>
                <select id="celaAlocacao" name="celaAlocacao" class="form-control selectUnidadeAlocacao">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

        </div>

        <div class="form-row">
            <div class="form-group col-md">
                <label for="motivoPr">Observações</label>
                <input type="text" name="motivoPr" class="form-control" id="motivoPr" placeholder="Observações" autocomplete="off">
            </div>

            <div class="form-group col-md">
                <label for="documentosPr">Documentos</label>
                <input type="file" name="documentosPr[]" class="form-control" id="documentosPr" accept="application/pdf" multiple>
            </div>
        </div>

        </div>


        <div id="flashRetorno">

        </div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary"><i class="fas fa-check"></i> Realizar transferência</button>

    </form>

@endsection
@push('css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="{{ asset('css/movimentacoes/transferenciaProgressao/transferenciaProgressao.css') }}" rel="stylesheet">
@endpush

@push('script')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
    <script src="{{ asset('js/movimentacoes/transferenciaProgressao/transferenciaProgressao.js') }}"></script>
    <script src="{{ asset('js/outros/buscaInterno.js') }}"></script>
@endpush
