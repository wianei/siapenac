<form action="#" class="mb-4 mt-4">

    <div class="form-row">

        <div class="form-group col-md-6">
            <label for="tipoB">Situação</label>
            <select id="tipoB" name="tipoB" class="form-control" required>
                <option value="">
                    Selecione
                </option>
                <option value="mudancaaberto">
                    Provisório para o Aberto
                </option>
                <option value="mudancasemiaberto">
                    Provisório para o Semiaberto
                </option>
                <option value="mudancafechado">
                    Provisório para o Fechado
                </option>
                <option value="mudancaprovisorio">
                    Sentenciado para Provisório
                </option>
            </select>
        </div>

        <div class="form-group col-md-3">
            <label for="nome">Data Inicial</label>
            <input type="text" name="datainicial" class="form-control" id="datainicial" placeholder="Data Inicial" autocomplete="off">
        </div>

        <div class="form-group col-md-3">
            <label for="nome">Data Final</label>
            <input type="text" name="datafinal" class="form-control" id="datafinal" placeholder="Data Final" autocomplete="off">
        </div>

    </div>

    <button type="submit" class="btn btn-primary mb-4"><i class="fas fa-search"></i> Buscar</button>

</form>

@if(strpos($url,"?"))

    <div id="links" class="mt-5">
        {{ $mudancasentencaHist->links() }}
    </div>

    @if($mudancasentencaHist->total() > 0)
        <h6 class="mt-2"><i>Sua busca retornou
                {{ $mudancasentencaHist->total() }} resultados
            </i>
        </h6>
    @else

        <h5 class="mt-5"><i>Sua busca não obteve resultados! </i>
        </h5>

    @endif

    <div class="pl-1 pr-1">
        @foreach($mudancasentencaHist as $prHist)
            <div class="row">
                <div class="col-md-2 mt-5">
                    <div style="z-index: 2;" class="font-weight-bold">
                        <div class="right-col text-right">
                            <div class="text-center align-middle fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                            {{ TdataService::getDiaData(TdataService::getDataTimestamp($prHist->data)) }}<br>{{ TdataService::getMesExtAbrev(TdataService::getMesData(TdataService::getDataTimestamp($prHist->data))) }}<br><div style="font-size:0.7em;">{{ TdataService::getAnoData(TdataService::getDataTimestamp($prHist->data)) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <p>
                        @if($prHist->tipo == "mudancaaberto")<br/><strong>Tipo:</strong> Mudança de provisório para o regime aberto
                        @elseif($prHist->tipo == "mudancasemiaberto")<br/><strong>Tipo:</strong> Mudança de provisório para o regime semiaberto
                        @elseif($prHist->tipo == "mudancafechado")<br/><strong>Tipo:</strong> Mudança de provisório para o regime fechado
                        @endif
                        <br/><strong>Observações:</strong> {{$prHist->motivo}}
                        <br/><strong>Internos:</strong>
                        @foreach(MovimentacoesService::internosMov($prHist->fk_movimentacao) as $interno)
                            @if ($loop->last)
                                <a href="{{route('interno.show',$interno['id'])}}">{{$interno['nome']}} (Prontuário {{$interno['id']}} </a>.
                            @else
                                <a href="{{route('interno.show',$interno['id'])}}">{{$interno['nome']}} (Prontuário {{$interno['id']}} </a>,
                            @endif
                        @endforeach
                    </p>
                    @foreach(MovimentacoesService::arquivosMov($prHist->fk_movimentacao) as $arquivo)
                        <a href="{{ route('movimentacao.mudancasentenca.arquivoVisualizar',['id'=>$prHist->idmov,'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                    @endforeach
                    <p class="text-left"><strong>Registrado por:</strong> {{ MovimentacoesService::logMov($prHist->fk_movimentacao) }}</p>
                </div>
            </div>
            @if (!$loop->last)
                <hr/>
            @endif
        @endforeach
    </div>

@endif