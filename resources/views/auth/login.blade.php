<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Automobile Garage Management ERP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="h-full flex items-center justify-center p-4">
    <div class="w-full max-w-xl">
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-blue-600 items-center justify-center text-white shadow-xl shadow-blue-500/30 mb-4">
                <i data-lucide="wrench" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">ABC Automobile Care & Service Centre</h1>
            <p class="text-sm text-slate-400 mt-1">Enterprise Automobile Workshop Management ERP</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
            <!-- Normal Login Form -->
            <div class="p-8">
                @if(session('info'))
                    <div class="mb-4 p-3 bg-blue-50 text-blue-700 text-xs rounded-lg font-medium border border-blue-200">
                        {{ session('info') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-3 bg-rose-50 text-rose-700 text-xs rounded-lg font-medium border border-rose-200">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Email Address</label>
                        <input type="email" name="email" required value="{{ old('email', 'admin@automobile.com') }}" class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:outline-none transition" />
                        @error('email')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Password</label>
                            <span class="text-xs text-slate-400">Default: password</span>
                        </div>
                        <input type="password" name="password" required value="password" class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:outline-none transition" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center text-xs text-slate-600">
                            <input type="checkbox" name="remember" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 mr-2"> Remember session
                        </label>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg shadow-md transition flex items-center justify-center gap-2">
                        <i data-lucide="log-in" class="w-4 h-4"></i> Sign In to ERP
                    </button>
                </form>
            </div>

            <!-- Quick Demo Role Logins as requested in Section 35 -->
            <div class="bg-slate-50 border-t border-slate-200 p-6">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 text-center">
                    ⚡ Quick Demo One-Click Access (Section 35)
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    <a href="{{ route('quick-login', 'admin@automobile.com') }}" class="p-2.5 bg-white border border-slate-200 hover:border-blue-500 hover:bg-blue-50/50 rounded-xl text-center transition group">
                        <div class="text-xs font-bold text-slate-800 group-hover:text-blue-600">Administrator</div>
                        <div class="text-[10px] text-slate-500">Super Admin</div>
                    </a>
                    <a href="{{ route('quick-login', 'advisor@automobile.com') }}" class="p-2.5 bg-white border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/50 rounded-xl text-center transition group">
                        <div class="text-xs font-bold text-slate-800 group-hover:text-indigo-600">Service Advisor</div>
                        <div class="text-[10px] text-slate-500">Check-in & Estimates</div>
                    </a>
                    <a href="{{ route('quick-login', 'mechanic@automobile.com') }}" class="p-2.5 bg-white border border-slate-200 hover:border-amber-500 hover:bg-amber-50/50 rounded-xl text-center transition group">
                        <div class="text-xs font-bold text-slate-800 group-hover:text-amber-600">Lead Mechanic</div>
                        <div class="text-[10px] text-slate-500">Tasks & Time Tracking</div>
                    </a>
                    <a href="{{ route('quick-login', 'inventory@automobile.com') }}" class="p-2.5 bg-white border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/50 rounded-xl text-center transition group">
                        <div class="text-xs font-bold text-slate-800 group-hover:text-emerald-600">Inventory Mgr</div>
                        <div class="text-[10px] text-slate-500">Spare Parts & Stock</div>
                    </a>
                    <a href="{{ route('quick-login', 'accountant@automobile.com') }}" class="p-2.5 bg-white border border-slate-200 hover:border-purple-500 hover:bg-purple-50/50 rounded-xl text-center transition group">
                        <div class="text-xs font-bold text-slate-800 group-hover:text-purple-600">Accountant</div>
                        <div class="text-[10px] text-slate-500">Invoices & GST</div>
                    </a>
                    <a href="{{ route('quick-login', 'manager@automobile.com') }}" class="p-2.5 bg-white border border-slate-200 hover:border-cyan-500 hover:bg-cyan-50/50 rounded-xl text-center transition group">
                        <div class="text-xs font-bold text-slate-800 group-hover:text-cyan-600">Manager</div>
                        <div class="text-[10px] text-slate-500">Floor & Approvals</div>
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center mt-6 text-xs text-slate-500">
            Automobile Garage Management ERP • Indian GST Compliant • Version 2.0
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>

