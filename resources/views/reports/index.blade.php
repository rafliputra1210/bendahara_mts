@extends('layouts.admin')
@section('header_title', 'Laporan Keuangan')

@section('content')
<div class="mb-5 sm:mb-6">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Laporan Keuangan</h2>
    <p class="text-gray-500 text-sm mt-0.5">Buku Kas Umum (Pemasukan & Pengeluaran)</p>
</div>

{{-- Filter & Export --}}
<div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100 mb-5 sm:mb-6">
    <form action="{{ route('reports.index') }}" method="GET">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:flex-wrap">
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" 
                       class="w-full border-gray-300 rounded-xl focus:ring-gray-500 focus:border-gray-500 text-sm py-2">
            </div>
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                       class="w-full border-gray-300 rounded-xl focus:ring-gray-500 focus:border-gray-500 text-sm py-2">
            </div>
            <div class="flex gap-2 flex-wrap sm:flex-nowrap sm:shrink-0">
                <button type="submit" 
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 bg-gray-800 text-white px-4 py-2 rounded-xl font-medium hover:bg-gray-900 transition text-sm">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('reports.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                   target="_blank" 
                   class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 bg-red-500 text-white px-4 py-2 rounded-xl font-medium hover:bg-red-600 transition text-sm">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                <a href="{{ route('reports.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                   class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 bg-green-600 text-white px-4 py-2 rounded-xl font-medium hover:bg-green-700 transition text-sm">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[500px]">
            <thead>
                <tr class="bg-gray-50/80 border-b-2 border-gray-200">
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-600">Tanggal</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-600">Keterangan / Rincian</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-600 text-right">Debet (Masuk)</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-600 text-right">Kredit (Keluar)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @php 
                    $totalMasuk = 0; 
                    $totalKeluar = 0; 
                @endphp
                
                @forelse ($transactions as $t)
                    @php
                        if($t->tipe == 'masuk') $totalMasuk += $t->nominal;
                        else $totalKeluar += $t->nominal;
                    @endphp
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="p-3 sm:p-4 text-sm text-gray-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
                        <td class="p-3 sm:p-4 text-sm text-gray-700">
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $t->tipe == 'masuk' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                {{ $t->deskripsi }}
                            </div>
                        </td>
                        <td class="p-3 sm:p-4 text-sm text-right whitespace-nowrap font-medium text-green-600">
                            {{ $t->tipe == 'masuk' ? 'Rp ' . number_format($t->nominal, 0, ',', '.') : '' }}
                        </td>
                        <td class="p-3 sm:p-4 text-sm text-right whitespace-nowrap font-medium text-red-600">
                            {{ $t->tipe == 'keluar' ? 'Rp ' . number_format($t->nominal, 0, ',', '.') : '' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-10 text-center text-gray-400">
                            <i class="fas fa-chart-bar text-4xl mb-3 block opacity-20"></i>
                            <p class="font-medium">Tidak ada transaksi pada periode ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="border-t-2 border-gray-200">
                <tr class="bg-gray-50/80 font-bold">
                    <td colspan="2" class="p-3 sm:p-4 text-sm text-right text-gray-700">Total Periode:</td>
                    <td class="p-3 sm:p-4 text-sm text-right text-green-700 whitespace-nowrap">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
                    <td class="p-3 sm:p-4 text-sm text-right text-red-700 whitespace-nowrap">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
                </tr>
                <tr class="bg-blue-50 border-t border-gray-200">
                    <td colspan="2" class="p-3 sm:p-4 text-sm text-right font-bold text-blue-900">Saldo Akhir Periode:</td>
                    <td colspan="2" class="p-3 sm:p-4 text-center font-bold text-lg {{ ($totalMasuk - $totalKeluar) >= 0 ? 'text-blue-900' : 'text-red-700' }}">
                        Rp {{ number_format($totalMasuk - $totalKeluar, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection