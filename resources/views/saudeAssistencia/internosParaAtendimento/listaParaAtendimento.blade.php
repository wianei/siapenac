@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-format-list-numbers"></i> Lista de Internos para Atendimento</h3>
        <hr>
        <p>
            Internos que estão na lista para atendimento.
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

    <div class="form-row">

        <div class="form-group col-sm-3">
            <label for="tipoAtendimento">Tipo de Atendimento</label>
            <select id="tipoAtendimento" name="tipoAtendimento" class="form-control">
                <option value="all">
                    Todos
                </option>
                <option value="med">Médico</option>
                <option value="odont">Odontológico</option>
                <option value="assistSocial">Assistente Social</option>
                <option value="assistJur">Assistência Jurídica</option>
                <option value="psi">Psicológico</option>
            </select>
        </div>

        <div class="form-group col-sm-3">
            <label for="pavilhao">Pavilhão</label>
            <select id="pavilhao" name="pavilhao" class="form-control selectUnidade">
                <option value="all">
                    Todos
                </option>
                @foreach($pavilhao as $pv)
                    <option value="{{ str_replace(" ", "", $pv['nome']) }}">
                        {{ $pv['nome'] }}
                    </option>
                @endforeach
            </select>
        </div>

    </div>
<form action="{{ route('saudeAssistencia.atendimentoRealizado') }}" method="post">
<div id="med" class="tipoAtendimento med">
    <h5 class="titleInterno mt-5">Internos para Atendimento Médico<i class="mdi mdi-arrow-bottom-right"></i></h5>
    <div id="tableInternos" class="table-responsive-md">
        <table class="table mb-3">
            <thead class="background_e65251 color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Prontuário</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">Prioridade</th>
                <th scope="col">Localização</th>
            </tr>
            </thead>
            <tbody id="retornoInterno" class="retornoInternos">
            @if(isset($lista))
                @foreach($lista as $list)
                    @if($list->tipo_atendimento == "Médico")
                    <tr class="{{ str_replace(" ","",$list->pvNome) }}">
                        <td>
                            <input type="checkbox" name="internosCelaCheck[]" class="checkbox checkBoxMedico" value="{{ $list->idSaudeAssit }}">
                        </td>
                        <td>{{ $list->id }}</td>
                        <td>
                            @if(file_exists(storage_path('app/arquivos/internos/fotos/' . $list->id . "/" . FotoService::getFotoPrincipalByIdTipo($list->id, 'interno') )))
                                <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $list->id . "/". FotoService::getFotoPrincipalByIdTipo($list->id, 'interno'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                            @else
                                <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                            @endif
                        </td>
                        <td>{{ $list->nome }}</td>
                        <td>
                            @if($list->prioridade == "A")
                                Alta
                                @elseif($list->prioridade == "B")
                                    Média
                                @elseif($list->prioridade == "C")
                                    Baixa
                            @endif
                        </td>
                        <td>{{ $list->sigla }}</td>
                    </tr>
                    <tr><td colspan="7" style="border-bottom: 1px solid #CCCCCC"><i>Descrição: {{ $list->descricao }}</i></td></tr>
                    @endif
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
</div>

<div id="odont" class="tipoAtendimento odont">
    <h5 class="titleInterno mt-5">Internos para Atendimento Odontológico<i class="mdi mdi-arrow-bottom-right"></i></h5>
    <div id="tableInternos" class="table-responsive-md">
        <table class="table mb-3">
            <thead class="background_81c784 color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Prontuário</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">Prioridade</th>
                <th scope="col">Localização</th>
            </tr>
            </thead>
            <tbody id="retornoInterno" class="retornoInternos">
            @if(isset($lista))
                @foreach($lista as $list)
                    @if($list->tipo_atendimento == "Odontológico")
                    <tr class="{{ str_replace(" ","",$list->pvNome) }}">
                        <td>
                            <input type="checkbox" name="internosCelaCheck[]" class="checkbox checkBoxMedico" value="{{ $list->idSaudeAssit }}">
                        </td>
                        <td>{{ $list->id }}</td>
                        <td>
                            @if(file_exists(storage_path('app/arquivos/internos/fotos/' . $list->id . "/" . FotoService::getFotoPrincipalByIdTipo($list->id, 'interno') )))
                                <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $list->id . "/". FotoService::getFotoPrincipalByIdTipo($list->id, 'interno'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                            @else
                                <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                            @endif
                        </td>
                        <td>{{ $list->nome }}</td>
                        <td>
                            @if($list->prioridade == "A")
                                Alta
                                @elseif($list->prioridade == "B")
                                    Média
                                @elseif($list->prioridade == "C")
                                    Baixa
                            @endif
                        </td>
                        <td>{{ $list->sigla }}</td>
                    </tr>
                    <tr><td colspan="7" style="border-bottom: 1px solid #CCCCCC"><i>Descrição: {{ $list->descricao }}</i></td></tr>
                    @endif
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
</div>

<div id="assistSocial" class="tipoAtendimento assistSocial">
    <h5 class="titleInterno mt-5">Internos para Atendimento com Assistente Social<i class="mdi mdi-arrow-bottom-right"></i></h5>
    <div id="tableInternos" class="table-responsive-md">
        <table class="table mb-3">
            <thead class="background_287ec3 color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Prontuário</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">Prioridade</th>
                <th scope="col">Localização</th>
            </tr>
            </thead>
            <tbody id="retornoInterno" class="retornoInternos">
            @if(isset($lista))
                @foreach($lista as $list)
                    @if($list->tipo_atendimento == "Assistente Social")
                    <tr class="{{ str_replace(" ","",$list->pvNome) }}">
                        <td>
                            <input type="checkbox" name="internosCelaCheck[]" class="checkbox checkBoxMedico" value="{{ $list->idSaudeAssit }}">
                        </td>
                        <td>{{ $list->id }}</td>
                        <td>
                            @if(file_exists(storage_path('app/arquivos/internos/fotos/' . $list->id . "/" . FotoService::getFotoPrincipalByIdTipo($list->id, 'interno') )))
                                <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $list->id . "/". FotoService::getFotoPrincipalByIdTipo($list->id, 'interno'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                            @else
                                <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                            @endif
                        </td>
                        <td>{{ $list->nome }}</td>
                        <td>
                            @if($list->prioridade == "A")
                                Alta
                                @elseif($list->prioridade == "B")
                                    Média
                                @elseif($list->prioridade == "C")
                                    Baixa
                            @endif
                        </td>
                        <td>{{ $list->sigla }}</td>
                    </tr>
                    <tr><td colspan="7" style="border-bottom: 1px solid #CCCCCC"><i>Descrição: {{ $list->descricao }}</i></td></tr>
                    @endif
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
</div>

<div id="psi" class="tipoAtendimento psi">
    <h5 class="titleInterno mt-5">Internos para Atendimento Psicológico<i class="mdi mdi-arrow-bottom-right"></i></h5>
    <div id="tableInternos" class="table-responsive-md">
        <table class="table mb-3">
            <thead class="background_8f8f8f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Prontuário</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">Prioridade</th>
                <th scope="col">Localização</th>
            </tr>
            </thead>
            <tbody id="retornoInterno" class="retornoInternos">
            @if(isset($lista))
                @foreach($lista as $list)
                    @if($list->tipo_atendimento == "Psicológico")
                    <tr class="{{ str_replace(" ","",$list->pvNome) }}">
                        <td>
                            <input type="checkbox" name="internosCelaCheck[]" class="checkbox checkBoxMedico" value="{{ $list->idSaudeAssit }}">
                        </td>
                        <td>{{ $list->id }}</td>
                        <td>
                            @if(file_exists(storage_path('app/arquivos/internos/fotos/' . $list->id . "/" . FotoService::getFotoPrincipalByIdTipo($list->id, 'interno') )))
                                <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $list->id . "/". FotoService::getFotoPrincipalByIdTipo($list->id, 'interno'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                            @else
                                <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                            @endif
                        </td>
                        <td>{{ $list->nome }}</td>
                        <td>
                            @if($list->prioridade == "A")
                                Alta
                                @elseif($list->prioridade == "B")
                                    Média
                                @elseif($list->prioridade == "C")
                                    Baixa
                            @endif
                        </td>
                        <td>{{ $list->sigla }}</td>
                    </tr>
                    <tr><td colspan="7" style="border-bottom: 1px solid #CCCCCC"><i>Descrição: {{ $list->descricao }}</i></td></tr>
                    @endif
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
</div>
    
<div id="assistJur" class="tipoAtendimento assistJur">
    <h5 class="titleInterno mt-5">Internos para Atendimento Jurídico<i class="mdi mdi-arrow-bottom-right"></i></h5>
    <div id="tableInternos" class="table-responsive-md">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Prontuário</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">Prioridade</th>
                <th scope="col">Localização</th>
            </tr>
            </thead>
            <tbody id="retornoInterno" class="retornoInternos">
            @if(isset($lista))
                @foreach($lista as $list)
                    @if($list->tipo_atendimento == "Assistência Jurídica")
                    <tr class="{{ str_replace(" ","",$list->pvNome) }}">
                        <td>
                            <input type="checkbox" name="internosCelaCheck[]" class="checkbox checkBoxMedico" value="{{ $list->idSaudeAssit }}">
                        </td>
                        <td>{{ $list->id }}</td>
                        <td>
                            @if(file_exists(storage_path('app/arquivos/internos/fotos/' . $list->id . "/" . FotoService::getFotoPrincipalByIdTipo($list->id, 'interno') )))
                                <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $list->id . "/". FotoService::getFotoPrincipalByIdTipo($list->id, 'interno'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                            @else
                                <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                            @endif
                        </td>
                        <td>{{ $list->nome }}</td>
                        <td>
                            @if($list->prioridade == "A")
                                Alta
                                @elseif($list->prioridade == "B")
                                    Média
                                @elseif($list->prioridade == "C")
                                    Baixa
                            @endif
                        </td>
                        <td>{{ $list->sigla }}</td>
                    </tr>
                    <tr><td colspan="7" style="border-bottom: 1px solid #CCCCCC"><i>Descrição: {{ $list->descricao }}</i></td></tr>
                    @endif
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
</div>
    <div class="form-row">

        <div class="form-group col-sm-3">
            <label for="dataAtendimento">Data do Atendimento</label>
            <input type="text" name="dataAtendimento" value="{{ $dados[0]->data_atendimento or old('dataAtendimento') }}" class="form-control" id="dataAtendimento" placeholder="Informe a data do Atendimento" autocomplete="off" required>
        </div>

        <div class="form-group col-sm-3">
            <button type="submit" id="btnRetirarSancao" class="btn btn-primary mt-4"><i class="fas fa-check"></i>Confirmar Atendimento</button>
        </div>

    </div>

{!! csrf_field() !!}

</form>

@endsection
@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="{{ asset('js/saudeAssistencia/listaInternosParaAtendimento.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
@endpush