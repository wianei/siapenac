@extends('template.tema_geral.theme')
<input type="hidden" name="idInterno" id="idInterno" value="{{ $id }}">
@section('content')
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div>
    @php
    $acoesLiberadas = \Illuminate\Support\Facades\Auth::user()->acoesLiberadas;
    $teclaf12 = "teclaf12";
    $btRightMouse = "rightButtonMouse";
    $cpfShowInterno = "cpfShowInterno";
    $lF12 = strpos($acoesLiberadas, $teclaf12);
    $lRightMouse = strpos($acoesLiberadas, $btRightMouse);
    $lCpfShowInterno = strpos($acoesLiberadas, $cpfShowInterno);
    @endphp


    <style type="text/css">
        @if ($lCpfShowInterno === false)

        .bgNoPrint{
            background-image: url('{{asset("storage/app/arquivos/usuarios/cpfimg/".\Illuminate\Support\Facades\Auth::user()->cpf.".png")}}');
            background-repeat: repeat;

        }
        @else
        .bgNoPrint{
            background-color: #FFF;
        }

        @endif

    </style>



    @if ($lF12 === false)
    <script>
        window.addEventListener('keydown', function (e) {
            var code = e.which || e.keyCode;
            if (code == 123) e.preventDefault();
            else return;

            console.log("invalid");
        });
        window.addEventListener('keydown', function (e) {
            var code = e.which || e.keyCode;
            if (code == 73) e.preventDefault();
            else return;

            console.log("invalid");
        });
    </script>
    @endif
    @if ($lRightMouse === false)
        <script>

            document.oncontextmenu = document.body.oncontextmenu = function() {return false;}

        </script>
    @endif

    <h3 style="display: none" class="text-center mostrarNaImpressao">
        @if($fotoPerfilDados['nome_arquivo'] != 'storage/app/semfoto.jpg')
            <img src="{{asset($fotoPerfilDados['nome_arquivo'])}}" style="max-height: 20em;" data-content="{{$fotoPerfilDados['tipo']}},{{$fotoPerfilDados['legenda']}},{{$fotoPerfilDados['created_at']}}" class="mx-auto d-block imagemSrc cursorPointer text-center figure-img img-fluid" title="Clique para ampliar a foto" alt="{{$fotoPerfilDados['tipo']}}"/>
        @else
            <img src="{{asset($fotoPerfilDados['nome_arquivo'])}}" style="max-height: 8em;" class="mx-auto d-block text-center figure-img img-fluid"/>
        @endif
        @if($interno['nome_social'] == "Não Informado")
                {{ $interno['nome'] }} - Prontuário: {{ $interno['id'] }}
        @else
                {{ $interno['nome_social'] }} - Prontuário: {{ $interno['id'] }}
                <br/>
                <i style="font-size: 10px">Nome Principal: {{ $interno['nome'] }}</i>
        @endif

        <hr/>
    </h3>
    <div class="row">
        <div class="col-md-2 p-2 rounded m-1 esconderNaImpressao bgNoPrint">
            @if( $interno['status'] == 'A')<h5 class="text-center text-green">{{ $interno['condicao_nome'] }}</h5>
            @elseif( $interno['status'] == 'I')<h5 class="text-center text-red">{{ $interno['condicao_nome'] }}</h5>@endif
            @if($fotoPerfilDados['nome_arquivo'] != 'storage/app/semfoto.jpg')
                <img src="{{asset($fotoPerfilDados['nome_arquivo'])}}" style="max-height: 20em;" data-content="{{$fotoPerfilDados['tipo']}},{{$fotoPerfilDados['legenda']}},{{$fotoPerfilDados['created_at']}}" class="mx-auto d-block imagemSrc cursorPointer text-center figure-img img-fluid" title="Clique para ampliar a foto" alt="{{$fotoPerfilDados['tipo']}}"/>
            @else
                <img src="{{asset($fotoPerfilDados['nome_arquivo'])}}" style="max-height: 20em;" class="mx-auto d-block text-center figure-img img-fluid"/>
            @endif
            <h5 class="mt-4 text-center">
                @if($interno['nome_social'] == "Não Informado")
                    {{ $interno['nome'] }}
                @else
                    {{ $interno['nome_social'] }}
                    <br/>
                    <i style="font-size: 10px">Nome Principal: {{ $interno['nome'] }}</i>
                @endif
            </h5>
            @if($interno['periculosidade'] == 'Alta') <h4 class="mt-2 text-center"><strong class="color_red">{{ $interno['periculosidade'] }} Periculosidade</strong> </h4>@endif
            @if($interno['periculosidade'] == 'Média') <h4 class="mt-2 text-center"><strong class="color_orange">{{ $interno['periculosidade'] }} Periculosidade</strong> </h4>@endif
                <p class="text-left">
                Prontuário: <strong>{{ $interno['id'] }}</strong>,<br/>
                @if( $interno['status'] == 'A' ) Unidade Prisional: <strong>{{ $interno['unidade_prisional_nome'] }}</strong>,<br/>
                @elseif( $interno['status'] == 'I' ) Última Unidade Prisional: <strong>{{ $interno['unidade_prisional_nome'] }}</strong>,<br/>@endif
                @if( $interno['status'] == 'A' )
                    @if(\Illuminate\Support\Facades\Auth::user()->tipo >= 300)
                        Pavilhão: <strong>{{ $interno['pavilhao_nome'] }}</strong>,<br/>
                        Ala: <strong>{{ $interno['ala_nome'] }}</strong>,<br/>
                        Cela: <strong>{{ $interno['cela_nome'] }}</strong>,<br/>
                    @endif
                    Medida de Segurança: <strong>{{ $interno['medida_seguranca']=="S"?"Sim":"Não" }}</strong>,<br/>
                    Sentenciado: <strong>{{ $interno['sentenciado']=='N'?"Não":($interno['sentenciado']=='S'?"Sim":"Não Informado") }}</strong>,<br/>
                    @if($interno['sentenciado'] == 'S') Regime: <strong>{{ $interno['regime'] }}</strong>,<br/>@endif
                    @if($interno['regime'] == "SEMIABERTO")Autorização de Saída no Regime Semiaberto: <strong>{{ $interno['saidasexternas'] }}</strong>,<br/>@endif
                @elseif( $interno['status'] == 'I' )
                    @if(\Illuminate\Support\Facades\Auth::user()->tipo >= 300)
                        Último Pavilhão: <strong>{{ $interno['pavilhao_nome'] }}</strong>,<br/>
                        Última Ala: <strong>{{ $interno['ala_nome'] }}</strong>,<br/>
                        Última Cela: <strong>{{ $interno['cela_nome'] }}</strong>,<br/>
                    @endif
                    Medida de Segurança: <strong>{{ $interno['medida_seguranca']=="S"?"Sim":"Não" }}</strong>,<br/>
                    Sentenciado: <strong>{{ $interno['sentenciado']=='N'?"Não":($interno['sentenciado']=='S'?"Sim":"Não Informado") }}</strong>,<br/>
                    @if($interno['sentenciado'] == 'S')Último Regime: <strong>{{ $interno['regime'] }}</strong>,<br/>@endif
                    @if($interno['regime'] == "SEMIABERTO")Tinha Autorização de Saída no Regime Semiaberto: <strong>{{ $interno['saidasexternas'] }}</strong>,<br/>@endif
                @endif
                Facção: <strong>{{ $interno['faccao_nome'] }}</strong>,<br/>
                Liderança: <strong>{{ $interno['lideranca'] }}</strong>,<br/>
                @if($interno['periculosidade'] == 'Baixa' || $interno['periculosidade'] == 'Não Informado') Periculosidade: <strong>{{ $interno['periculosidade'] }}</strong> @endif
                {{--Biometria Registrada na {{ config('app.secretaria') }} {{ config('app.uf') }}: <strong>{{ $interno['biometria_sejuc'] }}</strong>,<br/>--}}
                {{--Identidade Criminal Biométricamente Confirmada na SESED-RN: <strong>{{ $interno['identificacao_criminal_sesed'] }}</strong>--}}
                @if(\App\Http\Controllers\Setic\SeticController::existeUnificacao_id('interno',$interno['id']))<br/><br/><i class="fas fa-user-cog"></i> Este perfil passou pelo processo de unificação e pode haver eventuais inconsistências.@endif
            </p>
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical" style="background-color: white">

                <a class="nav-link active" id="v-pills-dadospessoais-tab" data-toggle="pill" href="#v-pills-dadospessoais" role="tab" aria-controls="v-pills-dadospessoais" aria-selected="true"><i class="fas fa-id-card"></i> Dados Pessoais</a>

                <a class="nav-link" id="v-pills-galeria-tab" data-toggle="pill" href="#v-pills-fotos" role="tab" aria-controls="v-pills-fotos" aria-selected="false"><i class="fas fa-camera"></i> Fotos</a>

                <a class="nav-link" id="companheirocela"  href="{{ route('interno.companheirosCela',$interno['id']) }}" ><i class="mdi mdi-sitemap"></i> Companheiros de Cela</a>

                <a class="nav-link" id="v-pills-historico-tab" data-toggle="pill" href="#v-pills-historico" role="tab" aria-controls="v-pills-historico" aria-selected="false"><i class="fas fa-history"></i> Histórico</a>

                <a class="nav-link" id="v-pills-visitantes-tab" data-toggle="pill" href="#v-pills-visitantes" role="tab" aria-controls="v-pills-visitantes" aria-selected="false"><i class="fas fa-users"></i> Visitantes</a>

                @if(\Illuminate\Support\Facades\Auth::user()->tipo >= 300)
                <a class="nav-link" id="v-pills-advogados-tab" data-toggle="pill" href="#v-pills-advogados" role="tab" aria-controls="v-pills-advogados" aria-selected="false"><i class="fas fa-user-tie"></i> Advogados</a>
                @endif

                <a class="nav-link" id="v-pills-disciplina-tab" data-toggle="pill" href="#v-pills-disciplina" role="tab" aria-controls="v-pills-disciplina" aria-selected="false"><i class="fas fa-gavel"></i> Disciplina</a>

                @if(\Illuminate\Support\Facades\Auth::user()->tipo >= 300)
                <a class="nav-link" id="v-pills-ocorrencias-tab" data-toggle="pill" href="#v-pills-ocorrencias" role="tab" aria-controls="v-pills-ocorrencias" aria-selected="false"><i class="fas fa-exclamation-triangle"></i> Ocorrências</a>
                @endif

                @if(\Illuminate\Support\Facades\Auth::user()->tipo >= 300)
                <a class="nav-link" id="v-pills-escoltas-tab" data-toggle="pill" href="#v-pills-escoltas" role="tab" aria-controls="v-pills-escoltas" aria-selected="false"><i class="fas fa-taxi"></i> Escoltas</a>
                @endif

                <a class="nav-link" id="v-pills-saude-tab" data-toggle="pill" href="#v-pills-saude" role="tab" aria-controls="v-pills-saude" aria-selected="false"><i class="fas fa-briefcase-medical"></i> Saúde e Assistência</a>

                @if(\Illuminate\Support\Facades\Auth::user()->tipo >= 600)
                <a class="nav-link" id="v-pills-documentos-tab" data-toggle="pill" href="#v-pills-documentos" role="tab" aria-controls="v-pills-documentos" aria-selected="true"><i class="fas fa-folder-open"></i> Documentos</a>
                @endif

                @if(\Illuminate\Support\Facades\Auth::user()->tipo >= 100)
                <a class="nav-link" id="v-pills-imprimir-tab" data-toggle="pill" href="#v-pills-imprimir" role="tab" aria-controls="v-pills-imprimir" aria-selected="true"><i class="fas fa-print"></i> Imprimir</a>
                @endif

            </div>
        </div>
        <div class="col p-2 m-1 w-100 rounded bgNoPrint" style="background: white">
            <div class="tab-content" id="v-pills-tabContent">
                <div class="tab-pane fade show active" id="v-pills-dadospessoais" role="tabpanel" aria-labelledby="v-pills-dadospessoais-tab">@include('interno.show.includes._dadospessoais')</div>
                <div class="tab-pane fade" id="v-pills-fotos" role="tabpanel" aria-labelledby="v-pills-fotos-tab">@include('interno.show.includes._fotos')</div>
                {{--<div class="tab-pane fade" id="v-pills-companheirosdecela" role="tabpanel" aria-labelledby="v-pills-companheirosdecela-tab">@include('interno.show.includes._companheirosCela')</div>--}}
                <div class="tab-pane fade" id="v-pills-historico" role="tabpanel" aria-labelledby="v-pills-historico-tab">@include('interno.show.includes._historico')</div>
                <div class="tab-pane fade" id="v-pills-visitantes" role="tabpanel" aria-labelledby="v-pills-visitantes-tab">@include('interno.show.includes._visitantes')</div>
                <div class="tab-pane fade" id="v-pills-advogados" role="tabpanel" aria-labelledby="v-pills-advogados-tab">@include('interno.show.includes._advogados')</div>
                <div class="tab-pane fade" id="v-pills-disciplina" role="tabpanel" aria-labelledby="v-pills-disciplina-tab">@include('interno.show.includes._disciplina')</div>
                <div class="tab-pane fade" id="v-pills-ocorrencias" role="tabpanel" aria-labelledby="v-pills-ocorrencias-tab">@include('interno.show.includes._ocorrencias')</div>
                <div class="tab-pane fade" id="v-pills-escoltas" role="tabpanel" aria-labelledby="v-pills-escoltas-tab">@include('interno.show.includes._escoltas')</div>
                <div class="tab-pane fade" id="v-pills-saude" role="tabpanel" aria-labelledby="v-pills-saude-tab">@include('interno.show.includes._saude')</div>
                <div class="tab-pane fade" id="v-pills-documentos" role="tabpanel" aria-labelledby="v-pills-documentos-tab">@include('interno.show.includes._documentos')</div>
                <div class="tab-pane fade" id="v-pills-imprimir" role="tabpanel" aria-labelledby="v-pills-imprimir-tab">@include('interno.show.includes._imprimir')</div>
            </div>
        </div>
    </div>
    <a id="back-to-top" href="#" class="back-to-top" role="button" title="Ir para o topo da tela" data-toggle="tooltip" data-placement="left"><i style="font-size: 2em; margin: 0 auto !important" class="fas fa-arrow-alt-circle-up"></i></a>
    {{-- modal para abrir a foto do interno --}}
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
    {{-- !! modal para abrir a foto do interno --}}
@endsection
@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{ asset('css/interno/show/show.css') }}" rel="stylesheet">
@endpush
@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{asset('js/interno/show.js')}}"></script>
@endpush