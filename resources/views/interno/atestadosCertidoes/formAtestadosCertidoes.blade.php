@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-file-document"></i> Atestados e Certidões</h3>
        <hr>
        <p>
            Emissão de atestados e certidões do interno. As certidões de cumprimento de alvará de soltura só será emitida se já tiver dado saída no interno por alvará.
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

    <form action="{{ route('interno.atestadosCertidoesStore') }}" id="formAtestadoCertidao" method="post">

        {!! csrf_field() !!}

        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="tipo">Tipo de Atestado/Certidão</label>
                <select id="tipo" name="tipo" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="acc">
                        Atestado de Conduta Carcerária (ACC)
                    </option>
                    <option value="apc">
                        Atestado Permanencia Carcerária
                    </option>
                    <option value="certidaocumprimento">
                        Certidão de Cumprimento de Alvará de Soltura
                    </option>
                    <option value="certidaonaocumprimento">
                        Certidão de NÃO Cumprimento de Alvará
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3 displayNone" id="divConduta">
                <label for="conduta">Conduta</label>
                <select id="conduta" name="conduta" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="Excelente">
                        Excelente
                    </option>
                    <option value="Boa">
                        Boa
                    </option>
                    <option value="Regular">
                        Regular
                    </option>
                    <option value="Má">
                        Má
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3 displayNone" id="divNumero">
                <label for="numero">Número</label>
                <input type="text" name="numero" value="0000/{{date("Y")}}" class="form-control" id="numero" placeholder="Número">
            </div>

            <div class="form-group col-sm-3 displayNone" id="divProcesso">
                <label for="processo">Nº do Processo</label>
                <input type="text" name="processo" value="" class="form-control" id="processo" placeholder="Número">
            </div>

            <div class="form-group col-sm-3 displayNone" id="divVara">
                <label for="vara">Vara Criminal e Comarca</label>
                <input type="text" name="vara" value="" class="form-control" id="vara" placeholder="Nome da Vara Criminal e Comarca">
            </div>

            <div class="form-group col-sm-9 displayNone" id="divJustificativa">
                <label for="justificativa">Justificativa da Negativa</label>
                <input type="text" name="justificativa" value="" class="form-control" id="justificativa" placeholder="Justificativa do não cumprimento">
            </div>

            <div class="form-group col-sm-3">
                <label for="assinatura">Assinatura do documento</label>
                <select id="assinatura" name="assinatura" class="form-control">
                    <option value="diretor">
                        Diretor da Unidade Prisional
                    </option>
                    <option value="viceDiretor">
                        Vice-Diretor da Unidade Prisional
                    </option>
                </select>
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
<script src="{{ asset('js/interno/atestadosCertidoes.js') }}"></script>
<script src="{{ asset('js/outros/buscaInterno.js') }}"></script>
<script src="{{ asset('js/outros/buscaAdvogado.js') }}"></script>
@endpush