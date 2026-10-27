@if(\Illuminate\Support\Facades\Auth::user()->tipo >= 300)
<div class="container">
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-taxi"></i> Escoltas</h3>
        <hr>
        <p>
            Lista de escoltas internas e externas envolvendo este interno.
        </p>
    </div>
</div>
<div class="card-body pl-1 pr-1 row">
    <div class="col-md-2 esconderNaImpressao">
        <h4 class="card-title">Filtros</h4>
        <p class="card-description">Deixe marcado apenas os filtros a exibir:</p>
        <form class="forms-sample">
            <div class="form-group">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" name="checkFiltroEscolta" value="tgep" class="form-check-input"> FTPEN
                        <i class="input-helper"></i></label>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" name="checkFiltroEscolta" value="tinterna" class="form-check-input"> Interna
                        <i class="input-helper"></i></label>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" name="checkFiltroEscolta" value="texterna" class="form-check-input"> Externa
                        <i class="input-helper"></i></label>
                </div>
                <div class="mb-2">
                    <button type="button" id="btnPesquisarEscoltas" class="btn btn-dark m-t-20 buscaAjax">Filtrar</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col allItensEscoltas">

        @foreach($escolta as $esc)

            <div class="t{{$esc['dadosEscolta'][0]['tipo']}} itensEscolta">
                <!--first section-->
                <div class="row align-items-center how-it-works d-flex">
                    <div class="col-md-10">
                        <h5>{{ strtoupper($esc['dadosEscolta'][0]['tipo']) }}</h5>
                        <p class="text-left">
                        {{--@if($esc['dadosEscolta'][0]['tipo'] == "FTPEN")--}}
                            {{--<strong><em>{{ $esc['dadosEscolta'][0]['fkMovimentacao'] }}</em></strong><br/>--}}
                        {{--@endif--}}
                        <strong>Origem:</strong> {{ $esc['dadosEscolta'][0]['unidade'] }}
                        <br/><strong>Destino:</strong> {{ $esc['dadosEscolta'][0]['destino'] }}
                        @if($esc['dadosAgentes'] != null)
                            <br/><strong>Escoltantes:</strong>
                                @foreach($esc['dadosAgentes'] as $agentes)
                                    {{ $agentes['nomeAgente']." (".$agentes['matricula']."), " }}
                                @endforeach
                        @endif
                        @if(count($esc['dadosEscolta'][0]['internosEscolta']) > 0)
                            <br/><strong>Internos na mesma escolta:</strong>
                                @foreach($esc['dadosEscolta'][0]['internosEscolta'] as $interEsc)
                                    {{ $interEsc['nome']." (".$interEsc['prontuario']."), " }}
                                @endforeach
                        @endif
                        <br/><strong>Saída:</strong> {{ $esc['dadosEscolta'][0]['dataSaida']." às ".$esc['dadosEscolta'][0]['horaSaida'] }}
                        <br/><strong>Retorno:</strong> {{ $esc['dadosEscolta'][0]['dataRetorno']." às ".$esc['dadosEscolta'][0]['horaRetorno'] }}
                        @if($esc['dadosEscolta'][0]['vtrPlaca'] != null)
                            <br/><strong>Placa da Viatura:</strong> {!!  $esc['dadosEscolta'][0]['vtrPlaca']." / <strong>Motorista: </strong>".$esc['dadosEscolta'][0]['vtrMotorista'] !!}
                        @endif
                        <br/><strong>Observação:</strong> {{ $esc['dadosEscolta'][0]['obs'] }}</p>
                        @if($esc['dadosArquivos'] != null)
                            @foreach($esc['dadosArquivos'] as $arquivo)
                                <a href="{{ route('escolta.arquivo',['id' => $esc['dadosEscolta'][0]['id'], 'filename' => $arquivo['nomeArquivo']])}}" onclick="" class="btn btn-primary mr-1 mb-1 fileDownload" id="fileDownload" title="Abrir arquivo: {{ $arquivo['nomeArquivo'] }}" data-toggle="tooltip" data-placement="right"><i class="mdi mdi-cloud-download"></i></a>
                            @endforeach
                        @endif
                        @if($esc['dadosLog'] != null)
                            <p class="text-left">
                                {{ "Registrado por: ".$esc['dadosLog'][0]['realizadoPor']." em ".$esc['dadosLog'][0]['data']." às ".$esc['dadosLog'][0]['hora'] }}
                            </p>
                        @endif
                    </div>
                </div>
                <!--path between 1-2-->
                <div class="row timeline">
                    <div class="col-md-10">
                        <hr/>
                    </div>
                    <div class="col-md-2">
                        <div class="corner left-bottom"></div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
</div>
@endif
@push('script')
<script src="{{ asset('js/interno/_escoltas.js') }}"></script>
@endpush