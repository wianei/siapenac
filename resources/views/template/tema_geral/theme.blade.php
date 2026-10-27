<!DOCTYPE html>
<html lang="pt-br">

<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-134707801-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-134707801-1');
    </script>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }}</title>
    <link rel="shortcut icon" href="{{ asset('imagens/template/favicon.png') }}">
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{asset('css/template/tema_geral/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css"
          integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('css/template/tema_geral/vendor.bundle.base.css')}}">
    <link rel="stylesheet" href="{{asset('css/template/tema_geral/vendor.bundle.addons.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{asset('css/template/tema_geral/style.css')}}">
    <link rel="stylesheet" href="{{asset('css/util.css')}}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{asset('images/template/tema_geral/favicon.png')}}"/>
    @stack('css')
</head>

<body>
@php

$mensagensNaoLidas = \App\Http\Controllers\Mensagem\MensagemController::getMensagensNaoLido();

$notificacao = \App\Http\Controllers\Notificacao\NotificacaoController::getNotificacoes();

$notificacaoSetic = \App\Http\Controllers\Notificacao\NotificacaoController::getNotificacoesSetic();

$permissaoUsuario = \App\Http\Controllers\Permissoes\PermissoesController::getTipoPermissao();

$siglaUpLogado = \App\Http\Controllers\Facade\FacadeController::getLotacaoServidor(\Illuminate\Support\Facades\Auth::user()->cpf, "LOTAÇÃO NO RH");

$idUpLogado = \App\Http\Controllers\unidades\UnidadesController::getIdUpPorSigla(\App\Http\Controllers\Facade\FacadeController::getLotacaoServidor(\Illuminate\Support\Facades\Auth::user()->cpf, "LOTAÇÃO NO RH"));

$noRH = \App\Http\Controllers\Facade\FacadeController::servidorNoRH(\Illuminate\Support\Facades\Auth::user()->cpf);

$unidadeUserLogado = \App\Http\Controllers\Facade\FacadeController::getLotacaoServidor(\Illuminate\Support\Facades\Auth::user()->cpf);

$cpfLogado = \Illuminate\Support\Facades\Auth::user()->cpf;

$trocaUnidade = \Illuminate\Support\Facades\Auth::user()->visualiza_outras_up;

$fotoUser = \App\Http\Controllers\Facade\FacadeController::getFotoServidor();

@endphp

