@extends('layouts.admin')
@section('header_title', 'Dashboard')

@section('content')
{{-- Page Header --}}
<div class="mb-5 sm:mb-6">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Dashboard Statistik</h2>
    <p class="text-gray-500 text-sm mt-0.5">Ringkasan keuangan dan data siswa.</p>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-6 sm:mb-8">
    
    {{-- Saldo Kas --}}
    <div class="col-span-2 sm:col-span-1 bg-white rounded-xl shadow-sm p-4 sm:p-5 border border-gray-100 border-l-4 border-l-blue-500 flex items-center justify-between hover:shadow-md transition">
        <div class="min-w-0">
            <p class="text-xs sm:text-sm font-semibold text-gray-500 mb-1">Total Saldo Kas</p>
            <h3 class="text-lg sm:text-2xl font-bold text-gray-800 truncate">Rp {{ number_format($saldoKas, 0, ',', '.') }}</h3>
        </div>
        <div class="p-2.5 sm:p-3 bg-blue-100 rounded-full text-blue-500 shrink-0 ml-3">
            <i class="fas fa-wallet sm:fa-lg text-sm sm:text-base"></i>
        </div>
    </div>

    {{-- Total Pemasukan --}}
    <div class="bg-white rounded-xl shadow-sm p-4 sm:p-5 border border-gray-100 border-l-4 border-l-green-500 flex items-center justify-between hover:shadow-md transition">
        <div class="min-w-0">
            <p class="text-xs sm:text-sm font-semibold text-gray-500 mb-1">Total Pemasukan</p>
            <h3 class="text-base sm:text-2xl font-bold text-gray-800 truncate">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
        </div>
        <div class="p-2.5 sm:p-3 bg-green-100 rounded-full text-green-500 shrink-0 ml-3">
            <i class="fas fa-arrow-down sm:fa-lg text-sm sm:text-base"></i>
        </div>
    </div>

    {{-- Total Pengeluaran --}}
    <div class="bg-white rounded-xl shadow-sm p-4 sm:p-5 border border-gray-100 border-l-4 border-l-red-500 flex items-center justify-between hover:shadow-md transition">
        <div class="min-w-0">
            <p class="text-xs sm:text-sm font-semibold text-gray-500 mb-1">Total Pengeluaran</p>
            <h3 class="text-base sm:text-2xl font-bold text-gray-800 truncate">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
        </div>
        <div class="p-2.5 sm:p-3 bg-red-100 rounded-full text-red-500 shrink-0 ml-3">
            <i class="fas fa-arrow-up sm:fa-lg text-sm sm:text-base"></i>
        </div>
    </div>

    {{-- Siswa Aktif --}}
    <div class="bg-white rounded-xl shadow-sm p-4 sm:p-5 border border-gray-100 border-l-4 border-l-purple-500 flex items-center justify-between hover:shadow-md transition">
        <div class="min-w-0">
            <p class="text-xs sm:text-sm font-semibold text-gray-500 mb-1">Siswa Aktif</p>
            <h3 class="text-lg sm:text-2xl font-bold text-gray-800">{{ $totalSiswa }} <span class="text-sm font-medium text-gray-500">Anak</span></h3>
        </div>
        <div class="p-2.5 sm:p-3 bg-purple-100 rounded-full text-purple-500 shrink-0 ml-3">
            <i class="fas fa-users sm:fa-lg text-sm sm:text-base"></i>
        </div>
    </div>

</div>

{{-- Recent Transactions --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-base sm:text-lg font-bold text-gray-800">Pemasukan Terbaru</h3>
        <a href="{{ route('incomes.index') }}" class="text-xs sm:text-sm text-emerald-600 hover:text-emerald-700 font-medium flex items-center gap-1">
            Lihat semua <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>
    
    {{-- Desktop Table View --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80">
                    <th class="border-b border-gray-100 p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Tanggal</th>
                    <th class="border-b border-gray-100 p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Siswa</th>
                    <th class="border-b border-gray-100 p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Jenis</th>
                    <th class="border-b border-gray-100 p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Nominal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($transaksiTerbaru as $transaksi)
                <tr class="hover:bg-gray-50/80 transition">
                    <td class="p-3 sm:p-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d M Y') }}</td>
                    <td class="p-3 sm:p-4 text-sm text-gray-800 font-medium">{{ $transaksi->student->nama ?? 'Non-Siswa' }}</td>
                    <td class="p-3 sm:p-4 text-sm">
                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-medium border border-emerald-100">
                            {{ $transaksi->jenis_pembayaran }}
                        </span>
                    </td>
                    <td class="p-3 sm:p-4 text-sm font-bold text-green-600 text-right">+ Rp {{ number_format($transaksi->nominal, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center p-8 text-gray-400">
                        <i class="fas fa-inbox text-3xl mb-2 block opacity-40"></i>
                        Belum ada transaksi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Card View --}}
    <div class="sm:hidden divide-y divide-gray-100">
        @forelse ($transaksiTerbaru as $transaksi)
        <div class="p-4 flex items-center justify-between gap-3">
            <div class="min-w-0 flex-1">
                <div class="font-semibold text-gray-800 text-sm truncate">{{ $transaksi->student->nama ?? 'Non-Siswa' }}</div>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d M Y') }}</span>
                    <span class="text-[10px] px-1.5 py-0.5 bg-emerald-50 text-emerald-700 rounded font-medium">{{ $transaksi->jenis_pembayaran }}</span>
                </div>
            </div>
            <div class="text-sm font-bold text-green-600 shrink-0">+ Rp {{ number_format($transaksi->nominal, 0, ',', '.') }}</div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-400">
            <i class="fas fa-inbox text-3xl mb-2 block opacity-40"></i>
            <p class="text-sm">Belum ada transaksi.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection