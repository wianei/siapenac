<form id="mudancaCelaForm" action="{{ route('movimentacoes.mudancacelaStore') }}" method="post" enctype="multipart/form-data" class="mb-4 mt-5">
    {!! csrf_field() !!}

    <h4>Origem:</h4>
    <div class="form-row">
        <div class="form-group col-6">
            <label for="interno">Nome do interno</label>
            <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome do interno" autocomplete="off">
            <div class="form-group col-sm-12" id="filter-records"></div>
        </div>

        <div class="form-group col-3">
            <label for="data">Data da Mudança de Cela</label>
            <input type="text" name="data" class="form-control" id="data" placeholder="Data" autocomplete="off" required>
        </div>

        <div class="form-group col-3">
            <label for="hora">Horário da Mudança de Cela</label>
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
            <select id="cela" name="cela" class="form-control">
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
            <button type="button" id="btnAddTodosInternos" class="btn btn-success mb-3"><i class="mdi mdi-account-plus"></i> Adicionar</button>
        </div>
    </span>

    <div id="tableInternos" class="table-responsive">
        <h5 class="titleInternosCela"><span id="txtCela"></span>Internos que mudarão de cela <i class="mdi mdi-arrow-bottom-right"></i></h5>
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
        <div class="form-group col-md">
            <label for="pavilhaor">Pavilhão</label>
            <select id="pavilhaor" name="pavilhaor" class="form-control selectPvAlaCela">
                <option value="">
                    Selecione
                </option>
                @foreach($usuarioLogadoPVs as $pv)
                <option value="{{ $pv['id'] }}">
                    {{ $pv['nome'] }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md">
            <label for="alar">Ala</label>
            <select id="alar" name="alar" class="form-control selectPvAlaCela">
                <option value="">
                    Selecione
                </option>
            </select>
        </div>
        <div class="form-group col-md">
            <label for="celar">Cela</label>
            <select id="celar" name="celar" class="form-control">
                <option value="">
                    Selecione
                </option>
            </select>
        </div>
    </div>

    <div id="celaDestinoSelecionada" class="displayNone"></div>
    <div id="tableInternosDestino" class="table-responsive displayNone">
        <h5 class="titleInternosCelaDestino"><span id="txtCela"></span>Internos que estão na cela de destino <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <table class="table mb-3">
            <thead class="background_3E4551 color_FFF">
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
            <tbody id="retornoInternosCelaDestino">

            </tbody>
        </table>
    </div>

    <div id="flashRetornoSancaoTriagem" class="displayNone">
        <div class="flash-message">
            <p class="alert alert-danger" id="mensagemRetornoSancaoTriagem">
            </p>
        </div>
    </div>

    <h4>Tipo de Mudança:</h4>
    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="tipoMudanca">Tipo:</label>
            <select id="tipoMudanca" name="tipoMudanca" class="form-control">
                <option value="normal" selected>
                    Normal
                </option>
                <option value="sancao">
                    Sanção Disciplinar
                </option>
                <option value="triagem">
                    Triagem
                </option>
            </select>
        </div>

        <div id="divAddDias" class="form-group col-md-4 displayNone">
            <label for="diasAdd">Quantidade de dias a partir de hoje</label>
            <input type="text" name="diasAdd" class="form-control" id="diasAdd" placeholder="Informe a quantidade de dias" autocomplete="off">
        </div>

    </div>

    <div class="form-row">
        <div class="form-group col-md">
            <label for="motivo">Motivo e observações</label>
            <textarea class="form-control" id="motivo" name="motivo" aria-label="motivo"></textarea>
        </div>
        <div class="form-group col-md">
            <label for="documentos">Documentos</label>
            <input type="file" name="documentos[]" class="form-control" id="documentos" accept="application/pdf" multiple>
        </div>
    </div>
    <div id="flashRetorno">

    </div>
    <button id="submitMudancaCela" type="submit" onclick="return confirm('Deseja confirmar o registro?');" class="btn btn-primary"><i class="fas fa-check"></i> Realizar mudança</button>

</form>