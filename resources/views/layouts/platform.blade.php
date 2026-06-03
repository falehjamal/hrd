<!DOCTYPE html>
<html lang="id" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('sneat/') }}/">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Platform') - {{ config('platform.name') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('sneat/img/favicon/favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/vendor/css/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    @vite(['resources/css/app.css', 'resources/js/datatables.js'])
    <script src="{{ asset('sneat/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('sneat/js/config.js') }}"></script>
    @stack('styles')
</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="{{ route('platform.dashboard') }}" class="app-brand-link">
                        <span class="app-brand-text demo menu-text fw-bolder sidebar-brand-uppercase">{{ platform_sidebar_title() }}</span>
                    </a>
                </div>
                <div class="menu-inner-shadow"></div>
                <ul class="menu-inner py-1">
                    <li class="menu-item {{ request()->routeIs('platform.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('platform.dashboard') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div>Dashboard</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('platform.tenants.*') ? 'active' : '' }}">
                        <a href="{{ route('platform.tenants.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-buildings"></i>
                            <div>Tenant</div>
                        </a>
                    </li>
                </ul>
            </aside>

            <div class="layout-page">
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="navbar-nav-right d-flex align-items-center w-100 justify-content-end" id="navbar-collapse">
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <li class="nav-item">
                                <span class="fw-medium me-2">{{ auth('platform')->user()->name }}</span>
                            </li>
                            <li class="nav-item ms-2">
                                <form method="POST" action="{{ route('platform.logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </nav>

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @yield('content')
                    </div>
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div>&copy; {{ date('Y') }} {{ config('platform.name') }}</div>
                            <div>
                                <a href="{{ route('login') }}" class="footer-link">Login tenant</a>
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    </div>

    @include('partials.delete-modal')

    <script src="{{ asset('sneat/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('sneat/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('sneat/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('sneat/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('sneat/js/main.js') }}"></script>
    @stack('datatable-scripts')
    @stack('scripts')
</body>
</html>
