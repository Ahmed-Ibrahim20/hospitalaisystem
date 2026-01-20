<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ in_array(app()->getLocale(), ['ar','ar_EG','ar-SA']) ? 'rtl' : 'ltr' }}"
    data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'TravelApp'))</title>
    <meta name="description" content="@yield('meta_description', 'Dashboard of ' . config('app.name', 'TravelApp'))">
    <link rel="canonical" href="{{ url()->current() }}" />

    {{-- Fonts --}}
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    {{-- Bootstrap (LTR/RTL depending on language) --}}
    @if(in_array(app()->getLocale(), ['ar','ar_EG','ar-SA']))
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"
        integrity="sha384-p8GfD1xk8lqv7o5Hq8f8yC9KJQz1Q+QxqkXc6k2zfr4m4sDk5m7S8y9oK3q8n9el"
        crossorigin="anonymous">
    @else
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">
    @endif

    {{-- Font Awesome --}}
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- UI Colors + Small Enhancements --}}
    <style>
        .image-upload-wrapper {
            background: #f9f9fb;
            transition: all 0.3s ease;
        }

        .image-upload-wrapper:hover {
            background: #eef6ff;
            border-color: #0d6efd;
        }

        :root {
            --primary: #4f46e5;
            --primary-700: #4338ca;
            --accent: #f59e0b;
            --bg: #f6f7fb;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --hover: rgba(79, 70, 229, .08);
            --active: rgba(79, 70, 229, .16);
        }

        [data-theme="dark"] {
            --bg: #0b1220;
            --card: #121a2a;
            --text: #e5e7eb;
            --muted: #9ca3af;
            --hover: rgba(99, 102, 241, .15);
            --active: rgba(99, 102, 241, .25);
        }

        html,
        body {
            height: 100%;
        }

        body {
            font-family: 'Figtree', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji";
            background: var(--bg);
            color: var(--text);
        }

        .navbar-modern {
            background: var(--card);
            border-bottom: 1px solid rgba(0, 0, 0, .06);
            transition: all .25s ease;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: .2px;
        }

        .navbar-modern .nav-link {
            font-weight: 600;
            border-radius: .6rem;
            padding: .5rem .85rem;
            color: var(--muted);
            transition: .2s;
        }

        .navbar-modern .nav-link:hover {
            background: var(--hover);
            color: var(--primary);
        }

        .navbar-modern .nav-link.active {
            background: var(--active);
            color: var(--primary);
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .12);
            object-fit: cover;
        }

        .content-wrap {
            min-height: calc(100vh - 64px);
            padding: 1.25rem 0 2rem;
        }

        .card-soft {
            background: var(--card);
            border: 1px solid rgba(0, 0, 0, .06);
            border-radius: .9rem;
            box-shadow: 0 6px 16px -8px rgba(0, 0, 0, .12);
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background: var(--primary-700);
            border-color: var(--primary-700);
        }

        .dropdown-menu {
            border: none;
            background: var(--card);
            box-shadow: 0 10px 28px rgba(0, 0, 0, .12);
            border-radius: .8rem;
        }

        /* Toast position fix for RTL */
        .swal2-top-end {
            inset-inline-start: auto !important;
            inset-inline-end: 1.25rem !important;
        }

        /* Font Awesome Fallback */
        .fa-solid,
        .fa-regular,
        .fa-brands,
        .fas,
        .far,
        .fab {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Pro", "FontAwesome", sans-serif !important;
            font-weight: 900 !important;
            display: inline-block;
        }

        .fa-search:before {
            content: "🔍";
        }
    </style>

    @stack('styles')
    @yield('head')
</head>

