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
        <div class="form-group col">
            <label for="unidade_destino">Destino</label>
            <select id="unidade_destino" name="unidade_destino" class="form-control">
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
    </div>

    <div class="form-row">

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

</form>

@if(strpos($url,"?"))

    <div id="links" class="mt-5">
        {{ $recambiamentosHist->links() }}
    </div>

    @if($recambiamentosHist->total() > 0)
        <h6 class="mt-2"><i>Sua busca retornou
                {{ $recambiamentosHist->total() }} resultados
            </i>
        </h6>
    @else

        <h5 class="mt-5"><i>Sua busca não obteve resultados! </i>
        </h5>

    @endif

    <div class="pl-1 pr-1">
        @foreach($recambiamentosHist as $recambiamentoHist)
            <div class="row">
                <div class="col-md-2 mt-5">
                    <div style="z-index: 2;" class="font-weight-bold">
                        <div class="right-col text-right">
                            <div class="text-center align-middle fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                {{ TdataService::getDiaData(TdataService::getDataTimestamp($recambiamentoHist->data)) }}<br>{{ TdataService::getMesExtAbrev(TdataService::getMesData(TdataService::getDataTimestamp($recambiamentoHist->data))) }}<br><div style="font-size:0.7em;">{{ TdataService::getAnoData(TdataService::getDataTimestamp($recambiamentoHist->data)) }}</div>
                                <div class="fontSize-15px">às {{ TdataService::getTempoTimestamp($recambiamentoHist->data) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <p>
                        <br/><strong>Destino:</strong> {{ UnidadesService::getNomeUp($recambiamentoHist->unidade_destino) }}
                        <br/><strong>Motivo e Observações:</strong> {{$recambiamentoHist->motivo}}
                        <br/><strong>Internos:</strong>
                        @foreach(MovimentacoesService::internosMov($recambiamentoHist->fk_movimentacao) as $interno)
                            @if ($loop->last)
                                <a href="{{route('interno.show',$interno['id'])}}">{{$interno['nome']}} (Prontuário {{$interno['id']}} - Lotação Anterior: {{$interno['lotacao_anterior']}})</a>.
                            @else
                                <a href="{{route('interno.show',$interno['id'])}}">{{$interno['nome']}} (Prontuário {{$interno['id']}} - Lotação Anterior: {{$interno['lotacao_anterior']}})</a>,
                            @endif
                        @endforeach
                    </p>
                    @foreach(MovimentacoesService::arquivosMov($recambiamentoHist->fk_movimentacao) as $arquivo)
                        <a href="{{ route('movimentacao.recambiamento.arquivoVisualizar',['id'=>$recambiamentoHist->fk_movimentacao,'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                    @endforeach
                    <p class="text-left"><strong>Registrado por:</strong> {{ MovimentacoesService::logMov($recambiamentoHist->fk_movimentacao) }}</p>
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