
<div class="alert alert-warning mt-3 fontsize-12px">
ATENÇÃO: Todos os registros abaixo devem ser realizados pelas unidades de origem, antes de transferir os internos, exceto nos caso de ser necessário transferir internos para que eles cumpram regressão provisória, nesses casos, realize uma transferência de unidade e a unidade que receberá o interno é quem deverá realizar o procedimento de regressão provisória ou fim de regressão provisória acima.
</div>

<form id="progressaoregressaoForm" action="{{ route('movimentacoes.progressaoregressaoStore') }}" method="post" enctype="multipart/form-data" class="mb-4 mt-4">
    {!! csrf_field() !!}
    <div class="form-row">

        <div class="form-group col-md-6">
            <label for="interno">Nome dos internos</label>
            <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome dos internos" autocomplete="off">
            <div class="form-group col-md" id="filter-records"></div>
        </div>

        <div class="form-group col-md-3">
            <label for="data">Data da Progressão ou Regressão</label>
            <input type="text" name="data" class="form-control" id="data" placeholder="Data da Progressão" autocomplete="off" required>
        </div>

        <div class="form-group col-md-3">
            <label for="hora">Horário da Progressão ou Regressão</label>
            <input type="text" name="hora" class="form-control" id="hora" placeholder="Hora da Progressão" autocomplete="off" required>
        </div>

        <div id="tableInternos" class="table-responsive">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Facção</th>
                    <th scope="col">Regime Atual</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoInterno">

                </tbody>
            </table>
        </div>
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
        {{--<div id="tornozeleiraDiv" class="form-group col-md-3 displayNone">--}}
            {{--<label for="tornozeleira">Com uso de tornozeleira eletrônica?</label>--}}
            {{--<select id="tornozeleira" name="tornozeleira" class="form-control">--}}
                {{--<option value="Não">--}}
                    {{--Não--}}
                {{--</option>--}}
                {{--<option value="Sim">--}}
                    {{--Sim--}}
                {{--</option>--}}
            {{--</select>--}}
        {{--</div>--}}
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
            <label for="motivo">Observações</label>
            <input type="motivo" name="motivo" class="form-control" id="motivo" placeholder="Observações" autocomplete="off">
        </div>

        <div class="form-group col-md">
            <label for="documentos">Documentos</label>
            <input type="file" name="documentos[]" class="form-control" id="documentos" accept="application/pdf" multiple>
        </div>
    </div>
    <div id="flashRetorno">

    </div>
    <button id="proregBtn" type="submit" onclick="return confirm('Deseja confirmar o registro?');" class="btn btn-primary"><i class="fas fa-check"></i> Registrar</button>
</form>

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush
@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
@endpush
