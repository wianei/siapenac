@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-plus"></i> Cadastro de Ocorrências</h3>
        <hr>
        <p>
            Cadastro de ocorrência da unidade.
        </p>
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))
                <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg) !!}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    <button type="button" id="btnAddOcorrencia" class="btn btn-dark"><i class="fa fa-plus"></i> Inserir Ocorrência</button>


    <form action="{{ route('ocorrencia.store',['id'=>0, 'returnLivro' => $returnLivro]) }}" method="post" enctype="multipart/form-data" class="displayNone mt-5" id="formAddOcorrencia">

        {!! csrf_field() !!}

        <div class="form-row">

            <div class="form-group col-sm-6">
                <label for="natureza">Natureza</label>
                <select id="natureza" name="natureza" class="form-control" required>
                    <option value="">
                        Selecione
                    </option>
                    @foreach($naturezaOcorrencia as $natureza)
                        <option value="{{ $natureza->id }}">
                            {{ $natureza->natureza }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group col-sm-3 displayNone" id="divTipoObjeto">
                <label for="tipo_objeto">Tipo do Objeto/Substância Apreendida</label>
                <select id="tipo_objeto" name="tipo_objeto" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="dt_ocorrencia">Data da Ocorrência</label>
                <input type="text" name="dt_ocorrencia" class="form-control" id="dt_ocorrencia" placeholder="Informe a data da ocorrência" autocomplete="off" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="hr_ocorrencia">Horário da Ocorrência</label>
                <input type="text" name="hr_ocorrencia" class="form-control" id="hr_ocorrencia" placeholder="Informe o horário da ocorrência" autocomplete="off" required>
            </div>

        </div>

        <div class="table-responsive-md  mb-3 displayNone" id="divTableInternosObito">
            <h5>Marque o(s) Interno(s) que Foi(foram) a Óbito <i class="mdi mdi-arrow-bottom-right"></i></h5>
            <table  class="table">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">#</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Localização</th>
                </tr>
                </thead>
                <tbody id="internosObito" >
                </tbody>
            </table>
        </div>

        <div class="form-row" id="divRowAgentes">
            <div class="form-group col-sm-12">
                <label for="agente">Servidor(es) Envolvido(s)</label>
                <input type="text" name="agente" class="form-control" id="agente" placeholder="Informe o nome do agente ou matrícula" autocomplete="off">
                <div class="form-group col-sm-12" id="divRetornoAgentes"></div>
            </div>

        </div>

        <h5 class="titleAgente displayNone">Servidor <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableAgentes" class="table-responsive-md displayNone">
            <table class="table mb-3">
                <thead class="background_1c2331 color_FFF">
                <tr class="">
                    <th scope="col">Matrícula</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoAgente">

                </tbody>
            </table>
        </div>

        <div class="form-row">
            <div class="form-group col-sm-12">
                <label for="interno">Interno(s) Envolvido(s)</label>
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
            <div class="form-group col-sm-12">
                <label for="visita">Visita(s) Envolvida(s)</label>
                <input type="text" name="visita" class="form-control" id="visita" placeholder="Informe o nome da visita" autocomplete="off">
                <div class="form-group col-sm-12" id="filter-records-visita"></div>
            </div>
        </div>
        <h5 class="titleVisita displayNone">Visita(s) <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableVisitas" class="table-responsive-md displayNone">
            <table class="table mb-3">
                <thead class="background_81c784 color_FFF">
                <tr class="">
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">CPF</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoVisita">

                </tbody>
            </table>
        </div>

        <div class="form-row">
            <div class="form-group col-sm-12">
                <label for="advogado">Advogado(s) Envolvido(s)</label>
                <input type="text" name="advogado" class="form-control" id="advogado" placeholder="Informe o nome do advogado" autocomplete="off">
                <div class="form-group col-sm-12" id="filter-records-advogado"></div>
            </div>
        </div>
        <h5 class="titleAdvogado displayNone">Advogado(s) <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableAdvogados" class="table-responsive-md displayNone">
            <table class="table mb-3">
                <thead class="background_e65251 color_FFF">
                <tr class="">
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Nº OAB</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoAdvogado">

                </tbody>
            </table>
        </div>


        <div class="form-row">
            <label for="descricao">Descrição</label>
        </div>

        <textarea name="descricao" id="descricao" class="form-control" required></textarea>

        <div class="form-row mt-3">
            <div class="form-group col-sm-3">
                <label for="inputFile">Arquivos</label>
                <input type="file" name="inputFile[]" class="form-control" id="inputFile" accept="application/pdf" multiple>
            </div>
        </div>

        <div id="flashRetorno"></div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Salvar Ocorrência</button>


    </form>
    <h4 class="mb-5 mt-5 alert alert-primary"><i class="fas fa-save"></i> Ocorrências Salvas</h4>
{{--{{ dd($dadosOcorrencia) }}--}}
    @foreach($dadosOcorrencia as $ocorrencia)
    <div class="ocorrencias mt-5">

        <div class="row ocorrencia">
            <div class="col-md-3">
               {{ $ocorrencia['data'] }}  às {{ $ocorrencia['horario'] }}
            </div>
            <div class="col-md-9">
                {{ $ocorrencia['natureza'] }}
            </div>
            <div class="cf"></div>
            <div class="col-md-12 mt-3 textP15px">
                <p class="fontSize-15px">
                    {!! $ocorrencia['descricao'] !!}
                </p>
            </div>
            <div class="col-md-12 fontSize-13px">
                <i>
                    @if(strpos($ocorrencia['envol'],'inter') != false)
                        <span>Internos Envolvidos:</span>
                           @foreach($ocorrencia['envolvidos'] as $envolvidos)
                                @if(isset($envolvidos['interno']))
                                    @foreach($envolvidos['interno'] as $interno)
                                        {{$interno['nome'].", "}}
                                        @endforeach
                                @endif
                           @endforeach
                    @endif
                    @if(strpos($ocorrencia['envol'],'agnt,') >= 0)

                        <br>
                        <span>Agentes Envolvidos: </span>
                        @foreach($ocorrencia['envolvidos'] as $envolvidos)
                            @if(isset($envolvidos['servidor']))
                                @foreach($envolvidos['servidor'] as $servidor)
                                    {{$servidor->nome.", "}}
                                @endforeach
                            @endif
                        @endforeach
                    @endif


                    @if(strpos($ocorrencia['envol'],'adv') != false)
                        <br>
                        <span>Advogados Envolvidos: </span>
                        @foreach($ocorrencia['envolvidos'] as $envolvidos)
                            @if(isset($envolvidos['advogado']))
                                @foreach($envolvidos['advogado'] as $advogado)
                                    {{$advogado->nome.", "}}
                                @endforeach
                            @endif
                        @endforeach
                    @endif

                    @if(strpos($ocorrencia['envol'],'vis') != false)
                        <br>
                        <span>Visitas Envolvidas: </span>
                        @foreach($ocorrencia['envolvidos'] as $envolvidos)
                            @if(isset($envolvidos['visita']))
                                @foreach($envolvidos['visita'] as $visita)
                                    {{$visita->nome.", "}}
                                @endforeach
                            @endif
                        @endforeach
                    @endif

                </i>
            </div>

            <div class="col-md-6 mt-2">

                @foreach($ocorrencia['arquivos'] as $arquivo)
                    <a href="{{ route('ocorrencia.arquivoVisualizar',['id'=>$ocorrencia['id'],'filename'=>$arquivo->nome_arquivo]) }}" class="btn btn-icons btn-primary fileDownload" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo->nome_arquivo }}"><i class="mdi mdi-cloud-download"></i></a>
                @endforeach

            </div>
            <div class="col-md-6 mt-2">
                <a href="{{ route('ocorrencia.excluirOcorrencia', $ocorrencia['id']) }}" class="btn btn-danger btn-fw ml-2 float-right" onclick="return confirm('Deseja realmente excluir essa ocorrência?');"><i class="mdi mdi-delete-forever"></i> Excluir Ocorrência</a>

                <a href="{{ route('ocorrencia.editar',$ocorrencia['id']) }}" class="btn btn-warning btn-fw float-right"><i class="mdi mdi-pencil"></i> Editar Ocorrência</a>
            </div>
            <div class="col-md-12 mt-4">
                <div class="float-left  blockquote-footer">
                    <i>{{ $ocorrencia['registro'] }}</i>
                    @if($ocorrencia['atualizacao'] != false)
                        <br>
                        <i class="color_red">- Última atualização em {{ $ocorrencia['atualizacao'] }}, por {{ LogService::getDadosLogService($ocorrencia['id'], "ocorrencia","update")->name}}</i>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endforeach


@endsection

@push('css')
<link href="{{ asset('css/ocorrencia/cadastro/cadastro.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/ocorrencia/cadastro.js') }}"></script>
<script src="{{ asset('js/outros/buscaServidor.js') }}"></script>
<script src="{{ asset('js/outros/buscaVisita.js') }}"></script>
<script src="{{ asset('js/outros/buscaInterno.js') }}"></script>
<script src="{{ asset('js/outros/buscaAdvogado.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
@endpush

