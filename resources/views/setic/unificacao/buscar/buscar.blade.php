@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-users-cog"></i> Buscar Unificações</h3>
        <hr>
        <p>
            Buscar unificações realizadas através do menu Unificar do {{ config('app.name') }}.
        </p>
    </div>
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))
                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div>

    <form action="{{ route('setic.unificacao.buscar.buscar') }}" method="get">

        <div class="form-row">

            <div class="form-group col-3">
                <label for="tipo">Tipo</label>
                <select id="tipo" name="tipo" class="form-control">
                    <option value="" selected>
                        Todas
                    </option>
                    <option value="interno">
                        Internos
                    </option>
                    <option value="visita">
                        Visitas
                    </option>
                    <option value="advogado">
                        Advogados
                    </option>
                </select>
            </div>

            <div class="form-group col-3">
                <label for="gerado">Prontuário Gerado</label>
                <input type="text" name="gerado" class="form-control" id="gerado" placeholder="Prontuário Gerado">
            </div>
            
            <div class="form-group col-3">
                <label for="principal">Prontuário Principal</label>
                <input type="text" name="principal" class="form-control" id="principal" placeholder="Prontuário Principal">
            </div>
            
            <div class="form-group col-3">
                <label for="secundario">Prontuário Secundário</label>
                <input type="text" name="secundario" class="form-control" id="secundario" placeholder="Prontuário Secundário">
            </div>

            <div class="form-group col-3">
                <label for="apos">Unificações após:</label>
                <input type="text" class="form-control" name="apos" id="apos" placeholder="00/00/0000">
            </div>

            <div class="form-group col-3">
                <label for="ate">Unificações até:</label>
                <input type="text" class="form-control" name="ate" id="ate" placeholder="00/00/0000">
            </div>


        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar Unificações</button>

    </form>

    @if($unificacoes != false)

        <div id="links" class="mt-5">
            {{ $unificacoes->links() }}
        </div>

        @if($unificacoes->total() > 0)
            <form action="{{ route('interno.gerarListaBusca') }}" method="post">
                {!! csrf_field() !!}
                <h6 class="mt-2"><i>Sua busca retornou
                        {{ $unificacoes->total() }} resultados
                    </i>
                </h6>
                {{--<button type="submit" class="btn btn-primary"><i class="fas fa-print"></i> Imprimir Busca</button>--}}
            </form>
        @else

            <h5 class="mt-5"><i>Sua busca não obteve resultados! </i>
            </h5>

        @endif

        <div class="table-responsive">
        <table class="table mt-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">Tipo</th>
                <th scope="col">Prontuário Gerado</th>
                <th scope="col">Prontuário Principal</th>
                <th scope="col">Prontuário Secundário</th>
                <th scope="col">Data de Realização</th>
            </tr>
            </thead>
            <tbody>
                @foreach($unificacoes as $unificacao)
                    <tr>
                        <td scope="row">{{ $unificacao->fk_tabela }}</td>
                        <td scope="row" @if($unificacao->idGeradoStatus == "I") class="background_ffd7d7" @endif><a href="{{ route($unificacao->fk_tabela.'.show',$unificacao->fk_novo) }}" data-toggle="tooltip" data-placement="top" title="Detalhes">{{ $unificacao->fk_novo }}</a></td>
                        <td scope="row" @if($unificacao->idAStatus == "I") class="background_ffd7d7" @endif>{{ $unificacao->fk_A }}</td>
                        <td scope="row" @if($unificacao->idBStatus == "I") class="background_ffd7d7" @endif>{{ $unificacao->fk_B }}</td>
                        <td scope="row">{{ $unificacao->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
        <div id="links" class="mt-5">
            {{ $unificacoes->links() }}
        </div>

    @endif
@endsection
@push('css')
<link href="{{ asset('css/interno/buscar/buscar.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
<script src="{{ asset('js/interno/buscar.js') }}"></script>
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
@endpush