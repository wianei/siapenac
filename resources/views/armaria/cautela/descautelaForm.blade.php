@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-3">
        <h3><i class="mdi mdi-information"></i>Informação da Cautela</h3>
        <hr>
        <p>
            Dados da Cautela.
        </p>
    </div>


    <form action="{{ route('armaria.descautelaStore', $id) }}" method="post" id="formDescautela">
        {!! csrf_field() !!}
        <h5 class="titleEquipamentoCautela">Equipamentos Cautelados<i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="equipamentosEscolta" class="table-responsive-md">
            <table class="table mb-3">
                <thead class="background_287ec3 color_FFF">
                <tr class="">
                    <th><input type="checkbox" id="btnCheckTodosInternos"></th>
                    <th>Quantidade</th>
                    <th>Equipamentos da Cautela</th>
                </tr>
                </thead>
                <tbody id="retornoMensagens">
                @foreach($dadosEquipamentoCautela as $item)
                    <tr>
                        <td><input type="checkbox" name="equipamentoCheck[]" class="checkbox" id="" value="{{ $item['id'] }}"></td>
                        <td style="width: 10%">

                            @if($item['tipoEquipamento'] == "armaFogo" || $item['tipoEquipamento'] == "colete" || $item['tipoEquipamento'] == "dei")
                                {{--<input type="text" class="form-control" value="1" name="qtdMunicao[]" id="qtdMunicao">--}}
                            @else
                                <input type="text" class="form-control qtdEquipamento" value="{{ $item['quantidade'] }}" data-id="{{ $item['id'] }}" name="qtdMunicao[]" id="qtdMunicao">
                            @endif
                            <input type="hidden" value="{{ $item['quantidade']."/".$item['id']."/".$item['tipoEquipamento'] }}" data-name="{{ $item['tipoEquipamento'] }}" data-id="{{ $item['id'] }}" name="equipamento[]">

                        </td>
                        <td>{{ $item['equipamento'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <button type="submit" onclick="return confirm('Deseja realmente prosseguir com a descautela?');" class="btn btn-primary"><i class="mdi mdi-content-save"></i> Salvar Descautela</button>

    </form>

@endsection
@push('css')
<link href="{{ asset('css/armaria/mapa/mapaEquipamento.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/armaria/DescautelaForm.js') }}"></script>
@endpush
