@extends('layouts.admin')
@section('header_title', 'Data Siswa')

@section('content')
{{-- Page Header --}}
<div class="mb-5 sm:mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Data Siswa</h2>
        <p class="text-gray-500 text-sm mt-0.5">Kelola data master santri/siswa.</p>
    </div>
    
    <div class="flex flex-col xs:flex-row gap-2 sm:gap-3 sm:shrink-0">
        {{-- Download Template --}}
        <a href="{{ route('students.template') }}" class="bg-gray-50 hover:bg-gray-100 text-gray-700 px-3 py-2 rounded-xl transition text-sm font-medium flex items-center justify-center gap-2 border border-gray-200 shadow-sm">
            <i class="fas fa-file-download text-emerald-600"></i> 
            <span class="hidden sm:inline">Template</span>
        </a>

        {{-- Import Excel --}}
        <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data" 
              class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl shadow-sm border border-gray-200 flex-1 xs:flex-initial">
            @csrf
            <label class="flex-1 xs:flex-initial cursor-pointer">
                <input type="file" name="file_excel" class="sr-only" required accept=".xlsx,.xls,.csv" id="fileImport">
                <div id="fileLabel" class="flex items-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-file-excel text-blue-500"></i>
                    <span id="fileLabelText" class="truncate max-w-[120px]">Pilih file Excel</span>
                </div>
            </label>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition text-xs font-semibold shrink-0 flex items-center gap-1">
                <i class="fas fa-upload"></i> 
                <span class="hidden xs:inline">Import</span>
            </button>
        </form>

        {{-- Kenaikan Kelas Massal --}}
        <button type="button" onclick="openPromoteModal()"
           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-xl font-medium shadow-sm transition flex items-center justify-center gap-2 text-sm shrink-0">
            <i class="fas fa-level-up-alt"></i> 
            <span class="hidden sm:inline">Pindah Kelas Massal</span>
        </button>

        <a href="{{ route('students.create') }}" 
           class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl font-medium shadow-sm transition flex items-center justify-center gap-2 text-sm shrink-0">
            <i class="fas fa-plus"></i> 
            <span class="hidden sm:inline">Tambah Manual</span>
        </a>
    </div>
</div>

@if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-2 text-sm">
        <i class="fas fa-check-circle shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-2 text-sm">
        <i class="fas fa-exclamation-circle shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

