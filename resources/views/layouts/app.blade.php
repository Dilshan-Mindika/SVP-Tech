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
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        @auth
        <aside class="sidebar">
            <div class="brand">
                <h1>SVP Tech</h1>
            </div>
            <nav class="nav-links">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('technicians.index') }}" class="{{ request()->routeIs('technicians.*') ? 'active' : '' }}">Technicians</a>
                @endif
                
                <a href="{{ route('repair-jobs.index') }}" class="{{ request()->routeIs('repair-jobs.*') ? 'active' : '' }}">Repairs</a>
                <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">Customers</a>
                <a href="{{ route('inventory.index') }}" class="{{ request()->routeIs('inventory.*') ? 'active' : '' }}">Inventory</a>
            </nav>
            <div class="user-panel">
                <span>{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </aside>
        @endauth

        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>
</body>
</html>
