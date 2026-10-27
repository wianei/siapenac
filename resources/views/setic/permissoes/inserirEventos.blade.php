@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-security"></i> Permissões</h3>
        <hr>
        <p>
            Permissões do SIAPEN.
        </p>
    </div>


    <form action="">
        {{ csrf_field() }}
        <div class="form-row">

            <input type="hidden" name="idPerfil" value="{{ $id }}" id="idPerfil">
            <div class="form-group col-sm-12">
                <label for="controller">Controller</label>
                <select id="controller" name="controller" class="form-control">
                    <option value="">
                        Selecione o Controller
                    </option>
                    
                    @foreach($controllers as $controller)
                    <option value="{{ $controller->id }}">{{ $controller->texto }}</option>
                    @endforeach
                
                </select>
            </div>

        </div>
    </form>

<hr>

<form action="{{ route('setic.permissoesInserirEventoStore', $id) }}" method="post" class="mt-5 displayNone formInserirEventos">
    {{ csrf_field() }}
        <h5 id="nomeEvento">Eventos</h5>
        <div class="table-responsive-md mt-3">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col"><input type="checkbox" name="checkAll" id="checkAll" value=""></th>
                    <th scope="col">Nome</th>
                </tr>
                </thead>
                <tbody id="retornoEventos">
                </tbody>
            </table>
        </div>

        <button type="submit" id="" class="btn btn-primary mt-3"><i class="fa fa-save"></i> Salvar Permissões</button>
</form>

@endsection
@push('css')
@endpush

@push('script')
<script src="{{ asset('js/setic/inserirEventos.js') }}"></script>
@endpush