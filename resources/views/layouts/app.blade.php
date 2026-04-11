<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FMS') }}</title>

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Highcharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/highcharts@11.4.8/highcharts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/highcharts@11.4.8/modules/series-label.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/highcharts@11.4.8/modules/exporting.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/highcharts@11.4.8/modules/export-data.min.js"></script>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.documentElement.classList.add('page-loader-enabled');
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            dropdownElementList.forEach(function(dropdownToggle) {
                new bootstrap.Dropdown(dropdownToggle);
            });
        });
    </script>
    <style>
        :root {
            --app-mobile-gutter: 0.75rem;
            --page-loader-bg: rgba(248, 250, 252, 0.96);
            --page-loader-primary: #0d6efd;
            --page-loader-accent: #7cb5ff;
            --page-loader-text: #12304f;
        }

        main.py-4 {
            padding-top: 1rem !important;
            padding-bottom: 1.5rem !important;
        }

        body {
            transition: opacity 0.2s ease;
        }

        .page-loader {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background:
                radial-gradient(circle at top, rgba(124, 181, 255, 0.32), transparent 42%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.92), var(--page-loader-bg));
            opacity: 1;
            visibility: visible;
            transition: opacity 0.35s ease, visibility 0.35s ease;
        }

        body.page-ready .page-loader {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .page-loader-panel {
            min-width: min(100%, 20rem);
            max-width: 24rem;
            padding: 1.5rem 1.35rem;
            border: 1px solid rgba(13, 110, 253, 0.12);
            border-radius: 1.25rem;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 20px 45px rgba(18, 48, 79, 0.14);
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .page-loader-brand {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 3.5rem;
            height: 3.5rem;
            margin-bottom: 1rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, var(--page-loader-primary), #4aa3ff);
            color: #fff;
            font-size: 1.4rem;
            box-shadow: 0 10px 24px rgba(13, 110, 253, 0.28);
        }

        .page-loader-spinner {
            position: relative;
            width: 3.75rem;
            height: 3.75rem;
            margin: 0 auto 1rem;
        }

        .page-loader-spinner::before,
        .page-loader-spinner::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 3px solid transparent;
        }

        .page-loader-spinner::before {
            border-top-color: var(--page-loader-primary);
            border-right-color: var(--page-loader-primary);
            animation: page-loader-spin 0.9s linear infinite;
        }

        .page-loader-spinner::after {
            inset: 0.4rem;
            border-bottom-color: var(--page-loader-accent);
            border-left-color: var(--page-loader-accent);
            animation: page-loader-spin-reverse 1.2s linear infinite;
        }

        .page-loader-title {
            margin: 0;
            color: var(--page-loader-text);
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .page-loader-copy {
            margin: 0.45rem 0 0;
            color: #5b7087;
            font-size: 0.92rem;
        }

        @keyframes page-loader-spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes page-loader-spin-reverse {
            to {
                transform: rotate(-360deg);
            }
        }

        .page-shell {
            width: 100%;
        }

        .page-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .page-title {
            margin-bottom: 0;
        }

        .page-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            width: 100%;
        }

        .page-actions>* {
            flex: 1 1 auto;
        }

        .responsive-filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: end;
        }

        .responsive-filter-form .form-select,
        .responsive-filter-form .form-control,
        .responsive-filter-form .btn,
        .responsive-filter-form .form-check,
        .responsive-filter-form .form-check-label {
            width: 100%;
        }

        .responsive-filter-form .btn {
            white-space: nowrap;
        }

        .table-card {
            overflow: hidden;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .excel-table {
            min-width: 640px;
        }

        .table-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .table-actions .btn,
        .table-actions form {
            margin: 0;
        }

        .pagination-shell {
            display: flex;
            justify-content: center;
            width: 100%;
            overflow-x: auto;
            padding-bottom: 0.25rem;
        }

        .pagination-shell .pagination {
            flex-wrap: nowrap;
        }

        .navbar .nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0.5rem;
        }

        .navbar .nav-link i {
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
        }

        .navbar .nav-link span {
            font-size: 0.75rem;
        }

        .navbar .user-dropdown-toggle {
            flex-direction: row;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar .user-dropdown-toggle i {
            margin-bottom: 0;
            font-size: 1rem;
        }

        .navbar .user-dropdown-toggle span {
            font-size: 0.95rem;
        }

        .nav-scroll-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            width: 100%;
        }

        .nav-scroll-container::-webkit-scrollbar {
            height: 4px;
        }

        .nav-scroll-container::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 2px;
        }

        .nav-scroll-container .nav {
            flex-wrap: nowrap;
            white-space: nowrap;
        }

        @media (min-width: 768px) {
            .nav-scroll-container {
                overflow: visible;
            }

            .nav-scroll-container .nav {
                flex-wrap: wrap;
                white-space: normal;
            }
        }

        .navbar .navbar-collapse {
            gap: 1rem;
        }

        .navbar .navbar-nav {
            gap: 0.25rem;
        }

        @media (max-width: 575.98px) {
            .page-loader-panel {
                padding: 1.25rem 1rem;
                border-radius: 1rem;
            }

            .page-loader-brand {
                width: 3rem;
                height: 3rem;
                font-size: 1.2rem;
            }

            .container {
                padding-left: var(--app-mobile-gutter);
                padding-right: var(--app-mobile-gutter);
            }

            .page-header {
                align-items: stretch;
            }

            .auth-card {
                margin: 0.5rem;
                max-width: calc(100% - 1rem);
            }

            .auth-body {
                padding: 1.25rem;
            }

            h4,
            .h4 {
                font-size: 1rem;
            }

            .btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }

            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }

            .card-body {
                padding: 0.75rem;
            }

            .form-select,
            .form-control {
                font-size: 0.875rem;
            }

            .table-responsive {
                margin-left: calc(var(--app-mobile-gutter) * -0.25);
                margin-right: calc(var(--app-mobile-gutter) * -0.25);
            }
        }

        .filter-form-mobile {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        @media (min-width: 576px) {
            .filter-form-mobile {
                flex-direction: row;
                flex-wrap: wrap;
            }
        }

        .filter-form-mobile .form-select,
        .filter-form-mobile .form-control,
        .filter-form-mobile .btn {
            flex: 1 1 100%;
            min-width: 120px;
        }

        @media (min-width: 576px) {

            .filter-form-mobile .form-select,
            .filter-form-mobile .form-control {
                flex: 1 1 auto;
            }

            .filter-form-mobile .btn {
                flex: 0 0 auto;
            }
        }

        /* Excel-like table styles with blue theme */
        .excel-table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 1rem;
            background-color: #fff;
        }

        .excel-table th,
        .excel-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .excel-table thead th {
            background-color: var(--bs-primary, #0d6efd);
            font-weight: bold;
            color: #fff;
        }

        .excel-table tbody tr:nth-child(even)>* {
            background-color: #d9e8f7 !important;
        }

        .excel-table tbody tr:hover>* {
            background-color: #6fb6ff;
        }

        @media (min-width: 576px) {
            .page-actions {
                width: auto;
            }

            .page-actions>* {
                width: auto;
                flex: 0 0 auto;
            }

            .responsive-filter-form .form-select,
            .responsive-filter-form .form-control {
                width: auto;
                flex: 1 1 12rem;
            }

            .responsive-filter-form .btn,
            .responsive-filter-form .form-check {
                width: auto;
                flex: 0 0 auto;
            }
        }

        @media (max-width: 767.98px) {
            .navbar .nav-link {
                flex-direction: row;
                justify-content: flex-start;
                gap: 0.5rem;
                align-items: center;
            }

            .navbar .nav-link i {
                margin-bottom: 0;
                font-size: 1rem;
            }

            .navbar .nav-link span {
                font-size: 0.95rem;
            }

            .navbar .user-dropdown-toggle {
                justify-content: flex-start;
            }

            footer small {
                display: inline-block;
                line-height: 1.5;
            }
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <div id="pageLoader" class="page-loader" aria-live="polite" aria-label="Page loading">
        <div class="page-loader-panel">
            <div class="page-loader-brand">
                <i class="bi bi-bar-chart-line-fill"></i>
            </div>
            <div class="page-loader-spinner" aria-hidden="true"></div>
            <p class="page-loader-title">{{ config('app.name', 'FMS') }}</p>
            <p class="page-loader-copy">Preparing your workspace...</p>
        </div>
    </div>
    <div id="app" class="d-flex flex-column flex-grow-1">
        @auth
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <div class="container">
                    <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="{{ url('/') }}">
                        <i class="bi bi-speedometer2 me-2"></i>
                        <span class="d-none d-sm-inline">{{ config('app.name', 'FMS') }}</span>
                        <span class="d-sm-none">FMS</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <div class="nav-scroll-container">
                            <ul class="navbar-nav me-auto">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'text-primary fw-semibold' : '' }}"
                                        href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i><span>
                                            {{ __('Dashboard') }}</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('cost-centres.*') ? 'text-primary fw-semibold' : '' }}"
                                        href="{{ route('cost-centres.index') }}"><i class="bi bi-diagram-3"></i><span>
                                            {{ __('Cost Centres') }}</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('accounts.*') ? 'text-primary fw-semibold' : '' }}"
                                        href="{{ route('accounts.index') }}"><i class="bi bi-wallet2"></i><span>
                                            {{ __('Accounts') }}</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('budgets.*') ? 'text-primary fw-semibold' : '' }}"
                                        href="{{ route('budgets.index') }}"><i class="bi bi-calculator"></i><span>
                                            {{ __('Budgets') }}</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('actuals.*') ? 'text-primary fw-semibold' : '' }}"
                                        href="{{ route('actuals.index') }}"><i class="bi bi-receipt"></i><span>
                                            {{ __('Actuals') }}</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('reports') ? 'text-primary fw-semibold' : '' }}"
                                        href="{{ route('reports') }}"><i class="bi bi-file-earmark-ruled"></i><span>
                                            {{ __('Reports') }}</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('forecast') ? 'text-primary fw-semibold' : '' }}"
                                        href="{{ route('forecast') }}"><i class="bi bi-graph-up-arrow"></i><span>
                                            {{ __('Forecast') }}</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('import.*') ? 'text-primary fw-semibold' : '' }}"
                                        href="{{ route('import.actuals') }}"><i class="bi bi-upload"></i><span>
                                            {{ __('Import') }}</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('alerts') ? 'text-primary fw-semibold' : '' }}"
                                        href="{{ route('alerts') }}"><i class="bi bi-bell"></i><span>
                                            {{ __('Alerts') }}</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('analysis') ? 'text-primary fw-semibold' : '' }}"
                                        href="{{ route('analysis') }}"><i class="bi bi-cpu"></i><span>
                                            {{ __('AI Analysis') }}</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('charts') ? 'text-primary fw-semibold' : '' }}"
                                        href="{{ route('charts') }}"><i class="bi bi-pie-chart"></i><span>
                                            {{ __('Charts') }}</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('powerbi') ? 'text-primary fw-semibold' : '' }}"
                                        href="{{ route('powerbi') }}"><i class="bi bi-bar-chart-fill"></i><span>
                                            {{ __('Power BI') }}</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('settings.*') ? 'text-primary fw-semibold' : '' }}"
                                        href="{{ route('settings.index') }}"><i class="bi bi-gear"></i><span>
                                            {{ __('Settings') }}</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('tutorial') ? 'text-primary fw-semibold' : '' }}"
                                        href="{{ route('tutorial') }}"><i class="bi bi-book"></i><span>
                                            {{ __('Tutorial') }}</span></a>
                                </li>
                            </ul>
                        </div>

                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle user-dropdown-toggle" href="#"
                                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                    v-pre>
                                    <i class="bi bi-person-circle text-primary"></i>
                                    <span>{{ Auth::user()->name }}</span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('settings.profile.page') }}">
                                        <i class="bi bi-person"></i> {{ __('Profile') }}
                                    </a>
                                    <a class="dropdown-item" href="#"
                                        data-confirm="Are you sure you want to logout?"
                                        data-confirm-form="logout-form">
                                        <i class="bi bi-box-arrow-right"></i> {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        @endauth

        <main class="py-4">
            @yield('content')
        </main>

        <footer class="bg-white border-top py-3 mt-auto">
            <div class="container text-center text-muted">
                <small>&copy; {{ date('Y') }} Financial Management System. Created by TazarChriss</small>
            </div>
        </footer>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var body = document.body;
            var pageLoader = document.getElementById('pageLoader');
            var hidePageLoader = function() {
                body.classList.add('page-ready');

                if (pageLoader) {
                    pageLoader.setAttribute('aria-hidden', 'true');
                }
            };

            var showPageLoader = function() {
                body.classList.remove('page-ready');

                if (pageLoader) {
                    pageLoader.setAttribute('aria-hidden', 'false');
                }
            };

            window.addEventListener('load', hidePageLoader);
            setTimeout(hidePageLoader, 600);

            document.querySelectorAll('a[href]').forEach(function(link) {
                link.addEventListener('click', function(event) {
                    if (event.defaultPrevented || this.target === '_blank' || this.hasAttribute('download')) {
                        return;
                    }

                    var href = this.getAttribute('href');
                    if (!href || href === '#' || href.startsWith('javascript:') || href.startsWith('mailto:') || href
                        .startsWith('tel:')) {
                        return;
                    }

                    if (this.dataset.bsToggle || this.dataset.confirmForm) {
                        return;
                    }

                    var currentUrl = new URL(window.location.href);
                    var nextUrl = new URL(href, window.location.href);

                    if (currentUrl.href === nextUrl.href || nextUrl.origin !== currentUrl.origin) {
                        return;
                    }

                    showPageLoader();
                });
            });

            document.querySelectorAll('form').forEach(function(form) {
                form.addEventListener('submit', function() {
                    if (this.dataset.confirm) {
                        return;
                    }

                    showPageLoader();
                });
            });

            document.querySelectorAll('form[data-confirm]').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    var message = this.getAttribute('data-confirm') ||
                        'Are you sure you want to delete this item?';
                    e.preventDefault();
                    var formToSubmit = this;
                    Swal.fire({
                        title: 'Confirm Delete',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Delete',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            formToSubmit.submit();
                        }
                    });
                });
            });

            document.querySelectorAll('button[data-confirm]').forEach(function(button) {
                button.addEventListener('click', function(e) {
                    var message = this.getAttribute('data-confirm') ||
                        'Are you sure you want to delete this item?';
                    var form = this.closest('form');
                    if (!form) return;
                    e.preventDefault();
                    Swal.fire({
                        title: 'Confirm Delete',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Delete',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            document.querySelectorAll('a[data-confirm]').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    var message = this.getAttribute('data-confirm') || 'Are you sure?';
                    var formId = this.getAttribute('data-confirm-form');
                    e.preventDefault();
                    var href = this.getAttribute('href');
                    Swal.fire({
                        title: 'Confirm Action',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Confirm',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (!result.isConfirmed) {
                            return;
                        }

                        if (formId) {
                            var form = document.getElementById(formId);
                            if (form) {
                                form.submit();
                                return;
                            }
                        }

                        if (href && href !== '#') {
                            window.location.href = href;
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>
