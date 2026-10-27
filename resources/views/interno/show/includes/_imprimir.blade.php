<div class="container">
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-users"></i> Imprimir</h3>
        <hr>
        <p>
            Selecione os dados que deseja imprimir.
        </p>
    </div>
</div>
<form class="pl-5 pr-5" action="{{ route('interno.prontuarioPdf') }}" method="post">
    {!! csrf_field() !!}
<div class="col-md-4" style="display:flex; flex-direction:column">
        <div>
            <label><input type="checkbox" name="dadospessoais" value="dadospessoais" class="form-check-input"> Dados Pessoais</label>
        </div>
        @if(\Illuminate\Support\Facades\Auth::user()->tipo >= 100)
            <div>
                <label><input type="checkbox" id="fotos" name="fotos" class="form-check-input"> Fotos</label>
            </div>
            <div>
                <label><input type="checkbox" name="visita" id="visita" class="form-check-input"> Visitantes</label>
            </div>
            <div>
                <label><input type="checkbox" name="historico" id="historico" class="form-check-input"> Histórico</label>
            </div>
            <div>
                <label><input type="checkbox" id="advogados" name="advogados"  class="form-check-input"> Advogados</label>
            </div>
            <div>
                <label><input type="checkbox" name="falta" id="falta" class="form-check-input"> Falta Disciplinar</label>
            </div>
            <div>
                <label><input type="checkbox" name="escoltas" id="escoltas" class="form-check-input"> Escoltas</label>
            </div>
            <div>
                <label><input type="checkbox" name="saude" id="saude" class="form-check-input"> Saúde e Assistência</label>
            </div>
            <div>
                <label><input type="checkbox" name="companheiros" id="companheiros" class="form-check-input"> Companheiros de Cela</label>
            </div>
        @endif
</div>
<button type="submit" id="btnPesquisarVisitasData" class="btn btn-dark m-t-20 buscaAjax">Gerar PDF</button>
</form>
