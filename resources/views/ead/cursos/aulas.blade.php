@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-4">
        <h3><i class="mdi mdi-filmstrip"></i> Curso SIAPEN-{{ config('app.uf') }} Online</h3>
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg) !!}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    <div class="row">

        <input type="hidden" id="progressoAula" value="{{ $progresso }}">
        <input type="hidden" id="idAula" value="{{ $aulaSelecionada->id }}">
        <input type="hidden" id="idProximaAula" value="{{ $proximaAula }}">
        <input type="hidden" id="idCurso" value="{{ $idCurso }}">

        <div class="col-md-9 align-top">
            <div id="videoContainer">
                <span class="textProximoAula displayNone"><i class="mdi mdi-skip-next"></i> Carregando Próxima Aula em <span class="countRegressive"></span></span>
                <video width="90%" controls="true" poster="" id="video">
                    <source type="video/mp4" src="{{asset('storage/app/arquivos/cursos/'.$idCurso.'/aulas/'.$aulaSelecionada->id.'/'.$aulaSelecionada->arquivo)}}"></source>
                </video>
            </div>
        </div>

        <div class="col-md-3">

            <h4 class="conteudo"><i class="mdi mdi-format-list-bulleted"></i> Conteúdo do Curso</h4>

            <div class="accordion mt-4">

                @foreach($dados as $dado)
                    <div class="card">

                        <div class="card-header" id="heading{{$dado["id"]}}">
                            <h2 class="mb-0">
                                @if($dado['conclusao'] == "S")
                                    <button class="btn btn-link modulosTitulo" type="button" data-toggle="collapse" data-target="#collapse{{$dado["id"]}}" aria-expanded="true" aria-controls="collapse{{$dado["id"]}}">
                                        {{ $dado["titulo"] }} <i class="mdi mdi-checkbox-marked ml-2 float-left"></i>
                                    </button>
                                @else
                                    <button class="btn btn-link modulosTitulo" type="button" data-toggle="collapse" data-target="#collapse{{$dado["id"]}}" aria-expanded="true" aria-controls="collapse{{$dado["id"]}}">
                                        {{ $dado["titulo"] }} <i class="mdi mdi-arrow-down-drop-circle float-left ml-2"></i>
                                    </button>
                                @endif
                            </h2>
                        </div>

                        @foreach($dado["aulas"] as $aula)

                            <div id="collapse{{$dado["id"]}}" class="collapse" aria-labelledby="heading{{$dado["id"]}}" data-parent="#accordionExample">
                                <div class="card-body">
                                    @if(!in_array($aula->id, $aulasConcluidas))

                                    <a class="dropdown-item itemAula" href="{{ route('ead.aulas', ['idCurso' => $idCurso, 'idAula' => $aula->id]) }}">
                                        <span class="addEndVideo"></span>{{ $loop->iteration }}. {{ $aula->titulo }}
                                        <i class="mdi mdi-play-circle iconePlay ml-2"></i><small>{{ TdataService::format_time($aula->duracao) }}</small></a>
                                        @else

                                        <a class="dropdown-item itemAula aulaAssistida" href="{{ route('ead.aulas', ['idCurso' => $idCurso, 'idAula' => $aula->id]) }}">
                                            <i class="mdi mdi-checkbox-marked fontSize-18px iconeAssistido ml-2"></i> {{ $loop->iteration }}. {{ $aula->titulo }}</a>

                                    @endif
                                </div>
                            </div>
                        @endforeach

                    </div>
                @endforeach

            </div>

        </div>
    </div>
    {{--<div id="status" class="incomplete">--}}
        {{--<span>Play status: </span>--}}
        {{--<span class="status complete">COMPLETE</span>--}}
        {{--<span class="status incomplete">INCOMPLETE</span>--}}
        {{--<br />--}}
    {{--</div>--}}
    {{--<div>--}}
        {{--<span id="played">0</span> seconds out of--}}
        {{--<span id="duration"></span> seconds. (only updates when the video pauses)--}}
    {{--</div>--}}

@endsection

@push('css')
<link href="{{ asset('css/ead/cursos/aulas.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/ead/cursos/aula.js') }}"></script>
@endpush

