<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Automobile ERP') }} - @yield('title', 'Garage Management')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full flex overflow-hidden" x-data="{ sidebarOpen: false, quickActionOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-300 flex flex-col transition-transform duration-200 ease-in-out lg:static lg:translate-x-0">
        <!-- Brand Header -->
        <div class="h-16 flex items-center justify-between px-6 bg-slate-950/60 border-b border-slate-800">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                    <i data-lucide="wrench" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-base font-bold text-white tracking-tight leading-none">AUTO CARE ERP</h1>
                    <span class="text-xs text-blue-400 font-medium">Garage Management</span>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden p-1 text-slate-400 hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Branch Selector -->
        <div class="px-4 py-3 bg-slate-800/40 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2 overflow-hidden">
                <i data-lucide="map-pin" class="w-4 h-4 text-emerald-400 shrink-0"></i>
                <div class="truncate">
                    <div class="text-[10px] uppercase font-semibold text-slate-400 tracking-wider">Active Branch</div>
                    <div class="text-xs font-medium text-slate-200 truncate">{{ auth()->user()->branch->name ?? 'Downtown Central' }}</div>
                </div>
            </div>
            <span class="px-2 py-0.5 text-[10px] bg-emerald-500/20 text-emerald-400 font-semibold rounded-full">Open</span>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6 text-sm">
            <!-- Core -->
            <div>
                <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Overview</div>
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('workshop.board') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('workshop.*') ? 'bg-blue-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="kanban" class="w-4 h-4"></i>
                        <span>Workshop Floor Board</span>
                        <span class="ml-auto px-1.5 py-0.5 text-[10px] bg-amber-500/20 text-amber-300 rounded font-bold">Live</span>
                    </a>
                </div>
            </div>

            <!-- Front Desk & Job Operations -->
            <div>
                <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Service Operations</div>
                <div class="space-y-1">
                    <a href="{{ route('appointments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('appointments.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                        <span>Appointments</span>
                    </a>
                    <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('customers.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        <span>Customers</span>
                    </a>
                    <a href="{{ route('vehicles.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('vehicles.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="car" class="w-4 h-4"></i>
                        <span>Vehicles</span>
                    </a>
                    <a href="{{ route('inspections.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('inspections.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                        <span>Vehicle Inspections</span>
                    </a>
                    <a href="{{ route('job-cards.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('job-cards.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        <span>Job Cards</span>
                        <span class="ml-auto px-2 py-0.5 text-xs bg-slate-800 text-slate-300 rounded-full font-mono">{{ \App\Models\JobCard::whereNotIn('status', ['delivered', 'cancelled'])->count() }}</span>
                    </a>
                    <a href="{{ route('estimates.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('estimates.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="calculator" class="w-4 h-4"></i>
                        <span>Repair Estimates</span>
                    </a>
                </div>
            </div>

            <!-- Floor & Technicians -->
            <div>
                <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Workshop & Technicians</div>
                <div class="space-y-1">
                    <a href="{{ route('mechanics.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('mechanics.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="user-cog" class="w-4 h-4"></i>
                        <span>Mechanics & Techs</span>
                    </a>
                    <a href="{{ route('services.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('services.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="settings-2" class="w-4 h-4"></i>
                        <span>Service Catalog</span>
                    </a>
                    <a href="{{ route('packages.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('packages.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="package" class="w-4 h-4"></i>
                        <span>Service Packages</span>
                    </a>
                    <a href="{{ route('quality.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('quality.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        <span>Quality Control (QC)</span>
                    </a>
                </div>
            </div>

            <!-- Spare Parts & Inventory -->
            <div>
                <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Spare Parts & Inventory</div>
                <div class="space-y-1">
                    <a href="{{ route('inventory.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('inventory.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="boxes" class="w-4 h-4"></i>
                        <span>Spare Parts Master</span>
                    </a>
                    <a href="{{ route('purchases.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('purchases.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                        <span>Purchase Orders</span>
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('suppliers.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="truck" class="w-4 h-4"></i>
                        <span>Suppliers & Vendors</span>
                    </a>
                </div>
            </div>

            <!-- Billing, Claims & Finance -->
            <div>
                <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Billing & Finance</div>
                <div class="space-y-1">
                    <a href="{{ route('invoices.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('invoices.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="receipt" class="w-4 h-4"></i>
                        <span>GST Invoices</span>
                    </a>
                    <a href="{{ route('payments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('payments.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                        <span>Payments Received</span>
                    </a>
                    <a href="{{ route('insurance.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('insurance.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="shield" class="w-4 h-4"></i>
                        <span>Insurance Claims</span>
                    </a>
                    <a href="{{ route('expenses.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('expenses.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="wallet" class="w-4 h-4"></i>
                        <span>Expenses</span>
                    </a>
                </div>
            </div>

            <!-- CRM & Analytics -->
            <div>
                <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">CRM & Analytics</div>
                <div class="space-y-1">
                    <a href="{{ route('crm.reminders') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('crm.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="bell-ring" class="w-4 h-4"></i>
                        <span>Service Reminders</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                        <span>Reports & GST Analytics</span>
                    </a>
                </div>
            </div>

            <!-- Admin & Settings -->
            <div>
                <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">System</div>
                <div class="space-y-1">
                    <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="shield-alert" class="w-4 h-4"></i>
                        <span>Users & RBAC Roles</span>
                    </a>
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('settings.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="sliders" class="w-4 h-4"></i>
                        <span>Workshop Settings</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Current User Profile Footer -->
        <div class="p-3 bg-slate-950/80 border-t border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="w-9 h-9 rounded-full bg-blue-600/30 text-blue-400 font-bold flex items-center justify-center shrink-0 border border-blue-500/30">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="truncate text-xs">
                    <div class="font-semibold text-white truncate">{{ auth()->user()->name ?? 'Demo User' }}</div>
                    <div class="text-[11px] text-blue-400 capitalize">{{ auth()->user()->role->name ?? 'Administrator' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-400 rounded-lg hover:bg-slate-800/60 transition">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top Navigation Bar -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-20 shadow-sm shrink-0">
            <!-- Mobile Menu Toggle & Title -->
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-slate-900">@yield('page_title', 'Workshop ERP')</h2>
                </div>
            </div>

            <!-- Global Search & Quick Actions -->
            <div class="flex items-center gap-3">
                <!-- Search bar -->
                <form action="{{ route('vehicles.index') }}" method="GET" class="hidden md:flex items-center relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3"></i>
                    <input type="text" name="search" placeholder="Search Reg No, Customer, Phone..." class="pl-9 pr-4 py-1.5 text-xs bg-slate-100 border border-slate-200 rounded-lg w-64 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition" />
                </form>

                <!-- Quick Actions Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false" class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Quick Action</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                    </button>
                    <div x-show="open" x-cloak class="absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-xl shadow-xl py-2 z-50">
                        <a href="{{ route('job-cards.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 font-medium">
                            <i data-lucide="file-plus" class="w-4 h-4 text-blue-600"></i> Create Job Card
                        </a>
                        <a href="{{ route('appointments.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 font-medium">
                            <i data-lucide="calendar-plus" class="w-4 h-4 text-indigo-600"></i> Book Appointment
                        </a>
                        <a href="{{ route('customers.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 font-medium">
                            <i data-lucide="user-plus" class="w-4 h-4 text-emerald-600"></i> Register Customer
                        </a>
                        <a href="{{ route('vehicles.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 font-medium">
                            <i data-lucide="car" class="w-4 h-4 text-purple-600"></i> Add Vehicle
                        </a>
                        <div class="border-t border-slate-100 my-1"></div>
                        <a href="{{ route('estimates.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 font-medium">
                            <i data-lucide="calculator" class="w-4 h-4 text-amber-600"></i> New Estimate
                        </a>
                        <a href="{{ route('invoices.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 font-medium">
                            <i data-lucide="receipt" class="w-4 h-4 text-teal-600"></i> Create Invoice
                        </a>
                    </div>
                </div>

                <!-- Notifications indicator -->
                <a href="{{ route('crm.reminders') }}" title="Due Service Reminders" class="p-2 text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded-lg relative transition">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-emerald-50 border-b border-emerald-200 px-6 py-2.5 flex items-center justify-between text-xs text-emerald-800">
                <div class="flex items-center gap-2 font-medium">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border-b border-rose-200 px-6 py-2.5 flex items-center justify-between text-xs text-rose-800">
                <div class="flex items-center gap-2 font-medium">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Main Page Body Container -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50">
            @yield('content')
        </main>
    </div>

    @livewireScripts
    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
