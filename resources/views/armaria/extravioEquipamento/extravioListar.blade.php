@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-3">
        <h3><i class="mdi mdi-information"></i>Lista de Equipamentos Extraviados</h3>
        <hr>
        <p>
            Lista os equipamentos extraviados.
        </p>
    </div>

    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Equipamento</th>
                <th scope="col">Quantidade</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($equipamentos as $equipamento)
                <tr class="">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $equipamento['equipamento'] }}</td>
                    <td>{{ $equipamento['quantidade'] }}</td>
                    <td class="btn-acoes color_01579b">
                        @if($equipamento['tipo'] == 'armaFogo' || $equipamento['tipo'] == 'colete')

                            @if($equipamento['status'] == "I")
                                <a href="{{ route('armaria.extravioEquipamentoAtivar', $equipamento['id']) }}" data-toggle="tooltip" data-placement="top" title="Ativar Equipamento"><i class="mdi mdi-backup-restore"></i></a>

                                @else
                                <span class="color_green">[ATIVO]</span>
                            @endif
                        @endif

                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="border1pxSolidRGBA0125"><strong><em>Motivação:</em></strong> {{ $equipamento['motivacao'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>

@endsection

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/armaria/DescautelaForm.js') }}"></script>
@endpush
