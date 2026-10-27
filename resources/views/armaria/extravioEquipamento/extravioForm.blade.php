@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-3">
        <h3><i class="mdi mdi-cancel"></i>Extravio de Equipamento</h3>
        <hr>
        <p>
            Informar extravio de equipamento.
        </p>
    </div>


    <form action="{{ route('armaria.extravioEquipamentoStore' ,$id) }}" method="post" id="formDescautela">
        {!! csrf_field() !!}

        <h5>{{ $textoEquipamento[0]['equipamento'] }}</h5>


        @if($textoEquipamento[0]['tipoEquipamento'] != "armaFogo" && $textoEquipamento[0]['tipoEquipamento'] != 'colete' && $textoEquipamento[0]['tipoEquipamento'] != 'dei')
        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="quantidade">Quantidade</label>
                <input type="text" name="quantidade" class="form-control" id="quantidade" placeholder="Quantidade extraviada" autocomplete="off">
            </div>


        </div>
        @endif

        <div class="form-row">
            <label for="motivacao">Motivação</label>
            <textarea name="motivacao" id="motivacao" class="form-control" required></textarea>
        </div>
        <br/>


        <button type="submit" onclick="return confirm('Deseja realmente prosseguir com esta operação?');" class="btn btn-primary"><i class="mdi mdi-content-save"></i> Salvar Dados</button>

    </form>

@endsection

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/armaria/extravioEquipamentoForm.js') }}"></script>
@endpush
