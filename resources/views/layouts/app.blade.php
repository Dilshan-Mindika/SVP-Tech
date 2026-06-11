<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Cloud Tech')</title>
    
    <!-- Tailwind Compiled CSS -->
    @vite(['resources/css/app.css'])

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
</head>
<body>
    <div class="app-container" x-data="{ sidebarOpen: false }">
        <!-- Mobile Header & Toggle -->
        <div class="mobile-header">
            <button @click="sidebarOpen = !sidebarOpen" class="mobile-toggle-btn">
                <i class="fas fa-bars"></i>
            </button>
            <div class="mobile-brand">Cloud Tech</div>
        </div>

        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity></div>

        <!-- Sidebar -->
        @auth
        <aside class="sidebar" :class="{ 'open': sidebarOpen }">
            <button class="mobile-close-btn" @click="sidebarOpen = false">
                <i class="fas fa-times"></i>
            </button>
            <div class="brand" style="flex-direction: column; height: auto; padding: 2rem 1rem; gap: 1rem;">
                <img src="https://i.ibb.co/JRyxmV6R/Cloud-Logo.png" alt="Cloud Tech" style="height: 100px; width: auto;">
                <h1 style="font-size: 1.2rem; font-weight: 700; text-align: center;">Cloud Tech</h1>
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
                <a href="{{ route('invoices.index') }}" class="{{ request()->routeIs('invoices.*') || request()->routeIs('sales.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar" style="margin-right: 10px; width: 20px;"></i> Invoices
                </a>
                <a href="{{ route('reports.outstanding') }}" class="{{ request()->routeIs('reports.outstanding') ? 'active' : '' }}">
                    <i class="fas fa-hand-holding-usd" style="margin-right: 10px; width: 20px;"></i> Payments
                </a>

                <span style="padding: 0.5rem 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); opacity: 0.6; font-weight: 700; margin-top: 1rem; display: block;">Sales & Operations</span>
                
                <a href="{{ route('business_dashboard') }}" class="{{ request()->routeIs('business_dashboard') ? 'active' : '' }}">
                    <i class="fas fa-terminal" style="margin-right: 10px; width: 20px;"></i> Business Console
                </a>
                
                @if(auth()->user()->hasPermission('read-products'))
                    <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i class="fas fa-cubes" style="margin-right: 10px; width: 20px;"></i> Products List
                    </a>
                @endif
                
                @if(auth()->user()->hasPermission('read-categories'))
                    <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="fas fa-tags" style="margin-right: 10px; width: 20px;"></i> Categories
                    </a>
                @endif

                @if(auth()->user()->hasPermission('create-invoices') || auth()->user()->hasPermission('read-invoices'))
                    <a href="{{ route('sales_invoices.index') }}" class="{{ request()->routeIs('sales_invoices.*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice" style="margin-right: 10px; width: 20px;"></i> Sales Invoices
                    </a>
                @endif

                @if(auth()->user()->hasPermission('read-quotations'))
                    <a href="{{ route('quotations.index') }}" class="{{ request()->routeIs('quotations.*') ? 'active' : '' }}">
                        <i class="fas fa-file-contract" style="margin-right: 10px; width: 20px;"></i> Quotations
                    </a>
                @endif

                @if(auth()->user()->hasPermission('read-customers'))
                    <a href="{{ route('customer_directory.index') }}" class="{{ request()->routeIs('customer_directory.*') ? 'active' : '' }}">
                        <i class="fas fa-address-book" style="margin-right: 10px; width: 20px;"></i> Customer Directory
                    </a>
                @endif

                @if(auth()->user()->hasPermission('read-grn'))
                    <a href="{{ route('grn.index') }}" class="{{ request()->routeIs('grn.*') ? 'active' : '' }}">
                        <i class="fas fa-truck-loading" style="margin-right: 10px; width: 20px;"></i> Goods Received (GRN)
                    </a>
                @endif

                @if(auth()->user()->hasPermission('read-suppliers'))
                    <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                        <i class="fas fa-handshake" style="margin-right: 10px; width: 20px;"></i> Suppliers
                    </a>
                @endif

                @if(auth()->user()->hasPermission('read-repairs'))
                    <a href="{{ route('service_repairs.index') }}" class="{{ request()->routeIs('service_repairs.*') ? 'active' : '' }}">
                        <i class="fas fa-wrench" style="margin-right: 10px; width: 20px;"></i> Service Repairs
                    </a>
                @endif

                @if(auth()->user()->hasPermission('read-warranty'))
                    <a href="{{ route('warranty.index') }}" class="{{ request()->routeIs('warranty.*') ? 'active' : '' }}">
                        <i class="fas fa-shield-alt" style="margin-right: 10px; width: 20px;"></i> Warranty Claims
                    </a>
                @endif

                @if(auth()->user()->hasPermission('read-appointments'))
                    <a href="{{ route('appointments.index') }}" class="{{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt" style="margin-right: 10px; width: 20px;"></i> Appointments
                    </a>
                @endif

                @if(auth()->user()->hasPermission('read-returns'))
                    <a href="{{ route('returns.index') }}" class="{{ request()->routeIs('returns.*') ? 'active' : '' }}">
                        <i class="fas fa-undo-alt" style="margin-right: 10px; width: 20px;"></i> Returns
                    </a>
                @endif

                @if(auth()->user()->hasPermission('read-employees'))
                    <a href="{{ route('employees.index') }}" class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <i class="fas fa-user-tie" style="margin-right: 10px; width: 20px;"></i> Employees
                    </a>
                @endif

                @if(auth()->user()->hasPermission('read-salaries'))
                    <a href="{{ route('salaries.index') }}" class="{{ request()->routeIs('salaries.*') ? 'active' : '' }}">
                        <i class="fas fa-money-bill-wave" style="margin-right: 10px; width: 20px;"></i> Salaries
                    </a>
                @endif

                @if(auth()->user()->hasPermission('read-expenses'))
                    <a href="{{ route('expenses.index') }}" class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                        <i class="fas fa-receipt" style="margin-right: 10px; width: 20px;"></i> Expenses
                    </a>
                @endif

                @if(auth()->user()->hasPermission('read-expenses'))
                    <a href="{{ route('business_reports.index') }}" class="{{ request()->routeIs('business_reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie" style="margin-right: 10px; width: 20px;"></i> Business Reports
                    </a>
                @endif

                @if(auth()->user()->hasPermission('read-users'))
                    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fas fa-user-cog" style="margin-right: 10px; width: 20px;"></i> User Accounts
                    </a>
                @endif

                @if(auth()->user()->hasPermission('read-roles'))
                    <a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'active' : '' }}">
                        <i class="fas fa-user-shield" style="margin-right: 10px; width: 20px;"></i> Roles & Permissions
                    </a>
                @endif

                @if(auth()->user()->hasPermission('read-bank-accounts'))
                    <a href="{{ route('bank_accounts.index') }}" class="{{ request()->routeIs('bank_accounts.*') ? 'active' : '' }}">
                        <i class="fas fa-university" style="margin-right: 10px; width: 20px;"></i> Bank Accounts
                    </a>
                @endif
            </nav>

            <div class="user-panel">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.05); padding-bottom: 0.8rem;" class="user-info-row">
                    <span class="user-name" style="margin: 0; border: none; padding: 0;">
                        <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
                    </span>
                    <button id="theme-toggle" title="Toggle Theme" class="theme-toggle-btn">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
                
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </aside>
        @endauth

        <script>
            // Theme Toggle Logic
            const themeToggleBtn = document.getElementById('theme-toggle');
            const htmlElement = document.documentElement;
            const icon = themeToggleBtn ? themeToggleBtn.querySelector('i') : null;

            function applyTheme(theme) {
                if (theme === 'light') {
                    htmlElement.setAttribute('data-theme', 'light');
                    if(icon) { icon.classList.remove('fa-moon'); icon.classList.add('fa-sun'); }
                } else {
                    htmlElement.removeAttribute('data-theme');
                    if(icon) { icon.classList.remove('fa-sun'); icon.classList.add('fa-moon'); }
                }
                localStorage.setItem('theme', theme);
            }

            // Init
            const savedTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
            applyTheme(savedTheme);

            if(themeToggleBtn) {
                themeToggleBtn.addEventListener('click', () => {
                    const currentTheme = htmlElement.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
                    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                    applyTheme(newTheme);
                });
            }
        </script>

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
