@extends('layouts.admin')
@section('header_title', 'Riwayat Kas Transaksi')

@section('content')
<!-- Notifikasi SweetAlert -->
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

<!-- Header Navigation & Action -->
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-xl sm:text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
            <i class="fas fa-history text-blue-600"></i> Riwayat Kas Transaksi
        </h2>
        <p class="text-gray-500 text-sm mt-1">Daftar seluruh riwayat transaksi penerimaan kas (pemasukan) dan pengeluaran operasional.</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('incomes.index') }}" 
           class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl font-medium shadow-sm transition text-sm">
            <i class="fas fa-plus-circle"></i> Input Kas Masuk
        </a>
        <a href="{{ route('expenses.index') }}" 
           class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl font-medium shadow-sm transition text-sm">
            <i class="fas fa-minus-circle"></i> Input Kas Keluar
        </a>
    </div>
</div>

<!-- Ringkasan Keuangan Card -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
            <i class="fas fa-arrow-down"></i>
        </div>
        <div>
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Kas Masuk</div>
            <div class="text-lg sm:text-xl font-black text-emerald-600 mt-0.5">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl shrink-0">
            <i class="fas fa-arrow-up"></i>
        </div>
        <div>
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Kas Keluar</div>
            <div class="text-lg sm:text-xl font-black text-red-600 mt-0.5">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
            <i class="fas fa-wallet"></i>
        </div>
        <div>
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo Bersih Kas</div>
            <div class="text-lg sm:text-xl font-black {{ $netBalance >= 0 ? 'text-gray-900' : 'text-red-600' }} mt-0.5">
                Rp {{ number_format($netBalance, 0, ',', '.') }}
            </div>
        </div>
    </div>
</div>