<body class="antialiased">

    <nav class="navbar navbar-expand-lg navbar-modern sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                <i class="fa-solid fa-compass text-warning"></i>
                <span>{{ config('app.name', 'TravelApp') }}</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse order-lg-1" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard*') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">Home</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                            href="{{ route('users.index') }}">
                        
                            المستخدمين
                        </a>
                    </li>
                

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}"
                            href="{{ route('roles.index') }}">
                            <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11.5C14.8,12.4 14.4,13.2 13.7,13.7V16.5C13.7,17.1 13.3,17.5 12.7,17.5H11.3C10.7,17.5 10.3,17.1 10.3,16.5V13.7C9.6,13.2 9.2,12.4 9.2,11.5V10C9.2,8.6 10.6,7 12,7Z" />
                            </svg>
                            الأدوار
                        </a>
                    </li>
        

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}"
                            href="{{ route('patients.index') }}">
                            <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 5.5V7.5L12 8.5L9 7.5V5.5L3 7V9L9 10.5V12L3 13.5V15.5L9 14L12 15L15 14L21 15.5V13.5L15 12V10.5L21 9ZM12 10.5C10.9 10.5 10 11.4 10 12.5S10.9 14.5 12 14.5 14 13.6 14 12.5 13.1 10.5 12 10.5Z" />
                            </svg>
                            المرضى
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('encounters.*') ? 'active' : '' }}"
                            href="{{ route('encounters.index') }}">
                            <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19.5 3.5L18.1 4.9C19.2 6 19.8 7.5 19.8 9.1C19.8 12.4 17.1 15.1 13.8 15.1S7.8 12.4 7.8 9.1C7.8 7.5 8.4 6 9.5 4.9L8.1 3.5C6.7 4.9 5.8 6.9 5.8 9.1C5.8 13.5 9.4 17.1 13.8 17.1S21.8 13.5 21.8 9.1C21.8 6.9 20.9 4.9 19.5 3.5M7 17.1C6.4 17.1 6 17.5 6 18.1V21C6 21.6 6.4 22 7 22S8 21.6 8 21V18.1C8 17.5 7.6 17.1 7 17.1Z" />
                            </svg>
                            الزيارات
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('medicines.*') ? 'active' : '' }}"
                            href="{{ route('medicines.index') }}">
                            <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M6,13C6.55,13 7,13.45 7,14A1,1 0 0,1 6,15A1,1 0 0,1 5,14C5,13.45 5.45,13 6,13M6,17C6.55,17 7,17.45 7,18A1,1 0 0,1 6,19A1,1 0 0,1 5,18C5,17.45 5.45,17 6,17M6,9A1,1 0 0,1 7,10A1,1 0 0,1 6,11A1,1 0 0,1 5,10A1,1 0 0,1 6,9M3,5A2,2 0 0,1 5,3H19A2,2 0 0,1 21,5V19A2,2 0 0,1 19,21H5A2,2 0 0,1 3,19V5M9,7V9H19V7H9M9,11V13H19V11H9M9,15V17H19V15H9Z" />
                            </svg>
                            الصيدلية
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('ai.*') ? 'active' : '' }}"
                            href="{{ route('ai.dashboard') }}">
                            <i class="fa-solid fa-brain me-1"></i>
                            الذكاء الاصطناعي
                        </a>
                    </li>
                </ul>

                @auth
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="fw-semibold">{{ Auth::user()->name }}</span>
                            <img class="avatar"
                                src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4f46e5&color=fff"
                                alt="Avatar">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2"
                                    href="{{ route('profile.edit') }}">
                                    <i class="fa-solid fa-user text-muted"></i>
                                    <span>Profile</span>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                {{-- Logout Form --}}
                                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="d-none">
                                    @csrf
                                </form>
                                <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-danger"
                                    onclick="confirmLogout()">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    <span>Logout</span>
                                </button>
                            </li>
                        </ul>
                    </li>
                </ul>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Breadcrumb (Optional) --}}
    @if(View::hasSection('breadcrumb'))
    <div class="container mt-3">
        <div class="card-soft p-3">
            @yield('breadcrumb')
        </div>
    </div>
    @endif

    <div class="container content-wrap">
        {{-- Flash Messages -> SweetAlert Toast --}}
        @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    icon: 'success',
                    title: {
                        !!json_encode(session('success')) !!
                    }
                });
            });
        </script>
        @endif
        @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3500,
                    icon: 'error',
                    title: {
                        !!json_encode(session('error')) !!
                    }
                });
            });
        </script>
        @endif

        <main>
            @yield('content')
        </main>
    </div>

    <footer class="py-4 mt-auto">
        <div class="container text-center text-muted small">
            <span class="d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-plane-departure"></i>
                <span>{{ config('app.name', 'TravelApp') }} &copy; {{ now()->year }}</span>
            </span>
        </div>
    </footer>

    {{-- Bootstrap Bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script>
        // Confirm Logout
        function confirmLogout() {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم تسجيل خروجك من النظام.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، تسجيل الخروج',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }

        // Dark/Light Theme Toggle
        (function() {
            const storageKey = 'hospitalapp-theme';
            const html = document.documentElement;
            const saved = localStorage.getItem(storageKey);

            if (saved === 'dark' || saved === 'light') {
                html.setAttribute('data-theme', saved);
            }

            const btn = document.getElementById('themeToggle');
            if (btn) {
                btn.innerHTML = html.getAttribute('data-theme') === 'dark' ?
                    '<svg class="icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1z"/></svg>' :
                    '<svg class="icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9c0-.46-.04-.92-.1-1.36-.98 1.37-2.58 2.26-4.4 2.26-2.98 0-5.4-2.42-5.4-5.4 0-1.81.89-3.42 2.26-4.4-.44-.06-.9-.1-1.36-.1z"/></svg>';

                btn.addEventListener('click', () => {
                    const current = html.getAttribute('data-theme');
                    const next = current === 'dark' ? 'light' : 'dark';
                    html.setAttribute('data-theme', next);
                    localStorage.setItem(storageKey, next);

                    btn.innerHTML = next === 'dark' ?
                        '<svg class="icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1z"/></svg>' :
                        '<svg class="icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9c0-.46-.04-.92-.1-1.36-.98 1.37-2.58 2.26-4.4 2.26-2.98 0-5.4-2.42-5.4-5.4 0-1.81.89-3.42 2.26-4.4-.44-.06-.9-.1-1.36-.1z"/></svg>';
                });
            }
        })();
    </script>

    @stack('scripts')
    @yield('scripts')
</body>

</html>