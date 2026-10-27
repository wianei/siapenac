<form action="#" class="mb-4 mt-4">

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
    </div>

    <div class="form-row">

        <div class="form-group col-md-4">
            <label for="obitoSispernB">Óbito na {{ config('app.secretaria') }} {{ config('app.uf') }}?</label>
            <select id="obitoSispernB" name="obitoSispernB" class="form-control">
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

        <div class="form-group col-md-4">
            <label for="datainicial">Data Inicial</label>
            <input type="text" name="datainicial" class="form-control" id="datainicial" placeholder="Data Inicial" autocomplete="off">
        </div>

        <div class="form-group col-md-4">
            <label for="datafinal">Data Final</label>
            <input type="text" name="datafinal" class="form-control" id="datafinal" placeholder="Data Final" autocomplete="off">
        </div>

    </div>

    <button type="submit" class="btn btn-primary mb-4"><i class="fas fa-search"></i> Buscar</button>

</form>

@if(strpos($url,"?"))

    <div id="links" class="mt-5">
        {{ $obitosHist->links() }}
    </div>

    @if($obitosHist->total() > 0)
        <h6 class="mt-2"><i>Sua busca retornou
                {{ $obitosHist->total() }} resultados
            </i>
        </h6>
    @else

        <h5 class="mt-5"><i>Sua busca não obteve resultados! </i>
        </h5>

    @endif

    <div class="pl-1 pr-1">
        @foreach($obitosHist as $obitoHist)
            <div class="row">
                <div class="col-md-2 mt-5">
                    <div style="z-index: 2;" class="font-weight-bold">
                        <div class="right-col text-right">
                            <div class="text-center align-middle fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                {{ TdataService::getDiaData(TdataService::getDataTimestamp($obitoHist->data)) }}<br>{{ TdataService::getMesExtAbrev(TdataService::getMesData(TdataService::getDataTimestamp($obitoHist->data))) }}<br><div style="font-size:0.7em;">{{ TdataService::getAnoData(TdataService::getDataTimestamp($obitoHist->data)) }}</div>
                                @if($obitoHist->obito_sispern == 'S')
                                <div class="fontSize-15px">às {{ TdataService::getTempoTimestamp($obitoHist->data) }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <p>
                        @if($obitoHist->obito_sispern == 'S' || $obitoHist->obito_sispern == 'N')
                            @if($obitoHist->obito_sispern == 'S')
                                <br/><strong>Óbito ocorrido dentro da {{ config('app.secretaria') }} {{ config('app.uf') }}:</strong> Sim
                            @else
                                <br/><strong>Óbito ocorrido dentro da {{ config('app.secretaria') }} {{ config('app.uf') }}:</strong> Não
                            @endif
                        @endif
                        <br/><strong>Observações:</strong> {{$obitoHist->motivo}}
                        <br/><strong>Internos:</strong>
                        @foreach(MovimentacoesService::internosMov($obitoHist->fk_movimentacao) as $interno)
                            @if ($loop->last)
                                <a href="{{route('interno.show',$interno['id'])}}">{{$interno['nome']}} (Prontuário {{$interno['id']}} - Lotação Anterior: {{$interno['lotacao_anterior']}})</a>.
                            @else
                                <a href="{{route('interno.show',$interno['id'])}}">{{$interno['nome']}} (Prontuário {{$interno['id']}} - Lotação Anterior: {{$interno['lotacao_anterior']}})</a>,
                            @endif
                        @endforeach
                    </p>
                    @foreach(MovimentacoesService::arquivosMov($obitoHist->fk_movimentacao) as $arquivo)
                        <a href="{{ route('movimentacao.obito.arquivoVisualizar',['id'=>$obitoHist->fk_movimentacao,'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                    @endforeach
                    <p class="text-left"><strong>Registrado por:</strong> {{ MovimentacoesService::logMov($obitoHist->fk_movimentacao) }}</p>
                </div>
            </div>
            @if (!$loop->last)
                <hr/>
            @endif
        @endforeach
    </div>

@endif

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endpush