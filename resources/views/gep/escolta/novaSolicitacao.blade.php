@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fa fa-plus"></i> Nova Solicitação de Escolta</h3>
        <hr>
        <p>
            Nova de solicitação de escolta.
        </p>
    </div>
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))
                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div>

    <form action="{{ route('gep.storeNovaSolicitacao') }}" id="formNovaSolicitacao" method="post" enctype="multipart/form-data" class="mb-4 mt-5">
        {!! csrf_field() !!}
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="interno">Nome dos internos</label>
                <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome dos internos" autocomplete="off">
                <div class="form-group col-md" id="filter-records"></div>
            </div>
        </div>

        <h5 class="titleInterno">Internos <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableInternos" class="table-responsive">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Periculosidade</th>
                    <th scope="col">Facção</th>
                    <th scope="col">Liderança</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoInterno">

                </tbody>
                <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div id="fototipo"></div>
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
                            </div>
                            <div class="modal-body embed-responsive text-center">
                                <img src="" id="imagepreview" style="max-width:90%">
                            </div>
                            <hr/>
                            <div class="text-left">
                                <div id="fotolegenda"></div>
                                <div id="fotodatainserida"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </table>
        </div>

        <div class="form-row">

            <div class="form-group col-md-3">
                <label for="tipoEscolta">Tipo de Escolta</label>
                <select id="tipoEscolta" name="tipoEscolta" class="form-control" required>
                    <option value="">
                        Selecione
                    </option>
                    <option value="audiencia">
                        Audiência
                    </option>
                    <option value="diversas">
                        Diversas
                    </option>
                </select>
            </div>

            <div class="form-group col-md-6 displayNone" id="divDestino">
                <label for="destino">Destino</label>
                <input type="text" name="destino" class="form-control" id="destino" placeholder="Informe o destino">
            </div>

            <div class="form-group col-md-3 displayNone" id="divDestinoVara">
                <label for="varaDestino">Vara</label>
                <select id="varaDestino" name="varaDestino" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($varas as $vara)
                        <option value="{{ $vara->id }}">{{ $vara->vara }} - {{ $vara->comarca }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="data_saida">Data da Escolta</label>
                <input type="text" name="data_saida" class="form-control" id="data_saida" placeholder="Data de Saída" autocomplete="off" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="horarioEscolta">Horário da Escolta</label>
                <input type="text" name="horarioEscolta" class="form-control" id="horarioEscolta" placeholder="Horário da Escolta" autocomplete="off">
            </div>


            <div class="form-group col-md-3">
                <label for="documentos">Documentos</label>
                <input type="file" name="documentos[]" class="form-control" id="documentos" required accept="application/pdf" multiple>
            </div>

            <div class="form-group col-md-3" id="divHoraRetorno">
                <label for="documento">Número de Documento</label>
                <input type="text" name="documento" class="form-control" id="documento" placeholder="Informe o número do documento">
            </div>

            <div class="form-group col-md-3">
                <label for="responsavel">Responsável</label>
                <select id="responsavel" name="responsavel" class="form-control" required>
                    <option value="">
                        Selecione
                    </option>
                    <option value="6405">
                        FTPEN-JOÃO PESSOA
                    </option>
                    <!-- <option value="130">
                        GEP Caicó
                    </option>
                    <option value="140">
                        GEP Caraúbas
                    </option>
                    <option value="150">
                        GEP Mossoró
                    </option>
                    <option value="165">
                        GEP Pau dos Ferros
                    </option> -->
                </select>
            </div>


        </div>

        <div class="form-row">
            <div class="form-group col-md-9">
                <label for="motivo">Motivo e Observações</label>
                <textarea name="motivo" id="motivo" class="form-control"></textarea>
            </div>
        </div>

        <div id="flashRetorno"></div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Salvar Solicitação</button>


    </form>

@endsection

@push('css')
<link href="{{ asset('css/gep/escolta/novaSolicitacao.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/gep/escolta/novaSolicitacao.js') }}"></script>
@endpush