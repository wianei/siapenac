@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-book-open-page-variant"></i> EAD - Lista de Cursos Online</h3>
        <hr>
        <p>
            Lista de Cursos online através do SIAPEN-{{ config('app.uf') }}
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

    <div class="card-deck col-4">

        @foreach($cursos as $curso)
        <div class="card">
            <img src="{{asset('storage/app/arquivos/cursos/'.$curso->id.'/imagens/'.$curso->foto)}}" class="card-img-top" alt="...">
            <div class="card-body">
                <h5 class="card-title">{{ $curso->titulo }}</h5>
                <p class="card-text">{{ $curso->resumo }}</p>
                <!-- <a href="#" class="btn btn-secondary"><i class="mdi mdi-arrow-top-right"></i> Mais Detalhes</a> -->
            </div>
            @if(!in_array($curso->id, $cursosInscrito))
                <a href="{{ route('ead.inscricaoConcurso', $curso->id) }}" class="btn btn-success"><i class="mdi mdi-check"></i> Realizar Inscrição</a>
            @else
                <a href="{{ route('ead.aulas', ['idCurso' => $curso->id, 'idAula' => 0]) }}" class="btn btn-primary"><i class="mdi mdi-check"></i> Inscrito - Abrir</a>
            @endif
            <div class="card-footer">
                {{--<small class="text-muted">Última atualização em 11/04/2020</small>--}}
            </div>
        </div>
        @endforeach

    </div>


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
