@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-file-document"></i> Sindicância</h3>
        <hr>
        <p>
            Visualizar sindicância cadastrada.
        </p>
    </div>

    <div class="ocorrencias mt-5">
        <h4 class="mb-5 alert alert-primary"> {{ $dados[0]->titulo }}</h4>

        <div class="row ocorrencia">
            <div class="col-md-3">
                Data do Incidente{{ $dados[0]->data }}
            </div>
            <div class="col-md-6">
                Status: {{ $dados[0]->status }}
            </div>
            <div class="col-md-3">
                Gravidade: {{ $dados[0]->gravidade_falta }}
            </div>
            <div class="cf"></div>
            <div class="col-md-12 mt-3">
                <p class="fontSize-15px">
                    {!! $dados[0]->descricao !!}
                </p>
            </div>
            <div class="col-md-12">
                <i>
                    <span>Internos Envolvidos:</span>
                    @foreach($dadosInterno as $di)
                        {{ $di['nome']."(".$di['id'].")" }},
                    @endforeach
                    <br>
                    <span>Agentes Envolvidos: </span>
                    @foreach($dadosAgentes as $dA)
                        {{ $dA['nome']."(".$dA['matricula'].")" }},
                    @endforeach
                </i>
            </div>
            <div class="col-md-12 mt-2">
                @if($dadosArquivos != false)

                    @foreach($dadosArquivos as $dAqv)
                        <a href="{{ route('sindicancia.arquivoVisualizar',['id'=>$dados[0]->id,'filename'=>$dAqv->nome_arquivo]) }}" class="btn btn-icons btn-primary fileDownload" id="fileDownload" title="" data-toggle="tooltip" data-placement="right" data-original-title="Ver arquivo"><i class="mdi mdi-cloud-download"></i></a>
                    @endforeach

                @endif

            </div>
            <div class="col-md-12 mt-4">
                <div class="float-left  blockquote-footer">
                    <i>{{ $textoLog }}
                        @if($dados[0]->dataAtt != null)
                        / Última atualização em {{ $dados[0]->dataAtt }}
                        @endif
                    </i>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('css')
<link href="{{ asset('css/ocorrencia/cadastro/cadastro.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/ocorrencia/cadastro.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
@endpush

