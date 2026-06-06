<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'BendaharaPro') }} - Login</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        
        <div class="min-h-screen flex flex-col lg:flex-row">
            
            {{-- Left Panel (hidden on small screens) --}}
            <div class="hidden lg:flex lg:flex-1 bg-gradient-to-br from-emerald-800 to-emerald-600 items-center justify-center p-12 relative overflow-hidden">
                {{-- Background decoration --}}
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-10 left-10 w-64 h-64 bg-white rounded-full"></div>
                    <div class="absolute bottom-20 right-10 w-96 h-96 bg-emerald-400 rounded-full"></div>
                    <div class="absolute top-1/2 left-1/3 w-32 h-32 bg-white rounded-full"></div>
                </div>
                
                <div class="relative z-10 text-white max-w-md text-center">
                    <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-2xl backdrop-blur-sm">
                        <i class="fas fa-coins text-white text-3xl"></i>
                    </div>
                    <h1 class="text-3xl font-black mb-3 tracking-tight">BendaharaPro</h1>
                    <p class="text-emerald-100 text-lg font-medium mb-2">Sistem Keuangan MTs Miftahul Ulum</p>
                    <p class="text-emerald-200/80 text-sm">Kelola kas masuk, kas keluar, tagihan siswa, dan laporan keuangan dengan mudah.</p>
                    
                    <div class="flex items-center justify-center gap-6 mt-10">
                        <div class="text-center">
                            <div class="text-2xl font-bold">100%</div>
                            <div class="text-xs text-emerald-200/80">Terintegrasi</div>
                        </div>
                        <div class="w-px h-10 bg-white/20"></div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">Realtime</div>
                            <div class="text-xs text-emerald-200/80">Laporan</div>
                        </div>
                        <div class="w-px h-10 bg-white/20"></div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">Auto</div>
                            <div class="text-xs text-emerald-200/80">Kuitansi</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Panel (Login Form) --}}
            <div class="flex-1 lg:max-w-md xl:max-w-lg flex flex-col justify-center min-h-screen p-6 sm:p-10 bg-white">
                
                {{-- Mobile logo --}}
                <div class="lg:hidden text-center mb-8">
                    <div class="w-16 h-16 bg-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-coins text-white text-2xl"></i>
                    </div>
                    <h1 class="text-2xl font-black text-gray-900">BendaharaPro</h1>
                    <p class="text-gray-500 text-sm mt-1">MTs Miftahul Ulum Dampit</p>
                </div>
                
                <div class="w-full max-w-sm mx-auto lg:max-w-none">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
