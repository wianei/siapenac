@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-file-document"></i> Justificativa de Não Realização de Escolta</h3>
        <hr>
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    <form action="{{ route('gep.justificativaNaoRealizacaoPdf') }}" id="formJustificativas" method="post">

        {!! csrf_field() !!}

        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="tipo">Tipo de Justificativa</label>
                <select id="tipo" name="tipo" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="aberto">Aberto</option>
                    <option value="acordo">Acordo</option>
                    <option value="alvara">Alvará</option>
                    <option value="condicional">Condicional</option>
                    {{--<option value="duasVaras">Duas Varas**</option>--}}
                    <option value="doente">Doente</option>
                    <option value="efetivo">Efetivo</option>
                    <option value="foragido">Foragido</option>
                    <option value="greve">Greve</option>
                    <option value="horario">Horário</option>
                    <option value="lapsoTemporal">Lapso Temporal</option>
                    <option value="localDistante">Local Distante</option>
                    <option value="monitoramento">Monitoramento</option>
                    <option value="naoLocalizado">Interno Não Localizado</option>
                    <option value="obito">Óbito</option>
                    <option value="operacao">Operação</option>
                    <option value="prisaoDomiciliar">Prisão Domiciliar</option>
                    <option value="presoDistante">Preso Distante</option>
                    <option value="presidioFederal">Presídio Federal</option>
                    <option value="quartelPm">Quartel PM</option>
                    <option value="semiaberto">Semiaberto</option>
                    {{--<option value="viatura">Viatura</option>--}}
                </select>
            </div>

            <div class="form-group col-sm-3" id="">
                <label for="nOficio">Nº do Ofício Inicial do FTPEN</label>
                <input type="text" name="nOficio" class="form-control" id="nOficio" autocomplete="off">
            </div>


            <div class="form-group col-sm-3 displayNone" id="divOpreracaoNaCela">
                <label for="opreracaoNaCela">Operação na Cela</label>
                <select id="opreracaoNaCela" name="opreracaoNaCela" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="sim">
                        Sim
                    </option>
                    <option value="Nao">
                        Nao
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3 displayNone" id="divDataEvento">
                <label for="dataEvento">Data do Evento</label>
                <input type="text" name="dataEvento" class="form-control" id="dataEvento" autocomplete="off">
            </div>

            <div class="form-group col-sm-3 displayNone" id="divDataEvento2">
                <label for="dataEvento2">Data do Evento 02</label>
                <input type="text" name="dataEvento2" class="form-control" id="dataEvento2" autocomplete="off">
            </div>

            <div class="form-group col-sm-3 displayNone" id="divHoraEvento">
                <label for="horaEvento">Hora do Evento</label>
                <input type="text" name="horaEvento" class="form-control" id="horaEvento" autocomplete="off">
            </div>

            <div class="form-group col-sm-6 displayNone" id="divTextoEvento">
                <label for="textoEvento">Texto do Evento</label>
                <input type="text" name="textoEvento" class="form-control" id="textoEvento" autocomplete="off">
            </div>

            <div class="form-group col-sm-6 displayNone" id="divTextoEvento2">
                <label for="textoEvento2">Texto do Evento 02</label>
                <input type="text" name="textoEvento2" class="form-control" id="textoEvento2" autocomplete="off">
            </div>

            <div class="form-group col-sm-3">
                <label for="dataDesignada">Data Designada</label>
                <input type="text" name="dataDesignada" class="form-control" id="dataDesignada" autocomplete="off">
            </div>

            <div class="form-group col-sm-3">
                <label for="horaDesignada">Hora Designada</label>
                <input type="text" name="horaDesignada" class="form-control" id="horaDesignada">
            </div>

            <div class="form-group col-sm-3">
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

            <div class="form-group col-sm-12 displayNone" id="divTextoEvento3">
                <label for="textoEvento3">Texto do Evento 02</label>
                <input type="text" name="textoEvento3" class="form-control" id="textoEvento3" autocomplete="off">
            </div>


        </div>

        <div class="form-row" id="formRowDivInternos">
            <div class="form-group col-sm-12">
                <label for="interno">Interno(s)</label>
                <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome do interno" autocomplete="off" disabled>
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

        <div id="flashRetorno"></div>
        <button type="submit" class="btn btn-primary"><i class="mdi mdi-file-document"></i>Gerar Documento</button>

    </form>

@endsection
@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{ asset('css/ocorrencia/buscar/buscar.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/gep/escolta/justificativaCancelamento.js') }}"></script>
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/outros/buscaInternoAI.js') }}"></script>
@endpush