@extends('layouts.admin')
@section('header_title', 'Pengaturan Tahun Ajaran')

@section('content')
<div class="mb-5 sm:mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Manajemen Tahun Ajaran</h2>
        <p class="text-gray-500 text-sm mt-0.5">Kelola tahun ajaran untuk memisahkan data transaksi keuangan setiap tahunnya.</p>
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Form Tambah Tahun Ajaran --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-gray-800"><i class="fas fa-plus-circle text-emerald-600 mr-2"></i>Tambah Tahun Ajaran</h3>
            </div>
            <div class="p-5">
                <form action="{{ route('academic-years.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Tahun Ajaran <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: 2024/2025 Genap" 
                               class="w-full border-gray-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm transition">
                        <p class="text-[10px] text-gray-500 mt-1">Harap gunakan format yang konsisten, misalnya tahun dan semester.</p>
                        @error('name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-xl transition text-sm flex justify-center items-center gap-2 shadow-sm">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </form>
            </div>
        </div>
        
        <div class="mt-4 bg-blue-50 border border-blue-100 rounded-xl p-4">
            <h4 class="font-bold text-blue-800 text-sm mb-2"><i class="fas fa-info-circle mr-1"></i> Informasi Penting</h4>
            <ul class="text-xs text-blue-700 space-y-2 list-disc pl-4">
                <li>Menambah tahun ajaran baru <strong>tidak</strong> akan langsung mengaktifkannya.</li>
                <li>Setelah tahun ajaran baru dibuat, Anda perlu mengklik tombol <strong>Set Aktif</strong> pada tabel di samping.</li>
                <li>Saat tahun ajaran baru diaktifkan, data transaksi (Kas Masuk, Kas Keluar, Tagihan) akan kosong karena Anda memulai tahun yang baru. Data tahun sebelumnya tersimpan dengan aman.</li>
            </ul>
        </div>

        {{-- Form Pengaturan WhatsApp --}}
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
                <i class="fab fa-whatsapp text-green-500 text-xl"></i>
                <h3 class="font-bold text-gray-800">WhatsApp Gateway (Fonnte)</h3>
            </div>
            <div class="p-5">
                <form action="{{ route('settings.wa_token') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fonnte API Token</label>
                        <input type="text" name="token" required value="{{ $waToken ?? '' }}" placeholder="Masukkan Token Fonnte..." 
                               class="w-full border-gray-200 rounded-xl focus:ring-green-500 focus:border-green-500 text-sm transition">
                        <p class="text-[10px] text-gray-500 mt-1">Dapatkan token di <a href="https://fonnte.com" target="_blank" class="text-blue-500 hover:underline">fonnte.com</a></p>
                    </div>
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-xl transition text-sm flex justify-center items-center gap-2 shadow-sm">
                        <i class="fas fa-save"></i> Simpan Token
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabel Daftar Tahun Ajaran --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-200">
                            <th class="p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Tahun Ajaran</th>
                            <th class="p-4 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                            <th class="p-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($academicYears as $year)
                        <tr class="hover:bg-gray-50/80 transition {{ $year->is_active ? 'bg-emerald-50/30' : '' }}">
                            <td class="p-4 text-sm font-semibold text-gray-900">{{ $year->name }}</td>
                            <td class="p-4 text-sm">
                                @if($year->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm flex items-center w-max gap-1">
                                        <i class="fas fa-check-circle"></i> Sedang Aktif
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200 w-max inline-block">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-sm text-center">
                                @if(!$year->is_active)
                                    <form action="{{ route('academic-years.set_active', $year->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menjadikan tahun ajaran ini sebagai yang aktif? Transaksi di Dashboard akan berubah menyesuaikan tahun ini.')">
                                        @csrf
                                        <button type="submit" class="text-xs bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white border border-blue-200 transition font-bold py-1.5 px-3 rounded-lg shadow-sm">
                                            Jadikan Aktif
                                        </button>
                                    </form>
                                @else
                                    <button disabled class="text-xs bg-gray-100 text-gray-400 font-bold py-1.5 px-3 rounded-lg cursor-not-allowed">
                                        Sudah Aktif
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="p-10 text-center text-gray-400">
                                <i class="fas fa-calendar-times text-3xl mb-2 opacity-30 block"></i>
                                Belum ada data Tahun Ajaran.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
