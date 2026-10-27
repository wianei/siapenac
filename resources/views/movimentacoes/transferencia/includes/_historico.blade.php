<form id="buscaTransfForm" action="#" class="mb-4 mt-4">

    <div class="form-row">
        <div class="form-group col-md-3">
            <label for="unidade_prisionalv">Unidade Prisional de Origem</label>
            <select id="unidade_prisionalv" name="unidade_prisionalv" class="form-control selectUnidadev">
                <option value="">
                    Selecione
                </option>
                @foreach($unidades as $unidade)
                    <option value="{{ $unidade['id'] }}">
                        {{ $unidade['sigla'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-3">
            <label for="pavilhaov">Pavilhão de Origem</label>
            <select id="pavilhaov" name="pavilhaov" class="form-control selectUnidadev">
                <option value="">
                    Selecione
                </option>
            </select>
        </div>

        <div class="form-group col-md-3">
            <label for="alav">Ala de Origem</label>
            <select id="alav" name="alav" class="form-control selectUnidadev">
                <option value="">
                    Selecione
                </option>
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="celav">Cela de Origem</label>
            <select id="celav" name="celav" class="form-control selectUnidadev">
                <option value="">
                    Selecione
                </option>
            </select>
        </div>

        <div class="form-group col-md-3">
            <label for="unidade_prisionalh">Unidade Prisional Destino</label>
            <select id="unidade_prisionalh" name="unidade_prisionalh" class="form-control selectUnidadeh">
                <option value="">
                    Selecione
                </option>
                @foreach($unidades as $unidade)
                    <option value="{{ $unidade['id'] }}">
                        {{ $unidade['sigla'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-3">
            <label for="pavilhaoh">Pavilhão Destino</label>
            <select id="pavilhaoh" name="pavilhaoh" class="form-control selectUnidadeh">
                <option value="">
                    Selecione
                </option>
            </select>
        </div>

        <div class="form-group col-md-3">
            <label for="alah">Ala Destino</label>
            <select id="alah" name="alah" class="form-control selectUnidadeh">
                <option value="">
                    Selecione
                </option>
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="celah">Cela Destino</label>
            <select id="celah" name="celah" class="form-control selectUnidadeh">
                <option value="">
                    Selecione
                </option>
            </select>
        </div>

        <div class="form-group col-md-6">
            <label for="nome">Data Inicial</label>
            <input type="text" name="datainicial" class="form-control" id="datainicial" placeholder="Data Inicial" autocomplete="off">
        </div>

        <div class="form-group col-md-6">
            <label for="nome">Data Final</label>
            <input type="text" name="datafinal" class="form-control" id="datafinal" placeholder="Data Final" autocomplete="off">
        </div>
    </div>

    <button type="submit" class="btn btn-primary mb-4"><i class="fas fa-search"></i> Buscar</button>

    <!-- <button type="submit" value="PDF" name="btnGerarPdf" class="btn btn-primary mb-4"><i class="mdi mdi-file-pdf"></i> Gerar PDF</button> -->

</form>
<div id="flashRetornoo">

</div>
@if(strpos($url,"?"))

    <div id="links" class="mt-5">
        {{ $transferenciasHist->links() }}
    </div>

    @if($transferenciasHist->total() > 0)
        <h6 class="mt-2"><i>Sua busca retornou
                {{ $transferenciasHist->total() }} resultados
            </i>
        </h6>
    @else

        <h5 class="mt-5"><i>Sua busca não obteve resultados! </i>
        </h5>

    @endif

    <div class="pl-1 pr-1">
        @foreach($transferenciasHist as $transferenciaHist)
            <div class="row">
                <div class="col-2 mt-5">
                    <div style="z-index: 2;" class="font-weight-bold">
                        <div class="right-col text-right">
                            <div class="text-center align-middle fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                {{ TdataService::getDiaData(TdataService::getDataTimestamp($transferenciaHist->data)) }}<br>{{ TdataService::getMesExtAbrev(TdataService::getMesData(TdataService::getDataTimestamp($transferenciaHist->data))) }}<br><div style="font-size:0.7em;">{{ TdataService::getAnoData(TdataService::getDataTimestamp($transferenciaHist->data)) }}</div>
                                <div class="fontSize-15px">às {{ TdataService::getTempoTimestamp($transferenciaHist->data) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <p>
                        <br/><strong>Motivo e Observações:</strong> {{$transferenciaHist->motivo}}
                        <br/><strong>Destino:</strong> {{MovimentacoesService::destinoMov($transferenciaHist->unidade_destino)}}
                        <br/><strong>Internos:</strong>
                        @foreach(MovimentacoesService::internosMov($transferenciaHist->fk_movimentacao) as $interno)
                            @if ($loop->last)
                                <a href="{{route('interno.show',$interno['id'])}}">{{$interno['nome']}} (Prontuário {{$interno['id']}} - Lotação Anterior: {{$interno['lotacao_anterior']}}, Status: {{$interno['status']}})</a>.
                            @else
                                <a href="{{route('interno.show',$interno['id'])}}">{{$interno['nome']}} (Prontuário {{$interno['id']}} - Lotação Anterior: {{$interno['lotacao_anterior']}}, Status: {{$interno['status']}})</a>,
                            @endif
                        @endforeach
                    </p>
                    @foreach(MovimentacoesService::arquivosMov($transferenciaHist->fk_movimentacao) as $arquivo)
                        <a href="{{ route('movimentacao.transferenciaHist.arquivoVisualizar',['id'=>$transferenciaHist->fk_movimentacao,'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                    @endforeach
                    <p class="text-left"><strong>Registrado por:</strong> {{ MovimentacoesService::logMov($transferenciaHist->fk_movimentacao) }}</p>
                </div>
            </div>
            <hr/>
        @endforeach
    </div>

@endif

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endpush