{{--@extends('template.tema_geral.theme')--}}

{{--@section('content')--}}

    {{--<div class="titlePage color_365f80 mb-5">--}}
        {{--<h3><i class="mdi mdi-bell"></i> Notificações</h3>--}}
        {{--<hr>--}}
        {{--<p>--}}
            {{--Visualização de notificações--}}
        {{--</p>--}}
    {{--</div>--}}

    {{--<h5 class="titleInterno mt-5">Internos Pendentes de Alocação em Cela<i class="mdi mdi-arrow-bottom-right"></i></h5>--}}
    {{--<div id="tableInternos" class="table-responsive-md">--}}
        {{--<table class="table mb-3">--}}
            {{--<thead class="background_e65251 color_FFF">--}}
            {{--<tr class="">--}}
                {{--<th scope="col">#</th>--}}
                {{--<th scope="col">Prontuário</th>--}}
                {{--<th scope="col">Foto</th>--}}
                {{--<th scope="col">Nome</th>--}}
                {{--<th scope="col">Alocar em Cela</th>--}}
            {{--</tr>--}}
            {{--</thead>--}}
            {{--<tbody id="retornoInterno" class="retornoInternos">--}}
            {{--@foreach($internosPendentesDeAlocacao as $interno)--}}
                {{--<tr class="">--}}
                    {{--<td>{{ $loop->iteration }}</td>--}}
                    {{--<td>{{ $interno->id }}</td>--}}
                    {{--<td>--}}
                        {{--@if($interno->nome_arquivo != null)--}}
                            {{--<img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $interno->id . "/$interno->nome_arquivo")}}" data-content="{{$interno->legenda}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="">--}}
                        {{--@else--}}
                            {{--<img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">--}}
                        {{--@endif--}}
                    {{--</td>--}}
                    {{--<td>{{ $interno->nome }}</td>--}}
                {{--</tr>--}}
            {{--@endforeach--}}
            {{--</tbody>--}}
        {{--</table>--}}
    {{--</div>--}}

    {{--<h5 class="titleInterno mt-5">Escoltas Solicitadas a FTPEN Canceladas<i class="mdi mdi-arrow-bottom-right"></i></h5>--}}
    {{--<div id="tableInternos" class="table-responsive-md">--}}
        {{--<table class="table mb-3">--}}
            {{--<thead class="background_30516f color_FFF">--}}
            {{--<tr class="">--}}
                {{--<th scope="col">#</th>--}}
                {{--<th scope="col">Prontuário</th>--}}
                {{--<th scope="col">Foto</th>--}}
                {{--<th scope="col">Nome</th>--}}
                {{--<th scope="col">Facção</th>--}}
                {{--<th scope="col">Liderança</th>--}}
                {{--<th scope="col">Localização</th>--}}
            {{--</tr>--}}
            {{--</thead>--}}
            {{--<tbody id="retornoInterno" class="retornoInternos">--}}
            {{--@foreach($escoltaGep as $escolta)--}}
                {{--<tr class="{{ str_replace(" ","",$escolta->pvNome) }}">--}}
                    {{--<td>{{ $loop->iteration }}</td>--}}
                    {{--<td>{{ $escolta->id }}</td>--}}
                    {{--<td>Foto</td>--}}
                    {{--<td>{{ $escolta->nome }}</td>--}}
                    {{--<td>{{ $escolta->facNome }}</td>--}}
                    {{--<td>{{ $escolta->lideranca }}</td>--}}
                    {{--<td>{{ $escolta->sigla }}</td>--}}
                {{--</tr>--}}
            {{--@endforeach--}}
            {{--</tbody>--}}
        {{--</table>--}}
    {{--</div>--}}


{{--@endsection--}}

{{--@push('script')--}}
{{--<script src="{{ asset('js/interno/separacaoInterno.js') }}"></script>--}}
{{--@endpush--}}