<div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">

        <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center" id="logoSiapenMini">
            <a class="navbar-brand brand-logo" href="{{ route('inicio')}}">
                <h2>{{ config('app.name') }}</h2><br>
            </a>
            <a class="navbar-brand brand-logo-mini" href="{{route('inicio')}}">
                <h2>{{ config('app.name') }}</h2>
            </a>
        </div>

        <div class="navbar-menu-wrapper d-flex align-items-center" id="divMenuTop">
            @if(config('app.ambiente') == "hml")
                <h6>**AMBIENTE DE TREINAMENTO **</h6>
            @endif
            <ul class="navbar-nav navbar-nav-right" id="ulMenuTop">

                <li class="nav-item dropdown" id="liHelpDesk">
                        <a class="nav-link count-indicator dropdown-toggle" id="messageDropdown" href="#"
                           data-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-help-box"></i>

                            @if(($notificacaoSetic) != "false")
                                <span class="count">{{ count($notificacaoSetic) }}</span>
                            @endif

                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                             aria-labelledby="messageDropdown">
                            <div class="dropdown-item">
                                <p class="mb-0 font-weight-normal float-left">
                                    A {{ config('app.ti') }} Respondeu Seu Chamado
                                </p>
                                <span class="badge badge-info badge-pill float-right"><a href="{{ route('helpDesk.chamadosListar') }}" class="color_FFF">Ver Todas</a></span>
                            </div>
                            <div class="dropdown-divider"></div>
                            @if(($notificacaoSetic) != "false")
                                @foreach($notificacaoSetic as $notSetic)

                                    <a href="{{ route('helpDesk.chamadosVisualizar', $notSetic->fk_chamado) }}" class="dropdown-item preview-item">
                                        <div class="preview-item-content flex-grow">
                                            <h6 class="preview-subject ellipsis font-weight-medium text-dark">
                                                <span class="float-left font-weight-light small-text">
                                                    {{ $notSetic->update_at }}
                                                </span>
                                            </h6>

                                            <p class="font-weight-light small-text">
                                                {{ $notSetic->assunto }}
                                            </p>
                                        </div>
                                    </a>
                                @endforeach
                            @endif
                                <div class="dropdown-divider"></div>

                        </div>
                </li>

                <li class="nav-item dropdown" id="liMensagens">
                    @if($mensagensNaoLidas != 0)
                        <a class="nav-link count-indicator dropdown-toggle" id="messageDropdown" href="#"
                           data-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-envelope"></i>
                            @if(count($mensagensNaoLidas) != 0)
                                <span class="count">{{ count($mensagensNaoLidas) }}</span>
                            @endif
                        </a>
                    @endif
                    @if($mensagensNaoLidas != 0)
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                             aria-labelledby="messageDropdown">
                            <div class="dropdown-item">
                                <p class="mb-0 font-weight-normal float-left">
                                    @if(count($mensagensNaoLidas) == 0)
                                        Você não possui mensagens não lidas
                                    @else
                                        Você possui {{ count($mensagensNaoLidas) }} mensagens não lidas
                                    @endif
                                </p>
                                <span class="badge badge-info badge-pill float-right"><a href="{{ route('mensagem.recebidas') }}" class="color_FFF">Ver Todas</a></span>
                            </div>
                            <div class="dropdown-divider"></div>

                            @foreach($mensagensNaoLidas as $mensagensNaoLida)
                                <a href="{{ route('mensagem.ler', $mensagensNaoLida['id']) }}" class="dropdown-item preview-item">
                                    <div class="preview-item-content flex-grow">
                                        <h6 class="preview-subject ellipsis font-weight-medium text-dark">{{ $mensagensNaoLida['remetente'] }}
                                            <span class="float-left font-weight-light small-text">{{ $mensagensNaoLida['hora']." - ".$mensagensNaoLida['data'] }}</span>
                                        </h6>

                                        <p class="font-weight-light small-text">
                                            {{ $mensagensNaoLida['assunto'] }}
                                        </p>
                                    </div>
                                </a>
                                <div class="dropdown-divider"></div>
                            @endforeach

                        </div>
                    @endif
                </li>

                <li class="nav-item dropdown" id="liNotificacoes">

                    @if($permissaoUsuario == "I")
                    <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
                        <i class="mdi mdi-bell"></i>
                        @if(count($notificacao) != 0)
                            <span class="count"> {{ count($notificacao) }} </span>
                        @endif
                    </a>
                    @endif

                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
                        @if($permissaoUsuario == "I")
                            <a class="dropdown-item">
                                <p class="mb-0 font-weight-normal float-left">
                                        Você possui
                                        @if(count($notificacao) == 1)
                                            {{ count($notificacao) }} notificação
                                        @else
                                            {{ count($notificacao) }} notificações
                                        @endif

                                </p>
                                {{--<span class="badge badge-pill badge-warning float-right">Ver Todas</span>--}}
                            </a>
                        @endif

                        @if(isset($notificacao['alocacaoCela']))
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('movimentacoes.receberInterno').'#alocacaoCela' }}" class="dropdown-item preview-item">
                            <div class="preview-thumbnail">
                                <div class="preview-icon bg-warning">
                                    <i class="mdi mdi-alert-circle-outline mx-0"></i>
                                </div>
                            </div>
                            <div class="preview-item-content">
                                <h6 class="preview-subject font-weight-medium text-dark">Alocação de Internos em Cela</h6>

                                <p class="font-weight-light small-text">
                                    {{ count($notificacao['alocacaoCela']) }} Interno(s) sem cela
                                </p>
                            </div>
                        </a>
                        @endif

                        @if(isset($notificacao['internoPedenteTransferencia']))
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('movimentacoes.receberInterno') }}" class="dropdown-item preview-item">
                            <div class="preview-thumbnail">
                                <div class="preview-icon bg-warning">
                                    <i class="mdi mdi-alert-circle-outline mx-0"></i>
                                </div>
                            </div>
                            <div class="preview-item-content">
                                <h6 class="preview-subject font-weight-medium text-dark">Internos pendentes de recebimento</h6>

                                <p class="font-weight-light small-text">
                                    {{ count($notificacao['internoPedenteTransferencia']) }} Interno(s) para receber
                                </p>
                            </div>
                        </a>
                        @endif

                        @if(isset($notificacao['internoPedenteSaidaTransferencia']))
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('movimentacoes.transferenciaShow') }}" class="dropdown-item preview-item">
                            <div class="preview-thumbnail">
                                <div class="preview-icon bg-warning">
                                    <i class="mdi mdi-alert-circle-outline mx-0"></i>
                                </div>
                            </div>
                            <div class="preview-item-content">
                                <h6 class="preview-subject font-weight-medium text-dark">Internos com Transferência (Saída) Pendente</h6>

                                <p class="font-weight-light small-text">
                                    {{ count($notificacao['internoPedenteSaidaTransferencia']) }} Interno(s) aguardando confirmação do destino
                                </p>
                            </div>
                        </a>
                        @endif

                        @if(isset($notificacao['escoltaGepConfirmada']))
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('escolta.buscar', '?destino=&unidade_prisional=&data_saida=&status=Confirmada&tipo_escolta=&vtr=&agente=#') }}" class="dropdown-item preview-item">
                            <div class="preview-thumbnail">
                                <div class="preview-icon bg-success">
                                    <i class="mdi mdi-approval mx-0"></i>
                                </div>
                            </div>
                            <div class="preview-item-content">
                                <h6 class="preview-subject font-weight-medium text-dark">Confirmação de Escoltas FTPEN</h6>

                                <p class="font-weight-light small-text">
                                    {{ count($notificacao['escoltaGepConfirmada']) }} Escoltas confirmadas pelo FTPEN
                                </p>
                            </div>
                        </a>
                        @endif

                        @if(isset($notificacao['gepCancelamento']))
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('escolta.buscar', '?destino=&unidade_prisional=&data_saida=&status=Cancelada&tipo_escolta=&vtr=&agente=#') }}" class="dropdown-item preview-item">
                            <div class="preview-thumbnail">
                                <div class="preview-icon bg-youtube">
                                    <i class="mdi mdi-cancel mx-0"></i>
                                </div>
                            </div>
                            <div class="preview-item-content">
                                <h6 class="preview-subject font-weight-medium text-dark">Cancelamento de Escoltas FTPEN</h6>

                                <p class="font-weight-light small-text">
                                    {{ count($notificacao['gepCancelamento']) }} Escoltas canceladas pelo FTPEN
                                </p>
                            </div>
                        </a>
                        @endif

                        @if(isset($notificacao['sancaoTriagem']))
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('interno.saidaSancaoTriagem') }}" class="dropdown-item preview-item">
                            <div class="preview-thumbnail">
                                <div class="preview-icon bg-github">
                                    <i class="mdi mdi-exit-to-app mx-0"></i>
                                </div>
                            </div>
                            <div class="preview-item-content">
                                <h6 class="preview-subject font-weight-medium text-dark">Saída da Sanção ou Triagem</h6>

                                <p class="font-weight-light small-text">
                                    {{ count($notificacao['sancaoTriagem']) }} Internos
                                </p>
                            </div>
                        </a>
                        @endif

                        @if(isset($notificacao['solicitacoesInterno']))
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('solicitacaoInterno.pedidos') }}" class="dropdown-item preview-item">
                            <div class="preview-thumbnail">
                                <div class="preview-icon bg-google">
                                    <i class="mdi mdi-account-switch mx-0"></i>
                                </div>
                            </div>
                            <div class="preview-item-content">
                                <h6 class="preview-subject font-weight-medium text-dark">Solicitação de Transferência</h6>

                                <p class="font-weight-light small-text">
                                    {{ $notificacao['solicitacoesInterno'] }} Internos
                                </p>
                            </div>
                        </a>
                        @endif

                    </div>
                </li>

                <span class="profile-text" id="liLotacao">{{ \App\Http\Controllers\Facade\FacadeController::getLotacaoServidor(Auth::user()->cpf) }}</span>
                <li class="nav-item dropdown d-none d-xl-inline-block" id="liMenuSup">
                    <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown"
                       aria-expanded="false">
                        <span class="profile-text" id="spanNomeUser">Olá, {{ \App\Http\Controllers\Facade\FacadeController::getDadosServidor(Auth::user()->cpf, '*')[0]->nome}} !</span>
                        @if($permissaoUsuario == "E")
                            <img class="img-xs rounded-circle" src="{{ asset('imagens/usuario/avatarInterno.png') }}" alt="Profile image">
                        @endif

                        @if($permissaoUsuario == "I")

                            @if($fotoUser == null)


                                <img class="img-xs rounded-circle" src="{{ asset('imagens/usuario/avatarInterno.png') }}" alt="">

                            @else

                                <img class="img-xs rounded-circle" src="{{$fotoUser}}" alt="">

                            @endif

                        @endif

                    </a>

                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                        <a class="dropdown-item mt-4" href="{{ route('usuario.alterarMinhaSenha') }}">
                            Alterar Senha
                        </a>
                        @if($trocaUnidade == 'S')
                            <a class="dropdown-item" href="{{ route('unidadde.alterarLotacaoSiapen') }}">
                                Alterar Minha Lotação no {{ config('app.name') }}
                            </a>
                        @endif

                        <a class="dropdown-item" href="{{ route('logout') }}">
                            Sair
                        </a>
                    </div>
                </li>
            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                    data-toggle="offcanvas">
                <span class="icon-menu">MENU</span>
            </button>
        </div>
    </nav>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
            <ul class="nav">
                {{--<li class="nav-item nav-profile">--}}
                    {{--<div class="nav-link">--}}
                        {{--<div class="user-wrapper">--}}
                            {{--<div class="profile-image">--}}
                                {{--<img src="{{ asset('imagens/usuario/face1.jpg') }}" alt="profile image">--}}
                            {{--</div>--}}
                            {{--<div class="text-wrapper">--}}
                                {{--<p class="profile-name">NOME DO SERVIDOR</p>--}}

                                {{--<div>--}}
                                    {{--<small class="designation text-muted">IP</small>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</li>--}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('inicio') }}">
                        <i class="menu-icon fas fa-home"></i>
                        <span class="menu-title">Página Inicial</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('helpDesk.chamadosListar') }}">
                        <i class="menu-icon mdi mdi-help-circle"></i>
                        <span class="menu-title">Precisa de Ajuda?</span>
                    </a>
                </li>
                
                @if($permissaoUsuario == "I")
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('ead.listaCursos') }}">
                            <i class="menu-icon mdi mdi-book-open-page-variant"></i>
                            <span class="menu-title">Cursos EAD</span>
                        </a>
                    </li>
                @endif

                @if(PermissaoService::getVerifyPermissionController("InternoController"))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false"
                       aria-controls="ui-basic">
                        <i class="menu-icon fas fa-user-circle"></i>
                        <span class="menu-title">Internos</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="ui-basic">
                        <ul class="nav flex-column sub-menu">

                            @if(PermissaoService::getVerifyPermissionView("InternoController","buscar"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('interno.buscar') }}"><i
                                            class="fa fa-search mr-1"></i> Buscar</a>
                            </li>
                            @endif

                            @if(PermissaoService::getVerifyPermissionView("InternoController","cadastro"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('interno.cadastro')}}"><i
                                            class="fa fa-plus mr-1"></i> Cadastrar</a>
                            </li>
                            @endif

                           @if(PermissaoService::getVerifyPermissionView("InternoController","confereGeral"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('interno.confereGeral') }}"><i
                                            class="fa fa-tasks mr-1"></i> Confere Geral</a>
                            </li>
                            @endif

                            @if(PermissaoService::getVerifyPermissionView("InternoController","atestadosCertidoes"))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('interno.atestadosCertidoes') }}"><i
                                                class="mdi mdi-file-document mr-1"></i> Atestados e Certidões</a>
                                </li>
                            @endif
                            
                            @if(PermissaoService::getVerifyPermissionView("ListagemController","frequenciaAberto"))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('listagem.frequenciaAberto') }}"><i
                                                class="mdi mdi-format-list-bulleted mr-1"></i> Frequência Aberto</a>
                                </li>
                                @endif
                                @if(PermissaoService::getVerifyPermissionView("ListagemController","frequenciaSemiaberto"))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('listagem.frequenciaSemiaberto') }}"><i
                                                class="mdi mdi-format-list-bulleted mr-1"></i> Frequência Semiaberto</a>
                                </li>
                                @endif
                                @if(PermissaoService::getVerifyPermissionView("ListagemController","frequenciaDiariaInternoGerenciarListas"))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('listagem.frequenciaDiariaInternoGerenciarListas') }}"><i
                                                class="mdi mdi-format-list-bulleted mr-1"></i> Lista de Frequência</a>
                                </li>
                                @endif
                                @if(PermissaoService::getVerifyPermissionView("ArtigosProcessosController","buscar"))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('artigosProcessos.buscar') }}"><i
                                                class="mdi mdi-file-document mr-1"></i> Artigos e Processos</a>
                                </li>
                                @endif
                            
                        </ul>
                    </div>
                </li>
                @endif

                @if(PermissaoService::getVerifyPermissionController("VisitaController"))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#MenuVisitas" aria-expanded="false"
                       aria-controls="ui-basic">
                        <i class="menu-icon fas fa-users"></i>
                        <span class="menu-title">Visitas</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="MenuVisitas">
                        <ul class="nav flex-column sub-menu">

                            @if(PermissaoService::getVerifyPermissionView("VisitaController","buscar"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('visita.buscar') }}"><i
                                            class="fa fa-search mr-1"></i> Buscar</a>
                            </li>
                            @endif

                            @if(PermissaoService::getVerifyPermissionView("VisitaController","cadastro"))
                            <li class="nav-item">
                                <a class="nav-link" id="visitacadastro" href="{{ route('visita.cadastro') }}"><i
                                            class="fa fa-plus mr-1"></i> Cadastrar</a>
                            </li>
                            @endif

                            @if(PermissaoService::getVerifyPermissionView("VisitaController","confereVisita"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('visita.confereVisita') }}"><i
                                            class="fa fa-tasks mr-1"></i> Confere Visitas</a>
                            </li>
                            @endif

                        </ul>
                    </div>
                </li>
                @endif

                @if(PermissaoService::getVerifyPermissionController("AdvogadoController"))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#MenuAdvogado" aria-expanded="false"
                       aria-controls="ui-basic">
                        <i class="menu-icon fas fa-gavel"></i>
                        <span class="menu-title">Advogados</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="MenuAdvogado">
                        <ul class="nav flex-column sub-menu">

                            @if(PermissaoService::getVerifyPermissionView("AdvogadoController","buscar"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('advogado.buscar') }}"><i
                                            class="fa fa-search mr-1"></i> Buscar</a>
                            </li>
                            @endif

                            @if(PermissaoService::getVerifyPermissionView("AdvogadoController","cadastro"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('advogado.cadastro') }}"><i
                                            class="fa fa-plus mr-1"></i> Cadastrar</a>
                            </li>
                            @endif

                            @if(PermissaoService::getVerifyPermissionView("AdvogadoController","inicioAtendimento"))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('advogado.inicioAtendimento') }}"><i
                                                class="mdi mdi-play mr-1"></i> Registrar Atendimento</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('advogado.atendimentoHistorico') }}"><i
                                                class="fa fa-history mr-1"></i> Histórico de Atendimento</a>
                                </li>
                            @endif

