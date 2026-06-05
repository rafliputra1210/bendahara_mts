@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Dashboard Statistik</h2>
    <p class="text-gray-600 text-sm">Ringkasan keuangan dan data siswa.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500 flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-gray-500 mb-1">Total Saldo Kas</p>
            <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($saldoKas, 0, ',', '.') }}</h3>
        </div>
        <div class="p-3 bg-blue-100 rounded-full text-blue-500">
            <i class="fas fa-wallet fa-lg"></i>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500 flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-gray-500 mb-1">Total Pemasukan</p>
            <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
        </div>
        <div class="p-3 bg-green-100 rounded-full text-green-500">
            <i class="fas fa-arrow-down fa-lg"></i>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-red-500 flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-gray-500 mb-1">Total Pengeluaran</p>
            <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
        </div>
        <div class="p-3 bg-red-100 rounded-full text-red-500">
            <i class="fas fa-arrow-up fa-lg"></i>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-purple-500 flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-gray-500 mb-1">Siswa Aktif</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $totalSiswa }} Anak</h3>
        </div>
        <div class="p-3 bg-purple-100 rounded-full text-purple-500">
            <i class="fas fa-users fa-lg"></i>
        </div>
    </div>

</div>

<div class="bg-white rounded-xl shadow">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-800">Pemasukan Terbaru</h3>
    </div>
    <div class="p-6 overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="border-b-2 p-3 text-sm font-semibold text-gray-600">Tanggal</th>
                    <th class="border-b-2 p-3 text-sm font-semibold text-gray-600">Siswa</th>
                    <th class="border-b-2 p-3 text-sm font-semibold text-gray-600">Jenis</th>
                    <th class="border-b-2 p-3 text-sm font-semibold text-gray-600">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksiTerbaru as $transaksi)
                <tr class="hover:bg-gray-50">
                    <td class="border-b p-3 text-sm text-gray-700">{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d M Y') }}</td>
                    <td class="border-b p-3 text-sm text-gray-700">{{ $transaksi->student->nama ?? 'Non-Siswa' }}</td>
                    <td class="border-b p-3 text-sm text-gray-700">{{ $transaksi->jenis_pembayaran }}</td>
                    <td class="border-b p-3 text-sm font-medium text-green-600">+ Rp {{ number_format($transaksi->nominal, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center p-4 text-gray-500 italic">Belum ada transaksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection