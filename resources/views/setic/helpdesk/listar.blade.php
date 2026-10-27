@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-help-box"></i> Help Desk</h3>
        <hr>
        <p>
            Lista os chamados abertos e cria novos chamados para {{ config('app.ti') }}.
        </p>

        @if(!PermissaoService::getVerifyPermissionController("SeticController"))
            <a href="{{ route('helpDesk.abrirChamados') }}" data-toggle="tooltip" data-placement="top" title="Abrir Chamado com a {{ config('app.ti') }}" class="btn btn-primary text-right"><i class="mdi mdi-library-plus"></i>Abrir Chamado</a>
        @endif

    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    @if(count($chamados) > 0)
        <div class="table-responsive-md">
            <table class="table tableLista mt-5">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">ID</th>
                    <th scope="col">Data</th>
                    <th scope="col">Status</th>
                    <th scope="col">Assunto</th>
                    <th scope="col">Tipo Atend.</th>
                    <th scope="col">Última Interação</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chamados as $chamado)
                    <tr class="">
                        <td>
                            {{ $chamado['id'] }}
                        </td>
                        <td>{{ $chamado['created_at'] }}</td>
                        <td>
                            @if($chamado['status'] == 'A')
                                Aberto
                            @elseif($chamado['status'] == 'F')
                                Fechado
                            @endif

                                @if($chamado['interacoes'] > 0 && $chamado['status'] == 'A')
                                    <i class="mdi mdi-check-circle color_green"></i>
                                    @if($chamado['novaInteracao'] == 'S')
                                        <i class="mdi mdi-comment-alert color_orange"></i>
                                    @endif
                                @endif

                        </td>
                        <td>
                            @if(strlen($chamado['assunto']) > 50)
                                {{ substr($chamado['assunto'], 0, 50)  }}...
                                @else
                               {{  $chamado['assunto'] }}
                            @endif
                        </td>
                        <td>
                            @if($chamado['prioridade'] == 'A')
                                Erro no Sistema
                            @elseif($chamado['prioridade'] == 'B')
                                Dúvida
                            @elseif($chamado['prioridade'] == 'C')
                                Sugestão
                            @elseif($chamado['prioridade'] == 'D')
                                Unificação de Custodiados
                            @endif

                        </td>
                        <td>{{ $chamado['update_at']}}</td>
                        <td class="btn-acoes color_01579b">
                            <a href="{{ route('helpDesk.chamadosVisualizar',$chamado['id']) }}" data-toggle="tooltip" data-placement="top" title="Detalhes do Chamado"><i class="mdi mdi-information-outline"></i></a>

                            @if($chamado['status'] != 'F')
                                <a href="{{ route('helpDesk.fecharChamado', $chamado['id']) }}" data-toggle="tooltip" data-placement="top" title="Fechar Chamado" onclick="return confirm('Deseja realmente fechar este chamado?');"><i class="mdi mdi-close-box color_red"></i></a>
                            @endif

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif


@endsection
@push('css')
<link href="{{ asset('css/interno/buscar/buscar.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/advogado/buscar.js') }}"></script>
@endpush