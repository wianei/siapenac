<div class="container">
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-exclamation-triangle"></i> Ocorrências</h3>
        <hr>
        <p>
            Ocorrências envolvendo o interno no {{ config('app.sistema_estado') }}.
        </p>
    </div>
    <div class="card-body pl-1 pr-1 row">
        <div class="col-md-2 esconderNaImpressao displayNone">
            <h4 class="card-title">Filtros</h4>
            <p class="card-description">Deixe marcado apenas os filtros a exibir:</p>
            <form action="#" class="forms-sample">
                <div class="form-group">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" name="checkFiltro" value="transferencia" class="form-check-input"> Ocorrências Diversas
                            <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" name="checkFiltro" value="mudancacela" class="form-check-input"> Tornozeleira - Rompimento
                            <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" name="checkFiltro" value="entradaunidadeprisional" class="form-check-input"> Tornozeleira - Deixou descarregar
                            <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" name="checkFiltro" value="saidajusicial" class="form-check-input"> Tornozeleira - Área não permitida
                            <i class="input-helper"></i></label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" name="checkFiltro" value="progressaoaberto" class="form-check-input"> Tornozeleira - Horário não permitido
                            <i class="input-helper"></i></label>
                    </div>
                    <div class="mb-2">
                        <button type="button" id="btnFiltro" class="btn btn-dark m-t-20 buscaAjax">Filtrar</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col allItens">
            @if($ocorrencias != null)
                @foreach($ocorrencias as $ocorrencia)
                    <div class="itens">
                        <div class="row align-items-center how-it-works d-flex ">
                            <div class="col-md-2 text-center bottom d-inline-flex justify-content-center align-items-center">
                                <div style="z-index: 2; background:white !important" class="font-weight-bold">
                                    <div class="mt-0 right-col text-right">
                                        <div class="text-center fontSize-1_5em line-heigt-1em mt-2 mb-2 color_365f80">
                                            {{ substr($ocorrencia['data'],8,2) }}<br>{{ strtoupper($ocorrencia['mesData']) }}<br><div style="font-size:0.7em;">{{ substr($ocorrencia['data'],0,4) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md">
                                <h5>{{ $ocorrencia['natureza'] }}</h5>
                                <p>
                                    {{ TdataService::TtimestampBR($ocorrencia['data'])." ".$ocorrencia['horario'] }}<br/><br/>
                                    <strong>Descrição:</strong> {!! $ocorrencia['descricao'] !!}<br/>
                                    @foreach($ocorrencia['arquivos'] as $arquivo)
                                        <a href="{{ route('ocorrencia.arquivoVisualizar',['id'=>$ocorrencia['id'],'filename'=>$arquivo['nome_arquivo']]) }}" class="btn btn-icons btn-primary fileDownload fontSize-10px" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo['nome_arquivo'] }}"><i class="mdi mdi-cloud-download"></i></a>
                                    @endforeach
                                </p>
                                 Internos envolvidos
                                @if($ocorrencia['internosEnvolvidos'] != [])
                                    <p>
                                        <strong>Internos Envolvidos:</strong>
                                        @foreach($ocorrencia['internosEnvolvidos'] as $intEnv)
                                            @if ($loop->last)
                                                <a href="{{route('interno.show', $intEnv['fk_id_envolvido'])}}">{{ InternoService::getInternoNome($intEnv['fk_id_envolvido']) }} (Prontuário {{ $intEnv['fk_id_envolvido'] }})</a>.
                                            @else
                                                <a href="{{route('interno.show', $intEnv['fk_id_envolvido'])}}">{{ InternoService::getInternoNome($intEnv['fk_id_envolvido']) }} (Prontuário {{ $intEnv['fk_id_envolvido'] }})</a>,
                                            @endif
                                        @endforeach
                                    </p>
                                @endif

                                 Agentes envolvidos
                                @if($ocorrencia['servidoresEnvolvidos'] != [])
                                    <p>
                                        <strong>Servidores Envolvidos:</strong>
                                        @foreach($ocorrencia['servidoresEnvolvidos'] as $serEnv)
                                            @if ($loop->last)
                                                {{ FacadeService::getNomeServidor($serEnv['fk_id_envolvido']) }} (Matrícula {{ $serEnv['fk_id_envolvido'] }}).
                                            @else
                                                {{ FacadeService::getNomeServidor($serEnv['fk_id_envolvido']) }} (Matrícula {{ $serEnv['fk_id_envolvido'] }}),
                                            @endif
                                        @endforeach
                                    </p>
                                @endif

                                 Advogados envolvidos
                                @if($ocorrencia['advogadosEnvolvidos'] != [])
                                    <p>
                                        <strong>Advogados Envolvidos:</strong>
                                        @foreach($ocorrencia['advogadosEnvolvidos'] as $advEnv)
                                            @if ($loop->last)
                                                <a href="{{route('advogado.show', $advEnv['fk_id_envolvido'])}}">{{ AdvogadoService::getNomeAdv($advEnv['fk_id_envolvido']) }} (Prontuário {{ $advEnv['fk_id_envolvido'] }})</a>.
                                            @else
                                                <a href="{{route('advogado.show', $advEnv['fk_id_envolvido'])}}">{{ AdvogadoService::getNomeAdv($advEnv['fk_id_envolvido']) }} (Prontuário {{ $advEnv['fk_id_envolvido'] }})</a>,
                                            @endif
                                        @endforeach
                                    </p>
                                @endif

                                 Visitas envolvidas
                                @if($ocorrencia['visitasEnvolvidas'] != [])
                                    <p>
                                        <strong>Visitas Envolvidas:</strong>
                                        @foreach($ocorrencia['visitasEnvolvidas'] as $visEnv)
                                            @if ($loop->last)
                                                <a href="{{route('visita.show', $visEnv['fk_id_envolvido'])}}">{{ VisitaService::getNomeVisita($visEnv['fk_id_envolvido']) }} (Prontuário {{ $visEnv['fk_id_envolvido'] }})</a>.
                                            @else
                                                <a href="{{route('visita.show', $visEnv['fk_id_envolvido'])}}">{{ VisitaService::getNomeVisita($visEnv['fk_id_envolvido']) }} (Prontuário {{ $visEnv['fk_id_envolvido'] }})</a>,
                                            @endif
                                        @endforeach
                                    </p>
                                @endif

                                @if($ocorrencia['log'] != [])
                                    <p class="text-left">
                                        {{ "Ocorrência registrada por: ".$ocorrencia['log'][0]['realizadoPor']." em ".$ocorrencia['log'][0]['data']." às ".$ocorrencia['log'][0]['hora'] }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="row timeline">
                            <div class="col-md-2">
                                <div class="corner top-right"></div>
                            </div>
                            <div class="col-md-8">
                                <hr/>
                            </div>
                            <div class="col-md-2">
                                <div class="corner left-bottom"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                Sem ocorrências registradas para este interno.
            @endif
        </div>
    </div>
</div>
@push('script')
<script src="{{ asset('js/interno/_historico.js') }}"></script>
@endpush