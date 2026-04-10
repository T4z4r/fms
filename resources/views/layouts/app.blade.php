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
        document.addEventListener('DOMContentLoaded', function() {
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            dropdownElementList.forEach(function(dropdownToggle) {
                new bootstrap.Dropdown(dropdownToggle);
            });
        });
    </script>
    <style>
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

        .nav-scroll-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
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

        @media (max-width: 575.98px) {
            .auth-card {
                margin: 0.5rem;
                max-width: calc(100% - 1rem);
            }
            .auth-body {
                padding: 1.25rem;
            }
            h4, .h4 {
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
            .form-select, .form-control {
                font-size: 0.875rem;
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
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
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
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('settings.profile.page') }}">
                                        <i class="bi bi-person"></i> {{ __('Profile') }}
                                    </a>
                                    <a class="dropdown-item" href="#" data-confirm="Are you sure you want to logout?">
                                        <i class="bi bi-box-arrow-right"></i> {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
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
        document.querySelectorAll('form[data-confirm]').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                var message = this.getAttribute('data-confirm') || 'Are you sure you want to delete this item?';
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
                var message = this.getAttribute('data-confirm') || 'Are you sure you want to delete this item?';
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
                    if (result.isConfirmed && href) {
                        window.location.href = href;
                    }
                });
            });
        });
    });
</script>
</body>

</html>
