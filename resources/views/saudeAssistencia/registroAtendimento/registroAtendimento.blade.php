@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-plus"></i> Cadastro de Atendimento</h3>
        <hr>
        <p>
            Realizar o cadastro de atendimento.
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

    @if(isset($dados))
        <h3>{{ $nomeInterno }} - ({{ $dados[0]->fk_interno }})</h3>
        <br>
    @endif
    @if(isset($dados))
    <form action="{{ route('saudeAssistencia.editarAtendimentoStore',$id) }}" id="formRegistroAtendimento" method="post">
        @else
    <form action="{{ route('saudeAssistencia.cadastroAtendimentoStore') }}" id="formRegistroAtendimento" method="post">
    @endif
        <div class="form-row">
            @if(!isset($dados))
                <div class="form-group col-sm-3">
                    <label for="unidade_prisional">Unidade</label>
                    <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade" required>
                        <option value="">
                            Selecione
                        </option>
                        @foreach($unidades as $unidade)
                            <option value="{{ $unidade->id }}">
                                {{ $unidade->sigla }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="form-group col-sm-3">
                <label for="setor">Setor</label>
                <select id="setor" name="setor" class="form-control" required>
                    <option value="">
                        Selecione
                    </option>
                    <option value="Assistência Social" @if( isset( $dados ) && $dados[0]->setor == "Assistência Social") selected @endif>Assistência Social</option>
                    <option value="Assistência Jurídica" @if( isset( $dados ) && $dados[0]->setor == "Assistência Jurídica") selected @endif>Assistência Jurídica</option>

                    <option value="Clinica Médica" @if( isset( $dados ) && $dados[0]->setor == "Clinica Médica") selected @endif>Clinica Médica</option>
                    <option value="Controle de Dieta" @if( isset( $dados ) && $dados[0]->setor == "Controle de Dieta") selected @endif>Controle de Dieta</option>
                    <option value="Deficiência" @if( isset( $dados ) && $dados[0]->setor == "Deficiência") selected @endif>Deficiência</option>
                    <option value="Enfermagem" @if( isset( $dados ) && $dados[0]->setor == "Enfermagem") selected @endif>Enfermagem</option>
                    <option value="Enfermidade" @if( isset( $dados ) && $dados[0]->setor == "Enfermidade") selected @endif>Enfermidade</option>
                    <option value="Odontologia" @if( isset( $dados ) && $dados[0]->setor == "Odontologia") selected @endif>Odontologia</option>
                    <option value="Psicologia" @if( isset( $dados ) && $dados[0]->setor == "Psicologia") selected @endif>Psicologia</option>
                    <option value="Psiquiatria" @if( isset( $dados ) && $dados[0]->setor == "Psiquiatria") selected @endif>Psiquiatria</option>
                    <option value="Saúde" @if( isset( $dados ) && $dados[0]->setor == "Saúde") selected @endif>Saúde</option>
                    <option value="Terapia Ocupacional" @if( isset( $dados ) && $dados[0]->setor == "Terapia Ocupacional") selected @endif>Terapia Ocupacional</option>
                    <option value="Vacina" @if( isset( $dados ) && $dados[0]->setor == "Vacina") selected @endif>Vacina</option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="dataAtendimento">Data do Atendimento</label>
                <input type="text" name="dataAtendimento" value="{{ $dados[0]->data_atendimento or old('dataAtendimento') }}" class="form-control" id="dataAtendimento" placeholder="Informe a data do Atendimento" autocomplete="off" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="horaAtendimento">Hora do Atendimento</label>
                <input type="text" name="horaAtendimento" class="form-control" value="{{ $dados[0]->hora_atendimento or old('horaAtendimento') }}" id="horaAtendimento" placeholder="Informe a hora de inicio" required>
            </div>

            <div class="form-group col-sm-6">
                <label for="nomeMedico">Nome do Profissional</label>
                <input type="text" name="nomeMedico" class="form-control" value="{{ $dados[0]->nome_medico or old('nomeMedico') }}" id="nomeMedico" placeholder="Informe o nome do profissional que atendeu o interno" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="crmMedico">Número do Conselho</label>
                <input type="text" name="crmMedico" class="form-control" value="{{ $dados[0]->crm_medico or old('crmMedico') }}" id="crmMedico" placeholder="Informe o número da inscrição no conselho do profissional que atendeu o interno" required>
            </div>

        </div>
        {!! csrf_field() !!}

        @if(!isset($dados))
        <div class="form-row">
            <div class="form-group col-sm-12">
                <label for="interno">Nome do interno</label>
                <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome do interno" autocomplete="off">
                <div class="form-group col-sm-12" id="filter-records"></div>
            </div>
        </div>

        <div id="tableInternos" class="table-responsive-md displayNone">
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

                {{--@if(isset($advogado))--}}
                    {{--@foreach($dadosInterno as $di)--}}
                        {{--<tr class="">--}}
                            {{--<td scope="row">{{ $di['id'] }}</td>--}}
                            {{--<td>--}}
                                {{--@if($di['foto'] != null)--}}
                                    {{--<img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $di['id']. "/".$di['foto'])}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">--}}
                                {{--@else--}}
                                    {{--<img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">--}}
                                {{--@endif--}}

                            {{--</td>--}}
                            {{--<td>{{ $di['nome'] }}</td>--}}
                            {{--<td>{{ $di['faccao'] }}</td>--}}
                            {{--<td>{{ $di['lideranca'] }}</td>--}}
                            {{--<td>{{ $di['sigla'] }}</td>--}}
                            {{--<td class="btn-acoes color_01579b">--}}
                                {{--<a href="#" data-toggle="tooltip" data-placement="top" class="proibir" title="Proibir"><i class="mdi mdi-cancel color_red"></i></a>--}}

                                {{--<input type="hidden" name="internos[]" class="hiddenInternoId" id="internos{{ $di['id'] }}" value="{{ $di['id'] }}"/>--}}
                                {{--<a href="/interno/show/{{ $di['id'] }}" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>--}}
                            {{--</td>--}}
                        {{--</tr>--}}
                    {{--@endforeach--}}
                {{--@endif--}}

                </tbody>
            </table>
        </div>
        @endif

        <div class="form-row">
            <label for="descricao">Descrição</label>
        </div>

        <textarea name="descricao" id="descricao" class="form-control" required>@if(isset($dados)) {!! $dados[0]->descricao !!}  @endif</textarea>

        <div id="flashRetorno"></div>
        <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Salvar Dados</button>

    </form>
@endsection
@push('css')
<link href="{{ asset('css/saudeAssistencia/registrarAtendimento.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
    @if(isset($dados))
        <script src="{{ asset('js/saudeAssistencia/editarAtendimento.js') }}"></script>
            @else
        <script src="{{ asset('js/saudeAssistencia/registrarAtendimento.js') }}"></script>
        <script src="{{ asset('js/outros/buscaInterno.js') }}"></script>
    @endif
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
@endpush
