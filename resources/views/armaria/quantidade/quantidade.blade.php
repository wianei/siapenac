@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-cart-plus"></i> Informar Quantidade</h3>
        <hr>
        <p>
            Informar a quantidade em estoque do equipamento.
        </p>
    </div>

    <form action="{{ route('equipamento.quantidadeUpdate', $id) }}" method="post">

        {!! csrf_field() !!}

        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="quantidade">Quantidade</label>
                <input type="text" name="quantidade" value="{{ $quantidade }}" class="form-control" id="quantidade" placeholder="Informe a quantidade">
            </div>
            <div class="form-group col-sm-3">
                <button type="submit" class="btn btn-primary mt-4"><i class="fas fa-save"></i> Salvar Quantidade</button>
            </div>
        </div>

    </form>


@endsection