<!-- Main Table Box -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <!-- Tab Filters & Search -->
    <div class="p-4 sm:p-5 border-b border-gray-100 bg-gradient-to-r from-gray-50/80 to-white flex flex-col md:flex-row justify-between items-stretch md:items-center gap-4">
        
        <!-- Tab Options -->
        <div class="flex items-center gap-1.5 p-1 bg-gray-100/80 rounded-xl self-start md:self-auto overflow-x-auto max-w-full">
            <a href="{{ route('incomes.history', ['type' => 'all', 'search' => request('search')]) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ $type === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
               <i class="fas fa-list mr-1.5"></i> Semua Transaksi
            </a>
            <a href="{{ route('incomes.history', ['type' => 'income', 'search' => request('search')]) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ $type === 'income' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
               <i class="fas fa-arrow-down mr-1.5"></i> Pemasukan (Kas Masuk)
            </a>
            <a href="{{ route('incomes.history', ['type' => 'expense', 'search' => request('search')]) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ $type === 'expense' ? 'bg-red-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
               <i class="fas fa-arrow-up mr-1.5"></i> Pengeluaran (Kas Keluar)
            </a>
        </div>
        
        <!-- Search Input -->
        <form action="{{ route('incomes.history') }}" method="GET" class="w-full md:w-72 relative">
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Cari transaksi, siswa, jenis..." 
                   class="w-full pl-10 pr-4 py-2 border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-150">
                    <th class="p-3.5 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500 pl-4 sm:pl-6">Tipe & Tanggal</th>
                    <th class="p-3.5 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Detail Transaksi</th>
                    <th class="p-3.5 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Bukti</th>
                    <th class="p-3.5 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Nominal</th>
                    <th class="p-3.5 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-center pr-4 sm:pr-6">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $trx)
                <tr class="hover:bg-gray-50/80 transition duration-150">
                    <!-- Tipe & Tanggal -->
                    <td class="p-3.5 sm:p-4 pl-4 sm:pl-6 text-sm text-gray-600 whitespace-nowrap">
                        <div class="flex items-center gap-2 mb-1">
                            @if($trx->transaction_type === 'income')
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-extrabold rounded-md border border-emerald-100 uppercase tracking-wider inline-flex items-center gap-1">
                                    <i class="fas fa-arrow-down text-[9px]"></i> Pemasukan
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-red-50 text-red-700 text-[10px] font-extrabold rounded-md border border-red-100 uppercase tracking-wider inline-flex items-center gap-1">
                                    <i class="fas fa-arrow-up text-[9px]"></i> Pengeluaran
                                </span>
                            @endif
                        </div>
                        <div class="font-medium text-gray-700 text-xs">
                            {{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}
                        </div>
                    </td>

                    <!-- Detail Transaksi -->
                    <td class="p-3.5 sm:p-4">
                        @if($trx->transaction_type === 'income')
                            @if($trx->student)
                                <div class="font-bold text-gray-900 text-sm">{{ $trx->student->nama }}</div>
                                <div class="text-[11px] text-gray-500 font-medium">NIS: {{ $trx->student->nis }} | Kelas {{ $trx->student->kelas }}</div>
                            @else
                                <div class="font-bold text-gray-700 text-sm italic">Pemasukan Lainnya / Non-Siswa</div>
                            @endif

                            <div class="text-xs text-gray-600 mt-1 flex items-center gap-1.5 flex-wrap">
                                <span class="font-semibold text-gray-800">{{ $trx->jenis_pembayaran }}</span>
                                @if($trx->tagihan)
                                    <span class="text-[10px] bg-blue-50 border border-blue-100 px-2 py-0.5 rounded text-blue-600 font-bold">{{ $trx->tagihan->nama_tagihan }}</span>
                                @endif
                            </div>
                            @if($trx->keterangan)
                                <div class="text-[11px] text-gray-400 mt-0.5 italic">Catatan: {{ $trx->keterangan }}</div>
                            @endif
                        @else
                            <div class="font-bold text-gray-900 text-sm flex items-center gap-2">
                                <span>{{ $trx->kategori }}</span>
                            </div>
                            @if($trx->keterangan)
                                <div class="text-xs text-gray-600 mt-0.5">{{ $trx->keterangan }}</div>
                            @else
                                <div class="text-xs text-gray-400 italic">Tidak ada rincian keterangan</div>
                            @endif
                        @endif
                    </td>

                    <!-- Bukti File -->
                    <td class="p-3.5 sm:p-4 text-center whitespace-nowrap">
                        @if($trx->bukti)
                            <a href="{{ asset('storage/' . $trx->bukti) }}" target="_blank" 
                               class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-xs font-semibold border border-blue-100 transition">
                                <i class="fas fa-file-invoice"></i> Lihat Bukti
                            </a>
                        @else
                            <span class="text-gray-300 text-xs">-</span>
                        @endif
                    </td>

                    <!-- Nominal -->
                    <td class="p-3.5 sm:p-4 text-right whitespace-nowrap">
                        @if($trx->transaction_type === 'income')
                            <span class="text-sm font-black text-emerald-600 block">+ Rp {{ number_format($trx->nominal, 0, ',', '.') }}</span>
                        @else
                            <span class="text-sm font-black text-red-600 block">- Rp {{ number_format($trx->nominal, 0, ',', '.') }}</span>
                        @endif
                    </td>

                    <!-- Aksi -->
                    <td class="p-3.5 sm:p-4 text-center pr-4 sm:pr-6 whitespace-nowrap">
                        @if($trx->transaction_type === 'income')
                            <form action="{{ route('incomes.destroy', $trx->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pemasukan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition border border-transparent hover:border-red-200" title="Hapus Pemasukan">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        @else
                            <button type="button" onclick="confirmDeleteExpense({{ $trx->id }})" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition border border-transparent hover:border-red-200" title="Hapus Pengeluaran">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                            <form id="delete-expense-{{ $trx->id }}" action="{{ route('expenses.destroy', $trx->id) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
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

    <!-- Pagination -->
    @if($transactions->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
    @endif
</div>

<script>
    function confirmDeleteExpense(id) {
        Swal.fire({
            title: 'Hapus Pengeluaran?',
            text: "Data pengeluaran ini akan dihapus dari riwayat transaksi!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-expense-' + id).submit();
            }
        });
    }
</script>
@endsection
