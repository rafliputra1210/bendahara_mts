@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Data Kas Keluar</h2>
        <p class="text-gray-600 text-sm">Pencatatan seluruh pengeluaran operasional.</p>
    </div>
    <a href="{{ route('expenses.create') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium shadow transition">
        <i class="fas fa-plus mr-2"></i> Input Pengeluaran
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="p-4 text-sm font-semibold text-gray-600">Tanggal</th>
                    <th class="p-4 text-sm font-semibold text-gray-600">Kategori</th>
                    <th class="p-4 text-sm font-semibold text-gray-600">Keterangan</th>
                    <th class="p-4 text-sm font-semibold text-gray-600">Nominal</th>
                    <th class="p-4 text-sm font-semibold text-gray-600 text-center">Bukti Nota</th>
                    <th class="p-4 text-sm font-semibold text-gray-600 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($expenses as $expense)
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="p-4 text-sm text-gray-700">{{ \Carbon\Carbon::parse($expense->tanggal)->format('d M Y') }}</td>
                    <td class="p-4 text-sm font-medium text-gray-900">{{ $expense->kategori }}</td>
                    <td class="p-4 text-sm text-gray-700">{{ Str::limit($expense->keterangan, 30, '...') }}</td>
                    <td class="p-4 text-sm font-bold text-red-600">Rp {{ number_format($expense->nominal, 0, ',', '.') }}</td>
                    <td class="p-4 text-sm text-center">
                        @if($expense->bukti)
                            <a href="{{ asset('storage/' . $expense->bukti) }}" target="_blank" class="text-blue-500 hover:underline inline-flex items-center">
                                <i class="fas fa-file-invoice mr-1"></i> Lihat
                            </a>
                        @else
                            <span class="text-gray-400 italic text-xs">Tidak ada</span>
                        @endif
                    </td>
                    <td class="p-4 text-sm text-center space-x-3">
                        <a href="{{ route('expenses.edit', $expense->id) }}" class="text-blue-500 hover:text-blue-700 inline-block">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" onclick="confirmDelete({{ $expense->id }})" class="text-red-500 hover:text-red-700 inline-block">
                            <i class="fas fa-trash"></i>
                        </button>
                        <form id="delete-form-{{ $expense->id }}" action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-gray-500">Belum ada data pengeluaran.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
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