@extends('template.tema_geral.theme')
@section('content')

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    <div class="titlePage color_365f80 mb-5">
        <div class="titlePage color_365f80 mb-5">
            <h3><i class="fas fa-map"></i> Mapa das Unidades</h3>
            <hr>
            <p>
                Mapa das Unidades do Sistema Penitenciário da {{ config('app.uf_extenso') }}.
            </p>
        </div>
    </div>


    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">

            <img src="{{asset('imagens/mapa/mapaUnidadesPrisionaisBa.jpg')}}" width="100%"/>

        </div>
    </div>


@endsection
@push('css')
<link rel="stylesheet" href="{{asset('css/home/home.css')}}">
@endpush
@push('script')
<script src="{{ asset('js/jquery.mask.js') }}"></script>
<script src="{{ asset('js/cadastro.js') }}"></script>


@endpush