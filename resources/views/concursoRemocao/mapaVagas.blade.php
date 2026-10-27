@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-map"></i> Mapas de Vagas</h3>
        <hr>
        <p>
            Mostra o mapa das vagas disponíveis por Unidade Prisional.
        </p>
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg)  !!}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    <div class="alert alert-warning fontSize-13px mb-3">
        <strong>Atenção!</strong><br>
        As vagas do COMPLEXO PENAL JOÃO CHAVES são destinadas à unidade MASCULINA<br>
    </div>

    <br>

    <div class="mt-3">
        <div class="row">
            <div class="col-sm-3 fontSize-10px"><button class="btn btn-success"></button> - Vagas disponíveis</div>
            <div class="col-sm-3 fontSize-10px"><button class="btn btn-warning"></button> - Servidores candidatos à vaga</div>
        </div>
    </div>
    <div class="table-responsive-md mt-3">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                {{--<th scope="col">#</th>--}}
                <th scope="col">Unidade</th>
                <th class="text-center" scope="col">Vagas 1ª turma 2001 / Candidatos</th>
                <th class="text-center" scope="col">Vagas 2ª turma 2009 / Candidatos</th>
                <th class="text-center" scope="col">Vagas 3ª turma 2017 / Candidatos</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados as $dado)
                <tr class="">
                    {{--<td scope="row" class="fontSize-10px"><i><strong>{{($dados->currentPage() * $dados->perPage()) - $dados->perPage() + $loop->iteration  }}</strong></i></td>--}}
                    <td class="border1pxSolidRGBA0125">{{ $dado->nome}} - {{ $dado->sigla }}</td>
                    <td class="border1pxSolidRGBA0125 text-center"><button class="btn btn-success">{{ $dado->vagas_primeira_turma }}</button> - <button class="btn btn-warning">{{ $dado->qtdTurma1 }}</button></td>

                    <td class="border1pxSolidRGBA0125 text-center"><button class="btn btn-success">{{ $dado->vagas_segunda_turma }}</button> - <button class="btn btn-warning">{{ $dado->qtdTurma2 }}</button></td>

                    <td class="border1pxSolidRGBA0125 text-center"><button class="btn btn-success">{{ $dado->vagas_terceira_turma }}</button> - <button class="btn btn-warning">{{ $dado->qtdTurma3 }}</button></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/concursoRemocao/escolhaLotacao.js') }}"></script>
@endpush