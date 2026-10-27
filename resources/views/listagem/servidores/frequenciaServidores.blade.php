@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-format-list-numbers"></i> Listagem de Frequência dos Servidores</h3>
        <hr>
        <p>
            Listagem de frequência dos servidores.
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

    <form action="{{ route('listagem.frequenciaServidoresStore') }}" method="post" enctype="multipart/form-data" id="formServidores">
        {!! csrf_field() !!}

        <div class="form-row" id="divRowAgentes">
            <div class="form-group col-sm-12">
                <label for="agente">Servidor</label>
                <input type="text" name="agente" class="form-control" id="agente" placeholder="Informe o nome do agente ou matrícula" autocomplete="off">
                <div class="form-group col-sm-12" id="divRetornoAgentes"></div>
            </div>

        </div>
        <button type="button" id="btnCarregaAgentes" class="btn btn-secondary btn-lg btn-block mb-3"><i class="mdi mdi-check-all"></i> Carregar Informações de Todos os Agentes da Unidade</button>


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
            <div class="form-group col-sm-3">
                <label for="mesImpressao">Mês de Impressão</label>
                <select id="mesImpressao" name="mesImpressao" class="form-control">
                    <option value="">Selecione</option>
                    <option value="1">Janeiro</option>
                    <option value="2">Fevereiro</option>
                    <option value="3">Março</option>
                    <option value="4">Abril</option>
                    <option value="5">Maio</option>
                    <option value="6">Junho</option>
                    <option value="7">Julho</option>
                    <option value="8">Agosto</option>
                    <option value="9">Setembro</option>
                    <option value="10">Outrubro</option>
                    <option value="11">Novembro</option>
                    <option value="12">Dezembro</option>
                </select>
            </div>

            <div class="form-group col-3">
                <label for="data">Data de Início</label>
                <input type="text" name="data" class="form-control" id="data" placeholder="Data" autocomplete="off">
            </div>

        </div>

        <div id="flashRetorno"> </div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary"><i class="fas fa-check"></i> Gerar Lista de Frequência</button>

    </form>

@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{ asset('css/listagem/servidores/frequenciaServidores.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
<script src="{{ asset('js/outros/buscaServidor.js') }}"></script>
<script src="{{ asset('js/listagem/servidores/frequenciaServidores.js') }}"></script>
@endpush