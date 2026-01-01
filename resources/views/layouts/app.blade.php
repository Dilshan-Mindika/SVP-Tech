<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SVP Tech')</title>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        @auth
        <aside class="sidebar">
            <div class="brand" style="flex-direction: column; height: auto; padding: 2rem 1rem; gap: 1rem;">
                <img src="{{ asset('images/logo.png') }}" alt="SVP Tech" style="height: 100px; width: auto; border-radius: 50%;">
                <h1 style="font-size: 1.2rem; font-weight: 700; color: #fff; text-align: center;">SVP Technologies</h1>
            </div>
            <nav class="nav-links">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line" style="margin-right: 10px; width: 20px;"></i> Dashboard
                </a>
                
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('technicians.index') }}" class="{{ request()->routeIs('technicians.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog" style="margin-right: 10px; width: 20px;"></i> Technicians
                    </a>
                @endif
                
                <a href="{{ route('repair-jobs.index') }}" class="{{ request()->routeIs('repair-jobs.*') ? 'active' : '' }}">
                    <i class="fas fa-tools" style="margin-right: 10px; width: 20px;"></i> Repairs
                </a>
                <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="fas fa-user-friends" style="margin-right: 10px; width: 20px;"></i> Customers
                </a>
                <a href="{{ route('inventory.index') }}" class="{{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                    <i class="fas fa-boxes" style="margin-right: 10px; width: 20px;"></i> Inventory
                </a>
            </nav>
            <div class="user-panel">
                <span class="user-name"><i class="fas fa-user-circle"></i> {{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </aside>
        @endauth

        <!-- Main Content -->
        <main class="main-content">
            <!-- Toast Container -->
            <div class="toast-container" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.5rem;">
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.duration.300ms class="alert alert-success toast">
                        {{ session('success') }}
                    </div>
                @endif
    
                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.duration.300ms class="alert alert-error toast">
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            @if ($errors->any())
                <div class="alert alert-error">
                    <strong>Please check the form for errors:</strong>
                    <ul style="margin-top: 0.5rem; padding-left: 1.5rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
