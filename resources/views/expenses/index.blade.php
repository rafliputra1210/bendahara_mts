@extends('layouts.admin')
@section('header_title', 'Kas Keluar')

@section('content')
<div class="mb-5 sm:mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Data Kas Keluar</h2>
        <p class="text-gray-500 text-sm mt-0.5">Pencatatan seluruh pengeluaran operasional.</p>
    </div>
    <a href="{{ route('expenses.create') }}" 
       class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl font-medium shadow-sm transition text-sm shrink-0">
        <i class="fas fa-plus"></i> Input Pengeluaran
    </a>
</div>

@if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-2 text-sm">
        <i class="fas fa-check-circle shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    
    {{-- Desktop/Tablet Table --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-200">
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Tanggal</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Kategori</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Keterangan</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Nominal</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Bukti</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($expenses as $expense)
                <tr class="hover:bg-gray-50/80 transition">
                    <td class="p-3 sm:p-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($expense->tanggal)->format('d M Y') }}</td>
                    <td class="p-3 sm:p-4 text-sm">
                        <span class="px-2.5 py-1 bg-red-50 text-red-700 rounded-lg text-xs font-semibold border border-red-100">{{ $expense->kategori }}</span>
                    </td>
                    <td class="p-3 sm:p-4 text-sm text-gray-600 max-w-[200px] truncate">{{ $expense->keterangan ?: '-' }}</td>
                    <td class="p-3 sm:p-4 text-sm font-bold text-red-600 text-right">- Rp {{ number_format($expense->nominal, 0, ',', '.') }}</td>
                    <td class="p-3 sm:p-4 text-sm text-center">
                        @if($expense->bukti)
                            <a href="{{ asset('storage/' . $expense->bukti) }}" target="_blank" 
                               class="text-blue-500 hover:underline inline-flex items-center gap-1 text-xs">
                                <i class="fas fa-file-invoice"></i> Lihat
                            </a>
                        @else
                            <span class="text-gray-300 text-xs">-</span>
                        @endif
                    </td>
                    <td class="p-3 sm:p-4 text-sm text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('expenses.edit', $expense->id) }}" 
                               class="p-1.5 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <button type="button" onclick="confirmDelete({{ $expense->id }})" 
                                    class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                        <form id="delete-form-{{ $expense->id }}" action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-10 text-center text-gray-400">
                        <i class="fas fa-file-invoice-dollar text-4xl mb-3 block opacity-20"></i>
                        <p class="font-medium">Belum ada data pengeluaran.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Card View --}}
    <div class="sm:hidden divide-y divide-gray-100">
        @forelse ($expenses as $expense)
        <div class="p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="px-2 py-0.5 bg-red-50 text-red-700 rounded text-[10px] font-semibold border border-red-100">{{ $expense->kategori }}</span>
                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($expense->tanggal)->format('d M Y') }}</span>
                    </div>
                    <div class="text-sm font-bold text-red-600 mb-0.5">- Rp {{ number_format($expense->nominal, 0, ',', '.') }}</div>
                    @if($expense->keterangan)
                        <div class="text-xs text-gray-500 truncate">{{ $expense->keterangan }}</div>
                    @endif
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    @if($expense->bukti)
                        <a href="{{ asset('storage/' . $expense->bukti) }}" target="_blank" 
                           class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition">
                            <i class="fas fa-file-invoice text-sm"></i>
                        </a>
                    @endif
                    <a href="{{ route('expenses.edit', $expense->id) }}" 
                       class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition">
                        <i class="fas fa-edit text-sm"></i>
                    </a>
                    <button type="button" onclick="confirmDelete({{ $expense->id }})"
                            class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </div>
            </div>
            <form id="delete-form-{{ $expense->id }}" action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
        @empty
        <div class="p-10 text-center text-gray-400">
            <i class="fas fa-file-invoice-dollar text-4xl mb-3 block opacity-20"></i>
            <p class="font-medium text-sm">Belum ada data pengeluaran.</p>
        </div>
        @endforelse
    </div>
    
    <div class="p-4 border-t border-gray-100">
        {{ $expenses->links() }}
    </div>
</div>

<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 2000
        });
    @endif

    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Pengeluaran?',
            text: "Data yang dihapus akan otomatis mengembalikan nominal saldo kas!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endsection