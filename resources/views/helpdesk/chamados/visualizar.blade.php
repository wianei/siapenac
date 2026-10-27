@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-file-document"></i> Chamado</h3>
        <hr>
        <p>
            Visualiza as interações do chamado.
        </p>

        @if($chamados[0]['status'] != 'F')
            <a href="{{ route('helpDesk.fecharChamado', $chamados[0]['id']) }}" data-toggle="tooltip" data-placement="top" title="Abrir Chamado com a {{ config('app.ti') }}" class="btn btn-danger text-right"><i class="mdi mdi-close"></i>Fechar Chamado</a>
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

    @foreach($chamados as $chamado)

        <div class="row mb-3">
            <div class="col-sm-9">Assunto: {{ $chamado['assunto'] }}</div>
            <div class="col-sm-3">
                Tipo de Atendimento:
                @if($chamado['prioridade'] == 'A')
                    Erro no Sistema
                @elseif($chamado['prioridade'] == 'B')
                    Dúvida
                @elseif($chamado['prioridade'] == 'C')
                    Sugestão
                @elseif($chamado['prioridade'] == 'D')
                    Unificação de Custodiados
                @endif
            </div>
            @if($chamado['status'] == 'F')
                <div class="col-sm-3">
                    ** Chamado Fechado **
                </div>
            @endif
        </div>

        @if($chamado['status'] != 'F')
        <a href="#" id="btResponder" data-toggle="tooltip" data-placement="top" title="Abrir Chamado com a {{ config('app.ti') }}" class="mb-3 btn btn-primary text-right"><i class="mdi mdi-send"></i>Responter</a>
        @endif

        <form action="{{ route('helpDesk.responderChamado', $chamado['id']) }}" id="formResponder" class="mb-3 displayNone" method="post" enctype="multipart/form-data">

            {{ csrf_field() }}

            <div class="form-row">
                <label for="descricao">Mensagem</label>
            </div>

            <textarea name="descricao" id="descricao" class="form-control"></textarea>

            <div class="form-row mt-3">
                <div class="form-group col-sm-3">
                    <label for="inputFile">Arquivos</label>
                    <input type="file" name="inputFile[]" class="form-control" id="inputFile" accept="image/jpeg,image/gif,image/png,application/pdf,image/x-eps" multiple>
                </div>
            </div>

            <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="mdi mdi-send"></i> Enviar Resposta</button>

            <button type="button" id="btnCancelForm" class="btn btn-danger mt-3"><i class="mdi mdi-cancel"></i> Cancelar Resposta</button>


        </form>


        <div>

        @if(count($chamado['interacoes']) > 0)
            @foreach($chamado['interacoes'] as $interacoes)

                @if(\Illuminate\Support\Facades\Auth::user()->cpf == $interacoes['user'])
                    <div class="alert-success p-3 fontSize-13px mb-3" style="border-radius: 40px 0px 20px 40px !important; border: 1px solid #8bbaa3;">
                        <div class="form-row color_333 text-black">
                            <div class="col-sm-6">
                                {{ $interacoes['nome'] }} (CPF: {{$interacoes['user']}}, Unidade Atual: {{\App\Http\Controllers\Facade\FacadeController::getLotacaoServidor($interacoes['user'],"S")}}) em {{ $interacoes['data'] }}
                            </div>
                            <div class="col-sm-12 mt-2 text-black">
                                {!!  $interacoes['descricao']  !!}
                            </div>
                            <div class="col-sm-12">
                                @if(count($interacoes['arquivos']) > 0)
                                    Arquivos:
                                    @foreach($interacoes['arquivos'] as $intArq)
                                        <a href="{{ route('helpDesk.arquivoChamado',['id'=>$chamado['id'], 'filename'=>$intArq->nome_arquivo]) }}">{{ $intArq->nome_arquivo }}</a>,
                                    @endforeach
                                @endif
                            </div>

                        </div>

                    </div>

                    @else

                        <div class="alert-dark p-3 fontSize-13px mb-3" style="border-radius: 0px 40px 40px 20px !important; border: 1px solid #a9a9a9;">
                            <div class="form-row color_333">
                                <div class="col-sm-6 float-right">
                                    {{ $interacoes['nome'] }} (CPF: {{$interacoes['user']}}, Unidade Atual: {{\App\Http\Controllers\Facade\FacadeController::getLotacaoServidor($interacoes['user'],"S")}}) em {{ $interacoes['data'] }}
                                </div>

                                <div class="col-sm-12 mt-2">
                                    {!!  $interacoes['descricao']  !!}
                                </div>

                                <div class="col-sm-12">
                                    @if(count($interacoes['arquivos']) > 0)
                                        Arquivos:
                                        @foreach($interacoes['arquivos'] as $intArq)
                                            <a href="{{ route('helpDesk.arquivoChamado',['id'=>$chamado['id'], 'filename'=>$intArq->nome_arquivo]) }}">{{ $intArq->nome_arquivo }}</a>,
                                        @endforeach
                                    @endif
                                </div>

                            </div>

                        </div>

                    @endif

            @endforeach
        @endif

        @if(\Illuminate\Support\Facades\Auth::user()->cpf != $chamado['user'])
        <div class="alert-dark p-3 fontSize-13px" style="border-radius: 0px 40px 40px 20px !important; border: 1px solid #a9a9a9;">
            <div class="form-row color_333">
                <div class="col-sm-6">
                    {{ $chamado['nomeUser'] }} (CPF: {{$chamado['user']}}, Unidade Atual: {{\App\Http\Controllers\Facade\FacadeController::getLotacaoServidor($chamado['user'],"S")}}) em {{ $chamado['created_at'] }}
                </div>
                <div class="col-sm-12 mt-2 text-black">
                    {!!  $chamado['descricao']  !!}
                </div>
                <div class="col-sm-12">
                    @if(count($chamado['arquivos']) > 0)
                        Arquivos:
                        @foreach($chamado['arquivos'] as $arquivo)
                            <a href="{{ route('helpDesk.arquivoChamado',['id'=>$chamado['id'], 'filename'=>$arquivo->nome_arquivo]) }}">{{ $arquivo->nome_arquivo }}</a>,
                        @endforeach
                    @endif
                </div>

            </div>

        </div>

            @else

                <div class="alert-success p-3 fontSize-13px" style="border-radius: 40px 0px 20px 40px !important; border: 1px solid #8bbaa3;">
                    <div class="form-row color_333">
                        <div class="col-sm-6">
                            {{ $chamado['nomeUser'] }} (CPF: {{$chamado['user']}}, Unidade Atual: {{\App\Http\Controllers\Facade\FacadeController::getLotacaoServidor($chamado['user'],"S")}}) em {{ $chamado['created_at'] }}
                        </div>
                        <div class="col-sm-12 mt-2 text-black">
                            {!!  $chamado['descricao']  !!}
                        </div>
                        <div class="col-sm-12">
                            Arquivos:
                            @foreach($chamado['arquivos'] as $arquivo)
                                <a href="{{ route('helpDesk.arquivoChamado',['id'=>$chamado['id'], 'filename'=>$arquivo->nome_arquivo]) }}">{{ $arquivo->nome_arquivo }}</a>,
                            @endforeach
                        </div>

                    </div>

                </div>

            @endif
    </div>
    @endforeach

@endsection

@push('script')
<script src="{{ asset('js/helpdesk/chamado/visualizar.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
@endpush
