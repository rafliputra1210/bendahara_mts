@extends('layouts.admin')
@section('header_title', 'Riwayat Kas')

@section('content')
<!-- Notifikasi -->
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        timer: 3000,
        showConfirmButton: false
    });
</script>
@endif

<div class="mb-5 sm:mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-xl sm:text-3xl font-bold text-gray-800 tracking-tight">Riwayat Kas Masuk</h2>
        <p class="text-gray-500 text-sm mt-0.5">Daftar seluruh transaksi penerimaan kas dan pembayaran siswa.</p>
    </div>
    <a href="{{ route('incomes.index') }}" 
       class="inline-flex items-center justify-center gap-2 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 px-4 py-2.5 rounded-xl font-medium shadow-sm transition text-sm shrink-0">
        <i class="fas fa-arrow-left"></i> Kembali ke Kas Masuk
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <div class="p-4 sm:p-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h3 class="text-base sm:text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-history text-blue-500"></i> Semua Riwayat Transaksi
        </h3>
        
        <form action="{{ route('incomes.history') }}" method="GET" class="w-full sm:w-auto relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIS, jenis..." class="w-full sm:w-64 pl-10 pr-4 py-2 border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-150">
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500 pl-4 sm:pl-6">Tanggal</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Siswa</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Keterangan</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Nominal</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-center pr-4 sm:pr-6">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($incomes as $income)
                <tr class="hover:bg-gray-50/80 transition duration-150">
                    <td class="p-3 sm:p-4 pl-4 sm:pl-6 text-sm text-gray-600 font-medium whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($income->tanggal)->format('d M Y') }}
                    </td>
                    <td class="p-3 sm:p-4">
                        @if($income->student)
                            <div class="font-semibold text-gray-900 text-sm">{{ $income->student->nama }}</div>
                            <div class="text-[10px] text-gray-500">NIS: {{ $income->student->nis }} | Kelas {{ $income->student->kelas }}</div>
                        @else
                            <div class="font-semibold text-gray-500 text-sm italic">Pemasukan Lainnya / Non-Siswa</div>
                        @endif
                    </td>
                    <td class="p-3 sm:p-4 text-sm text-gray-700">
                        {{ $income->jenis_pembayaran }} 
                        @if($income->tagihan)
                            <span class="text-[10px] bg-blue-50 border border-blue-100 px-2 py-0.5 rounded text-blue-600 font-bold ml-1">{{ $income->tagihan->nama_tagihan }}</span>
                        @endif
                        @if($income->keterangan)
                            <div class="text-[10px] text-gray-400 mt-0.5 italic">Catatan: {{ $income->keterangan }}</div>
                        @endif
                    </td>
                    <td class="p-3 sm:p-4 text-right whitespace-nowrap">
                        <span class="text-sm font-black text-emerald-600 block">Rp {{ number_format($income->nominal, 0, ',', '.') }}</span>
                    </td>
                    <td class="p-3 sm:p-4 text-center pr-4 sm:pr-6">
                        <form action="{{ route('incomes.destroy', $income->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini? (Total tagihan siswa perlu disesuaikan manual jika dibutuhkan)')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition border border-transparent hover:border-red-200" title="Hapus">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-10 text-center text-gray-400 font-medium">
                        <i class="fas fa-inbox text-4xl mb-3 opacity-20 block"></i>
                        Belum ada riwayat transaksi atau pencarian tidak ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($incomes->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $incomes->links() }}
        </div>
    @endif
</div>
@endsection
