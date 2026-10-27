<form id="transferirInternoForm" action="{{ route('movimentacoes.transferir.store') }}" method="post" enctype="multipart/form-data" class="mb-4 mt-5">

    {!! csrf_field() !!}

    @php
        $lotacaoServidor = \App\Http\Controllers\Facade\FacadeController::getLotacaoServidor(Auth::user()->cpf);
    @endphp

    <h4>Origem:</h4>
    <div class="form-row">
        <div class="form-group col-6">
            <label for="interno">Nome do interno</label>
            <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome do interno" autocomplete="off">
            <div class="form-group col-sm-12" id="filter-records"></div>
        </div>

        <div class="form-group col-3">
            <label for="data">Data da Saída</label>
            <input type="text" name="data" class="form-control" id="data" placeholder="Data" autocomplete="off" required>
        </div>

        <div class="form-group col-3">
            <label for="hora">Horário da Saída</label>
            <input type="text" name="hora" class="form-control" id="hora" placeholder="Hora" autocomplete="off" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-sm-3">
            <label for="unidade_prisional">Unidade Prisional</label>
            <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">
                <option value="">
                    Selecione
                </option>
                <option value="{{ $usuarioLogadoUP['id'] }}">
                    {{ $usuarioLogadoUP['sigla'] }}
                </option>
            </select>
        </div>

        <div class="form-group col-sm-3">
            <label for="pavilhao">Pavilhão</label>
            <select id="pavilhao" name="pavilhao" class="form-control selectUnidade">
                <option value="">
                    Selecione
                </option>

            </select>
        </div>

        <div class="form-group col-sm-3">
            <label for="ala">Ala</label>
            <select id="ala" name="ala" class="form-control selectUnidade">
                <option value="">
                    Selecione
                </option>

            </select>
        </div>

        <div class="form-group col-sm-3">
            <label for="cela">Cela</label>
            <select id="cela" name="cela" class="form-control selectUnidade">
                <option value="">
                    Selecione
                </option>

            </select>
        </div>

    </div>

        <span id="spanInternosCela" class="displayNone">
            <h5 class="titleInternosCela"><span id="txtCela"></span> <i class="mdi mdi-arrow-bottom-right"></i></h5>
            <div id="tableInternosCela" class="table-responsive">
                <table class="table mb-3" id="internosCela">
                    <thead class="btn-danger color_FFF">
                    <tr class="">
                        <th scope="col"><input type="checkbox" id="btnCheckTodosInternosCela"></th>
                        <th scope="col">Prontuário</th>
                        <th scope="col">Foto</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Facção</th>
                        <th scope="col">Liderança</th>
                        <th scope="col">Localização</th>
                        <th scope="col">Ações</th>
                    </tr>
                    </thead>
                    <tbody id="retornoInternosCela">

                    </tbody>
                </table>
                <button type="button" id="btnAddTodosInternos" class="btn btn-success mt-3 mb-3"><i class="mdi mdi-account-plus"></i> Adicionar</button>
            </div>

        </span>

    <div id="tableInternos" class="table-responsive">
        <h5 class="titleInternosCela"><span id="txtCela"></span>Internos que serão transferidos <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">Prontuário</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">Facção</th>
                <th scope="col">Liderança</th>
                <th scope="col">Localização</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody id="retornoInterno">

            </tbody>
        </table>
    </div>

    <h4>Destino:</h4>
    <div class="form-row">
            <div class="form-group col-sm">
                <label for="destino">Unidade Prisional</label>
                <select id="destino" name="destino" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidadesTransferir as $unidade)
                    @if($unidade['sigla'] != $lotacaoServidor)
                        <option value="{{ $unidade['id'] }}">
                            {{ $unidade['sigla'] }}
                        </option>
                    @endif
                    @endforeach
                </select>
            </div>
    </div>


    <div class="form-row">
        <div class="form-group col-md">
            <label for="motivo">Motivo e Observações</label>
            <textarea class="form-control" id="motivo" name="motivo" aria-label="Motivo"></textarea>
        </div>
        <div class="form-group col-md">
            <label for="documentos">Documentos Anexos</label>
            <input type="file" name="documentos[]" class="form-control" id="documentos" accept="application/pdf" multiple>
        </div>
    </div>
    <div id="flashRetorno">

    </div>
    <button type="submit" id="btnSubmitForm" class="btn btn-primary"><i class="fas fa-check"></i> Realizar transferência</button>
</form>

AVISO: Não é possível transferir internos com transferências anteriores em aberto.
<hr/>
<h5 class="titleInternosCela mt-5"><span id="txtCela"></span>Internos(as) do(a) {{$lotacaoServidor}} que tiveram saída por transferência registrada e ainda não tiveram o recebimento confirmado no destino<i class="mdi mdi-arrow-bottom-right"></i></h5>
<form id="cancelarTransfEnvForm" action="{{ route('movimentacoes.transferencia.cancelarTransfEnv') }}" method="post">
    {!! csrf_field() !!}
    @if($internosPendentesEnv != [])
        <div id="tableInternosCelaEnv" class="table-responsive" style="max-height: 17em; overflow: scroll;">
            <table class="table mb-3" id="cancelarTransfInternosEnv">
                <thead class="background_e65251 color_FFF">
                <tr class="">
                    <th scope="col"><input type="checkbox" id="btnCheckCancelarTransfEnv"></th>
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Unidade de Destino</th>
                    <th scope="col">Localização Atual</th>
                    <th scope="col">Data de Envio</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoInternosCancelarTransfEnv">
                @foreach($internosPendentesEnv as $interno)
                    <tr class="" id="{{$interno['id']}}">
                        <td><input type="checkbox" name="internosCancelarTransfEnvCheck[]" class="checkbox" id="check_{{$interno['id']}}" value="{{$interno['id']}}"></td>
                        <td>{{$interno['id']}}</td>
                        <td>
                            @if(file_exists(storage_path('app/arquivos/internos/fotos/' . $interno['id'] . "/" . FotoService::getFotoPrincipalByIdTipo($interno['id'],'interno'))))
                                <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $interno['id'] . "/". FotoService::getFotoPrincipalByIdTipo($interno['id'],'interno'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                            @else
                                <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                            @endif
                        </td>
                        <td>{{$interno['nome']}}</td>
                        <td class="fontSize-11px">{{ $interno['destino'] }}</td>
                        <td class="fontSize-11px">{{$interno['lotacao']}}</td>
                        <td class="fontSize-11px">{{$interno['data']}}</td>
                        <td class="btn-acoes color_01579b">
                            <a href="{{ route('interno.show', $interno['id']) }}" data-toggle="tooltip" data-placement="top" title="Detalhes do Interno"><i class="mdi mdi-information-outline"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="form-row">
            <div class="form-group col-sm-12">
                <label for="obs">Observação:</label>
                <textarea name="obs" id="obs" rows="3" class="w-100"></textarea>
            </div>

        </div>


        <div id="flashCancelarTransfEnv">

        </div>
        <button type="submit" name="buttonRec" value="Cancelar" id="btnAddTodosInternosC" class="btn btn-primary mb-3"><i class="fas fa-user-slash"></i> Cancelar Transferências</button>

    @else
        Nenhum interno transferido do(a) {{$lotacaoServidor}} está com o recebimento pendente no destino.
    @endif
</form>

@push('css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush
@push('script')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
    <script src="{{ asset('js/movimentacoes/transferencia/transferir.js') }}"></script>
    <script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
    <script src="{{ asset('js/outros/buscaInterno.js') }}"></script>
@endpush