<form id="obitoForm" action="{{ route('movimentacoes.obitoStore') }}" method="post" enctype="multipart/form-data" id="formSaveObito" class="mb-4 mt-5">
    {!! csrf_field() !!}

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="interno">Nome dos internos</label>
            <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome dos internos" autocomplete="off">
            <div class="form-group col-md" id="filter-records"></div>
        </div>
        <div class="form-group col-md-6">
            <label for="obitoSispern">Óbito na {{ config('app.secretaria') }} {{ config('app.uf') }}?</label>
            <select id="obitoSispern" name="obitoSispern" class="form-control" required="required">
                <option value="">
                    Selecione
                </option>
                <option value="S">
                    Sim
                </option>
                <option value="N">
                    Não
                </option>
            </select>
        </div>
        <div class="form-group col-md-6">
            <label for="data">Data do Óbito</label>
            <input type="text" name="data" class="form-control" id="data" placeholder="Data do Óbito" autocomplete="off" required>
        </div>
        <div id="horaDiv" class="form-group col-6">
            <label for="hora">Horário do Óbito</label>
            <input type="text" name="hora" class="form-control" id="hora" placeholder="Hora" autocomplete="off">
        </div>
    </div>

    <div id="tableInternos" class="table-responsive">
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

    <div class="form-row">
        <div class="form-group col-md">
            <label for="obs">Observações</label>
            <textarea name="obs" id="obs" class="form-control"></textarea>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="documentos">Documentos</label>
            <input type="file" name="documentos[]" class="form-control" id="documentos" accept="application/pdf" multiple>
        </div>
    </div>

    <div id="flashRetorno">

    </div>

    <button id="btnObitoeOcorrencia" type="submit" class="btn btn-success displayNone"><i class="fas fa-check"></i> Registrar Óbito e Preencher Ocorrência</button>

    <button id="btnSomenteObito" type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar Óbito</button>

</form>

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush
@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
{{--<script src="{{ asset('js/vendor/buscaInterno.js') }}"></script>--}}
@endpush
