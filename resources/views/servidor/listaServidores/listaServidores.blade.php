@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-location"></i> {{ $totalServidores }} Servidores Lotados na Unidade</h3>
        <hr>
        <p>
            lista de servidores Lotados na Unidade
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

    <div class="alert-warning">
        <p class=" alert alert-warning">
           O diretor da unidade prisional poderá alterar a permissão do usuário até o nível de controlador. Se houver dúvidas em relação ao que cada nível pode realizar, <a href="{{asset('arquivos/Permissoes.pdf')}}">clique aqui</a>.
            <br>Para novos cadastros, enviar a solicitação para {{ config('app.ti') }}.
        </p>
    </div>


    <div class="form-row">

        <div class="form-group col-sm-12">
            <label for="filtroNome">Filtrar por nome, matrícula ou permissão</label>
            <input type="text" name="filtroNome" class="form-control" id="filtroNome" placeholder="Filtrar por nome, matrícula ou permissão" autocomplete="off">
        </div>

    </div>


    <div class="table-responsive-md" id="tableServidores">
        <table class="table mt-2">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">Matrícula</th>
                <th scope="col">Telefone</th>
                <th scope="col">Permissão</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody id="bodyTableServidores">               
            @foreach($dados as $dado)
                <tr class="">
                    <td scope="row" class="fontSize-10px border1pxSolidRGBA0125"><i><strong>{{$loop->iteration  }}</strong></i></td>
                    <td class="border1pxSolidRGBA0125">
                        @if($dado->foto != null)
                            <img style="text-align: center" src="{{ $dado->foto }}" class="imagemSrc cursorPointer" alt="">
                            @else
                            <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                        @endif
                    </td>
                    <td class="border1pxSolidRGBA0125">{{ $dado->nome }}</td>
                    <td class="border1pxSolidRGBA0125">{{ $dado->matricula }}</td>
                    <td class="border1pxSolidRGBA0125">{{ $dado->fone }}</td>
                    <td class="border1pxSolidRGBA0125">{{ $dado->permissao }}</td>
                    <td class="btn-acoes color_01579b border1pxSolidRGBA0125">

                        
                            @if(PermissaoService::getVerifyPermissionView("ServidorController","alterarPermissao"))
                                <a href="{{ route('servidor.alterarPermissao', $dado->cpf) }}" data-toggle="tooltip" data-placement="top" title="Alterar Permissão do Servidor no ".config('app.name')><i class="mdi mdi-pencil-circle-outline"></i></a>
                            @endif
                        

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
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


@endsection

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/servidor/listaServidores.js') }}"></script>
@endpush

