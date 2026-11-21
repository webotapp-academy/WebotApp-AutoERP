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
        
        /* Global High-Density Typography Scale */
        html { 
            font-size: 13.5px; 
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.825rem;
            line-height: 1.4;
        }

        /* Compact ERP Density Styles */
        html.compact-erp, body.compact-erp {
            font-size: 12px !important;
        }
        .compact-erp main { padding: 0.5rem 0.75rem !important; }
        .compact-erp .space-y-6 > * + * { margin-top: 0.75rem !important; }
        .compact-erp .space-y-4 > * + * { margin-top: 0.55rem !important; }
        .compact-erp .space-y-3 > * + * { margin-top: 0.45rem !important; }
        .compact-erp table td { padding: 0.35rem 0.5rem !important; font-size: 0.75rem !important; }
        .compact-erp table th { padding: 0.35rem 0.5rem !important; font-size: 0.65rem !important; }
        .compact-erp .kpi-card { padding: 0.6rem 0.75rem !important; }
        .compact-erp .p-6 { padding: 0.75rem !important; }
        .compact-erp .p-5 { padding: 0.65rem !important; }
        .compact-erp .p-4 { padding: 0.55rem !important; }
        .compact-erp .gap-6 { gap: 0.65rem !important; }
        .compact-erp .gap-4 { gap: 0.5rem !important; }
        .compact-erp h1 { font-size: 1.2rem !important; }
        .compact-erp h2 { font-size: 1.05rem !important; }
        .compact-erp h3 { font-size: 0.875rem !important; }
        .compact-erp h4 { font-size: 0.8rem !important; }
        .compact-erp button, 
        .compact-erp input, 
        .compact-erp select, 
        .compact-erp textarea {
            font-size: 0.75rem !important;
        }
        .compact-erp aside nav a {
            padding-top: 0.35rem !important;
            padding-bottom: 0.35rem !important;
            font-size: 0.75rem !important;
        }
    </style>
