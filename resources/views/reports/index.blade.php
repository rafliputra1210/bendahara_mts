@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Laporan Keuangan</h2>
    <p class="text-gray-600 text-sm">Buku Kas Umum (Pemasukan & Pengeluaran)</p>
</div>

<div class="bg-white p-6 rounded-xl shadow mb-6">
    <form action="{{ route('reports.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="border-gray-300 rounded-lg focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="border-gray-300 rounded-lg focus:ring-blue-500">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg font-medium hover:bg-gray-900 transition"><i class="fas fa-filter mr-1"></i> Filter</button>
            <a href="{{ route('reports.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="bg-red-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-600 transition"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
            <a href="{{ route('reports.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-green-700 transition"><i class="fas fa-file-excel mr-1"></i> Excel</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto p-6">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b-2 border-gray-300">
                    <th class="p-3 text-sm font-semibold text-gray-700">Tanggal</th>
                    <th class="p-3 text-sm font-semibold text-gray-700">Keterangan / Rincian</th>
                    <th class="p-3 text-sm font-semibold text-gray-700 text-right">Debet (Masuk)</th>
                    <th class="p-3 text-sm font-semibold text-gray-700 text-right">Kredit (Keluar)</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $totalMasuk = 0; 
                    $totalKeluar = 0; 
                @endphp
                
                @forelse ($transactions as $t)
                    @php
                        if($t->tipe == 'masuk') $totalMasuk += $t->nominal;
                        else $totalKeluar += $t->nominal;
                    @endphp
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="p-3 text-sm">{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
                        <td class="p-3 text-sm">{{ $t->deskripsi }}</td>
                        <td class="p-3 text-sm text-right text-green-600">{{ $t->tipe == 'masuk' ? 'Rp ' . number_format($t->nominal, 0, ',', '.') : '-' }}</td>
                        <td class="p-3 text-sm text-right text-red-600">{{ $t->tipe == 'keluar' ? 'Rp ' . number_format($t->nominal, 0, ',', '.') : '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-4 text-center text-gray-500">Tidak ada transaksi pada periode ini.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-gray-50 font-bold border-t-2 border-gray-300">
                    <td colspan="2" class="p-3 text-right">Total:</td>
                    <td class="p-3 text-right text-green-700">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-red-700">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
                </tr>
                <tr class="bg-blue-50 font-bold border-t border-gray-300 text-lg">
                    <td colspan="2" class="p-3 text-right text-blue-900">Saldo Akhir Periode:</td>
                    <td colspan="2" class="p-3 text-center text-blue-900">Rp {{ number_format($totalMasuk - $totalKeluar, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection