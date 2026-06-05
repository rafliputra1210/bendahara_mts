@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Data Kas Masuk</h2>
        <p class="text-gray-600 text-sm">Pencatatan pembayaran siswa dan pemasukan lainnya.</p>
    </div>
    <a href="{{ route('incomes.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium shadow transition">
        <i class="fas fa-plus mr-2"></i> Input Pemasukan
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="p-4 text-sm font-semibold text-gray-600">Tanggal</th>
                    <th class="p-4 text-sm font-semibold text-gray-600">Siswa/Sumber</th>
                    <th class="p-4 text-sm font-semibold text-gray-600">Jenis</th>
                    <th class="p-4 text-sm font-semibold text-gray-600">Nominal</th>
                    <th class="p-4 text-sm font-semibold text-gray-600">Bukti</th>
                    <th class="p-4 text-sm font-semibold text-gray-600 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($incomes as $income)
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="p-4 text-sm text-gray-700">{{ \Carbon\Carbon::parse($income->tanggal)->format('d M Y') }}</td>
                    <td class="p-4 text-sm font-medium text-gray-900">{{ $income->student->nama ?? 'Lainnya / Non-Siswa' }}</td>
                    <td class="p-4 text-sm text-gray-700">{{ $income->jenis_pembayaran }}</td>
                    <td class="p-4 text-sm font-bold text-green-600">Rp {{ number_format($income->nominal, 0, ',', '.') }}</td>
                    <td class="p-4 text-sm">
                        @if($income->bukti)
                            <a href="{{ asset('storage/' . $income->bukti) }}" target="_blank" class="text-blue-500 hover:underline"><i class="fas fa-file-alt mr-1"></i> Lihat</a>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="p-4 text-sm text-center space-x-2">
                        <form action="{{ route('incomes.destroy', $income->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus data kas masuk ini? Saldo akan berkurang otomatis.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-6 text-center text-gray-500">Belum ada transaksi kas masuk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $incomes->links() }}</div>
</div>
@endsection