@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Data Siswa</h2>
        <p class="text-gray-600 text-sm">Kelola data master santri/siswa.</p>
    </div>
    <a href="{{ route('students.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium shadow transition">
        <i class="fas fa-plus mr-2"></i> Tambah Siswa
    </a>
</div>

<div class="bg-white p-4 rounded-xl shadow mb-6">
    <form action="{{ route('students.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIS atau Nama Siswa..." class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div class="w-full md:w-48">
            <select name="status" class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="tidak_aktif" {{ request('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>
        <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-2 rounded-lg font-medium transition">
            Filter
        </button>
        @if(request('search') || request('status'))
            <a href="{{ route('students.index') }}" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition text-center">Reset</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="p-4 text-sm font-semibold text-gray-600">NIS</th>
                    <th class="p-4 text-sm font-semibold text-gray-600">Nama Lengkap</th>
                    <th class="p-4 text-sm font-semibold text-gray-600">Kelas</th>
                    <th class="p-4 text-sm font-semibold text-gray-600">L/P</th>
                    <th class="p-4 text-sm font-semibold text-gray-600">Status</th>
                    <th class="p-4 text-sm font-semibold text-gray-600 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $siswa)
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="p-4 text-sm text-gray-700">{{ $siswa->nis }}</td>
                    <td class="p-4 text-sm font-medium text-gray-900">{{ $siswa->nama }}</td>
                    <td class="p-4 text-sm text-gray-700">{{ $siswa->kelas }}</td>
                    <td class="p-4 text-sm text-gray-700">{{ $siswa->jenis_kelamin }}</td>
                    <td class="p-4 text-sm">
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $siswa->status == 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst(str_replace('_', ' ', $siswa->status)) }}
                        </span>
                    </td>
                    <td class="p-4 text-sm text-center space-x-2">
                        <a href="{{ route('students.edit', $siswa->id) }}" class="text-blue-500 hover:text-blue-700 inline-block">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" onclick="confirmDelete({{ $siswa->id }})" class="text-red-500 hover:text-red-700 inline-block">
                            <i class="fas fa-trash"></i>
                        </button>
                        <form id="delete-form-{{ $siswa->id }}" action="{{ route('students.destroy', $siswa->id) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-gray-500">Tidak ada data siswa ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $students->links() }}
    </div>
</div>

<script>
    // Notifikasi Sukses
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 2000
        });
    @endif

    // Konfirmasi Hapus
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data siswa yang dihapus tidak dapat dikembalikan beserta riwayat keuangannya!",
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