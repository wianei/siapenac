<!DOCTYPE html>
<html lang="pt-br">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }}</title>
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
    <link rel="stylesheet" href="{{asset('css/template/oab/styleOab.css')}}">
    <link rel="stylesheet" href="{{asset('css/util.css')}}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{asset('images/template/tema_geral/favicon.png')}}"/>
    @stack('css')
</head>

<body>
<div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center">
            <a class="navbar-brand brand-logo" href="{{route('inicio')}}">
                <h2>{{ config('app.name') }}</h2>
            </a>
            <a class="navbar-brand brand-logo-mini" href="{{route('inicio')}}">
                <h2>{{ config('app.name') }}</h2>
            </a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center">
            <ul class="navbar-nav navbar-nav-right">
                <li class="nav-item dropdown d-none d-xl-inline-block">
                    <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown"
                       aria-expanded="false">
                        <span class="profile-text">Olá, {{ \App\Http\Controllers\Advogado\AdvogadoController::getNomeAdvCPF(Auth::user()->cpf)}} !</span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                        <a class="dropdown-item mt-4" href="password/reset">
                            Alterar Senha
                        </a>
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
                <li class="nav-item nav-profile">
                </li>
                {{--<li class="nav-item">--}}
                    {{--<a class="nav-link" href="{{ route('inicio') }}">--}}
                        {{--<i class="menu-icon fas fa-home"></i>--}}
                        {{--<span class="menu-title">Página Inicial</span>--}}
                    {{--</a>--}}
                {{--</li>--}}
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false"
                       aria-controls="ui-basic">
                        <i class="menu-icon mdi mdi-calendar"></i>
                        <span class="menu-title">Agendamento</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="ui-basic">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('oab.home') }}"><i
                                            class="fa fa-plus mr-1"></i> Agendar</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('oab.cancelarAgendamento') }}"><i
                                            class="mdi mdi mdi-close mr-1"></i> Cancelar</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('oab.historicoAgendamento') }}"><i
                                            class="fas fa-history mr-1"></i> Histórico</a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- partial -->
        <div class="main-panel">
            <div class="content-wrapper">
                @yield('content')
            </div>
            <!-- content-wrapper ends -->
            <!-- partial:partials/_footer.html -->
            <footer class="footer">
                <div class="container-fluid clearfix">
                    <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © 2018 - DESENVOLVIDO POR: DTI SEAP RN</span>
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