<!--                                @if($permissaoUsuario >= 700)
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('advogado.cancelarAgendamentos') }}"><i
                                                    class="mdi mdi-close mr-1"></i> Cancelar Agendamentos</a>
                                    </li>
                                @endif-->

                        </ul>
                    </div>
                </li>
                @endif

                @if(PermissaoService::getVerifyPermissionController("ServidorController"))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#MenuServidores" aria-expanded="false"
                       aria-controls="ui-basic">
                        <i class="menu-icon mdi mdi-account-location"></i>
                        <span class="menu-title">Servidores</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="MenuServidores">
                        <ul class="nav flex-column sub-menu">                            
                          
                            @if(PermissaoService::getVerifyPermissionView("ServidorController","listarServidores"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('servidor.listarServidores') }}"><i
                                            class="menu-icon mdi mdi-account-location mr-1"></i> Servidores da Unidade</a>
                            </li>
                            @endif

                            @if(PermissaoService::getVerifyPermissionView("ListagemController","frequenciaServidores"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('listagem.frequenciaServidores') }}"><i
                                            class="mdi mdi-format-list-bulleted mr-1"></i> Folha de Frequência</a>
                            </li>
                            @endif

                        </ul>
                    </div>
                </li>
                @endif

                {{--@if($permissaoUsuario >= 600)--}}
                    {{--<li class="nav-item">--}}
                        {{--<a class="nav-link" href="{{ route('servidor.listarServidores') }}">--}}
                            {{--<i class="menu-icon mdi mdi-account-location"></i>--}}
                            {{--<span class="menu-title">Servidores</span>--}}
                        {{--</a>--}}
                    {{--</li>--}}
                {{--@endif--}}

                <!-- @if($permissaoUsuario == 200 || $idUpLogado == 6397)
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#MenuJudiciario" aria-expanded="false"
                       aria-controls="ui-basic">
                        <i class="menu-icon mdi mdi-gavel"></i>
                        <span class="menu-title">Judiciário</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="MenuJudiciario">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('judiciario.agendamentos') }}"><i
                                            class="mdi mdi-calendar mr-1"></i> Agendamentos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('judiciario.novoAgendamento') }}"><i
                                            class="fa fa-plus mr-1"></i> Agendar Escolta</a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif -->

                @if(PermissaoService::getVerifyPermissionController("MovimentacoesController"))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#MenuMovimentacoes" aria-expanded="false"
                       aria-controls="ui-basic">
                        <i class="menu-icon fas fa-exchange-alt"></i>
                        <span class="menu-title">Movimentações</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="MenuMovimentacoes">
                        <ul class="nav flex-column sub-menu">
                            @if(PermissaoService::getVerifyPermissionView("MovimentacoesController","mudancacelaShow"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('movimentacoes.mudancacelaShow') }}"><i
                                            class="fas fa-sync mr-1"></i> Mudança de Cela</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("MovimentacoesController","transferenciaShow"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('movimentacoes.transferenciaShow') }}"><i
                                            class="fas fa-arrow-right mr-1"></i> Transferência de Unidade</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("SolicitacaoInternoController","solicitar"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('solicitacaoInterno.solicitar') }}"><i
                                            class="mdi mdi-arrow-down mr-1"></i> Solicitação de Interno</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("MovimentacoesController","progressaoregressaoShow"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('movimentacoes.progressaoregressaoShow') }}"><i
                                            class="mdi mdi-swap-horizontal mr-1"></i> Progressão e Regressão</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("MovimentacoesController","alvaraShow"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('movimentacoes.alvaraShow') }}"><i
                                            class="fas fa-lock-open mr-1"></i> Alvará de Soltura</a>
                            </li>
                            @endif 
                            @if(PermissaoService::getVerifyPermissionView("MovimentacoesController","saidajudicialShow"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('movimentacoes.saidajudicialShow') }}"><i
                                            class="fas fa-user-minus mr-1"></i> Saída Judicial</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("MovimentacoesController","livramentocondicionalShow"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('movimentacoes.livramentocondicionalShow') }}"><i
                                            class="far fa-file-alt mr-1"></i> Livramento Condicional</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("MovimentacoesController","fugaShow"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('movimentacoes.fugaShow') }}"><i
                                            class="fas fa-walking mr-1"></i> Fuga</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("MovimentacoesController","recambiamentoShow"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('movimentacoes.recambiamentoShow') }}"><i
                                            class="fas fa-angle-double-right mr-1"></i> Recambiamento</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("MovimentacoesController","obitoShow"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('movimentacoes.obitoShow') }}"><i
                                            class="mdi mdi-church mr-1"></i> Óbito</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("MovimentacoesController","extincaopunibilidadeShow"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('movimentacoes.extincaopunibilidadeShow') }}"><i
                                            class="fas fa-gavel mr-1"></i> Extinção de Punibilidade</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("MovimentacoesController","extradicaoShow"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('movimentacoes.extradicaoShow') }}"><i
                                            class="fas fa-globe-americas mr-1"></i> Extradição</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("MovimentacoesController","prisaodomiciliarShow"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('movimentacoes.prisaodomiciliarShow') }}"><i
                                            class="fas fa-home mr-1"></i> Prisão Domiciliar</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("MovimentacoesController","mudancaSentencaShow"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('movimentacoes.mudancasentencaStoreShow') }}">
                                    <i class="fas fa-balance-scale mr-1"></i> Sentenciado ou Provisório
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                @if(PermissaoService::getVerifyPermissionController("EstatisticasController"))
                <li class="nav-item">
                    {{--<a class="nav-link" href="{{ route('estatisticas.internosUnidade') }}">
                        <i class="menu-icon fas fa-chart-pie"></i>
                        <span class="menu-title">Estatísticas</span>
                    </a>--}}

                    <a class="nav-link" data-toggle="collapse" href="#MenuEstatisticas" aria-expanded="false"
                       aria-controls="ui-basic">
                        <i class="menu-icon fas fa-chart-pie"></i>
                        <span class="menu-title">Estatísticas</span>
                        <i class="menu-arrow"></i>
                    </a>

                    
                    <div class="collapse" id="MenuEstatisticas">
                        <ul class="nav flex-column sub-menu">
                            @if(PermissaoService::getVerifyPermissionView("EstatisticasController","internosPorUnidade"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('estatisticas.internosUnidade') }}"><i class="menu-icon fas fa-user-circle"></i> Internos</a>
                            </li>
                            @endif
                            
                            @if(PermissaoService::getVerifyPermissionView("EstatisticasController","movimentacoes"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('estatisticas.movimentacoes', 'entradas') }}"><i class="menu-icon fas fa-exchange-alt"></i> Movimentações</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("EstatisticasController","faltasGravidade"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('estatisticas.faltasGravidade') }}"><i class="menu-icon fas fa-file-contract"></i> Faltas e Sindicâncias</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("EstatisticasController","saudeSetor"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('estatisticas.saudeSetor') }}"><i class="menu-icon mdi mdi-play"></i> Atendimentos</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("EstatisticasController","escoltaUnidade"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('estatisticas.escoltaUnidade') }}"><i class="menu-icon fas fa-taxi"></i> Escoltas</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("EstatisticasController","ocorrenciasNatureza"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('estatisticas.ocorrenciasNatureza') }}"><i class="menu-icon fas fa-exclamation-triangle"></i> Ocorrências</a>
                            </li>
                            @endif
                            
                            @if(PermissaoService::getVerifyPermissionView("EstatisticasController","showVisita"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('estatisticas.showVisita') }}"><i class="menu-icon fas fa-users"></i> Visitantes</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("EstatisticasController","showAdvogado"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('estatisticas.showAdvogado') }}"><i class="menu-icon fas fa-gavel"></i> Advogados</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("EstatisticasController","showServidor"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('estatisticas.showServidor') }}"><i class="menu-icon mdi mdi-account-location"></i> Servidores</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("EstatisticasController","showCoeap"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('estatisticas.showCoeap') }}"><i class="menu-icon fas fa-hotel"></i> Coordenação</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("EstatisticasController","showDadosEmBranco"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('estatisticas.showDadosEmBranco') }}"><i class="menu-icon fas fa-database"></i> Dados em Branco</a>
                            </li>
                            @endif
                        </ul>
                    </div>

                </li>
                @endif

                @if(PermissaoService::getVerifyPermissionController("EscoltaController"))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#MenuEscoltas" aria-expanded="false"
                       aria-controls="ui-basic">
                        <i class="menu-icon fas fa-taxi"></i>
                        <span class="menu-title">Escoltas</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="MenuEscoltas">
                        <ul class="nav flex-column sub-menu">
                            @if(PermissaoService::getVerifyPermissionView("EscoltaController","buscar"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('escolta.buscar') }}"><i
                                            class="fa fa-search mr-1"></i> Buscar</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("EscoltaController","cadastro"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('escolta.cadastro') }}"><i
                                            class="fa fa-plus mr-1"></i> Cadastrar</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                @if(PermissaoService::getVerifyPermissionController("SindicanciaController"))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#MenuSindicancias" aria-expanded="false"
                       aria-controls="ui-basic">
                        <i class="menu-icon fas fa-file-contract"></i>
                        <span class="menu-title">Sindicâncias</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="MenuSindicancias">
                        <ul class="nav flex-column sub-menu">
                            @if(PermissaoService::getVerifyPermissionView("SindicanciaController","buscar"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('sindicancia.buscar') }}"><i
                                            class="fa fa-search mr-1"></i> Buscar</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("SindicanciaController","cadastro"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('sindicancia.cadastro') }}"><i
                                            class="fa fa-plus mr-1"></i> Cadastrar</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

