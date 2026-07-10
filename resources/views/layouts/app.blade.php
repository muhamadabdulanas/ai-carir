<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AI-Carir - AI Career Guidance')</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css'])
    
    <!-- Feather Icons for modern graphics -->
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar no-print">
        <div class="container flex items-center justify-between">
            <a href="/" class="navbar-brand">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
                <span class="gradient-text">AI-Carir</span>
            </a>

            @auth
                <div class="nav-links">
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">Dashboard Admin</a>
                    @else
                        <a href="{{ route('student.dashboard') }}" class="nav-link {{ Route::is('student.dashboard') ? 'active' : '' }}">Dashboard Karir</a>
                    @endif
                    
                    <div class="flex items-center gap-4">
                        <span class="user-badge">
                            <i data-feather="user" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;"></i>
                            {{ Auth::user()->name }} ({{ Auth::user()->role === 'admin' ? 'Admin' : 'Mahasiswa' }})
                        </span>
                        
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.85rem;">
                                <i data-feather="log-out" style="width: 14px; height: 14px;"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="nav-links">
                    <a href="{{ route('login') }}" class="btn btn-secondary">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Daftar</a>
                </div>
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <main style="flex: 1; padding: 40px 0;">
        <div class="container">
            <!-- Session Messages -->
            @if(session('success'))
                <div class="alert alert-success no-print">
                    <i data-feather="check-circle" style="width: 20px; height: 20px;"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error no-print">
                    <i data-feather="alert-circle" style="width: 20px; height: 20px;"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer no-print">
        <div class="container">
            <p>&copy; 2026 AI-Carir. Dibuat dengan dedikasi untuk masa depan karir mahasiswa.</p>
        </div>
    </footer>

    <!-- Initialize feather icons -->
    <script>
        feather.replace();
    </script>
    @yield('scripts')
</body>
</html>
