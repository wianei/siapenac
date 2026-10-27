@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-note-text"></i> Inserir Artigos e Processos</h3>
        <hr>
        <p>
            Cadastrar artigo de crime ou processo que o interno responda.
        </p>
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->


    <form id="cadastroForm" action="{{ route('artigosProcessos.cadastroStore') }}" method="post">

        {!! csrf_field() !!}

        <div class="form-row" id="formRowDivInternos">
            <div class="form-group col-sm-12">
                <label for="interno">Interno(s)</label>
                <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome do interno" autocomplete="off">
                <div class="form-group col-sm-12" id="filter-records"></div>
            </div>
        </div>

        <h5 class="titleInterno">Internos <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableInternos" class="table-responsive-md">
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


        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="tipo">Tipo de Cadastro</label>
                <select id="tipo" name="tipo" class="form-control" required>
                    <option value="">
                        Selecione
                    </option>
                    <option value="inqueritoPolicial">Inquérito Policial</option>
                    <option value="processo">Processo</option>
                    <option value="condenacao">Condenação</option>
                </select>
            </div>



        </div>

        <div class="form-row border1pxSolid_CCC p-3 displayNone tipoCadastro" id="divInquerito">
            <div class="form-group col-sm-3">
                <label for="numeroInquerito">Número do Inquérito</label>
                <input type="text" name="numeroInquerito" class="form-control" id="numeroInquerito" placeholder="Informe o número do inquérito policial">
            </div>

            <div class="form-group col-sm-3">
                <label for="dataInquerito">Data do Inquérito Policial</label>
                <input type="text" name="dataInquerito" class="form-control" id="dataInquerito" placeholder="Informe a data do inquérito policial">
            </div>

            <div class="form-group col-sm-3">
                <label for="dataFato">Data do Fato</label>
                <input type="text" name="dataFato" class="form-control" id="dataFato" placeholder="Informe a data que o fato ocorreu">
            </div>

            <div class="form-group col-sm-3">
                <label for="dataPrisao">Data da Prisão</label>
                <input type="text" name="dataPrisao" class="form-control" id="dataPrisao" placeholder="Informe a data da prisão">
            </div>

            <div class="form-group col-sm-3">
                <label for="ufInquerito">UF</label>
                <select id="ufInquerito" name="ufInquerito" class="form-control selectEstados">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidadesFederativas as $uf)
                        <option value="{{ $uf->uf }}">{{ $uf->nome }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="municipioInquerito">Município</label>
                <select id="municipioInquerito" name="municipioInquerito" class="form-control">
                </select>
            </div>

            <div class="form-group col-sm-6">
                <label for="delegacia">Delegacia</label>
                <input type="text" name="delegacia" class="form-control" id="delegacia" placeholder="Informe a delegacia">
            </div>

            <div class="form-group col-sm-3">
                <label for="motivoPrisao">Motivo da Prisão</label>
                <select id="motivoPrisao" name="motivoPrisao" class="form-control">
                    <option value="Nenhum">Nenhum</option>
                    <option value="Cumprimento de Sentença">Cumprimento de Sentença</option>
                    <option value="Decorrente de Sentença de Pronúncia">Decorrente de Sentença de Pronúncia</option>
                    <option value="Flagrante">Flagrante</option>
                    <option value="Mandado de Prisão Preventiva">Mandado de Prisão Preventiva</option>
                    <option value="Mandado de Prisão Provisório">Mandado de Prisão Provisório</option>
                    <option value="Mandado de Prisão Temporária">Mandado de Prisão Temporária</option>
                    <option value="Mandado de Prisão Condenatório">Mandado de Prisão Condenatório</option>
                    <option value="Recomendação de Prisão">Recomendação de Prisão</option>
                    <option value="Sentença Penal Recorrível">Sentença Penal Recorrível</option>
                </select>
            </div>
        </div>

        <div class="form-row border1pxSolid_CCC p-3 displayNone tipoCadastro" id="divProcesso">

            <div class="form-group col-sm-3">
                <label for="numeroProcesso">Nº do Processo</label>
                <input type="text" name="numeroProcesso" class="form-control" id="numeroProcesso" placeholder="Informe o número do processo">
            </div>

            <div class="form-group col-sm-3">
                <label for="dataProcesso">Data do Processo</label>
                <input type="text" name="dataProcesso" class="form-control" id="dataProcesso" placeholder="Informe a data do processo">
            </div>

            <div class="form-group col-sm-3">
                <label for="guiaRecolhimento">Guia de Recolhimento</label>
                <input type="text" name="guiaRecolhimento" class="form-control" id="guiaRecolhimento" placeholder="Informe a guia de recolhimento">
            </div>

            <div class="form-group col-sm-3">
                <label for="motivoPrisaoProcesso">Motivo da Prisão</label>
                <select id="motivoPrisaoProcesso" name="motivoPrisaoProcesso" class="form-control">
                    <option value="Nenhum">Nenhum</option>
                    <option value="Cumprimento de Sentença">Cumprimento de Sentença</option>
                    <option value="Decorrente de Sentença de Pronúncia">Decorrente de Sentença de Pronúncia</option>
                    <option value="Flagrante">Flagrante</option>
                    <option value="Mandado de Prisão Preventiva">Mandado de Prisão Preventiva</option>
                    <option value="Mandado de Prisão Provisório">Mandado de Prisão Provisório</option>
                    <option value="Mandado de Prisão Temporária">Mandado de Prisão Temporária</option>
                    <option value="Mandado de Prisão Condenatório">Mandado de Prisão Condenatório</option>
                    <option value="Recomendação de Prisão">Recomendação de Prisão</option>
                    <option value="Sentença Penal Recorrível">Sentença Penal Recorrível</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="esferaJustica">Esfera Judicial</label>
                <select id="esferaJustica" name="esferaJustica" class="form-control">
                    <option value="Estadual">Estadual</option>
                    <option value="Federal">Federal</option>
                    <option value="Militar">Militar</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="reu">Réu</label>
                <select id="reu" name="reu" class="form-control">
                    <option value="">Selecione</option>
                    <option value="Primário">Primário</option>
                    <option value="Reincidente">Reincidente</option>
                    <option value="Tecnicamente Primário">Tecnicamente Primário</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="ufProcesso">UF</label>
                <select id="ufProcesso" name="ufProcesso" class="form-control selectEstados">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidadesFederativas as $uf)
                        <option value="{{ $uf->uf }}">{{ $uf->nome }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="municipioProcesso">Município</label>
                <select id="municipioProcesso" name="municipioProcesso" class="form-control">
                </select>
            </div>

            <div class="form-group col-sm-6">
                <label for="varaProcesso">Vara</label>
                <input type="text" name="varaProcesso" class="form-control" id="varaProcesso" placeholder="Informe a vara do processo">
            </div>

        </div>

        <div class="form-row border1pxSolid_CCC p-3 displayNone tipoCadastro" id="divCondenacao">

            <div class="form-group col-sm-3">
                <label for="numeroProcessoCondenacao">Nº do Processo</label>
                <input type="text" name="numeroProcessoCondenacao" class="form-control" id="numeroProcessoCondenacao" placeholder="Informe o número do processo">
            </div>

            <div class="form-group col-sm-3">
                <label for="dataCondenacaoTransJulgado">Data do Trânsito em Julgado</label>
                <input type="text" name="dataCondenacaoTransJulgado" class="form-control" id="dataCondenacaoTransJulgado" placeholder="Informe a data da condenação">
            </div>

            <div class="form-group col-sm-3">
                <label for="dataCondenacaoSentenca">Data da Sentença</label>
                <input type="text" name="dataCondenacaoSentenca" class="form-control" id="dataCondenacaoSentenca" placeholder="Informe a data da sentença">
            </div>

            <div class="form-group col-sm-3">
                <label for="condenacaoAnos">Quantidade de Anos</label>
                <input type="text" name="condenacaoAnos" class="form-control" id="condenacaoAnos" placeholder="Informe a quantidade de anos">
            </div>

            <div class="form-group col-sm-3">
                <label for="condenacaoMeses">Quantidade de Meses</label>
                <input type="text" name="condenacaoMeses" class="form-control" id="condenacaoMeses" placeholder="Informe a quantidade de meses">
            </div>

            <div class="form-group col-sm-3">
                <label for="condenacaoDias">Quantidade de Dias</label>
                <input type="text" name="condenacaoDias" class="form-control" id="condenacaoDias" placeholder="Informe a quantidade de dias">
            </div>

            <div class="form-group col-sm-3">
                <label for="tipoPrisao">Tipo de Prisão</label>
                <select id="tipoPrisao" name="tipoPrisao" class="form-control">
                    <option value="">Selecione</option>
                    <option value="Detenção">Detenção</option>
                    <option value="Prisão Civil">Prisão Civil</option>
                    <option value="Prisão Simples">Prisão Simples</option>
                    <option value="Reclusão">Reclusão</option>
                </select>
            </div>

        </div>

        <div class="form-row mt-3 border1pxSolid_CCC p-3 displayNone" id="divDispositivoLegal">

            <div class="form-group col-sm-6">
                <label for="dispositivoLegal">Dispositivo Legal</label>
                <select id="dispositivoLegal" name="dispositivoLegal[]" class="form-control dispositivoLegal">
                    <option value="">Selecione</option>
                    @foreach($dispositivoLegal as $dispositivo)
                        <option value="{{ $dispositivo->id }}">{{ $dispositivo->nome }} - {{$dispositivo->texto}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-12 displayNone divArtigo">
                <label for="artigo">Artigo</label>
                <select id="artigo" name="artigo[]" class="form-control artigo">
                    <option value="">Selecione</option>
                </select>
            </div>

            <div class="form-group col-sm-6 displayNone divParagrafo">
                <label for="paragrafo"><span class="tipoParagrafo" id="tipoParagrafo"></span></label>
                <select id="paragrafo" name="paragrafo[]" class="form-control paragrafo">
                    <option value="">Selecione</option>
                </select>
            </div>

            <input type="hidden" name="tipoSequencia[]" class="tipoSequencia" id="tipoSequencia" value="">
            <input type="hidden" name="tipoSequenciaUm[]" class="tipoSequenciaUm" id="tipoSequenciaUm" value="">
            <input type="hidden" name="tipoSequenciaDois[]" class="tipoSequenciaDois" id="tipoSequenciaDois" value="">

            <div class="form-group col-sm-6 displayNone divSubDois">
                <label for="subDois"><span class="txtSubDois"></span></label>
                <select id="subDois" name="subDois[]" class="form-control subDois">
                    <option value="">Selecione</option>
                </select>
            </div>

            <div class="alert alert-warning col-12 mt-3 resumo displayNone">
            </div>

            <div class="col-6 mt-4">
                <button type="button" id="addDispositivo" class="btn btn-primary">Adicionar Novo Artigo +</button>
                <button type="button" class="btn btn-danger btnDelDispositivo" id="">Remover Campo -</button>
            </div>

        </div>

        <div id="divDispositivoLegalClone"></div>

        <div id="flashRetorno" class="mt-2"></div>
        <button type="submit" id="cadastrarFormBtn" class="btn btn-primary mt-3"><i class="fa fa-save"></i> Salvar Informações</button>

    </form>

@endsection
@push('css')
<link href="{{ asset('css/ocorrencia/buscar/buscar.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/artigosProcessos/cadastroArtigoProcessos.js') }}"></script>
@endpush