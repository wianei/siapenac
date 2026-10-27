<form id="mudancasentencaForm" action="{{ route('movimentacoes.mudancasentencaStore') }}" method="post" enctype="multipart/form-data" class="mb-4 mt-5">
    {!! csrf_field() !!}
    <div class="form-row">

        <div class="form-group col-6">
            <label for="interno">Nome do interno</label>
            <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome dos internos" autocomplete="off">
            <div class="form-group col-md" id="filter-records"></div>
        </div>

        <div class="form-group col-3">
            <label for="data">Data da sentença ou da prisão provisória</label>
            <input type="text" name="data" class="form-control" id="data" placeholder="Data da sentença ou da prisão provisória" autocomplete="off" required>
        </div>

        <div class="form-group col-3">
            <label for="hora">Horário da sentença ou da prisão provisória</label>
            <input type="text" name="hora" class="form-control" id="hora" placeholder="Hora" autocomplete="off" required>
        </div>

        <div class="form-group col-3">
            <label for="tipo">Situação</label>
            <select id="tipo" name="tipo" class="form-control">
                <option value="">
                    Selecione
                </option>
                <option value="mudancafechado">
                    Sentenciado ao Fechado
                </option>
                <option value="mudancasemiaberto">
                    Sentenciado ao Semiaberto
                </option>
                <option value="mudancaaberto">
                    Sentenciado ao Aberto
                </option>
                <option value="mudancaprovisorio">
                    Provisório
                </option>
            </select>
        </div>

        <div class="form-group col">
            <label for="documentos">Documentos</label>
            <input type="file" name="documentos[]" class="form-control" id="documentos" accept="application/pdf" multiple>
        </div>

        <div id="tableInternos" class="table-responsive">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Sentenciado</th>
                    <th scope="col">Regime</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoInterno">

                </tbody>
            </table>
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

    </div>

    <div class="form-row">

        <div class="form-group col">
            <label for="motivo">Observações</label>
            <input type="motivo" name="motivo" class="form-control" id="motivo" placeholder="Observações" autocomplete="off">
        </div>

    </div>



    <div id="flashRetorno">

    </div>
    <button id="mudSenBtn" type="submit" onclick="return confirm('Deseja confirmar o registro?');" class="btn btn-primary"><i class="fas fa-check"></i> Registrar</button>
</form>