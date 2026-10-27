@extends('template.tema_geral.theme')

@section('content')
    <style type="text/css">
        .tbodyAdv tr:nth-child(even) {background: #FFF !important;}
        .tbodyAdv tr:nth-child(odd) {background: #f2f8f9 !important;}
    </style>

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-search"></i> Buscar Advogado</h3>
        <hr>
        <p>
            Buscar advogado na base de dados.
        </p>
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg) !!}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    <form action="{{ route('advogado.buscar') }}" method="get">

        <div class="form-row">

            <div class="form-group col-sm-6">
                <label for="nome">Nome</label>
                <input type="text" name="nome" class="form-control" id="nome" placeholder="Nome">
            </div>

            <div class="form-group col-sm-3">
                <label for="oab_numero">Nº OAB</label>
                <input type="text" name="oab_numero" class="form-control" id="oab_numero" placeholder="Número da OAB">
            </div>

            <div class="form-group col-sm-3">
                <label class="form-check-label">Sexo</label><br>
                <div class="ml-1 fontSize-13px">
                    Masculino   <input type="checkbox" name="sexo[]" id="sexo" value="M" class="mr-3">
                    Feminino    <input type="checkbox" name="sexo[]" id="sexo" value="F">
                </div>

            </div>

        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar Advogado</button>

    </form>
    @if($dados != false)

        <div id="links" class="mt-5">
            {{ $dados->links() }}
        </div>

        @if($dados->total() > 0)
            <h6 class="mt-2"><i>Sua busca retornou
                    {{ $dados->total() }} resultados
                </i>
            </h6>

        @else

            <h5 class="mt-5"><i>Sua busca não obteve resultados! </i>
            </h5>

        @endif

        <div class="table-responsive-md mt-3">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    {{--<th scope="col">#</th>--}}
                    <th scope="col">OAB</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">CPF</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody class="tbodyAdv">
                @foreach($dados as $dado)
                    <tr class="">
                        {{--<td scope="row" class="fontSize-10px"><i><strong>{{($dados->currentPage() * $dados->perPage()) - $dados->perPage() + $loop->iteration  }}</strong></i></td>--}}
                        <td scope="row">{{ $dado->oab_numero}}</td>
                        <td>
                            @if(file_exists(storage_path('app/arquivos/advogados/fotos/' . $dado->id . "/" . FotoService::getFotoPrincipalByIdTipo($dado->id,'advogado'))))
                                <img style="text-align: center" src="{{asset('storage/app/arquivos/advogados/fotos/' . $dado->id . "/". FotoService::getFotoPrincipalByIdTipo($dado->id,'advogado'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                            @else
                                <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                            @endif

                        </td>
                        <td>{{ strtoupper($dado->nome) }}</td>
                        <td>{{ $dado->cpf }}</td>
                        <td class="btn-acoes color_01579b">
                            <a href="{{ route('advogado.show',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>

                            @if(PermissaoService::getVerifyPermissionView("AdvogadoController","editar"))
                            <a href="{{ route('advogado.editar',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>
                            @endif

                            {{-- @if(PermissaoService::getVerifyPermissionView("AdvogadoController","editar"))
                            <a href="{{ route('advogado.envioSenha',$dado->id) }}" data-toggle="tooltip" data-placement="top" title="Autorizar Acesso ao Agendamento de Atendimento"><i class="mdi mdi-checkbox-multiple-marked"></i></a>
                            @endif --}} 
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    @endif

    {{-- modal para abrir foto --}}
    <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div id="fototipo"></div>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
                </div>
                <div class="modal-body embed-responsive text-center">
                    <img src="" id="imagepreview" style="max-width:90%">
                </div>
                <hr/>
                <div class="text-left">
                    <div id="fotolegenda"></div>
                    <div id="fotodatainserida"></div>
                </div>
            </div>
        </div>
    </div>
    {{-- !! modal para abrir foto --}}

@endsection
@push('css')
<link href="{{ asset('css/interno/buscar/buscar.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/advogado/buscar.js') }}"></script>
@endpush