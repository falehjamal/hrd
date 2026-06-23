<!DOCTYPE html>
<html lang="id" class="light-style layout-menu-fixed hrd-app" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('sneat/') }}/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ tenant_app_name() }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('sneat/img/favicon/favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300..700;1,14..32,300..700&family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('sneat/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('sneat/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    @include('partials.theme-boot')
    @vite(['resources/css/app.css', 'resources/js/datatables.js'])
    <script src="{{ asset('sneat/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('sneat/js/config.js') }}"></script>
    @stack('styles')
</head>
<body class="hrd-body">
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('partials.sidebar')

            <div class="layout-page">
                @include('partials.navbar')

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @yield('content')
                    </div>

                    @include('partials.footer')
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>

        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    @include('partials.delete-modal')

    <script src="{{ asset('sneat/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('sneat/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('sneat/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('sneat/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('sneat/js/main.js') }}"></script>
    @vite(['resources/js/ui-preferences.js'])
    @stack('datatable-scripts')
    @stack('scripts')
</body>
</html>
