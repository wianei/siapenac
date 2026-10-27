@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-security"></i> Permissões</h3>
        <hr>
        <p>
            Permissões do SIAPEN.
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


<h5>Permissões Cadastrados Para o Perfil {{ $nomePerfil }}</h5>

        <div class="table-responsive-md mt-3">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col" style="text-align: center">#</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach($permissoes as $permissao)
                <tr class="" @if($permissao['status'] == "I") style="opacity: 0.4" @endif>
                        <td scope="row" style="text-align: center">{{ $loop->iteration }} </td>
                        <td scope="row" @if($permissao['status'] == "A") style="color: green;" @endif>
                            
                            @if($permissao['status'] == "A") 
                                <i class="mdi mdi-check-circle"></i>
                            @endif

                            {{ $permissao['nome'] }}
                            
                        </td>
                        <td class="btn-acoes color_01579b">
                            
                            @if($permissao['status'] == 'A')
                                <a href="{{ route('setic.permissoesExcluirMetodo', [ 'idPerfil' => $idPerfil, 'idMetodo' => $permissao['id'] ]) }}" data-toggle="tooltip" data-placement="top" title="Excluir Método do Perfil {{ $nomePerfil }}"><i class="mdi mdi-close-box color_red"></i></a>
                            @endif
                            
                            @if($permissao['status'] == 'I')
                                <a href="{{ route('setic.permissoesIncluirMetodo', [ 'idPerfil' => $idPerfil, 'idMetodo' => $permissao['id'] ]) }}" data-toggle="tooltip" data-placement="top" title="Incluir Método no Perfil {{ $nomePerfil }}"><i class="mdi mdi-check-circle color_green"></i></a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>


@endsection
@push('css')
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
@endpush