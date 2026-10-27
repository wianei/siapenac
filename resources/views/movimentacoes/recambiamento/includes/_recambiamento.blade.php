<form id="recambiamentoForm" action="{{ route('movimentacoes.recambiamentoStore') }}" method="post" enctype="multipart/form-data" class="mb-4 mt-5">
    {!! csrf_field() !!}

    <div class="form-row">
        <div class="form-group col-6">
            <label for="interno">Nome dos internos</label>
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

    <div class="form-row">
        <div class="form-group col-12">
            <label for="unidade_destino_store">Destino</label>
            <select id="unidade_destino_store" name="unidade_destino_store" class="form-control" required>
                <option value="">
                    Selecione
                </option>
                <option value="2526">DEPEN</option>
                <option value="2534">Acre</option>
                <option value="2542">Alagoas</option>
                <option value="2569">Amazonas</option>
                <option value="2550">Amapá</option>
                <option value="2577">Bahia</option>
                <option value="2585">Ceará</option>
                <option value="2593">Distrito Federal</option>
                <option value="2607">Espírito Santo</option>
                <option value="2615">Goiás</option>
                <option value="2623">Maranhão</option>
                <option value="2658">Minas Gerais</option>
                <option value="2640">Mato Grosso do Sul</option>
                <option value="2631">Mato Grosso</option>
                <option value="2674">Rio Grande do Norte</option>
                <option value="6409">Paraíba</option>
                <option value="2410">Pernambuco</option>
                <option value="2429">Piauí</option>
                <option value="2666">Paraná</option>
                <option value="2437">Rio de Janeiro</option>
                <option value="2461">Rondônia</option>
                <option value="2470">Roraima</option>
                <option value="2488">Santa Catarina</option>
                <option value="2496">Sergipe</option>
                <option value="2500">São Paulo</option>
                <option value="2518">Tocantins</option>
                <option value="2682">Pará</option>
                <option value="2453">Rio Grande do Sul</option>
            </select>
        </div>
        <div class="form-group col-12">
            <label for="motivo">Motivo e Observações</label>
            <input type="motivo" name="motivo" class="form-control" id="motivo" placeholder="Destino, Motivo e Observações do Recambiamento" autocomplete="off">
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
            <label for="documentos">Documentos</label>
            <input type="file" name="documentos[]" class="form-control" id="documentos" accept="application/pdf" multiple>
        </div>
    </div>
    <div id="flashRetorno">

    </div>
    <button id="recamBtn" type="submit" onclick="return confirm('Deseja confirmar o registro?');" class="btn btn-primary"><i class="fas fa-check"></i> Registrar Recambiamento</button>
</form>
@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush
@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/outros/buscaInterno.js') }}"></script>
@endpush