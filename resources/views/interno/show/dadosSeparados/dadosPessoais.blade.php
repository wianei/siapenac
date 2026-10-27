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
            <img src="data:image/png;base64, {{ $fotoPerfilDados }}" style="max-height: 20em;" class="mx-auto d-block imagemSrc cursorPointer text-center figure-img img-fluid" title="Clique para ampliar a foto"/>
        @if($interno['nome_social'] == "Não Informado")
                {{ $interno['nome'] }} - Prontuário: {{ $interno['id'] }}
        @else
                {{ $interno['nome_social'] }} - Prontuário: {{ $interno['id'] }}
                <br/>
                <i style="font-size: 16px"> {{ $interno['nome'] }}</i>
        @endif

        <hr/>
    </h3>
    <div class="row">
        <div class="col-md-2 p-2 rounded m-1 esconderNaImpressao bgNoPrint">
            @if( $interno['status'] == 'A')<h5 class="text-center text-green">{{ $interno['condicao_nome'] }}</h5>
            @elseif( $interno['status'] == 'I')<h5 class="text-center text-red">{{ $interno['condicao_nome'] }}</h5>@endif
            <img src="data:image/png;base64, {{ $fotoPerfilDados }}" style="max-height: 20em;" class="mx-auto d-block imagemSrc cursorPointer text-center figure-img img-fluid" title="Clique para ampliar a foto" />
            <h5 class="mt-4 text-center">
                @if($interno['nome_social'] == "Não Informado")
                    {{ strtoupper($interno)['nome'] }}
                @else
                    {{ strtoupper($interno['nome_social']) }}
                    <br/>
                    <i style="font-size: 16px">{{ strtoupper($interno['nome']) }}</i>
                @endif
            </h5>
            @if($interno['periculosidade'] == 'Alta') <h4 class="mt-2 text-center"><strong class="color_red">{{ $interno['periculosidade'] }} Periculosidade</strong> </h4>@endif
            @if($interno['periculosidade'] == 'Média') <h4 class="mt-2 text-center"><strong class="color_orange">{{ $interno['periculosidade'] }} Periculosidade</strong> </h4>@endif
                <p class="text-left">
                Prontuário: <strong>{{ $interno['id'] }}</strong>,<br/>
                @if( $interno['status'] == 'A' ) Unidade Prisional: <strong>{{ $interno['unidade_prisional_nome'] }}</strong>,<br/>
                @elseif( $interno['status'] == 'I' ) Última Unidade Prisional: <strong>{{ $interno['unidade_prisional_nome'] }}</strong>,<br/>@endif
                @if( $interno['status'] == 'A' )
                    @if(PermissaoService::getTipoPermissao() == "I")
                        Pavilhão: <strong>{{ $interno['pavilhao_nome'] }}</strong>,<br/>
                        Ala: <strong>{{ $interno['ala_nome'] }}</strong>,<br/>
                        Cela: <strong>{{ $interno['cela_nome'] }}</strong>,<br/>
                    @endif
                    Medida de Segurança: <strong>{{ $interno['medida_seguranca']=="S"?"Sim":"Não" }}</strong>,<br/>
                    Sentenciado: <strong>{{ $interno['sentenciado']=='N'?"Não":($interno['sentenciado']=='S'?"Sim":"Não Informado") }}</strong>,<br/>
                    @if($interno['sentenciado'] == 'S') Regime: <strong>{{ $interno['regime'] }}</strong>,<br/>@endif
                    @if($interno['regime'] == "SEMIABERTO")Autorização de Saída no Regime Semiaberto: <strong>{{ $interno['saidasexternas'] }}</strong>,<br/>@endif
                @elseif( $interno['status'] == 'I' )
                    @if(PermissaoService::getTipoPermissao() == "I")
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
            
                <a class="nav-link active" id="" data-toggle="" href="{{ route('interno.show', $id)}}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-id-card"></i> Dados Pessoais</a>

                @if(PermissaoService::getVerifyPermissionView("InternoController","showFotos"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showFotos', $id)}}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-camera"></i> Fotos</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","companheirosCela"))
                <a class="nav-link" id="companheirocela"  href="{{ route('interno.companheirosCela',$interno['id']) }}" ><i class="mdi mdi-sitemap"></i> Companheiros de Cela</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","showHistorico"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showHistorico',$id) }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-history"></i> Histórico</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","showProcessos"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showProcessos', $id)}}" role="tab" aria-controls="" aria-selected="false"><i class="mdi mdi-clipboard-text"></i> Processos e Crimes</a>
                @endif                

                @if(PermissaoService::getVerifyPermissionView("InternoController","showVisitantes"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showVisitantes',$id) }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-users"></i> Visitantes</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","showAdvogados"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showAdvogados',$id) }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-user-tie"></i> Advogados</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","showDisciplina"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showDisciplina',$id) }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-gavel"></i> Disciplina</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","showOcorrencia"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showOcorrencia',$id) }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-exclamation-triangle"></i> Ocorrências</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","showEscoltas"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showEscoltas',$id) }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-taxi"></i> Escoltas</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","showSaudeAssistencia"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showSaudeAssistencia',$id) }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-briefcase-medical"></i> Saúde e Assistência</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","showDocumentos"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showDocumentos',$id) }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-folder-open"></i> Documentos</a>
                @endif

                @if(PermissaoService::getVerifyPermissionView("InternoController","showImprimir"))
                <a class="nav-link" id="" data-toggle="" href="{{ route('interno.showImprimir',$id) }}" role="tab" aria-controls="" aria-selected="false"><i class="fas fa-print"></i> Imprimir</a>
                @endif
            
            </div>
        </div>
        <div class="col p-2 m-1 w-100 rounded bgNoPrint" style="background: white">
            <div class="tab-content" id="v-pills-tabContent">
            
            <!-- dados da rota -->

        <div class="container">
            <div class="titlePage color_365f80 mb-5">
                <h3><i class="fas fa-id-card"></i> Dados pessoais do interno</h3>
                <hr>
                <p>
                    Dados pessoais do interno preenchidos pelo {{ config('app.sistema_estado') }}.
                </p>
                @if(PermissaoService::getVerifyPermissionView("InternoController","editar"))
                <a href="{{route('interno.editar',$interno['id'])}}" data-toggle="tooltip" data-placement="top" title="Editar" class="esconderNaImpressao btn btn-primary text-right"><i class="fas fa-edit"></i>Editar</a>
                @endif
            </div>
        </div>
        <div class="pl-5 pr-5">
            <div class="mostrarNaImpressao" style="display: none">
                @if( $interno['status'] == 'A')<h5 class="mt-4 text-center text-green">{{ $interno['condicao_nome'] }}</h5>
                @elseif( $interno['status'] == 'I')<h5 class="mt-4 text-center text-red">{{ $interno['condicao_nome'] }}</h5>@endif
                <strong>Prontuário:</strong> {{ $interno['id'] }},<br/>
                @if( $interno['status'] == 'A' ) <strong>Unidade Prisional:</strong> {{ $interno['unidade_prisional_nome'] }},<br/>
                @elseif( $interno['status'] == 'I' ) <strong>Última Unidade Prisional:</strong> {{ $interno['unidade_prisional_nome'] }},<br/>@endif
                @if( $interno['status'] == 'A' )
                    <strong>Pavilhão:</strong> {{ $interno['pavilhao_nome'] }},<br/>
                    @if(PermissaoService::getTipoPermissao() == "I")
                        <strong>Ala:</strong> {{ $interno['ala_nome'] }},<br/>
                        <strong>Cela:</strong> {{ $interno['cela_nome'] }},<br/>
                    @endif
                @elseif( $interno['status'] == 'I' )
                    <strong>Último Pavilhão:</strong> {{ $interno['pavilhao_nome'] }},<br/>
                    @if(PermissaoService::getTipoPermissao() == "I")
                        <strong>Última Ala:</strong> {{ $interno['ala_nome'] }},<br/>
                        <strong>Última Cela:</strong> {{ $interno['cela_nome'] }},<br/>
                    @endif
                    <strong>Último Regime:</strong> {{ $interno['regime'] }},<br/>
                @endif
                <strong>Sentenciado:</strong> {{ $interno['sentenciado']=='N'?"Não":($interno['sentenciado']=='S'?"Sim":"Não Informado") }},<br/>
                <strong>Regime:</strong> {{ $interno['regime'] }},<br/>
                {{--<strong>Tornozeleira:</strong> {{ $interno['tornozeleira'] }},<br/>--}}
                <strong>Facção:</strong> {{ $interno['faccao_nome'] }},<br/>
                <strong>Liderança:</strong> {{ $interno['lideranca'] }},<br/>
                <strong>Periculosidade:</strong> {{ $interno['periculosidade'] }},<br/>
                {{--<strong>Biometria Registrada na {{ config('app.secretaria') }} {{ config('app.uf') }}:</strong> {{ $interno['biometria_sejuc'] }},<br/>--}}
                {{--<strong>Identidade Criminal Biométricamente Confirmada na SESED-RN:</strong> {{ $interno['identificacao_criminal_sesed'] }}--}}
            </div>
            <div class="row bgNoPrint" style="font-size: 14px !important;">
                {{--<div id="background">--}}
                    {{--<p id="bg-text">--}}
                        {{--@for($i =0; $i < 4; $i++)--}}
                            {{--{{ \Illuminate\Support\Facades\Auth::user()->cpf }}--}}
                        {{--@endfor--}}

                    {{--</p>--}}
                {{--</div>--}}

                <div class="col-sm-3">
                    <strong>Sexo:</strong> {{$interno['sexo']=='M'?"Masculino":($interno['sexo']=='F'?"Feminino":"Não Informado")}}
                </div>
                <div class="col-sm-12 mt-2">
                    @if($interno['outrosnomes'] == [])
                        <strong>Outros Nomes:</strong> Não Informado,<br/>
                    @else
                        <strong>Outros Nomes:</strong>
                        @foreach($interno['outrosnomes'] as $outrosnomes)
                            {{$outrosnomes}},
                        @endforeach
                    @endif
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Data de Nascimento:</strong> {{$interno['data_nascimento']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Idade:</strong> {{$interno['idade']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Estado Civil:</strong> {{$interno['estado_civil']}}
                </div>
                <div class="col-sm-12 mt-2">
                    <strong>Nome da Mãe:</strong> {{$interno['mae']}}
                </div>
                <div class="col-sm-12 mt-2">
                    <strong>Nome do Pai:</strong> {{$interno['pai']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Número do RG:</strong> {{$interno['rg_numero']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Número do CPF:</strong> {{$interno['cpf']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Órgão e Estado Expedidor do RG:</strong> {{$interno['rg_orgaoexpedidor']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Data de Emissão do RG:</strong> {{$interno['rg_data']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Número do Passaporte:</strong> {{$interno['passaportenum']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Número do Cartão SUS:</strong> {{$interno['cartaosus']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Profissão:</strong> {{$interno['profissao_nome']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Escolaridade:</strong> {{$interno['escolaridade']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Altura:</strong> {{$interno['altura']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Peso:</strong> {{$interno['peso']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Cor da Pele:</strong> {{$interno['etnia']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Cor dos Olhos:</strong> {{$interno['cor_olhos']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Cor do Cabelo:</strong> {{$interno['cor_cabelos']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Opção Sexual:</strong> {{$interno['opcao_sexual']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Religião:</strong> {{$interno['religiao']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Facção:</strong> {{$interno['faccao_nome']}}
                </div>
            @if($interno['faccao'] != "Não Informado")
                <div class="col-sm-4 mt-2">
                    <strong>Facção - Postos:</strong> {{$interno['faccao_posto']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Facção - Padrinho:</strong> {{$interno['faccao_padrinho']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Facção - Quebrada:</strong> {{$interno['faccao_quebrada']}}
                </div>
            @endif
            <div class="col-sm-4 mt-2">
                <strong>Naturalidade - País:</strong> {{$interno['naturalidade_pais']}}
            </div>
            <div class="col-sm-4 mt-2">
                <strong>Naturalidade - Estado:</strong> {{$interno['naturalidade_estado']}}
            </div>
            <div class="col-sm-4 mt-2">
                <strong>Naturalidade - Cidade:</strong> {{$interno['naturalidade_cidade']}}
            </div>

            @if(PermissaoService::getVerifyPermissionView("InternoController","enderecoInterno"))
                @if($interno['enderecos'] == [])
                    <div class="col-sm-12 mt-2">
                        <strong>Endereço:</strong> Não Informado
                    </div>
                @else
                    @foreach($interno['enderecos'] as $key=>$endereco)
                        <div class="col-sm-12 mt-2">
                            <strong>Endereço {{$key+1}}:</strong>
                        </div>
                        <div class="col-sm-4 mt-2">
                            <strong>Cidade/Estado:</strong> {{$endereco['cidade']}}/{{$endereco['estado']}}
                        </div>
                        <div class="col-sm-4 mt-2">
                            <strong>Bairro:</strong> {{$endereco['bairro']}}
                        </div>
                        <div class="col-sm-4 mt-2">
                            <strong>CEP:</strong> {{$endereco['cep']}}
                        </div>
                        <div class="col-sm-12 mt-2">
                            <strong>Logradouro:</strong> {{$endereco['logradouro']}}
                        </div>
                        <div class="col-sm-4 mt-2">
                            <strong>Número:</strong> {{$endereco['numero']}}
                        </div>
                        <div class="col-sm-4 mt-2">
                            <strong>Complemento:</strong> {{$endereco['complemento']}}
                        </div>
                        <div class="col-sm-4 mt-2">
                            <strong>Latitude:</strong> {{$endereco['latitude']}}
                        </div>
                        <div class="col-sm-4 mt-2">
                            <strong>Longitude:</strong> {{$endereco['longitude']}}
                        </div>
                    @endforeach
                @endif
            @endif

            <div class="col-sm-12 mt-2">
                <h4>Observações: </h2> 
                {{$interno['observacao']}}
                </div>
            </div>
        </div>

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