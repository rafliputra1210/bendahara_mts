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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900" x-data="{ sidebarOpen: false }">
    
    <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-64 bg-emerald-800 text-white transition duration-300 transform lg:translate-x-0 lg:static lg:inset-0 flex flex-col h-screen">
        <div class="flex items-center justify-center h-16 bg-emerald-900">
            <span class="text-xl font-bold uppercase tracking-wider text-white">BendaharaPro</span>
        </div>
        
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-emerald-700 transition {{ request()->routeIs('dashboard') ? 'bg-emerald-700' : '' }}">
                <i class="fas fa-home w-6"></i>
                <span>Dashboard</span>
            </a>
           <a href="{{ route('students.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-emerald-700 transition {{ request()->routeIs('students.*') ? 'bg-emerald-700' : '' }}">
                <i class="fas fa-users w-6"></i>
                <span>Data Siswa</span>
            </a>
            <a href="{{ route('incomes.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-emerald-700 transition {{ request()->routeIs('incomes.*') ? 'bg-emerald-700' : '' }}">
                <i class="fas fa-arrow-down w-6 text-green-300"></i>
                <span>Kas Masuk</span>
            </a>
            <a href="{{ route('expenses.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-emerald-700 transition {{ request()->routeIs('expenses.*') ? 'bg-emerald-700' : '' }}">
                <i class="fas fa-arrow-up w-6 text-red-300"></i>
                <span>Kas Keluar</span>
            </a>
            <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-emerald-700 transition {{ request()->routeIs('reports.*') ? 'bg-emerald-700' : '' }}">
            <i class="fas fa-file-pdf w-6 text-blue-300"></i>
                <span>Laporan</span>
            </a>
        </nav>
    </div>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="flex items-center justify-between px-6 py-4 bg-white shadow">
            <div class="flex items-center">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none lg:hidden">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="text-sm font-medium text-gray-700">
                    {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
            @yield('content')
        </main>
    </div>
</body>
</html>