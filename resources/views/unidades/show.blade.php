@extends('template.tema_geral.theme')

@section('content')
    <div class="table-responsive-md">
        <table class="table">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">Unidade Prisional</th>
                <th scope="col">Diretor</th>
                <th scope="col">Vice-Diretor</th>
                {{--<th scope="col">Endereço</th>--}}
                {{--<th scope="col">Telefone da Unidade</th>--}}
                {{--<th scope="col">Telefone da Unidade</th>--}}
                {{--<th scope="col">Endereço da Unidade</th>--}}
            </tr>
            <tbody>
            @foreach($unidades as $unidade)
            <tr class="">
                <td>{{$unidade['nome']}}</td>
                <td>{{$unidade['diretor']}}</td>
                <td>{{$unidade['vicediretor']}}</td>
                {{--<td>{{$unidade['email']}}</td>--}}
{{--                <td>{{$unidade['telefone1']}} @if($unidade['telefone2'])/ {{$unidade['telefone2']}}@endif</td>--}}
                {{--<td>{{$unidade['telefone2']}}</td>--}}
                {{--<td>{{$unidade['endereco']}}</td>--}}
            </tr>
            @endforeach
        </table>
    </div>
    <a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
@endsection
@push('css')
    <link href="{{ asset('css/unidades/show.css') }}" rel="stylesheet">
@endpush
@push('script')
    <script src="{{asset('js/unidades/show.js')}}"></script>
@endpush