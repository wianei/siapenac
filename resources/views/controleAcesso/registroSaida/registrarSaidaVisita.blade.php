@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-logout"></i> Registro de Saída de Visitantes</h3>
        <hr>
        <p>
            Registrar a saída de visitantes da unidade.
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

    <form action="{{ route('controleAcesso.registrarSaidaVisitaStore') }}" method="post">
        {{ csrf_field() }}

        <h5 class="titleInterno">Visitas que Entraram na Unidade no Plantão em Aberto ({{ $dataInicioPlantao }} - {{ $dataTerminoPlantao }}) - TOTAL: {{ count($historico) }}<i class="mdi mdi-arrow-bottom-right"></i></h5>

        <div class="form-row">

            <div class="form-group col-sm-12">
                <label for="filtroNome">Filtrar por Nome ou CPF</label>
                <input type="text" name="filtroNome" class="form-control" id="filtroNome" placeholder="Filtrar por nome ou cpf" autocomplete="off">
            </div>

        </div>

        <div class="table-responsive-md">
            <table  class="table" id="tableVisitas">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">#</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">CPF</th>
                    <th scope="col">Entrada</th>
                </tr>
                </thead>
                <tbody id="bodyTableVisitas">
                @foreach($historico as $his)
                    <tr class="">
                        <td style="border-bottom: 1px solid #CCCCCC"><input type="checkbox" value="{{ $his['idControleAcesso'] }}" class="checkbox" name="checkVisitas[]"></td>
                        <td style="border-bottom: 1px solid #CCCCCC">
                            @if(file_exists(storage_path('app/arquivos/visitas/fotos/' . $his['id'] . "/" . FotoService::getFotoPrincipalByIdTipo($his['id'],'visita'))))
                                <img style="text-align: center" src="{{asset('storage/app/arquivos/visitas/fotos/' . $his['id'] . "/". FotoService::getFotoPrincipalByIdTipo($his['id'],'visita'))}}" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Perfil">
                            @else
                                <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                            @endif

                        </td>
                        <td style="border-bottom: 1px solid #CCCCCC">{{ $his['nome'] }}</td>
                        <td style="border-bottom: 1px solid #CCCCCC">{{ $his['cpf'] }}</td>
                        <td style="border-bottom: 1px solid #CCCCCC">{{ $his['dataUltimaEntrada'] }} - {{ $his['horaUltimaEntrada'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="form-row mt-5">

            <div class="form-group col-sm-3">
                <label for="dataSaida">Data da Saída</label>
                <input type="text" name="dataSaida" class="form-control" id="dataSaida" placeholder="Informe a data de saida" autocomplete="off" required>
            </div>

            <div class="form-group col-sm-3">
                <label for="horarioSaida">Horário da Saída</label>
                <input type="text" name="horarioSaida" class="form-control" id="horarioSaida" placeholder="Informe o horário de saida" autocomplete="off" required>
            </div>

        </div>

        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-save"></i> Registrar Saída</button>

    </form>

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
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/controleAcesso/registrarSaida/registrarSaidaVisita.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endpush


