@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-table"></i> Estatísticas da {{ config('app.secretaria') }} {{ config('app.uf') }} - Tabelas</h3>
        <hr>
        <p>
            Selecione os dados que deseja imprimir.
        </p>
    </div>


<form action="{{route('estatistica.internoPdfGerar')}}" method="post" enctype="multipart/form-data">
        {!! csrf_field() !!}
    <div style="display: flex; flex-direction: row">
        <div class="col-md-6" style="display:flex; flex-direction:column">
            <div>
                <label><input type="checkbox" name="internosunidade" value="internosunidade" class="form-check-input"> Internos Por Unidade</label>
            </div>
            <div>
                <label><input type="checkbox" id="regimeunidade" name="regimeunidade" class="form-check-input"> Regime por Unidade </label>
            </div>
            <div>
                <label><input type="checkbox" name="sexo" id="sexo" class="form-check-input"> Sexo </label>
            </div>
            <div>
                <label><input type="checkbox" name="regimeprisional" id="regimeprisional" class="form-check-input"> Regime prisional</label>
            </div>
            <div>
                <label><input type="checkbox" id="sentprov_sem_ms" name="sentprov_sem_ms"  class="form-check-input"> Sentenciados e provisórios (exceto medida de segurança)</label>
            </div>
            <div>
                <label><input type="checkbox" name="sentprov_em_ms" id="sentprov_em_ms" class="form-check-input"> Sentenciados e provisórios (em medida de segurança) </label>
            </div>
            <div>
                <label><input type="checkbox" name="sentprov_geral" id="sentprov_geral" class="form-check-input"> Sentenciados e provisórios (inclusive medida de segurança)  </label>
            </div>
            <div>
                <label><input type="checkbox" name="medidaseg" id="medidaseg" class="form-check-input"> Medida de segurança </label>
            </div>
            <div>
                <label><input type="checkbox" name="nacionalidade" id="nacionalidade" class="form-check-input"> Nacionalidade </label>
            </div>
            <div>
                <label><input type="checkbox" name="estadocivil" id="estadocivil" class="form-check-input"> Estado Civil </label>
            </div>
            <div>
                <label><input type="checkbox" name="escolaridade" id="escolaridade" class="form-check-input"> Escolaridade </label>
            </div>
            <div>
                <label><input type="checkbox" name="corpele" id="corpele" class="form-check-input"> Cor da pele </label>
            </div>
            <div>
                <label><input type="checkbox" name="orientacaosexual" id="orientacaosexual" class="form-check-input"> Orientação Sexual </label>
            </div>
            <div>
                <label><input type="checkbox" name="saidaunidade" id="saidaunidade" class="form-check-input"> Saída da Unidade </label>
            </div>
            <div>
                <label><input type="checkbox" name="religiao" id="religiao" class="form-check-input"> Religião </label>
            </div>
            @if(PermissaoService::getVerifyPermissionController("SeticController"))
            <div>
                <label><input type="checkbox" name="setic" id="setic" class="form-check-input"> Lideranças </label>
            </div>
            @endif
        </div>
        <div style="flex: 1;">
            <div class="form-row">
                <div class="form-group col-sm-3">
                    <label for="unidade_prisional">Unidade Prisional</label>
                    <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">
                        <option value="">
                            Todas
                        </option>
                        @foreach($unidades as $unidade)
                            <option value="{{ $unidade->id }}">
                                {{ $unidade->sigla }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-sm-3 displayNone" id="divPavilhao">
                    <label for="pavilhao">Pavilhão</label>
                    <select id="pavilhao" name="pavilhao" class="form-control selectUnidade">
                        <option value="">
                            Selecione
                        </option>
                    </select>
                </div>

                <div class="form-group col-sm-3 displayNone" id="divAla">
                    <label for="ala">Ala</label>
                    <select id="ala" name="ala" class="form-control selectUnidade">
                        <option value="">
                            Selecione
                        </option>
                    </select>
                </div>

                <div class="form-group col-sm-3 displayNone" id="divCela">
                    <label for="cela">Cela</label>
                    <select id="cela" name="cela" class="form-control">
                        <option value="">
                            Selecione
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" id="gerarPdf" class="btn btn-dark m-t-20 buscaAjax">Gerar PDF</button>

</form>
@endsection


@push('css')
    <link href="{{ asset('css/unidades/show.css') }}" rel="stylesheet">
@endpush
@push('script')
    <script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
    <script src="{{ asset('js/estatisticas/buscar.js') }}"></script>
@endpush