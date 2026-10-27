@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-plus"></i> Ocorrência</h3>
        <hr>
        <p>
            Visualização da ocorrência.
        </p>
    </div>

    <h4 class="mb-5 mt-5 alert alert-primary"><i class="fas fa-save"></i> Visualização da ocorrência</h4>
    {{--{{ dd($dadosOcorrencia) }}--}}
    @foreach($dadosOcorrencia as $ocorrencia)
        <div class="ocorrencias mt-5">

            <div class="row ocorrencia">
                <div class="col-md-3">
                    {{ $ocorrencia['data'] }}  às {{ $ocorrencia['horario'] }}
                </div>
                <div class="col-md-9">
                    {{ $ocorrencia['natureza'] }}
                </div>
                <div class="cf"></div>
                <div class="col-md-12 mt-3 textP15px">
                    <p class="fontSize-15px">
                        {!! $ocorrencia['descricao'] !!}
                    </p>
                </div>
                <div class="col-md-12 fontSize-13px">
                    <i>
                        @if(strpos($ocorrencia['envol'],'inter,') >= 0)
                            @if(strpos($ocorrencia['envol'],'inter,') != false)
                                <span>Internos Envolvidos:</span>
                            @endif
                            @foreach($ocorrencia['envolvidos'] as $envolvidos)
                                @if(isset($envolvidos['interno']))
                                    @foreach($envolvidos['interno'] as $interno)
                                        {{$interno['nome'].", "}}
                                    @endforeach
                                @endif
                            @endforeach
                                <br>
                        @endif
                        @if(strpos($ocorrencia['envol'],'agnt,') >= 0)
                            @if(strpos($ocorrencia['envol'],'agnt,') != false)
                                <span>Agentes Envolvidos: </span>
                            @endif
                            @foreach($ocorrencia['envolvidos'] as $envolvidos)
                                @if(isset($envolvidos['servidor']))
                                    @foreach($envolvidos['servidor'] as $servidor)
                                        {{$servidor->nome.", "}}
                                    @endforeach
                                @endif
                            @endforeach
                                <br>
                        @endif


                        @if(strpos($ocorrencia['envol'],'adv') >= 0)
                            @if(strpos($ocorrencia['envol'],'adv') != false)
                                <span>Advogados Envolvidos: </span>
                            @endif
                            @foreach($ocorrencia['envolvidos'] as $envolvidos)
                                @if(isset($envolvidos['advogado']))
                                    @foreach($envolvidos['advogado'] as $advogado)
                                        {{$advogado->nome.", "}}
                                    @endforeach
                                @endif
                            @endforeach
                                <br>
                        @endif

                        @if(strpos($ocorrencia['envol'],'vis') >= 0)
                            @if(strpos($ocorrencia['envol'],'vis') != false)
                                <span>Visitas Envolvidas: </span>
                            @endif
                            @foreach($ocorrencia['envolvidos'] as $envolvidos)
                                @if(isset($envolvidos['visita']))
                                    @foreach($envolvidos['visita'] as $visita)
                                        {{$visita->nome.", "}}
                                    @endforeach
                                @endif
                            @endforeach
                        @endif

                    </i>
                </div>

                    <div class="col-md-6 mt-2">

                        @foreach($ocorrencia['arquivos'] as $arquivo)
                            <a href="{{ route('ocorrencia.arquivoVisualizar',['id'=>$ocorrencia['id'],'filename'=>$arquivo->nome_arquivo]) }}" class="btn btn-icons btn-primary fileDownload" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="{{ $arquivo->nome_arquivo }}"><i class="mdi mdi-cloud-download"></i></a>
                        @endforeach

                    </div>

                @if($ocorrencia['data']." ".$ocorrencia['horario'] >= $dataInicioPlantao." 08:00" && $ocorrencia['data']." ".$ocorrencia['horario'] <= $dataTerminoPlantao." 07:59")

                    <div class="col-md-6 mt-2">
                        <a href="{{ route('ocorrencia.excluirOcorrencia', $ocorrencia['id']) }}" class="btn btn-danger btn-fw ml-2 float-right"><i class="mdi mdi-delete-forever"></i> Excluir Ocorrência</a>

                        <a href="{{ route('ocorrencia.editar',$ocorrencia['id']) }}" class="btn btn-warning btn-fw float-right"><i class="mdi mdi-pencil"></i> Editar Ocorrência</a>
                    </div>

                @endif
                <div class="col-md-12 mt-4">
                    <div class="float-left  blockquote-footer">
                        <i>{{ $ocorrencia['registro'] }}</i>
                        @if($ocorrencia['atualizacao'] != false)
                            <br>
                            <i class="color_red">- Última atualização em {{ $ocorrencia['atualizacao'] }}, por {{ LogService::getDadosLogService($ocorrencia['id'], "ocorrencia","update")->name}}</i>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    @endforeach


@endsection

@push('css')
<link href="{{ asset('css/ocorrencia/cadastro/cadastro.css') }}" rel="stylesheet">
@endpush

@push('script')
@endpush

