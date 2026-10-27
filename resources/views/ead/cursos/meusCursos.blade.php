@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-book-open-page-variant"></i> EAD - Meus Cursos Online</h3>
        <hr>
        <p>
            Minha lista de cursos online.
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

    <div class="row">
        @foreach($meusCursos as $cursos)

            <div class="card-deck col-4">
                <div class="card">
                    <img src="{{asset('storage/app/arquivos/cursos/'.$cursos->cursoId.'/imagens/'.$cursos->foto)}}" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">{{ $cursos->titulo }}</h5>
                        <p class="card-text">{{ $cursos->resumo }}</p>
                        <a href="#" class="btn btn-secondary"><i class="fa fa-plus"></i> Mais Detalhes</a>
                    </div>
                    <a href="{{ route('ead.aulas', ['idCurso' => $cursos->cursoId, 'idAula' => 0]) }}" class="btn btn-primary"><i class="mdi mdi-movie"></i> Ver Aulas </a>

                    {{--<div class="card-footer">--}}
                    {{--<small class="text-muted">Última atualização em 11/04/2020</small>--}}
                    {{--</div>--}}
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
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
@endpush