<!--                @if($permissaoUsuario == 1000 || $permissaoUsuario == 400)

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="collapse" href="#MenuMatBelUnidade" aria-expanded="false"
                           aria-controls="ui-basic">
                            <i class="menu-icon mdi mdi-pistol"></i>
                            <span class="menu-title">Mat. Bél. Unidade</span>
                            <i class="menu-arrow"></i>
                        </a>

                        <div class="collapse" id="MenuMatBelUnidade">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('matBelUnidade.estoqueUnidade') }}"><i
                                                class="mdi mdi-chart-line mr-1"></i> Estoque da Unidade</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('matBelUnidade.UsoMaterial') }}"><i
                                                class="mdi mdi-file-document mr-1"></i> Uso de Material</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('matBelUnidade.listarUsoMaterial') }}"><i
                                                class="mdi mdi-format-list-numbers mr-1"></i>Listar Uso de Material</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#MenuArmaria" aria-expanded="false"
                       aria-controls="ui-basic">
                        <i class="menu-icon mdi mdi-pistol"></i>
                        <span class="menu-title">Armaria</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="MenuArmaria">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('armaria.buscar') }}"><i
                                            class="fa fa-search mr-1"></i> Buscar</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('armaria.cautela') }}"><i
                                            class="mdi mdi-share mr-1"></i> Realizar Cautela</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('armaria.listaCautela') }}"><i
                                            class="mdi mdi-format-list-numbers mr-1"></i> Listar Cautelas</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('armaria.listaDescautela') }}"><i
                                            class="mdi mdi-format-list-numbers mr-1"></i> Listar Descautelas</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('armaria.cadastro') }}"><i
                                            class="fa fa-plus mr-1"></i> Cadastrar Equip.</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('armaria.mapaequipamento') }}"><i
                                            class="mdi mdi-map-marker-radius mr-1"></i> Mapa Equip.</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('armaria.mapaLocalizacaoEquipamento') }}"><i
                                            class="mdi mdi-map-marker-radius mr-1"></i> Localização Equip.</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('armaria.relatorioUsoMaterial') }}"><i
                                            class="mdi mdi-format-list-numbers mr-1"></i> Listar Uso de Material</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('armaria.extravioEquipamentoListar') }}"><i
                                            class="mdi mdi-format-list-numbers mr-1"></i> Listar Equip. Extraviados</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('armaria.inserirTelefoneServidor') }}"><i
                                            class="fa fa-plus mr-1"></i> Inserir Telefone do Serv.</a>
                            </li>

                        </ul>
                    </div>
                </li>
                @endif-->


                @if(PermissaoService::getVerifyPermissionController("OcorrenciaController"))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#MenuOcorrencias" aria-expanded="false"
                       aria-controls="ui-basic">
                        <i class="menu-icon fas fa-exclamation-triangle"></i>
                        <span class="menu-title">Ocorrências</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="MenuOcorrencias">
                        <ul class="nav flex-column sub-menu">
                            @if(PermissaoService::getVerifyPermissionView("OcorrenciaController","buscar"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('ocorrencia.buscar') }}"><i
                                            class="fa fa-search mr-1"></i> Buscar</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("OcorrenciaController","cadastro"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route("ocorrencia.cadastro") }}"><i
                                            class="fa fa-plus mr-1"></i> Cadastrar</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("OcorrenciaController","livro"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route("ocorrencia.livro") }}"><i
                                            class="mdi mdi-file-document mr-1"></i> Livro</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                @if(PermissaoService::getVerifyPermissionController("MensagemController"))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#MenuMensagens" aria-expanded="false"
                       aria-controls="ui-basic">
                        <i class="menu-icon fas fa-envelope"></i>
                        <span class="menu-title"> Mensagens</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="MenuMensagens">
                        <ul class="nav flex-column sub-menu">
                            @if(PermissaoService::getVerifyPermissionView("MensagemController","enviar"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('mensagem.enviar') }}"><i
                                            class="fas fa-plus mr-1"></i> Enviar Mensagem</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("MensagemController","recebidas"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route("mensagem.recebidas") }}"><i
                                            class="mdi mdi-arrow-bottom-left mr-1"></i> Recebidas</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("MensagemController","enviadas"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route("mensagem.enviada") }}"><i
                                            class="mdi mdi-arrow-top-right mr-1"></i> Enviadas</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                {{--<li class="nav-item">--}}
                    {{--<a class="nav-link" data-toggle="collapse" href="#MenuAgenda" aria-expanded="false"--}}
                       {{--aria-controls="ui-basic">--}}
                        {{--<i class="menu-icon fas fa-calendar-alt"></i>--}}
                        {{--<span class="menu-title"> Agenda</span>--}}
                        {{--<i class="menu-arrow"></i>--}}
                    {{--</a>--}}

                    {{--<div class="collapse" id="MenuAgenda">--}}
                        {{--<ul class="nav flex-column sub-menu">--}}
                            {{--<li class="nav-item">--}}
                                {{--<a class="nav-link" href="{{ route('agenda.consulta') }}"><i--}}
                                            {{--class="fa fa-clock mr-1"></i> Consultar</a>--}}
                            {{--</li>--}}
                            {{--<li class="nav-item">--}}
                                {{--<a class="nav-link" href="{{ route('agenda.gerenciamento') }}"><i--}}
                                            {{--class="fa fa-cog mr-1"></i> Gerenciar Eventos</a>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                    {{--</div>--}}
                {{--</li>--}}

                @if(PermissaoService::getVerifyPermissionController("EquipeController"))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#MenuEquipe" aria-expanded="false"
                       aria-controls="ui-basic">
                        <i class="menu-icon fas fa-user"></i>
                        <span class="menu-title"> Equipe</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="MenuEquipe">
                        <ul class="nav flex-column sub-menu">

                            @if(PermissaoService::getVerifyPermissionView("EquipeController","dia"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('equipe.dia')}}"><i
                                            class="fa fa-users mr-1"></i> Equipe do dia</a>
                            </li>
                            @endif

                            @if(PermissaoService::getVerifyPermissionView("EquipeController","gerenciarEquipes"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('equipe.gerenciarequipes')}}"><i
                                            class="fa fa-plus mr-1"></i> Registrar Equipe</a>
                            </li>
                            @endif

                            @if(PermissaoService::getVerifyPermissionView("EquipeController","trocas"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('equipe.trocas')}}"><i
                                            class="fa fa-exchange-alt mr-1"></i> Registrar Trocas</a>
                            </li>
                            @endif

                        </ul>
                    </div>
                </li>
                @endif

                @if(PermissaoService::getVerifyPermissionController("ControleAcessoController"))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#MenuAcesso" aria-expanded="false"
                       aria-controls="ui-basic">
                        <i class="menu-icon fas fa-fingerprint"></i>
                        <span class="menu-title"> Controle de Acesso</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="MenuAcesso">
                        <ul class="nav flex-column sub-menu">
                            @if(PermissaoService::getVerifyPermissionView("ControleAcessoController","registrarEntrada"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('controleAcesso.registrarEntrada') }}"><i
                                            class="fas fa-sign-in-alt mr-1"></i> Registrar Entrada</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("ControleAcessoController","registrarSaida"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('controleAcesso.registrarSaida') }}"><i
                                            class="fas fa-sign-out-alt mr-1"></i> Registrar Saída</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("ControleAcessoController","historico"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('controleAcesso.historico') }}"><i
                                            class="fa fa-history mr-1"></i> Histórico</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("ControleAcessoController","alertaEntradaGeral"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('controleAcesso.alertaEntradaGeral') }}"><i
                                            class="mdi mdi-alert mr-1"></i> Criar Alerta</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("ControleAcessoController","alertaEntradaGeralBuscar"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('controleAcesso.alertaEntradaGeralBuscar') }}"><i
                                            class="fa fa-search mr-1"></i> Buscar Alertas</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                @if(PermissaoService::getVerifyPermissionController("SaudeAssistenciaController"))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#MenuSaude" aria-expanded="false"
                       aria-controls="ui-basic">
                        <i class="menu-icon fas fa-briefcase-medical"></i>
                        <span class="menu-title"> Saúde e Assistência</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="MenuSaude">
                        <ul class="nav flex-column sub-menu">
                            @if(PermissaoService::getVerifyPermissionView("SaudeAssistenciaController","historicoAtendimento"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('saudeAssistencia.historicoAtendimento') }}"><i
                                            class="fa fa-search mr-1"></i> Buscar Atendimentos</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("SaudeAssistenciaController","cadastroAtendimento"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('saudeAssistencia.cadastroAtendimento')}}"><i
                                            class="fas fa-plus mr-1"></i> Registrar Atendimento</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("SaudeAssistenciaController","internosAtendimento"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('saudeAssistencia.internosAtendimento') }}"><i
                                            class="fas fa-plus mr-1"></i> Internos para Atendimento</a>
                            </li>
                            @endif
                            @if(PermissaoService::getVerifyPermissionView("SaudeAssistenciaController","listaInternosAtendimento"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('saudeAssistencia.listaInternosAtendimento') }}"><i
                                            class="fa fa-tasks mr-1"></i> Lista para Atendimento</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                @if(PermissaoService::getVerifyPermissionController("UnidadesController"))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#MenuUnidades" aria-expanded="false"
                       aria-controls="ui-basic">
                        <i class="menu-icon fas fa-hotel"></i>
                        <span class="menu-title">Unidades</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="MenuUnidades">
                        <ul class="nav flex-column sub-menu">
                            @if(PermissaoService::getVerifyPermissionView("UnidadesController","show"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('unidades.show') }}"><i
                                            class="menu-icon fas fa-user-friends"></i>Diretores e Vices
                                </a>
                            </li>
                            @endif
                            
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('mapa-unidades') }}">
                                    <i class="menu-icon fas fa-map"></i>Mapa das Unidades
                                </a>
                            </li>
                            
                            @if(PermissaoService::getVerifyPermissionView("UnidadesController","unidadeGerenciar"))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('unidade.unidadeGerenciar') }}">
                                    <i class="fas fa-cogs mr-1"></i>Gerenciar Unidade</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                
                @if(PermissaoService::getVerifyPermissionController("SeticController"))
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="collapse" href="#MenuSetic" aria-expanded="false"
                           aria-controls="ui-basic">
                            <i class="menu-icon fas fa-code"></i>
                            <span class="menu-title"> {{ config('app.ti') }}</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="MenuSetic">
                            <ul class="nav flex-column sub-menu">

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('setic.log') }}">
                                        <i class="fa fa-history mr-1"></i> Log</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('setic.listarUsuarios') }}"><i
                                                class="mdi mdi-account mr-1"></i> Usuários</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('setic.permissoesListarPerfis') }}"><i
                                                class="mdi mdi-security mr-1"></i> Permissões</a>
                                </li>

                                {{-- <li class="nav-item"> --}}
                                    {{-- <a class="nav-link" href="{{ route('setic.recuperacaoContas') }}"><i --}}
                                    {{-- class="mdi mdi-account-check mr-1"></i> Recuperação de Contas</a> --}}
                                        {{-- </li> --}}

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('setic.listarChamadosHelpDesk') }}"><i
                                                class="mdi mdi-help-network mr-1"></i> Help Desk</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('setic.unidadesListar') }}"><i
                                                class="fas fa-cogs mr-1"></i> Gerenciar Unidades</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('setic.listaObjetosOcorrencia') }}"><i
                                                class="mdi mdi-format-list-bulleted mr-1"></i> Ger. Objt. Ocorrência</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="collapse" href="#MenuUnificacao" aria-expanded="false"
                           aria-controls="ui-basic">
                            <i class="menu-icon fas fa-users-cog"></i>
                            <span class="menu-title"> Unificações</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="MenuUnificacao">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('setic.unificacao.buscar.buscar') }}"><i
                                                class="fa fa-search mr-1"></i> Buscar</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('setic.unificacao.cadastrar.join') }}"><i
                                                class="fa fa-plus mr-1"></i> Unificar Internos</a>
                                </li>

                                {{--<li class="nav-item">--}}
                                {{--<a class="nav-link" href="{{ route('setic.unificacao.cadastrar.visita') }}"><i--}}
                                {{--class="fa fa-plus mr-1"></i> Unificar Visita</a>--}}
                                {{--</li>--}}

                                {{--<li class="nav-item">--}}
                                {{--<a class="nav-link" href="{{ route('setic.unificacao.cadastrar.advogado') }}"><i--}}
                                {{--class="fa fa-plus mr-1"></i> Unificar Advogado</a>--}}
                                {{--</li>--}}
                            </ul>
                        </div>
                    </li>

                @endif

            </ul>
        </nav>
        <!-- partial -->
        <div class="main-panel">
            <div class="content-wrapper">
                {{--<div class="flash-message padding02em">--}}
                    {{--@foreach (['danger', 'warning', 'success', 'info'] as $msg)--}}
                        {{--@if(Session::has('alert-' . $msg))--}}
                            {{--<p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}--}}
                                {{--<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>--}}
                        {{--@endif--}}
                    {{--@endforeach--}}
                {{--</div>--}}
                @yield('content')
            </div>
            <!-- content-wrapper ends -->
            <!-- partial:partials/_footer.html -->
            <footer class="footer">
                <div class="container-fluid clearfix">
                    <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">DESENVOLVIDO POR: DTI SEAP RN</span>
                </div>
            </footer>
            <!-- partial -->
        </div>
        <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->

<!-- plugins:js -->
<script src="{{asset('js/template/tema_geral/vendor.bundle.base.js')}}"></script>
<script src="{{asset('js/template/tema_geral/vendor.bundle.addons.js')}}"></script>
<!-- endinject -->
<!-- Plugin js for this page-->
<!-- End plugin js for this page-->
<!-- inject:js -->
<script src="{{asset('js/template/tema_geral/off-canvas.js')}}"></script>
<script src="{{asset('js/template/tema_geral/misc.js')}}"></script>
<!-- endinject -->
<!-- Custom js for this page-->
<script src="{{asset('js/template/tema_geral/dashboard.js')}}"></script>
<!-- End custom js for this page-->
@stack('script')
</body>

</html>