</head>
<body class="h-full flex overflow-hidden" 
      x-data="{ 
          sidebarOpen: false, 
          sidebarCollapsed: (localStorage.getItem('sidebar_collapsed') === 'true'),
          compactMode: (localStorage.getItem('compact_mode') === null ? true : localStorage.getItem('compact_mode') === 'true'),
          quickActionOpen: false 
      }"
      x-init="
          $watch('compactMode', val => { 
              localStorage.setItem('compact_mode', val); 
              document.documentElement.classList.toggle('compact-erp', val); 
          });
          if (compactMode) document.documentElement.classList.add('compact-erp');
      "
      :class="{ 'compact-erp': compactMode }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside :class="[
        sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        sidebarCollapsed ? 'lg:w-20' : 'lg:w-64'
    ]" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 flex flex-col transition-all duration-200 ease-in-out lg:static lg:translate-x-0 shrink-0">
        <!-- Brand Header -->
        <div class="h-14 flex items-center justify-between px-4 bg-slate-950/70 border-b border-slate-800">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden">
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 shrink-0">
                    <i data-lucide="wrench" class="w-4 h-4"></i>
                </div>
                <div :class="sidebarCollapsed ? 'lg:hidden' : ''" class="truncate">
                    <h1 class="text-sm font-bold text-white tracking-tight leading-none truncate">WebotApp AutoERP</h1>
                    <span class="text-[10px] text-blue-400 font-medium">Garage Management</span>
                </div>
            </a>
            <div class="flex items-center gap-1">
                <button @click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebar_collapsed', sidebarCollapsed)" 
                        title="Toggle Sidebar Density"
                        class="hidden lg:flex p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition">
                    <i data-lucide="chevrons-left" class="w-4 h-4 transition-transform duration-200" :class="sidebarCollapsed ? 'rotate-180' : ''"></i>
                </button>
                <button @click="sidebarOpen = false" class="lg:hidden p-1 text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <!-- Branch Selector -->
        <div class="px-3.5 py-2.5 bg-slate-800/40 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2 overflow-hidden">
                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-400 shrink-0"></i>
                <div :class="sidebarCollapsed ? 'lg:hidden' : ''" class="truncate">
                    <div class="text-[9px] uppercase font-semibold text-slate-400 tracking-wider">Active Branch</div>
                    <div class="text-xs font-medium text-slate-200 truncate">{{ auth()->user()->branch->name ?? 'Downtown Central' }}</div>
                </div>
            </div>
            <span :class="sidebarCollapsed ? 'lg:hidden' : ''" class="px-1.5 py-0.5 text-[9px] bg-emerald-500/20 text-emerald-400 font-semibold rounded-full">Open</span>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6 text-sm">
            @php
                $user = auth()->user();
                $isMgmt = $user ? $user->isManager() : true;
                $isAdvisor = $user ? $user->isAdvisor() : false;
                $isMechanic = $user ? $user->isMechanic() : false;
                $isInventory = $user ? $user->isInventory() : false;
                $isAccountant = $user ? $user->isAccountant() : false;
            @endphp

            <!-- Core Overview -->
            <div>
                <div :class="sidebarCollapsed ? 'lg:hidden' : ''" class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Overview</div>
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}" :title="sidebarCollapsed ? 'Dashboard' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">
                            @if($isMechanic) Mechanic Workbench
                            @elseif($isInventory) Inventory Hub
                            @elseif($isAccountant) Financial Dashboard
                            @elseif($isAdvisor) Advisor Dashboard
                            @else Executive Dashboard
                            @endif
                        </span>
                    </a>
                    @if($isMgmt || $isAdvisor || $isMechanic)
                    <a href="{{ route('workshop.board') }}" :title="sidebarCollapsed ? 'Workshop Floor Board' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('workshop.*') ? 'bg-blue-600 text-white font-medium shadow-sm' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="kanban" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Workshop Floor Board</span>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''" class="ml-auto px-1.5 py-0.5 text-[10px] bg-amber-500/20 text-amber-300 rounded font-bold">Live</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Front Desk & Job Operations -->
            @if($isMgmt || $isAdvisor || $isMechanic || $isAccountant)
            <div>
                <div :class="sidebarCollapsed ? 'lg:hidden' : ''" class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Service Operations</div>
                <div class="space-y-1">
                    @if($isMgmt || $isAdvisor)
                    <a href="{{ route('appointments.index') }}" :title="sidebarCollapsed ? 'Appointments' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('appointments.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="calendar" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Appointments</span>
                    </a>
                    @endif

                    @if($isMgmt || $isAdvisor || $isAccountant)
                    <a href="{{ route('customers.index') }}" :title="sidebarCollapsed ? 'Customers' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('customers.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="users" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Customers</span>
                    </a>
                    @endif

                    @if($isMgmt || $isAdvisor)
                    <a href="{{ route('vehicles.index') }}" :title="sidebarCollapsed ? 'Vehicles' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('vehicles.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="car" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Vehicles</span>
                    </a>
                    @endif

                    @if($isMgmt || $isAdvisor || $isMechanic)
                    <a href="{{ route('inspections.index') }}" :title="sidebarCollapsed ? 'Vehicle Inspections' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('inspections.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="clipboard-check" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Vehicle Inspections</span>
                    </a>
                    @endif

                    @if($isMgmt || $isAdvisor || $isMechanic)
                    <a href="{{ route('job-cards.index') }}" :title="sidebarCollapsed ? 'Job Cards' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('job-cards.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="file-text" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">{{ $isMechanic ? 'My Assigned Jobs' : 'Job Cards' }}</span>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''" class="ml-auto px-2 py-0.5 text-xs bg-slate-800 text-slate-300 rounded-full font-mono">{{ \App\Models\JobCard::whereNotIn('status', ['delivered', 'cancelled'])->count() }}</span>
                    </a>
                    @endif

                    @if($isMgmt || $isAdvisor)
                    <a href="{{ route('estimates.index') }}" :title="sidebarCollapsed ? 'Repair Estimates' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('estimates.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="calculator" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Repair Estimates</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- Floor & Technicians -->
            @if($isMgmt || $isMechanic || $isAdvisor)
            <div>
                <div :class="sidebarCollapsed ? 'lg:hidden' : ''" class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Workshop & Technicians</div>
                <div class="space-y-1">
                    @if($isMgmt)
                    <a href="{{ route('mechanics.index') }}" :title="sidebarCollapsed ? 'Mechanics & Techs' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('mechanics.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="user-cog" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Mechanics & Techs</span>
                    </a>
                    @endif

                    <a href="{{ route('services.index') }}" :title="sidebarCollapsed ? 'Service Catalog' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('services.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="settings-2" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Service Catalog</span>
                    </a>

                    @if($isMgmt || $isAdvisor)
                    <a href="{{ route('packages.index') }}" :title="sidebarCollapsed ? 'Service Packages' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('packages.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="package" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Service Packages</span>
                    </a>
                    @endif

                    <a href="{{ route('quality.index') }}" :title="sidebarCollapsed ? 'Quality Control (QC)' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('quality.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="shield-check" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Quality Control (QC)</span>
                    </a>
                </div>
            </div>
            @endif

            <!-- Spare Parts & Inventory -->
            @if($isMgmt || $isInventory || $isMechanic)
            <div>
                <div :class="sidebarCollapsed ? 'lg:hidden' : ''" class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Spare Parts & Inventory</div>
                <div class="space-y-1">
                    <a href="{{ route('inventory.index') }}" :title="sidebarCollapsed ? 'Spare Parts Master' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('inventory.index') || request()->routeIs('inventory.edit') || request()->routeIs('inventory.create') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="boxes" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Spare Parts Master</span>
                    </a>

                    @if($isMgmt || $isInventory)
                    <a href="{{ route('inventory.ledger') }}" :title="sidebarCollapsed ? 'Stock Movement Ledger' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('inventory.ledger') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="history" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Stock Movement Ledger</span>
                    </a>
                    <a href="{{ route('purchases.index') }}" :title="sidebarCollapsed ? 'Purchase Orders' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('purchases.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="shopping-cart" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Purchase Orders</span>
                    </a>
                    <a href="{{ route('suppliers.index') }}" :title="sidebarCollapsed ? 'Suppliers & Vendors' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('suppliers.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="truck" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Suppliers & Vendors</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- Billing, Claims & Finance -->
            @if($isMgmt || $isAccountant || $isAdvisor)
            <div>
                <div :class="sidebarCollapsed ? 'lg:hidden' : ''" class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Billing & Finance</div>
                <div class="space-y-1">
                    <a href="{{ route('invoices.index') }}" :title="sidebarCollapsed ? 'GST Invoices' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('invoices.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="receipt" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">GST Invoices</span>
                    </a>
                    <a href="{{ route('payments.index') }}" :title="sidebarCollapsed ? 'Payments Received' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('payments.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="credit-card" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Payments Received</span>
                    </a>
                    @if($isMgmt || $isAccountant)
                    <a href="{{ route('insurance.index') }}" :title="sidebarCollapsed ? 'Insurance Claims' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('insurance.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="shield" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Insurance Claims</span>
                    </a>
                    <a href="{{ route('expenses.index') }}" :title="sidebarCollapsed ? 'Expenses' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('expenses.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="wallet" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Expenses</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- CRM & Analytics -->
            @if($isMgmt || $isAdvisor || $isAccountant)
            <div>
                <div :class="sidebarCollapsed ? 'lg:hidden' : ''" class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">CRM & Analytics</div>
                <div class="space-y-1">
                    @if($isMgmt || $isAdvisor)
                    <a href="{{ route('crm.reminders') }}" :title="sidebarCollapsed ? 'Service Reminders' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('crm.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="bell-ring" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Service Reminders</span>
                    </a>
                    @endif

                    @if($isMgmt || $isAccountant)
                    <a href="{{ route('reports.index') }}" :title="sidebarCollapsed ? 'Reports & GST Analytics' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="bar-chart-3" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Reports & GST Analytics</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- Admin & Settings -->
            @if($isMgmt)
            <div>
                <div :class="sidebarCollapsed ? 'lg:hidden' : ''" class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">System</div>
                <div class="space-y-1">
                    @if($user && $user->isAdmin())
                    <a href="{{ route('users.index') }}" :title="sidebarCollapsed ? 'Users & RBAC Roles' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="shield-alert" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Users & RBAC Roles</span>
                    </a>
                    @endif
                    <a href="{{ route('settings.index') }}" :title="sidebarCollapsed ? 'Workshop Settings' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('settings.*') ? 'bg-blue-600 text-white font-medium' : 'hover:bg-slate-800/70 text-slate-300' }}">
                        <i data-lucide="sliders" class="w-4 h-4 shrink-0"></i>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Workshop Settings</span>
                    </a>
                </div>
            </div>
            @endif
        </nav>

        <!-- Current User Profile Footer -->
        <div class="p-3 bg-slate-950/80 border-t border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="w-8 h-8 rounded-full bg-blue-600/30 text-blue-400 font-bold flex items-center justify-center shrink-0 border border-blue-500/30 text-xs">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div :class="sidebarCollapsed ? 'lg:hidden' : ''" class="truncate text-xs">
                    <div class="font-semibold text-white truncate">{{ auth()->user()->name ?? 'Demo User' }}</div>
                    <div class="text-[10px] text-blue-400 capitalize">{{ auth()->user()->role->name ?? 'Administrator' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" :class="sidebarCollapsed ? 'lg:hidden' : ''">
                @csrf
                <button type="submit" title="Logout" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-lg hover:bg-slate-800/60 transition">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top Navigation Bar -->
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-3 sm:px-5 z-20 shadow-sm shrink-0">
            <!-- Mobile Menu Toggle, Sidebar Collapse & Title -->
            <div class="flex items-center gap-2 sm:gap-3">
                <button @click="sidebarOpen = true" class="lg:hidden p-1.5 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <button @click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebar_collapsed', sidebarCollapsed)" 
                        title="Toggle Sidebar Collapse (Expand/Compact)" 
                        class="hidden lg:flex p-1.5 text-slate-500 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition">
                    <i data-lucide="panel-left-close" class="w-4 h-4 transition-transform duration-200" :class="sidebarCollapsed ? 'rotate-180 text-blue-600' : ''"></i>
                </button>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold text-slate-900">@yield('page_title', 'Workshop ERP')</h2>
                </div>
            </div>

            <!-- Global Search & Quick Actions -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Compact Density Mode Toggle Button -->
                <button @click="compactMode = !compactMode; localStorage.setItem('compact_mode', compactMode)" 
                        :title="compactMode ? 'Click for Standard Spacing' : 'Click for Dense Compact ERP View'" 
                        :class="compactMode ? 'bg-indigo-50 border-indigo-300 text-indigo-700 shadow-sm' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-lg border transition">
                    <i data-lucide="minimize-2" class="w-3.5 h-3.5" :class="compactMode ? 'text-indigo-600' : 'text-slate-500'"></i>
                    <span x-text="compactMode ? 'Compact' : 'Normal'" class="hidden sm:inline"></span>
                </button>

                <!-- Search bar -->
                <form action="{{ route('vehicles.index') }}" method="GET" class="hidden md:flex items-center relative">
                    <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3"></i>
                    <input type="text" name="search" placeholder="Search Reg, Customer, Phone..." class="pl-8 pr-3 py-1 text-xs bg-slate-100 border border-slate-200 rounded-lg w-56 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition" />
                </form>

                <!-- Quick Actions Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false" class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Quick Action</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                    </button>
                    <div x-show="open" x-cloak class="absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-xl shadow-xl py-2 z-50">
                        @if($isMechanic)
                            <a href="{{ route('workshop.board') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 font-medium">
                                <i data-lucide="play-circle" class="w-4 h-4 text-emerald-600"></i> Workshop Floor Board
                            </a>
                            <a href="{{ route('quality.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 font-medium">
                                <i data-lucide="shield-check" class="w-4 h-4 text-blue-600"></i> New Quality Check
                            </a>
                            <a href="{{ route('inventory.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 font-medium">
                                <i data-lucide="boxes" class="w-4 h-4 text-amber-600"></i> Request Spare Part
                            </a>
                        @elseif($isInventory)
                            <a href="{{ route('inventory.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 font-medium">
                                <i data-lucide="package-plus" class="w-4 h-4 text-blue-600"></i> Add Spare Part
                            </a>
                            <a href="{{ route('purchases.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 font-medium">
                                <i data-lucide="shopping-cart" class="w-4 h-4 text-emerald-600"></i> New Purchase Order
                            </a>
                            <a href="{{ route('suppliers.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 font-medium">
                                <i data-lucide="truck" class="w-4 h-4 text-purple-600"></i> Register Supplier
                            </a>
                            <a href="{{ route('inventory.ledger') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 font-medium">
                                <i data-lucide="history" class="w-4 h-4 text-indigo-600"></i> Stock Audit Ledger
                            </a>
                        @elseif($isAccountant)
                            <a href="{{ route('invoices.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 font-medium">
                                <i data-lucide="receipt" class="w-4 h-4 text-blue-600"></i> Generate GST Invoice
                            </a>
                            <a href="{{ route('payments.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 font-medium">
                                <i data-lucide="credit-card" class="w-4 h-4 text-emerald-600"></i> Collect Payment
                            </a>
                            <a href="{{ route('expenses.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 font-medium">
                                <i data-lucide="wallet" class="w-4 h-4 text-rose-600"></i> Record Expense
                            </a>
                            <a href="{{ route('reports.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 font-medium">
                                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-amber-600"></i> GSTR-1 Tax Report
                            </a>
                        @else
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
                        @endif
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
        <main :class="compactMode ? 'p-3 sm:p-4' : 'p-4 sm:p-6 lg:p-7'" class="flex-1 overflow-y-auto bg-slate-50 transition-all duration-150">
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

