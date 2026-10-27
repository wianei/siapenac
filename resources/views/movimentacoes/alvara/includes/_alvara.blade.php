<div class="flash-message">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))

            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>
<form id="alvaraForm" action="{{ route('movimentacoes.alvaraStore') }}" method="post" enctype="multipart/form-data" class="mb-4 mt-5">
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
        <div class="form-group col-6">
            <label for="motivo">Observações</label>
            <input type="motivo" name="motivo" class="form-control" id="motivo" placeholder="Observações" autocomplete="off">
        </div>

        {{-- só aparece essa opção para o setor de tornozeleiras --}}
        @if($lotacaoServidor == 8)
            <div class="form-group col-3">
                <label for="tornozeleira">Permanecerá com tornozeleira eletrônica?</label>
                <select id="tornozeleira" name="tornozeleira" class="form-control">
                    <option value="N">
                        Não
                    </option>
                    <option value="S">
                        Sim
                    </option>
                </select>
            </div>
        @endif

        <div class="form-group col-3">
            <label for="documentos">Documentos</label>
            <input type="file" name="documentos[]" class="form-control" id="documentos" accept="application/pdf" multiple>
        </div>
    </div>
    <div id="flashRetorno"></div>
    <button id="alvaraBtn" type="submit" onclick="return confirm('Deseja confirmar o registro?');" class="btn btn-primary"><i class="fas fa-check"></i> Registrar Alvará</button>
</form>
<div class="alert alert-warning">
    AVISO: Caso o interno vá permanecer com tornozeleira eletrônica após a execução deste alvará, transfira-o para {{ config('app.tz') }} e ela realizará o procedimento acima.

</div>
@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush
@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/outros/buscaInterno.js') }}"></script>
@endpush