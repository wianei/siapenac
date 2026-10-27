@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-3">
        <h3><i class="mdi mdi-format-list-numbers "></i> Listagem de Uso de Material Bélico</h3>
        <hr>
        <p>
            Listagens dos relatórios de uso do material bélico.
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

    <form action="">

        <div class="form-row">

            <div class="form-group col-sm-3" id="divUnidade">
                <label for="unidade_prisional">Unidade</label>
                <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}" @if( isset( $interno ) && $unidade->id == $interno->unidade_prisional ) selected @endif>
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar Relatórios</button>

    </form>

    @if($dados != false)
    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Data</th>
                <th scope="col">Unidade</th>
                <th scope="col">Servidor</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dados as $dado)
                <tr class="">
                    <td scope="row" class="fontSize-10px"><i><strong>{{ $loop->iteration  }}</strong></i></td>
                    <td>{{$dado->data}}</td>
                    <td>{{$dado->unidade}}</td>
                    <td>{{ $dado->servidor  }} ({{ $dado->matricula }})</td>
                    <td class="btn-acoes color_01579b">
                        <a href="{{ route('armaria.visualizarRelatorioUsoMaterial',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Visualizar relatório"><i class="mdi mdi-information-outline"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

@endsection
@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/armaria/listarCautela.js') }}"></script>
@endpush

