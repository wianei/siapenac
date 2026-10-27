@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-security"></i> Permissões</h3>
        <hr>
        <p>
            Permissões do SIAPEN.
            <br>
            <a href="{{ route('setic.permissoesCadastrarPerfil') }}" class="btn btn-primary mt-3">
                <i class="fa fa-plus"></i> Cadastrar Novo Perfil de Permissão
            </a>

        </p>
    </div>



        <h5 id="nomeEvento">Lista de Perfis de Permissões do SIAPEN</h5>
        
        <div class="table-responsive-md mt-3">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">#</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="">
                    @foreach($perfis as $perfil)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $perfil->nome }}</td>
                        <td>{{ $perfil->tipo }}</td>
                        <td class="btn-acoes color_01579b">
                            
                        <a href="{{ route('setic.permissoesGerenciarEventos', $perfil->id) }}" data-toggle="tooltip" data-placement="top" title="Ver Permissões do Perfil {{ $perfil->nome }}"><i class="mdi mdi-lan"></i></a>

                        @if($perfil->id != 1 && $perfil->id != 10 && $perfil->id != 8)
                        <a href="{{ route('setic.permissoesExcluirPerfil', $perfil->id) }}" onclick="return confirm('Deseja realmente apagar o perfil {{ $perfil->nome }}');" data-toggle="tooltip" data-placement="top" title="Excluir o Perfil  {{ $perfil->nome }}"><i class="mdi mdi-delete color_red"></i></a>
                        @endif

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

@endsection
