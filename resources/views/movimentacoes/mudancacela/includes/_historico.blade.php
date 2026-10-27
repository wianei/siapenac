<form id="buscaMudCelForm" action="#" class="mt-3">

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
            <label for="unidade_prisionalh">Unidade Prisional de Destino</label>
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
            <label for="pavilhaoh">Pavilhão de Destino</label>
            <select id="pavilhaoh" name="pavilhaoh" class="form-control selectUnidadeh">
                <option value="">
                    Selecione
                </option>
            </select>
        </div>

        <div class="form-group col-md-3">
            <label for="alah">Ala de Destino</label>
            <select id="alah" name="alah" class="form-control selectUnidadeh">
                <option value="">
                    Selecione
                </option>
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="celah">Cela de Destino</label>
            <select id="celah" name="celah" class="form-control selectUnidadeh">
                <option value="">
                    Selecione
                </option>
            </select>
        </div>

        <div class="form-group col-md-6">
            <label for="datainicial">Data Inicial</label>
            <input type="text" name="datainicial" class="form-control" id="datainicial" placeholder="Data Inicial" autocomplete="off">
        </div>

        <div class="form-group col-md-6">
            <label for="datafinal">Data Final</label>
            <input type="text" name="datafinal" class="form-control" id="datafinal" placeholder="Data Final" autocomplete="off">
        </div>

    </div>

    <button type="submit" class="btn btn-primary mb-4"><i class="fas fa-search"></i> Buscar</button>

</form>

@if(strpos($url,"?"))

    <div id="links" class="mt-5">
        {{ $mudancasCelas->links() }}
    </div>

    @if($mudancasCelas->total() > 0)
        <h6 class="mt-2"><i>Sua busca retornou
                {{ $mudancasCelas->total() }} resultados
            </i>
        </h6>
    @else

        <h5 class="mt-5"><i>Sua busca não obteve resultados! </i>
        </h5>

    @endif

    <div class="pl-1 pr-1">
        @foreach($mudancasCelas as $mudancaCela)
        <div class="row">
            <div class="col-md-2 mt-5">
                <div style="z-index: 2;" class="font-weight-bold">
                    <div class="right-col text-right">
                        <div class="text-center align-middle fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                            {{ TdataService::getDiaData(TdataService::getDataTimestamp($mudancaCela->data)) }}<br>{{ TdataService::getMesExtAbrev(TdataService::getMesData(TdataService::getDataTimestamp($mudancaCela->data))) }}<br><div style="font-size:0.7em;">{{ TdataService::getAnoData(TdataService::getDataTimestamp($mudancaCela->data)) }}</div>
                            <div class="fontSize-15px">às {{ TdataService::getTempoTimestamp($mudancaCela->data) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md">
                <p>
                    <br/><strong>Motivo:</strong> {{$mudancaCela->motivo}}
                    <br/><strong>Destino:</strong> {{ UnidadesService::getSiglaUp($mudancaCela->unidade_destino)." - ".UnidadesService::getNomePav($mudancaCela->pavilhao_destino)." - ".UnidadesService::getNomeAla($mudancaCela->ala_destino)." - ".UnidadesService::getNomeCela($mudancaCela->cela_destino) }}
                    <br/><strong>Internos:</strong>
                        @foreach(MovimentacoesService::internosMov($mudancaCela->fk_movimentacao) as $interno)
                            @if ($loop->last)
                                <a href="{{route('interno.show',$interno['id'])}}">{{$interno['nome']}} (Prontuário {{$interno['id']}} - Lotação Anterior: {{$interno['lotacao_anterior']}})</a>.
                            @else
                                <a href="{{route('interno.show',$interno['id'])}}">{{$interno['nome']}} (Prontuário {{$interno['id']}} - Lotação Anterior: {{$interno['lotacao_anterior']}})</a>,
                            @endif
                        @endforeach
                </p>
                @foreach(MovimentacoesService::arquivosMov($mudancaCela->fk_movimentacao) as $arquivo)
                    <a href="{{ route('movimentacao.mudancaCela.arquivoVisualizar',['id'=>$mudancaCela->fk_movimentacao,'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                @endforeach
                <p class="text-left"><strong>Registrado por:</strong> {{ MovimentacoesService::logMov($mudancaCela->fk_movimentacao) }}</p>
            </div>
        </div>
            @if (!$loop->last)
                <hr/>
            @endif
        @endforeach
    </div>

@endif