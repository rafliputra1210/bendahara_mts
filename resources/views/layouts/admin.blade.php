<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Bendahara</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.4); }

        /* Sidebar transition */
        .sidebar-transition { transition: transform 0.3s ease, width 0.3s ease; }
        
        /* Table responsive */
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        
        /* Mobile card layout untuk tabel */
        @media (max-width: 640px) {
            .mobile-stack td { display: block; width: 100%; }
            .mobile-stack td::before { 
                content: attr(data-label); 
                font-weight: 600; 
                color: #6b7280; 
                font-size: 0.65rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                display: block;
                margin-bottom: 2px;
            }
        }
        
        /* Sidebar overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 29;
            backdrop-filter: blur(2px);
            cursor: pointer; /* Fix for iOS Safari click event on div */
        }
        .sidebar-overlay.active { display: block; }
        
        /* Mobile sidebar visibility */
        @media (max-width: 1023px) {
            .mobile-sidebar-hidden { transform: translateX(-100%); }
            .mobile-sidebar-shown { transform: translateX(0); }
        }

        /* Active nav item */
        .nav-active { background-color: rgba(255,255,255,0.15); box-shadow: inset 3px 0 0 #34d399; }
        
        /* Header title responsive */
        @media (max-width: 480px) {
            .header-title { display: none; }
        }
        
        /* Content padding responsive */
        .main-content { 
            padding: 1rem; 
        }
        @media (min-width: 640px) {
            .main-content { padding: 1.5rem; }
        }
        @media (min-width: 1024px) {
            .main-content { padding: 2rem; }
        }

        /* Mobile bottom safe area */
        @supports (padding-bottom: env(safe-area-inset-bottom)) {
            .main-content { padding-bottom: calc(1rem + env(safe-area-inset-bottom)); }
        }

        /* Select2 Tailwind Fixes */
        .select2-container .select2-selection--single {
            height: 42px !important;
            border-color: #e5e7eb !important;
            border-radius: 0.75rem !important;
            display: flex !important;
            align-items: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #374151 !important;
            font-size: 0.875rem !important;
            padding-left: 1rem !important;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #d1d5db !important;
            border-radius: 0.5rem !important;
            padding: 0.5rem 0.75rem !important;
            outline: none !important;
            font-size: 0.875rem !important;
            margin-bottom: 4px;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2) !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #10b981 !important;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    
    <div class="flex h-screen overflow-hidden">
        
        {{-- Overlay Mobile --}}
        <div id="sidebarOverlay" class="sidebar-overlay" onclick="closeSidebar()"></div>

        {{-- SIDEBAR --}}
        <aside id="mainSidebar" 
               class="fixed inset-y-0 left-0 z-30 w-64 bg-emerald-800 text-white flex flex-col shadow-2xl sidebar-transition mobile-sidebar-hidden lg:relative lg:translate-x-0 lg:shadow-none">
            
            {{-- Logo / Brand --}}
            <div class="flex items-center justify-between h-16 bg-emerald-900 px-5 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-emerald-400 rounded-lg flex items-center justify-center shadow-inner">
                        <i class="fas fa-coins text-emerald-900 text-sm"></i>
                    </div>
                    <span class="text-base font-bold uppercase tracking-wider text-white">BendaharaPro</span>
                </div>
                <button type="button" onclick="closeSidebar()" class="text-emerald-300 hover:text-white focus:outline-none lg:hidden p-1.5 rounded-lg hover:bg-emerald-700 transition">
                    <i class="fas fa-times fa-lg"></i>
                </button>
            </div>
            
            {{-- User Info (Mobile only) --}}
            <div class="lg:hidden px-5 py-3 bg-emerald-700/50 border-b border-emerald-700/50 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-full bg-emerald-500 flex items-center justify-center text-white font-bold shadow text-sm shrink-0">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</div>
                        <span class="text-[10px] font-medium text-emerald-300">{{ ucfirst(Auth::user()->role) }}</span>
                    </div>
                </div>
            </div>
            
            {{-- Navigation --}}
            <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto custom-scrollbar">
                
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 transition text-sm font-medium {{ request()->routeIs('dashboard') ? 'nav-active bg-white/10' : 'text-emerald-100' }}">
                    <i class="fas fa-home w-5 text-center {{ request()->routeIs('dashboard') ? 'text-emerald-300' : 'text-emerald-400' }}"></i>
                    <span>Dashboard</span>
                </a>
                
                {{-- Divider --}}
                <div class="pt-3 pb-1.5 px-3">
                    <span class="text-[10px] font-bold text-emerald-400/80 uppercase tracking-widest">Master Data</span>
                </div>
                
                <a href="{{ route('students.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 transition text-sm font-medium {{ request()->routeIs('students.*') ? 'nav-active bg-white/10' : 'text-emerald-100' }}">
                    <i class="fas fa-users w-5 text-center {{ request()->routeIs('students.*') ? 'text-emerald-300' : 'text-emerald-400' }}"></i>
                    <span>Data Siswa</span>
                    
                </a>
                
                {{-- Divider --}}
                <div class="pt-3 pb-1.5 px-3">
                    <span class="text-[10px] font-bold text-emerald-400/80 uppercase tracking-widest">Transaksi</span>
                </div>
                
                <a href="{{ route('incomes.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 transition text-sm font-medium {{ request()->routeIs('incomes.*') ? 'nav-active bg-white/10' : 'text-emerald-100' }}">
                    <i class="fas fa-arrow-down w-5 text-center text-green-400"></i>
                    <span>Kas Masuk</span>
                </a>
                
                <a href="{{ route('expenses.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 transition text-sm font-medium {{ request()->routeIs('expenses.*') ? 'nav-active bg-white/10' : 'text-emerald-100' }}">
                    <i class="fas fa-arrow-up w-5 text-center text-red-400"></i>
                    <span>Kas Keluar</span>
                </a>
                
                <a href="{{ route('incomes.history') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 transition text-sm font-medium {{ request()->routeIs('incomes.history') ? 'nav-active bg-white/10' : 'text-emerald-100' }}">
                    <i class="fas fa-history w-5 text-center text-blue-300"></i>
                    <span>Riwayat Transaksi</span>
                </a>
                
                <a href="{{ route('tunggakan.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 transition text-sm font-medium {{ request()->routeIs('tunggakan.*') ? 'nav-active bg-white/10' : 'text-emerald-100' }}">
                    <i class="fas fa-exclamation-triangle w-5 text-center text-orange-400"></i>
                    <span>Tunggakan Lama</span>
                </a>
                
                {{-- Divider --}}
                <div class="pt-3 pb-1.5 px-3">
                    <span class="text-[10px] font-bold text-emerald-400/80 uppercase tracking-widest">Rekapitulasi</span>
                </div>
                
                <a href="{{ route('reports.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 transition text-sm font-medium {{ request()->routeIs('reports.*') ? 'nav-active bg-white/10' : 'text-emerald-100' }}">
                    <i class="fas fa-file-pdf w-5 text-center text-blue-300"></i>
                    <span>Laporan</span>
                </a>
                
                {{-- Divider --}}
                <div class="pt-3 pb-1.5 px-3">
                    <span class="text-[10px] font-bold text-emerald-400/80 uppercase tracking-widest">Pengaturan</span>
                </div>
                
                <a href="{{ route('academic-years.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 transition text-sm font-medium {{ request()->routeIs('academic-years.*') ? 'nav-active bg-white/10' : 'text-emerald-100' }}">
                    <i class="fas fa-cog w-5 text-center text-gray-300"></i>
                    <span>Tahun Ajaran</span>
                </a>
            </nav>

            {{-- Logout (bottom of sidebar) --}}
            <div class="px-3 py-4 border-t border-emerald-700/50 shrink-0">
                <form method="POST" action="{{ route('logout') }}" id="logout-form-sidebar">
                    @csrf
                    <button type="button" onclick="confirmLogout('logout-form-sidebar')"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-emerald-200 hover:text-white hover:bg-red-500/20 transition text-sm font-medium">
                        <i class="fas fa-sign-out-alt w-5 text-center text-red-400"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- MAIN CONTENT AREA --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            {{-- HEADER --}}
            <header class="flex items-center justify-between px-4 lg:px-6 py-3 bg-white shadow-sm border-b border-gray-100 sticky top-0 z-20 shrink-0">
                
                {{-- Left: Hamburger + Title --}}
                <div class="flex items-center gap-3 min-w-0">
                    <button onclick="openSidebar()" 
                            class="lg:hidden text-gray-500 hover:text-emerald-600 focus:outline-none p-2 rounded-lg hover:bg-gray-100 transition shrink-0">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <div class="min-w-0 flex items-center gap-3">
                        <div>
                            <h1 class="text-base sm:text-lg font-bold text-gray-800 truncate">
                                @yield('header_title', 'Sistem Keuangan')
                            </h1>
                            <p class="text-[10px] text-gray-400 hidden sm:block">MTs Miftahul Ulum Dampit</p>
                        </div>
                        @php
                            $activeYear = \App\Models\AcademicYear::getActive();
                        @endphp
                        @if($activeYear)
                            <div class="hidden md:flex bg-emerald-50 border border-emerald-200 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold items-center gap-1.5 shadow-sm">
                                <i class="fas fa-calendar-alt"></i>
                                TA: {{ $activeYear->name }}
                            </div>
                        @endif
                    </div>
                </div>
                
                {{-- Right: User Info + Logout --}}
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    {{-- User Info (desktop) --}}
                    <div class="hidden md:flex flex-col text-right">
                        <span class="text-sm font-bold text-gray-800 leading-tight">{{ Auth::user()->name }}</span>
                        <span class="text-[10px] font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full inline-block mt-0.5 ml-auto w-max border border-emerald-100">
                            {{ ucfirst(Auth::user()->role) }}
                        </span>
                    </div>
                    
                    {{-- Avatar --}}
                    <div class="h-9 w-9 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white font-bold shadow text-sm shrink-0">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>

                    {{-- Logout (desktop only - sidebar has logout too) --}}
                    <form method="POST" action="{{ route('logout') }}" class="hidden lg:block" id="logout-form-header">
                        @csrf
                        <button type="button" onclick="confirmLogout('logout-form-header')"
                                class="text-gray-400 hover:text-red-600 transition p-2 rounded-lg hover:bg-red-50" 
                                title="Logout">
                            <i class="fas fa-sign-out-alt text-lg"></i>
                        </button>
                    </form>
                </div>
            </header>

            {{-- PAGE CONTENT --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 main-content">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        function openSidebar() {
            document.getElementById('mainSidebar').classList.remove('mobile-sidebar-hidden');
            document.getElementById('mainSidebar').classList.add('mobile-sidebar-shown');
            document.getElementById('sidebarOverlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            document.getElementById('mainSidebar').classList.add('mobile-sidebar-hidden');
            document.getElementById('mainSidebar').classList.remove('mobile-sidebar-shown');
            document.getElementById('sidebarOverlay').classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close sidebar on resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                document.getElementById('sidebarOverlay').classList.remove('active');
                document.body.style.overflow = '';
                // reset mobile specific classes on desktop
                document.getElementById('mainSidebar').classList.remove('mobile-sidebar-shown');
                document.getElementById('mainSidebar').classList.add('mobile-sidebar-hidden');
            }
        });

        // Close sidebar with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeSidebar();
        });

        // Logout Confirmation
        function confirmLogout(formId) {
            Swal.fire({
                title: 'Konfirmasi Keluar',
                text: "Apakah Anda yakin ingin keluar dari sistem?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            })
        }
    </script>
</body>
</html>