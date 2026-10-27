<form id="saidaJudicialForm" action="{{ route('movimentacoes.saidajudicialStore') }}" method="post" enctype="multipart/form-data" class="mb-4 mt-5">
    {!! csrf_field() !!}

    <div class="form-row">
        <div class="form-group col-6">
            <label for="interno">Nome do interno</label>
            <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome dos internos" autocomplete="off">
            <div class="form-group col-md" id="filter-records"></div>
        </div>
        <div class="form-group col-3">
            <label for="data">Data da Saída</label>
            <input type="text" name="data" class="form-control" id="data" placeholder="Data" autocomplete="off" required>
        </div>

        <div class="form-group col-3">
            <label for="hora">Horário da Saída</label>
            <input type="text" name="hora" class="form-control" id="hora" placeholder="Hora" autocomplete="off" required>
        </div>
    </div>

    <div id="tableInternos" class="table-responsive">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">Prontuário</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">Regime</th>
                <th scope="col">Localização</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody id="retornoInterno">

            </tbody>
        </table>
    </div>

    <div class="form-row">
        <div class="form-group col-6">
            <label for="motivo">Motivo e Observações</label>
            <input type="motivo" name="motivo" class="form-control" id="motivo" placeholder="Observações" autocomplete="off">
        </div>


        <div class="form-group col-3">
            <label for="documentos">Documentos</label>
            <input type="file" name="documentos[]" class="form-control" id="documentos" accept="application/pdf" multiple>
        </div>
    </div>
    <div id="flashRetorno">

    </div>
    <button id="saijudBtn" type="submit" onclick="return confirm('Deseja confirmar o registro?');" class="btn btn-primary"><i class="fas fa-check"></i> Registrar Saída Judicial</button>
</form>