{{-- Data Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    
    {{-- Desktop/Tablet Table --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-200">
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500">NIS</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Lengkap</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Kelas</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500">L/P</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500">No HP Wali</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Alamat</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="p-3 sm:p-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($students as $siswa)
                <tr class="hover:bg-gray-50/80 transition">
                    <td class="p-3 sm:p-4 text-sm text-gray-500 font-mono">{{ $siswa->nis }}</td>
                    <td class="p-3 sm:p-4 text-sm font-semibold text-gray-900">{{ $siswa->nama }}</td>
                    <td class="p-3 sm:p-4 text-sm text-gray-600">
                        <span class="px-2 py-0.5 bg-gray-100 rounded-lg text-xs font-medium">{{ $siswa->kelas }}</span>
                    </td>
                    <td class="p-3 sm:p-4 text-sm text-gray-600">{{ $siswa->jenis_kelamin == 'L' ? 'L' : 'P' }}</td>
                    <td class="p-3 sm:p-4 text-sm text-gray-600">{{ $siswa->no_hp_wali ?? '-' }}</td>
                    <td class="p-3 sm:p-4 text-sm text-gray-600 max-w-[150px] truncate" title="{{ $siswa->alamat }}">{{ $siswa->alamat ?? '-' }}</td>
                    <td class="p-3 sm:p-4 text-sm">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $siswa->status == 'aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                            {{ ucfirst(str_replace('_', ' ', $siswa->status)) }}
                        </span>
                    </td>
                    <td class="p-3 sm:p-4 text-sm text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('students.edit', $siswa->id) }}" 
                               class="p-1.5 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <button type="button" onclick="confirmDelete({{ $siswa->id }})" 
                                    class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                        <form id="delete-form-{{ $siswa->id }}" action="{{ route('students.destroy', $siswa->id) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="p-10 text-center text-gray-400">
                        <i class="fas fa-users text-4xl mb-3 block opacity-20"></i>
                        <p class="font-medium">Tidak ada data siswa ditemukan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Card View --}}
    <div class="sm:hidden divide-y divide-gray-100">
        @forelse ($students as $siswa)
        <div class="p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="h-10 w-10 rounded-full {{ $siswa->jenis_kelamin == 'L' ? 'bg-blue-100 text-blue-600' : 'bg-pink-100 text-pink-600' }} flex items-center justify-center font-bold text-sm shrink-0">
                        {{ substr($siswa->nama, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <div class="font-semibold text-gray-900 text-sm truncate">{{ $siswa->nama }}</div>
                        <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                            <span class="text-[10px] text-gray-400 font-mono">{{ $siswa->nis }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 rounded font-medium text-gray-600">{{ $siswa->kelas }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded {{ $siswa->status == 'aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }} font-medium">
                                {{ ucfirst(str_replace('_', ' ', $siswa->status)) }}
                            </span>
                        </div>
                        <div class="text-[10px] text-gray-500 mt-1 flex flex-col gap-0.5">
                            @if($siswa->no_hp_wali)
                                <span><i class="fas fa-phone mr-1 text-gray-400"></i>{{ $siswa->no_hp_wali }}</span>
                            @endif
                            @if($siswa->alamat)
                                <span class="truncate"><i class="fas fa-map-marker-alt mr-1.5 text-gray-400"></i>{{ $siswa->alamat }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-center gap-1 shrink-0">
                    <a href="{{ route('students.edit', $siswa->id) }}" 
                       class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition">
                        <i class="fas fa-edit text-sm"></i>
                    </a>
                    <button type="button" onclick="confirmDelete({{ $siswa->id }})"
                            class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </div>
            </div>
            <form id="delete-form-{{ $siswa->id }}" action="{{ route('students.destroy', $siswa->id) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
        @empty
        <div class="p-10 text-center text-gray-400">
            <i class="fas fa-users text-4xl mb-3 block opacity-20"></i>
            <p class="font-medium text-sm">Tidak ada data siswa ditemukan.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="p-4 border-t border-gray-100">
        {{ $students->links() }}
    </div>
</div>

<!-- MODAL PINDAH KELAS MASSAL -->
<div id="promoteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 hidden">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Pindah Kelas Massal (Kenaikan Kelas)</h3>
            <button type="button" onclick="closePromoteModal()" class="text-gray-400 hover:text-gray-600 transition p-1">
                <i class="fas fa-times fa-lg"></i>
            </button>
        </div>
        <form action="{{ route('students.promote') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memindahkan siswa dan/atau mereset tagihan mereka?')">
            @csrf
            <div class="p-5 space-y-4">
                <div class="bg-blue-50 text-blue-800 p-3 rounded-lg text-xs font-medium border border-blue-100 flex items-start gap-2">
                    <i class="fas fa-info-circle mt-0.5"></i>
                    <p>Gunakan fitur ini saat pergantian tahun ajaran untuk menaikkan kelas semua siswa di suatu kelas sekaligus, dan opsional mereset target cicilan kas mereka.</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Kelas Asal *</label>
                    <select name="kelas_asal" required class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">-- Pilih Kelas Asal --</option>
                        @foreach($classes ?? [] as $cls)
                            <option value="{{ $cls }}">{{ $cls }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Kelas Tujuan *</label>
                    <input type="text" name="kelas_tujuan" required placeholder="Contoh: 8A, 9B, Lulus" class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <p class="text-[10px] text-gray-500 mt-1">Isi "Lulus" jika siswa sudah tamat (opsional).</p>
                </div>

                <div class="pt-2">
                    <label class="flex items-start gap-2 cursor-pointer bg-red-50 p-3 rounded-xl border border-red-100 hover:bg-red-100 transition">
                        <input type="checkbox" name="reset_tagihan" value="1" class="mt-0.5 rounded border-red-300 text-red-600 focus:ring-red-500">
                        <div class="text-sm text-red-800 font-medium">
                            Reset Progress Tagihan/Cicilan
                            <p class="text-[10px] text-red-600 font-normal mt-0.5">Centang ini untuk mengosongkan status bayar mereka (jadi belum bayar di kelas baru). Riwayat transaksi lama tidak akan terhapus.</p>
                        </div>
                    </label>
                </div>
            </div>
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2">
                <button type="button" onclick="closePromoteModal()" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition shadow-sm text-sm">Proses Pemindahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPromoteModal() {
        document.getElementById('promoteModal').classList.remove('hidden');
    }
    function closePromoteModal() {
        document.getElementById('promoteModal').classList.add('hidden');
    }

    // File input label update
    const fileInput = document.getElementById('fileImport');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const label = document.getElementById('fileLabelText');
            if (this.files[0]) {
                label.textContent = this.files[0].name;
            }
        });
    }

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

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session('error') }}',
            showConfirmButton: false,
            timer: 3000
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