@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-email"></i> Enviar Mensagem</h3>
        <hr>
        <p>
            Enviar mensagem para uma unidade, pavilhão ou servidor.
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

    <form action="{{ route('mensagem.enviarStore') }}" method="post" enctype="multipart/form-data">
 {{ csrf_field() }}
        @if(!isset($resposta))

        <div class="form-row">


            <div class="form-group col-sm-3">
                <label for="enviar_mensagem_para">Enviar mensagem para</label>
                <select id="enviar_mensagem_para" name="enviar_mensagem_para" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="unidade">
                        Unidade
                    </option>
                    <option value="servidor">
                        Servidor
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divPresidio">
                <label for="presidio">Unidade</label>
                <select id="presidio" name="presidio" class="form-control">
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

            <div class="form-group col-sm-3" id="divPavilhao">
                <label for="pavilhao">Pavilhão</label>
                <select id="pavilhao" name="pavilhao" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="1">
                        PV 01
                    </option>
                    <option value="2">
                        PV 02
                    </option>

                </select>
            </div>

            <button type="button" class="btn btn-icons btn-primary btnAddDestinatario">
                <i class="mdi mdi-plus"></i>
            </button>
        </div>


        <div class="form-row" id="divRowAgentes">

            <div class="form-group col-sm-12">
                <label for="agente">Nome do Servidor</label>
                <input type="text" name="agente" class="form-control" id="agente" placeholder="Informe o nome do agente ou matrícula" autocomplete="off">
                <div class="form-group col-sm-12" id="divRetornoAgentes"></div>

            </div>

        </div>
@endif

       

        <div class="form-row">

            <div class="form-group col-sm-12">
                <label for="destinatarios">Destinatários</label>
                <div class="destinatarios" id="divDestinatarios">
                    @if(isset($resposta))
                        <div class="destinatariosDiv">
                            <span> {{ $msgs[0]['remetente'] }} </span>
                            <input type="hidden" name="destimatario[]" id="destimatarioHidden" value="{{ $msgs[0]['remetenteMatricula'] }}"/>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <div class="form-row">
            <div class="form-group col-sm-12">
                <label for="assunto">Assunto</label>
                @if(isset($resposta))
                <input type="text" name="assunto" value="{{ "RE: ".$msgs[0]['assunto'] }}" class="form-control" id="assunto" placeholder="Informe o assunto" autocomplete="off" required>
                    @elseif(isset($encaminhar))
                <input type="text" name="assunto" value="{{ "ENC: ".$msgs[0]['assunto'] }}" class="form-control" id="assunto" placeholder="Informe o assunto" autocomplete="off" required>
                    @else
                    <input type="text" name="assunto" class="form-control" id="assunto" placeholder="Informe o assunto" autocomplete="off" required>
                @endif

            </div>

        </div>

        <div class="form-row">
            <label for="descricao">Mensagem</label>
        </div>

        <textarea name="descricao" id="descricao" class="form-control">
            @if(isset($resposta))
                <br>
                <br>
                |---------<br>
                | <em style="font-size: 08px !important;">{{ "Mensagem recebida em ".$msgs[0]['data']." às ".$msgs[0]['hora'] }}</em><br>
                |---------<br>

                {!! $msgs[0]['mensagem'] !!}
            @endif
            @if(isset($encaminhar))
                |---------<br>
                | <em>{{ "Mensagem recebida em ".$msgs[0]['data']." às ".$msgs[0]['hora'] }}</em><br>
                |---------<br>

                {!! $msgs[0]['mensagem'] !!}
            @endif
        </textarea>

        <div class="form-row mt-3">
            <div class="form-group col-sm-3">
                <label for="inputFile">Arquivos</label>
                <input type="file" name="inputFile[]" class="form-control" id="inputFile" accept="application/pdf" multiple>
            </div>
        </div>

        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="mdi mdi-send"></i> Enviar Mensagem</button>


    </form>

@endsection

@push('css')
<link href="{{ asset('css/mensagem/enviar/enviar.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/mensagem/enviar.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
@endpush