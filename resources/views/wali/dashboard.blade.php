<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Wali Siswa - BendaharaPro</title>
    <link rel="icon" type="image/png" href="{{ asset('images/LOGO MTS.png') }}">
    @vite(['resources/css/app.css'])
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 antialiased font-sans" x-data="{ tab: 'ringkasan' }">
    
    <!-- Wrapper Utama (Membatasi lebar maksimal agar bagus di Desktop & Mobile) -->
    <div class="max-w-3xl mx-auto bg-gray-50 min-h-screen shadow-2xl relative pb-10">

        <!-- HEADER BAGIAN ATAS -->
        <header class="bg-emerald-700 text-white rounded-b-[2.5rem] shadow-lg relative z-20">
            <!-- Info Profil -->
            <div class="px-6 pt-10 pb-6 flex justify-between items-start">
                <div class="flex-1 pr-4">
                    <p class="text-emerald-200 text-[11px] font-bold uppercase tracking-widest mb-1.5">Selamat Datang, Wali Dari</p>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight truncate">
                        {{ $student->nama }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-2 mt-3">
                        <span class="bg-emerald-800/60 border border-emerald-600/50 text-emerald-50 text-[10px] sm:text-xs px-2.5 py-1 rounded-md font-semibold tracking-wide shadow-sm">
                            NISN: {{ $student->nis }}
                        </span>
                        <span class="bg-emerald-800/60 border border-emerald-600/50 text-emerald-50 text-[10px] sm:text-xs px-2.5 py-1 rounded-md font-semibold tracking-wide shadow-sm">
                            Kelas {{ $student->kelas }}
                        </span>
                    </div>
                </div>
                
                <!-- Tombol Logout -->
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="w-11 h-11 bg-red-500 hover:bg-red-600 text-white rounded-full shadow-md flex justify-center items-center transition-all active:scale-95 border-2 border-red-400" title="Keluar">
                        <i class="fas fa-power-off text-sm"></i>
                    </button>
                </form>
            </div>

            <!-- TAB NAVIGASI -->
            <div class="px-4 mt-2 pb-2">
                <div class="flex gap-2 overflow-x-auto no-scrollbar pb-3 px-2">
                    <button @click="tab = 'ringkasan'" 
                            :class="tab === 'ringkasan' ? 'bg-white text-emerald-700 shadow-md scale-100' : 'text-emerald-50 hover:bg-emerald-600/50 scale-95'" 
                            class="px-5 py-2.5 rounded-2xl text-sm font-bold whitespace-nowrap transition-all duration-300 flex items-center gap-2 transform">
                        <i class="fas fa-chart-pie"></i> Ringkasan
                    </button>
                    <button @click="tab = 'tagihan'" 
                            :class="tab === 'tagihan' ? 'bg-white text-emerald-700 shadow-md scale-100' : 'text-emerald-50 hover:bg-emerald-600/50 scale-95'" 
                            class="px-5 py-2.5 rounded-2xl text-sm font-bold whitespace-nowrap transition-all duration-300 flex items-center gap-2 transform">
                        <i class="fas fa-file-invoice-dollar"></i> Tagihan
                    </button>
                    <button @click="tab = 'riwayat'" 
                            :class="tab === 'riwayat' ? 'bg-white text-emerald-700 shadow-md scale-100' : 'text-emerald-50 hover:bg-emerald-600/50 scale-95'" 
                            class="px-5 py-2.5 rounded-2xl text-sm font-bold whitespace-nowrap transition-all duration-300 flex items-center gap-2 transform">
                        <i class="fas fa-history"></i> Riwayat Bayar
                    </button>
                </div>
            </div>
        </header>

        <!-- KONTEN UTAMA -->
        <main class="px-6 pt-8 pb-10">

            <!-- TAB 1: RINGKASAN -->
            <div x-show="tab === 'ringkasan'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                
                <!-- Progress Bar Pelunasan -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-5 relative overflow-hidden">
                    <div class="flex justify-between items-end mb-3">
                        <h3 class="font-extrabold text-gray-800 text-sm">Progress Pelunasan</h3>
                        <span class="text-emerald-600 font-black text-xl">{{ $persentase }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3 mb-1 overflow-hidden shadow-inner">
                        <div class="bg-emerald-500 h-3 rounded-full transition-all duration-1000 ease-out" style="width: {{ $persentase }}%"></div>
                    </div>
                </div>

                <!-- Kartu Saldo -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center items-center text-center">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex justify-center items-center mb-3">
                            <i class="fas fa-check-circle text-lg"></i>
                        </div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Telah Dibayar</p>
                        <p class="font-black text-gray-800 text-sm sm:text-base">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</p>
                    </div>
                    
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center items-center text-center">
                        <div class="w-10 h-10 rounded-full bg-red-50 text-red-500 flex justify-center items-center mb-3">
                            <i class="fas fa-exclamation-circle text-lg"></i>
                        </div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Sisa Tunggakan</p>
                        <p class="font-black text-red-600 text-sm sm:text-base">Rp {{ number_format($sisaTunggakan, 0, ',', '.') }}</p>
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl flex items-start gap-3 shadow-sm">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    <p class="text-xs text-blue-800 leading-relaxed font-medium">
                        Catatan: Data keuangan diperbarui otomatis. Jika terdapat ketidaksesuaian nominal, silakan konfirmasi ke Bendahara.
                    </p>
                </div>
            </div>

            <!-- TAB 2: TAGIHAN -->
            <div x-show="tab === 'tagihan'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <h3 class="font-extrabold text-gray-800 mb-5 flex items-center gap-2 text-lg">
                    <i class="fas fa-list-ul text-emerald-600"></i> Daftar Rincian Tagihan
                </h3>
                
                <div class="space-y-4">
                    @forelse($tagihans as $tagihan)
                        @php 
                            $sisa = $tagihan->total_tagihan - $tagihan->total_dibayar; 
                            $persenTagihan = $tagihan->total_tagihan > 0 ? ($tagihan->total_dibayar / $tagihan->total_tagihan) * 100 : 0;
                        @endphp
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden transition-all hover:shadow-md">
                            @if($tagihan->status == 'lunas')
                                <div class="absolute top-0 right-0 w-20 h-20 overflow-hidden pointer-events-none">
                                    <div class="bg-emerald-500 text-white text-[10px] font-black text-center py-1 absolute top-4 -right-5 w-24 transform rotate-45 shadow-sm">LUNAS</div>
                                </div>
                            @endif

                            <h4 class="font-extrabold text-gray-800 text-base pr-10 mb-1">{{ $tagihan->nama_tagihan }}</h4>
                            <p class="text-[11px] text-gray-500 font-semibold mb-4">Total Target: Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</p>
                            
                            <div class="flex justify-between items-end mb-2">
                                <div>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Telah Dibayar</span>
                                    <p class="text-sm font-black text-emerald-600">Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Sisa</span>
                                    <p class="text-sm font-black text-red-500">Rp {{ number_format($sisa, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden shadow-inner">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $persenTagihan }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white p-10 rounded-2xl border border-dashed border-gray-200 text-center">
                            <i class="fas fa-check-circle text-emerald-200 text-5xl mb-4"></i>
                            <p class="text-sm font-bold text-gray-500">Hebat! Tidak ada tagihan saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- TAB 3: RIWAYAT -->
            <div x-show="tab === 'riwayat'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <h3 class="font-extrabold text-gray-800 mb-5 flex items-center gap-2 text-lg">
                    <i class="fas fa-history text-emerald-600"></i> Histori Pembayaran Terakhir
                </h3>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-100">
                    @forelse($incomes as $income)
                        <div class="p-4 sm:p-5 hover:bg-gray-50 transition flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full {{ $income->bukti ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-500' }} flex justify-center items-center shrink-0">
                                <i class="fas fa-file-invoice-dollar text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-gray-800 text-sm truncate mb-0.5">{{ $income->jenis_pembayaran }}</p>
                                <p class="text-xs text-gray-400 font-medium">
                                    <i class="far fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($income->tanggal)->format('d M Y') }}
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-black text-emerald-600 text-sm">+ Rp {{ number_format($income->nominal, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center">
                            <i class="fas fa-history text-gray-200 text-5xl mb-4"></i>
                            <p class="text-sm font-bold text-gray-500">Belum ada riwayat pembayaran.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </main>
    </div>
</body>
</html>