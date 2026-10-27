@php
$lotacaoServidor = \App\Http\Controllers\Facade\FacadeController::getLotacaoServidor(Auth::user()->cpf);
@endphp
<span id="spanInternosCela">
<h5 class="titleInternosCela mt-5"><span id="txtCela"></span>Internos com confirmação de recebimento pendente no(a) {{$lotacaoServidor}} <i class="mdi mdi-arrow-bottom-right"></i></h5>
<form id="receberInternoForm" action="{{ route('movimentacoes.transferencia.receberInterno') }}" method="post">
    {!! csrf_field() !!}
@if($internosPendentes != [])
    <div id="tableInternosCela" class="table-responsive" style="max-height: 17em; overflow: scroll;">
        <table class="table mb-3" id="recebimentoInternos">
            <thead class="background_e65251 color_FFF">
            <tr class="">
                <th scope="col"><input type="checkbox" id="btnCheckTodosInternosRecebimento"></th>
                <th scope="col">Prontuário</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">Facção</th>
                <th scope="col">Liderança</th>
                <th scope="col">Regime</th>
                <th scope="col">Localização Anterior</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody id="retornoInternosRecebimento">
                @foreach($internosPendentes as $interno)
                <tr class="" id="{{$interno['id']}}">
                    <td><input type="checkbox" name="internosReceberCheck[]" class="checkbox" id="check_{{$interno['id']}}" value="{{$interno['id']}}"></td>
                    <td>{{$interno['id']}}</td>
                    <td>
                        @if(file_exists(storage_path('app/arquivos/internos/fotos/' . $interno['id'] . "/" . FotoService::getFotoPrincipalByIdTipo($interno['id'],'interno') )))
                            <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $interno['id'] . "/". FotoService::getFotoPrincipalByIdTipo($interno['id'],'interno'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                        @else
                            <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                        @endif
                    </td>
                    <td>{{$interno['nome']}}</td>
                    <td class="fontSize-11px">{{$interno['faccao_nome']}}</td>
                    <td class="fontSize-11px">{{$interno['lideranca']}}</td>
                    <td class="fontSize-11px">{{$interno['regime']}}</td>
                    <td class="fontSize-11px">{{$interno['lotacao']}}</td>
                    <td class="btn-acoes color_01579b">
                        <a href="{{ route('interno.show', $interno['id']) }}" data-toggle="tooltip" data-placement="top" title="Detalhes do Interno"><i class="mdi mdi-information-outline"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="form-row">
        <div class="form-group col-sm-6">
            <label for="data_entrada_r">Data de Entrada</label>
            <input type="text" class="form-control" name="data_entrada_r" id="data_entrada_r" placeholder="Data de Entrada" required>
        </div>

        <div class="form-group col-sm-6">
            <label for="hora_entrada_r">Horário da Entrada</label>
            <input type="text" name="hora_entrada_r" class="form-control" id="hora_entrada_r" placeholder="Hora" autocomplete="off" required>
        </div>
    </div>

    <div id="divAddDias" class="form-group">
        <label for="diasAdd">Quantidade de dias de triagem a partir de hoje:</label>
        <input type="text" name="diasAdd" class="form-control" id="diasAdd" placeholder="Informe aqui a quantidade de dias" autocomplete="off">
    </div>

    <div class="form-row">
        <div class="form-group col-sm-12">
            <label for="obss">Observação:</label>
            <textarea name="obss" id="obss" rows="3" class="w-100"></textarea>
            {{--<input type="text" name="obs" class="form-control" id="obs" placeholder="observação" autocomplete="off">--}}
        </div>
    </div>

    <div id="flashRetornoReceber">

    </div>
    <button type="submit" name="buttonRecConfirmar" value="Confirmar" id="btnAddTodosInternosR" onclick="return confirm('Deseja confirmar o recebimento?');" class="btn btn-primary mb-3 bntClicacoAcao">
        <i class="fas fa-check">
        </i> Confirmar Recebimento
    </button>
    <button type="submit" name="buttonRecCancelar" value="Cancelar" id="btnAddTodosInternosC" onclick="return confirm('Deseja cancelar o recebimento?');" class="btn btn-primary mb-3 bntClicacoAcao">
        <i class="fas fa-user-slash">
        </i> Cancelar Recebimento
    </button>
    <input type="hidden" value="" id="getClickBtn" name="getClickBtn">
    <input type="text" name="buttonRec" id="buttonRec" style="display: none" value="">

@else
    Nenhum interno com confirmação de recebimento pendente no(a) {{$lotacaoServidor}}.
@endif
</form>
<h5 class="titleInternosCela mt-5"><span id="txtCela"></span>Internos com alocação de cela pendente no(a) {{$lotacaoServidor}} <i class="mdi mdi-arrow-bottom-right"></i></h5>
@if($internosAlocacao != [])
<form id="alocarInternoForm" action="{{ route('movimentacoes.transferencia.alocarInterno') }}" method="post">
    {!! csrf_field() !!}
    <div class="table-responsive" style="max-height: 17em; overflow: scroll;">
        <table class="table" id="celaAlocar">
            <thead class="background_30516f color_FFF">
                <tr class="" style="">
                    <th scope="col"><input type="checkbox" id="btnCheckTodosInternosCelaReceber"></th>
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Mãe</th>
                    <th scope="col">Facção</th>
                    <th scope="col">Liderança</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody id="retornoInternosCelaReceber">
                @foreach($internosAlocacao as $interno)
                <tr class="">
                    <td scope="row"><input type="checkbox" name="internosCelaCheck[]" class="checkbox" id="check_{{ $interno['id'] }}" value="{{ $interno['id'] }}"></td>
                    <td scope="row">{{ $interno['id'] }}</td>
                    <td>
                        @if(file_exists(storage_path('app/arquivos/internos/fotos/' . $interno['id'] . "/" . FotoService::getFotoPrincipalByIdTipo($interno['id'],'interno'))))
                            <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $interno['id'] . "/". FotoService::getFotoPrincipalByIdTipo($interno['id'],'interno') )}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                        @else
                            <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                        @endif
                    </td>
                    <td>{{ $interno['nome'] }}</td>
                    <td>{{ $interno['mae'] }}</td>
                    <td>{{ $interno['faccao'] }}</td>
                    <td>{{ $interno['lideranca'] }}</td>
                    <td class="btn-acoes color_01579b">
                        <a href="#" data-toggle="tooltip" data-placement="top" title="Detalhes do Interno"><i class="mdi mdi-information-outline"></i></a>
                        <a href="#" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="form-row mt-4">
        <div class="form-group col-md-3">
            <label for="unidade_prisionalAlocacao">Unidade Prisional</label>
            <select id="unidade_prisionalAlocacao" name="unidade_prisionalAlocacao" class="form-control selectUnidadeAlocacao">
                <option value="">
                    Selecione
                </option>
                <option value="{{ $usuarioLogadoUP['id'] }}">
                    {{ $usuarioLogadoUP['sigla'] }}
                </option>
            </select>
        </div>

        <div class="form-group col-sm-3">
            <label for="pavilhaoAlocacao">Pavilhão</label>
            <select id="pavilhaoAlocacao" name="pavilhaoAlocacao" class="form-control selectUnidadeAlocacao">
                <option value="">
                    Selecione
                </option>
            </select>
        </div>

        <div class="form-group col-sm-3">
            <label for="alaAlocacao">Ala</label>
            <select id="alaAlocacao" name="alaAlocacao" class="form-control selectUnidadeAlocacao">
                <option value="">
                    Selecione
                </option>
            </select>
        </div>

        <div class="form-group col-sm-3">
            <label for="celaAlocacao">Cela</label>
            <select id="celaAlocacao" name="celaAlocacao" class="form-control selectUnidadeAlocacao">
                <option value="">
                    Selecione
                </option>
            </select>
        </div>

    </div>
    <div id="flashRetornoAlocacao">

    </div>
    <button id="alocaBtn" type="submit" onclick="return confirm('Deseja confirmar a alocação?');" class="btn btn-primary"><i class="fas fa-map-marker-alt"></i> Alocar na cela</button>
</form>
@else
    Nenhum interno com alocação de cela pendente no(a) {{$lotacaoServidor}}.
@endif

@push('script')
    <script src="{{ asset('js/movimentacoes/transferencia/receber.js') }}"></script>
    <script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
@endpush